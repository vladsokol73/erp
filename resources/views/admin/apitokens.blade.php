@section('title', 'Api Tokens - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="grid grid-cols-1 gap-7">
            <div
                 class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Api Tokens</p>
                </div>

                <div class="dataTable-top flex">
                    <div class="flex-row flex">
                        <form id="refresh_names" method="get"
                              action="{{ route("refreshOperatorsNames") }}">
                            @csrf

                            <div x-data="modals">
                                <button type="button" @click="toggle"
                                        class="mr-2 btn text-black dark:text-black border bg-blue-500 dark:bg-blue-500 border-blue-500 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white mb-5">
                                    Refresh Names
                                </button>

                                <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden overflow-y-auto"
                                     :class="open && '!block'">
                                    <div class="flex items-start justify-center min-h-screen px-4"
                                         @click.self="open = false">
                                        <div x-show="open" x-transition x-transition.duration.300
                                             class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                            <div
                                                class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                <h5 class="font-semibold text-lg">Refresh Names</h5>
                                                <button type="button"
                                                        class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                        @click="toggle">
                                                    <x-icons.cross />
                                                </button>
                                            </div>
                                            <div x-data="{activeunderTab:'profile'}">
                                                <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                                                    <li class="mr-2">
                                                        <a href="javaScript:;"
                                                           @click="activeunderTab = 'profile'"
                                                           :class="activeunderTab === 'profile' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                           class="inline-block p-4">
                                                            Services
                                                        </a>
                                                    </li>
                                                </ul>

                                                <div class="tab-content">
                                                    <div class="p-5">
                                                        <div class="text-sm text-black dark:text-white">
                                                            <div
                                                                class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                                                <div x-show="activeunderTab === 'profile'" class="">
                                                                    <div class="form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                                                    >
                                                                        <ul>
                                                                        @foreach($services as $service)
                                                                            <li class="mb-1">
                                                                                <input type="checkbox" name="services[]" class="form-checkbox outline-success" value="{{ $service }}" checked>&nbsp;{{ $service }}
                                                                            </li>
                                                                        @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="flex justify-end items-center mt-8 gap-4">
                                                                <button type="button"
                                                                        class="btn !bg-lightred !text-white"
                                                                        @click="toggle">Discard
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                                >
                                                                    Refresh
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <form id="form_create" method="POST">
                        @csrf
                        <div x-data="modals">
                            <button type="button" @click="toggle"
                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white mb-5">
                                Create Api Token
                            </button>

                            <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden overflow-y-auto"
                                 :class="open && '!block'">
                                <div class="flex items-start justify-center min-h-screen px-4"
                                     @click.self="open = false">
                                    <div x-show="open" x-transition x-transition.duration.300
                                         class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                        <div
                                            class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                            <h5 class="font-semibold text-lg">Create Api Token</h5>
                                            <button type="button"
                                                    class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                    @click="toggle">
                                                <x-icons.cross />
                                            </button>
                                        </div>

                                        <div x-data="{activeunderTab:'profile'}">
                                            <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'profile'"
                                                       :class="activeunderTab === 'profile' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       class="inline-block p-4">
                                                        Api Tokens
                                                    </a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div class="p-5">
                                                    <div class="text-sm text-black dark:text-white">
                                                        <div
                                                            id="token-modal"
                                                            class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                                            <div x-show="activeunderTab === 'profile'" class="">

                                                                <div class="mb-5">
                                                                    <p class="text-sm font-semibold">
                                                                        Service
                                                                    </p>
                                                                </div>
                                                                <select required id="service" name="service"
                                                                        class="h-10 form-select font-semibold text-sm px-2 dark:bg-black dark:text-white rounded-md form-input border border-white/10 dark:border-white/10 dark:focus:border-white/10 focus:border-white">
                                                                    @foreach($services as $service)
                                                                        <option class="px-2" value="{{ $service }}">{{ $service }}</option>
                                                                    @endforeach
                                                                </select>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Email
                                                                        </p>
                                                                    </div>
                                                                    <input id="email" name="email" type="email"
                                                                           placeholder="Email"
                                                                           required
                                                                           minlength="3"
                                                                           maxlength="30"
                                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                </div>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Token
                                                                        </p>
                                                                    </div>
                                                                    <input name="token" type="text"
                                                                           id="token"
                                                                           required
                                                                           placeholder="Token"
                                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="flex justify-end items-center mt-8 gap-4">
                                                            <button type="button"
                                                                    class="btn !bg-lightred !text-white"
                                                                    @click="toggle">Discard
                                                            </button>
                                                            <button type="submit"
                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                                    >
                                                                Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                    <form method="GET" class="dataTable-search" action="{{ route('apiTokens') }}">
                        <input value="{{ request('searchToken') }}" class="dataTable-input" placeholder="Search..."
                               type="text" name="searchToken" onchange="this.form.submit()">
                    </form>
                </div>


                <table id="users" class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                    <thead>
                    <tr>
                        <th class="py-2">ID</th>
                        <th class="py-2">Service</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Token</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($apiTokens as $token)
                        <tr>
                            <td class="whitespace-nowrap py-2">{{ $token->id }}</td>
                            <td class="py-2">{{ $token->service }}</td>
                            <td class="py-2">{{ $token->email }}</td>
                            <td class="py-2 font-mono">{{ $token->censored_token }}</td>
                            <!--EDIT MODAL-->
                            <td class="py-2">
                                <form id="form_edit_{{ $token->id }}" method="post"
                                      action="{{ route("editApiToken", $token->id) }}">
                                    @csrf
                                    <div x-data="modals">
                                        <button type="button"
                                                class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-2 px-3"
                                                @click="toggle">
                                            Edit
                                        </button>
                                        <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden"
                                             :class="open && '!block'">
                                            <div class="flex items-start justify-center min-h-screen px-4"
                                                 @click.self="open = false">
                                                <div x-show="open" x-transition x-transition.duration.300
                                                     class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden  w-full max-w-xl my-8">
                                                    <div
                                                        class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                        <h5 class="font-semibold text-lg">Edit Api Token</h5>
                                                        <button type="button"
                                                                class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                                @click="toggle">
                                                            <x-icons.cross />
                                                        </button>
                                                    </div>
                                                    <div x-data="{activeunderTab:'profile'}">
                                                        <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'profile'"
                                                                   :class="activeunderTab === 'profile' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4">
                                                                    Api token
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <div class="p-5">
                                                                <div class="text-sm text-black dark:text-white">
                                                                    <div
                                                                        class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                                                        <div x-show="activeunderTab === 'profile'">
                                                                            <div class="mb-5">
                                                                                <p class="text-sm font-semibold">
                                                                                    Service
                                                                                </p>
                                                                            </div>
                                                                            <select required id="service_{{ $token->id }}" name="service"
                                                                                    class="h-10 form-select font-semibold text-sm px-2 dark:bg-black dark:text-white rounded-md form-input border border-white/10 dark:border-white/10 dark:focus:border-white/10 focus:border-white">
                                                                                @foreach($services as $service)
                                                                                    <option class="px-2" value="{{ $service }}" @if($service == $token->service) selected @endif>{{ $service }}</option>
                                                                                @endforeach
                                                                            </select>

                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Email
                                                                                    </p>
                                                                                </div>
                                                                                <input id="email_{{ $token->id }}" name="email" type="email"
                                                                                       placeholder="Email"
                                                                                       required
                                                                                       value="{{ $token->email }}"
                                                                                       minlength="3"
                                                                                       maxlength="30"
                                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                            </div>

                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Token
                                                                                    </p>
                                                                                </div>
                                                                                <input name="token" type="text"
                                                                                       id="token_{{ $token->id }}"
                                                                                       required
                                                                                       value="{{ $token->token }}"
                                                                                       placeholder="Token"
                                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="flex justify-end items-center mt-8 gap-4">
                                                                            <button type="button"
                                                                                    class="btn !bg-lightred !text-white"
                                                                                    @click="toggle">Discard
                                                                            </button>
                                                                            <button type="submit"
                                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                                                    >
                                                                                Save
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            <td class="py-2 flex flex-wrap text-lightred whitespace-normal">
                                <form method="post"
                                      action="{{ route("deleteApiToken", $token->id) }}">
                                    @csrf
                                    <div x-data="modals">
                                        <button type="button"
                                                class="flex justify-end items-center btn text-xs h-auto leading-none text-black dark:text-black border bg-red-600 dark:bg-red-600 border-red-600 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-1"
                                                @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                 fill="currentColor" viewBox="0 0 256 256">
                                                <path
                                                    d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                                            </svg>
                                            <p class="mt-1">Delete</p>
                                        </button>
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
                                                            <p>Are you sure you want to delete a token for "{{ $token->service }}" and
                                                                Email: "{{ $token->email }}" <br/> Token: {{ $token->token }}  ?</p>
                                                        </div>
                                                        <div class="flex justify-end items-center mt-8 gap-4">
                                                            <button type="button"
                                                                    class="btn"
                                                                    @click="toggle">Discard
                                                            </button>
                                                            <button type="submit" class="btn !bg-lightred !text-white">
                                                                Confirm
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        @if(!$apiTokens->isEmpty())
                            {{ $apiTokens->appends(request()->all())->links('vendor.pagination.optimized') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('form_create').addEventListener('submit', async function(e) {
                e.preventDefault(); // Предотвращаем стандартную отправку формы

                const modalContent = document.getElementById('token-modal'); // Место для отображения сообщений

                const formData = new FormData(this); // Собираем данные формы

                const csrfToken = document.querySelector('input[name="_token"]').value;

                modalContent.innerHTML = `<x-elements.loader/>`; // Показываем индикатор загрузки

                try {
                    const response = await fetch('/admin-panel/api-tokens', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            // Оставьте Content-Type неуказанным для FormData (автоматически устанавливается)
                        },
                        body: formData,  // Передаём данные формы напрямую
                    });

                    const result = await response.json(); // Парсим ответ сервера как JSON

                    if (!response.ok) {
                        // Обработка ошибок от сервера
                        modalContent.innerHTML = `
                <x-elements.alerts.simple-danger>
                    Error: ${result.message || 'Failed to create token.'}
                </x-elements.alerts.simple-danger>
            `;
                        // Перезагружаем страницу через 3 секунды
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        // Успешное создание токена
                        modalContent.innerHTML = `
                <x-elements.alerts.simple-success>
                    Token successfully created!
                </x-elements.alerts.simple-success>
            `;
                        // Перезагружаем страницу через 3 секунды
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    }
                } catch (error) {
                    // Обработка сетевых ошибок
                    console.error('Error with request:', error);
                    modalContent.innerHTML = `
            <x-elements.alerts.simple-danger>
                An unexpected error occurred. Please try again.
            </x-elements.alerts.simple-danger>
        `;
                }
            });

            // Функция для закрытия модального окна
            function closeModal() {
                const modal = document.querySelector('[x-data="modals"]'); // Находим модальное окно
                if (modal && modal.__x) {
                    modal.__x.$data.open = false; // Устанавливаем open в false
                } else {
                    console.warn('Modal is not available or Alpine.js is not initialized');
                }
            }
        </script>

        <x-slot name="script">
            <!-- Simple-Datatables js -->
            <script src="/assets/js/simple-datatables.js"></script>
        </x-slot>
    </div>
</x-layout.default>
