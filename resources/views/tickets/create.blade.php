@section('title', 'Create Ticket - Gteam')

<x-layout.default>
    <div class="min-h-[calc(100vh-145px)] py-4 px-4 sm:px-12 flex justify-center items-center">
        <div
            class="max-w-[680px] flex-none w-full bg-white dark:bg-white/5 p-4 sm:p-10 lg:px-[146px] lg:py-[107px] rounded-2xl loginform">
            <div class="p-4 sm:p-7">
                <h1 class="text-2xl font-semibold mb-2 text-center">Create Ticket</h1>
                <div class="flex items-center mb-7">
                    <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
                    <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
                </div>

                <form id="ticketForm" method="POST" action="/tickets/create" enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    <!-- Category Select -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium mb-2">Category</label>
                        <select id="category_id" name="category_id" required
                                data-placeholder="Select Category"
                                class="select">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Topic Select -->
                    <div id="topicSelectContainer" class="hidden">
                        <label for="topic_id" class="block text-sm font-medium mb-2">Topic</label>
                        <select id="topic_id" name="topic_id" required
                                data-placeholder="Select Topic"
                                class="select">
                        </select>
                    </div>

                    <!-- Priority Select -->
                    <div id="prioritySelectContainer" class="hidden">
                        <label for="priority" class="block text-sm font-medium mb-2">Priority</label>
                        <select id="priority" name="priority" required
                                data-placeholder="Select Priority"
                                class="select">
                            <option value="low">Low</option>
                            <option value="middle">Middle</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <!-- Dynamic Form Fields Container -->
                    <div id="dynamicFieldsContainer" class="hidden space-y-6">
                        <!-- Fields will be added dynamically -->
                    </div>

                    <!-- Submit Button -->
                    <div class="hidden" id="submitContainer">
                        <button type="submit"
                                class="py-2 px-4 bg-black dark:bg-lightpurple-200 w-full rounded-lg text-white dark:text-black text-lg font-semibold border border-black dark:border-lightpurple-200 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">Create Ticket</button>
                    </div>

                    @if (session('error'))
                        <x-elements.alerts.simple-danger>
                            {{ session('error') }}
                        </x-elements.alerts.simple-danger>
                    @endif
                </form>
            </div>

            <x-slot name="script">
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const form = document.getElementById('ticketForm');
                        const categorySelect = document.getElementById('category_id');
                        const topicContainer = document.getElementById('topicSelectContainer');
                        const topicSelect = document.getElementById('topic_id');
                        const priorityContainer = document.getElementById('prioritySelectContainer');
                        const dynamicFieldsContainer = document.getElementById('dynamicFieldsContainer');
                        const submitContainer = document.getElementById('submitContainer');

                        // Store all topics data
                        const topics = @json($topics);

                        // Обработчик выбора категории
                        categorySelect.addEventListener('change', function () {
                            const categoryId = this.value;

                            // Очистка списка топиков
                            topicSelect.innerHTML = ``;

                            if (categoryId) {
                                // Фильтрация топиков по категории
                                const categoryTopics = topics.filter(topic => topic.category_id == categoryId);

                                // Добавление топиков в select
                                categoryTopics.forEach(topic => {
                                    topicSelect.innerHTML += `<option value="${topic.id}">${topic.name}</option>`;
                                });

                                topicContainer.classList.remove('hidden');

                                // Скрытие других секций
                                resetFormAfterTopic();
                                defaultSelect();
                            } else {
                                resetForm();
                            }
                        });

                        // Обработчик выбора топика
                        topicSelect.addEventListener('change', function () {
                            const topicId = this.value;

                            // Очистка динамических полей
                            dynamicFieldsContainer.innerHTML = '';

                            if (topicId) {
                                const selectedTopic = topics.find(t => t.id == topicId);

                                // Показ контейнера приоритета
                                priorityContainer.classList.remove('hidden');

                                // Обновление динамических полей
                                if (selectedTopic.form_fields && selectedTopic.form_fields.length > 0) {
                                    dynamicFieldsContainer.innerHTML = selectedTopic.form_fields.map(field => {
                                        let fieldHtml = `
                                    <div class="mb-5">
                                        <label class="block text-sm font-medium mb-2">${field.label}</label>
                                `;

                                        switch (field.type) {
                                            case 'text':
                                                fieldHtml += `
                                            <input type="text" name="field_${field.id}"
                                                class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                        `;
                                                break;
                                            case 'textarea':
                                                fieldHtml += `
                                            <textarea name="field_${field.id}" rows="3"
                                                class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}></textarea>
                                        `;
                                                break;
                                            case 'number':
                                                fieldHtml += `
                                            <input type="number" name="field_${field.id}"
                                                class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                        `;
                                                break;
                                            case 'date':
                                                fieldHtml += `
                                            <input type="date" name="field_${field.id}"
                                                class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                        `;
                                                break;
                                            case 'select':
                                                fieldHtml += `
                                            <select name="field_${field.id}"
                                                class="select"
                                                data-placeholder="Select option"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                                ${field.options ? field.options.map(option =>
                                                    `<option value="${option}">${option}</option>`
                                                ).join('') : ''}
                                            </select>
                                        `;
                                                break;
                                            case 'multiselect':
                                                fieldHtml += `
                                            <select name="field_${field.id}"
                                                class="select"
                                                multiple
                                                data-placeholder="Select option"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                                ${field.options ? field.options.map(option =>
                                                    `<option value="${option}">${option}</option>`
                                                ).join('') : ''}
                                            </select>
                                        `;
                                                break;
                                            case 'country':
                                                fieldHtml += `
                                            <select name="field_${field.id}"
                                                class="select"
                                                multiple
                                                data-placeholder="Select option"
                                                ${field.is_required ? 'required' : ''}
                                                ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                                @foreach($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                                </select>
`;
                                                break;
                                            case 'checkbox':
                                                fieldHtml += `
                                            <div class="flex items-center">
                                                <input type="checkbox" name="field_${field.id}"
                                                    class="form-checkbox rounded border-black/10 dark:border-white/10 text-primary focus:ring-0"
                                                    ${field.is_required ? 'required' : ''}
                                                    ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>
                                            </div>
                                        `;
                                                break;
                                            case 'radio':
                                                if (field.options) {
                                                    fieldHtml += `<div class="space-y-2" ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''}>`;
                                                    field.options.forEach(option => {
                                                        fieldHtml += `
                                                    <div class="flex items-center">
                                                        <input type="radio" name="field_${field.id}" value="${option}"
                                                            class="form-radio border-black/10 dark:border-white/10 text-primary focus:ring-0"
                                                            ${field.is_required ? 'required' : ''}>
                                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">${option}</span>
                                                    </div>
                                                `;
                                                    });
                                                    fieldHtml += `</div>`;
                                                }
                                                break;
                                            case 'file':
                                                fieldHtml += `
                                            <div class="space-y-2">
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="field_${field.id}">Upload file</label>
                                            <input accept="${field.options ? '.' + field.options.join(',.') : ''}" ${field.is_required ? 'required' : ''} ${field.validation_rules ? `data-validation-rules='${JSON.stringify(field.validation_rules)}'` : ''} name="field_${field.id}${field.multiple ? '[]' : ''}" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="field_${field.id}" type="file">
                                            </div>
                                        `;
                                                break;
                                        }

                                        fieldHtml += `</div>`;
                                        return fieldHtml;
                                    }).join('');

                                    dynamicFieldsContainer.classList.remove('hidden');
                                    defaultSelect();
                                } else {
                                    dynamicFieldsContainer.classList.add('hidden');
                                }

                                submitContainer.classList.remove('hidden');
                            } else {
                                resetFormAfterTopic();
                            }
                        });

                        function resetForm() {
                            topicContainer.classList.add('hidden');
                            resetFormAfterTopic();
                        }

                        function resetFormAfterTopic() {
                            priorityContainer.classList.add('hidden');
                            dynamicFieldsContainer.classList.add('hidden');
                            submitContainer.classList.add('hidden');
                        }
                    });
                </script>

            </x-slot>
        </div>
    </div>
    <!-- FilePond styles/scripts -->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Для динамических полей пересоздаём FilePond после генерации
            const observer = new MutationObserver(() => {
                FilePond.parse(document.body);
            });
            const dynamicFieldsContainer = document.getElementById('dynamicFieldsContainer');
            if (dynamicFieldsContainer) {
                observer.observe(dynamicFieldsContainer, { childList: true, subtree: true });
            }
            // Инициализируем FilePond для уже существующих полей
            FilePond.parse(document.body);
        });
    </script>
</x-layout.default>
