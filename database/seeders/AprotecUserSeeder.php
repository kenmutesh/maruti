<?php

namespace Database\Seeders;

use App\Models\AprotecUser;
use Illuminate\Database\Seeder;

class AprotecUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AprotecUser::factory(2)->create();
    }
}
