@section('title', 'Admin Panel: Operators - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
            <div x-data="basic"
                 class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Operators</p>
                </div>


                <table id="operators"
                       class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                    <thead>
                    <tr>
                        <th class="py-2">Operator ID</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Edit</th>
                        <th class="py-2">Clear Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($operators as $operator)
                        <tr>
                            <td class="whitespace-nowrap py-2">{{ $operator->operator_id }}</td>
                            <td class="whitespace-nowrap py-2">{{ $operator->name }}</td>
                            <td class="whitespace-nowrap py-2">
                                <form method="POST"
                                      action="{{ route('updateOperator', ['id' => $operator->operator_id]) }}">
                                    @csrf
                                    <x-modals.large>
                                        <x-slot name="title">
                                            Edit Operator Name
                                        </x-slot>
                                        <x-slot name="button">
                                            <button type="button"
                                                    class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-2 px-3"
                                                    @click="toggle">
                                                Edit
                                            </button>
                                        </x-slot>

                                        <div class="text-sm text-black dark:text-white">
                                            <input name="operatorName" type="text" value="{{ $operator->name }}"
                                                   placeholder="Operator Name" required maxlength="40"
                                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                        </div>
                                    </x-modals.large>
                                </form>
                            </td>
                            <td class="py-2 flex flex-wrap text-lightred whitespace-normal">
                                <form method="post"
                                      action="{{ route("deleteOperator", $operator->operator_id) }}">
                                    @csrf
                                    <x-modals.delete>
                                        Are you sure you want to delete operator name: "{{ $operator->name }}"?
                                    </x-modals.delete>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div x-data="basic"
                 class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Channels</p>
                </div>


                <table id="channels" class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                    <thead>
                    <tr>
                        <th class="py-2">Channel ID</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Edit</th>
                        <th class="py-2">Clear Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($channels as $channel)
                        <tr>
                            <td class="whitespace-nowrap py-2">{{ $channel->channel_id }}</td>
                            <td class="whitespace-nowrap py-2">{{ $channel->name }}</td>
                            <td class="whitespace-nowrap py-2">
                                <form method="POST"
                                      action="{{ route('updateChannel', ['id' => $channel->channel_id]) }}">
                                    @csrf
                                    <x-modals.large>
                                        <x-slot name="title">
                                            Edit Channel Name
                                        </x-slot>
                                        <x-slot name="button">
                                            <button type="button"
                                                    class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-2 px-3"
                                                    @click="toggle">
                                                Edit
                                            </button>
                                        </x-slot>

                                        <div class="text-sm text-black dark:text-white">
                                            <input name="channelName" type="text" value="{{ $channel->name }}"
                                                   placeholder="Channel Name" required maxlength="40"
                                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                        </div>
                                    </x-modals.large>
                                </form>
                            </td>
                            <td class="py-2 flex flex-wrap text-lightred whitespace-normal">
                                <form method="post"
                                      action="{{ route("deleteChannel", $channel->channel_id) }}">
                                    @csrf
                                    <x-modals.delete>
                                        Are you sure you want to delete channel name: "{{ $channel->name }}"?
                                    </x-modals.delete>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <x-slot name="script">
            <!-- Simple-Datatables js -->
            <script src="/assets/js/simple-datatables.js"></script>

            <script>
                document.addEventListener("alpine:init", () => {
                    Alpine.data('basic', () => ({
                        datatable: null,
                        init() {
                            this.datatable = new simpleDatatables.DataTable('#operators', {
                                sortable: true,
                                searchable: true,
                                perPage: 10,
                                perPageSelect: [5, 10, 20, 50, 100],
                                firstLast: false,
                                firstText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                lastText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                prevText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                nextText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                labels: {
                                    perPage: '{select}',
                                },
                                layout: {
                                    top: '{select}{search}',
                                    bottom: '{info}{pager}',
                                },
                            });

                            this.datatable = new simpleDatatables.DataTable('#channels', {
                                sortable: true,
                                searchable: true,
                                perPage: 10,
                                perPageSelect: [5, 10, 20, 50, 100],
                                firstLast: false,
                                firstText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                lastText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                prevText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                nextText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                                labels: {
                                    perPage: '{select}',
                                },
                                layout: {
                                    top: '{select}{search}',
                                    bottom: '{info}{pager}',
                                },
                            });
                        },
                    }));
                });
            </script>
        </x-slot>
    </div>
</x-layout.default>
