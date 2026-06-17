<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Forum\RequestIndex;
use App\Livewire\Forum\RequestShow;
use App\Livewire\Material\MaterialIndex;
use App\Livewire\Material\MaterialCreate;

use App\Livewire\Admin\UserIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\PointLogIndex;

use App\Livewire\UserProfile;
use App\Livewire\UserPublicProfile;

use App\Livewire\Library\EbookIndex;
use App\Livewire\Library\EbookCreate;
use App\Http\Controllers\Api\SearchController;

Route::view('/', 'welcome');
Route::view('/about', 'about')->name('about');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// Group Route Admin
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    
    // Admin Dashboard / User Management
    Route::get('/users', UserIndex::class)->name('admin.users');
    
    // Reports
    Route::get('/reports', ReportIndex::class)->name('admin.reports');

    // Superadmin Only Logs (Bisa proteksi dobel di component atau tambah middleware)
    Route::get('/logs', PointLogIndex::class)->name('admin.logs');
    
});

Route::middleware(['auth'])->group(function () {
    Route::get('/forum', RequestIndex::class)->name('forum.index');
    Route::get('/forum/{id}', RequestShow::class)->name('forum.show');

    Route::get('/materials', MaterialIndex::class)->name('material.index');
    Route::get('/materials/upload', MaterialCreate::class)->name('material.create');

    Route::get('/library', EbookIndex::class)->name('library.index');
    Route::get('/library/upload', EbookCreate::class)->name('library.create');
});

Route::middleware('auth')->group(function () {
    // Arahkan /profile ke komponen Livewire kita
    Route::get('/profile', UserProfile::class)->name('profile');
    Route::get('/profile', UserProfile::class)->name('profile.edit');
    Route::get('/user/{id}', UserPublicProfile::class)->name('user.show');
    
    // Leaderboard
    Route::get('/leaderboard', App\Livewire\LeaderboardIndex::class)->name('leaderboard');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/search', [SearchController::class, 'search']);
});