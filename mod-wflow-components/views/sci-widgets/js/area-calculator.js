
// Area Calculator
// (c) 2006-2017 unix-world.org
// v.2017.10.02


//==================================================================
//==================================================================


var GeometryAreaCalculator = new function() { // START CLASS :: v.171002

	// :: static

	var _class = this; // self referencing


	var theNumAreas = 0;
	var theGrandTotal = 0;


	var updateGrandTotal = function(result) {
		theGrandTotal += parseFloat(result);
		theGrandTotal = Math.round(100 * theGrandTotal) / 100; // format with 2 decimals
		document.getElementById('result-total').innerHTML = '<h4>' + 'Total: ' + parseFloat(theGrandTotal) + '</h4>';
	} //END FUNCTION


	this.calculateTriangleAreaBH = function(elemIdResult, elemIdBase, elemIdHeight) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdBase));
		var fld2 = document.getElementById(String(elemIdHeight));
		//-- read values from form fields
		var base = fld1.value;
		var height = fld2.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('triangle-bs', {base:base, height:height});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	this.calculateTriangleAreaHeron = function(elemIdResult, elemIdS1, elemIdS2, elemIdS3) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdS1));
		var fld2 = document.getElementById(String(elemIdS2));
		var fld3 = document.getElementById(String(elemIdS3));
		//-- read values from form fields
		var s1 = fld1.value;
		var s2 = fld2.value;
		var s3 = fld3.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('triangle-heron', {s1:s1, s2:s2, s3:s3});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
			fld3.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	this.calculateRectangleOrSquareArea = function(elemIdResult, elemIdBase, elemIdHeight) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdBase));
		var fld2 = document.getElementById(String(elemIdHeight));
		//-- read values from form fields
		var base = fld1.value;
		var height = fld2.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('rectangle-or-square', {base:base, height:height});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	this.calculateRhombusOrSquareArea = function(elemIdResult, elemIdDiagonal1, elemIdDiagonal2) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdDiagonal1));
		var fld2 = document.getElementById(String(elemIdDiagonal2));
		//-- read values from form fields
		var d1 = fld1.value;
		var d2 = fld2.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('rhombus-or-square', {diagonal1:d1, diagonal2:d2});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	this.calculateParallelogramArea = function(elemIdResult, elemIdBase, elemIdHeight) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdBase));
		var fld2 = document.getElementById(String(elemIdHeight));
		//-- read values from form fields
		var base = fld1.value;
		var height = fld2.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('parallelogram', {base:base, height:height});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	this.calculateTrapezoidArea = function(elemIdResult, elemIdBase1, elemIdBase2, elemIdHeight) {
		//-- bind fields
		var fld1 = document.getElementById(String(elemIdBase1));
		var fld2 = document.getElementById(String(elemIdBase2));
		var fld3 = document.getElementById(String(elemIdHeight));
		//-- read values from form fields
		var base1 = fld1.value;
		var base2 = fld2.value;
		var height = fld3.value;
		//-- calculate
		var calc = _class.calculateGeometryArea('trapezoid', {base1:base1, base2:base2, height:height});
		//-- display
		if(calc.status == 'OK') {
			theNumAreas++;
			document.getElementById(String(elemIdResult)).innerHTML += '' + theNumAreas + '.&nbsp;' + String(calc.html);
			updateGrandTotal(calc.result);
			fld1.value = '';
			fld2.value = '';
			fld3.value = '';
		} else {
			alert('Failed: ' + calc.status);
		} //end if else
		//--
	} //END FUNCTION


	//=====


	this.calculateGeometryArea = function(figure, obj) {

		var objResult = {
			status: 'ERROR: No Data ...',
			result: 0,
			html: ''
		};

		switch(String(figure)) {
			case 'triangle-bs': // triangle area by base and height: obj.base ; obj.height
				var base = parseFloat(obj.base);
				var height = parseFloat(obj.height);
				var result = Math.round(100 * (height * base / 2)) / 100;
				var html = '';
				var metainfo = '[H='+height+' ; B='+base+']';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>TRIANGLE Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html =  '<div align="right"><font color="#003399"><b>Area of TRIANGLE:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			case 'triangle-heron': // triangle area by heron: obj.s1 ; obj.s2 ; obj.s3
				var s1 = parseFloat(obj.s1);
				var s2 = parseFloat(obj.s2);
				var s3 = parseFloat(obj.s3);
				var heron = ((s1 + s2 + s3) / 2);
				var result = Math.round(100 * Math.sqrt(heron * (heron - s1) * (heron - s2) * (heron - s3))) / 100;
				var html = '';
				var metainfo = '<span style="cursor:help;" title="Calculated Heron Semi-Perimeter *HS='+heron+'">' + '[S1='+s1+' ; S2='+s2+' ; S3='+s3+']' + '</span>';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>TRIANGLE (Heron) Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html = '<div align="right"><font color="#003399"><b>Area of TRIANGLE (Heron):</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			case 'rectangle-or-square': // rectangle/square area by base and height: obj.base ; obj.height
				var base = parseFloat(obj.base);
				var height = parseFloat(obj.height);
				var figType = 'RECTANGLE';
				if(base === height) {
					figType = 'SQUARE';
				} //end if
				var result = Math.round(100 * (height * base)) / 100;
				var html = '';
				var metainfo = '[H='+height+' ; B='+base+']';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>RECTANGLE/SQUARE Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html =  '<div align="right"><font color="#003399"><b>Area of '+figType+':</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			case 'rhombus-or-square': // rhombus or square area by the diagonals: obj.diagonal1 ; obj.diagonal2
				var diagonal1 = parseFloat(obj.diagonal1);
				var diagonal2 = parseFloat(obj.diagonal2);
				var figType = 'RHOMBUS';
				if(diagonal1 === diagonal2) {
					figType = 'SQUARE';
				} //end if
				var result = Math.round(100 * ((parseFloat(diagonal1) * parseFloat(diagonal2)) / 2)) / 100;
				var html = '';
				var metainfo = '[D1='+diagonal1+' ; D2='+diagonal2+']';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>RHOMBUS/SQUARE Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html =  '<div align="right"><font color="#003399"><b>Area of '+figType+':</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			case 'parallelogram': // parallelogram area by base and height: obj.base ; obj.height
				var base = parseFloat(obj.base);
				var height = parseFloat(obj.height);
				var result = Math.round(100 * (parseFloat(base) * parseFloat(height))) / 100;
				var html = '';
				var metainfo = '[H='+height+' ; B='+base+']';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>PARALLELOGRAM Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html =  '<div align="right"><font color="#003399"><b>Area of PARALLELOGRAM:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			case 'trapezoid': // trapezoid area by base1, base2 and height: obj.base1 ; obj.base2 ; obj.height
				var base1 = parseFloat(obj.base1);
				var base2 = parseFloat(obj.base2);
				var height = parseFloat(obj.height);
				var result = Math.round(100 * (((parseFloat(base1) + parseFloat(base2)) * parseFloat(height)) / 2)) / 100;
				var html = '';
				var metainfo = '[B1='+base1+' ; B2='+base2+' ; H='+height+']';
				if((result <= 0) || isNaN(result)) {
					result = 0;
					html = '<div align="right"><font color="#FF3300"><b>TRAPEZOID Area is Zero or Impossible:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font></div><hr>';
				} else {
					html = '<div align="right"><font color="#003399"><b>Area of TRAPEZOID:</b></font> <font color="#778899"><b><i>'+metainfo+'</i></b></font> = <font color="#FF6600"><b>'+result+'</b></font></div><hr>';
				} //end if else
				objResult.status = 'OK';
				objResult.result = result;
				objResult.html = String(html);
				break;
			default:
				objResult.status = 'ERROR: Invalid Geometry Figure !';
		} //end switch

		return objResult;

	} //END FUNCTION


} //END CLASS


//==================================================================
//==================================================================


// #END
