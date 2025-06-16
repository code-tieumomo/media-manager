<div class="min-h-screen bg-gray-100 p-6" x-data="{
    mode2: new URLSearchParams(window.location.search).get('mode') === '2',
    sourceId: new URLSearchParams(window.location.search).get('source_id') || null,
    selectedFiles: (() => {
        const dataParam = new URLSearchParams(window.location.search).get('data');
        console.log('dataParam', dataParam);
        try {
            return dataParam ? JSON.parse(dataParam) : [];
        } catch (e) {
            return [];
        }
    })(),
    multiple: new URLSearchParams(window.location.search).get('multiple') === 'true',
    expandedFolders: @js($expandedFolders),
    getFileUrl(file) {
        // This must match the Blade logic for file URL
        return '{{ rtrim(Storage::disk(config('media-manager.disk', 'public'))->url('')) }}' + '/' + file.replace(/^\/+/, '');
    },
    submitFiles() {
        let urls = [];

        if (this.multiple) {
            urls = this.selectedFiles.map(f => this.getFileUrl(f));
        } else if (this.selectedFiles) {
            urls = [this.getFileUrl(this.selectedFiles)];
        }

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
            <div class="md:max-w-xs w-full flex flex-col grow">
                <h2 class="font-semibold mb-2">Directory Tree</h2>
                <div class="bg-[#f1f0ef] rounded-lg p-3 overflow-y-auto grow">
                    <!-- Root folder -->
                    <div class="mb-1">
                        <button wire:click="goToFolder('/')"
                                class="flex items-center text-gray-700 hover:text-gray-900 text-sm py-1 px-1 rounded hover:bg-gray-50 w-full text-left transition-colors cursor-pointer {{ $currentPath === '/' ? 'text-gray-600 bg-gray-50' : '' }}">
                            <svg class="size-5 mr-1" viewBox="0 0 32 32"><!-- Icon from VSCode Icons by Roberto Huertas - https://github.com/vscode-icons/vscode-icons/blob/master/LICENSE --><path fill="#dcb67a" d="M27.4 5.5h-9.2l-2.1 4.2H4.3v16.8h25.2v-21Zm0 18.7H6.6V11.8h20.8Zm0-14.5h-8.2l1-2.1h7.1v2.1Z"/><path fill="#dcb67a" d="M25.7 13.7H.5l3.8 12.8h25.2z"/></svg>
                            <span class="font-medium">Root</span>
                        </button>
                    </div>

                    @if(!empty($directoryTree))
                        @include('media-manager::partials.directory-tree', ['tree' => $directoryTree, 'level' => 0])
                    @else
                        <p class="text-gray-400 text-sm">No subdirectories found</p>
                    @endif
                </div>
                <div class="mt-4 bg-[#f1f0ef] rounded-lg p-3 overflow-y-auto">
                    <div>
                        <div class="relative h-4 w-full rounded-2xl border border-gray-200 bg-white dark:bg-dark-3">
                            <div
                                class="absolute left-0 top-0 flex h-full items-center justify-center rounded-2xl bg-gray-700 text-xs font-semibold text-white transition-all duration-300" style="width: {{ $totalSizePercent ?? 0 }}%">
                                @if ($totalSizePercent > 12)
                                    {{ $totalSizePercent ?? 0 }}%
                                @endif
                            </div>
                        </div>
                    </div>
                    <ul class="mt-1">
                        <li class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-600">Total Size:</span>
                            <span class="text-sm font-semibold">{{ $totalSizeHuman }} / {{ $displayedMaxTotalSize }}</span>
                        </li>
                        <li class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-600">Total Files:</span>
                            <span class="text-sm font-semibold">{{ $totalFiles }}</span>
                        </li>
                        <li class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-600">Total Folders:</span>
                            <span class="text-sm font-semibold">{{ $totalFolders }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex-1 @container">
                <h2 class="font-semibold mb-2">
                    Folders ({{ count($folders) }})
                </h2>
                <div class="mb-8 grid grid-cols-3 @md:grid-cols-8 gap-4">
                    @forelse ($folders as $folder)
                    <div class="flex items-center justify-between group py-1">
                        <button wire:click="goToFolder('{{ $folder }}')"
                            class="text-gray-800 hover:underline flex-1 cursor-pointer flex flex-col items-center group-hover:bg-[#f1f0ef] rounded p-2  transition-colors {{ $currentPath === $folder ? 'bg-gray-50 font-medium' : '' }}">
                            <svg class="size-20" viewBox="0 0 32 32"><!-- Icon from VSCode Icons by Roberto Huertas - https://github.com/vscode-icons/vscode-icons/blob/master/LICENSE --><path fill="#dcb67a" d="M27.4 5.5h-9.2l-2.1 4.2H4.3v16.8h25.2v-21Zm0 18.7H6.6V11.8h20.8Zm0-14.5h-8.2l1-2.1h7.1v2.1Z"/><path fill="#dcb67a" d="M25.7 13.7H.5l3.8 12.8h25.2z"/></svg>
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
                <div class="grid grid-cols-8 gap-4">
                    @forelse ($files as $file)
                    @php
                        $mime = \Storage::disk(config('media-manager.disk', 'public'))->mimeType($file);
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $url = \Storage::disk(config('media-manager.disk', 'public'))->url($file);
                    @endphp
                    <div class="relative flex flex-col items-center group bg-white rounded p-2 hover:bg-[#f1f0ef] transition-colors"
                         :class="multiple
                            ? { 'border-2 border-blue-500': selectedFiles.includes('{{ $file }}') }
                            : { 'border-2 border-blue-500': selectedFiles === '{{ $file }}' }"
                    >
                        <span class="absolute top-1 right-2 bg-gray-800 text-white text-[10px] px-2 py-0.5 rounded-full uppercase z-10">{{ $ext }}</span>
                        <div class="flex flex-col items-center w-full " >
                            <template x-if="mode2">
                                <input
                                    type="checkbox"
                                    class="mr-2 mb-1"
                                    :value="'{{ $file }}'"
                                    x-model="selectedFiles"
                                    :checked="multiple ? selectedFiles.includes('{{ $file }}') : selectedFiles === '{{ $file }}'"
                                    @change="
                                       if (multiple) {
                                           if ($event.target.checked) {
                                               selectedFiles.push('{{ $file }}')
                                           } else {
                                               selectedFiles = selectedFiles.filter(f => f !== '{{ $file }}')
                                           }
                                       } else {
                                           selectedFiles = $event.target.checked ? '{{ $file }}' : ''
                                       }
                                   "
                                >
                            </template>
                            @if (str_starts_with($mime, 'image/'))
                                <a href="{{ $url }}" target="_blank" class="block mb-2">
                                    <img src="{{ $url }}" alt="{{ basename($file) }}" class="object-cover size-20 rounded w-full border border-gray-200 shadow-sm hover:scale-105 transition-transform" loading="lazy">
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" class="block mb-2">
                                    <svg class="size-20" viewBox="0 0 512 512"><!-- Icon from Firefox OS Emoji by Mozilla - https://mozilla.github.io/fxemoji/LICENSE.md --><path fill="#F9E7C0" d="M437.567 512H88.004a8.18 8.18 0 0 1-8.182-8.182V8.182A8.18 8.18 0 0 1 88.004 0H288.79l156.96 156.96v346.858a8.183 8.183 0 0 1-8.183 8.182"/><path fill="#EAC083" d="m288.79 0l156.96 156.96H322.152c-18.426 0-33.363-14.937-33.363-33.363V0z"/></svg>
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
