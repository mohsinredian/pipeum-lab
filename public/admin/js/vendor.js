$(document).ready(function(){
	
	// Datatable
	if($(document).find('#vendor_datatable').length > 0){
		$('#vendor_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/vendors'
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
					data: 'address',
					name: 'address',
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
	$('#create_vendor_form, #edit_vendor_form').validate({
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
				maxlength:10,
			},
			address: {
					required:true,
					maxlength:225,
			},


			status: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter vendor name",
				maxlength: "Vendor name should not be more than 50 characters",
				alpha: "Please enter valid input",
			},
			email: {
				  required: "Please enter vendor email",
				  email: "Please enter valid email",
				  maxLength: "vendor email should not be more than 50 characters",
			},
			mobile: {
				required: "Please enter vendor mobile",
				number: "Please enter valid mobile",
				minlength: "vendor name should not be less than 10 numeric",
				maxLength: "vendor name should not be more than 10 numeric",
		},
			address: {
				required: "Please enter vendor address",
				maxlength: "vendor address should not be more than 225 characters"
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

	$('#create_vendor_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_vendor_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_vendor_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/vendors/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Vendor created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/vendors';
						});
						
						 $('#create_vendor_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_vendor_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                // text: "Something went wrong. Please try again.",
							text: "Please enter unique email.",
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
	$('#edit_vendor_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_vendor_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_vendor_form')[0]),
				type: 'post',
				url: "/admin/vendors/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Vendor updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/vendors';
						});
						
						$('#edit_vendor_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_vendor_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_vendor', function () {

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

				let vendor_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'vendor_id': vendor_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/vendors/delete/'+vendor_id,

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
								confirmButtonText: "Ok",
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
	
});

