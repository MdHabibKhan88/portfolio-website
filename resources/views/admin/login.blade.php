<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ t('a_login_title') }}</title>
    <script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||'dark')}catch(e){}</script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="error-page d-flex align-items-center justify-content-center min-vh-100">
<main class="p-3 w-100" style="max-width: 430px;">
    <div class="card-x p-4">
        <div class="text-center mb-4">
            <div class="vol-icon mx-auto mb-3"><i class="fa-solid fa-lock"></i></div>
            <h1 class="h4 mb-1">{{ t('a_login_title') }}</h1>
        </div>
        <form method="POST" action="{{ route('admin.login.attempt') }}" data-loading>
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">{{ t('f_email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">{{ t('a_password') }}</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                <label class="form-check-label" for="remember">{{ t('a_remember') }}</label>
            </div>
            <button type="submit" class="btn btn-accent w-100">
                <span class="btn-label">{{ t('a_login') }}</span>
                <span class="btn-busy d-none"><span class="spinner-border spinner-border-sm me-2"></span>{{ t('sending') }}</span>
            </button>
        </form>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('home') }}" class="small footer-link"><i class="fa-solid fa-arrow-left me-1"></i>{{ t('go_home') }}</a>
            <a class="small footer-link" href="{{ route('lang', app()->getLocale() === 'en' ? 'bn' : 'en') }}">{{ app()->getLocale() === 'en' ? 'বাংলা' : 'English' }}</a>
        </div>
    </div>
</main>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
