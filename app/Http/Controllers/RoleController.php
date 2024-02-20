<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

  public function index(Request $request)
  {
    $this->authorize('viewAny', Role::class);
    $roles = Role::where('name', '<>', 'ADMIN')->orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $roles;
    } else {
      return view('system.roles.index', [
        'roles' => $roles
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
    $this->authorize('create', Role::class);
    $request->validate([
      'role_name' => ['required'],
    ]);

    if(strtolower($request->role_name) == 'admin'){
      return back()->with('Error', 'Cannot use name provided');
    }

    $privileges['coatingjob']['view'] = (isset($request->powder_coating_view)) ? true : false ;
    $privileges['coatingjob']['create'] = (isset($request->powder_coating_create)) ? true : false ;
    $privileges['coatingjob']['update'] = (isset($request->powder_coating_update)) ? true : false ;

    $privileges['location']['view'] = (isset($request->sections_location)) ? true : false ;
    $privileges['location']['create'] = (isset($request->sections_location)) ? true : false ;
    $privileges['location']['update'] = (isset($request->sections_location)) ? true : false ;

    $privileges['warehouse']['view'] = (isset($request->sections_warehouse)) ? true : false ;
    $privileges['warehouse']['create'] = (isset($request->sections_warehouse)) ? true : false ;
    $privileges['warehouse']['update'] = (isset($request->sections_warehouse)) ? true : false ;

    $privileges['floor']['view'] = (isset($request->sections_floor)) ? true : false ;
    $privileges['floor']['create'] = (isset($request->sections_floor)) ? true : false ;
    $privileges['floor']['update'] = (isset($request->sections_floor)) ? true : false ;

    $privileges['shelf']['view'] = (isset($request->sections_shelf)) ? true : false ;
    $privileges['shelf']['create'] = (isset($request->sections_shelf)) ? true : false ;
    $privileges['shelf']['update'] = (isset($request->sections_shelf)) ? true : false ;

    $privileges['bin']['view'] = (isset($request->sections_bin)) ? true : false ;
    $privileges['bin']['create'] = (isset($request->sections_bin)) ? true : false ;
    $privileges['bin']['update'] = (isset($request->sections_bin)) ? true : false ;

    $privileges['supplier']['view'] = (isset($request->suppliers)) ? true : false ;
    $privileges['supplier']['create'] = (isset($request->suppliers)) ? true : false ;
    $privileges['supplier']['update'] = (isset($request->suppliers)) ? true : false ;

    $privileges['customer']['view'] = (isset($request->customer)) ? true : false ;
    $privileges['customer']['create'] = (isset($request->customer)) ? true : false ;
    $privileges['customer']['update'] = (isset($request->customer)) ? true : false ;

    $privileges['purchaseorder']['view'] = (isset($request->purchase_order_view)) ? true : false ;
    $privileges['purchaseorder']['create'] = (isset($request->purchase_order_create)) ? true : false ;
    $privileges['purchaseorder']['update'] = (isset($request->purchase_order_update)) ? true : false ;

    $privileges['invoice']['view'] = (isset($request->invoices)) ? true : false ;
    $privileges['invoice']['create'] = (isset($request->invoices)) ? true : false ;
    $privileges['invoice']['update'] = (isset($request->invoices)) ? true : false ;

    $privileges['cashsale']['view'] = (isset($request->cashsales)) ? true : false ;
    $privileges['cashsale']['create'] = (isset($request->cashsales)) ? true : false ;
    $privileges['cashsale']['update'] = (isset($request->cashsales)) ? true : false ;

    $privileges['noninventoryitem']['view'] = (isset($request->non_inventory)) ? true : false ;
    $privileges['noninventoryitem']['create'] = (isset($request->non_inventory)) ? true : false ;
    $privileges['noninventoryitem']['update'] = (isset($request->non_inventory)) ? true : false ;

    $privileges['inventoryitem']['view'] = (isset($request->inventory)) ? true : false ;
    $privileges['inventoryitem']['create'] = (isset($request->inventory)) ? true : false ;
    $privileges['inventoryitem']['update'] = (isset($request->inventory)) ? true : false ;

    $privileges['powder']['view'] = (isset($request->powder)) ? true : false ;
    $privileges['powder']['create'] = (isset($request->powder)) ? true : false ;
    $privileges['powder']['update'] = (isset($request->powder)) ? true : false ;

    $role = new Role();

    $role->fill([
      'name' => strtoupper($request->role_name),
      'privileges' => json_encode($privileges),
      'created_by' => auth()->user()->id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($role->save()) {
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please try again.');
    }
  }

  public function update(Request $request, Role $role)
  {
    $this->authorize('update', Role::class);
    $request->validate([
      'role_name' => ['required'],
    ]);

    if(strtolower($request->role_name) == 'admin'){
      return back()->with('Error', 'Cannot use name provided');
    }

    $privileges['coatingjob']['view'] = (isset($request->powder_coating_view)) ? true : false ;
    $privileges['coatingjob']['create'] = (isset($request->powder_coating_create)) ? true : false ;
    $privileges['coatingjob']['update'] = (isset($request->powder_coating_update)) ? true : false ;

    $privileges['location']['view'] = (isset($request->sections_location)) ? true : false ;
    $privileges['location']['create'] = (isset($request->sections_location)) ? true : false ;
    $privileges['location']['update'] = (isset($request->sections_location)) ? true : false ;

    $privileges['warehouse']['view'] = (isset($request->sections_warehouse)) ? true : false ;
    $privileges['warehouse']['create'] = (isset($request->sections_warehouse)) ? true : false ;
    $privileges['warehouse']['update'] = (isset($request->sections_warehouse)) ? true : false ;

    $privileges['floor']['view'] = (isset($request->sections_floor)) ? true : false ;
    $privileges['floor']['create'] = (isset($request->sections_floor)) ? true : false ;
    $privileges['floor']['update'] = (isset($request->sections_floor)) ? true : false ;

    $privileges['shelf']['view'] = (isset($request->sections_shelf)) ? true : false ;
    $privileges['shelf']['create'] = (isset($request->sections_shelf)) ? true : false ;
    $privileges['shelf']['update'] = (isset($request->sections_shelf)) ? true : false ;

    $privileges['bin']['view'] = (isset($request->sections_bin)) ? true : false ;
    $privileges['bin']['create'] = (isset($request->sections_bin)) ? true : false ;
    $privileges['bin']['update'] = (isset($request->sections_bin)) ? true : false ;

    $privileges['supplier']['view'] = (isset($request->suppliers)) ? true : false ;
    $privileges['supplier']['create'] = (isset($request->suppliers)) ? true : false ;
    $privileges['supplier']['update'] = (isset($request->suppliers)) ? true : false ;

    $privileges['customer']['view'] = (isset($request->customer)) ? true : false ;
    $privileges['customer']['create'] = (isset($request->customer)) ? true : false ;
    $privileges['customer']['update'] = (isset($request->customer)) ? true : false ;

    $privileges['purchaseorder']['view'] = (isset($request->purchase_order_view)) ? true : false ;
    $privileges['purchaseorder']['create'] = (isset($request->purchase_order_create)) ? true : false ;
    $privileges['purchaseorder']['update'] = (isset($request->purchase_order_update)) ? true : false ;

    $privileges['invoice']['view'] = (isset($request->invoices)) ? true : false ;
    $privileges['invoice']['create'] = (isset($request->invoices)) ? true : false ;
    $privileges['invoice']['update'] = (isset($request->invoices)) ? true : false ;

    $privileges['cashsale']['view'] = (isset($request->cashsales)) ? true : false ;
    $privileges['cashsale']['create'] = (isset($request->cashsales)) ? true : false ;
    $privileges['cashsale']['update'] = (isset($request->cashsales)) ? true : false ;

    $privileges['noninventoryitem']['view'] = (isset($request->non_inventory)) ? true : false ;
    $privileges['noninventoryitem']['create'] = (isset($request->non_inventory)) ? true : false ;
    $privileges['noninventoryitem']['update'] = (isset($request->non_inventory)) ? true : false ;

    $privileges['inventoryitem']['view'] = (isset($request->inventory)) ? true : false ;
    $privileges['inventoryitem']['create'] = (isset($request->inventory)) ? true : false ;
    $privileges['inventoryitem']['update'] = (isset($request->inventory)) ? true : false ;

    $privileges['powder']['view'] = (isset($request->powder)) ? true : false ;
    $privileges['powder']['create'] = (isset($request->powder)) ? true : false ;
    $privileges['powder']['update'] = (isset($request->powder)) ? true : false ;

    $role->fill([
      'name' => strtoupper($request->role_name),
      'privileges' => json_encode($privileges),
      'created_by' => auth()->user()->id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($role->update()) {
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry.');
    }
  }

  public function destroy(Role $role)
  {
    $this->authorize('delete', Role::class);
    if(count($role->users)>0){
      return back()->with('Error', 'Failed to deleted. This privilege is being used.');
    }
    if ($role->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to deleted. Please retry.');
    }
  }
}
