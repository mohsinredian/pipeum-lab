<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <style>
    table,
    td,
    th {
        border: 1px solid #ddd;
        text-align: left;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 15px;
    }
    </style>
</head>

<body width="100%" style="background-color: #ffffff;margin:0 auto;">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Column</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <th>Application<< /th>
                        <td>{{$application}}</td>
                    </tr>
                    <tr>
                        <th>Document Description</th>
                        <td>{{$description}}</td>
                    </tr>
                    <tr>
                        <th>Page</th>
                        <td>{{$page}}</td>
                    </tr>
                    <tr>
                        <th>Sub Menu</th>
                        <td>{{$sub_menu}}</td>
                    </tr>
                    <tr>
                        <th>Page Breakdown</th>
                        <td>{{$page_breakdown}}</td>
                    </tr>
                    <tr>
                        <th>Link</th>
                        <td><a href="{{$link}}">{{$link}}</a></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>{{$dept_id}}</td>
                    </tr>
                    <tr>
                        <th>Department Spoc Person</th>
                        <td>{{$dept_spoc_id}}</td>
                    </tr>
                    <tr>
                        <th>Frequency</th>
                        <td>{{$frequency}}</td>
                    </tr>
                    <tr>
                        <th>Last Uploaded Date</th>
                        <td>{{$document_upload_end_date}}</td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td>{{$remarks}}</td>
                    </tr>

                    <tr>
                        <th>Assign To</th>
                        <td>{{$assign_to}}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{$status}}</td>
                    </tr>
                </thead>
            </table> 
        </div>
    </div>
    <p>Thanks,</p>
    <p><b>Team BSES</b></p>
</body>

</html>
