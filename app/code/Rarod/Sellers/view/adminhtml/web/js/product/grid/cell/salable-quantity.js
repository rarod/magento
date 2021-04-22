/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_InventorySalesAdminUi/product/grid/cell/salable-quantity.html'
        },

        /**
         * Get salable quantity data (stock name and salable qty)
         *
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getSalableQuantityData: function (record) {
            let sources = JSON.parse(window.adminAcl.sources);
            let allowed = [];
            for(var key in record[this.index]) {
                if(sources.indexOf(record[this.index][key].stock_name) > -1) {
                    allowed.push(record[this.index][key]);
                }
            }
            return allowed;
        }
    });
});
