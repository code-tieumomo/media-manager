@foreach($tree as $item)
    <div class="mb-1" style="margin-left: {{ $level * 12 }}px;">
        <div class="flex items-center group">
            @if(!empty($item['children']))
                <button wire:click="toggleFolder('{{ $item['path'] }}')" 
                        class="flex items-center text-gray-600 hover:text-gray-800 focus:outline-none mr-1">
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
                    class="flex items-center text-gray-700 hover:text-blue-600 text-sm py-1 px-1 rounded hover:bg-blue-50 flex-1 text-left transition-colors
                           {{ $currentPath === $item['path'] ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                <svg class="w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <span class="truncate">{{ $item['name'] }}</span>
            </button>
        </div>
        
        @if($item['expanded'] && !empty($item['children']))
            @include('media-manager::partials.directory-tree', ['tree' => $item['children'], 'level' => $level + 1])
        @endif
    </div>
@endforeach
