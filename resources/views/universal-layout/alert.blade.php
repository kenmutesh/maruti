@include('universal-layout.scripts',
  [
  'sweetalert' => true,
  ]
  )
@if(Session::has('Success'))
    <script type="text/javascript">
        swal({
            title: 'Success',
            text: '{{ Session::get("Success") }}',
            type: 'success',
            timer: 3000,
            buttons: false,
        });
        // if(window.location.port.includes('80')){
        //     // syncDBUI(false);
        // }
    </script>
@endif

@if(Session::has('Error'))
    <script type="text/javascript">
        swal({
            title: 'Failed',
            text: '{{ Session::get("Error") }}',
            type: 'error',
            timer: 3000,
            buttons: false,
        });
    </script>
@endif