@extends('admin.layout.master', ['page_title' => 'Edit Serices'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage  Department</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active"> Department</li>
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
            
            <form class="form" id="edit_supdepartment_form">
                @csrf
                <input type="hidden" name="department_id" value="{{$supdepart['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit Department Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">

                            <div class="col-lg-4">
                                <label> Department Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Service Name" value="{{$supdepart['name']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>

                            <div class="col-7">
                              <label> Sub-Department  <span class="mandatory_input">*</span></label>
                              <select class="form-control select2_element abilities_select2_dropdown"
                                  name="departments[]" multiple="multiple" style="width:100%">
                                  @php
                              $selectedDepartmentsArray = is_array($selectedDepartments) ? $selectedDepartments : [$selectedDepartments];
                              @endphp
                              @foreach($departments as $department)
                                  <option value="{{ $department->id }}" @if(in_array($department->id, $selectedDepartmentsArray)) selected="selected" @endif>{{ $department->name }}</option>
                              @endforeach
                              </select>
                              <div class="common-error form-text abilities_error"></div>
                          </div>
                            
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $supdepart["status"] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $supdepart["status"] == 0 ? 'selected' : '' }}>Inactive</option>
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
<script src="{{ asset('theme/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{asset('admin/js/supdept.js')}}"></script>

@endpush