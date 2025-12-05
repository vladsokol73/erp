@section('title', 'Creatives Tags - Gteam')

<x-layout.default>
    <div class="h-[calc(100vh-73px)] overflow-y-auto overflow-x-hidden">
        <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">


            <div
                class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md lg:col-span-2">
                <div class="mb-1">
                    <p class="text-sm font-semibold">Tags</p>
                </div>

                <div class="flex flex-row">
                    <form action="{{ route('createTag') }}" method="POST">
                        @csrf
                        <x-modals.large>
                            <x-slot name="title">
                                New Tag
                            </x-slot>
                            <x-slot name="button">
                                <div class="flex flex-row items-center">
                                    <x-buttons.create>New Tag</x-buttons.create>
                                </div>
                            </x-slot>

                            <div class="text-sm text-black dark:text-white">
                                <input name="name" type="text" placeholder="Tag Name" maxlength="15"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">

                                <!-- Скрытое поле для сохранения выбранного стиля -->
                                <input type="hidden" id="style" name="style" value="" required>

                                @php
                                    $styleMap = [
                                        'red'         => 'red-500',
                                        'crimson'     => 'red-600',
                                        'rose'        => 'rose-500',
                                        'pink'        => 'pink-500',
                                        'magenta'     => 'fuchsia-600',
                                        'fuchsia'     => 'fuchsia-500',
                                        'purple'      => 'purple-500',
                                        'violet'      => 'violet-500',     // изменено с purple-600
                                        'indigo'      => 'indigo-500',
                                        'blue'        => 'blue-500',
                                        'azure'       => 'sky-300',
                                        'sky'         => 'sky-500',
                                        'cyan'        => 'cyan-500',
                                        'teal'        => 'teal-500',
                                        'mint'        => 'emerald-200',
                                        'emerald'     => 'emerald-500',
                                        'green'       => 'green-500',
                                        'lime'        => 'lime-500',
                                        'chartreuse'  => 'lime-300',
                                        'yellow'      => 'yellow-500',
                                        'amber'       => 'amber-500',
                                        'orange'      => 'orange-500',
                                        'tangerine'   => 'orange-400',
                                        'salmon'      => 'rose-300',       // изменено для лучшего соответствия
                                        'lightpink'   => 'pink-200',
                                        'lavender'    => 'violet-200',     // изменено с purple-200
                                        'lightblue'   => 'blue-200',
                                        'lightcyan'   => 'cyan-200',
                                        'lightgreen'  => 'green-200',
                                        'lightyellow' => 'yellow-200',
                                        'peach'       => 'orange-200',
                                        'coral'       => 'rose-400',
                                    ];
                                @endphp

                                    <!-- Кнопки для выбора стиля -->
                                <div class="mt-5 space-x-5 space-y-3">
                                    @foreach($styleMap as $style => $tailwindClass)
                                        <button
                                            type="button"
                                            onclick="selectStyle('{{ $style }}', this)"
                                            class="rounded-full p-2 text-black dark:text-black border bg-{{ $tailwindClass }} dark:bg-{{ $tailwindClass }} border-{{ $tailwindClass }} dark:border-{{ $tailwindClass }} hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                        >
                                            {{ ucfirst($style) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </x-modals.large>
                    </form>

                    <input type="text" value="" placeholder="Looking for tag..."
                           class="ml-3 mt-3 px-2 py-2 h-10 rounded-md form-input border border-black/10 dark:border-white/10 w-4/5"
                           id="tag_search"/>
                </div>

                <div class="table-responsive">
                    <table class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5 text-xs">
                        <thead>
                        <tr>
                            <th class="py-2">Id</th>
                            <th class="py-2">Name</th>
                            <th class="py-2">Style</th>
                            <th class="py-2">Created At</th>
                            <th class="py-2">Edit</th>
                            <th class="py2">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tags as $tag)
                            <tr data-tag="{{ $tag->name }}">
                                <td class="whitespace-nowrap py-2">{{ $tag->id }}</td>
                                <td class="whitespace-nowrap py-2">{{ $tag->name }}</td>
                                <td class="whitespace-nowrap py-2">
                                    <p class="px-1.5 text-black bg-{{ $tag->tailwind_color }} text-xs rounded-[18px] inline-block">{{ $tag->name }}</p>
                                </td>
                                <td class="whitespace-nowrap py-2">{{ $tag->created_at }}</td>
                                <td class="whitespace-nowrap py-2">
                                    <form action="{{ route('updateTag', ['id' => $tag->id]) }}" method="POST">
                                        @csrf
                                        <x-modals.large>
                                            <x-slot name="title">
                                                Edit Tag
                                            </x-slot>
                                            <x-slot name="button">
                                                <x-buttons.edit></x-buttons.edit>
                                            </x-slot>

                                            <div class="text-sm text-black dark:text-white">
                                                <input name="name" type="text" value="{{ $tag->name }}"
                                                       placeholder="Tag Name" maxlength="15"
                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">

                                                <!-- Скрытое поле для сохранения выбранного стиля -->
                                                <input type="hidden" id="style" name="style" required>

                                                <!-- Кнопки для выбора стиля -->
                                                <div class="mt-5 space-x-5 space-y-3 flex flex-wrap">
                                                    @foreach($styleMap as $style => $tailwindClass)
                                                        <button
                                                            type="button"
                                                            onclick="selectStyle('{{ $style }}', this)"
                                                            class="rounded-full p-2 text-black dark:text-black border bg-{{ $tailwindClass }} dark:bg-{{ $tailwindClass }} border-{{ $tailwindClass }} dark:border-{{ $tailwindClass }} hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                        >
                                                            {{ ucfirst($style) }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </x-modals.large>
                                    </form>
                                </td>
                                <td class="py-2 flex flex-wrap text-lightred whitespace-normal">
                                    <form method="post"
                                          action="{{ route("deleteTag", $tag->id) }}">
                                        @csrf
                                        <x-modals.delete>Are you sure you want to delete tag with
                                            name: "{{ $tag->name }}"?
                                        </x-modals.delete>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        {{ $tags->appends(request()->all())->links('vendor.pagination.optimized') }}
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
        function selectStyle(style, element) {
            document.getElementById('style').value = style;

            // Убираем рамку у всех кнопок
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.classList.remove('ring-4', 'ring-blue-500'); // Сбрасываем выделение
            });

            // Добавляем рамку к выбранной кнопке
            element.classList.add('ring-4', 'ring-blue-500');
        }


    </script>
</x-layout.default>
