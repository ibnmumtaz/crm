<?php

use App\Http\Controllers\Web\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\CommunicationController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\MarketingController;
use App\Http\Controllers\Web\PipelineController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\TargetController;
use App\Http\Controllers\Web\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/features', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('/solutions', [MarketingController::class, 'solutions'])->name('marketing.solutions');
Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('marketing.how');
Route::get('/about', [MarketingController::class, 'about'])->name('marketing.about');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('marketing.terms');
Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::get('/book', [BookingController::class, 'index'])->name('bookings.index');
Route::get('/book/{meetingType}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/book', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle:10,1');
Route::get('/book/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
Route::post('/book/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::post('/contact', [MarketingController::class, 'submitContact'])->name('marketing.contact.submit')->middleware('throttle:5,1');

Route::middleware(['auth'])->group(function () {
    Route::get('/app', function () {
        return redirect()->route('dashboard');
    })->name('app');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('leads/kanban', [LeadController::class, 'kanban'])->name('leads.kanban');
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('leads/{lead}/stage', [LeadController::class, 'stage'])->name('leads.stage');
    Route::resource('leads', LeadController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');
    Route::resource('communications', CommunicationController::class);
    Route::resource('targets', TargetController::class);
    Route::resource('pipelines', PipelineController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::resource('teams', AdminTeamController::class);
    Route::get('bookings', [BookingController::class, 'adminIndex'])->name('bookings.admin');
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
});
