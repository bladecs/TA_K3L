<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmergencyContactController;
use App\Http\Controllers\Admin\EmergencyResponseStepController;
use App\Http\Controllers\Admin\FirstAidGuideController;
use App\Http\Controllers\Admin\IncidentReportController as AdminIncidentReportController;
use App\Http\Controllers\Admin\IncidentCategoryController;
use App\Http\Controllers\Admin\KnowledgeArticleController;
use App\Http\Controllers\Admin\KnowledgeCategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PotentialHazardReportController as AdminPotentialHazardReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Satgas\DashboardController as SatgasDashboardController;
use App\Http\Controllers\Satgas\IncidentReportController as SatgasIncidentReportController;
use App\Http\Controllers\Satgas\IncidentReviewController;
use App\Http\Controllers\Satgas\KnowledgeArticleController as SatgasKnowledgeArticleController;
use App\Http\Controllers\Satgas\PotentialHazardReportController as SatgasPotentialHazardReportController;
use App\Http\Controllers\Satgas\PotentialHazardReviewController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\EmergencyCenterController;
use App\Http\Controllers\User\IncidentReportController;
use App\Http\Controllers\User\KnowledgeCenterController;
use App\Http\Controllers\User\KnowledgeModuleController;
use App\Http\Controllers\User\ActivityLogController;
use App\Http\Controllers\User\PotentialHazardReportController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        /** @var \App\Models\User $user */
        $user = auth()->user()->loadMissing('role');

        return redirect()->route($user->dashboardRouteName());
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'active.user', 'role:mahasiswa'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    Route::get('/activities', ActivityLogController::class)->name('activities.index');
    Route::patch('/activities/read-all', [ActivityLogController::class, 'markAllRead'])->name('activities.read-all');
    Route::patch('/activities/{activityLog}/read', [ActivityLogController::class, 'markRead'])->name('activities.read');
    Route::get('/emergency-center', EmergencyCenterController::class)->name('emergency.index');
    Route::get('/knowledge-center', KnowledgeCenterController::class)->name('knowledge.index');
    Route::get('/knowledge-center/module/{slug}', KnowledgeModuleController::class)->name('knowledge.show');
    Route::get('/profile', ProfileController::class)->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/hazard-reports/create', PotentialHazardReportController::class)->name('hazards.create');
    Route::get('/hazard-reports', [PotentialHazardReportController::class, 'index'])->name('hazards.index');
    Route::get('/hazard-reports/{potentialHazardReport}', [PotentialHazardReportController::class, 'show'])->name('hazards.show');
    Route::post('/hazard-reports', [PotentialHazardReportController::class, 'store'])->name('hazards.store');

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/status', [IncidentReportController::class, 'status'])->name('status');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])->name('store');
        Route::get('/{incidentReport}', [IncidentReportController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'active.user', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('incidents/create', [AdminIncidentReportController::class, 'create'])->name('incidents.create');
    Route::post('incidents', [AdminIncidentReportController::class, 'store'])->name('incidents.store');
    Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('incident-categories', IncidentCategoryController::class)->except(['show']);
    Route::resource('knowledge-categories', KnowledgeCategoryController::class)->except(['show']);
    Route::resource('knowledge-articles', KnowledgeArticleController::class)->except(['show']);
    Route::resource('emergency-contacts', EmergencyContactController::class)->except(['show']);
    Route::resource('emergency-response-steps', EmergencyResponseStepController::class)->except(['show']);
    Route::resource('first-aid-guides', FirstAidGuideController::class)->except(['show']);
    Route::get('hazards/create', [AdminPotentialHazardReportController::class, 'create'])->name('hazards.create');
    Route::post('hazards', [AdminPotentialHazardReportController::class, 'store'])->name('hazards.store');
    Route::get('hazards', [AdminPotentialHazardReportController::class, 'index'])->name('hazards.index');
    Route::get('hazards/{potentialHazardReport}', [AdminPotentialHazardReportController::class, 'show'])->name('hazards.show');
});

Route::middleware(['auth', 'active.user', 'role:satgas'])->prefix('satgas')->name('satgas.')->group(function () {
    Route::get('/dashboard', SatgasDashboardController::class)->name('dashboard');
    Route::resource('knowledge-articles', SatgasKnowledgeArticleController::class)->except(['show']);

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/create', [SatgasIncidentReportController::class, 'create'])->name('create');
        Route::post('/', [SatgasIncidentReportController::class, 'store'])->name('store');
        Route::get('/', [IncidentReviewController::class, 'index'])->name('index');
        Route::get('/{incidentReport}', [IncidentReviewController::class, 'show'])->name('show');
        Route::patch('/{incidentReport}/verify', [IncidentReviewController::class, 'verify'])->name('verify');
        Route::patch('/{incidentReport}/status', [IncidentReviewController::class, 'updateStatus'])->name('update-status');
        Route::post('/{incidentReport}/follow-ups', [IncidentReviewController::class, 'storeFollowUp'])->name('follow-ups.store');
    });

    Route::prefix('hazards')->name('hazards.')->group(function () {
        Route::get('/create', [SatgasPotentialHazardReportController::class, 'create'])->name('create');
        Route::post('/', [SatgasPotentialHazardReportController::class, 'store'])->name('store');
        Route::get('/', [PotentialHazardReviewController::class, 'index'])->name('index');
        Route::get('/{potentialHazardReport}', [PotentialHazardReviewController::class, 'show'])->name('show');
        Route::patch('/{potentialHazardReport}/status', [PotentialHazardReviewController::class, 'updateStatus'])->name('update-status');
    });
});
