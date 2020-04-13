
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/ExampleComponent.vue'));


function registerServiceWorker() {
    // register sw script in supporting browsers
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('swplanner.js', {scope: '/'}).then(function () {
            console.log('Service Worker registered successfully.');
        }).catch(function (error) {
            console.log('Service Worker registration failed:', error);
        });
    }
}
registerServiceWorker();