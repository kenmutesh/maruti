<?php

namespace App\Http\Controllers;

use App\Enums\CoatingJobOwnerEnum;
use App\Enums\CoatingJobStatusEnum;
use App\Enums\TaxTypesEnum;
use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Powder;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Cache;

class CashSaleController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', CashSale::class);
    $cashSales = Cache::remember('upto_thirty_days_cash_sales', (60 * 2), function () {
      $pastThirtyDays = Carbon::now()->subDays(30);
      return CashSale::select('id', 'cash_sale_prefix', 'cash_sale_suffix', 'discount', 'created_at', 'customer_id', 'external', 'cancelled_at')
        ->orderBy('cash_sale_suffix', 'desc')
        ->where('external', '=', 0)
        ->whereDate('created_at', '>=', $pastThirtyDays->format('Y-m-d'))
        ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
    });

    if ($request->is('api/*')) {
      return $cashSales;
    } else {
      $allCashSales = CashSale::select('id', 'cash_sale_prefix', 'cash_sale_suffix')->where('external', 0)->orderBy('cash_sale_suffix')->get()->chunk(500);
      return view('system.cashsales.index', [
        'cashSales' => $cashSales,
        'allCashSales' => $allCashSales
      ]);
    }
  }

  public function indexExternal(Request $request)
  {
    $this->authorize('viewAny', CashSale::class);
    $cashSales = CashSale::select('id', 'ext_cash_sale_prefix', 'ext_cash_sale_suffix', 'created_at', 'customer_id', 'external', 'cancelled_at')
      ->where('external', '=', 1)
      ->orderBy('ext_cash_sale_suffix', 'desc')
      ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();

    if ($request->is('api/*')) {
      return $cashSales;
    } else {
      return view('system.cashsales.ext-index', [
        'cashSales' => $cashSales
      ]);
    }
  }

  public function agedCashSales(Request $request)
  {
    $this->authorize('viewAny', CoatingJob::class);
    $cashSales = Cache::remember('aged_cash_sales', (60 * 60 * 2), function () {
      $pastThirtyDays = Carbon::now()->subDays(30);
      return CashSale::select('id', 'cash_sale_prefix', 'cash_sale_suffix', 'created_at', 'customer_id')
        ->orderBy('cash_sale_suffix', 'desc')
        ->orderBy('ext_cash_sale_suffix', 'desc')
        ->whereDate('created_at', '<', $pastThirtyDays->format('Y-m-d'))
        ->with(['customer:id,customer_name'])->get();
    });

    $html = view('system.cashsales.misc.searchlist', [
      'cashSales' => $cashSales,
      'number' => $request->number
    ])->render();

    return $html;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $cashsale = new CashSale();

    if ($request->external) {
      $cashsale->fill([
        'ext_cash_sale_prefix' => $cashsale->next_ext_cash_sale_prefix,
        'ext_cash_sale_suffix' => $cashsale->next_ext_cash_sale_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'external' => $request->external,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $cashsale->fill([
        'cash_sale_prefix' => $cashsale->next_cash_sale_prefix,
        'cash_sale_suffix' => $cashsale->next_cash_sale_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    if ($cashsale->save()) {
      $this->refreshCache();
      $job = CoatingJob::find($request->job_id);

      $job->fill([
        'status' => CoatingJobStatusEnum::CLOSED,
        'cash_sale_id' => $cashsale->id
      ]);

      $job->update();

      if ($request->combined_jobcards) {
        foreach ($request->combined_jobcards as $coatingjobid) {
          $job = CoatingJob::find($coatingjobid);

          $job->fill([
            'status' => CoatingJobStatusEnum::CLOSED,
            'cash_sale_id' => $cashsale->id
          ]);

          $job->update();
        }
      }
      return redirect('/cashsales')->with('Success', 'Succesfully created');
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function show(Request $request, CashSale $cashsale)
  {
    $this->authorize('viewAny', CashSale::class);
    return view('system.cashsales.document', [
      'cashsale' => $cashsale
    ]);
  }

  public function edit(CashSale $cashsale)
  {
    if (!Gate::allows('accounting')) {
      abort(403);
    }
    return view('system.cashsales.edit', [
      'cashsale' => $cashsale
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Bin  $bin
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, CashSale $cashsale)
  {
    if (!Gate::allows('accounting')) {
      abort(403);
    }

    if ($request->external) {
      $cashsale->fill([
        'ext_cash_sale_prefix' => $request->ext_cash_sale_prefix,
        'ext_cash_sale_suffix' => $request->ext_cash_sale_suffix,
        'discount' => $request->discount,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix
      ]);
    } else {
      $cashsale->fill([
        'cash_sale_prefix' => $request->cash_sale_prefix,
        'cash_sale_suffix' => $request->cash_sale_suffix,
        'discount' => $request->discount,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix
      ]);
    }

    if ($cashsale->update()) {
      if ($request->external) {
        return redirect('/cashsales/external')->with('Success', 'Succesfully edited');
      } else {
        $this->refreshCache();
        return redirect('/cashsales')->with('Success', 'Succesfully edited');
      }
    } else {
      return back()->with('Error', 'Failed to update please retry');
    }
  }

  public function destroy(CashSale $cashsale)
  {
    $cashsale->fill([
      'cancelled_at' => Carbon::now()
    ]);
    if ($cashsale->update()) {
      $this->refreshCache();
      return back()->with('Success', 'Successfully updated');
    } else {
      return back()->with('Error', 'Failed. Please retry');
    }
  }

  public function createDirectCashSale()
  {
    $this->authorize('create', CashSale::class);

    $customers = Customer::all();

    $powders = Powder::all();

    $inventoryItemsCollection = collect(InventoryItem::all()->toArray());

    $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();

    $vat = Tax::where('type', TaxTypesEnum::VAT)->first();

    $ownerEnums = CoatingJobOwnerEnum::cases();

    $cashsale = new CashSale();

    return view('system.cashsales.create-direct', [
      'customers' => $customers,
      'powders' => $powders,
      'inventoryitems' => $inventoryitems,
      'vat' => $vat,
      'ownerEnums' => $ownerEnums,
      'cashsale' => $cashsale
    ]);
  }

  public function storeDirectCashSale(Request $request)
  {
    $this->authorize('create', CashSale::class);

    $request->validate([
      'customer_id' => ['required'],
      'cu_number_prefix' => ['required'],
      'cu_number_suffix' => ['required']
    ]);

    if (count($request->maruti_direct_item_id) < 1) {
      return back()->with('Error', 'Failed to create. Please retry');
    }

    $coatingjob = new CoatingJob();

    $cashsale = new CashSale();

    if ($request->external) {
      $cashsale->fill([
        'ext_cash_sale_prefix' => $cashsale->next_ext_cash_sale_prefix,
        'ext_cash_sale_suffix' => $cashsale->next_ext_cash_sale_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'external' => $request->external,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $cashsale->fill([
        'cash_sale_prefix' => $cashsale->next_cash_sale_prefix,
        'cash_sale_suffix' => $cashsale->next_cash_sale_suffix,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'cu_number_prefix' => $request->cu_number_prefix,
        'cu_number_suffix' => $request->cu_number_suffix,
        'created_by' => auth()->user()->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    $cashsale->save();

    $coatingjob->fill([
      'customer_id' => $request->customer_id,
      'lpo' => $request->lpo,
      'status' => CoatingJobStatusEnum::CLOSED,
      'sale_by' => $request->sale_by,
      'sale_by' => auth()->user()->id,
      'created_by' => auth()->user()->id,
      'company_id' => auth()->user()->company_id,
      'cash_sale_id' => $cashsale->id
    ]);

    if ($coatingjob->save()) {
      $this->addMarutiDirectItems($request, $coatingjob);
      $coatingjob->updateAmounts();
      if ($request->combined_jobcards) {
        foreach ($request->combined_jobcards as $coatingjobid) {
          $job = CoatingJob::find($coatingjobid);
  
          $job->fill([
            'status' => CoatingJobStatusEnum::CLOSED,
            'cash_sale_id' => $cashsale->id
          ]);
          $job->updateAmounts();
          $job->update();
        }
      }
      if ($request->external) {
        return redirect('/cashsales/external')->with('Success', 'Succesfully created');
      }else{
        Cache::forget('upto_thirty_days_cash_sales');
        return redirect('/cashsales')->with('Success', 'Succesfully created');
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
          'custom_item_name' => strtoupper($request->custom_item_name[$i]),
          'uom' => strtoupper($request->maruti_direct_uom[$i]),
          'unit_price' => $request->maruti_direct_unit_price[$i],
          'quantity' => $request->maruti_direct_item_qty[$i],
          'vat' => $request->maruti_direct_unit_vat[$i],
          'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
        ]);
      } else {
        if ($request->maruti_direct_inventory_type[$i] == "Powder") {
          $coatingjobMarutiItem->fill([
            'coating_job_id' => $coatingjob->id,
            'powder_id' => $request->maruti_direct_item_id[$i],
            'uom' => strtoupper($request->maruti_direct_uom[$i]),
            'unit_price' => $request->maruti_direct_unit_price[$i],
            'quantity' => $request->maruti_direct_item_kg[$i],
            'vat' => $request->maruti_direct_unit_vat[$i],
            'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
          ]);
        } else {
          $coatingjobMarutiItem->fill([
            'coating_job_id' => $coatingjob->id,
            'inventory_item_id' => $request->maruti_direct_item_id[$i],
            'uom' => strtoupper($request->maruti_direct_uom[$i]),
            'unit_price' => $request->maruti_direct_unit_price[$i],
            'quantity' => $request->maruti_direct_item_qty[$i],
            'vat' => $request->maruti_direct_unit_vat[$i],
            'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
          ]);
        }
      }
      $coatingjobMarutiItem->save();
    }
  }

  private function refreshCache()
  {
    Cache::forget('upto_thirty_days_cash_sales');
  }

  public function undoCashSale(Request $request, CashSale $cashsale)
  {
    $this->authorize('update', CashSale::class);
    $coatingjobs = $cashsale->coatingjobs;
    foreach ($coatingjobs as $coatingjob) {
      $coatingjob->fill([
        'cash_sale_id' => null,
        'status' => CoatingJobStatusEnum::OPEN->value
      ]);
      $coatingjob->update();
    }
    $cashsale->delete();
    $this->refreshCache();
    return back()->with('Success', 'Cash sale undone and job cards re-opened');
  }

  public function cashSaleSections(Request $request)
  {
    $this->authorize('viewAny', CashSale::class);
    $cashSales = Cache::remember('section_cash_sales_' . $request->minimum . '_' . $request->maximum, (60 * 2), function () use ($request) {

      return CashSale::select('id', 'cash_sale_prefix', 'cash_sale_suffix', 'discount', 'created_at', 'customer_id', 'external')
        ->orderBy('cash_sale_suffix', 'desc')
        ->where('external', '=', 0)
        ->where('cash_sale_suffix', '>=', $request->minimum)
        ->where('cash_sale_suffix', '<=', $request->maximum)
        ->with(['customer:id,customer_name,contact_person_name,contact_person_email,kra_pin'])->get();
    });

    if ($request->is('api/*')) {
      return $cashSales;
    } else {
      $allCashSales = CashSale::select('id', 'cash_sale_prefix', 'cash_sale_suffix')->orderBy('cash_sale_suffix')->get()->chunk(500);
      return view('system.cashsales.index', [
        'cashSales' => $cashSales,
        'allCashSales' => $allCashSales
      ]);
    }
  }
}
