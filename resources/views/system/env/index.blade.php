@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Document Labels',
]
)
<style>
    .modal-backdrop.show {
        z-index: 4;
    }
</style>

<body class="theme-green">
    @include('universal-layout.spinner')

    @include('universal-layout.system-sidemenu',
    [
    'slug' => '/settings'
    ]
    )
    <section class="content home">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="main-panel">
                    <div class="content">
                        <div class="col">
                            <div class="card card-plain">
                                <div class="card-header">
                                    <h4 class="card-title p-0 m-0">Email Credentials</h4>
                                </div>
                                <div class="card-body p-0">
                                    <form action="/email-secrets" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email Username</label>
                                            <input type="text" class="form-control" name="PHPMAILER_EMAIL_USERNAME" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $data['user_name'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email Password</label>
                                            <input type="text" class="form-control" name="PHPMAILER_EMAIL_PASSWORD" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $data['password'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email Host</label>
                                            <input type="text" class="form-control" name="PHPMAILER_EMAIL_HOST" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $data['host'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email Security Standard</label>
                                            <input type="text" class="form-control" name="PHPMAILER_SMTP_SECURE_OPTION" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $data['secure_option'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email Port</label>
                                            <input type="text" class="form-control" name="PHPMAILER_EMAIL_PORT" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $data['email_port'] ?>">
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                @include('universal-layout.scripts',
                [
                'libscripts' => true,
                'vendorscripts' => true,
                'mainscripts' => true,

                ]
                )
                @include('universal-layout.alert')
                @include('universal-layout.footer')