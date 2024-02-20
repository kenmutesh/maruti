@include('universal-layout.header',
[
'pageTitle' => 'Dashboard',
'morris' => true,
'jvectormap' => true,
]
)
</head>

<body class="theme-green">
    <!-- Page Loader -->
    @include('universal-layout.spinner')

    @include('universal-layout.system-sidemenu',
    [
    'slug' => '/'
    ]
    )
    <!-- Main Content -->
    <section class="content home">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row clearfix">
                    <div class="col-12 d-flex">
                        <div class="col-6">
                            <h2>Dashboard</h2>
                            <ul class="breadcrumb padding-0">
                                <li class="breadcrumb-item"><a href="/dashboard"><i class="zmdi zmdi-home"></i></a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ul>
                        </div>
                        <div class="col-6 text-right">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row clearfix justify-content-between">
                @if(auth()->user()->role->name === 'ADMIN')
                <div class="col-lg-6 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Monthly invoice totals for current and past year</h2>
                        </div>
                        <div class="body m-b-10">
                            <canvas id="invoiceYearChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Monthly cash sale totals current and past year</h2>
                        </div>
                        <div class="body m-b-10">
                            <canvas id="cashSaleYearChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>User job card creation ratio for <?php echo date('Y', time()) ?></h2>
                        </div>
                        <div class="body m-b-10">
                            <canvas id="creationRatio"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Customers with most job cards for <?php echo date('Y', time()) ?></h2>
                        </div>
                        <div class="body m-b-10 table-responsive pb-0">
                            <table class="table table-bordered table-hover border-top">
                                <thead>
                                    <th class="p-0">Customer Name</th>
                                    <th class="p-0">Job Cards</th>
                                </thead>
                                <tbody>
                                    @foreach($customerJobCardDistribution as $customerJobCards)
                                    <tr>
                                        <td class="p-0">{{ $customerJobCards->customer->customer_name }}</td>
                                        <td class="p-0">{{ $customerJobCards->total_number }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Powder with the most job cards for <?php echo date('Y', time()) ?></h2>
                        </div>
                        <div class="body m-b-10 table-responsive pb-0">
                            <table class="table table-bordered table-hover border-top">
                                <thead>
                                    <th class="p-0">Powder</th>
                                    <th class="p-0">Job Cards</th>
                                </thead>
                                <tbody>
                                    @foreach($powderJobCardDistribution as $powderJobCards)
                                    <tr>
                                        <td class="p-0">{{ $powderJobCards->powder->powder_color }}</td>
                                        <td class="p-0">{{ $powderJobCards->total_number }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Most requested hardware for <?php echo date('Y', time()) ?></h2>
                        </div>
                        <div class="body m-b-10 table-responsive pb-0">
                            <table class="table table-bordered table-hover border-top">
                                <thead>
                                    <th class="p-0">Hardware</th>
                                    <th class="p-0">No. of times</th>
                                </thead>
                                <tbody>
                                    @foreach($inventoryItemMostSold as $hardwareItem)
                                    @if($hardwareItem->inventoryitem->type == App\Enums\InventoryItemsEnum::HARDWARE)
                                    <tr>
                                        <td class="p-0">{{ $hardwareItem->inventoryitem->item_name }}</td>
                                        <td class="p-0">{{ $hardwareItem->total_number }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Most requested aluminium <?php echo date('Y', time()) ?></h2>
                        </div>
                        <div class="body m-b-10 table-responsive pb-0">
                            <table class="table table-bordered table-hover border-top">
                                <thead>
                                    <th class="p-0">Aluminium</th>
                                    <th class="p-0">No. of times</th>
                                </thead>
                                <tbody>
                                    @foreach($inventoryItemMostSold as $aluminiumItem)
                                    @if($aluminiumItem->inventoryitem->type == App\Enums\InventoryItemsEnum::ALUMINIUM) 
                                    <tr>
                                        <td class="p-0">{{ $aluminiumItem->inventoryitem->item_name }}</td>
                                        <td class="p-0">{{ $aluminiumItem->total_number }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else

                <div class="col-lg-4 col-md-6">
                    <div class="card text-center">
                        <div class="body">
                            <p class="m-b-20"><i class="zmdi zmdi-balance zmdi-hc-3x col-amber"></i></p>
                            <span class="font-weight-bold">Job Cards You've Created for <?php echo date('F', time()) ?></span>
                            @if(isset($coatingJobs[0]))
                            <h3 class="m-b-10"><span class="number count-to" data-from="0" data-to="{{ $coatingJobs[0]->amount + $coatingJobs[0]->amount }}" data-speed="2000" data-fresh-interval="700">
                                    {{ $coatingJobs[0]->amount + $coatingJobs[0]->amount }}
                                </span></h3>
                            @else
                            <h3 class="m-b-10"><span class="number count-to" data-from="0" data-to="0" data-speed="2000" data-fresh-interval="700">
                                    0
                                </span></h3>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card">
                        <div class="body text-center">
                            <span class="font-weight-bold">Your billed and unbilled job cards for <?php echo date('F', time()) ?></span>
                            <div id="billingCards" style="height: 7em"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="row clearfix">
                @if(auth()->user()->role->name === 'ADMIN')
                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Invoices Trend Latest 7 days Statistics</h2>
                        </div>
                        <div class="body m-b-10">
                            <div id="invoiceChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Cash Sales Trend Latest 7 days Statistics</h2>
                        </div>
                        <div class="body m-b-10">
                            <div id="cashSaleChart"></div>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-lg-12 col-md-12">
                    <div class="card visitors-map">
                        <div class="header">
                            <h2>Your job card making trend for the past 7 days</h2>
                        </div>
                        <div class="body m-b-10">
                            <div id="jobCardChart"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @include('universal-layout.scripts',
    [
    'libscripts' => true,
    'mainscripts' => true,
    'morrisscripts' => true,
    'charts' => true,
    'index' => true
    ]
    )
    @if(auth()->user()->role->name === 'ADMIN')
    <script>
        const pastYearInvoice = [];
        const currentYearInvoice = [];
    </script>
    <?php
    $pastYear = date('Y', time()) - 1;
    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    for ($i = 0; $i < count($months); $i++) {
        foreach ($invoiceYearTrend as $invoiceData) {
            if ($invoiceData->month_name == $months[$i]) {
                if ($invoiceData->associated_year == $pastYear) {
    ?>
                    <script>
                        pastYearInvoice[<?php echo $i ?>] = <?php echo $invoiceData->total_amount ?>;
                    </script>
                <?php
                } else if ($invoiceData->associated_year == ($pastYear + 1)) {
                ?>
                    <script>
                        currentYearInvoice[<?php echo $i ?>] = <?php echo $invoiceData->total_amount ?>;
                    </script>
    <?php
                }
            }
        }
    }
    ?>

    <script>
        const pastYearCashSale = [];
        const currentYearCashSale = [];
    </script>
    <?php
    $pastYear = date('Y', time()) - 1;
    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    for ($i = 0; $i < count($months); $i++) {
        foreach ($cashSaleYearTrend as $cashSaleData) {
            if ($cashSaleData->month_name == $months[$i]) {
                if ($cashSaleData->associated_year == $pastYear) {
    ?>
                    <script>
                        pastYearCashSale[<?php echo $i ?>] = <?php echo $cashSaleData->total_amount ?>;
                    </script>
                <?php
                } else if ($cashSaleData->associated_year == ($pastYear + 1)) {
                ?>
                    <script>
                        currentYearCashSale[<?php echo $i ?>] = <?php echo $cashSaleData->total_amount ?>;
                    </script>
    <?php
                }
            }
        }
    }
    ?>

    <script>
        const monthLabels = ['Jan', 'Feb', 'Mar', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        const configYearInvoiceChart = {
            type: 'line',
            data: {

                labels: monthLabels,
                datasets: [{
                        label: '<?php echo $pastYear ?>',
                        data: pastYearInvoice,
                        fill: true,
                        borderColor: '#ff6000',
                        tension: 0.1
                    },
                    {
                        label: '<?php echo $pastYear + 1 ?>',
                        data: currentYearInvoice,
                        fill: true,
                        borderColor: '#00ff00',
                        tension: 0.1
                    }
                ]
            }
        };
        new Chart(document.querySelector('#invoiceYearChart'), configYearInvoiceChart);

        const configYearCashSaleChart = {
            type: 'line',
            data: {

                labels: monthLabels,
                datasets: [{
                        label: '<?php echo $pastYear ?>',
                        data: pastYearCashSale,
                        fill: true,
                        borderColor: '#ff6000',
                        tension: 0.1
                    },
                    {
                        label: '<?php echo $pastYear + 1 ?>',
                        data: currentYearCashSale,
                        fill: true,
                        borderColor: '#00ff00',
                        tension: 0.1
                    }
                ]
            }
        };
        new Chart(document.querySelector('#cashSaleYearChart'), configYearCashSaleChart);
    </script>

    <script>
        const invoiceData = [];
    </script>
    <?php
    foreach ($invoiceDaily as $invoices) {
    ?>
        <script>
            invoiceData.push({
                period: '<?php echo $invoices['single_date'] ?>',
                Amount: <?php echo $invoices['sum'] ?>
            })
        </script>
    <?php
    }
    ?>
    <script>
        Morris.Area({
            element: 'invoiceChart',
            data: invoiceData,
            xkey: 'period',
            ykeys: ['Amount'],
            labels: ['Amount'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors: ['#13ed02'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 2,
            hideHover: 'auto',
            lineColors: ['#ff6600'],
            resize: true
        });
    </script>
    <script>
        const cashSaleData = [];
    </script>
    <?php
    foreach ($cashSaleDaily as $cashsale) {
    ?>
        <script>
            cashSaleData.push({
                period: '<?php echo $cashsale['single_date'] ?>',
                Amount: <?php echo $cashsale['sum'] ?>
            })
        </script>
    <?php
    }
    ?>
    <script>
        Morris.Area({
            element: 'cashSaleChart',
            data: cashSaleData,
            xkey: 'period',
            ykeys: ['Amount'],
            labels: ['Amount'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors: ['#ff6600'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 2,
            hideHover: 'auto',
            lineColors: ['#13ed02'],
            resize: true
        });
        const jobCardCreationRatio = {};
        jobCardCreationRatio.labels = [];
        jobCardCreationRatio.datasets = [{
            label: 'Job card user distribution',
            data: [],
            backgroundColor: [],
            hoverOffset: 4
        }]
    </script>
    <?php
    foreach ($userJobCardDistribution as $jobCard) {
    ?>
        <script>
            jobCardCreationRatio.labels.push('<?php echo $jobCard->createdBy->username ?>');
            jobCardCreationRatio.datasets[0].data.push('<?php echo $jobCard->total_number ?>');
            var number = Math.random() * 100;
            if (number > 50) {
                jobCardCreationRatio.datasets[0].backgroundColor.push(`hsl(23, ${number.toFixed(0)}%, 50%)`);
            } else {
                jobCardCreationRatio.datasets[0].backgroundColor.push(`hsl(120, ${number.toFixed(0)}%, 50%)`);
            }
        </script>
    <?php
    }
    ?>
    <script>
        const configJobCardDistribution = {
            type: 'doughnut',
            data: jobCardCreationRatio
        };
        new Chart(document.querySelector('#creationRatio'), configJobCardDistribution);
    </script>
    <script>
        const jobCardData = [];
    </script>
    <?php
    foreach ($coatingJobsDaily as $jobCard) {
    ?>
        <script>
            jobCardData.push({
                period: '<?php echo $jobCard['single_date'] ?>',
                Amount: <?php echo $jobCard['count'] ?>
            })
        </script>
    <?php
    }
    ?>
    <script>
        Morris.Area({
            element: 'jobCardChart',
            data: jobCardData,
            xkey: 'period',
            ykeys: ['Amount'],
            labels: ['Amount'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors: ['#13ed02'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 2,
            hideHover: 'auto',
            lineColors: ['#ff6600'],
            resize: true
        });
    </script>
    @else

    <script>
        const billingCardsInfo = [];
    </script>
    <?php
    foreach ($coatingJobs as $jobCard) {
        $status = 'BILLED';
        if ($jobCard->status == 'OPEN') {
            $status = 'UNBILLED';
        }
    ?>
        <script>
            billingCardsInfo.push({
                label: '<?php echo $status ?>',
                value: <?php echo $jobCard->amount ?>
            })
        </script>
    <?php
    }
    ?>
    <script>
        Morris.Donut({
            element: 'billingCards',
            data: billingCardsInfo,
            resize: true,
            colors: ['#ff6000', '#00ff00']
        });
    </script>
    @endif
    @include('universal-layout.alert')
    @include('universal-layout.footer')