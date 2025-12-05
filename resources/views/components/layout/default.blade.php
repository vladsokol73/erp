<!DOCTYPE html>
<html x-data="main" class="dark" :class="[$store.app.mode]">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="erp inversingindigiral" />

    <!-- Site Tiltle -->
    <title>@yield("title")</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.png" />

    {{ $style ?? '' }}
    @vite(['resources/css/app.css'])

    <!-- Include Choices CSS -->
    <link rel="stylesheet" href="/assets/css/choices.min.css" />
    <link rel="stylesheet" href="/assets/css/dark-choices.css" />
</head>

<body x-data="main" class="antialiased relative font-inter bg-white dark:bg-black text-black dark:text-white text-sm font-normal overflow-x-hidden vertical"
      :class="[$store.app.sidebar ? 'toggle-sidebar' : '', $store.app.rightsidebar ? 'right-sidebar' : '', $store.app.menu, $store.app.layout]">

<!-- Start Menu Sidebar Olverlay -->
<div x-cloak class="fixed inset-0 bg-[black]/60 z-40 lg:hidden" :class="{ 'hidden': !$store.app.sidebar }" @click="$store.app.toggleSidebar()"></div>
<!-- End Menu Sidebar Olverlay -->

<!-- Start Right Sidebar Olverlay -->
{{--    <div x-cloak class="fixed inset-0 bg-[black]/60 z-50 2xl:hidden" :class="{ 'hidden': !$store.app.rightsidebar }" @click="$store.app.rightSidebar()"></div>--}}
<!-- End Right Sidebar Olverlay -->

<!-- Start Main Content -->
<div class="main-container navbar-sticky flex" :class="[$store.app.navbar]">
    <!-- Start Sidebar -->
    <x-common.sidebar />
    <!-- End sidebar -->

    <!-- Start Content Area -->
    <div class="main-content flex-1">
        <!-- Start Topbar -->
        <x-common.header />
        <!-- End Topbar -->

        <!-- Start Content -->
        <div class="h-[calc(100vh-73px)] overflow-y-auto overflow-x-hidden" data-main_div>
            <x-notification />

            {{ $slot }}

            <!-- Start Footer -->
            <x-common.footer />
            <!-- End Footer -->
        </div>
        <!-- End Content -->
    </div>
    <!-- End Content Area -->

    {{--        <!-- Start Right Sidebar -->--}}
    {{--        <x-common.right-sidebar />--}}
    {{--        <!-- End Right Sidebar -->--}}
</div>
<!-- End Main Content -->

<!-- All javascirpt -->
<!-- Include Choices JavaScript -->
<script src="/assets/js/choices.min.js"></script>
<!-- Alpine js -->
<script src="/assets/js/alpine-collaspe.min.js"></script>
<script src="/assets/js/alpine-persist.min.js"></script>
<script src="/assets/js/alpine-ui.min.js"></script>
<script src="/assets/js/alpine.min.js" defer></script>

<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>



<!-- Custom js -->
<script src="/assets/js/custom.js"></script>
<script src="/assets/js/default.js"></script>
<script src="/assets/js/filesize.min.js"></script>
<script src="/assets/js/jquery.min.js"></script>
{{ $script ?? '' }}
</body>

</html>
