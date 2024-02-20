<ul class="list-group text-dark">
    <?php
        $matches = 0;
    ?>
    @foreach($coatingJobs as $coatingJob)
    @if(str_contains($coatingJob->coating_suffix, $number))
    <?php
        $matches += 1;
    ?>
    <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
        <span>{{ $coatingJob->coating_prefix }}{{ $coatingJob->coating_suffix }}</span>
        <span>{{ $coatingJob->customer->customer_name }}</span>
        <div>
            <a target="_blank" class="btn btn-sm btn-info" href="/coatingjobs/{{ $coatingJob->id }}">
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