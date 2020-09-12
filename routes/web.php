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


Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('paginate',       'UserController@paginate');
    $router->post('detail/{id}',    'UserController@show');
    $router->post('save/{id?}',     'UserController@save');
    $router->post('delete/{id?}',     'UserController@delete');
});

$router->group(['prefix' => 'kabupaten'], function () use ($router) {
    $router->post('paginate',       'KabupatenController@paginate');
    $router->post('all',            'KabupatenController@all');
});

$router->group(['prefix' => 'kecamatan'], function () use ($router) {
    $router->post('paginate',       'KecamatanController@paginate');
    $router->post('detail/{id}',    'KecamatanController@show');
    $router->post('save/{id?}',     'KecamatanController@save');
    $router->post('all',            'KecamatanController@all');
    $router->post('delete/{id?}',   'KecamatanController@delete');
});

$router->group(['prefix' => 'kelurahan'], function () use ($router) {
    $router->post('paginate',       'KelurahanController@paginate');
    $router->post('detail/{id}',    'KelurahanController@show');
    $router->post('save/{id?}',     'KelurahanController@save');
    $router->post('all',            'KelurahanController@all');
    $router->post('maps',           'KelurahanController@maps');
    $router->post('delete/{id?}',   'KelurahanController@delete');
});

$router->group(['prefix' => 'kategori'], function () use ($router) {
    $router->post('paginate',       'KategoriController@paginate');
    $router->post('detail/{id}',    'KategoriController@show');
    $router->post('save/{id?}',     'KategoriController@save');
    $router->post('form',           'KategoriController@form');
    $router->post('form/{kriteria}','KategoriController@formByKriteria');
    $router->post('all',            'KategoriController@all');
    $router->post('delete/{id?}',   'KategoriController@delete');
});

$router->group(['prefix' => 'kriteria'], function () use ($router) {
    $router->post('paginate',       'KriteriaController@paginate');
    $router->post('detail/{id}',    'KriteriaController@show');
    $router->post('save/{id?}',     'KriteriaController@save');
    $router->post('all',            'KriteriaController@all');
    $router->post('details/all',    'KriteriaController@detailAll');
    $router->post('delete/{id?}',   'KriteriaController@delete');
});

$router->group(['prefix' => 'aturan'], function () use ($router) {
    $router->post('paginate',       'AturanController@paginate');
    $router->post('detail/{id}',    'AturanController@show');
    $router->post('save/{id?}',     'AturanController@save');
    $router->post('all',            'AturanController@all');
    $router->post('delete/{id?}',   'AturanController@delete');
});

$router->group(['prefix' => 'class'], function () use ($router) {
    $router->post('paginate',       'ClassController@paginate');
    $router->post('detail/{id}',    'ClassController@show');
    $router->post('save/{id?}',     'ClassController@save');
    $router->post('all',            'ClassController@all');
    $router->post('delete/{id?}',   'ClassController@delete');
});

$router->group(['prefix' => 'klasifikasi'], function () use ($router) {
    $router->post('proses-new',   'KlasifikasiController@prosesNew');
    $router->post('proses',       'KlasifikasiController@proses');
    $router->post('result/{id}',  'KlasifikasiController@result');
});

$router->group(['prefix' => 'maps'], function () use ($router) {
    $router->post('shp',       'MapsController@shp');
    $router->post('cluster',   'MapsController@cluster');
    $router->post('maps',   'MapsController@maps');
});


$router->group(['prefix' => 'config'], function () use ($router) {
    $router->post('detail/{key}',   'ConfigController@detail');
    $router->post('save',           'ConfigController@save');
});
