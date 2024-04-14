/**
 *  Amasty Base Submenu Items UI Component
 */

define([
    'ko',
    'uiComponent',
    'Amasty_Base/js/actions/createMenuItem'
], function (ko, Component, createMenuItem) {
    'use strict';

    return Component.extend({
        defaults: {
            vendorName: 'Amasty',
            templates: {
                label: 'Amasty_Base/submenu/components/label',
                list: 'Amasty_Base/submenu/list',
                dropdown: 'Amasty_Base/submenu/dropdown/dropdown',
                dropdownContent: 'Amasty_Base/submenu/dropdown/content'
            },
            elemIndex: 0,
            solutions: [],
            simples: [],
            listens: {
                data: 'initChild'
            }
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe({
                    secondLevelItem: false,
                    isDropdownActive: false,
                    isNotFound: false
                });
        },

        /**
         * Init menu items
         *
         * @return {void}
         */
        initChild: function () {
            this.data.forEach(function (item) {
                createMenuItem.call(this, item, this.elemIndex);
                this.elemIndex += 1;
            }.bind(this));
        }
    });
});
