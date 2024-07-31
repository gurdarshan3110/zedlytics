<div class="form-group col-sm-6">
    {{ html()->label('Transfered From') }}
    {{ html()->select('transfered_from', $transferedFrom)->class('form-control')->attributes(['disabled' => 'disabled']) }}
</div>
<div class="form-group col-sm-6">
    {{ html()->label('Transfered To') }}
    {{ html()->select('transfered_to', $transferedTo)->class('form-control') }}
</div>
