/*
 |--------------------------------------------------------------------------
 | Server Tracker Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Server Tracker components which makes up the core
 | client application.
 */

/**
 * Global Components ...
 */
Vue.component('search-box', require('./global/search-box.vue'));

/**
 * Account Components ...
 */
Vue.component('accounts-listing', require('./accounts/accounts-listing.vue'));

/**
 * Dashboard Components ...
 */
Vue.component('dashboard-stats', require('./dashboard/dashboard-stats.vue'));
Vue.component('dashboard-servers', require('./dashboard/dashboard-servers.vue'));
Vue.component('dashboard-latest-accounts', require('./dashboard/dashboard-latest-accounts.vue'));

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

/**
 * Search Components ...
 */
Vue.component('search-servers', require('./search/search-servers.vue'));
Vue.component('search-accounts', require('./search/search-accounts.vue'));
