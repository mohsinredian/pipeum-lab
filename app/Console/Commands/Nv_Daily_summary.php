<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\NeedValidation;
use App\Models\User;
use SAPNWRFC\Connection;

class Nv_Daily_summary extends Command
{
    protected $signature = 'nv-daily-summary';
    protected $description = 'Send NV data to SAP daily';

    public function handle()
    {
        try {

            $this->info("Connecting to SAP...");

            $conn = new Connection([
                'ashost' => '10.8.61.49',
                'sysnr'  => '03',
                'client' => '470',
                'user'   => 'TMADVAIYA1',
                'passwd' => 'Bses@123',
                'lang'   => 'EN'
            ]);

            $this->info("SAP Connected Successfully");

            $func = $conn->getFunction('ZBAPI_NV_INSERT');

            $this->info("Function Loaded Successfully");

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

            if ($nvData->isEmpty()) {
                $this->info("No NV records found for today.");
                return Command::SUCCESS;
            }

            foreach ($nvData as $row) {

                $createdBy  = User::find($row->user_id);
                $modifiedBy = User::find($row->modified_user_id);

                // ===============================
                // BASIC VALUES
                // ===============================
                $iv_werks   = ($row->company_id == 6) ? 'D031' : 'D021';
                $iv_znvid   = 'NV' . str_pad($row->nv_id, 6, '0', STR_PAD_LEFT);

                $iv_zcredat = Carbon::parse($row->created_at)->format('Ymd');
                $iv_zcreby  = $createdBy?->name ?? '';

                $iv_zreldat = $row->modified_date
                    ? Carbon::parse($row->modified_date)->format('Ymd')
                    : '';

                $iv_zrelby  = $modifiedBy?->name ?? '';

                // ===============================
                // SAP PARAMS BUILD (YOUR LOGIC)
                // ===============================
                $params = [
                    'IV_WERKS' => $iv_werks,
                    'IV_ZNVID' => $iv_znvid,
                ];

                // If release details exist
                if (!empty($iv_zreldat) && !empty($iv_zrelby)) {

                    $params['IV_ZRELDAT'] = $iv_zreldat;
                    $params['IV_ZRELBY']  = $iv_zrelby;
                    $params['IV_ZCREDAT'] = $iv_zcredat;
                    $params['IV_ZCREBY']  = $iv_zcreby;

                } else {

                    // Only creation data
                    $params['IV_ZCREDAT'] = $iv_zcredat;
                    $params['IV_ZCREBY']  = $iv_zcreby;
                }

                // ===============================
                // DEBUG OUTPUT
                // ===============================
                $this->info("================================");
                $this->info("Sending NV: " . $iv_znvid);
                print_r($params);

                // ===============================
                // SAP CALL
                // ===============================
                try {

                    $result = $func->invoke($params);

                    $this->info("SAP Success for " . $iv_znvid);

                    print_r($result);

                    Log::info("SAP NV Success", [
                        'params' => $params,
                        'result' => $result
                    ]);

                } catch (\Throwable $e) {

                    $this->error("SAP call failed for " . $iv_znvid);

                    Log::error("SAP NV Failed", [
                        'params' => $params,
                        'error'  => $e->getMessage()
                    ]);

                    continue;
                }
            }

            return Command::SUCCESS;

        } catch (\SAPNWRFC\ConnectionException $e) {

            $this->error("SAP Connection Error: " . $e->getMessage());

            Log::error("SAP Connection Error", [
                'message' => $e->getMessage()
            ]);

        } catch (\Throwable $e) {

            $this->error("General Error: " . $e->getMessage());

            Log::error("General Error", [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }

        return Command::FAILURE;
    }
}