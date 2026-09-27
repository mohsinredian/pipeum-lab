<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OTPController;
use Auth;

class VerifyOTP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        $session = session()->get('otp_verified');
        $AuthRoutes = $otpVerifiedRoutes = config('app.otp_verified_routes', []);
        $routes = $request->route()->uri;
        $requiresOtpVerification = in_array($routes, $AuthRoutes);
        $otpRecord = $user->otp()
                ->whereNull('used_at')
                ->latest()
                ->first();
        if ($requiresOtpVerification) {
            if ((!isset($session[$routes]) || !$session[$routes])) {
                $OTPController = new OTPController();
                $OTPController->sendOTP($request);
                if (preg_match('/opex/', $routes)) {
                    return redirect()->route('opex_otp', ['route' => $routes]);
                } else {
                    return redirect()->route('capex_otp', ['route' => $routes]);
                }
            }
        } elseif (!isset($session['login']) || !$session['login']) {
            
            return redirect()->route('view-otp');
        }

        return $next($request);
    }
}
