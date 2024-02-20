
</div>
</div>

<div class="fixed-plugin">
<div class="dropdown show-dropdown">
<a href="#" data-toggle="dropdown">
  <i class="fa fa-cog fa-2x"> </i>
</a>
<ul class="dropdown-menu">
  <li class="header-title"> System Theme</li>
      <div class="clearfix"></div>
    </a>
  </li>
  <li class="adjustments-line text-center color-change">
    <span class="color-label">LIGHT MODE</span>
    <span class="badge light-badge mr-2"></span>
    <span class="badge dark-badge ml-2"></span>
    <span class="color-label">DARK MODE</span>
  </li>
</ul>
</div>
</div>

<!--   Core JS Files   -->
<script src="/assets/js/core/jquery.min.js"></script>
<script src="/assets/js/core/popper.min.js"></script>
<script src="/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>

<!--  Notifications Plugin    -->
<script src="/assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Black Dashboard: parallax effects, scripts for the example pages etc -->
<script src="/assets/js/black-dashboard.min.js?v=1.0.0"></script><!-- Black Dashboard DEMO methods, don't include it in your project! -->

@include('system.layout.alert')

<script src="/assets/demo/demo.js"></script>


<script>
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
@if(isset($notifications) && $notifications)
<script src="/assets/js/custom/notifications.min.js" charset="utf-8"></script>
@endif

</script>
@if(isset($dataTable) && $dataTable)
  <script src="/assets/js/plugins/dataTables.js" charset="utf-8"></script>
  <script src="/assets/js/custom/dataTableInit.min.js" charset="utf-8"></script>
@endif

@if(isset($dataTableExcel) && $dataTableExcel)
  <script src="/assets/js/plugins/dataTables.js" charset="utf-8"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
  <script>
      $(document).ready(function() {
        $('table').DataTable( {
            "order": [[ 0, "desc" ]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Get Excel Report',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3 ]
                    }
                },
            ]
        });
      });
  </script>
@endif

@if(isset($select2) && $select2)
  <script src="/assets/js/plugins/select2.min.js" charset="utf-8"></script>
  <script src="/assets/js/custom/select2Init.min.js" charset="utf-8"></script>
@endif

@if(isset($flyAPI))
  <script src="/assets/js/custom/flyAPIs.min.js" charset="utf-8"></script>
@endif

@if(isset($tableAction))
  <script src="/assets/js/custom/tableAction.min.js" charset="utf-8"></script>
@endif
<script src="/assets/js/custom/universal.min.js" charset="utf-8"></script>
</body>

</html>
