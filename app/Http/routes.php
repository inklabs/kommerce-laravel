<?php

Route::get('/', 'HomeController@index')->name('home');

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
Route::get('/t', 'TagController@getList')->name('tag.list');

Route::get('/s', 'StyleController@serve')
    ->name('style.serve');

Route::get('/a/{theme}/{path}', 'AssetController@serve')
    ->name('asset.serve')
    ->where([
        'path' => '(.*)',
    ]);

Route::group(['namespace' => 'Cart'], function() {
    Route::get('cart', 'CartController@getShow')->name('cart.get.show');
    Route::get('cart/estimate-tax', 'CartController@getEstimateTax')->name('cart.estimate-tax');
    Route::get('cart/estimate-shipping', 'CartController@getEstimateShipping')->name('cart.estimate-shipping');
    Route::get('cart/added/{cartItemId}', 'CartController@getAdded')->name('cart.added');

    Route::post('cart/add-item', 'CartController@postAddItem')->name('cart.add-item');
    Route::post('cart/apply-coupon', 'CartController@postApplyCoupon')->name('cart.apply-coupon');
    Route::post('cart/apply-shipping-method', 'CartController@postApplyShippingMethod')->name('cart.apply-shipping-method');
    Route::post('cart/delete-item', 'CartController@postDeleteItem')->name('cart.delete-item');
    Route::post('cart/estimate-shipping', 'CartController@postEstimateShipping');
    Route::post('cart/remove-coupon', 'CartController@postRemoveCoupon')->name('cart.remove-coupon');
    Route::post('cart/update-quantity', 'CartController@postUpdateQuantity')->name('cart.update-quantity');
});

Route::get('/checkout/pay', 'CheckoutController@getPay')->name('checkout.pay');
Route::get('/checkout/complete/{orderId}', 'CheckoutController@getComplete')->name('checkout.complete');
Route::post('/checkout/pay', 'CheckoutController@postPay')->name('checkout.pay');
Route::get('search', 'SearchController@index')->name('search');


Route::group(['namespace' => 'User'], function() {
    Route::get('user/change-password', 'ChangePasswordController@index')->name('user.change-password');
    Route::post('user/change-password', 'ChangePasswordController@post')->name('user.change-password.post');

    Route::get('user/account', 'AccountController@index')->name('user.account');
    Route::get('user/account/view-order/{orderId}', 'AccountController@viewOrder')->name('user.account.view-order');
});

Route::group(['namespace' => 'Admin'], function() {
    Route::group(['namespace' => 'Product'], function() {
        Route::get('admin/product', 'ListProductsController@index')->name('admin.product');
        Route::get('admin/product/new', 'NewProductController@index')->name('admin.product.new');
        Route::get('admin/product/edit/{productId}', 'EditProductController@index')->name('admin.product.edit');
    });
});

Route::get('login', 'LoginController@index')->name('login');
Route::get('logout', 'LogoutController@index')->name('logout');

Route::get('page/privacy', 'PageController@privacy')->name('page.privacy');
Route::get('page/terms', 'PageController@terms')->name('page.terms');
Route::get('page/contact', 'PageController@contact')->name('page.contact');

Route::get('data/image/{imagePath}', function() {})->name('product.image');
Route::get('data/image/{imagePath}', function() {})->name('tag.image');

Route::controller('dummy-data', 'DummyDataController');
