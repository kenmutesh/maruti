@include('system.layout.header', ['pageTitle' => 'Aprotec | User Account Activation'])

<body class="">
  @include('system.layout.spinner')
  <div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="card col-sm-6">
      <div class="card-header text-center">
        <img src="../assets/img/aprotec.png" class="w-50" alt=""/>
      </div>
    <div class="card-body">
        <p class="display-4 text-center">
          USER ACCOUNT ACTIVATION
        </p>

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('activate_user') }}">
          @csrf
          <div class="form-group">
            <label for="email" class="visible-show">Email address</label>
            <input type="email" name="email" class="form-control visible-show" required id="email" aria-describedby="email" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="activationKey" class="visible-show">Enter Token</label>
            <input type="password" name="token" class="form-control visible-show" required id="activationKey" placeholder="Token">
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

@include('system.layout.footer')
