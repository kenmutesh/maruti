@include('universal-layout.header', ['pageTitle' => 'Aprotec | Role'])
<style>
  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
    white-space: inherit !important;
  }

  .modal-backdrop.show {
    z-index: 4;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/settings'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">

            <div class="row">
              <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25" data-toggle="modal" data-target="#createRoleForm">
                <i class="tim-icons icon-simple-add"></i> CREATE A ROLE
              </button>

              <!-- Modal -->
              <div class="modal fade" id="createRoleForm" tabindex="-1" role="dialog" aria-labelledby="createCompanyForm" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Create A Role in the System</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="tim-icons icon-simple-remove"></i>
                      </button>
                    </div>
                    <div class="modal-body">

                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('roles.store') }}">
                        @csrf
                        <div class="d-flex flex-column">

                          <div class="col">
                            <div class="form-group">
                              <label for="roleName">Role Name</label>
                              <input type="text" required name="role_name" class="form-control" id="roleName" aria-describedby="roleName" class="dark-text" placeholder="Enter role name">
                            </div>
                          </div>

                          <div class="col">
                            <label for="locationDescription">Privilege Set</label>
                            <div class="row border-bottom">

                              <div class="col-sm-6 border-right">
                                <p class="m-0 font-weight-bold">Powder Coating</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="powder_coating_view">
                                      View
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="powder_coating_create">
                                      Create
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="powder_coating_update">
                                      Update
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="col-sm-6">
                                <p class="m-0 font-weight-bold">Sections</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="sections_location">
                                      Locations
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="sections_warehouse">
                                      Warehouses
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="sections_floor">
                                      Floors
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="sections_shelf">
                                      Shelves
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="sections_bin">
                                      Bins
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>

                            </div>

                            <div class="row border-bottom">

                              <div class="col-sm-6 border-right">
                                <p class="m-0 font-weight-bold">Suppliers</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="suppliers">
                                      Suppliers
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="col-sm-6">
                                <p class="m-0 font-weight-bold">Customers</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="customers">
                                      Customers
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="row border-bottom">

                              <div class="col-sm-6 border-right">
                                <p class="m-0 font-weight-bold">Purchase Orders</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="purchase_order_view">
                                      View
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="purchase_order_create">
                                      Create
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="purchase_order_update">
                                      Update
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>


                              <div class="col-sm-6">
                                <p class="m-0 font-weight-bold">Invoices</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="invoices">
                                      Invoices
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row border-bottom">

                              <div class="col-sm-6 border-right">
                                <p class="m-0 font-weight-bold">Cash Sales</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="cashsales">
                                      Cash sales
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="col-sm-6">
                                <p class="m-0 font-weight-bold">Inventory</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="non_inventory">
                                      Non Inventory
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="inventory">
                                      Inventory
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="powder">
                                      Powder
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                              </div>

                            </div>

                          </div>

                        </div>

                    </div>
                    <div class="modal-footer d-flex">
                      <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">CLOSE</button>
                      <button type="submit" name="submit_btn" value="Create Role" class="btn col-6 btn-success">CREATE ROLE</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title p-0 m-0">Available roles</h4>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 data-table" id="">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Role Name - Privileges
                          </th>
                          <th class="py-0 px-1 border">
                            Date Created
                          </th>
                          <th class="py-0 px-1 border">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($roles as $singleRole)
                        <tr>
                          <td class="p-0">
                            {{ $singleRole->name }}

                          </td>

                          <td class="p-0">
                            {{ $singleRole->created_at }}
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editRoleForm{{ $singleRole->id }}">
                              EDIT ROLE
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" style="z-index: 5;" id="editRoleForm{{ $singleRole->id }}" tabindex="-1" role="dialog" aria-labelledby="editRoleForm" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Role</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" class="edit-role" method="POST" autocomplete="off" action="{{ route('roles.update', $singleRole->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <div class="d-flex flex-column">

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="roleName">Role Name</label>
                                            <input type="text" required name="role_name" class="form-control" id="roleName" aria-describedby="roleName" class="dark-text" placeholder="Enter role name" value="{{ $singleRole->name }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <label for="locationDescription">Privilege Set</label>
                                          @if($singleRole->name != 'ADMIN')
                                          
                                          <div class="row border-bottom">
                                            <div class="col-sm-6 border-right">
                                              <p class="m-0 font-weight-bold">Powder Coating</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" 
                                                      <?php echo  $checkedStatus = ($singleRole->decoded_privileges->coatingjob->view) ? 'checked' : '' ; ?>
                                                      type="checkbox" name="powder_coating_view">
                                                    View
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->coatingjob->create) ? 'checked' : '' ; ?> type="checkbox" name="powder_coating_create">
                                                    Create
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->coatingjob->update) ? 'checked' : '' ; ?> type="checkbox" name="powder_coating_update">
                                                    Update
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-sm-6">
                                              <p class="m-0 font-weight-bold">Sections</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->location->view) ? 'checked' : '' ; ?> type="checkbox" name="sections_location">
                                                    Locations
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->warehouse->view) ? 'checked' : '' ; ?> type="checkbox" name="sections_warehouse">
                                                    Warehouses
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->floor->view) ? 'checked' : '' ; ?> type="checkbox" name="sections_floor">
                                                    Floors
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->shelf->view) ? 'checked' : '' ; ?> type="checkbox" name="sections_shelf">
                                                    Shelves
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->bin->view) ? 'checked' : '' ; ?> type="checkbox" name="sections_bin">
                                                    Bins
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>

                                          </div>

                                          <div class="row border-bottom">

                                            <div class="col-sm-6 border-right">
                                              <p class="m-0 font-weight-bold">Suppliers</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->supplier->view) ? 'checked' : '' ; ?> type="checkbox" name="suppliers">
                                                    Suppliers
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-sm-6">
                                              <p class="m-0 font-weight-bold">Customers</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->customer->view) ? 'checked' : '' ; ?> type="checkbox" name="customers">
                                                    Customers
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div class="row border-bottom">

                                            <div class="col-sm-6 border-right">
                                              <p class="m-0 font-weight-bold">Purchase Orders</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->purchaseorder->view) ? 'checked' : '' ; ?> type="checkbox" name="purchase_order_view">
                                                    View
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->purchaseorder->create) ? 'checked' : '' ; ?> type="checkbox" name="purchase_order_create">
                                                    Create
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->purchaseorder->update) ? 'checked' : '' ; ?> type="checkbox" name="purchase_order_update">
                                                    Update
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>


                                            <div class="col-sm-6">
                                              <p class="m-0 font-weight-bold">Invoices</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->invoice->view) ? 'checked' : '' ; ?> type="checkbox" name="invoices">
                                                    Invoices
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          
                                          <div class="row border-bottom">

                                            <div class="col-sm-6 border-right">
                                              <p class="m-0 font-weight-bold">Cash Sales</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->cashsale->view) ? 'checked' : '' ; ?> type="checkbox" name="cashsales">
                                                    Cash sales
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-sm-6">
                                              <p class="m-0 font-weight-bold">Inventory</p>
                                              <div class="form-group">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->noninventoryitem->view) ? 'checked' : '' ; ?> type="checkbox" name="non_inventory">
                                                    Non Inventory
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->inventoryitem->view) ? 'checked' : '' ; ?> type="checkbox" name="inventory">
                                                    Inventory
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input class="form-check-input" <?php echo  $checkedStatus = ($singleRole->decoded_privileges->powder->view) ? 'checked' : '' ; ?> type="checkbox" name="powder">
                                                    Powder
                                                    <span class="form-check-sign">
                                                      <span class="check"></span>
                                                    </span>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>

                                          </div>
                                          @else
                                          <p>
                                            Admins have full access
                                          </p>
                                          @endif

                                        </div>

                                      </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Edit Role" class="btn btn-success col-6">EDIT ROLE</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->

                            <button type="button" name="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteRoleForm{{ $singleRole->id }}">
                              DELETE ROLE
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="deleteRoleForm{{ $singleRole->id }}" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Role: {{ $singleRole->name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('roles.destroy', $singleRole->id ) }}">
                                      @csrf
                                      @method("DELETE")

                                      <p class="text-center">
                                        Are you sure you want to delete this role?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Role" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,

  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')