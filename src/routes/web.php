<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RestaurantInfoController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RestaurantController::class, 'index']);
Route::get('/detail', [RestaurantController::class, 'detail']);
Route::get('/search', [RestaurantController::class, 'search']);
Route::get('/two-factor-auth', [AuthenticationController::class, 'twoFactorAuthView']);
Route::post('/two-factor-auth', [AuthenticationController::class, 'twoFactorAuth']);

Route::get('/pro/search', [ProController::class, 'searchSort']);
Route::get('/pro/sort', [ProController::class, 'searchSort']);

Route::middleware('auth')->group(
    function () {
        Route::get('/thanks', [AuthenticationController::class, 'thanks']);
        Route::get('/thanks/login', [AuthenticationController::class, 'thanksLogin']);
        Route::get('/two-factor-auth/wait', [AuthenticationController::class, 'twoFactorAuthWaitView']);
        Route::post('/two-factor-auth/wait/mail', [AuthenticationController::class, 'twoFactorAuthMail']);
    }
);

Route::middleware(['auth', 'two.factor.auth'])->group(
    function () {
        Route::get('/two-factor-auth/next', [AuthenticationController::class, 'twoFactorAuthNext']);
        Route::get('/mypage', [RestaurantController::class, 'mypage']);
        Route::get('/reservation/change', [ReservationController::class, 'reservationChangeView']);
        Route::get('/review', [ReviewController::class, 'review']);
        Route::post('/favorite/add', [RestaurantController::class, 'favoriteAdd']);
        Route::post('/favorite/delete', [RestaurantController::class, 'favoriteDelete']);
        Route::post('/reservation', [ReservationController::class, 'reservation']);
        Route::delete('/reservation/delete', [ReservationController::class, 'reservationDelete']);
        Route::patch('/reservation/change', [ReservationController::class, 'reservationChange']);
        Route::post('/review/post', [ReviewController::class, 'reviewPost']);
        Route::get('/card', [CardController::class, 'card']);
        Route::get('/card/create', [CardController::class, 'cardCreateView']);
        Route::post('/card/create', [CardController::class, 'cardCreate']);
        Route::post('/card/delete', [CardController::class, 'cardDelete']);
        Route::get('/card/update', [CardController::class, 'cardUpdateView']);
        Route::post('/card/update', [CardController::class, 'cardUpdate']);
        Route::get('/done', [ReservationController::class, 'reservationDone']);

        Route::get('/pro/review', [ProController::class, 'review']);
        Route::post('/pro/review/post', [ProController::class, 'reviewPost']);
        Route::post('/pro/favorite/add', [ProController::class, 'favoriteAdd']);
        Route::post('/pro/favorite/delete', [ProController::class, 'favoriteDelete']);
        Route::get('/pro/review/edit', [ProController::class, 'reviewEditView']);
        Route::patch('/pro/review/edit', [ProController::class, 'reviewEdit']);
        Route::delete('/pro/review/delete', [ProController::class, 'reviewDelete']);
    }
);

Route::middleware(['auth', 'two.factor.auth', 'permission:admin'])->group(
    function () {
        Route::get('/representative/register', [AdminController::class, 'representativeRegisterView']);
        Route::post('/representative/register', [AdminController::class, 'representativeRegister']);
        Route::get('/notification', [AdminController::class, 'notificationView']);
        Route::post('/notification', [AdminController::class, 'notification']);

        Route::get('/pro/csv', [ProController::class, 'Csv']);
        Route::post('/pro/csv/import', [ProController::class, 'CsvImport']);
    }
);

Route::middleware(['auth', 'two.factor.auth', 'permission:restaurant'])->group(
    function () {
        Route::get('/restaurant/create', [RestaurantInfoController::class, 'restaurantCreateView']);
        Route::post('/restaurant/create', [RestaurantInfoController::class, 'restaurantCreate']);
        Route::get('/restaurant/edit', [RestaurantInfoController::class, 'restaurantEditView']);
        Route::patch('/restaurant/edit', [RestaurantInfoController::class, 'restaurantEdit']);
    }
);

Route::middleware(['auth', 'two.factor.auth', 'permission:reservation'])->group(
    function () {
        Route::get('/reservation/record', [ReservationController::class, 'reservationRecord']);
        Route::get('/reservation/qr', [ReservationController::class, 'reservationQr']);
        Route::get('/reservation/confirm', [ReservationController::class, 'reservationConfirm']);
        Route::get('/reservation/payment', [PaymentController::class, 'paymentView']);
        Route::post('/reservation/payment', [PaymentController::class, 'payment']);
    }
);
