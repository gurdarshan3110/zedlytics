@extends('includes.app')

@section('content')
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
        </div>
        <div class="row">
            <!-- Cards for Total Balance, Today's Deposits, Today's Withdrawals -->
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Balance</h5>
                                <p class="card-text">{{ $totalBalance }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Today's Deposits</h5>
                                <p class="card-text">{{ $yesterdayDeposits }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Today's Withdrawals</h5>
                                <p class="card-text">{{ $todaysWithdrawals }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Difference</h5>
                        <p class="card-text">{{ $todaysDeposits-$todaysWithdrawals }}</p>
                        <canvas id="myPieChart" width="50" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Yesterday's Deposits</h5>
                                <p class="card-text">{{ $yesterdayDeposits }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Yesterday's Withdrawals</h5>
                                <p class="card-text">{{ $yesterdayWithdrawals }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Yesterday's Difference</h5>
                        <p class="card-text">{{ $yesterdayDeposits-$yesterdayWithdrawals }}</p>
                        <canvas id="myPieChart2" width="50" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Bar charts for Today's Credit, Debit, and Balance -->
            <div class="col-md-6">
                <h5 class="card-title">Today's Chart</h5>
                <canvas id="todayChart"></canvas>
            </div>
            <!-- Bar charts for Last 7 Days Credit, Debit, and Balance -->
            <div class="col-md-6">
                <h5 class="card-title">Bank's Chart</h5>
                <canvas id="bankChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <!-- Bar charts for Last Month Credit, Debit, and Balance -->
            <div class="col-md-6">
                <h5 class="card-title">Week's Chart</h5>
                <canvas id="weekChart"></canvas>
            </div>
            <div class="col-md-6">
                <h5 class="card-title">Month's Chart</h5>
                <canvas id="monthChart"></canvas>
            </div>

        </div>
    </div>
</main>
@push('jsscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for charts
    const todayData = @json($todayData);
    const weekData = @json($weekData);
    const monthData = @json($monthData);
    const bankData = @json($bankData);

    function generateChartData(data) {
        return {
            labels: data.map(item => item.account_code),
            datasets: [
                {
                    label: 'Deposit',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    data: data.map(item => item.credit)
                },
                {
                    label: 'Withdraw',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    data: data.map(item => item.debit)
                },
                {
                    label: 'Balance',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    data: data.map(item => item.balance)
                }
            ]
        };
    }

    // Create a chart for today
    new Chart(document.getElementById('todayChart'), {
        type: 'bar',
        data: generateChartData(todayData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Create a chart for week
    new Chart(document.getElementById('weekChart'), {
        type: 'bar',
        data: generateChartData(weekData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Create a chart for month
    new Chart(document.getElementById('monthChart'), {
        type: 'bar',
        data: generateChartData(monthData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('bankChart'), {
        type: 'bar',
        data: generateChartData(bankData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var todaysDeposits = @json($todaysDeposits); 
    var todaysWithdrawals = @json($todaysWithdrawals); 
    var difference = todaysDeposits - todaysWithdrawals;
    difference = difference.toFixed(2);
    var ctx = document.getElementById('myPieChart').getContext('2d');
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Deposits', 'Withdrawals', 'Difference'],
            datasets: [{
                data: [todaysDeposits, todaysWithdrawals, difference],
                backgroundColor: ['#36a2eb', '#ff6384', '#ffcd56'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.raw !== null) {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    var yesterdayDeposits = @json($yesterdayDeposits); 
    var yesterdayWithdrawals = @json($yesterdayWithdrawals); 
    var yesterdaydifference = yesterdayDeposits - yesterdayWithdrawals;
    yesterdaydifference = yesterdaydifference.toFixed(2);
    var ctx = document.getElementById('myPieChart2').getContext('2d');
    var myPieChart2 = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Deposits', 'Withdrawals', 'Difference'],
            datasets: [{
                data: [yesterdayDeposits, yesterdayWithdrawals, yesterdaydifference],
                backgroundColor: ['#36a2eb', '#ff6384', '#ffcd56'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.raw !== null) {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
