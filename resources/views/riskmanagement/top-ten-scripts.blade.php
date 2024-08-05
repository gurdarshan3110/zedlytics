<div class="container">
    <h5>Top Winner Scripts</h5>
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
                @if(count($top10scripts)>0)
                    @foreach($top10scripts as $winner)
                    	@php
                            // Calculate growth direction
                            $growthClass = '';
                            if ($previousProfit !== null) {
                                if ($winner->totalCloseProfit > 0) {
                                    $growthClass = 'text-amount-success text-white'; // Increasing
                                } elseif ($winner->totalCloseProfit < 0) {
                                    $growthClass = 'text-amount-danger text-white'; // Decreasing
                                }
                            }
                            $previousProfit = $winner->totalCloseProfit
                        @endphp
                        <tr>
                            @if($winner->currencyId==562)
                                <td class="name-cell" title="{{$winner->currency->name}}"><a href="/market-details/{{$winner->currencyId}}" target="_blank" class="text-dark text-decoration-none">{{$winner->currency->name}}</a></td>
                            @else
                                <td class="name-cell" title="{{$winner->currency->name}}"><a href="/client-details/{{$winner->currencyId}}" target="_blank" class="text-dark text-decoration-none">{{$winner->currency->name}}</a></td>
                            @endif
                            <td class="text-end">                            	
                                <span class="{{$growthClass}}">
                                    {{$winner->totalCloseProfit}}
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
