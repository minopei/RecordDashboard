<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\Chart2Controller;

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

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/testDB', function () {
//     var_dump( DB::table('Users')->first() );
// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/records', 'App\Http\Controllers\RecordController@index');

Route::get('/echart', 'App\Http\Controllers\RecordController@getjson');

Route::get('/echarts', function () {
    return view('echart');
});
Route::get('/charts2',[Chart2Controller::class, 'getjson']);
Route::get('/charts',[ChartController::class, 'getjson']);

Route::get('/',function () {
    return view('chart');
});

Route::get('/ch', function () {
    return view('chart2');
});
