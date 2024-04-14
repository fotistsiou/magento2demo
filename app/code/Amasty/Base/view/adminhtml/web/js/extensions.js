/**
 *  Amasty Base Extensions UI Component
 */

define([
    'uiComponent',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_Base/extensions/extensions',
            templates: {
                updateButtons: 'Amasty_Base/extensions/update-buttons',
                filterButtons: 'Amasty_Base/extensions/filter-buttons',
                table: 'Amasty_Base/extensions/table'
            },
            modulesData: [],
            update: [],
            solutions: [],
            shouldRenderLicenseStatus: false,
            subscription: {
                statusTypesToCheck: ['warning', 'error'],
                url: 'https://amasty.com/amasty_recurring/customer/subscriptions',
                faqUrl: 'https://amasty.com/knowledge-base/what-does-each-product-license-status-in-the-base-extension-mean'
            },
            stateValues: {
                default: 'default',
                solutions: 'solutions',
                update: 'update'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.update = this.prepareModules(this.modulesData.filter(function (item) {
                return item.has_update;
            }));

            this.solutions = this.prepareModules(this.modulesData.filter(function (item) {
                return item.upgrade_url;
            }));

            this.modules(this.prepareModules(this.modulesData));

            this.shouldRenderLicenseStatus = this.modules().some((module) => !!module.verify_status);

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super()
                .observe({
                    state: 'default',
                    modules: []
                });
        },

        /**
         * Use Extensions Filter
         *
         * @param {String} state
         * @returns {void}
         */
        useGridFilter: function (state) {
            this.state(state);

            if (this.stateValues.default === state) {
                this.modules(this.prepareModules(this.modulesData));

                return;
            }

            this.modules(this[state]);
        },

        /**
         * Is filter active
         *
         * @param {String} state
         * @returns {Boolean}
         */
        isActive: function (state) {
            return this.state() === state;
        },

        /**
         * Prepare modules data
         *
         * @param {Array} data
         * @returns {Array}
         */
        prepareModules: function (data) {
            var availableUpgrade = data.filter(function (item) {
                    return item.upgrade_url;
                }),
                needUpdate = data.filter(function (item) {
                    return item.has_update && !item.upgrade_url;
                }),
                modules = data.filter(function (item) {
                    return !item.has_update && !item.upgrade_url;
                });

            return availableUpgrade.concat(needUpdate, modules);
        },

        /**
         * @param {Object} module
         * @returns {Object|null}
         */
        getActionLink: function (module) {
            if (!module.upgrade_url && !this.isNeedCheckSubscription(module)) {
                return null;
            }

            return {
                text: this.isNeedCheckSubscription(module) ? $t('Check Your Subscriptions') : $t('Upgrade Your Plan'),
                url: this.isNeedCheckSubscription(module) ? this.subscription.url : module.upgrade_url
            }
        },

        /**
         * @param {Object} module
         * @returns {boolean}
         */
        isNeedCheckSubscription: function (module) {
            return this.subscription.statusTypesToCheck.includes(module.verify_status?.type);
        }
    });
});
