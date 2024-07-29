<!-- resources/views/path/to/permissions_list.blade.php -->
@php
    $lastParentName = null;
@endphp

@if ($permissionsByParent->isEmpty())
    <p>No permissions available for this parent role.</p>
@endif

@foreach ($permissionsByParent as $parentId => $permissionsGroup)
    @php
        $parentName = $permissionsGroup->first()->parent;
    @endphp

    @if ($parentName !== $lastParentName)
        <div class="permission-group mb-4 card p-2 bg-fff">
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
