@foreach($tree as $item)
    <div class="mb-1" style="margin-left: {{ $level * 12 }}px;">
        <div class="flex items-center group mb-1">
            @if(!empty($item['children']))
                <button wire:click="toggleFolder('{{ $item['path'] }}')" 
                        class="flex items-center text-gray-600 hover:text-gray-800 focus:outline-none mr-1 cursor-pointer">
                    @if($item['expanded'])
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                </button>
            @else
                <span class="w-4"></span>
            @endif
            
            <button wire:click="goToFolder('{{ $item['path'] }}')" 
                    class="flex items-center text-gray-700 hover:text-gray-600 text-sm py-1 px-1 rounded hover:bg-gray-50 flex-1 text-left transition-colors
                           {{ $currentPath === $item['path'] ? 'text-gray-600 bg-gray-50 font-medium' : '' }}">
                <svg class="size-5 mr-1" viewBox="0 0 32 32"><!-- Icon from VSCode Icons by Roberto Huertas - https://github.com/vscode-icons/vscode-icons/blob/master/LICENSE --><path fill="#dcb67a" d="M27.4 5.5h-9.2l-2.1 4.2H4.3v16.8h25.2v-21Zm0 18.7H6.6V11.8h20.8Zm0-14.5h-8.2l1-2.1h7.1v2.1Z"/><path fill="#dcb67a" d="M25.7 13.7H.5l3.8 12.8h25.2z"/></svg>
                <span class="truncate">{{ $item['name'] }}</span>
            </button>
        </div>
        
        @if($item['expanded'] && !empty($item['children']))
            @include('media-manager::partials.directory-tree', ['tree' => $item['children'], 'level' => $level + 1])
        @endif
    </div>
@endforeach
