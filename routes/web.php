<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');

Route::get('/collection', [CollectionController::class, 'index'])->name('collection.index');

Route::get('/create/asa', [AssetController::class, 'create'])->name('asa.create');
Route::post('/asa/transaction/fields', [AssetController::class, 'getTransactionFields']);
Route::post('/asa/transaction', [AssetController::class, 'store']);

Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
Route::get('/wallet/install', [WalletController::class, 'create'])->name('wallet.install');

Route::post('/signin', [WalletController::class, 'signIn'])->name('wallet.signin');
Route::get('/logout', [WalletController::class, 'logout'])->name('wallet.logout');


