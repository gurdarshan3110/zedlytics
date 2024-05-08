<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create a super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@zedlytics.com',
            'phone_no' => '9023279634', 
            'password' => Hash::make('zed@lytics'), 
            'user_type' => 'super_admin',
            'status' => 1, // Active
        ]);
    }
}
