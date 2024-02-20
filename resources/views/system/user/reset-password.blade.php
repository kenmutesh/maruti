@include('universal-layout.header', ['pageTitle' => 'Aprotec | Set User Password'])
</head>
<body class="theme-green">
  @include('universal-layout.spinner')
  <div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="card col-sm-8 p-0 border">
      <div class="card-header text-center">
        <img src="../assets/img/aprotec.png" class="w-50" alt=""/>
      </div>
    <div class="card-body">
        <p class="display-4 text-center">
          SET PASSWORD
        </p>

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('set_new_password_user') }}">
          @csrf
          <input type="hidden" name="user_id" value="{{ $user->id }}">
          <div class="form-group">
            <label for="password" class="visible-show">Email</label>
            <input type="email" readonly name="email" class="form-control visible-show" required id="password" placeholder="Password" value="{{ $user->email }}">
          </div>

          <div class="form-group">
            <label for="password" class="visible-show">New Password</label>
            <input type="password" name="password" class="form-control visible-show" required id="password" placeholder="Password">
          </div>

          <div class="text-center">
            <button type="submit" name="submit_btn" value="Set Password" class="btn btn-primary display-4">
              PROCEED
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
