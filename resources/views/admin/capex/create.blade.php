@extends('admin.layout.master', ['page_title' => 'Create Service'])


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage CAPEX</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">CAPEX</li>
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

                    <form class="form" id="create_capex_form">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">New CAPEX Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <!--begin::Form-->

                            <div class="card-body">
                                <div class="form-group row">

                                    <div class="col-lg-4">
                                        <label> Department <span class="mandatory_input">*</span></label>
                                        <select class="form-control" name="super_department" id="super_department">
                                            <option value="">Select Department</option>
                                            @foreach ($supdepart as $supdeparts)
                                                <option value="{{ $supdeparts->id }}">{{ $supdeparts->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="common-error form-text super_department_error"></div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <label>Sub-Department <span class="mandatory_input">*</span></label>
                                         <select class="form-control" name="department_id" id="department_id">
                                            <option value="">Select Sub-Department</option>
                                        </select>
                                        <input type="hidden" id="deps" value="">
                                        <div class="common-error form-text department_id_error"></div>
                                    </div> 
                                    @php
                                    $selectedDepartmentsArray = is_array($selectedDepartments) ? $selectedDepartments : [$selectedDepartments];
                                    $selectedDepartmentsString = implode(',', $selectedDepartmentsArray); 
                                @endphp

                                <input type="hidden" value="{{ htmlspecialchars($selectedDepartmentsString) }}" id="deps">


                                    <div class="col-lg-4">
                                        <label>Head <span class="mandatory_input">*</span></label>
                                        <select class="form-control" id="head" name="head">
                                            <option value="">Select Head</option>
                                            <option value="0">Load Growth</option>
                                            <option value="1">System Improvement</option>
                                            <option value="2">Statutory Requiremt</option>
                                            <option value="3">Infrastructure</option>
                                            <option value="4">Technology</option>
                                            <option value="5">Deposit</option>
                                            <option value="6">Overheads & Interest</option>
                                        </select>
                                        
                                        <div class="common-error form-text head_error">
                                          
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>Sub Head <span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="sub_head" name="sub_head"
                                            placeholder="Sub Head">
                                        <div class="common-error form-text sub_head_error"></div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>BPR Head <span class="mandatory_input">*</span></label>
                                        <select class="form-control" id="brp_head" name="brp_head">
                                            <option value="">Select BPR Head</option>
                                            <option value="0">Performance Obligation</option>
                                            <option value="1">Power Reliability</option>
                                            <option value="2">Infrastructure Development</option>
                                        </select>
                                        <div class="common-error form-text brp_head_error"></div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>Regular CAPEX/Project CAPEX <span class="mandatory_input">*</span></label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Select Regular CAPEX/Project CAPEX</option>
                                            <option value="0">Regular</option>
                                            <option value="1">Project</option>
                                        </select>
                                        <div class="common-error form-text status_error"></div>
                                    </div>
                                </div>  
                              
                                    <div id="budget-fields" class="form-group row">
                                        @php
                                        $currentYear = date('y');
                                        $currentMonth = date('m');
                                        $nextYear = $currentYear + 1;
                                        if ($currentMonth >= 4) {
                                            $startYear = $currentYear;
                                            $endYear = $nextYear;
                                        } else {
                                            $startYear = $currentYear - 1;
                                            $endYear = $currentYear;
                                        }
                                        @endphp
                                        
                                        
                                            @for ($i = 0; $i < 3; $i++)
                                                @php
                                                $fy = $startYear + $i;
                                                $fieldName = "capx_fy_one[]";
                                                $fieldName2 = "capx_fy_two[]";
                                                $transfer = "transfer_budget".($i+1);
                                                $additonal = "additional_budget".($i+1);
                                                $revised = "revised_budget".($i+1);
                                                $capex_without = "capx_fy_one".($i+1);
                                                $year = '20'. $fy.'-'. $fy + 1;
                                                @endphp
                                                <input type="hidden" name="years[]" value="{{$year}}">
                                                <div class="col-12">
                                                    <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20{{ $fy }}-20{{ $fy + 1 }}</h6>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label>CAPEX FY 20{{ $fy }}-20{{ $fy + 1 }} (w/o OH & INT) <span class="mandatory_input">*</span></label>
                                                    <input type="number" class="form-control decimal" id="{{ $capex_without }}" name="capx_fy_one[]" placeholder="Initial Approved Budget" oninput="appendValue('{{$capex_without}}', '{{ $revised }}')">
                                                    <div class="common-error form-text capx_fy_one_error"></div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label>CAPEX FY 20{{ $fy }}-20{{ $fy + 1 }} (with OH & INT) <span class="mandatory_input">*</span></label>
                                                    <input type="number" class="form-control decimal" id="{{ $fieldName2 }}" name="capx_fy_two[]"
                                                        placeholder="Initial Approved Budget">
                                                </div>
                                                <!-- <div class="col-lg-4">
                                                    
                                                    
                                                    <label>Transfer Budget</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                          <button id="plus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised }}', 'plus')">+</button>
                                                        </div>
                                                        <input type="text" class="form-control decimal" id="{{$transfer}}" name="transfer_budget[]"
                                                          placeholder="Transfer Budget" >
                                                        <div class="input-group-append">
                                                          <button id="minus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised}}', 'minus')">-</button>
                                                        </div>
                                                      </div>
                                                   
                                                    
                                                </div> -->
                                                <div class="col-lg-4 mb-2">
                                                    <label>Additional Budget</label>
                                                    <input type="text" class="form-control" id="{{$additonal}}" name="additional_budget[]"
                                                        placeholder="Additional Budget" readonly>
                                                   
                                                    
                                                </div>



                                                <div class="col-lg-4">
                                                    <label>Revised Budget <span class="mandatory_input">*</span></label>
                                                    <input type="text" class="form-control" id="{{$revised}}" name="revised_budget[]"
                                                        placeholder="Revised Budget" readonly>
                                                   
                                                    
                                                </div>
                                         
                                            @endfor
                                     
                                        
                                       
                                    
                                        
                                    </div>

                                    <div class="col-lg-4">
                                        {{-- <label>Reason for change <span class="mandatory_input"></span></label>
                                        <textarea id="remark_capex" name="remark_capex" rows="4" cols="50" value="">
                                        
                                    
                                          </textarea> --}}
                                     </div>

                                        <button id="addMore" class="btn btn-primary" type="button">Add More</button>
                                        <button class="btn btn-danger remove-field" type="button" onclick="removeBudgetField()">Remove</button>
                                

                                   


                            </div>
                            <!--end::Form-->
                            <!-- /.card-body -->
                        </div>

                        <div class="card">

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
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
    <script src="{{ asset('admin/js/capex.js') }}"></script>
    

   <script>
                  var budgetFieldsCount = 3;
                  var fy = <?php echo $endYear + 2; ?>;
                //   var fy = {{ $startYear}} ; 

   document.getElementById("addMore").addEventListener("click", addBudgetField);

   function addBudgetField() {
if (budgetFieldsCount < 10) {
   var budgetFields = document.getElementById("budget-fields");

   var newField = document.createElement("div");
   newField.className = "col-12";

   newField.innerHTML = `
       <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20${fy}-20${fy + 1}</h6>
       <input type="hidden" name="years[]" value="${`20${fy}-${fy + 1}`}">

   `;

                  budgetFields.appendChild(newField);

                  var fieldName = "capx_fy_one[]";
                  var fieldName2 = "capx_fy_two[]";
                  var capex_without = fieldName + (budgetFieldsCount + 1);
                  var transferId = "transfer_" + (budgetFieldsCount + 1);
                  var additionalId = "additional_" + (budgetFieldsCount + 1);
                  var revisedId = "revised_budget" + (budgetFieldsCount + 1);

                  // for (var i = 0; i < 2; i++) {
                      var field = document.createElement("div");
                      field.className = "col-lg-4 col-md-6 col-sm-12";

                      field.innerHTML = `
                          <label>Capex FY 20${fy}-20${fy + 1} (w/o OH & INt) <span class="mandatory_input">*</span></label>
                          <input type="number" class="form-control decimal"  name="${capex_without}" id="${capex_without}"
                          placeholder="Initial Approved Budget" oninput="appendValue('${capex_without}', '${revisedId }')">
                          <div class="common-error form-text capx_fy_one_error"></div>

                      `;

                      budgetFields.appendChild(field);
                  // }
                  // Add with approved budget
                  var withbudget = document.createElement("div");
                  withbudget.className = "col-lg-4 col-md-6 col-sm-12";
                  withbudget.innerHTML = `
                          <label> Capex FY 20${fy}-20${fy + 1}(with OH & INt)<span class="mandatory_input">*</span></label>
                          <input type="number" class="form-control decimal" id="${fieldName2}" name="${fieldName2}"
                          placeholder="Initial Approved Budget">
                          <div class="common-error form-text capx_fy_one_error"></div>

                      `;

                      budgetFields.appendChild(withbudget);

                  // Add Transfer Budget field
                  /*var transferField = document.createElement("div");
                  transferField.className = "col-lg-4 col-md-6 col-sm-12";

                  transferField.innerHTML = `
                        <label>Transfer Budget</label>
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                          <button id="plus" class="btn btn-primary" type="button" onclick="updateValues('${transferId}', '${ revisedId }', 'plus')">+</button>
                                                                        </div>
                                                                        <input type="text" class="form-control decimal" id="${transferId}" name="transfer_budget[]"
                                                                          placeholder="Transfer Budget" >
                                                                        <div class="input-group-append">
                                                                          <button id="minus" class="btn btn-primary" type="button" onclick="updateValues('${transferId}', '${revisedId}', 'minus')">-</button>
                                                                        </div>
                                                                      </div>

                        `;

                  budgetFields.appendChild(transferField);*/

                  // Add Additional Budget field
                  var additionalField = document.createElement("div");
                  additionalField.className = "col-lg-4 col-md-6 col-sm-12";

                  additionalField.innerHTML = `
                      <label>Additional Budget</label>
                      <input type="text" class="form-control" id="${additionalId}" name="additional_budget[]" placeholder="Additional Budget" readonly>
                  `;

                  budgetFields.appendChild(additionalField);

                  // Add Revised Budget field
                  var revisedField = document.createElement("div");
                  revisedField.className = "col-lg-4 col-md-6 col-sm-12";

                  revisedField.innerHTML = `
                      <label>Revised Budget<span class="mandatory_input">*</span></label>
                      <input type="text" value="0" class="form-control" id="${revisedId}" name="revised_budget[]" placeholder=Revised Budget" readonly>
                  `;

                  budgetFields.appendChild(revisedField);

                  budgetFieldsCount++;
                  fy++; // Increment fy for the next budget field
                }
                }


                  function removeBudgetField() {
                      if (budgetFieldsCount > 3) {
                          var budgetFields = document.getElementById("budget-fields");
                          budgetFields.removeChild(budgetFields.lastChild); // Remove the 'h6' element

                          for (var i = 0; i < 5; i++) {
                              budgetFields.removeChild(budgetFields.lastChild); // Remove the input fields
                          }

                          budgetFieldsCount--;
                          fy--;
                      }
                  }


   
    </script>
 <script>
    function appendValue(a,b) {
        var id = a;
        var id2 = b;
        
        var originalInputValue = document.getElementById(id).value;
  
        document.getElementById(id2).value = originalInputValue;
       
    }
  </script>
  
  <script>
    function appendValue1(a, b) {
      var id = a;
      var id2 = b;
  
      var transferInput = document.getElementById(id);
      var revisedInput = document.getElementById(id2);
  
     
      var transfer = parseInt(transferInput.value) || 0;
      var revised = parseInt(revisedInput.value) || 0;
  
      if (!isNaN(transfer) && !isNaN(revised)) {
          var total = transfer + revised;
  
          revisedInput.value = total;
      } 
  }
  function updateValues(a, b, operation) {
    
      var id = a;
      var id2 = b;
  
      var transferInput = document.getElementById(id);
      var revisedInput = document.getElementById(id2);
  
      var transfer = parseInt(transferInput.value) || 0;
      var revised = parseInt(revisedInput.value) || 0;
  
      if (!isNaN(transfer) && !isNaN(revised)) {
          if (operation === 'plus') {
              revisedInput.value = transfer + revised;
          } else if (operation === 'minus') {
            if(revisedInput.value !=  0){
                revisedInput.value = revised - transfer;
            }
            
          }
        }
        revisedInput.value = revisedInput.value;
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
  
      
@endpush
