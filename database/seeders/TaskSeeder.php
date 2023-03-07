<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tasks')->insert([
            [
                'name' => 'Spring cleaning',
                'description' => 'Get rid of unnecessary items!',
                'due_date_at' => Carbon::now()->addDays(10),
                'category_id' => null,
            ],
            [
                'name' => 'Find an apartment',
                'description' => null,
                'due_date_at' => Carbon::now()->addDays(15),
                'category_id' => 1,
            ],
            [
                'name' => 'Get a friend',
                'description' => 'Society of Grownups class',
                'due_date_at' => Carbon::now()->addDays(20),
                'category_id' => 1,
            ],
            [
                'name' => 'Clean Up LGF email - for real',
                'description' => 'My company email account',
                'due_date_at' => Carbon::now()->addDays(25),
                'category_id' => 2,
            ],
            [
                'name' => 'DonorsChoose Thank You Project (Lightning Thief)',
                'description' => null,
                'due_date_at' => null,
                'category_id' => 2,
            ],
            [
                'name' => 'Mail back Cait\'s book',
                'description' => null,
                'due_date_at' => null,
                'category_id' => 1,
            ],
            [
                'name' => 'File for forbearance on Stafford Loans',
                'description' => null,
                'due_date_at' => Carbon::now()->addDays(60),
                'category_id' => 3,
            ],
            [
                'name' => 'File for forbearance on Perkins Loans',
                'description' => null,
                'due_date_at' => null,
                'category_id' => 3,
            ],
        ]);
    }
}
