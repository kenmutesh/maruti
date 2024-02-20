<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatusEnum;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class CompanyFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    // TODO:: Smoke test to load companies

    public function test_can_create_a_company(){
        $company = Company::factory()->create();
        $this->assertTrue($company->save(), "Created a company succesfully");
    }

    public function test_can_default_subscription_is_incomplete(){
        $company = Company::factory()->create();
        $this->assertTrue(($company->subscription_status === SubscriptionStatusEnum::INCOMPLETE), "Newly created companies have an incomplete status");
    }

    public function test_cannot_save_duplicate_email(){
        $companyOneDetail = array(
            'email' => 'test@test.com',
        );
        $companyTwoDetail = array(
            'email' => 'test@test.com',
        );
        Company::factory()->create($companyOneDetail);
        try {
            Company::factory()->create($companyTwoDetail);
            $this->fail("Application can create company with duplicate emails");
        } catch (\Throwable $th) {
            $this->assertTrue(true, "You cannot create aprotec user with duplicate username");
        }
    }

    public function test_correct_expiry_date_attribute(){
        $company = Company::factory()->create();
        $dateCreated = Carbon::parse($company->created_at);
        $dateCreated->addDays($company->key_validity_period);
        $this->assertTrue(($company->subscription_expiry_date->timestamp === $dateCreated->timestamp), "Subscription end dates do not match");
    }

    // TODO:: Ensure each company has the same number of document labels as those listed on App\Enums\DocumentLabelsEnum
}
