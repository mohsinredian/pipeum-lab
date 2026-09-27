@extends('admin.layout.master', ['page_title' => 'Dashboard'])

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
                <!-- <div class="header-right d-flex flex-wrap mt-2 mt-sm-0 align-items-center">
                                                                                                                                                                                                                <h5 class="mr-1">Date :</h5>
                                                                                                                                                                                                                <h5 class="font-weight-normal mr-3 text-muted">{{ date('jS \of F Y h:i:s A') }}</h5>
                                                                                                                                                                                                              
                                                                                                                                                                                                              </div> -->
            </div><!-- /.col -->
            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        @if (session()->has('notification'))
        <div class="alert alert-{{ session('notification')['type'] }}">
            {!! session('notification')['message'] !!}
        </div>
        @endif
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->


<?php
    $TodDate = date('Y-m-d');
    $tommDate = date('Y-m-d', strtotime('+1 day', strtotime($TodDate)));
    ?>


@if (\Auth::user()->isA('Admin'))
<!-- <div class="card-header">
                                                                                                                                                                                            <h3 class="card-title"><b>User's, Company & Location Information</b></h3>
                                                                                                                                                                                        </div>
                                                                                                                                                                                        <div class="row mt-3">

                                                                                                                                                                                            <div class="col-lg-4 col-6">
                                                                                                                                                                                               
                                                                                                                                                                                                <div class="small-box bg-info rounded-0 shadow-lg">
                                                                                                                                                                                                    <div class="inner">
                                                                                                                                                                                                        <h3></h3>

                                                                                                                                                                                                        <p class="text-white">Total Employee</p>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="icon">
                                                                                                                                                                                                        <i class="ion ion-person-stalker"></i>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <a href="/admin/employees" class="small-box-footer">More info <i
                                                                                                                                                                                                            class="fas fa-arrow-circle-right"></i></a>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> -->

<!-- <div class="col-lg-4 col-6">
                                                                                                                                                                                               
                                                                                                                                                                                                <div class="small-box bg-success rounded-0 shadow-lg">
                                                                                                                                                                                                    <div class="inner">
                                                                                                                                                                                                        <h3></h3>

                                                                                                                                                                                                        <p class="text-white">Total Companies</p>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="icon">
                                                                                                                                                                                                        <i class="ion ion-briefcase"></i>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <a href="/admin/company" class="small-box-footer">More info <i
                                                                                                                                                                                                            class="fas fa-arrow-circle-right"></i></a>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> -->

<!-- <div class="col-lg-4 col-6">
                                                                                                                                                                                             
                                                                                                                                                                                                <div class="small-box bg-warning rounded-0 shadow-lg">
                                                                                                                                                                                                    <div class="inner">
                                                                                                                                                                                                        <h3 class="text-white"></h3>

                                                                                                                                                                                                        <p class="text-white">Total Locations</p>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="icon">
                                                                                                                                                                                                        <i class="ion ion-location"></i>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <a href="/admin/locations" class="small-box-footer">More info <i
                                                                                                                                                                                                            class="fas fa-arrow-circle-right"></i></a>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> -->

<!-- <div class="col-lg-3 col-6">
                                                                                                                                                                                              
                                                                                                                                                                                                <div class="small-box bg-danger rounded-0 shadow-lg">
                                                                                                                                                                                                    <div class="inner">
                                                                                                                                                                                                        <h3></h3>

                                                                                                                                                                                                        <p class="text-white">Total Tickets</p>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="icon">
                                                                                                                                                                                                        <i class="ion ion-compose"></i>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>  -->
<!-- </div>
                                                                                                                                                                                    

                                                                                                                                                                                    </div>
                                                                                                                                                                                </div> -->
@endif

<section class="content">
    <div class="container-fluid">
        <div class="row mb-4 justify-content-end">
            <div class="col-3 ">
                <label>Fiscal Year<span class="mandatory_input"></span></label>
                <select class="form-control" id="fiscal_year" name="fiscal_year">
                    <option value="">-- Select Fiscal Year --</option>
                    <option value="2022-23">2022-23</option>
                    <option value="2023-24">2023-24</option>
                    <option value="2024-25">2024-25</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card retAjax">
                    <div class="card-header" style="background: linear-gradient(to bottom, #ec5f67 40%, #b82d35);">
                        <h3 class="card-title text-white"><b>Need Validation Details </b></h3>
                    </div>
                    <div class="card-body">
                        <div class="row col-xsm-12">

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                <div class="info-box dashobard3-card shadow-lg">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-copy"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Total NV</span>
                                        <span class="info-box-number">
                                            {{ $totalNV ?? ''}}
                                            <small></small>
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>


                            <!-- /.col -->
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                <div class="info-box mb-3 dashobard3-card shadow-lg">
                                    <span class="info-box-icon bg-success elevation-1"><i
                                            class="fas fa-thumbs-up"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Approved NV</span>
                                        <span class="info-box-number">{{ $approvedNV ?? ''}}</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->

                            <!-- fix for small devices only -->
                            <div class="clearfix hidden-md-up"></div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                <div class="info-box mb-3 dashobard3-card shadow-lg">
                                    <span class="info-box-icon bg-danger elevation-1"><i
                                            class="far fa-file-excel"></i></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Rejected NV</span>
                                        <span class="info-box-number">{{ $rejectedNV ?? ''}}</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            {{-- @if(Auth::user()->user_id==11) --}}
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                <div class="info-box mb-3 dashobard3-card shadow-lg">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="far fa-clock"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending NV</span>
                                        <span class="info-box-number">{{ $pendingNV ?? '' }}</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            {{-- @endif --}}
                            <div class="clearfix hidden-md-up"></div>
                            @if(Auth::user()->role_id==0)

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                <div class="info-box mb-3 dashobard3-card shadow-lg">
                                    <span class="info-box-icon bg-primary elevation-1"><i
                                            class="fas fa-undo"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Reverted NV</span>
                                        <span class="info-box-number">{{ $revertedNV ?? '' }}</span>
                                    </div>

                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card retAjax">
                    <div class="card-header" style="background: linear-gradient(to bottom, #ec5f67 40%, #b82d35);">
                        <h3 class="card-title text-white"> <b>Need Validation Request</b> </h3>
                    </div>
                    <div class="card-body">

                        <!-- <div class="row col-xsm-12">
                                                                                                                                                                                            <div class="col-12 col-sm-6 col-md-3">
                                                                                                                                                                                                <div class="info-box mb-3 dashobard3-card shadow-lg">
                                                                                                                                                                                                    <span class="info-box-icon bg-warning elevation-1"><i
                                                                                                                                                                                                            class="fas fa-folder"></i></i></span>

                                                                                                                                                                                                    <div class="info-box-content">
                                                                                                                                                                                                        <span class="info-box-text">Stage Wise NV</span>
                                                                                                                                                                                                        <span class="info-box-number">5</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                              
                                                                                                                                                                                                </div>
                                                                                                                                                                                              
                                                                                                                                                                                            </div> -->
                        <!-- <br> -->
                    </div>
                    <div class="row col-xsm-12">
                        @if(Auth::user()->role_id==2 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-tasks"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">HOD<br> Approval Stage</span>
                                    <span class="info-box-number">
                                        {{ $hodApproval ?? '' }}
                                        <small></small>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                        <!-- /.col -->
                        @if(Auth::user()->role_id==5 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-primary elevation-1"><i
                                        class="fas fa-align-left"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Budget<br> Approval Stage</span>
                                    <span class="info-box-number">{{ $cpmgApproval ?? '' }}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        @endif
                        @if(Auth::user()->role_id==10 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-success elevation-1"><i
                                        class="fas fa-align-left"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">CPMG<br> Approval Stage</span>
                                    <span class="info-box-number">{{ $cesApproval ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                        <!-- fix for small devices only -->
                        <div class="clearfix hidden-md-up"></div>
                        @if(Auth::user()->role_id==6 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-list"></i></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">CEO Nominee1 <br> Approval Stage</span>
                                    <span class="info-box-number">{{ $btApproval ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                        <!-- /.col -->
                        @if(Auth::user()->role_id==7 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-warning elevation-1"><i
                                        class="far fa-list-alt"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">CEO Nominee2 <br> Approval Stage 1</span>
                                    <span class="info-box-number">{{ $ceonomineeApproval ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                        <!-- /.col -->
                        <!-- /.col -->
                        @if(Auth::user()->role_id==8 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-th-list"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">CEO Nominee2 <br> Approval Stage 2</span>
                                    <span class="info-box-number">{{ $ceoApproval ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                        @if(Auth::user()->role_id==8 || Auth::user()->role_id==1)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-secondary elevation-1"><i
                                        class="fas fa-tablet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">CEO <br> Approval Stage </span>
                                    <span class="info-box-number">{{ $ceoApproval ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endif
                    </div>
                </div>


            </div>
        </div>

        @if(Auth::user()->role_id==1)
        <div class="col-12">
            <div class="card retAjax">
                <div class="card-header" style="background: linear-gradient(to bottom, #ec5f67 40%, #b82d35);">
                    <h3 class="card-title text-white"> <b>Need Validation Information By Department</b> </h3>
                </div>
                <div class="card-body">

                    <div class="row col-xsm-12">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-warning elevation-1"><i
                                        class="fas fa-folder"></i></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Department <br> Wise NV</span>
                                    <span class="info-box-number">{{ $dpnv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <br>
                    </div>
                    <div class="row col-xsm-12">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-tasks"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">BPI</span>
                                    <span class="info-box-number">
                                        {{ $dpbpinv ?? ''}}
                                        <small></small>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-primary elevation-1"><i
                                        class="fas fa-align-left"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">CEO Cell</span>
                                    <span class="info-box-number">{{ $dpceocellnv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->

                        <!-- fix for small devices only -->
                        <div class="clearfix hidden-md-up"></div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-list"></i></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Regulatory</span>
                                    <span class="info-box-number">{{ $dpregnv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-warning elevation-1"><i
                                        class="far fa-list-alt"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">O&M</span>
                                    <span class="info-box-number">{{ $dpomnv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-th-list"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Enforcement</span>
                                    <span class="info-box-number">{{ $dpinfonv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-table"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Safety</span>
                                    <span class="info-box-number">{{ $dpsafenv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-secondary elevation-1"><i
                                        class="fas fa-tablet"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">DSM & PAT</span>
                                    <span class="info-box-number">{{ $dpdsmnv ?? ''}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3 dashobard3-card shadow-lg">
                                <span class="info-box-icon bg-primary elevation-1"><i
                                        class="fab fa-stack-exchange"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">BET</span>
                                    <span class="info-box-number">{{ $dpbetnv ?? '' }}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                    </div>
                    {{-- code here --}}

                </div>


            </div>
        </div>
        @endif
    </div>
    {{-- Table --}}
    <table id="approve_list" class="table bg-light shadow-lg table-bordered table-responsive">
        <thead>
            <tr>
                <th class="text-center">Sr. No</th>
                <th class="text-center">Proposal Number</th>
                <th class="text-center">Initiated By</th>
                <th class="text-center">Initiated Date</th>
                <th class="text-center">NV Type</th>
                <th class="text-center">Dept Reviewer1</th>
                <th class="text-center">NV-Status? (Reviewer1)</th>
                <th class="text-center">Dept Reviewer2</th>
                <th class="text-center">NV-Status? (Reviewer2)</th>
                <th class="text-center">Dept Reviewer3</th>
                <th class="text-center">NV-Status? (Reviewer3)</th>
                <th class="text-center">Dept Reviewer4</th>
                <th class="text-center">NV-Status? (Reviewer4)</th>
                <th class="text-center">Dept HOD</th>
                <th class="text-center">NV-Status? (HOD)</th>

                <th class="text-center">Budget Capex</th>
                <th class="text-center">NV-Status? (Budget Capex)</th>
                <th class="text-center">Budget Opex</th>
                <th class="text-center">NV-Status? (Budget Opex)</th>


                <th class="text-center">CPMG</th>
                <th class="text-center">NV-Status? (CPMG) </th>

                <th class="text-center">CEO Nominee1</th>
                <th class="text-center">NV-Status? (CEO Nominee1) </th>

                <th class="text-center">CEO Nominee2</th>
                <th class="text-center">NV-Status? (CEO Nominee2)</th>

                <th class="text-center">CEO</th>
                <th class="text-center">NV-Status? (CEO) </th>
                <!-- <th class="text-center">Action</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
                $no = 101;
                
                ?>
            @if (!empty($nv_sm_data) && count($nv_sm_data) >= 1)
            @foreach ($nv_sm_data as $nv_list)
            @php

            $proposal_no = getProposalNumber($nv_list->nv_id);
            $status_obj = getAllStatus($nv_list->nv_id);

            $inc_date = new DateTime($nv_list->created_at);

            $hod_app = new DateTime($nv_list->hod_timestamp);
            $interval = $hod_app->diff($inc_date);
            $days = $interval->days + 1;

            $cpmg_app = new DateTime($nv_list->cpmg_timestamp);
            $cpmginterval = $cpmg_app->diff($hod_app);
            $cpmgdays = $cpmginterval->days + 1;

            $ces_app = new DateTime($nv_list->ces_timestamp);
            $cesinterval = $ces_app->diff($cpmg_app);
            $cesdays = $cesinterval->days + 1;

            $bt_app = new DateTime($nv_list->bt_timestamp);
            $btinterval = $bt_app->diff($ces_app);
            $btdays = $btinterval->days + 1;

            $ceon_app = new DateTime($nv_list->ceo_nominee_timestamp);
            $ceoninterval = $ceon_app->diff($bt_app);
            $ceondays = $ceoninterval->days + 1;

            $ceo_app = new DateTime($nv_list->ceo_timestamp);
            $ceointerval = $ceo_app->diff($ceon_app);
            $ceodays = $ceointerval->days + 1;

            $value = null;

            $nv_id[] = $nv_list->nv_id;
            //print_r($nv_id);
            $result = [];

            foreach ($nv_id as $key => $value) {
            $count = count(array_keys($nv_id, $value)); // Count the occurrences of the value


            if ($count > 1) {
            for ($i = 0; $i <= $count; $i++) { $result=$value . "-v" . $i; } } else { $result=$value; } } @endphp <tr>
                @php $service= getServiceName($nv_list->nv_id);@endphp
                <?php
                            $url = '/admin/nv_' . lcfirst($service) . '/create/' . $nv_list->nv_id;
                            ?>
                <td>{{ $i++ }}</td>

                <td>
                    <a href="{{$url}}">
                        NV/{{ $proposal_no->budget_type }}/FY
                        {{ $proposal_no->fiscal_year }}/
                        @if ($nv_list->service_id == null)
                        {{ getDepartmentName($nv_list->material->dept_id) }}
                        @else
                        {{ getDepartmentName($nv_list->service->dept_id) }}
                        @endif
                        /{{ getServiceName($nv_list->nv_id) }} / {{ $result }}
                    </a>

                </td>

                <?php

// echo $nv_list;
?>

                @if ($nv_list->service_id == null)
                <td> {{ getUserName($nv_list->material->user_id) }}</td>
                @else
                <td> {{ getUserName($nv_list->service->user_id) }}</td>
                @endif

                <td>{{ date('d-M-y', strtotime($nv_list->created_at)) }} <br>
                    {{ date('H:i', strtotime($nv_list->created_at)) }}</td>

                <td> {{ getServiceName($nv_list->nv_id) }}</td>



                @if ($nv_list->service_id == null)
                <td>{{ getUserName($nv_list->material->user_id ?? '') }}</td>

                @else
                <td>{{ getUserName($nv_list->service->user_id ?? '') }}</td>
                @endif
                {{-- <td></td> --}}
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @if ($nv_list->hod_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->hod_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}
                    <br> {{ date('H:i', strtotime($nv_list->hod_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->hod_status == 1)
                <td style="color: green;"><b>Approved</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }} <br>
                        {{ date('H:i', strtotime($nv_list->hod_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->hod_action_ip }}
                    Days:{{ $days }}
                </td>
                @elseif($nv_list->hod_status == 2)
                <td style="color: red;"><b>Rejected</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }} <br>
                        {{ date('H:i', strtotime($nv_list->hod_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->hod_action_ip }}
                    Days:{{ $days }}
                </td>
                @endif

                @if ($nv_list->service_id == null)
                <td>

                </td>
                @else
                <td>
                </td>
                @endif
                @if ($nv_list->cpmg_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->cpmg_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->cpmg_timestamp)) }}
                    <br> {{ date('H:i', strtotime($nv_list->cpmg_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->cpmg_status == 1)
                <td style="color: green;"><b>Approved</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->cpmg_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->cpmg_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->cpmg_action_ip }}
                    Days:{{ $cpmgdays }}
                </td>
                @elseif($nv_list->cpmg_status == 2)
                <td style="color: red;"><b>Rejected</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->cpmg_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->cpmg_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->cpmg_action_ip }}
                    Days:{{$cpmgdays}}
                </td>
                @endif
                @if ($nv_list->service_id == null)
                <td></td>
                @else
                <td></td>
                @endif
                @if ($nv_list->ces_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->ces_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->ces_timestamp)) }}
                    <br> {{ date('H:i', strtotime($nv_list->ces_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->ces_status == 1)
                <td style="color: green;"><b>Approved</b><br> <i>{{ date('d-M-y', strtotime($nv_list->ces_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->ces_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->ces_action_ip }}
                    Days:{{$cesdays}}
                </td>
                @elseif($nv_list->ces_status == 2)
                <td style="color: red;"><b>Rejected</b><br> <i>{{ date('d-M-y', strtotime($nv_list->ces_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->ces_timestamp)) }} </i> <br>
                    IP:{{ $nv_list->ces_action_ip }}
                    Days:{{$cesdays}}
                </td>
                @endif
                @if ($nv_list->service_id == null)
                <td>
                </td>
                @else
                <td>
                </td>
                @endif
                @if ($nv_list->bt_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->bt_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->bt_timestamp)) }}
                    <br> {{ date('H:i', strtotime($nv_list->bt_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->bt_status == 1)
                <td style="color: green;"><b>Approved</b><br> <i>{{ date('d-M-y', strtotime($nv_list->bt_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->bt_timestamp)) }}</i> <br> IP:{{ $nv_list->bt_action_ip
                    }}
                    Days:{{$btdays}}
                </td>
                @elseif($nv_list->bt_status == 2)
                <td style="color: red;"><b>Rejected</b><br> <i>{{ date('d-M-y', strtotime($nv_list->bt_timestamp)) }}
                        <br>
                        {{ date('H:i', strtotime($nv_list->bt_timestamp)) }}</i> <br> IP:{{ $nv_list->bt_action_ip }}
                    Days:{{$btdays}}
                </td>
                @endif
                @if ($nv_list->service_id == null)
                <td>
                </td>
                @else
                <td>
                </td>
                @endif
                @if ($nv_list->ceo_nominee_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->ceo_nominee_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp)) }}
                    <br> {{ date('H:i', strtotime($nv_list->ceo_nominee_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->ceo_nominee_status == 1)
                <td style="color: green;"><b>Approved</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp)) }} <br>
                        {{ date('H:i', strtotime($nv_list->ceo_nominee_timestamp)) }}</i><br>
                    IP:{{ $nv_list->ceo_nomnee_action_ip }}
                    Days:{{$ceondays}}
                </td>
                @elseif($nv_list->ceo_nominee_status == 2)
                <td style="color: red;"><b>Rejected</b><br>
                    <i>{{ date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp)) }} <br>
                        {{ date('H:i', strtotime($nv_list->ceo_nominee_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->ceo_nomnee_action_ip }}
                    Days:{{$ceondays}}
                </td>
                @endif
                @if ($nv_list->service_id == null)
                <td>
                </td>
                @else
                <td>
                </td>
                @endif
                @if ($nv_list->ceo_status == 0)
                <td><b> </b><br>
                    @if (!empty($nv_list->ceo_timestamp))
                    {{ date('d-M-y', strtotime($nv_list->ceo_timestamp)) }}
                    <br>{{ date('H:i', strtotime($nv_list->ceo_timestamp)) }}
                </td>
                @endif
                @elseif($nv_list->ceo_status == 1)
                <td style="color: green;"><b>Approved</b><br> <i>{{ date('d-M-y ', strtotime($nv_list->ceo_timestamp))
                        }}
                        <br> {{ date('H:i', strtotime($nv_list->ceo_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->ceo_action_ip }}
                    Days:{{$ceodays}}
                </td>
                @elseif($nv_list->ceo_status == 2)
                <td style="color: red;"><b>Rejected</b><br> <i>{{ date('d-M-y', strtotime($nv_list->ceo_timestamp)) }}
                        <br> {{ date('H:i', strtotime($nv_list->ceo_timestamp)) }}</i> <br>
                    IP:{{ $nv_list->ceo_action_ip }}
                    Days:{{$ceodays}}
                </td>
                @endif


                <td></td>
                <!-- <td style="color: green;"><a  class="btn btn-info" href="#">Approve</a></br></br><a class="btn btn-danger">Reject</a></td> -->

                </tr>
                @endforeach

                @endif


        </tbody>
    </table>
    </div>


</section>




</div>
@endsection
@push('script')
<script>
        // $(document).ready(function () {
        //   if($(document).find('#approve_list').length > 0){
        //  $('#approve_list').DataTable({
        //   responsive: false,
        // 		searching: true,
        // 			lengthChange: false,
        // 			dom: 'Bfrtip',


        //  });
        // }
        //  });
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/js/brand.js') }}"></script>
<!-- <script src="{{ asset('admin/js/employee.js') }}"></script> -->
<script></script>
@endpush