@props(['categories' => [], 'users' => [], 'roles' => [], 'permissions' => [], 'validationRules' => []])

<x-modals.large>
    <x-slot:title>Create Topic</x-slot:title>
    <x-slot:button>
        <x-buttons.create>Create Topic</x-buttons.create>
    </x-slot:button>

    <div id="topicFormContainer">
        <x-form-messages prefix="topic"/>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Category</p>
            <select name="category_id" required
                    class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Topic name</p>
            <input type="text" name="name" placeholder="Enter name" maxlength="64"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                   required>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Description</p>
            <textarea name="description" placeholder="Enter description" rows="3"
                      class="form-input resize-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"></textarea>
        </div>

        <!-- Approval User Selection -->
        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Approval User</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <select name="approval_type" id="approvalType"
                            class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                        <option value="user">User</option>
                        <option value="role">Role</option>
                        <option value="permission">Permission</option>
                    </select>
                </div>
                <div>
                    <select name="approval_value" id="approvalValue"
                            class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                        <!-- Options will be populated via JavaScript -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Responsible Users -->
        <div class="mb-5">
            <div class="flex justify-between items-center mb-3">
                <p class="text-sm font-semibold">Responsible Users</p>
                <button type="button" id="addResponsibleBtn"
                        class="text-sm text-primary hover:text-primary-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 inline-block">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Responsible
                </button>
            </div>
            <div id="responsibleContainer">
                <!-- Responsible users will be added here -->
            </div>
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

        <!-- Form Fields Constructor -->
        <div class="mb-5">
            <div class="flex justify-between items-center mb-3">
                <p class="text-sm font-semibold">Form Fields</p>
                <button type="button" id="addFormFieldBtn"
                        class="text-sm text-primary hover:text-primary-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 inline-block">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Field
                </button>
            </div>
            <div id="formFieldsContainer">
                <!-- Form fields will be added here -->
            </div>
        </div>
    </div>
</x-modals.large>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Data initialization
        const users = @json($users);
        const roles = @json($roles);
        const permissions = @json($permissions);
        const validationRules = @json($validationRules);

        // Elements
        const approvalType = document.getElementById('approvalType');
        const approvalValue = document.getElementById('approvalValue');
        const responsibleContainer = document.getElementById('responsibleContainer');
        const addResponsibleBtn = document.getElementById('addResponsibleBtn');
        const formFieldsContainer = document.getElementById('formFieldsContainer');
        const addFormFieldBtn = document.getElementById('addFormFieldBtn');

        // Initialize responsible users array with default user
        let responsibleUsers = [{
            type: 'user',
            value: users.length > 0 ? users[0].id : ''
        }];
        let formFields = [{
            name: '',
            label: '',
            type: 'text',
            options: [],
            validation: [],
            sort_order: 0
        }];

        // Update approval value options based on type
        function updateApprovalOptions() {
            const type = approvalType.value;
            let options = [];

            switch (type) {
                case 'user':
                    options = users.map(user => ({value: user.id, text: user.name}));
                    break;
                case 'role':
                    options = roles.map(role => ({value: role.id, text: role.title}));
                    break;
                case 'permission':
                    options = permissions.map(permission => ({value: permission.id, text: permission.title}));
                    break;
            }

            approvalValue.innerHTML = options.map(option =>
                `<option value="${option.value}">${option.text}</option>`
            ).join('');
        }

        // Render responsible users
        function renderResponsibleUsers() {
            responsibleContainer.innerHTML = responsibleUsers.map((responsible, index) => `
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <select name="responsible[${index}][type]" onchange="updateResponsibleOptions(${index})"
                                class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            <option value="user" ${responsible.type === 'user' ? 'selected' : ''}>User</option>
                            <option value="role" ${responsible.type === 'role' ? 'selected' : ''}>Role</option>
                            <option value="permission" ${responsible.type === 'permission' ? 'selected' : ''}>Permission</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <select name="responsible[${index}][value]" onchange="updateResponsibleValue(${index})"
                                class="form-select flex-1 py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            ${getOptionsForType(responsible.type, responsible.value)}
                        </select>
                        ${responsibleUsers.length > 1 ? `
                        <button type="button" onclick="removeResponsible(${index})"
                                class="text-red-500 hover:text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        // Get options based on type
        function getOptionsForType(type, selectedValue = '') {
            let options = [];
            switch (type) {
                case 'user':
                    options = users.map(user => ({value: user.id, text: user.name}));
                    break;
                case 'role':
                    options = roles.map(role => ({value: role.id, text: role.title}));
                    break;
                case 'permission':
                    options = permissions.map(permission => ({value: permission.id, text: permission.title}));
                    break;
            }
            return options.map(option =>
                `<option value="${option.value}" ${String(option.value) === String(selectedValue) ? 'selected' : ''}>${option.text}</option>`
            ).join('');
        }

        // Update responsible value when changed
        window.updateResponsibleValue = function (index) {
            const valueSelect = document.querySelector(`select[name="responsible[${index}][value]"]`);
            responsibleUsers[index].value = valueSelect.value;
        }

        // Update responsible options
        window.updateResponsibleOptions = function (index) {
            const typeSelect = document.querySelector(`select[name="responsible[${index}][type]"]`);
            const valueSelect = document.querySelector(`select[name="responsible[${index}][value]"]`);

            // Обновляем тип
            responsibleUsers[index].type = typeSelect.value;

            // Получаем первое значение для нового типа
            let defaultValue = '';
            switch (typeSelect.value) {
                case 'user':
                    defaultValue = users.length > 0 ? users[0].id : '';
                    break;
                case 'role':
                    defaultValue = roles.length > 0 ? roles[0].id : '';
                    break;
                case 'permission':
                    defaultValue = permissions.length > 0 ? permissions[0].id : '';
                    break;
            }

            // Обновляем список опций и значение
            valueSelect.innerHTML = getOptionsForType(typeSelect.value, defaultValue);
            responsibleUsers[index].value = valueSelect.value;
        }

        // Add responsible user
        addResponsibleBtn.addEventListener('click', () => {
            // Сохраняем текущие значения
            for (let i = 0; i < responsibleUsers.length; i++) {
                const typeSelect = document.querySelector(`select[name="responsible[${i}][type]"]`);
                const valueSelect = document.querySelector(`select[name="responsible[${i}][value]"]`);
                if (typeSelect && valueSelect) {
                    responsibleUsers[i] = {
                        type: typeSelect.value,
                        value: valueSelect.value
                    };
                }
            }

            // Добавляем нового пользователя
            responsibleUsers.push({
                type: 'user',
                value: users.length > 0 ? users[0].id : ''
            });

            renderResponsibleUsers();
        });

        // Remove responsible
        window.removeResponsible = function (index) {
            // Проверяем, что останется хотя бы один responsible user
            if (responsibleUsers.length <= 1) {
                return;
            }

            // Сохраняем текущие значения
            const updatedUsers = [];
            for (let i = 0; i < responsibleUsers.length; i++) {
                if (i !== index) {
                    const typeSelect = document.querySelector(`select[name="responsible[${i}][type]"]`);
                    const valueSelect = document.querySelector(`select[name="responsible[${i}][value]"]`);
                    if (typeSelect && valueSelect) {
                        updatedUsers.push({
                            type: typeSelect.value,
                            value: valueSelect.value
                        });
                    }
                }
            }

            responsibleUsers = updatedUsers;
            renderResponsibleUsers();
        }

        // Функция для рендеринга поля значения правила
        function renderRuleValueField(rule, value = '', fieldIndex, ruleIndex) {
            const ruleConfig = validationRules[rule];
            if (!ruleConfig || !ruleConfig.value_type) return '';

            switch (ruleConfig.value_type) {
                case 'string':
                    return `
                        <input type="text" value="${value}"
                               onchange="updateValidationRuleValue(${fieldIndex}, ${ruleIndex}, '${rule}', this.value)"
                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                    `;
                case 'number':
                    return `
                        <input type="number" value="${value}"
                               onchange="updateValidationRuleValue(${fieldIndex}, ${ruleIndex}, '${rule}', this.value)"
                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                    `;
                case 'date':
                    if (['before', 'after'].includes(rule)) {
                        return `
                            <input type="date" value="${value || ''}"
                                   onchange="updateValidationRuleValue(${fieldIndex}, ${ruleIndex}, '${rule}', this.value)"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                        `;
                    }
                    return `
                        <input type="text" value="${value}"
                               onchange="updateValidationRuleValue(${fieldIndex}, ${ruleIndex}, '${rule}', this.value)"
                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                    `;
                default:
                    return `
                        <input type="text" value="${value}"
                               onchange="updateValidationRuleValue(${fieldIndex}, ${ruleIndex}, '${rule}', this.value)"
                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                    `;
            }
        }

        // Render validation rules
        function renderValidationRules(fieldIndex) {
            const rulesContainer = document.getElementById(`validationRules_${fieldIndex}`);
            if (!rulesContainer) return;

            rulesContainer.innerHTML = (formFields[fieldIndex].validation || []).map((rule, ruleIndex) => `
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-1/3">
                        <select name="fields[${fieldIndex}][validation][${ruleIndex}][rule]" onchange="updateValidationRule(${fieldIndex}, ${ruleIndex}, this.value)"
                                class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            ${Object.entries(validationRules).map(([key, config]) =>
                            `<option value="${key}" ${rule.rule === key ? 'selected' : ''}>${config.label}</option>`
                        ).join('')}
                        </select>
                        <input type="hidden" name="fields[${fieldIndex}][validation][${ruleIndex}][value]" value="${rule.value || ''}">
                    </div>
                    <div class="flex-1">
                        <div id="ruleValue_${fieldIndex}_${ruleIndex}">
                            ${renderRuleValueField(rule.rule, rule.value, fieldIndex, ruleIndex)}
                        </div>
                    </div>
                    <div class="flex-none">
                        <button type="button" onclick="removeValidationRule(${fieldIndex}, ${ruleIndex})"
                                class="text-red-500 hover:text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Render form fields
        function renderFormFields() {
            formFieldsContainer.innerHTML = formFields.map((field, index) => `
                <div class="border border-black/10 dark:border-white/10 rounded-lg p-4 mb-3 relative transition-all duration-200" draggable="true" data-index="${index}">
                    <div class="pl-8">
                        <input type="hidden" name="fields[${index}][sort_order]" value="${field.sort_order}">
                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <input type="text" name="fields[${index}][name]" value="${field.name}" placeholder="Field name"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                       required>
                            </div>
                            <div>
                                <input type="text" name="fields[${index}][label]" value="${field.label}" placeholder="Field label"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                       required>
                            </div>
                            <div>
                                <select name="fields[${index}][type]" onchange="updateFieldType(${index})"
                                        class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                    <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                                    <option value="number" ${field.type === 'number' ? 'selected' : ''}>Number</option>
                                    <option value="date" ${field.type === 'date' ? 'selected' : ''}>Date</option>
                                    <option value="select" ${field.type === 'select' ? 'selected' : ''}>Select</option>
                                    <option value="multiselect" ${field.type === 'multiselect' ? 'selected' : ''}>Multi Select</option>
                                    <option value="country" ${field.type === 'country' ? 'selected' : ''}>Country Select</option>
                                    <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                                    <option value="file" ${field.type === 'file' ? 'selected' : ''}>File Upload</option>
                                </select>
                            </div>
                        </div>

                        ${field.type === 'multiselect' || field.type === 'select' ? `
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-semibold">Options</p>
                                <button type="button" onclick="addOption(${index})"
                                        class="text-sm text-primary hover:text-primary-dark">Add Option</button>
                            </div>
                            <div id="optionsContainer_${index}">
                                ${(field.options || []).map((option, optionIndex) => `
                                    <div class="flex gap-2 mb-2">
                                        <input type="text"
                                               name="fields[${index}][options][]"
                                               value="${option}"
                                               placeholder="Option value"
                                               class="form-input flex-1 py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                        <button type="button"
                                                onclick="removeOption(${index}, ${optionIndex})"
                                                class="text-red-500 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                 stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ` : ''}

                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-semibold">Validation Rules</p>
                                <button type="button" onclick="addValidationRule(${index})"
                                        class="text-sm text-primary hover:text-primary-dark">Add Rule</button>
                            </div>
                            <div id="validationRules_${index}">
                                <!-- Validation rules will be rendered here -->
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="removeFormField(${index})"
                                    class="remove-field-button ${formFields.length === 1 ? 'hidden' : ''} inline-flex items-center gap-1 text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 6L18 18M6 18L18 6L6 18Z" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Remove Field</span>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Render validation rules for each field
            formFields.forEach((field, index) => {
                renderValidationRules(index);
            });
        }

        // Функция для сохранения текущих значений всех полей
        function saveFieldValues() {
            formFields = formFields.map((field, index) => {
                const nameInput = document.querySelector(`input[name="fields[${index}][name]"]`);
                const labelInput = document.querySelector(`input[name="fields[${index}][label]"]`);
                const typeSelect = document.querySelector(`select[name="fields[${index}][type]"]`);

                field.name = nameInput ? nameInput.value : field.name;
                field.label = labelInput ? labelInput.value : field.label;
                field.type = typeSelect ? typeSelect.value : field.type;

                // Сохраняем options для select
                if (field.type === 'select') {
                    const optionsContainer = document.querySelector(`#optionsContainer_${index}`);
                    if (optionsContainer) {
                        field.options = Array.from(optionsContainer.querySelectorAll('input[type="text"]'))
                            .map(input => input.value);
                    }
                }

                // Сохраняем правила валидации
                const validationContainer = document.querySelector(`#validationRules_${index}`);
                if (validationContainer) {
                    field.validation = Array.from(validationContainer.querySelectorAll('.grid'))
                        .map(ruleDiv => {
                            const select = ruleDiv.querySelector('select');
                            const input = ruleDiv.querySelector('input[type="text"], input[type="date"]');
                            return {
                                rule: select ? select.value : '',
                                value: input ? input.value : ''
                            };
                        });
                }

                return field;
            });
        }

        // Add form field
        window.addFormField = function () {
            saveFieldValues();
            formFields.push({
                name: '',
                label: '',
                type: 'text',
                options: [],
                validation: [],
                sort_order: formFields.length
            });
            renderFormFields();
        };

        // Remove form field
        window.removeFormField = function (index) {
            saveFieldValues();
            formFields.splice(index, 1);
            formFields.forEach((field, idx) => field.sort_order = idx);
            renderFormFields();
        };

        // Add option to select field
        window.addOption = function (fieldIndex) {
            saveFieldValues();
            if (!formFields[fieldIndex].options) {
                formFields[fieldIndex].options = [];
            }
            formFields[fieldIndex].options.push('');
            renderFormFields();
        };

        // Remove option from select field
        window.removeOption = function (fieldIndex, optionIndex) {
            saveFieldValues();
            formFields[fieldIndex].options.splice(optionIndex, 1);
            renderFormFields();
        };

        // Update field type
        window.updateFieldType = function (index) {
            saveFieldValues();
            const typeSelect = document.querySelector(`select[name="fields[${index}][type]"]`);
            if (typeSelect) {
                formFields[index].type = typeSelect.value;
                if (formFields[index].type === 'select' && !formFields[index].options) {
                    formFields[index].options = [''];
                }
            }
            renderFormFields();
        };

        window.updateValidationRuleValue = function (fieldIndex, ruleIndex, rule, value) {
            formFields[fieldIndex].validation[ruleIndex] = {
                rule: rule,
                value: value
            };
            renderValidationRules(fieldIndex);
        }

        window[`addValidationRule`] = addValidationRule;
        window[`removeValidationRule`] = removeValidationRule;
        window[`updateValidationRule`] = updateValidationRule;

        // Add validation rule
        function addValidationRule(fieldIndex) {
            if (!formFields[fieldIndex].validation) {
                formFields[fieldIndex].validation = [];
            }

            const firstRule = Object.keys(validationRules)[0];
            formFields[fieldIndex].validation.push({
                rule: firstRule,
                value: ''
            });

            renderValidationRules(fieldIndex);
        }

        // Remove validation rule
        function removeValidationRule(fieldIndex, ruleIndex) {
            formFields[fieldIndex].validation.splice(ruleIndex, 1);
            renderValidationRules(fieldIndex);
        }

        // Update validation rule
        function updateValidationRule(fieldIndex, ruleIndex, rule, value = '') {
            formFields[fieldIndex].validation[ruleIndex] = {rule, value};
            renderValidationRules(fieldIndex);
        }

        let draggedItem = null;

        function handleDragStart(e) {
            draggedItem = this;
            draggedItem.classList.add('opacity-50');
        }

        function handleDragEnd(e) {
            if (draggedItem) {
                draggedItem.classList.remove('opacity-50');
            }
            draggedItem = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
        }

        function handleDrop(e) {
            e.preventDefault();
            if (draggedItem === this) return;

            saveFieldValues();

            const draggedIndex = parseInt(draggedItem.dataset.index);
            const droppedIndex = parseInt(this.dataset.index);

            // Перемещаем элемент в массиве
            const [movedField] = formFields.splice(draggedIndex, 1);
            formFields.splice(droppedIndex, 0, movedField);

            // Обновляем sort_order для всех полей
            formFields.forEach((field, index) => {
                field.sort_order = index;
            });

            renderFormFields();
        }

        // Функция для добавления обработчиков drag and drop
        function initDragAndDrop() {
            const fieldItems = formFieldsContainer.querySelectorAll('[draggable="true"]');
            fieldItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('drop', handleDrop);
            });
        }

        // Добавляем вызов initDragAndDrop после рендеринга полей
        const originalRenderFormFields = renderFormFields;
        renderFormFields = function () {
            originalRenderFormFields();
            initDragAndDrop();
        };

        // Event Listeners
        approvalType.addEventListener('change', updateApprovalOptions);
        addFormFieldBtn.addEventListener('click', addFormField);

        // Initial setup
        updateApprovalOptions();
        renderResponsibleUsers();
        renderFormFields();
    });
</script>
