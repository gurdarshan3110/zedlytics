<div class="form-group col-sm-6">
    {{ html()->label('Account Code') }}
    @if(isset($bank) && !empty($bank))
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
    {{ html()->label('Account No') }}
    {{ html()->text('account_no')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('RM') }}
    {{ html()->text('rm')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Description') }}
    {{ html()->textarea('description')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($bank) && $bank['status'] != '' ){ $status = $bank['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['1'=>'Active','0'=>'Deactive']) }}
</div>
