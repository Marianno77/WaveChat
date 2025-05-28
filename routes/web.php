<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\HomeController;
use App\Models\Conversation;
use Illuminate\Support\Facades\Route;

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

Route::get('/',[HomeController::class,'index'])->name('home');
Route::middleware('auth')->group(function() {
    Route::get('/friends', [FriendController::class, 'index'])->name('friends');
    Route::get('/friends/all-users', [FriendController::class, 'allUsers']);
    Route::post('/friends/add', [FriendController::class, 'store']);
    Route::get('friends/get-friends',[FriendController::class, 'getFriends']);
    Route::get('friends/get-invitations',[FriendController::class, 'getInvitations']);
    Route::get('friends/get-block-list', [FriendController::class, 'getBlockList']);
    Route::post('friends/accept', [FriendController::class, 'acceptFriend']);
    Route::post('friends/block',[FriendController::class, 'block']);
    Route::post('friends/delete', [FriendController::class, 'delete']);
});

Route::middleware('auth')->get('/user', function () {
    return view('profile.show');
})->name('profile');

Route::middleware('auth')->group(function() {
    Route::get('conversations', [ConversationController::class, 'index'])->name('conversations');
    Route::post('conversations/get-conversations', [ConversationController::class, 'getConversations']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversationId}/get-messages', [ConversationController::class, 'getMessages']);
    Route::post('/conversations/{id}/send-message', [ConversationController::class, 'sendMessage']);
    Route::get('conversations/{conversationId}/change-status',[ConversationController::class, 'changeStatus']);
});


///login and register system:

Route::get('login', [LoginController::class, 'show'])->name('login');
Route::post('login', [LoginController::class, 'authenticate']);

Route::get('register', [RegisterController::class, 'show'])->name('register');
Route::post('register', [RegisterController::class, 'store']);

Route::post('/logout',[LogoutController::class,'logout'])->name('logout');
