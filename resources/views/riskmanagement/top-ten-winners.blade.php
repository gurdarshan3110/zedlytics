<div class="container">
    <h5>Top 10 Winners</h5>
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
                @if(count($topTenWinners)>0)
                    @foreach($topTenWinners as $winner)
                        @php
                            // Calculate growth direction
                            $growthClass = '';
                            if ($previousProfit !== null) {
                                if ($winner->totalCloseProfit > 0) {
                                    $growthClass = 'text-amount-success text-white'; // Increasing
                                } elseif ($winner->totalCloseProfit < $previousProfit) {
                                    $growthClass = 'text-amount-danger text-white'; // Decreasing
                                }
                            }
                            $previousProfit = $winner->totalCloseProfit; // Update previous profit
                            $txtClass='';
                            if($winner->client->highlight==true){
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
        <a href="{{ route('moreWL', ['status' => 'winners','date' => $date]) }}" id="view-more-winners" target="_blank" class="fs-7">View More</a>
    </div>
</div>
