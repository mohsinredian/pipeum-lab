@extends('admin.layout.master', ['page_title' => 'Create Material'])


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Material BOQ</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Material BOQ</li>
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
                    {{-- @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif
                    @if (Session::has('err'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ Session::get('err') }}
                        </div>
                    @endif --}}
                    <form class="form" id="yourFormID" action="{{ url('admin/materialboq/store') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">New Material BOQ Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <!--begin::Form-->

                            <div class="card-body">
                                <div class="form-group row">
                                    {{-- <div class="col-lg-4">
                                        <label>Company Name<span class="mandatory_input">*</span></label>

                                        <select class="form-control" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                            <option value="">Select Company</option>
                                            @foreach ($company as $com)
                                                <option value="{{ $com->id }}">{{ $com->name }}</option>
                                            @endforeach
                                        </select>

                                       <div class="common-error form-text" id="company_id_error"></div>
                                    </div> --}}

                                    <div class="col-lg-4">
                                        <label>Material Code <span class="mandatory_input">*</span></label>
                                        <input type="number" class="form-control" id="activity" name="activity" value="{{ old('activity') }}"
                                            placeholder="Enter Material Code" onkeypress="return isNumberKey(event)" oninput="validateInput(this)">
                                       <div class="common-error form-text" id="activity_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Material Description<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="material_short_text" value="{{ old('material_short_text') }}"
                                            name="material_short_text" placeholder="Material Description">
                                       <div class="common-error form-text" id="material_short_text_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>UoM<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="uom" name="uom" value="{{ old('uom') }}"
                                            placeholder="Enter UoM">
                                       <div class="common-error form-text" id="uom_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Rate<span class="mandatory_input">*</span></label>
                                        <input type="number" class="form-control decimal" id="rate_add" name="rate_add" value="{{ old('rate_add') }}"
                                            placeholder="Enter Rate">
                                       <div class="common-error form-text" id="rate_add_error"></div>
                                    </div>
                                </div>
                                <div class="form-group row">



                                </div>
                            </div>
                            <!--end::Form-->
                            <!-- /.card-body -->
                        </div>

                        <div class="card">

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" id="submitBtn" class="btn btn-success">Submit</button>
                                        <button type="reset" class="btn btn-secondary"
                                            onclick="history.back();">Cancel</button>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
  
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            $("#submitBtn").click(function(e) {
                e.preventDefault(); 
        
                var valid = true;
                $(".common-error").text(""); 
        

                // Activity field
                if ($("#activity").val() === "") {
                    $("#activity_error").text("Material Code is required");
                    valid = false;
                } else {
                    $("#activity_error").text(""); 
                }
        
                // UoM field
                if ($("#uom").val() === "") {
                    $("#uom_error").text("UoM is required");
                    valid = false;
                } else {
                    $("#uom_error").text(""); 
                }
        
                // Service Short Text field
                if ($("#material_short_text").val() === "") {
                    $("#material_short_text_error").text("Material Description is required");
                    valid = false;
                } else {
                    $("#material_short_text_error").text(""); 
                }
        
                // Rate field
                if ($("#rate_add").val() === "") {
                    $("#rate_add_error").text("Rate is required");
                    valid = false;
                } else {
                    $("#rate_add_error").text(""); 
                }
        
                if (valid) {
                    
                Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Form submitted successfully!',
                buttonsStyling: false,
                confirmButtonText: "Ok",
                confirmButtonClass: "btn font-weight-bold btn-primary"
            }).then(function() {
                $("#yourFormID").submit();
            });
           
        }
    });
               

            $("#activity").on("input", function() {
                $("#activity_error").text("");
            });
        
            $("#uom").on("input", function() {
                $("#uom_error").text("");
            });
        
            $("#material_short_text").on("input", function() {
                $("#material_short_text_error").text("");
            });
        
            $("#rate_add").on("input", function() {
                $("#rate_add_error").text("");
            });
        });

        $(document).on('keydown', '.decimal', function(e) {
        var type = $(this).hasClass('decimal') ? 'decimal' : '';
        var key = e.key;
        var isCtrlV = (e.ctrlKey && (key === 'v' || key === 'V'));
        var isSelectAll = (key === "a" || key === 'A') && e.ctrlKey;
        var isTab = key === "Tab" || (key === "Tab" && e.shiftKey);
        var isReload = (key === "R" || key === "r" || key === "F5") && e.ctrlKey;
        var keys = ["Del", "Delete", "Backspace", "Home", "End", "Up", "Down", "Left", "Right", "ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", ",", ".", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
 
        if (isTab || isReload || isSelectAll || isCtrlV) {
            return true;
        }
 
        switch(type) {
            case 'decimal':
                var invalidKey = $.inArray(key, keys) === -1;
                break;
           
                invalidKey = $.inArray(key, keys) === -1;
        }
 
        if (invalidKey) {
            e.preventDefault();
        }
    });
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    function validateInput(input) {
        input.value = input.value.replace(/[^\d]/g, '');
    }
    </script>
@endpush
