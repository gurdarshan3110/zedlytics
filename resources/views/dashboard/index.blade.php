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
    @if(Auth::user()->role=='Partner')
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                FUTURES
            </h3>
        </div>
        <div class="row mt-2 mb-2">
            <div class="table-responsive">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@0.9.15/dist/css/bootstrap-multiselect.css">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="filterParent">Filter by Script Parent:</label>
                        <select id="filterParent" class="form-control" multiple>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="filterName">Filter by Script Name:</label>
                        <select id="filterName"  class="form-control" multiple>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                </div>

                <table id="positionsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sno</th>
                            <th>Script Parent</th>
                            <th>Script Name</th>
                            <th>Long Deals</th>
                            <th>Long Qty</th>
                            <th>Short Deals</th>
                            <th>Short Qty</th>
                            <th>Net Qty</th>
                            <th>Last Change</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $serial=1;
                        @endphp
                        @foreach($positions as $k => $position)
                        <tr>
                            <td>{{ $serial }}</td>
                            <td>{{ $position['parent'] }}</td>
                            <td><a href="/segregate-positions/{{$position['currency_id']}}" class="text-decoration-none text-black text-decoration-underline" target="_blank">{{ $position['currency_name'] }}</a></td>
                            <td>{{ $position['longDeals'] }}</td>
                            <td>{{ $position['longQty'] }}</td>
                            <td>{{ $position['shortDeals'] }}</td>
                            <td>{{ $position['shortQty'] }}</td>
                            <td class="{{(($position['netQty']>0)?'bg-success text-light':(($position['netQty']<0)?'bg-danger text-light':''))}}">{{ $position['netQty'] }}</td>
                            <td class="{{ (($position['changeQty'] > 0) ? 'bg-success text-light' : (($position['changeQty'] == 0) ? 'text-dark' : 'bg-danger text-light')) }}">
                                {{ $position['changeQty'] }}
                            </td>
                            <td>{{ $position['lastChange'] }}</td>
                        </tr>
                        @php
                        $serial++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@0.9.15/dist/js/bootstrap-multiselect.min.js"></script>
                <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script> 

                <script>
                    $(document).ready(function() {
                        var table = $('#positionsTable').DataTable({
                            dom: 'Bfrtip',
                            pageLength: 15,
                            buttons: [
                                {
                                    extend: 'copyHtml5',
                                    title: 'open-position'
                                },
                                {
                                    extend: 'excelHtml5',
                                    title: 'open-position'
                                },
                                {
                                    extend: 'csvHtml5',
                                    title: 'open-position'
                                },
                                {
                                    extend: 'pdfHtml5',
                                    title: 'open-position'
                                }
                            ],
                            drawCallback: function () {
                                $('.dataTables_filter input').addClass('form-control form-control-solid w-250px');
                                $('.dt-buttons button').addClass('fs-7 active-menu-item text-light border-0');
                                $('.dt-buttons').addClass('mb-2');
                                $('.dt-button').addClass('btn-primary');
                                $('.paginate_button').addClass('fs-7');
                                $('.paginate_button.current').addClass('fs-7 active-menu-item');
                                $('.paginate_button.active-menu-item').removeClass('current');
                            },
                        });

                        // Initialize Bootstrap Multiselect
                        $('#filterParent').multiselect({
                            includeSelectAllOption: true,
                            nonSelectedText: 'Select Script Parent',
                            buttonWidth: '100%'
                        });
                        $('#filterName').multiselect({
                            includeSelectAllOption: true,
                            nonSelectedText: 'Select Script Name',
                            buttonWidth: '100%'
                        });

                        // Populate filter options
                        function populateFilterOptions() {
                            var parentOptions = new Set();
                            var nameOptions = new Set();

                            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                                var data = this.data();
                                parentOptions.add(data[1]);
                                nameOptions.add(data[2]);
                            });

                            $('#filterParent').html('');
                            $('#filterName').html('');

                            parentOptions.forEach(function(option) {
                                $('#filterParent').append('<option value="' + option + '">' + option + '</option>');
                            });
                            nameOptions.forEach(function(option) {
                                $('#filterName').append('<option value="' + option + '">' + option + '</option>');
                            });

                            $('#filterParent').multiselect('rebuild');
                            $('#filterName').multiselect('rebuild');
                        }

                        populateFilterOptions();

                        function filterTable() {
                            var parentValues = $('#filterParent').val();
                            var nameValues = $('#filterName').val();

                            table.columns(1).search(parentValues ? parentValues.join('|') : '', true, false).draw();
                            table.columns(2).search(nameValues ? nameValues.join('|') : '', true, false).draw();
                        }

                        $('#filterParent').on('change', filterTable);
                        $('#filterName').on('change', filterTable);
                    });
                </script>
            </div>
        </div>
    </div>
    @else
    <div class="container-fluid px-4">
        <div class="d-flex">
            <p class="mt-2 mb-0 w-95">
                {{$title}} @if(Auth::user()->user_type==\App\Models\User::USER_SUPER_ADMIN): Balance {{$totalBalance}}

                (Withdrawal Requests : {{$withdrawRequests}})
                @endif
            </p>
        </div>
        
        
        <div class="row mt-2">
            @foreach($brands as $brand)
                @php
                    $bankAccountCodes = $brand->banks->pluck('account_code')->toArray();
                @endphp
                @if(array_intersect($bankAccountCodes, permissions()))
                    <h5 class="card-title mt-2 mb-2 p-2 text-dark">Account Details for <strong>{{$brand->name}}</strong> <strong class="ms-2">(Balance: {{$brand->brandBalance()}})</strong></h5>
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
                        <div class="col-md-4">
                            <div class="card bg-fff current-card">
                                <div class="card-body financial">
                                    <h5 class="card-title mb-0 pb-0 text-dark w-100 d-flex">{{$brand->name}} Financials</h5>
                                    <p class="text-dark">{{date('d M Y l',strtotime($startDate))}}</p>
                                    {!! financialCard($brand->id,$startDate,$todayDeposit,$todayWithdraw,$todayGap,$todayParking,$todayEquity,$todayActualDeposit,$todayActualWithdraw,$todayDepositCount,$todayWithdrawCount,$yesterDeposit,$yesterWithdraw,$yesterGap,$yesterParking,$yesterEquity) !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-fff">
                                <div class="card-body financial">
                                    <h5 class="card-title mb-0 pb-0 text-dark w-100 d-flex">{{$brand->name}} Financials</h5>
                                    <p class="text-dark">{{date('d M Y l',strtotime($yesterdayStartDate))}}</p>
                                    {!! financialCard($brand->id,$yesterdayStartDate,$yesterDeposit,$yesterWithdraw,$yesterGap,$yesterParking,$yesterEquity,$yesterActualDeposit,$yesterActualWithdraw,$yesterDepositCount,$yesterWithdrawCount,$daybefYesDeposit,$daybefYesWithdraw,$daybefYesGap,$daybefYesParking,$daybefYesEquity) !!}
                                </div>
                            </div>
                        </div>
                        @php
                            $deposits = $brand->depositsBetween($monthStartDate,$monthEndDate);
                            $withdrawals = $brand->withdrawalsBetween($monthStartDate,$monthEndDate);
                            $gap = $deposits['deposit'] - $withdrawals['withdraw'];
                            $monthEquityRecords = $brand->equityRecords($monthStartDate->toDateString(),$monthEndDate->toDateString());
                            $monthParkings = $brand->parkingsupto($monthStartDate->toDateString(),$monthEndDate->toDateString());
                            
                            $monDeposit = $deposits['deposit'];
                            $monDepositCount = $deposits['count'];
                            $monWithdraw = $withdrawals['withdraw'];
                            $monWithdrawCount = $withdrawals['count'];
                            $monActualDeposit = $monthEquityRecords['deposit'];
                            $monActualWithdraw = $monthEquityRecords['withdraw'];
                            $monEquity = $monthEquityRecords['equity'];
                            $monGap = $monDeposit - $monWithdraw;
                            $monParking = $monthParkings;

                            $monBedeposits = $brand->depositsBetween($monthBeforeStartDate,$monthBeforeEndDate);
                            $monBewithdrawals = $brand->withdrawalsBetween($monthBeforeStartDate,$monthBeforeEndDate);
                            $monBegap = $deposits['deposit'] - $withdrawals['withdraw'];
                            $monBemonthEquityRecords = $brand->equityRecords($monthBeforeStartDate->toDateString(),$monthBeforeEndDate->toDateString());
                            $monBemonthParkings = $brand->parkingsupto($monthBeforeStartDate->toDateString(),$monthBeforeEndDate->toDateString());
                            
                            $monBDeposit = $monBedeposits['deposit'];
                            $monBWithdraw = $monBewithdrawals['withdraw'];
                            $monBEquity = $monBemonthEquityRecords['equity'];
                            $monBGap = $monBDeposit - $monBWithdraw;
                            $monBParking = $monBemonthParkings;
                        @endphp
                        <div class="col-md-4">
                            <div class="card bg-fff">
                                <div class="card-body financial">
                                    <h5 class="card-title mb-0 pb-0 text-dark w-100 d-flex">{{$brand->name}} Financials</h5>
                                    <p class="text-dark">{{date('M Y',strtotime($monthStartDate))}}</p>
                                    {!! financialCard($brand->id,$monthStartDate,$monDeposit,$monWithdraw,$monGap,$monParking,$monEquity,$monActualDeposit,$monActualWithdraw,$monDepositCount,$monWithdrawCount,$monBDeposit,$monBWithdraw,$monBGap,$monBParking,$monBEquity) !!}
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
            <h5 class="card-title mt-2 mb-2 p-2 text-dark">Complete Overview</h5>
            <div class="col-md-4">
                <div class="card current-card">
                    <div class="card-body bg-fff">
                        <p class="text-dark">{{date('d M Y',strtotime($startDate))}}</p>
                        @php
                            $todayDeposit = $todaysDeposits['deposit'];
                            $todayDepositCount = $todaysDeposits['count'];
                            $todayWithdraw = $todaysWithdrawals['withdraw'];
                            $todayWithdrawCount = $todaysWithdrawals['count'];
                            $todayActualDeposit = $todaysEquity['deposit'];
                            $todayActualWithdraw = $todaysEquity['withdraw'];
                            $todayEquity = $todaysEquity['equity'];
                            $todayGap = $todayDeposit - $todayWithdraw;
                            $todayParking = $todaysParkings;

                            $yesterDayDeposit = $yesterdayDeposits['deposit'];
                            $yesterDayDepositCount = $yesterdayDeposits['count'];
                            $yesterDayWithdraw = $yesterdayWithdrawals['withdraw'];
                            $yesterDayWithdrawCount = $yesterdayWithdrawals['count'];
                            $yesterDayActualDeposit = $yesterdaysEquity['deposit'];
                            $yesterDayActualWithdraw = $yesterdaysEquity['withdraw'];
                            $yesterDayEquity = $yesterdaysEquity['equity'];
                            $yesterDayGap = $yesterDayDeposit - $yesterDayWithdraw;
                            $yesterDayParking = $yesterdaysParkings;

                            $dayBefyesterDeposit = $dayBefYesDeposits['deposit'];
                            $dayBefyesterDepositCount = $dayBefYesDeposits['count'];
                            $dayBefyesterWithdraw = $dayBefYesWithdrawals['withdraw'];
                            $dayBefyesterWithdrawCount = $dayBefYesWithdrawals['count'];
                            $dayBefyesterActualDeposit = $dayBefYesEquity['deposit'];
                            $dayBefyesterActualWithdraw = $dayBefYesEquity['withdraw'];
                            $dayBefyesterEquity = $dayBefYesEquity['equity'];
                            $dayBefyesterGap = $dayBefyesterDeposit - $dayBefyesterWithdraw;
                            $dayBefyesterParking = $dayBefYesParkings;
                        @endphp
                        {!! financialCard('all',$startDate,$todayDeposit,$todayWithdraw,$todayGap,$todayParking,$todayEquity,$todayActualDeposit,$todayActualWithdraw,$todayDepositCount,$todayWithdrawCount,$yesterDayDeposit,$yesterDayWithdraw,$yesterDayGap,$yesterDayParking,$yesterDayEquity) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body bg-fff">
                        <p class="text-dark">{{date('d M Y',strtotime($yesterdayStartDate))}}</p>
                        {!! financialCard('all',$endDate,$yesterDayDeposit,$yesterDayWithdraw,$yesterDayGap,$yesterDayParking,$yesterDayEquity,$yesterDayActualDeposit,$yesterDayActualWithdraw,$yesterDayDepositCount,$yesterDayWithdrawCount,$dayBefyesterDeposit,$dayBefyesterWithdraw,$dayBefyesterGap,$dayBefyesterParking,$dayBefyesterEquity) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body bg-fff">
                        <p class="text-dark">{{date('M Y',strtotime($endDate))}}</p>
                        @php
                            $todayDeposit = $monthlyDeposits['deposit'];
                            $todayDepositCount = $monthlyDeposits['count'];
                            $todayWithdraw = $monthlyWithdrawals['withdraw'];
                            $todayWithdrawCount = $monthlyWithdrawals['count'];
                            $todayActualDeposit = $monthlyEquity['deposit'];
                            $todayActualWithdraw = $monthlyEquity['withdraw'];
                            $todayEquity = $monthlyEquity['equity'];
                            $todayGap = $todayDeposit - $todayWithdraw;
                            $todayParking = $monthlyParkings;

                            $yesterDeposit = $yesMonthlyDeposits['deposit'];
                            $yesterDepositCount = $yesMonthlyDeposits['count'];
                            $yesterWithdraw = $yesMonthlyWithdrawals['withdraw'];
                            $yesterWithdrawCount = $yesMonthlyWithdrawals['count'];
                            $yesterActualDeposit = $yesMonthlyEquity['deposit'];
                            $yesterActualWithdraw = $yesMonthlyEquity['withdraw'];
                            $yesterEquity = $yesMonthlyEquity['equity'];
                            $yesterGap = $yesterDeposit - $yesterWithdraw;
                            $yesterParking = $yesMonthlyParkings;
                        @endphp
                        {!! financialCard('all',$endDate,$todayDeposit,$todayWithdraw,$todayGap,$todayParking,$todayEquity,$todayActualDeposit,$todayActualWithdraw,$todayDepositCount,$todayWithdrawCount,$yesterDeposit,$yesterWithdraw,$yesterGap,$yesterParking,$yesterEquity) !!}
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
    @endif
</main>
@endsection
