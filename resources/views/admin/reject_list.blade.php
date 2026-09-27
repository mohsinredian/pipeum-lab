@extends('admin.layout.master', ['page_title' => 'New Workflow'])
@push('styles')
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Reject NV List</h1>
            </div><!-- /.col -->
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h4 class="card-label">NV List</h4>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                
                    <table id="approve_list" class="table shadow-lg table-responsive dashboard-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Sr. No</th>
                                    <th class="text-center">Proposal Number</th>
                                    <th class="text-center">NV Budget Type</th>
                                    <th class="text-center">Sub-Department</th>
                                    <th class="text-center">NV Type</th>
                                    <th class="text-center">Initiated By & Date </th>
                                    <th class="text-center">HOD </th>
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
                                @if (!empty($latest))
                                @php
                                $sno = count($latest);
                                $sequenceNumber = $sno;
                                @endphp

                                @foreach ($latest as $nv_list)

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

                                foreach ($nv_id as $key => $value) {
                                $count = count(array_keys($nv_id, $value)); // Count the occurrences of the value

                                if ($count > 1) {
                                for ($i = 0; $i <= $count; $i++) { $result=$value . '-v' . $i; } } else {
                                    $result=$value; } } @endphp <tr style="margin-bottom:30px; !important">
                                    @php $service= getServiceName($nv_list->nv_id);
                                    @endphp
                                    <?php
                                    if ($nv_list->service_id == null){
                                        $service_id = $nv_list->material_id;
                                    }else{
                                        $service_id = $nv_list->service_id;
                                    }
            
                                    $url = '/admin/nv_' . lcfirst($service) . '/create/' . $nv_list->nv_id . '/' . $nv_list->company_id . '/' .$service_id;
                                    $i = $key + 1;
                                    $url_app = '/admin/nv_' . lcfirst($service) . '/preview/' . $nv_list->nv_id. '/' .$service_id;
                                    ?>
                                     <td>{{$sequenceNumber}}</td>
                                    <td>
                                        @if (Auth::user()->role_id == 1)
                                        @if($nv_list->is_reject == 1)
                                        <a style="text-decoration:none; color:white;  font-size:10px;">
                                            <button
                                                style="background-color:rgb(245, 24, 8); color:white; border:none; border-radius:5px; font-size:0.75rem;">

                                                NV/{{ $proposal_no->budget_type }}/FY
                                                {{ $proposal_no->fiscal_year }}/
                                                @if ($nv_list->service_id == null)
                                                {{ getDepartmentName($nv_list->material->dept_id) }}
                                                @else
                                                {{ getDepartmentName($nv_list->service->dept_id) }}
                                                @endif
                                                /{{ getServiceName($nv_list->nv_id) }} / {{ $nv_list->version_nv}}

                                            </button>
                                        </a>
                                        @endif
                                        @elseif(Auth::user()->role_id == 9)
                                        @if($nv_list->is_reject == 1)
                                        <a href="{{ $url_app }}"
                                            style="text-decoration:none; color:white;  font-size:10px;">
                                            <button
                                                style="background-color:rgb(220, 18, 3); color:white; border:none; border-radius:5px; font-size:0.75rem;">

                                                NV/{{ $proposal_no->budget_type }}/FY
                                                {{ $proposal_no->fiscal_year }}/
                                                @if ($nv_list->service_id == null)
                                                {{ getDepartmentName($nv_list->material->dept_id) }}
                                                @else
                                                {{ getDepartmentName($nv_list->service->dept_id) }}
                                                @endif
                                                /{{ getServiceName($nv_list->nv_id) }} / {{ $nv_list->version_nv}}

                                            </button>
                                        </a>
                                        @endif
                                        @elseif(Auth::user()->role_id == 11)
                                        @if($nv_list->is_reject == 1)
                                        <a href="{{ $url_app }}"
                                            style="text-decoration:none; color:white;  font-size:10px;">
                                            <button
                                                style="background-color:rgb(220, 18, 3); color:white; border:none; border-radius:5px; font-size:0.75rem;">

                                                NV/{{ $proposal_no->budget_type }}/FY
                                                {{ $proposal_no->fiscal_year }}/
                                                @if ($nv_list->service_id == null)
                                                {{ getDepartmentName($nv_list->material->dept_id) }}
                                                @else
                                                {{ getDepartmentName($nv_list->service->dept_id) }}
                                                @endif
                                                /{{ getServiceName($nv_list->nv_id) }} / {{ $nv_list->version_nv}}

                                            </button>
                                        </a>

                                        @endif
                                        @endif


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
                                        @if ($nv_list->service_id == null && $nv_list->material->ser_rel_nv == 2)
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
                                                <a class="hod-approve-modal"
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-approve-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-approve-days="<?= $days ?>" href="#HODapproveModal"
                                                    data-target="#HODapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODapproveModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='hod-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='hod-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='hod-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                            @elseif($nv_list->hod_status == 2)
                                            {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                <a class="hod-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-reject-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-reject-days="<?= $days ?>" href="#HODrejectModal"
                                                    data-target="#HODrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODrejectModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red">
                                                                    Rejection Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='hod-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='hod-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='hod-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

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
                                                <a class="hod-approve-modal"
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-approve-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-approve-days="<?= $days ?>" href="#HODapproveModal"
                                                    data-target="#HODapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODapproveModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='hod-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='hod-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='hod-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                            @elseif($nv_list->hod_status == 2)
                                            {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                <a class="hod-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-reject-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-reject-days="<?= $days ?>" href="#HODrejectModal"
                                                    data-target="#HODrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODrejectModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red">
                                                                    Rejection Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='hod-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='hod-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='hod-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                            @endif
                                        </td>
                                    @endif

                                    @if ($nv_list->service_id == null)
                                        <td>
                                            @if ($nv_list->groupcio_status == 0)
                                            {{-- @if($proposal_no->budget_type == 'CAPEX' && ($nv_list->ces_status == 1 || $nv_list->ces_status == 2))
                                            <span>N/A</span>
                                            @elseif($proposal_no->budget_type == 'OPEX' && ($nv_list->ceo_nominee_status == 1 || $nv_list->ceo_nominee_status == 2))
                                            <span>N/A</span>
                                            @endif --}}
                                            <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                            {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                            <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                            </span>
                                            @endif
                                            @elseif($nv_list->groupcio_status == 1)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                <a class="grouphead-approve-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-grouphead-approve-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-approve-days="<?= $groupheaddays ?>"
                                                    href="#GroupHeadapproveModal" data-target="#GroupHeadapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadapproveModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='grouphead-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='grouphead-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='grouphead-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                            @elseif($nv_list->groupcio_status == 2)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                <a class="grouphead-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-grouphead-reject-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-reject-days="<?= $groupheaddays ?>"
                                                    href="#GroupHeadrejectModal" data-target="#GroupHeadrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadrejectModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red;">Rejection
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='grouphead-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='grouphead-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='grouphead-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                            @endif
                                        </td>
                                    @else
                                        <td>
                                            @if ($nv_list->groupcio_status == 0)
                                            {{-- @if($proposal_no->budget_type == 'CAPEX' && ($nv_list->ces_status == 1 || $nv_list->ces_status == 2))
                                            <span>N/A</span>
                                            @elseif($proposal_no->budget_type == 'OPEX' && ($nv_list->ceo_nominee_status == 1 || $nv_list->ceo_nominee_status == 2))
                                            <span>N/A</span>
                                            @endif --}}
                                            <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                            {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                            <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                            </span>
                                            @endif
                                            @elseif($nv_list->groupcio_status == 1)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                <a class="grouphead-approve-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-grouphead-approve-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-approve-days="<?= $groupheaddays ?>"
                                                    href="#GroupHeadapproveModal" data-target="#GroupHeadapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadapproveModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='grouphead-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='grouphead-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='grouphead-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                            @elseif($nv_list->groupcio_status == 2)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                <a class="grouphead-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-grouphead-reject-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-reject-days="<?= $groupheaddays ?>"
                                                    href="#GroupHeadrejectModal" data-target="#GroupHeadrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadrejectModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red;">Rejection Status
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='grouphead-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='grouphead-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='grouphead-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                            
                                                                    <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                        <a class="ces-approve-modal"
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                            data-target="#CESapproveModal">
                                                                            View
                                                                        </a>
                                                                        
                                                                        <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:green;">Approval Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Approval Date </b></span> : <span
                                                                                            id='ces-approve-date'></span><br>
                                                                                        <span><b>Approval Time </b></span> : <span
                                                                                            id='ces-approve-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-approve-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span
                                                                                            id='ces-approve-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @elseif($signature->nv_stage_status == 2)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                        <a class="ces-reject-modal "
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                            data-target="#CESrejectModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:red">
                                                                                            Rejection Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Rejection Date </b></span> : <span
                                                                                            id='ces-reject-date'></span><br>
                                                                                        <span><b>Rejection Time </b></span> : <span
                                                                                            id='ces-reject-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-reject-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @endif
                                                        
                                                        @else
                                                            @if($signature->nv_stage_status == 1)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                        <a class="ces-approve-modal"
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                            data-target="#CESapproveModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:green;">Approval Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Approval Date </b></span> : <span
                                                                                            id='ces-approve-date'></span><br>
                                                                                        <span><b>Approval Time </b></span> : <span
                                                                                            id='ces-approve-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-approve-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span
                                                                                            id='ces-approve-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @elseif($signature->nv_stage_status == 2)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                        <a class="ces-reject-modal "
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                            data-target="#CESrejectModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:red">
                                                                                            Rejection Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Rejection Date </b></span> : <span
                                                                                            id='ces-reject-date'></span><br>
                                                                                        <span><b>Rejection Time </b></span> : <span
                                                                                            id='ces-reject-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-reject-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

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
                                                            
                                                                    <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                        <a class="ces-approve-modal"
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                            data-target="#CESapproveModal">
                                                                            View
                                                                        </a>
                                                                        
                                                                        <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:green;">Approval Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Approval Date </b></span> : <span
                                                                                            id='ces-approve-date'></span><br>
                                                                                        <span><b>Approval Time </b></span> : <span
                                                                                            id='ces-approve-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-approve-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span
                                                                                            id='ces-approve-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @elseif($signature->nv_stage_status == 2)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                        <a class="ces-reject-modal "
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                            data-target="#CESrejectModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:red">
                                                                                            Rejection Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Rejection Date </b></span> : <span
                                                                                            id='ces-reject-date'></span><br>
                                                                                        <span><b>Rejection Time </b></span> : <span
                                                                                            id='ces-reject-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-reject-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @endif
                                                        
                                                        @else
                                                            @if($signature->nv_stage_status == 1)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                        <a class="ces-approve-modal"
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                            data-target="#CESapproveModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:green;">Approval Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Approval Date </b></span> : <span
                                                                                            id='ces-approve-date'></span><br>
                                                                                        <span><b>Approval Time </b></span> : <span
                                                                                            id='ces-approve-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-approve-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span
                                                                                            id='ces-approve-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @elseif($signature->nv_stage_status == 2)
                                                                {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                    <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                        <a class="ces-reject-modal "
                                                                            style="border:none; background-color:none; color:black;" rel="dialog"
                                                                            data-toggle="modal"
                                                                            data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                            data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                            data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                            data-target="#CESrejectModal">
                                                                            View
                                                                        </a>
                                                                        <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                                                            style="color:red">
                                                                                            Rejection Status</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <span><b>Rejection Date </b></span> : <span
                                                                                            id='ces-reject-date'></span><br>
                                                                                        <span><b>Rejection Time </b></span> : <span
                                                                                            id='ces-reject-time'></span><br>
                                                                                        <span><b>System IP </b></span> : <span
                                                                                            id='ces-reject-ip'></span><br>
                                                                                        <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">
                                                                                            Close
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </span>
                                                            @endif
                                                        
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif 

                                    </tr>
                                    @php
                                        $sequenceNumber-- 
                                    @endphp
                                    @endforeach
                                    @endif
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </div><!-- /.container-fluid -->
</section>
@endsection

@push('script')
<script>
        $(document).ready(function() {
            if ($(document).find('#approve_list').length > 0) {
                $('#approve_list').DataTable({
                    responsive: false,
                    searching: true,
                    lengthChange: false,
                    dom: 'Bfrtip',
                    order: [[0, 'asc']]


                });
            }
        });
    </script>
<script>
    $(document).ready(function () {
        $('.hod-approve-modal').click(function () {
            $('#hod-approve-date').html($(this).data('hod-approve-date'));
            $('#hod-approve-time').html($(this).data('hod-approve-time'));
            $('#hod-approve-ip').html($(this).data('hod-approve-ip'));
            $('#hod-approve-days').html($(this).data('hod-approve-days'));

            $('#HODapproveModal').modal('show');
        });

        $('.hod-reject-modal').click(function () {
            $('#hod-reject-date').html($(this).data('hod-reject-date'));
            $('#hod-reject-time').html($(this).data('hod-reject-time'));
            $('#hod-reject-ip').html($(this).data('hod-reject-ip'));
            $('#hod-reject-days').html($(this).data('hod-reject-days'));

            $('#HODrejectModal').modal('show');
        });

        $('.ces-approve-modal').click(function () {
            $('#ces-approve-date').html($(this).data('ces-approve-date'));
            $('#ces-approve-time').html($(this).data('ces-approve-time'));
            $('#ces-approve-ip').html($(this).data('ces-approve-ip'));
            $('#ces-approve-days').html($(this).data('ces-approve-days'));

            $('#CESapproveModal').modal('show');
        });

        $('.ces-reject-modal').click(function () {
            $('#ces-reject-date').html($(this).data('ces-reject-date'));
            $('#ces-reject-time').html($(this).data('ces-reject-time'));
            $('#ces-reject-ip').html($(this).data('ces-reject-ip'));
            $('#ces-reject-days').html($(this).data('ces-reject-days'));

            $('#CESrejectModal').modal('show');
        });

        $('.cpmg-approve-modal').click(function () {
            $('#cpmg-approve-date').html($(this).data('cpmg-approve-date'));
            $('#cpmg-approve-time').html($(this).data('cpmg-approve-time'));
            $('#cpmg-approve-ip').html($(this).data('cpmg-approve-ip'));
            $('#cpmg-approve-days').html($(this).data('cpmg-approve-days'));

            $('#CPMGapproveModal').modal('show');
        });

        $('.cpmg-reject-modal').click(function () {
            $('#cpmg-reject-date').html($(this).data('cpmg-reject-date'));
            $('#cpmg-reject-time').html($(this).data('cpmg-reject-time'));
            $('#cpmg-reject-ip').html($(this).data('cpmg-reject-ip'));
            $('#cpmg-reject-days').html($(this).data('cpmg-reject-days'));

            $('#CPMGrejectModal').modal('show');
        });

        $('.cto-approve-modal').click(function () {
            $('#cto-approve-date').html($(this).data('cto-approve-date'));
            $('#cto-approve-time').html($(this).data('cto-approve-time'));
            $('#cto-approve-ip').html($(this).data('cto-approve-ip'));
            $('#cto-approve-days').html($(this).data('cto-approve-days'));

            $('#CTOapproveModal').modal('show');
        });

        $('.cto-reject-modal').click(function () {
            $('#cto-reject-date').html($(this).data('cto-reject-date'));
            $('#cto-reject-time').html($(this).data('cto-reject-time'));
            $('#cto-reject-ip').html($(this).data('cto-reject-ip'));
            $('#cto-reject-days').html($(this).data('cto-reject-days'));

            $('#CTOrejectModal').modal('show');
        });

        $('.ceon1-approve-modal').click(function () {
            $('#ceon1-approve-date').html($(this).data('ceon1-approve-date'));
            $('#ceon1-approve-time').html($(this).data('ceon1-approve-time'));
            $('#ceon1-approve-ip').html($(this).data('ceon1-approve-ip'));
            $('#ceon1-approve-days').html($(this).data('ceon1-approve-days'));

            $('#CEON1approveModal').modal('show');
        });

        $('.ceon1-reject-modal').click(function () {
            $('#ceon1-reject-date').html($(this).data('ceon1-reject-date'));
            $('#ceon1-reject-time').html($(this).data('ceon1-reject-time'));
            $('#ceon1-reject-ip').html($(this).data('ceon1-reject-ip'));
            $('#ceon1-reject-days').html($(this).data('ceon1-reject-days'));

            $('#CEON1rejectModal').modal('show');
        });

        $('.ceon2-approve-modal').click(function () {
            $('#ceon2-approve-date').html($(this).data('ceon2-approve-date'));
            $('#ceon2-approve-time').html($(this).data('ceon2-approve-time'));
            $('#ceon2-approve-ip').html($(this).data('ceon2-approve-ip'));
            $('#ceon2-approve-days').html($(this).data('ceon2-approve-days'));

            $('#CEON2approveModal').modal('show');
        });

        $('.grouphead-approve-modal').click(function () {
            $('#grouphead-approve-date').html($(this).data('grouphead-approve-date'));
            $('#grouphead-approve-time').html($(this).data('grouphead-approve-time'));
            $('#grouphead-approve-ip').html($(this).data('grouphead-approve-ip'));
            $('#grouphead-approve-days').html($(this).data('grouphead-approve-days'));

            $('#GroupHeadapproveModal').modal('show');
        });

        $('.grouphead-reject-modal').click(function () {
            $('#grouphead-reject-date').html($(this).data('grouphead-reject-date'));
            $('#grouphead-reject-time').html($(this).data('grouphead-reject-time'));
            $('#grouphead-reject-ip').html($(this).data('grouphead-reject-ip'));
            $('#grouphead-reject-days').html($(this).data('grouphead-reject-days'));

            $('#GroupHeadrejectModal').modal('show');
        });

        $('.ceon2-reject-modal').click(function () {
            $('#ceon2-reject-date').html($(this).data('ceon2-reject-date'));
            $('#ceon2-reject-time').html($(this).data('ceon2-reject-time'));
            $('#ceon2-reject-ip').html($(this).data('ceon2-reject-ip'));
            $('#ceon2-reject-days').html($(this).data('ceon2-reject-days'));

            $('#CEON2rejectModal').modal('show');
        });
        $('.ceo-approve-modal').click(function () {
            $('#ceo-approve-date').html($(this).data('ceo-approve-date'));
            $('#ceo-approve-time').html($(this).data('ceo-approve-time'));
            $('#ceo-approve-ip').html($(this).data('ceo-approve-ip'));
            $('#ceo-approve-days').html($(this).data('ceo-approve-days'));

            $('#CEOapproveModal').modal('show');
        });

        $('.ceo-reject-modal').click(function () {
            $('#ceo-reject-date').html($(this).data('ceo-reject-date'));
            $('#ceo-reject-time').html($(this).data('ceo-reject-time'));
            $('#ceo-reject-ip').html($(this).data('ceo-reject-ip'));
            $('#ceo-reject-days').html($(this).data('ceo-reject-days'));

            $('#CEOrejectModal').modal('show');
        });
    });
</script>
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

@endpush