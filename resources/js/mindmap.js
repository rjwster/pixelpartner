import * as go from 'gojs';

import { floatingCubes, bubbles } from './theme-snippets';
import { Modal } from 'flowbite';

var mindmapDiagram;

function init() {
    const $ = go.GraphObject.make;

    mindmapDiagram = new go.Diagram("mindmap", {
        "commandHandler.copiesTree": true,
        "commandHandler.copiesParentKey": true,
        "commandHandler.deletesTree": true,
        "draggingTool.dragsTree": true,
        "undoManager.isEnabled": true
    });

    let color = 'white';
    if (window.theme == 'simple') {
        color = 'black';
    }

    if (window.theme == 'floating-cubes' || window.theme == 'bubbles') {
        const svg = document.createElement('div');

        if (window.theme == 'floating-cubes') {
            svg.innerHTML = floatingCubes;
        }

        if (window.theme == 'bubbles') {
            svg.innerHTML = bubbles;
        }

        mindmapDiagram.div.append(svg);
    }

    // when the document is modified, add a "*" to the title and enable the "Save" button
    mindmapDiagram.addDiagramListener("Modified", e => {
        var button = document.getElementById("SaveButton");
        if (button) button.disabled = !mindmapDiagram.isModified;
        var idx = document.title.indexOf("*");
        if (mindmapDiagram.isModified) {
            if (idx < 0) document.title += "*";
        } else {
            if (idx >= 0) document.title = document.title.slice(0, idx);
        }
    });

    // a node consists of some text with a line shape underneath
    mindmapDiagram.nodeTemplate =
        $(go.Node, "Horizontal",
            { selectionObjectName: "TEXT" },
            $(go.Panel, "Auto",
                $(go.Panel, "Auto",
                    $(go.Shape, "Rectangle",
                        {
                            opacity: 0,
                            width: 120,
                            height: 200,
                        }
                    ),
                    new go.Binding("visible", "", function(data) {
                        return !!data.data.source;
                    }).ofObject(),
                    $(go.Panel, "Auto",
                        $(go.Shape, "RoundedRectangle",
                            {
                                fill: 'white',
                                width: 120,
                                height: 170,
                                strokeWidth: 0,
                            }
                        ),
                        $(go.Picture,
                            {
                                name: "IMAGE",
                                desiredSize: new go.Size(100, 150),
                                margin: new go.Margin(10),
                                imageStretch: go.GraphObject.Uniform,
                            },
                            new go.Binding("source", "source")
                        ),
                    ),
                ),
                $(go.Panel, "Spot",
                    {
                        alignment: new go.Spot(0.5, 0, 0, -10),
                        alignmentFocus: go.Spot.TopCenter,
                        margin: new go.Margin(10, 0, 0, 0),
                    },
                    $(go.Shape, "Circle",
                        {
                            width: 40,
                            height: 40,
                            fill: (window.theme == 'simple') ? "#000" : $(go.Brush, go.Brush.Linear, { 0: "#6E96E8", 1: "#91FF6B" }),
                            stroke: null,
                            portId: "",
                            fromSpot: go.Spot.Right,
                            toSpot: go.Spot.Left
                        },
                    ),
                    $(go.Panel, "Spot",
                        new go.Picture("../storage/images/love.svg",
                        { width: 25, height: 25 })
                    ),
                ),
                new go.Binding("visible", "", function(data) {
                    return !!data.data.source;
                }).ofObject(),
            ),

            $(go.Panel, "Vertical",
                $(go.TextBlock,
                    {
                        name: "TEXT",
                        minSize: new go.Size(30, 15),
                        editable: true,
                        stroke: color,
                    },
                    new go.Binding("text", "text").makeTwoWay(),
                    new go.Binding("scale", "scale").makeTwoWay(),
                    new go.Binding("font", "font").makeTwoWay()),
                $(go.Shape, "LineH",
                    {
                        stretch: go.GraphObject.Horizontal,
                        strokeWidth: 3,
                        height: 3,
                        portId: "",
                        fromSpot: go.Spot.LeftRightSides,
                        toSpot: go.Spot.LeftRightSides,
                        stroke: color
                    },
                    new go.Binding("fromSpot", "dir", d => spotConverter(d, true)),
                    new go.Binding("toSpot", "dir", d => spotConverter(d, false))
                ),
            ),
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
            new go.Binding("locationSpot", "dir", d => spotConverter(d, false)),
        );

    // selected nodes show a button for adding children
    mindmapDiagram.nodeTemplate.selectionAdornmentTemplate =
        $(go.Adornment, "Spot",
            $(go.Panel, "Auto",
                // this Adornment has a rectangular blue Shape around the selected node
                $(go.Shape, { fill: null, stroke: "dodgerblue", strokeWidth: 3 }),
                $(go.Placeholder, { margin: new go.Margin(4, 4, 0, 4) })
            ),
        );

    // the context menu allows users to change the font size and weight,
    // and to perform a limited tree layout starting at that node
    mindmapDiagram.nodeTemplate.contextMenu =
        $("ContextMenu",
            $("ContextMenuButton",
                $(go.TextBlock, "Bigger"),
                { click: (e, obj) => changeTextSize(obj, 1.1) }),
            $("ContextMenuButton",
                $(go.TextBlock, "Smaller"),
                { click: (e, obj) => changeTextSize(obj, 1 / 1.1) }),
            $("ContextMenuButton",
                $(go.TextBlock, "Bold/Normal"),
                { click: (e, obj) => toggleTextWeight(obj) }),
            $("ContextMenuButton",
                $(go.TextBlock, "Copy"),
                { click: (e, obj) => e.diagram.commandHandler.copySelection() }),
            $("ContextMenuButton",
                $(go.TextBlock, "Delete"),
                { click: (e, obj) => e.diagram.commandHandler.deleteSelection() }),
            $("ContextMenuButton",
                $(go.TextBlock, "Undo"),
                { click: (e, obj) => e.diagram.commandHandler.undo() }),
            $("ContextMenuButton",
                $(go.TextBlock, "Redo"),
                { click: (e, obj) => e.diagram.commandHandler.redo() }),
            $("ContextMenuButton",
                $(go.TextBlock, "Layout"),
                {
                    click: (e, obj) => {
                        var adorn = obj.part;
                        adorn.diagram.startTransaction("Subtree Layout");
                        layoutTree(adorn.adornedPart);
                        adorn.diagram.commitTransaction("Subtree Layout");
                    }
                }
            )
        );

    // a link is just a Bezier-curved line of the same color as the node to which it is connected
    mindmapDiagram.linkTemplate =
        $(go.Link,
            {
                curve: go.Link.Bezier,
                fromShortLength: -2,
                toShortLength: -2,
                selectable: false
            },
            $(go.Shape,
                { strokeWidth: 3 },
                new go.Binding("stroke", "toNode", n => {
                    return color;
                }).ofObject())
        );

    // the Diagram's context menu just displays commands for general functionality
    mindmapDiagram.contextMenu =
        $("ContextMenu",
            $("ContextMenuButton",
                $(go.TextBlock, "Paste"),
                { click: (e, obj) => e.diagram.commandHandler.pasteSelection(e.diagram.toolManager.contextMenuTool.mouseDownPoint) },
                new go.Binding("visible", "", o => o.diagram && o.diagram.commandHandler.canPasteSelection(o.diagram.toolManager.contextMenuTool.mouseDownPoint)).ofObject()),
            $("ContextMenuButton",
                $(go.TextBlock, "Undo"),
                { click: (e, obj) => e.diagram.commandHandler.undo() },
                new go.Binding("visible", "", o => o.diagram && o.diagram.commandHandler.canUndo()).ofObject()),
            $("ContextMenuButton",
                $(go.TextBlock, "Redo"),
                { click: (e, obj) => e.diagram.commandHandler.redo() },
                new go.Binding("visible", "", o => o.diagram && o.diagram.commandHandler.canRedo()).ofObject()),
            $("ContextMenuButton",
                $(go.TextBlock, "Save"),
                { click: (e, obj) => save() }),
            $("ContextMenuButton",
                $(go.TextBlock, "Load"),
                { click: (e, obj) => load() })
        );

    mindmapDiagram.addDiagramListener("SelectionMoved", e => {
        var selectedNode = mindmapDiagram.selection.first();
        if (!selectedNode) {
            var selectedNode = mindmapDiagram.nodes.first();
        }

        var rootX = selectedNode.location.x;
        mindmapDiagram.selection.each(node => {
            if (node.data.parent !== 0) return; // Only consider nodes connected to the root
            var nodeX = node.location.x;
            if (rootX < nodeX && node.data.dir !== "right") {
                updateNodeDirection(node, "right");
            } else if (rootX > nodeX && node.data.dir !== "left") {
                updateNodeDirection(node, "left");
            }
            layoutTree(node);
        });

        updateNodes();
    });

    mindmapDiagram.addModelChangedListener(function (e) {
        if (e.isTransactionFinished) {
            var txn = e.object;
            if (txn && txn.changes && txn.changes.count > 0) {
                txn.changes.each(function (change) {
                    if (change.modelChange === "nodeDataArray" && change.change === go.ChangedEvent.Remove) {
                        updateNodes();
                        aideaTimer();
                    }
                });
            }
        }
    });

    mindmapDiagram.addDiagramListener('TextEdited', updateNodes);
    mindmapDiagram.addDiagramListener('TextEdited', aideaTimer);

    load();
}

function spotConverter(dir, from) {
    if (dir === "left") {
        return (from ? go.Spot.Left : go.Spot.Right);
    } else {
        return (from ? go.Spot.Right : go.Spot.Left);
    }
}

function changeTextSize(obj, factor) {
    var adorn = obj.part;
    adorn.diagram.startTransaction("Change Text Size");
    var node = adorn.adornedPart;
    var tb = node.findObject("TEXT");
    tb.scale *= factor;
    adorn.diagram.commitTransaction("Change Text Size");
}

function toggleTextWeight(obj) {
    var adorn = obj.part;
    adorn.diagram.startTransaction("Change Text Weight");
    var node = adorn.adornedPart;
    var tb = node.findObject("TEXT");
    // assume "bold" is at the start of the font specifier
    var idx = tb.font.indexOf("bold");
    if (idx < 0) {
        tb.font = "bold " + tb.font;
    } else {
        tb.font = tb.font.slice(idx + 5);
    }
    adorn.diagram.commitTransaction("Change Text Weight");
}

function updateNodeDirection(node, dir) {
    mindmapDiagram.model.setDataProperty(node.data, "dir", dir);
    // recursively update the direction of the child nodes
    var chl = node.findTreeChildrenNodes(); // gives us an iterator of the child nodes related to this particular node
    while (chl.next()) {
        updateNodeDirection(chl.value, dir);
    }
}

function addNodeAndLink(e, obj, inputText = "Typ hier je idee...") {
    var adorn = obj.part;
    var diagram = adorn.diagram;

    diagram.startTransaction("Add Node");

    if (e == null) {
        var selectedNode = mindmapDiagram.selection.first();
        if (selectedNode) {
            adorn = selectedNode;
        }
        var oldnode = adorn;
    } else {
        var oldnode = adorn.adornedPart;
    }

    var olddata = oldnode.data;
    // copy the brush and direction to the new node data
    var newdata = { text: inputText, brush: olddata.brush, textColor: olddata.textColor, dir: olddata.dir, parent: olddata.key };
    diagram.model.addNodeData(newdata);
    layoutTree(oldnode);
    diagram.commitTransaction("Add Node");

    // if the new node is off-screen, scroll the diagram to show the new node
    var newnode = diagram.findNodeForData(newdata);
    if (newnode !== null) diagram.scrollToRect(newnode.actualBounds);

    return newnode;
}

function layoutTree(node) {
    if (node.data.key === 0) {  // adding to the root?
        layoutAll();  // lay out everything
    } else {  // otherwise lay out only the subtree starting at this parent node
        var parts = node.findTreeParts();
        layoutAngle(parts, node.data.dir === "left" ? 180 : 0);
    }
}

function layoutAngle(parts, angle) {
    var layout = go.GraphObject.make(go.TreeLayout,
        {
            angle: angle,
            arrangement: go.TreeLayout.ArrangementFixedRoots,
            nodeSpacing: 5,
            layerSpacing: 20,
            setsPortSpot: false, // don't set port spots since we're managing them with our spotConverter function
            setsChildPortSpot: false
        });
    layout.doLayout(parts);
}

function layoutAll() {
    var root = mindmapDiagram.nodes.first();
    if (root === null) return;
    mindmapDiagram.startTransaction("Layout");
    // split the nodes and links into two collections
    var rightward = new go.Set(/*go.Part*/);
    var leftward = new go.Set(/*go.Part*/);
    root.findLinksConnected().each(link => {
        var child = link.toNode;
        if (child.data.dir === "left") {
            leftward.add(root);  // the root node is in both collections
            leftward.add(link);
            leftward.addAll(child.findTreeParts());
        } else {
            rightward.add(root);  // the root node is in both collections
            rightward.add(link);
            rightward.addAll(child.findTreeParts());
        }
    });
    // do one layout and then the other without moving the shared root node
    layoutAngle(rightward, 0);
    layoutAngle(leftward, 180);
    mindmapDiagram.commitTransaction("Layout");
}

// Show the diagram's model in JSON format
function save() {
    document.getElementById("savedMindmapJSON").value = mindmapDiagram.model.toJson();
    mindmapDiagram.isModified = false;
}
function load() {
    if (document.getElementById("savedMindmapJSON").value !== "") {
        mindmapDiagram.model = go.Model.fromJson(document.getElementById("savedMindmapJSON").value);
    }
}

// Timer
var timer;

// Functie om de timer te starten
function aideaTimer() {
    if (!window.aideaStatus) {
        return;
    }

    clearTimeout(timer);

    timer = setTimeout(function () {
        if (!window.aideaStatus) {
            return;
        }

        document.getElementById('add-ai-node').click();
        aideaTimer();
    }, parseInt(window.aideaSlider) * 1000); // 10 seconden
}

function getTimerValue() {
    const aideaStatus = document.getElementById('aidea-slider');

    window.aideaSlider = aideaStatus.value;
    document.getElementById('aidea-slider-value').textContent = aideaStatus.value + ' sec';

    aideaTimer();
}

function updateNodes() {
    if (!window.mindmapId) {
        return;
    }

    const data = mindmapDiagram.model.toJson();
    fetch('/mindmap/update-ideas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            mindmap_id: window.mindmapId,
            data: data,
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.log(data.error);
                return;
            }
        });
}

function fireModal(content) {
    const $targetEl = document.getElementById('swiper-modal');

    $targetEl.querySelector('#swiper-container').innerHTML = content;

    // options with default values
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: false
    };

    // instance options object
    const instanceOptions = {
        id: 'swiper-modal',
        override: true
    };

    window.activeModal = new Modal($targetEl, options, instanceOptions);

    window.activeModal.show();

    window.generateSwiper();
}

window.refreshMindmap = function () {
    if (!window.mindmapId) {
        console.log('No mindmap id found');
        return;
    }
    // get json from the database
    fetch('/mindmap/' + window.mindmapId + '/json')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.log(data.error);
                return;
            }

            mindmapDiagram.model = go.Model.fromJson(data);
        });
};

(function () {
    window.addEventListener('DOMContentLoaded', init);
    
    document.getElementById('theme-select').addEventListener('change', function () {
        window.theme = this.value;
        document.getElementById('mindmap').className = window.theme;

        mindmapDiagram.div = null;
        init();
    });

    document.getElementById("add-node").addEventListener("click", function () {
        if (!window.mindmapId) {
            return;
        }

        var selectedNode = mindmapDiagram.selection.first();
        if (!selectedNode) {
            selectedNode = mindmapDiagram.nodes.first();
        }

        var inputText = document.getElementById('node-text').value;
        if (inputText.length === 0) {
            inputText = "Typ hier je idee...";
        }

        const newNode = addNodeAndLink(null, mindmapDiagram.findNodeForKey(selectedNode.data.key), inputText);
        // save this to the database
        fetch(this.dataset.route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                mindmap_id: window.mindmapId,
                data: newNode.data,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.log(data.error);
                    return;
                }

                document.getElementById('node-text').value = '';

                mindmapDiagram.model.setDataProperty(newNode.data, 'key', data.key);
            });
    });

    document.getElementById('add-ai-node').addEventListener('click', function () {
        if (!window.mindmapId) {
            return;
        }

        var selectedNode = mindmapDiagram.selection.first();
        if (!selectedNode) {
            selectedNode = mindmapDiagram.nodes.first();
        }

        // start loading screen
        document.getElementById('cube-container').classList.remove('d-none');

        fetch(this.dataset.route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                mindmap_id: window.mindmapId,
                parent_id: selectedNode.data.key,
                text: selectedNode.data.text,
            })
        })
            .then(response => response.text())
            .then(data => {
                document.getElementById('cube-container').classList.add('d-none');

                if (data.error) {
                    console.log(data.error);
                    return;
                }

                fireModal(data);
            });
    });

    document.getElementById('remove-node').addEventListener('click', function () {
        if (!window.mindmapId) {
            return;
        }

        const selectedNode = mindmapDiagram.selection.first();
        if (!selectedNode) {
            return;
        }

        mindmapDiagram.commandHandler.deleteSelection();
        updateNodes();
    });

    document.getElementById('save').addEventListener('click', updateNodes);

    const aideaStatus = document.getElementById('aidea-slider');
    aideaStatus.disabled = true;

    window.aideaStatus = false;

    document.getElementById('aidea-status').addEventListener('click', function () {
        window.aideaStatus = this.checked;

        if (!window.aideaStatus) {
            aideaStatus.disabled = true;
        } else {
            aideaStatus.disabled = false;
        }

        getTimerValue();
    });

    aideaStatus.addEventListener('input', getTimerValue);

    window.mindmapId = document.getElementById('mindmapId').value;
    window.theme = document.getElementById('theme-select').value;
    document.getElementById('mindmap').className = window.theme;
})();
