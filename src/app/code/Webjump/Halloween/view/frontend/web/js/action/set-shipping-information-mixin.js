define([
    'jquery',
    'mage/utils/wrapper',
    'uiRegistry',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, registry, quote) {
    'use strict';

    return function (originalAction) {
        return wrapper.wrap(originalAction, function (original) {
            var provider = registry.get('checkoutProvider'),
                field = registry.get('checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.mensagem_bruxaria'),
                address = quote.shippingAddress(),
                message = provider ? provider.get('shippingAddress.mensagem_bruxaria') || '' : '';

            if (field && !field.validate().valid) {
                return $.Deferred().reject().promise();
            }

            address.extension_attributes = address.extension_attributes || {};
            address.extension_attributes.mensagem_bruxaria = message;

            return original();
        });
    };
});
