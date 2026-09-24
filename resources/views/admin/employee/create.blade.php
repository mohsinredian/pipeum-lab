@extends('admin.layout.master', ['page_title' => 'Create Employee'])


@section('content')
@push('style')



<style>
  .password-input-container {
      position: relative;
  }

  .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
  }
</style>

<link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Employee</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Employee</li>
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
            
            <form class="form" id="create_employee_form">
                @csrf             
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Employee Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            
                            <div class="col-lg-4">
                                <label>Employee Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Employee Name">
                                <div class="common-error form-text name_error"></div>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Employee Email <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Employee Email">
                                <div class="common-error form-text email_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Employee Mobile No <span class="mandatory_input">*</span></label>
                                <input type="number" class="form-control" id="mobile" name="phone" placeholder="Employee Mobile No ">
                                <span id="mobile-valid" class="hidden mob" style="display: none;">
                                  <i class="fa fa-check pwd-valid"></i>Valid Mobile No
                                </span>  
                                <span id="folio-invalid" class="hidden mob-helpers" style="display: none;"> 
                                  <i class="fa fa-times mobile-invalid"></i>Invalid mobile No
                                </span>
                                <div class="common-error form-text phone_error"></div> 
                            </div>
                        </div>
                        <div class="form-group row">
                          {{-- <div class="col-lg-4">
                            <label>Department <span class="mandatory_input">*</span></label>
                            <select class="form-control " name="department" id="department">
                                <option value="">Select Department </option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id}}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                            <div class="col-lg-4">
                                <label>Company <span class="mandatory_input">*</span></label>
                                <select class="form-control " name="division" id="division">
                                    <option value="">Select Company </option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <div class="col-lg-4">
                                <label>Access Right <span class="mandatory_input">*</span></label>
                                <select class="form-control" id="rights" name="rights">
                                    <option value="">Select Access Right</option>
                                   <option value="1">Need Validation</option>
                                   <option value="2">Notes</option>
                                   <option value="3">Both</option>
                                </select>
                                <div class="common-error form-text rights_error"></div>
                           </div> -->
                            <!-- <div class="col-lg-4">
                                <label>Location <span class="mandatory_input">*</span></label>
                                <select class="form-control " name="location" id="location">
                                    {{-- <option value="">Select Location </option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id}}">{{ $location->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div> -->
                            {{-- <div class="col-lg-4">
                                <label>Password <span class="mandatory_input">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Employee Password" onkeyup="validatePassword()">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" id="toggle-password" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                                <div id="password-validation-message" style="color: red;"></div>
                            </div> --}}
                            
                            <div class="col-lg-4">
                                <label> Password</label>
                                
                                <input type="password" class="form-control" id="password" name="password" placeholder="Employee Password" onkeyup="validatePassword()">
                                <div class="input-group-append" style="position: absolute; top:31px; right:7px;">
                                    <span class="input-group-text toggle-password" id="toggle-password" onclick="togglePasswordVisibility()" style="padding: 10px;cursor: pointer;">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="common-error form-text super_department_error"></div>
                           </div>
                           <div class="col-lg-4">
                            <label>Roles <span class="mandatory_input">*</span></label>
                            <select class="form-control" id="role" name="role_id">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                                    @endforeach
                            </select>
                            <div class="common-error form-text status_error"></div>
                       </div>
                       
                        </div>
                        
                        <div class="form-group row">

                            <div class="col-lg-4">
                                <label> Department</label>
                                <select class="form-control select2_element departments_select2_dropdown" id="super_department" name="super_department[]" multiple>
                                    <option value="">Select  Department</option>
                                    @foreach($supdept as $supdepts)
                                            <option value="{{ $supdepts->id }}">{{ $supdepts->name }}</option>
                                        @endforeach
                                </select>
                                <div class="common-error form-text super_department_error"></div>
                           </div>

                        <div class="col-lg-4">
                          <label>Sub-Department <span class="mandatory_input">*</span></label>
                          <select class="form-control select2_element departments_select2_dropdown" id="departments" name="department_id[]" multiple>
                              <!-- The 'multiple' attribute allows multiple selections -->
                             
                          </select>
                          <div class="common-error form-text department_id_error"></div>
                      </div>

                         
                            <div class="col-lg-4">
                                <label>Employee Id<span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="emp_id" name="employee_id" placeholder="Employee id">
                                <div class="common-error form-text employee_id_error"></div>
                            </div>                                                                            
                            
                          
                        
                         <div class="col-lg-4 mt-2">
                        <label>Designation <span class="mandatory_input">*</span></label>
                        <select class="form-control" id="designation" name="designation" onchange="handleDesignationChange(this)">
                            <option value="">Select Designation</option>
                            @foreach($designation as $designation)
                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
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
        $('input[name="mobile"]').keyup(function(e)
                                {
         if (/\D/g.test(this.value))
        {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
        });
    </script>
    <script>
         $("#mobile").on("blur", function(){
        var mobNum = $(this).val();
        var filter = /^\d*(?:\.\d{1,2})?$/;

          if (filter.test(mobNum)) {
            if(mobNum.length==10){
                  // alert("valid");
              $("#mobile-valid").removeClass("hidden");
              $("#folio-invalid").addClass("hidden");
             } else {
                //  alert('Please put 10  digit mobile number');
               $("#folio-invalid").removeClass("hidden");
               $("#mobile-valid").addClass("hidden");
                return false;
              }
            }
            else {
              //  alert('Not a valid number');
              $("#folio-invalid").removeClass("hidden");
              $("#mobile-valid").addClass("hidden");
              return false;
           }
    
  });
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

//     function validatePassword() {
//     const passwordInput = document.getElementById("password");
//     const password = passwordInput.value;
//     const passwordValidationMessage = document.getElementById("password-validation-message");

    
//     const lengthPattern = /^.{8,}$/;
//     const uppercasePattern = /[A-Z]/;
//     const lowercasePattern = /[a-z]/;
//     const specialCharacterPattern = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/;

//     let errorMessage = "";

//     if (!lengthPattern.test(password)) {
//         errorMessage = "Password must be at least 8 characters long.";
//     } else if (!uppercasePattern.test(password)) {
//         errorMessage = "Password must include at least one uppercase [A-Z] letter.";
//     } else if (!lowercasePattern.test(password)) {
//         errorMessage = "Password must include at least one lowercase [a-z] letter.";
//     } else if (!specialCharacterPattern.test(password)) {
//         errorMessage = "Password must include at least one special character (!@#$%^&*()_+{}[]:;<>,.?~-).";
//     }

//     passwordValidationMessage.textContent = errorMessage;
// }

function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("toggle-password");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        togglePassword.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
    } else {
        passwordInput.type = "password";
        togglePassword.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
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
    var roleSelect = document.getElementById('role');
    var departmentSelect = document.getElementById('departments');
    var departmentSelect = document.getElementById('super_department');
    roleSelect.addEventListener('change', function() {
        var selectedRole = this.value;
        if (selectedRole == 9) {
            departmentSelect.removeAttribute('multiple');
            super_departmentSelect.removeAttribute('multiple');
        } else {
            departmentSelect.setAttribute('multiple', 'multiple');
            super_departmentSelect.setAttribute('multiple', 'multiple');
        }
    });
</script>
@endpush