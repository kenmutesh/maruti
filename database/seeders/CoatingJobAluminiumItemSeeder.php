<?php

namespace Database\Seeders;

use App\Models\CoatingJob;
use App\Models\CoatingJobAluminiumItem;
use Illuminate\Database\Seeder;

class CoatingJobAluminiumItemSeeder extends Seeder
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
            CoatingJobAluminiumItem::factory(2)->create([
                'coating_job_id' => $coatingjob->id
            ]);
        }
    }
}
