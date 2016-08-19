<?php

use App\Http\Controllers\Cart\ChangePasswordController;

Route::get('/', function () {
    return view('default');
})->name('home');

Route::get('/conditions', function() {
    echo 'TODO: conditions';
})->name('page.conditions');

Route::get('/privacy', function() {
    echo 'TODO: privacy';
})->name('page.privacy');

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

Route::get('/cart', 'CartController@getShow')->name('cart.get.show');
Route::get('/cart/estimate-tax', 'CartController@getEstimateTax')->name('cart.estimate-tax');
Route::get('/cart/estimate-shipping', 'CartController@getEstimateShipping')->name('cart.estimate-shipping');

Route::post('/cart/add-item', 'CartController@postAddItem')->name('cart.add-item');
Route::post('/cart/apply-coupon', 'CartController@postApplyCoupon')->name('cart.apply-coupon');
Route::post('/cart/apply-shipping-method', 'CartController@postApplyShippingMethod')->name('cart.apply-shipping-method');
Route::post('/cart/delete-item', 'CartController@postDeleteItem')->name('cart.delete-item');
Route::post('/cart/estimate-shipping', 'CartController@postEstimateShipping');
Route::post('/cart/remove-coupon', 'CartController@postRemoveCoupon')->name('cart.remove-coupon');
Route::post('/cart/update-quantity', 'CartController@postUpdateQuantity')->name('cart.update-quantity');

Route::get('/checkout/pay', 'CheckoutController@getPay')->name('checkout.pay');


Route::group(['namespace' => 'User'], function() {
    Route::get('user/change-password', 'ChangePasswordController@index')->name('user.change-password');
    Route::post('user/change-password', 'ChangePasswordController@post')->name('user.change-password.post');

    Route::get('user/account', 'AccountController@index')->name('user.account');
    Route::get('user/account/view-order/{orderId}', 'AccountController@viewOrder')->name('user.account.view-order');
});

Route::controller('cart', 'CartController');
Route::controller('checkout', 'CheckoutController');
Route::controller('dummy-data', 'DummyDataController');

//Route::get('/dummyData/createDummyProduct', [
//    'as' => 'dd.createDummyProduct',
//    'uses' => 'DummyDataController@createDummyProduct'
//]);
