<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            @foreach($issueDetails as $issueDetail)
            <div class="card-body">
                <figure class="text-center">
                <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')->generate($issueDetail->reference_id)) !!}">
                <figcaption style="font-weight: 400;font-size:20px">{{ $issueDetail->reference_id }}</figcaption>
            </figure>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>