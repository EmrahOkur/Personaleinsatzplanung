<?php


use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UrlaubController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
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
  
    Route::get('/urlaubs', [UrlaubController::class, 'index'])->name('urlaubs');
    Route::get('/urlaubs/beantragen', [UrlaubController::class, 'beantragen'])->name('urlaubs.beantragen');
    Route::post('/urlaubs/speichern', [UrlaubController::class, 'speichern'])->name('urlaubs.speichern');
    Route::get('/urlaubs/übersicht', [UrlaubController::class, 'übersicht'])->name('urlaubs.übersicht');
    Route::get('/urlaubs/feiertage', [UrlaubController::class, 'feiertage'])->name('urlaubs.feiertage');
    Route::delete('/urlaub/{id}/loeschen', [UrlaubController::class, 'destroy'])->name('urlaubs.loeschen');
   
    

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::delete('/customers/{id}', [CustomerController::class, 'delete'])->name('customers.delete');
    Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/edit/{id}', [CustomerController::class, 'update'])->name('customers.update');
   
});

require __DIR__.'/auth.php';
