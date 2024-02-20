@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Inventory',
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
                                        <h4 class="card-title p-0 m-0 col-12 text-center">Inventory Records</h4>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#inventoryExcelModal">
                                            <i class="tim-icons icon-simple-add"></i> EXCEL UPLOAD/DOWNLOAD INVENTORY
                                        </button>

                                        <div class="modal fade" id="inventoryExcelModal" tabindex="-1" role="dialog" aria-labelledby="inventoryExcelModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Create Inventory Item</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" enctype="multipart/form-data" autocomplete="off" action="{{ route('inventoryitems.exceltemplate.upload') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">
                                                                <a href="{{ route('inventoryitems.exceltemplate') }}" class="btn btn-success">Download Excel Template</a>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="excelfile">Excel File</label>
                                                                            <input type="file" required name="inventory_excel_file" class="form-control" id="excelfile" class="dark-text" placeholder="Enter inventory file">
                                                                            <small>*For use only when adding new inventory</small>
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

                                        <a href="{{ route('inventoryitems.excelreport') }}" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col">
                                            <i class="tim-icons icon-simple-add"></i> GET EXCEL SHEET {{ date('d-M-Y', time()) }}
                                        </a>

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#inventoryExcelCustomDateModal">
                                            <i class="tim-icons icon-simple-add"></i> GET EXCEL SHEET CUSTOM
                                        </button>

                                        <div class="modal fade" id="inventoryExcelCustomDateModal" tabindex="-1" role="dialog" aria-labelledby="inventoryExcelCustomDateModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Inventory Excel</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('inventoryitems.custom.excel') }}">
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

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col" data-toggle="modal" data-target="#inventoryEditExcelModal">
                                            <i class="tim-icons icon-simple-add"></i> EXCEL EDIT INVENTORY QUANTITY
                                        </button>

                                        <div class="modal fade" id="inventoryEditExcelModal" tabindex="-1" role="dialog" aria-labelledby="inventoryExcelModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Edit Inventory</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" enctype="multipart/form-data" autocomplete="off" action="{{ route('inventoryitems.exceltemplate.edit.upload') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">
                                                                <a href="{{ route('inventoryitems.exceltemplate.edit') }}" class="btn btn-success">Download Excel Template</a>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="excelfile">Excel File For Inventory</label>
                                                                            <input type="file" required name="inventory_excel_file" class="form-control" id="excelfile" class="dark-text" placeholder="Enter inventory file">
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

                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-4" data-toggle="modal" data-target="#createInventoryItemModal">
                                            <i class="tim-icons icon-simple-add"></i> CREATE AN INVENTORY ITEM
                                        </button>
                                        

                                        <div class="modal fade" id="createInventoryItemModal" tabindex="-1" role="dialog" aria-labelledby="createInventoryItemModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Create Inventory Item</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('inventoryitems.store') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="itemName">Name</label>
                                                                            <input type="text" required name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="itemCode">Code</label>
                                                                            <input type="text" required name="item_code" class="form-control" id="itemCode" class="dark-text" placeholder="Enter item code">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="itemDescription">Description</label>
                                                                            <input type="text" required name="item_description" class="form-control" id="itemDescription" class="dark-text" placeholder="Enter item description">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="serial">Serial</label>
                                                                            <input type="text" required name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter item serial number">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="quantityTag">Quantity Tag</label>
                                                                            <input type="text" required name="quantity_tag" class="form-control" id="quantityTag" class="dark-text" placeholder="Enter item quantity tag">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="type">Type</label>
                                                                            <select name="type" required class="form-control ms">
                                                                                @forelse($inventoryItemTypes as $type)
                                                                                <option value="{{ $type->value }}">
                                                                                    {{ $type->humanreadablestring() }}
                                                                                </option>
                                                                                @empty
                                                                                <option disabled selected>No types to choose from</option>
                                                                                @endforelse
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="goodsWeight">Goods Weight</label>
                                                                            <input type="number" min="0" step=".0001" required name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
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
                                                                            <input type="number" min="0" step=".01" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="openingQuantity">Opening Quantity</label>
                                                                            <input type="number" min="0" step=".01" name="opening_quantity" class="form-control" id="openingQuantity" class="dark-text" placeholder="Enter opening quantity">
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
                                                            Item Name
                                                        </th>
                                                        <th data-sort="color" class="sort cursor-pointer border p-0 text-center">
                                                            Type
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Standard Price
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Current Quantity
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Goods Weight
                                                        </th>
                                                        <th data-sort="warehouse" class="text-center sort cursor-pointer border p-0">
                                                            Supplier
                                                        </th>
                                                        <th class="p-0 border text-center">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    @forelse($inventoryItems as $inventoryItem)
                                                    <tr>
                                                        <td style="max-width:7rem;" class="text-truncate color p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->item_name }}">
                                                            {{ $inventoryItem->item_name }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->standard_price }}">
                                                            {{ $inventoryItem->type->humanreadablestring() }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->standard_price }}">
                                                            {{ $inventoryItem->standard_price }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->current_quantity }}">
                                                            {{ $inventoryItem->current_quantity }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->goods_weight }}">
                                                            {{ $inventoryItem->goods_weight }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inventoryItem->supplier->name ?? '' }}">
                                                            {{ $inventoryItem->supplier->supplier_name ?? '' }}
                                                        </td>
                                                        <td class="p-0 d-flex justify-content-around">
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editInventoryItemModal{{ $inventoryItem->id }}" attr-data-inventory-item-id="{{ $inventoryItem->id }}" onclick="loadEditForm(this)">
                                                                Edit
                                                            </button>
                                                            <div class="modal fade" id="editInventoryItemModal{{ $inventoryItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">

                                                                </div>
                                                            </div>

                                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteInventoryItemModal{{ $inventoryItem->id }}">
                                                                Delete
                                                            </button>
                                                            <div class="modal fade" id="deleteInventoryItemModal{{ $inventoryItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Delete inventory item</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                                                <i class="tim-icons icon-simple-remove"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('inventoryitems.destroy', $inventoryItem->id) }}">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <div class="d-flex flex-column">

                                                                                    <div class="row">
                                                                                        <div class="alert alert-danger mx-auto">
                                                                                            Are you sure you want to delete this item?
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

                                                            <a href="/inventoryitems/{{ $inventoryItem->id }}" class="btn btn-info btn-sm">
                                                                Show Breakdown
                                                            </a>

                                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editInventoryQuantity{{ $inventoryItem->id }}" attr-data-inventory-item-id="{{ $inventoryItem->id }}" onclick="loadEditQtyForm(this)">
                                                                Edit Quantity
                                                            </button>
                                                            <div class="modal fade" id="editInventoryQuantity{{ $inventoryItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="100%" class="text-center">
                                                            No inventory items registered
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="100%">
                                                            <ul class="pagination list-group list-group-horizontal">

                                                            </ul>
                                                        </td>
                                                    </tr>
                                                </tfoot>
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

            const inventoryItemID = editButton.getAttribute('attr-data-inventory-item-id');

            const inventoryItemEditForm = document.querySelector(editButton.getAttribute('data-target')).querySelector('.modal-dialog');

            const inventoryItemEditFormRequest = await fetch(`/inventoryitems/edit-form/${inventoryItemID}`);

            if (inventoryItemEditFormRequest.ok) {
                const response = await inventoryItemEditFormRequest.text();

                inventoryItemEditForm.innerHTML = response;
            } else {
                inventoryItemEditForm.innerHTML = '<div class="alert alert-danger">Error in getting data</div>';
            }
            $(inventoryItemEditForm.querySelector('select.search-dropdown')).select2();
        }

        async function loadEditQtyForm(editButton) {

            const inventoryItemID = editButton.getAttribute('attr-data-inventory-item-id');

            const inventoryItemEditForm = document.querySelector(editButton.getAttribute('data-target')).querySelector('.modal-dialog');

            const inventoryItemEditFormRequest = await fetch(`/inventoryitems/edit-qty-form/${inventoryItemID}`);

            if (inventoryItemEditFormRequest.ok) {
                const response = await inventoryItemEditFormRequest.text();

                inventoryItemEditForm.innerHTML = response;
            } else {
                inventoryItemEditForm.innerHTML = '<div class="alert alert-danger">Error in getting data</div>';
            }
        }
    </script>
    @include('universal-layout.alert')
    @include('universal-layout.footer')