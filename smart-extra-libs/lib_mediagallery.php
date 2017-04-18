<?php
// [LIB - SmartFramework / Plugins / MediaGallery]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.3.1.2 r.2017.04.11 / smart.framework.v.3.1

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.1')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// Media Gallery - Display and Process Images and Videos
// DEPENDS:
//	* Smart::
//	* SmartUtils::
//	* SmartFileSystem::
// DEPENDS-EXT: PHP GD with TrueColor or ImageMagick
// REQUIRED CSS:
//	* mediagallery.css
//======================================================
// # Sample Configuration #
/*
//--------------------------------------- MEDIA GALLERY
define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER', 	'@gd'); 				// `@gd` | path to ImagMagick Convert (change to match your system) ; can be `/usr/bin/convert` or `/usr/local/bin/convert` or `c:/open_runtime/image_magick/convert.exe`
define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE', 	'@gd'); 				// `@gd` | path to ImagMagick Composite/Watermark (change to match your system) ; can be `/usr/bin/composite` or `/usr/local/bin/composite` or `c:/open_runtime/image_magick/composite.exe`
define('SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER', 	'/usr/bin/ffmpeg'); 	// path to FFMpeg (Video Thumbnailer to extract a preview Image from a movie) ; (change to match your system) ; can be `/usr/bin/ffmpeg` or `/usr/local/bin/ffmpeg` or `c:/open_runtime/ffmpeg/ffmpeg.exe`
define('SMART_FRAMEWORK_MEDIAGALLERY_PDF_EXTRACTOR', 	''); 					// path to PDF Extractor (Pdf2HtmlEx)
//--
*/
//======================================================

//--
if(!defined('SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER')) {
	define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER', '@gd'); // by default use the built-in PHP-GD
} //end if
if(!defined('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE')) {
	define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE', '@gd'); // by default use the built-in PHP-GD
} //end if
if(!defined('SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER')) {
	define('SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER', ''); // by default don't use
} //end if
if(!defined('SMART_FRAMEWORK_MEDIAGALLERY_PDF_EXTRACTOR')) {
	define('SMART_FRAMEWORK_MEDIAGALLERY_PDF_EXTRACTOR', ''); // by default don't use
} //end if
//--

//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


/**
 * Class: SmartMediaGalleryConverter - provides a Media Gallery converter.
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @depends 	extensions: PHP GD Extension (w. TrueColor support) ; executables: imageMagick Utility (can replace PHP GD), FFMpeg (for movies) ; classes: Smart, SmartUtils, SmartFileSystem
 * @version 	v.160809
 * @package 	Media:Gallery
 *
 */
final class SmartMediaGalleryConverter {

	// ::

//=====================================================================
// this is for the uploads of specific mediagallery content only (to replace the
public static function get_allowed_extensions_list() {
	return (string)SMART_FRAMEWORK_UPLOAD_PICTS.','.SMART_FRAMEWORK_UPLOAD_MOVIES; // <pdf>,<swf>
} //END FUNCTION
//=====================================================================


//=====================================================================
// sync with draw
public static function validate_extension($y_ext) {

	//--
	switch((string)$y_ext) {
		case 'png':
		case 'gif':
		case 'jpg':
		case 'jpeg':
			$out = 1;
			break;
		case 'webm': // open video vp8 / vp9
		case 'ogv': // open video theora
		case 'mp4':
		case 'flv':
		case 'mov':
			$out = 1;
			break;
		/* this is not yet tested
		case 'pdf': // docs
		case 'swf':
			$out = 1;
			break;
		*/
		default:
			$out = 0;
	} //end switch
	//--

	//--
	return $out;
	//--

} //END FUNCTION
//=====================================================================


//=====================================================================
// Resize or Create a Preview for an Image, and Apply a watermark if set
public static function img_process($y_mode, $iflowerpreserve, $y_file, $y_newfile, $y_quality, $y_width, $y_height, $y_watermark='', $y_waterlocate='center') {

	//--
	$y_file = (string) trim((string)$y_file);
	$y_newfile = (string) trim((string)$y_newfile);
	$y_watermark = (string) trim((string)$y_watermark);
	//--

	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_file)) {
		Smart::log_warning('SmartMediaGalleryConverter :: img_process // Unsafe Path: SRC='.$y_file);
		return '';
	} //end if
	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_newfile)) {
		Smart::log_warning('SmartMediaGalleryConverter :: img_process // Unsafe Path: DEST='.$y_newfile);
		return '';
	} //end if
	//--
	if((string)$y_file == (string)$y_newfile) {
		Smart::log_warning('SmartMediaGalleryConverter :: img_process // The Origin and Destination images are the same: SRC='.$y_file.' ; DEST='.$y_newfile);
		return '';
	} //end if
	//--
	if((string)$y_watermark != '') {
		if(!SmartFileSysUtils::check_file_or_dir_name($y_watermark)) {
			$y_watermark = '';
			Smart::log_warning('SmartMediaGalleryConverter :: img_process // Unsafe Path: WATERMARK='.$y_watermark);
		} //end if
	} //end if
	//--

	//--
	if((string)$iflowerpreserve != 'no') {
		$iflowerpreserve = 'yes';
	} //end if
	//--

	//--
	$y_quality = Smart::format_number_int($y_quality,'+');
	if($y_quality < 1) {
		$y_quality = 1;
	} //end if
	if($y_quality > 100) {
		$y_quality = 100;
	} //end if
	//--

	//--
	$y_width = Smart::format_number_int($y_width,'+');
	$y_height = Smart::format_number_int($y_height,'+');
	//--
	switch((string)$y_mode) {
		case 'preview':
			//--
			$y_mode = 'preview';
			//--
			if($y_width < 10) {
				$y_width = 10;
			} //end if
			if($y_width > 240) {
				$y_width = 240;
			} //end if
			//--
			if($y_height < 10) {
				$y_height = 10;
			} //end if
			if($y_height > 240) {
				$y_height = 240;
			} //end if
			//--
			break;
		case 'resize':
			//--
			$y_mode = 'resize';
			//--
			if($y_width < 320) {
				$y_width = 320;
			} //end if
			if($y_width > 2048) {
				$y_width = 2048;
			} //end if
			//--
			if($y_height !== 0) { // here can be zero to ignore height and resize by width keeping height proportion
				if($y_height < 320) {
					$y_height = 320;
				} //end if
				if($y_height > 2048) {
					$y_height = 2048;
				} //end if
			} //end if
			//--
			break;
		default:
			Smart::log_warning('SmartMediaGalleryConverter :: img_process INVALID MODE: '.$y_mode);
			return ''; // invalid mode
	} //end switch
	//--

	//-- {{{SYNC-GRAVITY}}}
	switch((string)$y_waterlocate) {
		case 'northwest':
			$y_waterlocate = 'northwest';
			break;
		case 'northeast':
			$y_waterlocate = 'northeast';
			break;
		case 'southwest':
			$y_waterlocate = 'southwest';
			break;
		case 'southeast':
			$y_waterlocate = 'southeast';
			break;
		case 'center':
		default:
			$y_waterlocate = 'center';
	} //end switch
	//--

	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
			'title' => '[INFO] :: MediaUTIL/Img/Process',
			'data' => "'".SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER."'".' :: '."'".SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE."'"
		]);
	} //end if
	//--

	//--
	$out = '';
	//--
	if((defined('SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER')) AND ((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER != '')) {
		//--
		$lock_file = $y_newfile.'.LOCK-IMG-MEDIAGALLERY';
		$lock_time = Smart::format_number_int(SmartFileSystem::read($lock_file),'+');
		//--
		if($lock_time > 0) {
			//--
			if(($lock_time + 30) < time()) { // allow max locktime of 30 seconds
				SmartFileSystem::delete($y_newfile); // delete img as it might be incomplete (it will be created again later)
				SmartFileSystem::delete($lock_file); // release the lock file
			} else {
				return '';
			} //end if
			//--
		} //end if
		//--
		if((is_file($y_file)) AND (!SmartFileSystem::file_or_link_exists($y_newfile)) AND (!SmartFileSystem::file_or_link_exists($lock_file))) {
			//--
			@chmod($y_file, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
			//--
			if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER == '@gd') OR (((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER)))) {
				//--
				$out .= '<table width="550" bgcolor="#FFCC00">';
				//--
				$out .= '<tr><td>Processing Image ['.strtoupper($y_mode).']:'.' '."'".Smart::escape_html(basename($y_file))."'".' -&gt; '."'".Smart::escape_html(basename($y_newfile))."'".'</td><tr>';
				//-- create a lock file
				SmartFileSystem::write($lock_file, time());
				//--
				$exitcode = 0;
				//--
				if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER))) {
					//-- generate preview by ImageMagick
					if((string)$y_mode == 'preview') {
						$exec = SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER.' '.SmartImagickImageProcess::create_preview($y_file, $y_newfile, $y_width, $y_height, $y_quality);
					} else {
						$exec = SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER.' '.SmartImagickImageProcess::create_resized($y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve);
					} //end if else
					@exec($exec, $arr_result, $exitcode);
					//--
					$out .= '<tr><td>[DONE]</td></tr>';
					if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
						SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
							'title' => '[INFO] :: MediaUTIL/Img/Process/ImageMagick',
							'data' => 'Runtime Result: '."'".$y_file."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.'] @ '.@print_r($arr_result,1)
						]);
					} //end if
					//--
				} else {
					//-- generate preview by @GD Library
					if((string)$y_mode == 'preview') {
						$exitcode = SmartGdImageProcess::create_preview($y_file, $y_newfile, $y_width, $y_height, $y_quality);
					} else {
						$exitcode = SmartGdImageProcess::create_resized($y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve);
					} //end if else
					//--
					$out .= '<tr><td>[*DONE*]</td></tr>';
					if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
						SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
							'title' => '[INFO] :: MediaUTIL/Img/Process/GD',
							'data' => 'Runtime Result: '."'".$y_file."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.']'
						]);
					} //end if
					//--
				} //end if else
				//--
				if($exitcode !== 0) {
					if(!is_file($y_newfile)) {
						Smart::log_notice('Media Gallery // SmartMediaGalleryConverter::img_process // Removing Invalid Image [Exitcode='.$exitcode.' / Converter='.SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER.']: '.$y_file);
						SmartFileSystem::delete($y_file); // remove invalid files as the failures will go into infinite loops
					} //end if
				} //end if
				//-- apply watermark
				if((defined('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE')) AND ((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE != '') AND (strlen($y_watermark) > 0)) {
					//--
					if((is_file($y_newfile)) AND (is_file($y_watermark))) {
						//--
						if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE))) {
							//--
							$exec = SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE.' '.SmartImagickImageProcess::apply_watermark($y_newfile, $y_watermark, $y_quality, $y_waterlocate);
							@exec($exec, $arr_result, $exitcode);
							//--
							$out .= '<tr><td><i>[WATERMARK]</i></td></tr>';
							if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
								SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
									'title' => '[INFO] :: MediaUTIL/Img/Process/Watermark/ImageMagick',
									'data' => 'Runtime Result: '."'".$y_watermark."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.'] @ '.@print_r($arr_result,1)
								]);
							} //end if
							//--
						} else {
							//--
							$exitcode = SmartGdImageProcess::apply_watermark($y_newfile, $y_watermark, $y_quality, $y_waterlocate);
							//--
							$out .= '<tr><td><i>[*WATERMARK*]</i></td></tr>';
							if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
								SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
									'title' => '[INFO] :: MediaUTIL/Img/Process/Watermark/GD',
									'data' => 'Runtime Result: '."'".$y_watermark."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.']'
								]);
							} //end if
							//--
						} //end if else
						//--
					} //end if
					//--
				} //end if
				//-- chmod
				if(is_file($y_newfile)) {
					@chmod($y_newfile, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
				} //end if
				//-- release the lock file
				SmartFileSystem::delete($lock_file);
				//--
				$out .= '</table>';
				//--
			} //end if
			//--
		} //end if
		//--
	} //end if
	//--

	//--
	return $out;
	//--

} //END FUNCTION
//=====================================================================


//=====================================================================
// Create a Preview for a movie
public static function mov_pw_process($y_mov_file, $y_mov_img_preview, $y_quality, $y_width, $y_height, $y_watermark='', $y_waterlocate='center', $y_mov_blank_img_preview='') {

	//--
	$y_mov_file = (string) trim((string)$y_mov_file);
	$y_mov_img_preview = (string) trim((string)$y_mov_img_preview);
	$y_mov_blank_img_preview = (string) trim((string)$y_mov_blank_img_preview);
	$y_watermark = (string) trim((string)$y_watermark);
	//--

	//--
	$blank_mov_pw = 'lib/core/plugins/img/mediagallery/video.jpg'; // this must be jpeg like the preview generated by ffmpeg
	//--
	$watermark_mov_pw = 'lib/core/plugins/img/mediagallery/play.png';
	//--

	//--
	if((string)$y_mov_blank_img_preview == '') {
		$y_mov_blank_img_preview = $blank_mov_pw;
	} //end if
	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_mov_blank_img_preview)) {
		$y_mov_blank_img_preview = $blank_mov_pw;
	} //end if
	//--
	if(!is_file($y_mov_blank_img_preview)) {
		Smart::log_warning('SmartMediaGalleryConverter :: mov_pw_process // Invalid Blank Preview Path: BLANK-PREVIEW='.$y_mov_blank_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_watermark == '') {
		$y_watermark = $watermark_mov_pw;
	} //end if
	//--

	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_mov_file)) {
		Smart::log_warning('SmartMediaGalleryConverter :: mov_pw_process // Unsafe Path: SRC='.$y_mov_file);
		return '';
	} //end if
	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_mov_img_preview)) {
		Smart::log_warning('SmartMediaGalleryConverter :: mov_pw_process // Unsafe Path: DEST='.$y_mov_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_mov_file == (string)$y_mov_img_preview) {
		Smart::log_warning('SmartMediaGalleryConverter :: mov_pw_process // The Origin movie and Destination image are the same: SRC='.$y_mov_file.' ; DEST='.$y_mov_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_watermark != '') {
		if(!SmartFileSysUtils::check_file_or_dir_name($y_watermark)) {
			$y_watermark = '';
			Smart::log_warning('SmartMediaGalleryConverter :: mov_pw_process // Unsafe Path: WATERMARK='.$y_watermark);
		} //end if
	} //end if
	//--

	//--
	$y_quality = Smart::format_number_int($y_quality,'+');
	if($y_quality < 1) {
		$y_quality = 1;
	} //end if
	if($y_quality > 100) {
		$y_quality = 100;
	} //end if
	//--

	//--
	$y_width = Smart::format_number_int($y_width,'+');
	$y_height = Smart::format_number_int($y_height,'+');
	//--
	if($y_width < 10) {
		$y_width = 10;
	} //end if
	if($y_width > 240) {
		$y_width = 240;
	} //end if
	//--
	if($y_height < 10) {
		$y_height = 10;
	} //end if
	if($y_height > 240) {
		$y_height = 240;
	} //end if
	//--

	//-- {{{SYNC-GRAVITY}}}
	switch((string)$y_waterlocate) {
		case 'northwest':
			$y_waterlocate = 'northwest';
			break;
		case 'northeast':
			$y_waterlocate = 'northeast';
			break;
		case 'southwest':
			$y_waterlocate = 'southwest';
			break;
		case 'southeast':
			$y_waterlocate = 'southeast';
			break;
		case 'center':
		default:
			$y_waterlocate = 'center';
	} //end switch
	//--

	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
			'title' => '[INFO] :: MediaUTIL/Mov/Process-Preview',
			'data' => "'".SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER."'".' :: '."'".SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE."'"
		]);
	} //end if
	//--

	//--
	$out = '';
	//--
	if((defined('SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER')) AND ((string)SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER != '')) {
		//--
		$lock_file = $y_mov_img_preview.'.LOCK-MOV-MEDIAGALLERY';
		$temporary_pw = $y_mov_img_preview.'.#tmp-preview#.jpg'; // {{{SYNC-MOV-TMP-PREVIEW}}}
		//--
		$lock_time = Smart::format_number_int(SmartFileSystem::read($lock_file),'+');
		//--
		if($lock_time > 0) {
			if(($lock_time + 45) < time()) { // allow max locktime of 45 seconds
				SmartFileSystem::delete($temporary_pw); // delete the old temporary if any
				SmartFileSystem::delete($lock_file); // release the lock file
			} //end if
		} //end if
		//--
		if((is_file($y_mov_file)) AND (!SmartFileSystem::file_or_link_exists($y_mov_img_preview)) AND (!SmartFileSystem::file_or_link_exists($lock_file))) {
			//--
			@chmod($y_mov_file, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
			//--
			$out .= '<table width="550" bgcolor="#74B83F">';
			$out .= '<tr><td>Processing Movie Preview:'.' '."'".Smart::escape_html(basename($y_mov_file))."'".' -&gt; '."'".Smart::escape_html(basename($y_mov_img_preview))."'".'</td></tr>';
			//-- create a lock file
			SmartFileSystem::write($lock_file, time());
			//-- generate preview (jpeg)
			if(is_executable(SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER)) { // generate a max preview of 240x240 which will be later converted below
				$exec = SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER.' -y -i '.'"'.$y_mov_file.'"'.' -s 240x240 -vframes 60 -f image2 -vcodec mjpeg -deinterlace '.'"'.$temporary_pw.'"';
				@exec($exec, $arr_result, $exitcode);
			} else {
				$arr_result = array('error' => 'IS NOT EXECUTABLE ...', 'movie-thumbnailer' => SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER);
				$exitcode = -1;
			} //end if
			//--
			$is_ok_pw = 1;
			if(!is_file($temporary_pw)) {
				$is_ok_pw = 0;
			} elseif(@filesize($temporary_pw) <= 1444) { // detect if blank jpeg of 240x240
				$is_ok_pw = 0;
			} //end if
			//--
			if($is_ok_pw != 1) {
				SmartFileSystem::delete($temporary_pw);
				SmartFileSystem::copy($y_mov_blank_img_preview, $temporary_pw); // in the case ffmpeg fails we avoid enter into a loop, or if ffmpeg is not found we use a blank preview
			} //end if
			//--
			$out .= '<tr><td>[DONE]</td></tr>';
			if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
				SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
					'title' => '[INFO] :: MediaUTIL/Mov/Process-Preview/FFMpeg',
					'data' => 'Runtime Result: '."'".$y_mov_file."'".' -> '."'".$y_mov_img_preview."'".' = ['.$exitcode.'] @ '.@print_r($arr_result,1)
				]);
			} //end if
			//-- process and apply watermark if any
			if(is_file($temporary_pw)) {
				//--
				@chmod($temporary_pw, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
				//--
				self::img_process('preview', 'no', $temporary_pw, $y_mov_img_preview, $y_quality, $y_width, $y_height, $y_watermark, $y_waterlocate);
				//--
				SmartFileSystem::delete($temporary_pw);
				//--
			} //end if
			//-- release the lock file
			SmartFileSystem::delete($lock_file);
			//--
			$out .= '</table>';
			//--
		} //end if
		//--
	} //end if
	//--

	//--
	return $out;
	//--

} //END FUNCTION
//=====================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


/**
 * Class: SmartMediaGalleryPlayers - provides the Media Gallery players (optional).
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @depends 	extensions: PHP GD Extension (w. TrueColor support) / imageMagick Utility (executable) ; classes: Smart, SmartUtils, SmartFileSystem
 * @version 	v.160809
 * @package 	MediaGallery
 *
 * @access 		private
 * @internal
 *
 */
final class SmartMediaGalleryPlayers {

	// ::

//=====================================================================
public static function movie_player($y_url, $y_title, $y_movie, $y_type, $y_width='720', $y_height='404') {

//--
$player_title = (string) Smart::escape_html($y_title);
//--

//--
if((string)$y_url == '') {
	$y_url = SmartUtils::get_server_current_url();
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
	$html = SmartComponents::operation_notice('Invalid Media Type / Video: '.Smart::escape_html((string)$y_type), '725px');
} //end if else


//--
return $html ;
//--

} //END FUNCTION
//=====================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


// ToDo:
// - finish Draw (gallery) public function
// use PDF2SWF: /usr/pkg/bin/pdf2swf -z -t -Q 120 file.pdf -o file.swf
// or: Pdf2HtmlEx


/**
 * Class: SmartMediaGalleryManager - provides the Media Gallery manager.
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @depends 	extensions: PHP GD Extension (w. TrueColor support) ; executables: imageMagick Utility (can replace PHP GD), FFMpeg (for movies) ; classes: Smart, SmartUtils, SmartFileSystem
 * @version 	v.160927
 * @package 	Media:Gallery
 *
 */
final class SmartMediaGalleryManager {

	// ->

//=====================================================================
//-- secure links (this is to make gallery possible from secure (protected access) folders)
public $use_secure_links;			// yes/no :: if yes will use secure (download bind to unique browser session that will expire after browser is closed and will not be accessible to other browsers ...)
public $secure_download_link;		// if secure links is turned on, then this parameter is mandatory to be set to an url that will handle the downloads like: script.php?page=controller.action-download
public $secure_download_ctrl_key;	// the page controller key (a secret non-public key that have to bind to a specific unique combination of controller context)
//-- styles
public $use_styles;					// yes/no :: use styles
//-- preview
public $preview_formvar;			// '' | the name of html form variable for checkbox
public $preview_formpict;			// '' | the html image to attach near checkbox
public $preview_description;		// if = 'no' will hide the description under previews
public $preview_watermark;			// '' | (relative path) 'path/to/watermark.gif|jpg|png' :: watermark for image previews
public $preview_place_watermark;	// '' (default is 'center') | 'northeast' | 'southwest' | 'southeast' | 'northwest'
//--
public $preview_width;				// width in pixels for creating previews
public $preview_height;				// height in pixels for creating previews (required for ffmpeg as WxH)
public $preview_quality;			// default preview quality
public $force_preview_w;			// force preview width to display in pixels
public $force_preview_h;			// force preview height to display in pixels
//-- images
public $img_width;					// '800' :: the image width (height will keep proportions to avoid distortion)
public $img_quality;				// '90' :: the preview quality
public $img_watermark;				// '' | (relative path) 'path/to/watermark.gif|jpg|png' :: watermark for images
public $img_place_watermark;		// '' (default is 'center') | 'northeast' | 'southwest' | 'southeast' | 'northwest'
//-- movies
public $mov_pw_watermark;			// '' | (relative path) 'path/to/watermark.gif|jpg|png' :: watermark for movie previews
public $mov_pw_blank;				// (relative path) 'path/to/moviepreview.gif|jpg|png' :: if FFMpeg is not available, this preview will be used
public $url_player_mov;				// movie player 'script.php?action&player_type={{{MOVIE-TYPE}}}&player_movie={{{MOVIE-FILE}}}&player_title={{{MOVIE-TITLE}}}' :: the URL to movie player
//-- extra
public $pict_reloading;				// path to reloading animated icon gif
public $pict_delete;				// path to delete icon
//-- counter for items [private]
public $gallery_show_counter;		// can be: full/yes/no
public $gallery_items;				// register the number of gallery items
//--
//=====================================================================


//=====================================================================
public function __construct($y_url_player_mov='') {
	//--
	$this->use_secure_links = 'no';
	$this->secure_download_link = '';
	$this->secure_download_ctrl_key = '';
	//--
	$this->use_styles = 'yes';
	//--
	$this->pict_reloading = 'lib/framework/img/busy_circle.gif';
	$this->pict_delete = 'lib/core/plugins/img/mediagallery/delete.png';
	//--
	$this->preview_formvar = '';
	$this->preview_formpict = '<img src="'.$this->pict_delete.'" alt="[x]" title="[x]">';
	$this->preview_description = 'yes';
	//--
	$this->preview_width = '160';
	$this->preview_height = '120';
	$this->preview_quality = '85';
	$this->force_preview_w = '';
	$this->force_preview_h = '';
	//--
	$this->img_width = '800';
	$this->img_quality = '90';
	$this->preview_watermark = '';
	$this->img_watermark = '';
	//--
	$this->mov_pw_watermark = 'lib/core/plugins/img/mediagallery/play.png';
	$this->mov_pw_blank = 'lib/core/plugins/img/mediagallery/video.jpg';
	$this->url_player_mov = (string) $y_url_player_mov;
	//--
	$this->gallery_show_counter = 'yes';
	//--
	$this->gallery_items = 0; // init (do not change !)
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * [PUBLIC] Draw an Image Gallery
 *
 * @param STRING $y_title									:: a title for the gallery
 * @param STRING $y_dir										:: path to scan
 * @param *OPTIONAL[yes/no] $y_process_previews_and_images	:: If = 'yes', will create previews for images and videos (movies) and will create conformed images
 * @param *OPTIONAL[yes/no] $y_remove_originals				:: If = 'yes', will remove original (images) after creating previews [$y_process_previews_and_images must be yes]
 * @param *OPTIONAL[>=0]	$y_display_limit				:: Items limit to display
 */
public function draw($y_title, $y_dir, $y_process_previews_and_images='no', $y_remove_originals='no', $y_display_limit='0') {

	//--
	$y_title = (string) $y_title;
	//--
	$y_dir = (string) $y_dir;
	//--
	$y_process_previews_and_images = (string) $y_process_previews_and_images;
	if((string)$y_process_previews_and_images != 'yes') {
		$y_process_previews_and_images = 'no';
	} //end if
	//--
	$y_display_limit = Smart::format_number_int($y_display_limit,'+');
	//--

	//--
	if((string)$this->use_secure_links == 'yes') {
		if(((string)$this->secure_download_link == '') OR ((string)$this->secure_download_ctrl_key == '')) {
			return '<h1>WARNING: Media Gallery / Secure Links Mode is turned ON but at least one of the: download link or the controller was NOT provided ...</h1>';
		} //end if
	} //end if
	//--

	//--
	if(!SmartFileSysUtils::check_file_or_dir_name($y_dir)) {
		return '<h1>ERROR: Invalid Folder for Media Gallery ...</h1>';
	} //end if
	//--
	$y_dir = SmartFileSysUtils::add_dir_last_slash($y_dir);
	SmartFileSysUtils::raise_error_if_unsafe_path($y_dir);
	//--
	if(!is_dir($y_dir)) {
		return '<h1>WARNING: The Folder for Media Gallery does not exists ...</h1>';
	} //end if
	//--

	//--
	$this->gallery_items = 0;
	//--

	//-- constraint of params
	if((string)$y_process_previews_and_images != 'yes') {
		$y_remove_originals = 'no';
	} //end if
	//--
	if(strlen($this->preview_formvar) > 0) { // avoid processing if it is displayed in a form
		$y_process_previews_and_images = 'no';
		$y_remove_originals = 'no';
	} //end if
	//--

	//-- some inits ...
	$out = '';
	$arr_files = array();
	$processed = 0;
	//--

	//--
	$arr_storage = (array) (new SmartGetFileSystem(true))->get_storage($y_dir, false, false);
	$all_mg_files = (array) $arr_storage['list-files'];
	//--

	//--
	for($i=0; $i<Smart::array_size($all_mg_files); $i++) {
		//--
		$file = (string) $all_mg_files[$i];
		$ext = strtolower(SmartFileSysUtils::get_file_extension_from_path($file));
		//--
		if((substr($file, 0, 1) != '.') AND (strpos($file, '.#') === false) AND (strpos($file, '#.') === false)) {
			//--
			if((is_file($y_dir.$file)) AND (((string)$ext == 'jpeg') OR ((string)$ext == 'jpg') OR ((string)$ext == 'gif') OR ((string)$ext == 'png'))) {
				//--
				if(SmartFileSysUtils::version_check($file, 'mg-preview')) {
					//-- it is an image preview file
					if(!is_file($y_dir.SmartFileSysUtils::version_add($file, 'mg-image'))) {
						SmartFileSystem::delete($y_dir.$file); // remove preview if orphan
					} //end if
					//--
				} elseif(SmartFileSysUtils::version_check($file, 'mg-image')) {
					//-- it is an image file
					if((string)$y_process_previews_and_images == 'yes') {
						//--
						$tmp_file = $y_dir.SmartFileSysUtils::version_add($file, 'mg-preview');
						//--
						if(!is_file($tmp_file)) {
							//--
							$out .= $this->img_preview_create($y_dir.$file, $tmp_file).'<br>';
							$processed += 1;
							//--
						} //end if
						//--
					} //end if
					//--
					$arr_files[] = $file;
					$this->gallery_items += 1;
					//--
				} elseif(SmartFileSysUtils::version_check($file, 'mg-vpreview')) {
					//-- it is a movie preview file
					if(stripos($file, '.#tmp-preview#.jpg') === false) {
						//--
						$tmp_linkback_file = SmartFileSysUtils::get_noext_file_name_from_path(SmartFileSysUtils::version_remove($file));
						//--
						if(!is_file($y_dir.$tmp_linkback_file)) {
							SmartFileSystem::delete($y_dir.$file); // remove if orphan
						} //end if
						//--
					} //end if
					//--
				} else { // unprocessed image
					//--
					if((string)$y_process_previews_and_images == 'yes') {
						//--
						$tmp_file = $y_dir.SmartFileSysUtils::version_add($file, 'mg-image');
						//--
						if(!is_file($tmp_file)) {
							//--
							if((string)$y_dir.$file != (string)$y_dir.strtolower($file)) {
								SmartFileSystem::rename($y_dir.$file, $y_dir.strtolower($file)); // make sure is lowercase, to be ok for back-check since versioned is lowercase
							} //end if
							//--
							$out .= $this->img_conform_create($y_dir.$file, $tmp_file).'<br>';
							$processed += 1;
							//--
						} else {
							//--
							if((string)$y_remove_originals == 'yes') {
								//--
								SmartFileSystem::delete($y_dir.$file);
								$out .= '<table width="550" bgcolor="#FF3300"><tr><td>removing original image: \''.Smart::escape_html($file).'\'</td></tr></table><br>';
								$processed += 1;
								//--
							} //end if
							//--
						} //end if else
						//--
					} //end if
					//--
				} //end if else
				//--
			} elseif((is_file($y_dir.$file)) AND (((string)$ext == 'webm') OR ((string)$ext == 'ogv') OR ((string)$ext == 'mp4') OR ((string)$ext == 'mov') OR ((string)$ext == 'flv'))) { // WEBM, OGV, MP4, MOV, FLV
				//-- process preview FLV / MOV ...
				if((string)$y_process_previews_and_images == 'yes') {
					//--
					$tmp_file = $y_dir.SmartFileSysUtils::version_add($file, 'mg-vpreview').'.jpg';
					//--
					if(!is_file($tmp_file)) {
						//--
						if((string)$y_dir.$file != (string)$y_dir.strtolower($file)) {
							SmartFileSystem::rename($y_dir.$file, $y_dir.strtolower($file)); // make sure is lowercase, to be ok for back-check since versioned is lowercase
						} //end if
						//--
						$out .= $this->mov_preview_create($y_dir.strtolower($file), $tmp_file).'<br>';
						$processed += 1;
						//--
					} //end if
					//--
				} //end if
				//--
				$arr_files[] = $file;
				$this->gallery_items += 1;
				//--
			} //end if else
			//--
		} //end if
		//--
	} //end for
	//--

	//--
	$out .= '<!-- START MEDIA GALLERY -->'."\n";
	//--
	if((string)$this->use_styles == 'yes') {
		$out .= '<div id="mediagallery_box">'."\n";
	} //end if
	//--

	//--
	$out_arr = array();
	//--
	if($processed <= 0) {
		//--
		$arr_files = Smart::array_sort($arr_files, 'natsort');
		//--
		$max_loops = Smart::array_size($arr_files);
		if($y_display_limit > 0) {
			if($y_display_limit < $max_loops) {
				$max_loops = $y_display_limit;
			} //end if
		} //end if
		//--
		for($i=0; $i<$max_loops; $i++) {
			//--
			$tmp_the_ext = strtolower(SmartFileSysUtils::get_file_extension_from_path($arr_files[$i])); // [OK]
			//--
			if(((string)$tmp_the_ext == 'webm') OR ((string)$tmp_the_ext == 'ogv') OR ((string)$tmp_the_ext == 'mp4') OR ((string)$tmp_the_ext == 'mov') OR ((string)$tmp_the_ext == 'flv')) {
				$out_arr[] = $this->mov_draw_box($y_dir, $arr_files[$i], $tmp_the_ext);
			} else {
				$out_arr[] = $this->img_draw_box($y_dir, $arr_files[$i]);
			} //end if
			//--
		} //end for
		//--
		$out .= '<div title="'.Smart::escape_html($this->gallery_show_counter).'">'."\n";
		//--
		if((string)$y_title != '') {
			$out .= '<div id="mediagallery_title">'.Smart::escape_html($y_title).'</div><br>';
		} //end if
		$out .= '<div id="mediagallery_row">';
		for($i=0; $i<Smart::array_size($out_arr); $i++) {
			$out .= '<div id="mediagallery_cell">';
			$out .= $out_arr[$i];
			$out .= '</div>'."\n";
		} //end for
		$out .= '</div>';
		//--
		$out .= '</div>'."\n";
		//--
	} //end if
	//--
	$out_arr = array();
	//--

	//--
	if((string)$this->use_styles == 'yes') {
		$out .= '</div>'."\n";
	} //end if
	//--
	$out .= '<!-- END MEDIA GALLERY -->'."\n";
	//--

	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE != 'yes') {
		if($processed > 0) {
			$out = '<img src="'.$this->pict_reloading.'" alt="[Reloading Page ...]" title="[Reloading Page ...]"><script type="text/javascript">setTimeout(function(){ self.location = self.location; }, 2000);</script>'.'<br><hr><br>'.$out;
			define('SMART_FRAMEWORK__MEDIA_GALLERY_IS_PROCESSING', $processed); // notice that the media galery is processing
		} //end if
	} //end if
	//--

	//--
	return $out;
	//--

} //END FUNCTION
//=====================================================================


//=====================================================================
private function standardize_title($y_file_name) {
	//--
	$y_file_name = SmartFileSysUtils::version_remove($y_file_name);
	$y_file_name = strtolower(SmartFileSysUtils::get_noext_file_name_from_path($y_file_name));
	//--
	return str_replace(array('_', '-', '  '), array(' ', ' ', ' '), (string)ucfirst((string)$y_file_name));
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * [PRIVATE] Draw a box for one Image Preview with link to Image, using SlimBox
 *
 * @param RELATIVEPATH $y_big_img_file
 * @return HTML Code
 */
private function img_draw_box($y_dir, $y_big_img_file) {

	//--
	$description = Smart::escape_html($this->standardize_title($y_big_img_file));
	//--
	$base_preview = SmartFileSysUtils::version_add($y_big_img_file, 'mg-preview'); // req. for deletion
	//--
	$image_preview = $y_dir.$base_preview;
	$image_big = $y_dir.$y_big_img_file;
	//--

	//--
	if((string)$this->use_secure_links == 'yes') { // OK
		$the_preview = (string) $this->secure_download_link.SmartUtils::create_download_link($image_preview, $this->secure_download_ctrl_key);
		$the_img = (string) $this->secure_download_link.SmartUtils::create_download_link($image_big, $this->secure_download_ctrl_key);
	} else {
		$the_preview = (string) $image_preview;
		$the_img = (string) $image_big;
	} //end if else
	//--

	//--
	if(strlen($this->force_preview_w) > 0) {
		$forced_dim = ' width="'.$this->force_preview_w.'"';
	} elseif(strlen($this->force_preview_h) > 0) {
		$forced_dim = ' height="'.$this->force_preview_h.'"';
	} else {
		$forced_dim = '';
	} //end if else
	//--

	//--
	$out = '';
	//--
	$out .= '<div align="center" id="mediagallery_box_item">';
	//--
	if((string)$this->preview_description == 'no') {
		$description = '';
	} //end if
	//--
	$out .= '<a rel="slimbox[media_gallery]" rel="nofollow" href="'.Smart::escape_html($the_img).'" target="_blank" '.'title="'.$description.'"'.'>';
	$out .= '<img src="'.Smart::escape_html($the_preview).'" border="0" alt="'.$description.'" title="'.$description.'"'.$forced_dim.'>';
	$out .= '</a>';
	//--
	if(strlen($this->preview_formvar) > 0) {
		$out .= '<input type="checkbox" name="'.$this->preview_formvar.'[]" value="'.Smart::escape_html($y_big_img_file.'|'.$base_preview).'" title="'.Smart::escape_html($y_big_img_file.'|'.$base_preview).'">'.$this->preview_formpict;
	} //end if
	//--
	if((string)$this->preview_description != 'no') {
		if(strlen($description) > 0) {
			$out .= '<div id="mediagallery_label">'.$description.'</div>';
		} //end if
	} //end if
	//--
	$out .= '</div>';
	//--

	//--
	return $out ;
	//--

} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * [PRIVATE] Create a Preview to Images, and Apply a watermark to Center
 *
 * @param STRING $y_file		Path to File
 * @param STRING $y_newfile		New File Name
 * @return STRING				Message
 */
private function img_preview_create($y_file, $y_newfile) {
	//--
	return SmartMediaGalleryConverter::img_process('preview', 'no', $y_file, $y_newfile, $this->preview_quality, $this->preview_width, $this->preview_height, $this->preview_watermark, $this->preview_place_watermark);
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * [PRIVATE] Create a Conformed Image, and Apply a watermark to Right-Bottom (SE) corner
 *
 * @param STRING $y_file		Path to File
 * @param STRING $y_newfile		New File Name
 * @return STRING				Message
 */
private function img_conform_create($y_file, $y_newfile) {
	//--
	return SmartMediaGalleryConverter::img_process('resize', 'yes', $y_file, $y_newfile, $this->img_quality, $this->img_width, 0, $this->img_watermark, $this->img_place_watermark);
	//--
} //END FUNCTION
//=====================================================================


//############### [PRIVATES] Movies


//=====================================================================
private function mov_draw_box($y_dir, $y_video_file, $y_type) {

	//--
	$description = Smart::escape_html($this->standardize_title($y_video_file));
	//--

	//--
	$base_preview = SmartFileSysUtils::version_add($y_video_file, 'mg-vpreview').'.jpg';
	$preview_file = $y_dir.$base_preview;
	$video_file = $y_dir.$y_video_file;
	//--

	//--
	if((string)$this->use_secure_links == 'yes') { // OK
		$the_preview = (string) $this->secure_download_link.SmartUtils::create_download_link($preview_file, $this->secure_download_ctrl_key);
		$the_video = (string) $this->secure_download_link.SmartUtils::create_download_link($video_file, $this->secure_download_ctrl_key);
	} else {
		$the_preview = (string) $preview_file;
		$the_video = (string) $video_file;
	} //end if else
	//--

	//--
	if(((string)$y_type == 'ogv') OR ((string)$y_type == 'webm') OR ((string)$y_type == 'mp4')) { // {{{SYNC-MOVIE-TYPE}}}
		$link = $this->url_player_mov.$the_video;
	} else { // mp4, mov, flv
		//if((string)self::get_server_current_protocol() == 'https://'){} // needs fix: the Flash player do not work with mixing http/https
		$link = $this->url_player_mov.$the_video;
	} //end if else
	//--
	$link = str_replace(array('{{{MOVIE-FILE}}}', '{{{MOVIE-TYPE}}}', '{{{MOVIE-TITLE}}}'), array(rawurlencode($the_video), rawurlencode($y_type), rawurlencode($description)), $link);
	//--

	//--
	$out = '';
	//--
	if(strlen($this->force_preview_w) > 0) {
		$forced_dim = ' width="'.$this->force_preview_w.'"';
	} elseif(strlen($this->force_preview_h) > 0) {
		$forced_dim = ' height="'.$this->force_preview_h.'"';
	} else {
		$forced_dim = '';
	} //end if else
	//--
	$out .= '<div align="center" id="mediagallery_box_item">';
	//--
	if((string)$this->preview_description == 'no') {
		$description = '';
	} //end if
	//--
	$out .= '<a data-smart="open.modal 780 475 1" rel="nofollow" href="'.$link.'" target="media-gallery-movie-player" '.'title="'.$description.'"'.'>';
	$out .= '<img src="'.Smart::escape_html($the_preview).'" border="0" alt="'.$description.'" title="'.$description.'"'.$forced_dim.'>';
	$out .= '</a>';
	//--
	if(strlen($this->preview_formvar) > 0) {
		$out .= '<input type="checkbox" name="'.$this->preview_formvar.'[]" value="'.Smart::escape_html($y_video_file.'|'.$base_preview).'" title="'.Smart::escape_html($y_video_file.'|'.$base_preview).'">'.$this->preview_formpict;
	} //end if
	//--
	if((string)$this->preview_description != 'no') {
		if(strlen($description) > 0) {
			$out .= '<div id="mediagallery_label">'.$description.'</div>';
		} //end if
	} //end if
	//--
	$out .= '</div>';
	//--

	//--
	return $out ;
	//--

} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * [PRIVATE] Create a Preview to Images, and Apply a watermark to Center
 *
 * @param STRING $y_file		Path to File
 * @param STRING $y_newfile		New File Name
 * @return STRING				Message
 */
private function mov_preview_create($y_mov_file, $y_mov_img_preview) {
	//--
	return SmartMediaGalleryConverter::mov_pw_process($y_mov_file, $y_mov_img_preview, $this->preview_quality, $this->preview_width, $this->preview_height, $this->mov_pw_watermark, 'center', $this->mov_pw_blank);
	//--
} //END FUNCTION
//=====================================================================


} //END CLASS

//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================



//==================================================================================================
//================================================================================================== START
//==================================================================================================


/**
 * Class Smart Image Process IMagick
 *
 * @access 		private
 * @internal
 *
 */
final class SmartImagickImageProcess {

	// ::
	// v.160809

//===========================================================================
// create a preview from a big image {{{SYNC-IMGALLERY-PREVIEW}}}
public static function create_preview($y_file, $y_newfile, $y_width, $y_height, $y_quality) {
	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	SmartFileSysUtils::raise_error_if_unsafe_path($y_newfile);
	//--
	return '-quality '.$y_quality.' -resize '.$y_width.'x'.$y_height.'^ "'.$y_file.'" -background white -gravity northwest -extent '.$y_width.'x'.$y_height.' "'.$y_newfile.'"'; // this will use the max available w / h
	//--
} //END FUNCTION
//===========================================================================


//===========================================================================
// resize a big image
public static function create_resized($y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve='yes') {
	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	SmartFileSysUtils::raise_error_if_unsafe_path($y_newfile);
	//--
	if((string)$iflowerpreserve == 'yes') {
		$resize_flag = '\\>';
	} else {
		$resize_flag = '';
	} //end if else
	//--
	if($y_height > 0) { // resize by height
		return '-quality '.$y_quality.' -resize x'.$y_height.$resize_flag.' "'.$y_file.'" "'.$y_newfile.'"';
	} else { // resize by width (default)
		return '-quality '.$y_quality.' -resize '.$y_width.'x'.$resize_flag.' "'.$y_file.'" "'.$y_newfile.'"';
	} //end if else
	//--
} //END FUNCTION
//===========================================================================


//===========================================================================
// apply a watermark to an image or to a preview
public static function apply_watermark($y_file, $y_watermark_file, $y_quality, $y_watermark_gravity) {
	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($y_file);
	SmartFileSysUtils::raise_error_if_unsafe_path($y_watermark_file);
	//--
	return '-dissolve 100 -gravity '.$y_watermark_gravity.' "'.$y_watermark_file.'" "'.$y_file.'" "'.$y_file.'"';
	//--
} //END FUNCTION
//===========================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================



//==================================================================================================
//================================================================================================== START
//==================================================================================================


/**
 * Class Smart Image Process GD
 *
 * @access 		private
 * @internal
 *
 */
final class SmartGdImageProcess {

	// ::
	// v.160915

//===========================================================================
private static function check_gd_truecolor() {
	//--
	if(!function_exists('imagecreatetruecolor')) {
		Smart::raise_error(
			'[ERROR] :: SmartGdImageProcess LIB :: PHP-GD extension (TrueColor) is required.',
			'A required component is missing ... See error log for more details'
		);
		die('Missing GD True Color');
	} //end if
	//--
} //END FUNCTION
//===========================================================================


//===========================================================================
// Create Preview Image [OK: returns 0 if OK or non-zero on errors]
public static function create_preview($imagePath, $newPath, $newW, $newH, $quality=100, $colorRGB = array(255, 255, 255)) {

	//-- check for required extension
	self::check_gd_truecolor();
	//--

	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($imagePath);
	SmartFileSysUtils::raise_error_if_unsafe_path($newPath);
	//--

	//--
	$imagePath = (string) $imagePath;
	$newPath = (string) $newPath;
	//--

	//--
	if(is_file($imagePath)) {

		//-- get image size
		$arr_imgsize = (array) @getimagesize($imagePath);
		$imgW = (int) $arr_imgsize[0];
		$imgH = (int) $arr_imgsize[1];
		$t_img = (int) $arr_imgsize[2]; // OK
		unset($arr_imgsize);
		//-- detect image type
		if($t_img <= 0) {
			Smart::log_notice('Media Gallery // SmartGdImageProcess // Preview :: Unknown Type: '.$imagePath);
			return 1; // not ok (unknown type)
		} //end if
		$t_img = (string) @image_type_to_mime_type((int)$t_img); // OK
		//-- reading image from source
		switch((string)$t_img) {
			case 'image/png':
			case 'image/x-png':
				$the_type = 'png';
				$source = @imagecreatefrompng($imagePath);
				break;
			case 'image/gif':
				$the_type = 'gif';
				$source = @imagecreatefromgif($imagePath);
				break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$the_type = 'jpg';
				$source = @imagecreatefromjpeg($imagePath);
				break;
			default:
				Smart::log_notice('Media Gallery // SmartGdImageProcess // Preview :: Unsupported Type (not PNG/GIF/JPEG ; Type='.$t_img.'): '.$imagePath);
				return 1; // not ok (invalid type)
		} //end switch
		//--
		if(!is_resource($source)) { // if the immage is corrupt or invalid ...
			Smart::log_warning('Media Gallery // SmartGdImageProcess // Preview :: Source Image Failure: '.$imagePath);
			return 2; // not ok (there was an error reading the image / have no privileges / or may be an invalid image type)
		} //end if
		//--

		//-- param fixes and constraints
		$newW = (int) $newW;
		if($newW < 10) {
			$newW = 10;
		} //end if
		if($newW > 2048) {
			$newW = 2048;
		} //end if
		//--
		$newH = (int) $newH;
		if($newH < 10) {
			$newH = 10;
		} //end if
		if($newH > 2048) {
			$newH = 2048;
		} //end if
		//--

		//-- picture scalling params {{{SYNC-IMGALLERY-PREVIEW}}}
		//if($imgW > $imgH) { // this will have a bounding box
		if($imgW < $imgH) { // this will use the max available w / h (new and modern previews)
			$scale = ($newW / $imgW);
		} else {
			$scale = ($newH / $imgH);
		} //end if else
		//-- new image dimensions
		$newImgW = ceil($imgW * $scale * 1.1);
		$newImgH = ceil($imgH * $scale * 1.1);
		//--

		//-- create the new image
		$imgnew = @imagecreatetruecolor($newW, $newH);
		//-- fill it with bg
		$background = @imagecolorallocate($imgnew, $colorRGB[0], $colorRGB[1], $colorRGB[2]);
		@imagefill($imgnew, 0, 0, $background);
		//-- copy image in the new created image
		if($imgW > $imgH) {
			@imagecopyresampled($imgnew, $source, 0, ceil(($newH - $newImgH) / 2), 0, 0, $newImgW, $newImgH, $imgW, $imgH);
		} else {
			@imagecopyresampled($imgnew, $source, ceil(($newW - $newImgW) / 2), 0, 0, 0, $newImgW, $newImgH, $imgW, $imgH);
		} //end if else
		//--

		//-- saving new image
		switch((string)$the_type) {
			case 'png':
				@imagepng($imgnew, $newPath);
				break;
			case 'gif':
				@imagegif($imgnew, $newPath);
				break;
			case 'jpg':
				@imagejpeg($imgnew, $newPath, $quality); // preserve 100% quality for jpeg
				break;
			default:
				// this should not happen, it is catched above
		} //end switch
		//--

		//--
		@imagedestroy($source);
		@imagedestroy($imgnew);
		//--

		//--
		return 0; // OK
		//--

	} //end if else
	//--

	//--
	return -1; // not ok, file does not exists / not a file / invalid path provided
	//--

} //END FUNCTION
//===========================================================================


//===========================================================================
// Create Resized Image [OK: returns 0 if OK or non-zero on errors]
public static function create_resized($imagePath, $newPath, $newW, $newH, $quality=100, $iflowerpreserve='yes', $colorRGB = array(255, 255, 255)) {

	//-- check for required extension
	self::check_gd_truecolor();
	//--

	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($imagePath);
	SmartFileSysUtils::raise_error_if_unsafe_path($newPath);
	//--

	//--
	$imagePath = (string) $imagePath;
	$newPath = (string) $newPath;
	//--

	//--
	if(is_file($imagePath)) {

		//--
		$arr_imgsize = (array) @getimagesize($imagePath);
		$imgW = (int) $arr_imgsize[0];
		$imgH = (int) $arr_imgsize[1];
		$t_img = (int) $arr_imgsize[2]; // OK
		unset($arr_imgsize);
		//--
		if($t_img <= 0) {
			Smart::log_notice('Media Gallery // SmartGdImageProcess // Resized :: Unknown Type: '.$imagePath);
			return 1; // not ok (unknown type)
		} //end if
		$t_img = (string) @image_type_to_mime_type((int)$t_img); // OK
		//-- reading image from source
		switch((string)$t_img) {
			case 'image/png':
			case 'image/x-png':
				$the_type = 'png';
				$source = @imagecreatefrompng($imagePath);
				break;
			case 'image/gif':
				$the_type = 'gif';
				$source = @imagecreatefromgif($imagePath);
				break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$the_type = 'jpg';
				$source = @imagecreatefromjpeg($imagePath);
				break;
			default:
				Smart::log_notice('Media Gallery // SmartGdImageProcess // Resized :: Unsupported Type (not PNG/GIF/JPEG ; Type='.$t_img.'): '.$imagePath);
				return 1; // not ok (invalid type)
		} //end switch
		//--
		if(!is_resource($source)) { // if the immage is corrupt or invalid ...
			Smart::log_warning('Media Gallery // SmartGdImageProcess // Resized :: Source Image Failure: '.$imagePath);
			return 2; // not ok (there was an error reading the image / have no privileges / or may be an invalid image type)
		} //end if
		//--

		//-- in the case if one of the W or H is not specified (zero), will be calculated based on image ratio
		if($newH <= 0) {
			$ratio = $imgH / $imgW;
			$newH = ceil($newW * $ratio);
		} elseif($newW <= 0) {
			$ratio = $imgW / $imgH;
			$newW = ceil($imgH * $ratio);
		} //end if
		//-- param fixes and constraints (after fixing the missing W or H
		$newW = (int) $newW;
		if($newW < 10) {
			$newW = 10;
		} //end if
		if($newW > 2048) {
			$newW = 2048;
		} //end if
		//--
		$newH = (int) $newH;
		if($newH < 10) {
			$newH = 10;
		} //end if
		if($newH > 2048) {
			$newH = 2048;
		} //end if
		//--

		//-- preserve the image as is if lower dimensions and set so
		if((string)$iflowerpreserve == 'yes') {
			if(($imgW <= $newW) AND ($imgH <= $newH)) {
				$newW = $imgW;
				$newH = $imgH;
			} //end if
		} //end if
		//--

		//-- picture scalling params
		if($imgW > $imgH) {
			//--
			$scale = ($imgH / $imgW);
			//-- new image dimensions
			$newImgW = $newW; // keep the width
			$newImgH = floor($newImgW * $scale);
			//--
		} else {
			//--
			$scale = ($imgW / $imgH);
			//-- new image dimensions
			$newImgH = $newH; // keep the height
			$newImgW = floor($newImgH * $scale);
			//--
		} //end if else
		//--

		//-- create the new img
		$imgnew = @imagecreatetruecolor($newImgW, $newImgH);
		//-- fill image with bg color
		$background = @imagecolorallocate($imgnew, $colorRGB[0], $colorRGB[1], $colorRGB[2]);
		@imagefill($imgnew, 0, 0, $background);
		//-- copy image in the new created image
		@imagecopyresampled($imgnew, $source, 0, 0, 0, 0, $newImgW, $newImgH, $imgW, $imgH);
		//--

		//--
		// if the destination image exists it should be first deleted (this must be managed outside ...)
		//--

		//-- saving new image
		switch((string)$the_type) {
			case 'png':
				@imagepng($imgnew, $newPath);
				break;
			case 'gif':
				@imagegif($imgnew, $newPath);
				break;
			case 'jpg':
				@imagejpeg($imgnew, $newPath, $quality); // preserve 100% quality for jpeg
				break;
			default:
				// this should not happen, it is catched above
		} //end switch
		//--

		//--
		@imagedestroy($source);
		@imagedestroy($imgnew);
		//--

		//--
		return 0; // OK
		//--

	} //end if

	//--
	return -1; // not ok, file does not exists / not a file / invalid path provided
	//--

} //END FUNCTION
//===========================================================================


//===========================================================================
// Apply Watermark [OK: returns 0 if OK or non-zero on errors]
public static function apply_watermark($imagePath, $watermarkPath, $quality, $gravity) {

	//-- check for required extension
	self::check_gd_truecolor();
	//--

	//--
	SmartFileSysUtils::raise_error_if_unsafe_path($imagePath);
	SmartFileSysUtils::raise_error_if_unsafe_path($watermarkPath);
	//--

	//--
	$imagePath = (string) $imagePath;
	$watermarkPath = (string) $watermarkPath;
	//--

	//--
	if((is_file($imagePath)) AND (is_file($watermarkPath))) {

		//--
		$arr_imgsize = (array) @getimagesize($watermarkPath);
		$wtmW = (int) $arr_imgsize[0];
		$wtmH = (int) $arr_imgsize[1];
		$t_wtm = (int) $arr_imgsize[2]; // OK
		unset($arr_imgsize);
		//--
		if($t_wtm <= 0) {
			Smart::log_notice('Media Gallery // SmartGdImageProcess // Watermark :: Unknown Type [W]: '.$watermarkPath);
			return 1; // not ok (unknown type)
		} //end if
		$t_wtm = (string) @image_type_to_mime_type((int)$t_wtm); // OK
		//--
		switch((string)$t_wtm) {
			case 'image/png':
			case 'image/x-png':
				$watermark = @imagecreatefrompng($watermarkPath);
				break;
			case 'image/gif':
				$watermark = @imagecreatefromgif($watermarkPath);
				break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$watermark = @imagecreatefromjpeg($watermarkPath);
				break;
			default:
				Smart::log_notice('Media Gallery // SmartGdImageProcess // Watermark :: Unsupported Type [W] (not PNG/GIF/JPEG ; Type='.$t_wtm.'): '.$watermarkPath);
				return 1; // not ok (invalid type)
		} //end switch
		//--
		if(!is_resource($watermark)) { // if the immage is corrupt or invalid ...
			Smart::log_warning('Media Gallery // SmartGdImageProcess // Watermark :: Source Watermark Image Failure: '.$watermarkPath);
			return 2; // not ok (there was an error reading the image / have no privileges / or may be an invalid image type)
		} //end if
		//--

		//--
		$arr_imgsize = (array) @getimagesize($imagePath);
		$imgW = (int) $arr_imgsize[0];
		$imgH = (int) $arr_imgsize[1];
		$t_img = (int) $arr_imgsize[2]; // OK
		unset($arr_imgsize);
		//--
		if($t_img <= 0) {
			Smart::log_notice('Media Gallery // SmartGdImageProcess // Watermark :: Unknown Type [I]: '.$imagePath);
			return 3; // not ok (unknown type)
		} //end if
		$t_img = (string) @image_type_to_mime_type((int)$t_img); // OK
		//--
		switch((string)$t_img) {
			case 'image/png':
			case 'image/x-png':
				$the_type = 'png';
				$source = @imagecreatefrompng($imagePath);
				break;
			case 'image/gif':
				$the_type = 'gif';
				$source = @imagecreatefromgif($imagePath);
				break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$the_type = 'jpg';
				$source = @imagecreatefromjpeg($imagePath);
				break;
			default:
				Smart::log_notice('Media Gallery // SmartGdImageProcess // Watermark :: Unsupported Type [I] (not PNG/GIF/JPEG ; Type='.$t_img.'): '.$imagePath);
				return 3; // not ok (invalid type)
		} //end switch
		//--
		if(!is_resource($source)) { // if the immage is corrupt or invalid ...
			Smart::log_warning('Media Gallery // SmartGdImageProcess // Watermark :: Source Image Failure: '.$watermarkPath);
			return 4; // not ok (there was an error reading the image / have no privileges / or may be an invalid image type)
		} //end if
		//--

		//-- apply watermark
		switch((string)$gravity) { // {{{SYNC-GRAVITY}}}
			case 'northwest':
				$gravityX = 0;
				$gravityY = 0;
				break;
			case 'northeast':
				$gravityX = ceil($imgW - $wtmW);
				$gravityY = 0;
				break;
			case 'southwest':
				$gravityX = 0;
				$gravityY = ceil($imgH - $wtmH);
				break;
			case 'southeast':
				$gravityX = ceil($imgW - $wtmW);
				$gravityY = ceil($imgH - $wtmH);
				break;
			case 'center':
			default:
				$gravityX = ceil(($imgW / 2) - ($wtmW / 2));
				$gravityY = ceil(($imgH / 2) - ($wtmH / 2));
		} //end switch
		//--
		@imagecopy($source, $watermark, $gravityX, $gravityY, 0, 0, $wtmW, $wtmH);
		//--

		//-- saving new image
		switch((string)$the_type) {
			case 'png':
				@imagepng($source, $imagePath);
				break;
			case 'gif':
				@imagegif($source, $imagePath);
				break;
			case 'jpg':
				@imagejpeg($source, $imagePath, $quality); // preserve 100% quality for jpeg
				break;
			default:
				// this should not happen, it is catched above
		} //end switch
		//--

		//--
		@imagedestroy($source);
		@imagedestroy($watermark);
		//--

		//--
		return 0; // OK
		//--

	} //end if else
	//--

	//--
	return -1; // not ok, files do not exists / not files / invalid paths provided
	//--

} //END FUNCTION
//===========================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//end of php code
?>