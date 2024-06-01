<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\StateController;
use \App\Http\Controllers\ProvinceController;
use \App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserSharesAndPaymentsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RemovedUsers;

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

// Public Routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Routes that require authentication
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard Route for admin user
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Special Dashboard Route for role_id 11 normal user
    Route::get('/user-dashboard', function () {
        return Inertia::render('UserDashboard');
    })->name('user-dashboard');

    // Route for user list page with pagniation
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Route for fetching all users together for saving as pdf purpose
    Route::get('/api/all-users', [UserController::class, 'getAllUsers']);

    // Route for displaying removed users
    Route::get('/removed-users', [RemovedUsers::class, 'index']);

    // Route for fetching removed users
    Route::get('/api/removed-users', [RemovedUsers::class, 'getRemovedUsers']);

    // routes/web.php or routes/api.php
    Route::get('/api/removed-users/all', [RemovedUsers::class, 'getAllRemovedUsers']);

    // Route to retrieve a user
    Route::post('/api/retrieve-user/{id}', [RemovedUsers::class, 'retrieveUser']);    // Route for fetching users related to the current logged in admin
    
    // Route for fetching users related to the current logged in admin
    Route::get('/api/users/byAdmin', [UserController::class, 'getUsersByAdmin'])->name('userByAdmin');

    // Route for display the Monthly Share page that contains the users list under the current admin
    Route::get('/api/user-shares-and-payment', [UserSharesAndPaymentsController::class, 'index'])->name('UserSharesAndPayments');

    // Route for displaying the new registered user for approval
    Route::get('/user/approval/{userId}', [UserController::class, 'approval'])->name('user.approval');

    // Route for fetching the notifications
    Route::get('/api/notifications', [NotificationController::class, 'getUserNotifications']);

    // Route for updating the read status for the notification
    Route::put('/api/mark-as-read/{notificationId}', [NotificationController::class, 'markAsRead'])->name('markAsRead');

    // Route to update the user status and approve it, also update the user data if needed
    Route::put('/api/approve-user/{id}', [UserController::class, 'approveUser'])->name('approveUser');

    // Route to update the user status and reject it
    Route::put('/api/reject-user/{id}', [UserController::class, 'rejectUser'])->name('rejectUser');

    // Route to delete a user by changing its user_status_id
    Route::delete('/api/users/{id}', [UserController::class, 'deleteUser'])->name('users.delete');

    // Route for Admins list page
    Route::get('/api/admins', [AdminController::class, 'showAdminsList'])->name('admins.show');

    // Route to fetch Offices
    Route::get('/api/offices', [OfficeController::class, 'index'])->name('offices');

    // Route for membership officers list data
    Route::get('/api/membership-officers', [AdminController::class, 'index'])->name('officers');

    // Route to update user data
    Route::put('/api/user/{userId}', [UserController::class, 'update']);

    // Route for updating user role
    Route::put('/api/users/{id}', [UserController::class, 'updateRole']);

    // Route for updating membership officer for a user
    Route::put('/api/users-officers/{id}', [UserController::class, 'updateOfficer']);

    // Route for user list data
    Route::get('/api/users', [UserController::class, 'showUserList'])->name('users.show');

    // Route for display a specific user data
    Route::get('/api/user/{id}', [UserController::class, 'displayUserDetails']);

    // Route to get the paid users at a specific month and year
    Route::get('/api/paid-users', [UserSharesAndPaymentsController::class, 'getPaidUsers'])->name('paidUsers');

    // Route to get the shares at a specific month and year
    Route::get('/api/shares', [UserSharesAndPaymentsController::class, 'getSharesReport'])->name('sharesReport');

    // Route to get the added and left users at a specific month
    Route::get('/api/users/add-leave-report', [UserController::class, 'getAddAndLeaveReport']);

    // Route to pay a user
    Route::put('/api/users/{id}/pay', [UserSharesAndPaymentsController::class, 'payUser'])->name('users.pay');

    // Route to Handle the toggling of the admin status for a user
    Route::post('/toggle-admin/{userId}', [AdminController::class, 'toggleAdmin']);
});

// Route to get the current user and it's admin status
Route::middleware('auth:sanctum')->get('/api/current-user', [UserController::class, 'getCurrentUser']);

// Public API Routes
// Route to fetch states
Route::get('/api/states', [StateController::class, 'index']);

// Route to fetch provinces by state ID
Route::get('/api/provinces/{state}', [ProvinceController::class, 'show']);
