<?php

namespace App\Http\Controllers;

use App\Enums\TaxTypesEnum;
use App\Models\InventoryItem;
use App\Models\NonInventoryItem;
use App\Models\Powder;
use App\Models\Supplier;
use App\Models\SupplierCreditNote;
use App\Models\SupplierCreditNoteItem;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SupplierCreditNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $creditNotes = SupplierCreditNote::select('id','supplier_id', 'purchase_order_id', 'sum_grandtotal', 'created_at', 'cancelled_at')->with(['supplier', 'purchaseorder:id,lpo_prefix,lpo_suffix'])->orderBy('id','desc')->get();
        return view('system.supplier-creditnotes.index', [
            'creditNotes' => $creditNotes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Cache::remember('suppliers', (60 * 10), function () {
            return Supplier::select('id', 'supplier_name')->get();
        });

        $creditNote = new SupplierCreditNote();

        $powders = Cache::remember('powder_list_supplier_credit_notes', (60 * 10), function () {
            return Powder::select('id', 'powder_color', 'supplier_id', 'standard_price', 'standard_price_vat', 'current_weight')->get();
        });

        $inventoryItemsCollection = Cache::remember('inventory_list_supplier_credit_notes', (60 * 10), function () {
            return collect(InventoryItem::all()->toArray());
        });

        $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();

        $nonInventoryItems = Cache::remember('non_inventory_list_supplier_credit_notes', (60 * 10), function () {
            return collect(
                NonInventoryItem::select('id', 'item_name', 'standard_cost', 'standard_cost_vat')
                    ->get()
            );
        });
        
        return view('system.supplier-creditnotes.create', [
            'suppliers' => $suppliers,
            'creditNote' => $creditNote,
            'powders' => $powders,
            'inventoryitems' => $inventoryitems,
            'noninventoryitems' => $nonInventoryItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'credit_suffix' => ['required'],
            'date' => ['required'],
            'grand_total' => ['required'],
        ]);

        if(count($request->maruti_direct_item_id)<1){
            return back()->with('Error', 'Not enough items listed');
        }else{
            $creditNote = new SupplierCreditNote();
            $creditNote->fill([
                'supplier_credit_note_prefix' => $creditNote->next_credit_note_prefix,
                'supplier_credit_note_suffix' => $creditNote->next_credit_note_suffix,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'record_date' => $request->date,
                'memo' => strtoupper($request->memo),
                'company_id' => auth()->user()->company_id
            ]);
            if($creditNote->save()){
                $this->addCreditNoteItems($request, $creditNote);
                $creditNote->updateAmounts();
                return redirect('/suppliercreditnotes')->with('Success', 'Created successfully');
            }else{
                return back()->with('Error', 'Failed to create');
            }
        }
    }

    private function addCreditNoteItems(Request $request, SupplierCreditNote $creditNote)
    {
        for ($i = 0; $i < count($request->maruti_direct_item_id); $i++) {
            $creditNoteItem = new SupplierCreditNoteItem();
            
            if ($request->maruti_direct_inventory_type[$i] == "POWDER") {
                $creditNoteItem->fill([
                    'supplier_credit_note_id' => $creditNote->id,
                    'powder_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'KG'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_kg[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);
            } else if($request->maruti_direct_inventory_type[$i] == "NON-INVENTORY") {
                $creditNoteItem->fill([
                    'supplier_credit_note_id' => $creditNote->id,
                    'non_inventory_item_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'UNITS'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_qty[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);
            }else{
                $creditNoteItem->fill([
                    'supplier_credit_note_id' => $creditNote->id,
                    'inventory_item_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'UNITS'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_qty[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);  
            }
            $creditNoteItem->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierCreditNote  $supplierCreditNote
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierCreditNote $suppliercreditnote)
    {
        return view('system.supplier-creditnotes.doc', [
            'creditNote' => $suppliercreditnote
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierCreditNote  $supplierCreditNote
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierCreditNote $supplierCreditNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierCreditNote  $supplierCreditNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierCreditNote $supplierCreditNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierCreditNote  $supplierCreditNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierCreditNote $suppliercreditnote)
    {
        $suppliercreditnote->cancelled_at = Carbon::now();
        if($suppliercreditnote->update()){
            return back()->with('Success', 'Cancelled');
        }else{
            return back()->with('Error', 'Failed');
        }
    }
}
