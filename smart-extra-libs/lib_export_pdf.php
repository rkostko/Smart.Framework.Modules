<?php
// [LIB - SmartFramework / Plugins / PDF Export]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.3.1.2 r.2017.04.11 / smart.framework.v.3.1

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.1')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// PDF Export - using HTMLDoc
// DEPENDS:
//		* Smart::
//		* SmartUtils::
//		* SmartFileSysUtils::
//		* SmartHtmlParser->
// DEPENDS-EXT: HTMLDOC Executable 1.8.x (external)
// 		tested with htmldoc-1.8.24 / htmldoc-1.8.25 / htmldoc-1.8.26 / htmldoc-1.8.27
//======================================================

// [REGEX-SAFE-OK]

/* Config settings required for this library:
define('SMART_FRAMEWORK_PDF_GENERATOR_APP', 	'/usr/bin/htmldoc'); 			// path to HtmlDoc Utility (change to match your system) ; can be `/usr/bin/htmldoc` or `/usr/local/bin/htmldoc` or `c:/open_runtime/htmldoc/htmldoc.exe` or any custom path
define('SMART_FRAMEWORK_PDF_GENERATOR_FORMAT', 	'pdf13'); 						// PDF format: `pdf14` | `pdf13` | `pdf12`
define('SMART_FRAMEWORK_PDF_GENERATOR_MODE', 	'color'); 						// PDF mode: `color` | `gray`
*/

//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================

/**
 * Class: SmartPdfExport - Exports HTML Code to PDF Document.
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @depends 	executables: HTMLDoc ; classes: Smart, SmartUtils, SmartFileSysUtils, SmartHtmlParser
 * @version 	v.160204
 * @package 	Exporters
 *
 */
final class SmartPdfExport {

	// ::


//=====================================================================
/**
 * Check if HTMLDoc exists and is set correctly
 *
 * @return '' OR '/path/to/htmldoc.exe'
 */
public static function is_active() {
	//--
	$out = '';
	//--
	if((defined('SMART_FRAMEWORK_PDF_GENERATOR_APP')) AND ((string)SMART_FRAMEWORK_PDF_GENERATOR_APP != '')) {
		if(is_executable(SMART_FRAMEWORK_PDF_GENERATOR_APP)) {
			$out = SMART_FRAMEWORK_PDF_GENERATOR_APP;
		} //end if
	} //end if
	//--
	return $out;
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG END (all code between START/END like these tags will be removed)
 *
 * @return HTML Comment
 */
public static function tag_remove_start() {
	//--
	return '<!-- PDF REMOVE START -->';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG START (all code between START/END like these tags will be removed)
 *
 * @return HTML Comment
 */
public static function tag_remove_end() {
	//--
	return '<!-- PDF REMOVE END -->';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG HTMLDoc PageBreak
 *
 * @return HTML Comment
 */
public static function tag_page_break() {
	//--
	return '<!-- PAGE BREAK -->';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG HTMLDoc Page SIZE
 *
 * @return HTML Comment
 */
public static function tag_page_size() {
	//--
	return '<!-- MEDIA SIZE 215x279mm -->'; // A4
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG HTMLDoc Page PORTRAIT
 *
 * @return HTML Comment
 */
public static function tag_page_normal() {
	//--
	return self::tag_page_size().'<!-- MEDIA LANDSCAPE NO -->';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Set an HTML Comment TAG HTMLDoc Page LANDSCAPE
 *
 * @return HTML Comment
 */
public static function tag_page_wide() {
	//--
	return self::tag_page_size().'<!-- MEDIA LANDSCAPE YES -->';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Get the PDF Document Mime Type Header Data
 *
 * @return STRING		'application/pdf'
 */
public static function pdf_mime_header() {
	//--
	return (string) 'application/pdf';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Get the PDF Document FileName Header Data
 *
 * @param STRING 	$y_filename		:: The PDF Document file name: default is: file.pdf
 * @param ENUM 		$y_disp 		:: The content disposition, default is: inline ; can be also: attachment
 *
 * @return STRING		'inline; filename="somedoc.pdf"' or 'attachment; filename="somedoc.pdf"'
 *
 */
public static function pdf_disposition_header($y_filename='file.pdf', $y_disp='inline') {
	//--
	switch((string)$y_disp) {
		case 'attachment':
			$y_disp = 'attachment';
			break;
		case 'inline':
		default:
			$y_disp = 'inline';
	} //end switch
	//--
	return (string) $y_disp.'; filename="'.Smart::safe_validname($y_filename).'"';
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Generate a PDF Document on the fly from a piece of HTML code.
 *
 * Notice: this is using a secured cache folder, unique per visitor ID
 *
 * @param STRING $y_html_content				:: The HTML Code
 * @param ENUM $y_orientation					:: Page Orientation: 'normal' | 'wide'
 * @param STRING $y_runtime_script 				:: The allowed Runtime Script to allow send credentials for sub-downloads. Ex: admin.php
 * @param STRING $y_runtime_url					:: The allowed Runtime URL ended by '/' to allow send credentials for sub-downloads. Ex: http(s)://some-server/some_path/ ; normally this should be set in config to enforce https:// and a single URL only
 * @param BOOLEAN $y_allow_send_credentials 	:: Set to TRUE to allow or set to FALSE to dissalow sending the auth credentials for sub-downloads: in the case there are embedded pictures generated by admin.php which may need authentication before to work, the credentials need to be set automatically in this case
 *
 * @returns STRING 							:: The PDF Document Contents
 *
 */
public static function generate($y_html_content, $y_orientation='normal', $y_runtime_script='', $y_runtime_url='', $y_allow_send_credentials=false) {
	//--
	$pdfdata = '';
	//--
	$htmldoc = self::is_active();
	//--
	if((string)$htmldoc != '') {
		//--
		if((string)$y_orientation == 'wide') {
			$orientation = self::tag_page_wide();
		} else {
			$orientation = self::tag_page_normal();
		} //end if else
		//--
		$tmp_prefix_dir = 'tmp/cache/pdf/';
		$protect_file = $tmp_prefix_dir.'.htaccess';
		$dir = $tmp_prefix_dir.SMART_FRAMEWORK_SESSION_PREFIX.'/'; // we use different for index / admin / @
		//--
		$uniquifier = SmartUtils::unique_auth_client_private_key().SMART_APP_VISITOR_COOKIE;
		$the_dir = $dir.Smart::safe_varname(Smart::uuid_10_seq().'_'.Smart::uuid_10_num().'_'.SmartHashCrypto::sha1($uniquifier)).'/';
		//--
		$tmp_uuid = Smart::uuid_45($uniquifier).Smart::uuid_36($uniquifier);
		$file = $the_dir.'__document_'.SmartHashCrypto::sha256('@@PDF#File::Cache@@'.$tmp_uuid).'.html' ;
		$logfile = $the_dir.'__headers_'.SmartHashCrypto::sha256('@@PDF#File::Cache@@'.$tmp_uuid).'.log';
		//--
		if(is_dir($the_dir)) {
			SmartFileSystem::dir_delete($the_dir);
		} //end if
		//--
		if(!is_dir($the_dir)) {
			SmartFileSystem::dir_recursive_create($the_dir);
		} // end if
		//--
		SmartFileSystem::write_if_not_exists($protect_file, trim(SMART_FRAMEWORK_HTACCESS_FORBIDDEN)."\n", 'yes');
		//-- process the code
		$y_html_content = (string) self::remove_between_tags((string)$y_html_content);
		$y_html_content = (string) self::safe_charset((string)$y_html_content);
		//-- extract images
		$htmlparser = new SmartHtmlParser((string)$y_html_content);
		$arr_imgs = $htmlparser->get_tags('img');
		$htmlparser = '';
		unset($htmlparser);
		//--
		$chk_duplicates_arr = array();
		//--
		for($i=0; $i<Smart::array_size($arr_imgs); $i++) {
			//--
			$tmp_img_src = trim((string)$arr_imgs[$i]['src']);
			//--
			if(strlen($chk_duplicates_arr[$tmp_img_src]) <= 0) {
				//--
				$tmp_url_img_src = '';
				//--
				if(((string)$y_runtime_script != '') AND ((string)$y_runtime_url != '')) { // replace relative paths
					if(substr($tmp_img_src, 0, @strlen($y_runtime_script)) == (string)$y_runtime_script) {
						$tmp_url_img_src = (string) $y_runtime_url.$tmp_img_src;
						$y_html_content = (string) @str_replace('src="'.$tmp_img_src.'"', 'src="'.$tmp_url_img_src.'"', (string)$y_html_content);
						$tmp_img_src = (string) $tmp_url_img_src;
					} //end if
				} //end if
				//--
				$tmp_img_ext = '.'.strtolower(SmartFileSysUtils::get_file_extension_from_path($tmp_img_src)); // [OK]
				$tmp_img_cache = 'pdf_img_'.SmartHashCrypto::sha256('@@PDF#File::Cache::IMG@@'.'#'.$i.'@'.$tmp_img_src.'//'.$tmp_uuid);
				//--
				$tmp_arr = array();
				//--
				if((substr($tmp_img_src, 0, 7) == 'http://') OR (substr($tmp_img_src, 0, 8) == 'https://')) {
					//--
					$tmp_img_ext = ''; // we clear the extension as we don't know yet (we will get it from headers)
					$tmp_img_cache = 'pdf_url_img_'.SmartHashCrypto::sha256('@@PDF#File::Cache::URL::IMG@@'.'#'.$i.'@'.$tmp_img_src.'//'.$tmp_uuid);
					//--
				} //end if
				//--
				if($y_allow_send_credentials === true) {
					$allow_set_credentials = 'yes';
				} else {
					$allow_set_credentials = 'no';
				} //end if else
				//--
				$tmp_arr = SmartUtils::load_url_or_file($tmp_img_src, SMART_FRAMEWORK_NETSOCKET_TIMEOUT, 'GET', '', '', '', $allow_set_credentials); // [OK] :: allow set credentials
				//--
				$tmp_img_ext = '.noextension';
				$tmp_where_we_guess = '';
				//--
				$guess_arr = array();
				//--
				$guess_arr = SmartUtils::guess_image_extension_by_url_head($tmp_arr['headers']);
				$tmp_img_ext = (string) $guess_arr['extension'];
				$tmp_where_we_guess = (string) $guess_arr['where-was-detected'];
				$guess_arr = array();
				if((string)$tmp_img_ext == '') {
					$tmp_img_ext = SmartUtils::guess_image_extension_by_first_bytes(substr($tmp_arr['content'], 0, 256));
					if((string)$tmp_img_ext != '') {
						$tmp_where_we_guess = ' First Bytes ...';
					} //end if
				} //end if
				//--
				if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') { // if debug, append information to log
					SmartFileSystem::write($logfile, '####################'."\n".'#################### [FILE # '.$i.' = \''.$tmp_img_src.'\']'."\n\n".'==== [MODE] :: '.$tmp_arr['mode']."\n".'==== [LOG] :: '."\n".$tmp_arr['log']."\n".'==== [HEADERS] ::'."\n".$tmp_arr['headers']."\n".'########'."\n".'==== [GUESS EXTENSION] :: '.$tmp_where_we_guess."\n\n".'###################'."\n\n\n\n", 'a');
				} //end if
				//--
				if(((string)$tmp_arr['result'] == '1') AND ((string)$tmp_arr['code'] == '200')) {
					//--
					SmartFileSystem::write($the_dir.$tmp_img_cache.$tmp_img_ext, $tmp_arr['content']);
					//-- if empty, it may be a file
					if(((string)$tmp_img_ext == '') OR ((string)$tmp_img_ext == '.png') OR ((string)$tmp_img_ext == '.gif') OR ((string)$tmp_img_ext == '.jpg')) {
						$y_html_content = (string) @str_replace('src="'.$tmp_img_src.'"', 'src="'.$tmp_img_cache.$tmp_img_ext.'"', (string)$y_html_content);
					} else { // we want to avoid html code to be loaded as image by mistakes of http browser class or servers
						$y_html_content = (string) @str_replace('src="'.$tmp_img_src.'"', 'src="'.$y_runtime_url.'lib/framework/img/sign_warn.png"', (string)$y_html_content);
					} //end if else
					//--
				} else {
					//--
					$y_html_content = (string) @str_replace('src="'.$tmp_img_src.'"', 'src="'.$y_runtime_url.'lib/framework/img/sign_error.png"', (string)$y_html_content);
					//--
				} //end if
				//--
			} //end if
			//--
			$chk_duplicates_arr[$tmp_img_src] = 'processed';
			//--
		} //end for
		//--
		$chk_duplicates_arr = array();
		unset($chk_duplicates_arr);
		$arr_imgs = array();
		unset($arr_imgs);
		//--
		SmartFileSystem::write($file, $orientation."\n".$y_html_content);
		//--
		if(is_file($file)) {
			//--
			ob_start();
			//--
			@passthru($htmldoc.' '.self::pdf_options($file));
			//--
			$pdfdata = ob_get_clean();
			//--
		} else {
			//--
			Smart::log_warning('ERROR: PDF Generator Failed to find the PDF Document: '.$file."\n".$y_html_content);
			//--
		} //end if else
		//-- cleanup
		if((string)SMART_FRAMEWORK_DEBUG_MODE != 'yes') { // if not debug, cleanup the dir
			if(is_dir($the_dir)) {
				SmartFileSystem::dir_delete($the_dir);
			} //end if
		} //end if
		//--
	} else {
		//--
		Smart::log_notice('NOTICE: PDF Generator is INACTIVE ...');
		//--
	} //end if
	//--
	return (string) $pdfdata;
	//--
} //END FUNCTION
//=====================================================================


//############### PRIVATES


//=====================================================================
/**
 * Remove all HTML Code between PDF-REMOVE Tags
 *
 * @param HTML Code $y_html
 * @return HTML Code
 */
private static function remove_between_tags($y_html) {
	//--
	return @preg_replace("'".self::tag_remove_start().".*?".self::tag_remove_end()."'si", '&nbsp;', (string)$y_html); // insensitive
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Replace HTML type accents in UTF-8 encoded strings, and then DE-ACCENT
 * Replace accented UTF-8 characters like by unaccented ASCII-7 equivalents
 *
 * @param STRING $string
 * @return STRING
 */
private static function safe_charset($html) {
	//--
	return SmartUnicode::html_entities($html);
	//--
} //END FUNCTION
//=====================================================================


//=====================================================================
/**
 * Return HTMLDoc Options
 *
 * @param STRING $y_html_file 	:: HTMLDoc Input File as: /path/to/file.html
 * @return STRING
 */
private static function pdf_options($y_html_file) {
	//--
	$y_html_file = (string) $y_html_file;
	//--
	if((defined('SMART_FRAMEWORK_PDF_GENERATOR_MODE')) AND (strtolower((string)SMART_FRAMEWORK_PDF_GENERATOR_MODE == 'gray'))) {
		$pdf_color = '--gray';
	} else {
		$pdf_color = '--color';
	} //end if else
	//--
	if(defined('SMART_FRAMEWORK_PDF_GENERATOR_FORMAT')) {
		switch(strtolower((string)SMART_FRAMEWORK_PDF_GENERATOR_FORMAT)) {
			case 'pdf14':
				$pdf_ver = 'pdf14';
				break;
			case 'pdf12':
				$pdf_ver = 'pdf12';
				break;
			case 'pdf13':
			default:
				$pdf_ver = 'pdf13';
		} //end switch
	} else {
		$pdf_ver = 'pdf13';
	} //end if else
	//-- replace "
	$y_html_file = trim(str_replace('"', '', $y_html_file));
	//-- executable convert options as FILE/STDIN output
	return '--quiet '.$pdf_color.' --format '.$pdf_ver.' --charset ISO-8859-1 --browserwidth 900 --embedfonts --permissions no-modify,no-annotate,no-copy --encryption --no-links --no-strict --no-toc --jpeg=100 --compression 5 --numbered --fontspacing 1.2 --textfont Helvetica --fontsize 9.0 --headfootfont Helvetica --headfootsize 7.0 --header ... --footer ../ --continuous --left 10mm --right 10mm --top 7mm --bottom 7mm --pagelayout single --pagemode document "'.$y_html_file.'"';
	//--
} //END FUNCTION
//=====================================================================


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>