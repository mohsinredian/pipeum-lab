@extends('admin.layout.master', ['page_title' => 'Edit Department'])
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Sub-Department</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Sub-Department</li>
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
            
            <form class="form" id="edit_department_form">
                @csrf
                <input type="hidden" name="department_id" value="{{$department['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit Sub-Department Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">

                            <!-- <div class="col-lg-4">
                                <label>Prefix <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="prefix" name="prefix" placeholder="Enter Prefix" value="{{$department['prefix']}}">
                                <div class="common-error form-text prefix_error"></div>
                            </div> -->

                            <div class="col-lg-4">
                                <label>Sub-Department Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Department Name" value="{{$department['name']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>
                            
                            
                             <div class="col-lg-4">
                             
                              <label>Reviewer 1 </label>
                              
                                
                                <select class="form-control " name="dep_rew1" id="dep_rew1">
                                <option value="">Select Reviewer 1 </option>
                                @foreach ($emp_hods as $emp_hod)
                                    <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['dep_rew1']) selected="selected" @endif>{{ $emp_hod->name }}</option>
                                @endforeach
                             </select>
                                <div class="common-error form-text name_error"></div>
                             </div>
                             <div class="col-lg-4">
                              
                              <label>Reviewer 2 </label>
                             
                                <select class="form-control " name="dep_rew2" id="dep_rew2">
                                <option value="">Select Reviewer 2 </option>
                                @foreach ($emp_hods as $emp_hod)
                                    <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['dep_rew2']) selected="selected" @endif>{{ $emp_hod->name }}</option>
                                @endforeach
                             </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                            
                              <label>Reviewer 3 </label>
                            
                                <select class="form-control " name="dep_rew3" id="dep_rew3">
                                <option value="">Select Reviewer 3 </option>
                                @foreach ($emp_hods as $emp_hod)
                                    <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['dep_rew3'])  selected="selected" @endif>{{ $emp_hod->name }}</option>
                                @endforeach
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                           
                              <label>Reviewer 4 </label>
                             
                                <select class="form-control " name="dep_rew4" id="dep_rew4">
                                <option value="">Select Reviewer 4 </option>
                                @foreach ($emp_hods as $emp_hod)
                                    <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['dep_rew4']) selected="selected" @endif>{{ $emp_hod->name }}</option>
                                @endforeach
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                              <label>HOD <span class="mandatory_input">*</span></label>
                              <!-- <input type="text" class="form-control" id="name" name="name" placeholder="Department Name" value="{{$department['name']}}"> 
                            -->
                           
                            <select class="form-control " name="dep_hod" id="dep_hod">
                              <option value="">Select HOD </option>
                              @foreach ($emp_hods as $emp_hod)
                                  <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['dep_hod']) selected="selected" @endif>{{ $emp_hod->name }}</option>
                              @endforeach
                           </select>
                              <div class="common-error form-text name_error"></div>
                           </div>
                           <div class="col-lg-4">
                          
                            <label>Group Head </label>
                           
                            
                          <select class="form-control " name="group_cio" id="group_cio">
                            <option value="">Select Group Head</option>
                            @foreach ($emp_hods as $emp_hod)
                                <option value="{{ $emp_hod->user_id}}"@if($emp_hod->user_id == $department['group_cio']) data-toggle="tooltip" selected="selected" @endif>{{ $emp_hod->name }}</option>
                            @endforeach
                         </select>
                            <div class="common-error form-text name_error"></div>
                         </div>
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $department["status"] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $department["status"] == 0 ? 'selected' : '' }}>Inactive</option>
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
                <div class="col-lg-4" id="customToast" style="display: none; position: fixed; top:450px; right: 20px; padding: 15px; background-color: #1e50a1;color: #fff;  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); "></div>

            </form>
          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>

$(document).ready(function() {
  var departmentNameInput = $('#name');
  var statusInput = $('#status');
  var editDepartmentForm = $('#edit_department_form');

  // Function to initialize or destroy validation
  function initializeOrDestroyValidation() {
    var isNameChanged = departmentNameInput.val() !== departmentNameInput.attr('value');
    var isStatusChanged = statusInput.val() !== "{{ $department['status'] }}";

    if (isNameChanged || isStatusChanged) {
      // Department name or status has changed, remove validation for specific fields
      editDepartmentForm.validate().settings.rules = {};
    } else {
      // Department name and status have not changed, reapply validation rules
      editDepartmentForm.validate({
        rules: {
          dep_hod: {
            required: true
          },
          // dep_rew1: {
          //   required: true
          // },
          // dep_rew2: {
          //   required: function(element) {
          //     return $('#dep_rew3').val().length > 0 || $('#dep_rew4').val().length > 0;
          //   }
          // },
          // dep_rew3: {
          //   required: function(element) {
          //     return $('#dep_rew4').val().length > 0;
          //   }
          // }
        },
        messages: {
          dep_hod: {
            required: 'Please Select HOD of Department'
          },
          // dep_rew1: {
          //   required: 'This Field Is Required..'
          // },
          // dep_rew2: {
          //   required: 'This Field Is Required'
          // },
          // dep_rew3: {
          //   required: 'This Field Is Required'
          // }
        },
        errorPlacement: function(error, element) {
          error.appendTo(element.siblings('.name_error'));
        }
      });
    }
  }

  // Initialize validation and check when department name or status changes
  initializeOrDestroyValidation();
  departmentNameInput.on('keyup', initializeOrDestroyValidation);
  statusInput.on('change', initializeOrDestroyValidation);
});


</script>
    </section>
@endsection

@push('script')

<script src="{{asset('admin/js/department.js')}}"></script>
<script>
  // $(document).ready(function () {
  //     // Display a toast message when the page loads
  //     Swal.fire({
  //         icon: 'success',
  //         text: ' In case reviewer is not available, please leave the field blank',
  //         showConfirmButton: false,
  //         timer: 3000
  //     });
  // });
  $(document).ready(function () {
            // Display a toast message in a custom div when the page loads
            $('#customToast').text('In case reviewer is not available, please leave the field blank').fadeIn().delay(5000).fadeOut();
        });
</script>

@endpush