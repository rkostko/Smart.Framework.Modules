<?php
// Class: \SmartModExtLib\MediaGallery\Players
// Media Gallery Manager Plugin: Players :: for Smart.Framework
// Module Library
// v.3.1.2 r.2017.04.11 / smart.framework.v.3.1

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\MediaGallery;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// Media Players
// DEPENDS:
//	* Smart::
//	* SmartUtils::
//	* SmartComponents::
//======================================================


//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


/**
 * Provides the Media Gallery players (optional).
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @depends 	extensions: PHP GD Extension (w. TrueColor support) / imageMagick Utility (executable) ; classes: Smart, SmartUtils, SmartFileSystem
 * @version 	v.170418
 * @package 	MediaGallery
 *
 * @access 		private
 * @internal
 *
 */
final class Players { // [OK]

	// ::

//===================================================================== [OK]
public static function videoPlayer($y_url, $y_title, $y_movie, $y_type, $y_width='720', $y_height='404') {

//--
$player_title = (string) \Smart::escape_html((string)$y_title);
//--

//--
if((string)$y_url == '') {
	$y_url = \SmartUtils::get_server_current_url();
} //end if
//--
$player_movie = (string) $y_url.$y_movie;
//--
$tmp_movie_id = 'smartframework_movie_player_'.sha1($player_movie);
//--

//--
$tmp_div_width = $y_width + 5;
//--
$tmp_bgcolor = '#222222';
$tmp_color = '#FFFFFF';
//--

//--
if(((string)$y_type == 'ogv') OR ((string)$y_type == 'webm') OR ((string)$y_type == 'mp4')) { // {{{SYNC-MOVIE-TYPE}}}
//--
if((string)$y_type == 'webm') {
	$tmp_vtype = 'type="video/webm; codecs=&quot;vp8.0, vorbis&quot;"';
} else {
	$tmp_vtype = 'type="video/ogg; codecs=&quot;theora, vorbis&quot;"';
} //end if else
//--
$html = <<<HTML_CODE
<div align="center" style="padding-top:4px;">
<div style="z-index:1; background-color:{$tmp_bgcolor}; padding:2px; width:725px;">
<!-- start HTML5 Open-Media Player v.120415 -->
<video id="{$tmp_movie_id}" width="{$y_width}" height="{$y_height}" controls="controls" autoplay="autoplay">
	<source src="{$player_movie}" {$tmp_vtype}>
	WARNING: Your browser does not support the HTML5 Video Tag.
</video>
<br>
<h2 style="color:{$tmp_color}">{$player_title}</h2>
</div>
<!-- end HTML5 Open-Media Player -->
</div>
</div>
<br>
HTML_CODE;
} else {
	$html = (string) \SmartComponents::operation_notice('Invalid Media Type / Video: '.Smart::escape_html((string)$y_type), '725px');
} //end if else


//--
return (string) $html;
//--

} //END FUNCTION
//=====================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//end of php code
?>