<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\Task;
use App\Models\Nvsericestatus;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Location;
use App\Models\Service;
use App\Models\NVMaterial;
use App\Models\MaterialDoc;
use App\Models\Workflow;
use App\Models\NVService;
use App\Models\ServiceDoc;
use App\Models\NeedValidation;
use Silber\Bouncer\Database\Role;
use Config;
use App\Models\FloorPlan;
use Illuminate\Support\Facades\DB;
use Exception;
use Hash;
use DateTime;
use DatePeriod;
use DateInterval;
use PDF;

class NVController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put("active", "needvalidation");

            return $next($request);
        });
    }

    public function NV_list(Request $request)
    {


        $user = \Auth()->user();
        $user_id = [];
        if ($user->role_id == 1) {
            $nv = NeedValidation::with('division', 'service')
                ->orderBy('id', 'desc')
                ->get();
            $totalId = NeedValidation::pluck('id');


            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();

            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
            $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
            $rejectedNV = $latestData->filter(function ($data) {
                return in_array($data->ceo_status, [2]) ||
                $data->rv1_status == 2 ||
                $data->rv2_status == 2 ||
                $data->rv3_status == 2 ||
                $data->rv4_status == 2 ||
                $data->hod_status == 2 ||
                $data->ces_rew1_status == 2 ||
                $data->ces_rew2_status == 2 ||
                $data->ces_rew3_status == 2 ||
                $data->ces_rew4_status == 2 ||
                $data->ces_status == 2 ||
                $data->work_rew1_status == 2 ||
                $data->work_rew2_status == 2 ||
                $data->work_rew3_status == 2 ||
                $data->work_rew4_status == 2 ||
                $data->cpmg_status == 2 ||
                $data->work_rew1dep2_status == 2 ||
                $data->work_rew2dep2_status == 2 ||
                $data->work_rew3dep2_status == 2 ||
                $data->work_rew4dep2_status == 2 ||
                $data->cto_status == 2 ||
                $data->work_rew1dep3_status == 2 ||
                $data->work_rew2dep3_status == 2 ||
                $data->work_rew3dep3_status == 2 ||
                $data->work_rew4dep3_status == 2 ||
                $data->ceo_nominee_status == 2 ||
                $data->work_rew1dep4_status == 2 ||
                $data->work_rew2dep4_status == 2 ||
                $data->work_rew3dep4_status == 2 ||
                $data->work_rew4dep4_status == 2 ||
                $data->ceo_nominee2_status == 2 ||
                $data->groupcio_status == 2;
            })->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [0])
                    && in_array($data->rv1_status, [0,1])
                    && in_array($data->rv2_status, [0,1])
                    && in_array($data->rv3_status, [0,1])
                    && in_array($data->rv4_status, [0,1])
                    && in_array($data->hod_status, [0, 1])
                    && in_array($data->ces_rew1_status, [0,1])
                    && in_array($data->ces_rew2_status, [0,1])
                    && in_array($data->ces_rew3_status, [0,1])
                    && in_array($data->ces_rew4_status, [0,1])
                    && in_array($data->cpmg_status, [0, 1])
                    && in_array($data->work_rew1_status, [0,1])
                    && in_array($data->work_rew2_status, [0,1])
                    && in_array($data->work_rew3_status, [0,1])
                    && in_array($data->work_rew4_status, [0,1])
                    && in_array($data->ces_status, [0, 1])
                    && in_array($data->work_rew1dep2_status, [0,1])
                    && in_array($data->work_rew2dep2_status, [0,1])
                    && in_array($data->work_rew3dep2_status, [0,1])
                    && in_array($data->work_rew4dep2_status, [0,1])
                    && in_array($data->cto_status, [0, 1])
                    && in_array($data->work_rew1dep3_status, [0,1])
                    && in_array($data->work_rew2dep3_status, [0,1])
                    && in_array($data->work_rew3dep3_status, [0,1])
                    && in_array($data->work_rew4dep3_status, [0,1])
                    && in_array($data->ceo_nominee_status, [0, 1])
                    && in_array($data->work_rew1dep4_status, [0,1])
                    && in_array($data->work_rew2dep4_status, [0,1])
                    && in_array($data->work_rew3dep4_status, [0,1])
                    && in_array($data->work_rew4dep4_status, [0,1])
                    && in_array($data->ceo_nominee2_status, [0, 1])
                    && in_array($data->groupcio_status, [0, 1]) ;
                })->count();
        } else {
            $employees = Employee::where("user_id", $user->id)->first();
            $employeesss = Employee::where("department_id", $employees->department_id)->get();
            foreach ($employeesss as $employee) {
                array_push($user_id, $employee["user_id"]);
            }
            // $dep = Department::where("id", $employees->department_id)->first();
            // $group_cio = $dep->group_cio;
            
            $id0 = Workflow::where("id", 1)->first();
            $id1 = Workflow::skip(1)->first();
            $id2 = Workflow::skip(2)->first();
            $id3 = Workflow::skip(3)->first();
            $id4 = Workflow::skip(4)->first();
            $id5 = Workflow::skip(5)->first();
                $departmentIds = explode(',', $employees->department_id);
                $departments = Department::whereIn("id", $departmentIds)->get();

                foreach ($departments as $department) {
                    $hod = $department->dep_hod;
                    $rv1 = $department->dep_rew1;
                    $rv2 = $department->dep_rew2;
                    $rv3 = $department->dep_rew3;
                    $rv4 = $department->dep_rew4;
                    $group_cio = $department->group_cio;
                }
               
            if (!empty($rv1) && $rv1 == $user->id) {
                $departmentIds = explode(',', $user->department_id);
                $nv_id = [];
                $nv_status = Nvsericestatus::where('draft',1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }


                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                ->whereIn('department_id', $departmentIds)
                ->whereIn("id", $nv_id)
                ->Orwhere("user_id", $user_id)
                ->orderBy("id", "desc")
                
                ->get();

                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');


                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->get();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $approvedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("ceo_status", 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv1_status", 2)->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [0]);  

                })->count();
            } elseif (!empty($rv2) && $rv2 == $user->id) {
                $departmentIds = explode(',', $user->department_id);
                $nv_id = [];

                if(!empty($rv1)){
                $nv_status = Nvsericestatus::where('rv1_status', 1)->get();
                }else{
                $nv_status = Nvsericestatus::where('draft', 1)->get();  
                }
         
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }


                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                ->whereIn('department_id', $departmentIds)
                ->whereIn("id", $nv_id)
                ->Orwhere("user_id", $user_id)
                ->orderBy("id", "desc")
                ->get();

                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');


                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->count();
                $approvedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("ceo_status", 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv2_status", 2)->count();

                if(!empty($rv1)){
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [0]);
                })->count();
                }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->draft, [1]) &&
                            in_array($data->rv2_status, [0]);
                    })->count();
                }
            } elseif (!empty($rv3) && $rv3 == $user->id) {
                $departmentIds = explode(',', $user->department_id);


                $nv_id = [];
                if(!empty($rv1)){
                    $nv_status = Nvsericestatus::where('rv1_status', 1)->get();
                }elseif(!empty($rv2)){
                    $nv_status = Nvsericestatus::where('rv2_status', 1)->get();
                }elseif(!empty($rv1)||!empty($rv2)){
                    $nv_status = Nvsericestatus::where('rv2_status', 1)->get();
                }else{
                    $nv_status = Nvsericestatus::where('draft', 1)->get();
                }
              
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }


                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                ->whereIn('department_id', $departmentIds)
                ->whereIn("id", $nv_id)
                ->Orwhere("user_id", $user_id)
                ->orderBy("id", "desc")
                
                ->get();

                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');


                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->count();
                $approvedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("ceo_status", 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv3_status", 2)->count();
              
                if(!empty($rv1)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv1_status, [1]) &&
                            in_array($data->rv3_status, [0]);
                    })->count();
                }elseif(!empty($rv2)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->rv2_status, [1]) &&
                            in_array($data->rv3_status, [0]);
                    })->count();
                }elseif(!empty($rv1)||!empty($rv2)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv1_status, [1]) &&
                            in_array($data->rv2_status, [1]) &&
                            in_array($data->rv3_status, [0]);
                    })->count();
                }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->draft, [1]) &&
                            in_array($data->rv3_status, [0]);
                    })->count();
                }
            } elseif (!empty($rv4) && $rv4 == $user->id) {
                $departmentIds = explode(',', $user->department_id);

                $nv_id = [];
                if(!empty($rv1)){
                    $nv_status = Nvsericestatus::where('rv1_status', 1)->get();
                }elseif(!empty($rv2)){
                    $nv_status = Nvsericestatus::where('rv2_status', 1)->get();
                }elseif(!empty($rv3)){
                    $nv_status = Nvsericestatus::where('rv3_status', 1)->get();
                }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)){
                    $nv_status = Nvsericestatus::where('rv3_status', 1)->get();
                }else{
                    $nv_status = Nvsericestatus::where('draft', 1)->get();
                }
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }


                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                ->whereIn('department_id', $departmentIds)
                ->whereIn("id", $nv_id)
                ->Orwhere("user_id", $user_id)
                ->orderBy("id", "desc")
                ->get();

                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');


                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->count();
                $approvedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("ceo_status", 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv4_status", 2)->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [1]) &&
                        in_array($data->rv3_status, [1]) &&
                        in_array($data->rv4_status, [0]);
                })->count();
                if(!empty($rv1)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv1_status, [1]) &&
                            in_array($data->rv4_status, [0]);
                    })->count();
                }elseif(!empty($rv2)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv2_status, [1]) &&
                            in_array($data->rv4_status, [0]);
                    })->count();
                }elseif(!empty($rv3)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv3_status, [1]) &&
                            in_array($data->rv4_status, [0]);
                    })->count();
                }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->rv1_status, [1]) &&
                            in_array($data->rv2_status, [1]) &&
                            in_array($data->rv3_status, [1]) &&
                            in_array($data->rv4_status, [0]);
                    })->count();
                }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return  in_array($data->draft, [1]) &&
                            in_array($data->rv4_status, [0]);
                    })->count();
                }
            } elseif (!empty($hod) && $hod == $user->id) {
                $departmentIds = explode(',', $user->department_id);
               
                $nv_id = [];
               

                    if(!empty($rv1)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv1_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('rv1_status', '!=', 2);
                            })
                            ->get();   
                    }elseif(!empty($rv1)||!empty($rv2)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv2_status', 1)
                                ->orWhere('rv1_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('rv1_status', '!=', 2)
                                    ->where('rv2_status', '!=', 2);
                            })
                            ->get();
                    }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv3_status', 1)
                                ->orWhere('rv1_status', 1)
                                ->orWhere('rv2_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('rv1_status', '!=', 2)
                                    ->where('rv2_status', '!=', 2)
                                    ->where('rv3_status', '!=', 2);
                            })
                            ->get();
                  
                    }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)||!empty($rv4)){
                        $nv_status = Nvsericestatus::where(function ($query) {
                            $query->where('rv4_status', 1)
                                ->orWhere('rv2_status', 1)
                                ->orWhere('rv3_status', 1)
                                ->orWhere('rv1_status', 1);
                        })
                            ->where(function ($query) {
                                $query->where('rv1_status', '!=', 2)
                                    ->where('rv2_status', '!=', 2)
                                    ->where('rv3_status', '!=', 2)
                                    ->where('rv4_status', '!=', 2);
                            })
                            ->get();
                    }else{
                        $nv_status = Nvsericestatus::where('draft', 1)->get(); 
                    }
                   
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }


                $cpmg_employees = Employee::whereIn("user_id", $user_id)->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn('department_id', $departmentIds)
                    ->whereIn("id", $nv_id)
                    ->Orwhere("user_id", $user_id)
                    ->orderBy("id", "desc")
                    
                    ->get();
                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');


                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->count();
                $approvedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("ceo_status", 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 2)->count();
                if(!empty($rv1)){
                    $pendingNV = $latestData->filter(function ($data) {
                                 return in_array($data->rv1_status, [1])&&
                                    in_array($data->hod_status, [0]);
                            })->count();
                }elseif(!empty($rv1)||!empty($rv2)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->rv2_status, [1])&&
                           in_array($data->hod_status, [0]);
                   })->count();
                }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->rv3_status, [1])&&
                           in_array($data->hod_status, [0]);
                   })->count();
              
                }elseif(!empty($rv1)||!empty($rv2)||!empty($rv3)||!empty($rv4)){
                        $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv4_status, [1]) &&
                            in_array($data->rv2_status, [0,1]) &&
                            in_array($data->rv3_status, [0,1])&&
                            in_array($data->rv1_status, [0,1]) &&
                            in_array($data->hod_status, [0]);
                })->count();
                }else{
                          $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->draft, [1]) &&
                             in_array($data->hod_status, [0]);
                })->count();
                }
            } elseif (!empty($id0->work_rew1) && $id0->work_rew1 == $user->id) {
                $nv_id = [];
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('derc_info', 1);
                })->where(function ($query) {
                    $query->where('hod_status', 1);
                })
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew1_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew1_status', 2)->count();

                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0]);
                })->count();
            } elseif (!empty($id0->work_rew2) && $id0->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('derc_info', 1);
                })->where(function ($query) {
                    $query->where('hod_status', 1);
                })
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
             
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
              
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew2_status', 2)->count();

                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0]);
                })->count();
            } elseif (!empty($id0->work_rew3) && $id0->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
             
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('derc_info', 1);
                })->where(function ($query) {
                    $query->where('hod_status', 1);
                })
                    ->get();

                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
             
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
             
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew3_status', 2)->count();

                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0]);
                })->count();
            } elseif (!empty($id0->work_rew4) && $id0->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
            
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('derc_info', 1);
                })->where(function ($query) {
                    $query->where('hod_status', 1);
                })
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
              
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew4_status', 2)->count();

             
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0]);
                })->count();
            } elseif (!empty($id0->approver) && $id0->approver == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = []; 
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
                    ->get();
                } elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)) {
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query
                            ->where('hod_status', 1);
                    })
                        ->where(function ($query) {
                            $query ->where('hod_status', '!=', 2)
                                  ->where('ces_rew1_status', '!=', 2)
                                ->where('ces_rew2_status', '!=', 2)
                                ->where('ces_rew3_status', '!=', 2)
                                ->where('ces_rew4_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 1);
                        })
                        ->get();
                }

                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
            
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
              
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_status', 2)->count();
          
                
                if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->hod_status, [1]) &&
                           ( in_array($data->ces_rew1_status, [1])||in_array($data->ces_rew2_status, [1])||in_array($data->ces_rew3_status, [1])||in_array($data->ces_rew4_status, [1])) &&
                            in_array($data->derc_info, [1]) &&
                            in_array($data->ces_status, [0]);
                    })->count();
                    } elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)) {
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->hod_status, [1]) &&
                              
                                in_array($data->derc_info, [1]) &&
                                in_array($data->ces_status, [0]);
                        })->count();
                    }

            } elseif (!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) {
                // dd('hi');
                $nv_id = [];
            
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('ces_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    // ->where('check_technology', 0)
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
               
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                  
                    ->Orwhere("user_id", $user->id)
                    ->get();
              

                $totalId = NeedValidation::pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                // dd($totalId);
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 2)->count();

               
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->hod_status, [1]) &&
                                in_array($data->work_rew1_status, [0]) &&    
                                in_array($data->work_rew2_status, [0])&&
                                in_array($data->work_rew3_status, [0])&&
                                in_array($data->work_rew4_status, [0])&&
                                (
                                    (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                                    ||
                                    (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                                );
                        })->count();
                       
            } elseif (!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
             
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('ces_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    // ->where('check_technology', 0)
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
           
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
            
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                    in_array($data->work_rew1_status, [0]) &&    
                    in_array($data->work_rew2_status, [0])&&
                    in_array($data->work_rew3_status, [0])&&
                    in_array($data->work_rew4_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
            } elseif (!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
               
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('ces_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    // ->where('check_technology', 0)
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
            
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

              
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                    in_array($data->work_rew1_status, [0]) &&    
                    in_array($data->work_rew2_status, [0])&&
                    in_array($data->work_rew3_status, [0])&&
                    in_array($data->work_rew4_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
            } elseif (!empty($id1->work_rew4) && $id1->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
               
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('ces_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    // ->where('check_technology', 0)
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
             
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                    in_array($data->work_rew1_status, [0]) &&    
                    in_array($data->work_rew2_status, [0])&&
                    in_array($data->work_rew3_status, [0])&&
                    in_array($data->work_rew4_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
            } elseif (!empty($id1->approver) && $id1->approver == $user->id) {
                // dd('hi');
                $nv_id = [];


                //         $nv_status = Nvsericestatus::where(function ($query) {
                //             $query->where('work_rew1_status', 1)
                //                 ->orWhere('work_rew2_status', 1)
                //                 ->orWhere('work_rew3_status', 1)
                //                 ->orWhere('work_rew4_status', 1);

                // })
                //             ->where(function ($query) {
                //             $query->where('work_rew1_status', '!=', 2)
                //             ->where('work_rew2_status', '!=', 2)
                //             ->where('work_rew3_status', '!=', 2)
                //             ->where('work_rew4_status', '!=', 2);
                // })
                //        ->get();
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
                    ->get();
                }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('ces_status', 1);
                    })
                        ->orWhere(function ($query) {
                            $query->where('hod_status', 1);
                        })
                        ->where(function ($query) {
                            $query->where('derc_info', 0);
                        })
                        ->get();
                }



                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
              
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();

                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                       ( in_array($data->work_rew1_status, [1])||in_array($data->work_rew2_status, [1])||in_array($data->work_rew3_status, [1])||in_array($data->work_rew4_status, [1])) &&
                        in_array($data->approver_status, [0]);
                })->count();
               }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                $pendingNV = $latestData->filter(function ($data) {
                     return in_array($data->hod_status, [1]) &&
                    in_array($data->approver_status, [0]) &&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
                }

                
            }
            // $check_technology = $id1->check_technology ;
            elseif (!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) {
                // dd('hi');
                //   $check_technology = $id1->check_technology ;
                $nv_id = [];

                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
                    ->get();


                // $nv_status = Nvsericestatus::where('approver_status', 1)->where('check_technology', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                // $workflow_rw1 = Workflow::whereIn(
                //     "id",
                //     $id
                // )->get();
                // foreach ($workflow_rw1 as $workflo_rw1) {
                //     array_push($id, $workflo_rw1["id"]);
                // }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","CAPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)
                    ->where('cto_status', 1)
                    ->count();

                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0]);
                })->count();
            } elseif (!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew1dep2_status', 1)->get();
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew1dep2_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->where('check_technology', 1);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)
                    ->where('cto_status', 1)
                    ->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return ($data->work_rew2_status == [0, 1] ||
                //         $data->work_rew3_status == [0, 1] ||
                //         $data->work_rew4_status == [0, 1]) ||

                //         $data->hod_status == 1 &&
                //         $data->work_rew1_status == 1 &&
                //         $data->approver_status == 1 &&
                //         $data->check_technology == 1 &&
                //         $data->work_rew1dep2_status == 1 &&
                //         $data->work_rew2dep2_status == 0;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0]);
                })->count();
            } elseif (!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew2dep2_status', 1)->get();
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew2dep2_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->where('check_technology', 1);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
                    ->get();

                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)
                    ->where('cto_status', 1)
                    ->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return ($data->work_rew2_status == [0, 1] ||
                //         $data->work_rew3_status == [0, 1] ||
                //         $data->work_rew4_status == [0, 1]) ||

                //         $data->hod_status == 1 &&
                //         $data->work_rew1_status == 1 &&
                //         $data->approver_status == 1 &&
                //         $data->check_technology == 1 &&
                //         $data->work_rew1dep2_status == 1 &&
                //         $data->work_rew2dep2_status == 1 &&
                //         $data->work_rew3dep2_status == 0;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0]);
                })->count();
            } elseif (!empty($id2->work_rew4) && $id2->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew3dep2_status', 1)->get();
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew3dep2_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->where('check_technology', 1);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
                    ->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)
                    ->where('cto_status', 1)
                    ->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return ($data->work_rew2_status == [0, 1] ||
                //         $data->work_rew3_status == [0, 1] ||
                //         $data->work_rew4_status == [0, 1]) ||

                //         $data->hod_status == 1 &&
                //         $data->work_rew1_status == 1 &&
                //         $data->approver_status == 1 &&
                //         $data->check_technology == 1 &&
                //         $data->work_rew1dep2_status == 1 &&
                //         $data->work_rew2dep2_status == 1 &&
                //         $data->work_rew3dep2_status == 1 &&
                //         $data->work_rew4dep2_status == 0;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0]);
                })->count();
            } elseif (!empty($id2->approver) && $id2->approver == $user->id) {
                // dd('hi');
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
                    })
                    ->get();
                }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('approver_status', 1)
                           ;
                    })
                        ->where(function ($query) {
                            $query->where('work_rew1dep2_status', '!=', 2)
                                ->where('work_rew2dep2_status', '!=', 2)
                                ->where('work_rew3dep2_status', '!=', 2)
                                ->where('work_rew4dep2_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 1);
                        })
                        ->get();
                }
           

                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)
                    ->where('cto_status', 1)
                    ->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approver_status, [1]) &&
                //         in_array($data->work_rew1dep2_status, [1]) &&
                //         in_array($data->cto_status, [0]);
                // })->count();
                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                       ( in_array($data->work_rew1dep2_status, [1])||in_array($data->work_rew2dep2_status, [1])||in_array($data->work_rew3dep2_status, [1])||in_array($data->work_rew4dep2_status, [1])) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->cto_status, [0]);
                })->count();
            }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                      
                        in_array($data->check_technology, [1]) &&
                        in_array($data->cto_status, [0]);
                })->count();
            }
            } elseif (!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('approverdep2_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('approver_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('check_technology', 0);
                    })
               
                    ->get();
             
                // $nv_status = Nvsericestatus::where('approverdep2_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                // $workflow_rw1 = Workflow::whereIn(
                //     "id",
                //     $id
                // )->get();
                // foreach ($workflow_rw1 as $workflo_rw1) {
                //     array_push($id, $workflo_rw1["id"]);
                // }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","CAPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

             
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
            } elseif (!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew1dep3_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->whereIn('check_technology', [0, 1]);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('approverdep2_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('approver_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('check_technology', 0);
                    })
               
                    ->get();
                // $nv_status = Nvsericestatus::where('work_rew1dep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->cto_status, [1]) ||
                //         in_array($data->cpmg_status, [1]) &&
                //         in_array($data->work_rew1dep3_status, [1]) &&
                //         in_array($data->work_rew2dep3_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
            } elseif (!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew2dep3_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->whereIn('check_technology', [0, 1]);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('approverdep2_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('approver_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('check_technology', 0);
                    })
               
                    ->get();
                // $nv_status = Nvsericestatus::where('work_rew2dep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->cto_status, [1]) ||
                //         in_array($data->cpmg_status, [1]) &&
                //         in_array($data->work_rew1dep3_status, [1]) &&
                //         in_array($data->work_rew3dep3_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
            } elseif (!empty($id3->work_rew4) && $id3->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where(function ($query) {
                //     $query->where('work_rew3dep3_status', 1);
                // })
                //     ->where(function ($query) {
                //         $query->whereIn('check_technology', [0, 1]);
                //     })->get();
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('approverdep2_status', 1);
                })
                    ->orWhere(function ($query) {
                        $query->where('approver_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('check_technology', 0);
                    })
               
                    ->get();
                // $nv_status = Nvsericestatus::where('work_rew3dep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->cto_status, [1]) ||
                //         in_array($data->cpmg_status, [1]) &&
                //         in_array($data->work_rew1dep3_status, [1]) &&
                //         in_array($data->work_rew4dep3_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
            } elseif (!empty($id3->approver) && $id3->approver == $user->id) {
                // dd('hi');
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
                    ->get();
                }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){

                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('cpmg_status', 1)
                            ->whereIn('approverdep3_status', [0,1])
                            ;
                    })
                        ->where(function ($query) {
                            $query->where('cpmg_status', '!=', 2)
                               ;
                        })
                        ->where(function ($query) {
                            $query->whereIn('check_technology', [0, 1]);
                        })
                        ->get();

                }

                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();

                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->cto_status, [0, 1]) &&
                //         in_array($data->cpmg_status, [1]) &&
                //         in_array($data->work_rew1dep3_status, [1]) &&
                //         in_array($data->approverdep3_status, [0]);

                // })->count();
                

                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->cpmg_status, [1]) &&
                              in_array($data->cto_status, [0,1]) &&
                           ( in_array($data->work_rew1dep3_status, [1])||in_array($data->work_rew2dep3_status, [1])||in_array($data->work_rew3dep3_status, [1])||in_array($data->work_rew4dep3_status, [1])) &&
                            in_array($data->approverdep3_status, [0]);
                    })->count();
                   }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->cpmg_status, [1]) &&
                              in_array($data->cto_status, [0,1]) &&
                           
                            in_array($data->approverdep3_status, [0]);
                    })->count();
                  }

            } elseif (!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                // $workflow_rw1 = Workflow::whereIn(
                //     "id",
                //     $id
                // )->get();
                // foreach ($workflow_rw1 as $workflo_rw1) {
                //     array_push($id, $workflo_rw1["id"]);
                // }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","CAPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [1]) &&
                //         in_array($data->work_rew1dep4_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]);
                })->count();
            } elseif (!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [1]) &&
                //         in_array($data->work_rew1dep4_status, [1]) &&
                //         in_array($data->work_rew2dep4_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]);
                })->count();
            } elseif (!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [1]) &&
                //         in_array($data->work_rew1dep4_status, [1]) &&
                //         in_array($data->work_rew3dep4_status, [0]);
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]);
                })->count();
            } elseif (!empty($id4->work_rew4) && $id4->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep3_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]);
                })->count();
            } elseif (!empty($id4->approver) && $id4->approver == $user->id) {
                // dd('hi');
                $nv_id = [];

                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                    ->get();
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->orWhere('approverdep3_status', 1)
                            ;
                    })
                        ->where(function ($query) {
                            $query->where('approverdep3_status', '!=', 2)
                               ;
                        })
                        ->get();
                }
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

               

                    if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                               (in_array($data->work_rew1dep4_status, [1])||in_array($data->work_rew2dep4_status, [1])||in_array($data->work_rew3dep4_status, [1])||in_array($data->work_rew4dep4_status, [1]))  &&
                                in_array($data->approverdep4_status, [0]);
                            })->count();
                       }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                             
                                in_array($data->approverdep4_status, [0]);
                            })->count();
                      }
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
             } elseif (!empty($id5->work_rew1) && $id5->work_rew1 == $user->id) {
                // dd('hi');
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                // $workflow_rw1 = Workflow::whereIn(
                //     "id",
                //     $id
                // )->get();
                // foreach ($workflow_rw1 as $workflo_rw1) {
                //     array_push($id, $workflo_rw1["id"]);
                // }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","CAPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [1]) &&
                    in_array($data->work_rew1dep5_status, [0]) &&
                    in_array($data->work_rew2dep5_status, [0]) &&
                    in_array($data->work_rew3dep5_status, [0]) &&
                    in_array($data->work_rew4dep5_status, [0]);
                })->count();
            } elseif (!empty($id5->work_rew2) && $id5->work_rew2 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew1dep5_status', 1)->get();
                $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [1]) &&
                    in_array($data->work_rew1dep5_status, [0]) &&
                    in_array($data->work_rew2dep5_status, [0]) &&
                    in_array($data->work_rew3dep5_status, [0]) &&
                    in_array($data->work_rew4dep5_status, [0]);
                })->count();
            } elseif (!empty($id5->work_rew3) && $id5->work_rew3 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew2dep5_status', 1)->get();
                $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [1]) &&
                    in_array($data->work_rew1dep5_status, [0]) &&
                    in_array($data->work_rew2dep5_status, [0]) &&
                    in_array($data->work_rew3dep5_status, [0]) &&
                    in_array($data->work_rew4dep5_status, [0]);
                })->count();
            } elseif (!empty($id5->work_rew4) && $id5->work_rew4 == $user->id) {
                // dd('hi');
                $nv_id = [];
                // $nv_status = Nvsericestatus::where('work_rew3dep5_status', 1)->get();
                $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }

                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                //   $workflow_rw1 = Workflow::whereIn(
                //         "id",
                //         $id
                //     )->get();
                //     foreach ($workflow_rw1 as $workflo_rw1) {
                //         array_push($id, $workflo_rw1["id"]);
                //     }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","OPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // dd($nv);
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();

                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [1]) &&
                        in_array($data->work_rew1dep5_status, [0]) &&
                        in_array($data->work_rew2dep5_status, [0]) &&
                        in_array($data->work_rew3dep5_status, [0]) &&
                        in_array($data->work_rew4dep5_status, [0]);
                })->count();
                
            } elseif (!empty($group_cio) && $group_cio == $user->id) { 
                $nv_id = [];
                $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    ->Orwhere("user_id", $user->id)
                    ->get();

                $totalId = NeedValidation::pluck('id');
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->count();
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [1]) &&
                        in_array($data->groupcio_status, [0]);
                })->count();
               
            }elseif (!empty($id5->approver) && $id5->approver == $user->id) {
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty($user_nv)){
                        $employees = Employee::where("user_id", $user_nv->user_id)->first();
                        $department = Department::where("id", $employees->department_id)->first();
                        $group_cio = $department->group_cio;
                    }
            
                $nv_id = [];
                    if(!empty( $group_cio)){
                     $nv_status = Nvsericestatus::where('groupcio_status', 1)->get();
                    }else{
                    $nv_status = Nvsericestatus::where('approverdep4_status', 1)->get();
                    }
                  
              
                foreach ($nv_status as $nv_status) {
                    array_push($nv_id, $nv_status["nv_id"]);
                }
                $cpmg_employees = Employee::whereIn(
                    "user_id",
                    $user_id
                )->get();
                foreach ($cpmg_employees as $cpmg) {
                    array_push($user_id, $cpmg["user_id"]);
                }
                
                $nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nv_id)
                    ->orderBy("id", "desc")
                    // ->where("budget_type","CAPEX")
                    // ->whereIn("user_id", $user_id)
                    ->Orwhere("user_id", $user->id)
                    ->get();
                // $totalNV = NeedValidation::whereIn('user_id',$user_id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();

                if(!empty( $group_cio)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->groupcio_status, [1]) &&
                            in_array($data->approverdep5_status, [0]);
                    })->count();
                   }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approverdep4_status, [1]) &&
                            in_array($data->approverdep5_status, [0]);
                    })->count();
                   }
                
            } else {
                // dd('else');
                // $nv = NeedValidation::with('service','division')
                // ->join('tbl_material',  'needvalidations.id', '=','tbl_material.nv_id')
                // ->select('tbl_material.id','needvalidations.*')
                // ->where("needvalidations.user_id", $user->id)
                // ->orderBy('tbl_material.id', 'desc')
                // ->get();
                $nv = NeedValidation::with('division', 'service')
                    ->orderBy('id', 'desc')
                    ->where("user_id", $user->id)
                    ->get();

                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) ||
                    $data->rv1_status == 2 ||
                    $data->rv2_status == 2 ||
                    $data->rv3_status == 2 ||
                    $data->rv4_status == 2 ||
                    $data->hod_status == 2 ||
                    $data->ces_rew1_status == 2 ||
                    $data->ces_rew2_status == 2 ||
                    $data->ces_rew3_status == 2 ||
                    $data->ces_rew4_status == 2 ||
                    $data->ces_status == 2 ||
                    $data->work_rew1_status == 2 ||
                    $data->work_rew2_status == 2 ||
                    $data->work_rew3_status == 2 ||
                    $data->work_rew4_status == 2 ||
                    $data->cpmg_status == 2 ||
                    $data->work_rew1dep2_status == 2 ||
                    $data->work_rew2dep2_status == 2 ||
                    $data->work_rew3dep2_status == 2 ||
                    $data->work_rew4dep2_status == 2 ||
                    $data->cto_status == 2 ||
                    $data->work_rew1dep3_status == 2 ||
                    $data->work_rew2dep3_status == 2 ||
                    $data->work_rew3dep3_status == 2 ||
                    $data->work_rew4dep3_status == 2 ||
                    $data->ceo_nominee_status == 2 ||
                    $data->work_rew1dep4_status == 2 ||
                    $data->work_rew2dep4_status == 2 ||
                    $data->work_rew3dep4_status == 2 ||
                    $data->work_rew4dep4_status == 2 ||
                    $data->ceo_nominee2_status == 2 ||
                    $data->groupcio_status == 2;
                })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [0])
                    && in_array($data->rv1_status, [0,1])
                    && in_array($data->rv2_status, [0,1])
                    && in_array($data->rv3_status, [0,1])
                    && in_array($data->rv4_status, [0,1])
                    && in_array($data->hod_status, [0, 1])
                    && in_array($data->ces_rew1_status, [0,1])
                    && in_array($data->ces_rew2_status, [0,1])
                    && in_array($data->ces_rew3_status, [0,1])
                    && in_array($data->ces_rew4_status, [0,1])
                    && in_array($data->cpmg_status, [0, 1])
                    && in_array($data->work_rew1_status, [0,1])
                    && in_array($data->work_rew2_status, [0,1])
                    && in_array($data->work_rew3_status, [0,1])
                    && in_array($data->work_rew4_status, [0,1])
                    && in_array($data->ces_status, [0, 1])
                    && in_array($data->work_rew1dep2_status, [0,1])
                    && in_array($data->work_rew2dep2_status, [0,1])
                    && in_array($data->work_rew3dep2_status, [0,1])
                    && in_array($data->work_rew4dep2_status, [0,1])
                    && in_array($data->cto_status, [0, 1])
                    && in_array($data->work_rew1dep3_status, [0,1])
                    && in_array($data->work_rew2dep3_status, [0,1])
                    && in_array($data->work_rew3dep3_status, [0,1])
                    && in_array($data->work_rew4dep3_status, [0,1])
                    && in_array($data->ceo_nominee_status, [0, 1])
                    && in_array($data->work_rew1dep4_status, [0,1])
                    && in_array($data->work_rew2dep4_status, [0,1])
                    && in_array($data->work_rew3dep4_status, [0,1])
                    && in_array($data->work_rew4dep4_status, [0,1])
                    && in_array($data->ceo_nominee2_status, [0, 1])
                    && in_array($data->groupcio_status, [0, 1]) ;
                })->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV ;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
            }
        }
        // $nvv = NeedValidation::whereHas('service')->select('id')
        // ->get();

        // $nv_mat = NVMaterial::whereIn('nv_id',$nvv)->first();

        if ($request->ajax()) {
            $needvalidation = datatables()
                ->of($nv)
                ->addColumn("id", function ($data) {
                    return $data->id;
                })
            //     ->addColumn("Edit_NV", function ($data) use ($user) {
            //         $button = "";
                   
            //         if ($user->can("edit_NV")) {
            //         if ($user->role_id == 9) {
            //          $button = '<a href="/admin/needvalidations/edit_need/' .
            //          $data->id .
            //          '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="far fa-edit" style="font-size:20px;color:red"></i></a>';
                   
            //      }else{
            //         $button = '<a href="#' .
            //         $data->id .
            //         '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="far fa-edit" style="font-size:20px;color:red"></i></a>';
            //     }
               
            // }
            //    return $button;     
               
            //     })
                ->addColumn("proposal_No", function ($data) {
                    return 'NV' . '/' . $data->budget_type . '/' . $data->fiscal_year . '/' . getDepartmentNameByPro($data->user_id) . '/' . $data->service->name . '/' . $data->id;
                })
                ->addColumn("budget_type", function ($data) {
                    return $data->budget_type;
                })
                ->addColumn("user", function ($data) {
                    return $data->user->name;
                })
                ->addColumn("created_at", function ($data) {
                    return $data->created_at->format('d-m-Y');
                })
                ->addColumn("ref_num", function ($data) {
                    if ($data->service_id == 1) {
                        return $data->material->dop ?? '';
                    } else {
                        return $data->services->dop_ref_no ?? '';
                    }
                })
                ->addColumn("budgetary_provision", function ($data) {
                    return $data->budgetary_provision;
                })
                ->addColumn("proposal_type", function ($data) {
                    return $data->proposal_type;
                })

                ->addColumn("service", function ($data) {
                    return $data->service->name;
                })
                ->addColumn("fiscal_year", function ($data) {
                    return $data->fiscal_year;
                })

                ->addColumn("action", function ($data) use ($user) {
                    $button = "";
                     if ($user->can("edit_NV")) {
                          if ($user->role_id == 9) {
                                if ($data->service_id == 1) {
                                    if (!empty($data->material)){
                                        if($data->material->draft == 0){
                                            $button =
                                            '<a href="/admin/nv_material/create/' .
                                            $data->id . '/' . $data->company_id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>' .
                                            '<a  href="/admin/nv_material/preview/' .
                                            $data->id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
                                            '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download Material PDF">' .
                                            '<i class="fas fa-print text-info"></i>'.'</a>'.
                                            '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download MaterialBOQ PDF">' .
                                                '<i class="fa fa-download text-info"></i>'.'</a>';
                                        }elseif($data->material->draft == 1){
                                            $button =  '<a  href="/admin/nv_material/preview/' .
                                            $data->id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
                                            '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download Material PDF">' .
                                            '<i class="fas fa-print text-info"></i>'.'</a>'.
                                            '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download MaterialBOQ PDF">' .
                                                '<i class="fa fa-download text-info"></i>'.'</a>';
                                        }
                                       
                                        
                                    } else {
                                        $button =
                                            '<a href="/admin/nv_material/create/' .
                                            $data->id . '/' . $data->company_id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>';
                                    }
                                } else {
                                    if (!empty($data->services)) {
                                        if($data->services->draft == 0){
                                            $button =
                                            '<a href="/admin/nv_service/create/' .
                                            $data->id . '/' . $data->company_id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Service"><i class="fas fa-edit text-info"></i></a>' .
                                            '<a href="/admin/nv_service/preview/' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .
                                            // '<a target="_blank" href="/images/form.png' .
                                            // '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'. 
                                            '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV Service PDF">' .
                                            '<i class="fas fa-print text-info"></i>' .'</a>';
                                        //   .'<a target="_blank" href="' . route("admin.view", ["id" => encrypt($data->id)]) . '"class="btn btn-sm btn-clean btn-icon" id="popUp"><i class="fas fa-print text-info"></i></a>';
                                        }elseif($data->services->draft == 1){
                                            $button = '<a href="/admin/nv_service/preview/' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .
                                            // '<a target="_blank" href="/images/form.png' .
                                            // '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'. 
                                            '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV Service PDF">' .
                                            '<i class="fas fa-print text-info"></i>' .'</a>';
                                        //   .'<a target="_blank" href="' . route("admin.view", ["id" => encrypt($data->id)]) . '"class="btn btn-sm btn-clean btn-icon" id="popUp"><i class="fas fa-print text-info"></i></a>';
                                        }
                                       
                                    } else {
                                        $button =
                                            '<a href="/admin/nv_service/create/' .
                                            $data->id . '/' . $data->company_id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Service"><i class="fas fa-edit text-info"></i></a>';
                                    }
                                }
                            } elseif ($user->role_id == 11) {
                                if ($data->service_id == 1) {
                                    if (!empty($data->material)) {
                                        $button = '<a  href="/admin/nv_material/preview/' .
                                            $data->id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
                                            // '<a target="_blank" href="/images/form.png' .
                                            // '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'.  
                                            '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download Material PDF">' .
                                            '<i class="fas fa-print text-info"></i>' .
                                            '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download MaterialBOQ PDF">' .
                                            '<i class="fa fa-download text-info"></i>'.'</a>' ;
                                            // '</a>'.'<a  href="' . route('download.attachments', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download PDF" >' .
                                            // '<i class="fas fa-download text-info"></i>' .
                                            // '</a>' ;
                                        //   .'<a target="_blank" href="' . route("admin.view", ["id" => encrypt($data->id)]) . '"class="btn btn-sm btn-clean btn-icon" id="popUp"><i class="fas fa-print text-info"></i></a>';

                                    } else {
                                        $button = '<a  href="#' .
                                            $data->id .
                                            '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>' ;
                                            // '<a target="_blank" href="/images/form.png' .
                                            // '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'.
                                            // '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download PDF">' .
                                            // '<i class="fas fa-print text-info"></i>' .
                                            // '</a>';
                                    }
                                } else {
                                    if (!empty($data->services)) {
                                        $button = '<a href="/admin/nv_service/preview/' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .
                                            //   '<a target="_blank" href="/images/form.png' .
                                            //     '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'.  
                                            '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download Service PDF">' .
                                            '<i class="fas fa-print text-info"></i>' .'</a>';
                                        //   .'<a target="_blank" href="' . route("admin.view", ["id" => encrypt($data->id)]) . '"class="btn btn-sm btn-clean btn-icon" id="popUp"><i class="fas fa-print text-info"></i></a>';
                                    } else {
                                        $button = '<a href="#' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' ;
                                            //   '<a target="_blank" href="/images/form.png' .
                                            //     '" class="btn btn-sm btn-clean btn-icon" title="View"><i class="fas fa-print text-info"></i></a>'. 
                                            // '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download PDF">' .
                                            // '<i class="fas fa-print text-info"></i>' .
                                            // '</a>';
                                    }
                                }
                            } elseif ($user->role_id == 1) {
                                if ($data->service_id == 1) {

                                    $button = '<a  href="#' .
                                        $data->id .
                                        '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>' ;
                                } else {

                                    $button = '<a href="#' .
                                        $data->id .
                                        '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>';
                                }
                            }
                        }
                  
                    return $button;
                })

                ->addIndexColumn()
                ->rawColumns(["action","Edit_NV", "proposal_No", "service", "user", "ref_num"])

                ->make(true);

            return $needvalidation;
        }
        // $totalNV = NeedValidation::select('*')->get()->count();
        return view("admin.NV.list", compact("totalNV", "approvedNV", "rejectedNV", "latestData", "pendingNV"));
    }

    public function create_NV(Request $request)
    {
        $user = \Auth()->user();
        $emp = Employee::select("id", "name", 'division_id','department_id')
            ->where("status", 1)->where('user_id', $user->id)
            ->first();
        // dd($divisions);
        $divisions = Division::select("id", "name")
            ->where("status", 1)->where('id', $emp->division_id)
            ->get();
        $departments = Department::select("id", "name")
            ->where("status", 1)->where('id', $emp->department_id)
            ->get();
        $services = Service::select("id", "name")
            ->where("status", 1)
            ->get();
        return view("admin.NV.create", compact("divisions", "services", "departments"));
    }
    public function print_NV(Request $request)
    {
        $divisions = Division::select("id", "name")
            ->where("status", 1)
            ->get();
        $services = Service::select("id", "name")
            ->where("status", 1)
            ->get();
        return view("admin.NV.create", compact("divisions", "services"));
    }
    public function store_NV(Request $request)
    {
        try {
            $request_input = $request->except("_token");
            $company_id = $request_input["company_name"];

           
       
            if (
                NeedValidation::where(["company_id" => $company_id])->exists()
            ) {
                $rules = [
                    "company_name" => "required",
                    "budget_type" => "required",
                    "budget_prov" => "required",
                    "prop_type" => "required",
                    "nv_type" => "required",
                    "fiscal_year" => "required",
                ];

                $messages = [
                    "company_name.required" => "Please enter company name",
                    "budget_type.required" => "Please enter budget type",
                    "budget_prov.required" =>
                    "Please enter budgetary provision",
                    "prop_type.required" => "Please enter proposal type",
                    "nv_type.required" => "Please enter nv type",
                    "fiscal_year.required" => "Please enter fiscal year",
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $needvalidation = NeedValidation::create([
                        "company_id" => $request_input["company_name"],
                        "department_id" => $request_input["department_id"],
                        "user_id" => \Auth::user()->id,
                        
                        "budget_type" => $request_input["budget_type"],
                        "budgetary_provision" => $request_input["budget_prov"],
                        "proposal_type" => $request_input["prop_type"],
                        "service_id" => $request_input["nv_type"],
                        "fiscal_year" => $request_input["fiscal_year"],
                    ]);
                   
                    $response["result"] = "success";
                    $response["msg"] = "NV created";
                }
            } else {
                $rules = [
                    "company_name" => "required",
                    "budget_type" => "required",
                    "budget_prov" => "required",
                    "prop_type" => "required",
                    "nv_type" => "required",
                    "fiscal_year" => "required",
                ];

                $messages = [];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $needvalidation = NeedValidation::create([
                        "company_id" => $request_input["company_name"],
                        "department_id" => $request_input["department_id"],
                        "user_id" => \Auth::user()->id,
                        
                        "budget_type" => $request_input["budget_type"],
                        "budgetary_provision" => $request_input["budget_prov"],
                        "proposal_type" => $request_input["prop_type"],
                        "service_id" => $request_input["nv_type"],
                        "fiscal_year" => $request_input["fiscal_year"],
                    ]);
                    // dd($needvalidation);

                    $response["result"] = "success";
                    $response["msg"] = "NV created";
                }
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function edit_NV(Request $request)
    {
        $brand = Brand::findOrFail($request->id);
        return view("admin.brand.edit", compact("brand"));
    }

    public function edit_need($id)
    {
        $user = \Auth()->user();
        $data = NeedValidation::where(['id' => $id])->first();
        $divisions = Employee::select("id", "name", 'division_id')
            ->where("status", 1)->where('user_id', $user->id)
            ->first();
        // dd($divisions);
        $divisions = Division::select("id", "name")
            ->where("status", 1)->where('id', $divisions->division_id)
            ->get();
        $services = Service::select("id", "name")
            ->where("status", 1)
            ->get();
            $nvType = Service::where('id', $data->service_id)->first();

   

           return view('admin.NV.edit', compact("nvType","data","divisions", "services"));
       
    }
    
     public function update_NV(Request $request, $id)
    {
       
        $request->validate([
            'company_name' => 'required',
            'budget_type' => 'required',
            'budget_prov' => 'required',
            'prop_type' => 'required',
            'nv_type' => 'required',
            'fiscal_year' => 'required',
        ]);

     
        $nv = NeedValidation::findOrFail($id);

    
        $nv->update([
            "company_id" => $request->input("company_name"),
            "budget_type" => $request->input("budget_type"),
            "budgetary_provision" => $request->input("budget_prov"),
            "proposal_type" => $request->input("prop_type"),
            "service_id" => $request->input("nv_type"),
            "fiscal_year" => $request->input("fiscal_year"),
        ]);

        return response()->json(['message' => 'NV details updated successfully']);
        
    }
    
    public function delete_NV(Request $request)
    {
        try {
            $id = $request["id"];
            if (!empty($id)) {
                //$brand = Brand::findOrFail($id);
                $assets = Task::where("id", $id);
                $floor = Ticket::where("task_id", $id);

                $assets->delete();
                $floor->delete();

                $response["result"] = "success";
                $response["msg"] = "Task Deleted";
            } else {
                $response["result"] = "failure";
                $response["msg"] = "Select Brand";
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function createPrintMaterial(Request $request, $id)
    {
        $user = \Auth()->user();
        $data = NVMaterial::where('nv_id', $id)->first();
        $data_doc = MaterialDoc::where('service_id', $data->id)->first();
        return view("admin.nvMaterial.view", compact('data', 'data_doc'));
    }

    public function createPrintService(Request $request, $id)
    {
        $user = \Auth()->user();
        $data = NVService::where('nv_id', $id)->first();
        $service_doc = ServiceDoc::where('service_id', $data->id)->first();
        return view("admin.nvService.view", compact('data', 'service_doc'));
    }

    public function createView(Request $request, $id)
    {
        $id = decrypt($id);
        $needvalidation = NeedValidation::find($id);
        return view("admin.print.view", compact("needvalidation"));
    }
    public function export_excel(Request $request)
    {
        $list = NeedValidation::with('division', 'services', 'user', 'material', 'services')->orderby('id', 'DESC')->get();
        $output = '';
        $output .= "S.no,Proposal Number,Budget Type,Initiated By,Initiated Date,DOP Ref No,Budgetary Provision,Proposal Type,NV Type,Fiscal Year";
        $output .= "\n";
        foreach ($list as $key => $NV_list) {
            $i = $key + 1;
            $output .= $i . ',';
            if ($NV_list->service_id == 1) {
                $output .= 'NV/' . '' . $NV_list->budget_type . '' . '/' . '' . $NV_list->fiscal_year . '' . '/' . '' . '/' . '' . 'Material' . '' . $NV_list->id . ',';
            } else {
                $output .= 'NV/' . '' . $NV_list->budget_type . '' . '/' . '' . $NV_list->fiscal_year . '' . '/' . '' . '/' . '' . 'Service' . '' . $NV_list->id . ',';
            }
            $output .= $NV_list->budget_type . ',';
            $output .= $NV_list['user']['name'] . ',';
            $output .= date('d-M-y', strtotime($NV_list->created_at)) . ',';
            if ($NV_list->service_id == 1) {
                $output .= ($NV_list['material']['dop'] ?? '') . ',';
            } else {
                $output .= ($NV_list['services']['dop_ref_no'] ?? '') . ',';
            }
            $output .= $NV_list->budgetary_provision . ',';
            $output .= $NV_list->proposal_type . ',';
            if ($NV_list->service_id == 1) {
                $output .= 'Material' . ',';
            } elseif ($NV_list->service_id == 2) {
                $output .= 'Service' . ',';
            }
            $output .= $NV_list->fiscal_year . ',';
            $output .= "\n";
        }
        header("Content-type: text/xlsx");
        header("Content-Disposition: attachment; filename=NV_list_excel.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $output;
    }
    public function export_pdf(Request $request)
    {
        $list = NeedValidation::with('division', 'services', 'user', 'material', 'services')->orderby('id', 'DESC')->get();
        $pdf = PDF::loadView('admin.NV.list_pdf', ["list" => $list])->setPaper('a4', 'landscape');
        return $pdf->download('NV_list_pdf.pdf');
    }


   
    public function list_nv(Request $request)
    {
        $user_id = \Auth::user()->id;
        $list = NeedValidation::with('division', 'services', 'user', 'material', 'services')->orderby('id', 'DESC')->get();
        $material = DB::table('tbl_material')->where('user_id',$user_id)->select('dop')->orderby('id', 'DESC')->get();
        $service = DB::table('tbl_service')->where('user_id',$user_id)->orderby('id', 'DESC')->select('dop_ref_no')->get();
       if($list[0]->service_id == 1)
       {
        $dop = $material;
       }
       else{
        $dop = $service;
       }
       
        return response()->json([
            "message"       => "Success",
            "list"          =>  $list ?? '',
            "material"      =>  $material ??'',
            "service"       =>  $service ?? '',
            "dop"           =>  $dop ?? '',
            "code"          => 200
        ]);
    }
}
