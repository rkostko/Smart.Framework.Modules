jsPlumb.ready(function () {

	var instance = window.jsp = jsPlumb.getInstance({
		Container: "canvas"
	});

	jsPlumb.fire("jsPlumbDemoLoaded", instance);

	//console.log('jsPlumbDemoLoaded');

});