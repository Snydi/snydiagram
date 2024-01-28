import {Position} from "@vue-flow/core";

export function addTableS(elementsRef, TableStyle) {
    const elements = elementsRef.value;

    elementsRef.value = [
        ...elements,
        {
            id: Math.random().toString(),
            type: 'table',
            label: 'new table',
            data: {
                toolbarPosition: Position.Top,
                toolbarVisible: true
            },
            position: { x: 0, y: 0 },
            style: TableStyle,
        }
    ];
}
export function addRowS(elementsRef, nodeProps) {
    const elements = elementsRef.value;
    const RowStyle = {
        display: 'flex',
        border: '1px solid #10b981',
        borderColor: '#898989',
        background: '#ffffff',
        color: '#000000',
        borderRadius: '5px',
        width: '350px',
        height: '40px',
        alignItems: 'center',
        justifyContent: 'space-between',
    }
    const existingRows = elements.filter(el => el.parentNode === nodeProps.id);
    const position = nodeProps.data.position || { x: 0, y: 0 };

    elementsRef.value = [
        ...elements,
        {
            id: Date.now().toString(),
            type: 'row',
            label: 'New Row',
            position: { x: position.x, y: position.y + 40 + 40 * existingRows.length },
            style: RowStyle,
            draggable: false,
            parentNode: nodeProps.id,
            data: {
                editing: false,
                showModal: false,
                showOptionsModal: false,
                keyMod: 'None',
                sqlType: 'INT',
                nullable: false,
                unsigned: false,
            },
        },
    ];
}
export function updateConnectionLineTypeS(elementsRef, selectedEdgeRef, relationshipType) {
    const elements = elementsRef.value;
    const selectedEdge = selectedEdgeRef.value;

    const edgeIndex = elements.findIndex(el => el.id === selectedEdge.id);
    if (edgeIndex !== -1) {
        elements[edgeIndex].data.relationshipType = relationshipType;
        elements[edgeIndex].type = 'chickenFoot';
        if (relationshipType === 'one-to-one') {
            elements[edgeIndex].data.markerStart = 'none';
            elements[edgeIndex].data.markerEnd = 'none';
        } else if (relationshipType === 'one-to-many') {
            elements[edgeIndex].data.markerStart = 'none';
            elements[edgeIndex].data.markerEnd = 'url(#chickenFoot)';
        } else if (relationshipType === 'many-to-many') {
            elements[edgeIndex].data.markerStart = 'url(#chickenFoot)';
            elements[edgeIndex].data.markerEnd = 'url(#chickenFoot)';
        }
    }
}
export function deleteEdgeS(elementsRef, selectedEdgeRef) {
    const elements = elementsRef.value;
    const selectedEdge = selectedEdgeRef.value;

    elementsRef.value = elements.filter(el => el.id !== selectedEdge.id);
}

export function deleteNodeS(elementsRef, nodeId) {
    const elements = elementsRef.value;
    const nodeToDelete = elements.find(el => el.id === nodeId);

    if (nodeToDelete.type === 'table') {
        elementsRef.value = elements.filter(el => el.id !== nodeId && (el.type !== 'row' || el.parentNode !== nodeId));
    } else {
        elementsRef.value = elements.filter(el => el.id !== nodeId);
    }

    if (nodeToDelete.type === 'row') {
        const siblingRows = elements.filter(
            el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y);

        siblingRows.forEach((row, index) => {
            row.position.y = nodeToDelete.position.y + 40 * index;
        });
    }
}
