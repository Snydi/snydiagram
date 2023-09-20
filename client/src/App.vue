<template>
  <div id="app">
    <Header />

    <DraggableTable
        v-for="(table, index) in tables"
        :key="index"
        :index="table.id"
        :onRemove="removeTable"
    ></DraggableTable>

    <button @click="addTable">Add Table</button>
  </div>
</template>

<script>
import { ref } from 'vue';
import DraggableTable from './components/DraggableTable.vue';
import Header from './components/Header.vue';
import axios from 'axios'; // Import Axios

export default {
  setup() {
    const tables = ref([
    ]);

    const addTable = () => {
      tables.value.push({
        id: Date.now(),
        Name: "", // Add the "Name" property for the new table
        Type: "", // Add the "Type" property for the new table
      });
    };

    const removeTable = (index) => {
      tables.value = tables.value.filter((table) => table.id !== index);
    };

    const sendTablesToApi = () => {
      const apiUrl = 'your-api-endpoint';

      // Use Axios to make a POST request
      axios
          .post(apiUrl, tables.value, {
            headers: {
              'Content-Type': 'application/json',
            },
          })
          .then((response) => {
            // Handle a successful response from the API here
            console.log('Tables sent successfully!');
          })
          .catch((error) => {
            // Handle errors here
            console.error('Error sending tables to the API:', error);
          });
    };

    return {
      tables,
      addTable,
      removeTable,
      sendTablesToApi,
    };
  },
  components: {
    DraggableTable,
    Header,
  },
};
</script>

<style scoped>
/* Styles remain the same */
</style>
