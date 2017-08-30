define([
    'vue',
    'text!./index.html',
    'helpers/getCookie'
], function(Vue, template, getCookie) {

    'use strict';

    return function init() {
        return new Vue({

            el: '#root',
            
            data: {
                user_id: getCookie('user_id'),
                user_type: getCookie('user_type')
            },

            created: function() {
                this.$on('notification', function(data) {
                    alert([
                        data.type,
                        data.text
                    ].join(': '))
                });
            },

            template: template
        })
    }

});