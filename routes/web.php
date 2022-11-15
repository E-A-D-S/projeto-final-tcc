<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::match(['get', 'post'], '/home', [UserController::class, 'home'])->name('paciente.home');
Route::match(['get', 'post'], '/', [UserController::class, 'homeScreen'])->name('paciente.homeScreen');
Route::match(['get', 'post'], 'paciente/store', [UserController::class, 'store'])->name('paciente.store');

Route::prefix('paciente')->middleware('can:admin')->group(function () {
    Route::match(['get', 'post'], '', [UserController::class, 'index'])->name('paciente.index');
    Route::match(['get', 'post'], '/permission', [UserController::class, 'permission'])->name('paciente.permission');
    Route::match(['get', 'post'], '/permission/{id}', [UserController::class, 'permissionEdit'])->name('paciente.permission.edit');
    Route::put('/permission/update/{id}', [UserController::class, 'permissionUpdate'])->name('paciente.permission.update');
    Route::match(['get', 'post'], '/create', [UserController::class, 'create'])->name('paciente.create');
    
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('paciente.destroy');
    Route::match(['get', 'post'], '/edit/{id}', [UserController::class, 'edit'])->name('paciente.edit');
    Route::match(['get', 'post'], '/view/{id}', [UserController::class, 'view'])->name('paciente.view');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('paciente.update');
    Route::match(['get', 'post'], 'generatePdf/{id}', [UserController::class, 'generatePdf'])->name('paciente.generatePdf');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/paciente');
    })->name('dashboard')->middleware('can:admin');
});
