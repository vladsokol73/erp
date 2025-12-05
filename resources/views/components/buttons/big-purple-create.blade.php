@props(['id' => ''])

<button type="button"
        {{ $id ? "id=$id" : '' }}
        class="py-2 px-4 bg-black dark:bg-lightpurple-200 w-full rounded-lg text-white dark:text-black text-lg font-semibold border border-black dark:border-lightpurple-200 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">
    {{ $slot }}
</button>
