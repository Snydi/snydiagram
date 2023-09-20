<template>
  <div class="draggable-container" :style="{ left: containerLeft + 'px', top: containerTop + 'px' }" @mousedown="startDrag">
    <div class="table-actions">
      <button class="remove-button" @click="removeTable">X</button>
    </div>
    <h1 class="draggable-handle">New table</h1>
    <div class="table-container">
      <div class="table-wrapper">
        <table>
          <thead>
          <tr>
            <th v-for="(column, columnIndex) in columnWidths" :key="columnIndex" :style="{ width: column.width }">{{ column.name }}</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(row, rowIndex) in tableData" :key="rowIndex">
            <td v-for="(column, columnIndex) in columnWidths" :key="columnIndex" :style="{ width: column.width }">
              <input type="text" v-model="row[column.name]" :style="{ width: column.inputWidth }" />
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <button @click="addRow">Add Row</button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';

export default {
  props: {
    index: Number, // Add a prop to pass the index of the table
    onRemove: Function, // Add a prop to pass the remove function from the parent
  },
  setup(props) {
    const isDragging = ref(false);
    const containerLeft = ref(0);
    const containerTop = ref(0);
    const initialX = ref(0);
    const initialY = ref(0);

    const tableData = reactive([
      { "Name": "", "Type": "" },
    ]);

    const columnWidths = reactive([
      { name: 'Name', width: '100px', inputWidth: '100%' },
      { name: 'Type', width: '50px', inputWidth: '100%' },
      // Add more columns with their respective widths as needed
    ]);

    const startDrag = (event) => {
      if (event.target.classList.contains("draggable-handle")) {
        isDragging.value = true;
        initialX.value = event.clientX - containerLeft.value;
        initialY.value = event.clientY - containerTop.value;

        window.addEventListener("mousemove", dragContainer);
        window.addEventListener("mouseup", stopDrag);
      }
    };

    const dragContainer = (event) => {
      if (isDragging.value) {
        const newX = event.clientX - initialX.value;
        const newY = event.clientY - initialY.value;
        containerLeft.value = newX;
        containerTop.value = newY;
      }
    };

    const stopDrag = () => {
      isDragging.value = false;
      window.removeEventListener("mousemove", dragContainer);
      window.removeEventListener("mouseup", stopDrag);
    };

    const addRow = () => {
      const newRow = {};
      columnWidths.forEach(column => {
        newRow[column.name] = "";
      });
      tableData.push(newRow);
    };

    const removeTable = () => {
      // Call the remove function passed from the parent component
      props.onRemove(props.index);
    };

    onMounted(() => {
      // Any setup code you need when the component is mounted
    });

    onBeforeUnmount(() => {
      // Clean up code when the component is about to be removed
    });

    return {
      isDragging,
      containerLeft,
      containerTop,
      tableData,
      columnWidths,
      startDrag,
      dragContainer,
      stopDrag,
      addRow,
      removeTable,
    };
  },
};
</script>

<style scoped>
.draggable-container {
  position: absolute;
  width: 400px; /* Adjust the width as needed */
  border: 1px solid #ccc;
  background-color: #fff;
}

.draggable-handle {
  cursor: grab;
  background-color: #2eecff;
  padding: 8px;
  margin: 0;
  user-select: none;
}

.table-actions {
  position: absolute;
  top: 0;
  right: 0;
  padding: 4px;
}

.remove-button {
  background-color: transparent;
  border: none;
  cursor: pointer;
  font-size: 16px;
  color: red;
}

.table-container {
  border-collapse: collapse;

  overflow-x: auto; /* Add horizontal scrollbar */
}

.table-wrapper {
  width: max-content; /* Adjust width to fit content */
}

table {
  width: 100%;
}

th,
td {
  border: 1px solid #ccc;
  padding: 8px;
  white-space: nowrap; /* Prevent text from wrapping */
}
</style>
