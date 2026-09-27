<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin;
use App\Models\User;
use Config;
use DB;
use Exception;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfileImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        // dd($request->hasFile('image'));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profileimages/'), $imageName);

            // Get the authenticated user
            $user = Auth::user();

            // Update the user's profile_img column with the image path
            $user->profile_img = 'profileimages/' . $imageName; // Adjust the path as needed

            // Save the updated user record
            $user->save();

            return response()->json(['message' => 'Image uploaded successfully', 'image' => $user->profile_img]);
        }

        return response()->json(['message' => 'No image found in the request'], 400);
    }



  public function changePassword(Request $request)
{
    // dd($request->all());
    $request->validate([
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password',
    ]);
 
    
    $user = Auth::user();
 
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Current password is incorrect'
        ]);
    }
 
    if ($request->new_password != $request->confirm_password) {
        return response()->json([
            'status' => false,
            'message' => 'Passwords do not match'
        ]);
    }
 
    $email = $request->user_email;
 
    if (!$email) {
        return response()->json([
            'status' => false,
            'message' => 'Session expired'
        ]);
    }
 
    
    $userUpdated = \DB::table('users')
        ->where('email', $email)
        ->update([
            'password' => Hash::make($request->new_password),
            'last_password_change_date' => now(),
            'updated_at' => now(),
        ]);
 
   
    $employeeUpdated = \DB::table('employee')
        ->where('email', $email)
        ->update([
            'password' => $request->new_password,
            
        ]);
 
    if ($userUpdated == 0 && $employeeUpdated == 0) {
        return response()->json([
            'status' => false,
            'message' => 'Email not found in both tables'
        ]);
    }
 
    Session::forget(['password_email']);
 
    return response()->json([
        'status' => true,
        'message' => 'Password updated successfully'
    ]);
}
}
