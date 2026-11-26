<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('page_title', t('a_dashboard')) | Admin</title>
    <script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||'dark')}catch(e){}</script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('head')
</head>
<body class="admin-body">
@php
    $nav = [
        ['admin.dashboard', null, 'fa-gauge', 'a_dashboard', 'admin/dashboard'],
        ['admin.profile.edit', 'profile', 'fa-user', 'a_profile', 'admin/page/profile'],
        ['admin.profile.edit', 'about', 'fa-address-card', 'a_about', 'admin/page/about'],
        ['admin.resource.index', 'projects', 'fa-diagram-project', 'a_projects', 'admin/projects*'],
        ['admin.resource.index', 'skills', 'fa-code', 'a_skills', 'admin/skills*'],
        ['admin.resource.index', 'skill-categories', 'fa-layer-group', 'a_skill_categories', 'admin/skill-categories*'],
        ['admin.resource.index', 'education', 'fa-graduation-cap', 'a_education', 'admin/education*'],
        ['admin.resource.index', 'experience', 'fa-briefcase', 'a_experience', 'admin/experience*'],
        ['admin.resource.index', 'volunteers', 'fa-hand-holding-heart', 'a_volunteers', 'admin/volunteers*'],
        ['admin.resource.index', 'certificates', 'fa-certificate', 'a_certificates', 'admin/certificates*'],
        ['admin.resource.index', 'achievements', 'fa-trophy', 'a_achievements', 'admin/achievements*'],
        ['admin.resource.index', 'gallery', 'fa-images', 'a_gallery', 'admin/gallery*'],
        ['admin.resource.index', 'social-links', 'fa-share-nodes', 'a_social_links', 'admin/social-links*'],
        ['admin.messages.index', null, 'fa-envelope', 'a_messages', 'admin/messages*'],
        ['admin.profile.edit', 'cv', 'fa-file-pdf', 'a_cv', 'admin/page/cv'],
        ['admin.settings.edit', null, 'fa-gear', 'a_settings', 'admin/settings'],
        ['admin.translations.edit', null, 'fa-language', 'a_language_content', 'admin/translations'],
        ['admin.account.edit', null, 'fa-user-shield', 'a_account', 'admin/account*'],
    ];
@endphp
<div class="d-flex">
    <nav class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-label="Admin">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.dashboard') }}" class="fw-bold text-decoration-none accent"><i class="fa-solid fa-user-gear me-2"></i>Admin</a>
            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
        </div>
        <ul class="nav flex-column p-2">
            @foreach($nav as [$route, $param, $icon, $label, $pattern])
                <li class="nav-item">
                    <a class="nav-link {{ request()->is($pattern) ? 'active' : '' }}" href="{{ $param ? route($route, $param) : route($route) }}">
                        <i class="fa-solid {{ $icon }} fa-fw me-2"></i>{{ t($label) }}
                    </a>
                </li>
            @endforeach
            <li class="nav-item mt-2">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger"><i class="fa-solid fa-right-from-bracket fa-fw me-2"></i>{{ t('a_logout') }}</button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="flex-grow-1 min-w-0">
        <header class="admin-topbar d-flex align-items-center gap-2 px-3 py-2">
            <button class="btn btn-sm btn-outline-accent d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
            <h1 class="h5 mb-0 me-auto">@yield('page_title')</h1>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-accent d-none d-sm-inline-block"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>{{ t('a_view_site') }}</a>
            <a class="btn btn-sm btn-outline-accent" href="{{ route('lang', app()->getLocale() === 'en' ? 'bn' : 'en') }}">{{ app()->getLocale() === 'en' ? 'বাংলা' : 'English' }}</a>
            <button type="button" class="btn btn-sm btn-outline-accent" data-theme-toggle aria-label="{{ t('toggle_theme') }}"><i class="fa-solid fa-sun icon-light"></i><i class="fa-solid fa-moon icon-dark"></i></button>
        </header>
        <div class="p-3 p-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" role="alert"><strong>{{ t('a_fix_errors') }}</strong>
                    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
