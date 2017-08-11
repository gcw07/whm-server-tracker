
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries.
 */

require('./bootstrap');

// window.Vue = require('vue');

import Vue from 'vue'
import Buefy from 'buefy'

Vue.use(Buefy)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
