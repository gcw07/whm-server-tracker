/**
 * Format the given date.
 */
Vue.filter('date', value => {
    if (value === null) return;
    return dateFns.format(value, 'MMMM Do, YYYY');
});


/**
 * Format the given date as a timestamp.
 */
Vue.filter('datetime', value => {
    if (value === null) return;
    return dateFns.format(value, 'MMMM Do, YYYY h:mm A');
});


/**
 * Format the given date into a relative time.
 */
Vue.filter('relative', value => {
    if (value === null) return;
    return dateFns.distanceInWordsToNow(value, {addSuffix: true});
});


/**
 * Convert the first character to upper case.
 *
 * Source: https://github.com/vuejs/vue/blob/1.0/src/filters/index.js#L37
 */
Vue.filter('capitalize', value => {
    if (! value && value !== 0) {
        return '';
    }

    return value.toString().charAt(0).toUpperCase()
        + value.slice(1);
});


/**
 * Allow for placeholder text if the input value is empty.
 *
 * Source: https://github.com/freearhey/vue2-filters
 */
Vue.filter('placeholder', (value, text) => {
    return (value === undefined || value === '' || value === null) ? text : value;
});
