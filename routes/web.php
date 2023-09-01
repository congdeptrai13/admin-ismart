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
    Route::get("admin/page/list", [AdminPageController::class, "list"])->name("page.list");
    Route::get("admin/page/add", [AdminPageController::class, "add"]);
    Route::group(['prefix' => 'laravel-filemanager'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    Route::post("admin/page/store", [AdminPageController::class, "store"])->name("page.store");
    Route::get("admin/page/delete/{id}", [AdminPageController::class, "delete"])->name("page.delete");
    Route::get("admin/page/edit/{id}", [AdminPageController::class, "edit"])->name("page.edit");
    Route::post("admin/page/update/{id}", [AdminPageController::class, "update"])->name("page.update");
    Route::get("admin/page/action", [AdminPageController::class, "action"]);
});



require __DIR__ . '/auth.php';
