@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Powder',
'datatable' => true,
'select2' => true,
]
)

<body class="theme-green">
    @include('universal-layout.spinner')

    @include('universal-layout.system-sidemenu',
    [
    'slug' => '/inventory'
    ]
    )
    <section class="content home">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="main-panel">

                    <div class="content">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header row justify-content-between p-0">
                                        <h4 class="card-title p-0 m-0 col-12 text-center">Powder Records</h4>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#powderExcelModal">
                                            <i class="tim-icons icon-simple-add"></i> EXCEL UPLOAD/DOWNLOAD POWDER
                                        </button>

                                        <div class="modal fade" id="powderExcelModal" tabindex="-1" role="dialog" aria-labelledby="powderExcelModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Create Powder</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" enctype="multipart/form-data" autocomplete="off" action="{{ route('powders.exceltemplate.upload') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">
                                                                <a href="{{ route('powders.exceltemplate') }}" class="btn btn-success">Download Excel Template</a>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="excelfile">Excel File For Powder</label>
                                                                            <input type="file" required name="powder_excel_file" class="form-control" id="excelfile" class="dark-text" placeholder="Enter powder file">
                                                                            <small>*For use only when adding new powders</small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                                        <button type="submit" name="submit_btn" value="Upload" class="btn btn-primary">UPLOAD</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('powders.excelreport') }}" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col">
                                            <i class="tim-icons icon-simple-add"></i> GET EXCEL SHEET {{ date('d-M-Y', time()) }}
                                        </a>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#powderExcelCustomDateModal">
                                            <i class="tim-icons icon-simple-add"></i> GET EXCEL SHEET CUSTOM
                                        </button>

                                        <div class="modal fade" id="powderExcelCustomDateModal" tabindex="-1" role="dialog" aria-labelledby="powderExcelCustomDateModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Inventory Excel</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('powders.custom.excel') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="excelfile">Date For Inventory</label>
                                                                            <input type="date" required name="inventory_date" class="form-control" class="dark-text" placeholder="Enter inventory date">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                                        <button type="submit" name="submit_btn" value="Upload" class="btn btn-primary">PROCEED</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#powderEditExcelModal">
                                            <i class="tim-icons icon-simple-add"></i> EXCEL EDIT POWDER QTY
                                        </button>

                                        <div class="modal fade" id="powderEditExcelModal" tabindex="-1" role="dialog" aria-labelledby="powderExcelModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Edit Powder</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" enctype="multipart/form-data" autocomplete="off" action="{{ route('powders.exceltemplate.edit.upload') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">
                                                                <a href="{{ route('powders.exceltemplate.edit') }}" class="btn btn-success">Download Excel Template</a>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="excelfile">Excel File For Powder</label>
                                                                            <input type="file" required name="powder_excel_file" class="form-control" id="excelfile" class="dark-text" placeholder="Enter powder file">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                                        <button type="submit" name="submit_btn" value="Upload" class="btn btn-primary">UPLOAD</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#createPowderModal">
                                            <i class="tim-icons icon-simple-add"></i> CREATE POWDER
                                        </button>

                                        <div class="modal fade" id="createPowderModal" tabindex="-1" role="dialog" aria-labelledby="createPowderModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Create Powder</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('powders.store') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="color">Color</label>
                                                                            <input type="text" required name="powder_color" class="form-control" id="color" class="dark-text" placeholder="Enter powder color">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="code">Code</label>
                                                                            <input type="text" required name="powder_code" class="form-control" id="code" class="dark-text" placeholder="Enter powder code">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="description">Description</label>
                                                                            <input type="text" required name="powder_description" class="form-control" id="description" class="dark-text" placeholder="Enter powder description">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="serial">Serial Number</label>
                                                                            <input type="text" required name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter serial number">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="manufactureDate">Manufacture Date</label>
                                                                            <input type="date" required name="manufacture_date" class="form-control" id="manufactureDate" class="dark-text">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="expiryDate">Expiry Date</label>
                                                                            <input type="date" required name="expiry_date" class="form-control" id="expiryDate" class="dark-text">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="goodsWeight">Goods Weight</label>
                                                                            <input type="number" min="0" step=".001" required name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="batchNo">Batch Number</label>
                                                                            <input type="text" required name="batch_no" class="form-control" id="batchNo" class="dark-text" placeholder="Enter batch">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="standardCost">Standard Cost(With VAT)</label>
                                                                            <input type="number" min="0" step=".01" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="vat">Standard Cost VAT</label>
                                                                            <input type="number" min="0" step=".01" max="100" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="standardCost">Standard Price(With VAT)</label>
                                                                            <input type="number" min="0" step=".01" required name="standard_price" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="vat">Standard Price VAT</label>
                                                                            <input type="number" min="0" step=".01" max="100" name="standard_price_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="minThreshold">Min Threshold</label>
                                                                            <input type="number" min="0" step=".01" required name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="maxThreshold">Max Threshold</label>
                                                                            <input type="number" min="0" step=".01" max="100" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="openingWeight">Opening Weight</label>
                                                                            <input type="number" min="0" step=".01" max="100" name="opening_weight" class="form-control" id="openingWeight" class="dark-text" placeholder="Enter opening weight">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="vat">Default Supplier</label>
                                                                            <select name="supplier_id" id="" class="form-control ms" required>
                                                                                @forelse($suppliers as $supplier)
                                                                                <option value="{{ $supplier->id }}">
                                                                                    {{ $supplier->supplier_name }}
                                                                                </option>
                                                                                @empty
                                                                                <option value="">No suppliers registered</option>
                                                                                @endforelse
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="vat">Storage Area</label>
                                                                            <select name="bin_id" class="form-control ms" required>
                                                                                @forelse($bins as $bin)
                                                                                <option value="{{ $bin->id }}">
                                                                                    {{ $bin->bin_name }} : ({{ $bin->shelf->shelf_name }} -> {{ $bin->shelf->floor->floor_name }} -> {{ $bin->shelf->floor->warehouse->warehouse_name }})
                                                                                </option>
                                                                                @empty
                                                                                <option value="">No suppliers registered</option>
                                                                                @endforelse
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                                        <button type="submit" name="submit_btn" value="Create" class="btn btn-primary">CREATE</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive powderInventory" id="powderInventory">
                                            <table class="table data-table table-bordered">
                                                <thead class=" text-primary">
                                                    <tr>
                                                        <th data-sort="color" class="sort cursor-pointer border p-0 text-center">
                                                            Powder Color
                                                        </th>
                                                        <th data-sort="warehouse" class="text-center sort cursor-pointer border p-0">
                                                            Supplier
                                                        </th>
                                                        <th data-sort="warehouse" class="text-center sort cursor-pointer border p-0">
                                                            Current Weight
                                                        </th>
                                                        <th data-sort="warehouse" class="text-center sort cursor-pointer border p-0">
                                                            Expiry Date
                                                        </th>
                                                        <th data-sort="warehouse" class="text-center sort cursor-pointer border p-0">
                                                            Date Created
                                                        </th>
                                                        <th class="p-0 border text-center">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    @forelse($powders as $powder)
                                                    <tr>
                                                        <td style="max-width:7rem;" class="text-truncate color p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $powder->powder_color }}">
                                                            {{ $powder->powder_color }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $powder->supplier->supplier_name }}">
                                                            {{ $powder->supplier->supplier_name }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $powder->current_weight }}">
                                                            {{ $powder->current_weight }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $powder->expiry_date }}">
                                                            {{ $powder->expiry_date }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $powder->created_at }}">
                                                            {{ $powder->created_at }}
                                                        </td>

                                                        <td class="p-0 d-flex justify-content-around">
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPowderModal{{ $powder->id }}" attr-data-powder-id="{{ $powder->id }}" onclick="loadEditForm(this)">
                                                                Edit
                                                            </button>
                                                            <div class="modal fade" id="editPowderModal{{ $powder->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document"></div>
                                                            </div>

                                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deletePowderModal{{ $powder->id }}">
                                                                Delete
                                                            </button>

                                                            <div class="modal fade" id="deletePowderModal{{ $powder->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Delete Powder</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                                                <i class="tim-icons icon-simple-remove"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('powders.destroy', $powder->id) }}">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <div class="d-flex flex-column">

                                                                                    <div class="row">
                                                                                        <div class="alert alert-danger mx-auto">
                                                                                            Are you sure you want to delete this powder?
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                                                            <button type="submit" name="submit_btn" value="Create" class="btn btn-primary">DELETE</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <a href="/powders/{{ $powder->id }}" class="btn btn-info btn-sm">
                                                                Show Breakdown
                                                            </a>

                                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editPowderQuantity{{ $powder->id }}" attr-data-powder-id="{{ $powder->id }}" onclick="loadEditQtyForm(this)">
                                                                Edit Quantity
                                                            </button>

                                                            <div class="modal fade" id="editPowderQuantity{{ $powder->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document"></div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="100%" class="text-center">
                                                            No powder item registered
                                                        </td>
                                                    </tr>
                                                    @endforelse
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
    </section>
    <!-- Init for powder inventory list -->
    @include('universal-layout.scripts',
    [
    'libscripts' => true,
    'vendorscripts' => true,
    'mainscripts' => true,
    'select2' => true,
    'datatable' => true,
    ])
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

    <script>
        async function loadEditForm(editButton) {

            const powderID = editButton.getAttribute('attr-data-powder-id');

            const powderEditForm = document.querySelector(editButton.getAttribute('data-target')).querySelector('.modal-dialog');

            const powderEditFormRequest = await fetch(`/powders/edit-form/${powderID}`);

            if (powderEditFormRequest.ok) {
                const response = await powderEditFormRequest.text();

                powderEditForm.innerHTML = response;
            } else {
                powderEditForm.innerHTML = '<div class="alert alert-danger">Error in getting data</div>';
            }
            $(powderEditForm.querySelector('select')).select2();
        }

        async function loadEditQtyForm(editButton) {

            const powderID = editButton.getAttribute('attr-data-powder-id');

            const powderEditForm = document.querySelector(editButton.getAttribute('data-target')).querySelector('.modal-dialog');

            const powderEditFormRequest = await fetch(`/powders/edit-qty-form/${powderID}`);

            if (powderEditFormRequest.ok) {
                const response = await powderEditFormRequest.text();

                powderEditForm.innerHTML = response;
            } else {
                powderEditForm.innerHTML = '<div class="alert alert-danger">Error in getting data</div>';
            }
        }
    </script>
    @include('universal-layout.alert')
    @include('universal-layout.footer')