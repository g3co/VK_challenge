define([
    'vue',
    'text!./index.html',
    'app/api',
    'helpers/getCookie',
    'components/OrderItem/index'
], function(Vue, template, api, getCookie) {
    'use strict';

    var data = {
        id: null,
        user_id: +getCookie('user_id'),
        user_type: +getCookie('user_type'),
        task: {},
        classNames: {
            disabled: false
        }
    };

    var TaskLayout = {

        //don't look at this shit
        created: function() {
            var id = this.id;

            if(id > 0) {
                this.getTask();
                return
            }

            this.$set(this, 'id', 0);
        },

        updated: function() {
            var id = this.id;

            if(isNaN(id)) {
                this.$set(this, 'id', 0);
                this.$set(this, 'task', {});
                return
            }
        },

        methods: {

            createTask: function(event) {
                var $this = this,
                    $root = this.$root,
                    toggleViewState = this.toggleViewState.bind(this),
                    task_id = this.id,
                    task_name = this.task.task_name,
                    task_descr = this.task.task_descr,
                    task_price = this.task.price;

                if(!!task_id) {
                    return
                }

                toggleViewState(true);

                api('tasks/task', {
                    task_name: task_name,
                    task_descr: task_descr,
                    price: task_price
                }, 'post')
                    .then(function(task) {
                        toggleViewState(false);

                        if(!!task) {
                            $this.$set($this, 'id', task.id);
                            $this.$set($this, 'task', task)
                        }

                        $root.$emit('notification', {
                            type: !!task.error ? 'error' : 'success',
                            text: task.message
                        });
                    })
                    .catch(function (err) {
                        toggleViewState(false);

                    });

                !!event && event.preventDefault();
                return false
            },

            getTask: function() {
                var $this = this,
                    id = this.id;

                api('tasks/task', { task_id: id }, 'get')
                    .then(function(task) {
                        if(!!task) {
                            $this.$set($this, 'task', task)
                        }
                    })
            },

            closeTask: function() {
                var $this = this,
                    $root = this.$root,
                    toggleViewState = this.toggleViewState.bind(this),
                    getTask = this.getTask.bind(this),
                    task_id = this.id,
                    state = this.task.state;

                if(!!state == false) {
                    return
                }

                toggleViewState(true);

                api('tasks/task/close', { task_id: task_id })
                    .then(function(res) {

                        $root.$emit('notification', {
                            type: 'success',
                            text: res.message
                        });

                        toggleViewState(false);
                    })
                    .then(getTask)
                    .catch(function (err) {

                        $root.$emit('notification', {
                            type: 'error',
                            text: err
                        });

                        toggleViewState(false);
                    })
            },

            holdTask: function() {
                var $this = this,
                    $root = this.$root,
                    toggleViewState = this.toggleViewState.bind(this),
                    getTask = this.getTask.bind(this),
                    user_type = this.user_type,
                    task_id = this.id,
                    state = +this.task.state;

                if(state !== 0 && user_type !== 2) {
                    return
                }

                this.toggleViewState(true);

                api('tasks/task/hold', { task_id: task_id })
                    .then(function(res) {

                        $root.$emit('notification', {
                            type: 'success',
                            text: res.message
                        });

                        toggleViewState(false);
                    })
                    .then(getTask)
                    .catch(function (err) {

                        $root.$emit('notification', {
                            type: 'error',
                            text: err
                        });

                        toggleViewState(false);
                    })
            },


            toggleViewState: function(state) {
                this.$set(this.classNames, 'disabled', state)
            }

        },

        computed: {
            className: function() {
                return {
                    disabled: this.disabled
                }
            }
        },

        data: function() {
            return data
        },

        template: template

    };

    Vue.component('TaskLayout', TaskLayout);

    return function(id) {

        console.log('Task(id): %o', id);

        data.id = +id;

        return TaskLayout
    }
});

//close: state == 1
//hold: user_type == 2 && state == 0