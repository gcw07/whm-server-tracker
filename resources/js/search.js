export default () => ({
  focusBox() {
    this.$nextTick(() => {
      this.$refs.search.focus();
    });
  },

  isMacintosh() {
    return navigator.platform.indexOf('Mac') === 0;
  },

  isWindows() {
    return navigator.platform.indexOf('Win') === 0
  },
})
