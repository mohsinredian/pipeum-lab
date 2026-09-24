<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>NV Material BOQ/Service BOQ, Details!</title>
    <style>
        @page {
            /* size: A4 landscape !important;
         margin: 2cm; */
            size: landscape;
            margin:1cm;
            
        }

        header {
            position: fixed;
            top: -40px;
            left: -40px;
            right: -40px;
            background-color: #65737e;
            color: white;
            text-align: center;
         
        }


        header table td{
            border: none !important;
        }


        footer {
            position: fixed;
            bottom: -37.5px;
            left: -40px;
            right: -40px;
            height: 25px;

            /** Extra personal styles **/
            background-color: #65737e;
            color: white;
            text-align: center;
           
        }

        #watermark {
            position: fixed;

            /**
                    Set a position in the page for your image
                    This should center it vertically
                **/
            bottom: 10cm;
            left: 5.5cm;

            /** Change image dimensions**/
            width: 8cm;
            height: 8cm;

            /** Your watermark should be behind every content**/
            z-index: 1;
        }

        @media screen {

            /* Styles for screen display */
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 16px;
            }
        }

        @media print {

            /* Styles for print */
            body {
                font-family: Arial, sans-serif;
                padding: 0;
            }

            h1 {
                font-size: 32px;
            }

            p {
                font-size: 18px;
            }
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            word-wrap: break-word;
        }

        .table th,
        .table td {
            font-size: 12px;
            white-space: normal;
        }

        .table td {
            padding: 2px 5px 2px 5px !important;
            vertical-align: middle !important;
        }

        #watermark img {
            /* transform: rotate(-45deg); */
            margin: 0;
            position: absolute;
            top: 50%;
           left: -30%;
            width: 400px;
            height: auto;
            filter: grayscale(100%) !important; 
        }

        .card{
            margin-top: 30px;
            border: 2px solid gray;
        }
        body {
        margin-top: 40px; /* Adjust this value as per your requirements */
    }
    
    </style>


</head>

<body>
    @php
            $id = Request::segment(3);
            $user = \Auth()->user();
            $nv_data = App\Models\NeedValidation::where('id', $id)->first();
            $data = App\Models\NVMaterial::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_date = App\Models\NVMaterial::select('created_at')->where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_by = App\Models\User::select('name','id')->where('id', $data->user_id)->first();
          
    @endphp
    <header>
        <table>
            <tr>
                <td class="text-center" style="width: 33.3%;color:white; font-weight: bold;"><img src="{{ public_path('theme/dist/img/pdf-logo.png') }}" alt="logo"><br> BSES Rajdhani Power Limited </td>

                <!-- <td class="text-center" style="width: 33.3%;"> <h6 style="color:white; font-weight: bold; font-size:15px;">{{'NV' . '/' . $nv_data->budget_type . '/' . $nv_data->fiscal_year . '/' . getDepartmentNameByPro($nv_data->user_id) . '/' . $nv_data->service->name}}</h6> </td> -->

            <!-- <p>Initiated By: {{$initiated_by->name}}<br>Initiated Date: {{ date('d-M-y h:i A', strtotime($initiated_date->created_at)) }}</p> -->
            
                    <td class="text-center" style="width: 33.3%;">
                        <!-- <h6 style="color:white; font-weight: bold;">BSES Rajdhani Power Limited 
                        BSES Bhawan Nehru Place Power Limited</h6> -->
                    </td>
            </tr>
        </table>
    </header>
    

    <footer>
        Copyright &copy; <?php echo date('Y'); ?> BSES. All Rights Reserved. 
    </footer>
    <div id="watermark">
        <img src="{{ public_path('theme/dist/img/watermark.png') }}" alt="Ashu">
    </div>
    <main>
    <h6 style="color:black; font-weight: bold; font-size:15px;text-align:center;">{{'NV' . '/' . $nv_data->budget_type . '/' . $nv_data->fiscal_year . '/' . getDepartmentNameByPro($nv_data->user_id) . '/' . $nv_data->service->name}}</h6>
        @php
            $id = Request::segment(3);
            $user = \Auth()->user();
            $data = App\Models\NVMaterial::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $mat_boq = App\Models\MateriBOQBulk::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $ser_boq = App\Models\ServiceBOQBulk::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->exists();
            if ($Nvsericestatus) {
                $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
            } else {
                $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
            }
            $employees_data = App\Models\Employee::where('user_id', $user->id)->first();
            $department = App\Models\Department::where('id', $employees_data->department_id)->first();
        @endphp

        <div class="card" style="page-break-after: always; margin-top:20px;">
      
            {{-- <div class="card-header">
                <section class="content responsive-columns">
                    <nav class="navbar navbar-expand-lg navbar-light text-center">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">
                                <img src="{{ public_path('theme/dist/img/logo.png') }}" alt="BSES"
                                    class="brand-image img-circle2" style="opacity: .8">
                            </a>

                            <div class="navbar-text text-content-end  d-flex flex-column align-items-end">
                                <div>
                                    <h5>BSES Rajdhani Power Limited</h5>
                                    <h5>BSES Bhawan Nehru Place Power Limited</h5>
                                    <h5>New Delhi - 110019</h5>
                                    <h4 style="color: rgb(3, 142, 220); font-weight: bold;">Need Validation Management
                                        System</h4>
                                </div>
                            </div>
                        </div>
                    </nav>
                </section>
            </div> --}}

            <br>
            <div class="card-body " style="page-break-after: always;" >
                <h4 class="text-center"> NV-Material BOQ/Service BOQ Details</h4><br>

                <input type="hidden" name="implements_years" value="{{$material_details->implements_years}}">
             @php 
             $selectedYear = $material_details != '' ? $material_details->implements_years : '';
             $from = explode(',', $material_details != '' ? $material_details->imp_from : '');
            
            if($from){
                $year = trim($from[0]);
                $fyear = date('Y', strtotime($year));
            
             }else{
                $year = $selectedYear;
             }
                @endphp

                 @if(!empty($mat_boq->material_code))
           
                <div>
                    <h5 style="background-color: rgb(3, 142, 220);color:white;padding:5px;"> <b> Material BOQ </b></h5>
                </div><br>

                <table id="nv_datatable" class="table table-bordered" style="width: 100%;">
                                <thead class="text-center">
                                <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-center">Mat Code</th>
                                <th class="text-center">UOM</th>
                                <th class="text-center">Desc.</th>
                                <th class="text-center">Rate</th>
                                <th class="text-center">Qty.</th>
                                <th class="text-center">Amt.</th>
                               <th class="text-center">Rate Ref</th>
                               <th class="text-center">FY</th>
                                <th class="text-center">Apr</th>
                                <th class="text-center">May</th>
                                <th class="text-center">Jun</th>
                                <th class="text-center">July</th>
                                <th class="text-center">Aug</th>
                                <th class="text-center">Sept</th>
                                <th class="text-center">Oct</th>
                                <th class="text-center">Nov</th>
                                <th class="text-center">Dec</th>
                                <th class="text-center">Jan</th>
                                <th class="text-center">Feb</th>
                                <th class="text-center">Mar</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php $total = 0; ?>
                            @foreach ($material_import as $key => $m_import)
                            
                                <tr>
                                <td class="text-center">{{ ++$key }}</td>
                                <td class="text-left">{{ $m_import->material_code }}</td>
                                <td class="text-left">{{ $m_import->uom }}</td>
                                <td class="text-left">{!! nl2br(e($m_import->material_short_text)) !!}</td>

                                <td class="text-center">{{ number_format($m_import->rate , 2) }}</td>
                                <td class="text-center">{{ number_format($m_import->quantity, 2) }}</td>
                                
                                <td class="text-center">{{ number_format($m_import->amount, 2) }}</td>

                                @if($m_import->rate_reference==0)
                                <td class="text-center">{{'C&M'}}</td>
                                @elseif($m_import->rate_reference==1)
                                <td class="text-center">{{'Last Purchase Price'}}</td>
                               @else
                                <td class="text-center">{{'Vendor Quotation'}}</td>
                                @endif
                              
                                        @if($material_details->implements_years==1)
                                         @if($year)
                                            <td class="text-center">{{$fyear}}-{{$fyear+1}}</td>
                                            @else <td class="text-center">{{ $selectedYear}}</td>
                                            @endif
                                        @elseif($material_details->implements_years==2)
                                        @if($year)
                                            <td class="text-center">{{$fyear}}-{{$fyear+1}}
                                            <hr>{{$fyear+1}}-{{$fyear+2}}
                                            </td>
                                            @else <td class="text-center">{{ $selectedYear}}</td>
                                            @endif
                                           @elseif($material_details->implements_years==3)
                                           @if($year)
                                            <td class="text-center">{{$fyear}}-{{$fyear+1}}
                                            <hr>{{$fyear+1}}-{{$fyear+2}}
                                            <hr>{{$fyear+2}}-{{$fyear+3}}
                                            </td>
                                            @else <td class="text-center">{{ $selectedYear}}</td>
                                            @endif
                                            @elseif($material_details->implements_years==null)
                                            <td class="text-center">{{''}}</td>
                                        @endif


                                @if($material_details->implements_years==1)
                              <td class="text-center">{{ $m_import->april1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->may1 ?? 0 }}</td>
                              <td class="text-center">{{ $m_import->june1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->july1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->august1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->september1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->oct1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->nov1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->dec1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->jan1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->feb1 ?? 0}}</td>
                              <td class="text-center">{{ $m_import->march1 ?? 0}}</td>

                              @elseif($material_details->implements_years==2)
                              <td class="text-center">{{ $m_import->april1 ?? 0}}
                                <hr>{{ $m_import->april2 ?? 0 }}</td>
                                <td class="text-center">{{ $m_import->may1 ?? 0 }}
                                <hr>{{ $m_import->may2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->june1 ?? 0}}
                                <hr> {{ $m_import->june2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->july1 ?? 0}}
                                <hr>{{ $m_import->july2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->august1 ?? 0}}
                                <hr>{{ $m_import->august2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->september1 ?? 0}}
                                <hr>{{ $m_import->september2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->oct1 ?? 0}}
                                <hr>{{ $m_import->oct2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->nov1 ?? 0}}
                                <hr>{{ $m_import->nov2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->dec1 ?? 0}}
                                <hr>{{ $m_import->dec2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->jan1 ?? 0}}
                                <hr>{{ $m_import->jan2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->feb1 ?? 0}}
                                <hr>{{ $m_import->feb2 ?? 0}}</td>
                                <td class="text-center">{{ $m_import->march1 ?? 0}}
                                <hr>{{ $m_import->march2 ?? 0}}</td>
                               

                             @elseif($material_details->implements_years==3)
                              <td class="text-center">{{ $m_import->april1 ?? 0}}
                                <hr>{{ $m_import->april2 ?? 0 }}
                                <hr>{{ $m_import->april3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->may1 ?? 0 }}
                                <hr>{{ $m_import->may2 ?? 0}}
                                <hr>{{ $m_import->may3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->june1 ?? 0}}
                                <hr> {{ $m_import->june2 ?? 0}}
                                <hr>{{ $m_import->june3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->july1 ?? 0}}
                                <hr>{{ $m_import->july2 ?? 0}}
                                <hr>{{ $m_import->july3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->august1 ?? 0}}
                                <hr>{{ $m_import->august2 ?? 0}}
                                <hr>{{ $m_import->august3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->september1 ?? 0}}
                                <hr>{{ $m_import->september2 ?? 0}}
                                <hr>{{ $m_import->september3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->oct1 ?? 0}}
                                <hr>{{ $m_import->oct2 ?? 0}}
                                <hr>{{ $m_import->oct3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->nov1 ?? 0}}
                                <hr>{{ $m_import->nov2 ?? 0}}
                                <hr>{{ $m_import->nov3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->dec1 ?? 0}}
                                <hr>{{ $m_import->dec2 ?? 0}}
                                <hr>{{ $m_import->dec3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->jan1 ?? 0}}
                                <hr>{{ $m_import->jan2 ?? 0}}
                                <hr>{{ $m_import->jan3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->feb1 ?? 0}}
                                <hr>{{ $m_import->feb2 ?? 0}}
                                <hr>{{ $m_import->feb3 ?? 0}}
                                </td>
                                <td class="text-center">{{ $m_import->march1 ?? 0}}
                                <hr>{{ $m_import->march2 ?? 0}}
                                <hr>{{ $m_import->march3 ?? 0}}
                                </td>
                                @elseif($material_details->implements_years==0)
                                <td class="text-center">{{ $m_import->april1 ?? ''}}</td>
                              <td class="text-center">{{ $m_import->may1 ?? ''}}</td>
                              <td class="text-center">{{ $m_import->june1 ?? ''}}</td>
                              <td class="text-center">{{ $m_import->july1 ?? ''}}</td>
                              <td class="text-center">{{ $m_import->august1 ??''}}</td>
                              <td class="text-center">{{ $m_import->september1 ??''}}</td>
                              <td class="text-center">{{ $m_import->oct1 ??''}}</td>
                              <td class="text-center">{{ $m_import->nov1 ??''}}</td>
                              <td class="text-center">{{ $m_import->dec1 ??''}}</td>
                              <td class="text-center">{{ $m_import->jan1 ??''}}</td>
                              <td class="text-center">{{ $m_import->feb1 ??''}}</td>
                              <td class="text-center">{{ $m_import->march1 ??''}}</td>
                              @endif
                             </tr>
                                 @endforeach
                                </tbody>
                                </table><br><br>
               

                      @endif
                      @if(!empty($ser_boq->service_code))
                                
                                <div>
                    <h5 style="background-color: rgb(3, 142, 220);color:white;padding:5px;"> <b> Service BOQ </b></h5>
                </div><br>
                                

                                <table id="nv_datatable" class="table table-bordered">
                                <thead class="text-center">
                                <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-center">Service Code</th>
                                <th class="text-center">Service Description</th>
                                <th class="text-center">UOM</th>
                                <th class="text-center">Rate</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Amount (Rs.)</th>

                                 </tr>
                                </thead>
                                <tbody>
                            @foreach ($service_import as $key => $s_import)
                              
                                <tr>
                                <td class="text-center">{{ ++$key }}</td>
                                <td class="text-left">{{ $s_import->service_code }}</td>
                                <td class="text-left">{!! nl2br(e($s_import->description)) !!}</td>
                                <td class="text-left">{{ $s_import->uom }}</td>
                                <td class="text-center">{{ number_format($s_import->rate , 2) }}</td>
                                <td class="text-center">{{ number_format($s_import->qty , 2) }}</td>
                                <td class="text-center">{{ number_format($s_import->amount , 2) }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                                </table>
                         
                        @endif
                 
                  
           <table>
            <tr>
                <td>
                    <footer class="main-footer text-center" style="color:rgb(3, 142, 220); font-size: 12px;">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <strong>&copy;{{ date('Y') }} <a href="#" target="_blank"
                                            style="color:rgb(3, 142, 220);">BSES</a>. All Rights Reserved.</strong>
                                </div>
                            </div>
                        </div>
                    </footer>

                </td>
            </tr>


        </table>

    </main>


    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</body>

</html>
