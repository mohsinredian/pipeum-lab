<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NV PDF</title>
        <style>
            #approve_list_filter>label {
                float: right;
                position: relative;
                top: -10px;
            }
   
            .dataTables_info {
                float: left;
                margin-left: -20px;
                position: relative;
                right: -15px;
   
            }
   
            .paging_simple_numbers {
                float: right;
                margin-left: -30px;
            }
   
            .buttons {
                position: relative;
                bottom: -50px;
            }
        </style>
    </head>
    <body>
        <h1 class="align-middle">NV Approve List</h1>
        <table border="1"  id="approve_list" class="table shadow-lg table-responsive dashboard-table ">

            <thead>
                <tr>
                    <th class="text-center">Sr. No</th>
                    <th class="text-center">Proposal Number</th>
                    <th class="text-center">NV Budget Type</th>
                    <th class="text-center">Sub-Department</th>
                    <th class="text-center">NV Type</th>
                    <th class="text-center">Initiated By & Date </th>
                    <th class="text-center">HOD</th>
                    <th class="text-center">Group Head</th>
                    @php
                    $workflowStages = App\Models\Workflow::where('status',1)->get();
                    $opexWorkflowStages = App\Models\OpexWorkflow::where('status',1)->get();
                    @endphp
                    @if(!empty($workflowStages))
                    @foreach ($workflowStages as $workflowStage)
                        <th class="text-center">{{ getPrefixDepartmentName($workflowStage->work_dep) }}<br>(CAPEX)</th>
                    @endforeach
                    @endif
                    @if(!empty($opexWorkflowStages))
                    @foreach ($opexWorkflowStages as $opexWorkflowStage)
                        <th class="text-center">{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }}<br>(OPEX)</th>
                    @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                    $no = 101;
                   
                ?>
               
                @if (!empty($nv_sm_data))
                    @foreach ($nv_sm_data as $nv_list)
                        @php
                           
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
                           
                            $nv_id[] = $nv_list->nv_id;
                           
                            $result = [];
                           
                            foreach ($nv_id as $key => $value) {
                                $count = count(array_keys($nv_id, $value)); // Count the occurrences of the value
                           
                                if ($count > 1) {
                                    for ($i = 0; $i <= $count; $i++) {
                                        $result = $value . '-v' . $i;
                                    }
                                } else {
                                    $result = $value;
                                }
                               
                            }

                          
                        @endphp

                        @php
                            $nvid = $nv_list->nv_id;
                            $count = \App\Models\Nvsericestatus::where('nv_id', $nvid)->count();
                            $currentOccurrence = \App\Models\Nvsericestatus::where('nv_id', $nvid)->where('created_at', '<=', $nv_list->created_at)->count();
                        @endphp

                        @if ($currentOccurrence > 1)
                            @for ($i = 1; $i <= $count; $i++) 
                                @php
                                    $result = $nvid . '-v' . $currentOccurrence; 
                                @endphp
                            @endfor
                        @else
                            @php
                                $result = $nvid;
                            @endphp
                        @endif
                        
                        <tr style="margin-bottom:30px; !important">
                            @php $service= getServiceName($nv_list->nv_id);@endphp
                            <?php
                            $url = '/admin/nv_' . lcfirst($service) . '/create/' . $nv_list->nv_id.'/'.
                            $nv_list->company_id;
                           
                            $i = $key + 1;
                            ?>
                            <td>{{ $i }}</td>
                            <td>
                                <span>NV/{{ $proposal_no->budget_type }}/FY{{ $proposal_no->fiscal_year }}/
                                    @if ($nv_list->service_id == null)
                                        {{ getDepartmentName($nv_list->material->dept_id) }}
                                    @else
                                        {{ getDepartmentName($nv_list->service->dept_id) }}
                                    @endif
                                    /{{ getServiceName($nv_list->nv_id) }} / {{ $nv_list->version_nv}}
                                </span>
                            </td>
                            <td>{{$proposal_no->budget_type}}</td>
                            <td>
                                @if ($nv_list->service_id == null)
                                    {{ getDepartmentName($nv_list->material->dept_id) }}
                                @else
                                    {{ getDepartmentName($nv_list->service->dept_id) }}
                                @endif
                            </td>
                            <td>
                                @if($nv_list->service_id == null && $nv_list->material->ser_rel_nv == 2)
                                   
                                    {{ getServiceName($nv_list->nv_id) }}+Service
                                        @else
                                    {{ getServiceName($nv_list->nv_id) }} 
                                @endif
                            </td>
                            @if ($nv_list->service_id == null)
                                <td> {{ getUserName($nv_list->material->user_id) }}<br>
                                    <span>{{ date('d-M-y', strtotime($nv_list->created_at)) }} <br>
                                        {{ date('h:i A', strtotime($nv_list->created_at)) }}
                                    </span>
                                </td>
                            @else
                                <td> {{ getUserName($nv_list->service->user_id) }}<br>
                                    <span>{{ date('d-M-y', strtotime($nv_list->created_at)) }} <br>
                                        {{ date('h:i A', strtotime($nv_list->created_at)) }}
                                    </span>
                                </td>
                            @endif
                           
                            @if ($nv_list->service_id == null)
                                <td>
                                    @if ($nv_list->hod_status == 0)
                                        <b> </b><br>
                                        @if (!empty($nv_list->hod_timestamp))
                                            {{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}
                                            <br> {{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}
                                        @endif
                                    @elseif($nv_list->hod_status == 1)
                                       {{ getHodName($nv_list->hod_id ?? '') }}
                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                            <span><b>Approval Date </b></span> : <span id='hod-approve-date'>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>Approval Time </b></span> : <span id='hod-approve-time'>{{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='hod-approve-ip'>{{$nv_list->hod_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='hod-approve-days'>{{$days}}</span>  -->
                                        </span>
                                    @elseif($nv_list->hod_status == 2)
                                       {{ getHodName($nv_list->hod_id ?? '') }}
                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                            <span><b>Rejection Date </b></span> : <span id='hod-reject-date'>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>Rejection Time </b></span> : <span id='hod-reject-time'>{{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='hod-reject-ip'>{{$nv_list->hod_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='hod-reject-days'>{{$days}}</span> -->
                                        </span>
                                    @endif
                                </td>
                            @else
                                <td>

                                    @if ($nv_list->hod_status == 0)
                                        <b> </b><br>
                                        @if (!empty($nv_list->hod_timestamp))
                                            {{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}
                                            <br> {{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}
                                        @endif
                                    @elseif($nv_list->hod_status == 1)
                                      {{ getHodName($nv_list->hod_id ?? '') }}
                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                            <span><b>Approval Date </b></span> : <span id='hod-approve-date'>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>Approval Time </b></span> : <span id='hod-approve-time'>{{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='hod-approve-ip'>{{$nv_list->hod_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='hod-approve-days'>{{$days}}</span> -->
                                        </span>
                                    @elseif($nv_list->hod_status == 2)
                                      {{ getHodName($nv_list->hod_id ?? '') }}
                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                            <span><b>Rejection Date </b></span> : <span id='hod-reject-date'>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>Rejection Time </b></span> : <span id='hod-reject-time'>{{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='hod-reject-ip'>{{$nv_list->hod_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='hod-reject-days'>{{$days}}</span> -->
                                        </span>
                                    @endif
                                </td>
                            @endif

                            @if ($nv_list->service_id == null)
                                <td>
                                    @if ($nv_list->groupcio_status == 0)
                                            <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                                {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                                <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                        </span>
                                         @endif
                                    @elseif($nv_list->groupcio_status == 1)
                                       {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$nv_list->groupcio_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$groupheaddays}}</span> -->
                                        </span>
                                    @elseif($nv_list->groupcio_status == 2)
                                        {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$nv_list->groupcio_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$groupheaddays}}</span> -->
                                        </span>
                                    @endif
                                </td>
                            @else
                                <td>
                                    @if ($nv_list->groupcio_status == 0)
                                        <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                                {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                                <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                        </span>
                                         @endif
                                    @elseif($nv_list->groupcio_status == 1)
                                       {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$nv_list->groupcio_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$groupheaddays}}</span> -->
                                        </span>
                                    @elseif($nv_list->groupcio_status == 2)
                                       {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}</span><br>
                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$nv_list->groupcio_action_ip}}</span><br>
                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$groupheaddays}}</span> -->
                                        </span>
                                    @endif
                                </td>
                            @endif
                          
                            @if(!empty($workflowStages))
                                @foreach ($workflowStages as $workflowStage)

                                        @php 
                                            $signatures = null;

                                            if ($nv_list->service_id == null){
                                                        $signatures = DB::table('capex_workflows_status')
                                                        ->where('nv_id', $nv_list->nv_id)
                                                        ->where('material_id', $nv_list->material_id)
                                                        ->whereIn('nv_stage_status',[1,2])
                                                        ->where('nv_budget_type', 'CAPEX')
                                                        ->where('nv_stage_remark', '!=', null)
                                                        ->where('reviewer_name', 'approver')
                                                        ->where('department_id', $workflowStage->work_dep)
                                                        ->get();
                                            }else{
                                                        $signatures = DB::table('capex_workflows_status')
                                                        ->where('nv_id', $nv_list->nv_id)
                                                        ->where('service_id', $nv_list->service_id)
                                                        ->whereIn('nv_stage_status',[1,2])
                                                        ->where('nv_budget_type', 'CAPEX')
                                                        ->where('nv_stage_remark', '!=', null)
                                                        ->where('reviewer_name', 'approver')
                                                        ->where('department_id', $workflowStage->work_dep)
                                                        ->get();
                                            }
                                        @endphp
                                    <td>
                                        @if(!empty($signatures))
                                            @foreach($signatures as $signature)
                                                @if ($nv_list->service_id == null)
                                                    @if($signature->nv_stage_status == 1)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                    
                                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @elseif($signature->nv_stage_status == 2)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @endif
                                                
                                                @else
                                                    @if($signature->nv_stage_status == 1)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @elseif($signature->nv_stage_status == 2)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @endif
                                                    
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            @endif

                            @if(!empty($opexWorkflowStages))
                                @foreach ($opexWorkflowStages as $opexWorkflowStage)

                                    @php 
                                        $signatures = null;
                                        if ($nv_list->service_id == null){
                                            $signatures = DB::table('capex_workflows_status')
                                                ->where('nv_id', $nv_list->nv_id)
                                                ->where('material_id', $nv_list->material_id)
                                                ->whereIn('nv_stage_status',[1,2])
                                                ->where('nv_budget_type', 'OPEX')
                                                ->where('nv_stage_remark', '!=', null)
                                                ->where('reviewer_name', 'approver')
                                                ->where('department_id', $opexWorkflowStage->work_dep)
                                                ->get();
                                        }else{
                                            $signatures = DB::table('capex_workflows_status')
                                                ->where('nv_id', $nv_list->nv_id)
                                                ->where('service_id', $nv_list->service_id)
                                                ->whereIn('nv_stage_status',[1,2])
                                                ->where('nv_budget_type', 'OPEX')
                                                ->where('nv_stage_remark', '!=', null)
                                                ->where('reviewer_name', 'approver')
                                                ->where('department_id', $opexWorkflowStage->work_dep)
                                                ->get();
                                        }
                                
                                    @endphp
                                    <td>
                                        @if(!empty($signatures))
                                            @foreach($signatures as $signature)
                                                @if ($nv_list->service_id == null) 
                                                    @if($signature->nv_stage_status == 1)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                    
                                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @elseif($signature->nv_stage_status == 2)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @endif
                                                
                                                @else
                                                    @if($signature->nv_stage_status == 1)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                            <span><b>Approval Date </b></span> : <span id='ceon2-approve-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Approval Time </b></span> : <span id='ceon2-approve-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-approve-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-approve-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @elseif($signature->nv_stage_status == 2)
                                                        {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                            <span><b>Rejection Date </b></span> : <span id='ceon2-reject-date'>{{ date('d-M-y', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>Rejection Time </b></span> : <span id='ceon2-reject-time'>{{ date('h:i A', strtotime($signature->nv_stage_timestamp)) }}</span><br>
                                                            <span><b>System IP </b></span> : <span id='ceon2-reject-ip'>{{$signature->nv_stage_action_ip}}</span><br>
                                                            <!-- <span><b>Pendency </b></span> : <span id='ceon2-reject-days'>{{$cesdays}}</span> -->
                                                        </span>
                                                    @endif
                                                
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            @endif              

                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <script>
            $(document).ready(function() {
                if ($(document).find('#approve_list').length > 0) {
                    $('#approve_list').DataTable({
                        responsive: false,
                        searching: true,
                        lengthChange: false,
                        dom: 'Bfrtip',
                        order: [[0, 'asc']],
   
   
                    });
                }
            });
        </script>
        
        <script src="http://thecodeplayer.com/uploads/js/prefixfree-1.0.7.js" type="text/javascript" type="text/javascript">
        </script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('admin/js/brand.js') }}"></script>
        <!-- <script src="{{ asset('admin/js/employee.js') }}"></script> -->
        <script src="{{ asset('theme/plugins/chart.js/Chart.min.js') }}"></script>
    </body>
</html>