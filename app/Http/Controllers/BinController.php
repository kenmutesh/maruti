<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use Illuminate\Http\Request;

use App\Models\Shelf;

class BinController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('viewAny', Bin::class);
    $bins = Bin::orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $bins;
    } else {
      $shelves = Shelf::all();
      return view('system.bins.index', [
        'shelves' => $shelves,
        'bins' => $bins,
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
    $this->authorize('create', Bin::class);
    $request->validate([
      'shelf_id' => ['required'],
      'bin_name' => ['required'],
      'bin_description' => ['required'],
    ]);

    $bin = new Bin();

    $bin->fill([
      'shelf_id' => $request->shelf_id,
      'bin_name' => strtoupper($request->bin_name),
      'bin_description' => strtoupper($request->bin_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($bin->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please try again.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Bin  $bin
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Bin $bin)
  {
    $this->authorize('update', Bin::class);
    $request->validate([
      'shelf_id' => ['required'],
      'bin_name' => ['required'],
      'bin_description' => ['required'],
    ]);

    $bin->fill([
      'shelf_id' => $request->shelf_id,
      'bin_name' => strtoupper($request->bin_name),
      'bin_description' => strtoupper($request->bin_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($bin->update()) {
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Bin  $bin
   * @return \Illuminate\Http\Response
   */
  public function destroy(Bin $bin)
  {
    $this->authorize('destroy', Bin::class);
    if ($bin->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to deleted. Please retry.');
    }
  }
}
