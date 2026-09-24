 <!-- @endif -->@extends('admin.layout.master', ['page_title' => 'Create Location'])


@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manage Floors</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Location</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">

    <!-- <div class="container-fluid"> -->
    <!-- <div class="row">
          <div class="col-12"> -->

    <div class="main-panel">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <h4 class="card-title">Add New Task</h4> -->
                        <form class="form-sample form" method="POST" id="create_floor_plan_form">
                            @csrf
                            <!-- <input class="form-check-input" type="hidden" name="company_id" value="" > -->
                            <input class="form-check-input" type="hidden" name="location_id" value="{{$locations[0]['id']}}">
                            <div class="row mt-3">
                                <div class="col-lg-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="alert alert-primary">
                                                        Manage Floor
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container-fluid">
                                                <table class="table table-bordered table-responsive">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th class="text-center">Area</th>
                                                            <th class="text-center">Select Area</th>
                                                            <th class="text-center">Store Room</th>
                                                            <th class="text-center">Gym</th>
                                                            <th class="text-center">Corridor</th>
                                                            <th class="text-center">Cabins</th>
                                                            <th class="text-center">Panel Room</th>
                                                            <th class="text-center">Reception Area</th>
                                                            <th class="text-center">Meeting Room</th>
                                                            <th class="text-center">Ladies Washroom</th>
                                                            <th class="text-center">Gents Washroom</th>
                                                            <th class="text-center">Board Room</th>
                                                            <th class="text-center">Common Area</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th class="text-left">Basement</th>
                                                            <td class="text-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Basement"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-left">Ground Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ground Floor"
                                                                        id="" name="floor_name[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">First Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="First Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">Second Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Second Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">Third Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Third Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">Fourth Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Fourth Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">Fifth Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Fifth Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th class="text-left">Sixth Floor</th>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" name="floor_name[]" value="Sixth Floor"
                                                                        id=""><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Store Room"
                                                                        id="" name="store_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gym"
                                                                        id="" name="gym[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Corridor"
                                                                        id="" name="corridor[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Cabins"
                                                                        id="" name="cabins[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Panel Room"
                                                                        id="" name="panel_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Reception Area"
                                                                        id="" name="reception_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Meeting Room"
                                                                        id="" name="meeting_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Ladies Washroom"
                                                                        id="" name="ladies_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Gents Washroom"
                                                                        id="" name="gents_washroom[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Board Room"
                                                                        id="" name="board_room[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check"><input class="form-check-input"
                                                                        type="checkbox" value="Common Area"
                                                                        id="" name="common_area[]"><label
                                                                        class="form-check-label"
                                                                        for="flexCheckDefault"></label></div>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                            <button type="submit" class="btn btn-success mr-2">Submit</button>
                            <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- <footer class="footer">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright ©
                                    2022 <a href="#" class="text-muted" target="_blank">Event Tracking System</a>.
                                    All rights reserved.</span>
                              
                            </div>
                        </div>
                    </div>
                </footer> -->
        <!-- partial -->
    </div>
    <!-- /.container-fluid -->
</section>




@endsection

@push('script')
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/location.js')}}"></script>
@endpush