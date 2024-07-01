<div class="form-group col-sm-6">
    {{ html()->label('Account Code') }}
    {{ html()->text('account_code')->class('form-control') }}
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
    {{ html()->label('Username') }}
    {{ html()->text('username')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Password') }}
    {{ html()->text('password')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Company Name') }}
    {{ html()->text('company_name')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($party) && $party['status'] != '' ){ $status = $party['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['1'=>'Active','0'=>'Deactive']) }}
</div>
