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

Route::post('activate', function() {
    sleep(10);
    return 1;
});

// /api/developer
Route::group(['prefix' => 'developer'], function () {
    // /api/developer/system-date-time
    // Route::get('system-date-time', function() {
    //     return date('Y-m-d h:i:s A');
    // });
    Route::get('system-date-time', 'DeveloperController@getSystemDateTime');

    Route::post('set-system-date-time', 'DeveloperController@setSystemDateTime');

    // /api/developer/client
    Route::get('client', 'ClientsController@index');

    // /api/developer/create-user
    Route::post('create-user', 'ClientsController@createUser');

    // /api/developer/{userId}/update-user
    Route::post('{userId}/update-user', 'ClientsController@updateUser');

    // /api/developer/setup-client
    Route::post('setup-client', 'ClientsController@setUpClient');

    // /api/developer/setup-machines
    Route::post('setup-machines', 'ClientsController@setUpMachines');

    // /api/developer/reset
    Route::post('reset', 'ClientsController@reset');

    // /api/developer/unsynch/{from?}/{to?}
    Route::get('unsynch/{from?}/{to?}', 'ClientsController@unsynch');


    Route::group(['middleware' => ['auth:api', 'role:developer']], function() {
        // /login-staff
        Route::post('login/{userId}', 'DeveloperController@login');
    });
});

// /api/test
Route::group(['prefix' => 'test'], function() {
    // /api/test/print
    Route::get('print', 'PrinterController@test');

    // /api/test/qr
    Route::get('qr', 'PrinterController@qrCode');
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// /api/thermal/print
Route::group(['prefix' => 'thermal/print'], function() {
    // /api/thermal/print
    // Route::post('claim-stub', 'ThermalPrinterController@claimStub');
});


// /api/tap
Route::group(['prefix' => 'tap'], function() {
    // /api/tap/check/{rfid}
    Route::get('check/{rfid}', 'TapCardController@check');

    // /api/tap/{machineIp}/{rfid}/{macAddress?}
    Route::get('{machineIp}/{rfid}/{macAddress?}', 'TapCardController@tap');
});

// /api/reset
Route::group(['prefix' => 'reset'], function() {
    // /api/reset/machines
    Route::get('machines', 'MachinesController@reset');
});

// /api/admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'role:admin,developer']], function () {
    // /api/admin/preferences/shop-details/{shopId}
    Route::get('preferences/shop-details/{shopId}', 'ClientsController@edit');

    // /api/admin/preferences/print-qr-codde
    Route::post('preferences/print-qr-code', 'ClientsController@printQRCode');

    // /api/admin/preferences/generate-qr-code
    Route::post('preferences/generate-qr-code', 'ClientsController@generateQRCode');

    // /api/admin/store-hours
    Route::group(['prefix' => 'store-hours'], function () {
        Route::get('/', 'StoreHoursController@index');

        // /api/admin/store-hourse/{id}/update
        Route::post('{id}/update', 'StoreHoursController@update');
    });
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
    // /api/users/
    Route::get('/', 'UsersController@index');

    // /api/users/create
    Route::post('create', 'UsersController@create');

    // /api/users/{userId}/update
    Route::post('{userId}/update', 'UsersController@update');

    // /api/users/{userId}/change-password
    Route::post('{userId}/change-password', 'UsersController@changePassword');

    // /api/users/{userId}/delete-user
    Route::post('{userId}/delete-user', 'UsersController@deleteUser');

    // /api/users/{userId}/assign-role
    Route::post('{userId}/assign-role', 'UsersController@assignRole');
});

// /api/daily-sales
Route::prefix('daily-sales')->group(function () {
    // /api/daily-sales
    Route::get('/', 'DailyReportController@index');
});

// /api/sales-report
Route::group(['prefix' => 'sales-report', 'middleware' => 'auth:api'], function() {
    // /api/sales-report/summary/{print}
    Route::post('summary/{print?}', 'SalesReportController@summary');

    // /api/sales-report/{monthIndex}/{year}/all/{excel?}
    Route::get('{monthIndex}/{year}/all/{excel?}', 'SalesReportController@index');

    // /api/sales-report/{monthIndex}/{year}/week-view
    Route::get('{monthIndex}/{year}/week-view', 'SalesReportController@weekly');

    // /api/sales-report/{year}/monthly
    Route::get('{year}/monthly', 'SalesReportController@monthy');

    // /api/sales-report/{yearFrom}/{yearUntil}/yearly
    Route::get('{yearFrom}/{yearUntil}/yearly', 'SalesReportController@yearly');

    // /api/sales-report/custom-range/{print?}
    Route::get('custom-range/{print?}', 'SalesReportController@customRange');

    // /api/sales-report/{monthIndex}/{year}/pos-transactions
    Route::get('{monthIndex}/{year}/pos-transactions', 'SalesReportController@posTransactions');

    // /api/sales-report/cumulative/{year}
    Route::get('cumulative/{year}', 'SalesReportController@yearlyCumulative');

    // /api/sales-report/excel
    Route::group(['prefix' => 'excel'], function() {
        // /api/sales-report/excel/custom-range/{download?}
        Route::get('custom-range/{download?}', 'ExcelController@customRange');
    });
});
Route::get('cumulative/{year}', 'SalesReportController@yearlyCumulative');


// /api/excel
Route::group(['prefix' => 'excel', 'middleware' => 'auth:api'], function() {
    // /api/excel/sales-custom-range/1
    Route::post('sales-custom-range/1', 'ExcelController@customRange');

    // /api/excel/export-services
    Route::post('export-services', 'ExcelController@exportServices');

    // /api/excel/new-customers
    Route::post('new-customers', 'CustomersController@excel');

    // /api/excel/job-orders
    Route::post('job-orders', 'ExcelController@jobOrders');
});


// /api/sales-report/{monthIndex}/{year}/weekly
// Route::get('/sales-report/{monthIndex}/{year}/weekly', 'SalesReportController@weekly');

// /api/customers
Route::group(['prefix' => 'customers', 'middleware' => 'auth:api'], function() {
    // /api/customers/new-customers
    Route::get('new-customers', 'CustomersController@newCustomers');

    // /api/customers/pre-registered
    Route::get('pre-registered', 'CustomersController@preRegistered');

    // /api/customers/{customerId}/check-points
    Route::get('{customerId}/check-points', 'CustomersController@checkPoints');

    // /api/customers/{customerId}/delete-customer
    Route::post('{customerId}/delete-customer', 'CustomersController@deleteCustomer');

    // /api/customers
    Route::get('/', 'CustomersController@index');

    // /api/customers/create
    Route::post('create', 'CustomersController@store');

    // /api/customers/{customerId}/loyalty-services
    Route::get('{customerId}/loyalty-services', 'PaymentsController@loyaltyServices');

    // /api/customers/{customerId}/with-tokens
    Route::get('{customerId}/with-tokens', 'CustomersController@customerWithTokens');

    // /api/customers/{customerId}/with-services
    Route::get('{customerId}/with-services', 'CustomersController@customerWithServices');

    // /api/customers/{customerId}/update
    Route::post('{customerId}/update', 'CustomersController@update');

    // /api/customers/get-crn
    Route::get('get-crn', 'CustomersController@getCRN');

    // /api/customers/{customerId}
    Route::get('{customerId}', 'CustomersController@show');
});

// /api/products
Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function() {
    // /api/products
    Route::get('/', 'ProductsController@index');

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
    // /api/services/all
    Route::get('all', 'ServicesController@index');

    // /api/services/washing-services
    Route::group(['prefix' => 'washing-services'], function() {
        // /api/services/washing-services
        Route::get('/', 'WashingServicesController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/washing-services/create
            Route::post('create', 'WashingServicesController@store');

            // /api/services/washing-services/{id}/update
            Route::post('{id}/update', 'WashingServicesController@update');

            // /api/services/washing-services/{id}/set-picture
            Route::post('{id}/set-picture', 'WashingServicesController@setPicture');

            // /api/services/washing-services/{id}/remove-picture
            Route::post('{id}/remove-picture', 'WashingServicesController@removePicture');

            // /api/services/washing-services/{id}/delete-service
            Route::post('{id}/delete-service', 'WashingServicesController@deleteService');
        });
    });

    // /api/services/drying-services
    Route::group(['prefix' => 'drying-services'], function() {
        // /api/services/drying-services
        Route::get('/', 'DryingServicesController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/drying-services/create
            Route::post('create', 'DryingServicesController@store');

            // /api/services/drying-services/{id}/update
            Route::post('{id}/update', 'DryingServicesController@update');

            // /api/services/drying-services/{id}/set-picture
            Route::post('{id}/set-picture', 'DryingServicesController@setPicture');

            // /api/services/drying-services/{id}/remove-picture
            Route::post('{id}/remove-picture', 'DryingServicesController@removePicture');

            // /api/services/drying-services/{id}/delete-service
            Route::post('{id}/delete-service', 'DryingServicesController@deleteService');
        });
    });

    // /api/services/other-services
    Route::group(['prefix' => 'other-services'], function() {
        // /api/services/other-services
        Route::get('/', 'OtherServicesController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/other-services/create
            Route::post('create', 'OtherServicesController@store');

            // /api/services/other-services/{id}/update
            Route::post('{id}/update', 'OtherServicesController@update');

            // /api/services/other-services/{id}/set-picture
            Route::post('{id}/set-picture', 'OtherServicesController@setPicture');

            // /api/services/other-services/{id}/remove-picture
            Route::post('{id}/remove-picture', 'OtherServicesController@removePicture');

            // /api/services/other-services/{id}/delete-service
            Route::post('{id}/delete-service', 'OtherServicesController@deleteService');
        });
    });

    // /api/services/full-services
    Route::group(['prefix' => 'full-services'], function() {
        // /api/services/full-services
        Route::get('/', 'FullServicesController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/full-services/create
            Route::post('create', 'FullServicesController@store');

            // /api/services/full-services/{id}/update
            Route::post('{id}/update', 'FullServicesController@update');

            // /api/services/full-services/{id}/set-picture
            Route::post('{id}/set-picture', 'FullServicesController@setPicture');

            // /api/services/full-services/{id}/remove-picture
            Route::post('{id}/remove-picture', 'FullServicesController@removePicture');

            // /api/services/full-services/{id}/delete-service
            Route::post('{id}/delete-service', 'FullServicesController@deleteService');
        });
    });

    // /api/services/full-service-items
    Route::group(['prefix' => 'full-service-items'], function() {

        // /api/services/full-service-items
        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/full-service-items/create
            Route::post('create', 'FullServiceItemsController@store');

            // /api/services/full-service-items/{id}/update
            Route::post('{id}/update', 'FullServiceItemsController@update');

            // /api/services/full-service-items/{id}/set-picture
            Route::post('{id}/set-picture', 'FullServiceItemsController@setPicture');

            // /api/services/full-service-items/{id}/remove-picture
            Route::post('{id}/remove-picture', 'FullServiceItemsController@removePicture');

            // /api/services/full-service-items/{id}/delete-service
            Route::post('{id}/delete-service', 'FullServiceItemsController@deleteService');
        });
    });

    // /api/services/full-service-products
    Route::group(['prefix' => 'full-service-products'], function() {

        // /api/services/full-service-products
        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/full-service-products/create
            Route::post('create', 'FullServiceProductsController@store');

            // /api/services/full-service-products/{id}/update
            Route::post('{id}/update', 'FullServiceProductsController@update');

            // /api/services/full-service-products/{id}/set-picture
            Route::post('{id}/set-picture', 'FullServiceProductsController@setPicture');

            // /api/services/full-service-products/{id}/remove-picture
            Route::post('{id}/remove-picture', 'FullServiceProductsController@removePicture');

            // /api/services/full-service-products/{id}/delete-product
            Route::post('{id}/delete-product', 'FullServiceProductsController@deleteProduct');
        });
    });

    // /api/services/scarpa-cleanings
    Route::group(['prefix' => 'scarpa-cleanings'], function() {
        // /api/services/scarpa-cleanings
        Route::get('/', 'ScarpaCleaningController@index');

        // /api/services/scarpa-cleanings
        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/scarpa-cleanings/create
            Route::post('create', 'ScarpaCleaningController@store');

            // /api/services/scarpa-cleanings/{id}/update
            Route::post('{id}/update', 'ScarpaCleaningController@update');

            // /api/services/scarpa-cleanings/{id}/set-picture
            Route::post('{id}/set-picture', 'ScarpaCleaningController@setPicture');

            // /api/services/scarpa-cleanings/{id}/remove-picture
            Route::post('{id}/remove-picture', 'ScarpaCleaningController@removePicture');

            // /api/services/scarpa-cleanings/{id}/delete-service
            Route::post('{id}/delete-service', 'ScarpaCleaningController@deleteService');
        });

        // /api/services/scarpa-cleanings/variations
        Route::group(['prefix' => 'variations'], function() {
            // /api/services/scarpa-cleanings/variations/{serviceId}/add
            Route::post('{serviceId}/add', 'ScarpaCleaningController@addVariation');

            // /api/services/scarpa-cleanings/variations/{serviceId}/{variationId}/update
            Route::post('{serviceId}/{variationId}/update', 'ScarpaCleaningController@updateVariation');

            // /api/services/scarpa-cleanings/variations/{serviceId}/delete
            Route::post('{serviceId}/delete', 'ScarpaCleaningController@deleteVariation');

            // /api/services/scarpa-cleanings/variations/{serviceId}
            Route::get('{serviceId}', 'ScarpaCleaningController@variations');
        });

    });

    // /api/services/lagoon
    Route::group(['prefix' => 'lagoon'], function() {
        // /api/services/lagoon
        Route::get('/', 'LagoonController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/lagoon/create
            Route::post('create', 'LagoonController@store');

            // /api/services/lagoon/{id}/update
            Route::post('{id}/update', 'LagoonController@update');

            // /api/services/lagoon/{id}/set-picture
            Route::post('{id}/set-picture', 'LagoonController@setPicture');

            // /api/services/lagoon/{id}/remove-picture
            Route::post('{id}/remove-picture', 'LagoonController@removePicture');

            // /api/services/lagoon/{id}/delete-service
            Route::post('{id}/delete-service', 'LagoonController@deleteService');
        });
    });

    // /api/services/lagoon-per-kilo
    Route::group(['prefix' => 'lagoon-per-kilo'], function() {

        // /api/services/lagoon-per-kilo
        Route::get('/', 'LagoonPerKiloController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/lagoon-per-kilo/create
            Route::post('create', 'LagoonPerKiloController@store');

            // /api/services/lagoon-per-kilo/{id}/update
            Route::post('{id}/update', 'LagoonPerKiloController@update');

            // /api/services/lagoon-per-kilo/{id}/set-picture
            Route::post('{id}/set-picture', 'LagoonPerKiloController@setPicture');

            // /api/services/lagoon-per-kilo/{id}/remove-picture
            Route::post('{id}/remove-picture', 'LagoonPerKiloController@removePicture');

            // /api/services/lagoon-per-kilo/{id}/delete-service
            Route::post('{id}/delete-service', 'LagoonPerKiloController@deleteService');
        });
    });

    // /api/services/per-kilo
    Route::group(['prefix' => 'per-kilo'], function() {
        // /api/services/per-kilo
        Route::get('/', 'PerKiloServicesController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/per-kilo/create
            Route::post('create', 'PerKiloServicesController@store');

            // /api/services/per-kilo/{id}/update
            Route::post('{id}/update', 'PerKiloServicesController@update');

            // /api/services/per-kilo/{id}/set-picture
            Route::post('{id}/set-picture', 'PerKiloServicesController@setPicture');

            // /api/services/per-kilo/{id}/remove-picture
            Route::post('{id}/remove-picture', 'PerKiloServicesController@removePicture');

            // /api/services/per-kilo/{id}/delete-service
            Route::post('{id}/delete-service', 'PerKiloServicesController@deleteService');
        });
    });

    // /api/services/elux
    Route::group(['prefix' => 'elux'], function() {
        // /api/services/elux
        Route::get('/{serviceType}', 'EluxController@index');

        Route::group(['middleware' => 'role:admin'], function() {
            // /api/services/elux/create
            Route::post('create', 'EluxController@store');

            // /api/services/elux/{id}/update
            Route::post('{id}/update', 'EluxController@update');

            // /api/services/elux/{id}/set-picture
            Route::post('{id}/set-picture', 'EluxController@setPicture');

            // /api/services/elux/{id}/remove-picture
            Route::post('{id}/remove-picture', 'EluxController@removePicture');

            // /api/services/elux/{id}/delete-service
            Route::post('{id}/delete-service', 'EluxController@deleteService');
        });
    });

});

// /apo/pos-transactions
Route::group(['prefix' => 'pos-transactions', 'middleware' => 'auth:api'], function () {
    // /api/pos-transactions/clear-monitor
    Route::post('clear-monitor', 'PosTransactionController@clearMonitorView');

    // /api/pos-transactions/current-transaction/{customerId}
    Route::get('current-transaction/{customerId}', 'PosTransactionController@currentTransaction');

    // /api/pos-transactions/services
    Route::get('services', 'PosTransactionController@services');

    // /api/pos-transactions/elux-services
    Route::get('elux-services', 'PosTransactionController@eluxServices');

    // /api/pos-transactions/lagoon
    Route::get('lagoon', 'LagoonController@index');

    // /api/pos-transactions/add-service/{category}
    Route::post('add-service/{category}', 'PosTransactionController@addService');

    // /api/pos-transactions/save-transaction/{transactionId}
    Route::post('save-transaction/{transactionId}', 'PosTransactionController@saveTransaction');

    // /api/pos-transactions/save-qr-code-transaction
    Route::post('save-qr-code-transaction', 'QRTransactionController@save');

    // /api/pos-transactions/products
    Route::get('products', 'PosTransactionController@products');

    // /api/pos-transactions/add-product
    Route::post('add-product', 'PosTransactionController@addProduct');

    // /api/pos-transactions/add-lagoon
    Route::post('add-lagoon', 'PosTransactionController@addLagoon');

    // /api/pos-transactions/add-lagoon-per-kilo
    Route::post('add-lagoon-per-kilo', 'PosTransactionController@addLagoonPerKilo');

    // /api/pos-transactions/reduce-products
    Route::post('reduce-products', 'PosTransactionController@reduceProducts');

    // /api/pos-transactions/reduce-lagoon
    Route::post('reduce-lagoon', 'PosTransactionController@reduceLagoon');

    // /api/pos-transactions/reduce-lagoon-per-kilo
    Route::post('reduce-lagoon-per-kilo', 'PosTransactionController@reduceLagoonPerKilo');

    // /api/pos-transactions/add-per-kilo
    Route::post('add-per-kilo', 'PosTransactionController@addPerKilo');

    // /api/pos-transactions/reduce-per-kilo
    Route::post('add-per-kilo', 'PosTransactionController@removePerKilo');

    // /api/pos-transactions/add-elux-service
    Route::post('add-elux-service', 'PosTransactionController@addEluxService');

    // /api/pos-transactions/{transactionId}/cancel-transaction
    Route::post('{id}/cancel-transaction', 'PosTransactionController@cancelTransaction');

    // /api/pos-transactions/{transactionId}/void-transaction
    Route::post('{id}/void-transaction', 'PosTransactionController@voidTransaction');

    // /api/pos-transactions/scarpa-cleanings
    Route::group(['prefix' => 'scarpa-cleanings'], function() {
        // /api/pos-transactions/scarpa-cleanings
        Route::get('/', 'PosTransactionController@scarpaCleanings');

        // /api/pos-transactions/scarpa-cleanings/{serviceId}/variations/{groupBy}
        Route::get('{serviceId}/variations/{groupBy}', 'PosTransactionController@scarpaVariations');

        // /api/pos-transactions/scarpa-cleanings/{variationId}/add
        Route::post('{variationId}/add', 'PosTransactionController@addScarpaCleaning');

        // /api/pos-transactions/scarpa-cleanings/reduce-scarpa-cleaning
        Route::post('reduce-scarpa-cleaning', 'PosTransactionController@reduceScarpaCleaning');
    });
});

// /api/transactions
Route::group(['prefix' => 'transactions', 'middleware' => 'auth:api'], function() {
    // /api/transactions/unsaved-transactions
    Route::get('unsaved-transactions', 'TransactionsController@unsavedTransactions');

    // /api/transactions/by-job-orders
    Route::get('by-job-orders', 'TransactionsController@byJobOrders');

    // /api/transactions/by-service-items
    Route::get('by-service-items', 'TransactionsController@byServiceItems');

    // /api/transactions/by-product-items
    Route::get('by-product-items', 'TransactionsController@byProductItems');

    // /api/transactions/unpaid-transactions
    Route::get('unpaid-transactions', 'TransactionsController@unpaidTransactions');

    // /api/transactions/{transactionId}
    Route::get('{transactionId}', 'TransactionsController@show');

    // /api/transactions/look-up-items
    Route::post('look-up-items', 'TransactionsController@lookUpItems');

    // /api/transactions/{transactionId}/delete-transaction
    Route::post('{transactionId}/delete-transaction', 'TransactionsController@deleteTransaction');

    // /api/transactions/{transactionId}/view-service-items
    Route::get('{transactionId}/view-service-items', 'TransactionsController@viewServiceItems');

    // /api/transactions/{transactionId}/view-elux-service-items
    Route::get('{transactionId}/view-elux-service-items', 'TransactionsController@viewEluxServiceItems');

    // /api/transactions/{transactionId}/print
    Route::post('{transactionId}/print', 'PrinterController@print');

    // /api/transactions/{transactionId}/print-claim-stub
    Route::post('{transactionId}/print-claim-stub', 'PrinterController@claimStub');

    // /api/transactions/{transactionId}/print-job-order
    Route::post('{transactionId}/print-job-order', 'PrinterController@jobOrder');

    // /api/transactions/{transactionId}/print-dopu
    Route::post('{transactionId}/print-dopu', 'PrinterController@dopu');

    // /api/transactions/{transactionId}/print-tap-card
    Route::post('{transactionId}/print-tap-card', 'PrinterController@tapCard');

    // /api/transaction/service-items
    Route::group(['prefix' => 'service-items'], function() {
        // /api/transaction/service-items/{serviceTransactionItemId}/delete
        Route::post('{serviceTransactionItemId}/delete', 'ServiceTransactionsController@deleteItem');
    });

    // /api/transaction/elux-service-items
    Route::group(['prefix' => 'elux-service-items'], function() {
        // /api/transaction/elux-service-items/{eluxServiceTransactionItemId}/delete
        Route::post('{eluxServiceTransactionItemId}/delete', 'ServiceTransactionsController@deleteEluxItem');
    });
});

// /api/transaction-remarks
Route::group(['prefix' => 'transaction-remarks', 'middleware' => 'auth:api'], function() {
    // /api/transaction-remarks/{transactionId}/remarks
    Route::get('{transactionId}/remarks', 'TransactionRemarksController@index');

    // /api/transaction-remarks/{transactionId}/add-remarks
    Route::post('{transactionId}/add-remarks', 'TransactionRemarksController@store');

    // /api/transaction-remarks/{transactionId}/delete-remarks
    Route::post('{transactionId}/delete-remarks', 'TransactionRemarksController@deleteRemarks');
});


// /api/reports
Route::group(['prefix' => 'reports', 'middleware' => 'auth:api'], function () {
    // /api/reports/excel
    Route::group(['prefix' => 'excel'], function () {
        // /api/reports/excel/pos-transactions
        Route::get('pos-transactions', 'ReportsController@excelPosTransactions');

        // /api/reports/excel/pos-collections
        Route::get('pos-collections', 'ReportsController@excelPosCollections');

        // /api/reports/excel/rfid-transactions
        Route::get('rfid-transactions', 'ReportsController@excelRfidTransactions');

        // /api/reports/excel/rfid-load-transactions
        Route::get('rfid-load-transactions', 'ReportsController@excelRfidLoadTransactions');

    });


    // /reports/print
    Route::group(['prefix' => 'print'], function() {
        // /api/reports/print/pos-collections
        Route::get('pos-collections', 'ReportsController@printPosCollections');

        // /api/reports/print/pos-transactions
        Route::get('pos-transactions', 'ReportsController@printPosTransactions');

        // /api/reports/print/rfid-transactions
        Route::get('rfid-transactions', 'ReportsController@printRfidTransactions');

        // /api/reports/print/rfid-load-transactions
        Route::get('rfid-load-transactions', 'ReportsController@printRfidLoadTransactions');

        // /api/reports/print/daily-sale/{date}/{print?=true}
        Route::get('daily-sale/{date}/{print?}', 'SalesReportController@summary');
    });

});

// /api/payments
Route::group(['prefix' => 'payments', 'middleware' => 'auth:api'], function() {
    // /api/payments/{transactionId}
    Route::get('{transactionId}', 'PaymentsController@transactionPayment');

    // /api/payments/{transactionId}/proceed
    Route::post('{transactionId}/full', 'PaymentsController@fullPayment');

    // /api/payments/{transactionId}/partial
    Route::post('{transactionId}/partial', 'PaymentsController@partialPayment');

    // /api/payments/partial-payments/{partialPaymentId}
    Route::get('partial-payments/{partialPaymentId}', 'PartialPaymentsController@show');
});


// /api/machines
Route::group(['prefix' => 'machines', 'middleware' => ['auth:api']], function() {
    // /api/machines
    Route::get('/', 'MachinesController@index');

    // /api/machines/elux
    Route::group(['prefix' => 'elux'], function() {
        // /api/machines/elux/models/{machineType}
        Route::get('models/{machineType}', 'EluxMachinesController@eluxModels');

        // /api/machines/elux/{machineId}/delete
        Route::post('{machineId}/delete', 'EluxMachinesController@destroy');

        // /api/machines/elux/{machineType}
        Route::get('{machineType}', 'EluxMachinesController@index');

        // /api/machines/elux/{machineType}/create
        Route::post('{machineType}/create', 'EluxMachinesController@store');

        // /api/machines/elux/next-stack-order/{machineType}
        Route::get('next-stack-order/{machineType}', 'EluxMachinesController@nextStackOrder');

        // /api/machines/{machineId}/update-settings
        Route::post('{machineId}/update-settings', 'EluxMachinesController@updateSettings');
    });


    // /api/machines/next-stack-order/{machineType}
    Route::get('next-stack-order/{machineType}', 'MachinesController@nextStackOrder');

    // /api/machines/remarks
    Route::get('remarks', 'MachinesController@remarks');

    // /api/machines/{machineId}/history
    Route::get('{machineId}/history', 'MachinesController@history');

    // /api/machines/{machineType}/create
    Route::post('{machineType}/create', 'MachinesController@create');

    // /api/machines/{machineId}/update-settings
    Route::post('{machineId}/update-settings', 'MachinesController@updateSettings');

    // /api/machines/{machineId}/delete
    Route::post('{machineId}/delete', 'MachinesController@destroy');

    Route::group(['middleware' => 'role:developer'], function() {
        // /api/machines/{branchId}/store
    });

    // /api/machines/view-all
    Route::get('{branchId}/view-all', 'MachinesController@index');

    // /api/machines/get-update
    Route::get('get-update', 'MachinesController@lastActivated');

    // /api/machines/{machineType}
    Route::get('{machineType}', 'MachinesController@viewByType');
});

// /api/machine-usages
Route::group(['prefix' => 'machine-usages', 'middleware' => 'auth:api'], function() {
    // /api/machine-usages/{machineUsageId}/delete-usage
    Route::post('{machineUsageId}/delete-usage', 'MachineUsagesController@deleteUsage');
});


// /api/remote
Route::group(['prefix' => 'remote', 'middleware' => ['auth:api']], function() {
    // /api/remote/machines/{machineId}/activate
    Route::post('machines/activate', 'MachinesController@activate');

    // /api/machines/force-stop
    Route::post('machines/force-stop', 'MachinesController@forceStop');

    // /api/remote/machines/test-connection/{machineId}
    Route::get('machines/test-connection/{machineId}', 'MachinesController@testConnection');

    // /api/remote/confirm-activation/{remoteToken}
    Route::get('confirm-activation/{remoteToken}', 'MachinesController@confirmActivation');
});

// /api/remote/confirm-activation/{remoteToken}
Route::get('/remote/confirm-activation/{remoteToken}/{terminalIP}', 'MachinesController@confirmActivation');

// /api/re-works
Route::group(['prefix' => 're-works', 'middleware' => ['auth:api']], function() {
    // /api/re-works
    Route::get('/', 'ReworksController@index');

    // /api/re-works/transfer/{from}/{to}
    Route::post('transfer/{from}/{to}', 'ReworksController@transfer');

    // /api/re-works/{machineId}
    Route::post('{machineId}', 'ReworksController@reWork');

    // /api/re-works/customer-wash/{customerWashId}
    Route::get('customer-wash/{customerWashId}', 'ReworksController@customerWash');

    // /api/re-works/customer-dry/{customerDryId}
    Route::get('customer-dry/{customerDryId}', 'ReworksController@customerDry');
});

// /api/pending-services
Route::group(['prefix' => 'pending-services', 'middleware' => 'auth:api'], function() {
    // /api/pending-services
    Route::get('', 'PendingServicesController@index');

    // /api/pending-services/{customerId}/view-all
    Route::get('{customerId}/view-all', 'PendingServicesController@viewAll');

    // /api/pending-services/{serviceType}/{serviceId}/dispose-service
    Route::post('{serviceType}/{serviceId}/dispose-service', 'PendingServicesController@disposeService');

    // /api/pending-services/customers
    Route::get('customers', 'PendingServicesController@customers');

    // /api/pending-services/washing-services
    Route::get('washing-services', 'PendingServicesController@washingServices');

    // /api/pending-services/drying-services
    Route::get('drying-services', 'PendingServicesController@dryingServices');

    // /api/pending-services/elux-services
    Route::get('elux-services', 'PendingServicesController@eluxServices');
});

// /api/product-purchases
Route::group(['prefix' => 'product-purchases', 'middleware' => 'auth:api'], function() {
    // /api/product-purchases
    Route::get('', 'ProductPurchasesController@index');

    // /api/product-purchases/create
    Route::post('create', 'ProductPurchasesController@store');

    // /api/product-purchases/{productPurchaseId}/delete-product-purchase
    Route::post('{productPurchaseId}/delete-product-purchase', 'ProductPurchasesController@deleteProductPurchase');
});


// /api/expenses
Route::group(['prefix' => 'expenses', 'middleware' => 'auth:api'], function() {
    // /api/expenses
    Route::get('/', 'ExpensesController@index');

    // /api/expenses/create
    Route::post('create', 'ExpensesController@store');

    // /api/expenses/{expenseId}/update
    Route::post('{expenseId}/update', 'ExpensesController@update');

    // /api/expenses/{expenseId}/delete
    Route::post('{expenseId}/delete', 'ExpensesController@deleteExpense');
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

// /api/rfid-cards
Route::group(['prefix' => 'rfid-cards', 'middleware' => 'auth:api'], function() {
    // /api/rfid-cards/details/{rfid}
    Route::get('details/{rfid}', 'RfidCardsController@cardDetails');

    // /api/rfid-cards/customer-cards
    Route::get('customer-cards', 'RfidCardsController@customerCards');

    // /api/rfid-cards/unregistered-cards
    Route::get('unregistered-cards', 'RfidCardsController@unregisteredCards');

    // /api/rfid-cards/tap-transactions
    Route::get('tap-transactions', 'RfidTapController@index');

    // /api/rfid-cards/tap-transactions/{transactionId}/delete
    Route::post('tap-transactions/{transactionId}/delete', 'RfidTapController@deleteTransaction');

    // /api/rfid-cards/load-transactions
    Route::get('load-transactions', 'RfidLoadController@index');

    // /api/rfid-cards/load-transactions/{transactionId}/print-load-transaction
    Route::get('load-transactions/{transactionId}/print-load-transaction', 'PrinterController@loadTransaction');

    // /api/rfid-cards/tap-transactions/{transactionId}/print
    Route::post('tap-transactions/{transactionId}/print', 'PrinterController@tapTransaction');

    // /api/rfid-cards/unregistered-cards/clear-all
    Route::post('unregistered-cards/clear-all', 'RfidCardsController@clearUnregisteredCards');

    // /api/rfid-cards/create
    Route::post('create', 'RfidCardsController@store');

    // /api/rfid-cards/{rfidCardId}/update
    Route::post('{rfidCardId}/update', 'RfidCardsController@update');

    // /api/rfid-cards/{rfidCardId}/delete-card
    Route::post('{rfidCardId}/delete-card', 'RfidCardsController@deleteCard');

    // /api/rfid-cards/{rfidCardId}/top-up
    Route::post('{rfidCardId}/top-up', 'RfidLoadController@topUp');

    // /api/rfid-cards/load-transactions/{rfidLoadId}/delete
    Route::post('load-transactions/{rfidLoadId}/delete', 'RfidLoadController@deleteTransaction');

    // /api/rfid-cards
    Route::get('', 'RfidCardsController@index');
});


// /api/service-prices
Route::group(['prefix' => 'service-prices', 'middleware' => 'auth:api'], function() {
    // /api/service-prices/{servicePriceId}/update
    Route::post('{servicePriceId}/update', 'RfidServicePricesController@update');
});

// /api/discounts
Route::group(['prefix' => 'discounts', 'middleware' => ['auth:api']], function() {
    // /api/discounts
    Route::get('/', 'DiscountsController@index');

    // /api/discounts/create
    Route::post('create', 'DiscountsController@store');

    // /api/discounts/{id}/create
    Route::post('{id}/update', 'DiscountsController@update');

    // /api/discounts/{id}/delete
    Route::post('{id}/delete', 'DiscountsController@delete');
});

// /api/lagoon-partners
Route::group(['prefix' => 'lagoon-partners', 'middleware' => ['auth:api']], function() {
    // /api/lagoon-partners
    Route::get('/', 'LagoonPartnersController@index');

    // /api/lagoon-partners/create
    Route::post('create', 'LagoonPartnersController@store');

    // /api/lagoon-partners/{id}/create
    Route::post('{id}/update', 'LagoonPartnersController@update');

    // /api/lagoon-partners/{id}/delete
    Route::post('{id}/delete', 'LagoonPartnersController@delete');
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

// /api/time-keeping

Route::group(['prefix' => 'time-keeping'], function() {
    // /api/time-keeping
    Route::get('/', 'TiToController@index');

    // /api/time-keeping/time-in
    Route::get('time-in', 'TiToController@getTimeIn');

    // /api/time-keeping/time-out
    Route::post('time-out', 'TiToController@timeOut');
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

    // /api/autocomplete/organizations
    Route::get('organizations', 'CustomersController@organizations');
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

// /api/boards
Route::group(['prefix' => 'boards'], function() {
    // /api/boards/today
    Route::get('today', 'BoardsController@getBoardToday');

    // /api/boards/announcement
    Route::get('announcement', 'AnnouncementsController@getAnnouncement');

    // /api/boards/{customerId}
    Route::get('{customerId}', 'CustomersController@show');

    // /api/boards/job-order/{transactionId}
    Route::get('job-order/{transactionId}', 'TransactionsController@show');

    // /api/boards/clear-monitor
    Route::post('clear-monitor', 'PosTransactionController@clearMonitorView');
});

// /api/announcements
Route::group(['prefix' => 'announcements', 'middleware' => 'auth:api'], function() {
    // /api/announcements
    Route::get('/', 'AnnouncementsController@index');

    // /api/announcements/create
    Route::post('create', 'AnnouncementsController@store');

    // /api/announcements/{id}/update
    Route::post('{id}/update', 'AnnouncementsController@update');

    // /api/announcements/{id}/delete
    Route::post('{id}/delete', 'AnnouncementsController@delete');

    // /api/events/set-default/{slideId}
    Route::post('set-default/{slideId}', 'AnnouncementsController@setDefault');
});


// /api/events
Route::group(['prefix' => 'events'], function() {
    Route::group(['middleware' => 'auth:api'], function() {
        // /api/events
        Route::get('/', 'EventsController@index');

        // /api/events/create
        Route::post('create', 'EventsController@store');

        // /api/events/{id}/update
        Route::post('{id}/update', 'EventsController@update');

        // /api/events/{id}/delete
        Route::post('{id}/delete', 'EventsController@delete');

        // /api/events/{id}
        Route::get('{id}', 'EventsController@show');

        // /api/events/remove-slide/{slideId}
        Route::post('remove-slide/{slideId}', 'EventsController@removeSlide');

        // /api/events/delete-video/{videoId}
        Route::post('delete-video/{videoId}', 'EventsController@deleteVideo');

        // /api/events/set-default/{slideId}
        Route::post('set-default/{slideId}', 'EventsController@setDefault');
    });
});

// /api/files
Route::group(['prefix' => 'files', 'middleware' => 'auth:api'], function() {
    // /api/files/upload-slides/{eventId}
    Route::post('upload-slides/{eventId}', 'FilesController@uploadSlides');

    // /api/files/upload-video/{eventId}
    Route::post('upload-video/{eventId}', 'FilesController@uploadVideo');

    // /api/files/change-picture/{slideId}
    Route::post('change-picture/{slideId}', 'FilesController@changePicture');
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
    // /api/live/update
    Route::get('update', 'LiveHostController@update');
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
