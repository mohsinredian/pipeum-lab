<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class AuthoriseUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
/*    public function handle($request, Closure $next)
    {
        
        $currentPath= Route::getCurrentRoute()->getActionName();
        $routeName = explode('@', $currentPath);

        // $forbidden_abilities = Auth::user()->getForbiddenAbilities()->toArray();
        // $forbidden_abilities = array_column($forbidden_abilities, 'name');
        
        $abilities = Auth::user()->getAbilities()->toArray();
        $abilities = array_column($abilities, 'name');

        // dd($routeName[1], $abilities);
        // if (!$request->ajax() && ! Gate::allows($routeName[1])) {
        // if (Gate::denies($routeName[1])) {
            // return redirect()->route('unauthorised_access');
        
        if(! $request->ajax() && ! in_array($routeName[1], $abilities)){
            abort(403, 'Access Denied');
        }
        return $next($request);
    }
*/
public function handle($request, Closure $next)
    {
        
        $currentPath= Route::getCurrentRoute()->getActionName();
        $routeName = explode('@', $currentPath);
 
        // $forbidden_abilities = Auth::user()->getForbiddenAbilities()->toArray();
        // $forbidden_abilities = array_column($forbidden_abilities, 'name');
        
        $abilities = Auth::user()->getAbilities()->toArray();
        $abilities = array_column($abilities, 'name');
 
        // dd($routeName[1], $abilities);
        if (!$request->ajax() && ! Gate::allows($abilities)) {
        // if (Gate::denies($routeName[1])) {
            return redirect()->route('unauthorised_access');
        // }
    }
        // if(! $request->ajax() && ! in_array($routeName[1], $abilities)){
        //     abort(403, 'Access Denied');
        // }
        return $next($request);
    }
}
