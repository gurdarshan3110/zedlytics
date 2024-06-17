<div class="form-group col-sm-6">
    {{ html()->label('Account Code') }}
    @if(isset($party) && !empty($party))
        {{ html()->text('account_code')->class('form-control')->attribute('readonly', 'readonly') }}
    @else
        {{ html()->text('account_code')->class('form-control') }}
    @endif
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Name') }}
    {{ html()->text('name')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Description') }}
    {{ html()->textarea('description')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Type') }}
    <?php 
    $status = '';
    if(!empty($party) && $party['type'] != '2' ){ $type = $party['type'];} else{ $type = 1; } 
    ?>
    
    {{ html()->select('type')->class('form-control')->options(['2'=>'Party','1'=>'Bank Account','0'=>'Pool Account']) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($party) && $party['status'] != '' ){ $status = $party['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['1'=>'Active','0'=>'Deactive']) }}
</div>
