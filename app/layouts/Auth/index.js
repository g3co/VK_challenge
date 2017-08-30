define([
    'vue',
    'text!./index.html'
], function(Vue, template) {
    'use strict';

    var AuthLayout = {

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

    Vue.component('AuthLayout', AuthLayout);

    return AuthLayout
});