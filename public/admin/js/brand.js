$(document).ready(function(){
	
	// Datatable
	if($(document).find('#brand_datatable').length > 0){
		$brand_list = $('#brand_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			dom: 'Bfrtip',
			buttons: [
				{ extend: 'excel', exportOptions: { modifier: { page: 'all', search: 'none'} } },
				{ extend: 'csv',   exportOptions: { modifier: { page: 'all', search: 'none' } } }
			],
			ajax: {
				url: '/admin/brands'
			},
			columns: [
				{ 
					data: 'DT_RowIndex', 
					name: 'DT_RowIndex', 
					orderable: false, 
					searchable: false,
					className: "text-center" 
				},
				{
					data: 'id',
					name: 'id',
					className: "text-center"
				},
				{
					data: 'id',
					name: 'test_id',
					className: "text-center"
				},
				
				
				{
					data: 'service',
					name: 'task_name',
					className: "text-center"
				},
				{
					data: 'task_description',
					name: 'task_description',
					className: "text-center"
				},
				{
					data: 'division',
					name: 'division',
					className: "text-center"
				},
				{
					data: 'location',
					name: 'location',
					className: "text-center"
				},
				{
					data: 'assignee',
					name: 'assignee',
					className: "text-center"
				},
				{
					data: 'start_date',
					name: 'start_date',
					className: "text-center"
				},
				{
					data: 'end_date',
					name: 'end_date',
					className: "text-center"
				},
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				
				{
					data: 'action',
					name: 'action',
					className: "text-center",
					orderable: false
				}
			],
			'columnDefs': [{ 'orderable': false, 'targets': 0 },{'visible': false, 'targets': [1], 'orderable': true}],
			'aaSorting': [[1, 'desc']]
		});
		$brand_list.$('#brand_datatable').DataTable( {
			dom: 'Bfrtip',
			buttons: [
				{ extend: 'excel', exportOptions: { modifier: { page: 'all', search: 'none'} } },
				{ extend: 'csv',   exportOptions: { modifier: { page: 'all', search: 'none' } } }
			]
		} );
	}
	jQuery.validator.addMethod(
        "validEmail",
        function (value, element) {
            return this.optional(element) || /\S+@\S+\.\S+/.test(value);
        },
        "Please enter valid email"
    );
	jQuery.validator.addMethod("alpha", function (value, element) {
		return this.optional(element) || /^[a-zA-Z\s']*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("alphanumeric", function (value, element) {
		return this.optional(element) || /^[a-zA-Z0-9\s']*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("alphadash", function (value, element) {
		return this.optional(element) || /^[a-zA-Z \s']*$/.test(value);
	}, "Invalid input");
	// Create role
	$('#create_brand_form, #edit_brand_form').validate({
		rules: {
			task_name: {
				required: true,
			},
			task_description: {
				required: true,
				maxlength: 1000,
			
			},
			frequency: {
				required: true,
			},

			division: {
				required: true,
			},
			location: {
				required: true, 
			},
			role: {
				required: true, 
			},
			assignee: {
				required: true,
			},
			start_date: {
				required: true,
			},
			end_date: {
				required: true,
			},
		},
		messages: {
			task_name: {
				required: 'Please select service',
			},

			task_description: {
			 	required: 'Please enter description',
				 maxlength: "Description name should not be more than 150 characters",
				 alpha: "Please enter valid input",
			 },
			 frequency: {
				required: 'Please select frequency',
			},
			division: {
				required: 'Please select company',
			},
			location: {
				required: 'Please select location',
			},
			role: {
				required: 'Please select role',
			},
			assignee: {
				required: 'Please select assignee',
			},
			start_date: {
				required: 'Please select start date',
			},
			end_date: {
				required: 'Please select end date',
			},
			end_date: {
				required: 'Please select end date',
			},
		},
		errorPlacement: function (error, element) {
			if (element.attr("name") == "logo") {
				// custom error placement
				$(element).closest('.form-group').find('.common-error').html(error.text());
			}
			else {
				// default error placement
				element.after(error);
			}
		}
	});

	$('#create_brand_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_brand_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_brand_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/brands/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Task created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/brands';
						});
						
						 $('#create_brand_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_brand_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});

	// Edit role
	$('#edit_brand_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_brand_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_brand_form')[0]),
				type: 'post',
				url: "/admin/brands/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Brand Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/brands';
						});
						
						$('#edit_brand_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_brand_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});
	
	$(document).on('click', '.delete_brand', function () {

		Swal.fire({
			title: 'Are you sure?',
			// text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((result) => {
			if (result.value) {
				$('.pre-loader').show();

				let brand_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'brand_id': brand_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/brands/delete/'+brand_id,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {

							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-primary"
							}).then(function () {
								window.location.reload();
							});
						}
						else if (res.result == 'failure') {

							Swal.fire({
								text: "Something went wrong. Please try again.",
								type: "error",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-light"
							}).then(function () {
								window.location.reload();
							});
						}

						$('.pre-loader').hide();
					},

					error: function (error) {

					}
				});
			}
		});
	});

	$('#division').on('change', function () {
		division = $(this).val();
		
		$('.pre-loader').show();
		$.ajax({
			data: {
				division: division,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/employees/location',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let locations = response.data;
					
                    $("#location").empty();
                    $("#location").append(
                        '<option value="">Select location</option>'
                    );

                    $.each(locations, function (i, location) {
                        $("#location").append(
                            $("<option></option>")
                                .attr("value", location.id)
                                .text(location.name)
                        );
                    });
				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: "Something went wrong. Please try again.",
						type: 'error',
						buttonsStyling: false,
						confirmButtonText: "OK",
						confirmButtonClass: "btn font-weight-bold btn-light"
					}).then(function () {
						window.location.reload();
					});
				}

				$('.pre-loader').hide();
			},

			error: function (error) {

			}
		});
	});
	
	$('#role').on('change', function () {
		role = $(this).val();
		var location = $('#location').val();
		//console.log(location);
		//alert(locations);
		$('.pre-loader').show();
		$.ajax({
			data: {
				role: role,
				location: location,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/brands/role',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let role = response.data;
					console.log(role);
                    $("#assignee").empty();
					
                    $.each(role, function (i, assignee) {
						$("#assignee").append(
							$("<option></option>")
								.attr("value", assignee.user_id)
								.text(assignee.name)
						);
					});
					
                    
				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: "Something went wrong. Please try again.",
						type: 'error',
						buttonsStyling: false,
						confirmButtonText: "OK",
						confirmButtonClass: "btn font-weight-bold btn-light"
					}).then(function () {
						window.location.reload();
					});
				}

				$('.pre-loader').hide();
			},
			// //$.each(assignee, function (i, assignee) {
			// 	$("#assignee").append(
			// 		$("<option></option>")
			// 			.attr("value", assignee.id)
			// 			.text(assignee.name)
			// 	);
			// });

			error: function (error) {

			}
		});
	});

	$('#location').on('change', function () {
		locations = $(this).val();
		//alert(locations);
		$('.pre-loader').show();
		$.ajax({
			data: {
				locations: locations,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/brands/location',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let assignee = response.data;
				
                    $("#assignee").empty();
					$("#role").empty();
                    $("#location").append(
                        '<option value="">Select location</option>'
                    );
					$("#role").append(
                        '<option value="">Select role</option>'
                    );
					 $.each(assignee, function (i, assignee) {
			 	$("#role").append(
			 		$("<option></option>")
			 			.attr("value", assignee.id)
			 			.text(assignee.title)
			 	);
				
				 $("#floor_plan").html(response.floorPlan);
			 });
				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: "Something went wrong. Please try again.",
						type: 'error',
						buttonsStyling: false,
						confirmButtonText: "OK",
						confirmButtonClass: "btn font-weight-bold btn-light"
					}).then(function () {
						window.location.reload();
					});
				}

				$('.pre-loader').hide();
			},
			// //$.each(assignee, function (i, assignee) {
			// 	$("#assignee").append(
			// 		$("<option></option>")
			// 			.attr("value", assignee.id)
			// 			.text(assignee.name)
			// 	);
			// });

			error: function (error) {

			}
		});
	});

	//Floor 
	$('#location').on('change', function () {
		locations = $(this).val();
		//alert(locations);
		$('.pre-loader').show();
		$.ajax({
			data: {
				locations: locations,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/brands/floor',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let assignee = response.data;
                    $("#assignee").empty();
                    $("#location").append(
                        '<option value="">Select location</option>'
                    );
					$("#role").append(
                        '<option value="">Select role</option>'
                    );
					 $.each(assignee, function (i, assignee) {
			 	$("#role").append(
			 		$("<option></option>")
			 			.attr("value", assignee.id)
			 			.text(assignee.role_name)
			 	);
			 });
				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: "Something went wrong. Please try again.",
						type: 'error',
						buttonsStyling: false,
						confirmButtonText: "OK",
						confirmButtonClass: "btn font-weight-bold btn-light"
					}).then(function () {
						window.location.reload();
					});
				}

				$('.pre-loader').hide();
			},
			error: function (error) {

			}
		});
	});
	if($(document).find('#ticket_datatable').length > 0){
		$ticket_list = $('#ticket_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			searching: true,
			lengthChange: false,
			dom: 'Bfrtip',
			buttons: [
				{ extend: 'excel', exportOptions: { modifier: { page: 'all', search: 'none'} } },
				{ extend: 'csv',   exportOptions: { modifier: { page: 'all', search: 'none' } } }
			],
			ajax: {
				url: '/admin/brands/ticket_list'
			},
			columns: [
				{ 
					data: 'DT_RowIndex', 
					name: 'DT_RowIndex', 
					orderable: false, 
					searchable: false,
					className: "text-center" 
				},
				{
					data: 'id',
					name: 'id',
					className: "text-center"
				},
				{
					data: 'id',
					name: 'id',
					className: "text-center"
				},
				
				
				{
					data: 'service',
					name: 'task_name',
					className: "text-center"
				},
				{
					data: 'task_description',
					name: 'task_description',
					className: "text-center"
				},
				{
					data: 'division',
					name: 'division',
					className: "text-center"
				},
				{
					data: 'location',
					name: 'location',
					className: "text-center"
				},
				
				{
					data: 'start_date',
					name: 'start_date',
					className: "text-center"
				},
				{
					data: 'assignee',
					name: 'assignee',
					className: "text-center"
				},
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				{
					data: 'oc_status',
					name: 'oc_status',
					className: "text-center"
				},
				{
					data: 'su_status',
					name: 'su_status',
					className: "text-center"
				},
				
			],
			'columnDefs': [{ 'orderable': false, 'targets': 0 },{'visible': false, 'targets': [1], 'orderable': true}],
			'aaSorting': [[1, 'desc']]
		});
	}
	
});


{/* <script> */}
    $(document).on('submit', 'form#formSubmits', function(e)
    {
    e.preventDefault();
	$('#myModal2').modal('show');
    var data = new FormData(this);
    $('#loderIcon').show();
    $('#loderButton').prop("disabled", true);
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            dataType: "json",
            data: data,
            success: function(response)
            {
				// alert(response.responseCode.signature_id);
				// console.log('hiiii');
                $('#loderIcon').hide();
                $('#loderButton').prop("disabled", false);
                if (response.responseCode == 200)
                {	
                   // toastr.success(response.responseMessage);
                    //$('div.add_model').modal('hide');
					location.reload();
                } else
                {
                    // toastr.error(response.responseMessage);
                }
            }
        });
    });

	
	// $(document).ready(function() {
	// 	if ($(document).find('#table_log').length > 0) {
	// 		$('#table_log').DataTable({
	// 			responsive: false,
	// 			searching: false,
	// 			lengthChange: false,
	// 			dom: 'Bfrtip',


	// 		});
	// 	}
	// });


	$(document).on('submit', 'form#logSubmits', function(e)




    {


        e.preventDefault();




            var data = new FormData(this);




            $('#myModal3').modal('show');




            $('#loderIcon').show();
            $.ajax({

                cache: false,

                contentType: false,

                processData: false,

                url: $(this).attr("action"),

                method: $(this).attr("method"),

                dataType: "json",

                data: data,

				success: function(response)

                {
                    console.log(response.Userlog);

                    $.each(response.Userlog, function (key, value) {

                        if(value.signature_id == 1)

                        {
							
							var tableRow = $("<tr>")
							.append("<td>" + (key + 1) + "</td>")
							.append("<td>" + moment(value.created_at).format("D-M-y h:mm A") + "</td>");
					
						var userNameElement = $("<td>")
							.html("<h5>" + value.user.name + "</h5>")
							.css('font-family', 'Cedarville Cursive, cursive');
					
						tableRow.append(userNameElement);
						
					
						$('#log').append(tableRow);



                        }

						else if (value.signature_id == 2) {
							var tableRow = $("<tr>")
								.append("<td>" + (key + 1) + "</td>")
								.append("<td>" + moment(value.created_at).format("D-M-y h:mm A") + "</td>");
						
							var userNameElement = $("<td>")
								.html("<h5 style='font-family:'Courier New', Courier, monospace;'>" + value.user.name + "</h5>");
						
							tableRow.append(userNameElement);
						
							$('#log').append(tableRow);
						}
						else if (value.signature_id == 3) {
							var tableRow = $("<tr>")
								.append("<td>" + (key + 1) + "</td>")
								.append("<td>" + moment(value.created_at).format("D-M-y h:mm A") + "</td>");
						
							var userNameElement = $("<td>")
								.html("<h5 style='font-family: Satisfy, cursive;'>" + value.user.name + "</h5>");
						
							tableRow.append(userNameElement);
						
							$('#log').append(tableRow);
						}

                        else if (value.signature_id == 4) {
							var tableRow = $("<tr>")
								.append("<td>" + (key + 1) + "</td>")
								.append("<td>" + moment(value.created_at).format("D-M-y h:mm A") + "</td>");
						
							var userNameElement = $("<td>")
								.html("<h5 style='font-family: Shadows Into Light Two, cursive;'>" + value.user.name + "</h5>");
						
							tableRow.append(userNameElement);
						
							$('#log').append(tableRow);
						}
						else if (value.signature_id == 5) {
							var tableRow = $("<tr>")
								.append("<td>" + (key + 1) + "</td>")
								.append("<td>" + moment(value.created_at).format("D-M-y h:mm A") + "</td>");
						
							var userNameElement = $("<td>").html("<h5 id='image_" + key + "'>" + value.image + "</h5>");
						
							// Append the userNameElement to the tableRow
							tableRow.append(userNameElement);
						
							$('#log').append(tableRow);
						
							// Create an image element and set its source attribute and dimensions
							var userImage = $("<img>")
								.attr("src", "/images/" + value.image)
								.attr("height", "50")
								.attr("width", "50");
						
							// Append the image to the corresponding userNameElement
							$('#image_' + key).html(userImage);
						}
						

                    

                    })

                }

            });

    });


setTimeout(function() {
    $('#alert_msg').fadeOut('fast');
});

$('#click_btn').on('click', function() {
    $('#myModal').modal('show');
});
$(document).ready(function(){

    $('.page_load').click(function(){

        location.reload();

    });

});
var uploadField = document.getElementById("file");

uploadField.onchange = function() {
    if(this.files[0].size > 1048576 ){
       alert("File is too big!");
       this.value = "";
    };
};
	// function previewFile() {
	// 	var preview = document.getElementById('output');
	// 	var file    = document.querySelector('input[type=file]').files[0];
	// 	var reader  = new FileReader();
	
	// 	reader.onloadend = function () {
	// 	preview.src = reader.result;
	// 	}
	
	// 	if (file) {
	// 	reader.readAsDataURL(file);
	// 	} else {
	// 	preview.src = "";
	// 	}
	// }









    function previewFile() {

        var preview = document.getElementById('output');

        var file    = document.querySelector('input[type=file]').files[0];

        var reader  = new FileReader();

    

        reader.onloadend = function () {

        preview.src = reader.result;

        }

    

        if (file) {

        reader.readAsDataURL(file);

        } else {

        preview.src = "";

        }

    }

    



    {/* </script> */}