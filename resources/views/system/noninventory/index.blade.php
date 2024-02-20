@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Non-Inventory',
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
                                    <div class="card-header d-flex justify-content-between p-0">
                                        <h4 class="card-title p-0 m-0">Non-Inventory Records</h4>
                                        <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-4" data-toggle="modal" data-target="#createNonInventoryItemModal">
                                            <i class="tim-icons icon-simple-add"></i> CREATE A NON INVENTORY ITEM
                                        </button>

                                        <div class="modal fade" id="createNonInventoryItemModal" tabindex="-1" role="dialog" aria-labelledby="createNonInventoryItemModal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Create Non-inventory Item</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                            <i class="tim-icons icon-simple-remove"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('noninventoryitems.store') }}">
                                                            @csrf
                                                            <div class="d-flex flex-column">

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="itemName">Name</label>
                                                                            <input type="text" required name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="standardCost">Standard Cost</label>
                                                                            <input type="number" min="0" step=".01" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label for="vat">VAT</label>
                                                                            <input type="number" min="0" step=".01" max="100" name="vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge">
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
                                            <input class="search form-control border-dark my-1" placeholder="Search non-inventory items" />
                                            <table class="table tablesorter table-bordered">
                                                <thead class=" text-primary">
                                                    <tr>
                                                        <th data-sort="color" class="sort cursor-pointer border p-0 text-center">
                                                            Item Name
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Standard Cost
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            VAT
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Cost(without VAT)
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
                                                    @forelse($nonInventoryItems as $nonInventoryItem)
                                                    <tr>
                                                        <td style="max-width:7rem;" class="text-truncate color p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $nonInventoryItem->item_name }}">
                                                            {{ $nonInventoryItem->item_name }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $nonInventoryItem->standard_cost }}">
                                                            {{ number_format($nonInventoryItem->standard_cost,2) }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $nonInventoryItem->cost_without_vat }}">
                                                            {{ $nonInventoryItem->standard_cost_vat }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $nonInventoryItem->cost_without_vat }}">
                                                            {{ number_format($nonInventoryItem->standard_cost_without_vat,2) }}
                                                        </td>
                                                        <td class="text-truncate text-nowrap supplier p-0" style="max-width:5rem;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $nonInventoryItem->supplier->name }}">
                                                            {{ $nonInventoryItem->supplier->supplier_name }}
                                                        </td>
                                                        <td class="p-0 d-flex justify-content-around">
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editNonInventoryItemModal{{ $nonInventoryItem->id }}">
                                                                Edit
                                                            </button>
                                                            <div class="modal fade" id="editNonInventoryItemModal{{ $nonInventoryItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Edit Non-inventory Item</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                                                <i class="tim-icons icon-simple-remove"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('noninventoryitems.update', $nonInventoryItem->id) }}">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="d-flex flex-column">

                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="itemName">Name</label>
                                                                                                <input type="text" required name="item_name" value="{{ $nonInventoryItem->item_name }}" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="standardCost">Standard Cost(With VAT)</label>
                                                                                                <input type="number" min="0" step=".01" value="{{ $nonInventoryItem->standard_cost }}" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="vat">VAT</label>
                                                                                                <input type="number" min="0" step=".01" max="100" name="vat" value="{{ $nonInventoryItem->standard_cost_vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="vat">Default Supplier</label>
                                                                                                <select name="supplier_id" id="" class="form-control ms" required>
                                                                                                    @forelse($suppliers as $supplier)
                                                                                                    @if($supplier->id == $nonInventoryItem->supplier_id)
                                                                                                    <option value="{{ $supplier->id }}" selected>
                                                                                                        {{ $supplier->supplier_name }} - (CURRENT)
                                                                                                    </option>
                                                                                                    @else
                                                                                                    <option value="{{ $supplier->id }}">
                                                                                                        {{ $supplier->supplier_name }}
                                                                                                    </option>
                                                                                                    @endif
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
                                                                            <button type="submit" name="submit_btn" value="Create" class="btn btn-primary">EDIT</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteNonInventoryItemModal{{ $nonInventoryItem->id }}">
                                                                Delete
                                                            </button>
                                                            <div class="modal fade" id="deleteNonInventoryItemModal{{ $nonInventoryItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Delete Non-inventory Item</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                                                <i class="tim-icons icon-simple-remove"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('noninventoryitems.destroy', $nonInventoryItem->id) }}">
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
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="100%" class="text-center">
                                                            No non-inventory items registered
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
    <script src="/assets/js/plugins/list.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('load', () => {
            const powderInventoryOptions = {
                valueNames: ['color', 'supplier', 'weight'],
                page: 50,
                pagination: {
                    item: "<li class='p-0'><a class='page px-2 d-block w-100 h-100' href='javascript:void(0);'></a></li>",
                }
            };

            const powderList = new List('powderInventory', powderInventoryOptions);

            const aluminiumInventoryOptions = {
                valueNames: ['name', 'description', 'supplier', 'quantity'],
                page: 50,
                pagination: {
                    item: "<li class='p-0'><a class='page px-2 d-block w-100 h-100' href='javascript:void(0);'></a></li>",
                }
            };

            const aluminiumList = new List('aluminiumInventory', aluminiumInventoryOptions);

            const hardwareInventoryOptions = {
                valueNames: ['name', 'description', 'supplier', 'quantity'],
                page: 50,
                pagination: {
                    item: "<li class='p-0'><a class='page px-2 d-block w-100 h-100' href='javascript:void(0);'></a></li>",
                }
            };

            const hardwareList = new List('hardwareInventory', hardwareInventoryOptions);

            const pagination = document.querySelectorAll('.pagination');

            stylePaginationListItems();

            resetAnchorTagStyling();

            function resetAnchorTagStyling() {
                const paginationAnchorTags = document.querySelectorAll('.pagination > li > a');

                [...paginationAnchorTags].forEach((anchorTag, i) => {
                    anchorTag.addEventListener('click', (e) => {
                        e.preventDefault();
                        setTimeout(() => {
                            stylePaginationListItems();
                        }, 10)
                    })
                });
            }

            function stylePaginationListItems() {
                [...pagination].forEach((paginationItem) => {
                    const list = paginationItem.querySelectorAll('li');
                    [...list].forEach((listItem) => {
                        listItem.classList += ' list-group-item';
                        listItem.querySelector('a').classList += ' text-dark';
                    });
                });
                resetAnchorTagStyling();
            }


            powderList.on('updated', stylePaginationListItems);

            powderList.on('searchComplete', stylePaginationListItems);

            powderList.on('sortComplete', stylePaginationListItems);

            powderList.on('filterComplete', stylePaginationListItems);

            aluminiumList.on('updated', stylePaginationListItems);

            aluminiumList.on('searchComplete', stylePaginationListItems);

            aluminiumList.on('sortComplete', stylePaginationListItems);

            aluminiumList.on('filterComplete', stylePaginationListItems);

            hardwareList.on('updated', stylePaginationListItems);

            hardwareList.on('searchComplete', stylePaginationListItems);

            hardwareList.on('sortComplete', stylePaginationListItems);

            hardwareList.on('filterComplete', stylePaginationListItems);

            const searchInputs = document.querySelectorAll('.search');
            searchInputs.forEach((input) => {
                input.dispatchEvent(new Event('keyup'));
            })

        });
    </script>
    @include('universal-layout.scripts',
    [
    'libscripts' => true,
    'vendorscripts' => true,
    'mainscripts' => true,
    'select2' => true,
    ]
    )
    @include('universal-layout.alert')
    @include('universal-layout.footer')