@props(['ticket', 'tab', 'countries'])

<div
        class="flex-1 md:h-[calc(100vh-173px)] overflow-y-auto absolute top-0 left-0 bg-white dark:bg-black md:static h-full"
        x-show="selectedTicket === {{ $ticket->id }}" x-cloak>

    <div
            class="p-2 mb-4 bg-lightwhite dark:bg-white/5 rounded-lg flex flex-wrap items-start justify-between gap-3">
        <div class="flex-1 flex items-start gap-2 p-1">
            <div class="flex-1 text-left ml-2">
                <p>{{ $ticket->topic->category->name  }}
                    <span class="text-black/40 dark:text-white/40">|</span>
                    {{ $ticket->topic->name }}</p>
                <p class="text-xs text-black/40 dark:text-white/40">
                    @php
                        $createdAt = \Carbon\Carbon::parse($ticket->created_at);
                        $now = \Carbon\Carbon::now();
                    @endphp
                    @if ($createdAt->isToday())
                        <!-- Если дата сегодня, показываем только время -->
                        Today, {{ $createdAt->format('H:i') }}
                    @elseif ($createdAt->isCurrentYear())
                        <!-- Если дата в этом году, показываем месяц и день -->
                        {{ $createdAt->format('F d') }}
                    @else
                        <!-- Если дата не в этом году, показываем год, месяц и день -->
                        {{ $createdAt->format('Y F d') }}
                    @endif
                </p>
            </div>
        </div>
        @if(auth()->user()->hasRole('admin'))
            <div class="flex-none flex items-center gap-1">
                <x-modals.large-classic>
                    <x-slot:button>
                        <button @click="toggle" type="button"
                                class="btn text-black dark:text-white border hover:bg-lightblue-200 dark:hover:bg-lightblue-200 border-lightblue-200 bg-transparent hover:text-black">
                            Logs
                        </button>
                    </x-slot:button>
                    <x-slot:title>Ticket {{ $ticket->ticket_number }} logs</x-slot:title>
                    <table class="whitespace-nowrap w-full table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                        <thead>
                        <tr>
                            <th class="py-2">ID</th>
                            <th class="py-2">User</th>
                            <th class="py-2">Action</th>
                            <th class="py-2">Old Value</th>
                            <th class="py-2">New Value</th>
                            <th class="py-2">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ticket->logs as $log)
                            <tr>
                                <td class="whitespace-nowrap py-2">{{ $log->id }}</td>
                                <td class="whitespace-nowrap py-2">{{ $log->user->name }}</td>
                                <td class="whitespace-nowrap py-2">{{ $log->action }}</td>
                                <td class="whitespace-nowrap py-2">{{ $log->old_values }}</td>
                                <td class="whitespace-nowrap py-2">{{ $log->new_values }}</td>
                                <td class="whitespace-nowrap py-2">{{ $log->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </x-modals.large-classic>
            </div>
        @endif
    </div>
    <div
            class="border border-black/10 dark:border-white/10 rounded-lg pt-10 md:h-[calc(100vh-301px)] overflow-auto">
        <div class="px-10 pb-10 border-b border-black/10 dark:border-white/10">
            <h3 class="text-2xl font-semibold text-black dark:text-white mb-7">Ticket
                № {{ $ticket->ticket_number }}</h3>
            <div class="bg-lightwhite dark:bg-white/5 rounded-2xl p-6 relative">
                <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold">Ticket Details</h3>
                    <div class="absolute top-6 right-6 flex">
                        <button type="button"
                                id="edit-cancel-{{ $ticket->id }}"
                                onclick="stopEditStatus({{ $ticket->id }})"
                                class="hidden mr-3 btn text-black dark:text-white border hover:bg-lightred dark:hover:bg-lightred border-lightred bg-transparent hover:text-black"
                        >Cancel
                        </button>
                        <button type="button"
                                class="btn text-black dark:text-white border hover:bg-lightblue-200 dark:hover:bg-lightblue-200 border-lightblue-200 bg-transparent hover:text-black"
                                onclick="editStatus({{ $ticket->id }})">
                            Edit
                        </button>
                    </div>
                </div>
                <div class="max-w-md grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <p class="first-letter:uppercase text-xs text-black/40 dark:text-white/40">Customer</p>
                    <p class="text-sm">{{ $ticket->user->name }}</p>

                    <p class="first-letter:uppercase text-xs text-black/40 dark:text-white/40">Executor</p>
                    <p class="text-sm">
                        @foreach($ticket->topic->responsibleUsers as $responsible)
                            @if($responsible->responsible_type == "user")
                                {{ \App\Models\User\User::findOrFail($responsible->value)->name }}
                            @elseif($responsible->responsible_type == "role")
                                {{ \App\Models\User\Role::findOrFail($responsible->value)->title }}
                            @else
                                {{ \App\Models\User\Permission::findOrFail($responsible->value)->title }}
                            @endif
                        @endforeach
                    </p>


                    <div class="flex items-center gap-1">
                        <p class="text-xs text-black/40 dark:text-white/40">Status</p>
                    </div>
                    <div class="flex items-center gap-2 relative">
                        <div id="status-div-{{ $ticket->id }}">
                            <p id="ticket-status-{{ $ticket->id }}"
                               class="w-fit text-sm px-1.5 rounded-[18px]"
                               style="background-color: {{ trim($ticket->status->color) }}">
                                {{ $ticket->status->name }}
                            </p>
                        </div>

                        <div class="hidden w-full" id="ticket-status-select-div-{{ $ticket->id }}">
                            <select class="form-select py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                    id="ticket-status-select-{{ $tab }}-{{ $ticket->id }}"
                                    data-placeholder="Select Status"
                                    onchange="handleStatusChange({{ $ticket->id }}, this, '{{ $tab }}')">
                                @foreach($ticket->statuses(auth()->user()) as $status)
                                    <option value="{{ $status->id }}"
                                            data-is-final="{{ $status->is_final ? 'true' : 'false' }}"
                                            {{ $ticket->status->id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="hidden flex items-center gap-1" id="final-status-placeholder-{{ $ticket->id }}">
                        <p class="text-xs text-black/40 dark:text-white/40">Result</p>
                    </div>

                    <div id="final-status-input-{{ $ticket->id }}" class="hidden mt-2 flex">
                        <textarea id="final-result-{{ $tab }}-{{ $ticket->id }}"
                                  class="min-w-[150px] form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                  placeholder="Enter result">{{ $ticket->result }}</textarea>
                        <button type="button"
                                class="ml-3 btn text-black dark:text-black border bg-lightblue-200 dark:bg-lightblue-200 border-lightblue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                onclick="saveFinalResult({{ $ticket->id }}, '{{ $tab }}')">
                            Сохранить
                        </button>
                    </div>


                    <p class="text-xs text-black/40 dark:text-white/40">Ticket priority</p>
                    <p class="text-sm first-letter:uppercase">{{ $ticket->priority }}</p>

                    @foreach($ticket->fieldValues as $fieldValue)
                        <p class="first-letter:uppercase text-xs text-black/40 dark:text-white/40">{{ $fieldValue->field->name }}</p>
                        @if($fieldValue->field->type == "file")
                            @php
                                $value = $fieldValue->value;
                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg|tiff?|heic|heif)$/i', $value);
                                $isVideo = preg_match('/\.(mp4|webm|ogg|mkv|avi|mov|flv|wmv)$/i', $value);
                            @endphp

                            @if($isImage)
                                <img src="{{ $value }}" alt="Uploaded Image" class="max-w-full h-auto rounded-lg">
                            @elseif($isVideo)
                                <video controls class="max-w-full rounded-lg">
                                    <source src="{{ $value }}" type="video/mp4">
                                    Ваш браузер не поддерживает тег видео.
                                </video>
                            @elseif($fieldValue->field->type == "country")
                                <p class="text-sm">{{ $countries->where('id', $fieldValue->value)->first()->name }}</p>
                            @else
                                <a href="{{ $value }}" class="text-blue-500 underline" download>
                                    📎 Скачать файл
                                </a>
                            @endif
                        @else
                            <p class="text-sm">{{ $fieldValue->value }}</p>
                        @endif
                    @endforeach

                    @if($ticket->result)
                        <p id="result-placeholder-{{ $ticket->id }}"
                           class="first-letter:uppercase text-xs text-black/40 dark:text-white/40">Result</p>
                        <p id="result-{{ $ticket->id }}" class="text-sm">{{ $ticket->result }}</p>
                    @else
                        <p id="result-placeholder-{{ $ticket->id }}"
                           class="hidden first-letter:uppercase text-xs text-black/40 dark:text-white/40">Result</p>
                        <p id="result-{{ $ticket->id }}" class="hidden text-sm"></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="py-[18px] w-full justify-center">
            <div class="flex justify-between items-center px-4 mb-3">
                <h2 class="text-lg lg:text-2xl font-bold dark:text-white">Comments</h2>
            </div>
            <div id="comments-list_{{ $ticket->id }}">
                @foreach($ticket->comments as $comment)
                    <article class="px-10 py-3 text-base"
                             id="comment-{{ $comment->id }}"
                             x-data='{ "editing": false, "editedComment": {!! json_encode($comment->comment, JSON_UNESCAPED_UNICODE) !!} }'>
                        <footer
                                class="flex justify-between items-center mb-2 @if($ticket->comments[0] != $comment) border-t border-black/10 dark:border-white/10 pt-3 @endif">
                            <div class="flex items-center">
                                <div
                                        class="h-7 w-7 xl:mr-2 rounded-full flex items-center justify-center font-semibold bg-lightpurple-200 text-base uppercase text-black">
                                    {{ $comment->user->initial() }}
                                </div>
                                <p class="text-sm mr-3">{{ $comment->user->getShortName() }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    @php
                                        $createdAt = \Carbon\Carbon::parse($comment->created_at);
                                        $now = \Carbon\Carbon::now();
                                    @endphp
                                    @if ($createdAt->isToday())
                                        Today, {{ $createdAt->format('H:i') }}
                                    @elseif ($createdAt->isCurrentYear())
                                        {{ $createdAt->format('F d') }}
                                    @else
                                        {{ $createdAt->format('Y F d') }}
                                    @endif
                                </p>
                            </div>

                            @if(auth()->user()->id == $comment->user->id)
                                <!-- Dropdown menu -->
                                <div x-data="{ dropdown: false }" class="dropdown">
                                    <button
                                            class="inline-flex items-center p-2 text-sm font-medium text-center rounded-lg focus:ring-4 focus:outline-none focus:ring-gray-50 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                                            type="button"
                                            @click="dropdown = !dropdown"
                                            @keydown.escape="dropdown = false">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                             fill="currentColor" viewBox="0 0 16 3">
                                            <path
                                                    d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                        </svg>
                                    </button>
                                    <ul x-show="dropdown" @click.away="dropdown = false" x-transition.duration.300ms=""
                                        class="right-0 whitespace-nowrap">
                                        <li><a href="javascript:;" @click="editing = true; dropdown = false">Edit</a>
                                        </li>
                                        <li><a href="javascript:;"
                                               @click="deleteComment({{ $comment->id }}, {{ $ticket->id }})">Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </footer>

                        <div x-show="!editing">
                            <p class="text-gray-500 dark:text-gray-400 break-all" x-text="editedComment"></p>
                        </div>

                        <div x-show="editing" class="mt-2 flex items-center">
                            <input type="text"
                                   class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none"
                                   x-model="editedComment"
                                   required>
                            <button type="button" @click="editing = false;"
                                    class="ml-3 btn text-black dark:text-white border hover:bg-lightred dark:hover:bg-lightred border-lightred bg-transparent hover:text-black">
                                Cancel
                            </button>
                            <button type="button"
                                    class="ml-3 btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                    @click="editing = false; saveComment({{ $comment->id }}, editedComment, {{ $ticket->id }})">
                                Save
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="px-4 w-full mt-3">
                <form id="addComment_{{ $ticket->id }}" class="mb-6">
                        <textarea id="comment_{{ $ticket->id }}" rows="6"
                                  class="resize-none form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                  placeholder="Write a comment..." required></textarea>
                    <button type="button" onclick="sendComment({{ $ticket->id }})"
                            class="mt-2 btn text-black dark:text-black border bg-lightblue-200 dark:bg-lightblue-200 border-lightblue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white">
                        Post Comment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
