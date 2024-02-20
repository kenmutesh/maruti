<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

use App\Models\Warehouse;

class FloorController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('viewAny', Floor::class);
    $floors = Floor::orderBy('id', 'desc')->with(['warehouse'])->get();
    if ($request->is('api/*')) {
      return $floors;
    } else {
      $warehouses = Warehouse::all();
      return view('system.floors.index', [
        'warehouses' => $warehouses,
        'floors' => $floors
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
    $this->authorize('create', Floor::class);
    $request->validate([
      'floor_name' => ['required'],
      'warehouse_id' => ['required'],
    ]);

    $floor = new Floor();

    $floor->fill([
      'floor_name' => strtoupper($request->floor_name),
      'warehouse_id' => $request->warehouse_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($floor->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Floor  $floor
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Floor $floor)
  {
    $this->authorize('update', Floor::class);
    $request->validate([
      'floor_name' => ['required'],
      'warehouse_id' => ['required'],
    ]);

    $floor->fill([
      'floor_name' => strtoupper($request->floor_name),
      'warehouse_id' => $request->warehouse_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($floor->update()) {
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Floor  $floor
   * @return \Illuminate\Http\Response
   */
  public function destroy(Floor $floor)
  {
    $this->authorize('delete', Floor::class);
    if ($floor->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry.');
    }
  }
}
