@extends('admin.layout.master', ['page_title' => 'Edit Service'])

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Update Service BOQ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Service BOQ</li>
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
                    @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    <form class="form" action="{{ url('admin/nv_material/update_service') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="s_id" value="{{ $data->id }}">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Service Details</h3>
                                <div class="card-toolbar">
                                <?php
                        $url = '/admin/nv_material/create/' . $data->nv_id . '/' . $nv_service->company_id;
                      ?>
                          
                          <button type="reset" class="btn btn-primary" onclick="goBack()">Back &lt;&lt;</button>
                                <!-- <button type="reset" class="btn btn-primary " onclick="window.history.go(-2);">
                                   Back << </button> -->

                            </div>
                            </div>
                            <!-- /.card-header -->
                            <!--begin::Form-->
                            {{--<div class="col-lg-4">
                                <label>Service Code <span class="mandatory_input">*</span></label>
                                <select class="form-control" name="service_code_new" id="service_code_new" onclick="fetchCodeSer()">
                                    <!-- <option value="">Select Material Code</option> -->
                                    @foreach ($serviceCodes as $serviceCode)
                                        <option value="{{ $serviceCode }}" {{ $serviceCode ==  $data->service_code ? 'selected':'' }}>
                                            {{ $serviceCode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>--}}

                            <div class="card-body">
                                <div class="form-group row">

                                    <!-- <div class="col-lg-4">
                                        <label>Company Name<span class="mandatory_input">*</span></label>
                                        <select class="form-control" name="company_id" id="company_id">
                                            <option value="">Select Company</option>
                                        </select>
                                        <div class="common-error form-text name_error"></div>
                                    </div> -->

                                    <div class="col-lg-4">
                                        <label>Service Code <span class="mandatory_input">*</span></label>
                                        <input type="number" class="form-control" id="service_code" name="service_code"
                                            value="{{ $data->service_code }}" placeholder="Enter service code" >
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Service Description<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="description"
                                            name="description" placeholder="Enter Service Short Text"
                                            value="{{ $data->description }}" readonly>
                                        <div class="common-error form-text name_error"></div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>UoM<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="uom" name="uom"
                                            value="{{ $data->uom }}" placeholder="Enter UoM" readonly>
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Rate<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="rate" name="rate"
                                            value="{{ $data->rate }}" placeholder="Enter rate" readonly onblur="sumSolarCap_2()">
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Quantity<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control decimal" id="qty" name="qty"
                                            value="{{ $data->qty }}" placeholder="Enter quantity" onblur="sumSolarCap_2()" onkeypress="return isNumberKey(event)">
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Amount<span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="amount" name="amount"
                                            value="{{ $data->amount }}" placeholder="Enter amount" readonly onblur="sumSolarCap_2()">
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                    
                                    <!-- <div class="col-lg-4">
                                        <label>File<span class="mandatory_input">*</span></label>
                                        <input type="file" class="form-control" id="file" name="file"
                                            value="{{ $data->file }}" placeholder="Enter file">
                                        <div class="common-error form-text name_error"></div>
                                    </div> -->
                                    
                                </div>
                            </div>
                            <!--end::Form-->
                            <!-- /.card-body -->
                        </div>

                        <div class="card">

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="btn btn-success" onclick="countAndSubmit()">Submit</button>
                                        <button type="reset" class="btn btn-secondary"
                                            onclick="history.back();">Cancel</button>
                                            <p style="display: none;"> <span id="clickCount"></span></p>
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
    <script src="{{ asset('admin/js/capex.js') }}"></script>

    <script>
         function fetchCodeSer() {
            var token = "{{ csrf_token() }}";
            var material_code = $("#service_code_new").val();
        
            console.log(material_code);
           
                $.ajax({
                    url: '/admin/fetch-material-data',
                    type: 'post',
                    dataType: "json",
                    data: {
                        '_token': token,
                        'key': material_code,
                    },
                    success: function(data) {
                        //response(data);
                        $('#service_code').val(data.service_code);
                        $('#description').val(data.description);
                        $('#uom').val(data.uom);
                        $('#rate').val(data.rate);
                        $('#qty').val(data.qty);
                        $('#amount').val(data.amount);
                      
                       
                    }

                });
               

           
            //   count++;
        };

        //Rate calculation
        function sumSolarCap_2() {
            var sce = $('#rate').val();
            var sca = $('#qty').val();
            var sum = (1 * sce) * (1 * sca);
            $('#amount').val(sum);
        }

//back button click

let clickCount = 0;


let totalCount = sessionStorage.getItem('totalClicks');
if (totalCount) {
    clickCount = parseInt(totalCount);
    document.getElementById('clickCount').textContent = clickCount;
}


function countAndSubmit() {
    clickCount++;
    document.getElementById('clickCount').textContent = clickCount;

    
    sessionStorage.setItem('totalClicks', clickCount);
}

function goBack() {
        if (clickCount > 0) {
            window.history.go(-clickCount);

            clickCount = 0;
            document.getElementById('clickCount').textContent = clickCount;

            sessionStorage.removeItem('totalClicks');

            if (clickCount == 0) {
                window.history.go(-1);
            }
        } else {

            window.history.go(-1);
        }
    }
    $(document).on('keydown', '.decimal', function(e) {
        var type = $(this).hasClass('decimal') ? 'decimal' : '';
        var key = e.key;
        var isSelectAll = (key === "a" || key === 'A') && e.ctrlKey;
        var isTab = key === "Tab" || (key === "Tab" && e.shiftKey);
        var isReload = (key === "R" || key === "r" || key === "F5") && e.ctrlKey;
        var keys = ["Del", "Delete", "Backspace", "Home", "End", "Up", "Down", "Left", "Right", "ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", ",", ".", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
 
        if (isTab || isReload || isSelectAll) {
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
    </script>
    <script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
@endpush
