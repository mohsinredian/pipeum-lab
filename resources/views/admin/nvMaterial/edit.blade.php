@extends('admin.layout.master', ['page_title' => 'Edit Material'])
@push('style')
<style>

    .swal2-icon.swal2-error {
        display: block !important;
        margin-left: auto !important;
        margin-right: auto !important;
        margin-top: 20px !important; 
        margin-bottom: 20px !important;
    }
</style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Update Material BOQ</h1>
            </div><!-- /.col -->
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
                @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ Session::get('success') }}
                </div>
                @endif
                <form id="edit_form" class="form" action="{{ url('admin/nv_material/update_material') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="mt_id" value="{{ $data->id }}">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Material Details</h3>
                            <div class="card-toolbar">
                                @php
                                $create_page_url = session('current_url')."#materialboq";
                                 @endphp
                               <button type="reset" class="btn btn-primary" onClick="window.open('<?=@$create_page_url?>','_self')">Back &lt;&lt;</button>  
                                {{-- <button type="reset" class="btn btn-primary" onclick="goBack()">Back &lt;&lt;</button> --}}


                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!--begin::Form-->


                        <div class="card-body">
                            <div class="form-group row">

                                {{-- <div class="col-lg-4">
                                    <label>Select Material</label>
                                    <select name="material_code_new" class="form-control" id="material_code_new" onclick="fetchCode()">
                                        @foreach ($materialCodes as $materialCode)
                                        <option value="{{ $materialCode }}" {{ $materialCode == $data->material_code ? 'selected' : '' }}>
                                            {{ $materialCode }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Material Code <span class="mandatory_input">*</span></label>
                                    <input type="number" class="form-control" id="material_code" name="material_code" value="{{ $data->material_code }}" placeholder="Enter material code">
                                    <div class="common-error form-text name_error"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label>Material Description<span class="mandatory_input">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Service Short Text"
                                    id="material_short_text" name="material_short_text" value="{{ $data->material_short_text }}" readonly>
                                    <div class="common-error form-text name_error"></div>
                                </div>

                                <div class="col-lg-4">
                                    <label>UoM<span class="mandatory_input">*</span></label>
                                    <input type="text" class="form-control" id="uom" name="uom"  value="{{ $data->uom }}"  placeholder="Enter UoM" readonly>
                                    <div class="common-error form-text name_error"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label>Rate<span class="mandatory_input">*</span></label>
                                    <input type="text" class="form-control decimal" readonly id="rate" name="rate" value="{{ $data->rate }}" placeholder="Enter UoM">
                                    <div class="common-error form-text name_error"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label>Rate Reference</label>
                                    <select name="rate_reference" class="form-control" id="rate_reference">
                                        <option value="">Select Rate Reference</option>
                                        <option value="0" {{ $data->rate_reference == '0' ? 'selected' : '' }}>Vendor Quotation</option>
                                        <option value="1" {{ $data->rate_reference == '1' ? 'selected' : '' }}>Last Work Order</option>
                                        <option value="2" {{ $data->rate_reference == '2' ? 'selected' : '' }}>C&M Rate Reference</option>
                                        <option value="3" {{ $data->rate_reference == '3' ? 'selected' : '' }}>User Estimation</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Quantity<span class="mandatory_input">*</span></label>
                                    <input type="text" class="form-control decimal" id="quantity" name="quantity" value="{{ $data->quantity }}" placeholder="Enter quantity" onkeypress="return isNumberKey(event)" oninput="validateInput(this)">
                                    <div class="common-error form-text name_error"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label>Amount<span class="mandatory_input">*</span></label>
                                    <input type="text" class="form-control" id="amount" name="amount" value="{{ $data->amount }}" placeholder="Enter amount" readonly>
                                    <div class="common-error form-text name_error"></div>
                                </div>


                                @if (isset($fiscalArr['fiscal1']))
                                <div class="col-12">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                                FY-{{ $fiscalArr['fiscal1'] }} </h6>

                                            <div id="collapseFive" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <div class="col-lg-1">
                                                            <label>April</label>
                                                            <input type="text" class="form-control month" id="april" name="april" value="{{ $data->april1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>May</label>
                                                            <input type="text" class="form-control month" id="may" name="may" value="{{ $data->may1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>June</label>
                                                            <input type="text" class="form-control month" id="june" name="june" value="{{ $data->june1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>July</label>
                                                            <input type="text" class="form-control month" id="july" name="july" value="{{ $data->july1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>August</label>
                                                            <input type="text" class="form-control month" id="august" name="august" value="{{ $data->august1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>September</label>
                                                            <input type="text" class="form-control month" id="september" name="september" value="{{ $data->september1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>October</label>
                                                            <input type="text" class="form-control month" id="oct" name="oct" value="{{ $data->oct1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>November</label>
                                                            <input type="text" class="form-control month" id="nov" name="nov" value="{{ $data->nov1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>December</label>
                                                            <input type="text" class="form-control month" id="dec" name="dec" value="{{ $data->dec1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>January</label>
                                                            <input type="text" class="form-control month" id="jan" name="jan" value="{{ $data->jan1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>Febuary</label>
                                                            <input type="text" class="form-control month" id="feb" name="feb" value="{{ $data->feb1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>March</label>
                                                            <input type="text" class="form-control month" id="march" name="march" value="{{ $data->march1 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if (isset($fiscalArr['fiscal2']))
                                <div class="col-12">

                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                FY-{{ $fiscalArr['fiscal2'] }} </h6>

                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <div class="col-lg-1">
                                                            <label>April</label>
                                                            <input type="text" class="form-control month" id="april2" name="april2" value="{{ $data->april2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>May</label>
                                                            <input type="text" class="form-control month" id="may2" name="may2" value="{{ $data->may2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>June</label>
                                                            <input type="text" class="form-control month" id="june2" name="june2" value="{{ $data->june2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>July</label>
                                                            <input type="text" class="form-control month" id="july2" name="july2" value="{{ $data->july2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>August</label>
                                                            <input type="text" class="form-control month" id="august2" name="august2" value="{{ $data->august2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>September</label>
                                                            <input type="text" class="form-control month" id="september2" name="september2" value="{{ $data->september2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>October</label>
                                                            <input type="text" class="form-control month" id="oct2" name="oct2" value="{{ $data->oct2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>November</label>
                                                            <input type="text" class="form-control month" id="nov2" name="nov2" value="{{ $data->nov2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>December</label>
                                                            <input type="text" class="form-control month" id="dec2" name="dec2" value="{{ $data->dec2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>January</label>
                                                            <input type="text" class="form-control month" id="jan2" name="jan2" value="{{ $data->jan2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>Febuary</label>
                                                            <input type="text" class="form-control month" id="feb2" name="feb2" value="{{ $data->feb2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>March</label>
                                                            <input type="text" class="form-control month" id="march2" name="march2" value="{{ $data->march2 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @endif

                                @if (isset($fiscalArr['fiscal3']))
                                <div class="col-12">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapsetwo" aria-expanded="true" aria-controls="collapsetwo">
                                                FY-{{ $fiscalArr['fiscal3'] }} </h6>

                                            <div id="collapsetwo" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">


                                                    <div class="form-group row">
                                                        <div class="col-lg-1">
                                                            <label>April</label>
                                                            <input type="text" class="form-control month" id="april3" name="april3" value="{{ $data->april3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>May</label>
                                                            <input type="text" class="form-control month" id="may3" name="may3" value="{{ $data->may3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>June</label>
                                                            <input type="text" class="form-control month" id="june3" name="june3" value="{{ $data->june3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>July</label>
                                                            <input type="text" class="form-control month" id="july3" name="july3" value="{{ $data->july3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>August</label>
                                                            <input type="text" class="form-control month" id="august3" name="august3" value="{{ $data->august3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>September</label>
                                                            <input type="text" class="form-control month" id="september3" name="september3" value="{{ $data->september3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>October</label>
                                                            <input type="text" class="form-control month" id="oct3" name="oct3" value="{{ $data->oct3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>

                                                        <div class="col-lg-1">
                                                            <label>November</label>
                                                            <input type="text" class="form-control month" id="nov3" name="nov3" value="{{ $data->nov3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>December</label>
                                                            <input type="text" class="form-control month" id="dec3" name="dec3" value="{{ $data->dec3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>January</label>
                                                            <input type="text" class="form-control month" id="jan3" name="jan3" value="{{ $data->jan3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>Febuary</label>
                                                            <input type="text" class="form-control month" id="feb3" name="feb3" value="{{ $data->feb3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <label>March</label>
                                                            <input type="text" class="form-control month" id="march3" name="march3" value="{{ $data->march3 }}" placeholder="Qty.">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div>



                                                        {{-- <div class="col-lg-4">
                                                            <label>File</label>
                                                            <input type="file" class="form-control" id="file" name="file" value="{{ $data->file }}" placeholder="Enter file">
                                                            <div class="common-error form-text name_error"></div>
                                                        </div> --}}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif


                            </div>
                            <!--end::Form-->
                            <!-- /.card-body -->
                        </div>

                        <div class="card">
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button id="edit_form" type="submit" class="btn btn-success" onclick="countAndSubmit()">Submit</button>
                                        <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">


</script>



<script>
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

    function fetchCode() {
        var token = "{{ csrf_token() }}";
        var material_code = $("#material_code_new").val();

        //console.log(material_code);

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
                $('#material_code').val(data.material_code);
                $('#material_short_text').val(data.material_short_text);
                $('#uom').val(data.uom);
                $('#rate').val(data.rate);
                $('#quantity').val(data.quantity);
                $('#amount').val(data.amount);
                $('#april').val(data.april);
                $('#may').val(data.may);
                $('#june').val(data.june);
                $('#july').val(data.july);
                $('#august').val(data.august);
                $('#september').val(data.september);
                $('#oct').val(data.oct);
                $('#nov').val(data.nov);
                $('#dec').val(data.dec);
                $('#jan').val(data.jan);
                $('#feb').val(data.feb);
                $('#march').val(data.march);
                $('#april2').val(data.april2);
                $('#may2').val(data.may2);
                $('#june2').val(data.june2);
                $('#july2').val(data.july2);
                $('#august2').val(data.august2);
                $('#september2').val(data.september2);
                $('#oct2').val(data.oct2);
                $('#nov2').val(data.nov2);
                $('#dec2').val(data.dec2);
                $('#jan2').val(data.jan2);
                $('#feb2').val(data.feb2);
                $('#march2').val(data.march2);
                $('#april3').val(data.april3);
                $('#may3').val(data.may3);
                $('#june3').val(data.june3);
                $('#july3').val(data.july3);
                $('#august3').val(data.august3);
                $('#september3').val(data.september3);
                $('#oct3').val(data.oct3);
                $('#nov3').val(data.nov3);
                $('#dec3').val(data.dec3);
                $('#jan3').val(data.jan3);
                $('#feb3').val(data.feb3);
                $('#march3').val(data.march3);

            }

        });



        //   count++;
    };

    //Rate calculation
    // function sumSolarCap_1() {
    //     var sce = $('#rate').val();
    //     var sca = $('#quantity').val();
    //     var sum = (1 * sce) * (1 * sca);
    //     $('#amount').val(sum);
    // }
</script>

<script>
    window.onload = function() {
        document.getElementById('edit_form').onsubmit = function(event) {

            function calculateTotalSum() {
                let totalSum = 0;
                const monthInputs = document.querySelectorAll('.month');

                monthInputs.forEach(monthInput => {
                    totalSum += parseFloat(monthInput.value) || 0;
                });

                return totalSum;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            const totalSum = calculateTotalSum().toFixed(2);
            if (isNaN(quantity)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid quantity!',
                    text: 'Please enter a valid quantity.',
                });
                event.preventDefault();
            } else if (totalSum != quantity) {
                Swal.fire({
                    icon: 'error',
                    title: 'Total quantity mismatch!',
                    text: 'The total sum of all months must be equal to the quantity.',
                });
                event.preventDefault();
            }
        };

   
    
        function sumSolarCap_2() {
            var rate = parseFloat(document.getElementById('rate').value);
            var sca = parseFloat(document.getElementById('quantity').value);
            var sum = rate * sca;

            document.getElementById('amount').value = isNaN(sum) ? '' : sum;
        }


        window.addEventListener('keyup', sumSolarCap_2);


        sumSolarCap_2();
    };
</script>

<script>
    // document.addEventListener("DOMContentLoaded", function () {
    //     var monthInputs = document.getElementsByClassName("month");

       
    //     for (var i = 0; i < monthInputs.length; i++) {
    //         monthInputs[i].addEventListener("input", function () {
               
    //             var sanitizedValue = this.value.replace(/[^0-9]/g, '');
    //             this.value = sanitizedValue;
    //         });
    //     }
    // });

    document.addEventListener("DOMContentLoaded", function () {
    var monthInputs = document.getElementsByClassName("month");

    for (var i = 0; i < monthInputs.length; i++) {
        monthInputs[i].addEventListener("input", function () {
            // Replace anything that isn't a digit or a single decimal point
            var sanitizedValue = this.value.replace(/[^0-9.]/g, '');
            
            // Ensure only one decimal point is allowed
            var parts = sanitizedValue.split('.');
            if (parts.length > 2) {
                sanitizedValue = parts[0] + '.' + parts.slice(1).join('');
            }
            
            this.value = sanitizedValue;
        });
    }
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
</script>
<script>
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
