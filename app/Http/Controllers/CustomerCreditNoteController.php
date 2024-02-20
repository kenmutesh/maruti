<?php

namespace App\Http\Controllers;

use App\Enums\TaxTypesEnum;
use App\Models\Customer;
use App\Models\CustomerCreditNote;
use App\Models\CustomerCreditNoteItem;
use App\Models\InventoryItem;
use App\Models\Powder;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;

class CustomerCreditNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $creditNotes = CustomerCreditNote::select('id','customer_id', 'invoice_id', 'sum_grandtotal', 'created_at', 'cancelled_at')->with(['customer', 'invoice'])->orderBy('id','desc')->get();
        return view('system.customer-creditnotes.index', [
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
        $customers = Cache::remember('customers', (60 * 10), function () {
            return Customer::select('id', 'customer_name', 'company')->get();
        });
        
        $creditNote = new CustomerCreditNote();

        $powders = Cache::remember('powder_list_customer_credit_notes', (60 * 10), function () {
            return Powder::select('id', 'powder_color', 'supplier_id', 'standard_price', 'standard_price_vat', 'current_weight')
                ->with(['supplier:id,supplier_name'])->get();
        });

        $inventoryItemsCollection = Cache::remember('inventory_list_customer_credit_notes', (60 * 10), function () {
            return collect(
                InventoryItem::select('id', 'type', 'item_name', 'item_code', 'quantity_tag', 'standard_price', 'standard_price_vat', 'current_quantity')
                    ->with(['supplier:id,supplier_name'])
                    ->get()->toArray()
            );
        });

        $inventoryitems = $inventoryItemsCollection->groupBy('type')->all();
        
        return view('system.customer-creditnotes.create', [
            'customers' => $customers,
            'creditNote' => $creditNote,
            'powders' => $powders,
            'inventoryitems' => $inventoryitems
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
            'customer_id' => ['required'],
            'date' => ['required'],
            'grand_total' => ['required'],
        ]);

        if(count($request->maruti_direct_item_id)<1){
            return back()->with('Error', 'Not enough items');
        }else{
            $creditNote = new CustomerCreditNote();
            $creditNote->fill([
                'credit_note_prefix' => $creditNote->next_credit_note_prefix,
                'credit_note_suffix' => $creditNote->next_credit_note_suffix,
                'customer_id' => $request->customer_id,
                'invoice_id' => $request->invoice_id,
                'record_date' => $request->date,
                'memo' => strtoupper($request->memo),
                'company_id' => auth()->user()->company_id
            ]);
            if($creditNote->save()){
                $this->addMarutiDirectItems($request, $creditNote);
                $creditNote->updateAmounts();
                return redirect('/customercreditnotes')->with('Success', 'Created successfully');
            }else{
                return back()->with('Error', 'Failed to create');
            }
        }
    }

    private function addMarutiDirectItems(Request $request, CustomerCreditNote $creditNote)
    {
        for ($i = 0; $i < count($request->maruti_direct_item_id); $i++) {
            $creditNoteItem = new CustomerCreditNoteItem();
            
            if ($request->maruti_direct_inventory_type[$i] == "Powder") {
                $creditNoteItem->fill([
                    'customer_credit_note_id' => $creditNote->id,
                    'powder_id' => $request->maruti_direct_item_id[$i],
                    'uom' => strtoupper($request->maruti_direct_uom[$i] ?? 'KG'),
                    'unit_price' => $request->maruti_direct_unit_price[$i] ?? 1,
                    'quantity' => $request->maruti_direct_item_kg[$i] ?? 1,
                    'vat' => $request->maruti_direct_unit_vat[$i] ?? Tax::where('type', TaxTypesEnum::VAT)->first()->percentage,
                    'vat_inclusive' => ($request->maruti_direct_vat_inclusive[$i] == 'Yes') ? 1 : 0,
                ]);
            } else {
                $creditNoteItem->fill([
                    'customer_credit_note_id' => $creditNote->id,
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
     * @param  \App\Models\CustomerCreditNote  $customerCreditNote
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCreditNote $customercreditnote)
    {
        return view('system.customer-creditnotes.doc', [
            'creditNote' => $customercreditnote
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerCreditNote  $customerCreditNote
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerCreditNote $customerCreditNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerCreditNote  $customerCreditNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerCreditNote $customerCreditNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerCreditNote  $customerCreditNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerCreditNote $customercreditnote)
    {
        $customercreditnote->cancelled_at = Carbon::now();
        if($customercreditnote->update()){
            return back()->with('Success', 'Cancelled');
        }else{
            return back()->with('Error', 'Failed');
        }
    }
}
