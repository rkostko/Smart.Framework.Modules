
// jsPlumb Editor
// (c) 2017 unix-world.org
// v.170719.r2

if(typeof flowchartIsReadonly == 'undefined') {
	var flowchartIsReadonly = true;
} //end if

if(typeof flowchartDataObj == 'undefined') {
	//alert('flowchartDataObj not found !!');
	console.error('flowchartDataObj not found !!');
}

jsPlumb.ready(function () {

	var instance = window.jsp = jsPlumb.getInstance({
		// default drag options
		DragOptions: { cursor: 'pointer', zIndex: 20000 },
		// the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
		// case it returns the 'labelText' member that we set on each connection in the 'init' method below.
		ConnectionOverlays: [
			[ "Arrow", {
				location: 1,
				visible: true,
				width:17,
				length:17,
				id:"ARROW"
			} ],
			[ "Diamond", {
				location: 0,
				visible: true,
				width:9,
				length:9,
				id:"ARRDOT"
			} ],
			[ "Label", {
				location: 0.5,
				id: "label",
				cssClass: "aLabel",
				events:{
					tap:function(obj) {
						if(flowchartIsReadonly !== true) {
							var label = prompt("Connection Label", obj.component.getOverlay("label").getLabel());
							if(label) {
								obj.component.getOverlay("label").setLabel(label);
							} //end if
						} //end if
					}
				}
			}]
		],
		Container: "canvas"
	});

	var basicType = {
		paintStyle: { stroke: "#216477", strokeWidth: 3 },
		hoverPaintStyle: { stroke: "#61B7CF" }
	};
	var connType = [ "Flowchart", { stub: [40, 60], gap: 7, cornerRadius: 5, stub: 25, alwaysRespectStubs: true } ];
	instance.registerConnectionType("basic", basicType);

	// this is the paint style for the connecting lines..
	var connectorPaintStyle = {
			strokeWidth: 3,
			stroke: "#61B7CF",
			joinstyle: "round",
		//	outlineStroke: "white",
		//	outlineWidth: 2
		},
	// .. and this is the hover style.
		connectorHoverStyle = {
			strokeWidth: 4,
			stroke: "#216477",
//			outlineWidth: 5,
//			outlineStroke: "white"
		},
		startpointHoverStyle = {
			fill: "#FF5500",
			stroke: "#FF5500"
		},
		endpointHoverStyle = {
			fill: "#7AB02C",
			stroke: "#7AB02C"
		},
	// the definition of source endpoints (the small blue ones)
		sourceEndpoint = {
			endpoint: "Dot",
			paintStyle: {
				strokeWidth: 1,
				stroke: "#FF5500",
			//	stroke: "transparent",
				fill: "transparent",
				radius: 7
			},
			isSource: true,
			connector: connType,
			connectorStyle: connectorPaintStyle,
			hoverPaintStyle: startpointHoverStyle,
			connectorHoverStyle: connectorHoverStyle,
			dragOptions: {},
			overlays: [
				[ "Label", {
					location: [0.5, 1.5],
					label: "Drag",
					cssClass: "endpointSourceLabel",
					visible: false
				} ]
			]
		},
	// the definition of target endpoints (will appear when the user drags a connection)
		targetEndpoint = {
			endpoint: "Dot",
			paintStyle: { strokeWidth: 1, stroke: "#7AB02C", fill: "transparent", radius: 7 },
		//	paintStyle: { fill: "transparent", radius: 7 },
			hoverPaintStyle: endpointHoverStyle,
			maxConnections: -1,
			dropOptions: { hoverClass: "hover", activeClass: "active" },
			isTarget: true,
			overlays: [
				[ "Label", { location: [0.5, -0.5], label: "Drop", cssClass: "endpointTargetLabel", visible:false } ]
			]
		},
		init = function (connection) {
			// nothing
		};

	var _AddNodeElement = function(theID, posX, posY, clsName, invertTargets, textLabel, iconFA) {
		var data_attr, ep1, ep2;
		if(invertTargets === true) {
			data_attr = 'inverted';
			ep1 = sourceEndpoint;
			ep2 = targetEndpoint;
		} else {
			data_attr = '';
			ep1 = targetEndpoint;
			ep2 = sourceEndpoint;
		} //end if else
		if(iconFA) {
			theIcon = '<span class="'+SmartJS_CoreUtils.escape_html(iconFA)+'"></span>';
		} else {
			iconFA = '';
			theIcon = '<span class=""></span>';
		} //end if else
		var Div = $('<div class="'+SmartJS_CoreUtils.escape_html(clsName)+'" id="'+SmartJS_CoreUtils.escape_html(theID)+'" data-conn-inv="'+SmartJS_CoreUtils.escape_html(data_attr)+'" data-fa-icon="'+SmartJS_CoreUtils.escape_html(iconFA)+'">'+theIcon+'<p>'+SmartJS_CoreUtils.escape_html(textLabel)+'</p></div>');
		Div.css({
	//		height: '100px',
	//		width: '100px',
			border: 'solid 1px',
			left: posX ? (posX + 'px') : '0px',
			top: posY ? (posY + 'px') : '0px'
		}).appendTo('#canvas');
		if(flowchartIsReadonly !== true) {
			instance.addEndpoint(Div, ep1, { anchor: "LeftMiddle", uuid: theID, editable: false, detachable: false, reattach: false });
			instance.addEndpoint(Div, ep1, { anchor: "RightMiddle", uuid: theID, editable: false, detachable: false, reattach: false });
			instance.addEndpoint(Div, ep2, { anchor: "TopCenter", uuid: theID, editable: false, detachable: false, reattach: false });
			instance.addEndpoint(Div, ep2, { anchor: "BottomCenter", uuid: theID, editable: false, detachable: false, reattach: false });
		} //end if
		if(flowchartIsReadonly !== true) {
			instance.draggable(Div);
		} //end if
		Div.addClass('window');
	}

	var _saveFlowChartAsJson = function () {

		var totalCount = 0;

		var nodes = [];

		$(".jtk-node").each(function (index, element) {
			totalCount++;
			var $element = $(element);
			var theIcon = $element.attr('data-fa-icon').toString();
			var isInverted = $element.attr('data-conn-inv').toString();
			if(isInverted == 'inverted') {
				isInverted = true;
			} else {
				isInverted = false;
			} //end if else
			nodes.push({
				elementId: $element.attr('id'),
				positionX: parseInt($element.css("left"), 10),
				positionY: parseInt($element.css("top"), 10),
				clsName: $element.attr('class').toString(),
				isInverted: isInverted,
				label: $element.text(),
				icon: theIcon
			});
		});

		var connections = [];
		$.each(instance.getConnections(), function (index, connection) {
			//console.log(connection);
			connections.push({
	//			connectionId: connection.id,
	//			sourceUUId: connection.endpoints[0].getUuid(),
	//			targetUUId: connection.endpoints[1].getUuid(),
				pageSourceId: connection.sourceId,
				pageTargetId: connection.targetId,
				sourceAnchor: connection.endpoints[0].anchor.type,
				targetAnchor: connection.endpoints[1].anchor.type,
				textLabel: connection.getOverlay("label").getLabel(),
			});
		});

		flowchartSave = {};
		flowchartSave.numberOfElements = totalCount;
		flowchartSave.nodes = nodes;
		flowchartSave.connections = connections;

		if(typeof flowchartSaveDataHelper == 'function') {
			flowchartSaveDataHelper(flowchartSave);
		} else {
			console.log(flowchartSave);
		} //end if

	}

	// suspend drawing and initialise.
	instance.batch(function () {

		// listen for new connections; initialise them the same way we initialise the connections at startup.
		instance.bind("connection", function (connInfo, originalEvent) {
			init(connInfo.connection); // comment this out to disable new connections
		});

		var node;
		for(var i=0; i<flowchartDataObj.nodes.length; i++) {
			node = flowchartDataObj.nodes[i];
			_AddNodeElement(node.elementId, node.positionX, node.positionY, node.clsName, node.isInverted, node.label, node.icon);
		} //end for
		node = null;

		var conn;
		for(var i=0; i<flowchartDataObj.connections.length; i++) {
			conn = flowchartDataObj.connections[i];
			var cx = instance.connect({
				source: conn.pageSourceId,
				target: conn.pageTargetId,
				anchors: [ conn.sourceAnchor, conn.targetAnchor ],
				paintStyle: connectorPaintStyle,
				hoverPaintStyle: connectorHoverStyle,
				connector: connType,
				endpoint: "Blank",
				editable: false, detachable: false, reattach: false
			});
			cx.getOverlay("label").setLabel("" + conn.textLabel);
		} //end for
		conn = null;

		// make all the window divs draggable
		//instance.draggable(jsPlumb.getSelector(".flowchart-demo .window"), { grid: [20, 20] });
		// THIS DEMO ONLY USES getSelector FOR CONVENIENCE. Use your library's appropriate selector
		// method, or document.querySelectorAll:
		//jsPlumb.draggable(document.querySelectorAll(".window"), { grid: [20, 20] });

		// listen for clicks on connections, and offer to delete connections on click.
		instance.bind("click", function (conn, originalEvent) {
			conn.toggleType("basic");
		});

		if(flowchartIsReadonly !== true) {

			instance.bind("dblclick", function (conn, originalEvent) {
				if(confirm("Delete connection from " + conn.sourceId + " to " + conn.targetId + "?")) {
					instance.deleteConnection(conn);
				}
			});

			instance.bind("connectionDrag", function (connection) {
				//console.log("connection " + connection.id + " is being dragged. suspendedElement is ", connection.suspendedElement, " of type ", connection.suspendedElementType);
			});

			instance.bind("connectionDragStop", function (connection) {
				//console.log("connection " + connection.id + " was dragged");
			});

			instance.bind("connectionMoved", function (params) {
				//console.log("connection " + params.connection.id + " was moved");
			});

		} //end if

	});

	if(flowchartIsReadonly !== true) {

		$('body').on('click', 'div.jtk-node > p', function(el) {
			var tgtP = $(this);
			var label = prompt("Box Label", tgtP.text());
			if(label) {
				tgtP.empty().text(label);
			} //end if
		});

		$('body').on('click', 'div.jtk-node > span', function(el) {
			var tgtP = $(this);
			var eClass = tgtP.attr('class') ? tgtP.attr('class') : 'fa fa-';
			var label = prompt("Box Icon", eClass);
			if(label) {
				tgtP.parent().attr('data-fa-icon', label);
				tgtP.attr('class', label);
			} //end if
		});

		$('body').on('dblclick', 'div.jtk-node', function(el) {
			if(confirm("Delete this element ?")) {
				var targetBoxId = $(this);
				instance.deleteConnectionsForElement(targetBoxId);
				instance.removeAllEndpoints(targetBoxId);
				//instance.detach(targetBoxId);
				targetBoxId.remove();
			}
		});

	} //end if

	function generateFlowChartUUID() {
		var date = new Date();
		var seconds = date.getTime();
		var milliseconds = date.getMilliseconds();
		var randNum = Math.random().toString(36);
		var uuID = SmartJS_CryptoHash.sha1('Flowchart UUID # ' + randNum + ' :: ' + seconds + '.' + milliseconds);
		return uuID;
	}

	if(flowchartIsReadonly !== true) {

		document.getElementById('flowchartNewBtn').onclick = function() {
			var uuID = generateFlowChartUUID();
			_AddNodeElement('flowchartElemProcess_'+uuID, 0, 0, 'window jtk-node', false, 'Process', '');
			return false;
		};

		document.getElementById('flowchartNewSBtn').onclick = function() {
			var uuID = generateFlowChartUUID();
			_AddNodeElement('flowchartElemTerminal_'+uuID, 0, 0, 'window jtk-node circle', false, 'Terminal', '');
			return false;
		};

		document.getElementById('flowchartNewCBtn').onclick = function() {
			var uuID = generateFlowChartUUID();
			_AddNodeElement('flowchartElemDisplay_'+uuID, 0, 0, 'window jtk-node oval', false, 'Display', '');
			return false;
		};

		document.getElementById('flowchartNewDBtn').onclick = function() {
			var uuID = generateFlowChartUUID();
			_AddNodeElement('flowchartElemDecision_'+uuID, 0, 0, 'window jtk-node diamond', true, 'Decision', '');
			return false;
		};

		document.getElementById('flowchartNewIOBtn').onclick = function() {
			var uuID = generateFlowChartUUID();
			_AddNodeElement('flowchartElemData_'+uuID, 0, 0, 'window jtk-node parallelogram', false, 'Data', '');
			return false;
		};

		document.getElementById('flowchartSaveBtn').onclick = function() {
			_saveFlowChartAsJson();
			return false;
		};

	} //end if

	jsPlumb.fire("jsPlumbDemoLoaded", instance);

});

// #END
