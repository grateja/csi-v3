<?php

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
Route::get('csi-v3/public/api/tap/{ipAddress}/{rfid}/{macAddress?}', 'TapCardController@tap');
Route::get('csi-2019/public/api/tap/{ipAddress}/{rfid}/{macAddress?}', 'TapCardController@tap');
// Route::get('/monitor-view', 'MonitorViewController@index');

Route::get('{any}', function() {
    return view('welcome');
})->where('any', '.*');

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/baliw', function() {
    return 'testing';
})->middleware('auth:api');

/// Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/testing', function() {
    $url = 'http://192.168.0.107/1?addtime=appWard2018&rfID=1232131';
    //$url = "localhost:8080/hack/test.json";
    $useragent = "Laundry CSI";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    die($result);
});
