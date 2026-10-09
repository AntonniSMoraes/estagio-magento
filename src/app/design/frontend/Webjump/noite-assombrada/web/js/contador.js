define(['uiComponent', 'ko', 'mage/translate'], function (Component, ko, $t) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Magento_Theme/contador',
            dataFinal: '2026-11-01T00:00:00-03:00'
        },
        initialize: function () {
            this._super();
            this.fim = Date.parse(this.dataFinal);
            this.segundosRestantes = ko.observable(0);
            this.atualizar();
            this.mensagem = ko.computed(function () {
                var total = this.segundosRestantes(), dias, horas, minutos, segundos;
                if (total <= 0) {
                    return $t('The haunted night has ended');
                }
                dias = Math.floor(total / 86400);
                horas = Math.floor(total % 86400 / 3600);
                minutos = Math.floor(total % 3600 / 60);
                segundos = total % 60;
                return $t('Time left: %1d %2h %3m %4s')
                    .replace('%1', dias).replace('%2', horas)
                    .replace('%3', minutos).replace('%4', segundos);
            }, this);
            if (this.segundosRestantes() > 0) {
                this.timer = setInterval(this.atualizar.bind(this), 1000);
            }
            return this;
        },
        atualizar: function () {
            var segundos = Math.ceil((this.fim - Date.now()) / 1000);
            this.segundosRestantes(isFinite(segundos) ? Math.max(0, segundos) : 0);
            if (this.segundosRestantes() === 0 && this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        destroy: function () {
            clearInterval(this.timer);
            this.mensagem.dispose();
            return this._super();
        }
    });
});
