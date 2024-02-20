<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('viewAny', Location::class);
    $locations = Location::orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $locations;
    } else {
      return view('system.locations.index', [
        'locations' => $locations,
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
      'location_name' => ['required'],
      'location_description' => ['required'],
    ]);

    $location = new Location();

    $location->fill([
      'location_name' => strtoupper($request->location_name),
      'location_description' => strtoupper($request->location_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($location->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Location  $location
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Location $location)
  {
    $this->authorize('update', Location::class);
    $request->validate([
      'location_name' => ['required'],
      'location_description' => ['required'],
    ]);

    $location->fill([
      'location_name' => strtoupper($request->location_name),
      'location_description' => strtoupper($request->location_description),
      'company_id' => auth()->user()->company_id
    ]);

    if ($location->update()) {
      return back()->with('Success', 'Updated successfully');
    } else {
      return back()->with('Error', 'Failed to update. Please retry.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Location  $location
   * @return \Illuminate\Http\Response
   */
  public function destroy(Location $location)
  {
    $this->authorize('delete', Location::class);
    if ($location->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry.');
    }
  }
}
