@section('title', 'Short URLs - Gteam')
<x-layout.default>
    <div class="relative">
        <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>
        <div id="loader" class="fixed inset-0 flex items-center justify-center hidden z-50">
            <x-elements.loader></x-elements.loader>
        </div>

        <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
            <div class="grid grid-cols-1 gap-7">
                <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                    <div class="flex justify-between items-center mb-5">
                        <p class="text-sm font-semibold">Short URLs</p>
                        <form method="GET" class="dataTable-search" action="{{ route('shorter') }}">
                            <input value="{{ request('search') }}" class="dataTable-input" placeholder="Search..." type="text" name="search" onchange="this.form.submit()">
                        </form>
                    </div>

                    <div id="urls-table">
                        <div class="table-responsive">
                            <table class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5 w-full">
                                <thead>
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Original URL</th>
                                        <th class="py-2">Short Code</th>
                                        <th class="py-2">Domain</th>
                                        <th class="py-2">Created</th>
                                        <th class="py-2">Edit</th>
                                        <th class="py-2">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($urls as $url)
                                        <tr id="url-row-{{ $url->id }}">
                                            <td class="whitespace-nowrap py-2">{{ $url->id }}</td>
                                            <td class="py-2 max-w-xs truncate">{{ $url->original_url }}</td>
                                            <td class="py-2">{{ $url->short_code }}</td>
                                            <td class="py-2">{{ $url->domain }}</td>
                                            <td class="py-2">{{ $url->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td class="py-2">
                                                <div x-data="modals">
                                                    <x-buttons.edit @click="toggle"/>

                                                    <!-- Modal Edit -->
                                                    <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                                         :class="open && '!block'">
                                                        <div class="flex items-start justify-center min-h-screen px-4"
                                                             @click.self="open = false">
                                                            <div x-show="open" x-transition x-transition.duration.300
                                                                 class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                                                <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                                    <h5 class="font-semibold text-lg">Edit Short URL</h5>
                                                                    <button type="button" class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                                            @click="toggle">
                                                                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                                                                            <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <div class="relative p-5">
                                                                    <div id="modal-overlay-{{ $url->id }}" class="absolute inset-0 bg-black/50 backdrop-blur-sm hidden z-[60]"></div>
                                                                    <div id="modal-loader-{{ $url->id }}" class="absolute inset-0 flex items-center justify-center hidden z-[70]">
                                                                        <x-elements.loader></x-elements.loader>
                                                                    </div>

                                                                    <div id="edit-error-{{ $url->id }}" class="mb-4 rounded bg-lightyellow p-3 text-black hidden"></div>
                                                                    <div id="edit-success-{{ $url->id }}" class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden"></div>

                                                                    <form id="edit_url_{{ $url->id }}" @submit.prevent="handleEdit($event, {{ $url->id }})">
                                                                        <div class="mb-4 rounded bg-lightyellow p-3 text-black hidden" id="edit-error-{{ $url->id }}"></div>
                                                                        <div class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden" id="edit-success-{{ $url->id }}"></div>
                                                                        <div class="mb-5">
                                                                            <label for="original_url_{{ $url->id }}" class="text-sm font-semibold mb-2 block">Original URL</label>
                                                                            <input type="url" id="original_url_{{ $url->id }}" name="original_url"
                                                                                   value="{{ $url->original_url }}" required
                                                                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                        </div>
                                                                        <div class="mb-5">
                                                                            <label for="short_code_{{ $url->id }}" class="text-sm font-semibold mb-2 block">Short Code</label>
                                                                            <input type="text" id="short_code_{{ $url->id }}" name="short_code"
                                                                                   value="{{ $url->short_code }}" required
                                                                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                        </div>
                                                                        <div class="mb-4">
                                                                            <p class="text-sm font-semibold">
                                                                                Select domain
                                                                            </p>
                                                                        </div>
                                                                        <div class="mb-5">
                                                                            <select name="domain" id="domain_{{ $url->id }}"
                                                                                    class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                                @foreach($domains as $domain)
                                                                                    <option value="{{ $domain }}" {{ $domain === $url->domain ? 'selected' : '' }}>{{ $domain }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="flex justify-end items-center mt-8 gap-4">
                                                                            <button type="button" class="btn !bg-lightred !text-white" @click="toggle">
                                                                                Discard
                                                                            </button>
                                                                            <button type="submit" id="edit-submit-{{ $url->id }}"
                                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                                                                                Save
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div x-data="modals">
                                                <x-buttons.delete @click="toggle"/>

                                                <!-- Modal Delete -->
                                                <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                                     :class="open && '!block'">
                                                    <div class="flex items-start justify-center min-h-screen px-4"
                                                         @click.self="open = false">
                                                        <div x-show="open" x-transition x-transition.duration.300
                                                             class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                                            <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                                <h5 class="font-semibold text-lg">Delete URL</h5>
                                                                <button type="button" class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                                        @click="toggle">
                                                                    <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                                                                        <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="relative p-5">
                                                                <div id="delete-modal-overlay-{{ $url->id }}" class="absolute inset-0 bg-black/50 backdrop-blur-sm hidden z-[60]"></div>
                                                                <div id="delete-modal-loader-{{ $url->id }}" class="absolute inset-0 flex items-center justify-center hidden z-[70]">
                                                                    <x-elements.loader></x-elements.loader>
                                                                </div>

                                                                <div id="delete-error-{{ $url->id }}" class="mb-4 rounded bg-lightyellow p-3 text-black hidden"></div>
                                                                <div id="delete-success-{{ $url->id }}" class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden"></div>
                                                                
                                                                <p class="mb-5">Are you sure you want to delete this URL? This action cannot be undone.</p>

                                                                <div class="flex justify-end items-center mt-8 gap-4">
                                                                    <button type="button"
                                                                            class="btn"
                                                                            @click="toggle">Discard
                                                                    </button>
                                                                    <button type="button" onclick="handleDelete({{ $url->id }}, this)" 
                                                                            id="delete-submit-{{ $url->id }}" 
                                                                            class="btn !bg-lightred !text-white">
                                                                        Confirm
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="dataTable-bottom grid place-items-end mt-4">
                        <div>
                            @if(!$urls->isEmpty())
                                {{ $urls->appends(request()->all())->links('vendor.pagination.optimized') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            function isValidUrl(url) {
                try {
                    new URL(url);
                    return true;
                } catch (e) {
                    return false;
                }
            }

            function showLoader(id, isDelete = false) {
                const prefix = isDelete ? 'delete-' : '';
                document.getElementById(`${prefix}modal-overlay-${id}`).classList.remove('hidden');
                document.getElementById(`${prefix}modal-loader-${id}`).classList.remove('hidden');
            }

            function hideLoader(id, isDelete = false) {
                const prefix = isDelete ? 'delete-' : '';
                document.getElementById(`${prefix}modal-overlay-${id}`).classList.add('hidden');
                document.getElementById(`${prefix}modal-loader-${id}`).classList.add('hidden');
            }

            function showError(id, message) {
                const errorDiv = document.getElementById(`edit-error-${id}`);
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }

            function hideError(id) {
                const errorDiv = document.getElementById(`edit-error-${id}`);
                errorDiv.classList.add('hidden');
            }

            async function handleEdit(event, id) {
                event.preventDefault();
                
                const form = document.getElementById(`edit_url_${id}`);
                const errorDiv = document.getElementById(`edit-error-${id}`);
                const successDiv = document.getElementById(`edit-success-${id}`);
                const submitBtn = document.getElementById(`edit-submit-${id}`);
                const row = document.getElementById(`url-row-${id}`);
                
                // Валидация полей
                const originalUrl = form.elements['original_url'].value.trim();
                const shortCode = form.elements['short_code'].value.trim();
                const domain = form.elements['domain'].value;

                // Проверяем URL
                if (!originalUrl) {
                    showError(id, 'Please enter URL');
                    return false;
                }

                if (!isValidUrl(originalUrl)) {
                    showError(id, 'Please enter a valid URL');
                    return false;
                }

                // Проверяем short code
                if (shortCode.length !== 6) {
                    showError(id, 'Short code must be exactly 6 characters');
                    return false;
                }

                if (!/^[a-zA-Z0-9]+$/.test(shortCode)) {
                    showError(id, 'Short code must contain only English letters and numbers');
                    return false;
                }
                
                // Скрываем сообщения и показываем loader
                hideError(id);
                successDiv.classList.add('hidden');
                showLoader(id);
                submitBtn.disabled = true;
                
                try {
                    const response = await fetch(`{{ url('shorter/edit') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            original_url: originalUrl,
                            short_code: shortCode,
                            domain: domain
                        })
                    });

                    const data = await response.json();

                    // Скрываем loader
                    hideLoader(id);
                    submitBtn.disabled = false;

                    if (!response.ok) {
                        let errorMessage = data.message;
                        if (data.errors && Array.isArray(data.errors)) {
                            errorMessage = data.errors.map(error =>
                                Object.values(error).join(' ')
                            ).join(', ');
                        }
                        showError(id, errorMessage);
                        return false;
                    }

                    // Обновляем данные в строке таблицы
                    row.children[1].textContent = data.url.original_url;
                    row.children[2].textContent = data.url.short_code;
                    row.children[3].textContent = data.url.domain;

                    // Показываем сообщение об успехе
                    successDiv.textContent = 'URL successfully updated';
                    successDiv.classList.remove('hidden');

                    // Автоматически закрываем модальное окно через 2 секунды
                    setTimeout(() => {
                        const modal = form.closest('[x-data="modals"]');
                        if (modal && modal.__x) {
                            modal.__x.$data.open = false;
                        }
                    }, 2000);

                    return false;

                } catch (error) {
                    hideLoader(id);
                    submitBtn.disabled = false;
                    showError(id, 'An error occurred while updating the URL');
                    return false;
                }
            }

            async function handleDelete(id, button) {
                const errorDiv = document.getElementById(`delete-error-${id}`);
                const successDiv = document.getElementById(`delete-success-${id}`);
                const submitBtn = document.getElementById(`delete-submit-${id}`);
                const row = document.getElementById(`url-row-${id}`);

                // Скрываем сообщения и показываем loader
                errorDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                showLoader(id, true);
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`{{ url('shorter/delete') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    // Скрываем loader
                    hideLoader(id, true);
                    submitBtn.disabled = false;

                    if (!response.ok) {
                        const data = await response.json();
                        errorDiv.textContent = data.message || 'An error occurred while deleting the URL';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    // Показываем сообщение об успехе
                    successDiv.textContent = 'URL successfully deleted';
                    successDiv.classList.remove('hidden');

                    // Удаляем строку из таблицы
                    row.remove();

                    // Автоматически закрываем модальное окно через 2 секунды
                    setTimeout(() => {
                        const modal = button.closest('[x-data="modals"]');
                        if (modal && modal.__x) {
                            modal.__x.$data.open = false;
                        }
                    }, 2000);

                } catch (error) {
                    hideLoader(id, true);
                    submitBtn.disabled = false;
                    errorDiv.textContent = 'An error occurred while deleting the URL';
                    errorDiv.classList.remove('hidden');
                }
            }
        </script>
    </x-slot>
</x-layout.default>
