@include('universal-layout.header', ['pageTitle' => 'Aprotec | Dashboard'])
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
          </div>
        </div>
      </div>

      @include('universal-layout.aprotec-sidemenu', ['currentPage' => 'dashboard'])

      <div class="content">

        @include('universal-layout.alert')

        <div class="row">
          <div class="col-lg-6">
            <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3" data-toggle="modal" data-target="#createCompanyForm">
              <i class="tim-icons icon-simple-add"></i> CREATE A COMPANY
            </button>

            <!-- Modal -->
            <div class="modal fade" id="createCompanyForm" tabindex="-1" role="dialog" aria-labelledby="createCompanyForml" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create A Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                      <i class="tim-icons icon-simple-remove"></i>
                    </button>
                  </div>
                  <div class="modal-body">

                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('add_company') }}">
                      @csrf
                      <div class="row">

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" required name="email" class="form-control" id="email" aria-describedby="email" class="dark-text" placeholder="Enter email">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="companyName">Company Name</label>
                            <input type="text" required name="company_name" class="form-control" id="companyName" aria-describedby="companyName" placeholder="Enter company name">
                          </div>
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="adminName">Admin Name(Optional)</label>
                            <input type="text" name="admin_name" class="form-control" id="adminName" aria-describedby="adminName" placeholder="Enter admin name">
                            <small class="font-10 text-warning">*Defaults to the company name</small>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="activationKey">
                              Activation Key
                            </label>
                            <input type="text" required name="activation_key" readonly class="form-control visible-show" id="activationKey" placeholder="Activation Key">
                            <small class="btn btn-default p-1 w-100" onclick="generateActivationKey()">Generate Another Key</small>
                          </div>
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="subscriptionStatus">Subscription Status</label>
                            <select class="ms form-control" name="subscription_status" id="subscriptionStatus" required>
                              @forelse($subscriptionStatus as $subscription)
                                <option value="{{ $subscription->value }}">{{ $subscription->humanreadablestring() }}</option>
                                  @empty
                                  <option disabled selected>No subscription status to choose from</option>
                              @endforelse
                            </select>
                          </div>

                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="validUntil">
                              Subscription Valid For
                            </label>
                            <input type="number" name="subscription_duration" required placeholder="Enter number" class="form-control">
                          </div>
                          <div class="input-group justify-content-around">
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" name="subscription_nature" checked class="form-check-input" value="Days">Days
                              </label>
                            </div>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" name="subscription_nature" class="form-check-input" value="Months">Months
                              </label>
                            </div>
                            <div class="form-check disabled">
                              <label class="form-check-label">
                                <input type="radio" name="subscription_nature" class="form-check-input" value="Years">Years
                              </label>
                            </div>
                          </div>
                        </div>

                      </div>


                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                    <button type="submit" name="submit_btn" value="Create Company" class="btn btn-primary">CREATE COMPANY</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <a href="/aprotec/profile" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3">
              <i class="tim-icons icon-pencil"></i> EDIT YOUR PROFILE
            </a>
          </div>
        </div>

        <div class="row">

          <div class="col-sm-6">
            <div class="card card-chart">
              <div class="card-header">
                <h5 class="card-category">Companies Created</h5>
                <small class="card-title"><i class="tim-icons icon-bank text-primary"></i> All time number of companies</small>
              </div>
              <div class="card-body">
                <p class="display-1 mt-0 mr-0 mb-0 ml-2">
                  <?php echo count($companies) ?>
                </p>
              </div>
              <div class="card-footer text-right m-2 p-0">
              </div>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="card card-chart">
              <div class="card-header">
                <h5 class="card-category">Your Companies</h5>
                <small class="card-title"><i class="tim-icons icon-bank text-info"></i> Companies created by you</small>
              </div>
              <div class="card-body">
                <p class="display-1 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $currentUserCompanies = array_filter(json_decode($companies), function ($company) {
                    return $company->created_by == session()->get('auth_aprotec_uid');
                  });
                  echo count($currentUserCompanies);
                  ?>
                </p>
              </div>
              <div class="card-footer text-right m-2 p-0">
              </div>
            </div>
          </div>
        </div>
        <div class="row">

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Incomplete</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $companiesArray = $companies->toArray();
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::INCOMPLETE->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Incomplete Expired</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::INCOMPLETE_EXPIRED->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Active</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::ACTIVE->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Trial</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::TRIAL->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Past Due</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::PAST_DUE->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Unpaid</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::UNPAID->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Cancelled</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter($companiesArray, function ($company) use ($subscriptionStatusEnum) {
                    return $company['subscription_status'] == $subscriptionStatusEnum::CANCELLED->value;
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="card card-chart">
              <div class="card-header p-0">
                <h5 class="card-category mb-0">Trial</h5>
              </div>
              <div class="card-body">
                <p class="display-3 mt-0 mr-0 mb-0 ml-2">
                  <?php
                  $statusSubscriptions = array_filter(json_decode($companies), function ($company) {
                    return $company->subscription_status == 'Trial';
                  });
                  echo count($statusSubscriptions);
                  ?>
                </p>
              </div>
            </div>
          </div>


        </div>
        <div class="row">
          <div class="col-12">
            <div class="card card-tasks">
              <div class="card-header p-0">
                <h6 class="title d-inline">
                  Companies
                </h6>
              </div>
              <div class="card-body p-0">
                <div class="table-full-width table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <th class="p-0">Created</th>
                      <th class="p-0">Name</th>
                      <th class="p-0">Email</th>
                      <th class="p-0">Subscription<br />status</th>
                      <th class="p-0">Subscription<br />Expiry</th>
                      <th class="p-0"></th>
                    </thead>
                    <tbody>
                      <?php
                      if (count($companies) > 0) {
                        foreach ($companies as $company) {
                      ?>
                          <tr>
                            <td class="p-0">
                              {{ date('d-M-Y h:i:sa', strtotime($company->created_at)); }}
                            </td>
                            <td class="p-0">
                              {{ $company->name }}
                            </td>
                            <td class="p-0">
                              {{ $company->email }}
                            </td>
                            <td class="td-actions p-0">
                              <span>
                                {{ ($company->subscription_status->humanreadablestring()) }}
                              </span>
                            </td>
                            <td class="p-0">
                              {{ date('d-M-Y h:i:sa', strtotime($company->subscription_expiry_date)); }}
                            </td>
                            <td class="p-0">
                              <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editCompanyForm{{ $company->id }}">
                                Edit Company
                              </button>
                              <div class="modal fade" id="editCompanyForm{{ $company->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        <i class="tim-icons icon-simple-remove"></i>
                                      </button>
                                    </div>
                                    <div class="modal-body">

                                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('edit_company', $company->id) }}">
                                        @csrf
                                        @method("PUT")
                                        <div class="row">

                                          <div class="col-sm-6">
                                            <div class="form-group">
                                              <label for="email">Email address</label>
                                              <input type="email" value="{{ $company->email }}" required name="email" class="form-control" id="email" aria-describedby="email" class="dark-text" placeholder="Enter email">
                                            </div>
                                          </div>

                                          <div class="col-sm-6">
                                            <div class="form-group">
                                              <label for="companyName">Company Name</label>
                                              <input type="text" required value="{{ $company->name }}" name="company_name" class="form-control" id="companyName" aria-describedby="companyName" placeholder="Enter company name">
                                            </div>
                                          </div>

                                        </div>

                                        <div class="row">

                                          <div class="col-sm-6">

                                            <div class="form-group">
                                              <label for="subscriptionStatus">Subscription Status</label>
                                              <select class="ms form-control" name="subscription_status" id="subscriptionStatus" required>
                                                @forelse($subscriptionStatus as $subscription)
                                                @if($subscription->value === $company->subscription_status)
                                                <option value="{{ $subscription->value }}" selected>{{ $subscription->humanreadablestring() }} - (CURRENT)</option>
                                                @else
                                                <option value="{{ $subscription->value }}">{{ $subscription->humanreadablestring() }}</option>
                                                @endif
                                                @empty
                                                <option disabled selected>No subscription status to choose from</option>
                                                @endforelse
                                              </select>
                                            </div>

                                          </div>

                                          <div class="col-sm-6">
                                            <div class="form-group">
                                              <label for="validUntil">
                                                Subscription Valid For
                                              </label>
                                              <input type="number" name="subscription_duration" placeholder="Enter number" class="form-control">
                                            </div>
                                            <div class="input-group justify-content-around">
                                              <div class="form-check">
                                                <label class="form-check-label">
                                                  <input type="radio" name="subscription_nature" checked class="form-check-input" value="Days">Days
                                                </label>
                                              </div>
                                              <div class="form-check">
                                                <label class="form-check-label">
                                                  <input type="radio" name="subscription_nature" class="form-check-input" value="Months">Months
                                                </label>
                                              </div>
                                              <div class="form-check disabled">
                                                <label class="form-check-label">
                                                  <input type="radio" name="subscription_nature" class="form-check-input" value="Years">Years
                                                </label>
                                              </div>
                                            </div>
                                          </div>

                                        </div>


                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                      <button type="submit" name="submit_btn" value="Edit Company" class="btn btn-primary">EDIT COMPANY</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCompanyForm{{ $company->id }}">
                                Delete Company
                              </button>
                              <div class="modal fade" id="deleteCompanyForm{{ $company->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        <i class="tim-icons icon-simple-remove"></i>
                                      </button>
                                    </div>
                                    <div class="modal-body">

                                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('delete_company', $company->id) }}">
                                        @csrf
                                        @method("DELETE")
                                        <div class="alert alert-danger">
                                          Are you sure you want to delete the company?
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                      <button type="submit" name="submit_btn" value="Delete Company" class="btn btn-primary">DELETE COMPANY</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                        <?php
                        }
                      } else {
                        ?>
                        <tr>
                          <td colspan="100%" class="text-center">
                            No recently registered companies
                          </td>
                        </tr>
                      <?php
                      }
                      ?>

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