<!-- This file has all the optional scripts passed to a page screen just before the </body> tag -->
@if(isset($coating))
<script src="/assets/js/custom/coating.min.js"></script>
@endif

@if(isset($jquery))
<script src="/assets/plugins/jquery/jquery-v3.3.1.min.js"></script>
@endif

@if(isset($aprotecNotifications))
<script>
  const notificationBubble = document.querySelector('#notificationBubble');
  const notificationDropdown = document.querySelector('#notificationDropdown');

  function readNotifications(sessionID) {
    if (!notificationBubble) {
      return;
    }
    fetch("/aprotec/clear-notifications/{{ Session::get('auth_aprotec_uid') }}")
      .then((response) => response.json())
      .then((json) => {
        console.log('Notifications cleared');
      })
  }
  // query for notifications
  setInterval(() => {
    if (!notificationBubble) {
      return;
    }
    notificationBubble.className = '';
    fetch("/aprotec/notifications/{{ Session::get('auth_aprotec_uid') }}")
      .then((response) => response.json())
      .then((json) => {

        let unreadMessages = false;

        [...json].forEach((notification) => {
          if (!notification.read_status) {
            unreadMessages = true;
            notificationDropdown.innerHTML += `
          <li class="nav-link">
            <a href="#" class="nav-item dropdown-item">
              ${notification.message}
            </a>
          </li>`;
          }
        })
        if (unreadMessages) {
          notificationBubble.className += 'notification d-none d-lg-block d-xl-block';
        }
      });
  }, 2500)

  $(document).ready(function() {
    $().ready(function() {
      $sidebar = $('.sidebar');
      $navbar = $('.navbar');
      $main_panel = $('.main-panel');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');
      sidebar_mini_active = true;
      white_color = false;

      window_width = $(window).width();

      fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();



      $('.fixed-plugin a').click(function(event) {
        if ($(this).hasClass('switch-trigger')) {
          if (event.stopPropagation) {
            event.stopPropagation();
          } else if (window.event) {
            window.event.cancelBubble = true;
          }
        }
      });

      $('.fixed-plugin .background-color span').click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('color');

        if ($sidebar.length != 0) {
          $sidebar.attr('data', new_color);
        }

        if ($main_panel.length != 0) {
          $main_panel.attr('data', new_color);
        }

        if ($full_page.length != 0) {
          $full_page.attr('filter-color', new_color);
        }

        if ($sidebar_responsive.length != 0) {
          $sidebar_responsive.attr('data', new_color);
        }
      });

      $('.switch-sidebar-mini input').on("switchChange.bootstrapSwitch", function() {
        var $btn = $(this);

        if (sidebar_mini_active == true) {
          $('body').removeClass('sidebar-mini');
          sidebar_mini_active = false;
          blackDashboard.showSidebarMessage('Sidebar mini deactivated...');
        } else {
          $('body').addClass('sidebar-mini');
          sidebar_mini_active = true;
          blackDashboard.showSidebarMessage('Sidebar mini activated...');
        }

        // we simulate the window Resize so the charts will get updated in realtime.
        var simulateWindowResize = setInterval(function() {
          window.dispatchEvent(new Event('resize'));
        }, 180);

        // we stop the simulation of Window Resize after the animations are completed
        setTimeout(function() {
          clearInterval(simulateWindowResize);
        }, 1000);
      });

      $('.switch-change-color input').on("switchChange.bootstrapSwitch", function() {
        var $btn = $(this);

        if (white_color == true) {

          $('body').addClass('change-background');
          setTimeout(function() {
            $('body').removeClass('change-background');
            $('body').removeClass('white-content');
          }, 900);
          white_color = false;
        } else {

          $('body').addClass('change-background');
          setTimeout(function() {
            $('body').removeClass('change-background');
            $('body').addClass('white-content');
          }, 900);

          white_color = true;
        }


      });
    });
  });
</script>
@endif

@if(isset($bootstrap))
<script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
@endif

@if(isset($libscripts))
<script src="/assets/bundles/libscripts.bundle.js"></script> 
@endif


@if(isset($vendorscripts))
<script src="/assets/bundles/vendorscripts.bundle.js"></script> <!-- slimscroll, waves Scripts Plugin Js -->
@endif
@if(isset($bootstrapselect))
<script src="/assets/plugins/bootstrap-select/js/bootstrap-select.min.js"></script> <!-- Morris Plugin Js --> 
@endif

@if(isset($knob))
<script src="/assets/bundles/knob.bundle.js"></script> <!-- Jquery Knob-->
@endif

@if(isset($jvectormap))
<script src="/assets/bundles/jvectormap.bundle.js"></script> <!-- JVectorMap Plugin Js -->
@endif

@if(isset($morrisscripts))
<script src="/assets/bundles/morrisscripts.bundle.js"></script> <!-- Morris Plugin Js --> 
@endif

@if(isset($charts))
<script src="/assets/bundles/chartscripts.bundle.js"></script> <!-- Morris Plugin Js --> 
@endif

@if(isset($sparkline))
<script src="/assets/bundles/sparkline.bundle.js"></script> <!-- sparkline Plugin Js --> 
@endif

@if(isset($doughnut))
<script src="/assets/bundles/doughnut.bundle.js"></script>
@endif
@if(isset($mainscripts))
<script src="/assets/bundles/mainscripts.bundle.js"></script>
@endif

@if(isset($index))
<script src="/assets/js/pages/index.js"></script>
@endif

@if(isset($universal))
<script src="/assets/js/custom/universal.min.js"></script>
@endif

@if(isset($onTheFlyApi))
<script src="/assets/js/custom/flyAPIs.min.js"></script>
@endif

@if(isset($datatable))
<script src="/assets/plugins/jquery-datatable/dataTables.bootstrap4.min.js"></script>
@endif

@if(isset($sweetalert))
<script src="/assets/plugins/sweetalert/sweetalert.min.js"></script>
@endif

@if(isset($tableAction))
  <script src="/assets/js/custom/tableAction.min.js" charset="utf-8"></script>
@endif

@if(isset($select2bs4))
<script src="/assets/js/plugins/select2.min.js"></script>
<script>
  $(function () {
  $('.search-select').each(function () {
    $(this).select2({
      theme: 'bootstrap4',
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      allowClear: Boolean($(this).data('allow-clear')),
      closeOnSelect: !$(this).attr('multiple'),
    });
  });
});
</script>
@endif
@if(isset($select2))
<script src="/assets/js/plugins/select2.min.js"></script>
<script src="/assets/js/custom/select2Init.min.js"></script>
@endif
<script src="/assets/js/custom/app.min.js"></script>