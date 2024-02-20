@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Accounts Customers',
'datatable' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }
</style>

<body class="theme-blue">
  @include('universal-layout.spinner')

  @include('universal-layout.accounts-sidemenu',
  [
  'slug' => '/accounts'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">

        <div class="content">

          <div class="row">
            <a href="/payments/create" class="btn btn-default btn-sm d-flex align-items-center container justify-content-center mb-3 ml-3 w-25">
              <i class="tim-icons icon-simple-add"></i> CREATE A PAYMENT
            </a>
          </div>


          <div class="col">
            <div class="card card-plain">
              <div class="card-body p-0">
                <div class="py-3 row m-0">
                  <div class="d-flex justify-content-around col-sm-6 p-0 payment-list filters">
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
                  <table class="table table-bordered sorter col-12 p-0 data-table" data-table="payment-list" id="documentFrame">
                    <thead class="text-primary p-0 m-0">
                      <tr class="p-0">
                        <th class="p-0 m-0">
                          Payment Date
                        </th>
                        <th class="p-0 m-0" style="width:100px;overflow:hidden;">
                          Customer
                        </th>
                        <th class="p-0 m-0">
                          Type
                        </th>
                        <th class="p-0 m-0">
                          Amount
                        </th>
                        <th class="p-0 m-0">
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($payments as $payment)
                      <tr class="p-0">
                        <td class="p-0 m-0">
                          {{ date('d/m/Y' ,strtotime($payment->payment_date)) }}
                        </td>
                        <td class="p-0 m-0">
                          <span class="d-block overflow-hidden" style="width:100px;text-overflow:ellipsis;">
                            {{ $payment->customer->customer_name }}
                          </span>
                        </td>
                        <td class="p-0 m-0">
                          {{ $payment->payment_mode->humanreadablestring() }}
                        </td>
                        <td class="p-0 m-0">
                          {{ number_format($payment->sum_invoice_payments, 2) }}
                        </td>

                        <td class="p-0 m-0">
                          @if($payment->nullified_at)
                          <span class="badge badge-danger">Nullified</span>
                          @else
                          <a href="/payments/{{ $payment->id }}/edit" class="btn btn-sm btn-primary">
                            Edit
                          </a>

                          <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" onclick="loadNullifyForm('{{ $payment->id }}')" data-target="#nullifyForm">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="NULLIFY">
                              Nullify
                            </span>
                          </button>

                          <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" onclick="loadDeleteForm('{{ $payment->id }}')" data-target="#deleteForm">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="DELETE">
                              Delete
                            </span>
                          </button>

                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <div class="modal fade" id="nullifyForm" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Cancel</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="tim-icons icon-simple-remove"></i>
                          </button>
                        </div>
                        <div class="modal-body">
                          <span class="spinner-border d-block mx-auto"></span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="modal fade" id="deleteForm" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="tim-icons icon-simple-remove"></i>
                          </button>
                        </div>
                        <div class="modal-body">
                          <span class="spinner-border d-block mx-auto"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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

    async function loadNullifyForm(paymentID){
      const formSection = document.querySelector('#nullifyForm .modal-body');
      const request = await fetch(`/payments/nullify-form/${paymentID}`);

      const response = await request.text();

      if(request.ok){
        formSection.innerHTML = response;
      }else{
        formSection.innerText = 'Sorry, an error occured loading information';
      }
    }

    async function loadDeleteForm(paymentID){
      const formSection = document.querySelector('#deleteForm .modal-body');
      const request = await fetch(`/payments/delete-form/${paymentID}`);

      const response = await request.text();

      if(request.ok){
        formSection.innerHTML = response;
      }else{
        formSection.innerText = 'Sorry, an error occured loading information';
      }
    }
  </script>
  @include('universal-layout.footer')