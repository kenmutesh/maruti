<!DOCTYPE html>
<html class="no-js " lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport">
<meta name="description" content="Aprotec Powder Coating System">
<meta name="author" content="Elvis Ben, xlvisben" />
<title>
  {{ config('app.name') }} - {{ $pageTitle }}
</title>
<!-- Favicon-->
<link rel="apple-touch-icon" sizes="76x76" href="/assets/img/aprotec-ico.png">
<link rel="icon" type="image/png" href="/assets/img/aprotec-ico.png">

@if(isset($select2bs4))
<link rel="stylesheet" href="/assets/plugins/select2/select2.min.css"/>
<link rel="stylesheet" href="/assets/plugins/select2/select2bs4.min.css"/>
<style>
  .select2-container--bootstrap4 .select2-results__option--highlighted, .select2-container--bootstrap4 .select2-results__option--highlighted.select2-results__option[aria-selected="true"]{
    background-color: #ffb236;
  }
</style>
@endif

@if(isset($select2))
<link rel="stylesheet" href="/assets/plugins/select2/select2.min.css"/>
@endif

<link rel="stylesheet" href="/assets/plugins/bootstrap/css/bootstrap.min.css">

@if(isset($morris))
<link rel="stylesheet" href="/assets/plugins/morrisjs/morris.css" />
@endif

@if(isset($jvectormap))
<link rel="stylesheet" href="/assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.css"/>
@endif

@if(isset($bootstrapselect))
<link rel="stylesheet" href="/assets/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
@endif

@if(isset($datatable))
<link rel="stylesheet" href="/assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css"/>
<style>
  div.dataTables_wrapper div.dataTables_filter input{
    border: 1px solid;
  }
</style>
@endif

<!-- Custom Css -->
<link rel="stylesheet" href="/assets/scss/main.min.css">    
<link rel="stylesheet" href="/assets/scss/color_skins.min.css">
<link rel="stylesheet" href="/assets/plugins/sweetalert/sweetalert.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
  .modal-backdrop.show{
    z-index: -1;
  }
</style>
<link rel="stylesheet" href="/assets/css/pace-theme-barber-shop.css">
<script src="/assets/js/custom/pace.min.js"></script>
</head>
