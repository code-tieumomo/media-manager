<div class="min-h-screen bg-gray-100 p-6" x-data="" x-on:filepond-upload-completed="$wire.saveTmpFiles()">
    <div class="bg-white p-6 rounded shadow w-full max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Media Manager</h1>
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <button wire:click="setFilter('all')" class="cursor-pointer px-3 py-1 rounded text-sm font-medium border border-gray-300 mr-2" :class="{'bg-gray-700 text-white': @js($filter) === 'all'}">All</button>
            <button wire:click="setFilter('images')" class="cursor-pointer px-3 py-1 rounded text-sm font-medium border border-gray-300 mr-2" :class="{'bg-gray-700 text-white': @js($filter) === 'images'}">Images</button>
            <button wire:click="setFilter('docs')" class="cursor-pointer px-3 py-1 rounded text-sm font-medium border border-gray-300" :class="{'bg-gray-700 text-white': @js($filter) === 'docs'}">Docs</button>
            <form wire:submit.prevent="createFolder" class="flex items-center ml-auto gap-2">
                <input type="text" wire:model.defer="newFolderName" placeholder="New folder name" class="border rounded px-2 py-1 text-sm" />
                <button type="submit" class="bg-gray-700 text-white px-3 py-1 rounded text-sm disabled:cursor-not-allowed disabled:opacity-50" :disabled="!$wire.newFolderName">Create Folder</button>
            </form>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="flex-1">
                <x-filepond::upload wire:model="tmpFiles" multiple />
            </div>
        </div>
        <div>
            <span class="text-sm text-gray-500 font-mono">
                Current Path:
                <span class="font-semibold">{{ $currentPath }}</span>
            </span>
            <div class="my-2 w-full h-px bg-gray-200"></div>
        </div>
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <h2 class="font-semibold mb-2">Folders</h2>
                <ul class="mb-4">
                    @if ($currentPath !== '/')
                        <li>
                            <button wire:click="goToFolder('{{ dirname($currentPath) }}')" class="text-gray-800 hover:underline flex gap-1 flex-1 text-left cursor-pointer">
                                <svg class="inline w-5 h-5 mr-1 text-red-500" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2 7.5V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-1.5M2 13h10"/><path d="m5 10l-3 3l3 3"/></g></svg>
                                ..
                            </button>
                        </li>
                    @endif
                    @forelse ($folders as $folder)
                        <li class="flex items-center justify-between group py-1">
                            <button wire:click="goToFolder('{{ $folder }}')" class="text-gray-800 hover:underline flex-1 text-left cursor-pointer">
                                <svg class="inline w-5 h-5 mr-1 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open"><path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/></svg>
                                {{ basename($folder) }}
                            </button>
                            <button wire:click="delete('{{ $folder }}')" class="text-red-500 ml-2" title="Delete folder">
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
                                    {{-- <img src="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($file) }}" alt="" class="w-8 h-8 object-cover rounded mr-2 border" /> --}}
                                    <svg class="size-5 mr-2 text-gray-400" viewBox="0 0 24 24"><path fill="#888888" d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5zm1-2h12l-3.75-5l-3 4L9 13zm-1 2V5zm3.5-9q.625 0 1.063-.437T10 8.5t-.437-1.062T8.5 7t-1.062.438T7 8.5t.438 1.063T8.5 10"/></svg>
                                @else
                                    <svg class="size-5 mr-2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                @endif
                                <span class="truncate">{{ basename($file) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($file) }}" target="_blank" class="text-blue-500 hover:underline text-xs">Preview</a>
                                <button wire:click="delete('{{ $file }}')" class="text-red-500 ml-2 cursor-pointer" title="Delete file">
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
    @filepondScripts
</div> 