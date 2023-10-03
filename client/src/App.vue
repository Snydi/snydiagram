<script setup>
import { Handle, Position, VueFlow, useVueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'
import { ref, provide, defineProps, inject } from 'vue'
const { updateEdge, addEdges } = useVueFlow();

function onConnect(params) {
  params.updatable = true;
  return addEdges([params])
}
function onEdgeUpdate({ edge, connection }) {
  return updateEdge(edge, connection)
}

const TableStyle = {
  display: 'flex',
  border: '1px solid #10b981',
  background: '#ff3736',
  color: 'white',
  borderRadius: '5px',
  width: '200px',
  height: '50px',
  alignItems: 'center',
  justifyContent: 'space-between',
}
const RowStyle = {
  border: '1px solid #10b981',
  background: '#ffffff',
  color: '#000000',
  borderRadius: '0px',
  width: '200px',
  height: '50px',
}
const elements = ref([
  {
    id: '1',
    type: 'table',
    label: 'First table',
    data: { toolbarPosition: Position.Top, toolbarVisible: true },
    position: { x: 0, y: -100 },
    style: TableStyle,
  },
])

const addTable = () => {
  elements.value.push({
    id: Math.random().toString(),
    type: 'table',
    label: 'New Table',
    data: {
      toolbarPosition: Position.Top,
      toolbarVisible: true
    },
    position: { x: 0, y: 0 },
    style: TableStyle,

  })
}
const updateLabel = (id, newLabel) => {
  const element = elements.value.find(el => el.id === id);
  if (element) {
    element.label = newLabel;
  }
}

const addRow = (nodeProps) => {

  const existingRows = elements.value.filter(el => el.parentNode === nodeProps.id);
  const position = nodeProps.data.position || { x: 0, y: 0 }
  elements.value = [...elements.value, {
    id: Date.now().toString(), // use a timestamp to generate a unique id
    type: 'row', // change this to your actual row node type
    label: 'New Row',
    position: { x: position.x, y: position.y + 50 * (existingRows.length +1) }, // calculate y based on number of existing rows
    style: RowStyle,
    draggable: false, //disable dragging for rows
    parentNode: nodeProps.id, //set the parent of the node (have it dragged with parent
    sourcePosition: 'right', // source handle is on the right
    targetPosition: 'left', // target handle is on the left
    data: { editing: false, showModal: false, KeyMod: '', selectedSqlType: '' },
  }]


}

const deleteNode = (nodeId) => {

  const nodeToDelete = elements.value.find(el => el.id === nodeId);

  if (nodeToDelete.type === 'table') {
    elements.value = elements.value.filter(el => el.parentNode !== nodeId);
  }

  elements.value = elements.value.filter(el => el.id !== nodeId);

  if (nodeToDelete.type === 'row') {
    const siblingRows = elements.value.filter(el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y);
    siblingRows.forEach((row, index) => {
      row.position.y = nodeToDelete.position.y + 40 * index;
    });
  }
};
const collectData = () => {
  const links = elements.value.filter(el => el.type === 'smoothstep');
  const nodes = elements.value.filter(elem => elem.type === 'table');
  const data = nodes.map(node => {
    const rows = elements.value.filter(row => row.parentNode === node.id);
    return {
      table: node.label,
      rows: rows.map(row => {
        return {id: row.id , label: row.label, KeyMod: row.data.KeyMod, sqlType: row.data.selectedSqlType, connectedTo: links.filter(link => link.target === row.id ).map(link => link.source)};
      }),
    }

  });

  console.log(data);

}

const updateKeyMod = (id, KeyMod) => {
  const element = elements.value.find(el => el.id === id);
  if (element) {
    element.data.KeyMod = KeyMod;
  }
}
provide('updateKeyMod', updateKeyMod);
provide('updateLabel', updateLabel);
provide('elements', elements)
provide('addTable', addTable)

</script>

<template>
  <button @mousedown.stop @click="collectData">Collect Data</button>

  <button @mousedown.stop @click="addTable">Add Table</button>
  <VueFlow
      :default-edge-options="{ type:'smoothstep' }"
      @edge-update="onEdgeUpdate"
      @connect="onConnect"
      v-model="elements"
      fit-view-on-init
      class="vue-flow-basic-example"
  >
    <Background :variant="BackgroundVariant.Lines" />

    <template #node-table="{ id, data, label }">
      <button class="table_button" @mousedown.stop @click="addRow({ id, data, label })">
        <img class="table_icon" src="./components/icons/plus.svg" alt="Add row">
      </button>

      <input class="table_input" v-if="data.editing" :value="label" @input="updateLabel(id, $event.target.value)" @blur="data.editing = false">
      <span class="table_input"  v-else @click="data.editing = true">{{ label }}</span>

      <button class ="table_button" @mousedown.stop  @click="deleteNode(id)">
        <img class="table_icon" src="./components/icons/cancel.svg" alt="Cancel">
      </button>
    </template>

    <template #node-row="{ id, data, label }">
      <input class="row_input" v-if="data.editing" :value="label" @input="updateLabel(id, $event.target.value)" @blur="data.editing = false">
      <span class="row_input" v-else @click="data.editing = true">{{ label }}</span>
      <button class="table_button" @mousedown.stop @click="data.showModal = !data.showModal">
        <img class="table_icon" src="./components/icons/key.svg" alt="Key">
      </button>

      <div class="modal" v-if="data.showModal">
        <select v-model="data.KeyMod" @change="updateKeyMod(id, data.KeyMod)">
          <option selected="selected" value="None">None</option>
          <option value="Primary">Primary</option>
          <option value="Unique">Unique</option>
          <option value="Index">Index</option>
        </select>
        <button class="modal_close_button" @click="data.showModal = false">Close</button>
      </div>

      <select v-model="data.selectedSqlType">
        <option value="">Select SQL Type</option>
        <option value="INT">INT</option>
        <option value="VARCHAR">VARCHAR</option>
        <option value="TEXT">TEXT</option>
        <option value="DATE">DATE</option>
      </select>
      <button class ="table_button" @mousedown.stop  @click="deleteNode(id)">
        <img class="table_icon" src="./components/icons/cancel.svg" alt="Cancel">
      </button>
      <Handle type="source" position="right" />
      <Handle type="target" position="left" />
    </template>

  </VueFlow>
</template>

<style scoped>

.vue-flow-basic-example {
  width: 100%;
  height: 100vh;
  background-color: #f0f0f0;
}
.table_button {
  width: 15%;
  height: 70%;
  padding: 0;
  border: none;
  background: none;
}
.table_icon {
  width: 70%;
  height: 70%;
}
.table_input{
  width: 80%;
}
input {
  padding: 0.5rem;
  font-size: 1rem;
  color: #000;
  background-color: #fff;
  border: none;
}
</style>