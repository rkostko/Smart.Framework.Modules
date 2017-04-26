<?php
// [@[#[!DEV-ONLY!]#]@]
// Controller: Media Gallery Test Sample
// Route: ?/page/media-gallery.test (?page=media-gallery.test)
// Author: unix-world.org
// v.3.1.2 r.2017.04.11 / smart.framework.v.3.1

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'SHARED'); // INDEX, ADMIN, SHARED

/*
define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_CONVERTER', 	'/usr/local/bin/convert'); 				// `@gd` | path to ImagMagick Convert (change to match your system) ; can be `/usr/bin/convert` or `/usr/local/bin/convert` or `c:/open_runtime/image_magick/convert.exe`
define('SMART_FRAMEWORK_MEDIAGALLERY_IMG_COMPOSITE', 	'/usr/local/bin/composite'); 			// `@gd` | path to ImagMagick Composite/Watermark (change to match your system) ; can be `/usr/bin/composite` or `/usr/local/bin/composite` or `c:/open_runtime/image_magick/composite.exe`
define('SMART_FRAMEWORK_MEDIAGALLERY_MOV_THUMBNAILER', 	'/usr/local/bin/ffmpeg'); 				// path to FFMpeg (Video Thumbnailer to extract a preview Image from a movie) ; (change to match your system) ; can be `/usr/bin/ffmpeg` or `/usr/local/bin/ffmpeg` or `c:/open_runtime/ffmpeg/ffmpeg.exe`
*/

//define('SMART_FRAMEWORK_MEDIAGALLERY_SECUREMODE', true);
//define('SMART_FRAMEWORK_MEDIAGALLERY_WATERMARK', 'modules/mod-media-gallery/views/img/delete.png');

/**
 * Index Controller
 *
 * @ignore
 *
 */
class SmartAppIndexController extends SmartAbstractAppController {

	public function Run() {

		//-- dissalow run this sample if not test mode enabled
		if(SMART_FRAMEWORK_TEST_MODE !== true) {
			$this->PageViewSetErrorStatus(500, 'ERROR: Test mode is disabled ...');
			return;
		} //end if
		//--

		//--
		if(SMART_FRAMEWORK_MEDIAGALLERY_SECUREMODE === true) {
			//--
			$key = sha1((string)SMART_FRAMEWORK_SECURITY_KEY.date('Y-m-d'));
			//--
			$lnk = $this->RequestVarGet('lnk', '', 'string');
			if((string)$lnk != '') {
				$this->PageViewSetCfgs([
					'download-key' 		=> (string) $key,
					'download-packet' 	=> (string) $lnk
				]);
				return;
			} //end if
			//--
		} //end if
		//--

		//--
		$mg = new \SmartModExtLib\MediaGallery\Manager();
		//--
		if(SMART_FRAMEWORK_MEDIAGALLERY_SECUREMODE === true) {
			//--
			$mg->use_secure_links 			= 'yes';
			$mg->secure_download_link 		= '?page='.Smart::escape_url($this->ControllerGetParam('controller')).'&lnk=';
			$mg->secure_download_ctrl_key 	= (string) $key;
			//--
		} //end if
		//--
		if(defined('SMART_FRAMEWORK_MEDIAGALLERY_WATERMARK')) {
			$mg->img_watermark 			= (string) SMART_FRAMEWORK_MEDIAGALLERY_WATERMARK;
			$mg->preview_watermark 		= (string) SMART_FRAMEWORK_MEDIAGALLERY_WATERMARK;
		} //end if
		//--

		//--
		$this->PageViewSetVars([
			'title' => 'Sample Media Gallery',
			'main' => (string) '<link rel="stylesheet" type="text/css" href="'.Smart::escape_html($this->ControllerGetParam('module-path')).'views/css/mediagallery.css">'.$mg->draw(
				'Sample Media Gallery',
				'wpub/sample-media-gallery',
				'yes', // process img and movies
				'no', // remove originals
				0 // display limit
			)
		]);
		//--

	} //END FUNCTION

} //END CLASS

class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php

} //END CLASS

//end of php code
?>