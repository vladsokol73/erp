@props([])

<x-modals.small>
    <x-slot:title>Create Status</x-slot:title>
    <x-slot:button>
        <x-buttons.create>Create Status</x-buttons.create>
    </x-slot:button>

    <div class="space-y-4">
        <div>
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none" required>
        </div>

        <div>
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none" min="0" value="0">
        </div>

        <div>
            <label for="color" class="form-label">Color</label>
            <input type="color" id="color" name="color" value="#3b82f6" class="form-input h-10" required>
        </div>

        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_default" value="1" class="form-checkbox">
                <span>Is Default</span>
            </label>
        </div>

        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_final" value="1" class="form-checkbox">
                <span>Is Final</span>
            </label>
        </div>
    </div>
</x-modals.small>
