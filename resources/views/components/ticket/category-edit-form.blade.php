@props(['category', 'statuses' => []])

<x-modals.large>
    <x-slot:title>Edit Category</x-slot:title>
    <x-slot:button>
        <button class="btn btn-sm btn-primary"
                type="button"
                @click="toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
        </button>
    </x-slot:button>

    <form action="{{ route('tickets.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div id="categoryEditForm_{{ $category->id }}">
            <!-- Блок для отображения ошибок -->
            <div id="formErrors_{{ $category->id }}" class="mb-4 hidden">
                <div class="p-4 text-sm rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400">
                    <p class="error-message"></p>
                </div>
            </div>

            <!-- Блок для отображения предупреждений -->
            <div id="formWarnings_{{ $category->id }}" class="mb-4 hidden">
                <div class="p-4 text-sm rounded-lg bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400">
                    <p class="warning-message"></p>
                </div>
            </div>

            <!-- Блок для отображения успешных сообщений -->
            <div id="formSuccess_{{ $category->id }}" class="mb-4 hidden">
                <div class="p-4 text-sm rounded-lg bg-green-50 dark:bg-green-500/10 text-green-500 dark:text-green-400">
                    <p class="success-message"></p>
                </div>
            </div>

            <div class="mb-5">
                <p class="text-sm font-semibold mb-2">Category name</p>
                <input type="text" name="name" placeholder="Enter name" maxlength="64"
                       value="{{ $category->name }}"
                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                       required>
            </div>

            <div class="mb-5">
                <p class="text-sm font-semibold mb-2">Description</p>
                <textarea name="description" placeholder="Enter description" rows="3"
                          maxlength="256"
                          class="form-input resize-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">{{ $category->description }}</textarea>
            </div>

            <div class="mb-5">
                <p class="text-sm font-semibold mb-2">Sort order</p>
                <input type="number" name="sort_order" value="{{ $category->sort_order }}" min="0"
                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
            </div>

            <div class="mb-5">
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ $category->is_active ? 'checked' : '' }}>
                    <span class="ml-2">Is Active</span>
                </label>
            </div>

            <div class="mb-5">
                <p class="text-sm font-semibold mb-3">Statuses</p>

                <!-- Секция добавления существующего статуса -->
                <div class="mb-4 p-4 border border-black/10 dark:border-white/10 rounded-lg">
                    <p class="text-sm mb-3">Add existing status</p>
                    <div class="flex gap-2">
                        <select id="existingStatusSelect_{{ $category->id }}" class="form-select flex-1 py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            <option value="">Select status</option>
                            @foreach($statuses as $status)
                                <option value="{{ json_encode(['name' => $status->name, 'color' => $status->color, 'is_default' => $status->is_default, 'is_final' => $status->is_final]) }}">
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="addExistingStatusBtn_{{ $category->id }}" class="btn btn-primary">Add</button>
                    </div>
                </div>

                <!-- Секция добавления нового статуса -->
                <div class="mb-4 p-4 border border-black/10 dark:border-white/10 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm">Add new status</p>
                        <button type="button" id="addStatusBtn_{{ $category->id }}"
                                class="text-sm text-primary hover:text-primary-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 inline-block">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add New
                        </button>
                    </div>
                </div>

                <!-- Контейнер для всех добавленных статусов -->
                <div class="p-4 border border-black/10 dark:border-white/10 rounded-lg">
                    <p class="text-sm mb-3">Added statuses</p>
                    <div id="statusesContainer_{{ $category->id }}">
                        <!-- Статусы будут добавляться сюда динамически -->
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-modals.large>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('categoryEditForm_{{ $category->id }}');
        const statusesContainer = container.querySelector('#statusesContainer_{{ $category->id }}');
        const addStatusBtn = container.querySelector('#addStatusBtn_{{ $category->id }}');
        const existingStatusSelect = container.querySelector('#existingStatusSelect_{{ $category->id }}');
        const addExistingStatusBtn = container.querySelector('#addExistingStatusBtn_{{ $category->id }}');

        let statuses = @json($category->statuses ?? []);

        // Инициализируем статусы из переданных данных
        let statusesData = {!! json_encode($category->statuses->map(function($status) {
            return [
                'name' => $status->name,
                'color' => $status->color,
                'is_default' => $status->pivot->is_default,
                'is_final' => $status->is_final
            ];
        })) !!} || [{
            name: 'New',
            color: '#3B82F6',
            is_default: true,
            is_final: false
        }];

        // Добавление существующего статуса
        addExistingStatusBtn.addEventListener('click', function() {
            const selectedOption = existingStatusSelect.value;
            if (selectedOption) {
                const statusData = JSON.parse(selectedOption);
                statuses.push(statusData);
                renderStatuses();
            }
        });

        function renderStatuses() {
            statusesContainer.innerHTML = '';
            statuses.forEach((status, index) => {
                const statusHtml = `
                    <div class="flex gap-4 items-start mb-3">
                        <div class="flex-1">
                            <input type="text" name="statuses[${index}][name]"
                                   value="${status.name}"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                   placeholder="Status name" required>
                        </div>
                        <div class="w-24">
                            <input type="color" name="statuses[${index}][color]"
                                   value="${status.color}"
                                   class="form-input w-full h-[42px] p-1 rounded-lg">
                        </div>
                        <div class="flex items-center space-x-3 pt-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="default_status" value="${index}"
                                       ${status.is_default ? 'checked' : ''}
                                       class="form-radio">
                                <span class="ml-2">Default</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="statuses[${index}][is_final]" value="0">
                                <input type="checkbox" name="statuses[${index}][is_final]"
                                       ${status.is_final ? 'checked' : ''}
                                       value="1"
                                       class="form-checkbox">
                                <span class="ml-2">Final</span>
                            </label>
                            ${statuses.length > 1 ? `
                                <button type="button" class="text-danger hover:text-danger-dark delete-status" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="1.5"
                                         stroke-linecap="round"
                                         stroke-linejoin="round" class="h-5 w-5">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                statusesContainer.insertAdjacentHTML('beforeend', statusHtml);
            });

            // Добавляем обработчики для кнопок удаления
            statusesContainer.querySelectorAll('.delete-status').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    statuses.splice(index, 1);
                    renderStatuses();
                });
            });

            // Добавляем обработчики для радио-кнопок default
            statusesContainer.querySelectorAll('input[name="default_status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedIndex = parseInt(this.value);
                    statuses.forEach((status, index) => {
                        status.is_default = index === selectedIndex;
                    });
                });
            });

            // Добавляем обработчики для чекбоксов is_final
            statusesContainer.querySelectorAll('input[name^="statuses"][name$="[is_final]"]').forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    statuses[index].is_final = this.checked;
                });
            });

            // Добавляем обработчики для изменения имени и цвета
            statusesContainer.querySelectorAll('input[name^="statuses"][name$="[name]"]').forEach((input, index) => {
                input.addEventListener('input', function() {
                    statuses[index].name = this.value;
                });
            });

            statusesContainer.querySelectorAll('input[name^="statuses"][name$="[color]"]').forEach((input, index) => {
                input.addEventListener('input', function() {
                    statuses[index].color = this.value;
                });
            });
        }

        // Добавление нового статуса
        if (addStatusBtn) {
            addStatusBtn.addEventListener('click', function() {
                statuses.push({
                    name: '',
                    color: '#6B7280',
                    is_default: false,
                    is_final: false
                });
                renderStatuses();
            });
        }

        // Инициализация отображения статусов
        if (statusesContainer) {
            renderStatuses();
        }

        // Обработка отправки формы
        const form = document.getElementById('categoryEditForm_{{ $category->id }}').closest('form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const successMessage = document.getElementById('formSuccess_{{ $category->id }}');
                        successMessage.classList.remove('hidden');
                        successMessage.querySelector('.success-message').textContent = data.message || 'Category updated successfully';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1300);
                    } else if (response.status === 409) {
                        // Обработка конфликта (дубликат категории)
                        const errorMessage = document.getElementById('formErrors_{{ $category->id }}');
                        errorMessage.classList.remove('hidden');
                        errorMessage.querySelector('.error-message').textContent = data.message || 'Category with this name already exists';
                    } else if (response.status === 422) {
                        // Обработка ошибок валидации
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            const errorMessage = document.getElementById('formErrors_{{ $category->id }}');
                            errorMessage.classList.remove('hidden');
                            errorMessage.querySelector('.error-message').textContent = errorMessages.join('\n');
                        } else {
                            const errorMessage = document.getElementById('formErrors_{{ $category->id }}');
                            errorMessage.classList.remove('hidden');
                            errorMessage.querySelector('.error-message').textContent = data.message || 'Validation error';
                        }
                    } else {
                        const errorMessage = document.getElementById('formErrors_{{ $category->id }}');
                        errorMessage.classList.remove('hidden');
                        errorMessage.querySelector('.error-message').textContent = data.message || 'Error updating category';
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    const errorMessage = document.getElementById('formErrors_{{ $category->id }}');
                    errorMessage.classList.remove('hidden');
                    errorMessage.querySelector('.error-message').textContent = 'Error: ' + error.message;
                }
            });
        }
    });
</script>
