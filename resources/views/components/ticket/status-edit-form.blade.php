@props(['status'])

<x-modals.small>
    <x-slot:title>Edit Status</x-slot:title>
    <x-slot:button>
        <button type="button" class="btn btn-sm btn-primary" @click="toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                 stroke-linejoin="round" class="h-5 w-5">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
        </button>
    </x-slot:button>

    <div class="space-y-4">
        <div class="flex flex-col gap-1">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" value="{{ $status->name }}"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                   required>
        </div>

        <div class="flex flex-col gap-1">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ $status->sort_order }}"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                   min="0">
        </div>

        <div class="flex flex-col gap-1">
            <label for="color" class="form-label">Color</label>
            <input type="color" id="color" name="color" value="{{ $status->color }}" class="form-input h-10" required>
        </div>

        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_default" value="1"
                       class="form-checkbox" {{ $status->is_default ? 'checked' : '' }}>
                <span>Is Default</span>
            </label>
        </div>

        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_final" value="1"
                       class="form-checkbox" {{ $status->is_final ? 'checked' : '' }}>
                <span>Is Final</span>
            </label>
        </div>
    </div>
</x-modals.small>
