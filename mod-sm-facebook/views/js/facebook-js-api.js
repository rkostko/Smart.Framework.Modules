
// Facebook JS API Handler
// (c) 2012 - 2017 Radu I.
// v.170908

// Depends on: jQuery
// Depends on: SmartJS_Base64, SmartJS_Archiver_LZS, SmartJS_BrowserUtils

var FacebookApiHandler = new function() { // START CLASS


	var FbSettings = {
		appId: 		'',
		lang: 		'en_US', // en_US ; ro_RO ; ...
		status: 	true,
		cookie: 	true,
		oauth: 		true,
		xfbml: 		true,
		perms: 		'public_profile,email,publish_actions', // Extended permissions (req. FB approval): 'manage_pages,publish_pages,user_managed_groups'
		version: 	'v2.10',
		domain: 	null
	};


	var FbAccessToken = null;
	var FbLoginData = null;


	var FbGetLoginData = function(fxResponseOk) {
		//-- {{{SYNC-FACEBOOK-GET-ME}}}
		FB.api('/me?fields=id,name,email,gender,birthday,location,timezone,locale,verified,permissions', function(response) {
			//console.log(response);
			if(response && response.id) {
				//--
				var perms = [];
				if(response.permissions && response.permissions.data) {
					for(var i=0; i<response.permissions.data.length; i++) {
						if(response.permissions.data[i].status === 'granted') {
							var p = response.permissions.data[i].permission;
							perms.push(String(p));
						} //end if
					} //end for
				} //end if
				//--
				FbLoginData = { // {{{SYNC-FB-DATA}}}
					//--
					httpstatus: '200/200',
					//--
					token: String(FbAccessToken) || '',
					//--
					uid: response.id || '',
					email: response.email || '',
					name: response.name || '',
					timezone: Math.round(response.timezone) * 60 || 0, // timezone in minutes
					location: response.location || '',
					//--
					locale: response.locale || '',
					gender: response.gender || '',
					birthday: response.birthday || '',
					//--
					verified: response.verified ? 1 : 0,
					permissions: perms
					//--
					// #imageurl# : http://graph.facebook.com/{uid}/picture?width=64|1280&height=64|1280
					//--
				};
				if(typeof fxResponseOk === 'function') {
					fxResponseOk(response, FbLoginData);
				} //end if
				storageSetItem('smartfbookjsapi_data', String(JSON.stringify(FbLoginData)), true);
			} //end if
			//console.log(FbLoginData);
		});
		//--
	} //END FUNCTION


	this.init = function(settings, fxSubscribe, fxResponseOk, fxResponseNotOk, fxResponseUnauth) {
		//--
		if(!settings.appId) {
			console.error('Facebook Js Api: No App Id used to initialize ...');
		} //end if
		//-- mandatory settings
		FbSettings.appId = String(settings.appId);
		//-- optional settings
		if(settings.lang) {
			FbSettings.lang = String(settings.lang);
		} //end if
		if(settings.status === false) {
			FbSettings.status = false;
		} //end if else
		if(settings.cookie === false) {
			FbSettings.cookie = false;
		} //end if else
		if(settings.oauth === false) {
			FbSettings.oauth = false;
		} //end if else
		if(settings.xfbml === false) {
			FbSettings.xfbml = false;
		} //end if else
		if(settings.perms) {
			FbSettings.perms = settings.perms;
		} //end if
		//-- async init
		window.fbAsyncInit = function() {
			FB.init({
				appId: 		FbSettings.appId,
				status: 	FbSettings.status,
				cookie: 	FbSettings.cookie,
				xfbml: 		FbSettings.xfbml,
				oauth: 		FbSettings.oauth,
				version: 	FbSettings.version
			});
			FB.Event.subscribe('auth.login', function(response) {
				if(typeof fxSubscribe === 'function') {
					fxSubscribe(response); // Ex: self.location = self.location;
				} //end if
			});
			FB.getLoginStatus(function(response) {
				//console.log('FB:Logging....');
				if(response && response.status === 'connected' && response.authResponse && response.authResponse.accessToken) {
					// the user is logged in and has authenticated your app, and response.authResponse supplies the user's ID,
					// a valid access token, a signed request, and the time the access token  and signed request each expire
					FbAccessToken = response.authResponse.accessToken;
					FbGetLoginData(fxResponseOk);
				} else if(response && response.status === 'not_authorized') {
					// the user is logged in to Facebook, but has not authorized the app
					if(typeof fxResponseUnauth === 'function') {
						fxResponseUnauth(response); // Ex: console.log('WARNING: You must accept this app via Facebook !');
					} //end if
				} else {
					// the user isn't logged in to Facebook
					if(typeof fxResponseNotOk === 'function') {
						fxResponseNotOk(response); // Ex: console.log('WARNING: Facebook Login Failed !');
					} //end if
				}
			});
		};
		//-- load FB api
		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if(d.getElementById(id)) {
				return;
			} //end if
			js = d.createElement(s); js.id = id;
			js.src = '//connect.facebook.net/' + FbSettings.lang + '/sdk.js';
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		//--
	} //END FUNCTION


	this.login = function(fxResponseOk) {
		//--
		FB.getLoginStatus(
			function(response){
				FB.login(
					function(response) {
						if(response && response.authResponse) { //user authorized the app
							if(response.authResponse && response.authResponse.accessToken) {
								FbAccessToken = response.authResponse.accessToken;
								FbGetLoginData(fxResponseOk);
							} //end if
						} //end if
					},
					{
						scope: String(FbSettings.perms),
						return_scopes: true
					}
				);
			} //end function
		);
		//--
	} //END FUNCTION


	this.logout = function(fxResponseLogout) {
		//--
		FB.getLoginStatus(
			function(response){
				if((response && response.status === 'connected') || (response && response.status === 'not_authorized')) {
					FB.logout(function(response) {
						FbLoginData = null;
						FbAccessToken = null;
						storageSetItem('smartfbookjsapi_data', '', true);
						fxResponseLogout(response);
					});
				} //end if
			} //end function
		);
		//--
	} //END FUNCTION


	this.postFeed = function(data, fxDone, fxFail) {
		FB.ui(
			{ // data definition
				method: 		'feed',
				name: 			'' + data.name,
				link: 			'' + data.link,
				picture: 		'' + data.picture,
				caption: 		'' + data.caption,
				description: 	'' + data.description
			},
			function(response) {
				if(response && response.post_id) {
					if(typeof fxDone === 'function') {
						fxDone(response); //Ex: console.log('Post was published.');
					} //end if
				} else {
					if(typeof fxFail === 'function') {
						fxFail(response); //Ex: console.log('Post was not published.');
					} //end if
				} //end if else
			}
		);
	} //END FUNCTION


	this.postImage = function(imgB64Data, postMessage, fxDone, fxFail) {
		//--
		var mimeType = imgB64Data.split(',')[0];
		mimeType = mimeType.split(';')[0];
		mimeType = mimeType.split(':')[1];
		//console.log(mimeType);
		var imB64 = imgB64Data.split(',')[1];
		imgB64Data = null; // free mem
		//console.log(imB64);
		//--
		var blob;
		try {
		//	if(typeof SmartJS_Base64 != 'undefined') {
				var byteString = SmartJS_Base64.decode(imB64, true); // works in all browsers
		//	} else {
		//		var byteString = atob(imB64); // IE 10+ ; FFox 3+ ; Webkit 3+ ; Safari 3+ ; Opera 7+
		//	} //end if else
			var ab = new ArrayBuffer(byteString.length);
			var ia = new Uint8Array(ab);
			for(var i = 0; i < byteString.length; i++) {
				ia[i] = byteString.charCodeAt(i);
			} //end for
			blob = new Blob([ab], { type: mimeType });
		} catch(e) {
			alert('Failed to convert the B64Image to Blob. See console for more details ...');
			console.error(e);
			return;
		} //end try catch
		//--
		var fd = new FormData();
		fd.append('source', blob);
		fd.append('message', '' + postMessage);
		//--
		FB.login(
			function(response){
				if(response.authResponse) {
					var auth = response.authResponse;
					jQuery.ajax({
						url: 'https://graph.facebook.com/' + auth.userID + '/photos?access_token=' + auth.accessToken,
						type: 'POST',
						data: fd,
						processData: false,
						contentType: false,
						cache: false
					}).done(function(data){
						if(typeof fxDone === 'function') {
							fxDone(data);
						} //end if
					}).fail(function(xhr, status, data){
						if(typeof fxFail === 'function') {
							fxFail(xhr, status, data);
						} //end if
					});
				} //end if
			},
			{
				scope: String(FbSettings.perms),
				return_scopes: true,
				auth_type: 'rerequest'
			}
		);
		//--
	} //END FUNCTION


	//##### Data Model


	var storageGetItem = function(key, archive) {
		//--
		var value = getCookie(key);
		//--
		if(archive) {
			if(value) {
				value = String(SmartJS_Archiver_LZS.decompressFromBase64(String(value)));
			} //end if
		} //end if
		//--
		return value;
		//--
	} //END FUNCTION


	var storageSetItem = function(key, value, archive) {
		//--
		if(archive) {
			if(value) {
				value = String(SmartJS_Archiver_LZS.compressToBase64(String(value)));
			} //end if
		} //end if
		//--
		if(value) {
			setCookie(key, value, null, '/', FbSettings.domain);
		} else {
			deleteCookie(key, '/', FbSettings.domain);
		} //end if else
		//--
	} //END FUNCTION


	//##### Below functions can be supplied by Smart.Framework/Js.Api/SmartJS_BrowserUtils


	var getCookie = function(name) {
		//--
		return SmartJS_BrowserUtils.getCookie(name);
		//--
		/*
		var c;
		try {
			c = document.cookie.match(new RegExp('(^|;)\\s*' + String(name) + '=([^;\\s]*)'));
		} catch(err){
			console.error('NOTICE: BrowserUtils Failed to getCookie: ' + err);
		} //end try catch
		//--
		if(c && c.length >= 3) {
			var d = decodeURIComponent(c[2]) || ''; // fix to avoid working with null !!
			return String(d);
		} else {
			return ''; // fix to avoid working with null !!
		} //end if
		*/
		//--
	} //END FUNCTION


	var setCookie = function(name, value, days, path, domain, secure) {
		//--
		SmartJS_BrowserUtils.setCookie(name, value, days, path, domain, secure);
		//--
		/*
		if((typeof value == 'undefined') || (value == undefined) || (value == null)) {
			return; // bug fix (avoid to set null cookie)
		} //end if
		//--
		var d = new Date();
		//--
		if(days) {
			d.setTime(d.getTime() + (days * 8.64e7)); // now + days in milliseconds
		} //end if
		//--
		try {
			document.cookie = String(name) + '=' + encodeURIComponent(value) + (days ? ('; expires=' + d.toGMTString()) : '') + '; path=' + (path || '/') + (domain ? ('; domain=' + domain) : '') + (secure ? '; secure' : '');
		} catch(err){
			console.error('NOTICE: Failed to setCookie: ' + err);
		} //end try catch
		*/
		//--
	} //END FUNCTION


	var deleteCookie = function(name, path, domain, secure) {
		//--
		SmartJS_BrowserUtils.deleteCookie(name, path, domain, secure);
		//--
		/*
		setCookie(name, '', -1, path, domain, secure); // sets expiry to now - 1 day
		*/
		//--
	} //END FUNCTION


} //END CLASS

// END
