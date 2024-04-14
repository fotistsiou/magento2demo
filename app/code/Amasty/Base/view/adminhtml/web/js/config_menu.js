/**
 *  Amasty Base Config Menu UI Component
 */

define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_Base/config/menu',
            templates: {
                items: 'Amasty_Base/config/items'
            },
            extensions: [],
            base: {},
            solutions: []
        },

        /**
         * Is tab active
         *
         * @param {Array} extensions
         * @returns {Boolean}
         */
        isActive: function (extensions) {
            return extensions.some(function (item) {
                return item.is_active;
            });
        }
    });
});
