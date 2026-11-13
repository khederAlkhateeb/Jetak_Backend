<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        //(Super Admin)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin User',
                'password' => Hash::make('password'),
                'phone' => '0911111111',
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole('Super Admin');

        //(Store Admin)
        $storeAdmin = User::firstOrCreate(
            ['email' => 'storeadmin@gmail.com'],
            [
                'name' => 'Store Admin User',
                'password' => Hash::make('password'),
                'phone' => '0922222222',
                'status' => 'active',
            ]
        );
        $storeAdmin->assignRole('Store Admin');

        //(Hall Admin)
        $hallAdmin = User::firstOrCreate(
            ['email' => 'halladmin@gmail.com'],
            [
                'name' => 'Hall Admin User',
                'password' => Hash::make('password'),
                'phone' => '0933333333',
                'status' => 'active',
            ]
        );
        $hallAdmin->assignRole('Hall Admin');

        //(Delivery)
        $delivery = User::firstOrCreate(
            ['email' => 'delivery@gmail.com'],
            [
                'name' => 'Delivery Representative',
                'password' => Hash::make('password'),
                'phone' => '0944444444',
                'status' => 'active',
            ]
        );
        $delivery->assignRole('Delivery');

        //(Student)
        $student = User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'phone' => '0955555555',
                'status' => 'active',
                'building_number' => 15,
                'floor' => 1,
                'room_number' => 22,
            ]
        );
        $student->assignRole('Student');

        $student->wallet()->create([
                'balance' => 0.00,
        ]);
        $delivery->wallet()->create([
                'balance' => 0.00,
        ]);
        $hallAdmin->wallet()->create([
                'balance' => 0.00,
        ]);
        $storeAdmin->wallet()->create([
                'balance' => 0.00,
        ]);
         $superAdmin->wallet()->create([
                'balance' => 0.00,
        ]);
    }
}