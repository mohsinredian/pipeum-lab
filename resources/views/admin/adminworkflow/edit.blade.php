@extends('admin.layout.master', ['page_title' => 'Edit Notes Workflow'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Notes Workflow</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Notes Workflow</li>
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
            
            <form class="form" id="edit_newworkflow_form" method="POST" action="{{ route('admin.update_notesworkflows', ['workflow' => $workflow->id]) }}">
                @csrf
                
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit Notes Workflow Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">

                            <div class="col-lg-4">
                                <label>Workflow Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Workflow Name" value="{{ $workflow->name }}">
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

<script>
  $(document).ready(function() {
      $('#edit_newworkflow_form').submit(function(event) {
          event.preventDefault(); // Prevent default form submission
          $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: new FormData(this),
              processData: false,
              contentType: false,
              success: function(response) {
                  var res = response;
                  if (res.result == 'success') {
                      Swal.fire({
                        text: "Notes Workflow updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
                      }).then(function() {
                          window.location = '/admin/notesworkflows';
                      });

                      $('#edit_newworkflow_form')[0].reset();
                  }
              }
          });
      });
  });
</script>

@endpush