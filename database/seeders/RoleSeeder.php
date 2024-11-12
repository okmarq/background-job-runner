<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => config('constants.role.admin')],
            ['name' => config('constants.role.instructor')],
            ['name' => config('constants.role.student')]
        ];
        Role::insert($roles);
    }
}
