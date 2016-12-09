
var check;



define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form'
    ],
    function (
        jQuery,
        ko,
        Component
    ) {
        'use strict';

        return function(){

            check = function(){

                console.log("ffsdf");
                var ua= navigator.userAgent, tem,
                    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                if(/trident/i.test(M[1])){
                    tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
                    return 'IE '+(tem[1] || '');
                }
                if(M[1]=== 'Chrome'){
                    tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
                    if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
                }
                M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
                if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
                var join =  M.join(' ');



                if(join.indexOf("Chrome") > -1){
                    jQuery("legend").map(function(el){
                        console.log(el);
                    });
                }
            };
        }

        return Component.extend({

            initialize: function () {
                this._super(); //you must call super on components or they will not render
                var self = this;

                console.log(this.saysWho());
            },

            saysWho: function(){
                var ua= navigator.userAgent, tem,
                    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                if(/trident/i.test(M[1])){
                    tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
                    return 'IE '+(tem[1] || '');
                }
                if(M[1]=== 'Chrome'){
                    tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
                    if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
                }
                M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
                if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
                return M.join(' ');
            }
        });
    }
)



