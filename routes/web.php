<?php

use App\Filament\Pages\View3DModel;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KiriWebhookController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Product3DModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ShopPageController;
use App\Http\Controllers\UserController;
use App\Models\Bookings;
use App\Models\Products;
use App\Models\Shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Facades\ResponseCache;

Route::get('/', function () {
    try {
        $products = Cache::remember('homepage_products', 300, function () {
            return Products::where('visibility', 'Yes')
                ->where('status', 'Available')
                ->with(['product_images' => function ($query) {
                    $query
                        ->whereNotNull('thumbnail_image')
                        ->orderBy('created_at', 'asc');
                }])
                ->select(
                    'product_id',
                    'name',
                    'type',
                    'subtype',
                )
                ->limit(5)
                ->get();
        });

        return view('home', compact('products'));
    } catch (\Exception $e) {
        // Fallback if cache fails
        $products = Products::where('visibility', 'Yes')
            ->where('status', 'Available')
            ->with('product_images')
            ->select('product_id', 'name', 'type', 'subtype')
            ->limit(5)
            ->get();

        return view('home', compact('products'));
    }
});

// Home Controller Routes
Route::get('/login', [HomeController::class, 'showLogin'])->name('login.show');
Route::get('/register', [HomeController::class, 'showRegister'])->name('register.show');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/forgot-password', [HomeController::class, 'showForgotPassword'])->name('forgot.password.show');

// Registration Controller Routes
Route::post('/login', [RegistrationController::class, 'login'])->name('login');
Route::post('/register', [RegistrationController::class, 'register'])->name('register.submit');
Route::get('/check-email', [RegistrationController::class, 'checkEmail'])->name('check.email');
Route::post('/forgot-password', [RegistrationController::class, 'forgotPassword'])->name('forgot.password');

// Password Reset Routes
Route::post('/password/send-code', [PasswordResetController::class, 'sendResetCode']);
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyCode']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Product Routes
Route::get('/product-list', [ProductController::class, 'productList'])->name('product.list');
Route::get('/product-overview/{product_id}', [ProductController::class, 'show'])->name('product.overview');

// Route::get('/shop-overview/{shop}', [ProductController::class, 'shopOverview'])->name('shop.overview');

Route::get('/shop-center', function () {
    return view('shopcenter');
});

// account page route
Route::get('/account', [UserController::class, 'accountPage'])->name('account.page');
// profile route
Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');

// measurements route
Route::post('/account/measurements/update', [UserController::class, 'updateMeasurements'])->name('measurements.update');
// password route
Route::post('/account/update-password', [UserController::class, 'updatePassword'])->name('account.updatePassword');
// preferences route
Route::post('/account/update-preferences', [UserController::class, 'updatePreferences'])->name('account.updatePreferences');

// delete account
Route::delete('/account/delete', [UserController::class, 'deleteAccount'])->name('account.delete');
// review route
Route::post('/submit-review', [UserController::class, 'submitReview'])->name('user.submitReview');
// review data route
Route::get('/user/review-data/{shop}', [UserController::class, 'getReviewData']);
// Account deletion routes
Route::post('/account/request-deletion', [UserController::class, 'requestAccountDeletion'])->name('account.request-deletion');
Route::delete('/account/cancel-deletion', [UserController::class, 'cancelAccountDeletion'])->name('account.cancel-deletion');

// Shop policy route (handles auth internally)
Route::get('/shop-policy/{id}', function ($id) {
    try {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $shop = null;

        // First try to find shop by shop_id
        $shop = Shops::where('shop_id', $id)->first();

        // If not found, try to find by product_id
        if (!$shop) {
            $product = Products::with('user.shop')->where('product_id', $id)->first();
            if ($product?->user?->shop) {
                $shop = $product->user->shop;
            }
        }

        if (!$shop) {
            return response()->json(['error' => 'Shop not found for this product'], 404);
        }

        // Get the shop policy, with fallback message
        $policy = $shop->shop_policy;
        if (empty($policy) || trim($policy) === '') {
            $policy = 'No specific rental policy has been set by this shop. Please contact the shop directly for rental terms and conditions.';
        }

        return response()->json([
            'policy' => $policy,
            'shop_name' => $shop->shop_name,
            'shop_id' => $shop->shop_id
        ]);
    } catch (\Exception $e) {
        \Log::error('Shop policy fetch error: ' . $e->getMessage(), [
            'product_id' => $id,
            'user_id' => auth()->id() ?? 'guest',
            'error' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Unable to fetch shop policy. Please try again later.'], 500);
    }
})->name('shop.policy');

// Chat routes (only for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/chat/conversation/{userId}', [App\Http\Controllers\ChatController::class, 'getConversation'])->name('chat.conversation');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/send-inquiry', [App\Http\Controllers\ChatController::class, 'sendInquiry'])->name('chat.send-inquiry');
    Route::get('/chat/partners', [App\Http\Controllers\ChatController::class, 'getConversationPartners'])->name('chat.partners');
    Route::get('/chat/admins', [App\Http\Controllers\ChatController::class, 'getAdmins'])->name('chat.admins');
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getAllUsers'])->name('chat.users');
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread');

    // Booking routes (admin only)
    Route::get('/chat/available-products', [App\Http\Controllers\ChatController::class, 'getAvailableProducts'])->name('chat.available-products');
    Route::post('/chat/create-booking', [App\Http\Controllers\ChatController::class, 'createBooking'])->name('chat.create-booking');
});

// Shop pages routes
Route::get('/shops', [ShopPageController::class, 'list'])->name('shops.list');
Route::get('/shop-overview/{shop}', [ShopPageController::class, 'overview'])->name('shop.overview');

Route::middleware(['auth'])->group(function () {
    Route::put('/user/measurements', [UserController::class, 'updateMeasurements'])
        ->name('user.measurements.update');
});

Route::post('/products/{product}/favorite', [FavoriteController::class, 'store'])->name('products.favorite');
Route::delete('/products/{product}/unfavorite', [FavoriteController::class, 'destroy'])->name('products.unfavorite');

// KIRI Engine webhook - excluded from CSRF protection
Route::post('/webhook', [App\Http\Controllers\KiriWebhookController::class, 'handleWebhook'])
    ->name('kiri.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/save-clipping/{id}', [Product3DModelController::class, 'saveClipping']);

Route::get('view3-d-model/{id}', View3DModel::class)
    ->middleware(['web', 'auth'])
    ->name('view-3d-model');

Route::get('/test-email', function () {
    Mail::raw('This is a test email from Dress Up Davao.', function ($message) {
        $message
            ->to('decoyyv1@gmail.com')
            ->subject('SendGrid Test');
    });

    return 'Email sent!';
});

Route::get('/test-storage', function () {
    $testPath = 'temp/test.txt';

    // Test writing
    Storage::disk('local')->put($testPath, 'test content');

    // Test reading
    $content = Storage::disk('local')->get($testPath);
    $absolutePath = Storage::disk('local')->path($testPath);

    return [
        'written' => true,
        'read' => $content,
        'absolute_path' => $absolutePath,
        'file_exists' => file_exists($absolutePath),
        'storage_exists' => Storage::disk('local')->exists($testPath),
    ];
});
