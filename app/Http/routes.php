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
    Route::group(['namespace' => 'Attachment'], function() {
        Route::get('user/attachment/createForOrderItem/{orderItemId}', 'CreateAttachmentForOrderItemController@get')->name('user.attachment.createForOrderItem');
        Route::get('user/attachment/createForProduct/{productId}', 'CreateAttachmentForProductController@get')->name('user.attachment.createForProduct');

        Route::post('user/attachment/createForOrderItem', 'CreateAttachmentForOrderItemController@post')->name('user.attachment.createForOrderItem.post');
        Route::post('user/attachment/createForProduct', 'CreateAttachmentForProductController@post')->name('user.attachment.createForProduct.post');
    });

    Route::get('user/change-password', 'ChangePasswordController@index')->name('user.change-password');
    Route::post('user/change-password', 'ChangePasswordController@post')->name('user.change-password.post');

    Route::get('user/account', 'AccountController@index')->name('user.account');
    Route::get('user/account/view-order/{orderId}', 'AccountController@viewOrder')->name('user.account.view-order');
});

Route::group(['namespace' => 'Admin'], function() {
    Route::group(['namespace' => 'Attachment'], function() {
        Route::post('admin/attachment/createForOrderItem', 'CreateAttachmentForOrderItemController@post')->name('admin.attachment.createForOrderItem.post');
        Route::post('admin/attachment/deleteAttachment', 'DeleteAttachmentController@post')->name('admin.attachment.deleteAttachment');
    });
    Route::group(['namespace' => 'Product'], function() {
        Route::get('admin/product', 'ListProductsController@index')->name('admin.product');
        Route::get('admin/product/new', 'EditProductController@getNew')->name('admin.product.new');
        Route::get('admin/product/edit/{productId}', 'EditProductController@getEdit')->name('admin.product.edit');
        Route::get('admin/product/images/{productId}', 'ListProductImagesController@index')->name('admin.product.images');
        Route::get('admin/product/tags/{productId}', 'ListProductTagsController@index')->name('admin.product.tags');
        Route::get('admin/product/options/{productId}', 'ListProductOptionsController@index')->name('admin.product.options');
        Route::get('admin/product/quantity-discounts/{productId}', 'ListProductQuantityDiscountsController@index')->name('admin.product.quantity-discounts');

        Route::post('admin/product/new', 'EditProductController@postNew')->name('admin.product.new.post');
        Route::post('admin/product/edit', 'EditProductController@postEdit')->name('admin.product.edit.post');
        Route::post('admin/product/upload-image', 'UploadProductImageController@post')->name('admin.product.upload-image');
        Route::post('admin/product/remove-image', 'RemoveImageFromProductController@post')->name('admin.product.remove-image');
        Route::post('admin/product/add-tag', 'AddTagToProductController@post')->name('admin.product.add-tag');
        Route::post('admin/product/remove-tag', 'RemoveTagFromProductController@post')->name('admin.product.remove-tag');
        Route::post('admin/product/set-default-image', 'SetDefaultImageForProductController@post')->name('admin.product.set-default-image');
        Route::post('admin/product/unset-default-image', 'UnsetDefaultImageForProductController@post')->name('admin.product.unset-default-image');
        Route::post('admin/product/delete-quantity-discount', 'DeleteProductQuantityDiscountController@post')->name('admin.product.delete-quantity-discount');
        Route::post('admin/product/delete', 'DeleteProductController@post')->name('admin.product.delete');
    });
    Route::group(['namespace' => 'Tag'], function() {
        Route::get('admin/tag', 'ListTagsController@index')->name('admin.tag');
        Route::get('admin/tag/new', 'EditTagController@getNew')->name('admin.tag.new');
        Route::get('admin/tag/edit/{tagId}', 'EditTagController@getEdit')->name('admin.tag.edit');
        Route::get('admin/tag/images/{tagId}', 'ListTagImagesController@index')->name('admin.tag.images');
        Route::get('admin/tag/options/{tagId}', 'ListTagOptionsController@index')->name('admin.tag.options');
        Route::get('admin/tag/products/{tagId}', 'ListTagProductsController@index')->name('admin.tag.products');

        Route::post('admin/tag/new', 'EditTagController@postNew')->name('admin.tag.new.post');
        Route::post('admin/tag/edit', 'EditTagController@postEdit')->name('admin.tag.edit.post');
        Route::post('admin/tag/upload-image', 'UploadTagImageController@post')->name('admin.tag.upload-image');
        Route::post('admin/tag/remove-image', 'RemoveImageFromTagController@post')->name('admin.tag.remove-image');
        Route::post('admin/tag/remove-option', 'RemoveOptionFromTagController@post')->name('admin.tag.remove-option');
        Route::post('admin/tag/add-product', 'AddProductToTagController@post')->name('admin.tag.add-product');
        Route::post('admin/tag/remove-product', 'RemoveProductFromTagController@post')->name('admin.tag.remove-product');
        Route::post('admin/tag/set-default-image', 'SetDefaultImageForTagController@post')->name('admin.tag.set-default-image');
        Route::post('admin/tag/unset-default-image', 'UnsetDefaultImageForTagController@post')->name('admin.tag.unset-default-image');
        Route::post('admin/tag/add-option', 'AddOptionToTagController@post')->name('admin.tag.add-option');
        Route::post('admin/tag/delete', 'DeleteTagController@post')->name('admin.tag.delete');
    });
    Route::group(['namespace' => 'Promotion'], function() {
        Route::group(['namespace' => 'Coupon'], function () {
            Route::get('admin/promotion/coupon', 'ListCouponsController@index')->name('admin.coupon');
            Route::get('admin/promotion/coupon/new', 'EditCouponController@getNew')->name('admin.coupon.new');
            Route::get('admin/promotion/coupon/edit/{couponId}', 'EditCouponController@getEdit')->name('admin.coupon.edit');

            Route::post('admin/promotion/coupon/new', 'EditCouponController@postNew')->name('admin.coupon.new.post');
            Route::post('admin/promotion/coupon/edit', 'EditCouponController@postEdit')->name('admin.coupon.edit.post');
            Route::post('admin/promotion/coupon/delete', 'DeleteCouponController@post')->name('admin.coupon.delete');

        });
        Route::group(['namespace' => 'CartPriceRule'], function () {
            Route::get('admin/promotion/cart-price-rule', 'ListCartPriceRulesController@index')->name('admin.cart-price-rule');
        });
        Route::group(['namespace' => 'CatalogPromotion'], function () {
            Route::get('admin/promotion/catalog-promotion', 'ListCatalogPromotionsController@index')->name('admin.catalog-promotion');
            Route::get('admin/promotion/catalog-promotion/new', 'NewCatalogPromotionController@index')->name('admin.catalog-promotion.new');
            Route::get('admin/promotion/catalog-promotion/edit/{catalogPromotionId}', 'EditCatalogPromotionController@get')->name('admin.catalog-promotion.edit');

            Route::post('admin/promotion/catalog-promotion/edit', 'EditCatalogPromotionController@post')->name('admin.catalog-promotion.edit.post');
            Route::post('admin/promotion/catalog-promotion/delete', 'DeleteCatalogPromotionController@post')->name('admin.catalog-promotion.delete');
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
        Route::get('admin/option/new', 'EditOptionController@getNew')->name('admin.option.new');
        Route::get('admin/option/edit/{optionId}', 'EditOptionController@getEdit')->name('admin.option.edit');
        Route::get('admin/option/values/{optionId}', 'ListOptionValuesController@index')->name('admin.option.values');
        Route::get('admin/option/option-products/{optionId}', 'ListOptionProductsController@index')->name('admin.option.option-products');
        Route::get('admin/option/tags/{optionId}', 'ListTagsForOptionController@index')->name('admin.option.tags');

        Route::post('admin/option/new', 'EditOptionController@postNew')->name('admin.option.new.post');
        Route::post('admin/option/edit', 'EditOptionController@postEdit')->name('admin.option.edit.post');
        Route::post('admin/option/delete', 'DeleteOptionController@post')->name('admin.option.delete');
        Route::post('admin/option/add-option-value', 'AddOptionValueToOptionController@post')->name('admin.option.add-option-value');
        Route::post('admin/option/delete-option-value', 'DeleteOptionValueController@post')->name('admin.option.delete-option-value');
        Route::post('admin/option/add-option-product', 'AddOptionProductToOptionController@post')->name('admin.option.add-option-product');
        Route::post('admin/option/delete-option-product', 'DeleteOptionProductController@post')->name('admin.option.delete-option-product');
        Route::post('admin/option/add-tag', 'AddTagToOptionController@post')->name('admin.option.add-tag');
        Route::post('admin/option/remove-tag', 'RemoveTagFromOptionController@post')->name('admin.option.remove-tag');
    });
    Route::group(['namespace' => 'Settings'], function() {
        Route::get('admin/settings/sales-tax', 'ListSalesTaxRulesController@index')->name('admin.settings.sales-tax');

        Route::post('admin/settings/sales-tax/edit', 'EditTaxRateController@post')->name('admin.settings.sales-tax.edit');
    });
});

Route::get('login', 'LoginController@index')->name('login');
Route::get('logout', 'LogoutController@index')->name('logout');

Route::get('page/privacy', 'PageController@privacy')->name('page.privacy');
Route::get('page/terms', 'PageController@terms')->name('page.terms');
Route::get('page/contact', 'PageController@contact')->name('page.contact');

Route::get('data/image/{imagePath}', 'ImageController@get')
    ->name('image.path')
    ->where('imagePath', '(.*)');

Route::controller('dummy-data', 'DummyDataController');
