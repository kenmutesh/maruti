<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use Illuminate\Http\Request;

use App\Models\Floor;

class ShelfController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('create', Shelf::class);
    $shelves = Shelf::orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $shelves;
    } else {
      $floors = Floor::all();
      return view('system.shelves.index', [
        'shelves' => $shelves,
        'floors' => $floors,
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
    $this->authorize('create', Shelf::class);
    $request->validate([
      'shelf_name' => ['required'],
      'floor_id' => ['required'],
    ]);

    $shelf = new Shelf();

    $shelf->fill([
      'shelf_name' => strtoupper($request->shelf_name),
      'floor_id' => $request->floor_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($shelf->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Shelf  $shelf
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Shelf $shelf)
  {
    $this->authorize('update', Shelf::class);
    $request->validate([
      'shelf_name' => ['required'],
      'floor_id' => ['required'],
    ]);

    $shelf->fill([
      'shelf_name' => strtoupper($request->shelf_name),
      'floor_id' => $request->floor_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($shelf->update()) {
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Shelf  $shelf
   * @return \Illuminate\Http\Response
   */
  public function destroy(Shelf $shelf)
  {
    $this->authorize('delete', Shelf::class);

    if ($shelf->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to deleted. Please retry.');
    }
  }
}
