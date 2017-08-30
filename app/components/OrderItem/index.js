define([
    'vue',
    'text!./index.html'
], function(Vue, template) {
    'use strict';

    var OrderItem = {

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
    
    Vue.component('OrderItem', OrderItem);

    return OrderItem
});