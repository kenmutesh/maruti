<?php

namespace App\Http\Controllers;

use App\Enums\DocumentLabelsEnum;
use App\Enums\TaxTypesEnum;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Role;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'company_name' => 'required',
      'email' => 'required',
      'activation_key' => 'required',
      'subscription_status' => 'required',
      'subscription_duration' => 'required',
    ]);
    $company = new Company();
    $days = 1;
    if ($request->subscription_nature == 'Days') {
      $days = $request->subscription_duration;
    } else if ($request->subscription_nature == 'Months') {
      $days = $request->subscription_duration * 30;
    } else {
      $days = $request->subscription_duration * 365;
    }
    $company->fill([
      'name' => strtoupper($request->company_name),
      'email' => strtoupper($request->email),
      'activation_key' => $request->activation_key,
      'subscription_status' => $request->subscription_status,
      'subscription_duration' => $days,
      'created_by' => session()->get('auth_aprotec_uid')
    ]);
    if ($company->save()) {
      $this->bootstrapCompanyInfo($request, $company);

      $messageBody = "
        <p>Please proceed with the link below to activate your account using the key</p>
        <p>" . $request->activation_key . "</p>
        <p><a href='" . env('APP_URL') . "/activate'>" . env('APP_URL') . "/activate</a>
        ";

      $this->composeTextEmail($request->email, 'ACCOUNT ACTIVATION', $messageBody);
      return back()->with('Success', 'Company added');
    } else {
      return back()->with('Errors', 'Company failed to add please retry');
    }
  }

  public function update(Request $request, Company $company)
  {
    if ($request->subscription_duration == "") {
      $company->fill([
        'name' => $request->company_name,
        'email' => $request->email,
        'subscription_status' => $request->subscription_status,
      ]);
    } else {
      $days = 1;
      if ($request->subscription_nature == 'Days') {
        $days = $request->subscription_duration;
      } else if ($request->subscription_nature == 'Months') {
        $days = $request->subscription_duration * 30;
      } else {
        $days = $request->subscription_duration * 365;
      }
      $company->fill([
        'name' => $request->company_name,
        'email' => $request->email,
        'subscription_status' => $request->subscription_status,
        'subscription_start_date' => Carbon::now(),
        'subscription_duration' => $days
      ]);
    }
    if ($company->update()) {
      return back()->with('Success', 'Company Details Updated');
    } else {
      return back()->with('Error', 'Failed to edit company details please retry');
    }
  }

  public function destroy(Company $company)
  {
    if ($company->delete()) {
      return back()->with('Success', 'Company deleted');
    } else {
      return back()->with('Error', 'Failed to delete company please retry');
    }
  }

  private function bootstrapCompanyInfo(Request $request, Company $Company)
  {
    $user = new User();

    $username = $request->admin_name == '' ? strtoupper($request->admin_name) : strtoupper($request->company_name);

    $role = Role::factory()->admin()->create([
      'company_id' => $Company->id,
    ]);
    $role->save();

    $user->fill([
      'username' => $username,
      'email' => $Company->email,
      'role_id' => $role->id,
      'company_id' => $Company->id
    ]);

    $user->save();

    $documentLabels = DocumentLabelsEnum::cases();

    foreach ($documentLabels as $document) {
      $documentLabel = DocumentLabel::factory()->create([
        'document' => $document->value,
        'document_prefix' => 'A',
        'document_suffix' => '1',
        'company_id' => $Company->id
      ]);
      $documentLabel->save();
    }

    $taxes = TaxTypesEnum::cases();
    foreach ($taxes as $tax) {
      $tax = Tax::factory()->create([
        'percentage' => 16,
        'type' => $tax->value,
        'company_id' => $Company->id
      ]);
      $tax->save();
    }
  }

  public function activate(){
    return view('company.activate');
  }

  public function activateCompany(Request $request){
    $request->validate([
      'email' => 'required',
      'activation_key' => 'required',
      'password' => 'required|min:8',
      'conf_password' => 'required|min:8',
    ]);
    if($request->conf_password != $request->password){
      return back()->with('Error', 'Invalid password and confirm password');
    }
    $company = Company::where([
      'activation_key' => $request->activation_key,
      'subscription_start_date' => null,
    ])->first();
    if($company){
      $company->subscription_start_date = Carbon::now();
      $company->update();

      $user = User::where([
        'email' => $company->email,
        'company_id' => $company->id
      ])->first();

      $user->fill([
        'email_verified_at' => Carbon::now(),
        'password' => Hash::make($request->password)
      ]);

      $user->update();

      return redirect('/')->with('Success','Account details set. You can now log in');
    }else{
      return back()->with('Error', 'Invalid company details');
    }
  }
}
