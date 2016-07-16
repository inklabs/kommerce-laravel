<?php

Route::get('/', function () {
    return redirect()->route('p.createDummy');
});

Route::resource('/p', 'ProductController');

Route::get('/createDummyProduct', [
    'as' => 'p.createDummy',
    'uses' => 'ProductController@createDummy'
]);
