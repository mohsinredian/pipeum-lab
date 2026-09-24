<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;
use App\Models\ApiLog;
use App\Models\Subsidy;
use App\Models\ConfirmationSubsidyOptin;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CustomHelper;
use App\Models\IssueAsset;
use App\Models\InventorySerialNumberMapping;
use App\Models\ItemIssue;
use Illuminate\Support\Carbon;
use JWT;
use Illuminate\Support\Facades\Log;


class ApiController extends Controller
{

    // Login
    public function login(Request $request)
    {
        $email = $request->email;
        $pass = $request->password;

        if(empty($email) || empty($pass))
        {
            $response['status'] = 400;
            $response['message'] = 'Email and Password both are required!';  
            $response['data'] = '';  
            //$response['token'] = '';  
        }
        else
        {
            $data = User::where('email',$request->email)->first();
            if(empty($data))
            {
                $response['status'] = 404;
                $response['message'] = 'Email not found!';   
                $response['data'] = '';  
                //$response['token'] = '';    
            }
            else
            {
                if(Auth::attempt(['email' => $email, 'password' => $pass]))
                { 
                    //$token =  $data->createToken('MyApp')->plainTextToken; 
                    $response['status'] = 200;
                    //$response['token'] = $token;
                    $response['data'] = $data;
                    $response['message'] = 'User login successfully!';
                } 
                else
                { 
                    $response['status'] = 404;
                    //$response['token'] = '';
                    $response['data'] = '';
                    $response['message'] = 'Unauthorized user!';
                } 
            }
        }
        return response()->json($response);
    }


    // forgot Password
    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        if(empty($email))
        {
            $response['status'] = 400;
            $response['message'] = 'Email is required!';     
            $response['data'] = '';  
            //$response['token'] = '';  
        }
        else
        {
            $user = User::where(array('email'=>$email))->first();
            if(empty($user))
            {
                $response['status'] = 404;
                $response['message'] = 'Email not found!';    
                $response['data'] = '';    
                //$response['token'] = '';  
            }
            else
            {
                //$token =  $user->createToken('MyApp')->plainTextToken; 
                $details['name'] = $user->name;
                $details['email'] = $user->email;
                $mail = Mail::to('sonali.k@rediansoftware.com')->send(new ForgotPassword($details));
                $response['status'] = 200;
                $response['message'] = 'Mail sent successfully!';    
                $response['data'] = $details;    
                //$response['token'] = $token;  
            }
        }  
        return response()->json($response);
    }

    //Fetch form details
    public function fetchFormDetails(Request $request)
    {
        $reference_id = $request->reference_id;
        if(empty($reference_id))
        {
            $response['status'] = 400;
            $response['message'] = 'Reference Number is required!';     
            $response['data'] = '';  
        }
        else
        {
            $form = InventorySerialNumberMapping::where(array('reference_id'=>$reference_id))->first();
            $issue = ItemIssue::get();

            if(!empty($form)){
            foreach($issue as $val){
                $serial_no = $val->serial_number;
                $serial_no1 = explode(',',$serial_no);
                if(in_array($form->serial_number, $serial_no1)){
                    $issue = ItemIssue::where('serial_number',$serial_no)->first();
                }
            }
        }
            
            if(empty($form))
            {
                $response['status'] = 404;
                $response['message'] = 'Reference Number not found!';    
                $response['data'] = '';    
            }
            else
            {
                $data = array();
                $data['reference_no'] = $form->reference_id;
                $data['serial_no'] = $form->serial_number;
                $data['item_type'] = @$issue->asset->name;
                $data['item_qty'] = $issue->item_qty;
                $data['brand_name'] = @$issue->brand->name;
                $data['asset_type'] = @$issue->asset_type;
                $data['circle_name'] = @$issue->circle->name;
                $data['division_name'] = @$issue->division->name;
                $data['location_name'] = @$issue->location->name;
                $data['issued_to'] = $issue->issued_to;
                $data['issued_to_email'] = $issue->issued_to_email;
                $data['issue_reason'] = $issue->issue_reason;
                $data['date_of_issue'] = Carbon::createFromFormat('Y-m-d H:i:s', $issue->created_at)->format('Y-m-d');
                if(!empty($issue->id))
                {
                    $response['status'] = 200;
                    $response['message'] = 'Form data fetched successfully!';    
                    $response['data'] = $data;    
                }
                else
                {
                    $response['status'] = 409;
                    $response['message'] = 'Form data not found!';    
                    $response['data'] = $data; 
                }
            }
        }
        return response()->json($response);
    }

    //Reset 
    public function resetFormDetails(Request $request)
    {
        $ca_no = $request->ca_no;
        if(empty($ca_no))
        {
            $response['status'] = 400;
            $response['message'] = 'CA Number is required!';     
            $response['data'] = '';  
        }
        else
        {
            $form = Subsidy::where(array('ca_no'=>$ca_no))->first();
            if(empty($form))
            {
                $response['status'] = 404;
                $response['message'] = 'CA Number not found!';    
                $response['data'] = '';    
            }
            else
            {
                $form->deleted = '1';
                $form->save();
                $response['status'] = 200;
                $response['message'] = 'Form reset successfully!';    
                $response['data'] = $form;    
            }
        }
        return response()->json($response);
    }
    
}