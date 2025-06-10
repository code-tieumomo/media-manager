@php($selected = null)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Picker Demo</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded shadow w-full max-w-lg mt-10">
        <h1 class="text-2xl font-bold mb-4">Media Picker Demo</h1>
        <livewire:media-manager.media-picker />
        <div id="selected-images" class="mt-6"></div>
    </div>
    @livewireScripts
    <script>
        // document.addEventListener('livewire:load', function () {
        //     window.Livewire.on('mediaPickerSelected', function (urls) {
        //         const container = document.getElementById('selected-images');
        //         container.innerHTML = '';
        //         (Array.isArray(urls) ? urls : [urls]).forEach(url => {
        //             const img = document.createElement('img');
        //             img.src = url;
        //             img.className = 'w-32 h-32 object-cover rounded border m-2 inline-block';
        //             container.appendChild(img);
        //         });
        //     });
        // });
    </script>
</body>
</html> 