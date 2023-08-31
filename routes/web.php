<?php

use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, "show"]);
    Route::get("admin", [DashboardController::class, "show"]);
    Route::get("admin/user/list", [AdminUserController::class, "list"]);
    Route::get("admin/user/add", [AdminUserController::class, "add"]);
    Route::post("admin/user/store", [AdminUserController::class, "store"]);
    Route::get("admin/user/delete/{id}", [AdminUserController::class, "delete"])->name("user.delete");
    Route::get("admin/user/action", [AdminUserController::class, "action"]);
    Route::get("admin/user/edit/{id}", [AdminUserController::class, "edit"])->name("user.edit");
    Route::post("admin/user/update/{id}", [AdminUserController::class, "update"])->name("user.update");
    Route::get("admin/page/list", [AdminPageController::class, "list"]);
});



require __DIR__ . '/auth.php';
