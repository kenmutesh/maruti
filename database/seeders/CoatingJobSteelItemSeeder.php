<?php

namespace Database\Seeders;

use App\Models\CoatingJob;
use App\Models\CoatingJobSteelItem;
use Illuminate\Database\Seeder;

class CoatingJobSteelItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coatingjobs = CoatingJob::all();
        foreach ($coatingjobs as $coatingjob) {
            CoatingJobSteelItem::factory(5)->create([
                'coating_job_id' => $coatingjob->id
            ]);
        }
    }
}
