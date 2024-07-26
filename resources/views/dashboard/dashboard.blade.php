@extends('includes.app')

@section('content')
@push('jsscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function autoReload() {
        setTimeout(function() {
            location.reload();
        }, 120000); 
    }
    window.onload = autoReload;
</script>
@endpush
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}} @if(Auth::user()->role=='Accounts Manager'): Balance {{$totalBalance}}@endif
            </h3>
        </div>
        
        
        <div class="row mt-2">
            @foreach($brands as $brand)
                @php
                    $bankAccountCodes = $brand->banks->pluck('account_code')->toArray();
                @endphp
                @if(array_intersect($bankAccountCodes, permissions()))
                    <h5 class="card-title mt-2 mb-2 p-2 bg-primary text-light">Account Details for <strong>{{$brand->name}}</strong></h5>
                    @foreach($brand->banks as $data)
                        @if(in_array($data->account_code, permissions())) 
                        <div class="col-md-2 mt-1">
                            <div class="card {{(($data->bankBalance()
                                <=$data->first_limit)?'bg-brand-primary':(($data->bankBalance()<=$data->second_limit)?'bg-brand-warning':'bg-brand-danger'))}} text-light">
                                <div class="card-body">
                                    <h6 class="card-title fs-8">{{$data->account_code}}</h6>
                                    <h6 class=" ps-1">{{$data->bankBalance()}}</h6>
                                </div>
                            </div>
                            @if($data->bankBalance() >= $data->first_limit)
                            @push('jsscript')
                            <script>
                                setTimeout(() => {
                                    firstAlert();
                                }, 2000);
                            </script>
                            @endpush('jsscript')
                            @endif
                            @if($data->bankBalance() >= $data->second_limit)
                            @push('jsscript')
                            <script>
                                setTimeout(() => {
                                    secondAlert();
                                }, 2000);
                            </script>
                            @endpush('jsscript')
                            @endif
                        </div>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
        <div class="row mt-2">
            @foreach($brands as $brand)
                @php
                    $bankAccountCodes = $brand->banks->pluck('account_code')->toArray();
                    $deposits = 0;
                    $withdrawals = 0;
                    $gap = 0;
                    $todayEquityRecords = $brand->equityRecords($startDate->toDateString(),$startDate->toDateString());
                    $todayParkings = $brand->parkings($startDate->toDateString());
                    $todaysWithdraw = $brand->todaysWithdrawals();
                    $todaysDeposit = $brand->todaysDeposits();
                @endphp
                @if(array_intersect($bankAccountCodes, permissions()))
                    <!-- <h5 class="card-title mt-2 mb-2 p-2 bg-primary  text-light"><strong>{{$brand->name}}</strong> Financials</h5> -->
                    @if(in_array('dashboard charts', permissions()))
                    <div class="row mt-2">
                        @php
                            $todayDeposit = $todaysDeposit['deposit'];
                            $todayDepositCount = $todaysDeposit['count'];
                            $todayWithdraw = $todaysWithdraw['withdraw'];
                            $todayWithdrawCount = $todaysWithdraw['count'];
                            $todayActualDeposit = $todayEquityRecords['deposit'];
                            $todayActualWithdraw = $todayEquityRecords['withdraw'];
                            $todayEquity = $todayEquityRecords['equity'];
                            $todayGap = $todayDeposit - $todayWithdraw;
                            $todayParking = $todayParkings;
                     
                            $deposits = $brand->depositsBetween($yesterdayStartDate,$yesterdayEndDate);
                            $withdrawals = $brand->withdrawalsBetween($yesterdayStartDate,$yesterdayEndDate);
                            $gap = $deposits['deposit'] - $withdrawals['withdraw'];
                            $yesterdayEquityRecords = $brand->equityRecords($yesterdayStartDate->toDateString(),$yesterdayStartDate->toDateString());
                            $yesterdayParkings = $brand->parkings($yesterdayStartDate->toDateString());

                            $yesterDeposit = $deposits['deposit'];
                            $yesterDepositCount = $deposits['count'];
                            $yesterWithdraw = $withdrawals['withdraw'];
                            $yesterWithdrawCount = $withdrawals['count'];
                            $yesterActualDeposit = $yesterdayEquityRecords['deposit'];
                            $yesterActualWithdraw = $yesterdayEquityRecords['withdraw'];
                            $yesterEquity = $yesterdayEquityRecords['equity'];
                            $yesterGap = $yesterDeposit - $yesterWithdraw;
                            $yesterParking = $yesterdayParkings;


                            $dayBdeposits = $brand->depositsBetween($dayBeforeYesterdayStartDate,$dayBeforeYesterdayEndDate);
                            $dayBwithdrawals = $brand->withdrawalsBetween($dayBeforeYesterdayStartDate,$dayBeforeYesterdayEndDate);
                            $dayBgap = $deposits['deposit'] - $withdrawals['withdraw'];
                            $dayByesterdayEquityRecords = $brand->equityRecords($dayBeforeYesterdayStartDate->toDateString(),$dayBeforeYesterdayEndDate->toDateString());
                            $dayByesterdayParkings = $brand->parkings($dayBeforeYesterdayStartDate->toDateString());

                            $daybefYesDeposit = $dayBdeposits['deposit'];
                            $daybefYesWithdraw = $dayBwithdrawals['withdraw'];
                            $daybefYesEquity = $dayByesterdayEquityRecords['equity'];
                            $daybefYesGap = $daybefYesDeposit - $daybefYesWithdraw;
                            $daybefYesParking = $dayByesterdayParkings;

                        @endphp
                        <div class="col-md-6">
                            <div class="card bg-fff">
                                <div class="card-body financial">
                                    <h5 class="card-title mb-0 pb-0 text-dark w-100 d-flex">{{$brand->name}} Financials</h5>
                                    <p class="text-dark">{{date('d M Y l',strtotime($startDate))}}</p>
                                    {!! financialCard($brand->id,$startDate,$todayDeposit,$todayWithdraw,$todayGap,$todayParking,$todayEquity,$todayActualDeposit,$todayActualWithdraw,$todayDepositCount,$todayWithdrawCount,$yesterDeposit,$yesterWithdraw,$yesterGap,$yesterParking,$yesterEquity) !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-fff">
                                <div class="card-body financial">
                                    <h5 class="card-title mb-0 pb-0 text-dark w-100 d-flex">{{$brand->name}} Financials</h5>
                                    <p class="text-dark">{{date('d M Y l',strtotime($yesterdayStartDate))}}</p>
                                    {!! financialCard($brand->id,$yesterdayStartDate,$yesterDeposit,$yesterWithdraw,$yesterGap,$yesterParking,$yesterEquity,$yesterActualDeposit,$yesterActualWithdraw,$yesterDepositCount,$yesterWithdrawCount,$daybefYesDeposit,$daybefYesWithdraw,$daybefYesGap,$daybefYesParking,$daybefYesEquity) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif
                @endif
            @endforeach
        </div>
        
    </div>
    <audio id="first-alert" src="{{asset('/assets/alerts/first-alert.wav')}}" preload="auto"></audio>
    <audio id="second-alert" src="{{asset('/assets/alerts/second-alert.wav')}}" preload="auto"></audio>
</main>
@push('jsscript')
<script>
    function firstAlert(){
        var alertSound = document.getElementById('first-alert');
        //alertSound.play();
    }
    function secondAlert(){
        var alertSound = document.getElementById('second-alert');
        //alertSound.play();
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
