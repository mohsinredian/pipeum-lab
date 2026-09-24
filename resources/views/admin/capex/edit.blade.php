@extends('admin.layout.master', ['page_title' => 'Edit Serices'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage CAPEX</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
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
            
            <form class="form" id="edit_capex_form">
                @csrf
                <input type="hidden" name="capexlist_id" value="{{$capexlist['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit CAPEX Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                 
                    <div class="card-body">
                        <div class="form-group row">

                          <div class="col-lg-4">
                                        <label> Department <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="super_department" id="super_department">
                                            <option value="">Select Department </option>
                                            @foreach ($supdepart as $supdeparts)
                                        <option value="{{ $supdeparts->id }}" @if ($supdeparts->id == $capexlist['super_department']) selected="selected" @endif>{{ $supdeparts->name }}</option>
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
                                    $isSelected = $department->id != $capexlist['department_id'];
                                    $Selected = $department->id == $capexlist['department_id'];
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
                            <label>Head <span class="mandatory_input">*</span></label>
                            <select id="head" name="head" class="form-control">
                                <option value="">Select Head</option>
                                <option value="0" {{ $capexlist["head"] == 0 ? 'selected' : '' }}>Load Growth</option>
                                <option value="1" {{ $capexlist["head"] == 1 ? 'selected' : '' }}>System Improvement</option>
                                <option value="2" {{ $capexlist["head"] == 2 ? 'selected' : '' }}>Statutory Requiremt</option>
                                <option value="3" {{ $capexlist["head"] == 3 ? 'selected' : '' }}>Infrastructure</option>
                                <option value="4" {{ $capexlist["head"] == 4 ? 'selected' : '' }}>Technology</option>
                                <option value="5" {{ $capexlist["head"] == 5 ? 'selected' : '' }}>Deposit</option>
                                <option value="6" {{ $capexlist["head"] == 6 ? 'selected' : '' }}>Overheads & Interest</option>
                            </select>
                            <div class="common-error form-text status_error"></div>
                          </div>

                            <div class="col-lg-4">
                                <label>Sub Head <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="sub_head" name="sub_head" placeholder="Service Name" value="{{$capexlist['sub_head']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>

                            <div class="col-lg-4">
                              <label>BPR Head <span class="mandatory_input">*</span></label>
                              <select id="brp_head" name="brp_head" class="form-control">
                                  <option value="">Select Head</option>
                                  <option value="0" {{ $capexlist["brp_head"] == 0 ? 'selected' : '' }}>Performance Obligation</option>
                                  <option value="1" {{ $capexlist["brp_head"] == 1 ? 'selected' : '' }}>Power Reliability</option>
                                  <option value="2" {{ $capexlist["brp_head"] == 2 ? 'selected' : '' }}>Infrastructure Development</option>
                              </select>
                              <div class="common-error form-text status_error"></div>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Regular CAPEX/Project CAPEX <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0" {{ $capexlist["status"] == 0 ? 'selected' : '' }}>Regular</option>
                                    <option value="1" {{ $capexlist["status"] == 1 ? 'selected' : '' }}>Project</option>
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

                          {{-- @for ($i = 0; $i < $numFields; $i++) --}}
                          @php
                         
                          $fieldName = "capx_fy_one[]"  ;
                          $fieldName2 = "capx_fy_two[]"  ;
                         

                          $budgets_one = [
                                $capexlist->capx_fy_one,
                                
                            ];
                         
                            $yearwise = json_decode($budgets_one[0], true); // Decode as an associative array


                            // if (is_array($yearwise) && array_key_exists($i, $yearwise)) {
                            //     $budgetValue = $yearwise[$i];
                            // } else {
                            //     $budgetValue = '';
                            // }

                          $budgets_two = [
                              $capexlist->capx_fy_two,
                             
                          ];
                          $transferBudgetString = $capexlist->transfer_budget; 
                          if($transferBudgetString == null)
                          {
                            $transferBudgetArray = [0,0,0];
                          }else
                          {
                            $transferBudgetArray = explode(',', $transferBudgetString);
                          }
                          $additionalBudgetString = $capexlist->additional_budget;
                          if($additionalBudgetString == null)
                          {
                            $additionalBudgetArray = [0,0,0];
                          }else
                          {
                            $additionalBudgetArray = explode(',', $additionalBudgetString);
                          }

                          $revisedBudgetString = $capexlist->revised_budget; 
                          if($revisedBudgetString == null)
                          {
                            $revisedBudgetArray = [0,0,0];
                          }else
                          {
                            $revisedBudgetArray = explode(',', $revisedBudgetString);
                          }

                          $yearwise2 = json_decode($budgets_two[0], true); // Decode as an associative array


                          // if (is_array($yearwise2) && array_key_exists($i, $yearwise2)) {
                          //     $budgetValue2 = $yearwise2[$i];
                          // } else {
                          //     $budgetValue2 = '';
                          // }
                          @endphp
                         {{-- @dd($capex_budget_approve) --}}
                           @foreach($yearwise as $fy => $budgetValue)
                           {{-- @dd($capex_budget_approve[$fy]) --}}
                           @if(array_key_exists($fy, $yearwise))
                           @php
                              $budgetValue2 = $yearwise[$fy];
                              $transfer = "transfer_budget".($fy+1);
                          $additonal = "additional_budget".($fy+1);
                          $provision_budget = "provision_budget".($fy+1);
                          $revised1 = "revised_budget".($fy+1);
                          $capex_without = "capx_fy_one".($fy+1);
                          $year = '20'.$startYear + $fy .'-'.$startYear + ($fy + 1);
                            @endphp
                            <input type="hidden" name="years[]" value="{{$year}}" id="year">
                          <div class="col-12 ">
                              <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY  20{{$startYear + $fy }}-20{{$startYear + ($fy + 1) }}</h6>
                          </div>
                          <div class="col-lg-4 col-md-6 col-sm-12">
                            <label>Capex FY {{ $startYear + $fy }}-{{$startYear + ($fy + 1) }} (w/o OH & INt) <span class="mandatory_input">*</span></label>
                            @if(isset($budgetValue))
                              <input type="number" class="form-control decimal" id="{{ $capex_without }}" value="{{ $budgetValue }}" name="capx_fy_one[]" placeholder="Initial Approved Budget" oninput="appendValue('{{$capex_without}}', '{{ $revised1 }}')" readonly >
                            @else
                              <input type="number" class="form-control decimal" id="{{ $capex_without }}" value="" name="capx_fy_one[]" placeholder="Initial Approved Budget" oninput="appendValue('{{$capex_without}}', '{{ $revised1 }}')" >
                            @endif
                            <div class="common-error form-text capx_fy_one_error"></div>
                        </div>
                
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <label>Capex FY {{$startYear + $fy }}-{{$startYear + ($fy + 1) }} (with OH & INt) <span class="mandatory_input">*</span></label>
                            <input type="number" class="form-control decimal" id="{{ $fieldName2 }}" value="{{ $yearwise2[0] }}" name="capx_fy_two[]" placeholder="Initial Approved Budget" readonly>
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
                            <label>Tranfer Budget</label>
                         
                              <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <button id="plus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised1 }}', 'plus','{{ $capexlist['department_id'] }}','{{$year}}','{{$netCredit}}')">+</button>
                                </div>
                                <input type="text" class="form-control decimal" id="{{$transfer}}" name="transfer_budget[]"
                                value=""  placeholder="Transfer Budget" >
                                <div class="input-group-append">
                                  <button id="minus" class="btn btn-primary" type="button" onclick="updateValues('{{$transfer}}', '{{ $revised1 }}', 'minus','{{ $capexlist['department_id'] }}','{{$year}}','{{$netCredit}}')">-</button>
                                </div>
                              </div>
                              <span style="font-size:15px;color:red;" class="{{$transfer}}"></span>

                        </div>
                        <div class="col-lg-4 mb-2">
                            <label>Additional Budget <span class="mandatory_input">*</span></label>
                            {{-- @dd($capex_budget_approve[$fy]->additional_budget) --}}
                            {{-- @if(isset($additionalBudgetArray[$fy])) --}}
                            @if(!empty($capex_budget_approve[$fy]->additional_budget));
                            <input type="text" class="form-control" id="{{$additonal}}" name="additional_budget[]"
                            value="{{$capex_budget_approve[$fy]->additional_budget}}" placeholder="Additional Budget" readonly> 
                            @else
                            <input type="text" class="form-control" id="{{$additonal}}" name="additional_budget[]"
                               value="" placeholder="Additional Budget" readonly>
                           @endif
                            
                        </div>

                        <div class="col-lg-4 mb-2">
                            <label>Provision Budget <span class="mandatory_input">*</span></label>
                            @if(!empty($capex_budget_approve[$fy]->provision_budget));
                            <input type="text" class="form-control" id="{{$provision_budget}}" name="provision_budget[]"
                            value="{{$capex_budget_approve[$fy]->provision_budget}}" placeholder="Provision Budget" readonly> 
                            @else
                            <input type="text" class="form-control" id="{{$provision_budget}}" name="provision_budget[]"
                               value="" placeholder="Provision Budget" readonly>
                           @endif
                            
                        </div>
                    
                        <div class="col-lg-4 mb-2">
                            <label>Revised Budget <span class="mandatory_input">*</span></label>
                            @if(isset($revisedBudgetArray[$fy]))
                            <input type="text" class="form-control" id="{{$revised1}}" name="revised_budget[]"
                              value="{{$revisedBudgetArray[$fy] }}"  placeholder="Revised Budget" readonly>
                          @else
                            <input type="text" class="form-control" id="{{$revised1}}" name="revised_budget[]"
                              value=""  placeholder="Revised Budget" readonly>
                            
                            @endif
                        </div>
                          @endif
                          @endforeach
                  
                          </div>
                          
                          <div class="col-lg-4">
                            <label>Reason for change <span class="mandatory_input"></span></label>
                            <textarea id="remark_capex" name="remark_capex" rows="4" cols="50" value="">
                              {{$capexlist['remark_capex']??''}}
                        
                              </textarea>
                         </div>
                         <div class="col-lg-4 mb-2">
                            <label>Attachment @if (!empty($capexlist) && !empty($capexlist->attachment))
                                                    <a class="ml-2"
                                                        download="{{ $capexlist->attachment }}"
                                                        href="{{ url(asset('attachment-capex/' . $capexlist->attachment)) }}"><i
                                                            class="fa fa-download" title="Download"></i></a>
                                                @endif
                                              </label>
                          <input type="file" name="attachment" id="attachment" value="" class="form-control">
                         </div>
                         <div class="col-lg-4 mt-4">
                          <button id="addMore" class="btn btn-primary" type="button">Add More</button>
                          <button class="btn btn-danger remove-field" type="button" onclick="removeBudgetField()">Remove</button>
                          </div>
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
                              <button type="submit" class="btn btn-success">Submit</button>
                              <button type="reset" class="btn btn-secondary" onclick="handleCancel('{{$capexlist['department_id']}}')">Cancel</button>
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

<script src="{{asset('admin/js/capex.js')}}"></script>

                <script>
                  var budgetFieldsCount = 3;
                  <?php $fy = $endYear + ($fy-1); ?>
                var fy = <?php echo $fy + 1; ?>; // Use the initial start year from your PHP code

                  document.getElementById("addMore").addEventListener("click", addBudgetField);

                  function addBudgetField() {
                if (budgetFieldsCount < 10) {
                  var budgetFields = document.getElementById("budget-fields");

                  var newField = document.createElement("div");
                  newField.className = "col-12";

                  newField.innerHTML = `
                      <h6 style="background-color: rgb(3, 142, 220); color: white; padding: 5px;">FY 20${fy}-20${fy + 1}</h6>
                      <input type="hidden" name="years[]" value="${`20${fy}-${fy + 1}`}" id="year${budgetFieldsCount + 1}">
                  `;

                  budgetFields.appendChild(newField);

                  var fieldName = "capx_fy_one[]";
                  var fieldName2 = "capx_fy_two[]";
                  var capex_without = fieldName + (budgetFieldsCount + 1);
                  var transferId = "transfer_" + (budgetFieldsCount + 1);
                  var additionalId = "additional_" + (budgetFieldsCount + 1);
                  var provisionId = "provision_" + (budgetFieldsCount + 1);
                  var revisedId = capex_without + (budgetFieldsCount + 1);
                  var department_id = $('#department_id').val();
                  var year = $('#year').val();
                  var netCredit = $('#netCredit').val();

                  // for (var i = 0; i < 2; i++) {
                      var field = document.createElement("div");
                      field.className = "col-lg-4 col-md-6 col-sm-12";

                      field.innerHTML = `
                          <label>Capex ${fy}-${fy + 1} (w/o OH & INt) <span class="mandatory_input">*</span></label>
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
                          <label> Capex ${fy}-${fy + 1}(with OH & INt) <span class="mandatory_input">*</span></label>
                          <input type="number" class="form-control decimal" id="${fieldName2}" name="${fieldName2}"
                          placeholder="Initial Approved Budget" readonly>
                          <div class="common-error form-text capx_fy_one_error"></div>

                      `;

                      budgetFields.appendChild(withbudget);

                  // Add Transfer Budget field
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
                      <input type="text" value="0" class="form-control" id="${revisedId}" name="revised_budget[]" placeholder="Revised Budget" readonly>
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

                  function appendValue(a,b) {
                      var id = a;
                      var id2 = b;
                    
                      var value = document.getElementById(id).value;
                  
                      document.getElementById(id2).value = value;
                    
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
                    if (!isNaN(transfer)) {
                      
                      if (operation === 'plus') {
                        if (netCredit != null && netCredit >= transfer) {
                            revisedInput.value = transfer + revised;
                        }
                        } else if (operation === 'minus') {
                          if(!isNaN(revised) && revisedInput.value >= transfer){
                              revisedInput.value = revised - transfer;
                          }
                        }
                      }
                     $.ajax({
                        type: 'POST',
                        url: '/admin/capexmaster/update-dummy-department', 
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
        url: '/admin/capexmaster/cancel-dummy-department', 
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