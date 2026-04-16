<?php

use App\Http\Controllers\Admin\AlbumController as AdminAlbumController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ImageController as AdminImageController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Frontend\AlbumController as FrontendAlbumController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Authenticate::redirectUsing(fn ($request) => $request->expectsJson() ? null : route('admin.login'));

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/albums/{slug}', [FrontendAlbumController::class, 'show'])->name('album.show');

Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AdminAuthController::class, 'login']);

Route::prefix('admin')->group(function () {

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/albums', [AdminAlbumController::class, 'index'])->name('admin.albums.index');
        Route::get('/albums/create', [AdminAlbumController::class, 'create'])->name('admin.albums.create');
        Route::post('/albums', [AdminAlbumController::class, 'store'])->name('admin.albums.store');
        Route::get('/albums/{album}/edit', [AdminAlbumController::class, 'edit'])->name('admin.albums.edit');
        Route::put('/albums/{album}', [AdminAlbumController::class, 'update'])->name('admin.albums.update');
        Route::delete('/albums/{album}', [AdminAlbumController::class, 'destroy'])->name('admin.albums.destroy');
        Route::post('/albums/{album}/images', [AdminImageController::class, 'store'])->name('admin.albums.images.store');
        Route::post('/albums/{album}/images/{image}/cover', [AdminImageController::class, 'setCover'])->name('admin.albums.images.cover');
        Route::delete('/images/{image}', [AdminImageController::class, 'destroy'])->name('admin.images.destroy');
        Route::get('/tags', [AdminTagController::class, 'index'])->name('admin.tags.index');
        Route::post('/tags', [AdminTagController::class, 'store'])->name('admin.tags.store');
        Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('admin.tags.destroy');
    });
});
