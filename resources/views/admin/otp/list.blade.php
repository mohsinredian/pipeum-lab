@extends('admin.layout.master', ['page_title' => 'Employee'])
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


        

        <h1 class="m-0">Manage User OTP</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">User OTP</li>
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
              <h3 class="card-label">User OTP</h3>
            </div>
           
           
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive">
            <table id="user_otp_datatable" class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center" width="5%">S.No</th>
                  <th>Name</th>
                  <th>Otp</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php $i = 1; @endphp
                @foreach($data as $key =>$value)
                
                <tr>
                    <td>{{$i}}</td>
                    <td>{{getUserName($value->user_id)}}</td>
                    <td>{{$value->otp}}</td>
                    <td class='text-center'><a href='{{ url('admin/otp_edit/'.$i) }}'><i class='fas fa-edit text-info'></i></a></td>
                        
                </tr>
              
               
                @php
                $i++;
                @endphp
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
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
{{-- <script src="{{asset('admin/js/employee.js')}}"></script> --}}
<script>
    $(document).ready(function() {
        if ($(document).find('#user_otp_datatable').length > 0) {
            $('#user_otp_datatable').DataTable({
                responsive: false,
                searching: true,
                lengthChange: false,
                dom: 'Bfrtip',


            });
        }
    });
</script>
@endpush