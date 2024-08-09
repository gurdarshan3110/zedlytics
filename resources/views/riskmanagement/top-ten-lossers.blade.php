<div class="container">
    <h5>Top 10 Losers</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="22%">Id</th>
                <th width="56%">Name</th>
                <th width="22%">PNL</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $previousProfit = 0; 
                @endphp
                @if(count($topTenLossers)>0)
                    @foreach($topTenLossers as $winner)
                        @php
                            // Calculate growth direction
                            $growthClass = '';
                            if ($previousProfit !== null) {
                                if ($winner->totalCloseProfit < 0) {
                                    $growthClass = 'text-amount-danger text-white'; // Increasing
                                } elseif ($winner->totalCloseProfit > 0) {
                                    $growthClass = 'text-amount-success text-white'; // Decreasing
                                }
                            }
                            $previousProfit = $winner->totalCloseProfit; 
                            $txtClass='';
                            if($winner->highlight==1){
                                $txtClass='text-danger';
                            }
                        @endphp
                        <tr>
                            <td class="text-start {{$txtClass}}">{{$winner->accountId}}</td>
                            <td class="name-cell" title="{{ $winner->client->name }}">{{$winner->client->name}}</td>
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
    <div class="d-flex justify-content-end">
        <a href="{{ route('moreWL', ['status' => 'losers','date' => $date]) }}" id="view-more-losers" target="_blank" class="fs-7">View More</a>
    </div>
</div>
