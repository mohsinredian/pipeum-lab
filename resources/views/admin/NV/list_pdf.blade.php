<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NV PDF</title>
    </head>
    <body>
        <div  class="card-body table-responsive">
            <table border="1" id="nv_datatable" class="table">
                <thead class="text-center">
                    <tr>
                        <th class="text-center" width="5%">S.No</th>
                        <th>Proposal Number</th>
                        <th>Budget Type</th>
                        <th>Initiated By</th>
                        <th width="15%">Initiated Date</th>
                        <th width="10%">DOP Ref No</th>
                        <th width="15%">Budgetary Provision</th>
                        <th width="15%">Proposal Type</th>
                        <th width="18%">NV Type</th>
                        <th width="15%">Fiscal Year</th>
                    </tr>
                </thead>
                <tbody>

                @foreach($nv1 as $key => $NV_list)

                   @php $i = $key + 1; @endphp

                    <tr>

                        <td>{{$i}}</td>

                        <td>

                            <span>NV/{{$NV_list->budget_type}}/{{$NV_list->fiscal_year}}/

                                /@if($NV_list->service_id == 1)

                                    {{'Material'}}

                                @elseif($NV_list->service_id == 2)

                                    {{'Service'}}

                                @endif

                                /{{$NV_list->id}}</span>

                        </td>

                        <td>{{$NV_list->budget_type}}</td>

                        <td>{{$NV_list['user']['name']}}</td>

                        <td>{{ date('d-M-y', strtotime($NV_list->created_at)) }}</td>

                        <td>

                            @if($NV_list->service_id == 1 )

                                {{$NV_list['material']['dop'] ?? ''}}

                            @else

                                {{$NV_list['services']['dop_ref_no'] ?? ''}}

                           




                            @endif

                           

                        </td>

                        <td>{{$NV_list->budgetary_provision}}</td>

                        <td>{{$NV_list->proposal_type}}</td>

                        <td>

                            @if($NV_list->service_id == 1)

                                {{'Material'}}

                            @elseif($NV_list->service_id == 2)

                                {{'Service'}}

                            @endif

                        </td>

                        <td>{{$NV_list->fiscal_year}}</td>

                    </tr>

                @endforeach

            </tbody>
            </table>
        </div>
        <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('admin/js/nv.js') }}"></script>
    </body>
</html>