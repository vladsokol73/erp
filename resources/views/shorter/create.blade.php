@section('title', 'Short URL - Gteam')
<x-layout.default>
    <div class="min-h-[calc(100vh-145px)] py-4 px-4 sm:px-12 flex justify-center items-center relative">
        <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>
        <div id="loader" class="fixed inset-0 flex items-center justify-center hidden z-50">
            <x-elements.loader></x-elements.loader>
        </div>
        <div
            class="max-w-[680px] flex-none w-full bg-white dark:bg-white/5 p-4 sm:p-10 lg:px-[146px] lg:py-[107px] rounded-2xl loginform">
            <h1 class="text-2xl font-semibold mb-2 text-center">Create Short URL</h1>
            <div class="flex items-center mb-7">
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
            </div>

            <form id="create_short_url" class="mb-4">
                @csrf
                @if($errors->any())
                    <div class="mb-4 rounded bg-lightyellow p-3 text-black">{{ $errors->first() }}</div>
                @endif
                <div class="mb-4 rounded bg-lightyellow p-3 text-black hidden" id="moFoErrors"></div>
                <div class="mb-4">
                    <input id="original_url" type="text" required name="original_url" placeholder="Original URL"
                           class="form-input"/>
                </div>
                <div class="mb-4">
                    <input id="short_code" type="text" name="short_code" placeholder="Short Code (6 characters)" class="form-input"/>
                    <p class="text-xs text-gray-500 mt-1">Leave empty for random code generation</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm font-semibold">
                        Select domain
                    </p>
                </div>
                <div class="mb-5">
                    <select name="domain" id="domain"
                            class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                        @foreach($domains as $domain)
                            <option value="{{ $domain }}">{{ $domain }}</option>
                        @endforeach
                    </select>
                </div>
                <x-buttons.big-purple-create id="submitBtn">Create</x-buttons.big-purple-create>
            </form>
        </div>
    </div>

    <x-slot name="script">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('create_short_url');
                const formContent = form.innerHTML;
                const loader = document.getElementById('loader');
                const overlay = document.getElementById('overlay');

                function isValidUrl(url) {
                    try {
                        new URL(url);
                        return true;
                    } catch (e) {
                        return false;
                    }
                }

                function showError(message) {
                    const errorDiv = document.getElementById('moFoErrors');
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                }

                function hideError() {
                    const errorDiv = document.getElementById('moFoErrors');
                    errorDiv.classList.add('hidden');
                }

                function showLoader() {
                    loader.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Предотвращаем прокрутку
                }

                function hideLoader() {
                    loader.classList.add('hidden');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = ''; // Возвращаем прокрутку
                }

                function showSuccessView(shortUrl) {
                    form.innerHTML = `
                        <div class="text-center">
                            <p class="mb-4">Your short URL:</p>
                            <a href="${shortUrl}" class="text-blue-600 hover:text-blue-800 break-all mb-4 block" target="_blank">
                                ${shortUrl}
                            </a>
                            <div class="flex gap-4 justify-center mt-4">
                                <button type="button" class="copy-btn py-2 px-4 bg-gray-200 hover:bg-gray-300 rounded-lg transition-all duration-300 text-black">
                                    Copy
                                </button>
                                <button type="button" class="create-new-btn py-2 px-4 bg-black text-white hover:bg-gray-800 rounded-lg">
                                    Create Another
                                </button>
                            </div>
                        </div>
                    `;

                    // Обработчик для кнопки копирования
                    const copyBtn = form.querySelector('.copy-btn');
                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(shortUrl).then(() => {
                            const originalText = copyBtn.textContent;
                            copyBtn.textContent = 'Copied!';
                            copyBtn.classList.add('bg-green-500', 'text-white');
                            copyBtn.classList.remove('bg-gray-200', 'hover:bg-gray-300');

                            setTimeout(() => {
                                copyBtn.textContent = originalText;
                                copyBtn.classList.remove('bg-green-500', 'text-white');
                                copyBtn.classList.add('bg-gray-200', 'hover:bg-gray-300');
                            }, 2000);
                        });
                    });

                    // Обработчик для кнопки "Создать ещё"
                    form.querySelector('.create-new-btn').addEventListener('click', () => {
                        form.innerHTML = formContent;
                        initializeSubmitButton();
                    });
                }

                function initializeSubmitButton() {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.addEventListener('click', async function (e) {
                            e.preventDefault();
                            hideError();

                            const originalUrl = document.getElementById('original_url').value;
                            if (!originalUrl) {
                                showError('Please enter URL');
                                return;
                            }

                            if (!isValidUrl(originalUrl)) {
                                showError('Please enter a valid URL');
                                return;
                            }

                            const domain = document.getElementById('domain').value;
                            const shortCode = document.getElementById('short_code').value.trim();

                            if (shortCode && shortCode.length !== 6) {
                                showError('Short code must be exactly 6 characters');
                                return;
                            }

                            showLoader();

                            try {
                                const response = await fetch('{{ route("shorterSubmit") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                    },
                                    body: JSON.stringify({
                                        original_url: originalUrl,
                                        short_code: shortCode || null,
                                        domain: domain
                                    })
                                });

                                const data = await response.json();

                                if (!response.ok) {
                                    // Если URL уже существует, показываем существующую короткую ссылку
                                    if (response.status === 422 && data.short_url) {
                                        showSuccessView(data.short_url);
                                        return;
                                    }
                                    throw new Error(data.message || 'An error occurred');
                                }

                                showSuccessView(data.short_url);
                            } catch (error) {
                                showError(error.message);
                            } finally {
                                hideLoader();
                            }
                        });
                    }
                }

                // Инициализируем обработчик кнопки при загрузке страницы
                initializeSubmitButton();
            });
        </script>
    </x-slot>
</x-layout.default>
