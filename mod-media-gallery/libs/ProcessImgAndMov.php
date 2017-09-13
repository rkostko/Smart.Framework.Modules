<?php
// Class: \SmartModExtLib\MediaGallery\ProcessImgAndMov
// Media Gallery Process: Images and Movies :: for Smart.Framework
// Module Library
// v.3.5.7 r.2017.09.05 / smart.framework.v.3.5

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\MediaGallery;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// Media Gallery - Process Images and Videos (Movies)
// DEPENDS:
//	* Smart::
//	* SmartFileSystem::
//	* SmartFileSysUtils::
//	* SmartFrameworkRegistry::
// 	* \SmartModExtLib\MediaGallery\ImgProcGd::
//	* \SmartModExtLib\MediaGallery\ImgProcImagick::
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


// TODO:
// use PDF2SWF: /usr/pkg/bin/pdf2swf -z -t -Q 120 file.pdf -o file.swf
// or: Pdf2HtmlEx


//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


/**
 * Media Gallery Processing (Images and Movies)
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @access 		private
 * @internal
 *
 * @depends 	extensions: plugins: \SmartModExtLib\MediaGallery\ImgProcImagick:: OR \SmartModExtLib\MediaGallery\ImgProcGd:: ;
 * @version 	v.170518
 * @package 	Media:Gallery
 *
 */
final class ProcessImgAndMov { // [OK]

	// ::

	private static $blank_mov = 'modules/mod-media-gallery/views/img/video.jpg'; // this must be jpeg like the preview generated by ffmpeg
	private static $wtmim_mov = 'modules/mod-media-gallery/views/img/play.png';


//===================================================================== [OK]
// this is for the uploads of specific mediagallery content only (to replace the
public static function get_allowed_extensions_list() {

	//--
	return (string) SMART_FRAMEWORK_UPLOAD_PICTS.','.SMART_FRAMEWORK_UPLOAD_MOVIES; // <pdf>,<swf>
	//--

} //END FUNCTION
//=====================================================================


//===================================================================== [OK]
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
	return (int) $out;
	//--

} //END FUNCTION
//=====================================================================


//===================================================================== [OK]
// Resize or Create a Preview for an Image, and Apply a watermark if set
public static function img_process($y_mode, $iflowerpreserve, $y_file, $y_newfile, $y_quality, $y_width, $y_height, $y_watermark='', $y_waterlocate='center') {

	//--
	$y_mode = (string) $y_mode;
	$iflowerpreserve = (string) $iflowerpreserve;
	$y_file = (string) trim((string)$y_file);
	$y_newfile = (string) trim((string)$y_newfile);
	$y_quality = (int) $y_quality;
	$y_width = (int) $y_width;
	$y_height = (int) $y_height;
	$y_watermark = (string) trim((string)$y_watermark);
	$y_waterlocate = (string) $y_waterlocate;
	//--

	//--
	if(!\SmartFileSysUtils::check_file_or_dir_name($y_file)) {
		\Smart::log_warning(__METHOD__.' :: img_process // Unsafe Path: SRC='.$y_file);
		return '';
	} //end if
	//--
	if(!\SmartFileSysUtils::check_file_or_dir_name($y_newfile)) {
		\Smart::log_warning(__METHOD__.' :: img_process // Unsafe Path: DEST='.$y_newfile);
		return '';
	} //end if
	//--
	if((string)$y_file == (string)$y_newfile) {
		\Smart::log_warning(__METHOD__.' :: img_process // The Origin and Destination images are the same: SRC='.$y_file.' ; DEST='.$y_newfile);
		return '';
	} //end if
	//--
	if((string)$y_watermark != '') {
		if(!\SmartFileSysUtils::check_file_or_dir_name($y_watermark)) {
			$y_watermark = '';
			\Smart::log_warning(__METHOD__.' :: img_process // Unsafe Path: WATERMARK='.$y_watermark);
		} //end if
	} //end if
	//--

	//--
	if((string)$iflowerpreserve != 'no') {
		$iflowerpreserve = 'yes';
	} //end if
	//--

	//--
	$y_quality = (int) \Smart::format_number_int($y_quality,'+');
	if($y_quality < 1) {
		$y_quality = 1;
	} //end if
	if($y_quality > 100) {
		$y_quality = 100;
	} //end if
	//--

	//--
	$y_width 	= (int) \Smart::format_number_int($y_width,'+');
	$y_height 	= (int) \Smart::format_number_int($y_height,'+');
	//--
	switch((string)$y_mode) {
		case 'preview':
			//--
			$y_mode = 'preview';
			//--
			if($y_width < 16) {
				$y_width = 16;
			} //end if
			if($y_width > 320) {
				$y_width = 320;
			} //end if
			//--
			if($y_height < 16) {
				$y_height = 16;
			} //end if
			if($y_height > 320) {
				$y_height = 320;
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
			if($y_width > 1920) {
				$y_width = 1920;
			} //end if
			//--
			if($y_height !== 0) { // here can be zero to ignore height and resize by width keeping height proportion
				if($y_height < 320) {
					$y_height = 320;
				} //end if
				if($y_height > 1920) {
					$y_height = 1920;
				} //end if
			} //end if
			//--
			break;
		default:
			\Smart::log_warning(__METHOD__.' :: img_process INVALID MODE: '.$y_mode);
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
		\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
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
		$lock_time = (int) \Smart::format_number_int((string)\SmartFileSystem::read($lock_file),'+');
		//--
		if($lock_time > 0) {
			//--
			if(($lock_time + 30) < time()) { // allow max locktime of 30 seconds
				\SmartFileSystem::delete($y_newfile); // delete img as it might be incomplete (it will be created again later)
				\SmartFileSystem::delete($lock_file); // release the lock file
			} else {
				return '';
			} //end if
			//--
		} //end if
		//--
		if((is_file($y_file)) AND (!\SmartFileSystem::path_exists($y_newfile)) AND (!\SmartFileSystem::path_exists($lock_file))) {
			//--
			@chmod($y_file, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
			//--
			if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER == '@gd') OR (((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER)))) {
				//--
				$out .= '<table width="550" bgcolor="#FFCC00">';
				//--
				$out .= '<tr><td>Processing Image ['.strtoupper($y_mode).']:'.' '."'".\Smart::escape_html(\Smart::base_name($y_file))."'".' -&gt; '."'".\Smart::escape_html(\Smart::base_name($y_newfile))."'".'</td><tr>';
				//-- create a lock file
				\SmartFileSystem::write($lock_file, time());
				//--
				$exitcode = 0;
				//--
				if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER))) {
					//-- generate preview by ImageMagick
					if((string)$y_mode == 'preview') {
						$exec = (string) \SmartModExtLib\MediaGallery\ImgProcImagick::create_preview((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER, $y_file, $y_newfile, $y_width, $y_height, $y_quality);
					} else {
						$exec = (string) \SmartModExtLib\MediaGallery\ImgProcImagick::create_resized((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER, $y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve);
					} //end if else
					@exec($exec, $arr_result, $exitcode);
					//--
					$out .= '<tr><td>[DONE]</td></tr>';
					if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
						\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
							'title' => '[INFO] :: MediaUTIL/Img/Process/ImageMagick',
							'data' => 'Runtime Result: '."'".$y_file."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.'] @ '.@print_r($arr_result,1)
						]);
					} //end if
					//--
				} else {
					//-- generate preview by @GD Library
					if((string)$y_mode == 'preview') {
						$exitcode = \SmartModExtLib\MediaGallery\ImgProcGd::create_preview($y_file, $y_newfile, $y_width, $y_height, $y_quality);
					} else {
						$exitcode = \SmartModExtLib\MediaGallery\ImgProcGd::create_resized($y_file, $y_newfile, $y_width, $y_height, $y_quality, $iflowerpreserve);
					} //end if else
					//--
					$out .= '<tr><td>[*DONE*]</td></tr>';
					if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
						\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
							'title' => '[INFO] :: MediaUTIL/Img/Process/GD',
							'data' => 'Runtime Result: '."'".$y_file."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.']'
						]);
					} //end if
					//--
				} //end if else
				//--
				if($exitcode !== 0) {
					if(!is_file($y_newfile)) {
						\Smart::log_notice(__METHOD__.' :: Removing Invalid Image [Exitcode='.$exitcode.' / Converter='.SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER.']: '.$y_file);
						\SmartFileSystem::delete($y_file); // remove invalid files as the failures will go into infinite loops
					} //end if
				} //end if
				//-- apply watermark
				if((defined('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE')) AND ((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE != '') AND (strlen($y_watermark) > 0)) {
					//--
					if((is_file($y_newfile)) AND (is_file($y_watermark))) {
						//--
						if(((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE != '@gd') AND (is_executable(SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE))) {
							//--
							$exec = (string) \SmartModExtLib\MediaGallery\ImgProcImagick::apply_watermark((string)SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE, $y_newfile, $y_watermark, $y_quality, $y_waterlocate);
							@exec($exec, $arr_result, $exitcode);
							//--
							$out .= '<tr><td><i>[WATERMARK]</i></td></tr>';
							if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
								\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
									'title' => '[INFO] :: MediaUTIL/Img/Process/Watermark/ImageMagick',
									'data' => 'Runtime Result: '."'".$y_watermark."'".' -> '."'".$y_newfile."'".' = ['.$exitcode.'] @ '.@print_r($arr_result,1)
								]);
							} //end if
							//--
						} else {
							//--
							$exitcode = \SmartModExtLib\MediaGallery\ImgProcGd::apply_watermark($y_newfile, $y_watermark, $y_quality, $y_waterlocate);
							//--
							$out .= '<tr><td><i>[*WATERMARK*]</i></td></tr>';
							if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
								\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
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
				\SmartFileSystem::delete($lock_file);
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
	return (string) $out;
	//--

} //END FUNCTION
//=====================================================================


//===================================================================== [OK]
// Create a Preview for a movie
public static function mov_pw_process($y_mov_file, $y_mov_img_preview, $y_quality, $y_width, $y_height, $y_watermark='', $y_waterlocate='center', $y_mov_blank_img_preview='') {

	//--
	$y_mov_file = (string) trim((string)$y_mov_file);
	$y_mov_img_preview = (string) trim((string)$y_mov_img_preview);
	$y_quality = (int) $y_quality;
	$y_width = (int) $y_width;
	$y_height = (int) $y_height;
	$y_watermark = (string) trim((string)$y_watermark);
	$y_waterlocate = (string) $y_waterlocate;
	$y_mov_blank_img_preview = (string) trim((string)$y_mov_blank_img_preview);
	//--

	//--
	$blank_mov_pw = (string) self::$blank_mov;
	$watermark_mov_pw = (string) self::$wtmim_mov;
	//--

	//--
	if((string)$y_mov_blank_img_preview == '') {
		$y_mov_blank_img_preview = (string) $blank_mov_pw;
	} //end if
	if(!\SmartFileSysUtils::check_file_or_dir_name($y_mov_blank_img_preview)) {
		$y_mov_blank_img_preview = (string) $blank_mov_pw;
	} //end if
	if(!is_file($y_mov_blank_img_preview)) {
		\Smart::log_warning(__METHOD__.' :: mov_pw_process // Invalid Blank Preview Path: BLANK-PREVIEW='.$y_mov_blank_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_watermark == '') {
		$y_watermark = (string) $watermark_mov_pw;
	} //end if
	//--

	//--
	if(!\SmartFileSysUtils::check_file_or_dir_name($y_mov_file)) {
		\Smart::log_warning(__METHOD__.' :: mov_pw_process // Unsafe Path: SRC='.$y_mov_file);
		return '';
	} //end if
	//--
	if(!\SmartFileSysUtils::check_file_or_dir_name($y_mov_img_preview)) {
		\Smart::log_warning(__METHOD__.' :: mov_pw_process // Unsafe Path: DEST='.$y_mov_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_mov_file == (string)$y_mov_img_preview) {
		\Smart::log_warning(__METHOD__.' :: mov_pw_process // The Origin movie and Destination image are the same: SRC='.$y_mov_file.' ; DEST='.$y_mov_img_preview);
		return '';
	} //end if
	//--
	if((string)$y_watermark != '') {
		if(!\SmartFileSysUtils::check_file_or_dir_name($y_watermark)) {
			$y_watermark = '';
			\Smart::log_warning(__METHOD__.' :: mov_pw_process // Unsafe Path: WATERMARK='.$y_watermark);
		} //end if
	} //end if
	//--

	//--
	$y_quality = (int) \Smart::format_number_int($y_quality,'+');
	if($y_quality < 1) {
		$y_quality = 1;
	} //end if
	if($y_quality > 100) {
		$y_quality = 100;
	} //end if
	//--

	//--
	$y_width 	= (int) \Smart::format_number_int($y_width,'+');
	$y_height 	= (int) \Smart::format_number_int($y_height,'+');
	//--
	if($y_width < 16) {
		$y_width = 16;
	} //end if
	if($y_width > 320) {
		$y_width = 320;
	} //end if
	//--
	if($y_height < 16) {
		$y_height = 16;
	} //end if
	if($y_height > 320) {
		$y_height = 320;
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
		\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
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
		$lock_time = (int) \Smart::format_number_int((string)\SmartFileSystem::read($lock_file),'+');
		//--
		if($lock_time > 0) {
			if(($lock_time + 45) < time()) { // allow max locktime of 45 seconds
				\SmartFileSystem::delete($temporary_pw); // delete the old temporary if any
				\SmartFileSystem::delete($lock_file); // release the lock file
			} //end if
		} //end if
		//--
		if((is_file($y_mov_file)) AND (!\SmartFileSystem::path_exists($y_mov_img_preview)) AND (!\SmartFileSystem::path_exists($lock_file))) {
			//--
			@chmod($y_mov_file, SMART_FRAMEWORK_CHMOD_FILES); //mark chmod
			//--
			$out .= '<table width="550" bgcolor="#74B83F">';
			$out .= '<tr><td>Processing Movie Preview:'.' '."'".\Smart::escape_html(\Smart::base_name($y_mov_file))."'".' -&gt; '."'".\Smart::escape_html(\Smart::base_name($y_mov_img_preview))."'".'</td></tr>';
			//-- create a lock file
			\SmartFileSystem::write($lock_file, time());
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
				\SmartFileSystem::delete($temporary_pw);
				\SmartFileSystem::copy($y_mov_blank_img_preview, $temporary_pw); // in the case ffmpeg fails we avoid enter into a loop, or if ffmpeg is not found we use a blank preview
			} //end if
			//--
			$out .= '<tr><td>[DONE]</td></tr>';
			if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
				\SmartFrameworkRegistry::setDebugMsg('extra', 'MEDIA-GALLERY', [
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
				\SmartFileSystem::delete($temporary_pw);
				//--
			} //end if
			//-- release the lock file
			\SmartFileSystem::delete($lock_file);
			//--
			$out .= '</table>';
			//--
		} //end if
		//--
	} //end if
	//--

	//--
	return (string) $out;
	//--

} //END FUNCTION
//=====================================================================


} //END CLASS


//==================================================================================================
//================================================================================================== END CLASS
//==================================================================================================


//end of php code
?>