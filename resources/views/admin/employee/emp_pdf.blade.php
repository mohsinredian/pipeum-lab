<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Employee PDF</title>
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
        <div  class="card-body table-responsive">
               <div>
                 <h2 style="background-color: rgb(3, 142, 220);color:white;padding:5px; text-align:center;"> <b> Employee Details</b></h2>
                </div><br>
            <table class="table table-bordered" style="width: 100%;">
                <thead class="text-center">
                    <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email Id</th>
                    <th width="18%" class="text-center">Mobile No</th>
                    <th class="text-center">Department</th>
                    <th width="18%" class="text-center">Employee Id</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">LastLogin</th>
                    </tr>
                </thead>
                <tbody class="text-center">

                @foreach($employees as $key => $employee)

                   @php $i = $key + 1; @endphp

                    <tr>

                        <td class="text-center">{{$i}}</td>

                        <td class="text-center">{{$employee->name}}</td>
                        <td class="text-center">{{$employee->email}}</td>
                        <td class="text-center">{{$employee->phone}}</td>
                        @php 
                            $departmentIds = explode(',', $employee->department_id);
                            $departmentNames = [];
                            foreach ($departmentIds as $id) {
                                $departmentNames[] = getDepartmentName($id);
                            }
                        @endphp
                        <td class="text-center">{{implode(', ', $departmentNames)}}</td>
                        <td class="text-center">{{$employee->employee_id}}</td>
                        <td class="text-center">{{$employee->role->name}}</td>
                        <td class="text-center">{{$employee->status == 1 ? 'Active' : 'Inactive'}}</td>

                        @if ($employee->last_login_at === null) 
                          <td class="text-center"> Not logged in </td>
                        @else
                         <td class="text-center">{{date("d-M-y h:i A", strtotime($employee->last_login_at))}}</td>
                        @endif
                       </tr>

                @endforeach

            </tbody>
            </table>
        </div>
        <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('admin/js/employee.js') }}"></script>
    </body>
</html>