<form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('payments.nullify', $payment->id ) }}">
    @csrf
    @method("PUT")

    <p class="text-center">
        Are you sure you want to nullify this payment?
    </p>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
        <button type="submit" name="submit_btn" value="Nullify Payment" class="btn btn-danger">YES</button>
</form>