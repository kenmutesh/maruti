<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DocumentLabelFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_document_labels_for_a_company()
    {
        $company = Company::factory()->create();
        $documentLabels = DocumentLabelsEnum::cases();
        // associate all companies with document labels
        try {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
            $this->assertTrue(true, "The company and associated document labels can be created");
        } catch (\Throwable $th) {
            $this->fail("The company and associated document labels cannot be created. " . $th->getMessage());
        }
    }

    public function test_will_load_document_labels_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();
        $documentLabels = DocumentLabelsEnum::cases();
        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
        }

        $response = $this->actingAs($user)
            ->get('/api/documentlabels')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $documentLabel) {
                if ($documentLabel->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $documentLabel->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load document labels from other companies " . $th->getMessage());
        }
    }

    public function test_will_not_load_document_labels_if_not_an_admin()
    {
        $companies = Company::factory(1)->create();
        $documentLabels = DocumentLabelsEnum::cases();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
        }

        $response = $this->actingAs($user)
            ->get('/api/documentlabels');
        $response->assertStatus(403);
    }

    public function test_admin_role_can_edit_document_label()
    {
        $companies = Company::factory(1)->create();
        $documentLabels = DocumentLabelsEnum::cases();
        $role = Role::factory()->admin()->create();
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);

        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
        }

        $documentLabelEdit = DocumentLabel::get()->first();

        $response = $this->actingAs($user)
            ->put('/api/documentlabels/'.$documentLabelEdit->id, 
                [
                    'id' => $documentLabelEdit->id,
                    'document_prefix' => null,
                    'document_suffix' => 2000,
                ]);
        $response->assertStatus(200);
    }

    public function test_other_roles_cannot_edit_document_label()
    {
        $companies = Company::factory(1)->create();
        $documentLabels = DocumentLabelsEnum::cases();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE'
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);

        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }
        }

        $documentLabelEdit = DocumentLabel::get()->first();

        $response = $this->actingAs($user)
            ->put('/api/documentlabels/'.$documentLabelEdit->id, 
                [
                    'id' => $documentLabelEdit->id,
                    'document_prefix' => null,
                    'document_suffix' => 2000,
                ]);
        $response->assertStatus(403);
    }
}

