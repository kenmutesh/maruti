<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class InvoiceFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_will_load_invoices_for_only_logged_in_user_company()
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

            Invoice::factory(3)->create([
                'company_id' => $company->id,
            ]);

            Invoice::factory(2)->cancelled()->create([
                'company_id' => $company->id,
            ]);
        }

        $response = $this->actingAs($loggedInUser)
            ->get('/api/invoices')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $invoice) {
                if ($invoice->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $invoice->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load cash sales from other companies " . $th->getMessage());
        }
    }

    public function test_will_follow_invoice_document_label_document_prefix_sequence()
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

        $invoiceOne = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $invoiceOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::INVOICE->value,
            'company_id' => $user->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $invoiceTwo = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $invoiceTwo->save();

        $this->assertTrue(($invoiceOne->invoice_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $invoiceOne->invoice_prefix);

        $this->assertTrue(($invoiceTwo->invoice_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $invoiceTwo->invoice_prefix);
    }

    public function test_will_follow_invoice_document_label_document_suffix_sequence()
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

        $invoiceOne = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $invoiceOne->save();

        $invoiceTwo = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $invoiceTwo->save();

        $this->assertTrue(($invoiceOne->invoice_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $invoiceOne->invoice_suffix);

        $this->assertTrue(($invoiceTwo->invoice_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $invoiceTwo->invoice_suffix);
    }

    public function test_will_load_invoice_with_correct_sub_total()
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

        $invoice = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
            'invoice_id' => $invoice->id
        ]);

        $coatingJob->save();

        CoatingJobMarutiItem::factory(3)->create([
            'coating_job_id' => $coatingJob->id
        ]);
        
        $coatingJob = $coatingJob->fresh();

        $invoice = $invoice->fresh();

        $this->assertTrue($invoice->sub_total == $coatingJob->sub_total, "Expected ". $coatingJob->sub_total ." got ".$invoice->sub_total );
    }

    public function test_will_load_invoice_amount_due_correctly()
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

        $invoice = Invoice::factory()->create([
            'company_id' => $company->id,
        ]);

        $coatingJob = CoatingJob::factory()->jobcard()->create([
            'company_id' => $company->id,
            'prepared_by' => $user->id,
            'supervisor' => $user->id,
            'quality_by' => $user->id,
            'sale_by' => $user->id,
            'created_by' => $user->id,
            'invoice_id' => $invoice->id
        ]);

        $coatingJob->save();

        CoatingJobMarutiItem::factory(3)->create([
            'coating_job_id' => $coatingJob->id,
            'unit_price' => 200,
        ]);

        $payment = Payment::factory()->create();

        InvoicePayment::factory()->create([
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount_applied' => 200
        ]);

        $payment = $payment->fresh();
        
        $coatingJob = $coatingJob->fresh();

        $invoice = $invoice->fresh();

        $this->assertTrue($invoice->amount_due == ($invoice->grand_total - $payment->paid_amount), "Expected ". ($invoice->grand_total - $payment->paid_amount) ." got ".$invoice->amount_due );
    }
}
