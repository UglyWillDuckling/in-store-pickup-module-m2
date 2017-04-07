/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data'
    ],
    function ($, quote, checkoutData) {
        'use strict';

        return function (billingAddress) {
            var address = null;

            if(!checkoutData.getSelectedBillingAddress() && quote.shippingMethod().method_code == 'instore')
            {
                quote.billingAddress(null);
                return;
            }

            if (quote.shippingAddress() && billingAddress.getCacheKey() == quote.shippingAddress().getCacheKey()) {
                address = $.extend({}, billingAddress);
                address.saveInAddressBook = null;
            } else {
                address = billingAddress;
            }
            quote.billingAddress(address);
        };
    }
);
