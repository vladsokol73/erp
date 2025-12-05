@section('title', 'Moderation Tickets - Gteam')
<x-layout.default>
    @php
        $tabTickets = [
            'all-tickets' => $allTickets,
            'open-tickets' => $openTickets,
            'approval-tickets' => $approvalTickets,
            'to-do-tickets' => $toDoTickets,
            'closed-tickets' => $closedTickets
        ];
    @endphp
    <div class="p-4 sm:p-7 !pb-0 min-h-[calc(100vh-145px)]">
        <div x-data="{
    activeTab: 'all-tickets',
    selectedTicket: null,
    loadDetails(tab, ticketId) {
    if (!ticketId) return;
    let detailsContainer = document.querySelector(`#ticket-details-${tab}`);
    if (!detailsContainer) {
        console.error('Не найден контейнер для вкладки:', tab);
        return;
    }
    fetch(`/tickets/moderation/${ticketId}?tab=${tab}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка загрузки тикета');
        }
        return response.text();
    })
    .then(html => { detailsContainer.innerHTML = html; })
    .catch(error => console.error('Ошибка:', error));
},
    updateTab(newTab) {
        this.activeTab = newTab;
        if (this.selectedTicket) {
            this.loadDetails(newTab, this.selectedTicket);
        }
    }
}"
             class="tabs flex flex-col md:flex-row items-start gap-4 md:gap-7">

            <!-- Таб для выбора -->
            <div class="tabs-list flex flex-wrap md:flex-col gap-2 text-sm">
                <button @click="activeTab = 'all-tickets'"
                        :class="activeTab === 'all-tickets' ? 'text-black dark:text-white font-semibold' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white hover:font-semibold'"
                        class="px-3 py-2 flex items-center gap-3 font-normal rounded-lg">
                    <span class="hidden xl:block">All</span>
                </button>
                <button @click="activeTab = 'open-tickets'"
                        :class="activeTab === 'open-tickets' ? 'text-black dark:text-white font-semibold' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white hover:font-semibold'"
                        class="px-3 py-2 flex items-center gap-3 font-normal rounded-lg">
                    <span class="hidden xl:block">Open</span>
                </button>
                <button @click="activeTab = 'approval-tickets'"
                        :class="activeTab === 'approval-tickets' ? 'text-black dark:text-white font-semibold' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white hover:font-semibold'"
                        class="px-3 py-2 flex items-center gap-3 font-normal rounded-lg whitespace-nowrap">
                    <span class="hidden xl:block">For approval</span>
                </button>
                <button @click="activeTab = 'to-do-tickets'"
                        :class="activeTab === 'to-do-tickets' ? 'text-black dark:text-white font-semibold' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white hover:font-semibold'"
                        class="px-3 py-2 flex items-center gap-3 font-normal rounded-lg whitespace-nowrap">
                    <span class="hidden xl:block">To Do</span>
                </button>
                <button @click="activeTab = 'closed-tickets'"
                        :class="activeTab === 'closed-tickets' ? 'text-black dark:text-white font-semibold' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white hover:font-semibold'"
                        class="px-3 py-2 flex items-center gap-3 font-normal rounded-lg whitespace-nowrap">
                    <span class="hidden xl:block">Closed</span>
                </button>
            </div>

            <!-- Контент вкладок -->
            <div class="tab-content w-full md:w-auto flex-1">
                @foreach ($tabTickets as $key => $tickets)
                    <div x-show="activeTab === '{{ $key }}'"
                         class="flex flex-row items-start gap-7 relative" x-cloak>
                        @if(!$tickets->isEmpty())

                            <div class="lg:max-w-[250px] xl:max-w-[326px] w-full flex-1 flex flex-col gap-2">
                                <div class="space-y-2 md:h-[calc(100vh-225px)] overflow-y-auto">
                                    <form method="GET" action="{{ route('tickets.show') }}">
                                        <input type="search" name="search" value="{{ request('search') }}"
                                               placeholder="Search..."
                                               class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                               onchange="this.form.submit()">
                                    </form>
                                    @foreach($tickets as $ticket)
                                        <button @click="selectedTicket = {{ $ticket->id }}; loadDetails('{{ $key }}', selectedTicket);"
                                                :class="selectedTicket === {{ $ticket->id }} ? 'text-black bg-white/5 dark:text-white' : 'text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white'"
                                                class="p-2 bg-transparent w-full border-b border-black/5 dark:border-white/5 rounded-lg flex items-start gap-2 hover:border-transparent hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-300">
                                            <x-ticket.show :ticket="$ticket"></x-ticket.show>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Уникальный ID для каждой вкладки -->
                            <div :id="'ticket-details-' + '{{ $key }}'" class="flex-1 w-full">
                                <p class="text-center text-gray-500">Select Ticket</p>
                            </div>

                        @else
                            <div class="text-center mt-5 w-full">
                                <h1 class="text-xl font-bold">Sorry, nothing was found.</h1>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function sendComment(ticketId) {
            // Получаем все текстовые поля с комментарием для данного тикета
            let commentInputs = document.querySelectorAll(`#comment_${ticketId}`);

            let commentText = null;

            // Ищем первое заполненное поле (если их несколько)
            commentInputs.forEach(input => {
                if (!commentText && input.value.trim()) {
                    commentText = input.value.trim();
                }
            });

            if (!commentText) return; // Если поле пустое, ничего не делаем

            let url = "{{ route('tickets.comment', ':id') }}".replace(':id', ticketId);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({comment: commentText})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Очищаем все текстовые поля с комментарием
                        commentInputs.forEach(input => input.value = "");

                        // Создаём новый комментарий
                        let newComment = document.createElement("article");
                        newComment.classList.add("px-10", "py-3", "text-base");
                        newComment.innerHTML = `
                <footer class="flex justify-between items-center mb-2 border-t border-black/10 dark:border-white/10 pt-3">
                    <div class="flex items-center">
                        <div class="h-7 w-7 xl:mr-2 rounded-full flex items-center justify-center font-semibold bg-lightpurple-200 text-base uppercase text-black">
                            ${data.user_initial}
                        </div>
                        <p class="text-sm mr-3">${data.user_name}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Today, ${data.time}</p>
                    </div>
                    <div x-data="{ dropdown: false}" class="dropdown">
                        <button class="inline-flex items-center p-2 text-sm font-medium text-center rounded-lg focus:ring-4 focus:outline-none focus:ring-gray-50 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                            type="button"
                            @click="dropdown = !dropdown" @keydown.escape="dropdown = false">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 16 3">
                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                            </svg>
                        </button>
                        <ul x-show="dropdown" @click.away="dropdown = false" x-transition=""
                            x-transition.duration.300ms="" class="right-0 whitespace-nowrap" style="display: none;">
                            <li><a href="javascript:;">Edit</a></li>
                            <li><a href="javascript:;">Delete</a></li>
                        </ul>
                    </div>
                </footer>
                <p class="text-gray-500 dark:text-gray-400">${data.comment}</p>
            `;

                        // Обновляем все списки комментариев на всех вкладках
                        document.querySelectorAll(`#comments-list_${ticketId}`).forEach(commentList => {
                            commentList.appendChild(newComment.cloneNode(true)); // Вставляем в конец
                        });
                    } else {
                        alert("Ошибка при отправке комментария");
                    }
                })
                .catch(error => console.error("Ошибка:", error));
        }

        function saveComment(commentId, newContent, ticketId) {
            let url = "{{ route('tickets.updateComment', ':id') }}".replace(':id', commentId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({comment: newContent, ticket: ticketId})
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Comment updated");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function deleteComment(commentId, ticketId) {

            let url = "{{ route('tickets.destroyComment', ':id') }}".replace(':id', commentId);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ticket: ticketId})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Удаление комментария из DOM
                        const commentElement = document.getElementById(`comment-${commentId}`);
                        commentElement.remove();
                    }
                })
                .catch(error => console.error('Error:', error));

        }

        function editStatus(ticketId) {
            document.querySelectorAll(`#ticket-status-${ticketId}`).forEach(statusText => {
                statusText.classList.add('hidden');
            });

            document.querySelectorAll(`#edit-cancel-${ticketId}`).forEach(button => {
                button.classList.remove('hidden');
            });

            document.querySelectorAll(`#ticket-status-select-div-${ticketId}`).forEach(statusSelectDiv => {
                statusSelectDiv.classList.remove('hidden');
            });

            document.querySelectorAll(`#ticket-status-select-${ticketId}`).forEach(statusSelect => {
                let selectedOption = statusSelect.options[statusSelect.selectedIndex];
                let isFinal = selectedOption.getAttribute('data-is-final') === 'true';

                document.querySelectorAll(`#final-status-input-${ticketId}`).forEach(finalInputDiv => {
                    document.querySelectorAll(`#final-status-placeholder-${ticketId}`).forEach(finalInputPlaceholder => {
                        if (isFinal) {
                            finalInputDiv.classList.remove('hidden');
                            finalInputPlaceholder.classList.remove('hidden');
                        } else {
                            finalInputDiv.classList.add('hidden');
                            finalInputPlaceholder.classList.add('hidden');
                        }
                    });
                });
            });
        }

        function stopEditStatus(ticketId) {
            document.querySelectorAll(`#ticket-status-${ticketId}`).forEach(statusText => {
                statusText.classList.remove('hidden');
            });

            document.querySelectorAll(`#ticket-status-select-div-${ticketId}`).forEach(statusSelect => {
                statusSelect.classList.add('hidden');
            });

            document.querySelectorAll(`#edit-cancel-${ticketId}`).forEach(button => {
                button.classList.add('hidden');
            });
        }

        function handleStatusChange(ticketId, selectElement, tab) {
            let selectedOption = selectElement.options[selectElement.selectedIndex];
            let isFinal = selectedOption.getAttribute('data-is-final') === 'true';

            document.querySelectorAll(`#final-status-input-${ticketId}`).forEach(finalInputDiv => {
                finalInputDiv.classList.add('hidden');
            });

            document.querySelectorAll(`#final-status-placeholder-${ticketId}`).forEach(finalInputPlaceholder => {
                finalInputPlaceholder.classList.add('hidden');
            });

            if (isFinal) {
                document.querySelectorAll(`#final-status-input-${ticketId}`).forEach(finalInputDiv => {
                    finalInputDiv.classList.remove('hidden');
                });

                document.querySelectorAll(`#final-status-placeholder-${ticketId}`).forEach(finalInputPlaceholder => {
                    finalInputPlaceholder.classList.remove('hidden');
                });
            } else {
                saveFinalResult(ticketId, tab, false);
            }
        }

        function saveFinalResult(ticketId, tab, result = true) {
            let resultValue = null;
            if (result) {
                resultValue = document.getElementById(`final-result-${tab}-${ticketId}`).value;
            }

            let selectValue = document.getElementById(`ticket-status-select-${tab}-${ticketId}`).value;
            let url = "{{ route('tickets.update', ':id') }}".replace(':id', ticketId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({result: resultValue, status: selectValue})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll(`#final-status-input-${ticketId}`).forEach(finalStatusInput => {
                            finalStatusInput.classList.add('hidden');
                        });
                        document.querySelectorAll(`#final-status-placeholder-${ticketId}`).forEach(finalStatusPlaceholder => {
                            finalStatusPlaceholder.classList.add('hidden');
                        });
                        document.querySelectorAll(`#status-div-${ticketId}`).forEach(statusDiv => {
                            statusDiv.value = ``;
                            statusDiv.innerHTML = `
                    <p id="ticket-status-${ticketId}"
                       class="w-fit text-sm px-1.5 rounded-[18px]"
                       style="background-color: ${data.color}">
                        ${data.name}
                    </p>
                `;
                        });

                        document.querySelectorAll(`#result-placeholder-${ticketId}`).forEach(resultPlaceholder => {
                            resultPlaceholder.classList.remove('hidden');
                        });
                        document.querySelectorAll(`#result-${ticketId}`).forEach(resultElement => {
                            resultElement.classList.remove('hidden');
                            resultElement.innerHTML = `${resultValue}`;
                        });

                        stopEditStatus(ticketId);

                        // Вызов уведомления через CustomEvent
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {message: 'Status changed success!', type: 'success'}
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {message: data.message, type: 'error'}
                        }));
                    }
                });
        }
    </script>
</x-layout.default>
