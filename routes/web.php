<?php

use App\Http\Controllers\FileController;
use App\Http\Middleware\CheckProjectAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    //
    // PROJECTS
    //
    Route::get('projects/generate/{quantity}/{year}', [ProjectController::class, 'generate'])->name('projects.generate');
    Route::get('projects/project/{id}', [ProjectController::class, 'index'])->middleware(CheckProjectAccess::class)->name('projects.project');
    Route::delete('projects/delete/{project}', [ProjectController::class, 'delete'])->name('projects.delete');
    Route::post('projects/delete-selected', [ProjectController::class, 'deleteSelected'])->name('projects.delete-selected');
    Route::post('projects/delete-empty', [ProjectController::class, 'deleteEmptyProject'])->name('projects.delete-empty');
    Route::post('projects/add', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('projects/upload/{project}', [ProjectController::class, 'uploadFiles'])->name('projects.upload');
    Route::post('projects/update', [ProjectController::class, 'update'])->name('projects.update');
    // FILES IN PROJECT
    Route::post('projects/download', [ProjectController::class, 'downloadFiles'])->name('projects.download');
    Route::post('projects/distribute', [ProjectController::class, 'distributeFiles'])->name('projects.distribute');
    // MASKS
    Route::post('projects/update-mask-validated', [ProjectController::class, 'updateMaskValidated'])->name('projects.updateMaskValidated');
    Route::post('projects/update-mask-distributed', [ProjectController::class, 'updateMaskDistributed'])->name('projects.updateMaskDistributed');
    //
    // PROFILE
    //
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    //
    // USER MANAGER
    //
    Route::get('usermanager', [UserController::class, 'index'])->name('usermanager');
    // USER
    Route::post('usermanager/adduser', [UserController::class, 'adduser'])->name('usermanager.adduser');
    Route::post('usermanager/addcompany', [UserController::class, 'addcompany'])->name('usermanager.addcompany');
    // COMPANY
    Route::post('usermanager/updatecompany', [UserController::class, 'updateCompany'])->name('usermanager.updatecompany');
    Route::post('usermanager/deletecompany', [UserController::class, 'deleteCompany'])->name('usermanager.deletecompany');
    // USERS
    Route::post('usermanager/updateuser', [UserController::class, 'updateUser'])->name('usermanager.updateuser');
    Route::post('usermanager/deleteuser', [UserController::class, 'deleteUser'])->name('usermanager.deleteuser');
    //
    // FILES
    //
    Route::post('files/update/{file}', [FileController::class, 'update'])->name('files.update');
    Route::delete('files/delete/{file}', [FileController::class, 'delete'])->name('file.delete');
    Route::get('/files/download_multiple', [FileController::class, 'downloadMultipleFiles'])->name('files.download');

});

