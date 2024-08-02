<div class="container">
    <h5>Top 10 Loser Scripts</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="75%">Name</th>
                <th width="25%">PNL</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $previousProfit = 0; 
                @endphp
                @if(count($bottom10scripts)>0)
                    @foreach($bottom10scripts as $loser)
                        @php
                            // Calculate growth direction
                            $growthClass = '';
                            if ($previousProfit !== null) {
                                if ($loser->totalCloseProfit > 0) {
                                    $growthClass = 'text-amount-success text-white'; // Increasing
                                } elseif ($loser->totalCloseProfit < 0) {
                                    $growthClass = 'text-amount-danger text-white'; // Decreasing
                                }
                            }
                            $previousProfit = $loser->totalCloseProfit
                        @endphp
                        <tr>
                            <td class="name-cell" title="{{$loser->currency->name}}">{{$loser->currency->name}}</td>
                            <td class="text-end">                               <span class="{{$growthClass}}">
                                    {{$loser->totalCloseProfit}}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">no records found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-3">
        
    </div>
</div>
