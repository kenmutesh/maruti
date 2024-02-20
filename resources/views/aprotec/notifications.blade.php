@include('universal-layout.header', ['pageTitle' => 'Aprotec | Notifications'])
</head>
<body class="theme-green">
  @include('universal-layout.spinner')
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
                         @include('universal-layout.aprotec-notifications')
                    </div>
                </div>
            </div>
        </div>

        @include('universal-layout.aprotec-sidemenu', ['currentPage' => 'dashboard'])
        <div class="content">

        <div class="row">

          <div class="col-md-12">
            <div class="card  card-plain">
              <div class="card-header">
                <h4 class="card-title">All Notifications</h4>
                <p class="category">List of notifications registered in the system</p>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table tablesorter data-table" id="">
                    <thead class="text-primary">
                      <tr>
                        <th>
                          Message
                        </th>
                        <th>
                          Read Date
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $singleNotification)
                          <tr>
                            <td class="d-flex flex-column text-left">
                              {{ $singleNotification->message }}
                            </td>

                            <td class="text-left">
                              {{ $singleNotification->updated_at }}
                            </td>

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
  </section>

@include('universal-layout.scripts',
    [
        'libscripts' => true,
        'vendorscripts' => true,
        'mainscripts' => true,
        'index' => true,
        'universal' => true
    ]
)
@include('universal-layout.alert')
@include('universal-layout.footer')
