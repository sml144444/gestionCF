<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredStagiaireController;
use App\Http\Controllers\EduImportController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// GUEST
// ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
    Route::get('register',  [RegisteredStagiaireController::class, 'create'])->name('register');
    Route::post('register', [RegisteredStagiaireController::class, 'store'])->name('register.post');
});

// ─────────────────────────────────────────────
// LOGOUT
// ─────────────────────────────────────────────
Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// ─────────────────────────────────────────────
// REDIRECT BY ROLE
// ─────────────────────────────────────────────
Route::middleware('auth')->get('/redirect-by-role', function () {
    return match (Auth::user()->role) {
        'admin'        => redirect()->route('admin.dashboard'),
        'gestionnaire' => redirect()->route('gestionnaire.dashboard'),
        'formateur'    => redirect()->route('formateur.dashboard'),
        'stagiaire'    => redirect()->route('stagiaire.dashboard'),
        default        => redirect('/'),
    };
})->name('redirect.by.role');

// ─────────────────────────────────────────────
// DASHBOARDS
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:stagiaire'])->group(function () {
    Route::get('/stagiaire/dashboard', fn() => view('stagiaire.dashboard'))
        ->name('stagiaire.dashboard');
});

Route::middleware(['auth', 'role:formateur'])->group(function () {
    Route::get('/formateur/dashboard', fn() => view('formateur.dashboard'))
        ->name('formateur.dashboard');
});

Route::middleware(['auth', 'role:gestionnaire'])->group(function () {
    Route::get('/gestionnaire/dashboard', fn() => view('gestionnaire.dashboard'))
        ->name('gestionnaire.dashboard');
});

// ─────────────────────────────────────────────
// ADMIN
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
        ->name('admin.dashboard');

    Route::resource('roles', \App\Http\Controllers\RoleController::class);

    Route::prefix('users')->name('users.management.')->group(function () {
        Route::get('/',              [UserManagementController::class, 'index'])      ->name('index');
        Route::get('/create',        [UserManagementController::class, 'create'])     ->name('create');
        Route::post('/',             [UserManagementController::class, 'store'])      ->name('store');
        Route::get('/{user}/edit',   [UserManagementController::class, 'edit'])       ->name('edit');
        Route::put('/{user}',        [UserManagementController::class, 'update'])     ->name('update');
        Route::delete('/{user}',     [UserManagementController::class, 'destroy'])    ->name('destroy');
        Route::patch('/{user}/role', [UserManagementController::class, 'updateRole']) ->name('updateRole');
    });
});

// ─────────────────────────────────────────────
// EMPLOI DU TEMPS
// IMPORTANT: static sub-routes (available, pdf, publish)
// MUST be declared BEFORE the {emploi} wildcard routes.
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire,formateur,stagiaire'])->group(function () {

    // ── READ ────────────────────────────────────────────────
    Route::get('/emplois', [EmploiDuTempsController::class, 'index'])
        ->name('emplois.index');

    // ── Static sub-routes FIRST (before any {emploi} routes) ─
    Route::get('/emplois/available', [EmploiDuTempsController::class, 'available'])
        ->name('emplois.available');

    Route::get('/emplois/pdf', [EmploiDuTempsController::class, 'downloadPdf'])
        ->name('emplois.pdf');

    Route::post('/emplois/publish', [EmploiDuTempsController::class, 'publish'])
        ->name('emplois.publish');

    // ── CREATE ──────────────────────────────────────────────
    Route::post('/emplois', [EmploiDuTempsController::class, 'store'])
        ->name('emplois.store');

    // ── UPDATE / DELETE (wildcard routes last) ───────────────
    Route::put('/emplois/{emploi}', [EmploiDuTempsController::class, 'update'])
        ->name('emplois.update');

    Route::delete('/emplois/{emploi}', [EmploiDuTempsController::class, 'destroy'])
        ->name('emplois.destroy');

    Route::patch('/emplois/{emploi}/lien', [EmploiDuTempsController::class, 'updateLien'])
        ->name('emplois.updateLien');

    // ── REPLACEMENT ─────────────────────────────────────────
    Route::post('/emplois/{emploi}/remplacant', [EmploiDuTempsController::class, 'assignRemplacant'])
        ->name('emplois.remplacant');
});

// ─────────────────────────────────────────────
// EDU IMPORT
// ─────────────────────────────────────────────
// Dans le groupe edu-import, après les routes existantes
Route::middleware(['auth'])->group(function () {
    Route::get('/edu-import', [EduImportController::class, 'index'])
        ->name('edu-import.index')
        ->middleware('can:edu-view');

    Route::get('/edu-import/template', [EduImportController::class, 'downloadTemplate'])
        ->name('edu-import.template')
        ->middleware('can:edu-view');

    Route::post('/edu-import/preview', [EduImportController::class, 'preview'])
        ->name('edu-import.preview')
        ->middleware('can:edu-import');

    Route::post('/edu-import/confirm', [EduImportController::class, 'confirm'])
        ->name('edu-import.confirm')
        ->middleware('can:edu-import');

    Route::post('/edu-import/manual', [EduImportController::class, 'manualStore'])
        ->name('edu-import.manual')
        ->middleware('can:edu-import');

    // ⭐ NOUVELLES ROUTES À AJOUTER ⭐
    Route::get('/edu-import/{edu}/edit', [EduImportController::class, 'edit'])
        ->name('edu-import.edit')
        ->middleware('can:edu-import');

    Route::put('/edu-import/{edu}', [EduImportController::class, 'update'])
        ->name('edu-import.update')
        ->middleware('can:edu-import');

    Route::delete('/edu-import/{edu}', [EduImportController::class, 'destroy'])
        ->name('edu-import.destroy')
        ->middleware('can:edu-import');
});

// ─────────────────────────────────────────────
// REPORTATIONS
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire,formateur'])->group(function () {
    Route::post('/reportations', [\App\Http\Controllers\ReportationController::class, 'store'])
        ->name('reportations.store')
        ->middleware('can:reportation-create');

    Route::get('/reportations/mes', [\App\Http\Controllers\ReportationController::class, 'myIndex'])
        ->name('reportations.my')
        ->middleware('can:reportation-create');

    Route::get('/reportations', [\App\Http\Controllers\ReportationController::class, 'index'])
        ->name('reportations.index')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/accept', [\App\Http\Controllers\ReportationController::class, 'accept'])
        ->name('reportations.accept')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/refuse', [\App\Http\Controllers\ReportationController::class, 'refuse'])
        ->name('reportations.refuse')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/delete-session', [\App\Http\Controllers\ReportationController::class, 'deleteSession'])
        ->name('reportations.delete-session')
        ->middleware('can:reportation-manage');
});

// ─────────────────────────────────────────────
// FILIÈRES + GROUPES
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/filieres', [FiliereController::class, 'index'])
        ->name('filieres.index')->middleware('can:groupe-list');
    Route::post('/filieres', [FiliereController::class, 'store'])
        ->name('filieres.store')->middleware('can:groupe-create');
    Route::patch('/filieres/{filiere}', [FiliereController::class, 'update'])
        ->name('filieres.update')->middleware('can:groupe-edit');
    Route::delete('/filieres/{filiere}', [FiliereController::class, 'destroy'])
        ->name('filieres.destroy')->middleware('can:groupe-delete');

    Route::get('/groupes', [GroupeController::class, 'index'])
        ->name('groupes.index')->middleware('can:groupe-list');
    Route::post('/groupes', [GroupeController::class, 'store'])
        ->name('groupes.store')->middleware('can:groupe-create');
    Route::patch('/groupes/{groupe}', [GroupeController::class, 'update'])
        ->name('groupes.update')->middleware('can:groupe-edit');
    Route::delete('/groupes/{groupe}', [GroupeController::class, 'destroy'])
        ->name('groupes.destroy')->middleware('can:groupe-delete');
});

// ─────────────────────────────────────────────
// MODULES
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/modules', [ModuleController::class, 'index'])
        ->name('modules.index')->middleware('can:groupe-list');
    Route::post('/modules', [ModuleController::class, 'store'])
        ->name('modules.store')->middleware('can:groupe-create');
    Route::patch('/modules/{module}', [ModuleController::class, 'update'])
        ->name('modules.update')->middleware('can:groupe-edit');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])
        ->name('modules.destroy')->middleware('can:groupe-delete');
});

// ─────────────────────────────────────────────
// SALLES
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/salles', [SalleController::class, 'index'])
        ->name('salles.index')->middleware('can:salle-list');
    Route::post('/salles', [SalleController::class, 'store'])
        ->name('salles.store')->middleware('can:salle-create');
    Route::patch('/salles/{salle}', [SalleController::class, 'update'])
        ->name('salles.update')->middleware('can:salle-edit');
    Route::delete('/salles/{salle}', [SalleController::class, 'destroy'])
        ->name('salles.destroy')->middleware('can:salle-delete');
});

// ─────────────────────────────────────────────
// STAGIAIRES
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire'])->group(function () {
    Route::get('/stagiaire', [\App\Http\Controllers\StagiaireController::class, 'index'])
        ->name('stagiaire.index')->middleware('can:stagiaire-list');
    Route::post('/stagiaire', [\App\Http\Controllers\StagiaireController::class, 'store'])
        ->name('stagiaire.store')->middleware('can:stagiaire-create');
    Route::put('/stagiaire/{stagiaire}', [\App\Http\Controllers\StagiaireController::class, 'update'])
        ->name('stagiaire.update')->middleware('can:stagiaire-edit');
    Route::delete('/stagiaire/{stagiaire}', [\App\Http\Controllers\StagiaireController::class, 'destroy'])
        ->name('stagiaire.destroy')->middleware('can:stagiaire-delete');
});