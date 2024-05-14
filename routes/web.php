<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\StateController;
use \App\Http\Controllers\ProvinceController;
use \App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MonthlyShareController;
use App\Http\Controllers\NotificationController;

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

// Route for updating membership officer for a user
Route::put('/api/users-officers/{id}', [UserController::class, 'updateOfficer']);

// Route for user list data
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Route for display a specific user data
Route::get('/api/user/{id}', [UserController::class, 'displayUserDetails']);

// Route for update user data
Route::put('/api/user/{userId}', [UserController::class, 'update']);

// Route for offices list data
Route::get('/api/offices', [OfficeController::class, 'index'])->name('offices');

// Route for membership officers list data
Route::get('/api/membership-officers', [AdminController::class, 'index'])->name('officers');

// Route for fetching the notifications
Route::get('/api/notifications', [NotificationController::class, 'getUserNotifications']);

// Route for displaying the new registered user for approval
Route::get('/user/approval/{userId}', [UserController::class, 'approval'])->name('user.approval');

// Route for updating the read status for the notification
Route::put('/api/mark-as-read/{notificationId}', [NotificationController::class, 'markAsRead'])->name('markAsRead');

// Route to update the user status and approve it, also update the user data if needed
Route::put('/api/approve-user/{id}', [UserController::class, 'approveUser'])->name('approveUser');

// Route to update the user status and reject it
Route::put('/api/reject-user/{id}', [UserController::class, 'rejectUser'])->name('rejectUser');

// Route for Admins list page
Route::get('/api/admins', [AdminController::class, 'showAdminsList'])->name('admins.show');

// Route for fetching users related to the current logged in admin
Route::get('/api/users/byAdmin', [UserController::class, 'getUsersByAdmin'])->name('userByAdmin');

// Route for display the Monthly Share page that contains the users list under the current admin
Route::get('/api/monthly-share', [MonthlyShareController::class, 'index'])->name('monthlyShare');

Route::put('/api/users/{id}/pay', [MonthlyShareController::class, 'payUser'])->name('users.pay');
