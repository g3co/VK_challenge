define([
    'vue',
    'text!./index.html',
    'app/api',
    'components/OrderItem/index'
], function(Vue, template, api) {
    'use strict';

    var data = {
        id: null,
        task: {}
    };

    var TaskLayout = {

        created: function() {

            var $this = this,
                id = this.id;

            api('tasks/task', { task_id: id }, 'get')
                .then(function(task) {
                    if(!!task) {
                        $this.$set($this, 'task', task)
                    }
                })
        },

        data: function() {
            return data
        },

        template: template

    };

    Vue.component('TaskLayout', TaskLayout);

    return function(id) {

        data.id = +id;

        return TaskLayout
    }
});