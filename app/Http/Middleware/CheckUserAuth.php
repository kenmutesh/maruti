<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckUserAuth
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
         if (session()->get('auth_company_uid') === '' || session()->get('auth_user_uid') === NULL || session()->get('auth_warehouse_uid') === NULL || session()->get('auth_role_uid') === NULL) {
            return redirect('/')->with('Error','Not authenticated!');
         }

         return $next($request);

     }
}
