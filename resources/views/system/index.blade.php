@include('universal-layout.header', ['pageTitle' => 'Log In'])

<body class="theme-black">
    @include('universal-layout.spinner')
    <div class="authentication">
        <div class="container">
            <div class="col-md-12 content-center">
                <div class="row">
                    <div class="col-lg-5 col-md-12 mt-4 mt-sm-0 d-flex justify-content-center align-items-center align-content-center">
                        <div class="company_detail">
                            <div>
                                <img src="/assets/img/maruti.png" alt="Maruti Glazer Logo">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 offset-lg-1">
                        <div class="card-plain" style="max-width: none;">
                            <img src="/assets/img/aprotec.png" alt="" class="col-11 mx-auto shadow-0">
                            <hr>
                            <div class="header">
                                <h5>Log in</h5>
                            </div>
                            <form class="form" onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('login_user') }}">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Email" name="u_email">
                                    <span class="input-group-addon"><i class="zmdi zmdi-account-circle"></i></span>
                                </div>
                                <div class="input-group">
                                    <input type="password" name="password" placeholder="Password" class="form-control" />
                                    <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                                </div>
                                <button type="submit" name="submit_btn" class="btn btn-warning rounded-pill display-4 w-100">
                                    LOG IN
                                </button>
                                <div class="row m-0 m-sm-2">
                                    <div class="form-check col-6">
                                        <label class="form-check-label visible-show">
                                            <input class="form-check-input" name="remember_me" type="checkbox" value="Yes">
                                            Remember Me
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-6 mt-1 text-right">
                                        <a data-toggle="modal" href="#forgotPassword">Forgot Password</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="forgotPassword">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Forgot Password</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form method="post" onsubmit="showSpinner(event)" action="{{ route('forgot_password') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" required name="email" />
                            </div>
                            <button type="submit" name="submit_btn" class="btn btn-warning rounded-pill display-4 w-100">
                                SEND RESET EMAIL
                            </button>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
        <div class="mb-auto bg-white position-absolute w-100" style="top: 0px;">
            <div class="text-center text-dark">
                <span>Made by</span>
                <img src="/assets/img/aprotec-ico.png" alt="Aprotec Icon" class="img-fluid" style="height: 3rem;width: auto;">
            </div>
        </div>
    </div>
    @include('universal-layout.scripts',
    [
    'mainscripts' => true,
    'vendorscripts' => true,
    'libscripts' => true,
    ]
    )
    @include('universal-layout.alert')
    @include('universal-layout.footer')