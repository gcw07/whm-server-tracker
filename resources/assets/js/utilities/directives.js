/**
 * Focus on the element using v-focus
 */
Vue.directive('focus', {
    inserted: function (el) {
        el.focus();
    }
});
