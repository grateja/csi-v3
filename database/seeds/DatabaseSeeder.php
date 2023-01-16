<?php

use App\LagoonPerKilo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\RolesSeeder::class);
        $this->call(\UsersSeeder::class);
        $this->call(\MachinesSeeder::class);
        $this->call(\DryingServiceSeeder::class);
        $this->call(\WashingServiceSeeder::class);
        $this->call(\OtherServicesSeeder::class);
        $this->call(\JobOrderSeeder::class);
        $this->call(\PointsDataSeeder::class);
        $this->call(\StoreHoursSeeder::class);
        DB::table('role_users')->insert([
            [
                'user_id' => 'e026cf14-0093-4de3-8ab2-e13086acb7ac',
                'role_id' => 1
            ],
        ]);

        $this->call(\EventTypesTableSeeder::class);
        $this->call(\EventsTableSeeder::class);
        $this->call(\SlidesTableSeeder::class);
        $this->call(\AnnouncementsDataSeeder::class);
        $this->call(\SysDefaultsTableSeeder::class);
        $this->call(\MonitorCheckerSeeder::class);
    }
}
