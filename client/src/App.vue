<template>
  <div id="app">
    <Header />

    <div v-for="(table, index) in tables" :key="table.Id">
      <DraggableTable
          :index="index"
          :onRemove="removeTable"
          :tableData="table"
          @addRow="addRowToTable(index)"
      ></DraggableTable>
    </div>

    <button @click="addTable">Add Table</button>
    <button @click="sendTablesToApi">Send Tables to API</button>
  </div>
</template>

<script>
import { ref } from 'vue';
import DraggableTable from './components/DraggableTable.vue';
import Header from './components/Header.vue';
import axios from 'axios';

export default {
  setup() {
    const tables = ref([]);

    const addTable = () => {
      const newTable = {
        Id: Date.now(), // Use "Id" instead of "id"
        rows: [], // Initialize an empty array for "rows"
      };
      tables.value.push(newTable);
    };

    const removeTable = (index) => {
      tables.value.splice(index, 1);
    };

    const addRowToTable = (tableIndex) => {
      // Trigger the addRow method for the specified table
      tables.value[tableIndex].rows.push({
        Name: "",
        Type: "",
      });
    };

    const sendTablesToApi = () => {
      const apiUrl = 'your-api-endpoint';

      axios
          .post(apiUrl, tables.value, {
            headers: {
              'Content-Type': 'application/json',
            },
          })
          .then((response) => {
            console.log('Tables sent successfully!');
          })
          .catch((error) => {
            console.error('Error sending tables to the API:', error);
          });
    };

    return {
      tables,
      addTable,
      removeTable,
      addRowToTable,
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
