<?php
$matches = 0;
?>
@foreach($purchases as $purchase)
    @if($purchase->amount_due <= 0)
        @continue
    @endif
    <tr attr-customer-id="{{ $purchase->supplier_id }}" class="invoice-item">
        <td class="text-center p-0">
            <label class="form-check-label">
                <input class="" type="checkbox" onchange="activateAmountPaid(this)" value="{{ $purchase->id }}" name="purchase_order_id[]">
                <span class="form-check-sign"></span>
            </label>
        </td>
        <td class="p-0">
            {{ date('d-m-Y', strtotime($purchase->created_at)) }}
        </td>
        <td class="p-0">
            <a href="/purchaseorders/{{ $purchase->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                VIEW PURCHASE - {{ $purchase->lpo_prefix }}{{ $purchase->lpo_suffix }}
            </a>
        </td>
        <td class="p-0">
            {{ $purchase->amount_due }}
        </td>
        <td class="p-0">
            <div class="form-group">
                <input type="number" step=".01" name="amount_paid[]" disabled max="{{ $purchase->amount_due }}" onkeyup="updateTotal(this)" class="form-control p-1 border border-dark rounded-sm">
            </div>
        </td>
    </tr>
    <?php
        $matches += 1;
    ?>
@endforeach
@if($matches < 1)
<tr>
    <td colspan="100%">No purchases for selected supplier</td>
</tr>
@endif