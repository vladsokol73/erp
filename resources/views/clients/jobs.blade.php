@section('title', 'Jobs - Gteam')
<x-layout.default>
    <div class="p-4 sm:p-7 min-h-[calc(100vh-145px)]">
        <div class="px-2 py-1 mb-4">
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div class="border bg-lightwhite dark:bg-white/5 dark:border-white/10 border-black/10 p-5 rounded-md">
                <div class="mb-5">
                    <p class="text-sm font-semibold">Failed Jobs</p>
                </div>
                <button id="restartAllJobs" class="btn text-black dark:text-black border bg-lightgreen-100 dark:bg-lightgreen-100 border-lightgreen-100 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white mb-5">
                    Restart All Jobs
                </button>
                <div class="dataTable-container">
                    <table id="myTablesorter" class="whitespace-nowrap table-bordered dark:[&>tbody>tr:nth-child(odd)]:bg-white/5 w-full">
                        <thead>
                        <tr>
                            <th class="py-2 w-16">Id</th>
                            <th class="py-2 w-32">Connection</th>
                            <th class="py-2 w-32">Queue</th>
                            <th class="py-2 w-40">Failed At</th>
                            <th class="py-2">Exception</th>
                            <th class="py-2 w-32">Restart Job</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->connection }}</td>
                                <td>{{ $job->queue }}</td>
                                <td>{{ $job->failed_at }}</td>
                                <td class="max-w-xs overflow-x-auto">
                                    <div class="whitespace-nowrap overflow-x-auto">{{ $job->exception }}</div>
                                </td>
                                <td>
                                    <form action="{{ route('jobs.restart.single', ['uuid' => $job->uuid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn h-auto leading-none text-black dark:text-black border bg-blue-200 dark:bg-blue-200 border-blue-200 hover:bg-transparent hover:text-black dark:hover:bg-transparent dark:hover:text-white p-1">
                                            Restart
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="dataTable-bottom grid place-items-end">
                    <div>
                        {{ $jobs->appends(request()->all())->links('vendor.pagination.optimized') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Кнопка для перезапуска всех failed jobs
                document.getElementById('restartAllJobs').addEventListener('click', () => {
                    fetch("{{ route('jobs.restart') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF-токен
                        }
                    })
                });
            });
        </script>
    </x-slot>
</x-layout.default>
