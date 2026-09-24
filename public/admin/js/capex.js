$(document).ready(function(){

	if($(document).find('#dummy_datatable').length > 0){
		$('#dummy_datatable').DataTable({
			dom: 'Bfrtip',	
			buttons: [
				  'pdf', 'excel',
			],
			// buttons:true,
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/list-dummy-department'
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
					data: 'department',
					name: 'department',
					className: "text-center"
				},
				{
					data: 'budget_type',
					name: 'budget_type',
					className: "text-center"
				},
				{
					data: 'year',
					name: 'year',
					className: "text-center"
				},
				{
					data: 'transaction_type',
					name: 'transaction_type',
					className: "text-center"
				},
				{
					data: 'amount',
					name: 'amount',
					className: "text-center"
				},
				{
					data: 'updated_at',
					name: 'updated_at',
					className: "text-center"
				},
			
				
			],
			'columnDefs': [{ 'orderable': false, 'targets': 0 },{'visible': false, 'targets': [1], 'orderable': true}],
			'aaSorting': [[1, 'desc']]
		});
	}

	if($(document).find('#provision_datatable').length > 0){
		$('#provision_datatable').DataTable({
			dom: 'Bfrtip',	
			buttons: [
				  'pdf', 'excel',
			],
			// buttons:true,
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/list-provisional'
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
					data: 'proposal_No',
					name: 'proposal_No',
					className: "text-center"
				},
				{
					data: 'department_id',
					name: 'department_id',
					className: "text-center"
				},
				{
					data: 'service',
					name: 'service',
					className: "text-center"
				},
				{
					data: 'fiscal_year',
					name: 'fiscal_year',
					className: "text-center"
				},
				{
					data: 'budget_type',
					name: 'budget_type',
					className: "text-center"
				},
				{
					data: 'budgetary_provision',
					name: 'budgetary_provision',
					className: "text-center"
				},
				
				{
					data: 'user',
					name: 'user',
					className: "text-center"
				},
				{
					data: 'created_at',
					name: 'created_at',
					className: "text-center"
				},
				{
					data: 'total_mat_mat',
					name: 'total_mat_mat',
					className: "text-center"
				},
				{
					data: 'total_mat_mat2',
					name: 'total_mat_mat2',
					className: "text-center"
				},
				{
					data: 'total_mat_mat3',
					name: 'total_mat_mat3',
					className: "text-center"
				},
			],
			'columnDefs': [{ 'orderable': false, 'targets': 0 },{'visible': false, 'targets': [1], 'orderable': true}],
			'aaSorting': [[1, 'desc']]
		});
	}
	
	// Datatable
	if($(document).find('#capex_datatable').length > 0){
		$('#capex_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/capexmaster'
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
					data: 'super_department',
					name: 'super_department',
					className: "text-center"
				},
				{
					data: 'department_id',
					name: 'department_id',
					className: "text-center"
				},
				{
					data: 'head',
					name: 'head',
					className: "text-center"
				},
				{
					data: 'sub_head',
					name: 'sub_head',
					className: "text-center"
				},
				{
					data: 'brp_head',
					name: 'brp_head',
					className: "text-center"
				},
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				{
					data: 'capx_fy_one',
					name: 'capx_fy_one',
					className: "text-center",
					render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
				  },
				  {
					data: 'capx_fy_two',
					name: 'capx_fy_two',
					className: "text-center",
					render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
				  },
				  {
					data: 'capx_fy_one1',
					name: 'capx_fy_one1',
					className: "text-center",
					render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
				  },
				  {
					data: 'capx_fy_two1',
					name: 'capx_fy_two1',
					className: "text-center",
					render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
				  },
				  {
					data: 'capx_fy_one2',
					name: 'capx_fy_one2',
					className: "text-center",
					render: function (data, type, row) {
					  
					  return data.toLocaleString('en-IN');
					}
				  },
				  {
					data: 'capx_fy_two2',
					name: 'capx_fy_two2',
					className: "text-center",
					render: function (data, type, row) {
					  
					  return data.toLocaleString('en-IN');
					}
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
	$('#create_capex_form, #edit_capex_form').validate({
		rules: {
			department_id:{
				required: true,
			},
			head:{
				required: true,
			},
			super_department: {
				required: true,
			  },
			sub_head: {
				required: true,
				maxlength: 250,
				//alpha: true,
			},
			brp_head: {
				required: true,
			},
			status: {
				required: true,
			},
			capx_fy_one: {
				// required: true,
				maxlength: 250,
				//alpha: true,
			},
			capx_fy_two: {
				// required: true,
				maxlength: 250,
			},
			
		},
		messages: {
			department_id:{
				required: 'Please select department',
			},
			head:{
				required: 'Please select head',
			},
			super_department: {
				required: "Please select Super Department",
			  },
			sub_head: {
				required: "Please enter Sub Head",
				maxlength: "Sub head should not be more than 250 characters",
			},
			brp_head: {
				required: 'Please select BPR Head',
			},
			status: {
				required: 'Please select Regular Capex/Project Capex '
			},
			// capx_fy_one: {
			// 	required: "Please enter Capex FY 24 (W/O OH & INt)",
			// 	maxlength: "Capex FY 24 (W/O OH & INt) should not be more than 50 characters",
			// },
			// capx_fy_two: {
			// 	required: "Please enter Capex FY 24 (With OH & INt)",
			// 	maxlength: "Capex FY 24 (With OH & INt) should not be more than 50 characters",
			// },
		},
		errorPlacement: function (error, element) {
			if (element.attr("name") == "logo") {
			  // custom error placement
			  $(element)
				.closest(".form-group")
				.find(".common-error")
				.html(error.text());
			} else {
			  // default error placement
			  element.after(error);
			}
		  },
	});

	$('#create_capex_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_capex_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_capex_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/capexmaster/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Capex created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/capexmaster';
						});
						
						 $('#create_capex_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_capex_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_capex_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_capex_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_capex_form')[0]),
				type: 'post',
				url: "/admin/capexmaster/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Capex updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/capexmaster';
						});
						
						$('#edit_capex_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_capex_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
//import
$(document).ready(function() { 
	$('#file-upload').on('change', () => {
	  $.ajax({
		url: '/admin/capexmaster/upload',
		type: 'POST',
		data: new FormData($('#upload-form')[0]),
		dataType: 'json',
		processData: false,
		contentType: false,
		success: function(data) {
		  console.log(data);
		  swal({
			title: "Success!",
			text: "Your file has been Imported.",
			icon: "success",
			button: "OK"
		  });
		},
		error: function(xhr, textStatus, errorThrown) {
		  console.error('There was a problem with the ajax operation:', errorThrown);
		  swal({
			title: "Error!",
			text: 'Invalid File Format, Please select valid format file' ,
			icon: "error",
			button: "OK"
		  });
		}
	  });
	});
  });

$(document).ready(function () {

    $('#super_department').change(function () {

        let selected = $('#deps').val() || "";
        let selectedArray = selected !== "" ? selected.split(',').map(v => v.trim()) : [];

        let selectedDepartment = $(this).val();

        let url = selectedDepartment !== ''
            ? '/admin/getSubDepartments_capex/' + selectedDepartment
            : '/admin/getSubDepartments1_capex';

        console.log("Calling URL =>", url);

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',

            success: function (response) {

                console.log("Response =>", response.data);

                $('#department_id').empty();
                $('#department_id').append('<option value="">Select Sub-Department</option>');

                if (response.data && response.data.length > 0) {

                    $.each(response.data, function (key, value) {

                        // If no selected previous OR not in selected list → show item
                        if (selectedArray.length === 0 ||
                            !selectedArray.includes(value.id.toString())) {

                            $('#department_id').append(
                                `<option value="${value.id}">${value.name}</option>`
                            );
                        }
                    });
                } 
                
                // refresh if using Select2
                $('#department_id').trigger('change');
            },

            error: function (xhr) {
                console.log("AJAX ERROR:", xhr.responseText);
            }
        });

    });

});

