@forelse($purchases as $purchase)
@if($loop->first)
<option disabled>Choose one</option>
@endif
<option value="{{ $purchase->id }}">{{ $purchase->lpo_prefix }}{{ $purchase->lpo_suffix }}</option>
@empty
<option disabled>No data</option>
@endforelse