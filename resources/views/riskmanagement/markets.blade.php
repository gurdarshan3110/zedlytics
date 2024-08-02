<div class="container">
    <h5>Markets</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="market-winners">
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
                @if(count($markets)>0)
                    @foreach($markets as $winner)
                    	@php
                            // Calculate growth direction
                            $growthClass = '';
                            if ($previousProfit !== null) {
                                if ($winner['totalCloseProfit'] > 0) {
                                    $growthClass = 'text-amount-success text-white'; // Increasing
                                } elseif ($winner['totalCloseProfit'] < 0) {
                                    $growthClass = 'text-amount-danger text-white'; // Decreasing
                                }
                            }
                        @endphp
                        <tr>
                            <td class="name-cell" title="{{$winner['name']}}">{{$winner['name']}}</td>
                            <td class="text-end">                            	<span class="{{$growthClass}}">
                                    {{$winner['totalCloseProfit']}}
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
