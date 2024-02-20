@include('universal-layout.header', ['pageTitle' => 'Aprotec | Set User Password'])
</head>
<body class="">
  @include('universal-layout.spinner')
<div class="authentication">
    <div class="container">
        <div class="col-md-12 content-center">
            <div class="row">
                <div class="col-lg-5 col-md-12 d-flex justify-content-center align-items-center align-content-center">
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
                            <h5>Reset password</h5>
                        </div>
                        <form class="form" onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('reset_password_token') }}">
                            @csrf
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="Enter email">
                                <span class="input-group-addon"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                            <button type="submit" name="submit_btn" class="btn btn-warning rounded-pill display-4 w-100">
                                PROCEED
                            </button>        
                            <div class="row m-0 m-sm-2">
                            </div>                   
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('universal-layout.scripts',[
  'jquery' => true  
])
@include('universal-layout.alert')
@include('universal-layout.footer')
