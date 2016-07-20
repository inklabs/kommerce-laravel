<?php

Route::get('/', function () {
    return view('default');
});

Route::group(['prefix' => 'api'], function() {
    Route::resource('products', 'ProductController');
});

Route::get('/createDummyProduct', [
    'as' => 'p.createDummy',
    'uses' => 'ProductController@createDummy'
]);
