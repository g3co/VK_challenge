var proto = '/',
    base = 'vk.local:8090',
    apiPath = 'api.php',
    apiRoutes = [
        'POST@users/login',
        'POST@users/logout',
        'POST@users/user',
        'GET@users/user',
        'POST@tasks/task',
        'GET@tasks/tasks',
        'GET@tasks/task',
        'POST@tasks/task/hold',
        'POST@tasks/task/close'
    ];

define([
    'axios'
], function(axios) {
    'use strict';

    return function(route, body, _type) {
        if(!!route == false) {
            return false
        }

        var apiRoute = '';

        for(var r in apiRoutes) {
            if(!!apiRoutes[r].match(route)) {
                apiRoute = apiRoutes[r];
                break
            }
        }

        apiRoute = apiRoute.split('@');

        var type = apiRoute.shift(),
            method = apiRoute.shift();

        type = !!_type ? _type : type;

        switch(type.toLowerCase()) {
            case 'post':
                return axios.post(getFullPath(method), encodedObject(body), {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(filterResponse)
                    .catch(errorHandler);
                break;
            case 'get':
                return axios.get([
                    getFullPath(method),
                    encodedObject(body)
                ].join('&'))
                    .then(filterResponse)
                    .catch(errorHandler);
                break;
            default:break;
        }
    }
});

function encodedObject(obj) {
    var data = [];

    if(!!obj == false) {
        return ''
    }

    Object.keys(obj).forEach(function(item) {
        data.push(item +'='+ obj[item])
    });

    return data.join('&')
}

function errorHandler(err) {
    var status = !!err && !!err.response ? err.response.status : 500;

    switch(status) {
        case 401:
            window.location.replace('/auth');
            break;
        case 500:
            console.log(err);
            break;
        default:break
    }
}

function filterResponse(res) {

    console.log('Axios(response): %o', res);

    try {
        res = JSON.parse(res.request.response)
    } catch(e) {
        res = {
            error: true,
            message: e
        }
    }

    return res

}

function getFullPath(method) {
    return [
        proto,
        //base,
        [apiPath, 'method='+ method].join('?')
    ].join('')
}