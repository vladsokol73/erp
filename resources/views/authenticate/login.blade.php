<x-layout.auth>
    <div class="min-h-[calc(100vh-134px)] py-4 px-4 sm:px-12 flex justify-center items-center">
        <div
            class="max-w-[680px] flex-none w-full bg-white dark:bg-white/5 p-4 sm:p-10 lg:px-[146px] lg:py-[107px] rounded-2xl loginform">
            <h1 class="text-2xl font-semibold mb-2 text-center">Sign In</h1>
            <div class="flex items-center mb-7">
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
                <div class="text-black/40 dark:text-white/40 px-5 whitespace-nowrap">With Email</div>
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
            </div>
            <form class="mb-4" method="post" action="{{ route('loginSubmit') }}">
                @csrf
                @if($errors->any())
                    <div class="mb-4 rounded bg-lightyellow p-3 text-black">{{ $errors->first() }}</div>
                @endif
                <div class="mb-4">
                    <input id="email" type="text" required minlength="5" maxlength="30" value="" name="email" placeholder="Email" class="form-input"/>
                </div>
                <div class="mb-2">
                    <input name="password" required minlength="8" maxlength="30" type="password" value="" placeholder="Password"
                           class="form-input"/>
                </div>
                <div class="mb-7 text-right">
                    <a id="forgot-password" style="cursor: pointer" class="text-lightpurple-300">Forgot Password?</a>
                    <div style="display: none" id="forgot-alert"
                         class="flex mt-3 items-center rounded border border-lightyellow p-3 text-black dark:text-white">
                        <span class="pr-2">Contact your manager!</span>
                        <a style="cursor: pointer" id="forgot-alert-close"
                           class="ml-auto hover:opacity-50 rotate-0 hover:rotate-180 transition-all duration-300">
                            <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z"
                                    fill="currentcolor"/>
                                <path
                                    d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z"
                                    fill="currentcolor"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <button type="submit"
                        class="py-2 px-4 bg-black dark:bg-lightpurple-200 w-full rounded-lg text-white dark:text-black text-lg font-semibold border border-black dark:border-lightpurple-200 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">
                    Sign In
                </button>
            </form>

            <!-- Модальное окно для 2FA -->
            <div id="modal2FA" class="fixed inset-0 bg-black/40 dark:bg-white/10 backdrop-blur-sm z-[999] hidden flex items-center justify-center">
                <div class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden w-full max-w-xl">
                    <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                        <h5 class="font-semibold text-lg">Two-Factor Authentication</h5>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            <form id="verify2FAForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="code" class="block text-sm font-medium mb-6 text-center">Enter verification code from your authenticator app</label>
                                    <div id="loginOtp" class="flex flex-row justify-center text-center gap-3 mb-4">
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="0" autofocus>
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="1">
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="2">
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="3">
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="4">
                                        <input style="width: 70px; height: 70px; min-width: 70px;" class="text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white" type="text" maxlength="1" data-index="5">
                                    </div>
                                    <input type="hidden" id="code" name="code">
                                    <p id="verifyError" class="mt-1 text-center text-sm text-red-500 hidden"></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modal2FA');
                const form = document.getElementById('verify2FAForm');
                const verifyError = document.getElementById('verifyError');
                const otpInputs = document.querySelectorAll('#loginOtp input');
                const hiddenCode = document.getElementById('code');

                // Показываем модальное окно 2FA если есть флаг в сессии
                @if(session('show2fa'))
                    modal.classList.remove('hidden');
                @endif

                // Функция для настройки OTP инпутов
                function setupOtpInputs(inputs) {
                    inputs.forEach((input, index) => {
                        // Автофокус на следующее поле
                        input.addEventListener('input', (e) => {
                            if (e.target.value.length === 1) {
                                const nextInput = inputs[index + 1];
                                if (nextInput) {
                                    nextInput.focus();
                                } else {
                                    // Если это последнее поле и введена цифра
                                    updateHiddenInput();
                                    // Проверяем, что все поля заполнены
                                    const allFilled = Array.from(inputs).every(input => input.value.length === 1);
                                    if (allFilled) {
                                        form.dispatchEvent(new Event('submit'));
                                    }
                                }
                            }
                            updateHiddenInput();
                        });

                        // Обработка backspace
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Backspace' && !e.target.value) {
                                const prevInput = inputs[index - 1];
                                if (prevInput) {
                                    prevInput.focus();
                                }
                            }
                        });

                        // Разрешаем только цифры
                        input.addEventListener('keypress', (e) => {
                            if (!/[0-9]/.test(e.key)) {
                                e.preventDefault();
                            }
                        });

                        // Вставка из буфера обмена
                        input.addEventListener('paste', (e) => {
                            e.preventDefault();
                            const paste = e.clipboardData.getData('text');
                            const numbers = paste.match(/[0-9]/g);
                            if (numbers) {
                                numbers.forEach((number, i) => {
                                    if (inputs[index + i]) {
                                        inputs[index + i].value = number;
                                    }
                                });
                                updateHiddenInput();
                                // Фокус на последнее поле после последней цифры
                                const lastIndex = Math.min(index + numbers.length, inputs.length - 1);
                                inputs[lastIndex].focus();
                            }
                        });
                    });
                }

                // Обновление скрытого поля
                function updateHiddenInput() {
                    const code = Array.from(otpInputs).map(input => input.value).join('');
                    hiddenCode.value = code;
                }

                // Настройка OTP инпутов
                setupOtpInputs(otpInputs);

                // Очистка полей
                function clearOtpInputs() {
                    otpInputs.forEach(input => input.value = '');
                    hiddenCode.value = '';
                }

                // Функция для показа ошибки
                function showError(message) {
                    verifyError.textContent = message;
                    verifyError.classList.remove('hidden');
                }

                // Функция для скрытия ошибки
                function hideError() {
                    verifyError.classList.add('hidden');
                    verifyError.textContent = '';
                }

                // Отправка формы для верификации
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        hideError();

                        try {
                            const response = await fetch('{{ route('verify2FA') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    code: hiddenCode.value
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                window.location.href = '{{ route('home') }}';
                            } else {
                                showError(data.error || 'Invalid verification code');
                                clearOtpInputs();
                                otpInputs[0].focus();
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showError('An error occurred during verification');
                            clearOtpInputs();
                            otpInputs[0].focus();
                        }
                    });
                }
            });
            </script>
        </div>
    </div>
</x-layout.auth>
