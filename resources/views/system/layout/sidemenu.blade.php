<div class="sidebar">

    <?php
$privileges = session()->get('auth_privileges_uid');
if ($privileges == 'ALL') {
  $allowedMenuPages = 'ALL';
}else {
  $allowedMenuPages = json_decode($privileges);
}
?>

    <div class="sidebar-wrapper">
        <div class="logo">
            <div class="simple-text d-flex">
                <img src="/assets/img/aprotec-old.png" class="w-100" alt="Aprotec Icon">
            </div>
        </div>
        <ul class="nav">


            <li class="<?php echo $activeStatus = ($currentPage == 'dashboard') ? 'active' : '' ; ?>">
                <a href="/dashboard">
                    <p class="menu-option">Dashboard</p>
                </a>
            </li>

            <li class="display-4 ml-2 mt-3 text-white">
                Projects
            </li>

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' &&
            $allowedMenuPages->powder_coating) )
            <li class="<?php echo $activeStatus = ($currentPage == 'coatingjobs') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#payments">
                <a href="javascript:void(0)">
                    <p class="menu-option">Powder Coating</p>
                </a>
            </li>
            <div id="payments" class="collapse pl-4">
                <li>
                    <a href="/coatingjobs/create">
                        <i class="fa-solid fa-spray-can"></i>
                        <p class="menu-sub-option">Coating Job</p>
                    </a>
                </li>
                <li>
                    <a href="/coatingjobs">
                        <i class="fa-solid fa-spray-can"></i>
                        <p class="menu-sub-option">Jobs List</p>
                    </a>
                </li>
            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' &&
            $allowedMenuPages->aluminium_acquisition))
            <li class="<?php echo $activeStatus = ($currentPage == 'aluminium-acquisition') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#aluminiumAcquisition">
                <a href="javascript:void(0)">
                    <p class="menu-option">Aluminium Acquisition</p>
                </a>
            </li>
            <div id="aluminiumAcquisition" class="collapse pl-4">
                <li>
                    <a href="/acquisition/aluminium">
                        <i class="fa-solid fa-circle-plus"></i>
                        <p class="menu-sub-option">Create Acquisition</p>
                    </a>
                </li>
                <li>
                    <a href="/acquisition/aluminium/past">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Past Acquisitions</p>
                    </a>
                </li>
            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' &&
            $allowedMenuPages->hardware_acquisition))
            <li class="<?php echo $activeStatus = ($currentPage == 'hardware-acquisition') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#hardwareAcquisition">
                <a href="javascript:void(0)">
                    <p class="menu-option">Hardware Acquisition</p>
                </a>
            </li>
            <div id="hardwareAcquisition" class="collapse pl-4">
                <li>
                    <a href="/acquisition/hardware">
                        <i class="fa-solid fa-circle-plus"></i>
                        <p class="menu-sub-option">Create Acquisition</p>
                    </a>
                </li>
                <li>
                    <a href="/acquisition/hardware/past">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Past Acquisitions</p>
                    </a>
                </li>
            </div>
            @endif

            <li class="display-4 ml-2 text-white">
                Office
            </li>

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->locations))
            <li class="<?php echo $activeStatus = ($currentPage == 'locations') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#locations">
                <a href="javascript:void(0)">
                    <p class="menu-option">Locations, Warehouses & Bins</p>
                </a>
            </li>
            <div id="locations" class="collapse pl-4">
                <li>
                    <a href="/locations">
                        <i class="fa-solid fa-location-dot"></i>
                        <p class="menu-sub-option">Locations</p>
                    </a>
                </li>

                <li>
                    <a href="/warehouses">
                        <i class="fa-solid fa-warehouse"></i>
                        <p class="menu-sub-option">Warehouses</p>
                    </a>
                </li>

                <li>
                    <a href="/floors">
                        <i class="fa-solid fa-building"></i>
                        <p class="menu-sub-option">Floor Levels</p>
                    </a>
                </li>

                <li>
                    <a href="/shelves">
                        <i class="fa-solid fa-store"></i>
                        <p class="menu-sub-option">Shelves</p>
                    </a>
                </li>

                <li>
                    <a href="/bins">
                        <i class="fa-solid fa-ring"></i>
                        <p class="menu-sub-option">Bin</p>
                    </a>
                </li>

            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->suppliers))
            <li class="<?php echo $activeStatus = ($currentPage == 'suppliers') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#suppliers">
                <a href="javascript:void(0)">
                    <p class="menu-option">Suppliers & Customers</p>
                </a>
            </li>
            <div id="suppliers" class="collapse pl-4">
                <li>
                    <a href="/suppliers">
                        <i class="fa-solid fa-shop"></i>
                        <p class="menu-sub-option">Suppliers</p>
                    </a>
                </li>

                <li>
                    <a href="/customers">
                        <i class="fa-solid fa-cash-register"></i>
                        <p class="menu-sub-option">Customers</p>
                    </a>
                </li>

            </div>
            @endif


            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->purchase))
            <li class="<?php echo $activeStatus = ($currentPage == 'purchases') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#purchaseorders">
                <a href="javascript:void(0)">
                    <p class="menu-option">Purchases</p>
                </a>
            </li>
            <div id="purchaseorders" class="collapse pl-4">
                <li>
                    <a href="/purchaseorders/create">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                        <p class="menu-sub-option">Add Purchase Order</p>
                    </a>
                </li>

                <li>
                    <a href="/purchaseorders/create-new">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                        <p class="menu-sub-option">Add Purchase(No Docs)</p>
                    </a>
                </li>

                <li>
                    <a href="/purchaseorders/open">
                        <i class="fa-solid fa-comment-dollar"></i>
                        <p class="menu-sub-option">Open Purchase Orders</p>
                    </a>
                </li>

                <li>
                    <a href="/purchaseorders/completed">
                        <i class="fa-solid fa-comments-dollar"></i>
                        <p class="menu-sub-option">Completed Purchase Orders</p>
                    </a>
                </li>

            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->expenses))
            <li class="<?php echo $activeStatus = ($currentPage == 'expenses') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#expenses">
                <a href="javascript:void(0)">
                    <p class="menu-option">Expenses</p>
                </a>
            </li>
            <div id="expenses" class="collapse pl-4">

                <li>
                    <a href="/expenses/ap-creditnote">
                        <i class="fa-solid fa-square-plus"></i>
                        <p class="menu-sub-option">Create AP Credit Note</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/view-ap-creditnote">
                        <i class="fa-solid fa-comment-dollar"></i>
                        <p class="menu-sub-option">View AP Credit Notes</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/goodsreturned-note">
                        <i class="fa-solid fa-square-plus"></i>
                        <p class="menu-sub-option">Create Goods Returned Note</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/view-goodsreturned-note">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <p class="menu-sub-option">View Goods Returned Note</p>
                    </a>
                </li>

            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->sales))
            <li class="<?php echo $activeStatus = ($currentPage == 'sales') ? 'active' : '' ; ?>" data-toggle="collapse"
                data-target="#sales">
                <a href="javascript:void(0)">
                    <p class="menu-option">Sales</p>
                </a>
            </li>
            <div id="sales" class="collapse pl-4">
                <li>
                    <a href="/sales/quotations">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <p class="menu-sub-option">Quotations</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/invoices">
                        <i class="fa-solid fa-receipt"></i>
                        <p class="menu-sub-option">Invoices</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/cash-sales">
                        <i class="fa-solid fa-cash-register"></i>
                        <p class="menu-sub-option">Cash Sales</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/credit-note">
                        <i class="fa-solid fa-square-plus"></i>
                        <p class="menu-sub-option">Create Credit Note</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/credit-note/view">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <p class="menu-sub-option">View Credit Notes</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/crm">
                        <i class="fa-solid fa-comment-dollar"></i>
                        <p class="menu-sub-option">CRM</p>
                    </a>
                </li>

            </div>
            @endif

            @if($allowedMenuPages == 'ALL')
            <li class="<?php echo $activeStatus = ($currentPage == 'accounting') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#accounts">
                <a href="javascript:void(0)">
                    <p class="menu-option">Accounts</p>
                </a>
            </li>
            <div id="accounts" class="collapse pl-4">
                
                <li>
                    <a href="/sales/customer-transactions">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">Customer Transactions</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/payments">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">View Customer Payments</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/payments/create">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">Create Customer Payment</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/bills">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">View Bills Paid</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/bills/create">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">Create Bill Payment</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/supplier-transactions">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <p class="menu-sub-option">Supplier Transactions</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/receivables">
                        <i class="fa-solid fa-coins"></i>
                        <p class="menu-sub-option">A\R Aging Report</p>
                    </a>
                </li>

                <li>
                    <a href="/sales/statements">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <p class="menu-sub-option">Statements</p>
                    </a>
                </li>

                <li>
                    <a href="/expenses/payable">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                        <p class="menu-sub-option">A/P Aging Report</p>
                    </a>
                </li>

                <li class="d-none">
                    <a href="/inventory/report" class="d-none">
                        <i class="fa-solid fa-coins"></i>
                        <p class="menu-sub-option">Inventory Item Reports</p>
                    </a>
                </li>
            </div>
            @endif

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->inventory))
            <li class="<?php echo $activeStatus = ($currentPage == 'inventory') ? 'active' : '' ; ?>"
                data-toggle="collapse" data-target="#inventory">
                <a href="javascript:void(0)">
                    <p class="menu-option">Inventory</p>
                </a>
            </li>
            <div id="inventory" class="collapse pl-4">
                <li>
                    <a>
                        <i class="fa-solid fa-circle-plus"></i>
                        <p class="menu-sub-option" id="addItem" data-toggle="collapse" data-target="#addItem">Add Item
                        </p>
                        <div id="addItem" class="collapse">
                            <div class="h-auto ml-5 pl-3 col-sm-10 d-flex flex-column">
                                <a href="/inventory/add/powder"
                                    class="menu-sub-option p-2 text-white text-decoration-none">
                                    Powder
                                </a>
                                <a href="/inventory/add/item"
                                    class="menu-sub-option p-2 text-white text-decoration-none">
                                    Aluminium/Hardware
                                </a>
                            </div>
                        </div>
                    </a>
                </li>

                <li>
                    <a href="/inventory/view">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Inventory Items</p>
                    </a>
                </li>

                <li>
                    <a href="/inventory/zero-quantity">
                        <i class="fa-brands fa-creative-commons-zero"></i>
                        <p class="menu-sub-option">Zero Quantity Items</p>
                    </a>
                </li>

                <li>
                    <a href="/stockout">
                        <i class="fa-solid fa-circle-minus"></i>
                        <p class="menu-sub-option">Stock Out</p>
                    </a>
                </li>

                <li>
                    <a href="/stockout/view-stockout">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Stock Out</p>
                    </a>
                </li>

                <li>
                    <a href="/transfers/create">
                        <i class="fa-solid fa-shuffle"></i>
                        <p class="menu-sub-option">Create Transfer</p>
                    </a>
                </li>

                <li>
                    <a href="/inventory/view-memo">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Memos</p>
                    </a>
                </li>

                <li>
                    <a href="/transfers">
                        <i class="fa-solid fa-list"></i>
                        <p class="menu-sub-option">View Transfers</p>
                    </a>
                </li>
            </div>
            @endif

            <li class="<?php echo $activeStatus = ($currentPage == 'users') ? 'active' : '' ; ?>" data-toggle="collapse"
                data-target="#settings">
                <a href="javascript:void(0)">
                    <p class="menu-option">Settings</p>
                </a>
            </li>
            <div id="settings" class="collapse pl-4">
                @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->users))
                <li>
                    <a href="/users">
                        <i class="fa-solid fa-user-plus"></i>
                        <p class="menu-sub-option">Add User</p>
                    </a>
                </li>
                @endif

                @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' &&
                $allowedMenuPages->documents))
                <li>
                    <a href="/documents">
                        <i class="fa-solid fa-file-excel"></i>
                        <p class="menu-sub-option">Document Numbering</p>
                    </a>
                </li>
                @endif

                @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->roles))
                <li>
                    <a href="/roles">
                        <i class="fa-solid fa-user-check"></i>
                        <p class="menu-sub-option">Roles</p>
                    </a>
                </li>
                @endif

                @if($allowedMenuPages == 'ALL')
                <li>
                    <a href="/vat">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <p class="menu-sub-option">VAT</p>
                    </a>
                </li>
                @endif
            </div>

            @if($allowedMenuPages == 'ALL' || (gettype($allowedMenuPages) == 'object' && $allowedMenuPages->logs))
            <li class="<?php echo $activeStatus = ($currentPage == 'logs') ? 'active' : '' ; ?>" data-toggle="collapse"
                data-target="#logs">
                <a href="javascript:void(0)">
                    <p class="menu-option">Logs</p>
                </a>
            </li>
            <div id="logs" class="collapse pl-4">
                <li>
                    <a href="/users/logs">
                        <i class="fa-solid fa-book"></i>
                        <p class="menu-sub-option">View Logs</p>
                    </a>
                </li>
            </div>
            @endif
            <li class="<?php echo $activeStatus = ($currentPage == 'contact') ? 'active' : '' ; ?>">
                <a href="/contact">
                    <p class="menu-option">Contact Us</p>
                </a>
            </li>



        </ul>
    </div>

</div>