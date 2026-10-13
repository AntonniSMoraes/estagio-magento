define(['ko', 'mage/translate'], function (ko, $t) {
    'use strict';
    return function (original) {
        return original.extend({
            initialize: function () {
                this._super();
                this.mensagemAssombrada = ko.computed(function () {
                    var quantity = Number(this.getCartParam('summary_count')) || 0;
                    if (quantity === 0) {
                        return $t('Your cauldron is empty');
                    }
                    if (quantity >= 13) {
                        return $t('Thirteen items or more. Brave soul.');
                    }
                    return $t('Items in the cauldron: %1').replace('%1', quantity);
                }, this);
                return this;
            },
            destroy: function () {
                if (this.mensagemAssombrada) {
                    this.mensagemAssombrada.dispose();
                }
                return this._super();
            }
        });
    };
});
