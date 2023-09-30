<script setup>
import { Position, VueFlow } from '@vue-flow/core'
import { ref, provide } from 'vue'
import ToolbarNode from './components/ToolbarNode.vue'

const defaultNodeStyle = {
  border: '1px solid #10b981',
  background: '#ef467e',
  color: 'white',
  borderRadius: '99px',
}

const elements = ref([
  {
    id: '1',
    type: 'toolbar',
    label: 'toolbar always open',
    data: { toolbarPosition: Position.Top, toolbarVisible: true },
    position: { x: 0, y: -100 },
    style: defaultNodeStyle,
  },
])
const defaultTableStyle = {
  border: '1px solid #10b981',
  background: '#ef467e',
  color: 'white',
  borderRadius: '5px',
}
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
    style: defaultTableStyle,
  })
}

provide('elements', elements)
provide('addTable', addTable)
</script>

<template>
  <button @click="addTable">Add Table</button>
  <VueFlow v-model="elements" fit-view-on-init class="vue-flow-basic-example">

    <template #node-toolbar="nodeProps">
      <ToolbarNode  :id="nodeProps.id" :data="nodeProps.data" :label="nodeProps.label" />
    </template>
  </VueFlow>
</template>
