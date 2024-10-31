<?php

declare(strict_types=1);

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('layouts/app');
})->middleware(['auth', 'verified'])->name('layout.app');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::controller(EmployeeController::class)->group(function () {
        Route::get('/employees', 'index')->name('employees');
        Route::get('/employees/new', 'new')->name('employees.new');
        Route::post('/employees/create', 'create')->name('employees.create');
        Route::get('/employees/search', 'search')->name('employees.search');
        Route::post('/employees/store', 'store')->name('employees.store');
        Route::get('/employees/edit/{id}', 'edit')->name('employees.edit');
        Route::post('/employees/update/{id}', 'update')->name('employees.update');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/users/new', 'new')->name('users.new');
        Route::post('/users/create', 'create')->name('users.create');
        Route::get('/users/search', 'search')->name('users.search');
        Route::post('/users/store', 'store')->name('users.store');
        Route::get('/users/edit/{id}', 'edit')->name('users.edit');
        Route::post('/users/update/{id}', 'update')->name('users.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
