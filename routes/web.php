<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\BorrowerController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tools', function () {
    return Inertia::render('Admin/Tools');
})->middleware(['auth', 'verified'])->name('tools');

Route::get('/transactions', function () {
    return Inertia::render('Admin/Transactions');
})->middleware(['auth', 'verified'])->name('transactions');

Route::get('/borrowers', function () {
    return Inertia::render('Admin/Borrowers');
})->middleware(['auth', 'verified'])->name('borrowers');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->group(function () {
    Route::post('/tools/store', [ToolController::class, 'store']);
    Route::get('/tools', [ToolController::class, 'index']);
    Route::get('/tools/{id}', [ToolController::class, 'show']);
    Route::put('/tools/{id}', [ToolController::class, 'update']);
    Route::delete('/tools/{id}', [ToolController::class, 'destroy']);
    
    Route::post('/borrowers/store', [BorrowerController::class, 'storeBorrower']);
    Route::get('/borrowers', [BorrowerController::class, 'indexBorrower']);
    Route::get('/borrowers/{id}', [BorrowerController::class, 'showBorrower']);
    Route::put('/borrowers/{id}', [BorrowerController::class, 'updateBorrower']);
    Route::delete('/borrowers/{id}', [BorrowerController::class, 'destroyBorrower']);

});
require __DIR__ . '/auth.php';
