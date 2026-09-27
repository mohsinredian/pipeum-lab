$(document).ready(function(){

	// Datatable
	if($(document).find('#abilities_datatable').length > 0){
		$('#abilities_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: '/admin/abilities'
			},
			columns: [
				{
					data: 'id',
					name: 'id',
					className: "text-center"
				},
				{
					name: 'title',
					data: 'title'
				},
				{
					data: 'name',
					name: 'name',
				},
				{
					data: 'action',
					name: 'action',
					orderable: false,
					className: "text-center"
				}
			]
		});
	}

	// Create ability
	$('#create_ability_form, #edit_ability_form').validate({

		rules: {
			name: {
				required: true,
				maxlength: 250
			},
			title: {
				required: true,
				maxlength: 250
			}
		},

		messages: {
			name: {
				required: "Please enter ability name",
				maxlength: "Ability name should not be more than 250 characters"
			},
			title: {
				required: "Please enter title",
				maxlength: "Title should not be more than 250 characters"
			}
		}
	});

	$('#create_ability_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_ability_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: $('#create_ability_form').serializeArray(),
				type: 'post',
				url: "/admin/store_ability",

				success: function(response){
					let res = $.parseJSON(response);

					if(res.result == 'success'){
						Swal.fire({
			                text: "Ability Created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/abilities';
						});

						$('#create_ability_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_ability_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                icon: "error",
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

	// Edit ability
	$('#edit_ability_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_ability_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: $('#edit_ability_form').serializeArray(),
				type: 'post',
				url: "/admin/update_ability",

				success: function(response){
					let res = $.parseJSON(response);

					if(res.result == 'success'){

						Swal.fire({
			                text: "Ability Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/abilities';
						});
						
						$('#edit_ability_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_ability_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){

						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                icon: "error",
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
});