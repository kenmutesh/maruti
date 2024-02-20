<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Powder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="tim-icons icon-simple-remove"></i>
        </button>
    </div>
    <div class="modal-body">

        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('powders.update.quantity', $powder->id) }}">
            @csrf
            <div class="d-flex flex-column">

                <div class="form-group">
                    <label for="">Quantity</label>
                    <input type="number" name="quantity" step=".01" min="0.01" class="form-control" placeholder="Enter new quantity">
                </div>
            </div>
            <button type="submit" name="submit_btn" value="Create" class="btn btn-primary">UPDATE</button>
        </form>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
        
    </div>
</div>