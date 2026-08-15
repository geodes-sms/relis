/* jshint esversion: 6 */
"use strict";

function phaseNode(phase, inactive = false) {

    let buttons = ``;
    if (inactive) {
        buttons = `
            <a title="Go to the phase" href="${baseUrl}/screening/select_screen_phase/${phase.screen_phase_id}">
                <button class="btn btn-info">
                    <i class="fa fa-send"></i> <i class="fa"></i> Go To
                </button>
            </a>
        `;
    }
    else if (phase.phase_state !== "Closed") {
        buttons = `
            <a title="Lock the phase" href="${baseUrl}/screening/screening_phase_manage/${phase.screen_phase_id}/2">
                <button class="btn btn-danger">
                    <i class="fa fa-lock"></i> <i class="fa"></i> Close
                </button>
            </a>
            <a title="Go to the phase" href="${baseUrl}/screening/select_screen_phase/${phase.screen_phase_id}">
                <button class="btn btn-info">
                    <i class="fa fa-send"></i> <i class="fa"></i> Go To
                </button>
            </a>
        `;
    } else {
        buttons = `
            <a title="Unlock the phase" href="${baseUrl}/screening/screening_phase_manage/${phase.screen_phase_id}">
                <button class="btn btn-success">
                    <i class="fa fa-unlock"></i> <i class="fa"></i> Open
                </button>
            </a>
    `;
    }

    return `
        <div class="node-card ${inactive ? `inactive` : ``}">
            <b>${phase.phase_title}</b>
            ${!inactive ? `
                <p>
                    Phase State : ${phase.phase_state}<br>
                    My Completion : ${phase.user_completion}<br>
                    Overall Completion : ${phase.gen_completion}
                </p>
            ` : ``}
            ${buttons}
        </div>
    `;
}

function phaseNodeEdition(id, title, displayedFields, used, inactive = false) {
    const fieldsCleaned = displayedFields.replaceAll('|', ', ');

    let buttonReplace = `
        <a title="Modify the phase" href="${baseUrl}/screen_phases/replace_phase/${id}">
            <button class="btn btn-info">
                <i class="fa fa-gear"></i> <i class="fa"></i> Replace
            </button>
        </a>
    `;

    let buttonModify = `
        <a title="Modify the phase" href="${baseUrl}/screen_phases/modify_phase/${id}">
            <button class="btn btn-info">
                <i class="fa fa-gear"></i> <i class="fa"></i> Modify
            </button>
        </a>
    `;

    let actionButton = "";
    if (!inactive) {
        actionButton = used ? buttonReplace : buttonModify;
    }

    return `
        <div class="node-card edit ${inactive ? `inactive` : ``}" data-id="${id}">
            <b>${title}</b>
            <p>Fields : ${fieldsCleaned}</p>
            ${actionButton}
        </div>
    `;
}

const calssificationNode = (classification) => `
    <div class="node-card">
        <b>Calssification</b>
        <p>
            State : ${classification.State}<br>
            My Completion : ${classification.User_completion}<br>
            Overall Completion : ${classification.Gen_completion}
        </p>
        ${classification.action}
    </div>  
`;

const qaNode = (qa) => `
    <div class="node-card">
        <b>Question & Answer</b>
        <p>
            State : ${qa.State}<br>
            My Completion : ${qa.User_completion}<br>
            Overall Completion : ${qa.Gen_completion}
        </p>
        ${qa.action}
    </div>  
`;

function formatDataToDisplay(phasesList, classification, qa, mode, showInactive = true) {
    const nodes = [];
    const edges = [];
    const inactivesPhases = [];
    const keys = Object.keys(phasesList);
    let qaId = -1;
    let classificationId = -1;

    if (qa) {
        qaId = (keys.length ? Math.max(...keys.map(Number)) : 0) + 1;
        classificationId = qaId + 1;
    } else {
        classificationId = (keys.length ? Math.max(...keys.map(Number)) : 0) + 1;
    }


    if (mode != 'edition') {
        if (qa) {
            nodes.push({
                "id": qaId,
                "content": qaNode(qa)
            });
        }
        nodes.push({
            "id": classificationId,
            "content": calssificationNode(classification)
        });
    }

    Object.entries(phasesList).forEach(([id, phase]) => {
       if (phase.screen_phase_active == 0) {
           inactivesPhases.push(id);
       }
    });

    Object.entries(phasesList).forEach(([id, phase]) => {

        if (inactivesPhases.includes(id) && !showInactive) {
            return;
        }

        nodes.push({
            "id" : id,
            "content" : mode == 'edition' ?
                phaseNodeEdition(id ,phase.phase_title, phase.displayed_fields, phase.used == 1, inactivesPhases.includes(id)) :
                phaseNode(phase, inactivesPhases.includes(id))
        });

        if (phase.next_phase != null) {
            edges.push({
                "from" : id,
                "to" : phase.next_phase,
                "dashed" : !!(inactivesPhases.includes(id) || inactivesPhases.includes(phase.next_phase))
            });
        } else if (mode != 'edition'){
            if (qa) {
                edges.push({
                    "from": id,
                    "to": qaId,
                    "dashed": false
                });
            }
            edges.push({
                "from" : qa ? qaId : id,
                "to" : classificationId,
                "dashed" : false
            });
        }
    });

    if (qa && Object.entries(phasesList).length == 0) {
        edges.push({
            "from" : qaId,
            "to" : classificationId,
            "dashed" : false
        });
    }

    if (showInactive) {
        inactivesPhases.forEach(phaseId => {
            const parents = JSON.parse(phasesList[phaseId].parent);
            parents.forEach(parent => {
                edges.push({
                    "from": parent,
                    "to": phaseId,
                    "dashed": true
                });
            });
        });
    }

    return {nodes, edges};
}

function createTree(phasesList, classification, qa, mode, showInactive = true) {
    //rajouter un parametre mode pour soit edition, soit using
    const graphData = formatDataToDisplay(phasesList, classification, qa, mode, showInactive);

    var g = new dagreD3.graphlib.Graph().setGraph({ rankdir: "TB" });

    graphData.nodes.forEach(node => {

        g.setNode(node.id, {
            labelType: "html",
            label: node.content,
            paddingX: 0, paddingY: 0
        });
    });

    graphData.edges.forEach(edge => {
        g.setEdge(edge.from, edge.to, {
            class: edge.dashed ? "dashed" : "",
            curve: d3.curveBasis
        });
    });

    return g;
}

function hasSiblings(phaseId, phaseList) {
    const depth_level = phaseList[phaseId].depth_level;

    const phasesOnDepthLevel = Object.values(phaseList).filter((phase) => {
        return phase.depth_level == depth_level && phase.screen_phase_active == 1;
    }).length;

    return phasesOnDepthLevel == 1 ? false : true;
}

const addSibling = (phaseId) => `
    <div class="contextmenu-button">
        <a href="${baseUrl}/screen_phases/add_phase/sibling/${phaseId}">
            <i class="fa fa-sitemap"></i> Split
        </a>
    </div>
`;

const addFirstPhase = () => `
    <div class="contextmenu-button">
        <a href="${baseUrl}/screen_phases/add_phase/first/-1">
            <i class="fa fa-plus-circle"></i> Add First Phase
        </a>
    </div>
`;

const addParent = (phaseId) => `
    <div class="contextmenu-button">
        <a href="${baseUrl}/screen_phases/add_phase/parent/${phaseId}">
            <i class="fa fa-arrow-up"></i> Add Previous Phase
        </a>
    </div>
`;

const addChild = (phaseId) => `
    <div class="contextmenu-button">
        <a href="${baseUrl}/screen_phases/add_phase/child/${phaseId}">
            <i class="fa fa-arrow-down"></i> Add Next Phase
        </a>
    </div>
`;

const deletePhase = (phaseId) => `
    <div class="contextmenu-button danger">
        <a href="${baseUrl}/screen_phases/delete_phase/${phaseId}">
            <i class="fa fa-trash"></i> Delete
        </a>
    </div>
`;

function generateContextMenuHTML(phaseId, phaseList) {
    const actualPhase = phaseList[phaseId];
    var contextMenu = ``;

    if (actualPhase.depth_level == 0 && actualPhase.next_phase != null) {
        // phase initiale
        contextMenu += addSibling(phaseId);

        if (actualPhase.has_pending == 0 && actualPhase.used == 0 && !hasSiblings(phaseId, phaseList)) {
            contextMenu += addParent(phaseId);
        }

        if (phaseList[actualPhase.next_phase].used == 0
            && phaseList[actualPhase.next_phase].has_pending == 0
            && !hasSiblings(actualPhase.next_phase, phaseList)) {
            contextMenu += addChild(phaseId);
        }

        contextMenu += deletePhase(phaseId);

    } else if (actualPhase.next_phase == null) {
        // phase finale
        if (actualPhase.has_pending == 0 && actualPhase.used == 0) {
            contextMenu += addChild(phaseId);
        }

        if (Object.keys(phaseList).length === 1) {
            contextMenu += addParent(phaseId);
        }
    } else {
        //phase intermédiaire
        if (phaseList[actualPhase.next_phase].used == 0 && phaseList[actualPhase.next_phase].has_pending == 0 && !hasSiblings(actualPhase.next_phase, phaseList)) {
            contextMenu += addChild(phaseId);
        }

        const numberOfParents = Object.values(phaseList).filter((phase) => {
            return phase.next_phase == phaseId && phase.screen_phase_active == 1;
        }).length;

        if (numberOfParents > 1) {
            contextMenu += addSibling(phaseId);
        }
        if (hasSiblings(phaseId, phaseList) || actualPhase.used == 0) {
            contextMenu += deletePhase(phaseId);
        }
    }

    return contextMenu;
}

function contextMenu(e, divContextMenu, phasesList) {

    if (e.target.closest('button') || e.target.closest('a')) {
        return;
    }

    document.querySelectorAll('.node-card.edit').forEach(c => c.style.boxShadow = '');

    const card = e.target.closest('.node-card.edit');

    if (card) {
        e.preventDefault();

        const phaseId = card.getAttribute('data-id');

        if (phasesList[phaseId].screen_phase_active == 0) {
            return;
        }

        const html = generateContextMenuHTML(phaseId, phasesList);

        card.style.boxShadow = 'inset 0 0 0 3px #007bff';

        if (html.trim() !== '') {
            divContextMenu.innerHTML = html;
            divContextMenu.style.top = `${e.clientY}px`;
            divContextMenu.style.left = `${e.clientX}px`;
            divContextMenu.style.display = 'block';
        } else {
            divContextMenu.style.display = 'none';
        }

    } else if (Object.keys(phasesList).length === 0) {
        e.preventDefault();

        divContextMenu.innerHTML = addFirstPhase();

        divContextMenu.style.top = `${e.clientY}px`;
        divContextMenu.style.left = `${e.clientX}px`;
        divContextMenu.style.display = 'block';
    } else {
        divContextMenu.style.display = 'none';
    }
}