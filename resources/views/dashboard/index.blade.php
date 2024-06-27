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
                {{$title}} @if(Auth::user()->user_type==\App\Models\User::USER_SUPER_ADMIN): Balance {{$totalBalance}}@endif

                (Withdrawal Requests : {{$withdrawRequests}})
                
            </h3>
        </div>
        
        
        <div class="row mt-2">
            @foreach($brands as $brand)
                @php
                    $bankAccountCodes = $brand->banks->pluck('account_code')->toArray();
                @endphp
                @if(array_intersect($bankAccountCodes, permissions()))
                    <h5 class="card-title mt-2 mb-2 p-2 bg-primary rounded text-light">Account Details for <strong>{{$brand->name}}</strong> <strong class="ms-2">(Balance: {{$brand->brandBalance()}})</strong></h5>
                    @foreach($brand->banks as $data)
                        @if(in_array($data->account_code, permissions())) 
                        <div class="col-md-2 mt-1">
                            <div class="card {{(($data->bankBalance()
                                <=$data->first_limit)?'bg-success':(($data->bankBalance()<=$data->second_limit)?'bg-warning':'bg-danger'))}} text-light">
                                <div class="card-body">
                                    <h6 class="card-title fs-7">{{$data->account_code}}</h6>
                                    <h6 class="card-footer ps-1">{{$data->bankBalance()}}</h6>
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
                    <h5 class="card-title mt-2 mb-2 p-2 bg-primary rounded text-light"><strong>{{$brand->name}}</strong> Financials</h5>
                    @if(in_array('dashboard charts', permissions()))
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">{{date('d/m/Y',strtotime($startDate))}} Financials</span><span class="text-end w-25">{{$brand->name}}</span></h5>
                                    <div class="row">
                                        <!-- First half of the card -->
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$startDate->toDateString()}}/{{$brand->id}}">
                                                <div class="card-text fw-bold deposit text-dark">
                                                    <div class="w-100 fw-bold">Deposits: {{ $todaysDeposit['count'] }}</div> 
                                                    <div class="w-100">{{ $todaysDeposit['deposit'] }} 
                                                        @if($todayEquityRecords!=null && $todaysDeposit['deposit']==$todayEquityRecords['deposit'])
                                                        <img src="{{asset('/assets/images/tick-icon.png')}}" class="icon float-end"/>
                                                        @else
                                                        <img src="{{asset('/assets/images/cross-icon.png')}}" class="icon float-end"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$startDate->toDateString()}}/{{$brand->id}}">
                                                <div class="card-text mt-3 fw-bold withdraw">
                                                    <div class="w-100 fw-bold">Withdraw: {{ $todaysWithdraw['count'] }}</div> 
                                                    <div class="w-100">{{ $todaysWithdraw['withdraw'] }}
                                                        @if($todayEquityRecords!=null && $todaysWithdraw['withdraw']==$todayEquityRecords['withdraw'])
                                                        <img src="{{asset('/assets/images/tick-icon.png')}}" class="icon float-end"/>
                                                        @else
                                                        <img src="{{asset('/assets/images/cross-icon.png')}}" class="icon float-end"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="card-text mt-3 fw-bold gap">
                                                <div class="w-100 fw-bold">Gap:</div> 
                                                <div class="w-100">{{ $todaysDeposit['deposit']- $todaysWithdraw['withdraw']}}</div>
                                            </div>
                                        </div>
                                        <!-- Second half of the card -->
                                        <div class="col-md-6">
                                            <canvas id="myPieChart{{$brand->id}}a" width="50" height="50"></canvas>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold equity text-dark">
                                                <div class="w-100 fw-bold">Equity:</div> 
                                                <div class="w-100">{{ $todayEquityRecords==null?0:$todayEquityRecords['equity'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold parking text-dark">
                                                <div class="w-100 fw-bold">Parking:</div> 
                                                <div class="w-100">{{ $todayParkings }}</div>
                                            </div>
                                        </div>
                                        @push('jsscript')
                                        <script>
                                            var todaysDeposits = @json($brand->todaysDeposits()); 
                                            var todaysWithdrawals = @json($brand->todaysWithdrawals()); 
                                            var difference = todaysDeposits['deposit'] - todaysWithdrawals['withdraw'];
                                            var id = {{$brand->id}};
                                            difference = difference.toFixed(2);
                                            var ctx = document.getElementById('myPieChart'+id+'a').getContext('2d');
                                            var myPieChart = new Chart(ctx, {
                                                type: 'pie',
                                                data: {
                                                    labels: ['Deposits', 'Withdrawals', 'Gap'],
                                                    datasets: [{
                                                        data: [todaysDeposits['deposit'], todaysWithdrawals['withdraw'], difference],
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">{{date('d/m/Y',strtotime($endDate))}} Financials</span><span class="text-end w-25">{{$brand->name}}</span></h5>
                                    <?php
                                    $deposits = $brand->depositsBetween($yesterdayStartDate,$yesterdayEndDate);
                                    $withdrawals = $brand->withdrawalsBetween($yesterdayStartDate,$yesterdayEndDate);
                                    $gap = $deposits['deposit'] - $withdrawals['withdraw'];
                                    $yesterdayEquityRecords = $brand->equityRecords($yesterdayStartDate->toDateString(),$yesterdayStartDate->toDateString());
                                    $yesterdayParkings = $brand->parkings($yesterdayStartDate->toDateString());
                                    ?>
                                    <div class="row">
                                        <!-- First half of the card -->
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$yesterdayStartDate->toDateString()}}/{{$brand->id}}">
                                                <div class="card-text fw-bold deposit text-dark">
                                                    <div class="w-100 fw-bold">Deposits: {{$deposits['count']}}</div> 
                                                    <div class="w-100">{{ $deposits['deposit'] }}
                                                        @if($yesterdayEquityRecords!=null && $deposits['deposit']==$yesterdayEquityRecords['deposit'])
                                                        <img src="{{asset('/assets/images/tick-icon.png')}}" class="icon float-end"/>
                                                        @else
                                                        <img src="{{asset('/assets/images/cross-icon.png')}}" class="icon float-end"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$yesterdayStartDate->toDateString()}}/{{$brand->id}}">
                                                <div class="card-text mt-3 fw-bold withdraw">
                                                    <div class="w-100 fw-bold">Withdraw: {{$withdrawals['count']}}</div> 
                                                    <div class="w-100">{{ $withdrawals['withdraw'] }}
                                                        @if($yesterdayEquityRecords!=null && $withdrawals['withdraw']==$yesterdayEquityRecords['withdraw'])
                                                        <img src="{{asset('/assets/images/tick-icon.png')}}" class="icon float-end"/>
                                                        @else
                                                        <img src="{{asset('/assets/images/cross-icon.png')}}" class="icon float-end"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="card-text mt-3 fw-bold gap">
                                                <div class="w-100 fw-bold">Gap:</div> 
                                                <div class="w-100">{{ $gap}}</div>
                                            </div>
                                        </div>
                                        <!-- Second half of the card -->
                                        <div class="col-md-6">
                                            <canvas id="myPieChart{{$brand->id}}b" width="50" height="50"></canvas>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold equity text-dark">
                                                <div class="w-100 fw-bold">Equity:</div> 
                                                <div class="w-100">{{ $yesterdayEquityRecords==null?0:$yesterdayEquityRecords['equity'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold parking text-dark">
                                                <div class="w-100 fw-bold">Parking:</div> 
                                                <div class="w-100">{{ $yesterdayParkings }}</div>
                                            </div>
                                        </div>
                                        @push('jsscript')
                                        <script>
                                            var todaysDeposits = @json($deposits['deposit']); 
                                            var todaysWithdrawals = @json($withdrawals['withdraw']); 
                                            var difference = todaysDeposits - todaysWithdrawals;
                                            var id = '{{$brand->id}}';
                                            difference = difference.toFixed(2);
                                            var ctx = document.getElementById('myPieChart'+id+'b').getContext('2d');
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
                                        </script>
                                        @endpush
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">Monthly Financials</span><span class="text-end w-25">{{$brand->name}}</span></h5>
                                    <?php
                                    $deposits = $brand->depositsBetween($monthStartDate,$monthEndDate);
                                    $withdrawals = $brand->withdrawalsBetween($monthStartDate,$monthEndDate);
                                    $gap = $deposits['deposit'] - $withdrawals['withdraw'];
                                    $monthEquityRecords = $brand->equityRecords($monthStartDate->toDateString(),$monthEndDate->toDateString());
                                    $monthParkings = $brand->parkingsupto($monthEndDate->toDateString());
                                    ?>
                                    <div class="row">
                                        <!-- First half of the card -->
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text fw-bold deposit text-dark">
                                                <div class="w-100 fw-bold">Deposits: {{$deposits['count']}}</div> 
                                                <div class="w-100">{{ $deposits['deposit'] }}</div>
                                            </div>
                                            <div class="card-text mt-3 fw-bold withdraw">
                                                <div class="w-100 fw-bold">Withdraw: {{$withdrawals['count']}}</div> 
                                                <div class="w-100">{{ $withdrawals['withdraw'] }}</div>
                                            </div>
                                            <div class="card-text mt-3 fw-bold gap">
                                                <div class="w-100 fw-bold">Gap:</div> 
                                                <div class="w-100">{{ $gap}}</div>
                                            </div>
                                        </div>
                                        <!-- Second half of the card -->
                                        <div class="col-md-6">
                                            <canvas id="myPieChart{{$brand->id}}c" width="50" height="50"></canvas>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold equity text-dark">
                                                <div class="w-100 fw-bold">Equity:</div> 
                                                <div class="w-100">{{ $monthEquityRecords==null?0:$monthEquityRecords['equity'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                            <div class="card-text mt-3 fw-bold parking text-dark">
                                                <div class="w-100 fw-bold">Parking:</div> 
                                                <div class="w-100">{{ $monthParkings }}</div>
                                            </div>
                                        </div>
                                        @push('jsscript')
                                        <script>
                                            var todaysDeposits = @json($deposits['deposit']); 
                                            var todaysWithdrawals = @json($withdrawals['withdraw']); 
                                            var difference = todaysDeposits - todaysWithdrawals;
                                            var id = '{{$brand->id}}';
                                            difference = difference.toFixed(2);
                                            var ctx = document.getElementById('myPieChart'+id+'c').getContext('2d');
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
                                        </script>
                                        @endpush
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            @endforeach
        </div>
        @if(in_array('dashboard charts', permissions()))
        <div class="row mt-2">
            <h5 class="card-title mt-2 mb-2 p-2 bg-primary rounded text-light">Complete Overview</strong></h5>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">{{date('d/m/Y',strtotime($startDate))}} Financials</span></h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$startDate->toDateString()}}/all">
                                    <div class="card-text fw-bold deposit text-dark">
                                        <div class="w-100 fw-bold">Deposits: {{ $todaysDeposits['count'] }}</div> 
                                        <div class="w-100">{{ $todaysDeposits['deposit'] }}</div>
                                    </div>
                                </a>
                                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$startDate->toDateString()}}/all">
                                    <div class="card-text mt-3 fw-bold withdraw">
                                        <div class="w-100 fw-bold">Withdraw: {{ $todaysWithdrawals['count'] }}</div> 
                                        <div class="w-100">{{ $todaysWithdrawals['withdraw'] }}</div>
                                    </div>
                                </a>
                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $todaysDeposits['deposit'] - $todaysWithdrawals['withdraw'] }}</div>
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
                        <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">{{date('d/m/Y',strtotime($endDate))}} Financials</span></h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$endDate->toDateString()}}/all">
                                    <div class="card-text fw-bold deposit">
                                        <div class="w-100 fw-bold">Deposits: {{ $yesterdayDeposits['count'] }}</div> 
                                        <div class="w-100">{{ $yesterdayDeposits['deposit'] }}</div>
                                    </div>
                                </a>
                                <a class="text-decoration-none text-dark cursor-pointer" href="/financial-details/{{$endDate->toDateString()}}/all">
                                    <div class="card-text mt-3 fw-bold withdraw">
                                        <div class="w-100 fw-bold">Withdraw: {{ $yesterdayWithdrawals['count'] }}</div> 
                                        <div class="w-100">{{ $yesterdayWithdrawals['withdraw'] }}</div>
                                    </div>
                                </a>
                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $yesterdayDeposits['deposit'] - $yesterdayWithdrawals['withdraw'] }}</div>
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
                        <h5 class="card-title bg-success p-2 rounded text-light w-100 d-flex"><span class="text-start w-75">Monthly Financials</span></h5>
                        <div class="row">
                            <!-- First half of the card -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="card-text fw-bold deposit">
                                    <div class="w-100 fw-bold">Deposits: {{ $monthlyDeposits['count'] }}</div> 
                                    <div class="w-100">{{ $monthlyDeposits['deposit'] }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold withdraw">
                                    <div class="w-100 fw-bold">Withdraw: {{ $monthlyWithdrawals['count'] }}</div> 
                                    <div class="w-100">{{ $monthlyWithdrawals['withdraw'] }}</div>
                                </div>

                                <div class="card-text mt-3 fw-bold gap">
                                    <div class="w-100 fw-bold">Gap:</div> 
                                    <div class="w-100">{{ $monthlyDeposits['deposit'] -$monthlyWithdrawals['withdraw'] }}</div>
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

    var todaysDeposits = @json($todaysDeposits['deposit']); 
    var todaysWithdrawals = @json($todaysWithdrawals['withdraw']); 
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

    var yesterdayDeposits = @json($yesterdayDeposits['deposit']); 
    var yesterdayWithdrawals = @json($yesterdayWithdrawals['withdraw']); 
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

    var monthlyDeposits = @json($monthlyDeposits['deposit']); 
    var monthlyWithdrawals = @json($monthlyWithdrawals['withdraw']); 
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
