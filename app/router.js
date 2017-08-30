'use strict';

define([
    'vue'
], function(Vue) {

    var vm = new Vue(),
        router = {

            mode: !!window.history ? 'history' : 'anchor',

            base: '/',

            routes: [],

            matched: null,

            _before: function() {},
            _after: function() {},

            start: function() {
                var mode = this.mode;
                this.check(mode == 'history' ? window.location.pathname : window.location.href.slice(1))
            },

            go: function(path) {
                path = !!path ? path : '';

                var componentInitialization = this.check(path);
                if(!componentInitialization) {
                    return false
                }

                path = this.filter(path);

                this._before(path);

                if(this.mode == 'history') {
                    var url = this.base.concat(path);
                    window.history.pushState(null, '', url);

                    return
                }

                window.location.href = window.location.href.replace(/#(.*)$/, '') + '#' + path;
                location.href = path;

                this._after(path);
            },

            getParam: function(path, route) {

                var _match = route.match(/:(.*)$/i),
                    index = !!_match ? _match.index : -1;

                if(index >= 0) {
                    return path.slice(index)
                }

                return null
            },

            check: function(path) {
                var routes = this.routes,
                    componentInitialization = false,
                    route, param, _path;

                for(var i in routes) {
                    route = routes[i];
                    _path = route.path;
                    param = this.getParam(path, _path);

                    if(!!param) {
                        _path = _path.replace(/\/:(.[^\/]*)$/i, '');
                        path = path.replace(/\/(.[^\/]*)$/i, '');
                    }

                    if(_path == path) {
                        componentInitialization = route.handler;
                        break
                    }
                }

                if(!!Object.prototype.toString.call(componentInitialization).match(/Function/i)) {
                    this.matched = componentInitialization.call(null, param);

                    return this.matched
                }

                return false

            },

            filter: function(path) {
                return path.toString().replace(/\/$/, '').replace(/^\//, '')
            },

            on: function(event, callback) {

                if (!!Object.prototype.toString.call(callback).match(/Function/i) == false) {
                    return
                }

                switch (event.toLowerCase()) {
                    case 'before':
                        this._before = callback;
                        break;
                    case 'after':
                        this._after = callback;
                        break;
                    default:
                        break;
                }
            }
        };

    Vue.component('RouterLink', {

        props: ['href'],

        data: function() {
            return {
                linkTo: this.href
            }
        },

        methods: {
            traceRoute: function(event) {

                router.go(this.linkTo);

                vm.$emit('routeReload', this.linkTo);

                event.preventDefault();
                return false
            }
        },

        template: '<a :href="linkTo" @click="traceRoute"><slot></slot></a>'

    });

    Vue.component('RouterView', {

        data: function () {
            return {
                component: router.matched
            }
        },

        created: function() {

            var $this = this;

            vm.$on('routeReload', function (path) {

                var component = router.matched;

                if(!!component) {
                    $this.$set($this, 'component', component)
                }
            })
        },

        computed: {
            ViewComponent: function () {
                return this.component || { template: '<div>Not Found</div>' }
            }
        },

        render: function(h) {
            return h(this.ViewComponent)
        }

    });

    return function Router(routes, opts) {

        //init routes
        if(!!routes) {
            routes.forEach(function(item) {
                if(!!item) {
                    router.routes.push(item)
                }
            })
        }

        //presets
        if(!!opts) {
            router.mode = opts.mode || router.mode;
            router.base = opts.base || router.base;
        }

        return router

    }
});
