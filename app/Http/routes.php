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
        Route::get('admin/product/edit/{productId}', 'EditProductController@get')->name('admin.product.edit');

        Route::post('admin/product/edit', 'EditProductController@post')->name('admin.product.edit.post');
        Route::post('admin/product/delete', 'DeleteProductController@index')->name('admin.product.delete');
    });
    Route::group(['namespace' => 'Tag'], function() {
        Route::get('admin/tag', 'ListTagsController@index')->name('admin.tag');
        Route::get('admin/tag/new', 'NewTagController@index')->name('admin.tag.new');
        Route::get('admin/tag/edit/{tagId}', 'EditTagController@index')->name('admin.tag.edit');
    });
    Route::group(['namespace' => 'Promotion'], function() {
        Route::group(['namespace' => 'Coupon'], function () {
            Route::get('admin/promotion/coupon', 'ListCouponsController@index')->name('admin.coupon');
            Route::get('admin/promotion/coupon/new', 'NewCouponController@index')->name('admin.coupon.new');
            Route::get('admin/promotion/coupon/edit/{couponId}', 'EditCouponController@index')->name('admin.coupon.edit');
        });
        Route::group(['namespace' => 'CartPriceRule'], function () {
            Route::get('admin/promotion/cart-price-rule', 'ListCartPriceRulesController@index')->name('admin.cart-price-rule');
        });
        Route::group(['namespace' => 'CatalogPromotion'], function () {
            Route::get('admin/promotion/catalog-promotion', 'ListCatalogPromotionsController@index')->name('admin.catalog-promotion');
        });
    });
    Route::group(['namespace' => 'Order'], function() {
        Route::get('admin/order', 'ListOrdersController@index')->name('admin.order');
        Route::get('admin/order/view/{orderId}', 'ViewOrderController@index')->name('admin.order.view');
        Route::get('admin/order/shipments/{orderId}', 'ViewOrderShipmentsController@index')->name('admin.order.shipments');
        Route::get('admin/order/invoice/{orderId}', 'ViewOrderInvoiceController@index')->name('admin.order.invoice');
        Route::get('admin/order/add-shipment/{orderId}', 'AddShipmentController@get')->name('admin.order.add-shipment');

        Route::post('admin/order/add-shipment', 'AddShipmentController@post')->name('admin.order.add-shipment.post');
        Route::post('admin/order/add-shipment-with-tracking-code', 'AddShipmentController@postAddShipmentWithTrackingCode')->name('admin.order.add-shipment-with-tracking-code');
        Route::post('admin/order/add-shipment-label', 'AddShipmentController@postAddShipmentLabel')->name('admin.order.add-shipment-label');
        Route::post('admin/order/buy-shipment-label', 'AddShipmentController@postBuyShipmentLabel')->name('admin.order.buy-shipment-label');
        Route::post('admin/order/set-status', 'SetOrderStatusController@index')->name('admin.order.set-status');
    });
    Route::group(['namespace' => 'User'], function() {
        Route::get('admin/user', 'ListUsersController@index')->name('admin.user');
    });
    Route::group(['namespace' => 'Option'], function() {
        Route::get('admin/option', 'ListOptionsController@index')->name('admin.option');
        Route::get('admin/option/edit/{optionId}', 'EditOptionController@index')->name('admin.option.edit');
    });
    Route::group(['namespace' => 'Setting'], function() {
        Route::get('admin/settings', 'ListSettingsController@index')->name('admin.settings');
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
