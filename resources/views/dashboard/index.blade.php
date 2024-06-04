@extends('includes.app')

@section('content')
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}} @if(Auth::user()->user_type==\App\Models\User::USER_SUPER_ADMIN): Balance {{$totalBalance}}@endif
            </h3>
        </div>
        <div class="row mt-2">
            <h5 class="card-title">Account Details</h5>
            @foreach($banks as $data)
            @if(in_array($data->account_code, permissions())) 
            <div class="col-md-2 mt-1">
                <div class="card {{(($data->bankBalance()<=100000)?'bg-success':(($data->bankBalance()<=200000)?'bg-warning':'bg-danger'))}} text-light">
                    <div class="card-body">
                        <h6 class="card-title fs-7">{{$data->account_code}}</h6>
                        <h6 class="card-footer ps-1">{{$data->bankBalance()}}</h6>
                    </div>
                </div>
                @if($data->bankBalance() >= $data->first_limit)
                <div onload="firstAlert();"></div>
                @endif
                @if($data->bankBalance() >= $data->second_limit)
                    <div onload="secondAlert();"></div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
        @if(in_array('dashboard charts', permissions()))
        <div class="row mt-2">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title bg-success p-2 rounded text-light text-center">{{date('d/m/Y',strtotime($startDate))}} Financials</h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="card-text fw-bold deposit">
                                    <div class="w-100 fw-bold">Deposits:</div> 
                                    <div class="w-100">{{ $todaysDeposits }}</div>
                                </div>
                                
                                <div class="card-text mt-3 fw-bold withdraw">
                                    <div class="w-100 fw-bold">Withdraw:</div> 
                                    <div class="w-100">{{ $todaysWithdrawals }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $todaysDeposits - $todaysWithdrawals }}</div>
                                </div>
                            </div>
                            <!-- Second half of the card -->
                            <div class="col-md-6">
                                <canvas id="myPieChart" width="50" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title bg-primary rounded p-2 text-light text-center">{{date('d/m/Y',strtotime($endDate))}} Financials</h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="card-text fw-bold deposit">
                                    <div class="w-100 fw-bold">Deposits:</div> 
                                    <div class="w-100">{{ $yesterdayDeposits }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold withdraw">
                                    <div class="w-100 fw-bold">Withdraw:</div> 
                                    <div class="w-100">{{ $yesterdayWithdrawals }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $yesterdayDeposits -$yesterdayWithdrawals }}</div>
                                </div>
                            </div>
                            <!-- Second half of the card -->
                            <div class="col-md-6">
                                <canvas id="myPieChart2" width="50" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title bg-secondary p-2 rounded text-light text-center">Monthly Financials</h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div clas-tex3 fw-bold deposit">
                                    <div class="w-100 fw-bold">Deposits:</div> 
                                    <div class="w-100">{{ $monthlyDeposits }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold withdraw">
                                    <div class="w-100 fw-bold">Withdraw:</div> 
                                    <div class="w-100">{{ $monthlyWithdrawals }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $monthlyDeposits -$monthlyWithdrawals }}</div>
                                </div>
                            </div>
                            <!-- Second half of the card -->
                            <div class="col-md-6">
                                <canvas id="myPieChart3" width="50" height="50"></canvas>
                            </div>
                        </div>
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
        @endif
    </div>
    <audio id="first-alert" src="{{asset('/assets/alerts/first-alert.wav')}}" autoplay></audio>
    <audio id="second-alert" src="{{asset('/assets/alerts/second-alert.wav')}}" autoplay></audio>
</main>
@push('jsscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function firstAlert(){
        var alertSound = document.getElementById('first-alert');
        alertSound.play();
    }
    function secondAlert(){
        var alertSound = document.getElementById('second-alert');
        alertSound.play();
    }
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
            labels: ['Deposits', 'Withdrawals', 'Gap'],
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
            labels: ['Deposits', 'Withdrawals', 'Gap'],
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

    var monthlyDeposits = @json($monthlyDeposits); 
    var monthlyWithdrawals = @json($monthlyWithdrawals); 
    var monthlydifference = monthlyDeposits - monthlyWithdrawals;
    monthlydifference = monthlydifference.toFixed(2);
    var ctx = document.getElementById('myPieChart3').getContext('2d');
    var myPieChart3 = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Deposits', 'Withdrawals', 'Gap'],
            datasets: [{
                data: [monthlyDeposits, monthlyWithdrawals, monthlydifference],
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
