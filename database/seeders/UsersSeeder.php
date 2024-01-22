<?php

namespace database\seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Gourab',
                'last_name' => 'Ghosh',
                'email' => 'gourab@gamil.com',
                'password'=>bcrypt('123456789'),
                'phone' => '123456789',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }


    }
}
