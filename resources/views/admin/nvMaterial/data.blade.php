<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <link href='https://fonts.googleapis.com/css?family=Cedarville Cursive' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Shadows Into Light Two' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Courier New' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet'>
    <title>NV Material, Details!</title>
    <style>
        @page {
            /* size: portrait; */
            size: landscape;

        }

        header {
            position: fixed;
            top: -40px;
            left: -40px;
            right: -40px;
            color: white;
            text-align: center;
        }


        header table td{
            border: none !important;
        }

        header img{
            width: 200px !important;
            height: auto;
        }


        footer {
            position: fixed;
            bottom: -15px;
            left: -40px;
            right: -40px;
            height: 25px;
        }

        footer table td{
            border: none !important;
        }

        #watermark {
            position: fixed;
            bottom: 10cm;
            left: 5.5cm;
            width: 8cm;
            height: 8cm;
            z-index: 1;
        }

        @media screen {
            body {
                font-family: 'DejaVu Sans' !important;
                /* font-family: Arial, sans-serif; */
                padding: 10px;
            }

            h1 {
                font-size: 15px;
            }

            p {
                font-size: 12px !important;
            }
        }

        @media print {
            body {
                font-family: Arial, sans-serif;
                padding: 0;
            }

            h1 {
                font-size: 15px;
            }

            p {
                font-size: 12px !important;
            }
        }

        .table {
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .table th,
        .table td {
            padding: 5px;
            word-wrap: break-word;
        }

        .table th,
        .table td {
            font-size: 12px !important;
            white-space: normal;
           
        }

        .table td {
            padding: 1px 3px 1px 3px !important;
        }

        #watermark img {
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
        }
        body {
        margin-top: 40px; 
    }

    
    .just-prop-content{
                     color:#666666; 
                     font-family: Arial, sans-serif!important;
                    }
    .card-body p span{color:#666666; font-family: Arial, sans-serif;}
    h6{background-color: rgb(3, 142, 220);color:white; padding:3px 5px; margin-top:20px; display:block;}
    .sign-box{display:block;  clear:both; margin-bottom:5px;}
  
    .sign-box span { margin-right:5px; margin-left:5px; display:inline-block;}

    .fileds table {
            border-collapse: collapse;
            width: 100%;
        }

        .fileds table td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            text-align: center;
        }

        .fileds table th {
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .h4{
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .table4{
            border-collapse: separate ;
        }

        .table4 td{
           font-size:12px !important;
        }

        .fileds span{
            font-size:10px !important;
        }

        .signature-t th{
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .signature-t{
            margin-top:6px;
        }

        td{
           word-break: break-word !important;
        }

    </style>

</head>

<body>
    @php
            // $id = Request::segment(3);
            $user = \Auth()->user();
            $nv_data = App\Models\NeedValidation::where('id', $id)->first();
            $data = App\Models\NVMaterial::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_date = App\Models\NVMaterial::select('created_at')->where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_by = App\Models\User::select('name','id')->where('id', $data->user_id)->first();
          
    @endphp
    <header>
        <table >
            <tr>
                <td style="width: 33.3%;"><img style="margin-left:50px" src="{{ public_path('theme/dist/img/pdf-logo.png') }}" alt="logo"></td>
                
                <td> <p style="font-size: 17px !important; font-weight: bold;color:black; margin-top:17px; margin-left:230px;">BSES Rajdhani Power Limited </p></td>
            </tr>
        </table>
    </header>
    

    <footer>
        <table style="border:none;">
            <tr >
                <!-- <td>
                <span style="color:black; font-size:10px; margin-left:50px;">{{'NV' . '/' . $nv_data->budget_type . '/' . $nv_data->fiscal_year . '/' . getDepartmentNameByPro($nv_data->user_id) . '/' . $nv_data->service->name}}</span>
                </td> -->
                {{-- <td >
                    <p style="margin-left:450px;" > 1/20 </p>
                 </td> --}}
            </tr>
        </table>
    </footer>

    <!-- <div id="watermark">
        <img src="{{ public_path('theme/dist/img/watermark.png') }}" alt="Ashu">
    </div> -->

    <main>
        @php
            // $id = Request::segment(3);
            $user = \Auth()->user();
            $data = App\Models\NVMaterial::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->exists();
            if ($Nvsericestatus) {
                $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
            } else {
                $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
            }
 
            $employees_data = App\Models\Employee::where('user_id', $user->id)->first();
        
        @endphp

        <div>
      

        {{-- <div class="card-body" style="page-break-after: always;"> --}}
            <h5 class="text-center h4"> NV-Material Details <span style="float: right">Annexure 2a</span></h5>

            <table style="width:100%; paddding:0px; margin:0px; border:1px solid black;" class="table4">
                <tr style="  border:1px solid black;">
                    <td style="width:50%;  border:1px solid black;" colspan="2">  
                        
                            <span><b>Department Name :- </b>
                                @if (!empty($material_details->dept_id))
                                <span> {{ $material_details->department->name }} </span>
                                @else
                                <span>  </span>
                                @endif
                            </span>
                    
                    </td>

                    <td style="width:50%;  border:1px solid black;">
                                <span><b>DOP Reference Number :- </b>
                                    @if (!empty($material_details->dop))
                                        <span> {{ $material_details != '' ? $material_details['dop'] : '' }} </span>
                                    @else
                                    <span> </span>
                                    @endif
                                </span>
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                            <span><b>Proposal Name :- </b>
                                @if (!empty($material_details->proposal_name))
                                    <span>{{ $material_details != '' ? $material_details['proposal_name'] : '' }}</span>
                                @else
                                    <span>  </span>
                                @endif
                            </span>
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                            <span style="font-size:12px;"><b>Background:-</b>
                                @if (!empty($material_details->background))
                                {!! ($material_details->background) !!}
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                            {{-- <span style="font-size:10px;">{!! $material_details['background'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                            </span>
                    
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                        
                            <span style="font-size:12px;"> <b>Broad Justification:-</b>
                                @if (!empty($material_details->broad_just))
                                {!! ($material_details->broad_just) !!}
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                                {{-- <span style="font-size:10px;"> {!! $material_details['broad_just'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                    
                    </td>
                </tr>
                
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                        
                            <span style="font-size:12px;"> <b>Detailed Justification:-</b>
                                @if (!empty($material_details->just_Prop))
                                {!! ($material_details->just_Prop) !!} 
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                                {{-- <span style="font-size:10px;"> {!! $material_details['just_Prop'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                    
                    </td>
                </tr>

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold;text-align:center;">
                        Past 3 Years actual cost trend Material
                    </td>
                </tr>
                    
       

                @php
                $year1 = explode(',', $material_details != '' ? $material_details->cost_trend_year1 : '');
                $year2 = explode(',', $material_details != '' ? $material_details->cost_trend_year2 : '');
                $year3 = explode(',', $material_details != '' ? $material_details->cost_trend_year3 : '');
                $year = $nv_year->fiscal_year;
                $yr_l = substr($year, 5);
                $yr_f = substr($year, 0, 4);
                @endphp

                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 1 }}-{{ $yr_l - 1 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($material_details->cost_trend_year1))
                                        <span>{{ isset($year1[0]) ? $year1[0] : '' }}</span>
                                        {{-- <span>{{ isset($year1[1]) ? $year1[1] : '' }}</span>
                                        <span>{{ isset($year1[2]) ? $year1[2] : '' }}</span> --}}
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($material_details->cost_trend_year2))
                                    <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span>
                                    {{-- <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                                    <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span> --}}
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>
                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 2 }}-{{ $yr_l - 2 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($material_details->cost_trend_year1))
                                        {{-- <span>{{ isset($year1[0]) ? $year1[0] : '' }}</span> --}}
                                        <span>{{ isset($year1[1]) ? $year1[1] : '' }}</span>
                                        {{-- <span>{{ isset($year1[2]) ? $year1[2] : '' }}</span> --}}
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($material_details->cost_trend_year2))
                                    {{-- <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span> --}}
                                    <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                                    {{-- <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span> --}}
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>
                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 3 }}-{{ $yr_l - 3 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($material_details->cost_trend_year1))
                                        {{-- <span>{{ isset($year1[0]) ? $year1[0] : '' }}</span>
                                        <span>{{ isset($year1[1]) ? $year1[1] : '' }}</span> --}}
                                        <span>{{ isset($year1[2]) ? $year1[2] : '' }}</span>
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($material_details->cost_trend_year2))
                                    {{-- <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span>
                                    <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span> --}}
                                    <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span>
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>


                @php
                $from = explode(',', $material_details != '' ? $material_details->imp_from : '');
                $to = explode(',', $material_details != '' ? $material_details->imp_to : '');
                $service = explode('.,', $material_details != '' ? $material_details->imp_plan : '');
                $selectedYear = $material_details != '' ? $material_details->implements_years : '';

                @endphp

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                        <span class="table-box">
                            <span style="color:black;"><b>Implementation Period From :- </b></span>
                            @if (!empty($material_details->imp_from))
                                <span>{{date('d-m-Y', strtotime(isset($from[0]) ? $from[0] : ''))}}</span>
                                {{-- <span>{{(isset($from[1]) ? $from[1] : '') }}</span>
                                <span>{{(isset($from[2]) ? $from[2] : '') }}</span> --}}
                            @else
                                <span>  </span>
                            @endif
                        </span>
                </td>
                    <td style="width:50%;border:1px solid black;">
                        <span class="table-box">
                        <span style="color:black;"><b>Implementation Period To :- </b></span>

                        @if (!empty($material_details->imp_to))
                            @if($selectedYear == 1 )
                                <span>{{date('d-m-Y', strtotime(isset($to[0]) ? $to[0] : ''))}}</span>
                            @elseif($selectedYear == 2) 
                                <span>{{date('d-m-Y', strtotime(isset($to[1]) ? $to[1] : '' ))}}</span>
                            @else
                                <span>{{date('d-m-Y', strtotime(isset($to[2]) ? $to[2] : '' ))}}</span>
                            @endif

                            {{-- 
                                <span>{{(isset($to[1]) ? $to[1] : '' ) }}</span> --}}
                               
                        @else
                            <span>  </span>
                        @endif
                    </span>
                </td>
                   

                    
                </tr>  
                
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <span class="table-box">
                            @if (!empty($material_details->imp_plan))
                            @if(!empty($service[0]))<span><p><b>Implementation Plan Year Wise 1</b> :- {{ substr(isset($service[0]) ? $service[0] : '' , 0, 500) }}</span>@endif
                            @if(!empty($service[1]))<span><p><b>Implementation Plan Year Wise 2</b> :-{{ substr(isset($service[1]) ? $service[1] : '', 0, 500) }}</span>@endif
                            @if(!empty($service[2]))<span><p><b>Implementation Plan Year Wise 3</b> :-{{ substr(isset($service[2]) ? $service[2] : '' , 0, 500) }}</span>@endif
                            @else
                            <span style="color:black;"><b>Implementation Plan Year Wise :-  </b> </span>
                        @endif
                        </span>
                    </td>
                </tr>


                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <b>Benefit :- </b>
                            @if (!empty($material_details->benefit))
                            <span> {{ $material_details != '' ? $material_details['benefit'] : '' }}</span>
                            @else
                            <span>  </span>
                            @endif
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <b>Nature of Work :- </b>
                            @if (!empty($material_details->worktype))
                            <span>{{ $material_details != '' ? $material_details['worktype'] : '' }}</span>
                            @else
                            <span>  </span>
                            @endif
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <b>Mode of Award :- </b>
                            @if (!empty($material_details->mode_award))
                            <span>{{ $material_details != '' ? $material_details['mode_award'] : '' }}</span>
                            @else
                            <span>  </span>
                            @endif
                    </td>
                </tr>

               
                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                        Scheme
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td  style="border:1px solid black;" colspan="2"> 
                    <b>Scheme Type / Category :- </b>
                    @if (!empty($material_details->scheme_type))
                        <span>
                            {{ $material_details != '' ? $material_details['scheme_type'] : '' }}</span>
                    @else
                        <span>  </span>
                    @endif
                    </td>
                    <td  style="border:1px solid black;" colspan="2"> 
                        <b>Scheme No :- </b>
                        @if (!empty($material_details->scheme_no))
                            <span>
                                {{ $material_details != '' ? $material_details['scheme_no'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                        
                </tr>
                <tr style="border:1px solid black;">
                    <td  style="border:1px solid black;" colspan="6"> 
                        <b>Scheme Description :- </b>
                        @if (!empty($material_details->scheme_des))
                            <span>
                                {{ $material_details != '' ? $material_details['scheme_des'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                </tr>   

                

                @if ( $nv_data->budget_type === 'CAPEX')

                    
                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                          DERC
                      </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                        <b>DERC Proposal Number :- </b>
                        @if (!empty($material_details->prop_number))
                            <span>{{ $material_details != '' ? $material_details['prop_number'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>DERC Reference No :- </b>
                            @if (!empty($material_details->derc_ref_no))
                                <span>{{ $material_details != '' ? $material_details['derc_ref_no'] : '' }}</span>
                            @else
                                <span>  </span>
                            @endif
                            </td>
                    </tr>

                    

                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                        <b>DERC Approval Status :- </b>
                        @if (!empty($material_details->derc_approval))
                            <span>
                                {{ $material_details != '' ? $material_details['derc_approval'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>DERC Approval Date :- </b>
                            @if (!empty($material_details->derc_app_date))
                                <span>{{\Carbon\Carbon::parse($material_details != '' ? $material_details['derc_app_date'] : '')->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                            </td>
                    </tr>

                   
                @endif

                    <?php
                    $total = 0;
                    $m_importAmount = 0;
                    $totalvalue = $total;
                    ?>

                    @foreach ($material_import as $key => $m_import)
                        <?php
                        $m_importAmount =  $m_import->amount;
                        ?>

                        @if (!empty($m_importAmount))
                            <?php
                            
                            $total = $total + $m_importAmount;
                            
                            ?>
                        @else
                            <?php
                            $total = $total;
                            ?>
                        @endif
                    @endforeach

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                        Material BOQ
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td style="border:1px solid black;" colspan="1">
                        <b>Material BOQ Amount :- </b>
                        @php
                     
                        $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                        $wordss = $fmt->format(round($total));

                        $formatted_budget = 0;
                        if (strlen(round($total)) >= 8) {
                            $crore = $total / 10000000;
                            $croress = floor($crore * 100) / 100;
                            $crores = floor($crore);
                            $remaining = ($total - ($crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            
                            if ($lakhs > 0) {
                                $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $words = convertNumberToWords($crores) . ' Crores';
                            }
                            $formatted_budget = number_format($croress, 2) . '  Cr';
                        }else{
                            $words = convertNumberToWords($total). '  Rupees';
                            $formatted_budget = number_format($total, 2) . '  Rs.';
                        }
                        @endphp
                        @php
                        $Imp_year = explode('-',$nv_data->fiscal_year);
                        $Imp = $nv_data->fiscal_year;
                        $mat_imp = App\Models\MateriBOQBulk::where('nv_id', $nv_data->id)->where('material_id', $material_details->id)->orderBy('id', 'desc')->get(); 
                        $totalAmt1 = 0;
                        $totalAmt2 = 0;
                        $totalAmt3 = 0;
                        foreach ($mat_imp as $key => $m_imp){
                            $rate = $m_imp->rate;
                            $months = [
                                $m_imp->april1, $m_imp->may1, $m_imp->june1, $m_imp->july1, $m_imp->august1, $m_imp->september1,
                                $m_imp->oct1, $m_imp->nov1, $m_imp->dec1, $m_imp->jan1, $m_imp->feb1, $m_imp->march1
                            ];
                            foreach ($months as $value) {
                            $totalAmt1 += $value*$rate;
                            }  
                            $months2 = [
                                $m_imp->april2, $m_imp->may2, $m_imp->june2, $m_imp->july2, $m_imp->august2, $m_imp->september2,
                                $m_imp->oct2, $m_imp->nov2, $m_imp->dec2, $m_imp->jan2, $m_imp->feb2, $m_imp->march2
                            ];
                           foreach ($months2 as $value2) {
                            $totalAmt2 += $value2*$rate;
                            }
                            $months3 = [
                                $m_imp->april3, $m_imp->may3, $m_imp->june3, $m_imp->july3, $m_imp->august3, $m_imp->september3,
                                $m_imp->oct3, $m_imp->nov3, $m_imp->dec3, $m_imp->jan3, $m_imp->feb3, $m_imp->march3
                            ];
                        
                            foreach ($months3 as $value3) {
                            $totalAmt3 += $value3*$rate;
                            }
                        }
                        $totalAmt1_budget = 0;
                        if (strlen(round($totalAmt1)) >= 8) {
                            $totalAmt1_crore = $totalAmt1 / 10000000;
                            $totalAmt1_croress = floor($totalAmt1_crore * 100) / 100;
                            $totalAmt1_crores = floor($totalAmt1_crore);
                            $remaining = ($totalAmt1 - ($totalAmt1_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt1_words = convertNumberToWords($totalAmt1_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt1_words = convertNumberToWords($totalAmt1_crores) . ' Crores';
                            }
                            $totalAmt1_budget = number_format($totalAmt1_croress, 2) . '  Cr';
                        }else{
                            $totalAmt1_words = convertNumberToWords($totalAmt1). '  Rupees';
                            $totalAmt1_budget = number_format($totalAmt1, 2) . '  Rs.';
                        }
                        $totalAmt2_budget = 0;
                        if (strlen(round($totalAmt2)) >= 8) {
                            $totalAmt2_crore = $totalAmt2 / 10000000;
                            $totalAmt2_croress = floor($totalAmt2_crore * 100) / 100;
                            $totalAmt2_crores = floor($totalAmt2_crore);
                            $remaining = ($totalAmt2 - ($totalAmt2_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt2_words = convertNumberToWords($totalAmt2_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt2_words = convertNumberToWords($totalAmt2_crores) . ' Crores';
                            }
                            $totalAmt2_budget = number_format($totalAmt2_croress, 2) . '  Cr';
                        }else{
                            $totalAmt2_words = convertNumberToWords($totalAmt2). '  Rupees';
                            $totalAmt2_budget = number_format($totalAmt2, 2) . '  Rs.';
                        }
                        $totalAmt3_budget = 0;
                        if (strlen(round($totalAmt3)) >= 8) {
                            $totalAmt3_crore = $totalAmt3 / 10000000;
                            $totalAmt3_croress = floor($totalAmt3_crore * 100) / 100;
                            $totalAmt3_crores = floor($totalAmt3_crore);
                            $remaining = ($totalAmt3 - ($totalAmt3_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt3_words = convertNumberToWords($totalAmt3_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt3_words = convertNumberToWords($totalAmt3_crores) . ' Crores';
                            }
                            $totalAmt3_budget = number_format($totalAmt3_croress, 2) . '  Cr';
                        }else{
                            $totalAmt3_words = convertNumberToWords($totalAmt3). '  Rupees';
                            $totalAmt3_budget = number_format($totalAmt3, 2) . '  Rs.';
                        }
                        @endphp
                        @if($material_details->implements_years == 3)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+1}}-{{$Imp_year[1]+1}} :</b> {{$totalAmt2_budget ?? null}}<span>({{ucwords($totalAmt2_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+2}}-{{$Imp_year[1]+2}} :</b> {{$totalAmt3_budget ?? null}}<span>({{ucwords($totalAmt3_words)}})</span>
                        @elseif($material_details->implements_years == 2)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+1}}-{{$Imp_year[1]+1}} :</b> {{$totalAmt2_budget ?? null}}<span>({{ucwords($totalAmt2_words)}})</span>
                        @elseif($material_details->implements_years == 1)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        @else
                        <span>{{$formatted_budget ?? null}}</span><br>
                         <span>({{ucwords($words)}})</span>
                         @endif
                    </td>

                    <td style="border:1px solid black;">
                        @if($nv_data->budgetary_provision=="Approved")
                            <b>Budget Available :- </b>
                                @if (!empty($material_details->budget_avl))
                                @php
                                $budget_avl = $material_details->budget_avl;
                                $formatted_budget = 0;

                                if (strlen(round($budget_avl)) >= 8) {
                                    $crore = $budget_avl / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($budget_avl - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }

                                    $formatted_budget = number_format($croress, 2) . ' Cr';
                                } else {
                                    $words = convertNumberToWords($budget_avl) . ' Rupees';
                                    $formatted_budget = number_format($budget_avl, 2) . ' Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span><br>
                                <span>({{ucwords($words)}})</span>
                            @else
                            <span>  </span>
                            
                            @endif
                            {{-- <span>{{"one thousands"}}</span> --}}
                        @endif
                    </td>
                    <td style="border:1px solid black;" colspan="1"> 
                        <b>Material BOQ Attachment </b>
                       
                        <a class="ml-2" download="{{ asset('materials-doc/MaterialBOQ.xls') }}" href="{{ asset('materials-doc/MaterialBOQ.xls') }}"><i class="fa fa-download" title="Download"></i>MaterialBOQ.xls</a>
                           
                        </td>

                </tr>  

                   @php
                   $material_amount = array_map('floatval', explode(',', $material_details != '' ? $material_details->material_amount : '0'));
                   $material_descripition = explode(',', $material_details != ''?$material_details->material_description : '');
                    $ttal = 0;
                    @endphp


                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;">
                        <b>Amount :- </b>
                    
                    @if (!empty($material_details->material_amount))
                    @foreach ($material_amount as $key => $material_amt)
                    @php
                    $formatted_budget = 0;
                    if (strlen(round($material_amt)) >= 8) {
                        $crore = $material_amt / 10000000;
                        $crores = floor($crore * 100) / 100;
                        $formatted_budget = number_format($crores, 2) . '  Cr';
                    }else{
                        $formatted_budget = number_format($material_amt, 2) . '  Rs.';
                    }
                    @endphp
                        <span style="margin-right:5px;padding:2px;">{{$formatted_budget ?? '' }}</span>
                    @endforeach
                    @else
                        <span>  </span>
                    @endif
                    
                   {{-- <span>({{($number_letter)}})</span>--}}
                    
                        </td>

                        <td style="width:50%;border:1px solid black;">
                        {{-- <b>Description :- </b> --}}
                        @if (!empty($material_details->material_description))
                        @foreach ($material_descripition as $key => $material_des)
                            <span style="margin-right:5px;padding:2px;">{{ $material_details != '' ? $material_des : '' }}</span>
                            @endforeach
                        @else
                            <span> <strong>Description :-</strong> </span>
                        @endif
                        </td>
                        <td style="width:50%;border:1px solid black;" colspan="2">
                            <b>Total Material Amount  :- </b>
                            @php
                            
                          if (!empty($material_details->material_amount)){
                            foreach ($material_amount as $key => $material_amt){
                                $total += $material_amt;
                             }
                            }else{
                                $total = $total;
                            }
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($total));

                            $formatted_budget = 0;
                            if (strlen(round($total)) >= 8) {
                                $crore = $total / 10000000;
                                $croress = floor($crore * 100) / 100;
                                $crores = floor($crore);
                                    $remaining = ($total - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                $formatted_budget = number_format($croress, 2) . '  Cr';
                            }else{
                                $words = convertNumberToWords($total). '  Rupees';
                                $formatted_budget = number_format($total, 2) . '  Rs.';
                            }
                            @endphp
                                <span> {{$formatted_budget ?? ''}} </span><br>
                                <span>({{ucwords($words)}})</span>
                            
                            </td>

                </tr>  

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                        Rate Reference
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                        <b>C&M Rate Reference :- </b>
                            @if (!empty($material_doc->cm_rate_ref))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['cm_rate_ref'])) }}">{{ $material_doc->cm_rate_ref }}</a>
                            @else
                                <span>  </span>
                        @endif
                    </td>

                    <td style="width:50%;border:1px solid black;">
                        <b>Vendor Quotation :- </b>
                            @if (!empty($material_doc->vend_quatation))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['vend_quatation'])) }}">{{ $material_doc->vend_quatation }}</a>
                            @else
                                <span>  </span>
                            @endif
                    </td>

                </tr>  

                   
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                    <b>Last Purchase Price  :- </b>
                    @if (!empty($material_doc->last_purchase_price))
                    <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['last_purchase_price'])) }}">{{ $material_doc->last_purchase_price }}</a>
                    @else
                        <span>  </span>
                    @endif
                    </td>

                    <td style="width:50%;border:1px solid black;">
                    <b>User Estimation :- </b>
                    @if (!empty($material_doc->user_estimation))
                    <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['user_estimation'])) }}">{{ $material_doc->user_estimation }}</a>
                    @else
                        <span>  </span>
                    @endif
                    </td>
                </tr>  

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Previous WO/RC (if any) :- </b>
                        @if (!empty($material_doc->previous_wo_rc))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['previous_wo_rc'])) }}">{{ $material_doc->previous_wo_rc }}</a>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                </tr>
                
                
               

                @if ( $nv_data->budget_type === 'CAPEX')


                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                      Is There Any Capacity Addition:-  @if (!empty($material_details->cap_add))@if ($material_details->cap_add == 4)Yes @elseif ($material_details->cap_add == 5)No @endif @else @endif
                      </td>
                    </tr>

                    @if ($material_details->cap_add == 4)

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>PTR MVA :- </b>
                        @if (!empty($material_details->ptr_mva))
                            <span>{{ $material_details != '' ? $material_details['ptr_mva'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>DT MVA :- </b>
                        @if (!empty($material_details->dt_mva))
                            <span>{{ $material_details != '' ? $material_details['dt_mva'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>EHV Line(Ckt.km) :- </b>
                        @if (!empty($material_details->ehv_line))
                            <span>{{ $material_details != '' ? $material_details['ehv_line'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>HT Line(Ckt.km) :- </b>
                        @if (!empty($material_details->ht_line))
                            <span>{{ $material_details != '' ? $material_details['ht_line'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>LT Line(Ckt.km) :- </b>
                        @if (!empty($material_details->lt_line))
                            <span>{{ $material_details != '' ? $material_details['lt_line'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>

                    @endif

                @endif

                

                

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                             Attachments
                      </td>
                </tr>

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Copy Of Previous Work Order/Purchase Order :- </b>
                        @if (!empty($material_doc->previous_work_order))
                        <a class="ml-2" download="{{ $material_doc->previous_work_order }}" href="{{ url(asset('materials-doc/' . $material_doc->previous_work_order)) }}"><i class="fa fa-download" title="Download"></i>{{ $material_doc->previous_work_order }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Copy Of DERC/Other Stakeholder Approvals :- </b>
                            @if (!empty($material_doc->derc_stakeholder_approvals))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['derc_stakeholder_approvals'])) }}">{{ $material_doc->derc_stakeholder_approvals }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Consumption Details - Last 3 Year :- </b>
                        @if (!empty($material_doc->consumption_details))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['consumption_details'])) }}">{{ $material_doc->consumption_details }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Technical Specifications :- </b>
                            @if (!empty($material_doc->vendor_quatation))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['vendor_quatation'])) }}">{{ $material_doc->vendor_quatation }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Photographs Of Product :- </b>
                        @if (!empty($material_doc->photo_product))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['photo_product'])) }}">{{ $material_doc->photo_product }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Material Procurement :- </b>
                            @if (!empty($material_doc->material_procurement))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['material_procurement'])) }}">{{ $material_doc->material_procurement }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Budget Statement For Both OPEX/CAPEX Activities :- </b>
                        @if (!empty($material_doc->budget_for_both))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['budget_for_both'])) }}">{{ $material_doc->budget_for_both }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Others :- </b><br>
                            @if (!empty($material_doc->others))
                            @php
                                $images = explode(',', $material_doc->others);
                            @endphp
                                @foreach($images as $image)
                                    <a href="{{ url(asset('materials-doc/' . $image)) }}" target="_blank" class="ml-2">
                                        {{ $image }}
                                    </a><br>
                                @endforeach
                            @else
                                <span>N/A</span>
                            @endif
                            </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>New Product :- </b>
                            @if (!empty($material_doc->new_product))
                            <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['new_product'])) }}">{{ $material_doc->new_product }}</a>
                            @else
                                <span>  </span>
                        @endif
                        <br>
                        <span>{{ $material_details != '' ? $material_details->new_product_text : '' }}</span>
                    </td>

                   

                </tr> 
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Quantity Justification :- </b>
                            @if (!empty($material_doc->quant_just))
                            <a class="ml-2" href="{{ url(asset('materials-doc/' . $material_doc['quant_just'])) }}">{{ $material_doc->quant_just }}</a>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                            <span>{{ $material_details != '' ? $material_details->quant_just_text : '' }}</span>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Root Cause Analysis :- </b>
                        @if (!empty($material_doc->root_cause_analysis))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['root_cause_analysis'])) }}">{{ $material_doc->root_cause_analysis }}</a>
                        @else
                            <span>  </span>
                        @endif
                        <span>{{ $material_details != '' ? $material_details->root_cause_analysis : '' }}</span>
                    </td>

                    

                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Cost Reduction :- </b>
                        @if (!empty($material_doc->cause_analysis))
                        <a class="ml-2" target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['cause_analysis'])) }}">{{ $material_doc->cause_analysis }}</a>
                        @else
                            <span>  </span>
                        @endif
                            <span>{{ $material_details != '' ? $material_details->cause_analysis : '' }}</span>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Budget Calculation :- </b>
                         @if (!empty($material_details->approved_budget) && !empty($material_details->add_budget))
                    @php
                        $approved_budget = $material_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (strlen(round($approved_budget)) >= 8) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = number_format($approved_budget, 2) . '  Rs.';
                        }

                        $add_budget = $material_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (strlen(round($add_budget)) >= 8) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = number_format($add_budget, 2) . '  Rs.';
                        }
                      
                    @endphp
                        

                    <span style="color:black;">
                    Appr : {{$formatted_budget_appr}} , Addl : {{$formatted_budget_addl}}</span>
                    @elseif(!empty($material_details->approved_budget) && empty($material_details->add_budget))
                    @php
                    $approved_budget = $material_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (strlen(round($approved_budget)) >= 8) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = number_format($approved_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Appr : {{$formatted_budget_appr}}
                    @elseif(empty($material_details->approved_budget) && !empty($material_details->add_budget))
                    @php
                    $add_budget = $material_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (strlen(round($add_budget)) >= 8) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = number_format($add_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Addl : {{$formatted_budget_addl}}
                    @else
                        <span></span>
                    @endif
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Special Remarks :- </b>
                        @if (!empty($material_doc->special_attch))
                            <a class="ml-2" download target="_blank" href="{{ url(asset('materials-doc/' . $material_doc['special_attch'])) }}">{{ $material_doc->special_attch }}</a>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                            <span>{{ $material_details != '' ? $material_details->special_remarks : '' }}</span>
                          
                      
                    </td>

                   

                </tr>
                
                
                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220); padding-top:20px;">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                      Are There Any Services Related To This NV :-  @if (!empty($material_details->ser_rel_nv))@if ($material_details->ser_rel_nv == 2)Yes @elseif ($material_details->ser_rel_nv == 3)No @endif @else @endif
                      </td>
                </tr>

                @if ($material_details->ser_rel_nv == 2)
                    @php
                        $year1 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost_fy : '');
                        $year2 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost : '');
                        $year3 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost_service : '');
                        $year = $nv_year->fiscal_year;
                        $yr_l = substr($year, 5);
                        $yr_f = substr($year, 0, 4);
                    @endphp

                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                        <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                            Past 3 Years actual cost trend Services
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span style="color:black;"><b>Year :- </b></span>
                            @if (!empty($material_details->past_3_year_actual_cost_fy))
                            <span>{{ $yr_f - 1 }}-{{ $yr_l - 1 }}</span>
                            {{-- <span>{{ $yr_f - 2 }}-{{ $yr_l - 2 }}</span>
                            <span>{{ $yr_f - 3 }}-{{ $yr_l - 3 }}</span> --}}
                            @else
                            <span>  </span>
                            @endif
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                        <span style="color:black;"><b>Cost :- </b></span>
                            @if (!empty($material_details->past_3_year_actual_cost))
                            <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span>
                            {{-- <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                            <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span> --}}
                            @else
                                <span>  </span>
                            @endif
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                        <span style="color:black;"><b>Service :- </b></span>
                            @if (!empty($material_details->past_3_year_actual_cost_service))
                        <span>{{ isset($year3[0]) ? $year3[0] : '' }}</span>
                        {{-- <span>{{ isset($year3[1]) ? $year3[1] : '' }}</span>
                        <span>{{ isset($year3[2]) ? $year3[2] : '' }}</span> --}}
                        @else
                                <span>  </span>
                            @endif
                    </td> 

                    </tr>
                    <tr style="border:1px solid black;">
                        <td style="border:1px solid black; width:33.3%;">
                            <span style="color:black;"><b>Year :- </b></span>
                                @if (!empty($material_details->past_3_year_actual_cost_fy))
                                {{-- <span>{{ $yr_f - 1 }}-{{ $yr_l - 1 }}</span> --}}
                                <span>{{ $yr_f - 2 }}-{{ $yr_l - 2 }}</span>
                                {{-- <span>{{ $yr_f - 3 }}-{{ $yr_l - 3 }}</span> --}}
                                @else
                                <span>  </span>
                                @endif
                        </td> 
    
                        <td style="border:1px solid black;width:33.3%;">
                            <span style="color:black;"><b>Cost :- </b></span>
                                @if (!empty($material_details->past_3_year_actual_cost))
                                {{-- <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span> --}}
                                <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                                {{-- <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span> --}}
                                @else
                                    <span>  </span>
                                @endif
                        </td> 
    
                        <td style="border:1px solid black;width:33.3%;">
                            <span style="color:black;"><b>Service :- </b></span>
                                @if (!empty($material_details->past_3_year_actual_cost_service))
                            {{-- <span>{{ isset($year3[0]) ? $year3[0] : '' }}</span> --}}
                            <span>{{ isset($year3[1]) ? $year3[1] : '' }}</span>
                            {{-- <span>{{ isset($year3[2]) ? $year3[2] : '' }}</span> --}}
                            @else
                                    <span>  </span>
                                @endif
                        </td> 
    
                        </tr>
                        <tr style="border:1px solid black;">
                            <td style="border:1px solid black; width:33.3%;">
                                <span style="color:black;"><b>Year :- </b></span>
                                    @if (!empty($material_details->past_3_year_actual_cost_fy))
                                    {{-- <span>{{ $yr_f - 1 }}-{{ $yr_l - 1 }}</span>
                                    <span>{{ $yr_f - 2 }}-{{ $yr_l - 2 }}</span> --}}
                                    <span>{{ $yr_f - 3 }}-{{ $yr_l - 3 }}</span>
                                    @else
                                    <span>  </span>
                                    @endif
                            </td> 
        
                            <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                    @if (!empty($material_details->past_3_year_actual_cost))
                                    {{-- <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span>
                                    <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span> --}}
                                    <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span>
                                    @else
                                        <span>  </span>
                                    @endif
                            </td> 
        
                            <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Service :- </b></span>
                                    @if (!empty($material_details->past_3_year_actual_cost_service))
                                {{-- <span>{{ isset($year3[0]) ? $year3[0] : '' }}</span>
                                <span>{{ isset($year3[1]) ? $year3[1] : '' }}</span> --}}
                                <span>{{ isset($year3[2]) ? $year3[2] : '' }}</span>
                                @else
                                        <span>  </span>
                                    @endif
                            </td> 
        
                            </tr>

                    <?php
                    $total = 0;
                    $s_importAmount = 0;
                    $totalvalue = $total;
                    ?>

                    @foreach ($service_import as $key => $s_import)
                        <?php
                        $s_importAmount = $s_import->amount;
                        ?>

                        @if (!empty($s_importAmount))
                            <?php
                            
                            $total = $total + $s_importAmount;
                            
                            ?>
                        @else
                            <?php
                            $total = $total;
                            ?>
                        @endif
                    @endforeach


                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                            Service BOQ 
                      </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                           <b>Total Amount :- </b>
                           @php
                           $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($total));

                            $formatted_budget = 0;
                            if (strlen(round($total)) >= 8) {
                                $crore = $total / 10000000;
                                $croress = floor($crore * 100) / 100;
                                $crores = floor($crore);
                                    $remaining = ($total - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                $formatted_budget = number_format($croress, 2) . '  Cr';
                            }else{
                                $words = convertNumberToWords($total). '  Rupees';
                                $formatted_budget = number_format($total, 2) . '  Rs.';
                            }
                            @endphp
                             <span>{{$formatted_budget ?? null }}</span>
                             <span>({{ucwords($words)}})</span>
                        </td>
                    </tr>

                    @php
                        $service_amount = explode(',', $material_details != '' ? $material_details->service_amount : '0');
                        $service_description = explode(',', $material_details != '' ? $material_details->service_description : '');
                    @endphp


                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>Amount :- </b>
                    
                             @if (!empty($material_details->service_amount))
                             @foreach ($service_amount as $key => $service_amt)
                                @php
                                $formatted_budget = 0;
                                if (strlen(round($service_amt)) >= 8) {
                                    $crore = $service_amt / 10000000;
                                    $crores = floor($crore * 100) / 100;
                                    $formatted_budget = number_format($crores, 2) . '  Cr';
                                }else{
                                    $formatted_budget = number_format($service_amt, 2) . '  Rs.';
                                }
                                @endphp
                              <span style="background-color:lightgray;margin-right:5px;padding:2px;">{{$formatted_budget ?? ''}}</span>
                            @endforeach
                             @else
                                 <span>  </span>
                               @endif
                        </td>

                        <td colspan="2" style="border:1px solid black;"> 
                        <b>Description :- </b>
                        @if (!empty($material_details->service_description))
                        @foreach ($service_description as $key => $service_des)
                            <span style="background-color:lightgray;margin-right:5px;padding:2px;">{{ $material_details != '' ? $service_des : '' }}</span>
                            @endforeach
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>  

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Cost Calculation For Services :- </b>
                        @if (!empty($material_doc->cost_calculation_for_service))
                        <a class="ml-2" href="{{ url(asset('materials-doc/' . $material_doc['cost_calculation_for_service'])) }}">{{ $material_doc->cost_calculation_for_service }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Past Practice Follow For Services (If Any) :- </b>
                            @if (!empty($material_details->past_practice_follow))
                                <span>{{ $material_details != '' ? $material_details['past_practice_follow'] : '' }}</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>AMC Proposed Start Date :- </b>
                            @if (!empty($material_details->amc_prop_start_date))
                                <span>{{\Carbon\Carbon::parse($material_details != '' ? $material_details['amc_prop_start_date'] : '' )->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>AMC Proposed End Date :- </b>
                            @if (!empty($material_details->amc_prop_end_date))
                                <span>{{\Carbon\Carbon::parse($material_details != '' ? $material_details['amc_prop_end_date'] : '' )->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Estimated Amount Of Services :- </b>
                            @if (!empty($material_details->estimate_amount_of_service))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->estimate_amount_of_service));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->estimate_amount_of_service)) >= 8) {
                                    $crore = $material_details->estimate_amount_of_service / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->estimate_amount_of_service - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->estimate_amount_of_service). '  Rupees';
                                    $formatted_budget = number_format($material_details->estimate_amount_of_service, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Estimated Amount Of Services Civil :- </b>
                            @if (!empty($material_details->estimate_amount_of_service_civil))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->estimate_amount_of_service_civil));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->estimate_amount_of_service_civil)) >= 8) {
                                    $crore = $material_details->estimate_amount_of_service_civil / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->estimate_amount_of_service_civil - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->estimate_amount_of_service_civil). '  Rupees';
                                    $formatted_budget = number_format($material_details->estimate_amount_of_service_civil, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Estimated Amount Of RR Charges :- </b>
                            @if (!empty($material_details->estimate_amount_of_rr_charge))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->estimate_amount_of_rr_charge));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->estimate_amount_of_rr_charge)) >= 8) {
                                    $crore = $material_details->estimate_amount_of_rr_charge / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->estimate_amount_of_rr_charge - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->estimate_amount_of_rr_charge). '  Rupees';
                                    $formatted_budget = number_format($material_details->estimate_amount_of_rr_charge, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Estimated Amount - Other :- </b>
                            @if (!empty($material_details->estimate_amount_other))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->estimate_amount_other));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->estimate_amount_other)) >= 8) {
                                    $crore = $material_details->estimate_amount_other / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->estimate_amount_other - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->estimate_amount_other). '  Rupees';
                                    $formatted_budget = number_format($material_details->estimate_amount_other, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Total Budget For Services (Including Civil And RR) :- </b>
                            @if (!empty($material_details->total_budget_service))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->total_budget_service));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->total_budget_service)) >= 8) {
                                    $crore = $material_details->total_budget_service / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->total_budget_service - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->total_budget_service). '  Rupees';
                                    $formatted_budget = number_format($material_details->total_budget_service, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                        <b>Total NV Amount :- </b>
                            @if (!empty($material_details->total_budget_both))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(round($material_details->total_budget_both));

                                $formatted_budget = 0;
                                if (strlen(round($material_details->total_budget_both)) >= 8) {
                                    $crore = $material_details->total_budget_both / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($material_details->total_budget_both - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $formatted_budget = number_format($croress, 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($material_details->total_budget_both). '  Rupees';
                                    $formatted_budget = number_format($material_details->total_budget_both, 2) . '  Rs.';
                                }
                                @endphp
                                <span>{{ $formatted_budget ?? '' }}</span>
                                <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                    </tr>

                    
               
            </table>

            
            {{-- <div style="page-break-after:always;"></div> --}}
            <table style="width:100% ;  border-collapse: collapse;" class="signature-t" >
        
                <tr style="border:1px solid lightgray !important;">
                    <th style="width:25%;border:1px solid lightgray !important;">Name</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Remarks</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Signature</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Date & Time</th>
                </tr>
                @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)   
                <tr style="width:25%;border:1px solid lightgray !important;">
                  
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer1</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->rv1_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                                $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                            @endphp
                            @if($user_rv1->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{  $user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv1->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv1_timestamp)) }}</td>

                      
                </tr> 
                @endif           
                @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)            
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer2</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->rv2_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                                $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                            @endphp
                            @if($user_rv2->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv2->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv2_timestamp)) }}</td>

                    
                </tr> 
                @endif                   
                @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)        
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer3</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span >{{ $Nvsericestatus->rv3_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                            @endphp
                            @if($user_rv3->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv3->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv3_timestamp)) }}</td>
                    
                </tr> 
                @endif           
                @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)         
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer4</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span >{{ $Nvsericestatus->rv4_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
                            @endphp
                            @if($user_rv4->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv4->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv4_timestamp)) }}</td>
                      
                </tr>
                @endif           
                @if($Nvsericestatus->hod_status == 1||$Nvsericestatus->hod_status == 2)      
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>HOD</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->hod_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_hod= App\Models\User::where("id",$Nvsericestatus->hod_id)->first();
                            @endphp
                            @if($user_hod->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_hod->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->hod_timestamp)) }}</td>
                      
                </tr>
                @endif 

                @if( $Nvsericestatus->groupcio_status == 1||$Nvsericestatus->groupcio_status == 2 )
                <tr style="width:25%;border:1px solid lightgray !important;">
                   
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b> Group Head </b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->groupcio_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                           $user_groupcio = App\Models\User::where("id",$Nvsericestatus->groupcio_id)->first();
                            @endphp
                            @if($user_groupcio->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if($user_groupcio->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if($user_groupcio->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if($user_groupcio->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if($user_groupcio->signature_id==5) 
                            <img  src="{{public_path('/images/'.   $user_groupcio->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->groupcio_timestamp)) }}</td>
                      
                </tr>
                @endif 
                
                @php 
                    $workflow_data = DB::table('capex_workflows_status')
                                    ->where('material_id', $material_details->id)
                                    ->where('nv_budget_type', $nv_data->budget_type)
                                    ->whereIn('nv_stage_status',[1,2])
                                    ->where('nv_stage_remark', '!=', null)
                                    ->get();

                    $reviewerLabels = [
                    'work_rew1' => 'Reviewer1',
                    'work_rew2' => 'Reviewer2',
                    'work_rew3' => 'Reviewer3',
                    'work_rew4' => 'Reviewer4',
                    'approver' => 'Approver'
                    ];
            
                @endphp
            
                @if($workflow_data->isNotEmpty())
                    @foreach($workflow_data as $workflowData)
                        @php 
                                $role = $reviewerLabels[$workflowData->reviewer_name] ?? '';
                                $roleWithDepartment = $role . ' (' . getDepartmentName($workflowData->department_id) . ')';
                                $stage_user_name = App\Models\User::where("id",$workflowData->workflow_user_id)->first();
                        @endphp
                        <tr style="width:25%;border:1px solid lightgray !important;">
                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                                <b> {{ $roleWithDepartment }}</b> 
                            </td> 
                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                                <span>{{$workflowData->nv_stage_remark ?? ''}}</span>
                            </td>

                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @if(in_array($workflowData->nv_stage_signature, [1,2,3,4]))
                                <span style="font-family: 
                                    @if($workflowData->nv_stage_signature == 1) 'Cedarville Cursive', cursive;
                                    @elseif($workflowData->nv_stage_signature == 2) 'Courier New', Courier, monospace;
                                    @elseif($workflowData->nv_stage_signature == 3) 'Satisfy', cursive;
                                    @elseif($workflowData->nv_stage_signature == 4) 'Shadows Into Light Two', cursive;
                                    @endif">
                                    {{ $stage_user_name->name ?? '' }}</span>
                                @elseif($workflowData->nv_stage_signature == 5)
                                <img src="{{ public_path('/images/' . $stage_user_name->image) }}"
                                    style="height:auto;width:80px; margin-top:10px;" />
                                @endif 
                            </td>

                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            {{ date('d-M-y h:i A', strtotime($workflowData->nv_stage_timestamp)) }}
                            </td>

                        </tr>
                    @endforeach
                @endif
            </table>  
                      
            
               
           
        </div>

        </div>

        
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
    <script>
        
    </script>

</body>

</html>
