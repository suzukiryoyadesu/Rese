<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;

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

Route::middleware('auth')->group(function () {
        Route::get('/mypage', [RestaurantController::class, 'mypage']);
        Route::get('/reservation/change', [RestaurantController::class, 'changeReservationView']);
        Route::get('/review', [RestaurantController::class, 'review']);
        Route::post('/favorite/add', [RestaurantController::class, 'addFavorite']);
        Route::post('/favorite/delete', [RestaurantController::class, 'deleteFavorite']);
        Route::post('/reservation', [RestaurantController::class, 'reservation']);
        Route::delete('/reservation/delete', [RestaurantController::class, 'deleteReservation']);
        Route::patch('/reservation/change', [RestaurantController::class, 'changeReservation']);
        Route::post('/review/post', [RestaurantController::class, 'reviewPost']);
        Route::get('/card', [RestaurantController::class, 'card']);
        Route::get('/card/create', [RestaurantController::class, 'cardCreateView']);
        Route::post('/card/create', [RestaurantController::class, 'cardCreate']);
        Route::post('/card/delete', [RestaurantController::class, 'cardDelete']);
        Route::get('/card/update', [RestaurantController::class, 'cardUpdateView']);
        Route::post('/card/update', [RestaurantController::class, 'cardUpdate']);
        Route::middleware('permission:representative')->group(function () {
                Route::get('/representative/register', [RestaurantController::class, 'representativeRegisterView']);
                Route::post('/representative/register', [RestaurantController::class, 'representativeRegister']);
                Route::get('/notification', [RestaurantController::class, 'notificationView']);
                Route::post('/notification', [RestaurantController::class, 'notification']);
            }
        );
        Route::middleware('permission:restaurant')->group(function () {
                Route::get('/restaurant/create', [RestaurantController::class, 'restaurantCreateView']);
                Route::post('/restaurant/create', [RestaurantController::class, 'restaurantCreate']);
                Route::get('/restaurant/edit', [RestaurantController::class, 'restaurantEditView']);
                Route::patch('/restaurant/edit', [RestaurantController::class, 'restaurantEdit']);
            }
        );
        Route::middleware('permission:reservation')->group(
            function () {
                Route::get('/reservation/record', [RestaurantController::class, 'reservationRecord']);
                Route::get('/reservation/qr', [RestaurantController::class, 'reservationQr']);
                Route::get('/reservation/confirm', [RestaurantController::class, 'reservationConfirm']);
                Route::get('/reservation/payment', [RestaurantController::class, 'paymentView']);
                Route::post('/reservation/payment', [RestaurantController::class, 'payment']);
            }
        );
    }
);

Route::get('/', [RestaurantController::class, 'index']);
Route::get('/detail', [RestaurantController::class, 'detail']);
Route::get('/search', [RestaurantController::class, 'search']);
