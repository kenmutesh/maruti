@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Contact Us',
'datatable' => true,
]
)
<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/contact'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
  <div class="wrapper">
    <div class="main-panel">

      <div class="content">

        <div class="row">
    <div class="card col-sm-12">
      <p class="display-4 text-center">
        CONTACT US
      </p>
      <div class="card-header text-center">
        <img src="/assets/img/aprotec-old.png" class="w-25" alt=""/>
      </div>
      <div class="card-body">

          <form onsubmit="showSpinner(event)" enctype="multipart/form-data" method="POST" autocomplete="off" action="{{ route('send_contact_email') }}">
            @csrf
            <div class="form-group">
              <label for="email" class="visible-show">Your Email</label>
              <input type="email" name="email" class="form-control visible-show" required id="email" aria-describedby="email" placeholder="Enter email">
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="subject" id="exampleRadios1" value="Want another system" >
                    Want another system
                    <span class="form-check-sign"></span>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="subject" id="exampleRadios2" value="Problem with the system" checked>
                    Problem with this system
                    <span class="form-check-sign"></span>
                </label>
            </div>

            <div class="form-group">
              <label for="locationDescription">Your Message</label>
              <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="message" class="form-control" rows="3" required id="locationDescription"></textarea>
            </div>

            <div class="form-group">
              <label for="email" class="visible-show">Attach Files</label>
              <input type="file" style="opacity:1;position:relative;" multiple name="attachments[]" class="form-control visible-show">
            </div>

            <div class="text-center">
              <button type="submit" name="submit_btn" class="btn btn-primary display-4">
                SEND MESSAGE
              </button>
            </div>

            <div class="mt-5 text-center">
              <p>Call Us</p>
              <a href="tel:+2547 95 899 111" class="btn btn-primary" data-rel="external">
                +254 795 899 111
              </a>
            </div>
          </form>
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
  ]
  )
  @include('universal-layout.alert')
@include('universal-layout.footer')
