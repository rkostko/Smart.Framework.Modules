jsPlumb.ready(function () {

var elementCount = 4;

/*
	var palette = jsPlumb.getInstance({
		// default drag options
		DragOptions: { cursor: 'pointer', zIndex: 2000 },
		// the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
		// case it returns the 'labelText' member that we set on each connection in the 'init' method below.
		ConnectionOverlays: [
			[ "Arrow", {
				location: 1,
				visible:true,
				width:11,
				length:11,
				id:"ARROW",
				events:{
//					click:function() { alert("you clicked on the arrow overlay")}
				}
			} ],
			[ "Label", {
				location: 0.15,
				id: "label",
				cssClass: "aLabel",
				events:{
//					tap:function(obj) { alert("You Clicked Label: " + ' # ' + obj.component.sourceId.substring(15) + '-' + obj.component.targetId.substring(15) + ' / ' + obj.labelText); }
				}
			}]
		],
		Container: "canvas1"
	});
*/

	var instance = window.jsp = jsPlumb.getInstance({
		// default drag options
		DragOptions: { cursor: 'pointer', zIndex: 2000 },
		// the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
		// case it returns the 'labelText' member that we set on each connection in the 'init' method below.
		ConnectionOverlays: [
			[ "Arrow", {
				location: 1,
				visible:true,
				width:11,
				length:11,
				id:"ARROW",
				events:{
					click:function() { alert("you clicked on the arrow overlay")}
				}
			} ],
			[ "Label", {
				location: 0.15,
				id: "label",
				cssClass: "aLabel",
				events:{
					tap:function(obj) { alert("You Clicked Label: " + ' # ' + obj.component.sourceId.substring(15) + '-' + obj.component.targetId.substring(15) + ' / ' + obj.labelText); }
				}
			}]
		],
		Container: "canvas"
	});

	var basicType = {
		connector: "StateMachine",
		paintStyle: { stroke: "red", strokeWidth: 4 },
		hoverPaintStyle: { stroke: "blue" },
		overlays: [
			"Arrow"
		]
	};
	instance.registerConnectionType("basic", basicType);

	// this is the paint style for the connecting lines..
	var connectorPaintStyle = {
			strokeWidth: 2,
			stroke: "#61B7CF",
			joinstyle: "round",
			outlineStroke: "white",
			outlineWidth: 2
		},
	// .. and this is the hover style.
		connectorHoverStyle = {
			strokeWidth: 3,
			stroke: "#216477",
			outlineWidth: 5,
			outlineStroke: "white"
		},
		endpointHoverStyle = {
			fill: "#216477",
			stroke: "#216477"
		},
	// the definition of source endpoints (the small blue ones)
		sourceEndpoint = {
			endpoint: "Dot",
			paintStyle: {
				stroke: "#7AB02C",
				fill: "transparent",
				radius: 7,
				strokeWidth: 1
			},
			isSource: true,
			connector: [ "Flowchart", { stub: [40, 60], gap: 10, cornerRadius: 5, alwaysRespectStubs: true } ],
			connectorStyle: connectorPaintStyle,
			hoverPaintStyle: endpointHoverStyle,
			connectorHoverStyle: connectorHoverStyle,
			dragOptions: {},
			overlays: [
				[ "Label", {
					location: [0.5, 1.5],
					label: "Drag",
					cssClass: "endpointSourceLabel",
					visible:false
				} ]
			]
		},
	// the definition of target endpoints (will appear when the user drags a connection)
		targetEndpoint = {
			endpoint: "Dot",
			paintStyle: { fill: "#7AB02C", radius: 7 },
			hoverPaintStyle: endpointHoverStyle,
			maxConnections: -1,
			dropOptions: { hoverClass: "hover", activeClass: "active" },
			isTarget: true,
			overlays: [
				[ "Label", { location: [0.5, -0.5], label: "Drop", cssClass: "endpointTargetLabel", visible:false } ]
			]
		},
		init = function (connection) {
			//console.log(connection);
			var connID = connection.sourceId.substring(15) + '-' + connection.targetId.substring(15);
			var theText = connID;
			switch(connID) {
				case '1-3':
					theText = 'Yes (Read-Only)';
					break;
				case '2-3':
				case '3-1':
				case '4-1':
					theText = 'Yes';
					break;
				case '3-2':
				case '2-4':
				case '4-4':
					theText = 'No';
					break;
			}
			connection.getOverlay("label").setLabel(theText);
		};

	var _addEndpoints = function(instObj, toId, sourceAnchors, targetAnchors) {
		for (var i = 0; i < sourceAnchors.length; i++) {
			var sourceUUID = toId + sourceAnchors[i];
			instObj.addEndpoint("flowchart" + toId, sourceEndpoint, {
				anchor: sourceAnchors[i], uuid: sourceUUID
			});
		}
		for (var j = 0; j < targetAnchors.length; j++) {
			var targetUUID = toId + targetAnchors[j];
			instObj.addEndpoint("flowchart" + toId, targetEndpoint, { anchor: targetAnchors[j], uuid: targetUUID });
		}
	};

/*
	palette.batch(function () {
		_addEndpoints(palette, "StartEv", ["LeftMiddle", "RightMiddle"], ["TopCenter", "BottomCenter"]);
	});
*/

	// suspend drawing and initialise.
	instance.batch(function () {

		_addEndpoints(instance, "Window1", ["LeftMiddle", "RightMiddle"], ["TopCenter", "BottomCenter"]);
		_addEndpoints(instance, "Window2", ["LeftMiddle", "BottomCenter"], ["TopCenter", "RightMiddle"]);
		_addEndpoints(instance, "Window3", ["RightMiddle", "BottomCenter"], ["LeftMiddle", "TopCenter"]);
		_addEndpoints(instance, "Window4", ["TopCenter", "BottomCenter"], ["LeftMiddle", "RightMiddle"]);

		// listen for new connections; initialise them the same way we initialise the connections at startup.
		instance.bind("connection", function (connInfo, originalEvent) {
			init(connInfo.connection); // comment this out to disable new connections
		});

		// make all the window divs draggable
		instance.draggable(jsPlumb.getSelector(".flowchart-demo .window"), { grid: [20, 20] });
		// THIS DEMO ONLY USES getSelector FOR CONVENIENCE. Use your library's appropriate selector
		// method, or document.querySelectorAll:
		//jsPlumb.draggable(document.querySelectorAll(".window"), { grid: [20, 20] });

		// connect a few up
		instance.connect({uuids: ["Window2BottomCenter", "Window3TopCenter"], editable: true});
		instance.connect({uuids: ["Window2LeftMiddle", "Window4LeftMiddle"], editable: true});
		instance.connect({uuids: ["Window4TopCenter", "Window4RightMiddle"], editable: true});
		instance.connect({uuids: ["Window3RightMiddle", "Window2RightMiddle"], editable: true});
		instance.connect({uuids: ["Window4BottomCenter", "Window1TopCenter"], editable: true});
		instance.connect({uuids: ["Window3BottomCenter", "Window1BottomCenter"], editable: true, detachable: true, reattach: true});
		instance.connect({uuids: ["Window1RightMiddle", "Window3LeftMiddle"], editable: false, detachable: false});
		//

		//
		// listen for clicks on connections, and offer to delete connections on click.
		//
		instance.bind("click", function (conn, originalEvent) {
			if(confirm("Delete connection from " + conn.sourceId + " to " + conn.targetId + "?")) {
				instance.detach(conn);
			}
			conn.toggleType("basic");
		});

		instance.bind("connectionDrag", function (connection) {
			console.log("connection " + connection.id + " is being dragged. suspendedElement is ", connection.suspendedElement, " of type ", connection.suspendedElementType);
		});

		instance.bind("connectionDragStop", function (connection) {
			console.log("connection " + connection.id + " was dragged");
		});

		instance.bind("connectionMoved", function (params) {
			console.log("connection " + params.connection.id + " was moved");
		});
	});

// Extend


_saveFlowchart = function () {
	var totalCount = 0;
	if (elementCount > 0) {
		var nodes = [];

		//check whether the diagram has a start element
//		var elm = $(".start.jtk-node");
//		if (elm.length == 0) {
//			alert("The flowchart diagram should have a start element");
//		} else {
			$(".jtk-node").each(function (index, element) {
				totalCount++;
				var $element = $(element);
				var type = $element.attr('class').toString().split(" ")[1];
				if (type == "step" || type == "diamond" || type == "parallelogram") {
					nodes.push({
						elementId: $element.attr('id'),
						nodeType: type,
						positionX: parseInt($element.css("left"), 10),
						positionY: parseInt($element.css("top"), 10),
						clsName: $element.attr('class').toString(),
						label: $element.text(),
						width: $element.outerWidth(),
						height: $element.outerHeight()
					});
				} else {
					nodes.push({
						elementId: $element.attr('id'),
						nodeType: $element.attr('class').toString().split(" ")[1],
						positionX: parseInt($element.css("left"), 10),
						positionY: parseInt($element.css("top"), 10),
						clsName: $element.attr('class').toString(),
						label: $element.text()
					});
				}
			});

			var connections = [];
			$.each(instance.getConnections(), function (index, connection) {
				//console.log(connection);
				connections.push({
					connectionId: connection.id,
					pageSourceId: connection.sourceId,
					pageTargetId: connection.targetId,
					sourceAnchor: connection.endpoints[0].anchor.type,
					targetAnchor: connection.endpoints[1].anchor.type,
//					sourceUUId: connection.endpoints[0].getUuid(),
//					targetUUId: connection.endpoints[1].getUuid(),
					textLabel: connection.getOverlay("label").getLabel(),
				});
			});

			var flowchart = {};
			flowchart.nodes = nodes;
			flowchart.connections = connections;
			flowchart.numberOfElements = totalCount;

			console.log(JSON.stringify(flowchart));

//		}

	}
}


function AddDiv() {
	elementCount++;
	var theID = 'Window' + elementCount;
//	var Div = $('<div class="window diamond jsplumb-connected-step" id="flowchartDescEv"><strong><p class="desc-text">decision</p></strong></div>');
	var Div = $('<div class="window jtk-node start" id="'+theID+'"><strong><p>start</p></strong></div>');
	Div.css({
		height: '100px',
		width: '100px',
		border: 'solid 1px'
	}).appendTo('#canvas');
	instance.addEndpoint($(Div), targetEndpoint, { anchor: "LeftMiddle", uuid: theID, editable: true, detachable: true, reattach: true });
	instance.addEndpoint($(Div), targetEndpoint, { anchor: "RightMiddle", uuid: theID, editable: true, detachable: true, reattach: true });
	instance.addEndpoint($(Div), sourceEndpoint, { anchor: "TopCenter", uuid: theID, editable: true, detachable: true, reattach: true });
	instance.addEndpoint($(Div), sourceEndpoint, { anchor: "BottomCenter", uuid: theID, editable: true, detachable: true, reattach: true });
	instance.draggable($(Div));
	$(Div).addClass('window');
}


document.getElementById('flowchartNewBtn').onclick = function() {
	AddDiv();
	return false;
};

document.getElementById('flowchartSaveBtn').onclick = function() {
	_saveFlowchart();
	return false;
};

	jsPlumb.fire("jsPlumbDemoLoaded", instance);


});