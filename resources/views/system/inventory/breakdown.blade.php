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
                                        <h4 class="card-title p-0 m-0 col-12 text-center">Item Breakdown</h4>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p class="p-0 m-0">NAME: {{ $inventoryitem->item_name }}</p>
                                                <p class="p-0 m-0">SUPPLIER: {{ $inventoryitem->supplier->supplier_name ?? 'N/A' }}</p>
                                                <p class="p-0 m-0">DATE: {{ $date }}</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Select Date</label>
                                                    <input type="date" class="form-control" onchange="redirectDate(this)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive powderInventory" id="powderInventory">
                                            <table class="table tablesorter table-bordered">
                                                <thead class=" text-primary">
                                                    <tr>
                                                        <th data-sort="color" class="sort cursor-pointer border p-0 text-center">
                                                            Date
                                                        </th>
                                                        <th data-sort="color" class="sort cursor-pointer border p-0 text-center">
                                                            Reason
                                                        </th>
                                                        <th data-sort="supplier" class="text-center sort cursor-pointer border p-0">
                                                            Quantity
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list">
                                                    <tr>
                                                        <td class="p-0">Opening Quantity</td>
                                                        <td class="p-0"></td>
                                                        <td class="p-0">{{ number_format($openingQuantity, 2) }}</td>
                                                    </tr>
                                                    @forelse($currentTransactions as $currentTransactions)
                                                    <tr>
                                                        <td class="p-0">{{ $currentTransactions->created_at }}</td>
                                                        <td class="p-0">{{ $currentTransactions->reason->humanreadablestring() }}</td>
                                                        <td class="p-0">{{ $currentTransactions->sum_added }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="100%" class="text-center">
                                                            No movements for date
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="p-0">Final quantity</td>
                                                        <td class="p-0"></td>
                                                        <td class="p-0">{{ $inventoryitem->current_quantity }}</td>
                                                    </tr>
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
    <script>
        function redirectDate(dateInput) {
            window.location.search = `?date=${dateInput.value}`;
        }
    </script>
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