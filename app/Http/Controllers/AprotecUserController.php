<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatusEnum;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\AprotecUser;
use Illuminate\Support\Facades\Hash;

class AprotecUserController extends Controller
{
    public function index()
    {
      return view('aprotec.index');
    }

    public function login(Request $request)
    {
      $request->validate([
          'email_uname' => 'required',
          'password' => ['required'],
      ]);

      $results = AprotecUser::where('email', $request->email_uname)
                  ->orWhere('username', $request->email_uname)  
                  ->first();

      if ($results != NULL) {
        if ($results->count() > 0) {
          if (Hash::check($request->password, $results->password)) {
            session()->put('auth_aprotec_uid', $results->id);
            if (isset($request->remember_me)) {
              setcookie('auth_aprotec_cookie', $results->id, time() + (86400 * 30), "/aprotec");
            }
            return redirect('/aprotec/dashboard')->with('Success', 'Logged in successfully');
          }else {
            return back()->withInput()->with('Error', 'Invalid credentials');
          }
        }
      }else {
        return back()->withInput()->with('Error', 'Invalid credentials');
      }
    }

    public function dashboard()
    {
      $companies = Company::orderBy('id', 'desc')->with(['users'])->get();
      $subscriptionStatus = SubscriptionStatusEnum::cases();
      return view('aprotec.dashboard', [
        'companies' => $companies,
        'subscriptionStatus' => $subscriptionStatus,
        'subscriptionStatusEnum' => SubscriptionStatusEnum::class
      ]);
    }

    public function logout(Request $request)
    {
      $request->session()->forget('auth_aprotec_uid');
      if (isset($_COOKIE['auth_aprotec_cookie'])) {
        setcookie('auth_aprotec_cookie', $request->session()->forget('auth_aprotec_uid'), time() + (86400 * 30), "/aprotec");
      }
      return redirect('/aprotec')->with('Success', 'Logged out successfully');
    }

    public function profile()
    {
      $profile = AprotecUser::where('id', session()->get('auth_aprotec_uid'))->first();

      return view('aprotec.profile', [
        'profile' => $profile,
      ]);
    }
}
