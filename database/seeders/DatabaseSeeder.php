<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'firstname' => 'AdminF',
            'lastname' => 'AdminL',
            'email' => 'admin@example.com',
            'password' => Hash::make(env('TEST_PASSWORD'))
        ]);
        $admin->attachRole(config('constants.role.admin'));

        $instructor = User::factory()->create([
            'firstname' => 'InstructorF',
            'lastname' => 'InstructorL',
            'email' => 'instructor@example.com',
            'password' => Hash::make(env('TEST_PASSWORD'))
        ]);
        $instructor->attachRole(config('constants.role.instructor'));

        $student = User::factory()->create([
            'firstname' => 'StudentF',
            'lastname' => 'StudentL',
            'email' => 'student@example.com',
            'password' => Hash::make(env('TEST_PASSWORD'))
        ]);
        $student->attachRole(config('constants.role.student'));
    }
}
