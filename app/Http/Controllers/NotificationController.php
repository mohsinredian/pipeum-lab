<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Employee;
use App\Models\Workflow;
use App\Models\Nvsericestatus;
use App\Models\Department;
use App\Models\NeedValidation;
use Auth;
use DB;

class NotificationController extends Controller
{
    //
    public function NotificationList(Request $request)
    {
        $user=Auth::user();
        if($user->role_id == 1){
            $data = DB::table('needvalidations')
            ->join('employee','needvalidations.user_id','=','employee.user_id')
            ->join('nvservicestatus','needvalidations.id','=','nvservicestatus.nv_id')
            ->where('nvservicestatus.ceo_status',1);
            $fdata = $data->get();
            $count = $data->count();
          return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
        }else{
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();

            foreach ($departments as $department) {
                $hod = $department->dep_hod;
                $rv1 = $department->dep_rew1;
                $rv2 = $department->dep_rew2;
                $rv3 = $department->dep_rew3;
                $rv4 = $department->dep_rew4;
                $group_cio = $department->group_cio;

                $departments_with_group_cio = [];
                $departments_without_group_cio = [];
                $all_departments = Department::where('status', 1)->get();
        
                foreach ($all_departments as $all_department) {
                    if (!empty($all_department->group_cio)) {
                        $departments_with_group_cio[] = $all_department->id;
                    } else {
                        $departments_without_group_cio[] = $all_department->id;
                    }
                }
                
                $id0 = Workflow::where("id", 1)->first();
                $id1 = Workflow::skip(1)->first();
                $id2 = Workflow::skip(2)->first();
                $id3 = Workflow::skip(3)->first();
                $id4 = Workflow::skip(4)->first();
                $id5 = Workflow::skip(5)->first();
                if($user->role_id == 11){
                $departmentIds = explode(',', $user->department_id);
                $data = DB::table('needvalidations')
                    ->join('employee','needvalidations.user_id','=','employee.user_id')
                    ->join('nvservicestatus','needvalidations.id','=','nvservicestatus.nv_id')
                    ->whereIn('employee.department_id',$departmentIds);
                    
                if(!empty($rv1) && $rv1 == $user->id){ 
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew1", $user->id)->pluck('id');
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where('draft', 1)->where('rv1_status', 0)->get();
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }

                    $nv = NeedValidation::with("division", "service")
                    ->whereIn('department_id', $departmentIds)
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc");
                    $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($rv2) && $rv2 == $user->id){
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew2", $user->id)->pluck('id');
                    $nv_id = [];
                       if(!empty($rv1)){
                        $nv_status = Nvsericestatus::where('rv1_status', 1)->where('rv2_status', 0)->get();
                        }else{
                        $nv_status = Nvsericestatus::where('draft', 1)->where('rv2_status', 0)->where('rv1_status','!=', 2)->get();  
                        }
                 
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }

                    $nv = NeedValidation::with("division", "service")
                    ->whereIn('department_id', $departmentIds)
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc");
                    $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($rv3) && $rv3 == $user->id){
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew3", $user->id)->pluck('id');
                    $nv_id = [];
                    if(!empty($rv2)){
                        $nv_status = Nvsericestatus::where('rv2_status', 1)->where('rv3_status', 0)->get();
                    }elseif(!empty($rv1)){
                        $nv_status = Nvsericestatus::where('rv1_status', 1)->where('rv3_status', 0)->where('rv2_status','!=', 2)->get();
                    }else{
                        $nv_status = Nvsericestatus::where('draft', 1)->where('rv3_status', 0)
                        ->where('rv1_status','!=', 2)
                        ->where('rv2_status','!=', 2)->get();
                    }
                  
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }

                    $nv = NeedValidation::with("division", "service")
                    ->whereIn('department_id', $departmentIds)
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc");
                    $fdata = $nv->get();
                    $count = $nv->count();
                   
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($rv4) && $rv4 == $user->id){
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew4", $user->id)->pluck('id');
                    $nv_id = [];
                    if(!empty($rv3)){
                        $nv_status = Nvsericestatus::where('rv3_status', 1)->where('rv4_status', 0)->get();
                    }elseif(!empty($rv2)){
                        $nv_status = Nvsericestatus::where('rv2_status', 1)->where('rv4_status', 0)->where('rv3_status','!=', 2)->get();
                    }elseif(!empty($rv1)){
                        $nv_status = Nvsericestatus::where('rv1_status', 1)->where('rv4_status', 0)
                        ->where('rv2_status','!=', 2)
                        ->where('rv3_status','!=', 2)->get();
                    }else{
                        $nv_status = Nvsericestatus::where('draft', 1)->where('rv4_status', 0)
                        ->where('rv1_status','!=', 2)
                        ->where('rv2_status','!=', 2)
                        ->where('rv3_status','!=', 2)->get();
                    }
                 
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }


                   
                    $nv = NeedValidation::with("division", "service")
                    ->whereIn('department_id', $departmentIds)
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc");
                    $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($hod) && $hod == $user->id){
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_hod", $user->id)->pluck('id');
                    $nv = array();
                    $nv_statuses = array();
                    $nv_id = [];
                    $id = [];
            
                   $depart = Department::whereIn('id', $departmentIds)->get();
            
                   foreach ($depart as $depart) {
                    $dep_rew4 = $depart->dep_rew4;
                    $dep_rew3 = $depart->dep_rew3;
                    $dep_rew2 = $depart->dep_rew2;
                    $dep_rew1 = $depart->dep_rew1;
                    $dep_hod = $depart->dep_hod;

                    if(!empty($dep_hod)){
                    if(!empty($dep_rew4)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv4_status', 1);
                               
                        })
                            ->where(function ($query) {
                                $query
                                    ->where('rv4_status', '!=', 2);
                            })
                            ->get();
                            if(count($nv_status) > 0) {
                                $nv_statuses[] = $nv_status;
                               
                                
                            }
                   
                  
                    }elseif(!empty($dep_rew3)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv3_status', 1);
                            
                        })
                            ->where(function ($query) {
                                $query
                                    ->where('rv3_status', '!=', 2);
                            })
                            ->get();
                            if(count($nv_status) > 0) {
                                $nv_statuses[] = $nv_status;
                                
                                
                            }
                    
                    }elseif(!empty($dep_rew2)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('rv2_status', 1);
                        
                    })
                        ->where(function ($query) {
                            $query
                                ->where('rv2_status', '!=', 2);
                        })
                        ->get();
                        if(count($nv_status) > 0) {
                            $nv_statuses[] = $nv_status;
                            
                            
                        }
                    
                
                    }elseif(!empty($dep_rew1)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv1_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('rv1_status', '!=', 2);
                            })
                            ->get();  
                        if(count($nv_status) > 0) {
                            $nv_statuses[] = $nv_status;
                            
                        }
                    
                    
                    }else{
                        $nv_status = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
                        ->join('department','department.id','=','needvalidations.department_id')
                        ->where('department.id',$depart->id)
                        ->where('nvservicestatus.draft',1)
                        ->get();
                    
                        if(count($nv_status) > 0) {
                            $nv_statuses[] = $nv_status;
                        }
                    
                    
                    }
                    }
             
                   }
                   if(!empty($nv_statuses)) {
                    foreach ($nv_statuses as $idx => $nv_status) {
                        foreach($nv_status as $data) {
                            array_push($nv_id, $data["nv_id"]);
                            array_push($id, $data["id"]);
                        }
                    }
                    $nv1 = Nvsericestatus::whereIn("nv_id", $nv_id)
                    ->where('draft', 1)
                    ->where('hod_status', 0)
                    ->where('nvservicestatus.rv1_status', '!=', 2)
                    ->where('nvservicestatus.rv2_status', '!=', 2)
                    ->where('nvservicestatus.rv3_status', '!=', 2)
                    ->where('nvservicestatus.rv4_status', '!=', 2)
                    ->pluck('nv_id');

                    $nv = NeedValidation::where('delete_draft',0)
                    ->whereIn('department_id', $departmentIds)->whereIn("id", $nv1)->orderBy("id", "desc")->get(); 
                    
                  }
                        $fdata = $nv;
                        $count = count($nv);
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
    
                }elseif(!empty($id0->work_rew1) && $id0->work_rew1 == $user->id){
                
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('derc_info', 1);
                    })->where(function ($query) {
                        $query->where('hod_status', 1);
                    })->where(function ($query) {
                        $query->where('ces_rew1_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew2_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew3_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew4_status', 0);
                    })->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        
                        $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($id0->work_rew2) && $id0->work_rew2 == $user->id){
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('derc_info', 1);
                    })->where(function ($query) {
                        $query->where('hod_status', 1);
                    })->where(function ($query) {
                        $query->where('ces_rew1_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew2_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew3_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew4_status', 0);
                    })->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        
                        $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id0->work_rew3) && $id0->work_rew3 == $user->id){
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('derc_info', 1);
                    })->where(function ($query) {
                        $query->where('hod_status', 1);
                    })->where(function ($query) {
                        $query->where('ces_rew1_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew2_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew3_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew4_status', 0);
                    })->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        
                        $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));

                }elseif(!empty($id0->work_rew4) && $id0->work_rew4 == $user->id){
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('derc_info', 1);
                    })->where(function ($query) {
                        $query->where('hod_status', 1);
                    })->where(function ($query) {
                        $query->where('ces_rew1_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew2_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew3_status', 0);
                    })->where(function ($query) {
                        $query->where('ces_rew4_status', 0);
                    })->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        
                        $fdata = $nv->get();
                    $count = $nv->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id0->approver) && $id0->approver == $user->id){
                    $nv_id = [];
                    if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->orWhere('ces_rew1_status', 1)
                            ->orWhere('ces_rew2_status', 1)
                            ->orWhere('ces_rew3_status', 1)
                            ->orWhere('ces_rew4_status', 1);
                    })
                        ->where(function ($query) {
                            $query->where('ces_rew1_status', '!=', 2)
                                ->where('ces_rew2_status', '!=', 2)
                                ->where('ces_rew3_status', '!=', 2)
                                ->where('ces_rew4_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 1);
                        })
                        ->where(function ($query) {
                            $query->where('ces_status', 0);
                        })
                        ->get();
                    }else{
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('derc_info', 1);
                        })->where(function ($query) {
                            $query->where('hod_status', 1);
                        })->where(function ($query) {
                            $query->where('ces_status', 0);
                        })->get();
                    }
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('ces_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('hod_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4_status', 0);
                        })
                        ->get();
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                            
                }elseif(!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) {
                        $nv_id = [];
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('ces_status', 1);
                        })
                            ->orWhere(function ($query) {
                                $query->where('hod_status', 1);
                            })
                            ->where(function ($query) {
                                $query->where('derc_info', 0);
                            })->where(function ($query) {
                                $query->where('work_rew1_status', 0);
                            })->where(function ($query) {
                                $query->where('work_rew2_status', 0);
                            })->where(function ($query) {
                                $query->where('work_rew3_status', 0);
                            })->where(function ($query) {
                                $query->where('work_rew4_status', 0);
                            })
                            ->get();
                        foreach ($nv_status as $nv_status) {
                            array_push($nv_id, $nv_status["nv_id"]);
                        }
                        
                        $nv = NeedValidation::with("division", "service")
                            ->whereIn("id", $nv_id)
                            ->orderBy("id", "desc");
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('ces_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('hod_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4_status', 0);
                        })
                        ->get();
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                 
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id1->work_rew4) && $id1->work_rew4 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('ces_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('hod_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4_status', 0);
                        })
                        ->get();
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                 
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id1->approver) && $id1->approver == $user->id) {
                        
                        $nv_id = [];
                        if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                            $nv_status = Nvsericestatus::where(function ($query) {
                            $query->orWhere('work_rew1_status', 1)
                                ->orWhere('work_rew2_status', 1)
                                ->orWhere('work_rew3_status', 1)
                                ->orWhere('work_rew4_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('work_rew1_status', '!=', 2)
                                    ->where('work_rew2_status', '!=', 2)
                                    ->where('work_rew3_status', '!=', 2)
                                    ->where('work_rew4_status', '!=', 2);
                            })
                            ->where(function ($query) {
                                $query->whereIn('derc_info', [0,1]);
                            })
                            ->where(function ($query) {
                                $query->where('approver_status', 0);
                            })
                            ->get();
                        }else{
                            $nv_status = Nvsericestatus::where(function ($query) {
                                $query->where('ces_status', 1);
                            })
                                ->orWhere(function ($query) {
                                    $query->where('hod_status', 1);
                                })
                                ->where(function ($query) {
                                    $query->where('derc_info', 0);
                                })->where(function ($query) {
                                    $query->where('approver_status', 0);
                                })
                                ->get();
                        }
                        foreach ($nv_status as $nv_status) {
                            array_push($nv_id, $nv_status["nv_id"]);
                        }
        
                      
                        $nv = NeedValidation::with("division", "service")
                            ->whereIn("id", $nv_id)
                            ->orderBy("id", "desc");
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) {
                    
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })->where(function ($query) {
                        $query->where('work_rew1dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep2_status', 0);
                    })
                        ->get();
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                   
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                    
                }elseif(!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) {
                    
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })->where(function ($query) {
                        $query->where('work_rew1dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep2_status', 0);
                    })
                        ->get();
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })->where(function ($query) {
                        $query->where('work_rew1dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep2_status', 0);
                    })
                        ->get();
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id2->work_rew4) && $id2->work_rew4 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })->where(function ($query) {
                        $query->where('work_rew1dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep2_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep2_status', 0);
                    })
                        ->get();
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id2->approver) && $id2->approver == $user->id) {
                        
                        $nv_id = [];
                        if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                            $nv_status = Nvsericestatus::where(function ($query) {
                            $query->orWhere('work_rew1dep2_status', 1)
                                ->orWhere('work_rew2dep2_status', 1)
                                ->orWhere('work_rew3dep2_status', 1)
                                ->orWhere('work_rew4dep2_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('work_rew1dep2_status', '!=', 2)
                                    ->where('work_rew2dep2_status', '!=', 2)
                                    ->where('work_rew3dep2_status', '!=', 2)
                                    ->where('work_rew4dep2_status', '!=', 2);
                            })
                            ->where(function ($query) {
                                $query->where('check_technology', 1);
                            })  ->where(function ($query) {
                                $query->where('approverdep2_status', 0);
                            })
                            ->get();
                        }else{
                            $nv_status = Nvsericestatus::where(function ($query) {
                                $query->where('check_technology', 1);
                            })->where(function ($query) {
                                $query->where('approver_status', 1);
                            })->where(function ($query) {
                                $query->where('approverdep2_status', 0);
                            })
                                ->get(); 
                        }
        
                        foreach ($nv_status as $nv_status) {
                            array_push($nv_id, $nv_status["nv_id"]);
                        }
        
                     
                        $nv = NeedValidation::with("division", "service")
                            ->whereIn("id", $nv_id)
                            ->orderBy("id", "desc");
        
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) {
                
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('approverdep2_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('approver_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4dep3_status', 0);
                        })
                    
                        ->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('approverdep2_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('approver_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4dep3_status', 0);
                        })
                    
                        ->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                   
                    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('approverdep2_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('approver_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4dep3_status', 0);
                        })
                    
                        ->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                  
                    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id3->work_rew4) && $id3->work_rew4 == $user->id) {
                    $nv_id = [];
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('approverdep2_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('approver_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 0);
                        })->where(function ($query) {
                            $query->where('work_rew1dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew2dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew3dep3_status', 0);
                        })->where(function ($query) {
                            $query->where('work_rew4dep3_status', 0);
                        })
                    
                        ->get();
                    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id3->approver) && $id3->approver == $user->id) {
                    
                    $nv_id = [];
                    if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                        $query->orWhere('work_rew1dep3_status', 1)
                            ->orWhere('work_rew2dep3_status', 1)
                            ->orWhere('work_rew3dep3_status', 1)
                            ->orWhere('work_rew4dep3_status', 1);
                    })
                        ->where(function ($query) {
                            $query->where('work_rew1dep3_status', '!=', 2)
                                ->where('work_rew2dep3_status', '!=', 2)
                                ->where('work_rew3dep3_status', '!=', 2)
                                ->where('work_rew4dep3_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->whereIn('check_technology', [0, 1]);
                        })
                        ->where(function ($query) {
                            $query->where('approverdep3_status', 0);
                        })
                        ->get();
                    }else{
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('approverdep2_status', 1);
                        })
                            ->orWhere(function ($query) {
                                $query->where('approver_status', 1);
                            })
                            ->where(function ($query) {
                                $query->where('check_technology', 0);
                            })->where(function ($query) {
                                $query->where('approverdep3_status', 0);
                            })
                        
                            ->get();
                    }
    
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
    
                   
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($group_cio) && $group_cio == $user->id) {
                    $departmentIds = Department::where("group_cio", $user->id)->pluck('id');
                        // $departmentIds = explode(',', $user->department_id);
                        $nv_id = [];
                        $nv_status = Nvsericestatus::where('approverdep3_status', 1)->get();
                        foreach ($nv_status as $nv_status) {
                            array_push($nv_id, $nv_status["nv_id"]);
                        }
                       
                        $nv = NeedValidation::with("division", "service")
                        ->whereIn('department_id', $departmentIds)
                            ->whereIn("id", $nv_id)
                            ->orderBy("id", "desc");
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                        
                }elseif(!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) {
                    $withGH = collect();
                    $withoutGH = collect();
                    
                    if (!empty($departments_with_group_cio)) {
                        $withGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_with_group_cio)
                            ->where('nvservicestatus.groupcio_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.groupcio_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    
                    if (!empty($departments_without_group_cio)) {
                        $withoutGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_without_group_cio)
                            ->where('nvservicestatus.approverdep3_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.approverdep3_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    $nv_sm_data_query = $withGH->union($withoutGH); 
                    $nv_sm_data_query = Nvsericestatus::query()->fromSub($nv_sm_data_query, 'combined');

                    $nv_id = $nv_sm_data_query->where(function ($query) {
                        $query->where('work_rew1dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep4_status', 0);
                    })->pluck('nv_id');
                    
                        $nv = NeedValidation::with("division", "service")
                            ->whereIn("id", $nv_id)
                            ->orderBy("id", "desc");
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                        
                }elseif(!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) {
                    $withGH = collect();
                    $withoutGH = collect();
                    
                    if (!empty($departments_with_group_cio)) {
                        $withGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_with_group_cio)
                            ->where('nvservicestatus.groupcio_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.groupcio_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    
                    if (!empty($departments_without_group_cio)) {
                        $withoutGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_without_group_cio)
                            ->where('nvservicestatus.approverdep3_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.approverdep3_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    $nv_sm_data_query = $withGH->union($withoutGH); 
                    $nv_sm_data_query = Nvsericestatus::query()->fromSub($nv_sm_data_query, 'combined');
                    
                    $nv_id = $nv_sm_data_query->where(function ($query) {
                        $query->where('work_rew1dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep4_status', 0);
                    })->pluck('nv_id');

                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) {
                    $withGH = collect();
                    $withoutGH = collect();
                    
                    if (!empty($departments_with_group_cio)) {
                        $withGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_with_group_cio)
                            ->where('nvservicestatus.groupcio_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.groupcio_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    
                    if (!empty($departments_without_group_cio)) {
                        $withoutGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_without_group_cio)
                            ->where('nvservicestatus.approverdep3_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.approverdep3_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    $nv_sm_data_query = $withGH->union($withoutGH); 
                    $nv_sm_data_query = Nvsericestatus::query()->fromSub($nv_sm_data_query, 'combined');
                    
                    $nv_id = $nv_sm_data_query->where(function ($query) {
                        $query->where('work_rew1dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep4_status', 0);
                    })->pluck('nv_id');

                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id4->work_rew4) && $id4->work_rew4 == $user->id) {
                    $withGH = collect();
                    $withoutGH = collect();
                    
                    if (!empty($departments_with_group_cio)) {
                        $withGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_with_group_cio)
                            ->where('nvservicestatus.groupcio_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.groupcio_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    
                    if (!empty($departments_without_group_cio)) {
                        $withoutGH = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                            ->join('department', 'department.id', '=', 'needvalidations.department_id')
                            ->whereIn('department.id', $departments_without_group_cio)
                            ->where('nvservicestatus.approverdep3_status', 1)
                            ->where(function ($query) {
                                $query->where('nvservicestatus.approverdep3_status', '!=', 2) ;
                            })
                            ->select('nvservicestatus.*');
                    }
                    $nv_sm_data_query = $withGH->union($withoutGH); 
                    $nv_sm_data_query = Nvsericestatus::query()->fromSub($nv_sm_data_query, 'combined');
                    
                    $nv_id = $nv_sm_data_query->where(function ($query) {
                        $query->where('work_rew1dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew2dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew3dep4_status', 0);
                    })->where(function ($query) {
                        $query->where('work_rew4dep4_status', 0);
                    })->pluck('nv_id');

                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");

                        $fdata = $nv->get();
                        $count = $nv->count();
                        return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }elseif(!empty($id4->approver) && $id4->approver == $user->id) {

                    if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){

                        $withGH = collect();
                        $withoutGH = collect();
                        
                        if (!empty($departments_with_group_cio)) {
                            $withGH = Nvsericestatus::with(['service', 'material', 'user'])
                                ->join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                                ->join('department', 'department.id', '=', 'needvalidations.department_id')
                                ->whereIn('department.id', $departments_with_group_cio)

                                ->where('nvservicestatus.groupcio_status', 1)
                            
                                ->where(function ($query) {
                                $query->orWhere('nvservicestatus.work_rew1dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew2dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew3dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew4dep4_status', 1);
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.work_rew1dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew2dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew3dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew4dep4_status', '!=', 2);
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.approverdep4_status', 0) ;
                                })
                                    ->select('nvservicestatus.*')
                                    ->orderBy('nvservicestatus.id', 'asc');
                        }
                        
                        if (!empty($departments_without_group_cio)) {
                            $withoutGH = Nvsericestatus::with(['service', 'material', 'user'])
                                ->join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                                ->join('department', 'department.id', '=', 'needvalidations.department_id')
                                ->whereIn('department.id', $departments_without_group_cio)

                                ->where('nvservicestatus.approverdep3_status', 1)
                            
                                ->where(function ($query) {
                                $query->orWhere('nvservicestatus.work_rew1dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew2dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew3dep4_status', 1)
                                    ->orWhere('nvservicestatus.work_rew4dep4_status', 1);
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.work_rew1dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew2dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew3dep4_status', '!=', 2)
                                        ->where('nvservicestatus.work_rew4dep4_status', '!=', 2);
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.approverdep4_status', 0) ;
                                })
                                ->select('nvservicestatus.*')
                                ->orderBy('nvservicestatus.id', 'asc');
                        }
                 
                    }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                 
                        $withGH = collect();
                        $withoutGH = collect();
                        
                        if (!empty($departments_with_group_cio)) {
                            $withGH = Nvsericestatus::with(['service', 'material', 'user'])
                                ->join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                                ->join('department', 'department.id', '=', 'needvalidations.department_id')
                                ->whereIn('department.id', $departments_with_group_cio)
                                ->where('nvservicestatus.groupcio_status', 1)
                            
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.groupcio_status', '!=', 2) ;
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.approverdep4_status', 0) ;
                                })
                                    ->select('nvservicestatus.*')
                                    ->orderBy('nvservicestatus.id', 'asc');
                        }
                        
                        if (!empty($departments_without_group_cio)) {
                            $withoutGH = Nvsericestatus::with(['service', 'material', 'user'])
                                ->join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                                ->join('department', 'department.id', '=', 'needvalidations.department_id')
                                ->whereIn('department.id', $departments_without_group_cio)
                                ->where('nvservicestatus.approverdep3_status', 1)
                            
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.approverdep3_status', '!=', 2) ;
                                })
                                ->where(function ($query) {
                                    $query->where('nvservicestatus.approverdep4_status', 0) ;
                                })
                                ->select('nvservicestatus.*')
                                ->orderBy('nvservicestatus.id', 'asc');
                        }
                    }
                        
                    $nv_sm_data_query = $withGH->union($withoutGH); 
                    $nv_sm_data_query = Nvsericestatus::query()->fromSub($nv_sm_data_query, 'combined');
                    
                    $nv_id = $nv_sm_data_query->pluck('nv_id');
                    
                            $nv = NeedValidation::with("division", "service")
                                ->whereIn("id", $nv_id)
                                ->orderBy("id", "desc");
                                $fdata = $nv->get();
                                $count = $nv->count();
                                return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                        
                        
                        
                }elseif (!empty($id5->approver) && $id5->approver == $user->id){
                    
                    $nv_id = [];
                        
                    $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                    foreach ($nv_status as $nv_status) {
                        array_push($nv_id, $nv_status["nv_id"]);
                    }
                    
                    $nv = NeedValidation::with("division", "service")
                        ->whereIn("id", $nv_id)
                        ->orderBy("id", "desc");
                        
                            $fdata = $nv->get();
                            $count = $nv->count();
                            return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
                }
                  
                }elseif($user->role_id == 9){
                
                    $data = DB::table('needvalidations')
                        ->join('employee','needvalidations.user_id','=','employee.user_id')
                        ->join('nvservicestatus','needvalidations.id','=','nvservicestatus.nv_id')
                        ->where('employee.department_id',$employees->department_id)
                        ->where('needvalidations.user_id',$user->id)
                        ->where('nvservicestatus.ceo_status',1);
                        $fdata = $data->get();
                        $count = $data->count();
                    return response()->json(array('fcount'=>$count,'fdata'=>$fdata));
        
                }
           }

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
