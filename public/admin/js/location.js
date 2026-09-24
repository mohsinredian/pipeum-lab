$(document).ready(function(){
	
	// Datatable
	if($(document).find('#location_datatable').length > 0){
		$('#location_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/locations'
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
					className: "text-left"
				},
				{
					data: 'division_name',
					name: 'division_name',
					className: "text-center"
				},
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				// {
				// 	data: 'manage_floor',
				// 	name: 'manage_floor',
				// 	className: "text-center"
				// },
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
	$('#create_location_form, #edit_location_form').validate({
		rules: {
			name: {
				required: true,
				//maxlength: 250,
				
			},
			// alpha: true,
			company_id: {
				required: true,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter location name",
				//maxlength: "Location name should not be more than 250 characters",
				// alpha: "Please enter valid input",
			},
			company_id: {
				required: "Please select company",
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

	$('#create_location_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_location_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_location_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/locations/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						   
						Swal.fire({
			                text: "Location created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/locations';
						});
						
						 $('#create_location_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_location_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_location_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_location_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_location_form')[0]),
				type: 'post',
				url: "/admin/locations/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Location updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/locations';
						});
						
						$('#edit_location_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_location_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_location', function () {

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

				let location_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'location_id': location_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/locations/delete/'+location_id,

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

	$('#create_floor_plan_form').on('submit', function(e){
		// let obj ={basement:[],ground_floor:[],first_floor:[],second_floor:[],third_floor:[],fourth_floor:[],fifth_floor:[],sixth_floor:[]}
		// var basement = document.querySelectorAll("[id^='basement']");
		// //console.log(basement,"basement")

		// basement.forEach(x=>{
		// 	if(x && x.checked){
		// 		if(obj.basement)
		// 		obj.basement.push([x.value])
		// 	}
		//   });

		// var ground_floor = document.querySelectorAll("[id^='ground']");
		// ground_floor.forEach(x=>{
		// 	if(x && x.checked){
		// 		obj.ground_floor.push([x.value])
		// 	}
		//   });

		// var first_floor = document.querySelectorAll("[id^='first']");
		// first_floor.forEach(x=>{
		// 	if(x && x.checked){
		// 		obj.first_floor.push([x.value])
		// 	}
		//   });

		// var second_floor = document.querySelectorAll("[id^='second']");
		// second_floor.forEach(x=>{
		// 	if(x && x.checked){
		// 		obj.second_floor.push([x.value])
		// 	}
		//   });
		//   var third_floor = document.querySelectorAll("[id^='third']");
		//   third_floor.forEach(x=>{
		// 	  if(x && x.checked){
		// 		  obj.third_floor.push([x.value])
		// 	  }
		// 	});
		//   var fourth_floor = document.querySelectorAll("[id^='fourth']");
		//   fourth_floor.forEach(x=>{
		// 	  if(x && x.checked){
		// 		  obj.fourth_floor.push([x.value])
		// 	  }
		// 	});
		// 	var fifth_floor = document.querySelectorAll("[id^='fifth']");
		// 	fifth_floor.forEach(x=>{
		// 		if(x && x.checked){
		// 			obj.fifth_floor.push([x.value])
		// 		}
		// 	  });
		// 	  var sixth_floor = document.querySelectorAll("[id^='sixth']");
		// 	sixth_floor.forEach(x=>{
		// 		if(x && x.checked){
		// 			obj.sixth_floor.push([x.value])
		// 		}
		// 	  });
		//   console.log(obj)
		e.preventDefault();
		//$('.common-error').empty();

		if($('#create_floor_plan_form').valid()){
          let formData = new FormData($('#create_floor_plan_form')[0]);
		//   formData.append('data',JSON.stringify(obj));
		  console.log(formData)
			$('.pre-loader').show();

			$.ajax({
				// data: formData,
				data: new FormData($('#create_floor_plan_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/locations/store_floor_plan",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						   
						Swal.fire({
			                text: "Location created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/locations';
						});
						
						 $('#create_floor_plan_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_floor_plan_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
});