@props(['fields' => [], 'topicId', 'countries'])

<div>
    <button type="button"
            @click="$dispatch('open-modal', 'view-fields-{{ $topicId }}')"
            class="btn py-1 text-xs font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors duration-200">
        Show
    </button>

    <x-modals.view :id="'view-fields-' . $topicId">
        <x-slot:title>Form Fields</x-slot:title>

        <div id="topicFormContainer">
            @foreach($fields as $field)
                <div class="mb-5">
                    <p class="text-sm font-semibold mb-2">{{ $field->label }}</p>
                    @switch($field->type)
                        @case('text')
                            <input type="text"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            @break

                        @case('textarea')
                            <textarea rows="3"
                                      class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"></textarea>
                            @break

                        @case('number')
                            <input type="number"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            @break

                        @case('date')
                            <input type="date"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none">
                            @break

                        @case('select')
                            <select data-placeholder="Select Value" class="select">
                                @foreach($field->options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('multiselect')
                            <select multiple
                                    data-placeholder="Select Value"
                                    class="select">
                                @foreach($field->options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('country')
                            <select multiple
                                    data-placeholder="Select Value"
                                    class="select">
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('checkbox')
                            <div class="flex items-center">
                                <input type="checkbox" name="field_{{ $field->id }}"
                                       class="form-checkbox rounded border-black/10 dark:border-white/10 text-primary focus:ring-0">
                            </div>
                            @break

                        @case('radio')
                            <div class="space-y-2">
                                @foreach(json_decode($field->options) as $option)
                                    <div class="flex items-center">
                                        <input type="radio" value="{{ $option }}"
                                               class="form-radio border-black/10 dark:border-white/10 text-primary focus:ring-0">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @break

                        @case('file')
                            <div class="space-y-2">
                                <input type="file"
                                       id="field_{{ $field->id }}"
                                       class="hidden"
                                       accept="{{ implode(',', array_map(fn($ext) => '.' . $ext, $field->options ?? [])) }}"
                                       @if($field->is_required) required @endif>
                                <label for="field_{{ $field->id }}"
                                       class="flex items-center justify-center px-4 py-2 border border-black/10 dark:border-white/10 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Choose File</span>
                                </label>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    @if(!empty($field->options))
                                        Allowed types: {{ implode(', ', $field->options) }}
                                    @endif
                                    @if(!empty($field->validation_rules['max']))
                                        • Max size: {{ number_format($field->validation_rules['max'] / 1024, 1) }}MB
                                    @endif
                                </div>
                                <div id="preview_{{ $field->id }}" class="hidden mt-2">
                                    <div
                                        class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-sm file-name"></span>
                                        </div>
                                        <button type="button" onclick="clearFile('field_{{ $field->id }}')"
                                                class="text-red-500 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>
    </x-modals.view>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Обработка изменения файла
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function () {
                const preview = document.getElementById('preview_' + this.id);
                const fileName = preview.querySelector('.file-name');

                if (this.files && this.files[0]) {
                    preview.classList.remove('hidden');
                    fileName.textContent = this.files[0].name;
                } else {
                    preview.classList.add('hidden');
                    fileName.textContent = '';
                }
            });
        });
    });

    // Функция очистки файла
    function clearFile(fieldId) {
        const input = document.getElementById(fieldId);
        const preview = document.getElementById('preview_' + fieldId);

        input.value = '';
        preview.classList.add('hidden');
        preview.querySelector('.file-name').textContent = '';
    }
</script>
