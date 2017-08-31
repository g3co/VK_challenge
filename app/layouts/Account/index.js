define([
    'vue',
    'text!./index.html',
    'app/api'
], function(Vue, template, api) {
    'use strict';

    var AccountLayout = {

        created: function() {

            var $this = this;

            api('users/user', null, 'get')
                .then(function(user) {
                    if(!!user == false) {
                        return
                    }

                    $this.$set($this, 'user', user);
                })
        },

        data: function() {
            return {
                user: {}
            }
        },

        methods: {

        },

        template: template

    };

    Vue.component('AccountLayout', AccountLayout);

    return AccountLayout
});