@section('title', 'Clients - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="px-2 py-1 mb-4">
            <h2 class="text-lg font-semibold">Clients Table</h2>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Clients</p>
                </div>
                <form method="GET" action="{{ route('clients') }}">
                    <div class="dataTable-top">
                        <div class="dataTable-search">
                            <input value="{{ request('searchClient') }}" class="dataTable-input" placeholder="Search..."
                                   type="text" name="searchClient" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
                <div class="dataTable-container">
                    <table id="myTablesorter" class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                        <thead>
                        <tr>
                            <th class="py-2">Client ID</th>
                            <th class="py-2">Click Id</th>
                            <th class="py-2">Telegram ID</th>
                            <th class="py-2">C2D Channel Id</th>
                            <th class="py-2">Details</th>
                        </tr>
                        <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->clickid }}</td>
                                <td>{{$client->tg_id}}</td>
                                <td>{{$client->c2d_channel_id}}</td>
                                <td>
                                    <div x-data="modals">
                                        <button type="button"
                                                class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-1.5"
                                                @click="fetchClientDetails({{ $client->id }})">
                                            Details
                                        </button>
                                        <div
                                            class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                            :class="open && '!block'">
                                            <div class="flex items-start justify-center min-h-screen px-4"
                                                 @click.self="open = false">
                                                <div x-show="open" x-transition x-transition.duration.300
                                                     class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden my-8 w-full max-w-5xl">
                                                    <template x-if="client">
                                                        <div x-data="{activeunderTab:'details'}">
                                                            <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50 border-gray-200">
                                                                <li class="mr-2">
                                                                    <a href="javaScript:;"
                                                                       @click="activeunderTab = 'details'"
                                                                       :class="activeunderTab === 'details' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                       class="inline-block p-4">
                                                                        Details
                                                                    </a>
                                                                </li>
                                                                <li class="mr-2">
                                                                    <a href="javaScript:;"
                                                                       @click="activeunderTab = 'preview'"
                                                                       :class="activeunderTab === 'preview' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                       class="inline-block p-4">
                                                                        Preview
                                                                    </a>
                                                                </li>
                                                                <li class="mr-2">
                                                                    <a href="javaScript:;"
                                                                       @click="activeunderTab = 'logs'"
                                                                       :class="activeunderTab === 'logs' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                       class="inline-block p-4">
                                                                        Logs
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content mt-3 text-[13px]">
                                                                <div x-show="activeunderTab === 'details'" class="">
                                                                    <div class="p-5">
                                                                        <div class="text-sm text-black dark:text-white">
                                                                            <div
                                                                                class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                                                                                <div class="table-responsive">
                                                                                    <table
                                                                                        id="client-details-table-{{ $client->id }}">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Field</th>
                                                                                            <th>Value</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div x-show="activeunderTab === 'preview'" class="">
                                                                    <div class="p-5">
                                                                        <div class="text-sm text-black dark:text-white">
                                                                            <div
                                                                                x-show="thumbnail !== null && client !== null && client.sub11 !== null">
                                                                                <a :href="`/creatives/library?search=${client.sub11}`">
                                                                                    <img :src="thumbnail"
                                                                                         class="w-full aspect-[3/2] object-cover rounded-lg"
                                                                                         onerror="this.onerror=null; this.outerHTML='<p>Preview not found</p>'">
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div x-show="activeunderTab === 'logs'" class="">
                                                                    <div class="p-5">
                                                                        <div class="text-sm text-black dark:text-white">
                                                                            <div
                                                                                class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                                                                                <div class="table-responsive">
                                                                                    <table>
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Id</th>
                                                                                            <th>Client Id</th>
                                                                                            <th>Webhook Event</th>
                                                                                            <th>Task Status</th>
                                                                                            <th>Worker Id</th>
                                                                                            <th>Finished At</th>
                                                                                            <th>Result</th>
                                                                                            <th>Webhook Data</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        <template x-for="log in logs"
                                                                                                  :key="log.id">
                                                                                            <tr>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.id"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.client_id"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.webhook_event"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.task_status"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.worker_id"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.finished_at"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.result"></span>
                                                                                                </td>
                                                                                                <td class="whitespace-nowrap">
                                                                                                    <span
                                                                                                        x-text="log.webhook_data"></span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </template>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="loading">
                                                        <x-elements.loader/>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        </thead>
                    </table>
                </div>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        @if(!$clients->isEmpty())
                        {{ $clients->appends(request()->all())->links('vendor.pagination.optimized') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script src="/assets/js/simple-datatables.js"></script>
        <script>
            document.addEventListener("alpine:init", () => {
                Alpine.data('modals', () => ({
                    open: false,
                    client: null,
                    logs: [],
                    thumbnail: null,
                    loading: false,

                    // Функция для запроса данных клиента
                    fetchClientDetails(clientId) {
                        this.open = true
                        this.loading = true
                        // Отправляем AJAX-запрос
                        fetch(`/clients/${clientId}/details`)
                            .then(response => response.json())  // Парсим ответ в JSON
                            .then(data => {
                                // Записываем полученные данные клиента в переменные
                                this.client = data.client;     // Сохраняем данные клиента
                                this.logs = data.logs;         // Сохраняем логи
                                this.thumbnail = data.thumbnail; // Сохраняем миниатюру

                                // Используем nextTick, чтобы убедиться, что DOM обновился
                                this.$nextTick(() => {
                                    this.insertClientDetails(clientId);
                                    console.log('Модальное окно открыто для клиента:', this.client);
                                });
                            })
                            .catch(error => {
                                console.error('Ошибка при получении данных клиента:', error);
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    },

                    // Функция для вставки данных клиента в таблицу
                    insertClientDetails(clientId) {
                        const clientDetailsTable = document.querySelector(`#client-details-table-${clientId} tbody`);

                        // Проверка наличия таблицы
                        if (!clientDetailsTable) {
                            console.error('Таблица с id #client-details-table-' + clientId + ' не найдена');
                            return;
                        }

                        // Очищаем предыдущие данные
                        clientDetailsTable.innerHTML = '';

                        // Заполняем таблицу новыми данными клиента
                        for (const [field, value] of Object.entries(this.client)) {
                            const row = `<tr><td>${field}</td><td>${value}</td></tr>`;
                            clientDetailsTable.innerHTML += row;
                        }
                    }
                }));
            });
        </script>
    </x-slot>
</x-layout.default>
