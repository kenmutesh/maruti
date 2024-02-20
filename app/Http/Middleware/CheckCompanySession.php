<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Session;

class CheckCompanySession
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

           return redirect('/activate')->with('Error','No activation session in place!');

         }

         return $next($request);
     }
}
