<?php
// Class: \SmartModExtLib\MediaGallery\Players
// Media Gallery Manager Plugin: Players :: for Smart.Framework
// Module Library
// v.3.7.5 r.2018.03.09 / smart.framework.v.3.7

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
 * @version 	v.170518
 * @package 	MediaGallery
 *
 * @access 		private
 * @internal
 *
 */
final class Players { // [OK]

	// ::

//===================================================================== [OK]
public static function videoPlayer($y_url, $y_title, $y_movie, $y_type, $y_width='720', $y_height='404', $y_autoplay=true) {

	//--
	if(((string)$y_type != 'ogv') AND ((string)$y_type != 'webm') AND ((string)$y_type != 'mp4')) { // {{{SYNC-MOVIE-TYPE}}}
		return (string) \SmartComponents::operation_notice('Invalid Media Type / Video: '.Smart::escape_html((string)$y_type), '725px');
	} //end if
	//--

	//--
	if((string)$y_url == '') {
		$y_url = (string) \SmartUtils::get_server_current_url();
	} //end if
	//--

	//--
	$the_title = (string) \Smart::escape_html((string)$y_title);
	//--
	$tmp_movie_id = 'smartframework_movie_player_'.sha1($player_movie);
	$player_movie = (string) $y_url.$y_movie;
	//--

	//--
	if((string)$y_type == 'webm') {
		$tmp_vtype = 'video/webm'; // 'video/webm; codecs="vp8.0, vorbis"'
	} elseif((string)$y_type == 'mp4') {
		$tmp_vtype = 'video/mp4';
	} else { // ogv
		$tmp_vtype = 'video/ogg'; // 'video/ogg; codecs="theora, vorbis"'
	} //end if else
	//--

	//--
	return (string) SmartMarkersTemplating::render_file_template(
		'modules/mod-media-gallery/players/html5/player.inc.htm',
		[
			'MOVIE-TITLE' 		=> (string) $the_title,
			'MOVIE-ID' 			=> (string) $tmp_movie_id,
			'MOVIE-URL' 		=> (string) $player_movie,
			'MOVIE-TYPE' 		=> (string) $tmp_vtype,
			'MOVIE-AUTOPLAY' 	=> (string) ($y_autoplay ? 'autoplay' : ''),
			'WIDTH' 			=> (string) $y_width,
			'HEIGHT' 			=> (string) $y_height
		]
	);
	//--

} //END FUNCTION
//=====================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//end of php code
?>