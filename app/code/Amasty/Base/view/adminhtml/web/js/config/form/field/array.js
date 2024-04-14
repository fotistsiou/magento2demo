define([
    'jquery',
    'mage/template',
    'text!Amasty_Base/template/config/form/field/array/row.html'
], function ($, mageTemplateRenderer, rowTemplate) {
    'use strict';

    $.widget('mage.amLicenseKeys', {
        options: {
            selectors: {
                licenseKeysContainer: '[data-ambase-license=license-keys-container]',
                addRowButton: '[data-ambase-license=add-license-key]',
            },
            columnNames: [],
            existingKeys: [],
            elementName: []
        },

        /**
         * @returns {void}
         */
        _create: function () {
            $(this.options.selectors.addRowButton).on('click', () => {
                this.addRow();
            });

            if (this.options.existingKeys.length === 0) {
                this.addRow();
            } else {
                this.options.existingKeys.forEach((existingKey) => {
                    this.addRow(existingKey);
                });
            }
        },

        /**
         * @param {Object} rowData
         * @returns {void}
         */
        addRow: function (rowData = null) {
            let templateValues;

            // Prepare template values
            if (rowData) {
                templateValues = rowData;
            } else {
                const d = new Date();
                templateValues = {
                    'option_extra_attrs': {},
                    _id: '_' + d.getTime() + '_' + d.getMilliseconds()
                };

                this.options.columnNames.forEach((columnName) => {
                    templateValues[columnName] = '';
                });
            }

            templateValues.columnNames = this.options.columnNames;
            templateValues.elementName = this.options.elementName;


            $(this.options.selectors.licenseKeysContainer)
                .append(mageTemplateRenderer(rowTemplate, {data: templateValues}));

            this.bindRemoveRow(templateValues._id);
        },

        /**
         * @param {string} rowId
         * @returns {void}
         */
        bindRemoveRow: function (rowId) {
            $(`#${rowId}`).find('[data-ambase-license=delete-license-key]').on('click', function () {
                $(`#${rowId}`).remove();
            });
        }
    });

    return $.mage.amLicenseKeys;
});
