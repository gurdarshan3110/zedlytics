<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Table for posType1 -->
        <div class="col-md-12 mb-3">
        	<h2>{{$title}}</h2>
        </div>
        <div class="col-md-6">
            <h2>Positions with Long</h2>
            <table class="table table-bordered table-striped">
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
                    <tr class="bg-success">
                        <td>{{ $serial1 }}</td>
                        <td>{{ $position->userID }}</td>
                        <td>{{ $position->openAmount }} - {{ $position->closeAmount }} = {{ $position->openAmount - $position->closeAmount }}</td>
                    </tr>
                    @php $serial1++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table for posType2 -->
        <div class="col-md-6">
            <h2>Positions with Short</h2>
            <table class="table table-bordered table-striped">
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
                    <tr class="bg-danger">
                        <td>{{ $serial2 }}</td>
                        <td>{{ $position->userID }}</td>
                        <td>{{ abs($position->openAmount) }} - {{ abs($position->closeAmount) }} = {{ abs($position->openAmount - $position->closeAmount) }}</td>
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
