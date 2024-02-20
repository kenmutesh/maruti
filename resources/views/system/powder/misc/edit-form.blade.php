<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="tim-icons icon-simple-remove"></i>
        </button>
    </div>
    <div class="modal-body">

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('powders.update', $powder->id) }}">
            @csrf
            @method('PUT')
            <div class="d-flex flex-column">

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" required name="powder_color" value="{{ $powder->powder_color }}" class="form-control" id="color" class="dark-text" placeholder="Enter powder color">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" required name="powder_code" value="{{ $powder->powder_code }}" class="form-control" id="code" class="dark-text" placeholder="Enter powder code">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" required name="powder_description" value="{{ $powder->powder_description }}" class="form-control" id="description" class="dark-text" placeholder="Enter powder description">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="serial">Serial Number</label>
                            <input type="text" required name="serial_no" value="{{ $powder->serial_no }}" class="form-control" id="serial" class="dark-text" placeholder="Enter serial number">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="manufactureDate">Manufacture Date</label>
                            <input type="date" required name="manufacture_date" value="{{ $powder->manufacture_date }}" class="form-control" id="manufactureDate" class="dark-text">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="date" required name="expiry_date" value="{{ $powder->expiry_date }}" class="form-control" id="expiryDate" class="dark-text">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="goodsWeight">Goods Weight</label>
                            <input type="number" min="0" step=".001" required name="goods_weight" value="{{ $powder->goods_weight }}" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="batchNo">Batch Number</label>
                            <input type="text" required name="batch_no" value="{{ $powder->batch_no }}" class="form-control" id="batchNo" class="dark-text" placeholder="Enter batch">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="standardCost">Standard Cost(With VAT)</label>
                            <input type="number" min="0" step=".01" required name="standard_cost" value="{{ $powder->standard_cost }}" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vat">Standard Cost VAT</label>
                            <input type="number" min="0" step=".01" max="100" name="standard_cost_vat" value="{{ $powder->standard_cost_vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="standardCost">Standard Price(With VAT)</label>
                            <input type="number" min="0" step=".01" required name="standard_price" value="{{ $powder->standard_price }}" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vat">Standard Price VAT</label>
                            <input type="number" min="0" step=".01" max="100" name="standard_price_vat" value="{{ $powder->standard_price_vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="minThreshold">Min Threshold</label>
                            <input type="number" min="0" step=".01" required name="min_threshold" value="{{ $powder->min_threshold }}" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="maxThreshold">Max Threshold</label>
                            <input type="number" min="0" step=".01" max="100" name="max_threshold" value="{{ $powder->max_threshold }}" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="d-block">Default Supplier</label>
                            <select name="supplier_id" class="form-control ms" required>
                                @forelse($suppliers as $supplier)
                                @if($powder->supplier_id == $supplier->id)
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