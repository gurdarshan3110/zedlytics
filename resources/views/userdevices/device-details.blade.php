@extends('includes.app')

@section('content')
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-75">
                {{$title}}
            </h3>
        </div>
    
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card bg-fff p-2">
                    <div class="container">
                        <div class="table-responsive">
                            <table class="w-100 align-middle fs-6 table table-bordered" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
                                <thead>
                                <tr>
                                    <th width="5%">S.No</th>
                                    <th width="10%">Account Id</th>
                                    <th width="20%">Name</th>
                                    <th width="15%">Username</th>
                                    <th width="25%">Address</th>
                                    <th width="7%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($allUsers as $k => $data)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$data->client->client_code}}</td>
                                        <td>{{$data->client->name}}</td>
                                        <td>{{$data->client->username}}</td>
                                        <td>{{$data->client_address}}</td>
                                        <td class="p-1">
                                            @if($data->is_available==0)
                                            <a class="btn btn-warning btn-sm" data-value="{{$data->id}}" id="blacklist-btn{{$data->id}}" onclick="blackList({{$data->id}});">Black List</a>
                                            @else
                                            <a class="btn btn-primary btn-sm" data-value="{{$data->id}}" id="blacklist-btn{{$data->id}}" onclick="blackList({{$data->id}});">White List</a>
                                            @endif
                                            <div id="error-message" style="color: red; display: none;">Reason is required.</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('jsscript')
<script>
function blackList(id) {
    // Get the data-value from the button
    //var button = this;
    var dataValue = id;

    // Show prompt box
    var reason = prompt("Please enter the reason for blacklisting:");

    // Check if a reason was provided
    if (reason === null) {
        // User canceled the prompt, remove the error message
        document.getElementById('error-message').style.display = 'none';
    } else if (reason.trim() === "") {
        // No reason provided, show error message
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-message').innerText = "Reason is required.";
    } else {
        // Valid reason provided, hide error message and make AJAX request
        document.getElementById('error-message').style.display = 'none';

        // Prepare data for the AJAX request
        var data = {
            reason: reason,
            id: dataValue // Include the data-value
        };

        fetch('/update-blacklist', { // Replace with your endpoint
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            // Handle response
            if (data) {
                var btnTxt = document.getElementById('blacklist-btn'+dataValue);
                if(btnTxt.innerText=='Black List'){
                    btnTxt.innerText = 'White List';
                    btnTxt.classList.add('btn-primary');
                    btnTxt.classList.remove('btn-warning');
                }else{
                    btnTxt.innerText = 'Black List';
                    btnTxt.classList.remove('btn-primary');
                    btnTxt.classList.add('btn-warning');
                }
            } else {
                alert("Update failed.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred.");
        });
    }
}
</script>


@endpush
@endsection 

