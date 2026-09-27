@extends('admin.layout.master', ['page_title' => 'Delete List'])
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
                <h1 class="m-0">Delete Need Validation List</h1>
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
                            <h4 class="card-label">Need Validation List</h4>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                



    <table id="approve_list" class="table shadow-lg table-responsive dashboard-table">
        <thead class="text-center">
            <tr>
                <th class="text-center" width="5%">S.No</th>
                <th>Proposal Number</th>
                <th>Budget Type</th>
                <th>Initiated By</th>
                <th width="15%">Initiated Date</th>
                <th width="10%">DOP Ref No</th>
                <th width="15%">Budgetary Provision</th>
                <th width="15%">Proposal Type</th>
                <th width="18%">NV Type</th>
                <th width="15%">Fiscal Year</th>
                
            </tr>
        </thead>
        <tbody>
         
            @foreach($latest as $key =>$value)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{'NV' . '/' . $value->budget_type . '/' . $value->fiscal_year . '/' . getDepartmentNameByPro($value->user_id) . '/' . $value->service->name . '/' . $value->id}}</td>
                    <td>{{$value->budget_type}}</td>
                    <td>{{$value->user->name}}</td>
                    <td>{{$value->created_at->format('d-m-Y')}}</td>
                    
                        @if ($value->service_id == 1)
                        <td>{{$value->material->dop ?? ''}}</td>
                        @else 
                            <td>{{$value->services->dop_ref_no ?? ''}}</td>
                        @endif
                    <td>{{$value->budgetary_provision}}</td>
                    <td>{{$value->proposal_type}}</td>
                    <td>{{$value->service->name;}}</td>
                    <td>{{$value->fiscal_year}}</td>
                </tr>
            @endforeach
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
    $(document).ready(function () {
        if ($(document).find('#approve_list').length > 0) {
            $('#approve_list').DataTable({
                responsive: false,
                searching: true,
                lengthChange: false,
                dom: 'Bfrtip',


            });
        }
    });
</script>

<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

@endpush