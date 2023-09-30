<template>
  <header>
    <h1>Export</h1>

    <button @click="sendTablesToApi">Send to API</button>
  </header>
</template>

<script>
export default {
  methods: {
    sendTablesToApi() {
      this.$emit('sendTablesToApi');
    },
  },
};
</script>

<style scoped>
header {
  background-color: #ff5252;
  color: white;
  padding: 16px;
  text-align: center;
}
</style>
