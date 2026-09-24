@extends('admin.layout.master', ['page_title' => 'Edit Location'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage OPEX</h1>
          </div><!-- /.col -->
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
           
            <form class="form" id="edit_opex_form">
                @csrf
                <input type="hidden" name="opex_id" value="{{$opex['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit OPEX Details</h3>
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
                                        <option value="{{ $supdeparts->id }}" @if ($supdeparts->id == $opex['super_department']) selected="selected" @endif>{{ $supdeparts->name }}</option>
                                    @endforeach
                                        </select>
                                        <div class="common-error form-text super_department_error"></div>
                                    </div>

                            <div class="col-lg-4">
                                <label> Sub-Department Name <span class="mandatory_input">*</span></label>
                               
                                @php
                                $selectedDepartmentsArray = is_array($selectedDep) ? $selectedDep : [$selectedDep];
                                $selectedDepartmentsString = implode(',', $selectedDepartmentsArray); 
                                $departments_map = is_array($map_dept) ? $map_dept : [$map_dept];
                                @endphp

                                <select class="form-control" name="department_id" id="department_id">
                                @foreach($dept_name as $department)
                                    @php
                                    $isSelected = $department->id != $opex['department_id'];
                                    $Selected = $department->id == $opex['department_id'];
                                    $isExcluded = $isSelected && in_array($department->id, $selectedDepartmentsArray);
                                    @endphp
                                    @unless($isExcluded)
                                        <option value="{{ $department->id }}" {{ $Selected ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endunless
                                @endforeach
                            </select>
                                <div class="common-error form-text division_error"></div>
                            </div>
                            <input type="hidden" value="{{ htmlspecialchars($selectedDepartmentsString) }}" id="deps">
                            <div class="col-lg-4">
                                <label>Expense Head <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="expenses_head" name="expenses_head" placeholder="Location Name" value="{{$opex['expenses_head']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>
                           
                            <div class="col-lg-4">
                                <label>Activity
                                  <span class="mandatory_input">*</span></label>
                                <select id="activity" name="activity" class="form-control">
                                    <option value="" @if ($opex["activity"] == '')  
                                    @endif>Select Status </option>
                                    <option value="0" {{ $opex["activity"] == 0 ? 'selected' : '' }}>Activity 1</option>
                                    <option value="1" {{ $opex["activity"] == 1 ? 'selected' : '' }}>Activity 2</option>
                                    <option value="2" {{ $opex["activity"] == 2 ? 'selected' : '' }}>Activity 3</option>
                                </select>
                                <div class="common-error form-text status_error"></div>
                           </div>
                          </div>

                           {{-- @php
                            $initial_approved_budget = explode(',', $opex  != '' ? $opex ->initial_approved_budget : '');
                           @endphp --}}
{{--                        
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
                           @endphp --}}
                       


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
                           $budgets = old('initial_approved_budget', [
                               $opex->initial_approved_budget
                           ]);
                           $yearwise = json_decode($budgets[0], true);

                           $revised_data = explode(',',$opex->revised_budget);
                           
                           @endphp


                           
                           <div id="budget-fields" class=" form-group row">
                            
                               @if (!empty($yearwise))
                                   @php
                                   $fieldName = "initial_approved_budget";
                                          
                            
                                   @endphp
                                   
                                   @foreach ($yearwise as $fy => $budgetValue)
                                
                                   @if(array_key_exists($fy, $revised_data))
                                   @php
                                   $transfer = "transfer_budget".($fy+1);
                                    $additonal = "additional_budget".($fy+1);
                                    $provision_budget = "provision_budget".($fy+1);
                                    $revised = "revised_budget".($fy+1);
                                    $opexs = "initial_approved_budget".($fy+1);
                                    $year = '20'.$startYear + $fy .'-'.$startYear + ($fy + 1);
                                   
                                    @endphp
                                    <input type="hidden" name="years[]" value="{{$year}}" id="year">
                                    
                                   <div class="col-12">
                                    <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20{{ $startYear + $fy  }}-20{{ $startYear + ($fy+1) }}</h6>
                                </div>
                                       <div class="col-lg-4 budget-fields">
                                           <label>Initial Approved Budget <span class="mandatory_input">*</span></label>
                                          @if(isset($budgetValue))
                                              <input type="text" class="form-control decimal" id="{{ $opexs }}" value="{{ $budgetValue }}" placeholder="Initial Approved Budget" name="initial_approved_budget[]" oninput="appendValue('{{$opexs}}', '{{ $revised }}')" readonly>
                                          @else
                                              <input type="text" class="form-control decimal" id="{{ $opexs }}" value="{{ $budgetValue }}" placeholder="Initial Approved Budget" name="initial_approved_budget[]" oninput="appendValue('{{$opexs}}', '{{ $revised }}')">
                                          @endif
                                       </div>
                                       <div class="col-lg-4">
                                       @php
                                      $creditAdd = App\Models\DummyDepartment::where('transaction_type', 'Credit')->where('transfer_status',1)
                                        ->where('department_id', null)
                                        ->sum('amount');
                                        $creditDep = App\Models\DummyDepartment::where('transaction_type', 'Credit')->where('transfer_status',1)
                                        ->where('department_id','!=', null)
                                        ->sum('amount');
                                        $debitDep = App\Models\DummyDepartment::where('transaction_type', 'Debit')->where('transfer_status',1)
                                        ->where('department_id','!=', null)
                                        ->sum('amount');
                                        $netCredit = ($creditDep + $creditAdd) - $debitDep;
                                        
                                      @endphp
                                      <input type="hidden" value="{{$netCredit}}" id="netCredit">    
                                             
                                        <label>Transfer Budget</label>
                                        
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <button id="plus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised }}', 'plus','{{ $opex['department_id'] }}','{{$year}}','{{$netCredit}}')">+</button>
                                            </div>
                                            @if(isset($transfer_budget[$fy]))
                                            <input type="text" class="form-control decimal" id="{{$transfer}}" name="transfer_budget[]"
                                              placeholder="Transfer Budget"  value="">
                                            @else
                                            <input type="text" class="form-control decimal" id="{{$transfer}}" name="transfer_budget[]"
                                            placeholder="Transfer Budget"  value="">
                                            @endif
                                            <div class="input-group-append">
                                              <button id="minus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised}}', 'minus','{{ $opex['department_id'] }}','{{$year}}','{{$netCredit}}')">-</button>
                                            </div>
                                          </div>
                                          <span style="font-size:15px;color:red;" class="{{$transfer}}"></span>

                                        
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <label>Additional Budget</label>                            
                                        @if(isset($opex_budget_approve[$fy]->additional_budget))
                                        
                                        <input type="text" class="form-control" id="{{$additonal}}" value="{{$opex_budget_approve[$fy]->additional_budget}}" name="additional_budget[]"
                                        placeholder="Additional Budget" readonly>
                                        @else
                                        <input type="text" class="form-control" id="{{$additonal}}" value="" name="additional_budget[]"
                                            placeholder="Additional Budget">
                                        @endif
                                    </div>
      
                                    <div class="col-lg-4 mb-2">
                                      <label>Provision Budget <span class="mandatory_input">*</span></label>
                                      @if(!empty($opex_budget_approve[$fy]->provision_budget));
                                      <input type="text" class="form-control" id="{{$provision_budget}}" name="provision_budget[]"
                                      value="{{$opex_budget_approve[$fy]->provision_budget}}" placeholder="Provision Budget" readonly> 
                                      @else
                                      <input type="text" class="form-control" id="{{$provision_budget}}" name="provision_budget[]"
                                        value="" placeholder="Provision Budget" readonly>
                                    @endif
                                      
                                </div>
    
                                    <div class="col-lg-4">
                                        <label>Revised Budget <span class="mandatory_input">*</span></label>
                                        @if(isset($revised_data[$fy]))
                                        <input type="text" class="form-control" id="{{$revised}}" value="{{$revised_data[$fy]}}" name="revised_budget[]"
                                            placeholder="Revised Budget" readonly>
                                       @else
                                       <input type="text" class="form-control" id="{{$revised}}" value="" name="revised_budget[]"
                                            placeholder="Revised Budget" readonly>
                                        @endif
                                    </div>
                                     @endif
                                   @endforeach
                                   
                               @endif
                              
                           </div>
                           <div class="col-lg-4 mt-2" >
                            <label>Reason for change <span class="mandatory_input"></span></label>
                            <textarea id="remark" name="remark_add" rows="4" cols="50" value="">
                              {{$opex->remark_add ?? ''}}
                        
                              </textarea>
                         </div>
                         <div class="col-lg-4 mb-2">
                                <label>Attachment @if (!empty($opex) && !empty($opex->attachment))
                                                        <a class="ml-2"
                                                            download="{{ $opex->attachment }}"
                                                            href="{{ url(asset('attachment-opex/' . $opex->attachment)) }}"><i
                                                                class="fa fa-download" title="Download"></i></a>
                                                    @endif
                                                  </label>
                              <input type="file" name="attachment" id="attachment" value="" class="form-control">
                             </div>
                           
                         <div class="col-lg-4 mt-4">
                            <button id="addMore" class="btn btn-primary ml-3" type="button" onclick="addBudgetField()">Add More</button>
                            <button class="btn btn-danger remove-field" type="button" onclick="removeBudgetField()">Remove</button>  
                         
                            </div>
                            </div>
                         </div>
                        </div>
                    </div>
                    <div class="card">
                            
                       <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                  <button type="submit" class="btn btn-success ">Submit</button>
                                  <button type="reset" class="btn btn-secondary" onclick="handleCancel('{{$opex['department_id']}}')">Cancel</button>
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

<script src="{{asset('admin/js/opex.js')}}"></script>
<script>
   var budgetFieldsCount = 3;
    <?php $fy = $endYear + ($fy-1); ?>
  var fy = <?php echo $fy + 1; ?>; // Use the initial start year from your PHP code

  function addBudgetField() {
      if (budgetFieldsCount < 10) {
        
        var budgetFields = document.getElementById("budget-fields");

        var newField1 = document.createElement("div");
        newField1.className = "col-12"; 
        newField1.innerHTML = `
            <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20${fy}-20${fy + 1}</h6>
            <input type="hidden" name="years[]" value="${`20${fy}-${fy + 1}`}" id="year${budgetFieldsCount + 1}">
        `;

        budgetFields.appendChild(newField1);
          var newField = document.createElement("div");
          newField.className = "col-lg-4 budget-field";

          var fieldName = "initial_approved_budget[]";
          var opex = fieldName + (budgetFieldsCount+1);
          var transferId = "transfer_" + (budgetFieldsCount+1);
        var additionalId = "additional_" + (budgetFieldsCount+1);
        var provisionId = "provision_" + (budgetFieldsCount + 1);
        var revisedId = opex + (budgetFieldsCount+1);
        var year = $('#year').val();
        var netCredit = $('#netCredit').val();
     
          newField.innerHTML = `
          
              <label>Initial Approved Budget <span class="mandatory_input">*</span></label>
              <input type="number" class="form-control decimal" id="${opex}" name="${fieldName}" placeholder="Initial Approved Budget" oninput="appendValue('${opex}', '${revisedId}')">
          `;

          budgetFields.appendChild(newField);
          // Add Transfer Budget field
          var department_id = $('#department_id').val();
     
    
        var transferField = document.createElement("div");
        transferField.className = "col-lg-4 col-md-6 col-sm-12";

        transferField.innerHTML = `
        <label>Transfer Budget</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                          <button id="plus" class="btn btn-primary" type="button" onclick="updateValues('${transferId}', '${ revisedId }', 'plus','${department_id}','${year}','${netCredit}')">+</button>
                                                        </div>
                                                        <input type="text" class="form-control decimal" id="${transferId}" name="transfer_budget[]"
                                                          placeholder="Transfer Budget" >
                                                        <div class="input-group-append">
                                                          <button id="minus" class="btn btn-primary" type="button" onclick="updateValues('${transferId}', '${revisedId}', 'minus','${department_id}','${year}','${netCredit}')">-</button>
                                                        </div>
                                                      </div>

        `;
        budgetFields.appendChild(transferField);
          // Add Additional Budget field
          var additionalField = document.createElement("div");
        additionalField.className = "col-lg-4 col-md-6 col-sm-12";

        additionalField.innerHTML = `
            <label>Additional Budget</label>
            <input type="text" class="form-control" id="${additionalId}" name="additional_budget[]" placeholder="Additional Budget" readonly>
        `;

        budgetFields.appendChild(additionalField);

         // Add provision Budget field
         var provisionField = document.createElement("div");
                  provisionField.className = "col-lg-4 col-md-6 col-sm-12";

                  provisionField.innerHTML = `
                      <label>Provision Budget</label>
                      <input type="text" class="form-control" id="${provisionId}" name="provision_budget[]" placeholder="provision Budget" readonly>
                  `;
            budgetFields.appendChild(provisionField);

        // Add Revised Budget field
        var revisedField = document.createElement("div");
        revisedField.className = "col-lg-4 col-md-6 col-sm-12";

        revisedField.innerHTML = `
        <label>Revised Budget <span class="mandatory_input">*</span></label>
                                                    <input type="text" class="form-control" id="${revisedId}" name="revised_budget[]" value=""
                                                        placeholder="Revised Budget" readonly >
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

    function appendValue(a,b) {
        var id = a;
        var id2 = b;
      
        var originalInputValue = document.getElementById(id).value;
  
        document.getElementById(id2).value = originalInputValue;
       
    }
  
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
  function updateValues(a, b, operation,department_id,year,netCredit) {
    
      var id = a;
      var id2 = b;
  
      var transferInput = document.getElementById(id);
      var revisedInput = document.getElementById(id2);
  
      var transfer = parseInt(transferInput.value) || 0;
      var revised = parseInt(revisedInput.value) || 0;
      var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      if (!isNaN(transfer) ) {
          if (operation === 'plus') {
            if (netCredit != null && netCredit >= transfer) {
                revisedInput.value = transfer + revised;
            }
          } else if (operation === 'minus') {
            if(!isNaN(revised) && revisedInput.value >= transfer){
                revisedInput.value = revised - transfer;
            }
          }
          $.ajax({
            type: 'POST',
            url: '/admin/opex/update-dummy-department', 
            data: {
              transfer: transfer,
              operation:operation,
              afterTransfer:revisedInput.value,
              revised:revised,
              department_id:department_id,
              year:year,
              _token: csrfToken 
            },
            success: function(response) {
              if(response.status == 'false'){
                console.log(response.message);
                $('.' + id).html(response.message);
              }else{
                $('#' + id).val('');
                $('.' + id).html('');
              }
            },
      
            error: function(error) {
          
        }
        });
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

<script>
function handleCancel(department_id) {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Retrieve all year values
    var yearInputs = document.querySelectorAll('input[name="years[]"]');
    var years = Array.from(yearInputs).map(input => input.value);
    
    $.ajax({
        type: 'POST',
        url: '/admin/opex/cancel-dummy-department', 
        data: {
            department_id: department_id,
            years: years,
            _token: csrfToken 
        },
        success: function(response) {
            if(response.status === 'success') {
                history.back(); // Navigate back in history
            } else {
                console.log(response.message);
            }
        },
        error: function(error) {
            console.log('Error:', error);
        }
    });
}
</script>

@endpush
