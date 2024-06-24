<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Colorfull',
                'email' => 'pixelpartner+colorfull@example.com',
                'password' => bcrypt('colorfull-' . date('d-m')),
                'theme' => 'colorfull',
            ],
            [
                'name' => 'Simple',
                'email' => 'pixelpartner+simple@example.com',
                'password' => bcrypt('simple-' . date('d-m')),
                'theme' => 'simple',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate([
                'email' => $data['email'],
            ], $data);

            $user->assignRole($data['theme']);
        }
    }
}
