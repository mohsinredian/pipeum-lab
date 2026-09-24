$(document).ready(function(){
	
	if($('.select2_element').length > 0){
		$('.abilities_select2_dropdown').select2();
	}

	// Datatable
	if($(document).find('#roles_datatable').length > 0){
		$('#roles_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/roles'
			},
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
				{
					data: 'name',
					name: 'name',
				},
				{
					data: 'action',
					name: 'action',
					orderable: false
				}
			]
		});
	}

	// Create role
	$('#create_role_form, #edit_role_form').validate({

		rules: {
			name: {
				required: true,
				maxlength: 250
			},
			'abilities[]': {
				required: true
			}
		},

		messages: {
			name: {
				required: "Please enter role name",
				maxlength: "Role name should not be more than 250 characters"
			},
			'abilities[]': {
				required: "Please select abilities"
			}
		},
		errorPlacement: function(error, element){
			if(element.attr("name") == "abilities[]"){
		        // custom error placement
		        $(element).closest('.form-group').find('.common-error').html(error.text());
		    }
            else{
            	// default error placement
                element.after(error);
            }
      	}
	});

	$('#create_role_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_role_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: $('#create_role_form').serializeArray(),
				type: 'post',
				url: "/admin/store_role",

				success: function(response){
					let res = $.parseJSON(response);

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Role Created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/roles';
						});
						
						$('#create_role_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_role_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_role_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_role_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: $('#edit_role_form').serializeArray(),
				type: 'post',
				url: "/admin/update_role",

				success: function(response){
					let res = $.parseJSON(response);

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Role Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/roles';
						});
						
						$('#edit_role_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_role_form').find('.'+key+'_error').html(error_msgs[key][0]);
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

	// Role Active - In-active
	$(document).on('click', '.change_role_status', function(){

		$('.pre-loader').show();

		let role_id = $(this).attr('data-role-id');

		$.ajax({
			data: {
				'role_id': role_id,
				'_token': $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/change_role_status',

			success: function(response){
				let res = $.parseJSON(response);
				
				if(res.result == 'success'){
					Swal.fire({
		                text: "Status Updated.",
		                type: 'success',
		                buttonsStyling: false,
		                confirmButtonText: "OK",
		                confirmButtonClass: "btn font-weight-bold btn-primary"
		            }).then(function() {
						window.location.reload();
					});
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
		
	});
});