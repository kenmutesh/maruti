<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class CashSaleFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_will_load_cash_sales_for_only_logged_in_user_company()
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

            CashSale::factory(5)->normalcashsale()->create([
                'company_id' => $company->id,
            ]);

            CashSale::factory(3)->extcashsale()->create([
                'company_id' => $company->id,
            ]);

            CashSale::factory(2)->cancelled()->create([
                'company_id' => $company->id,
            ]);
        }

        $response = $this->actingAs($loggedInUser)
            ->get('/api/cashsales')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $cashSale) {
                if ($cashSale->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $cashSale->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load cash sales from other companies " . $th->getMessage());
        }
    }

    public function test_will_follow_normal_cash_sale_document_label_document_prefix_sequence()
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

        $cashSaleOne = CashSale::factory()->normalcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::CASHSALE->value,
            'company_id' => $user->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $cashSaleTwo = CashSale::factory()->normalcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleTwo->save();

        $this->assertTrue(($cashSaleOne->cash_sale_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $cashSaleOne->cash_sale_prefix);

        $this->assertTrue(($cashSaleTwo->cash_sale_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $cashSaleTwo->cash_sale_prefix);
    }

    public function test_will_follow_external_cash_sale_document_label_document_prefix_sequence()
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

        $cashSaleOne = CashSale::factory()->extcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::EXTCASHSALE->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $cashSaleTwo = CashSale::factory()->extcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleTwo->save();

        $this->assertTrue(($cashSaleOne->ext_cash_sale_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $cashSaleOne->ext_cash_sale_prefix);

        $this->assertTrue(($cashSaleTwo->ext_cash_sale_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $cashSaleTwo->ext_cash_sale_prefix);
    }

    public function test_will_follow_normal_cash_sale_document_label_document_suffix_sequence()
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

        $cashSaleOne = CashSale::factory()->normalcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::CASHSALE->value,
            'company_id' => $user->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $cashSaleTwo = CashSale::factory()->normalcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleTwo->save();

        $this->assertTrue(($cashSaleOne->cash_sale_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $cashSaleOne->cash_sale_suffix);

        $this->assertTrue(($cashSaleTwo->cash_sale_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $cashSaleOne->cash_sale_suffix);
    }

    public function test_will_follow_external_cash_sale_document_label_document_suffix_sequence()
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

        $cashSaleOne = CashSale::factory()->extcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::EXTCASHSALE->value,
            'company_id' => $user->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $cashSaleTwo = CashSale::factory()->extcashsale()->create([
            'company_id' => $company->id,
        ]);

        $cashSaleTwo->save();

        $this->assertTrue(($cashSaleOne->ext_cash_sale_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $cashSaleOne->ext_cash_sale_suffix);

        $this->assertTrue(($cashSaleTwo->ext_cash_sale_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $cashSaleOne->ext_cash_sale_suffix);
    }

    public function test_will_load_cash_sale_with_correct_sub_total()
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

        $cashSale = CashSale::factory()->normalcashsale()->create([
            'company_id' => $company->id,
        ]);

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
            'cash_sale_id' => $cashSale->id
        ]);

        $coatingJob->save();

        CoatingJobMarutiItem::factory(3)->create([
            'coating_job_id' => $coatingJob->id
        ]);
        
        $coatingJob = $coatingJob->fresh();

        $cashSale = $cashSale->fresh();

        $this->assertTrue($cashSale->sub_total == $coatingJob->sub_total, "Expected ". $coatingJob->sub_total ." got ".$cashSale->sub_total );
    }
}
