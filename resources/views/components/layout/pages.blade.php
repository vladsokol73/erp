<!DOCTYPE html>
<html x-data="main" class="dark" :class="[$store.app.mode]">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Erp Inversingindigiral" />

    <!-- Site Tiltle -->
    <title>@yield("title")</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.png" />

    @vite(['resources/css/app.css'])
</head>

<body class="antialiased relative font-inter bg-lightwhite dark:bg-black text-black dark:text-white text-sm font-normal overflow-x-hidden">
    <!-- Start Header -->
   <x-common.pages-header/>
    <!-- End Header -->
    <!-- Start Content -->
    {{ $slot }}
    <!-- End Content -->
    <!-- Start Footer -->
   <x-common.pages-footer/>
    <!-- End Footer -->
    <!-- All javascirpt -->
    <!-- Alpine js -->
    <script src="/assets/js/alpine-collaspe.min.js"></script>
    <script src="/assets/js/alpine-persist.min.js"></script>
    <script src="/assets/js/alpine-ui.min.js"></script>
    <script src="/assets/js/alpine.min.js" defer></script>
    <!-- Custom js -->
    <script src="/assets/js/custom.js"></script>

    {{ $footer ?? "" }}
</body>

</html>
