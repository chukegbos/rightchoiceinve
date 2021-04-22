<?php

use Illuminate\Http\Request;

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
	
	
	Route::get('setting', 'API\UserController@setting');
	Route::get('all', 'API\UserController@all');
	Route::put('setting/{id}', 'API\UserController@updateSetting');
	Route::get('stat', 'API\DashboardController@stat');
	Route::get('searchcustomer', 'API\CustomerController@searchcustomer');
	Route::get('allusers', 'API\UserController@allusers');

	Route::group(['prefix' => 'user'], function(){
		Route::group(['prefix' => 'dashboard'], function(){
	    	Route::get('rep', 'API\DashboardController@rep');
	    	Route::get('sales', 'API\DashboardController@totalSales');
		});

		Route::get('bank', 'API\UserController@bank');
		Route::post('createpin', 'API\UserController@createpin');
	    Route::post('fetchbank', 'API\UserController@fetchbank');
	    Route::put('password', 'API\UserController@password');
	    Route::get('/', 'API\UserController@getUser');
	    Route::get('{id}', 'API\UserController@viewUser');

	    Route::post('credituser', 'API\UserController@credituser');
	    Route::post('walletuser', 'API\UserController@walletuser');
	});

	Route::group(['prefix' => 'cart'], function(){
    	Route::get('shopping/{sale_id}', 'API\CartController@testqty');
    	Route::get('testqty', 'API\CartController@testqty');
    	Route::get('getorder/{id}', 'API\DashboardController@getorder');
    	Route::post('setqty', 'API\CartController@setqty');
    	Route::get('addCart', 'API\CartController@addToCart');
    	Route::get('addCart/{id}', 'API\CartController@addCart');
    	Route::get('getCart', 'API\CartController@getCart');
    	Route::get('addOne/{id}', 'API\CartController@addOne');
    	Route::get('reduceOne/{id}', 'API\CartController@reduceOne');
    	Route::get('reduceAll/{id}', 'API\CartController@reduceAll');
    	Route::post('checkout', 'API\CartController@checkout');
    	Route::put('checkout/{id}', 'API\CartController@updateCheckout');
    	Route::post('approvequote', 'API\CartController@approvequote');
    	Route::post('closedeal', 'API\CartController@closedeal');
    	Route::get('cancel', 'API\CartController@cancel');
	});

	Route::group(['prefix' => 'purchases'], function(){
    	Route::get('/', 'API\PurchaseController@index');
    	Route::post('/', 'API\PurchaseController@store');
    	Route::put('{id}', 'API\PurchaseController@update');
    	Route::get('{id}', 'API\PurchaseController@show');
    	Route::delete('{id}', 'API\PurchaseController@destroy');
	});

	Route::group(['prefix' => 'production'], function(){
    	Route::group(['prefix' => 'used'], function(){
	    	Route::get('/', 'API\ProductionUsedController@index');
	    	Route::post('/', 'API\ProductionUsedController@store');
	    	Route::put('{id}', 'API\ProductionUsedController@update');
	    	Route::get('{prod_id}', 'API\ProductionUsedController@show');
	    	Route::delete('{id}', 'API\ProductionUsedController@destroy');
		});

		Route::get('/', 'API\ProductionController@index');
    	Route::post('/', 'API\ProductionController@store');
    	Route::put('{id}', 'API\ProductionController@update');
    	Route::get('{prod_id}', 'API\ProductionController@show');
    	Route::delete('{id}', 'API\ProductionController@destroy');
	});
	
    Route::group(['prefix' => 'store'], function(){
    	Route::post('gettotal', 'API\StoreController@gettotal');
    	Route::get('makerequest', 'API\StoreRequestController@makerequest');
    	Route::get('getrequest/{req_id}', 'API\StoreRequestController@getrequest');
    	Route::get('viewrequest/{req_id}', 'API\StoreRequestController@viewrequest');
    	Route::get('request', 'API\StoreRequestController@index');
    	Route::get('delete', 'API\StoreController@destroy');
    	Route::get('loadinventory', 'API\StoreController@loadinventory');
    	Route::delete('request/{id}', 'API\StoreRequestController@destroy');
    	Route::put('request/{id}', 'API\StoreRequestController@edit');
    	Route::post('/accept', 'API\StoreController@acceptall');
    	Route::get('/accept/{id}', 'API\StoreController@accept');
    	Route::get('/inventory', 'API\StoreController@getInventory');
    	Route::get('/tradeinventory', 'API\StoreController@tradeinventory');
    	
    	Route::get('/allinventory', 'API\StoreController@allInventory');
    	Route::get('/reports', 'API\StoreController@reports');
    	Route::get('/orders', 'API\StoreController@orders');
    	Route::get('/quotes', 'API\StoreController@quotes');
    	Route::get('/invoice', 'API\StoreController@invoice');
    	Route::get('/debtors', 'API\StoreController@debtors');
    	Route::post('/debt', 'API\StoreController@addDebt');
    	Route::get('/debtors/{sale_id}', 'API\StoreController@debtorview');


    	Route::get('{code}', 'API\StoreController@getStore');
    	Route::get('{code}/{id}', 'API\StoreController@show');

    	Route::get('room/{code}', 'API\StoreController@getRoom');
    	Route::get('room/{code}/{id}', 'API\StoreController@showroom');
	});


    Route::group(['prefix' => 'movement'], function(){
    	Route::get('/store', 'API\StoreController@storemovement');
    	Route::get('/store/reject', 'API\StoreController@srejectall');
    	Route::get('/store/accept', 'API\StoreController@sacceptall');
	});


	Route::group(['prefix' => 'cashier'], function(){
	    Route::get('', 'API\SalesRepController@index');
	    Route::post('', 'API\SalesRepController@store');
	    Route::put('{id}', 'API\SalesRepController@update');
	    Route::delete('{id}', 'API\SalesRepController@destroy');
    });

    Route::group(['prefix' => 'customer'], function(){
	    Route::get('/', 'API\CustomerController@index');
	    Route::get('/delete', 'API\CustomerController@delete');
	    Route::get('/edit/{id}', 'API\CustomerController@edit');
	    Route::get('{unique_id}', 'API\CustomerController@view');
	    Route::post('', 'API\CustomerController@store');
	    Route::put('{id}', 'API\CustomerController@update');
	    Route::delete('{id}', 'API\CustomerController@destroy');
    });

    Route::group(['prefix' => 'admin'], function(){
	    Route::get('', 'API\UserController@index');
	    Route::post('', 'API\UserController@store');
	    Route::post('settrequest', 'API\StoreController@storereq');
	    Route::post('updaterequest', 'API\StoreController@updatereq');
	    Route::get('delete', 'API\UserController@destroyall');
	    Route::put('{id}', 'API\UserController@update');
	    Route::delete('{id}', 'API\UserController@destroy');
	    Route::get('inventory', 'API\InventoryController@getInventory');
	    Route::get('sales', 'API\DashboardController@orders');
    	Route::get('/order-report', 'API\DashboardController@orderReport');
    	Route::get('/discharge', 'API\StoreController@discharge');
    	Route::put('/discharge/{id}', 'API\StoreController@approve');
    	Route::get('/discharge/{id}', 'API\StoreController@approve');
    	Route::get('/decline/{id}', 'API\StoreController@decline');
    });

    //Bar manager route
    Route::group(['prefix' => 'manager'], function(){
	    Route::get('', 'API\ManagerController@index');
	    Route::post('', 'API\ManagerController@store');
	    Route::put('{id}', 'API\ManagerController@update');
	    Route::delete('{id}', 'API\ManagerController@destroy');
    });

    /** Inventory Route */
	Route::group(['prefix' => 'inventory'], function(){
		Route::group(['prefix' => 'category'], function(){
		    Route::get('', 'API\CategoryController@index');
		    Route::get('delete', 'API\CategoryController@destroy');
		    Route::get('{id}', 'API\CategoryController@show');
		    Route::post('', 'API\CategoryController@store');
		    Route::put('{id}', 'API\CategoryController@update');
		    Route::delete('{id}', 'API\CategoryController@destroy');
	    });

	    Route::get('', 'API\InventoryController@index');
	    Route::post('/increase', 'API\InventoryController@increase');
	    Route::get('delete', 'API\InventoryController@destroy');
	    Route::get('{id}', 'API\InventoryController@show');
	    Route::post('', 'API\InventoryController@store');
	    Route::put('{id}', 'API\InventoryController@update');
	    
	    Route::delete('{id}', 'API\InventoryController@destroy');
    });
   
    Route::apiResources(['store' => 'API\StoreController']);

    /** Account Route */
	Route::group(['prefix' => 'account'], function(){
	    Route::get('', 'API\AccountController@index');
	    Route::get('search', 'API\AccountController@search');
	    Route::get('trialbalance', 'API\AccountController@trialbalance');
	    Route::get('profitloss', 'API\AccountController@profitloss');
	    Route::get('balancesheet', 'API\AccountController@balancesheet');
	    Route::get('{id}', 'API\AccountController@show');
	    Route::post('', 'API\AccountController@store');
	    Route::put('{id}', 'API\AccountController@update');
    });

    Route::group(['prefix' => 'type'], function(){
	    Route::get('', 'API\AccountTypeController@index');
	    Route::get('/search', 'API\AccountTypeController@search');
	    Route::get('{id}', 'API\AccountTypeController@show');
	    Route::post('', 'API\AccountTypeController@store');
	    Route::put('{id}', 'API\AccountTypeController@update');
	    Route::delete('{id}', 'API\AccountTypeController@destroy');
    });

    Route::group(['prefix' => 'tax'], function(){
	    Route::get('', 'API\AccountTaxController@index');
	    Route::get('/search', 'API\AccountTaxController@search');
	    Route::get('{id}', 'API\AccountTaxController@show');
	    Route::post('', 'API\AccountTaxController@store');
	    Route::put('{id}', 'API\AccountTaxController@update');
	    Route::delete('{id}', 'API\AccountTaxController@destroy');
    });

    Route::group(['prefix' => 'ledger'], function(){
	    Route::get('', 'API\LedgerController@index');
	    Route::get('/search', 'API\LedgerController@search');
	    Route::get('{id}', 'API\LedgerController@show');
	    Route::post('', 'API\LedgerController@store');
	    Route::put('{id}', 'API\LedgerController@update');
	    Route::delete('{id}', 'API\LedgerController@destroy');
    });

    Route::group(['prefix' => 'funding'], function(){
	    Route::get('', 'API\UserController@funding');
    });

    Route::group(['prefix' => 'suppliers'], function(){
    	Route::get('/', 'API\SupplierController@index');
    	Route::get('delete', 'API\SupplierController@destroy');
    	Route::get('all', 'API\SupplierController@allSuppliers');
    	Route::get('{id}', 'API\SupplierController@singleSupplier');
    	Route::post('/', 'API\SupplierController@store');
    	Route::put('{id}', 'API\SupplierController@update');
        Route::delete('{id}', 'API\SupplierController@destroy');
    });