<div class="container">
    <h5>Top 10 Losers</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="20%">Id</th>
                <th width="60%">Name</th>
                <th width="20%">PNL</th>
            </tr>
            </thead>
            <tbody>
                @if(count($topTenLossers)>0)
                    @foreach($topTenLossers as $winner)
                        <tr>
                            <td class="text-start">{{$winner->accountId}}</td>
                            <td>{{$winner->client->name}}</td>
                            <td class="text-end">{{$winner->totalCloseProfit}}</td>
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
