<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi QR Code - POD</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('public/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('public/favicon.ico') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    @yield('content')
</body>
</html>
