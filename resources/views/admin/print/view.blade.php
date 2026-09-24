<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>New Request</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: sans-serif;
            font-size: 100% !important;

        }

        * {
            margin-left: 0;
            margin-right: 0;
            font-family: sans-serif;


        }

        .note {
            border: 1px solid black;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .venture-bses {
            border: 1px solid black;
            border-left: 0;
            border-right: 0;
            border-top: 0;
        }

        table,
        th,
        td {
            border: 1px solid black !important;
            border-collapse: collapse;
            /* border-left:0; */
            margin-bottom: -2px !important;
            margin-left: 5px !important;
            margin: -4px;
        }

        table {
            border-left: 0;
        }

        .mainsection4 {
            border: 1px solid #161616;

            font-weight: bold;
            font-size: 14px;
            border-top: 0;


        }

        td {
            margin-top: -10px;
        }

        .mb-5,
        .my-5 {
            margin-top: 9rem !important;
        }

        .mainsection5 {
            border: 1px solid #161616;

            font-weight: bold;
            font-size: 14px;
            border-top: 0;


        }

        thead {
            border-bottom: 1px solid black;


        }

        @media only screen and (max-width: 600px) {

            .container img {
                display: inline !important;
                margin-right: 0 !important;
            }

            .material-pass {
                font-size: 11px;
            }

            .bsescheckbox label {
                font-size: 8px;
            }

            .venture-bses {
                margin-right: -20px !important;
            }


        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex">
            <div class="mr-auto p-2">
                <img src="{{ url('images/logo.png') }}" />
            </div>
            <!-- <div class="p-2 sm-d-none bsescheckbox"><input align="right" type="checkbox" />
                <label>BSES Rajdhani Power Limited</label><br>
                <input align="right" type="checkbox" />
                <label>BSES Yamuna Power Limited</label>
            </div> -->

        </div>
        <!-- <p class="text-center font-weight-bold venture-bses">A joint venture of BSES Limited with Govt. of NCT , Delhi
        </p>
        <p class="text-center">Corporate Office: BSES Bhawan, Nehru Place, New Delhi - 110019 Tel:39999999 </p> -->
        <div class="row ml-5 font-weight-bold mb-3 material-pass">
         <div class="col-4 ">
               
            </div> -
            <div class="col-6">
                <span style="text-align:center;font-size:40px;">NV-Service</span>
                </div>
          
            <div class="col-15 float-right">
           
                <a href="{{ redirect()->back() }}"><button class="btn btn-info d-flex float-right" onClick="window.print()">Print
                        this page »</button></a>
                        
            </div>
           
        </div>

    </div>




    <!-- ............number coloumn............. -->
    <div class="container  mainsection2">
        <form action="">

            <!-- ...............................14 coloumn table.................................................. -->


            <div class="">
            <table class="table   table-bordered" style="border:3px solid black">
                    <thead>
                        <tr>
                           
                          


            </div>
            </td>
            </tr>
            <tr>
                <th class="text-center" col>Sr no.</th>
                <th class="text-center">Department Name</th>
                <th class="text-center">DOP Refrence Number</th>
                
                <th class="text-center" colspan="4">Proposal Name</th>
                <th class="text-center">Benefit</th>
                <th class="text-center">Type Of Proposal</th>
                <th class="text-center">Total Buget</th>

               
              
            </tr>

            </thead>
    
            <tbody>
                <tr>
                    <th class="text-center" scope="row">1</th>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center" colspan="4"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                 
                </tr>
               
                <tr>
                <th class="text-center" scope="row">2</th>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center" colspan="4"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                </tr>
                <tr>
                    <td colspan="12">
                        <div class="d-flex mt-5 mb-5 justify-content-around">
                            <p><b>Approved by </b></p>
                            <p><b>Reviewed By</b></p>
                           <div><p class="mb-0"><b>Prepared By</b></p>
                            <div class="text-center"><p>{{ Auth::user()->name }}</p>
                            </div>
                        </div> 
                        </div>
                    </td>
                </tr>
            </tbody>

            </table>
           
    </div>

    </form>
    <!-- <div class="d-flex mt-5 mb-5 justify-content-around">
            <p>Approved by DGM(IT)</p>
            <p>Reviewed By</p>
            <p>Prepared By</p>

        </div> -->
    </div>
    <!-- <center>
        <div class="container mt-2 font-weight-bold note">
            <p>Note: Please help security to check the materials at gate.</p>
        </div>
    </center> -->
    <script src="index.js"></script>
</body>

</html>
