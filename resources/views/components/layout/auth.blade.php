<!DOCTYPE html>
<html x-data="main" class="dark" :class="[$store.app.mode]">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="erp inversingindigiral" />

    <!-- Site Tiltle -->
    <title>Login - Gteam</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.png" />

    @vite(['resources/css/app.css'])
</head>

@php
$currentRoute = request()->path();
$isLoginCover = $currentRoute === 'authenticate/login-cover';
@endphp
<body class="antialiased relative font-inter bg-lightwhite dark:bg-black text-black dark:text-white text-sm font-normal overflow-x-hidden @if($isLoginCover) bg-[url(/assets/images/login-bg.jpg)] bg-no-repeat bg-cover @endif">

    <!-- Start Header -->
    @if(!$isLoginCover)
        <x-common.auth-header/>
    @endif
    <!-- End Header -->
    <!-- Start Content -->
    {{ $slot }}
    <!-- End Content -->
    <!-- Start Footer -->
    <x-common.auth-footer/>
    <!-- End Footer -->
    <!-- All javascirpt -->
    <!-- Alpine js -->
    <script src="/assets/js/alpine-collaspe.min.js"></script>
    <script src="/assets/js/alpine-persist.min.js"></script>
    <script src="/assets/js/alpine-ui.min.js"></script>
    <script src="/assets/js/alpine.min.js" defer></script>
    <!-- Custom js -->
    <script src="/assets/js/custom.js"></script>
    <script src="/assets/js/login.js"></script>
</body>

</html>
