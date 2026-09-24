$(document).ready(function () {

	// Communication modal configuration
	let redirect_flag = false,
		redirect_url = '';

	// Set Password
	$.validator.addMethod('validPassword', function (value, element) {
		return /^(?=.*[a-zA-Z])(?=\S+$).{8,15}$/.test(value);
	});



	// Login
	$('#login_form').validate({

		rules: {
			login_field: {
				required: true,
			},
			password: {
				required: true,
				minlength: 8,
				maxlength: 15
			},
		},

		messages: {
			login_field: {
				required: "Please enter your email or username",
			},
			password: {
				required: "Please enter your password",
				minlength: "Password should be at least 8 characters long",
				maxlength: "Password should be at most 15 characters long"
			}
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

	$('#login_form').on('submit', function (e) {
		e.preventDefault();
		$('.common-error').empty();

		if ($('#login_form').valid()) {

			$('.pre-loader').show();

			$.ajax({
				data: $('#login_form').serializeArray(),
				type: 'post',
				url: '/admin/login',

				success: function (response) {
					let res = response;
											console.log('successTestttttttttttttttttttttttt-----------------------------',response);

					if (res.result == 'success') {

						window.location = '/admin/dashboard';
					}
					if (res.result == 'error' && res.msg === "Your id has been blocked Please Conctacts to adminitsttion") {

                       Swal.fire({
							text: res.msg ,
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light"
						}).then(function () {
							window.location = '/user/auth';
						});					}
					
					else if (res.result == 'error') {
						let error_msgs = res.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								if (key === 'captcha') { // Check if it's the captcha error
									$('#login_form').find('.' + key + '_error').html('Invalid Captcha, Please try again');
								} else {
									$('#login_form').find('.' + key + '_error').html(error_msgs[key][0]);
								}
							}
						}
					
						$('.pre-loader').hide();
					}
					else if (res.result == 'failure') {

						Swal.fire({
							text: "Something went wrong. Please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light"
						}).then(function () {
							window.location = '/user/auth';
						});

						$('.pre-loader').hide();
					}
				},

				error: function (error) {

				}
			});
		}

	});

	// Forgot Password
	$('#kt_login_forgot_form').validate({

		rules: {
			email: {
				required: true,
				email: true
			}
		},

		messages: {
			email: {
				required: "Please enter your email",
				email: "Please enter valid email"
			}
		}
	});

	$('#kt_login_forgot_form').on('submit', function (e) {
		e.preventDefault();
		$('.common-error').empty();

		if ($('#kt_login_forgot_form').valid()) {

			$('.pre-loader').show();

			$.ajax({
				data: $('#kt_login_forgot_form').serializeArray(),
				type: 'post',
				url: "/user/process_reset_password_request",

				success: function (response) {
					let res = $.parseJSON(response);

					if (res.result == 'success') {

						Swal.fire({
							text: "We have sent you an email. Please check your inbox and follow instructions to reset the password.",
							icon: "info",
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light-primary"
						}).then(function () {
							window.location = '/user/auth';
						});

						$('#kt_login_forgot_form')[0].reset();
					}
					else if (res.result == 'error') {
						let error_msgs = res.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								$('.' + key + '_error').html(error_msgs[key][0]);
							}
						}
					}
					else if (res.result == 'failure') {

						Swal.fire({
							text: "Something went wrong. Please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light"
						}).then(function () {
							window.location = '/user/auth';
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