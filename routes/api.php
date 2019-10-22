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

Route::get('/sync', 'LiveHostController@test');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// /api/daily-reports
Route::group(['prefix' => 'daily-reports', 'middleware' => 'auth:api'], function() {
    // /api/daily-reports/opening-inventories
    Route::get('opening-inventories', 'DailyReportsController@openingInventories');
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
    // /api/users/{clientId}|self
    Route::post('{clientId}/create', 'UsersController@create')->middleware('self:clientId');

    // /api/users/{userId}/assign-role
    Route::post('{userId}/assign-role', 'UsersController@assignRole');
});

// /api/clients[auth,role:developer]
Route::group(['prefix' => 'clients', 'middleware' => ['auth:api', 'role:developer']], function() {
    // /api/clients/create
    Route::post('create', 'ClientsController@store');

    // /api/clients/{id}/update
    Route::post('{id}/update', 'ClientsController@update');

    // /api/clients/{id}/update-client-details
    Route::post('{id}/update-client-details', 'ClientsController@updateClientDetails');

    // /api/clients/{clientId}/delete
    Route::post('{clientId}/delete-client', 'ClientsController@deleteClient');
});

// /api/branches[auth,role:developer,admin]
Route::group(['prefix' => 'branches', 'middleware' => ['auth:api', 'role:admin,developer']], function() {
    // /api/branches/{id}
    Route::get('{id}', 'BranchesController@show');

    // /api/branches/create/{clientId}
    Route::post('create/{clientId}', 'BranchesController@store')->middleware('self:clientId');

    // /api/branches/{id}/update
    Route::post('{id}/update', 'BranchesController@update');
});

// /api/customers
Route::group(['prefix' => 'customers', 'middleware' => 'auth:api'], function() {
    // /api/customers/{clientId}|self
    Route::post('{clientId}/create', 'CustomersController@store');

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
    // /api/products/{userId}|self/create
    Route::post('{userId}/create', 'ProductsController@store');

    // /api/products/{id}
    Route::get('{id}', 'ProductsController@show');

    // /api/products/{id}/update
    Route::post('{id}/update', 'ProductsController@update');

    // /api/products/{productId}/add-stock
    Route::post('{productId}/add-stock', 'ProductsController@addStock');
});

// /api/branch-products
Route::group(['prefix' => 'branch-products', 'middleware' => ['auth:api', 'role:admin']], function() {
    // /api/branch-products/{id}/update-price
    Route::post('{id}/update-price', 'ProductsController@updatePrice');
});

// /api/branch-services
Route::group(['prefix' => 'branch-services', 'middleware' => ['auth:api', 'role:admin']], function() {
    // /api/branch-services/{id}/update-price
    Route::post('{id}/update-price', 'ServicesController@updatePrice');

    // /api/branch-services/{id}/update-branch-service
    Route::post('{id}/update-branch-service', 'ServicesController@updateBranchService');
});

// /api/services
Route::group(['prefix' => 'services', 'middleware' => 'auth:api'], function() {
    // /api/services/{clientId}|self/create
    Route::post('{clientId}/create', 'ServicesController@store');

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

    // /api/machines/{branchId}/view-all
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
    // /api/expenses/{branchId}|self/store
    Route::post('{branchId}/store', 'ExpensesController@store');
});


// /api/search
Route::group(['prefix' => 'search', 'middleware' => 'auth:api'], function() {
    // /api/search/clients/by-branches
    Route::get('clients/by-branches', 'ClientsController@getAllByBranch');

    // /api/search/clients/by-clients
    Route::get('clients/by-clients', 'ClientsController@getAllByClients');

    // /api/search/branches/{userId}|self
    Route::get('branches/{userId}', 'BranchesController@index');

    // /api/search/users/{userId}|self
    Route::get('users/{userId}', 'UsersController@index')->middleware('self:userId');

    // /api/search/products/{userId}|self
    Route::get('products/{userId}', 'ProductsController@index');

    // /api/search/pos-services/{userId}|self
    Route::get('pos-services/{userId}', 'ServicesController@posIndex');

    // /api/search/services/{userId}|self
    Route::get('services/{userId}', 'ServicesController@index');

    // /api/search/customers/{userId}|self
    Route::get('customers/{clientId}', 'CustomersController@index');

    // /api/search/rfid-cards/{master|customer}/{branchId}|self
    Route::get('rfid-cards/{rfidType}/{branchId}', 'RfidCardsController@index');

    // /api/search/product-purchases/{branchId}|self
    Route::get('product-purchases/{branchId}', 'ProductPurchasesController@index');

    // /api/search/expenses/{branchId}|self
    Route::get('expenses/{branchId}', 'ExpensesController@index');

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
        // /api/search/transactions/rfid-services/{branchId}|self
        Route::get('rfid-services/{branchId}', 'RfidTransactionsController@rfidServiceIndex');

        // /api/search/transactions/rfid-top-up/{branchId}|self
        Route::get('rfid-top-up/{branchId}', 'RfidTransactionsController@topUpIndex');

        // /api/search/transactions/pos/by-customers/{clientId}|self/{branchId?}|self
        Route::get('pos/by-customers/{clientId}/{branchId?}', 'ReportsController@posTransactionsByCustomers');

        // /api/search/transactions/pos/by-items/{clientId}|self/{branchId?}|self
        Route::get('pos/by-items/{clientId}/{branchId?}', 'ReportsController@posTransactionsByItems');

        // /api/search/transactions/pos/services/{clientId}|self/{branchId?}|self
        Route::get('pos/services/{clientId}/{branchId?}', 'ReportsController@posServices');

        // /api/search/transactions/pos/products/{clientId}|self/{branchId?}|self
        Route::get('pos/products/{clientId}/{branchId?}', 'ReportsController@posProducts');

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

// /api/dashboard
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth:api', 'role:admin']], function () {
    // /api/dashboard/{branchId}|self
    Route::get('{branchId}', 'DashboardController@index');

    // /api/dashboard/{branchId}|self/expenses
    Route::get('{branchId}/expenses', 'DashboardController@expenses');

    // /api/dashboard/{branchId}|self/income
    Route::get('{branchId}/income', 'DashboardController@income');

    // /api/dashboard/{branchId}|self/liquidate/expenses
    Route::get('{branchId}/liquidate/expenses', 'DashboardController@liquidateExpenses');

    // /api/dashboard/{branchId}|self/liquidate/purchases
    Route::get('{branchId}/liquidate/purchases', 'DashboardController@liquidatePurchases');

    // /api/dashboard/{branchId}|self/liquidate/services
    Route::get('{branchId}/liquidate/services', 'DashboardController@liquidateServices');

    // /api/dashboard/{branchId}|self/liquidate/products
    Route::get('{branchId}/liquidate/products', 'DashboardController@liquidateProducts');

    // /api/dashboard/{branchId}|self/liquidate/rfidTopUps
    Route::get('{branchId}/liquidate/rfidTopUps', 'DashboardController@liquidateRfidTopUps');

    // /api/dashboard/{branchId}|self/liquidate/rfidTransactions
    Route::get('{branchId}/liquidate/rfidTransactions', 'DashboardController@liquidateRfidTransactions');
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
    // /api/search/city-municipalities
    Route::get('city-municipalities', 'CityMunicipalitiesController@autocomplete');

    // /api/autocomplete/barangays
    Route::get('barangays', 'BarangaysController@autocomplete');

    // /api/autocomplete/customers/{clientId|self}
    Route::get('customers/{clientId}', 'CustomersController@autocomplete');

    // /api/autocomplete/usres/{clientId}|self
    Route::get('users/{clientId}', 'UsersController@autocomplete');

    // /api/autocomplete/expense-types
    Route::get('expense-types', 'ExpensesController@autocomplete');
});

// /api/all
Route::group(['prefix' => 'all', 'middleware' => 'auth:api'], function() {
    // /api/all/roles
    Route::get('roles', 'RolesController@all');

    // /api/all/branches/{userId}|self
    Route::get('branches/{userId}', 'BranchesController@all');

    // /api/all/service-types
    Route::get('service-types', 'ServiceTypesController@all');

    // /api/all/rfid/service-prices/{branchId}|self
    Route::get('rfid/service-prices/{branchId}', 'RfidServicePricesController@index');

    // /api/all/discounts
    Route::get('discounts', 'DiscountsController@all');
});

// /api/exports/pos-services
Route::group(['prefix' => 'exports', 'middleware' => 'auth:api'], function () {
    // /api/exports/pos-services/{clientId}/{branchId?}
    Route::get('pos-services/{clientId}/{branchId?}', 'ExcelReportsController@posServices');

    // /api/exports/pos-products/{clientId}/{branchId?}
    Route::get('pos-products/{clientId}/{branchId?}', 'ExcelReportsController@posProducts');

    // /api/exports/pos-job-order/{clientId}/{branchId?}
    Route::get('pos-job-order/{clientId}/{branchId?}', 'ExcelReportsController@posJobOrder');

    // /api/exports/rfid-transactions/{clientId}/{branchId?}
    Route::get('rfid-transactions/{clientId}/{branchId?}', 'ExcelReportsController@rfidServices');

    // /api/exports/rfid-topups/{clientId}/{branchId?}
    Route::get('rfid-topups/{clientId}/{branchId?}', 'ExcelReportsController@rfidTopups');
});

// /api/print
Route::group(['prefix' => 'print'], function() {
    // /api/print/receipt/{transactionId}
    Route::get('receipt/{transactionId}', 'PrinterController@printReceipt');
});


// /clients
Route::group(['prefix' => 'clients', 'middleware' => 'auth:api', 'role:developer'], function() {
    // /clients/
    Route::get('/', 'ClientsController@index');
});

// /api/sys-defaults
Route::group(['prefix' => 'sys-defaults', 'middleware' => 'auth:api'], function() {
    // /api/sys-defaults/set-branch/{userId}|self
    Route::post('set-branch/{userId}', 'BranchesController@setDefaultBranch');
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

    // /api/live/upload
    Route::group(['prefix' => 'upload'], function() {
        // /api/live/upload/users/{ownerEmail}
        Route::get('users/{ownerEmail}', 'LiveHostController@uploadUsers');

        // /api/live/upload/products/{ownerEmail}
        Route::get('products/{ownerEmail}', 'LiveHostController@uploadProducts');

        // /api/live/upload/services/{ownerEmail}
        Route::get('services/{ownerEmail}', 'LiveHostController@uploadServices');

        // /api/live/upload/branch-setup/{ownerEmail}
        Route::get('branch-setup/{ownerEmail}', 'LiveHostController@uploadBranchSetup');

        // /api/live/upload/transactions/{ownerEmail}
        Route::get('transactions/{ownerEmail}', 'LiveHostController@uploadTransactions');

        // /api/live/upload/rfid/{ownerEmail}
        Route::get('rfid/{ownerEmail}', 'LiveHostController@uploadRfid');

        // /api/live/upload/{ownerEmail}
        Route::get('{ownerEmail}', 'LiveHostController@upload');
    });
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
