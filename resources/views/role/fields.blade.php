<div class="form-group col-sm-6">
    {{ html()->label('Role') }}
    {{ html()->text('name')->class('form-control') }}
</div>

<div class="form-group col-sm-12">
    {{ html()->label('Permissions') }}

    <div id="permissions-container">
        <div class="form-check mb-3">
            <input type="checkbox" id="select-all-permissions" class="form-check-input">
            <label for="select-all-permissions" class="form-check-label">Select All</label>
        </div>
        @php
            $permissionsByParent = $permissions->groupBy('parent');
        @endphp

        @foreach ($permissionsByParent as $parentId => $permissionsGroup)
            <div class="permission-group">
                <div class="row">
                @foreach ($permissionsGroup as $permission)
                    <div class="col-sm-3">
                        <div class="form-check">
                            {{ html()->checkbox('permissions[]', $permission->name, $permission->id)
                                ->id('permission_' . $permission->id)
                                ->class('form-check-input permission-checkbox')
                                ->checked(isset($role) ? $role->permissions->contains('id', $permission->id) : false) }}
                            {{ html()->label(ucwords($permission->name))->for('permission_' . $permission->id)->class('form-check-label') }}
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script>
        (function() {
            const selectAllCheckbox = document.getElementById('select-all-permissions');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

        })();
    </script>
</div>



