<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') | MD. HABIB KHAN</title>
    <meta name="robots" content="noindex">
    <script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||'dark')}catch(e){}</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="error-page d-flex align-items-center justify-content-center text-center min-vh-100">
    <main class="p-4">
        <div class="display-1 fw-bold accent">@yield('code')</div>
        <h1 class="h3 mb-3">@yield('heading')</h1>
        <p class="muted mb-4">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-accent">{{ __('ui.go_home') }}</a>
    </main>
</body>
</html>
