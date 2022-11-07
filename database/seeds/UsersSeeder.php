<?php

use App\Client;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(User::count() == 0) {
            $users = [
                [
                    'id' => 'e026cf14-0093-4de3-8ab2-e13086acb7ac',
                    'name' => 'The Programmer',
                    'email' => 'developer@csi.com',
                    'password' => bcrypt('admin'),
                ],
                [
                    'id' => 'lms-demo-v3.1',
                    'name' => 'Jay Grateja',
                    'email' => 'lmsv3@csi.com',
                    'password' => bcrypt('admin'),
                ],
                [
                    'id' => 'lms-demo-staff-v3.1',
                    'name' => 'Staff',
                    'email' => 'staff@csi.com',
                    'password' => bcrypt('admin'),
                ],
            ];
            User::insert($users);
            User::where('email', 'lmsv3@csi.com')->first()->assignRole(2);
            User::where('email', 'staff@csi.com')->first()->assignRole(3);
            Client::create([
                'user_id' => 'lms-demo-v3.1',
                'shop_name' => 'LMS Demo',
                'address' => 'Goldland Tower Eisenhower St. San Juan Metro Manila',
            ]);
        }
    }
}
