@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Completed Purchase Order Details',
'bootstrapselect' => true,
'datatable' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
  .bootstrap-select {
    padding: 0 !important;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/purchases'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">
            <h3 class="text-center">{{ $purchaseorder->lpo_prefix }}{{ $purchaseorder->lpo_suffix }}</h3>

            <div class="row">
              <div class="col-sm-12 text-center">
                @if($purchaseorder->grand_total > 0 && $purchaseorder->lpo_suffix != '')
                <a href="/purchaseorders/{{ $purchaseorder->id }}" target="_blank" class="btn btn-primary">
                  VIEW LPO DOCUMENT
                </a>
                @endif
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                {{ $purchaseorder->supplier->supplier_name }}
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                {{ $purchaseorder->record_date }}
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                {{ $purchaseorder->due_date }}
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                {{ $purchaseorder->currency }} {{ $purchaseorder->grand_total }}
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                Quotation References
                <p class="m-0 font-weight-bold">{{ $purchaseorder->quotation_ref }} </p>
                Attached Documents:
                @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::QUOTATION->value)
                @continue
                @endif
                <a class="w-100 btn btn-primary" target="_blank" href="{{ asset('/storage/'. $purchaseorderdocument->document_path ) }}">
                {{ $purchaseorderdocument->document_name }}
                </a>
                @endforeach
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                Memo References
                <p class="m-0 font-weight-bold"> {{ $purchaseorder->memo_ref }} </p>
                Attached Documents:
                @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::MEMO->value)
                @continue
                @endif
                <a class="w-100 btn btn-primary" target="_blank" href="{{ asset('/storage/'. $purchaseorderdocument->document_path ) }}">
                {{ $purchaseorderdocument->document_name }}
                </a>
                @endforeach
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                Invoice References
                <p class="m-0 font-weight-bold"> {{ $purchaseorder->invoice_ref }} </p>
                Attached Documents:
                @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::INVOICE->value)
                @continue
                @endif
                <a class="w-100 btn btn-primary" target="_blank" href="{{ asset('/storage/'. $purchaseorderdocument->document_path ) }}">
                {{ $purchaseorderdocument->document_name }}
                </a>
                @endforeach
              </div>

              <div class="col-sm-5 border border-dark m-3 p-3">
                Delivery Note References
                <p class="m-0 font-weight-bold">{{ $purchaseorder->delivery_ref }} </p>
                Attached Documents:
                @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::DELIVERY->value)
                @continue
                @endif
                <a class="w-100 btn btn-primary" target="_blank" href="{{ asset('/storage/'. $purchaseorderdocument->document_path ) }}">
                {{ $purchaseorderdocument->document_name }}
                </a>
                @endforeach
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