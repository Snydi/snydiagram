<template>
    <Header
        :addTable="addTable"
        :openImportModal="openImportModal"
        :openExportModal="openExportModal"
        :saveDiagram="saveDiagram"
    >
    </Header>

    <VueFlow
        :default-edge-options="{ type:'chickenFoot' }"
        @edge-update="onEdgeUpdate"
        @edge-click="openRelationshipModal"
        @connect="onConnect"
        v-model="schema"
        fit-view-on-init
        :zoomOnDoubleClick=false
        :controlled=false
        class=".vue-flow"
    >
        <!--Chicken foot custom edge component -->
        <template #edge-chickenFoot="props">
            <ChickenFootEdge v-bind="props"></ChickenFootEdge>
        </template>

        <Background :variant="BackgroundVariant.Lines"/>
        <!-- Table -->
        <template #node-table="{ id, data, label }">
            <button class="table_button" @mousedown.stop @click="addRow({ id, data, label })">
                <img class="table_icon" src="../../icons/plus.svg" alt="Add row">
            </button>

            <input
                class="input input_designer_table"
                :value="label"
                @click="data.editing = true"
                @blur="() => { data.editing = false; updateLabel(id, label); }"
                @input="updateLabel(id, $event.target.value)"
                :readonly="!data.editing"
            />

            <button class="table_button" @mousedown.stop @click="deleteNode(id)">
                <img class="table_icon" src="../../icons/cancel.svg" alt="Cancel">
            </button>
        </template>
        <!-- Row -->
        <template #node-row="{ id, data, label }">
            <input
                class="input input_designer_row ml-5 mr-5"
                :value="label"
                @click="data.editing = true"
                @blur="() => { data.editing = false; updateLabel(id, label); }"
                @input="updateLabel(id, $event.target.value)"
                :readonly="!data.editing"
            />

            <!-- SQL Type -->
            <div>
                <select v-model="data.sqlType">
                    <option value="TINYINT(1)">TINYINT</option>
                    <option value="BIGINT">BIGINT</option>
                    <option value="CHAR(255)">CHAR</option>
                    <option value="VARCHAR(255)">VARCHAR</option>
                    <option value="TEXT">TEXT</option>
                    <option value="DATE">DATE</option>
                    <option value="DATETIME">DATETIME</option>
                    <option value="TIME">TIME</option>
                    <option value="TIMESTAMP">TIMESTAMP</option>
                    <option v-bind:value="data.sqlType">{{ data.sqlType }}</option>
                </select>
            </div>

            <!-- Options -->
            <button class="table_button" @mousedown.stop @click="toggleOptionsModal(id, $event)">
                <img class="table_icon" src="../../icons/dots.svg" alt="More options">
            </button>

            <!-- Options modal -->
            <div v-if="data.showOptionsModal" class="options_modal"
                 :style="{ left: `${data.modalPosition.x}px`, top: `${data.modalPosition.y}px` }">
                <select v-model="data.keyMod" @change="updateKeyMod(id, data.keyMod)">
                    <option selected="selected" value="None">None</option>
                    <option value="PRIMARY KEY">Primary</option>
                    <option value="UNIQUE">Unique</option>
                    <option value="INDEX">Index</option>
                </select>
                <p class="modal_text">Unsigned</p>
                <input type="checkbox" @mousedown.stop :checked="data.unsigned" @change="toggleUnsigned(id)">
                <p class="modal_text">Nullable</p>
                <input type="checkbox" @mousedown.stop :checked="data.nullable" @change="toggleNullable(id)">
            </div>

            <!-- Delete row -->
            <button class="table_button" @mousedown.stop @click="deleteNode(id)">
                <img class="table_icon" src="../../icons/cancel.svg" alt="Cancel">
            </button>

            <Handle type="source" position="right"/>
            <Handle type="source" position="left"/>
        </template>

    </VueFlow>
    <!--Relationship modal-->
    <div v-if="showRelationshipModal" class="relationship_modal"
         :style="{ left: `${modalPosition.x}px`, top: `${modalPosition.y}px` }">
        <button @click="updateConnectionLineType('one-to-one')">One to One</button>
        <button @click="updateConnectionLineType('one-to-many')">One to Many</button>
        <button @click="updateConnectionLineType('many-to-many')">Many to Many</button>
        <button @click="deleteEdge">Delete</button>
        <button @click="showRelationshipModal = false">Close</button>
    </div>
    <!--Import modal-->
    <div v-if="showImportModal" class="modal flex-centered">
        <div class="sql_modal_content">
            <textarea class="sql_textarea" v-model="importContent"></textarea>
            <button class="btn btn-primary" @click="importSql">Import</button>
            <button class="btn btn-primary" @click="showImportModal = false">Close</button>
        </div>
    </div>
    <!--Export modal-->
    <div v-if="showExportModal" class="modal sql_modal flex-centered">
        <div class="sql_modal_content ">
            <textarea class="sql_textarea" v-model="exportContent"></textarea>
            <button class="btn btn-primary" @click="exportSql">Export</button>
            <button class="btn btn-primary" @click="showExportModal = false">Close</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeMount } from 'vue'
import { Handle, Position, VueFlow, useVueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'

import { TableActions } from '../services/TableActions.js';
import { Diagram } from "../services/Diagram";

import Header from "../components/Header.vue";
import ChickenFootEdge from "../components/ChickenFootEdge.vue";

const { updateEdge, addEdges } = useVueFlow();

import { useRoute } from "vue-router";
import { useStore } from 'vuex';

const store = useStore();
store.dispatch('initializeAuth');
const route = useRoute();
const diagramId = route.params.id;

const modalPosition = ref({ x: 0, y: 0 });
const selectedEdge = ref(null);
const showRelationshipModal = ref(false);

const showImportModal = ref(false);
const importContent = ref('');

const showExportModal = ref(false);
const exportContent = ref('');

const schema = ref();

const TableStyle = {
    display: 'flex',
    border: '1px solid #10b981',
    background: '#ff6029',
    borderColor: '#ff6029',
    color: 'white',
    borderRadius: '5px',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between',
}

const addTable = () => {
    TableActions.addTable(schema, TableStyle, 'new_table');
}
const addRow = (nodeProps) => {
    TableActions.addRow(schema, nodeProps, {
        rowName: 'new_row',
        keyMod: 'None',
        sqlType: 'INT(11)',
        nullable: false,
        unsigned: false
    });
};
const deleteEdge = () => {
    TableActions.deleteEdge(schema, selectedEdge);
};
const deleteNode = (nodeId) => {
    TableActions.deleteNode(schema, nodeId);
};

function onConnect(params) {
    params.updatable = true;
    return addEdges([params])
}

function onEdgeUpdate({ edge, connection }) {
    return updateEdge(edge, connection)
}

const updateConnectionLineType = (relationshipType) => {
    TableActions.updateConnectionLineType(schema, selectedEdge, relationshipType);
    showRelationshipModal.value = false;
};

const updateLabel = (id, newLabel) => {
    const element = schema.value.find(el => el.id === id);
    if (element) {
        element.label = newLabel.replace(' ', '_');
    }
}
const updateKeyMod = (id, keyMod) => {
    const element = schema.value.find(el => el.id === id);
    if (element) {
        element.data.keyMod = keyMod;
    }
}
const toggleNullable = (id) => {
    const element = schema.value.find(el => el.id === id);
    if (element) {
        element.data.nullable = !element.data.nullable;
    }
}
const toggleUnsigned = (id) => {
    const element = schema.value.find(el => el.id === id);
    if (element) {
        element.data.unsigned = !element.data.unsigned;
    }
};
const toggleOptionsModal = (id) => {
    const row = schema.value.find(el => el.id === id);
    const offsetX = 350;

    const documentX = row.position.x;
    const documentY = row.position.y;

    const rowHeight = 60;
    const rowIndex = schema.value.find(el => el.id === id);
    const offsetY = rowIndex * (rowHeight - 20);

    row.data.modalPosition = { x: documentX + offsetX, y: documentY - offsetY };
    row.data.showOptionsModal = !row.data.showOptionsModal;
};
const openRelationshipModal = (params) => {
    selectedEdge.value = params.edge;
    const edgeElement = document.querySelector(`[id="${params.edge.id}"]`);
    const edgeRect = edgeElement.getBoundingClientRect();
    modalPosition.value = {
        x: edgeRect.left + window.scrollX + edgeRect.width / 2,
        y: edgeRect.top + window.scrollY + edgeRect.height / 2
    };
    showRelationshipModal.value = true;
};
const openImportModal = () => {
    showImportModal.value = true;
};
const importSql = () => {
    schema.value = Diagram.import(importContent.value);
};
const openExportModal = () => {
    showExportModal.value = true;
};
const exportSql = async () => {
    await Diagram.save(diagramId, schema.value);
    exportContent.value = await Diagram.export(diagramId)
}
const saveDiagram = () => {
    Diagram.save(diagramId, schema.value);
}
const getDiagram = async (diagramId) => {
    schema.value = await Diagram.get(diagramId);
    if (schema.value == null) {
        schema.value = [
            {
                id: '1',
                type: 'table',
                label: 'first_table',
                data: { toolbarPosition: Position.Top, toolbarVisible: true },
                position: { x: 0, y: -100 },
                style: TableStyle,
            },
        ]
    }
}
onBeforeMount(() => {
    getDiagram(diagramId);
})
onMounted(() => {
    setInterval(() => {
        Diagram.save(diagramId, schema.value)
    }, 60000);
})
</script>

<style scoped>
.sql_modal_content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    height: 500px;
    width: 700px;
}

.sql_textarea {
    width: 100%;
    height: 500px;
    margin-bottom: 10px;
}

.table_button {
    width: 15%;
    height: 80%;
    margin-top: 5px;
    padding: 0;
    border: none;
    background: none;
}

.table_icon {
    width: 70%;
    height: 70%;
    color: white;
}

.relationship_modal {
    position: absolute;
    width: 200px;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.relationship_modal button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #ff6029;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.relationship_modal button:hover {
    background-color: #ff6029;
}

select {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f9f9f9;
    color: #333;
    cursor: pointer;
}

select:hover {
    background-color: #f0f0f0;
}

.options_modal {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #ff6029;
    border-radius: 5px;
    width: 300px;
}

.modal_text {
    margin: 0;
    font-size: 15px;
}
</style>
