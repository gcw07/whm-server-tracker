/*
 |--------------------------------------------------------------------------
 | Server Tracker Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Server Tracker components which makes up the core
 | client application.
 */

/**
 * Server Components ...
 */
Vue.component('servers-listing', require('./servers/servers-listing.vue'));
Vue.component('servers-edit-server', require('./servers/servers-edit.vue'));
Vue.component('servers-show-server', require('./servers/servers-show.vue'));

/**
 * Account Components ...
 */
Vue.component('accounts-listing', require('./accounts/accounts-listing.vue'));

/**
 * User Components ...
 */
Vue.component('users-listing', require('./users/users-listing.vue'));