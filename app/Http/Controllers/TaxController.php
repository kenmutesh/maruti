<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $taxes = Tax::all();
    $this->authorize('viewAny', Tax::class);
    if ($request->is('api/*')) {
      return $taxes;
    } else {
      return view('system.tax.index', [
        'taxes' => $taxes,
      ]);
    }
  }

  public function update(Request $request, Tax $tax)
  {
    $this->authorize('update', Tax::class);
    $request->validate([
      'id' => ['required'],
      'percentage' => ['required'],
    ]);

    $tax->percentage = $request->percentage;

    if ($tax->update()) {
      if ($request->is('api/*')) {
        return $tax;
      } else {
        return back()->with('Success', 'Label has been edited successfully');
      }
    } else {
      return back()->with('Error', 'Failed to edit label. Please retry');
    }
  }
}
