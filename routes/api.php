<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,
    CategoryPostController,
    DistituteController,
    EventController,
    PostController,
    CommentController,
    PostReactionController,
    RequestController,
    RequestFileController,
    StripeController,
    StripeWebhookController,
    WidowController
};

//Route::post("register", [\App\Http\Controllers\AuthController::class, "register"]);
//Route::post("login", [\App\Http\Controllers\AuthController::class, "login"]);
//Route::get("/categories", [\App\Http\Controllers\CategoryPostController::class, "getAll"]);
//Route::get("/find/posts/category/{name}", [\App\Http\Controllers\PostController::class, "findByCategory"]);
//
//Route::middleware(['auth:sanctum'])->group(function () {
//    Route::get("user", [\App\Http\Controllers\AuthController::class, "user"]);
//    Route::post("logout", [\App\Http\Controllers\AuthController::class, "logout"]);
//    Route::get("orphans", [\App\Http\Controllers\OrphanController::class, "getAll"]);
//    Route::post("/orphan/create", [\App\Http\Controllers\OrphanController::class, "create"]);
//    Route::delete("/orphan/delete/{id}", [\App\Http\Controllers\OrphanController::class, "delete"]);
//    Route::get("count", [\App\Http\Controllers\OrphanController::class, "count"]);
//    Route::get("findId/{id}", [\App\Http\Controllers\OrphanController::class, "findById"]);
//    Route::post("/create/post", [\App\Http\Controllers\PostController::class, "store"]);
//    Route::get("/find/post/{id}", [\App\Http\Controllers\PostController::class, "search"]);
//    Route::post("/create/comment", [\App\Http\Controllers\CommentController::class, "store"]);
//    Route::get("/find/comments/user", [\App\Http\Controllers\AuthController::class, "getCommentsById"]);
//    Route::get("/find/posts/by/category/{name}", [\App\Http\Controllers\CategoryPostController::class, "getPostsByCategory"]);
//    Route::post("/guardian", [\App\Http\Controllers\GuardianController::class, "store"]);
//    Route::get('/guardians/{id}', [GuardianController::class, 'findGuardian']);
//    Route::get('/guardians', [GuardianController::class, 'getAll']);
//    Route::post("/request/create", [\App\Http\Controllers\RequestController::class, "store"]);
//    Route::get("/request/find/{id}", [\App\Http\Controllers\RequestController::class, "findById"]);
//    Route::get("/request/find/status/{status}", [\App\Http\Controllers\RequestController::class, "findByStatus"]);
//    Route::post("/request/file/create", [\App\Http\Controllers\RequestFileController::class, "store"]);
//    Route::get("/requests", [\App\Http\Controllers\RequestController::class, "getAllRequests"]);
//    Route::put("/files/{fileId}/status/{status}", [\App\Http\Controllers\RequestFileController::class, "updateFileStatus"]);
//    Route::get("/requests/count/status/{status}", [\App\Http\Controllers\RequestController::class, "countAllRequests"]);
//    Route::put("/request/{idRequest}/status/{status}", [\App\Http\Controllers\RequestController::class, "updateStatus"]);
//});
//
//


// Public routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);
Route::get("/categories", [CategoryPostController::class, "getAll"]);
Route::get('/posts', [PostController::class, 'getAll']);
Route::get("/posts/find/{id}", [PostController::class, "search"]);
Route::get('/posts/{postId}/reactions', [PostReactionController::class, 'likeAndDeslikeByPost']);
Route::get("comments/{post_id}", [CommentController::class, "getAll"]);
Route::get('/posts/find/posts/category/{name}', [PostController::class, 'findByCategory']);


// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth related
    Route::get("user", [AuthController::class, "user"]);
    Route::post("logout", [AuthController::class, "logout"]);
    Route::get("/find/comments/user", [AuthController::class, "getCommentsById"]);
    Route::get("/find/request/id/{id}", [AuthController::class, "findRequestsById"]);

    //Orphan management
    Route::prefix('orphans')->group(function () {
        Route::get("/", [\App\Http\Controllers\OrphaneController::class, "getAllOrphanes"]);
        Route::get("/count", [\App\Http\Controllers\OrphaneController::class, "count"]);
    });

    // Posts & Categories
    Route::prefix('posts')->group(function () {
        Route::post("/create", [PostController::class, "store"]);
        Route::get('/{id}/user', [\App\Http\Controllers\PostReactionController::class, 'show']);
        Route::post('/{postId}/react', [\App\Http\Controllers\PostReactionController::class, 'react']);
        Route::delete('{postId}/destroy', [\App\Http\Controllers\PostReactionController::class, 'removeReaction']);
        Route::post('/update/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'delete']);
    });

    Route::prefix('categories')->group(function () {
        Route::post("/create", [CategoryPostController::class, "store"]);
    });

    // Comments
    Route::prefix('comments')->group(function () {
        Route::post("/create", [CommentController::class, "store"]);
        Route::delete('/comment/id/{id}', [CommentController::class, 'destroy']);
    });
    Route::prefix('users')->group(function () {
        Route::get("/", [AuthController::class, "getWritersAndAdmins"]);
        Route::post('/{id}/reset-password', [AuthController::class, 'adminResetUserPassword']);
        Route::put('/{id}/role', [AuthController::class, 'modifyUserRole']);
        Route::delete('/{id}', [AuthController::class, 'deleteUser']);
    });


    Route::prefix('events')->group(function () {
        Route::post('/create', [EventController::class, 'store']);
        Route::get('/', [EventController::class, 'getAll']);
        Route::put('/{event}', [EventController::class, 'update']);     // Update event
        Route::delete('/{event}', [EventController::class, 'destroy']); // Delete event
    });


    // widows management
    Route::prefix('widows')->group(function () {
        Route::post("/create", [\App\Http\Controllers\WidowController::class, "store"]);
        Route::get("/", [\App\Http\Controllers\WidowController::class, "getAll"]);
        Route::get("/count", [\App\Http\Controllers\WidowController::class, "countWidows"]);
        Route::put('/{id}', [WidowController::class, 'update']);
        Route::delete('/{id}', [WidowController::class, 'destroy']);


    });
    Route::prefix('destitutes')->group(function () {
        Route::post("/create", [\App\Http\Controllers\DistituteController::class, "store"]);
        Route::post("/update/{id}", [\App\Http\Controllers\DistituteController::class, "update"]);
        Route::get("/", [\App\Http\Controllers\DistituteController::class, "showAll"]);
        Route::delete('/{id}', [DistituteController::class, 'destroy']);

    });

    // Requests & Request Files
    Route::prefix('requests')->group(function () {
        Route::post("/create", [RequestController::class, "store"]);
        Route::get("/find/{id}", [RequestController::class, "findById"]);
        Route::get("/find/status/{status}", [RequestController::class, "findByStatus"]);
        Route::get("/", [RequestController::class, "getAllRequests"]);
        Route::get("/count/status/{status}", [RequestController::class, "countAllRequests"]);
        Route::put("/{idRequest}/status/{status}", [RequestController::class, "updateStatus"]);
        Route::get('/summary/{requestId}', [\App\Http\Controllers\PdfController::class, "downloadPDF"]);
        Route::post("/file/create", [RequestFileController::class, "store"]);
        Route::post("/files/{fileId}/status/{status}", [RequestFileController::class, "updateFileStatus"]);
        Route::post('/file/{fileId}/add/note/{note}', [RequestFileController::class, "addNote"]);
        Route::post('/reupload/{fileId}', [RequestFileController::class, 'reuploadFile']);

    });


    Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);

});
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::delete('/stripe/delete/{customerId}', [StripeController::class, 'deleteStripeCustomer']);
Route::prefix('donations')->group(function () {
    Route::get("/", [\App\Http\Controllers\DonationController::class, "getAll"]);
    Route::get("/count", [\App\Http\Controllers\DonationController::class, "totalAmount"]);
});
