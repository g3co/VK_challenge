define([
    'vue',
    'text!./index.html'
], function(Vue, template) {

    'use strict';

    return function init() {
        return new Vue({

            el: '#root',

            data: {
                message: 'my message here'
            },

            template: template
        })
    }

});