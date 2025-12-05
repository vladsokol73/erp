@section('title', 'Users - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="grid grid-cols-1 gap-7">
            <div x-data="{ basic: {} }"
                 class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Users</p>
                </div>


                <div class="dataTable-top flex" x-cloak>
                    <form id="form_create" method="post"
                          action="{{ route("createUser") }}">
                        @csrf
                        <div x-data="modals">
                            <button type="button" @click="toggle"
                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white mb-5">
                                Create User
                            </button>
                            <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden overflow-y-auto"
                                 :class="open && '!block'">
                                <div class="flex items-start justify-center min-h-screen px-4"
                                     @click.self="open = false">
                                    <div x-show="open" x-transition x-transition.duration.300
                                         class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg w-full max-w-xl my-8">
                                        <div
                                            class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                            <h5 class="font-semibold text-lg">Create User</h5>
                                            <button type="button"
                                                    class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                    @click="toggle">
                                                <svg class="w-5 h-5" width="32" height="32"
                                                     viewBox="0 0 32 32"
                                                     fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z"
                                                        fill="currentcolor"/>
                                                    <path
                                                        d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z"
                                                        fill="currentcolor"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-data="{activeunderTab:'profile'}">
                                            <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'profile'"
                                                       :class="activeunderTab === 'profile' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       class="inline-block p-4">
                                                        Profile
                                                    </a>
                                                </li>

                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'permissions'"
                                                       :class="activeunderTab === 'permissions' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       class="inline-block p-4">
                                                        Permissions
                                                    </a>
                                                </li>

                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'country'"
                                                       :class="activeunderTab === 'country' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       class="inline-block p-4">
                                                        Countries
                                                    </a>
                                                </li>

                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'tags'"
                                                       :class="activeunderTab === 'tags' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       class="inline-block p-4">
                                                        Tags
                                                    </a>
                                                </li>

                                                <li class="mr-2">
                                                    <a href="javaScript:;"
                                                       @click="activeunderTab = 'projects'"
                                                       :class="activeunderTab === 'projects' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                       id="projectsCreate"
                                                       class="inline-block p-4 invisible">
                                                        Projects
                                                    </a>
                                                </li>

                                            </ul>

                                            <div class="tab-content">
                                                <div class="p-5">
                                                    <div class="text-sm text-black dark:text-white">
                                                        <div
                                                            class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                                            <div x-show="activeunderTab === 'profile'" class="">

                                                                <div class="mb-5">
                                                                    <p class="text-sm font-semibold">
                                                                        Login
                                                                    </p>
                                                                </div>
                                                                <input id="login" name="email" type="text"
                                                                       placeholder="User Email"
                                                                       required
                                                                       minlength="5"
                                                                       maxlength="30"
                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Name
                                                                        </p>
                                                                    </div>
                                                                    <input id="name" name="name" type="text"
                                                                           placeholder="User Name"
                                                                           required
                                                                           minlength="3"
                                                                           maxlength="30"
                                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                </div>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Password
                                                                        </p>
                                                                    </div>
                                                                    <input name="password" type="password"
                                                                           id="password"
                                                                           required
                                                                           minlength="8"
                                                                           maxlength="40"
                                                                           placeholder="User Password"
                                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                </div>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Repeat
                                                                            Password
                                                                        </p>
                                                                    </div>
                                                                    <input type="password"
                                                                           id="confirm_password"
                                                                           required
                                                                           minlength="8"
                                                                           maxlength="40"
                                                                           placeholder="Repeat Password"
                                                                           class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                </div>
                                                                <div style="display: none"
                                                                     id="error-pass"
                                                                     class="mt-5 inline-flex items-center rounded-full text-xs justify-center px-2 py-1 bg-lightred text-black">
                                                                </div>

                                                                <div class="mt-5">
                                                                    <div class="mb-5">
                                                                        <div class="mb-5">
                                                                            <p class="text-sm font-semibold">
                                                                                Roles
                                                                            </p>
                                                                        </div>
                                                                        @foreach($roles as $role)
                                                                            <label class="inline-flex">
                                                                                <input type="radio"
                                                                                       value="{{ $role->id }}"
                                                                                       name="role"
                                                                                       class="form-radio text-lightgreen-200 peer"/>
                                                                                <span
                                                                                    class="peer-checked:text-lightgreen-200">{{ $role->title }}</span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div x-show="activeunderTab === 'permissions'" class="">
                                                                @foreach($groupedPermissions as $group => $permissions)
                                                                    <h3 class="font-bold text-lg mb-2">{{ ucfirst($group) }}</h3>
                                                                    <div class="mb-5 flex flex-wrap border-b border-white/10">
                                                                        @foreach($permissions as $permission)
                                                                            <label class="inline-flex mr-4 mb-2">
                                                                                <input value="{{ $permission->id }}"
                                                                                       name="permission_{{ $permission->id }}"
                                                                                       type="checkbox"
                                                                                       data-guard-name="{{ $permission->guard_name }}"
                                                                                       class="form-checkbox outline-success"/>
                                                                                <span>{{ $permission->title }}</span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <div x-show="activeunderTab === 'country'" class="">
                                                                <div class="mb-5">
                                                                    <!--select countries-->
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Available Countries</p>
                                                                    </div>

                                                                    <input type="text" id="searchCountry"
                                                                           placeholder="Search country..."
                                                                           class="form-input !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none mb-2">

                                                                    <div class="flex items-center mb-2">
                                                                        <label
                                                                            class="flex items-center cursor-pointer mb-4">
                                                                            <input type="checkbox" id="showSelected"
                                                                                   class="form-checkbox">
                                                                            <span>Show only selected countries</span>
                                                                        </label>
                                                                    </div>

                                                                    <select
                                                                        id="countrySelect"
                                                                        name="available_countries[]"
                                                                        multiple="multiple"
                                                                        size="10"
                                                                        class="focus:outline-none mt-3 form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                        <option value="" selected>Null</option>
                                                                        <option value="all">All Countries</option>

                                                                        @foreach($countries as $country)
                                                                            <option
                                                                                value="{{ $country->id }}">{{ $country->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div x-show="activeunderTab === 'tags'" class="">
                                                                <div class="mb-5">
                                                                    <!--select countries-->
                                                                    <div class="mb-5">
                                                                        <p class="text-sm font-semibold">
                                                                            Available Tags</p>
                                                                    </div>

                                                                    <select
                                                                        name="available_tags[]"
                                                                        multiple="multiple"
                                                                        size="10"
                                                                        data-placeholder="Select Tags"
                                                                        class="select">
                                                                        <option value="all">All Tags</option>

                                                                        @foreach($tags as $tag)
                                                                            <option
                                                                                value="{{ $tag->id }}">{{ $tag->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div x-show="activeunderTab === 'projects'" class="">
                                                                <div class="flex">
                                                                    <div id="client" style="display: none"
                                                                         class="w-full">
                                                                        <input type="text" value=""
                                                                               placeholder="Search Channel..."
                                                                               class="px-2 py-2 h-10 rounded-md form-input border border-black/10 dark:border-white/10"
                                                                               id="clients_search"/>

                                                                        <p class="text-sm font-semibold mt-3 ml-1">
                                                                            Choose
                                                                            Channels</p>

                                                                        <select
                                                                            id="clientSelect"
                                                                            name="available_clients[]"
                                                                            multiple="multiple"
                                                                            size="10"
                                                                            class="focus:outline-none mt-2 form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                            <option value="" selected>Null</option>
                                                                            <option value="all">All Channels
                                                                            </option>
                                                                            @foreach($channels as $channel)
                                                                                <option
                                                                                    value="{{ $channel->channel_id }}">
                                                                                    {{ $channel->channel_id }}
                                                                                    @if(!is_null($channel->name))
                                                                                        ({{ $channel->name }})
                                                                                    @endif
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div id="operator" style="display: none"
                                                                         class="w-full">
                                                                        <input type="text" value=""
                                                                               placeholder="Search Operator..."
                                                                               class="px-2 py-2 h-10 rounded-md form-input border border-black/10 dark:border-white/10"
                                                                               id="operators_search"/>

                                                                        <p class="text-sm font-semibold mt-3 ml-1">
                                                                            Choose
                                                                            Operators</p>

                                                                        <select
                                                                            id="channelSelect"
                                                                            name="available_operators[]"
                                                                            multiple="multiple"
                                                                            size="10"
                                                                            class="focus:outline-none mt-2 form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                            <option value="" selected>Null</option>
                                                                            <option value="all">All Operators
                                                                            </option>

                                                                            @foreach($operators as $operator)
                                                                                <option
                                                                                    value="{{ $operator->operator_id }}">
                                                                                    {{ $operator->operator_id }}
                                                                                    @if(!is_null($operator->name))
                                                                                        ({{ $operator->name }})
                                                                                    @endif
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="flex justify-end items-center mt-8 gap-4">
                                                            <button type="button"
                                                                    class="btn !bg-lightred !text-white"
                                                                    @click="toggle">Discard
                                                            </button>
                                                            <button type="submit"
                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                                    onclick="validatePassword()">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form method="GET" class="dataTable-search" action="{{ route('users') }}">
                        <input value="{{ request('searchUser') }}" class="dataTable-input" placeholder="Search..."
                               type="text" name="searchUser" onchange="this.form.submit()">
                    </form>
                </div>


                <table id="users" class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5">
                    <thead>
                    <tr>
                        <th class="py-2">User ID</th>
                        <th class="py-2">Login</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Role</th>
                        <th class="py-2">2FA</th>
                        <th class="py-2">Last Login</th>
                        <th class="py-2">Edit</th>
                        <th class="py-2">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="whitespace-nowrap py-2">{{ $user->id }}</td>
                            <td class="py-2">{{ $user->email }}</td>
                            <td class="py-2">{{ $user->name }}</td>
                            <td class="py-2">
                                @foreach($user->roles as $role)
                                    {{ $role->title }}
                                @endforeach
                            </td>
                            <td class="py-2">
                                <button type="button"
                                        onclick="toggle2FA({{ $user->id }})"
                                        class="twofa-status-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-2fa-enabled="{{ $user->google2fa_enabled ? 'true' : 'false' }}">
                                    <span class="twofa-icon {{ $user->google2fa_enabled ? 'text-emerald-500' : 'text-gray-400' }}">
                                        @if($user->google2fa_enabled)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                        </svg>
                                        @endif
                                    </span>
                                </button>
                            </td>
                            <td class="py-2">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d') : '' }}</td>
                            <!--EDIT MODAL-->
                            <td class="py-2">
                                <form id="form_edit_{{ $user->id }}" method="post"
                                      action="{{ route("editUser", $user->id) }}">
                                    @csrf
                                    <div x-data="modals">
                                        <button type="button"
                                                class="btn text-xs h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-2 px-3"
                                                @click="toggle">
                                            Edit
                                        </button>
                                        <div class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999]  hidden"
                                             :class="open && '!block'">
                                            <div class="flex items-start justify-center min-h-screen px-4"
                                                 @click.self="open = false">
                                                <div x-show="open" x-transition x-transition.duration.300
                                                     class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden  w-full max-w-xl my-8">
                                                    <div
                                                        class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                                                        <h5 class="font-semibold text-lg">Edit User</h5>
                                                        <button type="button"
                                                                class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white"
                                                                @click="toggle">
                                                            <svg class="w-5 h-5" width="32" height="32"
                                                                 viewBox="0 0 32 32"
                                                                 fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z"
                                                                    fill="currentcolor"/>
                                                                <path
                                                                    d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z"
                                                                    fill="currentcolor"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div x-data="{activeunderTab:'profile'}">
                                                        <ul class="p-5 flex flex-wrap -mb-px text-sm text-center text-black/50 dark:text-white/50">
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'profile'"
                                                                   :class="activeunderTab === 'profile' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4">
                                                                    Profile
                                                                </a>
                                                            </li>
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'permissions'"
                                                                   :class="activeunderTab === 'permissions' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4">
                                                                    Permissions
                                                                </a>
                                                            </li>
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'countries'"
                                                                   :class="activeunderTab === 'countries' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4">
                                                                    Countries
                                                                </a>
                                                            </li>
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'tags'"
                                                                   :class="activeunderTab === 'tags' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4">
                                                                    Tags
                                                                </a>
                                                            </li>
                                                            <li class="mr-2">
                                                                <a href="javaScript:;"
                                                                   @click="activeunderTab = 'projects'"
                                                                   :class="activeunderTab === 'projects' ? 'text-black border-b-2 border-black dark:text-white dark:border-white' : 'text-black/40 dark:text-white/40 border-b-2 border-transparent rounded-t-lg hover:text-black dark:hover:text-white hover:border-black dark:hover:border-white'"
                                                                   class="inline-block p-4 invisible"
                                                                   id="projects_{{ $user->id }}">
                                                                    Projects
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <div class="p-5">
                                                                <div class="text-sm text-black dark:text-white">
                                                                    <div
                                                                        class="border border-black/10 dark:border-white/10  p-5 rounded-md">
                                                                        <div x-show="activeunderTab === 'profile'">
                                                                            <div class="mb-5">
                                                                                <p class="text-sm font-semibold">
                                                                                    Login</p>
                                                                            </div>
                                                                            <input name="email" type="text"
                                                                                   placeholder="User Email"
                                                                                   class="form-input pointer-events-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                                                                   value="{{ $user->email }}"/>

                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Name</p>
                                                                                </div>
                                                                                <input name="name" type="text"
                                                                                       placeholder="User Name"
                                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"
                                                                                       value="{{ $user->name }}"/>
                                                                            </div>

                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Password</p>
                                                                                </div>
                                                                                <input name="password"
                                                                                       type="password"
                                                                                       id="password_{{ $user->id }}"
                                                                                       placeholder="User Password"
                                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                            </div>

                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Repeat
                                                                                        Password</p>
                                                                                </div>
                                                                                <input type="password"
                                                                                       id="confirm_password_{{ $user->id }}"
                                                                                       placeholder="Repeat Password"
                                                                                       class="form-input py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;"/>
                                                                            </div>
                                                                            <div style="display: none"
                                                                                 id="error_{{ $user->id }}"
                                                                                 class="mt-5 inline-flex items-center rounded-full text-xs justify-center px-2 py-1 bg-lightred text-black">
                                                                                Incorrect Password
                                                                            </div>
                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    @foreach($roles as $role)
                                                                                        <label class="inline-flex">
                                                                                            @if($user->hasRole($role->title))
                                                                                                <input type="radio"
                                                                                                       checked
                                                                                                       value="{{ $role->id }}"
                                                                                                       name="role"
                                                                                                       class="form-radio text-lightgreen-200 peer"/>
                                                                                                <span
                                                                                                    class="peer-checked:text-lightgreen-200">{{ $role->title }}</span>
                                                                                            @else
                                                                                                <input type="radio"
                                                                                                       value="{{ $role->id }}"
                                                                                                       name="role"
                                                                                                       class="form-radio text-lightgreen-200 peer"/>
                                                                                                <span
                                                                                                    class="peer-checked:text-lightgreen-200">{{ $role->title }}</span>
                                                                                            @endif
                                                                                        </label>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div x-show="activeunderTab === 'permissions'">
                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    @foreach($groupedPermissions as $group => $permissions)
                                                                                        <h2 class="font-bold text-lg mb-2">{{ ucfirst($group) }}</h2>
                                                                                        <div
                                                                                            class="mb-5 flex flex-wrap border-b border-white/10">
                                                                                            @foreach($permissions as $permission)
                                                                                                <label
                                                                                                    class="inline-flex mr-4 mb-2">
                                                                                                    <input
                                                                                                        value="{{ $permission->id }}"
                                                                                                        name="permission_{{ $permission->id }}"
                                                                                                        id="permission_{{ $user->id }}_{{ $permission->id }}"
                                                                                                        @if($user->hasPermissionTo($permission))
                                                                                                            checked
                                                                                                        @endif
                                                                                                        data-guard-name_{{ $user->id }}="{{ $permission->guard_name }}"
                                                                                                        type="checkbox"
                                                                                                        class="form-checkbox outline-success"/>
                                                                                                    <span>{{ $permission->title }}</span>
                                                                                                </label>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div x-show="activeunderTab === 'countries'">
                                                                            <div class="mt-5">
                                                                                <div class="mb-5">
                                                                                    <!--select countries-->
                                                                                    <div class="mb-5">
                                                                                        <p class="text-sm font-semibold">
                                                                                            Available
                                                                                            Countries</p>

                                                                                        <input type="text"
                                                                                               id="searchCountry_{{ $user->id }}"
                                                                                               placeholder="Search country..."
                                                                                               class="mt-5 form-input !rounded-full py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white focus:ring-0 focus:shadow-none;">
                                                                                    </div>

                                                                                    <div
                                                                                        class="flex items-center mb-2">
                                                                                        <label
                                                                                            class="flex items-center cursor-pointer mb-4">
                                                                                            <input
                                                                                                type="checkbox"
                                                                                                id="showSelected_{{ $user->id }}"
                                                                                                class="form-checkbox">
                                                                                            <span>Show only selected countries</span>
                                                                                        </label>
                                                                                    </div>

                                                                                    <select
                                                                                        id="countrySelect_{{ $user->id }}"
                                                                                        name="countries[]"
                                                                                        multiple="multiple"
                                                                                        size="10"
                                                                                        class="focus:outline-none form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                                        <option value=""
                                                                                                @if($user->hasCountry())
                                                                                                    selected
                                                                                            @endif>
                                                                                            Null
                                                                                        </option>

                                                                                        <option
                                                                                            value="all"
                                                                                            @if($user->hasCountry('all'))
                                                                                                selected
                                                                                            @endif>
                                                                                            All Countries
                                                                                        </option>

                                                                                        @foreach($countries as $country)
                                                                                            <option
                                                                                                value="{{ $country->id }}"
                                                                                                @if($user->hasCountry($country->id))
                                                                                                    selected
                                                                                                @endif>
                                                                                                {{ $country->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div x-show="activeunderTab === 'tags'" class="">
                                                                            <div class="mb-5">
                                                                                <!--select countries-->
                                                                                <div class="mb-5">
                                                                                    <p class="text-sm font-semibold">
                                                                                        Available Tags</p>
                                                                                </div>

                                                                                <select
                                                                                    name="tags[]"
                                                                                    multiple="multiple"
                                                                                    size="10"
                                                                                    data-placeholder="Select Tags"
                                                                                    class="select">
                                                                                    <option value="all"
                                                                                            @if($user->hasTag('all'))
                                                                                                selected
                                                                                        @endif>
                                                                                        All Tags
                                                                                    </option>

                                                                                    @foreach($tags as $tag)
                                                                                        <option
                                                                                            value="{{ $tag->id }}"
                                                                                            @if($user->hasTag($tag->id))
                                                                                                selected
                                                                                            @endif>
                                                                                            {{ $tag->name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div x-show="activeunderTab === 'projects'">
                                                                            <div class="flex">
                                                                                <div id="client_{{$user->id}}"
                                                                                     style="display: none"
                                                                                     class="w-full">
                                                                                    <input type="text" value=""
                                                                                           placeholder="Search Channel..."
                                                                                           class="px-2 py-2 h-10 rounded-md form-input border border-black/10 dark:border-white/10"
                                                                                           id="clients_search_{{$user->id}}"/>

                                                                                    <p class="text-sm font-semibold mt-3 ml-1">
                                                                                        Choose
                                                                                        Channels</p>

                                                                                    <select
                                                                                        id="clientSelect_{{ $user->id }}"
                                                                                        name="available_clients[]"
                                                                                        multiple="multiple"
                                                                                        size="10"
                                                                                        class="focus:outline-none mt-2 form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                                        <option value=""
                                                                                                @if($user->hasClient())
                                                                                                    selected
                                                                                            @endif>Null
                                                                                        </option>
                                                                                        <option value="all"
                                                                                                @if($user->hasClient("all"))
                                                                                                    selected
                                                                                            @endif>All Channels
                                                                                        </option>
                                                                                        @foreach($channels as $channel)
                                                                                            <option
                                                                                                value="{{ $channel->channel_id }}"
                                                                                                @if($user->hasClient($channel->channel_id))
                                                                                                    selected
                                                                                                @endif>
                                                                                                {{ $channel->channel_id }}
                                                                                                @if(!is_null($channel->name))
                                                                                                    ({{ $channel->name }}
                                                                                                    )
                                                                                                @endif
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>

                                                                                <div id="operator_{{$user->id}}"
                                                                                     style="display: none"
                                                                                     class="w-full">
                                                                                    <input type="text" value=""
                                                                                           placeholder="Search Operator..."
                                                                                           class="px-2 py-2 h-10 rounded-md form-input border border-black/10 dark:border-white/10"
                                                                                           id="operators_search_{{$user->id}}"/>

                                                                                    <p class="text-sm font-semibold mt-3 ml-1">
                                                                                        Choose
                                                                                        Operators</p>

                                                                                    <select
                                                                                        id="channelSelect_{{ $user->id }}"
                                                                                        name="available_operators[]"
                                                                                        multiple="multiple"
                                                                                        size="10"
                                                                                        class="focus:outline-none mt-2 form-multiselect !bg-none py-2.5 px-4 w-full text-black dark:text-white border border-black/10 dark:border-white/10 rounded-lg placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white/10 focus:ring-0 focus:shadow-none;">
                                                                                        <option value=""
                                                                                                @if($user->hasOperator())
                                                                                                    selected
                                                                                            @endif>Null
                                                                                        </option>
                                                                                        <option value="all"
                                                                                                @if($user->hasOperator("all"))
                                                                                                    selected
                                                                                            @endif>All Operators
                                                                                        </option>

                                                                                        @foreach($operators as $operator)
                                                                                            <option
                                                                                                value="{{ $operator->operator_id }}"
                                                                                                @if($user->hasOperator($operator->operator_id))
                                                                                                    selected
                                                                                                @endif>
                                                                                                {{ $operator->operator_id }}
                                                                                                @if(!is_null($operator->name))
                                                                                                    ({{ $operator->name }}
                                                                                                    )
                                                                                                @endif
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="flex justify-end items-center mt-8 gap-4">
                                                                            <button type="button"
                                                                                    class="btn !bg-lightred !text-white"
                                                                                    @click="toggle">Discard
                                                                            </button>
                                                                            <button type="submit"
                                                                                    class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white"
                                                                                    onclick="validatePasswords({{ $user->id }})">
                                                                                Save
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            <td class="py-2 flex flex-wrap text-lightred whitespace-normal">
                                <form method="post"
                                      action="{{ route("deleteUser", $user->id) }}">
                                    @csrf
                                    <x-modals.delete>
                                        Are you sure you want to delete a user with
                                        login: "{{ $user->email }}"?
                                    </x-modals.delete>

                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        @if(!$users->isEmpty())
                            {{ $users->appends(request()->all())->links('vendor.pagination.optimized') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="script">
            <!-- Simple-Datatables js -->
            <script src="/assets/js/simple-datatables.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const userIds = @json($users->pluck('id'));  // Получаем все ID пользователей

                    // Инициализация для каждого пользователя
                    userIds.forEach(userId => {
                        const searchInput = document.getElementById(`searchCountry_${userId}`);
                        const countrySelect = document.getElementById(`countrySelect_${userId}`);
                        const showSelectedCheckbox = document.getElementById(`showSelected_${userId}`);
                        const SearchOperators = document.getElementById(`operators_search_${userId}`);
                        const SearchClients = document.getElementById(`clients_search_${userId}`);
                        const ChannelSelect = document.getElementById(`channelSelect_${userId}`);
                        const ClientSelect = document.getElementById(`clientSelect_${userId}`);

                        if (searchInput && countrySelect) {
                            // Функция поиска стран
                            searchInput.addEventListener('input', function () {
                                filterSearch(searchInput, countrySelect);
                            });

                            // Функция для показа только выбранных стран
                            if (showSelectedCheckbox) {
                                showSelectedCheckbox.addEventListener('change', function () {
                                    toggleSelectedCountries(showSelectedCheckbox, countrySelect);
                                });
                            }

                            // Инициализация поведения для выбора "Null" и "All Countries"
                            initializeSelect(countrySelect);
                        }

                        if (SearchOperators && ChannelSelect) {
                            SearchOperators.addEventListener('input', function () {
                                filterSearch(SearchOperators, ChannelSelect);
                            });

                            initializeSelect(ChannelSelect)
                        }

                        if (SearchClients && ClientSelect) {
                            SearchClients.addEventListener('input', function () {
                                filterSearch(SearchClients, ClientSelect);
                            });

                            initializeSelect(ClientSelect)
                        }


                        // инициализация поведения раздела projects для user edit
                        // Получаем элемент с id "projectsCreate"
                        const projectsCreate = document.getElementById(`projects_${userId}`);

                        // Функция для проверки условий и изменения видимости "projectsCreate"
                        function checkProjects() {
                            const checkboxes = document.querySelectorAll(`input[type='checkbox'][id^='permission_${userId}']`);
                            let shouldShow = false;
                            let shouldShowOperator = false;
                            let shouldShowClient = false;
                            const selectChannel = document.getElementById(`operator_${userId}`);
                            const selectClient = document.getElementById(`client_${userId}`);

                            checkboxes.forEach((checkbox) => {
                                if (checkbox.checked) {
                                    const guardName = checkbox.getAttribute(`data-guard-name_${userId}`);

                                    // Проверяем условие
                                    if (guardName === "operators.show" || guardName === "clients".show) {
                                        shouldShow = true;
                                    }

                                    if (guardName === "operators.show") {
                                        shouldShowOperator = true;
                                    }

                                    if (guardName === "clients.show") {
                                        shouldShowClient = true;
                                    }
                                }
                            });

                            // Добавляем или убираем класс "invisible"
                            if (shouldShow) {
                                projectsCreate.classList.remove("invisible");
                            } else {
                                projectsCreate.classList.add("invisible");
                            }

                            if (shouldShowOperator) {
                                selectChannel.style.display = 'block';
                            } else {
                                selectChannel.style.display = 'none';
                            }

                            if (shouldShowClient) {
                                selectClient.style.display = 'block';
                            } else {
                                selectClient.style.display = 'none';
                            }

                            if (shouldShowClient && shouldShowOperator) {
                                selectChannel.classList.add('ml-5');
                            } else {
                                selectChannel.classList.remove('ml-5');
                            }
                        }

                        // Запускаем проверку при загрузке страницы
                        checkProjects();

                        // Запускаем проверку при изменении состояния любого чекбокса
                        document.querySelectorAll("input[type='checkbox'][name^='permission_']").forEach((checkbox) => {
                            checkbox.addEventListener("change", checkProjects);
                        });
                    });

                    // Для общей формы (без привязки к пользователю)
                    const globalSearchInput = document.getElementById('searchCountry');
                    const globalCountrySelect = document.getElementById('countrySelect');
                    const globalShowSelectedCheckbox = document.getElementById('showSelected');
                    const globalSearchClients = document.getElementById('clients_search');
                    const globalSearchOperators = document.getElementById('operators_search');
                    const globalChannelSelect = document.getElementById('channelSelect');
                    const globalClientSelect = document.getElementById('clientSelect');

                    if (globalSearchInput && globalCountrySelect) {
                        globalSearchInput.addEventListener('input', function () {
                            filterSearch(globalSearchInput, globalCountrySelect);
                        });

                        if (globalShowSelectedCheckbox) {
                            globalShowSelectedCheckbox.addEventListener('change', function () {
                                toggleSelectedCountries(globalShowSelectedCheckbox, globalCountrySelect);
                            });
                        }

                        initializeSelect(globalCountrySelect);
                    }

                    if (globalSearchOperators && globalChannelSelect) {
                        globalSearchOperators.addEventListener('input', function () {
                            filterSearch(globalSearchOperators, globalChannelSelect);
                        });

                        initializeSelect(globalChannelSelect)
                    }

                    if (globalSearchClients && globalClientSelect) {
                        globalSearchClients.addEventListener('input', function () {
                            filterSearch(globalSearchClients, globalClientSelect);
                        });

                        initializeSelect(globalClientSelect)
                    }
                });


                // Функция для фильтрации стран по вводу
                function filterSearch(searchInput, countrySelect) {
                    const searchValue = searchInput.value.toLowerCase();
                    const options = countrySelect.options;

                    for (let i = 0; i < options.length; i++) {
                        const option = options[i];
                        const text = option.textContent.toLowerCase();
                        option.style.display = text.includes(searchValue) ? '' : 'none';
                    }
                }


                // Функция для показа только выбранных стран
                function toggleSelectedCountries(checkbox, countrySelect) {
                    const options = countrySelect.options;

                    if (checkbox.checked) {
                        for (let i = 0; i < options.length; i++) {
                            if (!options[i].selected) {
                                options[i].style.display = 'none';
                            }
                        }
                    } else {
                        for (let i = 0; i <options.length; i++) {
                            options[i].style.display = '';
                        }
                    }
                }

                // Инициализация поведения для "Null" и "All Countries"
                function initializeSelect(select) {
                    const nullOption = select.querySelector('option[value=""]');
                    const allOption = select.querySelector('option[value="all"]');

                    select.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const option = e.target;

                        if (option === nullOption) {
                            clearAllOptions(select);
                            nullOption.selected = true;
                        } else if (option === allOption) {
                            clearAllOptions(select);
                            allOption.selected = true;
                        } else {
                            if (nullOption.selected || allOption.selected) {
                                nullOption.selected = false;
                                allOption.selected = false;
                            }
                            option.selected = !option.selected;
                        }
                    });
                }

                // Снять выделение со всех опций
                function clearAllOptions(select) {
                    for (let opt of select.options) {
                        opt.selected = false;
                    }
                }
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let currentUserId = null;
                    let currentButton = null;

                    // Показать уведомление
                    function showNotification(message, isSuccess = true) {
                        const notification = document.getElementById('notification');
                        const messageEl = document.getElementById('notificationMessage');

                        messageEl.textContent = message;
                        notification.classList.remove('hidden');

                        if (!isSuccess) {
                            notification.querySelector('div').classList.remove('bg-emerald-500/20', 'text-emerald-600', 'dark:text-emerald-400');
                            notification.querySelector('div').classList.add('bg-red-500/20', 'text-red-600', 'dark:text-red-400');
                        } else {
                            notification.querySelector('div').classList.remove('bg-red-500/20', 'text-red-600', 'dark:text-red-400');
                            notification.querySelector('div').classList.add('bg-emerald-500/20', 'text-emerald-600', 'dark:text-emerald-400');
                        }

                        // Скрыть уведомление через 3 секунды
                        setTimeout(() => {
                            notification.classList.add('hidden');
                        }, 3000);
                    }

                    // Открыть модальное окно подтверждения
                    window.toggle2FA = function(userId) {
                        const button = document.querySelector(`.twofa-status-btn[data-user-id="${userId}"]`);
                        const isEnabled = button.getAttribute('data-2fa-enabled') === 'true';

                        currentUserId = userId;
                        currentButton = button;

                        const modal = document.getElementById('confirm2FAModal');
                        const text = document.getElementById('confirm2FAText');

                        text.textContent = isEnabled ?
                            'Are you sure you want to disable 2FA for this user?' :
                            'Are you sure you want to enable 2FA for this user?';

                        modal.classList.remove('hidden');
                    }

                    // Закрыть модальное окно подтверждения
                    window.closeConfirm2FAModal = function() {
                        const modal = document.getElementById('confirm2FAModal');
                        modal.classList.add('hidden');
                        currentUserId = null;
                        currentButton = null;
                    }

                    // Подтвердить изменение статуса 2FA
                    window.confirmToggle2FA = async function() {
                        if (!currentUserId || !currentButton) return;

                        const isEnabled = currentButton.getAttribute('data-2fa-enabled') === 'true';

                        try {
                            const response = await fetch(`/admin-panel/users/${currentUserId}/toggle-2fa`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });

                            if (response.ok) {
                                // Обновляем состояние кнопки
                                currentButton.setAttribute('data-2fa-enabled', (!isEnabled).toString());
                                const icon = currentButton.querySelector('.twofa-icon');

                                if (isEnabled) {
                                    icon.classList.remove('text-emerald-500');
                                    icon.classList.add('text-gray-400');
                                    icon.innerHTML = `
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    `;
                                } else {
                                    icon.classList.remove('text-gray-400');
                                    icon.classList.add('text-emerald-500');
                                    icon.innerHTML = `
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    `;
                                }

                                showNotification(`2FA has been successfully ${isEnabled ? 'disabled' : 'enabled'}`);
                            } else {
                                showNotification('An error occurred while changing 2FA status', false);
                            }

                            closeConfirm2FAModal();
                        } catch (error) {
                            console.error('Error:', error);
                            showNotification('An error occurred while changing 2FA status', false);
                        }
                    }
                });
            </script>
        </x-slot>
    </div>

    <!-- Модальное окно подтверждения 2FA -->
    <div id="confirm2FAModal" class="fixed inset-0 bg-black/60 dark:bg-white/10 z-[999] hidden">
        <div class="flex items-start justify-center min-h-screen px-4">
            <div class="bg-white dark:bg-black relative shadow-3xl border-0 p-0 rounded-lg overflow-hidden w-full max-w-xl my-8">
                <div class="flex bg-white dark:bg-black border-b border-black/10 dark:border-white/10 items-center justify-between px-5 py-3">
                    <h5 class="font-semibold text-lg">Confirm 2FA Status Change</h5>
                    <button type="button" onclick="closeConfirm2FAModal()" class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white">
                        <svg class="w-5 h-5" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24.2929 6.29289L6.29289 24.2929C6.10536 24.4804 6 24.7348 6 25C6 25.2652 6.10536 25.5196 6.29289 25.7071C6.48043 25.8946 6.73478 26 7 26C7.26522 26 7.51957 25.8946 7.70711 25.7071L25.7071 7.70711C25.8946 7.51957 26 7.26522 26 7C26 6.73478 25.8946 6.48043 25.7071 6.29289C25.5196 6.10536 25.2652 6 25 6C24.7348 6 24.4804 6.10536 24.2929 6.29289Z" fill="currentcolor"/>
                            <path d="M7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711L24.2929 25.7071C24.4804 25.8946 24.7348 26 25 26C25.2652 26 25.5196 25.8946 25.7071 25.7071C25.8946 25.5196 26 25.2652 26 25C26 24.7348 25.8946 24.4804 25.7071 24.2929L7.70711 6.29289Z" fill="currentcolor"/>
                        </svg>
                    </button>
                </div>
                <div class="p-5">
                    <p id="confirm2FAText" class="mb-4">Are you sure you want to disable 2FA for this user?</p>
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeConfirm2FAModal()" class="btn bg-gray-200 text-black hover:bg-gray-300">Cancel</button>
                        <button type="button" onclick="confirmToggle2FA()" class="btn !bg-lightred !text-white">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Уведомление -->
    <div id="notification" class="fixed top-4 left-1/2 -translate-x-1/2 z-[1000] hidden transition-opacity duration-300">
        <div class="bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-lg shadow-lg">
            <p id="notificationMessage"></p>
        </div>
    </div>
</x-layout.default>

<script src="/assets/js/admin.js"></script>
