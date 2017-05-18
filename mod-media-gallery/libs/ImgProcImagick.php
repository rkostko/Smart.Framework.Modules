<?php
// Class: \SmartModExtLib\MediaGallery\ImgProcImagick
// Media Gallery Process Plugin: Image Imagick Process :: for Smart.Framework
// Module Library
// v.3.5.1 r.2017.05.12 / smart.framework.v.3.5

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\MediaGallery;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//==================================================================================================
//================================================================================================== START
//==================================================================================================


/**
 * Class Smart Image Process IMagick
 *
 * @access 		private
 * @internal
 *
 * @version 	v.170518
 *
 */
final class ImgProcImagick { // [OK]

	// ::

//=========================================================================== [OK]
// create a preview from a big image {{{SYNC-IMGALLERY-PREVIEW}}}
public static function create_preview($y_exe_convert, $y_file, $y_newfile, $y_width, $y_height, $y_quality) {
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_exe_convert, 'no'); // on windows must use like on unix: / as path separator and without drive letter as: /path/to/exe
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_newfile);
	//--
	return (string) $y_exe_convert.' -strip -sampling-factor 4:2:0 -quality '.$y_quality.' -resize '.$y_width.'x'.$y_height.'^ "'.$y_file.'" -background white -gravity northwest -extent '.$y_width.'x'.$y_height.' "'.$y_newfile.'"'; // this will use the max available w / h
	//--
} //END FUNCTION
//===========================================================================


//=========================================================================== [OK]
// resize a big image
public static function create_resized($y_exe_convert, $y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve='yes') {
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_exe_convert, 'no'); // on windows must use like on unix: / as path separator and without drive letter as: /path/to/exe
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_newfile);
	//--
	if((string)$iflowerpreserve == 'yes') {
		$resize_flag = '\\>';
	} else {
		$resize_flag = '';
	} //end if else
	//--
	if($y_height > 0) { // resize by height
		$out = '-strip -sampling-factor 4:2:0 -quality '.$y_quality.' -resize x'.$y_height.$resize_flag.' "'.$y_file.'" "'.$y_newfile.'"';
	} else { // resize by width (default)
		$out = '-strip -sampling-factor 4:2:0 -quality '.$y_quality.' -resize '.$y_width.'x'.$resize_flag.' "'.$y_file.'" "'.$y_newfile.'"';
	} //end if else
	//--
	return (string) $y_exe_convert.' '.$out;
	//--
} //END FUNCTION
//===========================================================================


//===========================================================================
// apply a watermark to an image or to a preview
public static function apply_watermark($y_exe_composite, $y_file, $y_watermark_file, $y_quality, $y_watermark_gravity) {
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_exe_composite, 'no'); // on windows must use like on unix: / as path separator and without drive letter as: /path/to/exe
	//--
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	\SmartFileSysUtils::raise_error_if_unsafe_path($y_watermark_file);
	//--
	return (string) $y_exe_composite.' -dissolve 100 -gravity '.$y_watermark_gravity.' "'.$y_watermark_file.'" "'.$y_file.'" "'.$y_file.'"';
	//--
} //END FUNCTION
//===========================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//end of php code
?>