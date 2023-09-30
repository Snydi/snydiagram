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
  border: '1px solid #10b981',
  background: '#ff251e',
  color: 'white',
  borderRadius: '5px',
  width: '200px',
  height: '40px',
}
const RowStyle = {
  border: '1px solid #10b981',
  background: '#ffffff',
  color: '#000000',
  borderRadius: '0px',
  width: '200px',
  height: '40px',
}
const elements = ref([
  {
    id: '1',
    type: 'toolbar',
    label: 'First table',
    data: { toolbarPosition: Position.Top, toolbarVisible: true },
    position: { x: 0, y: -100 },
    style: TableStyle,
  },
])

const addTable = () => {
  elements.value.push({
    id: Math.random().toString(),
    type: 'toolbar',
    label: 'New Table',
    data: {
      toolbarPosition: Position.Top,
      toolbarVisible: true
    },
    position: { x: 0, y: 0 },
    style: TableStyle,

  })
}

provide('elements', elements)
provide('addTable', addTable)



let lastYPosition = 0;
const addRow = (nodeProps) => {

  const existingRows = elements.value.filter(el => el.parentNode === nodeProps.id);
  const position = nodeProps.data.position || { x: 0, y: 0 }
  elements.value = [...elements.value, {
    id: Date.now().toString(), // use a timestamp to generate a unique id
    type: 'row', // change this to your actual row node type
    label: 'New Row',
    position: { x: position.x, y: position.y + 40 * (existingRows.length +1) }, // calculate y based on number of existing rows
    style: RowStyle,
    draggable: false, //disable dragging for rows
    parentNode: nodeProps.id, //set the parent of the node (have it dragged with parent
    sourcePosition: 'right', // source handle is on the right
    targetPosition: 'left', // target handle is on the left
  }]
}

const deleteNode = (nodeId) => {
  // Find the node to be deleted
  const nodeToDelete = elements.value.find(el => el.id === nodeId);

  // If the deleted node is a parent, delete all its child nodes
  if (nodeToDelete.type === 'toolbar') {
    elements.value = elements.value.filter(el => el.parentNode !== nodeId);
  }

  // Filter out the node to be deleted
  elements.value = elements.value.filter(el => el.id !== nodeId);

  // If the deleted node is a row, adjust the positions of other rows
  if (nodeToDelete.type === 'row') {
    const siblingRows = elements.value.filter(el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y);
    siblingRows.forEach((row, index) => {
      row.position.y = nodeToDelete.position.y + 40 * index;
    });
  }
};


</script>

<template>
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

    <template #node-toolbar="{ id, data, label }">
      <div style="display:flex; gap:.5rem; align-items:center;">
        <button  @mousedown.stop @click="addRow({ id, data, label })">Add row</button>
        {{ label }}
        <button @mousedown.stop style="color: red; margin-left: auto;" @click="deleteNode(id)">x</button>
      </div>
    </template>

    <template #node-row="{ id, data, label }">
      <div style="display:flex; gap:.5rem; align-items:center; ">
        <Handle type="target" position="left" style="background: #555;" />
        {{ label }}
        <Handle type="source" position="right" style="background: #555;" />
        <button @mousedown.stop style="color: red; margin-left: auto;" @click="deleteNode(id)">x</button>
      </div>
    </template>


  </VueFlow>
</template>