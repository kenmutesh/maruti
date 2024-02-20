@include('universal-layout.header', ['pageTitle' => 'Log In - Aprotec Administration Side'])

<body class="theme-black">
@include('universal-layout.spinner')
<div class="authentication">
    <div class="container">
        <div class="col-md-12 content-center">
            <div class="row">
                <div class="col-lg-6 col-md-12 d-flex justify-content-center align-items-center align-content-center">
                    <div class="company_detail">
                        <div>
                          <img src="/assets/img/aprotec-old.png" alt="Maruti Glazer Logo">
                        </div>
                    </div>                    
                </div>
                <div class="col-lg-5 col-md-12 offset-lg-1">
                    <div class="card-plain" style="max-width: none;">
                        <img src="/assets/img/aprotec.png" alt="" class="col-11 mx-auto shadow-0">
                        <div class="header">
                            <h5>Log in</h5>
                        </div>
                        <form class="form" onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('aprotec_login') }}">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="email_uname" class="form-control" placeholder="User Name">
                                <span class="input-group-addon"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                            <div class="input-group">
                                <input type="password" name="password" placeholder="Password" class="form-control" />
                                <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                            </div>
                            <button type="submit" name="submit_btn" class="btn btn-warning rounded-pill display-4 w-100">
                                LOG IN APROTEC ADMIN SIDE
                            </button>        
                            <div class="row mt-2">
                            <div class="form-check col-6">
                                <label class="form-check-label visible-show">
                                    <input class="form-check-input" name="remember_me" type="checkbox" value="Yes">
                                    Remember Me
                                    <span class="form-check-sign">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>

                            </div>                   
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-auto bg-white position-absolute w-100" style="top: 0px;">
        <div class="text-center">
            <span>Made by</span>
            <img src="/assets/img/aprotec-ico.png" alt="Aprotec Icon" class="img-fluid" style="height: 3rem;width: auto;">
        </div>
    </div>
</div>
@include('universal-layout.scripts',
    [
        'jquery' => true
    ]
)
@include('universal-layout.alert')
@include('universal-layout.footer')
