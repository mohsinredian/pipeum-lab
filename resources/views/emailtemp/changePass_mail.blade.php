<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>Your Title Here</title> --}}
    <style>
        body {
            background-color: #ffffff;
            margin: 0 auto;
            max-width: 60%;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .container {
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 5px;
        }

        p {
            margin-bottom: 10px;
        }

        span {
            font-weight: bold;
        }

        b {
            font-weight: bold;
        }

        em {
            font-style: italic;
        }
    </style>
</head>
<body>

    <p>Dear <span>{{ $name }}</span>,</p>
  
    <div class="container">
        <p>Now your password has been changed.</p>
    </div>

    <p>Thank you,</p>
    <p><b>Admin</b></p>
    <p><em>This is an automated message, please do not reply.</em></p>

</body>
</html>
