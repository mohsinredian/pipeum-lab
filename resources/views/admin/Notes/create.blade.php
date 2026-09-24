
@extends('admin.layout.master', ['page_title' => 'Create Notes'])

@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <style>
      select[readonly] {
              background: #eee;
              pointer-events: none;
              touch-action: none;
          }
          </style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Notes</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Create Notes</li>
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
            
            <form class="form" id="create_notes_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Note Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                      
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Department Name</label>
                                <select class="form-control" id="dept_id" name="dept_id" readonly>
                                    @foreach ($departments as $department)
                                        <option
                                            value="{{ $department->id }}">
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                              
                                <div class="common-error form-text dept_id_error"></div>
                            </div>
                           
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Proposal Number</label>
                             <input type="text" id="prop_no" name="prop_no" value="" class="form-control" Placeholder="Enter Proposal Number">
                             <div class="common-error form-text prop_no_error"></div>
                            </div>
                           
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Subject Line</label>
                             <input type="text" id="sub_line" name="sub_line" value="" class="form-control" Placeholder="Enter Subject Line">
                             <div class="common-error form-text sub_line_error"></div>
                            </div>
                           
                        </div>
                        </div>
                        <div class="form-group row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Upload Documents</label>
                             <input type="file" id="upload_docs" name="upload_docs" value="" class="form-control" Placeholder="Enter Subject Line" accept=".docx">
                             <div class="common-error form-text upload_docs_error"></div>
                            </div>
                        </div>
                      </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 ">
                        <textarea name="subject" id="subject" rows="10" cols="80"></textarea>
                           <div class="common-error form-text subject_error"></div>
                        </div>
                 </div>
                        <select class="form-control" id="division_id" name="division_id" hidden>
                            @foreach ($divisions as $division)
                                <option
                                    value="{{ $division->id }}">
                                    {{ $division->name }}</option>
                            @endforeach
                        </select>
                             <input type="hidden" name="draft1" id="draft1" value="0" >
                            <input type="hidden" name="draft2" id="draft2" value="1" >
                   <div class="card">
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                            <button type="button" class="btn btn-success save_btn" value="save_nv">Save</button>
                            <button type="submit" class="btn btn-success submit_btn" value="submit_nv" >Submit</button>
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
<script src="{{asset('admin/js/notes.js')}}"></script>
<script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>


<script>
   $(document).ready(function () {
    // Initialize CKEditor
    CKEDITOR.replace('subject');

    // Handle file upload
    $('#upload_docs').on('change', function (event) {
        var file = event.target.files[0];

        if (file) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var arrayBuffer = e.target.result;
                var docFile = new Uint8Array(arrayBuffer);

                mammoth.convertToHtml({ arrayBuffer: docFile })
                    .then(function (result) {
                        // Set converted HTML content to CKEditor
                        CKEDITOR.instances.subject.setData(result.value);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            };

            reader.readAsArrayBuffer(file);
        }
    });
});

</script>


@endpush
