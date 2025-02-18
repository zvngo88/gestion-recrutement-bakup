<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\StepController;
use App\Models\Post;
use App\Models\Step;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterviewController;

Route::get('/', function () {
    return view('welcome');
});




Route::resource('posts', PostController::class);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::patch('/posts/{post}/toggle-status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');
// Afficher la liste des candidats
Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');

// Afficher le formulaire de création d'un candidat
Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');

// Sauvegarder un nouveau candidat
Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');

// Afficher le formulaire de modification d'un candidat
Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');

// Mettre à jour un candidat
Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');

// Supprimer un candidat
Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');


Route::prefix('posts/{post}/steps')->group(function () {
    Route::get('/', [StepController::class, 'index'])->name('steps.index');
    Route::post('/', [StepController::class, 'store'])->name('steps.store');
    Route::patch('/{stepId}', [StepController::class, 'update'])->name('steps.update');
});

Route::post('/assignments/store', [CandidateController::class, 'storeAssignment'])->name('assignments.store');



Route::put('candidates/{candidate}/steps', [CandidateController::class, 'updateSteps'])->name('candidates.updateSteps');

Route::put('candidates/{id}/steps', [CandidateController::class, 'updateSteps'])->name('candidates.steps.update');
Route::post('/candidates/{id}/upload-cv', [CandidateController::class, 'uploadCv'])->name('candidates.uploadCv');


Route::get('/api/posts/search', function (Illuminate\Http\Request $request) {
    $search = $request->get('q', ''); // Terme de recherche
    $posts = Post::query()
        ->where('title', 'like', '%' . $search . '%') // Rechercher par titre
        ->take(10) // Limitez les résultats
        ->get(['id', 'title']); // Récupérez uniquement les colonnes nécessaires

    return response()->json($posts);
});

Route::get('/candidates/{candidate}/assign', [CandidateController::class, 'assign'])->name('candidates.assign');
Route::post('/candidates/{candidate}/assign', [CandidateController::class, 'storeAssignment'])->name('candidates.storeAssignment');
Route::get('/posts/{post}/candidates/{candidate}/steps', [StepController::class, 'show'])->name('steps.show');
Route::patch('/posts/{post}/candidates/{candidate}/steps/{step}', [StepController::class, 'update'])->name('steps.update');

Route::resource('candidates', CandidateController::class);
Route::get('/candidates/{candidate}/assign', [CandidateController::class, 'assign'])->name('candidates.assign');
Route::post('/candidates/{candidate}/assign', [CandidateController::class, 'storeAssignment'])->name('candidates.storeAssignment');
Route::get('/assignments/{assignment}', [CandidateController::class, 'track'])->name('assignments.track');

Route::get('/candidates/{candidate}/track', [CandidateController::class, 'track'])->name('candidates.track');
Route::post('/candidates/{candidate}/track', [CandidateController::class, 'storeTracking'])->name('candidates.storeTracking');
Route::patch('/posts/{post}/steps/{step}', [StepController::class, 'update'])->name('steps.update');


Route::resource('interviews', InterviewController::class);
Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
Route::get('/interviews/{id}/email', [InterviewController::class, 'sendOutlookEmail'])->name('interviews.email');


Route::resource('clients', ClientController::class);
Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
Route::get('/posts-by-client/{clientId}', function ($clientId) {
    $posts = Post::where('client_id', $clientId)->get();
    return response()->json($posts);
});
Route::get('/candidates-by-post/{postId}', [InterviewController::class, 'getCandidatesByPost']);

