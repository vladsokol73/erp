@if ($paginator->hasPages())
    <div class="mt-10 flex flex-col items-center justify-center gap-3">
        <ul class="inline-flex items-center space-x-1 m-auto mb-4">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <button type="button"
                            class="flex justify-center px-2.5 py-2.5 rounded-full bg-black/10 dark:bg-white/10 text-black/60 dark:text-white/60"
                            disabled>
                        <svg class="w-4 h-4" viewBox="0 0 32 32">
                            <path
                                d="M25.7067 6.70748L14.2929 16.7071C14.1054 16.8946 14 17.149 14 17.4142C14 17.6794 14.1054 17.9338 14.2929 18.1213L25.2929 27.1213C25.4804 27.3089 25.7348 27.4142 26 27.4142C26.2652 27.4142 26.5196 27.3089 26.7071 27.1213C26.8946 26.9338 27 26.6794 27 26.4142C27 26.149 26.8946 25.8946 26.7071 25.7071L16.4142 16L26.7071 6.70711C26.8946 6.51957 27 6.26522 27 6C27 5.73478 26.8946 5.48043 26.7071 5.29289C26.5196 5.10536 26.2652 5 26 5C25.7348 5 25.4804 5.10536 25.2929 5.29289Z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="flex justify-center px-2.5 py-2.5 rounded-full bg-black/10 hover:bg-black dark:bg-white/10 dark:hover:bg-white text-black/60 hover:text-white dark:text-white/60 dark:hover:text-black">
                        <svg class="w-4 h-4" viewBox="0 0 32 32">
                            <path
                                d="M25.7067 6.70748L14.2929 16.7071C14.1054 16.8946 14 17.149 14 17.4142C14 17.6794 14.1054 17.9338 14.2929 18.1213L25.2929 27.1213C25.4804 27.3089 25.7348 27.4142 26 27.4142C26.2652 27.4142 26.5196 27.3089 26.7071 27.1213C26.8946 26.9338 27 26.6794 27 26.4142C27 26.149 26.8946 25.8946 26.7071 25.7071L16.4142 16L26.7071 6.70711C26.8946 6.51957 27 6.26522 27 6C27 5.73478 26.8946 5.48043 26.7071 5.29289C26.5196 5.10536 26.2652 5 26 5C25.7348 5 25.4804 5.10536 25.2929 5.29289Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="flex justify-center px-2.5 py-2.5 rounded-full bg-black/10 hover:bg-black dark:bg-white/10 dark:hover:bg-white text-black/60 hover:text-white dark:text-white/60 dark:hover:text-black">
                        <svg class="w-4 h-4" viewBox="0 0 32 32">
                            <path
                                d="M6.29289 25.2929L17.7071 16L6.29289 6.70711C6.10536 6.51957 6 6.26522 6 6C6 5.73478 6.10536 5.48043 6.29289 5.29289C6.51957 5.10536 6.73478 5 7 5C7.26522 5 7.51957 5.10536 7.70711 5.29289L18.7071 15.2929C19.0976 15.6834 19.0976 16.3166 18.7071 16.7071L7.70711 26.7071C7.51957 26.8946 7.26522 27 7 27C6.73478 27 6.48043 26.8946 6.29289 26.7071C6.10536 26.5196 6 26.2652 6 26C6 25.7348 6.10536 25.4804 6.29289 25.2929Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>
                </li>
            @else
                <li>
                    <button type="button"
                            class="flex justify-center px-2.5 py-2.5 rounded-full bg-black/10 dark:bg-white/10 text-black/60 dark:text-white/60"
                            disabled>
                        <svg class="w-4 h-4" viewBox="0 0 32 32">
                            <path
                                d="M6.29289 25.2929L17.7071 16L6.29289 6.70711C6.10536 6.51957 6 6.26522 6 6C6 5.73478 6.10536 5.48043 6.29289 5.29289C6.51957 5.10536 6.73478 5 7 5C7.26522 5 7.51957 5.10536 7.70711 5.29289L18.7071 15.2929C19.0976 15.6834 19.0976 16.3166 18.7071 16.7071L7.70711 26.7071C7.51957 26.8946 7.26522 27 7 27C6.73478 27 6.48043 26.8946 6.29289 26.7071C6.10536 26.5196 6 26.2652 6 26C6 25.7348 6.10536 25.4804 6.29289 25.2929Z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                </li>
            @endif
        </ul>
    </div>
@endif
