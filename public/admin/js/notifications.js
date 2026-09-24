$(document).ready(function(){
	
	// Datatable
	if($(document).find('#notification_datatable').length > 0){
		$('#notification_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/notification'
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
					data: 'subject',
					name: 'subject',
					className: "text-center"
				},
				{
					data: 'email',
					name: 'email',
					className: "text-center"
				},
				{
					data: 'content',
					name: 'content',
					className: "text-center"
				},
			
				{
					data: 'name',
					name: 'name',
					className: "text-center"
				},
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				{
					data: 'created_at',
					name: 'created_at',
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
		var hasLetter = /[a-zA-Z]/.test(value);
		var hasNumber = /\d/.test(value);
		var hasSpecial = /[~`!@#$%^&*()_+\-=[\]{}|\\;:'",.<>/?]/.test(value);
		var isValid = hasLetter && hasNumber && hasSpecial;
		return this.optional(element) || isValid;
	}, "Please enter a combination of letters, numbers, and special characters.");
	

	jQuery.validator.addMethod("alphadash", function (value, element) {
		return this.optional(element) || /^[a-zA-Z \s']*$/.test(value);
	}, "Invalid input");
	// Create role
	$('#create_employee_form, #create_employee_form').validate({
		rules: {
			name: {
				required: true,
				maxlength: 50,
				alpha: true,
			},
			
			email: {
                   required: true,
                   validEmail:true,
                   maxlength: 50,
			},
			mobile: {
				required:true,
				number:true,
				minlength:10,
				maxlength:15,
			},
			division: {
				required:true,
			},
			department: {
				required:true,
			},
			location:{
				required:true,
			},
			emp_id: {
				required: true,
				//alphanumeric:true,
			},
			status: {
				required: true,
			},
			password: {
				required: true,
				alphanumeric:true,
				minlength:10,
				maxlength:20,
			},
			role_id: {
				required: true,
			},
		},
		messages: {
			name: {
				required: "Please enter employee name ",
				maxlength: "Employee name should not be more than 20 characters",
				alpha: "Please enter valid input",
			},
			password: {
				required: "Please enter your password ",
				maxlength: "the password should not be more than 50 characters",
				alpha: "Please enter valid input",
			},
			email: {
				  required: "Please enter employee email",
				  email: "Please enter valid email",
				  maxLength: "employee email should not be more than 50 characters",
			},
			mobile: {
				required: "Please enter employee mobile",
				number: "Please enter valid mobile",
				minlength: "employee name should not be less than 10 numeric",
				maxLength: "employee name should not be more than 15 numeric",
			},
			division: {
				required: "Please select division",
			},
			department: {
				required: "Please select department",
			},
			location: {
				required: "Please select location",
			},
			emp_id: {
				required: "Please enter employee id",
				alphanumeric: "Please enter valid input",
			},	
			status: {
				required: 'Please select status',
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

	$('#create_employee_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_employee_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_employee_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/employees/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Employee Created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/employees';
						});
						
						 $('#create_employee_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_employee_form').find('.'+key+'_error').html(error_msgs[key][0]);
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

	// Edit role
	$('#edit_employee_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_employee_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_employee_form')[0]),
				type: 'post',
				url: "/admin/employees/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Employee Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/employees';
						});
						
						$('#edit_employee_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_employee_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_notification', function () {

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

				let notification_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'notification_id': notification_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/notification/delete/'+notification_id,

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
});