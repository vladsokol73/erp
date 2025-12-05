@props(['statuses' => []])

<x-modals.large>
    <x-slot:title>Create Category</x-slot:title>
    <x-slot:button>
        <x-buttons.create>Create Category</x-buttons.create>
    </x-slot:button>

    <div id="categoryFormContainer">
        <x-form-messages prefix="category" />

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Category name</p>
            <input type="text" name="name" placeholder="Enter name" maxlength="64"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                   required>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Description</p>
            <textarea name="description" placeholder="Enter description" rows="3"
                      maxlength="256"
                      class="form-input resize-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"></textarea>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Sort order</p>
            <input type="number" name="sort_order" value="0" min="0"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
        </div>

        <div class="mb-5">
            <label class="flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-checkbox" checked>
                <span class="ml-2">Is Active</span>
            </label>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-3">Statuses</p>

            <!-- Секция добавления существующего статуса -->
            <div class="mb-4 p-4 border border-black/10 dark:border-white/10 rounded-lg">
                <p class="text-sm mb-3">Add existing status</p>
                <div class="flex gap-2">
                    <select id="existingStatusSelect" class="form-select flex-1 py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                        <option value="">Select status</option>
                        @foreach($statuses as $status)
                            <option value="{{ json_encode(['name' => $status->name, 'color' => $status->color, 'is_default' => $status->is_default, 'is_final' => $status->is_final]) }}">
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="addExistingStatusBtn" class="btn btn-primary">Add</button>
                </div>
            </div>

            <!-- Секция добавления нового статуса -->
            <div class="mb-4 p-4 border border-black/10 dark:border-white/10 rounded-lg">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-sm">Add new status</p>
                    <button type="button" id="addStatusBtn" class="text-sm text-primary hover:text-primary-dark">
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
                <div id="statusesContainer">
                    <!-- Статусы будут добавляться сюда динамически -->
                </div>
            </div>
        </div>
    </div>
</x-modals.large>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statusesContainer = document.getElementById('statusesContainer');
        const addStatusBtn = document.getElementById('addStatusBtn');
        const existingStatusSelect = document.getElementById('existingStatusSelect');
        const addExistingStatusBtn = document.getElementById('addExistingStatusBtn');

        let statuses = [{
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
            document.querySelectorAll('.delete-status').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    statuses.splice(index, 1);
                    renderStatuses();
                });
            });

            // Добавляем обработчики для радио-кнопок default
            document.querySelectorAll('input[name="default_status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedIndex = parseInt(this.value);
                    statuses.forEach((status, index) => {
                        status.is_default = index === selectedIndex;
                    });
                });
            });

            // Добавляем обработчики для чекбоксов is_final
            document.querySelectorAll('input[name^="statuses"][name$="[is_final]"]').forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    statuses[index].is_final = this.checked;
                });
            });

            // Добавляем обработчики для изменения имени и цвета
            document.querySelectorAll('input[name^="statuses"][name$="[name]"]').forEach((input, index) => {
                input.addEventListener('input', function() {
                    statuses[index].name = this.value;
                });
            });

            document.querySelectorAll('input[name^="statuses"][name$="[color]"]').forEach((input, index) => {
                input.addEventListener('input', function() {
                    statuses[index].color = this.value;
                });
            });
        }

        // Добавление нового статуса
        addStatusBtn.addEventListener('click', function() {
            statuses.push({
                name: '',
                color: '#6B7280',
                is_default: false,
                is_final: false
            });
            renderStatuses();
        });

        // Инициализация отображения статусов
        renderStatuses();
    });
</script>
