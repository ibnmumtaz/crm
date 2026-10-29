<?php

use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('leads', LeadController::class)->names('api.leads');
    Route::post('leads/{lead}/restore', [LeadController::class, 'restore'])->name('api.leads.restore');
    Route::post('leads/{lead}/reassign', [LeadController::class, 'reassign'])->name('api.leads.reassign');
    Route::post('leads/{lead}/stage', [LeadController::class, 'stage'])->name('api.leads.stage');
    Route::get('leads/{lead}/timeline', [LeadController::class, 'timeline'])->name('api.leads.timeline');

    Route::apiResource('tasks', TaskController::class)->names('api.tasks');
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('api.tasks.complete');
    Route::post('tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('api.tasks.cancel');
    Route::get('tasks/calendar', [TaskController::class, 'calendar'])->name('api.tasks.calendar');

    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('dashboard/pipeline-value', [DashboardController::class, 'pipelineValue']);
    Route::get('dashboard/win-rate', [DashboardController::class, 'winRate']);
    Route::get('dashboard/lead-source-performance', [DashboardController::class, 'leadSourcePerformance']);
    Route::get('dashboard/rep-leaderboard', [DashboardController::class, 'repLeaderboard']);
    Route::get('dashboard/target-progress', [DashboardController::class, 'targetProgress']);
    Route::get('dashboard/tasks-overdue', [DashboardController::class, 'tasksOverdue']);
});
