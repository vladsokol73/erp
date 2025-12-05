@props(['ticket', 'countries'])

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
            <div class="bg-lightwhite dark:bg-white/5 rounded-2xl p-6">
                <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold">Ticket Details</h3>
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
                    <div class="flex items-center gap-2">
                        <div x-data="{
                        status: '{{ $ticket->status->name }}',
                        sort: '{{ $ticket->status->sort_order }}',
                        color: '{{ trim($ticket->status->color) }}',
                        async updateStatus(ticketId) {
                        try {
                            let response = await fetch('/tickets/approve', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ticketId: ticketId})
                            });

                            if (response.ok) {
                                let data = await response.json();
                                this.status = data.new_status;
                                this.color = data.new_color;
                                this.sort = data.new_sort
                            } else {
                                let errorData = await response.json();
                                alert(errorData.error || 'Ошибка обновления статуса');
                            }
                        } catch (error) {
                            console.error('Ошибка запроса:', error);
                        }}}" class="flex items-center space-x-2">
                            <p class="w-fit text-sm px-1.5 rounded-[18px]" x-text="status"
                               :style="'background-color: ' + color"></p>

                            <template x-if="sort === '0'">
                                <button type="button" @click="updateStatus({{ $ticket->id }})"
                                        class="px-1.5 rounded-[18px] inline-flex items-center bg-lightgreen-100 dark:bg-lightblue-100 text-black hover:bg-lightgreen-200 dark:hover:bg-lightgreen-200">
                                    <svg class="w-4 h-4 mr-1" width="32" height="32"
                                         viewBox="0 0 32 32"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M11.4012 27.0849C11.4012 27.0849 10.9664 26.9028 9.6139 26.8843C9.6139 26.8843 8.4575 26.8685 7.88867 26.7645C7.88867 26.7645 6.77082 26.56 6.10539 25.8946C6.10539 25.8946 5.43594 25.2252 5.22844 24.0882C5.22844 24.0882 5.12294 23.5102 5.10465 22.3366C5.10465 22.3366 5.08389 21.0046 4.91418 20.5965C4.91418 20.5965 4.74093 20.18 3.79698 19.1924C3.79698 19.1924 2.98525 18.3431 2.6547 17.8655C2.6547 17.8655 2 16.9195 2 16C2 16 2 15.0846 2.64417 14.1522C2.64417 14.1522 2.96978 13.6809 3.77243 12.8434C3.77243 12.8434 4.7293 11.8449 4.91512 11.4012C4.91512 11.4012 5.09721 10.9664 5.1157 9.6139C5.1157 9.6139 5.13151 8.4575 5.23553 7.88867C5.23553 7.88867 5.43996 6.77082 6.10539 6.10539C6.10539 6.10539 6.77484 5.43594 7.91181 5.22844C7.91181 5.22844 8.48983 5.12294 9.66342 5.10465C9.66342 5.10465 10.9954 5.08389 11.4035 4.91418C11.4035 4.91418 11.82 4.74093 12.8076 3.79698C12.8076 3.79698 13.6569 2.98525 14.1345 2.6547C14.1345 2.6547 15.0805 2 16 2C16 2 16.9154 2 17.8478 2.64417C17.8478 2.64417 18.3191 2.96978 19.1566 3.77243C19.1566 3.77243 20.1551 4.7293 20.5988 4.91512C20.5988 4.91512 21.0336 5.09721 22.3861 5.1157C22.3861 5.1157 23.5425 5.13151 24.1113 5.23553C24.1113 5.23553 25.2292 5.43996 25.8946 6.10539C25.8946 6.10539 26.5641 6.77484 26.7716 7.91181C26.7716 7.91181 26.8771 8.48985 26.8953 9.66342C26.8953 9.66342 26.9161 10.9954 27.0858 11.4035C27.0858 11.4035 27.2591 11.82 28.203 12.8076C28.203 12.8076 29.0148 13.6569 29.3453 14.1345C29.3453 14.1345 30 15.0805 30 16C30 16 30 16.9154 29.3558 17.8478C29.3558 17.8478 29.0302 18.3191 28.2276 19.1566C28.2276 19.1566 27.2707 20.1551 27.0849 20.5988C27.0849 20.5988 26.9028 21.0336 26.8843 22.3861C26.8843 22.3861 26.8685 23.5425 26.7645 24.1113C26.7645 24.1113 26.56 25.2292 25.8946 25.8946C25.8946 25.8946 25.2252 26.5641 24.0882 26.7716C24.0882 26.7716 23.5102 26.8771 22.3366 26.8953C22.3366 26.8953 21.0046 26.9161 20.5965 27.0858C20.5965 27.0858 20.18 27.2591 19.1924 28.203C19.1924 28.203 18.3431 29.0148 17.8655 29.3453C17.8655 29.3453 16.9195 30 16 30C16 30 15.0846 30 14.1522 29.3558C14.1522 29.3558 13.6809 29.0302 12.8434 28.2276C12.8434 28.2276 11.8449 27.2707 11.4012 27.0849ZM12.1738 25.2401C12.1738 25.2401 12.9603 25.5695 14.2272 26.7836C14.2272 26.7836 15.4965 28 16 28C16 28 16.5103 28 17.8105 26.7572C17.8105 26.7572 19.0676 25.5556 19.8285 25.2392C19.8285 25.2392 20.5903 24.9223 22.3054 24.8956C22.3054 24.8956 24.0931 24.8677 24.4804 24.4804C24.4804 24.4804 24.8607 24.1001 24.8845 22.3588C24.8845 22.3588 24.9083 20.6186 25.2401 19.8262C25.2401 19.8262 25.5695 19.0397 26.7836 17.7728C26.7836 17.7728 28 16.5035 28 16C28 16 28 15.4897 26.7572 14.1895C26.7572 14.1895 25.5556 12.9324 25.2392 12.1715C25.2392 12.1715 24.9223 11.4097 24.8956 9.69459C24.8956 9.69459 24.8677 7.90694 24.4804 7.51961C24.4804 7.51961 24.1001 7.13932 22.3588 7.11551C22.3588 7.11551 20.6186 7.09172 19.8262 6.75988C19.8262 6.75988 19.0397 6.43046 17.7728 5.2164C17.7728 5.2164 16.5035 4 16 4C16 4 15.4897 4 14.1895 5.24278C14.1895 5.24278 12.9324 6.44437 12.1715 6.76082C12.1715 6.76082 11.4097 7.07767 9.69459 7.10441C9.69459 7.10441 7.90694 7.13227 7.51961 7.51961C7.51961 7.51961 7.13932 7.8999 7.11551 9.64124C7.11551 9.64124 7.09172 11.3814 6.75988 12.1738C6.75988 12.1738 6.43047 12.9603 5.2164 14.2272C5.2164 14.2272 4 15.4965 4 16C4 16 4 16.5103 5.24278 17.8105C5.24278 17.8105 6.44437 19.0676 6.76082 19.8285C6.76082 19.8285 7.07767 20.5903 7.10441 22.3054C7.10441 22.3054 7.13227 24.0931 7.51961 24.4804C7.51961 24.4804 7.8999 24.8607 9.64124 24.8845C9.64124 24.8845 11.3814 24.9083 12.1738 25.2401Z"
                                              fill="currentColor"></path>
                                        <path
                                                d="M11.1909 15.777C11.0048 15.5992 10.7574 15.5 10.5 15.5C10.4998 15.5 10.4773 15.5003 10.4773 15.5003C10.2122 15.5063 9.96027 15.6174 9.77704 15.8091C9.59923 15.9952 9.5 16.2426 9.5 16.5L9.50026 16.5227C9.50627 16.7878 9.61737 17.0397 9.80911 17.223L13.4716 20.723C13.8579 21.0921 14.4662 21.0924 14.8528 20.7236L22.19 13.7238C22.3819 13.5407 22.4935 13.2887 22.4997 13.0235C22.5001 13.0075 22.5001 12.9915 22.4997 12.9755C22.4936 12.727 22.3952 12.4896 22.2236 12.3097C22.0348 12.1119 21.7734 12 21.5 12L21.4718 12.0004C21.2245 12.0074 20.9887 12.1057 20.8097 12.2764L14.1631 18.6174L11.1909 15.777Z"
                                                fill="currentColor"></path>
                                    </svg>
                                    <span class="text-sm">To Approve</span>
                                </button>
                            </template>
                        </div>

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
                            @else
                                <a href="{{ $value }}" class="text-blue-500 underline" download>
                                    📎 Скачать файл
                                </a>
                            @endif
                        @elseif($fieldValue->field->type == "country")
                            <p class="text-sm">{{ $countries->where('id', $fieldValue->value)->first()->name }}</p>
                        @else
                            <p class="text-sm">{{ $fieldValue->value }}</p>
                        @endif
                    @endforeach

                    @if($ticket->result)
                        <p id="result-placeholder-{{ $ticket->id }}"
                           class="first-letter:uppercase text-xs text-black/40 dark:text-white/40">Result</p>
                        <p id="result-{{ $ticket->id }}" class="text-sm">{{ $ticket->result }}</p>
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
