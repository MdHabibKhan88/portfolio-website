<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/* ---------- Public website ---------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/projects/{slug}', [HomeController::class, 'project'])->name('projects.show');
Route::get('/cv/download', [HomeController::class, 'cv'])->name('cv.download');
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [HomeController::class, 'robots'])->name('robots');

/* ---------- Admin ---------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('admin')->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profile / About / CV (single record)
        Route::get('page/{section}', [Admin\ProfileController::class, 'edit'])
            ->whereIn('section', ['profile', 'about', 'cv'])->name('profile.edit');
        Route::put('page/{section}', [Admin\ProfileController::class, 'update'])
            ->whereIn('section', ['profile', 'about', 'cv'])->name('profile.update');

        // Messages
        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/read', [Admin\MessageController::class, 'toggleRead'])->name('messages.read');
        Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        // Settings, language content, account
        Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('translations', [Admin\TranslationController::class, 'edit'])->name('translations.edit');
        Route::put('translations', [Admin\TranslationController::class, 'update'])->name('translations.update');
        Route::get('account', [Admin\AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [Admin\AccountController::class, 'update'])->name('account.update');
        Route::put('account/password', [Admin\AccountController::class, 'password'])->name('account.password');

        // Generic CRUD for every content type defined in config/admin.php
        $resources = 'projects|skill-categories|skills|education|experience|volunteers|certificates|achievements|gallery|social-links';
        Route::controller(Admin\ResourceController::class)
            ->where(['resource' => $resources, 'id' => '[0-9]+'])
            ->group(function () {
                Route::get('{resource}', 'index')->name('resource.index');
                Route::get('{resource}/create', 'create')->name('resource.create');
                Route::post('{resource}', 'store')->name('resource.store');
                Route::get('{resource}/{id}/edit', 'edit')->name('resource.edit');
                Route::put('{resource}/{id}', 'update')->name('resource.update');
                Route::delete('{resource}/{id}', 'destroy')->name('resource.destroy');
                Route::post('{resource}/{id}/toggle', 'toggle')->name('resource.toggle');
                Route::post('{resource}/{id}/move/{direction}', 'move')->name('resource.move');
            });
    });
});
