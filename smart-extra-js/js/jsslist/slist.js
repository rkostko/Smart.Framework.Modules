
// NetVision JS - Selectable List
// (c) 2006-2015 unix-world.org
// v.2015.02.15

//========================

var SelList_DIV_Class = new function() { // START CLASS

// :: static

this.form_Select = function(selName, srcList, selList, selDiv) {
	//--
	var x = document.getElementById(srcList);
	var y = document.getElementById(selList);
	var z = document.getElementById(selDiv);
	//--
	while(x.selectedIndex >= 0) {
		//--
		z.innerHTML = z.innerHTML + '<div style="padding:2px;"><table width="100%" cellpadding="1" cellspacing="0" border="1" bordercolor="#CCCCCC" bgcolor="#FFFFFF" style="border-style: solid; border-collapse: collapse;"><tr>' + '<input type="hidden" name="' +selName + '[]" value="' + x.options[x.selectedIndex].value + '">' + '<td style="font: 11px Tahoma,Arial;">' + x.options[x.selectedIndex].text + '</td>' + '</tr></table></div>';
		appendOptionLast(y, x.options[x.selectedIndex].text, x.options[x.selectedIndex].value);
		removeOptionSelected(x);
		//--
	} //end while
	//--
} //END FUNCTION

this.form_Reset = function(srcList, selList, selDiv) {
	//--
	var x = document.getElementById(srcList);
	var y = document.getElementById(selList);
	var z = document.getElementById(selDiv);
	var i;
	//--
	z.innerHTML = '';
	//--
	for(i=y.length-1; i>=0; i--) {
		appendOptionLast(x, y.options[i].text, y.options[i].value);
		y.remove(i);
	} //end for
	//--
} //END FUNCTION

var appendOptionLast = function(aList, vtext, vvalue) {
	//--
	var elOptNew = document.createElement('option');
	elOptNew.text = vtext;
	elOptNew.value = vvalue;
	//--
	try {
		aList.add(elOptNew, null); // standards compliant; doesn't work in IE
	} catch(ex) {
		aList.add(elOptNew); // IE only
	} //end try
	//--
} //END FUNCTION

var removeOptionSelected = function(aList) {
	//--
	aList.remove(aList.selectedIndex);
	//--
} //END FUNCTION

} //END CLASS

//========================

// #END
