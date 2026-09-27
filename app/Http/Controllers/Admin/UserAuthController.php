<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Config;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Session;
use Validator;
use Carbon\Carbon;
use App\Models\Employee;
use App\Http\Controllers\Admin\OTPController;


class UserAuthController extends Controller
{
    // Show login view
  
    public function login_view(Request $request)
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        } else {
            return view('admin.auth');
        }
    }
    /**
     * [login description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */

     public function changePassword(Request $request)
     {
     
         return view('admin.password_change');
     }
     public function validatePasswordChangeRequest(Request $request, $user)
     {
         $request->validate([
             
             'password' => [
                 'required',
                 'min:8',
                 'confirmed',
                 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
             ],
         ], ['password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).']);
     }
     
     public function savechangePassword(Request $request)
     {
         // Get the current user
         $user = Auth::user();
     
         // Validate the request
         $this->validatePasswordChangeRequest($request, $user);
     
         // Update the user's password and last password change date
         $user->password = Hash::make($request->input('password'));
         $user->last_password_change_date = Carbon::now();
         $user->disabled=false;
         $user->save();
     
         // Redirect to the dashboard
         return redirect('admin/auth');
     }
     public function login(Request $request)
     {
         try {
             $request_input = $request->except('_token');
     
             $rules = [
                 'login_field' => ['required'],
                 'password' => 'required',
                 'captcha' => 'required|captcha',
             ];
     
             $messages = [
                 'login_field.required' => 'Please enter your email or username',
                 'password.required' => 'Please enter your password',
                 'captcha.required' => 'Invalid captcha, please try again',
             ];
     
             $validator = Validator::make($request_input, $rules, $messages);
     
             if ($validator->fails()) {
                 $response['msg'] = $validator->errors()->toArray();
                 $response['result'] = 'error';
             } else {
                 $loginField = $request_input['login_field'];
                 $user = User::where(function ($query) use ($loginField) {
                     $query->where('email', $loginField)
                         ->orWhere('username', $loginField);
                 })->first();
                 
                 if (!is_null($user)) {
                     if (Hash::check($request_input['password'], $user['password'])) {
                         Auth::login($user, isset($request_input['remember']));
                         Auth::logoutOtherDevices($request_input['password']);
     
                         if (Auth::check()) {
                             $user = Auth::user();
                             $lastlogin = $user->last_login_at !== null ? date('d-M-y h:i A', strtotime($user->last_login_at)) : 'First time logged in';
                             session()->put('notifications', $lastlogin);
                         }
     
                         // Check if the user needs to change their password and if the notification has not already been displayed
                         // Check if the user needs to change their password
                         $lastPasswordChangeDate =  Carbon::parse($user->last_password_change_date);
                         $current_date = Carbon::parse(Carbon::now());
                         $checkdate = $lastPasswordChangeDate->diffInDays($current_date) + 1;
                         if ($lastPasswordChangeDate &&  $checkdate >= 15) {
                             // Disable the user's account
                             $user->disabled = true;
                             $user->save();
                         }
     
                         $daysSincePasswordChange = $lastPasswordChangeDate ? $checkdate : null;
                         if ($daysSincePasswordChange !== null && $daysSincePasswordChange >= 10 && $checkdate < 15 ) {
                             $daysRemaining = 15 - $daysSincePasswordChange;
     
                             // Set a notification message for the user to change their password
                             $notification = [
                                 'type' => 'warning',
                                 'message' => "You must change your password within $daysRemaining days. <a href='" . route('expiry.password') . "'>Change Password</a>",
                             ];
                            //  session()->flash('notification', $notification);
                            session()->put('notification', $notification);
                         }
                        //  $OTPController = new OTPController();
                        //  $OTPController->sendOTP($request);
                         $response['result'] = 'success';
                         $response['msg'] = 'Login successful';
                         $response['approver'] = $user->isA('Approver');
                         $response['admin'] = $user->isA('Admin');
                         $response['hod'] = $user->isA('HOD');
                         $response['ceo'] = $user->isA('CEO');
                         $response['user'] = $user->isA('User');
                         $response['subadmin'] = $user->isA('Subadmin');

                         $data = DB::table('assigned_roles as asrole')
                    ->leftJoin('users as usr', 'usr.id', '=', 'asrole.entity_id')
                    ->where('usr.id', $user->id)
                    ->select('asrole.role_id', 'asrole.entity_id', 'usr.disabled')
                    ->first();
                        if (!$data) {
                            $response['result'] = 'error';
                            $response['msg'] = 'Your id has been blocked Please Conctacts to adminitsttion';

                        }
                     } else {
                         $response['result'] = 'error';
                         $response['msg'] = ['password' => ['Wrong password. Please try again.']];
                     }
                 } else {
                     $response['result'] = 'error';
                     $response['msg'] = ['login_field' => ['Invalid email or username.']];
                 }
             }
         } catch (Exception $e) {
             app(\App\Exceptions\Handler::class)->report($e);
             $response['result'] = 'failure';
             $response['msg'] = $e->getMessage();
         }
     
        //  dd( response()->json($response));
         return response()->json($response);
     }
     
    /**
     * [logout description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function logout(Request $request)
    {
        $user = Auth::User();
        
        if ($user) {

        date_default_timezone_set('Asia/Kolkata');
            
        $user->last_login_at = now();
        $user->otp_verify = false;
            
        $user->save();
            
        }
        if($user->role_id != 1){
            $employee= Employee::where('email',$user->email)->first();
            if(!empty($employee->last_login_at)){
            $employee->last_login_at = now();
           $employee->save();
            }
           }
        Session::flush();
        Auth::logout();


        return redirect('/admin/auth');
    }
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img('math')]);
    }   

    public function showForgotForm(){
        return view('admin.ResetPassword.forgot');
    }
    public function sendResetLink(Request $request ){
     $request->validate([
       'email'=>'required|email|exists:users,email'
     ]);

     $token = \Str::random(64);
     DB::table('password_resets')->insert([
        'email'=>$request->email,
        'token'=>$token,
        'created_at'=>Carbon::now(),
     ]);

       $UserName= Employee::select('name')->where('email',$request->email)->first();
       $action_link=route('reset.password.form',['token'=>$token,'email'=>$request->email]);
       $emailID = $request->email;

       \Mail::send('admin/ResetPassword/email-forgot',['action_link'=>$action_link,'emailID'=>$emailID,'UserName'=>$UserName],function($message) use($request){
        $message->from(env('MAIL_FROM_ADDRESS'), 'Need Validation');
        $message->to($request->email)
        ->subject('Reset Password');
       });

      return back()->with('success','E-mailed password reset link successfully!');
    }
 public function showresetForm(Request $request, $token=null){
  return view('admin.ResetPassword.resetpass')->with(['token'=>$token,'email'=>$request->email]);
 }

 public function resetPassword(Request $request){
    $request->validate([
        'email'=>'required|email|exists:users,email',
        'password'=>'required|min:5|confirmed',
        'password_confirmation'=>'required',
      ]);

      $check_token = \DB::table('password_resets')->where([
        'email'=>$request->email,
        'token'=>$request->token,
      ])->first();

      if(!$check_token){
        return back()->withInput()->with('fail','Invalid token');

      }else{
          User::where('email',$request->email)->update([
            'password'=>\Hash::make($request->password),
            'last_password_change_date'=>now()
          ]);

           Employee::where('email',$request->email)->update([
            'password'=> $request->password
          ]);

          \DB::table('password_resets')->where([
            'email'=>$request->email
          ])->delete();

          return redirect('/admin/auth')->with('success','Your password has been changed successfully!')->with('varifiedEmail',$request->email);
      }
   }
}
