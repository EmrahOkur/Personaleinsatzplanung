<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::delete('/customers/{id}', [CustomerController::class, 'delete'])->name('customers.delete');
    Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/edit/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/shifts', [ShiftController::class,'index'] )->name('shifts');
    Route::post('/shifts/edit', [ShiftController::class,'edit'] )->name('shifts.edit');
    Route::get('/shifts/getUsersWithShifts', [ShiftController::class,'getUsersWithShifts'] )->name('shifts.getUsersWithShifts');
    Route::get('/scheduling', [SchedulingController::class,'index'] )->name('scheduling');
    Route::post('/scheduling/addshifts', [SchedulingController::class,'addshifts'] )->name('scheduling.addshifts');
    Route::get('/scheduling/getShifts', [SchedulingController::class, 'getShifts'])->name('scheduling.getShifts');
    Route::get('/shifts/getShiftsWithUsers', [ShiftController::class, 'getShiftsWithUsers'])->name('scheduling.getShiftsWithUsers');

    Route::post('/scheduling/assignEmployeesToShift', [SchedulingController::class,'assignEmployeesToShift'])->name('scheduling.assignEmployeesToShift');
    Route::post('/scheduling/removeEmployeesFromShift', [SchedulingController::class,'removeEmployeesFromShift'])->name('scheduling.removeEmployeesFromShift');
    Route::get('/scheduling/getEmployeesForShift/{shiftId}', [SchedulingController::class, 'getEmployeesForShift'])->name('scheduling.getEmployeesForShift');
    Route::delete('/scheduling/deleteShift/{shiftId}', [SchedulingController::class, 'deleteShift'])->name('scheduling.deleteShift');
});

require __DIR__.'/auth.php';
