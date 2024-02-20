<?php
$privileges = session()->get('auth_privileges_uid');
$companyID = session()->get('auth_company_uid');
if ($privileges == 'ALL') {
    $allowedMenuPages = 'ALL';
} else {
    $allowedMenuPages = json_decode($privileges);
}
?>
<div class="overlay_menu px-2 px-sm-0">
    <button class="btn btn-primary btn-icon btn-icon-mini btn-round"><i class="zmdi zmdi-close"></i></button>
    <div class="container">
        <div class="row clearfix">
            <div class="card">
                <div class="body">
                    <div class="input-group m-b-0">
                        <input type="text" class="form-control" oninput="searchList(this, '.apps-list > li', 'a')" placeholder="Search...">
                        <span class="input-group-addon">
                            <i class="zmdi zmdi-search"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card links">
                <div class="body">
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <div class="social">
                    <p>
                        Created by <img src="/assets/img/aprotec-old.png" alt="Aprotec Logo" style="width: 5rem;">
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="overlay"></div><!-- Overlay For Sidebars -->
<div class="vh-100 vw-100 bg-dark z-index position-fixed d-none flex-column text-white justify-content-center align-items-center" id="syncer">
    <h1>Syncing In Progress</h1>
    <span class="spinner-border" style="height:3rem;width:3rem;"></span>
</div>

<div class="bg-dark z-index position-fixed d-none flex-column text-white text-center justify-content-center align-items-center" id="syncerSmall">
    <h6>Syncing In Progress</h6>
    <span class="spinner-border" style="height:1rem;width:1rem;"></span>
</div>
<!-- Left Sidebar -->
<aside id="minileftbar" class="minileftbar">
    <ul class="menu_list">
        <li><a href="javascript:void(0);" class="menu-sm"><i class="zmdi zmdi-swap"></i></a></li>
        <li><a href="javascript:void(0);" class="fullscreen" data-provide="fullscreen"><i class="zmdi zmdi-fullscreen"></i></a></li>
        <li><a href="/flush-cache"><i class="zmdi zmdi-refresh"></i></a></li>
        <li class="d-none" onclick="syncDBUI()"><a href="javascript:void(0);"><i class="zmdi zmdi-refresh"></i></a></li>
        <script>
            async function syncDBUI(showBig = true) {
                let syncerUI;

                if (showBig) {
                    syncerUI = document.querySelector('#syncer');
                } else {
                    syncerUI = document.querySelector('#syncerSmall');
                }
                syncerUI.classList.toggle('d-none');
                syncerUI.classList.toggle('d-flex');
                try {
                    const response = await sync();

                    if (response.local_sql) {
                        alert("Sync successful");
                    } else {
                        alert("Sync failed");
                    }
                } catch (error) {
                    alert("Sync failed");
                } finally {
                    syncerUI.classList.toggle('d-none');
                    syncerUI.classList.toggle('d-flex');
                }

            }
            async function sync() {
                await fetch('/backup');

                const fetchRequest = await fetch('/sync', {
                    headers: {
                        'company': '{{ auth()->user()->company_id }}',
                    },
                });
                const response = await fetchRequest.json();
                return (response);
            }
        </script>
        <li>
            <a href="/download-log"><i class="zmdi zmdi-file-text"></i></a>
        </li>
        <li class="power">
            <a href="javascript:void(0);" class="js-right-sidebar"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a>
            <a href="/logout" class="mega-menu"><i class="zmdi zmdi-power"></i></a>
        </li>
    </ul>
</aside>

<aside class="right_menu">
    <div id="leftsidebar" class="sidebar">
        <div class="menu">
            <ul class="list">
                <li>
                    <div class="user-info m-0">
                        <div class="image">
                            <a href="#"><img src="/assets/img/maruti-square.png" alt="User" class="w-25"></a>
                        </div>
                        <div class="detail">
                            <h6>{{ auth()->user()->username }}</h6>
                        </div>
                    </div>
                </li>
                <li class="<?php echo $activeStatus = ($slug == '/') ? 'active open' : ''; ?>">
                    <a href="/dashboard"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a>
                </li>

                <li class="<?php echo $activeStatus = ($slug == '/coatingjobs/create' || $slug == '/coatingjobs') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">format_paint</i>
                        <span class="m-0">Powder Coating</span></a>
                    <ul class="ml-menu">
                        @can('create', App\Models\CoatingJob::class)
                        <li>
                            <a href="/coatingjobs/create">
                                Create Coating Job
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\CoatingJob::class)
                        <li>
                            <a href="/coatingjobs">
                                Jobs List
                            </a>
                        </li>
                        <li>
                            <a href="/coatingjobs/closed">
                                Closed Jobs
                            </a>
                        </li>
                        <li>
                            <a href="/coatingjobs/cancelled">
                                Cancelled Jobs
                            </a>
                        </li>
                        <li>
                            <a href="/coatingjobs/unbilled">
                                Unbilled Job Cards
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>

                <li class="<?php echo $activeStatus = ($slug == '/locations') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">location_on</i>
                        <span class="m-0">Sections</span></a>
                    <ul class="ml-menu">
                        @can('viewAny', App\Models\Location::class)
                        <li>
                            <a href="/locations">
                                Locations
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Warehouse::class)
                        <li>
                            <a href="/warehouses">
                                Warehouses
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Floor::class)
                        <li>
                            <a href="/floors">
                                Floors
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Shelf::class)
                        <li>
                            <a href="/shelves">
                                Shelves
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Bin::class)
                        <li>
                            <a href="/bins">
                                Bins
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>


                <li class="<?php echo $activeStatus = ($slug == '/suppliers' || $slug == '/customers') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">add_shopping_cart</i>
                        <span class="m-0">Suppliers & Customers</span></a>
                    <ul class="ml-menu">
                        @can('viewAny', App\Models\Supplier::class)
                        <li>
                            <a href="/suppliers">
                                Suppliers
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Customer::class)
                        <li>
                            <a href="/customers">
                                Customers
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>

                <li class="<?php echo $activeStatus = ($slug == '/purchases') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">shopping_cart</i>
                        <span class="m-0">Purchases</span></a>
                    <ul class="ml-menu">
                        @can('create', App\Models\PurchaseOrder::class)
                        <li>
                            <a href="/purchaseorders/create">
                                Add Purchase Order
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\PurchaseOrder::class)
                        <li>
                            <a href="/purchaseorders">
                                View Purchase Orders
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="/suppliercreditnotes/create">
                                Create Supplier Credit Notes
                            </a>
                        </li>
                        <li>
                            <a href="/suppliercreditnotes">
                                View Supplier Credit Notes
                            </a>
                        </li>
                    </ul>
                </li>

                @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->expenses))
                <li class="<?php echo $activeStatus = ($slug == '/expenses') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">attach_money</i>
                        <span class="m-0">Expenses</span></a>
                    <ul class="ml-menu">
                        <li>
                            <a href="/expenses/ap-creditnote">
                                Create AP Credit Note
                            </a>
                        </li>
                        <li>
                            <a href="/expenses/view-ap-creditnote">
                                View AP Credit Notes
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="<?php echo $activeStatus = ($slug == '/sales') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">credit_card</i>
                        <span class="m-0">Sales</span></a>
                    <ul class="ml-menu">
                        @can('viewAny', App\Models\CoatingJob::class)
                        <li>
                            <a href="/coatingjobs/quotations">
                                Quotations
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Invoice::class)
                        <li>
                            <a href="/invoices">
                                Invoices
                            </a>
                            <a href="/invoices/external" class="d-none">
                                Ext Invoices
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\CashSale::class)
                        <li>
                            <a href="/cashsales">
                                Cash Sales
                            </a>
                        </li>
                        <li>
                            <a href="/cashsales/external">
                                Ext Cash Sales
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="/customercreditnotes/create">
                                Create Credit Note
                            </a>
                        </li>
                        <li>
                            <a href="/customercreditnotes">
                                View Credit Notes
                            </a>
                        </li>
                    </ul>
                </li>

                @can('accounting')
                <li class="<?php echo $activeStatus = ($slug == '/accounts') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">account_balance</i>
                        <span class="m-0">Accounts</span></a>
                    <ul class="ml-menu">
                        <li>
                            <a href="/customers/agingreport" target="_blank">
                                View Accounting
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <li class="<?php echo $activeStatus = ($slug == '/inventory') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">assignment</i>
                        <span class="m-0">Inventory & Non-inventory</span></a>
                    <ul class="ml-menu">
                        @can('viewAny', App\Models\NonInventoryItem::class)
                        <li>
                            <a href="/noninventoryitems">
                                Non-inventory Items
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Powder::class)
                        <li>
                            <a href="/powders">
                                Powder Items
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\InventoryItem::class)
                        <li>
                            <a href="/inventoryitems">
                                Inventory Items
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>

                <li class="<?php echo $activeStatus = ($slug == '/settings') ? 'active open' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">settings</i>
                        <span class="m-0">Settings</span></a>
                    <ul class="ml-menu">
                        @can('viewAny', App\Models\User::class)
                        <li>
                            <a href="/users">
                                Users
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\DocumentLabel::class)
                        <li>
                            <a href="/documentlabels">
                                Document Numbering
                            </a>
                        </li>
                        @endcan
                        
                        @can('accounting')
                        <li>
                            <a href="/email-secrets">
                                Email Credentials
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\Role::class)
                        <li>
                            <a href="/roles">
                                Roles
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\Tax::class)
                        <li>
                            <a href="/taxes">
                                VAT
                            </a>
                        </li>
                        @endif

                    </ul>
                </li>

                <li class="<?php echo $activeStatus = ($slug == '/contact') ? 'active' : ''; ?>">
                    <a href="mailto:info@aprotec.com">
                        <i class="material-icons">forum</i>
                        <span class="m-0">Contact Us</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</aside>