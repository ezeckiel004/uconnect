<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssociationController;
use App\Http\Controllers\Api\CagnoteController;
use App\Http\Controllers\Api\Admin\AdminCagnoteController;
use App\Http\Controllers\Api\CagnoteLikeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WithdrawalRequestController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\ForumCommentController;
use App\Http\Controllers\Api\NotificationController;

Route::prefix('auth')->group(function () {
    // Public routes (no authentication required)
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/login-donor', [AuthController::class, 'loginDonor'])->name('auth.login-donor');
    Route::post('/login-admin', [AuthController::class, 'loginAdmin'])->name('auth.login-admin');
    Route::post('/guest', [AuthController::class, 'guestAccess'])->name('auth.guest');
    Route::post('/register-donor', [AuthController::class, 'registerDonor'])->name('auth.register-donor');
    Route::post('/register-association', [AuthController::class, 'registerAssociation'])->name('auth.register-association');

    // Debug: Test authentication
    Route::get('/debug/auth', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'authenticated' => $user ? true : false,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'auth_header' => $request->header('Authorization') ? substr($request->header('Authorization'), 0, 50) . '...' : 'MISSING',
            'timestamp' => now(),
        ]);
    })->middleware('auth:api')->name('debug.auth');

    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->name('auth.user');
        Route::put('/user', [AuthController::class, 'update'])->name('auth.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
        Route::post('/change-first-login-password', [AuthController::class, 'changeFirstLoginPassword'])->name('auth.change-first-login-password');
    });
});

// Admin routes (authentication required)
Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('/associations', [AuthController::class, 'getAllAssociations'])->name('admin.associations');
    Route::post('/assign-credentials', [AuthController::class, 'assignAssociationCredentials'])->name('admin.assign-credentials');
    Route::post('/update-password', [AuthController::class, 'updateAssociationPassword'])->name('admin.update-password');

    // Admin Cagnote routes
    Route::get('/cagnotes/pending', [AdminCagnoteController::class, 'getPendingCagnotes'])->name('admin.cagnotes.pending');
    Route::get('/cagnotes', [AdminCagnoteController::class, 'getAllCagnotes'])->name('admin.cagnotes.all');
    Route::get('/cagnotes/{id}/review', [AdminCagnoteController::class, 'reviewCagnote'])->name('admin.cagnotes.review');
    Route::post('/cagnotes/{id}/approve', [AdminCagnoteController::class, 'approveCagnote'])->name('admin.cagnotes.approve');
    Route::post('/cagnotes/{id}/reject', [AdminCagnoteController::class, 'rejectCagnote'])->name('admin.cagnotes.reject');

    // Admin Events routes
    Route::get('/events/pending', [EventController::class, 'getPendingEvents'])->name('admin.events.pending');
    Route::post('/events/{id}/approve', [EventController::class, 'approveEvent'])->name('admin.events.approve');
    Route::post('/events/{id}/reject', [EventController::class, 'rejectEvent'])->name('admin.events.reject');
});

// Public cagnotes routes (no authentication required)
Route::prefix('cagnotes')->group(function () {
    // Public route to get all approved cagnotes
    Route::get('/approved/all', [CagnoteController::class, 'getApprovedCagnotes'])->name('cagnotes.approved.all');
    
    // Public route to get approved cagnotes by category
    Route::get('/category/{category}', [CagnoteController::class, 'getApprovedCagnotesByCategory'])->name('cagnotes.category');
    
    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [CagnoteController::class, 'index'])->name('cagnotes.index');
        Route::post('/', [CagnoteController::class, 'store'])->name('cagnotes.store');
        Route::get('/{id}', [CagnoteController::class, 'show'])->name('cagnotes.show');
        Route::put('/{id}', [CagnoteController::class, 'update'])->name('cagnotes.update');
        Route::delete('/{id}', [CagnoteController::class, 'destroy'])->name('cagnotes.destroy');
        
        // Like routes (protected)
        Route::post('/{id}/like', [CagnoteLikeController::class, 'like'])->name('cagnotes.like');
        Route::delete('/{id}/like', [CagnoteLikeController::class, 'unlike'])->name('cagnotes.unlike');
        Route::get('/{id}/like-status', [CagnoteLikeController::class, 'checkLikeStatus'])->name('cagnotes.like-status');
    });
});

// Additional protected routes can be added here
Route::middleware('auth:api')->group(function () {
    // Example: Campaigns routes
    // Route::apiResource('campaigns', 'Api\CampaignController');
});
// Payment routes
Route::prefix('payments')->group(function () {
    // Public routes (no authentication required)
    Route::get('cagnote/{cagnoteId}/donations', [PaymentController::class, 'getCagnoteDonations'])->name('payments.cagnote-donations');
    Route::post('webhook', [PaymentController::class, 'handleWebhook'])->name('payments.webhook');

    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        Route::post('intent', [PaymentController::class, 'createPaymentIntent'])->name('payments.create-intent');
        Route::post('confirm', [PaymentController::class, 'confirmPayment'])->name('payments.confirm');
        Route::get('donation/{id}', [PaymentController::class, 'getDonation'])->name('payments.get-donation');
        Route::get('my-donations', [PaymentController::class, 'getDonorDonations'])->name('payments.donor-donations');
    });
});

// Withdrawal Requests routes
Route::prefix('withdrawal-requests')->group(function () {
    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [WithdrawalRequestController::class, 'index'])->name('withdrawal-requests.index');
        Route::get('/{id}', [WithdrawalRequestController::class, 'show'])->name('withdrawal-requests.show');
    });
});

// Admin Withdrawal Requests routes
Route::prefix('admin/withdrawal-requests')->middleware('auth:api')->group(function () {
    Route::get('/', [WithdrawalRequestController::class, 'adminIndex'])->name('admin.withdrawal-requests.index');
    Route::patch('/{id}/process', [WithdrawalRequestController::class, 'processWithdrawal'])->name('admin.withdrawal-requests.process');
    Route::patch('/{id}/reject', [WithdrawalRequestController::class, 'rejectWithdrawal'])->name('admin.withdrawal-requests.reject');
});

// Events routes
Route::prefix('events')->group(function () {
    // Public route to get all approved events
    Route::get('/approved', [EventController::class, 'getApprovedEvents'])->name('events.approved');
    
    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::get('/{id}', [EventController::class, 'show'])->name('events.show');
        Route::put('/{id}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    });
});

// Forum routes
Route::prefix('forum')->group(function () {
    // Public routes (no authentication required)
    Route::get('/posts', [ForumPostController::class, 'index'])->name('forum.index');
    Route::get('/posts/{id}', [ForumPostController::class, 'show'])->name('forum.show');
    Route::get('/posts/{id}/download', [ForumPostController::class, 'downloadFile'])->name('forum.download');

    // Protected routes (authentication required)
    Route::middleware('auth:api')->group(function () {
        // Forum posts routes
        Route::post('/posts', [ForumPostController::class, 'store'])->name('forum.store');
        Route::put('/posts/{id}', [ForumPostController::class, 'update'])->name('forum.update');
        Route::delete('/posts/{id}', [ForumPostController::class, 'destroy'])->name('forum.destroy');
        Route::post('/posts/{id}/like', [ForumPostController::class, 'toggleLike'])->name('forum.like');

        // Forum comments routes
        Route::post('/posts/{postId}/comments', [ForumCommentController::class, 'store'])->name('forum.comments.store');
        Route::put('/comments/{id}', [ForumCommentController::class, 'update'])->name('forum.comments.update');
        Route::delete('/comments/{id}', [ForumCommentController::class, 'destroy'])->name('forum.comments.destroy');
        Route::post('/comments/{id}/like', [ForumCommentController::class, 'toggleLike'])->name('forum.comments.like');

        // Forum replies routes
        Route::post('/comments/{commentId}/replies', [ForumReplyController::class, 'store'])->name('forum.replies.store');
        Route::put('/replies/{id}', [ForumReplyController::class, 'update'])->name('forum.replies.update');
        Route::delete('/replies/{id}', [ForumReplyController::class, 'destroy'])->name('forum.replies.destroy');
        Route::post('/replies/{id}/like', [ForumReplyController::class, 'toggleLike'])->name('forum.replies.like');
    });
});

// Messages routes (only for associations)
Route::prefix('messages')->middleware('auth:api')->group(function () {
    // Conversations routes
    Route::get('/conversations', [ConversationController::class, 'index'])->name('messages.conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('messages.conversations.store');
    Route::get('/conversations/search', [ConversationController::class, 'search'])->name('messages.conversations.search');
    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('messages.conversations.show');
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy'])->name('messages.conversations.destroy');

    // Messages routes
    Route::post('/', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/', [MessageController::class, 'index'])->name('messages.index');
    Route::delete('/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
});

// Associations routes (public - for Explorer page)
Route::prefix('associations')->group(function () {
    Route::get('/by-country', [AssociationController::class, 'getAssociationsByCountry'])->name('associations.by-country');
    Route::get('/{associationId}/campaigns', [AssociationController::class, 'getAssociationCampaigns'])->name('associations.campaigns');
});

// Notifications routes (protected)
Route::prefix('notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});