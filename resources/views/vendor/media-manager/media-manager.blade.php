<div class="min-h-screen bg-gray-100 p-6">
    <div class="bg-white p-6 rounded shadow w-full max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Media Manager</h1>
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <button wire:click="setFilter('all')" class="px-3 py-1 rounded text-sm font-medium border border-gray-300 mr-2" :class="{'bg-blue-100 text-blue-700': @js($filter) === 'all'}">All</button>
            <button wire:click="setFilter('images')" class="px-3 py-1 rounded text-sm font-medium border border-gray-300 mr-2" :class="{'bg-blue-100 text-blue-700': @js($filter) === 'images'}">Images</button>
            <button wire:click="setFilter('docs')" class="px-3 py-1 rounded text-sm font-medium border border-gray-300" :class="{'bg-blue-100 text-blue-700': @js($filter) === 'docs'}">Docs</button>
            <form wire:submit.prevent="createFolder" class="flex items-center ml-auto gap-2">
                <input type="text" wire:model.defer="newFolderName" placeholder="New folder name" class="border rounded px-2 py-1 text-sm" />
                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Create Folder</button>
            </form>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="flex-1">
                <div class="border-2 border-dashed border-gray-300 rounded p-4 flex flex-col items-center justify-center cursor-pointer bg-gray-50 hover:bg-gray-100"
                    x-data="{ isDropping: false }"
                    @dragover.prevent="isDropping = true"
                    @dragleave.prevent="isDropping = false"
                    @drop.prevent="isDropping = false"
                >
                    <label class="cursor-pointer w-full flex flex-col items-center">
                        <span class="text-gray-500 mb-2">Drag & drop files here or click to browse</span>
                        <input type="file" wire:model="upload" multiple class="hidden" />
                        <span wire:loading wire:target="upload" class="text-blue-500 mt-2">Uploading...</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <h2 class="font-semibold mb-2">Folders</h2>
                <ul class="mb-4">
                    @if ($currentPath !== '/')
                        <li>
                            <button wire:click="goToFolder('{{ dirname($currentPath) }}')" class="text-blue-500 hover:underline">.. (Up)</button>
                        </li>
                    @endif
                    @forelse ($folders as $folder)
                        <li class="flex items-center justify-between group py-1">
                            <button wire:click="goToFolder('{{ $folder }}')" class="text-gray-800 hover:underline flex-1 text-left">
                                <svg class="inline w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" /></svg>
                                {{ basename($folder) }}
                            </button>
                            <button wire:click="delete('{{ $folder }}')" class="text-red-500 opacity-0 group-hover:opacity-100 ml-2" title="Delete folder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </li>
                    @empty
                        <li class="text-gray-400">No folders</li>
                    @endforelse
                </ul>
                <h2 class="font-semibold mb-2">Files</h2>
                <ul>
                    @forelse ($files as $file)
                        <li class="flex items-center justify-between group py-1">
                            <div class="flex items-center">
                                @php $mime = \Storage::disk(config('media-manager.disk', 'public'))->mimeType($file); @endphp
                                @if (str_starts_with($mime, 'image/'))
                                    <img src="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($file) }}" alt="" class="w-8 h-8 object-cover rounded mr-2 border" />
                                @else
                                    <svg class="w-6 h-6 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7v10M17 7v10M5 7h14M5 17h14" /></svg>
                                @endif
                                <span class="truncate max-w-xs">{{ basename($file) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($file) }}" target="_blank" class="text-blue-500 hover:underline text-xs">Preview</a>
                                <button wire:click="delete('{{ $file }}')" class="text-red-500 opacity-0 group-hover:opacity-100 ml-2" title="Delete file">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-400">No files</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div> 