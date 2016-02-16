
// NetVision JS - JX Area Calculator
// (c) 2006-2015 unix-world.org
// v.2015.02.15

//-- inits
var JX_Area_Version = '2015-02-15';
var JX_Area_TOTAL = 0;
//--
var JX_Area_Calculator = function(selectObj, resultDiv, totalDIV) {
	//--
	// :: static
	//--
	var GeometryElement = document.getElementById(selectObj).value;
	var jx_field = document.getElementById(resultDiv);
	var jx_total = document.getElementById(totalDIV);
	var jx_result = 0;
	var jx_area = '';
	var jx_desc = '';
	var skip_calculate = 0;
	//--
	switch(GeometryElement) {
		case 'square':
			var length = prompt('LENGTH of one Side of the SQUARE:', '');
			jx_result = Math.round(100 * (parseFloat(length) * parseFloat(length))) / 100; // format with 4 decimals
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of SQUARE:</b></font> <font color="#778899"><b><i>[L='+length+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'rectangle':
			var width = prompt('WIDTH of the RECTANGLE:', '');
			var height = prompt('HEIGHT of the RECTANGLE:', '');
			jx_result = Math.round(100 * (parseFloat(width) * parseFloat(height))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of RECTANGLE:</b></font> <font color="#778899"><b><i>[W='+width+' ; H='+height+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'rhombus':
			var diagonal_1 = prompt('DIAGONAL #1 of the RHOMBUS:', '');
			var diagonal_2 = prompt('DIAGONAL #2 of the RHOMBUS:', '');
			jx_result = Math.round(100 * ((parseFloat(diagonal_1) * parseFloat(diagonal_2)) / 2)) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of RHOMBUS:</b></font> <font color="#778899"><b><i>[D1='+diagonal_1+' ; D2='+diagonal_2+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'parallelogram':
			var base = prompt('BASE of the PARALLELOGRAM:', '');
			var height = prompt('HEIGHT of the PARALLELOGRAM:', '');
			jx_result = Math.round(100 * (parseFloat(base) * parseFloat(height))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of PARALLELOGRAM:</b></font> <font color="#778899"><b><i>[B='+base+' ; H='+height+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'trapezoid':
			var base_1 = prompt("BASE #1 of the TRAPEZOID:", "");
			var base_2 = prompt("BASE #2 of the TRAPEZOID:", "");
			var height = prompt("HEIGHT of the TRAPEZOID:", "");
			jx_result = Math.round(100 * (((parseFloat(base_1) + parseFloat(base_2)) * parseFloat(height)) / 2)) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of TRAPEZOID:</b></font> <font color="#778899"><b><i>[B1='+base_1+' ; B2='+base_2+' ; H='+height+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'triangle':
			var height = prompt("HEIGHT of the TRIANGLE:", "");
			var base = prompt("BASE of the TRIANGLE:", "");
			jx_result = Math.round(100 * (parseFloat(height) * parseFloat(base) / 2)) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of TRIANGLE:</b></font> <font color="#778899"><b><i>[H='+height+' ; B='+base+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'triangle_heron':
			var side_1 = prompt('SIDE #1 of the TRIANGLE (Heron):', '');
			var side_2 = prompt('SIDE #2 of the TRIANGLE (Heron):', '');
			var side_3 = prompt('SIDE #3 of the TRIANGLE (Heron):', '');
			var heron = ((parseFloat(side_1) + parseFloat(side_2) + parseFloat(side_3)) / 2);
			jx_result = Math.round(100 * Math.sqrt(heron * (heron - parseFloat(side_1)) * (heron - parseFloat(side_2)) * (heron - parseFloat(side_3)))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of TRIANGLE (Heron):</b></font> <font color="#778899"><b><i>[L1='+side_1+' ; L2='+side_2+' ; L3='+side_3+' ; <span title="Calculated Value: Heron Semi-Perimeter">*HS='+heron+'</span>]</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'ellipse':
			var radius_1 = prompt("RADIUS #1 of the ELLIPSE:", "");
			var radius_2 = prompt("RADIUS #2 of the ELLIPSE:", "");
			jx_result = Math.round(100 * (Math.PI * parseFloat(radius_1) * parseFloat(radius_2))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of ELLIPSE:</b></font> <font color="#778899"><b><i>[R1='+radius_1+' ; R2='+radius_2+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'circle':
			var radius = prompt("RADIUS of the CIRCLE:", "");
			jx_result = Math.round(100 * (Math.PI * Math.pow(parseFloat(radius), 2))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of CIRCLE:</b></font> <font color="#778899"><b><i>[R='+radius+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'sphere':
			var radius = prompt("RADIUS of the SPHERE:", "");
			jx_result = Math.round(100 * (4 * Math.PI * (Math.pow(parseFloat(radius), 2)))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>Area of SPHERE:</b></font> <font color="#778899"><b><i>[R='+radius+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'rcilinder_lateral':
			var radius = prompt('RADIUS of the Right CILINDER:', '');
			var height = prompt('HEIGHT of the Right CILINDER:', '');
			jx_result = Math.round(100 * (2 * Math.PI * parseFloat(radius) * parseFloat(height))) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>LATERAL Area of Right CILINDER:</b></font> <font color="#778899"><b><i>[R='+radius+' ; H='+height+']</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'rcircle_cone_lateral':
			var radius = prompt('RADIUS of the Right/Circle CONE (from the Base Circle):', '');
			var height = prompt('HEIGHT of the Right/Circle CONE (perpendicular from Top to Base Circle center):', '');
			var slant =  Math.sqrt((Math.pow(parseFloat(radius), 2)) + (Math.pow(parseFloat(height), 2)));
			jx_result = Math.round(100 * (Math.PI * parseFloat(radius) * slant)) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>LATERAL Area of Right/Circle CONE:</b></font> <font color="#778899"><b><i>[R='+radius+' ; H='+height+' ; <span title="Calculated Value: Slant">*SL='+slant+'</span>]</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		case 'square_pyramide_lateral':
			var length = prompt('LENGTH of one Side of the Base Square of the PYRAMIDE:', '');
			var height = prompt('HEIGHT of the Rectangle PYRAMIDE (perpendicular from Top to Base Square center):', '');
			var slant =  Math.sqrt((Math.pow((parseFloat(length) / 2), 2)) + (Math.pow(parseFloat(height), 2)));
			jx_result = Math.round(100 * ((4 * parseFloat(length)) * slant / 2)) / 100;
			jx_area = '<div align="right" class="jx_active_div"><font color="#003399"><b>LATERAL Area of Square PYRAMIDE:</b></font> <font color="#778899"><b><i>[L='+length+' ; H='+height+' ; <span title="Calculated Value: Slant">*SL='+slant+'</span>]</i></b></font> = <font color="#FF6600"><b>'+jx_result+'</b></font></div>';
			break;
		default:
			jx_result = 0;
			jx_area = '';
			skip_calculate = 1;
	} //end switch
	//--
	if(skip_calculate != 1) {
		jx_desc = prompt('A Description of Area to Calculate', '');
	} //end if
	if(jx_desc != '') {
		//-- htmlspecialchars
		jx_desc = jx_desc.toString();
		jx_desc = jx_desc.replace(/&/g, '&amp;');
		jx_desc = jx_desc.replace(/</g, '&lt;');
		jx_desc = jx_desc.replace(/>/g, '&gt;');
		jx_desc = jx_desc.replace(/"/g, '&quot;');
		//--
		jx_desc = '<h2>'+jx_desc+'</h2>';
		//--
	} //end if
	//--
	jx_result = parseFloat(0 + jx_result);
	//--
	if(jx_result > 0) {
		//--
		JX_Area_TOTAL = Math.round(100 * (parseFloat(JX_Area_TOTAL) + jx_result)) / 100;
		//--
		jx_field.innerHTML += '' + jx_desc + jx_area + '<hr color="#778899" width="100%" size="1" align="center" noshade>';
		jx_total.innerHTML = '<div align="right" class="jx_active_div"><h2><font color="#33CC33"><b>TOTAL AREA:</b></font> <font color="#FF3300"><b>' + JX_Area_TOTAL + '</b></font></div><br><div align="left"><font size="1" color="#CCCCCC">v.' + JX_Area_Version + '</font></div>';
		//--
	} else {
		//--
		alert('Area is Zero or Impossible !');
		//--
	} //end if
	//--
	return false;
	//--
} //END FUNCTION
//--

// #END
