@props(['topic', 'categories' => [], 'users' => [], 'roles' => [], 'permissions' => [], 'validationRules' => []])

<x-modals.large>
    <x-slot:title>Edit Topic</x-slot:title>
    <x-slot:button>
        <button type="button" class="btn btn-sm btn-primary" @click="toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                 stroke-linejoin="round" class="h-5 w-5">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
        </button>
    </x-slot:button>

    <div id="topicEditFormContainer_{{ $topic->id }}" x-data="{ topicData: @js(['topic' => $topic, 'users' => $users, 'roles' => $roles, 'permissions' => $permissions, 'validationRules' => $validationRules]) }" x-init="$nextTick(() => initializeTopicEditForm($el, topicData))">
        <x-form-messages prefix="topic_edit_{{ $topic->id }}" />

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Category</p>
            <select name="category_id" required
                    class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $topic->category->id === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Topic name</p>
            <input type="text" name="name" placeholder="Enter name" maxlength="64" value="{{ $topic->name }}"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                   required>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Description</p>
            <textarea name="description" placeholder="Enter description" rows="3"
                      class="form-input resize-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">{{ $topic->description }}</textarea>
        </div>

        <!-- Approval User Selection -->
        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Approval User</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <select name="approval_type" id="approvalType"
                            class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                        <option value="user" {{ $topic->approval->type === 'user' ? 'selected' : '' }}>User</option>
                        <option value="role" {{ $topic->approval->type === 'role' ? 'selected' : '' }}>Role</option>
                        <option value="permission" {{ $topic->approval->type === 'permission' ? 'selected' : '' }}>Permission</option>
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
                <!-- Responsible users will be populated via JavaScript -->
            </div>
        </div>

        <div class="mb-5">
            <p class="text-sm font-semibold mb-2">Sort order</p>
            <input type="number" name="sort_order" value="{{ $topic->sort_order }}" min="0"
                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
        </div>

        <div class="mb-5">
            <label class="flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ $topic->is_active ? 'checked' : '' }}>
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
                <!-- Form fields will be populated via JavaScript -->
            </div>
        </div>
    </div>
</x-modals.large>

<script>
    function initializeTopicEditForm(container, data) {
        if (!container) return;

        const { topic, users, roles, permissions, validationRules } = data;
        const topicFormFields = topic.form_fields || [];
        const topicResponsibleUsers = topic.responsible_users || [];

        // Elements
        const approvalType = container.querySelector('#approvalType');
        const approvalValue = container.querySelector('#approvalValue');
        const responsibleContainer = container.querySelector('#responsibleContainer');
        const addResponsibleBtn = container.querySelector('#addResponsibleBtn');
        const formFieldsContainer = container.querySelector('#formFieldsContainer');
        const addFormFieldBtn = container.querySelector('#addFormFieldBtn');

        // Initialize responsible users array with topic data
        let responsibleUsers = Array.isArray(topicResponsibleUsers) ? topicResponsibleUsers.map(responsible => ({
            type: responsible.responsible_type,
            value: responsible.value
        })) : [];

        // Initialize form fields with topic data
        let formFields = Array.isArray(topicFormFields) ? topicFormFields.map((field, index) => {
            // Преобразуем строковые правила валидации в объекты
            let validationRules = [];
            if (Array.isArray(field.validation_rules)) {
                validationRules = field.validation_rules.map(rule => {
                    const parts = rule.split(':');
                    return {
                        rule: parts[0],
                        value: parts.length > 1 ? parts[1] : ''
                    };
                });
            }
            
            return {
                id: field.id,
                name: field.name,
                label: field.label,
                type: field.type,
                options: field.options || [],
                validation: validationRules,
                sort_order: field.sort_order ?? index
            };
        }) : [];

        // Если полей нет, создаем одно поле по умолчанию
        if (formFields.length === 0) {
            formFields.push({
                id: null,
                name: '',
                label: '',
                type: 'text',
                options: [],
                validation: [],
                sort_order: 0
            });
        }

        // Update approval value options based on type
        function updateApprovalOptions() {
            if (!approvalType || !approvalValue) return;

            const type = approvalType.value;
            let options = [];
            let selectedValue = topic.approval ? topic.approval.value : '';

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
                `<option value="${option.value}" ${option.value == selectedValue ? 'selected' : ''}>${option.text}</option>`
            ).join('');
        }

        // Get options for responsible type
        function getOptionsForType(type, selectedValue) {
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
                `<option value="${option.value}" ${option.value == selectedValue ? 'selected' : ''}>${option.text}</option>`
            ).join('');
        }

        // Render responsible users
        function renderResponsibleUsers() {
            if (!responsibleContainer) return;

            responsibleContainer.innerHTML = responsibleUsers.map((responsible, index) => `
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <select name="responsible[${index}][type]"
                                class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            <option value="user" ${responsible.type === 'user' ? 'selected' : ''}>User</option>
                            <option value="role" ${responsible.type === 'role' ? 'selected' : ''}>Role</option>
                            <option value="permission" ${responsible.type === 'permission' ? 'selected' : ''}>Permission</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <select name="responsible[${index}][value]"
                                class="form-select flex-1 py-2.5 px-4 text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            ${getOptionsForType(responsible.type, responsible.value)}
                        </select>
                        ${responsibleUsers.length > 1 ? `
                        <button type="button" onclick="removeResponsible_${topic.id}(${index})"
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

            // Add event listeners for type changes
            responsibleContainer.querySelectorAll('select[name^="responsible"][name$="[type]"]').forEach((select, index) => {
                select.addEventListener('change', () => updateResponsibleOptions(index));
            });
        }

        // Render form fields
        function renderFormFields() {
            if (!formFieldsContainer) return;

            formFieldsContainer.innerHTML = formFields.map((field, index) => `
                <div class="border border-black/10 dark:border-white/10 rounded-lg p-4 mb-3 relative transition-all duration-200" draggable="true" data-index="${index}">
                    <div class="pl-8">
                        ${field.id ? `<input type="hidden" name="fields[${index}][id]" value="${field.id}">` : ''}
                        <input type="hidden" name="fields[${index}][sort_order]" value="${field.sort_order}">
                        <input type="hidden" name="fields[${index}][validation]" value="${Array.isArray(field.validation) ? field.validation.map(v => v.rule + (v.value ? ':' + v.value : '')).join('|') : ''}">
                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <input type="text" name="fields[${index}][name]" value="${field.name}" placeholder="Field name"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            </div>
                            <div>
                                <input type="text" name="fields[${index}][label]" value="${field.label}" placeholder="Field label"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            </div>
                            <div>
                                <select name="fields[${index}][type]" onchange="updateFieldType_${topic.id}(${index}, this.value)"
                                        class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                    <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                                    <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                                    <option value="number" ${field.type === 'number' ? 'selected' : ''}>Number</option>
                                    <option value="date" ${field.type === 'date' ? 'selected' : ''}>Date</option>
                                    <option value="select" ${field.type === 'select' ? 'selected' : ''}>Select</option>
                                    <option value="multiselect" ${field.type === 'multiselect' ? 'selected' : ''}>Multiselect</option>
                                    <option value="country" ${field.type === 'country' ? 'selected' : ''}>Country</option>
                                    <option value="checkbox" ${field.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                                    <option value="file" ${field.type === 'file' ? 'selected' : ''}>File Upload</option>
                                </select>
                            </div>
                        </div>

                        ${field.type === 'multiselect' || field.type === 'select' ? `
                        <!-- Select Options -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-semibold">Options</p>
                                <button type="button" onclick="addFieldOption_${topic.id}(${index})"
                                        class="text-sm text-primary hover:text-primary-dark">Add Option</button>
                            </div>
                            <div id="fieldOptions_${index}" class="space-y-2">
                                ${(field.options || []).map((option, optionIndex) => `
                                    <div class="flex gap-2">
                                        <input type="text" name="fields[${index}][options][${optionIndex}]" value="${option}"
                                               placeholder="Option value"
                                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                               onchange="updateFieldOption_${topic.id}(${index}, ${optionIndex}, this.value)">
                                        <button type="button" onclick="removeFieldOption_${topic.id}(${index}, ${optionIndex})"
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

                        <!-- Validation Rules -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-semibold">Validation Rules</p>
                                <button type="button" onclick="addValidationRule_${topic.id}(${index})"
                                        class="text-sm text-primary hover:text-primary-dark">Add Rule</button>
                            </div>
                            <div id="validationRules_${index}">
                                ${(field.validation || []).map((validation, ruleIndex) => `
                                    <div class="grid grid-cols-2 gap-4 mb-2">
                                        <div>
                                            <select onchange="updateValidationRule_${topic.id}(${index}, ${ruleIndex}, this.value)"
                                                    class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                                ${Object.entries(validationRules).map(([rule, config]) =>
                `<option value="${rule}" ${validation.rule === rule ? 'selected' : ''}>${config.label}</option>`
            ).join('')}
                                            </select>
                                        </div>
                                        <div class="flex gap-2">
                                            ${(() => {
                if (!validationRules[validation.rule]?.value_type) {
                    return '<div class="flex-1"></div>';
                }

                if (['before', 'after'].includes(validation.rule)) {
                    return `
                                                        <input type="date" value="${validation.value || ''}"
                                                               onchange="updateValidationRule_${topic.id}(${index}, ${ruleIndex}, '${validation.rule}', this.value)"
                                                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                                    `;
                }

                return `
                                                    <input type="text" value="${validation.value || ''}"
                                                           onchange="updateValidationRule_${topic.id}(${index}, ${ruleIndex}, '${validation.rule}', this.value)"
                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                                `;
            })()}
                                            <button type="button" onclick="removeValidationRule_${topic.id}(${index}, ${ruleIndex})"
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
                                `).join('')}
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button"
                                    onclick="removeFormField_${topic.id}(${index})"
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

        // Render validation rules
        function renderValidationRules(fieldIndex) {
            const rulesContainer = container.querySelector(`#validationRules_${fieldIndex}`);
            if (!rulesContainer) return;

            rulesContainer.innerHTML = (formFields[fieldIndex].validation || []).map((validation, ruleIndex) => `
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-1/3">
                        <select onchange="updateValidationRule_${topic.id}(${fieldIndex}, ${ruleIndex}, this.value)"
                                class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            ${Object.entries(validationRules).map(([rule, config]) =>
                `<option value="${rule}" ${validation.rule === rule ? 'selected' : ''}>${config.label}</option>`
            ).join('')}
                        </select>
                    </div>
                    <div class="flex gap-2">
                        ${(() => {
                if (!validationRules[validation.rule]?.value_type) {
                    return '<div class="flex-1"></div>';
                }

                if (['before', 'after'].includes(validation.rule)) {
                    return `
                                    <input type="date" value="${validation.value || ''}"
                                           onchange="updateValidationRule_${topic.id}(${fieldIndex}, ${ruleIndex}, '${validation.rule}', this.value)"
                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                                `;
                }

                return `
                                <input type="text" value="${validation.value || ''}"
                                       onchange="updateValidationRule_${topic.id}(${fieldIndex}, ${ruleIndex}, '${validation.rule}', this.value)"
                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            `;
            })()}
                        <button type="button" onclick="removeValidationRule_${topic.id}(${fieldIndex}, ${ruleIndex})"
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
            
            // Обновляем скрытое поле для правил валидации
            updateValidationHiddenField(fieldIndex);
        }

        // Функция для обновления скрытого поля validation
        function updateValidationHiddenField(fieldIndex) {
            const validationField = container.querySelector(`input[name="fields[${fieldIndex}][validation]"]`);
            if (validationField && formFields[fieldIndex].validation) {
                validationField.value = formFields[fieldIndex].validation
                    .map(v => v.rule + (v.value ? ':' + v.value : ''))
                    .join('|');
            }
        }

        // Add responsible user
        function addResponsible() {
            responsibleUsers.push({
                type: 'user',
                value: users.length > 0 ? users[0].id : ''
            });
            renderResponsibleUsers();
        }

        // Remove responsible user
        function removeResponsible(index) {
            responsibleUsers.splice(index, 1);
            renderResponsibleUsers();
        }

        // Update responsible options
        function updateResponsibleOptions(index) {
            const typeSelect = container.querySelector(`select[name="responsible[${index}][type]"]`);
            const valueSelect = container.querySelector(`select[name="responsible[${index}][value]"]`);
            const type = typeSelect.value;
            responsibleUsers[index].type = type;
            responsibleUsers[index].value = '';
            valueSelect.innerHTML = getOptionsForType(type, '');
            if (valueSelect.options.length > 0) {
                responsibleUsers[index].value = valueSelect.options[0].value;
            }
        }

        // Add form field
        function addFormField() {
            formFields.push({
                id: null,
                name: '',
                label: '',
                type: 'text',
                options: [],
                validation: [],
                sort_order: formFields.length
            });
            renderFormFields();
        }

        // Remove form field
        function removeFormField(index) {
            // Если это последнее поле, не удаляем его
            if (formFields.length === 1) {
                return;
            }
            formFields.splice(index, 1);
            renderFormFields();
        }

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
            formFields[fieldIndex].validation[ruleIndex] = { rule, value };
            renderValidationRules(fieldIndex);
        }

        // Update field type
        function updateFieldType(index, type) {
            formFields[index].type = type;
            if (type === 'select' && !formFields[index].options) {
                formFields[index].options = [];
            }
            renderFormFields();
        }

        // Add field option
        function addFieldOption(fieldIndex) {
            if (!formFields[fieldIndex].options) {
                formFields[fieldIndex].options = [];
            }
            formFields[fieldIndex].options.push('');
            renderFormFields();
        }

        // Remove field option
        function removeFieldOption(fieldIndex, optionIndex) {
            formFields[fieldIndex].options.splice(optionIndex, 1);
            renderFormFields();
        }

        // Update field option
        function updateFieldOption(fieldIndex, optionIndex, value) {
            formFields[fieldIndex].options[optionIndex] = value;
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
        renderFormFields = function() {
            originalRenderFormFields();
            initDragAndDrop();
        };

        // Make functions available globally for this specific topic
        window[`removeResponsible_${topic.id}`] = removeResponsible;
        window[`removeFormField_${topic.id}`] = removeFormField;
        window[`addValidationRule_${topic.id}`] = addValidationRule;
        window[`removeValidationRule_${topic.id}`] = removeValidationRule;
        window[`updateValidationRule_${topic.id}`] = updateValidationRule;
        window[`updateFieldType_${topic.id}`] = updateFieldType;
        window[`addFieldOption_${topic.id}`] = addFieldOption;
        window[`removeFieldOption_${topic.id}`] = removeFieldOption;
        window[`updateFieldOption_${topic.id}`] = updateFieldOption;

        // Event Listeners
        if (addResponsibleBtn) {
            addResponsibleBtn.addEventListener('click', addResponsible);
        }

        if (addFormFieldBtn) {
            addFormFieldBtn.addEventListener('click', addFormField);
        }

        if (approvalType) {
            approvalType.addEventListener('change', updateApprovalOptions);
        }

        // Initial setup
        updateApprovalOptions();
        renderResponsibleUsers();
        renderFormFields();
    }
</script>
