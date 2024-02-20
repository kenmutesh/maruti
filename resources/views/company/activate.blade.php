@include('universal-layout.header', ['pageTitle' => 'Aprotec | Company Activation'])

</head>
<body class="theme-green">
  @include('universal-layout.spinner')
  <div class="container-fluid d-flex align-items-center justify-content-center">
    <div class="card col-sm-4 mt-3 border p-0">
      <div class="card-header text-center">
        <img src="../assets/img/aprotec.png" class="w-50" alt=""/>
      </div>
    <div class="card-body">
        <p class="text-center">
          COMPANY ACCOUNT ACTIVATION
        </p>
        @if(count($errors->all()) > 0)
              <div class="alert alert-danger alert-dismissible fade show">
                @foreach ($errors->all() as $error)
                <strong>{{ $error }}</strong><br />
                @endforeach
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('activate_company') }}">
          @csrf
          <div class="form-group">
            <label for="email" class="visible-show">Email address</label>
            <input type="email" name="email" class="form-control visible-show" required id="email" aria-describedby="email" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="activationKey" class="visible-show">Activation Key</label>
            <input type="password" name="activation_key" class="form-control visible-show" required id="activationKey" placeholder="Activation key">
          </div>

          <div class="form-group">
            <label for="adminPassword" class="visible-show">Administrator Password</label>
            <input type="password" name="password" class="form-control visible-show" required id="adminPassword" placeholder="Password">
          </div>

          <div class="form-group">
            <label for="confAdminPassword" class="visible-show">Confirm Administrator Password</label>
            <input type="password" name="conf_password" class="form-control visible-show" required id="confAdminPassword" placeholder="Confirm password">
          </div>

          <div class="text-center">
            <button type="submit" name="submit_btn" class="btn btn-primary display-4">
              ACTIVATE
            </button>
          </div>
        </form>
    </div>
    <div class="card-footer text-center">
      <h2 class="nav-item">
          APROTEC SYSTEMS
      </h2>
    </div>

    </div>
  </div>

@include('universal-layout.scripts',[
  'jquery' => true  
])
@include('universal-layout.alert')
@include('universal-layout.footer')
