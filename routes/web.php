<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');

// صفحات الكتب
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/{book:slug}', [BookController::class, 'show'])->name('show');
    Route::get('/{book:slug}/view', [BookController::class, 'view'])->name('view');
    Route::get('/{book:slug}/serve', [BookController::class, 'serve'])->name('serve');
});

// صفحات الفئات
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
});

// البحث
Route::get('/search', [BookController::class, 'index'])->name('search');

// صفحة اختبار PDF.js
Route::get('/test-pdfjs', function () {
    return view('test-pdfjs');
})->name('test.pdfjs');
