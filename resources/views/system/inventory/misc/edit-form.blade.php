<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit inventory Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="tim-icons icon-simple-remove"></i>
        </button>
    </div>
    <div class="modal-body">

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('inventoryitems.update', $inventoryItem->id) }}">
            @csrf
            @method('PUT')
            <div class="d-flex flex-column">

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="itemName">Name</label>
                            <input type="text" required name="item_name" value="{{ $inventoryItem->item_name }}" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="itemCode">Code</label>
                            <input type="text" required name="item_code" value="{{ $inventoryItem->item_code }}" class="form-control" id="itemCode" class="dark-text" placeholder="Enter item code">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="itemDescription">Description</label>
                            <input type="text" required name="item_description" value="{{ $inventoryItem->item_description }}" class="form-control" id="itemDescription" class="dark-text" placeholder="Enter item description">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="serial">Serial</label>
                            <input type="text" required name="serial_no" value="{{ $inventoryItem->serial_no }}" class="form-control" id="serial" class="dark-text" placeholder="Enter item serial number">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="quantityTag">Quantity Tag</label>
                            <input type="text" required name="quantity_tag" value="{{ $inventoryItem->quantity_tag }}" class="form-control" id="quantityTag" class="dark-text" placeholder="Enter item quantity tag">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" class="form-control ms" required>
                                @forelse($inventoryItemTypes as $type)
                                @if($inventoryItem->type->value == $type->value)
                                <option value="{{ $type->value }}" selected>
                                    {{ $type->humanreadablestring() }} - (CURRENT)
                                </option>
                                @else
                                <option value="{{ $type->value }}">
                                    {{ $type->humanreadablestring() }}
                                </option>
                                @endif
                                @empty
                                <option disabled selected>No types to choose from</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="goodsWeight">Goods Weight</label>
                            <input type="number" min="0" step=".0001" required name="goods_weight" value="{{ $inventoryItem->goods_weight }}" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="standardCost">Standard Cost(With VAT)</label>
                            <input type="number" min="0" step=".01" required name="standard_cost" value="{{ $inventoryItem->standard_cost }}" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vat">Standard Cost VAT</label>
                            <input type="number" min="0" step=".01" max="100" name="standard_cost_vat" value="{{ $inventoryItem->standard_cost_vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="standardCost">Standard Price(With VAT)</label>
                            <input type="number" min="0" step=".01" required name="standard_price" value="{{ $inventoryItem->standard_price }}" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vat">Standard Price VAT</label>
                            <input type="number" min="0" step=".01" max="100" name="standard_price_vat" value="{{ $inventoryItem->standard_price_vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="minThreshold">Min Threshold</label>
                            <input type="number" min="0" step=".01" value="{{ $inventoryItem->min_threshold }}" required name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="maxThreshold">Max Threshold</label>
                            <input type="number" min="0" step=".01" value="{{ $inventoryItem->max_threshold }}" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="d-block">Default Supplier</label>
                            <select name="supplier_id" id="" class="form-control ms search-dropdown">
                                <option>Choose supplier</option>
                                @forelse($suppliers as $supplier)
                                @if($inventoryItem->supplier_id == $supplier->id)
                                <option value="{{ $supplier->id }}" selected>
                                    {{ $supplier->supplier_name }} - (CURRENT)
                                </option>
                                @else
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->supplier_name }}
                                </option>
                                @endif
                                @empty
                                <option value="">No suppliers registered</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="submit_btn" value="Create" class="btn btn-primary">EDIT</button>
        </form>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
        
    </div>
</div>