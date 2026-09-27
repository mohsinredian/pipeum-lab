@extends('admin.layout.master', ['page_title' => 'Provisional Balance Sheet'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
       button.dt-button, div.dt-button, a.dt-button, input.dt-button {
            background-color:rgb(3, 142, 220) !important;
            color: white !important;
           }
           .pagination {
            float:right;
           }
    </style>
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content">
        <div class="container-fluid">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manage Provisional Balance Sheet</h1>
                        </div><!-- /.col -->

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Provisional Balance Sheet</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
          

    </section><br>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h5 class="card-label mt-2" style="color:rgb(3, 142, 220)  !important;">List of Provisional Budget</h5>
                            </div>
                          
                        </div>
                       <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="provision_datatable1" class="table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-center ">S.No</th>
                                        <!-- <th class="text-center ">Id</th> -->
                                        <th class="list-nv">Proposal Number</th>
                                        <th class="text-center">Department</th>
                                        <th class="list-nv">NV Type</th>
                                        <th class="list-nv">Fiscal Year</th>
                                        <th class="list-nv">Budget Type</th>
                                        <th class="list-nv">Budgetary Provision</th>
                                        <th class="list-nv">Initiated By</th>
                                        <th class="list-nv">Initiated Date</th>
                                        <th class="list-nv" style="width:250px;"> Provisional Budget </th>
                                      </tr>
                                </thead>
                                <tbody>
                                @foreach ($nvs as $key => $nv)
                                @php
                                $Impyear = explode('-',$nv->fiscal_year);
                                $nextStartYear1 = $Impyear[0] + 1;
                                $nextEndYear1 = $Impyear[1] + 1;
                                $nextStartYear2 = $Impyear[0] + 2;
                                $nextEndYear2 = $Impyear[1] + 2;
                                $nextYear1 = "{$nextStartYear1}-{$nextEndYear1}";
                                $nextYear2 = "{$nextStartYear2}-{$nextEndYear2}";
                                @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{'NV' . '/' . $nv->budget_type . '/' . $nv->fiscal_year . '/' . getDepartmentNameByPro($nv->user_id) . '/' . $nv->service->name . '/' . $nv->id}}</td>
                                        <td>{{getDepartmentName($nv->department_id) ?? ''}}</td>
                                        <td>{{$nv->service->name}}</td>
                                        <td>{{$nv->fiscal_year}}</td>
                                        <td>{{$nv->budget_type}}</td>
                                        <td>{{$nv->budgetary_provision}}</td>
                                        <td>{{$nv->user->name}}</td>
                                        <td>{{date("d-M-y h:i A", strtotime($nv->created_at)) ?? ''}}</td>
                                        <td >
                                            <div class="row" style="padding:0px;margin:0px;">
                                              @if($nv->service->name == "Material")
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                    {{$nv->fiscal_year}}</h6>{{$nv->material->total_mat_mat ?? 0}}</div>
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                {{$nextYear1}}</h6>{{$nv->material->total_mat_mat2 ?? 0}}</div>
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                {{$nextYear2}}</h6>{{$nv->material->total_mat_mat3 ?? 0}}</div>
                                                @else
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                {{$nv->fiscal_year}}</h6>{{$nv->services->total_mat_mat ?? 0}}</div>
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                {{$nextYear1}}</h6>{{$nv->services->total_mat_mat2 ?? 0}}</div>
                                                <div class="col-4" style="padding:0px;margin:0px;"> <h6 style="font-size:12px; padding:1px;margin:1px;background-color:rgb(3, 142, 220);color:white; ">
                                                {{$nextYear2}}</h6>{{$nv->services->total_mat_mat3 ?? 0}}</div>
                                                @endif
                                            </div>
                                        </td>
                                     
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table><br>
                            {{ $nvs->links() }}
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
    <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

{{-- For Datatable buttons cdn --}}

    <script src="{{ asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/vfs_fonts.js') }}"></script>

    <script src="{{ asset('admin/js/capex.js') }}"></script>

@endpush