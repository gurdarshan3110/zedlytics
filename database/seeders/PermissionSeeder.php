<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'dashboard',
            'create employee',
            'edit employee',
            'delete employee',
            'view employee',
            'create client',
            'edit client',
            'delete client',
            'view client',
            'create role',
            'edit role',
            'delete role',
            'view role',
            'assign permissions',
            'create bank',
            'edit bank',
            'delete bank',
            'view bank',
            'create brand',
            'edit brand',
            'delete brand',
            'view brand',
            'create party',
            'edit party',
            'delete party',
            'view party',
            'create expense type',
            'edit expense type',
            'delete expense type',
            'view expense type',
            'create ledger',
            'view ledger',
            'ledger status',
            'view deposit',
            
        ];

        // Create each permission
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' =>'web'
            ]);
        }
    }
}
