
// NetVision JS - Ajax Suggest
// (c) 2006-2015 unix-world.org
// v.2015.02.15

// DEPENDS: jQuery

//==================

var AJX_Suggest_LoaderImg = 'modules/mod-js-components/views/js/jssuggest/img/ajax_loader.gif';

var AJX_Suggest_Class = new function() {

// :: static

var table_code_start = '<table cellpadding="0" cellspacing="0" id="AJX_suggest_container">';
var table_code_end = '</table>';

//Called from keyup on the search textbox :: Starts the AJAX request.
// y_method = GET / POST ; y_search_results = search.php?search=
this.search_Suggest = function(y_div, y_txt, y_method, y_search_results) {
	//--
	var the_divelem = $('#' + y_div);
	var the_txtfield = $('#' + y_txt);
	//--
	var url = '' + y_search_results + encodeURIComponent(the_txtfield.val());
	var method = 'GET';
	if(y_method !== 'GET') {
		method = 'POST';
	} //end if
	//--
	if(the_divelem.is(':visible')) {
		//--
		AJX_Suggest_Class.resetDIV(y_div);
		//--
	} else {
		//--
		the_divelem.show();
		the_divelem.css({'height':'auto'});
		the_divelem.html('<div class="AJX_suggest_load"><img src="' + AJX_Suggest_LoaderImg + '"></div>');
		//--
		$.ajax({
			async: true,
			cache: false,
			timeout: 0,
			type: method,
			url: url,
			data: '',
			dataType: 'json',
			success: function(answer) {
				//--
				var srq = '' + answer.search_value;
				var scl = parseInt(answer.search_cols);
				var str = answer.search_data; // array
				//--
				var el = 0;
				var suggest = table_code_start;
				//--
				for(var i=0; i<(str.length); i++) {
					//--
					suggest += '<tr valign="top" data-id="' + el + '" data-value="' + str[i].id + '" onclick="AJX_Suggest_Class.set_Search(\'' + y_div + '\', \'' + y_txt + '\', $(this).data(\'value\'), $(this).html());" title="' + (el + 1) + '">';
					//--
					suggest += '<td>';
					suggest += str[i].id;
					suggest += '</td>';
					//--
					for(var j=0; j<scl; j++) {
						suggest += '<td>';
						suggest += str[i]['c' + (j+1)];
						suggest += '</td>';
					} //end for
					//--
					suggest += '</tr>';
					//--
					el += 1;
					//--
				} //end for
				//--
				suggest += table_code_end;
				//-- clear the loading img
				AJX_Suggest_Class.resetDIV(y_div);
				the_divelem.show();
				the_divelem.css({'height':'auto'});
				//-- set div content
				if(el > 0) {
					the_divelem.html(suggest);
				} else {
					the_divelem.html(table_code_start + '<tr valign="top" onclick="AJX_Suggest_Class.resetDIV(\'' + y_div + '\');">' + '<td align="center" title="[No Matching Results]">(' + el + ')</td>' + '</tr>' + table_code_end);
				} //end if
				//-- cleanup
				el = 0;
				suggest = '';
				//--
			}, //END FUNCTION
			error: function(answer) {
				//--
				alert('ERROR (JS-Suggest): Invalid Server Response !', '' + answer.responseText);
				//--
				AJX_Suggest_Class.resetDIV(y_div);
				//--
			} //END FUNCTION
		});
		//--
	} //end if else
	//--
} //END FUNCTION

this.resetDIV = function(y_div) {
	//--
	var the_divelem = $('#' + y_div);
	var the_left = parseInt( -1 * parseInt(the_divelem.css('width')) / 4);
	//--
	the_divelem.html('');
	the_divelem.css({'height':'1px', 'left':the_left+'px'});
	var the_offset = parseInt(the_divelem.offset().left);
	if(the_offset < 0) { // constraint to avoid be outside page on left
		the_divelem.css({'height':'1px', 'left':the_left-the_offset+10+'px'});
	} //end if
	the_divelem.hide();
	//--
} //END FUNCTION

//Click function :: a function that sets the text of the search textbox when one of our suggested items are clicked.
// values are separed by :: in a cell and \n in rows
this.set_Search = function(y_div, y_txt, value, lineval) {
	//--
	var the_txtfield = $('#' + y_txt);
	var the_divelem = $('#' + y_div);
	//--
	value = '' + value; // force string
	//--
	the_divelem.html('');
	the_divelem.css({'height':'auto'});
	//--
	the_txtfield.val('' + value); // it must not apply htmlspecialchars because it comes from jQuery.data() and is set by jQuery.val()
	the_txtfield.dblclick(function() {
		$(this).val('');
	});
	//--
	the_divelem.html(table_code_start + '<tr valign="top" onclick="AJX_Suggest_Class.resetDIV(\'' + y_div + '\');">' + lineval + '</tr>' + table_code_end);
	//--
	setTimeout(function(){ AJX_Suggest_Class.resetDIV(y_div); }, 850);
	//--
} //END FUNCTION

// disable ENTER Key to submit form
this.disableEnterKey = function(e, y_div, y_txt, y_method, y_search_results) {
	//--
	var key;
	//--
	if(window.event) {
		key = window.event.keyCode; //IE
	} else {
		key = e.which; //firefox
	} //end if else
	//--
	if(key == 13) {
		AJX_Suggest_Class.search_Suggest(y_div, y_txt, y_method, y_search_results);
	} //end if
	//--
	return (key != 13);
	//--
} //END FUNCTION

} //END CLASS

//==================

// #END
