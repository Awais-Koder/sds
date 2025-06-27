<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'editor']);
        Role::firstOrCreate(['name' => 'dc']);
        Role::firstOrCreate(['name' => 'actioner']);
        Role::firstOrCreate(['name' => 'viewer']);


        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sds.com',
            'password' => Hash::make('12345678'),
        ])->assignRole('super_admin');

        User::factory()->create([
            'name' => 'Documnet Controller',
            'email' => 'dc@sds.com',
            'password' => Hash::make('12345678'),
        ])->assignRole('dc');

        User::factory()->create([
            'name' => 'Actioner',
            'email' => 'actioner@sds.com',
            'password' => Hash::make('12345678'),
        ])->assignRole('actioner');

        User::factory()->create([
            'name' => 'Viewer',
            'email' => 'viewer@sds.com',
            'password' => Hash::make('12345678'),
        ])->assignRole('viewer');
    }
}
