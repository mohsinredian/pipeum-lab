$(document).ready(function(){
	
	// Datatable
	if($(document).find('#circle_datatable').length > 0){
		$('#circle_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/circles'
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
	$('#create_circle_form, #edit_circle_form').validate({
		rules: {
			name: {
				required: true,
				maxlength: 50,
				//alpha: true,
			},


			status: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter circle name",
				maxlength: "Circle name should not be more than 50 characters",
				//alpha: "Please enter valid input",
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

	$('#create_circle_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_circle_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_circle_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/circles/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Circle created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/circles';
						});
						
						 $('#create_circle_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_circle_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_circle_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_circle_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_circle_form')[0]),
				type: 'post',
				url: "/admin/circles/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Circle updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/circles';
						});
						
						$('#edit_circle_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_circle_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_circle', function () {

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

				let circle_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'circle_id': circle_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/circles/delete/'+circle_id,

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