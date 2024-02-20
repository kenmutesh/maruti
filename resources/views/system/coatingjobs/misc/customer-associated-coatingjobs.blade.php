<option disabled>Choose one or more</option>
@foreach($coatingJobs as $coatingJob)
<option value="{{ $coatingJob->id }}">{{ $coatingJob->coating_prefix }}{{ $coatingJob->coating_suffix }} (KES {{ number_format($coatingJob->sum_grandtotal, 2) }})</option>
@endforeach