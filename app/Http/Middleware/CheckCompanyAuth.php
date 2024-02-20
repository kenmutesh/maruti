<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Session;

class CheckCompanyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     public function handle($request, Closure $next)
     {
         if (Session::get('activation_company_uid') === '' || Session::get('activation_company_uid') === NULL) {
           if (Session::get('auth_company_uid') !== NULL || Session::get('auth_user_uid') !== NULL || Session::get('auth_warehouse_uid') !== NULL || Session::get('auth_role_uid') !== NULL) {
              return redirect('/dashboard')->with('Success','Already authenticated!');
           }
            return $next($request);
         }

        return redirect('/setpassword')->with('Success','Already in activation session!');
     }
}
