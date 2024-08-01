@extends('includes.app')

@section('content')
<script>
    function autoReload() {
        setTimeout(function() {
            location.reload();
        }, 120000); 
    }
    window.onload = autoReload;
</script>
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-75">
                {{$title}}
            </h3>
            <h3 class="mt-4 d-flex justify-content-end w-25">
                <input type="date" id="date" value="{{$date}}" class="form-control"/> 
            </h3>
        </div>
    
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-winners')
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-lossers')
                </div>
            </div>
        </div>
    </div>
</main>
<script>
$(document).ready(function() {
    $('#date').on('change', function() {
        var date = $(this).val();
        $.ajax({
            url: '{{ route("trx-logs") }}',
            type: 'GET',
            data: { date: date },
            success: function(data) {
                updateResults(data.topTenWinners, data.topTenLosers);
                updateViewMoreLinks(date);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });

    function updateResults(winners, losers) {
         $('#top-table-winners tbody').empty();
        $('#top-table-losers tbody').empty();

        // Update winners table
        if (winners.length > 0) {
            $.each(winners, function(index, winner) {
                console.log(winner);
                $('#top-table-winners tbody').append(
                    '<tr>' +
                        '<td>' + winner.accountId + '</td>' +
                        '<td>' + (winner.client ? winner.client.name : '') + '</td>' +
                        '<td>' + winner.totalCloseProfit + '</td>' +
                    '</tr>'
                );
            });
        } else {
            $('#top-table-winners tbody').append('<tr><td colspan="2">No winners found for this date.</td></tr>');
        }

        // Update losers table
        if (losers.length > 0) {
            $.each(losers, function(index, loser) {
                $('#top-table-losers tbody').append(
                    '<tr>' +
                        '<td>' + loser.accountId + '</td>' +
                        '<td>' + (loser.client ? loser.client.name : '') + '</td>' +
                        '<td>' + loser.totalCloseProfit + '</td>' +
                    '</tr>'
                );
            });
        } else {
            $('#top-table-losers tbody').append('<tr><td colspan="3">No losers found for this date.</td></tr>');
        }
    }
    function updateViewMoreLinks(date) {
        $('#view-more-winners').attr('href', '{{ route("moreWL", ["status" => "winners"]) }}' + '&date=' + date);
        $('#view-more-losers').attr('href', '{{ route("moreWL", ["status" => "losers"]) }}' + '&date=' + date);
    }
});
</script>
@endsection  

