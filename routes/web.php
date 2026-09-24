<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PortfolioController;

/*
|--------------------------------------------------------------------------
| Public Web Routes (Clean, Professional & SEO-Friendly)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/portofolio', [PageController::class, 'portofolio'])->name('portofolio');
Route::get('/layanan', [PageController::class, 'layanan'])->name('layanan');
Route::get('/cara-order', [PageController::class, 'caraOrder'])->name('cara-order');
Route::get('/tanya-jawab', [PageController::class, 'tanyaJawab'])->name('tanya-jawab');

// Legacy 301 Permanent Redirects for backward compatibility & SEO preservation
Route::redirect('/index.html', '/', 301);
Route::redirect('/portofolio.html', '/portofolio', 301);
Route::redirect('/layanan.html', '/layanan', 301);
Route::redirect('/cara-order.html', '/cara-order', 301);
Route::redirect('/tanya-jawab.html', '/tanya-jawab', 301);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Studio Management & Financial Routes (Inertia + Vue 3)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Admin shortcut
    Route::get('/admin', fn () => redirect()->route('dashboard'));

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Finance: Invoices
    Route::get('/finance/invoices', [FinanceController::class, 'invoicesIndex'])->name('finance.invoices');
    Route::post('/finance/invoices', [FinanceController::class, 'invoiceStore'])->name('finance.invoices.store');
    Route::put('/finance/invoices/{invoice}/status', [FinanceController::class, 'invoiceUpdateStatus'])->name('finance.invoices.update_status');
    Route::delete('/finance/invoices/{invoice}', [FinanceController::class, 'invoiceDestroy'])->name('finance.invoices.destroy');

    // 3. Finance: Expenses
    Route::get('/finance/expenses', [FinanceController::class, 'expensesIndex'])->name('finance.expenses');
    Route::post('/finance/expenses', [FinanceController::class, 'expenseStore'])->name('finance.expenses.store');
    Route::delete('/finance/expenses/{expense}', [FinanceController::class, 'expenseDestroy'])->name('finance.expenses.destroy');

    // 4. Finance: Cash Flow & Laba Rugi
    Route::get('/finance/cashflow', [FinanceController::class, 'cashflowIndex'])->name('finance.cashflow');

    // 5. Projects Management
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // 6. Clients CRM
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // 7. Portfolio Showcase CMS
    Route::get('/portfolio-manager', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio-manager', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::put('/portfolio-manager/{portfolio}', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('/portfolio-manager/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
});
