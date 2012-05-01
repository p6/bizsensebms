/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

/*
	This is an optimized version of Dojo, built for deployment and not for
	development. To get sources and documentation, please visit:

		http://dojotoolkit.org
*/

if(!dojo._hasResource["dojo.window"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.window"] = true;
dojo.provide("dojo.window");

dojo.window.getBox = function(){
	// summary:
	//		Returns the dimensions and scroll position of the viewable area of a browser window

	var scrollRoot = (dojo.doc.compatMode == 'BackCompat') ? dojo.body() : dojo.doc.documentElement;

	// get scroll position
	var scroll = dojo._docScroll(); // scrollRoot.scrollTop/Left should work
	return { w: scrollRoot.clientWidth, h: scrollRoot.clientHeight, l: scroll.x, t: scroll.y };
};

dojo.window.get = function(doc){
	// summary:
	// 		Get window object associated with document doc

	// In some IE versions (at least 6.0), document.parentWindow does not return a
	// reference to the real window object (maybe a copy), so we must fix it as well
	// We use IE specific execScript to attach the real window reference to
	// document._parentWindow for later use
	if(dojo.isIE && window !== document.parentWindow){
		/*
		In IE 6, only the variable "window" can be used to connect events (others
		may be only copies).
		*/
		doc.parentWindow.execScript("document._parentWindow = window;", "Javascript");
		//to prevent memory leak, unset it after use
		//another possibility is to add an onUnload handler which seems overkill to me (liucougar)
		var win = doc._parentWindow;
		doc._parentWindow = null;
		return win;	//	Window
	}

	return doc.parentWindow || doc.defaultView;	//	Window
};

dojo.window.scrollIntoView = function(/*DomNode*/ node, /*Object?*/ pos){
	// summary:
	//		Scroll the passed node into view, if it is not already.
	
	// don't rely on node.scrollIntoView working just because the function is there

	try{ // catch unexpected/unrecreatable errors (#7808) since we can recover using a semi-acceptable native method
		node = dojo.byId(node);
		var doc = node.ownerDocument || dojo.doc,
			body = doc.body || dojo.body(),
			html = doc.documentElement || body.parentNode,
			isIE = dojo.isIE, isWK = dojo.isWebKit;
		// if an untested browser, then use the native method
		if((!(dojo.isMoz || isIE || isWK || dojo.isOpera) || node == body || node == html) && (typeof node.scrollIntoView != "undefined")){
			node.scrollIntoView(false); // short-circuit to native if possible
			return;
		}
		var backCompat = doc.compatMode == 'BackCompat',
			clientAreaRoot = backCompat? body : html,
			scrollRoot = isWK ? body : clientAreaRoot,
			rootWidth = clientAreaRoot.clientWidth,
			rootHeight = clientAreaRoot.clientHeight,
			rtl = !dojo._isBodyLtr(),
			nodePos = pos || dojo.position(node),
			el = node.parentNode,
			isFixed = function(el){
				return ((isIE <= 6 || (isIE && backCompat))? false : (dojo.style(el, 'position').toLowerCase() == "fixed"));
			};
		if(isFixed(node)){ return; } // nothing to do

		while(el){
			if(el == body){ el = scrollRoot; }
			var elPos = dojo.position(el),
				fixedPos = isFixed(el);
	
			if(el == scrollRoot){
				elPos.w = rootWidth; elPos.h = rootHeight;
				if(scrollRoot == html && isIE && rtl){ elPos.x += scrollRoot.offsetWidth-elPos.w; } // IE workaround where scrollbar causes negative x
				if(elPos.x < 0 || !isIE){ elPos.x = 0; } // IE can have values > 0
				if(elPos.y < 0 || !isIE){ elPos.y = 0; }
			}else{
				var pb = dojo._getPadBorderExtents(el);
				elPos.w -= pb.w; elPos.h -= pb.h; elPos.x += pb.l; elPos.y += pb.t;
			}
	
			if(el != scrollRoot){ // body, html sizes already have the scrollbar removed
				var clientSize = el.clientWidth,
					scrollBarSize = elPos.w - clientSize;
				if(clientSize > 0 && scrollBarSize > 0){
					elPos.w = clientSize;
					if(isIE && rtl){ elPos.x += scrollBarSize; }
				}
				clientSize = el.clientHeight;
				scrollBarSize = elPos.h - clientSize;
				if(clientSize > 0 && scrollBarSize > 0){
					elPos.h = clientSize;
				}
			}
			if(fixedPos){ // bounded by viewport, not parents
				if(elPos.y < 0){
					elPos.h += elPos.y; elPos.y = 0;
				}
				if(elPos.x < 0){
					elPos.w += elPos.x; elPos.x = 0;
				}
				if(elPos.y + elPos.h > rootHeight){
					elPos.h = rootHeight - elPos.y;
				}
				if(elPos.x + elPos.w > rootWidth){
					elPos.w = rootWidth - elPos.x;
				}
			}
			// calculate overflow in all 4 directions
			var l = nodePos.x - elPos.x, // beyond left: < 0
				t = nodePos.y - Math.max(elPos.y, 0), // beyond top: < 0
				r = l + nodePos.w - elPos.w, // beyond right: > 0
				bot = t + nodePos.h - elPos.h; // beyond bottom: > 0
			if(r * l > 0){
				var s = Math[l < 0? "max" : "min"](l, r);
				nodePos.x += el.scrollLeft;
				el.scrollLeft += (isIE >= 8 && !backCompat && rtl)? -s : s;
				nodePos.x -= el.scrollLeft;
			}
			if(bot * t > 0){
				nodePos.y += el.scrollTop;
				el.scrollTop += Math[t < 0? "max" : "min"](t, bot);
				nodePos.y -= el.scrollTop;
			}
			el = (el != scrollRoot) && !fixedPos && el.parentNode;
		}	
	}catch(error){
		console.error('scrollIntoView: ' + error);
		node.scrollIntoView(false);
	}
};

}

if(!dojo._hasResource["dijit._base.manager"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.manager"] = true;
dojo.provide("dijit._base.manager");

dojo.declare("dijit.WidgetSet", null, {
	// summary:
	//		A set of widgets indexed by id. A default instance of this class is
	//		available as `dijit.registry`
	//
	// example:
	//		Create a small list of widgets:
	//		|	var ws = new dijit.WidgetSet();
	//		|	ws.add(dijit.byId("one"));
	//		| 	ws.add(dijit.byId("two"));
	//		|	// destroy both:
	//		|	ws.forEach(function(w){ w.destroy(); });
	//
	// example:
	//		Using dijit.registry:
	//		|	dijit.registry.forEach(function(w){ /* do something */ });

	constructor: function(){
		this._hash = {};
		this.length = 0;
	},

	add: function(/*dijit._Widget*/ widget){
		// summary:
		//		Add a widget to this list. If a duplicate ID is detected, a error is thrown.
		//
		// widget: dijit._Widget
		//		Any dijit._Widget subclass.
		if(this._hash[widget.id]){
			throw new Error("Tried to register widget with id==" + widget.id + " but that id is already registered");
		}
		this._hash[widget.id] = widget;
		this.length++;
	},

	remove: function(/*String*/ id){
		// summary:
		//		Remove a widget from this WidgetSet. Does not destroy the widget; simply
		//		removes the reference.
		if(this._hash[id]){
			delete this._hash[id];
			this.length--;
		}
	},

	forEach: function(/*Function*/ func, /* Object? */thisObj){
		// summary:
		//		Call specified function for each widget in this set.
		//
		// func:
		//		A callback function to run for each item. Is passed the widget, the index
		//		in the iteration, and the full hash, similar to `dojo.forEach`.
		//
		// thisObj:
		//		An optional scope parameter
		//
		// example:
		//		Using the default `dijit.registry` instance:
		//		|	dijit.registry.forEach(function(widget){
		//		|		console.log(widget.declaredClass);
		//		|	});
		//
		// returns:
		//		Returns self, in order to allow for further chaining.

		thisObj = thisObj || dojo.global;
		var i = 0, id;
		for(id in this._hash){
			func.call(thisObj, this._hash[id], i++, this._hash);
		}
		return this;	// dijit.WidgetSet
	},

	filter: function(/*Function*/ filter, /* Object? */thisObj){
		// summary:
		//		Filter down this WidgetSet to a smaller new WidgetSet
		//		Works the same as `dojo.filter` and `dojo.NodeList.filter`
		//
		// filter:
		//		Callback function to test truthiness. Is passed the widget
		//		reference and the pseudo-index in the object.
		//
		// thisObj: Object?
		//		Option scope to use for the filter function.
		//
		// example:
		//		Arbitrary: select the odd widgets in this list
		//		|	dijit.registry.filter(function(w, i){
		//		|		return i % 2 == 0;
		//		|	}).forEach(function(w){ /* odd ones */ });

		thisObj = thisObj || dojo.global;
		var res = new dijit.WidgetSet(), i = 0, id;
		for(id in this._hash){
			var w = this._hash[id];
			if(filter.call(thisObj, w, i++, this._hash)){
				res.add(w);
			}
		}
		return res; // dijit.WidgetSet
	},

	byId: function(/*String*/ id){
		// summary:
		//		Find a widget in this list by it's id.
		// example:
		//		Test if an id is in a particular WidgetSet
		//		| var ws = new dijit.WidgetSet();
		//		| ws.add(dijit.byId("bar"));
		//		| var t = ws.byId("bar") // returns a widget
		//		| var x = ws.byId("foo"); // returns undefined

		return this._hash[id];	// dijit._Widget
	},

	byClass: function(/*String*/ cls){
		// summary:
		//		Reduce this widgetset to a new WidgetSet of a particular `declaredClass`
		//
		// cls: String
		//		The Class to scan for. Full dot-notated string.
		//
		// example:
		//		Find all `dijit.TitlePane`s in a page:
		//		|	dijit.registry.byClass("dijit.TitlePane").forEach(function(tp){ tp.close(); });

		var res = new dijit.WidgetSet(), id, widget;
		for(id in this._hash){
			widget = this._hash[id];
			if(widget.declaredClass == cls){
				res.add(widget);
			}
		 }
		 return res; // dijit.WidgetSet
},

	toArray: function(){
		// summary:
		//		Convert this WidgetSet into a true Array
		//
		// example:
		//		Work with the widget .domNodes in a real Array
		//		|	dojo.map(dijit.registry.toArray(), function(w){ return w.domNode; });

		var ar = [];
		for(var id in this._hash){
			ar.push(this._hash[id]);
		}
		return ar;	// dijit._Widget[]
},

	map: function(/* Function */func, /* Object? */thisObj){
		// summary:
		//		Create a new Array from this WidgetSet, following the same rules as `dojo.map`
		// example:
		//		|	var nodes = dijit.registry.map(function(w){ return w.domNode; });
		//
		// returns:
		//		A new array of the returned values.
		return dojo.map(this.toArray(), func, thisObj); // Array
	},

	every: function(func, thisObj){
		// summary:
		// 		A synthetic clone of `dojo.every` acting explicitly on this WidgetSet
		//
		// func: Function
		//		A callback function run for every widget in this list. Exits loop
		//		when the first false return is encountered.
		//
		// thisObj: Object?
		//		Optional scope parameter to use for the callback

		thisObj = thisObj || dojo.global;
		var x = 0, i;
		for(i in this._hash){
			if(!func.call(thisObj, this._hash[i], x++, this._hash)){
				return false; // Boolean
			}
		}
		return true; // Boolean
	},

	some: function(func, thisObj){
		// summary:
		// 		A synthetic clone of `dojo.some` acting explictly on this WidgetSet
		//
		// func: Function
		//		A callback function run for every widget in this list. Exits loop
		//		when the first true return is encountered.
		//
		// thisObj: Object?
		//		Optional scope parameter to use for the callback

		thisObj = thisObj || dojo.global;
		var x = 0, i;
		for(i in this._hash){
			if(func.call(thisObj, this._hash[i], x++, this._hash)){
				return true; // Boolean
			}
		}
		return false; // Boolean
	}

});

(function(){

	/*=====
	dijit.registry = {
		// summary:
		//		A list of widgets on a page.
		// description:
		//		Is an instance of `dijit.WidgetSet`
	};
	=====*/
	dijit.registry = new dijit.WidgetSet();

	var hash = dijit.registry._hash,
		attr = dojo.attr,
		hasAttr = dojo.hasAttr,
		style = dojo.style;

	dijit.byId = function(/*String|dijit._Widget*/ id){
		// summary:
		//		Returns a widget by it's id, or if passed a widget, no-op (like dojo.byId())
		return typeof id == "string" ? hash[id] : id; // dijit._Widget
	};

	var _widgetTypeCtr = {};
	dijit.getUniqueId = function(/*String*/widgetType){
		// summary:
		//		Generates a unique id for a given widgetType
	
		var id;
		do{
			id = widgetType + "_" +
				(widgetType in _widgetTypeCtr ?
					++_widgetTypeCtr[widgetType] : _widgetTypeCtr[widgetType] = 0);
		}while(hash[id]);
		return dijit._scopeName == "dijit" ? id : dijit._scopeName + "_" + id; // String
	};
	
	dijit.findWidgets = function(/*DomNode*/ root){
		// summary:
		//		Search subtree under root returning widgets found.
		//		Doesn't search for nested widgets (ie, widgets inside other widgets).
	
		var outAry = [];
	
		function getChildrenHelper(root){
			for(var node = root.firstChild; node; node = node.nextSibling){
				if(node.nodeType == 1){
					var widgetId = node.getAttribute("widgetId");
					if(widgetId){
						outAry.push(hash[widgetId]);
					}else{
						getChildrenHelper(node);
					}
				}
			}
		}
	
		getChildrenHelper(root);
		return outAry;
	};
	
	dijit._destroyAll = function(){
		// summary:
		//		Code to destroy all widgets and do other cleanup on page unload
	
		// Clean up focus manager lingering references to widgets and nodes
		dijit._curFocus = null;
		dijit._prevFocus = null;
		dijit._activeStack = [];
	
		// Destroy all the widgets, top down
		dojo.forEach(dijit.findWidgets(dojo.body()), function(widget){
			// Avoid double destroy of widgets like Menu that are attached to <body>
			// even though they are logically children of other widgets.
			if(!widget._destroyed){
				if(widget.destroyRecursive){
					widget.destroyRecursive();
				}else if(widget.destroy){
					widget.destroy();
				}
			}
		});
	};
	
	if(dojo.isIE){
		// Only run _destroyAll() for IE because we think it's only necessary in that case,
		// and because it causes problems on FF.  See bug #3531 for details.
		dojo.addOnWindowUnload(function(){
			dijit._destroyAll();
		});
	}
	
	dijit.byNode = function(/*DOMNode*/ node){
		// summary:
		//		Returns the widget corresponding to the given DOMNode
		return hash[node.getAttribute("widgetId")]; // dijit._Widget
	};
	
	dijit.getEnclosingWidget = function(/*DOMNode*/ node){
		// summary:
		//		Returns the widget whose DOM tree contains the specified DOMNode, or null if
		//		the node is not contained within the DOM tree of any widget
		while(node){
			var id = node.getAttribute && node.getAttribute("widgetId");
			if(id){
				return hash[id];
			}
			node = node.parentNode;
		}
		return null;
	};

	var shown = (dijit._isElementShown = function(/*Element*/ elem){
		var s = style(elem);
		return (s.visibility != "hidden")
			&& (s.visibility != "collapsed")
			&& (s.display != "none")
			&& (attr(elem, "type") != "hidden");
	});
	
	dijit.hasDefaultTabStop = function(/*Element*/ elem){
		// summary:
		//		Tests if element is tab-navigable even without an explicit tabIndex setting
	
		// No explicit tabIndex setting, need to investigate node type
		switch(elem.nodeName.toLowerCase()){
			case "a":
				// An <a> w/out a tabindex is only navigable if it has an href
				return hasAttr(elem, "href");
			case "area":
			case "button":
			case "input":
			case "object":
			case "select":
			case "textarea":
				// These are navigable by default
				return true;
			case "iframe":
				// If it's an editor <iframe> then it's tab navigable.
				//TODO: feature detect "designMode" in elem.contentDocument?
				if(dojo.isMoz){
					try{
						return elem.contentDocument.designMode == "on";
					}catch(err){
						return false;
					}
				}else if(dojo.isWebKit){
					var doc = elem.contentDocument,
						body = doc && doc.body;
					return body && body.contentEditable == 'true';
				}else{
					// contentWindow.document isn't accessible within IE7/8
					// if the iframe.src points to a foreign url and this
					// page contains an element, that could get focus
					try{
						doc = elem.contentWindow.document;
						body = doc && doc.body;
						return body && body.firstChild && body.firstChild.contentEditable == 'true';
					}catch(e){
						return false;
					}
				}
			default:
				return elem.contentEditable == 'true';
		}
	};
	
	var isTabNavigable = (dijit.isTabNavigable = function(/*Element*/ elem){
		// summary:
		//		Tests if an element is tab-navigable
	
		// TODO: convert (and rename method) to return effective tabIndex; will save time in _getTabNavigable()
		if(attr(elem, "disabled")){
			return false;
		}else if(hasAttr(elem, "tabIndex")){
			// Explicit tab index setting
			return attr(elem, "tabIndex") >= 0; // boolean
		}else{
			// No explicit tabIndex setting, so depends on node type
			return dijit.hasDefaultTabStop(elem);
		}
	});

	dijit._getTabNavigable = function(/*DOMNode*/ root){
		// summary:
		//		Finds descendants of the specified root node.
		//
		// description:
		//		Finds the following descendants of the specified root node:
		//		* the first tab-navigable element in document order
		//		  without a tabIndex or with tabIndex="0"
		//		* the last tab-navigable element in document order
		//		  without a tabIndex or with tabIndex="0"
		//		* the first element in document order with the lowest
		//		  positive tabIndex value
		//		* the last element in document order with the highest
		//		  positive tabIndex value
		var first, last, lowest, lowestTabindex, highest, highestTabindex;
		var walkTree = function(/*DOMNode*/parent){
			dojo.query("> *", parent).forEach(function(child){
				// Skip hidden elements, and also non-HTML elements (those in custom namespaces) in IE,
				// since show() invokes getAttribute("type"), which crash on VML nodes in IE.
				if((dojo.isIE && child.scopeName!=="HTML") || !shown(child)){
					return;
				}

				if(isTabNavigable(child)){
					var tabindex = attr(child, "tabIndex");
					if(!hasAttr(child, "tabIndex") || tabindex == 0){
						if(!first){ first = child; }
						last = child;
					}else if(tabindex > 0){
						if(!lowest || tabindex < lowestTabindex){
							lowestTabindex = tabindex;
							lowest = child;
						}
						if(!highest || tabindex >= highestTabindex){
							highestTabindex = tabindex;
							highest = child;
						}
					}
				}
				if(child.nodeName.toUpperCase() != 'SELECT'){
					walkTree(child);
				}
			});
		};
		if(shown(root)){ walkTree(root) }
		return { first: first, last: last, lowest: lowest, highest: highest };
	}
	dijit.getFirstInTabbingOrder = function(/*String|DOMNode*/ root){
		// summary:
		//		Finds the descendant of the specified root node
		//		that is first in the tabbing order
		var elems = dijit._getTabNavigable(dojo.byId(root));
		return elems.lowest ? elems.lowest : elems.first; // DomNode
	};
	
	dijit.getLastInTabbingOrder = function(/*String|DOMNode*/ root){
		// summary:
		//		Finds the descendant of the specified root node
		//		that is last in the tabbing order
		var elems = dijit._getTabNavigable(dojo.byId(root));
		return elems.last ? elems.last : elems.highest; // DomNode
	};
	
	/*=====
	dojo.mixin(dijit, {
		// defaultDuration: Integer
		//		The default animation speed (in ms) to use for all Dijit
		//		transitional animations, unless otherwise specified
		//		on a per-instance basis. Defaults to 200, overrided by
		//		`djConfig.defaultDuration`
		defaultDuration: 200
	});
	=====*/
	
	dijit.defaultDuration = dojo.config["defaultDuration"] || 200;

})();

}

if(!dojo._hasResource["dijit._base.focus"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.focus"] = true;
dojo.provide("dijit._base.focus");


	// for dijit.isTabNavigable()

// summary:
//		These functions are used to query or set the focus and selection.
//
//		Also, they trace when widgets become activated/deactivated,
//		so that the widget can fire _onFocus/_onBlur events.
//		"Active" here means something similar to "focused", but
//		"focus" isn't quite the right word because we keep track of
//		a whole stack of "active" widgets.  Example: ComboButton --> Menu -->
//		MenuItem.  The onBlur event for ComboButton doesn't fire due to focusing
//		on the Menu or a MenuItem, since they are considered part of the
//		ComboButton widget.  It only happens when focus is shifted
//		somewhere completely different.

dojo.mixin(dijit, {
	// _curFocus: DomNode
	//		Currently focused item on screen
	_curFocus: null,

	// _prevFocus: DomNode
	//		Previously focused item on screen
	_prevFocus: null,

	isCollapsed: function(){
		// summary:
		//		Returns true if there is no text selected
		return dijit.getBookmark().isCollapsed;
	},

	getBookmark: function(){
		// summary:
		//		Retrieves a bookmark that can be used with moveToBookmark to return to the same range
		var bm, rg, tg, sel = dojo.doc.selection, cf = dijit._curFocus;

		if(dojo.global.getSelection){
			//W3C Range API for selections.
			sel = dojo.global.getSelection();
			if(sel){
				if(sel.isCollapsed){
					tg = cf? cf.tagName : "";
					if(tg){
						//Create a fake rangelike item to restore selections.
						tg = tg.toLowerCase();
						if(tg == "textarea" ||
								(tg == "input" && (!cf.type || cf.type.toLowerCase() == "text"))){
							sel = {
								start: cf.selectionStart,
								end: cf.selectionEnd,
								node: cf,
								pRange: true
							};
							return {isCollapsed: (sel.end <= sel.start), mark: sel}; //Object.
						}
					}
					bm = {isCollapsed:true};
				}else{
					rg = sel.getRangeAt(0);
					bm = {isCollapsed: false, mark: rg.cloneRange()};
				}
			}
		}else if(sel){
			// If the current focus was a input of some sort and no selection, don't bother saving
			// a native bookmark.  This is because it causes issues with dialog/page selection restore.
			// So, we need to create psuedo bookmarks to work with.
			tg = cf ? cf.tagName : "";
			tg = tg.toLowerCase();
			if(cf && tg && (tg == "button" || tg == "textarea" || tg == "input")){
				if(sel.type && sel.type.toLowerCase() == "none"){
					return {
						isCollapsed: true,
						mark: null
					}
				}else{
					rg = sel.createRange();
					return {
						isCollapsed: rg.text && rg.text.length?false:true,
						mark: {
							range: rg,
							pRange: true
						}
					};
				}
			}
			bm = {};

			//'IE' way for selections.
			try{
				// createRange() throws exception when dojo in iframe
				//and nothing selected, see #9632
				rg = sel.createRange();
				bm.isCollapsed = !(sel.type == 'Text' ? rg.htmlText.length : rg.length);
			}catch(e){
				bm.isCollapsed = true;
				return bm;
			}
			if(sel.type.toUpperCase() == 'CONTROL'){
				if(rg.length){
					bm.mark=[];
					var i=0,len=rg.length;
					while(i<len){
						bm.mark.push(rg.item(i++));
					}
				}else{
					bm.isCollapsed = true;
					bm.mark = null;
				}
			}else{
				bm.mark = rg.getBookmark();
			}
		}else{
			console.warn("No idea how to store the current selection for this browser!");
		}
		return bm; // Object
	},

	moveToBookmark: function(/*Object*/bookmark){
		// summary:
		//		Moves current selection to a bookmark
		// bookmark:
		//		This should be a returned object from dijit.getBookmark()

		var _doc = dojo.doc,
			mark = bookmark.mark;
		if(mark){
			if(dojo.global.getSelection){
				//W3C Rangi API (FF, WebKit, Opera, etc)
				var sel = dojo.global.getSelection();
				if(sel && sel.removeAllRanges){
					if(mark.pRange){
						var r = mark;
						var n = r.node;
						n.selectionStart = r.start;
						n.selectionEnd = r.end;
					}else{
						sel.removeAllRanges();
						sel.addRange(mark);
					}
				}else{
					console.warn("No idea how to restore selection for this browser!");
				}
			}else if(_doc.selection && mark){
				//'IE' way.
				var rg;
				if(mark.pRange){
					rg = mark.range;
				}else if(dojo.isArray(mark)){
					rg = _doc.body.createControlRange();
					//rg.addElement does not have call/apply method, so can not call it directly
					//rg is not available in "range.addElement(item)", so can't use that either
					dojo.forEach(mark, function(n){
						rg.addElement(n);
					});
				}else{
					rg = _doc.body.createTextRange();
					rg.moveToBookmark(mark);
				}
				rg.select();
			}
		}
	},

	getFocus: function(/*Widget?*/ menu, /*Window?*/ openedForWindow){
		// summary:
		//		Called as getFocus(), this returns an Object showing the current focus
		//		and selected text.
		//
		//		Called as getFocus(widget), where widget is a (widget representing) a button
		//		that was just pressed, it returns where focus was before that button
		//		was pressed.   (Pressing the button may have either shifted focus to the button,
		//		or removed focus altogether.)   In this case the selected text is not returned,
		//		since it can't be accurately determined.
		//
		// menu: dijit._Widget or {domNode: DomNode} structure
		//		The button that was just pressed.  If focus has disappeared or moved
		//		to this button, returns the previous focus.  In this case the bookmark
		//		information is already lost, and null is returned.
		//
		// openedForWindow:
		//		iframe in which menu was opened
		//
		// returns:
		//		A handle to restore focus/selection, to be passed to `dijit.focus`
		var node = !dijit._curFocus || (menu && dojo.isDescendant(dijit._curFocus, menu.domNode)) ? dijit._prevFocus : dijit._curFocus;
		return {
			node: node,
			bookmark: (node == dijit._curFocus) && dojo.withGlobal(openedForWindow || dojo.global, dijit.getBookmark),
			openedForWindow: openedForWindow
		}; // Object
	},

	focus: function(/*Object || DomNode */ handle){
		// summary:
		//		Sets the focused node and the selection according to argument.
		//		To set focus to an iframe's content, pass in the iframe itself.
		// handle:
		//		object returned by get(), or a DomNode

		if(!handle){ return; }

		var node = "node" in handle ? handle.node : handle,		// because handle is either DomNode or a composite object
			bookmark = handle.bookmark,
			openedForWindow = handle.openedForWindow,
			collapsed = bookmark ? bookmark.isCollapsed : false;

		// Set the focus
		// Note that for iframe's we need to use the <iframe> to follow the parentNode chain,
		// but we need to set focus to iframe.contentWindow
		if(node){
			var focusNode = (node.tagName.toLowerCase() == "iframe") ? node.contentWindow : node;
			if(focusNode && focusNode.focus){
				try{
					// Gecko throws sometimes if setting focus is impossible,
					// node not displayed or something like that
					focusNode.focus();
				}catch(e){/*quiet*/}
			}
			dijit._onFocusNode(node);
		}

		// set the selection
		// do not need to restore if current selection is not empty
		// (use keyboard to select a menu item) or if previous selection was collapsed
		// as it may cause focus shift (Esp in IE).
		if(bookmark && dojo.withGlobal(openedForWindow || dojo.global, dijit.isCollapsed) && !collapsed){
			if(openedForWindow){
				openedForWindow.focus();
			}
			try{
				dojo.withGlobal(openedForWindow || dojo.global, dijit.moveToBookmark, null, [bookmark]);
			}catch(e2){
				/*squelch IE internal error, see http://trac.dojotoolkit.org/ticket/1984 */
			}
		}
	},

	// _activeStack: dijit._Widget[]
	//		List of currently active widgets (focused widget and it's ancestors)
	_activeStack: [],

	registerIframe: function(/*DomNode*/ iframe){
		// summary:
		//		Registers listeners on the specified iframe so that any click
		//		or focus event on that iframe (or anything in it) is reported
		//		as a focus/click event on the <iframe> itself.
		// description:
		//		Currently only used by editor.
		// returns:
		//		Handle to pass to unregisterIframe()
		return dijit.registerWin(iframe.contentWindow, iframe);
	},

	unregisterIframe: function(/*Object*/ handle){
		// summary:
		//		Unregisters listeners on the specified iframe created by registerIframe.
		//		After calling be sure to delete or null out the handle itself.
		// handle:
		//		Handle returned by registerIframe()

		dijit.unregisterWin(handle);
	},

	registerWin: function(/*Window?*/targetWindow, /*DomNode?*/ effectiveNode){
		// summary:
		//		Registers listeners on the specified window (either the main
		//		window or an iframe's window) to detect when the user has clicked somewhere
		//		or focused somewhere.
		// description:
		//		Users should call registerIframe() instead of this method.
		// targetWindow:
		//		If specified this is the window associated with the iframe,
		//		i.e. iframe.contentWindow.
		// effectiveNode:
		//		If specified, report any focus events inside targetWindow as
		//		an event on effectiveNode, rather than on evt.target.
		// returns:
		//		Handle to pass to unregisterWin()

		// TODO: make this function private in 2.0; Editor/users should call registerIframe(),

		var mousedownListener = function(evt){
			dijit._justMouseDowned = true;
			setTimeout(function(){ dijit._justMouseDowned = false; }, 0);
			
			// workaround weird IE bug where the click is on an orphaned node
			// (first time clicking a Select/DropDownButton inside a TooltipDialog)
			if(dojo.isIE && evt && evt.srcElement && evt.srcElement.parentNode == null){
				return;
			}

			dijit._onTouchNode(effectiveNode || evt.target || evt.srcElement, "mouse");
		};
		//dojo.connect(targetWindow, "onscroll", ???);

		// Listen for blur and focus events on targetWindow's document.
		// IIRC, I'm using attachEvent() rather than dojo.connect() because focus/blur events don't bubble
		// through dojo.connect(), and also maybe to catch the focus events early, before onfocus handlers
		// fire.
		// Connect to <html> (rather than document) on IE to avoid memory leaks, but document on other browsers because
		// (at least for FF) the focus event doesn't fire on <html> or <body>.
		var doc = dojo.isIE ? targetWindow.document.documentElement : targetWindow.document;
		if(doc){
			if(dojo.isIE){
				doc.attachEvent('onmousedown', mousedownListener);
				var activateListener = function(evt){
					// IE reports that nodes like <body> have gotten focus, even though they have tabIndex=-1,
					// Should consider those more like a mouse-click than a focus....
					if(evt.srcElement.tagName.toLowerCase() != "#document" &&
						dijit.isTabNavigable(evt.srcElement)){
						dijit._onFocusNode(effectiveNode || evt.srcElement);
					}else{
						dijit._onTouchNode(effectiveNode || evt.srcElement);
					}
				};
				doc.attachEvent('onactivate', activateListener);
				var deactivateListener =  function(evt){
					dijit._onBlurNode(effectiveNode || evt.srcElement);
				};
				doc.attachEvent('ondeactivate', deactivateListener);

				return function(){
					doc.detachEvent('onmousedown', mousedownListener);
					doc.detachEvent('onactivate', activateListener);
					doc.detachEvent('ondeactivate', deactivateListener);
					doc = null;	// prevent memory leak (apparent circular reference via closure)
				};
			}else{
				doc.addEventListener('mousedown', mousedownListener, true);
				var focusListener = function(evt){
					dijit._onFocusNode(effectiveNode || evt.target);
				};
				doc.addEventListener('focus', focusListener, true);
				var blurListener = function(evt){
					dijit._onBlurNode(effectiveNode || evt.target);
				};
				doc.addEventListener('blur', blurListener, true);

				return function(){
					doc.removeEventListener('mousedown', mousedownListener, true);
					doc.removeEventListener('focus', focusListener, true);
					doc.removeEventListener('blur', blurListener, true);
					doc = null;	// prevent memory leak (apparent circular reference via closure)
				};
			}
		}
	},

	unregisterWin: function(/*Handle*/ handle){
		// summary:
		//		Unregisters listeners on the specified window (either the main
		//		window or an iframe's window) according to handle returned from registerWin().
		//		After calling be sure to delete or null out the handle itself.

		// Currently our handle is actually a function
		handle && handle();
	},

	_onBlurNode: function(/*DomNode*/ node){
		// summary:
		// 		Called when focus leaves a node.
		//		Usually ignored, _unless_ it *isn't* follwed by touching another node,
		//		which indicates that we tabbed off the last field on the page,
		//		in which case every widget is marked inactive
		dijit._prevFocus = dijit._curFocus;
		dijit._curFocus = null;

		if(dijit._justMouseDowned){
			// the mouse down caused a new widget to be marked as active; this blur event
			// is coming late, so ignore it.
			return;
		}

		// if the blur event isn't followed by a focus event then mark all widgets as inactive.
		if(dijit._clearActiveWidgetsTimer){
			clearTimeout(dijit._clearActiveWidgetsTimer);
		}
		dijit._clearActiveWidgetsTimer = setTimeout(function(){
			delete dijit._clearActiveWidgetsTimer;
			dijit._setStack([]);
			dijit._prevFocus = null;
		}, 100);
	},

	_onTouchNode: function(/*DomNode*/ node, /*String*/ by){
		// summary:
		//		Callback when node is focused or mouse-downed
		// node:
		//		The node that was touched.
		// by:
		//		"mouse" if the focus/touch was caused by a mouse down event

		// ignore the recent blurNode event
		if(dijit._clearActiveWidgetsTimer){
			clearTimeout(dijit._clearActiveWidgetsTimer);
			delete dijit._clearActiveWidgetsTimer;
		}

		// compute stack of active widgets (ex: ComboButton --> Menu --> MenuItem)
		var newStack=[];
		try{
			while(node){
				var popupParent = dojo.attr(node, "dijitPopupParent");
				if(popupParent){
					node=dijit.byId(popupParent).domNode;
				}else if(node.tagName && node.tagName.toLowerCase() == "body"){
					// is this the root of the document or just the root of an iframe?
					if(node === dojo.body()){
						// node is the root of the main document
						break;
					}
					// otherwise, find the iframe this node refers to (can't access it via parentNode,
					// need to do this trick instead). window.frameElement is supported in IE/FF/Webkit
					node=dojo.window.get(node.ownerDocument).frameElement;
				}else{
					// if this node is the root node of a widget, then add widget id to stack,
					// except ignore clicks on disabled widgets (actually focusing a disabled widget still works,
					// to support MenuItem)
					var id = node.getAttribute && node.getAttribute("widgetId"),
						widget = id && dijit.byId(id);
					if(widget && !(by == "mouse" && widget.get("disabled"))){
						newStack.unshift(id);
					}
					node=node.parentNode;
				}
			}
		}catch(e){ /* squelch */ }

		dijit._setStack(newStack, by);
	},

	_onFocusNode: function(/*DomNode*/ node){
		// summary:
		//		Callback when node is focused

		if(!node){
			return;
		}

		if(node.nodeType == 9){
			// Ignore focus events on the document itself.  This is here so that
			// (for example) clicking the up/down arrows of a spinner
			// (which don't get focus) won't cause that widget to blur. (FF issue)
			return;
		}

		dijit._onTouchNode(node);

		if(node == dijit._curFocus){ return; }
		if(dijit._curFocus){
			dijit._prevFocus = dijit._curFocus;
		}
		dijit._curFocus = node;
		dojo.publish("focusNode", [node]);
	},

	_setStack: function(/*String[]*/ newStack, /*String*/ by){
		// summary:
		//		The stack of active widgets has changed.  Send out appropriate events and records new stack.
		// newStack:
		//		array of widget id's, starting from the top (outermost) widget
		// by:
		//		"mouse" if the focus/touch was caused by a mouse down event

		var oldStack = dijit._activeStack;
		dijit._activeStack = newStack;

		// compare old stack to new stack to see how many elements they have in common
		for(var nCommon=0; nCommon<Math.min(oldStack.length, newStack.length); nCommon++){
			if(oldStack[nCommon] != newStack[nCommon]){
				break;
			}
		}

		var widget;
		// for all elements that have gone out of focus, send blur event
		for(var i=oldStack.length-1; i>=nCommon; i--){
			widget = dijit.byId(oldStack[i]);
			if(widget){
				widget._focused = false;
				widget._hasBeenBlurred = true;
				if(widget._onBlur){
					widget._onBlur(by);
				}
				dojo.publish("widgetBlur", [widget, by]);
			}
		}

		// for all element that have come into focus, send focus event
		for(i=nCommon; i<newStack.length; i++){
			widget = dijit.byId(newStack[i]);
			if(widget){
				widget._focused = true;
				if(widget._onFocus){
					widget._onFocus(by);
				}
				dojo.publish("widgetFocus", [widget, by]);
			}
		}
	}
});

// register top window and all the iframes it contains
dojo.addOnLoad(function(){
	var handle = dijit.registerWin(window);
	if(dojo.isIE){
		dojo.addOnWindowUnload(function(){
			dijit.unregisterWin(handle);
			handle = null;
		})
	}
});

}

if(!dojo._hasResource["dojo.AdapterRegistry"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.AdapterRegistry"] = true;
dojo.provide("dojo.AdapterRegistry");

dojo.AdapterRegistry = function(/*Boolean?*/ returnWrappers){
	//	summary:
	//		A registry to make contextual calling/searching easier.
	//	description:
	//		Objects of this class keep list of arrays in the form [name, check,
	//		wrap, directReturn] that are used to determine what the contextual
	//		result of a set of checked arguments is. All check/wrap functions
	//		in this registry should be of the same arity.
	//	example:
	//	|	// create a new registry
	//	|	var reg = new dojo.AdapterRegistry();
	//	|	reg.register("handleString",
	//	|		dojo.isString,
	//	|		function(str){
	//	|			// do something with the string here
	//	|		}
	//	|	);
	//	|	reg.register("handleArr",
	//	|		dojo.isArray,
	//	|		function(arr){
	//	|			// do something with the array here
	//	|		}
	//	|	);
	//	|
	//	|	// now we can pass reg.match() *either* an array or a string and
	//	|	// the value we pass will get handled by the right function
	//	|	reg.match("someValue"); // will call the first function
	//	|	reg.match(["someValue"]); // will call the second

	this.pairs = [];
	this.returnWrappers = returnWrappers || false; // Boolean
}

dojo.extend(dojo.AdapterRegistry, {
	register: function(/*String*/ name, /*Function*/ check, /*Function*/ wrap, /*Boolean?*/ directReturn, /*Boolean?*/ override){
		//	summary: 
		//		register a check function to determine if the wrap function or
		//		object gets selected
		//	name:
		//		a way to identify this matcher.
		//	check:
		//		a function that arguments are passed to from the adapter's
		//		match() function.  The check function should return true if the
		//		given arguments are appropriate for the wrap function.
		//	directReturn:
		//		If directReturn is true, the value passed in for wrap will be
		//		returned instead of being called. Alternately, the
		//		AdapterRegistry can be set globally to "return not call" using
		//		the returnWrappers property. Either way, this behavior allows
		//		the registry to act as a "search" function instead of a
		//		function interception library.
		//	override:
		//		If override is given and true, the check function will be given
		//		highest priority. Otherwise, it will be the lowest priority
		//		adapter.
		this.pairs[((override) ? "unshift" : "push")]([name, check, wrap, directReturn]);
	},

	match: function(/* ... */){
		// summary:
		//		Find an adapter for the given arguments. If no suitable adapter
		//		is found, throws an exception. match() accepts any number of
		//		arguments, all of which are passed to all matching functions
		//		from the registered pairs.
		for(var i = 0; i < this.pairs.length; i++){
			var pair = this.pairs[i];
			if(pair[1].apply(this, arguments)){
				if((pair[3])||(this.returnWrappers)){
					return pair[2];
				}else{
					return pair[2].apply(this, arguments);
				}
			}
		}
		throw new Error("No match found");
	},

	unregister: function(name){
		// summary: Remove a named adapter from the registry

		// FIXME: this is kind of a dumb way to handle this. On a large
		// registry this will be slow-ish and we can use the name as a lookup
		// should we choose to trade memory for speed.
		for(var i = 0; i < this.pairs.length; i++){
			var pair = this.pairs[i];
			if(pair[0] == name){
				this.pairs.splice(i, 1);
				return true;
			}
		}
		return false;
	}
});

}

if(!dojo._hasResource["dijit._base.place"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.place"] = true;
dojo.provide("dijit._base.place");





dijit.getViewport = function(){
	// summary:
	//		Returns the dimensions and scroll position of the viewable area of a browser window

	return dojo.window.getBox();
};

/*=====
dijit.__Position = function(){
	// x: Integer
	//		horizontal coordinate in pixels, relative to document body
	// y: Integer
	//		vertical coordinate in pixels, relative to document body

	thix.x = x;
	this.y = y;
}
=====*/


dijit.placeOnScreen = function(
	/* DomNode */			node,
	/* dijit.__Position */	pos,
	/* String[] */			corners,
	/* dijit.__Position? */	padding){
	// summary:
	//		Positions one of the node's corners at specified position
	//		such that node is fully visible in viewport.
	// description:
	//		NOTE: node is assumed to be absolutely or relatively positioned.
	//	pos:
	//		Object like {x: 10, y: 20}
	//	corners:
	//		Array of Strings representing order to try corners in, like ["TR", "BL"].
	//		Possible values are:
	//			* "BL" - bottom left
	//			* "BR" - bottom right
	//			* "TL" - top left
	//			* "TR" - top right
	//	padding:
	//		set padding to put some buffer around the element you want to position.
	// example:
	//		Try to place node's top right corner at (10,20).
	//		If that makes node go (partially) off screen, then try placing
	//		bottom left corner at (10,20).
	//	|	placeOnScreen(node, {x: 10, y: 20}, ["TR", "BL"])

	var choices = dojo.map(corners, function(corner){
		var c = { corner: corner, pos: {x:pos.x,y:pos.y} };
		if(padding){
			c.pos.x += corner.charAt(1) == 'L' ? padding.x : -padding.x;
			c.pos.y += corner.charAt(0) == 'T' ? padding.y : -padding.y;
		}
		return c;
	});

	return dijit._place(node, choices);
}

dijit._place = function(/*DomNode*/ node, /* Array */ choices, /* Function */ layoutNode){
	// summary:
	//		Given a list of spots to put node, put it at the first spot where it fits,
	//		of if it doesn't fit anywhere then the place with the least overflow
	// choices: Array
	//		Array of elements like: {corner: 'TL', pos: {x: 10, y: 20} }
	//		Above example says to put the top-left corner of the node at (10,20)
	// layoutNode: Function(node, aroundNodeCorner, nodeCorner)
	//		for things like tooltip, they are displayed differently (and have different dimensions)
	//		based on their orientation relative to the parent.   This adjusts the popup based on orientation.

	// get {x: 10, y: 10, w: 100, h:100} type obj representing position of
	// viewport over document
	var view = dojo.window.getBox();

	// This won't work if the node is inside a <div style="position: relative">,
	// so reattach it to dojo.doc.body.   (Otherwise, the positioning will be wrong
	// and also it might get cutoff)
	if(!node.parentNode || String(node.parentNode.tagName).toLowerCase() != "body"){
		dojo.body().appendChild(node);
	}

	var best = null;
	dojo.some(choices, function(choice){
		var corner = choice.corner;
		var pos = choice.pos;

		// configure node to be displayed in given position relative to button
		// (need to do this in order to get an accurate size for the node, because
		// a tooltips size changes based on position, due to triangle)
		if(layoutNode){
			layoutNode(node, choice.aroundCorner, corner);
		}

		// get node's size
		var style = node.style;
		var oldDisplay = style.display;
		var oldVis = style.visibility;
		style.visibility = "hidden";
		style.display = "";
		var mb = dojo.marginBox(node);
		style.display = oldDisplay;
		style.visibility = oldVis;

		// coordinates and size of node with specified corner placed at pos,
		// and clipped by viewport
		var startX = Math.max(view.l, corner.charAt(1) == 'L' ? pos.x : (pos.x - mb.w)),
			startY = Math.max(view.t, corner.charAt(0) == 'T' ? pos.y : (pos.y - mb.h)),
			endX = Math.min(view.l + view.w, corner.charAt(1) == 'L' ? (startX + mb.w) : pos.x),
			endY = Math.min(view.t + view.h, corner.charAt(0) == 'T' ? (startY + mb.h) : pos.y),
			width = endX - startX,
			height = endY - startY,
			overflow = (mb.w - width) + (mb.h - height);

		if(best == null || overflow < best.overflow){
			best = {
				corner: corner,
				aroundCorner: choice.aroundCorner,
				x: startX,
				y: startY,
				w: width,
				h: height,
				overflow: overflow
			};
		}
		return !overflow;
	});

	node.style.left = best.x + "px";
	node.style.top = best.y + "px";
	if(best.overflow && layoutNode){
		layoutNode(node, best.aroundCorner, best.corner);
	}
	return best;
}

dijit.placeOnScreenAroundNode = function(
	/* DomNode */		node,
	/* DomNode */		aroundNode,
	/* Object */		aroundCorners,
	/* Function? */		layoutNode){

	// summary:
	//		Position node adjacent or kitty-corner to aroundNode
	//		such that it's fully visible in viewport.
	//
	// description:
	//		Place node such that corner of node touches a corner of
	//		aroundNode, and that node is fully visible.
	//
	// aroundCorners:
	//		Ordered list of pairs of corners to try matching up.
	//		Each pair of corners is represented as a key/value in the hash,
	//		where the key corresponds to the aroundNode's corner, and
	//		the value corresponds to the node's corner:
	//
	//	|	{ aroundNodeCorner1: nodeCorner1, aroundNodeCorner2: nodeCorner2, ...}
	//
	//		The following strings are used to represent the four corners:
	//			* "BL" - bottom left
	//			* "BR" - bottom right
	//			* "TL" - top left
	//			* "TR" - top right
	//
	// layoutNode: Function(node, aroundNodeCorner, nodeCorner)
	//		For things like tooltip, they are displayed differently (and have different dimensions)
	//		based on their orientation relative to the parent.   This adjusts the popup based on orientation.
	//
	// example:
	//	|	dijit.placeOnScreenAroundNode(node, aroundNode, {'BL':'TL', 'TR':'BR'});
	//		This will try to position node such that node's top-left corner is at the same position
	//		as the bottom left corner of the aroundNode (ie, put node below
	//		aroundNode, with left edges aligned).  If that fails it will try to put
	// 		the bottom-right corner of node where the top right corner of aroundNode is
	//		(ie, put node above aroundNode, with right edges aligned)
	//

	// get coordinates of aroundNode
	aroundNode = dojo.byId(aroundNode);
	var oldDisplay = aroundNode.style.display;
	aroundNode.style.display="";
	// #3172: use the slightly tighter border box instead of marginBox
	var aroundNodePos = dojo.position(aroundNode, true);
	aroundNode.style.display=oldDisplay;

	// place the node around the calculated rectangle
	return dijit._placeOnScreenAroundRect(node,
		aroundNodePos.x, aroundNodePos.y, aroundNodePos.w, aroundNodePos.h,	// rectangle
		aroundCorners, layoutNode);
};

/*=====
dijit.__Rectangle = function(){
	// x: Integer
	//		horizontal offset in pixels, relative to document body
	// y: Integer
	//		vertical offset in pixels, relative to document body
	// width: Integer
	//		width in pixels
	// height: Integer
	//		height in pixels

	this.x = x;
	this.y = y;
	this.width = width;
	this.height = height;
}
=====*/


dijit.placeOnScreenAroundRectangle = function(
	/* DomNode */			node,
	/* dijit.__Rectangle */	aroundRect,
	/* Object */			aroundCorners,
	/* Function */			layoutNode){

	// summary:
	//		Like dijit.placeOnScreenAroundNode(), except that the "around"
	//		parameter is an arbitrary rectangle on the screen (x, y, width, height)
	//		instead of a dom node.

	return dijit._placeOnScreenAroundRect(node,
		aroundRect.x, aroundRect.y, aroundRect.width, aroundRect.height,	// rectangle
		aroundCorners, layoutNode);
};

dijit._placeOnScreenAroundRect = function(
	/* DomNode */		node,
	/* Number */		x,
	/* Number */		y,
	/* Number */		width,
	/* Number */		height,
	/* Object */		aroundCorners,
	/* Function */		layoutNode){

	// summary:
	//		Like dijit.placeOnScreenAroundNode(), except it accepts coordinates
	//		of a rectangle to place node adjacent to.

	// TODO: combine with placeOnScreenAroundRectangle()

	// Generate list of possible positions for node
	var choices = [];
	for(var nodeCorner in aroundCorners){
		choices.push( {
			aroundCorner: nodeCorner,
			corner: aroundCorners[nodeCorner],
			pos: {
				x: x + (nodeCorner.charAt(1) == 'L' ? 0 : width),
				y: y + (nodeCorner.charAt(0) == 'T' ? 0 : height)
			}
		});
	}

	return dijit._place(node, choices, layoutNode);
};

dijit.placementRegistry= new dojo.AdapterRegistry();
dijit.placementRegistry.register("node",
	function(n, x){
		return typeof x == "object" &&
			typeof x.offsetWidth != "undefined" && typeof x.offsetHeight != "undefined";
	},
	dijit.placeOnScreenAroundNode);
dijit.placementRegistry.register("rect",
	function(n, x){
		return typeof x == "object" &&
			"x" in x && "y" in x && "width" in x && "height" in x;
	},
	dijit.placeOnScreenAroundRectangle);

dijit.placeOnScreenAroundElement = function(
	/* DomNode */		node,
	/* Object */		aroundElement,
	/* Object */		aroundCorners,
	/* Function */		layoutNode){

	// summary:
	//		Like dijit.placeOnScreenAroundNode(), except it accepts an arbitrary object
	//		for the "around" argument and finds a proper processor to place a node.

	return dijit.placementRegistry.match.apply(dijit.placementRegistry, arguments);
};

dijit.getPopupAroundAlignment = function(/*Array*/ position, /*Boolean*/ leftToRight){
	// summary:
	//		Transforms the passed array of preferred positions into a format suitable for passing as the aroundCorners argument to dijit.placeOnScreenAroundElement.
	//
	// position: String[]
	//		This variable controls the position of the drop down.
	//		It's an array of strings with the following values:
	//
	//			* before: places drop down to the left of the target node/widget, or to the right in
	//			  the case of RTL scripts like Hebrew and Arabic
	//			* after: places drop down to the right of the target node/widget, or to the left in
	//			  the case of RTL scripts like Hebrew and Arabic
	//			* above: drop down goes above target node
	//			* below: drop down goes below target node
	//
	//		The list is positions is tried, in order, until a position is found where the drop down fits
	//		within the viewport.
	//
	// leftToRight: Boolean
	//		Whether the popup will be displaying in leftToRight mode.
	//
	var align = {};
	dojo.forEach(position, function(pos){
		switch(pos){
			case "after":
				align[leftToRight ? "BR" : "BL"] = leftToRight ? "BL" : "BR";
				break;
			case "before":
				align[leftToRight ? "BL" : "BR"] = leftToRight ? "BR" : "BL";
				break;
			case "below":
				// first try to align left borders, next try to align right borders (or reverse for RTL mode)
				align[leftToRight ? "BL" : "BR"] = leftToRight ? "TL" : "TR";
				align[leftToRight ? "BR" : "BL"] = leftToRight ? "TR" : "TL";
				break;
			case "above":
			default:
				// first try to align left borders, next try to align right borders (or reverse for RTL mode)
				align[leftToRight ? "TL" : "TR"] = leftToRight ? "BL" : "BR";
				align[leftToRight ? "TR" : "TL"] = leftToRight ? "BR" : "BL";
				break;
		}
	});
	return align;
};

}

if(!dojo._hasResource["dijit._base.window"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.window"] = true;
dojo.provide("dijit._base.window");



dijit.getDocumentWindow = function(doc){
	return dojo.window.get(doc);
};

}

if(!dojo._hasResource["dijit._base.popup"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.popup"] = true;
dojo.provide("dijit._base.popup");





/*=====
dijit.popup.__OpenArgs = function(){
	// popup: Widget
	//		widget to display
	// parent: Widget
	//		the button etc. that is displaying this popup
	// around: DomNode
	//		DOM node (typically a button); place popup relative to this node.  (Specify this *or* "x" and "y" parameters.)
	// x: Integer
	//		Absolute horizontal position (in pixels) to place node at.  (Specify this *or* "around" parameter.)
	// y: Integer
	//		Absolute vertical position (in pixels) to place node at.  (Specify this *or* "around" parameter.)
	// orient: Object|String
	//		When the around parameter is specified, orient should be an
	//		ordered list of tuples of the form (around-node-corner, popup-node-corner).
	//		dijit.popup.open() tries to position the popup according to each tuple in the list, in order,
	//		until the popup appears fully within the viewport.
	//
	//		The default value is {BL:'TL', TL:'BL'}, which represents a list of two tuples:
	//			1. (BL, TL)
	//			2. (TL, BL)
	//		where BL means "bottom left" and "TL" means "top left".
	//		So by default, it first tries putting the popup below the around node, left-aligning them,
	//		and then tries to put it above the around node, still left-aligning them.   Note that the
	//		default is horizontally reversed when in RTL mode.
	//
	//		When an (x,y) position is specified rather than an around node, orient is either
	//		"R" or "L".  R (for right) means that it tries to put the popup to the right of the mouse,
	//		specifically positioning the popup's top-right corner at the mouse position, and if that doesn't
	//		fit in the viewport, then it tries, in order, the bottom-right corner, the top left corner,
	//		and the top-right corner.
	// onCancel: Function
	//		callback when user has canceled the popup by
	//			1. hitting ESC or
	//			2. by using the popup widget's proprietary cancel mechanism (like a cancel button in a dialog);
	//			   i.e. whenever popupWidget.onCancel() is called, args.onCancel is called
	// onClose: Function
	//		callback whenever this popup is closed
	// onExecute: Function
	//		callback when user "executed" on the popup/sub-popup by selecting a menu choice, etc. (top menu only)
	// padding: dijit.__Position
	//		adding a buffer around the opening position. This is only useful when around is not set.
	this.popup = popup;
	this.parent = parent;
	this.around = around;
	this.x = x;
	this.y = y;
	this.orient = orient;
	this.onCancel = onCancel;
	this.onClose = onClose;
	this.onExecute = onExecute;
	this.padding = padding;
}
=====*/

dijit.popup = {
	// summary:
	//		This singleton is used to show/hide widgets as popups.

	// _stack: dijit._Widget[]
	//		Stack of currently popped up widgets.
	//		(someone opened _stack[0], and then it opened _stack[1], etc.)
	_stack: [],
	
	// _beginZIndex: Number
	//		Z-index of the first popup.   (If first popup opens other
	//		popups they get a higher z-index.)
	_beginZIndex: 1000,

	_idGen: 1,

	moveOffScreen: function(/*DomNode*/ node){
		// summary:
		//		Initialization for nodes that will be used as popups
		//
		// description:
		//		Puts node inside a wrapper <div>, and
		//		positions wrapper div off screen, but not display:none, so that
		//		the widget doesn't appear in the page flow and/or cause a blank
		//		area at the bottom of the viewport (making scrollbar longer), but
		//		initialization of contained widgets works correctly

		var wrapper = node.parentNode;

		// Create a wrapper widget for when this node (in the future) will be used as a popup.
		// This is done early because of IE bugs where creating/moving DOM nodes causes focus
		// to go wonky, see tests/robot/Toolbar.html to reproduce
		if(!wrapper || !dojo.hasClass(wrapper, "dijitPopup")){
			wrapper = dojo.create("div",{
				"class":"dijitPopup",
				style:{
					visibility:"hidden",
					top: "-9999px"
				}
			}, dojo.body());
			dijit.setWaiRole(wrapper, "presentation");
			wrapper.appendChild(node);
		}


		var s = node.style;
		s.display = "";
		s.visibility = "";
		s.position = "";
		s.top = "0px";

		dojo.style(wrapper, {
			visibility: "hidden",
			// prevent transient scrollbar causing misalign (#5776), and initial flash in upper left (#10111)
			top: "-9999px"
		});
	},

	getTopPopup: function(){
		// summary:
		//		Compute the closest ancestor popup that's *not* a child of another popup.
		//		Ex: For a TooltipDialog with a button that spawns a tree of menus, find the popup of the button.
		var stack = this._stack;
		for(var pi=stack.length-1; pi > 0 && stack[pi].parent === stack[pi-1].widget; pi--){
			/* do nothing, just trying to get right value for pi */
		}
		return stack[pi];
	},

	open: function(/*dijit.popup.__OpenArgs*/ args){
		// summary:
		//		Popup the widget at the specified position
		//
		// example:
		//		opening at the mouse position
		//		|		dijit.popup.open({popup: menuWidget, x: evt.pageX, y: evt.pageY});
		//
		// example:
		//		opening the widget as a dropdown
		//		|		dijit.popup.open({parent: this, popup: menuWidget, around: this.domNode, onClose: function(){...}});
		//
		//		Note that whatever widget called dijit.popup.open() should also listen to its own _onBlur callback
		//		(fired from _base/focus.js) to know that focus has moved somewhere else and thus the popup should be closed.

		var stack = this._stack,
			widget = args.popup,
			orient = args.orient || (
				(args.parent ? args.parent.isLeftToRight() : dojo._isBodyLtr()) ?
				{'BL':'TL', 'BR':'TR', 'TL':'BL', 'TR':'BR'} :
				{'BR':'TR', 'BL':'TL', 'TR':'BR', 'TL':'BL'}
			),
			around = args.around,
			id = (args.around && args.around.id) ? (args.around.id+"_dropdown") : ("popup_"+this._idGen++);


		// The wrapper may have already been created, but in case it wasn't, create here
		var wrapper = widget.domNode.parentNode;
		if(!wrapper || !dojo.hasClass(wrapper, "dijitPopup")){
			this.moveOffScreen(widget.domNode);
			wrapper = widget.domNode.parentNode;
		}

		dojo.attr(wrapper, {
			id: id,
			style: {
				zIndex: this._beginZIndex + stack.length
			},
			"class": "dijitPopup " + (widget.baseClass || widget["class"] || "").split(" ")[0] +"Popup",
			dijitPopupParent: args.parent ? args.parent.id : ""
		});

		if(dojo.isIE || dojo.isMoz){
			var iframe = wrapper.childNodes[1];
			if(!iframe){
				iframe = new dijit.BackgroundIframe(wrapper);
			}
		}

		// position the wrapper node and make it visible
		var best = around ?
			dijit.placeOnScreenAroundElement(wrapper, around, orient, widget.orient ? dojo.hitch(widget, "orient") : null) :
			dijit.placeOnScreen(wrapper, args, orient == 'R' ? ['TR','BR','TL','BL'] : ['TL','BL','TR','BR'], args.padding);

		wrapper.style.visibility = "visible";
		widget.domNode.style.visibility = "visible";	// counteract effects from _HasDropDown

		var handlers = [];

		// provide default escape and tab key handling
		// (this will work for any widget, not just menu)
		handlers.push(dojo.connect(wrapper, "onkeypress", this, function(evt){
			if(evt.charOrCode == dojo.keys.ESCAPE && args.onCancel){
				dojo.stopEvent(evt);
				args.onCancel();
			}else if(evt.charOrCode === dojo.keys.TAB){
				dojo.stopEvent(evt);
				var topPopup = this.getTopPopup();
				if(topPopup && topPopup.onCancel){
					topPopup.onCancel();
				}
			}
		}));

		// watch for cancel/execute events on the popup and notify the caller
		// (for a menu, "execute" means clicking an item)
		if(widget.onCancel){
			handlers.push(dojo.connect(widget, "onCancel", args.onCancel));
		}

		handlers.push(dojo.connect(widget, widget.onExecute ? "onExecute" : "onChange", this, function(){
			var topPopup = this.getTopPopup();
			if(topPopup && topPopup.onExecute){
				topPopup.onExecute();
			}
		}));

		stack.push({
			wrapper: wrapper,
			iframe: iframe,
			widget: widget,
			parent: args.parent,
			onExecute: args.onExecute,
			onCancel: args.onCancel,
 			onClose: args.onClose,
			handlers: handlers
		});

		if(widget.onOpen){
			// TODO: in 2.0 standardize onShow() (used by StackContainer) and onOpen() (used here)
			widget.onOpen(best);
		}

		return best;
	},

	close: function(/*dijit._Widget*/ popup){
		// summary:
		//		Close specified popup and any popups that it parented

		var stack = this._stack;

		// Basically work backwards from the top of the stack closing popups
		// until we hit the specified popup, but IIRC there was some issue where closing
		// a popup would cause others to close too.  Thus if we are trying to close B in [A,B,C]
		// closing C might close B indirectly and then the while() condition will run where stack==[A]...
		// so the while condition is constructed defensively.
		while(dojo.some(stack, function(elem){return elem.widget == popup;})){
			var top = stack.pop(),
				wrapper = top.wrapper,
				iframe = top.iframe,
				widget = top.widget,
				onClose = top.onClose;

			if(widget.onClose){
				// TODO: in 2.0 standardize onHide() (used by StackContainer) and onClose() (used here)
				widget.onClose();
			}
			dojo.forEach(top.handlers, dojo.disconnect);

			// Move the widget plus it's wrapper off screen, unless it has already been destroyed in above onClose() etc.
			if(widget && widget.domNode){
				this.moveOffScreen(widget.domNode);
			}else{
				dojo.destroy(wrapper);
			}
                        
			if(onClose){
				onClose();
			}
		}
	}
};

dijit._frames = new function(){
	// summary:
	//		cache of iframes
	var queue = [];

	this.pop = function(){
		var iframe;
		if(queue.length){
			iframe = queue.pop();
			iframe.style.display="";
		}else{
			if(dojo.isIE){
				var burl = dojo.config["dojoBlankHtmlUrl"] || (dojo.moduleUrl("dojo", "resources/blank.html")+"") || "javascript:\"\"";
				var html="<iframe src='" + burl + "'"
					+ " style='position: absolute; left: 0px; top: 0px;"
					+ "z-index: -1; filter:Alpha(Opacity=\"0\");'>";
				iframe = dojo.doc.createElement(html);
			}else{
			 	iframe = dojo.create("iframe");
				iframe.src = 'javascript:""';
				iframe.className = "dijitBackgroundIframe";
				dojo.style(iframe, "opacity", 0.1);
			}
			iframe.tabIndex = -1; // Magic to prevent iframe from getting focus on tab keypress - as style didnt work.
			dijit.setWaiRole(iframe,"presentation");
		}
		return iframe;
	};

	this.push = function(iframe){
		iframe.style.display="none";
		queue.push(iframe);
	}
}();


dijit.BackgroundIframe = function(/* DomNode */node){
	// summary:
	//		For IE/FF z-index schenanigans. id attribute is required.
	//
	// description:
	//		new dijit.BackgroundIframe(node)
	//			Makes a background iframe as a child of node, that fills
	//			area (and position) of node

	if(!node.id){ throw new Error("no id"); }
	if(dojo.isIE || dojo.isMoz){
		var iframe = dijit._frames.pop();
		node.appendChild(iframe);
		if(dojo.isIE<7){
			this.resize(node);
			this._conn = dojo.connect(node, 'onresize', this, function(){
				this.resize(node);
			});
		}else{
			dojo.style(iframe, {
				width: '100%',
				height: '100%'
			});
		}
		this.iframe = iframe;
	}
};

dojo.extend(dijit.BackgroundIframe, {
	resize: function(node){
		// summary:
		// 		resize the iframe so its the same size as node
		// description:
		//		this function is a no-op in all browsers except
		//		IE6, which does not support 100% width/height 
		//		of absolute positioned iframes
		if(this.iframe && dojo.isIE<7){
			dojo.style(this.iframe, {
				width: node.offsetWidth + 'px',
				height: node.offsetHeight + 'px'
			});
		}
	},
	destroy: function(){
		// summary:
		//		destroy the iframe
		if(this._conn){
			dojo.disconnect(this._conn);
			this._conn = null;
		}
		if(this.iframe){
			dijit._frames.push(this.iframe);
			delete this.iframe;
		}
	}
});

}

if(!dojo._hasResource["dijit._base.scroll"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.scroll"] = true;
dojo.provide("dijit._base.scroll");



dijit.scrollIntoView = function(/*DomNode*/ node, /*Object?*/ pos){
	// summary:
	//		Scroll the passed node into view, if it is not already.
	//		Deprecated, use `dojo.window.scrollIntoView` instead.
	
	dojo.window.scrollIntoView(node, pos);
};

}

if(!dojo._hasResource["dojo.uacss"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.uacss"] = true;
dojo.provide("dojo.uacss");

(function(){
	// summary:
	//		Applies pre-set CSS classes to the top-level HTML node, based on:
	// 			- browser (ex: dj_ie)
	//			- browser version (ex: dj_ie6)
	//			- box model (ex: dj_contentBox)
	//			- text direction (ex: dijitRtl)
	//
	//		In addition, browser, browser version, and box model are
	//		combined with an RTL flag when browser text is RTL.  ex: dj_ie-rtl.

	var d = dojo,
		html = d.doc.documentElement,
		ie = d.isIE,
		opera = d.isOpera,
		maj = Math.floor,
		ff = d.isFF,
		boxModel = d.boxModel.replace(/-/,''),

		classes = {
			dj_ie: ie,
			dj_ie6: maj(ie) == 6,
			dj_ie7: maj(ie) == 7,
			dj_ie8: maj(ie) == 8,
			dj_quirks: d.isQuirks,
			dj_iequirks: ie && d.isQuirks,

			// NOTE: Opera not supported by dijit
			dj_opera: opera,

			dj_khtml: d.isKhtml,

			dj_webkit: d.isWebKit,
			dj_safari: d.isSafari,
			dj_chrome: d.isChrome,

			dj_gecko: d.isMozilla,
			dj_ff3: maj(ff) == 3
		}; // no dojo unsupported browsers

	classes["dj_" + boxModel] = true;

	// apply browser, browser version, and box model class names
	var classStr = "";
	for(var clz in classes){
		if(classes[clz]){
			classStr += clz + " ";
		}
	}
	html.className = d.trim(html.className + " " + classStr);

	// If RTL mode, then add dj_rtl flag plus repeat existing classes with -rtl extension.
	// We can't run the code below until the <body> tag has loaded (so we can check for dir=rtl).  
	// Unshift() is to run sniff code before the parser.
	dojo._loaders.unshift(function(){
		if(!dojo._isBodyLtr()){
			var rtlClassStr = "dj_rtl dijitRtl " + classStr.replace(/ /g, "-rtl ")
			html.className = d.trim(html.className + " " + rtlClassStr);
		}
	});
})();

}

if(!dojo._hasResource["dijit._base.sniff"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.sniff"] = true;
// summary:
//		Applies pre-set CSS classes to the top-level HTML node, see
//		`dojo.uacss` for details.
//
//		Simply doing a require on this module will
//		establish this CSS.  Modified version of Morris' CSS hack.

dojo.provide("dijit._base.sniff");



}

if(!dojo._hasResource["dijit._base.typematic"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.typematic"] = true;
dojo.provide("dijit._base.typematic");

dijit.typematic = {
	// summary:
	//		These functions are used to repetitively call a user specified callback
	//		method when a specific key or mouse click over a specific DOM node is
	//		held down for a specific amount of time.
	//		Only 1 such event is allowed to occur on the browser page at 1 time.

	_fireEventAndReload: function(){
		this._timer = null;
		this._callback(++this._count, this._node, this._evt);
		
		// Schedule next event, timer is at most minDelay (default 10ms) to avoid
		// browser overload (particularly avoiding starving DOH robot so it never gets to send a mouseup)
		this._currentTimeout = Math.max(
			this._currentTimeout < 0 ? this._initialDelay :
				(this._subsequentDelay > 1 ? this._subsequentDelay : Math.round(this._currentTimeout * this._subsequentDelay)),
			this._minDelay);
		this._timer = setTimeout(dojo.hitch(this, "_fireEventAndReload"), this._currentTimeout);
	},

	trigger: function(/*Event*/ evt, /*Object*/ _this, /*DOMNode*/ node, /*Function*/ callback, /*Object*/ obj, /*Number*/ subsequentDelay, /*Number*/ initialDelay, /*Number?*/ minDelay){
		// summary:
		//		Start a timed, repeating callback sequence.
		//		If already started, the function call is ignored.
		//		This method is not normally called by the user but can be
		//		when the normal listener code is insufficient.
		// evt:
		//		key or mouse event object to pass to the user callback
		// _this:
		//		pointer to the user's widget space.
		// node:
		//		the DOM node object to pass the the callback function
		// callback:
		//		function to call until the sequence is stopped called with 3 parameters:
		// count:
		//		integer representing number of repeated calls (0..n) with -1 indicating the iteration has stopped
		// node:
		//		the DOM node object passed in
		// evt:
		//		key or mouse event object
		// obj:
		//		user space object used to uniquely identify each typematic sequence
		// subsequentDelay (optional):
		//		if > 1, the number of milliseconds until the 3->n events occur
		//		or else the fractional time multiplier for the next event's delay, default=0.9
		// initialDelay (optional):
		//		the number of milliseconds until the 2nd event occurs, default=500ms
		// minDelay (optional):
		//		the maximum delay in milliseconds for event to fire, default=10ms
		if(obj != this._obj){
			this.stop();
			this._initialDelay = initialDelay || 500;
			this._subsequentDelay = subsequentDelay || 0.90;
			this._minDelay = minDelay || 10;
			this._obj = obj;
			this._evt = evt;
			this._node = node;
			this._currentTimeout = -1;
			this._count = -1;
			this._callback = dojo.hitch(_this, callback);
			this._fireEventAndReload();
			this._evt = dojo.mixin({faux: true}, evt);
		}
	},

	stop: function(){
		// summary:
		//		Stop an ongoing timed, repeating callback sequence.
		if(this._timer){
			clearTimeout(this._timer);
			this._timer = null;
		}
		if(this._obj){
			this._callback(-1, this._node, this._evt);
			this._obj = null;
		}
	},

	addKeyListener: function(/*DOMNode*/ node, /*Object*/ keyObject, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay, /*Number?*/ minDelay){
		// summary:
		//		Start listening for a specific typematic key.
		//		See also the trigger method for other parameters.
		// keyObject:
		//		an object defining the key to listen for:
		// 		charOrCode:
		//			the printable character (string) or keyCode (number) to listen for.
		// 		keyCode:
		//			(deprecated - use charOrCode) the keyCode (number) to listen for (implies charCode = 0).
		// 		charCode:
		//			(deprecated - use charOrCode) the charCode (number) to listen for.
		// 		ctrlKey:
		//			desired ctrl key state to initiate the callback sequence:
		//			- pressed (true)
		//			- released (false)
		//			- either (unspecified)
		// 		altKey:
		//			same as ctrlKey but for the alt key
		// 		shiftKey:
		//			same as ctrlKey but for the shift key
		// returns:
		//		an array of dojo.connect handles
		if(keyObject.keyCode){
			keyObject.charOrCode = keyObject.keyCode;
			dojo.deprecated("keyCode attribute parameter for dijit.typematic.addKeyListener is deprecated. Use charOrCode instead.", "", "2.0");
		}else if(keyObject.charCode){
			keyObject.charOrCode = String.fromCharCode(keyObject.charCode);
			dojo.deprecated("charCode attribute parameter for dijit.typematic.addKeyListener is deprecated. Use charOrCode instead.", "", "2.0");
		}
		return [
			dojo.connect(node, "onkeypress", this, function(evt){
				if(evt.charOrCode == keyObject.charOrCode &&
				(keyObject.ctrlKey === undefined || keyObject.ctrlKey == evt.ctrlKey) &&
				(keyObject.altKey === undefined || keyObject.altKey == evt.altKey) &&
				(keyObject.metaKey === undefined || keyObject.metaKey == (evt.metaKey || false)) && // IE doesn't even set metaKey
				(keyObject.shiftKey === undefined || keyObject.shiftKey == evt.shiftKey)){
					dojo.stopEvent(evt);
					dijit.typematic.trigger(evt, _this, node, callback, keyObject, subsequentDelay, initialDelay, minDelay);
				}else if(dijit.typematic._obj == keyObject){
					dijit.typematic.stop();
				}
			}),
			dojo.connect(node, "onkeyup", this, function(evt){
				if(dijit.typematic._obj == keyObject){
					dijit.typematic.stop();
				}
			})
		];
	},

	addMouseListener: function(/*DOMNode*/ node, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay, /*Number?*/ minDelay){
		// summary:
		//		Start listening for a typematic mouse click.
		//		See the trigger method for other parameters.
		// returns:
		//		an array of dojo.connect handles
		var dc = dojo.connect;
		return [
			dc(node, "mousedown", this, function(evt){
				dojo.stopEvent(evt);
				dijit.typematic.trigger(evt, _this, node, callback, node, subsequentDelay, initialDelay, minDelay);
			}),
			dc(node, "mouseup", this, function(evt){
				dojo.stopEvent(evt);
				dijit.typematic.stop();
			}),
			dc(node, "mouseout", this, function(evt){
				dojo.stopEvent(evt);
				dijit.typematic.stop();
			}),
			dc(node, "mousemove", this, function(evt){
				evt.preventDefault();
			}),
			dc(node, "dblclick", this, function(evt){
				dojo.stopEvent(evt);
				if(dojo.isIE){
					dijit.typematic.trigger(evt, _this, node, callback, node, subsequentDelay, initialDelay, minDelay);
					setTimeout(dojo.hitch(this, dijit.typematic.stop), 50);
				}
			})
		];
	},

	addListener: function(/*Node*/ mouseNode, /*Node*/ keyNode, /*Object*/ keyObject, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay, /*Number?*/ minDelay){
		// summary:
		//		Start listening for a specific typematic key and mouseclick.
		//		This is a thin wrapper to addKeyListener and addMouseListener.
		//		See the addMouseListener and addKeyListener methods for other parameters.
		// mouseNode:
		//		the DOM node object to listen on for mouse events.
		// keyNode:
		//		the DOM node object to listen on for key events.
		// returns:
		//		an array of dojo.connect handles
		return this.addKeyListener(keyNode, keyObject, _this, callback, subsequentDelay, initialDelay, minDelay).concat(
			this.addMouseListener(mouseNode, _this, callback, subsequentDelay, initialDelay, minDelay));
	}
};

}

if(!dojo._hasResource["dijit._base.wai"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.wai"] = true;
dojo.provide("dijit._base.wai");

dijit.wai = {
	onload: function(){
		// summary:
		//		Detects if we are in high-contrast mode or not

		// This must be a named function and not an anonymous
		// function, so that the widget parsing code can make sure it
		// registers its onload function after this function.
		// DO NOT USE "this" within this function.

		// create div for testing if high contrast mode is on or images are turned off
		var div = dojo.create("div",{
			id: "a11yTestNode",
			style:{
				cssText:'border: 1px solid;'
					+ 'border-color:red green;'
					+ 'position: absolute;'
					+ 'height: 5px;'
					+ 'top: -999px;'
					+ 'background-image: url("' + (dojo.config.blankGif || dojo.moduleUrl("dojo", "resources/blank.gif")) + '");'
			}
		}, dojo.body());

		// test it
		var cs = dojo.getComputedStyle(div);
		if(cs){
			var bkImg = cs.backgroundImage;
			var needsA11y = (cs.borderTopColor == cs.borderRightColor) || (bkImg != null && (bkImg == "none" || bkImg == "url(invalid-url:)" ));
			dojo[needsA11y ? "addClass" : "removeClass"](dojo.body(), "dijit_a11y");
			if(dojo.isIE){
				div.outerHTML = "";		// prevent mixed-content warning, see http://support.microsoft.com/kb/925014
			}else{
				dojo.body().removeChild(div);
			}
		}
	}
};

// Test if computer is in high contrast mode.
// Make sure the a11y test runs first, before widgets are instantiated.
if(dojo.isIE || dojo.isMoz){	// NOTE: checking in Safari messes things up
	dojo._loaders.unshift(dijit.wai.onload);
}

dojo.mixin(dijit, {
	_XhtmlRoles: /banner|contentinfo|definition|main|navigation|search|note|secondary|seealso/,

	hasWaiRole: function(/*Element*/ elem, /*String*/ role){
		// summary:
		//		Determines if an element has a particular non-XHTML role.
		// returns:
		//		True if elem has the specific non-XHTML role attribute and false if not.
		// 		For backwards compatibility if role parameter not provided,
		// 		returns true if has non XHTML role
		var waiRole = this.getWaiRole(elem);
		return role ? (waiRole.indexOf(role) > -1) : (waiRole.length > 0);
	},

	getWaiRole: function(/*Element*/ elem){
		// summary:
		//		Gets the non-XHTML role for an element (which should be a wai role).
		// returns:
		//		The non-XHTML role of elem or an empty string if elem
		//		does not have a role.
		 return dojo.trim((dojo.attr(elem, "role") || "").replace(this._XhtmlRoles,"").replace("wairole:",""));
	},

	setWaiRole: function(/*Element*/ elem, /*String*/ role){
		// summary:
		//		Sets the role on an element.
		// description:
		//		Replace existing role attribute with new role.
		//		If elem already has an XHTML role, append this role to XHTML role
		//		and remove other ARIA roles.

		var curRole = dojo.attr(elem, "role") || "";
		if(!this._XhtmlRoles.test(curRole)){
			dojo.attr(elem, "role", role);
		}else{
			if((" "+ curRole +" ").indexOf(" " + role + " ") < 0){
				var clearXhtml = dojo.trim(curRole.replace(this._XhtmlRoles, ""));
				var cleanRole = dojo.trim(curRole.replace(clearXhtml, ""));
				dojo.attr(elem, "role", cleanRole + (cleanRole ? ' ' : '') + role);
			}
		}
	},

	removeWaiRole: function(/*Element*/ elem, /*String*/ role){
		// summary:
		//		Removes the specified non-XHTML role from an element.
		// 		Removes role attribute if no specific role provided (for backwards compat.)

		var roleValue = dojo.attr(elem, "role");
		if(!roleValue){ return; }
		if(role){
			var t = dojo.trim((" " + roleValue + " ").replace(" " + role + " ", " "));
			dojo.attr(elem, "role", t);
		}else{
			elem.removeAttribute("role");
		}
	},

	hasWaiState: function(/*Element*/ elem, /*String*/ state){
		// summary:
		//		Determines if an element has a given state.
		// description:
		//		Checks for an attribute called "aria-"+state.
		// returns:
		//		true if elem has a value for the given state and
		//		false if it does not.

		return elem.hasAttribute ? elem.hasAttribute("aria-"+state) : !!elem.getAttribute("aria-"+state);
	},

	getWaiState: function(/*Element*/ elem, /*String*/ state){
		// summary:
		//		Gets the value of a state on an element.
		// description:
		//		Checks for an attribute called "aria-"+state.
		// returns:
		//		The value of the requested state on elem
		//		or an empty string if elem has no value for state.

		return elem.getAttribute("aria-"+state) || "";
	},

	setWaiState: function(/*Element*/ elem, /*String*/ state, /*String*/ value){
		// summary:
		//		Sets a state on an element.
		// description:
		//		Sets an attribute called "aria-"+state.

		elem.setAttribute("aria-"+state, value);
	},

	removeWaiState: function(/*Element*/ elem, /*String*/ state){
		// summary:
		//		Removes a state from an element.
		// description:
		//		Sets an attribute called "aria-"+state.

		elem.removeAttribute("aria-"+state);
	}
});

}

if(!dojo._hasResource["dijit._base"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base"] = true;
dojo.provide("dijit._base");











}

if(!dojo._hasResource["dijit._Widget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Widget"] = true;
dojo.provide("dijit._Widget");

dojo.require( "dijit._base" );


// This code is to assist deferring dojo.connect() calls in widgets (connecting to events on the widgets'
// DOM nodes) until someone actually needs to monitor that event.
dojo.connect(dojo, "_connect",
	function(/*dijit._Widget*/ widget, /*String*/ event){
		if(widget && dojo.isFunction(widget._onConnect)){
			widget._onConnect(event);
		}
	});

dijit._connectOnUseEventHandler = function(/*Event*/ event){};

// Keep track of where the last keydown event was, to help avoid generating
// spurious ondijitclick events when:
// 1. focus is on a <button> or <a>
// 2. user presses then releases the ENTER key
// 3. onclick handler fires and shifts focus to another node, with an ondijitclick handler
// 4. onkeyup event fires, causing the ondijitclick handler to fire
dijit._lastKeyDownNode = null;
if(dojo.isIE){
	(function(){
		var keydownCallback = function(evt){
			dijit._lastKeyDownNode = evt.srcElement;
		};
		dojo.doc.attachEvent('onkeydown', keydownCallback);
		dojo.addOnWindowUnload(function(){
			dojo.doc.detachEvent('onkeydown', keydownCallback);
		});
	})();
}else{
	dojo.doc.addEventListener('keydown', function(evt){
		dijit._lastKeyDownNode = evt.target;
	}, true);
}

(function(){

var _attrReg = {},	// cached results from getSetterAttributes
	getSetterAttributes = function(widget){
		// summary:
		//		Returns list of attributes with custom setters for specified widget
		var dc = widget.declaredClass;
		if(!_attrReg[dc]){
			var r = [],
				attrs,
				proto = widget.constructor.prototype;
			for(var fxName in proto){
				if(dojo.isFunction(proto[fxName]) && (attrs = fxName.match(/^_set([a-zA-Z]*)Attr$/)) && attrs[1]){
					r.push(attrs[1].charAt(0).toLowerCase() + attrs[1].substr(1));
				}
			}
			_attrReg[dc] = r;
		}
		return _attrReg[dc] || [];	// String[]
	};

dojo.declare("dijit._Widget", null, {
	// summary:
	//		Base class for all Dijit widgets.

	// id: [const] String
	//		A unique, opaque ID string that can be assigned by users or by the
	//		system. If the developer passes an ID which is known not to be
	//		unique, the specified ID is ignored and the system-generated ID is
	//		used instead.
	id: "",

	// lang: [const] String
	//		Rarely used.  Overrides the default Dojo locale used to render this widget,
	//		as defined by the [HTML LANG](http://www.w3.org/TR/html401/struct/dirlang.html#adef-lang) attribute.
	//		Value must be among the list of locales specified during by the Dojo bootstrap,
	//		formatted according to [RFC 3066](http://www.ietf.org/rfc/rfc3066.txt) (like en-us).
	lang: "",

	// dir: [const] String
	//		Bi-directional support, as defined by the [HTML DIR](http://www.w3.org/TR/html401/struct/dirlang.html#adef-dir)
	//		attribute. Either left-to-right "ltr" or right-to-left "rtl".  If undefined, widgets renders in page's
	//		default direction.
	dir: "",

	// class: String
	//		HTML class attribute
	"class": "",

	// style: String||Object
	//		HTML style attributes as cssText string or name/value hash
	style: "",

	// title: String
	//		HTML title attribute.
	//
	//		For form widgets this specifies a tooltip to display when hovering over
	//		the widget (just like the native HTML title attribute).
	//
	//		For TitlePane or for when this widget is a child of a TabContainer, AccordionContainer,
	//		etc., it's used to specify the tab label, accordion pane title, etc.
	title: "",

	// tooltip: String
	//		When this widget's title attribute is used to for a tab label, accordion pane title, etc.,
	//		this specifies the tooltip to appear when the mouse is hovered over that text.
	tooltip: "",

	// baseClass: [protected] String
	//		Root CSS class of the widget (ex: dijitTextBox), used to construct CSS classes to indicate
	//		widget state.
	baseClass: "",

	// srcNodeRef: [readonly] DomNode
	//		pointer to original DOM node
	srcNodeRef: null,

	// domNode: [readonly] DomNode
	//		This is our visible representation of the widget! Other DOM
	//		Nodes may by assigned to other properties, usually through the
	//		template system's dojoAttachPoint syntax, but the domNode
	//		property is the canonical "top level" node in widget UI.
	domNode: null,

	// containerNode: [readonly] DomNode
	//		Designates where children of the source DOM node will be placed.
	//		"Children" in this case refers to both DOM nodes and widgets.
	//		For example, for myWidget:
	//
	//		|	<div dojoType=myWidget>
	//		|		<b> here's a plain DOM node
	//		|		<span dojoType=subWidget>and a widget</span>
	//		|		<i> and another plain DOM node </i>
	//		|	</div>
	//
	//		containerNode would point to:
	//
	//		|		<b> here's a plain DOM node
	//		|		<span dojoType=subWidget>and a widget</span>
	//		|		<i> and another plain DOM node </i>
	//
	//		In templated widgets, "containerNode" is set via a
	//		dojoAttachPoint assignment.
	//
	//		containerNode must be defined for any widget that accepts innerHTML
	//		(like ContentPane or BorderContainer or even Button), and conversely
	//		is null for widgets that don't, like TextBox.
	containerNode: null,

/*=====
	// _started: Boolean
	//		startup() has completed.
	_started: false,
=====*/

	// attributeMap: [protected] Object
	//		attributeMap sets up a "binding" between attributes (aka properties)
	//		of the widget and the widget's DOM.
	//		Changes to widget attributes listed in attributeMap will be
	//		reflected into the DOM.
	//
	//		For example, calling attr('title', 'hello')
	//		on a TitlePane will automatically cause the TitlePane's DOM to update
	//		with the new title.
	//
	//		attributeMap is a hash where the key is an attribute of the widget,
	//		and the value reflects a binding to a:
	//
	//		- DOM node attribute
	// |		focus: {node: "focusNode", type: "attribute"}
	// 		Maps this.focus to this.focusNode.focus
	//
	//		- DOM node innerHTML
	//	|		title: { node: "titleNode", type: "innerHTML" }
	//		Maps this.title to this.titleNode.innerHTML
	//
	//		- DOM node innerText
	//	|		title: { node: "titleNode", type: "innerText" }
	//		Maps this.title to this.titleNode.innerText
	//
	//		- DOM node CSS class
	// |		myClass: { node: "domNode", type: "class" }
	//		Maps this.myClass to this.domNode.className
	//
	//		If the value is an array, then each element in the array matches one of the
	//		formats of the above list.
	//
	//		There are also some shorthands for backwards compatibility:
	//		- string --> { node: string, type: "attribute" }, for example:
	//	|	"focusNode" ---> { node: "focusNode", type: "attribute" }
	//		- "" --> { node: "domNode", type: "attribute" }
	attributeMap: {id:"", dir:"", lang:"", "class":"", style:"", title:""},

	// _deferredConnects: [protected] Object
	//		attributeMap addendum for event handlers that should be connected only on first use
	_deferredConnects: {
		onClick: "",
		onDblClick: "",
		onKeyDown: "",
		onKeyPress: "",
		onKeyUp: "",
		onMouseMove: "",
		onMouseDown: "",
		onMouseOut: "",
		onMouseOver: "",
		onMouseLeave: "",
		onMouseEnter: "",
		onMouseUp: ""
	},

	onClick: dijit._connectOnUseEventHandler,
	/*=====
	onClick: function(event){
		// summary:
		//		Connect to this function to receive notifications of mouse click events.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onDblClick: dijit._connectOnUseEventHandler,
	/*=====
	onDblClick: function(event){
		// summary:
		//		Connect to this function to receive notifications of mouse double click events.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onKeyDown: dijit._connectOnUseEventHandler,
	/*=====
	onKeyDown: function(event){
		// summary:
		//		Connect to this function to receive notifications of keys being pressed down.
		// event:
		//		key Event
		// tags:
		//		callback
	},
	=====*/
	onKeyPress: dijit._connectOnUseEventHandler,
	/*=====
	onKeyPress: function(event){
		// summary:
		//		Connect to this function to receive notifications of printable keys being typed.
		// event:
		//		key Event
		// tags:
		//		callback
	},
	=====*/
	onKeyUp: dijit._connectOnUseEventHandler,
	/*=====
	onKeyUp: function(event){
		// summary:
		//		Connect to this function to receive notifications of keys being released.
		// event:
		//		key Event
		// tags:
		//		callback
	},
	=====*/
	onMouseDown: dijit._connectOnUseEventHandler,
	/*=====
	onMouseDown: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse button is pressed down.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseMove: dijit._connectOnUseEventHandler,
	/*=====
	onMouseMove: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse moves over nodes contained within this widget.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseOut: dijit._connectOnUseEventHandler,
	/*=====
	onMouseOut: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse moves off of nodes contained within this widget.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseOver: dijit._connectOnUseEventHandler,
	/*=====
	onMouseOver: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse moves onto nodes contained within this widget.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseLeave: dijit._connectOnUseEventHandler,
	/*=====
	onMouseLeave: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse moves off of this widget.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseEnter: dijit._connectOnUseEventHandler,
	/*=====
	onMouseEnter: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse moves onto this widget.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/
	onMouseUp: dijit._connectOnUseEventHandler,
	/*=====
	onMouseUp: function(event){
		// summary:
		//		Connect to this function to receive notifications of when the mouse button is released.
		// event:
		//		mouse Event
		// tags:
		//		callback
	},
	=====*/

	// Constants used in templates

	// _blankGif: [protected] String
	//		Path to a blank 1x1 image.
	//		Used by <img> nodes in templates that really get their image via CSS background-image.
	_blankGif: (dojo.config.blankGif || dojo.moduleUrl("dojo", "resources/blank.gif")).toString(),

	//////////// INITIALIZATION METHODS ///////////////////////////////////////

	postscript: function(/*Object?*/params, /*DomNode|String*/srcNodeRef){
		// summary:
		//		Kicks off widget instantiation.  See create() for details.
		// tags:
		//		private
		this.create(params, srcNodeRef);
	},

	create: function(/*Object?*/params, /*DomNode|String?*/srcNodeRef){
		// summary:
		//		Kick off the life-cycle of a widget
		// params:
		//		Hash of initialization parameters for widget, including
		//		scalar values (like title, duration etc.) and functions,
		//		typically callbacks like onClick.
		// srcNodeRef:
		//		If a srcNodeRef (DOM node) is specified:
		//			- use srcNodeRef.innerHTML as my contents
		//			- if this is a behavioral widget then apply behavior
		//			  to that srcNodeRef
		//			- otherwise, replace srcNodeRef with my generated DOM
		//			  tree
		// description:
		//		Create calls a number of widget methods (postMixInProperties, buildRendering, postCreate,
		//		etc.), some of which of you'll want to override. See http://docs.dojocampus.org/dijit/_Widget
		//		for a discussion of the widget creation lifecycle.
		//
		//		Of course, adventurous developers could override create entirely, but this should
		//		only be done as a last resort.
		// tags:
		//		private

		// store pointer to original DOM tree
		this.srcNodeRef = dojo.byId(srcNodeRef);

		// For garbage collection.  An array of handles returned by Widget.connect()
		// Each handle returned from Widget.connect() is an array of handles from dojo.connect()
		this._connects = [];

		// For garbage collection.  An array of handles returned by Widget.subscribe()
		// The handle returned from Widget.subscribe() is the handle returned from dojo.subscribe()
		this._subscribes = [];

		// To avoid double-connects, remove entries from _deferredConnects
		// that have been setup manually by a subclass (ex, by dojoAttachEvent).
		// If a subclass has redefined a callback (ex: onClick) then assume it's being
		// connected to manually.
		this._deferredConnects = dojo.clone(this._deferredConnects);
		for(var attr in this.attributeMap){
			delete this._deferredConnects[attr]; // can't be in both attributeMap and _deferredConnects
		}
		for(attr in this._deferredConnects){
			if(this[attr] !== dijit._connectOnUseEventHandler){
				delete this._deferredConnects[attr];	// redefined, probably dojoAttachEvent exists
			}
		}

		//mixin our passed parameters
		if(this.srcNodeRef && (typeof this.srcNodeRef.id == "string")){ this.id = this.srcNodeRef.id; }
		if(params){
			this.params = params;
			dojo.mixin(this,params);
		}
		this.postMixInProperties();

		// generate an id for the widget if one wasn't specified
		// (be sure to do this before buildRendering() because that function might
		// expect the id to be there.)
		if(!this.id){
			this.id = dijit.getUniqueId(this.declaredClass.replace(/\./g,"_"));
		}
		dijit.registry.add(this);

		this.buildRendering();

		if(this.domNode){
			// Copy attributes listed in attributeMap into the [newly created] DOM for the widget.
			this._applyAttributes();

			var source = this.srcNodeRef;
			if(source && source.parentNode){
				source.parentNode.replaceChild(this.domNode, source);
			}

			// If the developer has specified a handler as a widget parameter
			// (ex: new Button({onClick: ...})
			// then naturally need to connect from DOM node to that handler immediately,
			for(attr in this.params){
				this._onConnect(attr);
			}
		}

		if(this.domNode){
			this.domNode.setAttribute("widgetId", this.id);
		}
		this.postCreate();

		// If srcNodeRef has been processed and removed from the DOM (e.g. TemplatedWidget) then delete it to allow GC.
		if(this.srcNodeRef && !this.srcNodeRef.parentNode){
			delete this.srcNodeRef;
		}

		this._created = true;
	},

	_applyAttributes: function(){
		// summary:
		//		Step during widget creation to copy all widget attributes to the
		//		DOM as per attributeMap and _setXXXAttr functions.
		// description:
		//		Skips over blank/false attribute values, unless they were explicitly specified
		//		as parameters to the widget, since those are the default anyway,
		//		and setting tabIndex="" is different than not setting tabIndex at all.
		//
		//		It processes the attributes in the attribute map first, and then
		//		it goes through and processes the attributes for the _setXXXAttr
		//		functions that have been specified
		// tags:
		//		private
		var condAttrApply = function(attr, scope){
			if((scope.params && attr in scope.params) || scope[attr]){
				scope.set(attr, scope[attr]);
			}
		};

		// Do the attributes in attributeMap
		for(var attr in this.attributeMap){
			condAttrApply(attr, this);
		}

		// And also any attributes with custom setters
		dojo.forEach(getSetterAttributes(this), function(a){
			if(!(a in this.attributeMap)){
				condAttrApply(a, this);
			}
		}, this);
	},

	postMixInProperties: function(){
		// summary:
		//		Called after the parameters to the widget have been read-in,
		//		but before the widget template is instantiated. Especially
		//		useful to set properties that are referenced in the widget
		//		template.
		// tags:
		//		protected
	},

	buildRendering: function(){
		// summary:
		//		Construct the UI for this widget, setting this.domNode
		// description:
		//		Most widgets will mixin `dijit._Templated`, which implements this
		//		method.
		// tags:
		//		protected
		this.domNode = this.srcNodeRef || dojo.create('div');
	},

	postCreate: function(){
		// summary:
		//		Processing after the DOM fragment is created
		// description:
		//		Called after the DOM fragment has been created, but not necessarily
		//		added to the document.  Do not include any operations which rely on
		//		node dimensions or placement.
		// tags:
		//		protected

		// baseClass is a single class name or occasionally a space-separated list of names.
		// Add those classes to the DOMNod.  If RTL mode then also add with Rtl suffix.		
		if(this.baseClass){
			var classes = this.baseClass.split(" ");
			if(!this.isLeftToRight()){
				classes = classes.concat( dojo.map(classes, function(name){ return name+"Rtl"; }));
			}
			dojo.addClass(this.domNode, classes);
		}
	},

	startup: function(){
		// summary:
		//		Processing after the DOM fragment is added to the document
		// description:
		//		Called after a widget and its children have been created and added to the page,
		//		and all related widgets have finished their create() cycle, up through postCreate().
		//		This is useful for composite widgets that need to control or layout sub-widgets.
		//		Many layout widgets can use this as a wiring phase.
		this._started = true;
	},

	//////////// DESTROY FUNCTIONS ////////////////////////////////

	destroyRecursive: function(/*Boolean?*/ preserveDom){
		// summary:
		// 		Destroy this widget and its descendants
		// description:
		//		This is the generic "destructor" function that all widget users
		// 		should call to cleanly discard with a widget. Once a widget is
		// 		destroyed, it is removed from the manager object.
		// preserveDom:
		//		If true, this method will leave the original DOM structure
		//		alone of descendant Widgets. Note: This will NOT work with
		//		dijit._Templated widgets.

		this._beingDestroyed = true;
		this.destroyDescendants(preserveDom);
		this.destroy(preserveDom);
	},

	destroy: function(/*Boolean*/ preserveDom){
		// summary:
		// 		Destroy this widget, but not its descendants.
		//		This method will, however, destroy internal widgets such as those used within a template.
		// preserveDom: Boolean
		//		If true, this method will leave the original DOM structure alone.
		//		Note: This will not yet work with _Templated widgets

		this._beingDestroyed = true;
		this.uninitialize();
		var d = dojo,
			dfe = d.forEach,
			dun = d.unsubscribe;
		dfe(this._connects, function(array){
			dfe(array, d.disconnect);
		});
		dfe(this._subscribes, function(handle){
			dun(handle);
		});

		// destroy widgets created as part of template, etc.
		dfe(this._supportingWidgets || [], function(w){
			if(w.destroyRecursive){
				w.destroyRecursive();
			}else if(w.destroy){
				w.destroy();
			}
		});

		this.destroyRendering(preserveDom);
		dijit.registry.remove(this.id);
		this._destroyed = true;
	},

	destroyRendering: function(/*Boolean?*/ preserveDom){
		// summary:
		//		Destroys the DOM nodes associated with this widget
		// preserveDom:
		//		If true, this method will leave the original DOM structure alone
		//		during tear-down. Note: this will not work with _Templated
		//		widgets yet.
		// tags:
		//		protected

		if(this.bgIframe){
			this.bgIframe.destroy(preserveDom);
			delete this.bgIframe;
		}

		if(this.domNode){
			if(preserveDom){
				dojo.removeAttr(this.domNode, "widgetId");
			}else{
				dojo.destroy(this.domNode);
			}
			delete this.domNode;
		}

		if(this.srcNodeRef){
			if(!preserveDom){
				dojo.destroy(this.srcNodeRef);
			}
			delete this.srcNodeRef;
		}
	},

	destroyDescendants: function(/*Boolean?*/ preserveDom){
		// summary:
		//		Recursively destroy the children of this widget and their
		//		descendants.
		// preserveDom:
		//		If true, the preserveDom attribute is passed to all descendant
		//		widget's .destroy() method. Not for use with _Templated
		//		widgets.

		// get all direct descendants and destroy them recursively
		dojo.forEach(this.getChildren(), function(widget){
			if(widget.destroyRecursive){
				widget.destroyRecursive(preserveDom);
			}
		});
	},


	uninitialize: function(){
		// summary:
		//		Stub function. Override to implement custom widget tear-down
		//		behavior.
		// tags:
		//		protected
		return false;
	},

	////////////////// MISCELLANEOUS METHODS ///////////////////

	onFocus: function(){
		// summary:
		//		Called when the widget becomes "active" because
		//		it or a widget inside of it either has focus, or has recently
		//		been clicked.
		// tags:
		//		callback
	},

	onBlur: function(){
		// summary:
		//		Called when the widget stops being "active" because
		//		focus moved to something outside of it, or the user
		//		clicked somewhere outside of it, or the widget was
		//		hidden.
		// tags:
		//		callback
	},

	_onFocus: function(e){
		// summary:
		//		This is where widgets do processing for when they are active,
		//		such as changing CSS classes.  See onFocus() for more details.
		// tags:
		//		protected
		this.onFocus();
	},

	_onBlur: function(){
		// summary:
		//		This is where widgets do processing for when they stop being active,
		//		such as changing CSS classes.  See onBlur() for more details.
		// tags:
		//		protected
		this.onBlur();
	},

	_onConnect: function(/*String*/ event){
		// summary:
		//		Called when someone connects to one of my handlers.
		//		"Turn on" that handler if it isn't active yet.
		//
		//		This is also called for every single initialization parameter
		//		so need to do nothing for parameters like "id".
		// tags:
		//		private
		if(event in this._deferredConnects){
			var mapNode = this[this._deferredConnects[event] || 'domNode'];
			this.connect(mapNode, event.toLowerCase(), event);
			delete this._deferredConnects[event];
		}
	},

	_setClassAttr: function(/*String*/ value){
		// summary:
		//		Custom setter for the CSS "class" attribute
		// tags:
		//		protected
		var mapNode = this[this.attributeMap["class"] || 'domNode'];
		dojo.removeClass(mapNode, this["class"])
		this["class"] = value;
		dojo.addClass(mapNode, value);
	},

	_setStyleAttr: function(/*String||Object*/ value){
		// summary:
		//		Sets the style attribut of the widget according to value,
		//		which is either a hash like {height: "5px", width: "3px"}
		//		or a plain string
		// description:
		//		Determines which node to set the style on based on style setting
		//		in attributeMap.
		// tags:
		//		protected

		var mapNode = this[this.attributeMap.style || 'domNode'];

		// Note: technically we should revert any style setting made in a previous call
		// to his method, but that's difficult to keep track of.

		if(dojo.isObject(value)){
			dojo.style(mapNode, value);
		}else{
			if(mapNode.style.cssText){
				mapNode.style.cssText += "; " + value;
			}else{
				mapNode.style.cssText = value;
			}
		}

		this.style = value;
	},

	setAttribute: function(/*String*/ attr, /*anything*/ value){
		// summary:
		//		Deprecated.  Use set() instead.
		// tags:
		//		deprecated
		dojo.deprecated(this.declaredClass+"::setAttribute(attr, value) is deprecated. Use set() instead.", "", "2.0");
		this.set(attr, value);
	},

	_attrToDom: function(/*String*/ attr, /*String*/ value){
		// summary:
		//		Reflect a widget attribute (title, tabIndex, duration etc.) to
		//		the widget DOM, as specified in attributeMap.
		//
		// description:
		//		Also sets this["attr"] to the new value.
		//		Note some attributes like "type"
		//		cannot be processed this way as they are not mutable.
		//
		// tags:
		//		private

		var commands = this.attributeMap[attr];
		dojo.forEach(dojo.isArray(commands) ? commands : [commands], function(command){

			// Get target node and what we are doing to that node
			var mapNode = this[command.node || command || "domNode"];	// DOM node
			var type = command.type || "attribute";	// class, innerHTML, innerText, or attribute

			switch(type){
				case "attribute":
					if(dojo.isFunction(value)){ // functions execute in the context of the widget
						value = dojo.hitch(this, value);
					}

					// Get the name of the DOM node attribute; usually it's the same
					// as the name of the attribute in the widget (attr), but can be overridden.
					// Also maps handler names to lowercase, like onSubmit --> onsubmit
					var attrName = command.attribute ? command.attribute :
						(/^on[A-Z][a-zA-Z]*$/.test(attr) ? attr.toLowerCase() : attr);

					dojo.attr(mapNode, attrName, value);
					break;
				case "innerText":
					mapNode.innerHTML = "";
					mapNode.appendChild(dojo.doc.createTextNode(value));
					break;
				case "innerHTML":
					mapNode.innerHTML = value;
					break;
				case "class":
					dojo.removeClass(mapNode, this[attr]);
					dojo.addClass(mapNode, value);
					break;
			}
		}, this);
		this[attr] = value;
	},

	attr: function(/*String|Object*/name, /*Object?*/value){
		// summary:
		//		Set or get properties on a widget instance.
		//	name:
		//		The property to get or set. If an object is passed here and not
		//		a string, its keys are used as names of attributes to be set
		//		and the value of the object as values to set in the widget.
		//	value:
		//		Optional. If provided, attr() operates as a setter. If omitted,
		//		the current value of the named property is returned.
		// description:
		//		This method is deprecated, use get() or set() directly.

		// Print deprecation warning but only once per calling function
		if(dojo.config.isDebug){
			var alreadyCalledHash = arguments.callee._ach || (arguments.callee._ach = {}),
				caller = (arguments.callee.caller || "unknown caller").toString();
			if(!alreadyCalledHash[caller]){
				dojo.deprecated(this.declaredClass + "::attr() is deprecated. Use get() or set() instead, called from " +
				caller, "", "2.0");
				alreadyCalledHash[caller] = true;
			}
		}

		var args = arguments.length;
		if(args >= 2 || typeof name === "object"){ // setter
			return this.set.apply(this, arguments);
		}else{ // getter
			return this.get(name);
		}
	},
	
	get: function(name){
		// summary:
		//		Get a property from a widget.
		//	name:
		//		The property to get.
		// description:
		//		Get a named property from a widget. The property may
		//		potentially be retrieved via a getter method. If no getter is defined, this
		// 		just retrieves the object's property.  
		// 		For example, if the widget has a properties "foo"
		//		and "bar" and a method named "_getFooAttr", calling:
		//	|	myWidget.get("foo");
		//		would be equivalent to writing:
		//	|	widget._getFooAttr();
		//		and:
		//	|	myWidget.get("bar");
		//		would be equivalent to writing:
		//	|	widget.bar;
		var names = this._getAttrNames(name);
		return this[names.g] ? this[names.g]() : this[name];
	},
	
	set: function(name, value){
		// summary:
		//		Set a property on a widget
		//	name:
		//		The property to set. 
		//	value:
		//		The value to set in the property.
		// description:
		//		Sets named properties on a widget which may potentially be handled by a 
		// 		setter in the widget.
		// 		For example, if the widget has a properties "foo"
		//		and "bar" and a method named "_setFooAttr", calling:
		//	|	myWidget.set("foo", "Howdy!");
		//		would be equivalent to writing:
		//	|	widget._setFooAttr("Howdy!");
		//		and:
		//	|	myWidget.set("bar", 3);
		//		would be equivalent to writing:
		//	|	widget.bar = 3;
		//
		//	set() may also be called with a hash of name/value pairs, ex:
		//	|	myWidget.set({
		//	|		foo: "Howdy",
		//	|		bar: 3
		//	|	})
		//	This is equivalent to calling set(foo, "Howdy") and set(bar, 3)

		if(typeof name === "object"){
			for(var x in name){
				this.set(x, name[x]); 
			}
			return this;
		}
		var names = this._getAttrNames(name);
		if(this[names.s]){
			// use the explicit setter
			var result = this[names.s].apply(this, Array.prototype.slice.call(arguments, 1));
		}else{
			// if param is specified as DOM node attribute, copy it
			if(name in this.attributeMap){
				this._attrToDom(name, value);
			}
			var oldValue = this[name];
			// FIXME: what about function assignments? Any way to connect() here?
			this[name] = value;
		}
		return result || this;
	},
	
	_attrPairNames: {},		// shared between all widgets
	_getAttrNames: function(name){
		// summary:
		//		Helper function for get() and set().
		//		Caches attribute name values so we don't do the string ops every time.
		// tags:
		//		private

		var apn = this._attrPairNames;
		if(apn[name]){ return apn[name]; }
		var uc = name.charAt(0).toUpperCase() + name.substr(1);
		return (apn[name] = {
			n: name+"Node",
			s: "_set"+uc+"Attr",
			g: "_get"+uc+"Attr"
		});
	},

	toString: function(){
		// summary:
		//		Returns a string that represents the widget
		// description:
		//		When a widget is cast to a string, this method will be used to generate the
		//		output. Currently, it does not implement any sort of reversible
		//		serialization.
		return '[Widget ' + this.declaredClass + ', ' + (this.id || 'NO ID') + ']'; // String
	},

	getDescendants: function(){
		// summary:
		//		Returns all the widgets contained by this, i.e., all widgets underneath this.containerNode.
		//		This method should generally be avoided as it returns widgets declared in templates, which are
		//		supposed to be internal/hidden, but it's left here for back-compat reasons.

		return this.containerNode ? dojo.query('[widgetId]', this.containerNode).map(dijit.byNode) : []; // dijit._Widget[]
	},

	getChildren: function(){
		// summary:
		//		Returns all the widgets contained by this, i.e., all widgets underneath this.containerNode.
		//		Does not return nested widgets, nor widgets that are part of this widget's template.
		return this.containerNode ? dijit.findWidgets(this.containerNode) : []; // dijit._Widget[]
	},

	// nodesWithKeyClick: [private] String[]
	//		List of nodes that correctly handle click events via native browser support,
	//		and don't need dijit's help
	nodesWithKeyClick: ["input", "button"],

	connect: function(
			/*Object|null*/ obj,
			/*String|Function*/ event,
			/*String|Function*/ method){
		// summary:
		//		Connects specified obj/event to specified method of this object
		//		and registers for disconnect() on widget destroy.
		// description:
		//		Provide widget-specific analog to dojo.connect, except with the
		//		implicit use of this widget as the target object.
		//		This version of connect also provides a special "ondijitclick"
		//		event which triggers on a click or space or enter keyup
		// returns:
		//		A handle that can be passed to `disconnect` in order to disconnect before
		//		the widget is destroyed.
		// example:
		//	|	var btn = new dijit.form.Button();
		//	|	// when foo.bar() is called, call the listener we're going to
		//	|	// provide in the scope of btn
		//	|	btn.connect(foo, "bar", function(){
		//	|		console.debug(this.toString());
		//	|	});
		// tags:
		//		protected

		var d = dojo,
			dc = d._connect,
			handles = [];
		if(event == "ondijitclick"){
			// add key based click activation for unsupported nodes.
			// do all processing onkey up to prevent spurious clicks
			// for details see comments at top of this file where _lastKeyDownNode is defined
			if(dojo.indexOf(this.nodesWithKeyClick, obj.nodeName.toLowerCase()) == -1){ // is NOT input or button
				var m = d.hitch(this, method);
				handles.push(
					dc(obj, "onkeydown", this, function(e){
						//console.log(this.id + ": onkeydown, e.target = ", e.target, ", lastKeyDownNode was ", dijit._lastKeyDownNode, ", equality is ", (e.target === dijit._lastKeyDownNode));
						if((e.keyCode == d.keys.ENTER || e.keyCode == d.keys.SPACE) &&
							!e.ctrlKey && !e.shiftKey && !e.altKey && !e.metaKey){
							// needed on IE for when focus changes between keydown and keyup - otherwise dropdown menus do not work
							dijit._lastKeyDownNode = e.target;
							e.preventDefault();		// stop event to prevent scrolling on space key in IE
						}
			 		}),
					dc(obj, "onkeyup", this, function(e){
						//console.log(this.id + ": onkeyup, e.target = ", e.target, ", lastKeyDownNode was ", dijit._lastKeyDownNode, ", equality is ", (e.target === dijit._lastKeyDownNode));
						if( (e.keyCode == d.keys.ENTER || e.keyCode == d.keys.SPACE) &&
							e.target === dijit._lastKeyDownNode &&
							!e.ctrlKey && !e.shiftKey && !e.altKey && !e.metaKey){
								//need reset here or have problems in FF when focus returns to trigger element after closing popup/alert
								dijit._lastKeyDownNode = null;
								return m(e);
						}
					})
				);
			}
			event = "onclick";
		}
		handles.push(dc(obj, event, this, method));

		this._connects.push(handles);
		return handles;		// _Widget.Handle
	},

	disconnect: function(/* _Widget.Handle */ handles){
		// summary:
		//		Disconnects handle created by `connect`.
		//		Also removes handle from this widget's list of connects.
		// tags:
		//		protected
		for(var i=0; i<this._connects.length; i++){
			if(this._connects[i] == handles){
				dojo.forEach(handles, dojo.disconnect);
				this._connects.splice(i, 1);
				return;
			}
		}
	},

	subscribe: function(
			/*String*/ topic,
			/*String|Function*/ method){
		// summary:
		//		Subscribes to the specified topic and calls the specified method
		//		of this object and registers for unsubscribe() on widget destroy.
		// description:
		//		Provide widget-specific analog to dojo.subscribe, except with the
		//		implicit use of this widget as the target object.
		// example:
		//	|	var btn = new dijit.form.Button();
		//	|	// when /my/topic is published, this button changes its label to
		//	|   // be the parameter of the topic.
		//	|	btn.subscribe("/my/topic", function(v){
		//	|		this.set("label", v);
		//	|	});
		var d = dojo,
			handle = d.subscribe(topic, this, method);

		// return handles for Any widget that may need them
		this._subscribes.push(handle);
		return handle;
	},

	unsubscribe: function(/*Object*/ handle){
		// summary:
		//		Unsubscribes handle created by this.subscribe.
		//		Also removes handle from this widget's list of subscriptions
		for(var i=0; i<this._subscribes.length; i++){
			if(this._subscribes[i] == handle){
				dojo.unsubscribe(handle);
				this._subscribes.splice(i, 1);
				return;
			}
		}
	},

	isLeftToRight: function(){
		// summary:
		//		Return this widget's explicit or implicit orientation (true for LTR, false for RTL)
		// tags:
		//		protected
		return this.dir ? (this.dir == "ltr") : dojo._isBodyLtr(); //Boolean
	},

	isFocusable: function(){
		// summary:
		//		Return true if this widget can currently be focused
		//		and false if not
		return this.focus && (dojo.style(this.domNode, "display") != "none");
	},

	placeAt: function(/* String|DomNode|_Widget */reference, /* String?|Int? */position){
		// summary:
		//		Place this widget's domNode reference somewhere in the DOM based
		//		on standard dojo.place conventions, or passing a Widget reference that
		//		contains and addChild member.
		//
		// description:
		//		A convenience function provided in all _Widgets, providing a simple
		//		shorthand mechanism to put an existing (or newly created) Widget
		//		somewhere in the dom, and allow chaining.
		//
		// reference:
		//		The String id of a domNode, a domNode reference, or a reference to a Widget posessing
		//		an addChild method.
		//
		// position:
		//		If passed a string or domNode reference, the position argument
		//		accepts a string just as dojo.place does, one of: "first", "last",
		//		"before", or "after".
		//
		//		If passed a _Widget reference, and that widget reference has an ".addChild" method,
		//		it will be called passing this widget instance into that method, supplying the optional
		//		position index passed.
		//
		// returns:
		//		dijit._Widget
		//		Provides a useful return of the newly created dijit._Widget instance so you
		//		can "chain" this function by instantiating, placing, then saving the return value
		//		to a variable.
		//
		// example:
		// | 	// create a Button with no srcNodeRef, and place it in the body:
		// | 	var button = new dijit.form.Button({ label:"click" }).placeAt(dojo.body());
		// | 	// now, 'button' is still the widget reference to the newly created button
		// | 	dojo.connect(button, "onClick", function(e){ console.log('click'); });
		//
		// example:
		// |	// create a button out of a node with id="src" and append it to id="wrapper":
		// | 	var button = new dijit.form.Button({},"src").placeAt("wrapper");
		//
		// example:
		// |	// place a new button as the first element of some div
		// |	var button = new dijit.form.Button({ label:"click" }).placeAt("wrapper","first");
		//
		// example:
		// |	// create a contentpane and add it to a TabContainer
		// |	var tc = dijit.byId("myTabs");
		// |	new dijit.layout.ContentPane({ href:"foo.html", title:"Wow!" }).placeAt(tc)

		if(reference.declaredClass && reference.addChild){
			reference.addChild(this, position);
		}else{
			dojo.place(this.domNode, reference, position);
		}
		return this;
	},

	_onShow: function(){
		// summary:
		//		Internal method called when this widget is made visible.
		//		See `onShow` for details.
		this.onShow();
	},

	onShow: function(){
		// summary:
		//		Called when this widget becomes the selected pane in a
		//		`dijit.layout.TabContainer`, `dijit.layout.StackContainer`,
		//		`dijit.layout.AccordionContainer`, etc.
		//
		//		Also called to indicate display of a `dijit.Dialog`, `dijit.TooltipDialog`, or `dijit.TitlePane`.
		// tags:
		//		callback
	},

	onHide: function(){
		// summary:
			//		Called when another widget becomes the selected pane in a
			//		`dijit.layout.TabContainer`, `dijit.layout.StackContainer`,
			//		`dijit.layout.AccordionContainer`, etc.
			//
			//		Also called to indicate hide of a `dijit.Dialog`, `dijit.TooltipDialog`, or `dijit.TitlePane`.
			// tags:
			//		callback
	},

	onClose: function(){
		// summary:
		//		Called when this widget is being displayed as a popup (ex: a Calendar popped
		//		up from a DateTextBox), and it is hidden.
		//		This is called from the dijit.popup code, and should not be called directly.
		//
		//		Also used as a parameter for children of `dijit.layout.StackContainer` or subclasses.
		//		Callback if a user tries to close the child.   Child will be closed if this function returns true.
		// tags:
		//		extension

		return true;		// Boolean
	}
});

})();

}

if(!dojo._hasResource["dijit._CssStateMixin"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._CssStateMixin"] = true;
dojo.provide("dijit._CssStateMixin");


dojo.declare("dijit._CssStateMixin", [], {
	// summary:
	//		Mixin for widgets to set CSS classes on the widget DOM nodes depending on hover/mouse press/focus
	//		state changes, and also higher-level state changes such becoming disabled or selected.
	//
	// description:
	//		By mixing this class into your widget, and setting the this.baseClass attribute, it will automatically
	//		maintain CSS classes on the widget root node (this.domNode) depending on hover,
	//		active, focus, etc. state.   Ex: with a baseClass of dijitButton, it will apply the classes
	//		dijitButtonHovered and dijitButtonActive, as the user moves the mouse over the widget and clicks it.
	//
	//		It also sets CSS like dijitButtonDisabled based on widget semantic state.
	//
	//		By setting the cssStateNodes attribute, a widget can also track events on subnodes (like buttons
	//		within the widget).

	// cssStateNodes: [protected] Object
	//		List of sub-nodes within the widget that need CSS classes applied on mouse hover/press and focus
	//.
	//		Each entry in the hash is a an attachpoint names (like "upArrowButton") mapped to a CSS class names
	//		(like "dijitUpArrowButton"). Example:
	//	|		{
	//	|			"upArrowButton": "dijitUpArrowButton",
	//	|			"downArrowButton": "dijitDownArrowButton"
	//	|		}
	//		The above will set the CSS class dijitUpArrowButton to the this.upArrowButton DOMNode when it
	//		is hovered, etc.
	cssStateNodes: {},

	postCreate: function(){
		this.inherited(arguments);

		// Automatically monitor mouse events (essentially :hover and :active) on this.domNode
		dojo.forEach(["onmouseenter", "onmouseleave", "onmousedown"], function(e){
			this.connect(this.domNode, e, "_cssMouseEvent");
		}, this);
		
		// Monitoring changes to disabled, readonly, etc. state, and update CSS class of root node
		this.connect(this, "set", function(name, value){
			if(arguments.length >= 2 && {disabled: true, readOnly: true, checked:true, selected:true}[name]){
				this._setStateClass();
			}
		});

		// The widget coming in/out of the focus change affects it's state
		dojo.forEach(["_onFocus", "_onBlur"], function(ap){
			this.connect(this, ap, "_setStateClass");
		}, this);

		// Events on sub nodes within the widget
		for(var ap in this.cssStateNodes){
			this._trackMouseState(this[ap], this.cssStateNodes[ap]);
		}
		// Set state initially; there's probably no hover/active/focus state but widget might be
		// disabled/readonly so we want to set CSS classes for those conditions.
		this._setStateClass();
	},

	_cssMouseEvent: function(/*Event*/ event){
		// summary:
		//	Sets _hovering and _active properties depending on mouse state,
		//	then calls _setStateClass() to set appropriate CSS classes for this.domNode.

		if(!this.disabled){
			switch(event.type){
				case "mouseenter":
				case "mouseover":	// generated on non-IE browsers even though we connected to mouseenter
					this._hovering = true;
					this._active = this._mouseDown;
					break;

				case "mouseleave":
				case "mouseout":	// generated on non-IE browsers even though we connected to mouseleave
					this._hovering = false;
					this._active = false;
					break;

				case "mousedown" :
					this._active = true;
					this._mouseDown = true;
					// Set a global event to handle mouseup, so it fires properly
					// even if the cursor leaves this.domNode before the mouse up event.
					// Alternately could set active=false on mouseout.
					var mouseUpConnector = this.connect(dojo.body(), "onmouseup", function(){
						this._active = false;
						this._mouseDown = false;
						this._setStateClass();
						this.disconnect(mouseUpConnector);
					});
					break;
			}
			this._setStateClass();
		}
	},

	_setStateClass: function(){
		// summary:
		//		Update the visual state of the widget by setting the css classes on this.domNode
		//		(or this.stateNode if defined) by combining this.baseClass with
		//		various suffixes that represent the current widget state(s).
		//
		// description:
		//		In the case where a widget has multiple
		//		states, it sets the class based on all possible
		//	 	combinations.  For example, an invalid form widget that is being hovered
		//		will be "dijitInput dijitInputInvalid dijitInputHover dijitInputInvalidHover".
		//
		//		The widget may have one or more of the following states, determined
		//		by this.state, this.checked, this.valid, and this.selected:
		//			- Error - ValidationTextBox sets this.state to "Error" if the current input value is invalid
		//			- Checked - ex: a checkmark or a ToggleButton in a checked state, will have this.checked==true
		//			- Selected - ex: currently selected tab will have this.selected==true
		//
		//		In addition, it may have one or more of the following states,
		//		based on this.disabled and flags set in _onMouse (this._active, this._hovering, this._focused):
		//			- Disabled	- if the widget is disabled
		//			- Active		- if the mouse (or space/enter key?) is being pressed down
		//			- Focused		- if the widget has focus
		//			- Hover		- if the mouse is over the widget

		// Compute new set of classes
		var newStateClasses = this.baseClass.split(" ");

		function multiply(modifier){
			newStateClasses = newStateClasses.concat(dojo.map(newStateClasses, function(c){ return c+modifier; }), "dijit"+modifier);
		}

		if(!this.isLeftToRight()){
			// For RTL mode we need to set an addition class like dijitTextBoxRtl.
			multiply("Rtl");
		}

		if(this.checked){
			multiply("Checked");
		}
		if(this.state){
			multiply(this.state);
		}
		if(this.selected){
			multiply("Selected");
		}

		if(this.disabled){
			multiply("Disabled");
		}else if(this.readOnly){
			multiply("ReadOnly");
		}else{
			if(this._active){
				multiply("Active");
			}else if(this._hovering){
				multiply("Hover");
			}
		}

		if(this._focused){
			multiply("Focused");
		}

		// Remove old state classes and add new ones.
		// For performance concerns we only write into domNode.className once.
		var tn = this.stateNode || this.domNode,
			classHash = {};	// set of all classes (state and otherwise) for node

		dojo.forEach(tn.className.split(" "), function(c){ classHash[c] = true; });

		if("_stateClasses" in this){
			dojo.forEach(this._stateClasses, function(c){ delete classHash[c]; });
		}

		dojo.forEach(newStateClasses, function(c){ classHash[c] = true; });

		var newClasses = [];
		for(var c in classHash){
			newClasses.push(c);
		}
		tn.className = newClasses.join(" ");

		this._stateClasses = newStateClasses;
	},

	_trackMouseState: function(/*DomNode*/ node, /*String*/ clazz){
		// summary:
		//		Track mouse/focus events on specified node and set CSS class on that node to indicate
		//		current state.   Usually not called directly, but via cssStateNodes attribute.
		// description:
		//		Given class=foo, will set the following CSS class on the node
		//			- fooActive: if the user is currently pressing down the mouse button while over the node
		//			- fooHover: if the user is hovering the mouse over the node, but not pressing down a button
		//			- fooFocus: if the node is focused
		//
		//		Note that it won't set any classes if the widget is disabled.
		// node: DomNode
		//		Should be a sub-node of the widget, not the top node (this.domNode), since the top node
		//		is handled specially and automatically just by mixing in this class.
		// clazz: String
		//		CSS class name (ex: dijitSliderUpArrow).

		// Current state of node (initially false)
		// NB: setting specifically to false because dojo.toggleClass() needs true boolean as third arg
		var hovering=false, active=false, focused=false;

		var self = this,
			cn = dojo.hitch(this, "connect", node);

		function setClass(){
			var disabled = ("disabled" in self && self.disabled) || ("readonly" in self && self.readonly);
			dojo.toggleClass(node, clazz+"Hover", hovering && !active && !disabled);
			dojo.toggleClass(node, clazz+"Active", active && !disabled);
			dojo.toggleClass(node, clazz+"Focused", focused && !disabled);
		}

		// Mouse
		cn("onmouseenter", function(){
			hovering = true;
			setClass();
		});
		cn("onmouseleave", function(){
			hovering = false;
			active = false;
			setClass();
		});
		cn("onmousedown", function(){
			active = true;
			setClass();
		});
		cn("onmouseup", function(){
			active = false;
			setClass();
		});

		// Focus
		cn("onfocus", function(){
			focused = true;
			setClass();
		});
		cn("onblur", function(){
			focused = false;
			setClass();
		});

		// Just in case widget is enabled/disabled while it has focus/hover/active state.
		// Maybe this is overkill.
		this.connect(this, "set", function(name, value){
			if(name == "disabled" || name == "readOnly"){
				setClass();
			}
		});
	}
});

}

if(!dojo._hasResource["dijit._editor.selection"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.selection"] = true;
dojo.provide("dijit._editor.selection");

// FIXME:
//		all of these methods branch internally for IE. This is probably
//		sub-optimal in terms of runtime performance. We should investigate the
//		size difference for differentiating at definition time.

dojo.mixin(dijit._editor.selection, {
	getType: function(){
		// summary:
		//		Get the selection type (like dojo.doc.select.type in IE).
		if(dojo.isIE){
			return dojo.doc.selection.type.toLowerCase();
		}else{
			var stype = "text";

			// Check if the actual selection is a CONTROL (IMG, TABLE, HR, etc...).
			var oSel;
			try{
				oSel = dojo.global.getSelection();
			}catch(e){ /*squelch*/ }

			if(oSel && oSel.rangeCount == 1){
				var oRange = oSel.getRangeAt(0);
				if(	(oRange.startContainer == oRange.endContainer) &&
					((oRange.endOffset - oRange.startOffset) == 1) &&
					(oRange.startContainer.nodeType != 3 /* text node*/)
				){
					stype = "control";
				}
			}
			return stype; //String
		}
	},

	getSelectedText: function(){
		// summary:
		//		Return the text (no html tags) included in the current selection or null if no text is selected
		if(dojo.isIE){
			if(dijit._editor.selection.getType() == 'control'){
				return null;
			}
			return dojo.doc.selection.createRange().text;
		}else{
			var selection = dojo.global.getSelection();
			if(selection){
				return selection.toString(); //String
			}
		}
		return '';
	},

	getSelectedHtml: function(){
		// summary:
		//		Return the html text of the current selection or null if unavailable
		if(dojo.isIE){
			if(dijit._editor.selection.getType() == 'control'){
				return null;
			}
			return dojo.doc.selection.createRange().htmlText;
		}else{
			var selection = dojo.global.getSelection();
			if(selection && selection.rangeCount){
				var i;
				var html = "";
				for(i = 0; i < selection.rangeCount; i++){
					//Handle selections spanning ranges, such as Opera
					var frag = selection.getRangeAt(i).cloneContents();
					var div = dojo.doc.createElement("div");
					div.appendChild(frag);
					html += div.innerHTML;
				}
				return html; //String
			}
			return null;
		}
	},

	getSelectedElement: function(){
		// summary:
		//		Retrieves the selected element (if any), just in the case that
		//		a single element (object like and image or a table) is
		//		selected.
		if(dijit._editor.selection.getType() == "control"){
			if(dojo.isIE){
				var range = dojo.doc.selection.createRange();
				if(range && range.item){
					return dojo.doc.selection.createRange().item(0);
				}
			}else{
				var selection = dojo.global.getSelection();
				return selection.anchorNode.childNodes[ selection.anchorOffset ];
			}
		}
		return null;
	},

	getParentElement: function(){
		// summary:
		//		Get the parent element of the current selection
		if(dijit._editor.selection.getType() == "control"){
			var p = this.getSelectedElement();
			if(p){ return p.parentNode; }
		}else{
			if(dojo.isIE){
				var r = dojo.doc.selection.createRange();
				r.collapse(true);
				return r.parentElement();
			}else{
				var selection = dojo.global.getSelection();
				if(selection){
					var node = selection.anchorNode;
					while(node && (node.nodeType != 1)){ // not an element
						node = node.parentNode;
					}
					return node;
				}
			}
		}
		return null;
	},

	hasAncestorElement: function(/*String*/tagName /* ... */){
		// summary:
		// 		Check whether current selection has a  parent element which is
		// 		of type tagName (or one of the other specified tagName)
		// tagName: String
		//		The tag name to determine if it has an ancestor of.
		return this.getAncestorElement.apply(this, arguments) != null; //Boolean
	},

	getAncestorElement: function(/*String*/tagName /* ... */){
		// summary:
		//		Return the parent element of the current selection which is of
		//		type tagName (or one of the other specified tagName)
		// tagName: String
		//		The tag name to determine if it has an ancestor of.
		var node = this.getSelectedElement() || this.getParentElement();
		return this.getParentOfType(node, arguments); //DOMNode
	},

	isTag: function(/*DomNode*/ node, /*String[]*/ tags){
		// summary:
		//		Function to determine if a node is one of an array of tags.
		// node:
		//		The node to inspect.
		// tags:
		//		An array of tag name strings to check to see if the node matches.
		if(node && node.tagName){
			var _nlc = node.tagName.toLowerCase();
			for(var i=0; i<tags.length; i++){
				var _tlc = String(tags[i]).toLowerCase();
				if(_nlc == _tlc){
					return _tlc; // String
				}
			}
		}
		return "";
	},

	getParentOfType: function(/*DomNode*/ node, /*String[]*/ tags){
		// summary:
		//		Function to locate a parent node that matches one of a set of tags
		// node:
		//		The node to inspect.
		// tags:
		//		An array of tag name strings to check to see if the node matches.
		while(node){
			if(this.isTag(node, tags).length){
				return node; // DOMNode
			}
			node = node.parentNode;
		}
		return null;
	},

	collapse: function(/*Boolean*/beginning){
		// summary:
		//		Function to collapse (clear), the current selection
		// beginning: Boolean
		//		Boolean to indicate whether to collapse the cursor to the beginning of the selection or end.
		if(window.getSelection){
			var selection = dojo.global.getSelection();
			if(selection.removeAllRanges){ // Mozilla
				if(beginning){
					selection.collapseToStart();
				}else{
					selection.collapseToEnd();
				}
			}else{ // Safari
				// pulled from WebCore/ecma/kjs_window.cpp, line 2536
				selection.collapse(beginning);
			}
		}else if(dojo.isIE){ // IE
			var range = dojo.doc.selection.createRange();
			range.collapse(beginning);
			range.select();
		}
	},

	remove: function(){
		// summary:
		//		Function to delete the currently selected content from the document.
		var sel = dojo.doc.selection;
		if(dojo.isIE){
			if(sel.type.toLowerCase() != "none"){
				sel.clear();
			}
			return sel; //Selection
		}else{
			sel = dojo.global.getSelection();
			sel.deleteFromDocument();
			return sel; //Selection
		}
	},

	selectElementChildren: function(/*DomNode*/element,/*Boolean?*/nochangefocus){
		// summary:
		//		clear previous selection and select the content of the node
		//		(excluding the node itself)
		// element: DOMNode
		//		The element you wish to select the children content of.
		// nochangefocus: Boolean
		//		Boolean to indicate if the foxus should change or not.
		var win = dojo.global;
		var doc = dojo.doc;
		var range;
		element = dojo.byId(element);
		if(doc.selection && dojo.isIE && dojo.body().createTextRange){ // IE
			range = element.ownerDocument.body.createTextRange();
			range.moveToElementText(element);
			if(!nochangefocus){
				try{
					range.select(); // IE throws an exception here if the widget is hidden.  See #5439
				}catch(e){ /* squelch */}
			}
		}else if(win.getSelection){
			var selection = dojo.global.getSelection();
			if(dojo.isOpera){
				//Opera's selectAllChildren doesn't seem to work right
				//against <body> nodes and possibly others ... so
				//we use the W3C range API
				if(selection.rangeCount){
					range = selection.getRangeAt(0);
				}else{
					range = doc.createRange();
				}
				range.setStart(element, 0);
				range.setEnd(element,(element.nodeType == 3)?element.length:element.childNodes.length);
				selection.addRange(range);
			}else{
				selection.selectAllChildren(element);
			}
		}
	},

	selectElement: function(/*DomNode*/element,/*Boolean?*/nochangefocus){
		// summary:
		//		clear previous selection and select element (including all its children)
		// element:  DOMNode
		//		The element to select.
		// nochangefocus: Boolean
		//		Boolean indicating if the focus should be changed.  IE only.
		var range;
		var doc = dojo.doc;
		var win = dojo.global;
		element = dojo.byId(element);
		if(dojo.isIE && dojo.body().createTextRange){
			try{
				range = dojo.body().createControlRange();
				range.addElement(element);
				if(!nochangefocus){
					range.select();
				}
			}catch(e){
				this.selectElementChildren(element,nochangefocus);
			}
		}else if(dojo.global.getSelection){
			var selection = win.getSelection();
			range = doc.createRange();
			if(selection.removeAllRanges){ // Mozilla
				// FIXME: does this work on Safari?
				if(dojo.isOpera){
					//Opera works if you use the current range on
					//the selection if present.
					if(selection.getRangeAt(0)){
						range = selection.getRangeAt(0);
					}
				}
				range.selectNode(element);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
	},

	inSelection: function(node){
		// summary:
		//		This function determines if 'node' is
		//		in the current selection.
		// tags:
		//		public
		if(node){
			var newRange;
			var doc = dojo.doc;
			var range;

			if(dojo.global.getSelection){
				//WC3
				var sel = dojo.global.getSelection();
				if(sel && sel.rangeCount > 0){
					range = sel.getRangeAt(0);
				}
				if(range && range.compareBoundaryPoints && doc.createRange){
					try{
						newRange = doc.createRange();
						newRange.setStart(node, 0);
						if(range.compareBoundaryPoints(range.START_TO_END, newRange) === 1){
							return true;
						}
					}catch(e){ /* squelch */}
				}
			}else if(doc.selection){
				// Probably IE, so we can't use the range object as the pseudo
				// range doesn't implement the boundry checking, we have to 
				// use IE specific crud.
				range = doc.selection.createRange();
				try{
					newRange = node.ownerDocument.body.createControlRange();
					if(newRange){
						newRange.addElement(node);
					}
				}catch(e1){
					try{
						newRange = node.ownerDocument.body.createTextRange();
						newRange.moveToElementText(node);
					}catch(e2){/* squelch */}
				}
				if(range && newRange){
					// We can finally compare similar to W3C
					if(range.compareEndPoints("EndToStart", newRange) === 1){
						return true;
					}
				}
			}
		}
		return false; // boolean
	}

});

}

if(!dojo._hasResource["dijit._editor.range"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.range"] = true;
dojo.provide("dijit._editor.range");

dijit.range={};

dijit.range.getIndex=function(/*DomNode*/node, /*DomNode*/parent){
//	dojo.profile.start("dijit.range.getIndex");
	var ret=[], retR=[];
	var stop = parent;
	var onode = node;

	var pnode, n;
	while(node != stop){
		var i = 0;
		pnode = node.parentNode;
		while((n=pnode.childNodes[i++])){
			if(n === node){
				--i;
				break;
			}
		}
		//if(i>=pnode.childNodes.length){
			//dojo.debug("Error finding index of a node in dijit.range.getIndex");
		//}
		ret.unshift(i);
		retR.unshift(i-pnode.childNodes.length);
		node = pnode;
	}

	//normalized() can not be called so often to prevent
	//invalidating selection/range, so we have to detect
	//here that any text nodes in a row
	if(ret.length > 0 && onode.nodeType == 3){
		n = onode.previousSibling;
		while(n && n.nodeType == 3){
			ret[ret.length-1]--;
			n = n.previousSibling;
		}
		n = onode.nextSibling;
		while(n && n.nodeType == 3){
			retR[retR.length-1]++;
			n = n.nextSibling;
		}
	}
//	dojo.profile.end("dijit.range.getIndex");
	return {o: ret, r:retR};
}

dijit.range.getNode = function(/*Array*/index, /*DomNode*/parent){
	if(!dojo.isArray(index) || index.length == 0){
		return parent;
	}
	var node = parent;
//	if(!node)debugger
	dojo.every(index, function(i){
		if(i >= 0 && i < node.childNodes.length){
			node = node.childNodes[i];
		}else{
			node = null;
			//console.debug('Error: can not find node with index',index,'under parent node',parent );
			return false; //terminate dojo.every
		}
		return true; //carry on the every loop
	});

	return node;
}

dijit.range.getCommonAncestor = function(n1,n2,root){
	root = root||n1.ownerDocument.body;
	var getAncestors = function(n){
		var as=[];
		while(n){
			as.unshift(n);
			if(n !== root){
				n = n.parentNode;
			}else{
				break;
			}
		}
		return as;
	};
	var n1as = getAncestors(n1);
	var n2as = getAncestors(n2);

	var m = Math.min(n1as.length,n2as.length);
	var com = n1as[0]; //at least, one element should be in the array: the root (BODY by default)
	for(var i=1;i<m;i++){
		if(n1as[i] === n2as[i]){
			com = n1as[i]
		}else{
			break;
		}
	}
	return com;
}

dijit.range.getAncestor = function(/*DomNode*/node, /*RegEx?*/regex, /*DomNode?*/root){
	root = root || node.ownerDocument.body;
	while(node && node !== root){
		var name = node.nodeName.toUpperCase() ;
		if(regex.test(name)){
			return node;
		}

		node = node.parentNode;
	}
	return null;
}

dijit.range.BlockTagNames = /^(?:P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|DT|DE)$/;
dijit.range.getBlockAncestor = function(/*DomNode*/node, /*RegEx?*/regex, /*DomNode?*/root){
	root = root || node.ownerDocument.body;
	regex = regex || dijit.range.BlockTagNames;
	var block=null, blockContainer;
	while(node && node !== root){
		var name = node.nodeName.toUpperCase() ;
		if(!block && regex.test(name)){
			block = node;
		}
		if(!blockContainer && (/^(?:BODY|TD|TH|CAPTION)$/).test(name)){
			blockContainer = node;
		}

		node = node.parentNode;
	}
	return {blockNode:block, blockContainer:blockContainer || node.ownerDocument.body};
}

dijit.range.atBeginningOfContainer = function(/*DomNode*/container, /*DomNode*/node, /*Int*/offset){
	var atBeginning = false;
	var offsetAtBeginning = (offset == 0);
	if(!offsetAtBeginning && node.nodeType == 3){ //if this is a text node, check whether the left part is all space
		if(/^[\s\xA0]+$/.test(node.nodeValue.substr(0,offset))){
			offsetAtBeginning = true;
		}
	}
	if(offsetAtBeginning){
		var cnode = node;
		atBeginning = true;
		while(cnode && cnode !== container){
			if(cnode.previousSibling){
				atBeginning = false;
				break;
			}
			cnode = cnode.parentNode;
		}
	}
	return atBeginning;
}

dijit.range.atEndOfContainer = function(/*DomNode*/container, /*DomNode*/node, /*Int*/offset){
	var atEnd = false;
	var offsetAtEnd = (offset == (node.length || node.childNodes.length));
	if(!offsetAtEnd && node.nodeType == 3){ //if this is a text node, check whether the right part is all space
		if(/^[\s\xA0]+$/.test(node.nodeValue.substr(offset))){
			offsetAtEnd = true;
		}
	}
	if(offsetAtEnd){
		var cnode = node;
		atEnd = true;
		while(cnode && cnode !== container){
			if(cnode.nextSibling){
				atEnd = false;
				break;
			}
			cnode = cnode.parentNode;
		}
	}
	return atEnd;
}

dijit.range.adjacentNoneTextNode=function(startnode, next){
	var node = startnode;
	var len = (0-startnode.length) || 0;
	var prop = next?'nextSibling':'previousSibling';
	while(node){
		if(node.nodeType!=3){
			break;
		}
		len += node.length
		node = node[prop];
	}
	return [node,len];
}

dijit.range._w3c = Boolean(window['getSelection']);
dijit.range.create = function(/*Window?*/win){
	if(dijit.range._w3c){
		return (win || dojo.global).document.createRange();
	}else{//IE
		return new dijit.range.W3CRange;
	}
}

dijit.range.getSelection = function(/*Window*/win, /*Boolean?*/ignoreUpdate){
	if(dijit.range._w3c){
		return win.getSelection();
	}else{//IE
		var s = new dijit.range.ie.selection(win);
		if(!ignoreUpdate){
			s._getCurrentSelection();
		}
		return s;
	}
}

if(!dijit.range._w3c){
	dijit.range.ie={
		cachedSelection: {},
		selection: function(win){
			this._ranges = [];
			this.addRange = function(r, /*boolean*/internal){
				this._ranges.push(r);
				if(!internal){
					r._select();
				}
				this.rangeCount = this._ranges.length;
			};
			this.removeAllRanges = function(){
				//don't detach, the range may be used later
//				for(var i=0;i<this._ranges.length;i++){
//					this._ranges[i].detach();
//				}
				this._ranges = [];
				this.rangeCount = 0;
			};
			var _initCurrentRange = function(){
				var r = win.document.selection.createRange();
				var type=win.document.selection.type.toUpperCase();
				if(type == "CONTROL"){
					//TODO: multiple range selection(?)
					return new dijit.range.W3CRange(dijit.range.ie.decomposeControlRange(r));
				}else{
					return new dijit.range.W3CRange(dijit.range.ie.decomposeTextRange(r));
				}
			};
			this.getRangeAt = function(i){
				return this._ranges[i];
			};
			this._getCurrentSelection = function(){
				this.removeAllRanges();
				var r=_initCurrentRange();
				if(r){
					this.addRange(r, true);
				}
			};
		},
		decomposeControlRange: function(range){
			var firstnode = range.item(0), lastnode = range.item(range.length-1);
			var startContainer = firstnode.parentNode, endContainer = lastnode.parentNode;
			var startOffset = dijit.range.getIndex(firstnode, startContainer).o;
			var endOffset = dijit.range.getIndex(lastnode, endContainer).o+1;
			return [startContainer, startOffset,endContainer, endOffset];
		},
		getEndPoint: function(range, end){
			var atmrange = range.duplicate();
			atmrange.collapse(!end);
			var cmpstr = 'EndTo' + (end?'End':'Start');
			var parentNode = atmrange.parentElement();

			var startnode, startOffset, lastNode;
			if(parentNode.childNodes.length>0){
				dojo.every(parentNode.childNodes, function(node,i){
					var calOffset;
					if(node.nodeType != 3){
						atmrange.moveToElementText(node);

						if(atmrange.compareEndPoints(cmpstr,range) > 0){
							//startnode = node.previousSibling;
							if(lastNode && lastNode.nodeType == 3){
								//where shall we put the start? in the text node or after?
								startnode = lastNode;
								calOffset = true;
							}else{
								startnode = parentNode;
								startOffset = i;
								return false;
							}
						}else{
							if(i == parentNode.childNodes.length-1){
								startnode = parentNode;
								startOffset = parentNode.childNodes.length;
								return false;
							}
						}
					}else{
						if(i == parentNode.childNodes.length-1){//at the end of this node
							startnode = node;
							calOffset = true;
						}
					}
		//			try{
						if(calOffset && startnode){
							var prevnode = dijit.range.adjacentNoneTextNode(startnode)[0];
							if(prevnode){
								startnode = prevnode.nextSibling;
							}else{
								startnode = parentNode.firstChild; //firstChild must be a text node
							}
							var prevnodeobj = dijit.range.adjacentNoneTextNode(startnode);
							prevnode = prevnodeobj[0];
							var lenoffset = prevnodeobj[1];
							if(prevnode){
								atmrange.moveToElementText(prevnode);
								atmrange.collapse(false);
							}else{
								atmrange.moveToElementText(parentNode);
							}
							atmrange.setEndPoint(cmpstr, range);
							startOffset = atmrange.text.length-lenoffset;

							return false;
						}
		//			}catch(e){ debugger }
					lastNode = node;
					return true;
				});
			}else{
				startnode = parentNode;
				startOffset = 0;
			}

			//if at the end of startnode and we are dealing with start container, then
			//move the startnode to nextSibling if it is a text node
			//TODO: do this for end container?
			if(!end && startnode.nodeType == 1 && startOffset == startnode.childNodes.length){
				var nextnode=startnode.nextSibling;
				if(nextnode && nextnode.nodeType == 3){
					startnode = nextnode;
					startOffset = 0;
				}
			}
			return [startnode, startOffset];
		},
		setEndPoint: function(range, container, offset){
			//text node
			var atmrange = range.duplicate(), node, len;
			if(container.nodeType!=3){ //normal node
				if(offset > 0){
					node = container.childNodes[offset-1];
					if(node){
						if(node.nodeType == 3){
							container = node;
							offset = node.length;
							//pass through
						}else{
							if(node.nextSibling && node.nextSibling.nodeType == 3){
								container=node.nextSibling;
								offset=0;
								//pass through
							}else{
								atmrange.moveToElementText(node.nextSibling?node:container);
								var parent = node.parentNode;
								var tempNode = parent.insertBefore(node.ownerDocument.createTextNode(' '), node.nextSibling);
								atmrange.collapse(false);
								parent.removeChild(tempNode);
							}
						}
					}
				}else{
					atmrange.moveToElementText(container);
					atmrange.collapse(true);
				}
			}
			if(container.nodeType == 3){
				var prevnodeobj = dijit.range.adjacentNoneTextNode(container);
				var prevnode = prevnodeobj[0];
				len = prevnodeobj[1];
				if(prevnode){
					atmrange.moveToElementText(prevnode);
					atmrange.collapse(false);
					//if contentEditable is not inherit, the above collapse won't make the end point
					//in the correctly position: it always has a -1 offset, so compensate it
					if(prevnode.contentEditable!='inherit'){
						len++;
					}
				}else{
					atmrange.moveToElementText(container.parentNode);
					atmrange.collapse(true);
				}

				offset += len;
				if(offset>0){
					if(atmrange.move('character',offset) != offset){
						console.error('Error when moving!');
					}
				}
			}

			return atmrange;
		},
		decomposeTextRange: function(range){
			var tmpary = dijit.range.ie.getEndPoint(range);
			var startContainer = tmpary[0], startOffset = tmpary[1];
			var endContainer = tmpary[0], endOffset = tmpary[1];

			if(range.htmlText.length){
				if(range.htmlText == range.text){ //in the same text node
					endOffset = startOffset+range.text.length;
				}else{
					tmpary = dijit.range.ie.getEndPoint(range,true);
					endContainer = tmpary[0], endOffset = tmpary[1];
//					if(startContainer.tagName == "BODY"){
//						startContainer = startContainer.firstChild;
//					}
				}
			}
			return [startContainer, startOffset, endContainer, endOffset];
		},
		setRange: function(range, startContainer,
			startOffset, endContainer, endOffset, collapsed){
			var start=dijit.range.ie.setEndPoint(range, startContainer, startOffset);

			range.setEndPoint('StartToStart',start);
			if(!collapsed){
				var end=dijit.range.ie.setEndPoint(range, endContainer, endOffset);
			}
			range.setEndPoint('EndToEnd',end || start);

			return range;
		}
	}

dojo.declare("dijit.range.W3CRange",null, {
	constructor: function(){
		if(arguments.length>0){
			this.setStart(arguments[0][0],arguments[0][1]);
			this.setEnd(arguments[0][2],arguments[0][3]);
		}else{
			this.commonAncestorContainer = null;
			this.startContainer = null;
			this.startOffset = 0;
			this.endContainer = null;
			this.endOffset = 0;
			this.collapsed = true;
		}
	},
	_updateInternal: function(){
		if(this.startContainer !== this.endContainer){
			this.commonAncestorContainer = dijit.range.getCommonAncestor(this.startContainer, this.endContainer);
		}else{
			this.commonAncestorContainer = this.startContainer;
		}
		this.collapsed = (this.startContainer === this.endContainer) && (this.startOffset == this.endOffset);
	},
	setStart: function(node, offset){
		offset=parseInt(offset);
		if(this.startContainer === node && this.startOffset == offset){
			return;
		}
		delete this._cachedBookmark;

		this.startContainer = node;
		this.startOffset = offset;
		if(!this.endContainer){
			this.setEnd(node, offset);
		}else{
			this._updateInternal();
		}
	},
	setEnd: function(node, offset){
		offset=parseInt(offset);
		if(this.endContainer === node && this.endOffset == offset){
			return;
		}
		delete this._cachedBookmark;

		this.endContainer = node;
		this.endOffset = offset;
		if(!this.startContainer){
			this.setStart(node, offset);
		}else{
			this._updateInternal();
		}
	},
	setStartAfter: function(node, offset){
		this._setPoint('setStart', node, offset, 1);
	},
	setStartBefore: function(node, offset){
		this._setPoint('setStart', node, offset, 0);
	},
	setEndAfter: function(node, offset){
		this._setPoint('setEnd', node, offset, 1);
	},
	setEndBefore: function(node, offset){
		this._setPoint('setEnd', node, offset, 0);
	},
	_setPoint: function(what, node, offset, ext){
		var index = dijit.range.getIndex(node, node.parentNode).o;
		this[what](node.parentNode, index.pop()+ext);
	},
	_getIERange: function(){
		var r = (this._body || this.endContainer.ownerDocument.body).createTextRange();
		dijit.range.ie.setRange(r, this.startContainer, this.startOffset, this.endContainer, this.endOffset, this.collapsed);
		return r;
	},
	getBookmark: function(body){
		this._getIERange();
		return this._cachedBookmark;
	},
	_select: function(){
		var r = this._getIERange();
		r.select();
	},
	deleteContents: function(){
		var r = this._getIERange();
		r.pasteHTML('');
		this.endContainer = this.startContainer;
		this.endOffset = this.startOffset;
		this.collapsed = true;
	},
	cloneRange: function(){
		var r = new dijit.range.W3CRange([this.startContainer,this.startOffset,
			this.endContainer,this.endOffset]);
		r._body = this._body;
		return r;
	},
	detach: function(){
		this._body = null;
		this.commonAncestorContainer = null;
		this.startContainer = null;
		this.startOffset = 0;
		this.endContainer = null;
		this.endOffset = 0;
		this.collapsed = true;
}
});
} //if(!dijit.range._w3c)

}

if(!dojo._hasResource["dijit._editor.html"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.html"] = true;
dojo.provide("dijit._editor.html");

dijit._editor.escapeXml=function(/*String*/str, /*Boolean?*/noSingleQuotes){
	// summary:
	//		Adds escape sequences for special characters in XML: &<>"'
	//		Optionally skips escapes for single quotes
	str = str.replace(/&/gm, "&amp;").replace(/</gm, "&lt;").replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
	if(!noSingleQuotes){
		str = str.replace(/'/gm, "&#39;");
	}
	return str; // string
};

dijit._editor.getNodeHtml=function(/* DomNode */node){
	var output;
	switch(node.nodeType){
		case 1: //element node
			var lName = node.nodeName.toLowerCase();
			if(!lName || lName.charAt(0) == "/"){
				// IE does some strange things with malformed HTML input, like
				// treating a close tag </span> without an open tag <span>, as
				// a new tag with tagName of /span.  Corrupts output HTML, remove
				// them.  Other browsers don't prefix tags that way, so will
				// never show up.
				return "";
			}
			output = '<' + lName;

			//store the list of attributes and sort it to have the
			//attributes appear in the dictionary order
			var attrarray = [];
			var attr;
			if(dojo.isIE && node.outerHTML){
				var s = node.outerHTML;
				s = s.substr(0, s.indexOf('>'))
					.replace(/(['"])[^"']*\1/g, ''); //to make the following regexp safe
				var reg = /(\b\w+)\s?=/g;
				var m, key;
				while((m = reg.exec(s))){
					key = m[1];
					if(key.substr(0,3) != '_dj'){
						if(key == 'src' || key == 'href'){
							if(node.getAttribute('_djrealurl')){
								attrarray.push([key,node.getAttribute('_djrealurl')]);
								continue;
							}
						}
						var val, match;
						switch(key){
							case 'style':
								val = node.style.cssText.toLowerCase();
								break;
							case 'class':
								val = node.className;
								break;
							case 'width':
								if(lName === "img"){
									// This somehow gets lost on IE for IMG tags and the like
									// and we have to find it in outerHTML, known IE oddity.
									match=/width=(\S+)/i.exec(s);
									if(match){
										val = match[1];
									}
									break;
								}
							case 'height':
								if(lName === "img"){
									// This somehow gets lost on IE for IMG tags and the like
									// and we have to find it in outerHTML, known IE oddity.
									match=/height=(\S+)/i.exec(s);
									if(match){
										val = match[1];
									}
									break;
								}
							default:
								val = node.getAttribute(key);
						}
						if(val != null){
							attrarray.push([key, val.toString()]);
						}
					}
				}
			}else{
				var i = 0;
				while((attr = node.attributes[i++])){
					//ignore all attributes starting with _dj which are
					//internal temporary attributes used by the editor
					var n = attr.name;
					if(n.substr(0,3) != '_dj' /*&&
						(attr.specified == undefined || attr.specified)*/){
						var v = attr.value;
						if(n == 'src' || n == 'href'){
							if(node.getAttribute('_djrealurl')){
								v = node.getAttribute('_djrealurl');
							}
						}
						attrarray.push([n,v]);
					}
				}
			}
			attrarray.sort(function(a,b){
				return a[0] < b[0] ? -1 : (a[0] == b[0] ? 0 : 1);
			});
			var j = 0;
			while((attr = attrarray[j++])){
				output += ' ' + attr[0] + '="' +
					(dojo.isString(attr[1]) ? dijit._editor.escapeXml(attr[1], true) : attr[1]) + '"';
			}
			if(lName === "script"){
				// Browsers handle script tags differently in how you get content,
				// but innerHTML always seems to work, so insert its content that way
				// Yes, it's bad to allow script tags in the editor code, but some people
				// seem to want to do it, so we need to at least return them right.
				// other plugins/filters can strip them.
				output += '>' + node.innerHTML +'</' + lName + '>';
			}else{
				if(node.childNodes.length){
					output += '>' + dijit._editor.getChildrenHtml(node)+'</' + lName +'>';
				}else{
					switch(lName){
						case 'br':
						case 'hr':
						case 'img':
						case 'input':
						case 'base':
						case 'meta':
						case 'area':
						case 'basefont':
							// These should all be singly closed
							output += ' />';
							break;
						default:
							// Assume XML style separate closure for everything else.
							output += '></' + lName + '>';
					}
				}
			}
			break;
		case 4: // cdata
		case 3: // text
			// FIXME:
			output = dijit._editor.escapeXml(node.nodeValue, true);
			break;
		case 8: //comment
			// FIXME:
			output = '<!--' + dijit._editor.escapeXml(node.nodeValue, true) + '-->';
			break;
		default:
			output = "<!-- Element not recognized - Type: " + node.nodeType + " Name: " + node.nodeName + "-->";
	}
	return output;
};

dijit._editor.getChildrenHtml = function(/* DomNode */dom){
	// summary:
	//		Returns the html content of a DomNode and children
	var out = "";
	if(!dom){ return out; }
	var nodes = dom["childNodes"] || dom;

	//IE issue.
	//If we have an actual node we can check parent relationships on for IE,
	//We should check, as IE sometimes builds invalid DOMS.  If no parent, we can't check
	//And should just process it and hope for the best.
	var checkParent = !dojo.isIE || nodes !== dom;

	var node, i = 0;
	while((node = nodes[i++])){
		//IE is broken.  DOMs are supposed to be a tree.  But in the case of malformed HTML, IE generates a graph
		//meaning one node ends up with multiple references (multiple parents).  This is totally wrong and invalid, but
		//such is what it is.  We have to keep track and check for this because otherise the source output HTML will have dups.
		//No other browser generates a graph.  Leave it to IE to break a fundamental DOM rule.  So, we check the parent if we can
		//If we can't, nothing more we can do other than walk it.
		if(!checkParent || node.parentNode == dom){
			out += dijit._editor.getNodeHtml(node);
		}
	}
	return out; // String
};

}

if(!dojo._hasResource["dijit._editor.RichText"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.RichText"] = true;
dojo.provide("dijit._editor.RichText");







// used to restore content when user leaves this page then comes back
// but do not try doing dojo.doc.write if we are using xd loading.
// dojo.doc.write will only work if RichText.js is included in the dojo.js
// file. If it is included in dojo.js and you want to allow rich text saving
// for back/forward actions, then set dojo.config.allowXdRichTextSave = true.
if(!dojo.config["useXDomain"] || dojo.config["allowXdRichTextSave"]){
	if(dojo._postLoad){
		(function(){
			var savetextarea = dojo.doc.createElement('textarea');
			savetextarea.id = dijit._scopeName + "._editor.RichText.savedContent";
			dojo.style(savetextarea, {
				display:'none',
				position:'absolute',
				top:"-100px",
				height:"3px",
				width:"3px"
			});
			dojo.body().appendChild(savetextarea);
		})();
	}else{
		//dojo.body() is not available before onLoad is fired
		try{
			dojo.doc.write('<textarea id="' + dijit._scopeName + '._editor.RichText.savedContent" ' +
				'style="display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;"></textarea>');
		}catch(e){ }
	}
}

dojo.declare("dijit._editor.RichText", [dijit._Widget, dijit._CssStateMixin], {
	constructor: function(params){
		// summary:
		//		dijit._editor.RichText is the core of dijit.Editor, which provides basic
		//		WYSIWYG editing features.
		//
		// description:
		//		dijit._editor.RichText is the core of dijit.Editor, which provides basic
		//		WYSIWYG editing features. It also encapsulates the differences
		//		of different js engines for various browsers.  Do not use this widget
		//		with an HTML &lt;TEXTAREA&gt; tag, since the browser unescapes XML escape characters,
		//		like &lt;.  This can have unexpected behavior and lead to security issues
		//		such as scripting attacks.
		//
		// tags:
		//		private

		// contentPreFilters: Function(String)[]
		//		Pre content filter function register array.
		//		these filters will be executed before the actual
		//		editing area gets the html content.
		this.contentPreFilters = [];

		// contentPostFilters: Function(String)[]
		//		post content filter function register array.
		//		These will be used on the resulting html
		//		from contentDomPostFilters. The resulting
		//		content is the final html (returned by getValue()).
		this.contentPostFilters = [];

		// contentDomPreFilters: Function(DomNode)[]
		//		Pre content dom filter function register array.
		//		These filters are applied after the result from
		//		contentPreFilters are set to the editing area.
		this.contentDomPreFilters = [];

		// contentDomPostFilters: Function(DomNode)[]
		//		Post content dom filter function register array.
		//		These filters are executed on the editing area dom.
		//		The result from these will be passed to contentPostFilters.
		this.contentDomPostFilters = [];

		// editingAreaStyleSheets: dojo._URL[]
		//		array to store all the stylesheets applied to the editing area
		this.editingAreaStyleSheets = [];

		// Make a copy of this.events before we start writing into it, otherwise we
		// will modify the prototype which leads to bad things on pages w/multiple editors
		this.events = [].concat(this.events);

		this._keyHandlers = {};
		this.contentPreFilters.push(dojo.hitch(this, "_preFixUrlAttributes"));
		if(dojo.isMoz){
			this.contentPreFilters.push(this._normalizeFontStyle);
			this.contentPostFilters.push(this._removeMozBogus);
		}
		if(dojo.isWebKit){
			// Try to clean up WebKit bogus artifacts.  The inserted classes
			// made by WebKit sometimes messes things up.
			this.contentPreFilters.push(this._removeWebkitBogus);
			this.contentPostFilters.push(this._removeWebkitBogus);
		}
		if(dojo.isIE){
			// IE generates <strong> and <em> but we want to normalize to <b> and <i>
			this.contentPostFilters.push(this._normalizeFontStyle);
		}
		//this.contentDomPostFilters.push(this._postDomFixUrlAttributes);

		if(params && dojo.isString(params.value)){
			this.value = params.value;
		}

		this.onLoadDeferred = new dojo.Deferred();
	},

	baseClass: "dijitEditor",

	// inheritWidth: Boolean
	//		whether to inherit the parent's width or simply use 100%
	inheritWidth: false,

	// focusOnLoad: [deprecated] Boolean
	//		Focus into this widget when the page is loaded
	focusOnLoad: false,

	// name: String?
	//		Specifies the name of a (hidden) <textarea> node on the page that's used to save
	//		the editor content on page leave.   Used to restore editor contents after navigating
	//		to a new page and then hitting the back button.
	name: "",

	// styleSheets: [const] String
	//		semicolon (";") separated list of css files for the editing area
	styleSheets: "",

	// _content: [private] String
	//		temporary content storage
	_content: "",

	// height: String
	//		Set height to fix the editor at a specific height, with scrolling.
	//		By default, this is 300px.  If you want to have the editor always
	//		resizes to accommodate the content, use AlwaysShowToolbar plugin
	//		and set height="".  If this editor is used within a layout widget,
	//		set height="100%".
	height: "300px",

	// minHeight: String
	//		The minimum height that the editor should have.
	minHeight: "1em",

	// isClosed: [private] Boolean
	isClosed: true,

	// isLoaded: [private] Boolean
	isLoaded: false,

	// _SEPARATOR: [private] String
	//		Used to concat contents from multiple editors into a single string,
	//		so they can be saved into a single <textarea> node.  See "name" attribute.
	_SEPARATOR: "@@**%%__RICHTEXTBOUNDRY__%%**@@",

	// onLoadDeferred: [protected] dojo.Deferred
	//		Deferred which is fired when the editor finishes loading
	onLoadDeferred: null,

	// isTabIndent: Boolean
	//		Make tab key and shift-tab indent and outdent rather than navigating.
	//		Caution: sing this makes web pages inaccessible to users unable to use a mouse.
	isTabIndent: false,

	// disableSpellCheck: [const] Boolean
	//		When true, disables the browser's native spell checking, if supported.
	//		Works only in Firefox.
	disableSpellCheck: false,

	postCreate: function(){
		if("textarea" == this.domNode.tagName.toLowerCase()){
			console.warn("RichText should not be used with the TEXTAREA tag.  See dijit._editor.RichText docs.");
		}

		this.inherited(arguments);

		dojo.publish(dijit._scopeName + "._editor.RichText::init", [this]);
		this.open();
		this.setupDefaultShortcuts();
	},

	setupDefaultShortcuts: function(){
		// summary:
		//		Add some default key handlers
		// description:
		// 		Overwrite this to setup your own handlers. The default
		// 		implementation does not use Editor commands, but directly
		//		executes the builtin commands within the underlying browser
		//		support.
		// tags:
		//		protected
		var exec = dojo.hitch(this, function(cmd, arg){
			return function(){
				return !this.execCommand(cmd,arg);
			};
		});

		var ctrlKeyHandlers = {
			b: exec("bold"),
			i: exec("italic"),
			u: exec("underline"),
			a: exec("selectall"),
			s: function(){ this.save(true); },
			m: function(){ this.isTabIndent = !this.isTabIndent; },

			"1": exec("formatblock", "h1"),
			"2": exec("formatblock", "h2"),
			"3": exec("formatblock", "h3"),
			"4": exec("formatblock", "h4"),

			"\\": exec("insertunorderedlist")
		};

		if(!dojo.isIE){
			ctrlKeyHandlers.Z = exec("redo"); //FIXME: undo?
		}

		for(var key in ctrlKeyHandlers){
			this.addKeyHandler(key, true, false, ctrlKeyHandlers[key]);
		}
	},

	// events: [private] String[]
	//		 events which should be connected to the underlying editing area
	events: ["onKeyPress", "onKeyDown", "onKeyUp", "onClick"],

	// captureEvents: [deprecated] String[]
	//		 Events which should be connected to the underlying editing
	//		 area, events in this array will be addListener with
	//		 capture=true.
	// TODO: looking at the code I don't see any distinction between events and captureEvents,
	// so get rid of this for 2.0 if not sooner
	captureEvents: [],

	_editorCommandsLocalized: false,
	_localizeEditorCommands: function(){
		// summary:
		//		When IE is running in a non-English locale, the API actually changes,
		//		so that we have to say (for example) danraku instead of p (for paragraph).
		//		Handle that here.
		// tags:
		//		private
		if(this._editorCommandsLocalized){
			return;
		}
		this._editorCommandsLocalized = true;

		//in IE, names for blockformat is locale dependent, so we cache the values here

		//if the normal way fails, we try the hard way to get the list

		//do not use _cacheLocalBlockFormatNames here, as it will
		//trigger security warning in IE7

		//put p after div, so if IE returns Normal, we show it as paragraph
		//We can distinguish p and div if IE returns Normal, however, in order to detect that,
		//we have to call this.document.selection.createRange().parentElement() or such, which
		//could slow things down. Leave it as it is for now
		var formats = ['div', 'p', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'address'];
		var localhtml = "", format, i=0;
		while((format=formats[i++])){
			//append a <br> after each element to separate the elements more reliably
			if(format.charAt(1) != 'l'){
				localhtml += "<"+format+"><span>content</span></"+format+"><br/>";
			}else{
				localhtml += "<"+format+"><li>content</li></"+format+"><br/>";
			}
		}
		//queryCommandValue returns empty if we hide editNode, so move it out of screen temporary
		var div = dojo.doc.createElement('div');
		dojo.style(div, {
			position: "absolute",
			top: "-2000px"
		});
		dojo.doc.body.appendChild(div);
		div.innerHTML = localhtml;
		var node = div.firstChild;
		while(node){
			dijit._editor.selection.selectElement(node.firstChild);
			dojo.withGlobal(this.window, "selectElement", dijit._editor.selection, [node.firstChild]);
			var nativename = node.tagName.toLowerCase();
			this._local2NativeFormatNames[nativename] = document.queryCommandValue("formatblock");
			//this.queryCommandValue("formatblock");
			this._native2LocalFormatNames[this._local2NativeFormatNames[nativename]] = nativename;
			node = node.nextSibling.nextSibling;
		}
		dojo.body().removeChild(div);
	},

	open: function(/*DomNode?*/ element){
		// summary:
		//		Transforms the node referenced in this.domNode into a rich text editing
		//		node.
		// description:
		//		Sets up the editing area asynchronously. This will result in
		//		the creation and replacement with an iframe.
		//
		//		A dojo.Deferred object is created at this.onLoadDeferred, and
		//		users may attach to it to be informed when the rich-text area
		//		initialization is finalized.
		// tags:
		//		private

		if(!this.onLoadDeferred || this.onLoadDeferred.fired >= 0){
			this.onLoadDeferred = new dojo.Deferred();
		}

		if(!this.isClosed){ this.close(); }
		dojo.publish(dijit._scopeName + "._editor.RichText::open", [ this ]);

		this._content = "";
		if(arguments.length == 1 && element.nodeName){ // else unchanged
			this.domNode = element;
		}

		var dn = this.domNode;

		// "html" will hold the innerHTML of the srcNodeRef and will be used to
		// initialize the editor.
		var html;

		if(dojo.isString(this.value)){
			// Allow setting the editor content programmatically instead of
			// relying on the initial content being contained within the target
			// domNode.
			html = this.value;
			delete this.value;
			dn.innerHTML = "";
		}else if(dn.nodeName && dn.nodeName.toLowerCase() == "textarea"){
			// if we were created from a textarea, then we need to create a
			// new editing harness node.
			var ta = (this.textarea = dn);
			this.name = ta.name;
			html = ta.value;
			dn = this.domNode = dojo.doc.createElement("div");
			dn.setAttribute('widgetId', this.id);
			ta.removeAttribute('widgetId');
			dn.cssText = ta.cssText;
			dn.className += " " + ta.className;
			dojo.place(dn, ta, "before");
			var tmpFunc = dojo.hitch(this, function(){
				//some browsers refuse to submit display=none textarea, so
				//move the textarea off screen instead
				dojo.style(ta, {
					display: "block",
					position: "absolute",
					top: "-1000px"
				});

				if(dojo.isIE){ //nasty IE bug: abnormal formatting if overflow is not hidden
					var s = ta.style;
					this.__overflow = s.overflow;
					s.overflow = "hidden";
				}
			});
			if(dojo.isIE){
				setTimeout(tmpFunc, 10);
			}else{
				tmpFunc();
			}

			if(ta.form){
				dojo.connect(ta.form, "onsubmit", this, function(){
					// FIXME: should we be calling close() here instead?
					ta.value = this.getValue();
				});
			}
		}else{
			html = dijit._editor.getChildrenHtml(dn);
			dn.innerHTML = "";
		}

		var content = dojo.contentBox(dn);
		this._oldHeight = content.h;
		this._oldWidth = content.w;

		this.savedContent = html;

		// If we're a list item we have to put in a blank line to force the
		// bullet to nicely align at the top of text
		if(dn.nodeName && dn.nodeName == "LI"){
			dn.innerHTML = " <br>";
		}
	
		// Construct the editor div structure.
		this.header = dn.ownerDocument.createElement("div");
		dn.appendChild(this.header);
		this.editingArea = dn.ownerDocument.createElement("div");
		dn.appendChild(this.editingArea);
		this.footer = dn.ownerDocument.createElement("div");
		dn.appendChild(this.footer);

		// User has pressed back/forward button so we lost the text in the editor, but it's saved
		// in a hidden <textarea> (which contains the data for all the editors on this page),
		// so get editor value from there
		if(this.name !== "" && (!dojo.config["useXDomain"] || dojo.config["allowXdRichTextSave"])){
			var saveTextarea = dojo.byId(dijit._scopeName + "._editor.RichText.savedContent");
			if(saveTextarea.value !== ""){
				var datas = saveTextarea.value.split(this._SEPARATOR), i=0, dat;
				while((dat=datas[i++])){
					var data = dat.split(":");
					if(data[0] == this.name){
						html = data[1];
						datas.splice(i, 1);	// TODO: this has no effect
						break;
					}
				}
			}

			// TODO: this is troublesome if this editor has been destroyed, should have global handler.
			// TODO: need to clear <textarea> in global handler
			dojo.addOnUnload(dojo.hitch(this, "_saveContent"));
		}

		this.isClosed = false;

		var ifr = (this.editorObject = this.iframe = dojo.doc.createElement('iframe'));
		ifr.id = this.id+"_iframe";
		this._iframeSrc = this._getIframeDocTxt();
		ifr.style.border = "none";
		ifr.style.width = "100%";
		if(this._layoutMode){
			// iframe should be 100% height, thus getting it's height from surrounding
			// <div> (which has the correct height set by Editor)
			ifr.style.height = "100%";
		}else{
			if(dojo.isIE >= 7){
				if(this.height){
					ifr.style.height = this.height;
				}
				if(this.minHeight){
					ifr.style.minHeight = this.minHeight;
				}
			}else{
				ifr.style.height = this.height ? this.height : this.minHeight;
			}
		}
		ifr.frameBorder = 0;
		ifr._loadFunc = dojo.hitch( this, function(win){
			this.window = win;
			this.document = this.window.document;

			if(dojo.isIE){
				this._localizeEditorCommands();
			}
			
			// Do final setup and set initial contents of editor
			this.onLoad(html);
		});

		// Set the iframe's initial (blank) content.
		var s = 'javascript:parent.' + dijit._scopeName + '.byId("'+this.id+'")._iframeSrc';
		ifr.setAttribute('src', s);
		this.editingArea.appendChild(ifr);

		// TODO: this is a guess at the default line-height, kinda works
		if(dn.nodeName == "LI"){
			dn.lastChild.style.marginTop = "-1.2em";
		}

		dojo.addClass(this.domNode, this.baseClass);
	},

	//static cache variables shared among all instance of this class
	_local2NativeFormatNames: {},
	_native2LocalFormatNames: {},

	_getIframeDocTxt: function(){
		// summary:
		//		Generates the boilerplate text of the document inside the iframe (ie, <html><head>...</head><body/></html>).
		//		Editor content (if not blank) should be added afterwards.
		// tags:
		//		private
		var _cs = dojo.getComputedStyle(this.domNode);

		// The contents inside of <body>.  The real contents are set later via a call to setValue().
		var html = "";
		var setBodyId = true;
		if(dojo.isIE || (!this.height && !dojo.isMoz)){
			// In auto-expand mode, need a wrapper div for AlwaysShowToolbar plugin to correctly
			// expand/contract the editor as the content changes.
			html = "<div id='dijitEditorBody'></div>";
			setBodyId = false;
		}else if(dojo.isMoz){
			// workaround bug where can't select then delete text (until user types something
			// into the editor)... and/or issue where typing doesn't erase selected text
			this._cursorToStart = true;
			html = "&nbsp;";
		}

		var font = [ _cs.fontWeight, _cs.fontSize, _cs.fontFamily ].join(" ");

		// line height is tricky - applying a units value will mess things up.
		// if we can't get a non-units value, bail out.
		var lineHeight = _cs.lineHeight;
		if(lineHeight.indexOf("px") >= 0){
			lineHeight = parseFloat(lineHeight)/parseFloat(_cs.fontSize);
			// console.debug(lineHeight);
		}else if(lineHeight.indexOf("em")>=0){
			lineHeight = parseFloat(lineHeight);
		}else{
			// If we can't get a non-units value, just default
			// it to the CSS spec default of 'normal'.  Seems to
			// work better, esp on IE, than '1.0'
			lineHeight = "normal";
		}
		var userStyle = "";
		var self = this;
		this.style.replace(/(^|;)\s*(line-|font-?)[^;]+/ig, function(match){ 
			match = match.replace(/^;/ig,"") + ';'; 
			var s = match.split(":")[0];
			if(s){
				s = dojo.trim(s);
				s = s.toLowerCase();
				var i;
				var sC = "";
				for(i = 0; i < s.length; i++){
					var c = s.charAt(i);
					switch(c){
						case "-":
							i++;
							c = s.charAt(i).toUpperCase();
						default:
							sC += c;
					}
				}
				dojo.style(self.domNode, sC, "");
			}
			userStyle += match + ';'; 
		});


		// need to find any associated label element and update iframe document title
		var label=dojo.query('label[for="'+this.id+'"]');

		return [
			this.isLeftToRight() ? "<html>\n<head>\n" : "<html dir='rtl'>\n<head>\n",
			(dojo.isMoz && label.length ? "<title>" + label[0].innerHTML + "</title>\n" : ""),
			"<meta http-equiv='Content-Type' content='text/html'>\n",
			"<style>\n",
			"\tbody,html {\n",
			"\t\tbackground:transparent;\n",
			"\t\tpadding: 1px 0 0 0;\n",
			"\t\tmargin: -1px 0 0 0;\n", // remove extraneous vertical scrollbar on safari and firefox

			// Set the html/body sizing.  Webkit always needs this, other browsers
			// only set it when height is defined (not auto-expanding), otherwise 
			// scrollers do not appear.
			((dojo.isWebKit)?"\t\twidth: 100%;\n":""),
			((dojo.isWebKit)?"\t\theight: 100%;\n":""),
			"\t}\n",
			
			// TODO: left positioning will cause contents to disappear out of view
			//	   if it gets too wide for the visible area
			"\tbody{\n",
			"\t\ttop:0px;\n",
			"\t\tleft:0px;\n",
			"\t\tright:0px;\n",
			"\t\tfont:", font, ";\n",
				((this.height||dojo.isOpera) ? "" : "\t\tposition: fixed;\n"),
			// FIXME: IE 6 won't understand min-height?
			"\t\tmin-height:", this.minHeight, ";\n",
			"\t\tline-height:", lineHeight,";\n",
			"\t}\n",
			"\tp{ margin: 1em 0; }\n",
			
			// Determine how scrollers should be applied.  In autoexpand mode (height = "") no scrollers on y at all.
			// But in fixed height mode we want both x/y scrollers.  Also, if it's using wrapping div and in auto-expand
			// (Mainly IE) we need to kill the y scroller on body and html.
			(!setBodyId && !this.height ? "\tbody,html {overflow-y: hidden;}\n" : ""),
			"\t#dijitEditorBody{overflow-x: auto; overflow-y:" + (this.height ? "auto;" : "hidden;") + "}\n",
			"\tli > ul:-moz-first-node, li > ol:-moz-first-node{ padding-top: 1.2em; }\n",
			"\tli{ min-height:1.2em; }\n",
			"</style>\n",
			this._applyEditingAreaStyleSheets(),"\n",
			"</head>\n<body ",
			(setBodyId?"id='dijitEditorBody' ":""),
			"onload='frameElement._loadFunc(window,document)' style='"+userStyle+"'>", html, "</body>\n</html>"
		].join(""); // String
	},

	_applyEditingAreaStyleSheets: function(){
		// summary:
		//		apply the specified css files in styleSheets
		// tags:
		//		private
		var files = [];
		if(this.styleSheets){
			files = this.styleSheets.split(';');
			this.styleSheets = '';
		}

		//empty this.editingAreaStyleSheets here, as it will be filled in addStyleSheet
		files = files.concat(this.editingAreaStyleSheets);
		this.editingAreaStyleSheets = [];

		var text='', i=0, url;
		while((url=files[i++])){
			var abstring = (new dojo._Url(dojo.global.location, url)).toString();
			this.editingAreaStyleSheets.push(abstring);
			text += '<link rel="stylesheet" type="text/css" href="'+abstring+'"/>';
		}
		return text;
	},

	addStyleSheet: function(/*dojo._Url*/ uri){
		// summary:
		//		add an external stylesheet for the editing area
		// uri:
		//		A dojo.uri.Uri pointing to the url of the external css file
		var url=uri.toString();

		//if uri is relative, then convert it to absolute so that it can be resolved correctly in iframe
		if(url.charAt(0) == '.' || (url.charAt(0) != '/' && !uri.host)){
			url = (new dojo._Url(dojo.global.location, url)).toString();
		}

		if(dojo.indexOf(this.editingAreaStyleSheets, url) > -1){
//			console.debug("dijit._editor.RichText.addStyleSheet: Style sheet "+url+" is already applied");
			return;
		}

		this.editingAreaStyleSheets.push(url);
		this.onLoadDeferred.addCallback(dojo.hitch(function(){
			if(this.document.createStyleSheet){ //IE
				this.document.createStyleSheet(url);
			}else{ //other browser
				var head = this.document.getElementsByTagName("head")[0];
				var stylesheet = this.document.createElement("link");
				stylesheet.rel="stylesheet";
				stylesheet.type="text/css";
				stylesheet.href=url;
				head.appendChild(stylesheet);
			}
		}));
	},

	removeStyleSheet: function(/*dojo._Url*/ uri){
		// summary:
		//		remove an external stylesheet for the editing area
		var url=uri.toString();
		//if uri is relative, then convert it to absolute so that it can be resolved correctly in iframe
		if(url.charAt(0) == '.' || (url.charAt(0) != '/' && !uri.host)){
			url = (new dojo._Url(dojo.global.location, url)).toString();
		}
		var index = dojo.indexOf(this.editingAreaStyleSheets, url);
		if(index == -1){
//			console.debug("dijit._editor.RichText.removeStyleSheet: Style sheet "+url+" has not been applied");
			return;
		}
		delete this.editingAreaStyleSheets[index];
		dojo.withGlobal(this.window,'query', dojo, ['link:[href="'+url+'"]']).orphan();
	},

	// disabled: Boolean
	//		The editor is disabled; the text cannot be changed.
	disabled: false,

	_mozSettingProps: {'styleWithCSS':false},
	_setDisabledAttr: function(/*Boolean*/ value){
		this.disabled = value;
		if(!this.isLoaded){ return; } // this method requires init to be complete
		value = !!value;
		if(dojo.isIE || dojo.isWebKit || dojo.isOpera){
			var preventIEfocus = dojo.isIE && (this.isLoaded || !this.focusOnLoad);
			if(preventIEfocus){ this.editNode.unselectable = "on"; }
			this.editNode.contentEditable = !value;
			if(preventIEfocus){
				var _this = this;
				setTimeout(function(){ _this.editNode.unselectable = "off"; }, 0);
			}
		}else{ //moz
			try{
				this.document.designMode=(value?'off':'on');
			}catch(e){ return; } // ! _disabledOK
			if(!value && this._mozSettingProps){
				var ps = this._mozSettingProps;
				for(var n in ps){
					if(ps.hasOwnProperty(n)){
						try{
							this.document.execCommand(n,false,ps[n]);
						}catch(e2){}
					}
				}
			}
//			this.document.execCommand('contentReadOnly', false, value);
//				if(value){
//					this.blur(); //to remove the blinking caret
//				}
		}
		this._disabledOK = true;
	},

/* Event handlers
 *****************/

	onLoad: function(/*String*/ html){
		// summary:
		//		Handler after the iframe finishes loading.
		// html: String
		//		Editor contents should be set to this value
		// tags:
		//		protected

		// TODO: rename this to _onLoad, make empty public onLoad() method, deprecate/make protected onLoadDeferred handler?

		if(!this.window.__registeredWindow){
			this.window.__registeredWindow = true;
			this._iframeRegHandle = dijit.registerIframe(this.iframe);
		}
		if(!dojo.isIE && (this.height || dojo.isMoz)){
			this.editNode=this.document.body;
		}else{
			// there's a wrapper div around the content, see _getIframeDocTxt().
			this.editNode=this.document.body.firstChild;
			var _this = this;
			if(dojo.isIE){ // #4996 IE wants to focus the BODY tag
				var tabStop = (this.tabStop = dojo.doc.createElement('<div tabIndex=-1>'));
				this.editingArea.appendChild(tabStop);
				this.iframe.onfocus = function(){ _this.editNode.setActive(); };
			}
		}
		this.focusNode = this.editNode; // for InlineEditBox


		var events = this.events.concat(this.captureEvents);
		var ap = this.iframe ? this.document : this.editNode;
		dojo.forEach(events, function(item){
			this.connect(ap, item.toLowerCase(), item);
		}, this);

		if(dojo.isIE){ // IE contentEditable
			this.connect(this.document, "onmousedown", "_onIEMouseDown"); // #4996 fix focus

			// give the node Layout on IE
			// TODO: this may no longer be needed, since we've reverted IE to using an iframe,
			// not contentEditable.   Removing it would also probably remove the need for creating
			// the extra <div> in _getIframeDocTxt()
			this.editNode.style.zoom = 1.0;
		}else{
			this.connect(this.document, "onmousedown", function(){
				// Clear the moveToStart focus, as mouse 
				// down will set cursor point.  Required to properly
				// work with selection/position driven plugins and clicks in
				// the window. refs: #10678
				delete this._cursorToStart;
			}); 
		}

		if(dojo.isWebKit){
			//WebKit sometimes doesn't fire right on selections, so the toolbar
			//doesn't update right.  Therefore, help it out a bit with an additional
			//listener.  A mouse up will typically indicate a display change, so fire this
			//and get the toolbar to adapt.  Reference: #9532
			this._webkitListener = this.connect(this.document, "onmouseup", "onDisplayChanged");
		}

		if(dojo.isIE){
			// Try to make sure 'hidden' elements aren't visible in edit mode (like browsers other than IE
			// do).  See #9103
			try{
				this.document.execCommand('RespectVisibilityInDesign', true, null);
			}catch(e){/* squelch */}
		}

		this.isLoaded = true;

		this.set('disabled', this.disabled); // initialize content to editable (or not)

		// Note that setValue() call will only work after isLoaded is set to true (above)

		// Set up a function to allow delaying the setValue until a callback is fired
		// This ensures extensions like dijit.Editor have a way to hold the value set 
		// until plugins load (and do things like register filters.
		var setContent = dojo.hitch(this, function(){
			this.setValue(html);
			if(this.onLoadDeferred){
				this.onLoadDeferred.callback(true);
			}
			this.onDisplayChanged();
			if(this.focusOnLoad){
				// after the document loads, then set focus after updateInterval expires so that
				// onNormalizedDisplayChanged has run to avoid input caret issues
				dojo.addOnLoad(dojo.hitch(this, function(){ setTimeout(dojo.hitch(this, "focus"), this.updateInterval); }));
			}
			// Save off the initial content now
			this.savedContent = this.getValue(true);
		});
		if(this.setValueDeferred){
			this.setValueDeferred.addCallback(setContent);
		}else{
			setContent();
		}

	},

	onKeyDown: function(/* Event */ e){
		// summary:
		//		Handler for onkeydown event
		// tags:
		//		protected

		// we need this event at the moment to get the events from control keys
		// such as the backspace. It might be possible to add this to Dojo, so that
		// keyPress events can be emulated by the keyDown and keyUp detection.

		if(e.keyCode === dojo.keys.TAB && this.isTabIndent ){
			dojo.stopEvent(e); //prevent tab from moving focus out of editor

			// FIXME: this is a poor-man's indent/outdent. It would be
			// better if it added 4 "&nbsp;" chars in an undoable way.
			// Unfortunately pasteHTML does not prove to be undoable
			if(this.queryCommandEnabled((e.shiftKey ? "outdent" : "indent"))){
				this.execCommand((e.shiftKey ? "outdent" : "indent"));
			}
		}
		if(dojo.isIE){
			if(e.keyCode == dojo.keys.TAB && !this.isTabIndent){
				if(e.shiftKey && !e.ctrlKey && !e.altKey){
					// focus the BODY so the browser will tab away from it instead
					this.iframe.focus();
				}else if(!e.shiftKey && !e.ctrlKey && !e.altKey){
					// focus the BODY so the browser will tab away from it instead
					this.tabStop.focus();
				}
			}else if(e.keyCode === dojo.keys.BACKSPACE && this.document.selection.type === "Control"){
				// IE has a bug where if a non-text object is selected in the editor,
				// hitting backspace would act as if the browser's back button was
				// clicked instead of deleting the object. see #1069
				dojo.stopEvent(e);
				this.execCommand("delete");
			}else if((65 <= e.keyCode && e.keyCode <= 90) ||
				(e.keyCode>=37 && e.keyCode<=40) // FIXME: get this from connect() instead!
			){ //arrow keys
				e.charCode = e.keyCode;
				this.onKeyPress(e);
			}
		}
		return true;
	},

	onKeyUp: function(e){
		// summary:
		//		Handler for onkeyup event
		// tags:
		//      callback
		return;
	},

	setDisabled: function(/*Boolean*/ disabled){
		// summary:
		//		Deprecated, use set('disabled', ...) instead.
		// tags:
		//		deprecated
		dojo.deprecated('dijit.Editor::setDisabled is deprecated','use dijit.Editor::attr("disabled",boolean) instead', 2.0);
		this.set('disabled',disabled);
	},
	_setValueAttr: function(/*String*/ value){
		// summary:
		//      Registers that attr("value", foo) should call setValue(foo)
		this.setValue(value);
	},
	_setDisableSpellCheckAttr: function(/*Boolean*/ disabled){
		if(this.document){
			dojo.attr(this.document.body, "spellcheck", !disabled);
		}else{
			// try again after the editor is finished loading
			this.onLoadDeferred.addCallback(dojo.hitch(this, function(){
				dojo.attr(this.document.body, "spellcheck", !disabled);
			}));
		}
		this.disableSpellCheck = disabled;
	},

	onKeyPress: function(e){
		// summary:
		//		Handle the various key events
		// tags:
		//		protected

		var c = (e.keyChar && e.keyChar.toLowerCase()) || e.keyCode,
			handlers = this._keyHandlers[c],
			args = arguments;

		if(handlers && !e.altKey){
			dojo.some(handlers, function(h){
				// treat meta- same as ctrl-, for benefit of mac users
				if(!(h.shift ^ e.shiftKey) && !(h.ctrl ^ (e.ctrlKey||e.metaKey))){
					if(!h.handler.apply(this, args)){
						e.preventDefault();
					}
					return true;
				}
			}, this);
		}

		// function call after the character has been inserted
		if(!this._onKeyHitch){
			this._onKeyHitch = dojo.hitch(this, "onKeyPressed");
		}
		setTimeout(this._onKeyHitch, 1);
		return true;
	},

	addKeyHandler: function(/*String*/ key, /*Boolean*/ ctrl, /*Boolean*/ shift, /*Function*/ handler){
		// summary:
		//		Add a handler for a keyboard shortcut
		// description:
		//		The key argument should be in lowercase if it is a letter character
		// tags:
		//		protected
		if(!dojo.isArray(this._keyHandlers[key])){
			this._keyHandlers[key] = [];
		}
		//TODO: would be nice to make this a hash instead of an array for quick lookups
		this._keyHandlers[key].push({
			shift: shift || false,
			ctrl: ctrl || false,
			handler: handler
		});
	},

	onKeyPressed: function(){
		// summary:
		//		Handler for after the user has pressed a key, and the display has been updated.
		//		(Runs on a timer so that it runs after the display is updated)
		// tags:
		//		private
		this.onDisplayChanged(/*e*/); // can't pass in e
	},

	onClick: function(/*Event*/ e){
		// summary:
		//		Handler for when the user clicks.
		// tags:
		//		private

		// console.info('onClick',this._tryDesignModeOn);
		this.onDisplayChanged(e);
	},

	_onIEMouseDown: function(/*Event*/ e){
		// summary:
		//		IE only to prevent 2 clicks to focus
		// tags:
		//		protected

		if(!this._focused && !this.disabled){
			this.focus();
		}
	},

	_onBlur: function(e){
		// summary:
		//		Called from focus manager when focus has moved away from this editor
		// tags:
		//		protected

		// console.info('_onBlur')

		this.inherited(arguments);
		var _c=this.getValue(true);

		if(_c!=this.savedContent){
			this.onChange(_c);
			this.savedContent=_c;
		}
	},
	_onFocus: function(/*Event*/ e){
		// summary:
		//		Called from focus manager when focus has moved into this editor
		// tags:
		//		protected

		// console.info('_onFocus')
		if(!this.disabled){
			if(!this._disabledOK){
				this.set('disabled', false);
			}
			this.inherited(arguments);
		}
	},

	// TODO: why is this needed - should we deprecate this ?
	blur: function(){
		// summary:
		//		Remove focus from this instance.
		// tags:
		//		deprecated
		if(!dojo.isIE && this.window.document.documentElement && this.window.document.documentElement.focus){
			this.window.document.documentElement.focus();
		}else if(dojo.doc.body.focus){
			dojo.doc.body.focus();
		}
	},

	focus: function(){
		// summary:
		//		Move focus to this editor
		if(!this.isLoaded){
			this.focusOnLoad = true;
			return;
		}
		if(this._cursorToStart){ 
			delete this._cursorToStart;
			if(this.editNode.childNodes){
				this.placeCursorAtStart(); // this calls focus() so return
				return;
			}
		}
		if(!dojo.isIE){
			dijit.focus(this.iframe);
		}else if(this.editNode && this.editNode.focus){
			// editNode may be hidden in display:none div, lets just punt in this case
			//this.editNode.focus(); -> causes IE to scroll always (strict and quirks mode) to the top the Iframe
			// if we fire the event manually and let the browser handle the focusing, the latest
			// cursor position is focused like in FF
			this.iframe.fireEvent('onfocus', document.createEventObject()); // createEventObject only in IE
		//	}else{
		// TODO: should we throw here?
		// console.debug("Have no idea how to focus into the editor!");
		}
	},

	// _lastUpdate: 0,
	updateInterval: 200,
	_updateTimer: null,
	onDisplayChanged: function(/*Event*/ e){
		// summary:
		//		This event will be fired everytime the display context
		//		changes and the result needs to be reflected in the UI.
		// description:
		//		If you don't want to have update too often,
		//		onNormalizedDisplayChanged should be used instead
		// tags:
		//		private

		// var _t=new Date();
		if(this._updateTimer){
			clearTimeout(this._updateTimer);
		}
		if(!this._updateHandler){
			this._updateHandler = dojo.hitch(this,"onNormalizedDisplayChanged");
		}
		this._updateTimer = setTimeout(this._updateHandler, this.updateInterval);
	},
	onNormalizedDisplayChanged: function(){
		// summary:
		//		This event is fired every updateInterval ms or more
		// description:
		//		If something needs to happen immediately after a
		//		user change, please use onDisplayChanged instead.
		// tags:
		//		private
		delete this._updateTimer;
	},
	onChange: function(newContent){
		// summary:
		//		This is fired if and only if the editor loses focus and
		//		the content is changed.
	},
	_normalizeCommand: function(/*String*/ cmd, /*Anything?*/argument){
		// summary:
		//		Used as the advice function by dojo.connect to map our
		//		normalized set of commands to those supported by the target
		//		browser.
		// tags:
		//		private

		var command = cmd.toLowerCase();
		if(command == "formatblock"){
			if(dojo.isSafari && argument === undefined){ command = "heading"; }
		}else if(command == "hilitecolor" && !dojo.isMoz){
			command = "backcolor";
		}

		return command;
	},

	_qcaCache: {},
	queryCommandAvailable: function(/*String*/ command){
		// summary:
		//		Tests whether a command is supported by the host. Clients
		//		SHOULD check whether a command is supported before attempting
		//		to use it, behaviour for unsupported commands is undefined.
		// command:
		//		The command to test for
		// tags:
		//		private

		// memoizing version. See _queryCommandAvailable for computing version
		var ca = this._qcaCache[command];
		if(ca !== undefined){ return ca; }
		return (this._qcaCache[command] = this._queryCommandAvailable(command));
	},

	_queryCommandAvailable: function(/*String*/ command){
		// summary:
		//		See queryCommandAvailable().
		// tags:
		//		private

		var ie = 1;
		var mozilla = 1 << 1;
		var webkit = 1 << 2;
		var opera = 1 << 3;
		var webkit420 = 1 << 4;

		function isSupportedBy(browsers){
			return {
				ie: Boolean(browsers & ie),
				mozilla: Boolean(browsers & mozilla),
				webkit: Boolean(browsers & webkit),
				webkit420: Boolean(browsers & webkit420),
				opera: Boolean(browsers & opera)
			};
		}

		var supportedBy = null;

		switch(command.toLowerCase()){
			case "bold": case "italic": case "underline":
			case "subscript": case "superscript":
			case "fontname": case "fontsize":
			case "forecolor": case "hilitecolor":
			case "justifycenter": case "justifyfull": case "justifyleft":
			case "justifyright": case "delete": case "selectall": case "toggledir":
				supportedBy = isSupportedBy(mozilla | ie | webkit | opera);
				break;

			case "createlink": case "unlink": case "removeformat":
			case "inserthorizontalrule": case "insertimage":
			case "insertorderedlist": case "insertunorderedlist":
			case "indent": case "outdent": case "formatblock":
			case "inserthtml": case "undo": case "redo": case "strikethrough": case "tabindent":
				supportedBy = isSupportedBy(mozilla | ie | opera | webkit420);
				break;

			case "blockdirltr": case "blockdirrtl":
			case "dirltr": case "dirrtl":
			case "inlinedirltr": case "inlinedirrtl":
				supportedBy = isSupportedBy(ie);
				break;
			case "cut": case "copy": case "paste":
				supportedBy = isSupportedBy( ie | mozilla | webkit420);
				break;

			case "inserttable":
				supportedBy = isSupportedBy(mozilla | ie);
				break;

			case "insertcell": case "insertcol": case "insertrow":
			case "deletecells": case "deletecols": case "deleterows":
			case "mergecells": case "splitcell":
				supportedBy = isSupportedBy(ie | mozilla);
				break;

			default: return false;
		}

		return (dojo.isIE && supportedBy.ie) ||
			(dojo.isMoz && supportedBy.mozilla) ||
			(dojo.isWebKit && supportedBy.webkit) ||
			(dojo.isWebKit > 420 && supportedBy.webkit420) ||
			(dojo.isOpera && supportedBy.opera);	// Boolean return true if the command is supported, false otherwise
	},

	execCommand: function(/*String*/ command, argument){
		// summary:
		//		Executes a command in the Rich Text area
		// command:
		//		The command to execute
		// argument:
		//		An optional argument to the command
		// tags:
		//		protected
		var returnValue;

		//focus() is required for IE to work
		//In addition, focus() makes sure after the execution of
		//the command, the editor receives the focus as expected
		this.focus();

		command = this._normalizeCommand(command, argument);


		if(argument !== undefined){
			if(command == "heading"){
				throw new Error("unimplemented");
			}else if((command == "formatblock") && dojo.isIE){
				argument = '<'+argument+'>';
			}
		}

		//Check to see if we have any over-rides for commands, they will be functions on this
		//widget of the form _commandImpl.  If we don't, fall through to the basic native
		//exec command of the browser.
		var implFunc = "_" + command + "Impl";
		if(this[implFunc]){
			returnValue = this[implFunc](argument);
		}else{
			argument = arguments.length > 1 ? argument : null;
			if(argument || command!="createlink"){
				returnValue = this.document.execCommand(command, false, argument);
			}
		}

		this.onDisplayChanged();
		return returnValue;
	},

	queryCommandEnabled: function(/*String*/ command){
		// summary:
		//		Check whether a command is enabled or not.
		// tags:
		//		protected
		if(this.disabled || !this._disabledOK){ return false; }
		command = this._normalizeCommand(command);
		if(dojo.isMoz || dojo.isWebKit){
			if(command == "unlink"){ // mozilla returns true always
				// console.debug(this._sCall("hasAncestorElement", ['a']));
				return this._sCall("hasAncestorElement", ["a"]);
			}else if(command == "inserttable"){
				return true;
			}
		}
		//see #4109
		if(dojo.isWebKit){
			if(command == "copy"){
				command = "cut";
			}else if(command == "paste"){
				return true;
			}
		}

		var elem = dojo.isIE ? this.document.selection.createRange() : this.document;
		try{
			return elem.queryCommandEnabled(command);
		}catch(e){
			//Squelch, occurs if editor is hidden on FF 3 (and maybe others.)
			return false;
		}

	},

	queryCommandState: function(command){
		// summary:
		//		Check the state of a given command and returns true or false.
		// tags:
		//		protected

		if(this.disabled || !this._disabledOK){ return false; }
		command = this._normalizeCommand(command);
		try{
			return this.document.queryCommandState(command);
		}catch(e){
			//Squelch, occurs if editor is hidden on FF 3 (and maybe others.)
			return false;
		}
	},

	queryCommandValue: function(command){
		// summary:
		//		Check the value of a given command. This matters most for
		//		custom selections and complex values like font value setting.
		// tags:
		//		protected

		if(this.disabled || !this._disabledOK){ return false; }
		var r;
		command = this._normalizeCommand(command);
		if(dojo.isIE && command == "formatblock"){
			r = this._native2LocalFormatNames[this.document.queryCommandValue(command)];
		}else if(dojo.isMoz && command === "hilitecolor"){
			var oldValue;
			try{
				oldValue = this.document.queryCommandValue("styleWithCSS");
			}catch(e){
				oldValue = false;
			}
			this.document.execCommand("styleWithCSS", false, true);
			r = this.document.queryCommandValue(command);
			this.document.execCommand("styleWithCSS", false, oldValue);
		}else{
			r = this.document.queryCommandValue(command);
		}
		return r;
	},

	// Misc.

	_sCall: function(name, args){
		// summary:
		//		Run the named method of dijit._editor.selection over the
		//		current editor instance's window, with the passed args.
		// tags:
		//		private
		return dojo.withGlobal(this.window, name, dijit._editor.selection, args);
	},

	// FIXME: this is a TON of code duplication. Why?

	placeCursorAtStart: function(){
		// summary:
		//		Place the cursor at the start of the editing area.
		// tags:
		//		private

		this.focus();

		//see comments in placeCursorAtEnd
		var isvalid=false;
		if(dojo.isMoz){
			// TODO:  Is this branch even necessary?
			var first=this.editNode.firstChild;
			while(first){
				if(first.nodeType == 3){
					if(first.nodeValue.replace(/^\s+|\s+$/g, "").length>0){
						isvalid=true;
						this._sCall("selectElement", [ first ]);
						break;
					}
				}else if(first.nodeType == 1){
					isvalid=true;
					var tg = first.tagName ? first.tagName.toLowerCase() : "";
					// Collapse before childless tags.
					if(/br|input|img|base|meta|area|basefont|hr|link/.test(tg)){
						this._sCall("selectElement", [ first ]);
					}else{
						// Collapse inside tags with children.
						this._sCall("selectElementChildren", [ first ]);
					}
					break;
				}
				first = first.nextSibling;
			}
		}else{
			isvalid=true;
			this._sCall("selectElementChildren", [ this.editNode ]);
		}
		if(isvalid){
			this._sCall("collapse", [ true ]);
		}
	},

	placeCursorAtEnd: function(){
		// summary:
		//		Place the cursor at the end of the editing area.
		// tags:
		//		private

		this.focus();

		//In mozilla, if last child is not a text node, we have to use
		// selectElementChildren on this.editNode.lastChild otherwise the
		// cursor would be placed at the end of the closing tag of
		//this.editNode.lastChild
		var isvalid=false;
		if(dojo.isMoz){
			var last=this.editNode.lastChild;
			while(last){
				if(last.nodeType == 3){
					if(last.nodeValue.replace(/^\s+|\s+$/g, "").length>0){
						isvalid=true;
						this._sCall("selectElement", [ last ]);
						break;
					}
				}else if(last.nodeType == 1){
					isvalid=true;
					if(last.lastChild){
						this._sCall("selectElement", [ last.lastChild ]);
					}else{
						this._sCall("selectElement", [ last ]);
					}
					break;
				}
				last = last.previousSibling;
			}
		}else{
			isvalid=true;
			this._sCall("selectElementChildren", [ this.editNode ]);
		}
		if(isvalid){
			this._sCall("collapse", [ false ]);
		}
	},

	getValue: function(/*Boolean?*/ nonDestructive){
		// summary:
		//		Return the current content of the editing area (post filters
		//		are applied).  Users should call attr('value') instead.
		//	nonDestructive:
		//		defaults to false. Should the post-filtering be run over a copy
		//		of the live DOM? Most users should pass "true" here unless they
		//		*really* know that none of the installed filters are going to
		//		mess up the editing session.
		// tags:
		//		private
		if(this.textarea){
			if(this.isClosed || !this.isLoaded){
				return this.textarea.value;
			}
		}

		return this._postFilterContent(null, nonDestructive);
	},
	_getValueAttr: function(){
		// summary:
		//		Hook to make attr("value") work
		return this.getValue(true);
	},

	setValue: function(/*String*/ html){
		// summary:
		//		This function sets the content. No undo history is preserved.
		//		Users should use set('value', ...) instead.
		// tags:
		//		deprecated

		// TODO: remove this and getValue() for 2.0, and move code to _setValueAttr()

		if(!this.isLoaded){
			// try again after the editor is finished loading
			this.onLoadDeferred.addCallback(dojo.hitch(this, function(){
				this.setValue(html);
			}));
			return;
		}
		this._cursorToStart = true;
		if(this.textarea && (this.isClosed || !this.isLoaded)){
			this.textarea.value=html;
		}else{
			html = this._preFilterContent(html);
			var node = this.isClosed ? this.domNode : this.editNode;

			// Use &nbsp; to avoid webkit problems where editor is disabled until the user clicks it
			if(!html && dojo.isWebKit){
				html = "&nbsp;";
			}
			node.innerHTML = html;
			this._preDomFilterContent(node);
		}
		this.onDisplayChanged();
	},

	replaceValue: function(/*String*/ html){
		// summary:
		//		This function set the content while trying to maintain the undo stack
		//		(now only works fine with Moz, this is identical to setValue in all
		//		other browsers)
		// tags:
		//		protected

		if(this.isClosed){
			this.setValue(html);
		}else if(this.window && this.window.getSelection && !dojo.isMoz){ // Safari
			// look ma! it's a totally f'd browser!
			this.setValue(html);
		}else if(this.window && this.window.getSelection){ // Moz
			html = this._preFilterContent(html);
			this.execCommand("selectall");
			if(!html){ 
				this._cursorToStart = true;
				html = "&nbsp;"; 
			}
			this.execCommand("inserthtml", html);
			this._preDomFilterContent(this.editNode);
		}else if(this.document && this.document.selection){//IE
			//In IE, when the first element is not a text node, say
			//an <a> tag, when replacing the content of the editing
			//area, the <a> tag will be around all the content
			//so for now, use setValue for IE too
			this.setValue(html);
		}
	},

	_preFilterContent: function(/*String*/ html){
		// summary:
		//		Filter the input before setting the content of the editing
		//		area. DOM pre-filtering may happen after this
		//		string-based filtering takes place but as of 1.2, this is not
		//		guaranteed for operations such as the inserthtml command.
		// tags:
		//		private

		var ec = html;
		dojo.forEach(this.contentPreFilters, function(ef){ if(ef){ ec = ef(ec); } });
		return ec;
	},
	_preDomFilterContent: function(/*DomNode*/ dom){
		// summary:
		//		filter the input's live DOM. All filter operations should be
		//		considered to be "live" and operating on the DOM that the user
		//		will be interacting with in their editing session.
		// tags:
		//		private
		dom = dom || this.editNode;
		dojo.forEach(this.contentDomPreFilters, function(ef){
			if(ef && dojo.isFunction(ef)){
				ef(dom);
			}
		}, this);
	},

	_postFilterContent: function(
		/*DomNode|DomNode[]|String?*/ dom,
		/*Boolean?*/ nonDestructive){
		// summary:
		//		filter the output after getting the content of the editing area
		//
		// description:
		//		post-filtering allows plug-ins and users to specify any number
		//		of transforms over the editor's content, enabling many common
		//		use-cases such as transforming absolute to relative URLs (and
		//		vice-versa), ensuring conformance with a particular DTD, etc.
		//		The filters are registered in the contentDomPostFilters and
		//		contentPostFilters arrays. Each item in the
		//		contentDomPostFilters array is a function which takes a DOM
		//		Node or array of nodes as its only argument and returns the
		//		same. It is then passed down the chain for further filtering.
		//		The contentPostFilters array behaves the same way, except each
		//		member operates on strings. Together, the DOM and string-based
		//		filtering allow the full range of post-processing that should
		//		be necessaray to enable even the most agressive of post-editing
		//		conversions to take place.
		//
		//		If nonDestructive is set to "true", the nodes are cloned before
		//		filtering proceeds to avoid potentially destructive transforms
		//		to the content which may still needed to be edited further.
		//		Once DOM filtering has taken place, the serialized version of
		//		the DOM which is passed is run through each of the
		//		contentPostFilters functions.
		//
		//	dom:
		//		a node, set of nodes, which to filter using each of the current
		//		members of the contentDomPostFilters and contentPostFilters arrays.
		//
		//	nonDestructive:
		//		defaults to "false". If true, ensures that filtering happens on
		//		a clone of the passed-in content and not the actual node
		//		itself.
		//
		// tags:
		//		private

		var ec;
		if(!dojo.isString(dom)){
			dom = dom || this.editNode;
			if(this.contentDomPostFilters.length){
				if(nonDestructive){
					dom = dojo.clone(dom);
				}
				dojo.forEach(this.contentDomPostFilters, function(ef){
					dom = ef(dom);
				});
			}
			ec = dijit._editor.getChildrenHtml(dom);
		}else{
			ec = dom;
		}

		if(!dojo.trim(ec.replace(/^\xA0\xA0*/, '').replace(/\xA0\xA0*$/, '')).length){
			ec = "";
		}

		//	if(dojo.isIE){
		//		//removing appended <P>&nbsp;</P> for IE
		//		ec = ec.replace(/(?:<p>&nbsp;</p>[\n\r]*)+$/i,"");
		//	}
		dojo.forEach(this.contentPostFilters, function(ef){
			ec = ef(ec);
		});

		return ec;
	},

	_saveContent: function(/*Event*/ e){
		// summary:
		//		Saves the content in an onunload event if the editor has not been closed
		// tags:
		//		private

		var saveTextarea = dojo.byId(dijit._scopeName + "._editor.RichText.savedContent");
		if(saveTextarea.value){
			saveTextarea.value += this._SEPARATOR;
		}
		saveTextarea.value += this.name + ":" + this.getValue(true);
	},


	escapeXml: function(/*String*/ str, /*Boolean*/ noSingleQuotes){
		// summary:
		//		Adds escape sequences for special characters in XML.
		//		Optionally skips escapes for single quotes
		// tags:
		//		private

		str = str.replace(/&/gm, "&amp;").replace(/</gm, "&lt;").replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
		if(!noSingleQuotes){
			str = str.replace(/'/gm, "&#39;");
		}
		return str; // string
	},

	getNodeHtml: function(/* DomNode */ node){
		// summary:
		//		Deprecated.   Use dijit._editor._getNodeHtml() instead.
		// tags:
		//		deprecated
		dojo.deprecated('dijit.Editor::getNodeHtml is deprecated','use dijit._editor.getNodeHtml instead', 2);
		return dijit._editor.getNodeHtml(node); // String
	},

	getNodeChildrenHtml: function(/* DomNode */ dom){
		// summary:
		//		Deprecated.   Use dijit._editor.getChildrenHtml() instead.
		// tags:
		//		deprecated
		dojo.deprecated('dijit.Editor::getNodeChildrenHtml is deprecated','use dijit._editor.getChildrenHtml instead', 2);
		return dijit._editor.getChildrenHtml(dom);
	},

	close: function(/*Boolean*/ save){
		// summary:
		//		Kills the editor and optionally writes back the modified contents to the
		//		element from which it originated.
		// save:
		//		Whether or not to save the changes. If false, the changes are discarded.
		// tags:
		//		private

		if(this.isClosed){return false; }

		if(!arguments.length){ save = true; }
		this._content = this.getValue();
		var changed = (this.savedContent != this._content);

		// line height is squashed for iframes
		// FIXME: why was this here? if (this.iframe){ this.domNode.style.lineHeight = null; }

		if(this.interval){ clearInterval(this.interval); }

		if(this._webkitListener){
			//Cleaup of WebKit fix: #9532
			this.disconnect(this._webkitListener);
			delete this._webkitListener;
		}

		// Guard against memory leaks on IE (see #9268)
		if(dojo.isIE){
			 this.iframe.onfocus = null;
		}
		this.iframe._loadFunc = null;

		if(this._iframeRegHandle){
			dijit.unregisterIframe(this._iframeRegHandle);
			delete this._iframeRegHandle;
		}

		if(this.textarea){
			var s = this.textarea.style;
			s.position = "";
			s.left = s.top = "";
			if(dojo.isIE){
				s.overflow = this.__overflow;
				this.__overflow = null;
			}
			this.textarea.value = save ? this._content : this.savedContent;
			dojo.destroy(this.domNode);
			this.domNode = this.textarea;
		}else{
			// if(save){
			// why we treat moz differently? comment out to fix #1061
			//		if(dojo.isMoz){
			//			var nc = dojo.doc.createElement("span");
			//			this.domNode.appendChild(nc);
			//			nc.innerHTML = this.editNode.innerHTML;
			//		}else{
			//			this.domNode.innerHTML = this._content;
			//		}
			// }

			// Note that this destroys the iframe
			this.domNode.innerHTML = save ? this._content : this.savedContent;
		}
		delete this.iframe;

		dojo.removeClass(this.domNode, this.baseClass);
		this.isClosed = true;
		this.isLoaded = false;

		delete this.editNode;
		delete this.focusNode;

		if(this.window && this.window._frameElement){
			this.window._frameElement = null;
		}

		this.window = null;
		this.document = null;
		this.editingArea = null;
		this.editorObject = null;

		return changed; // Boolean: whether the content has been modified
	},

	destroy: function(){
		if(!this.isClosed){ this.close(false); }
		this.inherited(arguments);
	},

	_removeMozBogus: function(/* String */ html){
		// summary:
		//		Post filter to remove unwanted HTML attributes generated by mozilla
		// tags:
		//		private
		return html.replace(/\stype="_moz"/gi, '').replace(/\s_moz_dirty=""/gi, '').replace(/_moz_resizing="(true|false)"/gi,''); // String
	},
	_removeWebkitBogus: function(/* String */ html){
		// summary:
		//		Post filter to remove unwanted HTML attributes generated by webkit
		// tags:
		//		private
		html = html.replace(/\sclass="webkit-block-placeholder"/gi, '');
		html = html.replace(/\sclass="apple-style-span"/gi, '');
		return html; // String
	},
	_normalizeFontStyle: function(/* String */ html){
		// summary:
		//		Convert 'strong' and 'em' to 'b' and 'i'.
		// description:
		//		Moz can not handle strong/em tags correctly, so to help
		//		mozilla and also to normalize output, convert them to 'b' and 'i'.
		//
		//		Note the IE generates 'strong' and 'em' rather than 'b' and 'i'
		// tags:
		//		private
		return html.replace(/<(\/)?strong([ \>])/gi, '<$1b$2')
			.replace(/<(\/)?em([ \>])/gi, '<$1i$2' ); // String
	},

	_preFixUrlAttributes: function(/* String */ html){
		// summary:
		//		Pre-filter to do fixing to href attributes on <a> and <img> tags
		// tags:
		//		private
		return html.replace(/(?:(<a(?=\s).*?\shref=)("|')(.*?)\2)|(?:(<a\s.*?href=)([^"'][^ >]+))/gi,
				'$1$4$2$3$5$2 _djrealurl=$2$3$5$2')
			.replace(/(?:(<img(?=\s).*?\ssrc=)("|')(.*?)\2)|(?:(<img\s.*?src=)([^"'][^ >]+))/gi,
				'$1$4$2$3$5$2 _djrealurl=$2$3$5$2'); // String
	},

	/*****************************************************************************
		The following functions implement HTML manipulation commands for various
		browser/contentEditable implementations.  The goal of them is to enforce
		standard behaviors of them.
	******************************************************************************/

	_inserthorizontalruleImpl: function(argument){
		// summary:
		//		This function implements the insertion of HTML 'HR' tags.
		//		into a point on the page.  IE doesn't to it right, so
		//		we have to use an alternate form
		// argument:
		//		arguments to the exec command, if any.
		// tags:
		//		protected
		if(dojo.isIE){
			return this._inserthtmlImpl("<hr>");
		}
		return this.document.execCommand("inserthorizontalrule", false, argument);
	},

	_unlinkImpl: function(argument){
		// summary:
		//		This function implements the unlink of an 'a' tag.
		// argument:
		//		arguments to the exec command, if any.
		// tags:
		//		protected
		if((this.queryCommandEnabled("unlink")) && (dojo.isMoz || dojo.isWebKit)){
			var a = this._sCall("getAncestorElement", [ "a" ]);
			this._sCall("selectElement", [ a ]);
			return this.document.execCommand("unlink", false, null);
		}
		return this.document.execCommand("unlink", false, argument);
	},

	_hilitecolorImpl: function(argument){
		// summary:
		//		This function implements the hilitecolor command
		// argument:
		//		arguments to the exec command, if any.
		// tags:
		//		protected
		var returnValue;
		if(dojo.isMoz){
			// mozilla doesn't support hilitecolor properly when useCSS is
			// set to false (bugzilla #279330)
			this.document.execCommand("styleWithCSS", false, true);
			returnValue = this.document.execCommand("hilitecolor", false, argument);
			this.document.execCommand("styleWithCSS", false, false);
		}else{
			returnValue = this.document.execCommand("hilitecolor", false, argument);
		}
		return returnValue;
	},

	_backcolorImpl: function(argument){
		// summary:
		//		This function implements the backcolor command
		// argument:
		//		arguments to the exec command, if any.
		// tags:
		//		protected
		if(dojo.isIE){
			// Tested under IE 6 XP2, no problem here, comment out
			// IE weirdly collapses ranges when we exec these commands, so prevent it
			//	var tr = this.document.selection.createRange();
			argument = argument ? argument : null;
		}
		return this.document.execCommand("backcolor", false, argument);
	},

	_forecolorImpl: function(argument){
		// summary:
		//		This function implements the forecolor command
		// argument:
		//		arguments to the exec command, if any.
		// tags:
		//		protected
		if(dojo.isIE){
			// Tested under IE 6 XP2, no problem here, comment out
			// IE weirdly collapses ranges when we exec these commands, so prevent it
			//	var tr = this.document.selection.createRange();
			argument = argument? argument : null;
		}
		return this.document.execCommand("forecolor", false, argument);
	},

	_inserthtmlImpl: function(argument){
		// summary:
		//		This function implements the insertion of HTML content into
		//		a point on the page.
		// argument:
		//		The content to insert, if any.
		// tags:
		//		protected
		argument = this._preFilterContent(argument);
		var rv = true;
		if(dojo.isIE){
			var insertRange = this.document.selection.createRange();
			if(this.document.selection.type.toUpperCase() == 'CONTROL'){
				var n=insertRange.item(0);
				while(insertRange.length){
					insertRange.remove(insertRange.item(0));
				}
				n.outerHTML=argument;
			}else{
				insertRange.pasteHTML(argument);
			}
			insertRange.select();
			//insertRange.collapse(true);
		}else if(dojo.isMoz && !argument.length){
			//mozilla can not inserthtml an empty html to delete current selection
			//so we delete the selection instead in this case
			this._sCall("remove"); // FIXME
		}else{
			rv = this.document.execCommand("inserthtml", false, argument);
		}
		return rv;
	},

	getHeaderHeight: function(){
		// summary:
		//		A function for obtaining the height of the header node
		return this._getNodeChildrenHeight(this.header); // Number
	},

	getFooterHeight: function(){
		// summary:
		//		A function for obtaining the height of the footer node
		return this._getNodeChildrenHeight(this.footer); // Number
	},

	_getNodeChildrenHeight: function(node){
		// summary:
		//		An internal function for computing the cumulative height of all child nodes of 'node'
		// node:
		//		The node to process the children of;
		var h = 0;
		if(node && node.childNodes){
			// IE didn't compute it right when position was obtained on the node directly is some cases, 
			// so we have to walk over all the children manually.
			var i; 
			for(i = 0; i < node.childNodes.length; i++){ 
				var size = dojo.position(node.childNodes[i]); 
				h += size.h;   
			} 
		}
		return h; // Number
	}
});

}

if(!dojo._hasResource["dijit._Container"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Container"] = true;
dojo.provide("dijit._Container");

dojo.declare("dijit._Container",
	null,
	{
		// summary:
		//		Mixin for widgets that contain a set of widget children.
		// description:
		//		Use this mixin for widgets that needs to know about and
		//		keep track of their widget children. Suitable for widgets like BorderContainer
		//		and TabContainer which contain (only) a set of child widgets.
		//
		//		It's not suitable for widgets like ContentPane
		//		which contains mixed HTML (plain DOM nodes in addition to widgets),
		//		and where contained widgets are not necessarily directly below
		//		this.containerNode.   In that case calls like addChild(node, position)
		//		wouldn't make sense.

		// isContainer: [protected] Boolean
		//		Indicates that this widget acts as a "parent" to the descendant widgets.
		//		When the parent is started it will call startup() on the child widgets.
		//		See also `isLayoutContainer`.
		isContainer: true,

		buildRendering: function(){
			this.inherited(arguments);
			if(!this.containerNode){
				// all widgets with descendants must set containerNode
	 				this.containerNode = this.domNode;
			}
		},

		addChild: function(/*dijit._Widget*/ widget, /*int?*/ insertIndex){
			// summary:
			//		Makes the given widget a child of this widget.
			// description:
			//		Inserts specified child widget's dom node as a child of this widget's
			//		container node, and possibly does other processing (such as layout).

			var refNode = this.containerNode;
			if(insertIndex && typeof insertIndex == "number"){
				var children = this.getChildren();
				if(children && children.length >= insertIndex){
					refNode = children[insertIndex-1].domNode;
					insertIndex = "after";
				}
			}
			dojo.place(widget.domNode, refNode, insertIndex);

			// If I've been started but the child widget hasn't been started,
			// start it now.  Make sure to do this after widget has been
			// inserted into the DOM tree, so it can see that it's being controlled by me,
			// so it doesn't try to size itself.
			if(this._started && !widget._started){
				widget.startup();
			}
		},

		removeChild: function(/*Widget or int*/ widget){
			// summary:
			//		Removes the passed widget instance from this widget but does
			//		not destroy it.  You can also pass in an integer indicating
			//		the index within the container to remove

			if(typeof widget == "number" && widget > 0){
				widget = this.getChildren()[widget];
			}

			if(widget){
				var node = widget.domNode;
				if(node && node.parentNode){
					node.parentNode.removeChild(node); // detach but don't destroy
				}
			}
		},

		hasChildren: function(){
			// summary:
			//		Returns true if widget has children, i.e. if this.containerNode contains something.
			return this.getChildren().length > 0;	// Boolean
		},

		destroyDescendants: function(/*Boolean*/ preserveDom){
			// summary:
			//      Destroys all the widgets inside this.containerNode,
			//      but not this widget itself
			dojo.forEach(this.getChildren(), function(child){ child.destroyRecursive(preserveDom); });
		},

		_getSiblingOfChild: function(/*dijit._Widget*/ child, /*int*/ dir){
			// summary:
			//		Get the next or previous widget sibling of child
			// dir:
			//		if 1, get the next sibling
			//		if -1, get the previous sibling
			// tags:
			//      private
			var node = child.domNode,
				which = (dir>0 ? "nextSibling" : "previousSibling");
			do{
				node = node[which];
			}while(node && (node.nodeType != 1 || !dijit.byNode(node)));
			return node && dijit.byNode(node);	// dijit._Widget
		},

		getIndexOfChild: function(/*dijit._Widget*/ child){
			// summary:
			//		Gets the index of the child in this container or -1 if not found
			return dojo.indexOf(this.getChildren(), child);	// int
		},

		startup: function(){
			// summary:
			//		Called after all the widgets have been instantiated and their
			//		dom nodes have been inserted somewhere under dojo.doc.body.
			//
			//		Widgets should override this method to do any initialization
			//		dependent on other widgets existing, and then call
			//		this superclass method to finish things off.
			//
			//		startup() in subclasses shouldn't do anything
			//		size related because the size of the widget hasn't been set yet.

			if(this._started){ return; }

			// Startup all children of this widget
			dojo.forEach(this.getChildren(), function(child){ child.startup(); });

			this.inherited(arguments);
		}
	}
);

}

if(!dojo._hasResource["dijit._KeyNavContainer"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._KeyNavContainer"] = true;
dojo.provide("dijit._KeyNavContainer");


dojo.declare("dijit._KeyNavContainer",
	dijit._Container,
	{

		// summary:
		//		A _Container with keyboard navigation of its children.
		// description:
		//		To use this mixin, call connectKeyNavHandlers() in
		//		postCreate() and call startupKeyNavChildren() in startup().
		//		It provides normalized keyboard and focusing code for Container
		//		widgets.
/*=====
		// focusedChild: [protected] Widget
		//		The currently focused child widget, or null if there isn't one
		focusedChild: null,
=====*/

		// tabIndex: Integer
		//		Tab index of the container; same as HTML tabIndex attribute.
		//		Note then when user tabs into the container, focus is immediately
		//		moved to the first item in the container.
		tabIndex: "0",

		_keyNavCodes: {},

		connectKeyNavHandlers: function(/*dojo.keys[]*/ prevKeyCodes, /*dojo.keys[]*/ nextKeyCodes){
			// summary:
			//		Call in postCreate() to attach the keyboard handlers
			//		to the container.
			// preKeyCodes: dojo.keys[]
			//		Key codes for navigating to the previous child.
			// nextKeyCodes: dojo.keys[]
			//		Key codes for navigating to the next child.
			// tags:
			//		protected

			var keyCodes = (this._keyNavCodes = {});
			var prev = dojo.hitch(this, this.focusPrev);
			var next = dojo.hitch(this, this.focusNext);
			dojo.forEach(prevKeyCodes, function(code){ keyCodes[code] = prev; });
			dojo.forEach(nextKeyCodes, function(code){ keyCodes[code] = next; });
			this.connect(this.domNode, "onkeypress", "_onContainerKeypress");
			this.connect(this.domNode, "onfocus", "_onContainerFocus");
		},

		startupKeyNavChildren: function(){
			// summary:
			//		Call in startup() to set child tabindexes to -1
			// tags:
			//		protected
			dojo.forEach(this.getChildren(), dojo.hitch(this, "_startupChild"));
		},

		addChild: function(/*dijit._Widget*/ widget, /*int?*/ insertIndex){
			// summary:
			//		Add a child to our _Container
			dijit._KeyNavContainer.superclass.addChild.apply(this, arguments);
			this._startupChild(widget);
		},

		focus: function(){
			// summary:
			//		Default focus() implementation: focus the first child.
			this.focusFirstChild();
		},

		focusFirstChild: function(){
			// summary:
			//		Focus the first focusable child in the container.
			// tags:
			//		protected
			var child = this._getFirstFocusableChild();
			if(child){ // edge case: Menu could be empty or hidden
				this.focusChild(child);
			}
		},

		focusNext: function(){
			// summary:
			//		Focus the next widget
			// tags:
			//		protected
			var child = this._getNextFocusableChild(this.focusedChild, 1);
			this.focusChild(child);
		},

		focusPrev: function(){
			// summary:
			//		Focus the last focusable node in the previous widget
			//		(ex: go to the ComboButton icon section rather than button section)
			// tags:
			//		protected
			var child = this._getNextFocusableChild(this.focusedChild, -1);
			this.focusChild(child, true);
		},

		focusChild: function(/*dijit._Widget*/ widget, /*Boolean*/ last){
			// summary:
			//		Focus widget.
			// widget:
			//		Reference to container's child widget
			// last:
			//		If true and if widget has multiple focusable nodes, focus the
			//		last one instead of the first one
			// tags:
			//		protected
			
			if(this.focusedChild && widget !== this.focusedChild){
				this._onChildBlur(this.focusedChild);
			}
			widget.focus(last ? "end" : "start");
			this.focusedChild = widget;
		},

		_startupChild: function(/*dijit._Widget*/ widget){
			// summary:
			//		Setup for each child widget
			// description:
			//		Sets tabIndex=-1 on each child, so that the tab key will 
			//		leave the container rather than visiting each child.
			// tags:
			//		private
			
			widget.set("tabIndex", "-1");
			
			this.connect(widget, "_onFocus", function(){
				// Set valid tabIndex so tabbing away from widget goes to right place, see #10272
				widget.set("tabIndex", this.tabIndex);
			});
			this.connect(widget, "_onBlur", function(){
				widget.set("tabIndex", "-1");
			});
		},

		_onContainerFocus: function(evt){
			// summary:
			//		Handler for when the container gets focus
			// description:
			//		Initially the container itself has a tabIndex, but when it gets
			//		focus, switch focus to first child...
			// tags:
			//		private

			// Note that we can't use _onFocus() because switching focus from the
			// _onFocus() handler confuses the focus.js code
			// (because it causes _onFocusNode() to be called recursively)

			// focus bubbles on Firefox,
			// so just make sure that focus has really gone to the container
			if(evt.target !== this.domNode){ return; }

			this.focusFirstChild();

			// and then set the container's tabIndex to -1,
			// (don't remove as that breaks Safari 4)
			// so that tab or shift-tab will go to the fields after/before
			// the container, rather than the container itself
			dojo.attr(this.domNode, "tabIndex", "-1");
		},

		_onBlur: function(evt){
			// When focus is moved away the container, and its descendant (popup) widgets,
			// then restore the container's tabIndex so that user can tab to it again.
			// Note that using _onBlur() so that this doesn't happen when focus is shifted
			// to one of my child widgets (typically a popup)
			if(this.tabIndex){
				dojo.attr(this.domNode, "tabIndex", this.tabIndex);
			}
			this.inherited(arguments);
		},

		_onContainerKeypress: function(evt){
			// summary:
			//		When a key is pressed, if it's an arrow key etc. then
			//		it's handled here.
			// tags:
			//		private
			if(evt.ctrlKey || evt.altKey){ return; }
			var func = this._keyNavCodes[evt.charOrCode];
			if(func){
				func();
				dojo.stopEvent(evt);
			}
		},

		_onChildBlur: function(/*dijit._Widget*/ widget){
			// summary:
			//		Called when focus leaves a child widget to go
			//		to a sibling widget.
			// tags:
			//		protected
		},

		_getFirstFocusableChild: function(){
			// summary:
			//		Returns first child that can be focused
			return this._getNextFocusableChild(null, 1);	// dijit._Widget
		},

		_getNextFocusableChild: function(child, dir){
			// summary:
			//		Returns the next or previous focusable child, compared
			//		to "child"
			// child: Widget
			//		The current widget
			// dir: Integer
			//		* 1 = after
			//		* -1 = before
			if(child){
				child = this._getSiblingOfChild(child, dir);
			}
			var children = this.getChildren();
			for(var i=0; i < children.length; i++){
				if(!child){
					child = children[(dir>0) ? 0 : (children.length-1)];
				}
				if(child.isFocusable()){
					return child;	// dijit._Widget
				}
				child = this._getSiblingOfChild(child, dir);
			}
			// no focusable child found
			return null;	// dijit._Widget
		}
	}
);

}

if(!dojo._hasResource["dojo.string"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.string"] = true;
dojo.provide("dojo.string");

/*=====
dojo.string = { 
	// summary: String utilities for Dojo
};
=====*/

dojo.string.rep = function(/*String*/str, /*Integer*/num){
	//	summary:
	//		Efficiently replicate a string `n` times.
	//	str:
	//		the string to replicate
	//	num:
	//		number of times to replicate the string
	
	if(num <= 0 || !str){ return ""; }
	
	var buf = [];
	for(;;){
		if(num & 1){
			buf.push(str);
		}
		if(!(num >>= 1)){ break; }
		str += str;
	}
	return buf.join("");	// String
};

dojo.string.pad = function(/*String*/text, /*Integer*/size, /*String?*/ch, /*Boolean?*/end){
	//	summary:
	//		Pad a string to guarantee that it is at least `size` length by
	//		filling with the character `ch` at either the start or end of the
	//		string. Pads at the start, by default.
	//	text:
	//		the string to pad
	//	size:
	//		length to provide padding
	//	ch:
	//		character to pad, defaults to '0'
	//	end:
	//		adds padding at the end if true, otherwise pads at start
	//	example:
	//	|	// Fill the string to length 10 with "+" characters on the right.  Yields "Dojo++++++".
	//	|	dojo.string.pad("Dojo", 10, "+", true);

	if(!ch){
		ch = '0';
	}
	var out = String(text),
		pad = dojo.string.rep(ch, Math.ceil((size - out.length) / ch.length));
	return end ? out + pad : pad + out;	// String
};

dojo.string.substitute = function(	/*String*/		template, 
									/*Object|Array*/map, 
									/*Function?*/	transform, 
									/*Object?*/		thisObject){
	//	summary:
	//		Performs parameterized substitutions on a string. Throws an
	//		exception if any parameter is unmatched.
	//	template: 
	//		a string with expressions in the form `${key}` to be replaced or
	//		`${key:format}` which specifies a format function. keys are case-sensitive. 
	//	map:
	//		hash to search for substitutions
	//	transform: 
	//		a function to process all parameters before substitution takes
	//		place, e.g. mylib.encodeXML
	//	thisObject: 
	//		where to look for optional format function; default to the global
	//		namespace
	//	example:
	//		Substitutes two expressions in a string from an Array or Object
	//	|	// returns "File 'foo.html' is not found in directory '/temp'."
	//	|	// by providing substitution data in an Array
	//	|	dojo.string.substitute(
	//	|		"File '${0}' is not found in directory '${1}'.",
	//	|		["foo.html","/temp"]
	//	|	);
	//	|
	//	|	// also returns "File 'foo.html' is not found in directory '/temp'."
	//	|	// but provides substitution data in an Object structure.  Dotted
	//	|	// notation may be used to traverse the structure.
	//	|	dojo.string.substitute(
	//	|		"File '${name}' is not found in directory '${info.dir}'.",
	//	|		{ name: "foo.html", info: { dir: "/temp" } }
	//	|	);
	//	example:
	//		Use a transform function to modify the values:
	//	|	// returns "file 'foo.html' is not found in directory '/temp'."
	//	|	dojo.string.substitute(
	//	|		"${0} is not found in ${1}.",
	//	|		["foo.html","/temp"],
	//	|		function(str){
	//	|			// try to figure out the type
	//	|			var prefix = (str.charAt(0) == "/") ? "directory": "file";
	//	|			return prefix + " '" + str + "'";
	//	|		}
	//	|	);
	//	example:
	//		Use a formatter
	//	|	// returns "thinger -- howdy"
	//	|	dojo.string.substitute(
	//	|		"${0:postfix}", ["thinger"], null, {
	//	|			postfix: function(value, key){
	//	|				return value + " -- howdy";
	//	|			}
	//	|		}
	//	|	);

	thisObject = thisObject || dojo.global;
	transform = transform ? 
		dojo.hitch(thisObject, transform) : function(v){ return v; };

	return template.replace(/\$\{([^\s\:\}]+)(?:\:([^\s\:\}]+))?\}/g,
		function(match, key, format){
			var value = dojo.getObject(key, false, map);
			if(format){
				value = dojo.getObject(format, false, thisObject).call(thisObject, value, key);
			}
			return transform(value, key).toString();
		}); // String
};

/*=====
dojo.string.trim = function(str){
	//	summary:
	//		Trims whitespace from both sides of the string
	//	str: String
	//		String to be trimmed
	//	returns: String
	//		Returns the trimmed string
	//	description:
	//		This version of trim() was taken from [Steven Levithan's blog](http://blog.stevenlevithan.com/archives/faster-trim-javascript).
	//		The short yet performant version of this function is dojo.trim(),
	//		which is part of Dojo base.  Uses String.prototype.trim instead, if available.
	return "";	// String
}
=====*/

dojo.string.trim = String.prototype.trim ?
	dojo.trim : // aliasing to the native function
	function(str){
		str = str.replace(/^\s+/, '');
		for(var i = str.length - 1; i >= 0; i--){
			if(/\S/.test(str.charAt(i))){
				str = str.substring(0, i + 1);
				break;
			}
		}
		return str;
	};

}

if(!dojo._hasResource["dojo.date.stamp"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date.stamp"] = true;
dojo.provide("dojo.date.stamp");

// Methods to convert dates to or from a wire (string) format using well-known conventions

dojo.date.stamp.fromISOString = function(/*String*/formattedString, /*Number?*/defaultTime){
	//	summary:
	//		Returns a Date object given a string formatted according to a subset of the ISO-8601 standard.
	//
	//	description:
	//		Accepts a string formatted according to a profile of ISO8601 as defined by
	//		[RFC3339](http://www.ietf.org/rfc/rfc3339.txt), except that partial input is allowed.
	//		Can also process dates as specified [by the W3C](http://www.w3.org/TR/NOTE-datetime)
	//		The following combinations are valid:
	//
	//			* dates only
	//			|	* yyyy
	//			|	* yyyy-MM
	//			|	* yyyy-MM-dd
	// 			* times only, with an optional time zone appended
	//			|	* THH:mm
	//			|	* THH:mm:ss
	//			|	* THH:mm:ss.SSS
	// 			* and "datetimes" which could be any combination of the above
	//
	//		timezones may be specified as Z (for UTC) or +/- followed by a time expression HH:mm
	//		Assumes the local time zone if not specified.  Does not validate.  Improperly formatted
	//		input may return null.  Arguments which are out of bounds will be handled
	// 		by the Date constructor (e.g. January 32nd typically gets resolved to February 1st)
	//		Only years between 100 and 9999 are supported.
	//
  	//	formattedString:
	//		A string such as 2005-06-30T08:05:00-07:00 or 2005-06-30 or T08:05:00
	//
	//	defaultTime:
	//		Used for defaults for fields omitted in the formattedString.
	//		Uses 1970-01-01T00:00:00.0Z by default.

	if(!dojo.date.stamp._isoRegExp){
		dojo.date.stamp._isoRegExp =
//TODO: could be more restrictive and check for 00-59, etc.
			/^(?:(\d{4})(?:-(\d{2})(?:-(\d{2}))?)?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(.\d+)?)?((?:[+-](\d{2}):(\d{2}))|Z)?)?$/;
	}

	var match = dojo.date.stamp._isoRegExp.exec(formattedString),
		result = null;

	if(match){
		match.shift();
		if(match[1]){match[1]--;} // Javascript Date months are 0-based
		if(match[6]){match[6] *= 1000;} // Javascript Date expects fractional seconds as milliseconds

		if(defaultTime){
			// mix in defaultTime.  Relatively expensive, so use || operators for the fast path of defaultTime === 0
			defaultTime = new Date(defaultTime);
			dojo.forEach(dojo.map(["FullYear", "Month", "Date", "Hours", "Minutes", "Seconds", "Milliseconds"], function(prop){
				return defaultTime["get" + prop]();
			}), function(value, index){
				match[index] = match[index] || value;
			});
		}
		result = new Date(match[0]||1970, match[1]||0, match[2]||1, match[3]||0, match[4]||0, match[5]||0, match[6]||0); //TODO: UTC defaults
		if(match[0] < 100){
			result.setFullYear(match[0] || 1970);
		}

		var offset = 0,
			zoneSign = match[7] && match[7].charAt(0);
		if(zoneSign != 'Z'){
			offset = ((match[8] || 0) * 60) + (Number(match[9]) || 0);
			if(zoneSign != '-'){ offset *= -1; }
		}
		if(zoneSign){
			offset -= result.getTimezoneOffset();
		}
		if(offset){
			result.setTime(result.getTime() + offset * 60000);
		}
	}

	return result; // Date or null
}

/*=====
	dojo.date.stamp.__Options = function(){
		//	selector: String
		//		"date" or "time" for partial formatting of the Date object.
		//		Both date and time will be formatted by default.
		//	zulu: Boolean
		//		if true, UTC/GMT is used for a timezone
		//	milliseconds: Boolean
		//		if true, output milliseconds
		this.selector = selector;
		this.zulu = zulu;
		this.milliseconds = milliseconds;
	}
=====*/

dojo.date.stamp.toISOString = function(/*Date*/dateObject, /*dojo.date.stamp.__Options?*/options){
	//	summary:
	//		Format a Date object as a string according a subset of the ISO-8601 standard
	//
	//	description:
	//		When options.selector is omitted, output follows [RFC3339](http://www.ietf.org/rfc/rfc3339.txt)
	//		The local time zone is included as an offset from GMT, except when selector=='time' (time without a date)
	//		Does not check bounds.  Only years between 100 and 9999 are supported.
	//
	//	dateObject:
	//		A Date object

	var _ = function(n){ return (n < 10) ? "0" + n : n; };
	options = options || {};
	var formattedDate = [],
		getter = options.zulu ? "getUTC" : "get",
		date = "";
	if(options.selector != "time"){
		var year = dateObject[getter+"FullYear"]();
		date = ["0000".substr((year+"").length)+year, _(dateObject[getter+"Month"]()+1), _(dateObject[getter+"Date"]())].join('-');
	}
	formattedDate.push(date);
	if(options.selector != "date"){
		var time = [_(dateObject[getter+"Hours"]()), _(dateObject[getter+"Minutes"]()), _(dateObject[getter+"Seconds"]())].join(':');
		var millis = dateObject[getter+"Milliseconds"]();
		if(options.milliseconds){
			time += "."+ (millis < 100 ? "0" : "") + _(millis);
		}
		if(options.zulu){
			time += "Z";
		}else if(options.selector != "time"){
			var timezoneOffset = dateObject.getTimezoneOffset();
			var absOffset = Math.abs(timezoneOffset);
			time += (timezoneOffset > 0 ? "-" : "+") + 
				_(Math.floor(absOffset/60)) + ":" + _(absOffset%60);
		}
		formattedDate.push(time);
	}
	return formattedDate.join('T'); // String
}

}

if(!dojo._hasResource["dojo.parser"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.parser"] = true;
dojo.provide("dojo.parser");


new Date("X"); // workaround for #11279, new Date("") == NaN

dojo.parser = new function(){
	// summary: The Dom/Widget parsing package

	var d = dojo;
	this._attrName = d._scopeName + "Type";
	this._query = "[" + this._attrName + "]";

	function val2type(/*Object*/ value){
		// summary:
		//		Returns name of type of given value.

		if(d.isString(value)){ return "string"; }
		if(typeof value == "number"){ return "number"; }
		if(typeof value == "boolean"){ return "boolean"; }
		if(d.isFunction(value)){ return "function"; }
		if(d.isArray(value)){ return "array"; } // typeof [] == "object"
		if(value instanceof Date) { return "date"; } // assume timestamp
		if(value instanceof d._Url){ return "url"; }
		return "object";
	}

	function str2obj(/*String*/ value, /*String*/ type){
		// summary:
		//		Convert given string value to given type
		switch(type){
			case "string":
				return value;
			case "number":
				return value.length ? Number(value) : NaN;
			case "boolean":
				// for checked/disabled value might be "" or "checked".  interpret as true.
				return typeof value == "boolean" ? value : !(value.toLowerCase()=="false");
			case "function":
				if(d.isFunction(value)){
					// IE gives us a function, even when we say something like onClick="foo"
					// (in which case it gives us an invalid function "function(){ foo }"). 
					//  Therefore, convert to string
					value=value.toString();
					value=d.trim(value.substring(value.indexOf('{')+1, value.length-1));
				}
				try{
					if(value === "" || value.search(/[^\w\.]+/i) != -1){
						// The user has specified some text for a function like "return x+5"
						return new Function(value);
					}else{
						// The user has specified the name of a function like "myOnClick"
						// or a single word function "return"
						return d.getObject(value, false) || new Function(value);
					}
				}catch(e){ return new Function(); }
			case "array":
				return value ? value.split(/\s*,\s*/) : [];
			case "date":
				switch(value){
					case "": return new Date("");	// the NaN of dates
					case "now": return new Date();	// current date
					default: return d.date.stamp.fromISOString(value);
				}
			case "url":
				return d.baseUrl + value;
			default:
				return d.fromJson(value);
		}
	}

	var instanceClasses = {
		// map from fully qualified name (like "dijit.Button") to structure like
		// { cls: dijit.Button, params: {label: "string", disabled: "boolean"} }
	};

	// Widgets like BorderContainer add properties to _Widget via dojo.extend().
	// If BorderContainer is loaded after _Widget's parameter list has been cached,
	// we need to refresh that parameter list (for _Widget and all widgets that extend _Widget).
	dojo.connect(dojo, "extend", function(){
		instanceClasses = {};
	});

	function getClassInfo(/*String*/ className){
		// className:
		//		fully qualified name (like "dijit.form.Button")
		// returns:
		//		structure like
		//			{ 
		//				cls: dijit.Button, 
		//				params: { label: "string", disabled: "boolean"}
		//			}

		if(!instanceClasses[className]){
			// get pointer to widget class
			var cls = d.getObject(className);
			if(!cls){ return null; }		// class not defined [yet]

			var proto = cls.prototype;
	
			// get table of parameter names & types
			var params = {}, dummyClass = {};
			for(var name in proto){
				if(name.charAt(0)=="_"){ continue; } 	// skip internal properties
				if(name in dummyClass){ continue; }		// skip "constructor" and "toString"
				var defVal = proto[name];
				params[name]=val2type(defVal);
			}

			instanceClasses[className] = { cls: cls, params: params };
		}
		return instanceClasses[className];
	}

	this._functionFromScript = function(script){
		var preamble = "";
		var suffix = "";
		var argsStr = script.getAttribute("args");
		if(argsStr){
			d.forEach(argsStr.split(/\s*,\s*/), function(part, idx){
				preamble += "var "+part+" = arguments["+idx+"]; ";
			});
		}
		var withStr = script.getAttribute("with");
		if(withStr && withStr.length){
			d.forEach(withStr.split(/\s*,\s*/), function(part){
				preamble += "with("+part+"){";
				suffix += "}";
			});
		}
		return new Function(preamble+script.innerHTML+suffix);
	}

	this.instantiate = function(/* Array */nodes, /* Object? */mixin, /* Object? */args){
		// summary:
		//		Takes array of nodes, and turns them into class instances and
		//		potentially calls a startup method to allow them to connect with
		//		any children.
		// nodes: Array
		//		Array of nodes or objects like
		//	|		{
		//	|			type: "dijit.form.Button",
		//	|			node: DOMNode,
		//	|			scripts: [ ... ],	// array of <script type="dojo/..."> children of node
		//	|			inherited: { ... }	// settings inherited from ancestors like dir, theme, etc.
		//	|		}
		// mixin: Object?
		//		An object that will be mixed in with each node in the array.
		//		Values in the mixin will override values in the node, if they
		//		exist.
		// args: Object?
		//		An object used to hold kwArgs for instantiation.
		//		Supports 'noStart' and inherited.
		var thelist = [], dp = dojo.parser;
		mixin = mixin||{};
		args = args||{};
		
		d.forEach(nodes, function(obj){
			if(!obj){ return; }

			// Get pointers to DOMNode, dojoType string, and clsInfo (metadata about the dojoType), etc.s
			var node, type, clsInfo, clazz, scripts;
			if(obj.node){
				// new format of nodes[] array, object w/lots of properties pre-computed for me
				node = obj.node;
				type = obj.type;
				clsInfo = obj.clsInfo || (type && getClassInfo(type));
				clazz = clsInfo && clsInfo.cls;
				scripts = obj.scripts;
			}else{
				// old (backwards compatible) format of nodes[] array, simple array of DOMNodes
				node = obj;
				type = dp._attrName in mixin ? mixin[dp._attrName] : node.getAttribute(dp._attrName);
				clsInfo = type && getClassInfo(type);
				clazz = clsInfo && clsInfo.cls;
				scripts = (clazz && (clazz._noScript || clazz.prototype._noScript) ? [] : 
							d.query("> script[type^='dojo/']", node));
			}
			if(!clsInfo){
				throw new Error("Could not load class '" + type);
			}

			// Setup hash to hold parameter settings for this widget.   Start with the parameter
			// settings inherited from ancestors ("dir" and "lang").
			// Inherited setting may later be overridden by explicit settings on node itself.
			var params = {},
				attributes = node.attributes;
			if(args.defaults){
				// settings for the document itself (or whatever subtree is being parsed)
				dojo.mixin(params, args.defaults);
			}
			if(obj.inherited){
				// settings from dir=rtl or lang=... on a node above this node
				dojo.mixin(params, obj.inherited);
			}

			// read parameters (ie, attributes) specified on DOMNode
			// clsInfo.params lists expected params like {"checked": "boolean", "n": "number"}
			for(var name in clsInfo.params){
				var item = name in mixin?{value:mixin[name],specified:true}:attributes.getNamedItem(name);
				if(!item || (!item.specified && (!dojo.isIE || name.toLowerCase()!="value"))){ continue; }
				var value = item.value;
				// Deal with IE quirks for 'class' and 'style'
				switch(name){
				case "class":
					value = "className" in mixin?mixin.className:node.className;
					break;
				case "style":
					value = "style" in mixin?mixin.style:(node.style && node.style.cssText); // FIXME: Opera?
				}
				var _type = clsInfo.params[name];
				if(typeof value == "string"){
					params[name] = str2obj(value, _type);
				}else{
					params[name] = value;
				}
			}

			// Process <script type="dojo/*"> script tags
			// <script type="dojo/method" event="foo"> tags are added to params, and passed to
			// the widget on instantiation.
			// <script type="dojo/method"> tags (with no event) are executed after instantiation
			// <script type="dojo/connect" event="foo"> tags are dojo.connected after instantiation
			// note: dojo/* script tags cannot exist in self closing widgets, like <input />
			var connects = [],	// functions to connect after instantiation
				calls = [];		// functions to call after instantiation

			d.forEach(scripts, function(script){
				node.removeChild(script);
				var event = script.getAttribute("event"),
					type = script.getAttribute("type"),
					nf = d.parser._functionFromScript(script);
				if(event){
					if(type == "dojo/connect"){
						connects.push({event: event, func: nf});
					}else{
						params[event] = nf;
					}
				}else{
					calls.push(nf);
				}
			});

			var markupFactory = clazz.markupFactory || clazz.prototype && clazz.prototype.markupFactory;
			// create the instance
			var instance = markupFactory ? markupFactory(params, node, clazz) : new clazz(params, node);
			thelist.push(instance);

			// map it to the JS namespace if that makes sense
			var jsname = node.getAttribute("jsId");
			if(jsname){
				d.setObject(jsname, instance);
			}

			// process connections and startup functions
			d.forEach(connects, function(connect){
				d.connect(instance, connect.event, null, connect.func);
			});
			d.forEach(calls, function(func){
				func.call(instance);
			});
		});

		// Call startup on each top level instance if it makes sense (as for
		// widgets).  Parent widgets will recursively call startup on their
		// (non-top level) children
		if(!mixin._started){
			// TODO: for 2.0, when old instantiate() API is desupported, store parent-child
			// relationships in the nodes[] array so that no getParent() call is needed.
			// Note that will  require a parse() call from ContentPane setting a param that the
			// ContentPane is the parent widget (so that the parse doesn't call startup() on the
			// ContentPane's children)
			d.forEach(thelist, function(instance){
				if(	!args.noStart && instance  && 
					instance.startup &&
					!instance._started && 
					(!instance.getParent || !instance.getParent())
				){
					instance.startup();
				}
			});
		}
		return thelist;
	};

	this.parse = function(/*DomNode?*/ rootNode, /* Object? */ args){
		// summary:
		//		Scan the DOM for class instances, and instantiate them.
		//
		// description:
		//		Search specified node (or root node) recursively for class instances,
		//		and instantiate them Searches for
		//		dojoType="qualified.class.name"
		//
		// rootNode: DomNode?
		//		A default starting root node from which to start the parsing. Can be
		//		omitted, defaulting to the entire document. If omitted, the `args`
		//		object can be passed in this place. If the `args` object has a 
		//		`rootNode` member, that is used.
		//
		// args:
		//		a kwArgs object passed along to instantiate()
		//		
		//			* noStart: Boolean?
		//				when set will prevent the parser from calling .startup()
		//				when locating the nodes. 
		//			* rootNode: DomNode?
		//				identical to the function's `rootNode` argument, though
		//				allowed to be passed in via this `args object. 
		//			* inherited: Object
		//				Hash possibly containing dir and lang settings to be applied to
		//				parsed widgets, unless there's another setting on a sub-node that overrides
		//
		//
		// example:
		//		Parse all widgets on a page:
		//	|		dojo.parser.parse();
		//
		// example:
		//		Parse all classes within the node with id="foo"
		//	|		dojo.parser.parse(dojo.byId(foo));
		//
		// example:
		//		Parse all classes in a page, but do not call .startup() on any 
		//		child
		//	|		dojo.parser.parse({ noStart: true })
		//
		// example:
		//		Parse all classes in a node, but do not call .startup()
		//	|		dojo.parser.parse(someNode, { noStart:true });
		//	|		// or
		// 	|		dojo.parser.parse({ noStart:true, rootNode: someNode });

		// determine the root node based on the passed arguments.
		var root;
		if(!args && rootNode && rootNode.rootNode){
			args = rootNode;
			root = args.rootNode;
		}else{
			root = rootNode;
		}

		var attrName = this._attrName;
		function scan(parent, list){
			// summary:
			//		Parent is an Object representing a DOMNode, with or without a dojoType specified.
			//		Scan parent's children looking for nodes with dojoType specified, storing in list[].
			//		If parent has a dojoType, also collects <script type=dojo/*> children and stores in parent.scripts[].
			// parent: Object
			//		Object representing the parent node, like
			//	|	{
			//	|		node: DomNode, 			// scan children of this node
			//	|		inherited: {dir: "rtl"},	// dir/lang setting inherited from above node
			//	|
			//	|		// attributes only set if node has dojoType specified
			//	|		scripts: [],			// empty array, put <script type=dojo/*> in here
			//	|		clsInfo: { cls: dijit.form.Button, ...}
			//	|	}
			// list: DomNode[]
			//		Output array of objects (same format as parent) representing nodes to be turned into widgets

			// Effective dir and lang settings on parent node, either set directly or inherited from grandparent
			var inherited = dojo.clone(parent.inherited);
			dojo.forEach(["dir", "lang"], function(name){
				var val = parent.node.getAttribute(name);
				if(val){
					inherited[name] = val;
				}
			});

			// if parent is a widget, then search for <script type=dojo/*> tags and put them in scripts[].
			var scripts = parent.scripts;

			// unless parent is a widget with the stopParser flag set, continue search for dojoType, recursively
			var recurse = !parent.clsInfo || !parent.clsInfo.cls.prototype.stopParser;

			// scan parent's children looking for dojoType and <script type=dojo/*>
			for(var child = parent.node.firstChild; child; child = child.nextSibling){
				if(child.nodeType == 1){
					var type = recurse && child.getAttribute(attrName);
					if(type){
						// if dojoType specified, add to output array of nodes to instantiate
						var params = {
							"type": type,
							clsInfo: getClassInfo(type),	// note: won't find classes declared via dojo.Declaration
							node: child,
							scripts: [], // <script> nodes that are parent's children
							inherited: inherited // dir & lang attributes inherited from parent
						};
						list.push(params);

						// Recurse, collecting <script type="dojo/..."> children, and also looking for
						// descendant nodes with dojoType specified (unless the widget has the stopParser flag),
						scan(params, list);
					}else if(scripts && child.nodeName.toLowerCase() == "script"){
						// if <script type="dojo/...">, save in scripts[]
						type = child.getAttribute("type");
						if (type && /^dojo\//i.test(type)) {
							scripts.push(child);
						}
					}else if(recurse){
						// Recurse, looking for grandchild nodes with dojoType specified
						scan({
							node: child,
							inherited: inherited
						}, list);
					}
				}
			}
		}

		// Make list of all nodes on page w/dojoType specified
		var list = [];
		scan({
			node: root ? dojo.byId(root) : dojo.body(),
			inherited: (args && args.inherited) || {
				dir: dojo._isBodyLtr() ? "ltr" : "rtl"
			}
		}, list);

		// go build the object instances
		return this.instantiate(list, null, args); // Array
	};
}();

//Register the parser callback. It should be the first callback
//after the a11y test.

(function(){
	var parseRunner = function(){ 
		if(dojo.config.parseOnLoad){
			dojo.parser.parse(); 
		}
	};

	// FIXME: need to clobber cross-dependency!!
	if(dojo.exists("dijit.wai.onload") && (dijit.wai.onload === dojo._loaders[0])){
		dojo._loaders.splice(1, 0, parseRunner);
	}else{
		dojo._loaders.unshift(parseRunner);
	}
})();

}

if(!dojo._hasResource["dojo.cache"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.cache"] = true;
dojo.provide("dojo.cache");

/*=====
dojo.cache = { 
	// summary:
	// 		A way to cache string content that is fetchable via `dojo.moduleUrl`.
};
=====*/

(function(){
	var cache = {};
	dojo.cache = function(/*String||Object*/module, /*String*/url, /*String||Object?*/value){
		// summary:
		// 		A getter and setter for storing the string content associated with the
		// 		module and url arguments.
		// description:
		// 		module and url are used to call `dojo.moduleUrl()` to generate a module URL.
		// 		If value is specified, the cache value for the moduleUrl will be set to
		// 		that value. Otherwise, dojo.cache will fetch the moduleUrl and store it
		// 		in its internal cache and return that cached value for the URL. To clear
		// 		a cache value pass null for value. Since XMLHttpRequest (XHR) is used to fetch the
		// 		the URL contents, only modules on the same domain of the page can use this capability.
		// 		The build system can inline the cache values though, to allow for xdomain hosting.
		// module: String||Object
		// 		If a String, the module name to use for the base part of the URL, similar to module argument
		// 		to `dojo.moduleUrl`. If an Object, something that has a .toString() method that
		// 		generates a valid path for the cache item. For example, a dojo._Url object.
		// url: String
		// 		The rest of the path to append to the path derived from the module argument. If
		// 		module is an object, then this second argument should be the "value" argument instead.
		// value: String||Object?
		// 		If a String, the value to use in the cache for the module/url combination.
		// 		If an Object, it can have two properties: value and sanitize. The value property
		// 		should be the value to use in the cache, and sanitize can be set to true or false,
		// 		to indicate if XML declarations should be removed from the value and if the HTML
		// 		inside a body tag in the value should be extracted as the real value. The value argument
		// 		or the value property on the value argument are usually only used by the build system
		// 		as it inlines cache content.
		//	example:
		//		To ask dojo.cache to fetch content and store it in the cache (the dojo["cache"] style
		// 		of call is used to avoid an issue with the build system erroneously trying to intern
		// 		this example. To get the build system to intern your dojo.cache calls, use the
		// 		"dojo.cache" style of call):
		// 		|	//If template.html contains "<h1>Hello</h1>" that will be
		// 		|	//the value for the text variable.
		//		|	var text = dojo["cache"]("my.module", "template.html");
		//	example:
		//		To ask dojo.cache to fetch content and store it in the cache, and sanitize the input
		// 		 (the dojo["cache"] style of call is used to avoid an issue with the build system 
		// 		erroneously trying to intern this example. To get the build system to intern your
		// 		dojo.cache calls, use the "dojo.cache" style of call):
		// 		|	//If template.html contains "<html><body><h1>Hello</h1></body></html>", the
		// 		|	//text variable will contain just "<h1>Hello</h1>".
		//		|	var text = dojo["cache"]("my.module", "template.html", {sanitize: true});
		//	example:
		//		Same example as previous, but demostrates how an object can be passed in as
		//		the first argument, then the value argument can then be the second argument.
		// 		|	//If template.html contains "<html><body><h1>Hello</h1></body></html>", the
		// 		|	//text variable will contain just "<h1>Hello</h1>".
		//		|	var text = dojo["cache"](new dojo._Url("my/module/template.html"), {sanitize: true});

		//Module could be a string, or an object that has a toString() method
		//that will return a useful path. If it is an object, then the "url" argument
		//will actually be the value argument.
		if(typeof module == "string"){
			var pathObj = dojo.moduleUrl(module, url);
		}else{
			pathObj = module;
			value = url;
		}
		var key = pathObj.toString();

		var val = value;
		if(value != undefined && !dojo.isString(value)){
			val = ("value" in value ? value.value : undefined);
		}

		var sanitize = value && value.sanitize ? true : false;

		if(typeof val == "string"){
			//We have a string, set cache value
			val = cache[key] = sanitize ? dojo.cache._sanitize(val) : val;
		}else if(val === null){
			//Remove cached value
			delete cache[key];
		}else{
			//Allow cache values to be empty strings. If key property does
			//not exist, fetch it.
			if(!(key in cache)){
				val = dojo._getText(key);
				cache[key] = sanitize ? dojo.cache._sanitize(val) : val;
			}
			val = cache[key];
		}
		return val; //String
	};

	dojo.cache._sanitize = function(/*String*/val){
		// summary: 
		//		Strips <?xml ...?> declarations so that external SVG and XML
		// 		documents can be added to a document without worry. Also, if the string
		//		is an HTML document, only the part inside the body tag is returned.
		// description:
		// 		Copied from dijit._Templated._sanitizeTemplateString.
		if(val){
			val = val.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im, "");
			var matches = val.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
			if(matches){
				val = matches[1];
			}
		}else{
			val = "";
		}
		return val; //String
	};
})();

}

if(!dojo._hasResource["dijit._Templated"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Templated"] = true;
dojo.provide("dijit._Templated");






dojo.declare("dijit._Templated",
	null,
	{
		// summary:
		//		Mixin for widgets that are instantiated from a template

		// templateString: [protected] String
		//		A string that represents the widget template. Pre-empts the
		//		templatePath. In builds that have their strings "interned", the
		//		templatePath is converted to an inline templateString, thereby
		//		preventing a synchronous network call.
		//
		//		Use in conjunction with dojo.cache() to load from a file.
		templateString: null,

		// templatePath: [protected deprecated] String
		//		Path to template (HTML file) for this widget relative to dojo.baseUrl.
		//		Deprecated: use templateString with dojo.cache() instead.
		templatePath: null,

		// widgetsInTemplate: [protected] Boolean
		//		Should we parse the template to find widgets that might be
		//		declared in markup inside it?  False by default.
		widgetsInTemplate: false,

		// skipNodeCache: [protected] Boolean
		//		If using a cached widget template node poses issues for a
		//		particular widget class, it can set this property to ensure
		//		that its template is always re-built from a string
		_skipNodeCache: false,

		// _earlyTemplatedStartup: Boolean
		//		A fallback to preserve the 1.0 - 1.3 behavior of children in
		//		templates having their startup called before the parent widget
		//		fires postCreate. Defaults to 'false', causing child widgets to
		//		have their .startup() called immediately before a parent widget
		//		.startup(), but always after the parent .postCreate(). Set to
		//		'true' to re-enable to previous, arguably broken, behavior.
		_earlyTemplatedStartup: false,

		// _attachPoints: [private] String[]
		//		List of widget attribute names associated with dojoAttachPoint=... in the
		//		template, ex: ["containerNode", "labelNode"]
/*=====
 		_attachPoints: [],
 =====*/

		constructor: function(){
			this._attachPoints = [];
		},

		_stringRepl: function(tmpl){
			// summary:
			//		Does substitution of ${foo} type properties in template string
			// tags:
			//		private
			var className = this.declaredClass, _this = this;
			// Cache contains a string because we need to do property replacement
			// do the property replacement
			return dojo.string.substitute(tmpl, this, function(value, key){
				if(key.charAt(0) == '!'){ value = dojo.getObject(key.substr(1), false, _this); }
				if(typeof value == "undefined"){ throw new Error(className+" template:"+key); } // a debugging aide
				if(value == null){ return ""; }

				// Substitution keys beginning with ! will skip the transform step,
				// in case a user wishes to insert unescaped markup, e.g. ${!foo}
				return key.charAt(0) == "!" ? value :
					// Safer substitution, see heading "Attribute values" in
					// http://www.w3.org/TR/REC-html40/appendix/notes.html#h-B.3.2
					value.toString().replace(/"/g,"&quot;"); //TODO: add &amp? use encodeXML method?
			}, this);
		},

		// method over-ride
		buildRendering: function(){
			// summary:
			//		Construct the UI for this widget from a template, setting this.domNode.
			// tags:
			//		protected

			// Lookup cached version of template, and download to cache if it
			// isn't there already.  Returns either a DomNode or a string, depending on
			// whether or not the template contains ${foo} replacement parameters.
			var cached = dijit._Templated.getCachedTemplate(this.templatePath, this.templateString, this._skipNodeCache);

			var node;
			if(dojo.isString(cached)){
				node = dojo._toDom(this._stringRepl(cached));
				if(node.nodeType != 1){
					// Flag common problems such as templates with multiple top level nodes (nodeType == 11)
					throw new Error("Invalid template: " + cached);
				}
			}else{
				// if it's a node, all we have to do is clone it
				node = cached.cloneNode(true);
			}

			this.domNode = node;

			// recurse through the node, looking for, and attaching to, our
			// attachment points and events, which should be defined on the template node.
			this._attachTemplateNodes(node);

			if(this.widgetsInTemplate){
				// Make sure dojoType is used for parsing widgets in template.
				// The dojo.parser.query could be changed from multiversion support.
				var parser = dojo.parser, qry, attr;
				if(parser._query != "[dojoType]"){
					qry = parser._query;
					attr = parser._attrName;
					parser._query = "[dojoType]";
					parser._attrName = "dojoType";
				}

				// Store widgets that we need to start at a later point in time
				var cw = (this._startupWidgets = dojo.parser.parse(node, {
					noStart: !this._earlyTemplatedStartup,
					inherited: {dir: this.dir, lang: this.lang}
				}));

				// Restore the query.
				if(qry){
					parser._query = qry;
					parser._attrName = attr;
				}

				this._supportingWidgets = dijit.findWidgets(node);

				this._attachTemplateNodes(cw, function(n,p){
					return n[p];
				});
			}

			this._fillContent(this.srcNodeRef);
		},

		_fillContent: function(/*DomNode*/ source){
			// summary:
			//		Relocate source contents to templated container node.
			//		this.containerNode must be able to receive children, or exceptions will be thrown.
			// tags:
			//		protected
			var dest = this.containerNode;
			if(source && dest){
				while(source.hasChildNodes()){
					dest.appendChild(source.firstChild);
				}
			}
		},

		_attachTemplateNodes: function(rootNode, getAttrFunc){
			// summary:
			//		Iterate through the template and attach functions and nodes accordingly.
			// description:
			//		Map widget properties and functions to the handlers specified in
			//		the dom node and it's descendants. This function iterates over all
			//		nodes and looks for these properties:
			//			* dojoAttachPoint
			//			* dojoAttachEvent
			//			* waiRole
			//			* waiState
			// rootNode: DomNode|Array[Widgets]
			//		the node to search for properties. All children will be searched.
			// getAttrFunc: Function?
			//		a function which will be used to obtain property for a given
			//		DomNode/Widget
			// tags:
			//		private

			getAttrFunc = getAttrFunc || function(n,p){ return n.getAttribute(p); };

			var nodes = dojo.isArray(rootNode) ? rootNode : (rootNode.all || rootNode.getElementsByTagName("*"));
			var x = dojo.isArray(rootNode) ? 0 : -1;
			for(; x<nodes.length; x++){
				var baseNode = (x == -1) ? rootNode : nodes[x];
				if(this.widgetsInTemplate && getAttrFunc(baseNode, "dojoType")){
					continue;
				}
				// Process dojoAttachPoint
				var attachPoint = getAttrFunc(baseNode, "dojoAttachPoint");
				if(attachPoint){
					var point, points = attachPoint.split(/\s*,\s*/);
					while((point = points.shift())){
						if(dojo.isArray(this[point])){
							this[point].push(baseNode);
						}else{
							this[point]=baseNode;
						}
						this._attachPoints.push(point);
					}
				}

				// Process dojoAttachEvent
				var attachEvent = getAttrFunc(baseNode, "dojoAttachEvent");
				if(attachEvent){
					// NOTE: we want to support attributes that have the form
					// "domEvent: nativeEvent; ..."
					var event, events = attachEvent.split(/\s*,\s*/);
					var trim = dojo.trim;
					while((event = events.shift())){
						if(event){
							var thisFunc = null;
							if(event.indexOf(":") != -1){
								// oh, if only JS had tuple assignment
								var funcNameArr = event.split(":");
								event = trim(funcNameArr[0]);
								thisFunc = trim(funcNameArr[1]);
							}else{
								event = trim(event);
							}
							if(!thisFunc){
								thisFunc = event;
							}
							this.connect(baseNode, event, thisFunc);
						}
					}
				}

				// waiRole, waiState
				var role = getAttrFunc(baseNode, "waiRole");
				if(role){
					dijit.setWaiRole(baseNode, role);
				}
				var values = getAttrFunc(baseNode, "waiState");
				if(values){
					dojo.forEach(values.split(/\s*,\s*/), function(stateValue){
						if(stateValue.indexOf('-') != -1){
							var pair = stateValue.split('-');
							dijit.setWaiState(baseNode, pair[0], pair[1]);
						}
					});
				}
			}
		},

		startup: function(){
			dojo.forEach(this._startupWidgets, function(w){
				if(w && !w._started && w.startup){
					w.startup();
				}
			});
			this.inherited(arguments);
		},

		destroyRendering: function(){
			// Delete all attach points to prevent IE6 memory leaks.
			dojo.forEach(this._attachPoints, function(point){
				delete this[point];
			}, this);
			this._attachPoints = [];

			this.inherited(arguments);
		}
	}
);

// key is either templatePath or templateString; object is either string or DOM tree
dijit._Templated._templateCache = {};

dijit._Templated.getCachedTemplate = function(templatePath, templateString, alwaysUseString){
	// summary:
	//		Static method to get a template based on the templatePath or
	//		templateString key
	// templatePath: String||dojo.uri.Uri
	//		The URL to get the template from.
	// templateString: String?
	//		a string to use in lieu of fetching the template from a URL. Takes precedence
	//		over templatePath
	// returns: Mixed
	//		Either string (if there are ${} variables that need to be replaced) or just
	//		a DOM tree (if the node can be cloned directly)

	// is it already cached?
	var tmplts = dijit._Templated._templateCache;
	var key = templateString || templatePath;
	var cached = tmplts[key];
	if(cached){
		try{
			// if the cached value is an innerHTML string (no ownerDocument) or a DOM tree created within the current document, then use the current cached value
			if(!cached.ownerDocument || cached.ownerDocument == dojo.doc){
				// string or node of the same document
				return cached;
			}
		}catch(e){ /* squelch */ } // IE can throw an exception if cached.ownerDocument was reloaded
		dojo.destroy(cached);
	}

	// If necessary, load template string from template path
	if(!templateString){
		templateString = dojo.cache(templatePath, {sanitize: true});
	}
	templateString = dojo.string.trim(templateString);

	if(alwaysUseString || templateString.match(/\$\{([^\}]+)\}/g)){
		// there are variables in the template so all we can do is cache the string
		return (tmplts[key] = templateString); //String
	}else{
		// there are no variables in the template so we can cache the DOM tree
		var node = dojo._toDom(templateString);
		if(node.nodeType != 1){
			throw new Error("Invalid template: " + templateString);
		}
		return (tmplts[key] = node); //Node
	}
};

if(dojo.isIE){
	dojo.addOnWindowUnload(function(){
		var cache = dijit._Templated._templateCache;
		for(var key in cache){
			var value = cache[key];
			if(typeof value == "object"){ // value is either a string or a DOM node template
				dojo.destroy(value);
			}
			delete cache[key];
		}
	});
}

// These arguments can be specified for widgets which are used in templates.
// Since any widget can be specified as sub widgets in template, mix it
// into the base widget class.  (This is a hack, but it's effective.)
dojo.extend(dijit._Widget,{
	dojoAttachEvent: "",
	dojoAttachPoint: "",
	waiRole: "",
	waiState:""
});

}

if(!dojo._hasResource["dijit.ToolbarSeparator"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.ToolbarSeparator"] = true;
dojo.provide("dijit.ToolbarSeparator");




dojo.declare("dijit.ToolbarSeparator",
		[ dijit._Widget, dijit._Templated ],
		{
		// summary:
		//		A spacer between two `dijit.Toolbar` items
		templateString: '<div class="dijitToolbarSeparator dijitInline" waiRole="presentation"></div>',
		postCreate: function(){ dojo.setSelectable(this.domNode, false); },
		isFocusable: function(){
			// summary:
			//		This widget isn't focusable, so pass along that fact.
			// tags:
			//		protected
			return false;
		}

	});



}

if(!dojo._hasResource["dijit.Toolbar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Toolbar"] = true;
dojo.provide("dijit.Toolbar");





dojo.declare("dijit.Toolbar",
	[dijit._Widget, dijit._Templated, dijit._KeyNavContainer],
	{
	// summary:
	//		A Toolbar widget, used to hold things like `dijit.Editor` buttons

	templateString:
		'<div class="dijit" waiRole="toolbar" tabIndex="${tabIndex}" dojoAttachPoint="containerNode">' +
		//	'<table style="table-layout: fixed" class="dijitReset dijitToolbarTable">' + // factor out style
		//		'<tr class="dijitReset" dojoAttachPoint="containerNode"></tr>'+
		//	'</table>' +
		'</div>',

	baseClass: "dijitToolbar",

	postCreate: function(){
		this.connectKeyNavHandlers(
			this.isLeftToRight() ? [dojo.keys.LEFT_ARROW] : [dojo.keys.RIGHT_ARROW],
			this.isLeftToRight() ? [dojo.keys.RIGHT_ARROW] : [dojo.keys.LEFT_ARROW]
		);
		this.inherited(arguments);
	},

	startup: function(){
		if(this._started){ return; }

		this.startupKeyNavChildren();

		this.inherited(arguments);
	}
}
);

// For back-compat, remove for 2.0


}

if(!dojo._hasResource["dijit.form._FormWidget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._FormWidget"] = true;
dojo.provide("dijit.form._FormWidget");







dojo.declare("dijit.form._FormWidget", [dijit._Widget, dijit._Templated, dijit._CssStateMixin],
	{
	// summary:
	//		Base class for widgets corresponding to native HTML elements such as <checkbox> or <button>,
	//		which can be children of a <form> node or a `dijit.form.Form` widget.
	//
	// description:
	//		Represents a single HTML element.
	//		All these widgets should have these attributes just like native HTML input elements.
	//		You can set them during widget construction or afterwards, via `dijit._Widget.attr`.
	//
	//		They also share some common methods.

	// name: String
	//		Name used when submitting form; same as "name" attribute or plain HTML elements
	name: "",

	// alt: String
	//		Corresponds to the native HTML <input> element's attribute.
	alt: "",

	// value: String
	//		Corresponds to the native HTML <input> element's attribute.
	value: "",

	// type: String
	//		Corresponds to the native HTML <input> element's attribute.
	type: "text",

	// tabIndex: Integer
	//		Order fields are traversed when user hits the tab key
	tabIndex: "0",

	// disabled: Boolean
	//		Should this widget respond to user input?
	//		In markup, this is specified as "disabled='disabled'", or just "disabled".
	disabled: false,

	// intermediateChanges: Boolean
	//		Fires onChange for each value change or only on demand
	intermediateChanges: false,

	// scrollOnFocus: Boolean
	//		On focus, should this widget scroll into view?
	scrollOnFocus: true,

	// These mixins assume that the focus node is an INPUT, as many but not all _FormWidgets are.
	attributeMap: dojo.delegate(dijit._Widget.prototype.attributeMap, {
		value: "focusNode",
		id: "focusNode",
		tabIndex: "focusNode",
		alt: "focusNode",
		title: "focusNode"
	}),

	postMixInProperties: function(){
		// Setup name=foo string to be referenced from the template (but only if a name has been specified)
		// Unfortunately we can't use attributeMap to set the name due to IE limitations, see #8660
		// Regarding escaping, see heading "Attribute values" in
		// http://www.w3.org/TR/REC-html40/appendix/notes.html#h-B.3.2
		this.nameAttrSetting = this.name ? ('name="' + this.name.replace(/'/g, "&quot;") + '"') : '';
		this.inherited(arguments);
	},

	postCreate: function(){
		this.inherited(arguments);
		this.connect(this.domNode, "onmousedown", "_onMouseDown");
	},

	_setDisabledAttr: function(/*Boolean*/ value){
		this.disabled = value;
		dojo.attr(this.focusNode, 'disabled', value);
		if(this.valueNode){
			dojo.attr(this.valueNode, 'disabled', value);
		}
		dijit.setWaiState(this.focusNode, "disabled", value);

		if(value){
			// reset these, because after the domNode is disabled, we can no longer receive
			// mouse related events, see #4200
			this._hovering = false;
			this._active = false;

			// clear tab stop(s) on this widget's focusable node(s)  (ComboBox has two focusable nodes)
			var attachPointNames = "tabIndex" in this.attributeMap ? this.attributeMap.tabIndex : "focusNode";
			dojo.forEach(dojo.isArray(attachPointNames) ? attachPointNames : [attachPointNames], function(attachPointName){
				var node = this[attachPointName];
				// complex code because tabIndex=-1 on a <div> doesn't work on FF
				if(dojo.isWebKit || dijit.hasDefaultTabStop(node)){	// see #11064 about webkit bug
					node.setAttribute('tabIndex', "-1");
				}else{
					node.removeAttribute('tabIndex');				
				}
			}, this);
		}else{
			this.focusNode.setAttribute('tabIndex', this.tabIndex);
		}
	},

	setDisabled: function(/*Boolean*/ disabled){
		// summary:
		//		Deprecated.   Use set('disabled', ...) instead.
		dojo.deprecated("setDisabled("+disabled+") is deprecated. Use set('disabled',"+disabled+") instead.", "", "2.0");
		this.set('disabled', disabled);
	},

	_onFocus: function(e){
		if(this.scrollOnFocus){
			dojo.window.scrollIntoView(this.domNode);
		}
		this.inherited(arguments);
	},

	isFocusable: function(){
		// summary:
		//		Tells if this widget is focusable or not.   Used internally by dijit.
		// tags:
		//		protected
		return !this.disabled && !this.readOnly && this.focusNode && (dojo.style(this.domNode, "display") != "none");
	},

	focus: function(){
		// summary:
		//		Put focus on this widget
		dijit.focus(this.focusNode);
	},

	compare: function(/*anything*/val1, /*anything*/val2){
		// summary:
		//		Compare 2 values (as returned by attr('value') for this widget).
		// tags:
		//		protected
		if(typeof val1 == "number" && typeof val2 == "number"){
			return (isNaN(val1) && isNaN(val2)) ? 0 : val1 - val2;
		}else if(val1 > val2){
			return 1;
		}else if(val1 < val2){
			return -1;
		}else{
			return 0;
		}
	},

	onChange: function(newValue){
		// summary:
		//		Callback when this widget's value is changed.
		// tags:
		//		callback
	},

	// _onChangeActive: [private] Boolean
	//		Indicates that changes to the value should call onChange() callback.
	//		This is false during widget initialization, to avoid calling onChange()
	//		when the initial value is set.
	_onChangeActive: false,

	_handleOnChange: function(/*anything*/ newValue, /* Boolean? */ priorityChange){
		// summary:
		//		Called when the value of the widget is set.  Calls onChange() if appropriate
		// newValue:
		//		the new value
		// priorityChange:
		//		For a slider, for example, dragging the slider is priorityChange==false,
		//		but on mouse up, it's priorityChange==true.  If intermediateChanges==true,
		//		onChange is only called form priorityChange=true events.
		// tags:
		//		private
		this._lastValue = newValue;
		if(this._lastValueReported == undefined && (priorityChange === null || !this._onChangeActive)){
			// this block executes not for a change, but during initialization,
			// and is used to store away the original value (or for ToggleButton, the original checked state)
			this._resetValue = this._lastValueReported = newValue;
		}
		if((this.intermediateChanges || priorityChange || priorityChange === undefined) &&
			((typeof newValue != typeof this._lastValueReported) ||
				this.compare(newValue, this._lastValueReported) != 0)){
			this._lastValueReported = newValue;
			if(this._onChangeActive){
				if(this._onChangeHandle){
					clearTimeout(this._onChangeHandle);
				}
				// setTimout allows hidden value processing to run and
				// also the onChange handler can safely adjust focus, etc
				this._onChangeHandle = setTimeout(dojo.hitch(this,
					function(){
						this._onChangeHandle = null;
						this.onChange(newValue);
					}), 0); // try to collapse multiple onChange's fired faster than can be processed
			}
		}
	},

	create: function(){
		// Overrides _Widget.create()
		this.inherited(arguments);
		this._onChangeActive = true;
	},

	destroy: function(){
		if(this._onChangeHandle){ // destroy called before last onChange has fired
			clearTimeout(this._onChangeHandle);
			this.onChange(this._lastValueReported);
		}
		this.inherited(arguments);
	},

	setValue: function(/*String*/ value){
		// summary:
		//		Deprecated.   Use set('value', ...) instead.
		dojo.deprecated("dijit.form._FormWidget:setValue("+value+") is deprecated.  Use set('value',"+value+") instead.", "", "2.0");
		this.set('value', value);
	},

	getValue: function(){
		// summary:
		//		Deprecated.   Use get('value') instead.
		dojo.deprecated(this.declaredClass+"::getValue() is deprecated. Use get('value') instead.", "", "2.0");
		return this.get('value');
	},
	
	_onMouseDown: function(e){
		// If user clicks on the button, even if the mouse is released outside of it,
		// this button should get focus (to mimics native browser buttons).
		// This is also needed on chrome because otherwise buttons won't get focus at all,
		// which leads to bizarre focus restore on Dialog close etc.
		if(!e.ctrlKey && this.isFocusable()){ // !e.ctrlKey to ignore right-click on mac
			// Set a global event to handle mouseup, so it fires properly
			// even if the cursor leaves this.domNode before the mouse up event.
			var mouseUpConnector = this.connect(dojo.body(), "onmouseup", function(){
				if (this.isFocusable()) {
					this.focus();
				}
				this.disconnect(mouseUpConnector);
			});
		}
	}
});

dojo.declare("dijit.form._FormValueWidget", dijit.form._FormWidget,
{
	// summary:
	//		Base class for widgets corresponding to native HTML elements such as <input> or <select> that have user changeable values.
	// description:
	//		Each _FormValueWidget represents a single input value, and has a (possibly hidden) <input> element,
	//		to which it serializes it's input value, so that form submission (either normal submission or via FormBind?)
	//		works as expected.

	// Don't attempt to mixin the 'type', 'name' attributes here programatically -- they must be declared
	// directly in the template as read by the parser in order to function. IE is known to specifically
	// require the 'name' attribute at element creation time.   See #8484, #8660.
	// TODO: unclear what that {value: ""} is for; FormWidget.attributeMap copies value to focusNode,
	// so maybe {value: ""} is so the value *doesn't* get copied to focusNode?
	// Seems like we really want value removed from attributeMap altogether
	// (although there's no easy way to do that now)

	// readOnly: Boolean
	//		Should this widget respond to user input?
	//		In markup, this is specified as "readOnly".
	//		Similar to disabled except readOnly form values are submitted.
	readOnly: false,

	attributeMap: dojo.delegate(dijit.form._FormWidget.prototype.attributeMap, {
		value: "",
		readOnly: "focusNode"
	}),

	_setReadOnlyAttr: function(/*Boolean*/ value){
		this.readOnly = value;
		dojo.attr(this.focusNode, 'readOnly', value);
		dijit.setWaiState(this.focusNode, "readonly", value);
	},

	postCreate: function(){
		this.inherited(arguments);

		if(dojo.isIE){ // IE won't stop the event with keypress
			this.connect(this.focusNode || this.domNode, "onkeydown", this._onKeyDown);
		}
		// Update our reset value if it hasn't yet been set (because this.set()
		// is only called when there *is* a value)
		if(this._resetValue === undefined){
			this._resetValue = this.value;
		}
	},

	_setValueAttr: function(/*anything*/ newValue, /*Boolean, optional*/ priorityChange){
		// summary:
		//		Hook so attr('value', value) works.
		// description:
		//		Sets the value of the widget.
		//		If the value has changed, then fire onChange event, unless priorityChange
		//		is specified as null (or false?)
		this.value = newValue;
		this._handleOnChange(newValue, priorityChange);
	},

	_getValueAttr: function(){
		// summary:
		//		Hook so attr('value') works.
		return this._lastValue;
	},

	undo: function(){
		// summary:
		//		Restore the value to the last value passed to onChange
		this._setValueAttr(this._lastValueReported, false);
	},

	reset: function(){
		// summary:
		//		Reset the widget's value to what it was at initialization time
		this._hasBeenBlurred = false;
		this._setValueAttr(this._resetValue, true);
	},

	_onKeyDown: function(e){
		if(e.keyCode == dojo.keys.ESCAPE && !(e.ctrlKey || e.altKey || e.metaKey)){
			var te;
			if(dojo.isIE){
				e.preventDefault(); // default behavior needs to be stopped here since keypress is too late
				te = document.createEventObject();
				te.keyCode = dojo.keys.ESCAPE;
				te.shiftKey = e.shiftKey;
				e.srcElement.fireEvent('onkeypress', te);
			}
		}
	},

	_layoutHackIE7: function(){
		// summary:
		//		Work around table sizing bugs on IE7 by forcing redraw

		if(dojo.isIE == 7){ // fix IE7 layout bug when the widget is scrolled out of sight
			var domNode = this.domNode;
			var parent = domNode.parentNode;
			var pingNode = domNode.firstChild || domNode; // target node most unlikely to have a custom filter
			var origFilter = pingNode.style.filter; // save custom filter, most likely nothing
			var _this = this;
			while(parent && parent.clientHeight == 0){ // search for parents that haven't rendered yet
				(function ping(){
					var disconnectHandle = _this.connect(parent, "onscroll",
						function(e){
							_this.disconnect(disconnectHandle); // only call once
							pingNode.style.filter = (new Date()).getMilliseconds(); // set to anything that's unique
							setTimeout(function(){ pingNode.style.filter = origFilter }, 0); // restore custom filter, if any
						}
					);
				})();
				parent = parent.parentNode;
			}
		}
	}
});

}

if(!dojo._hasResource["dijit._HasDropDown"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._HasDropDown"] = true;
dojo.provide("dijit._HasDropDown");




dojo.declare("dijit._HasDropDown",
	null,
	{
		// summary:
		//		Mixin for widgets that need drop down ability.

		// _buttonNode: [protected] DomNode
		//		The button/icon/node to click to display the drop down.
		//		Can be set via a dojoAttachPoint assignment.
		//		If missing, then either focusNode or domNode (if focusNode is also missing) will be used.
		_buttonNode: null,

		// _arrowWrapperNode: [protected] DomNode
		//		Will set CSS class dijitUpArrow, dijitDownArrow, dijitRightArrow etc. on this node depending
		//		on where the drop down is set to be positioned.
		//		Can be set via a dojoAttachPoint assignment.
		//		If missing, then _buttonNode will be used.
		_arrowWrapperNode: null,

		// _popupStateNode: [protected] DomNode
		//		The node to set the popupActive class on.
		//		Can be set via a dojoAttachPoint assignment.
		//		If missing, then focusNode or _buttonNode (if focusNode is missing) will be used.
		_popupStateNode: null,

		// _aroundNode: [protected] DomNode
		//		The node to display the popup around.
		//		Can be set via a dojoAttachPoint assignment.
		//		If missing, then domNode will be used.
		_aroundNode: null,

		// dropDown: [protected] Widget
		//		The widget to display as a popup.  This widget *must* be
		//		defined before the startup function is called.
		dropDown: null,

		// autoWidth: [protected] Boolean
		//		Set to true to make the drop down at least as wide as this
		//		widget.  Set to false if the drop down should just be its
		//		default width
		autoWidth: true,

		// forceWidth: [protected] Boolean
		//		Set to true to make the drop down exactly as wide as this
		//		widget.  Overrides autoWidth.
		forceWidth: false,

		// maxHeight: [protected] Integer
		//		The max height for our dropdown.  Set to 0 for no max height.
		//		any dropdown taller than this will have scrollbars
		maxHeight: 0,

		// dropDownPosition: [const] String[]
		//		This variable controls the position of the drop down.
		//		It's an array of strings with the following values:
		//
		//			* before: places drop down to the left of the target node/widget, or to the right in
		//			  the case of RTL scripts like Hebrew and Arabic
		//			* after: places drop down to the right of the target node/widget, or to the left in
		//			  the case of RTL scripts like Hebrew and Arabic
		//			* above: drop down goes above target node
		//			* below: drop down goes below target node
		//
		//		The list is positions is tried, in order, until a position is found where the drop down fits
		//		within the viewport.
		//
		dropDownPosition: ["below","above"],

		// _stopClickEvents: Boolean
		//		When set to false, the click events will not be stopped, in
		//		case you want to use them in your subwidget
		_stopClickEvents: true,

		_onDropDownMouseDown: function(/*Event*/ e){
			// summary:
			//		Callback when the user mousedown's on the arrow icon

			if(this.disabled || this.readOnly){ return; }

			this._docHandler = this.connect(dojo.doc, "onmouseup", "_onDropDownMouseUp");

			this.toggleDropDown();
		},

		_onDropDownMouseUp: function(/*Event?*/ e){
			// summary:
			//		Callback when the user lifts their mouse after mouse down on the arrow icon.
			//		If the drop is a simple menu and the mouse is over the menu, we execute it, otherwise, we focus our
			//		dropDown node.  If the event is missing, then we are not
			//		a mouseup event.
			//
			//		This is useful for the common mouse movement pattern
			//		with native browser <select> nodes:
			//			1. mouse down on the select node (probably on the arrow)
			//			2. move mouse to a menu item while holding down the mouse button
			//			3. mouse up.  this selects the menu item as though the user had clicked it.
			if(e && this._docHandler){
				this.disconnect(this._docHandler);
			}
			var dropDown = this.dropDown, overMenu = false;

			if(e && this._opened){
				// This code deals with the corner-case when the drop down covers the original widget,
				// because it's so large.  In that case mouse-up shouldn't select a value from the menu.
				// Find out if our target is somewhere in our dropdown widget,
				// but not over our _buttonNode (the clickable node)
				var c = dojo.position(this._buttonNode, true);
				if(!(e.pageX >= c.x && e.pageX <= c.x + c.w) ||
					!(e.pageY >= c.y && e.pageY <= c.y + c.h)){
					var t = e.target;
					while(t && !overMenu){
						if(dojo.hasClass(t, "dijitPopup")){
							overMenu = true;
						}else{
							t = t.parentNode;
						}
					}
					if(overMenu){
						t = e.target;
						if(dropDown.onItemClick){
							var menuItem;
							while(t && !(menuItem = dijit.byNode(t))){
								t = t.parentNode;
							}
							if(menuItem && menuItem.onClick && menuItem.getParent){
								menuItem.getParent().onItemClick(menuItem, e);
							}
						}
						return;
					}
				}
			}
			if(this._opened && dropDown.focus){
				// Focus the dropdown widget - do it on a delay so that we
				// don't steal our own focus.
				window.setTimeout(dojo.hitch(dropDown, "focus"), 1);
			}
		},

		_onDropDownClick: function(/*Event*/ e){
			// the drop down was already opened on mousedown/keydown; just need to call stopEvent()
			if(this._stopClickEvents){
				dojo.stopEvent(e);
			}			
		},

		_setupDropdown: function(){
			// summary:
			//		set up nodes and connect our mouse and keypress events
			this._buttonNode = this._buttonNode || this.focusNode || this.domNode;
			this._popupStateNode = this._popupStateNode || this.focusNode || this._buttonNode;
			this._aroundNode = this._aroundNode || this.domNode;
			this.connect(this._buttonNode, "onmousedown", "_onDropDownMouseDown");
			this.connect(this._buttonNode, "onclick", "_onDropDownClick");
			this.connect(this._buttonNode, "onkeydown", "_onDropDownKeydown");
			this.connect(this._buttonNode, "onkeyup", "_onKey");

			// If we have a _setStateClass function (which happens when
			// we are a form widget), then we need to connect our open/close
			// functions to it
			if(this._setStateClass){
				this.connect(this, "openDropDown", "_setStateClass");
				this.connect(this, "closeDropDown", "_setStateClass");
			}

			// Add a class to the "dijitDownArrowButton" type class to _buttonNode so theme can set direction of arrow
			// based on where drop down will normally appear
			var defaultPos = {
					"after" : this.isLeftToRight() ? "Right" : "Left",
					"before" : this.isLeftToRight() ? "Left" : "Right",
					"above" : "Up",
					"below" : "Down",
					"left" : "Left",
					"right" : "Right"
			}[this.dropDownPosition[0]] || this.dropDownPosition[0] || "Down";
			dojo.addClass(this._arrowWrapperNode || this._buttonNode, "dijit" + defaultPos + "ArrowButton");
		},

		postCreate: function(){
			this._setupDropdown();
			this.inherited(arguments);
		},

		destroyDescendants: function(){
			if(this.dropDown){
				// Destroy the drop down, unless it's already been destroyed.  This can happen because
				// the drop down is a direct child of <body> even though it's logically my child.
				if(!this.dropDown._destroyed){
					this.dropDown.destroyRecursive();
				}
				delete this.dropDown;
			}
			this.inherited(arguments);
		},

		_onDropDownKeydown: function(/*Event*/ e){
			if(e.keyCode == dojo.keys.DOWN_ARROW || e.keyCode == dojo.keys.ENTER || e.keyCode == dojo.keys.SPACE){
				e.preventDefault();	// stop IE screen jump
			}
		},

		_onKey: function(/*Event*/ e){
			// summary:
			//		Callback when the user presses a key while focused on the button node

			if(this.disabled || this.readOnly){ return; }
			var d = this.dropDown;
			if(d && this._opened && d.handleKey){
				if(d.handleKey(e) === false){ return; }
			}
			if(d && this._opened && e.keyCode == dojo.keys.ESCAPE){
				this.toggleDropDown();
			}else if(d && !this._opened && 
					(e.keyCode == dojo.keys.DOWN_ARROW || e.keyCode == dojo.keys.ENTER || e.keyCode == dojo.keys.SPACE)){
				this.toggleDropDown();
				if(d.focus){
					setTimeout(dojo.hitch(d, "focus"), 1);
				}
			}
		},

		_onBlur: function(){
			// summary:
			//		Called magically when focus has shifted away from this widget and it's dropdown

			this.closeDropDown();
			// don't focus on button.  the user has explicitly focused on something else.
			this.inherited(arguments);
		},

		isLoaded: function(){
			// summary:
			//		Returns whether or not the dropdown is loaded.  This can
			//		be overridden in order to force a call to loadDropDown().
			// tags:
			//		protected

			return true;
		},

		loadDropDown: function(/* Function */ loadCallback){
			// summary:
			//		Loads the data for the dropdown, and at some point, calls
			//		the given callback
			// tags:
			//		protected

			loadCallback();
		},

		toggleDropDown: function(){
			// summary:
			//		Toggle the drop-down widget; if it is up, close it, if not, open it
			// tags:
			//		protected

			if(this.disabled || this.readOnly){ return; }
			this.focus();
			var dropDown = this.dropDown;
			if(!dropDown){ return; }
			if(!this._opened){
				// If we aren't loaded, load it first so there isn't a flicker
				if(!this.isLoaded()){
					this.loadDropDown(dojo.hitch(this, "openDropDown"));
					return;
				}else{
					this.openDropDown();
				}
			}else{
				this.closeDropDown();
			}
		},

		openDropDown: function(){
			// summary:
			//		Opens the dropdown for this widget - it returns the
			//		return value of dijit.popup.open
			// tags:
			//		protected

			var dropDown = this.dropDown;
			var ddNode = dropDown.domNode;
			var self = this;

			// Prepare our popup's height and honor maxHeight if it exists.

			// TODO: isn't maxHeight dependent on the return value from dijit.popup.open(),
			// ie, dependent on how much space is available (BK)

			if(!this._preparedNode){
				dijit.popup.moveOffScreen(ddNode);
				this._preparedNode = true;			
				// Check if we have explicitly set width and height on the dropdown widget dom node
				if(ddNode.style.width){
					this._explicitDDWidth = true;
				}
				if(ddNode.style.height){
					this._explicitDDHeight = true;
				}
			}

			// Code for resizing dropdown (height limitation, or increasing width to match my width)
			if(this.maxHeight || this.forceWidth || this.autoWidth){
				var myStyle = {
					display: "",
					visibility: "hidden"
				};
				if(!this._explicitDDWidth){
					myStyle.width = "";
				}
				if(!this._explicitDDHeight){
					myStyle.height = "";
				}
				dojo.style(ddNode, myStyle);
				
				// Get size of drop down, and determine if vertical scroll bar needed
				var mb = dojo.marginBox(ddNode);
				var overHeight = (this.maxHeight && mb.h > this.maxHeight);
				dojo.style(ddNode, {
					overflowX: "hidden",
					overflowY: overHeight ? "auto" : "hidden"
				});
				if(overHeight){
					mb.h = this.maxHeight;
					if("w" in mb){
						mb.w += 16;	// room for vertical scrollbar
					}
				}else{
					delete mb.h;
				}
				delete mb.t;
				delete mb.l;

				// Adjust dropdown width to match or be larger than my width
				if(this.forceWidth){
					mb.w = this.domNode.offsetWidth;
				}else if(this.autoWidth){
					mb.w = Math.max(mb.w, this.domNode.offsetWidth);
				}else{
					delete mb.w;
				}
				
				// And finally, resize the dropdown to calculated height and width
				if(dojo.isFunction(dropDown.resize)){
					dropDown.resize(mb);
				}else{
					dojo.marginBox(ddNode, mb);
				}
			}

			var retVal = dijit.popup.open({
				parent: this,
				popup: dropDown,
				around: this._aroundNode,
				orient: dijit.getPopupAroundAlignment((this.dropDownPosition && this.dropDownPosition.length) ? this.dropDownPosition : ["below"],this.isLeftToRight()),
				onExecute: function(){
					self.closeDropDown(true);
				},
				onCancel: function(){
					self.closeDropDown(true);
				},
				onClose: function(){
					dojo.attr(self._popupStateNode, "popupActive", false);
					dojo.removeClass(self._popupStateNode, "dijitHasDropDownOpen");
					self._opened = false;
					self.state = "";
				}
			});
			dojo.attr(this._popupStateNode, "popupActive", "true");
			dojo.addClass(self._popupStateNode, "dijitHasDropDownOpen");
			this._opened=true;
			this.state="Opened";
			// TODO: set this.checked and call setStateClass(), to affect button look while drop down is shown
			return retVal;
		},

		closeDropDown: function(/*Boolean*/ focus){
			// summary:
			//		Closes the drop down on this widget
			// tags:
			//		protected

			if(this._opened){
				if(focus){ this.focus(); }
				dijit.popup.close(this.dropDown);
				this._opened = false;
				this.state = "";
			}
		}

	}
);

}

if(!dojo._hasResource["dijit.form.Button"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.Button"] = true;
dojo.provide("dijit.form.Button");





dojo.declare("dijit.form.Button",
	dijit.form._FormWidget,
	{
	// summary:
	//		Basically the same thing as a normal HTML button, but with special styling.
	// description:
	//		Buttons can display a label, an icon, or both.
	//		A label should always be specified (through innerHTML) or the label
	//		attribute.  It can be hidden via showLabel=false.
	// example:
	// |	<button dojoType="dijit.form.Button" onClick="...">Hello world</button>
	//
	// example:
	// |	var button1 = new dijit.form.Button({label: "hello world", onClick: foo});
	// |	dojo.body().appendChild(button1.domNode);

	// label: HTML String
	//		Text to display in button.
	//		If the label is hidden (showLabel=false) then and no title has
	//		been specified, then label is also set as title attribute of icon.
	label: "",

	// showLabel: Boolean
	//		Set this to true to hide the label text and display only the icon.
	//		(If showLabel=false then iconClass must be specified.)
	//		Especially useful for toolbars.
	//		If showLabel=true, the label will become the title (a.k.a. tooltip/hint) of the icon.
	//
	//		The exception case is for computers in high-contrast mode, where the label
	//		will still be displayed, since the icon doesn't appear.
	showLabel: true,

	// iconClass: String
	//		Class to apply to DOMNode in button to make it display an icon
	iconClass: "",

	// type: String
	//		Defines the type of button.  "button", "submit", or "reset".
	type: "button",

	baseClass: "dijitButton",

	templateString: dojo.cache("dijit.form", "templates/Button.html", "<span class=\"dijit dijitReset dijitInline\"\n\t><span class=\"dijitReset dijitInline dijitButtonNode\"\n\t\tdojoAttachEvent=\"ondijitclick:_onButtonClick\"\n\t\t><span class=\"dijitReset dijitStretch dijitButtonContents\"\n\t\t\tdojoAttachPoint=\"titleNode,focusNode\"\n\t\t\twaiRole=\"button\" waiState=\"labelledby-${id}_label\"\n\t\t\t><span class=\"dijitReset dijitInline dijitIcon\" dojoAttachPoint=\"iconNode\"></span\n\t\t\t><span class=\"dijitReset dijitToggleButtonIconChar\">&#x25CF;</span\n\t\t\t><span class=\"dijitReset dijitInline dijitButtonText\"\n\t\t\t\tid=\"${id}_label\"\n\t\t\t\tdojoAttachPoint=\"containerNode\"\n\t\t\t></span\n\t\t></span\n\t></span\n\t><input ${!nameAttrSetting} type=\"${type}\" value=\"${value}\" class=\"dijitOffScreen\"\n\t\tdojoAttachPoint=\"valueNode\"\n/></span>\n"),

	attributeMap: dojo.delegate(dijit.form._FormWidget.prototype.attributeMap, {
		value: "valueNode",
		iconClass: { node: "iconNode", type: "class" }
	}),


	_onClick: function(/*Event*/ e){
		// summary:
		//		Internal function to handle click actions
		if(this.disabled){
			return false;
		}
		this._clicked(); // widget click actions
		return this.onClick(e); // user click actions
	},

	_onButtonClick: function(/*Event*/ e){
		// summary:
		//		Handler when the user activates the button portion.
		if(this._onClick(e) === false){ // returning nothing is same as true
			e.preventDefault(); // needed for checkbox
		}else if(this.type == "submit" && !(this.valueNode||this.focusNode).form){ // see if a nonform widget needs to be signalled
			for(var node=this.domNode; node.parentNode/*#5935*/; node=node.parentNode){
				var widget=dijit.byNode(node);
				if(widget && typeof widget._onSubmit == "function"){
					widget._onSubmit(e);
					break;
				}
			}
		}else if(this.valueNode){
			this.valueNode.click();
			e.preventDefault(); // cancel BUTTON click and continue with hidden INPUT click
		}
	},

	_fillContent: function(/*DomNode*/ source){
		// Overrides _Templated._fillContent().
		// If button label is specified as srcNodeRef.innerHTML rather than
		// this.params.label, handle it here.
		if(source && (!this.params || !("label" in this.params))){
			this.set('label', source.innerHTML);
		}
	},

	postCreate: function(){
		dojo.setSelectable(this.focusNode, false);
		this.inherited(arguments);
	},

	_setShowLabelAttr: function(val){
		if(this.containerNode){
			dojo.toggleClass(this.containerNode, "dijitDisplayNone", !val);
		}
		this.showLabel = val;
	},

	onClick: function(/*Event*/ e){
		// summary:
		//		Callback for when button is clicked.
		//		If type="submit", return true to perform submit, or false to cancel it.
		// type:
		//		callback
		return true;		// Boolean
	},

	_clicked: function(/*Event*/ e){
		// summary:
		//		Internal overridable function for when the button is clicked
	},

	setLabel: function(/*String*/ content){
		// summary:
		//		Deprecated.  Use set('label', ...) instead.
		dojo.deprecated("dijit.form.Button.setLabel() is deprecated.  Use set('label', ...) instead.", "", "2.0");
		this.set("label", content);
	},

	_setLabelAttr: function(/*String*/ content){
		// summary:
		//		Hook for attr('label', ...) to work.
		// description:
		//		Set the label (text) of the button; takes an HTML string.
		this.containerNode.innerHTML = this.label = content;
		if(this.showLabel == false && !this.params.title){
			this.titleNode.title = dojo.trim(this.containerNode.innerText || this.containerNode.textContent || '');
		}
	}
});


dojo.declare("dijit.form.DropDownButton", [dijit.form.Button, dijit._Container, dijit._HasDropDown], {
	// summary:
	//		A button with a drop down
	//
	// example:
	// |	<button dojoType="dijit.form.DropDownButton" label="Hello world">
	// |		<div dojotype="dijit.Menu">...</div>
	// |	</button>
	//
	// example:
	// |	var button1 = new dijit.form.DropDownButton({ label: "hi", dropDown: new dijit.Menu(...) });
	// |	dojo.body().appendChild(button1);
	//

	baseClass : "dijitDropDownButton",

	templateString: dojo.cache("dijit.form", "templates/DropDownButton.html", "<span class=\"dijit dijitReset dijitInline\"\n\t><span class='dijitReset dijitInline dijitButtonNode'\n\t\tdojoAttachEvent=\"ondijitclick:_onButtonClick\" dojoAttachPoint=\"_buttonNode\"\n\t\t><span class=\"dijitReset dijitStretch dijitButtonContents\"\n\t\t\tdojoAttachPoint=\"focusNode,titleNode,_arrowWrapperNode\"\n\t\t\twaiRole=\"button\" waiState=\"haspopup-true,labelledby-${id}_label\"\n\t\t\t><span class=\"dijitReset dijitInline dijitIcon\"\n\t\t\t\tdojoAttachPoint=\"iconNode\"\n\t\t\t></span\n\t\t\t><span class=\"dijitReset dijitInline dijitButtonText\"\n\t\t\t\tdojoAttachPoint=\"containerNode,_popupStateNode\"\n\t\t\t\tid=\"${id}_label\"\n\t\t\t></span\n\t\t\t><span class=\"dijitReset dijitInline dijitArrowButtonInner\"></span\n\t\t\t><span class=\"dijitReset dijitInline dijitArrowButtonChar\">&#9660;</span\n\t\t></span\n\t></span\n\t><input ${!nameAttrSetting} type=\"${type}\" value=\"${value}\" class=\"dijitOffScreen\"\n\t\tdojoAttachPoint=\"valueNode\"\n/></span>\n"),

	_fillContent: function(){
		// Overrides Button._fillContent().
		//
		// My inner HTML contains both the button contents and a drop down widget, like
		// <DropDownButton>  <span>push me</span>  <Menu> ... </Menu> </DropDownButton>
		// The first node is assumed to be the button content. The widget is the popup.

		if(this.srcNodeRef){ // programatically created buttons might not define srcNodeRef
			//FIXME: figure out how to filter out the widget and use all remaining nodes as button
			//	content, not just nodes[0]
			var nodes = dojo.query("*", this.srcNodeRef);
			dijit.form.DropDownButton.superclass._fillContent.call(this, nodes[0]);

			// save pointer to srcNode so we can grab the drop down widget after it's instantiated
			this.dropDownContainer = this.srcNodeRef;
		}
	},

	startup: function(){
		if(this._started){ return; }

		// the child widget from srcNodeRef is the dropdown widget.  Insert it in the page DOM,
		// make it invisible, and store a reference to pass to the popup code.
		if(!this.dropDown){
			var dropDownNode = dojo.query("[widgetId]", this.dropDownContainer)[0];
			this.dropDown = dijit.byNode(dropDownNode);
			delete this.dropDownContainer;
		}
		dijit.popup.moveOffScreen(this.dropDown.domNode);

		this.inherited(arguments);
	},

	isLoaded: function(){
		// Returns whether or not we are loaded - if our dropdown has an href,
		// then we want to check that.
		var dropDown = this.dropDown;
		return (!dropDown.href || dropDown.isLoaded);
	},

	loadDropDown: function(){
		// Loads our dropdown
		var dropDown = this.dropDown;
		if(!dropDown){ return; }
		if(!this.isLoaded()){
			var handler = dojo.connect(dropDown, "onLoad", this, function(){
				dojo.disconnect(handler);
				this.openDropDown();
			});
			dropDown.refresh();
		}else{
			this.openDropDown();
		}
	},

	isFocusable: function(){
		// Overridden so that focus is handled by the _HasDropDown mixin, not by
		// the _FormWidget mixin.
		return this.inherited(arguments) && !this._mouseDown;
	}
});

dojo.declare("dijit.form.ComboButton", dijit.form.DropDownButton, {
	// summary:
	//		A combination button and drop-down button.
	//		Users can click one side to "press" the button, or click an arrow
	//		icon to display the drop down.
	//
	// example:
	// |	<button dojoType="dijit.form.ComboButton" onClick="...">
	// |		<span>Hello world</span>
	// |		<div dojoType="dijit.Menu">...</div>
	// |	</button>
	//
	// example:
	// |	var button1 = new dijit.form.ComboButton({label: "hello world", onClick: foo, dropDown: "myMenu"});
	// |	dojo.body().appendChild(button1.domNode);
	//

	templateString: dojo.cache("dijit.form", "templates/ComboButton.html", "<table class=\"dijit dijitReset dijitInline dijitLeft\"\n\tcellspacing='0' cellpadding='0' waiRole=\"presentation\"\n\t><tbody waiRole=\"presentation\"><tr waiRole=\"presentation\"\n\t\t><td class=\"dijitReset dijitStretch dijitButtonNode\" dojoAttachPoint=\"buttonNode\" dojoAttachEvent=\"ondijitclick:_onButtonClick,onkeypress:_onButtonKeyPress\"\n\t\t><div id=\"${id}_button\" class=\"dijitReset dijitButtonContents\"\n\t\t\tdojoAttachPoint=\"titleNode\"\n\t\t\twaiRole=\"button\" waiState=\"labelledby-${id}_label\"\n\t\t\t><div class=\"dijitReset dijitInline dijitIcon\" dojoAttachPoint=\"iconNode\" waiRole=\"presentation\"></div\n\t\t\t><div class=\"dijitReset dijitInline dijitButtonText\" id=\"${id}_label\" dojoAttachPoint=\"containerNode\" waiRole=\"presentation\"></div\n\t\t></div\n\t\t></td\n\t\t><td id=\"${id}_arrow\" class='dijitReset dijitRight dijitButtonNode dijitArrowButton'\n\t\t\tdojoAttachPoint=\"_popupStateNode,focusNode,_buttonNode\"\n\t\t\tdojoAttachEvent=\"onkeypress:_onArrowKeyPress\"\n\t\t\ttitle=\"${optionsTitle}\"\n\t\t\twaiRole=\"button\" waiState=\"haspopup-true\"\n\t\t\t><div class=\"dijitReset dijitArrowButtonInner\" waiRole=\"presentation\"></div\n\t\t\t><div class=\"dijitReset dijitArrowButtonChar\" waiRole=\"presentation\">&#9660;</div\n\t\t></td\n\t\t><td style=\"display:none !important;\"\n\t\t\t><input ${!nameAttrSetting} type=\"${type}\" value=\"${value}\" dojoAttachPoint=\"valueNode\"\n\t\t/></td></tr></tbody\n></table>\n"),

	attributeMap: dojo.mixin(dojo.clone(dijit.form.Button.prototype.attributeMap), {
		id: "",
		tabIndex: ["focusNode", "titleNode"],
		title: "titleNode"
	}),

	// optionsTitle: String
	//		Text that describes the options menu (accessibility)
	optionsTitle: "",

	baseClass: "dijitComboButton",

	// Set classes like dijitButtonContentsHover or dijitArrowButtonActive depending on
	// mouse action over specified node
	cssStateNodes: {
		"buttonNode": "dijitButtonNode",
		"titleNode": "dijitButtonContents",
		"_popupStateNode": "dijitDownArrowButton"
	},

	_focusedNode: null,

	_onButtonKeyPress: function(/*Event*/ evt){
		// summary:
		//		Handler for right arrow key when focus is on left part of button
		if(evt.charOrCode == dojo.keys[this.isLeftToRight() ? "RIGHT_ARROW" : "LEFT_ARROW"]){
			dijit.focus(this._popupStateNode);
			dojo.stopEvent(evt);
		}
	},

	_onArrowKeyPress: function(/*Event*/ evt){
		// summary:
		//		Handler for left arrow key when focus is on right part of button
		if(evt.charOrCode == dojo.keys[this.isLeftToRight() ? "LEFT_ARROW" : "RIGHT_ARROW"]){
			dijit.focus(this.titleNode);
			dojo.stopEvent(evt);
		}
	},
	
	focus: function(/*String*/ position){
		// summary:
		//		Focuses this widget to according to position, if specified,
		//		otherwise on arrow node
		// position:
		//		"start" or "end"
		
		dijit.focus(position == "start" ? this.titleNode : this._popupStateNode);
	}
});

dojo.declare("dijit.form.ToggleButton", dijit.form.Button, {
	// summary:
	//		A button that can be in two states (checked or not).
	//		Can be base class for things like tabs or checkbox or radio buttons

	baseClass: "dijitToggleButton",

	// checked: Boolean
	//		Corresponds to the native HTML <input> element's attribute.
	//		In markup, specified as "checked='checked'" or just "checked".
	//		True if the button is depressed, or the checkbox is checked,
	//		or the radio button is selected, etc.
	checked: false,

	attributeMap: dojo.mixin(dojo.clone(dijit.form.Button.prototype.attributeMap), {
		checked:"focusNode"
	}),

	_clicked: function(/*Event*/ evt){
		this.set('checked', !this.checked);
	},

	_setCheckedAttr: function(/*Boolean*/ value, /* Boolean? */ priorityChange){
		this.checked = value;
		dojo.attr(this.focusNode || this.domNode, "checked", value);
		dijit.setWaiState(this.focusNode || this.domNode, "pressed", value);
		this._handleOnChange(value, priorityChange);
	},

	setChecked: function(/*Boolean*/ checked){
		// summary:
		//		Deprecated.   Use set('checked', true/false) instead.
		dojo.deprecated("setChecked("+checked+") is deprecated. Use set('checked',"+checked+") instead.", "", "2.0");
		this.set('checked', checked);
	},

	reset: function(){
		// summary:
		//		Reset the widget's value to what it was at initialization time

		this._hasBeenBlurred = false;

		// set checked state to original setting
		this.set('checked', this.params.checked || false);
	}
});

}

if(!dojo._hasResource["dijit._editor._Plugin"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor._Plugin"] = true;
dojo.provide("dijit._editor._Plugin");



dojo.declare("dijit._editor._Plugin", null, {
	// summary
	//		Base class for a "plugin" to the editor, which is usually
	//		a single button on the Toolbar and some associated code

	constructor: function(/*Object?*/args, /*DomNode?*/node){
		this.params = args || {};
		dojo.mixin(this, this.params);
		this._connects=[];
	},

	// editor: [const] dijit.Editor
	//		Points to the parent editor
	editor: null,

	// iconClassPrefix: [const] String
	//		The CSS class name for the button node is formed from `iconClassPrefix` and `command`
	iconClassPrefix: "dijitEditorIcon",

	// button: dijit._Widget?
	//		Pointer to `dijit.form.Button` or other widget (ex: `dijit.form.FilteringSelect`)
	//		that is added to the toolbar to control this plugin.
	//		If not specified, will be created on initialization according to `buttonClass`
	button: null,

	// command: String
	//		String like "insertUnorderedList", "outdent", "justifyCenter", etc. that represents an editor command.
	//		Passed to editor.execCommand() if `useDefaultCommand` is true.
	command: "",

	// useDefaultCommand: Boolean
	//		If true, this plugin executes by calling Editor.execCommand() with the argument specified in `command`.
	useDefaultCommand: true,

	// buttonClass: Widget Class
	//		Class of widget (ex: dijit.form.Button or dijit.form.FilteringSelect)
	//		that is added to the toolbar to control this plugin.
	//		This is used to instantiate the button, unless `button` itself is specified directly.
	buttonClass: dijit.form.Button,

	getLabel: function(/*String*/key){
		// summary:
		//		Returns the label to use for the button
		// tags:
		//		private
		return this.editor.commands[key];		// String
	},

	_initButton: function(){
		// summary:
		//		Initialize the button or other widget that will control this plugin.
		//		This code only works for plugins controlling built-in commands in the editor.
		// tags:
		//		protected extension
		if(this.command.length){
			var label = this.getLabel(this.command),
				editor = this.editor,
				className = this.iconClassPrefix+" "+this.iconClassPrefix + this.command.charAt(0).toUpperCase() + this.command.substr(1);
			if(!this.button){
				var props = dojo.mixin({
					label: label,
					dir: editor.dir,
					lang: editor.lang,
					showLabel: false,
					iconClass: className,
					dropDown: this.dropDown,
					tabIndex: "-1"
				}, this.params || {});
				this.button = new this.buttonClass(props);
			}
		}
	},

	destroy: function(){
		// summary:
		//		Destroy this plugin

		dojo.forEach(this._connects, dojo.disconnect);
		if(this.dropDown){
			this.dropDown.destroyRecursive();
		}
	},

	connect: function(o, f, tf){
		// summary:
		//		Make a dojo.connect() that is automatically disconnected when this plugin is destroyed.
		//		Similar to `dijit._Widget.connect`.
		// tags:
		//		protected
		this._connects.push(dojo.connect(o, f, this, tf));
	},

	updateState: function(){
		// summary:
		//		Change state of the plugin to respond to events in the editor.
		// description:
		//		This is called on meaningful events in the editor, such as change of selection
		//		or caret position (but not simple typing of alphanumeric keys).   It gives the
		//		plugin a chance to update the CSS of its button.
		//
		//		For example, the "bold" plugin will highlight/unhighlight the bold button depending on whether the
		//		characters next to the caret are bold or not.
		//
		//		Only makes sense when `useDefaultCommand` is true, as it calls Editor.queryCommandEnabled(`command`).
		var e = this.editor,
			c = this.command,
			checked, enabled;
		if(!e || !e.isLoaded || !c.length){ return; }
		if(this.button){
			try{
				enabled = e.queryCommandEnabled(c);
				if(this.enabled !== enabled){
					this.enabled = enabled;
					this.button.set('disabled', !enabled);
				}
				if(typeof this.button.checked == 'boolean'){
					checked = e.queryCommandState(c);
					if(this.checked !== checked){
						this.checked = checked;
						this.button.set('checked', e.queryCommandState(c));
					}
				}
			}catch(e){
				console.log(e); // FIXME: we shouldn't have debug statements in our code.  Log as an error?
			}
		}
	},

	setEditor: function(/*dijit.Editor*/ editor){
		// summary:
		//		Tell the plugin which Editor it is associated with.

		// TODO: refactor code to just pass editor to constructor.

		// FIXME: detach from previous editor!!
		this.editor = editor;

		// FIXME: prevent creating this if we don't need to (i.e., editor can't handle our command)
		this._initButton();

		// Processing for buttons that execute by calling editor.execCommand()
		if(this.button && this.useDefaultCommand){
			if(this.editor.queryCommandAvailable(this.command)){
				this.connect(this.button, "onClick",
					dojo.hitch(this.editor, "execCommand", this.command, this.commandArg)
				);
			}else{
				// hide button because editor doesn't support command (due to browser limitations)
				this.button.domNode.style.display = "none";			
			}
		}

		this.connect(this.editor, "onNormalizedDisplayChanged", "updateState");
	},

	setToolbar: function(/*dijit.Toolbar*/ toolbar){
		// summary:
		//		Tell the plugin to add it's controller widget (often a button)
		//		to the toolbar.  Does nothing if there is no controller widget.

		// TODO: refactor code to just pass toolbar to constructor.

		if(this.button){
			toolbar.addChild(this.button);
		}
		// console.debug("adding", this.button, "to:", toolbar);
	}
});

}

if(!dojo._hasResource["dijit._editor.plugins.EnterKeyHandling"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.plugins.EnterKeyHandling"] = true;
dojo.provide("dijit._editor.plugins.EnterKeyHandling");



dojo.declare("dijit._editor.plugins.EnterKeyHandling", dijit._editor._Plugin, {
	// summary:
	//		This plugin tries to make all browsers behave consistently w.r.t
	//		displaying paragraphs, specifically dealing with when the user presses
	//		the ENTER key.
	//
	//		It deals mainly with how the text appears on the screen (specifically
	//		address the double-spaced line problem on IE), but also has some code
	//		to normalize what attr('value') returns.
	//
	// description:
	//		This plugin has three modes:
	//
	//			* blockModeForEnter=BR
	//			* blockModeForEnter=DIV
	//			* blockModeForEnter=P
	//
	//		In blockModeForEnter=P, the ENTER key semantically means "start a new
	//		paragraph", whereas shift-ENTER means  "new line in the current paragraph".
	//		For example:
	//
	//		|	first paragraph <shift-ENTER>
	//		|	second line of first paragraph <ENTER>
	//		|
	//		|	second paragraph
	//
	//		In the other two modes, the ENTER key means to go to a new line in the
	//		current paragraph, and users [visually] create a new paragraph by pressing ENTER twice.
	//		For example, if the user enters text into an editor like this:
	//
	//		|		one <ENTER>
	//		|		two <ENTER>
	//		|		three <ENTER>
	//		|		<ENTER>
	//		|		four <ENTER>
	//		|		five <ENTER>
	//		|		six <ENTER>
	//
	//		It will appear on the screen as two paragraphs of three lines each.
	//
	//		blockNodeForEnter=BR
	//		--------------------
	//		On IE, typing the above keystrokes in the editor will internally produce DOM of:
	//
	//		|		<p>one</p>
	//		|		<p>two</p>
	//		|		<p>three</p>
	//		|		<p></p>
	//		|		<p>four</p>
	//		|		<p>five</p>
	//		|		<p>six</p>
	//
	//		However, blockNodeForEnter=BR makes the Editor on IE display like other browsers, by
	//		changing the CSS for the <p> node to not have top/bottom margins,
	//		thus eliminating the double-spaced appearance.
	//
	//		Also, attr('value') when used w/blockNodeForEnter=br on IE will return:
	//
	//		|	<p> one <br> two <br> three </p>
	//		|	<p> four <br> five <br> six </p>
	//
	//		This output normalization implemented by a filter when the
	//		editor writes out it's data, to convert consecutive <p>
	//		nodes into a single <p> node with internal <br> separators.
	//
	//		There's also a pre-filter to mirror the post-filter.
	//		It converts a single <p> with <br> line breaks
	//		into separate <p> nodes, and creates empty <p> nodes for spacing
	//		between paragraphs.
	//
	//		On FF typing the above keystrokes will internally generate:
	//
	//		|		one <br> two <br> three <br> <br> four <br> five <br> six <br>
	//
	//		And on Safari it will generate:
	//
	//		|		"one"
	//		|		<div>two</div>
	//		|		<div>three</div>
	//		|		<div><br></div>
	//		|		<div>four</div>
	//		|		<div>five</div>
	//		|		<div>six</div>
	//
	//		Thus, Safari and FF already look correct although semantically their content is a bit strange.
	//		On Safari or Firefox blockNodeForEnter=BR uses the builtin editor command "insertBrOnReturn",
	//		but that doesn't seem to do anything.
	//		Thus, attr('value') on safari/FF returns the browser-specific HTML listed above,
	//		rather than the semantically meaningful value that IE returns: <p>one<br>two</p> <p>three<br>four</p>.
	//
	//		(Note: originally based on http://bugs.dojotoolkit.org/ticket/2859)
	//
	//		blockNodeForEnter=P
	//		-------------------
	//		Plugin will monitor keystrokes and update the editor's content on the fly,
	//		so that the ENTER key will create a new <p> on FF and Safari (it already
	//		works that way by default on IE).
	//
	//		blockNodeForEnter=DIV
	//		---------------------
	//		Follows the same code path as blockNodeForEnter=P but inserting a <div>
	//		on ENTER key.  Although it produces strange internal DOM, like this:
	//
	//		|	<div>paragraph one</div>
	//		|	<div>paragraph one, line 2</div>
	//		|	<div>&nbsp;</div>
	//		|	<div>paragraph two</div>
	//
	//		it does provide a consistent look on all browsers, and the on-the-fly DOM updating
	//		can be useful for collaborative editing.

	// blockNodeForEnter: String
	//		This property decides the behavior of Enter key. It can be either P,
	//		DIV, BR, or empty (which means disable this feature). Anything else
	//		will trigger errors.
	//
	//		See class description for more details.
	blockNodeForEnter: 'BR',

	constructor: function(args){
		if(args){
			dojo.mixin(this,args);
		}
	},

	setEditor: function(editor){
		// Overrides _Plugin.setEditor().
		this.editor = editor;
		if(this.blockNodeForEnter == 'BR'){
			if(dojo.isIE){
				editor.contentDomPreFilters.push(dojo.hitch(this, "regularPsToSingleLinePs"));
				editor.contentDomPostFilters.push(dojo.hitch(this, "singleLinePsToRegularPs"));
				editor.onLoadDeferred.addCallback(dojo.hitch(this, "_fixNewLineBehaviorForIE"));
			}else{
				editor.onLoadDeferred.addCallback(dojo.hitch(this,function(d){
					try{
						this.editor.document.execCommand("insertBrOnReturn", false, true);
					}catch(e){}
					return d;
				}));
			}
		}else if(this.blockNodeForEnter){
			// add enter key handler
			// FIXME: need to port to the new event code!!
			dojo['require']('dijit._editor.range');
			var h = dojo.hitch(this,this.handleEnterKey);
			editor.addKeyHandler(13, 0, 0, h); //enter
			editor.addKeyHandler(13, 0, 1, h); //shift+enter
			this.connect(this.editor,'onKeyPressed','onKeyPressed');
		}
	},
	onKeyPressed: function(e){
		// summary:
		//		Handler for keypress events.
		// tags:
		//		private
		if(this._checkListLater){
			if(dojo.withGlobal(this.editor.window, 'isCollapsed', dijit)){
				var liparent=dojo.withGlobal(this.editor.window, 'getAncestorElement', dijit._editor.selection, ['LI']);
				if(!liparent){
					// circulate the undo detection code by calling RichText::execCommand directly
					dijit._editor.RichText.prototype.execCommand.call(this.editor, 'formatblock',this.blockNodeForEnter);
					// set the innerHTML of the new block node
					var block = dojo.withGlobal(this.editor.window, 'getAncestorElement', dijit._editor.selection, [this.blockNodeForEnter]);
					if(block){
						block.innerHTML=this.bogusHtmlContent;
						if(dojo.isIE){
							// move to the start by moving backwards one char
							var r = this.editor.document.selection.createRange();
							r.move('character',-1);
							r.select();
						}
					}else{
						console.error('onKeyPressed: Cannot find the new block node'); // FIXME
					}
				}else{
					if(dojo.isMoz){
						if(liparent.parentNode.parentNode.nodeName == 'LI'){
							liparent=liparent.parentNode.parentNode;
						}
					}
					var fc=liparent.firstChild;
					if(fc && fc.nodeType == 1 && (fc.nodeName == 'UL' || fc.nodeName == 'OL')){
						liparent.insertBefore(fc.ownerDocument.createTextNode('\xA0'),fc);
						var newrange = dijit.range.create(this.editor.window);
						newrange.setStart(liparent.firstChild,0);
						var selection = dijit.range.getSelection(this.editor.window, true);
						selection.removeAllRanges();
						selection.addRange(newrange);
					}
				}
			}
			this._checkListLater = false;
		}
		if(this._pressedEnterInBlock){
			// the new created is the original current P, so we have previousSibling below
			if(this._pressedEnterInBlock.previousSibling){
				this.removeTrailingBr(this._pressedEnterInBlock.previousSibling);
			}
			delete this._pressedEnterInBlock;
		}
	},

	// bogusHtmlContent: [private] String
	//		HTML to stick into a new empty block
	bogusHtmlContent: '&nbsp;',

	// blockNodes: [private] Regex
	//		Regex for testing if a given tag is a block level (display:block) tag
	blockNodes: /^(?:P|H1|H2|H3|H4|H5|H6|LI)$/,

	handleEnterKey: function(e){
		// summary:
		//		Handler for enter key events when blockModeForEnter is DIV or P.
		// description:
		//		Manually handle enter key event to make the behavior consistent across
		//		all supported browsers. See class description for details.
		// tags:
		//		private

		var selection, range, newrange, doc=this.editor.document,br;
		if(e.shiftKey){		// shift+enter always generates <br>
			var parent = dojo.withGlobal(this.editor.window, "getParentElement", dijit._editor.selection);
			var header = dijit.range.getAncestor(parent,this.blockNodes);
			if(header){
				if(!e.shiftKey && header.tagName == 'LI'){
					return true; // let browser handle
				}
				selection = dijit.range.getSelection(this.editor.window);
				range = selection.getRangeAt(0);
				if(!range.collapsed){
					range.deleteContents();
					selection = dijit.range.getSelection(this.editor.window);
					range = selection.getRangeAt(0);
				}
				if(dijit.range.atBeginningOfContainer(header, range.startContainer, range.startOffset)){
					if(e.shiftKey){
						br=doc.createElement('br');
						newrange = dijit.range.create(this.editor.window);
						header.insertBefore(br,header.firstChild);
						newrange.setStartBefore(br.nextSibling);
						selection.removeAllRanges();
						selection.addRange(newrange);
					}else{
						dojo.place(br, header, "before");
					}
				}else if(dijit.range.atEndOfContainer(header, range.startContainer, range.startOffset)){
					newrange = dijit.range.create(this.editor.window);
					br=doc.createElement('br');
					if(e.shiftKey){
						header.appendChild(br);
						header.appendChild(doc.createTextNode('\xA0'));
						newrange.setStart(header.lastChild,0);
					}else{
						dojo.place(br, header, "after");
						newrange.setStartAfter(header);
					}

					selection.removeAllRanges();
					selection.addRange(newrange);
				}else{
					return true; // let browser handle
				}
			}else{
				// don't change this: do not call this.execCommand, as that may have other logic in subclass
				dijit._editor.RichText.prototype.execCommand.call(this.editor, 'inserthtml', '<br>');
			}
			return false;
		}
		var _letBrowserHandle = true;

		// first remove selection
		selection = dijit.range.getSelection(this.editor.window);
		range = selection.getRangeAt(0);
		if(!range.collapsed){
			range.deleteContents();
			selection = dijit.range.getSelection(this.editor.window);
			range = selection.getRangeAt(0);
		}

		var block = dijit.range.getBlockAncestor(range.endContainer, null, this.editor.editNode);
		var blockNode = block.blockNode;

		// if this is under a LI or the parent of the blockNode is LI, just let browser to handle it
		if((this._checkListLater = (blockNode && (blockNode.nodeName == 'LI' || blockNode.parentNode.nodeName == 'LI')))){
			if(dojo.isMoz){
				// press enter in middle of P may leave a trailing <br/>, let's remove it later
				this._pressedEnterInBlock = blockNode;
			}
			// if this li only contains spaces, set the content to empty so the browser will outdent this item
			if(/^(\s|&nbsp;|\xA0|<span\b[^>]*\bclass=['"]Apple-style-span['"][^>]*>(\s|&nbsp;|\xA0)<\/span>)?(<br>)?$/.test(blockNode.innerHTML)){
				// empty LI node
				blockNode.innerHTML = '';
				if(dojo.isWebKit){ // WebKit tosses the range when innerHTML is reset
					newrange = dijit.range.create(this.editor.window);
					newrange.setStart(blockNode, 0);
					selection.removeAllRanges();
					selection.addRange(newrange);
				}
				this._checkListLater = false; // nothing to check since the browser handles outdent
			}
			return true;
		}

		// text node directly under body, let's wrap them in a node
		if(!block.blockNode || block.blockNode===this.editor.editNode){
			try{
				dijit._editor.RichText.prototype.execCommand.call(this.editor, 'formatblock',this.blockNodeForEnter);
			}catch(e2){ /*squelch FF3 exception bug when editor content is a single BR*/ }
			// get the newly created block node
			// FIXME
			block = {blockNode:dojo.withGlobal(this.editor.window, "getAncestorElement", dijit._editor.selection, [this.blockNodeForEnter]),
					blockContainer: this.editor.editNode};
			if(block.blockNode){
				if(block.blockNode != this.editor.editNode &&
					(!(block.blockNode.textContent || block.blockNode.innerHTML).replace(/^\s+|\s+$/g, "").length)){
					this.removeTrailingBr(block.blockNode);
					return false;
				}
			}else{	// we shouldn't be here if formatblock worked
				block.blockNode = this.editor.editNode;
			}
			selection = dijit.range.getSelection(this.editor.window);
			range = selection.getRangeAt(0);
		}

		var newblock = doc.createElement(this.blockNodeForEnter);
		newblock.innerHTML=this.bogusHtmlContent;
		this.removeTrailingBr(block.blockNode);
		if(dijit.range.atEndOfContainer(block.blockNode, range.endContainer, range.endOffset)){
			if(block.blockNode === block.blockContainer){
				block.blockNode.appendChild(newblock);
			}else{
				dojo.place(newblock, block.blockNode, "after");
			}
			_letBrowserHandle = false;
			// lets move caret to the newly created block
			newrange = dijit.range.create(this.editor.window);
			newrange.setStart(newblock, 0);
			selection.removeAllRanges();
			selection.addRange(newrange);
			if(this.editor.height){
				dojo.window.scrollIntoView(newblock);
			}
		}else if(dijit.range.atBeginningOfContainer(block.blockNode,
				range.startContainer, range.startOffset)){
			dojo.place(newblock, block.blockNode, block.blockNode === block.blockContainer ? "first" : "before");
			if(newblock.nextSibling && this.editor.height){
				// position input caret - mostly WebKit needs this
				newrange = dijit.range.create(this.editor.window);
				newrange.setStart(newblock.nextSibling, 0);
				selection.removeAllRanges();
				selection.addRange(newrange);
				// browser does not scroll the caret position into view, do it manually
				dojo.window.scrollIntoView(newblock.nextSibling);
			}
			_letBrowserHandle = false;
		}else{ //press enter in the middle of P/DIV/Whatever/
			if(block.blockNode === block.blockContainer){
				block.blockNode.appendChild(newblock);
			}else{
				dojo.place(newblock, block.blockNode, "after");
			}
			_letBrowserHandle = false;

			// Clone any block level styles.
			if(block.blockNode.style){
				if(newblock.style){
					if(block.blockNode.style.cssText){
						newblock.style.cssText = block.blockNode.style.cssText;
					}
				}
			}
			
			// Okay, we probably have to split.
			var rs = range.startContainer;
			if(rs && rs.nodeType == 3){
				// Text node, we have to split it.
				var nodeToMove, tNode;
				var txt = rs.nodeValue;
				var startNode = doc.createTextNode(txt.substring(0, range.startOffset));
				var endNode = doc.createTextNode(txt.substring(range.startOffset, txt.length));

				// Place the split, then remove original nodes.
				dojo.place(startNode, rs, "before");
				dojo.place(endNode, rs, "after");
				dojo.destroy(rs);

				// Okay, we split the text.  Now we need to see if we're
				// parented to the block element we're splitting and if
				// not, we have to split all the way up.  Ugh.
				var parentC = startNode.parentNode;
				while(parentC !== block.blockNode){
					var tg = parentC.tagName;
					var newTg = doc.createElement(tg);
					// Clone over any 'style' data. 
					if(parentC.style){
						if(newTg.style){
							if(parentC.style.cssText){
								newTg.style.cssText = parentC.style.cssText;
							}
						}
					}

					nodeToMove = endNode;
					while(nodeToMove){
						tNode = nodeToMove.nextSibling;
						newTg.appendChild(nodeToMove);
						nodeToMove = tNode;
					}
					dojo.place(newTg, parentC, "after");
					startNode = parentC;
					endNode = newTg;
					parentC = parentC.parentNode;
				}

				// Lastly, move the split out tags to the new block.
				// as they should now be split properly.
				nodeToMove = endNode;
				if(nodeToMove.nodeType == 1 || (nodeToMove.nodeType == 3 && nodeToMove.nodeValue)){
					// Non-blank text and non-text nodes need to clear out that blank space
					// before moving the contents.
					newblock.innerHTML = "";
				}
				while(nodeToMove){
					tNode = nodeToMove.nextSibling;
					newblock.appendChild(nodeToMove);
					nodeToMove = tNode;
				}
			}
			
			//lets move caret to the newly created block
			newrange = dijit.range.create(this.editor.window);
			newrange.setStart(newblock, 0);
			selection.removeAllRanges();
			selection.addRange(newrange);
			if(this.editor.height){
				dijit.scrollIntoView(newblock);
			}
			if(dojo.isMoz){
				// press enter in middle of P may leave a trailing <br/>, let's remove it later
				this._pressedEnterInBlock = block.blockNode;
			}
		}
		return _letBrowserHandle;
	},

	removeTrailingBr: function(container){
		// summary:
		//		If last child of container is a <br>, then remove it.
		// tags:
		//		private
		var para = /P|DIV|LI/i.test(container.tagName) ?
			container : dijit._editor.selection.getParentOfType(container,['P','DIV','LI']);

		if(!para){ return; }
		if(para.lastChild){
			if((para.childNodes.length > 1 && para.lastChild.nodeType == 3 && /^[\s\xAD]*$/.test(para.lastChild.nodeValue)) ||
				para.lastChild.tagName=='BR'){

				dojo.destroy(para.lastChild);
			}
		}
		if(!para.childNodes.length){
			para.innerHTML=this.bogusHtmlContent;
		}
	},
	_fixNewLineBehaviorForIE: function(d){
		// summary:
		//		Insert CSS so <p> nodes don't have spacing around them,
		//		thus hiding the fact that ENTER key on IE is creating new
		//		paragraphs

		// cannot use !important since there may be custom user styling;
		var doc = this.editor.document;
		if(doc.__INSERTED_EDITIOR_NEWLINE_CSS === undefined){
			var style = dojo.create("style", {type: "text/css"}, doc.getElementsByTagName("head")[0]);
			style.styleSheet.cssText = "p{margin:0;}"; // cannot use !important since there may be custom user styling;
			this.editor.document.__INSERTED_EDITIOR_NEWLINE_CSS = true;
		}
		return d;
	},
	regularPsToSingleLinePs: function(element, noWhiteSpaceInEmptyP){
		// summary:
		//		Converts a <p> node containing <br>'s into multiple <p> nodes.
		// description:
		//		See singleLinePsToRegularPs().   This method does the
		//		opposite thing, and is used as a pre-filter when loading the
		//		editor, to mirror the effects of the post-filter at end of edit.
		// tags:
		//		private
		function wrapLinesInPs(el){
		  // move "lines" of top-level text nodes into ps
			function wrapNodes(nodes){
				// nodes are assumed to all be siblings
				var newP = nodes[0].ownerDocument.createElement('p'); // FIXME: not very idiomatic
				nodes[0].parentNode.insertBefore(newP, nodes[0]);
				dojo.forEach(nodes, function(node){
					newP.appendChild(node);
				});
			}

			var currentNodeIndex = 0;
			var nodesInLine = [];
			var currentNode;
			while(currentNodeIndex < el.childNodes.length){
				currentNode = el.childNodes[currentNodeIndex];
				if( currentNode.nodeType==3 ||	// text node
					(currentNode.nodeType==1 && currentNode.nodeName!='BR' && dojo.style(currentNode, "display")!="block")
				){
					nodesInLine.push(currentNode);
				}else{
					// hit line delimiter; process nodesInLine if there are any
					var nextCurrentNode = currentNode.nextSibling;
					if(nodesInLine.length){
						wrapNodes(nodesInLine);
						currentNodeIndex = (currentNodeIndex+1)-nodesInLine.length;
						if(currentNode.nodeName=="BR"){
							dojo.destroy(currentNode);
						}
					}
					nodesInLine = [];
				}
				currentNodeIndex++;
			}
			if(nodesInLine.length){ wrapNodes(nodesInLine); }
		}

		function splitP(el){
			// split a paragraph into seperate paragraphs at BRs
			var currentNode = null;
			var trailingNodes = [];
			var lastNodeIndex = el.childNodes.length-1;
			for(var i=lastNodeIndex; i>=0; i--){
				currentNode = el.childNodes[i];
				if(currentNode.nodeName=="BR"){
					var newP = currentNode.ownerDocument.createElement('p');
					dojo.place(newP, el, "after");
					if(trailingNodes.length==0 && i != lastNodeIndex){
						newP.innerHTML = "&nbsp;"
					}
					dojo.forEach(trailingNodes, function(node){
						newP.appendChild(node);
					});
					dojo.destroy(currentNode);
					trailingNodes = [];
				}else{
					trailingNodes.unshift(currentNode);
				}
			}
		}

		var pList = [];
		var ps = element.getElementsByTagName('p');
		dojo.forEach(ps, function(p){ pList.push(p); });
		dojo.forEach(pList, function(p){
			var prevSib = p.previousSibling;
			if(	(prevSib) && (prevSib.nodeType == 1) && 
				(prevSib.nodeName == 'P' || dojo.style(prevSib, 'display') != 'block')
			){
				var newP = p.parentNode.insertBefore(this.document.createElement('p'), p);
				// this is essential to prevent IE from losing the P.
				// if it's going to be innerHTML'd later we need
				// to add the &nbsp; to _really_ force the issue
				newP.innerHTML = noWhiteSpaceInEmptyP ? "" : "&nbsp;";
			}
			splitP(p);
		},this.editor);
		wrapLinesInPs(element);
		return element;
	},

	singleLinePsToRegularPs: function(element){
		// summary:
		//		Called as post-filter.
		//		Apparently collapses adjacent <p> nodes into a single <p>
		//		nodes with <br> separating each line.
		//
		// example:
		//		Given this input:
		//	|	<p>line 1</p>
		//	|	<p>line 2</p>
		//	|	<ol>
		//	|		<li>item 1
		//	|		<li>item 2
		//	|	</ol>
		//	|	<p>line 3</p>
		//	|	<p>line 4</p>
		//
		//		Will convert to:
		//	|	<p>line 1<br>line 2</p>
		//	|	<ol>
		//	|		<li>item 1
		//	|		<li>item 2
		//	|	</ol>
		//	|	<p>line 3<br>line 4</p>
		//
		//		Not sure why this situation would even come up after the pre-filter and
		//		the enter-key-handling code.
		//
		// tags:
		//		private

		function getParagraphParents(node){
			// summary:
			//		Used to get list of all nodes that contain paragraphs.
			//		Seems like that would just be the very top node itself, but apparently not.
			var ps = node.getElementsByTagName('p');
			var parents = [];
			for(var i=0; i<ps.length; i++){
				var p = ps[i];
				var knownParent = false;
				for(var k=0; k < parents.length; k++){
					if(parents[k] === p.parentNode){
						knownParent = true;
						break;
					}
				}
				if(!knownParent){
					parents.push(p.parentNode);
				}
			}
			return parents;
		}

		function isParagraphDelimiter(node){
			return (!node.childNodes.length || node.innerHTML=="&nbsp;");
		}

		var paragraphContainers = getParagraphParents(element);
		for(var i=0; i<paragraphContainers.length; i++){
			var container = paragraphContainers[i];
			var firstPInBlock = null;
			var node = container.firstChild;
			var deleteNode = null;
			while(node){
				if(node.nodeType != 1 || node.tagName != 'P' ||
						(node.getAttributeNode('style') || {/*no style*/}).specified){
					firstPInBlock = null;
				}else if(isParagraphDelimiter(node)){
					deleteNode = node;
					firstPInBlock = null;
				}else{
					if(firstPInBlock == null){
						firstPInBlock = node;
					}else{
						if( (!firstPInBlock.lastChild || firstPInBlock.lastChild.nodeName != 'BR') &&
							(node.firstChild) &&
							(node.firstChild.nodeName != 'BR')
						){
							firstPInBlock.appendChild(this.editor.document.createElement('br'));
						}
						while(node.firstChild){
							firstPInBlock.appendChild(node.firstChild);
						}
						deleteNode = node;
					}
				}
				node = node.nextSibling;
				if(deleteNode){
					dojo.destroy(deleteNode);
					deleteNode = null;
				}
			}
		}
		return element;
	}
});

}

if(!dojo._hasResource["dojo.i18n"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.i18n"] = true;
dojo.provide("dojo.i18n");

/*=====
dojo.i18n = {
	// summary: Utility classes to enable loading of resources for internationalization (i18n)
};
=====*/

dojo.i18n.getLocalization = function(/*String*/packageName, /*String*/bundleName, /*String?*/locale){
	//	summary:
	//		Returns an Object containing the localization for a given resource
	//		bundle in a package, matching the specified locale.
	//	description:
	//		Returns a hash containing name/value pairs in its prototypesuch
	//		that values can be easily overridden.  Throws an exception if the
	//		bundle is not found.  Bundle must have already been loaded by
	//		`dojo.requireLocalization()` or by a build optimization step.  NOTE:
	//		try not to call this method as part of an object property
	//		definition (`var foo = { bar: dojo.i18n.getLocalization() }`).  In
	//		some loading situations, the bundle may not be available in time
	//		for the object definition.  Instead, call this method inside a
	//		function that is run after all modules load or the page loads (like
	//		in `dojo.addOnLoad()`), or in a widget lifecycle method.
	//	packageName:
	//		package which is associated with this resource
	//	bundleName:
	//		the base filename of the resource bundle (without the ".js" suffix)
	//	locale:
	//		the variant to load (optional).  By default, the locale defined by
	//		the host environment: dojo.locale

	locale = dojo.i18n.normalizeLocale(locale);

	// look for nearest locale match
	var elements = locale.split('-');
	var module = [packageName,"nls",bundleName].join('.');
	var bundle = dojo._loadedModules[module];
	if(bundle){
		var localization;
		for(var i = elements.length; i > 0; i--){
			var loc = elements.slice(0, i).join('_');
			if(bundle[loc]){
				localization = bundle[loc];
				break;
			}
		}
		if(!localization){
			localization = bundle.ROOT;
		}

		// make a singleton prototype so that the caller won't accidentally change the values globally
		if(localization){
			var clazz = function(){};
			clazz.prototype = localization;
			return new clazz(); // Object
		}
	}

	throw new Error("Bundle not found: " + bundleName + " in " + packageName+" , locale=" + locale);
};

dojo.i18n.normalizeLocale = function(/*String?*/locale){
	//	summary:
	//		Returns canonical form of locale, as used by Dojo.
	//
	//  description:
	//		All variants are case-insensitive and are separated by '-' as specified in [RFC 3066](http://www.ietf.org/rfc/rfc3066.txt).
	//		If no locale is specified, the dojo.locale is returned.  dojo.locale is defined by
	//		the user agent's locale unless overridden by djConfig.

	var result = locale ? locale.toLowerCase() : dojo.locale;
	if(result == "root"){
		result = "ROOT";
	}
	return result; // String
};

dojo.i18n._requireLocalization = function(/*String*/moduleName, /*String*/bundleName, /*String?*/locale, /*String?*/availableFlatLocales){
	//	summary:
	//		See dojo.requireLocalization()
	//	description:
	// 		Called by the bootstrap, but factored out so that it is only
	// 		included in the build when needed.

	var targetLocale = dojo.i18n.normalizeLocale(locale);
 	var bundlePackage = [moduleName, "nls", bundleName].join(".");
	// NOTE: 
	//		When loading these resources, the packaging does not match what is
	//		on disk.  This is an implementation detail, as this is just a
	//		private data structure to hold the loaded resources.  e.g.
	//		`tests/hello/nls/en-us/salutations.js` is loaded as the object
	//		`tests.hello.nls.salutations.en_us={...}` The structure on disk is
	//		intended to be most convenient for developers and translators, but
	//		in memory it is more logical and efficient to store in a different
	//		order.  Locales cannot use dashes, since the resulting path will
	//		not evaluate as valid JS, so we translate them to underscores.
	
	//Find the best-match locale to load if we have available flat locales.
	var bestLocale = "";
	if(availableFlatLocales){
		var flatLocales = availableFlatLocales.split(",");
		for(var i = 0; i < flatLocales.length; i++){
			//Locale must match from start of string.
			//Using ["indexOf"] so customBase builds do not see
			//this as a dojo._base.array dependency.
			if(targetLocale["indexOf"](flatLocales[i]) == 0){
				if(flatLocales[i].length > bestLocale.length){
					bestLocale = flatLocales[i];
				}
			}
		}
		if(!bestLocale){
			bestLocale = "ROOT";
		}		
	}

	//See if the desired locale is already loaded.
	var tempLocale = availableFlatLocales ? bestLocale : targetLocale;
	var bundle = dojo._loadedModules[bundlePackage];
	var localizedBundle = null;
	if(bundle){
		if(dojo.config.localizationComplete && bundle._built){return;}
		var jsLoc = tempLocale.replace(/-/g, '_');
		var translationPackage = bundlePackage+"."+jsLoc;
		localizedBundle = dojo._loadedModules[translationPackage];
	}

	if(!localizedBundle){
		bundle = dojo["provide"](bundlePackage);
		var syms = dojo._getModuleSymbols(moduleName);
		var modpath = syms.concat("nls").join("/");
		var parent;

		dojo.i18n._searchLocalePath(tempLocale, availableFlatLocales, function(loc){
			var jsLoc = loc.replace(/-/g, '_');
			var translationPackage = bundlePackage + "." + jsLoc;
			var loaded = false;
			if(!dojo._loadedModules[translationPackage]){
				// Mark loaded whether it's found or not, so that further load attempts will not be made
				dojo["provide"](translationPackage);
				var module = [modpath];
				if(loc != "ROOT"){module.push(loc);}
				module.push(bundleName);
				var filespec = module.join("/") + '.js';
				loaded = dojo._loadPath(filespec, null, function(hash){
					// Use singleton with prototype to point to parent bundle, then mix-in result from loadPath
					var clazz = function(){};
					clazz.prototype = parent;
					bundle[jsLoc] = new clazz();
					for(var j in hash){ bundle[jsLoc][j] = hash[j]; }
				});
			}else{
				loaded = true;
			}
			if(loaded && bundle[jsLoc]){
				parent = bundle[jsLoc];
			}else{
				bundle[jsLoc] = parent;
			}
			
			if(availableFlatLocales){
				//Stop the locale path searching if we know the availableFlatLocales, since
				//the first call to this function will load the only bundle that is needed.
				return true;
			}
		});
	}

	//Save the best locale bundle as the target locale bundle when we know the
	//the available bundles.
	if(availableFlatLocales && targetLocale != bestLocale){
		bundle[targetLocale.replace(/-/g, '_')] = bundle[bestLocale.replace(/-/g, '_')];
	}
};

(function(){
	// If other locales are used, dojo.requireLocalization should load them as
	// well, by default. 
	// 
	// Override dojo.requireLocalization to do load the default bundle, then
	// iterate through the extraLocale list and load those translations as
	// well, unless a particular locale was requested.

	var extra = dojo.config.extraLocale;
	if(extra){
		if(!extra instanceof Array){
			extra = [extra];
		}

		var req = dojo.i18n._requireLocalization;
		dojo.i18n._requireLocalization = function(m, b, locale, availableFlatLocales){
			req(m,b,locale, availableFlatLocales);
			if(locale){return;}
			for(var i=0; i<extra.length; i++){
				req(m,b,extra[i], availableFlatLocales);
			}
		};
	}
})();

dojo.i18n._searchLocalePath = function(/*String*/locale, /*Boolean*/down, /*Function*/searchFunc){
	//	summary:
	//		A helper method to assist in searching for locale-based resources.
	//		Will iterate through the variants of a particular locale, either up
	//		or down, executing a callback function.  For example, "en-us" and
	//		true will try "en-us" followed by "en" and finally "ROOT".

	locale = dojo.i18n.normalizeLocale(locale);

	var elements = locale.split('-');
	var searchlist = [];
	for(var i = elements.length; i > 0; i--){
		searchlist.push(elements.slice(0, i).join('-'));
	}
	searchlist.push(false);
	if(down){searchlist.reverse();}

	for(var j = searchlist.length - 1; j >= 0; j--){
		var loc = searchlist[j] || "ROOT";
		var stop = searchFunc(loc);
		if(stop){ break; }
	}
};

dojo.i18n._preloadLocalizations = function(/*String*/bundlePrefix, /*Array*/localesGenerated){
	//	summary:
	//		Load built, flattened resource bundles, if available for all
	//		locales used in the page. Only called by built layer files.

	function preload(locale){
		locale = dojo.i18n.normalizeLocale(locale);
		dojo.i18n._searchLocalePath(locale, true, function(loc){
			for(var i=0; i<localesGenerated.length;i++){
				if(localesGenerated[i] == loc){
					dojo["require"](bundlePrefix+"_"+loc);
					return true; // Boolean
				}
			}
			return false; // Boolean
		});
	}
	preload();
	var extra = dojo.config.extraLocale||[];
	for(var i=0; i<extra.length; i++){
		preload(extra[i]);
	}
};

}

if(!dojo._hasResource["dijit._Contained"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Contained"] = true;
dojo.provide("dijit._Contained");

dojo.declare("dijit._Contained",
		null,
		{
			// summary:
			//		Mixin for widgets that are children of a container widget
			//
			// example:
			// | 	// make a basic custom widget that knows about it's parents
			// |	dojo.declare("my.customClass",[dijit._Widget,dijit._Contained],{});

			getParent: function(){
				// summary:
				//		Returns the parent widget of this widget, assuming the parent
				//		specifies isContainer
				var parent = dijit.getEnclosingWidget(this.domNode.parentNode);
				return parent && parent.isContainer ? parent : null;
			},

			_getSibling: function(/*String*/ which){
				// summary:
				//      Returns next or previous sibling
				// which:
				//      Either "next" or "previous"
				// tags:
				//      private
				var node = this.domNode;
				do{
					node = node[which+"Sibling"];
				}while(node && node.nodeType != 1);
				return node && dijit.byNode(node);	// dijit._Widget
			},

			getPreviousSibling: function(){
				// summary:
				//		Returns null if this is the first child of the parent,
				//		otherwise returns the next element sibling to the "left".

				return this._getSibling("previous"); // dijit._Widget
			},

			getNextSibling: function(){
				// summary:
				//		Returns null if this is the last child of the parent,
				//		otherwise returns the next element sibling to the "right".

				return this._getSibling("next"); // dijit._Widget
			},

			getIndexInParent: function(){
				// summary:
				//		Returns the index of this widget within its container parent.
				//		It returns -1 if the parent does not exist, or if the parent
				//		is not a dijit._Container

				var p = this.getParent();
				if(!p || !p.getIndexOfChild){
					return -1; // int
				}
				return p.getIndexOfChild(this); // int
			}
		}
	);


}

if(!dojo._hasResource["dijit.layout._LayoutWidget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout._LayoutWidget"] = true;
dojo.provide("dijit.layout._LayoutWidget");





dojo.declare("dijit.layout._LayoutWidget",
	[dijit._Widget, dijit._Container, dijit._Contained],
	{
		// summary:
		//		Base class for a _Container widget which is responsible for laying out its children.
		//		Widgets which mixin this code must define layout() to manage placement and sizing of the children.

		// baseClass: [protected extension] String
		//		This class name is applied to the widget's domNode
		//		and also may be used to generate names for sub nodes,
		//		for example dijitTabContainer-content.
		baseClass: "dijitLayoutContainer",

		// isLayoutContainer: [protected] Boolean
		//		Indicates that this widget is going to call resize() on its
		//		children widgets, setting their size, when they become visible.
		isLayoutContainer: true,

		postCreate: function(){
			dojo.addClass(this.domNode, "dijitContainer");

			this.inherited(arguments);
		},

		startup: function(){
			// summary:
			//		Called after all the widgets have been instantiated and their
			//		dom nodes have been inserted somewhere under dojo.doc.body.
			//
			//		Widgets should override this method to do any initialization
			//		dependent on other widgets existing, and then call
			//		this superclass method to finish things off.
			//
			//		startup() in subclasses shouldn't do anything
			//		size related because the size of the widget hasn't been set yet.

			if(this._started){ return; }

			// Need to call inherited first - so that child widgets get started
			// up correctly
			this.inherited(arguments);

			// If I am a not being controlled by a parent layout widget...
			var parent = this.getParent && this.getParent()
			if(!(parent && parent.isLayoutContainer)){
				// Do recursive sizing and layout of all my descendants
				// (passing in no argument to resize means that it has to glean the size itself)
				this.resize();

				// Since my parent isn't a layout container, and my style *may be* width=height=100%
				// or something similar (either set directly or via a CSS class),
				// monitor when my size changes so that I can re-layout.
				// For browsers where I can't directly monitor when my size changes,
				// monitor when the viewport changes size, which *may* indicate a size change for me.
				this.connect(dojo.isIE ? this.domNode : dojo.global, 'onresize', function(){
					// Using function(){} closure to ensure no arguments to resize.
					this.resize();
				});
			}
		},

		resize: function(changeSize, resultSize){
			// summary:
			//		Call this to resize a widget, or after its size has changed.
			// description:
			//		Change size mode:
			//			When changeSize is specified, changes the marginBox of this widget
			//			and forces it to relayout its contents accordingly.
			//			changeSize may specify height, width, or both.
			//
			//			If resultSize is specified it indicates the size the widget will
			//			become after changeSize has been applied.
			//
			//		Notification mode:
			//			When changeSize is null, indicates that the caller has already changed
			//			the size of the widget, or perhaps it changed because the browser
			//			window was resized.  Tells widget to relayout its contents accordingly.
			//
			//			If resultSize is also specified it indicates the size the widget has
			//			become.
			//
			//		In either mode, this method also:
			//			1. Sets this._borderBox and this._contentBox to the new size of
			//				the widget.  Queries the current domNode size if necessary.
			//			2. Calls layout() to resize contents (and maybe adjust child widgets).
			//
			// changeSize: Object?
			//		Sets the widget to this margin-box size and position.
			//		May include any/all of the following properties:
			//	|	{w: int, h: int, l: int, t: int}
			//
			// resultSize: Object?
			//		The margin-box size of this widget after applying changeSize (if
			//		changeSize is specified).  If caller knows this size and
			//		passes it in, we don't need to query the browser to get the size.
			//	|	{w: int, h: int}

			var node = this.domNode;

			// set margin box size, unless it wasn't specified, in which case use current size
			if(changeSize){
				dojo.marginBox(node, changeSize);

				// set offset of the node
				if(changeSize.t){ node.style.top = changeSize.t + "px"; }
				if(changeSize.l){ node.style.left = changeSize.l + "px"; }
			}

			// If either height or width wasn't specified by the user, then query node for it.
			// But note that setting the margin box and then immediately querying dimensions may return
			// inaccurate results, so try not to depend on it.
			var mb = resultSize || {};
			dojo.mixin(mb, changeSize || {});	// changeSize overrides resultSize
			if( !("h" in mb) || !("w" in mb) ){
				mb = dojo.mixin(dojo.marginBox(node), mb);	// just use dojo.marginBox() to fill in missing values
			}

			// Compute and save the size of my border box and content box
			// (w/out calling dojo.contentBox() since that may fail if size was recently set)
			var cs = dojo.getComputedStyle(node);
			var me = dojo._getMarginExtents(node, cs);
			var be = dojo._getBorderExtents(node, cs);
			var bb = (this._borderBox = {
				w: mb.w - (me.w + be.w),
				h: mb.h - (me.h + be.h)
			});
			var pe = dojo._getPadExtents(node, cs);
			this._contentBox = {
				l: dojo._toPixelValue(node, cs.paddingLeft),
				t: dojo._toPixelValue(node, cs.paddingTop),
				w: bb.w - pe.w,
				h: bb.h - pe.h
			};

			// Callback for widget to adjust size of its children
			this.layout();
		},

		layout: function(){
			// summary:
			//		Widgets override this method to size and position their contents/children.
			//		When this is called this._contentBox is guaranteed to be set (see resize()).
			//
			//		This is called after startup(), and also when the widget's size has been
			//		changed.
			// tags:
			//		protected extension
		},

		_setupChild: function(/*dijit._Widget*/child){
			// summary:
			//		Common setup for initial children and children which are added after startup
			// tags:
			//		protected extension

			dojo.addClass(child.domNode, this.baseClass+"-child");
			if(child.baseClass){
				dojo.addClass(child.domNode, this.baseClass+"-"+child.baseClass);
			}
		},

		addChild: function(/*dijit._Widget*/ child, /*Integer?*/ insertIndex){
			// Overrides _Container.addChild() to call _setupChild()
			this.inherited(arguments);
			if(this._started){
				this._setupChild(child);
			}
		},

		removeChild: function(/*dijit._Widget*/ child){
			// Overrides _Container.removeChild() to remove class added by _setupChild()
			dojo.removeClass(child.domNode, this.baseClass+"-child");
			if(child.baseClass){
				dojo.removeClass(child.domNode, this.baseClass+"-"+child.baseClass);
			}
			this.inherited(arguments);
		}
	}
);

dijit.layout.marginBox2contentBox = function(/*DomNode*/ node, /*Object*/ mb){
	// summary:
	//		Given the margin-box size of a node, return its content box size.
	//		Functions like dojo.contentBox() but is more reliable since it doesn't have
	//		to wait for the browser to compute sizes.
	var cs = dojo.getComputedStyle(node);
	var me = dojo._getMarginExtents(node, cs);
	var pb = dojo._getPadBorderExtents(node, cs);
	return {
		l: dojo._toPixelValue(node, cs.paddingLeft),
		t: dojo._toPixelValue(node, cs.paddingTop),
		w: mb.w - (me.w + pb.w),
		h: mb.h - (me.h + pb.h)
	};
};

(function(){
	var capitalize = function(word){
		return word.substring(0,1).toUpperCase() + word.substring(1);
	};

	var size = function(widget, dim){
		// size the child
		widget.resize ? widget.resize(dim) : dojo.marginBox(widget.domNode, dim);

		// record child's size, but favor our own numbers when we have them.
		// the browser lies sometimes
		dojo.mixin(widget, dojo.marginBox(widget.domNode));
		dojo.mixin(widget, dim);
	};

	dijit.layout.layoutChildren = function(/*DomNode*/ container, /*Object*/ dim, /*Object[]*/ children){
		// summary
		//		Layout a bunch of child dom nodes within a parent dom node
		// container:
		//		parent node
		// dim:
		//		{l, t, w, h} object specifying dimensions of container into which to place children
		// children:
		//		an array like [ {domNode: foo, layoutAlign: "bottom" }, {domNode: bar, layoutAlign: "client"} ]

		// copy dim because we are going to modify it
		dim = dojo.mixin({}, dim);

		dojo.addClass(container, "dijitLayoutContainer");

		// Move "client" elements to the end of the array for layout.  a11y dictates that the author
		// needs to be able to put them in the document in tab-order, but this algorithm requires that
		// client be last.
		children = dojo.filter(children, function(item){ return item.layoutAlign != "client"; })
			.concat(dojo.filter(children, function(item){ return item.layoutAlign == "client"; }));

		// set positions/sizes
		dojo.forEach(children, function(child){
			var elm = child.domNode,
				pos = child.layoutAlign;

			// set elem to upper left corner of unused space; may move it later
			var elmStyle = elm.style;
			elmStyle.left = dim.l+"px";
			elmStyle.top = dim.t+"px";
			elmStyle.bottom = elmStyle.right = "auto";

			dojo.addClass(elm, "dijitAlign" + capitalize(pos));

			// set size && adjust record of remaining space.
			// note that setting the width of a <div> may affect its height.
			if(pos == "top" || pos == "bottom"){
				size(child, { w: dim.w });
				dim.h -= child.h;
				if(pos == "top"){
					dim.t += child.h;
				}else{
					elmStyle.top = dim.t + dim.h + "px";
				}
			}else if(pos == "left" || pos == "right"){
				size(child, { h: dim.h });
				dim.w -= child.w;
				if(pos == "left"){
					dim.l += child.w;
				}else{
					elmStyle.left = dim.l + dim.w + "px";
				}
			}else if(pos == "client"){
				size(child, dim);
			}
		});
	};

})();

}

if(!dojo._hasResource["dijit.Editor"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Editor"] = true;
dojo.provide("dijit.Editor");













dojo.declare(
	"dijit.Editor",
	dijit._editor.RichText,
	{
		// summary:
		//		A rich text Editing widget
		//
		// description:
		//		This widget provides basic WYSIWYG editing features, based on the browser's
		//		underlying rich text editing capability, accompanied by a toolbar (`dijit.Toolbar`).
		//		A plugin model is available to extend the editor's capabilities as well as the
		//		the options available in the toolbar.  Content generation may vary across
		//		browsers, and clipboard operations may have different results, to name
		//		a few limitations.  Note: this widget should not be used with the HTML
		//		&lt;TEXTAREA&gt; tag -- see dijit._editor.RichText for details.

		// plugins: Object[]
		//		A list of plugin names (as strings) or instances (as objects)
		//		for this widget.
		//
		//		When declared in markup, it might look like:
		//	|	plugins="['bold',{name:'dijit._editor.plugins.FontChoice', command:'fontName', generic:true}]"
		plugins: null,

		// extraPlugins: Object[]
		//		A list of extra plugin names which will be appended to plugins array
		extraPlugins: null,

		constructor: function(){
			// summary:
			//		Runs on widget initialization to setup arrays etc.
			// tags:
			//		private

			if(!dojo.isArray(this.plugins)){
				this.plugins=["undo","redo","|","cut","copy","paste","|","bold","italic","underline","strikethrough","|",
				"insertOrderedList","insertUnorderedList","indent","outdent","|","justifyLeft","justifyRight","justifyCenter","justifyFull",
				"dijit._editor.plugins.EnterKeyHandling" /*, "createLink"*/];
			}

			this._plugins=[];
			this._editInterval = this.editActionInterval * 1000;

			//IE will always lose focus when other element gets focus, while for FF and safari,
			//when no iframe is used, focus will be lost whenever another element gets focus.
			//For IE, we can connect to onBeforeDeactivate, which will be called right before
			//the focus is lost, so we can obtain the selected range. For other browsers,
			//no equivelent of onBeforeDeactivate, so we need to do two things to make sure
			//selection is properly saved before focus is lost: 1) when user clicks another
			//element in the page, in which case we listen to mousedown on the entire page and
			//see whether user clicks out of a focus editor, if so, save selection (focus will
			//only lost after onmousedown event is fired, so we can obtain correct caret pos.)
			//2) when user tabs away from the editor, which is handled in onKeyDown below.
			if(dojo.isIE){
				this.events.push("onBeforeDeactivate");
				this.events.push("onBeforeActivate");
			}
		},

		postCreate: function(){
			//for custom undo/redo, if enabled.
			this._steps=this._steps.slice(0);
			this._undoedSteps=this._undoedSteps.slice(0);

			if(dojo.isArray(this.extraPlugins)){
				this.plugins=this.plugins.concat(this.extraPlugins);
			}

			// Set up a deferred so that the value isn't applied to the editor 
			// until all the plugins load, needed to avoid timing condition
			// reported in #10537.
			this.setValueDeferred = new dojo.Deferred();

			this.inherited(arguments);

			this.commands = dojo.i18n.getLocalization("dijit._editor", "commands", this.lang);

			if(!this.toolbar){
				// if we haven't been assigned a toolbar, create one
				this.toolbar = new dijit.Toolbar({
					dir: this.dir,
					lang: this.lang
				});
				this.header.appendChild(this.toolbar.domNode);
			}

			dojo.forEach(this.plugins, this.addPlugin, this);

			// Okay, denote the value can now be set.
			this.setValueDeferred.callback(true);

			dojo.addClass(this.iframe.parentNode, "dijitEditorIFrameContainer");
			dojo.addClass(this.iframe, "dijitEditorIFrame");
			dojo.attr(this.iframe, "allowTransparency", true);

			if(dojo.isWebKit){
				// Disable selecting the entire editor by inadvertant double-clicks.
				// on buttons, title bar, etc.  Otherwise clicking too fast on
				// a button such as undo/redo selects the entire editor.
				dojo.style(this.domNode, "KhtmlUserSelect", "none");
			}
			this.toolbar.startup();
			this.onNormalizedDisplayChanged(); //update toolbar button status
		},
		destroy: function(){
			dojo.forEach(this._plugins, function(p){
				if(p && p.destroy){
					p.destroy();
				}
			});
			this._plugins=[];
			this.toolbar.destroyRecursive();
			delete this.toolbar;
			this.inherited(arguments);
		},
		addPlugin: function(/*String||Object*/plugin, /*Integer?*/index){
			// summary:
			//		takes a plugin name as a string or a plugin instance and
			//		adds it to the toolbar and associates it with this editor
			//		instance. The resulting plugin is added to the Editor's
			//		plugins array. If index is passed, it's placed in the plugins
			//		array at that index. No big magic, but a nice helper for
			//		passing in plugin names via markup.
			//
			// plugin: String, args object or plugin instance
			//
			// args:
			//		This object will be passed to the plugin constructor
			//
			// index: Integer
			//		Used when creating an instance from
			//		something already in this.plugins. Ensures that the new
			//		instance is assigned to this.plugins at that index.
			var args=dojo.isString(plugin)?{name:plugin}:plugin;
			if(!args.setEditor){
				var o={"args":args,"plugin":null,"editor":this};
				dojo.publish(dijit._scopeName + ".Editor.getPlugin",[o]);
				if(!o.plugin){
					var pc = dojo.getObject(args.name);
					if(pc){
						o.plugin=new pc(args);
					}
				}
				if(!o.plugin){
					console.warn('Cannot find plugin',plugin);
					return;
				}
				plugin=o.plugin;
			}
			if(arguments.length > 1){
				this._plugins[index] = plugin;
			}else{
				this._plugins.push(plugin);
			}
			plugin.setEditor(this);
			if(dojo.isFunction(plugin.setToolbar)){
				plugin.setToolbar(this.toolbar);
			}
		},
		//the following 3 functions are required to make the editor play nice under a layout widget, see #4070
		startup: function(){
			// summary:
			//		Exists to make Editor work as a child of a layout widget.
			//		Developers don't need to call this method.
			// tags:
			//		protected
			//console.log('startup',arguments);
		},
		resize: function(size){
			// summary:
			//		Resize the editor to the specified size, see `dijit.layout._LayoutWidget.resize`
			if(size){
				// we've been given a height/width for the entire editor (toolbar + contents), calls layout()
				// to split the allocated size between the toolbar and the contents
				dijit.layout._LayoutWidget.prototype.resize.apply(this, arguments);
			}
			/*
			else{
				// do nothing, the editor is already laid out correctly.   The user has probably specified
				// the height parameter, which was used to set a size on the iframe
			}
			*/
		},
		layout: function(){
			// summary:
			//		Called from `dijit.layout._LayoutWidget.resize`.  This shouldn't be called directly
			// tags:
			//		protected

			// Converts the iframe (or rather the <div> surrounding it) to take all the available space
			// except what's needed for the header (toolbars) and footer (breadcrumbs, etc).
			// A class was added to the iframe container and some themes style it, so we have to
			// calc off the added margins and padding too. See tracker: #10662
			var areaHeight = (this._contentBox.h - 
				(this.getHeaderHeight() + this.getFooterHeight() + 
				 dojo._getPadBorderExtents(this.iframe.parentNode).h +
				 dojo._getMarginExtents(this.iframe.parentNode).h));
			this.editingArea.style.height = areaHeight + "px";
			if(this.iframe){
				this.iframe.style.height="100%";
			}
			this._layoutMode = true;
		},
		_onIEMouseDown: function(/*Event*/ e){
			// summary:
			//		IE only to prevent 2 clicks to focus
			// tags:
			//		private
			var outsideClientArea;
			// IE 8's componentFromPoint is broken, which is a shame since it
			// was smaller code, but oh well.  We have to do this brute force
			// to detect if the click was scroller or not.
			var b = this.document.body;
			var clientWidth = b.clientWidth;
			var clientHeight = b.clientHeight;
			var clientLeft = b.clientLeft;
			var offsetWidth = b.offsetWidth;
			var offsetHeight = b.offsetHeight;
			var offsetLeft = b.offsetLeft;

			//Check for vertical scroller click.
			bodyDir = b.dir?b.dir.toLowerCase():""
			if(bodyDir != "rtl"){
				if(clientWidth < offsetWidth && e.x > clientWidth && e.x < offsetWidth){ 
					// Check the click was between width and offset width, if so, scroller
					outsideClientArea = true;
				}
			}else{
				// RTL mode, we have to go by the left offsets.
				if(e.x < clientLeft && e.x > offsetLeft){
					// Check the click was between width and offset width, if so, scroller
					outsideClientArea = true;
				}
			}
			if(!outsideClientArea){
				// Okay, might be horiz scroller, check that.
				if(clientHeight < offsetHeight && e.y > clientHeight && e.y < offsetHeight){
					// Horizontal scroller.
					outsideClientArea = true;
				}
			}
			if(!outsideClientArea){
				delete this._cursorToStart; // Remove the force to cursor to start position. 
				delete this._savedSelection; // new mouse position overrides old selection
				if(e.target.tagName == "BODY"){
					setTimeout(dojo.hitch(this, "placeCursorAtEnd"), 0);
				}
				this.inherited(arguments);
			}
		},
		onBeforeActivate: function(e){
			this._restoreSelection();
		},
		onBeforeDeactivate: function(e){
			// summary:
			//		Called on IE right before focus is lost.   Saves the selected range.
			// tags:
			//		private
			if(this.customUndo){
				this.endEditing(true);
			}
			//in IE, the selection will be lost when other elements get focus,
			//let's save focus before the editor is deactivated
			if(e.target.tagName != "BODY"){
				this._saveSelection();
			}
			//console.log('onBeforeDeactivate',this);
		},

		/* beginning of custom undo/redo support */

		// customUndo: Boolean
		//		Whether we shall use custom undo/redo support instead of the native
		//		browser support. By default, we only enable customUndo for IE, as it
		//		has broken native undo/redo support. Note: the implementation does
		//		support other browsers which have W3C DOM2 Range API implemented.
		//		It was also enabled on WebKit, to fix undo/redo enablement. (#9613)
		customUndo: dojo.isIE || dojo.isWebKit,

		// editActionInterval: Integer
		//		When using customUndo, not every keystroke will be saved as a step.
		//		Instead typing (including delete) will be grouped together: after
		//		a user stops typing for editActionInterval seconds, a step will be
		//		saved; if a user resume typing within editActionInterval seconds,
		//		the timeout will be restarted. By default, editActionInterval is 3
		//		seconds.
		editActionInterval: 3,

		beginEditing: function(cmd){
			// summary:
			//		Called to note that the user has started typing alphanumeric characters, if it's not already noted.
			//		Deals with saving undo; see editActionInterval parameter.
			// tags:
			//		private
			if(!this._inEditing){
				this._inEditing=true;
				this._beginEditing(cmd);
			}
			if(this.editActionInterval>0){
				if(this._editTimer){
					clearTimeout(this._editTimer);
				}
				this._editTimer = setTimeout(dojo.hitch(this, this.endEditing), this._editInterval);
			}
		},
		_steps:[],
		_undoedSteps:[],
		execCommand: function(cmd){
			// summary:
			//		Main handler for executing any commands to the editor, like paste, bold, etc.
			//      Called by plugins, but not meant to be called by end users.
			// tags:
			//		protected
			if(this.customUndo && (cmd == 'undo' || cmd == 'redo')){
				return this[cmd]();
			}else{
				if(this.customUndo){
					this.endEditing();
					this._beginEditing();
				}
				var r;
				try{
					r = this.inherited('execCommand', arguments);
					if(dojo.isWebKit && cmd == 'paste' && !r){ //see #4598: safari does not support invoking paste from js
						throw { code: 1011 }; // throw an object like Mozilla's error
					}
				}catch(e){
					//TODO: when else might we get an exception?  Do we need the Mozilla test below?
					if(e.code == 1011 /* Mozilla: service denied */ && /copy|cut|paste/.test(cmd)){
						// Warn user of platform limitation.  Cannot programmatically access clipboard. See ticket #4136
						var sub = dojo.string.substitute,
							accel = {cut:'X', copy:'C', paste:'V'};
						alert(sub(this.commands.systemShortcut,
							[this.commands[cmd], sub(this.commands[dojo.isMac ? 'appleKey' : 'ctrlKey'], [accel[cmd]])]));
					}
					r = false;
				}
				if(this.customUndo){
					this._endEditing();
				}
				return r;
			}
		},
		queryCommandEnabled: function(cmd){
			// summary:
			//		Returns true if specified editor command is enabled.
			//      Used by the plugins to know when to highlight/not highlight buttons.
			// tags:
			//		protected
			if(this.customUndo && (cmd == 'undo' || cmd == 'redo')){
				return cmd == 'undo' ? (this._steps.length > 1) : (this._undoedSteps.length > 0);
			}else{
				return this.inherited('queryCommandEnabled',arguments);
			}
		},
		_moveToBookmark: function(b){
			// summary:
			//		Selects the text specified in bookmark b
			// tags:
			//		private
			var bookmark = b.mark;
			var mark = b.mark;
			var col = b.isCollapsed;
			var r, sNode, eNode, sel;
			if(mark){
				if(dojo.isIE){
					if(dojo.isArray(mark)){
						//IE CONTROL, have to use the native bookmark.
						bookmark = [];
						dojo.forEach(mark,function(n){
							bookmark.push(dijit.range.getNode(n,this.editNode));
						},this);
						dojo.withGlobal(this.window,'moveToBookmark',dijit,[{mark: bookmark, isCollapsed: col}]);
					}else{
						if(mark.startContainer && mark.endContainer){
							// Use the pseudo WC3 range API.  This works better for positions
							// than the IE native bookmark code.
							sel = dijit.range.getSelection(this.window);
							if(sel && sel.removeAllRanges){
								sel.removeAllRanges();
								r = dijit.range.create(this.window);
								sNode = dijit.range.getNode(mark.startContainer,this.editNode);
								eNode = dijit.range.getNode(mark.endContainer,this.editNode);
								if(sNode && eNode){
									// Okay, we believe we found the position, so add it into the selection
									// There are cases where it may not be found, particularly in undo/redo, when
									// IE changes the underlying DOM on us (wraps text in a <p> tag or similar.
									// So, in those cases, don't bother restoring selection.
									r.setStart(sNode,mark.startOffset);
									r.setEnd(eNode,mark.endOffset);
									sel.addRange(r);
								}
							}
						}
					}
				}else{//w3c range
					sel = dijit.range.getSelection(this.window);
					if(sel && sel.removeAllRanges){
						sel.removeAllRanges();
						r = dijit.range.create(this.window);
						sNode = dijit.range.getNode(mark.startContainer,this.editNode);
						eNode = dijit.range.getNode(mark.endContainer,this.editNode);
						if(sNode && eNode){
							// Okay, we believe we found the position, so add it into the selection
							// There are cases where it may not be found, particularly in undo/redo, when
							// formatting as been done and so on, so don't restore selection then.
							r.setStart(sNode,mark.startOffset);
							r.setEnd(eNode,mark.endOffset);
							sel.addRange(r);
						}
					}
				}
			}
		},
		_changeToStep: function(from, to){
			// summary:
			//		Reverts editor to "to" setting, from the undo stack.
			// tags:
			//		private
			this.setValue(to.text);
			var b=to.bookmark;
			if(!b){ return; }
			this._moveToBookmark(b);
		},
		undo: function(){
			// summary:
			//		Handler for editor undo (ex: ctrl-z) operation
			// tags:
			//		private
			//console.log('undo');
			var ret = false;
			if(!this._undoRedoActive){
				this._undoRedoActive = true;
				this.endEditing(true);
				var s=this._steps.pop();
				if(s && this._steps.length>0){
					this.focus();
					this._changeToStep(s,this._steps[this._steps.length-1]);
					this._undoedSteps.push(s);
					this.onDisplayChanged();
					delete this._undoRedoActive;
					ret = true;
				}
				delete this._undoRedoActive;
			}	
			return ret;
		},
		redo: function(){
			// summary:
			//		Handler for editor redo (ex: ctrl-y) operation
			// tags:
			//		private
			//console.log('redo');
			var ret = false;
			if(!this._undoRedoActive){
				this._undoRedoActive = true;
				this.endEditing(true);
				var s=this._undoedSteps.pop();
				if(s && this._steps.length>0){
					this.focus();
					this._changeToStep(this._steps[this._steps.length-1],s);
					this._steps.push(s);
					this.onDisplayChanged();
					ret = true;
				}
				delete this._undoRedoActive;
			}
			return ret;
		},
		endEditing: function(ignore_caret){
			// summary:
			//		Called to note that the user has stopped typing alphanumeric characters, if it's not already noted.
			//		Deals with saving undo; see editActionInterval parameter.
			// tags:
			//		private
			if(this._editTimer){
				clearTimeout(this._editTimer);
			}
			if(this._inEditing){
				this._endEditing(ignore_caret);
				this._inEditing=false;
			}
		},

		_getBookmark: function(){
			// summary:
			//		Get the currently selected text
			// tags:
			//		protected
			var b=dojo.withGlobal(this.window,dijit.getBookmark);
			var tmp=[];
			if(b && b.mark){
				var mark = b.mark;
				if(dojo.isIE){
					// Try to use the pseudo range API on IE for better accuracy.
					var sel = dijit.range.getSelection(this.window);
					if(!dojo.isArray(mark)){
						if(sel){
							var range;
							if(sel.rangeCount){
								range = sel.getRangeAt(0);
							}
							if(range){
								b.mark = range.cloneRange();
							}else{
								b.mark = dojo.withGlobal(this.window,dijit.getBookmark);
							}
						}
					}else{
						// Control ranges (img, table, etc), handle differently.
						dojo.forEach(b.mark,function(n){
							tmp.push(dijit.range.getIndex(n,this.editNode).o);
						},this);
						b.mark = tmp;
					}
				}
				try{
					if(b.mark && b.mark.startContainer){
						tmp=dijit.range.getIndex(b.mark.startContainer,this.editNode).o;
						b.mark={startContainer:tmp,
							startOffset:b.mark.startOffset,
							endContainer:b.mark.endContainer===b.mark.startContainer?tmp:dijit.range.getIndex(b.mark.endContainer,this.editNode).o,
							endOffset:b.mark.endOffset};
					}
				}catch(e){
					b.mark = null;
				}
			}
			return b;
		},
		_beginEditing: function(cmd){
			// summary:
			//		Called when the user starts typing alphanumeric characters.
			//		Deals with saving undo; see editActionInterval parameter.
			// tags:
			//		private
			if(this._steps.length === 0){
				// You want to use the editor content without post filtering
				// to make sure selection restores right for the 'initial' state.
				// and undo is called.  So not using this.savedContent, as it was 'processed'
				// and the line-up for selections may have been altered.
				this._steps.push({'text':dijit._editor.getChildrenHtml(this.editNode),'bookmark':this._getBookmark()});
			}
		},
		_endEditing: function(ignore_caret){
			// summary:
			//		Called when the user stops typing alphanumeric characters.
			//		Deals with saving undo; see editActionInterval parameter.
			// tags:
			//		private
			// Avoid filtering to make sure selections restore.
			var v = dijit._editor.getChildrenHtml(this.editNode);

			this._undoedSteps=[];//clear undoed steps
			this._steps.push({text: v, bookmark: this._getBookmark()});
		},
		onKeyDown: function(e){
			// summary:
			//		Handler for onkeydown event.
			// tags:
			//		private

			//We need to save selection if the user TAB away from this editor
			//no need to call _saveSelection for IE, as that will be taken care of in onBeforeDeactivate
			if(!dojo.isIE && !this.iframe && e.keyCode == dojo.keys.TAB && !this.tabIndent){
				this._saveSelection();
			}
			if(!this.customUndo){
				this.inherited(arguments);
				return;
			}
			var k = e.keyCode, ks = dojo.keys;
			if(e.ctrlKey && !e.altKey){//undo and redo only if the special right Alt + z/y are not pressed #5892
				if(k == 90 || k == 122){ //z
					dojo.stopEvent(e);
					this.undo();
					return;
				}else if(k == 89 || k == 121){ //y
					dojo.stopEvent(e);
					this.redo();
					return;
				}
			}
			this.inherited(arguments);

			switch(k){
					case ks.ENTER:
					case ks.BACKSPACE:
					case ks.DELETE:
						this.beginEditing();
						break;
					case 88: //x
					case 86: //v
						if(e.ctrlKey && !e.altKey && !e.metaKey){
							this.endEditing();//end current typing step if any
							if(e.keyCode == 88){
								this.beginEditing('cut');
								//use timeout to trigger after the cut is complete
								setTimeout(dojo.hitch(this, this.endEditing), 1);
							}else{
								this.beginEditing('paste');
								//use timeout to trigger after the paste is complete
								setTimeout(dojo.hitch(this, this.endEditing), 1);
							}
							break;
						}
						//pass through
					default:
						if(!e.ctrlKey && !e.altKey && !e.metaKey && (e.keyCode<dojo.keys.F1 || e.keyCode>dojo.keys.F15)){
							this.beginEditing();
							break;
						}
						//pass through
					case ks.ALT:
						this.endEditing();
						break;
					case ks.UP_ARROW:
					case ks.DOWN_ARROW:
					case ks.LEFT_ARROW:
					case ks.RIGHT_ARROW:
					case ks.HOME:
					case ks.END:
					case ks.PAGE_UP:
					case ks.PAGE_DOWN:
						this.endEditing(true);
						break;
					//maybe ctrl+backspace/delete, so don't endEditing when ctrl is pressed
					case ks.CTRL:
					case ks.SHIFT:
					case ks.TAB:
						break;
				}
		},
		_onBlur: function(){
			// summary:
			//		Called from focus manager when focus has moved away from this editor
			// tags:
			//		protected

			//this._saveSelection();
			this.inherited('_onBlur',arguments);
			this.endEditing(true);
		},
		_saveSelection: function(){
			// summary:
			//		Save the currently selected text in _savedSelection attribute
			// tags:
			//		private
			this._savedSelection=this._getBookmark();
			//console.log('save selection',this._savedSelection,this);
		},
		_restoreSelection: function(){
			// summary:
			//		Re-select the text specified in _savedSelection attribute;
			//		see _saveSelection().
			// tags:
			//		private
			if(this._savedSelection){
				// Clear off cursor to start, we're deliberately going to a selection.
				delete this._cursorToStart;
				// only restore the selection if the current range is collapsed
				// if not collapsed, then it means the editor does not lose
				// selection and there is no need to restore it
				if(dojo.withGlobal(this.window,'isCollapsed',dijit)){
					this._moveToBookmark(this._savedSelection);
				}
				delete this._savedSelection;
			}
		},

		onClick: function(){
			// summary:
			//		Handler for when editor is clicked
			// tags:
			//		protected
			this.endEditing(true);
			this.inherited(arguments);
		},

		_setDisabledAttr: function(/*Boolean*/ value){
			if(!this.disabled && value){
				// Disable editor: disable all enabled buttons and remember that list
				this._buttonEnabledPlugins = dojo.filter(this._plugins, function(p){
					if (p && p.button && !p.button.get("disabled")) {
						p.button.set("disabled", true);
						return true;
					}
					return false;
				});
			}else if(this.disabled && !value){
				// Enable editor: we only want to enable the buttons that should be
				// enabled (for example, the outdent button shouldn't be enabled if the current
				// text can't be outdented).
				dojo.forEach(this._buttonEnabledPlugins, function(p){
					p.button.attr("disabled", false);
					p.updateState && p.updateState();	// just in case something changed, like caret position
				});
			}
			
			this.inherited(arguments);
		},
		
		_setStateClass: function(){
			this.inherited(arguments);
			
			// Let theme set the editor's text color based on editor enabled/disabled state.
			// We need to jump through hoops because the main document (where the theme CSS is)
			// is separate from the iframe's document.
			if(this.document && this.document.body){
				dojo.style(this.document.body, "color", dojo.style(this.iframe, "color"));
			}
		}
	}
);

// Register the "default plugins", ie, the built-in editor commands
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin",null,function(o){
	if(o.plugin){ return; }
	var args = o.args, p;
	var _p = dijit._editor._Plugin;
	var name = args.name;
	switch(name){
		case "undo": case "redo": case "cut": case "copy": case "paste": case "insertOrderedList":
		case "insertUnorderedList": case "indent": case "outdent": case "justifyCenter":
		case "justifyFull": case "justifyLeft": case "justifyRight": case "delete":
		case "selectAll": case "removeFormat": case "unlink":
		case "insertHorizontalRule":
			p = new _p({ command: name });
			break;

		case "bold": case "italic": case "underline": case "strikethrough":
		case "subscript": case "superscript":
			p = new _p({ buttonClass: dijit.form.ToggleButton, command: name });
			break;
		case "|":
			p = new _p({ button: new dijit.ToolbarSeparator(), setEditor: function(editor) {this.editor = editor;} });
	}
//	console.log('name',name,p);
	o.plugin=p;
});

}

if(!dojo._hasResource["editor.layer"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["editor.layer"] = true;
dojo.provide("editor.layer");



}


dojo.i18n._preloadLocalizations("editor.nls.layer", ["ROOT","ar","ca","cs","da","de","de-de","el","en","en-gb","en-us","es","es-es","fi","fi-fi","fr","fr-fr","he","he-il","hu","it","it-it","ja","ja-jp","ko","ko-kr","nb","nl","nl-nl","pl","pt","pt-br","pt-pt","ru","sk","sl","sv","th","tr","xx","zh","zh-cn","zh-tw"]);
