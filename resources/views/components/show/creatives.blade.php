<div class="grid grid-cols-1 lg:grid-cols-4 gap-7">
    @foreach($creatives as $creative)
        <div class="border border-black/10 dark:border-white/10 p-5 rounded-md">
            <div class="relative flex items-center space-x-2 justify-between">
                <img class="w-10 h-10 rounded-full" src="{{ $creative->country->img }}" alt="">
                <div class="font-medium flex-1">
                    <h6 class="text-sm">{{ $creative->country->name }}</h6>
                    <div class="flex">
                        <p class="text-xs text-black/40 dark:text-white/40">{{ $creative->created_at->format('Y-m-d') }}</p>
                        <p class="text-xs text-blue-400 ml-1">{{ $creative->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="text-sm text-right flex flex-col">
                    <div>{{ $creative->type }} [{{ $creative->resolution }}]</div>
                    <div class="mt-2">
                        <form method="POST"
                              action="{{ route("addCommentToCreative", ['id' => $creative->id]) }}">
                            @csrf

                            <x-modals.large>
                                <x-slot name="button">
                                    <button type="button"
                                            class="mofo-comments-button text-black dark:text-white bg-black dark:bg-black hover:text-black whitespace-nowrap"
                                            @click="toggle">
                                        <span class="relative">
                                            <x-icons.comments/>
                                            <span class="absolute top-[-15px] right-[-10px] @if(count($comments[$creative->id]) > 9) px-2 py-1.5 @elseif(count($comments[$creative->id]) == 1) py-1 px-2.5  @else py-1 px-2 @endif rounded-full border hover:text-black hover:bg-white transform scale-[0.75]">
                                                @if (count($comments[$creative->id]) < 100)
                                                    {{count($comments[$creative->id])}}
                                                @else
                                                    99+
                                                @endif
                                            </span>
                                        </span>
                                    </button>
                                </x-slot>
                                <x-slot name="title">
                                    Creative Comments
                                </x-slot>
                                <div
                                    class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                    <div class="mb-1">
                                        <p class="text-sm font-semibold">Comments</p>
                                    </div>
                                    @if (count($comments[$creative->id]))
                                        <div class="relative w-full max-h-80 overflow-y-auto">
                                            <div class="flex flex-col w-full">
                                                @foreach($comments[$creative->id] as $comment)
                                                    <div
                                                        class="flex flex-col w-full mt-2 rounded border border-white px-2 py-2">
                                                        <div
                                                            class="flex flex-row w-full justify-between">
                                                            <div>{{$comment->user->name}}</div>
                                                            <div>
                                                                {{$comment->created_at->format('Y-m-d H:i:s')}}
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-row w-full">
                                                            {{$comment->comment}}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div class="relative">
                                        <textarea name="comment"
                                                  id="comment_{{$creative->id}}"
                                                  class="mt-3 block rounded-lg px-5 pb-4 w-full text-black dark:text-white bg-white dark:bg-white/5 border border-black/10 dark:border-white/10 appearance-none focus:outline-none focus:ring-0 focus:border-black/10 dark:focus:border-black/10 peer">

                                        </textarea>
                                    </div>
                                </div>
                            </x-modals.large>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Tags -->
            <div class="h-[66px] my-3 flex flex-row flex-wrap space-x-1">
                @foreach($creative->tags as $tag)
                    <p class="h-[16px] px-1.5 text-black bg-{{ $tag->tailwind_color }} text-xs rounded-[18px] inline-block mb-3">
                        {{ $tag->name }}
                    </p>
                @endforeach
                @permission("creatives.update")
                <div class="">
                    <form method="POST" action="{{ route("updateCreative", $creative->id) }}">
                        @csrf
                        <x-modals.large>
                            <x-slot name="button">
                                <button
                                    type="button"
                                    class="mofo-edit-creative-tag-button"
                                    @click="toggle">
                                    <span class="mofo-tags-svg">
                                        <x-icons.plus-in-vis/>
                                    </span>
                                </button>
                            </x-slot>
                            <x-slot name="title">
                                Creative Edit
                            </x-slot>
                            <div
                                class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                <div class="mb-5">
                                    <p class="text-sm font-semibold">Tags</p>
                                </div>
                                <div class="mb-5">
                                    <input type="text" value="" placeholder="Looking for tag..."
                                           class="form-input border px-2 py-2 rounded-md border-white"
                                           data-tag_searcher="creative_{{$creative->id}}"/>
                                </div>
                                @foreach($tags as $tag)
                                    <label
                                        data-tag_searcher_creative_{{$creative->id}}="{{ $tag->name }}"
                                        class="inline-flex mr-2 items-center cursor-pointer mb-4">
                                        <input value="{{ $tag->id }}"
                                               name="tag_{{ $tag->id }}"
                                               type="checkbox"
                                               class="form-checkbox"
                                               @if($creative->hasTag($tag->id))
                                                   checked
                                            @endif>
                                        <span>
                                            <p class="px-1.5 text-black bg-{{ $tag->tailwind_color }} text-xs rounded-[18px] inline-block">
                                                {{ $tag->name }}
                                            </p>
                                        </span>
                                    </label>
                                @endforeach
                            </div>


                            <x-slot name="buttonDelete">
                                <div x-data="modals">
                                    <x-buttons.delete/>
                                    <div
                                        class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                        :class="open && '!block'">
                                        <div class="flex items-start justify-center min-h-screen px-4"
                                             @click.self="open = false">
                                            <div x-show="open" x-transition x-transition.duration.300
                                                 class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden  w-full max-w-sm my-8">
                                                <div
                                                    class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                    <h5 class="font-semibold text-lg">Confirm Delete</h5>
                                                    <button type="button"
                                                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                            @click="toggle">
                                                        <svg class="w-5 h-5" width="32" height="32"
                                                             viewBox="0 0 32 32" fill="none"
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
                                                    <div class="text-sm text-black dark:text-white">
                                                        <p>Are you sure you want to delete a creative with
                                                            code: "{{ $creative->code }}"?</p>
                                                    </div>
                                                    <div class="flex justify-end items-center mt-8 gap-4">
                                                        <button type="button"
                                                                class="btn"
                                                                @click="toggle">Discard
                                                        </button>
                                                        <button type="button" class="btn !bg-lightred !text-white"
                                                                @click="document.getElementById('deleteForm_{{ $creative->id }}').submit()">
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                        </x-modals.large>
                    </form>
                    <form id="deleteForm_{{ $creative->id }}" method="POST"
                          action="{{ route("deleteCreative", $creative->id) }}">
                        @csrf
                    </form>
                </div>
                @endpermission
            </div>
            <!-- End Tags -->

            <div class="place-content-center relative mt-5">
                <a href="{{ $creative->url }}" data-fancybox="gallery">
                    @if($creative->type === 'image')
                        <img src="{{ $creative->url }}"
                             class="w-full aspect-[3/2] object-cover rounded-lg">
                    @else
                        <video poster="{{ $thumbnails[$creative->id] }}"
                               muted
                               loop
                               class="w-full aspect-[3/2] object-cover rounded-lg"
                               loading="lazy"
                               preload="metadata"
                               playsinline
                               crossorigin="anonymous"
                               onmouseover="playVideo(this)"
                               onmouseout="stopVideo(this)">
                            <source src="{{ $creative->url }}" type="video/mp4"/>
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </a>

                <!-- Иконка избранного -->
                <div class="absolute right-3 top-3 cursor-pointer" onclick="toggleFavorite({{ $creative->id }})">
                    @if($creative->isFavorite())
                        <x-icons.favorited/>
                    @else
                        <x-icons.favorite/>
                    @endif
                </div>
            </div>
            <div class="relative mt-5 flex items-center flex-row">
                <div class="flex flex-row">
                    <div
                        class="cursor-pointer"
                        id="like-btn-{{ $creative->id }}"
                        onclick="toggleLike({{ $creative->id }}, 1);"
                    >
                        @if($creative->liked())
                            <x-icons.liked/>
                        @else
                            <x-icons.like/>
                        @endif
                    </div>
                    <div class="ml-1" id="like-count-{{ $creative->id }}">
                        {{ count($likes[$creative->id]['positive']) }}
                    </div>
                </div>
                <div class="ml-5 flex flex-row">
                    <div
                        class="cursor-pointer"
                        id="dislike-btn-{{ $creative->id }}"
                        onclick="toggleLike({{ $creative->id }}, -1);"
                    >
                        @if($creative->disliked())
                            <x-icons.disliked/>
                        @else
                            <x-icons.dislike/>
                        @endif
                    </div>
                    <div class="ml-1" id="dislike-count-{{ $creative->id }}">
                        {{ count($likes[$creative->id]['negative']) }}
                    </div>
                </div>
            </div>
            <div class="relative mt-5 flex items-center space-x-2">
                <button id="copyButton_{{ $creative->id }}"
                        onclick="copyToClipboard('{{ $creative->url }}', this)"
                        class="btn text-black dark:text-white border hover:bg-indigo-300 dark:hover:bg-indigo-300 border-indigo-300 bg-transparent hover:text-black">
                    {{ $creative->code }}
                </button>

                <div class="absolute inset-y-0 right-0 object-position: right">

                    <div x-data="{ dropdown: false}" class="dropdown">
                        <button
                            @click="dropdown = !dropdown"
                            @keydown.escape="dropdown = false"
                            class="btn text-black dark:text-white border hover:bg-indigo-300 dark:hover:bg-indigo-300 border-indigo-300 bg-transparent hover:text-black">
                            Download
                        </button>
                        <ul x-show="dropdown" @click.away="dropdown = false" x-transition=""
                            x-transition.duration.300ms="" class="right-0 whitespace-nowrap" style="display: none;">
                            <li><a href="javascript:;"
                                   onclick="window.location.href='{{ route('download', ['file' => $creative->url]) }}'">Original</a>
                            </li>
                            <div x-data="downloadModal('{{ $creative->url }}')">
                                <li><a href="javascript:;" @click="toggleModal()">Unique</a></li>
                                <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                     :class="open && '!block'">
                                    <div class="flex items-center justify-center min-h-screen px-4"
                                         @click.self="toggleModal()">
                                        <div x-show="open" x-transition.duration.300=""
                                             class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden w-full max-w-sm my-8">
                                            <div
                                                class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                <h5 class="font-semibold text-lg">Download Creative</h5>
                                                <button type="button"
                                                        class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                        @click="toggleModal()">
                                                    <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32"
                                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z"
                                                            fill="currentcolor"></path>
                                                        <path
                                                            d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z"
                                                            fill="currentcolor"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="p-5">
                                                <template x-if="loading">
                                                    <div class="flex flex-col items-center">
                                                        <x-elements.loader/>
                                                        <div class="mt-3 text-center">
                                                            <div class="text-sm text-black/70 dark:text-white/70"
                                                                 x-text="'Stage: ' + processingStage"></div>
                                                            <div class="text-sm text-black/70 dark:text-white/70"
                                                                 x-text="'Progress: ' + processingProgress + '%'"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="!loading && downloadLink">
                                                    <div class="flex justify-center">
                                                        <a :href="downloadLink" class="btn btn-primary">Download</a>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div>
    @if(!$creatives->isEmpty())
        {{ $creatives->appends(request()->all())->links('vendor.pagination.optimized') }}
    @endif
</div>
@if($creatives->isEmpty())
    <div class="text-center mt-5 w-full">
        <h1 class="text-xl font-bold">Sorry, nothing was found.</h1>
    </div>
@endif

@if($creatives->isEmpty())
    @for($i = 0; $i < 4; $i++)
        <div class="border border-black/10 dark:border-white/10 p-5 rounded-md animate-pulse">
            <div class="relative flex items-center space-x-2 justify-between">
                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="flex mt-2">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4 ml-1"></div>
                    </div>
                </div>
                <div class="text-sm text-right flex flex-col">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-40 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
    @endfor
@endif

<script>
    const videoStates = new WeakMap();

    function playVideo(video) {
        const state = videoStates.get(video) || {};
        if (state.isPlaying) return;

        // Если есть активное обещание воспроизведения, не начинаем новое
        if (state.playPromise) return;

        // Если есть активный таймер, не создаем новый
        if (state.playTimer) return;

        // Пробуем загрузить видео перед воспроизведением
        try {
            video.load();
        } catch (e) {
            console.error('Error loading video:', e);
        }

        // Создаем таймер с задержкой в 500мс
        state.playTimer = setTimeout(() => {
            state.playTimer = null;
            videoStates.set(video, state);

            try {
                const playPromise = video.play();
                if (playPromise !== undefined) {
                    state.playPromise = playPromise;
                    state.isPlaying = true;
                    videoStates.set(video, state);

                    playPromise
                        .then(() => {
                            state.playPromise = null;
                            console.log('Video playback started successfully');
                        })
                        .catch(error => {
                            console.error('Playback failed:', {
                                error: error,
                                videoSrc: video.currentSrc || video.src,
                                readyState: video.readyState,
                                networkState: video.networkState,
                                errorCode: video.error ? video.error.code : null,
                                errorMessage: video.error ? video.error.message : null
                            });
                            state.playPromise = null;
                            state.isPlaying = false;
                            videoStates.set(video, state);

                            // Пробуем перезагрузить видео при ошибке
                            if (error.name === 'NotSupportedError') {
                                video.load();
                            }
                        });
                }
            } catch (e) {
                console.error('Error during play:', e);
            }
        }, 500);

        videoStates.set(video, state);
    }

    function stopVideo(video) {
        const state = videoStates.get(video) || {};
        if (!state.isPlaying && !state.playTimer) return;

        if (state.playTimer) {
            clearTimeout(state.playTimer);
            state.playTimer = null;
        }

        state.isPlaying = false;
        videoStates.set(video, state);

        try {
            if (state.playPromise) {
                state.playPromise
                    .then(() => {
                        video.pause();
                        video.currentTime = 0;
                    })
                    .catch(() => {
                        video.pause();
                        video.currentTime = 0;
                    });
            } else {
                video.pause();
                video.currentTime = 0;
            }
        } catch (e) {
            console.error('Error during stop:', e);
        }
    }

    // Добавляем обработчики событий для всех видео
    document.addEventListener('DOMContentLoaded', function () {
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            videoStates.set(video, {
                isPlaying: false,
                playPromise: null,
                playTimer: null
            });

            video.addEventListener('error', function (e) {
                const error = video.error || e;
                console.error('Video loading error:', {
                    src: video.currentSrc || video.src,
                    error: error,
                    errorCode: error.code,
                    errorMessage: error.message,
                    networkState: video.networkState
                });
            });
        });
    });
</script>

<script>
    window.appConfig = {
        unicBase: "{{ rtrim(config('app.unic-endpoint'), '/') }}"
    };
</script>

<script>
    document.addEventListener('alpine:init', () => {
        const unicBase = window.appConfig.unicBase;
        Alpine.data('downloadModal', (creativeUrl) => ({
            open: false,
            loading: false,
            downloadLink: '',
            taskCode: '',
            processingStage: '',
            processingProgress: 0,
            creativeUrl, // Получаем URL из параметра

            toggleModal() {
                this.open = !this.open;
                if (this.open) {
                    this.startDownload();
                }
            },

            async startDownload() {
                this.loading = true;
                this.downloadLink = '';

                try {
                    // POST запрос для начала загрузки
                    const response = await fetch(`${unicBase}/upload`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        mode: 'cors',
                        credentials: 'include',
                        body: JSON.stringify({url: this.creativeUrl}), // Используем переданный URL
                    });

                    const result = await response.json();
                    this.taskCode = result.task_code;

                    // Отслеживание состояния
                    this.checkStatus();
                } catch (error) {
                    console.error('Error starting download:', error);
                    this.loading = false;
                }
            },

            async checkStatus() {
                const interval = setInterval(async () => {
                    try {
                        const statusResponse = await fetch(`${unicBase}/status/${this.taskCode}`);
                        const statusResult = await statusResponse.json();

                        if (statusResult.state === 'COMPLETED') {
                            clearInterval(interval);
                            this.loading = false;
                            this.downloadLink = `https://services.investingindigital.com/api/unic/download/${this.taskCode}`;
                        }

                        // Обновляем информацию о прогрессе
                        this.processingStage = statusResult.stage || 'Processing';
                        this.processingProgress = statusResult.progress || 0;
                    } catch (error) {
                        console.error('Error checking status:', error);
                    }
                }, 1000); // Проверка каждую секунду
            }
        }));
    });
</script>
