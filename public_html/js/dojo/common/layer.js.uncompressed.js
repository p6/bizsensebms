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

if(!dojo._hasResource["dojo.data.util.filter"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.filter"] = true;
dojo.provide("dojo.data.util.filter");

dojo.data.util.filter.patternToRegExp = function(/*String*/pattern, /*boolean?*/ ignoreCase){
	//	summary:  
	//		Helper function to convert a simple pattern to a regular expression for matching.
	//	description:
	//		Returns a regular expression object that conforms to the defined conversion rules.
	//		For example:  
	//			ca*   -> /^ca.*$/
	//			*ca*  -> /^.*ca.*$/
	//			*c\*a*  -> /^.*c\*a.*$/
	//			*c\*a?*  -> /^.*c\*a..*$/
	//			and so on.
	//
	//	pattern: string
	//		A simple matching pattern to convert that follows basic rules:
	//			* Means match anything, so ca* means match anything starting with ca
	//			? Means match single character.  So, b?b will match to bob and bab, and so on.
	//      	\ is an escape character.  So for example, \* means do not treat * as a match, but literal character *.
	//				To use a \ as a character in the string, it must be escaped.  So in the pattern it should be 
	//				represented by \\ to be treated as an ordinary \ character instead of an escape.
	//
	//	ignoreCase:
	//		An optional flag to indicate if the pattern matching should be treated as case-sensitive or not when comparing
	//		By default, it is assumed case sensitive.

	var rxp = "^";
	var c = null;
	for(var i = 0; i < pattern.length; i++){
		c = pattern.charAt(i);
		switch(c){
			case '\\':
				rxp += c;
				i++;
				rxp += pattern.charAt(i);
				break;
			case '*':
				rxp += ".*"; break;
			case '?':
				rxp += "."; break;
			case '$':
			case '^':
			case '/':
			case '+':
			case '.':
			case '|':
			case '(':
			case ')':
			case '{':
			case '}':
			case '[':
			case ']':
				rxp += "\\"; //fallthrough
			default:
				rxp += c;
		}
	}
	rxp += "$";
	if(ignoreCase){
		return new RegExp(rxp,"mi"); //RegExp
	}else{
		return new RegExp(rxp,"m"); //RegExp
	}
	
};

}

if(!dojo._hasResource["dojo.data.util.sorter"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.sorter"] = true;
dojo.provide("dojo.data.util.sorter");

dojo.data.util.sorter.basicComparator = function(	/*anything*/ a, 
													/*anything*/ b){
	//	summary:  
	//		Basic comparision function that compares if an item is greater or less than another item
	//	description:  
	//		returns 1 if a > b, -1 if a < b, 0 if equal.
	//		'null' values (null, undefined) are treated as larger values so that they're pushed to the end of the list.
	//		And compared to each other, null is equivalent to undefined.
	
	//null is a problematic compare, so if null, we set to undefined.
	//Makes the check logic simple, compact, and consistent
	//And (null == undefined) === true, so the check later against null
	//works for undefined and is less bytes.
	var r = -1;
	if(a === null){
		a = undefined;
	}
	if(b === null){
		b = undefined;
	}
	if(a == b){
		r = 0; 
	}else if(a > b || a == null){
		r = 1; 
	}
	return r; //int {-1,0,1}
};

dojo.data.util.sorter.createSortFunction = function(	/* attributes array */sortSpec,
														/*dojo.data.core.Read*/ store){
	//	summary:  
	//		Helper function to generate the sorting function based off the list of sort attributes.
	//	description:  
	//		The sort function creation will look for a property on the store called 'comparatorMap'.  If it exists
	//		it will look in the mapping for comparisons function for the attributes.  If one is found, it will
	//		use it instead of the basic comparator, which is typically used for strings, ints, booleans, and dates.
	//		Returns the sorting function for this particular list of attributes and sorting directions.
	//
	//	sortSpec: array
	//		A JS object that array that defines out what attribute names to sort on and whether it should be descenting or asending.
	//		The objects should be formatted as follows:
	//		{
	//			attribute: "attributeName-string" || attribute,
	//			descending: true|false;   // Default is false.
	//		}
	//	store: object
	//		The datastore object to look up item values from.
	//
	var sortFunctions=[];

	function createSortFunction(attr, dir, comp, s){
		//Passing in comp and s (comparator and store), makes this
		//function much faster.
		return function(itemA, itemB){
			var a = s.getValue(itemA, attr);
			var b = s.getValue(itemB, attr);
			return dir * comp(a,b); //int
		};
	}
	var sortAttribute;
	var map = store.comparatorMap;
	var bc = dojo.data.util.sorter.basicComparator;
	for(var i = 0; i < sortSpec.length; i++){
		sortAttribute = sortSpec[i];
		var attr = sortAttribute.attribute;
		if(attr){
			var dir = (sortAttribute.descending) ? -1 : 1;
			var comp = bc;
			if(map){
				if(typeof attr !== "string" && ("toString" in attr)){
					 attr = attr.toString();
				}
				comp = map[attr] || bc;
			}
			sortFunctions.push(createSortFunction(attr, 
				dir, comp, store));
		}
	}
	return function(rowA, rowB){
		var i=0;
		while(i < sortFunctions.length){
			var ret = sortFunctions[i++](rowA, rowB);
			if(ret !== 0){
				return ret;//int
			}
		}
		return 0; //int  
	}; // Function
};

}

if(!dojo._hasResource["dojo.data.util.simpleFetch"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.simpleFetch"] = true;
dojo.provide("dojo.data.util.simpleFetch");


dojo.data.util.simpleFetch.fetch = function(/* Object? */ request){
	//	summary:
	//		The simpleFetch mixin is designed to serve as a set of function(s) that can
	//		be mixed into other datastore implementations to accelerate their development.  
	//		The simpleFetch mixin should work well for any datastore that can respond to a _fetchItems() 
	//		call by returning an array of all the found items that matched the query.  The simpleFetch mixin
	//		is not designed to work for datastores that respond to a fetch() call by incrementally
	//		loading items, or sequentially loading partial batches of the result
	//		set.  For datastores that mixin simpleFetch, simpleFetch 
	//		implements a fetch method that automatically handles eight of the fetch()
	//		arguments -- onBegin, onItem, onComplete, onError, start, count, sort and scope
	//		The class mixing in simpleFetch should not implement fetch(),
	//		but should instead implement a _fetchItems() method.  The _fetchItems() 
	//		method takes three arguments, the keywordArgs object that was passed 
	//		to fetch(), a callback function to be called when the result array is
	//		available, and an error callback to be called if something goes wrong.
	//		The _fetchItems() method should ignore any keywordArgs parameters for
	//		start, count, onBegin, onItem, onComplete, onError, sort, and scope.  
	//		The _fetchItems() method needs to correctly handle any other keywordArgs
	//		parameters, including the query parameter and any optional parameters 
	//		(such as includeChildren).  The _fetchItems() method should create an array of 
	//		result items and pass it to the fetchHandler along with the original request object 
	//		-- or, the _fetchItems() method may, if it wants to, create an new request object 
	//		with other specifics about the request that are specific to the datastore and pass 
	//		that as the request object to the handler.
	//
	//		For more information on this specific function, see dojo.data.api.Read.fetch()
	request = request || {};
	if(!request.store){
		request.store = this;
	}
	var self = this;

	var _errorHandler = function(errorData, requestObject){
		if(requestObject.onError){
			var scope = requestObject.scope || dojo.global;
			requestObject.onError.call(scope, errorData, requestObject);
		}
	};

	var _fetchHandler = function(items, requestObject){
		var oldAbortFunction = requestObject.abort || null;
		var aborted = false;

		var startIndex = requestObject.start?requestObject.start:0;
		var endIndex = (requestObject.count && (requestObject.count !== Infinity))?(startIndex + requestObject.count):items.length;

		requestObject.abort = function(){
			aborted = true;
			if(oldAbortFunction){
				oldAbortFunction.call(requestObject);
			}
		};

		var scope = requestObject.scope || dojo.global;
		if(!requestObject.store){
			requestObject.store = self;
		}
		if(requestObject.onBegin){
			requestObject.onBegin.call(scope, items.length, requestObject);
		}
		if(requestObject.sort){
			items.sort(dojo.data.util.sorter.createSortFunction(requestObject.sort, self));
		}
		if(requestObject.onItem){
			for(var i = startIndex; (i < items.length) && (i < endIndex); ++i){
				var item = items[i];
				if(!aborted){
					requestObject.onItem.call(scope, item, requestObject);
				}
			}
		}
		if(requestObject.onComplete && !aborted){
			var subset = null;
			if(!requestObject.onItem){
				subset = items.slice(startIndex, endIndex);
			}
			requestObject.onComplete.call(scope, subset, requestObject);
		}
	};
	this._fetchItems(request, _fetchHandler, _errorHandler);
	return request;	// Object
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

if(!dojo._hasResource["dojo.data.ItemFileReadStore"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.ItemFileReadStore"] = true;
dojo.provide("dojo.data.ItemFileReadStore");





dojo.declare("dojo.data.ItemFileReadStore", null,{
	//	summary:
	//		The ItemFileReadStore implements the dojo.data.api.Read API and reads
	//		data from JSON files that have contents in this format --
	//		{ items: [
	//			{ name:'Kermit', color:'green', age:12, friends:['Gonzo', {_reference:{name:'Fozzie Bear'}}]},
	//			{ name:'Fozzie Bear', wears:['hat', 'tie']},
	//			{ name:'Miss Piggy', pets:'Foo-Foo'}
	//		]}
	//		Note that it can also contain an 'identifer' property that specified which attribute on the items 
	//		in the array of items that acts as the unique identifier for that item.
	//
	constructor: function(/* Object */ keywordParameters){
		//	summary: constructor
		//	keywordParameters: {url: String}
		//	keywordParameters: {data: jsonObject}
		//	keywordParameters: {typeMap: object)
		//		The structure of the typeMap object is as follows:
		//		{
		//			type0: function || object,
		//			type1: function || object,
		//			...
		//			typeN: function || object
		//		}
		//		Where if it is a function, it is assumed to be an object constructor that takes the 
		//		value of _value as the initialization parameters.  If it is an object, then it is assumed
		//		to be an object of general form:
		//		{
		//			type: function, //constructor.
		//			deserialize:	function(value) //The function that parses the value and constructs the object defined by type appropriately.
		//		}
	
		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = [];
		this._loadFinished = false;
		this._jsonFileUrl = keywordParameters.url;
		this._ccUrl = keywordParameters.url;
		this.url = keywordParameters.url;
		this._jsonData = keywordParameters.data;
		this.data = null;
		this._datatypeMap = keywordParameters.typeMap || {};
		if(!this._datatypeMap['Date']){
			//If no default mapping for dates, then set this as default.
			//We use the dojo.date.stamp here because the ISO format is the 'dojo way'
			//of generically representing dates.
			this._datatypeMap['Date'] = {
											type: Date,
											deserialize: function(value){
												return dojo.date.stamp.fromISOString(value);
											}
										};
		}
		this._features = {'dojo.data.api.Read':true, 'dojo.data.api.Identity':true};
		this._itemsByIdentity = null;
		this._storeRefPropName = "_S"; // Default name for the store reference to attach to every item.
		this._itemNumPropName = "_0"; // Default Item Id for isItem to attach to every item.
		this._rootItemPropName = "_RI"; // Default Item Id for isItem to attach to every item.
		this._reverseRefMap = "_RRM"; // Default attribute for constructing a reverse reference map for use with reference integrity
		this._loadInProgress = false; //Got to track the initial load to prevent duelling loads of the dataset.
		this._queuedFetches = [];
		if(keywordParameters.urlPreventCache !== undefined){
			this.urlPreventCache = keywordParameters.urlPreventCache?true:false;
		}
		if(keywordParameters.hierarchical !== undefined){
			this.hierarchical = keywordParameters.hierarchical?true:false;
		}
		if(keywordParameters.clearOnClose){
			this.clearOnClose = true;
		}
		if("failOk" in keywordParameters){
			this.failOk = keywordParameters.failOk?true:false;
		}
	},
	
	url: "",	// use "" rather than undefined for the benefit of the parser (#3539)

	//Internal var, crossCheckUrl.  Used so that setting either url or _jsonFileUrl, can still trigger a reload
	//when clearOnClose and close is used.
	_ccUrl: "",

	data: null,	// define this so that the parser can populate it

	typeMap: null, //Define so parser can populate.
	
	//Parameter to allow users to specify if a close call should force a reload or not.
	//By default, it retains the old behavior of not clearing if close is called.  But
	//if set true, the store will be reset to default state.  Note that by doing this,
	//all item handles will become invalid and a new fetch must be issued.
	clearOnClose: false,

	//Parameter to allow specifying if preventCache should be passed to the xhrGet call or not when loading data from a url.  
	//Note this does not mean the store calls the server on each fetch, only that the data load has preventCache set as an option.
	//Added for tracker: #6072
	urlPreventCache: false,
	
	//Parameter for specifying that it is OK for the xhrGet call to fail silently.
	failOk: false,

	//Parameter to indicate to process data from the url as hierarchical 
	//(data items can contain other data items in js form).  Default is true 
	//for backwards compatibility.  False means only root items are processed 
	//as items, all child objects outside of type-mapped objects and those in 
	//specific reference format, are left straight JS data objects.
	hierarchical: true,

	_assertIsItem: function(/* item */ item){
		//	summary:
		//		This function tests whether the item passed in is indeed an item in the store.
		//	item: 
		//		The item to test for being contained by the store.
		if(!this.isItem(item)){ 
			throw new Error("dojo.data.ItemFileReadStore: Invalid item argument.");
		}
	},

	_assertIsAttribute: function(/* attribute-name-string */ attribute){
		//	summary:
		//		This function tests whether the item passed in is indeed a valid 'attribute' like type for the store.
		//	attribute: 
		//		The attribute to test for being contained by the store.
		if(typeof attribute !== "string"){ 
			throw new Error("dojo.data.ItemFileReadStore: Invalid attribute argument.");
		}
	},

	getValue: function(	/* item */ item, 
						/* attribute-name-string */ attribute, 
						/* value? */ defaultValue){
		//	summary: 
		//		See dojo.data.api.Read.getValue()
		var values = this.getValues(item, attribute);
		return (values.length > 0)?values[0]:defaultValue; // mixed
	},

	getValues: function(/* item */ item, 
						/* attribute-name-string */ attribute){
		//	summary: 
		//		See dojo.data.api.Read.getValues()

		this._assertIsItem(item);
		this._assertIsAttribute(attribute);
		// Clone it before returning.  refs: #10474
		return (item[attribute] || []).slice(0); // Array
	},

	getAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getAttributes()
		this._assertIsItem(item);
		var attributes = [];
		for(var key in item){
			// Save off only the real item attributes, not the special id marks for O(1) isItem.
			if((key !== this._storeRefPropName) && (key !== this._itemNumPropName) && (key !== this._rootItemPropName) && (key !== this._reverseRefMap)){
				attributes.push(key);
			}
		}
		return attributes; // Array
	},

	hasAttribute: function(	/* item */ item,
							/* attribute-name-string */ attribute){
		//	summary: 
		//		See dojo.data.api.Read.hasAttribute()
		this._assertIsItem(item);
		this._assertIsAttribute(attribute);
		return (attribute in item);
	},

	containsValue: function(/* item */ item, 
							/* attribute-name-string */ attribute, 
							/* anything */ value){
		//	summary: 
		//		See dojo.data.api.Read.containsValue()
		var regexp = undefined;
		if(typeof value === "string"){
			regexp = dojo.data.util.filter.patternToRegExp(value, false);
		}
		return this._containsValue(item, attribute, value, regexp); //boolean.
	},

	_containsValue: function(	/* item */ item, 
								/* attribute-name-string */ attribute, 
								/* anything */ value,
								/* RegExp?*/ regexp){
		//	summary: 
		//		Internal function for looking at the values contained by the item.
		//	description: 
		//		Internal function for looking at the values contained by the item.  This 
		//		function allows for denoting if the comparison should be case sensitive for
		//		strings or not (for handling filtering cases where string case should not matter)
		//	
		//	item:
		//		The data item to examine for attribute values.
		//	attribute:
		//		The attribute to inspect.
		//	value:	
		//		The value to match.
		//	regexp:
		//		Optional regular expression generated off value if value was of string type to handle wildcarding.
		//		If present and attribute values are string, then it can be used for comparison instead of 'value'
		return dojo.some(this.getValues(item, attribute), function(possibleValue){
			if(possibleValue !== null && !dojo.isObject(possibleValue) && regexp){
				if(possibleValue.toString().match(regexp)){
					return true; // Boolean
				}
			}else if(value === possibleValue){
				return true; // Boolean
			}
		});
	},

	isItem: function(/* anything */ something){
		//	summary: 
		//		See dojo.data.api.Read.isItem()
		if(something && something[this._storeRefPropName] === this){
			if(this._arrayOfAllItems[something[this._itemNumPropName]] === something){
				return true;
			}
		}
		return false; // Boolean
	},

	isItemLoaded: function(/* anything */ something){
		//	summary: 
		//		See dojo.data.api.Read.isItemLoaded()
		return this.isItem(something); //boolean
	},

	loadItem: function(/* object */ keywordArgs){
		//	summary: 
		//		See dojo.data.api.Read.loadItem()
		this._assertIsItem(keywordArgs.item);
	},

	getFeatures: function(){
		//	summary: 
		//		See dojo.data.api.Read.getFeatures()
		return this._features; //Object
	},

	getLabel: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getLabel()
		if(this._labelAttr && this.isItem(item)){
			return this.getValue(item,this._labelAttr); //String
		}
		return undefined; //undefined
	},

	getLabelAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getLabelAttributes()
		if(this._labelAttr){
			return [this._labelAttr]; //array
		}
		return null; //null
	},

	_fetchItems: function(	/* Object */ keywordArgs, 
							/* Function */ findCallback, 
							/* Function */ errorCallback){
		//	summary: 
		//		See dojo.data.util.simpleFetch.fetch()
		var self = this,
		    filter = function(requestArgs, arrayOfItems){
			var items = [],
			    i, key;
			if(requestArgs.query){
				var value,
				    ignoreCase = requestArgs.queryOptions ? requestArgs.queryOptions.ignoreCase : false;

				//See if there are any string values that can be regexp parsed first to avoid multiple regexp gens on the
				//same value for each item examined.  Much more efficient.
				var regexpList = {};
				for(key in requestArgs.query){
					value = requestArgs.query[key];
					if(typeof value === "string"){
						regexpList[key] = dojo.data.util.filter.patternToRegExp(value, ignoreCase);
					}else if(value instanceof RegExp){
						regexpList[key] = value;
					}
				}
				for(i = 0; i < arrayOfItems.length; ++i){
					var match = true;
					var candidateItem = arrayOfItems[i];
					if(candidateItem === null){
						match = false;
					}else{
						for(key in requestArgs.query){
							value = requestArgs.query[key];
							if(!self._containsValue(candidateItem, key, value, regexpList[key])){
								match = false;
							}
						}
					}
					if(match){
						items.push(candidateItem);
					}
				}
				findCallback(items, requestArgs);
			}else{
				// We want a copy to pass back in case the parent wishes to sort the array. 
				// We shouldn't allow resort of the internal list, so that multiple callers 
				// can get lists and sort without affecting each other.  We also need to
				// filter out any null values that have been left as a result of deleteItem()
				// calls in ItemFileWriteStore.
				for(i = 0; i < arrayOfItems.length; ++i){
					var item = arrayOfItems[i];
					if(item !== null){
						items.push(item);
					}
				}
				findCallback(items, requestArgs);
			}
		};

		if(this._loadFinished){
			filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions));
		}else{
			//Do a check on the JsonFileUrl and crosscheck it.
			//If it doesn't match the cross-check, it needs to be updated
			//This allows for either url or _jsonFileUrl to he changed to
			//reset the store load location.  Done this way for backwards 
			//compatibility.  People use _jsonFileUrl (even though officially
			//private.
			if(this._jsonFileUrl !== this._ccUrl){
				dojo.deprecated("dojo.data.ItemFileReadStore: ", 
					"To change the url, set the url property of the store," +
					" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
				this._ccUrl = this._jsonFileUrl;
				this.url = this._jsonFileUrl;
			}else if(this.url !== this._ccUrl){
				this._jsonFileUrl = this.url;
				this._ccUrl = this.url;
			}

			//See if there was any forced reset of data.
			if(this.data != null && this._jsonData == null){
				this._jsonData = this.data;
				this.data = null;
			}

			if(this._jsonFileUrl){
				//If fetches come in before the loading has finished, but while
				//a load is in progress, we have to defer the fetching to be 
				//invoked in the callback.
				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs, filter: filter});
				}else{
					this._loadInProgress = true;
					var getArgs = {
							url: self._jsonFileUrl, 
							handleAs: "json-comment-optional",
							preventCache: this.urlPreventCache,
							failOk: this.failOk
						};
					var getHandler = dojo.xhrGet(getArgs);
					getHandler.addCallback(function(data){
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;
							
							filter(keywordArgs, self._getItemsArray(keywordArgs.queryOptions));
							self._handleQueuedFetches();
						}catch(e){
							self._loadFinished = true;
							self._loadInProgress = false;
							errorCallback(e, keywordArgs);
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						errorCallback(error, keywordArgs);
					});

					//Wire up the cancel to abort of the request
					//This call cancel on the deferred if it hasn't been called
					//yet and then will chain to the simple abort of the
					//simpleFetch keywordArgs
					var oldAbort = null;
					if(keywordArgs.abort){
						oldAbort = keywordArgs.abort;
					}
					keywordArgs.abort = function(){
						var df = getHandler;
						if(df && df.fired === -1){
							df.cancel();
							df = null;
						}
						if(oldAbort){
							oldAbort.call(keywordArgs);
						}
					};
				}
			}else if(this._jsonData){
				try{
					this._loadFinished = true;
					this._getItemsFromLoadedData(this._jsonData);
					this._jsonData = null;
					filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions));
				}catch(e){
					errorCallback(e, keywordArgs);
				}
			}else{
				errorCallback(new Error("dojo.data.ItemFileReadStore: No JSON source data was provided as either URL or a nested Javascript object."), keywordArgs);
			}
		}
	},

	_handleQueuedFetches: function(){
		//	summary: 
		//		Internal function to execute delayed request in the store.
		//Execute any deferred fetches now.
		if(this._queuedFetches.length > 0){
			for(var i = 0; i < this._queuedFetches.length; i++){
				var fData = this._queuedFetches[i],
				    delayedQuery = fData.args,
				    delayedFilter = fData.filter;
				if(delayedFilter){
					delayedFilter(delayedQuery, this._getItemsArray(delayedQuery.queryOptions)); 
				}else{
					this.fetchItemByIdentity(delayedQuery);
				}
			}
			this._queuedFetches = [];
		}
	},

	_getItemsArray: function(/*object?*/queryOptions){
		//	summary: 
		//		Internal function to determine which list of items to search over.
		//	queryOptions: The query options parameter, if any.
		if(queryOptions && queryOptions.deep){
			return this._arrayOfAllItems; 
		}
		return this._arrayOfTopLevelItems;
	},

	close: function(/*dojo.data.api.Request || keywordArgs || null */ request){
		 //	summary: 
		 //		See dojo.data.api.Read.close()
		 if(this.clearOnClose && 
			this._loadFinished && 
			!this._loadInProgress){
			 //Reset all internalsback to default state.  This will force a reload
			 //on next fetch.  This also checks that the data or url param was set 
			 //so that the store knows it can get data.  Without one of those being set,
			 //the next fetch will trigger an error.

			 if(((this._jsonFileUrl == "" || this._jsonFileUrl == null) && 
				 (this.url == "" || this.url == null)
				) && this.data == null){
				 console.debug("dojo.data.ItemFileReadStore: WARNING!  Data reload " +
					" information has not been provided." + 
					"  Please set 'url' or 'data' to the appropriate value before" +
					" the next fetch");
			 }
			 this._arrayOfAllItems = [];
			 this._arrayOfTopLevelItems = [];
			 this._loadFinished = false;
			 this._itemsByIdentity = null;
			 this._loadInProgress = false;
			 this._queuedFetches = [];
		 }
	},

	_getItemsFromLoadedData: function(/* Object */ dataObject){
		//	summary:
		//		Function to parse the loaded data into item format and build the internal items array.
		//	description:
		//		Function to parse the loaded data into item format and build the internal items array.
		//
		//	dataObject:
		//		The JS data object containing the raw data to convery into item format.
		//
		// 	returns: array
		//		Array of items in store item format.
		
		// First, we define a couple little utility functions...
		var addingArrays = false,
		    self = this;
		
		function valueIsAnItem(/* anything */ aValue){
			// summary:
			//		Given any sort of value that could be in the raw json data,
			//		return true if we should interpret the value as being an
			//		item itself, rather than a literal value or a reference.
			// example:
			// 	|	false == valueIsAnItem("Kermit");
			// 	|	false == valueIsAnItem(42);
			// 	|	false == valueIsAnItem(new Date());
			// 	|	false == valueIsAnItem({_type:'Date', _value:'May 14, 1802'});
			// 	|	false == valueIsAnItem({_reference:'Kermit'});
			// 	|	true == valueIsAnItem({name:'Kermit', color:'green'});
			// 	|	true == valueIsAnItem({iggy:'pop'});
			// 	|	true == valueIsAnItem({foo:42});
			var isItem = (
				(aValue !== null) &&
				(typeof aValue === "object") &&
				(!dojo.isArray(aValue) || addingArrays) &&
				(!dojo.isFunction(aValue)) &&
				(aValue.constructor == Object || dojo.isArray(aValue)) &&
				(typeof aValue._reference === "undefined") && 
				(typeof aValue._type === "undefined") && 
				(typeof aValue._value === "undefined") &&
				self.hierarchical
			);
			return isItem;
		}
		
		function addItemAndSubItemsToArrayOfAllItems(/* Item */ anItem){
			self._arrayOfAllItems.push(anItem);
			for(var attribute in anItem){
				var valueForAttribute = anItem[attribute];
				if(valueForAttribute){
					if(dojo.isArray(valueForAttribute)){
						var valueArray = valueForAttribute;
						for(var k = 0; k < valueArray.length; ++k){
							var singleValue = valueArray[k];
							if(valueIsAnItem(singleValue)){
								addItemAndSubItemsToArrayOfAllItems(singleValue);
							}
						}
					}else{
						if(valueIsAnItem(valueForAttribute)){
							addItemAndSubItemsToArrayOfAllItems(valueForAttribute);
						}
					}
				}
			}
		}

		this._labelAttr = dataObject.label;

		// We need to do some transformations to convert the data structure
		// that we read from the file into a format that will be convenient
		// to work with in memory.

		// Step 1: Walk through the object hierarchy and build a list of all items
		var i,
		    item;
		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = dataObject.items;

		for(i = 0; i < this._arrayOfTopLevelItems.length; ++i){
			item = this._arrayOfTopLevelItems[i];
			if(dojo.isArray(item)){
				addingArrays = true;
			}
			addItemAndSubItemsToArrayOfAllItems(item);
			item[this._rootItemPropName]=true;
		}

		// Step 2: Walk through all the attribute values of all the items, 
		// and replace single values with arrays.  For example, we change this:
		//		{ name:'Miss Piggy', pets:'Foo-Foo'}
		// into this:
		//		{ name:['Miss Piggy'], pets:['Foo-Foo']}
		// 
		// We also store the attribute names so we can validate our store  
		// reference and item id special properties for the O(1) isItem
		var allAttributeNames = {},
		    key;

		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			for(key in item){
				if(key !== this._rootItemPropName){
					var value = item[key];
					if(value !== null){
						if(!dojo.isArray(value)){
							item[key] = [value];
						}
					}else{
						item[key] = [null];
					}
				}
				allAttributeNames[key]=key;
			}
		}

		// Step 3: Build unique property names to use for the _storeRefPropName and _itemNumPropName
		// This should go really fast, it will generally never even run the loop.
		while(allAttributeNames[this._storeRefPropName]){
			this._storeRefPropName += "_";
		}
		while(allAttributeNames[this._itemNumPropName]){
			this._itemNumPropName += "_";
		}
		while(allAttributeNames[this._reverseRefMap]){
			this._reverseRefMap += "_";
		}

		// Step 4: Some data files specify an optional 'identifier', which is 
		// the name of an attribute that holds the identity of each item. 
		// If this data file specified an identifier attribute, then build a 
		// hash table of items keyed by the identity of the items.
		var arrayOfValues;

		var identifier = dataObject.identifier;
		if(identifier){
			this._itemsByIdentity = {};
			this._features['dojo.data.api.Identity'] = identifier;
			for(i = 0; i < this._arrayOfAllItems.length; ++i){
				item = this._arrayOfAllItems[i];
				arrayOfValues = item[identifier];
				var identity = arrayOfValues[0];
				if(!this._itemsByIdentity[identity]){
					this._itemsByIdentity[identity] = item;
				}else{
					if(this._jsonFileUrl){
						throw new Error("dojo.data.ItemFileReadStore:  The json data as specified by: [" + this._jsonFileUrl + "] is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}else if(this._jsonData){
						throw new Error("dojo.data.ItemFileReadStore:  The json data provided by the creation arguments is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}
				}
			}
		}else{
			this._features['dojo.data.api.Identity'] = Number;
		}

		// Step 5: Walk through all the items, and set each item's properties 
		// for _storeRefPropName and _itemNumPropName, so that store.isItem() will return true.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			item[this._storeRefPropName] = this;
			item[this._itemNumPropName] = i;
		}

		// Step 6: We walk through all the attribute values of all the items,
		// looking for type/value literals and item-references.
		//
		// We replace item-references with pointers to items.  For example, we change:
		//		{ name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
		// into this:
		//		{ name:['Kermit'], friends:[miss_piggy] } 
		// (where miss_piggy is the object representing the 'Miss Piggy' item).
		//
		// We replace type/value pairs with typed-literals.  For example, we change:
		//		{ name:['Nelson Mandela'], born:[{_type:'Date', _value:'July 18, 1918'}] }
		// into this:
		//		{ name:['Kermit'], born:(new Date('July 18, 1918')) } 
		//
		// We also generate the associate map for all items for the O(1) isItem function.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i]; // example: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
			for(key in item){
				arrayOfValues = item[key]; // example: [{_reference:{name:'Miss Piggy'}}]
				for(var j = 0; j < arrayOfValues.length; ++j){
					value = arrayOfValues[j]; // example: {_reference:{name:'Miss Piggy'}}
					if(value !== null && typeof value == "object"){
						if(("_type" in value) && ("_value" in value)){
							var type = value._type; // examples: 'Date', 'Color', or 'ComplexNumber'
							var mappingObj = this._datatypeMap[type]; // examples: Date, dojo.Color, foo.math.ComplexNumber, {type: dojo.Color, deserialize(value){ return new dojo.Color(value)}}
							if(!mappingObj){ 
								throw new Error("dojo.data.ItemFileReadStore: in the typeMap constructor arg, no object class was specified for the datatype '" + type + "'");
							}else if(dojo.isFunction(mappingObj)){
								arrayOfValues[j] = new mappingObj(value._value);
							}else if(dojo.isFunction(mappingObj.deserialize)){
								arrayOfValues[j] = mappingObj.deserialize(value._value);
							}else{
								throw new Error("dojo.data.ItemFileReadStore: Value provided in typeMap was neither a constructor, nor a an object with a deserialize function");
							}
						}
						if(value._reference){
							var referenceDescription = value._reference; // example: {name:'Miss Piggy'}
							if(!dojo.isObject(referenceDescription)){
								// example: 'Miss Piggy'
								// from an item like: { name:['Kermit'], friends:[{_reference:'Miss Piggy'}]}
								arrayOfValues[j] = this._getItemByIdentity(referenceDescription);
							}else{
								// example: {name:'Miss Piggy'}
								// from an item like: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
								for(var k = 0; k < this._arrayOfAllItems.length; ++k){
									var candidateItem = this._arrayOfAllItems[k],
									    found = true;
									for(var refKey in referenceDescription){
										if(candidateItem[refKey] != referenceDescription[refKey]){ 
											found = false; 
										}
									}
									if(found){ 
										arrayOfValues[j] = candidateItem; 
									}
								}
							}
							if(this.referenceIntegrity){
								var refItem = arrayOfValues[j];
								if(this.isItem(refItem)){
									this._addReferenceToMap(refItem, item, key);
								}
							}
						}else if(this.isItem(value)){
							//It's a child item (not one referenced through _reference).  
							//We need to treat this as a referenced item, so it can be cleaned up
							//in a write store easily.
							if(this.referenceIntegrity){
								this._addReferenceToMap(value, item, key);
							}
						}
					}
				}
			}
		}
	},

	_addReferenceToMap: function(/*item*/ refItem, /*item*/ parentItem, /*string*/ attribute){
		 //	summary:
		 //		Method to add an reference map entry for an item and attribute.
		 //	description:
		 //		Method to add an reference map entry for an item and attribute. 		 //
		 //	refItem:
		 //		The item that is referenced.
		 //	parentItem:
		 //		The item that holds the new reference to refItem.
		 //	attribute:
		 //		The attribute on parentItem that contains the new reference.
		 
		 //Stub function, does nothing.  Real processing is in ItemFileWriteStore.
	},

	getIdentity: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Identity.getIdentity()
		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			return item[this._itemNumPropName]; // Number
		}else{
			var arrayOfValues = item[identifier];
			if(arrayOfValues){
				return arrayOfValues[0]; // Object || String
			}
		}
		return null; // null
	},

	fetchItemByIdentity: function(/* Object */ keywordArgs){
		//	summary: 
		//		See dojo.data.api.Identity.fetchItemByIdentity()

		// Hasn't loaded yet, we have to trigger the load.
		var item,
		    scope;
		if(!this._loadFinished){
			var self = this;
			//Do a check on the JsonFileUrl and crosscheck it.
			//If it doesn't match the cross-check, it needs to be updated
			//This allows for either url or _jsonFileUrl to he changed to
			//reset the store load location.  Done this way for backwards 
			//compatibility.  People use _jsonFileUrl (even though officially
			//private.
			if(this._jsonFileUrl !== this._ccUrl){
				dojo.deprecated("dojo.data.ItemFileReadStore: ", 
					"To change the url, set the url property of the store," +
					" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
				this._ccUrl = this._jsonFileUrl;
				this.url = this._jsonFileUrl;
			}else if(this.url !== this._ccUrl){
				this._jsonFileUrl = this.url;
				this._ccUrl = this.url;
			}
			
			//See if there was any forced reset of data.
			if(this.data != null && this._jsonData == null){
				this._jsonData = this.data;
				this.data = null;
			}

			if(this._jsonFileUrl){

				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs});
				}else{
					this._loadInProgress = true;
					var getArgs = {
							url: self._jsonFileUrl, 
							handleAs: "json-comment-optional",
							preventCache: this.urlPreventCache,
							failOk: this.failOk
					};
					var getHandler = dojo.xhrGet(getArgs);
					getHandler.addCallback(function(data){
						var scope = keywordArgs.scope?keywordArgs.scope:dojo.global;
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;
							item = self._getItemByIdentity(keywordArgs.identity);
							if(keywordArgs.onItem){
								keywordArgs.onItem.call(scope, item);
							}
							self._handleQueuedFetches();
						}catch(error){
							self._loadInProgress = false;
							if(keywordArgs.onError){
								keywordArgs.onError.call(scope, error);
							}
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						if(keywordArgs.onError){
							var scope = keywordArgs.scope?keywordArgs.scope:dojo.global;
							keywordArgs.onError.call(scope, error);
						}
					});
				}

			}else if(this._jsonData){
				// Passed in data, no need to xhr.
				self._getItemsFromLoadedData(self._jsonData);
				self._jsonData = null;
				self._loadFinished = true;
				item = self._getItemByIdentity(keywordArgs.identity);
				if(keywordArgs.onItem){
					scope = keywordArgs.scope?keywordArgs.scope:dojo.global;
					keywordArgs.onItem.call(scope, item);
				}
			} 
		}else{
			// Already loaded.  We can just look it up and call back.
			item = this._getItemByIdentity(keywordArgs.identity);
			if(keywordArgs.onItem){
				scope = keywordArgs.scope?keywordArgs.scope:dojo.global;
				keywordArgs.onItem.call(scope, item);
			}
		}
	},

	_getItemByIdentity: function(/* Object */ identity){
		//	summary:
		//		Internal function to look an item up by its identity map.
		var item = null;
		if(this._itemsByIdentity){
			item = this._itemsByIdentity[identity];
		}else{
			item = this._arrayOfAllItems[identity];
		}
		if(item === undefined){
			item = null;
		}
		return item; // Object
	},

	getIdentityAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Identity.getIdentifierAttributes()
		 
		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			// If (identifier === Number) it means getIdentity() just returns
			// an integer item-number for each item.  The dojo.data.api.Identity
			// spec says we need to return null if the identity is not composed 
			// of attributes 
			return null; // null
		}else{
			return [identifier]; // Array
		}
	},
	
	_forceLoad: function(){
		//	summary: 
		//		Internal function to force a load of the store if it hasn't occurred yet.  This is required
		//		for specific functions to work properly.  
		var self = this;
		//Do a check on the JsonFileUrl and crosscheck it.
		//If it doesn't match the cross-check, it needs to be updated
		//This allows for either url or _jsonFileUrl to he changed to
		//reset the store load location.  Done this way for backwards 
		//compatibility.  People use _jsonFileUrl (even though officially
		//private.
		if(this._jsonFileUrl !== this._ccUrl){
			dojo.deprecated("dojo.data.ItemFileReadStore: ", 
				"To change the url, set the url property of the store," +
				" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
			this._ccUrl = this._jsonFileUrl;
			this.url = this._jsonFileUrl;
		}else if(this.url !== this._ccUrl){
			this._jsonFileUrl = this.url;
			this._ccUrl = this.url;
		}

		//See if there was any forced reset of data.
		if(this.data != null && this._jsonData == null){
			this._jsonData = this.data;
			this.data = null;
		}

		if(this._jsonFileUrl){
				var getArgs = {
					url: this._jsonFileUrl, 
					handleAs: "json-comment-optional",
					preventCache: this.urlPreventCache,
					failOk: this.failOk,
					sync: true
				};
			var getHandler = dojo.xhrGet(getArgs);
			getHandler.addCallback(function(data){
				try{
					//Check to be sure there wasn't another load going on concurrently 
					//So we don't clobber data that comes in on it.  If there is a load going on
					//then do not save this data.  It will potentially clobber current data.
					//We mainly wanted to sync/wait here.
					//TODO:  Revisit the loading scheme of this store to improve multi-initial
					//request handling.
					if(self._loadInProgress !== true && !self._loadFinished){
						self._getItemsFromLoadedData(data);
						self._loadFinished = true;
					}else if(self._loadInProgress){
						//Okay, we hit an error state we can't recover from.  A forced load occurred
						//while an async load was occurring.  Since we cannot block at this point, the best
						//that can be managed is to throw an error.
						throw new Error("dojo.data.ItemFileReadStore:  Unable to perform a synchronous load, an async load is in progress."); 
					}
				}catch(e){
					console.log(e);
					throw e;
				}
			});
			getHandler.addErrback(function(error){
				throw error;
			});
		}else if(this._jsonData){
			self._getItemsFromLoadedData(self._jsonData);
			self._jsonData = null;
			self._loadFinished = true;
		} 
	}
});
//Mix in the simple fetch implementation to this class.
dojo.extend(dojo.data.ItemFileReadStore,dojo.data.util.simpleFetch);

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

if(!dojo._hasResource["dijit.form.TextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.TextBox"] = true;
dojo.provide("dijit.form.TextBox");



dojo.declare(
	"dijit.form.TextBox",
	dijit.form._FormValueWidget,
	{
		// summary:
		//		A base class for textbox form inputs

		// trim: Boolean
		//		Removes leading and trailing whitespace if true.  Default is false.
		trim: false,

		// uppercase: Boolean
		//		Converts all characters to uppercase if true.  Default is false.
		uppercase: false,

		// lowercase: Boolean
		//		Converts all characters to lowercase if true.  Default is false.
		lowercase: false,

		// propercase: Boolean
		//		Converts the first character of each word to uppercase if true.
		propercase: false,

		//	maxLength: String
		//		HTML INPUT tag maxLength declaration.
		maxLength: "",

		//	selectOnClick: [const] Boolean
		//		If true, all text will be selected when focused with mouse
		selectOnClick: false,

		//	placeHolder: String
		//		Defines a hint to help users fill out the input field (as defined in HTML 5).
		//		This should only contain plain text (no html markup).
		placeHolder: "",
		
		templateString: dojo.cache("dijit.form", "templates/TextBox.html", "<div class=\"dijit dijitReset dijitInline dijitLeft\" id=\"widget_${id}\" waiRole=\"presentation\"\n\t><div class=\"dijitReset dijitInputField dijitInputContainer\"\n\t\t><input class=\"dijitReset dijitInputInner\" dojoAttachPoint='textbox,focusNode' autocomplete=\"off\"\n\t\t\t${!nameAttrSetting} type='${type}'\n\t/></div\n></div>\n"),
		_singleNodeTemplate: '<input class="dijit dijitReset dijitLeft dijitInputField" dojoAttachPoint="textbox,focusNode" autocomplete="off" type="${type}" ${!nameAttrSetting} />',

		_buttonInputDisabled: dojo.isIE ? "disabled" : "", // allows IE to disallow focus, but Firefox cannot be disabled for mousedown events

		baseClass: "dijitTextBox",

		attributeMap: dojo.delegate(dijit.form._FormValueWidget.prototype.attributeMap, {
			maxLength: "focusNode"
		}),
		
		postMixInProperties: function(){
			var type = this.type.toLowerCase();
			if(this.templateString.toLowerCase() == "input" || ((type == "hidden" || type == "file") && this.templateString == dijit.form.TextBox.prototype.templateString)){
				this.templateString = this._singleNodeTemplate;
			}
			this.inherited(arguments);
		},

		_setPlaceHolderAttr: function(v){
			this.placeHolder = v;
			if(!this._phspan){
				this._attachPoints.push('_phspan');
				/* dijitInputField class gives placeHolder same padding as the input field
				 * parent node already has dijitInputField class but it doesn't affect this <span>
				 * since it's position: absolute.
				 */
				this._phspan = dojo.create('span',{className:'dijitPlaceHolder dijitInputField'},this.textbox,'after');
			}
			this._phspan.innerHTML="";
			this._phspan.appendChild(document.createTextNode(v));
			
			this._updatePlaceHolder();
		},
		
		_updatePlaceHolder: function(){
			if(this._phspan){
				this._phspan.style.display=(this.placeHolder&&!this._focused&&!this.textbox.value)?"":"none";
			}
		},

		_getValueAttr: function(){
			// summary:
			//		Hook so attr('value') works as we like.
			// description:
			//		For `dijit.form.TextBox` this basically returns the value of the <input>.
			//
			//		For `dijit.form.MappedTextBox` subclasses, which have both
			//		a "displayed value" and a separate "submit value",
			//		This treats the "displayed value" as the master value, computing the
			//		submit value from it via this.parse().
			return this.parse(this.get('displayedValue'), this.constraints);
		},

		_setValueAttr: function(value, /*Boolean?*/ priorityChange, /*String?*/ formattedValue){
			// summary:
			//		Hook so attr('value', ...) works.
			//
			// description:
			//		Sets the value of the widget to "value" which can be of
			//		any type as determined by the widget.
			//
			// value:
			//		The visual element value is also set to a corresponding,
			//		but not necessarily the same, value.
			//
			// formattedValue:
			//		If specified, used to set the visual element value,
			//		otherwise a computed visual value is used.
			//
			// priorityChange:
			//		If true, an onChange event is fired immediately instead of
			//		waiting for the next blur event.

			var filteredValue;
			if(value !== undefined){
				// TODO: this is calling filter() on both the display value and the actual value.
				// I added a comment to the filter() definition about this, but it should be changed.
				filteredValue = this.filter(value);
				if(typeof formattedValue != "string"){
					if(filteredValue !== null && ((typeof filteredValue != "number") || !isNaN(filteredValue))){
						formattedValue = this.filter(this.format(filteredValue, this.constraints));
					}else{ formattedValue = ''; }
				}
			}
			if(formattedValue != null && formattedValue != undefined && ((typeof formattedValue) != "number" || !isNaN(formattedValue)) && this.textbox.value != formattedValue){
				this.textbox.value = formattedValue;
			}

			this._updatePlaceHolder();

			this.inherited(arguments, [filteredValue, priorityChange]);
		},

		// displayedValue: String
		//		For subclasses like ComboBox where the displayed value
		//		(ex: Kentucky) and the serialized value (ex: KY) are different,
		//		this represents the displayed value.
		//
		//		Setting 'displayedValue' through attr('displayedValue', ...)
		//		updates 'value', and vice-versa.  Otherwise 'value' is updated
		//		from 'displayedValue' periodically, like onBlur etc.
		//
		//		TODO: move declaration to MappedTextBox?
		//		Problem is that ComboBox references displayedValue,
		//		for benefit of FilteringSelect.
		displayedValue: "",

		getDisplayedValue: function(){
			// summary:
			//		Deprecated.   Use set('displayedValue') instead.
			// tags:
			//		deprecated
			dojo.deprecated(this.declaredClass+"::getDisplayedValue() is deprecated. Use set('displayedValue') instead.", "", "2.0");
			return this.get('displayedValue');
		},

		_getDisplayedValueAttr: function(){
			// summary:
			//		Hook so attr('displayedValue') works.
			// description:
			//		Returns the displayed value (what the user sees on the screen),
			// 		after filtering (ie, trimming spaces etc.).
			//
			//		For some subclasses of TextBox (like ComboBox), the displayed value
			//		is different from the serialized value that's actually
			//		sent to the server (see dijit.form.ValidationTextBox.serialize)

			return this.filter(this.textbox.value);
		},

		setDisplayedValue: function(/*String*/value){
			// summary:
			//		Deprecated.   Use set('displayedValue', ...) instead.
			// tags:
			//		deprecated
			dojo.deprecated(this.declaredClass+"::setDisplayedValue() is deprecated. Use set('displayedValue', ...) instead.", "", "2.0");
			this.set('displayedValue', value);
		},

		_setDisplayedValueAttr: function(/*String*/value){
			// summary:
			//		Hook so attr('displayedValue', ...) works.
			// description:
			//		Sets the value of the visual element to the string "value".
			//		The widget value is also set to a corresponding,
			//		but not necessarily the same, value.

			if(value === null || value === undefined){ value = '' }
			else if(typeof value != "string"){ value = String(value) }
			this.textbox.value = value;
			this._setValueAttr(this.get('value'), undefined, value);
		},

		format: function(/* String */ value, /* Object */ constraints){
			// summary:
			//		Replacable function to convert a value to a properly formatted string.
			// tags:
			//		protected extension
			return ((value == null || value == undefined) ? "" : (value.toString ? value.toString() : value));
		},

		parse: function(/* String */ value, /* Object */ constraints){
			// summary:
			//		Replacable function to convert a formatted string to a value
			// tags:
			//		protected extension

			return value;	// String
		},

		_refreshState: function(){
			// summary:
			//		After the user types some characters, etc., this method is
			//		called to check the field for validity etc.  The base method
			//		in `dijit.form.TextBox` does nothing, but subclasses override.
			// tags:
			//		protected
		},

		_onInput: function(e){
			if(e && e.type && /key/i.test(e.type) && e.keyCode){
				switch(e.keyCode){
					case dojo.keys.SHIFT:
					case dojo.keys.ALT:
					case dojo.keys.CTRL:
					case dojo.keys.TAB:
						return;
				}
			}
			if(this.intermediateChanges){
				var _this = this;
				// the setTimeout allows the key to post to the widget input box
				setTimeout(function(){ _this._handleOnChange(_this.get('value'), false); }, 0);
			}
			this._refreshState();
		},

		postCreate: function(){
			// setting the value here is needed since value="" in the template causes "undefined"
			// and setting in the DOM (instead of the JS object) helps with form reset actions
			if(dojo.isIE){ // IE INPUT tag fontFamily has to be set directly using STYLE
				var s = dojo.getComputedStyle(this.domNode);
				if(s){
					var ff = s.fontFamily;
					if(ff){
						var inputs = this.domNode.getElementsByTagName("INPUT");
						if(inputs){
							for(var i=0; i < inputs.length; i++){
								inputs[i].style.fontFamily = ff;
							}
						}
					}
				}
			}
			this.textbox.setAttribute("value", this.textbox.value); // DOM and JS values shuld be the same
			this.inherited(arguments);
			if(dojo.isMoz || dojo.isOpera){
				this.connect(this.textbox, "oninput", this._onInput);
			}else{
				this.connect(this.textbox, "onkeydown", this._onInput);
				this.connect(this.textbox, "onkeyup", this._onInput);
				this.connect(this.textbox, "onpaste", this._onInput);
				this.connect(this.textbox, "oncut", this._onInput);
			}
		},

		_blankValue: '', // if the textbox is blank, what value should be reported
		filter: function(val){
			// summary:
			//		Auto-corrections (such as trimming) that are applied to textbox
			//		value on blur or form submit.
			// description:
			//		For MappedTextBox subclasses, this is called twice
			// 			- once with the display value
			//			- once the value as set/returned by attr('value', ...)
			//		and attr('value'), ex: a Number for NumberTextBox.
			//
			//		In the latter case it does corrections like converting null to NaN.  In
			//		the former case the NumberTextBox.filter() method calls this.inherited()
			//		to execute standard trimming code in TextBox.filter().
			//
			//		TODO: break this into two methods in 2.0
			//
			// tags:
			//		protected extension
			if(val === null){ return this._blankValue; }
			if(typeof val != "string"){ return val; }
			if(this.trim){
				val = dojo.trim(val);
			}
			if(this.uppercase){
				val = val.toUpperCase();
			}
			if(this.lowercase){
				val = val.toLowerCase();
			}
			if(this.propercase){
				val = val.replace(/[^\s]+/g, function(word){
					return word.substring(0,1).toUpperCase() + word.substring(1);
				});
			}
			return val;
		},

		_setBlurValue: function(){
			this._setValueAttr(this.get('value'), true);
		},

		_onBlur: function(e){
			if(this.disabled){ return; }
			this._setBlurValue();
			this.inherited(arguments);

			if(this._selectOnClickHandle){
				this.disconnect(this._selectOnClickHandle);
			}
			if(this.selectOnClick && dojo.isMoz){
				this.textbox.selectionStart = this.textbox.selectionEnd = undefined; // clear selection so that the next mouse click doesn't reselect
			}
			
			this._updatePlaceHolder();
		},

		_onFocus: function(/*String*/ by){
			if(this.disabled || this.readOnly){ return; }

			// Select all text on focus via click if nothing already selected.
			// Since mouse-up will clear the selection need to defer selection until after mouse-up.
			// Don't do anything on focus by tabbing into the widget since there's no associated mouse-up event.
			if(this.selectOnClick && by == "mouse"){
				this._selectOnClickHandle = this.connect(this.domNode, "onmouseup", function(){
					// Only select all text on first click; otherwise users would have no way to clear
					// the selection.
					this.disconnect(this._selectOnClickHandle);

					// Check if the user selected some text manually (mouse-down, mouse-move, mouse-up)
					// and if not, then select all the text
					var textIsNotSelected;
					if(dojo.isIE){
						var range = dojo.doc.selection.createRange();
						var parent = range.parentElement();
						textIsNotSelected = parent == this.textbox && range.text.length == 0;
					}else{
						textIsNotSelected = this.textbox.selectionStart == this.textbox.selectionEnd;
					}
					if(textIsNotSelected){
						dijit.selectInputText(this.textbox);
					}
				});
			}

			this._updatePlaceHolder();
			
			this._refreshState();
			this.inherited(arguments);
		},

		reset: function(){
			// Overrides dijit._FormWidget.reset().
			// Additionally resets the displayed textbox value to ''
			this.textbox.value = '';
			this.inherited(arguments);
		}
	}
);

dijit.selectInputText = function(/*DomNode*/element, /*Number?*/ start, /*Number?*/ stop){
	// summary:
	//		Select text in the input element argument, from start (default 0), to stop (default end).

	// TODO: use functions in _editor/selection.js?
	var _window = dojo.global;
	var _document = dojo.doc;
	element = dojo.byId(element);
	if(isNaN(start)){ start = 0; }
	if(isNaN(stop)){ stop = element.value ? element.value.length : 0; }
	dijit.focus(element);
	if(_document["selection"] && dojo.body()["createTextRange"]){ // IE
		if(element.createTextRange){
			var range = element.createTextRange();
			with(range){
				collapse(true);
				moveStart("character", -99999); // move to 0
				moveStart("character", start); // delta from 0 is the correct position
				moveEnd("character", stop-start);
				select();
			}
		}
	}else if(_window["getSelection"]){
		if(element.setSelectionRange){
			element.setSelectionRange(start, stop);
		}
	}
};

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

if(!dojo._hasResource["dijit.form.DropDownButton"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.DropDownButton"] = true;
dojo.provide("dijit.form.DropDownButton");



}

if(!dojo._hasResource["dojo.regexp"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.regexp"] = true;
dojo.provide("dojo.regexp");

/*=====
dojo.regexp = {
	// summary: Regular expressions and Builder resources
};
=====*/

dojo.regexp.escapeString = function(/*String*/str, /*String?*/except){
	//	summary:
	//		Adds escape sequences for special characters in regular expressions
	// except:
	//		a String with special characters to be left unescaped

	return str.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, function(ch){
		if(except && except.indexOf(ch) != -1){
			return ch;
		}
		return "\\" + ch;
	}); // String
}

dojo.regexp.buildGroupRE = function(/*Object|Array*/arr, /*Function*/re, /*Boolean?*/nonCapture){
	//	summary:
	//		Builds a regular expression that groups subexpressions
	//	description:
	//		A utility function used by some of the RE generators. The
	//		subexpressions are constructed by the function, re, in the second
	//		parameter.  re builds one subexpression for each elem in the array
	//		a, in the first parameter. Returns a string for a regular
	//		expression that groups all the subexpressions.
	// arr:
	//		A single value or an array of values.
	// re:
	//		A function. Takes one parameter and converts it to a regular
	//		expression. 
	// nonCapture:
	//		If true, uses non-capturing match, otherwise matches are retained
	//		by regular expression. Defaults to false

	// case 1: a is a single value.
	if(!(arr instanceof Array)){
		return re(arr); // String
	}

	// case 2: a is an array
	var b = [];
	for(var i = 0; i < arr.length; i++){
		// convert each elem to a RE
		b.push(re(arr[i]));
	}

	 // join the REs as alternatives in a RE group.
	return dojo.regexp.group(b.join("|"), nonCapture); // String
}

dojo.regexp.group = function(/*String*/expression, /*Boolean?*/nonCapture){
	// summary:
	//		adds group match to expression
	// nonCapture:
	//		If true, uses non-capturing match, otherwise matches are retained
	//		by regular expression. 
	return "(" + (nonCapture ? "?:":"") + expression + ")"; // String
}

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

if(!dojo._hasResource["dijit.Tooltip"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Tooltip"] = true;
dojo.provide("dijit.Tooltip");




dojo.declare(
	"dijit._MasterTooltip",
	[dijit._Widget, dijit._Templated],
	{
		// summary:
		//		Internal widget that holds the actual tooltip markup,
		//		which occurs once per page.
		//		Called by Tooltip widgets which are just containers to hold
		//		the markup
		// tags:
		//		protected

		// duration: Integer
		//		Milliseconds to fade in/fade out
		duration: dijit.defaultDuration,

		templateString: dojo.cache("dijit", "templates/Tooltip.html", "<div class=\"dijitTooltip dijitTooltipLeft\" id=\"dojoTooltip\">\n\t<div class=\"dijitTooltipContainer dijitTooltipContents\" dojoAttachPoint=\"containerNode\" waiRole='alert'></div>\n\t<div class=\"dijitTooltipConnector\"></div>\n</div>\n"),

		postCreate: function(){
			dojo.body().appendChild(this.domNode);

			this.bgIframe = new dijit.BackgroundIframe(this.domNode);

			// Setup fade-in and fade-out functions.
			this.fadeIn = dojo.fadeIn({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onShow") });
			this.fadeOut = dojo.fadeOut({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onHide") });

		},

		show: function(/*String*/ innerHTML, /*DomNode*/ aroundNode, /*String[]?*/ position, /*Boolean*/ rtl){
			// summary:
			//		Display tooltip w/specified contents to right of specified node
			//		(To left if there's no space on the right, or if rtl == true)

			if(this.aroundNode && this.aroundNode === aroundNode){
				return;
			}

			if(this.fadeOut.status() == "playing"){
				// previous tooltip is being hidden; wait until the hide completes then show new one
				this._onDeck=arguments;
				return;
			}
			this.containerNode.innerHTML=innerHTML;

			var pos = dijit.placeOnScreenAroundElement(this.domNode, aroundNode, dijit.getPopupAroundAlignment((position && position.length) ? position : dijit.Tooltip.defaultPosition, !rtl), dojo.hitch(this, "orient"));

			// show it
			dojo.style(this.domNode, "opacity", 0);
			this.fadeIn.play();
			this.isShowingNow = true;
			this.aroundNode = aroundNode;
		},

		orient: function(/* DomNode */ node, /* String */ aroundCorner, /* String */ tooltipCorner){
			// summary:
			//		Private function to set CSS for tooltip node based on which position it's in.
			//		This is called by the dijit popup code.
			// tags:
			//		protected

			node.className = "dijitTooltip " +
				{
					"BL-TL": "dijitTooltipBelow dijitTooltipABLeft",
					"TL-BL": "dijitTooltipAbove dijitTooltipABLeft",
					"BR-TR": "dijitTooltipBelow dijitTooltipABRight",
					"TR-BR": "dijitTooltipAbove dijitTooltipABRight",
					"BR-BL": "dijitTooltipRight",
					"BL-BR": "dijitTooltipLeft"
				}[aroundCorner + "-" + tooltipCorner];
		},

		_onShow: function(){
			// summary:
			//		Called at end of fade-in operation
			// tags:
			//		protected
			if(dojo.isIE){
				// the arrow won't show up on a node w/an opacity filter
				this.domNode.style.filter="";
			}
		},

		hide: function(aroundNode){
			// summary:
			//		Hide the tooltip
			if(this._onDeck && this._onDeck[1] == aroundNode){
				// this hide request is for a show() that hasn't even started yet;
				// just cancel the pending show()
				this._onDeck=null;
			}else if(this.aroundNode === aroundNode){
				// this hide request is for the currently displayed tooltip
				this.fadeIn.stop();
				this.isShowingNow = false;
				this.aroundNode = null;
				this.fadeOut.play();
			}else{
				// just ignore the call, it's for a tooltip that has already been erased
			}
		},

		_onHide: function(){
			// summary:
			//		Called at end of fade-out operation
			// tags:
			//		protected

			this.domNode.style.cssText="";	// to position offscreen again
			this.containerNode.innerHTML="";
			if(this._onDeck){
				// a show request has been queued up; do it now
				this.show.apply(this, this._onDeck);
				this._onDeck=null;
			}
		}

	}
);

dijit.showTooltip = function(/*String*/ innerHTML, /*DomNode*/ aroundNode, /*String[]?*/ position, /*Boolean*/ rtl){
	// summary:
	//		Display tooltip w/specified contents in specified position.
	//		See description of dijit.Tooltip.defaultPosition for details on position parameter.
	//		If position is not specified then dijit.Tooltip.defaultPosition is used.
	if(!dijit._masterTT){ dijit._masterTT = new dijit._MasterTooltip(); }
	return dijit._masterTT.show(innerHTML, aroundNode, position, rtl);
};

dijit.hideTooltip = function(aroundNode){
	// summary:
	//		Hide the tooltip
	if(!dijit._masterTT){ dijit._masterTT = new dijit._MasterTooltip(); }
	return dijit._masterTT.hide(aroundNode);
};

dojo.declare(
	"dijit.Tooltip",
	dijit._Widget,
	{
		// summary:
		//		Pops up a tooltip (a help message) when you hover over a node.

		// label: String
		//		Text to display in the tooltip.
		//		Specified as innerHTML when creating the widget from markup.
		label: "",

		// showDelay: Integer
		//		Number of milliseconds to wait after hovering over/focusing on the object, before
		//		the tooltip is displayed.
		showDelay: 400,

		// connectId: [const] String[]
		//		Id's of domNodes to attach the tooltip to.
		//		When user hovers over any of the specified dom nodes, the tooltip will appear.
		//
		//		Note: Currently connectId can only be specified on initialization, it cannot
		//		be changed via attr('connectId', ...)
		//
		//		Note: in 2.0 this will be renamed to connectIds for less confusion.
		connectId: [],

		// position: String[]
		//		See description of `dijit.Tooltip.defaultPosition` for details on position parameter.
		position: [],

		constructor: function(){
			// Map id's of nodes I'm connected to to a list of the this.connect() handles
			this._nodeConnectionsById = {};
		},

		_setConnectIdAttr: function(newIds){
			for(var oldId in this._nodeConnectionsById){
				this.removeTarget(oldId);
			}
			dojo.forEach(dojo.isArrayLike(newIds) ? newIds : [newIds], this.addTarget, this);
		},

		_getConnectIdAttr: function(){
			var ary = [];
			for(var id in this._nodeConnectionsById){
				ary.push(id);
			}
			return ary;
		},

		addTarget: function(/*DOMNODE || String*/ id){
			// summary:
			//		Attach tooltip to specified node, if it's not already connected
			var node = dojo.byId(id);
			if(!node){ return; }
			if(node.id in this._nodeConnectionsById){ return; }//Already connected

			this._nodeConnectionsById[node.id] = [
				this.connect(node, "onmouseenter", "_onTargetMouseEnter"),
				this.connect(node, "onmouseleave", "_onTargetMouseLeave"),
				this.connect(node, "onfocus", "_onTargetFocus"),
				this.connect(node, "onblur", "_onTargetBlur")
			];
		},

		removeTarget: function(/*DOMNODE || String*/ node){
			// summary:
			//		Detach tooltip from specified node

			// map from DOMNode back to plain id string
			var id = node.id || node;

			if(id in this._nodeConnectionsById){
				dojo.forEach(this._nodeConnectionsById[id], this.disconnect, this);
				delete this._nodeConnectionsById[id];
			}
		},

		postCreate: function(){
			dojo.addClass(this.domNode,"dijitTooltipData");
		},

		startup: function(){
			this.inherited(arguments);

			// If this tooltip was created in a template, or for some other reason the specified connectId[s]
			// didn't exist during the widget's initialization, then connect now.
			var ids = this.connectId;
			dojo.forEach(dojo.isArrayLike(ids) ? ids : [ids], this.addTarget, this);
		},

		_onTargetMouseEnter: function(/*Event*/ e){
			// summary:
			//		Handler for mouseenter event on the target node
			// tags:
			//		private
			this._onHover(e);
		},

		_onTargetMouseLeave: function(/*Event*/ e){
			// summary:
			//		Handler for mouseleave event on the target node
			// tags:
			//		private
			this._onUnHover(e);
		},

		_onTargetFocus: function(/*Event*/ e){
			// summary:
			//		Handler for focus event on the target node
			// tags:
			//		private

			this._focus = true;
			this._onHover(e);
		},

		_onTargetBlur: function(/*Event*/ e){
			// summary:
			//		Handler for blur event on the target node
			// tags:
			//		private

			this._focus = false;
			this._onUnHover(e);
		},

		_onHover: function(/*Event*/ e){
			// summary:
			//		Despite the name of this method, it actually handles both hover and focus
			//		events on the target node, setting a timer to show the tooltip.
			// tags:
			//		private
			if(!this._showTimer){
				var target = e.target;
				this._showTimer = setTimeout(dojo.hitch(this, function(){this.open(target)}), this.showDelay);
			}
		},

		_onUnHover: function(/*Event*/ e){
			// summary:
			//		Despite the name of this method, it actually handles both mouseleave and blur
			//		events on the target node, hiding the tooltip.
			// tags:
			//		private

			// keep a tooltip open if the associated element still has focus (even though the
			// mouse moved away)
			if(this._focus){ return; }

			if(this._showTimer){
				clearTimeout(this._showTimer);
				delete this._showTimer;
			}
			this.close();
		},

		open: function(/*DomNode*/ target){
 			// summary:
			//		Display the tooltip; usually not called directly.
			// tags:
			//		private

			if(this._showTimer){
				clearTimeout(this._showTimer);
				delete this._showTimer;
			}
			dijit.showTooltip(this.label || this.domNode.innerHTML, target, this.position, !this.isLeftToRight());

			this._connectNode = target;
			this.onShow(target, this.position);
		},

		close: function(){
			// summary:
			//		Hide the tooltip or cancel timer for show of tooltip
			// tags:
			//		private

			if(this._connectNode){
				// if tooltip is currently shown
				dijit.hideTooltip(this._connectNode);
				delete this._connectNode;
				this.onHide();
			}
			if(this._showTimer){
				// if tooltip is scheduled to be shown (after a brief delay)
				clearTimeout(this._showTimer);
				delete this._showTimer;
			}
		},

		onShow: function(target, position){
			// summary:
			//		Called when the tooltip is shown
			// tags:
			//		callback
		},

		onHide: function(){
			// summary:
			//		Called when the tooltip is hidden
			// tags:
			//		callback
		},

		uninitialize: function(){
			this.close();
			this.inherited(arguments);
		}
	}
);

// dijit.Tooltip.defaultPosition: String[]
//		This variable controls the position of tooltips, if the position is not specified to
//		the Tooltip widget or *TextBox widget itself.  It's an array of strings with the following values:
//
//			* before: places tooltip to the left of the target node/widget, or to the right in
//			  the case of RTL scripts like Hebrew and Arabic
//			* after: places tooltip to the right of the target node/widget, or to the left in
//			  the case of RTL scripts like Hebrew and Arabic
//			* above: tooltip goes above target node
//			* below: tooltip goes below target node
//
//		The list is positions is tried, in order, until a position is found where the tooltip fits
//		within the viewport.
//
//		Be careful setting this parameter.  A value of "above" may work fine until the user scrolls
//		the screen so that there's no room above the target node.   Nodes with drop downs, like
//		DropDownButton or FilteringSelect, are especially problematic, in that you need to be sure
//		that the drop down and tooltip don't overlap, even when the viewport is scrolled so that there
//		is only room below (or above) the target node, but not both.
dijit.Tooltip.defaultPosition = ["after", "before"];

}

if(!dojo._hasResource["dijit.form.ValidationTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.ValidationTextBox"] = true;
dojo.provide("dijit.form.ValidationTextBox");








/*=====
	dijit.form.ValidationTextBox.__Constraints = function(){
		// locale: String
		//		locale used for validation, picks up value from this widget's lang attribute
		// _flags_: anything
		//		various flags passed to regExpGen function
		this.locale = "";
		this._flags_ = "";
	}
=====*/

dojo.declare(
	"dijit.form.ValidationTextBox",
	dijit.form.TextBox,
	{
		// summary:
		//		Base class for textbox widgets with the ability to validate content of various types and provide user feedback.
		// tags:
		//		protected

		templateString: dojo.cache("dijit.form", "templates/ValidationTextBox.html", "<div class=\"dijit dijitReset dijitInlineTable dijitLeft\"\n\tid=\"widget_${id}\" waiRole=\"presentation\"\n\t><div class='dijitReset dijitValidationContainer'\n\t\t><input class=\"dijitReset dijitInputField dijitValidationIcon dijitValidationInner\" value=\"&Chi; \" type=\"text\" tabIndex=\"-1\" readOnly waiRole=\"presentation\"\n\t/></div\n\t><div class=\"dijitReset dijitInputField dijitInputContainer\"\n\t\t><input class=\"dijitReset dijitInputInner\" dojoAttachPoint='textbox,focusNode' autocomplete=\"off\"\n\t\t\t${!nameAttrSetting} type='${type}'\n\t/></div\n></div>\n"),
		baseClass: "dijitTextBox dijitValidationTextBox",

		// required: Boolean
		//		User is required to enter data into this field.
		required: false,

		// promptMessage: String
		//		If defined, display this hint string immediately on focus to the textbox, if empty.
		//		Think of this like a tooltip that tells the user what to do, not an error message
		//		that tells the user what they've done wrong.
		//
		//		Message disappears when user starts typing.
		promptMessage: "",

		// invalidMessage: String
		// 		The message to display if value is invalid.
		//		The translated string value is read from the message file by default.
		// 		Set to "" to use the promptMessage instead.
		invalidMessage: "$_unset_$",

		// missingMessage: String
		// 		The message to display if value is empty and the field is required.
		//		The translated string value is read from the message file by default.
		// 		Set to "" to use the invalidMessage instead.
		missingMessage: "$_unset_$",

		// constraints: dijit.form.ValidationTextBox.__Constraints
		//		user-defined object needed to pass parameters to the validator functions
		constraints: {},

		// regExp: [extension protected] String
		//		regular expression string used to validate the input
		//		Do not specify both regExp and regExpGen
		regExp: ".*",

		regExpGen: function(/*dijit.form.ValidationTextBox.__Constraints*/constraints){
			// summary:
			//		Overridable function used to generate regExp when dependent on constraints.
			//		Do not specify both regExp and regExpGen.
			// tags:
			//		extension protected
			return this.regExp; // String
		},

		// state: [readonly] String
		//		Shows current state (ie, validation result) of input (Normal, Warning, or Error)
		state: "",

		// tooltipPosition: String[]
		//		See description of `dijit.Tooltip.defaultPosition` for details on this parameter.
		tooltipPosition: [],

		_setValueAttr: function(){
			// summary:
			//		Hook so attr('value', ...) works.
			this.inherited(arguments);
			this.validate(this._focused);
		},

		validator: function(/*anything*/value, /*dijit.form.ValidationTextBox.__Constraints*/constraints){
			// summary:
			//		Overridable function used to validate the text input against the regular expression.
			// tags:
			//		protected
			return (new RegExp("^(?:" + this.regExpGen(constraints) + ")"+(this.required?"":"?")+"$")).test(value) &&
				(!this.required || !this._isEmpty(value)) &&
				(this._isEmpty(value) || this.parse(value, constraints) !== undefined); // Boolean
		},

		_isValidSubset: function(){
			// summary:
			//		Returns true if the value is either already valid or could be made valid by appending characters.
			//		This is used for validation while the user [may be] still typing.
			return this.textbox.value.search(this._partialre) == 0;
		},

		isValid: function(/*Boolean*/ isFocused){
			// summary:
			//		Tests if value is valid.
			//		Can override with your own routine in a subclass.
			// tags:
			//		protected
			return this.validator(this.textbox.value, this.constraints);
		},

		_isEmpty: function(value){
			// summary:
			//		Checks for whitespace
			return /^\s*$/.test(value); // Boolean
		},

		getErrorMessage: function(/*Boolean*/ isFocused){
			// summary:
			//		Return an error message to show if appropriate
			// tags:
			//		protected
			return (this.required && this._isEmpty(this.textbox.value)) ? this.missingMessage : this.invalidMessage; // String
		},

		getPromptMessage: function(/*Boolean*/ isFocused){
			// summary:
			//		Return a hint message to show when widget is first focused
			// tags:
			//		protected
			return this.promptMessage; // String
		},

		_maskValidSubsetError: true,
		validate: function(/*Boolean*/ isFocused){
			// summary:
			//		Called by oninit, onblur, and onkeypress.
			// description:
			//		Show missing or invalid messages if appropriate, and highlight textbox field.
			// tags:
			//		protected
			var message = "";
			var isValid = this.disabled || this.isValid(isFocused);
			if(isValid){ this._maskValidSubsetError = true; }
			var isEmpty = this._isEmpty(this.textbox.value);
			var isValidSubset = !isValid && !isEmpty && isFocused && this._isValidSubset();
			this.state = ((isValid || ((!this._hasBeenBlurred || isFocused) && isEmpty) || isValidSubset) && this._maskValidSubsetError) ? "" : "Error";
			if(this.state == "Error"){ this._maskValidSubsetError = isFocused; } // we want the error to show up afer a blur and refocus
			this._setStateClass();
			dijit.setWaiState(this.focusNode, "invalid", isValid ? "false" : "true");
			if(isFocused){
				if(this.state == "Error"){
					message = this.getErrorMessage(true);
				}else{
					message = this.getPromptMessage(true); // show the prompt whever there's no error
				}
				this._maskValidSubsetError = true; // since we're focused, always mask warnings
			}
			this.displayMessage(message);
			return isValid;
		},

		// _message: String
		//		Currently displayed message
		_message: "",

		displayMessage: function(/*String*/ message){
			// summary:
			//		Overridable method to display validation errors/hints.
			//		By default uses a tooltip.
			// tags:
			//		extension
			if(this._message == message){ return; }
			this._message = message;
			dijit.hideTooltip(this.domNode);
			if(message){
				dijit.showTooltip(message, this.domNode, this.tooltipPosition, !this.isLeftToRight());
			}
		},

		_refreshState: function(){
			// Overrides TextBox._refreshState()
			this.validate(this._focused);
			this.inherited(arguments);
		},

		//////////// INITIALIZATION METHODS ///////////////////////////////////////

		constructor: function(){
			this.constraints = {};
		},

		_setConstraintsAttr: function(/* Object */ constraints){
			if(!constraints.locale && this.lang){
				constraints.locale = this.lang;
			}
			this.constraints = constraints;
			this._computePartialRE();
		},

		_computePartialRE: function(){
			var p = this.regExpGen(this.constraints);
			this.regExp = p;
			var partialre = "";
			// parse the regexp and produce a new regexp that matches valid subsets
			// if the regexp is .* then there's no use in matching subsets since everything is valid
			if(p != ".*"){ this.regExp.replace(/\\.|\[\]|\[.*?[^\\]{1}\]|\{.*?\}|\(\?[=:!]|./g,
				function (re){
					switch(re.charAt(0)){
						case '{':
						case '+':
						case '?':
						case '*':
						case '^':
						case '$':
						case '|':
						case '(':
							partialre += re;
							break;
						case ")":
							partialre += "|$)";
							break;
						 default:
							partialre += "(?:"+re+"|$)";
							break;
					}
				}
			);}
			try{ // this is needed for now since the above regexp parsing needs more test verification
				"".search(partialre);
			}catch(e){ // should never be here unless the original RE is bad or the parsing is bad
				partialre = this.regExp;
				console.warn('RegExp error in ' + this.declaredClass + ': ' + this.regExp);
			} // should never be here unless the original RE is bad or the parsing is bad
			this._partialre = "^(?:" + partialre + ")$";
		},

		postMixInProperties: function(){
			this.inherited(arguments);
			this.messages = dojo.i18n.getLocalization("dijit.form", "validate", this.lang);
			if(this.invalidMessage == "$_unset_$"){ this.invalidMessage = this.messages.invalidMessage; }
			if(!this.invalidMessage){ this.invalidMessage = this.promptMessage; }
			if(this.missingMessage == "$_unset_$"){ this.missingMessage = this.messages.missingMessage; }
			if(!this.missingMessage){ this.missingMessage = this.invalidMessage; }
			this._setConstraintsAttr(this.constraints); // this needs to happen now (and later) due to codependency on _set*Attr calls attachPoints
		},

		_setDisabledAttr: function(/*Boolean*/ value){
			this.inherited(arguments);	// call FormValueWidget._setDisabledAttr()
			this._refreshState();
		},

		_setRequiredAttr: function(/*Boolean*/ value){
			this.required = value;
			dijit.setWaiState(this.focusNode, "required", value);
			this._refreshState();
		},

		reset:function(){
			// Overrides dijit.form.TextBox.reset() by also
			// hiding errors about partial matches
			this._maskValidSubsetError = true;
			this.inherited(arguments);
		},

		_onBlur: function(){
			this.displayMessage('');
			this.inherited(arguments);
		}
	}
);

dojo.declare(
	"dijit.form.MappedTextBox",
	dijit.form.ValidationTextBox,
	{
		// summary:
		//		A dijit.form.ValidationTextBox subclass which provides a base class for widgets that have
		//		a visible formatted display value, and a serializable
		//		value in a hidden input field which is actually sent to the server.
		// description:
		//		The visible display may
		//		be locale-dependent and interactive.  The value sent to the server is stored in a hidden
		//		input field which uses the `name` attribute declared by the original widget.  That value sent
		//		to the server is defined by the dijit.form.MappedTextBox.serialize method and is typically
		//		locale-neutral.
		// tags:
		//		protected

		postMixInProperties: function(){
			this.inherited(arguments);

			// we want the name attribute to go to the hidden <input>, not the displayed <input>,
			// so override _FormWidget.postMixInProperties() setting of nameAttrSetting
			this.nameAttrSetting = "";
		},

		serialize: function(/*anything*/val, /*Object?*/options){
			// summary:
			//		Overridable function used to convert the attr('value') result to a canonical
			//		(non-localized) string.  For example, will print dates in ISO format, and
			//		numbers the same way as they are represented in javascript.
			// tags:
			//		protected extension
			return val.toString ? val.toString() : ""; // String
		},

		toString: function(){
			// summary:
			//		Returns widget as a printable string using the widget's value
			// tags:
			//		protected
			var val = this.filter(this.get('value')); // call filter in case value is nonstring and filter has been customized
			return val != null ? (typeof val == "string" ? val : this.serialize(val, this.constraints)) : ""; // String
		},

		validate: function(){
			// Overrides `dijit.form.TextBox.validate`
			this.valueNode.value = this.toString();
			return this.inherited(arguments);
		},

		buildRendering: function(){
			// Overrides `dijit._Templated.buildRendering`

			this.inherited(arguments);

			// Create a hidden <input> node with the serialized value used for submit
			// (as opposed to the displayed value).
			// Passing in name as markup rather than calling dojo.create() with an attrs argument
			// to make dojo.query(input[name=...]) work on IE. (see #8660)
			this.valueNode = dojo.place("<input type='hidden'" + (this.name ? " name='" + this.name + "'" : "") + ">", this.textbox, "after");
		},

		reset:function(){
			// Overrides `dijit.form.ValidationTextBox.reset` to
			// reset the hidden textbox value to ''
			this.valueNode.value = '';
			this.inherited(arguments);
		}
	}
);

/*=====
	dijit.form.RangeBoundTextBox.__Constraints = function(){
		// min: Number
		//		Minimum signed value.  Default is -Infinity
		// max: Number
		//		Maximum signed value.  Default is +Infinity
		this.min = min;
		this.max = max;
	}
=====*/

dojo.declare(
	"dijit.form.RangeBoundTextBox",
	dijit.form.MappedTextBox,
	{
		// summary:
		//		Base class for textbox form widgets which defines a range of valid values.

		// rangeMessage: String
		//		The message to display if value is out-of-range
		rangeMessage: "",

		/*=====
		// constraints: dijit.form.RangeBoundTextBox.__Constraints
		constraints: {},
		======*/

		rangeCheck: function(/*Number*/ primitive, /*dijit.form.RangeBoundTextBox.__Constraints*/ constraints){
			// summary:
			//		Overridable function used to validate the range of the numeric input value.
			// tags:
			//		protected
			return	("min" in constraints? (this.compare(primitive,constraints.min) >= 0) : true) &&
				("max" in constraints? (this.compare(primitive,constraints.max) <= 0) : true); // Boolean
		},

		isInRange: function(/*Boolean*/ isFocused){
			// summary:
			//		Tests if the value is in the min/max range specified in constraints
			// tags:
			//		protected
			return this.rangeCheck(this.get('value'), this.constraints);
		},

		_isDefinitelyOutOfRange: function(){
			// summary:
			//		Returns true if the value is out of range and will remain
			//		out of range even if the user types more characters
			var val = this.get('value');
			var isTooLittle = false;
			var isTooMuch = false;
			if("min" in this.constraints){
				var min = this.constraints.min;
				min = this.compare(val, ((typeof min == "number") && min >= 0 && val !=0) ? 0 : min);
				isTooLittle = (typeof min == "number") && min < 0;
			}
			if("max" in this.constraints){
				var max = this.constraints.max;
				max = this.compare(val, ((typeof max != "number") || max > 0) ? max : 0);
				isTooMuch = (typeof max == "number") && max > 0;
			}
			return isTooLittle || isTooMuch;
		},

		_isValidSubset: function(){
			// summary:
			//		Overrides `dijit.form.ValidationTextBox._isValidSubset`.
			//		Returns true if the input is syntactically valid, and either within
			//		range or could be made in range by more typing.
			return this.inherited(arguments) && !this._isDefinitelyOutOfRange();
		},

		isValid: function(/*Boolean*/ isFocused){
			// Overrides dijit.form.ValidationTextBox.isValid to check that the value is also in range.
			return this.inherited(arguments) &&
				((this._isEmpty(this.textbox.value) && !this.required) || this.isInRange(isFocused)); // Boolean
		},

		getErrorMessage: function(/*Boolean*/ isFocused){
			// Overrides dijit.form.ValidationTextBox.getErrorMessage to print "out of range" message if appropriate
			var v = this.get('value');
			if(v !== null && v !== '' && v !== undefined && (typeof v != "number" || !isNaN(v)) && !this.isInRange(isFocused)){ // don't check isInRange w/o a real value
				return this.rangeMessage; // String
			}
			return this.inherited(arguments);
		},

		postMixInProperties: function(){
			this.inherited(arguments);
			if(!this.rangeMessage){
				this.messages = dojo.i18n.getLocalization("dijit.form", "validate", this.lang);
				this.rangeMessage = this.messages.rangeMessage;
			}
		},

		_setConstraintsAttr: function(/* Object */ constraints){
			this.inherited(arguments);
			if(this.focusNode){ // not set when called from postMixInProperties
				if(this.constraints.min !== undefined){
					dijit.setWaiState(this.focusNode, "valuemin", this.constraints.min);
				}else{
					dijit.removeWaiState(this.focusNode, "valuemin");
				}
				if(this.constraints.max !== undefined){
					dijit.setWaiState(this.focusNode, "valuemax", this.constraints.max);
				}else{
					dijit.removeWaiState(this.focusNode, "valuemax");
				}
			}
		},

		_setValueAttr: function(/*Number*/ value, /*Boolean?*/ priorityChange){
			// summary:
			//		Hook so attr('value', ...) works.

			dijit.setWaiState(this.focusNode, "valuenow", value);
			this.inherited(arguments);
		}
	}
);

}

if(!dojo._hasResource["dijit.form.ComboBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.ComboBox"] = true;
dojo.provide("dijit.form.ComboBox");












dojo.declare(
	"dijit.form.ComboBoxMixin",
	null,
	{
		// summary:
		//		Implements the base functionality for `dijit.form.ComboBox`/`dijit.form.FilteringSelect`
		// description:
		//		All widgets that mix in dijit.form.ComboBoxMixin must extend `dijit.form._FormValueWidget`.
		// tags:
		//		protected

		// item: Object
		//		This is the item returned by the dojo.data.store implementation that
		//		provides the data for this ComboBox, it's the currently selected item.
		item: null,

		// pageSize: Integer
		//		Argument to data provider.
		//		Specifies number of search results per page (before hitting "next" button)
		pageSize: Infinity,

		// store: Object
		//		Reference to data provider object used by this ComboBox
		store: null,

		// fetchProperties: Object
		//		Mixin to the dojo.data store's fetch.
		//		For example, to set the sort order of the ComboBox menu, pass:
		//	|	{ sort: {attribute:"name",descending: true} }
		//		To override the default queryOptions so that deep=false, do:
		//	|	{ queryOptions: {ignoreCase: true, deep: false} }
		fetchProperties:{},

		// query: Object
		//		A query that can be passed to 'store' to initially filter the items,
		//		before doing further filtering based on `searchAttr` and the key.
		//		Any reference to the `searchAttr` is ignored.
		query: {},

		// autoComplete: Boolean
		//		If user types in a partial string, and then tab out of the `<input>` box,
		//		automatically copy the first entry displayed in the drop down list to
		//		the `<input>` field
		autoComplete: true,

		// highlightMatch: String
		// 		One of: "first", "all" or "none".
		//
		//		If the ComboBox/FilteringSelect opens with the search results and the searched
		//		string can be found, it will be highlighted.  If set to "all"
		//		then will probably want to change `queryExpr` parameter to '*${0}*'
		//
		//		Highlighting is only performed when `labelType` is "text", so as to not
		//		interfere with any HTML markup an HTML label might contain.
		highlightMatch: "first",

		// searchDelay: Integer
		//		Delay in milliseconds between when user types something and we start
		//		searching based on that value
		searchDelay: 100,

		// searchAttr: String
		//		Search for items in the data store where this attribute (in the item)
		//		matches what the user typed
		searchAttr: "name",

		// labelAttr: String?
		//		The entries in the drop down list come from this attribute in the
		//		dojo.data items.
		//		If not specified, the searchAttr attribute is used instead.
		labelAttr: "",

		// labelType: String
		//		Specifies how to interpret the labelAttr in the data store items.
		//		Can be "html" or "text".
		labelType: "text",

		// queryExpr: String
		//		This specifies what query ComboBox/FilteringSelect sends to the data store,
		//		based on what the user has typed.  Changing this expression will modify
		//		whether the drop down shows only exact matches, a "starting with" match,
		//		etc.   Use it in conjunction with highlightMatch.
		//		dojo.data query expression pattern.
		//		`${0}` will be substituted for the user text.
		//		`*` is used for wildcards.
		//		`${0}*` means "starts with", `*${0}*` means "contains", `${0}` means "is"
		queryExpr: "${0}*",

		// ignoreCase: Boolean
		//		Set true if the ComboBox/FilteringSelect should ignore case when matching possible items
		ignoreCase: true,

		// hasDownArrow: [const] Boolean
		//		Set this textbox to have a down arrow button, to display the drop down list.
		//		Defaults to true.
		hasDownArrow: true,

		templateString: dojo.cache("dijit.form", "templates/ComboBox.html", "<div class=\"dijit dijitReset dijitInlineTable dijitLeft\"\n\tid=\"widget_${id}\"\n\tdojoAttachPoint=\"comboNode\" waiRole=\"combobox\"\n\t><div class='dijitReset dijitRight dijitButtonNode dijitArrowButton dijitDownArrowButton dijitArrowButtonContainer'\n\t\tdojoAttachPoint=\"downArrowNode\" waiRole=\"presentation\"\n\t\tdojoAttachEvent=\"onmousedown:_onArrowMouseDown\"\n\t\t><input class=\"dijitReset dijitInputField dijitArrowButtonInner\" value=\"&#9660; \" type=\"text\" tabIndex=\"-1\" readOnly waiRole=\"presentation\"\n\t\t\t${_buttonInputDisabled}\n\t/></div\n\t><div class='dijitReset dijitValidationContainer'\n\t\t><input class=\"dijitReset dijitInputField dijitValidationIcon dijitValidationInner\" value=\"&Chi; \" type=\"text\" tabIndex=\"-1\" readOnly waiRole=\"presentation\"\n\t/></div\n\t><div class=\"dijitReset dijitInputField dijitInputContainer\"\n\t\t><input class='dijitReset dijitInputInner' ${!nameAttrSetting} type=\"text\" autocomplete=\"off\"\n\t\t\tdojoAttachEvent=\"onkeypress:_onKeyPress,compositionend\"\n\t\t\tdojoAttachPoint=\"textbox,focusNode\" waiRole=\"textbox\" waiState=\"haspopup-true,autocomplete-list\"\n\t/></div\n></div>\n"),

		baseClass: "dijitTextBox dijitComboBox",

		// Set classes like dijitDownArrowButtonHover depending on
		// mouse action over button node
		cssStateNodes: {
			"downArrowNode": "dijitDownArrowButton"
		},

		_getCaretPos: function(/*DomNode*/ element){
			// khtml 3.5.2 has selection* methods as does webkit nightlies from 2005-06-22
			var pos = 0;
			if(typeof(element.selectionStart) == "number"){
				// FIXME: this is totally borked on Moz < 1.3. Any recourse?
				pos = element.selectionStart;
			}else if(dojo.isIE){
				// in the case of a mouse click in a popup being handled,
				// then the dojo.doc.selection is not the textarea, but the popup
				// var r = dojo.doc.selection.createRange();
				// hack to get IE 6 to play nice. What a POS browser.
				var tr = dojo.doc.selection.createRange().duplicate();
				var ntr = element.createTextRange();
				tr.move("character",0);
				ntr.move("character",0);
				try{
					// If control doesnt have focus, you get an exception.
					// Seems to happen on reverse-tab, but can also happen on tab (seems to be a race condition - only happens sometimes).
					// There appears to be no workaround for this - googled for quite a while.
					ntr.setEndPoint("EndToEnd", tr);
					pos = String(ntr.text).replace(/\r/g,"").length;
				}catch(e){
					// If focus has shifted, 0 is fine for caret pos.
				}
			}
			return pos;
		},

		_setCaretPos: function(/*DomNode*/ element, /*Number*/ location){
			location = parseInt(location);
			dijit.selectInputText(element, location, location);
		},

		_setDisabledAttr: function(/*Boolean*/ value){
			// Additional code to set disabled state of ComboBox node.
			// Overrides _FormValueWidget._setDisabledAttr() or ValidationTextBox._setDisabledAttr().
			this.inherited(arguments);
			dijit.setWaiState(this.comboNode, "disabled", value);
		},

		_abortQuery: function(){
			// stop in-progress query
			if(this.searchTimer){
				clearTimeout(this.searchTimer);
				this.searchTimer = null;
			}
			if(this._fetchHandle){
				if(this._fetchHandle.abort){ this._fetchHandle.abort(); }
				this._fetchHandle = null;
			}
		},

		_onInput: function(/*Event*/ evt){
			// summary:
			//		Handles paste events
			if(!this.searchTimer && (evt.type == 'paste'/*IE|WebKit*/ || evt.type == 'input'/*Firefox*/) && this._lastInput != this.textbox.value){
				this.searchTimer = setTimeout(dojo.hitch(this, function(){
					this._onKeyPress({charOrCode: 229}); // fake IME key to cause a search
				}), 100); // long delay that will probably be preempted by keyboard input
			}
			this.inherited(arguments);
		},

		_onKeyPress: function(/*Event*/ evt){
			// summary:
			//		Handles keyboard events
			var key = evt.charOrCode;
			// except for cutting/pasting case - ctrl + x/v
			if(evt.altKey || ((evt.ctrlKey || evt.metaKey) && (key != 'x' && key != 'v')) || key == dojo.keys.SHIFT){
				return; // throw out weird key combinations and spurious events
			}
			var doSearch = false;
			var searchFunction = "_startSearchFromInput";
			var pw = this._popupWidget;
			var dk = dojo.keys;
			var highlighted = null;
			this._prev_key_backspace = false;
			this._abortQuery();
			if(this._isShowingNow){
				pw.handleKey(key);
				highlighted = pw.getHighlightedOption();
			}
			switch(key){
				case dk.PAGE_DOWN:
				case dk.DOWN_ARROW:
				case dk.PAGE_UP:
				case dk.UP_ARROW:
					if(!this._isShowingNow){
						doSearch = true;
						searchFunction = "_startSearchAll";
					}else{
						this._announceOption(highlighted);
					}
					dojo.stopEvent(evt);
					break;

				case dk.ENTER:
					// prevent submitting form if user presses enter. Also
					// prevent accepting the value if either Next or Previous
					// are selected
					if(highlighted){
						// only stop event on prev/next
						if(highlighted == pw.nextButton){
							this._nextSearch(1);
							dojo.stopEvent(evt);
							break;
						}else if(highlighted == pw.previousButton){
							this._nextSearch(-1);
							dojo.stopEvent(evt);
							break;
						}
					}else{
						// Update 'value' (ex: KY) according to currently displayed text
						this._setBlurValue(); // set value if needed
						this._setCaretPos(this.focusNode, this.focusNode.value.length); // move cursor to end and cancel highlighting
					}
					// default case:
					// prevent submit, but allow event to bubble
					evt.preventDefault();
					// fall through

				case dk.TAB:
					var newvalue = this.get('displayedValue');
					//	if the user had More Choices selected fall into the
					//	_onBlur handler
					if(pw && (
						newvalue == pw._messages["previousMessage"] ||
						newvalue == pw._messages["nextMessage"])
					){
						break;
					}
					if(highlighted){
						this._selectOption();
					}
					if(this._isShowingNow){
						this._lastQuery = null; // in case results come back later
						this._hideResultList();
					}
					break;

				case ' ':
					if(highlighted){
						dojo.stopEvent(evt);
						this._selectOption();
						this._hideResultList();
					}else{
						doSearch = true;
					}
					break;

				case dk.ESCAPE:
					if(this._isShowingNow){
						dojo.stopEvent(evt);
						this._hideResultList();
					}
					break;

				case dk.DELETE:
				case dk.BACKSPACE:
					this._prev_key_backspace = true;
					doSearch = true;
					break;

				default:
					// Non char keys (F1-F12 etc..)  shouldn't open list.
					// Ascii characters and IME input (Chinese, Japanese etc.) should.
					// On IE and safari, IME input produces keycode == 229, and we simulate
					// it on firefox by attaching to compositionend event (see compositionend method)
					doSearch = typeof key == 'string' || key == 229;
			}
			if(doSearch){
				// need to wait a tad before start search so that the event
				// bubbles through DOM and we have value visible
				this.item = undefined; // undefined means item needs to be set
				this.searchTimer = setTimeout(dojo.hitch(this, searchFunction),1);
			}
		},

		_autoCompleteText: function(/*String*/ text){
			// summary:
			// 		Fill in the textbox with the first item from the drop down
			// 		list, and highlight the characters that were
			// 		auto-completed. For example, if user typed "CA" and the
			// 		drop down list appeared, the textbox would be changed to
			// 		"California" and "ifornia" would be highlighted.

			var fn = this.focusNode;

			// IE7: clear selection so next highlight works all the time
			dijit.selectInputText(fn, fn.value.length);
			// does text autoComplete the value in the textbox?
			var caseFilter = this.ignoreCase? 'toLowerCase' : 'substr';
			if(text[caseFilter](0).indexOf(this.focusNode.value[caseFilter](0)) == 0){
				var cpos = this._getCaretPos(fn);
				// only try to extend if we added the last character at the end of the input
				if((cpos+1) > fn.value.length){
					// only add to input node as we would overwrite Capitalisation of chars
					// actually, that is ok
					fn.value = text;//.substr(cpos);
					// visually highlight the autocompleted characters
					dijit.selectInputText(fn, cpos);
				}
			}else{
				// text does not autoComplete; replace the whole value and highlight
				fn.value = text;
				dijit.selectInputText(fn);
			}
		},

		_openResultList: function(/*Object*/ results, /*Object*/ dataObject){
			this._fetchHandle = null;
			if(	this.disabled ||
				this.readOnly ||
				(dataObject.query[this.searchAttr] != this._lastQuery)
			){
				return;
			}
			this._popupWidget.clearResultList();
			if(!results.length && !this._maxOptions){ // this condition needs to match !this._isvalid set in FilteringSelect::_openResultList
				this._hideResultList();
				return;
			}


			// Fill in the textbox with the first item from the drop down list,
			// and highlight the characters that were auto-completed. For
			// example, if user typed "CA" and the drop down list appeared, the
			// textbox would be changed to "California" and "ifornia" would be
			// highlighted.

			dataObject._maxOptions = this._maxOptions;
			var nodes = this._popupWidget.createOptions(
				results,
				dataObject,
				dojo.hitch(this, "_getMenuLabelFromItem")
			);

			// show our list (only if we have content, else nothing)
			this._showResultList();

			// #4091:
			//		tell the screen reader that the paging callback finished by
			//		shouting the next choice
			if(dataObject.direction){
				if(1 == dataObject.direction){
					this._popupWidget.highlightFirstOption();
				}else if(-1 == dataObject.direction){
					this._popupWidget.highlightLastOption();
				}
				this._announceOption(this._popupWidget.getHighlightedOption());
			}else if(this.autoComplete && !this._prev_key_backspace /*&& !dataObject.direction*/
				// when the user clicks the arrow button to show the full list,
				// startSearch looks for "*".
				// it does not make sense to autocomplete
				// if they are just previewing the options available.
				&& !/^[*]+$/.test(dataObject.query[this.searchAttr])){
					this._announceOption(nodes[1]); // 1st real item
			}
		},

		_showResultList: function(){
			this._hideResultList();
			// hide the tooltip
			this.displayMessage("");

			// Position the list and if it's too big to fit on the screen then
			// size it to the maximum possible height
			// Our dear friend IE doesnt take max-height so we need to
			// calculate that on our own every time

			// TODO: want to redo this, see
			//		http://trac.dojotoolkit.org/ticket/3272
			//	and
			//		http://trac.dojotoolkit.org/ticket/4108


			// natural size of the list has changed, so erase old
			// width/height settings, which were hardcoded in a previous
			// call to this function (via dojo.marginBox() call)
			dojo.style(this._popupWidget.domNode, {width: "", height: ""});

			var best = this.open();
			// #3212:
			//		only set auto scroll bars if necessary prevents issues with
			//		scroll bars appearing when they shouldn't when node is made
			//		wider (fractional pixels cause this)
			var popupbox = dojo.marginBox(this._popupWidget.domNode);
			this._popupWidget.domNode.style.overflow =
				((best.h == popupbox.h) && (best.w == popupbox.w)) ? "hidden" : "auto";
			// #4134:
			//		borrow TextArea scrollbar test so content isn't covered by
			//		scrollbar and horizontal scrollbar doesn't appear
			var newwidth = best.w;
			if(best.h < this._popupWidget.domNode.scrollHeight){
				newwidth += 16;
			}
			dojo.marginBox(this._popupWidget.domNode, {
				h: best.h,
				w: Math.max(newwidth, this.domNode.offsetWidth)
			});
			
			// If we increased the width of drop down to match the width of ComboBox.domNode,
			// then need to reposition the drop down (wrapper) so (all of) the drop down still
			// appears underneath the ComboBox.domNode
			if(newwidth < this.domNode.offsetWidth){
				this._popupWidget.domNode.parentNode.style.left = dojo.position(this.domNode, true).x + "px";
			}

			dijit.setWaiState(this.comboNode, "expanded", "true");
		},

		_hideResultList: function(){
			this._abortQuery();
			if(this._isShowingNow){
				dijit.popup.close(this._popupWidget);
				this._isShowingNow=false;
				dijit.setWaiState(this.comboNode, "expanded", "false");
				dijit.removeWaiState(this.focusNode,"activedescendant");
			}
		},

		_setBlurValue: function(){
			// if the user clicks away from the textbox OR tabs away, set the
			// value to the textbox value
			// #4617:
			//		if value is now more choices or previous choices, revert
			//		the value
			var newvalue = this.get('displayedValue');
			var pw = this._popupWidget;
			if(pw && (
				newvalue == pw._messages["previousMessage"] ||
				newvalue == pw._messages["nextMessage"]
				)
			){
				this._setValueAttr(this._lastValueReported, true);
			}else if(typeof this.item == "undefined"){
				// Update 'value' (ex: KY) according to currently displayed text
				this.item = null;
				this.set('displayedValue', newvalue);
			}else{
				if(this.value != this._lastValueReported){
					dijit.form._FormValueWidget.prototype._setValueAttr.call(this, this.value, true);
				}
				this._refreshState();
			}
		},

		_onBlur: function(){
			// summary:
			//		Called magically when focus has shifted away from this widget and it's drop down
			this._hideResultList();
			this.inherited(arguments);
		},

		_setItemAttr: function(/*item*/ item, /*Boolean?*/ priorityChange, /*String?*/ displayedValue){
			// summary:
			//              Set the displayed valued in the input box, and the hidden value
			//              that gets submitted, based on a dojo.data store item.
			// description:
			//              Users shouldn't call this function; they should be calling
			//              attr('item', value)
			// tags:
			//              private
			if(!displayedValue){ displayedValue = this.labelFunc(item, this.store); }
			this.value = this._getValueField() != this.searchAttr? this.store.getIdentity(item) : displayedValue;
			this.item = item;
			dijit.form.ComboBox.superclass._setValueAttr.call(this, this.value, priorityChange, displayedValue);
		},

		_announceOption: function(/*Node*/ node){
			// summary:
			//		a11y code that puts the highlighted option in the textbox.
			//		This way screen readers will know what is happening in the
			//		menu.

			if(!node){
				return;
			}
			// pull the text value from the item attached to the DOM node
			var newValue;
			if(node == this._popupWidget.nextButton ||
				node == this._popupWidget.previousButton){
				newValue = node.innerHTML;
				this.item = undefined;
				this.value = '';
			}else{
				newValue = this.labelFunc(node.item, this.store);
				this.set('item', node.item, false, newValue);
			}
			// get the text that the user manually entered (cut off autocompleted text)
			this.focusNode.value = this.focusNode.value.substring(0, this._lastInput.length);
			// set up ARIA activedescendant
			dijit.setWaiState(this.focusNode, "activedescendant", dojo.attr(node, "id"));
			// autocomplete the rest of the option to announce change
			this._autoCompleteText(newValue);
		},

		_selectOption: function(/*Event*/ evt){
			// summary:
			//		Menu callback function, called when an item in the menu is selected.
			if(evt){
				this._announceOption(evt.target);
			}
			this._hideResultList();
			this._setCaretPos(this.focusNode, this.focusNode.value.length);
			dijit.form._FormValueWidget.prototype._setValueAttr.call(this, this.value, true); // set this.value and fire onChange
		},

		_onArrowMouseDown: function(evt){
			// summary:
			//		Callback when arrow is clicked
			if(this.disabled || this.readOnly){
				return;
			}
			dojo.stopEvent(evt);
			this.focus();
			if(this._isShowingNow){
				this._hideResultList();
			}else{
				// forces full population of results, if they click
				// on the arrow it means they want to see more options
				this._startSearchAll();
			}
		},

		_startSearchAll: function(){
			this._startSearch('');
		},

		_startSearchFromInput: function(){
			this._startSearch(this.focusNode.value.replace(/([\\\*\?])/g, "\\$1"));
		},

		_getQueryString: function(/*String*/ text){
			return dojo.string.substitute(this.queryExpr, [text]);
		},

		_startSearch: function(/*String*/ key){
			if(!this._popupWidget){
				var popupId = this.id + "_popup";
				this._popupWidget = new dijit.form._ComboBoxMenu({
					onChange: dojo.hitch(this, this._selectOption),
					id: popupId,
					dir: this.dir
				});
				dijit.removeWaiState(this.focusNode,"activedescendant");
				dijit.setWaiState(this.textbox,"owns",popupId); // associate popup with textbox
			}
			// create a new query to prevent accidentally querying for a hidden
			// value from FilteringSelect's keyField
			var query = dojo.clone(this.query); // #5970
			this._lastInput = key; // Store exactly what was entered by the user.
			this._lastQuery = query[this.searchAttr] = this._getQueryString(key);
			// #5970: set _lastQuery, *then* start the timeout
			// otherwise, if the user types and the last query returns before the timeout,
			// _lastQuery won't be set and their input gets rewritten
			this.searchTimer=setTimeout(dojo.hitch(this, function(query, _this){
				this.searchTimer = null;
				var fetch = {
					queryOptions: {
						ignoreCase: this.ignoreCase,
						deep: true
					},
					query: query,
					onBegin: dojo.hitch(this, "_setMaxOptions"),
					onComplete: dojo.hitch(this, "_openResultList"),
					onError: function(errText){
						_this._fetchHandle = null;
						console.error('dijit.form.ComboBox: ' + errText);
						dojo.hitch(_this, "_hideResultList")();
					},
					start: 0,
					count: this.pageSize
				};
				dojo.mixin(fetch, _this.fetchProperties);
				this._fetchHandle = _this.store.fetch(fetch);

				var nextSearch = function(dataObject, direction){
					dataObject.start += dataObject.count*direction;
					// #4091:
					//		tell callback the direction of the paging so the screen
					//		reader knows which menu option to shout
					dataObject.direction = direction;
					this._fetchHandle = this.store.fetch(dataObject);
				};
				this._nextSearch = this._popupWidget.onPage = dojo.hitch(this, nextSearch, this._fetchHandle);
			}, query, this), this.searchDelay);
		},

		_setMaxOptions: function(size, request){
			 this._maxOptions = size;
		},

		_getValueField: function(){
			// summmary:
			//		Helper for postMixInProperties() to set this.value based on data inlined into the markup.
			//		Returns the attribute name in the item (in dijit.form._ComboBoxDataStore) to use as the value.
			return this.searchAttr;
		},

		/////////////// Event handlers /////////////////////

		// FIXME: For 2.0, rename to "_compositionEnd"
		compositionend: function(/*Event*/ evt){
			// summary:
			//		When inputting characters using an input method, such as
			//		Asian languages, it will generate this event instead of
			//		onKeyDown event.
			//		Note: this event is only triggered in FF (not in IE/safari)
			// tags:
			//		private

			// 229 is the code produced by IE and safari while pressing keys during
			// IME input mode
			this._onKeyPress({charOrCode: 229});
		},

		//////////// INITIALIZATION METHODS ///////////////////////////////////////

		constructor: function(){
			this.query={};
			this.fetchProperties={};
		},

		postMixInProperties: function(){
			if(!this.store){
				var srcNodeRef = this.srcNodeRef;

				// if user didn't specify store, then assume there are option tags
				this.store = new dijit.form._ComboBoxDataStore(srcNodeRef);

				// if there is no value set and there is an option list, set
				// the value to the first value to be consistent with native
				// Select

				// Firefox and Safari set value
				// IE6 and Opera set selectedIndex, which is automatically set
				// by the selected attribute of an option tag
				// IE6 does not set value, Opera sets value = selectedIndex
				if(!("value" in this.params)){
					var item = this.store.fetchSelectedItem();
					if(item){
						var valueField = this._getValueField();
						this.value = valueField != this.searchAttr? this.store.getValue(item, valueField) : this.labelFunc(item, this.store);
					}
				}
			}
			this.inherited(arguments);
		},

		postCreate: function(){
			// summary:
			//		Subclasses must call this method from their postCreate() methods
			// tags:
			//		protected

			if(!this.hasDownArrow){
				this.downArrowNode.style.display = "none";
			}

			// find any associated label element and add to ComboBox node.
			var label=dojo.query('label[for="'+this.id+'"]');
			if(label.length){
				label[0].id = (this.id+"_label");
				var cn=this.comboNode;
				dijit.setWaiState(cn, "labelledby", label[0].id);

			}
			this.inherited(arguments);
		},

		uninitialize: function(){
			if(this._popupWidget && !this._popupWidget._destroyed){
				this._hideResultList();
				this._popupWidget.destroy();
			}
			this.inherited(arguments);
		},

		_getMenuLabelFromItem: function(/*Item*/ item){
			var label = this.labelAttr? this.store.getValue(item, this.labelAttr) : this.labelFunc(item, this.store);
			var labelType = this.labelType;
			// If labelType is not "text" we don't want to screw any markup ot whatever.
			if(this.highlightMatch != "none" && this.labelType == "text" && this._lastInput){
				label = this.doHighlight(label, this._escapeHtml(this._lastInput));
				labelType = "html";
			}
			return {html: labelType == "html", label: label};
		},

		doHighlight: function(/*String*/label, /*String*/find){
			// summary:
			//		Highlights the string entered by the user in the menu.  By default this
			//		highlights the first occurence found. Override this method
			//		to implement your custom highlighing.
			// tags:
			//		protected

			// Add greedy when this.highlightMatch == "all"
			var modifiers = "i"+(this.highlightMatch == "all"?"g":"");
			var escapedLabel = this._escapeHtml(label);
			find = dojo.regexp.escapeString(find); // escape regexp special chars
			var ret = escapedLabel.replace(new RegExp("(^|\\s)("+ find +")", modifiers),
					'$1<span class="dijitComboBoxHighlightMatch">$2</span>');
			return ret;// returns String, (almost) valid HTML (entities encoded)
		},

		_escapeHtml: function(/*string*/str){
			// TODO Should become dojo.html.entities(), when exists use instead
			// summary:
			//		Adds escape sequences for special characters in XML: &<>"'
			str = String(str).replace(/&/gm, "&amp;").replace(/</gm, "&lt;")
				.replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
			return str; // string
		},

		open: function(){
			// summary:
			//		Opens the drop down menu.  TODO: rename to _open.
			// tags:
			//		private
			this._isShowingNow=true;
			return dijit.popup.open({
				popup: this._popupWidget,
				around: this.domNode,
				parent: this
			});
		},

		reset: function(){
			// Overrides the _FormWidget.reset().
			// Additionally reset the .item (to clean up).
			this.item = null;
			this.inherited(arguments);
		},

		labelFunc: function(/*item*/ item, /*dojo.data.store*/ store){
			// summary:
			//              Computes the label to display based on the dojo.data store item.
			// returns:
			//              The label that the ComboBox should display
			// tags:
			//              private

			// Use toString() because XMLStore returns an XMLItem whereas this
			// method is expected to return a String (#9354)
			return store.getValue(item, this.searchAttr).toString(); // String
		}
	}
);

dojo.declare(
	"dijit.form._ComboBoxMenu",
	[dijit._Widget, dijit._Templated, dijit._CssStateMixin],
	{
		// summary:
		//		Focus-less menu for internal use in `dijit.form.ComboBox`
		// tags:
		//		private

		templateString: "<ul class='dijitReset dijitMenu' dojoAttachEvent='onmousedown:_onMouseDown,onmouseup:_onMouseUp,onmouseover:_onMouseOver,onmouseout:_onMouseOut' tabIndex='-1' style='overflow: \"auto\"; overflow-x: \"hidden\";'>"
				+"<li class='dijitMenuItem dijitMenuPreviousButton' dojoAttachPoint='previousButton' waiRole='option'></li>"
				+"<li class='dijitMenuItem dijitMenuNextButton' dojoAttachPoint='nextButton' waiRole='option'></li>"
			+"</ul>",

		// _messages: Object
		//		Holds "next" and "previous" text for paging buttons on drop down
		_messages: null,
		
		baseClass: "dijitComboBoxMenu",

		postMixInProperties: function(){
			this._messages = dojo.i18n.getLocalization("dijit.form", "ComboBox", this.lang);
			this.inherited(arguments);
		},

		_setValueAttr: function(/*Object*/ value){
			this.value = value;
			this.onChange(value);
		},

		// stubs
		onChange: function(/*Object*/ value){
			// summary:
			//		Notifies ComboBox/FilteringSelect that user clicked an option in the drop down menu.
			//		Probably should be called onSelect.
			// tags:
			//		callback
		},
		onPage: function(/*Number*/ direction){
			// summary:
			//		Notifies ComboBox/FilteringSelect that user clicked to advance to next/previous page.
			// tags:
			//		callback
		},

		postCreate: function(){
			// fill in template with i18n messages
			this.previousButton.innerHTML = this._messages["previousMessage"];
			this.nextButton.innerHTML = this._messages["nextMessage"];
			this.inherited(arguments);
		},

		onClose: function(){
			// summary:
			//		Callback from dijit.popup code to this widget, notifying it that it closed
			// tags:
			//		private
			this._blurOptionNode();
		},

		_createOption: function(/*Object*/ item, labelFunc){
			// summary:
			//		Creates an option to appear on the popup menu subclassed by
			//		`dijit.form.FilteringSelect`.

			var labelObject = labelFunc(item);
			var menuitem = dojo.doc.createElement("li");
			dijit.setWaiRole(menuitem, "option");
			if(labelObject.html){
				menuitem.innerHTML = labelObject.label;
			}else{
				menuitem.appendChild(
					dojo.doc.createTextNode(labelObject.label)
				);
			}
			// #3250: in blank options, assign a normal height
			if(menuitem.innerHTML == ""){
				menuitem.innerHTML = "&nbsp;";
			}
			menuitem.item=item;
			return menuitem;
		},

		createOptions: function(results, dataObject, labelFunc){
			// summary:
			//		Fills in the items in the drop down list
			// results:
			//		Array of dojo.data items
			// dataObject:
			//		dojo.data store
			// labelFunc:
			//		Function to produce a label in the drop down list from a dojo.data item

			//this._dataObject=dataObject;
			//this._dataObject.onComplete=dojo.hitch(comboBox, comboBox._openResultList);
			// display "Previous . . ." button
			this.previousButton.style.display = (dataObject.start == 0) ? "none" : "";
			dojo.attr(this.previousButton, "id", this.id + "_prev");
			// create options using _createOption function defined by parent
			// ComboBox (or FilteringSelect) class
			// #2309:
			//		iterate over cache nondestructively
			dojo.forEach(results, function(item, i){
				var menuitem = this._createOption(item, labelFunc);
				menuitem.className = "dijitReset dijitMenuItem" +
					(this.isLeftToRight() ? "" : " dijitMenuItemRtl");
				dojo.attr(menuitem, "id", this.id + i);
				this.domNode.insertBefore(menuitem, this.nextButton);
			}, this);
			// display "Next . . ." button
			var displayMore = false;
			//Try to determine if we should show 'more'...
			if(dataObject._maxOptions && dataObject._maxOptions != -1){
				if((dataObject.start + dataObject.count) < dataObject._maxOptions){
					displayMore = true;
				}else if((dataObject.start + dataObject.count) > dataObject._maxOptions && dataObject.count == results.length){
					//Weird return from a datastore, where a start + count > maxOptions
					// implies maxOptions isn't really valid and we have to go into faking it.
					//And more or less assume more if count == results.length
					displayMore = true;
				}
			}else if(dataObject.count == results.length){
				//Don't know the size, so we do the best we can based off count alone.
				//So, if we have an exact match to count, assume more.
				displayMore = true;
			}

			this.nextButton.style.display = displayMore ? "" : "none";
			dojo.attr(this.nextButton,"id", this.id + "_next");
			return this.domNode.childNodes;
		},

		clearResultList: function(){
			// summary:
			//		Clears the entries in the drop down list, but of course keeps the previous and next buttons.
			while(this.domNode.childNodes.length>2){
				this.domNode.removeChild(this.domNode.childNodes[this.domNode.childNodes.length-2]);
			}
		},

		_onMouseDown: function(/*Event*/ evt){
			dojo.stopEvent(evt);
		},

		_onMouseUp: function(/*Event*/ evt){
			if(evt.target === this.domNode || !this._highlighted_option){
				return;
			}else if(evt.target == this.previousButton){
				this.onPage(-1);
			}else if(evt.target == this.nextButton){
				this.onPage(1);
			}else{
				var tgt = evt.target;
				// while the clicked node is inside the div
				while(!tgt.item){
					// recurse to the top
					tgt = tgt.parentNode;
				}
				this._setValueAttr({ target: tgt }, true);
			}
		},

		_onMouseOver: function(/*Event*/ evt){
			if(evt.target === this.domNode){ return; }
			var tgt = evt.target;
			if(!(tgt == this.previousButton || tgt == this.nextButton)){
				// while the clicked node is inside the div
				while(!tgt.item){
					// recurse to the top
					tgt = tgt.parentNode;
				}
			}
			this._focusOptionNode(tgt);
		},

		_onMouseOut: function(/*Event*/ evt){
			if(evt.target === this.domNode){ return; }
			this._blurOptionNode();
		},

		_focusOptionNode: function(/*DomNode*/ node){
			// summary:
			//		Does the actual highlight.
			if(this._highlighted_option != node){
				this._blurOptionNode();
				this._highlighted_option = node;
				dojo.addClass(this._highlighted_option, "dijitMenuItemSelected");
			}
		},

		_blurOptionNode: function(){
			// summary:
			//		Removes highlight on highlighted option.
			if(this._highlighted_option){
				dojo.removeClass(this._highlighted_option, "dijitMenuItemSelected");
				this._highlighted_option = null;
			}
		},

		_highlightNextOption: function(){
			// summary:
			// 		Highlight the item just below the current selection.
			// 		If nothing selected, highlight first option.

			// because each press of a button clears the menu,
			// the highlighted option sometimes becomes detached from the menu!
			// test to see if the option has a parent to see if this is the case.
			if(!this.getHighlightedOption()){
				var fc = this.domNode.firstChild;
				this._focusOptionNode(fc.style.display == "none" ? fc.nextSibling : fc);
			}else{
				var ns = this._highlighted_option.nextSibling;
				if(ns && ns.style.display != "none"){
					this._focusOptionNode(ns);
				}else{
					this.highlightFirstOption();
				}
			}
			// scrollIntoView is called outside of _focusOptionNode because in IE putting it inside causes the menu to scroll up on mouseover
			dojo.window.scrollIntoView(this._highlighted_option);
		},

		highlightFirstOption: function(){
			// summary:
			// 		Highlight the first real item in the list (not Previous Choices).
			var first = this.domNode.firstChild;
			var second = first.nextSibling;
			this._focusOptionNode(second.style.display == "none" ? first : second); // remotely possible that Previous Choices is the only thing in the list
			dojo.window.scrollIntoView(this._highlighted_option);
		},

		highlightLastOption: function(){
			// summary:
			// 		Highlight the last real item in the list (not More Choices).
			this._focusOptionNode(this.domNode.lastChild.previousSibling);
			dojo.window.scrollIntoView(this._highlighted_option);
		},

		_highlightPrevOption: function(){
			// summary:
			// 		Highlight the item just above the current selection.
			// 		If nothing selected, highlight last option (if
			// 		you select Previous and try to keep scrolling up the list).
			if(!this.getHighlightedOption()){
				var lc = this.domNode.lastChild;
				this._focusOptionNode(lc.style.display == "none" ? lc.previousSibling : lc);
			}else{
				var ps = this._highlighted_option.previousSibling;
				if(ps && ps.style.display != "none"){
					this._focusOptionNode(ps);
				}else{
					this.highlightLastOption();
				}
			}
			dojo.window.scrollIntoView(this._highlighted_option);
		},

		_page: function(/*Boolean*/ up){
			// summary:
			//		Handles page-up and page-down keypresses

			var scrollamount = 0;
			var oldscroll = this.domNode.scrollTop;
			var height = dojo.style(this.domNode, "height");
			// if no item is highlighted, highlight the first option
			if(!this.getHighlightedOption()){
				this._highlightNextOption();
			}
			while(scrollamount<height){
				if(up){
					// stop at option 1
					if(!this.getHighlightedOption().previousSibling ||
						this._highlighted_option.previousSibling.style.display == "none"){
						break;
					}
					this._highlightPrevOption();
				}else{
					// stop at last option
					if(!this.getHighlightedOption().nextSibling ||
						this._highlighted_option.nextSibling.style.display == "none"){
						break;
					}
					this._highlightNextOption();
				}
				// going backwards
				var newscroll=this.domNode.scrollTop;
				scrollamount+=(newscroll-oldscroll)*(up ? -1:1);
				oldscroll=newscroll;
			}
		},

		pageUp: function(){
			// summary:
			//		Handles pageup keypress.
			//		TODO: just call _page directly from handleKey().
			// tags:
			//		private
			this._page(true);
		},

		pageDown: function(){
			// summary:
			//		Handles pagedown keypress.
			//		TODO: just call _page directly from handleKey().
			// tags:
			//		private
			this._page(false);
		},

		getHighlightedOption: function(){
			// summary:
			//		Returns the highlighted option.
			var ho = this._highlighted_option;
			return (ho && ho.parentNode) ? ho : null;
		},

		handleKey: function(key){
			switch(key){
				case dojo.keys.DOWN_ARROW:
					this._highlightNextOption();
					break;
				case dojo.keys.PAGE_DOWN:
					this.pageDown();
					break;
				case dojo.keys.UP_ARROW:
					this._highlightPrevOption();
					break;
				case dojo.keys.PAGE_UP:
					this.pageUp();
					break;
			}
		}
	}
);

dojo.declare(
	"dijit.form.ComboBox",
	[dijit.form.ValidationTextBox, dijit.form.ComboBoxMixin],
	{
		// summary:
		//		Auto-completing text box, and base class for dijit.form.FilteringSelect.
		//
		// description:
		//		The drop down box's values are populated from an class called
		//		a data provider, which returns a list of values based on the characters
		//		that the user has typed into the input box.
		//		If OPTION tags are used as the data provider via markup,
		//		then the OPTION tag's child text node is used as the widget value
		//		when selected.  The OPTION tag's value attribute is ignored.
		//		To set the default value when using OPTION tags, specify the selected
		//		attribute on 1 of the child OPTION tags.
		//
		//		Some of the options to the ComboBox are actually arguments to the data
		//		provider.

		_setValueAttr: function(/*String*/ value, /*Boolean?*/ priorityChange, /*String?*/ displayedValue){
			// summary:
			//		Hook so attr('value', value) works.
			// description:
			//		Sets the value of the select.
			this.item = null; // value not looked up in store
			if(!value){ value = ''; } // null translates to blank
			dijit.form.ValidationTextBox.prototype._setValueAttr.call(this, value, priorityChange, displayedValue);
		}
	}
);

dojo.declare("dijit.form._ComboBoxDataStore", null, {
	// summary:
	//		Inefficient but small data store specialized for inlined `dijit.form.ComboBox` data
	//
	// description:
	//		Provides a store for inlined data like:
	//
	//	|	<select>
	//	|		<option value="AL">Alabama</option>
	//	|		...
	//
	//		Actually. just implements the subset of dojo.data.Read/Notification
	//		needed for ComboBox and FilteringSelect to work.
	//
	//		Note that an item is just a pointer to the <option> DomNode.

	constructor: function( /*DomNode*/ root){
		this.root = root;
		if(root.tagName != "SELECT" && root.firstChild){
			root = dojo.query("select", root);
			if(root.length > 0){ // SELECT is a child of srcNodeRef
				root = root[0];
			}else{ // no select, so create 1 to parent the option tags to define selectedIndex
				this.root.innerHTML = "<SELECT>"+this.root.innerHTML+"</SELECT>";
				root = this.root.firstChild;
			}
			this.root = root;
		}
		dojo.query("> option", root).forEach(function(node){
			//	TODO: this was added in #3858 but unclear why/if it's needed;  doesn't seem to be.
			//	If it is needed then can we just hide the select itself instead?
			//node.style.display="none";
			node.innerHTML = dojo.trim(node.innerHTML);
		});

	},

	getValue: function(	/* item */ item,
						/* attribute-name-string */ attribute,
						/* value? */ defaultValue){
		return (attribute == "value") ? item.value : (item.innerText || item.textContent || '');
	},

	isItemLoaded: function(/* anything */ something){
		return true;
	},

	getFeatures: function(){
		return {"dojo.data.api.Read": true, "dojo.data.api.Identity": true};
	},

	_fetchItems: function(	/* Object */ args,
							/* Function */ findCallback,
							/* Function */ errorCallback){
		// summary:
		//		See dojo.data.util.simpleFetch.fetch()
		if(!args.query){ args.query = {}; }
		if(!args.query.name){ args.query.name = ""; }
		if(!args.queryOptions){ args.queryOptions = {}; }
		var matcher = dojo.data.util.filter.patternToRegExp(args.query.name, args.queryOptions.ignoreCase),
			items = dojo.query("> option", this.root).filter(function(option){
				return (option.innerText || option.textContent || '').match(matcher);
			} );
		if(args.sort){
			items.sort(dojo.data.util.sorter.createSortFunction(args.sort, this));
		}
		findCallback(items, args);
	},

	close: function(/*dojo.data.api.Request || args || null */ request){
		return;
	},

	getLabel: function(/* item */ item){
		return item.innerHTML;
	},

	getIdentity: function(/* item */ item){
		return dojo.attr(item, "value");
	},

	fetchItemByIdentity: function(/* Object */ args){
		// summary:
		//		Given the identity of an item, this method returns the item that has
		//		that identity through the onItem callback.
		//		Refer to dojo.data.api.Identity.fetchItemByIdentity() for more details.
		//
		// description:
		//		Given arguments like:
		//
		//	|		{identity: "CA", onItem: function(item){...}
		//
		//		Call `onItem()` with the DOM node `<option value="CA">California</option>`
		var item = dojo.query("> option[value='" + args.identity + "']", this.root)[0];
		args.onItem(item);
	},

	fetchSelectedItem: function(){
		// summary:
		//		Get the option marked as selected, like `<option selected>`.
		//		Not part of dojo.data API.
		var root = this.root,
			si = root.selectedIndex;
		return typeof si == "number"
			? dojo.query("> option:nth-child(" + (si != -1 ? si+1 : 1) + ")", root)[0]
			: null;	// dojo.data.Item
	}
});
//Mix in the simple fetch implementation to this class.
dojo.extend(dijit.form._ComboBoxDataStore,dojo.data.util.simpleFetch);

}

if(!dojo._hasResource["dojo.dnd.common"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.common"] = true;
dojo.provide("dojo.dnd.common");

dojo.dnd.getCopyKeyState = dojo.isCopyKey;

dojo.dnd._uniqueId = 0;
dojo.dnd.getUniqueId = function(){
	// summary:
	//		returns a unique string for use with any DOM element
	var id;
	do{
		id = dojo._scopeName + "Unique" + (++dojo.dnd._uniqueId);
	}while(dojo.byId(id));
	return id;
};

dojo.dnd._empty = {};

dojo.dnd.isFormElement = function(/*Event*/ e){
	// summary:
	//		returns true if user clicked on a form element
	var t = e.target;
	if(t.nodeType == 3 /*TEXT_NODE*/){
		t = t.parentNode;
	}
	return " button textarea input select option ".indexOf(" " + t.tagName.toLowerCase() + " ") >= 0;	// Boolean
};

}

if(!dojo._hasResource["dojo.dnd.autoscroll"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.autoscroll"] = true;
dojo.provide("dojo.dnd.autoscroll");

dojo.dnd.getViewport = function(){
	// summary:
	//		Returns a viewport size (visible part of the window)

	// TODO: remove this when getViewport() moved to dojo core, see #7028

	// FIXME: need more docs!!
	var d = dojo.doc, dd = d.documentElement, w = window, b = dojo.body();
	if(dojo.isMozilla){
		return {w: dd.clientWidth, h: w.innerHeight};	// Object
	}else if(!dojo.isOpera && w.innerWidth){
		return {w: w.innerWidth, h: w.innerHeight};		// Object
	}else if (!dojo.isOpera && dd && dd.clientWidth){
		return {w: dd.clientWidth, h: dd.clientHeight};	// Object
	}else if (b.clientWidth){
		return {w: b.clientWidth, h: b.clientHeight};	// Object
	}
	return null;	// Object
};

dojo.dnd.V_TRIGGER_AUTOSCROLL = 32;
dojo.dnd.H_TRIGGER_AUTOSCROLL = 32;

dojo.dnd.V_AUTOSCROLL_VALUE = 16;
dojo.dnd.H_AUTOSCROLL_VALUE = 16;

dojo.dnd.autoScroll = function(e){
	// summary:
	//		a handler for onmousemove event, which scrolls the window, if
	//		necesary
	// e: Event
	//		onmousemove event

	// FIXME: needs more docs!
	var v = dojo.dnd.getViewport(), dx = 0, dy = 0;
	if(e.clientX < dojo.dnd.H_TRIGGER_AUTOSCROLL){
		dx = -dojo.dnd.H_AUTOSCROLL_VALUE;
	}else if(e.clientX > v.w - dojo.dnd.H_TRIGGER_AUTOSCROLL){
		dx = dojo.dnd.H_AUTOSCROLL_VALUE;
	}
	if(e.clientY < dojo.dnd.V_TRIGGER_AUTOSCROLL){
		dy = -dojo.dnd.V_AUTOSCROLL_VALUE;
	}else if(e.clientY > v.h - dojo.dnd.V_TRIGGER_AUTOSCROLL){
		dy = dojo.dnd.V_AUTOSCROLL_VALUE;
	}
	window.scrollBy(dx, dy);
};

dojo.dnd._validNodes = {"div": 1, "p": 1, "td": 1};
dojo.dnd._validOverflow = {"auto": 1, "scroll": 1};

dojo.dnd.autoScrollNodes = function(e){
	// summary:
	//		a handler for onmousemove event, which scrolls the first avaialble
	//		Dom element, it falls back to dojo.dnd.autoScroll()
	// e: Event
	//		onmousemove event

	// FIXME: needs more docs!
	for(var n = e.target; n;){
		if(n.nodeType == 1 && (n.tagName.toLowerCase() in dojo.dnd._validNodes)){
			var s = dojo.getComputedStyle(n);
			if(s.overflow.toLowerCase() in dojo.dnd._validOverflow){
				var b = dojo._getContentBox(n, s), t = dojo.position(n, true);
				//console.log(b.l, b.t, t.x, t.y, n.scrollLeft, n.scrollTop);
				var w = Math.min(dojo.dnd.H_TRIGGER_AUTOSCROLL, b.w / 2), 
					h = Math.min(dojo.dnd.V_TRIGGER_AUTOSCROLL, b.h / 2),
					rx = e.pageX - t.x, ry = e.pageY - t.y, dx = 0, dy = 0;
				if(dojo.isWebKit || dojo.isOpera){
					// FIXME: this code should not be here, it should be taken into account 
					// either by the event fixing code, or the dojo.position()
					// FIXME: this code doesn't work on Opera 9.5 Beta
					rx += dojo.body().scrollLeft, ry += dojo.body().scrollTop;
				}
				if(rx > 0 && rx < b.w){
					if(rx < w){
						dx = -w;
					}else if(rx > b.w - w){
						dx = w;
					}
				}
				//console.log("ry =", ry, "b.h =", b.h, "h =", h);
				if(ry > 0 && ry < b.h){
					if(ry < h){
						dy = -h;
					}else if(ry > b.h - h){
						dy = h;
					}
				}
				var oldLeft = n.scrollLeft, oldTop = n.scrollTop;
				n.scrollLeft = n.scrollLeft + dx;
				n.scrollTop  = n.scrollTop  + dy;
				if(oldLeft != n.scrollLeft || oldTop != n.scrollTop){ return; }
			}
		}
		try{
			n = n.parentNode;
		}catch(x){
			n = null;
		}
	}
	dojo.dnd.autoScroll(e);
};

}

if(!dojo._hasResource["dojo.dnd.Mover"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.Mover"] = true;
dojo.provide("dojo.dnd.Mover");




dojo.declare("dojo.dnd.Mover", null, {
	constructor: function(node, e, host){
		// summary:
		//		an object, which makes a node follow the mouse. 
		//		Used as a default mover, and as a base class for custom movers.
		// node: Node
		//		a node (or node's id) to be moved
		// e: Event
		//		a mouse event, which started the move;
		//		only pageX and pageY properties are used
		// host: Object?
		//		object which implements the functionality of the move,
		//	 	and defines proper events (onMoveStart and onMoveStop)
		this.node = dojo.byId(node);
		this.marginBox = {l: e.pageX, t: e.pageY};
		this.mouseButton = e.button;
		var h = this.host = host, d = node.ownerDocument, 
			firstEvent = dojo.connect(d, "onmousemove", this, "onFirstMove");
		this.events = [
			dojo.connect(d, "onmousemove", this, "onMouseMove"),
			dojo.connect(d, "onmouseup",   this, "onMouseUp"),
			// cancel text selection and text dragging
			dojo.connect(d, "ondragstart",   dojo.stopEvent),
			dojo.connect(d.body, "onselectstart", dojo.stopEvent),
			firstEvent
		];
		// notify that the move has started
		if(h && h.onMoveStart){
			h.onMoveStart(this);
		}
	},
	// mouse event processors
	onMouseMove: function(e){
		// summary:
		//		event processor for onmousemove
		// e: Event
		//		mouse event
		dojo.dnd.autoScroll(e);
		var m = this.marginBox;
		this.host.onMove(this, {l: m.l + e.pageX, t: m.t + e.pageY}, e);
		dojo.stopEvent(e);
	},
	onMouseUp: function(e){
		if(dojo.isWebKit && dojo.isMac && this.mouseButton == 2 ? 
				e.button == 0 : this.mouseButton == e.button){
			this.destroy();
		}
		dojo.stopEvent(e);
	},
	// utilities
	onFirstMove: function(e){
		// summary:
		//		makes the node absolute; it is meant to be called only once. 
		// 		relative and absolutely positioned nodes are assumed to use pixel units
		var s = this.node.style, l, t, h = this.host;
		switch(s.position){
			case "relative":
			case "absolute":
				// assume that left and top values are in pixels already
				l = Math.round(parseFloat(s.left)) || 0;
				t = Math.round(parseFloat(s.top)) || 0;
				break;
			default:
				s.position = "absolute";	// enforcing the absolute mode
				var m = dojo.marginBox(this.node);
				// event.pageX/pageY (which we used to generate the initial
				// margin box) includes padding and margin set on the body.
				// However, setting the node's position to absolute and then
				// doing dojo.marginBox on it *doesn't* take that additional
				// space into account - so we need to subtract the combined
				// padding and margin.  We use getComputedStyle and
				// _getMarginBox/_getContentBox to avoid the extra lookup of
				// the computed style. 
				var b = dojo.doc.body;
				var bs = dojo.getComputedStyle(b);
				var bm = dojo._getMarginBox(b, bs);
				var bc = dojo._getContentBox(b, bs);
				l = m.l - (bc.l - bm.l);
				t = m.t - (bc.t - bm.t);
				break;
		}
		this.marginBox.l = l - this.marginBox.l;
		this.marginBox.t = t - this.marginBox.t;
		if(h && h.onFirstMove){
			h.onFirstMove(this, e);
		}
		dojo.disconnect(this.events.pop());
	},
	destroy: function(){
		// summary:
		//		stops the move, deletes all references, so the object can be garbage-collected
		dojo.forEach(this.events, dojo.disconnect);
		// undo global settings
		var h = this.host;
		if(h && h.onMoveStop){
			h.onMoveStop(this);
		}
		// destroy objects
		this.events = this.node = this.host = null;
	}
});

}

if(!dojo._hasResource["dojo.dnd.Moveable"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.Moveable"] = true;
dojo.provide("dojo.dnd.Moveable");



/*=====
dojo.declare("dojo.dnd.__MoveableArgs", [], {
	// handle: Node||String
	//		A node (or node's id), which is used as a mouse handle.
	//		If omitted, the node itself is used as a handle.
	handle: null,

	// delay: Number
	//		delay move by this number of pixels
	delay: 0,

	// skip: Boolean
	//		skip move of form elements
	skip: false,

	// mover: Object
	//		a constructor of custom Mover
	mover: dojo.dnd.Mover
});
=====*/

dojo.declare("dojo.dnd.Moveable", null, {
	// object attributes (for markup)
	handle: "",
	delay: 0,
	skip: false,
	
	constructor: function(node, params){
		// summary:
		//		an object, which makes a node moveable
		// node: Node
		//		a node (or node's id) to be moved
		// params: dojo.dnd.__MoveableArgs?
		//		optional parameters
		this.node = dojo.byId(node);
		if(!params){ params = {}; }
		this.handle = params.handle ? dojo.byId(params.handle) : null;
		if(!this.handle){ this.handle = this.node; }
		this.delay = params.delay > 0 ? params.delay : 0;
		this.skip  = params.skip;
		this.mover = params.mover ? params.mover : dojo.dnd.Mover;
		this.events = [
			dojo.connect(this.handle, "onmousedown", this, "onMouseDown"),
			// cancel text selection and text dragging
			dojo.connect(this.handle, "ondragstart",   this, "onSelectStart"),
			dojo.connect(this.handle, "onselectstart", this, "onSelectStart")
		];
	},

	// markup methods
	markupFactory: function(params, node){
		return new dojo.dnd.Moveable(node, params);
	},

	// methods
	destroy: function(){
		// summary:
		//		stops watching for possible move, deletes all references, so the object can be garbage-collected
		dojo.forEach(this.events, dojo.disconnect);
		this.events = this.node = this.handle = null;
	},
	
	// mouse event processors
	onMouseDown: function(e){
		// summary:
		//		event processor for onmousedown, creates a Mover for the node
		// e: Event
		//		mouse event
		if(this.skip && dojo.dnd.isFormElement(e)){ return; }
		if(this.delay){
			this.events.push(
				dojo.connect(this.handle, "onmousemove", this, "onMouseMove"),
				dojo.connect(this.handle, "onmouseup", this, "onMouseUp")
			);
			this._lastX = e.pageX;
			this._lastY = e.pageY;
		}else{
			this.onDragDetected(e);
		}
		dojo.stopEvent(e);
	},
	onMouseMove: function(e){
		// summary:
		//		event processor for onmousemove, used only for delayed drags
		// e: Event
		//		mouse event
		if(Math.abs(e.pageX - this._lastX) > this.delay || Math.abs(e.pageY - this._lastY) > this.delay){
			this.onMouseUp(e);
			this.onDragDetected(e);
		}
		dojo.stopEvent(e);
	},
	onMouseUp: function(e){
		// summary:
		//		event processor for onmouseup, used only for delayed drags
		// e: Event
		//		mouse event
		for(var i = 0; i < 2; ++i){
			dojo.disconnect(this.events.pop());
		}
		dojo.stopEvent(e);
	},
	onSelectStart: function(e){
		// summary:
		//		event processor for onselectevent and ondragevent
		// e: Event
		//		mouse event
		if(!this.skip || !dojo.dnd.isFormElement(e)){
			dojo.stopEvent(e);
		}
	},
	
	// local events
	onDragDetected: function(/* Event */ e){
		// summary:
		//		called when the drag is detected;
		//		responsible for creation of the mover
		new this.mover(this.node, e, this);
	},
	onMoveStart: function(/* dojo.dnd.Mover */ mover){
		// summary:
		//		called before every move operation
		dojo.publish("/dnd/move/start", [mover]);
		dojo.addClass(dojo.body(), "dojoMove"); 
		dojo.addClass(this.node, "dojoMoveItem"); 
	},
	onMoveStop: function(/* dojo.dnd.Mover */ mover){
		// summary:
		//		called after every move operation
		dojo.publish("/dnd/move/stop", [mover]);
		dojo.removeClass(dojo.body(), "dojoMove");
		dojo.removeClass(this.node, "dojoMoveItem");
	},
	onFirstMove: function(/* dojo.dnd.Mover */ mover, /* Event */ e){
		// summary:
		//		called during the very first move notification;
		//		can be used to initialize coordinates, can be overwritten.
		
		// default implementation does nothing
	},
	onMove: function(/* dojo.dnd.Mover */ mover, /* Object */ leftTop, /* Event */ e){
		// summary:
		//		called during every move notification;
		//		should actually move the node; can be overwritten.
		this.onMoving(mover, leftTop);
		var s = mover.node.style;
		s.left = leftTop.l + "px";
		s.top  = leftTop.t + "px";
		this.onMoved(mover, leftTop);
	},
	onMoving: function(/* dojo.dnd.Mover */ mover, /* Object */ leftTop){
		// summary:
		//		called before every incremental move; can be overwritten.
		
		// default implementation does nothing
	},
	onMoved: function(/* dojo.dnd.Mover */ mover, /* Object */ leftTop){
		// summary:
		//		called after every incremental move; can be overwritten.
		
		// default implementation does nothing
	}
});

}

if(!dojo._hasResource["dojo.dnd.move"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.move"] = true;
dojo.provide("dojo.dnd.move");




/*=====
dojo.declare("dojo.dnd.move.__constrainedMoveableArgs", [dojo.dnd.__MoveableArgs], {
	// constraints: Function
	//		Calculates a constraint box.
	//		It is called in a context of the moveable object.
	constraints: function(){},

	// within: Boolean
	//		restrict move within boundaries.
	within: false
});
=====*/

dojo.declare("dojo.dnd.move.constrainedMoveable", dojo.dnd.Moveable, {
	// object attributes (for markup)
	constraints: function(){},
	within: false,
	
	// markup methods
	markupFactory: function(params, node){
		return new dojo.dnd.move.constrainedMoveable(node, params);
	},

	constructor: function(node, params){
		// summary:
		//		an object that makes a node moveable
		// node: Node
		//		a node (or node's id) to be moved
		// params: dojo.dnd.move.__constrainedMoveableArgs?
		//		an optional object with additional parameters;
		//		the rest is passed to the base class
		if(!params){ params = {}; }
		this.constraints = params.constraints;
		this.within = params.within;
	},
	onFirstMove: function(/* dojo.dnd.Mover */ mover){
		// summary:
		//		called during the very first move notification;
		//		can be used to initialize coordinates, can be overwritten.
		var c = this.constraintBox = this.constraints.call(this, mover);
		c.r = c.l + c.w;
		c.b = c.t + c.h;
		if(this.within){
			var mb = dojo.marginBox(mover.node);
			c.r -= mb.w;
			c.b -= mb.h;
		}
	},
	onMove: function(/* dojo.dnd.Mover */ mover, /* Object */ leftTop){
		// summary:
		//		called during every move notification;
		//		should actually move the node; can be overwritten.
		var c = this.constraintBox, s = mover.node.style;
		s.left = (leftTop.l < c.l ? c.l : c.r < leftTop.l ? c.r : leftTop.l) + "px";
		s.top  = (leftTop.t < c.t ? c.t : c.b < leftTop.t ? c.b : leftTop.t) + "px";
	}
});

/*=====
dojo.declare("dojo.dnd.move.__boxConstrainedMoveableArgs", [dojo.dnd.move.__constrainedMoveableArgs], {
	// box: Object
	//		a constraint box
	box: {}
});
=====*/

dojo.declare("dojo.dnd.move.boxConstrainedMoveable", dojo.dnd.move.constrainedMoveable, {
	// box:
	//		object attributes (for markup)
	box: {},
	
	// markup methods
	markupFactory: function(params, node){
		return new dojo.dnd.move.boxConstrainedMoveable(node, params);
	},

	constructor: function(node, params){
		// summary:
		//		an object, which makes a node moveable
		// node: Node
		//		a node (or node's id) to be moved
		// params: dojo.dnd.move.__boxConstrainedMoveableArgs?
		//		an optional object with parameters
		var box = params && params.box;
		this.constraints = function(){ return box; };
	}
});

/*=====
dojo.declare("dojo.dnd.move.__parentConstrainedMoveableArgs", [dojo.dnd.move.__constrainedMoveableArgs], {
	// area: String
	//		A parent's area to restrict the move.
	//		Can be "margin", "border", "padding", or "content".
	area: ""
});
=====*/

dojo.declare("dojo.dnd.move.parentConstrainedMoveable", dojo.dnd.move.constrainedMoveable, {
	// area:
	//		object attributes (for markup)
	area: "content",

	// markup methods
	markupFactory: function(params, node){
		return new dojo.dnd.move.parentConstrainedMoveable(node, params);
	},

	constructor: function(node, params){
		// summary:
		//		an object, which makes a node moveable
		// node: Node
		//		a node (or node's id) to be moved
		// params: dojo.dnd.move.__parentConstrainedMoveableArgs?
		//		an optional object with parameters
		var area = params && params.area;
		this.constraints = function(){
			var n = this.node.parentNode, 
				s = dojo.getComputedStyle(n), 
				mb = dojo._getMarginBox(n, s);
			if(area == "margin"){
				return mb;	// Object
			}
			var t = dojo._getMarginExtents(n, s);
			mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
			if(area == "border"){
				return mb;	// Object
			}
			t = dojo._getBorderExtents(n, s);
			mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
			if(area == "padding"){
				return mb;	// Object
			}
			t = dojo._getPadExtents(n, s);
			mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
			return mb;	// Object
		};
	}
});

// WARNING: below are obsolete objects, instead of custom movers use custom moveables (above)

dojo.dnd.move.constrainedMover = function(fun, within){
	// summary:
	//		returns a constrained version of dojo.dnd.Mover
	// description:
	//		this function produces n object, which will put a constraint on 
	//		the margin box of dragged object in absolute coordinates
	// fun: Function
	//		called on drag, and returns a constraint box
	// within: Boolean
	//		if true, constraints the whole dragged object withtin the rectangle, 
	//		otherwise the constraint is applied to the left-top corner

	dojo.deprecated("dojo.dnd.move.constrainedMover, use dojo.dnd.move.constrainedMoveable instead");
	var mover = function(node, e, notifier){
		dojo.dnd.Mover.call(this, node, e, notifier);
	};
	dojo.extend(mover, dojo.dnd.Mover.prototype);
	dojo.extend(mover, {
		onMouseMove: function(e){
			// summary: event processor for onmousemove
			// e: Event: mouse event
			dojo.dnd.autoScroll(e);
			var m = this.marginBox, c = this.constraintBox,
				l = m.l + e.pageX, t = m.t + e.pageY;
			l = l < c.l ? c.l : c.r < l ? c.r : l;
			t = t < c.t ? c.t : c.b < t ? c.b : t;
			this.host.onMove(this, {l: l, t: t});
		},
		onFirstMove: function(){
			// summary: called once to initialize things; it is meant to be called only once
			dojo.dnd.Mover.prototype.onFirstMove.call(this);
			var c = this.constraintBox = fun.call(this);
			c.r = c.l + c.w;
			c.b = c.t + c.h;
			if(within){
				var mb = dojo.marginBox(this.node);
				c.r -= mb.w;
				c.b -= mb.h;
			}
		}
	});
	return mover;	// Object
};

dojo.dnd.move.boxConstrainedMover = function(box, within){
	// summary:
	//		a specialization of dojo.dnd.constrainedMover, which constrains to the specified box
	// box: Object
	//		a constraint box (l, t, w, h)
	// within: Boolean
	//		if true, constraints the whole dragged object withtin the rectangle, 
	//		otherwise the constraint is applied to the left-top corner

	dojo.deprecated("dojo.dnd.move.boxConstrainedMover, use dojo.dnd.move.boxConstrainedMoveable instead");
	return dojo.dnd.move.constrainedMover(function(){ return box; }, within);	// Object
};

dojo.dnd.move.parentConstrainedMover = function(area, within){
	// summary:
	//		a specialization of dojo.dnd.constrainedMover, which constrains to the parent node
	// area: String
	//		"margin" to constrain within the parent's margin box, "border" for the border box,
	//		"padding" for the padding box, and "content" for the content box; "content" is the default value.
	// within: Boolean
	//		if true, constraints the whole dragged object within the rectangle, 
	//		otherwise the constraint is applied to the left-top corner

	dojo.deprecated("dojo.dnd.move.parentConstrainedMover, use dojo.dnd.move.parentConstrainedMoveable instead");
	var fun = function(){
		var n = this.node.parentNode, 
			s = dojo.getComputedStyle(n), 
			mb = dojo._getMarginBox(n, s);
		if(area == "margin"){
			return mb;	// Object
		}
		var t = dojo._getMarginExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		if(area == "border"){
			return mb;	// Object
		}
		t = dojo._getBorderExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		if(area == "padding"){
			return mb;	// Object
		}
		t = dojo._getPadExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		return mb;	// Object
	};
	return dojo.dnd.move.constrainedMover(fun, within);	// Object
};

// patching functions one level up for compatibility

dojo.dnd.constrainedMover = dojo.dnd.move.constrainedMover;
dojo.dnd.boxConstrainedMover = dojo.dnd.move.boxConstrainedMover;
dojo.dnd.parentConstrainedMover = dojo.dnd.move.parentConstrainedMover;

}

if(!dojo._hasResource["dojo.dnd.TimedMoveable"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.TimedMoveable"] = true;
dojo.provide("dojo.dnd.TimedMoveable");



/*=====
dojo.declare("dojo.dnd.__TimedMoveableArgs", [dojo.dnd.__MoveableArgs], {
	// timeout: Number
	//		delay move by this number of ms,
	//		accumulating position changes during the timeout
	timeout: 0
});
=====*/

(function(){
	// precalculate long expressions
	var oldOnMove = dojo.dnd.Moveable.prototype.onMove;
		
	dojo.declare("dojo.dnd.TimedMoveable", dojo.dnd.Moveable, {
		// summary:
		//		A specialized version of Moveable to support an FPS throttling.
		//		This class puts an upper restriction on FPS, which may reduce 
		//		the CPU load. The additional parameter "timeout" regulates
		//		the delay before actually moving the moveable object.
		
		// object attributes (for markup)
		timeout: 40,	// in ms, 40ms corresponds to 25 fps
	
		constructor: function(node, params){
			// summary:
			//		an object that makes a node moveable with a timer
			// node: Node||String
			//		a node (or node's id) to be moved
			// params: dojo.dnd.__TimedMoveableArgs
			//		object with additional parameters.
			
			// sanitize parameters
			if(!params){ params = {}; }
			if(params.timeout && typeof params.timeout == "number" && params.timeout >= 0){
				this.timeout = params.timeout;
			}
		},
	
		// markup methods
		markupFactory: function(params, node){
			return new dojo.dnd.TimedMoveable(node, params);
		},
	
		onMoveStop: function(/* dojo.dnd.Mover */ mover){
			if(mover._timer){
				// stop timer
				clearTimeout(mover._timer)
				// reflect the last received position
				oldOnMove.call(this, mover, mover._leftTop)
			}
			dojo.dnd.Moveable.prototype.onMoveStop.apply(this, arguments);
		},
		onMove: function(/* dojo.dnd.Mover */ mover, /* Object */ leftTop){
			mover._leftTop = leftTop;
			if(!mover._timer){
				var _t = this;	// to avoid using dojo.hitch()
				mover._timer = setTimeout(function(){
					// we don't have any pending requests
					mover._timer = null;
					// reflect the last received position
					oldOnMove.call(_t, mover, mover._leftTop);
				}, this.timeout);
			}
		}
	});
})();

}

if(!dojo._hasResource["dojo.fx.Toggler"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.fx.Toggler"] = true;
dojo.provide("dojo.fx.Toggler");

dojo.declare("dojo.fx.Toggler", null, {
	// summary:
	//		A simple `dojo.Animation` toggler API.
	//
	// description:
	//		class constructor for an animation toggler. It accepts a packed
	//		set of arguments about what type of animation to use in each
	//		direction, duration, etc. All available members are mixed into 
	//		these animations from the constructor (for example, `node`, 
	//		`showDuration`, `hideDuration`). 
	//
	// example:
	//	|	var t = new dojo.fx.Toggler({
	//	|		node: "nodeId",
	//	|		showDuration: 500,
	//	|		// hideDuration will default to "200"
	//	|		showFunc: dojo.fx.wipeIn, 
	//	|		// hideFunc will default to "fadeOut"
	//	|	});
	//	|	t.show(100); // delay showing for 100ms
	//	|	// ...time passes...
	//	|	t.hide();

	// node: DomNode
	//		the node to target for the showing and hiding animations
	node: null,

	// showFunc: Function
	//		The function that returns the `dojo.Animation` to show the node
	showFunc: dojo.fadeIn,

	// hideFunc: Function	
	//		The function that returns the `dojo.Animation` to hide the node
	hideFunc: dojo.fadeOut,

	// showDuration:
	//		Time in milliseconds to run the show Animation
	showDuration: 200,

	// hideDuration:
	//		Time in milliseconds to run the hide Animation
	hideDuration: 200,

	// FIXME: need a policy for where the toggler should "be" the next
	// time show/hide are called if we're stopped somewhere in the
	// middle.
	// FIXME: also would be nice to specify individual showArgs/hideArgs mixed into
	// each animation individually. 
	// FIXME: also would be nice to have events from the animations exposed/bridged

	/*=====
	_showArgs: null,
	_showAnim: null,

	_hideArgs: null,
	_hideAnim: null,

	_isShowing: false,
	_isHiding: false,
	=====*/

	constructor: function(args){
		var _t = this;

		dojo.mixin(_t, args);
		_t.node = args.node;
		_t._showArgs = dojo.mixin({}, args);
		_t._showArgs.node = _t.node;
		_t._showArgs.duration = _t.showDuration;
		_t.showAnim = _t.showFunc(_t._showArgs);

		_t._hideArgs = dojo.mixin({}, args);
		_t._hideArgs.node = _t.node;
		_t._hideArgs.duration = _t.hideDuration;
		_t.hideAnim = _t.hideFunc(_t._hideArgs);

		dojo.connect(_t.showAnim, "beforeBegin", dojo.hitch(_t.hideAnim, "stop", true));
		dojo.connect(_t.hideAnim, "beforeBegin", dojo.hitch(_t.showAnim, "stop", true));
	},

	show: function(delay){
		// summary: Toggle the node to showing
		// delay: Integer?
		//		Ammount of time to stall playing the show animation
		return this.showAnim.play(delay || 0);
	},

	hide: function(delay){
		// summary: Toggle the node to hidden
		// delay: Integer?
		//		Ammount of time to stall playing the hide animation
		return this.hideAnim.play(delay || 0);
	}
});

}

if(!dojo._hasResource["dojo.fx"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.fx"] = true;
dojo.provide("dojo.fx");
 // FIXME: remove this back-compat require in 2.0 
/*=====
dojo.fx = {
	// summary: Effects library on top of Base animations
};
=====*/
(function(){
	
	var d = dojo, 
		_baseObj = {
			_fire: function(evt, args){
				if(this[evt]){
					this[evt].apply(this, args||[]);
				}
				return this;
			}
		};

	var _chain = function(animations){
		this._index = -1;
		this._animations = animations||[];
		this._current = this._onAnimateCtx = this._onEndCtx = null;

		this.duration = 0;
		d.forEach(this._animations, function(a){
			this.duration += a.duration;
			if(a.delay){ this.duration += a.delay; }
		}, this);
	};
	d.extend(_chain, {
		_onAnimate: function(){
			this._fire("onAnimate", arguments);
		},
		_onEnd: function(){
			d.disconnect(this._onAnimateCtx);
			d.disconnect(this._onEndCtx);
			this._onAnimateCtx = this._onEndCtx = null;
			if(this._index + 1 == this._animations.length){
				this._fire("onEnd");
			}else{
				// switch animations
				this._current = this._animations[++this._index];
				this._onAnimateCtx = d.connect(this._current, "onAnimate", this, "_onAnimate");
				this._onEndCtx = d.connect(this._current, "onEnd", this, "_onEnd");
				this._current.play(0, true);
			}
		},
		play: function(/*int?*/ delay, /*Boolean?*/ gotoStart){
			if(!this._current){ this._current = this._animations[this._index = 0]; }
			if(!gotoStart && this._current.status() == "playing"){ return this; }
			var beforeBegin = d.connect(this._current, "beforeBegin", this, function(){
					this._fire("beforeBegin");
				}),
				onBegin = d.connect(this._current, "onBegin", this, function(arg){
					this._fire("onBegin", arguments);
				}),
				onPlay = d.connect(this._current, "onPlay", this, function(arg){
					this._fire("onPlay", arguments);
					d.disconnect(beforeBegin);
					d.disconnect(onBegin);
					d.disconnect(onPlay);
				});
			if(this._onAnimateCtx){
				d.disconnect(this._onAnimateCtx);
			}
			this._onAnimateCtx = d.connect(this._current, "onAnimate", this, "_onAnimate");
			if(this._onEndCtx){
				d.disconnect(this._onEndCtx);
			}
			this._onEndCtx = d.connect(this._current, "onEnd", this, "_onEnd");
			this._current.play.apply(this._current, arguments);
			return this;
		},
		pause: function(){
			if(this._current){
				var e = d.connect(this._current, "onPause", this, function(arg){
						this._fire("onPause", arguments);
						d.disconnect(e);
					});
				this._current.pause();
			}
			return this;
		},
		gotoPercent: function(/*Decimal*/percent, /*Boolean?*/ andPlay){
			this.pause();
			var offset = this.duration * percent;
			this._current = null;
			d.some(this._animations, function(a){
				if(a.duration <= offset){
					this._current = a;
					return true;
				}
				offset -= a.duration;
				return false;
			});
			if(this._current){
				this._current.gotoPercent(offset / this._current.duration, andPlay);
			}
			return this;
		},
		stop: function(/*boolean?*/ gotoEnd){
			if(this._current){
				if(gotoEnd){
					for(; this._index + 1 < this._animations.length; ++this._index){
						this._animations[this._index].stop(true);
					}
					this._current = this._animations[this._index];
				}
				var e = d.connect(this._current, "onStop", this, function(arg){
						this._fire("onStop", arguments);
						d.disconnect(e);
					});
				this._current.stop();
			}
			return this;
		},
		status: function(){
			return this._current ? this._current.status() : "stopped";
		},
		destroy: function(){
			if(this._onAnimateCtx){ d.disconnect(this._onAnimateCtx); }
			if(this._onEndCtx){ d.disconnect(this._onEndCtx); }
		}
	});
	d.extend(_chain, _baseObj);

	dojo.fx.chain = function(/*dojo.Animation[]*/ animations){
		// summary: 
		//		Chain a list of `dojo.Animation`s to run in sequence
		//
		// description:
		//		Return a `dojo.Animation` which will play all passed
		//		`dojo.Animation` instances in sequence, firing its own
		//		synthesized events simulating a single animation. (eg:
		//		onEnd of this animation means the end of the chain, 
		//		not the individual animations within)
		//
		// example:
		//	Once `node` is faded out, fade in `otherNode`
		//	|	dojo.fx.chain([
		//	|		dojo.fadeIn({ node:node }),
		//	|		dojo.fadeOut({ node:otherNode })
		//	|	]).play();
		//
		return new _chain(animations) // dojo.Animation
	};

	var _combine = function(animations){
		this._animations = animations||[];
		this._connects = [];
		this._finished = 0;

		this.duration = 0;
		d.forEach(animations, function(a){
			var duration = a.duration;
			if(a.delay){ duration += a.delay; }
			if(this.duration < duration){ this.duration = duration; }
			this._connects.push(d.connect(a, "onEnd", this, "_onEnd"));
		}, this);
		
		this._pseudoAnimation = new d.Animation({curve: [0, 1], duration: this.duration});
		var self = this;
		d.forEach(["beforeBegin", "onBegin", "onPlay", "onAnimate", "onPause", "onStop", "onEnd"], 
			function(evt){
				self._connects.push(d.connect(self._pseudoAnimation, evt,
					function(){ self._fire(evt, arguments); }
				));
			}
		);
	};
	d.extend(_combine, {
		_doAction: function(action, args){
			d.forEach(this._animations, function(a){
				a[action].apply(a, args);
			});
			return this;
		},
		_onEnd: function(){
			if(++this._finished > this._animations.length){
				this._fire("onEnd");
			}
		},
		_call: function(action, args){
			var t = this._pseudoAnimation;
			t[action].apply(t, args);
		},
		play: function(/*int?*/ delay, /*Boolean?*/ gotoStart){
			this._finished = 0;
			this._doAction("play", arguments);
			this._call("play", arguments);
			return this;
		},
		pause: function(){
			this._doAction("pause", arguments);
			this._call("pause", arguments);
			return this;
		},
		gotoPercent: function(/*Decimal*/percent, /*Boolean?*/ andPlay){
			var ms = this.duration * percent;
			d.forEach(this._animations, function(a){
				a.gotoPercent(a.duration < ms ? 1 : (ms / a.duration), andPlay);
			});
			this._call("gotoPercent", arguments);
			return this;
		},
		stop: function(/*boolean?*/ gotoEnd){
			this._doAction("stop", arguments);
			this._call("stop", arguments);
			return this;
		},
		status: function(){
			return this._pseudoAnimation.status();
		},
		destroy: function(){
			d.forEach(this._connects, dojo.disconnect);
		}
	});
	d.extend(_combine, _baseObj);

	dojo.fx.combine = function(/*dojo.Animation[]*/ animations){
		// summary: 
		//		Combine a list of `dojo.Animation`s to run in parallel
		//
		// description:
		//		Combine an array of `dojo.Animation`s to run in parallel, 
		//		providing a new `dojo.Animation` instance encompasing each
		//		animation, firing standard animation events.
		//
		// example:
		//	Fade out `node` while fading in `otherNode` simultaneously
		//	|	dojo.fx.combine([
		//	|		dojo.fadeIn({ node:node }),
		//	|		dojo.fadeOut({ node:otherNode })
		//	|	]).play();
		//
		// example:
		//	When the longest animation ends, execute a function:
		//	|	var anim = dojo.fx.combine([
		//	|		dojo.fadeIn({ node: n, duration:700 }),
		//	|		dojo.fadeOut({ node: otherNode, duration: 300 })
		//	|	]);
		//	|	dojo.connect(anim, "onEnd", function(){
		//	|		// overall animation is done.
		//	|	});
		//	|	anim.play(); // play the animation
		//
		return new _combine(animations); // dojo.Animation
	};

	dojo.fx.wipeIn = function(/*Object*/ args){
		// summary:
		//		Expand a node to it's natural height.
		//
		// description:
		//		Returns an animation that will expand the
		//		node defined in 'args' object from it's current height to
		//		it's natural height (with no scrollbar).
		//		Node must have no margin/border/padding.
		//
		// args: Object
		//		A hash-map of standard `dojo.Animation` constructor properties
		//		(such as easing: node: duration: and so on)
		//
		// example:
		//	|	dojo.fx.wipeIn({
		//	|		node:"someId"
		//	|	}).play()
		var node = args.node = d.byId(args.node), s = node.style, o;

		var anim = d.animateProperty(d.mixin({
			properties: {
				height: {
					// wrapped in functions so we wait till the last second to query (in case value has changed)
					start: function(){
						// start at current [computed] height, but use 1px rather than 0
						// because 0 causes IE to display the whole panel
						o = s.overflow;
						s.overflow = "hidden";
						if(s.visibility == "hidden" || s.display == "none"){
							s.height = "1px";
							s.display = "";
							s.visibility = "";
							return 1;
						}else{
							var height = d.style(node, "height");
							return Math.max(height, 1);
						}
					},
					end: function(){
						return node.scrollHeight;
					}
				}
			}
		}, args));

		d.connect(anim, "onEnd", function(){ 
			s.height = "auto";
			s.overflow = o;
		});

		return anim; // dojo.Animation
	}

	dojo.fx.wipeOut = function(/*Object*/ args){
		// summary:
		//		Shrink a node to nothing and hide it. 
		//
		// description:
		//		Returns an animation that will shrink node defined in "args"
		//		from it's current height to 1px, and then hide it.
		//
		// args: Object
		//		A hash-map of standard `dojo.Animation` constructor properties
		//		(such as easing: node: duration: and so on)
		// 
		// example:
		//	|	dojo.fx.wipeOut({ node:"someId" }).play()
		
		var node = args.node = d.byId(args.node), s = node.style, o;
		
		var anim = d.animateProperty(d.mixin({
			properties: {
				height: {
					end: 1 // 0 causes IE to display the whole panel
				}
			}
		}, args));

		d.connect(anim, "beforeBegin", function(){
			o = s.overflow;
			s.overflow = "hidden";
			s.display = "";
		});
		d.connect(anim, "onEnd", function(){
			s.overflow = o;
			s.height = "auto";
			s.display = "none";
		});

		return anim; // dojo.Animation
	}

	dojo.fx.slideTo = function(/*Object*/ args){
		// summary:
		//		Slide a node to a new top/left position
		//
		// description:
		//		Returns an animation that will slide "node" 
		//		defined in args Object from its current position to
		//		the position defined by (args.left, args.top).
		//
		// args: Object
		//		A hash-map of standard `dojo.Animation` constructor properties
		//		(such as easing: node: duration: and so on). Special args members
		//		are `top` and `left`, which indicate the new position to slide to.
		//
		// example:
		//	|	dojo.fx.slideTo({ node: node, left:"40", top:"50", units:"px" }).play()

		var node = args.node = d.byId(args.node), 
			top = null, left = null;

		var init = (function(n){
			return function(){
				var cs = d.getComputedStyle(n);
				var pos = cs.position;
				top = (pos == 'absolute' ? n.offsetTop : parseInt(cs.top) || 0);
				left = (pos == 'absolute' ? n.offsetLeft : parseInt(cs.left) || 0);
				if(pos != 'absolute' && pos != 'relative'){
					var ret = d.position(n, true);
					top = ret.y;
					left = ret.x;
					n.style.position="absolute";
					n.style.top=top+"px";
					n.style.left=left+"px";
				}
			};
		})(node);
		init();

		var anim = d.animateProperty(d.mixin({
			properties: {
				top: args.top || 0,
				left: args.left || 0
			}
		}, args));
		d.connect(anim, "beforeBegin", anim, init);

		return anim; // dojo.Animation
	}

})();

}

if(!dojo._hasResource["dijit.form._FormMixin"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._FormMixin"] = true;
dojo.provide("dijit.form._FormMixin");



dojo.declare("dijit.form._FormMixin", null,
	{
	// summary:
	//		Mixin for containers of form widgets (i.e. widgets that represent a single value
	//		and can be children of a <form> node or dijit.form.Form widget)
	// description:
	//		Can extract all the form widgets
	//		values and combine them into a single javascript object, or alternately
	//		take such an object and set the values for all the contained
	//		form widgets

/*=====
    // value: Object
	//		Name/value hash for each child widget with a name and value.
	//		Child widgets without names are not part of the hash.
	// 
	//		If there are multiple child widgets w/the same name, value is an array,
	//		unless they are radio buttons in which case value is a scalar (since only
	//		one radio button can be checked at a time).
	//
	//		If a child widget's name is a dot separated list (like a.b.c.d), it's a nested structure.
	//
	//		Example:
	//	|	{ name: "John Smith", interests: ["sports", "movies"] }
=====*/

	//	TODO:
	//	* Repeater
	//	* better handling for arrays.  Often form elements have names with [] like
	//	* people[3].sex (for a list of people [{name: Bill, sex: M}, ...])
	//
	//

		reset: function(){
			dojo.forEach(this.getDescendants(), function(widget){
				if(widget.reset){
					widget.reset();
				}
			});
		},

		validate: function(){
			// summary:
			//		returns if the form is valid - same as isValid - but
			//			provides a few additional (ui-specific) features.
			//			1 - it will highlight any sub-widgets that are not
			//				valid
			//			2 - it will call focus() on the first invalid
			//				sub-widget
			var didFocus = false;
			return dojo.every(dojo.map(this.getDescendants(), function(widget){
				// Need to set this so that "required" widgets get their
				// state set.
				widget._hasBeenBlurred = true;
				var valid = widget.disabled || !widget.validate || widget.validate();
				if(!valid && !didFocus){
					// Set focus of the first non-valid widget
					dojo.window.scrollIntoView(widget.containerNode || widget.domNode);
					widget.focus();
					didFocus = true;
				}
	 			return valid;
	 		}), function(item){ return item; });
		},

		setValues: function(val){
			dojo.deprecated(this.declaredClass+"::setValues() is deprecated. Use set('value', val) instead.", "", "2.0");
			return this.set('value', val);
		},
		_setValueAttr: function(/*object*/obj){
			// summary:
			//		Fill in form values from according to an Object (in the format returned by attr('value'))

			// generate map from name --> [list of widgets with that name]
			var map = { };
			dojo.forEach(this.getDescendants(), function(widget){
				if(!widget.name){ return; }
				var entry = map[widget.name] || (map[widget.name] = [] );
				entry.push(widget);
			});

			for(var name in map){
				if(!map.hasOwnProperty(name)){
					continue;
				}
				var widgets = map[name],						// array of widgets w/this name
					values = dojo.getObject(name, false, obj);	// list of values for those widgets

				if(values === undefined){
					continue;
				}
				if(!dojo.isArray(values)){
					values = [ values ];
				}
				if(typeof widgets[0].checked == 'boolean'){
					// for checkbox/radio, values is a list of which widgets should be checked
					dojo.forEach(widgets, function(w, i){
						w.set('value', dojo.indexOf(values, w.value) != -1);
					});
				}else if(widgets[0].multiple){
					// it takes an array (e.g. multi-select)
					widgets[0].set('value', values);
				}else{
					// otherwise, values is a list of values to be assigned sequentially to each widget
					dojo.forEach(widgets, function(w, i){
						w.set('value', values[i]);
					});
				}
			}

			/***
			 * 	TODO: code for plain input boxes (this shouldn't run for inputs that are part of widgets)

			dojo.forEach(this.containerNode.elements, function(element){
				if(element.name == ''){return};	// like "continue"
				var namePath = element.name.split(".");
				var myObj=obj;
				var name=namePath[namePath.length-1];
				for(var j=1,len2=namePath.length;j<len2;++j){
					var p=namePath[j - 1];
					// repeater support block
					var nameA=p.split("[");
					if(nameA.length > 1){
						if(typeof(myObj[nameA[0]]) == "undefined"){
							myObj[nameA[0]]=[ ];
						} // if

						nameIndex=parseInt(nameA[1]);
						if(typeof(myObj[nameA[0]][nameIndex]) == "undefined"){
							myObj[nameA[0]][nameIndex] = { };
						}
						myObj=myObj[nameA[0]][nameIndex];
						continue;
					} // repeater support ends

					if(typeof(myObj[p]) == "undefined"){
						myObj=undefined;
						break;
					};
					myObj=myObj[p];
				}

				if(typeof(myObj) == "undefined"){
					return;		// like "continue"
				}
				if(typeof(myObj[name]) == "undefined" && this.ignoreNullValues){
					return;		// like "continue"
				}

				// TODO: widget values (just call attr('value', ...) on the widget)

				// TODO: maybe should call dojo.getNodeProp() instead
				switch(element.type){
					case "checkbox":
						element.checked = (name in myObj) &&
							dojo.some(myObj[name], function(val){ return val == element.value; });
						break;
					case "radio":
						element.checked = (name in myObj) && myObj[name] == element.value;
						break;
					case "select-multiple":
						element.selectedIndex=-1;
						dojo.forEach(element.options, function(option){
							option.selected = dojo.some(myObj[name], function(val){ return option.value == val; });
						});
						break;
					case "select-one":
						element.selectedIndex="0";
						dojo.forEach(element.options, function(option){
							option.selected = option.value == myObj[name];
						});
						break;
					case "hidden":
					case "text":
					case "textarea":
					case "password":
						element.value = myObj[name] || "";
						break;
				}
	  		});
	  		*/
		},

		getValues: function(){
			dojo.deprecated(this.declaredClass+"::getValues() is deprecated. Use get('value') instead.", "", "2.0");
			return this.get('value');
		},
		_getValueAttr: function(){
			// summary:
			// 		Returns Object representing form values.
			// description:
			//		Returns name/value hash for each form element.
			//		If there are multiple elements w/the same name, value is an array,
			//		unless they are radio buttons in which case value is a scalar since only
			//		one can be checked at a time.
			//
			//		If the name is a dot separated list (like a.b.c.d), creates a nested structure.
			//		Only works on widget form elements.
			// example:
			//		| { name: "John Smith", interests: ["sports", "movies"] }

			// get widget values
			var obj = { };
			dojo.forEach(this.getDescendants(), function(widget){
				var name = widget.name;
				if(!name || widget.disabled){ return; }

				// Single value widget (checkbox, radio, or plain <input> type widget
				var value = widget.get('value');

				// Store widget's value(s) as a scalar, except for checkboxes which are automatically arrays
				if(typeof widget.checked == 'boolean'){
					if(/Radio/.test(widget.declaredClass)){
						// radio button
						if(value !== false){
							dojo.setObject(name, value, obj);
						}else{
							// give radio widgets a default of null
							value = dojo.getObject(name, false, obj);
							if(value === undefined){
								dojo.setObject(name, null, obj);
							}
						}
					}else{
						// checkbox/toggle button
						var ary=dojo.getObject(name, false, obj);
						if(!ary){
							ary=[];
							dojo.setObject(name, ary, obj);
						}
						if(value !== false){
							ary.push(value);
						}
					}
				}else{
					var prev=dojo.getObject(name, false, obj);
					if(typeof prev != "undefined"){
						if(dojo.isArray(prev)){
							prev.push(value);
						}else{
							dojo.setObject(name, [prev, value], obj);
						}
					}else{
						// unique name
						dojo.setObject(name, value, obj);
					}
				}
			});

			/***
			 * code for plain input boxes (see also dojo.formToObject, can we use that instead of this code?
			 * but it doesn't understand [] notation, presumably)
			var obj = { };
			dojo.forEach(this.containerNode.elements, function(elm){
				if(!elm.name)	{
					return;		// like "continue"
				}
				var namePath = elm.name.split(".");
				var myObj=obj;
				var name=namePath[namePath.length-1];
				for(var j=1,len2=namePath.length;j<len2;++j){
					var nameIndex = null;
					var p=namePath[j - 1];
					var nameA=p.split("[");
					if(nameA.length > 1){
						if(typeof(myObj[nameA[0]]) == "undefined"){
							myObj[nameA[0]]=[ ];
						} // if
						nameIndex=parseInt(nameA[1]);
						if(typeof(myObj[nameA[0]][nameIndex]) == "undefined"){
							myObj[nameA[0]][nameIndex] = { };
						}
					} else if(typeof(myObj[nameA[0]]) == "undefined"){
						myObj[nameA[0]] = { }
					} // if

					if(nameA.length == 1){
						myObj=myObj[nameA[0]];
					} else{
						myObj=myObj[nameA[0]][nameIndex];
					} // if
				} // for

				if((elm.type != "select-multiple" && elm.type != "checkbox" && elm.type != "radio") || (elm.type == "radio" && elm.checked)){
					if(name == name.split("[")[0]){
						myObj[name]=elm.value;
					} else{
						// can not set value when there is no name
					}
				} else if(elm.type == "checkbox" && elm.checked){
					if(typeof(myObj[name]) == 'undefined'){
						myObj[name]=[ ];
					}
					myObj[name].push(elm.value);
				} else if(elm.type == "select-multiple"){
					if(typeof(myObj[name]) == 'undefined'){
						myObj[name]=[ ];
					}
					for(var jdx=0,len3=elm.options.length; jdx<len3; ++jdx){
						if(elm.options[jdx].selected){
							myObj[name].push(elm.options[jdx].value);
						}
					}
				} // if
				name=undefined;
			}); // forEach
			***/
			return obj;
		},

		// TODO: ComboBox might need time to process a recently input value.  This should be async?
	 	isValid: function(){
	 		// summary:
	 		//		Returns true if all of the widgets are valid

	 		// This also populate this._invalidWidgets[] array with list of invalid widgets...
	 		// TODO: put that into separate function?   It's confusing to have that as a side effect
	 		// of a method named isValid().

			this._invalidWidgets = dojo.filter(this.getDescendants(), function(widget){
				return !widget.disabled && widget.isValid && !widget.isValid();
	 		});
			return !this._invalidWidgets.length;
		},


		onValidStateChange: function(isValid){
			// summary:
			//		Stub function to connect to if you want to do something
			//		(like disable/enable a submit button) when the valid
			//		state changes on the form as a whole.
		},

		_widgetChange: function(widget){
			// summary:
			//		Connected to a widget's onChange function - update our
			//		valid state, if needed.
			var isValid = this._lastValidState;
			if(!widget || this._lastValidState === undefined){
				// We have passed a null widget, or we haven't been validated
				// yet - let's re-check all our children
				// This happens when we connect (or reconnect) our children
				isValid = this.isValid();
				if(this._lastValidState === undefined){
					// Set this so that we don't fire an onValidStateChange
					// the first time
					this._lastValidState = isValid;
				}
			}else if(widget.isValid){
				this._invalidWidgets = dojo.filter(this._invalidWidgets || [], function(w){
					return (w != widget);
				}, this);
				if(!widget.isValid() && !widget.get("disabled")){
					this._invalidWidgets.push(widget);
				}
				isValid = (this._invalidWidgets.length === 0);
			}
			if(isValid !== this._lastValidState){
				this._lastValidState = isValid;
				this.onValidStateChange(isValid);
			}
		},

		connectChildren: function(){
			// summary:
			//		Connects to the onChange function of all children to
			//		track valid state changes.  You can call this function
			//		directly, ex. in the event that you programmatically
			//		add a widget to the form *after* the form has been
			//		initialized.
			dojo.forEach(this._changeConnections, dojo.hitch(this, "disconnect"));
			var _this = this;

			// we connect to validate - so that it better reflects the states
			// of the widgets - also, we only connect if it has a validate
			// function (to avoid too many unneeded connections)
			var conns = (this._changeConnections = []);
			dojo.forEach(dojo.filter(this.getDescendants(),
				function(item){ return item.validate; }
			),
			function(widget){
				// We are interested in whenever the widget is validated - or
				// whenever the disabled attribute on that widget is changed
				conns.push(_this.connect(widget, "validate",
									dojo.hitch(_this, "_widgetChange", widget)));
				conns.push(_this.connect(widget, "_setDisabledAttr",
									dojo.hitch(_this, "_widgetChange", widget)));
			});

			// Call the widget change function to update the valid state, in
			// case something is different now.
			this._widgetChange(null);
		},

		startup: function(){
			this.inherited(arguments);
			// Initialize our valid state tracking.  Needs to be done in startup
			// because it's not guaranteed that our children are initialized
			// yet.
			this._changeConnections = [];
			this.connectChildren();
		}
	});

}

if(!dojo._hasResource["dijit._DialogMixin"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._DialogMixin"] = true;
dojo.provide("dijit._DialogMixin");



dojo.declare("dijit._DialogMixin", null,
	{
		// summary:
		//		This provides functions useful to Dialog and TooltipDialog

		attributeMap: dijit._Widget.prototype.attributeMap,

		execute: function(/*Object*/ formContents){
			// summary:
			//		Callback when the user hits the submit button.
			//		Override this method to handle Dialog execution.
			// description:
			//		After the user has pressed the submit button, the Dialog
			//		first calls onExecute() to notify the container to hide the
			//		dialog and restore focus to wherever it used to be.
			//
			//		*Then* this method is called.
			// type:
			//		callback
		},

		onCancel: function(){
			// summary:
			//	    Called when user has pressed the Dialog's cancel button, to notify container.
			// description:
			//	    Developer shouldn't override or connect to this method;
			//		it's a private communication device between the TooltipDialog
			//		and the thing that opened it (ex: `dijit.form.DropDownButton`)
			// type:
			//		protected
		},

		onExecute: function(){
			// summary:
			//	    Called when user has pressed the dialog's OK button, to notify container.
			// description:
			//	    Developer shouldn't override or connect to this method;
			//		it's a private communication device between the TooltipDialog
			//		and the thing that opened it (ex: `dijit.form.DropDownButton`)
			// type:
			//		protected
		},

		_onSubmit: function(){
			// summary:
			//		Callback when user hits submit button
			// type:
			//		protected
			this.onExecute();	// notify container that we are about to execute
			this.execute(this.get('value'));
		},

		_getFocusItems: function(/*Node*/ dialogNode){
			// summary:
			//		Find focusable Items each time a dialog is opened,
			//		setting _firstFocusItem and _lastFocusItem
			// tags:
			//		protected

			var elems = dijit._getTabNavigable(dojo.byId(dialogNode));
			this._firstFocusItem = elems.lowest || elems.first || dialogNode;
			this._lastFocusItem = elems.last || elems.highest || this._firstFocusItem;
			if(dojo.isMoz && this._firstFocusItem.tagName.toLowerCase() == "input" &&
					dojo.getNodeProp(this._firstFocusItem, "type").toLowerCase() == "file"){
				// FF doesn't behave well when first element is input type=file, set first focusable to dialog container
				dojo.attr(dialogNode, "tabIndex", "0");
				this._firstFocusItem = dialogNode;
			}
		}
	}
);

}

if(!dojo._hasResource["dijit.DialogUnderlay"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.DialogUnderlay"] = true;
dojo.provide("dijit.DialogUnderlay");






dojo.declare(
	"dijit.DialogUnderlay",
	[dijit._Widget, dijit._Templated],
	{
		// summary:
		//		The component that blocks the screen behind a `dijit.Dialog`
		//
		// description:
		// 		A component used to block input behind a `dijit.Dialog`. Only a single
		//		instance of this widget is created by `dijit.Dialog`, and saved as
		//		a reference to be shared between all Dialogs as `dijit._underlay`
		//
		//		The underlay itself can be styled based on and id:
		//	|	#myDialog_underlay { background-color:red; }
		//
		//		In the case of `dijit.Dialog`, this id is based on the id of the Dialog,
		//		suffixed with _underlay.

		// Template has two divs; outer div is used for fade-in/fade-out, and also to hold background iframe.
		// Inner div has opacity specified in CSS file.
		templateString: "<div class='dijitDialogUnderlayWrapper'><div class='dijitDialogUnderlay' dojoAttachPoint='node'></div></div>",

		// Parameters on creation or updatable later

		// dialogId: String
		//		Id of the dialog.... DialogUnderlay's id is based on this id
		dialogId: "",

		// class: String
		//		This class name is used on the DialogUnderlay node, in addition to dijitDialogUnderlay
		"class": "",

		attributeMap: { id: "domNode" },

		_setDialogIdAttr: function(id){
			dojo.attr(this.node, "id", id + "_underlay");
		},

		_setClassAttr: function(clazz){
			this.node.className = "dijitDialogUnderlay " + clazz;
		},

		postCreate: function(){
			// summary:
			//		Append the underlay to the body
			dojo.body().appendChild(this.domNode);
		},

		layout: function(){
			// summary:
			//		Sets the background to the size of the viewport
			//
			// description:
			//		Sets the background to the size of the viewport (rather than the size
			//		of the document) since we need to cover the whole browser window, even
			//		if the document is only a few lines long.
			// tags:
			//		private

			var is = this.node.style,
				os = this.domNode.style;

			// hide the background temporarily, so that the background itself isn't
			// causing scrollbars to appear (might happen when user shrinks browser
			// window and then we are called to resize)
			os.display = "none";

			// then resize and show
			var viewport = dojo.window.getBox();
			os.top = viewport.t + "px";
			os.left = viewport.l + "px";
			is.width = viewport.w + "px";
			is.height = viewport.h + "px";
			os.display = "block";
		},

		show: function(){
			// summary:
			//		Show the dialog underlay
			this.domNode.style.display = "block";
			this.layout();
			this.bgIframe = new dijit.BackgroundIframe(this.domNode);
		},

		hide: function(){
			// summary:
			//		Hides the dialog underlay
			this.bgIframe.destroy();
			this.domNode.style.display = "none";
		},

		uninitialize: function(){
			if(this.bgIframe){
				this.bgIframe.destroy();
			}
			this.inherited(arguments);
		}
	}
);

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

if(!dojo._hasResource["dojo.html"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.html"] = true;
dojo.provide("dojo.html");

// the parser might be needed..
 

(function(){ // private scope, sort of a namespace

	// idCounter is incremented with each instantiation to allow asignment of a unique id for tracking, logging purposes
	var idCounter = 0, 
		d = dojo;
	
	dojo.html._secureForInnerHtml = function(/*String*/ cont){
		// summary:
		//		removes !DOCTYPE and title elements from the html string.
		// 
		//		khtml is picky about dom faults, you can't attach a style or <title> node as child of body
		//		must go into head, so we need to cut out those tags
		//	cont:
		//		An html string for insertion into the dom
		//	
		return cont.replace(/(?:\s*<!DOCTYPE\s[^>]+>|<title[^>]*>[\s\S]*?<\/title>)/ig, ""); // String
	};

/*====
	dojo.html._emptyNode = function(node){
		// summary:
		//		removes all child nodes from the given node
		//	node: DOMNode
		//		the parent element
	};
=====*/
	dojo.html._emptyNode = dojo.empty;

	dojo.html._setNodeContent = function(/* DomNode */ node, /* String|DomNode|NodeList */ cont){
		// summary:
		//		inserts the given content into the given node
		//	node:
		//		the parent element
		//	content:
		//		the content to be set on the parent element. 
		//		This can be an html string, a node reference or a NodeList, dojo.NodeList, Array or other enumerable list of nodes
		
		// always empty
		d.empty(node);

		if(cont) {
			if(typeof cont == "string") {
				cont = d._toDom(cont, node.ownerDocument);
			}
			if(!cont.nodeType && d.isArrayLike(cont)) {
				// handle as enumerable, but it may shrink as we enumerate it
				for(var startlen=cont.length, i=0; i<cont.length; i=startlen==cont.length ? i+1 : 0) {
					d.place( cont[i], node, "last");
				}
			} else {
				// pass nodes, documentFragments and unknowns through to dojo.place
				d.place(cont, node, "last");
			}
		}

		// return DomNode
		return node;
	};

	// we wrap up the content-setting operation in a object
	dojo.declare("dojo.html._ContentSetter", null, 
		{
			// node: DomNode|String
			//		An node which will be the parent element that we set content into
			node: "",

			// content: String|DomNode|DomNode[]
			//		The content to be placed in the node. Can be an HTML string, a node reference, or a enumerable list of nodes
			content: "",
			
			// id: String?
			//		Usually only used internally, and auto-generated with each instance 
			id: "",

			// cleanContent: Boolean
			//		Should the content be treated as a full html document, 
			//		and the real content stripped of <html>, <body> wrapper before injection
			cleanContent: false,
			
			// extractContent: Boolean
			//		Should the content be treated as a full html document, and the real content stripped of <html>, <body> wrapper before injection
			extractContent: false,

			// parseContent: Boolean
			//		Should the node by passed to the parser after the new content is set
			parseContent: false,
			
			// lifecyle methods
			constructor: function(/* Object */params, /* String|DomNode */node){
				//	summary:
				//		Provides a configurable, extensible object to wrap the setting on content on a node
				//		call the set() method to actually set the content..
 
				// the original params are mixed directly into the instance "this"
				dojo.mixin(this, params || {});

				// give precedence to params.node vs. the node argument
				// and ensure its a node, not an id string
				node = this.node = dojo.byId( this.node || node );
	
				if(!this.id){
					this.id = [
						"Setter",
						(node) ? node.id || node.tagName : "", 
						idCounter++
					].join("_");
				}
			},
			set: function(/* String|DomNode|NodeList? */ cont, /* Object? */ params){
				// summary:
				//		front-end to the set-content sequence 
				//	cont:
				//		An html string, node or enumerable list of nodes for insertion into the dom
				//		If not provided, the object's content property will be used
				if(undefined !== cont){
					this.content = cont;
				}
				// in the re-use scenario, set needs to be able to mixin new configuration
				if(params){
					this._mixin(params);
				}

				this.onBegin();
				this.setContent();
				this.onEnd();

				return this.node;
			},
			setContent: function(){
				// summary:
				//		sets the content on the node 

				var node = this.node; 
				if(!node) {
				    // can't proceed
					throw new Error(this.declaredClass + ": setContent given no node");
				}
				try{
					node = dojo.html._setNodeContent(node, this.content);
				}catch(e){
					// check if a domfault occurs when we are appending this.errorMessage
					// like for instance if domNode is a UL and we try append a DIV
	
					// FIXME: need to allow the user to provide a content error message string
					var errMess = this.onContentError(e); 
					try{
						node.innerHTML = errMess;
					}catch(e){
						console.error('Fatal ' + this.declaredClass + '.setContent could not change content due to '+e.message, e);
					}
				}
				// always put back the node for the next method
				this.node = node; // DomNode
			},
			
			empty: function() {
				// summary
				//	cleanly empty out existing content

				// destroy any widgets from a previous run
				// NOTE: if you dont want this you'll need to empty 
				// the parseResults array property yourself to avoid bad things happenning
				if(this.parseResults && this.parseResults.length) {
					dojo.forEach(this.parseResults, function(w) {
						if(w.destroy){
							w.destroy();
						}
					});
					delete this.parseResults;
				}
				// this is fast, but if you know its already empty or safe, you could 
				// override empty to skip this step
				dojo.html._emptyNode(this.node);
			},
	
			onBegin: function(){
				// summary
				//		Called after instantiation, but before set(); 
				//		It allows modification of any of the object properties 
				//		- including the node and content provided - before the set operation actually takes place
				//		This default implementation checks for cleanContent and extractContent flags to 
				//		optionally pre-process html string content
				var cont = this.content;
	
				if(dojo.isString(cont)){
					if(this.cleanContent){
						cont = dojo.html._secureForInnerHtml(cont);
					}
  
					if(this.extractContent){
						var match = cont.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
						if(match){ cont = match[1]; }
					}
				}

				// clean out the node and any cruft associated with it - like widgets
				this.empty();
				
				this.content = cont;
				return this.node; /* DomNode */
			},
	
			onEnd: function(){
				// summary
				//		Called after set(), when the new content has been pushed into the node
				//		It provides an opportunity for post-processing before handing back the node to the caller
				//		This default implementation checks a parseContent flag to optionally run the dojo parser over the new content
				if(this.parseContent){
					// populates this.parseResults if you need those..
					this._parse();
				}
				return this.node; /* DomNode */
			},
	
			tearDown: function(){
				// summary
				//		manually reset the Setter instance if its being re-used for example for another set()
				// description
				//		tearDown() is not called automatically. 
				//		In normal use, the Setter instance properties are simply allowed to fall out of scope
				//		but the tearDown method can be called to explicitly reset this instance.
				delete this.parseResults; 
				delete this.node; 
				delete this.content; 
			},
  
			onContentError: function(err){
				return "Error occured setting content: " + err; 
			},
			
			_mixin: function(params){
				// mix properties/methods into the instance
				// TODO: the intention with tearDown is to put the Setter's state 
				// back to that of the original constructor (vs. deleting/resetting everything regardless of ctor params)
				// so we could do something here to move the original properties aside for later restoration
				var empty = {}, key;
				for(key in params){
					if(key in empty){ continue; }
					// TODO: here's our opportunity to mask the properties we dont consider configurable/overridable
					// .. but history shows we'll almost always guess wrong
					this[key] = params[key]; 
				}
			},
			_parse: function(){
				// summary: 
				//		runs the dojo parser over the node contents, storing any results in this.parseResults
				//		Any errors resulting from parsing are passed to _onError for handling

				var rootNode = this.node;
				try{
					// store the results (widgets, whatever) for potential retrieval
					this.parseResults = dojo.parser.parse({
						rootNode: rootNode,
						dir: this.dir,
						lang: this.lang
					});
				}catch(e){
					this._onError('Content', e, "Error parsing in _ContentSetter#"+this.id);
				}
			},
  
			_onError: function(type, err, consoleText){
				// summary:
				//		shows user the string that is returned by on[type]Error
				//		overide/implement on[type]Error and return your own string to customize
				var errText = this['on' + type + 'Error'].call(this, err);
				if(consoleText){
					console.error(consoleText, err);
				}else if(errText){ // a empty string won't change current content
					dojo.html._setNodeContent(this.node, errText, true);
				}
			}
	}); // end dojo.declare()

	dojo.html.set = function(/* DomNode */ node, /* String|DomNode|NodeList */ cont, /* Object? */ params){
			// summary:
			//		inserts (replaces) the given content into the given node. dojo.place(cont, node, "only")
			//		may be a better choice for simple HTML insertion.
			// description:
			//		Unless you need to use the params capabilities of this method, you should use
			//		dojo.place(cont, node, "only"). dojo.place() has more robust support for injecting
			//		an HTML string into the DOM, but it only handles inserting an HTML string as DOM
			//		elements, or inserting a DOM node. dojo.place does not handle NodeList insertions
			//		or the other capabilities as defined by the params object for this method.
			//	node:
			//		the parent element that will receive the content
			//	cont:
			//		the content to be set on the parent element. 
			//		This can be an html string, a node reference or a NodeList, dojo.NodeList, Array or other enumerable list of nodes
			//	params: 
			//		Optional flags/properties to configure the content-setting. See dojo.html._ContentSetter
			//	example:
			//		A safe string/node/nodelist content replacement/injection with hooks for extension
			//		Example Usage: 
			//		dojo.html.set(node, "some string"); 
			//		dojo.html.set(node, contentNode, {options}); 
			//		dojo.html.set(node, myNode.childNodes, {options}); 
		if(undefined == cont){
			console.warn("dojo.html.set: no cont argument provided, using empty string");
			cont = "";
		}	
		if(!params){
			// simple and fast
			return dojo.html._setNodeContent(node, cont, true);
		}else{ 
			// more options but slower
			// note the arguments are reversed in order, to match the convention for instantiation via the parser
			var op = new dojo.html._ContentSetter(dojo.mixin( 
					params, 
					{ content: cont, node: node } 
			));
			return op.set();
		}
	};
})();

}

if(!dojo._hasResource["dijit.layout.ContentPane"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout.ContentPane"] = true;
dojo.provide("dijit.layout.ContentPane");



	// for dijit.layout.marginBox2contentBox()






dojo.declare(
	"dijit.layout.ContentPane", dijit._Widget,
{
	// summary:
	//		A widget that acts as a container for mixed HTML and widgets, and includes an Ajax interface
	// description:
	//		A widget that can be used as a stand alone widget
	//		or as a base class for other widgets.
	//
	//		Handles replacement of document fragment using either external uri or javascript
	//		generated markup or DOM content, instantiating widgets within that content.
	//		Don't confuse it with an iframe, it only needs/wants document fragments.
	//		It's useful as a child of LayoutContainer, SplitContainer, or TabContainer.
	//		But note that those classes can contain any widget as a child.
	// example:
	//		Some quick samples:
	//		To change the innerHTML use .set('content', '<b>new content</b>')
	//
	//		Or you can send it a NodeList, .set('content', dojo.query('div [class=selected]', userSelection))
	//		please note that the nodes in NodeList will copied, not moved
	//
	//		To do a ajax update use .set('href', url)

	// href: String
	//		The href of the content that displays now.
	//		Set this at construction if you want to load data externally when the
	//		pane is shown.  (Set preload=true to load it immediately.)
	//		Changing href after creation doesn't have any effect; Use set('href', ...);
	href: "",

/*=====
	// content: String || DomNode || NodeList || dijit._Widget
	//		The innerHTML of the ContentPane.
	//		Note that the initialization parameter / argument to attr("content", ...)
	//		can be a String, DomNode, Nodelist, or _Widget.
	content: "",
=====*/

	// extractContent: Boolean
	//		Extract visible content from inside of <body> .... </body>.
	//		I.e., strip <html> and <head> (and it's contents) from the href
	extractContent: false,

	// parseOnLoad: Boolean
	//		Parse content and create the widgets, if any.
	parseOnLoad: true,

	// preventCache: Boolean
	//		Prevent caching of data from href's by appending a timestamp to the href.
	preventCache: false,

	// preload: Boolean
	//		Force load of data on initialization even if pane is hidden.
	preload: false,

	// refreshOnShow: Boolean
	//		Refresh (re-download) content when pane goes from hidden to shown
	refreshOnShow: false,

	// loadingMessage: String
	//		Message that shows while downloading
	loadingMessage: "<span class='dijitContentPaneLoading'>${loadingState}</span>",

	// errorMessage: String
	//		Message that shows if an error occurs
	errorMessage: "<span class='dijitContentPaneError'>${errorState}</span>",

	// isLoaded: [readonly] Boolean
	//		True if the ContentPane has data in it, either specified
	//		during initialization (via href or inline content), or set
	//		via attr('content', ...) / attr('href', ...)
	//
	//		False if it doesn't have any content, or if ContentPane is
	//		still in the process of downloading href.
	isLoaded: false,

	baseClass: "dijitContentPane",

	// doLayout: Boolean
	//		- false - don't adjust size of children
	//		- true - if there is a single visible child widget, set it's size to
	//				however big the ContentPane is
	doLayout: true,

	// ioArgs: Object
	//		Parameters to pass to xhrGet() request, for example:
	// |	<div dojoType="dijit.layout.ContentPane" href="./bar" ioArgs="{timeout: 500}">
	ioArgs: {},

	// isContainer: [protected] Boolean
	//		Indicates that this widget acts as a "parent" to the descendant widgets.
	//		When the parent is started it will call startup() on the child widgets.
	//		See also `isLayoutContainer`.
	isContainer: true,

	// isLayoutContainer: [protected] Boolean
	//		Indicates that this widget will call resize() on it's child widgets
	//		when they become visible.
	isLayoutContainer: true,

	// onLoadDeferred: [readonly] dojo.Deferred
	//		This is the `dojo.Deferred` returned by attr('href', ...) and refresh().
	//		Calling onLoadDeferred.addCallback() or addErrback() registers your
	//		callback to be called only once, when the prior attr('href', ...) call or
	//		the initial href parameter to the constructor finishes loading.
	//
	//		This is different than an onLoad() handler which gets called any time any href is loaded.
	onLoadDeferred: null,

	// Override _Widget's attributeMap because we don't want the title attribute (used to specify
	// tab labels) to be copied to ContentPane.domNode... otherwise a tooltip shows up over the
	// entire pane.
	attributeMap: dojo.delegate(dijit._Widget.prototype.attributeMap, {
		title: []
	}),

	postMixInProperties: function(){
		this.inherited(arguments);
		var messages = dojo.i18n.getLocalization("dijit", "loading", this.lang);
		this.loadingMessage = dojo.string.substitute(this.loadingMessage, messages);
		this.errorMessage = dojo.string.substitute(this.errorMessage, messages);

		// Detect if we were initialized with data
		if(!this.href && this.srcNodeRef && this.srcNodeRef.innerHTML){
			this.isLoaded = true;
		}
	},

	buildRendering: function(){
		// Overrides Widget.buildRendering().
		// Since we have no template we need to set this.containerNode ourselves.
		// For subclasses of ContentPane do have a template, does nothing.
		this.inherited(arguments);
		if(!this.containerNode){
			// make getDescendants() work
			this.containerNode = this.domNode;
		}
	},

	postCreate: function(){
		// remove the title attribute so it doesn't show up when hovering
		// over a node
		this.domNode.title = "";

		if(!dojo.attr(this.domNode,"role")){
			dijit.setWaiRole(this.domNode, "group");
		}

		dojo.addClass(this.domNode, this.baseClass);
	},

	startup: function(){
		// summary:
		//		See `dijit.layout._LayoutWidget.startup` for description.
		//		Although ContentPane doesn't extend _LayoutWidget, it does implement
		//		the same API.
		if(this._started){ return; }

		var parent = dijit._Contained.prototype.getParent.call(this);
		this._childOfLayoutWidget = parent && parent.isLayoutContainer;

		// I need to call resize() on my child/children (when I become visible), unless
		// I'm the child of a layout widget in which case my parent will call resize() on me and I'll do it then.
		this._needLayout = !this._childOfLayoutWidget;

		if(this.isLoaded){
			dojo.forEach(this.getChildren(), function(child){
				child.startup();
			});
		}

		if(this._isShown() || this.preload){
			this._onShow();
		}

		this.inherited(arguments);
	},

	_checkIfSingleChild: function(){
		// summary:
		//		Test if we have exactly one visible widget as a child,
		//		and if so assume that we are a container for that widget,
		//		and should propogate startup() and resize() calls to it.
		//		Skips over things like data stores since they aren't visible.

		var childNodes = dojo.query("> *", this.containerNode).filter(function(node){
				return node.tagName !== "SCRIPT"; // or a regexp for hidden elements like script|area|map|etc..
			}),
			childWidgetNodes = childNodes.filter(function(node){
				return dojo.hasAttr(node, "dojoType") || dojo.hasAttr(node, "widgetId");
			}),
			candidateWidgets = dojo.filter(childWidgetNodes.map(dijit.byNode), function(widget){
				return widget && widget.domNode && widget.resize;
			});

		if(
			// all child nodes are widgets
			childNodes.length == childWidgetNodes.length &&

			// all but one are invisible (like dojo.data)
			candidateWidgets.length == 1
		){
			this._singleChild = candidateWidgets[0];
		}else{
			delete this._singleChild;
		}

		// So we can set overflow: hidden to avoid a safari bug w/scrollbars showing up (#9449)
		dojo.toggleClass(this.containerNode, this.baseClass + "SingleChild", !!this._singleChild);
	},

	setHref: function(/*String|Uri*/ href){
		// summary:
		//		Deprecated.   Use set('href', ...) instead.
		dojo.deprecated("dijit.layout.ContentPane.setHref() is deprecated. Use set('href', ...) instead.", "", "2.0");
		return this.set("href", href);
	},
	_setHrefAttr: function(/*String|Uri*/ href){
		// summary:
		//		Hook so attr("href", ...) works.
		// description:
		//		Reset the (external defined) content of this pane and replace with new url
		//		Note: It delays the download until widget is shown if preload is false.
		//	href:
		//		url to the page you want to get, must be within the same domain as your mainpage

		// Cancel any in-flight requests (an attr('href') will cancel any in-flight attr('href', ...))
		this.cancel();

		this.onLoadDeferred = new dojo.Deferred(dojo.hitch(this, "cancel"));

		this.href = href;

		// _setHrefAttr() is called during creation and by the user, after creation.
		// only in the second case do we actually load the URL; otherwise it's done in startup()
		if(this._created && (this.preload || this._isShown())){
			this._load();
		}else{
			// Set flag to indicate that href needs to be loaded the next time the
			// ContentPane is made visible
			this._hrefChanged = true;
		}

		return this.onLoadDeferred;		// dojo.Deferred
	},

	setContent: function(/*String|DomNode|Nodelist*/data){
		// summary:
		//		Deprecated.   Use set('content', ...) instead.
		dojo.deprecated("dijit.layout.ContentPane.setContent() is deprecated.  Use set('content', ...) instead.", "", "2.0");
		this.set("content", data);
	},
	_setContentAttr: function(/*String|DomNode|Nodelist*/data){
		// summary:
		//		Hook to make attr("content", ...) work.
		//		Replaces old content with data content, include style classes from old content
		//	data:
		//		the new Content may be String, DomNode or NodeList
		//
		//		if data is a NodeList (or an array of nodes) nodes are copied
		//		so you can import nodes from another document implicitly

		// clear href so we can't run refresh and clear content
		// refresh should only work if we downloaded the content
		this.href = "";

		// Cancel any in-flight requests (an attr('content') will cancel any in-flight attr('href', ...))
		this.cancel();

		// Even though user is just setting content directly, still need to define an onLoadDeferred
		// because the _onLoadHandler() handler is still getting called from setContent()
		this.onLoadDeferred = new dojo.Deferred(dojo.hitch(this, "cancel"));

		this._setContent(data || "");

		this._isDownloaded = false; // mark that content is from a attr('content') not an attr('href')

		return this.onLoadDeferred; 	// dojo.Deferred
	},
	_getContentAttr: function(){
		// summary:
		//		Hook to make attr("content") work
		return this.containerNode.innerHTML;
	},

	cancel: function(){
		// summary:
		//		Cancels an in-flight download of content
		if(this._xhrDfd && (this._xhrDfd.fired == -1)){
			this._xhrDfd.cancel();
		}
		delete this._xhrDfd; // garbage collect

		this.onLoadDeferred = null;
	},

	uninitialize: function(){
		if(this._beingDestroyed){
			this.cancel();
		}
		this.inherited(arguments);
	},

	destroyRecursive: function(/*Boolean*/ preserveDom){
		// summary:
		//		Destroy the ContentPane and its contents

		// if we have multiple controllers destroying us, bail after the first
		if(this._beingDestroyed){
			return;
		}
		this.inherited(arguments);
	},

	resize: function(changeSize, resultSize){
		// summary:
		//		See `dijit.layout._LayoutWidget.resize` for description.
		//		Although ContentPane doesn't extend _LayoutWidget, it does implement
		//		the same API.

		// For the TabContainer --> BorderContainer --> ContentPane case, _onShow() is
		// never called, so resize() is our trigger to do the initial href download.
		if(!this._wasShown){
			this._onShow();
		}

		this._resizeCalled = true;

		// Set margin box size, unless it wasn't specified, in which case use current size.
		if(changeSize){
			dojo.marginBox(this.domNode, changeSize);
		}

		// Compute content box size of containerNode in case we [later] need to size our single child.
		var cn = this.containerNode;
		if(cn === this.domNode){
			// If changeSize or resultSize was passed to this method and this.containerNode ==
			// this.domNode then we can compute the content-box size without querying the node,
			// which is more reliable (similar to LayoutWidget.resize) (see for example #9449).
			var mb = resultSize || {};
			dojo.mixin(mb, changeSize || {}); // changeSize overrides resultSize
			if(!("h" in mb) || !("w" in mb)){
				mb = dojo.mixin(dojo.marginBox(cn), mb); // just use dojo.marginBox() to fill in missing values
			}
			this._contentBox = dijit.layout.marginBox2contentBox(cn, mb);
		}else{
			this._contentBox = dojo.contentBox(cn);
		}

		// Make my children layout, or size my single child widget
		this._layoutChildren();
	},

	_isShown: function(){
		// summary:
		//		Returns true if the content is currently shown.
		// description:
		//		If I am a child of a layout widget then it actually returns true if I've ever been visible,
		//		not whether I'm currently visible, since that's much faster than tracing up the DOM/widget
		//		tree every call, and at least solves the performance problem on page load by deferring loading
		//		hidden ContentPanes until they are first shown

		if(this._childOfLayoutWidget){
			// If we are TitlePane, etc - we return that only *IF* we've been resized
			if(this._resizeCalled && "open" in this){
				return this.open;
			}
			return this._resizeCalled;
		}else if("open" in this){
			return this.open;		// for TitlePane, etc.
		}else{
			// TODO: with _childOfLayoutWidget check maybe this branch no longer necessary?
			var node = this.domNode;
			return (node.style.display != 'none') && (node.style.visibility != 'hidden') && !dojo.hasClass(node, "dijitHidden");
		}
	},

	_onShow: function(){
		// summary:
		//		Called when the ContentPane is made visible
		// description:
		//		For a plain ContentPane, this is called on initialization, from startup().
		//		If the ContentPane is a hidden pane of a TabContainer etc., then it's
		//		called whenever the pane is made visible.
		//
		//		Does necessary processing, including href download and layout/resize of
		//		child widget(s)

		if(this.href){
			if(!this._xhrDfd && // if there's an href that isn't already being loaded
				(!this.isLoaded || this._hrefChanged || this.refreshOnShow)
			){
				this.refresh();
			}
		}else{
			// If we are the child of a layout widget then the layout widget will call resize() on
			// us, and then we will size our child/children.   Otherwise, we need to do it now.
			if(!this._childOfLayoutWidget && this._needLayout){
				// If a layout has been scheduled for when we become visible, do it now
				this._layoutChildren();
			}
		}

		this.inherited(arguments);

		// Need to keep track of whether ContentPane has been shown (which is different than
		// whether or not it's currently visible).
		this._wasShown = true;
	},

	refresh: function(){
		// summary:
		//		[Re]download contents of href and display
		// description:
		//		1. cancels any currently in-flight requests
		//		2. posts "loading..." message
		//		3. sends XHR to download new data

		// Cancel possible prior in-flight request
		this.cancel();

		this.onLoadDeferred = new dojo.Deferred(dojo.hitch(this, "cancel"));
		this._load();
		return this.onLoadDeferred;
	},

	_load: function(){
		// summary:
		//		Load/reload the href specified in this.href

		// display loading message
		this._setContent(this.onDownloadStart(), true);

		var self = this;
		var getArgs = {
			preventCache: (this.preventCache || this.refreshOnShow),
			url: this.href,
			handleAs: "text"
		};
		if(dojo.isObject(this.ioArgs)){
			dojo.mixin(getArgs, this.ioArgs);
		}

		var hand = (this._xhrDfd = (this.ioMethod || dojo.xhrGet)(getArgs));

		hand.addCallback(function(html){
			try{
				self._isDownloaded = true;
				self._setContent(html, false);
				self.onDownloadEnd();
			}catch(err){
				self._onError('Content', err); // onContentError
			}
			delete self._xhrDfd;
			return html;
		});

		hand.addErrback(function(err){
			if(!hand.canceled){
				// show error message in the pane
				self._onError('Download', err); // onDownloadError
			}
			delete self._xhrDfd;
			return err;
		});

		// Remove flag saying that a load is needed
		delete this._hrefChanged;
	},

	_onLoadHandler: function(data){
		// summary:
		//		This is called whenever new content is being loaded
		this.isLoaded = true;
		try{
			this.onLoadDeferred.callback(data);
			this.onLoad(data);
		}catch(e){
			console.error('Error '+this.widgetId+' running custom onLoad code: ' + e.message);
		}
	},

	_onUnloadHandler: function(){
		// summary:
		//		This is called whenever the content is being unloaded
		this.isLoaded = false;
		try{
			this.onUnload();
		}catch(e){
			console.error('Error '+this.widgetId+' running custom onUnload code: ' + e.message);
		}
	},

	destroyDescendants: function(){
		// summary:
		//		Destroy all the widgets inside the ContentPane and empty containerNode

		// Make sure we call onUnload (but only when the ContentPane has real content)
		if(this.isLoaded){
			this._onUnloadHandler();
		}

		// Even if this.isLoaded == false there might still be a "Loading..." message
		// to erase, so continue...

		// For historical reasons we need to delete all widgets under this.containerNode,
		// even ones that the user has created manually.
		var setter = this._contentSetter;
		dojo.forEach(this.getChildren(), function(widget){
			if(widget.destroyRecursive){
				widget.destroyRecursive();
			}
		});
		if(setter){
			// Most of the widgets in setter.parseResults have already been destroyed, but
			// things like Menu that have been moved to <body> haven't yet
			dojo.forEach(setter.parseResults, function(widget){
				if(widget.destroyRecursive && widget.domNode && widget.domNode.parentNode == dojo.body()){
					widget.destroyRecursive();
				}
			});
			delete setter.parseResults;
		}

		// And then clear away all the DOM nodes
		dojo.html._emptyNode(this.containerNode);

		// Delete any state information we have about current contents
		delete this._singleChild;
	},

	_setContent: function(cont, isFakeContent){
		// summary:
		//		Insert the content into the container node

		// first get rid of child widgets
		this.destroyDescendants();

		// dojo.html.set will take care of the rest of the details
		// we provide an override for the error handling to ensure the widget gets the errors
		// configure the setter instance with only the relevant widget instance properties
		// NOTE: unless we hook into attr, or provide property setters for each property,
		// we need to re-configure the ContentSetter with each use
		var setter = this._contentSetter;
		if(! (setter && setter instanceof dojo.html._ContentSetter)){
			setter = this._contentSetter = new dojo.html._ContentSetter({
				node: this.containerNode,
				_onError: dojo.hitch(this, this._onError),
				onContentError: dojo.hitch(this, function(e){
					// fires if a domfault occurs when we are appending this.errorMessage
					// like for instance if domNode is a UL and we try append a DIV
					var errMess = this.onContentError(e);
					try{
						this.containerNode.innerHTML = errMess;
					}catch(e){
						console.error('Fatal '+this.id+' could not change content due to '+e.message, e);
					}
				})/*,
				_onError */
			});
		};

		var setterParams = dojo.mixin({
			cleanContent: this.cleanContent,
			extractContent: this.extractContent,
			parseContent: this.parseOnLoad,
			dir: this.dir,
			lang: this.lang
		}, this._contentSetterParams || {});

		dojo.mixin(setter, setterParams);

		setter.set( (dojo.isObject(cont) && cont.domNode) ? cont.domNode : cont );

		// setter params must be pulled afresh from the ContentPane each time
		delete this._contentSetterParams;

		if(!isFakeContent){
			// Startup each top level child widget (and they will start their children, recursively)
			dojo.forEach(this.getChildren(), function(child){
				// The parser has already called startup on all widgets *without* a getParent() method
				if(!this.parseOnLoad || child.getParent){
					child.startup();
				}
			}, this);

			// Call resize() on each of my child layout widgets,
			// or resize() on my single child layout widget...
			// either now (if I'm currently visible)
			// or when I become visible
			this._scheduleLayout();

			this._onLoadHandler(cont);
		}
	},

	_onError: function(type, err, consoleText){
		this.onLoadDeferred.errback(err);

		// shows user the string that is returned by on[type]Error
		// overide on[type]Error and return your own string to customize
		var errText = this['on' + type + 'Error'].call(this, err);
		if(consoleText){
			console.error(consoleText, err);
		}else if(errText){// a empty string won't change current content
			this._setContent(errText, true);
		}
	},

	_scheduleLayout: function(){
		// summary:
		//		Call resize() on each of my child layout widgets, either now
		//		(if I'm currently visible) or when I become visible
		if(this._isShown()){
			this._layoutChildren();
		}else{
			this._needLayout = true;
		}
	},

	_layoutChildren: function(){
		// summary:
		//		Since I am a Container widget, each of my children expects me to
		//		call resize() or layout() on them.
		// description:
		//		Should be called on initialization and also whenever we get new content
		//		(from an href, or from attr('content', ...))... but deferred until
		//		the ContentPane is visible

		if(this.doLayout){
			this._checkIfSingleChild();
		}

		if(this._singleChild && this._singleChild.resize){
			var cb = this._contentBox || dojo.contentBox(this.containerNode);

			// note: if widget has padding this._contentBox will have l and t set,
			// but don't pass them to resize() or it will doubly-offset the child
			this._singleChild.resize({w: cb.w, h: cb.h});
		}else{
			// All my child widgets are independently sized (rather than matching my size),
			// but I still need to call resize() on each child to make it layout.
			dojo.forEach(this.getChildren(), function(widget){
				if(widget.resize){
					widget.resize();
				}
			});
		}
		delete this._needLayout;
	},

	// EVENT's, should be overide-able
	onLoad: function(data){
		// summary:
		//		Event hook, is called after everything is loaded and widgetified
		// tags:
		//		callback
	},

	onUnload: function(){
		// summary:
		//		Event hook, is called before old content is cleared
		// tags:
		//		callback
	},

	onDownloadStart: function(){
		// summary:
		//		Called before download starts.
		// description:
		//		The string returned by this function will be the html
		//		that tells the user we are loading something.
		//		Override with your own function if you want to change text.
		// tags:
		//		extension
		return this.loadingMessage;
	},

	onContentError: function(/*Error*/ error){
		// summary:
		//		Called on DOM faults, require faults etc. in content.
		//
		//		In order to display an error message in the pane, return
		//		the error message from this method, as an HTML string.
		//
		//		By default (if this method is not overriden), it returns
		//		nothing, so the error message is just printed to the console.
		// tags:
		//		extension
	},

	onDownloadError: function(/*Error*/ error){
		// summary:
		//		Called when download error occurs.
		//
		//		In order to display an error message in the pane, return
		//		the error message from this method, as an HTML string.
		//
		//		Default behavior (if this method is not overriden) is to display
		//		the error message inside the pane.
		// tags:
		//		extension
		return this.errorMessage;
	},

	onDownloadEnd: function(){
		// summary:
		//		Called when download is finished.
		// tags:
		//		callback
	}
});

}

if(!dojo._hasResource["dijit.TooltipDialog"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.TooltipDialog"] = true;
dojo.provide("dijit.TooltipDialog");






dojo.declare(
		"dijit.TooltipDialog",
		[dijit.layout.ContentPane, dijit._Templated, dijit.form._FormMixin, dijit._DialogMixin],
		{
			// summary:
			//		Pops up a dialog that appears like a Tooltip

			// title: String
			// 		Description of tooltip dialog (required for a11y)
			title: "",

			// doLayout: [protected] Boolean
			//		Don't change this parameter from the default value.
			//		This ContentPane parameter doesn't make sense for TooltipDialog, since TooltipDialog
			//		is never a child of a layout container, nor can you specify the size of
			//		TooltipDialog in order to control the size of an inner widget.
			doLayout: false,

			// autofocus: Boolean
			// 		A Toggle to modify the default focus behavior of a Dialog, which
			// 		is to focus on the first dialog element after opening the dialog.
			//		False will disable autofocusing. Default: true
			autofocus: true,

			// baseClass: [protected] String
			//		The root className to use for the various states of this widget
			baseClass: "dijitTooltipDialog",

			// _firstFocusItem: [private] [readonly] DomNode
			//		The pointer to the first focusable node in the dialog.
			//		Set by `dijit._DialogMixin._getFocusItems`.
			_firstFocusItem: null,

			// _lastFocusItem: [private] [readonly] DomNode
			//		The pointer to which node has focus prior to our dialog.
			//		Set by `dijit._DialogMixin._getFocusItems`.
			_lastFocusItem: null,

			templateString: dojo.cache("dijit", "templates/TooltipDialog.html", "<div waiRole=\"presentation\">\n\t<div class=\"dijitTooltipContainer\" waiRole=\"presentation\">\n\t\t<div class =\"dijitTooltipContents dijitTooltipFocusNode\" dojoAttachPoint=\"containerNode\" tabindex=\"-1\" waiRole=\"dialog\"></div>\n\t</div>\n\t<div class=\"dijitTooltipConnector\" waiRole=\"presentation\"></div>\n</div>\n"),

			postCreate: function(){
				this.inherited(arguments);
				this.connect(this.containerNode, "onkeypress", "_onKey");
				this.containerNode.title = this.title;
			},

			orient: function(/*DomNode*/ node, /*String*/ aroundCorner, /*String*/ corner){
				// summary:
				//		Configure widget to be displayed in given position relative to the button.
				//		This is called from the dijit.popup code, and should not be called
				//		directly.
				// tags:
				//		protected
				var c = this._currentOrientClass;
				if(c){
					dojo.removeClass(this.domNode, c);
				}
				c = "dijitTooltipAB"+(corner.charAt(1) == 'L'?"Left":"Right")+" dijitTooltip"+(corner.charAt(0) == 'T' ? "Below" : "Above");
				dojo.addClass(this.domNode, c);
				this._currentOrientClass = c;
			},

			onOpen: function(/*Object*/ pos){
				// summary:
				//		Called when dialog is displayed.
				//		This is called from the dijit.popup code, and should not be called directly.
				// tags:
				//		protected

				this.orient(this.domNode,pos.aroundCorner, pos.corner);
				this._onShow(); // lazy load trigger

				if(this.autofocus){
					this._getFocusItems(this.containerNode);
					dijit.focus(this._firstFocusItem);
				}
			},

			onClose: function(){
				// summary:
				//		Called when dialog is hidden.
				//		This is called from the dijit.popup code, and should not be called directly.
				// tags:
				//		protected
				this.onHide();
			},

			_onKey: function(/*Event*/ evt){
				// summary:
				//		Handler for keyboard events
				// description:
				//		Keep keyboard focus in dialog; close dialog on escape key
				// tags:
				//		private

				var node = evt.target;
				var dk = dojo.keys;
				if(evt.charOrCode === dk.TAB){
					this._getFocusItems(this.containerNode);
				}
				var singleFocusItem = (this._firstFocusItem == this._lastFocusItem);
				if(evt.charOrCode == dk.ESCAPE){
					// Use setTimeout to avoid crash on IE, see #10396.
					setTimeout(dojo.hitch(this, "onCancel"), 0);
					dojo.stopEvent(evt);
				}else if(node == this._firstFocusItem && evt.shiftKey && evt.charOrCode === dk.TAB){
					if(!singleFocusItem){
						dijit.focus(this._lastFocusItem); // send focus to last item in dialog
					}
					dojo.stopEvent(evt);
				}else if(node == this._lastFocusItem && evt.charOrCode === dk.TAB && !evt.shiftKey){
					if(!singleFocusItem){
						dijit.focus(this._firstFocusItem); // send focus to first item in dialog
					}
					dojo.stopEvent(evt);
				}else if(evt.charOrCode === dk.TAB){
					// we want the browser's default tab handling to move focus
					// but we don't want the tab to propagate upwards
					evt.stopPropagation();
				}
			}
		}
	);

}

if(!dojo._hasResource["dijit.Dialog"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Dialog"] = true;
dojo.provide("dijit.Dialog");















/*=====
dijit._underlay = function(kwArgs){
	// summary:
	//		A shared instance of a `dijit.DialogUnderlay`
	//
	// description:
	//		A shared instance of a `dijit.DialogUnderlay` created and
	//		used by `dijit.Dialog`, though never created until some Dialog
	//		or subclass thereof is shown.
};
=====*/

dojo.declare(
	"dijit._DialogBase",
	[dijit._Templated, dijit.form._FormMixin, dijit._DialogMixin, dijit._CssStateMixin],
	{
		// summary:
		//		A modal dialog Widget
		//
		// description:
		//		Pops up a modal dialog window, blocking access to the screen
		//		and also graying out the screen Dialog is extended from
		//		ContentPane so it supports all the same parameters (href, etc.)
		//
		// example:
		// |	<div dojoType="dijit.Dialog" href="test.html"></div>
		//
		// example:
		// |	var foo = new dijit.Dialog({ title: "test dialog", content: "test content" };
		// |	dojo.body().appendChild(foo.domNode);
		// |	foo.startup();

		templateString: dojo.cache("dijit", "templates/Dialog.html", "<div class=\"dijitDialog\" tabindex=\"-1\" waiRole=\"dialog\" waiState=\"labelledby-${id}_title\">\n\t<div dojoAttachPoint=\"titleBar\" class=\"dijitDialogTitleBar\">\n\t<span dojoAttachPoint=\"titleNode\" class=\"dijitDialogTitle\" id=\"${id}_title\"></span>\n\t<span dojoAttachPoint=\"closeButtonNode\" class=\"dijitDialogCloseIcon\" dojoAttachEvent=\"onclick: onCancel\" title=\"${buttonCancel}\">\n\t\t<span dojoAttachPoint=\"closeText\" class=\"closeText\" title=\"${buttonCancel}\">x</span>\n\t</span>\n\t</div>\n\t\t<div dojoAttachPoint=\"containerNode\" class=\"dijitDialogPaneContent\"></div>\n</div>\n"),
		
		baseClass: "dijitDialog",
		
		cssStateNodes: {
			closeButtonNode: "dijitDialogCloseIcon"
		},

		attributeMap: dojo.delegate(dijit._Widget.prototype.attributeMap, {
			title: [
				{ node: "titleNode", type: "innerHTML" },
				{ node: "titleBar", type: "attribute" }
			],
			"aria-describedby":""
		}),

		// open: Boolean
		//		True if Dialog is currently displayed on screen.
		open: false,

		// duration: Integer
		//		The time in milliseconds it takes the dialog to fade in and out
		duration: dijit.defaultDuration,

		// refocus: Boolean
		// 		A Toggle to modify the default focus behavior of a Dialog, which
		// 		is to re-focus the element which had focus before being opened.
		//		False will disable refocusing. Default: true
		refocus: true,

		// autofocus: Boolean
		// 		A Toggle to modify the default focus behavior of a Dialog, which
		// 		is to focus on the first dialog element after opening the dialog.
		//		False will disable autofocusing. Default: true
		autofocus: true,

		// _firstFocusItem: [private] [readonly] DomNode
		//		The pointer to the first focusable node in the dialog.
		//		Set by `dijit._DialogMixin._getFocusItems`.
		_firstFocusItem: null,

		// _lastFocusItem: [private] [readonly] DomNode
		//		The pointer to which node has focus prior to our dialog.
		//		Set by `dijit._DialogMixin._getFocusItems`.
		_lastFocusItem: null,

		// doLayout: [protected] Boolean
		//		Don't change this parameter from the default value.
		//		This ContentPane parameter doesn't make sense for Dialog, since Dialog
		//		is never a child of a layout container, nor can you specify the size of
		//		Dialog in order to control the size of an inner widget.
		doLayout: false,

		// draggable: Boolean
		//		Toggles the moveable aspect of the Dialog. If true, Dialog
		//		can be dragged by it's title. If false it will remain centered
		//		in the viewport.
		draggable: true,

		//aria-describedby: String
		//		Allows the user to add an aria-describedby attribute onto the dialog.   The value should
		//		be the id of the container element of text that describes the dialog purpose (usually
		//		the first text in the dialog).
		//		<div dojoType="dijit.Dialog" aria-describedby="intro" .....>
		//			<div id="intro">Introductory text</div>
		//			<div>rest of dialog contents</div>
		//		</div>
		"aria-describedby":"",

		postMixInProperties: function(){
			var _nlsResources = dojo.i18n.getLocalization("dijit", "common");
			dojo.mixin(this, _nlsResources);
			this.inherited(arguments);
		},

		postCreate: function(){
			dojo.style(this.domNode, {
				display: "none",
				position:"absolute"
			});
			dojo.body().appendChild(this.domNode);

			this.inherited(arguments);

			this.connect(this, "onExecute", "hide");
			this.connect(this, "onCancel", "hide");
			this._modalconnects = [];
		},

		onLoad: function(){
			// summary:
			//		Called when data has been loaded from an href.
			//		Unlike most other callbacks, this function can be connected to (via `dojo.connect`)
			//		but should *not* be overriden.
			// tags:
			//		callback

			// when href is specified we need to reposition the dialog after the data is loaded
			// and find the focusable elements
			this._position();
			if(this.autofocus){
				this._getFocusItems(this.domNode);
				dijit.focus(this._firstFocusItem);
			}
			this.inherited(arguments);
		},

		_endDrag: function(e){
			// summary:
			//		Called after dragging the Dialog. Saves the position of the dialog in the viewport.
			// tags:
			//		private
			if(e && e.node && e.node === this.domNode){
				this._relativePosition = dojo.position(e.node);
			}
		},

		_setup: function(){
			// summary:
			//		Stuff we need to do before showing the Dialog for the first
			//		time (but we defer it until right beforehand, for
			//		performance reasons).
			// tags:
			//		private

			var node = this.domNode;

			if(this.titleBar && this.draggable){
				this._moveable = (dojo.isIE == 6) ?
					new dojo.dnd.TimedMoveable(node, { handle: this.titleBar }) :	// prevent overload, see #5285
					new dojo.dnd.Moveable(node, { handle: this.titleBar, timeout: 0 });
				dojo.subscribe("/dnd/move/stop",this,"_endDrag");
			}else{
				dojo.addClass(node,"dijitDialogFixed");
			}

			this.underlayAttrs = {
				dialogId: this.id,
				"class": dojo.map(this["class"].split(/\s/), function(s){ return s+"_underlay"; }).join(" ")
			};

			this._fadeIn = dojo.fadeIn({
				node: node,
				duration: this.duration,
				beforeBegin: dojo.hitch(this, function(){
					var underlay = dijit._underlay;
					if(!underlay){
						underlay = dijit._underlay = new dijit.DialogUnderlay(this.underlayAttrs);
					}else{
						underlay.set(this.underlayAttrs);
					}

					var ds = dijit._dialogStack,
						zIndex = 948 + ds.length*2;
					if(ds.length == 1){	// first dialog
						underlay.show();
					}
					dojo.style(dijit._underlay.domNode, 'zIndex', zIndex);
					dojo.style(this.domNode, 'zIndex', zIndex + 1);
				}),
				onEnd: dojo.hitch(this, function(){
					if(this.autofocus){
						// find focusable Items each time dialog is shown since if dialog contains a widget the
						// first focusable items can change
						this._getFocusItems(this.domNode);
						dijit.focus(this._firstFocusItem);
					}
				})
			 });

			this._fadeOut = dojo.fadeOut({
				node: node,
				duration: this.duration,
				onEnd: dojo.hitch(this, function(){
					node.style.display = "none";

					// Restore the previous dialog in the stack, or if this is the only dialog
					// then restore to original page
					var ds = dijit._dialogStack;
					if(ds.length == 0){
						dijit._underlay.hide();
					}else{
						dojo.style(dijit._underlay.domNode, 'zIndex', 948 + ds.length*2);
						dijit._underlay.set(ds[ds.length-1].underlayAttrs);
					}

					// Restore focus to wherever it was before this dialog was displayed
					if(this.refocus){
						var focus = this._savedFocus;

						// If we are returning control to a previous dialog but for some reason
						// that dialog didn't have a focused field, set focus to first focusable item.
						// This situation could happen if two dialogs appeared at nearly the same time,
						// since a dialog doesn't set it's focus until the fade-in is finished.
						if(ds.length > 0){
							var pd = ds[ds.length-1];
							if(!dojo.isDescendant(focus.node, pd.domNode)){
								pd._getFocusItems(pd.domNode);
								focus = pd._firstFocusItem;
							}
						}

						dijit.focus(focus);
					}
				})
			 });
		},

		uninitialize: function(){
			var wasPlaying = false;
			if(this._fadeIn && this._fadeIn.status() == "playing"){
				wasPlaying = true;
				this._fadeIn.stop();
			}
			if(this._fadeOut && this._fadeOut.status() == "playing"){
				wasPlaying = true;
				this._fadeOut.stop();
			}
			
			// Hide the underlay, unless the underlay widget has already been destroyed
			// because we are being called during page unload (when all widgets are destroyed)
			if((this.open || wasPlaying) && !dijit._underlay._destroyed){
				dijit._underlay.hide();
			}

			if(this._moveable){
				this._moveable.destroy();
			}
			this.inherited(arguments);
		},

		_size: function(){
			// summary:
			// 		If necessary, shrink dialog contents so dialog fits in viewport
			// tags:
			//		private

			this._checkIfSingleChild();

			// If we resized the dialog contents earlier, reset them back to original size, so
			// that if the user later increases the viewport size, the dialog can display w/out a scrollbar.
			// Need to do this before the dojo.marginBox(this.domNode) call below.
			if(this._singleChild){
				if(this._singleChildOriginalStyle){
					this._singleChild.domNode.style.cssText = this._singleChildOriginalStyle;
				}
				delete this._singleChildOriginalStyle;
			}else{
				dojo.style(this.containerNode, {
					width:"auto",
					height:"auto"
				});
			}

			var mb = dojo.marginBox(this.domNode);
			var viewport = dojo.window.getBox();
			if(mb.w >= viewport.w || mb.h >= viewport.h){
				// Reduce size of dialog contents so that dialog fits in viewport

				var w = Math.min(mb.w, Math.floor(viewport.w * 0.75)),
					h = Math.min(mb.h, Math.floor(viewport.h * 0.75));

				if(this._singleChild && this._singleChild.resize){
					this._singleChildOriginalStyle = this._singleChild.domNode.style.cssText;
					this._singleChild.resize({w: w, h: h});
				}else{
					dojo.style(this.containerNode, {
						width: w + "px",
						height: h + "px",
						overflow: "auto",
						position: "relative"	// workaround IE bug moving scrollbar or dragging dialog
					});
				}
			}else{
				if(this._singleChild && this._singleChild.resize){
					this._singleChild.resize();
				}
			}
		},

		_position: function(){
			// summary:
			//		Position modal dialog in the viewport. If no relative offset
			//		in the viewport has been determined (by dragging, for instance),
			//		center the node. Otherwise, use the Dialog's stored relative offset,
			//		and position the node to top: left: values based on the viewport.
			// tags:
			//		private
			if(!dojo.hasClass(dojo.body(),"dojoMove")){
				var node = this.domNode,
					viewport = dojo.window.getBox(),
					p = this._relativePosition,
					bb = p ? null : dojo._getBorderBox(node),
					l = Math.floor(viewport.l + (p ? p.x : (viewport.w - bb.w) / 2)),
					t = Math.floor(viewport.t + (p ? p.y : (viewport.h - bb.h) / 2))
				;
				dojo.style(node,{
					left: l + "px",
					top: t + "px"
				});
			}
		},

		_onKey: function(/*Event*/ evt){
			// summary:
			//		Handles the keyboard events for accessibility reasons
			// tags:
			//		private

			var ds = dijit._dialogStack;
			if(ds[ds.length-1] != this){
				// console.debug(this.id + ': skipping because', this, 'is not the active dialog');
				return;
			}

			if(evt.charOrCode){
				var dk = dojo.keys;
				var node = evt.target;
				if(evt.charOrCode === dk.TAB){
					this._getFocusItems(this.domNode);
				}
				var singleFocusItem = (this._firstFocusItem == this._lastFocusItem);
				// see if we are shift-tabbing from first focusable item on dialog
				if(node == this._firstFocusItem && evt.shiftKey && evt.charOrCode === dk.TAB){
					if(!singleFocusItem){
						dijit.focus(this._lastFocusItem); // send focus to last item in dialog
					}
					dojo.stopEvent(evt);
				}else if(node == this._lastFocusItem && evt.charOrCode === dk.TAB && !evt.shiftKey){
					if(!singleFocusItem){
						dijit.focus(this._firstFocusItem); // send focus to first item in dialog
					}
					dojo.stopEvent(evt);
				}else{
					// see if the key is for the dialog
					while(node){
						if(node == this.domNode || dojo.hasClass(node, "dijitPopup")){
							if(evt.charOrCode == dk.ESCAPE){
								this.onCancel();
							}else{
								return; // just let it go
							}
						}
						node = node.parentNode;
					}
					// this key is for the disabled document window
					if(evt.charOrCode !== dk.TAB){ // allow tabbing into the dialog for a11y
						dojo.stopEvent(evt);
					// opera won't tab to a div
					}else if(!dojo.isOpera){
						try{
							this._firstFocusItem.focus();
						}catch(e){ /*squelch*/ }
					}
				}
			}
		},

		show: function(){
			// summary:
			//		Display the dialog
			if(this.open){ return; }

			// first time we show the dialog, there's some initialization stuff to do
			if(!this._alreadyInitialized){
				this._setup();
				this._alreadyInitialized=true;
			}

			if(this._fadeOut.status() == "playing"){
				this._fadeOut.stop();
			}

			this._modalconnects.push(dojo.connect(window, "onscroll", this, "layout"));
			this._modalconnects.push(dojo.connect(window, "onresize", this, function(){
				// IE gives spurious resize events and can actually get stuck
				// in an infinite loop if we don't ignore them
				var viewport = dojo.window.getBox();
				if(!this._oldViewport ||
						viewport.h != this._oldViewport.h ||
						viewport.w != this._oldViewport.w){
					this.layout();
					this._oldViewport = viewport;
				}
			}));
			this._modalconnects.push(dojo.connect(dojo.doc.documentElement, "onkeypress", this, "_onKey"));

			dojo.style(this.domNode, {
				opacity:0,
				display:""
			});

			this.open = true;
			this._onShow(); // lazy load trigger

			this._size();
			this._position();
			dijit._dialogStack.push(this);
			this._fadeIn.play();

			this._savedFocus = dijit.getFocus(this);
		},

		hide: function(){
			// summary:
			//		Hide the dialog

			// if we haven't been initialized yet then we aren't showing and we can just return
			// or if we aren't the active dialog, don't allow us to close yet
			var ds = dijit._dialogStack;
			if(!this._alreadyInitialized || this != ds[ds.length-1]){
				return;
			}

			if(this._fadeIn.status() == "playing"){
				this._fadeIn.stop();
			}

			// throw away current active dialog from stack -- making the previous dialog or the node on the original page active
			ds.pop();

			this._fadeOut.play();

			if(this._scrollConnected){
				this._scrollConnected = false;
			}
			dojo.forEach(this._modalconnects, dojo.disconnect);
			this._modalconnects = [];

			if(this._relativePosition){
				delete this._relativePosition;
			}
			this.open = false;

			this.onHide();
		},

		layout: function(){
			// summary:
			//		Position the Dialog and the underlay
			// tags:
			//		private
			if(this.domNode.style.display != "none"){
				if(dijit._underlay){	// avoid race condition during show()
					dijit._underlay.layout();
				}
				this._position();
			}
		},

		destroy: function(){
			dojo.forEach(this._modalconnects, dojo.disconnect);
			if(this.refocus && this.open){
				setTimeout(dojo.hitch(dijit,"focus",this._savedFocus), 25);
			}
			this.inherited(arguments);
		}
	}
);

dojo.declare(
	"dijit.Dialog",
	[dijit.layout.ContentPane, dijit._DialogBase],
	{}
);

// Stack of currenctly displayed dialogs, layered on top of each other
dijit._dialogStack = [];

// For back-compat.  TODO: remove in 2.0


}

if(!dojo._hasResource["dijit.form.FilteringSelect"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.FilteringSelect"] = true;
dojo.provide("dijit.form.FilteringSelect");



dojo.declare(
	"dijit.form.FilteringSelect",
	[dijit.form.MappedTextBox, dijit.form.ComboBoxMixin],
	{
		// summary:
		//		An enhanced version of the HTML SELECT tag, populated dynamically
		//
		// description:
		//		An enhanced version of the HTML SELECT tag, populated dynamically. It works
		//		very nicely with very large data sets because it can load and page data as needed.
		//		It also resembles ComboBox, but does not allow values outside of the provided ones.
		//		If OPTION tags are used as the data provider via markup, then the
		//		OPTION tag's child text node is used as the displayed value when selected
		//		while the OPTION tag's value attribute is used as the widget value on form submit.
		//		To set the default value when using OPTION tags, specify the selected
		//		attribute on 1 of the child OPTION tags.
		//
		//		Similar features:
		//			- There is a drop down list of possible values.
		//			- You can only enter a value from the drop down list.  (You can't
		//				enter an arbitrary value.)
		//			- The value submitted with the form is the hidden value (ex: CA),
		//				not the displayed value a.k.a. label (ex: California)
		//
		//		Enhancements over plain HTML version:
		//			- If you type in some text then it will filter down the list of
		//				possible values in the drop down list.
		//			- List can be specified either as a static list or via a javascript
		//				function (that can get the list from a server)

		_isvalid: true,

		// required: Boolean
		//		True (default) if user is required to enter a value into this field.
		required: true,

		_lastDisplayedValue: "",

		isValid: function(){
			// Overrides ValidationTextBox.isValid()
			return this._isvalid || (!this.required && this.get('displayedValue') == ""); // #5974
		},

		_refreshState: function(){
			if(!this.searchTimer){ // state will be refreshed after results are returned
				this.inherited(arguments);
			}
		},

		_callbackSetLabel: function(	/*Array*/ result,
						/*Object*/ dataObject,
						/*Boolean?*/ priorityChange){
			// summary:
			//		Callback function that dynamically sets the label of the
			//		ComboBox

			// setValue does a synchronous lookup,
			// so it calls _callbackSetLabel directly,
			// and so does not pass dataObject
			// still need to test against _lastQuery in case it came too late
			if((dataObject && dataObject.query[this.searchAttr] != this._lastQuery) || (!dataObject && result.length && this.store.getIdentity(result[0]) != this._lastQuery)){
				return;
			}
			if(!result.length){
				//#3268: do nothing on bad input
				//#3285: change CSS to indicate error
				this.valueNode.value = "";
				dijit.form.TextBox.superclass._setValueAttr.call(this, "", priorityChange || (priorityChange === undefined && !this._focused));
				this._isvalid = false;
				this.validate(this._focused);
				this.item = null;
			}else{
				this.set('item', result[0], priorityChange);
			}
		},

		_openResultList: function(/*Object*/ results, /*Object*/ dataObject){
			// Overrides ComboBox._openResultList()

			// #3285: tap into search callback to see if user's query resembles a match
			if(dataObject.query[this.searchAttr] != this._lastQuery){
				return;
			}
			if(this.item === undefined){ // item == undefined for keyboard search
				this._isvalid = results.length != 0 || this._maxOptions != 0; // result.length==0 && maxOptions != 0 implies the nextChoices item selected but then the datastore returned 0 more entries
				this.validate(true);
			}
			dijit.form.ComboBoxMixin.prototype._openResultList.apply(this, arguments);
		},

		_getValueAttr: function(){
			// summary:
			//		Hook for attr('value') to work.

			// don't get the textbox value but rather the previously set hidden value.
			// Use this.valueNode.value which isn't always set for other MappedTextBox widgets until blur
			return this.valueNode.value;
		},

		_getValueField: function(){
			// Overrides ComboBox._getValueField()
			return "value";
		},

		_setValueAttr: function(/*String*/ value, /*Boolean?*/ priorityChange){
			// summary:
			//		Hook so attr('value', value) works.
			// description:
			//		Sets the value of the select.
			//		Also sets the label to the corresponding value by reverse lookup.
			if(!this._onChangeActive){ priorityChange = null; }
			this._lastQuery = value;

			if(value === null || value === ''){
				this._setDisplayedValueAttr('', priorityChange);
				return;
			}

			//#3347: fetchItemByIdentity if no keyAttr specified
			var self = this;
			this.store.fetchItemByIdentity({
				identity: value,
				onItem: function(item){
					self._callbackSetLabel(item? [item] : [], undefined, priorityChange);
				}
			});
		},

		_setItemAttr: function(/*item*/ item, /*Boolean?*/ priorityChange, /*String?*/ displayedValue){
			// summary:
			//		Set the displayed valued in the input box, and the hidden value
			//		that gets submitted, based on a dojo.data store item.
			// description:
			//		Users shouldn't call this function; they should be calling
			//		attr('item', value)
			// tags:
			//		private
			this._isvalid = true;
			this.inherited(arguments);
			this.valueNode.value = this.value;
			this._lastDisplayedValue = this.textbox.value;
		},

		_getDisplayQueryString: function(/*String*/ text){
			return text.replace(/([\\\*\?])/g, "\\$1");
		},

		_setDisplayedValueAttr: function(/*String*/ label, /*Boolean?*/ priorityChange){
			// summary:
			//		Hook so attr('displayedValue', label) works.
			// description:
			//		Sets textbox to display label. Also performs reverse lookup
			//		to set the hidden value.

			// When this is called during initialization it'll ping the datastore
			// for reverse lookup, and when that completes (after an XHR request)
			// will call setValueAttr()... but that shouldn't trigger an onChange()
			// event, even when it happens after creation has finished
			if(!this._created){
				priorityChange = false;
			}

			if(this.store){
				this._hideResultList();
				var query = dojo.clone(this.query); // #6196: populate query with user-specifics
				// escape meta characters of dojo.data.util.filter.patternToRegExp().
				this._lastQuery = query[this.searchAttr] = this._getDisplayQueryString(label);
				// if the label is not valid, the callback will never set it,
				// so the last valid value will get the warning textbox set the
				// textbox value now so that the impending warning will make
				// sense to the user
				this.textbox.value = label;
				this._lastDisplayedValue = label;
				var _this = this;
				var fetch = {
					query: query,
					queryOptions: {
						ignoreCase: this.ignoreCase,
						deep: true
					},
					onComplete: function(result, dataObject){
						_this._fetchHandle = null;
						dojo.hitch(_this, "_callbackSetLabel")(result, dataObject, priorityChange);
					},
					onError: function(errText){
						_this._fetchHandle = null;
						console.error('dijit.form.FilteringSelect: ' + errText);
						dojo.hitch(_this, "_callbackSetLabel")([], undefined, false);
					}
				};
				dojo.mixin(fetch, this.fetchProperties);
				this._fetchHandle = this.store.fetch(fetch);
			}
		},

		postMixInProperties: function(){
			this.inherited(arguments);
			this._isvalid = !this.required;
		},

		undo: function(){
			this.set('displayedValue', this._lastDisplayedValue);
		}
	}
);

}

if(!dojo._hasResource["dojo.date"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date"] = true;
dojo.provide("dojo.date");

/*=====
dojo.date = {
	// summary: Date manipulation utilities
}
=====*/

dojo.date.getDaysInMonth = function(/*Date*/dateObject){
	//	summary:
	//		Returns the number of days in the month used by dateObject
	var month = dateObject.getMonth();
	var days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if(month == 1 && dojo.date.isLeapYear(dateObject)){ return 29; } // Number
	return days[month]; // Number
}

dojo.date.isLeapYear = function(/*Date*/dateObject){
	//	summary:
	//		Determines if the year of the dateObject is a leap year
	//	description:
	//		Leap years are years with an additional day YYYY-02-29, where the
	//		year number is a multiple of four with the following exception: If
	//		a year is a multiple of 100, then it is only a leap year if it is
	//		also a multiple of 400. For example, 1900 was not a leap year, but
	//		2000 is one.

	var year = dateObject.getFullYear();
	return !(year%400) || (!(year%4) && !!(year%100)); // Boolean
}

// FIXME: This is not localized
dojo.date.getTimezoneName = function(/*Date*/dateObject){
	//	summary:
	//		Get the user's time zone as provided by the browser
	// dateObject:
	//		Needed because the timezone may vary with time (daylight savings)
	//	description:
	//		Try to get time zone info from toString or toLocaleString method of
	//		the Date object -- UTC offset is not a time zone.  See
	//		http://www.twinsun.com/tz/tz-link.htm Note: results may be
	//		inconsistent across browsers.

	var str = dateObject.toString(); // Start looking in toString
	var tz = ''; // The result -- return empty string if nothing found
	var match;

	// First look for something in parentheses -- fast lookup, no regex
	var pos = str.indexOf('(');
	if(pos > -1){
		tz = str.substring(++pos, str.indexOf(')'));
	}else{
		// If at first you don't succeed ...
		// If IE knows about the TZ, it appears before the year
		// Capital letters or slash before a 4-digit year 
		// at the end of string
		var pat = /([A-Z\/]+) \d{4}$/;
		if((match = str.match(pat))){
			tz = match[1];
		}else{
		// Some browsers (e.g. Safari) glue the TZ on the end
		// of toLocaleString instead of putting it in toString
			str = dateObject.toLocaleString();
			// Capital letters or slash -- end of string, 
			// after space
			pat = / ([A-Z\/]+)$/;
			if((match = str.match(pat))){
				tz = match[1];
			}
		}
	}

	// Make sure it doesn't somehow end up return AM or PM
	return (tz == 'AM' || tz == 'PM') ? '' : tz; // String
}

// Utility methods to do arithmetic calculations with Dates

dojo.date.compare = function(/*Date*/date1, /*Date?*/date2, /*String?*/portion){
	//	summary:
	//		Compare two date objects by date, time, or both.
	//	description:
	//  	Returns 0 if equal, positive if a > b, else negative.
	//	date1:
	//		Date object
	//	date2:
	//		Date object.  If not specified, the current Date is used.
	//	portion:
	//		A string indicating the "date" or "time" portion of a Date object.
	//		Compares both "date" and "time" by default.  One of the following:
	//		"date", "time", "datetime"

	// Extra step required in copy for IE - see #3112
	date1 = new Date(+date1);
	date2 = new Date(+(date2 || new Date()));

	if(portion == "date"){
		// Ignore times and compare dates.
		date1.setHours(0, 0, 0, 0);
		date2.setHours(0, 0, 0, 0);
	}else if(portion == "time"){
		// Ignore dates and compare times.
		date1.setFullYear(0, 0, 0);
		date2.setFullYear(0, 0, 0);
	}
	
	if(date1 > date2){ return 1; } // int
	if(date1 < date2){ return -1; } // int
	return 0; // int
};

dojo.date.add = function(/*Date*/date, /*String*/interval, /*int*/amount){
	//	summary:
	//		Add to a Date in intervals of different size, from milliseconds to years
	//	date: Date
	//		Date object to start with
	//	interval:
	//		A string representing the interval.  One of the following:
	//			"year", "month", "day", "hour", "minute", "second",
	//			"millisecond", "quarter", "week", "weekday"
	//	amount:
	//		How much to add to the date.

	var sum = new Date(+date); // convert to Number before copying to accomodate IE (#3112)
	var fixOvershoot = false;
	var property = "Date";

	switch(interval){
		case "day":
			break;
		case "weekday":
			//i18n FIXME: assumes Saturday/Sunday weekend, but this is not always true.  see dojo.cldr.supplemental

			// Divide the increment time span into weekspans plus leftover days
			// e.g., 8 days is one 5-day weekspan / and two leftover days
			// Can't have zero leftover days, so numbers divisible by 5 get
			// a days value of 5, and the remaining days make up the number of weeks
			var days, weeks;
			var mod = amount % 5;
			if(!mod){
				days = (amount > 0) ? 5 : -5;
				weeks = (amount > 0) ? ((amount-5)/5) : ((amount+5)/5);
			}else{
				days = mod;
				weeks = parseInt(amount/5);
			}
			// Get weekday value for orig date param
			var strt = date.getDay();
			// Orig date is Sat / positive incrementer
			// Jump over Sun
			var adj = 0;
			if(strt == 6 && amount > 0){
				adj = 1;
			}else if(strt == 0 && amount < 0){
			// Orig date is Sun / negative incrementer
			// Jump back over Sat
				adj = -1;
			}
			// Get weekday val for the new date
			var trgt = strt + days;
			// New date is on Sat or Sun
			if(trgt == 0 || trgt == 6){
				adj = (amount > 0) ? 2 : -2;
			}
			// Increment by number of weeks plus leftover days plus
			// weekend adjustments
			amount = (7 * weeks) + days + adj;
			break;
		case "year":
			property = "FullYear";
			// Keep increment/decrement from 2/29 out of March
			fixOvershoot = true;
			break;
		case "week":
			amount *= 7;
			break;
		case "quarter":
			// Naive quarter is just three months
			amount *= 3;
			// fallthrough...
		case "month":
			// Reset to last day of month if you overshoot
			fixOvershoot = true;
			property = "Month";
			break;
//		case "hour":
//		case "minute":
//		case "second":
//		case "millisecond":
		default:
			property = "UTC"+interval.charAt(0).toUpperCase() + interval.substring(1) + "s";
	}

	if(property){
		sum["set"+property](sum["get"+property]()+amount);
	}

	if(fixOvershoot && (sum.getDate() < date.getDate())){
		sum.setDate(0);
	}

	return sum; // Date
};

dojo.date.difference = function(/*Date*/date1, /*Date?*/date2, /*String?*/interval){
	//	summary:
	//		Get the difference in a specific unit of time (e.g., number of
	//		months, weeks, days, etc.) between two dates, rounded to the
	//		nearest integer.
	//	date1:
	//		Date object
	//	date2:
	//		Date object.  If not specified, the current Date is used.
	//	interval:
	//		A string representing the interval.  One of the following:
	//			"year", "month", "day", "hour", "minute", "second",
	//			"millisecond", "quarter", "week", "weekday"
	//		Defaults to "day".

	date2 = date2 || new Date();
	interval = interval || "day";
	var yearDiff = date2.getFullYear() - date1.getFullYear();
	var delta = 1; // Integer return value

	switch(interval){
		case "quarter":
			var m1 = date1.getMonth();
			var m2 = date2.getMonth();
			// Figure out which quarter the months are in
			var q1 = Math.floor(m1/3) + 1;
			var q2 = Math.floor(m2/3) + 1;
			// Add quarters for any year difference between the dates
			q2 += (yearDiff * 4);
			delta = q2 - q1;
			break;
		case "weekday":
			var days = Math.round(dojo.date.difference(date1, date2, "day"));
			var weeks = parseInt(dojo.date.difference(date1, date2, "week"));
			var mod = days % 7;

			// Even number of weeks
			if(mod == 0){
				days = weeks*5;
			}else{
				// Weeks plus spare change (< 7 days)
				var adj = 0;
				var aDay = date1.getDay();
				var bDay = date2.getDay();

				weeks = parseInt(days/7);
				mod = days % 7;
				// Mark the date advanced by the number of
				// round weeks (may be zero)
				var dtMark = new Date(date1);
				dtMark.setDate(dtMark.getDate()+(weeks*7));
				var dayMark = dtMark.getDay();

				// Spare change days -- 6 or less
				if(days > 0){
					switch(true){
						// Range starts on Sat
						case aDay == 6:
							adj = -1;
							break;
						// Range starts on Sun
						case aDay == 0:
							adj = 0;
							break;
						// Range ends on Sat
						case bDay == 6:
							adj = -1;
							break;
						// Range ends on Sun
						case bDay == 0:
							adj = -2;
							break;
						// Range contains weekend
						case (dayMark + mod) > 5:
							adj = -2;
					}
				}else if(days < 0){
					switch(true){
						// Range starts on Sat
						case aDay == 6:
							adj = 0;
							break;
						// Range starts on Sun
						case aDay == 0:
							adj = 1;
							break;
						// Range ends on Sat
						case bDay == 6:
							adj = 2;
							break;
						// Range ends on Sun
						case bDay == 0:
							adj = 1;
							break;
						// Range contains weekend
						case (dayMark + mod) < 0:
							adj = 2;
					}
				}
				days += adj;
				days -= (weeks*2);
			}
			delta = days;
			break;
		case "year":
			delta = yearDiff;
			break;
		case "month":
			delta = (date2.getMonth() - date1.getMonth()) + (yearDiff * 12);
			break;
		case "week":
			// Truncate instead of rounding
			// Don't use Math.floor -- value may be negative
			delta = parseInt(dojo.date.difference(date1, date2, "day")/7);
			break;
		case "day":
			delta /= 24;
			// fallthrough
		case "hour":
			delta /= 60;
			// fallthrough
		case "minute":
			delta /= 60;
			// fallthrough
		case "second":
			delta /= 1000;
			// fallthrough
		case "millisecond":
			delta *= date2.getTime() - date1.getTime();
	}

	// Round for fractional values and DST leaps
	return Math.round(delta); // Number (integer)
};

}

if(!dojo._hasResource["dojo.cldr.supplemental"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.cldr.supplemental"] = true;
dojo.provide("dojo.cldr.supplemental");



dojo.cldr.supplemental.getFirstDayOfWeek = function(/*String?*/locale){
// summary: Returns a zero-based index for first day of the week
// description:
//		Returns a zero-based index for first day of the week, as used by the local (Gregorian) calendar.
//		e.g. Sunday (returns 0), or Monday (returns 1)

	// from http://www.unicode.org/cldr/data/common/supplemental/supplementalData.xml:supplementalData/weekData/firstDay
	var firstDay = {/*default is 1=Monday*/
		mv:5,
		af:6,bh:6,dj:6,dz:6,eg:6,er:6,et:6,iq:6,ir:6,jo:6,ke:6,kw:6,
		ly:6,ma:6,om:6,qa:6,sa:6,sd:6,so:6,tn:6,ye:6,
		ar:0,as:0,az:0,bw:0,ca:0,cn:0,fo:0,ge:0,gl:0,gu:0,hk:0,ie:0,
		il:0,'in':0,is:0,jm:0,jp:0,kg:0,kr:0,la:0,mh:0,mn:0,mo:0,mp:0,
		mt:0,nz:0,ph:0,pk:0,sg:0,sy:0,th:0,tt:0,tw:0,um:0,us:0,uz:0,
		vi:0,zw:0
// variant. do not use?		gb:0,
	};

	var country = dojo.cldr.supplemental._region(locale);
	var dow = firstDay[country];
	return (dow === undefined) ? 1 : dow; /*Number*/
};

dojo.cldr.supplemental._region = function(/*String?*/locale){
	locale = dojo.i18n.normalizeLocale(locale);
	var tags = locale.split('-');
	var region = tags[1];
	if(!region){
		// IE often gives language only (#2269)
		// Arbitrary mappings of language-only locales to a country:
		region = {de:"de", en:"us", es:"es", fi:"fi", fr:"fr", he:"il", hu:"hu", it:"it",
			ja:"jp", ko:"kr", nl:"nl", pt:"br", sv:"se", zh:"cn"}[tags[0]];
	}else if(region.length == 4){
		// The ISO 3166 country code is usually in the second position, unless a
		// 4-letter script is given. See http://www.ietf.org/rfc/rfc4646.txt
		region = tags[2];
	}
	return region;
}

dojo.cldr.supplemental.getWeekend = function(/*String?*/locale){
// summary: Returns a hash containing the start and end days of the weekend
// description:
//		Returns a hash containing the start and end days of the weekend according to local custom using locale,
//		or by default in the user's locale.
//		e.g. {start:6, end:0}

	// from http://www.unicode.org/cldr/data/common/supplemental/supplementalData.xml:supplementalData/weekData/weekend{Start,End}
	var weekendStart = {/*default is 6=Saturday*/
		'in':0,
		af:4,dz:4,ir:4,om:4,sa:4,ye:4,
		ae:5,bh:5,eg:5,il:5,iq:5,jo:5,kw:5,ly:5,ma:5,qa:5,sd:5,sy:5,tn:5
	};

	var weekendEnd = {/*default is 0=Sunday*/
		af:5,dz:5,ir:5,om:5,sa:5,ye:5,
		ae:6,bh:5,eg:6,il:6,iq:6,jo:6,kw:6,ly:6,ma:6,qa:6,sd:6,sy:6,tn:6
	};

	var country = dojo.cldr.supplemental._region(locale);
	var start = weekendStart[country];
	var end = weekendEnd[country];
	if(start === undefined){start=6;}
	if(end === undefined){end=0;}
	return {start:start, end:end}; /*Object {start,end}*/
};

}

if(!dojo._hasResource["dojo.date.locale"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date.locale"] = true;
dojo.provide("dojo.date.locale");

// Localization methods for Date.   Honor local customs using locale-dependent dojo.cldr data.







// Load the bundles containing localization information for
// names and formats


//NOTE: Everything in this module assumes Gregorian calendars.
// Other calendars will be implemented in separate modules.

(function(){
	// Format a pattern without literals
	function formatPattern(dateObject, bundle, options, pattern){
		return pattern.replace(/([a-z])\1*/ig, function(match){
			var s, pad,
				c = match.charAt(0),
				l = match.length,
				widthList = ["abbr", "wide", "narrow"];
			switch(c){
				case 'G':
					s = bundle[(l < 4) ? "eraAbbr" : "eraNames"][dateObject.getFullYear() < 0 ? 0 : 1];
					break;
				case 'y':
					s = dateObject.getFullYear();
					switch(l){
						case 1:
							break;
						case 2:
							if(!options.fullYear){
								s = String(s); s = s.substr(s.length - 2);
								break;
							}
							// fallthrough
						default:
							pad = true;
					}
					break;
				case 'Q':
				case 'q':
					s = Math.ceil((dateObject.getMonth()+1)/3);
//					switch(l){
//						case 1: case 2:
							pad = true;
//							break;
//						case 3: case 4: // unimplemented
//					}
					break;
				case 'M':
					var m = dateObject.getMonth();
					if(l<3){
						s = m+1; pad = true;
					}else{
						var propM = ["months", "format", widthList[l-3]].join("-");
						s = bundle[propM][m];
					}
					break;
				case 'w':
					var firstDay = 0;
					s = dojo.date.locale._getWeekOfYear(dateObject, firstDay); pad = true;
					break;
				case 'd':
					s = dateObject.getDate(); pad = true;
					break;
				case 'D':
					s = dojo.date.locale._getDayOfYear(dateObject); pad = true;
					break;
				case 'E':
					var d = dateObject.getDay();
					if(l<3){
						s = d+1; pad = true;
					}else{
						var propD = ["days", "format", widthList[l-3]].join("-");
						s = bundle[propD][d];
					}
					break;
				case 'a':
					var timePeriod = (dateObject.getHours() < 12) ? 'am' : 'pm';
					s = bundle['dayPeriods-format-wide-' + timePeriod];
					break;
				case 'h':
				case 'H':
				case 'K':
				case 'k':
					var h = dateObject.getHours();
					// strange choices in the date format make it impossible to write this succinctly
					switch (c){
						case 'h': // 1-12
							s = (h % 12) || 12;
							break;
						case 'H': // 0-23
							s = h;
							break;
						case 'K': // 0-11
							s = (h % 12);
							break;
						case 'k': // 1-24
							s = h || 24;
							break;
					}
					pad = true;
					break;
				case 'm':
					s = dateObject.getMinutes(); pad = true;
					break;
				case 's':
					s = dateObject.getSeconds(); pad = true;
					break;
				case 'S':
					s = Math.round(dateObject.getMilliseconds() * Math.pow(10, l-3)); pad = true;
					break;
				case 'v': // FIXME: don't know what this is. seems to be same as z?
				case 'z':
					// We only have one timezone to offer; the one from the browser
					s = dojo.date.locale._getZone(dateObject, true, options);
					if(s){break;}
					l=4;
					// fallthrough... use GMT if tz not available
				case 'Z':
					var offset = dojo.date.locale._getZone(dateObject, false, options);
					var tz = [
						(offset<=0 ? "+" : "-"),
						dojo.string.pad(Math.floor(Math.abs(offset)/60), 2),
						dojo.string.pad(Math.abs(offset)% 60, 2)
					];
					if(l==4){
						tz.splice(0, 0, "GMT");
						tz.splice(3, 0, ":");
					}
					s = tz.join("");
					break;
//				case 'Y': case 'u': case 'W': case 'F': case 'g': case 'A': case 'e':
//					console.log(match+" modifier unimplemented");
				default:
					throw new Error("dojo.date.locale.format: invalid pattern char: "+pattern);
			}
			if(pad){ s = dojo.string.pad(s, l); }
			return s;
		});
	}

/*=====
	dojo.date.locale.__FormatOptions = function(){
	//	selector: String
	//		choice of 'time','date' (default: date and time)
	//	formatLength: String
	//		choice of long, short, medium or full (plus any custom additions).  Defaults to 'short'
	//	datePattern:String
	//		override pattern with this string
	//	timePattern:String
	//		override pattern with this string
	//	am: String
	//		override strings for am in times
	//	pm: String
	//		override strings for pm in times
	//	locale: String
	//		override the locale used to determine formatting rules
	//	fullYear: Boolean
	//		(format only) use 4 digit years whenever 2 digit years are called for
	//	strict: Boolean
	//		(parse only) strict parsing, off by default
		this.selector = selector;
		this.formatLength = formatLength;
		this.datePattern = datePattern;
		this.timePattern = timePattern;
		this.am = am;
		this.pm = pm;
		this.locale = locale;
		this.fullYear = fullYear;
		this.strict = strict;
	}
=====*/

dojo.date.locale._getZone = function(/*Date*/dateObject, /*boolean*/getName, /*dojo.date.locale.__FormatOptions?*/options){
	// summary:
	//		Returns the zone (or offset) for the given date and options.  This
	//		is broken out into a separate function so that it can be overridden
	//		by timezone-aware code.
	//
	// dateObject:
	//		the date and/or time being formatted.
	//
	// getName:
	//		Whether to return the timezone string (if true), or the offset (if false)
	//
	// options:
	//		The options being used for formatting
	if(getName){
		return dojo.date.getTimezoneName(dateObject);
	}else{
		return dateObject.getTimezoneOffset();
	}
};


dojo.date.locale.format = function(/*Date*/dateObject, /*dojo.date.locale.__FormatOptions?*/options){
	// summary:
	//		Format a Date object as a String, using locale-specific settings.
	//
	// description:
	//		Create a string from a Date object using a known localized pattern.
	//		By default, this method formats both date and time from dateObject.
	//		Formatting patterns are chosen appropriate to the locale.  Different
	//		formatting lengths may be chosen, with "full" used by default.
	//		Custom patterns may be used or registered with translations using
	//		the dojo.date.locale.addCustomFormats method.
	//		Formatting patterns are implemented using [the syntax described at
	//		unicode.org](http://www.unicode.org/reports/tr35/tr35-4.html#Date_Format_Patterns)
	//
	// dateObject:
	//		the date and/or time to be formatted.  If a time only is formatted,
	//		the values in the year, month, and day fields are irrelevant.  The
	//		opposite is true when formatting only dates.

	options = options || {};

	var locale = dojo.i18n.normalizeLocale(options.locale),
		formatLength = options.formatLength || 'short',
		bundle = dojo.date.locale._getGregorianBundle(locale),
		str = [],
		sauce = dojo.hitch(this, formatPattern, dateObject, bundle, options);
	if(options.selector == "year"){
		return _processPattern(bundle["dateFormatItem-yyyy"] || "yyyy", sauce);
	}
	var pattern;
	if(options.selector != "date"){
		pattern = options.timePattern || bundle["timeFormat-"+formatLength];
		if(pattern){str.push(_processPattern(pattern, sauce));}
	}
	if(options.selector != "time"){
		pattern = options.datePattern || bundle["dateFormat-"+formatLength];
		if(pattern){str.push(_processPattern(pattern, sauce));}
	}

	return str.length == 1 ? str[0] : bundle["dateTimeFormat-"+formatLength].replace(/\{(\d+)\}/g,
		function(match, key){ return str[key]; }); // String
};

dojo.date.locale.regexp = function(/*dojo.date.locale.__FormatOptions?*/options){
	// summary:
	//		Builds the regular needed to parse a localized date

	return dojo.date.locale._parseInfo(options).regexp; // String
};

dojo.date.locale._parseInfo = function(/*dojo.date.locale.__FormatOptions?*/options){
	options = options || {};
	var locale = dojo.i18n.normalizeLocale(options.locale),
		bundle = dojo.date.locale._getGregorianBundle(locale),
		formatLength = options.formatLength || 'short',
		datePattern = options.datePattern || bundle["dateFormat-" + formatLength],
		timePattern = options.timePattern || bundle["timeFormat-" + formatLength],
		pattern;
	if(options.selector == 'date'){
		pattern = datePattern;
	}else if(options.selector == 'time'){
		pattern = timePattern;
	}else{
		pattern = bundle["dateTimeFormat-"+formatLength].replace(/\{(\d+)\}/g,
			function(match, key){ return [timePattern, datePattern][key]; });
	}

	var tokens = [],
		re = _processPattern(pattern, dojo.hitch(this, _buildDateTimeRE, tokens, bundle, options));
	return {regexp: re, tokens: tokens, bundle: bundle};
};

dojo.date.locale.parse = function(/*String*/value, /*dojo.date.locale.__FormatOptions?*/options){
	// summary:
	//		Convert a properly formatted string to a primitive Date object,
	//		using locale-specific settings.
	//
	// description:
	//		Create a Date object from a string using a known localized pattern.
	//		By default, this method parses looking for both date and time in the string.
	//		Formatting patterns are chosen appropriate to the locale.  Different
	//		formatting lengths may be chosen, with "full" used by default.
	//		Custom patterns may be used or registered with translations using
	//		the dojo.date.locale.addCustomFormats method.
	//	
	//		Formatting patterns are implemented using [the syntax described at
	//		unicode.org](http://www.unicode.org/reports/tr35/tr35-4.html#Date_Format_Patterns)
	//		When two digit years are used, a century is chosen according to a sliding 
	//		window of 80 years before and 20 years after present year, for both `yy` and `yyyy` patterns.
	//		year < 100CE requires strict mode.
	//
	// value:
	//		A string representation of a date

	var info = dojo.date.locale._parseInfo(options),
		tokens = info.tokens, bundle = info.bundle,
		re = new RegExp("^" + info.regexp + "$", info.strict ? "" : "i"),
		match = re.exec(value);

	if(!match){ return null; } // null

	var widthList = ['abbr', 'wide', 'narrow'],
		result = [1970,0,1,0,0,0,0], // will get converted to a Date at the end
		amPm = "",
		valid = dojo.every(match, function(v, i){
		if(!i){return true;}
		var token=tokens[i-1];
		var l=token.length;
		switch(token.charAt(0)){
			case 'y':
				if(l != 2 && options.strict){
					//interpret year literally, so '5' would be 5 A.D.
					result[0] = v;
				}else{
					if(v<100){
						v = Number(v);
						//choose century to apply, according to a sliding window
						//of 80 years before and 20 years after present year
						var year = '' + new Date().getFullYear(),
							century = year.substring(0, 2) * 100,
							cutoff = Math.min(Number(year.substring(2, 4)) + 20, 99),
							num = (v < cutoff) ? century + v : century - 100 + v;
						result[0] = num;
					}else{
						//we expected 2 digits and got more...
						if(options.strict){
							return false;
						}
						//interpret literally, so '150' would be 150 A.D.
						//also tolerate '1950', if 'yyyy' input passed to 'yy' format
						result[0] = v;
					}
				}
				break;
			case 'M':
				if(l>2){
					var months = bundle['months-format-' + widthList[l-3]].concat();
					if(!options.strict){
						//Tolerate abbreviating period in month part
						//Case-insensitive comparison
						v = v.replace(".","").toLowerCase();
						months = dojo.map(months, function(s){ return s.replace(".","").toLowerCase(); } );
					}
					v = dojo.indexOf(months, v);
					if(v == -1){
//						console.log("dojo.date.locale.parse: Could not parse month name: '" + v + "'.");
						return false;
					}
				}else{
					v--;
				}
				result[1] = v;
				break;
			case 'E':
			case 'e':
				var days = bundle['days-format-' + widthList[l-3]].concat();
				if(!options.strict){
					//Case-insensitive comparison
					v = v.toLowerCase();
					days = dojo.map(days, function(d){return d.toLowerCase();});
				}
				v = dojo.indexOf(days, v);
				if(v == -1){
//					console.log("dojo.date.locale.parse: Could not parse weekday name: '" + v + "'.");
					return false;
				}

				//TODO: not sure what to actually do with this input,
				//in terms of setting something on the Date obj...?
				//without more context, can't affect the actual date
				//TODO: just validate?
				break;
			case 'D':
				result[1] = 0;
				// fallthrough...
			case 'd':
				result[2] = v;
				break;
			case 'a': //am/pm
				var am = options.am || bundle['dayPeriods-format-wide-am'],
					pm = options.pm || bundle['dayPeriods-format-wide-pm'];
				if(!options.strict){
					var period = /\./g;
					v = v.replace(period,'').toLowerCase();
					am = am.replace(period,'').toLowerCase();
					pm = pm.replace(period,'').toLowerCase();
				}
				if(options.strict && v != am && v != pm){
//					console.log("dojo.date.locale.parse: Could not parse am/pm part.");
					return false;
				}

				// we might not have seen the hours field yet, so store the state and apply hour change later
				amPm = (v == pm) ? 'p' : (v == am) ? 'a' : '';
				break;
			case 'K': //hour (1-24)
				if(v == 24){ v = 0; }
				// fallthrough...
			case 'h': //hour (1-12)
			case 'H': //hour (0-23)
			case 'k': //hour (0-11)
				//TODO: strict bounds checking, padding
				if(v > 23){
//					console.log("dojo.date.locale.parse: Illegal hours value");
					return false;
				}

				//in the 12-hour case, adjusting for am/pm requires the 'a' part
				//which could come before or after the hour, so we will adjust later
				result[3] = v;
				break;
			case 'm': //minutes
				result[4] = v;
				break;
			case 's': //seconds
				result[5] = v;
				break;
			case 'S': //milliseconds
				result[6] = v;
//				break;
//			case 'w':
//TODO				var firstDay = 0;
//			default:
//TODO: throw?
//				console.log("dojo.date.locale.parse: unsupported pattern char=" + token.charAt(0));
		}
		return true;
	});

	var hours = +result[3];
	if(amPm === 'p' && hours < 12){
		result[3] = hours + 12; //e.g., 3pm -> 15
	}else if(amPm === 'a' && hours == 12){
		result[3] = 0; //12am -> 0
	}

	//TODO: implement a getWeekday() method in order to test 
	//validity of input strings containing 'EEE' or 'EEEE'...

	var dateObject = new Date(result[0], result[1], result[2], result[3], result[4], result[5], result[6]); // Date
	if(options.strict){
		dateObject.setFullYear(result[0]);
	}

	// Check for overflow.  The Date() constructor normalizes things like April 32nd...
	//TODO: why isn't this done for times as well?
	var allTokens = tokens.join(""),
		dateToken = allTokens.indexOf('d') != -1,
		monthToken = allTokens.indexOf('M') != -1;

	if(!valid ||
		(monthToken && dateObject.getMonth() > result[1]) ||
		(dateToken && dateObject.getDate() > result[2])){
		return null;
	}

	// Check for underflow, due to DST shifts.  See #9366
	// This assumes a 1 hour dst shift correction at midnight
	// We could compare the timezone offset after the shift and add the difference instead.
	if((monthToken && dateObject.getMonth() < result[1]) ||
		(dateToken && dateObject.getDate() < result[2])){
		dateObject = dojo.date.add(dateObject, "hour", 1);
	}

	return dateObject; // Date
};

function _processPattern(pattern, applyPattern, applyLiteral, applyAll){
	//summary: Process a pattern with literals in it

	// Break up on single quotes, treat every other one as a literal, except '' which becomes '
	var identity = function(x){return x;};
	applyPattern = applyPattern || identity;
	applyLiteral = applyLiteral || identity;
	applyAll = applyAll || identity;

	//split on single quotes (which escape literals in date format strings) 
	//but preserve escaped single quotes (e.g., o''clock)
	var chunks = pattern.match(/(''|[^'])+/g),
		literal = pattern.charAt(0) == "'";

	dojo.forEach(chunks, function(chunk, i){
		if(!chunk){
			chunks[i]='';
		}else{
			chunks[i]=(literal ? applyLiteral : applyPattern)(chunk.replace(/''/g, "'"));
			literal = !literal;
		}
	});
	return applyAll(chunks.join(''));
}

function _buildDateTimeRE(tokens, bundle, options, pattern){
	pattern = dojo.regexp.escapeString(pattern);
	if(!options.strict){ pattern = pattern.replace(" a", " ?a"); } // kludge to tolerate no space before am/pm
	return pattern.replace(/([a-z])\1*/ig, function(match){
		// Build a simple regexp.  Avoid captures, which would ruin the tokens list
		var s,
			c = match.charAt(0),
			l = match.length,
			p2 = '', p3 = '';
		if(options.strict){
			if(l > 1){ p2 = '0' + '{'+(l-1)+'}'; }
			if(l > 2){ p3 = '0' + '{'+(l-2)+'}'; }
		}else{
			p2 = '0?'; p3 = '0{0,2}';
		}
		switch(c){
			case 'y':
				s = '\\d{2,4}';
				break;
			case 'M':
				s = (l>2) ? '\\S+?' : p2+'[1-9]|1[0-2]';
				break;
			case 'D':
				s = p2+'[1-9]|'+p3+'[1-9][0-9]|[12][0-9][0-9]|3[0-5][0-9]|36[0-6]';
				break;
			case 'd':
				s = '3[01]|[12]\\d|'+p2+'[1-9]';
				break;
			case 'w':
				s = p2+'[1-9]|[1-4][0-9]|5[0-3]';
				break;
			case 'E':
				s = '\\S+';
				break;
			case 'h': //hour (1-12)
				s = p2+'[1-9]|1[0-2]';
				break;
			case 'k': //hour (0-11)
				s = p2+'\\d|1[01]';
				break;
			case 'H': //hour (0-23)
				s = p2+'\\d|1\\d|2[0-3]';
				break;
			case 'K': //hour (1-24)
				s = p2+'[1-9]|1\\d|2[0-4]';
				break;
			case 'm':
			case 's':
				s = '[0-5]\\d';
				break;
			case 'S':
				s = '\\d{'+l+'}';
				break;
			case 'a':
				var am = options.am || bundle['dayPeriods-format-wide-am'],
					pm = options.pm || bundle['dayPeriods-format-wide-pm'];
				if(options.strict){
					s = am + '|' + pm;
				}else{
					s = am + '|' + pm;
					if(am != am.toLowerCase()){ s += '|' + am.toLowerCase(); }
					if(pm != pm.toLowerCase()){ s += '|' + pm.toLowerCase(); }
					if(s.indexOf('.') != -1){ s += '|' + s.replace(/\./g, ""); }
				}
				s = s.replace(/\./g, "\\.");
				break;
			default:
			// case 'v':
			// case 'z':
			// case 'Z':
				s = ".*";
//				console.log("parse of date format, pattern=" + pattern);
		}

		if(tokens){ tokens.push(match); }

		return "(" + s + ")"; // add capture
	}).replace(/[\xa0 ]/g, "[\\s\\xa0]"); // normalize whitespace.  Need explicit handling of \xa0 for IE.
}
})();

(function(){
var _customFormats = [];
dojo.date.locale.addCustomFormats = function(/*String*/packageName, /*String*/bundleName){
	// summary:
	//		Add a reference to a bundle containing localized custom formats to be
	//		used by date/time formatting and parsing routines.
	//
	// description:
	//		The user may add custom localized formats where the bundle has properties following the
	//		same naming convention used by dojo.cldr: `dateFormat-xxxx` / `timeFormat-xxxx`
	//		The pattern string should match the format used by the CLDR.
	//		See dojo.date.locale.format() for details.
	//		The resources must be loaded by dojo.requireLocalization() prior to use

	_customFormats.push({pkg:packageName,name:bundleName});
};

dojo.date.locale._getGregorianBundle = function(/*String*/locale){
	var gregorian = {};
	dojo.forEach(_customFormats, function(desc){
		var bundle = dojo.i18n.getLocalization(desc.pkg, desc.name, locale);
		gregorian = dojo.mixin(gregorian, bundle);
	}, this);
	return gregorian; /*Object*/
};
})();

dojo.date.locale.addCustomFormats("dojo.cldr","gregorian");

dojo.date.locale.getNames = function(/*String*/item, /*String*/type, /*String?*/context, /*String?*/locale){
	// summary:
	//		Used to get localized strings from dojo.cldr for day or month names.
	//
	// item:
	//	'months' || 'days'
	// type:
	//	'wide' || 'narrow' || 'abbr' (e.g. "Monday", "Mon", or "M" respectively, in English)
	// context:
	//	'standAlone' || 'format' (default)
	// locale:
	//	override locale used to find the names

	var label,
		lookup = dojo.date.locale._getGregorianBundle(locale),
		props = [item, context, type];
	if(context == 'standAlone'){
		var key = props.join('-');
		label = lookup[key];
		// Fall back to 'format' flavor of name
		if(label[0] == 1){ label = undefined; } // kludge, in the absence of real aliasing support in dojo.cldr
	}
	props[1] = 'format';

	// return by copy so changes won't be made accidentally to the in-memory model
	return (label || lookup[props.join('-')]).concat(); /*Array*/
};

dojo.date.locale.isWeekend = function(/*Date?*/dateObject, /*String?*/locale){
	// summary:
	//	Determines if the date falls on a weekend, according to local custom.

	var weekend = dojo.cldr.supplemental.getWeekend(locale),
		day = (dateObject || new Date()).getDay();
	if(weekend.end < weekend.start){
		weekend.end += 7;
		if(day < weekend.start){ day += 7; }
	}
	return day >= weekend.start && day <= weekend.end; // Boolean
};

// These are used only by format and strftime.  Do they need to be public?  Which module should they go in?

dojo.date.locale._getDayOfYear = function(/*Date*/dateObject){
	// summary: gets the day of the year as represented by dateObject
	return dojo.date.difference(new Date(dateObject.getFullYear(), 0, 1, dateObject.getHours()), dateObject) + 1; // Number
};

dojo.date.locale._getWeekOfYear = function(/*Date*/dateObject, /*Number*/firstDayOfWeek){
	if(arguments.length == 1){ firstDayOfWeek = 0; } // Sunday

	var firstDayOfYear = new Date(dateObject.getFullYear(), 0, 1).getDay(),
		adj = (firstDayOfYear - firstDayOfWeek + 7) % 7,
		week = Math.floor((dojo.date.locale._getDayOfYear(dateObject) + adj - 1) / 7);

	// if year starts on the specified day, start counting weeks at 1
	if(firstDayOfYear == firstDayOfWeek){ week++; }

	return week; // Number
};

}

if(!dojo._hasResource["dijit.Calendar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Calendar"] = true;
dojo.provide("dijit.Calendar");









dojo.declare(
	"dijit.Calendar",
	[dijit._Widget, dijit._Templated, dijit._CssStateMixin],
	{
		// summary:
		//		A simple GUI for choosing a date in the context of a monthly calendar.
		//
		// description:
		//		A simple GUI for choosing a date in the context of a monthly calendar.
		//		This widget can't be used in a form because it doesn't serialize the date to an
		//		`<input>` field.  For a form element, use dijit.form.DateTextBox instead.
		//
		//		Note that the parser takes all dates attributes passed in the
		//		[RFC 3339 format](http://www.faqs.org/rfcs/rfc3339.html), e.g. `2005-06-30T08:05:00-07:00`
		//		so that they are serializable and locale-independent.
		//
		// example:
		//	|	var calendar = new dijit.Calendar({}, dojo.byId("calendarNode"));
		//
		// example:
		//	|	<div dojoType="dijit.Calendar"></div>

		templateString: dojo.cache("dijit", "templates/Calendar.html", "<table cellspacing=\"0\" cellpadding=\"0\" class=\"dijitCalendarContainer\" role=\"grid\" dojoAttachEvent=\"onkeypress: _onKeyPress\">\n\t<thead>\n\t\t<tr class=\"dijitReset dijitCalendarMonthContainer\" valign=\"top\">\n\t\t\t<th class='dijitReset dijitCalendarArrow' dojoAttachPoint=\"decrementMonth\">\n\t\t\t\t<img src=\"${_blankGif}\" alt=\"\" class=\"dijitCalendarIncrementControl dijitCalendarDecrease\" waiRole=\"presentation\"/>\n\t\t\t\t<span dojoAttachPoint=\"decreaseArrowNode\" class=\"dijitA11ySideArrow\">-</span>\n\t\t\t</th>\n\t\t\t<th class='dijitReset' colspan=\"5\">\n\t\t\t\t<div class=\"dijitVisible\">\n\t\t\t\t\t<div class=\"dijitPopup dijitMenu dijitMenuPassive dijitHidden\" dojoAttachPoint=\"monthDropDown\" dojoAttachEvent=\"onmouseup: _onMonthSelect, onmouseover: _onMenuHover, onmouseout: _onMenuHover\">\n\t\t\t\t\t\t<div class=\"dijitCalendarMonthLabelTemplate dijitCalendarMonthLabel\"></div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t<div dojoAttachPoint=\"monthLabelSpacer\" class=\"dijitSpacer\"></div>\n\t\t\t\t<div dojoAttachPoint=\"monthLabelNode\" class=\"dijitCalendarMonthLabel dijitInline dijitVisible\" dojoAttachEvent=\"onmousedown: _onMonthToggle\"></div>\n\t\t\t</th>\n\t\t\t<th class='dijitReset dijitCalendarArrow' dojoAttachPoint=\"incrementMonth\">\n\t\t\t\t<img src=\"${_blankGif}\" alt=\"\" class=\"dijitCalendarIncrementControl dijitCalendarIncrease\" waiRole=\"presentation\"/>\n\t\t\t\t<span dojoAttachPoint=\"increaseArrowNode\" class=\"dijitA11ySideArrow\">+</span>\n\t\t\t</th>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<th class=\"dijitReset dijitCalendarDayLabelTemplate\" role=\"columnheader\"><span class=\"dijitCalendarDayLabel\"></span></th>\n\t\t</tr>\n\t</thead>\n\t<tbody dojoAttachEvent=\"onclick: _onDayClick, onmouseover: _onDayMouseOver, onmouseout: _onDayMouseOut, onmousedown: _onDayMouseDown, onmouseup: _onDayMouseUp\" class=\"dijitReset dijitCalendarBodyContainer\">\n\t\t<tr class=\"dijitReset dijitCalendarWeekTemplate\" role=\"row\">\n\t\t\t<td class=\"dijitReset dijitCalendarDateTemplate\" role=\"gridcell\"><span class=\"dijitCalendarDateLabel\"></span></td>\n\t\t</tr>\n\t</tbody>\n\t<tfoot class=\"dijitReset dijitCalendarYearContainer\">\n\t\t<tr>\n\t\t\t<td class='dijitReset' valign=\"top\" colspan=\"7\">\n\t\t\t\t<h3 class=\"dijitCalendarYearLabel\">\n\t\t\t\t\t<span dojoAttachPoint=\"previousYearLabelNode\" class=\"dijitInline dijitCalendarPreviousYear\"></span>\n\t\t\t\t\t<span dojoAttachPoint=\"currentYearLabelNode\" class=\"dijitInline dijitCalendarSelectedYear\"></span>\n\t\t\t\t\t<span dojoAttachPoint=\"nextYearLabelNode\" class=\"dijitInline dijitCalendarNextYear\"></span>\n\t\t\t\t</h3>\n\t\t\t</td>\n\t\t</tr>\n\t</tfoot>\n</table>\n"),

		// value: Date
		//		The currently selected Date
		value: new Date(),

		// datePackage: String
		//		JavaScript namespace to find Calendar routines.  Uses Gregorian Calendar routines
		//		at dojo.date by default.
		datePackage: "dojo.date",

		// dayWidth: String
		//		How to represent the days of the week in the calendar header. See dojo.date.locale
		dayWidth: "narrow",

		// tabIndex: Integer
		//		Order fields are traversed when user hits the tab key
		tabIndex: "0",
		
		baseClass:"dijitCalendar",

		// Set node classes for various mouse events, see dijit._CssStateMixin for more details 
		cssStateNodes: {
			"decrementMonth": "dijitCalendarArrow",
			"incrementMonth": "dijitCalendarArrow",
			"previousYearLabelNode": "dijitCalendarPreviousYear",
			"nextYearLabelNode": "dijitCalendarNextYear"			
		},

		attributeMap: dojo.delegate(dijit._Widget.prototype.attributeMap, {
			tabIndex: "domNode"
 		}),

		setValue: function(/*Date*/ value){
			// summary:
			//      Deprecated.   Used attr('value', ...) instead.
			// tags:
			//      deprecated
			dojo.deprecated("dijit.Calendar:setValue() is deprecated.  Use set('value', ...) instead.", "", "2.0");
			this.set('value', value);
		},

		_getValueAttr: function(){
			// summary:
			//		Support getter attr('value')
			var value = new this.dateClassObj(this.value);
			value.setHours(0, 0, 0, 0); // return midnight, local time for back-compat

			// If daylight savings pushes midnight to the previous date, fix the Date
			// object to point at 1am so it will represent the correct day. See #9366
			if(value.getDate() < this.value.getDate()){
				value = this.dateFuncObj.add(value, "hour", 1);
			}
			return value;
		},

		_setValueAttr: function(/*Date*/ value){
			// summary:
			//		Support setter attr("value", ...)
			// description:
			// 		Set the current date and update the UI.  If the date is disabled, the value will
			//		not change, but the display will change to the corresponding month.
			// tags:
			//      protected
			if(!this.value || this.dateFuncObj.compare(value, this.value)){
				value = new this.dateClassObj(value);
				value.setHours(1); // to avoid issues when DST shift occurs at midnight, see #8521, #9366
				this.displayMonth = new this.dateClassObj(value);
				if(!this.isDisabledDate(value, this.lang)){
					this.value = value;
					this.onChange(this.get('value'));
				}
				dojo.attr(this.domNode, "aria-label",
					this.dateLocaleModule.format(value,
						{selector:"date", formatLength:"full"}));
				this._populateGrid();
			}
		},

		_setText: function(node, text){
			// summary:
			//		This just sets the content of node to the specified text.
			//		Can't do "node.innerHTML=text" because of an IE bug w/tables, see #3434.
			// tags:
			//      private
			while(node.firstChild){
				node.removeChild(node.firstChild);
			}
			node.appendChild(dojo.doc.createTextNode(text));
		},

		_populateGrid: function(){
			// summary:
			//      Fills in the calendar grid with each day (1-31)
			// tags:
			//      private
			var month = this.displayMonth;
			month.setDate(1);
			var firstDay = month.getDay(),
				daysInMonth = this.dateFuncObj.getDaysInMonth(month),
				daysInPreviousMonth = this.dateFuncObj.getDaysInMonth(this.dateFuncObj.add(month, "month", -1)),
				today = new this.dateClassObj(),
				dayOffset = dojo.cldr.supplemental.getFirstDayOfWeek(this.lang);
			if(dayOffset > firstDay){ dayOffset -= 7; }

			// Iterate through dates in the calendar and fill in date numbers and style info
			dojo.query(".dijitCalendarDateTemplate", this.domNode).forEach(function(template, i){
				i += dayOffset;
				var date = new this.dateClassObj(month),
					number, clazz = "dijitCalendar", adj = 0;

				if(i < firstDay){
					number = daysInPreviousMonth - firstDay + i + 1;
					adj = -1;
					clazz += "Previous";
				}else if(i >= (firstDay + daysInMonth)){
					number = i - firstDay - daysInMonth + 1;
					adj = 1;
					clazz += "Next";
				}else{
					number = i - firstDay + 1;
					clazz += "Current";
				}

				if(adj){
					date = this.dateFuncObj.add(date, "month", adj);
				}
				date.setDate(number);

				if(!this.dateFuncObj.compare(date, today, "date")){
					clazz = "dijitCalendarCurrentDate " + clazz;
				}

				if(this._isSelectedDate(date, this.lang)){
					clazz = "dijitCalendarSelectedDate " + clazz;
				}

				if(this.isDisabledDate(date, this.lang)){
					clazz = "dijitCalendarDisabledDate " + clazz;
				}

				var clazz2 = this.getClassForDate(date, this.lang);
				if(clazz2){
					clazz = clazz2 + " " + clazz;
				}

				template.className = clazz + "Month dijitCalendarDateTemplate";
				template.dijitDateValue = date.valueOf();
				var label = dojo.query(".dijitCalendarDateLabel", template)[0],
					text = date.getDateLocalized ? date.getDateLocalized(this.lang) : date.getDate();
				this._setText(label, text);
			}, this);

			// Fill in localized month name
			var monthNames = this.dateLocaleModule.getNames('months', 'wide', 'standAlone', this.lang, month);
			this._setText(this.monthLabelNode, monthNames[month.getMonth()]);
			// Repopulate month list based on current year (Hebrew calendar)
			dojo.query(".dijitCalendarMonthLabelTemplate", this.domNode).forEach(function(node, i){
				dojo.toggleClass(node, "dijitHidden", !(i in monthNames)); // hide leap months (Hebrew)
				this._setText(node, monthNames[i]);
			}, this);

			// Fill in localized prev/current/next years
			var y = month.getFullYear() - 1;
			var d = new this.dateClassObj();
			dojo.forEach(["previous", "current", "next"], function(name){
				d.setFullYear(y++);
				this._setText(this[name+"YearLabelNode"],
					this.dateLocaleModule.format(d, {selector:'year', locale:this.lang}));
			}, this);

			// Set up repeating mouse behavior
			var _this = this;
			var typematic = function(nodeProp, dateProp, adj){
//FIXME: leaks (collects) listeners if populateGrid is called multiple times.  Do this once?
				_this._connects.push(
					dijit.typematic.addMouseListener(_this[nodeProp], _this, function(count){
						if(count >= 0){ _this._adjustDisplay(dateProp, adj); }
					}, 0.8, 500)
				);
			};
			typematic("incrementMonth", "month", 1);
			typematic("decrementMonth", "month", -1);
			typematic("nextYearLabelNode", "year", 1);
			typematic("previousYearLabelNode", "year", -1);
		},

		goToToday: function(){
			// summary:
			//      Sets calendar's value to today's date
			this.set('value', new this.dateClassObj());
		},

		constructor: function(/*Object*/args){
			var dateClass = (args.datePackage && (args.datePackage != "dojo.date"))? args.datePackage + ".Date" : "Date";
			this.dateClassObj = dojo.getObject(dateClass, false);
			this.datePackage = args.datePackage || this.datePackage;
			this.dateFuncObj = dojo.getObject(this.datePackage, false);
			this.dateLocaleModule = dojo.getObject(this.datePackage + ".locale", false);
		},

		postMixInProperties: function(){
			// parser.instantiate sometimes passes in NaN for IE.  Use default value in prototype instead.
			if(isNaN(this.value)){ delete this.value; }
			this.inherited(arguments);
		},

		postCreate: function(){
			this.inherited(arguments);
			dojo.setSelectable(this.domNode, false);

			var cloneClass = dojo.hitch(this, function(clazz, n){
				var template = dojo.query(clazz, this.domNode)[0];
	 			for(var i=0; i<n; i++){
					template.parentNode.appendChild(template.cloneNode(true));
				}
			});

			// clone the day label and calendar day templates 6 times to make 7 columns
			cloneClass(".dijitCalendarDayLabelTemplate", 6);
			cloneClass(".dijitCalendarDateTemplate", 6);

			// now make 6 week rows
			cloneClass(".dijitCalendarWeekTemplate", 5);

			// insert localized day names in the header
			var dayNames = this.dateLocaleModule.getNames('days', this.dayWidth, 'standAlone', this.lang);
			var dayOffset = dojo.cldr.supplemental.getFirstDayOfWeek(this.lang);
			dojo.query(".dijitCalendarDayLabel", this.domNode).forEach(function(label, i){
				this._setText(label, dayNames[(i + dayOffset) % 7]);
			}, this);

			var dateObj = new this.dateClassObj(this.value);
			// Fill in spacer/month dropdown element with all the month names (invisible) so that the maximum width will affect layout
			var monthNames = this.dateLocaleModule.getNames('months', 'wide', 'standAlone', this.lang, dateObj);
			cloneClass(".dijitCalendarMonthLabelTemplate", monthNames.length-1);
			dojo.query(".dijitCalendarMonthLabelTemplate", this.domNode).forEach(function(node, i){
				dojo.attr(node, "month", i);
				if(i in monthNames){ this._setText(node, monthNames[i]); }
				dojo.place(node.cloneNode(true), this.monthLabelSpacer);
			}, this);

			this.value = null;
			this.set('value', dateObj);
		},

		_onMenuHover: function(e){
			dojo.stopEvent(e);
			dojo.toggleClass(e.target, "dijitMenuItemHover");
		},

		_adjustDisplay: function(/*String*/ part, /*int*/ amount){
			// summary:
			//      Moves calendar forwards or backwards by months or years
			// part:
			//      "month" or "year"
			// amount:
			//      Number of months or years
			// tags:
			//      private
			this.displayMonth = this.dateFuncObj.add(this.displayMonth, part, amount);
			this._populateGrid();
		},

		_onMonthToggle: function(/*Event*/ evt){
			// summary:
			//      Handler for when user triggers or dismisses the month list
			// tags:
			//      protected
			dojo.stopEvent(evt);

			if(evt.type == "mousedown"){
				var coords = dojo.position(this.monthLabelNode);
//				coords.y -= dojo.position(this.domNode, true).y;
				// Size the dropdown's width to match the label in the widget
				// so that they are horizontally aligned
				var dim = {
					width: coords.w + "px",
					top: -this.displayMonth.getMonth() * coords.h + "px"
				};
				if((dojo.isIE && dojo.isQuirks) || dojo.isIE < 7){
					dim.left = -coords.w/2 + "px";
				}
				dojo.style(this.monthDropDown, dim);
				this._popupHandler = this.connect(document, "onmouseup", "_onMonthToggle");
			}else{
				this.disconnect(this._popupHandler);
				delete this._popupHandler;
			}

			dojo.toggleClass(this.monthDropDown, "dijitHidden");
			dojo.toggleClass(this.monthLabelNode, "dijitVisible");
		},

		_onMonthSelect: function(/*Event*/ evt){
			// summary:
			//      Handler for when user selects a month from a list
			// tags:
			//      protected
			this._onMonthToggle(evt);
			this.displayMonth.setMonth(dojo.attr(evt.target, "month"));
			this._populateGrid();
		},

		_onDayClick: function(/*Event*/ evt){
			// summary:
			//      Handler for day clicks, selects the date if appropriate
			// tags:
			//      protected
			dojo.stopEvent(evt);
			for(var node = evt.target; node && !node.dijitDateValue; node = node.parentNode);
			if(node && !dojo.hasClass(node, "dijitCalendarDisabledDate")){
				this.set('value', node.dijitDateValue);
				this.onValueSelected(this.get('value'));
			}
		},

		_onDayMouseOver: function(/*Event*/ evt){
			// summary:
			//      Handler for mouse over events on days, sets hovered style
			// tags:
			//      protected

			// event can occur on <td> or the <span> inside the td,
			// set node to the <td>.
			var node =
				dojo.hasClass(evt.target, "dijitCalendarDateLabel") ?
				evt.target.parentNode :
				evt.target;

			if(node && (node.dijitDateValue || node == this.previousYearLabelNode || node == this.nextYearLabelNode) ){
				dojo.addClass(node, "dijitCalendarHoveredDate");
				this._currentNode = node;
			}
		},

		_onDayMouseOut: function(/*Event*/ evt){
			// summary:
			//      Handler for mouse out events on days, clears hovered style
			// tags:
			//      protected
	
			if(!this._currentNode){ return; }
			
			// if mouse out occurs moving from <td> to <span> inside <td>, ignore it
			if(evt.relatedTarget && evt.relatedTarget.parentNode == this._currentNode){ return; }

			dojo.removeClass(this._currentNode, "dijitCalendarHoveredDate");
			if(dojo.hasClass(this._currentNode, "dijitCalendarActiveDate")) {
				dojo.removeClass(this._currentNode, "dijitCalendarActiveDate");
			}
			this._currentNode = null;
		},
		
		_onDayMouseDown: function(/*Event*/ evt){
			var node = evt.target.parentNode;
			if(node && node.dijitDateValue){
				dojo.addClass(node, "dijitCalendarActiveDate");
				this._currentNode = node;
			}
		},
		
		_onDayMouseUp: function(/*Event*/ evt){
			var node = evt.target.parentNode;
			if(node && node.dijitDateValue){
				dojo.removeClass(node, "dijitCalendarActiveDate");
			}
		},

//TODO: use typematic
//TODO: skip disabled dates without ending up in a loop
//TODO: could optimize by avoiding populate grid when month does not change
		_onKeyPress: function(/*Event*/evt){
			// summary:
			//		Provides keyboard navigation of calendar
			// tags:
			//		protected
			var dk = dojo.keys,
				increment = -1,
				interval,
				newValue = this.value;
			switch(evt.keyCode){
				case dk.RIGHT_ARROW:
					increment = 1;
					//fallthrough...
				case dk.LEFT_ARROW:
					interval = "day";
					if(!this.isLeftToRight()){ increment *= -1; }
					break;
				case dk.DOWN_ARROW:
					increment = 1;
					//fallthrough...
				case dk.UP_ARROW:
					interval = "week";
					break;
				case dk.PAGE_DOWN:
					increment = 1;
					//fallthrough...
				case dk.PAGE_UP:
					interval = evt.ctrlKey ? "year" : "month";
					break;
				case dk.END:
					// go to the next month
					newValue = this.dateFuncObj.add(newValue, "month", 1);
					// subtract a day from the result when we're done
					interval = "day";
					//fallthrough...
				case dk.HOME:
					newValue = new Date(newValue).setDate(1);
					break;
				case dk.ENTER:
					this.onValueSelected(this.get('value'));
					break;
				case dk.ESCAPE:
					//TODO
				default:
					return;
			}
			dojo.stopEvent(evt);

			if(interval){
				newValue = this.dateFuncObj.add(newValue, interval, increment);
			}

			this.set("value", newValue);
		},

		onValueSelected: function(/*Date*/ date){
			// summary:
			//		Notification that a date cell was selected.  It may be the same as the previous value.
			// description:
			//      Used by `dijit.form._DateTimeTextBox` (and thus `dijit.form.DateTextBox`)
			//      to get notification when the user has clicked a date.
			// tags:
			//      protected
		},

		onChange: function(/*Date*/ date){
			// summary:
			//		Called only when the selected date has changed
		},

		_isSelectedDate: function(/*Date*/ dateObject, /*String?*/ locale){
			// summary:
			//		Extension point so developers can subclass Calendar to
			//		support multiple (concurrently) selected dates
			// tags:
			//		protected extension
			return !this.dateFuncObj.compare(dateObject, this.value, "date")
		},

		isDisabledDate: function(/*Date*/ dateObject, /*String?*/ locale){
			// summary:
			//		May be overridden to disable certain dates in the calendar e.g. `isDisabledDate=dojo.date.locale.isWeekend`
			// tags:
			//      extension
/*=====
			return false; // Boolean
=====*/
		},

		getClassForDate: function(/*Date*/ dateObject, /*String?*/ locale){
			// summary:
			//		May be overridden to return CSS classes to associate with the date entry for the given dateObject,
			//		for example to indicate a holiday in specified locale.
			// tags:
			//      extension

/*=====
			return ""; // String
=====*/
		}
	}
);

}

if(!dojo._hasResource["dijit._Calendar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Calendar"] = true;
dojo.provide("dijit._Calendar");


dojo.deprecated("dijit._Calendar is deprecated", "dijit._Calendar moved to dijit.Calendar", 1.5);

// dijit._Calendar had an underscore all this time merely because it did
// not satisfy dijit's a11y policy.
dijit._Calendar = dijit.Calendar;

}

if(!dojo._hasResource["dijit.form.Form"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.Form"] = true;
dojo.provide("dijit.form.Form");





dojo.declare(
	"dijit.form.Form",
	[dijit._Widget, dijit._Templated, dijit.form._FormMixin],
	{
		// summary:
		//		Widget corresponding to HTML form tag, for validation and serialization
		//
		// example:
		//	|	<form dojoType="dijit.form.Form" id="myForm">
		//	|		Name: <input type="text" name="name" />
		//	|	</form>
		//	|	myObj = {name: "John Doe"};
		//	|	dijit.byId('myForm').set('value', myObj);
		//	|
		//	|	myObj=dijit.byId('myForm').get('value');

		// HTML <FORM> attributes

		// name: String?
		//		Name of form for scripting.
		name: "",

		// action: String?
		//		Server-side form handler.
		action: "",

		// method: String?
		//		HTTP method used to submit the form, either "GET" or "POST".
		method: "",

		// encType: String?
		//		Encoding type for the form, ex: application/x-www-form-urlencoded.
		encType: "",

		// accept-charset: String?
		//		List of supported charsets.
		"accept-charset": "",

		// accept: String?
		//		List of MIME types for file upload.
		accept: "",

		// target: String?
		//		Target frame for the document to be opened in.
		target: "",

		templateString: "<form dojoAttachPoint='containerNode' dojoAttachEvent='onreset:_onReset,onsubmit:_onSubmit' ${!nameAttrSetting}></form>",

		attributeMap: dojo.delegate(dijit._Widget.prototype.attributeMap, {
			action: "",
			method: "",
			encType: "",
			"accept-charset": "",
			accept: "",
			target: ""
		}),

		postMixInProperties: function(){
			// Setup name=foo string to be referenced from the template (but only if a name has been specified)
			// Unfortunately we can't use attributeMap to set the name due to IE limitations, see #8660
			this.nameAttrSetting = this.name ? ("name='" + this.name + "'") : "";
			this.inherited(arguments);
		},

		execute: function(/*Object*/ formContents){
			// summary:
			//		Deprecated: use submit()
			// tags:
			//		deprecated
		},

		onExecute: function(){
			// summary:
			//		Deprecated: use onSubmit()
			// tags:
			//		deprecated
		},

		_setEncTypeAttr: function(/*String*/ value){
			this.encType = value;
			dojo.attr(this.domNode, "encType", value);
			if(dojo.isIE){ this.domNode.encoding = value; }
		},

		postCreate: function(){
			// IE tries to hide encType
			// TODO: this code should be in parser, not here.
			if(dojo.isIE && this.srcNodeRef && this.srcNodeRef.attributes){
				var item = this.srcNodeRef.attributes.getNamedItem('encType');
				if(item && !item.specified && (typeof item.value == "string")){
					this.set('encType', item.value);
				}
			}
			this.inherited(arguments);
		},

		reset: function(/*Event?*/ e){
			// summary:
			//		restores all widget values back to their init values,
			//		calls onReset() which can cancel the reset by returning false

			// create fake event so we can know if preventDefault() is called
			var faux = {
				returnValue: true, // the IE way
				preventDefault: function(){ // not IE
							this.returnValue = false;
						},
				stopPropagation: function(){}, 
				currentTarget: e ? e.target : this.domNode, 
				target: e ? e.target : this.domNode
			};
			// if return value is not exactly false, and haven't called preventDefault(), then reset
			if(!(this.onReset(faux) === false) && faux.returnValue){
				this.inherited(arguments, []);
			}
		},

		onReset: function(/*Event?*/ e){
			// summary:
			//		Callback when user resets the form. This method is intended
			//		to be over-ridden. When the `reset` method is called
			//		programmatically, the return value from `onReset` is used
			//		to compute whether or not resetting should proceed
			// tags:
			//		callback
			return true; // Boolean
		},

		_onReset: function(e){
			this.reset(e);
			dojo.stopEvent(e);
			return false;
		},

		_onSubmit: function(e){
			var fp = dijit.form.Form.prototype;
			// TODO: remove this if statement beginning with 2.0
			if(this.execute != fp.execute || this.onExecute != fp.onExecute){
				dojo.deprecated("dijit.form.Form:execute()/onExecute() are deprecated. Use onSubmit() instead.", "", "2.0");
				this.onExecute();
				this.execute(this.getValues());
			}
			if(this.onSubmit(e) === false){ // only exactly false stops submit
				dojo.stopEvent(e);
			}
		},

		onSubmit: function(/*Event?*/e){
			// summary:
			//		Callback when user submits the form.
			// description:
			//		This method is intended to be over-ridden, but by default it checks and
			//		returns the validity of form elements. When the `submit`
			//		method is called programmatically, the return value from
			//		`onSubmit` is used to compute whether or not submission
			//		should proceed
			// tags:
			//		extension

			return this.isValid(); // Boolean
		},

		submit: function(){
			// summary:
			//		programmatically submit form if and only if the `onSubmit` returns true
			if(!(this.onSubmit() === false)){
				this.containerNode.submit();
			}
		}
	}
);

}

if(!dojo._hasResource["dijit.form._DateTimeTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._DateTimeTextBox"] = true;
dojo.provide("dijit.form._DateTimeTextBox");






new Date("X"); // workaround for #11279, new Date("") == NaN

/*=====
dojo.declare(
	"dijit.form._DateTimeTextBox.__Constraints",
	[dijit.form.RangeBoundTextBox.__Constraints, dojo.date.locale.__FormatOptions], {
	// summary:
	//		Specifies both the rules on valid/invalid values (first/last date/time allowed),
	//		and also formatting options for how the date/time is displayed.
	// example:
	//		To restrict to dates within 2004, displayed in a long format like "December 25, 2005":
	//	|		{min:'2004-01-01',max:'2004-12-31', formatLength:'long'}
});
=====*/

dojo.declare(
	"dijit.form._DateTimeTextBox",
	dijit.form.RangeBoundTextBox,
	{
		// summary:
		//		Base class for validating, serializable, range-bound date or time text box.

		// constraints: dijit.form._DateTimeTextBox.__Constraints
		//		Despite the name, this parameter specifies both constraints on the input
		//		(including starting/ending dates/times allowed) as well as
		//		formatting options like whether the date is displayed in long (ex: December 25, 2005)
		//		or short (ex: 12/25/2005) format.   See `dijit.form._DateTimeTextBox.__Constraints` for details.
		/*=====
		constraints: {},
		======*/

		// Override ValidationTextBox.regExpGen().... we use a reg-ex generating function rather
		// than a straight regexp to deal with locale  (plus formatting options too?)
		regExpGen: dojo.date.locale.regexp,

		// datePackage: String
		//	JavaScript namespace to find calendar routines.  Uses Gregorian calendar routines
		//	at dojo.date, by default.
		datePackage: "dojo.date",

		// Override _FormWidget.compare() to work for dates/times
		compare: dojo.date.compare,

		format: function(/*Date*/ value, /*dojo.date.locale.__FormatOptions*/ constraints){
			// summary:
			//		Formats the value as a Date, according to specified locale (second argument)
			// tags:
			//		protected
			if(!value){ return ''; }
			return this.dateLocaleModule.format(value, constraints);
		},

		parse: function(/*String*/ value, /*dojo.date.locale.__FormatOptions*/ constraints){
			// summary:
			//		Parses as string as a Date, according to constraints
			// tags:
			//		protected

			return this.dateLocaleModule.parse(value, constraints) || (this._isEmpty(value) ? null : undefined);	 // Date
		},

		// Overrides ValidationTextBox.serialize() to serialize a date in canonical ISO format.
		serialize: function(/*anything*/val, /*Object?*/options){
			if(val.toGregorian){
				val = val.toGregorian();
			}
			return dojo.date.stamp.toISOString(val, options);
		},

		// value: Date
		//		The value of this widget as a JavaScript Date object.  Use get("value") / set("value", val) to manipulate.
		//		When passed to the parser in markup, must be specified according to `dojo.date.stamp.fromISOString`
		value: new Date(""),	// value.toString()="NaN"
		_blankValue: null,	// used by filter() when the textbox is blank

		//	popupClass: [protected extension] String
		//		Name of the popup widget class used to select a date/time.
		//		Subclasses should specify this.
		popupClass: "", // default is no popup = text only


		// _selector: [protected extension] String
		//		Specifies constraints.selector passed to dojo.date functions, should be either
		//		"date" or "time".
		//		Subclass must specify this.
		_selector: "",

		constructor: function(/*Object*/args){
			var dateClass = args.datePackage ? args.datePackage + ".Date" : "Date";
			this.dateClassObj = dojo.getObject(dateClass, false);
			this.value = new this.dateClassObj("");

			this.datePackage = args.datePackage || this.datePackage;
			this.dateLocaleModule = dojo.getObject(this.datePackage + ".locale", false);
			this.regExpGen = this.dateLocaleModule.regexp;
		},

		_setConstraintsAttr: function(/* Object */ constraints){
			constraints.selector = this._selector;
			constraints.fullYear = true; // see #5465 - always format with 4-digit years
			var fromISO = dojo.date.stamp.fromISOString;
			if(typeof constraints.min == "string"){ constraints.min = fromISO(constraints.min); }
 			if(typeof constraints.max == "string"){ constraints.max = fromISO(constraints.max); }
			this.inherited(arguments, [constraints]);
		},

		_onFocus: function(/*Event*/ evt){
			// summary:
			//		open the popup
			this._open();
			this.inherited(arguments);
		},

		_setValueAttr: function(/*Date*/ value, /*Boolean?*/ priorityChange, /*String?*/ formattedValue){
			// summary:
			//		Sets the date on this textbox.  Note that `value` must be like a Javascript Date object.
			if(value !== undefined){
				if(!value || value.toString() == dijit.form._DateTimeTextBox.prototype.value.toString()){
					value = null;
				}
				if(value instanceof Date && !(this.dateClassObj instanceof Date)){
					value = new this.dateClassObj(value);
				}
			}
			this.inherited(arguments, [value, priorityChange, formattedValue]);
			if(this._picker){
				// #3948: fix blank date on popup only
				if(!value){value = new this.dateClassObj();}
				this._picker.set('value', value);
			}
		},

		_open: function(){
			// summary:
			//		opens the TimePicker, and sets the onValueSelected value

			if(this.disabled || this.readOnly || !this.popupClass){return;}

			var textBox = this;

			if(!this._picker){
				var PopupProto = dojo.getObject(this.popupClass, false);
				this._picker = new PopupProto({
					onValueSelected: function(value){
						if(textBox._tabbingAway){
							delete textBox._tabbingAway;
						}else{
							textBox.focus(); // focus the textbox before the popup closes to avoid reopening the popup
						}
						setTimeout(dojo.hitch(textBox, "_close"), 1); // allow focus time to take

						// this will cause InlineEditBox and other handlers to do stuff so make sure it's last
						dijit.form._DateTimeTextBox.superclass._setValueAttr.call(textBox, value, true);
					},
					id: this.id + "_popup",
					dir: textBox.dir,
					lang: textBox.lang,
					value: this.get('value') || new this.dateClassObj(),
					constraints: textBox.constraints,

					datePackage: textBox.datePackage,

					isDisabledDate: function(/*Date*/ date){
						// summary:
						// 	disables dates outside of the min/max of the _DateTimeTextBox
						var compare = dojo.date.compare;
						var constraints = textBox.constraints;
						return constraints && (
							(constraints.min && compare(constraints.min, date, textBox._selector) > 0) ||
							(constraints.max && compare(constraints.max, date, textBox._selector) < 0)
						);
					}
				});
			}
			if(!this._opened){
				// Open drop down.  Align left sides of input box and drop down, even in RTL mode,
				// otherwise positioning thrown off when the drop down width is changed in marginBox call below (#10676)
				dijit.popup.open({
					parent: this,
					popup: this._picker,
					orient: {'BL':'TL', 'TL':'BL'},
					around: this.domNode,
					onCancel: dojo.hitch(this, this._close),
					onClose: function(){ textBox._opened=false; }
				});
				this._opened=true;
			}

			dojo.marginBox(this._picker.domNode,{ w:this.domNode.offsetWidth });
		},

		_close: function(){
			if(this._opened){
				dijit.popup.close(this._picker);
				this._opened=false;
			}
		},

		_onBlur: function(){
			// summary:
			//		Called magically when focus has shifted away from this widget and it's dropdown
			this._close();
			if(this._picker){
				// teardown so that constraints will be rebuilt next time (redundant reference: #6002)
				this._picker.destroy();
				delete this._picker;
			}
			this.inherited(arguments);
			// don't focus on <input>.  the user has explicitly focused on something else.
		},

		_getDisplayedValueAttr: function(){
			return this.textbox.value;
		},

		_setDisplayedValueAttr: function(/*String*/ value, /*Boolean?*/ priorityChange){
			this._setValueAttr(this.parse(value, this.constraints), priorityChange, value);
		},

		destroy: function(){
			if(this._picker){
				this._picker.destroy();
				delete this._picker;
			}
			this.inherited(arguments);
		},

		postCreate: function(){
			this.inherited(arguments);
			this.connect(this.focusNode, 'onkeypress', this._onKeyPress);
			this.connect(this.focusNode, 'onclick', this._open);
		},

		_onKeyPress: function(/*Event*/ e){
			// summary:
			//		Handler for keypress events

			var p = this._picker, dk = dojo.keys;
			// Handle the key in the picker, if it has a handler.  If the handler
			// returns false, then don't handle any other keys.
			if(p && this._opened && p.handleKey){
				if(p.handleKey(e) === false){ return; }
			}
			if(this._opened && e.charOrCode == dk.ESCAPE && !(e.shiftKey || e.ctrlKey || e.altKey || e.metaKey)){
				this._close();
				dojo.stopEvent(e);
			}else if(!this._opened && e.charOrCode == dk.DOWN_ARROW){
				this._open();
				dojo.stopEvent(e);
			}else if(e.charOrCode === dk.TAB){
				this._tabbingAway = true;
			}else if(this._opened && (e.keyChar || e.charOrCode === dk.BACKSPACE || e.charOrCode == dk.DELETE)){
				// Replace the element - but do it after a delay to allow for
				// filtering to occur
				setTimeout(dojo.hitch(this, function(){
					if(this._picker && this._opened){
						dijit.placeOnScreenAroundElement(p.domNode.parentNode, this.domNode, {'BL':'TL', 'TL':'BL'}, p.orient ? dojo.hitch(p, "orient") : null);
					}
				}), 1);
			}
		}
	}
);

}

if(!dojo._hasResource["dijit.form.DateTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.DateTextBox"] = true;
dojo.provide("dijit.form.DateTextBox");




dojo.declare(
	"dijit.form.DateTextBox",
	dijit.form._DateTimeTextBox,
	{
		// summary:
		//		A validating, serializable, range-bound date text box with a drop down calendar
		//
		//		Example:
		// |	new dijit.form.DateTextBox({value: new Date(2009, 0, 20)})
		//
		//		Example:
		// |	<input dojotype='dijit.form.DateTextBox' value='2009-01-20'>

		baseClass: "dijitTextBox dijitDateTextBox",
		popupClass: "dijit.Calendar",
		_selector: "date",

		// value: Date
		//		The value of this widget as a JavaScript Date object, with only year/month/day specified.
		//		If specified in markup, use the format specified in `dojo.date.stamp.fromISOString`
		value: new Date("")	// value.toString()="NaN"
	}
);

}

if(!dojo._hasResource["common.layer"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["common.layer"] = true;
dojo.provide("common.layer");
























}


dojo.i18n._preloadLocalizations("common.nls.layer", ["ROOT","ar","ca","cs","da","de","de-de","el","en","en-gb","en-us","es","es-es","fi","fi-fi","fr","fr-fr","he","he-il","hu","it","it-it","ja","ja-jp","ko","ko-kr","nb","nl","nl-nl","pl","pt","pt-br","pt-pt","ru","sk","sl","sv","th","tr","xx","zh","zh-cn","zh-tw"]);
