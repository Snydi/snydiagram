<template>
  <div id="app">
    <!-- Render multiple draggable tables -->
    <DraggableTable
        v-for="(table, index) in tables"
        :key="index"
        @remove-table="removeTable(index)"
    />

    <!-- Button to add a new table -->
    <button @click="addTable">Add Table</button>
  </div>
</template>

<script>
import DraggableTable from './components/DraggableTable.vue';

export default {
  data() {
    return {
      tables: [], // Array to hold your draggable tables
    };
  },
  components: {
    DraggableTable,
  },
  methods: {
    addTable() {
      // Create a new table data object and add it to the tables array
      this.tables.push({});
    },
    removeTable(index) {
      // Remove the table at the specified index from the tables array
      this.tables.splice(index, 1);
    },
  },
};
</script>
