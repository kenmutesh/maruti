
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Create Credit Note',
'select2bs4' => true,
'bootstrapselect' => true,
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
  .select2-container--default .select2-results__option[aria-disabled=true] {
    display: none;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/sales',
  'select2bs4' => true,
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <form class="" onsubmit="showSpinner(event)" action="{{ route('submit_note') }}" method="post" autocomplete="off" enctype="multipart/form-data">
          @csrf
          <div class="row">

            <div class="col-sm-6">
              <div class="form-group">
                <label for="supplier">Customer</label>
                <select class="form-control searchable-select ms search-select customer-select" required name="customer_id" onchange="filterInvoices(this)">
                  <option disabled selected>Choose a customer</option>
                  @foreach($customers as $singleCustomer)
                    <option value="{{ $singleCustomer->id }}">
                      {{ $singleCustomer->company }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="supplier">Choose invoice</label>
                <select class="form-control ms search-select" required name="invoice_id" data-live-search="true" data-style="text-white">
                  <option disabled selected>Choose an invoice</option>
                  @foreach($invoices as $singleInvoice)
                    <option value="{{ $singleInvoice->id }}" class="invoice-item" attr-customer-id="{{ $singleInvoice->customer_id }}">
                      {{ $singleInvoice->invoice_prefix }}{{ $singleInvoice->invoice_suffix }} - (KES {{ $singleInvoice->balance }}) - {{ $singleInvoice->customer->company }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>

          <button type="submit" name="submit_btn" value="Create Coating Job" class="btn btn-success w-100 text-white">CREATE CREDIT NOTE</button>


        </form>


      </div>

      <script type="text/javascript">
        function filterInvoices(selectElement) {
          const customerID = selectElement.value;

          const invoiceList = document.querySelectorAll('.invoice-item');
          
          [...invoiceList].forEach((invoice) => {
            if (invoice.getAttribute('attr-customer-id') != customerID) {
              invoice.disabled = true;
            }else {
              invoice.disabled = false;
            }
          })

          $('.search-select').each(function (i, obj) {
            if ($(obj).data('select2'))
            {
                $(obj).select2('destroy');
            }
          });
          setTimeout(() => {
            $('.search-select').select2({
              placeholder: 'Select an option'
            });
          }, 500);
        }

      </script>

@include('universal-layout.scripts',
  [
  'select2bs4' => true,
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'bootstrapselect' => true,
  'tableAction' => true,
   
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
