<div class="row">
    <div class="form-group col-md-4">
        {{ html()->label('Start Date')->for('start_date') }}
        {{ html()->date('start_date', date('Y-m-d'))->class('form-control')->id('min')->required() }}
    </div>
    <div class="form-group col-md-4">
        {{ html()->label('End Date')->for('end_date') }}
        {{ html()->date('end_date', date('Y-m-d'))->class('form-control')->id('max')->required() }}
    </div>
    <div class="form-group col-sm-4">
        <br>
        {{ html()->submit('Search')->class('btn btn-primary btn-sm')}}
        <a href="{{ route($url.'.index') }}" class="btn btn-default bordered btn-sm">Cancel</a>
    </div>
</div>

