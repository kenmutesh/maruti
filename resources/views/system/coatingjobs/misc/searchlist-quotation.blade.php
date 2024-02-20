<ul class="list-group text-dark">
    <?php
        $matches = 0;
    ?>
    @foreach($quotations as $quotation)
    @if(str_contains($quotation->quotation_suffix, $number))
    <?php
        $matches += 1;
    ?>
    <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        <span>{{ $quotation->quotation_prefix }}{{ $coatingJob->quotation_suffix }}</span>
        <span>{{ $quotation->customer->customer_name }}</span>
        <div>
            <a target="_blank" class="btn btn-sm btn-info" href="/coatingjobs/{{ $quotation->id }}">
                <span class="d-block w-100 h-100" data-toggle="tooltip" title="Print Out Job Card">
                    QUOTATION DOC
                </span>
            </a>
        </div>
    </li>
    @endif
    @endforeach
    @if($matches < 1)
    <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        No Match
    </li>
    @endif
</ul>