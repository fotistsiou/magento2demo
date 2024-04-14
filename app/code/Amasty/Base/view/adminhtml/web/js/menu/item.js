/**
 *  Amasty Base Menu Item UI Component
 */

define([
    'ko',
    'uiComponent',
    'underscore',
    'Amasty_Base/js/actions/createMenuItem'
], function (ko, Component, _, createMenuItem) {
    'use strict';

    return Component.extend({
        defaults: {
            componentType: '',
            elemIndex: 0,
            typeSolution: 'solution'
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe({
                    isDropdownActive: false,
                    visible: true,
                    prevStr: '',
                    foundStr: '',
                    residualStr: ''
                });
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if (this.componentType === this.typeSolution) {
                this.items.forEach(function (item) {
                    createMenuItem.call(this, item, this.elemIndex);
                    this.elemIndex += 1;
                }.bind(this));
            }

            return this;
        },

        onDropdownClick: function () {
            this.toggleDropdown();
        },

        /**
         * Toggle dropdown
         *
         * @param {Boolean} showMode
         * @return {void}
         */
        toggleDropdown: function (showMode) {
            var isActive = _.isUndefined(showMode) ? !this.isDropdownActive() : showMode;

            this.isDropdownActive(isActive);
        },

        /**
         * On search query handler
         *
         * @param {String} value
         * @param {Boolean} isParentMatch
         * @return {Boolean}
         */
        onSearchQuery: function (value, isParentMatch) {
            if (!value) {
                this.useDefaultMode();

                return true;
            }

            var foundIndex = this.label.toLowerCase().indexOf(value),
                isMatchFound = foundIndex !== -1,
                hasInChild;

            if (isMatchFound) {
                this.highlightValue(value, foundIndex);
            }

            this.visible(isMatchFound || isParentMatch); // Show child items if parent item founded by search

            if (this.componentType === this.typeSolution) {
                hasInChild = this.searchInChildren(value, isMatchFound);

                this.toggleDropdown(hasInChild);
                this.visible(isMatchFound || hasInChild);
            }

            return isMatchFound || hasInChild;
        },

        /**
         * Search in children
         *
         * @param {String} value
         * @param {Boolean} isParentMatch
         * @return {Boolean}
         */
        searchInChildren: function (value, isParentMatch) {
            var hasInChildren = false;

            this.elems().forEach(function (item) {
                var isInclude;

                item.foundStr('');
                isInclude = item.onSearchQuery(value, isParentMatch);

                if (isInclude) {
                    hasInChildren = true;
                }
            });

            return hasInChildren;
        },

        /**
         * Highlight found Value
         *
         * @param {String} value
         * @param {Number} index
         * @return {void}
         */
        highlightValue: function (value, index) {
            var substr;

            this.foundStr(this.label.slice(index, value.length + index));
            substr = this.label.split(this.foundStr());

            if (substr.length > 2) {
                for (var i = 2; i < substr.length; i++) {
                    substr[1] += this.foundStr() + substr[i];
                }
            }

            this.prevStr(substr[0]);
            this.residualStr(substr[1]);
        },

        /**
         * Use default mode
         *
         * @return {void}
         */
        useDefaultMode: function () {
            this.restoreChanges();

            this.elems().forEach(function (item) {
                item.restoreChanges();
            });
        },

        /**
         * Restore changes
         *
         * @return {void}
         */
        restoreChanges: function () {
            this.foundStr('');
            this.visible(true);
            this.toggleDropdown(false);
        }
    });
});
