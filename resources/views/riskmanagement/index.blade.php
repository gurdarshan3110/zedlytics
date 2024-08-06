@extends('includes.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                {{$title}} @if(Auth::user()->user_type=='super_admin' || Auth::user()->role=='Super Admin')(Total PL : {{array_sum(array_column($markets, 'totalCloseProfit'))}})@endif
            </h3>
            <h3 class="mt-4 d-flex justify-content-end w-25">
                <input type="date" id="date" value="{{$date}}" class="form-control"/> 
            </h3>
        </div>
    
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="row">
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center text-primary">
                        <i class="fas fa-users card-icon text-center"></i>
                        
                        <div class="fs-3 text-center">
                            {{$activeUsers}}
                        </div>
                        <p class="fs-6 text-center mt-1 mb-0">Active Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center text-success">
                        <i class="fas fa-angle-double-up card-icon text-center"></i>
                        
                        <div class="fs-3 text-center">
                            {{$profitCount}}
                        </div>
                        <p class="fs-6 text-center mt-1 mb-0">Winners</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center text-danger">
                        <i class="fas fa-angle-double-down card-icon text-center"></i>
                        
                        <div class="fs-3 text-center">
                            {{$lossCount}}
                        </div>
                        <p class="fs-6 text-center mt-1 mb-0">Losers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center">
                        <div class="card-height d-flex justify-content-center text-primary">
                            <canvas id="profitLossChart"></canvas>
                        </div>
                        <p class=" text-primary fs-6 text-center mt-1 mb-0">Ratio</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center card-height-single d-flex justify-content-center text-primary align-items-end">
                        <canvas id="pllineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mt-1">
                <div class="card bg-fff text-light">
                    <div class="card-body text-center card-height-single d-flex justify-content-center text-primary align-items-end">
                        <canvas id="plbarChart"></canvas>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row mt-1">
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-winners')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-lossers')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-winner-parents')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-loser-parents')
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.markets')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-scripts')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.bottom-ten-scripts')
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card bg-fff p-2">
                    @include($directory.'.top-ten-loser-parents')
                </div>
            </div>
        </div>
    </div>
</main>
@push('jsscript')
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
    const profitCount = {{ $profitCount }};
    const lossCount = {{ $lossCount }};
    
    const ctx = document.getElementById('profitLossChart').getContext('2d');
    const profitLossChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Profits', 'Losses'],
            datasets: [{
                label: 'Profit and Loss',
                data: [profitCount, lossCount],
                backgroundColor: ['#28a745', '#dc3545'], 
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false, // Hide the legend
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
    const ctxLine = document.getElementById('pllineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Profits', 'Losses'],
            datasets: [{
                label: 'Profit and Loss',
                data: [profitCount, lossCount],
                backgroundColor: 'rgba(28, 200, 138, 0.2)', // Light green for profits
                borderColor: '#28a745', // Green for profits
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const ctxBar = document.getElementById('plbarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Profits', 'Losses'],
            datasets: [{
                label: 'Profit and Loss',
                data: [profitCount, lossCount],
                backgroundColor: ['#28a745', '#dc3545'], // Green for profits, red for losses
                borderColor: ['#1c7430', '#b52d2a'], // Darker green and red for borders
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection  


