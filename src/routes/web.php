<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;

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

Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/thanks', [ShopController::class, 'thanks']);
        Route::get('/', [ShopController::class, 'index']);
        Route::get('/detail', [ShopController::class, 'detail']);
        Route::get('/search', [ShopController::class, 'search']);
        Route::post('/favorite/add', [ShopController::class, 'addFavorite']);
        Route::post('/favorite/delete', [ShopController::class, 'deleteFavorite']);
        Route::post('/reservation', [ShopController::class, 'reservation']);
    }
);
