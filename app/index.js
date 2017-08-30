requirejs.config({
    paths: {
        app: '../../app',
        components: './components',
        vue: '../assets/js/vue',
        axios: '../assets/js/axios',
        text: '../assets/js/requirejs-text'
    }
});

requirejs([
    'app/app',
    'app/router',
    'layouts/Auth/index',
    'layouts/Orders/index'
], function(App, Router, AuthLayout, OrdersLayout) {

    App();

    (new Router([
        { path: '/', handler: function() {return true} },
        { path: '/auth', handler: function(id) {return AuthLayout} },
        { path: '/orders/:id', handler: function(id) {return OrdersLayout} }
    ]))
        .start();

    window.Router = Router;

});