@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Powder Coating',
'select2bs4' => true,
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
    'slug' => '/coatingjobs/create'
    ]
    )
    <section class="content home">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="main-panel">

                    <div class="content">

                        <form action="{{ route('coatingjobs.store') }}" method="post" autocomplete="off">
                            @csrf
                            <input type="hidden" name="quotation_prefix" value="{{ $coatingjob->next_quotation_prefix }}">
                            <input type="hidden" name="quotation_suffix" value="{{ $coatingjob->next_quotation_suffix }}">
                            <input type="hidden" name="coating_prefix" value="{{ $coatingjob->next_coating_job_prefix }}">
                            <input type="hidden" name="coating_suffix" value="{{ $coatingjob->next_coating_job_suffix }}">
                            <input type="hidden" required name="coating_job" value="{{ $coatingjob->next_coating_job_prefix }}{{ $coatingjob->next_coating_job_suffix }}">
                            <input type="hidden" name="quotation_number" class="form-control dark-text" readonly value="{{ $coatingjob->next_quotation_prefix }}{{ $coatingjob->next_quotation_suffix }}">

                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group position-relative" style="z-index: 2;">
                                        <label for="supplier">Customer</label>
                                        <select class="form-control customer-select ms search-select" required name="customer_id" data-live-search="true" data-style="text-white">
                                            @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-tokens="{{ $customer->customer_name }}">
                                                {{ $customer->customer_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 d-flex flex-column justify-content-center">
                                    <label>Add Name for Cash Sale(Optional)</label>
                                    <input type="text" name="cash_sale_name" class="form-control dark-text mt-1">
                                </div>

                                <div class="col-sm-6 d-flex flex-column justify-content-center">
                                    <label>LPO(Optional)</label>
                                    <input type="text" name="lpo" class="form-control dark-text mt-1">
                                </div>

                                <div class="col-sm-3 my-4">
                                    <label>Date</label>
                                    <input type="date" required name="date" class="form-control dark-text data-today-date">
                                </div>

                                <div class="col-sm-3 my-4">
                                    <label>In Date(Optional)</label>
                                    <input type="date" name="in_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 my-4">
                                    <label>Out Date(Optional)</label>
                                    <input type="date" name="out_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 my-4">
                                    <label>Ready Date(Optional)</label>
                                    <input type="date" name="ready_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 position-relative" style="z-index: 2;">
                                    <label>RAL/Color(Optional)</label>
                                    <select class="searchable-select form-control ms search-select" name="ral_main" onchange="prefillRALData(this)" data-live-search="true" data-style="text-white">
                                        <option disabled selected>Pick Powder Item</option>
                                        @forelse($powders as $powder)
                                        <option value="{{ $powder->id }}" attr-data-color="{{ $powder->powder_color }}" attr-data-code="{{ $powder->powder_color }}">
                                            {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                                        </option>
                                        @empty
                                        <option>No item under category</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label>Goods Weight(Optional)</label>
                                    <input type="number" step="any" name="goods_weight" onkeyup="calculatePowderEstimateAluminium(this)" class="form-control dark-text p-2">
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="profileType">Powder Profile Type</label>
                                        <select id="profileType" name="profile_type" class="form-control ms p-2" onchange="calculatePowderEstimateAluminium(this)">
                                            @foreach($profileTypesEnum as $profileType)
                                            <option value="{{ $profileType->value }}">
                                                {{ $profileType->humanreadablestring() }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label>Powder Estimate</label>
                                    <input type="number" step="any" required readonly name="powder_estimate" class="form-control dark-text p-2">
                                </div>

                                <input readonly type="hidden" name="color" class="form-control dark-text">
                                <input readonly type="hidden" multiple name="code" class="form-control dark-text">

                                <div class="col-sm-6">
                                    <label>Grand Total</label>
                                    <input type="text" name="grand_total" readonly class="form-control" value="">
                                </div>

                            </div>

                            <div class="row my-4">

                                <div class="col-sm-12 d-flex flex-column">
                                    <label>Product</label>
                                    <div>
                                        @foreach($ownerEnums as $owner)
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                @if ($loop->first)
                                                <input class="form-check-input" type="radio" name="belongs_to" id="maruti" onchange="toggleActiveItemList(this)" value="{{ $owner->value }}" data-value="{{ $owner->coatingjobselectionradiovalue() }}" checked> {{ $owner->coatingjobselectionradiovalue() }}
                                                @else
                                                <input class="form-check-input" type="radio" name="belongs_to" id="maruti" onchange="toggleActiveItemList(this)" value="{{ $owner->value }}" data-value="{{ $owner->coatingjobselectionradiovalue() }}"> {{ $owner->coatingjobselectionradiovalue() }}
                                                @endif
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive mt-3 w-80 maruti-coating" style="overflow-x: auto;display:block;overflow:initial;">
                                <table class="table table-bordered col w-70 mx-auto">
                                    <th class="p-0 border text-center">Item</th>
                                    <th class="p-0 border text-center">Quantity</th>
                                    <th class="p-0 border text-center">Boxes</th>
                                    <th class="p-0 border text-center">UoM</th>
                                    <th class="p-0 border text-center">Unit Price</th>
                                    <th class="p-0 border text-center">VAT(%)</th>
                                    <th class="p-0 border text-center">Vat Inclusive</th>
                                    <th class="p-0 border text-center">Total Amt</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list maruti-table">
                                        <tr>
                                            <td>
                                                <select style="width:200px;z-index: 2;" name="maruti_item_id[]" class="search-select ms position-relative" onchange="prefillItemRowMaruti(this)" data-style="text-white position-static" data-live-search="true" data-boundary="viewport">
                                                    <option disabled selected>Choose from inventory</option>
                                                    @forelse($inventoryitems as $type => $items)
                                                    <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                                                        <?php $items = json_decode(json_encode($items)) ?>
                                                        @forelse($items as $item)
                                                        <option value="{{ $item->id }}" attr-data-price="{{ $item->standard_price }}" attr-data-price-vat="{{ $item->standard_price_vat }}" attr-data-price-without-vat="{{ $item->standard_price_without_vat }}" attr-data-uom="{{ $item->quantity_tag }}" attr-data-current-quantity="{{ $item->current_quantity }}" attr-data-name="{{ $item->item_name }}">
                                                            {{ $item->item_name }}
                                                        </option>
                                                        @empty
                                                        <option>No item under category</option>
                                                        @endforelse
                                                    </optgroup>
                                                    @empty
                                                    No registered inventory items
                                                    @endforelse

                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" min="1" class="w-100" onkeyup="calculateMarutiItemRowTotal(this)" oninput="calculateMarutiItemRowTotal(this)" name="maruti_item_qty[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" class="w-100" name="maruti_item_boxes[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:3rem" class="w-100" name="maruti_item_uom[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" step="any" class="w-100" onkeyup="calculateMarutiItemRowTotal(this)" name="maruti_unit_price[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" style="width:3rem" step="any" onkeyup="calculateMarutiItemRowTotal(this)" name="maruti_item_vat[]" value="{{ $vat->percentage }}">
                                            </td>

                                            <td>
                                                <select class="ms w-100" name="maruti_item_vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" class="maruti-total w-100" name="maruti_item_amount[]" readonly>
                                            </td>

                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                <button type="button" name="button" onclick="addItemRow('.maruti-table')" class="btn btn-danger">ADD
                                                    ITEM</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-3 w-80 owner-aluminium-coating" style="overflow-x: auto;display:none;">
                                <table class="table table-bordered col w-70 mx-auto">
                                    <th class="p-0 border text-center">Name</th>
                                    <th class="p-0 border text-center">Qty</th>
                                    <th class="p-0 border text-center">KG</th>
                                    <th class="p-0 border text-center" style="width:3rem">UoM</th>
                                    <th class="p-0 border text-center">Unit Price</th>
                                    <th class="p-0 border text-center" style="width:3rem">VAT(%)</th>
                                    <th class="p-0 border text-center">Vat<br />Inclusive</th>
                                    <th class="p-0 border text-center">Amount</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list">
                                        <tr class="clients-tr">
                                            <td>
                                                <input type="text" name="aluminium_item_name[]" class="w-100" value="">
                                            </td>
                                            <td><input type="number" name="aluminium_item_qty[]" class="w-100" value="1" min="0"></td>
                                            <td><input type="number" step="any" class="w-100 owner-kg" onkeyup="updateAmountOwnerUnits(this)" onchange="updateAmountOwnerUnits(this)" name="item_kg[]" value="" min="0"></td>
                                            <td>
                                                <input type="text" style="width:3rem" name="aluminium_uom[]" value="LENGTHS">
                                            </td>
                                            <td>
                                                <input type="number" step="any" onkeyup="updateAmountOwnerUnits(this)" name="aluminium_unit_price[]" value="">
                                            </td>
                                            <td>
                                                <input type="text" style="width:3rem" name="aluminium_vat[]" value="{{ $vat->percentage }}">
                                            </td>
                                            <td>
                                                <select class="ms w-100" name="aluminium_vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="unit-cost-coating owner-total w-100" readonly name="amount[]" value="">
                                            </td>
                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-5 w-80 owner-steel-coating" style="overflow-x: auto;display:block;overflow:initial;">
                                <p class="text-center w-100 font-weight-bold h3">
                                    STEEL COATING
                                </p>
                                <table class="table table-bordered col w-70 mx-auto p-0">
                                    <th class="p-0 border text-center">Item<br />Name</th>
                                    <th class="p-0 border text-center" style="width:4.5rem;">Qty</th>
                                    <th class="p-0 border text-center" style="width:4rem;">Measure</th>
                                    <th class="p-0 border text-center">Unit</th>
                                    <th class="px-2 border text-center">Powder<br />Est</th>
                                    <th class="px-2 border text-center">Linear<br />Mtr</th>
                                    <th class="px-2 border text-center">Square<br />Mtr</th>
                                    <th class="px-2 border text-center">Unit Price</th>
                                    <th class="px-2 border text-center">UoM</th>
                                    <th class="px-2 border text-center">Vat(%)</th>
                                    <th class="px-2 border text-center">Vat<br />Inclusive</th>
                                    <th class="p-0 border text-center">Amount</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list steel-table">
                                        <tr class="clients-tr">
                                            <td>
                                                <input type="text" name="steel_item_name[]" value="" style="width:4rem;">
                                            </td>

                                            <td>
                                                <input type="number" step="any" class="w-100" name="steel_item_qty[]" onkeyup="changeItemAmountCoating(this)" value="0" min="0">
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column m-0 p-0" style="width:4rem;">
                                                    <span>Width</span>
                                                    <input type="number" name="steel_item_width[]" onkeyup="prefillArea(this)" value="">
                                                    <span>Length</span>
                                                    <input type="number" name="steel_item_length[]" onkeyup="prefillArea(this)" value="">
                                                </div>
                                            </td>

                                            <td>
                                                <input type="hidden" name="steel_charge" value="UNIT">
                                                <select class="form-control ms" style="width:5rem;" name="size[]" onchange="toggleSizeActive(this)">
                                                    <option value="LINEAR METRES">LINEAR METRES</option>
                                                    <option value="LINEAR METRES" data-per="true">LINEAR METRES(Charge Per Linear Mtr)</option>
                                                    <option value="SQUARE METRES" data-per="true">SQUARE METRES(Charge Per Sq Mtr)</option>
                                                    <option value="SQUARE METRES">SQUARE METRES</option>
                                                    <option value="-">INPUT MANUAL</option>
                                                </select>

                                                <input type="text" class="form-control mt-2" disabled name="size[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" class="w-100" name="steel_powder_estimate[]" value="">
                                            </td>
                                            <td>
                                                <input type="text" class="w-100 linear-metre-input" onkeyup="changeItemAmountCoating(this, true)" onchange="changeItemAmountCoating(this, true)" name="sq_metre[]">
                                            </td>
                                            <td>
                                                <input type="text" class="w-100 sq-metre-input" readonly onkeyup="changeItemAmountCoating(this, true)" onchange="changeItemAmountCoating(this, true)" name="sq_metre[]">
                                            </td>
                                            <td>
                                                <input type="number" step="any" style="width:4rem;" class="w-100" onchange="changeItemAmountCoating(this)" onkeyup="changeItemAmountCoating(this)" name="steel_unit_price[]" value="">
                                            </td>
                                            <td>
                                                <input type="text" value="PIECES" class="w-100" name="steel_uom[]">
                                            </td>
                                            <td>
                                                <input type="number" step="any" max="100" value="{{ $vat->percentage }}" class="w-100" name="steel_vat[]">
                                            </td>
                                            <td>
                                                <select class="ms w-100" name="steel_vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" onchange="updateCoatingJobGrandTotal()" class="owner-total w-100" name="steel_amount[]" value="">
                                            </td>
                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%">
                                                <button type="button" name="button" onclick="addItemRow('.steel-table')" class="btn btn-danger">ADD ITEM</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="table-responsive mt-3 w-80 direct-sale" style="overflow-x: auto;display:block;overflow:initial;">
                                <table class="table table-bordered col w-100 mx-auto">
                                    <thead>
                                        <tr>
                                            <th class="p-0 border text-center">Item</th>
                                            <th class="p-0 border text-center">Quantity</th>
                                            <th class="p-0 border text-center">KG</th>
                                            <th class="p-0 border text-center">Boxes</th>
                                            <th class="px-2 border text-center">UoM</th>
                                            <th class="p-0 border text-center">Unit Price</th>
                                            <th class="px-2 border text-center">Vat(%)</th>
                                            <th class="p-0 border text-center">Vat Inclusive</th>
                                            <th class="p-0 border text-center">Total Amt</th>
                                            <th class="p-0 border text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="item-list maruti-direct">
                                        
                                        <tr>
                                            <td>
                                                <input type="hidden" name="maruti_direct_inventory_type[]">
                                                <select style="width:200px;" name="maruti_direct_item_id[]" class="search-select ms" onchange="prefillItemRowMarutiDirect(this)" data-style="text-white" data-live-search="true">
                                                    <option disabled selected>Choose from inventory</option>

                                                    @forelse($inventoryitems as $type => $items)
                                                    <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                                                        <?php $items = json_decode(json_encode($items)) ?>
                                                        @forelse($items as $item)
                                                        <option value="{{ $item->id }}" 
                                                        attr-data-price="{{ $item->standard_price }}" 
                                                        attr-data-current-quantity="{{ $item->current_quantity }}" 
                                                        attr-data-price-vat="{{ $item->standard_price_vat }}" 
                                                        attr-data-price-without-vat="{{ $item->standard_price_without_vat }}" 
                                                        attr-data-uom="{{ $item->quantity_tag }}" 
                                                        attr-data-name="{{ $item->item_name }}">
                                                            {{ $item->item_name }}
                                                        </option>
                                                        @empty
                                                        <option>No item under category</option>
                                                        @endforelse
                                                    </optgroup>
                                                    @empty
                                                    No registered inventory items
                                                    @endforelse
                                                    <optgroup label="Powder" data-type="POWDER">
                                                        @forelse($powders as $powder)
                                                        <option value="{{ $powder->id }}" 
                                                        attr-data-price="{{ $powder->standard_price }}" 
                                                        attr-data-current-quantity="{{ $powder->current_weight }}" 
                                                        attr-data-price-vat="{{ $powder->standard_price_vat }}" 
                                                        attr-data-price-without-vat="{{ $powder->standard_price_without_vat }}" 
                                                        attr-data-uom="KG" 
                                                        attr-data-name="{{ $powder->powder_color }}">
                                                            {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                                                        </option>
                                                        @empty
                                                        <option>No item under category</option>
                                                        @endforelse
                                                    </optgroup>


                                                </select>
                                            </td>

                                            <td>
                                                <input type="number" min="1" step="any" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_qty[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" step="any" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_kg[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" name="maruti_direct_item_boxes[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" name="maruti_direct_uom[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" step="any" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_unit_price[]" class="w-100" value="">
                                            </td>

                                            <td>
                                                <input type="number" step="any" max="100" name="maruti_direct_unit_vat[]" class="w-100" value="">
                                            </td>

                                            <td>
                                                <select class="w-100 ms" name="maruti_direct_vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" class="maruti-total w-100" name="maruti_direct_amount[]" readonly>
                                            </td>

                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%">
                                                <button type="button" name="button" onclick="addItemRow('.maruti-direct')" class="btn btn-danger">ADD
                                                    ITEM</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-3">

                                <div class="col-sm-6">
                                    <label>Prepared By</label>
                                    <input type="hidden" name="prepared_by" value="{{ auth()->user()->id }}">
                                    <input type="text" readonly name="username" class="form-control" value="{{ auth()->user()->username }}">
                                </div>

                                <div class="col-sm-6">
                                    <label>Approved By</label>
                                    <select class="form-control search-select ms" name="approved_by">
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-6">
                                    <label>Supervisor</label>
                                    <select class="form-control search-select ms" name="supervisor">
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label>Quality By</label>
                                    <select class="form-control search-select ms" name="quality_by">
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label>Sale By</label>
                                    <select class="form-control search-select ms" name="sale_by">
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="d-flex justify-content-around">
                                <button type="submit" onclick="customSubmitShowSpinner(this)" name="document" value="Quotation" class="btn btn-success text-white col-5 rounded-pill my-4">CREATE NEXT QUOTATION {{ $coatingjob->next_quotation_prefix }}{{ $coatingjob->next_quotation_suffix }}</button>

                                <input type="hidden" name="next_card" value="{{ $coatingjob->next_coating_job_prefix }}{{ $coatingjob->next_coating_job_suffix }}">

                                <button type="submit" onclick="customSubmitShowSpinner(this)" name="document" value="Coating Job" class="btn btn-success text-white col-5 rounded-pill my-4">CREATE NEXT COATING JOB CARD ( NO: {{ $coatingjob->next_coating_job_prefix }}{{ $coatingjob->next_coating_job_suffix }} )</button>
                            </div>


                        </form>

                    </div>
                    <script>
                        const todayDate = new Date();
                        const todayDateDefault = document.querySelectorAll('.data-today-date');
                        todayDateDefault.forEach((dateInput) => {
                            dateInput.value = todayDate.toLocaleDateString('en-CA');
                        })
                    </script>
                </div>
            </div>
        </div>
    </section>

    @include('universal-layout.scripts',
    [
    'libscripts' => true,
    'vendorscripts' => true,
    'mainscripts' => true,
    'bootstrapselect' => true,
    'select2bs4' => true,
    'coating' => true,
    'onTheFlyApi' => true,
    ]
    )
    @include('universal-layout.footer')