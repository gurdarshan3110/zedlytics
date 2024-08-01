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
                @if(count($topTenWinners)>0)
                    @foreach($topTenWinners as $winner)
                        <tr>
                            <td class="text-start">{{$winner->accountId}}</td>
                            <td class="name-cell" title="{{ $winner->client->name }}">{{$winner->client->name}}</td>
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
        <a href="{{ route('moreWL', ['status' => 'winners','date' => $date]) }}" id="view-more-winners" target="_blank" class="fs-7">View More</a>
    </div>
</div>
