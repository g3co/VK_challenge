define([
    'vue',
    'text!./index.html',
    'app/api'
], function(Vue, template, api) {
    'use strict';

    var vm = new Vue(),
        AuthLayout = {

        created: function() {

        },

        data: function() {
            return {
                userEmail: this.userEmail,
                userPassword: this.userPassword
            }
        },

        methods: {
            userAuth: function(event) {

                var $this = this,
                    $root = $this.$root,
                    data = {
                    email: this.userEmail,
                    password: this.userPassword
                };

                api('users/login', data)
                    .then(function (res) {

                        if(!!res == false) {
                            $root.$emit('notification', {
                                type: 'error',
                                text: 'Unknown error occurred'
                            })
                        }

                        if(!res.hasOwnProperty('error')) {
                            window.location.replace('/');
                            return
                        }

                        $root.$emit('notification', {
                            type: 'error',
                            text: res.message
                        })
                    })
                    .catch(function (err) {
                        $root.$emit('notification', {
                            type: 'error',
                            text: err
                        });
                        console.error(err)
                    });

                event.preventDefault();
                return false
            }
        },

        template: template

    };

    Vue.component('AuthLayout', AuthLayout);

    return AuthLayout
});