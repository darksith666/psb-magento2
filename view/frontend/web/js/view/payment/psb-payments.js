/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'psb',
                component: 'Paysbuy_PsbGateway/js/view/payment/method-renderer/psb-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
