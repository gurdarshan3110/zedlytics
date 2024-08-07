<div class="container">
    <h5>Top 10 Loser Parents</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="25%">Id</th>
                <th width="50%">Name</th>
                <th width="25%">PNL</th>
            </tr>
            </thead>
            <tbody>
            	@php
                    $previousProfit = 0; 
                @endphp
                @if(count($topLoserParents)>0)
                    @foreach($topLoserParents as $winner)
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
                            $previousProfit = $winner['totalCloseProfit']
                        @endphp
                        <tr>
                            <td class="text-start">
                                <a href="/child-details/{{$winner['id']}}" target="_blank" class="text-dark text-decoration-none">{{$winner['accountId']}}</a>
                            </td>
                            <td class="name-cell" title="{{ $winner['name'] }}">
                                <a href="/child-details/{{$winner['id']}}" target="_blank" class="text-dark text-decoration-none">{{$winner['name']}}</a>
                            </td>
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
    <div class="d-flex justify-content-end">
        <a href="{{ route('more-parents', ['date' => $date]) }}" id="view-more-scripts" target="_blank" class="fs-7">View More</a>
    </div>
</div>
