<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GalleryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleare' => 'guest'], function(){
    Route::get('/test', [TestController::class, 'index']);
    Route::get('/', [GalleryController::class, 'index'])->name('home');
    Route::get('/register', function(){return view('pages.user.register');});
    Route::post('/register', [UserController::class, 'store'])->name('signUp');
    Route::get('/login', function(){return view('pages.user.login');})->name('login');
    Route::post('/login', [UserController::class, 'logOn'])->name('logOn');


});

Route::group(['middleare' => 'auth'], function(){
    Route::get('/verification', [UserController::class, 'verification'])->name('verification');
    Route::post('/send-email', [UserController::class, 'resendEmail'])->name('resend.email');
    Route::get('/logout', [UserController::class, 'logOff'])->name('logOff');
});


Route::group(['middleware' => ['auth','verified:verification']], function(){
    Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/{id}', [UserController::class, 'update'])->name('update.profile');
    Route::get('/my-galleries',[GalleryController::class, 'myGalleries'])->name('myGalleries');
    Route::get('/gallery/edit/{id}', [GalleryController::class, 'edit'])->name('edit.gallery');
    Route::get('/gallery/view/{id}', [GalleryController::class, 'view'])->name('view.gallery');
    Route::post('/gallery/{id}/edit', [GalleryController::class, 'update'])->name('update.gallery');
    Route::delete('gallery/delete/{id}', [GalleryController::class, 'destroy'])->name('delete.gallery');
    Route::get('gallery/create',function(){return view('pages.create-gallery');})->name('get.gallery');
    Route::post('gallery/create',[GalleryController::class, 'create'])->name('create.gallery');
    Route::post('gallery/{id}/comment', [CommentController::class, 'create'])->name('create.comment');
    Route::delete('gallery/{gallery_id}/comment/{comment_id}/delete', [CommentController::class, 'delete'])->name('delete.comment');
});

Route::get('/email/verify/{id}', [UserController::class, 'validateEmail']);
