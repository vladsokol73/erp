@section('title', 'Account Settings - Gteam')
<x-layout.default>
    <div class="h-[calc(100vh-73px)] overflow-y-auto overflow-x-hidden">
        <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
            <div id="loader" x-cloak class="hidden fixed inset-0 flex items-center justify-center backdrop-blur-sm z-50">
                <x-elements.loader/>
            </div>
            <div class="bg-lightwhite dark:bg-white/5 rounded-2xl p-6">
                <!-- Header -->
                <div class="flex flex-wrap items-start justify-between gap-3 mb-8">
                    <div>
                        <h2 class="text-xl font-semibold mb-1">Account Settings</h2>
                        <p class="text-sm text-black/60 dark:text-white/60">Manage your account settings and
                            preferences</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left column: Basic Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            class="rounded-xl p-6 border border-black/10 dark:border-white/10">
                            <h3 class="text-base font-semibold mb-6">Basic Information</h3>
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-semibold mb-2">Email</p>
                                    <p class="py-1.5 w-full text-black dark:text-white">
                                        {{ $user->email }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold mb-2">Name</p>
                                    <p class="py-1.5 w-full text-black dark:text-white">
                                        {{ $user->name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold mb-2">Role</p>
                                    <p class="py-1.5 w-full text-black dark:text-white">
                                        @foreach($user->roles as $role)
                                            {{ $role->title }}
                                        @endforeach
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold mb-2">Last Login</p>
                                    <p class="py-1.5 w-full text-black dark:text-white">
                                        {{ $user->last_login_at?->format('d.m.Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Access -->
                        <div
                            class="rounded-xl p-6 border border-black/10 dark:border-white/10">
                            <h3 class="text-base font-semibold mb-6">Access</h3>
                            <div class="space-y-6">
                                <!-- Available Countries -->
                                <div>
                                    <p class="text-sm font-semibold mb-2">Available Countries</p>
                                    <div
                                        class="flex flex-wrap gap-2 py-2.5 w-full min-h-[48px] text-black dark:text-white rounded-lg border-b border-white/10">
                                        @foreach($availableCountries as $country)
                                            <span
                                                class="px-3 py-1 bg-black/10 dark:bg-white/10 text-black dark:text-white rounded-full text-sm">
                                                {{ $country }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Available Channels -->
                                @if($user->hasPermissionTo('clients.show') || $user->hasPermissionTo('operators.show'))
                                    <div>
                                        <p class="text-sm font-semibold mb-2">Available Channels</p>
                                        <div
                                            class="flex flex-wrap gap-2 py-2.5 w-full min-h-[48px] text-black dark:text-white rounded-lg border-b border-white/10">
                                            @foreach($availableClients as $client)
                                                <span
                                                    class="px-3 py-1 bg-black/10 dark:bg-white/10 text-black dark:text-white rounded-full text-sm">
                                                {{ $client }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($user->hasPermissionTo('operators.show'))
                                    <!-- Available Operators -->
                                    <div>
                                        <p class="text-sm font-semibold mb-2">Available Operators</p>
                                        <div
                                            class="flex flex-wrap gap-2 py-2.5 w-full min-h-[48px] text-black dark:text-white rounded-lg">
                                            @foreach($availableOperators as $operator)
                                                <span
                                                    class="px-3 py-1 bg-black/10 dark:bg-white/10 text-black dark:text-white rounded-full text-sm">
                                                {{ $operator }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-lightpurple-100/50 dark:bg-white/5 rounded-lg p-4 flex items-start gap-1">
                            <div class="flex-none">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M8.00135 17.9611C8.00135 17.9611 8.8251 18.3957 9.59934 18.6529C9.59934 18.6529 9.99999 18.797 10.4006 18.6529C10.4006 18.6529 11.1749 18.3957 11.9987 17.9611C11.9987 17.9611 13.3229 17.2625 14.3744 16.2949C14.3744 16.2949 17.5 13.4184 17.5 8.96094V4.375C17.5 4.375 17.5 3.85723 17.1339 3.49112C17.1339 3.49112 16.7678 3.125 16.25 3.125H3.75C3.75 3.125 3.23223 3.125 2.86612 3.49112C2.86612 3.49112 2.5 3.85723 2.5 4.375V8.96094C2.5 8.96094 2.5 13.4184 5.6256 16.2949C5.6256 16.2949 6.6771 17.2625 8.00135 17.9611ZM11.4154 16.8555C11.4154 16.8555 10.6794 17.2438 10 17.4688C10 17.4688 9.32065 17.2438 8.58459 16.8555C8.58459 16.8555 7.40402 16.2327 6.47206 15.3751C6.47206 15.3751 3.75 12.87 3.75 8.96094V4.375H16.25V8.96094C16.25 8.96094 16.25 12.87 13.5279 15.3751C13.5279 15.3751 12.596 16.2327 11.4154 16.8555Z"
                                          fill="currentcolor"></path>
                                    <path
                                        d="M6.9943 9.86065C6.87801 9.74952 6.72335 9.6875 6.5625 9.6875C6.56237 9.6875 6.54832 9.68766 6.54832 9.68766C6.38261 9.69142 6.22517 9.76086 6.11065 9.8807C5.99952 9.99699 5.9375 10.1516 5.9375 10.3125L5.93766 10.3267C5.94142 10.4924 6.01086 10.6498 6.1307 10.7644L8.41976 12.9519C8.66119 13.1826 9.04135 13.1827 9.28298 12.9522L13.8688 8.57737C13.9887 8.46295 14.0584 8.30542 14.0623 8.13971C14.0626 8.1297 14.0626 8.11969 14.0623 8.10968C14.0585 7.95435 13.997 7.806 13.8897 7.69358C13.7718 7.56995 13.6084 7.5 13.4375 7.5L13.4199 7.50025C13.2653 7.50461 13.1179 7.56608 13.0061 7.67278L8.85193 11.6359L6.9943 9.86065Z"
                                        fill="currentcolor"></path>
                                </svg>
                            </div>
                            <div class="flex flex-1 items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-sm">Secure Your Account</h3>
                                    <p class="text-xs text-black/40 dark:text-white/40">Two-factor authentication adds
                                        an extra layer of security to your account. To log in, in addition you'll need
                                        to provide a 6 digit code.</p>
                                </div>
                                <div>
                                    @if(!$user->google2fa_enabled)
                                        <button type="button" id="enable2FABtn"
                                                class="btn flex-none text-xs px-2 py-[5px]">Enable
                                        </button>
                                    @else
                                        <button type="button" id="disable2FABtn"
                                                class="btn flex-none text-xs px-2 py-[5px] bg-red-500 hover:bg-red-600 text-white">
                                            Disable
                                        </button>
                                    @endif

                                    <!-- Уведомление об успешном действии -->
                                    <div id="twoFactorSuccess"
                                         class="fixed top-20 right-4 z-[1000] hidden transition-opacity duration-300">
                                        <div
                                            class="bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-lg shadow-lg">
                                            <p id="twoFactorMessage"></p>
                                        </div>
                                    </div>

                                    <!-- Модальное окно для включения 2FA -->
                                    <div id="modal2FA"
                                         class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden">
                                        <div class="flex items-start justify-center min-h-screen px-4">
                                            <div
                                                class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden w-full max-w-xl my-8">
                                                <div
                                                    class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                    <h5 class="font-semibold text-lg">Setup Two-Factor
                                                        Authentication</h5>
                                                    <button type="button" id="closeModal2FA"
                                                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white">
                                                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32"
                                                             fill="none"
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
                                                    <div class="space-y-4">
                                                        <div id="qrSection" class="text-center">
                                                            <p class="mb-4">Scan this QR code with your Google
                                                                Authenticator app</p>
                                                            <div id="qrCode" class="flex justify-center mb-4">
                                                                {!! $qrCode !!}
                                                            </div>
                                                            <p class="text-sm text-black/40 dark:text-white/40">Or enter
                                                                this code manually: <span id="secretKey"
                                                                                          class="font-mono">{{ auth()->user()->google2fa_secret }}</span>
                                                            </p>
                                                        </div>

                                                        <form id="verify2FAForm" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label for="code"
                                                                       class="block text-sm font-medium mb-2 text-center">Verification
                                                                    Code</label>
                                                                <div id="enableOtp"
                                                                     class="flex flex-row justify-center text-center gap-2 mb-2">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="0">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="1">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="2">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="3">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="4">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="5">
                                                                </div>
                                                                <input type="hidden" id="code" name="code">
                                                                <p id="enableError"
                                                                   class="mt-1 text-sm text-red-500 hidden"></p>
                                                            </div>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" id="cancelModal2FA"
                                                                        class="btn btn-outline">Cancel
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">Verify &
                                                                    Enable
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Модальное окно для отключения 2FA -->
                                    <div id="modalDisable2FA"
                                         class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden">
                                        <div class="flex items-start justify-center min-h-screen px-4">
                                            <div
                                                class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden w-full max-w-xl my-8">
                                                <div
                                                    class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                    <h5 class="font-semibold text-lg">Disable Two-Factor
                                                        Authentication</h5>
                                                    <button type="button" id="closeModalDisable2FA"
                                                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white">
                                                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32"
                                                             fill="none"
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
                                                    <div class="space-y-4">
                                                        <p>Are you sure you want to disable two-factor authentication?
                                                            This will make your account less secure.</p>

                                                        <form id="disable2FAForm" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label for="disableCode"
                                                                       class="block text-sm font-medium mb-2 text-center">Enter
                                                                    your
                                                                    verification code to confirm</label>
                                                                <div id="disableOtp"
                                                                     class="flex flex-row justify-center text-center gap-2 mb-2">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="0">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="1">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="2">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="3">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="4">
                                                                    <input
                                                                        class="w-[58px] h-[58px] text-center text-2xl font-semibold rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white"
                                                                        type="text" maxlength="1" data-index="5">
                                                                </div>
                                                                <input type="hidden" id="disableCode" name="code">
                                                                <p id="disableError"
                                                                   class="mt-1 text-sm text-red-500 hidden"></p>
                                                            </div>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" id="cancelModalDisable2FA"
                                                                        class="btn btn-outline">Cancel
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn bg-red-500 hover:bg-red-600 text-white">
                                                                    Disable
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const modal = document.getElementById('modal2FA');
                                            const modalDisable = document.getElementById('modalDisable2FA');
                                            const enableBtn = document.getElementById('enable2FABtn');
                                            const disableBtn = document.getElementById('disable2FABtn');
                                            const closeBtn = document.getElementById('closeModal2FA');
                                            const closeDisableBtn = document.getElementById('closeModalDisable2FA');
                                            const cancelBtn = document.getElementById('cancelModal2FA');
                                            const cancelDisableBtn = document.getElementById('cancelModalDisable2FA');
                                            const form = document.getElementById('verify2FAForm');
                                            const disableForm = document.getElementById('disable2FAForm');
                                            const enableError = document.getElementById('enableError');
                                            const disableError = document.getElementById('disableError');
                                            const enableOtpInputs = document.querySelectorAll('#enableOtp input');
                                            const disableOtpInputs = document.querySelectorAll('#disableOtp input');

                                            // Функция для показа уведомления об успехе
                                            function showSuccessMessage(message) {
                                                const notification = document.getElementById('twoFactorSuccess');
                                                const messageEl = document.getElementById('twoFactorMessage');
                                                messageEl.textContent = message;
                                                notification.classList.remove('hidden');

                                                setTimeout(() => {
                                                    notification.classList.add('opacity-0');
                                                    setTimeout(() => {
                                                        notification.classList.add('hidden');
                                                        notification.classList.remove('opacity-0');
                                                        window.location.reload();
                                                    }, 300);
                                                }, 2000);
                                            }

                                            // Функция для настройки OTP инпутов
                                            function setupOtpInputs(inputs, hiddenInput) {
                                                inputs.forEach((input, index) => {
                                                    // Автофокус на следующее поле
                                                    input.addEventListener('input', (e) => {
                                                        if (e.target.value.length === 1) {
                                                            const nextInput = inputs[index + 1];
                                                            if (nextInput) {
                                                                nextInput.focus();
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

                                                // Обновление скрытого поля
                                                function updateHiddenInput() {
                                                    const code = Array.from(inputs).map(input => input.value).join('');
                                                    document.getElementById(hiddenInput).value = code;
                                                }
                                            }

                                            // Настройка OTP инпутов для обеих форм
                                            setupOtpInputs(enableOtpInputs, 'code');
                                            setupOtpInputs(disableOtpInputs, 'disableCode');

                                            // Очистка полей при закрытии модальных окон
                                            function clearOtpInputs(inputs) {
                                                inputs.forEach(input => input.value = '');
                                            }

                                            // Функция для показа ошибки
                                            function showError(element, message) {
                                                element.textContent = message;
                                                element.classList.remove('hidden');
                                            }

                                            // Функция для скрытия ошибки
                                            function hideError(element) {
                                                element.classList.add('hidden');
                                                element.textContent = '';
                                            }

                                            // Открытие модального окна для включения
                                            if (enableBtn) {
                                                enableBtn.addEventListener('click', function () {
                                                    modal.classList.remove('hidden');
                                                    hideError(enableError);
                                                    clearOtpInputs(enableOtpInputs);
                                                    enableOtpInputs[0].focus();
                                                });
                                            }

                                            // Открытие модального окна для отключения
                                            if (disableBtn) {
                                                disableBtn.addEventListener('click', function () {
                                                    modalDisable.classList.remove('hidden');
                                                    hideError(disableError);
                                                    clearOtpInputs(disableOtpInputs);
                                                    disableOtpInputs[0].focus();
                                                });
                                            }

                                            // Закрытие модальных окон
                                            function closeModal() {
                                                modal.classList.add('hidden');
                                                hideError(enableError);
                                                clearOtpInputs(enableOtpInputs);
                                            }

                                            function closeDisableModal() {
                                                modalDisable.classList.add('hidden');
                                                hideError(disableError);
                                                clearOtpInputs(disableOtpInputs);
                                            }

                                            if (closeBtn) closeBtn.addEventListener('click', closeModal);
                                            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
                                            if (closeDisableBtn) closeDisableBtn.addEventListener('click', closeDisableModal);
                                            if (cancelDisableBtn) cancelDisableBtn.addEventListener('click', closeDisableModal);

                                            // Закрытие при клике вне модального окна
                                            modal.addEventListener('click', function (e) {
                                                if (e.target === modal) {
                                                    closeModal();
                                                }
                                            });

                                            modalDisable.addEventListener('click', function (e) {
                                                if (e.target === modalDisable) {
                                                    closeDisableModal();
                                                }
                                            });

                                            // Отправка формы для включения
                                            if (form) {
                                                form.addEventListener('submit', async function (e) {
                                                    e.preventDefault();
                                                    hideError(enableError);

                                                    try {
                                                        const response = await fetch('{{ route('account2FAEnable') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                                            },
                                                            body: JSON.stringify({
                                                                code: document.getElementById('code').value
                                                            })
                                                        });

                                                        const data = await response.json();

                                                        if (response.ok) {
                                                            closeModal();
                                                            showSuccessMessage('Two-factor authentication has been successfully enabled');
                                                        } else {
                                                            showError(enableError, data.error || 'An error occurred while enabling 2FA');
                                                            clearOtpInputs(enableOtpInputs);
                                                            enableOtpInputs[0].focus();
                                                        }
                                                    } catch (error) {
                                                        console.error('Error:', error);
                                                        showError(enableError, 'An error occurred while enabling 2FA');
                                                        clearOtpInputs(enableOtpInputs);
                                                        enableOtpInputs[0].focus();
                                                    }
                                                });
                                            }

                                            // Отправка формы для отключения
                                            if (disableForm) {
                                                disableForm.addEventListener('submit', async function (e) {
                                                    e.preventDefault();
                                                    hideError(disableError);

                                                    try {
                                                        const response = await fetch('{{ route('account2FADisable') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                                            },
                                                            body: JSON.stringify({
                                                                code: document.getElementById('disableCode').value
                                                            })
                                                        });

                                                        const data = await response.json();

                                                        if (response.ok) {
                                                            closeDisableModal();
                                                            showSuccessMessage('Two-factor authentication has been successfully disabled');
                                                        } else {
                                                            showError(disableError, data.error || 'An error occurred while disabling 2FA');
                                                            clearOtpInputs(disableOtpInputs);
                                                            disableOtpInputs[0].focus();
                                                        }
                                                    } catch (error) {
                                                        console.error('Error:', error);
                                                        showError(disableError, 'An error occurred while disabling 2FA');
                                                        clearOtpInputs(disableOtpInputs);
                                                        disableOtpInputs[0].focus();
                                                    }
                                                });
                                            }
                                        });
                                    </script>
                                </div>

                                <script>
                                    document.getElementById('password-form').addEventListener('submit', function (e) {
                                        e.preventDefault();

                                        // Очищаем предыдущие ошибки
                                        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
                                        document.getElementById('password-message').classList.add('hidden');

                                        const formData = new FormData(this);

                                        fetch('{{ route('accountPasswordReset') }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify(Object.fromEntries(formData))
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                const messageDiv = document.getElementById('password-message');

                                                if (data.success) {
                                                    // Успешная смена пароля
                                                    messageDiv.textContent = data.message;
                                                    messageDiv.classList.remove('hidden', 'bg-lightyellow', 'text-black');
                                                    messageDiv.classList.add('bg-emerald-500/20', 'text-emerald-600', 'dark:text-emerald-400');

                                                    // Очищаем форму
                                                    this.reset();

                                                    // Скрываем сообщение через 3 секунды
                                                    setTimeout(() => {
                                                        messageDiv.classList.add('opacity-0');
                                                        setTimeout(() => {
                                                            messageDiv.classList.add('hidden');
                                                            messageDiv.classList.remove('opacity-0');
                                                        }, 300); // Время анимации
                                                    }, 3000);
                                                } else {
                                                    // Показываем ошибки валидации
                                                    if (data.errors) {
                                                        Object.keys(data.errors).forEach(key => {
                                                            const errorSpan = document.getElementById(key + '_error');
                                                            if (errorSpan) {
                                                                errorSpan.textContent = data.errors[key][0];
                                                                errorSpan.classList.remove('hidden');
                                                            }
                                                        });
                                                    } else {
                                                        messageDiv.textContent = data.message;
                                                        messageDiv.classList.remove('hidden', 'bg-emerald-500/20');
                                                        messageDiv.classList.add('bg-lightyellow', 'text-black');
                                                    }
                                                }
                                            })
                                            .catch(error => {
                                                const messageDiv = document.getElementById('password-message');
                                                messageDiv.textContent = 'An error occurred while changing password.';
                                                messageDiv.classList.remove('hidden', 'bg-emerald-500/20');
                                                messageDiv.classList.add('bg-lightyellow', 'text-black');
                                            });
                                    });
                                </script>

                                <style>
                                    #password-message {
                                        transition: opacity 0.3s ease-in-out;
                                    }
                                </style>
                            </div>
                        </div>

                        <h2 class="text-sm font-semibold mb-4 border-t border-white/10 pt-3">Connect Notifications</h2>
                        <div class="flex items-center gap-4 py-3">
                            <img src="/assets/images/telegram.svg" alt="images" class="flex-none h-[32px] w-[32px]">
                            <div class="flex-1 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold">Telegram</p>
                                    @if(auth()->user()->telegramIntegrations()->count() === 0)
                                    <p class="text-xs text-black/40 dark:text-white/40">Connect Telegram
                                        Notifications</p>
                                    @else
                                        <p class="text-xs text-black/40 dark:text-white/40">Notifications Connected</p>
                                    @endif
                                </div>
                                @if(!auth()->user()->activeTelegramIntegrations())
                                    <button type="button" id="open-modal-btn" class="btn flex-none text-xs px-2 py-[5px]">
                                        Enable
                                    </button>

                                    <x-modals.view id="connect-tg">
                                        <x-slot:title>Connect Telegram</x-slot:title>
                                        <a id="tg-link"
                                           target="_blank"
                                           href="#"
                                           class="btn text-black dark:text-white border hover:bg-indigo-300 dark:hover:bg-indigo-300 border-indigo-300 bg-transparent hover:text-black">
                                            Open Link
                                        </a>
                                    </x-modals.view>
                                @else
                                    <button type="button" id="disableButton" class="btn flex-none text-xs px-2 py-[5px]">
                                        Disable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right column: Permissions and Password -->
                    <div class="space-y-6">
                        <!-- Permissions -->
                        <div
                            class="rounded-xl p-6 border border-black/10 dark:border-white/10">
                            <h3 class="text-base font-semibold mb-6">Permissions</h3>
                            <div class="space-y-2">
                                @foreach($groupedPermissions as $group => $permissions)
                                    <p class="text-sm font-semibold mb-2">{{ ucfirst($group) }}</p>
                                    <div class="mb-5 flex flex-wrap border-b border-white/10">
                                        @foreach($permissions as $permission)
                                            <div
                                                class="flex items-center py-2 pr-3 rounded-lg hover:bg-black/5 dark:hover:bg-white/5">
                                                <svg class="h-5 w-5 text-black dark:text-white shrink-0" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="ml-3 text-sm">{{ $permission->guard_name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div
                            class="rounded-xl p-6 border border-black/10 dark:border-white/10">
                            <h3 class="text-base font-semibold mb-6">Change Password</h3>

                            <div id="password-message" class="mb-4 rounded-lg p-3 text-sm hidden"></div>

                            <form id="password-form" class="space-y-4">
                                @csrf
                                <div class="sr-only">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" id="username" autocomplete="username"
                                           value="{{ auth()->user()->email }}" readonly>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold mb-2">Current Password</p>
                                    <input
                                        type="password"
                                        name="current_password"
                                        id="current_password"
                                        placeholder="Enter Your Password"
                                        autocomplete="current-password"
                                        class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                    >
                                    <span class="text-red-500 text-xs mt-1 hidden" id="current_password_error"></span>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold mb-2">New Password</p>
                                    <input
                                        type="password"
                                        name="new_password"
                                        id="new_password"
                                        placeholder="Enter New Password"
                                        autocomplete="new-password"
                                        class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                    >
                                    <span class="text-red-500 text-xs mt-1 hidden" id="new_password_error"></span>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold mb-2">Confirm Password</p>
                                    <input
                                        type="password"
                                        name="new_password_confirmation"
                                        id="new_password_confirmation"
                                        placeholder="Repeat New Password"
                                        autocomplete="new-password"
                                        class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                    >
                                    <span class="text-red-500 text-xs mt-1 hidden"
                                          id="new_password_confirmation_error"></span>
                                </div>

                                <button type="submit"
                                        class="mt-2 py-2.5 px-4 bg-black dark:bg-lightpurple-200 w-full rounded-lg text-white dark:text-black text-sm font-semibold border border-black dark:border-lightpurple-200 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">
                                    Change Password
                                </button>
                            </form>
                            <style>
                                #password-message {
                                    transition: opacity 0.3s ease-in-out;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Проверка, что элементы загружены
            const openModalBtn = document.getElementById("open-modal-btn");
            const tgLink = document.getElementById("tg-link");
            const loader = document.getElementById("loader");
            const disableButton = document.getElementById('disableButton');

            // Обработчик для кнопки "Enable" (открытие модалки и получение ссылки)
            if (openModalBtn) {
                openModalBtn.addEventListener("click", async function () {
                    loader.classList.remove('hidden');

                    try {
                        let response = await fetch("/get-telegram-link"); // Путь для получения ссылки
                        let data = await response.json();

                        if (data.link) {
                            tgLink.href = data.link; // Установить ссылку
                        }
                    } catch (error) {
                        console.error("Ошибка загрузки ссылки:", error);
                    } finally {
                        loader.classList.add('hidden');
                        window.dispatchEvent(new CustomEvent("open-modal", { detail: "connect-tg" })); // Открыть модалку
                    }
                });
            } else {
                console.warn('openModalBtn не найден');
            }

            // Обработчик для кнопки "Disable" (удаление интеграции Telegram)
            if (disableButton) {
                disableButton.addEventListener('click', function () {
                    let url = "{{ route('destroyTelegram') }}";
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { message: 'Telegram notifications successfully deleted!', type: 'success' }
                                }));
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            } else {
                console.warn('disableButton не найден');
            }
        });
    </script>

    <script>
        document.getElementById('password-form').addEventListener('submit', function (e) {
            e.preventDefault();

            // Очищаем предыдущие ошибки
            document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
            document.getElementById('password-message').classList.add('hidden');

            const formData = new FormData(this);

            fetch('{{ route('accountPasswordReset') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById('password-message');

                    if (data.success) {
                        // Успешная смена пароля
                        messageDiv.textContent = data.message;
                        messageDiv.classList.remove('hidden', 'bg-lightyellow', 'text-black');
                        messageDiv.classList.add('bg-emerald-500/20', 'text-emerald-600', 'dark:text-emerald-400');

                        // Очищаем форму
                        this.reset();

                        // Скрываем сообщение через 3 секунды
                        setTimeout(() => {
                            messageDiv.classList.add('opacity-0');
                            setTimeout(() => {
                                messageDiv.classList.add('hidden');
                                messageDiv.classList.remove('opacity-0');
                            }, 300); // Время анимации
                        }, 3000);
                    } else {
                        // Показываем ошибки валидации
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const errorSpan = document.getElementById(key + '_error');
                                if (errorSpan) {
                                    errorSpan.textContent = data.errors[key][0];
                                    errorSpan.classList.remove('hidden');
                                }
                            });
                        } else {
                            messageDiv.textContent = data.message;
                            messageDiv.classList.remove('hidden', 'bg-emerald-500/20');
                            messageDiv.classList.add('bg-lightyellow', 'text-black');
                        }
                    }
                })
                .catch(error => {
                    const messageDiv = document.getElementById('password-message');
                    messageDiv.textContent = 'An error occurred while changing password.';
                    messageDiv.classList.remove('hidden', 'bg-emerald-500/20');
                    messageDiv.classList.add('bg-lightyellow', 'text-black');
                });
        });
    </script>
</x-layout.default>
