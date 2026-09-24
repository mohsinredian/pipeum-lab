<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NeedValidation;
use App\Models\Nvsericestatus;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Clarification;
use App\Models\Workflow;
use App\Models\MasterMaterialboq;
use App\Models\NVMaterial;
use App\Models\MateriBOQBulk;
use DB;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OpexWorkflow;
use App\Exports\MaterialExport;

class reportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
           Session::put('active', 'reports');

            return $next($request);
        });
    }
    
    
    public function reports_list(Request $request)
    {
    
        $department = Department::select('name', 'id')->where('status',1)->get();
        $search = $request->input('search', '');

        $dataQuery = MasterMaterialboq::select('activity', 'uom', 'material_short_text', 'rate_add')
            ->when($search, function ($query, $search) {
                return $query->where('activity', 'like', "%{$search}%")
                    ->orWhere('material_short_text', 'like', "%{$search}%")
                    ->orWhere('rate_add', 'like', "%{$search}%")
                    ->orWhere('uom', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc');
        // ->groupBy('activity');
        $data = $dataQuery->paginate(10);
        // $data = MasterMaterialboq::select('activity', 'uom', 'material_short_text', 'rate_add')
        //     ->orderBy('id', 'desc')
        //     ->paginate(10);
        $material_codes = MasterMaterialboq::pluck('activity')->toArray();
        $materialCounts = [];

        $deptIds = $department->pluck('id')->toArray();
        $allNvMaterials = NVMaterial::whereIn('dept_id', $deptIds)->get(['id', 'nv_id', 'dept_id']);
        $nvIdsByDept = $allNvMaterials->groupBy('dept_id')->map(fn($items) => $items->pluck('nv_id')->unique()->values());

        foreach ($nvIdsByDept as $deptId => $nv_ids) {
            $materialCounts[$deptId] = MateriBOQBulk::whereIn('nv_id', $nv_ids)
                ->whereIn('material_code', $material_codes)
                ->select('material_code', DB::raw('count(*) as count'))
                ->groupBy('material_code')
                ->pluck('count', 'material_code')
                ->toArray();
        }
    
        return view('admin.reports.consolidated_list')->with([
            'datas' => $data,
            'department' => $department,
            'materialCounts' => $materialCounts,
            'search' => $search,
        ]);
    }

    public function export_excel(Request $request)
{
    $search = $request->input('search');

    return Excel::download(
        new MaterialExport($search), 
        'Consolidated_Requirement.xlsx'
    );
}

public function nv_tracker_list(Request $request)
    {
        $capexWorkflows = Workflow::orderBy('id')->take(5)->get();
        $opexWorkflows  = OpexWorkflow::orderBy('id')->take(3)->get();
        $id0 = $capexWorkflows->get(0);
        $id1 = $capexWorkflows->get(1);
        $id2 = $capexWorkflows->get(2);
        $id3 = $opexWorkflows->get(0);
        $id4 = $capexWorkflows->get(3);
        $id5 = $capexWorkflows->get(4);
        $id6 = $opexWorkflows->get(1);
        $id7 = $opexWorkflows->get(2);

        $nvIds = NeedValidation::where('delete_draft',0)->pluck("id");
        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nvIds)->with(['service', 'material', 'user'])->where('draft',1)->orderBy('id', 'desc')->get();

        $department = Department::where('status',1)->get();
        $hod = []; // Initialize array
        $groupHead = [];
        if(!empty($department)){

            foreach($department as $dep){
                $hod[] = $dep->dep_hod;
                $groupHead[] = $dep->group_cio;
               
            }

            $GH_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
            ->whereIn('user_id', $groupHead)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                return $items->take(3);
            });
            $GH_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
            ->whereIn('user_id', $groupHead)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                return $items->take(3);
            });
            $HOD_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
            ->whereIn('user_id', $hod)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                return $items->take(3);
            });
            $HOD_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
            ->whereIn('user_id', $hod)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                return $items->take(3);
            });
        }
      

         
        if(!empty($id0->approver)){
            $CES_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
            ->where('user_id', $id0->approver)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                return $items->take(3);
            });
            $CES_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
            ->where('user_id', $id0->approver)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                return $items->take(3);
            });
        }

        if(!empty($id1->approver)){
            $CPMG_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
            ->where('user_id', $id1->approver)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                return $items->take(3);
            });
            $CPMG_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
            ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
            ->where('user_id', $id1->approver)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                return $items->take(3);
            });
        }
        if(!empty($id2->approver)){
        $CEONM1_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
        ->where('user_id', $id2->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('material_id')
        ->map(function ($items) {
            return $items->take(3);
        });
        $CEONM1_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
        ->where('user_id', $id2->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('service_id')
        ->map(function ($items) {
            return $items->take(3);
        });
    }
    if(!empty($id3->approver)){
        $CEONM2_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
        ->where('user_id', $id3->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('material_id')
        ->map(function ($items) {
            return $items->take(3);
        });
        $CEONM2_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
        ->where('user_id', $id3->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('service_id')
        ->map(function ($items) {
            return $items->take(3);
        });
    }
    if(!empty($id4->approver) || !empty($id6->approver)){
        $CTO_material_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('material_id', $nv_sm_data->pluck('material_id'))
        ->where('user_id', $id4->approver)
        ->orWhere('user_id', $id6->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('material_id')
        ->map(function ($items) {
            return $items->take(3);
        });
        $CTO_service_clarification_logs = Clarification::whereIn('nv_id', $nv_sm_data->pluck('nv_id'))
        ->whereIn('service_id', $nv_sm_data->pluck('service_id'))
        ->where('user_id', $id4->approver)
        ->orWhere('user_id', $id6->approver)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('service_id')
        ->map(function ($items) {
            return $items->take(3);
        });
    }

        return view('admin.reports.nv_tracker_list')->with([
            'nv_sm_data' => $nv_sm_data,
            'GH_material_clarification_logs' => $GH_material_clarification_logs ?? '',
            'GH_service_clarification_logs' => $GH_service_clarification_logs ?? '',
            'HOD_material_clarification_logs' => $HOD_material_clarification_logs ?? '',
            'HOD_service_clarification_logs' => $HOD_service_clarification_logs ?? '',
            'CES_material_clarification_logs' => $CES_material_clarification_logs ?? '',
            'CES_service_clarification_logs' => $CES_service_clarification_logs ?? '',
            'CPMG_material_clarification_logs' => $CPMG_material_clarification_logs ?? '',
            'CPMG_service_clarification_logs' => $CPMG_service_clarification_logs ?? '',
            'CEONM1_material_clarification_logs' => $CEONM1_material_clarification_logs ?? '',
            'CEONM1_service_clarification_logs' => $CEONM1_service_clarification_logs ?? '',
            'CEONM2_material_clarification_logs' => $CEONM2_material_clarification_logs ?? '',
            'CEONM2_service_clarification_logs' => $CEONM2_service_clarification_logs ?? '',
            'CTO_material_clarification_logs' => $CTO_material_clarification_logs ?? '',
            'CTO_service_clarification_logs' => $CTO_service_clarification_logs ?? '',
        ]);
    }
    
}
