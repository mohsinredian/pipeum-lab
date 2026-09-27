
$(document).ready(function(){
	
	// Datatable
	if($(document).find('#employee_datatable').length > 0){
		$('#employee_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			searching: true,
			// dom: 'Bfrtip',
			// buttons: [
			// 	'excel', 'pdf'
			// ],
			ajax: {
				url: '/admin/employees'
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
					data: 'name',
					name: 'name',
					className: "text-center"
				},
				{
					data: 'email',
					name: 'email',
					className: "text-center"
				},
				{
					data: 'phone',
					name: 'phone',
					className: "text-center"
				},
				
				{
					data: 'department_id',
					name: 'department_id',
					className: "text-center",
				},
				{
					data: 'design',
					name: 'design',
					className: "text-center",
				},
				
				
				{
					data: 'employee_id',
					name: 'emp_id',
					
					className: "text-center"
				},
					{
					data: 'role',
					name: 'role',
					className: "text-center"
				},
				
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				{
					data: 'last_login_at',
					name: 'last_login_at',
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
		return this.optional(element) || /^[a-zA-Z0-9\s']*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("alphadash", function (value, element) {
		return this.optional(element) || /^[a-zA-Z \s']*$/.test(value);
	}, "Invalid input");
	// Create role

	$.validator.addMethod("passwordValidation", function(value, element) {
		const lengthPattern = /^.{8,}$/;
		const uppercasePattern = /[A-Z]/;
		const lowercasePattern = /[a-z]/;
		const specialCharacterPattern = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/;
	
		return lengthPattern.test(value) && uppercasePattern.test(value) && lowercasePattern.test(value) && specialCharacterPattern.test(value);
	}, "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one special character (!@#$%^&*()_+{}[]:;<>,.?~-).");

	$.validator.addMethod("uniqueMobileNumber", function(value, element) {
		var isUnique = true;
	
		$.ajax({
			cache: false,
			processData: false,
			contentType: false,
			type: 'post',
			url: "/admin/employees/store",
			data: {
				phone: value
			},
			async: false,
			success: function(response) {
				isUnique = response === 'true' ? false : true;
			}
		});
	
		return isUnique;
	},);
	
	$.validator.addMethod("uniqueEmployeeId", function(value, element) {
		var isUnique = true;
	
		$.ajax({
			cache: false,
			processData: false,
			contentType: false,
			type: 'post',
			url: "/admin/employees/store",
			data: {
				employee_id: value
			},
			async: false,
			success: function(response) {
				isUnique = response === 'true' ? false : true;
			}
		});
	
		return isUnique;
	}, );
	

	$('#create_employee_form').validate({
		
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
			phone: {
				required: true,
				number: true,
				minlength: 10,
				maxlength: 15,
				
			},
			division: {
				required:true,
			},
		
			'department_id[]': {
				required: true
			  },
			role_id: {
				required:true,
			},
			password: {
				required: true,
				passwordValidation: true,
			},
			location:{
				required:true,
			},
			employee_id: {
				required: true,
				alphanumeric: true,
				
			},
			designation: {
				required: true,
				
			},
			// report_to: {
			// 	required: true,
			// 	alphanumeric:true,
				
			// },
			rights: {
				required: true,
			},
			status: {
				required: true,
			},
			super_department: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter employee name",
				maxlength: "Employee name should not be more than 50 characters",
				alpha: "Please enter valid input",
			},
			
			email: {
				  required: "Please enter email",
				  email: "Please enter valid email",
				  maxLength: "email should not be more than 50 characters",
			},
			phone: {
				required: "Please enter mobile number",
				number: "Please enter valid mobile number",
				minlength: "Mobile number should not be less than 10 digits",
				maxlength: "Mobile number should not be more than 15 digits",
				
			},
			division: {
				required: "Please select company",
			},
			designation: {
				required: "Please select Designation",
			},
			super_department: {
				required: "Please select Super Department",
			},
			password: {
				required: "Please enter a password",
				passwordValidation: "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one special character (!@#$%^&*()_+{}[]:;<>,.?~-).",
			},
			'department_id[]': {
				required: "Please select at least one department",
				minlength: "Please select at least one department",
			},
			location: {
				required: "Please select location",
			},
			employee_id: {
				required: "Please enter employee id",
				alphanumeric: "Please enter valid input",
				
			},	
			role_id: {
				required: "Please select role",
			},
		
			rights: {
				required: 'Please select Access Right',
			},
			status: {
				required: 'Please select status',
			},
		},

		success: function (label, element) {
			$(element).closest('.form-group').find('.common-error').html('');
		},
		errorPlacement: function (error, element) {
			if (element.attr("name") == "logo") {
				$(element).closest('.form-group').find('.common-error').html(error.text());
			} else if (element.attr("name") == "department_id[]") {
				// Add error message after the span element
				error.insertAfter(element.closest('.form-group').find('.select2-container'));
			} else {
				element.after(error);
			}
		}
		
	});
	
	$('#edit_employee_form').validate({
		
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
			phone: {
				required: true,
				number: true,
				minlength: 10,
				maxlength: 15,
				
			},
			division: {
				required:true,
			},
			designation: {
				required: "Please select Designation",
			},
		
			'department_id[]': {
				required: true
			  },
			role_id: {
				required:true,
			},
			super_department: {
				required: true,
			},
			
			location:{
				required:true,
			},
			employee_id: {
				required: true,
				alphanumeric: true,
				
			},
			// report_to: {
			// 	required: true,
			// 	alphanumeric:true,
				
			// },
			rights: {
				required: true,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter employee name",
				maxlength: "Employee name should not be more than 50 characters",
				alpha: "Please enter valid input",
			},
			
			email: {
				  required: "Please enter email",
				  email: "Please enter valid email",
				  maxLength: "email should not be more than 50 characters",
			},
			phone: {
				required: "Please enter mobile number",
				number: "Please enter valid mobile number",
				minlength: "Mobile number should not be less than 10 digits",
				maxlength: "Mobile number should not be more than 15 digits",
				
			},
			division: {
				required: "Please select company",
			},
			super_department: {
				required: "Please select Super Department",
			},
			designation: {
				required: "Please select Designation",
			},
			
			'department_id[]': {
				required: "Please select at least one department",
				minlength: "Please select at least one department",
			},
			location: {
				required: "Please select location",
			},
			employee_id: {
				required: "Please enter employee id",
				alphanumeric: "Please enter valid input",
				
			},	
			role_id: {
				required: "Please select role",
			},
			// report_to: {
			// 	required: "Please select Report To",
			// },
			rights: {
				required: 'Please select Access Right',
			},
			status: {
				required: 'Please select status',
			},
		},

		success: function (label, element) {
			$(element).closest('.form-group').find('.common-error').html('');
		},
		errorPlacement: function (error, element) {
			if (element.attr("name") == "logo") {
				$(element).closest('.form-group').find('.common-error').html(error.text());
			} else if (element.attr("name") == "department_id[]") {
				// Add error message after the span element
				error.insertAfter(element.closest('.form-group').find('.select2-container'));
			} else {
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
			                text: "New Employee Created Succesfully!",
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
				 headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
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
	
	$(document).on('click', '.delete_employee', function () {

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

				let employee_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'employee_id': employee_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/employees/delete/'+employee_id,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {

							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok",
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


	// $('#division').on('change', function () {

	// 	division = $(this).val();
	// 	$('.pre-loader').show();
	// 	$.ajax({
	// 		data: {
	// 			division: division,
	// 			_token: $('input[name="_token"]').val()
	// 		},
	// 		type: 'post',
	// 		url: '/admin/employees/location',
	// 		async: false,
	// 		success: function (response) {
	// 			if (response.result == 'success') {
	// 				let locations = response.data;
    //                 $("#location").empty();
    //                 $("#location").append(
    //                     '<option value="">Select location</option>'
    //                 );

    //                 $.each(locations, function (i, location) {
    //                     $("#location").append(
    //                         $("<option></option>")
    //                             .attr("value", location.id)
    //                             .text(location.name)
    //                     );
    //                 });
	// 			}
	// 			else if (response.result == 'failure') {
	// 				Swal.fire({
	// 					text: "Something went wrong. Please try again.",
	// 					type: 'error',
	// 					buttonsStyling: false,
	// 					confirmButtonText: "OK",
	// 					confirmButtonClass: "btn font-weight-bold btn-light"
	// 				}).then(function () {
	// 					window.location.reload();
	// 				});
	// 			}

	// 			$('.pre-loader').hide();
	// 		},

	// 		error: function (error) {

	// 		}
	// 	});
	// });


	$('#super_department').change(function() {
		var selectedDepartment = $(this).val();
			$.ajax({
				type: 'GET',
				url: '/admin/getSubDepartments_employee/' + selectedDepartment,
				success: function(response) {
					$('#departments').empty();
					
					var data = response.data;
					
				
                   if(response.data && response.data.length > 0)
                   {
					$.each(response.data, function (key, value) {
						$('#departments').append('<option value="' + value.id + '">' + value.name + '</option>');
					
					});
                   }
                   {
                    $.each(response.department, function(key, value) {
                      
						$('#departments').append('<option value="">Select Sub-Department</option>');
					});
                   }
					
				}
			});
	
	});
});