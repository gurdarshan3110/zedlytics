<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleMaster;

class ModuleMasterSeeder extends Seeder
{
    public function run()
    {
        // Add Dashboard module
        ModuleMaster::create([
            'name' => 'Dashboard',
            'icon' => '/assets/images/dashboard-icon.png', 
            'url' => 'dashboard',
            'status' => 1,
        ]);

        ModuleMaster::create([
            'name' => 'Banks',
            'icon' => '/assets/images/banks-icon.png',
            'url' => 'banks',
            'status' => 1,
        ]);

        ModuleMaster::create([
            'name' => 'Employees',
            'icon' => '/assets/images/employees-icon.png',
            'url' => 'employees',
            'status' => 1,
        ]);

        ModuleMaster::create([
            'name' => 'Clients',
            'icon' => '/assets/images/clients-icon.png',
            'url' => 'clients',
            'status' => 1,
        ]);

        
    }
}
