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

                        <form onsubmit="showSpinner(event)" action="{{ route('invoices.direct.store') }}" method="post" autocomplete="off">
                            @csrf
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group position-relative" style="z-index: 2;">
                                        <label for="supplier">Customer</label>
                                        <select class="form-control customer-select ms search-select" onchange="populateCoatingJobs(this)" required name="customer_id" data-live-search="true" data-style="text-white">
                                            <option disabled selected>Choose customer</option>
                                            @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-tokens="{{ $customer->company }}">
                                                {{ $customer->company }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 d-flex flex-column justify-content-center">
                                    <label>LPO(Optional)</label>
                                    <input type="text" name="lpo" class="form-control dark-text mt-1">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">CU Number</label>
                                        <div class="row">
                                            <input class="col-6 form-control" type="text" name="cu_number_prefix" value="{{ $invoice->next_cu_prefix }}">
                                            <input class="col-6 form-control" type="number" step=".1" name="cu_number_suffix" value="{{ $invoice->next_cu_suffix }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label>Grand Total</label>
                                    <input type="text" name="grand_total" readonly class="form-control" value="">
                                </div>

                                <div class="col-sm-6">
                                    <label>Discount</label>
                                    <input type="number" min="0" step=".01" name="discount" class="form-control" value="0">
                                </div>

                                <div class="col-sm-6 align-items-center d-none">
                                    <label class="d-flex">
                                        <input type="checkbox" name="external" class="mr-2" value="1">
                                        Treat as external
                                    </label>
                                </div>

                                <div class="col-sm-6">
                                    <label>Combine Other Job Cards</label>
                                    <select class="form-control jobcard-select ms search-select" required name="combined_jobcards[]" multiple data-live-search="true" data-style="text-white">
                                        <option disabled>Choose customer first</option>
                                        
                                    </select>
                                </div>

                            </div>

                            <div class="row my-4 d-none">
                                <div class="col-sm-12 d-flex flex-column">
                                    <label>Product</label>
                                    <div>
                                        @foreach($ownerEnums as $owner)
                                        @if($owner != App\Enums\CoatingJobOwnerEnum::DIRECT)
                                        @continue
                                        @endif
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input checked class="form-check-input" type="radio" name="belongs_to" id="maruti" onchange="toggleActiveItemList(this)" value="{{ $owner->value }}" data-value="{{ $owner->coatingjobselectionradiovalue() }}"> {{ $owner->coatingjobselectionradiovalue() }}

                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive mt-3 w-80 direct-sale" style="overflow-x: auto;display:block;overflow:initial;">
                                <table class="table table-bordered col w-100 mx-auto">
                                    <thead>
                                        <tr>
                                            <th class="p-0 border text-center">Item</th>
                                            <th class="p-0 border text-center">Name</th>
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
                                                    <option value="">Custom Item</option>
                                                    @forelse($inventoryitems as $type => $items)
                                                    <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                                                        <?php $items = json_decode(json_encode($items)) ?>
                                                        @forelse($items as $item)
                                                        <option value="{{ $item->id }}" attr-data-price="{{ $item->standard_price }}" attr-data-current-quantity="{{ $item->current_quantity }}" attr-data-price-vat="{{ $item->standard_price_vat }}" attr-data-price-without-vat="{{ $item->standard_price_without_vat }}" attr-data-uom="{{ $item->quantity_tag }}" attr-data-name="{{ $item->item_name }}">
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
                                                        <option value="{{ $powder->id }}" attr-data-price="{{ $powder->standard_price }}" attr-data-current-quantity="{{ $powder->current_weight }}" attr-data-price-vat="{{ $powder->standard_price_vat }}" attr-data-price-without-vat="{{ $powder->standard_price_without_vat }}" attr-data-uom="KG" attr-data-name="{{ $powder->powder_color }}">
                                                            {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                                                        </option>
                                                        @empty
                                                        <option>No item under category</option>
                                                        @endforelse
                                                    </optgroup>


                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" name="custom_item_name[]" style="width:4rem;" readonly>
                                            </td>

                                            <td>
                                                <input type="number" min="1" step=".01" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_qty[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" step=".01" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_kg[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" name="maruti_direct_item_boxes[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" name="maruti_direct_uom[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" step=".01" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_unit_price[]" class="w-100" value="">
                                            </td>

                                            <td>
                                                <input type="number" step=".01" max="100" name="maruti_direct_unit_vat[]" class="w-100" value="0">
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

                            <div class="d-flex justify-content-around">
                                <button type="submit" name="submit_btn" value="Create Coating Job" class="btn btn-success text-white w-100 rounded-pill my-4">CREATE: {{ $invoice->next_invoice_prefix }}{{ $invoice->next_invoice_suffix }}</button>
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

    <script>
        async function populateCoatingJobs(customerSelect) {

            const customerSelectDropdown = document.querySelector(`.jobcard-select`);

            if ($(customerSelectDropdown).data("select2")) {
                $(customerSelectDropdown).select2('destroy');
            }
            const openCoatingJobsRequest = await fetch(`/coatingjobs/open/${customerSelect.value}`);

            if (openCoatingJobsRequest.ok) {
                const response = await openCoatingJobsRequest.text();

                customerSelectDropdown.innerHTML = response;
            } else {
                customerSelectDropdown.innerHTML = '<option disabled>Error in getting jobcards</option>';
            }
            $(customerSelectDropdown).select2();
        }
    </script>

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