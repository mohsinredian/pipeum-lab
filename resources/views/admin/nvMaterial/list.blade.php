@extends('admin.layout.master', ['page_title' => 'NV Material'])
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
        <h1 class="m-0">Manage NV Material</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">NV Material</li>
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
              <h3 class="card-label">NV Material</h3>
            </div>
            @if (\Auth::user()->isA('Admin'))
            <div class="card-toolbar">
              <a href="/admin/nv_material/create" class="btn btn-primary font-weight-bolder">
                <i class="fa fa-plus-circle mr-1"></i>
                Add NV Material</a>
            </div>
            @endif
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive">
            <table id="employee_datatable" class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center" width="5%">S.No</th>
                  <th>id</th>
                  <th>Name</th>
                  <th>Email Id</th>
                  <th width="18%">Mobile No</th>
                  <th>Company</th>
                  <th>Location</th>
                  <th width="18%">Employee Id</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
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
<script src="{{asset('admin/js/employee.js')}}"></script>

@endpush