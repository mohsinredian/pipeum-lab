@extends('admin.layout.master', ['page_title' => 'report'])
@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <style>
        *{
            font-family: 'Poppins', sans-serif;
        }
        .table td {
                   text-align: center;
                  } 

        .table th {
                   text-align: center;
                  } 

       .pagination{
                   float:right;
                  }

                  button.btn.btn-secondary.buttons-pdf.buttons-html5, button.btn.btn-secondary.buttons-excel.buttons-html5 {
                    background-color: rgb(3, 142, 220) !important;
                    margin-bottom:30px;
                  }

        .th-label{
          background-color:#153f57 !important;
        }
</style>
   
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage NV Tracker</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">NV Tracker</li>
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
                    <h3 class="card-label">NV Tracker</h3>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body ">
              <!-- <div class="row">
              <div class="col-1">
                <a href="/admin/consolidated_report/download-excel" class="btn btn-primary font-weight-bolder"
                  target="_blank">
                  <i class="fas fa-file-download mr-1"></i> Excel</a>
              </div>

              <div class="col-11">
                <form action="/admin/nv_tracker_list" method="get">
                  <div class="row justify-content-end">
                    <div class="col-4">
                      <div class="row justify-content-end">
                        <div class="col-10">
                          <input type="text" name="search" class="form-control form-control-sm search"
                            placeholder="Search by Material Fiscal Year/Sub-Department/NV Type/Budget Category/Emergency Flag/NV Description"
                            value="{{ request()->input('search') }}">
                        </div>

                        <div class="col-2 d-flex justify-content-end">
                          <button type="submit" class="btn-sm btn-primary form-control-sm">Search</button>
                        </div>
                      </div>
                    </div>

                  </div>
                </form>
              </div>
            </div> -->

   <table id="nv_tracker" class="table table-bordered mt-3 table-responsive">
    <thead class="text-center">
        <tr>
            <th colspan="8" class="th-label">NV Details</th>
            <th colspan="9" class="th-label">HOD Level</th>
            <th colspan="9" class="th-label">CES Level</th>
            <th colspan="9" class="th-label">CPMG Level</th>
            <th colspan="9" class="th-label">CEO Nominee 1 Level</th>
            <th colspan="9" class="th-label">CEO Nominee 2 Level</th>
            <th colspan="9" class="th-label">Group Head Level</th>
            <th colspan="9" class="th-label">CTO Level</th>
            <th colspan="2" class="th-label">CEO Level</th>
            <th colspan="5" class="th-label">No. of days with</th>
            <th colspan="5" class="th-label">On all level</th>
        </tr>
        <tr>
            <th class="text-center" width="5%">S.No</th>
            <th>Fiscal Year</th>
            <th>Sub-Department</th>
            <th>NV Type / NV Number</th>
            <th>Budget Category</th>
            <th>Emergency Flag</th>
            <th>NV Description</th>
            <th>NV Amount</th>

            <th>NV Receipt Date At HOD</th>
            <th>Set-1 Query Raised By HOD</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By HOD</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By HOD</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By HOD On</th>
            <th>Remarks if any by HOD</th>

            <th>NV Receipt Date At Group Head</th>
            <th>Set-1 Query Raised By Group Head</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By Group Head</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By Group Head</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By Group Head On</th>
            <th>Remarks if any by Group Head</th>

            <th>NV Receipt Date At CES</th>
            <th>Set-1 Query Raised By CES</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By CES</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By CES</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By CES On</th>
            <th>Remarks if any by CES</th>

            <th>NV Receipt Date At CPMG</th>
            <th>Set-1 Query Raised By CPMG</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By CPMG</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By CPMG</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By CPMG On</th>
            <th>Remarks if any by CPMG</th>

            <th>NV Receipt Date At CEO Nominee 1</th>
            <th>Set-1 Query Raised By CEO Nominee 1</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By CEO Nominee 1</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By CEO Nominee 1</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By CEO Nominee 1 On</th>
            <th>Remarks if any by CEO Nominee 1</th>

            <th>NV Receipt Date At CEO Nominee 2</th>
            <th>Set-1 Query Raised By CEO Nominee 2</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By CEO Nominee 2</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By CEO Nominee 2</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By CEO Nominee 2 On</th>
            <th>Remarks if any by CEO Nominee 2</th>

           

            <th>NV Receipt Date At CTO</th>
            <th>Set-1 Query Raised By CTO</th>
            <th>Set-1 Query Replied By User On</th>
            <th>Set-2 Query Raised By CTO</th>
            <th>Set-2 Query Replied By User On</th>
            <th>Set-3 Query Raised By CTO</th>
            <th>Set-3 Query Replied By User On</th>
            <th>Approved By CTO On</th>
            <th>Remarks if any by CTO</th>

            <th>Approval/Rejection given on</th>
            <th>Final Status on NV</th>

            <th>HOD</th>
            <th>Group Head</th>
            <th>CES</th>
            <th>CPMG</th>
            <th>CEO Nominee 1</th>
            <th>CEO Nominee 2</th>
            <th>CTO</th>
            <th>CEO</th>
            <th>Current Status</th>
            
        </tr>
    </thead>
      <tbody>
      @if (!empty($nv_sm_data))
      @php
            $sno = count($nv_sm_data);
            $sequenceNumber = $sno;
            
            @endphp
                   
                    @foreach ($nv_sm_data as $key => $nv_list)
                        @php

                          $id0 = App\Models\Workflow::where('id', 1)->first();
                          $id1 = App\Models\Workflow::skip(1)->first();
                          $id2 = App\Models\Workflow::skip(2)->first();
                          $id3 = App\Models\OpexWorkflow::where("id", 1)->first();
                          $id4 = App\Models\Workflow::skip(3)->first();
                          $id5 = App\Models\Workflow::skip(4)->first();
                          $id6 = App\Models\OpexWorkflow::skip(1)->first();
                          $id7 = App\Models\OpexWorkflow::skip(2)->first();
                           
                            $proposal_no = getProposalNumber($nv_list->nv_id);
                            $status_obj = getAllStatus($nv_list->nv_id);

                            if($nv_list->service_id != null){
                              $id = $nv_list->service_id;
                              $depId = App\Models\NVService::where('id',$id)->first();
                            }else{
                              $id = $nv_list->material_id;
                              $depId = App\Models\NVMaterial::where('id',$id)->first();
                            }
                            $department = App\Models\Department::where('id',$depId->dept_id)->first();
                            $group_cio = $department->group_cio ?? '';

                            $inc_date = new DateTime($nv_list->created_at);

                            if($nv_list->hod_timestamp != null){
                              $hod_app = new DateTime($nv_list->hod_timestamp);
                              $hodinterval = $hod_app->diff($inc_date);
                              $hoddays = $hodinterval->days;
                            }else{
                              $hoddays = 0;
                            }
                            if($nv_list->ces_timestamp != null){
                            $ces_app = new DateTime($nv_list->ces_timestamp);
                            $cesinterval = $ces_app->diff($inc_date);
                            $cesdays = $cesinterval->days;
                            }else{
                              $cesdays = 0;
                            }
                            if($nv_list->cpmg_timestamp != null){
                            $cpmg_app = new DateTime($nv_list->cpmg_timestamp);
                            $cpmginterval = $cpmg_app->diff($inc_date);
                            $cpmgdays = $cpmginterval->days;
                            }else{
                            $cpmgdays = 0;
                            }
                            if($nv_list->cto_timestamp != null){
                            $cto_app = new DateTime($nv_list->cto_timestamp);
                            $ctointerval = $cto_app->diff($inc_date);
                            $ctodays = $ctointerval->days;
                            }else{
                            $ctodays = 0;
                            }
                            if($nv_list->ceo_nominee_timestamp != null){
                            $ceon_app = new DateTime($nv_list->ceo_nominee_timestamp);
                            $ceoninterval = $ceon_app->diff($inc_date);
                            $ceondays = $ceoninterval->days;
                            }else{
                            $ceondays = 0;
                            }
                            if($nv_list->groupcio_timestamp != null){
                            $groupcio_app = new DateTime($nv_list->groupcio_timestamp);
                            $groupciointerval = $groupcio_app->diff($inc_date);
                            $groupciodays = $groupciointerval->days;
                            }else{
                            $groupciodays = 0;
                            }
                            if($nv_list->ceo_nominee2_timestamp != null){
                            $ceon2_app = new DateTime($nv_list->ceo_nominee2_timestamp);
                            $ceon2interval = $ceon2_app->diff($inc_date);
                            $ceon2days = $ceon2interval->days;
                            }else{
                            $ceon2days = 0;
                            }
                            if($nv_list->ceo_timestamp != null){
                            $ceo_app = new DateTime($nv_list->ceo_timestamp);
                            $ceointerval = $ceo_app->diff($inc_date);
                            $ceodays = $ceointerval->days;
                            }else{
                            $ceodays = 0;  
                            }

                            $nv_id[] = $nv_list->nv_id;
                           
                           foreach ($nv_id as $key => $value) {
                               $count = count(array_keys($nv_id, $value));
                          
                               if ($count > 1) {
                                   for ($i = 0; $i <= $count; $i++) {
                                       $result = $value . '-v' . $i;
                                   }
                               } else {
                                   $result = $value;
                               }
                           }
                            $service= getServiceName($nv_list->nv_id);
                            $NeedValidation = App\Models\NeedValidation::select("id", "service_id")->find($nv_list->nv_id);
                            $name_service= $NeedValidation->service_id == 1 ? 'material' : 'service';
                        @endphp
                        <tr style="margin-bottom:30px; !important">
                        <td>{{$key +1}}</td>
                        <td>{{ $proposal_no->fiscal_year }}</td>
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
                            @endif / {{$result}}
                            </td>
                        <td>{{ $proposal_no->budget_type }}</td>
                        @php
                        if ($nv_list->service_id == null){
                          $nvType = $nv_list->material;
                        }else{
                          $nvType = $nv_list->service;
                        }
                        @endphp
                        <td>@if(!empty($nvType->approved_budget) && empty($nvType->add_budget))
                          Approved
                          @elseif(empty($nvType->approved_budget) && !empty($nvType->add_budget))
                          Additional
                          @else 
                          Approved + Additional
                          @endif
                       </td>
                          @php 
                                $proposal_name = 'N/A';
                                  if ($nv_list->service_id != null && isset($nv_list->service)) {
                                      // Fetch from Service Table
                                      $proposal_name = $nv_list->service->proposal_name ?? 'N/A';
                                  } 
                                  elseif ($nv_list->service_id == null && isset($nv_list->material)) {
                                      // Fetch from Material Table
                                      $proposal_name = $nv_list->material->proposal_name ?? 'N/A';
                                  }
                          @endphp
                        <td> 
                          {{ $proposal_name }}
                        </td>
                        {{-- <td>
                          @if($nv_list->service_id == null) 
                              {{ $nv_list->material->total_budget_both >= 100000 ? ($nv_list->material->total_budget_both / 100000) . ' Lacs' : ($nv_list->material->total_budget_both) . ' Rs' }}
                          @else
                              {{ $nv_list->service->total_buget >= 100000 ? ($nv_list->service->total_buget / 100000) . ' Lacs' : ($nv_list->service->total_buget) . ' Rs' }}
                          @endif
                      </td> --}}
                      <td>
                        @if($nv_list->service_id == null) 
                            {{ 
                                $nv_list->material->total_budget_both >= 100000 
                                    ? number_format($nv_list->material->total_budget_both / 100000, ($nv_list->material->total_budget_both % 100000 == 0 ? 0 : 2)) . ' Lacs' 
                                    : number_format($nv_list->material->total_budget_both, ($nv_list->material->total_budget_both % 1 == 0 ? 0 : 2)) . ' Rs' 
                            }}
                        @else
                            {{ 
                                $nv_list->service->total_buget >= 100000 
                                    ? number_format($nv_list->service->total_buget / 100000, ($nv_list->service->total_buget % 100000 == 0 ? 0 : 2)) . ' Lacs' 
                                    : number_format($nv_list->service->total_buget, ($nv_list->service->total_buget % 1 == 0 ? 0 : 2)) . ' Rs' 
                            }}
                        @endif
                    </td>
                      @php
                          if($nv_list->service_id == null) {
                            $GH_clarifications = $GH_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $HOD_clarifications = $HOD_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $CES_clarifications = $CES_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $CPMG_clarifications = $CPMG_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $CEONM1_clarifications = $CEONM1_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $CEONM2_clarifications = $CEONM2_material_clarification_logs[$nv_list->material->id] ?? collect();
                            $CTO_clarifications = $CTO_material_clarification_logs[$nv_list->material->id] ?? collect();
                          }else{
                            $GH_clarifications = $GH_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $HOD_clarifications = $HOD_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $CES_clarifications = $CES_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $CPMG_clarifications = $CPMG_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $CEONM1_clarifications = $CEONM1_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $CEONM2_clarifications = $CEONM2_service_clarification_logs[$nv_list->service->id] ?? collect();
                            $CTO_clarifications = $CTO_service_clarification_logs[$nv_list->service->id] ?? collect();
                          }
                          $formatDate = fn($timestamp) => $timestamp ? date('d-M-y h:i A', strtotime($timestamp)) : ' ';

                      @endphp

                      <td>
                      @if($nv_list->rv4_status == 1)
                      {{ $nv_list->rv4_timestamp ? date('d-M-y h:i A', strtotime($nv_list->rv4_timestamp)) : ' ' }}
                      @elseif($nv_list->rv3_status == 1)
                      {{ $nv_list->rv3_timestamp ? date('d-M-y h:i A', strtotime($nv_list->rv3_timestamp)) : ' ' }}
                      @elseif($nv_list->rv2_status == 1)
                      {{ $nv_list->rv2_timestamp ? date('d-M-y h:i A', strtotime($nv_list->rv2_timestamp)) : ' ' }}
                      @elseif($nv_list->rv1_status == 1)
                      {{ $nv_list->rv1_timestamp ? date('d-M-y h:i A', strtotime($nv_list->rv1_timestamp)) : ' ' }}
                      @elseif($nv_list->draft == 1)
                      {{ $nv_list->updated_at ? date('d-M-y h:i A', strtotime($nv_list->updated_at)) : ' ' }}
                      @endif
                  </td>
                  <td>{{ $HOD_clarifications->get(0) ? $formatDate($HOD_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $HOD_clarifications->get(0) ? $formatDate($HOD_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $HOD_clarifications->get(1) ? $formatDate($HOD_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $HOD_clarifications->get(1) ? $formatDate($HOD_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $HOD_clarifications->get(2) ? $formatDate($HOD_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $HOD_clarifications->get(2) ? $formatDate($HOD_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if($nv_list->hod_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->hod_timestamp))}} @endif</td>
                  <td>@if($nv_list->hod_status == 1) {{$nv_list->hod_remark}} @endif</td>

                  <td>
                      @if($nv_list->hod_status == 1)
                        {{ $nv_list->hod_timestamp ? date('d-M-y h:i A', strtotime($nv_list->hod_timestamp)) : ' ' }}
                      @endif
                  </td>
                  <td>{{ $GH_clarifications->get(0) ? $formatDate($GH_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $GH_clarifications->get(0) ? $formatDate($GH_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $GH_clarifications->get(1) ? $formatDate($GH_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $GH_clarifications->get(1) ? $formatDate($GH_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $GH_clarifications->get(2) ? $formatDate($GH_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $GH_clarifications->get(2) ? $formatDate($GH_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if($nv_list->groupcio_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->groupcio_timestamp))}} @endif</td>
                  <td>@if($nv_list->groupcio_status == 1) {{$nv_list->groupcio_remark}} @endif</td>
                   
                  <td>
                  @if(!empty($id0->approver))
                      @if($proposal_no->budget_type == 'CAPEX' && !empty($group_cio) && $nv_list->groupcio_status == 1)
                      {{ $nv_list->groupcio_timestamp ? date('d-M-y h:i A', strtotime($nv_list->groupcio_timestamp)) : ' ' }}
                      @elseif($proposal_no->budget_type == 'CAPEX' && $nv_list->hod_status == 1)
                          {{ $nv_list->hod_timestamp ? date('d-M-y h:i A', strtotime($nv_list->hod_timestamp)) : ' ' }}
                      @endif
                  @endif
                  </td>
                  <td>{{ $CES_clarifications->get(0) ? $formatDate($CES_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $CES_clarifications->get(0) ? $formatDate($CES_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CES_clarifications->get(1) ? $formatDate($CES_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $CES_clarifications->get(1) ? $formatDate($CES_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CES_clarifications->get(2) ? $formatDate($CES_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $CES_clarifications->get(2) ? $formatDate($CES_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td> @if(!empty($id0->approver)) @if($nv_list->ces_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->ces_timestamp))}} @endif @endif</td>
                  <td>@if($nv_list->ces_status == 1) {{$nv_list->ces_remark}} @endif</td>

                  <td>
                    @if(!empty($id1->approver))
                      @if($nv_list->ces_status == 1)
                          {{ $nv_list->ces_timestamp ? date('d-M-y h:i A', strtotime($nv_list->ces_timestamp)) : ' ' }}
                      @endif
                    @endif
                  </td>
                  <td>{{ $CPMG_clarifications->get(0) ? $formatDate($CPMG_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $CPMG_clarifications->get(0) ? $formatDate($CPMG_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CPMG_clarifications->get(1) ? $formatDate($CPMG_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $CPMG_clarifications->get(1) ? $formatDate($CPMG_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CPMG_clarifications->get(2) ? $formatDate($CPMG_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $CPMG_clarifications->get(2) ? $formatDate($CPMG_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if(!empty($id1->approver)) @if($nv_list->cpmg_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->cpmg_timestamp))}} @endif @endif</td>
                  <td>@if($nv_list->cpmg_status == 1) {{$nv_list->cpmg_remark}} @endif</td>


                  <td>
                  @if(!empty($id2->approver))
                    @if($nv_list->cpmg_status == 1)
                    {{ $nv_list->cpmg_timestamp ? date('d-M-y h:i A', strtotime($nv_list->cpmg_timestamp)) : ' ' }}
                  @endif
                  @endif
                </td>
                  <td>{{ $CEONM1_clarifications->get(0) ? $formatDate($CEONM1_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM1_clarifications->get(0) ? $formatDate($CEONM1_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CEONM1_clarifications->get(1) ? $formatDate($CEONM1_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM1_clarifications->get(1) ? $formatDate($CEONM1_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CEONM1_clarifications->get(2) ? $formatDate($CEONM1_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM1_clarifications->get(2) ? $formatDate($CEONM1_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if(!empty($id2->approver)) @if($nv_list->cto_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->cto_timestamp))}} @endif @endif</td>
                  <td>@if($nv_list->cto_status == 1) {{$nv_list->cto_remark}} @endif</td>


                  <td>
                    @if(!empty($id3->approver))
                      @if($proposal_no->budget_type == 'OPEX' && !empty($group_cio) && $nv_list->groupcio_status == 1)
                      {{ $nv_list->groupcio_timestamp ? date('d-M-y h:i A', strtotime($nv_list->groupcio_timestamp)) : ' ' }}
                      @elseif($proposal_no->budget_type == 'OPEX' && $nv_list->hod_status == 1)
                          {{ $nv_list->hod_timestamp ? date('d-M-y h:i A', strtotime($nv_list->hod_timestamp)) : ' ' }}
                      @endif
                    @endif
                  </td>
                  <td>{{ $CEONM2_clarifications->get(0) ? $formatDate($CEONM2_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM2_clarifications->get(0) ? $formatDate($CEONM2_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CEONM2_clarifications->get(1) ? $formatDate($CEONM2_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM2_clarifications->get(1) ? $formatDate($CEONM2_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CEONM2_clarifications->get(2) ? $formatDate($CEONM2_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $CEONM2_clarifications->get(2) ? $formatDate($CEONM2_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if(!empty($id3->approver)) @if($nv_list->ceo_nominee_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->ceo_nominee_timestamp))}} @endif @endif</td>
                  <td>@if($nv_list->ceo_nominee_status == 1) {{$nv_list->ceo_nominee_remark}} @endif</td>


                  <td>
                    @if((empty($id4->approver) && !empty($id6->approver)) && (!empty($id4->approver) && empty($id6->approver)) && (!empty($id4->approver) && !empty($id6->approver)))
                      @if($proposal_no->budget_type == 'OPEX' && $nv_list->ceo_nominee_status == 1)
                        {{ $nv_list->ceo_nominee_timestamp ? date('d-M-y h:i A', strtotime($nv_list->ceo_nominee_timestamp)) : ' ' }}
                      @elseif($proposal_no->budget_type == 'CAPEX' && $nv_list->cto_status == 1)
                          {{ $nv_list->cto_timestamp ? date('d-M-y h:i A', strtotime($nv_list->cto_timestamp)) : ' ' }}
                      @endif
                    @endif
                  </td>
                  <td>{{ $CTO_clarifications->get(0) ? $formatDate($CTO_clarifications->get(0)->created_at) : ' ' }}</td>
                  <td>{{ $CTO_clarifications->get(0) ? $formatDate($CTO_clarifications->get(0)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CTO_clarifications->get(1) ? $formatDate($CTO_clarifications->get(1)->created_at) : ' ' }}</td>
                  <td>{{ $CTO_clarifications->get(1) ? $formatDate($CTO_clarifications->get(1)->reply_timestamp) : ' ' }}</td>
                  <td>{{ $CTO_clarifications->get(2) ? $formatDate($CTO_clarifications->get(2)->created_at) : ' ' }}</td>
                  <td>{{ $CTO_clarifications->get(2) ? $formatDate($CTO_clarifications->get(2)->reply_timestamp) : ' ' }}</td>
                  <td>@if((empty($id4->approver) && !empty($id6->approver)) && (!empty($id4->approver) && empty($id6->approver)) && (!empty($id4->approver) && !empty($id6->approver))) @if($nv_list->ceo_nominee2_status == 1) {{ date('d-M-y h:i A', strtotime($nv_list->ceo_nominee2_timestamp))}} @endif @endif</td>
                  <td>@if($nv_list->ceo_nominee2_status == 1) {{$nv_list->ceo_nominee2_remark}} @endif</td>

                  <td>@if($nv_list->ceo_status == 1 || $nv_list->ceo_status == 2)
                    {{ $nv_list->ceo_timestamp ? date('d-M-y h:i A', strtotime($nv_list->ceo_timestamp)) : ' ' }}
                    @endif
                  </td>
                  <td>@if($nv_list->ceo_status == 1)
                    Approved
                    @elseif($nv_list->ceo_status == 2)
                    Rejected
                    @endif
                  </td>

                  <td>@if($hoddays != 0) {{$hoddays}} @endif</td>
                  <td>@if($cesdays != 0) {{$cesdays}} @endif</td>
                  <td>@if($cpmgdays != 0) {{$cpmgdays}} @endif</td>
                  <td>@if($ctodays != 0){{$ctodays}} @endif</td>
                  <td>@if($ceondays != 0) {{$ceondays}} @endif</td>
                  <td>@if($groupciodays != 0) {{$groupciodays}} @endif</td>
                  <td>@if($ceon2days != 0) {{$ceon2days}} @endif</td>
                  <td>@if($ceodays != 0) {{$ceodays}} @endif</td>

                  <td>
                    @if($nv_list->rv1_status == 1 || 
                    $nv_list->rv2_status == 1 || 
                    $nv_list->rv3_status == 1 || 
                    $nv_list->rv4_status == 1 || 
                    $nv_list->hod_status == 1 || 
                    $nv_list->ces_rew1_status == 1 || 
                    $nv_list->ces_rew2_status == 1 || 
                    $nv_list->ces_rew3_status == 1 || 
                    $nv_list->ces_rew4_status == 1 || 
                    $nv_list->ces_status == 1 || 
                    $nv_list->work_rew1_status == 1 ||
                    $nv_list->work_rew2_status == 1 ||
                    $nv_list->work_rew3_status == 1 ||
                    $nv_list->work_rew4_status == 1 || 
                    $nv_list->approver_status == 1 || 
                    $nv_list->work_rew1dep2_status == 1 || 
                    $nv_list->work_rew2dep2_status == 1 ||
                    $nv_list->work_rew3dep2_status == 1 ||
                    $nv_list->work_rew4dep2_status == 1 ||
                    $nv_list->approverdep2_status == 1 || 
                    $nv_list->work_rew1dep3_status == 1 ||
                    $nv_list->work_rew2dep3_status == 1 || 
                    $nv_list->work_rew3dep3_status == 1 || 
                    $nv_list->work_rew4dep3_status == 1 || 
                    $nv_list->approverdep3_status == 1 ||
                    $nv_list->work_rew1dep4_status == 1 ||
                    $nv_list->work_rew2dep4_status == 1 ||
                    $nv_list->work_rew3dep4_status == 1 || 
                    $nv_list->work_rew4dep4_status == 1 || 
                    $nv_list->approverdep4_status == 1 || 
                    $nv_list->work_rew1dep5_status == 1 ||
                    $nv_list->work_rew2dep5_status == 1 || 
                    $nv_list->work_rew3dep5_status == 1 ||
                    $nv_list->work_rew4dep5_status == 1 || 
                    $nv_list->approverdep5_status == 1 || 
                    $nv_list->groupcio_status == 1 || 
                    $nv_list->ceo_status == 1)
                    Approved
                    @elseif($nv_list->rv1_status == 2 || 
                    $nv_list->rv2_status == 2 || 
                    $nv_list->rv3_status == 2 || 
                    $nv_list->rv4_status == 2 || 
                    $nv_list->hod_status == 2 || 
                    $nv_list->ces_rew1_status == 2 || 
                    $nv_list->ces_rew2_status == 2 || 
                    $nv_list->ces_rew3_status == 2 || 
                    $nv_list->ces_rew4_status == 2 || 
                    $nv_list->ces_status == 2 || 
                    $nv_list->work_rew1_status == 2 ||
                    $nv_list->work_rew2_status == 2 ||
                    $nv_list->work_rew3_status == 2 ||
                    $nv_list->work_rew4_status == 2 || 
                    $nv_list->approver_status == 2 || 
                    $nv_list->work_rew1dep2_status == 2 || 
                    $nv_list->work_rew2dep2_status == 2 ||
                    $nv_list->work_rew3dep2_status == 2 ||
                    $nv_list->work_rew4dep2_status == 2 ||
                    $nv_list->approverdep2_status == 2 || 
                    $nv_list->work_rew1dep3_status == 2 ||
                    $nv_list->work_rew2dep3_status == 2 || 
                    $nv_list->work_rew3dep3_status == 2 || 
                    $nv_list->work_rew4dep3_status == 2 || 
                    $nv_list->approverdep3_status == 2 ||
                    $nv_list->work_rew1dep4_status == 2 ||
                    $nv_list->work_rew2dep4_status == 2 ||
                    $nv_list->work_rew3dep4_status == 2 || 
                    $nv_list->work_rew4dep4_status == 2 || 
                    $nv_list->approverdep4_status == 2 || 
                    $nv_list->work_rew1dep5_status == 2 ||
                    $nv_list->work_rew2dep5_status == 2 || 
                    $nv_list->work_rew3dep5_status == 2 ||
                    $nv_list->work_rew4dep5_status == 2 || 
                    $nv_list->approverdep5_status == 2 || 
                    $nv_list->groupcio_status == 2 || 
                    $nv_list->ceo_status == 2)  
                    Rejected 
                    @else
                    Pending
                    @endif
                  </td>
                  
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
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

  {{-- For Datatable buttons cdn --}}

    <script src="{{ asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/vfs_fonts.js') }}"></script>
  <script>
    $(function() {
      $("#nv_tracker").DataTable({
        "responsive": false,
        "lengthChange": true,
        "lengthMenu": [10, 20, 30, 50, 100],
        "autoWidth": false,
         "ordering": false,
         "dom": 'Bfrtip',	
        "buttons": [
            'pdf', 'excel',
        ],
      }).buttons().container().appendTo('#nv_tracker_wrapper .col-md-6:eq(0)');
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "responsive": false,
      });
      $('#nv_tracker_filter input')
    .attr('placeholder', 'Fiscal Year/Sub-Department/NV Type/Budget Category/Emergency Flag ...')
    .css('width', '500px');

    });
</script>



@endpush