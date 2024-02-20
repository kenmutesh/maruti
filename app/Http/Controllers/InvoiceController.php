<?php

namespace App\Http\Controllers;

use App\Enums\CoatingJobOwnerEnum;
use App\Enums\CoatingJobStatusEnum;
use App\Enums\TaxTypesEnum;
use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Powder;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', Invoice::class);
    $invoices = Cache::remember('upto_thirty_days_invoices', (60 * 2), function () {
      $pastThirtyDays = Carbon::now()->subDays(30);
      if (!Gate::allows('accounting')) {
        return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'discount', 'created_at', 'customer_id')
          ->orderBy('invoice_suffix', 'desc')
          ->whereDate('created_at', '>=', $pastThirtyDays->format('Y-m-d'))
          ->with(
            ['customer:id,customer_name,kra_pin'],
            ['coating_jobs:id,coating_prefix,coating_suffix']
          )->get()
          ->append(['grand_total']);
      } else {
        return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'amount_due', 'discount', 'created_at', 'customer_id')
          ->orderBy('invoice_suffix', 'desc')
          ->whereDate('created_at', '>=', $pastThirtyDays->format('Y-m-d'))
          ->with(
            ['customer:id,customer_name,kra_pin'],
            ['coating_jobs:id,coating_prefix,coating_suffix']
          )->get()
          ->append('grand_total');
      }
    });

    if ($request->is('api/*')) {
      return $invoices;
    } else {
      $allInvoices = Invoice::select('invoice_prefix', 'invoice_suffix')->orderBy('invoice_suffix')->get()->chunk(500);
      return view('system.invoices.index', [
        'invoices' => $invoices,
        'allInvoices' => $allInvoices
      ]);
    }
  }

  public function indexExternal(Request $request)
  {
    $this->authorize('viewAny', Invoice::class);
    if (!Gate::allows('accounting')) {
      $invoices = Invoice::select('id', 'ext_invoice_prefix', 'ext_invoice_suffix', 'discount', 'created_at', 'customer_id')
        ->orderBy('ext_invoice_suffix', 'desc')
        ->where('external', '=', 1)
        ->with(
          ['customer:id,customer_name,kra_pin'],
          ['coating_jobs:id,coating_prefix,coating_suffix']
        )->get()
        ->append(['grand_total', 'amount_due']);
    } else {
      $invoices = Invoice::select('id', 'ext_invoice_prefix', 'discount', 'amount_due', 'ext_invoice_suffix', 'created_at', 'customer_id')
        ->orderBy('ext_invoice_suffix', 'desc')
        ->where('external', '=', 1)
        ->with(
          ['customer:id,customer_name,kra_pin'],
          ['coating_jobs:id,coating_prefix,coating_suffix']
        )->get()
        ->append('grand_total');
    }

    if ($request->is('api/*')) {
      return $invoices;
    } else {
      return view('system.invoices.ext-index', [
        'invoices' => $invoices
      ]);
    }
  }

  public function agedInvoices(Request $request)
  {
    $this->authorize('viewAny', Invoice::class);
    $invoices = Invoice::orderBy('invoice_suffix', 'desc')->get();

    $invoices = Cache::remember('aged_invoices', (60 * 60 * 2), function () {
      $pastThirtyDays = Carbon::now()->subDays(30);
      return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'created_at', 'customer_id')
        ->orderBy('invoice_suffix', 'desc')
        ->whereDate('created_at', '<', $pastThirtyDays->format('Y-m-d'))
        ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
    });

    $html = view('system.invoices.misc.searchlist', [
      'invoices' => $invoices,
      'number' => $request->number
    ])->render();

    return $html;
  }

  public function store(Request $request)
  {
    $this->authorize('create', Invoice::class);
    $request->validate([
      'customer_id' => ['required'],
      'cu_number_prefix' => ['required'],
      'cu_number_suffix' => ['required'],
      'job_id' => ['required']
    ]);

    $invoice = new Invoice();
    if ($request->external) {
      $invoice->fill([
        'ext_invoice_prefix' => $invoice->next_ext_invoice_prefix,
        'ext_invoice_suffix' => $invoice->next_ext_invoice_suffix,
        'external' => $request->external,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $invoice->fill([
        'invoice_prefix' => $invoice->next_invoice_prefix,
        'invoice_suffix' => $invoice->next_invoice_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    if ($invoice->save()) {
      $job = CoatingJob::find($request->job_id);

      $job->fill([
        'status' => CoatingJobStatusEnum::CLOSED,
        'invoice_id' => $invoice->id
      ]);

      $job->update();

      if ($request->combined_jobcards) {
        foreach ($request->combined_jobcards as $coatingjobid) {
          $job = CoatingJob::find($coatingjobid);

          $job->fill([
            'status' => CoatingJobStatusEnum::CLOSED,
            'invoice_id' => $invoice->id
          ]);
          $job->updateAmounts();
          $job->update();
        }
      }
      $invoice->calculateAmountDue();
      Cache::forget('upto_thirty_days_invoices');
      if ($request->external) {
        return redirect('/invoices/external')->with('Success', 'Created successfully');
      }
      return redirect('/invoices')->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function show(Request $request, Invoice $invoice)
  {
    $this->authorize('viewAny', Invoice::class);
    return view('system.invoices.document', [
      'invoice' => $invoice
    ]);
  }

  public function edit(Invoice $invoice)
  {
    if (!Gate::allows('accounting')) {
      abort(403);
    }
    return view('system.invoices.edit', [
      'invoice' => $invoice,
    ]);
  }

  public function update(Request $request, Invoice $invoice)
  {
    if (!Gate::allows('accounting')) {
      abort(403);
    }
    if ($request->external) {
      $invoice->fill([
        'ext_invoice_prefix' => $request->ext_invoice_prefix,
        'ext_invoice_suffix' => $request->ext_invoice_suffix,
        'discount' => $request->discount,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix
      ]);
    } else {
      $invoice->fill([
        'invoice_prefix' => $request->invoice_prefix,
        'invoice_suffix' => $request->invoice_suffix,
        'discount' => $request->discount,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix
      ]);
    }

    if ($invoice->update()) {
      Cache::forget('upto_thirty_days_invoices');
      if ($request->external) {
        return redirect('/invoices/external')->with('Success', 'Created successfully');
      }
      return redirect('/invoices')->with('Success', 'Succesfully edited');
    } else {
      return back()->with('Error', 'Failed to update please retry');
    }
  }

  public function destroy(Invoice $invoice)
  {
    $invoice->fill([
      'cancelled_at' => Carbon::now()
    ]);
    if ($invoice->update()) {
      foreach ($invoice->coatingjobs as $coatingjob) {
        $job = CoatingJob::find($coatingjob->id);
        if ($coatingjob->coating_suffix) {
          $job->fill([
            'status' => CoatingJobStatusEnum::OPEN->value,
            'invoice_id' => null
          ]);
        } else {
          $job->fill([
            'invoice_id' => null
          ]);
        }
        $job->update();
      }
      return back()->with('Success', 'Successfully updated');
    } else {
      return back()->with('Error', 'Failed. Please retry');
    }
  }

  public function createDirectInvoice()
  {
    $this->authorize('create', Invoice::class);

    $customers = Customer::all();

    $powders = Powder::all();

    $inventoryItemsCollection = collect(InventoryItem::all()->toArray());

    $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();

    $vat = Tax::where('type', TaxTypesEnum::VAT)->first();

    $ownerEnums = CoatingJobOwnerEnum::cases();

    $invoice = new Invoice();

    return view('system.invoices.create-direct', [
      'customers' => $customers,
      'powders' => $powders,
      'inventoryitems' => $inventoryitems,
      'vat' => $vat,
      'ownerEnums' => $ownerEnums,
      'invoice' => $invoice
    ]);
  }

  public function storeDirectInvoice(Request $request)
  {
    $this->authorize('create', Invoice::class);

    $request->validate([
      'customer_id' => ['required'],
      'cu_number_prefix' => ['required'],
      'cu_number_suffix' => ['required']
    ]);

    if (count($request->maruti_direct_item_id) < 1) {
      return back()->with('Error', 'Failed to create. Please retry');
    }

    $coatingjob = new CoatingJob();

    $invoice = new Invoice();

    if ($request->external) {
      $invoice->fill([
        'ext_invoice_prefix' => $invoice->next_ext_invoice_prefix,
        'ext_invoice_suffix' => $invoice->next_ext_invoice_suffix,
        'external' => 1,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $invoice->fill([
        'invoice_prefix' => $invoice->next_invoice_prefix,
        'invoice_suffix' => $invoice->next_invoice_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    $invoice->save();

    $coatingjob->fill([
      'customer_id' => $request->customer_id,
      'lpo' => $request->lpo,
      'status' => CoatingJobStatusEnum::CLOSED,
      'sale_by' => $request->sale_by,
      'sale_by' => auth()->user()->id,
      'created_by' => auth()->user()->id,
      'company_id' => auth()->user()->company_id,
      'invoice_id' => $invoice->id
    ]);

    if ($coatingjob->save()) {
      $this->addMarutiDirectItems($request, $coatingjob);
      $coatingjob->updateAmounts();
      if ($request->combined_jobcards) {
        foreach ($request->combined_jobcards as $coatingjobid) {
          $job = CoatingJob::find($coatingjobid);

          $job->fill([
            'status' => CoatingJobStatusEnum::CLOSED,
            'invoice_id' => $invoice->id
          ]);
          $job->updateAmounts();
          $job->update();
        }
      }
      $invoice->calculateAmountDue();
      if ($request->external) {
        return redirect('/invoices/external')->with('Success', 'Succesfully created');
      } else {
        Cache::forget('upto_thirty_days_invoices');
        return redirect('/invoices')->with('Success', 'Succesfully created');
      }
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  private function addMarutiDirectItems(Request $request, CoatingJob $coatingjob)
  {
    for ($i = 0; $i < count($request->maruti_direct_item_id); $i++) {
      $coatingjobMarutiItem = new CoatingJobMarutiItem();

      if ($request->maruti_direct_item_id[$i] == "" || $request->maruti_direct_item_id[$i] == null) {
        $coatingjobMarutiItem->fill([
          'coating_job_id' => $coatingjob->id,
          'inventory_item_id' => $request->maruti_item_id[$i],
          'uom' => strtoupper($request->maruti_item_uom[$i] ?? 'UNITS'),
          'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
          'quantity' => $request->maruti_item_qty[$i] ?? 1,
          'vat' => $request->maruti_item_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
          'vat_inclusive' => ($request->maruti_item_vat_inclusive[$i] == 'Yes') ? 1 : 0
        ]);
      } else {
        if ($request->maruti_direct_inventory_type[$i] == "Powder") {
          $coatingjobMarutiItem->fill([
            'coating_job_id' => $coatingjob->id,
            'powder_id' => $request->maruti_direct_item_id[$i],
            'uom' => strtoupper($request->maruti_direct_uom[$i]),
            'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
            'quantity' => $request->maruti_direct_item_kg[$i],
            'vat' => $request->maruti_direct_unit_vat[$i],
            'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
          ]);
        } else {
          $coatingjobMarutiItem->fill([
            'coating_job_id' => $coatingjob->id,
            'inventory_item_id' => $request->maruti_direct_item_id[$i],
            'uom' => strtoupper($request->maruti_direct_uom[$i]),
            'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
            'quantity' => $request->maruti_direct_item_qty[$i],
            'vat' => $request->maruti_direct_unit_vat[$i],
            'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
          ]);
        }
      }
      $coatingjobMarutiItem->save();
    }
  }

  public function undoInvoice(Request $request, Invoice $invoice)
  {
    $this->authorize('update', Invoice::class);
    $coatingjobs = $invoice->coatingjobs;
    foreach ($coatingjobs as $coatingjob) {
      $coatingjob->fill([
        'invoice_id' => null,
        'status' => CoatingJobStatusEnum::OPEN->value
      ]);
      $coatingjob->update();
    }
    $invoice->forceDelete();
    Cache::forget('upto_thirty_days_invoices');
    return back()->with('Success', 'Invoice undone and job cards re-opened');
  }

  public function invoiceSections(Request $request)
  {
    $this->authorize('viewAny', Invoice::class);
    $invoices = Cache::remember('section_invoice_' . $request->minimum . '_' . $request->maximum, (60 * 2), function () use ($request) {

      if (!Gate::allows('accounting')) {
        return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'discount', 'created_at', 'customer_id')
          ->orderBy('invoice_suffix', 'desc')
          ->where('invoice_suffix', '>=', $request->minimum)
          ->where('invoice_suffix', '<=', $request->maximum)
          ->with(
            ['customer:id,customer_name,kra_pin'],
            ['coating_jobs:id,coating_prefix,coating_suffix']
          )->get()
          ->append(['grand_total', 'amount_due']);
      } else {
        return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'discount', 'created_at', 'customer_id')
          ->orderBy('invoice_suffix', 'desc')
          ->where('invoice_suffix', '>=', $request->minimum)
          ->where('invoice_suffix', '<=', $request->maximum)
          ->with(
            ['customer:id,customer_name,kra_pin'],
            ['coating_jobs:id,coating_prefix,coating_suffix']
          )->get()
          ->append('grand_total');
      }
    });

    if ($request->is('api/*')) {
      return $invoices;
    } else {
      $allInvoices = Invoice::select('invoice_prefix', 'invoice_suffix')->orderBy('invoice_suffix')->get()->chunk(500);
      return view('system.invoices.index', [
        'invoices' => $invoices,
        'allInvoices' => $allInvoices
      ]);
    }
  }

  public function updateAmountDue(){
    
    $invoices = Invoice::select('id', 'invoice_prefix', 'invoice_suffix')->where('id', '>', 3247)->get();
    echo "<pre>";
    foreach ($invoices as $invoice) {
      echo "<p>". $invoice->id ."-". $invoice->invoice_prefix.$invoice->invoice_suffix  ."-". $invoice->grand_total ."</p>";
      $invoice->calculateAmountDue();
    }
  }
}
