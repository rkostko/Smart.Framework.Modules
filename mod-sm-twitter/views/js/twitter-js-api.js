
// Twitter JS API Handler
// (c) 2012 - 2017 Radu I.
// v.170830

// Depends on: codebird.js

var TwitterApiHandler = new function() { // START CLASS

	// :: static

	var _class = this; // self referencing

	var cb = null;

	this.init = function(kKey, kSecret, proxyUrl) { // always !!

		cb = new Codebird;
		if(proxyUrl) {
			cb.setUseProxy(true);
			cb.setProxy(String(proxyUrl));
		} //end if
		cb.setConsumerKey(String(kKey), String(kSecret));

	} //END FUNCTION


	this.unauthorize = function() {

		localStorage.setItem('weblogin_twitter_data', '');
		localStorage.setItem('weblogin_twitter_vf', '');
		localStorage.setItem('weblogin_twitter_sk', '');
		localStorage.setItem('weblogin_twitter_token', '');

	} //END FUNCTION


	this.authorize = function(callbackUrl, fxResponseOk, fxResponseNotOk) { // for main window

		var oauth_token = localStorage.getItem('weblogin_twitter_token');
		var oauth_token_secret = localStorage.getItem('weblogin_twitter_sk');
		var oauth_verifier = localStorage.getItem('weblogin_twitter_vf');
		var oauth_data = localStorage.getItem('weblogin_twitter_data');

		if(oauth_token && oauth_token_secret && oauth_verifier && oauth_data) {
			cb.setToken(String(oauth_token), String(oauth_token_secret));
		} else {
			requestPermissions(String(callbackUrl));
		} //end if else

	} //END FUNCTION


	this.finalizeauth = function(fxFinalizeOk, fxFinalizeNotOk) { // for redirection popup

		var urlParams = parseUrlParams();

		var oauth_token = '';
		if(urlParams.oauth_token) {
			oauth_token = String(urlParams.oauth_token);
			cb.setToken(oauth_token, String(localStorage.getItem('weblogin_twitter_sk'))); // oauth_token_secret
		} else {
			console.error('Twitter Js Api FinalizeAuth: No oAuth Token in URL');
			return;
		} //end if

		// get oauth_verifier from URL
		var oauth_verifier = '';
		if(urlParams.oauth_verifier) {
			oauth_verifier = String(urlParams.oauth_verifier);
			localStorage.setItem('weblogin_twitter_vf', String(oauth_verifier));
		} else {
			console.error('Twitter Js Api FinalizeAuth: No oAuth Verifier in URL');
			return;
		} //end if

		cb.__call(
			'oauth_accessToken',
			{
				oauth_verifier: String(oauth_verifier)
			},
			function (reply) {
				cb.setToken(reply.oauth_token, reply.oauth_token_secret);
				var twData = {};
				try {
					for(var k in reply) {
						k = String(k);
						if(k.substring(0,1) != '_') {
							var o = reply[k];
							twData[k] = o;
						} //end if
					} //end for
				} catch(err){}
				//console.log(twData);
				if(twData.httpstatus === 200 && twData.user_id && twData.oauth_token && twData.oauth_token_secret) {
					localStorage.setItem('weblogin_twitter_data', String(JSON.stringify(twData)));
					if(typeof fxFinalizeOk === 'function') {
						fxFinalizeOk(reply); // be sure to call popup close in fxFinalizeOk()
					} else {
						console.log('TwitterApiHandler: OK-reply =');
						console.log(reply);
						_class.closepopup();
					} //end if else
				} else {
					localStorage.setItem('weblogin_twitter_data', '');
					if(typeof fxFinalizeNotOk === 'function') {
						fxFinalizeNotOk(reply); // be sure to call popup close in fxFinalizeOk()
					} else {
						console.log('TwitterApiHandler: NOTOK-reply =');
						console.log(reply);
						_class.closepopup();
					} //end if else
				}
			} //end function
		);

	} //END FUNCTION


	this.closepopup = function() {

		if(window.opener) {
			try {
				self.close();
			} catch(err){}
		} //end if

	} //END FUNCTION


	this.getLoginData = function() {

		var oauth_data = String(localStorage.getItem('weblogin_twitter_data'));
		//console.log(oauth_data);
		if(!oauth_data) {
			return false;
		} //end if

		if(oauth_data) {
			try {
				oauth_data = JSON.parse(oauth_data);
				if(!oauth_data) {
					return false;
				} //end if
			} catch(err){}
		} //end if

		if(!oauth_data) {
			return false;
		} //end if

		if((!oauth_data.hasOwnProperty('oauth_token')) || (!oauth_data.hasOwnProperty('oauth_token_secret')) || (!oauth_data.hasOwnProperty('user_id')) || (!oauth_data.hasOwnProperty('httpstatus')) || (oauth_data.httpstatus !== 200)) {
			return false;
		} //end if

		return oauth_data;

	} //END FUNCTION


	this.postImage = function(imgB64Data, postMessage, fxDone, fxFail) {

		var oauth_data = _class.getLoginData();

		if(!oauth_data) {
			if(typeof fxFail === 'function') {
				fxFail(null, null, 'ERROR: Invalid Twitter Data');
			} else {
				console.log('ERROR: Invalid Twitter Data');
			} //end if else
			return;
		} //end if

		var oauth_token = String(oauth_data.oauth_token);
		var oauth_token_secret = String(oauth_data.oauth_token_secret);
		//console.log(oauth_token, oauth_token_secret);
		cb.setToken(oauth_token, oauth_token_secret);

		cb.__call(
			'media_upload',
			{
				'media': imgB64Data.split(',')[1]
			},
			function(reply, rate, err) {
				//console.log(reply, rate, err);
				if(!err) {
					cb.__call(
						'statuses_update',
						{
							'media_ids': String(reply.media_id_string),
							'status': String(postMessage)
						},
						function(reply) {
							if(typeof fxDone === 'function') {
								fxDone(reply, rate, err);
							} else {
								console.log('OK, media posted on Twitter ...')
								console.log(reply, rate, err);
							} //end if else
						} //end function
					);
				} else {
					if(typeof fxFail === 'function') {
						fxFail(reply, rate, err);
					} else {
						console.log('NOTOK, media was NOT posted on Twitter ...')
						console.log(reply, rate, err);
					} //end if else
				} //end if else
			} //end function
		);

	} //END FUNCTION


	//#####


	var requestPermissions = function(callbackUrl) {

		cb.__call(
			'oauth_requestToken',
			{
				oauth_callback: String(callbackUrl)
			},
			function(reply, rate, err) {
				if(err) {
					if(typeof fxResponseNotOk === 'function') {
						fxResponseNotOk(err);
					} else {
						console.error('TwitterApiHandler: Error response or timeout exceeded' + err.error);
					} //end if
				} //end if
				if(reply && reply.oauth_token && reply.oauth_token_secret) {
					//console.log('reply', reply)
					// stores it
					cb.setToken(String(reply.oauth_token), String(reply.oauth_token_secret));
					// save the token for the redirect (after user authorizes) ; we'll want to compare these values
					localStorage.setItem('weblogin_twitter_token', String(reply.oauth_token));
					localStorage.setItem('weblogin_twitter_sk', String(reply.oauth_token_secret));
					// gets the authorize screen URL
					// window.open(auth_url);
					// $('#authorize').attr('href', auth_url);
					// after user authorizes, user will be redirected to
					// http://127.0.0.1:49479/?oauth_token=[some_token]&oauth_verifier=[some_verifier]
					// then follow this section for coding that page:
					// https://github.com/jublonet/codebird-js#authenticating-using-a-callback-url-without-pin
					cb.__call(
						'oauth_authorize',
						{},
						function(auth_url) {
							var ref = window.open(String(auth_url), '_blank', 'location=no,width=600, height=400'); // redirection
							if(!ref) { // popup may be blocked
								alert('Twitter Api requires a PopUp to be opened. If you blocked PopUps you may allow this one in order to continue ...');
								return;
							} //end if
							var pollTimer = setInterval(function() {
								if(ref.closed) {
									clearInterval(pollTimer);
									if(typeof fxResponseOk === 'function') {
										fxResponseOk();
									} else {
										console.log('TwitterApiHandler: auth_url = ' + String(auth_url));
									} //end if else
									return false;
								}
							}, 500);
						} //end function
					);
				} else {
					console.error('Twitter Request: Invalid / Incomplete Reply ...');
				} //end if
			} //end function
		);

	} //END FUNCTION


	var parseUrlParams = function() {
		//--
		var result = {};
		//--
		if(!location.search) {
			return result; // Object
		} //end if
		var query = String(location.search.substr(1)); // get: 'param1=value1&param2=value%202' from '?param1=value1&param2=value%202'
		if(!query) {
			return result; // Object
		} //end if
		//--
		query.split('&').forEach(function(part) {
			var item = '';
			part = String(part);
			if(part) {
				item = part.split('=');
				result[String(item[0])] = String(decodeURIComponent(String(item[1])));
			} //end if
		});
		//--
		return result; // Object
		//--
	} //END FUNCTION


} //END CLASS


// END
