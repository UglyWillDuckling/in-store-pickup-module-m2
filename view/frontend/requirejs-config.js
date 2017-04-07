var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/originalShipping': 'Magento_Checkout/js/view/shipping',
            'Magento_Checkout/js/view/shipping': 'GaussDev_InStore/js/checkout/myShipping',

            'Magento_Checkout/template/shipping': 'GaussDev_InStore/template/checkout/shipping',

            'Magento_Checkout/js/view/billing-address': 'GaussDev_InStore/js/checkout/billing-address',
            'Magento_Checkout/template/billing-address': 'GaussDev_InStore/template/checkout/billing-address',

            'Magento_Checkout/js/view/shipping-address/address-renderer/default': 'GaussDev_InStore/js/checkout/shipping-address/default',

            'Magento_Checkout/js/view/shipping-information/address-renderer/default': 'GaussDev_InStore/js/checkout/shipping-information/default',

            'Magento_Checkout/js/action/select-billing-address': 'GaussDev_InStore/js/checkout/select-billing-address'
        }
    }
};