@extends('admin.layout.master', ['page_title' => 'Create workflow'])
@push('style')
<style>
   .common-error {
      color: red;
    }
    .error{
      color:red !important;
    }

    .work_dep_error {
    color: red !important;
}
#sr_no-error{
    color: red !important;
}
    </style>
    @endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage CAPEX Workflow </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">CAPEX Workflow</li>
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
            
            <form class="form" id="create_workflow_form">
                @csrf      
                <input type="hidden" class="form-control" name="workflow_id" id="workflow_id">    
                 
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Stage Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                      <div class="card-body">
                        <div class="form-group row">
                        <div class="col-lg-2">
                                <label>Sr No</label>
                             <input class="form-control " type="number" name="sr_no" id="sr_no">
                             <div class="common-error form-text sr_no_error"></div>
                            </div>
                            <div class="col-lg-10">
                            </div>
                            <div class="col-lg-4">
                              <label>Select Sub-Department <span class="mandatory_input">*</span></label>
                              <select class="form-control " name="work_dep" id="work_dep">
                                  <option value="">Select Sub-Department</option>
                                  @foreach ($departments as $department)
                                      <option value="{{ $department->id }}">{{ $department->name }}</option>
                                  @endforeach
                              </select>
                              <div class="common-error form-text work_dep_error"></div>
                            </div>
                            
                             <div class="col-lg-4">
                                <label>Reviewer 1 </label>
                                <select class="form-control " name="work_rew1" id="work_rew1">
                                <option value="">Select Reviewer 1 </option>
                             
                             </select>
                                <div class="common-error form-text name_error"></div>
                             </div>
                             <div class="col-lg-4">
                                <label>Reviewer 2 </label>
                                <select class="form-control " name="work_rew2" id="work_rew2">
                                <option value="">Select Reviewer 2 </option>
                           
                             </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Reviewer 3 </label>
                                <select class="form-control " name="work_rew3" id="work_rew3">
                                <option value="">Select Reviewer 3 </option>
                         
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Reviewer 4 </label>
                                <select class="form-control " name="work_rew4" id="work_rew4">
                                <option value="">Select Reviewer 4 </option>
                             
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Approver</label>
                                <select class="form-control " name="approver" id="approver">
                                <option value="">Select Approver</option>
                             
                            </select>
                                <div class="common-error form-text name_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Status</label>
                                <select class="form-control " name="status" id="status">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                             
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

  

  $('#create_workflow_form').validate({

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

<script src="{{asset('admin/js/workflow.js')}}"></script>

@endpush