@extends('admin.layout.master', ['page_title' => 'Edit Employee'])

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
            
            <form class="form" id="edit_employee_form">
                @csrf
                <input type="hidden" name="employee_id" value="{{$employee['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Employee Details</h3>
                    <div class="card-toolbar">
                                <button type="reset" class="btn btn-primary " onclick="window.history.go(-1);">
                                   Back << </button>

                            </div>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">

                            <div class="col-lg-4">
                                <label>Employee Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Employee Name" value="{{$employee['name']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>
                             <div class="col-lg-4">
                                <label>Employee Email <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Employee Email" value="{{$employee['email']}}">
                                <div class="common-error form-text email_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Employee Mobile No <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Employee Mobile No " value="{{$employee['phone']}}">
                                <div class="common-error form-text mobile_error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Company<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="division">
                                    <option value="">Select Company </option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}" @if ($division->id == $employee['division_id']) selected="selected" @endif>{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text division_error"></div>
                            </div>
                            <div class="col-lg-4">
                              <label>Roles<span class="mandatory_input">*</span></label>
                              <select class="form-control " name="role">
                              <option value="">Select Role </option>
                                    @foreach ($role as $roles)
                                        <option value="{{ $roles->id }}" @if ($roles->id == $employee['role_id']) selected="selected" @endif>{{ $roles->title}}</option>
                                    @endforeach
                              </select>
                          </div>
                     
                            <div class="col-lg-4">
                                <label>Location<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="location">
                                    <option value="">Select location </option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @if ($location->id == $employee['location_id']) selected="selected" @endif>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text location_error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Employee Id<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="emp_id" name="emp_id" placeholder="Employee Id" value="{{$employee['employee_id']}}">
                                <div class="common-error form-text emp_id_error"></div>
                            </div>

                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $employee["status"] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $employee["status"] == 0 ? 'selected' : '' }}>Inactive</option>
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

<script src="{{asset('admin/js/employee.js')}}"></script>

@endpush