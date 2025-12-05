@section('title', 'New Creative - Gteam')
<x-layout.default>
    <div class="min-h-[calc(100vh-145px)] py-4 px-4 sm:px-12 flex justify-center items-center">
        <div
            class="max-w-[680px] flex-none w-full bg-white dark:bg-white/5 p-4 sm:p-10 lg:px-[146px] lg:py-[107px] rounded-2xl loginform">
            <h1 class="text-2xl font-semibold mb-2 text-center">New Creative</h1>
            <div class="flex items-center mb-7">
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
                <div class="w-full h-[2px] bg-black/10 dark:bg-white/10"></div>
            </div>
            <form id="creative_form" class="mb-4" method="post" action="{{ route('creativeSubmit') }}" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                    <div class="mb-4 rounded bg-lightyellow p-3 text-black">{{ $errors->first() }}</div>
                @endif
                <div class="mb-4 rounded bg-lightyellow p-3 text-black hidden" id="moFoErrors"></div>
                <input type="hidden" name="uploaded_filez" id="uploaded_filez">
                <div class="mb-3">
                    <p class="text-sm font-semibold">
                        Select type
                    </p>
                </div>
                <div class="mb-5">
                    <select name="type" onchange="setMoFoFileFormat(this);" id="select_file_format"
                            class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                        <option value="video">Video</option>
                        <option value="image">Image</option>
                    </select>
                </div>

                <div class="mb-3">
                    <p class="text-sm font-semibold">
                        Select File
                    </p>
                </div>
                <div class="mb-5">
                    <div class="flex flex-col flex-grow mb-3">
                        <div x-data="{ files: null }" id="FileUpload"
                             class="block w-full py-2 px-3 relative appearance-none min-h-64 border border-gray-300 border-dashed dark:border-gray-600 dark:hover:border-gray-500 rounded-lg cursor-pointer bg-black/10 dark:bg-black/10 rounded-md hover:shadow-outline-gray">
                            <input type="file" multiple required accept="video/*" name="file" id="file"
                                   class="absolute inset-0 z-50 m-0 p-0 w-full h-full outline-none opacity-0"
                                   x-on:change="files = $event.target.files; uploadMoFoFiles();"
                                   x-on:dragover="$el.classList.add('active')"
                                   x-on:dragleave="$el.classList.remove('active')"
                                   x-on:drop="$el.classList.remove('active')"
                            >
                            <template x-if="files !== null">
                                <div class="flex flex-col space-y-1" >
                                    <template x-for="(_,index) in Array.from({ length: files.length })" >
                                        <div class="w-full flex flex-row items-center align-middle space-x-1 overflow-hidden justify-between" x-bind:data-fileindex="index">
                                            <div class="w-full flex flex-col space-y-1 max-w-95">
                                                <div class="flex flex-row w-full items-center align-middle">
                                                    <div x-bind:data-filename="index" class="font-medium text-red-600 w-4/5 dark:text-red-600 overflow-hidden" x-text="getFile('name', files, index);">Uploading</div>
                                                    <div x-bind:data-filesize="index" class="text-xs text-red-600 dark:text-red-600 align-middle" x-text="getFile('size', files, index, filesize);">...</div>
                                                </div>
                                                <div class="meter w-full" x-bind:data-meterdiv="index">
                                                    <span style="width: 0%; text-align: center" x-bind:data-meterdivspan="index">0%</span>
                                                </div>
                                            </div>
                                            <div x-bind:data-filestop="index" class="cursor-pointer text-red-600 z-50 text-lg" x-on:click="removeFileFromFileList(index); if (!document.querySelectorAll('[data-fileindex]').length){files = null;}">&times;</div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="files === null">
                                <div class="flex flex-row space-y-2 items-center justify-center w-full h-64">
                                    <svg class="mt-1 w-8 h-8 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="text-white dark:text-white ml-2">
                                        Upload <span id="file_format">Video</span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                <div class="mb-3">
                    <p class="text-sm font-semibold mb-3">
                        Select Country
                    </p>
                    <select name="country_id"
                            class="select"
                            id="selectfield"
                            data-placeholder="Select Country"
                            required>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5 mt-5">
                    <p class="text-sm font-semibold">Tags</p>
                </div>
                <div
                    class="border border-black/10 dark:border-white/10  p-5 rounded-md mb-5">
                    <div class="mb-4">
                        <input type="text" value="" placeholder="Looking for tag..." class="form-input" data-tag_searcher="filter" />
                    </div>
                    @foreach($tags as $tag)
                        <label
                            class="inline-flex mr-2 items-center cursor-pointer"
                            data-tag_searcher_filter="{{ $tag->name }}">
                            <input value="{{ $tag->id }}"
                                   name="tag_{{ $tag->id }}"
                                   type="checkbox"
                                   class="form-checkbox">
                            <span>
                            <p class="px-1.5 text-black bg-{{ $tag->tailwind_color }} text-xs rounded-[18px] inline-block">
                                {{ $tag->name }}
                            </p>
                        </span>
                        </label>
                    @endforeach
                </div>

                <button type="button"
                        onclick="submitDaForm();"
                        class="py-2 px-4 bg-black dark:bg-lightpurple-200 w-full rounded-lg text-white dark:text-black text-lg font-semibold border border-black dark:border-lightpurple-200 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">
                    Create
                </button>
            </form>
        </div>
    </div>

    <script>
        //Presets
        const uploaded_files = [];
        const uploadingPBIntervals = {};
        const uploadingPBPercents = {};
        const xhrS = {};

        function getFile(what, files, index, fs) {
            if (files) {
                if (what === 'size') {
                    return '('+fs(files[index].size)+')';
                }
                return files[index][what];
            }
        }

        //On from submit setting files
        function submitDaForm() {
            if (uploaded_files.length === 0) {
                setMoFoError('No files uploaded!');
                return;
            }

            const form = document.getElementById('creative_form');
            if (!form) {
                console.error('Form not found');
                return;
            }

            const country = document.getElementById('selectfield');
            if (!country || !country.value) {
                setMoFoError('Please select a country');
                return;
            }

            // Сортируем файлы по индексу перед отправкой
            const sortedFiles = [...uploaded_files].sort((a, b) => a.index - b.index);
            document.getElementById('uploaded_filez').value = JSON.stringify(sortedFiles);
            form.submit();
        }
        //Clear error div n hide
        function clearErrors() {
            const erDiv = document.getElementById('moFoErrors');
            erDiv.classList.add('hidden');
            erDiv.innerHTML='';
        }

        //Setting File format
        function setMoFoFileFormat(el) {
            const val = el.value;
            const fileInput = document.getElementById('file');
            const formatSpan = document.getElementById('file_format');
            const formats = {
                'video': {'title': "Video", 'accept': "video/*"},
                'image': {'title': "Image", 'accept': "image/*"},
            };
            if (formatSpan) formatSpan.innerText = formats[val]['title'];
            if (fileInput) fileInput.setAttribute('accept', formats[val]['accept']);
        }

        //Remove File From FileList
        function removeFileFromFileList(index) {
            const dt = new DataTransfer();
            const input = document.getElementById('file');
            const { files } = input;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (index !== i) {
                    dt.items.add(file);
                }
            }
            document.getElementById('file').files = dt.files;
            let fileDiv = document.querySelector('[data-fileindex="'+index+'"]');
            if (fileDiv) fileDiv.remove();

            // Отмена загрузки, если она в процессе
            if (xhrS[index]) {
                xhrS[index].abort();
                clearInterval(uploadingPBIntervals[index]);
            }

            // Удаление из S3, если файл уже загружен
            let toDel = -1;
            for (let i = 0; i < uploaded_files.length; i++) {
                if (uploaded_files[i]['index'] === index) {
                    toDel = i;
                    // Отправляем запрос на удаление
                    let deleteXhr = new XMLHttpRequest();
                    deleteXhr.open('POST', "{{route('deleteCreativeFile')}}", true);
                    deleteXhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    deleteXhr.setRequestHeader('Content-Type', 'application/json');
                    deleteXhr.send(JSON.stringify({ url: uploaded_files[i]['url'] }));
                    break;
                }
            }
            if (toDel > -1) uploaded_files.splice(toDel, 1);

            // Разблокируем селектор формата, если нет файлов
            if (!uploaded_files.length) {
                document.getElementById('select_file_format').classList.remove('pointer-events-none', 'bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
            }
        }

        //Set green color for uploaded file
        function setTextGreen (i) {
            document.querySelector('[data-filename="'+i+'"]').classList.remove('text-red-600');
            document.querySelector('[data-filename="'+i+'"]').classList.remove('dark:text-red-600');
            document.querySelector('[data-filename="'+i+'"]').classList.add('text-green-500');
            document.querySelector('[data-filename="'+i+'"]').classList.add('dark:text-green-500');
            document.querySelector('[data-filesize="'+i+'"]').classList.remove('text-red-600');
            document.querySelector('[data-filesize="'+i+'"]').classList.remove('dark:text-red-600');
            document.querySelector('[data-filesize="'+i+'"]').classList.add('text-green-500');
            document.querySelector('[data-filesize="'+i+'"]').classList.add('dark:text-green-500');
        }

        //Set green color for uploading file
        function setTextRed(element) {
            if (element && typeof element === 'string') {
                element = document.querySelector(`[data-filename="${element}"]`);
            }
            if (element && element.classList) {
                element.classList.add('text-red-600');
            }
        }

        //Pseudo progress bar scaling
        function setMyProgressBar(i, size) {
            uploadingPBIntervals[i] = setInterval(()=>{
                let progressBar = document.querySelector('[data-meterdivspan="'+i+'"]');
                if (progressBar) {
                    uploadingPBPercents[i] += 1;
                    if (uploadingPBPercents[i] < 99) {
                        progressBar.style.width = uploadingPBPercents[i] + '%';
                        progressBar.innerHTML = uploadingPBPercents[i] + '%';
                    }
                }
            }, Math.round( (size / 500) * 1000 ) );
        }

        //Setting error
        function setMoFoError(err, i) {
            const errorDiv = document.getElementById('moFoErrors');
            if (errorDiv) {
                errorDiv.innerText = err;
                errorDiv.classList.remove('hidden');
            }

            const mainDiv = document.querySelector('[data-main_div]');
            if (mainDiv) {
                mainDiv.scrollTo(0, 0);
            }

            if (typeof i !== 'undefined') {
                setTextRed(i);
            }

            if (i > -1) {
                try {
                    clearInterval(uploadingPBIntervals[i]);
                    const meterDiv = document.querySelector('[data-meterdiv="'+i+'"]');
                    if (meterDiv) {
                        meterDiv.remove();
                    }
                } catch (e) {
                    console.error('Error updating UI:', e);
                }
            }
        }

        //Uploading files
        function uploadMoFoFiles() {
            clearErrors();
            const select = document.getElementById('select_file_format');
            document.getElementById('select_file_format').classList.add('pointer-events-none', 'bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
            const input = document.getElementById('file');
            if (!input || !input.files || input.files.length === 0) {
                return;
            }

            const files = input.files;
            const selectedType = select.value; // получаем выбранный тип (image или video)

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file) {
                    continue;
                }

                // Проверяем соответствие типа файла
                if (selectedType === 'image' && !file.type.startsWith('image/')) {
                    setMoFoError('File "' + file.name + '" is not an image file. Detected type: ' + file.type, i);
                    continue;
                }
                if (selectedType === 'video' && !file.type.startsWith('video/')) {
                    setMoFoError('File "' + file.name + '" is not a video file. Detected type: ' + file.type, i);
                    continue;
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('type', selectedType);
                formData.append('_token', '{{ csrf_token() }}');

                // Отладочная информация
                console.log('Uploading file:', {
                    name: file.name,
                    type: file.type,
                    size: file.size,
                    chosenFormat: selectedType
                });

                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ', pair[1]);
                }

                let size = Math.round(file.size / 1000000);
                uploadingPBPercents[i] = 1;
                setMyProgressBar(i, size);

                xhrS[i] = new XMLHttpRequest();
                xhrS[i].open('POST', "{{ route('uploadCreativeFile') }}", true);
                xhrS[i].setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhrS[i].setRequestHeader('Accept', 'application/json');

                xhrS[i].upload.addEventListener('progress', function (event) {
                    if (event.lengthComputable) {
                        let percent = Math.round((event.loaded / event.total) * 100);
                        console.log('Upload progress:', percent + '%');
                        let progressBar = document.querySelector('[data-meterdivspan="'+i+'"]');
                        if (progressBar) {
                            progressBar.style.width = percent + '%';
                            progressBar.innerHTML = percent + '%';
                        }
                    }
                });

                xhrS[i].addEventListener('load', function (event) {
                    clearInterval(uploadingPBIntervals[i]);

                    try {
                        console.log('Server response:', event.target.responseText);
                        let resp = JSON.parse(event.target.responseText);
                        if (resp.status === 0) {
                            setMoFoError(resp.msg, i);
                        } else {
                            setTextGreen(i);
                            const meterDiv = document.querySelector('[data-meterdiv="'+i+'"]');
                            if (meterDiv) {
                                meterDiv.remove();
                            }
                            // Сохраняем resolution вместо dimensions
                            uploaded_files.push({
                                'index': i,
                                'url': resp.url,
                                'code': resp.code,
                                'resolution': resp.resolution || null
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        setMoFoError("Error processing server response", i);
                    }
                });

                xhrS[i].addEventListener('error', function (event) {
                    console.error('XHR error:', event);
                    setMoFoError("A network error occurred while sending the request.", i);
                });

                xhrS[i].onreadystatechange = function () {
                    if (xhrS[i].readyState === 4) {
                        if (xhrS[i].status !== 200) {
                            try {
                                let response = JSON.parse(xhrS[i].responseText);
                                setMoFoError(response.msg || "An error occurred while uploading the file.", i);
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                                setMoFoError("An HTTP error occurred: " + xhrS[i].status, i);
                            }
                        }
                    }
                };

                xhrS[i].send(formData);
            }
        }
    </script>
</x-layout.default>
