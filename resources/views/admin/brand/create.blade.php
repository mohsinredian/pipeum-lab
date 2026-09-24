@extends('admin.layout.master', ['page_title' => 'Create Task'])
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Task</h1>

                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Task</li>
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

                    <form class="form" id="create_brand_form">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">New Task Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <!--begin::Form-->

                            <div class="card-body">
                                <div class="form-group row">

                                    <div class="col-lg-4">
                                        <label>Task Name <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="task_name" id="task_name">
                                            <option value="">Select Task </option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>Task Description <span class="mandatory_input">*</span></label>
                                        <input type="text" class="form-control" id="task_description"
                                            name="task_description" placeholder="Task Description">
                                        <div class="common-error form-text name_error"></div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-8">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label eventLabel">Frequency</label>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-check" style="margin-left: 14px;">
                                                            <label class="form-check-label" for="">
                                                                <input class="checkbox frequency" name="frequency"
                                                                    type="radio" value="daily" id='daily'>
                                                                Daily
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="">
                                                                <input class="checkbox frequency" type="radio"
                                                                    name="frequency" value="weekly" id="weekly">
                                                                Weekly
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <!-- {{-- <div class="col-md-3">
                                            <div class="form-check">
                                                <label class="form-check-label" for="">
                                                    <input class="checkbox frequency" type="radio" 
                                                    name="frequency" value="monthly" id="monthly">
                                                    Monthly
                                                </label>
                                            </div>
                                        </div> --}} -->
                                                </div>
                                                <div class="row dailyoptions" id="dailyoptions">
                                                    <div class="col d-flex">
                                                        <div class="form-check mr-3" style="margin-left: 14px;">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" name="monday" value="monday"
                                                                    type="checkbox">
                                                                Monday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="tuesday"
                                                                    value="tuesday">
                                                                Tuesday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="wednesday"
                                                                    value="wednesday">
                                                                Wednesday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="thursday"
                                                                    value="thursday">
                                                                Thursday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="friday"
                                                                    value="friday">
                                                                Friday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="saturday"
                                                                    value="saturday">
                                                                Saturday
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="sunday"
                                                                    value="sunday">
                                                                Sunday
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row weeklyoptions" id="weeklyoptions"
                                                    style="margin-top: 27px;">
                                                    <div class="col d-flex">
                                                        <div class="form-check mr-3" style="margin-left: 14px;">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="monday"
                                                                    value="monday">
                                                                Monday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="tuesday"
                                                                    value="tuesday">
                                                                Tuesday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="wednesday"
                                                                    value="wednesday">
                                                                Wednesday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="thursday"
                                                                    value="thursday">
                                                                Thursday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="friday"
                                                                    value="friday">
                                                                Friday
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="saturday"
                                                                    value="saturday">
                                                                Saturday
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="sunday"
                                                                    value="sunday">
                                                                Sunday
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row monthlyoptionss" id="monthlyoptionss"
                                                    style="
    margin-top: 27px;
">
                                                    <div class="col d-flex">
                                                        <div class="form-check mr-3" style="margin-left: 14px;">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="january"
                                                                    value="january">
                                                                January
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="february"
                                                                    value="february">
                                                                February
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="march"
                                                                    value="march">
                                                                March
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="april"
                                                                    value="april">
                                                                April
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="may"
                                                                    value="may">
                                                                May
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="june"
                                                                    value="june">
                                                                June
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="july"
                                                                    value="july">
                                                                July
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="august"
                                                                    value="august">
                                                                August
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="september"
                                                                    value="september">
                                                                September
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col d-flex">
                                                        <div class="form-check mr-3" style="margin-left: 14px;">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="october"
                                                                    value="october">
                                                                October
                                                            </label>
                                                        </div>
                                                        <div class="form-check mr-3">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="november"
                                                                    value="november">
                                                                November
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input class="checkbox" type="checkbox" name="december"
                                                                    value="december">
                                                                December
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Company <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="division" id="division">
                                            <option value="">Select Company</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4">
                                        <label>Location <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="location" id="location">
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Role <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="role" id="role">
                                        </select>
                                    </div>

                                    <div class="col-lg-4" style="margin-top:20px;">
                                        <label>Assignee <span class="mandatory_input">*</span></label>
                                        <select class="form-control " name="assignee" id="assignee">
                                        </select>
                                    </div>
                                    <div class="col-lg-4" style="margin-top:20px;">
                                        <label>Start Date <span class="mandatory_input">*</span></label>
                                        <input type="Date" class="form-control" id="start_date" name="start_date"
                                            placeholder="">
                                    </div>

                                    <div class="col-lg-4" style="margin-top:20px;">
                                        <label>End Date <span class="mandatory_input">*</span></label>
                                        <input type="Date" class="form-control" id="end_date" name="end_date"
                                            placeholder="">
                                    </div>


                                    <input type="hidden" class="form-control" id="test_id" name="test_id"
                                        placeholder="">

                                    <div class="col-lg-4" style="margin-top:20px;">
                                        <label>Status <span class="mandatory_input">*</span></label>

                                        <input type="text" class="form-control" id="status" name="status"
                                            placeholder="Task Description" readonly value="New">
                                        <div class="common-error form-text name_error"></div>

                                        </select>
                                        <div class="common-error form-text status_error"></div>
                                    </div>


                                    <div class="row mb-5 col-12" id="floor_plan">
                                        {{-- 

                       <div class="col-2 mb-1">
                              <div class="form-check">
                                  <label class="form-check-label" for="">
                                      <input class="checkbox" type="checkbox" id="monthly"> Basement
                                 </label>
                              </div>
                        </div>
                    
                        <div class="col-2 mb-1">
                            <div class="form-check">
                                <label class="form-check-label" for="">
                                  <input class="checkbox" type="checkbox" id="monthly"> Ground Floor
                                </label>
                             </div>
                        </div>
                    
                        <div class="col-2 mb-1">
                            <div class="form-check">
                                <label class="form-check-label" for="">
                                   <input class="checkbox" type="checkbox" id='daily'> 1st Floor
                               </label>
                           </div>
                        </div>
                    
                       <div class="col-2 mb-1">
                          <div class="form-check">
                             <label class="form-check-label" for="">
                                 <input class="checkbox" type="checkbox" id="weekly"> 2nd Floor
                             </label>
                          </div>
                       </div>
                    
                    
                       <div class="col-2 mb-1">
                          <div class="form-check">
                                <label class="form-check-label" for="">
                                    <input class="checkbox" type="checkbox" id="monthly"> 3rd Floor
                                </label>
                           </div>
                       </div>
                    
                    
                       <div class="col-2 mb-1">
                          <div class="form-check">
                              <label class="form-check-label" for="">
                                 <input class="checkbox" type="checkbox" id="monthly"> 4th Floor
                              </label>
                          </div>
                      </div>
                    
                     <div class="col-2 mb-1">
                         <div class="form-check">
                            <label class="form-check-label" for="">
                                <input class="checkbox" type="checkbox" id="monthly"> 5th Floor
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-2 mb-1">
                        <div class="form-check">
                              <label class="form-check-label" for="">
                                 <input class="checkbox" type="checkbox" id="monthly"> 6th Floor
                              </label>
                        </div>
                    </div> --}}

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
    <script>
        $(document).ready(function() {
            var todaysDate = new Date(); // Get today's date.

            // Get today's date.
            var year = todaysDate.getFullYear(); // yyyy
            var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2); // mm
            var day = ("0" + todaysDate.getDate()).slice(-2); // dd

            var dtToday = (year + "-" + month + "-" + day); // Results in yyyy-mm-dd

            // Now set the max date value for the calendar to be that date.
            $("#start_date").attr('min', dtToday);
            $("#end_date").attr('min', dtToday);
        });
        $(document).ready(function() {
            $('#monthlyoptionss').hide();
            $('#monthly').on('click', function() {
                $('#monthlyoptionss').show();
                $('#weeklyoptions').hide();
                $('#dailyoptions').hide();
            });


            $('#weeklyoptions').hide();
            $('#weekly').on('click', function() {
                $('#weeklyoptions').show();
                $('#monthlyoptionss').hide();
                $('#dailyoptions').hide();
            });



            $('#dailyoptions').hide();
            $('#daily').on('click', function() {
                $('#dailyoptions').show();
                $('#weeklyoptions').hide();
                $('#monthlyoptionss').hide();
            });
        });
    </script>
    <script src="{{ asset('admin/js/brand.js') }}"></script>
@endpush
