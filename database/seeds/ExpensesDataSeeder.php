<?php

use App\Expense;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpensesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            for ($i=0; $i < 100; $i++) {
                $date = Carbon::now()->subDay(rand(0, 1000));
                $user = User::inRandomOrder()->first();

                $expense = Expense::create([
                    'date' => $date,
                    'remarks' => Str::random(32),
                    'amount' => rand(500, 3000),
                    'staff_name' => $user->name,
                ]);
            }
        });
    }
}
