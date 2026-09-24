@extends('admin.layout.master', ['page_title' => 'Create Sub Location'])
@push('styles')
@endpush

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage OPEX</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">OPEX</li>
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
           
            <form class="form" method="POST" id="create_opex_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New OPEX Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                 
                    <div class="card-body">
                        <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Department <span class="mandatory_input">*</span></label>
                            <select class="form-control " name="super_department" id="super_department">
                                <option value="">Select Super Department </option>
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
                                <label>Expense Head <span class="mandatory_input">*</span></label>
                               
                                <input type="text" class="form-control" id="expenses_head" name="expenses_head" placeholder="Expense Head">
                                <div class="common-error form-text expenses_head_error"></div>
                            </div>
                            <div class="col-lg-4">
                              <label>Activity <span class="mandatory_input">*</span></span></label>
                              <select class="form-control " name="activity" id="activity">
                                  <option value="">Select Activity </option>
                                  <option value="0" >Activity 1</option>
                                  <option value="1" >Activity 2</option>
                                  <option value="2" >Activity 3</option>
                              </select>
                              <div class="common-error form-text activity_error"></div>
                          </div>
                        </div>
                       
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
                       
                        <div id="budget-fields" class="form-group row">
                            @for ($i = 0; $i < 3; $i++)
                            @php
                             
                            $fy = $startYear + $i;
                            $fieldName = "initial_approved_budget" ;
                                  
                            $transfer = "transfer_budget".($i+1);
                            $additonal = "additional_budget".($i+1);
                            $revised = "revised_budget".($i+1);
                            $opex = "initial_approved_budget".($i+1);
                            $year = '20'. $fy.'-'. $fy + 1;
                            @endphp
                            <input type="hidden" name="years[]" value="{{$year}}">
                            <div class="col-12">
                              <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20{{ $fy }}-20{{ $fy + 1 }}</h6>
                          </div>
                                <div class="col-lg-4 budget-field">
                                   
                                    <label>Initial Approved Budget <span class="mandatory_input">*</span></label>
                                    <input type="number" class="form-control decimal" id="{{ $opex }}" name="initial_approved_budget[]" placeholder="Initial Approved Budget" oninput="appendValue('{{$opex}}', '{{ $revised }}')">
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
                              <div class="col-lg-4">
                                  <label>Additional Budget</label>
                                  <input type="text" class="form-control" id="{{$additonal}}" name="additional_budget[]"
                                      placeholder="Additional Budget" readonly>
                                 
                                  
                              </div>



                              <div class="col-lg-4 mb-2">
                                  <label>Revised Budget <span class="mandatory_input">*</span></label>
                                  <input type="text" class="form-control" id="{{$revised}}" name="revised_budget[]"
                                      placeholder="Revised Budget" readonly>
                                 
                                  
                              </div>
                            @endfor
                        </div>

                        <div class="col-lg-4">
                          {{-- <label>Reason for change <span class="mandatory_input"></span></label>
                          <textarea id="remark_add" name="remark_add" rows="4" cols="50">
                     
                            </textarea> --}}
                       </div>

                        <button id="addMore" class="btn btn-primary" type="button" onclick="addBudgetField()">Add More</button>
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

</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/opex.js')}}"></script>

<script>
  var budgetFieldsCount = 3;
  var fy = <?php echo $endYear + 2; ?>;

  function addBudgetField() {
      if (budgetFieldsCount < 10) {
        var budgetFields = document.getElementById("budget-fields");

        var newField1 = document.createElement("div");
        newField1.className = "col-12"; 
        newField1.innerHTML = `
            <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20${fy}-20${fy + 1}</h6>
            <input type="hidden" name="years[]" value="${`20${fy}-${fy + 1}`}">
        `;

        budgetFields.appendChild(newField1);
          var newField = document.createElement("div");
          newField.className = "col-lg-4 budget-field";

          var fieldName = "initial_approved_budget[]";
          var opex = "initial_approved_budget" + (budgetFieldsCount+1);
          var transferId = "transfer_" + (budgetFieldsCount+1);
        var additionalId = "additional_" + (budgetFieldsCount+1);
        var revisedId = "revised_budget" + (budgetFieldsCount+1);

          newField.innerHTML = `
          
              <label>Initial Approved Budget <span class="mandatory_input">*</span></label>
              <input type="number" class="form-control decimal" id="${opex}" name="${fieldName}" placeholder="Initial Approved Budget" oninput="appendValue('${opex}', '${revisedId}')">
          `;

          budgetFields.appendChild(newField);
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
        <label>Revised Budget <span class="mandatory_input">*</span></label>
                                                    <input type="text" class="form-control" id="${revisedId}" name="revised_budget[]"
                                                        placeholder="Revised Budget" readonly>
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

                          for (var i = 0; i < 4; i++) {
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
