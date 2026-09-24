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
    </style>

</head>



<body data-responsejs='{"create":[{"prop":"width", "prefix":"min-device-width-", "breakpoints":[0, 320, 481, 641, 767, 961, 1025, 1281] }]}'>

        @php
          
            $ser_boq = App\Models\ServiceBOQBulk::where('nv_id', $id)->orderBy('id', 'desc')->first();
          
        @endphp

        <div class="card" style="page-break-after: always; margin-top:20px;">
      
          

            <br>
            <div class="card-body " style="page-break-after: always;" >
                <!-- <h4 class="text-center"> NV-Material BOQ/Service BOQ Details</h4><br> -->

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
