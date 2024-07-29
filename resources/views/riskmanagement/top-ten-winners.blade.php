<div class="table-responsive">
    <table class="table table-bordered table-striped w-100 align-middle fs-6" id="record-table">
        <thead>
        <tr>
            <th>Account Id</th>
            <th>Username</th>
            <th>Name</th>
            <th>PNL</th>
        </tr>
        </thead>
        <tbody>
            @foreach($topTenWinners as $winner)
                <tr>
                    <td>$winner->accountId</td>
                    <td>$winner->client->username</td>
                    <td>$winner->client->name</td>
                    <td>$winner->totalCloseProfit</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>