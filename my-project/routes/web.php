<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index'); // отображение списка задач
    Route::post('/tasks', [App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');; // создание новой задачи
    Route::put('/tasks/{id}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update'); // обновление информации о задаче
    Route::delete('/tasks/{id}', [App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy'); // удаление задачи


});

require __DIR__.'/auth.php';
