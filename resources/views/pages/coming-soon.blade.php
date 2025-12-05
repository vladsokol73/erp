@section("title")
    Coming Soon
@endsection
<x-layout.pages>
<div class="min-h-[calc(100vh-134px)] py-4 px-4 sm:px-12 flex justify-center items-center">
    <div class="text-center sm:flex-none w-full sm:w-[360px]">
        <h2 class="text-5xl font-semibold mb-10">Coming Soon</h2>
        <img src="/assets/images/favicon.svg" class="mb-10 mx-auto dark:hidden" alt="images" />
        <img src="/assets/images/favicon-dark.svg" class="mb-10 mx-auto hidden dark:block" alt="images" />
{{--        <div class="flex justify-center items-center text-5xl font-semibold text-black dark:text-white gap-7 mb-10" x-data="timer(new Date().setDate(new Date().getDate() + 1))" x-init="init();">--}}
{{--            <div>--}}
{{--                <h1 x-text="time().hours"></h1>--}}
{{--                <p class="text-sm text-black/40 dark:text-white/40 text-center">Hours</p>--}}
{{--            </div>--}}
{{--            <p class="text-black/20 dark:text-white/20">:</p>--}}
{{--            <div>--}}
{{--                <h1 x-text="time().minutes"></h1>--}}
{{--                <p class="text-sm text-black/40 dark:text-white/40 text-center">Minutes</p>--}}
{{--            </div>--}}
{{--            <p class="text-black/20 dark:text-white/20">:</p>--}}
{{--            <div>--}}
{{--                <h1 x-text="time().seconds"></h1>--}}
{{--                <p class="text-sm text-black/40 dark:text-white/40 text-center">Seconds</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="relative">--}}
{{--            <input type="text" placeholder="Enter your email to subscribe for updates." class="form-input py-4 px-3 pr-12 w-full text-black dark:text-white bg-black/5 dark:bg-white/5 rounded-2xl placeholder:text-black/20 dark:placeholder:text-white/20 focus:border-black dark:focus:border-white focus:ring-0 focus:shadow-none;" required="">--}}
{{--            <button type="button" class="p-1 text-black/20 dark:text-white/20 absolute top-3 right-3">--}}
{{--                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4539 8.90776C17.4539 8.90776 17.7503 9.0727 17.9227 9.36509C17.9227 9.36509 18.0959 9.65888 18.0959 9.99993C18.0959 9.99993 18.0959 10.341 17.9227 10.6348C17.9227 10.6348 17.7495 10.9286 17.451 11.0937L4.25857 18.4827C4.25857 18.4827 3.91401 18.6731 3.52596 18.6341C3.52596 18.6341 3.13791 18.5951 2.84088 18.3424C2.84088 18.3424 2.54385 18.0896 2.44324 17.7128C2.44324 17.7128 2.34263 17.336 2.47413 16.9689L4.95858 9.99993L2.47382 3.03012C2.47382 3.03012 2.34263 2.66383 2.44324 2.28703C2.44324 2.28703 2.54385 1.91022 2.84088 1.65749C2.84088 1.65749 3.13791 1.40475 3.52596 1.36576C3.52596 1.36576 3.91401 1.32677 4.25538 1.51536L17.451 8.90618L17.4539 8.90776ZM3.65093 2.6095L16.8431 9.99835L16.8459 9.99993L3.65093 17.3904L6.13187 10.4312C6.13187 10.4312 6.30086 9.99993 6.13187 9.56862L3.65093 2.6095Z" fill="currentColor" />--}}
{{--                    <path d="M5.62503 10.625H10.625C10.9702 10.625 11.25 10.3451 11.25 9.99997C11.25 9.65479 10.9702 9.37497 10.625 9.37497H5.62503C5.27985 9.37497 5.00003 9.65479 5.00003 9.99997C5.00003 10.3451 5.27985 10.625 5.62503 10.625Z" fill="currentColor" />--}}
{{--                </svg>--}}
{{--            </button>--}}
{{--        </div>--}}
    </div>
</div>

{{--<x-slot name="footer">--}}
{{--    <script>--}}
{{--        function timer(expiry) {--}}
{{--            return {--}}
{{--                expiry: expiry,--}}
{{--                remaining: null,--}}
{{--                init() {--}}
{{--                    this.setRemaining()--}}
{{--                    setInterval(() => {--}}
{{--                        this.setRemaining();--}}
{{--                    }, 1000);--}}
{{--                },--}}
{{--                setRemaining() {--}}

{{--                    const diff = this.expiry - new Date().getTime();--}}
{{--                    this.remaining = parseInt(diff / 1000);--}}
{{--                },--}}
{{--                days() {--}}
{{--                    return {--}}
{{--                        value: this.remaining / 86400,--}}
{{--                        remaining: this.remaining % 86400--}}
{{--                    };--}}
{{--                },--}}
{{--                hours() {--}}
{{--                    return {--}}
{{--                        value: this.days().remaining / 3600,--}}
{{--                        remaining: this.days().remaining % 3600--}}
{{--                    };--}}
{{--                },--}}
{{--                minutes() {--}}
{{--                    return {--}}
{{--                        value: this.hours().remaining / 60,--}}
{{--                        remaining: this.hours().remaining % 60--}}
{{--                    };--}}
{{--                },--}}
{{--                seconds() {--}}
{{--                    return {--}}
{{--                        value: this.minutes().remaining,--}}
{{--                    };--}}
{{--                },--}}
{{--                format(value) {--}}
{{--                    return ("0" + parseInt(value)).slice(-2)--}}
{{--                },--}}
{{--                time() {--}}
{{--                    return {--}}
{{--                        days: this.format(this.days().value),--}}
{{--                        hours: this.format(this.hours().value),--}}
{{--                        minutes: this.format(this.minutes().value),--}}
{{--                        seconds: this.format(this.seconds().value),--}}
{{--                    }--}}
{{--                },--}}
{{--            }--}}
{{--        }--}}

{{--    </script>--}}
{{--</x-slot>--}}
</x-layout.pages>


