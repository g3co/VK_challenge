define([
    'vue',
    'text!./index.html',
    'app/api',
    'components/OrderItem/index'
], function(Vue, template, api, OrderItem) {
    'use strict';

    var OrdersLayout = {

        created: function() {

            var $this = this;

            api('tasks/tasks', {last_task: 100, first_task: 0})
                .then(function(tasks) {
                    if(!!tasks && tasks.length) {
                        $this.$set($this, 'tasks', tasks)
                    }
                })
        },

        data: function() {
            return {
                tasks: []
            }
        },

        template: template

    };

    Vue.component('OrdersLayout', OrdersLayout);

    return OrdersLayout
});