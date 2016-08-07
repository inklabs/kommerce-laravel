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

Route::get('/p/{slug}-{productId}', 'ProductController@show')
    ->name('product.show')
    ->where([
        'slug' => '[a-z0-9-]+',
        'productId' => '[0-9a-f]{32}',
    ]);

Route::get('/t/{slug}-{tagId}', 'TagController@show')
    ->name('tag.show')
    ->where([
        'slug' => '[a-z0-9-]+',
        'tagId' => '[0-9a-f]{32}',
    ]);

Route::get('/s', 'StyleController@serve')
    ->name('style.serve');

Route::get('/a/{theme}/{path}', 'AssetController@serve')
    ->name('asset.serve')
    ->where([
        'path' => '(.*)',
    ]);

Route::controller('cart', 'CartController');
Route::controller('checkout', 'CheckoutController');
Route::controller('dummy-data', 'DummyDataController');

//Route::get('/dummyData/createDummyProduct', [
//    'as' => 'dd.createDummyProduct',
//    'uses' => 'DummyDataController@createDummyProduct'
//]);
