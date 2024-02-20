<?php

namespace Database\Seeders;

use App\Enums\DocumentLabelsEnum;
use App\Models\Company;
use App\Models\DocumentLabel;
use Illuminate\Database\Seeder;

class DocumentLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentLabels = DocumentLabelsEnum::cases();
        $companies = Company::all();
        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
            }
        }
    }
}
