
<div class="form-group col-sm-6">
    {{ html()->label('Name') }}
    {{ html()->text('name')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Email') }}
    {{ html()->email('email')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Phone No') }}
    {{ html()->text('phone_no')->class('form-control')->autocomplete(false) }}
</div>
@if(!isset($employee))
<div class="form-group col-sm-6">
    {{ html()->label('Password') }}
    {{ html()->password('password')->class('form-control')->autocomplete(false) }}
</div>
@endif
<div class="form-group col-sm-6">
    {{ html()->label('Role') }}
    <?php 
    $role = '';
    if(!empty($role) && $employee['status'] != '' ){ $status = $employee['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('role')->class('form-control')->options($roles) }}
</div>
<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($employee) && $employee['status'] != '' ){ $status = $employee['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['1'=>'Active','0'=>'Deactive']) }}
</div>
