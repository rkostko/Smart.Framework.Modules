
// Twitter JS API Handler
// (c) 2012 - 2017 Radu I.
// v.170830

// Depends on: -

var TwitterApiHandler = new function() { // START CLASS

	this.init = function(kKey, kSecret, proxyUrl, callbackUrl, fxResponseOk, fxResponseNotOk) {

		var cb = new Codebird;

		if(proxyUrl) {
			cb.setUseProxy(true);
			cb.setProxy(String(proxyUrl));
		} //end if
		cb.setConsumerKey(String(kKey), String(kSecret));

		var oauth_token = localStorage.getItem("oauth_token");
		var oauth_token_secret = localStorage.getItem("oauth_token_secret");

		if(oauth_token && oauth_token_secret) {

			cb.setToken(oauth_token, oauth_token_secret);

		} else {

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
					if(reply) {
						//console.log("reply", reply)
						// stores it
						cb.setToken(reply.oauth_token, reply.oauth_token_secret);
						// save the token for the redirect (after user authorizes) ; we'll want to compare these values
						localStorage.setItem("oauth_token", reply.oauth_token);
						localStorage.setItem("oauth_token_secret", reply.oauth_token_secret);
						// gets the authorize screen URL
						cb.__call(
							'oauth_authorize',
							{},
							function(auth_url) {
								if(typeof fxResponseOk === 'function') {
									fxResponseOk(auth_url);
								} else {
									console.log('TwitterApiHandler: auth_url = ' + auth_url);
								} //end if else
								// JSFiddle doesn't open windows:
								// window.open(auth_url);
								//$("#authorize").attr("href", auth_url);
								// after user authorizes, user will be redirected to
								// http://127.0.0.1:49479/?oauth_token=[some_token]&oauth_verifier=[some_verifier]
								// then follow this section for coding that page:
								// https://github.com/jublonet/codebird-js#authenticating-using-a-callback-url-without-pin
							} //end function
						);
					} //end if
				} //end function
			);

		} //end if else

	} //END FUNCTION


} //END CLASS

// END
