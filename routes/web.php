<?php

use App\Filament\Pages\View3DModel;
use App\Http\Controllers\KiriWebhookController;
use App\Http\Controllers\Product3DModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Models\Products;
use App\Models\Shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    $products = Products::where('visibility', 'Yes')
        ->with('product_images')
        ->limit(8)
        ->get();

    return view('Home', compact('products'));
});

Route::get('/login', function () {
    return view('Login');
});
Route::post('/login', [RegistrationController::class, 'login'])->name('login');

Route::get('/register', function () {
    return view('Register');
});

Route::post('/register', [RegistrationController::class, 'register'])->name('register.submit');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/product-overview/{product_id}', [ProductController::class, 'show'])->name('product.overview');

Route::get('/shop-overview/{shop}', [ProductController::class, 'shopOverview'])->name('shop.overview');

Route::get('/shop-center', function () {
    return view('ShopCenter');
});

Route::get('/product-list', function (Request $request) {
    $query = Products::where('visibility', 'Yes')
        ->with(['product_images', 'occasions', 'user.shop']);

    // Category filter
    if ($request->filled('category') && $request->category !== 'all') {
        $query->where('type', $request->category);
    }

    // Subtype filter
    if ($request->filled('subtype')) {
        $subtypes = is_array($request->subtype) ? $request->subtype : [$request->subtype];
        $query->whereIn('subtype', $subtypes);
    }

    // Size filter
    if ($request->filled('size')) {
        $sizes = is_array($request->size) ? $request->size : [$request->size];

        // Size mapping: display name => database value
        $sizeMapping = [
            'XS' => 'Extra Small',
            'S' => 'Small',
            'M' => 'Medium',
            'L' => 'Large',
            'XL' => 'Extra Large',
            'XXL' => 'Extra Extra Large'
        ];

        // Convert abbreviated sizes to full names for database query
        $dbSizes = [];
        foreach ($sizes as $size) {
            if (isset($sizeMapping[$size])) {
                $dbSizes[] = $sizeMapping[$size];
            }
        }

        if (!empty($dbSizes)) {
            $query->whereIn('size', $dbSizes);
        }
    }

    // Color filter
    if ($request->filled('color')) {
        $colors = is_array($request->color) ? $request->color : [$request->color];
        $query->where(function ($q) use ($colors) {
            foreach ($colors as $color) {
                $q->orWhere('colors', 'LIKE', '%' . $color . '%');
            }
        });
    }

    // Occasion filter
    if ($request->filled('occasion')) {
        $occasions = is_array($request->occasion) ? $request->occasion : [$request->occasion];
        $query->whereHas('occasions', function ($q) use ($occasions) {
            $q->where(function ($subQ) use ($occasions) {
                foreach ($occasions as $occasion) {
                    $subQ->orWhereJsonContains('occasion_name', $occasion);
                }
            });
        });
    }

    // Body type filter (only for authenticated users)
    if ($request->filled('measurements_filter') && auth()->check()) {
        $userBodyType = auth()->user()->bodytype;
        if ($userBodyType) {
            // Add logic to filter products based on body type compatibility
            // This would require additional logic based on your business rules
            $query->whereHas('product_measurements', function ($q) use ($userBodyType) {
                // Add your body type matching logic here
                // For now, this is a placeholder
            });
        }
    }

    $products = $query->paginate(12)->appends($request->query());

    return view('ProductList', compact('products'));
});

Route::get('/account', function () {
    return view('AccountPage');
});
Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');

Route::post('/user/bodytype', [UserController::class, 'updateBodyType'])->name('user.updateBodyType');

// Chat routes (only for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/chat/conversation/{userId}', [App\Http\Controllers\ChatController::class, 'getConversation'])->name('chat.conversation');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/send-inquiry', [App\Http\Controllers\ChatController::class, 'sendInquiry'])->name('chat.send-inquiry');
    Route::get('/chat/partners', [App\Http\Controllers\ChatController::class, 'getConversationPartners'])->name('chat.partners');
    Route::get('/chat/admins', [App\Http\Controllers\ChatController::class, 'getAdmins'])->name('chat.admins');
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getAllUsers'])->name('chat.users');
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread');
});

Route::get('/shops', function () {
    $shops = Shops::with('user', 'products')->get();

    return view('Shops', compact('shops'));
});

Route::get('/shop-overview/{shop}', function (Request $request, Shops $shop) {
    $query = Products::where('visibility', 'Yes')
        ->where('user_id', $shop->user_id)
        ->with(['product_images', 'occasions', 'user.shop']);

    // Category filter
    if ($request->filled('category') && $request->category !== 'all') {
        $query->where('type', $request->category);
    }

    // Subtype filter
    if ($request->filled('subtype')) {
        $subtypes = is_array($request->subtype) ? $request->subtype : [$request->subtype];
        $query->whereIn('subtype', $subtypes);
    }

    // Size filter
    if ($request->filled('size')) {
        $sizes = is_array($request->size) ? $request->size : [$request->size];

        // Size mapping: display name => database value
        $sizeMapping = [
            'XS' => 'Extra Small',
            'S' => 'Small',
            'M' => 'Medium',
            'L' => 'Large',
            'XL' => 'Extra Large',
            'XXL' => 'Extra Extra Large'
        ];

        // Convert abbreviated sizes to full names for database query
        $dbSizes = [];
        foreach ($sizes as $size) {
            if (isset($sizeMapping[$size])) {
                $dbSizes[] = $sizeMapping[$size];
            }
        }

        if (!empty($dbSizes)) {
            $query->whereIn('size', $dbSizes);
        }
    }

    // Color filter
    if ($request->filled('color')) {
        $colors = is_array($request->color) ? $request->color : [$request->color];
        $query->where(function ($q) use ($colors) {
            foreach ($colors as $color) {
                $q->orWhere('colors', 'LIKE', '%' . $color . '%');
            }
        });
    }

    // Occasion filter
    if ($request->filled('occasion')) {
        $occasions = is_array($request->occasion) ? $request->occasion : [$request->occasion];
        $query->whereHas('occasions', function ($q) use ($occasions) {
            $q->where(function ($subQ) use ($occasions) {
                foreach ($occasions as $occasion) {
                    $subQ->orWhereJsonContains('occasion_name', $occasion);
                }
            });
        });
    }

    $products = $query->paginate(12)->appends($request->query());

    return view('ShopOverview', compact('shop', 'products'));
})->name('shop.overview');

Route::middleware(['auth'])->group(function () {
    Route::put('/user/measurements', [UserController::class, 'updateMeasurements'])
        ->name('user.measurements.update');
});
Route::post('/webhooks/kiri-model-ready', [KiriWebhookController::class, 'modelReady'])->name('webhooks.kiri-model-ready');

Route::post('/save-clipping/{id}', [Product3DModelController::class, 'saveClipping']);

Route::get('view3-d-model/{id}', View3DModel::class)
    ->middleware(['web', 'auth'])
    ->name('view-3d-model');
