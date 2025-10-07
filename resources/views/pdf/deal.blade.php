<!DOCTYPE html>
<html>

<head>
    <title>Deal Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h1>Deal Details</h1>
    @foreach ($deal as $d)
        <p><strong>Title:</strong> {{ $d->title }}</p>
        <p><strong>Description:</strong> {{ $d->description }}</p>
    @endforeach

    <div class="qr-code">
        <h3>QR Code</h3>
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
    </div>
</body>

</html>
