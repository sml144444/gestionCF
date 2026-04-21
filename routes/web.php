<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredStagiaireController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\EduImportController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\ReportationController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NewsEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReclamationController;
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

    // Password reset
    Route::get('forgot-password',        [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password',       [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password',        [NewPasswordController::class, 'store'])->name('password.store');
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
// ADMIN ONLY
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
        ->name('admin.dashboard');

    // Roles management — admin only
    Route::resource('roles', RoleController::class);
});

// ─────────────────────────────────────────────
// USER MANAGEMENT — admin + gestionnaire
// NOTE: constructor middleware in the controller
//       handles fine-grained permission checks.
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,gestionnaire'])->group(function () {
    Route::prefix('users')->name('users.management.')->group(function () {
        Route::get('/',              [UserManagementController::class, 'index'])      ->name('index');
        Route::get('/create',        [UserManagementController::class, 'create'])     ->name('create');
        Route::post('/',             [UserManagementController::class, 'store'])      ->name('store');
        Route::get('/{user}/edit',   [UserManagementController::class, 'edit'])       ->name('edit');
        Route::put('/{user}',        [UserManagementController::class, 'update'])     ->name('update');
        Route::patch('/{user}/role', [UserManagementController::class, 'updateRole']) ->name('updateRole');

        // Delete stays admin-only
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:admin');
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
// SÉANCES
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/seances/{emploi}',                       [SeanceController::class, 'show'])->name('seances.show');
    Route::post('/seances/{emploi}/presence',             [SeanceController::class, 'savePresence'])->name('seances.presence');
    Route::post('/seances/{emploi}/ressources',           [SeanceController::class, 'addRessource'])->name('seances.ressource.store');
    Route::delete('/seances/{emploi}/ressources/{cours}', [SeanceController::class, 'deleteRessource'])->name('seances.ressource.destroy');
});

// ─────────────────────────────────────────────
// EDU IMPORT
// ─────────────────────────────────────────────
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
    Route::post('/reportations', [ReportationController::class, 'store'])
        ->name('reportations.store')
        ->middleware('can:reportation-create');

    Route::get('/reportations/mes', [ReportationController::class, 'myIndex'])
        ->name('reportations.my')
        ->middleware('can:reportation-create');

    Route::get('/reportations', [ReportationController::class, 'index'])
        ->name('reportations.index')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/accept', [ReportationController::class, 'accept'])
        ->name('reportations.accept')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/refuse', [ReportationController::class, 'refuse'])
        ->name('reportations.refuse')
        ->middleware('can:reportation-manage');

    Route::post('/reportations/{reportation}/delete-session', [ReportationController::class, 'deleteSession'])
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
    Route::get('/stagiaire', [StagiaireController::class, 'index'])
        ->name('stagiaire.index')->middleware('can:stagiaire-list');
    Route::post('/stagiaire', [StagiaireController::class, 'store'])
        ->name('stagiaire.store')->middleware('can:stagiaire-create');
    Route::put('/stagiaire/{stagiaire}', [StagiaireController::class, 'update'])
        ->name('stagiaire.update')->middleware('can:stagiaire-edit');
    Route::delete('/stagiaire/{stagiaire}', [StagiaireController::class, 'destroy'])
        ->name('stagiaire.destroy')->middleware('can:stagiaire-delete');
});

// ─────────────────────────────────────────────
// RÉCLAMATIONS
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/reclamations', [ReclamationController::class, 'index'])
        ->name('reclamations.index');

    Route::get('/reclamations/create', [ReclamationController::class, 'create'])
        ->name('reclamations.create')
        ->middleware('can:reclamation-create');

    Route::post('/reclamations', [ReclamationController::class, 'store'])
        ->name('reclamations.store')
        ->middleware('can:reclamation-create');

    Route::patch('/reclamations/{reclamation}/status', [ReclamationController::class, 'updateStatus'])
        ->name('reclamations.status')
        ->middleware('can:reclamation-manage');

    Route::delete('/reclamations/{reclamation}', [ReclamationController::class, 'destroy'])
        ->name('reclamations.destroy')
        ->middleware('can:reclamation-manage');
});

// ─────────────────────────────────────────────
// NEWS & ÉVÉNEMENTS
// ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/news', [NewsEventController::class, 'index'])
        ->name('news.index')
        ->middleware('can:news-list');

    Route::get('/news/create', [NewsEventController::class, 'create'])
        ->name('news.create')
        ->middleware('can:news-create');

    Route::post('/news', [NewsEventController::class, 'store'])
        ->name('news.store')
        ->middleware('can:news-create');

    Route::get('/news/{news}', [NewsEventController::class, 'show'])
        ->name('news.show')
        ->middleware('can:news-list');

    Route::get('/news/{news}/edit', [NewsEventController::class, 'edit'])
        ->name('news.edit')
        ->middleware('can:news-edit');

    Route::put('/news/{news}', [NewsEventController::class, 'update'])
        ->name('news.update')
        ->middleware('can:news-edit');

    Route::delete('/news/{news}', [NewsEventController::class, 'destroy'])
        ->name('news.destroy')
        ->middleware('can:news-delete');

    // Commentaires
    Route::post('/news/{news}/comments', [NewsEventController::class, 'storeComment'])
        ->name('news.comments.store')
        ->middleware('can:news-comment');

    Route::delete('/news/{news}/comments/{comment}', [NewsEventController::class, 'destroyComment'])
        ->name('news.comments.destroy')
        ->middleware('can:news-list');

    // Likes (AJAX POST, retourne JSON)
    Route::post('/news/{news}/like', [NewsEventController::class, 'toggleLike'])
        ->name('news.like')
        ->middleware('can:news-like');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/absences', [AbsenceController::class, 'index'])
        ->name('absences.index')
        ->middleware('can:absence-view');

    Route::patch('/absences/{absence}/justification', [AbsenceController::class, 'toggleJustification'])
        ->name('absences.justify')
        ->middleware('can:absence-justify');

    Route::post('/absences/{absence}/fichier', [AbsenceController::class, 'uploadFichier'])
        ->name('absences.fichier')
        ->middleware('can:absence-justify');

    Route::delete('/absences/{absence}/fichier', [AbsenceController::class, 'deleteFichier'])
        ->name('absences.fichier.delete')
        ->middleware('can:absence-justify');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile',          [ProfileController::class, 'show'])          ->name('profile.show');
    Route::put('/profile',          [ProfileController::class, 'update'])        ->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo',   [ProfileController::class, 'updatePhoto'])   ->name('profile.photo');
});
 