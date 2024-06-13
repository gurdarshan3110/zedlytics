<div class="form-group col-md-6">
    {{ html()->label('Brand')->for('brand_id') }}
    {{ html()->select('brand_id', $brands)->class('form-control') }}
</div>
<div class="form-group col-sm-6">
    {{ html()->label('Date') }}
    {{ html()->date('ledger_date')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Deposit') }}
    {{ html()->text('deposit')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Withdraw') }}
    {{ html()->text('withdraw')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Equity') }}
    {{ html()->text('equity')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($equityrecord) && $equityrecord['status'] != '' ){ $status = $equityrecord['status'];} else{ $status = 1; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['1'=>'Active','0'=>'Deactive']) }}
</div>
