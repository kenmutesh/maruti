<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use App\Models\CoatingJob;
use App\Models\CoatingJobAluminiumItem;
use App\Models\CoatingJobMarutiItem;
use App\Models\CoatingJobSteelItem;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class CoatingJobFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_will_load_coating_jobs_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
            $otherRole = Role::factory()->create([
                'name' => 'OTHER ROLE',
                'company_id' => $company->id,
            ]);

            $userMaker = User::factory()->create([
                'company_id' => $company->id,
                'role_id' => $otherRole->id
            ]);

            CoatingJob::factory(2)->coatingjobquotation()->create([
                'company_id' => $company->id,
                'prepared_by' => $userMaker->id,
                'supervisor' => $userMaker->id,
                'quality_by' => $userMaker->id,
                'sale_by' => $userMaker->id,
                'created_by' => $userMaker->id,
            ]);
            CoatingJob::factory(2)->jobcard()->create([
                'company_id' => $company->id,
                'prepared_by' => $userMaker->id,
                'supervisor' => $userMaker->id,
                'quality_by' => $userMaker->id,
                'sale_by' => $userMaker->id,
                'created_by' => $userMaker->id,
            ]);
        }

        $response = $this->actingAs($loggedInUser)
            ->get('/api/coatingjobs')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $coatingJob) {
                if ($coatingJob->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $coatingJob->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load coating jobs from other companies " . $th->getMessage());
        }
    }

    public function test_will_follow_coating_jobs_quotation_document_label_document_prefix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $otherRole = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'company_id' => $company->id,
        ]);

        $userMaker = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $otherRole->id
        ]);

        $coatingJobOne = CoatingJob::factory()->coatingjobquotation()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::QUOTATION->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $coatingJobTwo = CoatingJob::factory()->coatingjobquotation()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobTwo->save();

        $this->assertTrue(($coatingJobOne->quotation_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $coatingJobOne->quotation_prefix);

        $this->assertTrue(($coatingJobTwo->quotation_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $coatingJobTwo->quotation_prefix);
    }

    public function test_will_follow_coating_jobs_quotation_document_label_document_suffix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $otherRole = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'company_id' => $company->id,
        ]);

        $userMaker = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $otherRole->id
        ]);

        $coatingJobOne = CoatingJob::factory()->coatingjobquotation()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::QUOTATION->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $coatingJobTwo = CoatingJob::factory()->coatingjobquotation()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobTwo->save();

        $this->assertTrue(($coatingJobOne->quotation_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $coatingJobOne->quotation_suffix);

        $this->assertTrue(($coatingJobTwo->quotation_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $coatingJobTwo->quotation_suffix);
    }

    public function test_will_follow_coating_jobs_jobcard_document_label_document_prefix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $otherRole = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'company_id' => $company->id,
        ]);

        $userMaker = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $otherRole->id
        ]);

        $coatingJobOne = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::COATING->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $coatingJobTwo = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobTwo->save();

        $this->assertTrue(($coatingJobOne->coating_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $coatingJobOne->coating_prefix);

        $this->assertTrue(($coatingJobTwo->coating_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $coatingJobTwo->coating_prefix);
    }

    public function test_will_follow_coating_jobs_jobcard_document_label_document_suffix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $otherRole = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'company_id' => $company->id,
        ]);

        $userMaker = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $otherRole->id
        ]);

        $coatingJobOne = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::COATING->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $coatingJobTwo = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $userMaker->id,
            'supervisor' => $userMaker->id,
            'quality_by' => $userMaker->id,
            'sale_by' => $userMaker->id,
            'created_by' => $userMaker->id,
        ]);

        $coatingJobTwo->save();

        $this->assertTrue(($coatingJobOne->coating_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $coatingJobOne->coating_suffix);

        $this->assertTrue(($coatingJobTwo->coating_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $coatingJobTwo->coating_suffix);
    }

    public function test_will_load_coating_job_with_associated_items()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
        ]);

        $coatingJob->save();

        CoatingJobMarutiItem::factory(3)->create([
            'coating_job_id' => $coatingJob->id
        ]);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/coatingjobs/' . $coatingJob->id)
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);

        $this->assertTrue(count($data->marutiitems) === 3);
    }

    public function test_will_load_coating_job_with_correct_sub_total_maruti_items()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
        ]);

        $coatingJob->save();

        $coatingJobMarutiItems = CoatingJobMarutiItem::factory(3)->create([
            'coating_job_id' => $coatingJob->id
        ]);

        $marutiItemTotal = array_reduce($coatingJobMarutiItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['sub_total'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/coatingjobs/' . $coatingJob->id)
            ->decodeResponseJson()
            ->json;

        $coatingJob = json_decode($response);

        $this->assertTrue($coatingJob->sub_total == $marutiItemTotal, "Expected ". $marutiItemTotal ." got ".$coatingJob->sub_total );
    }

    public function test_will_load_coating_job_with_correct_sub_total_aluminium_items()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
        ]);

        $coatingJob->save();

        $coatingJobAluminiumItems = CoatingJobAluminiumItem::factory(1)->create([
            'coating_job_id' => $coatingJob->id
        ]);

        $aluminiumItemTotal = array_reduce($coatingJobAluminiumItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['sub_total'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/coatingjobs/' . $coatingJob->id)
            ->decodeResponseJson()
            ->json;

        $coatingJob = json_decode($response);

        $this->assertTrue($coatingJob->sub_total == $aluminiumItemTotal, "Expected ". $aluminiumItemTotal ." got ".$coatingJob->sub_total );
    }

    public function test_will_load_coating_job_with_correct_sub_total_steel_items()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
        ]);

        $coatingJob->save();

        $coatingJobSteelItems = CoatingJobSteelItem::factory(1)->create([
            'coating_job_id' => $coatingJob->id
        ]);

        $steelItemTotal = array_reduce($coatingJobSteelItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['sub_total'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/coatingjobs/' . $coatingJob->id)
            ->decodeResponseJson()
            ->json;

        $coatingJob = json_decode($response);

        $this->assertTrue($coatingJob->sub_total == $steelItemTotal, "Expected ". $steelItemTotal ." got ".$coatingJob->sub_total );
    }
}
