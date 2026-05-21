<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CampusRoomController;
use App\Http\Controllers\Admin\EmergencyContactController;
use App\Http\Controllers\Admin\EmergencyResponseStepController;
use App\Http\Controllers\Admin\FirstAidGuideController;
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
use App\Http\Controllers\Satgas\ProfileController as SatgasProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\EmergencyCenterController;
use App\Http\Controllers\User\IncidentReportController;
use App\Http\Controllers\User\KnowledgeCenterController;
use App\Http\Controllers\User\KnowledgeModuleController;
use App\Http\Controllers\User\PotentialHazardReportController;
use App\Http\Controllers\User\ActivityLogController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('user.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboardController::class)->middleware('active.user')->name('dashboard');
    Route::get('/emergency-center', EmergencyCenterController::class)->name('emergency.index');
    Route::get('/knowledge-center', KnowledgeCenterController::class)->name('knowledge.index');
    Route::get('/knowledge-center/module/{slug}', KnowledgeModuleController::class)->name('knowledge.show');
    Route::get('/hazard-map', [PotentialHazardReportController::class, 'map'])->name('hazards.map');
    Route::get('/hazard-reports', [PotentialHazardReportController::class, 'index'])->middleware(['auth', 'active.user'])->name('hazards.index');
    Route::get('/hazard-reports/create', PotentialHazardReportController::class)->name('hazards.create');
    Route::post('/hazard-reports', [PotentialHazardReportController::class, 'store'])->name('hazards.store');
    Route::get('/hazard-reports/{potentialHazardReport}', [PotentialHazardReportController::class, 'show'])->middleware(['auth', 'active.user'])->name('hazards.show');
    Route::get('/profile', ProfileController::class)->middleware(['auth', 'active.user'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->middleware(['auth', 'active.user'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware(['auth', 'active.user'])->name('profile.update');
    Route::get('/activities', ActivityLogController::class)->middleware(['auth', 'active.user'])->name('activities.index');
    Route::patch('/activities/{activityLog}/read', [ActivityLogController::class, 'markRead'])->middleware(['auth', 'active.user'])->name('activities.read');
    Route::patch('/activities/read-all', [ActivityLogController::class, 'markAllRead'])->middleware(['auth', 'active.user'])->name('activities.read-all');

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'index'])->middleware(['auth', 'active.user'])->name('index');
        Route::get('/status', [IncidentReportController::class, 'status'])->name('status');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])->name('store');
        Route::get('/{incidentReport}', [IncidentReportController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'active.user', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
    Route::resource('emergency-contacts', EmergencyContactController::class)->except(['show']);
    Route::resource('emergency-response-steps', EmergencyResponseStepController::class)->except(['show']);
    Route::resource('first-aid-guides', FirstAidGuideController::class)->except(['show']);
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('campus-rooms', CampusRoomController::class)->except(['show']);
    Route::resource('incident-categories', IncidentCategoryController::class)->except(['show']);
    Route::resource('knowledge-categories', KnowledgeCategoryController::class)->except(['show']);
    Route::resource('knowledge-articles', KnowledgeArticleController::class)->except(['show']);
    Route::get('hazards', [AdminPotentialHazardReportController::class, 'index'])->name('hazards.index');
    Route::get('hazards/{potentialHazardReport}', [AdminPotentialHazardReportController::class, 'show'])->name('hazards.show');
});

Route::middleware(['auth', 'active.user', 'role:satgas'])->prefix('satgas')->name('satgas.')->group(function () {
    Route::get('/dashboard', SatgasDashboardController::class)->name('dashboard');
    Route::get('/profile', [SatgasProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [SatgasProfileController::class, 'update'])->name('profile.update');
    Route::resource('knowledge-articles', SatgasKnowledgeArticleController::class)->except(['show']);

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/create', [SatgasIncidentReportController::class, 'create'])->name('create');
        Route::post('/', [SatgasIncidentReportController::class, 'store'])->name('store');
        Route::get('/', [IncidentReviewController::class, 'index'])->name('index');
        Route::get('/gis', [IncidentReviewController::class, 'gis'])->name('gis');
        Route::get('/gis/export', [IncidentReviewController::class, 'exportGis'])->name('gis.export');
        Route::get('/{incidentReport}', [IncidentReviewController::class, 'show'])->name('show');
        Route::patch('/{incidentReport}/verify', [IncidentReviewController::class, 'verify'])->name('verify');
        Route::patch('/{incidentReport}/status', [IncidentReviewController::class, 'updateStatus'])->name('update-status');
        Route::post('/{incidentReport}/follow-ups', [IncidentReviewController::class, 'storeFollowUp'])->name('follow-ups.store');
    });

    Route::prefix('hazards')->name('hazards.')->group(function () {
        Route::get('/create', [SatgasPotentialHazardReportController::class, 'create'])->name('create');
        Route::post('/', [SatgasPotentialHazardReportController::class, 'store'])->name('store');
        Route::get('/map', [PotentialHazardReviewController::class, 'map'])->name('map');
        Route::post('/map-points', [PotentialHazardReviewController::class, 'storeMapPoint'])->name('map-points.store');
        Route::get('/', [PotentialHazardReviewController::class, 'index'])->name('index');
        Route::get('/{potentialHazardReport}', [PotentialHazardReviewController::class, 'show'])->name('show');
        Route::patch('/{potentialHazardReport}/pinpoint', [PotentialHazardReviewController::class, 'updatePinpoint'])->name('update-pinpoint');
        Route::patch('/{potentialHazardReport}/status', [PotentialHazardReviewController::class, 'updateStatus'])->name('update-status');
    });
});
