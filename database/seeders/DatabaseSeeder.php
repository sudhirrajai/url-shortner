<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::insert(
            'insert into users (name, email, password, role, created_at, updated_at) values (?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                'superadmin@example.com',
                Hash::make('password'),
                'super_admin',
                now(),
                now()
            ]
        );
    }
}
