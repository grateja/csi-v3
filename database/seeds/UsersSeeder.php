<?php

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
                'id' => 'e026cf14-0093-4de3-8ab2-e13086acb7ac',
                'name' => 'The Programmer',
                'email' => 'developer@gmail.com',
                'password' => bcrypt('admin'),
            ],
        ];

        \App\User::insert($users);
    }
}
