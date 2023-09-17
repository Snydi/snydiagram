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
export default {
  data() {
    return {
      isDragging: false,
      containerLeft: 0,
      containerTop: 0,
      initialX: 0,
      initialY: 0,
      tableData: [
        { "Name": "", "Type": "" },
      ],
      columnWidths: [
        { name: 'Name', width: '100px', inputWidth: '100%' },
        { name: 'Type', width: '50px', inputWidth: '100%' },
        // Add more columns with their respective widths as needed
      ],
    };
  },
  methods: {
    startDrag(event) {
      if (event.target.classList.contains("draggable-handle")) {
        this.isDragging = true;
        this.initialX = event.clientX - this.containerLeft;
        this.initialY = event.clientY - this.containerTop;

        window.addEventListener("mousemove", this.dragContainer);
        window.addEventListener("mouseup", this.stopDrag);
      }
    },
    dragContainer(event) {
      if (this.isDragging) {
        const newX = event.clientX - this.initialX;
        const newY = event.clientY - this.initialY;
        this.containerLeft = newX;
        this.containerTop = newY;
      }
    },
    stopDrag() {
      this.isDragging = false;
      window.removeEventListener("mousemove", this.dragContainer);
      window.removeEventListener("mouseup", this.stopDrag);
    },
    addRow() {
      const newRow = {};
      this.columnWidths.forEach(column => {
        newRow[column.name] = "";
      });
      this.tableData.push(newRow);
    },
    removeTable() {
      // Emit a custom event to notify the parent component to remove this table
      this.$emit("remove-table");
    },
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
  background-color: #ccc;
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
