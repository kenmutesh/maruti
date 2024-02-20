@forelse($invoices as $invoice)
@if($loop->first)
<option disabled>Choose one</option>
@endif
<option value="{{ $invoice->id }}">{{ $invoice->invoice_prefix }}{{ $invoice->invoice_suffix }}</option>
@empty
<option disabled>No invoices for selected customer</option>
@endforelse