<div class="form-group col-sm-6">
    {{ html()->label('Client Code') }}
    @if(isset($client) && !empty($client))
        {{ html()->text('account_code')->class('form-control')->attribute('readonly', 'readonly')->value($client->client_code) }}
    @else
        {{ html()->text('account_code')->class('form-control') }}
    @endif
</div>

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

<div class="form-group col-sm-6">
    {{ html()->label('Mobile No') }}
    {{ html()->text('mobile')->class('form-control')->autocomplete(false) }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('RM') }}
    <?php 
    $brand = '';
    if(!empty($client) && $client['rm'] != '' ){ $rm = $client['rm'];} else{ $rm = ''; } 
    ?>
    
    {{ html()->select('rm', $rms)->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Status') }}
    <?php 
    $status = '';
    if(!empty($client) && $client['status'] != '' ){ $status = $client['status'];} else{ $status = 0; } 
    ?>
    
    {{ html()->select('status')->class('form-control')->options(['0'=>'Active','1'=>'Deactive']) }}
</div>
