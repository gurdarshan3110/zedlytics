<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="{{asset('/assets/css/styles.css')}}" rel="stylesheet" />
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Table for posType1 -->
        <div class="col-md-12 mb-3">
        	<h2>{{$title}}</h2>
        </div>
        <div class="col-md-6">
            <h2>Long</h2>
            <table class="table table-low-heights table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>User Id</th>
                        <th>Deals</th>
                    </tr>
                </thead>
                <tbody>
                    @php $serial1 = 1; @endphp
                    @foreach($long as $position)
                    <tr class="bg-success text-light">
                        <td>{{ $serial1 }}</td>
                        <td>{{ (($position->client_name=='N/A')?$position->userID:$position->client_name) }}</td>
                        <td class="text-right">{{ $position->openAmount - $position->closeAmount }}</td>
                    </tr>
                    @php $serial1++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table for posType2 -->
        <div class="col-md-6">
            <h2>Short</h2>
            <table class="table table-bordered table-low-heights table-striped">
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>User Id</th>
                        <th>Deals</th>
                    </tr>
                </thead>
                <tbody>
                    @php $serial2 = 1; @endphp
                    @foreach($short as $position)
                    <tr class="bg-danger text-light">
                        <td>{{ $serial2 }}</td>
                        <td>{{ $position->userID }}</td>
                        <td class="text-right">{{ abs($position->openAmount + $position->closeAmount) }}</td> 
                    </tr>
                    @php $serial2++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
