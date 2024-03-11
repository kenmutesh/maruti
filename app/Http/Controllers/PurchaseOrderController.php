<?php

namespace App\Http\Controllers;

use App\Enums\InventoryItemsEnum;
use App\Enums\PowderAndInventoryLogsEnum;
use App\Enums\PurchaseOrderDocumentsEnum;
use App\Enums\PurchaseOrderStatusEnum;
use App\Models\Bin;
use App\Models\Floor;
use App\Models\InventoryItem;
use App\Models\Location;
use App\Models\NonInventoryItem;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDocument;
use App\Models\PurchaseOrderItem;
use App\Models\Shelf;
use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\Warehouse;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', PurchaseOrder::class);
    $purchaseOrders = Cache::remember('purchase_orders_cache', (60 * 3), function () {
      if (!Gate::allows('accounting')) {
        return PurchaseOrder::select('id', 'lpo_suffix', 'lpo_prefix', 'record_date', 'sum_grandtotal', 'status', 'supplier_id')->orderBy('id', 'desc')->with(['supplier:id,supplier_name'])->get();
      }else{
        return PurchaseOrder::select('id', 'lpo_suffix', 'lpo_prefix', 'record_date', 'sum_grandtotal', 'status', 'supplier_id', 'amount_due')->orderBy('id', 'desc')->with(['supplier:id,supplier_name'])->get();
      }
      
    });
    if ($request->is('api/*')) {
      return $purchaseOrders;
    } else {
      $suppliers = Supplier::select('id', 'supplier_name')->get();
      return view('system.purchaseorders.index', [
        'purchaseOrders' => $purchaseOrders,
        'suppliers' => $suppliers,
      ]);
    }
  }

  public function show(Request $request, PurchaseOrder $purchaseorder)
  {
    $this->authorize('viewAny', PurchaseOrder::class);
    if ($request->is('api/*')) {
      return $purchaseorder;
    } else {
      $hidePrice = isset($request->hideprice) ? true : 0;
      return view('system.purchaseorders.doc', [
        'hidePrice' => $hidePrice,
        'purchaseorder' => $purchaseorder,
      ]);
    }
  }

  public function create()
  {
    $this->authorize('create', PurchaseOrder::class);
    $suppliers = Supplier::all();

    $purchaseorder = new PurchaseOrder();

    $powders = Powder::all();

    $inventoryItemsCollection = collect(InventoryItem::all()->toArray());

    $inventoryItems = $inventoryItemsCollection->groupBy('type')->all();

    $nonInventoryItems = NonInventoryItem::all();

    return view('system.purchaseorders.create', [
      'purchaseorder' => $purchaseorder,
      'suppliers' => $suppliers,
      'powders' => $powders,
      'inventoryitems' => $inventoryItems,
      'noninventoryitems' => $nonInventoryItems,
    ]);
  }

  public function store(Request $request)
  {
    $this->authorize('create', PurchaseOrder::class);
    $request->validate([
      'supplier_id' => ['required'],
      'record_date' => ['required'],
      'currency' => ['required'],
      'item_id' => ['required'],
      'grand_total' => ['required'],
      'terms' => ['required'],
    ]);

    if (count($request->item_id) < 1) {
      return back()->withInput()->with('Error', 'You need to have at least one item in the list');
    }

    $purchaseorder = new PurchaseOrder();

    if($request->without_po){
      $purchaseorder->fill([
        'record_date' => $request->record_date,
        'due_date' => $request->due_date,
        'quotation_ref' => $request->quotation_ref,
        'memo_ref' => $request->memo_ref,
        'currency' => $request->currency,
        'discount' => $request->grand_total_discount ?? 0,
        'terms' => strtoupper($request->terms),
        'supplier_id' => $request->supplier_id,
        'company_id' => auth()->user()->company_id
      ]);
    }else{
      $purchaseorder->fill([
        'lpo_prefix' => $request->lpo_prefix,
        'lpo_suffix' => $request->lpo_suffix,
        'record_date' => $request->record_date,
        'due_date' => $request->due_date,
        'quotation_ref' => $request->quotation_ref,
        'memo_ref' => $request->memo_ref,
        'currency' => $request->currency,
        'discount' => $request->grand_total_discount ?? 0,
        'terms' => strtoupper($request->terms),
        'supplier_id' => $request->supplier_id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    if ($purchaseorder->save()) {
      $this->createPurchaseOrderItems($request, $purchaseorder);
      $this->createPurchaseOrderDocuments($request, $purchaseorder);
      $purchaseorder->updateAmounts();
      Cache::forget('purchase_orders_cache');
      return redirect('/purchaseorders')->with('Success', 'Created succesfully');
    } else {
      return back()->withInput()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function edit(PurchaseOrder $purchaseorder)
  {
    $this->authorize('update', [PurchaseOrder::class, $purchaseorder]);
    $suppliers = Supplier::all();

    $powders = Powder::all();

    $inventoryItemsCollection = collect(InventoryItem::all()->toArray());

    $inventoryItems = $inventoryItemsCollection->groupBy('type')->all();

    $nonInventoryItems = NonInventoryItem::all();

    $purchaseorder->purchaseorderdocuments;

    return view('system.purchaseorders.edit', [
      'purchaseorder' => $purchaseorder,
      'suppliers' => $suppliers,
      'powders' => $powders,
      'inventoryitems' => $inventoryItems,
      'noninventoryitems' => $nonInventoryItems,
    ]);
  }

  public function update(Request $request, PurchaseOrder $purchaseorder)
  {
    $this->authorize('update', [PurchaseOrder::class, $purchaseorder]);
    $request->validate([
      'supplier_id' => ['required'],
      'record_date' => ['required'],
      'currency' => ['required'],
      'item_id' => ['required'],
      'grand_total' => ['required'],
      'terms' => ['required'],
    ]);

    if (count($request->item_id) < 1) {
      return back()->withInput()->with('Error', 'You need to have at least one item in the list');
    }

    $purchaseorder->fill([
      'lpo_prefix' => $request->lpo_prefix,
      'lpo_suffix' => $request->lpo_suffix,
      'record_date' => $request->record_date,
      'due_date' => $request->due_date,
      'quotation_ref' => $request->quotation_ref,
      'memo_ref' => $request->memo_ref,
      'currency' => $request->currency,
      'discount' => $request->grand_total_discount ?? 0,
      'terms' => strtoupper($request->terms),
      'supplier_id' => $request->supplier_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($purchaseorder->update()) {
      $this->updatePurchaseOrderItems($request, $purchaseorder);
      $this->updatePurchaseOrderDocuments($request);
      $this->createPurchaseOrderDocuments($request, $purchaseorder);
      $purchaseorder->updateAmounts();
      return redirect('/purchaseorders')->with('Success', 'Edited succesfully');
    } else {
      return back()->withInput()->with('Error', 'Failed to edit. Please retry');
    }
  }

  public function destroy(Request $request, PurchaseOrder $purchaseorder)
  {
    if ($purchaseorder->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry.');
    }
  }

  public function cancel(Request $request, PurchaseOrder $purchaseorder)
  {
    $purchaseorder->status = PurchaseOrderStatusEnum::CANCELLED;
    if ($purchaseorder->update()) {
      return back()->with('Success', 'Cancelled successfully');
    } else {
      return back()->with('Success', 'Failed to cancel. Please retry');
    }
  }

  private function createPurchaseOrderItems(Request $request, PurchaseOrder $purchaseorder)
  {
    for ($i = 0; $i < count($request->item_id); $i++) {
      $purchaseOrderItem = new PurchaseOrderItem();
      if (strpos($request->item_id[$i], "New") === 0) {
        if ($request->item_type[$i] == "Powder") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'new_item_name' => strtoupper($request->item_name[$i]),
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_kg[$i]
          ]);
        } else {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'new_item_name' => strtoupper($request->item_name[$i]),
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        }
        $purchaseOrderItem->item_type = strtoupper($request->item_type[$i]);
        $purchaseOrderItem->save();
      } else {
        if ($request->item_type[$i] == "Powder") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'powder_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_kg[$i]
          ]);
        } else if ($request->item_type[$i] == "Non Inventory") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'non_inventory_item_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        } else {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'inventory_item_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        }
        $purchaseOrderItem->item_type = strtoupper($request->item_type[$i]);
        $purchaseOrderItem->save();
      }
    }
  }

  private function updatePurchaseOrderItems(Request $request, PurchaseOrder $purchaseorder)
  {
    for ($i = 0; $i < count($request->item_id); $i++) {
      $purchaseOrderItem = PurchaseOrderItem::find($request->purchase_order_item_id[$i]);
      if (!$purchaseOrderItem) {
        $purchaseOrderItem = new PurchaseOrderItem();
      }
      if (strpos($request->item_id[$i], "New") === 0) {
        if (strtoupper($request->item_type[$i]) == "POWDER") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'new_item_name' => strtoupper($request->item_name[$i]),
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_kg[$i]
          ]);
        } else {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'new_item_name' => strtoupper($request->item_name[$i]),
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        }
        $purchaseOrderItem->item_type = strtoupper($request->item_type[$i]);
        if ($purchaseOrderItem->exists) {
          $purchaseOrderItem->update();
        } else {
          $purchaseOrderItem->save();
        }
      } else {
        if (strtoupper($request->item_type[$i]) == "POWDER") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'powder_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_kg[$i]
          ]);
        } else if (strtoupper($request->item_type[$i]) == "NON INVENTORY") {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'non_inventory_item_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        } else {
          $purchaseOrderItem->fill([
            'purchase_order_id' => $purchaseorder->id,
            'inventory_item_id' => $request->item_id[$i],
            'cost' => $request->unit_cost_without_vat[$i],
            'vat' => $request->item_vat[$i],
            'quantity' => $request->item_qty[$i]
          ]);
        }
        $purchaseOrderItem->item_type = strtoupper($request->item_type[$i]);
        if ($purchaseOrderItem->exists) {
          $purchaseOrderItem->update();
        } else {
          $purchaseOrderItem->save();
        }
      }
    }
  }

  private function createPurchaseOrderDocuments(Request $request, PurchaseOrder $purchaseorder)
  {
    if ($request->file('quotation_docs') != NULL) {
      foreach ($request->file('quotation_docs') as $image) {
        $systemName = $this->uploadFile($image, 'public/purchaseorders');
        $originalImageName = $image->getClientOriginalName();
        $purchaseOrderDocument = new PurchaseOrderDocument();
        $purchaseOrderDocument->fill([
          'purchase_order_id' => $purchaseorder->id,
          'type' => PurchaseOrderDocumentsEnum::QUOTATION,
          'document_path' => $systemName,
          'document_name' => $originalImageName
        ]);
        $purchaseOrderDocument->save();
      }
    }

    if ($request->file('memo_docs') != NULL) {
      foreach ($request->file('memo_docs') as $image) {
        $systemName = $this->uploadFile($image, 'public/purchaseorders');
        $originalImageName = $image->getClientOriginalName();
        $purchaseOrderDocument = new PurchaseOrderDocument();
        $purchaseOrderDocument->fill([
          'purchase_order_id' => $purchaseorder->id,
          'type' => PurchaseOrderDocumentsEnum::MEMO,
          'document_path' => $systemName,
          'document_name' => $originalImageName
        ]);
        $purchaseOrderDocument->save();
      }
    }

    if ($request->file('invoice_docs') != NULL) {
      foreach ($request->file('invoice_docs') as $image) {
        $systemName = $this->uploadFile($image, 'public/purchaseorders');
        $originalImageName = $image->getClientOriginalName();
        $purchaseOrderDocument = new PurchaseOrderDocument();
        $purchaseOrderDocument->fill([
          'purchase_order_id' => $purchaseorder->id,
          'type' => PurchaseOrderDocumentsEnum::INVOICE,
          'document_path' => $systemName,
          'document_name' => $originalImageName
        ]);
        $purchaseOrderDocument->save();
      }
    }

    if ($request->file('delivery_docs') != NULL) {
      foreach ($request->file('delivery_docs') as $image) {
        try {
          $systemName = $this->uploadFile($image, 'public/purchaseorders');
        } catch (\Throwable $th) {
          info($th->getMessage());
        }
        $originalImageName = $image->getClientOriginalName();
        $purchaseOrderDocument = new PurchaseOrderDocument();
        $purchaseOrderDocument->fill([
          'purchase_order_id' => $purchaseorder->id,
          'type' => PurchaseOrderDocumentsEnum::DELIVERY,
          'document_path' => $systemName,
          'document_name' => $originalImageName
        ]);
        $purchaseOrderDocument->save();
      }
    }
  }

  private function updatePurchaseOrderDocuments(Request $request)
  {
    if ($request->document) {
      foreach ($request->document as $document) {
        $purchaseOrderDocument = PurchaseOrderDocument::find($document);
        $purchaseOrderDocument->delete();
      }
    }
  }

  public function completeCreate(Request $request, PurchaseOrder $purchaseorder)
  {
    $locations = Location::all();
    $warehouses = Warehouse::all();
    $floors = Floor::with(['warehouse'])->get();
    $shelves = Shelf::with(['floor'])->get();
    $bins = Bin::with(['shelf'])->get();
    $inventoryItemTypes = InventoryItemsEnum::cases();
    return view('system.purchaseorders.complete', [
      'purchaseorder' => $purchaseorder,
      'locations' => $locations,
      'warehouses' => $warehouses,
      'floors' => $floors,
      'shelves' => $shelves,
      'bins' => $bins,
      'inventoryItemTypes' => $inventoryItemTypes,
    ]);
  }

  public function complete(Request $request, PurchaseOrder $purchaseorder)
  {
    // $this->authorize('update', [PurchaseOrder::class, $purchaseorder]);
    $request->validate([
      'warehouse_id' => ['required'],
      'floor_id' => ['required'],
      'shelf_id' => ['required'],
      'bin_id' => ['required']
    ]);
    $purchaseorder->fill([
      'invoice_ref' => $request->invoice_ref,
      'delivery_ref' => $request->delivery_ref,
      'status' => PurchaseOrderStatusEnum::CLOSED,
      'warehouse_id' => $request->warehouse_id,
      'floor_id' => $request->floor_id,
      'shelf_id' => $request->shelf_id,
      'bin_id' => $request->bin_id,
    ]);

    if ($purchaseorder->update()) {
      $this->createPurchaseOrderDocuments($request, $purchaseorder);
      $this->storePurchaseOrderItems($request, $purchaseorder);
      $purchaseorder->calculateAmountDue();
      Cache::forget('purchase_orders_cache');
      return redirect('/purchaseorders')->with('Success', 'Completed succesfully');
    } else {
      return back()->withInput()->with('Error', 'Failed to complete. Please retry');
    }
  }

  private function storePurchaseOrderItems(Request $request, PurchaseOrder $purchaseorder)
  {
    foreach ($request->purchase_order_item as $item) {
      $purchaseorderitem = json_decode($item);
      if ($purchaseorderitem->new) {
        if ($purchaseorderitem->type == "POWDER") {
          $powder = new Powder();

          $powder->fill([
            'powder_color' => strtoupper($purchaseorderitem->powder_color),
            'powder_code' => strtoupper($purchaseorderitem->powder_code),
            'powder_description' => strtoupper($purchaseorderitem->powder_description),
            'serial_no' => strtoupper($purchaseorderitem->serial_no),
            'manufacture_date' => $purchaseorderitem->manufacture_date,
            'expiry_date' => $purchaseorderitem->expiry_date,
            'goods_weight' => $purchaseorderitem->goods_weight,
            'batch_no' => strtoupper($purchaseorderitem->batch_no),
            'standard_cost' => $purchaseorderitem->standard_cost,
            'standard_cost_vat' => $purchaseorderitem->standard_cost_vat,
            'standard_price' => $purchaseorderitem->standard_price,
            'standard_price_vat' => $purchaseorderitem->standard_price_vat,
            'min_threshold' => $purchaseorderitem->min_threshold,
            'max_threshold' => $purchaseorderitem->max_threshold,
            'current_weight' => $purchaseorderitem->weight_added,
            'opening_weight' => $purchaseorderitem->weight_added,
            'supplier_id' => $request->supplier_id,
            'company_id' => auth()->user()->company_id
          ]);

          $powder->save();
        }else if($purchaseorderitem->type == "NON INVENTORY"){
          $nonInventoryItem = new NonInventoryItem();

          $nonInventoryItem->fill([
              'item_name' => strtoupper($purchaseorderitem->item_name),
              'standard_cost' => $purchaseorderitem->standard_cost,
              'standard_cost_vat' => $purchaseorderitem->vat ?? 0,
              'supplier_id' => $request->supplier_id,
              'company_id' => auth()->user()->company_id
          ]);

          $nonInventoryItem->save();
        }else{
          $inventoryItem = new InventoryItem();

          $inventoryItem->fill([
              'item_name' => strtoupper($purchaseorderitem->item_name),
              'item_code' => strtoupper($purchaseorderitem->item_code),
              'item_description' => strtoupper($purchaseorderitem->item_description),
              'serial_no' => strtoupper($purchaseorderitem->serial_no),
              'quantity_tag' => strtoupper($purchaseorderitem->quantity_tag),
              'type' => $purchaseorderitem->inventory_type,
              'goods_weight' => $purchaseorderitem->goods_weight,
              'standard_cost' => $purchaseorderitem->standard_cost,
              'standard_cost_vat' => $purchaseorderitem->standard_cost_vat,
              'standard_price' => $purchaseorderitem->standard_price,
              'standard_price_vat' => $purchaseorderitem->standard_price_vat,
              'min_threshold' => $purchaseorderitem->min_threshold,
              'max_threshold' => $purchaseorderitem->max_threshold,
              'opening_quantity' => $purchaseorderitem->quantity_added,
              'current_quantity' => $purchaseorderitem->quantity_added,
              'supplier_id' => $request->supplier_id,
              'company_id' => auth()->user()->company_id
          ]);
          $inventoryItem->save();
        }
      } else {
        $powderAndInventoryLog = new PowderAndInventoryLog();
        if ($purchaseorderitem->type == "POWDER") {
          $powderAndInventoryLog->fill([
            'reason' => PowderAndInventoryLogsEnum::PURCHASEORDER,
            'reason_id' => $purchaseorder->id,
            'sum_added' => $purchaseorderitem->weight_added,
            'powder_id' => $purchaseorderitem->id,
            'warehouse_id' => $request->warehouse_id,
            'floor_id' => $request->floor_id,
            'shelf_id' => $request->shelf_id,
            'bin_id' => $request->bin_id
          ]);
          $powderAndInventoryLog->save();
        }else if($purchaseorderitem->type == "NON INVENTORY"){
          $powderAndInventoryLog->fill([
            'reason' => PowderAndInventoryLogsEnum::PURCHASEORDER,
            'reason_id' => $purchaseorder->id,
            'sum_added' => $purchaseorderitem->quantity_added,
            'non_inventory_item_id' => $purchaseorderitem->id,
            'warehouse_id' => $request->warehouse_id,
            'floor_id' => $request->floor_id,
            'shelf_id' => $request->shelf_id,
            'bin_id' => $request->bin_id
          ]);
          $powderAndInventoryLog->save();
        }else{
          $powderAndInventoryLog->fill([
            'reason' => PowderAndInventoryLogsEnum::PURCHASEORDER,
            'reason_id' => $purchaseorder->id,
            'sum_added' => $purchaseorderitem->quantity_added,
            'inventory_item_id' => $purchaseorderitem->id,
            'warehouse_id' => $request->warehouse_id,
            'floor_id' => $request->floor_id,
            'shelf_id' => $request->shelf_id,
            'bin_id' => $request->bin_id
          ]);
          $powderAndInventoryLog->save();
        }
      }
    }
  }

  function completeShow(Request $request, PurchaseOrder $purchaseorder){
    return view('system.purchaseorders.completed-detail', [
      'purchaseorder' => $purchaseorder,
    ]);
  }

  public function updateAmountDue(){
    
    $purchases = PurchaseOrder::select('id', 'lpo_prefix', 'lpo_suffix')->get();
    echo "<pre>";
    foreach ($purchases as $purchase) {
      echo "<p>". $purchase->id ."-". $purchase->lpo_prefix.$purchase->lpo_suffix  ."-". $purchase->grand_total ."</p>";
      $purchase->updateAmounts();
      $purchase->calculateAmountDue();
    }
  }
}
