<div class="min-h-screen bg-gray-100 p-6" x-data="{
    mode2: new URLSearchParams(window.location.search).get('mode') === '2',
    sourceId: new URLSearchParams(window.location.search).get('source_id') || null,
    selectedFiles: [],
    expandedFolders: @js($expandedFolders),
    getFileUrl(file) {
        // This must match the Blade logic for file URL
        return '{{ rtrim(Storage::disk(config('media-manager.disk', 'public'))->url('')) }}' + '/' + file.replace(/^\/+/, '');
    },
    submitFiles() {
        const urls = this.selectedFiles.map(f => this.getFileUrl(f));
        window.parent.postMessage({ type: 'media-manager-selected', files: urls }, '*');
    },
    isExpanded(path) {
        return this.expandedFolders.includes(path);
    }
}" x-on:filepond-upload-completed="$wire.saveTmpFiles()">
    <div class="bg-white p-6 rounded-lg shadow w-full container mx-auto">
        <div class="flex flex-wrap max-md:justify-center items-center gap-2 mb-4">
            <button wire:click="setFilter('all')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 mr-2 flex items-center gap-1"
                :class="{ 'bg-gray-700 border-gray-700 text-white': @js($filter) === 'all' }">
                All
            </button>
            <button wire:click="setFilter('images')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 mr-2 flex items-center gap-1"
                :class="{ 'bg-gray-700 border-gray-700 text-white': @js($filter) === 'images' }">
                <svg class="size-4" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M5 19h14V5H5zm4-5.86l2.14 2.58l3-3.87L18 17H6z" opacity=".3" />
                    <path fill="currentColor"
                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m0 16H5V5h14zm-4.86-7.14l-3 3.86L9 13.14L6 17h12z" />
                </svg>
                Images
            </button>
            <button wire:click="setFilter('docs')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 flex items-center gap-1"
                :class="{ 'bg-gray-700 border-gray-700 text-white': @js($filter) === 'docs' }">
                <svg class="size-4" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M7 6v12h10V6zm8 10H9v-2h6zm0-3H9v-2h6zm0-3H9V8h6z" opacity=".3" />
                    <path fill="currentColor"
                        d="M7 3H4v3H2V1h5zm15 3V1h-5v2h3v3zM7 21H4v-3H2v5h5zm13-3v3h-3v2h5v-5zM17 6H7v12h10zm2 12c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2zM15 8H9v2h6zm0 3H9v2h6zm0 3H9v2h6z" />
                </svg>
                Docs
            </button>

            <form wire:submit.prevent="createFolder" class="max-md:hidden md:flex items-center ml-auto gap-2">
                <input type="text" wire:model.defer="newFolderName" placeholder="New folder name"
                    class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-black focus:outline-none focus:ring-offset-1" />
                <button type="submit"
                    class="bg-gray-700 text-white px-3 py-1 rounded-md text-sm cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 flex items-center gap-1"
                    :disabled="!$wire.newFolderName">
                    <svg class="size-4" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="m11.17 8l-.59-.59L9.17 6H4v12h16V8zM14 10h2v2h2v2h-2v2h-2v-2h-2v-2h2z" opacity=".3" />
                        <path fill="currentColor"
                            d="M20 6h-8l-2-2H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2m0 12H4V6h5.17l1.41 1.41l.59.59H20zm-8-4h2v2h2v-2h2v-2h-2v-2h-2v2h-2z" />
                    </svg>
                    Create Folder
                </button>
            </form>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mb-4 min-h-[5.75rem]">
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
            <div class="md:min-w-xs w-full md:w-64">
                <h2 class="font-semibold mb-2">Directory Tree</h2>
                <div class="bg-[#f1f0ef] rounded-lg p-3 max-h-96 overflow-y-auto">
                    <!-- Root folder -->
                    <div class="mb-1">
                        <button wire:click="goToFolder('/')" 
                                class="flex items-center text-gray-700 hover:text-gray-900 text-sm py-1 px-1 rounded hover:bg-gray-50 w-full text-left transition-colors cursor-pointer {{ $currentPath === '/' ? 'text-gray-600 bg-gray-50' : '' }}">
                            <svg class="size-5 mr-1" viewBox="0 0 24 24"><g fill="none"><path fill="url(#fluentColorDocumentFolder240)" d="M8 6.25A2.25 2.25 0 0 1 10.25 4h7.5A2.25 2.25 0 0 1 20 6.25v8.5A2.25 2.25 0 0 1 17.75 17h-7.5A2.25 2.25 0 0 1 8 14.75z"/><path fill="url(#fluentColorDocumentFolder241)" d="M8 6.25A2.25 2.25 0 0 1 10.25 4h7.5A2.25 2.25 0 0 1 20 6.25v8.5A2.25 2.25 0 0 1 17.75 17h-7.5A2.25 2.25 0 0 1 8 14.75z"/><path fill="url(#fluentColorDocumentFolder243)" d="M4 4.25A2.25 2.25 0 0 1 6.25 2h9a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 15.25 17h-9A2.25 2.25 0 0 1 4 14.75z"/><path fill="url(#fluentColorDocumentFolder242)" d="M5.25 8A2.25 2.25 0 0 0 3 10.25v8.5A3.25 3.25 0 0 0 6.25 22h11.5A3.25 3.25 0 0 0 21 18.75v-1.5A2.25 2.25 0 0 0 18.75 15h-2.846a.75.75 0 0 1-.55-.24l-5.61-6.04A2.25 2.25 0 0 0 8.097 8z"/><defs><linearGradient id="fluentColorDocumentFolder240" x1="21.8" x2="23.639" y1="19.5" y2="5.773" gradientUnits="userSpaceOnUse"><stop stop-color="#BB45EA"/><stop offset="1" stop-color="#9C6CFE"/></linearGradient><linearGradient id="fluentColorDocumentFolder241" x1="20" x2="17" y1="8.5" y2="8.5" gradientUnits="userSpaceOnUse"><stop offset=".338" stop-color="#5750E2" stop-opacity="0"/><stop offset="1" stop-color="#5750E2"/></linearGradient><linearGradient id="fluentColorDocumentFolder242" x1="6.857" x2="6.857" y1="8" y2="27.091" gradientUnits="userSpaceOnUse"><stop offset=".241" stop-color="#FFD638"/><stop offset=".637" stop-color="#FAB500"/><stop offset=".985" stop-color="#CA6407"/></linearGradient><radialGradient id="fluentColorDocumentFolder243" cx="0" cy="0" r="1" gradientTransform="matrix(8.775 -11.5 18.53666 14.14428 8.05 14)" gradientUnits="userSpaceOnUse"><stop offset=".228" stop-color="#2764E7"/><stop offset=".685" stop-color="#5CD1FF"/><stop offset="1" stop-color="#6CE0FF"/></radialGradient></defs></g></svg>
                            <span class="font-medium">Root</span>
                        </button>
                    </div>
                    
                    @if(!empty($directoryTree))
                        @include('media-manager::partials.directory-tree', ['tree' => $directoryTree, 'level' => 0])
                    @else
                        <p class="text-gray-400 text-sm">No subdirectories found</p>
                    @endif
                </div>
            </div>
            <div class="flex-1">
                <h2 class="font-semibold mb-2">
                    Folders ({{ count($folders) }})
                </h2>
                <div class="mb-8 grid grid-cols-8 gap-4">
                    @forelse ($folders as $folder)
                    <div class="flex items-center justify-between group py-1">
                        <button wire:click="goToFolder('{{ $folder }}')"
                            class="text-gray-800 hover:underline flex-1 cursor-pointer flex flex-col items-center group-hover:bg-[#f1f0ef] rounded p-2  transition-colors {{ $currentPath === $folder ? 'bg-gray-50 font-medium' : '' }}">
                            <svg class="size-20" viewBox="0 0 24 24"><g fill="none"><path fill="url(#fluentColorDocumentFolder240)" d="M8 6.25A2.25 2.25 0 0 1 10.25 4h7.5A2.25 2.25 0 0 1 20 6.25v8.5A2.25 2.25 0 0 1 17.75 17h-7.5A2.25 2.25 0 0 1 8 14.75z"/><path fill="url(#fluentColorDocumentFolder241)" d="M8 6.25A2.25 2.25 0 0 1 10.25 4h7.5A2.25 2.25 0 0 1 20 6.25v8.5A2.25 2.25 0 0 1 17.75 17h-7.5A2.25 2.25 0 0 1 8 14.75z"/><path fill="url(#fluentColorDocumentFolder243)" d="M4 4.25A2.25 2.25 0 0 1 6.25 2h9a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 15.25 17h-9A2.25 2.25 0 0 1 4 14.75z"/><path fill="url(#fluentColorDocumentFolder242)" d="M5.25 8A2.25 2.25 0 0 0 3 10.25v8.5A3.25 3.25 0 0 0 6.25 22h11.5A3.25 3.25 0 0 0 21 18.75v-1.5A2.25 2.25 0 0 0 18.75 15h-2.846a.75.75 0 0 1-.55-.24l-5.61-6.04A2.25 2.25 0 0 0 8.097 8z"/><defs><linearGradient id="fluentColorDocumentFolder240" x1="21.8" x2="23.639" y1="19.5" y2="5.773" gradientUnits="userSpaceOnUse"><stop stop-color="#BB45EA"/><stop offset="1" stop-color="#9C6CFE"/></linearGradient><linearGradient id="fluentColorDocumentFolder241" x1="20" x2="17" y1="8.5" y2="8.5" gradientUnits="userSpaceOnUse"><stop offset=".338" stop-color="#5750E2" stop-opacity="0"/><stop offset="1" stop-color="#5750E2"/></linearGradient><linearGradient id="fluentColorDocumentFolder242" x1="6.857" x2="6.857" y1="8" y2="27.091" gradientUnits="userSpaceOnUse"><stop offset=".241" stop-color="#FFD638"/><stop offset=".637" stop-color="#FAB500"/><stop offset=".985" stop-color="#CA6407"/></linearGradient><radialGradient id="fluentColorDocumentFolder243" cx="0" cy="0" r="1" gradientTransform="matrix(8.775 -11.5 18.53666 14.14428 8.05 14)" gradientUnits="userSpaceOnUse"><stop offset=".228" stop-color="#2764E7"/><stop offset=".685" stop-color="#5CD1FF"/><stop offset="1" stop-color="#6CE0FF"/></radialGradient></defs></g></svg>
                            {{ basename($folder) }}
                        </button>
                    </div>
                    @empty
                    <div class="text-gray-400">No folders</div>
                    @endforelse
                </div>
                <h2 class="font-semibold mb-2">
                    Files ({{ count($files) }})
                </h2>
                <form x-show="mode2" x-cloak @submit.prevent="submitFiles" class="mb-4">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm mb-2">Submit Selected
                        Files</button>
                </form>
                <div class="mb-8 grid grid-cols-8 gap-4">
                    @forelse ($files as $file)
                    @php 
                        $mime = \Storage::disk(config('media-manager.disk', 'public'))->mimeType($file);
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $url = \Storage::disk(config('media-manager.disk', 'public'))->url($file);
                    @endphp
                    <div class="relative flex flex-col items-center group bg-white rounded p-2 hover:bg-[#f1f0ef] transition-colors">
                        <span class="absolute top-1 right-2 bg-gray-800 text-white text-[10px] px-2 py-0.5 rounded-full uppercase z-10">{{ $ext }}</span>
                        <div class="flex flex-col items-center w-full">
                            <template x-if="mode2">
                                <input type="checkbox" class="mr-2 mb-1" :value="'{{ $file }}'" x-model="selectedFiles">
                            </template>
                            @if (str_starts_with($mime, 'image/'))
                                <a href="{{ $url }}" target="_blank" class="block mb-2">
                                    <img src="{{ $url }}" alt="{{ basename($file) }}" class="object-cover size-20 rounded w-full border border-gray-200 shadow-sm hover:scale-105 transition-transform" loading="lazy">
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" class="block mb-2">
                                    <svg class="size-20" viewBox="0 0 24 24"><g fill="none"><path fill="url(#fluentColorDocument240)" d="M6 22h12a2 2 0 0 0 2-2V9l-5-2l-2-5H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2"/><path fill="url(#fluentColorDocument242)" fill-opacity=".5" d="M6 22h12a2 2 0 0 0 2-2V9l-5-2l-2-5H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2"/><path fill="url(#fluentColorDocument241)" d="M13 7.5V2l7 7h-5.5A1.5 1.5 0 0 1 13 7.5"/><defs><linearGradient id="fluentColorDocument240" x1="15.2" x2="16.822" y1="2" y2="18.87" gradientUnits="userSpaceOnUse"><stop stop-color="#6CE0FF"/><stop offset="1" stop-color="#4894FE"/></linearGradient><linearGradient id="fluentColorDocument241" x1="16.488" x2="14.738" y1="4.917" y2="7.833" gradientUnits="userSpaceOnUse"><stop stop-color="#9FF0F9"/><stop offset="1" stop-color="#B3E0FF"/></linearGradient><radialGradient id="fluentColorDocument242" cx="0" cy="0" r="1" gradientTransform="matrix(-8.66665 9.09357 -5.3691 -5.11703 20.667 2.625)" gradientUnits="userSpaceOnUse"><stop offset=".362" stop-color="#4A43CB"/><stop offset="1" stop-color="#4A43CB" stop-opacity="0"/></radialGradient></defs></g></svg>
                                </a>
                            @endif
                            <div class="truncate w-full text-center text-xs mt-1">{{ basename($file) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-gray-400">No files</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @filepondScripts
</div>