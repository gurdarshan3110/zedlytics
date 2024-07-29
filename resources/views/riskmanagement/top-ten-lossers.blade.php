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
            @foreach($topTenLossers as $losser)
                <tr>
                    <td>$losser->accountId</td>
                    <td>$losser->client->username</td>
                    <td>$losser->client->name</td>
                    <td>$losser->totalCloseProfit</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>