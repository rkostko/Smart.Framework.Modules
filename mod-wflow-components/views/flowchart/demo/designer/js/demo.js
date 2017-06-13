(function() {
	'use strict';

	// Create chart view model
	var workflowViewModel = new WorkflowViewModel();

	/**
	* Initializes view model and apply bindings
	*/
	function initializeDemoData(demoData) {

		// Add Templates to chart
		if (demoData.templates) {
		    for (var i = 0; i < demoData.templates.length; i++) {
		        var template = demoData.templates[i],
                    templateViewModel = new TemplateViewModel(workflowViewModel, template.Id, template.name);

		        workflowViewModel.templates.push(templateViewModel);
		    }
		}

	    // Initialize chart view model
		workflowViewModel.initialize();

		// Bind view model to view
		ko.applyBindings(workflowViewModel);
	}

	initializeDemoData(demoData);

})();