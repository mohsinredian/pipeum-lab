<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()){
            
            $user = Auth::user();
            
            if(! $user->isA('Admin') && ! $user->isA('Subadmin') && ! $user->isA('Approver') && ! $user->isA('HOD') && ! $user->isA('CEO') && ! $user->isA('User')){

                return redirect('/admin/auth');
            }

            return $next($request);
        }
        else{
            return redirect('/admin/auth')->with('error', 'Your session has expired. Please refresh the page.');
        }
    }

}
