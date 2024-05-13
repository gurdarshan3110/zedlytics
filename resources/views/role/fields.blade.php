<div class="form-group col-sm-6">
    {{ html()->label('Role') }}
    {{ html()->text('name')->class('form-control') }}
</div>

<div class="form-group col-sm-12">
    {{ html()->label('Permissions') }}
    <div class="row">
        @foreach ($permissions as $permission)
            <div class="col-sm-3">
                <div class="form-check">
                    <?php
                    $checked = false;
                    if(isset($role)){
                        $checked = $role->permissions->contains('id', $permission->id);
                    }
                    ?>
                    {{ html()->checkbox('permissions[]', $permission->name, $permission->id)
                        ->id('permission_'.$permission->id)
                        ->class('form-check-input')
                        ->checked($checked) }}
                    {{ html()->label(ucwords($permission->name))->for('permission_'.$permission->id)->class('form-check-label') }}
                </div>
            </div>
        @endforeach

    </div>
</div>


