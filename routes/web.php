<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredStagiaireController;
use App\Http\Controllers\EduImportController;
use App\Http\Controllers\EmploiDuTempsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// GUEST routes
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
// ADMIN — dashboard + roles + user management
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
        ->name('admin.dashboard');

    Route::resource('roles', \App\Http\Controllers\RoleController::class);

    Route::get('/users/management', [\App\Http\Controllers\UserManagementController::class, 'index'])
        ->name('users.management.index');

    Route::patch('/users/{user}/role', [\App\Http\Controllers\UserManagementController::class, 'updateRole'])
        ->name('users.management.updateRole');
});

// ─────────────────────────────────────────────
// EMPLOI DU TEMPS
// Permission-based — all roles that have
// emploi-view can access the index.
// emploi-create / edit / delete control buttons.
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire,formateur,stagiaire'])->group(function () {

    Route::get('/emplois', [EmploiDuTempsController::class, 'index'])
        ->name('emplois.index');

    Route::get('/emplois/available', [EmploiDuTempsController::class, 'available'])
        ->name('emplois.available');

    Route::post('/emplois', [EmploiDuTempsController::class, 'store'])
        ->name('emplois.store');

    Route::put('/emplois/{emploi}', [EmploiDuTempsController::class, 'update'])
        ->name('emplois.update');

    Route::delete('/emplois/{emploi}', [EmploiDuTempsController::class, 'destroy'])
        ->name('emplois.destroy');

    Route::patch('/emplois/{emploi}/lien', [EmploiDuTempsController::class, 'updateLien'])
        ->name('emplois.updateLien');

    Route::get('/emplois/pdf', [EmploiDuTempsController::class, 'downloadPdf'])
        ->name('emplois.pdf');

    Route::post('/emplois/publish', [EmploiDuTempsController::class, 'publish'])
    ->name('emplois.publish');
});

// ─────────────────────────────────────────────
// EDU IMPORT
// Permission-based — any role with edu-view
// or edu-import can access these routes.
// The role:gestionnaire,admin guard is removed
// so custom roles with edu permissions work too.
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // View the import page + download template = edu-view
    Route::get('/edu-import', [EduImportController::class, 'index'])
        ->name('edu-import.index')
        ->middleware('can:edu-view');

    Route::get('/edu-import/template', [EduImportController::class, 'downloadTemplate'])
        ->name('edu-import.template')
        ->middleware('can:edu-view');

    // Actually importing data = edu-import
    Route::post('/edu-import/preview', [EduImportController::class, 'preview'])
        ->name('edu-import.preview')
        ->middleware('can:edu-import');

    Route::post('/edu-import/confirm', [EduImportController::class, 'confirm'])
        ->name('edu-import.confirm')
        ->middleware('can:edu-import');

    Route::post('/edu-import/manual', [EduImportController::class, 'manualStore'])
        ->name('edu-import.manual')
        ->middleware('can:edu-import');
});


// ─────────────────────────────────────────────
// STAGIAIRES — liste par filière/groupe/option
// Permission : stagiaire-list (admin + gestionnaire)
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire'])->group(function () {
    Route::get('/stagiaire', [\App\Http\Controllers\StagiaireController::class, 'index'])
        ->name('stagiaire.index')
        ->middleware('can:stagiaire-list');
});



Route::middleware(['auth', 'role:admin,gestionnaire,formateur'])->group(function () {
 
    // Formateur: submit a request
    Route::post('/reportations', [\App\Http\Controllers\ReportationController::class, 'store'])
        ->name('reportations.store')
        ->middleware('can:reportation-create');
 
    // Formateur: view their OWN requests (separate route = no 403)
    Route::get('/reportations/mes', [\App\Http\Controllers\ReportationController::class, 'myIndex'])
        ->name('reportations.my')
        ->middleware('can:reportation-create');
 
    // Admin/gestionnaire: view ALL requests
    Route::get('/reportations', [\App\Http\Controllers\ReportationController::class, 'index'])
        ->name('reportations.index')
        ->middleware('can:reportation-manage');
 
    // Admin picks new date and accepts
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
 


use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GroupeController;

// Filières — list/create/edit/delete sur la même page (pas de show séparé)
Route::middleware(['auth'])->group(function () {

    Route::get('/filieres', [FiliereController::class, 'index'])
        ->name('filieres.index')
        ->middleware('can:groupe-list');

    Route::post('/filieres', [FiliereController::class, 'store'])
        ->name('filieres.store')
        ->middleware('can:groupe-create');

    Route::patch('/filieres/{filiere}', [FiliereController::class, 'update'])
        ->name('filieres.update')
        ->middleware('can:groupe-edit');

    Route::delete('/filieres/{filiere}', [FiliereController::class, 'destroy'])
        ->name('filieres.destroy')
        ->middleware('can:groupe-delete');

    // Groupes
    Route::get('/groupes', [GroupeController::class, 'index'])
        ->name('groupes.index')
        ->middleware('can:groupe-list');

    Route::post('/groupes', [GroupeController::class, 'store'])
        ->name('groupes.store')
        ->middleware('can:groupe-create');

    Route::patch('/groupes/{groupe}', [GroupeController::class, 'update'])
        ->name('groupes.update')
        ->middleware('can:groupe-edit');

    Route::delete('/groupes/{groupe}', [GroupeController::class, 'destroy'])
        ->name('groupes.destroy')
        ->middleware('can:groupe-delete');
});

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SalleController;

Route::middleware(['auth'])->group(function () {
    Route::get('/modules', [ModuleController::class, 'index'])
        ->name('modules.index')
        ->middleware('can:groupe-list');

    Route::post('/modules', [ModuleController::class, 'store'])
        ->name('modules.store')
        ->middleware('can:groupe-create');

    Route::patch('/modules/{module}', [ModuleController::class, 'update'])
        ->name('modules.update')
        ->middleware('can:groupe-edit');

    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])
        ->name('modules.destroy')
        ->middleware('can:groupe-delete');
});


Route::middleware(['auth'])->group(function () {
 
    Route::get('/salles', [SalleController::class, 'index'])
        ->name('salles.index')
        ->middleware('can:salle-list');
 
    Route::post('/salles', [SalleController::class, 'store'])
        ->name('salles.store')
        ->middleware('can:salle-create');
 
    Route::patch('/salles/{salle}', [SalleController::class, 'update'])
        ->name('salles.update')
        ->middleware('can:salle-edit');
 
    Route::delete('/salles/{salle}', [SalleController::class, 'destroy'])
        ->name('salles.destroy')
        ->middleware('can:salle-delete');
});