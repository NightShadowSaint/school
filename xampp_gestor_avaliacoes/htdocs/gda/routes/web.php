<?php

use App\Http\Controllers\InscricaoController;
use Illuminate\Support\Facades\Auth;
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
    return view('pages/welcome');
});

Auth::routes();

Route::get('/inscricao', 'InscricaoController@index');

Route::get('/inscricao/{avl}', 'InscricaoController@autoSelect');

Route::get('/classificacoes', 'ClassController@classificacoes');

Route::get('/calendario', 'CalendarioController@calendario');

Route::get('/home', 'HomeController@index')->name('home');

Route::post('login', [ 'as' => 'login', 'uses' => 'Auth\LoginController@login']);

Route::post('/inscricao/novo', 'InscricaoController@store')->name("insc.store");

Route::post('/calendario/novo', 'CalendarioController@store')->name("clnd.store");

Route::post('/classificacoes/novo', 'ClassController@store')->name("class.store");

Route::post('/classificacoes/fetch', 'ClassController@fetch')->name("class.fetch");