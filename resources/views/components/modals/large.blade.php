<div x-data="modals">
    {{ $button }}
    <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden" :class="open && '!block'">
        <div class="flex items-center justify-center min-h-screen px-4" @click.self="open = false">
            <div x-show="open" x-transition x-transition.duration.300
                 class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden  w-full max-w-xl my-8">
                <div
                    class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                    <h5 class="font-semibold text-lg">{{ $title }}</h5>
                    <button type="button"
                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                            @click="toggle">
                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z"
                                fill="currentcolor"/>
                            <path
                                d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z"
                                fill="currentcolor"/>
                        </svg>
                    </button>
                </div>
                <div class="p-5">
                    <div x-ref="modalContent" class="max-h-[calc(80vh-10rem)] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800">
                    {{ $slot }}
                    </div>

                    <div class="flex justify-between items-center mt-8 gap-4">
                        {{-- Левая часть --}}
                        <div class="flex">
                            @if(!empty($buttonDelete))
                                {!! $buttonDelete !!}
                            @endif
                        </div>

                        {{-- Правая часть --}}
                        <div class="flex gap-4">
                            <button type="button" class="btn !bg-lightred !text-white" @click="toggle">
                                Discard
                            </button>
                            <button type="submit"
                                    @click="$refs.modalContent.scrollTop = 0"
                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
