<?php

Route::get('/', ['as' => 'home', 'uses' => 'ProductController@index']);


//Route::resource('/p', 'ProductController');

Route::get('/createDummyProduct', [
    'as' => 'p.createDummy',
    'uses' => 'ProductController@createDummy'
]);
