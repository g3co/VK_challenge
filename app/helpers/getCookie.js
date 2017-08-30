'use strict';

define([], function() {
    return getCookie
});

function getCookie(id) {
    var cookie = document.cookie,
        item = {},
        items = [],
        key, value;

    items = cookie.split(';');

    for(var i in items) {
        item = items[i].trim().split('=');
        key = item.shift();
        value = item.shift();

        if(key == id) {
            return value
        }
    }
}