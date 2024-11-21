<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Muktar Usman",
            "email" => "mualiyuoox@gmail.com",
            "password" => Hash::make('20111755Db'),
            "phone" => "08167236629",
            "state" => "Abuja",
            "lga" => "Municipal",
            "country" => "Nigeria",
            "role" => "admin",
        ]);
    }
}
