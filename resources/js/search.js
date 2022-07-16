export default () => ({
  focusBox() {
    this.$nextTick(() => {
      this.$refs.search.focus();
    });
  }
})
