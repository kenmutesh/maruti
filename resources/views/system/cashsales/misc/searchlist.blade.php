<ul class="list-group text-dark">
    <?php
    $matches = 0;
    ?>
    @foreach($cashSales as $cashSale)
    @if(str_contains($cashSale->cash_sale_suffix, $number) || str_contains($cashSale->ext_cash_sale_suffix, $number))
    <?php
    $matches += 1;
    ?>
    <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        <span>
            @if($cashSale->external)
            (EXT) {{ $cashSale->ext_cash_sale_prefix }}{{ $cashSale->ext_cash_sale_suffix }}
            @else
            {{ $cashSale->cash_sale_prefix }}{{ $cashSale->cash_sale_suffix }}
            @endif
        </span>
        <span>{{ $cashSale->customer->customer_name }}</span>
        @if($cashSale->cancelled_at)
        <span class="text-danger">Cash Sale Cancelled</span>
        @endif
        <div>
            <a href="/cashsales/{{ $cashSale->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
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