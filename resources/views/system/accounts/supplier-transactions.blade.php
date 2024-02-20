@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Accounts Suppliers',
'datatable' => true,
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

    .cursor-pointer {
        cursor: pointer !important;
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
                <div class="main-panel">

                    <div class="content">

                        <div class="col">
                            <div class="card card-plain p-0">
                                <div class="card-header p-0">
                                    <h4 class="card-title p-0 m-0">Supplier Transactions</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row justify-content-between">
                                        <div class="col-sm-12">
                                            <div class="py-1 row m-0">
                                                <div class="d-flex justify-content-around col-sm-4 p-0 supplier-list filters">
                                                    <div class="d-flex flex-column col-5 col-sm-6 p-0">
                                                        <span>Min. Date</span>
                                                        <input type="date" name="min" class="min border-dark form-control rounded-0" id="">
                                                    </div>
                                                    <div class="d-flex flex-column col-5 col-sm-6 p-0">
                                                        <span>Max. Date</span>
                                                        <input type="date" name="max" class="max border-dark form-control rounded-0" id="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="text-center mx-2">
                                                        <p class="mb-0">Type</p>
                                                        <select onchange="resetOptions(this)" class="form-control border border-dark ms">
                                                            <option value="">All</option>
                                                            <option value="Purchase Order">Purchase Order</option>
                                                            <option value="Credit Note">Credit Note</option>
                                                            <option value="Payment">Bill Payment</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="text-center mx-2">
                                                        <p class="mb-0">Supplier</p>
                                                        <select onchange="resetOptions(this)" class="form-control" data-live-search="true" data-style="text-white">
                                                            <option value="" data-tokens="NONE" selected>NONE</option>
                                                            @foreach($suppliers as $singleSupplier)
                                                            <option value="{{ $singleSupplier->supplier_name }}" data-tokens="{{ $singleSupplier->supplier_name }}">
                                                                {{ $singleSupplier->supplier_name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive overflow-auto px-1 pt-1">
                                                <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="supplier-list">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Supplier</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Reference</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recordsArray as $record)
                                                        <tr data-customer-id="{{ $record['supplier_id'] }}">
                                                            <td>{{ $record['date'] }}</td>
                                                            <td>{{ $record['supplier_name'] }}</td>
                                                            <td>{{ $record['type'] }}</td>
                                                            <td>{{ $record['amount']  }}</td>
                                                            <td>{{ $record['id'] }}</td>
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

    <script src="/assets/js/plugins/dataTables.buttons.min.js"></script>
    <script src="/assets/js/plugins/jszip.min.js"></script>
    <script src="/assets/js/plugins/pdfmake.min.js"></script>
    <script src="/assets/js/plugins/vfs_fonts.js"></script>
    <script src="/assets/js/plugins/buttons.html5.min.js"></script>
    <script src="/assets/js/plugins/buttons.print.min.js"></script>

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
                    (min <= date && date <= max)) {
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
                    dom: 'Bfrtip',
                    buttons: [
                        'pdf', 'print'
                    ]
                });

                dataTableInstances.push(table);
                const printers = document.querySelectorAll('.dt-buttons button');
                printers.forEach((printerBtn) => {
                    printerBtn.classList.add('btn', 'btn-warning');
                })
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

        function resetOptions(input) {
            dataTableInstances[0].search(input.value).draw();
        }
    </script>
    @include('universal-layout.footer')