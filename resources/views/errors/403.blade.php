@section("title")
    Forbidden
@endsection
<x-layout.pages>
    <div class="min-h-[calc(100vh-134px)] py-4 px-4 sm:px-12 flex justify-center items-center">
        <div class="text-center sm:flex-none">
            <h2 class="text-5xl font-semibold mb-2">403 Forbidden</h2>
            <p class="text-black/40 dark:text-white/40 mb-10">Tou don't have permission to access on this page.</p>
            <img width="200" height="200" src="/assets/images/favicon.svg" class="mb-11 mx-auto dark:hidden" alt="images" />
            <img width="200" height="200" src="/assets/images/favicon-dark.svg" class="mb-11 mx-auto hidden dark:block" alt="images" />

            <a href="/" class="max-w-[149px] py-1 px-2 inline-block bg-black/5 dark:bg-white/5 w-full rounded-lg text-black/40 dark:text-white/40 border border-black/5 dark:border-white/5 hover:bg-transparent dark:hover:bg-transparent hover:text-black dark:hover:text-white transition-all duration-300">
                Back to Home Page
            </a>
        </div>
    </div>
</x-layout.pages>
