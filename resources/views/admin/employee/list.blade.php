@extends('admin.layout.master', ['page_title' => 'Employee'])
@push('styles')
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css"  href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css"  href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" />
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">


        

        <h1 class="m-0">Manage Employees</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">Employees</li>
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
              <h3 class="card-label">Employees</h3>
            </div>
            @if (\Auth::user()->isA('Admin'))
            <div class="card-toolbar">
            <a href="javascript:void" class="btn btn-primary font-weight-bolder" id="export_btn">
              <i class="fas fa-file-download mr-1"></i> Export</a>
            <a href="/admin/employees/download-excel" class="btn btn-primary font-weight-bolder" target="_blank" id="export_excel">
            Excel</a>
              <a href="/admin/employees/download-pdf" class="btn btn-primary font-weight-bolder" target="_blank" id="export_pdf">
               PDF</a>
              <a href="/admin/employees/create" class="btn btn-primary font-weight-bolder">
                <i class="fa fa-plus-circle mr-1"></i>
                Add Employee</a>
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
                  <th>Sub-Department</th>
                  <th>Designation</th>
                  <th width="18%">Employee Id</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>LastLogin</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {

        $("#export_pdf").hide();

        $("#export_excel").hide();

        $("#export_btn").click(function() {

            $("#export_pdf").toggle();

            $("#export_excel").toggle();

        });
    });
</script>

@endpush