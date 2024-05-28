<div class="form-group col-sm-6">
    {{ html()->label('IP Address') }}
    {{ html()->text('mac_address')->class('form-control')->autocomplete(false)->value($mac_address) }}
</div>
