<div class="form-group col-sm-6">
    {{ html()->label('Role') }}
    {{ html()->text('name')->class('form-control') }}
</div>

<div class="form-group col-sm-6">
    {{ html()->label('Parent Role')->for('parent_id') }}
    {{ html()->select('parent_id',$roleOptions)->class('form-control')->placeholder('Select Parent Role')->id('parent_id') }}
</div>

<div id="permissions-container">
    <div class="form-check mb-3">
        <input type="checkbox" id="select-all-permissions" class="form-check-input">
        <label for="select-all-permissions" class="form-check-label">Select All</label>
    </div>
    
    @php
        $permissionsByParent = $permissions->groupBy('parent');
        $lastParentName = null;
    @endphp
    
    @foreach ($permissionsByParent as $parentId => $permissionsGroup)
        @php
            // Assuming the parent name is stored in the first permission's parent attribute
            $parentName = $permissionsGroup->first()->parent;
        @endphp
        
        @if ($parentName !== $lastParentName)
            <div class="permission-group mb-4 card bg-fff p-2">
                <h5>{{ ucwords($parentName) }}</h5>
                <div class="row">
        @else
                <div class="row mt-3">
        @endif
        
                @foreach ($permissionsGroup as $permission)
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $permission->id }}" 
                                id="permission_{{ $permission->id }}" 
                                class="form-check-input permission-checkbox"
                                @if(isset($role) && $role->permissions->contains('id', $permission->id)) checked @endif
                            >
                            <label 
                                for="permission_{{ $permission->id }}" 
                                class="form-check-label">
                                {{ ucwords($permission->name) }}
                            </label>
                        </div>
                    </div>
                @endforeach
                
                </div>
            </div>

        @php
            $lastParentName = $parentName;
        @endphp
    @endforeach
</div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const parentRoleSelect = document.getElementById('parent_id');
            const permissionsList = document.getElementById('permissions-container');

            parentRoleSelect.addEventListener('change', function() {
                const parentId = this.value;

                if (parentId) {
                    fetch(`/permissions/${parentId}`)
                        .then(response => response.text())
                        .then(html => {
                            permissionsList.innerHTML = html;
                            attachSelectAllListener();
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                        });
                } else {
                    permissionsList.innerHTML = '<p>Please select a parent role to see permissions.</p>';
                }
            });

            function attachSelectAllListener() {
                const selectAllCheckbox = document.getElementById('select-all-permissions');
                const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

                selectAllCheckbox.addEventListener('change', function() {
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
        });

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



