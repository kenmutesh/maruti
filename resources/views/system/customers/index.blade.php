@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Customers',
'datatable' => true,
]
)
<style>
  .modal-backdrop.show {
    z-index: 4;
  }

  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/suppliers'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">

            <div class="row">
              <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-3" data-toggle="modal" data-target="#createCustomerForm">
                <i class="tim-icons icon-simple-add"></i> CREATE A CUSTOMER
              </button>

              <!-- Modal -->
              <div class="modal fade" id="createCustomerForm" tabindex="-1" role="dialog" aria-labelledby="createCustomerForm" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Create A Customer in the System</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="tim-icons icon-simple-remove"></i>
                      </button>
                    </div>
                    <div class="modal-body">

                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('customers.store') }}">
                        @csrf
                        <div class="d-flex flex-column">

                          <div class="row">
                            <div class="col">
                              <div class="form-group">
                                <label for="customerName">Customer Name</label>
                                <input type="text" required name="customer_name" class="form-control" id="customerName" aria-describedby="customerName" class="dark-text" placeholder="Enter customer name">
                              </div>
                            </div>

                            <div class="col">
                              <div class="form-group">
                                <label for="kra">KRA Pin</label>
                                <input type="text" name="kra_pin" class="form-control" id="kra" aria-describedby="customerName" class="dark-text" placeholder="Enter kra pin">
                              </div>
                            </div>

                            <div class="col">
                              <div class="form-group">
                                <label for="creditLimit">Credit Limit</label>
                                <input type="number" step=".01" min="0" required name="credit_limit" class="form-control" id="creditLimit" aria-describedby="creditLimit" class="dark-text" placeholder="Enter credit limit">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col">
                              <div class="form-group">
                                <label for="contactNumber">Contact Number</label>
                                <input type="text" required name="contact_number" class="form-control" id="contactNumber" aria-describedby="contactNumber" class="dark-text" placeholder="Enter contact number">
                              </div>
                            </div>

                            <div class="col">
                              <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" required name="location" class="form-control" id="location" aria-describedby="location" class="dark-text" placeholder="Enter location">
                              </div>
                            </div>

                            <div class="col">
                              <div class="form-group">
                                <label for="company">Company</label>
                                <input type="text" required name="company" class="form-control" id="company" aria-describedby="company" class="dark-text" placeholder="Enter company location">
                              </div>
                            </div>

                            <div class="col-6">
                              <div class="form-group">
                                <label for="contactName">Contact Person Name</label>
                                <input type="text" name="contact_name" class="form-control" id="contactName" aria-describedby="contactName" class="dark-text" placeholder="Enter contact person name">
                              </div>
                            </div>

                            <div class="col-6">
                              <div class="form-group">
                                <label for="contactEmail">Contact Person Email</label>
                                <input type="email" name="contact_email" class="form-control" id="contactEmail" aria-describedby="contactEmail" class="dark-text" placeholder="Enter contact person email">
                              </div>
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="contactEmail">Opening Balance</label>
                              <input type="number" step=".01" min="0" name="opening_balance" class="form-control" class="dark-text" placeholder="Enter opening balance">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="contactEmail">CC Emails</label>
                              <input type="email" name="cc_emails[]" class="form-control mb-1" class="dark-text" placeholder="Enter cc person email">
                            </div>
                            <button class="btn btn-sm btn-success w-100" type="button" onclick="addCCEmailField(this)">+Add CC Email</button>
                          </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                      <button type="submit" name="submit_btn" value="Create Customer" class="btn btn-primary">CREATE CUSTOMER</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title m-0 p-0">Available customers</h4>
                </div>
                <div class="card-body p-0">
                  <div class="py-3 row m-0">
                    <div class="d-flex justify-content-around col-sm-6 p-0 location-list filters">
                      <div class="d-flex flex-column col-5 col-sm-6 p-0">
                        <span>Min. Date</span>
                        <input type="date" name="min" class="min border-dark form-control rounded-0" id="">
                      </div>
                      <div class="d-flex flex-column col-5 col-sm-6 p-0">
                        <span>Max. Date</span>
                        <input type="date" name="max" class="max border-dark form-control rounded-0" id="">
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive p-0">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="location-list">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Date Created
                          </th>
                          <th class="py-0 px-1 border">
                            Name
                          </th>
                          <th class="py-0 px-1 border">
                            Opening balance
                          </th>
                          <th class="py-0 px-1 border">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($customers as $customer)
                        <tr>
                          <td class="p-0">
                            {{ $customer->created_at }}
                          </td>
                          <td class="p-0">
                            {{ $customer->customer_name }}
                          </td>

                          <td class="p-0">
                            {{ number_format($customer->opening_balance,2) }}
                          </td>

                          <td class="p-0">
                            @can('update', App\Models\Customer::class)
                            <button type="button" name="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editCustomerForm{{ $customer->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Edit Customer">
                                <i class="material-icons">mode_edit</i>
                              </span>
                            </button>
                            @endcan
                            <!-- Modal -->
                            <div class="modal fade" id="editCustomerForm{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="editCustomerForm{{ $customer->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Customer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('customers.update', $customer->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="row">
                                          <div class="col">
                                            <div class="form-group">
                                              <label for="customerName">Customer Name</label>
                                              <input type="text" required name="customer_name" class="form-control" id="customerName" aria-describedby="customerName" class="dark-text" placeholder="Enter customer name" value="{{ $customer->customer_name }}">
                                            </div>
                                          </div>

                                          <div class="col">
                                            <div class="form-group">
                                              <label for="kra">KRA Pin</label>
                                              <input type="text" required name="kra_pin" class="form-control" id="kra" aria-describedby="customerName" class="dark-text" placeholder="Enter kra pin" value="{{ $customer->kra_pin }}">
                                            </div>
                                          </div>

                                          <div class="col">
                                            <div class="form-group">
                                              <label for="creditLimit">Credit Limit</label>
                                              <input type="text" required name="credit_limit" class="form-control" id="creditLimit" aria-describedby="creditLimit" class="dark-text" placeholder="Enter credit limit" value="{{ $customer->credit_limit }}">
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="contactNumber">Contact Number</label>
                                            <input type="text" required name="contact_number" class="form-control" id="contactNumber" aria-describedby="contactNumber" class="dark-text" placeholder="Enter contact number" value="{{ $customer->contact_number }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="location">Location</label>
                                            <input type="text" required name="location" class="form-control" id="location" aria-describedby="location" class="dark-text" placeholder="Enter location" value="{{ $customer->location }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="company">Company</label>
                                            <input type="text" required name="company" class="form-control" id="company" aria-describedby="company" class="dark-text" placeholder="Enter company location" value="{{ $customer->company }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="contactName">Contact Person Name</label>
                                            <input type="text" name="contact_name" class="form-control" id="contactName" aria-describedby="contactName" class="dark-text" placeholder="Enter contact person name" value="{{ $customer->contact_person_name }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="contactEmail">Contact Person Email</label>
                                            <input type="email" name="contact_email" class="form-control" id="contactEmail" aria-describedby="contactEmail" class="dark-text" placeholder="Enter contact person email" value="{{ $customer->contact_person_email }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="contactEmail">CC Emails</label>
                                            @foreach($customer->carboncopyemails as $email)
                                            <div class="row">
                                              <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                  <div class="input-group-text">
                                                    Remove
                                                    <input type="checkbox" name="cc_email_delete[]" value="{{ $email->id }}" class="ml-1">
                                                  </div>
                                                </div>
                                                <input type="email" name="cc_emails[]" class="col-10 form-control mb-1" class="dark-text" placeholder="Enter cc person email" value="{{ $email->email }}">
                                                <input type="hidden" name="cc_email_id[]" class="col-10 form-control mb-1" class="dark-text" placeholder="Enter cc person email" value="{{ $email->id }}">
                                              </div>
                                            </div>
                                            @endforeach
                                            <input type="email" name="new_cc_email[]" class="form-control mb-1" class="dark-text" placeholder="Enter cc person email">
                                          </div>
                                          <button class="btn btn-sm btn-success w-100" type="button" onclick="addCCEmailField(this)">+Add CC Email</button>
                                        </div>

                                      </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Edit Customer" class="btn btn-primary">SUBMIT EDITS</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
                            @can('delete', App\Models\Customer::class)
                            <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCustomerForm{{ $customer->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete Customer">
                                <i class="material-icons">delete</i>
                              </span>
                            </button>
                            @endcan
                            <!-- Modal -->
                            <div class="modal fade" id="deleteCustomerForm{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="editCustomerForm{{ $customer->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Customer: {{ $customer->customer_name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('customers.destroy', $customer->id ) }}">
                                      @csrf
                                      @method("DELETE")
                                      <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this customer?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Customer" class="btn btn-danger">YES</button>
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
  <script>
    function addCCEmailField(btnElement) {
      const clone = btnElement.previousElementSibling.querySelector('input').cloneNode(true);
      clone.value = '';
      btnElement.previousElementSibling.append(clone);
    }

    function removeCCEmailField(btnElement) {
      btnElement.parentElement.remove();
    }
  </script>
  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' => true,

  ]
  )
  @include('universal-layout.alert')
  <script>
    const dataTableInstances = [];
    const filterDates = document.querySelectorAll('.filters');

    $.fn.dataTable.ext.search.push(
      function(settings, searchData, index, rowData, counter) {
        let filtersClass = settings.nTable.getAttribute('data-table');

        if (!filtersClass) {
          return true;
        }

        let minimumInput = document.querySelector(`.${filtersClass} .min`);

        let maximumInput = document.querySelector(`.${filtersClass} .max`);

        var min = new Date(minimumInput.value);
        var max = new Date(maximumInput.value);
        var date = new Date(searchData[0]) || 0;

        if ((isNaN(min) && isNaN(max)) ||
          (isNaN(min) && date <= max) ||
          (min <= date && isNaN(max)) ||
          (min <= date && age <= max)) {
          return true;
        }
        return false;
      }
    );

    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip(); //for tooltip functionality

      $('.data-table').each((index, element) => {
        const table = $(element).DataTable({
          paging: false,
          aaSorting: [],
        });

        dataTableInstances.push(table);
      })

    });

    filterDates.forEach((element, index) => {

      element.querySelector('.min').addEventListener('change', (e) => {
        dataTableInstances[index].draw();
      })

      element.querySelector('.max').addEventListener('change', (e) => {
        dataTableInstances[index].draw();
      })


    })
  </script>
  @include('universal-layout.footer')