// Js Kanban CSS

var JS_Kanban = new function() { // START CLASS

	this.initBoard = function() {

		jQuery('.jskanban_sorter').sortable({
			connectWith: '.jskanban_connect_sorter',
			receive: function(event, ui) {
				jQuery(this).css({
					'background-color': '#FFCC00'
				});
				//console.log('[' + ui.item.attr('id') + '] moved in: [' + ui.item.parent().attr('id') + ']');
			}
		}).disableSelection();

	} //END FUNCTION

	this.addTask = function(area, txtNewItem) {

		if(txtNewItem != '') {
			jQuery(area).find('div.jskanban_holder').find('ul#todo')
				.append('<li id="" class="jskanban_box">' +
					jQuery('<div></div>').text(txtNewItem).html() + // fix html special chars (unixman)
					'</li>');
		} //end if

	} //END FUNCTION

	this.saveBoard = function(area, evcode) {

		var theJson = {};
		jQuery(area).find('div.jskanban_holder').find('ul').find('li').each(function(index, element) {
			var li = jQuery(element);
			var id = li.attr('id') ? li.attr('id') : '';
			var name = li.text() ? li.text() : 'N/A';
			var pid = li.parent().attr('id') ? li.parent().attr('id') : '';
			if(pid != '') {
				var arr = {
					id: id,
					name: name
				};
				if(!theJson.hasOwnProperty(pid)) {
					theJson[pid] = [];
				} //end if
				theJson[pid].push(arr);
				//console.log('Element [' + pid + ']: `' + name + '`' + ' @ ID: (' + id + ')');
			} //end if
		});
		if((typeof evcode === 'function') && (evcode != null)) {
			evcode(theJson);
		} //end if

	} //END FUNCTION

} //END CLASS

// END
