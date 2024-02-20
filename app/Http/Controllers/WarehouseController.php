<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('viewAny', Warehouse::class);
    $warehouses = Warehouse::orderBy('id', 'desc')->with(['location'])->get();
    if ($request->is('api/*')) {
      return $warehouses;
    } else {
      $locations = Location::all();
      return view('system.warehouses.index', [
        'warehouses' => $warehouses,
        'locations' => $locations
      ]);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->authorize('create', Location::class);
    $request->validate([
      'location_id' => ['required'],
      'warehouse_name' => ['required'],
      'warehouse_description' => ['required'],
    ]);

    $warehouse = new Warehouse();

    $warehouse->fill([
      'location_id' => strtoupper($request->location_id),
      'warehouse_name' => strtoupper($request->warehouse_name),
      'warehouse_description' => strtoupper($request->warehouse_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($warehouse->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Warehouse  $warehouse
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Warehouse $warehouse)
  {
    $this->authorize('update', Location::class);
    $request->validate([
      'location_id' => ['required'],
      'warehouse_name' => ['required'],
      'warehouse_description' => ['required'],
    ]);

    $warehouse->fill([
      'location_id' => strtoupper($request->location_id),
      'warehouse_name' => strtoupper($request->warehouse_name),
      'warehouse_description' => strtoupper($request->warehouse_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($warehouse->update()) {
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Warehouse  $warehouse
   * @return \Illuminate\Http\Response
   */
  public function destroy(Warehouse $warehouse)
  {
    $this->authorize('delete', Location::class);
    if ($warehouse->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry.');
    }
  }
}
