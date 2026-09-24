<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OTPLog;
use App\Models\Otps as OTP;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use DateTime;
use Log;

class OTPController extends Controller
{

    public function sendOTP(Request $request)
    {
        // dd($request);
        $user = auth()->user();
        $role_id = $user->role_id;
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }
       
        // dd($data);
        if ($role_id == '1')
        {
            // $otp = 2356;
            $otp = rand(100000, 999999);
            // Calculate OTP expiration time (5 minutes from now)
            $otpExpiration = now()->addMinutes(3);
        
            // Update or create the OTP record
            $otpRecord = OTP::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp' => $otp,
                    'expiry' => $otpExpiration,
                    'used_at' => null // set used_at to null on update
                ]
            );
            
            return view('admin.otp_verify')->with(['otp_required' => true, 'otp' => $otp ,'otp_expiration' => $otpExpiration]);
        }
        
        else{
           

            // dd(1);
        $otp = rand(100000, 999999);

        // Calculate OTP expiration time (5 minutes from now)
        $otpExpiration = now()->addMinutes(3);
    
        // Update or create the OTP record
        $otpRecord = OTP::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expiry' => $otpExpiration,
                'used_at' => null // set used_at to null on update
            ]
        );
    
        // $app_name = "NEEDVALIDATION";
        // $encrypt_key ="!!B\$E\$@@*SMS";
        // $companycode = "BRPL";
        // $vendor_code = "REDIAN";
        // $mobile = $user->mobile_number; // Assuming you have the user's phone number in the user model
        // $sms_type = "OTP"; // Updated SMS type
        // $EmpCode = "895623";
        // // dd($app_name,$encrypt_key,$companycode,$vendor_code,$mobile,$sms_type,$EmpCode);
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://japi.instaalerts.zone/failsafe/HttpData_SS',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'POST',

        // CURLOPT_POSTFIELDS =>'<a2wml version="2.0">
        //     <request accId="508443" pin="bses@56">
        //         <fromAddress>BSESRP</fromAddress>
        //         <recipientList>
        //             <destAddress>' . $mobile . '</destAddress>
        //         </recipientList>
        //         <message>
        //             <messageTxt>' . $otp . ' is your one time password (OTP) for Need Validation. Please enter the OTP to proceed. Team BRPL</messageTxt>
        //         </message>
        //     </request>
        // </a2wml>',
        
        // CURLOPT_HTTPHEADER => array(
        //     'Content-Type: application/soap+xml;'
        // ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        //     // Check if the API request was successful
        //     if (!$response) {
        //         return response()->json([
        //             'message' => 'Failed to send OTP via SMS'
        //         ], 500);
        //     }

          // Authenticate and get the token from Auth endpoint
          $email = $request->input('login_field');
          $password = $request->input('password');
        
          $authData = [
              'username' =>'BRPL@SMS',
              'password' => 'SMS@12345',
          ];
         
              $authCurl = curl_init();
             curl_setopt_array($authCurl, array(
              CURLOPT_URL => 'https://bsesbrpl.co.in:7880/SMS_Hosting/Auth',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json'
              ),
              CURLOPT_POSTFIELDS => json_encode($authData),
          ));
      
          $authResponse = curl_exec($authCurl);
          curl_close($authCurl);
          if (!$authResponse) {
              return response()->json([
                  'message' => 'Failed to authenticate'
              ], 500);
          }
      
          // Decode the authentication response
          $authResponseData = json_decode($authResponse, true);
      
          if (!isset($authResponseData['token'])) {
              return response()->json([
                  'message' => 'Invalid authentication response'
              ], 500);
          }
      
          $bearerToken = $authResponseData['token'];
      
          // Now, send the OTP using the Send endpoint
          $smsPayload = [
              'mobileNumber' => $user->mobile_number,  // Assuming mobile number is stored in user model
              'applicationName' => 'NV',
              'message' =>  "$otp is your one time password (OTP) for Need Validation. Please enter the OTP to proceed. Team BRPL",
              'senderId' => 'BSESRP',
              'smsType' => 'NV OTP'
          ];
      
          $sendCurl = curl_init();
          curl_setopt_array($sendCurl, array(
              CURLOPT_URL => 'https://bsesbrpl.co.in:7880/SMS_Hosting/Send',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json',
                  'Authorization: Bearer ' . $bearerToken
              ),
              CURLOPT_POSTFIELDS => json_encode($smsPayload),
          ));
      
          $sendResponse = curl_exec($sendCurl);
          $sendError = curl_error($sendCurl); 
          curl_close($sendCurl);
      
          if (!$sendResponse || $sendError) {
              Log::error('Failed to send OTP via SMS', ['error' => $sendError, 'response' => $sendResponse]);
              return response()->json(['message' => 'Failed to send OTP via SMS'], 500);
          }
          Log::info('OTP sent successfully', ['otp' => $otp, 'mobile_number' => $user->mobile_number]);

         return view('admin.otp_verify')->with(['otp_required' => true, 'otp' => $otp, 'otp_expiration' => $otpExpiration]);
    }
}
    public function regenerateOTP(Request $request)
{
    $user = Auth::user();
    $role_id = $user->role_id;
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated'
        ], 401);
    }

    // Generate a new OTP
    $otp = rand(100000, 999999);
    // Calculate OTP expiration time (3 minutes from now)
    $otpExpiration = now()->addMinutes(3);

    // Update or create the OTP record
    $otpRecord = DB::table('otps')
        ->where('user_id', $user->id)
        ->updateOrInsert(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expiry' => $otpExpiration,
                'used_at' => null // set used_at to null on update
            ]
        );
   
        
    // // Send the new OTP via SOAP
    // $app_name = "NEEDVALIDATION";
    // $encrypt_key ="!!B\$E\$@@*SMS";
    // $companycode = "BRPL";
    // $vendor_code = "REDIAN";
    // $mobile = $user->mobile_number; // Assuming you have the user's phone number in the user model
    // $sms_type = "OTP"; // Updated SMS type
    // $EmpCode = "895623";
    // // dd($app_name,$encrypt_key,$companycode,$vendor_code,$mobile,$sms_type,$EmpCode);
    // $curl = curl_init();
    // curl_setopt_array($curl, array(
    // CURLOPT_URL => 'https://japi.instaalerts.zone/failsafe/HttpData_SS',
    // CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_ENCODING => '',
    // CURLOPT_MAXREDIRS => 10,
    // CURLOPT_TIMEOUT => 0,
    // CURLOPT_FOLLOWLOCATION => true,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // CURLOPT_CUSTOMREQUEST => 'POST',
    // CURLOPT_POSTFIELDS =>'<a2wml version="2.0">
    //         <request accId="508443" pin="bses@56">
    //             <fromAddress>BSESRP</fromAddress>
    //             <recipientList>
    //                 <destAddress>' . $mobile . '</destAddress>
    //             </recipientList>
    //             <message>
    //                 <messageTxt>' . $otp . ' is your one time password (OTP) for Need Validation. Please enter the OTP to proceed. Team BRPL</messageTxt>
    //             </message>
    //         </request>
    //     </a2wml>',
    // CURLOPT_HTTPHEADER => array(
    //     'Content-Type: application/soap+xml;'
    // ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);

    // // Check if the SOAP request was successful
    // if (!$response) {
    //     return response()->json([
    //         'message' => 'Failed to send OTP via SMS'
    //     ], 500);
    // }

     // Authenticate and get the token from Auth endpoint
     $email = $request->input('login_field');
     $password = $request->input('password');
   
     $authData = [
         'username' =>'BRPL@SMS',
         'password' => 'SMS@12345',
     ];
    
         $authCurl = curl_init();
        curl_setopt_array($authCurl, array(
         CURLOPT_URL => 'https://bsesbrpl.co.in:7880/SMS_Hosting/Auth',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER => array(
             'Content-Type: application/json'
         ),
         CURLOPT_POSTFIELDS => json_encode($authData),
     ));
 
     $authResponse = curl_exec($authCurl);
     curl_close($authCurl);
     if (!$authResponse) {
         return response()->json([
             'message' => 'Failed to authenticate'
         ], 500);
     }
 
     // Decode the authentication response
     $authResponseData = json_decode($authResponse, true);
 
     if (!isset($authResponseData['token'])) {
         return response()->json([
             'message' => 'Invalid authentication response'
         ], 500);
     }
 
     $bearerToken = $authResponseData['token'];
 
     // Now, send the OTP using the Send endpoint
     $smsPayload = [
         'mobileNumber' => $user->mobile_number,  // Assuming mobile number is stored in user model
         'applicationName' => 'NV',
         'message' =>  "$otp is your one time password (OTP) for Need Validation. Please enter the OTP to proceed. Team BRPL",
         'senderId' => 'BSESRP',
         'smsType' => 'NV OTP'
     ];
 
     $sendCurl = curl_init();
     curl_setopt_array($sendCurl, array(
         CURLOPT_URL => 'https://bsesbrpl.co.in:7880/SMS_Hosting/Send',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER => array(
             'Content-Type: application/json',
             'Authorization: Bearer ' . $bearerToken
         ),
         CURLOPT_POSTFIELDS => json_encode($smsPayload),
     ));
 
     $sendResponse = curl_exec($sendCurl);
     $sendError = curl_error($sendCurl); 
     curl_close($sendCurl);
 
     if (!$sendResponse || $sendError) {
         Log::error('Failed to send OTP via SMS', ['error' => $sendError, 'response' => $sendResponse]);
         return response()->json(['message' => 'Failed to send OTP via SMS'], 500);
     }
     Log::info('OTP sent successfully', ['otp' => $otp, 'mobile_number' => $user->mobile_number]);

    // Redirect back to the same page after regenerating OTP
    return redirect()->back()->with('success', 'OTP regenerated and sent via SMS');
}



    public function verifyOTP(Request $request)
    {
        /**
         * $query->where('email', $request->email)->orWhere('phone', $request->phone)->where('user_id', $user->id);
         */
        //  dd($request);
        $user = Auth::user();
        $role_id = $user->role_id;
        if ($role_id == '1')
        {
            $otpRecord = OTP::where('otp', $request->otp)
            ->where(fn ($query) => $query->where('user_id', auth()->user()->id))
            ->whereNull('used_at')
            ->first();
           
        }
        else
        {
            $otpRecord = OTP::where('otp', $request->otp)
            ->where(fn ($query) => $query->where('user_id', auth()->user()->id))
            ->whereNull('used_at')
            ->first();
        }
       

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        if ($otpRecord->expiry->isBefore(now())) {
            return back()->withErrors(['otp' => 'OTP has expired']);
        }

        if ($otpRecord->used_at !== null) {
            return back()->withErrors(['otp' => 'OTP already been used']);
        }

        $otpRecord->used_at = now();
        $otpRecord->save();

        if (!session()->has('otp_verified')) {
            session(['otp_verified' => []]);
        }

        $session = session()->get('otp_verified');
    
        $AuthRoutes =  config('app.otp_verified_routes', []);
        $previousUrl = URL::previous();
        parse_str(parse_url($previousUrl, PHP_URL_QUERY), $queryParams);
        $routes = $queryParams['route'] ?? null;
        $requiresOtpVerification = in_array($routes, $AuthRoutes);
        
        if ($requiresOtpVerification) {
            $session[$routes] = true;
            session()->put('otp_verified', $session);
            return redirect("/{$routes}");
        } else {
            $session['login'] = true;
            session()->put('otp_verified', $session);
            session()->flash('welcome_message');
            
            return redirect("/admin/dashboard");
        }
    }
    public function viewOTP(Request $request)
    {
        $user = Auth::user();
        $role_id = $user->role_id;
        $data = DB::table('manage_otp')->first();
        if($data->otp_status == 2)
        {
            if ($role_id != '1'){
                $user->increment('login_count');
            }
        $session['login'] = true;
        session()->put('otp_verified', $session);
        session()->flash('welcome_message');

        return redirect("/admin/dashboard");
        }
        else
        {
            if ($role_id != '1'){
                $user->increment('login_count');
            }
            return view('admin.otp_verify');
        }
    }

    public function otp_list(Request $request)
    {
        $data = DB::table('manage_otp')->where('id',1)->select('otp_status')->first();
        //  dd($data);
        return view('admin.otp.edit',compact('data'));
    }

    // public function otp_edit(Request $request, $id, $user_id)
    // {
    //     // dd($id);
    //     $data = OTP::where('user_id',$user_id)->first();
    //     // dd($data->user_id);
    //     return view('admin.otp.edit',compact('data'));

    // }

    public function otp_update(Request $request)
    {
        $user_id = \Auth::user()->id;
     
            $old_values = DB::table('manage_otp')->where('id',1)->get();
            $newValues = $request->except(['_token', 'id']);
            $new_values = json_encode($newValues);
       
        DB::table('manage_otp')->where('id',1)->update([
            'otp_status' => $request->otp_status,
        ]);
        OTPLog::create([
            'user_id'=>$user_id,
            'action' => 'edit',
            'old_values' => $old_values,
            'new_values' => $new_values,
        ]);
        $response['result'] = 'success';
        $response['msg'] = 'OTP Updated';
        return response()->json($response);
    }
    public function Logs(Request $request){
        $user_id = \Auth::user()->id;
       $otpLogs = OTPLog::with('user')->where('user_id',$user_id)->paginate(10);
       return view('admin.otp.log_otp',['otpLogs'=>$otpLogs]);
    } 

    public function export_logexcel(Request $request)
    {
       $user_id = \Auth::user()->id;
        $otplog =  OTPLog::where('user_id',$user_id)->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr>
        <th>S.No</th>
        <th>Event</th>
        <th>Audited By</th>
        <th >Audited At</th>
        <th></th>
        <th>OTP Status</th></tr>';
        
        foreach ($otplog as $key => $opex) {
           $newvalue = json_decode($opex->new_values, true);
           $oldvalue = json_decode($opex->old_values, true);
           $event = ucfirst($opex->action);
           $date = $opex->created_at->format('d-M-Y h:i:s A');
           $i = $key + 1;
           
           $output .= '<tr>';
           $output .= '<td rowspan="2">' . $i . '</td>';
           if($event == 'Edit'){
            $output .= '<td rowspan="2">' . 'Updated' . '</td>'; 
           }
           $output .= '<td rowspan="2">' . $opex->user->name . '</td>'; 
           $output .= '<td rowspan="2">' . $date . '</td>';
           $output .= '<td><b>' . 'Old Value' . '</b></td>';

           foreach ($oldvalue as $old){
            if(is_array($old) && array_key_exists('otp_status', $old)){
                if($old['otp_status'] =='1'){
                    $output .= '<td>' . 'Enable' . '</td>'; 
                }else{
                    $output .= '<td>' . 'Disable' . '</td>'; 
                }
                   
               }
           }
          
           $output .= '</tr>';

           $output .= '<tr>';
           $output .= '<td><b>' . 'New Value' . '</b></td>';
           if($newvalue['otp_status'] =='1'){
            $output .= '<td>' . 'Enable' . '</td>'; 
            }else{
                $output .= '<td>' . 'Disable' . '</td>'; 
            }
           $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=LogOTP_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
}