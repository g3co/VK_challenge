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

            api('tasks/tasks')
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

        methods: {
            syncLatest: function() {
                var $this = this,
                    tasks = [].concat(this.tasks),
                    first_task_id = tasks.shift();

                first_task_id = !!first_task_id ? first_task_id.id : 0;

                if(!first_task_id) {
                    return
                }

                api('tasks/tasks', { first_task: first_task_id })
                    .then(function(latest) {
                        if(!!latest == false || !latest.length) {
                            return
                        }

                        //insert latest before others
                        $this.$set($this, 'tasks', [].concat(latest, $this.tasks))
                    })
            },
            
            loadOlder: function() {
                var $this = this,
                    tasks = [].concat(this.tasks),
                    last_task_id = tasks.pop();

                last_task_id = !!last_task_id ? last_task_id.id : 0;

                if(!last_task_id) {
                    return
                }

                api('tasks/tasks', { last_task: last_task_id })
                    .then(function(older) {
                        if(!!older == false || !older.length) {
                            return
                        }

                        //insert latest before others
                        $this.$set($this, 'tasks', [].concat($this.tasks, older))
                    })
            }
        },

        template: template

    };

    Vue.component('OrdersLayout', OrdersLayout);

    return OrdersLayout
});