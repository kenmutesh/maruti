<?php

namespace App\Http\Controllers;

use App\Models\NonInventoryItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class NonInventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', NonInventoryItem::class);
        $nonInventoryItems = NonInventoryItem::orderBy('id', 'desc')->with(['supplier'])->get();
        if ($request->is('api/*')) {
            return $nonInventoryItems;
        } else {
            $suppliers = Supplier::all();
            return view('system.noninventory.index', [
                'nonInventoryItems' => $nonInventoryItems,
                'suppliers' => $suppliers
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'item_name' => ['required'],
            'standard_cost' => ['required'],
            'supplier_id' => ['required']
        ]);
        $nonInventoryItem = new NonInventoryItem();

        $nonInventoryItem->fill([
            'item_name' => strtoupper($request->item_name),
            'standard_cost' => $request->standard_cost,
            'standard_cost_vat' => $request->vat ?? 0,
            'supplier_id' => $request->supplier_id,
            'company_id' => auth()->user()->company_id
        ]);

        if($nonInventoryItem->save()){
            return back()->with('Success','Created successfully');
        }else{
            return back()->with('Error','Failed please retry');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NonInventoryItem  $nonInventoryItem
     * @return \Illuminate\Http\Response
     */
    public function show(NonInventoryItem $nonInventoryItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NonInventoryItem  $nonInventoryItem
     * @return \Illuminate\Http\Response
     */
    public function edit(NonInventoryItem $nonInventoryItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NonInventoryItem  $nonInventoryItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NonInventoryItem $noninventoryitem)
    {
        $request->validate([
            'item_name' => ['required'],
            'standard_cost' => ['required'],
            'supplier_id' => ['required']
        ]);

        $noninventoryitem->fill([
            'item_name' => strtoupper($request->item_name),
            'standard_cost' => $request->standard_cost,
            'standard_cost_vat' => $request->vat ?? 0,
            'supplier_id' => $request->supplier_id,
            'company_id' => auth()->user()->company_id
        ]);

        if($noninventoryitem->update()){
            return back()->with('Success','Edited successfully');
        }else{
            return back()->with('Error','Failed to edit retry');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NonInventoryItem  $nonInventoryItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(NonInventoryItem $noninventoryitem)
    {
        if($noninventoryitem->delete()){
            return back()->with('Success','Deleted successfully');
        }else{
            return back()->with('Error','Failed to delete, please retry');
        }
    }
}
