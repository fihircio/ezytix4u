<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/symlink', function () {
    Artisan::call('storage:link');
   echo "Link Done";
});

Route::get('/clear', function () {
   Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
   echo "Clear Done";
});


Eventmie::routes();
