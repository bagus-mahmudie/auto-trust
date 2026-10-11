<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MobilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\PostingKebutuhanController;
use App\Http\Controllers\KebutuhanPostingPublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ModalAuthController;
use App\Http\Controllers\MobilLikeController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\MotorReviewController;
use App\Http\Controllers\MotorLikeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SewaController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login', [ModalAuthController::class, 'loginGet'])->name('auth.login.get');
Route::post('/login', [ModalAuthController::class, 'loginPost'])->name('auth.login.post');
Route::post('/register', [ModalAuthController::class, 'registerPost'])->name('auth.register.post');

Route::post('/logout', [ModalAuthController::class, 'logout'])->name('auth.logout');

Route::get('/rekomendasi-mobil', [MobilController::class, 'index'])
    ->name('rekomendasi.mobil');

Route::get('/populer-mobil', [\App\Http\Controllers\MobilController::class, 'popular'])
    ->name('populer.mobil');

    Route::post('/auth/intended', function (Request $request) {
    $request->validate([
        'url' => ['required', 'string'],
    ]);

    // simpan intended untuk redirect setelah login/register
    session(['url.intended' => $request->url]);

    return response()->json(['ok' => true]);
})->name('auth.intended');


Route::middleware(['auth'])->group(function () {
    Route::get('/pengaturan', [ProfileSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/pengaturan', [ProfileSettingsController::class, 'update'])->name('settings.update');
});


Route::middleware('auth')->group(function () {
    Route::get('/profil', [SellerProfileController::class, 'me'])->name('seller.me');
});

Route::get('/penjual/{user}', [SellerProfileController::class, 'show'])->name('seller.show');

Route::middleware('auth')->group(function () {
    Route::get('/jual-mobil', [MobilController::class, 'create'])->name('mobil.create');
    Route::post('/jual-mobil', [MobilController::class, 'store'])->name('mobil.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/posting-kebutuhan', [PostingKebutuhanController::class, 'create'])->name('posting.kebutuhan');
    Route::post('/posting-kebutuhan', [PostingKebutuhanController::class, 'store'])->name('posting.kebutuhan.store');
});

Route::get('/butuh-mobil', [KebutuhanPostingPublicController::class, 'index'])
    ->name('butuh_mobil.index');

Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('mobil.show');
Route::post('/mobil/{mobil}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::view('/tentang-kami', 'pages.tentang-kami')->name('tentang-kami');
Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArtikelController::class, 'show'])->name('artikel.show');

Route::post('/mobil/{mobil}/like-toggle', [MobilLikeController::class, 'toggle'])
    ->name('mobil.like.toggle')
    ->middleware('auth');


// list motor
Route::get('/rekomendasi-motor', [MotorController::class, 'index'])->name('rekomendasi.motor');

// detail motor
Route::get('/motor/{motor}', [MotorController::class, 'show'])->name('motor.show');

Route::post('/motor/{motor}/reviews', [MotorReviewController::class, 'store'])
    ->name('motor.reviews.store');
    

    Route::middleware('auth')->group(function () {
    Route::get('/jual-motor', [MotorController::class, 'create'])->name('motor.create');
    Route::post('/jual-motor', [MotorController::class, 'store'])->name('motor.store');
});

Route::post('/motor/{motor}/like-toggle', [MotorLikeController::class, 'toggle'])
    ->name('motor.like.toggle')
    ->middleware('auth');

    Route::get('/lihat-semua-motor', [MotorController::class, 'index'])
    ->name('motor.index');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

    // ✅ taruh ini DI ATAS route /chat/{user}
    Route::get('/chat/threads', [ChatController::class, 'threads'])->name('chat.threads');

    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{user}/messages', [ChatController::class, 'fetch'])->name('chat.messages');
    Route::post('/chat/{user}/send', [ChatController::class, 'send'])->name('chat.send');
});

Route::get('/sewa', [SewaController::class, 'index'])->name('sewa.index');

Route::get('/sewa/mobil/{mobilSewa}', [SewaController::class, 'showMobil'])->name('sewa.mobil.show');
Route::get('/sewa/motor/{motorSewa}', [SewaController::class, 'showMotor'])->name('sewa.motor.show');