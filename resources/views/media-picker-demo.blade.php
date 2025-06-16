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
    <a class="flex border bg-white px-4 py-1 rounded-lg border-gray-200 shadow" href="{{ route('media-manager.index', ['mode' => 2, 'multiple' => false, 'data' => json_decode($selected)]) }}" data-fancybox data-type="iframe" data-width="90vw" data-height="690vh">
        Click here to chose or upload image(s)
    </a>
    <div id="selected-images" class="flex flex-wrap justify-center mt-6"></div>
    <input type="hidden" id="input_media" name="input_media" value="{{ $selected ?? '' }}" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    @livewireScripts
    <script>
        Fancybox.bind();

        // Listen for postMessage from iframe
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'media-manager-selected') {
                const urls = event.data.files;
                const container = document.getElementById('selected-images');
                container.innerHTML = '';
                (Array.isArray(urls) ? urls : [urls]).forEach(url => {
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'w-32 h-32 object-cover rounded border m-2 inline-block';
                    container.appendChild(img);
                });
                const inputMedia = document.getElementById('input_media');
                inputMedia.value = Array.isArray(urls) ? urls : [urls];
                // Optionally close Fancybox after selection
                if (window.Fancybox) {
                    window.Fancybox.close();
                }
            }
        });
    </script>
</body>

</html>
