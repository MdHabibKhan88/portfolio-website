<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteTitle = trim($__env->yieldContent('title')) ?: (setting('site_title') ?: 'MD. HABIB KHAN');
        $siteDesc = trim($__env->yieldContent('description')) ?: setting('meta_description', '');
        $ogImage = trim($__env->yieldContent('og_image')) ?: file_url($siteProfile->photo);
    @endphp
    <title>{{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ setting('site_title', 'MD. HABIB KHAN') }}">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0a192f">
    <script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||'dark')}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('head')
</head>
<body class="public">
<div id="page-loader" aria-hidden="true"><div class="spinner-border text-info" role="status"></div></div>
<a class="visually-hidden-focusable" href="#main">{{ t('skip_to_content') }}</a>

@php $home = route('home'); @endphp
<nav class="navbar navbar-expand-xl fixed-top site-nav" id="siteNav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ $home }}#home">{{ $siteProfile->tr('name') ?: 'MD. HABIB KHAN' }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-1">
                @foreach(['home','about','skills','projects','education','experience','volunteering','certificates','achievements','contact'] as $s)
                    <li class="nav-item"><a class="nav-link" href="{{ $home }}#{{ $s }}">{{ t('nav_'.$s) }}</a></li>
                @endforeach
                <li class="nav-item ms-xl-2">
                    <a class="btn btn-sm btn-outline-accent" href="{{ route('lang', app()->getLocale() === 'en' ? 'bn' : 'en') }}" hreflang="{{ app()->getLocale() === 'en' ? 'bn' : 'en' }}">
                        <i class="fa-solid fa-language me-1"></i>{{ app()->getLocale() === 'en' ? 'বাংলা' : 'English' }}
                    </a>
                </li>
                <li class="nav-item ms-xl-1 mt-2 mt-xl-0">
                    <button type="button" class="btn btn-sm btn-outline-accent" data-theme-toggle aria-label="{{ t('toggle_theme') }}" title="{{ t('toggle_theme') }}">
                        <i class="fa-solid fa-sun icon-light"></i><i class="fa-solid fa-moon icon-dark"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="main">
    @yield('content')
</main>

<footer class="site-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h2 class="h5 fw-bold">{{ $siteProfile->tr('name') ?: 'MD. HABIB KHAN' }}</h2>
                <p class="muted mb-2">{{ setting('footer_tagline') }}</p>
                @if($siteProfile->email)
                    <a href="mailto:{{ $siteProfile->email }}" class="footer-link"><i class="fa-solid fa-envelope me-2"></i>{{ $siteProfile->email }}</a>
                @endif
            </div>
            <div class="col-6 col-md-3">
                <h3 class="h6 text-uppercase accent">{{ t('quick_links') }}</h3>
                <ul class="list-unstyled mb-0">
                    @foreach(['about','skills','projects','contact'] as $s)
                        <li><a class="footer-link" href="{{ $home }}#{{ $s }}">{{ t('nav_'.$s) }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h3 class="h6 text-uppercase accent">{{ t('follow_me') }}</h3>
                @include('partials.social', ['links' => $socialLinks])
            </div>
        </div>
        <hr class="my-4">
        <p class="text-center muted small mb-0">&copy; 2026 MD. HABIB KHAN. {{ t('rights') }}</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
