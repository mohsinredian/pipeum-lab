<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use App\Models\NVService;
use App\Models\NeedValidation;
use App\Models\Circle;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Nvsericestatus;
use App\Models\User;
use App\Models\Workflow;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\NVMaterial;
use App\Models\InventorySerialNumberMapping;
use Silber\Bouncer\Database\Role;
use App\Models\Signaturelog;
use App\Models\Service;
use DB;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\OpexWorkflow;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put("active", "dashboard");
            return $next($request);
        });
    }

 public function dashboard(Request $request)
 {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;

        $total_processed = $request->total_processed;
        $total = $request->total;
        $rejected = $request->rejected;
        $approved = $request->approved;
        $pending = $request->pending ?? 0;

        $user = \Auth::user();
        $currentDate = Carbon::now();
        /*
        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }

        $currentFYStart = $financialYearStart->copy()->subYear();
        $currentFYEnd = $financialYearStart->copy()->subDay();
        $currentFinancialYear = $currentFYStart->format('Y') . '-' . $currentFYEnd->format('y');
	*/
	/*
	if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }

        $currentFYStart = $financialYearStart->copy();
        $currentFYEnd = $financialYearStart->copy()->addYear()->subDay();

        $currentFinancialYear = $currentFYStart->format('Y') . '-' . $currentFYEnd->format('y');	
	*/
	if ($currentDate->month >= 4) {
   		 $financialYearStart = Carbon::create($currentDate->year, 4, 1);
	} else {
   		 $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
	}
 
	$currentFYStart = $financialYearStart->copy()->subYear();       // 1 Apr 2025
	$currentFYEnd   = $financialYearStart->copy()->subDay();        // 31 Mar 2026
 
	$currentFinancialYear = $currentFYStart->format('Y') . '-' . $currentFYEnd->format('y'); // 2025-26
        
	$nextFYStart = $currentFYStart->copy()->addYear();
        $nextFYEnd = $nextFYStart->copy()->addYear()->subDay();
        $nextFinancialYear = $nextFYStart->format('Y') . '-' . $nextFYEnd->format('y');

        $nextToNextFYStart = $nextFYStart->copy()->addYear();
        $nextToNextFYEnd = $nextToNextFYStart->copy()->addYear()->subDay();
        $nextToNextFinancialYear = $nextToNextFYStart->format('Y') . '-' . $nextToNextFYEnd->format('y');

        $company = Division::select('id', 'name')->where('status', '1')->get();
        $dept_data_p = Department::where('status', 1)->paginate(12);
        $dept_data = Department::where('status', 1)->pluck('name');

        if ($company_id) {
            $Nvid = NeedValidation::where('company_id', $company_id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck("id");
        } elseif ($fiscal_year) {
            $Nvid = NeedValidation::where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->pluck("id");
        } else {
            $Nvid = NeedValidation::where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck("id");
        }
        
        $departmentNVCounts = [];
        foreach ($dept_data as $departmentName) {
            $department = Department::where('name', $departmentName)->where('status', 1)->first();
            if ($department) {
                $count = NVService::where('dept_id', $department->id)->whereIn('nv_id', $Nvid)->count() + NVMaterial::where('dept_id', $department->id)->whereIn('nv_id', $Nvid)->count();
                $departmentNVCounts[$departmentName] = $count;
            }
        }

        $departments_with_group_cio = [];
        $departments_without_group_cio = [];
        $all_departments = Department::where('status', 1)->get();
        foreach ($all_departments as $all_department) {
            if (!empty($all_department->group_cio)) { $departments_with_group_cio[] = $all_department->id; } 
            else { $departments_without_group_cio[] = $all_department->id; }
        }

        $workflowStages = Workflow::where('status', 1)->get();
        $capexApprovalCounts = [];
        foreach ($workflowStages as $workflowStage) {
            $count = DB::table('capex_workflows_status as current')
                ->where('current.department_id', $workflowStage->work_dep)->where('current.nv_budget_type', 'CAPEX')->where('current.reviewer_name', 'approver')->where('current.nv_stage_status', 0)->whereIn('current.nv_id', $Nvid)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('current.workflow_serial', 1)->whereExists(function ($exists) {
                            $exists->select(DB::raw(1))->from('capex_workflows_status as reviewer')->where(function ($sub) {
                                $sub->whereNotNull('current.service_id')->whereColumn('reviewer.service_id', 'current.service_id')->orWhere(function ($or) { $or->whereNull('current.service_id')->whereColumn('reviewer.material_id', 'current.material_id'); });
                            })->where('reviewer.workflow_serial', 1)->where('reviewer.reviewer_name', '!=', 'approver')->where('reviewer.nv_stage_status', 1);
                        });
                    })->orWhere(function ($q) {
                        $q->where('current.workflow_serial', '>', 1)->whereExists(function ($exists) {
                            $exists->select(DB::raw(1))->from('capex_workflows_status as prev')->where(function ($sub) {
                                $sub->whereNotNull('current.service_id')->whereColumn('prev.service_id', 'current.service_id')->orWhere(function ($or) { $or->whereNull('current.service_id')->whereColumn('prev.material_id', 'current.material_id'); });
                            })->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))->where('prev.reviewer_name', 'approver')->where('prev.nv_stage_status', 1);
                        });
                    });
                })->count();
            $capexApprovalCounts[$workflowStage->work_dep] = $count;
        }

        $opexWorkflowStages = OpexWorkflow::where('status', 1)->get();
        $opexApprovalCounts = [];
        foreach ($opexWorkflowStages as $workflowStage) {
            $count = DB::table('capex_workflows_status as current')
                ->where('current.department_id', $workflowStage->work_dep)->where('current.nv_budget_type', 'OPEX')->where('current.reviewer_name', 'approver')->where('current.nv_stage_status', 0)->whereIn('current.nv_id', $Nvid)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('current.workflow_serial', 1)->whereExists(function ($exists) {
                            $exists->select(DB::raw(1))->from('capex_workflows_status as reviewer')->where(function ($sub) {
                                $sub->whereNotNull('current.service_id')->whereColumn('reviewer.service_id', 'current.service_id')->orWhere(function ($or) { $or->whereNull('current.service_id')->whereColumn('reviewer.material_id', 'current.material_id'); });
                            })->where('reviewer.workflow_serial', 1)->where('reviewer.reviewer_name', '!=', 'approver')->where('reviewer.nv_stage_status', 1);
                        });
                    })->orWhere(function ($q) {
                        $q->where('current.workflow_serial', '>', 1)->whereExists(function ($exists) {
                            $exists->select(DB::raw(1))->from('capex_workflows_status as prev')->where(function ($sub) {
                                $sub->whereNotNull('current.service_id')->whereColumn('prev.service_id', 'current.service_id')->orWhere(function ($or) { $or->whereNull('current.service_id')->whereColumn('prev.material_id', 'current.material_id'); });
                            })->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))->where('prev.reviewer_name', 'approver')->where('prev.nv_stage_status', 1);
                        });
                    });
                })->count();
            $opexApprovalCounts[$workflowStage->work_dep] = $count;
        }

        // ==========================================
        // ADMIN ROLE (Role ID 1)
        // ==========================================
        if ($user->role_id == 1) {
                $totalId = NeedValidation::whereIn('id', $Nvid)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft', 1)->get();

                $approvedData = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id', $Nvid);
                $approvedNV = $approvedData->count();

                $totalData = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1);
                $totalNV = $totalData->count();

                $rejectedData = $latestData->filter(function ($data) {
                    return $data->is_reject == 1;
                });
                $rejectedNV = $rejectedData->count();

                $pendingData = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [0]) && in_array($data->is_reject, [0]);
                });
                $pendingNV = $pendingData->count();

                $totalAmount = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $pendingAmount = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $rejectedAmount = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $approvedAmount = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $totalId)
                    ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $nv_ids = NeedValidation::whereIn('id', $Nvid)->pluck("id");
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');

                if (!empty($total_processed)) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
                } elseif (!empty($total)) {
                    $nv_sm_data = $nv_sm_data->where('draft', 1);
                } elseif (!empty($approved)) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
                } elseif (!empty($rejected)) {
                    $nv_sm_data = $nv_sm_data->whereIn('nv_id', $rejectedData->pluck('nv_id'))->where('is_reject', 1);
                } else {
                    $nv_sm_data = $nv_sm_data->whereIn('nv_id', $pendingData->pluck('nv_id'))->where('is_reject', 0);
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('id', $Nvid)->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BRPLnv)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BRPLnv)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $rejectedAmountBRPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BRPLnv)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BRPLnv)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $approvedAmountBRPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                    ->sum('tbl_service.total_buget');

                $fileDataBRPL = Nvsericestatus::select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
                        DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")
                    )
                    ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                $workflowBRPLStages = Workflow::where('status', 1)->get();
                $capexBRPLApprovalCounts = [];
                foreach ($workflowBRPLStages as $workflowStage) {

                    $count = DB::table('capex_workflows_status as current')
                        ->where('current.department_id', $workflowStage->work_dep)
                        ->where('current.nv_budget_type', 'CAPEX')
                        ->where('current.reviewer_name', 'approver')
                        ->where('current.nv_stage_status', 0)
                        ->whereIn('current.nv_id', $BRPLnv)
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                // CASE 1: workflow_serial = 1
                                $q->where('current.workflow_serial', 1)
                                    ->whereExists(function ($exists) {
                                        $exists->select(DB::raw(1))
                                            ->from('capex_workflows_status as reviewer')
                                            ->where(function ($sub) {
                                                $sub->whereNotNull('current.service_id')
                                                    ->whereColumn('reviewer.service_id', 'current.service_id')
                                                    ->orWhere(function ($or) {
                                                        $or->whereNull('current.service_id')
                                                            ->whereColumn('reviewer.material_id', 'current.material_id');
                                                    });
                                            })
                                            ->where('reviewer.workflow_serial', 1)
                                            ->where('reviewer.reviewer_name', '!=', 'approver')
                                            ->where('reviewer.nv_stage_status', 1);
                                    });
                            })
                                ->orWhere(function ($q) {
                                    // CASE 2: workflow_serial > 1
                                    $q->where('current.workflow_serial', '>', 1)
                                        ->whereExists(function ($exists) {
                                            $exists->select(DB::raw(1))
                                                ->from('capex_workflows_status as prev')
                                                ->where(function ($sub) {
                                                    $sub->whereNotNull('current.service_id')
                                                        ->whereColumn('prev.service_id', 'current.service_id')
                                                        ->orWhere(function ($or) {
                                                            $or->whereNull('current.service_id')
                                                                ->whereColumn('prev.material_id', 'current.material_id');
                                                        });
                                                })
                                                ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
                                                ->where('prev.reviewer_name', 'approver')
                                                ->where('prev.nv_stage_status', 1);
                                        });
                                });
                        })
                        ->count();

                    $capexBRPLApprovalCounts[$workflowStage->work_dep] = $count;
                }

                $opexWorkflowBRPLStages = OpexWorkflow::where('status', 1)->get();
                $opexBRPLApprovalCounts = [];
                foreach ($opexWorkflowBRPLStages as $workflowStage) {

                    $count = DB::table('capex_workflows_status as current')
                        ->where('current.department_id', $workflowStage->work_dep)
                        ->where('current.nv_budget_type', 'OPEX')
                        ->where('current.reviewer_name', 'approver')
                        ->where('current.nv_stage_status', 0)
                        ->whereIn('current.nv_id', $BRPLnv)
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                // CASE 1: workflow_serial = 1
                                $q->where('current.workflow_serial', 1)
                                    ->whereExists(function ($exists) {
                                        $exists->select(DB::raw(1))
                                            ->from('capex_workflows_status as reviewer')
                                            ->where(function ($sub) {
                                                $sub->whereNotNull('current.service_id')
                                                    ->whereColumn('reviewer.service_id', 'current.service_id')
                                                    ->orWhere(function ($or) {
                                                        $or->whereNull('current.service_id')
                                                            ->whereColumn('reviewer.material_id', 'current.material_id');
                                                    });
                                            })
                                            ->where('reviewer.workflow_serial', 1)
                                            ->where('reviewer.reviewer_name', '!=', 'approver')
                                            ->where('reviewer.nv_stage_status', 1);
                                    });
                            })
                                ->orWhere(function ($q) {
                                    // CASE 2: workflow_serial > 1
                                    $q->where('current.workflow_serial', '>', 1)
                                        ->whereExists(function ($exists) {
                                            $exists->select(DB::raw(1))
                                                ->from('capex_workflows_status as prev')
                                                ->where(function ($sub) {
                                                    $sub->whereNotNull('current.service_id')
                                                        ->whereColumn('prev.service_id', 'current.service_id')
                                                        ->orWhere(function ($or) {
                                                            $or->whereNull('current.service_id')
                                                                ->whereColumn('prev.material_id', 'current.material_id');
                                                        });
                                                })
                                                ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
                                                ->where('prev.reviewer_name', 'approver')
                                                ->where('prev.nv_stage_status', 1);
                                        });
                                });
                        })
                        ->count();

                    $opexBRPLApprovalCounts[$workflowStage->work_dep] = $count;
                }
                $BRPLlabels = [];
                $BRPLapprovedData = [];
                $BRPLrejectedData = [];
                $BRPLpendingData = [];
                foreach ($fileDataBRPL as $dataPointBRPL) {
                    $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
                    $BRPLlabels[] = $monthBRPL;
                    $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                    $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                    $BRPLpendingData[] = $dataPointBRPL->pending_count;
                }

                $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('id', $Nvid)->pluck("id");

                $pendingAmountBYPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BYPLnv)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BYPLnv)
                    ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $rejectedAmountBYPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BYPLnv)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BYPLnv)
                    ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
                    ->sum('tbl_service.total_buget');

                $approvedAmountBYPL = DB::table('tbl_material')
                    ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                    ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                    ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                    ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                    ->sum('tbl_service.total_buget');

                $fileDataBYPL = Nvsericestatus::select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
                        DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")
                    )
                    ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
            $workflowBYPLStages = Workflow::where('status', 1)->get();
                $capexBYPLApprovalCounts = [];
                foreach ($workflowBYPLStages as $workflowStage) {

                    $count = DB::table('capex_workflows_status as current')
                        ->where('current.department_id', $workflowStage->work_dep)
                        ->where('current.nv_budget_type', 'CAPEX')
                        ->where('current.reviewer_name', 'approver')
                        ->where('current.nv_stage_status', 0)
                        ->whereIn('current.nv_id', $BYPLnv)
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                // CASE 1: workflow_serial = 1
                                $q->where('current.workflow_serial', 1)
                                    ->whereExists(function ($exists) {
                                        $exists->select(DB::raw(1))
                                            ->from('capex_workflows_status as reviewer')
                                            ->where(function ($sub) {
                                                $sub->whereNotNull('current.service_id')
                                                    ->whereColumn('reviewer.service_id', 'current.service_id')
                                                    ->orWhere(function ($or) {
                                                        $or->whereNull('current.service_id')
                                                            ->whereColumn('reviewer.material_id', 'current.material_id');
                                                    });
                                            })
                                            ->where('reviewer.workflow_serial', 1)
                                            ->where('reviewer.reviewer_name', '!=', 'approver')
                                            ->where('reviewer.nv_stage_status', 1);
                                    });
                            })
                                ->orWhere(function ($q) {
                                    // CASE 2: workflow_serial > 1
                                    $q->where('current.workflow_serial', '>', 1)
                                        ->whereExists(function ($exists) {
                                            $exists->select(DB::raw(1))
                                                ->from('capex_workflows_status as prev')
                                                ->where(function ($sub) {
                                                    $sub->whereNotNull('current.service_id')
                                                        ->whereColumn('prev.service_id', 'current.service_id')
                                                        ->orWhere(function ($or) {
                                                            $or->whereNull('current.service_id')
                                                                ->whereColumn('prev.material_id', 'current.material_id');
                                                        });
                                                })
                                                ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
                                                ->where('prev.reviewer_name', 'approver')
                                                ->where('prev.nv_stage_status', 1);
                                        });
                                });
                        })
                        ->count();

                    $capexBYPLApprovalCounts[$workflowStage->work_dep] = $count;
                }

                $opexWorkflowBYPLStages = OpexWorkflow::where('status', 1)->get();
                $opexBYPLApprovalCounts = [];
                foreach ($opexWorkflowBYPLStages as $workflowStage) {

                    $count = DB::table('capex_workflows_status as current')
                        ->where('current.department_id', $workflowStage->work_dep)
                        ->where('current.nv_budget_type', 'OPEX')
                        ->where('current.reviewer_name', 'approver')
                        ->where('current.nv_stage_status', 0)
                        ->whereIn('current.nv_id', $BYPLnv)
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                // CASE 1: workflow_serial = 1
                                $q->where('current.workflow_serial', 1)
                                    ->whereExists(function ($exists) {
                                        $exists->select(DB::raw(1))
                                            ->from('capex_workflows_status as reviewer')
                                            ->where(function ($sub) {
                                                $sub->whereNotNull('current.service_id')
                                                    ->whereColumn('reviewer.service_id', 'current.service_id')
                                                    ->orWhere(function ($or) {
                                                        $or->whereNull('current.service_id')
                                                            ->whereColumn('reviewer.material_id', 'current.material_id');
                                                    });
                                            })
                                            ->where('reviewer.workflow_serial', 1)
                                            ->where('reviewer.reviewer_name', '!=', 'approver')
                                            ->where('reviewer.nv_stage_status', 1);
                                    });
                            })
                                ->orWhere(function ($q) {
                                    // CASE 2: workflow_serial > 1
                                    $q->where('current.workflow_serial', '>', 1)
                                        ->whereExists(function ($exists) {
                                            $exists->select(DB::raw(1))
                                                ->from('capex_workflows_status as prev')
                                                ->where(function ($sub) {
                                                    $sub->whereNotNull('current.service_id')
                                                        ->whereColumn('prev.service_id', 'current.service_id')
                                                        ->orWhere(function ($or) {
                                                            $or->whereNull('current.service_id')
                                                                ->whereColumn('prev.material_id', 'current.material_id');
                                                        });
                                                })
                                                ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
                                                ->where('prev.reviewer_name', 'approver')
                                                ->where('prev.nv_stage_status', 1);
                                        });
                                });
                        })
                        ->count();

                    $opexBYPLApprovalCounts[$workflowStage->work_dep] = $count;
                }
                $BYPLlabels = [];
                $BYPLapprovedData = [];
                $BYPLrejectedData = [];
                $BYPLpendingData = [];
                foreach ($fileDataBYPL as $dataPointBYPL) {
                    $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
                    $BYPLlabels[] = $monthBYPL;
                    $BYPLapprovedData[] = $dataPointBYPL->approved_count;
                    $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
                    $BYPLpendingData[] = $dataPointBYPL->pending_count;
                }

                return view("admin.dashboard", compact(
                    "dept_data",
                    "dept_data_p",
                    "currentFinancialYear",
                    "nextFinancialYear",
                    "nextToNextFinancialYear",
                    "departmentNVCounts",
                    "approvedAmount",
                    "rejectedAmount",
                    "pendingAmount",
                    "totalAmount",
                    "company",
                    "company_id",
                    "nv_sm_data",
                    "totalNV",
                    "approvedNV",
                    "rejectedNV",
                    "pendingNV",
                    'BRPLlabels',
                    'BRPLapprovedData',
                    'BRPLrejectedData',
                    'BRPLpendingData',
                    'BYPLlabels',
                    'BYPLapprovedData',
                    'BYPLrejectedData',
                    'BYPLpendingData',
                    'approvedAmountBYPL',
                    'pendingAmountBYPL',
                    'rejectedAmountBYPL',
                    'approvedAmountBRPL',
                    'pendingAmountBRPL',
                    'rejectedAmountBRPL',
                    'capexApprovalCounts',
                    'opexApprovalCounts',
                    'workflowStages',
                    'workflowBRPLStages',
                    'opexWorkflowBRPLStages',
                    'capexBRPLApprovalCounts',
                    'opexBRPLApprovalCounts',
                    'workflowBYPLStages',
                    'opexWorkflowBYPLStages',
                    'opexWorkflowStages',
                    'capexBYPLApprovalCounts',
                    'opexBYPLApprovalCounts'
                ));
            }
        // ==========================================
        // NORMAL USER ROLE (Role ID 9)
        // ==========================================
        elseif ($user->role_id == 9) {
            if ($fiscal_year) { $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->pluck('id'); } 
            else { $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck('id'); }

            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $approvedData = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id', $totalId);
            $approvedNV = $approvedData->count();
            $totalData = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1);
            $totalNV = $totalData->count();
            $rejectedData = $latestData->filter(function ($data) { return $data->is_reject == 1; });
            $rejectedNV = $rejectedData->count();
            $pendingData = $latestData->filter(function ($data) { return in_array($data->ceo_status, [0]) && in_array($data->is_reject, [0]); });
            $pendingNV = $pendingData->count();

            $totalAmount = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $pendingAmount = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $rejectedAmount = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $approvedAmount = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))->sum('tbl_service.total_buget');

            if ($fiscal_year) { $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->get(); } 
            else { $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->get(); }
            $nv_ids = $nv->pluck('id');

            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
            if (!empty($total_processed)) { $nv_sm_data = $nv_sm_data; } 
            elseif (!empty($total)) { $nv_sm_data = $nv_sm_data; } 
            elseif (!empty($approved)) { $nv_sm_data = $nv_sm_data->where('ceo_status', 1); } 
            elseif (!empty($rejected)) { $nv_sm_data = $nv_sm_data->whereIn('nv_id', $rejectedData->pluck('nv_id'))->where('is_reject', 1); } 
            else { $nv_sm_data = $nv_sm_data->whereIn('nv_id', $pendingData->pluck('nv_id'))->where('is_reject', 0); }
            $nv_sm_data = $nv_sm_data->get();

            if ($fiscal_year) { $BRPLnv = NeedValidation::where('company_id', '6')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->pluck("id"); } 
            else { $BRPLnv = NeedValidation::where('company_id', '6')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck("id"); }
            
            $pendingAmountBRPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $rejectedAmountBRPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $approvedAmountBRPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
            $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"), DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count"))->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])->groupBy('month')->orderBy('month')->get();
            $BRPLlabels = []; $BRPLapprovedData = []; $BRPLrejectedData = []; $BRPLpendingData = [];
            foreach ($fileDataBRPL as $dataPointBRPL) { $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F'); $BRPLlabels[] = $monthBRPL; $BRPLapprovedData[] = $dataPointBRPL->approved_count; $BRPLrejectedData[] = $dataPointBRPL->rejected_count; $BRPLpendingData[] = $dataPointBRPL->pending_count; }

            if ($fiscal_year) { $BYPLnv = NeedValidation::where('company_id', '5')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where('delete_draft', 0)->pluck("id"); } 
            else { $BYPLnv = NeedValidation::where('company_id', '5')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where('delete_draft', 0)->pluck("id"); }
            $pendingAmountBYPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $rejectedAmountBYPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))->sum('tbl_service.total_buget');
            $approvedAmountBYPL = DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
            $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"), DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count"))->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])->groupBy('month')->orderBy('month')->get();
            $BYPLlabels = []; $BYPLapprovedData = []; $BYPLrejectedData = []; $BYPLpendingData = [];
            foreach ($fileDataBYPL as $dataPointBYPL) { $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F'); $BYPLlabels[] = $monthBYPL; $BYPLapprovedData[] = $dataPointBYPL->approved_count; $BYPLrejectedData[] = $dataPointBYPL->rejected_count; $BYPLpendingData[] = $dataPointBYPL->pending_count; }

            $userid = \Auth::user()->id;
            $log = DB::table('log_signature')->where('user_id', $userid)->select('created_at', 'signature_id')->get();

            return view("admin.dashboard", compact("approvedAmount", "currentFinancialYear", "nextFinancialYear", "nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount", "company", "company_id", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", 'BRPLlabels', 'BRPLapprovedData', 'BRPLrejectedData', 'BRPLpendingData', 'BYPLlabels', 'BYPLapprovedData', 'BYPLrejectedData', 'BYPLpendingData', 'log', 'pendingAmountBYPL', 'pendingAmountBRPL', 'approvedAmountBYPL', 'approvedAmountBRPL', 'rejectedAmountBYPL', 'rejectedAmountBRPL'));
        } 
        // ==========================================
        // ELSE BLOCK (Reviewer / HOD / Group CIO / Workflow)
        // ==========================================
        else {
            $nv_sm_data = collect(); 
            $totalNV = 0; $rejectedNV = 0; $approvedNV = 0; $pendingNV = 0;
            $totalAmount = 0; $pendingAmount = 0; $rejectedAmount = 0; $approvedAmount = 0;

            $BRPLlabels = []; $BRPLapprovedData = []; $BRPLrejectedData = []; $BRPLpendingData = [];
            $BYPLlabels = []; $BYPLapprovedData = []; $BYPLrejectedData = []; $BYPLpendingData = [];

            $pendingAmountBRPL = 0; $rejectedAmountBRPL = 0; $approvedAmountBRPL = 0;
            $pendingAmountBYPL = 0; $rejectedAmountBYPL = 0; $approvedAmountBYPL = 0;

            $mergeChartData = function(&$labels, &$approved, &$rejected, &$pending, $newLabels, $newApproved, $newRejected, $newPending) {
                $temp = [];
                foreach ($labels as $index => $label) { $temp[$label]['approved'] = ($temp[$label]['approved'] ?? 0) + ($approved[$index] ?? 0); $temp[$label]['rejected'] = ($temp[$label]['rejected'] ?? 0) + ($rejected[$index] ?? 0); $temp[$label]['pending'] = ($temp[$label]['pending'] ?? 0) + ($pending[$index] ?? 0); }
                foreach ($newLabels as $index => $label) { $temp[$label]['approved'] = ($temp[$label]['approved'] ?? 0) + ($newApproved[$index] ?? 0); $temp[$label]['rejected'] = ($temp[$label]['rejected'] ?? 0) + ($newRejected[$index] ?? 0); $temp[$label]['pending'] = ($temp[$label]['pending'] ?? 0) + ($newPending[$index] ?? 0); }
                $monthOrder = ['April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12, 'January'=>1, 'February'=>2, 'March'=>3];
                uksort($temp, function($a, $b) use ($monthOrder) { return $monthOrder[$a] <=> $monthOrder[$b]; });
                $labels = array_keys($temp); $approved = array_column($temp, 'approved'); $rejected = array_column($temp, 'rejected'); $pending = array_column($temp, 'pending');
            };

            $getChartArrays = function($fileData) {
                $labels = []; $approvedData = []; $rejectedData = []; $pendingData = [];
                foreach ($fileData as $dataPoint) { $month = Carbon::createFromFormat('!m', $dataPoint->month)->format('F'); $labels[] = $month; $approvedData[] = $dataPoint->approved_count ?? 0; $rejectedData[] = $dataPoint->rejected_count ?? 0; $pendingData[] = $dataPoint->pending_count ?? 0; }
                return [$labels, $approvedData, $rejectedData, $pendingData];
            };

            $employee = Employee::where('user_id', $user->id)->with('department')->first();
            if(!$employee) { return view("admin.dashboard", compact("currentFinancialYear", "company", "nv_sm_data")); }
		//$departmentIds = explode(',', $employee->department_id);
           // $allusers = Employee::where('department_id', $employee->department_id)->get();
		 $departmentIds = explode(',', $employee->department_id);
             $allusers = Employee::whereIn('department_id', $departmentIds)->get();
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = !empty($employees->department_id) ? explode(',', $employees->department_id) : [];
            $departments = Department::whereIn("id", $departmentIds)->get();

            // ==========================================
            // 3. REVIEWERS LOOP (Reviewer 1, 2, 3, 4)
            // ==========================================
            foreach ($departments as $dep) {                
                if (!empty($dep->dep_rew1) && $dep->dep_rew1 == $user->id) {
                    $Values = [$user->id, $dep->dep_rew1]; $rewDepIds = Department::where("dep_rew1", $user->id)->pluck('id');
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->where(function ($query) use ($Values, $allNormalUsers, $rewDepIds) { $query->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); })->pluck('id');
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                    $totalNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                    $approvedNV += $latestData->where('ceo_status', 1)->count();
                    $rejectedNV += $latestData->filter(function ($data) { return in_array($data->ceo_status, [2]) || $data->rv1_status == 2; })->count();
                    $pendingNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('draft', 1)->where('rv1_status', 0)->count();
                    $totalAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 1)->sum('tbl_service.total_buget');
                    $pendingAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_service.total_buget');
                    $rejectedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv1_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $rewDepIds) { $query->where('user_id', $user->id)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); })->whereHas('service')->select('id')->get(); $nv_ids = $nv->pluck('id');
                    $nv_sm_query = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft', 1)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
                    if (!empty($total_processed)) $nv_sm_query = $nv_sm_query->where('rv1_status', 1); elseif (!empty($total)) $nv_sm_query = $nv_sm_query; elseif (!empty($approved)) $nv_sm_query = $nv_sm_query->where('ceo_status', 1); elseif (!empty($rejected)) $nv_sm_query = $nv_sm_query->where('rv1_status', 2); else $nv_sm_query = $nv_sm_query->where('rv1_status', 0);
                    $nv_sm_data = $nv_sm_data->concat($nv_sm_query->get());
                    $BRPLnv = NeedValidation::whereIn("department_id", $rewDepIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                    $pendingAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_service.total_buget');
                    $rejectedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv1_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv1_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" ) THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])->groupBy('month')->orderBy('month')->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                    $BYPLnv = NeedValidation::whereIn("department_id", $rewDepIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                    $pendingAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)->sum('tbl_service.total_buget');
                    $rejectedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv1_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv1_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" )  THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])->groupBy('month')->orderBy('month')->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
                }
          elseif (!empty($dep->dep_rew2) && $dep->dep_rew2 == $user->id) {
            $Values = [$user->id, $dep->dep_rew1]; 
            $rewDepIds = Department::where("dep_rew2", $user->id)->pluck('id');
            
            // 1. totalId 
            $totalId = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                ->where(function ($query) use ($Values, $allNormalUsers, $rewDepIds) { 
                    $query->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); 
                })
                ->when(!empty($dep->dep_rew2_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew2_added_at))
                ->pluck('id');
                
            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $approvedNV += $latestData->where('ceo_status', 1)->count();
            $rejectedNV += $latestData->filter(function ($data) { return in_array($data->ceo_status, [2]) || $data->rv2_status == 2; })->count();
            $pendingNV += $latestData->filter(function ($data) use ($dep) { if (!empty($dep->dep_rew1)) return $data->rv1_status == 1 && $data->rv2_status == 0; else return $data->draft == 1 && $data->rv2_status == 0 && $data->rv1_status != 2; })->count();
            $totalNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->count();
            
            $totalAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 1)->sum('tbl_service.total_buget');
            $pendingAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_service.total_buget');
            $rejectedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv2_status', 2)->sum('tbl_service.total_buget');
            $approvedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
            
            // 2. $nv 
            $nv = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                ->where(function ($q) use ($user, $allNormalUsers, $rewDepIds) { 
                    $q->where('user_id', $user->id)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); 
                })
                ->when(!empty($dep->dep_rew2_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew2_added_at))
                ->whereHas('service')->select('id')->get(); 
                
            $nv_ids = $nv->pluck('id');
            $nv_sm_query = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status',1), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2))->orderBy('id', 'asc');
            
            if (!empty($total_processed)) $nv_sm_query = $nv_sm_query->where('rv2_status', 1); 
            elseif (!empty($total)) $nv_sm_query = $nv_sm_query; 
            elseif (!empty($approved)) $nv_sm_query = $nv_sm_query->where('ceo_status', 1); 
            elseif (!empty($rejected)) $nv_sm_query = $nv_sm_query->where('rv2_status', 2); 
            else $nv_sm_query = $nv_sm_query->where('rv2_status', 0);
            
            $nv_sm_data = $nv_sm_data->concat($nv_sm_query->get());
            
            // 3. $BRPLnv 
            $BRPLnv = NeedValidation::whereIn("department_id", $rewDepIds)
                ->where('company_id', '6')
                ->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                ->when(!empty($dep->dep_rew2_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew2_added_at))
                ->pluck("id");
                
            $pendingAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status',1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status',1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_service.total_buget');
            $rejectedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv2_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv2_status', 2)->sum('tbl_service.total_buget');
            $approvedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
            
            $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv2_status = "0" ) THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status',1), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2))->groupBy('month')->orderBy('month')->get();
            list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
            
            // 4. $BYPLnv 
            $BYPLnv = NeedValidation::whereIn("department_id", $rewDepIds)
                ->where('company_id', '5')
                ->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                ->when(!empty($dep->dep_rew2_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew2_added_at))
                ->pluck("id");
                
            $pendingAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status',1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv2_status', 0)->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status',1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2))->sum('tbl_service.total_buget');
            $rejectedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv2_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv2_status', 2)->sum('tbl_service.total_buget');
            $approvedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
            
            $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv2_status = "0" )  THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status',1), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2))->groupBy('month')->orderBy('month')->get();
            list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
        }
                elseif (!empty($dep->dep_rew3) && $dep->dep_rew3 == $user->id) {
                    $Values = [$user->id, $dep->dep_rew1, $dep->dep_rew2]; $rewDepIds = Department::where("dep_rew3", $user->id)->pluck('id');
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->where(function ($query) use ($Values, $allNormalUsers, $rewDepIds) { $query->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); })->pluck('id');
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                    $approvedNV += $latestData->where('ceo_status', 1)->count();
                    $rejectedNV += $latestData->filter(function ($data) { return in_array($data->ceo_status, [2]) || $data->rv3_status == 2; })->count();
                    $pendingNV += $latestData->filter(function ($data) use ($dep) { if (!empty($dep->dep_rew2)) return $data->rv2_status == 1 && $data->rv3_status == 0; elseif (!empty($dep->dep_rew1)) return $data->rv1_status == 1 && $data->rv3_status == 0 && $data->rv2_status != 2; else return $data->draft == 1 && $data->rv3_status == 0 && $data->rv1_status != 2 && $data->rv2_status != 2; })->count();
                    $totalNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->count();
                    $totalAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 1)->sum('tbl_service.total_buget');
                    $pendingAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_service.total_buget');
                    $rejectedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv3_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->where(function ($q) use ($user, $allNormalUsers, $rewDepIds) { $q->where('user_id', $user->id)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); })->whereHas('service')->select('id')->get(); $nv_ids = $nv->pluck('id');
                    $nv_sm_query = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status',1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2)->where('rv2_status','!=',2)))->orderBy('id', 'asc');
                    if (!empty($total_processed)) $nv_sm_query = $nv_sm_query->where('rv3_status', 1); elseif (!empty($total)) $nv_sm_query = $nv_sm_query; elseif (!empty($approved)) $nv_sm_query = $nv_sm_query->where('ceo_status', 1); elseif (!empty($rejected)) $nv_sm_query = $nv_sm_query->where('rv3_status', 2); else $nv_sm_query = $nv_sm_query->where('rv3_status', 0);
                    $nv_sm_data = $nv_sm_data->concat($nv_sm_query->get());
                    $BRPLnv = NeedValidation::whereIn("department_id", $rewDepIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                    $pendingAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_service.total_buget');
                    $rejectedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv3_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv3_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv3_status = "0" ) THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status',1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2)->where('rv2_status','!=',2)))->groupBy('month')->orderBy('month')->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                    $BYPLnv = NeedValidation::whereIn("department_id", $rewDepIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                    $pendingAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv3_status', 0)->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv2_status','!=',2)))->sum('tbl_service.total_buget');
                    $rejectedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv3_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv3_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status',1), fn($q)=>$q->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2), fn($q)=>$q->where('draft',1)->where('rv1_status','!=',2)->where('rv2_status','!=',2)))->groupBy('month')->orderBy('month')->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
                }
               elseif (!empty($dep->dep_rew4) && $dep->dep_rew4 == $user->id) {
                    $Values = [$user->id, $dep->dep_rew1, $dep->dep_rew2, $dep->dep_rew3]; 
                    $rewDepIds = Department::where("dep_rew4", $user->id)->pluck('id');
                    
                    // 1. totalId 
                    $totalId = NeedValidation::where("fiscal_year", $fiscal_year ?? $currentFinancialYear)
                        ->where(function ($query) use ($Values, $allNormalUsers, $rewDepIds) { 
                            $query->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); 
                        })
                        ->when(!empty($dep->dep_rew4_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew4_added_at))
                        ->pluck('id');
                        
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 0)->get();
                    $approvedNV += $latestData->where('ceo_status', 1)->count();
                    $rejectedNV += $latestData->filter(function ($data) { return in_array($data->ceo_status, [2]) || $data->rv4_status == 2; })->count();
                    $pendingNV += $latestData->filter(function ($data) use ($dep) { if (!empty($dep->dep_rew3)) return $data->rv3_status == 1 && $data->rv4_status == 0; elseif (!empty($dep->dep_rew2)) return $data->rv2_status == 1 && $data->rv4_status == 0 && $data->rv3_status != 2; elseif (!empty($dep->dep_rew1)) return $data->rv1_status == 1 && $data->rv4_status == 0 && $data->rv2_status != 2 && $data->rv3_status != 2; else return $data->draft == 1 && $data->rv4_status == 0 && $data->rv1_status != 2 && $data->rv2_status != 2 && $data->rv3_status != 2; })->count();
                    $totalNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->count();
                    $totalAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 1)->sum('tbl_service.total_buget');
                    $pendingAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_service.total_buget');
                    $rejectedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.rv4_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    
                    // 2. $nv 
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                        ->where(function ($q) use ($user, $allNormalUsers, $rewDepIds) { 
                            $q->where('user_id', $user->id)->orWhereIn('user_id', $allNormalUsers)->orWhereIn('department_id', $rewDepIds); 
                        })
                        ->when(!empty($dep->dep_rew4_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew4_added_at))
                        ->whereHas('service')->select('id')->get(); 
                        
                    $nv_ids = $nv->pluck('id');
                    $nv_sm_query = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->when(!empty($dep->dep_rew3), fn($q)=>$q->where('rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status', 1)->where('rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2)->where('rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('draft',1)->where('rv2_status','!=',2)->where('rv1_status','!=',2)->where('rv3_status','!=',2))->orderBy('id', 'asc');
                    
                    if (!empty($total_processed)) $nv_sm_query = $nv_sm_query->where('rv4_status', 1); 
                    elseif (!empty($total)) $nv_sm_query = $nv_sm_query; 
                    elseif (!empty($approved)) $nv_sm_query = $nv_sm_query->where('ceo_status', 1); 
                    elseif (!empty($rejected)) $nv_sm_query = $nv_sm_query->where('rv4_status', 2); 
                    else $nv_sm_query = $nv_sm_query->where('rv4_status', 0);
                    
                    $nv_sm_data = $nv_sm_data->concat($nv_sm_query->get());
                    
                    // 3. $BRPLnv 
                    $BRPLnv = NeedValidation::whereIn("department_id", $rewDepIds)
                        ->where('company_id', '6')
                        ->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                        ->when(!empty($dep->dep_rew4_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew4_added_at))
                        ->pluck("id");
                        
                    $pendingAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_service.total_buget');
                    $rejectedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.rv4_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.rv4_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    
                    
                    $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'))
                        ->whereIn('nv_id', $BRPLnv)
                        ->with(['service', 'material', 'user'])
                        ->when(!empty($dep->dep_rew3), fn($q)=>$q->where('rv3_status', 1))
                        ->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status', 1)->where('rv3_status','!=',2))
                        ->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2)->where('rv3_status','!=',2))
                        ->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('draft',1)->where('rv2_status','!=',2)->where('rv1_status','!=',2)->where('rv3_status','!=',2))
                        ->groupBy('month')->orderBy('month')->get();
                        
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                    
                    // 4. $BYPLnv 
                    $BYPLnv = NeedValidation::whereIn("department_id", $rewDepIds)
                        ->where('company_id', '5')
                        ->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)
                        ->when(!empty($dep->dep_rew4_added_at), fn($q) => $q->where('created_at', '>=', $dep->dep_rew4_added_at))
                        ->pluck("id");
                        
                    $pendingAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv4_status', 0)->when(!empty($dep->dep_rew3), fn($q)=>$q->where('nvservicestatus.rv3_status', 1))->when(!empty($dep->dep_rew2), fn($q)=>$q->where('nvservicestatus.rv2_status', 1)->where('nvservicestatus.rv3_status','!=',2))->when(!empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.rv1_status', 1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('nvservicestatus.draft',1)->where('nvservicestatus.rv2_status','!=',2)->where('nvservicestatus.rv1_status','!=',2)->where('nvservicestatus.rv3_status','!=',2))->sum('tbl_service.total_buget');
                    $rejectedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.rv4_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.rv4_status', 2)->sum('tbl_service.total_buget');
                    $approvedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                    
                   
                    $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'), DB::raw('SUM(CASE WHEN (rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'))
                        ->whereIn('nv_id', $BYPLnv)
                        ->with(['service', 'material', 'user'])
                        ->when(!empty($dep->dep_rew3), fn($q)=>$q->where('rv3_status', 1))
                        ->when(!empty($dep->dep_rew2), fn($q)=>$q->where('rv2_status', 1)->where('rv3_status','!=',2))
                        ->when(!empty($dep->dep_rew1), fn($q)=>$q->where('rv1_status', 1)->where('rv2_status','!=',2)->where('rv3_status','!=',2))
                        ->when(empty($dep->dep_rew3) && empty($dep->dep_rew2) && empty($dep->dep_rew1), fn($q)=>$q->where('draft',1)->where('rv2_status','!=',2)->where('rv1_status','!=',2)->where('rv3_status','!=',2))
                        ->groupBy('month')->orderBy('month')->get();
                        
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
                }
            }

            // ==========================================
            // 4. GROUP CIO BLOCK
            // ==========================================
            if (Department::where("group_cio", $user->id)->exists()) {
                $cioDepIds = Department::where("group_cio", $user->id)->where('status',1)->pluck('id');
                $totalId = NeedValidation::whereIn('department_id', $cioDepIds)->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $totalNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 1)->count();
                $rejectedNV += Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->count();
                $approvedNV += $latestData->where('ceo_status', 1)->count();
                $totalAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->where('nvservicestatus.groupcio_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->where('nvservicestatus.groupcio_status', 1)->sum('tbl_service.total_buget');
                $pen_amt = $latestData->filter(function ($data) { return in_array($data->hod_status, [1]) && in_array($data->groupcio_status, [0]); });
                $pendingAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_service.total_buget');
                $rejectedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.groupcio_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.groupcio_status', 2)->sum('tbl_service.total_buget');
                $approvedAmount += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $totalId)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $totalId)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                $pendingNV += $pen_amt->count();
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $cioDepIds)->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->get(); $nv_ids = $nv->pluck('id');
                $cio_nv_sm_query = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('hod_status', 1)->orderBy('id', 'asc');
                if (!empty($total_processed)) $cio_nv_sm_query = $cio_nv_sm_query->where('groupcio_status', 1); elseif (!empty($total)) $cio_nv_sm_query = $cio_nv_sm_query; elseif (!empty($approved)) $cio_nv_sm_query = $cio_nv_sm_query->where('ceo_status', 1); elseif (!empty($rejected)) $cio_nv_sm_query = $cio_nv_sm_query->where('groupcio_status', 2); else $cio_nv_sm_query = $cio_nv_sm_query->where('groupcio_status', 0);
                $nv_sm_data = $nv_sm_data->concat($cio_nv_sm_query->get());
                $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('department_id', $cioDepIds)->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                $pendingAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_service.total_buget');
                $rejectedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.groupcio_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.groupcio_status', 2)->sum('tbl_service.total_buget');
                $approvedAmountBRPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN (groupcio_status = "2") THEN 1 ELSE 0 END) as rejected_count'), DB::raw('SUM(CASE WHEN (groupcio_status = "0") THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])->where('hod_status', 1)->groupBy('month')->orderBy('month')->get();
                list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('department_id', $cioDepIds)->where('fiscal_year', $fiscal_year ?? $currentFinancialYear)->pluck("id");
                $pendingAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))->sum('tbl_service.total_buget');
                $rejectedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.groupcio_status', 2)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.groupcio_status', 2)->sum('tbl_service.total_buget');
                $approvedAmountBYPL += DB::table('tbl_material')->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)->sum('tbl_service.total_buget');
                $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN (groupcio_status = "2") THEN 1 ELSE 0 END) as rejected_count'), DB::raw('SUM(CASE WHEN (groupcio_status = "0") THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])->where('hod_status', 1)->groupBy('month')->orderBy('month')->get();
                list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
            }

            // ==========================================
            // 5. HOD BLOCK (FIXED)
            // ==========================================
            if (Department::where("dep_hod", $user->id)->exists()) {
                $hodDepIds = Department::where("dep_hod", $user->id)->where('status', 1)->pluck('id');
                $nvTable = (new NeedValidation)->getTable(); $deptTable = (new Department)->getTable(); $statusTable = (new Nvsericestatus)->getTable();
                 $baseQuery = Nvsericestatus::query()->join("{$nvTable} as nv", "{$statusTable}.nv_id", '=', 'nv.id')->join("{$deptTable} as d", 'nv.department_id', '=', 'd.id')->whereIn('d.dep_hod', [$user->id])->where('d.status', 1)->where('nv.fiscal_year', $fiscal_year ?? $currentFinancialYear)->where('nv.delete_draft', 0)->where("{$statusTable}.draft", 1)->where(function ($query) use ($user, $allNormalUsers, $hodDepIds) { $query->where('nv.user_id', $user->id)->orWhereIn('nv.user_id', $allNormalUsers)->orWhereIn('nv.department_id', $hodDepIds); })
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
                        $q->whereNull('d.dep_rew4')
                            ->orWhere('d.dep_rew4', '')
                            ->orWhere(function ($q) use ($statusTable) {

                        $q->where(function ($sub) {
              // Reviewer was added after NV creation
                        $sub->whereNotNull('d.dep_rew4_added_at')
                        ->whereColumn('nv.created_at', '<', 'd.dep_rew4_added_at');
          })

          ->orWhere(function ($sub) use ($statusTable) {
              // Reviewer existed before NV, so approval is required
              $sub->whereColumn('nv.created_at', '>=', 'd.dep_rew4_added_at')
                  ->where("{$statusTable}.rv4_status", 1);
          });

      }); 
});


                $pendingIds = (clone $baseQuery)->where("{$statusTable}.hod_status", 0)->distinct()->pluck("{$statusTable}.nv_id");
                $processedIds = (clone $baseQuery)->where("{$statusTable}.hod_status", 1)->distinct()->pluck("{$statusTable}.nv_id");
                $rejectedIds = (clone $baseQuery)->where("{$statusTable}.hod_status", 2)->distinct()->pluck("{$statusTable}.nv_id");
                $ceoApprovedIds = Nvsericestatus::whereIn('nv_id', $processedIds)->where('ceo_status', 1)->pluck('nv_id');
                $totalId = $pendingIds->merge($processedIds)->merge($rejectedIds);
                $approvedNV += $ceoApprovedIds->count(); $rejectedNV += $rejectedIds->count(); $totalNV += $processedIds->count(); $pendingNV += $pendingIds->count();
                $totalAmount += DB::table('tbl_material')->whereIn('nv_id', $processedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $processedIds)->sum('total_buget');
                $rejectedAmount += DB::table('tbl_material')->whereIn('nv_id', $rejectedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $rejectedIds)->sum('total_buget');
                $approvedAmount += DB::table('tbl_material')->whereIn('nv_id', $ceoApprovedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $ceoApprovedIds)->sum('total_buget');
                $pendingAmount += DB::table('tbl_material')->whereIn('nv_id', $pendingIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $pendingIds)->sum('total_buget');
                
                if (request()->has('total_processed')) { $selectedIds = $processedIds; } elseif (request()->has('approved')) { $selectedIds = $ceoApprovedIds; } elseif (request()->has('rejected')) { $selectedIds = $rejectedIds; } else { $selectedIds = $pendingIds; }
                
                // ✅ FIX: Use concat instead of overwrite
                $hodRecords = Nvsericestatus::with(['service', 'material', 'user'])->whereIn('nv_id', $selectedIds)->orderBy('id', 'desc')->get()->unique('nv_id');
                $nv_sm_data = $nv_sm_data->concat($hodRecords);

                $BRPLnv = NeedValidation::whereIn("id", $totalId)->where('company_id', '6')->pluck("id");
                $pendingAmountBRPL += DB::table('tbl_material')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $pendingIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $pendingIds)->sum('total_buget');
                $rejectedAmountBRPL += DB::table('tbl_material')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $rejectedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $rejectedIds)->sum('total_buget');
                $approvedAmountBRPL += DB::table('tbl_material')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $ceoApprovedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BRPLnv)->whereIn('nv_id', $ceoApprovedIds)->sum('total_buget');
                $fileDataBRPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN nv_id IN (' . implode(',', $ceoApprovedIds->toArray() ?: [0]) . ') THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN (hod_status = "2") THEN 1 ELSE 0 END) as rejected_count'), DB::raw('SUM(CASE WHEN (hod_status = "0") THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BRPLnv)->groupBy('month')->orderBy('month')->get();
                list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                $BYPLnv = NeedValidation::whereIn("id", $totalId)->where('company_id', '5')->pluck("id");
                $pendingAmountBYPL += DB::table('tbl_material')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $pendingIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $pendingIds)->sum('total_buget');
                $rejectedAmountBYPL += DB::table('tbl_material')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $rejectedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $rejectedIds)->sum('total_buget');
                $approvedAmountBYPL += DB::table('tbl_material')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $ceoApprovedIds)->sum('total_budget_both') + DB::table('tbl_service')->whereIn('nv_id', $BYPLnv)->whereIn('nv_id', $ceoApprovedIds)->sum('total_buget');
                $fileDataBYPL = Nvsericestatus::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(CASE WHEN nv_id IN (' . implode(',', $ceoApprovedIds->toArray() ?: [0]) . ') THEN 1 ELSE 0 END) as approved_count'), DB::raw('SUM(CASE WHEN (hod_status = "2") THEN 1 ELSE 0 END) as rejected_count'), DB::raw('SUM(CASE WHEN (hod_status = "0") THEN 1 ELSE 0 END) as pending_count'))->whereIn('nv_id', $BYPLnv)->groupBy('month')->orderBy('month')->get();
                list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
            }

            // ==========================================
            // 6. WORKFLOW USERS BLOCK
            // ==========================================
            $workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->get();
            if ($workflows->isNotEmpty()) {
                $year = $fiscal_year ?? $currentFinancialYear;
                $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('transfer_to_nominee1', 0)->get();
                $capexNvIds = []; $opexNvIds = [];
                foreach ($nv_workflows as $nvs) {
                    $nv_id = $nvs->nv_id; $workflow_serial = $nvs->workflow_serial; $budget_type = $nvs->nv_budget_type; $isApprover = strtolower($nvs->reviewer_name) === 'approver'; $addToResult = false;
                    if ($isApprover) {
                        $reviewers = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial)->where('reviewer_name', '!=', 'approver')->get();
                        if ($reviewers->isNotEmpty()) { $addToResult = $reviewers->contains(fn($rev) => $rev->nv_stage_status == 1); } elseif ($workflow_serial == 1) { $addToResult = true; } else {
                            if($nvs->department_id == 19){ $transfer_to_nom1 = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('department_id', 16)->where('transfer_to_nominee1', 1)->exists(); if($transfer_to_nom1){ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 2)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); }else{ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 1)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); } }else{ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 1)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); }
                        }
                    } else { if ($workflow_serial == 1) { $addToResult = true; } else { if($nvs->department_id == 19){ $transfer_to_nom1 = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('department_id', 16)->where('transfer_to_nominee1', 1)->exists(); if($transfer_to_nom1){ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 2)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); }else{ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 1)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); } }else{ $addToResult = DB::table('capex_workflows_status')->where('nv_id', $nv_id)->where('workflow_serial', $workflow_serial - 1)->where('reviewer_name', 'approver')->where('nv_stage_status', 1)->exists(); } } }
                    if ($addToResult) { if ($budget_type === 'CAPEX') $capexNvIds[] = $nv_id; elseif ($budget_type === 'OPEX') $opexNvIds[] = $nv_id; }
                }
                $capexNvIds = array_unique($capexNvIds); $opexNvIds = array_unique($opexNvIds);
                $capexData = NeedValidation::with("division", "service")->whereIn("id", $capexNvIds)->where("budget_type", 'CAPEX')->orderBy("id", "desc")->where('fiscal_year', $year)->get();
                $opexData = NeedValidation::with("division", "service")->whereIn("id", $opexNvIds)->where("budget_type", 'OPEX')->where('fiscal_year', $year)->orderBy("id", "desc")->get();
                $nv1 = $capexData->merge($opexData);
                if($nv1->isNotEmpty()) {
                    $wf_nv_ids = $nv1->pluck('id');
                    $wfApprovedData = Nvsericestatus::whereIn('nv_id', $wf_nv_ids)->where('ceo_status', 1); $approvedNV += $wfApprovedData->count();
                    $wfRejectedNVIDS = Nvsericestatus::whereIn('nv_id', $wf_nv_ids)->where('is_reject', 1);
                    $wfRejectedMatData = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 2)->whereIn('material_id', $wfRejectedNVIDS->pluck('material_id'))->get();
                    $wfRejectedSerData = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 2)->whereIn('service_id', $wfRejectedNVIDS->pluck('service_id'))->get();
                    $wfRejectedData = $wfRejectedMatData->merge($wfRejectedSerData); $rejectedNV += count($wfRejectedData);
                    $wfPendingNVIDS = Nvsericestatus::whereIn('nv_id', $wf_nv_ids)->where('is_reject', 0);
                    $wfPendingMatData = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 0)->whereIn('material_id', $wfPendingNVIDS->pluck('material_id'))->get();
                    $wfPendingSerData = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 0)->whereIn('service_id', $wfPendingNVIDS->pluck('service_id'))->get();
                    $wfPendingData = $wfPendingMatData->merge($wfPendingSerData); $pendingNV += count($wfPendingData);
                    $wfTotalData = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 1)->whereIn('nv_id', $wf_nv_ids); $totalNV += $wfTotalData->count();
                    $wf_nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])->whereIn('nv_id', $wf_nv_ids)->orderBy('id', 'asc');
                    if (!empty($total)) { $wf_nv_sm_data = $wf_nv_sm_data->where('hod_status', 1)->when(Department::where("group_cio", $user->id)->exists(), function ($query) { $query->orWhere('groupcio_status', 1); }); }elseif(!empty($total_processed)){ $wf_nv_sm_data = $wf_nv_sm_data->where('ceo_status','=', 0)->where('is_reject','=',0); }elseif (!empty($approved)) { $wf_nv_sm_data = $wf_nv_sm_data->where('ceo_status', 1); } elseif (!empty($rejected)) {
                        $wfRejectedMatIds = $wfRejectedMatData->pluck('material_id')->filter()->unique(); $wfRejectedSerIds = $wfRejectedSerData->pluck('service_id')->filter()->unique();
                        $wf_nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])->whereIn('nv_id', $wf_nv_ids)->where(function ($query) use ($wfRejectedMatIds, $wfRejectedSerIds) { if ($wfRejectedMatIds->isNotEmpty()) $query->whereIn('material_id', $wfRejectedMatIds); if ($wfRejectedMatIds->isNotEmpty() && $wfRejectedSerIds->isNotEmpty()) $query->orWhereIn('service_id', $wfRejectedSerIds); elseif ($wfRejectedSerIds->isNotEmpty()) $query->whereIn('service_id', $wfRejectedSerIds); })->orderBy('id', 'asc');
                    } else { $wf_nv_sm_data = $wf_nv_sm_data->whereIn('nv_id', $wfPendingData->pluck('nv_id'))->where('is_reject', 0); }
                    $nv_sm_data = $nv_sm_data->concat($wf_nv_sm_data->get());
                    $totalAmount += DB::table('tbl_material')->whereIn('tbl_material.id', $wfTotalData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.id', $wfTotalData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $pendingAmount += DB::table('tbl_material')->whereIn('tbl_material.id', $wfPendingData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.id', $wfPendingData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $rejectedAmount += DB::table('tbl_material')->whereIn('tbl_material.id', $wfRejectedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.id', $wfRejectedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $approvedAmount += DB::table('tbl_material')->whereIn('tbl_material.id', $wfApprovedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.id', $wfApprovedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('id', $wf_nv_ids)->pluck("id");
                    $pendingAmountBRPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('tbl_material.id', $wfPendingData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('tbl_service.id', $wfPendingData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $rejectedAmountBRPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('tbl_material.id', $wfRejectedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('tbl_service.id', $wfRejectedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $approvedAmountBRPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BRPLnv)->whereIn('tbl_material.id', $wfApprovedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BRPLnv)->whereIn('tbl_service.id', $wfApprovedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $wfRejectedMatIds ??= collect(); $wfPendingMatIds ??= collect();
                    $fileDataBRPL = Nvsericestatus::selectRaw("MONTH(created_at) AS month, SUM(CASE WHEN ceo_status = ? THEN 1 ELSE 0 END) AS approved_count, SUM(CASE WHEN is_reject = ? AND material_id IN (" . ($wfRejectedMatIds->isNotEmpty() ? $wfRejectedMatIds->implode(',') : 'NULL') . ") THEN 1 ELSE 0 END) AS rejected_count, SUM(CASE WHEN is_reject = ? AND material_id IN (" . ($wfPendingMatIds->isNotEmpty() ? $wfPendingMatIds->implode(',') : 'NULL') . ") THEN 1 ELSE 0 END) AS pending_count", [1, 1, 0])->whereIn('nv_id', $BRPLnv)->groupBy(DB::raw('MONTH(created_at)'))->orderBy(DB::raw('MONTH(created_at)'))->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBRPL); $mergeChartData($BRPLlabels, $BRPLapprovedData, $BRPLrejectedData, $BRPLpendingData, $lbs, $apps, $rejs, $pens);
                    $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('id', $wf_nv_ids)->pluck("id");
                    $pendingAmountBYPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('tbl_material.id', $wfPendingData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('tbl_service.id', $wfPendingData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $rejectedAmountBYPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('tbl_material.id', $wfRejectedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('tbl_service.id', $wfRejectedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $approvedAmountBYPL += DB::table('tbl_material')->whereIn('tbl_material.nv_id', $BYPLnv)->whereIn('tbl_material.id', $wfApprovedData->pluck('material_id'))->sum('tbl_material.total_budget_both') + DB::table('tbl_service')->whereIn('tbl_service.nv_id', $BYPLnv)->whereIn('tbl_service.id', $wfApprovedData->pluck('service_id'))->sum('tbl_service.total_buget');
                    $fileDataBYPL = Nvsericestatus::selectRaw("MONTH(created_at) AS month, SUM(CASE WHEN ceo_status = ? THEN 1 ELSE 0 END) AS approved_count, SUM(CASE WHEN is_reject = ? AND material_id IN (" . ($wfRejectedMatIds->isNotEmpty() ? $wfRejectedMatIds->implode(',') : 'NULL') . ") THEN 1 ELSE 0 END) AS rejected_count, SUM(CASE WHEN is_reject = ? AND material_id IN (" . ($wfPendingMatIds->isNotEmpty() ? $wfPendingMatIds->implode(',') : 'NULL') . ") THEN 1 ELSE 0 END) AS pending_count", [1, 1, 0])->whereIn('nv_id', $BYPLnv)->groupBy(DB::raw('MONTH(created_at)'))->orderBy(DB::raw('MONTH(created_at)'))->get();
                    list($lbs, $apps, $rejs, $pens) = $getChartArrays($fileDataBYPL); $mergeChartData($BYPLlabels, $BYPLapprovedData, $BYPLrejectedData, $BYPLpendingData, $lbs, $apps, $rejs, $pens);
                }
            }
            // Final Sort and Deduplication
            $nv_sm_data = $nv_sm_data->sortByDesc('id')->unique('id')->values();
            $userid = \Auth::user()->id;
            $log = DB::table('log_signature')->where('user_id', $userid)->select('created_at', 'signature_id')->get();
            return view("admin.dashboard", compact("approvedAmount", "currentFinancialYear", "nextFinancialYear", "nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount", "company", "company_id", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", 'BRPLlabels', 'BRPLapprovedData', 'BRPLrejectedData', 'BRPLpendingData', 'BYPLlabels', 'BYPLapprovedData', 'BYPLrejectedData', 'BYPLpendingData', 'log', 'pendingAmountBYPL', 'pendingAmountBRPL', 'approvedAmountBYPL', 'approvedAmountBRPL', 'rejectedAmountBYPL', 'rejectedAmountBRPL'));
        }
    }
     //06-02-2026-old
    // public function dashboard(Request $request)
    // {

    //     $fiscal_year = $request->fiscal_year;
    //     $company_id = $request->company_id;

    //     $total_processed = $request->total_processed;
    //     $total = $request->total;
    //     $rejected = $request->rejected;
    //     $approved = $request->approved;
    //     $pending = $request->pending ?? 0;

    //     $user = \Auth::user();
    //     $currentDate = Carbon::now();

        
    //     if ($currentDate->month >= 4) {
    //         $financialYearStart = Carbon::create($currentDate->year, 4, 1);
    //     } else {
    //         $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
    //     }
    //     $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
    //     $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');

    //     $nextFinancialYearStart = $financialYearStart->copy()->addYear();
    //     $nextFinancialYearEnd = $nextFinancialYearStart->copy()->addYear()->subDay();
    //     $nextFinancialYear = $nextFinancialYearStart->format('Y') . '-' . $nextFinancialYearEnd->format('y');

    //     $nextToNextFinancialYearStart = $nextFinancialYearStart->copy()->addYear();
    //     $nextToNextFinancialYearEnd = $nextToNextFinancialYearStart->copy()->addYear()->subDay();
    //     $nextToNextFinancialYear = $nextToNextFinancialYearStart->format('Y') . '-' . $nextToNextFinancialYearEnd->format('y');

    //     $company = Division::select('id', 'name')->where('status', '1')->get();

    //     $dept_data_p = Department::where('status', 1)->paginate(12);
    //     $dept_data = Department::where('status', 1)->pluck('name');

    //     if ($company_id) {
    //         $Nvid = NeedValidation::where('company_id', $company_id)
    //             ->where('fiscal_year', $currentFinancialYear)
    //             ->where("delete_draft", 0)
    //             ->pluck("id");
    //     } elseif ($fiscal_year) {
    //         $Nvid = NeedValidation::where('fiscal_year', $fiscal_year)
    //             ->where("delete_draft", 0)
    //             ->pluck("id");
    //     } else {
    //         $Nvid = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //             ->where("delete_draft", 0)
    //             ->pluck("id");
    //     }
    //     $departmentNVCounts = [];
    //     foreach ($dept_data as $departmentName) {
    //         $department = Department::where('name', $departmentName)->where('status', 1)->first();
    //         if ($department) {
    //             $count = NVService::where('dept_id', $department->id)
    //                 ->whereIn('nv_id', $Nvid)
    //                 ->count() +
    //                 NVMaterial::where('dept_id', $department->id)
    //                 ->whereIn('nv_id', $Nvid)
    //                 ->count();

    //             $departmentNVCounts[$departmentName] = $count;
    //         }
    //     }

    //     $departments_with_group_cio = [];
    //     $departments_without_group_cio = [];
    //     $all_departments = Department::where('status', 1)->get();

    //     foreach ($all_departments as $all_department) {
    //         if (!empty($all_department->group_cio)) {
    //             $departments_with_group_cio[] = $all_department->id;
    //         } else {
    //             $departments_without_group_cio[] = $all_department->id;
    //         }
    //     }

    //     $workflowStages = Workflow::where('status', 1)->get();
    //     $capexApprovalCounts = [];
    //     foreach ($workflowStages as $workflowStage) {

    //         $count = DB::table('capex_workflows_status as current')
    //             ->where('current.department_id', $workflowStage->work_dep)
    //             ->where('current.nv_budget_type', 'CAPEX')
    //             ->where('current.reviewer_name', 'approver')
    //             ->where('current.nv_stage_status', 0)
    //             ->whereIn('current.nv_id', $Nvid)
    //             ->where(function ($query) {
    //                 $query->where(function ($q) {
    //                     // CASE 1: workflow_serial = 1
    //                     $q->where('current.workflow_serial', 1)
    //                         ->whereExists(function ($exists) {
    //                             $exists->select(DB::raw(1))
    //                                 ->from('capex_workflows_status as reviewer')
    //                                 ->where(function ($sub) {
    //                                     $sub->whereNotNull('current.service_id')
    //                                         ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                         ->orWhere(function ($or) {
    //                                             $or->whereNull('current.service_id')
    //                                                 ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                         });
    //                                 })
    //                                 ->where('reviewer.workflow_serial', 1)
    //                                 ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                 ->where('reviewer.nv_stage_status', 1);
    //                         });
    //                 })
    //                     ->orWhere(function ($q) {
    //                         // CASE 2: workflow_serial > 1
    //                         $q->where('current.workflow_serial', '>', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as prev')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('prev.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('prev.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                     ->where('prev.reviewer_name', 'approver')
    //                                     ->where('prev.nv_stage_status', 1);
    //                             });
    //                     });
    //             })
    //             ->count();

    //         $capexApprovalCounts[$workflowStage->work_dep] = $count;
    //     }

    //     $opexWorkflowStages = OpexWorkflow::where('status', 1)->get();
    //     $opexApprovalCounts = [];
    //     foreach ($opexWorkflowStages as $workflowStage) {

    //         $count = DB::table('capex_workflows_status as current')
    //             ->where('current.department_id', $workflowStage->work_dep)
    //             ->where('current.nv_budget_type', 'OPEX')
    //             ->where('current.reviewer_name', 'approver')
    //             ->where('current.nv_stage_status', 0)
    //             ->whereIn('current.nv_id', $Nvid)
    //             ->where(function ($query) {
    //                 $query->where(function ($q) {
    //                     // CASE 1: workflow_serial = 1
    //                     $q->where('current.workflow_serial', 1)
    //                         ->whereExists(function ($exists) {
    //                             $exists->select(DB::raw(1))
    //                                 ->from('capex_workflows_status as reviewer')
    //                                 ->where(function ($sub) {
    //                                     $sub->whereNotNull('current.service_id')
    //                                         ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                         ->orWhere(function ($or) {
    //                                             $or->whereNull('current.service_id')
    //                                                 ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                         });
    //                                 })
    //                                 ->where('reviewer.workflow_serial', 1)
    //                                 ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                 ->where('reviewer.nv_stage_status', 1);
    //                         });
    //                 })
    //                     ->orWhere(function ($q) {
    //                         // CASE 2: workflow_serial > 1
    //                         $q->where('current.workflow_serial', '>', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as prev')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('prev.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('prev.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                     ->where('prev.reviewer_name', 'approver')
    //                                     ->where('prev.nv_stage_status', 1);
    //                             });
    //                     });
    //             })
    //             ->count();

    //         $opexApprovalCounts[$workflowStage->work_dep] = $count;
    //     }


    //     if (!empty($user->role_id == 1)) {
    //         $user = \Auth::user()->id;

    //         $totalId = NeedValidation::whereIn('id', $Nvid)->pluck('id');
    //         $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->get();

    //         $approvedData = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id', $Nvid);
    //         $approvedNV = $approvedData->count();

    //         $totalData = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1);
    //         $totalNV = $totalData->count();

    //         $rejectedData = $latestData->filter(function ($data) {
    //             return $data->is_reject == 1;
    //         });
    //         $rejectedNV = $rejectedData->count();

    //         $pendingData = $latestData->filter(function ($data) {
    //             return in_array($data->ceo_status, [0]) 
    //                 && in_array($data->is_reject, [0]);
    //         });
    //         $pendingNV = $pendingData->count();

    //         $totalAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $pendingAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $rejectedAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $approvedAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $nv_ids = NeedValidation::whereIn('id', $Nvid)->pluck("id");
    //         $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');

    //         if (!empty($total_processed)) {
    //             $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //         } elseif (!empty($total)) {
    //             $nv_sm_data = $nv_sm_data->where('draft',1);
    //         } elseif (!empty($approved)) {
    //             $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //         } elseif (!empty($rejected)) {
    //             $nv_sm_data = $nv_sm_data->whereIn('nv_id', $rejectedData->pluck('nv_id'))->where('is_reject', 1);
    //         } else {
    //             $nv_sm_data = $nv_sm_data->whereIn('nv_id', $pendingData->pluck('nv_id'))->where('is_reject', 0);
    //         }
    //         $nv_sm_data = $nv_sm_data->get();

    //         $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('id', $Nvid)->pluck("id");

    //         $pendingAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $rejectedAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $approvedAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_service.total_buget');


    //         $fileDataBRPL = Nvsericestatus::select(
    //                 DB::raw('MONTH(created_at) as month'),
    //                 DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                 DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
    //                 DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")

    //             )
    //             ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
    //             // ->whereYear('created_at', Carbon::now()->year)
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->get();

    //         $workflowBRPLStages = Workflow::where('status', 1)->get();
    //         $capexBRPLApprovalCounts = [];
    //         foreach ($workflowBRPLStages as $workflowStage) {

    //             $count = DB::table('capex_workflows_status as current')
    //                 ->where('current.department_id', $workflowStage->work_dep)
    //                 ->where('current.nv_budget_type', 'CAPEX')
    //                 ->where('current.reviewer_name', 'approver')
    //                 ->where('current.nv_stage_status', 0)
    //                 ->whereIn('current.nv_id', $BRPLnv)
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         // CASE 1: workflow_serial = 1
    //                         $q->where('current.workflow_serial', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as reviewer')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('reviewer.workflow_serial', 1)
    //                                     ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                     ->where('reviewer.nv_stage_status', 1);
    //                             });
    //                     })
    //                         ->orWhere(function ($q) {
    //                             // CASE 2: workflow_serial > 1
    //                             $q->where('current.workflow_serial', '>', 1)
    //                                 ->whereExists(function ($exists) {
    //                                     $exists->select(DB::raw(1))
    //                                         ->from('capex_workflows_status as prev')
    //                                         ->where(function ($sub) {
    //                                             $sub->whereNotNull('current.service_id')
    //                                                 ->whereColumn('prev.service_id', 'current.service_id')
    //                                                 ->orWhere(function ($or) {
    //                                                     $or->whereNull('current.service_id')
    //                                                         ->whereColumn('prev.material_id', 'current.material_id');
    //                                                 });
    //                                         })
    //                                         ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                         ->where('prev.reviewer_name', 'approver')
    //                                         ->where('prev.nv_stage_status', 1);
    //                                 });
    //                         });
    //                 })
    //                 ->count();

    //             $capexBRPLApprovalCounts[$workflowStage->work_dep] = $count;
    //         }

    //         $opexWorkflowBRPLStages = OpexWorkflow::where('status', 1)->get();
    //         $opexBRPLApprovalCounts = [];
    //         foreach ($opexWorkflowBRPLStages as $workflowStage) {

    //             $count = DB::table('capex_workflows_status as current')
    //                 ->where('current.department_id', $workflowStage->work_dep)
    //                 ->where('current.nv_budget_type', 'OPEX')
    //                 ->where('current.reviewer_name', 'approver')
    //                 ->where('current.nv_stage_status', 0)
    //                 ->whereIn('current.nv_id', $BRPLnv)
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         // CASE 1: workflow_serial = 1
    //                         $q->where('current.workflow_serial', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as reviewer')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('reviewer.workflow_serial', 1)
    //                                     ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                     ->where('reviewer.nv_stage_status', 1);
    //                             });
    //                     })
    //                         ->orWhere(function ($q) {
    //                             // CASE 2: workflow_serial > 1
    //                             $q->where('current.workflow_serial', '>', 1)
    //                                 ->whereExists(function ($exists) {
    //                                     $exists->select(DB::raw(1))
    //                                         ->from('capex_workflows_status as prev')
    //                                         ->where(function ($sub) {
    //                                             $sub->whereNotNull('current.service_id')
    //                                                 ->whereColumn('prev.service_id', 'current.service_id')
    //                                                 ->orWhere(function ($or) {
    //                                                     $or->whereNull('current.service_id')
    //                                                         ->whereColumn('prev.material_id', 'current.material_id');
    //                                                 });
    //                                         })
    //                                         ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                         ->where('prev.reviewer_name', 'approver')
    //                                         ->where('prev.nv_stage_status', 1);
    //                                 });
    //                         });
    //                 })
    //                 ->count();

    //             $opexBRPLApprovalCounts[$workflowStage->work_dep] = $count;
    //         }


    //         $BRPLlabels = [];
    //         $BRPLapprovedData = [];
    //         $BRPLrejectedData = [];
    //         $BRPLpendingData = [];

    //         foreach ($fileDataBRPL as $dataPointBRPL) {
    //             $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //             $BRPLlabels[] = $monthBRPL;
    //             $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //             $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //             $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //         }

    //         $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('id', $Nvid)->pluck("id");

    //         $approvedAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_service.total_buget');

    //         $pendingAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $rejectedAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $fileDataBYPL = Nvsericestatus::select(
    //                 DB::raw('MONTH(created_at) as month'),
    //                 DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                 DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
    //                 DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")

    //             )
    //             ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    //             // ->whereYear('created_at', Carbon::now()->year)
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->get();

    //         $workflowBYPLStages = Workflow::where('status', 1)->get();
    //         $capexBYPLApprovalCounts = [];
    //         foreach ($workflowBYPLStages as $workflowStage) {

    //             $count = DB::table('capex_workflows_status as current')
    //                 ->where('current.department_id', $workflowStage->work_dep)
    //                 ->where('current.nv_budget_type', 'CAPEX')
    //                 ->where('current.reviewer_name', 'approver')
    //                 ->where('current.nv_stage_status', 0)
    //                 ->whereIn('current.nv_id', $BYPLnv)
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         // CASE 1: workflow_serial = 1
    //                         $q->where('current.workflow_serial', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as reviewer')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('reviewer.workflow_serial', 1)
    //                                     ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                     ->where('reviewer.nv_stage_status', 1);
    //                             });
    //                     })
    //                         ->orWhere(function ($q) {
    //                             // CASE 2: workflow_serial > 1
    //                             $q->where('current.workflow_serial', '>', 1)
    //                                 ->whereExists(function ($exists) {
    //                                     $exists->select(DB::raw(1))
    //                                         ->from('capex_workflows_status as prev')
    //                                         ->where(function ($sub) {
    //                                             $sub->whereNotNull('current.service_id')
    //                                                 ->whereColumn('prev.service_id', 'current.service_id')
    //                                                 ->orWhere(function ($or) {
    //                                                     $or->whereNull('current.service_id')
    //                                                         ->whereColumn('prev.material_id', 'current.material_id');
    //                                                 });
    //                                         })
    //                                         ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                         ->where('prev.reviewer_name', 'approver')
    //                                         ->where('prev.nv_stage_status', 1);
    //                                 });
    //                         });
    //                 })
    //                 ->count();

    //             $capexBYPLApprovalCounts[$workflowStage->work_dep] = $count;
    //         }

    //         $opexWorkflowBYPLStages = OpexWorkflow::where('status', 1)->get();
    //         $opexBYPLApprovalCounts = [];
    //         foreach ($opexWorkflowBYPLStages as $workflowStage) {

    //             $count = DB::table('capex_workflows_status as current')
    //                 ->where('current.department_id', $workflowStage->work_dep)
    //                 ->where('current.nv_budget_type', 'OPEX')
    //                 ->where('current.reviewer_name', 'approver')
    //                 ->where('current.nv_stage_status', 0)
    //                 ->whereIn('current.nv_id', $BYPLnv)
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         // CASE 1: workflow_serial = 1
    //                         $q->where('current.workflow_serial', 1)
    //                             ->whereExists(function ($exists) {
    //                                 $exists->select(DB::raw(1))
    //                                     ->from('capex_workflows_status as reviewer')
    //                                     ->where(function ($sub) {
    //                                         $sub->whereNotNull('current.service_id')
    //                                             ->whereColumn('reviewer.service_id', 'current.service_id')
    //                                             ->orWhere(function ($or) {
    //                                                 $or->whereNull('current.service_id')
    //                                                     ->whereColumn('reviewer.material_id', 'current.material_id');
    //                                             });
    //                                     })
    //                                     ->where('reviewer.workflow_serial', 1)
    //                                     ->where('reviewer.reviewer_name', '!=', 'approver')
    //                                     ->where('reviewer.nv_stage_status', 1);
    //                             });
    //                     })
    //                         ->orWhere(function ($q) {
    //                             // CASE 2: workflow_serial > 1
    //                             $q->where('current.workflow_serial', '>', 1)
    //                                 ->whereExists(function ($exists) {
    //                                     $exists->select(DB::raw(1))
    //                                         ->from('capex_workflows_status as prev')
    //                                         ->where(function ($sub) {
    //                                             $sub->whereNotNull('current.service_id')
    //                                                 ->whereColumn('prev.service_id', 'current.service_id')
    //                                                 ->orWhere(function ($or) {
    //                                                     $or->whereNull('current.service_id')
    //                                                         ->whereColumn('prev.material_id', 'current.material_id');
    //                                                 });
    //                                         })
    //                                         ->where('prev.workflow_serial', DB::raw('current.workflow_serial - 1'))
    //                                         ->where('prev.reviewer_name', 'approver')
    //                                         ->where('prev.nv_stage_status', 1);
    //                                 });
    //                         });
    //                 })
    //                 ->count();

    //             $opexBYPLApprovalCounts[$workflowStage->work_dep] = $count;
    //         }


    //         $BYPLlabels = [];
    //         $BYPLapprovedData = [];
    //         $BYPLrejectedData = [];
    //         $BYPLpendingData = [];
    //         foreach ($fileDataBYPL as $dataPointBYPL) {
    //             $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    //             $BYPLlabels[] = $monthBYPL;
    //             $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //             $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //             $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //         }

    //         return view("admin.dashboard", compact(
    //             "dept_data",
    //             "dept_data_p",
    //             "currentFinancialYear",
    //             "nextFinancialYear",
    //             "nextToNextFinancialYear",
    //             "departmentNVCounts",
    //             "approvedAmount",
    //             "rejectedAmount",
    //             "pendingAmount",
    //             "totalAmount",
    //             "company",
    //             "company_id",
    //             "nv_sm_data",
    //             "totalNV",
    //             "approvedNV",
    //             "rejectedNV",
    //             "pendingNV",
    //             'BRPLlabels',
    //             'BRPLapprovedData',
    //             'BRPLrejectedData',
    //             'BRPLpendingData',
    //             'BYPLlabels',
    //             'BYPLapprovedData',
    //             'BYPLrejectedData',
    //             'BYPLpendingData',
    //             'approvedAmountBRPL',
    //             'pendingAmountBRPL',
    //             'rejectedAmountBRPL',
    //             'approvedAmountBYPL',
    //             'pendingAmountBYPL',
    //             'rejectedAmountBYPL',
    //             'capexApprovalCounts',
    //             'opexApprovalCounts',
    //             'workflowStages',
    //             'opexWorkflowStages',
    //             'workflowBRPLStages',
    //             'opexWorkflowBRPLStages',
    //             'capexBRPLApprovalCounts',
    //             'opexBRPLApprovalCounts',
    //             'workflowBYPLStages',
    //             'opexWorkflowBYPLStages',
    //             'capexBYPLApprovalCounts',
    //             'opexBYPLApprovalCounts'
    //         ));
    //     } elseif ($user->role_id == 9) {

    //         if ($fiscal_year) {
    //             $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->pluck('id');
    //         } else {
    //             $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck('id');
    //         }

    //         $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();

    //         $approvedData = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id', $totalId);
    //         $approvedNV = $approvedData->count();

    //         $totalData = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1);
    //         $totalNV = $totalData->count();

    //         $rejectedData = $latestData->filter(function ($data) {
    //             return $data->is_reject == 1;
    //         });
    //         $rejectedNV = $rejectedData->count();

    //         $pendingData = $latestData->filter(function ($data) {
    //             return in_array($data->ceo_status, [0])
    //                 && in_array($data->is_reject, [0]);
    //         });
    //         $pendingNV = $pendingData->count();


    //         $totalAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both')
    //             + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $totalData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $pendingAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $rejectedAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $approvedAmount = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $totalId)
    //             ->whereIn('nvservicestatus.nv_id', $approvedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         if ($fiscal_year) {
    //             $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->get();
    //         } else {
    //             $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->get();
    //         }
    //         $nv_ids = $nv->pluck('id');

    //         $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');


    //         if (!empty($total_processed)) {
    //             $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //         } elseif (!empty($total)) {
    //             $nv_sm_data = $nv_sm_data;
    //         } elseif (!empty($approved)) {
    //             $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //         } elseif (!empty($rejected)) {
    //             $nv_sm_data = $nv_sm_data->whereIn('nv_id', $rejectedData->pluck('nv_id'))->where('is_reject', 1);
    //         } else {
    //             $nv_sm_data = $nv_sm_data->whereIn('nv_id', $pendingData->pluck('nv_id'))->where('is_reject', 0);
    //         }
    //         $nv_sm_data = $nv_sm_data->get();

    //         if ($fiscal_year) {
    //             $BRPLnv = NeedValidation::where('company_id', '6')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->pluck("id");
    //         } else {
    //             $BRPLnv = NeedValidation::where('company_id', '6')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck("id");
    //         }
    //         $pendingAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $rejectedAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $approvedAmountBRPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_service.total_buget');


    //         $fileDataBRPL = Nvsericestatus::select(
    //                 DB::raw('MONTH(created_at) as month'),
    //                 DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                 DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
    //                 DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")

    //             )
    //             ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
    //             // ->whereYear('created_at', Carbon::now()->year)
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->get();

    //         $BRPLlabels = [];
    //         $BRPLapprovedData = [];
    //         $BRPLrejectedData = [];
    //         $BRPLpendingData = [];

    //         foreach ($fileDataBRPL as $dataPointBRPL) {
    //             $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //             $BRPLlabels[] = $monthBRPL;
    //             $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //             $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //             $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //         }
    //         if ($fiscal_year) {
    //             $BYPLnv = NeedValidation::where('company_id', '5')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where('delete_draft', 0)->pluck("id");
    //         } else {
    //             $BYPLnv = NeedValidation::where('company_id', '5')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where('delete_draft', 0)->pluck("id");
    //         }

    //         $pendingAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $pendingData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');

    //         $rejectedAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)
    //             ->whereIn('nvservicestatus.nv_id', $rejectedData->pluck('nv_id'))
    //             ->sum('tbl_service.total_buget');


    //         $approvedAmountBYPL = DB::table('tbl_material')
    //             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //             ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //             ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //             ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //             ->sum('tbl_service.total_buget');

    //         $fileDataBYPL = Nvsericestatus::select(
    //                 DB::raw('MONTH(created_at) as month'),
    //                 DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                 DB::raw("SUM(CASE WHEN (is_reject = '1') THEN 1 ELSE 0 END) as rejected_count"),
    //                 DB::raw("SUM(CASE WHEN (ceo_status = '0' AND is_reject = '0') THEN 1 ELSE 0 END) as pending_count")

    //             )
    //             ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    //             // ->whereYear('created_at', Carbon::now()->year)
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->get();

    //         $BYPLlabels = [];
    //         $BYPLapprovedData = [];
    //         $BYPLrejectedData = [];
    //         $BYPLpendingData = [];


    //         foreach ($fileDataBYPL as $dataPointBYPL) {
    //             $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    //             $BYPLlabels[] = $monthBYPL;
    //             $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //             $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //             $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //         }

    //         $userid = \Auth::user()->id;
    //         $log = DB::table('log_signature')->where('user_id', $userid)->select('created_at', 'signature_id')->get();

    //         return view("admin.dashboard", compact(
    //             "approvedAmount",
    //             "currentFinancialYear",
    //             "nextFinancialYear",
    //             "nextToNextFinancialYear",
    //             "rejectedAmount",
    //             "pendingAmount",
    //             "totalAmount",
    //             "company",
    //             "company_id",
    //             "nv_sm_data",
    //             "totalNV",
    //             "approvedNV",
    //             "rejectedNV",
    //             "pendingNV",
    //             'BRPLlabels',
    //             'BRPLapprovedData',
    //             'BRPLrejectedData',
    //             'BRPLpendingData',
    //             'BYPLlabels',
    //             'BYPLapprovedData',
    //             'BYPLrejectedData',
    //             'BYPLpendingData',
    //             'log',
    //             'pendingAmountBYPL',
    //             'pendingAmountBRPL',
    //             'approvedAmountBYPL',
    //             'approvedAmountBRPL',
    //             'rejectedAmountBYPL',
    //             'rejectedAmountBRPL'
    //         ));
    //     } else {
    //         $rejectedNV = $approvedNV = $totalNV = $totalAmount = $pendingAmount =
    //             $rejectedAmount = $approvedAmount = $pendingNV = $pendingAmountBYPL =
    //             $pendingAmountBRPL = $approvedAmountBYPL = $approvedAmountBRPL = $rejectedAmountBYPL = $rejectedAmountBRPL = 0;
    //         $BYPLpendingData = $BYPLrejectedData = $BYPLlabels = $BYPLapprovedData = $BRPLpendingData = $BRPLrejectedData =
    //             $BRPLlabels = $BRPLapprovedData = $nv_sm_data = [];

    //         $employee = Employee::where('user_id', $user->id)->with('department')->first();
    //         $allusers = Employee::where('department_id', $employee->department_id)->get();
    //         $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');
    //         $employees = Employee::where("user_id", $user->id)->first();
    //         $departmentIds = explode(',', $employees->department_id);
    //         $departments = Department::whereIn("id", $departmentIds)->get();
            
    //         foreach ($departments as $dep) {
    //             $dep_id = $dep->id;
    //             $hod = $dep->dep_hod;
    //             $dep_rew1 = $dep->dep_rew1;
    //             $dep_rew2 = $dep->dep_rew2;
    //             $dep_rew3 = $dep->dep_rew3;
    //             $dep_rew4 = $dep->dep_rew4;
    //             $group_cio = $dep->group_cio;
    //             $department = optional($employee->department);
    //             $Values = [$hod, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];
    //             $departmentIds = explode(',', $user->department_id);
    //             if (!empty($dep_rew1) && $dep_rew1 == $user->id) {
    //                 $Values = [$user->id, $dep_rew1];
    //                 $departmentIds = Department::where("dep_rew1", $user->id)->pluck('id');
    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 }
    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();
    //                 $rejectedNV = $latestData->filter(function ($data) {
    //                     return in_array($data->ceo_status, [2]) ||
    //                         $data->rv1_status == 2;
    //                 })->count();
    //                 $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft', 1)->where('rv1_status', 0)->count();



    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv1_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv1_status', 1)
    //                     ->sum('tbl_service.total_buget');


    //                 $pendingAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');


    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 if ($fiscal_year) {
    //                     $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 } else {
    //                     $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 }
    //                 $nv_ids = $nv->pluck('id');
    //                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft', 1)->with(['service', 'material', 'user'])->orderBy('id', 'asc');


    //                 if (!empty($total_processed)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv1_status', 1);
    //                 } elseif (!empty($total)) {
    //                     $nv_sm_data = $nv_sm_data;
    //                 } elseif (!empty($approved)) {
    //                     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                 } elseif (!empty($rejected)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv1_status', 2);
    //                 } else {
    //                     $nv_sm_data = $nv_sm_data->where('rv1_status', 0);
    //                 }
    //                 $nv_sm_data = $nv_sm_data->get();

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }
    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');
    //                 $fileDataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )

    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();

    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }
    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }
    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');
    //                 $fileDataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" )  THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();
    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }
    //                 return view("admin.dashboard", compact(
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "approvedAmount",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             } elseif (!empty($dep_rew2) && $dep_rew2 == $user->id) {
    //                 $Values = [$user->id, $dep_rew1];
    //                 // $departmentIds = explode(',', $user->department_id);
    //                 $departmentIds = Department::where("dep_rew2", $user->id)->pluck('id');
    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 }

    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();
    //                 $rejectedNV = $latestData->filter(function ($data) {
    //                     return in_array($data->ceo_status, [2]) ||
    //                         $data->rv2_status == 2;
    //                 })->count();

    //                 $pendingNV = $latestData->filter(function ($data) use ($dep_rew1) {
    //                     if (!empty($dep_rew1)) {
    //                         return $data->rv1_status == 1 && $data->rv2_status == 0;
    //                     } else {
    //                         return $data->draft == 1 && $data->rv2_status == 0 && $data->rv1_status != 2;
    //                     }
    //                 })->count();
    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->count();


    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv2_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv2_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $pendingAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');


    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 if ($fiscal_year) {
    //                     $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 } else {
    //                     $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 }

    //                 $nv_ids = $nv->pluck('id');
    //                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('draft', 1)
    //                             ->where('rv1_status', '!=', 2);
    //                     })
    //                     ->orderBy('id', 'asc');


    //                 if (!empty($total_processed)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv2_status', 1);
    //                 } elseif (!empty($total)) {
    //                     $nv_sm_data = $nv_sm_data;
    //                 } elseif (!empty($approved)) {
    //                     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                 } elseif (!empty($rejected)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv2_status', 2);
    //                 } else {
    //                     $nv_sm_data = $nv_sm_data->where('rv2_status', 0);
    //                 }
    //                 $nv_sm_data = $nv_sm_data->get();

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');


    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');


    //                 $DataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv2_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )

    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');
    //                 if (!empty($dep_rew1)) {
    //                     $DataBRPL->where('rv1_status', 1);
    //                 } else {
    //                     $DataBRPL->where('draft', 1)
    //                         ->where('rv1_status', '!=', 2);;
    //                 }
    //                 $fileDataBRPL = $DataBRPL->get();


    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }
    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv2_status', 0)
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         return $query->where('nvservicestatus.rv1_status', 1);
    //                     }, function ($query) {
    //                         return $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $DataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv2_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');
    //                 if (!empty($dep_rew1)) {
    //                     $DataBYPL->where('rv1_status', 1);
    //                 } else {
    //                     $DataBYPL->where('draft', 1)
    //                         ->where('rv1_status', '!=', 2);
    //                 }
    //                 $fileDataBYPL = $DataBYPL->get();


    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }

    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             } elseif (!empty($dep_rew3) && $dep_rew3 == $user->id) {
    //                 $Values = [$user->id, $dep_rew1, $dep_rew2];
    //                 // $departmentIds = explode(',', $user->department_id);
    //                 $departmentIds = Department::where("dep_rew3", $user->id)->pluck('id');
    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 }

    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();

    //                 $rejectedNV = $latestData->filter(function ($data) {
    //                     return in_array($data->ceo_status, [2]) ||
    //                         $data->rv3_status == 2;
    //                 })->count();

    //                 $pendingNV = $latestData->filter(function ($data) use ($dep_rew1, $dep_rew2) {
    //                     if (!empty($dep_rew2)) {
    //                         return $data->rv2_status == 1 &&
    //                             $data->rv3_status == 0;
    //                     } elseif (!empty($dep_rew1)) {
    //                         return $data->rv1_status == 1 &&
    //                             $data->rv3_status == 0 &&
    //                             $data->rv2_status != 2;
    //                     } else {
    //                         return $data->draft == 1 &&
    //                             $data->rv3_status == 0 &&
    //                             $data->rv1_status != 2 &&
    //                             $data->rv2_status != 2;
    //                     }
    //                 })->count();
    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->count();


    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv3_status', 1)
    //                     ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv3_status', 1)
    //                     ->sum('tbl_service.total_buget');


    //                 $pendingAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');


    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 if ($fiscal_year) {
    //                     $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 } else {
    //                     $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 }

    //                 $nv_ids = $nv->pluck('id');

    //                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('rv1_status', 1)
    //                                 ->where('rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('draft', 1)
    //                                 ->where('rv1_status', '!=', 2)
    //                                 ->where('rv2_status', '!=', 2);
    //                         });
    //                     })
    //                     ->orderBy('id', 'asc');

    //                 if (!empty($total_processed)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv3_status', 1);
    //                 } elseif (!empty($total)) {
    //                     $nv_sm_data = $nv_sm_data;
    //                 } elseif (!empty($approved)) {
    //                     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                 } elseif (!empty($rejected)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv3_status', 2);
    //                 } else {
    //                     $nv_sm_data = $nv_sm_data->where('rv3_status', 0);
    //                 }
    //                 $nv_sm_data = $nv_sm_data->get();

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_service.total_buget');



    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $DataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv3_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )

    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');
    //                 if (!empty($dep_rew2)) {
    //                     $DataBRPL->where('rv2_status', 1);
    //                 } elseif (!empty($dep_rew1)) {
    //                     $DataBRPL->where('rv1_status', 1)
    //                         ->where('rv2_status', '!=', 2);
    //                 } else {
    //                     $DataBRPL->where('draft', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv1_status', '!=', 2);
    //                 }
    //                 $fileDataBRPL = $DataBRPL->get();

    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }
    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv3_status', 0)
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         return $query->where('nvservicestatus.rv2_status', 1);
    //                     }, function ($query) use ($dep_rew1) {
    //                         return $query->when(!empty($dep_rew1), function ($query) {
    //                             return $query->where('nvservicestatus.rv1_status', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2);
    //                         }, function ($query) {
    //                             return $query->where('nvservicestatus.draft', 1)
    //                                 ->where('nvservicestatus.rv2_status', '!=', 2)
    //                                 ->where('nvservicestatus.rv1_status', '!=', 2);
    //                         });
    //                     })
    //                     ->sum('tbl_service.total_buget');



    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $DataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');
    //                 if (!empty($dep_rew2)) {
    //                     $DataBYPL->where('rv2_status', 1);
    //                 } elseif (!empty($dep_rew1)) {
    //                     $DataBYPL->where('rv1_status', 1)
    //                         ->where('rv2_status', '!=', 2);
    //                 } else {
    //                     $DataBYPL->where('draft', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv1_status', '!=', 2);
    //                 }
    //                 $fileDataBYPL = $DataBYPL->get();

    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }
    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             } elseif (!empty($dep_rew4) && $dep_rew4 == $user->id) {
    //                 $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3];
    //                 // $departmentIds = explode(',', $user->department_id);
    //                 $departmentIds = Department::where("dep_rew4", $user->id)->pluck('id');
    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 }


    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();

    //                 $rejectedNV = $latestData->filter(function ($data) {
    //                     return in_array($data->ceo_status, [2]) ||
    //                         $data->rv4_status == 2;
    //                 })->count();

    //                 $pendingNV = $latestData->filter(function ($data) {
    //                     return  in_array($data->draft, [1]) &&
    //                         in_array($data->rv4_status, [0]);
    //                 })->count();

    //                 $pendingNV = $latestData->filter(function ($data) use ($dep_rew1, $dep_rew2, $dep_rew3) {
    //                     if (!empty($dep_rew3)) {
    //                         return $data->rv3_status == 1 &&
    //                             $data->rv4_status == 0;
    //                     } elseif (!empty($dep_rew2)) {
    //                         return $data->rv2_status == 1 &&
    //                             $data->rv4_status == 0 &&
    //                             $data->rv3_status != 2;
    //                     } elseif (!empty($dep_rew1)) {
    //                         return $data->rv1_status == 1 &&
    //                             $data->rv4_status == 0 &&
    //                             $data->rv2_status != 2 &&
    //                             $data->rv3_status != 2;
    //                     } else {
    //                         return $data->draft == 1 &&
    //                             $data->rv4_status == 0 &&
    //                             $data->rv1_status != 2 &&
    //                             $data->rv2_status != 2 &&
    //                             $data->rv3_status != 2;
    //                     }
    //                 })->count();


    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->count();

    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv4_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv4_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $pendingAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');



    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');


    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 if ($fiscal_year) {
    //                     $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 } else {
    //                     $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
    //                         $query->where('user_id', $user->id)
    //                             ->orWhereIn('user_id', $allNormalUsers)
    //                             ->orWhereIn('department_id', $departmentIds);
    //                     })
    //                         ->whereHas('service')
    //                         ->select('id')
    //                         ->get();
    //                 }
    //                 $nv_ids = $nv->pluck('id');

    //                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('rv2_status', 1)
    //                             ->where('rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('rv1_status', 1)
    //                             ->where('rv2_status', '!=', 2)
    //                             ->where('rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('draft', 1)
    //                             ->where('rv2_status', '!=', 2)
    //                             ->where('rv1_status', '!=', 2)
    //                             ->where('rv3_status', '!=', 2);
    //                     })
    //                     ->orderBy('id', 'asc');


    //                 if (!empty($total_processed)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv4_status', 1);
    //                 } elseif (!empty($total)) {
    //                     $nv_sm_data = $nv_sm_data;
    //                 } elseif (!empty($approved)) {
    //                     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                 } elseif (!empty($rejected)) {
    //                     $nv_sm_data = $nv_sm_data->where('rv4_status', 2);
    //                 } else {
    //                     $nv_sm_data = $nv_sm_data->where('rv4_status', 0);
    //                 }
    //                 $nv_sm_data = $nv_sm_data->get();

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $DataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )

    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');

    //                 if (!empty($dep_rew3)) {
    //                     $DataBRPL->where('rv3_status', 1);
    //                 } elseif (!empty($dep_rew2)) {
    //                     $DataBRPL->where('rv2_status', 1)
    //                         ->where('rv3_status', '!=', 2);
    //                 } elseif (!empty($dep_rew1)) {
    //                     $DataBRPL->where('rv1_status', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2);
    //                 } else {
    //                     $DataBRPL->where('draft', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv1_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2);
    //                 }
    //                 $fileDataBRPL = $DataBRPL->get();


    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }
    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id', '5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv4_status', 0)
    //                     ->when(!empty($dep_rew3), function ($query) {
    //                         $query->where('nvservicestatus.rv3_status', 1);
    //                     })
    //                     ->when(!empty($dep_rew2), function ($query) {
    //                         $query->where('nvservicestatus.rv2_status', 1)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(!empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.rv1_status', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->when(empty($dep_rew3) && empty($dep_rew2) && empty($dep_rew1), function ($query) {
    //                         $query->where('nvservicestatus.draft', 1)
    //                             ->where('nvservicestatus.rv2_status', '!=', 2)
    //                             ->where('nvservicestatus.rv1_status', '!=', 2)
    //                             ->where('nvservicestatus.rv3_status', '!=', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.rv4_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $DataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN ( rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])

    //                     // ->whereYear('created_at', Carbon::now()->year)
    //                     ->groupBy('month')
    //                     ->orderBy('month');
    //                 if (!empty($dep_rew3)) {
    //                     $DataBYPL->where('rv3_status', 1);
    //                 } elseif (!empty($dep_rew2)) {
    //                     $DataBYPL->where('rv2_status', 1)
    //                         ->where('rv3_status', '!=', 2);
    //                 } elseif (!empty($dep_rew1)) {
    //                     $DataBYPL->where('rv1_status', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2);
    //                 } else {
    //                     $DataBYPL->where('draft', 1)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv1_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2);
    //                 }
    //                 $fileDataBYPL = $DataBYPL->get();


    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }

    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             }
    //                           // GROUP CIO BLOCK (Check Database Directly + Priority 1)
    //             elseif (Department::where("group_cio", $user->id)->exists()) {
                    
    //                 $departmentIds = Department::where("group_cio", $user->id)->pluck('id');

    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck('id');
    //                 }

    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 1)->count();
    //                 $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->count();
    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();

    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->where('nvservicestatus.groupcio_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->where('nvservicestatus.groupcio_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $pen_amt = $latestData->filter(function ($data) {
    //                     return in_array($data->hod_status, [1]) &&
    //                         in_array($data->groupcio_status, [0]);
    //                 });
    //                 $pendingAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->orWhere('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->orWhere('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $pendingNV = $latestData->filter(function ($data) {
    //                     return in_array($data->hod_status, [1]) &&
    //                         in_array($data->groupcio_status, [0]);
    //                 })->count();

    //                 if ($fiscal_year) {
    //                     $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->get();
    //                 } else {
    //                     $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->get();
    //                 }

    //                 $nv_ids = $nv->pluck('id');
    //                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
    //                     ->where('hod_status', 1)
    //                     ->orderBy('id', 'asc');

    //                 if (!empty($total_processed)) {
    //                     $nv_sm_data = $nv_sm_data->where('groupcio_status', 1);
    //                 } elseif (!empty($total)) {
    //                     $nv_sm_data = $nv_sm_data;
    //                 } elseif (!empty($approved)) {
    //                     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                 } elseif (!empty($rejected)) {
    //                     $nv_sm_data = $nv_sm_data->where('groupcio_status', 2);
    //                 } else {
    //                     $nv_sm_data = $nv_sm_data->where('groupcio_status', 0);
    //                 }
    //                 $nv_sm_data = $nv_sm_data->get();

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $fileDataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( groupcio_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (groupcio_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
    //                     ->where('hod_status', 1)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();

    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }

    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.groupcio_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $fileDataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN (groupcio_status = "2") THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (groupcio_status = "0") THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    //                     ->where('hod_status', 1)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();

    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }
    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'rejectedAmountBYPL',
    //                     'pendingAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             }
                
    //             // HOD BLOCK (Check Database Directly + Priority 2)
    //             elseif (Department::where("dep_hod", $user->id)->exists()) {
                    
    //                 $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];
    //                 $departmentIds = Department::where("dep_hod", $user->id)->pluck('id');
                    
    //                 if ($fiscal_year) {
    //                     $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 } else {
    //                     $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
    //                         ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
    //                             $query->whereIn('user_id', $Values)
    //                                 ->orWhereIn('user_id', $allNormalUsers)
    //                                 ->orWhereIn('department_id', $departmentIds);
    //                         })
    //                         ->pluck('id');
    //                 }

    //                 $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();

    //                 $totalAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.hod_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where('nvservicestatus.hod_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmount = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmount = DB::table('tbl_material')
    //                     ->whereIn('tbl_material.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->whereIn('tbl_service.nv_id', $totalId)
    //                     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedNV = $latestData->where('ceo_status', 1)->count();
    //                 $rejectedNV = $latestData->filter(function ($data) {
    //                     return $data->hod_status == 2;
    //                 })->count();
    //                 $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->count();

    //                 $nv_sm_data = array();
    //                 $nv_statuses = array();
    //                 $nv_id = [];
    //                 $id = [];
    //                 $pendingAmount = 0;
    //                 $pendingNV = 0;

    //                 $depart = Department::whereIn('id', $departmentIds)->get();

    //                 foreach ($depart as $depart) {
    //                     $dep_rew4 = $depart->dep_rew4;
    //                     $dep_rew3 = $depart->dep_rew3;
    //                     $dep_rew2 = $depart->dep_rew2;
    //                     $dep_rew1 = $depart->dep_rew1;
    //                     $dep_hod = $depart->dep_hod;
    //                     if (!empty($dep_hod)) {
    //                         if (!empty($dep_rew4)) {
    //                             $nv_status = Nvsericestatus::where(function ($query) {
    //                                 $query->where('rv4_status', 1);
    //                             })
    //                                 ->where(function ($query) {
    //                                     $query
    //                                         ->where('rv4_status', '!=', 2);
    //                                 })
    //                                 ->get();
    //                             if (count($nv_status) > 0) {
    //                                 $nv_statuses[] = $nv_status;
    //                             }
    //                         } elseif (!empty($dep_rew3)) {
    //                             $nv_status = Nvsericestatus::where(function ($query) {
    //                                 $query->where('rv3_status', 1);
    //                             })
    //                                 ->where(function ($query) {
    //                                     $query
    //                                         ->where('rv3_status', '!=', 2);
    //                                 })
    //                                 ->get();
    //                             if (count($nv_status) > 0) {
    //                                 $nv_statuses[] = $nv_status;
    //                             }
    //                         } elseif (!empty($dep_rew2)) {
    //                             $nv_status = Nvsericestatus::where(function ($query) {
    //                                 $query->where('rv2_status', 1);
    //                             })
    //                                 ->where(function ($query) {
    //                                     $query
    //                                         ->where('rv2_status', '!=', 2);
    //                                 })
    //                                 ->get();
    //                             if (count($nv_status) > 0) {
    //                                 $nv_statuses[] = $nv_status;
    //                             }
    //                         } elseif (!empty($dep_rew1)) {
    //                             $nv_status = Nvsericestatus::where(function ($query) {
    //                                 $query->where('rv1_status', 1);
    //                             })
    //                                 ->where(function ($query) {
    //                                     $query
    //                                         ->where('rv1_status', '!=', 2);
    //                                 })
    //                                 ->get();
    //                             if (count($nv_status) > 0) {
    //                                 $nv_statuses[] = $nv_status;
    //                             }
    //                         } else {
    //                             $nv_status = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
    //                                 ->join('department', 'department.id', '=', 'needvalidations.department_id')
    //                                 ->where('department.id', $depart->id)
    //                                 ->where('nvservicestatus.draft', 1)
    //                                 ->get();

    //                             if (count($nv_status) > 0) {
    //                                 $nv_statuses[] = $nv_status;
    //                             }
    //                         }
    //                     }
    //                 }
    //                 if (!empty($nv_statuses)) {
    //                     foreach ($nv_statuses as $idx => $nv_status) {
    //                         foreach ($nv_status as $data) {
    //                             array_push($nv_id, $data["nv_id"]);
    //                             array_push($id, $data["id"]);
    //                         }
    //                     }
    //                     if ($fiscal_year) {
    //                         $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft', 0)
    //                             ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
    //                     } else {
    //                         $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft', 0)
    //                             ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
    //                     }
    //                     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])
    //                         ->where('rv1_status', '!=', 2)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2)
    //                         ->where('rv4_status', '!=', 2)
    //                         ->orderBy('id', 'asc');

    //                     if (!empty($total_processed)) {
    //                         $nv_sm_data = $nv_sm_data->where('hod_status', 1);
    //                     } elseif (!empty($total)) {
    //                         $nv_sm_data = $nv_sm_data;
    //                     } elseif (!empty($approved)) {
    //                         $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                     } elseif (!empty($rejected)) {
    //                         $nv_sm_data = $nv_sm_data->where('hod_status', 2);
    //                     } else {
    //                         $nv_sm_data = $nv_sm_data->where('hod_status', 0);
    //                     }
    //                     $nv_sm_data = $nv_sm_data->get();

    //                     $pendingAmount = DB::table('tbl_material')
    //                         ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                         ->whereIn('tbl_material.nv_id', $nv_id)
    //                         ->whereIn('nvservicestatus.nv_id', $nv1)
    //                         ->where('nvservicestatus.rv1_status', '!=', 2)
    //                         ->where('nvservicestatus.rv2_status', '!=', 2)
    //                         ->where('nvservicestatus.rv3_status', '!=', 2)
    //                         ->where('nvservicestatus.rv4_status', '!=', 2)
    //                         ->where('nvservicestatus.hod_status', 0)
    //                         ->where('nvservicestatus.draft', 1)
    //                         ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                         ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                         ->whereIn('tbl_service.nv_id', $nv_id)
    //                         ->whereIn('nvservicestatus.nv_id', $nv1)
    //                         ->where('nvservicestatus.rv1_status', '!=', 2)
    //                         ->where('nvservicestatus.rv2_status', '!=', 2)
    //                         ->where('nvservicestatus.rv3_status', '!=', 2)
    //                         ->where('nvservicestatus.rv4_status', '!=', 2)
    //                         ->where('nvservicestatus.hod_status', 0)
    //                         ->where('nvservicestatus.draft', 1)
    //                         ->sum('tbl_service.total_buget');

    //                     $pendingNV = Nvsericestatus::whereIn("nv_id", $nv1)
    //                         ->where('draft', 1)
    //                         ->where('hod_status', 0)
    //                         ->where('rv1_status', '!=', 2)
    //                         ->where('rv2_status', '!=', 2)
    //                         ->where('rv3_status', '!=', 2)
    //                         ->where('rv4_status', '!=', 2)->count();
    //                 }

    //                 if ($fiscal_year) {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id', '6')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id', '6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv1_status', '!=', 2)
    //                     ->where('nvservicestatus.rv2_status', '!=', 2)
    //                     ->where('nvservicestatus.rv3_status', '!=', 2)
    //                     ->where('nvservicestatus.rv4_status', '!=', 2)
    //                     ->where('nvservicestatus.hod_status', 0)
    //                     ->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where('nvservicestatus.rv1_status', '!=', 2)
    //                     ->where('nvservicestatus.rv2_status', '!=', 2)
    //                     ->where('nvservicestatus.rv3_status', '!=', 2)
    //                     ->where('nvservicestatus.rv4_status', '!=', 2)
    //                     ->where('nvservicestatus.hod_status', 0)
    //                     ->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBRPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $fileDataBRPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( hod_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (hod_status = "0" AND draft = "1" ) THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
    //                     ->where('rv1_status', '!=', 2)
    //                     ->where('rv2_status', '!=', 2)
    //                     ->where('rv3_status', '!=', 2)
    //                     ->where('rv4_status', '!=', 2)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();

    //                 $BRPLlabels = [];
    //                 $BRPLapprovedData = [];
    //                 $BRPLrejectedData = [];
    //                 $BRPLpendingData = [];

    //                 foreach ($fileDataBRPL as $dataPointBRPL) {
    //                     $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
    //                     $BRPLlabels[] = $monthBRPL;
    //                     $BRPLapprovedData[] = $dataPointBRPL->approved_count;
    //                     $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
    //                     $BRPLpendingData[] = $dataPointBRPL->pending_count;
    //                 }
                    
    //                 if ($fiscal_year) {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id', '5')->where('fiscal_year', $fiscal_year)->pluck("id");
    //                 } else {
    //                     $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id', '5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    //                 }

    //                 $pendingAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv1_status', '!=', 2)
    //                     ->where('nvservicestatus.rv2_status', '!=', 2)
    //                     ->where('nvservicestatus.rv3_status', '!=', 2)
    //                     ->where('nvservicestatus.rv4_status', '!=', 2)
    //                     ->where('nvservicestatus.hod_status', 0)
    //                     ->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where('nvservicestatus.rv1_status', '!=', 2)
    //                     ->where('nvservicestatus.rv2_status', '!=', 2)
    //                     ->where('nvservicestatus.rv3_status', '!=', 2)
    //                     ->where('nvservicestatus.rv4_status', '!=', 2)
    //                     ->where('nvservicestatus.hod_status', 0)
    //                     ->where('nvservicestatus.draft', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $rejectedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                     ->where(function ($query) {
    //                         $query->where('nvservicestatus.hod_status', 2);
    //                     })
    //                     ->sum('tbl_service.total_buget');

    //                 $approvedAmountBYPL = DB::table('tbl_material')
    //                     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    //                     ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                     ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    //                     ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    //                     ->sum('tbl_service.total_buget');

    //                 $fileDataBYPL = Nvsericestatus::select(
    //                         DB::raw('MONTH(created_at) as month'),
    //                         DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
    //                         DB::raw('SUM(CASE WHEN ( hod_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
    //                         DB::raw('SUM(CASE WHEN (hod_status = "0" AND draft = "1" )  THEN 1 ELSE 0 END) as pending_count'),
    //                     )
    //                     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    //                     ->where('rv1_status', '!=', 2)
    //                     ->where('rv2_status', '!=', 2)
    //                     ->where('rv3_status', '!=', 2)
    //                     ->where('rv4_status', '!=', 2)
    //                     ->groupBy('month')
    //                     ->orderBy('month')
    //                     ->get();

    //                 $BYPLlabels = [];
    //                 $BYPLapprovedData = [];
    //                 $BYPLrejectedData = [];
    //                 $BYPLpendingData = [];

    //                 foreach ($fileDataBYPL as $dataPointBYPL) {
    //                     $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    //                     $BYPLlabels[] = $monthBYPL;
    //                     $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    //                     $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    //                     $BYPLpendingData[] = $dataPointBYPL->pending_count;
    //                 }

    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL'
    //                 ));
    //             } else {
    //                 $workflows = DB::table('capex_workflows_status')->get();

    //                 if ($workflows->isNotEmpty()) {

    //                     foreach ($workflows as $workflow) {

    //                         $capexWorkflowUsers = $workflow->workflow_user_id;
    //                         $year = !empty($fiscal_year) ? $fiscal_year : $currentFinancialYear;

    //                         if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {
    //                             $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('transfer_to_nominee1', 0)->get();
    //                             $capexNvIds = [];
    //                             $opexNvIds = [];

    //                             foreach ($nv_workflows as $nvs) {
    //                                 $nv_id = $nvs->nv_id;
    //                                 $workflow_serial = $nvs->workflow_serial;
    //                                 $budget_type = $nvs->nv_budget_type;
    //                                 $isApprover = strtolower($nvs->reviewer_name) === 'approver';

    //                                 $addToResult = false;

    //                                 if ($isApprover) {
    //                                     $reviewers = DB::table('capex_workflows_status')
    //                                         ->where('nv_id', $nv_id)
    //                                         ->where('workflow_serial', $workflow_serial)
    //                                         ->where('reviewer_name', '!=', 'approver')
    //                                         ->get();

    //                                     if ($reviewers->isNotEmpty()) {
    //                                         $addToResult = $reviewers->contains(fn($rev) => $rev->nv_stage_status == 1);
    //                                     } elseif ($workflow_serial == 1) {
    //                                         $addToResult = true;
    //                                     } else {
    //                                         if($nvs->department_id == 19){
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
    //                                         }else{
    //                                             $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 1)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                         }
    //                                     }
    //                                 } else {
    //                                     if ($workflow_serial == 1) {
    //                                         $addToResult = true;
    //                                     } else {
    //                                          if($nvs->department_id == 19){
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
    //                                         }else{
    //                                             $addToResult = DB::table('capex_workflows_status')
    //                                                 ->where('nv_id', $nv_id)
    //                                                 ->where('workflow_serial', $workflow_serial - 1)
    //                                                 ->where('reviewer_name', 'approver')
    //                                                 ->where('nv_stage_status', 1)
    //                                                 ->exists();
    //                                         }
    //                                     }
    //                                 }

    //                                 if ($addToResult) {
    //                                     if ($budget_type === 'CAPEX') {
    //                                         $capexNvIds[] = $nv_id;
    //                                     } elseif ($budget_type === 'OPEX') {
    //                                         $opexNvIds[] = $nv_id;
    //                                     }
    //                                 }
    //                             }
    //                             $capexNvIds = array_unique($capexNvIds);
    //                             $opexNvIds = array_unique($opexNvIds);

    //                             $capexData = NeedValidation::with("division", "service")
    //                                 ->whereIn("id", $capexNvIds)
    //                                 ->where("budget_type", 'CAPEX')
    //                                 ->orderBy("id", "desc")->where('fiscal_year', $year)
    //                                 ->get();

    //                             $opexData = NeedValidation::with("division", "service")
    //                                 ->whereIn("id", $opexNvIds)
    //                                 ->where("budget_type", 'OPEX')->where('fiscal_year', $year)
    //                                 ->orderBy("id", "desc")
    //                                 ->get();

    //                             $nv1 = $capexData->merge($opexData);


    //                             $nv_ids = $nv1->pluck('id');

    //                             $approvedData = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('ceo_status', 1);

    //                             $approvedNV = $approvedData->count();

    //                             $rejectedNVIDS = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('is_reject', 1);
    //                             $rejectedMatData = DB::table('capex_workflows_status')
    //                                 ->where('workflow_user_id', $user->id)
    //                                 ->where('nv_stage_status', 2)
    //                                 ->whereIn('material_id', $rejectedNVIDS->pluck('material_id'))->get();
    //                             $rejectedSerData = DB::table('capex_workflows_status')
    //                                 ->where('workflow_user_id', $user->id)
    //                                 ->where('nv_stage_status', 2)
    //                                 ->whereIn('service_id', $rejectedNVIDS->pluck('service_id'))->get();
    //                             $rejectedData = $rejectedMatData->merge($rejectedSerData);
    //                             $rejectedNV = count($rejectedData);


    //                             $pendingNVIDS = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('is_reject', 0);
    //                             $pendingMatData = DB::table('capex_workflows_status')
    //                                 ->where('workflow_user_id', $user->id)
    //                                 ->where('nv_stage_status', 0)
    //                                 ->whereIn('material_id', $pendingNVIDS->pluck('material_id'))->get();
    //                             $pendingSerData = DB::table('capex_workflows_status')
    //                                 ->where('workflow_user_id', $user->id)
    //                                 ->where('nv_stage_status', 0)
    //                                 ->whereIn('service_id', $pendingNVIDS->pluck('service_id'))->get();
    //                             $pendingData = $pendingMatData->merge($pendingSerData);
    //                             $pendingNV = count($pendingData);

    //                             $totalData = DB::table('capex_workflows_status')
    //                                 ->where('workflow_user_id', $user->id)
    //                                 ->where('nv_stage_status', 1)
    //                                 ->whereIn('nv_id', $nv_ids);
                    
    //                             $totalNV = $totalData->count();
    //                             $nv_sm_data = Nvsericestatus::query()
    //                                 ->join('capex_workflows_status as cws', 'cws.nv_id', '=', 'nvservicestatus.nv_id')
    //                                 ->with(['service', 'material', 'user'])
    //                                 ->whereIn('nvservicestatus.nv_id', $nv_ids)
    //                                 ->where('cws.workflow_user_id', $user->id)
    //                                 ->orderBy('nvservicestatus.id', 'asc');
                                
    //                             $nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])
    //                                 ->whereIn('nv_id', $nv_ids)->orderBy('id', 'asc');

    //                             //    if(!empty($total_processed)){
    //                             //     $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                             //     }else
    //                             if (!empty($total)) {
    //                                 $nv_sm_data = $nv_sm_data->where('hod_status', 1)
    //                                     ->when(isset($group_cio) && (!empty($group_cio)), function ($query) {
    //                                         $query->orWhere('groupcio_status', 1);
    //                                     });
    //                             }elseif(!empty($total_processed)){
    //                                 $nv_sm_data = $nv_sm_data->where('ceo_status','=', 0)->where('is_reject','=',0);
    //                             }elseif (!empty($approved)) {
    //                                 $nv_sm_data = $nv_sm_data->where('ceo_status', 1);
    //                             } elseif (!empty($rejected)) {
    //                                 // $nv_sm_data = $nv_sm_data->whereIn('nv_id', $rejectedData->pluck('nv_id'))->where('is_reject',1);
    //                                 $rejectedMatIds = $rejectedMatData->pluck('material_id')->filter()->unique();
    //                                 $rejectedSerIds = $rejectedSerData->pluck('service_id')->filter()->unique();

    //                                 $nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])
    //                                     ->whereIn('nv_id', $nv_ids)
    //                                     ->where(function ($query) use ($rejectedMatIds, $rejectedSerIds) {
    //                                         if ($rejectedMatIds->isNotEmpty()) {
    //                                             $query->whereIn('material_id', $rejectedMatIds);
    //                                         }

    //                                         if ($rejectedMatIds->isNotEmpty() && $rejectedSerIds->isNotEmpty()) {
    //                                             $query->orWhereIn('service_id', $rejectedSerIds);
    //                                         } elseif ($rejectedSerIds->isNotEmpty()) {
    //                                             $query->whereIn('service_id', $rejectedSerIds);
    //                                         }
    //                                     })
    //                                     ->orderBy('id', 'asc');
    //                             } else {
    //                                 $nv_sm_data = $nv_sm_data->whereIn('nv_id', $pendingData->pluck('nv_id'))->where('is_reject', 0);
    //                             }
    //                             $nv_sm_data = $nv_sm_data->get();

    //                             $totalAmount = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.id', $totalData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.id', $totalData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');

    //                             $pendingAmount = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.id', $pendingData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.id', $pendingData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');

    //                             $rejectedAmount = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.id', $rejectedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.id', $rejectedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             $approvedAmount = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.id', $approvedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.id', $approvedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');

    //                             $BRPLnv = NeedValidation::where('company_id', '6')->whereIn('id', $nv_ids)->pluck("id");

    //                             $pendingAmountBRPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_material.id', $pendingData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_service.id', $pendingData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             $rejectedAmountBRPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_material.id', $rejectedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_service.id', $rejectedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             $approvedAmountBRPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_material.id', $approvedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BRPLnv)
    //                                 ->whereIn('tbl_service.id', $approvedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             // Get all needed IDs first to avoid duplicate queries
    //                             $rejectedMatIds = $rejectedData->pluck('material_id')->filter()->unique()->values();
    //                             $pendingMatIds = $pendingData->pluck('material_id')->filter()->unique()->values();
    //                             $approvedMatIds = $approvedData->pluck('material_id')->filter()->unique()->values();
    //                             $rejectedSerIds = $rejectedData->pluck('service_id')->filter()->unique()->values();
    //                             $pendingSerIds = $pendingData->pluck('service_id')->filter()->unique()->values();
    //                             $approvedSerIds = $approvedData->pluck('service_id')->filter()->unique()->values();

    //                             // Prepare ID strings for SQL queries
    //                             $approvedMatIdsStr = $approvedMatIds->isEmpty() ? '0' : $approvedMatIds->implode(',');
    //                             $rejectedMatIdsStr = $rejectedMatIds->isEmpty() ? '0' : $rejectedMatIds->implode(',');
    //                             $pendingMatIdsStr = $pendingMatIds->isEmpty() ? '0' : $pendingMatIds->implode(',');
    //                             $approvedSerIdsStr = $approvedSerIds->isEmpty() ? '0' : $approvedSerIds->implode(',');
    //                             $rejectedSerIdsStr = $rejectedSerIds->isEmpty() ? '0' : $rejectedSerIds->implode(',');
    //                             $pendingSerIdsStr = $pendingSerIds->isEmpty() ? '0' : $pendingSerIds->implode(',');

    //                             // Common select clauses for both companies
    //                             $materialSelect = [
    //                                 DB::raw('MONTH(created_at) as month'),
    //                                 DB::raw("SUM(CASE WHEN ceo_status = 1 AND material_id IN ($approvedMatIdsStr) THEN 1 ELSE 0 END) as approved_count"),
    //                                 DB::raw("SUM(CASE WHEN is_reject = 1 AND material_id IN ($rejectedMatIdsStr) THEN 1 ELSE 0 END) as rejected_count"),
    //                                 DB::raw("SUM(CASE WHEN is_reject = 0 AND material_id IN ($pendingMatIdsStr) THEN 1 ELSE 0 END) as pending_count")
    //                             ];

    //                             $serviceSelect = [
    //                                 DB::raw('MONTH(created_at) as month'),
    //                                 DB::raw("SUM(CASE WHEN ceo_status = 1 AND service_id IN ($approvedSerIdsStr) THEN 1 ELSE 0 END) as approved_count"),
    //                                 DB::raw("SUM(CASE WHEN is_reject = 1 AND service_id IN ($rejectedSerIdsStr) THEN 1 ELSE 0 END) as rejected_count"),
    //                                 DB::raw("SUM(CASE WHEN is_reject = 0 AND service_id IN ($pendingSerIdsStr) THEN 1 ELSE 0 END) as pending_count")
    //                             ];

    //                             $fileDataMatBRPL = Nvsericestatus::select($materialSelect)
    //                                 ->whereIn('nv_id', $BRPLnv)
    //                                 ->groupBy(DB::raw('MONTH(created_at)'))
    //                                 ->orderBy('month')
    //                                 ->get();

    //                             $fileDataSerBRPL = Nvsericestatus::select($serviceSelect)
    //                                 ->whereIn('nv_id', $BRPLnv)
    //                                 ->groupBy(DB::raw('MONTH(created_at)'))
    //                                 ->orderBy('month')
    //                                 ->get();

    //                             // Combine BRPL data
    //                             $BRPLcombined = collect();
    //                             foreach ($fileDataMatBRPL as $data) {
    //                                 $BRPLcombined->put($data->month, [
    //                                     'approved' => $data->approved_count,
    //                                     'rejected' => $data->rejected_count,
    //                                     'pending' => $data->pending_count
    //                                 ]);
    //                             }

    //                             foreach ($fileDataSerBRPL as $data) {
    //                                 $month = $data->month;
    //                                 $existing = $BRPLcombined->get($month, ['approved' => 0, 'rejected' => 0, 'pending' => 0]);

    //                                 $BRPLcombined->put($month, [
    //                                     'approved' => $existing['approved'] + $data->approved_count,
    //                                     'rejected' => $existing['rejected'] + $data->rejected_count,
    //                                     'pending' => $existing['pending'] + $data->pending_count
    //                                 ]);
    //                             }

    //                             // Prepare BRPL chart data
    //                             $BRPLlabels = [];
    //                             $BRPLapprovedData = [];
    //                             $BRPLrejectedData = [];
    //                             $BRPLpendingData = [];

    //                             foreach ($BRPLcombined->sortBy('month') as $month => $data) {
    //                                 $BRPLlabels[] = Carbon::createFromFormat('!m', $month)->format('F');
    //                                 $BRPLapprovedData[] = $data['approved'];
    //                                 $BRPLrejectedData[] = $data['rejected'];
    //                                 $BRPLpendingData[] = $data['pending'];
    //                             }
    //                             $BYPLnv = NeedValidation::where('company_id', '5')->whereIn('id', $nv_ids)->pluck("id");

    //                             $pendingAmountBYPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_material.id', $pendingData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_service.id', $pendingData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             $rejectedAmountBYPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_material.id', $rejectedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_service.id', $rejectedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');


    //                             $approvedAmountBYPL = DB::table('tbl_material')
    //                                 ->whereIn('tbl_material.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_material.id', $approvedData->pluck('material_id'))
    //                                 ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    //                                 ->whereIn('tbl_service.nv_id', $BYPLnv)
    //                                 ->whereIn('tbl_service.id', $approvedData->pluck('service_id'))
    //                                 ->sum('tbl_service.total_buget');

    //                             $fileDataMatBYPL = Nvsericestatus::select($materialSelect)
    //                                 ->whereIn('nv_id', $BYPLnv)
    //                                 ->groupBy(DB::raw('MONTH(created_at)'))
    //                                 ->orderBy('month')
    //                                 ->get();

    //                             $fileDataSerBYPL = Nvsericestatus::select($serviceSelect)
    //                                 ->whereIn('nv_id', $BYPLnv)
    //                                 ->groupBy(DB::raw('MONTH(created_at)'))
    //                                 ->orderBy('month')
    //                                 ->get();

    //                             // Combine BYPL data
    //                             $BYPLcombined = collect();
    //                             foreach ($fileDataMatBYPL as $data) {
    //                                 $BYPLcombined->put($data->month, [
    //                                     'approved' => $data->approved_count,
    //                                     'rejected' => $data->rejected_count,
    //                                     'pending' => $data->pending_count
    //                                 ]);
    //                             }

    //                             foreach ($fileDataSerBYPL as $data) {
    //                                 $month = $data->month;
    //                                 $existing = $BYPLcombined->get($month, ['approved' => 0, 'rejected' => 0, 'pending' => 0]);

    //                                 $BYPLcombined->put($month, [
    //                                     'approved' => $existing['approved'] + $data->approved_count,
    //                                     'rejected' => $existing['rejected'] + $data->rejected_count,
    //                                     'pending' => $existing['pending'] + $data->pending_count
    //                                 ]);
    //                             }

    //                             // Prepare BYPL chart data
    //                             $BYPLlabels = [];
    //                             $BYPLapprovedData = [];
    //                             $BYPLrejectedData = [];
    //                             $BYPLpendingData = [];

    //                             foreach ($BYPLcombined->sortBy('month') as $month => $data) {
    //                                 $BYPLlabels[] = Carbon::createFromFormat('!m', $month)->format('F');
    //                                 $BYPLapprovedData[] = $data['approved'];
    //                                 $BYPLrejectedData[] = $data['rejected'];
    //                                 $BYPLpendingData[] = $data['pending'];
    //                             }
    //                         }
    //                     }
    //                 }


    //                 return view("admin.dashboard", compact(
    //                     "approvedAmount",
    //                     "currentFinancialYear",
    //                     "nextFinancialYear",
    //                     "nextToNextFinancialYear",
    //                     "rejectedAmount",
    //                     "pendingAmount",
    //                     "totalAmount",
    //                     "company",
    //                     "company_id",
    //                     "nv_sm_data",
    //                     "totalNV",
    //                     "approvedNV",
    //                     "rejectedNV",
    //                     "pendingNV",
    //                     'BRPLlabels',
    //                     'BRPLapprovedData',
    //                     'BRPLrejectedData',
    //                     'BRPLpendingData',
    //                     'BYPLlabels',
    //                     'BYPLapprovedData',
    //                     'BYPLrejectedData',
    //                     'BYPLpendingData',
    //                     'pendingAmountBYPL',
    //                     'pendingAmountBRPL',
    //                     'approvedAmountBYPL',
    //                     'approvedAmountBRPL',
    //                     'rejectedAmountBYPL',
    //                     'rejectedAmountBRPL',
    //                     'capexApprovalCounts',
    //                     'opexApprovalCounts',
    //                     'workflowStages',
    //                     'opexWorkflowStages'
    //                 ));
    //             }
    //         }
    //     }
    // }

    public function addSignature(Request $request)

    {
        $user_id = \Auth::user()->id;
        if ($request->hasfile('image')) {

            $file = $request->file('image');

            $filename = time() . rand() . '.' . $file->getClientOriginalName();

            $file->move('images/', $filename);

            $imgname = $filename ?? '';
        }
        // dd($imgname);

        $data = [
            'signature_id'      => $request->signature ?? '',
            'signature_status'  => 1,
            'image'             => $imgname ?? '',

        ];

        $create  = User::where('id', $request->user_id)->update($data);
        $data1 = [
            'signature_id'      => $request->signature ?? '',

            'signature_status'  => 1,

            'image'             => $imgname ?? '',



        ];



        // dd($data);

        $create  = User::where('id', $request->user_id)->update($data);

        $data1 = [

            'signature_id'      => $request->signature ?? '',

            'user_id'           => $user_id,

            'image'             => $imgname ?? '',

        ];

        $insert = DB::table('log_signature')->insert($data1);

        // dd( $create);

        if ($create) {

            $UserData = User::where('id', $request->user_id)->first();

            // dd($UserData);

            return response()->json([

                "message"       => "Success",

                "signature_id"  => $UserData->signature_id ?? '',

                "code"          => 200

            ]);
        }
    }

    public function logSignature(Request $request)
    {
        $user_id = \Auth::user()->id;
        $log = Signaturelog::where('user_id', $user_id)->exists();
        //    $log = Signaturelog::where('user_id',$user_id)->select('created_at','signature_id')->get();
        if ($log) {
            $Userlog = Signaturelog::with('user')->select('signature_id', 'user_id', 'created_at', 'image')->where('user_id', $user_id)->orderBy('id', 'desc')->get();
            // $Userlog = Signaturelog::join('users','users.id','=','log_signature.user_id')->where('log_signature.user_id',$user_id)->select('users.name','log_signature.signature_id','log_signature.image','log_signature.created_at')->get();

            return response()->json([
                "message"       => "Success",
                'Userlog'       =>  $Userlog ?? '',
                "code"          => 200
            ]);
        }
    }

    public function show_list()
    {

        $user = \Auth::user();

        if ($user->role_id == 1) {

            $totalId = NeedValidation::where("delete_draft", 0)->pluck('id');

            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $statusFields = [
                'is_reject'
            ];

            $latest = $latestData->filter(function ($data) use ($statusFields) {
                foreach ($statusFields as $field) {
                    if ($data->$field == 1) {
                        return true;
                    }
                }
                return false;
            });
        } elseif ($user->role_id == 9) {
            $totalId = NeedValidation::where("delete_draft", 0)->where('user_id', $user->id)->pluck('id');

            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $statusFields = [
                'is_reject'
            ];

            $latest = $latestData->filter(function ($data) use ($statusFields) {
                foreach ($statusFields as $field) {
                    if ($data->$field == 1) {
                        return true;
                    }
                }
                return false;
            });
        } elseif ($user->role_id == 11) {

            $employees = Employee::where("user_id", $user->id)->first();

            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();
            $latest = [];
            foreach ($departments as $department) {
                $dep_id = $department->id;
                $hod = $department->dep_hod;
                $rv1 = $department->dep_rew1;
                $rv2 = $department->dep_rew2;
                $rv3 = $department->dep_rew3;
                $rv4 = $department->dep_rew4;
                $group_cio = $department->group_cio;

                if (!empty($rv1) && $rv1 == $user->id) {
                    //    $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew1", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv1_status", 2)->get();
                } elseif (!empty($rv2) && $rv2 == $user->id) {
                    //    $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew2", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv2_status", 2)->get();
                } elseif (!empty($rv3) && $rv3 == $user->id) {
                    //    $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew3", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv3_status", 2)->get();
                } elseif (!empty($rv4) && $rv4 == $user->id) {
                    //    $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_rew4", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv4_status", 2)->get();
                } elseif (!empty($hod) && $hod == $user->id) {
                    //    $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("dep_hod", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where("delete_draft", 0)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 2)->get();
                } elseif (!empty($group_cio) && $group_cio == $user->id) {
                    $departmentIds = Department::where("group_cio", $user->id)->pluck('id');
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->get();
                } else {
                    $workflows = DB::table('capex_workflows_status')->get();

                    if ($workflows->isNotEmpty()) {

                        foreach ($workflows as $workflow) {

                            $capexWorkflowUsers = $workflow->workflow_user_id;

                            if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {
                                $totalId = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('nv_stage_status', 2);
                                // $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('is_reject',1)->get();

                                $latest_material = Nvsericestatus::whereIn("material_id", $totalId->pluck('material_id'))->get();
                                $latest_service = Nvsericestatus::whereIn("material_id", $totalId->pluck('service_id'))->get();
                                $latest = $latest_material->merge($latest_service);
                            }
                        }
                    }
                }
            }
        }


        return view("admin.reject_list", compact("latest"));
    }

    public function export_NV_pdf(Request $request)

    {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;
        $user = \Auth::user();
        $employees = Employee::where("user_id", $user->id)->first();
        $currentDate = Carbon::now();

        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }
        $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
        $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');

        if ($user->role_id == 1) {

            if ($company_id) {
                $nv_ids = NeedValidation::where('company_id', $company_id)->where('fiscal_year', $currentFinancialYear)->where('delete_draft', 0)->pluck("id");
            } elseif ($fiscal_year) {
                $nv_ids = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft', 0)->pluck("id");
            } else {
                $nv_ids = NeedValidation::where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->pluck("id");
            }
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
            // $nv_sm_data = $nv_sm_data->get();
            $customPaper = array(0, 0, 1240, 1748);
            $pdf = PDF::loadView('admin.dashboard_pdf', ["nv_sm_data" => $nv_sm_data])->setPaper('a4', 'landscape');
            return $pdf->download('NV_pdf.pdf');
        } elseif ($user->role_id == 9) {

            if ($fiscal_year) {
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $fiscal_year)->where("delete_draft", 0)->get();
            } else {
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year', $currentFinancialYear)->where("delete_draft", 0)->get();
            }
            $nv_ids = $nv->pluck('id');
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
            // $nv_sm_data = $nv_sm_data->get();
            $customPaper = array(0, 0, 1240, 1748);
            $pdf = PDF::loadView('admin.dashboard_pdf', ["nv_sm_data" => $nv_sm_data])->setPaper('a4', 'landscape');
            return $pdf->download('NV_pdf.pdf');
        } elseif (!empty($user->role_id == 11)) {

            $departments_with_group_cio = [];
            $departments_without_group_cio = [];
            $all_departments = Department::where('status', 1)->get();
            $nv_sm_data = [];
            foreach ($all_departments as $all_department) {
                if (!empty($all_department->group_cio)) {
                    $departments_with_group_cio[] = $all_department->id;
                } else {
                    $departments_without_group_cio[] = $all_department->id;
                }
            }

            $employee = Employee::where('user_id', $user->id)->with('department')->first();
            $allusers = Employee::where('department_id', $employee->department_id)->get();
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();
            foreach ($departments as $dep) {
                $dep_id = $dep->id;
                $hod = $dep->dep_hod;
                $dep_rew1 = $dep->dep_rew1;
                $dep_rew2 = $dep->dep_rew2;
                $dep_rew3 = $dep->dep_rew3;
                $dep_rew4 = $dep->dep_rew4;
                $group_cio = $dep->group_cio;

                if (!empty($dep_rew1) && $dep_rew1 == $user->id) {
                    $departmentIds = Department::where("dep_rew1", $user->id)->pluck('id');
                    if ($fiscal_year) {
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    } else {
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    }


                    $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft', 1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
                } elseif (!empty($dep_rew2) && $dep_rew2 == $user->id) {
                    $departmentIds = Department::where("dep_rew2", $user->id)->pluck('id');
                    if ($fiscal_year) {
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    } else {
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    }


                    $nv_ids = $nv->pluck('id');
                    if (!empty($dep_rew1)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status', 1)->orderBy('id', 'asc')->get();
                    } else {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft', 1)->where('rv1_status', '!=', 2)->orderBy('id', 'asc')->get();
                    }
                } elseif (!empty($dep_rew3) && $dep_rew3 == $user->id) {
                    $departmentIds = Department::where("dep_rew3", $user->id)->pluck('id');
                    if ($fiscal_year) {
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    } else {
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    }

                    $nv_ids = $nv->pluck('id');
                    if (!empty($dep_rew2)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('rv2_status', 1)
                            ->orderBy('id', 'asc')->get();
                    } elseif (!empty($dep_rew1)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('rv1_status', 1)->where('rv2_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    } else {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('draft', 1)->where('rv1_status', '!=', 2)
                            ->where('rv2_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    }
                } elseif (!empty($dep_rew4) && $dep_rew4 == $user->id) {
                    $departmentIds = Department::where("dep_rew4", $user->id)->pluck('id');
                    if ($fiscal_year) {
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    } else {
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                ->orWhereIn('user_id', $allNormalUsers)
                                ->orWhereIn('department_id', $departmentIds);
                        })
                            ->whereHas('service')
                            ->select('id')
                            ->get();
                    }
                    $nv_ids = $nv->pluck('id');
                    if (!empty($dep_rew3)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('rv3_status', 1)
                            ->orderBy('id', 'asc')->get();
                    } elseif (!empty($dep_rew2)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('rv2_status', 1)
                            ->where('rv3_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    } elseif (!empty($dep_rew1)) {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('rv1_status', 1)
                            ->where('rv2_status', '!=', 2)
                            ->where('rv3_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    } else {
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                            ->where('draft', 1)
                            ->where('rv1_status', '!=', 2)
                            ->where('rv2_status', '!=', 2)
                            ->where('rv3_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    }
                } elseif (!empty($hod) && $hod == $user->id) {
                    $nv_sm_data = array();
                    $nv_statuses = array();
                    $nv_id = [];
                    $id = [];
                    $departmentIds = Department::where("dep_hod", $user->id)->pluck('id');
                    $depart = Department::whereIn('id', $departmentIds)->get();

                    foreach ($depart as $depart) {
                        $dep_rew4 = $depart->dep_rew4;
                        $dep_rew3 = $depart->dep_rew3;
                        $dep_rew2 = $depart->dep_rew2;
                        $dep_rew1 = $depart->dep_rew1;
                        $dep_hod = $depart->dep_hod;
                        if (!empty($dep_hod)) {
                            if (!empty($dep_rew4)) {
                                $nv_status = Nvsericestatus::where(function ($query) {
                                    $query->where('rv4_status', 1);
                                })
                                    ->where(function ($query) {
                                        $query
                                            ->where('rv4_status', '!=', 2);
                                    })
                                    ->get();
                                if (count($nv_status) > 0) {
                                    $nv_statuses[] = $nv_status;
                                }
                            } elseif (!empty($dep_rew3)) {
                                $nv_status = Nvsericestatus::where(function ($query) {
                                    $query->where('rv3_status', 1);
                                })
                                    ->where(function ($query) {
                                        $query
                                            ->where('rv3_status', '!=', 2);
                                    })
                                    ->get();
                                if (count($nv_status) > 0) {
                                    $nv_statuses[] = $nv_status;
                                }
                            } elseif (!empty($dep_rew2)) {
                                // print_r("2");
                                $nv_status = Nvsericestatus::where(function ($query) {
                                    $query->where('rv2_status', 1);
                                })
                                    ->where(function ($query) {
                                        $query
                                            ->where('rv2_status', '!=', 2);
                                    })
                                    ->get();
                                if (count($nv_status) > 0) {
                                    $nv_statuses[] = $nv_status;
                                }
                            } elseif (!empty($dep_rew1)) {
                                // print_r("1");
                                $nv_status = Nvsericestatus::where(function ($query) {
                                    $query->where('rv1_status', 1);
                                })
                                    ->where(function ($query) {
                                        $query->where('rv1_status', '!=', 2);
                                    })
                                    ->get();
                                // print_r($nv_status);
                                if (count($nv_status) > 0) {
                                    $nv_statuses[] = $nv_status;
                                    // print_r("here");

                                }
                            } else {
                                $nv_status = Nvsericestatus::join('needvalidations', 'needvalidations.id', '=', 'nvservicestatus.nv_id')
                                    ->join('department', 'department.id', '=', 'needvalidations.department_id')
                                    ->where('department.id', $depart->id)
                                    ->where('nvservicestatus.draft', 1)
                                    ->get();

                                if (count($nv_status) > 0) {
                                    $nv_statuses[] = $nv_status;
                                }
                            }
                        }
                    }
                    if (!empty($nv_statuses)) {
                        foreach ($nv_statuses as $idx => $nv_status) {
                            foreach ($nv_status as $data) {
                                array_push($nv_id, $data["nv_id"]);
                                array_push($id, $data["id"]);
                            }
                        }
                        if ($fiscal_year) {
                            $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft', 0)
                                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
                        } else {
                            $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft', 0)
                                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
                        }
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])
                            ->where('rv1_status', '!=', 2)
                            ->where('rv2_status', '!=', 2)
                            ->where('rv3_status', '!=', 2)
                            ->where('rv4_status', '!=', 2)
                            ->orderBy('id', 'asc')->get();
                    }
                } elseif (!empty($group_cio) && $group_cio == $user->id) {
                    // $departmentIds = explode(',', $user->department_id);
                    $departmentIds = Department::where("group_cio", $user->id)->pluck('id');
                    if ($fiscal_year) {
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)
                            ->get();
                    } else {
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)
                            ->get();
                    }

                    $nv_ids = $nv->pluck('id');

                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('hod_status', 1)
                        ->orderBy('id', 'asc')->get();
                } else {

                    $workflows = DB::table('capex_workflows_status')->where('transfer_to_nominee1', 0)->get();

                    if ($workflows->isNotEmpty()) {

                        foreach ($workflows as $workflow) {

                            $capexWorkflowUsers = $workflow->workflow_user_id;
                            $year = !empty($fiscal_year) ? $fiscal_year : $currentFinancialYear;

                            if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {
                                $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->where('transfer_to_nominee1', 0)->get();
                                $capexNvIds = [];
                                $opexNvIds = [];

                                foreach ($nv_workflows as $nvs) {
                                    $nv_id = $nvs->nv_id;
                                    $workflow_serial = $nvs->workflow_serial;
                                    $budget_type = $nvs->nv_budget_type;
                                    $isApprover = strtolower($nvs->reviewer_name) === 'approver';

                                    $addToResult = false;

                                    if ($isApprover) {
                                        $reviewers = DB::table('capex_workflows_status')
                                            ->where('transfer_to_nominee1', 0)
                                            ->where('nv_id', $nv_id)
                                            ->where('workflow_serial', $workflow_serial)
                                            ->where('reviewer_name', '!=', 'approver')
                                            ->get();

                                        if ($reviewers->isNotEmpty()) {
                                            $addToResult = $reviewers->contains(fn($rev) => $rev->nv_stage_status == 1);
                                        } elseif ($workflow_serial == 1) {
                                            $addToResult = true;
                                        } else {
                                            $addToResult = DB::table('capex_workflows_status')
                                                ->where('nv_id', $nv_id)
                                                ->where('workflow_serial', $workflow_serial - 1)
                                                ->where('reviewer_name', 'approver')
                                                ->where('nv_stage_status', 1)
                                                ->where('transfer_to_nominee1', 0)
                                                ->exists();
                                        }
                                    } else {
                                        if ($workflow_serial == 1) {
                                            $addToResult = true;
                                        } else {
                                            $addToResult = DB::table('capex_workflows_status')
                                                ->where('nv_id', $nv_id)
                                                ->where('workflow_serial', $workflow_serial - 1)
                                                ->where('reviewer_name', 'approver')
                                                ->where('nv_stage_status', 1)
                                                ->where('transfer_to_nominee1', 0)
                                                ->exists();
                                        }
                                    }

                                    if ($addToResult) {
                                        if ($budget_type === 'CAPEX') {
                                            $capexNvIds[] = $nv_id;
                                        } elseif ($budget_type === 'OPEX') {
                                            $opexNvIds[] = $nv_id;
                                        }
                                    }
                                }
                                $capexNvIds = array_unique($capexNvIds);
                                $opexNvIds = array_unique($opexNvIds);

                                $capexData = NeedValidation::with("division", "service")
                                    ->whereIn("id", $capexNvIds)
                                    ->where("budget_type", 'CAPEX')
                                    ->orderBy("id", "desc")->where('fiscal_year', $year)
                                    ->get();

                                $opexData = NeedValidation::with("division", "service")
                                    ->whereIn("id", $opexNvIds)
                                    ->where("budget_type", 'OPEX')->where('fiscal_year', $year)
                                    ->orderBy("id", "desc")
                                    ->get();

                                $nv1 = $capexData->merge($opexData);


                                $nv_ids = $nv1->pluck('id');

                                $nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])
                                    ->whereIn('nv_id', $nv_ids)->where('hod_status', 1)
                                    ->when(isset($group_cio) && (!empty($group_cio)), function ($query) {
                                        $query->orWhere('groupcio_status', 1);
                                    })->orderBy('id', 'asc')->get();
                                // $nv_sm_data = $nv_sm_data->get();
                                $customPaper = array(0, 0, 1240, 1748);
                                $pdf = PDF::loadView('admin.dashboard_pdf', ["nv_sm_data" => $nv_sm_data])->setPaper('a4', 'landscape');
                                return $pdf->download('NV_pdf.pdf');
                            }
                        }
                    }
                }
            }
            //  $nv_sm_data = $nv_sm_data->get();
            $customPaper = array(0, 0, 1240, 1748);
            $pdf = PDF::loadView('admin.dashboard_pdf', ["nv_sm_data" => $nv_sm_data])->setPaper('a4', 'landscape');
            return $pdf->download('NV_pdf.pdf');
        }
    }

    public function export_NV_excel(Request $request)
    {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;
        $user = \Auth::user();
        $employees = Employee::where("user_id", $user->id)->first();
        $currentDate = Carbon::now();

        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }
        $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
        $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');
        
        if ($user->role_id == 1) {
            if($company_id){
            $nv_ids = NeedValidation::where('company_id',$company_id)->where('fiscal_year',$currentFinancialYear)->where('delete_draft',0)->pluck("id");
            }elseif($fiscal_year){
            $nv_ids = NeedValidation::where('fiscal_year',$fiscal_year)->where('delete_draft',0)->pluck("id");
            }else{
            $nv_ids = NeedValidation::where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");
            }
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();

        }elseif($user->role_id == 9){

            if($fiscal_year){
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->get();

            }else{
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->get();

            }
            $nv_ids = $nv->pluck('id');
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
        }elseif(!empty($user->role_id == 11)) {
           

            $departments_with_group_cio = [];
            $departments_without_group_cio = [];
            $all_departments = Department::where('status', 1)->get();
            $nv_sm_data = [];
            foreach ($all_departments as $all_department) {
                if (!empty($all_department->group_cio)) {
                    $departments_with_group_cio[] = $all_department->id;
                } else {
                    $departments_without_group_cio[] = $all_department->id;
                }
            }
            
            $employee = Employee::where('user_id', $user->id)->with('department')->first();	
            $allusers = Employee::where('department_id', $employee->department_id)->get();	
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');	
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();
                foreach ($departments as $dep) {
                        $dep_id = $dep->id;
                        $hod = $dep->dep_hod;
                        $dep_rew1 = $dep->dep_rew1;
                        $dep_rew2 = $dep->dep_rew2;
                        $dep_rew3 = $dep->dep_rew3;
                        $dep_rew4 = $dep->dep_rew4;
                        $group_cio = $dep->group_cio;

                if (!empty($dep_rew1) && $dep_rew1 == $user->id) {	
                    $departmentIds = Department::where("dep_rew1", $user->id)->pluck('id');
                    if($fiscal_year){
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service')
                        ->select('id')
                        ->get();  
                    }else{
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service')
                        ->select('id')
                        ->get();  
                    }
                            
                    
                    $nv_ids = $nv->pluck('id');	
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft',1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
                    
                        
                }elseif(!empty($dep_rew2) && $dep_rew2 == $user->id){
                    $departmentIds = Department::where("dep_rew2", $user->id)->pluck('id');
                    if($fiscal_year){
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service') 
                        ->select('id')
                        ->get();
                    }else{
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service') 
                        ->select('id')
                        ->get();
                    }
                    
            
                    $nv_ids = $nv->pluck('id');
                    if(!empty($dep_rew1)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status',1)->orderBy('id', 'asc')->get();
                    }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft',1)->where('rv1_status','!=', 2)->orderBy('id', 'asc')->get();
                    }
                }elseif(!empty($dep_rew3) && $dep_rew3 == $user->id){
                    $departmentIds = Department::where("dep_rew3", $user->id)->pluck('id');
                    if($fiscal_year){
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service') 
                        ->select('id')
                        ->get();
                    }else{
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service')
                        ->select('id')
                        ->get();
                    }
                    
                    $nv_ids = $nv->pluck('id');
                    if(!empty($dep_rew2)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('rv2_status',1)
                        ->orderBy('id', 'asc')->get();
                        
                    }elseif(!empty($dep_rew1)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('rv1_status',1)->where('rv2_status','!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                    
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('draft',1)->where('rv1_status','!=', 2)
                        ->where('rv2_status','!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                    }
                }elseif(!empty($dep_rew4) && $dep_rew4 == $user->id){
                    $departmentIds = Department::where("dep_rew4", $user->id)->pluck('id');
                    if($fiscal_year){
                        $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service') 
                        ->select('id')
                        ->get();  
                    }else{
                        $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                            $query->where('user_id', $user->id)
                                    ->orWhereIn('user_id', $allNormalUsers)
                                    ->orWhereIn('department_id', $departmentIds);
                        })
                        ->whereHas('service') 
                        ->select('id')
                        ->get();  
                    }
                        $nv_ids = $nv->pluck('id');
                    if(!empty($dep_rew3)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('rv3_status',1)
                        ->orderBy('id', 'asc')->get();
                    
                    }elseif(!empty($dep_rew2)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('rv2_status',1)->where('rv3_status','!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                    }elseif(!empty($dep_rew1)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('rv1_status',1)->where('rv2_status','!=', 2)
                        ->where('rv3_status','!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                    }else{
                            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('draft',1)->where('rv1_status','!=', 2)
                        ->where('rv2_status','!=', 2)
                        ->where('rv3_status','!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                    }
                }elseif(!empty($hod) && $hod == $user->id){
                    $nv_sm_data = array();
                    $nv_statuses = array();
                    $nv_id = [];
                    $id = [];
                    $departmentIds = Department::where("dep_hod", $user->id)->pluck('id');
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
                            // print_r("2");
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
                                // print_r("1");
                                $nv_status = Nvsericestatus::where(function ($query) {
                                $query->where('rv1_status', 1);
                            })
                                ->where(function ($query) {
                                    $query->where('rv1_status', '!=', 2);
                                })
                                ->get();  
                            // print_r($nv_status);
                            if(count($nv_status) > 0) {
                                $nv_statuses[] = $nv_status;
                                // print_r("here");
                                
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
                        if($fiscal_year){
                        $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft',0)
                        ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
                        }else{
                        $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft',0)
                        ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id'); 
                        }
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])
                        ->where('rv1_status', '!=', 2)
                        ->where('rv2_status', '!=', 2)
                        ->where('rv3_status', '!=', 2)
                        ->where('rv4_status', '!=', 2)
                        ->orderBy('id', 'asc')->get();
                        
                        }
                }elseif(!empty($group_cio) && $group_cio == $user->id) {
                        // $departmentIds = explode(',', $user->department_id);
                        $departmentIds = Department::where("group_cio", $user->id)->pluck('id');
                        if($fiscal_year){
                            $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)
                            ->get();
                        }else{
                            $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)
                        ->get();
                        }
                        
                        $nv_ids = $nv->pluck('id');
                    
                            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('hod_status', 1)
                        ->orderBy('id', 'asc')->get();
                }else{

                    $workflows = DB::table('capex_workflows_status')->get();
                    
                        if($workflows->isNotEmpty()){
                            
                                foreach ($workflows as $workflow) {
                    
                                    $capexWorkflowUsers = $workflow->workflow_user_id;
                                    $year = !empty($fiscal_year) ? $fiscal_year : $currentFinancialYear;
                    
                                    if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {
                                        $nv_workflows = DB::table('capex_workflows_status')->where('workflow_user_id', $user->id)->get();
                                        $capexNvIds = [];
                                        $opexNvIds = [];
                                    
                                        foreach ($nv_workflows as $nvs) {
                                            $nv_id = $nvs->nv_id;
                                            $workflow_serial = $nvs->workflow_serial;
                                            $budget_type = $nvs->nv_budget_type;
                                            $isApprover = strtolower($nvs->reviewer_name) === 'approver';
                                        
                                            $addToResult = false;
                                        
                                            if ($isApprover) {
                                                $reviewers = DB::table('capex_workflows_status')
                                                    ->where('nv_id', $nv_id)
                                                    ->where('workflow_serial', $workflow_serial)
                                                    ->where('reviewer_name', '!=', 'approver')
                                                    ->get();
                                        
                                                if ($reviewers->isNotEmpty()) {
                                                    $addToResult = $reviewers->contains(fn($rev) => $rev->nv_stage_status == 1);
                                                } elseif ($workflow_serial == 1) {
                                                    $addToResult = true;
                                                } else {
                                                    $addToResult = DB::table('capex_workflows_status')
                                                        ->where('nv_id', $nv_id)
                                                        ->where('workflow_serial', $workflow_serial - 1)
                                                        ->where('reviewer_name', 'approver')
                                                        ->where('nv_stage_status', 1)
                                                        ->exists();
                                                }
                                            } else {
                                                if ($workflow_serial == 1) {
                                                    $addToResult = true;
                                                } else {
                                                    $addToResult = DB::table('capex_workflows_status')
                                                        ->where('nv_id', $nv_id)
                                                        ->where('workflow_serial', $workflow_serial - 1)
                                                        ->where('reviewer_name', 'approver')
                                                        ->where('nv_stage_status', 1)
                                                        ->exists();
                                                }
                                            }
                                        
                                            if ($addToResult) {
                                                if ($budget_type === 'CAPEX') {
                                                    $capexNvIds[] = $nv_id;
                                                } elseif ($budget_type === 'OPEX') {
                                                    $opexNvIds[] = $nv_id;
                                                }
                                            }
                                        }
                                            $capexNvIds = array_unique($capexNvIds);
                                            $opexNvIds = array_unique($opexNvIds);

                                            $capexData = NeedValidation::with("division", "service")
                                            ->whereIn("id", $capexNvIds)
                                            ->where("budget_type", 'CAPEX')
                                            ->orderBy("id", "desc")->where('fiscal_year', $year)
                                            ->get();

                                            $opexData = NeedValidation::with("division", "service")
                                            ->whereIn("id", $opexNvIds)
                                            ->where("budget_type", 'OPEX')->where('fiscal_year', $year)
                                            ->orderBy("id", "desc")
                                            ->get();

                                            $nv1 = $capexData->merge($opexData);

        
                                            $nv_ids = $nv1->pluck('id');

                                            $nv_sm_data = Nvsericestatus::with(['service', 'material', 'user'])
                                            ->whereIn('nv_id', $nv_ids)->where('hod_status', 1)
                                            ->when(isset($group_cio) && (!empty($group_cio)), function ($query) {
                                                $query->orWhere('groupcio_status', 1);
                                            })->orderBy('id', 'asc')->get();

                                    }
                                }    
                        }
                
                }
            }
        }
        $workflowStages = Workflow::where('status',1)->get();
        $opexWorkflowStages = OpexWorkflow::where('status',1)->get();
                                    
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr>
                        <th>S.no</th>
                        <th>Proposal Number</th>
                        <th>NV Budget Type</th>
                        <th>Sub-Department</th>
                        <th>NV Type</th>
                        <th>Initiated BY & Date</th>
                        <th>HOD</th>
                        <th>Group Head</th>';
                        ;

                // Add CAPEX workflow stage headers
                if(!empty($workflowStages)) {
                    foreach ($workflowStages as $workflowStage) {
                        $output .= '<th class="text-center">'
                            . getPrefixDepartmentName($workflowStage->work_dep)
                            . '<br>(CAPEX)</th>';
                    }
                }

                // Add OPEX workflow stage headers
                if(!empty($opexWorkflowStages)) {
                    foreach ($opexWorkflowStages as $opexWorkflowStage) {
                        $output .= '<th class="text-center">'
                            . getPrefixDepartmentName($opexWorkflowStage->work_dep)
                            . '<br>(OPEX)</th>';
                    }
                }

                $output .= '</tr>';
                $i = 1;
                $no = 101;
    
            if (!empty($nv_sm_data)){
                foreach ($nv_sm_data as $nv_list){
                    $proposal_no = getProposalNumber($nv_list->nv_id);
                    $status_obj = getAllStatus($nv_list->nv_id);
                    
                    $inc_date = new DateTime($nv_list->created_at);

                    if($nv_list->hod_timestamp != null){
                        $hod_app = new DateTime($nv_list->hod_timestamp);
                        $hodinterval = $hod_app->diff($inc_date);
                        $days = $hodinterval->days;
                    }else{
                        $days = 0;
                    }
                    if($nv_list->groupcio_timestamp != null){
                    $groupcio_app = new DateTime($nv_list->groupcio_timestamp);
                    $groupciointerval = $groupcio_app->diff($inc_date);
                    $groupheaddays = $groupciointerval->days;
                    }else{
                    $groupheaddays = 0;
                    }
                    if($nv_list->nv_stage_timestamp != null){
                    $ces_app = new DateTime($nv_list->nv_stage_timestamp);
                    $cesinterval = $ces_app->diff($inc_date);
                    $cesdays = $cesinterval->days;
                    }else{
                        $cesdays = 0;
                    }
                    
                    $n_id[] = $nv_list->nv_id;
                    
                    $result = [];
                    
                    foreach ($n_id as $key => $value) {
                        $count = count(array_keys($n_id, $value)); 
                    
                        if ($count > 1) {
                            for ($i = 0; $i <= $count; $i++) {
                                $result = $value . '-v' . $i;
                            }
                        } else {
                            $result = $value;
                        }
                        
                    }
                    $nvid = $nv_list->nv_id;
                    $count = Nvsericestatus::where('nv_id', $nvid)->count();
                    $currentOccurrence = Nvsericestatus::where('nv_id', $nvid)->where('created_at', '<=', $nv_list->created_at)->count();
                

                        if ($currentOccurrence > 1){
                            for ($i = 1; $i <= $count; $i++) {
                                $result = $nvid . '-v' . $currentOccurrence; 
                            }
                                    
                        }else{
                            $result = $nvid;
                        }
                    
                    $service= getServiceName($nv_list->nv_id);
                    $url = '/admin/nv_' . lcfirst($service) . '/create/' . $nv_list->nv_id.'/'.
                    $nv_list->company_id;
                    $i = $key + 1;

                    
                    $output .= '<tr>';
                    $output .= '<td>' . $i . '</td>';
                    if($nv_list->service_id == null){
                        $proposalNo=  getDepartmentName($nv_list->material->dept_id);
                        }else{
                        $proposalNo= getDepartmentName($nv_list->service->dept_id);
                        }
                    $output .= '<td>' .'NV/'.''. ($proposal_no->budget_type).''. '/FY'.''. ($proposal_no->fiscal_year).''.'/'.''.$proposalNo.''.'/'.''.getServiceName($nv_list->nv_id).''.'/'.''. $nv_list->version_nv. '</td>';
                    $output .= '<td>' . $proposal_no->budget_type . '</td>';
                        if ($nv_list->service_id == null){
                        $departmentName = getDepartmentName($nv_list->material->dept_id);
                        }else{
                        $departmentName = getDepartmentName($nv_list->service->dept_id);
                        }
                        $output .='<td>' .$departmentName. '</td>';
                        
                        if($nv_list->service_id == null && $nv_list->material->ser_rel_nv == 2){
                        $serviceName = getServiceName($nv_list->nv_id).'+Service';
                        }else{
                        $serviceName = getServiceName($nv_list->nv_id);
                        }
                        $output .='<td>' . $serviceName. '</td>';

                    $date = date('d-M-y', strtotime($nv_list->created_at)) ;  
                    $time = date('h:i A', strtotime($nv_list->created_at));      
                    if ($nv_list->service_id == null)
                    {
                            $output .='<td>' . getUserName($nv_list->material->user_id).'<br>'.$date.'<br>'.$time . '</td>';
                    }
                    else
                    {
                            $output .='<td>' . getUserName($nv_list->service->user_id).'<br>'.$date.'<br>'.$time . '</td>';
                    }

                    if ($nv_list->service_id == null)
                    {
                        
                        $gethodname = getHodName($nv_list->hod_id ?? null) ;
                        if ($nv_list->hod_status == 0)
                        {
                            if (!empty($nv_list->hod_timestamp))
                            {
                                $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                                $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            }
                        }
                        elseif($nv_list->hod_status == 1)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Approval Date:'.''. $datehod.'<br>'.'Approval Time:'.''.$timehod.'<br>'.'System IP:'.''.$nv_list->hod_action_ip. '</td>';
                        }
                        elseif($nv_list->hod_status == 2)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Rejection Date:'.''. $datehod.'<br>'.'Rejection Time:'.''.$timehod.'<br>'.'System IP:'.''.$nv_list->hod_action_ip. '</td>';
                        }
                    }
                    else
                    {
                        $gethodname = getHodName($nv_list->hod_id ?? null) ;
                        if ($nv_list->hod_status == 0)
                        {
                            if (!empty($nv_list->hod_timestamp))
                            {
                                $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                                $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            }
                        }
                        elseif($nv_list->hod_status == 1)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Approval Date:'.''. $datehod.'<br>'.'Approval Time:'.''.$timehod.'<br>'.'System IP:'.''.$nv_list->hod_action_ip. '</td>';
                        }
                        elseif($nv_list->hod_status == 2)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Rejection Date:'.''. $datehod.'<br>'.'Rejection Time:'.''.$timehod.'<br>'.'System IP:'.''.$nv_list->hod_action_ip. '</td>';
                        }
                    }

                    if ($nv_list->service_id == null)
                    {
                        $groupcio =  getGroupHeadName($nv_list->groupcio_id ?? null);
                        if ($nv_list->groupcio_status == 0)
                        {
                            
                            if (!empty($nv_list->groupcio_timestamp))
                            {
                                $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                                $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            }
                        }
                        elseif($nv_list->groupcio_status == 1)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Approval Date:'.''. $groupciodate.'<br>'.'Approval Time:'.''.$groupciotime.'<br>'.'System IP:'.''.$nv_list->groupcio_action_ip . '</td>';
                        }
                        elseif($nv_list->groupcio_status == 2)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Rejection Date:'.''. $groupciodate.'<br>'.'Rejection Time:'.''.$groupciotime.'<br>'.'System IP:'.''.$nv_list->groupcio_action_ip. '</td>';
                        }
                    }
                    else
                    {
                        $groupcio =  getGroupHeadName($nv_list->groupcio_id ?? null);
                        if ($nv_list->groupcio_status == 0)
                        {
                            if (!empty($nv_list->groupcio_timestamp))
                            {
                                $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                                $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            }
                        }
                        elseif($nv_list->groupcio_status == 1)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Approval Date:'.''. $groupciodate.'<br>'.'Approval Time:'.''.$groupciotime.'<br>'.'System IP:'.''.$nv_list->groupcio_action_ip . '</td>';
                        }
                        elseif($nv_list->groupcio_status == 2)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Rejection Date:'.''. $groupciodate.'<br>'.'Rejection Time:'.''.$groupciotime.'<br>'.'System IP:'.''.$nv_list->groupcio_action_ip. '</td>';
                        }
                    }
                    $capexOutput = '';
                    $opexOutput  = '';

                    if (!empty($workflowStages)) {
                        
                        foreach ($workflowStages as $workflowStage) {

                            $signatures = DB::table('capex_workflows_status')
                                ->where('nv_id', $nv_list->nv_id)
                                ->when($nv_list->service_id == null, function ($q) use ($nv_list) {
                                    $q->where('material_id', $nv_list->material_id);
                                }, function ($q) use ($nv_list) {
                                    $q->where('service_id', $nv_list->service_id);
                                })
                                ->where('nv_budget_type', 'CAPEX')   // 🔒 STRICT FILTER
                                ->whereIn('nv_stage_status', [1,2])
                                ->whereNotNull('nv_stage_remark')
                                ->where('reviewer_name', 'approver')
                                ->where('department_id', $workflowStage->work_dep)
                                ->get();

                            if ($signatures->isEmpty()) {
                                $capexOutput .= '<td>-</td>'; // empty CAPEX cell
                                continue;
                            }

                            foreach ($signatures as $signature) {

                                $name = getStatusUserName($signature->workflow_user_id ?? '');
                                $date = $signature->nv_stage_timestamp
                                    ? date('d-M-y', strtotime($signature->nv_stage_timestamp))
                                    : '';
                                $time = $signature->nv_stage_timestamp
                                    ? date('h:i A', strtotime($signature->nv_stage_timestamp))
                                    : '';

                                if ($signature->nv_stage_status == 1) {
                                    $capexOutput .= "<td>
                                        $name<br>
                                        Approval Date: $date<br>
                                        Approval Time: $time<br>
                                        System IP: {$signature->nv_stage_action_ip}
                                    </td>";
                                } else {
                                    $capexOutput .= "<td>
                                        $name<br>
                                        Rejection Date: $date<br>
                                        Rejection Time: $time<br>
                                        System IP: {$signature->nv_stage_action_ip}
                                    </td>";
                                }
                            }
                        }
                    }


                    if (!empty($opexWorkflowStages)) {
                        foreach ($opexWorkflowStages as $workflowStage) {

                            $signatures = DB::table('capex_workflows_status')
                                ->where('nv_id', $nv_list->nv_id)
                                ->when($nv_list->service_id == null, function ($q) use ($nv_list) {
                                    $q->where('material_id', $nv_list->material_id);
                                }, function ($q) use ($nv_list) {
                                    $q->where('service_id', $nv_list->service_id);
                                })
                                ->where('nv_budget_type', 'OPEX')   // 🔒 STRICT FILTER
                                ->whereIn('nv_stage_status', [1,2])
                                ->whereNotNull('nv_stage_remark')
                                ->where('reviewer_name', 'approver')
                                ->where('department_id', $workflowStage->work_dep)
                                ->get();

                            if ($signatures->isEmpty()) {
                                $opexOutput .= '<td>-</td>';
                                continue;
                            }

                            foreach ($signatures as $signature) {

                                $name = getStatusUserName($signature->workflow_user_id ?? '');
                                $date = $signature->nv_stage_timestamp
                                    ? date('d-M-y', strtotime($signature->nv_stage_timestamp))
                                    : '';
                                $time = $signature->nv_stage_timestamp
                                    ? date('h:i A', strtotime($signature->nv_stage_timestamp))
                                    : '';

                                if ($signature->nv_stage_status == 1) {
                                    $opexOutput .= "<td>
                                        $name<br>
                                        Approval Date: $date<br>
                                        Approval Time: $time<br>
                                        System IP: {$signature->nv_stage_action_ip}
                                    </td>";
                                } else {
                                    $opexOutput .= "<td>
                                        $name<br>
                                        Rejection Date: $date<br>
                                        Rejection Time: $time<br>
                                        System IP: {$signature->nv_stage_action_ip}
                                    </td>";
                                }
                            }
                        }
                    }
                  
                              
                    $output .= $capexOutput;
                    $output .= $opexOutput;

                    $output .= '</tr>';
              
                }
            
                    
            }
            $output .= '</table>';
            $output .= '</body></html>';
                
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=NV_excel.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $output;      
    }
}
