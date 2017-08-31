requirejs.config({
    paths: {
        app: '../../app',
        components: './components',
        helpers: './helpers',
        vue: '../assets/js/vue',
        axios: '../assets/js/axios',
        text: '../assets/js/requirejs-text'
    }
});

requirejs([
    'app/app',
    'app/router',
    'helpers/getCookie',
    'layouts/Auth/index',
    'layouts/Logout/index',
    'layouts/Orders/index',
    'layouts/Account/index',
    'layouts/Task/index'
], function(App, Router, getCookie, AuthLayout, LogoutLayout, OrdersLayout, AccountLayout, TaskLayout) {

    App();

    var AppRouter = new Router([
        { path: '/', handler: function() {return true} },
        { path: '/auth', handler: function() {return AuthLayout} },
        { path: '/logout', handler: function() {return LogoutLayout} },
        { path: '/orders', handler: function() {return OrdersLayout} },
        { path: '/account', handler: function() {return AccountLayout} },
        { path: '/task/:id', handler: TaskLayout }
    ]);

    AppRouter
        .start()
        .on('before', function(path) {

            var user_id = getCookie('user_id');

            if(!!user_id == false && !!path.match(/auth/i) == false) {
                AppRouter.go('/auth');
                return false
            }

        });

});