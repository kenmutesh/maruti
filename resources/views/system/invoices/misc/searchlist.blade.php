<ul class="list-group text-dark">
    <?php
    $matches = 0;
    ?>
    @foreach($invoices as $invoice)
    @if(str_contains($invoice->invoice_suffix, $number))
    <?php
    $matches += 1;
    ?>
    <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        <span>
            {{ $invoice->invoice_prefix }}{{ $invoice->invoice_suffix }}
        </span>
        <span>{{ $invoice->customer->customer_name }}</span>
        @if($invoice->cancelled_at)
        <span class="text-danger">Invoice Cancelled</span>
        @endif
        <div>
            <a href="/invoices/{{ $invoice->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                VIEW DOC
            </a>
        </div>
    </li>
    @endif
    @endforeach
    @if($matches < 1) <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        No Match
        </li>
        @endif
</ul>