define([
    'vue',
    'app/api'
], function(Vue, api) {
    'use strict';

    var LogoutLayout = {

        created: function() {
            this.logOut()
        },

        methods: {
            logOut: function(event) {

                var $this = this,
                    $root = $this.$root;

                api('users/logout')
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

                !!event && event.preventDefault();
                return false
            }
        },

        template: '<div></div>'

    };

    Vue.component('LogoutLayout', LogoutLayout);

    return LogoutLayout
});