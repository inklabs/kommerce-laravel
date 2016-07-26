<?php

Route::get('/', function () {
    return view('default');
});

Route::get('/api/v1/{model}/{action}', [
    'as' => 'a.process',
    'uses' => 'ApiController@process'
]);

Route::group(['prefix' => 'api'], function() {
    Route::resource('products', 'ProductController');
});

Route::get('/createDummyProduct', [
    'as' => 'p.createDummy',
    'uses' => 'ProductController@createDummy'
]);
