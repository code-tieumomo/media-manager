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
    <a href="{{ route('media-manager.index') }}" data-fancybox data-type="iframe" data-width="900" data-height="600">
        Custom width and height using data attributes
    </a>

    <div class="bg-white p-8 rounded shadow w-full max-w-lg mt-10">
        <h1 class="text-2xl font-bold mb-4">Media Picker Demo</h1>
        <livewire:media-manager.media-picker />
        <div id="selected-images" class="mt-6"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    @livewireScripts
    <script>
        Fancybox.bind();

        document.addEventListener('livewire:load', function() {
            window.Livewire.on('mediaPickerSelected', function(urls) {
                const container = document.getElementById('selected-images');
                container.innerHTML = '';
                (Array.isArray(urls) ? urls : [urls]).forEach(url => {
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'w-32 h-32 object-cover rounded border m-2 inline-block';
                    container.appendChild(img);
                });
            });
        });
    </script>
</body>

</html>
