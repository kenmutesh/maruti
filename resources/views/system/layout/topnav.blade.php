<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <div class="navbar-toggle d-inline">
        <button type="button" class="navbar-toggler">
          <span class="navbar-toggler-bar bar1"></span>
          <span class="navbar-toggler-bar bar2"></span>
          <span class="navbar-toggler-bar bar3"></span>
        </button>
      </div>
      <p class="breadcrumb-paragraph">
        {{ $breadcrumb }}
      </p>
    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
    </button>
    <div class="collapse navbar-collapse" id="navigation">
      <ul class="navbar-nav ml-auto">
        <!-- <li class="search-bar input-group">
          <button class="btn btn-link" id="search-button" data-toggle="modal" data-target="#searchModal"><i class="tim-icons icon-zoom-split" ></i>
            <span class="d-lg-none d-md-block">Search</span>
          </button>
        </li> -->
        <li class="dropdown nav-item" onclick="readNotifications({{ Session::get('auth_aprotec_uid') }})">
          <a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
            <div id="notificationBubble" class="notification d-none"></div>
            <i class="tim-icons icon-bell-55"></i>
            <p class="d-lg-none">
              Notifications
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-navbar overflow-auto" style="height: 200px;">
            <ul id="notificationDropdown" class="p-0">
              <li class="text-center d-flex align-items-center bg-primary">
                No Notifications
              </li>
            </ul>
            <a href="/users/notifications" target="_blank" class="btn btn-primary position-relative mt-1" style="bottom:0px;">
              SEE ALL
            </a>
          </div>

        </li>
        <li class="dropdown nav-item">
          <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
            <div class="photo">
              <img src="/assets/img/maruti-square.png" alt="Profile Photo">

            </div>
            <b class="caret d-none d-lg-block d-xl-block"></b>
            <p class="d-lg-none">
              Log out
            </p>
          </a>
          <ul class="dropdown-menu dropdown-navbar col-sm-4 overflow-auto">
            <li class="nav-link">
              <a href="javascript:void(0)" class="nav-item dropdown-item">
                Profile
                <p class="mw-100 text-truncate">
                  {{ Session::get('auth_uname_uid') }} <br> {{ Session::get('auth_role_uid') }}
                </p>
              </a>
            </li>
            <li class="dropdown-divider"></li>
            <li class="nav-link"><a href="/logout" class="nav-item dropdown-item">Log out</a></li>
          </ul>
        </li>
        <li class="separator d-lg-none"></li>
      </ul>
    </div>
  </div>
</nav>

<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="SEARCH">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i class="tim-icons icon-simple-remove"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- End Navbar -->
