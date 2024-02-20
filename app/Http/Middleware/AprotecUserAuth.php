<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AprotecUserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // check if already authorised or cookie exists, direct to dashboard
     public function handle($request, Closure $next)
     {
        if (Session::get('auth_aprotec_uid') != NULL) {
          return $next($request);
        }else if (isset($_COOKIE['auth_aprotec_cookie'])) {
          Session::put('auth_aprotec_uid', $_COOKIE['auth_aprotec_cookie']);
          return $next($request);
        }else {
          return redirect('/aprotec')->with('Error','Not authenticated');
        }
     }
    }
