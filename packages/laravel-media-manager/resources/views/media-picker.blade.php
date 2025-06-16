@props([
    'multiple' => false,
    'required' => false,
    'disabled' => false,
    'placeholder' => __('Select media'),
])

@php
if (! $wireModelAttribute = $attributes->whereStartsWith('wire:model')->first()) {
    throw new Exception("You must provide a wire:model.");
}
@endphp

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<div
    class="w-full bg-white rounded-lg p-2 {{ $attributes->get('class') }}"
    wire:ignore
    x-cloak
    x-init="() => {
        Fancybox.bind();

        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'media-manager-selected') {
                const urls = event.data.files;
                const container = document.getElementById('selected-images');
                container.innerHTML = '';
                $wire.set('{{ $wireModelAttribute }}', urls);
                (Array.isArray(urls) ? urls : [urls]).forEach(url => {
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'w-8 h-8 object-cover rounded border m-2 inline-block';
                    container.appendChild(img);
                });
                
                if (window.Fancybox) {
                    window.Fancybox.close();
                }
            }
        });
    }"
>
    <a
        href="{{ route('media-manager.index', ['mode' => 2]) }}"
        data-fancybox data-type="iframe"
        data-width="90vw"
        data-height="90vh"
        class="block w-full text-center p-4 border-2 border-dashed rounded-lg border-gray-400 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
    >
        <span class="text-sm text-center text-gray-400 italic">{{ $placeholder }}</span>

        <div id="selected-images" class="flex flex-wrap justify-center mt-2"></div>
    </a>
</div>
