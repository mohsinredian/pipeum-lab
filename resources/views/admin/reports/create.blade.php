@extends('admin.layout.master', ['page_title' => 'Create Complaint'])


@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Complaint</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Complaints</li>
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
            
            <form class="form" id="create_complaint_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Complaint Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            
                            <div class="col-lg-4">
                                <label>Department<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="department" id="department">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id}}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text department_error"></div>
                            </div>
                              <div class="col-lg-4">
                                <label>Circle<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="circle" id="circle">
                                    <option value="">Select Circle</option>
                                    @foreach ($circles as $circle)
                                        <option value="{{ $circle->id}}">{{ $circle->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text circle_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Location<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="location" id="location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id}}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text location_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Asset<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="asset" id="asset">
                                    <option value="">Select Asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id}}">{{ $asset->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text asset_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Model Number<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="model" name="model" placeholder="Model">
                                <div class="common-error form-text model_id_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Serial Number<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number">
                                <div class="common-error form-text serial_number_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Vendor<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="vendor" id="vendor">
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id}}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text vendor_error"></div>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>SLA (In Hrs.)<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="sla" name="sla" placeholder="SLA (In Hrs.)">
                                <div class="common-error form-text sla_error"></div>
                            </div>
                             <div class="col-lg-4">
                                <label>Complaint Type<span class="mandatory_input">*</span></label>
                                <select class="form-control" id="complaint_type" name="complaint_type">
                                    <option value="">Select Complaint Type</option>
                                    <option value="Item Faulty">Item Faulty</option>
                                </select>
                                <div class="common-error form-text complaint_type_error"></div>
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
                              <button type="submit" class="btn btn-success mr-2">Submit</button>
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
@endsection

@push('script')

<script src="{{asset('admin/js/complaint.js')}}"></script>

@endpush