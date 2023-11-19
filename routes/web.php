<?php

use App\Http\Controllers\HomeController;
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

// Route::get('/', function () {
//     return view('login');
// });

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Bet
    Route::get('/summary-bet/filter-date', [HomeController::class, 'getBetSummaryByDate']);

    // Trans
    Route::get('/deposit', [HomeController::class, 'deposit']);
    Route::get('/deposit/data', [HomeController::class, 'getDepositData']);
    Route::get('/withdraw', [HomeController::class, 'withdraw']);
    Route::get('/withdraw/data', [HomeController::class, 'getWithdrawData']);
    Route::get('/expenses', [HomeController::class, 'expenses']);
    Route::get('/expenses/data', [HomeController::class, 'getExpensesData']);

    Route::post('/expenses/add', [HomeController::class, 'addExpenses']);
});
