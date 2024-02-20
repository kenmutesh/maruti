<?php

namespace App\Http\Controllers;

use App\Enums\CoatingJobOwnerEnum;
use App\Enums\CoatingJobProfileTypesEnum;
use App\Enums\CoatingJobStatusEnum;
use App\Enums\TaxTypesEnum;
use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\CoatingJobAluminiumItem;
use App\Models\CoatingJobMarutiItem;
use App\Models\CoatingJobSteelItem;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Powder;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Support\Facades\Cache;

class CoatingJobController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CoatingJob::class);
        $coatingJobs = Cache::remember('upto_thirty_days_coating_jobs', (60 * 2), function () {
            $pastThirtyDays = Carbon::now()->subDays(30);
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['coating_suffix', '!=', NULL],
                ])
                ->orderBy('coating_suffix', 'desc')
                ->whereDate('created_at', '>=', $pastThirtyDays->format('Y-m-d'))
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
        });

        if ($request->is('api/*')) {
            return $coatingJobs;
        } else {
            $invoice = new Invoice();
            $cashsale = new CashSale();

            $allCoatingJobs = CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'customer_id')
            ->where([
                ['status', '=', CoatingJobStatusEnum::OPEN],
                ['coating_suffix', '!=', NULL],
            ])->orderBy('coating_suffix')->get()->chunk(500);

            return view('system.coatingjobs.index', [
                'coatingJobs' => $coatingJobs,
                'invoice' => $invoice->next_invoice_prefix.''.$invoice->next_invoice_suffix,
                'ext_invoice' => $invoice->next_ext_invoice_prefix.''.$invoice->next_ext_invoice_suffix,
                'cashsale' => $cashsale->next_cash_sale_prefix.''.$cashsale->next_cash_sale_suffix,
                'ext_cashsale' => $cashsale->next_ext_cash_sale_prefix.''.$cashsale->next_ext_cash_sale_suffix,
                'cu_prefix' => $invoice->next_cu_prefix,
                'cu_suffix' => $invoice->next_cu_suffix,
                'allCoatingJobs' => $allCoatingJobs
            ]);
        }
    }

    public function agedOpenCoatingJobs(Request $request)
    {
        $this->authorize('viewAny', CoatingJob::class);
        $coatingJobs = Cache::remember('aged_coating_jobs', (60 * 60 * 2), function () {
            $pastThirtyDays = Carbon::now()->subDays(30);
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix',  'status', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['coating_suffix', '!=', NULL],
                ])
                ->orderBy('coating_suffix', 'desc')
                ->whereDate('created_at', '<', $pastThirtyDays->format('Y-m-d'))
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
        });

        $html = view('system.coatingjobs.misc.searchlist-coatingjob', [
            'coatingJobs' => $coatingJobs,
            'number' => $request->number
        ])->render();

        return $html;
    }

    public function openCustomerCoatingJobs(Request $request)
    {
        $this->authorize('viewAny', CoatingJob::class);

        $coatingJobs = Cache::remember('open_coating_jobs_customer_' . $request->customer_id, (60 * 2), function () use ($request) {
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'sum_grandtotal')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['coating_suffix', '!=', NULL],
                    ['id', '!=', $request->coatingjob_id],
                    ['customer_id', '=', $request->customer_id],
                ])
                ->orderBy('coating_suffix', 'desc')
                ->get();
        });

        $html = view('system.coatingjobs.misc.customer-associated-coatingjobs', [
            'coatingJobs' => $coatingJobs
        ])->render();

        return $html;
    }

    public function quotations(Request $request)
    {
        $this->authorize('viewAny', CoatingJob::class);
        $quotations = Cache::remember('upto_thirty_days_quotations', (60 * 2), function () {
            $pastThirtyDays = Carbon::now()->subDays(30);
            return CoatingJob::select('id', 'quotation_suffix', 'quotation_prefix', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['quotation_suffix', '!=', NULL],
                ])
                ->whereNull('coating_suffix')
                ->orderBy('quotation_suffix', 'desc')
                ->whereDate('created_at', '>=', $pastThirtyDays->format('Y-m-d'))
                ->with(['customer'])->get();
        });

        if ($request->is('api/*')) {
            return $quotations;
        } else {
            return view('system.coatingjobs.quotations', [
                'quotations' => $quotations,
            ]);
        }
    }

    public function agedOpenQuotations(Request $request)
    {
        $this->authorize('viewAny', CoatingJob::class);
        $quotations = Cache::remember('aged_quotations', (60 * 60 * 2), function () {
            $pastThirtyDays = Carbon::now()->subDays(30);
            return CoatingJob::select('id', 'quotation_suffix', 'quotation_prefix', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['quotation_suffix', '!=', NULL]
                ])
                ->whereNull('coating_suffix')
                ->orderBy('quotation_suffix', 'desc')
                ->whereDate('created_at', '<', $pastThirtyDays->format('Y-m-d'))
                ->with(['customer'])->get();
        });

        $html = view('system.coatingjobs.misc.searchlist-quotation', [
            'quotations' => $quotations,
            'number' => $request->number
        ])->render();

        return $html;
    }

    public function convert(Request $request, CoatingJob $coatingjob)
    {
        $this->authorize('update', [CoatingJob::class, $coatingjob]);
        $coatingjob->coating_prefix = $coatingjob->next_coating_job_prefix;
        $coatingjob->coating_suffix = $coatingjob->next_coating_job_suffix;

        if ($coatingjob->update()) {
            $this->refreshCache();
            return redirect('/coatingjobs')->with('Success', 'Card created successfully');
        } else {
            return back()->with('Error', 'Failed to create card. Please retry');
        }
    }

    public function create()
    {
        $this->authorize('create', CoatingJob::class);

        $customers = Customer::select('id', 'customer_name')->get();

        $powders = Powder::select('id', 'powder_color', 'supplier_id', 'standard_price', 'standard_price_vat', 'current_weight')
            ->with(['supplier:id,supplier_name'])->get();

        $inventoryItemsCollection = collect(
            InventoryItem::select('id', 'type', 'item_name', 'item_code', 'quantity_tag', 'standard_price', 'standard_price_vat', 'current_quantity')
                ->with(['supplier:id,supplier_name'])
                ->get()->toArray()
        );

        $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();

        $vat = Tax::where('type', TaxTypesEnum::VAT)->first();

        $coatingjob = new CoatingJob();

        $profileTypesEnum = CoatingJobProfileTypesEnum::cases();

        $ownerEnums = CoatingJobOwnerEnum::cases();

        $users = User::where([
            ['id', '!=', auth()->user()->id],
            ['company_id', '=', auth()->user()->company_id],
        ])->whereNotNull('email_verified_at')->get();

        return view('system.coatingjobs.create', [
            'customers' => $customers,
            'powders' => $powders,
            'inventoryitems' => $inventoryitems,
            'users' => $users,
            'coatingjob' => $coatingjob,
            'vat' => $vat,
            'profileTypesEnum' => $profileTypesEnum,
            'ownerEnums' => $ownerEnums
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', CoatingJob::class);
        // required items
        $request->validate([
            'quotation_suffix' => ['required'],
            'coating_suffix' => ['required'],
            'customer_id' => ['required'],
            'date' => ['required'],
            'belongs_to' => ['required'],
            'grand_total' => ['required'],
            'prepared_by' => ['required'],
            'approved_by' => ['required'],
            'supervisor' => ['required'],
            'quality_by' => ['required'],
            'sale_by' => ['required'],
            'document' => ['required']
        ]);

        $coatingjob = new CoatingJob();

        if ($request->document == "Coating Job") {
            $coatingjob->fill([
                'coating_prefix' => $coatingjob->next_coating_job_prefix,
                'coating_suffix' => $coatingjob->next_coating_job_suffix,
                'customer_id' => $request->customer_id,
                'cash_sale_name' => $request->cash_sale_name,
                'lpo' => $request->lpo,
                'date' => $request->date,
                'in_date' => $request->in_date,
                'ready_date' => $request->ready_date,
                'out_date' => $request->out_date,
                'goods_weight' => $request->goods_weight,
                'profile_type' => $request->profile_type,
                'powder_estimate' => $request->powder_estimate,
                'powder_id' => $request->ral_main,
                'belongs_to' => $request->belongs_to,
                'prepared_by' => $request->prepared_by,
                'supervisor' => $request->supervisor,
                'quality_by' => $request->quality_by,
                'sale_by' => $request->sale_by,
                'created_by' => auth()->user()->id,
                'company_id' => auth()->user()->company_id
            ]);
        }

        if ($request->document == "Quotation") {
            $coatingjob->fill([
                'quotation_prefix' => $coatingjob->next_quotation_prefix,
                'quotation_suffix' => $coatingjob->next_quotation_suffix,
                'customer_id' => $request->customer_id,
                'cash_sale_name' => $request->cash_sale_name,
                'lpo' => $request->lpo,
                'date' => $request->date,
                'in_date' => $request->in_date,
                'ready_date' => $request->ready_date,
                'out_date' => $request->out_date,
                'goods_weight' => $request->goods_weight,
                'profile_type' => $request->profile_type,
                'powder_estimate' => $request->powder_estimate,
                'powder_id' => $request->ral_main,
                'belongs_to' => $request->belongs_to,
                'prepared_by' => $request->prepared_by,
                'supervisor' => $request->supervisor,
                'quality_by' => $request->quality_by,
                'sale_by' => $request->sale_by,
                'created_by' => auth()->user()->id,
                'company_id' => auth()->user()->company_id
            ]);
        }

        if ($coatingjob->save()) {
            if ($request->belongs_to == CoatingJobOwnerEnum::MARUTI->value) {
                $this->addMarutiItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::DIRECT->value) {
                $this->addMarutiDirectItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::OWNERALUMINIUM->value || $request->belongs_to == CoatingJobOwnerEnum::OWNERSTEELALUMINIUM->value) {
                $this->addAluminiumItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::OWNERSTEEL->value || $request->belongs_to == CoatingJobOwnerEnum::OWNERSTEELALUMINIUM->value) {
                $this->addSteelItems($request, $coatingjob);
            }

            $coatingjob->updateAmounts();

            $this->refreshCache();

            if ($request->document === 'Coating Job') {
                return redirect('/coatingjobs')->with('Success', 'Created successfully');
            } else {
                return redirect('/coatingjobs/quotations')->with('Success', 'Created successfully');
            }
        } else {
            return back()->with('Error', 'Failed to create. Please retry');
        }
    }

    private function addMarutiItems(Request $request, CoatingJob $coatingjob)
    {
        for ($i = 0; $i < count($request->maruti_item_id); $i++) {
            $coatingjobMarutiItem = new CoatingJobMarutiItem();

            $coatingjobMarutiItem->fill([
                'coating_job_id' => $coatingjob->id,
                'inventory_item_id' => $request->maruti_item_id[$i],
                'uom' => strtoupper($request->maruti_item_uom[$i] ?? 'UNITS'),
                'unit_price' => $request->maruti_unit_price[$i] ?? 1,
                'quantity' => $request->maruti_item_qty[$i] ?? 1,
                'vat' => $request->maruti_item_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                'vat_inclusive' => ($request->maruti_item_vat_inclusive[$i] == 'Yes') ? 1 : 0
            ]);

            $coatingjobMarutiItem->save();
        }
    }

    private function updateMarutiItems(Request $request, CoatingJob $coatingjob)
    {
        for ($i = 0; $i < count($request->maruti_item_id); $i++) {
            $coatingjobMarutiItem = CoatingJobMarutiItem::find($request->maruti_item_db_id[$i]);
            if (!$coatingjobMarutiItem) {
                $coatingjobMarutiItem = new CoatingJobMarutiItem();
            }

            $coatingjobMarutiItem->fill([
                'coating_job_id' => $coatingjob->id,
                'inventory_item_id' => $request->maruti_item_id[$i],
                'uom' => strtoupper($request->maruti_item_uom[$i] ?? 'UNITS'),
                'unit_price' => $request->maruti_unit_price[$i] ?? 1,
                'quantity' => $request->maruti_item_qty[$i] ?? 1,
                'vat' => $request->maruti_item_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                'vat_inclusive' => ($request->maruti_item_vat_inclusive[$i] == 'Yes') ? 1 : 0
            ]);
            if ($coatingjobMarutiItem->exists) {
                $coatingjobMarutiItem->update();
            } else {
                $coatingjobMarutiItem->save();
            }
        }

        if($request->maruti_item_id_remove){
            for ($i = 0; $i < count($request->maruti_item_id_remove); $i++) {
                $coatingjobMarutiItem = CoatingJobMarutiItem::find($request->maruti_item_id_remove[$i]);
                $coatingjobMarutiItem->delete();
            }
        }
    }

    private function addMarutiDirectItems(Request $request, CoatingJob $coatingjob)
    {
        for ($i = 0; $i < count($request->maruti_direct_item_id); $i++) {
            $coatingjobMarutiItem = new CoatingJobMarutiItem();

            if ($request->maruti_direct_inventory_type[$i] == "Powder") {
                $coatingjobMarutiItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'powder_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'KG'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_kg[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);
            } else {
                $coatingjobMarutiItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'inventory_item_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'UNITS'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_qty[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);
            }

            $coatingjobMarutiItem->save();
        }
    }

    private function updateMarutiDirectItems(Request $request, CoatingJob $coatingjob)
    {
        for ($i = 0; $i < count($request->maruti_direct_id); $i++) {
            $coatingjobMarutiItem = CoatingJobMarutiItem::find($request->maruti_direct_id[$i]);
            if (!$coatingjobMarutiItem) {
                $coatingjobMarutiItem = new CoatingJobMarutiItem();
                if ($request->maruti_direct_inventory_type[$i] == "Powder") {
                    $coatingjobMarutiItem->fill([
                        'coating_job_id' => $coatingjob->id,
                        'powder_id' => $request->maruti_direct_item_id[$i],
                        'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'KG'),
                        'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                        'quantity' => $request->maruti_direct_item_kg[$i] ?? 1,
                        'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                        'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                    ]);
                } else {
                    $coatingjobMarutiItem->fill([
                        'coating_job_id' => $coatingjob->id,
                        'inventory_item_id' => $request->maruti_direct_item_id[$i],
                        'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'UNITS'),
                        'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                        'quantity' => $request->maruti_direct_item_qty[$i] ?? 1,
                        'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                        'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                    ]);
                }
    
                $coatingjobMarutiItem->save();
            }else{
                if ($request->maruti_direct_inventory_type[$i] == "Powder") {
                    $coatingjobMarutiItem->fill([
                        'coating_job_id' => $coatingjob->id,
                        'powder_id' => $request->maruti_direct_item_id[$i],
                        'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'KG'),
                        'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                        'quantity' => $request->maruti_direct_item_kg[$i] ?? 1,
                        'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                        'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                    ]);
                } else {
                    $coatingjobMarutiItem->fill([
                        'coating_job_id' => $coatingjob->id,
                        'inventory_item_id' => $request->maruti_direct_item_id[$i],
                        'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'UNITS'),
                        'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                        'quantity' => $request->maruti_direct_item_qty[$i] ?? 1,
                        'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                        'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                    ]);
                }
    
                $coatingjobMarutiItem->update();
            }

        }

        if($request->maruti_direct_id_remove){
            for ($i = 0; $i < count($request->maruti_direct_id_remove); $i++) {
                $coatingjobMarutiItem = CoatingJobMarutiItem::find($request->maruti_direct_id_remove[$i]);
                $coatingjobMarutiItem->delete();
            }
        }
    }

    private function addAluminiumItems(Request $request, CoatingJob $coatingjob)
    {
        if($request->aluminium_item_name){
            for ($i = 0; $i < count($request->aluminium_item_name); $i++) {
                $coatingjobAluminiumItem = new CoatingJobAluminiumItem();
    
                $coatingjobAluminiumItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'item_name' => strtoupper($request->aluminium_item_name[$i] ?? 'ALUMINIUM ITEM') ,
                    'uom' => $request->aluminium_uom[$i] ?? 'PIECES',
                    'item_kg' => $request->item_kg[$i] ?? 1,
                    'unit_price' => $request->aluminium_unit_price[$i] ?? 1,
                    'quantity' => $request->aluminium_item_qty[$i] ?? 1,
                    'vat' => $request->aluminium_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->aluminium_vat_inclusive[$i] === 'Yes') ? 1 : 0
                ]);
    
                $coatingjobAluminiumItem->save();
            }
        }
    }

    private function updateAluminiumItems(Request $request, CoatingJob $coatingjob)
    {
        if($request->aluminium_item_id){
            for ($i = 0; $i < count($request->aluminium_item_id); $i++) {
                $coatingjobAluminiumItem = CoatingJobAluminiumItem::find($request->aluminium_item_id[$i]);
                if (!$coatingjobAluminiumItem) {
                    $coatingjobAluminiumItem = new CoatingJobAluminiumItem();
                }
    
                $coatingjobAluminiumItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'item_name' => strtoupper($request->aluminium_item_name[$i] ?? 'ALUMINIUM ITEM') ,
                    'uom' => $request->aluminium_uom[$i] ?? 'PIECES',
                    'item_kg' => $request->item_kg[$i] ?? 1,
                    'unit_price' => $request->aluminium_unit_price[$i] ?? 1,
                    'quantity' => $request->aluminium_item_qty[$i] ?? 1,
                    'vat' => $request->aluminium_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->aluminium_vat_inclusive[$i] === 'Yes') ? 1 : 0
                ]);
                if ($coatingjobAluminiumItem->exists) {
                    $coatingjobAluminiumItem->update();
                } else {
                    $coatingjobAluminiumItem->save();
                }
            }
        }
    }

    private function addSteelItems(Request $request, CoatingJob $coatingjob)
    {
        if($request->steel_item_name){
            $powderEstimateTotal = 0;
            for ($i = 0; $i < count($request->steel_item_name); $i++) {
                $coatingjobSteelItem = new CoatingJobSteelItem();
                $powderEstimateTotal += $request->steel_powder_estimate[$i] ?? 0;
                $coatingjobSteelItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'item_name' => strtoupper($request->steel_item_name[$i] ?? 'STEEL ITEM'),
                    'powder_estimate' => $request->steel_powder_estimate[$i] ?? 0,
                    'uom' => strtoupper($request->steel_uom[$i] ?? 'PIECES'),
                    'quantity' => $request->steel_item_qty[$i] ?? 1,
                    'length' => $request->steel_item_length[$i] ?? 0,
                    'width' => $request->steel_item_width[$i] ?? 0,
                    'unit_price' => $request->steel_unit_price[$i] ?? 1,
                    'vat' => $request->steel_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->steel_vat_inclusive[$i] == 'Yes') ? 1 : 0
                ]);
    
                $coatingjobSteelItem->save();
                $coatingjob->powder_estimate = $powderEstimateTotal;
                $coatingjob->update();
            }
        }
    }

    private function updateSteelItems(Request $request, CoatingJob $coatingjob)
    {
        if($request->steel_item_name){
            $powderEstimateTotal = 0;
            for ($i = 0; $i < count($request->steel_item_name); $i++) {
                $coatingjobSteelItem = CoatingJobSteelItem::find($request->steel_item_id[$i]);
                if (!$coatingjobSteelItem) {
                    $coatingjobSteelItem = new CoatingJobSteelItem();
                }
                $powderEstimateTotal += $request->steel_powder_estimate[$i] ?? 0;
                $coatingjobSteelItem->fill([
                    'coating_job_id' => $coatingjob->id,
                    'item_name' => strtoupper($request->steel_item_name[$i] ?? 'STEEL ITEM'),
                    'powder_estimate' => $request->steel_powder_estimate[$i] ?? 0,
                    'uom' => strtoupper($request->steel_uom[$i] ?? 'PIECES'),
                    'quantity' => $request->steel_item_qty[$i] ?? 1,
                    'length' => $request->steel_item_length[$i] ?? 0,
                    'width' => $request->steel_item_width[$i] ?? 0,
                    'unit_price' => $request->steel_unit_price[$i] ?? 1,
                    'vat' => $request->steel_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->steel_vat_inclusive[$i] == 'Yes') ? 1 : 0
                ]);
                if ($coatingjobSteelItem->exists) {
                    $coatingjobSteelItem->update();
                } else {
                    $coatingjobSteelItem->save();
                }
                $coatingjob->powder_estimate = $powderEstimateTotal;
                $coatingjob->update();
            }
        }

        if($request->steel_item_id_remove){
            for ($i = 0; $i < count($request->steel_item_id_remove); $i++) {
                $coatingjobSteelItem = CoatingJobSteelItem::find($request->steel_item_id_remove[$i]);
                $coatingjobSteelItem->delete();
            }
        }
    }

    public function show(Request $request, CoatingJob $coatingjob)
    {
        $this->authorize('viewAny', CoatingJob::class);
        if ($request->is('api/*')) {
            return $coatingjob;
        } else {
            $hidePrice = isset($request->hideprice) ? true : 0;
            return view('system.coatingjobs.document', [
                'hidePrice' => $hidePrice,
                'coatingjob' => $coatingjob,
            ]);
        }
    }

    public function edit(CoatingJob $coatingjob)
    {
        $this->authorize('update', [CoatingJob::class, $coatingjob]);

        $customers = Customer::all();

        $powders = Powder::all();

        $inventoryItemsCollection = collect(InventoryItem::all()->toArray());

        $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();

        $vat = Tax::where('type', TaxTypesEnum::VAT)->first();

        $profileTypesEnum = CoatingJobProfileTypesEnum::cases();

        $ownerEnums = CoatingJobOwnerEnum::cases();

        $users = User::where([
            ['id', '!=', auth()->user()->id],
            ['company_id', '=', auth()->user()->company_id],
        ])->whereNotNull('email_verified_at')->get();

        return view('system.coatingjobs.edit', [
            'customers' => $customers,
            'powders' => $powders,
            'inventoryitems' => $inventoryitems,
            'users' => $users,
            'coatingjob' => $coatingjob,
            'vat' => $vat,
            'profileTypesEnum' => $profileTypesEnum,
            'ownerEnums' => $ownerEnums
        ]);
    }

    public function update(Request $request, CoatingJob $coatingjob)
    {
        $this->authorize('update', [CoatingJob::class, $coatingjob]);
        // required items
        $request->validate([
            'customer_id' => ['required'],
            'date' => ['required'],
            'belongs_to' => ['required'],
            'grand_total' => ['required'],
            'prepared_by' => ['required'],
            'approved_by' => ['required'],
            'supervisor' => ['required'],
            'quality_by' => ['required'],
            'sale_by' => ['required']
        ]);

        $coatingjob->fill([
            'coating_suffix' => $request->coating_suffix,
            'customer_id' => $request->customer_id,
            'cash_sale_name' => $request->cash_sale_name,
            'lpo' => $request->lpo,
            'date' => $request->date,
            'in_date' => $request->in_date,
            'ready_date' => $request->ready_date,
            'out_date' => $request->out_date,
            'goods_weight' => $request->goods_weight,
            'profile_type' => $request->profile_type,
            'powder_estimate' => $request->powder_estimate,
            'powder_id' => $request->ral_main,
            'belongs_to' => $request->belongs_to,
            'prepared_by' => $request->prepared_by,
            'supervisor' => $request->supervisor,
            'quality_by' => $request->quality_by,
            'sale_by' => $request->sale_by,
            'created_by' => auth()->user()->id,
            'company_id' => auth()->user()->company_id
        ]);

        if ($coatingjob->update()) {
            if ($request->belongs_to == CoatingJobOwnerEnum::MARUTI->value) {
                $this->updateMarutiItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::DIRECT->value) {
                $this->updateMarutiDirectItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::OWNERALUMINIUM->value || $request->belongs_to == CoatingJobOwnerEnum::OWNERSTEELALUMINIUM->value) {
                $this->updateAluminiumItems($request, $coatingjob);
            }

            if ($request->belongs_to == CoatingJobOwnerEnum::OWNERSTEEL->value || $request->belongs_to == CoatingJobOwnerEnum::OWNERSTEELALUMINIUM->value) {
                $this->updateSteelItems($request, $coatingjob);
            }

            $coatingjob->updateAmounts();

            if ($coatingjob->coating_suffix) {
                $this->refreshCache();
                return redirect('/coatingjobs')->with('Success', 'Edited successfully');
            } else {
                return redirect('/coatingjobs/quotations')->with('Success', 'Edited successfully');
            }
        } else {
            return back()->with('Error', 'Failed to edit. Please retry');
        }
    }

    public function destroy(Request $request, CoatingJob $coatingjob)
    {
        $this->authorize('update', [CoatingJob::class, $coatingjob]);
        $coatingjob->fill([
            'status' => CoatingJobStatusEnum::CANCELLED->value,
            'cancelled_at' => Carbon::now(),
        ]);

        if ($coatingjob->update()) {
            $this->refreshCache();
            return back()->with('Success', 'Cancelled successfully');
        } else {
            return back()->with('Error', 'Failed.Please retry.');
        }
    }

    public function cancelledJobs()
    {
        $coatingJobs = Cache::remember('cancelled_coating_jobs', (60 * 2), function () {
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'belongs_to', 'created_at', 'customer_id', 'belongs_to', 'cancelled_at')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::CANCELLED],
                    ['coating_suffix', '!=', NULL],
                ])
                ->orderBy('coating_suffix', 'desc')
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
        });

        return view('system.coatingjobs.cancelled', [
            'coatingJobs' => $coatingJobs,
        ]);
    }

    public function unbilledJobs()
    {

        $coatingJobs = Cache::remember('unbilled_coating_jobs', (60 * 2), function () {
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'belongs_to', 'created_at', 'customer_id')
                ->whereNotNull('coating_suffix')->whereNull(['invoice_id', 'cash_sale_id'])
                ->orderBy('coating_suffix', 'desc')
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
        });

        return view('system.coatingjobs.unbilled', [
            'coatingJobs' => $coatingJobs,
        ]);
    }

    public function closedJobs()
    {
        $coatingJobs = Cache::remember('closed_coating_jobs', (60 * 2), function () {
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'belongs_to', 'invoice_id', 'cash_sale_id', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::CLOSED],
                    ['coating_suffix', '!=', NULL],
                ])
                ->orderBy('coating_suffix', 'desc')
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
        });
        return view('system.coatingjobs.closed', [
            'coatingJobs' => $coatingJobs,
        ]);
    }

    public function unbilledJobCardsExcel()
    {
        $coatingJobs = CoatingJob::whereNotNull('coating_suffix')->whereNull(['invoice_id', 'cash_sale_id'])->orderBy('coating_suffix', 'desc')->with(['customer'])->get();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        // the titles
        $sheet->setCellValue('A1', 'UNBILLED JOB CARDS');
        $sheet->setCellValue('B1', date('d-M-Y', time()));

        $sheet->setCellValue('A2', 'DATE');
        $sheet->setCellValue('B2', 'JOB CARD NUMBER');
        $sheet->setCellValue('C2', 'CUSTOMER NAME');
        $sheet->setCellValue('D2', 'GRAND TOTAL');

        $unbilledJobCardsCount = count($coatingJobs);
        for ($i = 0; $i < $unbilledJobCardsCount; $i++) {
            $sheet->setCellValue('A' . ($i + 3), date('d-M-Y', strtotime($coatingJobs[$i]->created_at)));
            $sheet->setCellValue('B' . ($i + 3), $coatingJobs[$i]->coating_prefix . ' ' . $coatingJobs[$i]->coating_suffix);
            $sheet->setCellValue('C' . ($i + 3), $coatingJobs[$i]->customer->customer_name);
            $sheet->setCellValue('D' . ($i + 3), number_format($coatingJobs[$i]->grand_total, 2));
        }

        $writer = new Xlsx($spreadsheet);

        $extension = 'xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"Unbilled Job Cards.{$extension}\"");
        $writer->save('php://output');
        exit();
    }

    private function refreshCache()
    {
        Cache::forget('upto_thirty_days_quotations');
        Cache::forget('upto_thirty_days_coating_jobs');
    }

    public function coatingJobSections(Request $request){
        $this->authorize('viewAny', CashSale::class);
        $coatingJobs = Cache::remember('section_coatingjobs_'. $request->minimum .'_'.$request->maximum , (60 * 2), function () use($request) {
    
            return CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'created_at', 'customer_id', 'belongs_to')
                ->where([
                    ['status', '=', CoatingJobStatusEnum::OPEN],
                    ['coating_suffix', '!=', NULL],
                    ['coating_suffix', '>=', $request->minimum],
                    ['coating_suffix', '<=', $request->maximum],
                    
                ])
                ->orderBy('coating_suffix', 'desc')
                ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
    
        });
    
        if ($request->is('api/*')) {
          return $coatingJobs;
        } else {
            $invoice = new Invoice();
            $cashsale = new CashSale();

            $allCoatingJobs = CoatingJob::select('id', 'coating_suffix', 'coating_prefix', 'status', 'customer_id')
            ->where([
                ['status', '=', CoatingJobStatusEnum::OPEN],
                ['coating_suffix', '!=', NULL],
            ])->orderBy('coating_suffix')->get()->chunk(500);

            return view('system.coatingjobs.index', [
                'coatingJobs' => $coatingJobs,
                'invoice' => $invoice->next_invoice_prefix.''.$invoice->next_invoice_suffix,
                'ext_invoice' => $invoice->next_ext_invoice_prefix.''.$invoice->next_ext_invoice_suffix,
                'cashsale' => $cashsale->next_cash_sale_prefix.''.$cashsale->next_cash_sale_suffix,
                'ext_cashsale' => $cashsale->next_ext_cash_sale_prefix.''.$cashsale->next_ext_cash_sale_suffix,
                'cu_prefix' => $invoice->next_cu_prefix,
                'cu_suffix' => $invoice->next_cu_suffix,
                'allCoatingJobs' => $allCoatingJobs
            ]);
        }
      }
}
