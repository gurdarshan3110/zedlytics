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
                @if(count($topLoserParents)>0)
                    @foreach($topLoserParents as $winner)
                        <tr>
                            <td class="text-start">{{$winner['accountId']}}</td>
                            <td>{{$winner['name']}}</td>
                            <td class="text-end">{{$winner['totalCloseProfit']}}</td>
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
