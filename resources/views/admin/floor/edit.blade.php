@extends('admin.layout.master', ['page_title' => 'Edit Floor'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Floor</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Locations</li>
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
            
            <form class="form" id="edit_floor_form">
                @csrf
                <input type="hidden" name="floor_id" value="{{$floor['id']}}">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Floor Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Location Name<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="location_id">
                                    <option value="">Select Company </option>
                                    @foreach ($location as $locations)
                                        <option value="{{ $locations->id }}" @if ($locations->id == $floor['location_id']) selected="selected" @endif>{{ $locations->name }}</option>
                                    @endforeach
                                </select>
                                <div class="common-error form-text division_error"></div>
                            </div>
                            <div class="col-lg-4">
                                <label>Floor Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Floor Name" value="{{$floor['floor_name']}}">
                                <div class="common-error form-text name_error"></div>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $floor["status"] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $floor["status"] == 0 ? 'selected' : '' }}>Inactive</option>
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
                              <button type="submit" class="btn btn-success mr-2">Submit</button>
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

<script src="{{asset('admin/js/floor.js')}}"></script>

@endpush