<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Import Models (Check your actual Model namespace)
use App\Models\NeedValidation;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Nvsericestatus;
use App\Models\User;

class NvDetailsController extends Controller
{
   public function NV_summary_list_api(Request $request)
    {
        try {

           $nvData = NeedValidation::join(
                'nvservicestatus',
                'nvservicestatus.nv_id',
                '=',
                'needvalidations.id'
            )
            ->leftJoin(
                'capex_workflows_status',
                'capex_workflows_status.nv_id',
                '=',
                'needvalidations.id'
            )
            ->where('nvservicestatus.draft', 1)
            ->where('nvservicestatus.is_reject', 0)
            ->where('needvalidations.delete_draft', 0)
            ->where(function ($q) {
                $q->whereDate('needvalidations.created_at', Carbon::today())
                ->orWhere(function ($q2) {
                    $q2->where('capex_workflows_status.department_id', 19)
                        ->whereDate('capex_workflows_status.nv_stage_timestamp', Carbon::today());
                });
            })
            ->select(
                'needvalidations.id as nv_id',
                'needvalidations.company_id',
                'needvalidations.user_id',
                'needvalidations.created_at',
                'capex_workflows_status.workflow_user_id as modified_user_id',
                'capex_workflows_status.nv_stage_timestamp as modified_date'
            )
            ->get();

        $result = [];

        foreach ($nvData as $row) {

            $createdBy = User::find($row->user_id);
            $modifiedBy = User::find($row->modified_user_id);

            $result[] = [
                'IV_WERKS'   => ($row->company_id == 6) ? 'D031' : 'D021',

                // NV Number
                'IV_ZNVNR'   => 'NV' . str_pad($row->nv_id, 6, '0', STR_PAD_LEFT),

                'IV_ZCREDAI' => Carbon::parse($row->created_at)->format('Ymd'),
                'IV_ZCREBY'  => $createdBy?->name ?? '',

                'IV_ZRELDAT' => $row->modified_date
                    ? Carbon::parse($row->modified_date)->format('Ymd')
                    : '',

                'IV_ZRELBY'  => $modifiedBy?->name ?? '',
            ];
        }

        return response()->json([
            'success' => true,
            'count'   => count($result),
            'data'    => $result
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
