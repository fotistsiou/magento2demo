/**
 *  Amasty Theme Mixin
 */

define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';

    return function (widget) {
        $.widget('mage.globalNavigation', widget, {
            options: {
                components: {
                    ambase_submenu: 'index = ambase_submenu'
                }
            },

            /**
             * Extended creating widget functionality via adding Amasty submenu
             * uiComponent from Amasty module
             *
             * @private
             * @return {void}
             */
            _create: function () {
                registry.get(this.options.components.ambase_submenu, function (ambase_submenu) {
                    this.options.components.ambase_submenu = ambase_submenu;
                }.bind(this));

                this._super();
            },

            /**
             * Extended close functionality with clearing second level of submenu
             * from Amasty module
             *
             * @param {jQuery.Event} event
             * @private
             * @return {void}
             */
            _close: function (event) {
                if (this.options.components.ambase_submenu.resetActiveItem) {
                    this.options.components.ambase_submenu.resetActiveItem();
                }
                this._super(event);
            },

            /**
             * Extended open functionality with clearing second level of submenu
             * from Amasty module
             *
             * @param {jQuery.Event} event
             * @private
             * @return {void}
             */
            _open: function (event) {
                if (this.options.components.ambase_submenu.resetActiveItem) {
                    this.options.components.ambase_submenu.resetActiveItem();
                }
                this._super(event);
            }
        });

        return $.mage.globalNavigation;
    }
});
