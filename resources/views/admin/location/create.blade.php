@extends('admin.layout.master', ['page_title' => 'Create Location'])


@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Location</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Location</li>
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
            
            <form class="form" method="POST" id="create_location_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Location Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-4">
                            
                                <label>Company Name<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="company_id">
                                    <option value="">Select Company </option>
                                    @foreach ($division as $divisions)
                                        <option value="{{ $divisions->id }}" >{{ $divisions->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text company_id_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Location Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Location Name">
                                <div class="common-error form-text name_error"></div>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Select Status</option>
                                   <option value="1">Active</option>
                                   <option value="0">Inactive</option>
                                </select>
                                <div class="common-error form-text status_error"></div>
                           </div>
                        </div>
                    </div>
                  <!--end::Form-->
                  <!-- /.card-body -->
                </div>
                
                <div class="card">
                        
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success">Submit</button>
                              <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /.card -->
            </form>
          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>

<!-- Content Header (Page header) -->
    <!-- /.content-header -->

    <!-- Main content -->
    <!-- <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Locations</h3>
                </div>
                @if (\Auth::user()->isA('Admin'))
                {{-- <div class="card-toolbar">
                    <a href="/admin/locations/create" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add Location</a>
                </div> --}}
                @endif
              </div>
              
              <div class="card-body table-responsive">
                <table id="location_datatable" class="table table-bordered">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>id</th>
                    <th>Location Name</th>
                    <th class="text-center">Company Name</th>
                    <th class="text-center">Status</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                </table>
              </div>
             
            </div>
           
          </div>
         
        </div>
        </div>
    </section> -->
@endsection

@push('script')
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/location.js')}}"></script>
@endpush