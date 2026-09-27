@extends('admin.layout.master', ['page_title' => 'Services'])
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
            <h1 class="m-0">Manage Services</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Services</li>
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
                    <h3 class="card-label">Services</h3>
                </div>
                @if (\Auth::user()->isA('Admin'))
                <div class="card-toolbar">
                <a href="/admin/service/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                <i class="fas fa-file-download mr-1"></i> Excel</a>
                    <a href="/admin/service/create" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add Service</a>
                </div>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="department_datatable" class="table table-bordered">
                  <thead class="text-center">
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>id</th>
                    <th class="text-left">Name</th>
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
<script src="{{asset('admin/js/services.js')}}"></script>

@endpush