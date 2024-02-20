<?php
$matches = 0;
?>
@foreach($invoices as $invoice)
    @if($invoice->amount_due <= 0)
        @continue
    @endif
    <tr attr-customer-id="{{ $invoice->customer_id }}" class="invoice-item">
        <td class="text-center p-0">
            <label class="form-check-label">
                <input class="" type="checkbox" onchange="activateAmountPaid(this)" value="{{ $invoice->id }}" name="invoice_id[]">
                <span class="form-check-sign"></span>
            </label>
        </td>
        <td class="p-0">
            {{ date('d-m-Y', strtotime($invoice->created_at)) }}
        </td>
        <td class="p-0">
            <a href="/invoices/{{ $invoice->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                VIEW INVOICE - {{ $invoice->invoice_prefix }}{{ $invoice->invoice_suffix }}
            </a>
        </td>
        <td class="p-0">
            {{ $invoice->amount_due }}
        </td>
        <td class="p-0">
            <div class="form-group">
                <input type="number" step=".01" name="amount_paid[]" disabled max="{{ $invoice->amount_due }}" onkeyup="updateTotal(this)" class="form-control p-1 border border-dark rounded-sm">
            </div>
        </td>
    </tr>
    <?php
        $matches += 1;
    ?>
@endforeach
@if($matches < 1)
<tr>
    <td colspan="100%">No invoices for selected customer</td>
</tr>
@endif