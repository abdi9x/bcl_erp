<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\RoomsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RenterController;
use App\Http\Controllers\tr_renterController;
use App\Http\Controllers\FinJurnalController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\RoomCategoryImageController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/home', function () {
//     return view('home');
// })->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'api'], function () {
        // Route::get('/rooms', [RoomsController::class, 'api_rooms'])->name('api.rooms');
    });

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/resetpassword', [UsersController::class, 'resetpassword'])->name('resetpassword');
    Route::group(['middleware' => ['role:Administrator|Keuangan|Admin Kamar']], function () {
        Route::any('/finance/income', [FinJurnalController::class, 'index'])->name('income.index');
        Route::get('/finance/income/delete/{id}', [FinJurnalController::class, 'income_delete'])->name('income.delete');
        Route::any('/finance/expense', [FinJurnalController::class, 'expense'])->name('expense.index');
        Route::get('/finance/expense/view/{id}', [FinJurnalController::class, 'expense_show'])->name('expense.show');
        Route::get('/finance/expense/delete/{id}', [FinJurnalController::class, 'expense_delete'])->name('expense.delete');
        Route::group(['middleware' => ['permission:Pemasukan']], function () {
            Route::post('/finance/income/store', [FinJurnalController::class, 'store'])->name('income.store');
        });
        Route::group(['middleware' => ['permission:Pengeluaran']], function () {
            Route::post('/finance/expense/store', [FinJurnalController::class, 'store_expense'])->name('expense.store');
        });
    });

    Route::group(['middleware' => ['role:Administrator|Admin Kamar|Keuangan']], function () {
        route::get('/inventories', [InventoriesController::class, 'index'])->name('inventories.index');
        route::get('/inventories/show/{id}', [InventoriesController::class, 'show'])->name('inventories.show');
        Route::group(['middleware' => ['permission:Tambah Inventaris|Edit Inventaris|Hapus Inventaris']], function () {
            route::post('/inventories/store', [InventoriesController::class, 'store'])->name('inventories.store');
            route::get('/inventories/delete/{id}', [InventoriesController::class, 'destroy'])->name('inventories.delete');
            route::get('/inventories/edit/{id}', [InventoriesController::class, 'edit'])->name('inventories.edit');
            route::post('/inventories/update', [InventoriesController::class, 'update'])->name('inventories.update');
        });
        Route::get('/renter', [RenterController::class, 'index'])->name('renter.index');
        Route::group(['middleware' => ['permission:Tambah Penyewa|Edit Penyewa|Hapus Penyewa']], function () {
            Route::post('/renter/store', [RenterController::class, 'store'])->name('renter.store');
            Route::get('/renter/delete/{id}', [RenterController::class, 'destroy'])->name('renter.delete');
            Route::get('/renter/edit/{id}', [RenterController::class, 'edit'])->name('renter.edit');
            Route::post('/renter/update', [RenterController::class, 'update'])->name('renter.update');
        });

        Route::group(['prefix' => 'pricelist'], function () {
            Route::get('/', [PricelistController::class, 'index'])->name('pricelist.index');
            Route::group(['middleware' => ['permission:Tambah Pricelist|Edit Pricelist|Hapus Pricelist']], function () {
                Route::post('/store', [PricelistController::class, 'store'])->name('pricelist.store');
                Route::get('/edit/{id}', [PricelistController::class, 'edit'])->name('pricelist.edit');
                Route::get('/delete/{id}', [PricelistController::class, 'destroy'])->name('pricelist.delete');
                Route::post('/update', [PricelistController::class, 'update'])->name('pricelist.update');
            });
            Route::get('/rooms/{id}', [PricelistController::class, 'get_room_pricelist'])->name('pricelist.get_pl_room');
        });
        Route::group(['middleware' => ['permission:Tambah Kamar|Edit Kamar|Hapus Kamar']], function () {
            Route::post('/rooms/store', [RoomsController::class, 'store'])->name('rooms.store');
            Route::get('/rooms/edit/{id}', [RoomsController::class, 'edit'])->name('rooms.edit');
            Route::post('/rooms/update', [RoomsController::class, 'update'])->name('rooms.update');
            Route::get('/rooms/delete/{id}', [RoomsController::class, 'destroy'])->name('rooms.delete');

            Route::get('/category', [RoomCategoryController::class, 'index'])->name('category');
            Route::get('/category/edit/{id}', [RoomCategoryController::class, 'edit'])->name('category.edit');
            Route::get('/category/delete/{id}', [RoomCategoryController::class, 'destroy'])->name('category.delete');
            Route::post('/category/update', [RoomCategoryController::class, 'update'])->name('category.update');
            Route::post('/category/store', [RoomCategoryController::class, 'store'])->name('category.store');
            Route::post('/images/store', [RoomCategoryImageController::class, 'store'])->name('images.store');
            Route::get('/images/delete/{id}', [RoomCategoryImageController::class, 'destroy'])->name('images.delete');
        });
        Route::post('/rooms/sewa', [tr_renterController::class, 'sewa'])->name('rooms.sewa');
        Route::get('/rooms', [RoomsController::class, 'index'])->name('rooms');
        ROute::any('/transaksi', [tr_renterController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/show/{id}', [tr_renterController::class, 'show'])->name('transaksi.show');
        Route::get('/transaksi/delete/{id}', [tr_renterController::class, 'destroy'])->name('transaksi.delete');
        Route::post('/transaksi/refund', [tr_renterController::class, 'refund'])->name('transaksi.refund');
        Route::post('/transaksi/reschedule', [tr_renterController::class, 'reschedule'])->name('transaksi.reschedule');
        Route::get('/transaksi/cetak/{id}', [tr_renterController::class, 'cetak'])->name('transaksi.cetak');
    });
    Route::group(['middleware' => ['role:Administrator']], function () {
        route::get('/users', [UsersController::class, 'index'])->name('users.index');
        route::post('/users/store', [UsersController::class, 'store'])->name('users.store');
        route::get('/users/delete/{id}', [UsersController::class, 'destroy'])->name('users.delete');
        route::get('/users/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
        route::post('/users/update', [UsersController::class, 'update'])->name('users.update');
        route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        route::get('/roles/delete/{id}', [RoleController::class, 'destroy'])->name('roles.delete');

        route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
        route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');
        route::get('/permission/delete/{id}', [PermissionController::class, 'destroy'])->name('permission.delete');
    });
});
