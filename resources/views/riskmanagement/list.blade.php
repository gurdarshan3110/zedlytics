<div class="container">
    <div class="table-responsive">
        <table class="w-100 align-middle fs-6 table table-bordered" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="10%">Id</th>
                <th width="20%">Username</th>
                <th width="30%">Parent</th>
                <th width="30%">Name</th>
                <th width="10%">PNL</th>
            </tr>
            </thead>
            <tbody>
                @if(count($data)>0)
                    @foreach($data as $winner)
                        <tr>
                            <td class="text-start">{{$winner->accountId}}</td>
                            <td>{{$winner->client->username}}</td>
                            <td>{{$winner->client->parent->name}}</td>
                            <td>{{$winner->client->name}}</td>
                            <td class="text-end">{{$winner->totalCloseProfit}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">no records found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
