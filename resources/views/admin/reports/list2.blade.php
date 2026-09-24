@extends('admin.layout.master', ['page_title' => 'Reports'])
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
            <h1 class="m-0">Manage Reports</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
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
                    <h3 class="card-label">Reports</h3>
                </div>
                {{-- @if (\Auth::user()->isA('Admin'))
                <div class="card-toolbar">
                    <a href="/admin/complaints/create" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add Complaint</a>
                </div>
                @endif --}}
              </div>
              <!-- /.card-header -->
              {{-- {{dd($brandwisereport)}} --}}
              <div class="card-body table-responsive">
                {{-- <table id="complaint_datatable" class="table table-bordered"> --}}
                  <table  class="table table-bordered">

                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Brand Name</th>
                    <th>Asset Name</th>
                    <th>Total Stock</th>
                    <th>Total Issue</th>
                    <th>Avaiable Balance</th>
                    {{-- <th>Asset</th>
                    <th>Model Number</th>
                    <th>Item Serial Number</th>
                    <th>Vendor</th>
                    <th>SLA</th>
                    <th>Complaint Type</th>
                    <th>Status</th>
                    <th>Action</th> --}}
                  </tr>
                  </thead>
                  <tbody>
                   <?php
                    $i=1;
                   
                    ?>
                    @foreach ($inventories as $inventory)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$inventory->brandname}}</td>
                      <td>{{$inventory->assetsname}}</td>
                      <td>{{$inventory->item_qty}}</td>
                      <td>
                      @foreach ($inventories2 as $inventory2)
                      @if($inventory2->brandname==$inventory->brandname && $inventory2->assetsname==$inventory->assetsname)
                         {{$inventory2->issuecount}}                      

                        @endif
                         @endforeach
                      </td>
                      <td>
                        @foreach ($inventories2 as $inventory2)
                        @if($inventory2->brandname==$inventory->brandname && $inventory2->assetsname==$inventory->assetsname)
                           {{$inventory->item_qty-$inventory2->issuecount}}
                           
                          @endif
                           @endforeach
                      </td>
                    </tr>
                      <?php $i++;?>
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
<script src="{{asset('admin/js/reports.js')}}"></script>

@endpush