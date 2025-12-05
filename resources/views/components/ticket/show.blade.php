@props(['ticket'])

<div class="flex-1 flex items-start gap-2 p-1">
    <div class="flex-col text-left mr-2">
        <p class="line-clamp-1">{{ $ticket->topic->category->name }}</p>
        <p class="text-xs text-black/40 dark:text-white/40 line-clamp-1">
            {{ $ticket->topic->name }}
        </p>
    </div>
    <p class="w-fit text-sm px-1.5 rounded-[18px] text-white self-center"
       style="background-color: {{ trim($ticket->status->color) }}">
        {{ $ticket->status->name }}
    </p>
</div>
<p class="flex-none text-xs text-black/40 dark:text-white/40">
    @php
        $createdAt = \Carbon\Carbon::parse($ticket->created_at);
        $now = \Carbon\Carbon::now();
    @endphp
    @if ($createdAt->isToday())
        <!-- Если дата сегодня, показываем только время -->
        {{ $createdAt->format('H:i') }}
    @elseif ($createdAt->isCurrentYear())
        <!-- Если дата в этом году, показываем месяц и день -->
        {{ $createdAt->format('F d') }}
    @else
        <!-- Если дата не в этом году, показываем год, месяц и день -->
        {{ $createdAt->format('Y F d') }}
    @endif
</p>
