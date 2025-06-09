<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Manager</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html> 