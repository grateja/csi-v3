<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Role::count() == 0) {
            $roles = [
                [
                    'id' => 1,
                    'name' => 'developer',
                    'description' => 'Mostly for backoffice',
                    'level' => 'developer',
                ],
                [
                    'id' => 2,
                    'name' => 'admin',
                    'description' => 'This is awesome',
                    'level' => 'client',
                ],
                [
                    'id' => 3,
                    'name' => 'staff',
                    'description' => 'Not so awesome',
                    'level' => 'user',
                ],
            ];
    
            \App\Role::insert($roles);
        }
    }
}
