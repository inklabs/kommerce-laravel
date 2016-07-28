<?php

Route::get('/', function () {
    return view('default');
});

Route::get('/api/v1/{model}/{action}/{method}', [
    'as' => 'a.processQuery',
    'uses' => 'ApiController@processQuery'
])->where([
    'action' => '.*Query'
]);

Route::get('/api/v1/{model}/{action}/{method?}', [
    'as' => 'a.processCommand',
    'uses' => 'ApiController@processCommand'
])->where([
    'action' => '.*Command'
]);

Route::group(['prefix' => 'api'], function() {
    Route::resource('products', 'ProductController');
});

Route::get('/dummyData/createDummyProduct', [
    'as' => 'ddc.createDummyProduct',
    'uses' => 'DummyDataController@createDummyProduct'
]);
