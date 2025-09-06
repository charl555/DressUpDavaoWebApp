<?php

use App\Filament\Pages\View3DModel;
use App\Http\Controllers\KiriWebhookController;
use App\Http\Controllers\Product3DModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('Home');
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

Route::get('/product-overview', function () {
    return view('ProductOverview');
});

Route::get('/shop-center', function () {
    return view('ShopCenter');
});

// Route::get('/product-list', function () {
//     return view('ProductList');
// });

Route::get('/product-list', function () {
    $products = Products::where('visibility', 'Yes')
        ->with('thumbnail')
        ->get();

    return view('ProductList', compact('products'));
});

Route::get('/account', function () {
    return view('AccountPage');
});

Route::get('/shops', function () {
    return view('Shops');
});

Route::get('/shop-overview', function () {
    return view('ShopOverview');
});
Route::post('/webhooks/kiri-model-ready', [KiriWebhookController::class, 'modelReady'])->name('webhooks.kiri-model-ready');

Route::post('/save-clipping/{id}', [Product3DModelController::class, 'saveClipping']);

Route::get('view3-d-model/{id}', View3DModel::class)
    ->middleware(['web', 'auth'])
    ->name('view-3d-model');
