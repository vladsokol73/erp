@section('title', 'Domains - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="grid grid-cols-1 gap-7">
            <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="flex justify-between items-center mb-5">
                    <div class="items-center gap-4">
                        <p class="text-sm font-semibold">Domains</p>
                    </div>
                </div>

                <div class="dataTable-top flex justify-end mb-5">
                    <!-- Create Domain Button -->
                    <div x-data="modals">
                        <button type="button" @click="toggle"
                                class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                            Create Domain
                        </button>

                        <!-- Create Domain Modal -->
                        <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                             :class="open && '!block'">
                            <div class="flex items-start justify-center min-h-screen px-4"
                                 @click.self="open = false">
                                <div x-show="open" x-transition x-transition.duration.300
                                     class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                    <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                        <h5 class="font-semibold text-lg">Create Domain</h5>
                                        <button type="button"
                                                class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                @click="toggle">
                                            <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                                                <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="relative p-5">
                                        <div id="create-error" class="mb-4 rounded bg-lightyellow p-3 text-black hidden"></div>
                                        <div id="create-success" class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden"></div>

                                        <form id="create_domain">
                                            <div class="mb-5">
                                                <label for="domain" class="text-sm font-semibold mb-2 block">Domain</label>
                                                <input type="text" id="domain" name="domain" required
                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                            </div>
                                            <div class="mb-5">
                                                <label for="redirect_url" class="text-sm font-semibold mb-2 block">Redirect URL</label>
                                                <input type="url" id="redirect_url" name="redirect_url" required
                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                            </div>

                                            <div class="flex justify-end items-center mt-8 gap-4">
                                                <button type="button" class="btn !bg-lightred !text-white" @click="toggle">
                                                    Discard
                                                </button>
                                                <button type="submit" id="create-submit"
                                                        class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                                                    Create
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ config('app.shorter-endpoint') }}/docs"
                       target="_blank"
                       class="btn text-black dark:text-black border bg-blue-100 dark:bg-blue-100 border-blue-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white ml-2">
                        Docs
                    </a>

                    <div class="dataTable-search">
                        <input id="domain-search" class="dataTable-input" placeholder="Search..." type="text">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5 w-full">
                        <thead>
                            <tr>
                                <th class="py-2">ID</th>
                                <th class="py-2">Domain</th>
                                <th class="py-2">Redirect URL</th>
                                <th class="py-2">Created At</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Edit</th>
                                <th class="py-2">Delete</th>
                            </tr>
                        </thead>
                        <tbody id="domains-table-body">
                        </tbody>
                    </table>
                </div>

                <div class="dataTable-bottom grid place-items-end mt-4">
                    <div id="pagination" class="pagination">
                        <!-- Пагинация будет добавлена через JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Функции для работы с API
                async function fetchDomains() {
                    try {
                        const response = await fetch('{{ route('shorterDomainList') }}');

                        if (!response.ok) {
                            throw new Error('Failed to fetch domains');
                        }

                        const domains = await response.json();
                        renderDomains(domains);
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }

                // Добавляем параметры для пагинации
                const ITEMS_PER_PAGE = 10;
                let currentPage = 1;
                let filteredDomains = [];
                let allDomains = []; // Глобальная переменная для хранения всех доменов

                // Функция для фильтрации доменов
                function filterDomains(domains, searchTerm) {
                    if (!searchTerm) return domains;

                    searchTerm = searchTerm.toLowerCase();
                    return domains.filter(domain =>
                        domain.domain.toLowerCase().includes(searchTerm) ||
                        domain.redirect_url.toLowerCase().includes(searchTerm)
                    );
                }

                // Функция для отображения пагинации
                function renderPagination(totalItems) {
                    const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
                    const pagination = document.getElementById('pagination');

                    if (totalPages <= 1) {
                        pagination.innerHTML = '';
                        return;
                    }

                    let html = '<div class="flex items-center gap-2">';

                    // Кнопка Previous
                    html += `<button class="btn ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                    onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                                Previous
                            </button>`;

                    // Номера страниц
                    for (let i = 1; i <= totalPages; i++) {
                        if (
                            i === 1 || // Первая страница
                            i === totalPages || // Последняя страница
                            (i >= currentPage - 1 && i <= currentPage + 1) // Текущая и соседние страницы
                        ) {
                            html += `<button class="btn ${i === currentPage ? 'bg-blue-200' : ''}"
                                            onclick="changePage(${i})">
                                        ${i}
                                    </button>`;
                        } else if (
                            i === currentPage - 2 ||
                            i === currentPage + 2
                        ) {
                            html += '<span class="px-2">...</span>';
                        }
                    }

                    // Кнопка Next
                    html += `<button class="btn ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                                    onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                                Next
                            </button>`;

                    html += '</div>';
                    pagination.innerHTML = html;
                }

                // Функция для смены страницы
                function changePage(page) {
                    currentPage = page;
                    renderDomains(filteredDomains);
                }

                // Обновляем функцию renderDomains
                function renderDomains(domains) {
                    const tbody = document.getElementById('domains-table-body');
                    tbody.innerHTML = '';

                    // Применяем пагинацию
                    const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
                    const endIndex = startIndex + ITEMS_PER_PAGE;
                    const paginatedDomains = domains.slice(startIndex, endIndex);

                    paginatedDomains.forEach(domain => {
                        const tr = document.createElement('tr');
                        tr.id = `domain-row-${domain.id}`;
                        tr.innerHTML = `
                            <td class="whitespace-nowrap py-2">${domain.id}</td>
                            <td class="py-2">${domain.domain}</td>
                            <td class="py-2">${domain.redirect_url}</td>
                            <td class="py-2">${new Date(domain.created_at).toLocaleString()}</td>
                            <td class="py-2">
                                <span class="badge ${domain.is_active ? 'bg-lightgreen-100' : 'bg-lightred'} text-black">
                                    ${domain.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="py-2">
                                <div x-data="modals">
                                    <button type="button" @click="toggle"
                                            class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-2 px-3">
                                        Edit
                                    </button>
                                    <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                         :class="open && '!block'">
                                        <div class="flex items-start justify-center min-h-screen px-4"
                                             @click.self="open = false">
                                            <div x-show="open" x-transition x-transition.duration.300
                                                 class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                                <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                    <h5 class="font-semibold text-lg">Edit Domain</h5>
                                                    <button type="button"
                                                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                            @click="toggle">
                                                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                                                            <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="relative p-5">
                                                    <div id="edit-error-${domain.id}" class="mb-4 rounded bg-lightyellow p-3 text-black hidden"></div>
                                                    <div id="edit-success-${domain.id}" class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden"></div>

                                                    <form id="edit_domain_${domain.id}" onsubmit="return handleEdit(event, ${domain.id})">
                                                        <div class="mb-5">
                                                            <label for="redirect_url_${domain.id}" class="text-sm font-semibold mb-2 block">Redirect URL</label>
                                                            <input type="url" id="redirect_url_${domain.id}" name="redirect_url" value="${domain.redirect_url}" required
                                                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                        </div>

                                                        <div class="flex justify-end items-center mt-8 gap-4">
                                                            <button type="button" class="btn !bg-lightred !text-white" @click="toggle">
                                                                Discard
                                                            </button>
                                                            <button type="submit" id="edit-submit-${domain.id}"
                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2">
                                <div x-data="modals">
                                    <button type="button" @click="toggle"
                                            class="btn text-xs h-auto leading-none !bg-lightred !text-white p-2 px-3">
                                        Delete
                                    </button>
                                    <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden overflow-y-auto"
                                         :class="open && '!block'">
                                        <div class="flex items-start justify-center min-h-screen px-4"
                                             @click.self="open = false">
                                            <div x-show="open" x-transition x-transition.duration.300
                                                 class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                                <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                    <h5 class="font-semibold text-lg">Delete Domain</h5>
                                                    <button type="button"
                                                            class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                            @click="toggle">
                                                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                                                            <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="relative p-5">
                                                    <div id="delete-error-${domain.id}" class="mb-4 rounded bg-lightyellow p-3 text-black hidden"></div>
                                                    <div id="delete-success-${domain.id}" class="mb-4 rounded bg-lightgreen-100 p-3 text-black hidden"></div>

                                                    <p>Are you sure you want to delete this domain?</p>

                                                    <div class="flex justify-end items-center mt-8 gap-4">
                                                        <button type="button" class="btn !bg-lightred !text-white" @click="toggle">
                                                            Cancel
                                                        </button>
                                                        <button type="button" id="delete-submit-${domain.id}"
                                                                onclick="handleDelete(${domain.id}, this)"
                                                                class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Отображаем пагинацию
                    renderPagination(domains.length);
                }

                // Обработчик создания домена
                document.getElementById('create_domain').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const form = this;
                    const errorDiv = document.getElementById('create-error');
                    const successDiv = document.getElementById('create-success');
                    const submitBtn = document.getElementById('create-submit');

                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch('{{ route('createDomain') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                domain: form.elements['domain'].value.trim(),
                                redirect_url: form.elements['redirect_url'].value.trim()
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to create domain');
                        }

                        successDiv.textContent = 'Domain created successfully';
                        successDiv.classList.remove('hidden');
                        form.reset();

                        // Обновляем таблицу
                        fetchDomains();

                        // Закрываем модальное окно через 2 секунды
                        setTimeout(() => {
                            const modal = form.closest('[x-data="modals"]');
                            if (modal && modal.__x) {
                                modal.__x.$data.open = false;
                            }
                        }, 2000);

                    } catch (error) {
                        errorDiv.textContent = error.message;
                        errorDiv.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                    }
                });

                // Обработчик редактирования домена
                async function handleEdit(event, id) {
                    event.preventDefault();

                    const form = document.getElementById(`edit_domain_${id}`);
                    const errorDiv = document.getElementById(`edit-error-${id}`);
                    const successDiv = document.getElementById(`edit-success-${id}`);
                    const submitBtn = document.getElementById(`edit-submit-${id}`);

                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch(`/shorter/domains/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                redirect_url: form.elements['redirect_url'].value.trim()
                            })
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Failed to update domain');
                        }

                        successDiv.textContent = 'Domain updated successfully';
                        successDiv.classList.remove('hidden');

                        // Обновляем таблицу через 2 секунды и закрываем модальное окно
                        setTimeout(() => {
                            fetchDomains();
                            const modal = form.closest('[x-data="modals"]');
                            if (modal && modal.__x) {
                                modal.__x.$data.open = false;
                            }
                        }, 2000);

                    } catch (error) {
                        errorDiv.textContent = error.message;
                        errorDiv.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                    }

                    return false;
                }

                // Обработчик удаления домена
                async function handleDelete(id, button) {
                    const errorDiv = document.getElementById(`delete-error-${id}`);
                    const successDiv = document.getElementById(`delete-success-${id}`);
                    const submitBtn = document.getElementById(`delete-submit-${id}`);
                    const row = document.getElementById(`domain-row-${id}`);

                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch(`/shorter/domains/${id}`, {
                            method: 'DELETE',
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Failed to delete domain');
                        }

                        successDiv.textContent = 'Domain deleted successfully';
                        successDiv.classList.remove('hidden');

                        // Удаляем строку и закрываем модальное окно через 2 секунды
                        setTimeout(() => {
                            row.remove();
                            const modal = button.closest('[x-data="modals"]');
                            if (modal && modal.__x) {
                                modal.__x.$data.open = false;
                            }
                        }, 2000);

                    } catch (error) {
                        errorDiv.textContent = error.message;
                        errorDiv.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                    }
                }

                // Обработчик поиска
                document.getElementById('domain-search').addEventListener('input', function(e) {
                    const searchTerm = e.target.value;
                    currentPage = 1; // Сбрасываем на первую страницу при поиске
                    filteredDomains = filterDomains(allDomains, searchTerm);
                    renderDomains(filteredDomains);
                });

                // Загружаем домены при загрузке страницы
                fetchDomains();

                // Делаем функции доступными глобально
                window.handleEdit = handleEdit;
                window.handleDelete = handleDelete;
                window.changePage = changePage;
            });
        </script>
    </x-slot>
</x-layout.default>
