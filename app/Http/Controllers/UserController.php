<?php

namespace App\Http\Controllers;

use App\Enums\CoatingJobStatusEnum;
use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', User::class);
    $users = User::where([
      ['company_id', '=', auth()->user()->company_id],
      ['id', '<>', auth()->user()->id],
    ])->orderBy('id', 'desc')->get();

    if ($request->is('api/*')) {
      return $users;
    } else {
      $roles = Role::all();
      return view('system.user.index', [
        'users' => $users,
        'roles' => $roles
      ]);
    }
  }

  public function store(Request $request)
  {
    $this->authorize('create', User::class);

    $user = new User();

    $user->fill([
      'username' => strtoupper($request->username),
      'email' => strtoupper($request->email),
      'password' => Hash::make($request->password),
      'role_id' => $request->role_id,
      'company_id' => auth()->user()->company_id,
      'reset_token' => Str::random(15)
    ]);

    if ($user->save()) {
      if ($request->is('api/*')) {
        return $user;
      } else {
        $content = '<p>Hello,</p><p>You\'ve been invited to be part of the system users please click <a href="';
        $content .= env('APP_URL') . '/set-password/' . $user->reset_token;
        $content .= '">here</a> to set your password</p>';

        $this->composeTextEmail($user->email, "SYSTEM USER INVITATION", $content);
        return back()->with('Success', 'Created successfully');
      }
    } else {
      return back()->with('Error', 'Failed to create');
    }
  }

  public function update(Request $request, User $user)
  {
    $user->fill([
      'username' => $request->username,
      'role_id' => $request->role_id,
    ]);

    if ($user->update()) {
      if ($request->is('api/*')) {
        return $user;
      } else {
        return back()->with('Success', 'Edited successfully');
      }
    } else {
      return back()->with('Error', 'Failed to edit');
    }
  }

  public function login()
  {
    return view('system.index');
  }

  public function loginUser(Request $request)
  {
    $request->validate([
      'u_email' => 'required',
      'password' => 'required'
    ]);

    $user = User::where('email', $request->u_email)->orWhere('username', $request->u_email)->first();
    if ($user) {
      if ($user->email_verified_at) {
        if (
          Auth::attempt(['email' => $user->email, 'password' => $request->password]) ||
          Auth::attempt(['username' => $user->username, 'password' => $request->password])
        ) {
          if (isset($request->remember_me)) {
            Auth::loginUsingId($user->id, true);
          } else {
            Auth::loginUsingId($user->id);
          }
          return redirect('/dashboard')->with('Success', 'Successfully authenticated, welcome!');
        } else {
          return redirect("/")->with('Error', 'Authentication failed. Invalid credentials');
        }
      } else {
        return redirect("/")->with('Error', 'Authentication failed. Unverified email');
      }
    } else {
      return redirect("/")->with('Error', 'Authentication failed.');
    }
  }

  public function logout()
  {
    Auth::logout();
    return redirect("/")->with('Success', 'Logged out');
  }

  public function dashboard()
  {

    $date = Carbon::now()->subDays(1)->format('Y-m-d');

    $coatingJobs = Cache::remember('coating_jobs_dashboard', (60 * 60 * 3), function () use ($date) {
      return CoatingJob::selectRaw('status, COUNT(*) as amount')
        ->where('created_by', auth()->user()->id)
        ->whereNot('status', CoatingJobStatusEnum::CANCELLED->value)
        ->whereRaw('MONTHNAME(CURRENT_TIMESTAMP) = MONTHNAME(created_at)')
        ->get();
    });

    $cashSaleDaily = array();

    for ($i = 1; $i < 8; $i++) {
      $date = Carbon::now()->subDays($i)->format('Y-m-d');

      $cashSaleIDs = Cache::remember('dashboard_cash_sales_' . $date, (60 * 60 * 6), function () use ($date) {
        $cashSales = CashSale::select('id')
          ->whereDate('created_at', $date)
          ->whereNull('cancelled_at');
        return $cashSales->pluck('id');
      });

      $coatingJobs = Cache::remember('coating_jobs_cash_sale_dashboard' . $date, (60 * 60 * 6), function () use ($cashSaleIDs) {
        return CoatingJob::select('id')
          ->whereIn('cash_sale_id', $cashSaleIDs)
          ->get();
      });

      $coatingJobSum = 0;

      foreach ($coatingJobs as $coatingJob) {
        $coatingJobSum += $coatingJob->grand_total;
      }

      array_push($cashSaleDaily, array(
        'single_date' => $date,
        'sum' => $coatingJobSum,
        'type' => 'CASH SALE'
      ));
    }

    $invoiceDaily = array();

    for ($i = 1; $i < 8; $i++) {
      $date = Carbon::now()->subDays($i)->format('Y-m-d');

      $invoiceIDs = Cache::remember('dashboard_cash_sales_' . $date, (60 * 60 * 6), function () use ($date) {
        $cashSales = Invoice::select('id')
          ->whereDate('created_at', $date)
          ->whereNull('cancelled_at');
        return $cashSales->pluck('id');
      });

      $coatingJobs = Cache::remember('coating_jobs_invoice_dashboard' . $date, (60 * 60 * 6), function () use ($invoiceIDs) {
        return CoatingJob::select('id')
          ->whereIn('invoice_id', $invoiceIDs)
          ->get();
      });

      $coatingJobSum = 0;

      foreach ($coatingJobs as $coatingJob) {
        $coatingJobSum += $coatingJob->grand_total;
      }

      array_push($invoiceDaily, array(
        'single_date' => $date,
        'sum' => $coatingJobSum,
        'type' => 'INVOICE'
      ));
    }

    if (auth()->user()->role->name === 'ADMIN') {
      $coatingJobsDaily = array();

      for ($i = 1; $i < 8; $i++) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $coatingJob = Cache::remember('dashboard_coating_job_daily' . $date, (60 * 60 * 6), function () use ($date) {
          $coatingJob = CoatingJob::selectRaw('date(created_at) as single_date, COUNT(*) as count')
            ->where('created_by', auth()->user()->id)
            ->whereDate('created_at', $date);

          return $coatingJob->get();
        });
        array_push($coatingJobsDaily, array(
          'single_date' => $date,
          'count' => $coatingJob[0]->count
        ));
      }

      $currentYear = Carbon::now()->format('Y');

      $invoiceYearTrend = Cache::remember('dashboard_invoice_trend_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $invoices = Invoice::selectRaw('COUNT(*) as total_amount, MONTHNAME(created_at) as month_name, YEAR(created_at) as associated_year')
          ->whereNull('cancelled_at')
          ->groupByRaw('month_name, associated_year')
          ->having('associated_year', '>=', ($currentYear - 1))
          ->orderByRaw('month(created_at) ASC');
        return $invoices->get();
      });

      $cashSaleYearTrend = Cache::remember('dashboard_cash_sale_trend_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $cashSale = CashSale::selectRaw('COUNT(*) as total_amount, MONTHNAME(created_at) as month_name, YEAR(created_at) as associated_year')
          ->whereNull('cancelled_at')
          ->groupByRaw('month_name, associated_year')
          ->having('associated_year', '>=', ($currentYear - 1))
          ->orderByRaw('month(created_at) ASC');
        return $cashSale->get();
      });

      $userJobCardDistribution = Cache::remember('dashboard_job_card_distribution_user_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $coatingJobs = CoatingJob::selectRaw('COUNT(*) as total_number, created_by')
          ->whereRaw('YEAR(created_at) = ' . ($currentYear))
          ->groupByRaw('created_by')
          ->with([
            'createdBy:id,username'
          ])
          ->orderBy('total_number', 'DESC')
          ->limit(10);
        return $coatingJobs->get();
      });

      $customerJobCardDistribution = Cache::remember('dashboard_job_card_distribution_customer_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $coatingJobs = CoatingJob::selectRaw('COUNT(*) as total_number, customer_id')
          ->whereRaw('YEAR(created_at) = ' . ($currentYear))
          ->groupByRaw('customer_id')
          ->with([
            'customer:id,customer_name'
          ])
          ->orderBy('total_number', 'DESC')
          ->limit(10);
        return $coatingJobs->get();
      });

      $powderJobCardDistribution = Cache::remember('dashboard_job_card_distribution_powder_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $coatingJobs = CoatingJob::selectRaw('COUNT(*) as total_number, powder_id')
          ->whereRaw('YEAR(created_at) = ' . ($currentYear))
          ->whereNotNull('powder_id')
          ->groupByRaw('customer_id')
          ->orderBy('total_number', 'DESC')
          ->with([
            'powder:id,powder_color'
          ])
          ->limit(10);
        return $coatingJobs->get();
      });

      $inventoryItemMostSold = Cache::remember('dashboard_inventory_item_most_sold_' . $currentYear, (60 * 60 * 6), function () use ($currentYear) {
        $coatingJobs = CoatingJobMarutiItem::selectRaw('COUNT(*) as total_number, inventory_item_id')
          ->whereNull('custom_item_name')
          ->whereNotNull('inventory_item_id')
          ->whereRaw('YEAR(created_at) = ' . ($currentYear))
          ->groupByRaw('inventory_item_id')
          ->orderBy('total_number', 'DESC')
          ->having('total_number', '>', 5)
          ->with([
            'inventoryitem:id,type,item_name'
          ])
          ->limit(10);
        return $coatingJobs->get();
      });

      return view('system.dashboard', [
        'coatingJobs' => $coatingJobs,
        'cashSaleDaily' => $cashSaleDaily,
        'invoiceDaily' => $invoiceDaily,
        'coatingJobsDaily' => $coatingJobsDaily,
        'invoiceYearTrend' => $invoiceYearTrend,
        'cashSaleYearTrend' => $cashSaleYearTrend,
        'userJobCardDistribution' => $userJobCardDistribution,
        'customerJobCardDistribution' => $customerJobCardDistribution,
        'powderJobCardDistribution' => $powderJobCardDistribution,
        'inventoryItemMostSold' => $inventoryItemMostSold
      ]);
    } else {
      return view('system.dashboard', [
        'coatingJobs' => $coatingJobs,
        'cashSaleDaily' => $cashSaleDaily,
        'invoiceDaily' => $invoiceDaily,
      ]);
    }
  }

  public function setPassword(Request $request)
  {
    if ($request->reset_token) {
      $user = User::where('reset_token', $request->reset_token)->first();
      if ($user) {
        return view('system.user.set-password', [
          'custom_token' => $request->reset_token
        ]);
      } else {
        abort(403, 'Not authorized');
      }
    } else {
      abort(403, 'Not authorized');
    }
  }

  public function resetPassword(Request $request)
  {
    $request->validate([
      'reset_token' => ['required'],
      'password' => ['required'],
    ]);
    $user = User::where('reset_token', $request->reset_token)->first();
    if ($user) {
      $user->fill([
        'password' => Hash::make($request->password),
        'reset_token' => null,
        'email_verified_at' => Carbon::now()
      ]);
      if ($user->update()) {
        return redirect('/')->with('Success', 'Password successfully set for login');
      } else {
        return back()->with('Error', 'Failed');
      }
    } else {
      return back()->with('Error', 'Failed');
    }
  }

  public function forgotPassword(Request $request)
  {
    $request->validate([
      'email' => ['required']
    ]);
    $user = User::where('email', $request->email)->first();
    if ($user) {
      $user->fill([
        'reset_token' => bin2hex(random_bytes(20))
      ]);
      if ($user->update()) {
        $content = '<p>Hello,</p><p>To reset you\'re password please click <a href="';
        $content .= env('APP_URL') . '/set-password/' . $user->reset_token;
        $content .= '">here</a></p>';

        $this->composeTextEmail($user->email, "PASSWORD RESET", $content);

        return redirect('/')->with('Success', 'Password reset email sent');
      } else {
        return back()->with('Error', 'Failed');
      }
    } else {
      return back()->with('Error', 'Failed');
    }
  }
}
