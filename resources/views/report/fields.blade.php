<div class="row">
    <div class="form-group col-md-4">
        {{ html()->label('Start Date')->for('start_date') }}
        {{ html()->date('start_date')->class('form-control')->required() }}
    </div>
    <div class="form-group col-md-4">
        {{ html()->label('End Date')->for('end_date') }}
        {{ html()->date('end_date')->class('form-control')->required() }}
    </div>
    <div class="form-group col-md-4">
        {{ html()->label('Bank')->for('bank') }}
        {{ html()->select('bank', $banks)->class('form-control') }}
    </div>
</div>

