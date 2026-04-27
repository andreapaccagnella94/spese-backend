<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response('OK', 200);
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('accounts.index');
})->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotte per le spese
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('/expenses/ai', [ExpenseController::class, 'storeWithAI'])->name('expenses.store.ai');

    // Rotte per gli accrediti
    Route::get('/credits/create', [CreditController::class, 'create'])->name('credits.create');
    Route::post('/credits', [CreditController::class, 'store'])->name('credits.store');
    Route::post('/credits/ai', [CreditController::class, 'storeWithAI'])->name('credits.store.ai');
});

// Rotte CRUD per accounts
Route::resource('accounts', AccountController::class)
    ->middleware(['auth', 'verified']);

// Rotte CRUD per categories
Route::resource('categories', CategoryController::class)
    ->middleware(['auth', 'verified']);

require __DIR__ . '/auth.php';
