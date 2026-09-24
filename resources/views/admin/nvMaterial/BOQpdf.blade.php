<!doctype html>
<html lang="en">

<head>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href='https://fonts.googleapis.com/css?family=Cedarville Cursive' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Shadows Into Light Two' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Courier New' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet'>

    <title>NV Material, Details!</title>

    <style>
        .container-fluid {
            width: 98%;
            margin-left: 1%;
            margin-right: 1%;
            font-family: Arial, sans-serif;
        }
        .h4{
            background-color: rgb(3, 142, 220);
            color: white;
        }
        .container-fluid .fileds p {
            color: gray;

        }

        .container-fluid .fileds {
            margin-top: -5px;
        }

        .implementation-period .to-date {
            background-color: #d3d3d3;
            display: inline;
            color: black;
            font-size: 15px;
            padding: 5px 10px;
            margin-right: 10px;
        }


        .container-fluid .fileds span {
            color: black;
            font-size: 40px;
        }

        .container-fluid .implementation-period .to-date {
            background-color: #d3d3d3;
            display: inline;
            color: black;
            font-size: 16px;
            padding: 5px 10px;
            margin-right: 10px;
        }

        .implementation-period .impl {
            color: black !important;
        }

        .implementation-period .to-date-heading {
            color: black !important;
        }

        .fileds table {
            border-collapse: collapse;
            width: 100%;
        }

        .fileds table td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 2px !important;
            text-align: center;
        }

        .fileds table th {
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .main-heaing table {
            border-collapse: collapse;
            width: 100%;
        }

        .main-heaing table th {
            background-color: rgb(3, 142, 220);
            color: white;
            border: 1px solid rgb(3, 142, 220);
        }

        .main-heaing table .th1 {
            text-align: center;
        }

        .main-heaing table .th2 {
            text-align: right;
        }

        table td,
        th {
            font-size: 9px !important;
        }

        th,
        td {
            padding: 1px !important;
        }

        .padding-text1 p{
          padding: 0px;
          margin: 0px;
          font-size: 9px !important;
        }

        .bottom-heading
        {
                position: absolute;
                bottom: 20px;
                font-size: 12px;
                font-weight: 700;
        }
            body {
                font-family: 'DejaVu Sans' !important;
            }
    </style>

</head>



<body data-responsejs='{"create":[{"prop":"width", "prefix":"min-device-width-", "breakpoints":[0, 320, 481, 641, 767, 961, 1025, 1281] }]}'>

        @php
            $mat_boq = App\Models\MateriBOQBulk::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $ser_boq = App\Models\ServiceBOQBulk::where('nv_id', $id)->orderBy('id', 'desc')->first();
           
        @endphp

        <div class="card" style="page-break-after: always; margin-top:20px;">
      
           

            <br>
            <div class="card-body " style="page-break-after: always;" >
                <!-- <h4 class="text-center"> NV-Material BOQ/Service BOQ Details</h4><br> -->

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
           
                    <h5 class="text-center h4"> Material BOQ</h5>

                    <div class="fileds" style="padding-top:10px">

                   <table style="width:100%;">
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
                                <td class="text-center">{{'Vendor Quotation'}}</td>
                                @elseif($m_import->rate_reference==1)
                                <td class="text-center">{{'Last Work Order'}}</td>
                                @elseif($m_import->rate_reference==2)
                                <td class="text-center">{{'C&M Rate Reference'}}</td>
                                @elseif($m_import->rate_reference==4)
                                <td class="text-center">{{'Rate Reference'}}</td>
                                @else
                                <td class="text-center">{{'User Estimation'}}</td>
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
                                </table>
            </div><br><br>
               

                      @endif
                      @if(!empty($ser_boq->service_code))
                               
                    <h5 class="text-center h4"> Service BOQ</h5>

                                

                    <div class="fileds" style="padding-top:10px">

                   <table style="width:100%;">
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
                           </div>
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
   

</body>



</html>
