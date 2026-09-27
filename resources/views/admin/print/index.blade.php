@extends('admin.layout.master', ['page_title' => 'View NV Material'])
@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"> NV-Material</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active">View NV-Material</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

<section class="content">
      <div class="container-fluid">
          <div class="row">
        <div class="col-12">
          <div class="card  card-info shadow-lg printout show-table-data-nv-material">
            <div class="card-header bg-transparent">
               
              <div class="card-title">
                  <h3 class="card-label">NV-Material Details</h3>
              </div>
            
              <div class="card-toolbar">
                <button type="reset" class="btn btn-primary " onclick="history.back();">Back »</button>
                     
              </div>
           
            </div>
            <!-- /.card-header -->
            <div class="card-body rounded-0">
                <div class="container-fluid bg-3 text-left">
                    <div class="row p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Department Name</p>
                            <spam>HOD</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">DOP Refrence Number</p>
                            <spam>506</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Proposal Name</p>
                            <spam>XYZ</spam>
                        </div>
        
                    </div>
        
                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Background</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Justification Of Proposal</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Services</p>
                            <spam>....</spam>
                        </div>
        
                    </div>
        
                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Benefit</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Implementation Period To</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Implementation Period From</p>
                            <spam>....</spam>
                        </div>
        
                    </div>
        
                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Implementation Plan Year Wise</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Type Of Proposal</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Nature Of Work</p>
                            <spam>....</spam>
                        </div>
        
                    </div>
        
                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Scheme No</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Scheme Description</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Scheme Type / Category</p>
                            <spam>....</spam>
                        </div>
        
                    </div>
        
                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">DERC Refrence No</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">DERC Approval Status </p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">DERC Approval Date</p>
                            <spam>....</spam>
                        </div>
        
                    </div>

                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Material Code</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Material Description</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Material Justification</p>
                            <spam>....</spam>
                        </div>
        
                    </div>

                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Material Group</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">UoM</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Rate</p>
                            <spam>....</spam>
                        </div>
        
                    </div>

                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Quantity Required</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Total Amount</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Delivery Schdule</p>
                            <spam>....</spam>
                        </div>
        
                    </div>

                    <div class="row mt-2 p-2">
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">Budget Available</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ">
                            <p class="font-weight-bolder">UoM</p>
                            <spam>....</spam>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <p class="font-weight-bolder">Rate</p>
                            <spam>....</spam>
                        </div>
        
                    </div>
                     
        
                    </div>
        
                </div>
        
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->

        <!-- <button  onclick="myFunction()">click</button>
        <div class="box" style="display:block;border:2px solid red "  >
       </div> -->
      </div>
      </div><!-- /.container-fluid -->

   
@endsection
@push('script')
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

<script src="{{asset('admin/js/nv.js')}}"></script>

@endpush