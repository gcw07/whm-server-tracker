/*
 |--------------------------------------------------------------------------
 | Server Tracker Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Server Tracker components which makes up the core
 | client application.
 */

/**
 * Account Components ...
 */
Vue.component('accounts-listing', require('./accounts/accounts-listing.vue'));

/**
 * Server Components ...
 */
Vue.component('servers-listing', require('./servers/servers-listing.vue'));
Vue.component('servers-edit', require('./servers/servers-edit.vue'));
Vue.component('servers-show', require('./servers/servers-show.vue'));

/**
 * User Components ...
 */
Vue.component('users-listing', require('./users/users-listing.vue'));
Vue.component('users-edit', require('./users/users-edit.vue'));
