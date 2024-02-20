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
                        <h2>Profile</h2>
                        <ul class="breadcrumb padding-0">
                            <li class="breadcrumb-item"><a href="/dashboard"><i class="zmdi zmdi-home"></i></a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @include('universal-layout.aprotec-sidemenu', ['currentPage' => 'dashboard'])

      <div class="content">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5 class="title mb-0">Your Profile</h5>
              </div>
              <div class="card-body">
                <form>
                  <div class="row">
                    <div class="col-md-5 pr-md-1">
                      <div class="form-group">
                        <label class="visible-show">Email</label>
                        <input type="text" class="form-control visible-show" disabled placeholder="Company" value="{{ $profile->email }}">
                      </div>
                    </div>
                    <div class="col-md-3 px-md-1">
                      <div class="form-group">
                        <label class="visible-show">Username</label>
                        <input type="text" class="form-control visible-show" disabled placeholder="Username" value="{{ $profile->username }}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <p>
                        Joined: {{ $profile->created_at }}
                      </p>
                    </div>
                  </div>

                </form>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-fill btn-primary">Save</button>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-user">
              <div class="card-body">
                <p class="card-text">
                  <div class="author">
                    <div class="block block-one"></div>
                    <div class="block block-two"></div>
                    <div class="block block-three"></div>
                    <div class="block block-four"></div>
                    <a href="javascript:void(0)">
                      <img class="avatar" src="/assets/img/aprotec-ico.png" alt="Aprotec logo">
                      <h5 class="title">{{ $profile->email }}</h5>
                    </a>
                    <p class="description">
                      {{ $profile->username }}
                    </p>
                  </div>
                </p>
                <div class="card-description">
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </div>
              </div>
              <div class="card-footer text-center display-4">
                <?php echo date('Y', time()) ?>
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
