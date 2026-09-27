@extends('admin.layout.master', ['page_title' => 'View Frequency'])
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">View Frequency</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Frequency</li>
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
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Frequency Details</h3>
                    <br>
                  </div>
<br>
                  <div class="row">
                    <div class="col">
                        <div class="card" style="width: 18rem;margin-left: 17px;">
                            <div class="card-body">
                              <h5 class="card-title">Task Description</h5>
                              <hr style="background-color:black; height:1px; margin-top:25px;">
                              <p class="card-text">{{$data->task_description}}</p>
                            </div>
                          </div>
                    </div>


                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                              <h5 class="card-title">Frequency</h5>
                              <hr style="background-color:black; height:1px; margin-top:25px;">
                              <p class="card-text">{{$data->frequency}}</p>
                            </div>
                          </div>
                    </div>


                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                              <h5 class="card-title">Days /Months</h5>
                              <hr style="background-color:black; height:1px; margin-top:25px;">
                              
                              <p class="card-text">
                                @if(!empty($data->d1))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d2))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d3))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d4))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d5))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d6))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}
                                @elseif(!empty($data->d7))
                                {{$data->d1}} {{$data->d2}} {{$data->d3}} {{$data->d4}} {{$data->d5}}{{$data->d6}} {{$data->d7}}  
                                @elseif(!empty($data->m1))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}} 
                                @elseif(!empty($data->m2))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}  
                                @elseif(!empty($data->m3))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}
                                @elseif(!empty($data->m4))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}  
                                @elseif(!empty($data->m5))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}
                                @elseif(!empty($data->m6))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}
                                @elseif(!empty($data->m7))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}
                                @elseif(!empty($data->m8))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}  
                                @elseif(!empty($data->m9))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}} 
                                @elseif(!empty($data->m10))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}} 
                                @elseif(!empty($data->m11))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}} 
                                @elseif(!empty($data->m12))
                                {{$data->m1}} {{$data->m2}} {{$data->m3}} {{$data->m4}} {{$data->m5}}{{$data->m6}} {{$data->m7}} {{$data->m8}} {{$data->m9}} {{$data->m10}} {{$data->m11}} {{$data->m12}}    
                                @endif
                            </p>
                            </div>
                          </div>
                    </div>

                  </div>
                </div>
                
                
                
              
                <!-- /.card -->
           
          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection
