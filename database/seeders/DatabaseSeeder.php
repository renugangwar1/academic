<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Institute;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        

        Institute::create([
            'InstituteName' => 'Master Chapter',
            'email' => 'master@nchm.com',
            'InstituteCode' => 'MC123',
            'password' => Hash::make('master123'),
            'system' => '127.0.0.1',
            'type' => 'master',
        ]);

    }
}