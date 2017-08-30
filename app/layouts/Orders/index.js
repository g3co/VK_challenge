define([
    'vue',
    'text!./index.html'
], function(Vue, template) {
    'use strict';

    var OrdersLayout = {

        props: [
            'initialContent'
        ],

        data: function() {
            return {
                content: this.initialContent
            }
        },

        template: template

    };

    Vue.component('OrdersLayout', OrdersLayout);

    return OrdersLayout
});