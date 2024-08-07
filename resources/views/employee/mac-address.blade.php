<div class="form-group col-sm-6">
    {{ html()->label('IP Address (Click checkbox to turn off IP Authentication)') }}
    {{ html()->checkbox('ip_auth', $ip_auth == 0)->class('form-check-input') }}
    {{ html()->text('mac_address')->class('form-control')->autocomplete(false)->value($mac_address) }}
</div>