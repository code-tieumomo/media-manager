<div class="w-full">
    <form wire:submit.prevent="submit" class="w-full max-w-xs flex flex-col space-y-2 items-center mx-auto">
        <x-media-manager::picker wire:model="media" />
        <div class="mt-4">
            <button type="submit" class="px-4 py-1 bg-gray-700 text-white rounded hover:bg-gray-500 cursor-pointer">
                Submit
            </button>
        </div>
    </form>
</div>
