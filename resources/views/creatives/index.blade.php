@section('title', 'Creatives Library - Gteam')

<x-layout.default>
    <x-slot name="style">
        @vite(['resources/css/fancybox.css'])
    </x-slot>
    <div class="h-[calc(100vh-73px)] overflow-y-auto overflow-x-hidden">
        <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
            <div class="flex justify-between mb-4">
                <div class="space-y-2 font-semibold w-full" x-data="{ current: localStorage.getItem('dropdownOpen') === 'true' ? 1 : null }">
                    <button type="button" class="p-4 w-full flex items-center text-black/70 dark:text-white/70"
                            :class="{'!text-black dark:!text-white' : current === 1}"
                            x-on:click="
                current = current === 1 ? null : 1;
                localStorage.setItem('dropdownOpen', current === 1 ? 'true' : 'false');
            ">
                        <x-icons.filter-lines/>
                        Filters
                        <div class="ml-auto" :class="{'rotate-180' : current === 1}">
                            <x-icons.arrow-up/>
                        </div>
                    </button>
                    <div x-cloak x-show="current === 1" x-collapse>
                        <form id="filter_form" method="GET" action="{{ route('creatives') }}">
                            <div
                                class="mb-4 space-y-2 p-4 text-black dark:text-white text-sm border-t border-b border-black/10 dark:border-white/10">
                                <input type="search" name="search" value="{{ request('search') }}"
                                       placeholder="Search..."
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                       onchange="this.form.submit()">

                                <div class="flex justify-between space-x-4">
                                    <div class="w-1/4">
                                    <select name="country[]"
                                            class="select"
                                            data-placeholder="Select Country"
                                            multiple
                                            >
                                        @foreach($countries as $country)
                                            <option
                                                value="{{ $country->id }}" {{ in_array($country->id, (array) request('country')) ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    </div>

                                    <div class="w-1/4">
                                    <select name="user[]"
                                            class="select"
                                            data-placeholder="Select User"
                                            multiple
                                            >
                                        @foreach($users as $user)
                                            <option
                                                value="{{ $user->id }}" {{ in_array($user->id, (array) request('user')) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    </div>

                                    <div class="w-1/4">
                                    <select name="tag[]"
                                            class="select"
                                            data-placeholder="Select Tag"
                                            multiple
                                            >
                                        @foreach($tags as $tag)
                                            <option
                                                value="{{ $tag->id }}" {{ in_array($tag->id, (array) request('tag')) ? 'selected' : '' }}>
                                                {{ $tag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    </div>

                                    <div class="w-1/4">
                                    <select name="type[]"
                                            class="select"
                                            data-placeholder="Select Type"
                                            multiple
                                            >
                                        <option value="image" {{ in_array('image', (array) request('type')) ? 'selected' : '' }}>
                                            Image
                                        </option>
                                        <option value="video" {{ in_array('video', (array) request('type')) ? 'selected' : '' }}>
                                            Video
                                        </option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <form id="sortForm" class="border-b border-black/10 dark:border-white/10">
                        <select name="sort" id="sortSelect"
                                class="mb-3 w-1/6 form-select py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                            <option value="date_desc" selected>Date: New</option>
                            <option value="date_asc">Date: Old</option>
                            <option value="likes_positive">Likes positive</option>
                            <option value="likes_negative">Likes negative</option>
                        </select>
                    </form>

                    <div id="creativesContainer">
                        <x-show.creatives :creatives="$creatives"
                                          :thumbnails="$thumbnails"
                                          :tags="$tags"
                                          :comments="$comments"
                                          :likes="$likes"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let formChanged = false; // Флаг, был ли изменён select

        document.querySelectorAll(".select").forEach(select => {
            select.addEventListener("change", function () {
                formChanged = true; // Флаг активируется при изменении select
            });
        });

        document.addEventListener("click", function (event) {
            const form = document.getElementById("filter_form");

            if (formChanged && !form.contains(event.target)) {
                // Если форма была изменена и клик вне формы — отправляем её
                form.submit();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Восстанавливаем состояние dropdown из localStorage
            const savedDropdownState = localStorage.getItem('dropdownOpen');

            if (savedDropdownState === 'true') {
                Alpine.store('dropdownState', { current: 1 }); // Устанавливаем dropdown открытым
            }

            // Отслеживаем изменения переменной current и сохраняем в localStorage
            document.addEventListener('alpine:init', () => {
                Alpine.data('dropdownHandler', () => ({
                    current: savedDropdownState === 'true' ? 1 : null,
                    toggleDropdown() {
                        this.current = this.current === 1 ? null : 1;
                        localStorage.setItem('dropdownOpen', this.current === 1);
                    }
                }));
            });
        });

        $(document).ready(function () {
            $('#sortSelect').on('change', function () {
                const sortValue = $(this).val();

                $.ajax({
                    url: '/creatives/library',
                    method: 'GET',
                    data: {sort: sortValue},
                    beforeSend: function () {
                        // Показываем лоадер вместо текста
                        $('#creativesContainer').html(`
                    <div style="display: flex; justify-content: center; align-items: center; height: 100px;">
                        <x-elements.loader />
                    </div>
                `);
                    },
                    success: function (response) {
                        // Обновляем контейнер с креативами
                        $('#creativesContainer').html(response);
                    },
                    error: function () {
                        alert('Произошла ошибка при загрузке данных. Попробуйте ещё раз.');
                    }
                });
            });
        });

        function copyToClipboard(creativeUrl, button) {
            // Извлекаем имя файла из URL
            const fileNameWithExtension = creativeUrl.substring(creativeUrl.lastIndexOf('/') + 1);
            const fileName = fileNameWithExtension.substring(0, fileNameWithExtension.lastIndexOf('.'));

            // Копируем имя файла в буфер обмена
            navigator.clipboard.writeText(fileName).then(() => {
                console.log('Имя файла скопировано:', fileName);

                // Изменяем текст кнопки на "Copied"
                const originalText = button.textContent; // Сохраняем оригинальный текст
                button.textContent = 'Copied'; // Меняем текст на "Copied"

                // Восстанавливаем оригинальный текст через 3 секунды
                setTimeout(() => {
                    button.textContent = originalText; // Восстанавливаем оригинальный текст
                }, 3000); // 5000 мс = 5 секунд
            }).catch(err => {
                console.error('Ошибка при копировании:', err);
            });
        }
    </script>

    <script>
        function toggleLike(creativeId, value) {
            const url = `/creatives/like`;
            const formData = new FormData();
            formData.append('cid', creativeId);
            formData.append('val', value);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновление кнопок лайка/дизлайка
                        document.getElementById(`like-btn-${creativeId}`).innerHTML = data.liked ? `<x-icons.liked />` : `<x-icons.like />`;
                        document.getElementById(`dislike-btn-${creativeId}`).innerHTML = data.disliked ? `<x-icons.disliked />` : `<x-icons.dislike />`;

                        // Обновление счетчиков
                        document.getElementById(`like-count-${creativeId}`).textContent = data.positiveCount;
                        document.getElementById(`dislike-count-${creativeId}`).textContent = data.negativeCount;
                    } else {
                        alert('Произошла ошибка при изменении лайка!');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Не удалось связаться с сервером!');
                });
        }

        function toggleFavorite(creativeId) {
            fetch('/creatives/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({id: creativeId})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const icon = document.querySelector(`div[onclick="toggleFavorite(${creativeId})"]`);
                        if (data.isFavorite) {
                            icon.innerHTML = `{!! view('components.icons.favorited')->render() !!}`;
                        } else {
                            icon.innerHTML = `{!! view('components.icons.favorite')->render() !!}`;
                        }
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    <x-slot name="script">
        <!-- Facncybox Js -->
        <script src="/assets/js/fancybox.umd.js"></script>
        <script>
            Fancybox.bind('[data-fancybox="gallery"]', {
                //
            });
        </script>
    </x-slot>
</x-layout.default>
