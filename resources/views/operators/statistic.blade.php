@section('title', 'Operators Statistic - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="grid grid-cols-1 gap-7">
            <!-- Фильтры -->
            <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <form method="GET" action="{{ route('operators.statistic') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Фильтр по дате/интервалу -->
                        <div>
                            <div>
                                <label class="text-sm font-semibold mb-2 block">Date Range:</label>
                                <input type="text" id="dateRange" name="date_range"
                                       value="{{ request('date_range') ?? now()->toDateString() }}"
                                       class="border rounded-md px-3 py-2 w-full dark:bg-gray-700 dark:border-gray-600 text-white"/>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-7 mt-5">
                                <div class="bg-lightblue-100 rounded-2xl p-6">
                                    <p class="text-sm font-semibold text-black mb-2">Total Clients</p>
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-2xl leading-9 font-semibold text-black">{{ $all_clients }}</h2>
                                    </div>
                                </div>
                                <div class="bg-lightpurple-100 rounded-2xl p-6">
                                    <p class="text-sm font-semibold text-black mb-2">Total New Clients</p>
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-2xl leading-9 font-semibold text-black">{{ $all_new_clients }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Фильтр по оператору -->
                        <div class="mb-5">
                            <p class="text-sm font-semibold mb-2">Operator:</p>
                            <input type="text" id="searchOperator" placeholder="Search operator..."
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none mb-2">

                            <!-- Переключатель include/exclude -->
                            <div class="togglebutton out-line inline-block mb-2">
                                <label for="filterToggleOperators" class="flex items-center cursor-pointer">
                                    <span class="relative">
                                        <!-- Сам переключатель -->
                                        <input
                                            type="checkbox"
                                            id="filterToggleOperators"
                                            name="filter_mode_operators"
                                            class="sr-only"
                                            {{ request('filter_mode_operators') === 'on' ? 'checked' : '' }}
                                        >
                                        <!-- Блок для визуализации -->
                                        <span
                                            class="block band border border-black/10 dark:border-white/10 w-[52px] h-7 rounded-full"></span>
                                        <!-- Движущийся индикатор -->
                                        <span
                                            class="dot absolute left-1 top-[3px] bg-black/50 dark:bg-white/50 w-[22px] h-[22px] rounded-full transition"></span>
                                    </span>
                                </label>
                                <!-- Динамическая метка -->
                                <div class="text-xs mt-2 text-gray-500 dark:text-gray-400" id="filterModeLabelOperators">
                                    Include
                                </div>
                            </div>

                            <div class="mb-2 flex items-center gap-2">
                                <input type="checkbox" id="showOnlySelectedOperators" class="form-checkbox"/>
                                <label for="showOnlySelectedOperators" class="text-sm font-semibold">Show only
                                    selected</label>
                            </div>
                            <select id="operatorSelect" name="operator_id[]" multiple="multiple" size="10"
                                    class="focus:outline-none form-multiselect py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                @foreach($allOperators as $operator)
                                    <option
                                        value="{{ $operator->operator_id }}" {{ in_array($operator->operator_id, request('operator_id', [])) ? 'selected' : '' }}>
                                        Operator
                                        @if(!is_null($operator->name))
                                            {{ $operator->name }}
                                        @else
                                            {{ $operator->operator_id }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по каналу -->
                        <div class="mb-5">
                            <p class="text-sm font-semibold mb-2">Channel:</p>
                            <input type="text" id="searchChannel" placeholder="Search channel..."
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none mb-2">

                            <!-- Переключатель include/exclude -->
                            <div class="togglebutton out-line inline-block mb-2">
                                <label for="filterToggleChannels" class="flex items-center cursor-pointer">
                                    <span class="relative">
                                        <!-- Сам переключатель -->
                                        <input
                                            type="checkbox"
                                            id="filterToggleChannels"
                                            name="filter_mode_channels"
                                            class="sr-only"
                                            {{ request('filter_mode_channels') === 'on' ? 'checked' : '' }}
                                        >
                                        <!-- Блок для визуализации -->
                                        <span
                                            class="block band border border-black/10 dark:border-white/10 w-[52px] h-7 rounded-full"></span>
                                        <!-- Движущийся индикатор -->
                                        <span
                                            class="dot absolute left-1 top-[3px] bg-black/50 dark:bg-white/50 w-[22px] h-[22px] rounded-full transition"></span>
                                    </span>
                                </label>
                                <!-- Динамическая метка -->
                                <div class="text-xs mt-2 text-gray-500 dark:text-gray-400" id="filterModeLabelChannels">
                                    Include
                                </div>
                            </div>

                            <div class="mb-2 flex items-center gap-2">
                                <input type="checkbox" id="showOnlySelectedChannels" class="form-checkbox"/>
                                <label for="showOnlySelectedChannels" class="text-sm font-semibold">Show only
                                    selected</label>
                            </div>
                            <select id="channelSelect" name="channel_id[]" multiple="multiple" size="10"
                                    class="focus:outline-none form-multiselect py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                @foreach($allChannels as $channel)
                                    <option
                                        value="{{ $channel->channel_id }}" {{ in_array($channel->channel_id, request('channel_id', [])) ? 'selected' : '' }}>
                                        Channel
                                        @if(!is_null($channel->name))
                                            {{ $channel->name }}
                                        @else
                                            {{ $channel->channel_id }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Кнопка для применения фильтров -->
                    <div class="mt-4">
                        <button type="submit"
                                class="btn text-black dark:text-white border hover:bg-lightgreen-100 dark:hover:bg-lightgreen-100 border-lightgreen-100 bg-transparent hover:text-black">
                            Apply Filters
                        </button>
                        <a href="{{ route('operators.statistic') }}"
                           class="ml-3 btn text-black dark:text-white border hover:bg-lightpurple-200 dark:hover:bg-lightpurple-200 border-lightpurple-200 bg-transparent hover:text-black">
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Таблица статистики -->
            <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-1 flex justify-between items-center">
                    <p class="text-sm font-semibold">Statistic was updated at: {{ $latestUpdatedAt }}</p>
                    <div class="btn-group ml-2">
                        <a href="{{ route('operators.statistic.export', array_merge(request()->all(), ['format' => 'csv'])) }}" class="btn btn-primary">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
{{--                        <a href="{{ route('operators.statistic.export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-success">--}}
{{--                            <i class="fa fa-file-excel"></i> Export Excel--}}
{{--                        </a>--}}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="min-w-full whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                        <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Operator ID</th>
                            <th class="px-4 py-2 text-left">New Client Chats</th>
                            <th class="px-4 py-2 text-left">Total Clients</th>
                            <th class="px-4 py-2 text-left">Total Reg</th>
                            <th class="px-4 py-2 text-left">Total Dep</th>
                            <th class="px-4 py-2 text-left">Inbox Messages</th>
                            <th class="px-4 py-2 text-left">Outbox Messages</th>
                            <th class="px-4 py-2 text-left">Start Time</th>
                            <th class="px-4 py-2 text-left">End Time</th>
                            <th class="px-4 py-2 text-left">Total Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($statistics as $stat)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    @if(!is_null($stat['name']))
                                        {{ $stat['name'] }}
                                    @else
                                        {{ $stat['operator_id'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['new_client_chats'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['total_clients'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['reg_count'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['dep_count'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['inbox_messages'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['outbox_messages'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['start_time'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['end_time'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $stat['total_time'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        @if(!$statistics->isEmpty())
                            {{ $statistics->appends(request()->all())->links('vendor.pagination.optimized') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Инициализация фильтров поиска
            const filters = [
                {input: 'searchOperator', select: 'operatorSelect'},
                {input: 'searchChannel', select: 'channelSelect'},
            ];

            filters.forEach(filter => {
                const searchInput = document.getElementById(filter.input);
                const select = document.getElementById(filter.select);
                if (searchInput && select) {
                    searchInput.addEventListener('input', function () {
                        filterOptions(searchInput, select);
                    });

                    // Инициализация кастомного multiselect и сохранения скролла
                    initializeCustomMultiselect(select);
                    handleScrollPreservation(select, `${filter.select}ScrollTop`);
                }
            });

            // Функциональность "Показать только выбранные"
            const showOnlySelectedConfig = [
                {checkbox: 'showOnlySelectedOperators', select: 'operatorSelect'},
                {checkbox: 'showOnlySelectedChannels', select: 'channelSelect'},
            ];

            showOnlySelectedConfig.forEach(config => {
                const checkbox = document.getElementById(config.checkbox);
                const select = document.getElementById(config.select);

                if (checkbox && select) {
                    checkbox.addEventListener('change', function () {
                        const showOnlySelected = this.checked;
                        const options = select.options;

                        for (let i = 0; i < options.length; i++) {
                            const option = options[i];
                            option.style.display = showOnlySelected && !option.selected ? 'none' : '';
                        }
                    });
                }
            });

            // Инициализация переключателей "Exclude"/"Include"
            const toggleOperators = document.getElementById("filterToggleOperators");
            const labelOperators = document.getElementById("filterModeLabelOperators");
            const toggleChannels = document.getElementById('filterToggleChannels');
            const labelChannels = document.getElementById('filterModeLabelChannels');

            if (toggleOperators && labelOperators) {
                labelOperators.textContent = toggleOperators.checked ? "Exclude" : "Include";
                toggleOperators.addEventListener("change", function () {
                    labelOperators.textContent = toggleOperators.checked ? "Exclude" : "Include";
                });
            }

            if (toggleChannels && labelChannels) {
                labelChannels.textContent = toggleChannels.checked ? "Exclude" : "Include";
                toggleChannels.addEventListener("change", function () {
                    labelChannels.textContent = toggleChannels.checked ? "Exclude" : "Include";
                });
            }
        });

        // Фильтрация опций по тексту
        function filterOptions(searchInput, select) {
            const searchValue = searchInput.value.toLowerCase();
            const options = select.options;
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchValue) ? '' : 'none';
            }
        }

        // Кастомный multiselect
        function initializeCustomMultiselect(select) {
            if (!select) return;

            select.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const option = e.target;
                if (option.tagName === 'OPTION') {
                    option.selected = !option.selected;
                }
                const scrollTop = select.scrollTop;
                setTimeout(() => {
                    select.scrollTop = scrollTop;
                }, 0);
            });
        }

        // Сохранение и восстановление позиции скролла
        function handleScrollPreservation(select, storageKey) {
            if (!select) return;

            // Сохраняем позицию прокрутки при прокрутке
            select.addEventListener('scroll', () => {
                localStorage.setItem(storageKey, select.scrollTop.toString());
            });

            // Восстанавливаем позицию прокрутки при загрузке
            const savedScrollTop = localStorage.getItem(storageKey);
            if (savedScrollTop !== null) {
                select.scrollTop = parseInt(savedScrollTop, 10);
            }
        }
    </script>
</x-layout.default>
