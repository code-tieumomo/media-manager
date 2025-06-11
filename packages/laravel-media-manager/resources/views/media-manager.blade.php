<div class="min-h-screen bg-gray-100 p-6" x-data="{
    mode2: new URLSearchParams(window.location.search).get('mode') === '2',
    sourceId: new URLSearchParams(window.location.search).get('source_id') || null,
    selectedFiles: [],
    getFileUrl(file) {
        // This must match the Blade logic for file URL
        return '{{ rtrim(Storage::disk(config('media-manager.disk', 'public'))->url('')) }}' + '/' + file.replace(/^\/+/, '');
    },
    submitFiles() {
        const urls = this.selectedFiles.map(f => this.getFileUrl(f));
        window.parent.postMessage({ type: 'media-manager-selected', files: urls }, '*');
    }
}" x-on:filepond-upload-completed="$wire.saveTmpFiles()">
    <div class="bg-white p-6 rounded-lg shadow w-full container mx-auto">
        <div class="flex flex-wrap max-md:justify-center items-center gap-2 mb-4">
            <button wire:click="setFilter('all')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 mr-2 flex items-center gap-1"
                :class="{ 'bg-gray-700 text-white': @js($filter) === 'all' }">
                All
            </button>
            <button wire:click="setFilter('images')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 mr-2 flex items-center gap-1"
                :class="{ 'bg-gray-700 text-white': @js($filter) === 'images' }">
                <svg class="size-4" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M5 19h14V5H5zm4-5.86l2.14 2.58l3-3.87L18 17H6z" opacity=".3" />
                    <path fill="currentColor"
                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m0 16H5V5h14zm-4.86-7.14l-3 3.86L9 13.14L6 17h12z" />
                </svg>
                Images
            </button>
            <button wire:click="setFilter('docs')"
                class="cursor-pointer px-3 py-1 rounded-md text-sm font-medium border border-gray-300 flex items-center gap-1"
                :class="{ 'bg-gray-700 text-white': @js($filter) === 'docs' }">
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
                    class="bg-gray-700 text-white px-3 py-1 rounded-md text-sm cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!$wire.newFolderName">Create Folder</button>
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
                            <button wire:click="goToFolder('{{ dirname($currentPath) }}')"
                                class="text-gray-800 hover:underline flex gap-1 flex-1 text-left cursor-pointer">
                                <svg class="inline w-5 h-5 mr-1 text-red-500" viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2">
                                        <path
                                            d="M2 7.5V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-1.5M2 13h10" />
                                        <path d="m5 10l-3 3l3 3" />
                                    </g>
                                </svg>
                                ..
                            </button>
                        </li>
                    @endif
                    @forelse ($folders as $folder)
                        <li class="flex items-center justify-between group py-1">
                            <button wire:click="goToFolder('{{ $folder }}')"
                                class="text-gray-800 hover:underline flex-1 text-left cursor-pointer">
                                <svg class="inline w-5 h-5 mr-1 text-yellow-500" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open">
                                    <path
                                        d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2" />
                                </svg>
                                {{ basename($folder) }}
                            </button>
                            <button wire:click="delete('{{ $folder }}')" class="text-red-500 ml-2"
                                title="Delete folder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </li>
                    @empty
                        <li class="text-gray-400">No folders</li>
                    @endforelse
                </ul>
                <h2 class="font-semibold mb-2">Files</h2>
                <form x-show="mode2" @submit.prevent="submitFiles" class="mb-4">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm mb-2">Submit Selected
                        Files</button>
                </form>
                <ul>
                    @forelse ($files as $file)
                        <li class="flex items-center justify-between group py-1">
                            <div class="flex items-center max-w-[calc(100%-6rem)]">
                                <template x-if="mode2">
                                    <input type="checkbox" class="mr-2" :value="'{{ $file }}'"
                                        x-model="selectedFiles">
                                </template>
                                @php $mime = \Storage::disk(config('media-manager.disk', 'public'))->mimeType($file); @endphp
                                @if (str_starts_with($mime, 'image/'))
                                    <svg class="shrink-0 size-5 mr-2 text-gray-400" viewBox="0 0 24 24">
                                        <path fill="#888888"
                                            d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5zm1-2h12l-3.75-5l-3 4L9 13zm-1 2V5zm3.5-9q.625 0 1.063-.437T10 8.5t-.437-1.062T8.5 7t-1.062.438T7 8.5t.438 1.063T8.5 10" />
                                    </svg>
                                @else
                                    <svg class="shrink-0 size-5 mr-2 text-gray-400" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                        <path d="M10 9H8" />
                                        <path d="M16 13H8" />
                                        <path d="M16 17H8" />
                                    </svg>
                                @endif
                                <div class="truncate">{{ basename($file) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ \Storage::disk(config('media-manager.disk', 'public'))->url($file) }}"
                                    target="_blank" class="text-blue-500 hover:underline text-xs">Preview</a>
                                <button wire:click="delete('{{ $file }}')"
                                    class="text-red-500 ml-2 cursor-pointer" title="Delete file">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
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
