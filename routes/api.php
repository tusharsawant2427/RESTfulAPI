<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('users', 'App\Http\Controllers\User\UsersController');
Route::resource('buyers', 'App\Http\Controllers\Buyer\BuyersController', ['only' => ['index', 'show']]);
Route::resource('sellers', 'App\Http\Controllers\Seller\SellersController', ['only' => ['index', 'show']]);
Route::resource('categories', 'App\Http\Controllers\Category\CategoriesController');
Route::resource('products', 'App\Http\Controllers\Product\ProductsController');
Route::resource('transactions', 'App\Http\Controllers\Transaction\TransactionsController');
Route::resource('transactions.categories', 'App\Http\Controllers\Transaction\TransactionCategoryController')->only('index');
Route::resource('transactions.sellers', 'App\Http\Controllers\Transaction\TransactionSellersController')->only('index');
Route::resource('buyers.transactions', 'App\Http\Controllers\Buyer\BuyerTransactionsController')->only('index');
Route::resource('buyers.products', 'App\Http\Controllers\Buyer\BuyerProductsController')->only('index');
Route::resource('buyers.sellers', 'App\Http\Controllers\Buyer\BuyerSellersController')->only('index');
Route::resource('buyers.categories', 'App\Http\Controllers\Buyer\BuyerCategoriesController')->only('index');
Route::resource('categories.products', 'App\Http\Controllers\Category\CategoryProductsController')->only('index');
Route::resource('categories.sellers', 'App\Http\Controllers\Category\CategorySellersController')->only('index');
Route::resource('categories.transactions', 'App\Http\Controllers\Category\CategoryTransactionsController')->only('index');
Route::resource('categories.buyers', 'App\Http\Controllers\Category\CategoryBuyersController')->only('index');
Route::resource('sellers.transactions', 'App\Http\Controllers\Seller\SellerTransactionsController')->only('index');
Route::resource('sellers.categories', 'App\Http\Controllers\Seller\SellerCategoriesController')->only('index');
Route::resource('sellers.buyers', 'App\Http\Controllers\Seller\SellerBuyersController')->only('index');
Route::resource('sellers.products', 'App\Http\Controllers\Seller\SellerProductsController')->except('create', 'edit', 'show');
Route::resource('products.transactions', 'App\Http\Controllers\Product\ProductTransactionsController')->only('index');
Route::resource('products.buyers', 'App\Http\Controllers\Product\ProductBuyerController')->only('index');
Route::resource('products.categories', 'App\Http\Controllers\Product\ProductCategoryController')->only('index', 'update', 'destroy');
Route::resource('products.buyers.transactions', 'App\Http\Controllers\Product\ProductBuyerTransactionsController')->only('store');
Route::get('users/verify/{token}', ['App\Http\Controllers\User\UsersController', 'verify'])->name('verify');
Route::get('users/{user}/resend', ['App\Http\Controllers\User\UsersController', 'resend'])->name('resend');
Passport::routes();
