<script setup>
import { Handle, Position } from '@vue-flow/core'
import { NodeToolbar } from '@vue-flow/node-toolbar'
import { inject, ref } from 'vue'
const props = defineProps(['id', 'data', 'label'])

const elements = inject('elements')

const RowStyle = {
  border: '1px solid #10b981',
  background: '#ffffff',
  color: '#000000',
  borderRadius: '0px',
  width: '200px',
}

let lastYPosition = 0;
const addRow = () => {
  const position = props.data.position || { x: 0, y: lastYPosition } // use lastYPosition instead of 0
  elements.value = [...elements.value, {
    id: Date.now().toString(), // use a timestamp to generate a unique id
    type: 'row', // change this to your actual row node type
    label: 'New Row',
    position: { x: position.x, y: position.y + 50 }, // position the new node below the current node
    style: RowStyle,
    draggable: false, //disable dragging for rows
    parentNode: props.id, //set the parent of the node (have it dragged with parent
    sourcePosition: 'right', // source handle is on the right
    targetPosition: 'left', // target handle is on the left
  }]
  lastYPosition = position.y + 35; // Update lastYPosition each time a new node is added
}
</script>

<template>
  <NodeToolbar
      style="display: flex; gap: 0.5rem; align-items: center"
      :is-visible="props.data.toolbarVisible"
      :position="props.data.toolbarPosition"
  >
    <button @click="addRow(nodeProps)">Add row</button>


  </NodeToolbar>

  <div :style="{ padding: '10px 20px' }">
    {{ props.label }}
  </div>


</template>
