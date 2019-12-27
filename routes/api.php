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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// /api/tap
Route::group(['prefix' => 'tap'], function() {
    // /api/tap/{machineIp}/{rfid}
    Route::get('{machineIp}/{rfid}', 'TapCardController@tap');
});

// /api/account
Route::group(['prefix' => 'account', 'middleware' => 'auth:api'], function() {
    // /api/account/{id}|self/update-profile
    Route::post('{id}/update-profile', 'AccountsController@updateProfile');

    // /api/account/{id}|self/update-email
    Route::post('{id}/update-email', 'AccountsController@updateEmail');

    // /api/account/{id}|self/update-password
    Route::post('{id}/update-password', 'AccountsController@updatePassword');

    // /api/account/{id}|self/get-account-info
    Route::get('{id}/get-account-info', 'AccountsController@getAccountInfo');
});

// /api/users
Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function() {
    // /api/users
    Route::post('create', 'UsersController@create');

    // /api/users/{userId}/assign-role
    Route::post('{userId}/assign-role', 'UsersController@assignRole');
});

// /api/products
Route::group(['prefix' => 'products'], function() {
    // /api/products
    Route::get('/', 'ProductsController@index');
});


// /api/customers
Route::group(['prefix' => 'customers', 'middleware' => 'auth:api'], function() {
    // /api/customers
    Route::post('create', 'CustomersController@store');

    // /api/customers/{customerId}/loyalty-services
    Route::get('{customerId}/loyalty-services', 'PaymentsController@loyaltyServices');

    // /api/customers/{customerId}/with-tokens
    Route::get('{customerId}/with-tokens', 'CustomersController@customerWithTokens');

    // /api/customers/{customerId}/with-services
    Route::get('{customerId}/with-services', 'CustomersController@customerWithServices');

    // /api/customers/{customerId}/update
    Route::post('{customerId}/update', 'CustomersController@update');
});

// /api/products
Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function() {
    // /api/products/{id}
    Route::get('{id}', 'ProductsController@show');

    // /api/products/{productId}/add-stock
    Route::post('{productId}/add-stock', 'ProductsController@addStock');

    Route::group(['middleware' => 'role:admin'], function() {
        // /api/products/create
        Route::post('create', 'ProductsController@store');

        // /api/products/{id}/update
        Route::post('{id}/update', 'ProductsController@update');

        // /api/products/{id}/set-picture
        Route::post('{id}/set-picture', 'ProductsController@setPicture');

        // /api/products/{id}/delete-product
        Route::post('{id}/delete-product', 'ProductsController@deleteProduct');

        // /api/products/{id}/remove-picture
        Route::post('{id}/remove-picture', 'ProductsController@removePicture');
    });
});

// /api/services
Route::group(['prefix' => 'services', 'middleware' => 'auth:api'], function() {
    // /api/services/create
    Route::post('create', 'ServicesController@store');

    // /api/services/{id}
    Route::get('{id}', 'ServicesController@show');

    // /api/services/{id}
    Route::post('{id}/update', 'ServicesController@update');
});

// /api/transactions
Route::group(['prefix' => 'transactions', 'middleware' => 'auth:api'], function() {
    // /api/transactions/rfid-card/{rfidCardId}/top-up
    Route::post('rfid-card/{rfidCardId}/top-up', 'RfidTransactionsController@topUp');

    // /api/transactions/unpaid/{customerId}
    Route::get('unpaid/{customerId}', 'TransactionsController@customerTransaction');

    // /api/transaction/service-item/{serviceItemId}/remove
    Route::post('service-item/{serviceItemId}/remove', 'TransactionsController@removeServiceItem');

    // /api/transaction/product-item/{productItemId}/remove
    Route::post('product-item/{productItemId}/remove', 'TransactionsController@removeProductItem');

    // /api/transactions/{id}/add-order
    Route::post('{id}/add-order', 'TransactionsController@addOrder');

    // /api/transactions/{id}/add-service
    Route::post('{id}/add-service', 'TransactionsController@addService');

    // /api/transactions/{transactionId}/save-current-transaction
    Route::post('{transactionId}/save-current-transaction', 'TransactionsController@saveCurrentTransaction');
});

// /api/payments
Route::group(['prefix' => 'payments'], function() {
    // /api/payments/{transactionId}
    Route::get('{transactionId}', 'PaymentsController@transactionPayment');

    // /api/payments/{transactionId}/proceed
    Route::post('{transactionId}/proceed', 'PaymentsController@proceedToPayment');
});


// /api/machines
Route::group(['prefix' => 'machines', 'middleware' => ['auth:api']], function() {
    Route::group(['middleware' => 'role:developer'], function() {
        // /api/machines/{branchId}/store
    });

    // /api/machines/view-all
    Route::get('{branchId}/view-all', 'MachinesController@index');

    // /api/machines/last-activated
    Route::get('last-activated', 'MachinesController@lastActivated');
});

// /api/remote
Route::group(['prefix' => 'remote', 'middleware' => ['auth:api']], function() {
    // /api/remote/machines/{machineId}/activate
    Route::post('machines/{machineId}/activate', 'RemotesController@activate');
});

// /api/expenses
Route::group(['prefix' => 'expenses', 'middleware' => 'auth:api'], function() {
    // /api/expenses/store
    Route::post('store', 'ExpensesController@store');
});


// /api/search
Route::group(['prefix' => 'search', 'middleware' => 'auth:api'], function() {
    // /api/search/users
    Route::get('users', 'UsersController@index')->middleware('self:userId');

    // /api/search/products
    Route::get('products', 'ProductsController@index');

    // /api/search/pos-services
    Route::get('pos-services', 'ServicesController@posIndex');

    // /api/search/services
    Route::get('services', 'ServicesController@index');

    // /api/search/customers
    Route::get('customers', 'CustomersController@index');

    // /api/search/rfid-cards/{master|customer}
    Route::get('rfid-cards/{rfidType}', 'RfidCardsController@index');

    // /api/search/product-purchases
    Route::get('product-purchases', 'ProductPurchasesController@index');

    // /api/search/expenses
    Route::get('expenses', 'ExpensesController@index');

    // /api/search/discounts
    Route::get('discounts', 'DiscountsController@index');

    // /api/search/trashed
    Route::group(['prefix' => 'trashed'], function() {
        // /api/search/trashed/transactions
        Route::get('transactions', 'TrashedController@byCustomer');

        // /api/search/trashed/transactions-services
        Route::get('transaction-services', 'TrashedController@services');

        // /api/search/trashed/transactions-products
        Route::get('transaction-products', 'TrashedController@products');
    });

    // /api/search/transactions
    Route::group(['prefix' => 'transactions'], function() {
        // /api/search/transactions/rfid-services
        Route::get('rfid-services', 'RfidTransactionsController@rfidServiceIndex');

        // /api/search/transactions/rfid-top-up
        Route::get('rfid-top-up', 'RfidTransactionsController@topUpIndex');

        // /api/search/transactions/pos/by-customers
        Route::get('pos/by-customers', 'ReportsController@posTransactionsByCustomers');

        // /api/search/transactions/pos/by-items
        Route::get('pos/by-items', 'ReportsController@posTransactionsByItems');

        // /api/search/transactions/pos/services
        Route::get('pos/services', 'ReportsController@posServices');

        // /api/search/transactions/pos/products
        Route::get('pos/products', 'ReportsController@posProducts');

    });
});

// /api/rfid
Route::group(['prefix' => 'rfid', 'middleware' => 'auth:api'], function() {
    // /api/rfid/cards
    Route::group(['prefix' => 'cards'], function() {
        // /api/rfid/card/create
        Route::post('create', 'RfidCardsController@store');

        // /api/rfid/card/{id}/update
        Route::post('{id}/update', 'RfidCardsController@update');
    });
});

// /api/service-prices
Route::group(['prefix' => 'service-prices', 'middleware' => 'auth:api'], function() {
    // /api/service-prices/{servicePriceId}/update
    Route::post('{servicePriceId}/update', 'RfidServicePricesController@update');
});

// /api/discounts
Route::group(['prefix' => 'discounts', 'middleware' => ['auth:api', 'role:admin']], function() {
    // /api/discounts/create
    Route::post('create', 'DiscountsController@store');

    // /api/discounts/{id}/create
    Route::post('{id}/update', 'DiscountsController@update');

    // /api/discounts/{id}/delete
    Route::post('{id}/delete', 'DiscountsController@delete');
});

// /api/loyalty-points
Route::group(['prefix' => 'loyalty-points', 'middleware' => ['auth:api', 'role:admin']], function() {
    // /api/loyalty-points/get
    Route::get('get', 'LoyaltyPointsController@show');

    // /api/loyalty-points/update
    Route::post('update', 'LoyaltyPointsController@update');

    // /api/loyalty-points/create
    Route::post('create', 'LoyaltyPointsController@store');
});

// /api/job-order
Route::group(['prefix' => 'job-order', 'middleware' => ['auth:api', 'role:admin']], function() {
    // /api/job-order/get
    Route::get('get', 'JobOrdersController@show');

    // /api/job-order/update
    Route::post('update', 'JobOrdersController@update');
});

// /api/void-transaction
Route::group(['prefix' => 'void-transaction', 'middleware' => 'auth:api'], function() {
    // /api/void-transaction/{completedServiceTransactionId}/void-service
    Route::post('{completedServiceTransactionId}/void-service', 'VoidTransactionsController@voidService');

    // /api/void-transaction/{completedServiceTransactionId}/void-product
    Route::post('{completedProductTransactionId}/void-product', 'VoidTransactionsController@voidProduct');

    // /api/void-transaction/{transactionId}/void-transaction
    Route::post('{transactionId}/void-transaction', 'VoidTransactionsController@voidTransaction');

    // /api/void-transaction/{transactionId}
    Route::get('{transactionId}', 'VoidTransactionsController@getTransaction');
});

// /api/autocomplete
Route::group(['prefix' => 'autocomplete'], function() {
    // /api/autocomplete/customers
    Route::get('customers', 'CustomersController@autocomplete');

    // /api/autocomplete/usres
    Route::get('users', 'UsersController@autocomplete');

    // /api/autocomplete/expense-types
    Route::get('expense-types', 'ExpensesController@autocomplete');
});

// /api/all
Route::group(['prefix' => 'all', 'middleware' => 'auth:api'], function() {
    // /api/all/rfid/service-prices
    Route::get('rfid/service-prices', 'RfidServicePricesController@index');

    // /api/all/discounts
    Route::get('discounts', 'DiscountsController@all');
});

// /api/exports/pos-services
Route::group(['prefix' => 'exports', 'middleware' => 'auth:api'], function () {
    // /api/exports/pos-services
    Route::get('pos-services', 'ExcelReportsController@posServices');

    // /api/exports/pos-products
    Route::get('pos-products', 'ExcelReportsController@posProducts');

    // /api/exports/pos-job-order
    Route::get('pos-job-order', 'ExcelReportsController@posJobOrder');

    // /api/exports/rfid-transactions
    Route::get('rfid-transactions', 'ExcelReportsController@rfidServices');

    // /api/exports/rfid-topups
    Route::get('rfid-topups', 'ExcelReportsController@rfidTopups');
});

// /api/print
Route::group(['prefix' => 'print'], function() {
    // /api/print/receipt/{transactionId}
    Route::get('receipt/{transactionId}', 'PrinterController@printReceipt');
});

// oauth
Route::group(['prefix' => 'oauth', 'middleware' => 'auth:api'], function() {
    // oauth/check
    Route::get('check', 'OAuthController@check');

    // oauth/logout
    Route::post('logout', 'OAuthController@logout');
});

Route::group(['prefix' => 'oauth'], function() {
    Route::post('login', 'OAuthController@login');
});

Route::group(['prefix' => 'mail'], function () {
    Route::get('send', 'MailsController@send');
});

// /api/live
Route::group(['prefix' => 'live'], function() {
    // /api/live/register-owner
    Route::get('register-owner', 'LiveHostController@registerOwner');
});


Route::any('{any}', function() {
    return response()->json(['message' => 'Resource not found'], 404);
})->where('any', '.*');
// Route::group(['prefix' => 'manage', 'middleware' => ['auth:api', 'scopes:add-branch']], function() {
    // Route::get('branch/add/{userId}', function() {
    //     return response()->json([
    //         'keme' => 'lang',
    //         'uaser' => auth()->user()
    //     ]);
    // })->middleware(['auth:api', 'role:admin,keme', 'self']);
// });
