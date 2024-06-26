<div class="form-group col-sm-6">
    {{ html()->label('Brand') }}
    <?php 
    $brand = '';
    if(!empty($bank) && $bank['brand_id'] != '' ){ $brand = $bank['brand_id'];} else{ $brand = ''; } 
    ?>
    
    @if ($brand != '')
        {{ html()->select('brand_id', $brands)->class('form-control')->attributes(['disabled' => 'disabled']) }}
    @else
        {{ html()->select('brand_id', $brands)->class('form-control') }}
    @endif
</div>

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
    {{ html()->label('IFSC') }}
    {{ html()->text('ifsc')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Branch') }}
    {{ html()->text('branch')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('City') }}
    {{ html()->text('city')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('State') }}
    {{ html()->text('state')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('RM') }}
    {{ html()->text('rm')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('First Limit') }}
    {{ html()->number('first_limit')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Second Limit') }}
    {{ html()->number('second_limit')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Lean Balance') }}
    {{ html()->number('lean_balance')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Commission Rate') }}
    {{ html()->number('commission_rate')->class('form-control')->autocomplete(false) }}
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
