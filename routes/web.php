<?php

declare(strict_types=1);

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerUrlaubController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResponsibilityController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UrlaubController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminSettingsController;
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

Route::group(['prefix' => 'api', 'middleware' => 'api_key'], function () {
    Route::get('/employees', [ApiController::class, 'index']);
    Route::post('/employees/store', [ApiController::class, 'store']);
});

Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Employee Routes
    Route::controller(EmployeeController::class)->group(function () {
        Route::middleware('checkRole:manager')->group(function () {
            Route::get('/employees', 'index')->name('employees');
            Route::get('/employees/new', 'new')->name('employees.new');
            Route::post('/employees/create', 'create')->name('employees.create');
            Route::post('/employees/store', 'store')->name('employees.store');
            Route::get('/employees/edit/{id}', 'edit')->name('employees.edit');
            Route::post('/employees/update/{id}', 'update')->name('employees.update');
            Route::post('/employees/{id}/availabilities', 'saveAvailabilities')->name('employees.availabilities');
        });
        Route::middleware('checkRole:manager,admin')->group(function () {
            Route::get('/employees/search', 'search')->name('employees.search');
            Route::get('/employees/searchInfo', 'searchInfo')->name('employees.searchInfo');
        });
    });

    // Orders
    Route::controller(OrdersController::class)->middleware('checkRole:manager')->group(function () {
        Route::get('/orders', 'index')->name('orders');
        Route::get('/orders/create', 'create')->name('orders.create');
        Route::post('/orders/distance', 'distance')->name('orders.distance');
        Route::get('/orders/search', 'search')->name('orders.search');
        Route::get('/orders/test', 'test')->name('orders.test');
        Route::post('/orders/store', 'store')->name('orders.store');
        Route::get('/orders/availabilities', 'availabilities')->name('orders.availabilities');
    });

    //vacation
    Route::controller(UrlaubController::class)->middleware('checkRole:manager,employee')->group(function () {
        Route::get('/urlaubs', 'index')->name('urlaubs');
        Route::get('/urlaubs/beantragen', 'beantragen')->name('urlaubs.beantragen');
        Route::post('/urlaubs/speichern', 'speichern')->name('urlaubs.speichern');
        Route::get('/urlaubs/übersicht', 'übersicht')->name('urlaubs.übersicht');
        Route::get('/urlaubs/feiertage', 'feiertage')->name('urlaubs.feiertage');
        Route::delete('/urlaub/{id}/loeschen', 'destroy')->name('urlaubs.loeschen');
        Route::get('/urlaubs/genehmigen', 'genehmigen')->name('urlaubs.genehmigen');
    });

    // User Routes
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/users/new', 'new')->name('users.new');
        Route::post('/users/create', 'create')->name('users.create');
        Route::get('/users/search', 'search')->name('users.search');
        Route::post('/users/store', 'store')->name('users.store');
        Route::post('/users/createEmployeeCreds', 'createEmployeeCreds')->name('users.createEmployeeCreds');
        Route::post('/users/updateEmployeeCreds/{id}', 'updateEmployeeCreds')->name('users.updateEmployeeCreds');
        Route::get('/users/edit/{id}', 'edit')->name('users.edit');
        Route::post('/users/update/{id}', 'update')->name('users.update');
    });

    // Department Routes
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('/departments', 'index')->name('departments');
        Route::get('/departments/new', 'new')->name('departments.new');
        Route::post('/departments/create', 'create')->name('departments.create');
        Route::get('/departments/search', 'search')->name('departments.search');
        Route::post('/departments/store', 'store')->name('departments.store');
        Route::get('/departments/edit/{id}', 'edit')->name('departments.edit');
        Route::post('/departments/update/{id}', 'update')->name('departments.update');
        Route::get('/departments/getEmployeesFromDepartmentByUser/{id}/{startOfWeek}/{endOfWeek}', 'getEmployeesFromDepartmentByUser')->name('departments.getEmployeesFromDepartmentByUser');
    });

    // Time Entry Routes
    Route::resource('time_entries', TimeEntryController::class);
    Route::get('/time-entries/daily', [TimeEntryController::class, 'daily'])->name('time_entries.daily');
    Route::get('/time-entries/weekly', [TimeEntryController::class, 'weekly'])->name('time_entries.weekly');
    Route::get('/time-entries/monthly', [TimeEntryController::class, 'monthly'])->name('time_entries.monthly');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer Controller
    Route::controller(CustomerController::class)->middleware('checkRole:manager')->group(function () {
        Route::get('/customers', 'index')->name('customers');
        Route::post('/customers', 'store')->name('customers.store');
        Route::delete('/customers/{id}', 'delete')->name('customers.delete');
        Route::get('/customers/edit/{id}', 'edit')->name('customers.edit');
        Route::put('/customers/edit/{id}', 'update')->name('customers.update');
    });

    // Shift Controller (Employee Shifts)
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts');
    Route::post('/shifts/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
    Route::get('/shifts/getUsersWithShifts/{userId}', [ShiftController::class, 'getUsersWithShifts'])->name('shifts.getUsersWithShifts');
    Route::get('/shifts/getShiftsWithUsers', [ShiftController::class, 'getShiftsWithUsers'])->name('scheduling.getShiftsWithUsers');

    // Scheduling Controller (User Shifts)
    Route::controller(SchedulingController::class)->middleware('checkRole:manager,employee')->group(function () {
        Route::get('/scheduling', 'index')->name('scheduling');
        Route::post('/scheduling/addshifts', 'addshifts')->name('scheduling.addshifts');
        Route::post('/scheduling/addMultipleShifts', [SchedulingController::class, 'addMultipleShifts'])->name('scheduling.addMultipleShifts');
        Route::get('/scheduling/getShifts', 'getShifts')->name('scheduling.getShifts');
        Route::post('/scheduling/assignEmployeesToShift', 'assignEmployeesToShift')->name('scheduling.assignEmployeesToShift');
        Route::post('/scheduling/removeEmployeesFromShift', 'removeEmployeesFromShift')->name('scheduling.removeEmployeesFromShift');
        Route::get('/scheduling/getEmployeesForShift/{shiftId}/{userId}/{startOfWeek}/{endOfWeek}', [SchedulingController::class, 'getEmployeesForShift'])->name('scheduling.getEmployeesForShift');
        Route::delete('/scheduling/deleteShift/{shiftId}', 'deleteShift')->name('scheduling.deleteShift');
    });
    // responsibilities
    Route::controller(ResponsibilityController::class)->middleware('checkRole:manager,admin')->group(function () {
        Route::delete('/responsibilities/delete', 'delete')->name('responsibilities.delete');
        Route::post('/responsibilities/{id}/department/{department_id}', 'create')->name('responsibilities.create');
    });

    //manager urlaub
    Route::controller(ManagerUrlaubController::class)->middleware('checkRole:manager')->group(function () {
        Route::get('/managerurlaub', [ManagerUrlaubController::class, 'index'])->name('managerUrlaubs');
        Route::post('/managerurlaub/genehmigen', [ManagerUrlaubController::class, 'genehmigen'])->name('managerUrlaubs.genehmigen');
        Route::post('/managerurlaub/ablehnen', [ManagerUrlaubController::class, 'ablehnen'])->name('managerUrlaubs.ablehnen');
        Route::delete('/managerurlaub/{id}/loeschen', [ManagerUrlaubController::class, 'destroy'])->name('managerUrlaubs.loeschen');
    });
    // Admin Controller

    Route::controller(AdminSettingsController::class)->group(function () {
        Route::get('/adminsettings', [AdminSettingsController::class, 'index'])->name('adminsettings');
        Route::post('/adminsettings/change', [AdminSettingsController::class, 'change'])->name('adminsettings.change');
    });
    
});

require __DIR__ . '/auth.php';
