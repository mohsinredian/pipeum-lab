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
use App\Models\Capex;
use App\Models\Opex;
use App\Models\NVMaterial;
use App\Models\MaterialDoc;
use App\Models\Workflow;
use App\Models\OpexWorkflow;
use App\Models\NVService;
use App\Models\ServiceDoc;
use App\Models\NeedValidation;
use App\Models\budget;
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
use Auth;
use App\Models\CapexBudget;
use App\Models\OpexBudget;
use Carbon\Carbon;

class NVController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put("active", "needvalidation");

            return $next($request);
        });
    }

    // public function NV_list(Request $request)
    // {
    //     $user = \Auth()->user();
    //     $user_id = [];
    //     $nv1 = array();
    //     if ($user->role_id == 1) {
    //         $nv1 = NeedValidation::with('division', 'service')->where('delete_draft', 0)
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $totalId = NeedValidation::where('delete_draft', 0)->pluck('id');


    //         $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->get();

    //         $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
    //         $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();

    //         $rejectedNV = $latestData->filter(function ($data) {
    //             return $data->is_reject == 1;
    //         })->count();

    //         $pendingNV = $latestData->filter(function ($data) {
    //             return in_array($data->ceo_status, [0])
    //                 && in_array($data->is_reject, [0]);
    //         })->count();
    //     } elseif ($user->role_id == 9) {
    //         $nv1 = NeedValidation::with('division', 'service')
    //             ->orderBy('id', 'desc')
    //             ->where("user_id", $user->id)->where('delete_draft', 0)
    //             ->get();

    //         $totalId = NeedValidation::where("user_id", $user->id)->where('delete_draft', 0)->pluck('id');
    //         $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //         $approvedNV = $latestData->where('ceo_status', 1)->count();
    //         $rejectedNV = $latestData->filter(function ($data) {
    //             return $data->is_reject == 1;
    //         })->count();

    //         $pendingNV = $latestData->filter(function ($data) {
    //             return in_array($data->ceo_status, [0])
    //                 && in_array($data->is_reject, [0]);
    //         })->count();
    //         $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
    //     } else {

    //         $user = auth()->user();

    //         /* ======================================================
    //         | STEP 1: Employee + Department IDs
    //         ====================================================== */
    //         $employee = Employee::where('user_id', $user->id)->first();


    //         $departmentIds = array_map('intval', explode(',', $employee->department_id));

    //         $departments = Department::whereIn('id', $departmentIds)
    //             ->where('status', 1)
    //             ->get();

    //         /* ======================================================
    //         | STEP 2: Employees of same departments
    //         ====================================================== */
    //         $user_id = Employee::where(function ($q) use ($departmentIds) {
    //             foreach ($departmentIds as $id) {
    //                 $q->orWhereRaw("FIND_IN_SET(?, department_id)", [$id]);
    //             }
    //         })->pluck('user_id')->unique()->toArray();

    //         /* ======================================================
    //         | STEP 3: Init
    //         ====================================================== */
    //         $nv1 = collect();
    //         $totalNV = 0;
    //         $approvedNV = 0;
    //         $rejectedNV = 0;
    //         $pendingNV = 0;

    //         /* ======================================================
    //         | STEP 4: Department Loop
    //         ====================================================== */

    //         foreach ($departments as $department) {

    //             $depId = $department->id;

    //             $roles = [
    //                 'rv1' => $department->dep_rew1,
    //                 'rv2' => $department->dep_rew2,
    //                 'rv3' => $department->dep_rew3,
    //                 'rv4' => $department->dep_rew4,
    //                 'hod' => $department->dep_hod,
    //                 'group_cio' => $department->group_cio,
    //             ];
    //             /* ==========================================
    //             | REVIEWERS (RV1 → RV4)
    //             ========================================== */
    //             foreach (['rv1', 'rv2', 'rv3', 'rv4'] as $i => $role) {

    //                 if (empty($roles[$role]) || $roles[$role] != $user->id) {
    //                     continue;
    //                 }
    //                 $prev = $i > 0 ? 'rv' . $i . '_status' : 'draft';
    //                 $curr = $role . '_status';

    //                 $nvIds = Nvsericestatus::where(function ($q) use ($prev) {
    //                     $prev === 'draft'
    //                         ? $q->where('draft', 1)
    //                         : $q->where($prev, 1);
    //                 })
    //                     ->where($curr, '!=', 2)
    //                     ->pluck('nv_id');

    //                 $records = NeedValidation::with('division', 'service')
    //                     ->where('delete_draft', 0)
    //                     ->where('department_id', $depId)
    //                     ->whereIn('id', $nvIds)
    //                     ->get();

    //                 $recordNvIds = $records->pluck('id');
    //                 $nv1 = $nv1->merge($records);
    //                 $totalNV    += Nvsericestatus::whereIn('nv_id', $recordNvIds)->where($curr, 1)->count();
    //                 $rejectedNV += Nvsericestatus::whereIn('nv_id', $recordNvIds)->where($curr, 2)->count();
    //                 $pendingNV  += Nvsericestatus::whereIn('nv_id', $recordNvIds)->where($curr, 0)->count();
    //             }
                

 

    //             /* ==========================================
    //             | GROUP CIO LOGIC
    //             ========================================== */
    //             if (Department::where("group_cio", $user->id)->exists()) {
    //                             /* ==========================================
    //                             | INITIALIZATION 
    //                             ========================================== */
    //                             $nv1 = collect(); // Empty collection
    //                             $pendingNV = 0;
    //                             $totalNV = 0;
    //                             $rejectedNV = 0;
    //                             $approvedNV = 0;
    //                 /* ========== DEPARTMENTS ========== */
    //                 $departmentIds = Department::where('group_cio', $user->id)->pluck('id');

    //                 /* ===== LIST (SINGLE SOURCE OF TRUTH) ===== */
    //                 // CIO ka data calculate karke main variable mein store karein
    //                 $cioNv1 = NeedValidation::with(['division', 'service'])
    //                     ->join('nvservicestatus as nvs', 'nvs.nv_id', '=', 'needvalidations.id')
    //                     ->whereIn('needvalidations.department_id', $departmentIds)
    //                     ->where('nvs.hod_status', 1)
    //                     ->select(
    //                         'needvalidations.*',
    //                         'nvs.is_reject'
    //                     )
    //                     ->orderBy('needvalidations.id', 'desc')
    //                     ->get();

    //                 // List ko merge karein
    //                 $nv1 = $nv1->concat($cioNv1);

    //                 /* ===== IDS FROM LIST ===== */
    //                 $nvIds = $cioNv1->pluck('id');

    //                 /* ===== COUNTS (FROM SAME IDS) ===== */

    //                 // Pending at Group CIO
    //                 $pendingNV += Nvsericestatus::whereIn('nv_id', $nvIds)
    //                     ->where('draft', 1)
    //                     ->where('hod_status', 1)
    //                     ->where('groupcio_status', 0)
    //                     ->count();

    //                 // Approved at Group CIO
    //                 $totalNV += Nvsericestatus::whereIn('nv_id', $nvIds)
    //                     ->where('groupcio_status', 1)
    //                     ->count();

    //                 // Rejected at Group CIO
    //                 $rejectedNV += Nvsericestatus::whereIn('nv_id', $nvIds)
    //                     ->where('groupcio_status', 2)
    //                     ->count();

    //                 // Final Approved (workflow)
    //                 $approvedNV += DB::table('capex_workflows_status as cws')
    //                     ->join(DB::raw('(
    //                         SELECT nv_id, MAX(workflow_serial) max_serial
    //                         FROM capex_workflows_status
    //                         GROUP BY nv_id
    //                     ) latest'), function ($join) {
    //                         $join->on('cws.nv_id', '=', 'latest.nv_id')
    //                             ->on('cws.workflow_serial', '=', 'latest.max_serial');
    //                     })
    //                     ->whereIn('cws.nv_id', $nvIds)
    //                     ->where('cws.nv_stage_status', 1)
    //                     ->count();
    //             }

    //             /* ==========================================
    //             | HOD LOGIC
    //             ========================================== */
    //             if (!empty($roles['hod']) && $roles['hod'] == $user->id) {

    //                 /* ================= HOD DEPARTMENTS ================= */
    //                 $departmentIds = Department::where('dep_hod', $user->id)->pluck('id');

    //                 /* ================= BASE QUERY (SINGLE SOURCE) ================= */
    //                 $baseStatusQuery = Nvsericestatus::where('draft', 1)
    //                     ->where(function ($q) {
    //                         $q->where('rv1_status', '!=', 2)
    //                             ->where('rv2_status', '!=', 2)
    //                             ->where('rv3_status', '!=', 2)
    //                             ->where('rv4_status', '!=', 2)
    //                             ->where('hod_status', '!=', 2);
    //                     });

    //                 /* ================= NV IDS ================= */
    //                 $nvIds = $baseStatusQuery->pluck('nv_id');

    //                 /* ================= HOD LIST (Alag variable mein store karein) ================= */
    //                 // Hum $hodNv1 variable mein data lenge taaki $nv1 overwrite na ho
    //                 $hodNv1 = NeedValidation::with("division", "service")
    //                     ->where('delete_draft', 0)
    //                     ->whereIn('department_id', $departmentIds)
    //                     ->whereIn("id", $nvIds)
    //                     ->orderBy("id", "desc")
    //                     ->get();

    //                 // Ab is list ko main list ($nv1) mein add (merge) karein
    //                 $nv1 = $nv1->concat($hodNv1);

    //                 /* ================= COUNTS (ADD TO TOTALS) ================= */
    //                 $hodIds = $hodNv1->pluck('id');

    //                 $pendingNV += Nvsericestatus::whereIn("nv_id", $hodIds)
    //                     ->where('draft', 1)
    //                     ->where('hod_status', 0)
    //                     ->where('nvservicestatus.rv1_status', '!=', 2)
    //                     ->where('nvservicestatus.rv2_status', '!=', 2)
    //                     ->where('nvservicestatus.rv3_status', '!=', 2)
    //                     ->where('nvservicestatus.rv4_status', '!=', 2)
    //                     ->count();

    //                 $totalNV += Nvsericestatus::whereIn('nv_id', $hodIds)
    //                     ->where('hod_status', 1)
    //                     ->count();

    //                 $rejectedNV += Nvsericestatus::whereIn('nv_id', $hodIds)
    //                     ->where('hod_status', 2)
    //                     ->count();

    //                 /* ================= FINAL APPROVED ================= */
    //                 $approvedNV += DB::table('capex_workflows_status as cws')
    //                     ->join(DB::raw('(
    //                         SELECT nv_id, MAX(workflow_serial) max_serial
    //                         FROM capex_workflows_status
    //                         GROUP BY nv_id
    //                     ) latest'), function ($join) {
    //                         $join->on('cws.nv_id', '=', 'latest.nv_id')
    //                             ->on('cws.workflow_serial', '=', 'latest.max_serial');
    //                     })
    //                     ->where('cws.nv_stage_status', 1)
    //                     ->count();
    //             }

    //             /* ==========================================
    //             | OPTIONAL: SORT FINAL LIST
    //             ========================================== */
             
    //             $nv1 = $nv1->sortByDesc('id')->values(); // values() resets keys

    //         }
    //         $hasWorkflow = DB::table('capex_workflows_status')
    //             ->where('workflow_user_id', $user->id)
    //             ->exists();
    //         if ($hasWorkflow) {

    //             // Step 1: Fetch workflows assigned to the current user
    //             $workflows = DB::table('capex_workflows_status')->get();

    //             if ($workflows->isNotEmpty()) {

    //                 foreach ($workflows as $workflow) {

    //                     $capexWorkflowUsers = $workflow->workflow_user_id;

    //                     if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {
    //                         // $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->get();
    //                         $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('transfer_to_nominee1', 0)->get();

    //                         $capexNvIds = [];
    //                         $opexNvIds = [];

    //                         foreach ($nv_workflows as $nvs) {
    //                             $nv_id = $nvs->nv_id;
    //                             $workflow_serial = $nvs->workflow_serial;
    //                             $budget_type = $nvs->nv_budget_type;
    //                             $isApprover = strtolower($nvs->reviewer_name) === 'approver';

    //                             $addToResult = false;

    //                             if ($isApprover) {
    //                                 // Get all reviewers (not approvers) in the same workflow_serial
    //                                 $reviewers = DB::table('capex_workflows_status')
    //                                     ->where('nv_id', $nv_id)
    //                                     ->where('workflow_serial', $workflow_serial)
    //                                     ->where('reviewer_name', '!=', 'approver')
    //                                     ->get();

    //                                 if ($reviewers->isNotEmpty()) {
    //                                     // If any reviewer has approved
    //                                     $addToResult = $reviewers->contains(fn($rev) => $rev->nv_stage_status == 1);
    //                                 } elseif ($workflow_serial == 1) {
    //                                     // If workflow_serial is 1 and no reviewers exist, approver can directly see
    //                                     $addToResult = true;
    //                                 } else {
    //                                     // Check if previous workflow_serial's approver approved
    //                                      if($nvs->department_id == 19){
    //                                             $transfer_to_nom1 = DB::table('capex_workflows_status')
    //                                             ->where('nv_id', $nv_id)
    //                                             ->where('department_id', 16)
    //                                             ->where('transfer_to_nominee1', 1)
    //                                             ->exists();
    //                                              if($transfer_to_nom1){
    //                                                  $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 2)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                              }else{
    //                                                  $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 1)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                              }
    //                                     }
    //                                 }
    //                             } else {
    //                                 // Reviewer logic
    //                                 if ($workflow_serial == 1) {
    //                                     $addToResult = true;
    //                                 } else {
    //                                    if($nvs->department_id == 19){
    //                                             $transfer_to_nom1 = DB::table('capex_workflows_status')
    //                                             ->where('nv_id', $nv_id)
    //                                             ->where('department_id', 16)
    //                                             ->where('transfer_to_nominee1', 1)
    //                                             ->exists();
    //                                              if($transfer_to_nom1){
    //                                                  $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 2)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                              }else{
    //                                                  $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 1)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                              }
    //                                     }
    //                                 }
    //                             }

    //                             if ($addToResult) {
    //                                 if ($budget_type === 'CAPEX') {
    //                                     $capexNvIds[] = $nv_id;
    //                                 } elseif ($budget_type === 'OPEX') {
    //                                     $opexNvIds[] = $nv_id;
    //                                 }
    //                             }
    //                         }
    //                         $capexNvIds = array_unique($capexNvIds);
    //                         $opexNvIds = array_unique($opexNvIds);

    //                         $capexData = NeedValidation::with("division", "service")
    //                             ->whereIn("id", $capexNvIds)
    //                             ->where("budget_type", 'CAPEX')
    //                             ->orderBy("id", "desc")
    //                             ->get();

    //                         $opexData = NeedValidation::with("division", "service")
    //                             ->whereIn("id", $opexNvIds)
    //                             ->where("budget_type", 'OPEX')
    //                             ->orderBy("id", "desc")
    //                             ->get();

    //                         $nv1 = $capexData->merge($opexData);

    //                         $approvedNV = DB::table('capex_workflows_status as cws')
    //                             ->join(
    //                                 DB::raw('(SELECT nv_id, MAX(workflow_serial) as max_serial
    //                                                                             FROM capex_workflows_status
    //                                                                             GROUP BY nv_id) as latest'),
    //                                 function ($join) {
    //                                     $join->on('cws.nv_id', '=', 'latest.nv_id')
    //                                         ->on('cws.workflow_serial', '=', 'latest.max_serial');
    //                                 }
    //                             )
    //                             ->where('cws.nv_stage_status', 1)
    //                             ->whereIn('cws.nv_id', $nv1->pluck('id'))
    //                             ->count();

    //                         $rejectedNV = DB::table('capex_workflows_status')
    //                             ->where('workflow_user_id', $user->id)
    //                             ->where('nv_stage_status', 2)
    //                             ->whereIn('nv_id', $nv1->pluck('id'))
    //                             ->count();

    //                         $pendingNV = DB::table('capex_workflows_status')
    //                             ->where('workflow_user_id', $user->id)
    //                             ->where('nv_stage_status', 0)
    //                             ->whereIn('nv_id', $nv1->pluck('id'))
    //                             ->count();
    //                         $totalNV = DB::table('capex_workflows_status')
    //                             ->where('workflow_user_id', $user->id)
    //                             ->where('nv_stage_status', 1)
    //                             ->whereIn('nv_id', $nv1->pluck('id'))
    //                             ->count();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     if($user->role_id == 1){
    //         $nv1 =  NeedValidation::join(
    //                     'nvservicestatus',
    //                     'nvservicestatus.nv_id',
    //                     '=',
    //                     'needvalidations.id'
    //                 )
    //                 ->where('nvservicestatus.draft','=', 1)
    //                 ->where('nvservicestatus.is_reject','=', 0)
    //                 ->where('needvalidations.delete_draft','=', 0)
    //                 ->orderBy('needvalidations.id', 'desc')
    //                 ->select(
    //                     'needvalidations.*'
    //                 )->get();
    //     }else{
    //             $nv1;
    //     }

    //     if ($request->ajax()) {

    //         $needvalidation = datatables()
    //             ->of(
    //                 $nv1->filter(function ($data) {
    //                     return $data->delete_draft == 0 && $data->is_reject == 0;
    //                 })
    //             )


    //             ->addColumn("proposal_No", function ($data) {


    //                 if ($data->delete_draft == 0) {
    //                     return 'NV' . '/' . $data->budget_type . '/' . $data->fiscal_year . '/' . getDepartmentNameByPro($data->user_id) . '/' . $data->service->name . '/' . $data->id;
    //                 }
    //             })
    //             ->addColumn("budget_type", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->budget_type;
    //                 }
    //             })
    //             ->addColumn("user", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->user->name;
    //                 }
    //             })
    //             ->addColumn("created_at", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->created_at->format('d-m-Y');
    //                 }
    //             })
    //             ->addColumn("ref_num", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     if ($data->service_id == 1) {
    //                         return $data->material->dop ?? '';
    //                     } else {
    //                         return $data->services->dop_ref_no ?? '';
    //                     }
    //                 }
    //             })
    //             ->addColumn("budgetary_provision", function ($data) {
    //                 $item = null;

    //                 if ($data->service_id == 1 && !empty($data->material)) {
    //                     $item = $data->material;
    //                 } elseif (!empty($data->services)) {
    //                     $item = $data->services;
    //                 }

    //                 if ($item) {
    //                     $approved = !empty($item->approved_budget);
    //                     $additional = !empty($item->add_budget);

    //                     if ($approved && $additional) {
    //                         return 'Approved + Additional';
    //                     } elseif ($approved) {
    //                         return 'Approved';
    //                     } elseif ($additional) {
    //                         return 'Additional';
    //                     }
    //                 }

    //                 return 'Approved';
    //             })
    //             ->addColumn("proposal_type", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->proposal_type;
    //                 }
    //             })

    //             ->addColumn("service", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->service->name;
    //                 }
    //             })
    //             ->addColumn("fiscal_year", function ($data) {
    //                 if ($data->delete_draft == 0) {
    //                     return $data->fiscal_year;
    //                 }
    //             })

    //             ->addColumn("action", function ($data) use ($user) {
    //                 $button = "";
    //                 if ($user->can("edit_NV")) {
    //                     if ($data->delete_draft == 0) {
    //                         if ($user->role_id == 9) {
    //                             if ($data->service_id == 1) {
    //                                 if (!empty($data->material)) {
    //                                     if ($data->material->draft == 0) {
    //                                         $button =
    //                                             '<a href="/admin/nv_material/create/' .
    //                                             $data->id . '/' . $data->company_id .
    //                                             '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>' .
    //                                             '<a  href="/admin/nv_material/preview/' .
    //                                             $data->id .
    //                                             '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
    //                                             '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                             '<i class="fas fa-print text-info"></i>' . '</a>';
    //                                         // '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
    //                                         //     '<i class="fa fa-download text-info"></i>'.'</a>';
    //                                     } elseif ($data->material->draft == 1) {
    //                                         $button =  '<a  href="/admin/nv_material/preview/' .
    //                                             $data->id .
    //                                             '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
    //                                             '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                             '<i class="fas fa-print text-info"></i>' . '</a>' .
    //                                             '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
    //                                             '<i class="fa fa-download text-info"></i>' . '</a>';
    //                                     }
    //                                 } else {
    //                                     $button =
    //                                         '<a href="/admin/nv_material/create/' .
    //                                         $data->id . '/' . $data->company_id .
    //                                         '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>';
    //                                 }
    //                             } else {
    //                                 if (!empty($data->services)) {
    //                                     if ($data->services->draft == 0) {
    //                                         $button =
    //                                             '<a href="/admin/nv_service/create/' .
    //                                             $data->id . '/' . $data->company_id .
    //                                             '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Service"><i class="fas fa-edit text-info"></i></a>' .
    //                                             '<a href="/admin/nv_service/preview/' .
    //                                             $data->id .
    //                                             '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

    //                                             '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                             '<i class="fas fa-print text-info"></i>' . '</a>';
    //                                     } elseif ($data->services->draft == 1) {
    //                                         $button = '<a href="/admin/nv_service/preview/' .
    //                                             $data->id .
    //                                             '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

    //                                             '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                             '<i class="fas fa-print text-info"></i>' . '</a>';
    //                                     }
    //                                 } else {
    //                                     $button =
    //                                         '<a href="/admin/nv_service/create/' .
    //                                         $data->id . '/' . $data->company_id .
    //                                         '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Service"><i class="fas fa-edit text-info"></i></a>';
    //                                 }
    //                             }
    //                         } elseif ($user->role_id == 11) {
    //                             if ($data->service_id == 1) {
    //                                 if (!empty($data->material)) {
    //                                     $button = '<a  href="/admin/nv_material/preview/' .
    //                                         $data->id .
    //                                         '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .

    //                                         '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                         '<i class="fas fa-print text-info"></i>' .
    //                                         '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
    //                                         '<i class="fa fa-download text-info"></i>' . '</a>';
    //                                 } else {
    //                                     $button = '<a  href="#' .
    //                                         $data->id .
    //                                         '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>';
    //                                 }
    //                             } else {
    //                                 if (!empty($data->services)) {
    //                                     $button = '<a href="/admin/nv_service/preview/' .
    //                                         $data->id .
    //                                         '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

    //                                         '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
    //                                         '<i class="fas fa-print text-info"></i>' . '</a>';
    //                                 } else {
    //                                     $button = '<a href="#' .
    //                                         $data->id .
    //                                         '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>';
    //                                 }
    //                             }
    //                         } elseif ($user->role_id == 1) {
    //                             if ($data->service_id == 1) {

    //                                 $button = '<a  href="/admin/nv_material/preview/' .
    //                                     $data->id .
    //                                     '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>';
    //                             } else {

    //                                 $button = '<a href="/admin/nv_service/preview/' .
    //                                     $data->id .
    //                                     '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>';
    //                             }
    //                         }
    //                     }
    //                 }

    //                 return $button;
    //             })


    //             ->addIndexColumn()

    //             ->rawColumns(["action", "Edit_NV", "proposal_No", "service", "user", "ref_num"])
    //             ->make(true, null, ['DT_RowIndex' => 1]);



    //         return $needvalidation;
    //     }
    //     if ($user->role_id == 9) {
    //         $deleteNV = NeedValidation::where('delete_draft', 1)->where('user_id', $user->id)->count();

    //         return view("admin.NV.list", compact("totalNV", "approvedNV", "rejectedNV", "pendingNV", "deleteNV"));
    //     }

    //     // $totalNV = NeedValidation::select('*')->get()->count();
    //     return view("admin.NV.list", compact("totalNV", "approvedNV", "rejectedNV", "pendingNV"));
    // }

    public function NV_list(Request $request)
    {
        $user = \Auth()->user();
        $user_id = [];
        $nv1 = array();
        if ($user->role_id == 1) {
            $nv1 = NeedValidation::with('division', 'service')->where('delete_draft', 0)
                ->orderBy('id', 'desc')
                ->get();
            $totalId = NeedValidation::where('delete_draft', 0)->pluck('id');


            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->get();

            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
            $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();

            $rejectedNV = $latestData->filter(function ($data) {
                return $data->is_reject == 1;
            })->count();

            $pendingNV = $latestData->filter(function ($data) {
                return in_array($data->ceo_status, [0])
                    && in_array($data->is_reject, [0]);
            })->count();
        } elseif ($user->role_id == 9) {
            $nv1 = NeedValidation::with('division', 'service')
                ->orderBy('id', 'desc')
                ->where("user_id", $user->id)->where('delete_draft', 0)
                ->get();

            $totalId = NeedValidation::where("user_id", $user->id)->where('delete_draft', 0)->pluck('id');
            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $approvedNV = $latestData->where('ceo_status', 1)->count();
            $rejectedNV = $latestData->filter(function ($data) {
                return $data->is_reject == 1;
            })->count();

            $pendingNV = $latestData->filter(function ($data) {
                return in_array($data->ceo_status, [0])
                    && in_array($data->is_reject, [0]);
            })->count();
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
        } else {

            $user = auth()->user();

            /* ======================================================
            | STEP 1: Employee + Department IDs
            ====================================================== */
            $employee = Employee::where('user_id', $user->id)->first();

            $departmentIds = array_map('intval', explode(',', $employee->department_id));

            $departments = Department::whereIn('id', $departmentIds)
                ->where('status', 1)
                ->get();

            /* ======================================================
            | STEP 2: Employees of same departments
            ====================================================== */
            $user_id = Employee::where(function ($q) use ($departmentIds) {
                foreach ($departmentIds as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, department_id)", [$id]);
                }
            })->pluck('user_id')->unique()->toArray();

            /* ======================================================
            | STEP 3: Init
            ====================================================== */
            $nv1 = collect();
            $totalNV = 0;
            $approvedNV = 0;
            $rejectedNV = 0;
            $pendingNV = 0;

            /* ======================================================
            | STEP 4: Reviewer Logic (Per Department — stays in loop)
            ====================================================== */

            foreach ($departments as $department) {

                $depId = $department->id;

                $reviewerRoles = [
                    'rv1' => $department->dep_rew1,
                    'rv2' => $department->dep_rew2,
                    'rv3' => $department->dep_rew3,
                    'rv4' => $department->dep_rew4,
                ];

                foreach ($reviewerRoles as $role => $reviewerId) {

                    if (empty($reviewerId) || $reviewerId != $user->id) {
                        continue;
                    }

                    $curr = $role . '_status';

                    $query = Nvsericestatus::query()
                        ->where($curr, '!=', 2);

                    if ($department->dep_hod != $user->id) {
                        $query->where('hod_status', 0);
                    }

                    $nvIds = $query->pluck('nv_id');

                    $records = NeedValidation::with(['division', 'service'])
                        ->where('delete_draft', 0)
                        ->where('department_id', $depId)
                        ->whereIn('id', $nvIds);

                    // Reviewer-wise date filter
                    if ($role == 'rv1' && !empty($department->dep_rew1_added_at)) {
                        $records->where('created_at', '>=', $department->dep_rew1_added_at);
                    }

                    if ($role == 'rv2' && !empty($department->dep_rew2_added_at)) {
                        $records->where('created_at', '>=', $department->dep_rew2_added_at);
                    }

                    if ($role == 'rv3' && !empty($department->dep_rew3_added_at)) {
                        $records->where('created_at', '>=', $department->dep_rew3_added_at);
                    }

                    if ($role == 'rv4' && !empty($department->dep_rew4_added_at)) {
                        $records->where('created_at', '>=', $department->dep_rew4_added_at);
                    }

                    $records = $records->get();

                    $recordNvIds = $records->pluck('id');

                    $nv1 = $nv1->merge($records);

                    $totalNV += Nvsericestatus::whereIn('nv_id', $recordNvIds)
                        ->where($curr, 1)
                        ->count();

                    $pendingNV += Nvsericestatus::whereIn('nv_id', $recordNvIds)
                        ->where($curr, 0)
                        ->count();

                    $rejectedNV += Nvsericestatus::whereIn('nv_id', $recordNvIds)
                        ->where($curr, 2)
                        ->count();
                }
            }

            $nv1 = $nv1->unique('id')->values();

            /* ======================================================
            | STEP 5: HOD Logic (OUTSIDE loop — runs once)
            ====================================================== */
            $hodDepartments = Department::where('dep_hod', $user->id)
                    ->where('status', 1)
                    ->pluck('id');

                if ($hodDepartments->isNotEmpty()) {

                    $nvTable     = (new NeedValidation)->getTable();
                    $deptTable   = (new Department)->getTable();
                    $statusTable = (new Nvsericestatus)->getTable();

                    $baseQuery = Nvsericestatus::query()
                        ->join("{$nvTable} as nv", "{$statusTable}.nv_id", '=', 'nv.id')
                        ->join("{$deptTable} as d", 'nv.department_id', '=', 'd.id')
                        ->whereIn('nv.department_id', $hodDepartments)
                        ->where('nv.delete_draft', 0)
                        ->where("{$statusTable}.draft", 1)

                        // Reviewer 1
                        ->where(function ($q) use ($statusTable) {

                            $q->whereNull("d.dep_rew1")
                                ->orWhere("d.dep_rew1", '')
                                ->orWhere(function ($sub) {
                                    $sub->whereNotNull('d.dep_rew1_added_at')
                                        ->whereColumn('nv.created_at', '<', 'd.dep_rew1_added_at');
                                })
                                ->orWhere("{$statusTable}.rv1_status", 1);
                        })

                        // Reviewer 2
                        ->where(function ($q) use ($statusTable) {

                            $q->whereNull("d.dep_rew2")
                                ->orWhere("d.dep_rew2", '')
                                ->orWhere(function ($sub) {
                                    $sub->whereNotNull('d.dep_rew2_added_at')
                                        ->whereColumn('nv.created_at', '<', 'd.dep_rew2_added_at');
                                })
                                ->orWhere("{$statusTable}.rv2_status", 1);
                        })

                        // Reviewer 3
                        ->where(function ($q) use ($statusTable) {

                            $q->whereNull("d.dep_rew3")
                                ->orWhere("d.dep_rew3", '')
                                ->orWhere(function ($sub) {
                                    $sub->whereNotNull('d.dep_rew3_added_at')
                                        ->whereColumn('nv.created_at', '<', 'd.dep_rew3_added_at');
                                })
                                ->orWhere("{$statusTable}.rv3_status", 1);
                        })

                        // Reviewer 4
                        ->where(function ($q) use ($statusTable) {

                            $q->whereNull("d.dep_rew4")
                                ->orWhere("d.dep_rew4", '')
                                ->orWhere(function ($sub) {
                                    $sub->whereNotNull('d.dep_rew4_added_at')
                                        ->whereColumn('nv.created_at', '<', 'd.dep_rew4_added_at');
                                })
                                ->orWhere("{$statusTable}.rv4_status", 1);
                        });

                    $pendingNV += (clone $baseQuery)
                        ->where("{$statusTable}.hod_status", 0)
                        ->distinct()
                        ->count('nv.id');

                    $totalNV += (clone $baseQuery)
                        ->where("{$statusTable}.hod_status", 1)
                        ->distinct()
                        ->count('nv.id');

                    $rejectedNV += Nvsericestatus::whereIn('nv_id', function ($q) use ($hodDepartments, $nvTable) {
                            $q->select('id')
                                ->from($nvTable)
                                ->whereIn('department_id', $hodDepartments)
                                ->where('delete_draft', 0);
                        })
                        ->where('hod_status', 2)
                        ->count();

                    $listIds = (clone $baseQuery)
                        ->where("{$statusTable}.hod_status", '!=', 2)
                        ->distinct()
                        ->pluck("{$statusTable}.nv_id");

                    $hodRecords = NeedValidation::with(['division', 'service'])
                        ->whereIn('id', $listIds)
                        ->orderBy('id', 'desc')
                        ->get();

                    $nv1 = $nv1->merge($hodRecords);

                    $approvedNV += DB::table('capex_workflows_status as cws')
                        ->join(DB::raw('(
                            SELECT nv_id, MAX(workflow_serial) max_serial
                            FROM capex_workflows_status
                            GROUP BY nv_id
                        ) latest'), function ($join) {
                            $join->on('cws.nv_id', '=', 'latest.nv_id')
                                ->on('cws.workflow_serial', '=', 'latest.max_serial');
                        })
                        ->whereIn('cws.nv_id', $listIds)
                        ->where('cws.nv_stage_status', 1)
                        ->count();
                }

            /* ======================================================
            | STEP 6: Group CIO Logic (OUTSIDE loop — runs once)
            ====================================================== */
            $groupCioDepartments = Department::where('group_cio', $user->id)
                ->where('status', 1)
                ->pluck('id');

            if ($groupCioDepartments->isNotEmpty()) {

                $groupCioRecords = NeedValidation::with(['division', 'service'])
                    ->join('nvservicestatus as nvs', 'nvs.nv_id', '=', 'needvalidations.id')
                    ->whereIn('needvalidations.department_id', $groupCioDepartments)
                    ->where('nvs.hod_status', 1)
                    ->select('needvalidations.*', 'nvs.is_reject')
                    ->orderBy('needvalidations.id', 'desc')
                    ->get();

                /* ✅ merge instead of overwrite */
                $nv1 = $nv1->merge($groupCioRecords);

                $nvIds = $groupCioRecords->pluck('id');

                /* ✅ += instead of = */
                $pendingNV += Nvsericestatus::whereIn('nv_id', $nvIds)
                    ->where('draft', 1)
                    ->where('hod_status', 1)
                    ->where('groupcio_status', 0)
                    ->count();

                $totalNV += Nvsericestatus::whereIn('nv_id', $nvIds)
                    ->where('groupcio_status', 1)
                    ->count();

                $rejectedNV += Nvsericestatus::whereIn('nv_id', $nvIds)
                    ->where('groupcio_status', 2)
                    ->count();

                $approvedNV += DB::table('capex_workflows_status as cws')
                    ->join(DB::raw('(
                        SELECT nv_id, MAX(workflow_serial) max_serial
                        FROM capex_workflows_status
                        GROUP BY nv_id
                    ) latest'), function ($join) {
                        $join->on('cws.nv_id', '=', 'latest.nv_id')
                            ->on('cws.workflow_serial', '=', 'latest.max_serial');
                    })
                    ->whereIn('cws.nv_id', $nvIds)
                    ->where('cws.nv_stage_status', 1)
                    ->count();
            }

            /* ======================================================
            | STEP 7: Deduplicate (same NV may appear under multiple roles)
            ====================================================== */
            $nv1 = $nv1->unique('id');
            
            $hasWorkflow = DB::table('capex_workflows_status')
                ->where('workflow_user_id', $user->id)
                ->exists();

            if ($hasWorkflow) {
                // Step 1: Fetch workflows assigned to the current user ONCE
                $userWorkflows = DB::table('capex_workflows_status')
                    ->where('workflow_user_id', $user->id)
                    ->where('transfer_to_nominee1', 0)
                    ->get();

                if ($userWorkflows->isNotEmpty()) {
                    
                    // Step 2: Eager Load dependencies.
                    // Extract all nv_ids to fetch their full workflow history in ONE query.
                    $nvIds = $userWorkflows->pluck('nv_id')->unique()->toArray();

                    // Fetch ALL steps for these NVs and group them by nv_id in memory.
                    $allWorkflowSteps = DB::table('capex_workflows_status')
                        ->whereIn('nv_id', $nvIds)
                        ->get()
                        ->groupBy('nv_id');

                    $capexNvIds = [];
                    $opexNvIds = [];

                    // Step 3: Loop through the user's assigned tasks (Single Loop)
                    foreach ($userWorkflows as $nvs) {
                        $nv_id = $nvs->nv_id;
                        $workflow_serial = $nvs->workflow_serial;
                        $budget_type = $nvs->nv_budget_type;
                        $isApprover = strtolower($nvs->reviewer_name) === 'approver';

                        // Retrieve pre-fetched steps for this specific nv_id
                        $currentNvSteps = $allWorkflowSteps[$nv_id] ?? collect([]);
                        
                        $addToResult = false;

                        // --- Logic translated to use Collections instead of DB queries ---

                        if ($isApprover) {
                            
                            // Check for other reviewers in the same serial
                            $reviewers = $currentNvSteps->filter(function ($item) use ($workflow_serial) {
                                return $item->workflow_serial == $workflow_serial 
                                    && strtolower($item->reviewer_name) !== 'approver';
                            });

                            if ($reviewers->isNotEmpty()) {
                                // If reviewers exist, check if all have status 1
                                $addToResult = $reviewers->every(fn($rev) => $rev->nv_stage_status == 1);
                            } elseif ($workflow_serial == 1) {
                                $addToResult = true;
                            } else {
                                // Logic for serial > 1
                                $prevSerial = $workflow_serial - 1;

                                // Handle specific Department Transfer Logic
                                if ($nvs->department_id == 19) {
                                    $transfer_to_nom1 = $currentNvSteps->contains(function ($item) {
                                        return $item->department_id == 16 && $item->transfer_to_nominee1 == 1;
                                    });
                                    if ($transfer_to_nom1) {
                                        $prevSerial = $workflow_serial - 2;
                                    }
                                }

                                // Check previous approver status
                                $addToResult = $currentNvSteps->contains(function ($item) use ($prevSerial) {
                                    return $item->workflow_serial == $prevSerial 
                                        && strtolower($item->reviewer_name) === 'approver' 
                                        && $item->nv_stage_status == 1;
                                });
                            }
                        } else {

                            // Not Approver Logic
                            if ($workflow_serial == 1) {
                                $addToResult = true;
                            } else {
                                $prevSerial = $workflow_serial - 1;

                                if ($nvs->department_id == 19) {
                                    $transfer_to_nom1 = $currentNvSteps->contains(function ($item) {
                                        return $item->department_id == 16 && $item->transfer_to_nominee1 == 1;
                                    });
                                    if ($transfer_to_nom1) {
                                        $prevSerial = $workflow_serial - 2;
                                    }
                                }

                                $addToResult = $currentNvSteps->contains(function ($item) use ($prevSerial) {
                                    return $item->workflow_serial == $prevSerial 
                                        && strtolower($item->reviewer_name) === 'approver' 
                                        && $item->nv_stage_status == 1;
                                });
                            }
                        }

                        // --- End of Logic ---

                        if ($addToResult) {
                            if ($budget_type === 'CAPEX') $capexNvIds[] = $nv_id;
                            elseif ($budget_type === 'OPEX') $opexNvIds[] = $nv_id;
                        }
                    }

                    // Step 4: Prepare Final Data
                    $capexNvIds = array_unique($capexNvIds);
                    $opexNvIds = array_unique($opexNvIds);

                    $capexData = NeedValidation::with("division", "service")
                        ->whereIn("id", $capexNvIds)
                        ->where("budget_type", 'CAPEX')
                        ->orderBy("id", "desc")
                        ->get();
                        
                    $opexData = NeedValidation::with("division", "service")
                        ->whereIn("id", $opexNvIds)
                        ->where("budget_type", 'OPEX')
                        ->orderBy("id", "desc")
                        ->get();
                        
                    $nv1 = $capexData->merge($opexData);
                    // Step 5: Calculate Counts (Optimized to run once)
                    $nvIdsForCount = $nv1->pluck('id');

                    $approvedNV = DB::table('capex_workflows_status as cws')
                        ->join(
                            DB::raw('(SELECT nv_id, MAX(workflow_serial) as max_serial
                                                        FROM capex_workflows_status
                                                        GROUP BY nv_id) as latest'),
                            function ($join) {
                                $join->on('cws.nv_id', '=', 'latest.nv_id')
                                    ->on('cws.workflow_serial', '=', 'latest.max_serial');
                            }
                        )
                        ->where('cws.nv_stage_status', 1)
                        ->whereIn('cws.nv_id', $nvIdsForCount)
                        ->count();

                    $rejectedNV = DB::table('capex_workflows_status')
                        ->where('workflow_user_id', $user->id)
                        ->where('nv_stage_status', 2)
                        ->whereIn('nv_id', $nvIdsForCount)
                        ->count();

                    $pendingNV = DB::table('capex_workflows_status')
                        ->where('workflow_user_id', $user->id)
                        ->where('nv_stage_status', 0)
                        ->whereIn('nv_id', $nvIdsForCount)
                        ->count();

                    $totalNV = DB::table('capex_workflows_status')
                        ->where('workflow_user_id', $user->id)
                        ->where('nv_stage_status', 1)
                        ->whereIn('nv_id', $nvIdsForCount)
                        ->count();
                        
                    // Your variables ($nv1, $approvedNV, etc.) are now ready for use
                }
            }
        }
        if($user->role_id == 1){
            $nv1 =  NeedValidation::join(
                        'nvservicestatus',
                        'nvservicestatus.nv_id',
                        '=',
                        'needvalidations.id'
                    )
                    ->where('nvservicestatus.draft','=', 1)
                    ->where('nvservicestatus.is_reject','=', 0)
                    ->where('needvalidations.delete_draft','=', 0)
                    ->orderBy('needvalidations.id', 'desc')
                    ->select(
                        'needvalidations.*'
                    )->get();
                    
        }else{
                $nv1;
        }
        if ($request->ajax()) {

            $needvalidation = datatables()
                ->of(
                    $nv1->filter(function ($data) {
                        return $data->delete_draft == 0 && $data->is_reject == 0;
                    })
                )
                ->addColumn("proposal_No", function ($data) {
                    if ($data->delete_draft == 0) {
                        return 'NV' . '/' . $data->budget_type . '/' . $data->fiscal_year . '/' . getDepartmentNameByPro($data->user_id) . '/' . $data->service->name . '/' . $data->id;
                    }
                })
                ->addColumn("budget_type", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->budget_type;
                    }
                })
                ->addColumn("user", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->user->name;
                    }
                })
                ->addColumn("created_at", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->created_at->format('d-m-Y');
                    }
                })
                ->addColumn("ref_num", function ($data) {
                    if ($data->delete_draft == 0) {
                        if ($data->service_id == 1) {
                            return $data->material->dop ?? '';
                        } else {
                            return $data->services->dop_ref_no ?? '';
                        }
                    }
                })
                ->addColumn("budgetary_provision", function ($data) {
                    $item = null;

                    if ($data->service_id == 1 && !empty($data->material)) {
                        $item = $data->material;
                    } elseif (!empty($data->services)) {
                        $item = $data->services;
                    }

                    if ($item) {
                        $approved = !empty($item->approved_budget);
                        $additional = !empty($item->add_budget);

                        if ($approved && $additional) {
                            return 'Approved + Additional';
                        } elseif ($approved) {
                            return 'Approved';
                        } elseif ($additional) {
                            return 'Additional';
                        }
                    }

                    return 'Approved';
                })
                ->addColumn("proposal_type", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->proposal_type;
                    }
                })

                ->addColumn("service", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->service->name;
                    }
                })
                ->addColumn("fiscal_year", function ($data) {
                    if ($data->delete_draft == 0) {
                        return $data->fiscal_year;
                    }
                })

                ->addColumn("action", function ($data) use ($user) {
                    $button = "";
                    if ($user->can("edit_NV")) {
                        if ($data->delete_draft == 0) {
                            if ($user->role_id == 9) {
                                if ($data->service_id == 1) {
                                    if (!empty($data->material)) {
                                        if ($data->material->draft == 0) {
                                            $button =
                                                '<a href="/admin/nv_material/create/' .
                                                $data->id . '/' . $data->company_id .
                                                '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>' .
                                                '<a  href="/admin/nv_material/preview/' .
                                                $data->id .
                                                '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
                                                '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                                '<i class="fas fa-print text-info"></i>' . '</a>';
                                            // '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
                                            //     '<i class="fa fa-download text-info"></i>'.'</a>';
                                        } elseif ($data->material->draft == 1) {
                                            $button =  '<a  href="/admin/nv_material/preview/' .
                                                $data->id .
                                                '" class="btn btn-sm btn-clean btn-icon" title="Preview NV Material"><i class="fa fa-eye text-info"></i></a>' .
                                                '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                                '<i class="fas fa-print text-info"></i>' . '</a>' .
                                                '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
                                                '<i class="fa fa-download text-info"></i>' . '</a>';
                                        }
                                    } else {
                                        $button =
                                            '<a href="/admin/nv_material/create/' .
                                            $data->id . '/' . $data->company_id .
                                            '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Material"><i class="fas fa-edit text-info"></i></a>';
                                    }
                                } else {
                                    if (!empty($data->services)) {
                                        if ($data->services->draft == 0) {
                                            $button =
                                                '<a href="/admin/nv_service/create/' .
                                                $data->id . '/' . $data->company_id .
                                                '" class="btn btn-sm btn-clean btn-icon" title="Edit NV Service"><i class="fas fa-edit text-info"></i></a>' .
                                                '<a href="/admin/nv_service/preview/' .
                                                $data->id .
                                                '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

                                                '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                                '<i class="fas fa-print text-info"></i>' . '</a>';
                                        } elseif ($data->services->draft == 1) {
                                            $button = '<a href="/admin/nv_service/preview/' .
                                                $data->id .
                                                '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

                                                '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                                '<i class="fas fa-print text-info"></i>' . '</a>';
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

                                            '<a target="_blank" href="' . route('download.nv.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                            '<i class="fas fa-print text-info"></i>' .
                                            '<a target="_blank" href="' . route('download.nvmaterialboq.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV BOQ">' .
                                            '<i class="fa fa-download text-info"></i>' . '</a>';
                                    } else {
                                        $button = '<a  href="#' .
                                            $data->id .
                                            '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>';
                                    }
                                } else {
                                    if (!empty($data->services)) {
                                        $button = '<a href="/admin/nv_service/preview/' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon" title="Preview NV Service"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>' .

                                            '<a target="_blank" href="' . route('download.nvservice.pdf', ['id' =>  $data->id, 'userId' => $data->company_id]) . '" class="btn btn-sm btn-clean btn-icon" title="Download NV PDF">' .
                                            '<i class="fas fa-print text-info"></i>' . '</a>';
                                    } else {
                                        $button = '<a href="#' .
                                            $data->id .
                                            '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>';
                                    }
                                }
                            } elseif ($user->role_id == 1) {
                                if ($data->service_id == 1) {

                                    $button = '<a  href="/admin/nv_material/preview/' .
                                        $data->id .
                                        '" class="btn btn-sm btn-clean btn-icon"><i class="fa fa-eye text-info"></i></a>';
                                } else {

                                    $button = '<a href="/admin/nv_service/preview/' .
                                        $data->id .
                                        '"class="btn btn-sm btn-clean btn-icon"><i class="fas fa-eye text-info" onclick="clickme()"></i></a>';
                                }
                            }
                        }
                    }

                    return $button;
                })


                ->addIndexColumn()

                ->rawColumns(["action", "Edit_NV", "proposal_No", "service", "user", "ref_num"])
                ->make(true, null, ['DT_RowIndex' => 1]);



            return $needvalidation;
        }
        if ($user->role_id == 9) {
            $deleteNV = NeedValidation::where('delete_draft', 1)->where('user_id', $user->id)->count();

            return view("admin.NV.list", compact("totalNV", "approvedNV", "rejectedNV", "pendingNV", "deleteNV"));
        }

        // $totalNV = NeedValidation::select('*')->get()->count();
        return view("admin.NV.list", compact("totalNV", "approvedNV", "rejectedNV", "pendingNV"));
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
        // $departments = Department::select("id", "name")
        //     ->where("status", 1)->where('id', $emp->department_id)
        //     ->get();
        
        $services = Service::select("id", "name")
            ->where("status", 1)
            ->get();
        $capex = Capex::where("department_id", $user->department_id)
            ->first();
        $opex = Opex::where("department_id", $user->department_id)
            ->first();

        //For The Yamini Account
         $emp = Employee::select("id", "name", "division_id", "department_id")
                ->where("status", 1)
                ->where("user_id", $user->id)
                ->first();

            $departments = collect();

            if (!empty($emp->department_id)) {
                $departmentIds = array_map('intval', explode(',', $emp->department_id));

                $departments = Department::whereIn('id', $departmentIds)
                    ->select('id', 'name')
                    ->get();
            }

            $currentDate = Carbon::now();

            if ($currentDate->month >= 4) {
                $financialYearStart = Carbon::create($currentDate->year, 4, 1);
            } else {
                $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
            }
            $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
            $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');

            $nextFinancialYearStart = $financialYearStart->copy()->addYear();
            $nextFinancialYearEnd = $nextFinancialYearStart->copy()->addYear()->subDay();
            $nextFinancialYear = $nextFinancialYearStart->format('Y') . '-' . $nextFinancialYearEnd->format('y');

            $nextToNextFinancialYearStart = $nextFinancialYearStart->copy()->addYear();
            $nextToNextFinancialYearEnd = $nextToNextFinancialYearStart->copy()->addYear()->subDay();
            $nextToNextFinancialYear = $nextToNextFinancialYearStart->format('Y') . '-' . $nextToNextFinancialYearEnd->format('y');
        
        return view("admin.NV.create", compact("divisions", "services", "departments","capex","opex", "currentFinancialYear", "nextFinancialYear", "nextToNextFinancialYear"));
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
        $dept_name = getDepartmentName($request->department_id);

        try {
            $request_input = $request->except("_token");

            /* -------------------- VALIDATION -------------------- */
            $rules = [
                "company_name" => "required",
                "department_id" => "required",
                "budget_type" => "required",
                "budget_prov" => "required",
                "prop_type" => "required",
                "nv_type" => "required",
                "fiscal_year" => "required",
            ];

            $messages = [
                "company_name.required" => "Please enter company name",
                "budget_type.required" => "Please enter budget type",
                "budget_prov.required" => "Please enter budgetary provision",
                "prop_type.required" => "Please enter proposal type",
                "nv_type.required" => "Please enter NV type",
                "fiscal_year.required" => "Please enter fiscal year",
            ];

            $validator = Validator::make($request_input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    "result" => "error",
                    "msg" => $validator->errors()->toArray()
                ]);
            }

            /* -------------------- BUDGET CHECK -------------------- */
            $budget = null;

            if ($request_input["budget_prov"] === "Approved") {

                if ($request_input["budget_type"] === "CAPEX") {

                    $budgetRow = CapexBudget::where('department_id', $request_input["department_id"])
                        ->where('fiscal_year', $request_input["fiscal_year"])
                        ->first();

                    if (!$budgetRow || empty($budgetRow->revised_budget) || $budgetRow->revised_budget <= 0) {
                        return response()->json([
                            "result" => "error",
                            "msg" => "Approved CAPEX budget is not available for the selected department and fiscal year."
                        ]);
                    }

                    $budget = $budgetRow->revised_budget;
                }

                if ($request_input["budget_type"] === "OPEX") {

                    $budgetRow = OpexBudget::where('department_id', $request_input["department_id"])
                        ->where('fiscal_year', $request_input["fiscal_year"])
                        ->first();

                    if (!$budgetRow || empty($budgetRow->revised_budget) || $budgetRow->revised_budget <= 0) {
                        return response()->json([
                            "result" => "error",
                            "msg" => "Approved OPEX budget is not available for the selected department and fiscal year."
                        ]);
                    }

                    $budget = $budgetRow->revised_budget;
                }
            }

            /* -------------------- CREATE NV -------------------- */
            $needvalidation = NeedValidation::insertGetId([
                "company_id" => $request_input["company_name"],
                "department_id" => $request_input["department_id"],
                "user_id" => \Auth::user()->id,
                "budget_type" => $request_input["budget_type"],
                "budgetary_provision" => $request_input["budget_prov"],
                "proposal_type" => $request_input["prop_type"],
                "service_id" => $request_input["nv_type"],
                "fiscal_year" => $request_input["fiscal_year"],
            ]);

            /* -------------------- INSERT BUDGET LOG -------------------- */
            if ($budget !== null) {

                $budgetExists = budget::where('dept_id', $request_input["department_id"])
                    ->where('budget_type', $request_input["budget_type"])
                    ->where('fiscal_year', $request_input["fiscal_year"])
                    ->exists();

                if (!$budgetExists) {
                    budget::insert([
                        'dept_id'     => $request_input["department_id"],
                        'budget_type' => $request_input["budget_type"],
                        'fiscal_year' => $request_input["fiscal_year"],
                        'budget_avl'  => $budget,
                        'nv_id'       => $needvalidation,
                    ]);
                }
            }

            /* -------------------- SUCCESS RESPONSE -------------------- */
            return response()->json([
                "result"   => "success",
                "msg"      => "NV created successfully",
                "nvID"     => $needvalidation,
                "dept_name"=> $dept_name
            ]);

        } catch (\Exception $e) {

            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json([
                "result" => "failure",
                "msg" => "Something went wrong while creating NV. Please try again."
            ]);
        }
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

    public function show_list()
    {
     
       $user = \Auth::user();

       $latest = NeedValidation::where('delete_draft',1)->where('user_id',$user->id)->get();
    
       return view("admin.delete_list",compact("latest"));
    } 

    public function uploadDWGFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimetypes:image/vnd.dwg,application/acad,application/x-acad,application/autocad_dwg,application/dwg|max:5120', 
            // Max file size 5MB (adjust as needed)
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            return response()->json(['message' => '.dwg File uploaded successfully', 'file' => $filename]);
        }

        return response()->json(['error' => '.dwg File upload failed'], 400);
    }
}

