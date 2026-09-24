@extends('admin.layout.master', ['page_title' => 'Edit Employee'])
@push('style')
<link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
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
            
            <form class="form" id="edit_employee_form">
                @csrf
                <input type="hidden" name="emp_id" value="{{$employee['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit Employee Details</h3>
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
                                <input type="text" class="form-control" id="mobile" name="phone" placeholder="Employee Mobile No " value="{{$employee['phone']}}">
                                <div class="common-error form-text phone_error"></div> 
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
                              <label>Role<span class="mandatory_input">*</span></label>
                              <select class="form-control " name="role_id" id="role">
                              <option value="">Select Role </option>
                                    @foreach ($role as $roles)
                                        <option value="{{ $roles->id }}" @if ($roles->id == $employee['role_id']) selected="selected" @endif>{{ $roles->title}}</option>
                                    @endforeach
                              </select>
                          </div>


                        <div class="col-lg-4">
                              <label> Department</label>
                              @php
                                $selectedSupDepartmentsArray = is_array($selectedSuperDepartments) ? $selectedSuperDepartments : [$selectedSuperDepartments];
                                @endphp
                              <select class="form-control select2_element departments_select2_dropdown" id="super_department" name="super_department[]" multiple>
                                  <option value="">Select  Department</option>
                                  @foreach($supdepts as $supdept)
                                          <option value="{{ $supdept->id }}" @if (in_array($supdept->id, $selectedSupDepartmentsArray)) selected="selected" @endif>{{ $supdept->name }}</option>
                                      @endforeach
                              </select>
                              <div class="common-error form-text status_error"></div>
                         </div>
                
                      <div class="col-lg-4 mt-2">
                            <label>Sub-Department <span class="mandatory_input">*</span></label>
                            @php
                                $selectedDepartmentsArray = is_array($selectedDepartments) ? $selectedDepartments : [$selectedDepartments];
                                $departments_map = is_array($map_dept) ? $map_dept : [$map_dept];
                                
                                @endphp
                             
                            <select class="form-control select2_element departments_select2_dropdown" id="departments" name="department_id[]" multiple>
                                
                                @foreach($map_dept as $department)
                                    <option value="{{ $department->id }}" @if(in_array($department->id, $selectedDepartmentsArray)) selected="selected" @endif>{{ $department->name }}</option>
                                @endforeach
                                
                            </select>
                            
      
                          <div class="common-error form-text status_error"></div>
                      </div>

                            <div class="col-lg-4 mt-2">
                                <label>Employee Id<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="emp_id" name="employee_id" placeholder="Employee Id" value="{{$employee['employee_id']}}">
                                <div class="common-error form-text emp_id_error"></div>
                            </div>

                            <div class="col-lg-4 mt-2">
                              <label>Change Password</label>
                              <input type="password" class="form-control" id="password" name="password" placeholder="Employee Password" value="">
                              <div class="input-group-append" style="position: absolute; top:31px; right:7px;">
                                  <span class="input-group-text toggle-password" id="toggle-password" onclick="togglePasswordVisibility()" style="padding: 10px;cursor: pointer;">
                                      <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                  </span>
                              </div>
                             </div>
                           
                           
                             

                             <div class="col-lg-4 mt-2">
                              <label> Designation<span class="mandatory_input">*</span></label>
                              <select class="form-control" id="designation" name="designation" onchange="handleDesignationChange(this)">
                                  <option value="">Select Designation</option>
                                  @foreach($designation as $designation)
                                          <option value="{{ $designation->id }}" @if ($designation->id == $employee['designation']) selected="selected" @endif>{{ $designation->name }}</option>
                                      @endforeach
                                      <option value="other">Other</option>
                              </select>
                              <div id="customDesignationInput" class="mt-2" style="display: none;">
                            <label for="customDesignation">Enter Custom Designation</label>
                            <input type="text" class="form-control" id="customDesignation" name="customDesignation">
                        </div>

                        <div class="common-error form-text designation_error"></div>
                         </div>

                         <div class="col-lg-4 mt-2">
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
                              <button type="submit" class="btn btn-success ">Submit</button>
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
<script src="{{asset('theme/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('admin/js/employee.js')}}"></script>
<script>
$(document).ready(function () {
        $('.departments_select2_dropdown').select2({
            tags: true, // Enable tagging
            tokenSeparators: [',', ' '], // Define token separators (comma and space)
            createTag: function(params) {
            return null; // By default, disallow creating new tags
        }
        });
    });
</script>


<script>
  function togglePasswordVisibility() {
      var passwordInput = document.getElementById("password");
      var toggleIcon = document.getElementById("toggle-password");

      if (passwordInput.type === "password") {
          passwordInput.type = "text";
          toggleIcon.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
      } else {
          passwordInput.type = "password";
          toggleIcon.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
      }
  }
</script>
<script>
    function handleDesignationChange(select) {
        var customInput = document.getElementById('customDesignationInput');
        var customInputValue = document.getElementById('customDesignation');

        if (select.value === 'other') {
            customInput.style.display = 'block';
            customInputValue.setAttribute('required', 'required');
        } else {
            customInput.style.display = 'none';
            customInputValue.removeAttribute('required');
        }
    }
</script>
<script>
    $(document).ready(function () {
        $('#departments').change(function () {
            if ($(this).val() !== null && $(this).val().length > 0) {
                // Show the mapping dropdown
                $('#mapping_departments').show();
            } else {
                // Hide the mapping dropdown if no department is selected
                $('#mapping_departments').hide();
            }
        });
    });
</script>
<script>
    var roleSelect = document.getElementById('role');
    var departmentSelect = document.getElementById('departments');
    roleSelect.addEventListener('change', function() {
        var selectedRole = this.value;
        if (selectedRole == 9) {
            departmentSelect.removeAttribute('multiple');
        } else {
            departmentSelect.setAttribute('multiple', 'multiple');
        }
    });

    $('#password').on('keyup', function() {
        if ($(this).val().trim() !== '') {
            // If password field is not empty, add password validation
            $(this).rules('add', {
                passwordValidation: true
            });
        } else {
            // If password field is empty, remove password validation
            $(this).rules('remove', 'passwordValidation');
        }
    });

    $(document).ready(function() {
    var roleSelect = $('#role');
    var departmentSelect = $('#departments');
    var super_departmentSelect = $('#super_department');
    var initialRole = '{{ $employee["role_id"] }}';
    toggleDepartmentSelect(initialRole);
    roleSelect.on('change', function() {
        var selectedRole = $(this).val();
        toggleDepartmentSelect(selectedRole);
    });
    function toggleDepartmentSelect(roleId) {
        // if (roleId == 9) {
        //     departmentSelect.prop('multiple', false);
        //     departmentSelect.removeAttr('multiple'); 
        //     super_departmentSelect.prop('multiple', false);
        //     super_departmentSelect.removeAttr('multiple');   
        // } else {
            departmentSelect.prop('multiple', true); 
            super_departmentSelect.prop('multiple', true); 
        // }
       var abc= departmentSelect.select2('destroy').select2();
       var abc1= super_departmentSelect.select2('destroy').select2();
    }
});
</script>

@endpush