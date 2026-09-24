<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Auth;
use App\Models\Employee;
use App\Models\Department;
class NotificationController extends Controller
{
    //
    public function NotificationList(Request $request)
    {
        $user=Auth::user();
        // normal user 
        if($user->role_id == 9){
         $Employee =  Employee::where('user_id',$user->id)->first();
            $data = DB::table('needvalidations')
            ->join('nvservicestatus','needvalidations.id','=','nvservicestatus.nv_id');
            
        $departments=Department::get();
        foreach($departments as $department){
            if($Employee->department_id==$department->id){
                $department_id=$department->id;
                break;
            }
        }
            if($Employee->department_id ==  $department_id){
                if($rev == 'rev1'){
                    $data = $data->where('rv1id',$uid)->where('status.rv1satatus',NULL);
                }else if($rev == 'rev2'){
                    $data = $data->where('rv1id',$uid)->where('status.rv1satatus','approve')->andWhere('status.rv2satatus',NULL);
                }else  if($rev == 'rev3'){
                    $data = $data->where('rv1id',$uid)->where('status.rv1satatus','approve')->andWhere('status.rv2satatus','approve')
                    ->andWhere('status.rv3satatus',NULL);
                }
                
            }else if($dep == 'd2'){
                $data = $data->where('status.distatus','approved')->get();
            }
        
        
        
            $fdata = $data->get();
            $fcount = $data->count();
        
        }
        //user for approver
        elseif($user->role_id == 11){

        }
    }
    public function delete_notification(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $Notification = Notification::findOrFail($id);
                
                $Notification->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Notification Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Notification';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
}
