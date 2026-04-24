<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home',['title' => 'Главная']);
})->name('home');

Route::get('/product/{id}', function ($id) {
    return Inertia::render('Product', [
        'id' => $id,
        'title' => 'Страница товара'
    ]);
})->name('product.show');

// Админ маршруты
Route::prefix('admin')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Admin/Login',
            ['title' => 'Авторизация']
        );
    });

    Route::get('/products', function () {
        return Inertia::render('Admin/ProductList',
            ['title' => 'Список товаров']
        );
    })->name('admin.products.index');

    Route::get('/products/create', function () {
        return Inertia::render('Admin/ProductForm',
            ['title' => 'Создание товара']
        );
    })->name('admin.products.create');

    Route::get('/products/{id}/edit', function ($id) {
        return Inertia::render('Admin/ProductForm',
            ['id' => $id, 'title' => 'Редактирование товара']
        );
    })->name('admin.products.edit');
});
