@section('title', 'Ticket Settings - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="border border-black/10 dark:border-white/10 p-5 rounded-md">
            <div class="mb-4">
                <p class="text-sm font-semibold">Ticket Settings</p>
            </div>
            <div x-data="{
                activeTab: localStorage.getItem('settings_activeTab') || 'tickets',
                activeunderTab: localStorage.getItem('settings_activeunderTab') || 'categories',
                setActiveTab(tab) {
                    this.activeTab = tab;
                    localStorage.setItem('settings_activeTab', tab);
                },
                setActiveunderTab(tab) {
                    this.activeunderTab = tab;
                    localStorage.setItem('settings_activeunderTab', tab);
                }
            }">
                <div>
                    <ul class="flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                        <li class="mr-2">
                            <a href="javascript:;" @click="setActiveunderTab('categories')"
                               :class="activeunderTab === 'categories' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                               class="inline-block p-4 text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white">
                                Categories
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="javascript:;" @click="setActiveunderTab('topics')"
                               :class="activeunderTab === 'topics' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                               class="inline-block p-4 text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white">
                                Topics
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="javascript:;" @click="setActiveunderTab('statuses')"
                               :class="activeunderTab === 'statuses' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                               class="inline-block p-4 text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white">
                                Statuses
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content mt-3 text-[13px]">
                    <div x-show="activeunderTab === 'categories'" x-cloak>
                        <div x-data="basic"
                             class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                            <div class="panel-body overflow-x-auto">
                                @if (session('success'))
                                    <div class="alert alert-success mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger mb-4">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="dataTable-top flex">
                                    <form
                                        id="categoryForm"
                                        action="{{ route('tickets.categories.store') }}"
                                        method="POST"
                                    >
                                        @csrf
                                        <x-ticket.category-form :statuses="$statuses"/>
                                    </form>
                                </div>

                                <table id="categories"
                                       class="whitespace-nowrap w-full table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                                    <thead>
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Name</th>
                                        <th class="py-2">Description</th>
                                        <th class="py-2">Statuses</th>
                                        <th class="py-2">Is Active</th>
                                        <th class="py-2">Sort order</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td class="whitespace-nowrap py-2">{{ $category->id }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="max-w-[150px] truncate" title="{{ $category->name }}">
                                                    {{ $category->name }}
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <div class="max-w-[200px] truncate" title="{{ $category->description }}">
                                                    {{ $category->description }}
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($category->statuses as $status)
                                                        <span class="px-2 py-1 rounded text-xs"
                                                              style="background-color: {{ $status->color }}">
                                                            {{ $status->name }}
                                                            @if($status->is_default)
                                                                <span class="ml-1" title="Default">*</span>
                                                            @endif
                                                            @if($status->is_final)
                                                                <span class="ml-1" title="Final">⚑</span>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap py-2">{{ $category->is_active }}</td>
                                            <td class="whitespace-nowrap py-2">{{ $category->sort_order }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="flex items-center gap-2">
                                                    <form method="POST"
                                                          action="{{ route('tickets.categories.update', $category->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-ticket.category-edit-form :category="$category" :statuses="$statuses"/>
                                                    </form>

                                                    <form method="post"
                                                          action="{{ route('tickets.categories.destroy', $category->id) }}"
                                                          x-data="{ formError: '' }"
                                                          @submit.prevent="
                                                            formError = '';
                                                            fetch($el.action, {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Accept': 'application/json'
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    window.location.reload();
                                                                } else {
                                                                    formError = data.message || 'Error deleting category';
                                                                }
                                                            })
                                                            .catch(error => {
                                                                formError = 'Error: ' + error.message;
                                                            })">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-modals.delete-without-button>
                                                            <x-slot name="button">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-primary"
                                                                        @click="toggle">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                         height="24" viewBox="0 0 24 24" fill="none"
                                                                         stroke="currentColor" stroke-width="1.5"
                                                                         stroke-linecap="round" stroke-linejoin="round"
                                                                         class="h-4 w-4">
                                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                                        <path
                                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                    </svg>
                                                                </button>
                                                            </x-slot>

                                                            <!-- Блок для отображения ошибок -->
                                                            <div x-show="formError" x-cloak
                                                                 class="mb-4 p-4 bg-red-100 text-red-900 rounded-lg">
                                                                <p x-text="formError" class="text-sm"></p>
                                                            </div>

                                                            <p class="mb-4">Are you sure you want to delete category
                                                                "{{ $category->name }}"?</p>
                                                            <p class="mb-4 text-red-600">This action cannot be
                                                                undone.</p>
                                                        </x-modals.delete-without-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeunderTab === 'topics'" x-cloak>
                        <div x-data="basic"
                             class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                            <div class="panel-body overflow-x-auto">
                                @if (session('success'))
                                    <div class="alert alert-success mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger mb-4">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="dataTable-top flex items-start">
                                    <form
                                        id="topicForm"
                                        action="{{ route('tickets.topics.store') }}"
                                        method="POST"
                                    >
                                        @csrf
                                        <x-ticket.topic-form :categories="$categories" :users="$users" :roles="$roles"
                                                      :permissions="$permissions" :validation-rules="$validationRules"/>
                                    </form>
                                    <div class="ml-auto mt-0">
                                        <form method="GET" action="{{ route('tickets.settings') }}" class="flex items-center">
                                            <div class="w-64 md:w-80">
                                                <select name="category"
                                                        class="select w-full"
                                                        data-placeholder="Select Category"
                                                        onchange="this.form.submit()">
                                                    <option value="" {{ request('category') == "" ? 'selected' : '' }}>All Categories</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <label class="flex items-center cursor-pointer ml-3">
                                                <input type="checkbox" class="form-checkbox" name="is_active" value="1"
                                                       onchange="this.form.submit()"
                                                    {{ request('is_active') == '1' ? 'checked' : '' }}>
                                                <span>Is Active</span>
                                            </label>
                                        </form>
                                    </div>
                                </div>

                                <table id="topics"
                                       class="whitespace-nowrap w-full table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                                    <thead>
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Category</th>
                                        <th class="py-2">Name</th>
                                        <th class="py-2">Description</th>
                                        <th class="py-2">Approval</th>
                                        <th class="py-2">Responsible Users</th>
                                        <th class="py-2">Form Fields</th>
                                        <th class="py-2">Is Active</th>
                                        <th class="py-2">Sort order</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($topics as $topic)
                                        <tr>
                                            <td class="whitespace-nowrap py-2">{{ $topic->id }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="max-w-[150px] truncate" title="{{ $topic->category->name }}">
                                                    {{ $topic->category->name }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="max-w-[150px] truncate" title="{{ $topic->name }}">
                                                    {{ $topic->name }}
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <div class="max-w-[200px] truncate" title="{{ $topic->description }}">
                                                    {{ $topic->description }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap py-2">
                                                @if($topic->approval)
                                                    <span class="px-2 py-1 rounded text-xs bg-primary/10 text-primary">
                                                        @if($topic->approval->responsible_type === 'user')
                                                            {{ $topic->approval->responsibleUser?->name ?? 'N/A' }}
                                                        @else
                                                            {{ $topic->approval->responsibleUser?->title ?? 'N/A' }}
                                                        @endif
                                                        ({{ $topic->approval->responsible_type }})
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($topic->responsibleUsers as $responsible)
                                                        <span
                                                            class="px-2 py-1 rounded text-xs bg-primary/10 text-primary">
                                                            @if($responsible->responsible_type === 'user')
                                                                {{ $responsible->responsibleUser?->name ?? 'N/A' }}
                                                            @else
                                                                {{ $responsible->responsibleUser?->title ?? 'N/A' }}
                                                            @endif
                                                            ({{ $responsible->responsible_type }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                @if($topic->formFields->count() > 0)
                                                    <x-ticket.topic-fields-form :fields="$topic->formFields"
                                                                                :topic-id="$topic->id"
                                                                                :countries="$countries"/>
                                                @else
                                                    <p class="pl-2">No fields</p>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap py-2">{{ $topic->is_active }}</td>
                                            <td class="whitespace-nowrap py-2">{{ $topic->sort_order }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="flex items-center gap-2">
                                                    <form method="POST"
                                                          class="topic-edit-form"
                                                          id="topicEditForm_{{ $topic->id }}"
                                                          data-topic-id="{{ $topic->id }}"
                                                          action="{{ route('tickets.topics.update', $topic->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-ticket.topic-edit-form :topic="$topic" :categories="$categories"
                                                                           :users="$users" :roles="$roles"
                                                                           :permissions="$permissions"
                                                                           :validation-rules="$validationRules"/>
                                                    </form>

                                                    <form method="post"
                                                          action="{{ route('tickets.topics.destroy', $topic->id) }}"
                                                          x-data="{ formError: '' }"
                                                          @submit.prevent="
                                                            formError = '';
                                                            fetch($el.action, {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Accept': 'application/json'
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    window.location.reload();
                                                                } else {
                                                                    formError = data.message || 'Error deleting topic';
                                                                }
                                                            })
                                                            .catch(error => {
                                                                formError = 'Error: ' + error.message;
                                                            })">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-modals.delete-without-button>
                                                            <x-slot name="button">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-primary"
                                                                        @click="toggle">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                         height="24" viewBox="0 0 24 24" fill="none"
                                                                         stroke="currentColor" stroke-width="1.5"
                                                                         stroke-linecap="round" stroke-linejoin="round"
                                                                         class="h-5 w-5">
                                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                    </svg>
                                                                </button>
                                                            </x-slot>
                                                            <x-slot name="title">Delete Topic</x-slot>
                                                            Are you sure you want to delete this topic?
                                                        </x-modals.delete-without-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeunderTab === 'statuses'" x-cloak>
                        <div x-data="basic"
                             class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                            <div class="panel-body overflow-x-auto">
                                @if (session('success'))
                                    <div class="alert alert-success mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger mb-4">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="dataTable-top flex">
                                    <form
                                        id="statusForm"
                                        action="{{ route('tickets.statuses.store') }}"
                                        method="POST"
                                        x-data="{ formError: '' }"
                                        @submit.prevent="
                                            formError = '';
                                            fetch($el.action, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify(Object.fromEntries(new FormData($el)))
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    window.location.reload();
                                                } else {
                                                    formError = data.message || 'Error creating status';
                                                }
                                            })
                                            .catch(error => {
                                                formError = 'Error creating status';
                                            });"
                                    >
                                        @csrf
                                        <x-ticket.status-form/>
                                    </form>
                                    <p class="text-sm">For status "New" sort_order must be 0, for status "To Approve" order must be 1, for the rest any except 0 and 1</p>
                                </div>

                                <table id="statuses"
                                       class="whitespace-nowrap w-full table-fixed table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                                    <thead>
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Name</th>
                                        <th class="py-2">Slug</th>
                                        <th class="py-2">Color</th>
                                        <th class="py-2">Is Default</th>
                                        <th class="py-2">Is Final</th>
                                        <th class="py-2">Sort order</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($statuses as $status)
                                        <tr>
                                            <td class="whitespace-nowrap py-2">{{ $status->id }}</td>
                                            <td class="whitespace-nowrap py-2">{{ $status->name }}</td>
                                            <td class="whitespace-nowrap py-2">{{ $status->slug }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <span
                                                    class="px-2 py-1 rounded text-white"
                                                    style="background-color: {{ $status->color }};">
                                                    {{ $status->name }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap py-2">
                                                @if($status->is_default)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap py-2">
                                                @if($status->is_final)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap py-2">{{ $status->sort_order }}</td>
                                            <td class="whitespace-nowrap py-2">
                                                <div class="flex items-center gap-2">
                                                    <!-- Edit Status Form -->
                                                    <form
                                                        action="{{ route('tickets.statuses.update', $status->id) }}"
                                                        method="POST"
                                                        x-data="{ formError: '', toggle() { this.$refs.modal.classList.toggle('hidden') } }"
                                                        @submit.prevent="
                                                            formError = '';
                                                            fetch($el.action, {
                                                                method: 'PUT',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Accept': 'application/json'
                                                                },
                                                                body: JSON.stringify(Object.fromEntries(new FormData($el)))
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    window.location.reload();
                                                                } else {
                                                                    formError = data.message || 'Error updating status';
                                                                }
                                                            })
                                                            .catch(error => {
                                                                formError = 'Error: ' + error.message;
                                                            })"
                                                    >
                                                        @csrf
                                                        @method('PUT')

                                                        <x-ticket.status-edit-form :status="$status"/>

                                                    </form>

                                                    <!-- Delete Status Form -->
                                                    <form
                                                        action="{{ route('tickets.statuses.destroy', $status->id) }}"
                                                        method="POST"
                                                        x-data="{ formError: '' }"
                                                        @submit.prevent="
                                                            formError = '';
                                                            fetch($el.action, {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Accept': 'application/json'
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    window.location.reload();
                                                                } else {
                                                                    formError = data.message || 'Error deleting status';
                                                                }
                                                            })
                                                            .catch(error => {
                                                                formError = 'Error: ' + error.message;
                                                            })"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-modals.delete-without-button>
                                                            <x-slot name="button">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-primary"
                                                                        @click="toggle">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                         height="24" viewBox="0 0 24 24" fill="none"
                                                                         stroke="currentColor" stroke-width="1.5"
                                                                         stroke-linecap="round" stroke-linejoin="round"
                                                                         class="h-5 w-5">
                                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                    </svg>
                                                                </button>
                                                            </x-slot>
                                                            <x-slot name="title">Delete Status</x-slot>
                                                            Are you sure you want to delete this status?
                                                        </x-modals.delete-without-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="script">
            <!-- Simple-Datatables js -->
            <script src="/assets/js/simple-datatables.js"></script>

            <script>
                // Инициализация таблицы через Alpine
                document.addEventListener("alpine:init", () => {
                    Alpine.data('basic', () => ({
                        datatable: null,
                        init() {
                            this.datatable = new simpleDatatables.DataTable('#categories', {
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

                            this.datatable = new simpleDatatables.DataTable('#topics', {
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

                            this.datatable = new simpleDatatables.DataTable('#statuses', {
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
                        }
                    }));
                });

                // отправка формы
                document.addEventListener('DOMContentLoaded', function () {
                    // Обработка отправки формы категории
                    const categoryForm = document.getElementById('categoryForm');
                    if (categoryForm) {
                        categoryForm.addEventListener('submit', function (e) {
                            e.preventDefault();
                            const formData = new FormData(this);

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin'
                            })
                                .then(response => {
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        return response.json().then(data => ({status: response.status, data}));
                                    }
                                    throw new Error('Invalid response format');
                                })
                                .then(({status, data}) => {
                                    if (data.success) {
                                        showNotification(data.message || 'Category created successfully', 'success');
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1300);
                                    } else if (data.warning) {
                                        window.showFormMessage('warning', data.message || 'Warning', 'category');
                                    } else if (status === 422) {
                                        // Обработка ошибок валидации
                                        if (data.errors) {
                                            const errorMessages = Object.values(data.errors).flat();
                                            window.showFormMessage('error', errorMessages.join('\n'), 'category');
                                        } else {
                                            window.showFormMessage('error', data.message || 'Validation error', 'category');
                                        }
                                    } else if (status === 409) {
                                        // Обработка конфликта (дубликат категории)
                                        window.showFormMessage('error', 'Category with this name already exists', 'category');
                                    } else {
                                        window.showFormMessage('error', data.message || 'Error creating category', 'category');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    window.showFormMessage('error', 'Error: ' + error.message, 'category');
                                });
                        });
                    }

                    // Обработка отправки формы топика
                    const topicForm = document.getElementById('topicForm');
                    if (topicForm) {
                        topicForm.addEventListener('submit', function (e) {
                            e.preventDefault();
                            const formData = new FormData(this);

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                                .then(response => {
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        return response.json().then(data => ({status: response.status, data}));
                                    }
                                    throw new Error('Invalid response format');
                                })
                                .then(({status, data}) => {
                                    if (data.success) {
                                        window.showFormMessage('success', data.message || 'Topic created successfully', 'topic');
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1300);
                                    } else if (data.warning) {
                                        window.showFormMessage('warning', data.message || 'Warning', 'topic');
                                    } else if (status === 422) {
                                        // Обработка ошибок валидации
                                        if (data.errors) {
                                            const errorMessages = Object.values(data.errors).flat();
                                            window.showFormMessage('error', errorMessages.join('\n'), 'topic');
                                        } else {
                                            window.showFormMessage('error', data.message || 'Validation error', 'topic');
                                        }
                                    } else if (status === 409) {
                                        // Обработка конфликта (дубликат топика)
                                        window.showFormMessage('error', 'Topic with this name already exists', 'topic');
                                    } else {
                                        window.showFormMessage('error', data.message || 'Error creating topic', 'topic');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    window.showFormMessage('error', 'Error: ' + error.message, 'topic');
                                });
                        });
                    }

                    // Обработка отправки формы редактирования топика
                    document.querySelectorAll('.topic-edit-form').forEach(form => {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            const topicId = this.getAttribute('data-topic-id');
                            const messagePrefix = `topic_edit_${topicId}`;

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                                .then(response => {
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        return response.json().then(data => ({status: response.status, data}));
                                    }
                                    throw new Error('Invalid response format');
                                })
                                .then(({status, data}) => {
                                    if (data.success) {
                                        window.showFormMessage('success', data.message || 'Topic update successfully', messagePrefix);
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1300);
                                    } else if (data.warning) {
                                        window.showFormMessage('warning', data.message || 'Warning', messagePrefix);
                                    } else if (status === 422) {
                                        // Обработка ошибок валидации
                                        if (data.errors) {
                                            const errorMessages = Object.values(data.errors).flat();
                                            window.showFormMessage('error', errorMessages.join('\n'), messagePrefix);
                                        } else {
                                            window.showFormMessage('error', data.message || 'Validation error', messagePrefix);
                                        }
                                    } else if (status === 409) {
                                        // Обработка конфликта (дубликат топика)
                                        window.showFormMessage('error', 'Topic with this name already exists', messagePrefix);
                                    } else {
                                        window.showFormMessage('error', data.message || 'Error creating topic', messagePrefix);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    window.showFormMessage('error', 'Error: ' + error.message, messagePrefix);
                                });
                        });
                    });
                });
            </script>
        </x-slot>
    </div>
</x-layout.default>

@push('scripts')
    <script>
        function showTopicFields(topicId) {
            // Находим модальное окно для конкретного топика
            const modal = document.querySelector(`[data-topic-id="${topicId}"]`);
            if (modal) {
                // Показываем модальное окно
                modal.classList.remove('hidden');
            }
        }
    </script>
@endpush
