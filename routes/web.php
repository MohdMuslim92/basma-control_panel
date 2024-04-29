<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\StateController;
use \App\Http\Controllers\ProvinceController;
use \App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard Route for admin user
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Special Dashboard Route for role_id 11 normal user
Route::get('/user-dashboard', function () {
    return Inertia::render('UserDashboard');
})->name('user-dashboard');

// Route to fetch states
Route::get('/api/states', [StateController::class, 'index']);

// Route to fetch provinces by state ID
Route::get('/api/provinces/{state}', [ProvinceController::class, 'show']);

// Route for user list page
Route::get('/api/users', [UserController::class, 'showUserList'])->name('users.show');

// Route for updating user role
Route::put('/api/users/{id}', [UserController::class, 'updateRole']);

// Route for user list data
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Route for offices list data
Route::get('/api/offices', [OfficeController::class, 'index'])->name('offices');
