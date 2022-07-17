export default () => ({
  currentFocused: '',

  init () {
    addEventListener('focusin', () => {
      this.currentFocused = document.activeElement
    });
  },

  focusBox() {
    this.$nextTick(() => {
      this.$refs.search.focus();
    });
  },

  isFocused() {
    // console.log(this.currentFocused);
    // console.log(this.$refs.search);
    // console.log(this.currentFocused === this.$refs.search);
    return this.currentFocused === this.$refs.search;
  },

  isMacintosh() {
    return navigator.platform.indexOf('Mac') === 0;
  },

  isWindows() {
    return navigator.platform.indexOf('Win') === 0
  },
})
