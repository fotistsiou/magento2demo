/**
 *  Amasty Base Submenu UI Component
 */

define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            vendorName: 'Amasty',
            templates: {
                closeButton: 'Amasty_Base/submenu/components/close_button',
                link: 'Amasty_Base/submenu/components/link',
                title: 'Amasty_Base/submenu/components/title',
                submenuSecond: 'Amasty_Base/submenu/second_level',
                menu_lists: 'Amasty_Base/submenu/menu_lists',
                links_list: 'Amasty_Base/submenu/links_list',
                item_label: 'Amasty_Base/submenu/components/item_label',
            },
            elemIndex: 0,
            exports: {
                solutions: 'ambase_solutions:data',
                simples: 'ambase_simples:data',
                links: 'ambase_links:data'
            }
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe({
                    secondLevelItem: false,
                    isDropdownActive: false,
                    noSearchResults: false,
                    simples: [],
                    solutions: [],
                    links: []
                });
        },

        /**
         * Splits data into different arrays
         *
         * @return {void}
         */
        afterRender: function () {
            var simples = [],
                solutions = [],
                links = [];

            this.data.forEach(function (item) {
                if (item.type === 'simple') {
                    simples.push(item);
                } else if (item.type === 'link') {
                    links.push(item);
                } else {
                    solutions.push(item);
                }
            }.bind(this));

            this.simples(simples);
            this.solutions(solutions);
            this.links(links);
        },

        /**
         * Set active item for second level
         *
         * @param {Object} item - target item
         * @return {void}
         */
        setActiveItem: function (item) {
            this.resetActiveItem();
            this.secondLevelItem(item);
        },

        /**
         * Reset active item for second level
         *
         * @return {void}
         */
        resetActiveItem: function () {
            this.secondLevelItem(false);
        }
    });
});
