/**
 * Created by vladimir on 08.11.16..
 */


define(
    [
        'jquery',
        'underscore',
        'Magento_Checkout/js/view/originalShipping',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping'
            },
            stores: window.checkoutConfig.stores,
            selectedStoreId: ko.observable(window.checkoutConfig.storeid),
            enableButton: ko.observable(true), //fix for the button getting disabled
            showStores: ko.observable(true),

            initialize: function () {
                this._super(); //you must call super on components or they will not render
                var self = this;


                /*
                    Set the computed variables
                 */

                this.isInStorePickup = ko.computed(function(){
                    if(quote.shippingMethod())
                    {
                        var inStore = quote.shippingMethod().method_code == "instore";

                        self.updateFormFields(self.selectedStore(), inStore);
                        return inStore;
                    }
                });

                this.selectedStore = ko.computed(function(){
                    for(var i=0; i<self.stores.length; i++)
                    {
                        var store = self.stores[i];
                        if(stores[i].id == self.selectedStoreId()) return store;
                    }
                });

                this.showShippingAddress = ko.computed(function(){

                    if(quote.shippingMethod() && !self.isFormInline)
                    {
                        return !self.isInStorePickup();//hide the shipping address if the shipping method is 'instore'
                    }

                    return true;
                });
            },

            selectStoreAddress: function(parent, selectedAddress){

                parent.selectedStoreId(selectedAddress.id);
                return true;
            },

            updateFormFields: function(data, disable){

                for (var property in data) {
                     if(property == "id") continue;

                     if (data.hasOwnProperty(property)) {

                        if(property == "street")
                        {
                            var input = $('#shipping').find('input[name="' + property + '[0]"]');
                        }
                        else{
                            var input = $('#shipping').find('input[name="' + property + '"]');
                        }



                        if(disable){
                            input.val(data[property]);
                        }

                        input.keyup();
                        input.prop('disabled', disable);
                     }
                }
            },

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (parent, shippingMethod, event) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);

                if(shippingMethod.carrier_code == "instore")
                {
                    var store = $("input[name=store]").first();

                    //store.prop("checked", true);
                    store.click();
                    //find the selected store and click it
                }
                else{
                    //$("tr.stores").hide();
                    //enable all the disabled inputs
                    $("input:disabled").each(function(){
                        $(this).prop('disabled', false);
                    });

                    parent.showStores(false);

                    $("#shipping").show();//show the addresses if they were hidden
                }

                return true;
            },

            setStoreAddress: function(){

                var shippingAddress = quote.shippingAddress();
                var storeAddress = this.selectedStore();

                //replace the values from the shippingAddress with the storeAddress's
                for (var property in storeAddress)
                {
                    if (property == "id")
                    {
                    //send the selected storeId to the backend
                        this.saveStoreId(storeAddress['id']);
                        continue;
                    }

                    if (storeAddress.hasOwnProperty(property)) {

                        if(property == "street") {
                            shippingAddress[property] = new Array(storeAddress[property]);
                            continue;
                        }

                        shippingAddress[property] = storeAddress[property];
                    }
                }

                if(customer.isLoggedIn())
                {
                    //set the users data for the shipping address
                    shippingAddress['firstname'] = customer.customerData.firstname;
                    shippingAddress['lastname'] = customer.customerData.lastname;
                }

                quote.shippingAddress(shippingAddress);
            },

            saveStoreId: function(id){
                var url = 'http://magicbaby.loc/rest/V1/saveStoreId/id/' + id;

                $.ajax({
                    url: url,
                    type: 'GET',
                    contentType: 'application/json'
                });
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {

                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();
                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method.');

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid') ||
                        !quote.shippingMethod().method_code ||
                        !quote.shippingMethod().carrier_code ||
                        !emailValidationResult
                    ) {
                        return false;
                    }

                    shippingAddress = quote.shippingAddress();


                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );
                    //Copy form data to quote shipping address object
                    for (var field in addressData) {

                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                if(quote.shippingMethod().method_code == "instore"){
                    this.setStoreAddress();
                }

                return true;
            }

        });
    }
);