@if(Session::has('success'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <div class="w-75">{!! Session::get('success') !!}</div>
        <div class="w-25 d-flex justify-content-end">
            <button type="button" class="close btn" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif
@if(Session::has('error'))
    <div class="alert alert-error" role="alert">
        <div class="w-75">{!! Session::get('error') !!}</div>
        <div class="w-25 d-flex justify-content-end">
            <button type="button" class="close btn" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif