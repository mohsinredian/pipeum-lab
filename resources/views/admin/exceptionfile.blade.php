<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Include SweetAlert library from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>

<!-- Your page content goes here -->

<script>
    // Add a function to be called when the window is loaded
    window.onload = function() {
        // Display SweetAlert on window load
        Swal.fire({
            text: 'Please Contact to Admin',
            icon: 'warning',
            confirmButtonText: 'Go to Login'
        }).then(function() {
			window.location.href = "/admin/auth";
		});
    };
</script>

</body>
</html>
