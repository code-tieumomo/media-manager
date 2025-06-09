@if ($show)
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-lg relative">
        <h2 class="text-xl font-semibold mb-2">Select Image</h2>
        <button wire:click="close" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <div class="mb-4 flex items-center gap-2">
            @if ($currentPath !== '/')
                <button wire:click="goToFolder('{{ dirname($currentPath) }}')" class="text-blue-500 hover:underline">.. (Up)</button>
            @endif
            <span class="text-gray-500">Current: {{ $currentPath }}</span>
        </div>
        <div class="mb-4">
            <label class="block cursor-pointer">
                <span class="text-gray-500">Upload image</span>
                <input type="file" wire:model="upload" accept="image/*" class="hidden" />
                <span wire:loading wire:target="upload" class="text-blue-500 ml-2">Uploading...</span>
            </label>
        </div>
        <div class="mb-4">
            <h3 class="font-semibold mb-1">Folders</h3>
            <ul class="flex flex-wrap gap-2">
                @forelse ($folders ?? [] as $folder)
                    <li>
                        <button wire:click="goToFolder('{{ $folder }}')" class="flex items-center px-2 py-1 bg-gray-100 rounded hover:bg-blue-100">
                            <svg class="w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" /></svg>
                            {{ basename($folder) }}
                        </button>
                    </li>
                @empty
                    <li class="text-gray-400">No folders</li>
                @endforelse
            </ul>
        </div>
        <div class="grid grid-cols-3 gap-4 max-h-64 overflow-y-auto mb-2">
            @forelse ($images as $img)
                <div class="flex flex-col items-center group">
                    <img src="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($img) }}" alt="" class="w-24 h-24 object-cover rounded border mb-2 cursor-pointer group-hover:ring-2 group-hover:ring-blue-400 @if(is_array($selectedImage) && in_array(\Storage::disk(config('media-manager.disk', 'public'))->url($img), $selectedImage)) ring-2 ring-blue-500 @endif" wire:click="selectImage('{{ $img }}')" />
                    <span class="truncate text-xs max-w-[6rem]">{{ basename($img) }}</span>
                </div>
            @empty
                <div class="col-span-3 text-gray-400 text-center">No images found.</div>
            @endforelse
        </div>
        <div class="mt-4 flex justify-end gap-2">
            @if(is_array($selectedImage) && count($selectedImage) > 0)
                <button wire:click="confirmSelection" class="px-4 py-2 bg-blue-600 text-white rounded">Select ({{ count($selectedImage) }})</button>
            @endif
            <button wire:click="close" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
        </div>
    </div>
</div>
@endif 