@include('universal-layout.header', ['pageTitle' => '403 | Forbidden'])

<body class="">
  <div class="container-fluid m-0 p-0 vh-100 d-flex align-items-center justify-content-center">
    <div class="card col-12 m-0 p-0">
      <div class="card-header text-center">
        <img src="../assets/img/aprotec.png" class="w-25" alt="">
      </div>
      <div class="card-body text-center">
        <p class="display-1">
          403
        </p>
        <p> Requested Page Is Forbidden </p>
      </div>
      <h2 class="card-footer text-center nav-item">
        <a href="http://www.aprotecsystem.com">www.aprotecsystem.com</a>
      </h2>
    </div>
  </div>
  @include('universal-layout.scripts')
  @include('universal-layout.footer')