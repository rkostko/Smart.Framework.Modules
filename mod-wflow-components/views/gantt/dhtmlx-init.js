
// dhtmlx Objects v.3.2.1.uxm.180221
// License: GPL v2
// (c) 2015 Dinamenta, UAB.
// (c) 2015-2018 unix-world.org
/*
modified by unixman:
	- separed the dhtmlx object and functions from gantt (init) object
*/

if(typeof(window.dhx4) == "undefined") {

	window.dhx4 = {

		version: "4.1.3",

		skin: null, // allow to be set by user

		skinDetect: function(comp) {
			return {10:"dhx_skyblue",20:"dhx_web",30:"dhx_terrace"}[this.readFromCss(comp+"_skin_detect")]||null;
		},

		// read value from css
		readFromCss: function(className, property) {
			var t = document.createElement("DIV");
			t.className = className;
			if (document.body.firstChild != null) document.body.insertBefore(t, document.body.firstChild); else document.body.appendChild(t);
			var w = t[property||"offsetWidth"];
			t.parentNode.removeChild(t);
			t = null;
			return w;
		},

		// id manager
		lastId: 1,
		newId: function() {
			return this.lastId++;
		},

		// z-index manager
		zim: {
			data: {},
			step: 5,
			first: function() {
				return 100;
			},
			last: function() {
				var t = this.first();
				for (var a in this.data) t = Math.max(t, this.data[a]);
				return t;
			},
			reserve: function(id) {
				this.data[id] = this.last()+this.step;
				return this.data[id];
			},
			clear: function(id) {
				if (this.data[id] != null) {
					this.data[id] = null;
					delete this.data[id];
				}
			}
		},

		// string to boolean
		s2b: function(r) {
			if (typeof(r) == "string") r = r.toLowerCase();
			return (r == true || r == 1 || r == "true" || r == "1" || r == "yes" || r == "y");
		},

		// string to json
		s2j: function(s) {
			var obj = null;
			dhx4.temp = null;
			try { eval("dhx4.temp="+s); } catch(e) { dhx4.temp = null; }
			obj = dhx4.temp;
			dhx4.temp = null;
			return obj;
		},

		// absolute top/left position on screen
		absLeft: function(obj) {
			if (typeof(obj) == "string") obj = document.getElementById(obj);
			return this.getOffset(obj).left;
		},
		absTop: function(obj) {
			if (typeof(obj) == "string") obj = document.getElementById(obj);
			return this.getOffset(obj).top;
		},
		_aOfs: function(elem) {
			var top = 0, left = 0;
			while (elem) {
				top = top + parseInt(elem.offsetTop);
				left = left + parseInt(elem.offsetLeft);
				elem = elem.offsetParent;
			}
			return {top: top, left: left};
		},
		_aOfsRect: function(elem) {
			var box = elem.getBoundingClientRect();
			var body = document.body;
			var docElem = document.documentElement;
			var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop;
			var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
			var clientTop = docElem.clientTop || body.clientTop || 0;
			var clientLeft = docElem.clientLeft || body.clientLeft || 0;
			var top  = box.top +  scrollTop - clientTop;
			var left = box.left + scrollLeft - clientLeft;
			return { top: Math.round(top), left: Math.round(left) };
		},
		getOffset: function(elem) {
			if (elem.getBoundingClientRect) {
				return this._aOfsRect(elem);
			} else {
				return this._aOfs(elem);
			}
		},

		// copy obj
		_isObj: function(k) {
			return (k != null && typeof(k) == "object" && typeof(k.length) == "undefined");
		},
		_copyObj: function(r) {
			if (this._isObj(r)) {
				var t = {};
				for (var a in r) {
					if (typeof(r[a]) == "object" && r[a] != null) t[a] = this._copyObj(r[a]); else t[a] = r[a];
				}
			} else {
				var t = [];
				for (var a=0; a<r.length; a++) {
					if (typeof(r[a]) == "object" && r[a] != null) t[a] = this._copyObj(r[a]); else t[a] = r[a];
				}
			}
			return t;
		},

		// screen dim
		screenDim: function() {
			var isIE = window.dhx4.isIE; // (navigator.userAgent.indexOf("MSIE") >= 0);
			var dim = {};
			dim.left = document.body.scrollLeft;
			dim.right = dim.left+(window.innerWidth||document.body.clientWidth);
			dim.top = Math.max((isIE?document.documentElement:document.getElementsByTagName("html")[0]).scrollTop, document.body.scrollTop);
			dim.bottom = dim.top+(isIE?Math.max(document.documentElement.clientHeight||0,document.documentElement.offsetHeight||0):window.innerHeight);
			return dim;
		},

		// input/textarea range selection
		selectTextRange: function(inp, start, end) {

			inp = (typeof(inp)=="string"?document.getElementById(inp):inp);

			var len = inp.value.length;
			start = Math.max(Math.min(start, len), 0);
			end = Math.min(end, len);

			if (inp.setSelectionRange) {
				try {inp.setSelectionRange(start, end);} catch(e){}; // combo in grid under IE requires try/catch
			} else if (inp.createTextRange) {
				var range = inp.createTextRange();
				range.moveStart("character", start);
				range.moveEnd("character", end-len);
				try {range.select();} catch(e){};
			}
		},
		// transition
		transData: null,
		transDetect: function() {

			if (this.transData == null) {

				this.transData = {transProp: false, transEv: null};

				// transition, MozTransition, WebkitTransition, msTransition, OTransition
				var k = {
					"MozTransition": "transitionend",
					"WebkitTransition": "webkitTransitionEnd",
					"OTransition": "oTransitionEnd",
					"msTransition": "transitionend",
					"transition": "transitionend"
				};

				for (var a in k) {
					if (this.transData.transProp == false && document.documentElement.style[a] != null) {
						this.transData.transProp = a;
						this.transData.transEv = k[a];
					}
				}
				k = null;
			}

			return this.transData;

		},

		// xml parser
		_xmlNodeValue: function(node) {
			var value = "";
			for (var q=0; q<node.childNodes.length; q++) {
				value += (node.childNodes[q].nodeValue!=null?node.childNodes[q].nodeValue.toString().replace(/^[\n\r\s]{0,}/,"").replace(/[\n\r\s]{0,}$/,""):"");
			}
			return value;
		}

	};

	// browser
	window.dhx4.isFF = (navigator.userAgent.indexOf("Firefox") >= 0);
	window.dhx4.isIE = (navigator.userAgent.indexOf("MSIE") >= 0 || navigator.userAgent.indexOf("Trident") >= 0);
	window.dhx4.isChrome = (navigator.userAgent.indexOf("Chrome") >= 0);
	window.dhx4.isOpera = (navigator.userAgent.indexOf("Opera") >= 0);

};

if(typeof(window.dhx4._eventable) == "undefined") {

	window.dhx4._eventable = function(obj, mode) {

		if(mode == "clear") {
			obj.detachAllEvents();
			obj.dhxevs = null;
			obj.attachEvent = null;
			obj.detachEvent = null;
			obj.checkEvent = null;
			obj.callEvent = null;
			obj.detachAllEvents = null;
			obj = null;
			return;
		} //end if

		obj.dhxevs = { data: {} };

		obj.attachEvent = function(name, func) {
			name = String(name).toLowerCase();
			if (!this.dhxevs.data[name]) this.dhxevs.data[name] = {};
			var eventId = window.dhx4.newId();
			this.dhxevs.data[name][eventId] = func;
			return eventId;
		}

		obj.detachEvent = function(eventId) {
			for (var a in this.dhxevs.data) {
				var k = 0;
				for (var b in this.dhxevs.data[a]) {
					if (b == eventId) {
						this.dhxevs.data[a][b] = null;
						delete this.dhxevs.data[a][b];
					} else {
						k++;
					}
				}
				if (k == 0) {
					this.dhxevs.data[a] = null;
					delete this.dhxevs.data[a];
				}
			}
		}

		obj.checkEvent = function(name) {
			name = String(name).toLowerCase();
			return (this.dhxevs.data[name] != null);
		}

		obj.callEvent = function(name, params) {
			name = String(name).toLowerCase();
			if (this.dhxevs.data[name] == null) return true;
			var r = true;
			for (var a in this.dhxevs.data[name]) {
				r = this.dhxevs.data[name][a].apply(this, params) && r;
			}
			return r;
		}

		obj.detachAllEvents = function() {
			for (var a in this.dhxevs.data) {
				for (var b in this.dhxevs.data[a]) {
					this.dhxevs.data[a][b] = null;
					delete this.dhxevs.data[a][b];
				}
				this.dhxevs.data[a] = null;
				delete this.dhxevs.data[a];
			}
		}

		obj = null;
	};

	dhx4._eventable(dhx4);

};

if(typeof(window.dhtmlx) == "undefined") {

	window.dhtmlx = {
		extend: function(a, b){
			for(var key in b)
				if (!a[key])
					a[key]=b[key];
			return a;
		},
		extend_api: function(name,map,ext){
			var t = window[name];
			if (!t) return; //component not defined
			window[name] = function(obj) {
				if(obj && typeof obj == "object" && !obj.tagName) {
					var that = t.apply(this,(map._init?map._init(obj):arguments));
					//global settings
					for (var a in dhtmlx)
						if (map[a]) this[map[a]](dhtmlx[a]);
					//local settings
					for(var a in obj){
						if(map[a]) {
							this[map[a]](obj[a]);
						} else if(a.indexOf("on")===0) {
							this.attachEvent(a,obj[a]);
						} //end if else
					} //end for
				} else {
					var that = t.apply(this,arguments);
				} //end if else
				if(map._patch) {
					map._patch(this);
				} //end if
				return that||this;
			};
			window[name].prototype=t.prototype;
			if(ext) {
				dhtmlx.extend(window[name].prototype, ext);
			} //end if
		},
		url: function(str){
			if(str.indexOf("?") != -1) {
				return "&";
			} else {
				return "?";
			} //end if else
		}
	};
};

if(typeof(window.dhtmlxEvent) == "undefined") {
	function dhtmlxEvent(el, event, handler){
		if(el.addEventListener) {
			el.addEventListener(event, handler, false);
		} else if(el.attachEvent) {
			el.attachEvent("on"+event, handler);
		} //end if else
	} //end function
};

if(dhtmlxEvent.touchDelay == null) {
	dhtmlxEvent.touchDelay = 2000;
};

if(typeof(dhtmlxEvent.initTouch) == "undefined") {

	dhtmlxEvent.initTouch = function() {

		var longtouch;
		var target;
		var tx, ty;

		dhtmlxEvent(document.body, "touchstart", function(ev){
			target = ev.touches[0].target;
			tx = ev.touches[0].clientX;
			ty = ev.touches[0].clientY;
			longtouch = window.setTimeout(touch_event, dhtmlxEvent.touchDelay);
		});
		function touch_event(){
			if(target){
				var ev = document.createEvent("HTMLEvents"); // for chrome and firefox
				ev.initEvent("dblclick", true, true);
				target.dispatchEvent(ev);
				longtouch = target = null;
			}
		};
		dhtmlxEvent(document.body, "touchmove", function(ev){
			if(longtouch){
				if (Math.abs(ev.touches[0].clientX - tx) > 50 || Math.abs(ev.touches[0].clientY - ty) > 50 ){
					window.clearTimeout(longtouch);
					longtouch = target = false;
				}
			}
		});
		dhtmlxEvent(document.body, "touchend", function(ev){
			if(longtouch){
				window.clearTimeout(longtouch);
				longtouch = target = false;
			}
		});

		dhtmlxEvent.initTouch = function(){};

	};

};

if(!window.dhtmlx) {
	window.dhtmlx = {};
} //end if

(function(){

	var _dhx_msg_cfg = null;

	function callback(config, result) {
			var usercall = config.callback;
			modality(false);
			config.box.parentNode.removeChild(config.box);
			_dhx_msg_cfg = config.box = null;
			if(usercall) {
				usercall(result);
			}
	}

	function modal_key(e) {
		if(_dhx_msg_cfg) {
			e = e || event;
			var code = e.which || event.keyCode;
			if(dhtmlx.message.keyboard) {
				if(code == 13 || code == 32) {
					callback(_dhx_msg_cfg, true);
				}
				if(code == 27) {
					callback(_dhx_msg_cfg, false);
				}
			}
			if(e.preventDefault) {
				e.preventDefault();
			}
			return !(e.cancelBubble = true);
		}
	}

	if(document.attachEvent) {
		document.attachEvent("onkeydown", modal_key);
	} else {
		document.addEventListener("keydown", modal_key, true);
	}

	function modality(mode){
		if(!modality.cover){
			modality.cover = document.createElement("DIV");
			//necessary for IE only
			modality.cover.onkeydown = modal_key;
			modality.cover.className = "dhx_modal_cover";
			document.body.appendChild(modality.cover);
		}
		var height =  document.body.scrollHeight;
		modality.cover.style.display = mode ? "inline-block" : "none";
	}

	function button(text, result){
		var button_css = "dhtmlx_"+text.toLowerCase().replace(/ /g, "_")+"_button"; // dhtmlx_ok_button, dhtmlx_click_me_button
		return "<div class='dhtmlx_popup_button "+button_css+"' result='"+result+"' ><div>"+text+"</div></div>";
	}

	function info(text){

		if(!t.area){
			t.area = document.createElement("DIV");
			t.area.className = "dhtmlx_message_area";
			t.area.style[t.position]="5px";
			document.body.appendChild(t.area);
		}

		t.hide(text.id);
		var message = document.createElement("DIV");
		message.innerHTML = "<div>"+text.text+"</div>";
		message.className = "dhtmlx-info dhtmlx-" + text.type;
		message.onclick = function(){
			t.hide(text.id);
			text = null;
		};

		if(t.position == "bottom" && t.area.firstChild) {
			t.area.insertBefore(message,t.area.firstChild);
		} else {
			t.area.appendChild(message);
		}

		if(text.expire > 0) {
			t.timers[text.id]=window.setTimeout(function(){
				t.hide(text.id);
			}, text.expire);
		}

		t.pull[text.id] = message;
		message = null;

		return text.id;

	}

	function _boxStructure(config, ok, cancel) {

		var box = document.createElement("DIV");

		box.className = " dhtmlx_modal_box dhtmlx-"+config.type;
		box.setAttribute("dhxbox", 1);

		var inner = '';

		if(config.width) {
			box.style.width = config.width;
		}
		if(config.height) {
			box.style.height = config.height;
		}
		if(config.title) {
			inner+='<div class="dhtmlx_popup_title">'+config.title+'</div>';
		}
		inner+='<div class="dhtmlx_popup_text"><span>'+(config.content?'':config.text)+'</span></div><div  class="dhtmlx_popup_controls">';
		if(ok) {
			inner += button(config.ok || "OK", true);
		}
		if(cancel) {
			inner += button(config.cancel || "Cancel", false);
		}
		if(config.buttons) {
			for(var i=0; i<config.buttons.length; i++) {
				inner += button(config.buttons[i],i);
			}
		}
		inner += '</div>';
		box.innerHTML = inner;

		if(config.content){
			var node = config.content;
			if(typeof node == "string") {
				node = document.getElementById(node);
			}
			if(node.style.display == 'none') {
				node.style.display = "";
			}
			box.childNodes[config.title?1:0].appendChild(node);
		}

		box.onclick = function(e){
			e = e ||event;
			var source = e.target || e.srcElement;
			if(!source.className) {
				source = source.parentNode;
			}
			if(source.className.split(" ")[0] == "dhtmlx_popup_button") {
				var result = source.getAttribute("result");
				result = (result == "true") || (result == "false"?false:result);
				callback(config, result);
			}
		};

		config.box = box;

		if(ok || cancel) {
			_dhx_msg_cfg = config;
		}

		return box;

	}

	function _createBox(config, ok, cancel) {

		var box = config.tagName ? config : _boxStructure(config, ok, cancel);

		if(!config.hidden) {
			modality(true);
		}

		document.body.appendChild(box);
		var x = Math.abs(Math.floor(((window.innerWidth||document.documentElement.offsetWidth) - box.offsetWidth)/2));
		var y = Math.abs(Math.floor(((window.innerHeight||document.documentElement.offsetHeight) - box.offsetHeight)/2));

		if(config.position == "top") {
			box.style.top = "-3px";
		} else {
			box.style.top = y+'px';
		}

		box.style.left = x+'px';
		//necessary for IE only
		box.onkeydown = modal_key;

		box.focus();
		if(config.hidden) {
			dhtmlx.modalbox.hide(box);
		}

		return box;

	}

	function alertPopup(config){
		return _createBox(config, true, false);
	}

	function confirmPopup(config){
		return _createBox(config, true, true);
	}

	function boxPopup(config){
		return _createBox(config);
	}

	function box_params(text, type, callback){
		if(typeof text != "object") {
			if(typeof type == "function") {
				callback = type;
				type = "";
			}
			text = {text:text, type:type, callback:callback };
		}
		return text;
	}

	function params(text, type, expire, id){
		if(typeof text != "object") {
			text = {text:text, type:type, expire:expire, id:id};
		}
		text.id = text.id||t.uid();
		text.expire = text.expire||t.expire;
		return text;
	}

	dhtmlx.alert = function(){
		var text = box_params.apply(this, arguments);
		text.type = text.type || "confirm";
		return alertPopup(text);
	};

	dhtmlx.confirm = function(){
		var text = box_params.apply(this, arguments);
		text.type = text.type || "alert";
		return confirmPopup(text);
	};

	dhtmlx.modalbox = function(){
		var text = box_params.apply(this, arguments);
		text.type = text.type || "alert";
		return boxPopup(text);
	};

	dhtmlx.modalbox.hide = function(node){
		while (node && node.getAttribute && !node.getAttribute("dhxbox"))
			node = node.parentNode;
		if (node){
			node.parentNode.removeChild(node);
			modality(false);
		}
	};

	var t = dhtmlx.message = function(text, type, expire, id) {
		text = params.apply(this, arguments);
		text.type = text.type||"info";
		var subtype = text.type.split("-")[0];
		switch(subtype){
			case "alert":
				return alertPopup(text);
				break;
			case "confirm":
				return confirmPopup(text);
				break;
			case "modalbox":
				return boxPopup(text);
				break;
			default:
				return info(text);
		}
	};

	t.seed = (new Date()).valueOf();
	t.uid = function(){return t.seed++;};
	t.expire = 4000;
	t.keyboard = true;
	t.position = "top";
	t.pull = {};
	t.timers = {};
	t.hideAll = function() {
		for(var key in t.pull) {
			t.hide(key);
		} //end for
	};
	t.hide = function(id) {
		var obj = t.pull[id];
		if(obj && obj.parentNode) {
			window.setTimeout(function(){
				obj.parentNode.removeChild(obj);
				obj = null;
			},2000);
			obj.className+=" hidden";
			if(t.timers[id]) {
				window.clearTimeout(t.timers[id]);
			}
			delete t.pull[id];
		}
	};

})();

/*jsl:ignore*/
//import from dhtmlxcommon.js

function dhtmlxDetachEvent(el, event, handler){
	if(el.removeEventListener) {
		el.removeEventListener(event, handler, false);
	} else if(el.detachEvent) {
		el.detachEvent("on"+event, handler);
	}
} //END FUNCTION


/** Overrides event functionality.
 *  Includes all default methods from dhtmlx.common but adds _silentStart, _silendEnd
 *   @desc:
 *   @type: private
 */
dhtmlxEventable = function(obj) {
	obj._silent_mode = false;
	obj._silentStart = function() {
		this._silent_mode = true;
	};
	obj._silentEnd = function() {
		this._silent_mode = false;
	};
	obj.attachEvent = function(name, catcher, callObj){
		name='ev_'+name.toLowerCase();
		if(!this[name]) {
			this[name]=new this._eventCatcher(callObj||this);
		}
		return(name+':'+this[name].addEvent(catcher)); //return ID (event name & event ID)
	};
	obj.callEvent = function(name, arg0){
		if(this._silent_mode) {
			return true;
		}
		name='ev_'+name.toLowerCase();
		if(this[name]) {
			return this[name].apply(this, arg0);
		}
		return true;
	};
	obj.checkEvent = function(name){
		return (!!this['ev_'+name.toLowerCase()]);
	};
	obj._eventCatcher = function(obj){
		var dhx_catch = [];
		var z = function(){
			var res = true;
			for(var i=0; i<dhx_catch.length; i++){
				if(dhx_catch[i]) {
					var zr = dhx_catch[i].apply(obj, arguments);
					res = res && zr;
				}
			}
			return res;
		};
		z.addEvent = function(ev){
			if(typeof (ev) != "function") {
				ev = eval(ev);
			}
			if(ev) {
				return dhx_catch.push(ev) - 1;
			}
			return false;
		};
		z.removeEvent = function(id){
			dhx_catch[id] = null;
		};
		return z;
	};
	obj.detachEvent = function(id){
		if(id){
			var list = id.split(':'); //get EventName and ID
			this[list[0]].removeEvent(list[1]); //remove event
		}
	};
	obj.detachAllEvents = function(){
		for(var name in this){
			if(name.indexOf("ev_") === 0) {
				delete this[name];
			}
		}
	};
	obj = null;
};


/*jsl:end*/


dhtmlx.copy = function(object) {
	var i, t, result; // iterator, types array, result
	if (object && typeof object == "object") {
		result = {};
		t = [Array,Date,Number,String,Boolean];
		for (i=0; i<t.length; i++) {
			if (object instanceof t[i]) {
				result = i ? new t[i](object) : new t[i](); // first one is array
			}
		}
		for (i in object) {
			if (Object.prototype.hasOwnProperty.apply(object, [i])) {
				result[i] = dhtmlx.copy(object[i]);
			}
		}
	}
	return result || object;
};

dhtmlx.mixin = function(target, source, force){
	for(var f in source) {
		if((!target[f] || force)) {
			target[f]=source[f];
		}
	}
	return target;
};


dhtmlx.defined = function(obj) {
	return typeof(obj) != "undefined";
};

dhtmlx.uid = function() {
	if(!this._seed) {
		this._seed = (new Date()).valueOf();
	}
	this._seed++;
	return this._seed;
};


//creates function with specified "this" pointer
dhtmlx.bind = function(functor, object){
	if(functor.bind) {
		return functor.bind(object);
	} else {
		return function(){ return functor.apply(object,arguments); };
	}
};

// #END
