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

Route::get('/s/{theme}/{section}', 'ScssController@serve')
    ->name('scss.serve');

Route::get('/a/{theme}/{section}/{path}', 'AssetController@serve')
    ->name('asset.serve')
    ->where([
        'path' => '(.*)',
    ]);

Route::group(['namespace' => 'Cart', 'prefix' => 'cart'], function() {
    Route::get('', 'CartController@getShow')->name('cart.get.show');
    Route::get('estimate-tax', 'CartController@getEstimateTax')->name('cart.estimate-tax');
    Route::get('estimate-shipping', 'CartController@getEstimateShipping')->name('cart.estimate-shipping');
    Route::get('added/{cartItemId}', 'CartController@getAdded')->name('cart.added');

    Route::post('add-item', 'CartController@postAddItem')->name('cart.add-item');
    Route::post('apply-coupon', 'CartController@postApplyCoupon')->name('cart.apply-coupon');
    Route::post('apply-shipping-method', 'CartController@postApplyShippingMethod')->name('cart.apply-shipping-method');
    Route::post('delete-item', 'CartController@postDeleteItem')->name('cart.delete-item');
    Route::post('estimate-shipping', 'CartController@postEstimateShipping');
    Route::post('remove-coupon', 'CartController@postRemoveCoupon')->name('cart.remove-coupon');
    Route::post('update-quantity', 'CartController@postUpdateQuantity')->name('cart.update-quantity');
});

Route::get('/checkout/pay', 'CheckoutController@getPay')->name('checkout.pay');
Route::get('/checkout/complete/{orderId}', 'CheckoutController@getComplete')->name('checkout.complete');
Route::post('/checkout/pay', 'CheckoutController@postPay')->name('checkout.pay');
Route::get('search', 'SearchController@index')->name('search');


Route::group(['namespace' => 'User', 'prefix' => 'user'], function() {
    Route::group(['namespace' => 'Attachment', 'prefix' => 'attachment'], function() {
        Route::get('createForOrderItem/{orderItemId}', 'CreateAttachmentForOrderItemController@get')->name('user.attachment.createForOrderItem');
        Route::get('createForProduct/{productId}', 'CreateAttachmentForProductController@get')->name('user.attachment.createForProduct');

        Route::post('createForOrderItem', 'CreateAttachmentForOrderItemController@post')->name('user.attachment.createForOrderItem.post');
        Route::post('createForProduct', 'CreateAttachmentForProductController@post')->name('user.attachment.createForProduct.post');
    });

    Route::get('change-password', 'ChangePasswordController@index')->name('user.change-password');
    Route::post('change-password', 'ChangePasswordController@post')->name('user.change-password.post');

    Route::get('login', 'UserLoginController@get')->name('user.login');
    Route::get('forgot-password', 'UserForgotPasswordController@get')->name('user.forgot-password');
    Route::get('forgot-password/complete', 'UserForgotPasswordController@complete')->name('user.forgot-password.complete');
    Route::get('account', 'AccountController@index')->name('user.account');
    Route::get('account/view-order/{orderId}', 'AccountController@viewOrder')->name('user.account.view-order');

    Route::post('login', 'UserLoginController@post')->name('user.login.post');
    Route::post('logout', 'UserLogoutController@post')->name('user.logout');
    Route::post('forgot-password', 'UserForgotPasswordController@post')->name('user.forgot-password.post');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function() {
    Route::get('login', 'AdminLoginController@get')->name('admin.login');
    Route::get('forgot-password', 'AdminForgotPasswordController@get')->name('admin.forgot-password');
    Route::get('forgot-password/complete', 'AdminForgotPasswordController@complete')->name('admin.forgot-password.complete');

    Route::post('login', 'AdminLoginController@post')->name('admin.login.post');
    Route::post('forgot-password', 'AdminForgotPasswordController@post')->name('admin.forgot-password.post');
    Route::post('logout', 'AdminLogoutController@post')->name('admin.logout');

    Route::group(['middleware' => 'auth.admin'], function() {
        Route::group(['namespace' => 'Attachment', 'prefix' => 'attachment'], function () {
            Route::post('createForOrderItem', 'CreateAttachmentForOrderItemController@post')->name('admin.attachment.createForOrderItem.post');
            Route::post('deleteAttachment', 'DeleteAttachmentController@post')->name('admin.attachment.deleteAttachment');
        });
        Route::group(['namespace' => 'Product', 'prefix' => 'product'], function () {
            Route::get('', 'ListProductsController@index')->name('admin.product');
            Route::get('new', 'EditProductController@getNew')->name('admin.product.new');
            Route::get('edit/{productId}', 'EditProductController@getEdit')->name('admin.product.edit');
            Route::get('images/{productId}', 'ListProductImagesController@index')->name('admin.product.images');
            Route::get('tags/{productId}', 'ListProductTagsController@index')->name('admin.product.tags');
            Route::get('options/{productId}', 'ListProductOptionsController@index')->name('admin.product.options');
            Route::get('attributes/{productId}', 'ListProductAttributesForProductOptionsController@index')->name('admin.product.attributes');
            Route::get('quantity-discounts/{productId}', 'ListProductQuantityDiscountsController@index')->name('admin.product.quantity-discounts');

            Route::post('new', 'EditProductController@postNew')->name('admin.product.new.post');
            Route::post('edit', 'EditProductController@postEdit')->name('admin.product.edit.post');
            Route::post('upload-image', 'UploadProductImageController@post')->name('admin.product.upload-image');
            Route::post('remove-image', 'RemoveImageFromProductController@post')->name('admin.product.remove-image');
            Route::post('add-tag', 'AddTagToProductController@post')->name('admin.product.add-tag');
            Route::post('remove-tag', 'RemoveTagFromProductController@post')->name('admin.product.remove-tag');
            Route::post('add-attribute-value', 'AddAttributeValueToProductController@post')->name('admin.product.add-attribute-value');
            Route::post('remove-attribute-value', 'RemoveAttributeValueFromProductController@post')->name('admin.product.remove-attribute-value');
            Route::post('set-default-image', 'SetDefaultImageForProductController@post')->name('admin.product.set-default-image');
            Route::post('unset-default-image', 'UnsetDefaultImageForProductController@post')->name('admin.product.unset-default-image');
            Route::post('delete-quantity-discount', 'DeleteProductQuantityDiscountController@post')->name('admin.product.delete-quantity-discount');
            Route::post('delete', 'DeleteProductController@post')->name('admin.product.delete');
        });
        Route::group(['namespace' => 'Tag', 'prefix' => 'tag'], function () {
            Route::get('', 'ListTagsController@index')->name('admin.tag');
            Route::get('new', 'EditTagController@getNew')->name('admin.tag.new');
            Route::get('edit/{tagId}', 'EditTagController@getEdit')->name('admin.tag.edit');
            Route::get('images/{tagId}', 'ListTagImagesController@index')->name('admin.tag.images');
            Route::get('options/{tagId}', 'ListTagOptionsController@index')->name('admin.tag.options');
            Route::get('products/{tagId}', 'ListTagProductsController@index')->name('admin.tag.products');

            Route::post('new', 'EditTagController@postNew')->name('admin.tag.new.post');
            Route::post('edit', 'EditTagController@postEdit')->name('admin.tag.edit.post');
            Route::post('upload-image', 'UploadTagImageController@post')->name('admin.tag.upload-image');
            Route::post('remove-image', 'RemoveImageFromTagController@post')->name('admin.tag.remove-image');
            Route::post('remove-option', 'RemoveOptionFromTagController@post')->name('admin.tag.remove-option');
            Route::post('add-product', 'AddProductToTagController@post')->name('admin.tag.add-product');
            Route::post('remove-product', 'RemoveProductFromTagController@post')->name('admin.tag.remove-product');
            Route::post('set-default-image', 'SetDefaultImageForTagController@post')->name('admin.tag.set-default-image');
            Route::post('unset-default-image', 'UnsetDefaultImageForTagController@post')->name('admin.tag.unset-default-image');
            Route::post('add-option', 'AddOptionToTagController@post')->name('admin.tag.add-option');
            Route::post('delete', 'DeleteTagController@post')->name('admin.tag.delete');
        });
        Route::group(['namespace' => 'Promotion', 'prefix' => 'promotion'], function () {
            Route::group(['namespace' => 'Coupon', 'prefix' => 'coupon'], function () {
                Route::get('', 'ListCouponsController@index')->name('admin.coupon');
                Route::get('new', 'CreateCouponController@get')->name('admin.coupon.new');
                Route::get('edit/{couponId}', 'EditCouponController@get')->name('admin.coupon.edit');

                Route::post('new', 'CreateCouponController@post')->name('admin.coupon.new.post');
                Route::post('edit', 'EditCouponController@post')->name('admin.coupon.edit.post');
                Route::post('delete', 'DeleteCouponController@post')->name('admin.coupon.delete');

            });
            Route::group(['namespace' => 'CartPriceRule', 'prefix' => 'cart-price-rule'], function () {
                Route::get('', 'ListCartPriceRulesController@index')->name('admin.cart-price-rule');
                Route::get('new', 'CreateCartPriceRuleController@get')->name('admin.cart-price-rule.new');
                Route::get('edit/{cartPriceRuleId}', 'EditCartPriceRuleController@get')->name('admin.cart-price-rule.edit');
                Route::get('items/{cartPriceRuleId}', 'ListCartPriceRuleItemsController@index')->name('admin.cart-price-rule.items');
                Route::get('discounts/{cartPriceRuleId}', 'ListCartPriceRuleDiscountsController@index')->name('admin.cart-price-rule.discounts');

                Route::post('new', 'CreateCartPriceRuleController@post')->name('admin.cart-price-rule.new.post');
                Route::post('edit', 'EditCartPriceRuleController@post')->name('admin.cart-price-rule.edit.post');
                Route::post('delete', 'DeleteCartPriceRuleController@post')->name('admin.cart-price-rule.delete');
                Route::post('item/new-product', 'CreateCartPriceRuleProductItemController@post')->name('admin.cart-price-rule.item.new-product');
                Route::post('item/new-tag', 'CreateCartPriceRuleTagItemController@post')->name('admin.cart-price-rule.item.new-tag');
                Route::post('item/edit-tag', 'UpdateCartPriceRuleTagItemController@post')->name('admin.cart-price-rule.item.edit-tag');
                Route::post('item/delete', 'DeleteCartPriceRuleItemController@post')->name('admin.cart-price-rule.item.delete');
                Route::post('discount/new', 'CreateCartPriceRuleDiscountController@post')->name('admin.cart-price-rule.discount.new');
                Route::post('discount/delete', 'DeleteCartPriceRuleDiscountController@post')->name('admin.cart-price-rule.discount.delete');
            });
            Route::group(['namespace' => 'CatalogPromotion', 'prefix' => 'catalog-promotion'], function () {
                Route::get('', 'ListCatalogPromotionsController@index')->name('admin.catalog-promotion');
                Route::get('new', 'CreateCatalogPromotionController@get')->name('admin.catalog-promotion.new');
                Route::get('edit/{catalogPromotionId}', 'EditCatalogPromotionController@get')->name('admin.catalog-promotion.edit');

                Route::post('new', 'CreateCatalogPromotionController@post')->name('admin.catalog-promotion.new.post');
                Route::post('edit', 'EditCatalogPromotionController@post')->name('admin.catalog-promotion.edit.post');
                Route::post('delete', 'DeleteCatalogPromotionController@post')->name('admin.catalog-promotion.delete');
            });
        });
        Route::group(['namespace' => 'Order', 'prefix' => 'order'], function () {
            Route::get('', 'ListOrdersController@index')->name('admin.order');
            Route::get('view/{orderId}', 'ViewOrderController@index')->name('admin.order.view');
            Route::get('shipments/{orderId}', 'ViewOrderShipmentsController@index')->name('admin.order.shipments');
            Route::get('invoice/{orderId}', 'ViewOrderInvoiceController@index')->name('admin.order.invoice');
            Route::get('add-shipment/{orderId}', 'AddShipmentController@get')->name('admin.order.add-shipment');

            Route::post('add-shipment', 'AddShipmentController@post')->name('admin.order.add-shipment.post');
            Route::post('add-shipment-with-tracking-code', 'AddShipmentController@postAddShipmentWithTrackingCode')->name('admin.order.add-shipment-with-tracking-code');
            Route::post('add-shipment-label', 'AddShipmentController@postAddShipmentLabel')->name('admin.order.add-shipment-label');
            Route::post('buy-shipment-label', 'AddShipmentController@postBuyShipmentLabel')->name('admin.order.buy-shipment-label');
            Route::post('set-status', 'SetOrderStatusController@index')->name('admin.order.set-status');
        });
        Route::group(['namespace' => 'User', 'prefix' => 'user'], function () {
            Route::get('', 'ListUsersController@index')->name('admin.user');
        });
        Route::group(['namespace' => 'Option', 'prefix' => 'option'], function () {
            Route::get('', 'ListOptionsController@index')->name('admin.option');
            Route::get('new', 'CreateOptionController@get')->name('admin.option.new');
            Route::get('edit/{optionId}', 'EditOptionController@get')->name('admin.option.edit');
            Route::get('values/{optionId}', 'ListOptionValuesController@index')->name('admin.option.values');
            Route::get('option-products/{optionId}', 'ListOptionProductsController@index')->name('admin.option.option-products');
            Route::get('tags/{optionId}', 'ListTagsForOptionController@index')->name('admin.option.tags');

            Route::post('new', 'CreateOptionController@post')->name('admin.option.new.post');
            Route::post('edit', 'EditOptionController@post')->name('admin.option.edit.post');
            Route::post('delete', 'DeleteOptionController@post')->name('admin.option.delete');
            Route::post('add-option-value', 'AddOptionValueToOptionController@post')->name('admin.option.add-option-value');
            Route::post('delete-option-value', 'DeleteOptionValueController@post')->name('admin.option.delete-option-value');
            Route::post('add-option-product', 'AddOptionProductToOptionController@post')->name('admin.option.add-option-product');
            Route::post('delete-option-product', 'DeleteOptionProductController@post')->name('admin.option.delete-option-product');
            Route::post('add-tag', 'AddTagToOptionController@post')->name('admin.option.add-tag');
            Route::post('remove-tag', 'RemoveTagFromOptionController@post')->name('admin.option.remove-tag');
        });
        Route::group(['namespace' => 'Attribute', 'prefix' => 'attribute'], function () {
            Route::get('', 'ListAttributesController@index')->name('admin.attribute');
            Route::get('new', 'CreateAttributeController@get')->name('admin.attribute.new');
            Route::get('edit/{attributeId}', 'EditAttributeController@get')->name('admin.attribute.edit');
            Route::get('attribute-values/{attributeId}', 'ListAttributeValuesForAttributeController@get')->name('admin.attribute.attribute-values');

            Route::post('new', 'CreateAttributeController@post')->name('admin.attribute.new.post');
            Route::post('edit', 'EditAttributeController@post')->name('admin.attribute.edit.post');
            Route::post('delete', 'DeleteAttributeController@post')->name('admin.attribute.delete');
            Route::post('new-attribute-value', 'CreateAttributeValueController@post')->name('admin.attribute.new-attribute-value');
            Route::post('delete-attribute-value', 'DeleteAttributeValueController@post')->name('admin.attribute.delete-attribute-value');
            Route::post('delete-product-attribute', 'DeleteProductAttributeController@post')->name('admin.attribute.delete-product-attribute');

            Route::get('attribute-value/edit/{attributeValueId}', 'EditAttributeValueController@get')->name('admin.attribute.attribute-value.edit');
            Route::get('attribute-value/product-attributes/{attributeValueId}', 'ListProductAttributesForAttributeValueController@get')->name('admin.attribute.attribute-value.product-attributes');

            Route::post('attribute-value/edit', 'EditAttributeValueController@post')->name('admin.attribute.attribute-value.edit.post');
            Route::post('attribute-value/delete', 'DeleteAttributeValueController@post')->name('admin.attribute.attribute-value.delete');
            Route::post('attribute-value/add-product', 'CreateProductAttributeController@post')->name('admin.attribute.attribute-value.add-product');
        });
        Route::group(['namespace' => 'Settings', 'prefix' => 'settings'], function () {
            Route::get('sales-tax', 'ListSalesTaxRulesController@index')->name('admin.settings.sales-tax');
            Route::get('sales-tax/zipcode', 'ListZipcodeSalesTaxRulesController@index')->name('admin.settings.sales-tax.zipcode');
            Route::get('sales-tax/zipcode-range', 'ListZipcodeRangeSalesTaxRulesController@index')->name('admin.settings.sales-tax.zipcode-range');
            Route::get('sales-tax/state', 'ListStateSalesTaxRulesController@index')->name('admin.settings.sales-tax.state');

            Route::post('sales-tax/zipcode/new', 'CreateZipcodeSalesTaxRulesController@post')->name('admin.settings.sales-tax.zipcode.new');
            Route::post('sales-tax/zipcode/edit', 'UpdateZipcodeSalesTaxRulesController@post')->name('admin.settings.sales-tax.zipcode.edit');
            Route::post('sales-tax/zipcode-range/new', 'CreateZipcodeRangeSalesTaxRulesController@post')->name('admin.settings.sales-tax.zipcode-range.new');
            Route::post('sales-tax/zipcode-range/edit', 'UpdateZipcodeRangeSalesTaxRulesController@post')->name('admin.settings.sales-tax.zipcode-range.edit');
            Route::post('sales-tax/state/new', 'CreateStateSalesTaxRulesController@post')->name('admin.settings.sales-tax.state.new');
            Route::post('sales-tax/state/edit', 'UpdateStateSalesTaxRulesController@post')->name('admin.settings.sales-tax.state.edit');
            Route::post('sales-tax/delete', 'DeleteSalesTaxRateController@post')->name('admin.settings.sales-tax.delete');

            Route::get('store', 'ListStoreConfigurationController@index')->name('admin.settings.store');
            Route::get('store/shipping', 'EditShippingConfigurationController@get')->name('admin.settings.store.shipping');
            Route::get('store/payments', 'EditPaymentsConfigurationController@get')->name('admin.settings.store.payments');

            Route::post('store/shipping', 'EditShippingConfigurationController@post')->name('admin.settings.store.shipping.post');
            Route::post('store', 'EditConfigurationController@post')->name('admin.settings.store.post');

        });
        Route::group(['namespace' => 'Tools', 'prefix' => 'tools'], function () {
            Route::get('ad-hoc-shipment', 'ListAdHocShipmentsController@index')->name('admin.tools.ad-hoc-shipment');
            Route::get('ad-hoc-shipment/new', 'CreateAdHocShipmentController@get')->name('admin.tools.ad-hoc-shipment.new');
            Route::get('ad-hoc-shipment/view/{shipmentTrackerId}', 'ViewAdHocShipmentController@get')->name('admin.tools.ad-hoc-shipment.view');

            Route::post('ad-hoc-shipment/new', 'CreateAdHocShipmentController@post')->name('admin.tools.ad-hoc-shipment.new.post');
            Route::post('ad-hoc-shipment/buy-shipment-label', 'CreateAdHocShipmentController@postBuyShipmentLabel')->name('admin.tools.ad-hoc-shipment.buy-shipment-label');
        });
        Route::group(['namespace' => 'Warehouse', 'prefix' => 'warehouse'], function () {
            Route::get('', 'ListWarehousesController@index')->name('admin.warehouse');
            Route::get('new', 'CreateWarehouseController@get')->name('admin.warehouse.new');
            Route::get('edit/{couponId}', 'EditWarehouseController@get')->name('admin.warehouse.edit');
            Route::get('inventory-locations/{warehouseId}', 'ListWarehouseInventoryLocationsController@index')->name('admin.warehouse.inventory-locations');

            Route::post('new', 'CreateWarehouseController@post')->name('admin.warehouse.new.post');
            Route::post('edit', 'EditWarehouseController@post')->name('admin.warehouse.edit.post');
            Route::post('delete', 'DeleteWarehouseController@post')->name('admin.warehouse.delete');
            Route::post('add-inventory-location', 'CreateInventoryLocationController@post')->name('admin.warehouse.add-inventory-location');
            Route::post('remove-inventory-location', 'DeleteInventoryLocationController@post')->name('admin.warehouse.remove-inventory-location');
        });
    });
});

Route::get('page/privacy', 'PageController@privacy')->name('page.privacy');
Route::get('page/terms', 'PageController@terms')->name('page.terms');
Route::get('page/contact', 'PageController@contact')->name('page.contact');

Route::get('data/image/{imagePath}', 'ImageController@get')
    ->name('image.path')
    ->where('imagePath', '(.*)');
