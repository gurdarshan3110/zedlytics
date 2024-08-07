<div class="container">
    <h5>Top Commissions</h5>
    <div class="table-responsive">
        <table class="w-100 align-middle fs-7" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="25%">Id</th>
                <th width="50%">Name</th>
                <th width="25%">Commission</th>
            </tr>
            </thead>
            <tbody>
                @if(count($topCommissions)>0)
                    @foreach($topCommissions as $winner)
                        <tr>
                            <td class="text-start">
                                {{$winner['accountId']}}
                            </td>
                            <td class="name-cell" title="{{ $winner['name'] }}">
                                {{$winner['name']}}
                            </td>
                            <td class="text-end">                            	<span class="">
                                    {{$winner['commissions']}}
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
    </div>
</div>
