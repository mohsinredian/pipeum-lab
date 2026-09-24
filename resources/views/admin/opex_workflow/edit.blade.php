@extends('admin.layout.master', ['page_title' => 'Create workflow'])
@push('style')
<style>
   .common-error {
      color: red;
    }
    </style>
    @endpush

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage OPEX Workflow</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">OPEX Workflow</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            
            <form class="form" id="edit_workflow_form">
                @csrf      
                <input type="hidden" class="form-control" name="workflow_id" id="workflow_id" value="{{$workflow['id']}}">    
                 
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit Stage Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                      <div class="card-body">
                        <div class="form-group row">
                        <div class="col-lg-2">
                                <label>Sr No</label>
                             <input class="form-control " type="number" name="sr_no" id="sr_no" value="{{$workflow['sr_no']}}">
                            </div>
                            <div class="col-lg-10">
                            </div>
                            <div class="col-lg-4">
                              <label>Select Sub-Department <span class="mandatory_input">*</span></label>
                              <select class="form-control " name="work_dep" id="work_dep">
                                  <option value="">Select Department</option>
                                  @foreach ($departments as $department)
                                      <option value="{{ $department->id }}" @if ($department->id == $workflow['work_dep']) selected="selected" @endif>{{ $department->name }}</option>
                                  @endforeach
                              </select>
                            </div>
                            <input type="hidden" id="dep_work_rew1" value="{{$workflow['work_rew1']}}">   
                             <div class="col-lg-4">
                         
                                <label>Reviewer 1 </label>
                             
                                <select class="form-control " name="work_rew1" id="work_rew1">
                                <option value="">Select Reviewer 1 </option>
                             
                             </select>
                                <div class="common-error form-text name_error"></div>
                             </div>
                             <input type="hidden" id="dep_work_rew2" value="{{$workflow['work_rew2']}}">  
                             <div class="col-lg-4">
                           
                              <label>Reviewer 2 </label>
                           
                                <select class="form-control " name="work_rew2" id="work_rew2">
                                <option value="">Select Reviewer 2 </option>
                           
                             </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <input type="hidden" id="dep_work_rew3" value="{{$workflow['work_rew3']}}">  
                            <div class="col-lg-4">
                             
                              <label>Reviewer 3 </label>
                           
                                <select class="form-control " name="work_rew3" id="work_rew3">
                                <option value="">Select Reviewer 3 </option>
                         
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <input type="hidden" id="dep_work_rew4" value="{{$workflow['work_rew4']}}">  
                            <div class="col-lg-4">
                            
                              <label>Reviewer 4 </label>
                          
                                <select class="form-control " name="work_rew4" id="work_rew4">
                                <option value="">Select Reviewer 4 </option>
                             
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <input type="hidden" id="dep_work_app" value="{{$workflow['approver']}}">  
                            <div class="col-lg-4">
                                <label>Approver <span class="mandatory_input">*</span></label>
                                <select class="form-control " name="approver" id="approver">
                                <option value="">Select Approver</option>
                             
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $workflow["status"] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $workflow["status"] == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="common-error form-text status_error"></div>
                           </div>
                          
                        </div>
                    </div>
                     
                
                <div class="card">                        
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success ">Submit</button>
                              <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                            </div>
                            
                        </div>
                        <div  id="customToast" style="display: none; position: fixed; top:450px; right: 20px; padding: 15px; background-color: #1e50a1;color: #fff;  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); "></div>
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
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
<script>

  $(document).ready(function() {

  

  $('#edit_workflow_form').validate({

    rules: {
      approver: {
        required: true
      },
      work_rew1: {
        required: true
      },
      work_rew2: {
        required: function(element) {
          return $('#work_rew3').val().length > 0|| $('#work_rew4').val().length > 0;
        }

      },

      work_rew3: {
        required: function(element) {
          return $('#work_rew4').val().length > 0;
        }
      }
    },
    messages: {
      approver: {
        required: 'This Field Is Required'
      },
      work_rew1: {
        required: 'This Field Is Required'
      },
      work_rew2: {
        required: 'This Field Is Required'
      },
      work_rew3: {
        required: 'This Field Is Required'
      }
    },
    errorPlacement: function(error, element) {
      error.appendTo(element.siblings('.name_error'));
    }

  });

});

</script> -->
    </section>


   
@endsection

@push('script')

<script src="{{asset('admin/js/opex_workflow.js')}}"></script>
<script>
  
  $(document).ready(function () {
            // Display a toast message in a custom div when the page loads
            $('#customToast').text('In case reviewer is not available, please leave the field blank').fadeIn().delay(5000).fadeOut();
        });
  </script>

@endpush

