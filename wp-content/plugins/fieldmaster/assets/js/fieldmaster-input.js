( function( window, undefined ) {
	"use strict";

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction,
			storage : getStorage
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};
		
		function getStorage() {
			
			return STORAGE;
			
		};
		
		/**
		 * Adds an action to the event manager.
		 *
		 * @param action Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addAction( action, callback, priority, context ) {
			if( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		function doAction( /* action, arg1, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var action = args.shift();

			if( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified action if it contains a namespace.identifier & exists.
		 *
		 * @param action The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeAction( action, callback ) {
			if( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Adds a filter to the event manager.
		 *
		 * @param filter Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addFilter( filter, callback, priority, context ) {
			if( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var filter = args.shift();

			if( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified filter if it contains a namespace.identifier & exists.
		 *
		 * @param filter The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeFilter( filter, callback ) {
			if( typeof filter === 'string') {
				_removeHook( 'filters', filter, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified hook by resetting the value of it.
		 *
		 * @param type Type of hook, either 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to remove
		 * @private
		 */
		function _removeHook( type, hook, callback, context ) {
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				var handlers = STORAGE[ type ][ hook ];
				var i;
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						var handler = handlers[i];
						if ( handler.callback === callback && handler.context === context) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		/**
		 * Adds the hook to the appropriate storage container
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to add to our event manager
		 * @param callback The function that will be called when the hook is executed.
		 * @param priority The priority of this hook. Must be an integer.
		 * @param [context] A value to be used for this
		 * @private
		 */
		function _addHook( type, hook, callback, priority, context ) {
			var hookObject = {
				callback : callback,
				priority : priority,
				context : context
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		}

		/**
		 * Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		 * than bubble sort, etc: http://jsperf.com/javascript-sort
		 *
		 * @param hooks The custom array containing all of the appropriate hooks to perform an insert sort on.
		 * @private
		 */
		function _hookInsertSort( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		}

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook ( namespace.identifier ) to be ran.
		 * @param args Arguments to pass to the action/filter. If it's a filter, args is actually a single parameter.
		 * @private
		 */
		function _runHook( type, hook, args ) {
			var handlers = STORAGE[ type ][ hook ];
			
			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			var i = 0, len = handlers.length;
			if ( type === 'filters' ) {
				for ( ; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( ; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;

	};
	
	window.wp = window.wp || {};
	window.wp.hooks = new EventManager();

} )( window );


var fieldmaster;

(function($){
	
	
	/*
	*  exists
	*
	*  This function will return true if a jQuery selection exists
	*
	*  @type	function
	*  @date	8/09/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(boolean)
	*/
	
	$.fn.exists = function() {
	
		return $(this).length>0;
		
	};
	
	
	/*
	*  outerHTML
	*
	*  This function will return a string containing the HTML of the selected element
	*
	*  @type	function
	*  @date	19/11/2013
	*  @since	5.0.0
	*
	*  @param	$.fn
	*  @return	(string)
	*/
	
	$.fn.outerHTML = function() {
	    
	    return $(this).get(0).outerHTML;
	    
	};
	
	
	fieldmaster = {
		
		// vars
		l10n:	{},
		o:		{},
		
		
		/*
		*  update
		*
		*  This function will update a value found in fieldmaster.o
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	k (string) the key
		*  @param	v (mixed) the value
		*  @return	n/a
		*/
		
		update: function( k, v ){
				
			this.o[ k ] = v;
			
		},
		
		
		/*
		*  get
		*
		*  This function will return a value found in fieldmaster.o
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	k (string) the key
		*  @return	v (mixed) the value
		*/
		
		get: function( k ){
			
			if( typeof this.o[ k ] !== 'undefined' ) {
				
				return this.o[ k ];
				
			}
			
			return null;
			
		},
		
		
		/*
		*  _e
		*
		*  This functiln will return a string found in fieldmaster.l10n
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	k1 (string) the first key to look for
		*  @param	k2 (string) the second key to look for
		*  @return	string (string)
		*/
		
		_e: function( k1, k2 ){
			
			// defaults
			k2 = k2 || false;
			
			
			// get context
			var string = this.l10n[ k1 ] || '';
			
			
			// get string
			if( k2 ) {
			
				string = string[ k2 ] || '';
				
			}
			
			
			// return
			return string;
			
		},
		
		
		/*
		*  add_action
		*
		*  This function uses wp.hooks to mimics WP add_action
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		add_action: function() {
			
			// vars
			var a = arguments[0].split(' '),
				l = a.length;
			
			
			// loop
			for( var i = 0; i < l; i++) {
				
/*
				// allow for special actions
				if( a[i].indexOf('initialize') !== -1 ) {
					
					a.push( a[i].replace('initialize', 'ready') );
					a.push( a[i].replace('initialize', 'append') );
					l = a.length;
					
					continue;
				}
*/
				
				
				// prefix action
				arguments[0] = 'fieldmaster/' + a[i];
			
			
				// add
				wp.hooks.addAction.apply(this, arguments);
					
			}
			
			
			// return
			return this;
			
		},
		
		
		/*
		*  remove_action
		*
		*  This function uses wp.hooks to mimics WP remove_action
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		remove_action: function() {
			
			// prefix action
			arguments[0] = 'fieldmaster/' + arguments[0];
			
			wp.hooks.removeAction.apply(this, arguments);
			
			return this;
			
		},
		
		
		/*
		*  do_action
		*
		*  This function uses wp.hooks to mimics WP do_action
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		do_action: function() { //console.log('fieldmaster.do_action(%o)', arguments);
			
			// prefix action
			arguments[0] = 'fieldmaster/' + arguments[0];
			
			wp.hooks.doAction.apply(this, arguments);
			
			return this;
			
		},
		
		
		/*
		*  add_filter
		*
		*  This function uses wp.hooks to mimics WP add_filter
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		add_filter: function() {
			
			// prefix action
			arguments[0] = 'fieldmaster/' + arguments[0];
			
			wp.hooks.addFilter.apply(this, arguments);
			
			return this;
			
		},
		
		
		/*
		*  remove_filter
		*
		*  This function uses wp.hooks to mimics WP remove_filter
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		remove_filter: function() {
			
			// prefix action
			arguments[0] = 'fieldmaster/' + arguments[0];
			
			wp.hooks.removeFilter.apply(this, arguments);
			
			return this;
			
		},
		
		
		/*
		*  apply_filters
		*
		*  This function uses wp.hooks to mimics WP apply_filters
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	
		*  @return
		*/
		
		apply_filters: function() { //console.log('fieldmaster.apply_filters(%o)', arguments);
			
			// prefix action
			arguments[0] = 'fieldmaster/' + arguments[0];
			
			return wp.hooks.applyFilters.apply(this, arguments);
			
		},
		
		
		/*
		*  get_selector
		*
		*  This function will return a valid selector for finding a field object
		*
		*  @type	function
		*  @date	15/01/2015
		*  @since	5.1.5
		*
		*  @param	s (string)
		*  @return	(string)
		*/
		
		get_selector: function( s ) {
			
			// defaults
			s = s || '';
			
			
			// vars
			var selector = '.fm-field';
			
			
			// compatibility with object
			if( $.isPlainObject(s) ) {
				
				if( $.isEmptyObject(s) ) {
				
					s = '';
					
				} else {
					
					for( k in s ) { s = s[k]; break; }
					
				}
				
			}


			// search
			if( s ) {
				
				// append
				selector += '-' + s;
				
				
				// replace underscores (split/join replaces all and is faster than regex!)
				selector = selector.split('_').join('-');
				
				
				// remove potential double up
				selector = selector.split('field-field-').join('field-');
			
			}
			
			
			// return
			return selector;
			
		},
		
		
		/*
		*  get_fields
		*
		*  This function will return a jQuery selection of fields
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	args (object)
		*  @param	$el (jQuery) element to look within
		*  @param	all (boolean) return all fields or allow filtering (for repeater)
		*  @return	$fields (jQuery)
		*/
		
		get_fields: function( s, $el, all ){
			
			// debug
			//console.log( 'fieldmaster.get_fields(%o, %o, %o)', args, $el, all );
			//console.time("fieldmaster.get_fields");
			
			
			// defaults
			s = s || '';
			$el = $el || false;
			all = all || false;
			
			
			// vars
			var selector = this.get_selector(s);
			
			
			// get child fields
			var $fields = $( selector, $el );
			
			
			// append context to fields if also matches selector.
			// * Required for field group 'change_filed_type' append $tr to work
			if( $el !== false ) {
				
				$el.each(function(){
					
					if( $(this).is(selector) ) {
					
						$fields = $fields.add( $(this) );
						
					}
					
				});
				
			}
			
			
			// filter out fields
			if( !all ) {
				
				// remove clone fields
				$fields = $fields.not('.fieldmaster-clone .fm-field');
				
				
				// filter
				$fields = fieldmaster.apply_filters('get_fields', $fields);
								
			}
			
			
			//console.log('get_fields(%o, %o, %o) %o', s, $el, all, $fields);
			//console.log('fieldmaster.get_fields(%o):', this.get_selector(s) );
			//console.timeEnd("fieldmaster.get_fields");
			
			
			// return
			return $fields;
							
		},
		
		
		/*
		*  get_field
		*
		*  This function will return a jQuery selection based on a field key
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	field_key (string)
		*  @param	$el (jQuery) element to look within
		*  @return	$field (jQuery)
		*/
		
		get_field: function( s, $el ){
			
			// defaults
			s = s || '';
			$el = $el || false;
			
			
			// get fields
			var $fields = this.get_fields(s, $el, true);
			
			
			// check if exists
			if( $fields.exists() ) {
			
				return $fields.first();
				
			}
			
			
			// return
			return false;
			
		},
		
		
		/*
		*  get_closest_field
		*
		*  This function will return the closest parent field
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery) element to start from
		*  @param	args (object)
		*  @return	$field (jQuery)
		*/
		
		get_closest_field : function( $el, s ){
			
			// defaults
			s = s || '';
			
			
			// return
			return $el.closest( this.get_selector(s) );
			
		},
		
		
		/*
		*  get_field_wrap
		*
		*  This function will return the closest parent field
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery) element to start from
		*  @return	$field (jQuery)
		*/
		
		get_field_wrap: function( $el ){
			
			return $el.closest( this.get_selector() );
			
		},
		
		
		/*
		*  get_field_key
		*
		*  This function will return the field's key
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$field (jQuery)
		*  @return	(string)
		*/
		
		get_field_key: function( $field ){
		
			return $field.data('key');
			
		},
		
		
		/*
		*  get_field_type
		*
		*  This function will return the field's type
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$field (jQuery)
		*  @return	(string)
		*/
		
		get_field_type: function( $field ){
		
			return $field.data('type');
			
		},
		
		
		/*
		*  get_data
		*
		*  This function will return attribute data for a given elemnt
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery)
		*  @param	name (mixed)
		*  @return	(mixed)
		*/
		
		get_data: function( $el, name ){
			
			//console.log('get_data(%o, %o)', name, $el);
			
			
			// get all datas
			if( typeof name === 'undefined' ) {
				
				return $el.data();
				
			}
			
			
			// return
			return $el.data(name);
							
		},
		
		
		/*
		*  get_uniqid
		*
		*  This function will return a unique string ID
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	prefix (string)
		*  @param	more_entropy (boolean)
		*  @return	(string)
		*/
		
		get_uniqid : function( prefix, more_entropy ){
		
			// + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// + revised by: Kankrelune (http://www.webfaktory.info/)
			// % note 1: Uses an internal counter (in php_js global) to avoid collision
			// * example 1: uniqid();
			// * returns 1: 'a30285b160c14'
			// * example 2: uniqid('foo');
			// * returns 2: 'fooa30285b1cd361'
			// * example 3: uniqid('bar', true);
			// * returns 3: 'bara20285b23dfd1.31879087'
			if (typeof prefix === 'undefined') {
				prefix = "";
			}
			
			var retId;
			var formatSeed = function (seed, reqWidth) {
				seed = parseInt(seed, 10).toString(16); // to hex str
				if (reqWidth < seed.length) { // so long we split
					return seed.slice(seed.length - reqWidth);
				}
				if (reqWidth > seed.length) { // so short we pad
					return Array(1 + (reqWidth - seed.length)).join('0') + seed;
				}
				return seed;
			};
			
			// BEGIN REDUNDANT
			if (!this.php_js) {
				this.php_js = {};
			}
			// END REDUNDANT
			if (!this.php_js.uniqidSeed) { // init seed with big random int
				this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
			}
			this.php_js.uniqidSeed++;
			
			retId = prefix; // start with prefix, add current milliseconds hex string
			retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
			retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
			if (more_entropy) {
				// for more entropy we add a float lower to 10
				retId += (Math.random() * 10).toFixed(8).toString();
			}
			
			return retId;
			
		},
		
		
		/*
		*  serialize_form
		*
		*  This function will create an object of data containing all form inputs within an element
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery selection)
		*  @return	$post_id (int)
		*/
		
		serialize_form: function( $el, prefix ){
			
			// defaults
			prefix = prefix || '';
			
			
			// vars
			var data = {};
			var names = {};
			var values = $el.find('select, textarea, input').serializeArray();
			
			
			// populate data
			$.each( values, function( i, pair ) {
				
				// vars
				var name = pair.name;
				var value = pair.value;
				
				
				// prefix
				if( prefix ) {
					
					// bail early if does not contain
					if( name.indexOf(prefix) !== 0 ) return;
					
					
					// remove prefix
					name = name.substr(prefix.length);
					
					
					// name must not start as array piece
					if( name.slice(0, 1) == '[' ) {
						
						name = name.replace('[', '');
						name = name.replace(']', '');
						
					}
					
				}
				
				
				// initiate name
				if( name.slice(-2) === '[]' ) {
					
					// remove []
					name = name.slice(0, -2);
					
					
					// initiate counter
					if( typeof names[ name ] === 'undefined'){
						
						names[ name ] = -1;
						
					}
					
					
					// increase counter
					names[ name ]++;
					
					
					// add key
					name += '[' + names[ name ] +']';
				}
				
				
				// append to data
				data[ name ] = value;
				
			});
			
			
			// return
			return data;
			
		},
		
		serialize: function( $el, prefix ){
			
			return this.serialize_form.apply( this, arguments );
			
		},
		
		
		/*
		*  disable
		*
		*  This function will disable an input
		*
		*  @type	function
		*  @date	22/09/2016
		*  @since	5.4.0
		*
		*  @param	$el (jQuery)
		*  @param	context (string)
		*  @return	n/a
		*/
		
		disable: function( $input, context ){
			
			// defaults
			context = context || '';
			
			
			// bail early if is .fieldmaster-disabled
			if( $input.hasClass('fieldmaster-disabled') ) return false;
			
			
			// always disable input
			$input.prop('disabled', true);
			
			
			// context
			if( context ) {
				
				// vars
				var disabled = $input.data('fieldmaster_disabled') || [],
					i = disabled.indexOf(context);
					
				
				// append context if not found
				if( i < 0 ) {
					
					// append
					disabled.push( context );
					
					
					// update
					$input.data('fieldmaster_disabled', disabled);
					
				}
			}
			
			
			// return
			return true;
			
		}, 
		
		
		/*
		*  enable
		*
		*  This function will enable an input
		*
		*  @type	function
		*  @date	22/09/2016
		*  @since	5.4.0
		*
		*  @param	$el (jQuery)
		*  @param	context (string)
		*  @return	n/a
		*/
		
		enable: function( $input, context ){
			
			// defaults
			context = context || '';
			
			
			// bail early if is .fieldmaster-disabled
			if( $input.hasClass('fieldmaster-disabled') ) return false;
			
			
			// vars
			var disabled = $input.data('fieldmaster_disabled') || [];
				
				
			// context
			if( context ) {
				
				// vars
				var i = disabled.indexOf(context);
				
				
				// remove context if found
				if( i > -1 ) {
					
					// delete
					disabled.splice(i, 1);
					
					
					// update
					$input.data('fieldmaster_disabled', disabled);
					
				}
			}
			
			
			// bail early if other disabled exist
			if( disabled.length ) return false;
			
			
			// enable input
			$input.prop('disabled', false);
			
			
			// return
			return true;
			
		},
		
		
		/*
		*  disable_el
		*
		*  This function will disable all inputs within an element
		*
		*  @type	function
		*  @date	22/09/2016
		*  @since	5.4.0
		*
		*  @param	$el (jQuery)
		*  @param	context (string)
		*  @return	na
		*/
		
		disable_el: function( $el, context ) {
			
			// defaults
			context = context || '';
			
			
			// loop
			$el.find('select, textarea, input').each(function(){
				
				fieldmaster.disable( $(this), context );
				
			});
			
		},
		
		disable_form: function( $el, context ) {
			
			this.disable_el.apply( this, arguments );
			
		},
		
		
		/*
		*  enable_el
		*
		*  This function will enable all inputs within an element
		*
		*  @type	function
		*  @date	22/09/2016
		*  @since	5.4.0
		*
		*  @param	$el (jQuery)
		*  @param	context (string)
		*  @return	na
		*/
		
		enable_el: function( $el, context ) {
			
			// defaults
			context = context || '';
			
			
			// loop
			$el.find('select, textarea, input').each(function(){
				
				fieldmaster.enable( $(this), context );
				
			});
			
		},
		
		enable_form: function( $el, context ) {
			
			this.enable_el.apply( this, arguments );
			
		},
		
		
		/*
		*  remove_tr
		*
		*  This function will remove a tr element with animation
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$tr (jQuery selection)
		*  @param	callback (function) runs on complete
		*  @return	n/a
		*/
		
		remove_tr : function( $tr, callback ){
			
			// vars
			var height = $tr.height(),
				children = $tr.children().length;
			
			
			// add class
			$tr.addClass('fieldmaster-remove-element');
			
			
			// after animation
			setTimeout(function(){
				
				// remove class
				$tr.removeClass('fieldmaster-remove-element');
				
				
				// vars
				$tr.html('<td style="padding:0; height:' + height + 'px" colspan="' + children + '"></td>');
				
				
				$tr.children('td').animate({ height : 0}, 250, function(){
					
					$tr.remove();
					
					if( typeof(callback) == 'function' ) {
					
						callback();
					
					}
					
					
				});
				
					
			}, 250);
			
		},
		
		
		/*
		*  remove_el
		*
		*  This function will remove an element with animation
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery selection)
		*  @param	callback (function) runs on complete
		*  @param	end_height (int)
		*  @return	n/a
		*/
		
		remove_el : function( $el, callback, end_height ){
			
			// defaults
			end_height = end_height || 0;
			
			
			// set layout
			$el.css({
				height		: $el.height(),
				width		: $el.width(),
				position	: 'absolute'
			});
			
			
			// wrap field
			$el.wrap( '<div class="fieldmaster-temp-wrap" style="height:' + $el.outerHeight(true) + 'px"></div>' );
			
			
			// fade $el
			$el.animate({ opacity : 0 }, 250);
			
			
			// remove
			$el.parent('.fieldmaster-temp-wrap').animate({ height : end_height }, 250, function(){
				
				$(this).remove();
				
				if( typeof(callback) == 'function' ) {
				
					callback();
				
				}
				
			});
			
			
		},
		
		
		/*
		*  isset
		*
		*  This function will return true if an object key exists
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	(object)
		*  @param	key1 (string)
		*  @param	key2 (string)
		*  @param	...
		*  @return	(boolean)
		*/
		
		isset : function(){
			
			var a = arguments,
		        l = a.length,
		        c = null,
		        undef;
			
		    if (l === 0) {
		        throw new Error('Empty isset');
		    }
			
			c = a[0];
			
		    for (i = 1; i < l; i++) {
		    	
		        if (a[i] === undef || c[ a[i] ] === undef) {
		            return false;
		        }
		        
		        c = c[ a[i] ];
		        
		    }
		    
		    return true;	
			
		},
		
		
		/*
		*  maybe_get
		*
		*  This function will attempt to return a value and return null if not possible
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	obj (object) the array to look within
		*  @param	key (key) the array key to look for. Nested values may be found using '/'
		*  @param	value (mixed) the value returned if not found
		*  @return	(mixed)
		*/
		
		maybe_get: function( obj, key, value ){
			
			// default
			if( typeof value == 'undefined' ) value = null;
						
			
			// convert type to string and split
			keys = String(key).split('.');
			
			
			// loop through keys
			for( var i in keys ) {
				
				// vars
				var key = keys[i];
				
				
				// bail ealry if not set
				if( typeof obj[ key ] === 'undefined' ) {
					
					return value;
					
				}
				
				
				// update obj
				obj = obj[ key ];
				
			}
			
			
			// return
			return obj;
			
		},
		
		
		/*
		*  open_popup
		*
		*  This function will create and open a popup modal
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	args (object)
		*  @return	n/a
		*/
		
		open_popup : function( args ){
			
			// vars
			$popup = $('body > #fieldmaster-popup');
			
			
			// already exists?
			if( $popup.exists() ) {
			
				return update_popup(args);
				
			}
			
			
			// template
			var tmpl = [
				'<div id="fieldmaster-popup">',
					'<div class="fieldmaster-popup-box fieldmaster-box">',
						'<div class="title"><h3></h3><a href="#" class="fieldmaster-icon -cancel grey fieldmaster-close-popup"></a></div>',
						'<div class="inner"></div>',
						'<div class="loading"><i class="fieldmaster-loading"></i></div>',
					'</div>',
					'<div class="bg"></div>',
				'</div>'
			].join('');
			
			
			// append
			$('body').append( tmpl );
			
			
			$('#fieldmaster-popup').on('click', '.bg, .fieldmaster-close-popup', function( e ){
				
				e.preventDefault();
				
				fieldmaster.close_popup();
				
			});
			
			
			// update
			return this.update_popup(args);
			
		},
		
		
		/*
		*  update_popup
		*
		*  This function will update the content within a popup modal
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	args (object)
		*  @return	n/a
		*/
		
		update_popup : function( args ){
			
			// vars
			$popup = $('#fieldmaster-popup');
			
			
			// validate
			if( !$popup.exists() )
			{
				return false
			}
			
			
			// defaults
			args = $.extend({}, {
				title	: '',
				content : '',
				width	: 0,
				height	: 0,
				loading : false
			}, args);
			
			
			if( args.title ) {
			
				$popup.find('.title h3').html( args.title );
			
			}
			
			if( args.content ) {
				
				$inner = $popup.find('.inner:first');
				
				$inner.html( args.content );
				
				fieldmaster.do_action('append', $inner);
				
				// update height
				$inner.attr('style', 'position: relative;');
				args.height = $inner.outerHeight();
				$inner.removeAttr('style');
				
			}
			
			if( args.width ) {
			
				$popup.find('.fieldmaster-popup-box').css({
					'width'			: args.width,
					'margin-left'	: 0 - (args.width / 2)
				});
				
			}
			
			if( args.height ) {
				
				// add h3 height (44)
				args.height += 44;
				
				$popup.find('.fieldmaster-popup-box').css({
					'height'		: args.height,
					'margin-top'	: 0 - (args.height / 2)
				});	
				
			}
			
			
			if( args.loading ) {
			
				$popup.find('.loading').show();
				
			} else {
			
				$popup.find('.loading').hide();
				
			}
			
			return $popup;
		},
		
		
		/*
		*  close_popup
		*
		*  This function will close and remove a popup modal
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		close_popup : function(){
			
			// vars
			$popup = $('#fieldmaster-popup');
			
			
			// already exists?
			if( $popup.exists() )
			{
				$popup.remove();
			}
			
		},
		
		
		/*
		*  update_user_setting
		*
		*  This function will send an AJAX request to update a user setting
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		update_user_setting : function( name, value ) {
			
			// ajax
			$.ajax({
		    	url			: fieldmaster.get('ajaxurl'),
				dataType	: 'html',
				type		: 'post',
				data		: fieldmaster.prepare_for_ajax({
					'action'	: 'fieldmaster/update_user_setting',
					'name'		: name,
					'value'		: value
				})
			});
			
		},
		
		
		/*
		*  prepare_for_ajax
		*
		*  This function will prepare data for an AJAX request
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	args (object)
		*  @return	args
		*/
		
		prepare_for_ajax : function( args ) {
			
			// vars
			args.nonce = fieldmaster.get('nonce');
			args.post_id = fieldmaster.get('post_id');
			
			
			// filter for 3rd party customization
			args = fieldmaster.apply_filters('prepare_for_ajax', args);	
			
			
			// return
			return args;
			
		},
		
		
		/*
		*  is_ajax_success
		*
		*  This function will return true for a successful WP AJAX response
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	json (object)
		*  @return	(boolean)
		*/
		
		is_ajax_success : function( json ) {
			
			if( json && json.success ) {
				
				return true;
				
			}
			
			return false;
			
		},
		
		
		/*
		*  get_ajax_message
		*
		*  This function will return an object containing error/message information
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	json (object)
		*  @return	(boolean)
		*/
		
		get_ajax_message: function( json ) {
			
			// vars
			var message = {
				text: '',
				type: 'error'
			};
			
			
			// bail early if no json
			if( !json ) {
				
				return message;
				
			}
			
			
			// PHP error (too may themes will have warnings / errors. Don't show these in FieldMaster taxonomy popup)
/*
			if( typeof json === 'string' ) {
				
				message.text = json;
				return message;
					
			}
*/
			
			
			// success
			if( json.success ) {
				
				message.type = 'success';

			}
			
						
			// message
			if( json.data && json.data.message ) {
				
				message.text = json.data.message;
				
			}
			
			
			// error
			if( json.data && json.data.error ) {
				
				message.text = json.data.error;
				
			}
			
			
			// return
			return message;
			
		},
		
		
		/*
		*  is_in_view
		*
		*  This function will return true if a jQuery element is visible in browser
		*
		*  @type	function
		*  @date	8/09/2014
		*  @since	5.0.0
		*
		*  @param	$el (jQuery)
		*  @return	(boolean)
		*/
		
		is_in_view: function( $el ) {
			
			// vars
		    var elemTop = $el.offset().top,
		    	elemBottom = elemTop + $el.height();
		    
		    
			// bail early if hidden
			if( elemTop === elemBottom ) {
				
				return false;
				
			}
			
			
			// more vars
			var docViewTop = $(window).scrollTop(),
				docViewBottom = docViewTop + $(window).height();
			
			
			// return
		    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
					
		},
		
		
		/*
		*  val
		*
		*  This function will update an elements value and trigger the change event if different
		*
		*  @type	function
		*  @date	16/10/2014
		*  @since	5.0.9
		*
		*  @param	$el (jQuery)
		*  @param	val (mixed)
		*  @return	n/a
		*/
		
		val: function( $el, val ){
			
			// vars
			var orig = $el.val();
			
			
			// update value
			$el.val( val );
			
			
			// trigger change
			if( val != orig ) {
				
				$el.trigger('change');
				
			}
			
		},
		
		
		/*
		*  str_replace
		*
		*  This function will perform a str replace similar to php function str_replace
		*
		*  @type	function
		*  @date	1/05/2015
		*  @since	5.2.3
		*
		*  @param	$search (string)
		*  @param	$replace (string)
		*  @param	$subject (string)
		*  @return	(string)
		*/
		
		str_replace: function( search, replace, subject ) {
			
			return subject.split(search).join(replace);
			
		},
		
		
		/*
		*  str_sanitize
		*
		*  description
		*
		*  @type	function
		*  @date	4/06/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		str_sanitize: function( string ) {
			
			// chars (https://jsperf.com/replace-foreign-characters)
			var map = {
	            "À": "A",
	            "Á": "A",
	            "Â": "A",
	            "Ã": "A",
	            "Ä": "A",
	            "Å": "A",
	            "Æ": "AE",
	            "Ç": "C",
	            "È": "E",
	            "É": "E",
	            "Ê": "E",
	            "Ë": "E",
	            "Ì": "I",
	            "Í": "I",
	            "Î": "I",
	            "Ï": "I",
	            "Ð": "D",
	            "Ñ": "N",
	            "Ò": "O",
	            "Ó": "O",
	            "Ô": "O",
	            "Õ": "O",
	            "Ö": "O",
	            "Ø": "O",
	            "Ù": "U",
	            "Ú": "U",
	            "Û": "U",
	            "Ü": "U",
	            "Ý": "Y",
	            "ß": "s",
	            "à": "a",
	            "á": "a",
	            "â": "a",
	            "ã": "a",
	            "ä": "a",
	            "å": "a",
	            "æ": "ae",
	            "ç": "c",
	            "è": "e",
	            "é": "e",
	            "ê": "e",
	            "ë": "e",
	            "ì": "i",
	            "í": "i",
	            "î": "i",
	            "ï": "i",
	            "ñ": "n",
	            "ò": "o",
	            "ó": "o",
	            "ô": "o",
	            "õ": "o",
	            "ö": "o",
	            "ø": "o",
	            "ù": "u",
	            "ú": "u",
	            "û": "u",
	            "ü": "u",
	            "ý": "y",
	            "ÿ": "y",
	            "Ā": "A",
	            "ā": "a",
	            "Ă": "A",
	            "ă": "a",
	            "Ą": "A",
	            "ą": "a",
	            "Ć": "C",
	            "ć": "c",
	            "Ĉ": "C",
	            "ĉ": "c",
	            "Ċ": "C",
	            "ċ": "c",
	            "Č": "C",
	            "č": "c",
	            "Ď": "D",
	            "ď": "d",
	            "Đ": "D",
	            "đ": "d",
	            "Ē": "E",
	            "ē": "e",
	            "Ĕ": "E",
	            "ĕ": "e",
	            "Ė": "E",
	            "ė": "e",
	            "Ę": "E",
	            "ę": "e",
	            "Ě": "E",
	            "ě": "e",
	            "Ĝ": "G",
	            "ĝ": "g",
	            "Ğ": "G",
	            "ğ": "g",
	            "Ġ": "G",
	            "ġ": "g",
	            "Ģ": "G",
	            "ģ": "g",
	            "Ĥ": "H",
	            "ĥ": "h",
	            "Ħ": "H",
	            "ħ": "h",
	            "Ĩ": "I",
	            "ĩ": "i",
	            "Ī": "I",
	            "ī": "i",
	            "Ĭ": "I",
	            "ĭ": "i",
	            "Į": "I",
	            "į": "i",
	            "İ": "I",
	            "ı": "i",
	            "Ĳ": "IJ",
	            "ĳ": "ij",
	            "Ĵ": "J",
	            "ĵ": "j",
	            "Ķ": "K",
	            "ķ": "k",
	            "Ĺ": "L",
	            "ĺ": "l",
	            "Ļ": "L",
	            "ļ": "l",
	            "Ľ": "L",
	            "ľ": "l",
	            "Ŀ": "L",
	            "ŀ": "l",
	            "Ł": "l",
	            "ł": "l",
	            "Ń": "N",
	            "ń": "n",
	            "Ņ": "N",
	            "ņ": "n",
	            "Ň": "N",
	            "ň": "n",
	            "ŉ": "n",
	            "Ō": "O",
	            "ō": "o",
	            "Ŏ": "O",
	            "ŏ": "o",
	            "Ő": "O",
	            "ő": "o",
	            "Œ": "OE",
	            "œ": "oe",
	            "Ŕ": "R",
	            "ŕ": "r",
	            "Ŗ": "R",
	            "ŗ": "r",
	            "Ř": "R",
	            "ř": "r",
	            "Ś": "S",
	            "ś": "s",
	            "Ŝ": "S",
	            "ŝ": "s",
	            "Ş": "S",
	            "ş": "s",
	            "Š": "S",
	            "š": "s",
	            "Ţ": "T",
	            "ţ": "t",
	            "Ť": "T",
	            "ť": "t",
	            "Ŧ": "T",
	            "ŧ": "t",
	            "Ũ": "U",
	            "ũ": "u",
	            "Ū": "U",
	            "ū": "u",
	            "Ŭ": "U",
	            "ŭ": "u",
	            "Ů": "U",
	            "ů": "u",
	            "Ű": "U",
	            "ű": "u",
	            "Ų": "U",
	            "ų": "u",
	            "Ŵ": "W",
	            "ŵ": "w",
	            "Ŷ": "Y",
	            "ŷ": "y",
	            "Ÿ": "Y",
	            "Ź": "Z",
	            "ź": "z",
	            "Ż": "Z",
	            "ż": "z",
	            "Ž": "Z",
	            "ž": "z",
	            "ſ": "s",
	            "ƒ": "f",
	            "Ơ": "O",
	            "ơ": "o",
	            "Ư": "U",
	            "ư": "u",
	            "Ǎ": "A",
	            "ǎ": "a",
	            "Ǐ": "I",
	            "ǐ": "i",
	            "Ǒ": "O",
	            "ǒ": "o",
	            "Ǔ": "U",
	            "ǔ": "u",
	            "Ǖ": "U",
	            "ǖ": "u",
	            "Ǘ": "U",
	            "ǘ": "u",
	            "Ǚ": "U",
	            "ǚ": "u",
	            "Ǜ": "U",
	            "ǜ": "u",
	            "Ǻ": "A",
	            "ǻ": "a",
	            "Ǽ": "AE",
	            "ǽ": "ae",
	            "Ǿ": "O",
	            "ǿ": "o",
	            
	            // extra
	            ' ': '_',
				'\'': '',
				'?': '',
				'/': '',
				'\\': '',
				'.': '',
				',': '',
				'`': '',
				'>': '',
				'<': '',
				'"': '',
				'[': '',
				']': '',
				'|': '',
				'{': '',
				'}': '',
				'(': '',
				')': ''
	        };
			
		    
		    // vars
		    var regexp = /\W/g,
		        mapping = function (c) { return (typeof map[c] !== 'undefined') ? map[c] : c; };
			
			
			// replace
			string = string.replace(regexp, mapping);
			
			
			// lower case
			string = string.toLowerCase();
			
			
			// return
			return string;
						
		},
		
		
		/*
		*  addslashes
		*
		*  This function mimics the PHP addslashes function. 
		*  Returns a string with backslashes before characters that need to be escaped.
		*
		*  @type	function
		*  @date	9/1/17
		*  @since	5.5.0
		*
		*  @param	text (string)
		*  @return	(string)
		*/
		
		addslashes: function(text){
			
			return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
			
		},
		
		
		/*
		*  render_select
		*
		*  This function will update a select field with new choices
		*
		*  @type	function
		*  @date	8/04/2014
		*  @since	5.0.0
		*
		*  @param	$select
		*  @param	choices
		*  @return	n/a
		*/
		
		render_select: function( $select, choices ){
			
			// vars
			var value = $select.val();
			
			
			// clear choices
			$select.html('');
			
			
			// bail early if no choices
			if( !choices ) {
				
				return;
				
			}
			
			
			// populate choices
			$.each(choices, function( i, item ){
				
				// vars
				var $optgroup = $select;
				
				
				// add group
				if( item.group ) {
					
					$optgroup = $select.find('optgroup[label="' + item.group + '"]');
					
					if( !$optgroup.exists() ) {
						
						$optgroup = $('<optgroup label="' + item.group + '"></optgroup>');
						
						$select.append( $optgroup );
						
					}
					
				}
				
				
				// append select
				$optgroup.append( '<option value="' + item.value + '">' + item.label + '</option>' );
				
				
				// selectedIndex
				if( value == item.value ) {
					
					 $select.prop('selectedIndex', i);
					 
				}
				
			});
			
		},
		
		
		/*
		*  duplicate
		*
		*  This function will duplicate and return an element
		*
		*  @type	function
		*  @date	22/08/2015
		*  @since	5.2.3
		*
		*  @param	$el (jQuery) object to be duplicated
		*  @param	attr (string) attrbute name where $el id can be found
		*  @return	$el2 (jQuery)
		*/
		
		duplicate: function( args ){
			
			//console.time('duplicate');
			
			
			// backwards compatibility
			// - array of settings added in v5.4.6
			if( typeof args.length !== 'undefined' ) args = { $el: args };
			
			
			// defaults
			args = fieldmaster.parse_args(args, {
				$el: false,
				search: '',
				replace: '',
				before: function( $el ){},
				after: function( $el, $el2 ){},
				append: function( $el, $el2 ){ $el.after( $el2 ); }
			});
			
			
			// vars
			var $el = args.$el,
				$el2;
			
			
			// search
			if( !args.search ) args.search = $el.attr('data-id');
			
			
			// replace
			if( !args.replace ) args.replace = fieldmaster.get_uniqid();
			
			
			// before
			// - allow fieldmaster to modify DOM
			// - fixes bug where select field option is not selected
			args.before.apply( this, [$el] );
			fieldmaster.do_action('before_duplicate', $el);
			
			
			// clone
			var	$el2 = $el.clone();
			
			
			// remove fieldmaster-clone (may be a clone)
			$el2.removeClass('fieldmaster-clone');
			
			
			// remove JS functionality
			fieldmaster.do_action('remove', $el2);
			
			
			// find / replace
			if( args.search ) {
				
				// replace data
				$el2.attr('data-id', args.replace);
				
				
				// replace ids
				$el2.find('[id*="' + args.search + '"]').each(function(){	
				
					$(this).attr('id', $(this).attr('id').replace(args.search, args.replace) );
					
				});
				
				
				// replace names
				$el2.find('[name*="' + args.search + '"]').each(function(){	
				
					$(this).attr('name', $(this).attr('name').replace(args.search, args.replace) );
					
				});
				
				
				// replace label for
				$el2.find('label[for*="' + args.search + '"]').each(function(){
				
					$(this).attr('for', $(this).attr('for').replace(args.search, args.replace) );
					
				});
				
			}
			
			
			// remove ui-sortable
			$el2.find('.ui-sortable').removeClass('ui-sortable');
			
			
			// after
			// - allow fieldmaster to modify DOM
			fieldmaster.do_action('after_duplicate', $el, $el2 );
			args.after.apply( this, [$el, $el2] );
			
			
			// append
			args.append.apply( this, [$el, $el2] );
			
			
			// add JS functionality
			// - allow element to be moved into a visible position before fire action
			setTimeout(function(){
				
				fieldmaster.do_action('append', $el2);
				
			}, 1);
			
			
			//console.timeEnd('duplicate');
			
			
			// return
			return $el2;
			
		},
		
		decode: function( string ){
			
			return $('<textarea/>').html( string ).text();
			
		},
		
		
		/*
		*  parse_args
		*
		*  This function will merge together defaults and args much like the WP wp_parse_args function
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @param	args (object)
		*  @param	defaults (object)
		*  @return	args
		*/
		
		parse_args: function( args, defaults ) {
			
			return $.extend({}, defaults, args);
			
		},
		
		
		/*
		*  enqueue_script
		*
		*  This function will append a script to the page
		*
		*  @source	https://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/
		*  @type	function
		*  @date	27/08/2016
		*  @since	5.4.0
		*
		*  @param	url (string)
		*  @param	callback (function)
		*  @return	na
		*/
		
		enqueue_script: function( url, callback ) {
			
			// vars
		    var script = document.createElement('script');
		    
		    
		    // atts
		    script.type = "text/javascript";
			script.src = url;
		    script.async = true;
			
			
			// ie
		    if( script.readyState ) {
			    
		        script.onreadystatechange = function(){
			        
		            if( script.readyState == 'loaded' || script.readyState == 'complete' ){
			            
		                script.onreadystatechange = null;
		                callback();
		                
		            }
		            
		        };
		    
		    // normal browsers
		    } else {
			    
		        script.onload = function(){
		            callback();
		        };
		        
		    }
		    
		    
		    // append
		    document.body.appendChild(script);
			
		}
		
	};
	
	
	/*
	*  fieldmaster.model
	*
	*  This model acts as a scafold for action.event driven modules
	*
	*  @type	object
	*  @date	8/09/2014
	*  @since	5.0.0
	*
	*  @param	(object)
	*  @return	(object)
	*/
	
	fieldmaster.model = {
		
		// vars
		actions:	{},
		filters:	{},
		events:		{},
		
		extend: function( args ){
			
			// extend
			var model = $.extend( {}, this, args );
			
			
			// setup actions
			$.each(model.actions, function( name, callback ){
				
				model._add_action( name, callback );
			
			});
			
			
			// setup filters
			$.each(model.filters, function( name, callback ){
				
				model._add_filter( name, callback );
			
			});
			
			
			// setup events
			$.each(model.events, function( name, callback ){
				
				model._add_event( name, callback );
				
			});
			
			
			// return
			return model;
			
		},
		
		_add_action: function( name, callback ) {
			
			// split
			var model = this,
				data = name.split(' ');
			
			
			// add missing priority
			var name = data[0] || '',
				priority = data[1] || 10;
			
			
			// add action
			fieldmaster.add_action(name, model[ callback ], priority, model);
			
		},
		
		_add_filter: function( name, callback ) {
			
			// split
			var model = this,
				data = name.split(' ');
			
			
			// add missing priority
			var name = data[0] || '',
				priority = data[1] || 10;
			
			
			// add action
			fieldmaster.add_filter(name, model[ callback ], priority, model);
			
		},
		
		_add_event: function( name, callback ) {
			
			// vars
			var model = this,
				event = name.substr(0,name.indexOf(' ')),
				selector = name.substr(name.indexOf(' ')+1);
			
			
			// add event
			$(document).on(event, selector, function( e ){
				
				// append $el to event object
				e.$el = $(this);
				
				
				// event
				if( typeof model.event === 'function' ) {
					
					e = model.event( e );
					
				}
				
				
				// callback
				model[ callback ].apply(model, [e]);
				
			});
			
		},
		
		get: function( name, value ){
			
			// defaults
			value = value || null;
			
			
			// get
			if( typeof this[ name ] !== 'undefined' ) {
				
				value = this[ name ];
					
			}
			
			
			// return
			return value;
			
		},
		
		
		set: function( name, value ){
			
			// set
			this[ name ] = value;
			
			
			// function for 3rd party
			if( typeof this[ '_set_' + name ] === 'function' ) {
				
				this[ '_set_' + name ].apply(this);
				
			}
			
			
			// return for chaining
			return this;
			
		}
		
	};
	
	
	/*
	*  field
	*
	*  This model sets up many of the field's interactions
	*
	*  @type	function
	*  @date	21/02/2014
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	fieldmaster.field = fieldmaster.model.extend({
		
		// vars
		type:		'',
		o:			{},
		$field:		null,
		
		_add_action: function( name, callback ) {
			
			// vars
			var model = this;
			
			
			// update name
			name = name + '_field/type=' + model.type;
			
			
			// add action
			fieldmaster.add_action(name, function( $field ){
				
				// focus
				model.set('$field', $field);
				
				
				// callback
				model[ callback ].apply(model, arguments);
				
			});
			
		},
		
		_add_filter: function( name, callback ) {
			
			// vars
			var model = this;
			
			
			// update name
			name = name + '_field/type=' + model.type;
			
			
			// add action
			fieldmaster.add_filter(name, function( $field ){
				
				// focus
				model.set('$field', $field);
				
				
				// callback
				model[ callback ].apply(model, arguments);
				
			});
			
		},
		
		_add_event: function( name, callback ) {
			
			// vars
			var model = this,
				event = name.substr(0,name.indexOf(' ')),
				selector = name.substr(name.indexOf(' ')+1),
				context = fieldmaster.get_selector(model.type);
			
			
			// add event
			$(document).on(event, context + ' ' + selector, function( e ){
				
				// append $el to event object
				e.$el = $(this);
				e.$field = fieldmaster.get_closest_field(e.$el, model.type);
				
				
				// focus
				model.set('$field', e.$field);
				
				
				// callback
				model[ callback ].apply(model, [e]);
				
			});
			
		},
		
		_set_$field: function(){
			
			// callback
			if( typeof this.focus === 'function' ) {
				
				this.focus();
				
			}
			
		},
		
		// depreciated
		doFocus: function( $field ){
			
			return this.set('$field', $field);
			
		}
		
	});
	
	
	/*
	*  field
	*
	*  This model fires actions and filters for registered fields
	*
	*  @type	function
	*  @date	21/02/2014
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	fieldmaster.fields = fieldmaster.model.extend({
		
		actions: {
			'prepare'			: '_prepare',
			'prepare_field'		: '_prepare_field',
			'ready'				: '_ready',
			'ready_field'		: '_ready_field',
			'append'			: '_append',
			'append_field'		: '_append_field',
			'load'				: '_load',
			'load_field'		: '_load_field',
			'remove'			: '_remove',
			'remove_field'		: '_remove_field',
			'sortstart'			: '_sortstart',
			'sortstart_field'	: '_sortstart_field',
			'sortstop'			: '_sortstop',
			'sortstop_field'	: '_sortstop_field',
			'show'				: '_show',
			'show_field'		: '_show_field',
			'hide'				: '_hide',
			'hide_field'		: '_hide_field'
		},
		
		// prepare
		_prepare: function( $el ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('prepare_field', $(this));
				
			});
			
		},
		
		_prepare_field: function( $el ){
			
			fieldmaster.do_action('prepare_field/type=' + $el.data('type'), $el);
			
		},
		
		// ready
		_ready: function( $el ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('ready_field', $(this));
				
			});
			
		},
		
		_ready_field: function( $el ){
			
			fieldmaster.do_action('ready_field/type=' + $el.data('type'), $el);
			
		},
		
		// append
		_append: function( $el ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('append_field', $(this));
				
			});
			
		},
		
		_append_field: function( $el ){
		
			fieldmaster.do_action('append_field/type=' + $el.data('type'), $el);
			
		},
		
		// load
		_load: function( $el ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('load_field', $(this));
				
			});
			
		},
		
		_load_field: function( $el ){
		
			fieldmaster.do_action('load_field/type=' + $el.data('type'), $el);
			
		},
		
		// remove
		_remove: function( $el ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('remove_field', $(this));
				
			});
			
		},
		
		_remove_field: function( $el ){
		
			fieldmaster.do_action('remove_field/type=' + $el.data('type'), $el);
			
		},
		
		// sortstart
		_sortstart: function( $el, $placeholder ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('sortstart_field', $(this), $placeholder);
				
			});
			
		},
		
		_sortstart_field: function( $el, $placeholder ){
		
			fieldmaster.do_action('sortstart_field/type=' + $el.data('type'), $el, $placeholder);
			
		},
		
		// sortstop
		_sortstop: function( $el, $placeholder ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('sortstop_field', $(this), $placeholder);
				
			});
			
		},
		
		_sortstop_field: function( $el, $placeholder ){
		
			fieldmaster.do_action('sortstop_field/type=' + $el.data('type'), $el, $placeholder);
			
		},
		
		
		// hide
		_hide: function( $el, context ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('hide_field', $(this), context);
				
			});
			
		},
		
		_hide_field: function( $el, context ){
		
			fieldmaster.do_action('hide_field/type=' + $el.data('type'), $el, context);
			
		},
		
		// show
		_show: function( $el, context ){
		
			fieldmaster.get_fields('', $el).each(function(){
				
				fieldmaster.do_action('show_field', $(this), context);
				
			});
			
		},
		
		_show_field: function( $el, context ){
		
			fieldmaster.do_action('show_field/type=' + $el.data('type'), $el, context);
			
		}
		
	});
	
	
	/*
	*  ready
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	$(document).ready(function(){
		
		// action for 3rd party customization
		fieldmaster.do_action('ready', $('body'));
		
	});
	
	
	/*
	*  load
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	$(window).on('load', function(){
		
		// action for 3rd party customization
		fieldmaster.do_action('load', $('body'));
		
	});
	
	
	/*
	*  layout
	*
	*  This model handles the width layout for fields
	*
	*  @type	function
	*  @date	21/02/2014
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
		
	fieldmaster.layout = fieldmaster.model.extend({
		
		active: 0,
		
		actions: {
			'prepare 99': 	'prepare',
			'refresh': 		'refresh'
		},
		
		prepare: function(){
			
			// vars
			this.active = 1;
			
			
			// render
			this.refresh();
			
		},
		
		refresh: function( $el ){ 
			
			// bail early if not yet active
			if( !this.active ) return;
			
			
			// defaults
			$el = $el || $('body');
			
			
			// reference
			var self = this;
			
			
			// render
			this.render_tables( $el );
			this.render_groups( $el );
			
		},
		
		render_tables: function( $el ){ 
			
			// reference
			var self = this;
			
			
			// vars
			var $tables = $el.find('.fieldmaster-table:visible');
			
			
			// appent self if is tr
			if( $el.is('tr') ) {
				
				$tables = $el.parent().parent();
				
			}
			
			
			// loop
			$tables.each(function(){
				
				self.render_table( $(this) );
				
			});
			
		},
		
		render_table: function( $table ){
			
			// vars
			var $ths = $table.find('> thead th.fieldmaster-th'),
				colspan = 1,
				available_width = 100;
			
			
			// bail early if no $ths
			if( !$ths.exists() ) return;
			
			
			// vars
			var $trs = $table.find('> tbody > tr'),
				$tds = $trs.find('> td.fm-field');
			
			
			// remove clones if has visible rows
			if( $trs.hasClass('fieldmaster-clone') && $trs.length > 1 ) {
				
				$tds = $trs.not('.fieldmaster-clone').find('> td.fm-field');
				
			}
			
			
			// render th/td visibility
			$ths.each(function(){
				
				// vars
				var $th = $(this),
					key = $th.attr('data-key'),
					$td = $tds.filter('[data-key="'+key+'"]');
				
				// clear class
				$td.removeClass('appear-empty');
				$th.removeClass('hidden-by-conditional-logic');
				
				
				// no td
				if( !$td.exists() ) {
					
					// do nothing
				
				// if all td are hidden
				} else if( $td.not('.hidden-by-conditional-logic').length == 0 ) {
					
					$th.addClass('hidden-by-conditional-logic');
				
				// if 1 or more td are visible
				} else {
					
					$td.filter('.hidden-by-conditional-logic').addClass('appear-empty');
					
				}
				
			});
			
			
			
			// clear widths
			$ths.css('width', 'auto');
			
			
			// update $ths
			$ths = $ths.not('.hidden-by-conditional-logic');
			
			
			// set colspan
			colspan = $ths.length;
			
			
			// set custom widths first
			$ths.filter('[data-width]').each(function(){
				
				// vars
				var width = parseInt( $(this).attr('data-width') );
				
				
				// remove from available
				available_width -= width;
				
				
				// set width
				$(this).css('width', width + '%');
				
			});
			
			
			// update $ths
			$ths = $ths.not('[data-width]');
			
			
			// set custom widths first
			$ths.each(function(){
				
				// cal width
				var width = available_width / $ths.length;
				
				
				// set width
				$(this).css('width', width + '%');
				
			});
			
			
			// update colspan
			$table.find('.fieldmaster-row .fm-field.-collapsed-target').removeAttr('colspan');
			$table.find('.fieldmaster-row.-collapsed .fm-field.-collapsed-target').attr('colspan', colspan);
			
		},
		
		render_groups: function( $el ){
			
			// reference
			var self = this;
			
			
			// vars
			var $groups = $el.find('.fm-fields:visible');
			
			
			// appent self if is '.fm-fields'
			if( $el && $el.is('.fm-fields') ) {
				
				$groups = $groups.add( $el );
				
			}
			
			
			// loop
			$groups.each(function(){
				
				self.render_group( $(this) );
				
			});
			
		},
		
		render_group: function( $el ){
			
			// vars
			var $els = $(),
				top = 0,
				height = 0,
				cell = -1;
			
			
			// get fields
			var $fields = $el.children('.fm-field[data-width]:visible');
			
			
			// bail early if no fields
			if( !$fields.exists() ) return;
			
			
			// reset fields
			$fields.removeClass('fieldmaster-r0 fieldmaster-c0').css({'min-height': 0});
			
			
			// loop
			$fields.each(function( i ){
				
				// vars
				var $el = $(this),
					this_top = $el.position().top;
				
				
				// set top
				if( i == 0 ) top = this_top;
				
				
				// detect new row
				if( this_top != top ) {
					
					// set previous heights
					$els.css({'min-height': (height+1)+'px'});
					
					// reset
					$els = $();
					top = $el.position().top; // don't use variable as this value may have changed due to min-height css
					height = 0;
					cell = -1;
					
				}
				
								
				// increase
				cell++;
				
				
				// set height
				height = ($el.outerHeight() > height) ? $el.outerHeight() : height;
				
				
				// append
				$els = $els.add( $el );
				
				
				// add classes
				if( this_top == 0 ) {
					
					$el.addClass('fieldmaster-r0');
					
				} else if( cell == 0 ) {
					
					$el.addClass('fieldmaster-c0');
					
				}
				
			});
			
			
			// clean up
			if( $els.exists() ) {
				
				$els.css({'min-height': (height+1)+'px'});
				
			}
			
		}
		
	});
	
	
	/*
	*  Force revisions
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	$(document).on('change', '.fm-field input, .fm-field textarea, .fm-field select', function(){
		
		// preview hack
		if( $('#fieldmaster-form-data input[name="_fieldmasterchanged"]').exists() ) {
		
			$('#fieldmaster-form-data input[name="_fieldmasterchanged"]').val(1);
			
		}
		
		
		// action for 3rd party customization
		fieldmaster.do_action('change', $(this));
		
	});
	
	
	/*
	*  preventDefault helper
	*
	*  This function will prevent default of any link with an href of #
	*
	*  @type	function
	*  @date	24/07/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	$(document).on('click', '.fm-field a[href="#"]', function( e ){
		
		e.preventDefault();
		
	});
	
	
	/*
	*  unload
	*
	*  This model handles the unload prompt
	*
	*  @type	function
	*  @date	21/02/2014
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
		
	fieldmaster.unload = fieldmaster.model.extend({
		
		active: 1,
		changed: 0,
		
		filters: {
			'validation_complete': 'validation_complete'
		},
		
		actions: {
			'change':	'on',
			'submit':	'off'
		},
		
		events: {
			'submit form':	'off'
		},
		
		validation_complete: function( json, $form ){
			
			if( json && json.errors ) {
				
				this.on();
				
			}
			
			// return
			return json;
			
		},
		
		on: function(){
			
			// bail ealry if already changed (or not active)
			if( this.changed || !this.active ) {
				
				return;
				
			}
			
			
			// update 
			this.changed = 1;
			
			
			// add event
			$(window).on('beforeunload', this.unload);
			
		},
		
		off: function(){
			
			// update 
			this.changed = 0;
			
			
			// remove event
			$(window).off('beforeunload', this.unload);
			
		},
		
		unload: function(){
			
			// alert string
			return fieldmaster._e('unload');
			
		}
		 
	});
	
	
	fieldmaster.tooltip = fieldmaster.model.extend({
		
		$el: null,
		
		events: {
			'mouseenter .fieldmaster-js-tooltip':	'on',
			'mouseleave .fieldmaster-js-tooltip':	'off'
		},

		on: function( e ){
			
			//console.log('on');
			
			// vars
			var title = e.$el.attr('title');
			
			
			// hide empty titles
			if( !title ) {
				
				return;
									
			}
			
			
			// $t
			this.$el = $('<div class="fieldmaster-tooltip">' + title + '</div>');
			
			
			// append
			$('body').append( this.$el );
			
			
			// position
			var tolerance = 10;
				target_w = e.$el.outerWidth(),
				target_h = e.$el.outerHeight(),
				target_t = e.$el.offset().top,
				target_l = e.$el.offset().left,
				tooltip_w = this.$el.outerWidth(),
				tooltip_h = this.$el.outerHeight();
			
			
			// calculate top
			var top = target_t - tooltip_h,
				left = target_l + (target_w / 2) - (tooltip_w / 2);
			
			
			// too far left
			if( left < tolerance ) {
				
				this.$el.addClass('right');
				
				left = target_l + target_w;
				top = target_t + (target_h / 2) - (tooltip_h / 2);
			
			
			// too far right
			} else if( (left + tooltip_w + tolerance) > $(window).width() ) {
				
				this.$el.addClass('left');
				
				left = target_l - tooltip_w;
				top = target_t + (target_h / 2) - (tooltip_h / 2);
			
				
			// too far top
			} else if( top - $(window).scrollTop() < tolerance ) {
				
				this.$el.addClass('bottom');
				
				top = target_t + target_h;

			} else {
				
				this.$el.addClass('top');
				
			}
			
			
			// update css
			this.$el.css({ 'top': top, 'left': left });
			
			
			// avoid double title	
			e.$el.data('title', title);
			e.$el.attr('title', '');
			
		},
		
		off: function( e ){
			
			//console.log('off');
			
			// bail early if no $el
			if( !this.$el ) {
				
				return;
				
			}
			
			
			// replace title
			e.$el.attr('title', e.$el.data('title'));
			
			
			// remove tooltip
			this.$el.remove();
			
		}
		
	});
	
	
	fieldmaster.postbox = fieldmaster.model.extend({
		
		events: {
			'mouseenter .fieldmaster-postbox .handlediv':	'on',
			'mouseleave .fieldmaster-postbox .handlediv':	'off'
		},

		on: function( e ){
			
			e.$el.siblings('.hndle').addClass('hover');
			
		},
		
		off: function( e ){
			
			e.$el.siblings('.hndle').removeClass('hover');
			
		},
		
		render: function( args ){
			
			// defaults
			args = $.extend({}, {
				id: 		'',
				key:		'',
				style: 		'default',
				label: 		'top',
				edit_url:	'',
				edit_title:	'',
				visibility:	true
			}, args);
			
			
			// vars
			var $postbox = $('#' + args.id),
				$toggle = $('#' + args.id + '-hide'),
				$label = $toggle.parent();
			
			
			
			// add class
			$postbox.addClass('fieldmaster-postbox');
			$label.addClass('fieldmaster-postbox-toggle');
			
			
			// remove class
			$postbox.removeClass('hide-if-js');
			$label.removeClass('hide-if-js');
			
			
			// field group style
			if( args.style !== 'default' ) {
				
				$postbox.addClass( args.style );
				
			}
			
			
			// .inside class
			$postbox.children('.inside').addClass('fm-fields').addClass('-' + args.label);
			
				
			// visibility
			if( args.visibility ) {
				
				$toggle.prop('checked', true);
				
			} else {
				
				$postbox.addClass('fieldmaster-hidden');
				$label.addClass('fieldmaster-hidden');
				
			}
			
			
			// edit_url
			if( args.edit_url ) {
				
				$postbox.children('.hndle').append('<a href="' + args.edit_url + '" class="dashicons dashicons-admin-generic fieldmaster-hndle-cog fieldmaster-js-tooltip" title="' + args.edit_title + '"></a>');

			}
			
		}
		
	});
			
	
	/*
	*  Sortable
	*
	*  These functions will hook into the start and stop of a jQuery sortable event and modify the item and placeholder
	*
	*  @type	function
	*  @date	12/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	fieldmaster.add_action('sortstart', function( $item, $placeholder ){
		
		// if $item is a tr, apply some css to the elements
		if( $item.is('tr') ) {
			
			// temp set as relative to find widths
			$item.css('position', 'relative');
			
			
			// set widths for td children		
			$item.children().each(function(){
			
				$(this).width($(this).width());
				
			});
			
			
			// revert position css
			$item.css('position', 'absolute');
			
			
			// add markup to the placeholder
			$placeholder.html('<td style="height:' + $item.height() + 'px; padding:0;" colspan="' + $item.children('td').length + '"></td>');
		
		}
		
	});
	
	
	
	/*
	*  before & after duplicate
	*
	*  This function will modify the DOM before it is cloned. Primarily fixes a cloning issue with select elements
	*
	*  @type	function
	*  @date	16/05/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	fieldmaster.add_action('before_duplicate', function( $orig ){
		
		// add 'selected' class
		$orig.find('select option:selected').addClass('selected');
		
	});
	
	fieldmaster.add_action('after_duplicate', function( $orig, $duplicate ){
		
		// set select values
		$duplicate.find('select').each(function(){
			
			// vars
			var $select = $(this);
			
			
			// bail early if is 'Stylized UI'
			//if( $select.data('ui') ) return;


			// vars
			var val = [];
			
			
			// loop
			$select.find('option.selected').each(function(){
				
				val.push( $(this).val() );
				
		    });
		    
		    
		    // set val
			$select.val( val );
			
		});
		
		
		// remove 'selected' class
		$orig.find('select option.selected').removeClass('selected');
		$duplicate.find('select option.selected').removeClass('selected');
		
	});
	
	
/*
	
	
	console.time("fieldmaster_test_ready");
	console.time("fieldmaster_test_load");
	
	fieldmaster.add_action('ready', function(){
		
		console.timeEnd("fieldmaster_test_ready");
		
	}, 999);
	
	fieldmaster.add_action('load', function(){
		
		console.timeEnd("fieldmaster_test_load");
		
	}, 999);
*/


	/*
	*  indexOf
	*
	*  This function will provide compatibility for ie8
	*
	*  @type	function
	*  @date	5/3/17
	*  @since	5.5.10
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	if( !Array.prototype.indexOf ) {
		
	    Array.prototype.indexOf = function(val) {
	        return $.inArray(val, this);
	    };
	    
	}
	
})(jQuery);

(function($){
	
	fieldmaster.ajax = fieldmaster.model.extend({
		
		active: false,
		actions: {
			'ready': 'ready'
		},
		events: {
			'change #page_template':								'_change_template',
			'change #parent_id':									'_change_parent',
			'change #post-formats-select input':					'_change_format',
			'change .categorychecklist input':						'_change_term',
			'change .categorychecklist select':						'_change_term',
			'change .fieldmaster-taxonomy-field[data-save="1"] input':		'_change_term',
			'change .fieldmaster-taxonomy-field[data-save="1"] select':		'_change_term'
		},
		o: {
			//'post_id':		0,
			//'page_template':	0,
			//'page_parent':	0,
			//'page_type':		0,
			//'post_format':	0,
			//'post_taxonomy':	0
		},
		xhr: null,
		
		update: function( k, v ){
			
			this.o[ k ] = v;
			
			return this;
			
		},
		
		get: function( k ){
			
			return this.o[ k ] || null;
			
		},
		
		ready: function(){
			
			// update post_id
			this.update('post_id', fieldmaster.get('post_id'));
			
			
			// active
			this.active = true;
			
		},
		
/*
		timeout: null,
		maybe_fetch: function(){
			
			// reference
			var self = this;
			
			
			// abort timeout
			if( this.timeout ) {
				
				clearTimeout( this.timeout );
				
			}
			
			
		    // fetch
		    this.timeout = setTimeout(function(){
			    
			    self.fetch();
			    
		    }, 100);
		    
		},
*/
		
		fetch: function(){
			
			// bail early if not active
			if( !this.active ) return;
			
			
			// bail early if no ajax
			if( !fieldmaster.get('ajax') ) return;
			
			
			// abort XHR if is already loading AJAX data
			if( this.xhr ) {
			
				this.xhr.abort();
				
			}
			
			
			// vars
			var self = this,
				data = this.o;
			
			
			// add action url
			data.action = 'fieldmaster/post/get_field_groups';
			
			
			// add ignore
			data.exists = [];
			
			$('.fieldmaster-postbox').not('.fieldmaster-hidden').each(function(){
				
				data.exists.push( $(this).attr('id').substr(4) );
				
			});
			
			
			// ajax
			this.xhr = $.ajax({
				url:		fieldmaster.get('ajaxurl'),
				data:		fieldmaster.prepare_for_ajax( data ),
				type:		'post',
				dataType:	'json',
				
				success: function( json ){
					
					if( fieldmaster.is_ajax_success( json ) ) {
						
						self.render( json.data );
						
					}
					
				}
			});
			
		},
		
		render: function( json ){
			
			// hide
			$('.fieldmaster-postbox').addClass('fieldmaster-hidden');
			$('.fieldmaster-postbox-toggle').addClass('fieldmaster-hidden');
			
			
			// reset style
			$('#fieldmaster-style').html('');
			
			
			// show the new postboxes
			$.each(json, function( k, field_group ){
				
				// vars
				var $postbox = $('#fieldmaster-' + field_group.key),
					$toggle = $('#fieldmaster-' + field_group.key + '-hide'),
					$label = $toggle.parent();
					
				
				// show
				// use show() to force display when postbox has been hidden by 'Show on screen' toggle
				$postbox.removeClass('fieldmaster-hidden hide-if-js').show();
				$label.removeClass('fieldmaster-hidden hide-if-js').show();
				$toggle.prop('checked', true);
				
				
				// replace HTML if needed
				var $replace = $postbox.find('.fieldmaster-replace-with-fields');
				
				if( $replace.exists() ) {
					
					$replace.replaceWith( field_group.html );
					
					fieldmaster.do_action('append', $postbox);
					
				}
				
				
				// update style if needed
				if( k === 0 ) {
					
					$('#fieldmaster-style').html( field_group.style );
					
				}
				
				
				// enable inputs
				$postbox.find('.fieldmaster-hidden-by-postbox').prop('disabled', false);
				
			});
			
			
			// disable inputs
			$('.fieldmaster-postbox.fieldmaster-hidden').find('select, textarea, input').not(':disabled').each(function(){
				
				$(this).addClass('fieldmaster-hidden-by-postbox').prop('disabled', true);
				
			});
			
		},
		
		sync_taxonomy_terms: function(){
			
			// vars
			var values = [''];
			
			
			// loop over term lists
			$('.categorychecklist, .fieldmaster-taxonomy-field').each(function(){
				
				// vars
				var $el = $(this),
					$checkbox = $el.find('input[type="checkbox"]').not(':disabled'),
					$radio = $el.find('input[type="radio"]').not(':disabled'),
					$select = $el.find('select').not(':disabled'),
					$hidden = $el.find('input[type="hidden"]').not(':disabled');
				
				
				// bail early if not a field which saves taxonomy terms to post
				if( $el.is('.fieldmaster-taxonomy-field') && $el.attr('data-save') != '1' ) {
					
					return;
					
				}
				
				
				// bail early if in attachment
				if( $el.closest('.media-frame').exists() ) {
					
					return;
				
				}
				
				
				// checkbox
				if( $checkbox.exists() ) {
					
					$checkbox.filter(':checked').each(function(){
						
						values.push( $(this).val() );
						
					});
					
				} else if( $radio.exists() ) {
					
					$radio.filter(':checked').each(function(){
						
						values.push( $(this).val() );
						
					});
					
				} else if( $select.exists() ) {
					
					$select.find('option:selected').each(function(){
						
						values.push( $(this).val() );
						
					});
					
				} else if( $hidden.exists() ) {
					
					$hidden.each(function(){
						
						// ignor blank values
						if( ! $(this).val() ) {
							
							return;
							
						}
						
						values.push( $(this).val() );
						
					});
					
				}
								
			});
	
			
			// filter duplicates
			values = values.filter (function (v, i, a) { return a.indexOf (v) == i });
			
			
			// update screen
			this.update( 'post_taxonomy', values ).fetch();
			
		},
		
		
		/*
		*  events
		*
		*  description
		*
		*  @type	function
		*  @date	29/09/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_change_template: function( e ){
			
			// vars
			var page_template = e.$el.val();
			
			
			// update & fetch
			this.update('page_template', page_template).fetch();
			
		},
		
		_change_parent: function( e ){
			
			// vars
			var page_type = 'parent',
				page_parent = 0;
			
			
			// if is child
			if( e.$el.val() != "" ) {
			
				page_type = 'child';
				page_parent = e.$el.val();
				
			}
			
			// update & fetch
			this.update('page_type', page_type).update('page_parent', page_parent).fetch();
			
		},
		
		_change_format: function( e ){
			
			// vars			
			var post_format = e.$el.val();
			
			
			// default
			if( post_format == '0' ) {
				
				post_format = 'standard';
				
			}
			
			
			// update & fetch
			this.update('post_format', post_format).fetch();
			
		},
		
		_change_term: function( e ){
			
			// reference
			var self = this;
			
			
			// bail early if within media popup
			if( e.$el.closest('.media-frame').exists() ) {
				
				return;
			
			}
			
			
			// set timeout to fix issue with chrome which does not register the change has yet happened
			setTimeout(function(){
				
				self.sync_taxonomy_terms();
			
			}, 1);
			
			
		}
		
	});

	
})(jQuery);

(function($){
	
	fieldmaster.fields.checkbox = fieldmaster.field.extend({
		
		type: 'checkbox',
		
		events: {
			'change input':				'_change',
			'click .fieldmaster-add-checkbox':	'_add'
		},
		
		
		/*
		*  focus
		*
		*  This function will setup variables when focused on a field
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		focus: function(){
			
			// get elements
			this.$ul = this.$field.find('ul');
			this.$input = this.$field.find('input[type="hidden"]');
			
		},
		
		
		add: function(){
			
			// vars
			var name = this.$input.attr('name') + '[]';
			
			
			// vars
			var html = '<li><input class="fieldmaster-checkbox-custom" type="checkbox" checked="checked" /><input type="text" name="'+name+'" /></li>';
			
			
			// append
			this.$ul.find('.fieldmaster-add-checkbox').parent('li').before( html );	
			
		},
		
		_change: function( e ){
			
			// vars
			var $ul = this.$ul,
				$inputs = $ul.find('input[type="checkbox"]').not('.fieldmaster-checkbox-toggle'),
				checked = e.$el.is(':checked');
			
			
			// is toggle?
			if( e.$el.hasClass('fieldmaster-checkbox-toggle') ) {
				
				// toggle all
				$inputs.prop('checked', checked).trigger('change');
				
				
				// return
				return;
				
			}
			
			
			// is custom
			if( e.$el.hasClass('fieldmaster-checkbox-custom') ) {
				
				// vars
				var $text = e.$el.next('input[type="text"]');
				
				
				// toggle disabled
				e.$el.next('input[type="text"]').prop('disabled', !checked);
				
				
				// remove complelety if no value
				if( !checked && $text.val() == '' ) {
					
					e.$el.parent('li').remove();
				
				}
			}
			
			
			// bail early if no toggle
			if( !$ul.find('.fieldmaster-checkbox-toggle').exists() ) {
				
				return;
				
			}
			
			
			// determine if all inputs are checked
			var checked = ( $inputs.not(':checked').length == 0 );
			
			
			// update toggle
			$ul.find('.fieldmaster-checkbox-toggle').prop('checked', checked);
			
		},
		
		_add: function( e ){
			
			this.add();
			
		}
		
	});
	
})(jQuery);

(function($){
	
	fieldmaster.fields.color_picker = fieldmaster.field.extend({
		
		type: 'color_picker',
		$input: null,
		$hidden: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		focus: function(){
			
			this.$input = this.$field.find('input[type="text"]');
			this.$hidden = this.$field.find('input[type="hidden"]');
			
		},
		
		initialize: function(){
			
			// reference
			var $input = this.$input,
				$hidden = this.$hidden;
			
			
			// trigger change function
			var change_hidden = function(){
				
				// timeout is required to ensure the $input val is correct
				setTimeout(function(){ 
					
					fieldmaster.val( $hidden, $input.val() );
					
				}, 1);
				
			}
			
			
			// args
			var args = {
				
				defaultColor: false,
				palettes: true,
				hide: true,
				change: change_hidden,
				clear: change_hidden
				
			}
 			
 			
 			// filter
 			var args = fieldmaster.apply_filters('color_picker_args', args, this.$field);
        	
        	
 			// iris
			this.$input.wpColorPicker(args);
			
		}
		
	});
	
})(jQuery);

(function($){
	
	fieldmaster.conditional_logic = fieldmaster.model.extend({
			
		actions: {
			'prepare 20': 	'render',
			'append 20': 	'render'
		},
		
		events: {
			'change .fm-field input': 		'change',
			'change .fm-field textarea': 	'change',
			'change .fm-field select': 	'change'
		},
		
		items: {},
		triggers: {},
		
		
		/*
		*  add
		*
		*  This function will add a set of conditional logic rules
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	target (string) target field key
		*  @param	groups (array) rule groups
		*  @return	$post_id (int)
		*/
		
		add: function( target, groups ){
			
			// debug
			//console.log( 'conditional_logic.add(%o, %o)', target, groups );
			
			
			// populate triggers
			for( var i in groups ) {
				
				// vars
				var group = groups[i];
				
				for( var k in group ) {
					
					// vars
					var rule = group[k],
						trigger = rule.field,
						triggers = this.triggers[ trigger ] || {};
					
					
					// append trigger (sub field will simply override)
					triggers[ target ] = target;
					
					
					// update
					this.triggers[ trigger ] = triggers;
										
				}
				
			}
			
			
			// append items
			this.items[ target ] = groups;
			
		},
		
		
		/*
		*  render
		*
		*  This function will render all fields
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		render: function( $el ){
			
			// debug
			//console.log('conditional_logic.render(%o)', $el);
			
			
			// defaults
			$el = $el || false;
			
			
			// get targets
			var $targets = fieldmaster.get_fields( '', $el, true );
			
			
			// render fields
			this.render_fields( $targets );
			
			
			// action for 3rd party customization
			fieldmaster.do_action('refresh', $el);
			
		},
		
		
		/*
		*  change
		*
		*  This function is called when an input is changed and will render any fields which are considered targets of this trigger
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		change: function( e ){
			
			// debug
			//console.log( 'conditional_logic.change(%o)', $input );
			
			
			// vars
			var $input = e.$el,
				$field = fieldmaster.get_field_wrap( $input ),
				key = $field.data('key');
			
			
			// bail early if this field does not trigger any actions
			if( typeof this.triggers[key] === 'undefined' ) {
				
				return false;
				
			}
			
			
			// vars
			$parent = $field.parent();
			
			
			// update visibility
			for( var i in this.triggers[ key ] ) {
				
				// get the target key
				var target_key = this.triggers[ key ][ i ];
				
				
				// get targets
				var $targets = fieldmaster.get_fields(target_key, $parent, true);
				
				
				// render
				this.render_fields( $targets );
				
			}
			
			
			// action for 3rd party customization
			fieldmaster.do_action('refresh', $parent);
			
		},
		
		
		/*
		*  render_fields
		*
		*  This function will render a selection of fields
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		render_fields: function( $targets ) {
		
			// reference
			var self = this;
			
			
			// loop over targets and render them			
			$targets.each(function(){
					
				self.render_field( $(this) );
				
			});
			
		},
		
		
		/*
		*  render_field
		*
		*  This function will render a field
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		render_field : function( $target ){
			
			// vars
			var key = $target.data('key');
			
			
			// bail early if this field does not contain any conditional logic
			if( typeof this.items[ key ] === 'undefined' ) {
				
				return false;
				
			}
			
			
			// vars
			var visibility = false;
			
			
			// debug
			//console.log( 'conditional_logic.render_field(%o)', $field );
			
			
			// get conditional logic
			var groups = this.items[ key ];
			
			
			// calculate visibility
			for( var i = 0; i < groups.length; i++ ) {
				
				// vars
				var group = groups[i],
					match_group	= true;
				
				for( var k = 0; k < group.length; k++ ) {
					
					// vars
					var rule = group[k];
					
					
					// get trigger for rule
					var $trigger = this.get_trigger( $target, rule.field );
					
					
					// break if rule did not validate
					if( !this.calculate(rule, $trigger, $target) ) {
						
						match_group = false;
						break;
						
					}
										
				}
				
				
				// set visibility if rule group did validate
				if( match_group ) {
					
					visibility = true;
					break;
					
				}
				
			}
			
			
			// hide / show field
			if( visibility ) {
				
				this.show_field( $target );					
			
			} else {
				
				this.hide_field( $target );
			
			}
			
		},
		
		
		/*
		*  show_field
		*
		*  This function will show a field
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		show_field: function( $field ){
			
			// debug
			//console.log('show_field(%o)', $field);
			
			
			// vars
			var key = $field.data('key');
			
			
			// remove class
			$field.removeClass( 'hidden-by-conditional-logic' );
			
			
			// enable
			fieldmaster.enable_form( $field, 'condition_'+key );
			
						
			// action for 3rd party customization
			fieldmaster.do_action('show_field', $field, 'conditional_logic' );
			
		},
		
		
		/*
		*  hide_field
		*
		*  This function will hide a field
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		hide_field : function( $field ){
			
			// debug
			//console.log('hide_field(%o)', $field);
			
			
			// vars
			var key = $field.data('key');
			
			
			// add class
			$field.addClass( 'hidden-by-conditional-logic' );
			
			
			// disable
			fieldmaster.disable_form( $field, 'condition_'+key );
						
			
			// action for 3rd party customization
			fieldmaster.do_action('hide_field', $field, 'conditional_logic' );
			
		},
		
		
		/*
		*  get_trigger
		*
		*  This function will return the relevant $trigger for a $target
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		get_trigger: function( $target, key ){
			
			// vars
			var selector = fieldmaster.get_selector( key );
			
			
			// find sibling $trigger
			var $trigger = $target.siblings( selector );
			
			
			// parent trigger
			if( !$trigger.exists() ) {
				
				// vars
				var parent = fieldmaster.get_selector();
				
				
				// loop through parent fields and review their siblings too
				$target.parents( parent ).each(function(){
					
					// find sibling $trigger
					$trigger = $(this).siblings( selector );
					
					
					// bail early if $trigger is found
					if( $trigger.exists() ) {
						
						return false;
						
					}
	
				});
				
			}
			
			
			// bail early if no $trigger is found
			if( !$trigger.exists() ) {
				
				return false;
				
			}
			
			
			// return
			return $trigger;
			
		},
		
		
		/*
		*  calculate
		*
		*  This function will calculate if a rule matches based on the $trigger
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		calculate : function( rule, $trigger, $target ){
			
			// bail early if $trigger could not be found
			if( !$trigger || !$target ) return false;
			
			
			// debug
			//console.log( 'calculate(%o, %o, %o)', rule, $trigger, $target);
			
			
			// vars
			var match = false,
				type = $trigger.data('type');
			
			
			// input with :checked
			if( type == 'true_false' || type == 'checkbox' || type == 'radio' ) {
				
				match = this.calculate_checkbox( rule, $trigger );
	        
				
			} else if( type == 'select' ) {
				
				match = this.calculate_select( rule, $trigger );
								
			}
			
			
			// reverse if 'not equal to'
			if( rule.operator === "!=" ) {
				
				match = !match;
					
			}
	        
			
			// return
			return match;
			
		},
		
		calculate_checkbox: function( rule, $trigger ){
			
			// look for selected input
			var match = $trigger.find('input[value="' + rule.value + '"]:checked').exists();
			
			
			// override for "allow null"
			if( rule.value === '' && !$trigger.find('input:checked').exists() ) {
				
				match = true;
				
			}
			
			
			// return
			return match;
			
		},
		
		
		calculate_select: function( rule, $trigger ){
			
			// vars
			var $select = $trigger.find('select'),
				val = $select.val();
			
			
			// check for no value
			if( !val && !$.isNumeric(val) ) {
				
				val = '';
				
			}
			
			
			// convert to array
			if( !$.isArray(val) ) {
				
				val = [ val ];
				
			}
			
			
			// calc
			match = ($.inArray(rule.value, val) > -1);

			
			// return
			return match;
			
		}
		
	});

})(jQuery);

(function($){
	
	/*
	*  fieldmaster.datepicker
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	fieldmaster.datepicker = fieldmaster.model.extend({
		
		actions: {
			'ready 1': 'ready'
		},
		
		ready: function(){
			
			// vars
			var locale = fieldmaster.get('locale'),
				rtl = fieldmaster.get('rtl')
				l10n = fieldmaster._e('date_picker');
			
			
			// bail ealry if no l10n (fiedl groups admin page)
			if( !l10n ) return;
			
			
			// bail ealry if no datepicker library
			if( typeof $.datepicker === 'undefined' ) return;
			
			
			// rtl
			l10n.isRTL = rtl;
			
			
			// append
			$.datepicker.regional[ locale ] = l10n;
			$.datepicker.setDefaults(l10n);
			
		},
		
		
		/*
		*  init
		*
		*  This function will initialize JS
		*
		*  @type	function
		*  @date	2/06/2016
		*  @since	5.3.8
		*
		*  @param	$input (jQuery selector)
		*  @param	args (object)
		*  @return	n/a
		*/
		
		init: function( $input, args ){
			
			// bail ealry if no datepicker library
			if( typeof $.datepicker === 'undefined' ) return;
			
			
			// defaults
			args = args || {};
			
			
			// add date picker
			$input.datepicker( args );
			
			
			// wrap the datepicker (only if it hasn't already been wrapped)
			if( $('body > #ui-datepicker-div').exists() ) {
			
				$('body > #ui-datepicker-div').wrap('<div class="fieldmaster-ui-datepicker" />');
				
			}
		
		},
		
		
		/*
		*  init
		*
		*  This function will remove JS
		*
		*  @type	function
		*  @date	2/06/2016
		*  @since	5.3.8
		*
		*  @param	$input (jQuery selector)
		*  @return	n/a
		*/
		
		destroy: function( $input ){
			
			// do nothing
			
		}
		
	});
		
	fieldmaster.fields.date_picker = fieldmaster.field.extend({
		
		type: 'date_picker',
		$el: null,
		$input: null,
		$hidden: null,
		
		o: {},
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'blur input[type="text"]': 'blur'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-date-picker');
			this.$input = this.$el.find('input[type="text"]');
			this.$hidden = this.$el.find('input[type="hidden"]');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		initialize: function(){
			
			// save_format - compatibility with FieldMaster < 5.0.0
			if( this.o.save_format ) {
				
				return this.initialize2();
				
			}
			
			
			// create options
			var args = { 
				dateFormat:			this.o.date_format,
				altField:			this.$hidden,
				altFormat:			'yymmdd',
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.o.first_day
			};
			
			
			// filter for 3rd party customization
			args = fieldmaster.apply_filters('date_picker_args', args, this.$field);
			
			
			// add date picker
			fieldmaster.datepicker.init( this.$input, args );
			
			
			// action for 3rd party customization
			fieldmaster.do_action('date_picker_init', this.$input, args, this.$field);
			
		},
		
		initialize2: function(){
			
			// get and set value from alt field
			this.$input.val( this.$hidden.val() );
			
			
			// create options
			var args =  { 
				dateFormat:			this.o.date_format,
				altField:			this.$hidden,
				altFormat:			this.o.save_format,
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.o.first_day
			};
			
			
			// filter for 3rd party customization
			args = fieldmaster.apply_filters('date_picker_args', args, this.$field);
			
			
			// backup
			var dateFormat = args.dateFormat;
			
			
			// change args.dateFormat
			args.dateFormat = this.o.save_format;
				
			
			// add date picker
			fieldmaster.datepicker.init( this.$input, args );
			
			
			// now change the format back to how it should be.
			this.$input.datepicker( 'option', 'dateFormat', dateFormat );
			
			
			// action for 3rd party customization
			fieldmaster.do_action('date_picker_init', this.$input, args, this.$field);
			
		},
		
		blur: function(){
			
			if( !this.$input.val() ) {
			
				this.$hidden.val('');
				
			}
			
		}
		
	});
	
})(jQuery);

(function($){
	
	/*
	*  fieldmaster.datepicker
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	fieldmaster.datetimepicker = fieldmaster.model.extend({
		
		actions: {
			'ready 1': 'ready'
		},
		
		ready: function(){
			
			// vars
			var locale = fieldmaster.get('locale'),
				rtl = fieldmaster.get('rtl')
				l10n = fieldmaster._e('date_time_picker');
			
			
			// bail ealry if no l10n (fiedl groups admin page)
			if( !l10n ) return;
			
			
			// bail ealry if no timepicker library
			if( typeof $.timepicker === 'undefined' ) return;
			
			
			// rtl
			l10n.isRTL = rtl;
			
			
			// append
			$.timepicker.regional[ locale ] = l10n;
			$.timepicker.setDefaults(l10n);
			
		},
		
		
		/*
		*  init
		*
		*  This function will initialize JS
		*
		*  @type	function
		*  @date	2/06/2016
		*  @since	5.3.8
		*
		*  @param	$input (jQuery selector)
		*  @param	args (object)
		*  @return	n/a
		*/
		
		init: function( $input, args ){
			
			// bail ealry if no timepicker library
			if( typeof $.timepicker === 'undefined' ) return;
			
			
			// defaults
			args = args || {};
			
			
			// add date picker
			$input.datetimepicker( args );
			
			
			// wrap the datepicker (only if it hasn't already been wrapped)
			if( $('body > #ui-datepicker-div').exists() ) {
			
				$('body > #ui-datepicker-div').wrap('<div class="fieldmaster-ui-datepicker" />');
				
			}
		
		},
		
		
		/*
		*  init
		*
		*  This function will remove JS
		*
		*  @type	function
		*  @date	2/06/2016
		*  @since	5.3.8
		*
		*  @param	$input (jQuery selector)
		*  @return	n/a
		*/
		
		destroy: function( $input ){
			
			// do nothing
			
		}
		
	});
	
	
	fieldmaster.fields.date_time_picker = fieldmaster.field.extend({
		
		type: 'date_time_picker',
		$el: null,
		$input: null,
		$hidden: null,
		
		o: {},
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'blur input[type="text"]': 'blur'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-date-time-picker');
			this.$input = this.$el.find('input[type="text"]');
			this.$hidden = this.$el.find('input[type="hidden"]');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		initialize: function(){
			
			// create options
			var args = {
				dateFormat:			this.o.date_format,
				timeFormat:			this.o.time_format,
				altField:			this.$hidden,
				altFieldTimeOnly:	false,
				altFormat:			'yy-mm-dd',
				altTimeFormat:		'HH:mm:ss',
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.o.first_day,
				controlType: 		'select',
				oneLine:			true
			};
			
			
			// filter for 3rd party customization
			args = fieldmaster.apply_filters('date_time_picker_args', args, this.$field);
			
			
			// add date time picker
			fieldmaster.datetimepicker.init( this.$input, args );
			
			
			// action for 3rd party customization
			fieldmaster.do_action('date_time_picker_init', this.$input, args, this.$field);
			
		},
		
		blur: function(){
			
			if( !this.$input.val() ) {
			
				this.$hidden.val('');
				
			}
			
		}
		
	});	
	
})(jQuery);

(function($){
	
	fieldmaster.fields.file = fieldmaster.field.extend({
		
		type: 'file',
		$el: null,
		$input: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'click a[data-name="add"]': 	'add',
			'click a[data-name="edit"]': 	'edit',
			'click a[data-name="remove"]':	'remove',
			'change input[type="file"]':	'change'
		},
		
		
		/*
		*  focus
		*
		*  This function will setup variables when focused on a field
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-file-uploader');
			this.$input = this.$el.find('input[type="hidden"]');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		
		/*
		*  initialize
		*
		*  This function is used to setup basic upload form attributes
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		initialize: function(){
			
			// add attribute to form
			if( this.o.uploader == 'basic' ) {
				
				this.$el.closest('form').attr('enctype', 'multipart/form-data');
				
			}
				
		},
		
		
		/*
		*  prepare
		*
		*  This function will prepare an object of attachment data
		*  selecting a library image vs embed an image via url return different data
		*  this function will keep the 2 consistent
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	attachment (object)
		*  @return	data (object)
		*/
		
		prepare: function( attachment ) {
			
			// defaults
			attachment = attachment || {};
			
			
			// bail ealry if already valid
			if( attachment._valid ) return attachment;
			
			
			// vars
			var data = {
				url: '',
				alt: '',
				title: '',
				filename: '',
				filesizeHumanReadable: '',
				icon: '/wp-includes/images/media/default.png'
			};
			
			
			// wp image
			if( attachment.id ) {
				
				// update data
				data = attachment.attributes;
				
			}
			
	    	
	    	// valid
			data._valid = true;
			
			
	    	// return
	    	return data;
			
		},
		
		
		/*
		*  render
		*
		*  This function will render the UI
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	attachment (obj)
		*  @return	n/a
		*/
		
		render: function( data ){
			
			// prepare
			data = this.prepare(data);
			
			
			// update els
		 	this.$el.find('img').attr({
			 	src: data.icon,
			 	alt: data.alt,
			 	title: data.title
			});
			this.$el.find('[data-name="title"]').text( data.title );
		 	this.$el.find('[data-name="filename"]').text( data.filename ).attr( 'href', data.url );
		 	this.$el.find('[data-name="filesize"]').text( data.filesizeHumanReadable );
		 	
		 	
			// vars
			var val = '';
			
			
			// WP attachment
			if( data.id ) {
				
				val = data.id;
			
			}
			
			
			// update val
		 	fieldmaster.val( this.$input, val );
		 	
		 	
		 	// update class
		 	if( val ) {
			 	
			 	this.$el.addClass('has-value');
			 	
		 	} else {
			 	
			 	this.$el.removeClass('has-value');
			 	
		 	}
	
		},
		
		
		/*
		*  add
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		add: function() {
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// get repeater
			var $repeater = fieldmaster.get_closest_field( $field, 'repeater' );
			
			
			// popup
			var frame = fieldmaster.media.popup({
				
				title:		fieldmaster._e('file', 'select'),
				mode:		'select',
				type:		'',
				field:		$field.data('key'),
				multiple:	$repeater.exists(),
				library:	this.o.library,
				mime_types: this.o.mime_types,
				
				select: function( attachment, i ) {
					
					// select / add another image field?
			    	if( i > 0 ) {
			    		
						// vars
						var key = $field.data('key'),
							$tr = $field.closest('.fieldmaster-row');
						
						
						// reset field
						$field = false;
							
						
						// find next image field
						$tr.nextAll('.fieldmaster-row:visible').each(function(){
							
							// get next $field
							$field = fieldmaster.get_field( key, $(this) );
							
							
							// bail early if $next was not found
							if( !$field ) return;
							
							
							// bail early if next file uploader has value
							if( $field.find('.fieldmaster-file-uploader.has-value').exists() ) {
								
								$field = false;
								return;
								
							}
								
								
							// end loop if $next is found
							return false;
							
						});
						
						
						
						// add extra row if next is not found
						if( !$field ) {
							
							$tr = fieldmaster.fields.repeater.doFocus( $repeater ).add();
							
							
							// bail early if no $tr (maximum rows hit)
							if( !$tr ) return false;
							
							
							// get next $field
							$field = fieldmaster.get_field( key, $tr );
							
						}
						
					}
					
					
					// render
					self.set('$field', $field).render( attachment );
					
				}
			});
			
		},
		
		
		/*
		*  edit
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		edit: function() {
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// vars
			var val = this.$input.val();
			
			
			// bail early if no val
			if( !val ) return;
			
			
			// popup
			var frame = fieldmaster.media.popup({
			
				title:		fieldmaster._e('file', 'edit'),
				button:		fieldmaster._e('file', 'update'),
				mode:		'edit',
				attachment:	val,
				
				select:	function( attachment, i ) {
					
					// render
					self.set('$field', $field).render( attachment );
					
				}
				
			});
			
		},
		
		
		/*
		*  remove
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		remove: function() {
			
			// vars
	    	var attachment = {};
	    	
	    	
	    	// add file to field
	        this.render( attachment );
	        
		},
		
		
		/*
		*  get_file_info
		*
		*  This function will find basic file info and store it in a hidden input
		*
		*  @type	function
		*  @date	18/1/17
		*  @since	5.5.0
		*
		*  @param	$file_input (jQuery)
		*  @param	$hidden_input (jQuery)
		*  @return	n/a
		*/
		
		get_file_info: function( $file_input, $hidden_input ){
			
			// vars
			var val = $file_input.val(),
				attachment = {};
			
			
			// bail early if no value
			if( !val ) {
				
				$hidden_input.val('');
				return;
				
			}
			
			
			// url
			attachment.url = val;
			
			
			// modern browsers
			var files = $file_input[0].files;
			
			if( files.length ){
				
				// vars
				var file = files[0];
				
				
				// update
				attachment.size = file.size;
				attachment.type = file.type;
				
				
				// image
				if( file.type.indexOf('image') > -1 ) {
					
					// vars
					var _url = window.URL || window.webkitURL;
					
					
					// temp image
					var img = new Image();
					
					img.onload = function () {
						
						// update
						attachment.width = this.width;
						attachment.height = this.height;
						
						
						// set hidden input value
						$hidden_input.val( jQuery.param(attachment) );
						
					};
					
					img.src = _url.createObjectURL(file);
					
				}
				
			}
			
			
			// set hidden input value
			$hidden_input.val( jQuery.param(attachment) );
			
		},
		
		
		/*
		*  change
		*
		*  This function will update the hidden input when selecting a basic file to add basic validation
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		change: function( e ){
			
			this.get_file_info( e.$el, this.$input );
			
		}
		
	});
	

})(jQuery);

(function($){
	
	fieldmaster.fields.google_map = fieldmaster.field.extend({
		
		type: 'google_map',
		url: '',
		$el: null,
		$search: null,
		
		timeout: null,
		status : '', // '', 'loading', 'ready'
		geocoder : false,
		map : false,
		maps : {},
		$pending: $(),
		
		actions: {
			// have considered changing to 'load', however, could cause issues with devs expecting the API to exist earlier
			'ready':	'initialize',
			'append':	'initialize',
			'show':		'show'
		},
		
		events: {
			'click a[data-name="clear"]': 		'_clear',
			'click a[data-name="locate"]': 		'_locate',
			'click a[data-name="search"]': 		'_search',
			'keydown .search': 					'_keydown',
			'keyup .search': 					'_keyup',
			'focus .search': 					'_focus',
			'blur .search': 					'_blur',
			//'paste .search': 					'_paste',
			'mousedown .fieldmaster-google-map':		'_mousedown'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-google-map');
			this.$search = this.$el.find('.search');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			this.o.id = this.$el.attr('id');
			
			
			// get map
			if( this.maps[ this.o.id ] ) {
				
				this.map = this.maps[ this.o.id ];
				
			}
			
		},
		
		
		/*
		*  is_ready
		*
		*  This function will ensure google API is available and return a boolean for the current status
		*
		*  @type	function
		*  @date	19/11/2014
		*  @since	5.0.9
		*
		*  @param	n/a
		*  @return	(boolean)
		*/
		
		is_ready: function(){ 
			
			// reference
			var self = this;
			
			
			// ready
			if( this.status == 'ready' ) return true;
			
			
			// loading
			if( this.status == 'loading' ) return false;
			
			
			// check exists (optimal)
			if( fieldmaster.isset(window, 'google', 'maps', 'places') ) {
				
				this.status = 'ready';
				return true;
				
			}
			
			
			// check exists (ok)
			if( fieldmaster.isset(window, 'google', 'maps') ) {
				
				this.status = 'ready';
				
			}
			
			
			// attempt load google.maps.places
			if( this.url ) {
				
				// set status
				this.status = 'loading';
				
				
				// enqueue
				fieldmaster.enqueue_script(this.url, function(){
					
					// set status
			    	self.status = 'ready';
			    	
			    	
			    	// initialize pending
			    	self.initialize_pending();
			    	
				});
				
			}
			
			
			// ready
			if( this.status == 'ready' ) return true;
			
			
			// return
			return false;
			
		},
		
		
		/*
		*  initialize_pending
		*
		*  This function will initialize pending fields
		*
		*  @type	function
		*  @date	27/08/2016
		*  @since	5.4.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		initialize_pending: function(){
			
			// reference
			var self = this;
			
			this.$pending.each(function(){
				
				self.set('$field', $(this)).initialize();
				
			});
			
			
			// reset
			this.$pending = $();
			
		},
		
		
		/*
		*  actions
		*
		*  these functions are fired for this fields actions
		*
		*  @type	function
		*  @date	17/09/2015
		*  @since	5.2.3
		*
		*  @param	(mixed)
		*  @return	n/a
		*/
		
		initialize: function(){
			
			// add to pending
			if( !this.is_ready() ) {
				
				this.$pending = this.$pending.add( this.$field );
				
				return false;
				
			}
			
			
			// load geocode
			if( !this.geocoder ) {
				
				this.geocoder = new google.maps.Geocoder();
				
			}
			
			
			// reference
			var self = this,
				$field = this.$field,
				$el = this.$el,
				$search = this.$search;
			
			
			// input value may be cached by browser, so update the search input to match
			$search.val( this.$el.find('.input-address').val() );
			
			
			// map
			var map_args = fieldmaster.apply_filters('google_map_args', {
				
				scrollwheel:	false,
        		zoom:			parseInt(this.o.zoom),
        		center:			new google.maps.LatLng(this.o.lat, this.o.lng),
        		mapTypeId:		google.maps.MapTypeId.ROADMAP
        		
        	}, this.$field);
        	
			
			// create map	        	
        	this.map = new google.maps.Map( this.$el.find('.canvas')[0], map_args);
	        
	        
	        // search
	        if( fieldmaster.isset(window, 'google', 'maps', 'places', 'Autocomplete') ) {
		        
		        // vars
		        var autocomplete = new google.maps.places.Autocomplete( this.$search[0] );
				
				
				// bind
				autocomplete.bindTo('bounds', this.map);
				
				
				// event
				google.maps.event.addListener(autocomplete, 'place_changed', function( e ) {
				    
				    // vars
				    var place = this.getPlace();
				    
				    
				    // search
					self.search( place );
				    
				});
				
				
				// append
				this.map.autocomplete = autocomplete;
				
	        }
			
			
			// marker
			var marker_args = fieldmaster.apply_filters('google_map_marker_args', {
				
		        draggable: 		true,
		        raiseOnDrag: 	true,
		        map: 			this.map
		        
		    }, this.$field);
		    
		    
		    // add marker
	        this.map.marker = new google.maps.Marker( marker_args );
		    
		    
		    // add references
		    this.map.$el = $el;
		    this.map.$field = $field;
		    
		    
		    // value exists?
		    var lat = $el.find('.input-lat').val(),
		    	lng = $el.find('.input-lng').val();
		    
		    if( lat && lng ) {
			    
			    this.update(lat, lng).center();
			    
		    }
		    
		    
			// events
		    google.maps.event.addListener( this.map.marker, 'dragend', function(){
		    	
		    	// vars
				var position = this.map.marker.getPosition(),
					lat = position.lat(),
			    	lng = position.lng();
			    	
				self.update( lat, lng ).sync();
			    
			});
			
			
			google.maps.event.addListener( this.map, 'click', function( e ) {
				
				// vars
				var lat = e.latLng.lat(),
					lng = e.latLng.lng();
				
				
				self.update( lat, lng ).sync();
			
			});
			
			
			// action for 3rd party customization
			fieldmaster.do_action('google_map_init', this.map, this.map.marker, this.$field);
			
			
	        // add to maps
	        this.maps[ this.o.id ] = this.map;
	        
		},
		
		search: function( place ){
			
			// reference
			var self = this;
			
			
			// vars
		    var address = this.$search.val();
		    
		    
		    // bail ealry if no address
		    if( !address ) {
			    
			    return false;
			    
		    }
		    
		    
		    // update input
			this.$el.find('.input-address').val( address );
		    
		    
		    // is lat lng?
		    var latLng = address.split(',');
		    
		    if( latLng.length == 2 ) {
			    
			    var lat = latLng[0],
					lng = latLng[1];
			    
			   
			    if( $.isNumeric(lat) && $.isNumeric(lng) ) {
				    
				    // parse
				    lat = parseFloat(lat);
				    lng = parseFloat(lng);
				    
				    self.update( lat, lng ).center();
				    
				    return;
				    
			    }
			    
		    }
		    
		    
		    // if place exists
		    if( place && place.geometry ) {
			    
		    	var lat = place.geometry.location.lat(),
					lng = place.geometry.location.lng();
					
				
				// update
				self.update( lat, lng ).center();
			    
			    
			    // bail early
			    return;
			    
		    }
		    
		    
		    // add class
		    this.$el.addClass('-loading');
		    
		    self.geocoder.geocode({ 'address' : address }, function( results, status ){
		    	
		    	// remove class
		    	self.$el.removeClass('-loading');
		    	
		    	
		    	// validate
				if( status != google.maps.GeocoderStatus.OK ) {
					
					console.log('Geocoder failed due to: ' + status);
					return;
					
				} else if( !results[0] ) {
					
					console.log('No results found');
					return;
					
				}
				
				
				// get place
				place = results[0];
				
				var lat = place.geometry.location.lat(),
					lng = place.geometry.location.lng();
					
				
				self.update( lat, lng ).center();
			    
			});
				
		},
		
		update: function( lat, lng ){
			
			// vars
			var latlng = new google.maps.LatLng( lat, lng );
		    
		    
		    // update inputs
		    fieldmaster.val( this.$el.find('.input-lat'), lat );
		    fieldmaster.val( this.$el.find('.input-lng'), lng );
		    
			
		    // update marker
		    this.map.marker.setPosition( latlng );
		    
		    
			// show marker
			this.map.marker.setVisible( true );
		    
		    
		    // update class
		    this.$el.addClass('-value');
		    
		    
	        // validation
			this.$field.removeClass('error');
			
			
			// action
			fieldmaster.do_action('google_map_change', latlng, this.map, this.$field);
			
			
			// blur input
			this.$search.blur();
			
			
	        // return for chaining
	        return this;
	        
		},
		
		center: function(){
			
			// vars
			var position = this.map.marker.getPosition(),
				lat = this.o.lat,
				lng = this.o.lng;
			
			
			// if marker exists, center on the marker
			if( position ) {
				
				lat = position.lat();
				lng = position.lng();
				
			}
			
			
			var latlng = new google.maps.LatLng( lat, lng );
				
			
			// set center of map
	        this.map.setCenter( latlng );
	        
		},
		
		sync: function(){
			
			// reference
			var self = this;
				
			
			// vars
			var position = this.map.marker.getPosition(),
				latlng = new google.maps.LatLng( position.lat(), position.lng() );
			
			
			// add class
		    this.$el.addClass('-loading');
		    
		    
		    // load
			this.geocoder.geocode({ 'latLng' : latlng }, function( results, status ){
				
				// remove class
				self.$el.removeClass('-loading');
			    	
			    	
				// validate
				if( status != google.maps.GeocoderStatus.OK ) {
					
					console.log('Geocoder failed due to: ' + status);
					return;
					
				} else if( !results[0] ) {
					
					console.log('No results found');
					return;
					
				}
				
				
				// get location
				var location = results[0];
				
				
				// update title
				self.$search.val( location.formatted_address );

				
				// update input
				fieldmaster.val( self.$el.find('.input-address'), location.formatted_address );
		    
			});
			
			
			// return for chaining
	        return this;
	        
		},
		
		refresh: function(){
			
			// bail early if not ready
			if( !this.is_ready() ) {
				
				return false;
			
			}
			
			
			// trigger resize on map 
			google.maps.event.trigger(this.map, 'resize');
			
			
			
			// center map
			this.center();
			
		},
		
		show: function(){
			
			// vars
			var self = this,
				$field = this.$field;
			
			
			// center map when it is shown (by a tab / collapsed row)
			// - use delay to avoid rendering issues with browsers (ensures div is visible)
			setTimeout(function(){
				
				self.set('$field', $field).refresh();
				
			}, 10);
			
		},
		
		
		/*
		*  events
		*
		*  these functions are fired for this fields events
		*
		*  @type	function
		*  @date	17/09/2015
		*  @since	5.2.3
		*
		*  @param	e
		*  @return	n/a
		*/
		
		_clear: function( e ){ // console.log('_clear');
			
			// remove Class
			this.$el.removeClass('-value -loading -search');
		    
			
			// clear search
			this.$search.val('');
			
			
			// clear inputs
			fieldmaster.val( this.$el.find('.input-address'), '' );
			fieldmaster.val( this.$el.find('.input-lat'), '' );
			fieldmaster.val( this.$el.find('.input-lng'), '' );
						
			
			// hide marker
			this.map.marker.setVisible( false );
			
		},
		
		_locate: function( e ){ // console.log('_locate');
			
			// reference
			var self = this;
			
			
			// Try HTML5 geolocation
			if( !navigator.geolocation ) {
				
				alert( fieldmaster._e('google_map', 'browser_support') );
				return this;
				
			}
			
			
			// add class
		    this.$el.addClass('-loading');
		    
		    
		    // load
		    navigator.geolocation.getCurrentPosition(function(position){
		    	
		    	// remove class
				self.$el.removeClass('-loading');
		    
		    
		    	// vars
				var lat = position.coords.latitude,
			    	lng = position.coords.longitude;
			    	
				self.update( lat, lng ).sync().center();
				
			});
			
		},
		
		_search: function( e ){ // console.log('_search');
			
			this.search();
			
		},
		
		_focus: function( e ){ // console.log('_focus');
			
			// remove class
			this.$el.removeClass('-value');
			
			
			// toggle -search class
			this._keyup();
			
		},
		
		_blur: function( e ){ // console.log('_blur');
			
			// reference
			var self = this;
			
			
			// vars
			var val = this.$el.find('.input-address').val();
			
			
			// bail early if no val
			if( !val ) {
				
				return;
				
			}
			
			
			// revert search to hidden input value
			this.timeout = setTimeout(function(){
				
				self.$el.addClass('-value');
				self.$search.val( val );
				
			}, 100);
			
		},
		
/*
		_paste: function( e ){ console.log('_paste');
			
			// reference
			var $search = this.$search;
			
			
			// blur search
			$search.blur();
			
			
			// clear timeout
			this._mousedown(e);
			
			
			// focus on input
			setTimeout(function(){
				
				$search.focus();
				
			}, 1);
		},
*/
		
		_keydown: function( e ){ // console.log('_keydown');
			
			// prevent form from submitting
			if( e.which == 13 ) {
				
				e.preventDefault();
			    
			}
			
		},
		
		_keyup: function( e ){ // console.log('_keyup');
			
			// vars
			var val = this.$search.val();
			
			
			// toggle class
			if( val ) {
				
				this.$el.addClass('-search');
				
			} else {
				
				this.$el.removeClass('-search');
				
			}
			
		},
		
		_mousedown: function( e ){ // console.log('_mousedown');
			
			// reference
			var self = this;
			
			
			// clear timeout in 1ms (_mousedown will run before _blur)
			setTimeout(function(){
				
				clearTimeout( self.timeout );
				
			}, 1);
			
			
		}
		
	});

	
})(jQuery);

(function($){
	
	fieldmaster.fields.image = fieldmaster.field.extend({
		
		type: 'image',
		$el: null,
		$input: null,
		$img: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'click a[data-name="add"]': 	'add',
			'click a[data-name="edit"]': 	'edit',
			'click a[data-name="remove"]':	'remove',
			'change input[type="file"]':	'change'
		},
		
		
		/*
		*  focus
		*
		*  This function will setup variables when focused on a field
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		focus: function(){
			
			// vars
			this.$el = this.$field.find('.fieldmaster-image-uploader');
			this.$input = this.$el.find('input[type="hidden"]');
			this.$img = this.$el.find('img');
			
			
			// options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		
		/*
		*  initialize
		*
		*  This function is used to setup basic upload form attributes
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		initialize: function(){
			
			// add attribute to form
			if( this.o.uploader == 'basic' ) {
				
				this.$el.closest('form').attr('enctype', 'multipart/form-data');
				
			}
				
		},
		
		
		/*
		*  prepare
		*
		*  This function will prepare an object of attachment data
		*  selecting a library image vs embed an image via url return different data
		*  this function will keep the 2 consistent
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	attachment (object)
		*  @return	data (object)
		*/
		
		prepare: function( attachment ) {
			
			// defaults
			attachment = attachment || {};
			
			
			// bail ealry if already valid
			if( attachment._valid ) return attachment;
			
			
			// vars
			var data = {
				url: '',
				alt: '',
				title: '',
				caption: '',
				description: '',
				width: 0,
				height: 0
			};
			
			
			// wp image
			if( attachment.id ) {
				
				// update data
				data = attachment.attributes;
				
				
				// maybe get preview size
				data.url = fieldmaster.maybe_get(data, 'sizes.'+this.o.preview_size+'.url', data.url);
				
			} 
			
	    	
	    	// valid
			data._valid = true;
			
			
	    	// return
	    	return data;
			
		},
		
		
		/*
		*  render
		*
		*  This function will render the UI
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	attachment (obj)
		*  @return	n/a
		*/
		
		render: function( data ){
			
			// prepare
			data = this.prepare(data);
			
			
			// update image
		 	this.$img.attr({
			 	src: data.url,
			 	alt: data.alt,
			 	title: data.title
		 	});
		 	
		 	
			// vars
			var val = '';
			
			
			// WP attachment
			if( data.id ) {
				
				val = data.id;
				
			}
			
			
			// update val
		 	fieldmaster.val( this.$input, val );
		 	
		 	
		 	// update class
		 	if( val ) {
			 	
			 	this.$el.addClass('has-value');
			 	
		 	} else {
			 	
			 	this.$el.removeClass('has-value');
			 	
		 	}
	
		},
		
		
		/*
		*  add
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		add: function() {
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// get repeater
			var $repeater = fieldmaster.get_closest_field( this.$field, 'repeater' );
			
			
			// popup
			var frame = fieldmaster.media.popup({
				
				title:		fieldmaster._e('image', 'select'),
				mode:		'select',
				type:		'image',
				field:		$field.data('key'),
				multiple:	$repeater.exists(),
				library:	this.o.library,
				mime_types: this.o.mime_types,
				
				select: function( attachment, i ) {
					
					// select / add another image field?
			    	if( i > 0 ) {
			    		
			    		// vars
						var key = $field.data('key'),
							$tr = $field.closest('.fieldmaster-row');
						
						
						// reset field
						$field = false;
						
						
						// find next image field
						$tr.nextAll('.fieldmaster-row:visible').each(function(){
							
							// get next $field
							$field = fieldmaster.get_field( key, $(this) );
							
							
							// bail early if $next was not found
							if( !$field ) return;
							
							
							// bail early if next file uploader has value
							if( $field.find('.fieldmaster-image-uploader.has-value').exists() ) {
								
								$field = false;
								return;
								
							}
								
								
							// end loop if $next is found
							return false;
							
						});
						
						
						// add extra row if next is not found
						if( !$field ) {
							
							$tr = fieldmaster.fields.repeater.doFocus( $repeater ).add();
							
							
							// bail early if no $tr (maximum rows hit)
							if( !$tr ) return false;
							
							
							// get next $field
							$field = fieldmaster.get_field( key, $tr );
							
						}
						
					}
					
					
					// render
					self.set('$field', $field).render( attachment );
					
				}
				
			});
						
		},
		
		
		/*
		*  edit
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		edit: function() {
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// vars
			var val = this.$input.val();
			
			
			// bail early if no val
			if( !val ) return;
				
			
			// popup
			var frame = fieldmaster.media.popup({
			
				title:		fieldmaster._e('image', 'edit'),
				button:		fieldmaster._e('image', 'update'),
				mode:		'edit',
				attachment:	val,
				
				select:	function( attachment, i ) {
					
					// render
					self.set('$field', $field).render( attachment );
					
				}
				
			});
			
		},
		
		
		/*
		*  remove
		*
		*  event listener
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		remove: function() {
			
			// vars
	    	var attachment = {};
	    	
	    	
	    	// add file to field
	        this.render( attachment );
	        
		},
		
		
		/*
		*  change
		*
		*  This function will update the hidden input when selecting a basic file to add basic validation
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	e (event)
		*  @return	n/a
		*/
		
		change: function( e ){
			
			fieldmaster.fields.file.get_file_info( e.$el, this.$input );
			
		}
		
	});

})(jQuery);

(function($){
	
	fieldmaster.media = fieldmaster.model.extend({
		
		frames: [],
		mime_types: {},
		
		actions: {
			'ready': 'ready'
		},
		
		
		/*
		*  frame
		*
		*  This function will return the current frame
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.2
		*
		*  @param	n/a
		*  @return	frame (object)
		*/
		
		frame: function(){
			
			// vars
			var i = this.frames.length - 1;
			
			
			// bail early if no index
			if( i < 0 ) return false;
			
			
			// return
			return this.frames[ i ];
				
		},
		
		
		/*
		*  destroy
		*
		*  this function will destroy a frame
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @return	frame (object)
		*  @return	n/a
		*/
		
		destroy: function( frame ) {
			
			// detach
			frame.detach();
			frame.dispose();
					
			
			// remove frame
			frame = null;
			this.frames.pop();
			
		},
		
		
		/*
		*  popup
		*
		*  This function will create a wp media popup frame
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @param	args (object)
		*  @return	frame (object)
		*/
		
		popup: function( args ) {
			
			// vars
			var post_id = fieldmaster.get('post_id'),
				frame = false;
			
			
			// validate post_id
			if( !$.isNumeric(post_id) ) post_id = 0;
			
			
			// settings
			var settings = fieldmaster.parse_args( args, {
				mode:		'select',			// 'select', 'edit'
				title:		'',					// 'Upload Image'
				button:		'',					// 'Select Image'
				type:		'',					// 'image', ''
				field:		'',					// 'field_123'
				mime_types:	'',					// 'pdf, etc'
				library:	'all',				// 'all', 'uploadedTo'
				multiple:	false,				// false, true, 'add'
				attachment:	0,					// the attachment to edit
				post_id:	post_id,			// the post being edited
				select: 	function(){}
			});
			
			
			// id changed to attributes
			if( settings.id ) settings.attachment = settings.id;
			
			
			// create frame
			var frame = this.new_media_frame( settings );
			
			
			// append
			this.frames.push( frame );
			
			
			// open popup (allow frame customization before opening)
			setTimeout(function(){
				
				frame.open();
				
			}, 1);
			
			
			// return
			return frame;
				
		},
		
		
		/*
		*  _get_media_frame_settings
		*
		*  This function will return an object containing frame settings
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @param	frame (object)
		*  @param	settings (object)
		*  @return	frame (object)
		*/
		
		_get_media_frame_settings: function( frame, settings ){
			
			// select
			if( settings.mode === 'select' ) {
					
				frame = this._get_select_frame_settings( frame, settings );
			
			// edit	
			} else if( settings.mode === 'edit' ) {
				
				frame = this._get_edit_frame_settings( frame, settings );
				
			}
			
			
			// return
			return frame;
			
		},
		
		_get_select_frame_settings: function( frame, settings ){
			
			// type
			if( settings.type ) {
				
				frame.library.type = settings.type;
				
			}
			
			
			// library
			if( settings.library === 'uploadedTo' ) {
			
				frame.library.uploadedTo = settings.post_id;
			
			}
			
			
			// button
			frame._button = fieldmaster._e('media', 'select');
			
			
			// return
			return frame;
			
		},
		
		_get_edit_frame_settings: function( frame, settings ){

			// post__in
			frame.library.post__in = [ settings.attachment ];
			
			
			// button
			frame._button = fieldmaster._e('media', 'update');
			
			
			// return 
			return frame;
			
		},
		
		
		/*
		*  _add_media_frame_events
		*
		*  This function will add events to the frame object
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_add_media_frame_events: function( frame, settings ){
			
			// log events
/*
			frame.on('all', function( e ) {
				
				console.log( 'frame all: %o', e );
			
			});
*/
			
			
			// add class
			frame.on('open',function() {
				
				// add class
				this.$el.closest('.media-modal').addClass('fieldmaster-media-modal -' +settings.mode );
					
			}, frame);
			
						
			// edit image view
			// source: media-views.js:2410 editImageContent()
			frame.on('content:render:edit-image', function(){
				
				var image = this.state().get('image'),
					view = new wp.media.view.EditImage( { model: image, controller: this } ).render();
	
				this.content.set( view );
	
				// after creating the wrapper view, load the actual editor via an ajax call
				view.loadEditor();
				
			}, frame);
			
			
			// update toolbar button
			frame.on( 'toolbar:create:select', function( toolbar ) {
				
				toolbar.view = new wp.media.view.Toolbar.Select({
					text: frame.options._button,
					controller: this
				});
				
			}, frame );
			
			
			// select image
			frame.on('select', function() {
				
				// get selected images
				var state = frame.state(),
					image = state.get('image'),
					selection = state.get('selection');
				
				
				// if editing image
				if( image ) {
					
					settings.select.apply( frame, [image, 0] );
					
					return;
					
				}
				
				
				// if selecting images
				if( selection ) {
					
					// vars
					var i = 0;
				
					
					// loop
					selection.each(function( attachment ){
						
						settings.select.apply( frame, [attachment, i] );
						
						i++;
						
					});
					
					return;
					
				}
				
			});
			
			
			// close popup
			frame.on('close',function(){
			
				setTimeout(function(){
					
					fieldmaster.media.destroy( frame );
					
				}, 500);
				
			});
			
			
			// select
			if( settings.mode === 'select' ) {
					
				frame = this._add_select_frame_events( frame, settings );
			
			// edit	
			} else if( settings.mode === 'edit' ) {
				
				frame = this._add_edit_frame_events( frame, settings );
				
			}
			
			
			// return
			return frame;
			
		},
		
		_add_select_frame_events: function( frame, settings ){
			
			// reference
			var self = this;
			
			
			// plupload
			// adds _fieldmasteruploader param to validate uploads
			if( fieldmaster.isset(_wpPluploadSettings, 'defaults', 'multipart_params') ) {
				
				// add _fieldmasteruploader so that Uploader will inherit
				_wpPluploadSettings.defaults.multipart_params._fieldmasteruploader = settings.field;
				
				
				// remove fieldmaster_field so future Uploaders won't inherit
				frame.on('open', function(){
					
					delete _wpPluploadSettings.defaults.multipart_params._fieldmasteruploader;
					
				});
				
			}
			
			
			// modify DOM
			frame.on('content:activate:browse', function(){
				
				// populate above vars making sure to allow for failure
				try {
					
					var toolbar = frame.content.get().toolbar,
						filters = toolbar.get('filters'),
						search = toolbar.get('search');
				
				} catch(e) {
				
					// one of the objects was 'undefined'... perhaps the frame open is Upload Files
					// console.log( 'error %o', e );
					return;
					
				}
				
				
				// image
				if( settings.type == 'image' ) {
					
					// update all
					filters.filters.all.text = fieldmaster._e('image', 'all');
					
					
					// remove some filters
					delete filters.filters.audio;
					delete filters.filters.video;
					
					
					// update all filters to show images
					$.each( filters.filters, function( k, filter ){
						
						if( filter.props.type === null ) {
							
							filter.props.type = 'image';
							
						}
						
					});
					
				}
				
				
				// custom mime types
				if( settings.mime_types ) {
					
					// explode
					var extra_types = settings.mime_types.split(' ').join('').split('.').join('').split(',');
					
					
					// loop through mime_types
					$.each( extra_types, function( i, type ){
						
						// find mime
						$.each( self.mime_types, function( t, mime ){
							
							// continue if key does not match
							if( t.indexOf(type) === -1 ) {
								
								return;
								
							}
							
							
							// create new filter
							var filter = {
								text: type,
								props: {
									status:  null,
									type:    mime,
									uploadedTo: null,
									orderby: 'date',
									order:   'DESC'
								},
								priority: 20
							};			
											
							
							// append filter
							filters.filters[ mime ] = filter;
														
						});
						
					});
					
				}
				
				
				// uploaded to post
				if( settings.library == 'uploadedTo' ) {
					
					// remove some filters
					delete filters.filters.unattached;
					delete filters.filters.uploaded;
					
					
					// add 'uploadedTo' text
					filters.$el.parent().append('<span class="fieldmaster-uploadedTo">' + fieldmaster._e('image', 'uploadedTo') + '</span>');
					
					
					// add uploadedTo to filters
					$.each( filters.filters, function( k, filter ){
						
						filter.props.uploadedTo = settings.post_id;
						
					});
					
				}
				
				
				// add _fieldmasteruploader to filters
				$.each( filters.filters, function( k, filter ){
					
					filter.props._fieldmasteruploader = settings.field;
					
				});
				
				
				// add _fieldmasteruplaoder to search
				search.model.attributes._fieldmasteruploader = settings.field;
				
				
				// render
				if( typeof filters.refresh === 'function' ) {
					
					filters.refresh();
				
				}
				
			});
			
			
			// return
			return frame;
			
		},
		
		_add_edit_frame_events: function( frame, settings ){
			
			// add class
			frame.on('open',function() {
				
				// add class
				this.$el.closest('.media-modal').addClass('fieldmaster-expanded');
				
				
				// set to browse
				if( this.content.mode() != 'browse' ) {
				
					this.content.mode('browse');
					
				}
				
				
				// set selection
				var state 		= this.state(),
					selection	= state.get('selection'),
					attachment	= wp.media.attachment( settings.attachment );
				
				
				selection.add( attachment );
								
			}, frame);

			
			// return 
			return frame;
			
		},
		
		
		/*
		*  new_media_frame
		*
		*  this function will create a new media frame
		*
		*  @type	function
		*  @date	11/04/2016
		*  @since	5.3.8
		*
		*  @param	settings (object)
		*  @return	frame (object)
		*/
		
		new_media_frame: function( settings ){
			
			// vars
			var attributes = {
				title: settings.title,
				multiple: settings.multiple,
				library: {},
				states:	[]
			};
			
			
			// get options
			attributes = this._get_media_frame_settings( attributes, settings );
						
		
			// create query
			var Query = wp.media.query( attributes.library );
			
			
			// add _fieldmasteruploader
			// this is super wack!
			// if you add _fieldmasteruploader to the options.library args, new uploads will not be added to the library view.
			// this has been traced back to the wp.media.model.Query initialize function (which can't be overriden)
			// Adding any custom args will cause the Attahcments to not observe the uploader queue
			// To bypass this security issue, we add in the args AFTER the Query has been initialized
			// options.library._fieldmasteruploader = settings.field;
			if( fieldmaster.isset(Query, 'mirroring', 'args') ) {
				
				Query.mirroring.args._fieldmasteruploader = settings.field;
				
			}
			
			
			// add states
			attributes.states = [
				
				// main state
				new wp.media.controller.Library({
					library:		Query,
					multiple: 		attributes.multiple,
					title: 			attributes.title,
					priority: 		20,
					filterable: 	'all',
					editable: 		true,

					// If the user isn't allowed to edit fields,
					// can they still edit it locally?
					allowLocalEdits: true
				})
				
			];
			
			
			// edit image functionality (added in WP 3.9)
			if( fieldmaster.isset(wp, 'media', 'controller', 'EditImage') ) {
				
				attributes.states.push( new wp.media.controller.EditImage() );
				
			}
			
			
			// create frame
			var frame = wp.media( attributes );
			
			
			// add args reference
			frame.fieldmaster = settings;
			
			
			// add events
			frame = this._add_media_frame_events( frame, settings );
			
			
			// return
			return frame;
			
		},
		
		ready: function(){
			
			// vars
			var version = fieldmaster.get('wp_version'),
				browser = fieldmaster.get('browser'),
				post_id = fieldmaster.get('post_id');
			
			
			// update wp.media
			if( fieldmaster.isset(window,'wp','media','view','settings','post') && $.isNumeric(post_id) ) {
				
				wp.media.view.settings.post.id = post_id;
					
			}
			
			
			// append browser
			if( browser ) {
				
				$('body').addClass('browser-' + browser );
				
			}
			
			
			// append version
			if( version ) {
				
				// ensure is string
				version = version + '';
				
				
				// use only major version
				major = version.substr(0,1);
				
				
				// add body class
				$('body').addClass('major-' + major);
				
			}
			
			
			// customize wp.media views
			if( fieldmaster.isset(window, 'wp', 'media', 'view') ) {
				
				//this.customize_Attachments();
				//this.customize_Query();
				//this.add_AcfEmbed();
				this.customize_Attachment();
				this.customize_AttachmentFiltersAll();
				this.customize_AttachmentCompat();
			
			}
			
		},
		
		
/*
		add_AcfEmbed: function(){
			
			//test urls
			//(image) jpg: 	http://www.ml24.net/img/ml24_design_process_scion_frs_3d_rendering.jpg
			//(image) svg: 	http://kompozer.net/images/svg/Mozilla_Firefox.svg
			//(file) pdf: 	http://martinfowler.com/ieeeSoftware/whenType.pdf
			//(video) mp4:	https://videos.files.wordpress.com/kUJmAcSf/bbb_sunflower_1080p_30fps_normal_hd.mp4
				
			
			
			// add view
			wp.media.view.AcfEmbed = wp.media.view.Embed.extend({
				
				initialize: function() {
				
					// set attachments
					this.model.props.attributes = this.controller.fieldmaster.attachment || {};
						
					
					// refresh
					wp.media.view.Embed.prototype.initialize.apply( this, arguments );
					
				},
				
				refresh: function() {
					
					// vars
					var attachment = fieldmaster.parse_args(this.model.props.attributes, {
						url: '',
						filename: '',
						title: '',
						caption: '',
						alt: '',
						description: '',
						type: '',
						ext: ''
					});
					
					
					// update attachment
					if( attachment.url ) {
						
						// filename
						attachment.filename = attachment.url.split('/').pop().split('?')[0];
						
						
						// update
						attachment.ext = attachment.filename.split('.').pop();
						attachment.type = /(jpe?g|png|gif|svg)/i.test(attachment.ext) ? 'image': 'file';
						
					}
					
					
					// auto generate title
					if( attachment.filename && !attachment.title ) {
						
						// replace
						attachment.title = attachment.filename.split('-').join(' ').split('_').join(' ');
						
						
						// uppercase first word
						attachment.title = attachment.title.charAt(0).toUpperCase() + attachment.title.slice(1);
						
						
						// remove extension
						attachment.title = attachment.title.replace('.'+attachment.ext, '');
						
						
						// update model
						this.model.props.attributes.title = attachment.title;
						
					}
					
					
					// save somee extra data
					this.model.props.attributes.filename = attachment.filename;
					this.model.props.attributes.type = attachment.type;
					
						
					// always show image view
					// avoid this.model.set() to prevent listeners updating view
					this.model.attributes.type = 'image';
					
					
					// refresh
					wp.media.view.Embed.prototype.refresh.apply( this, arguments );

					
					// append title
					this.$el.find('.setting.caption').before([
						'<label class="setting title">',
							'<span>Title</span>',
							'<input type="text" data-setting="title" value="' + attachment.title + '">',
						'</label>'
					].join(''));
					
					
					// append description
					this.$el.find('.setting.alt-text').after([
						'<label class="setting description">',
							'<span>Description</span>',
							'<textarea type="text" data-setting="description">' + attachment.description + '</textarea>',
						'</label>'
					].join(''));
					
					
					// hide alt
					if( attachment.type !== 'image' ) {
						
						this.$el.find('.setting.alt-text').hide();
						
					}
					
				}
				
			});	
			
		},
*/
/*
		
		customize_Attachments: function(){
			
			// vars
			var Attachments = wp.media.model.Attachments;
			
			
			wp.media.model.Attachments = Attachments.extend({
				
				initialize: function( models, options ){
					
					// console.log('My Attachments initialize: %o %o %o', this, models, options);
					
					// return
					return Attachments.prototype.initialize.apply( this, arguments );
					
				},
				
				sync: function( method, model, options ) {
					
					// console.log('My Attachments sync: %o %o %o %o', this, method, model, options);
					
					
					// return
					return Attachments.prototype.sync.apply( this, arguments );
					
				}
				
			});
			
		},
		
		customize_Query: function(){
			
			// console.log('customize Query!');
			
			// vars
			var Query = wp.media.model.Query;
			
			
			wp.media.model.Query = {};
			
		},
*/
		
		customize_Attachment: function(){
			
			// vars
			var AttachmentLibrary = wp.media.view.Attachment.Library;
			
			
			// extend
			wp.media.view.Attachment.Library = AttachmentLibrary.extend({
				
				render: function() {
					
					// vars
					var frame = fieldmaster.media.frame(),
						errors = fieldmaster.maybe_get(this, 'model.attributes.fieldmaster_errors');
					
					
					// add class
					// also make sure frame exists to prevent this logic running on a WP popup (such as feature image)
					if( frame && errors ) {
						
						this.$el.addClass('fieldmaster-disabled');
						
					}
					
					
					// return
					return AttachmentLibrary.prototype.render.apply( this, arguments );
					
				},
				
				
				/*
				*  toggleSelection
				*
				*  This function is called before an attachment is selected
				*  A good place to check for errors and prevent the 'select' function from being fired
				*
				*  @type	function
				*  @date	29/09/2016
				*  @since	5.4.0
				*
				*  @param	options (object)
				*  @return	n/a
				*/
				
				toggleSelection: function( options ) {
					
					// vars
					// source: wp-includes/js/media-views.js:2880
					var collection = this.collection,
						selection = this.options.selection,
						model = this.model,
						single = selection.single();
					
					
					// vars
					var frame = fieldmaster.media.frame(),
						errors = fieldmaster.maybe_get(this, 'model.attributes.fieldmaster_errors'),
						$sidebar = this.controller.$el.find('.media-frame-content .media-sidebar');
					
					
					// remove previous error
					$sidebar.children('.fieldmaster-selection-error').remove();
					
					
					// show attachment details
					$sidebar.children().removeClass('fieldmaster-hidden');
					
					
					// add message
					if( frame && errors ) {
						
						// vars
						var filename = fieldmaster.maybe_get(this, 'model.attributes.filename', '');
						
						
						// hide attachment details
						// Gallery field continues to show previously selected attachment...
						$sidebar.children().addClass('fieldmaster-hidden');
						
						
						// append message
						$sidebar.prepend([
							'<div class="fieldmaster-selection-error">',
								'<span class="selection-error-label">' + fieldmaster._e('restricted') +'</span>',
								'<span class="selection-error-filename">' + filename + '</span>',
								'<span class="selection-error-message">' + errors + '</span>',
							'</div>'
						].join(''));
						
						
						// reset selection (unselects all attachments)
						selection.reset();
						
						
						// set single (attachment displayed in sidebar)
						selection.single( model );
						
						
						// return and prevent 'select' form being fired
						return;
						
					}
									
					
					// return					
					AttachmentLibrary.prototype.toggleSelection.apply( this, arguments );
					
				}
				
			});
			
		},
		
		customize_AttachmentFiltersAll: function(){
			
			// add function refresh
			wp.media.view.AttachmentFilters.All.prototype.refresh = function(){
				
				// Build `<option>` elements.
				this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
					return {
						el: $( '<option></option>' ).val( value ).html( filter.text )[0],
						priority: filter.priority || 50
					};
				}, this ).sortBy('priority').pluck('el').value() );
				
			};
			
		},
		
		customize_AttachmentCompat: function(){
			
			// vars
			var AttachmentCompat = wp.media.view.AttachmentCompat;
			
			
			// extend
			wp.media.view.AttachmentCompat = AttachmentCompat.extend({
				
				add_fieldmaster_expand_button: function(){
					
					// vars
					var $el = this.$el.closest('.media-modal');
					
					
					// does button already exist?
					if( $el.find('.media-frame-router .fieldmaster-expand-details').exists() ) return;
					
					
					// create button
					var $a = $([
						'<a href="#" class="fieldmaster-expand-details">',
							'<span class="is-closed"><span class="fieldmaster-icon -left small grey"></span>' + fieldmaster._e('expand_details') +  '</span>',
							'<span class="is-open"><span class="fieldmaster-icon -right small grey"></span>' + fieldmaster._e('collapse_details') +  '</span>',
						'</a>'
					].join('')); 
					
					
					// add events
					$a.on('click', function( e ){
						
						e.preventDefault();
						
						if( $el.hasClass('fieldmaster-expanded') ) {
						
							$el.removeClass('fieldmaster-expanded');
							
						} else {
							
							$el.addClass('fieldmaster-expanded');
							
						}
						
					});
					
					
					// append
					$el.find('.media-frame-router').append( $a );
					
				},
				
				render: function() {
					
					// validate
					if( this.ignore_render ) return this;
					
					
					// reference
					var self = this;
					
					
					// add expand button
					setTimeout(function(){
						
						self.add_fieldmaster_expand_button();
						
					}, 0);
					
					
					// setup fields
					// The clearTimout is needed to prevent many setup functions from running at the same time
					clearTimeout( fieldmaster.media.render_timout );
					fieldmaster.media.render_timout = setTimeout(function(){
						
						fieldmaster.do_action('append', self.$el);
						
					}, 50);
					
					
					// return
					return AttachmentCompat.prototype.render.apply( this, arguments );
					
				},
				
				
				dispose: function() {
					
					// remove
					fieldmaster.do_action('remove', this.$el);
					
					
					// return
					return AttachmentCompat.prototype.dispose.apply( this, arguments );
					
				},
				
				
				save: function( e ) {
				
					if( e ) {
						
						e.preventDefault();
						
					}
					
					
					// serialize form
					var data = fieldmaster.serialize_form(this.$el);
					
					
					// ignore render
					this.ignore_render = true;
					
					
					// save
					this.model.saveCompat( data );
					
				}
				
			
			});
			
		}
		
		
	});

})(jQuery);

(function($){
	
	fieldmaster.fields.oembed = fieldmaster.field.extend({
		
		type: 'oembed',
		$el: null,
		
		events: {
			'click [data-name="search-button"]': 	'_search',
			'click [data-name="clear-button"]': 	'_clear',
			'click [data-name="value-title"]':		'_edit',
			'keypress [data-name="search-input"]':	'_keypress',
			'keyup [data-name="search-input"]':		'_keyup',
			'blur [data-name="search-input"]':		'_blur'
		},
		
		
		/*
		*  focus
		*
		*  This function will setup variables when focused on a field
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		focus: function(){
			
			// vars
			this.$el = this.$field.find('.fieldmaster-oembed');
			this.$search = this.$el.find('[data-name="search-input"]');
			this.$input = this.$el.find('[data-name="value-input"]');
			this.$title = this.$el.find('[data-name="value-title"]');
			this.$embed = this.$el.find('[data-name="value-embed"]');
			
			
			// options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		
		/*
		*  maybe_search
		*
		*  description
		*
		*  @type	function
		*  @date	14/10/16
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		maybe_search: function(){
			
			// set url and focus
	        var old_url = this.$input.val(),
	        	new_url = this.$search.val();
	        
	        
	        // bail early if no value
	        if( !new_url ) {
		        
		        this.clear();
		        return;
		        
	        }
	        
	        
	        // bail early if no change
	        if( new_url == old_url ) return;
	        
	        
	        // search
	        this.search();
	        
		},
		
		
		/*
		*  search
		*
		*  This function will search for an oembed
		*
		*  @type	function
		*  @date	13/10/16
		*  @since	5.4.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		search: function(){ 
			
			// vars
			var s = this.$search.val();
			
			
			// fix missing 'http://' - causes the oembed code to error and fail
			if( s.substr(0, 4) != 'http' ) {
				
				s = 'http://' + s;
				this.$search.val( s );
				
			}
			
			
			// show loading
			this.$el.addClass('is-loading');
			
			
			// AJAX data
			var ajax_data = fieldmaster.prepare_for_ajax({
				'action'	: 'fieldmaster/fields/oembed/search',
				's'			: s,
				'field_key'	: this.$field.data('key')
			});
			
			
			// abort XHR if this field is already loading AJAX data
			if( this.$el.data('xhr') ) this.$el.data('xhr').abort();
			
			
			// get HTML
			var xhr = $.ajax({
				url: fieldmaster.get('ajaxurl'),
				data: ajax_data,
				type: 'post',
				dataType: 'json',
				context: this,
				success: this.search_success
			});
			
			
			// update el data
			this.$el.data('xhr', xhr);
			
		},
		
		search_success: function( json ){
			
			// vars
			var s = this.$search.val();
			
			
			// remove loading
			this.$el.removeClass('is-loading');
			
			
			// error
			if( !json || !json.html ) {
				
				this.$el.removeClass('has-value').addClass('has-error');
				return;
				
			}
			
			
			// add classes
			this.$el.removeClass('has-error').addClass('has-value');
			
			
			// update vars
			this.$input.val( s );
			this.$title.html( s );
			this.$embed.html( json.html );
			
		},
				
		clear: function(){
			
			// update class
	        this.$el.removeClass('has-error has-value');
			
			
			// clear search
			this.$el.find('[data-name="search-input"]').val('');
			
			
			// clear inputs
			this.$input.val('');
			this.$title.html('');
			this.$embed.html('');
			
		},
		
		edit: function(){ 
			
			// add class
	        this.$el.addClass('is-editing');
	        
	        
	        // set url and focus
	        this.$search.val( this.$title.text() ).focus();
			
		},
		
		blur: function( $el ){ 
			
			// remove class
			this.$el.removeClass('is-editing');
			
			
			// maybe search
			this.maybe_search();
				        	
		},
		
		_search: function( e ){ // console.log('_search');
			
			this.search();
			
		},
		
		_clear: function( e ){ // console.log('_clear');
			
			this.clear();
			
		},
		
		_edit: function( e ){ // console.log('_clear');
			
			this.edit();
			
		},
		
		_keypress: function( e ){ // console.log('_keypress');
			
			// don't submit form
			if( e.which == 13 ) e.preventDefault();
			
		},
		
		_keyup: function( e ){  //console.log('_keypress', e.which);
			
			// bail early if no value
			if( !this.$search.val() ) return;
			
			
			// maybe search
			this.maybe_search();
			
		},
		
		_blur: function( e ){ // console.log('_blur');
			
			this.blur();
			
		}
		
	});

})(jQuery);

(function($){
	
	fieldmaster.fields.radio = fieldmaster.field.extend({
		
		type: 'radio',
		
		$ul: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'click input[type="radio"]': 'click'
		},
		
		focus: function(){
			
			// focus on $select
			this.$ul = this.$field.find('.fieldmaster-radio-list');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$ul );
			
		},
		
		
		/*
		*  initialize
		*
		*  This function will fix a bug found in Chrome.
		*  A radio input (for a given name) may only have 1 selected value. When used within a fc layout 
		*  multiple times (clone field), the selected value (default value) will not be checked. 
		*  This simply re-checks it.
		*
		*  @type	function
		*  @date	30/08/2016
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		initialize: function(){
			
			// find selected input and check it
			this.$ul.find('.selected input').prop('checked', true);	
			
		},
		
		click: function(e){
			
			// vars
			var $radio = e.$el,
				$label = $radio.parent('label'),
				selected = $label.hasClass('selected'),
				val = $radio.val();
				
				
			// remove previous selected
			this.$ul.find('.selected').removeClass('selected');
				
			
			// add active class
			$label.addClass('selected');
			
			
			// allow null
			if( this.o.allow_null && selected ) {
				
				// unselect
				e.$el.prop('checked', false);
				$label.removeClass('selected');
				val = false;
				
				
				// trigger change
				e.$el.trigger('change');
				
			}
			
			
			// other
			if( this.o.other_choice ) {
				
				// vars
				var $other = this.$ul.find('input[type="text"]');
				
				
				// show
				if( val === 'other' ) {
			
					$other.prop('disabled', false).attr('name', $radio.attr('name'));
				
				// hide
				} else {
					
					$other.prop('disabled', true).attr('name', '');
					
				}
					
			}
			
		}
		
	});	

})(jQuery);

(function($){
	
	fieldmaster.fields.relationship = fieldmaster.field.extend({
		
		type: 'relationship',
		
		$el: null,
		$input: null,
		$filters: null,
		$choices: null,
		$values: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'keypress [data-filter]': 			'submit_filter',
			'change [data-filter]': 			'change_filter',
			'keyup [data-filter]': 				'change_filter',
			'click .choices .fieldmaster-rel-item': 	'add_item',
			'click [data-name="remove_item"]': 	'remove_item'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-relationship');
			this.$input = this.$el.find('.fieldmaster-hidden input');
			this.$choices = this.$el.find('.choices'),
			this.$values = this.$el.find('.values');
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		initialize: function(){
			
			// reference
			var self = this,
				$field = this.$field,
				$el = this.$el,
				$input = this.$input;
			
			
			// right sortable
			this.$values.children('.list').sortable({
				items:					'li',
				forceHelperSize:		true,
				forcePlaceholderSize:	true,
				scroll:					true,
				update:	function(){
					
					$input.trigger('change');
					
				}
			});
			
			
			this.$choices.children('.list').scrollTop(0).on('scroll', function(e){
				
				// bail early if no more results
				if( $el.hasClass('is-loading') || $el.hasClass('is-empty') ) {
				
					return;
					
				}
				
				
				// Scrolled to bottom
				if( Math.ceil( $(this).scrollTop() ) + $(this).innerHeight() >= $(this).get(0).scrollHeight ) {
					
					// get paged
					var paged = $el.data('paged') || 1;
					
					
					// update paged
					$el.data('paged', (paged+1) );
					
					
					// fetch
					self.set('$field', $field).fetch();
					
				}
				
			});
			
			
/*
			// scroll event
			var maybe_fetch = function( e ){
				console.log('scroll');
				// remove listener
				$(window).off('scroll', maybe_fetch);
				
				
				// is field in view
			    if( fieldmaster.is_in_view($field) ) {
					
					// fetch
					self.doFocus($field);
					self.fetch();
					
					
					// return
					return;
				}
						
				
				// add listener
				setTimeout(function(){
					
					$(window).on('scroll', maybe_fetch);
				
				}, 500);
				
			};
*/
			
			
			// fetch
			this.fetch();
			
		},
		
/*
		show: function(){
			
			console.log('show field: %o', this.o.xhr);
			
			// bail ealry if already loaded
			if( typeof this.o.xhr !== 'undefined' ) {
				
				return;	
				
			}
			
			
			// is field in view
		    if( fieldmaster.is_in_view(this.$field) ) {
				
				// fetch
				this.fetch();
				
			}
			
		},
*/
		
		maybe_fetch: function(){
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// abort timeout
			if( this.o.timeout ) {
				
				clearTimeout( this.o.timeout );
				
			}
			
			
		    // fetch
		    var timeout = setTimeout(function(){
			    
			    self.doFocus($field);
			    self.fetch();
			    
		    }, 300);
		    
		    this.$el.data('timeout', timeout);
		    
		},
		
		fetch: function(){
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// add class
			this.$el.addClass('is-loading');
			
			
			// abort XHR if this field is already loading AJAX data
			if( this.o.xhr ) {
			
				this.o.xhr.abort();
				this.o.xhr = false;
				
			}
			
			
			// add to this.o
			this.o.action = 'fieldmaster/fields/relationship/query';
			this.o.field_key = $field.data('key');
			this.o.post_id = fieldmaster.get('post_id');
			
			
			// ready for ajax
			var ajax_data = fieldmaster.prepare_for_ajax( this.o );
			
			
			// clear html if is new query
			if( ajax_data.paged == 1 ) {
				
				this.$choices.children('.list').html('')
				
			}
			
			
			// add message
			this.$choices.find('ul:last').append('<p><i class="fieldmaster-loading"></i> ' + fieldmaster._e('relationship', 'loading') + '</p>');
			
			
			// get results
		    var xhr = $.ajax({
		    	url:		fieldmaster.get('ajaxurl'),
				dataType:	'json',
				type:		'post',
				data:		ajax_data,
				success:	function( json ){
					
					self.set('$field', $field).render( json );
					
				}
			});
			
			
			// update el data
			this.$el.data('xhr', xhr);
			
		},
		
		render: function( json ){
			
			// remove loading class
			this.$el.removeClass('is-loading is-empty');
			
			
			// remove p tag
			this.$choices.find('p').remove();
			
			
			// no results?
			if( !json || !json.results || !json.results.length ) {
			
				// add class
				this.$el.addClass('is-empty');
			
				
				// add message
				if( this.o.paged == 1 ) {
				
					this.$choices.children('.list').append('<p>' + fieldmaster._e('relationship', 'empty') + '</p>');
			
				}

				
				// return
				return;
				
			}
			
			
			// get new results
			var $new = $( this.walker(json.results) );
			
				
			// apply .disabled to left li's
			this.$values.find('.fieldmaster-rel-item').each(function(){
				
				$new.find('.fieldmaster-rel-item[data-id="' +  $(this).data('id') + '"]').addClass('disabled');
				
			});
			
			
			// underline search match
			// consider removing due to bug where matched strings within HTML attributes caused incorrect results
			// Looks like Select2 v4 has moved away from highlighting results, so perhaps we should too
			if( this.o.s ) {
			
				// vars
				var s = this.o.s;
				
				
				// allow special characters to be used within regex
				s = fieldmaster.addslashes(s);
				
				
				// loop
				$new.find('.fieldmaster-rel-item').each(function(){
					
					// vars
					var find = $(this).text(),
						replace = find.replace( new RegExp('(' + s + ')', 'gi'), '<b>$1</b>');
					
					$(this).html( $(this).html().replace(find, replace) );	
									
				});
				
			}
			
			
			// append
			this.$choices.children('.list').append( $new );
			
			
			// merge together groups
			var label = '',
				$list = null;
				
			this.$choices.find('.fieldmaster-rel-label').each(function(){
				
				if( $(this).text() == label ) {
					
					$list.append( $(this).siblings('ul').html() );
					
					$(this).parent().remove();
					
					return;
				}
				
				
				// update vars
				label = $(this).text();
				$list = $(this).siblings('ul');
				
			});
			
			
		},
		
		walker: function( data ){
			
			// vars
			var s = '';
			
			
			// loop through data
			if( $.isArray(data) ) {
			
				for( var k in data ) {
				
					s += this.walker( data[ k ] );
					
				}
				
			} else if( $.isPlainObject(data) ) {
				
				// optgroup
				if( data.children !== undefined ) {
					
					s += '<li><span class="fieldmaster-rel-label">' + data.text + '</span><ul class="fieldmaster-bl">';
					
						s += this.walker( data.children );
					
					s += '</ul></li>';
					
				} else {
				
					s += '<li><span class="fieldmaster-rel-item" data-id="' + data.id + '">' + data.text + '</span></li>';
					
				}
				
			}
			
			
			// return
			return s;
			
		},
		
		submit_filter: function( e ){
			
			// don't submit form
			if( e.which == 13 ) {
				
				e.preventDefault();
				
			}
			
		},
		
		change_filter: function( e ){
			
			// vars
			var val = e.$el.val(),
				filter = e.$el.data('filter');
				
			
			// Bail early if filter has not changed
			if( this.$el.data(filter) == val ) {
			
				return;
				
			}
			
			
			// update attr
			this.$el.data(filter, val);
			
			
			// reset paged
			this.$el.data('paged', 1);
			
		    
		    // fetch
		    if( e.$el.is('select') ) {
			    
				this.fetch();
			
			// search must go through timeout
		    } else {
			    
			    this.maybe_fetch();
			     
		    }
		        
		},
		
		add_item: function( e ){
			
			// max posts
			if( this.o.max > 0 ) {
			
				if( this.$values.find('.fieldmaster-rel-item').length >= this.o.max ) {
				
					alert( fieldmaster._e('relationship', 'max').replace('{max}', this.o.max) );
					
					return;
					
				}
				
			}
			
			
			// can be added?
			if( e.$el.hasClass('disabled') ) {
			
				return false;
				
			}
			
			
			// disable
			e.$el.addClass('disabled');
			
			
			// template
			var html = [
				'<li>',
					'<input type="hidden" name="' + this.$input.attr('name') + '[]" value="' + e.$el.data('id') + '" />',
					'<span data-id="' + e.$el.data('id') + '" class="fieldmaster-rel-item">' + e.$el.html(),
						'<a href="#" class="fieldmaster-icon -minus small dark" data-name="remove_item"></a>',
					'</span>',
				'</li>'].join('');
						
			
			// add new li
			this.$values.children('.list').append( html )
			
			
			// trigger change on new_li
			this.$input.trigger('change');
			
			
			// validation
			fieldmaster.validation.remove_error( this.$field );
			
		},
		
		remove_item : function( e ){
			
			// vars
			var $span = e.$el.parent(),
				id = $span.data('id');
			
			
			// remove
			$span.parent('li').remove();
			
			
			// show
			this.$choices.find('.fieldmaster-rel-item[data-id="' + id + '"]').removeClass('disabled');
			
			
			// trigger change on new_li
			this.$input.trigger('change');
			
		}
		
	});
	

})(jQuery);

(function($){
	
	// globals
	var _select2,
		_select23,
		_select24;
	
	
	/*
	*  fieldmaster.select2
	*
	*  all logic to create select2 instances
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	_select2 = fieldmaster.select2 = fieldmaster.model.extend({
		
		// vars
		version: 0,
		version3: null,
		version4: null,
		
		
		// actions
		actions: {
			'ready 1': 'ready'
		},
		
		
		/*
		*  ready
		*
		*  This function will run on document ready
		*
		*  @type	function
		*  @date	21/06/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		ready: function(){
			
			// determine Select2 version
			this.version = this.get_version();
			
			
			// ready
			this.do_function('ready');
			
		},
		
		
		/*
		*  get_version
		*
		*  This function will return the Select2 version
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		get_version: function(){
			
			if( fieldmaster.maybe_get(window, 'Select2') ) return 3;
			if( fieldmaster.maybe_get(window, 'jQuery.fn.select2.amd') ) return 4;
			return 0;
			
		},
		
		
		/*
		*  do_function
		*
		*  This function will call the v3 or v4 equivelant function
		*
		*  @type	function
		*  @date	28/4/17
		*  @since	5.5.13
		*
		*  @param	name (string)
		*  @param	args (array)
		*  @return	(mixed)
		*/
		
		do_function: function( name, args ){
			
			// defaults
			args = args || [];
			
			
			// vars
			var model = 'version'+this.version;
			
			
			// bail early if not set
			if( typeof this[model] === 'undefined' ||
				typeof this[model][name] === 'undefined' ) return false;
			
			
			// run
			return this[model][name].apply( this, args );
			
		},
				
		
		/*
		*  get_data
		*
		*  This function will look at a $select element and return an object choices 
		*
		*  @type	function
		*  @date	24/12/2015
		*  @since	5.3.2
		*
		*  @param	$select (jQuery)
		*  @return	(array)
		*/
		
		get_data: function( $select, data ){
			
			// reference
			var self = this;
			
			
			// defaults
			data = data || [];
			
			
			// loop over children
			$select.children().each(function(){
				
				// vars
				var $el = $(this);
				
				
				// optgroup
				if( $el.is('optgroup') ) {
					
					data.push({
						'text':		$el.attr('label'),
						'children':	self.get_data( $el )
					});
				
				// option
				} else {
					
					data.push({
						'id':	$el.attr('value'),
						'text':	$el.text()
					});
					
				}
				
			});
			
			
			// return
			return data;
			
		},
		
				
		/*
		*  decode_data
		*
		*  This function will take an array of choices and decode the text
		*  Changes '&amp;' to '&' which fixes a bug (in Select2 v3 )when searching for '&'
		*
		*  @type	function
		*  @date	24/12/2015
		*  @since	5.3.2
		*
		*  @param	$select (jQuery)
		*  @return	(array)
		*/
		
		decode_data: function( data ) {
			
			// bail ealry if no data
			if( !data ) return [];
			
			
			//loop
			$.each(data, function(k, v){
				
				// text
				data[ k ].text = fieldmaster.decode( v.text );
				
				
				// children
				if( typeof v.children !== 'undefined' ) {
					
					data[ k ].children = _select2.decode_data(v.children);
					
				}
				
			});
			
			
			// return
			return data;
			
		},
		
		
		/*
		*  count_data
		*
		*  This function will take an array of choices and return the count
		*
		*  @type	function
		*  @date	24/12/2015
		*  @since	5.3.2
		*
		*  @param	data (array)
		*  @return	(int)
		*/
		
		count_data: function( data ) {
			
			// vars
			var i = 0;
			
			
			// bail ealry if no data
			if( !data ) return i;
			
			
			//loop
			$.each(data, function(k, v){
				
				// increase
				i++;
				
				
				// children
				if( typeof v.children !== 'undefined' ) {
					
					i += v.children.length;
					
				}
				
			});
			
			
			// return
			return i;
			
		},
		
		
		/*
		*  get_ajax_data
		*
		*  This function will return an array of data to send via AJAX
		*
		*  @type	function
		*  @date	19/07/2016
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		get_ajax_data: function( args, params, $el, $field ){
			
			// vars
			var data = fieldmaster.prepare_for_ajax({
				action: 	args.ajax_action,
				field_key: 	args.key,
				s: 			params.term || '',
				paged: 		params.page || 1
			});
			
			
			// filter
			data = fieldmaster.apply_filters( 'select2_ajax_data', data, args, $el, $field );
			
			
			// return
			return data;
			
		},
		
		
		/*
		*  get_ajax_results
		*
		*  This function will return a valid AJAX response
		*
		*  @type	function
		*  @date	19/07/2016
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		get_ajax_results: function( data, params ){
			
			// vars
			var valid = {
				results: []
			};
			
			
			// bail early if no data
			if( !data ) {
				
				data = valid;
				
			}
			
			
			// allow for an array of choices
			if( typeof data.results == 'undefined' ) {
				
				valid.results = data;
				
				data = valid;
				
			}
			
			
			// decode
			data.results = this.decode_data(data.results);
			
			
			// filter
			data = fieldmaster.apply_filters( 'select2_ajax_results', data, params );
			
			
			// return
			return data;
			
		},
		
		
		/*
		*  get_value
		*
		*  This function will return the selected options in a Select2 format
		*
		*  @type	function
		*  @date	5/01/2016
		*  @since	5.3.2
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		get_value: function( $select ){
		
			// vars
			var val = [],
				$selected = $select.find('option:selected');
			
			
			// bail early if no selected
			if( !$selected.exists() ) return val;
			
			
			// sort
			$selected = $selected.sort(function(a, b) {
				
			    return +a.getAttribute('data-i') - +b.getAttribute('data-i');
			    
			});
			
			
			// loop
			$selected.each(function(){
				
				// vars
				var $el = $(this);
				
				
				// append
				val.push({
					'id':	$el.attr('value'),
					'text':	$el.text(),
					'$el':	$el
				});
				
			});
			
			
			// return
			return val;
			
		},
		
		
		/*
		*  get_input_value
		*
		*  This function will return an array of values as per the hidden input
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	$input (jQuery)
		*  @return	(array)
		*/
		
		get_input_value: function( $input ) {
			
			return $input.val().split('||');
			
		},
		
		
		/*
		*  sync_input_value
		*
		*  This function will save the current selected values into the hidden input
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	$input (jQuery)
		*  @param	$select (jQuery)
		*  @return	n/a
		*/
		
		sync_input_value: function( $input, $select ) {
			
			$input.val( $select.val().join('||') );
			
		},
		
		
		/*
		*  add_option
		*
		*  This function will add an <option> element to a select (if it doesn't already exist)
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	$select (jQuery)
		*  @param	value (string)
		*  @param	label (string)
		*  @return	n/a
		*/
		
		add_option: function( $select, value, label ){
			
			if( !$select.find('option[value="'+value+'"]').length ) {
				
				$select.append('<option value="'+value+'">'+label+'</option>');
				
			}
			
		},
		
		
		/*
		*  select_option
		*
		*  This function will select an option
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	$select (jQuery)
		*  @param	value (string)
		*  @return	n/a
		*/
		
		select_option: function( $select, value ){
			
			$select.find('option[value="'+value+'"]').prop('selected', true);
			$select.trigger('change');
			
		},
		
		
		/*
		*  unselect_option
		*
		*  This function will unselect an option
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.13
		*
		*  @param	$select (jQuery)
		*  @param	value (string)
		*  @return	n/a
		*/
		
		unselect_option: function( $select, value ){
			
			$select.find('option[value="'+value+'"]').prop('selected', false);
			$select.trigger('change');
			
		},
		
		
		/*
		*  Select2 v3 or v4 functions
		*
		*  description
		*
		*  @type	function
		*  @date	29/4/17
		*  @since	5.5.10
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		init: function( $select, args, $field ){
			
			this.do_function( 'init', arguments );
					
		},
		
		destroy: function( $select ){
			
			this.do_function( 'destroy', arguments );
			
		},
		
		add_value: function( $select, value, label ){
			
			this.do_function( 'add_value', arguments );
			
		},
		
		remove_value: function( $select, value ){
			
			this.do_function( 'remove_value', arguments );
			
		},
		
		remove_value: function( $select, value ){
			
			this.do_function( 'remove_value', arguments );
			
		}
		
	});
	
	
	/*
	*  Select2 v3
	*
	*  This model contains the Select2 v3 functions
	*
	*  @type	function
	*  @date	28/4/17
	*  @since	5.5.10
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	_select23 = _select2.version3 = {
		
		ready: function(){
			
			// vars
			var locale = fieldmaster.get('locale'),
				rtl = fieldmaster.get('rtl')
				l10n = fieldmaster._e('select');
			
			
			// bail ealry if no l10n
			if( !l10n ) return;
			
			
			// vars
			var l10n_functions = {
				formatMatches: function( matches ) {
					
					if ( 1 === matches ) {
						return l10n.matches_1;
					}
	
					return l10n.matches_n.replace('%d', matches);
				},
				formatNoMatches: function() {
					return l10n.matches_0;
				},
				formatAjaxError: function() {
					return l10n.load_fail;
				},
				formatInputTooShort: function( input, min ) {
					var number = min - input.length;
	
					if ( 1 === number ) {
						return l10n.input_too_short_1;
					}
	
					return l10n.input_too_short_n.replace( '%d', number );
				},
				formatInputTooLong: function( input, max ) {
					var number = input.length - max;
	
					if ( 1 === number ) {
						return l10n.input_too_long_1;
					}
	
					return l10n.input_too_long_n.replace( '%d', number );
				},
				formatSelectionTooBig: function( limit ) {
					if ( 1 === limit ) {
						return l10n.selection_too_long_1;
					}
	
					return l10n.selection_too_long_n.replace( '%d', limit );
				},
				formatLoadMore: function() {
					return l10n.load_more;
				},
				formatSearching: function() {
					return l10n.searching;
				}
		    };
			
			
			// ensure locales exists
			// older versions of Select2 did not have a locale storage
			$.fn.select2.locales = fieldmaster.maybe_get(window, 'jQuery.fn.select2.locales', {});
			
			
			// append
			$.fn.select2.locales[ locale ] = l10n_functions;
			$.extend($.fn.select2.defaults, l10n_functions);
			
		},
		
		set_data: function( $select, data ){
			
			// v3
			if( this.version == 3 ) {
				
				$select = $select.siblings('input');
				
			}
			
			
			// set data
			$select.select2('data', data);
			
		},
		
		append_data: function( $select, data ){
			
			// v3
			if( this.version == 3 ) {
				
				$select = $select.siblings('input');
				
			}
			
			
			
			// vars
			var current = $select.select2('data') || [];
			
			
			// append
			current.push( data );
			
			
			// set data
			$select.select2('data', current);
			
		},
		
		
		/*
		*  init_v3
		*
		*  This function will create a new Select2 for v3
		*
		*  @type	function
		*  @date	24/12/2015
		*  @since	5.3.2
		*
		*  @param	$select (jQuery)
		*  @return	args (object)
		*/
		
		init: function( $select, args, $field ){
			
			// defaults
			args = args || {};
			$field = $field || null;
			
			
			// merge
			args = $.extend({
				allow_null:		false,
				placeholder:	'',
				multiple:		false,
				ajax:			false,
				ajax_action:	''
			}, args);
			
				
			// vars
			var $input = $select.siblings('input');
			
			
			// bail early if no input
			if( !$input.exists() ) return;
			
			
			// select2 args
			var select2_args = {
				width:				'100%',
				containerCssClass:	'-fieldmaster',
				allowClear:			args.allow_null,
				placeholder:		args.placeholder,
				multiple:			args.multiple,
				separator:			'||',
				data:				[],
				escapeMarkup:		function( m ){ return m; },
				formatResult:		function( result, container, query, escapeMarkup ){
					
					// run default formatResult
					var text = $.fn.select2.defaults.formatResult( result, container, query, escapeMarkup );
					
										
					// append description
					if( result.description ) {
						
						text += ' <span class="select2-result-description">' + result.description + '</span>';
						
					}
					
					
					// return
					return text;
					
				}
			};
			
			
			// value
			var value = this.get_value( $select );
			
			
			// multiple
			if( args.multiple ) {
				
				// vars
				var name = $select.attr('name');
				
				
				// add hidden input to each multiple selection
				select2_args.formatSelection = function( object, $div ){
					
					// vars
					var html = '<input type="hidden" class="select2-search-choice-hidden" name="' + name + '" value="' + object.id + '"' + ($input.prop('disabled') ? 'disabled="disabled"' : '') + ' />';
					
					
					// append input
					$div.parent().append(html);
					
					
					// return
					return object.text;
					
				}
				
			} else {
				
				// change array to single object
				value = fieldmaster.maybe_get(value, 0, false);
				
				
				// if no allow_null, this single select must contain a selection
				if( !args.allow_null && value ) {
					
					$input.val( value.id );
					
				}
			}
			
			
			// remove the blank option as we have a clear all button!
			if( args.allow_null ) {
				
				$select.find('option[value=""]').remove();
				
			}
			
			
			// get data
			select2_args.data = this.get_data( $select );
			
		    
		    // initial selection
		    select2_args.initSelection = function( element, callback ) {
				
				callback( value );
		        
		    };
		    
		    
			// ajax
			if( args.ajax ) {
				
				select2_args.ajax = {
					url:			fieldmaster.get('ajaxurl'),
					dataType: 		'json',
					type: 			'post',
					cache: 			false,
					quietMillis:	250,
					data: function( term, page ) {
						
						// vars
						var params = { 'term': term, 'page': page };
						
						
						// return
						return _select2.get_ajax_data(args, params, $input, $field);
						
					},
					results: function( data, page ){
						
						// vars
						var params = { 'page': page };
						
						
						// merge together groups
						setTimeout(function(){
							
							_select23.merge_results();
							
						}, 1);
						
						
						// return
						return _select2.get_ajax_results(data, params);
						
					}
				};
				
			}
			
			
			// attachment z-index fix
			select2_args.dropdownCss = {
				'z-index' : '999999999'
			};
			
			
			// append args
			select2_args.fieldmaster = args;
			
			
			// filter for 3rd party customization
			select2_args = fieldmaster.apply_filters( 'select2_args', select2_args, $select, args, $field );
			
			
			// add select2
			$input.select2( select2_args );
			
			
			// vars
			var $container = $input.select2('container');
			
			
			// reorder DOM
			// - this order is very important so don't change it
			// - $select goes first so the input can override it. Fixes issue where conditional logic will enable the select
			// - $input goes second to reset the input data
			// - container goes last to allow multiple hidden inputs to override $input
			$container.before( $select );
			$container.before( $input );
			
			
			// multiple
			if( args.multiple ) {
				
				// sortable
				$container.find('ul.select2-choices').sortable({
					 start: function() {
					 	$input.select2("onSortStart");
					 },
					 stop: function() {
					 	$input.select2("onSortEnd");
					 }
				});
				
			}
			
			
			// disbale select
			$select.prop('disabled', true).addClass('fieldmaster-disabled fieldmaster-hidden');
			
			
			// update select value
			// this fixes a bug where select2 appears blank after duplicating a post_object field (field settings).
			// the $select is disabled, so setting the value won't cause any issues (this is what select2 v4 does anyway).
			$input.on('change', function(e) {
				
				// add new data
				if( e.added ) {
					
					// add item
					_select2.add_option($select, e.added.id, e.added.text);
					
				}
				
				
				// select
				_select2.select_option($select, e.val);
				
			});
			
			
			// action for 3rd party customization
			fieldmaster.do_action('select2_init', $input, select2_args, args, $field);
			
		},
		
		
		/*
		*  merge_results_v3
		*
		*  description
		*
		*  @type	function
		*  @date	20/07/2016
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		merge_results: function(){
			
			// vars
			var label = '',
				$list = null;
			
			
			// loop
			$('#select2-drop .select2-result-with-children').each(function(){
				
				// vars
				var $label = $(this).children('.select2-result-label'),
					$ul = $(this).children('.select2-result-sub');
				
				
				// append group to previous
				if( $label.text() == label ) {
					
					$list.append( $ul.children() );
					
					$(this).remove();
					
					return;
					
				}
				
				
				// update vars
				label = $label.text();
				$list = $ul;
				
			});
			
		},
		
		
		/*
		*  destroy
		*
		*  This function will destroy a Select2
		*
		*  @type	function
		*  @date	24/12/2015
		*  @since	5.3.2
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		destroy: function( $select ){
			
			// vars
			var $input = $select.siblings('input');
			
			
			// bail early if no select2
			if( !$input.data('select2') ) return;
			
			
			// destroy
			$input.select2('destroy');
			
			
			// enable select
			$select.prop('disabled', false).removeClass('fieldmaster-disabled fieldmaster-hidden');
					
		},
		
		add_value: function( $select, value, label ){
			
			// add and select item
			_select2.add_option($select, value, label);
			_select2.select_option($select, value);
			
			
			// vars
			var $input = $select.siblings('input');
			
			
			// new item
			var item = {
				'id':	value,
				'text':	label
			};
			
			
			// single
			if( !$select.data('multiple') ) {
				
				return $input.select2('data', item);
				
			}
			
			
			// get existing value
			var values = $input.select2('data') || [];
			
			
			// append
			values.push(item);
			
			
			// set data
			return $input.select2('data', values);
			
		},
		
		remove_value: function( $select, value ){
			
			// unselect option
			_select2.unselect_option($select, value);
			
			
			// vars
			var $input = $select.siblings('input'),
				current = $input.select2('data');
			
			
			// single
			if( !$select.data('multiple') ) {
				
				if( current && current.id == value ) {
					
					$input.select2('data', null);
					
				}
			
			// multiple	
			} else {
				
				// filter
				current = $.grep(current, function( item ) {
				    return item.id != value;
				});
				
				
				// set data
				$input.select2('data', current);
				
			}
			
		}
		
		
	};
	
	
	/*
	*  Select2 v4
	*
	*  This model contains the Select2 v4 functions
	*
	*  @type	function
	*  @date	28/4/17
	*  @since	5.5.10
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	_select24 = _select2.version4 = {
		
		init: function( $select, args, $field ){
			
			// defaults
			args = args || {};
			$field = $field || null;
			
			
			// merge
			args = $.extend({
				allow_null:		false,
				placeholder:	'',
				multiple:		false,
				ajax:			false,
				ajax_action:	''
			}, args);
			
			
			// vars
			var $input = $select.siblings('input');
			
			
			// bail early if no input
			if( !$input.exists() ) return;
			
			
			// select2 args
			var select2_args = {
				width:				'100%',
				allowClear:			args.allow_null,
				placeholder:		args.placeholder,
				multiple:			args.multiple,
				separator:			'||',
				data:				[],
				escapeMarkup:		function( m ){ return m; }
			};
			
			
			// value
			var value = this.get_value( $select );
			
			
			// multiple
			if( args.multiple ) {
				
				// reorder opts
				$.each(value, function( k, item ){
					
					// detach and re-append to end
					item.$el.detach().appendTo( $select );
						
				});
				
			} else {
				
				// change array to single object
				value = fieldmaster.maybe_get(value, 0, '');
				
			}
			
			
			// remove the blank option as we have a clear all button!
			if( args.allow_null ) {
				
				$select.find('option[value=""]').remove();
				
			}
			
		    
		    // remove conflicting atts
			if( !args.ajax ) {
				
				$select.removeData('ajax');
				$select.removeAttr('data-ajax');
				
			} else {
				
				select2_args.ajax = {
					url:		fieldmaster.get('ajaxurl'),
					delay: 		250,
					dataType: 	'json',
					type: 		'post',
					cache: 		false,
					data: function( params ) {
						
						// return
						return _select2.get_ajax_data(args, params, $select, $field);
						
					},
					processResults: function( data, params ){
						
						// vars
						var results = _select2.get_ajax_results(data, params);
						
						
						// change to more
						if( results.more ) {
							
							results.pagination = { more: true };
							
						}
						
						
						// merge together groups
						setTimeout(function(){
							
							_select24.merge_results();
							
						}, 1);
						
						
						// return
						return results
						
					}
					
				};
				
			}
		    
			
			// filter for 3rd party customization
			select2_args = fieldmaster.apply_filters( 'select2_args', select2_args, $select, args, $field );
			
			
			// add select2
			$select.select2( select2_args );
			
			
			// get container (Select2 v4 deos not return this from constructor)
			var $container = $select.next('.select2-container');
			
			
			// reorder DOM
			// - no need to reorder, the select field is needed to $_POST values
			
			
			// multiple
			if( args.multiple ) {
				
				// vars
				var $ul = $container.find('ul');
				
				
				// sortable
				$ul.sortable({
					
		            stop: function( e ) {
			            
			            $ul.find('.select2-selection__choice').each(function() {
				            
				            // vars
							var $option = $( $(this).data('data').element );
							
							
							// detach and re-append to end
							$option.detach().appendTo( $select );
		                    
		                    
		                    // trigger change on input (JS error if trigger on select)
		                    $input.trigger('change');
		                    // update input
		                    //_select2.sync_input_value( $input, $select );
		                    
		                });
		                
		            }

				});
				
				
				// on select, move to end
				$select.on('select2:select', function( e ){
					
					// vars
					var $option = $(e.params.data.element);
					
					
					// detach and re-append to end
					$option.detach().appendTo( $select );
					
					 
					// trigger change
					//$select.trigger('change');
					
				});
				
			}
			
			
/*
			// update input
			$select.on('select2:select', function( e ){
				
				// update input
	            _select2.sync_input_value( $input, $select );
				
			});
			
			$select.on('select2:unselect', function( e ){
				
				// update input
	            _select2.sync_input_value( $input, $select );
				
			});
*/
			
			
			// clear value (allows null to be saved)
			$input.val('');
			
			
			// add class
			$container.addClass('-fieldmaster');
			
			
			// action for 3rd party customization
			fieldmaster.do_action('select2_init', $select, select2_args, args, $field);
			
		},
		
		
		/*
		*  merge_results_v4
		*
		*  description
		*
		*  @type	function
		*  @date	20/07/2016
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		merge_results: function(){
			
			// vars
			var $prev_options = null,
				$prev_group = null;
			
			
			// loop
			$('.select2-results__option[role="group"]').each(function(){
				
				// vars
				var $options = $(this).children('ul'),
					$group = $(this).children('strong');
				
				
				// compare to previous
				if( $prev_group !== null && $group.text() == $prev_group.text() ) {
					
					$prev_options.append( $options.children() );
					
					$(this).remove();
					
					return;
					
				}
				
				
				// update vars
				$prev_options = $options;
				$prev_group = $group;
				
			});
			
		},
		
		add_value: function( $select, value, label ){
			
			// add and select item
			_select2.add_option($select, value, label);
			_select2.select_option($select, value);
			
		},
		
		remove_value: function( $select, value ){
			
			// unselect
			_select2.unselect_option($select, value);
			
		},
		
		destroy: function( $select ){
			
			// bail early if no select2
			if( !$select.data('select2') ) return;
			
			
			// destroy
			$select.select2('destroy');
					
		}
		
	};
	
	
	/*
	*  depreciated
	*
	*  These functions have moved since v5.3.3 
	*
	*  @type	function
	*  @date	11/12/2015
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	fieldmaster.add_select2 = function( $select, args ) {
		
		_select2.init( $select, args );

	}
	
	fieldmaster.remove_select2 = function( $select ) {
		
		_select2.destroy( $select );
		
	}

})(jQuery);

(function($){
	
	// select
	fieldmaster.fields.select = fieldmaster.field.extend({
		
		type: 'select',
		
		$select: null,
		
		actions: {
			'ready':	'render',
			'append':	'render',
			'remove':	'remove'
		},

		focus: function(){
			
			// focus on $select
			this.$select = this.$field.find('select');
			
			
			// bail early if no select field
			if( !this.$select.exists() ) return;
			
			
			// get options
			this.o = fieldmaster.get_data( this.$select );
			
			
			// customize o
			this.o = fieldmaster.parse_args(this.o, {
				'ajax_action':	'fieldmaster/fields/'+this.type+'/query',
				'key':			this.$field.data('key')
			});
			
		},
		
		render: function(){
			
			// validate ui
			if( !this.$select.exists() || !this.o.ui ) {
				
				return false;
				
			}
			
			
			fieldmaster.select2.init( this.$select, this.o, this.$field );
			
		},
		
		remove: function(){
			
			// validate ui
			if( !this.$select.exists() || !this.o.ui ) {
				
				return false;
				
			}
			
			
			// remove select2
			fieldmaster.select2.destroy( this.$select );
			
		}
		 
	});
	
		
	// user
	fieldmaster.fields.user = fieldmaster.fields.select.extend({
		
		type: 'user'
		
	});	
	
	
	// post_object
	fieldmaster.fields.post_object = fieldmaster.fields.select.extend({
		
		type: 'post_object'
		
	});
	
	
	// page_link
	fieldmaster.fields.page_link = fieldmaster.fields.select.extend({
		
		type: 'page_link'
		
	});
	

})(jQuery);

(function($){
	
	fieldmaster.fields.tab = fieldmaster.field.extend({
		
		type: 'tab',
		$el: null,
		$wrap: null,
		
		actions: {
			'prepare':	'initialize',
			'append':	'initialize',
			'hide':		'hide',
			'show':		'show'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-tab');
			
			
			// get options
			this.o = this.$el.data();
			this.o.key = this.$field.data('key');
			this.o.text = this.$el.html();
			
		},
		
		initialize: function(){
			
			// bail early if is td
			if( this.$field.is('td') ) return;
			
			
			// add tab
			tab_manager.add_tab( this.$field, this.o );
			
		},
		
		hide: function( $field, context ){
			
			// bail early if not conditional logic
			if( context != 'conditional_logic' ) return;
			
			
			// vars
			var key = $field.data('key'),
				$group = $field.prevAll('.fieldmaster-tab-wrap'),
				$a = $group.find('a[data-key="' + key + '"]'),
				$li = $a.parent();
			
			
			// bail early if $group does not exist (clone field)
			if( !$group.exists() ) return;
			
			
			// hide li
			$li.addClass('hidden-by-conditional-logic');
			
			
			// set timout to allow proceeding fields to hide first
			// without this, the tab field will hide all fields, regarless of if that field has it's own conditional logic rules
			setTimeout(function(){
				
			// if this tab field was hidden by conditional_logic, disable it's children to prevent validation
			$field.nextUntil('.fm-field-tab', '.fm-field').each(function(){
				
				// bail ealry if already hidden
				if( $(this).hasClass('hidden-by-conditional-logic') ) return;
				
				
				// hide field
				fieldmaster.conditional_logic.hide_field( $(this) );
				
				
				// add parent reference
				$(this).addClass('-hbcl-' + key);
				
			});
			
			
			// select other tab if active
			if( $li.hasClass('active') ) {
				
				$group.find('li:not(.hidden-by-conditional-logic):first a').trigger('click');
				
			}
			
			}, 0);
			
		},
		
		show: function( $field, context ){
			
			// bail early if not conditional logic
			if( context != 'conditional_logic' ) return;
			
			// vars
			var key = $field.data('key'),
				$group = $field.prevAll('.fieldmaster-tab-wrap'),
				$a = $group.find('a[data-key="' + key + '"]'),
				$li = $a.parent();
			
			
			// bail early if $group does not exist (clone field)
			if( !$group.exists() ) return;
			
			
			// show li
			$li.removeClass('hidden-by-conditional-logic');
			
			
			// set timout to allow proceeding fields to hide first
			// without this, the tab field will hide all fields, regarless of if that field has it's own conditional logic rules
			setTimeout(function(){
				
			// if this tab field was shown by conditional_logic, enable it's children to allow validation
			$field.siblings('.fm-field.-hbcl-' + key).each(function(){
				
				// show field
				fieldmaster.conditional_logic.show_field( $(this) );
				
				
				// remove parent reference
				$(this).removeClass('-hbcl-' + key);
				
			});
			
			
			// select tab if no other active
			var $active = $li.siblings('.active');
			if( !$active.exists() || $active.hasClass('hidden-by-conditional-logic') ) {
				
				$a.trigger('click');
				
			}
			
			}, 0);
			
		}
		
	});
	
	
	/*
	*  tab_manager
	*
	*  This model will handle adding tabs and groups
	*
	*  @type	function
	*  @date	25/11/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	var tab_manager = fieldmaster.model.extend({
		
		actions: {
			'prepare 15':	'render',
			'append 15':	'render',
			'refresh 15': 	'render'
		},
		
		events: {
			'click .fieldmaster-tab-button': '_click'
		},
		
		
		render: function( $el ){
			
			// find visible tab wraps
			$('.fieldmaster-tab-wrap', $el).each(function(){
				
				// vars
				var $group = $(this),
					$wrap = $group.parent();
				
				
				// trigger click
				if( !$group.find('li.active').exists() ) {
					
					$group.find('li:not(.hidden-by-conditional-logic):first a').trigger('click');
					
				}
				
				
				if( $wrap.hasClass('-sidebar') ) {
					
					// vars
					var attribute = $wrap.is('td') ? 'height' : 'min-height';
					
					
					// find height (minus 1 for border-bottom)
					var height = $group.position().top + $group.children('ul').outerHeight(true) - 1;
					
					
					// add css
					$wrap.css(attribute, height);
					
				}
						
			});
			
		},
		
		add_group: function( $field, settings ){
			
			// vars
			var $wrap = $field.parent(),
				html = '';
			
			
			// add sidebar to wrap
			if( $wrap.hasClass('fm-fields') && settings.placement == 'left' ) {
				
				$wrap.addClass('-sidebar');
			
			// can't have side tab without sidebar	
			} else {
				
				settings.placement = 'top';
				
			}
			
			
			// generate html
			if( $wrap.is('tbody') ) {
				
				html = '<tr class="fieldmaster-tab-wrap"><td colspan="2"><ul class="fieldmaster-hl fieldmaster-tab-group"></ul></td></tr>';
			
			} else {
			
				html = '<div class="fieldmaster-tab-wrap -' + settings.placement + '"><ul class="fieldmaster-hl fieldmaster-tab-group"></ul></div>';
				
			}
			
			
			// save
			$group = $(html);
			
			
			// append
			$field.before( $group );
			
			
			// return
			return $group;
		},
		
		add_tab: function( $field, settings ){ //console.log('add_tab(%o, %o)', $field, settings);
			
			// vars
			var $group = $field.siblings('.fieldmaster-tab-wrap').last();
			
			
			// add tab group if no group exists
			if( !$group.exists() ) {
			
				$group = this.add_group( $field, settings );
			
			// add tab group if is endpoint	
			} else if( settings.endpoint ) {
				
				$group = this.add_group( $field, settings );
				
			}
			
			
			// vars
			var $li = $('<li><a class="fieldmaster-tab-button" href="#" data-key="' + settings.key + '">' + settings.text + '</a></li>');
			
			
			// hide li
			if( settings.text === '' ) $li.hide();
			
			
			// add tab
			$group.find('ul').append( $li );
			
			
			// conditional logic
			if( $field.hasClass('hidden-by-conditional-logic') ) {
				
				$li.addClass('hidden-by-conditional-logic');
				
			}
			
		},
		
		_click: function( e ){
			
			// prevent default
			e.preventDefault();
			
			
			// reference
			var self = this;
			
			
			// vars
			var $a = e.$el,
				$group = $a.closest('.fieldmaster-tab-wrap'),
				show = $a.data('key'),
				current = '';
			
			
			// add and remove classes
			$a.parent().addClass('active').siblings().removeClass('active');
			
			
			// loop over all fields until you hit another group
			$group.nextUntil('.fieldmaster-tab-wrap', '.fm-field').each(function(){
				
				// vars
				var $field = $(this);
				
				
				// set current
				if( $field.data('type') == 'tab' ) {
					
					current = $field.data('key');
					
					// bail early if endpoint is found
					if( $field.hasClass('endpoint') ) {
						
						// stop loop - current tab group is complete
						return false;
						
					}
					
				}
				
				
				// show
				if( current === show ) {
					
					// only show if hidden
					if( $field.hasClass('hidden-by-tab') ) {
						
						$field.removeClass('hidden-by-tab');
						
						fieldmaster.do_action('show_field', $(this), 'tab');
						
					}
				
				// hide
				} else {
					
					// only hide if not hidden
					if( !$field.hasClass('hidden-by-tab') ) {
						
						$field.addClass('hidden-by-tab');
						
						fieldmaster.do_action('hide_field', $(this), 'tab');
						
					}
					
				}
				
			});
			
			
			// action for 3rd party customization
			fieldmaster.do_action('refresh', $group.parent() );
			
			
			// blur
			$a.trigger('blur');
			
		}
	
	});
	
	
	/*
	*  tab_validation
	*
	*  This model will handle validation of fields within a tab group
	*
	*  @type	function
	*  @date	25/11/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	var tab_validation = fieldmaster.model.extend({
		
		active: 1,
		
		actions: {
			'add_field_error': 'add_field_error'
		},
		
		add_field_error: function( $field ){
			
			// bail early if already focused
			if( !this.active ) {
				
				return;
				
			}
			
			
			// bail early if not hidden by tab
			if( !$field.hasClass('hidden-by-tab') ) {
				
				return;
				
			}
			
			
			// reference
			var self = this;
			
			
			// vars
			var $tab = $field.prevAll('.fm-field-tab:first'),
				$group = $field.prevAll('.fieldmaster-tab-wrap:first');
			
			
			// focus
			$group.find('a[data-key="' + $tab.data('key') + '"]').trigger('click');
			
			
			// disable functionality for 1sec (allow next validation to work)
			this.active = 0;
			
			setTimeout(function(){
				
				self.active = 1;
				
			}, 1000);
			
		}
		
	});	
	

})(jQuery);

(function($){
	
	fieldmaster.fields.time_picker = fieldmaster.field.extend({
		
		type: 'time_picker',
		$el: null,
		$input: null,
		$hidden: null,
		
		o: {},
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize'
		},
		
		events: {
			'blur input[type="text"]': 'blur'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.fieldmaster-time-picker');
			this.$input = this.$el.find('input[type="text"]');
			this.$hidden = this.$el.find('input[type="hidden"]');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
		},
		
		initialize: function(){
			
			// bail ealry if no timepicker library
			if( typeof $.timepicker === 'undefined' ) return;
			
			
			// create options
			var args = {
				timeFormat:			this.o.time_format,
				altField:			this.$hidden,
				altFieldTimeOnly:	false,
				altTimeFormat:		'HH:mm:ss',
				showButtonPanel:	true,
				controlType: 		'select',
				oneLine:			true,
				closeText:			fieldmaster._e('date_time_picker', 'selectText')
			};
			
			
			// add custom 'Close = Select' functionality
			args.onClose = function( value, instance ){
				
				// vars
				var $div = instance.dpDiv,
					$close = $div.find('.ui-datepicker-close');
				
				
				// if clicking close button
				if( !value && $close.is(':hover') ) {
					
					// attempt to find new value
					value = fieldmaster.maybe_get(instance, 'settings.timepicker.formattedTime');
					
					
					// bail early if no value
					if( !value ) return;
					
					
					// update value
					$.datepicker._setTime(instance);
					
				}
									
			};
			
			
			// filter for 3rd party customization
			args = fieldmaster.apply_filters('time_picker_args', args, this.$field);
			
			
			// add date picker
			this.$input.timepicker( args );
			
			
			// wrap the datepicker (only if it hasn't already been wrapped)
			if( $('body > #ui-datepicker-div').exists() ) {
			
				$('body > #ui-datepicker-div').wrap('<div class="fieldmaster-ui-datepicker" />');
				
			}
			
			
			// action for 3rd party customization
			fieldmaster.do_action('time_picker_init', this.$input, args, this.$field);
			
		},
		
		blur: function(){
			
			if( !this.$input.val() ) {
			
				this.$hidden.val('');
				
			}
			
		}
		
	});
	
})(jQuery);

(function($){
	
	fieldmaster.fields.true_false = fieldmaster.field.extend({
		
		type: 'true_false',
		$switch: null,
		$input: null,
		
		actions: {
			'prepare':	'render',
			'append':	'render',
			'show':		'render'
		},
		
		events: {
			'change .fieldmaster-switch-input': '_change',
			'focus .fieldmaster-switch-input': 	'_focus',
			'blur .fieldmaster-switch-input': 	'_blur',
			'keypress .fieldmaster-switch-input':	'_keypress'
		},
		
		
		/*
		*  focus
		*
		*  This function will setup variables when focused on a field
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		focus: function(){
			
			// vars
			this.$input = this.$field.find('.fieldmaster-switch-input');
			this.$switch = this.$field.find('.fieldmaster-switch');
			
		},
		
		
		/*
		*  render
		*
		*  This function is used to setup basic upload form attributes
		*
		*  @type	function
		*  @date	12/04/2016
		*  @since	5.3.8
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		render: function(){
			
			// bail ealry if no $switch
			if( !this.$switch.exists() ) return;
			
			
			// vars
			var $on = this.$switch.children('.fieldmaster-switch-on'),
				$off = this.$switch.children('.fieldmaster-switch-off')
				width = Math.max( $on.width(), $off.width() );
			
			
			// bail ealry if no width
			if( !width ) return;
			
			
			// set widths
			$on.css( 'min-width', width );
			$off.css( 'min-width', width );
				
		},
		
		
		/*
		*  on
		*
		*  description
		*
		*  @type	function
		*  @date	10/1/17
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		on: function() { //console.log('on');
			
			this.$input.prop('checked', true);
			this.$switch.addClass('-on');
			
		},
		
		
		/*
		*  off
		*
		*  description
		*
		*  @type	function
		*  @date	10/1/17
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		off: function() { //console.log('off');
			
			this.$input.prop('checked', false);
			this.$switch.removeClass('-on');
			
		},
		
		
		/*
		*  change
		*
		*  description
		*
		*  @type	function
		*  @date	12/10/16
		*  @since	5.4.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_change: function( e ){
			
			// vars
			var checked = e.$el.prop('checked');
			
			
			// enable
			if( checked ) {
				
				this.on();
			
			// disable	
			} else {
				
				this.off();
			
			}
					
		},
		
		
		/*
		*  _focus
		*
		*  description
		*
		*  @type	function
		*  @date	10/1/17
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_focus: function( e ){
			
			this.$switch.addClass('-focus');
			
		},
		
		
		/*
		*  _blur
		*
		*  description
		*
		*  @type	function
		*  @date	10/1/17
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_blur: function( e ){
			
			this.$switch.removeClass('-focus');
			
		},
		
		
		/*
		*  _keypress
		*
		*  description
		*
		*  @type	function
		*  @date	10/1/17
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_keypress: function( e ){
			
			// left
			if( e.keyCode === 37 ) {
				
				return this.off();
				
			}
			
			
			// right
			if( e.keyCode === 39 ) {
				
				return this.on();
				
			}
			
		}
	
	});

})(jQuery);

(function($){
	
	// taxonomy
	fieldmaster.fields.taxonomy = fieldmaster.field.extend({
		
		type: 'taxonomy',
		$el: null,
		
		actions: {
			'ready':	'render',
			'append':	'render',
			'remove':	'remove'
		},
		events: {
			'click a[data-name="add"]': 	'add_term'
		},
		
		focus: function(){
			
			// $el
			this.$el = this.$field.find('.fieldmaster-taxonomy-field');
			
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			
			
			// extra
			this.o.key = this.$field.data('key');
			
		},
		
		render: function(){
			
			// attempt select2
			var $select = this.$field.find('select');
			
			
			// bail early if no select field
			if( !$select.exists() ) return;
			
			
			// select2 options
			var args = fieldmaster.get_data( $select );
			
			
			// customize args
			args = fieldmaster.parse_args(args, {
				'pagination':	true,
				'ajax_action':	'fieldmaster/fields/taxonomy/query',
				'key':			this.o.key
			});
						
			
			// add select2
			fieldmaster.select2.init( $select, args );
			
		},
		
		remove: function(){
			
			// attempt select2
			var $select = this.$field.find('select');
			
			
			// validate ui
			if( !$select.exists() ) return false;
			
			
			// remove select2
			fieldmaster.select2.destroy( $select );
			
		},
		
		add_term: function( e ){
			
			// reference
			var self = this;
			
			
			// open popup
			fieldmaster.open_popup({
				title:		e.$el.attr('title') || e.$el.data('title'),
				loading:	true,
				height:		220
			});
			
			
			
			// AJAX data
			var ajax_data = fieldmaster.prepare_for_ajax({
				action:		'fieldmaster/fields/taxonomy/add_term',
				field_key:	this.o.key
			});
			
			
			
			// get HTML
			$.ajax({
				url:		fieldmaster.get('ajaxurl'),
				data:		ajax_data,
				type:		'post',
				dataType:	'html',
				success:	function(html){
				
					self.add_term_confirm( html );
					
				}
			});
			
			
		},
		
		add_term_confirm: function( html ){
			
			// reference
			var self = this;
			
			
			// update popup
			fieldmaster.update_popup({
				content : html
			});
			
			
			// focus
			$('#fieldmaster-popup input[name="term_name"]').focus();
			
			
			// events
			$('#fieldmaster-popup form').on('submit', function( e ){
				
				// prevent default
				e.preventDefault();
				
				
				// submit
				self.add_term_submit( $(this ));
				
			});
			
		},
		
		add_term_submit: function( $form ){
			
			// reference
			var self = this;
			
			
			// vars
			var $submit = $form.find('.fieldmaster-submit'),
				$name = $form.find('input[name="term_name"]'),
				$parent = $form.find('select[name="term_parent"]');
			
			
			// basic validation
			if( $name.val() === '' ) {
				
				$name.focus();
				return false;
				
			}
			
			
			// show loading
			$submit.find('button').attr('disabled', 'disabled');
			$submit.find('.fieldmaster-spinner').addClass('is-active');
			
			
			// vars
			var ajax_data = fieldmaster.prepare_for_ajax({
				action:			'fieldmaster/fields/taxonomy/add_term',
				field_key:		this.o.key,
				term_name:		$name.val(),
				term_parent:	$parent.exists() ? $parent.val() : 0
			});
			
			
			// save term
			$.ajax({
				url:		fieldmaster.get('ajaxurl'),
				data:		ajax_data,
				type:		'post',
				dataType:	'json',
				success:	function( json ){
					
					// vars
					var message = fieldmaster.get_ajax_message(json);
					
					
					// success
					if( fieldmaster.is_ajax_success(json) ) {
						
						// clear name
						$name.val('');
						
						
						// update term lists
						self.append_new_term( json.data );

					}
					
					
					// message
					if( message.text ) {
						
						$submit.find('span').html( message.text );
						
					}
					
				},
				complete: function(){
					
					// reset button
					$submit.find('button').removeAttr('disabled');
					
					
					// hide loading
					$submit.find('.fieldmaster-spinner').removeClass('is-active');
					
					
					// remove message
					$submit.find('span').delay(1500).fadeOut(250, function(){
						
						$(this).html('');
						$(this).show();
						
					});
					
					
					// focus
					$name.focus();
					
				}
			});
			
		},
		
		append_new_term: function( term ){
			
			// vars
			var item = {
				id:		term.term_id,
				text:	term.term_label
			}; 
			
			
			// append to all taxonomy lists
			$('.fieldmaster-taxonomy-field[data-taxonomy="' + this.o.taxonomy + '"]').each(function(){
				
				// vars
				var type = $(this).data('type');
				
				
				// bail early if not checkbox/radio
				if( type == 'radio' || type == 'checkbox' ) {
					
					// allow
					
				} else {
					
					return;
					
				}
				
				
				// vars
				var $hidden = $(this).children('input[type="hidden"]'),
					$ul = $(this).find('ul:first'),
					name = $hidden.attr('name');
				
				
				// allow multiple selection
				if( type == 'checkbox' ) {
					
					name += '[]';
						
				}
				
				
				// create new li
				var $li = $([
					'<li data-id="' + term.term_id + '">',
						'<label>',
							'<input type="' + type + '" value="' + term.term_id + '" name="' + name + '" /> ',
							'<span>' + term.term_label + '</span>',
						'</label>',
					'</li>'
				].join(''));
				
				
				// find parent
				if( term.term_parent ) {
					
					// vars
					var $parent = $ul.find('li[data-id="' + term.term_parent + '"]');
				
					
					// update vars
					$ul = $parent.children('ul');
					
					
					// create ul
					if( !$ul.exists() ) {
						
						$ul = $('<ul class="children fieldmaster-bl"></ul>');
						
						$parent.append( $ul );
						
					}
					
				}
				
				
				// append
				$ul.append( $li );

			});
			
			
			// append to select
			$('#fieldmaster-popup #term_parent').each(function(){
				
				// vars
				var $option = $('<option value="' + term.term_id + '">' + term.term_label + '</option>');
				
				if( term.term_parent ) {
					
					$(this).children('option[value="' + term.term_parent + '"]').after( $option );
					
				} else {
					
					$(this).append( $option );
					
				}
				
			});
			
			
			// set value
			switch( this.o.type ) {
				
				// select
				case 'select':
					
					//this.$el.children('input').select2('data', item);
					
					
					// vars
					var $select = this.$el.children('select');
					fieldmaster.select2.add_value($select, term.term_id, term.term_label);
					
					
					break;
				
				case 'multi_select':
					
/*
					// vars
					var $input = this.$el.children('input'),
						value = $input.select2('data') || [];
					
					
					// append
					value.push( item );
					
					
					// update
					$input.select2('data', value);
					
					
*/
					// vars
					var $select = this.$el.children('select');
					fieldmaster.select2.add_value($select, term.term_id, term.term_label);
					
					
					break;
				
				case 'checkbox':
				case 'radio':
					
					// scroll to view
					var $holder = this.$el.find('.categorychecklist-holder'),
						$li = $holder.find('li[data-id="' + term.term_id + '"]'),
						offet = $holder.get(0).scrollTop + ( $li.offset().top - $holder.offset().top );
					
					
					// check input
					$li.find('input').prop('checked', true);
					
					
					// scroll to bottom
					$holder.animate({scrollTop: offet}, '250');
					break;
				
			}
			
			
		}
	
	});
	
})(jQuery);

(function($){
	
	fieldmaster.fields.url = fieldmaster.field.extend({
		
		type: 'url',
		$input: null,
		
		actions: {
			'ready':	'render',
			'append':	'render'
			
		},
		
		events: {
			'keyup input[type="url"]': 'render'
		},
		
		focus: function(){
			
			this.$input = this.$field.find('input[type="url"]');
			
		},
		
		is_valid: function(){
			
			// vars
			var val = this.$input.val();
			
			
			if( val.indexOf('://') !== -1 ) {
				
				// url
				
			} else if( val.indexOf('//') === 0 ) {
				
				// protocol relative url
				
			} else {
				
				return false;
				
			}
			
			
			// return
			return true;
			
		},
		
		render: function(){
			
			// add class
			if( this.is_valid() ) {
				
				this.$input.parent().addClass('valid');
			
			// remove class	
			} else {
				
				this.$input.parent().removeClass('valid');
				
			}
			
			
		}
		
	});

})(jQuery);

(function($){
  
	fieldmaster.validation = fieldmaster.model.extend({
		
		actions: {
			'ready':	'ready',
			'append':	'ready'
		},
		
		filters: {
			'validation_complete':	'validation_complete'
		},
		
		events: {
			'click #save-post':				'click_ignore',
			'click [type="submit"]':		'click_publish',
			'submit form':					'submit_form',
			'click .fieldmaster-error-message a':	'click_message'
		},
		
		
		// vars
		active: 1,
		ignore: 0,
		busy: 0,
		valid: true,
		errors: [],
		
		
		// classes
		error_class: 'fieldmaster-error',
		message_class: 'fieldmaster-error-message',
		
		
		// el
		$trigger: null,
		
		
		/*
		*  ready
		*
		*  This function will add 'non bubbling' events
		*
		*  @type	function
		*  @date	26/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		ready: function( $el ){
			
			// reference
			$el.find('.fm-field input').filter('[type="number"], [type="email"], [type="url"]').on('invalid', function( e ){
				
				// prvent defual
				// fixes chrome bug where 'hidden-by-tab' field throws focus error
				e.preventDefault();
				
				
				// append to errors
				fieldmaster.validation.errors.push({
					input: $(this).attr('name'),
					message: e.target.validationMessage
				});
				
				
				// run validation
				fieldmaster.validation.fetch( $(this).closest('form') );
			
			});
			
		},
		
		
		/*
		*  validation_complete
		*
		*  This function will modify the JSON response and add local 'invalid' errors
		*
		*  @type	function
		*  @date	26/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		validation_complete: function( json, $form ) {
			
			// bail early if no local errors
			if( !this.errors.length ) return json;
			
			
			// set valid
			json.valid = 0;
			
			
			// require array
			json.errors = json.errors || [];
			
			
			// vars
			var inputs = [];
			
			
			// populate inputs
			if( json.errors.length ) {
				
				for( i in json.errors ) {
					
					inputs.push( json.errors[ i ].input );
									
				}
				
			}
			
			
			// append
			if( this.errors.length ) {
				
				for( i in this.errors ) {
					
					// vars
					var error = this.errors[ i ];
					
					
					// bail ealry if alreay exists
					if( $.inArray(error.input, inputs) !== -1 ) continue;
					
					
					// append
					json.errors.push( error );
					
				}
				
			}
			
			
			// reset
			this.errors = [];
			
			
			// return
			return json;
			
		},
		
		
		/*
		*  click_message
		*
		*  This function will dismiss the validation message
		*
		*  @type	function
		*  @date	26/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		click_message: function( e ) {
			
			e.preventDefault();
			
			fieldmaster.remove_el( e.$el.parent() );
			
		},
		
		
		/*
		*  click_ignore
		*
		*  This event is trigered via submit butons which ignore validation
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		click_ignore: function( e ) {
			
			this.ignore = 1;
			this.$trigger = e.$el;
			
		},
		
		
		/*
		*  click_publish
		*
		*  This event is trigered via submit butons which trigger validation
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		click_publish: function( e ) {
			
			this.$trigger = e.$el;
			
		},
		
		
		/*
		*  submit_form
		*
		*  description
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		submit_form: function( e ){
			
			// bail early if not active
			if( !this.active ) {
			
				return true;
				
			}
			
			
			// ignore validation (only ignore once)
			if( this.ignore ) {
			
				this.ignore = 0;
				return true;
				
			}
			
			
			// bail early if this form does not contain FieldMaster data
			if( !e.$el.find('#fieldmaster-form-data').exists() ) {
			
				return true;
				
			}
				
			
			// bail early if is preview
			var $preview = e.$el.find('#wp-preview');
			if( $preview.exists() && $preview.val() ) {
				
				// WP will lock form, unlock it
				this.toggle( e.$el, 'unlock' );
				return true;
				
			}
			
			
			// prevent default
			e.preventDefault();
			
			
			// run validation
			this.fetch( e.$el );
			
		},
		
		
		/*
		*  lock
		*
		*  description
		*
		*  @type	function
		*  @date	7/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		toggle: function( $form, state ){
			
			// defaults
			state = state || 'unlock';
			
			
			// debug
			//console.log('toggle %o, %o %o', this.$trigger, $form, state);
			
			// vars
			var $submit = null,
				$spinner = null,
				$parent = $('#submitdiv');
			
			
			// 3rd party publish box
			if( !$parent.exists() ) {
				
				$parent = $('#submitpost');
				
			}
			
			
			// term, user
			if( !$parent.exists() ) {
				
				$parent = $form.find('p.submit').last();
				
			}
			
			
			// front end form
			if( !$parent.exists() ) {
				
				$parent = $form.find('.fieldmaster-form-submit');
				
			}
			
			
			// default
			if( !$parent.exists() ) {
				
				$parent = $form;
					
			}
			
			
			// find elements
			// note: media edit page does not use .button, this is why we need to look for generic input[type="submit"]
			$submit = $parent.find('input[type="submit"], .button');
			$spinner = $parent.find('.spinner, .fieldmaster-spinner');
			
			
			// hide all spinners (hides the preview spinner)
			this.hide_spinner( $spinner );
			
			
			// unlock
			if( state == 'unlock' ) {
				
				this.enable_submit( $submit );
				
			// lock
			} else if( state == 'lock' ) {
				
				// show only last spinner (allow all spinners to be hidden - preview spinner + submit spinner)
				this.disable_submit( $submit );
				this.show_spinner( $spinner.last() );
				
			}
			
		},
		
		
		/*
		*  fetch
		*
		*  description
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		fetch: function( $form ){
			
			// bail aelry if already busy
			if( this.busy ) return false;
			
			
			// reference
			var self = this;
			
			
			// action for 3rd party
			fieldmaster.do_action('validation_begin');
				
				
			// vars
			var data = fieldmaster.serialize_form($form);
				
			
			// append AJAX action		
			data.action = 'fieldmaster/validate_save_post';
			
			
			// prepare
			data = fieldmaster.prepare_for_ajax(data);
			
			
			// set busy
			this.busy = 1;
			
			
			// lock form
			this.toggle( $form, 'lock' );
			
			
			// ajax
			$.ajax({
				url: fieldmaster.get('ajaxurl'),
				data: data,
				type: 'post',
				dataType: 'json',
				success: function( json ){
					
					// bail early if not json success
					if( !fieldmaster.is_ajax_success(json) ) {
						
						return;
						
					}
					
					
					self.fetch_success( $form, json.data );
					
				},
				complete: function(){
					
					self.fetch_complete( $form );
			
				}
			});
			
		},
		
		
		/*
		*  fetch_complete
		*
		*  description
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		fetch_complete: function( $form ){
			
			// set busy
			this.busy = 0;
			
			
			// unlock so WP can publish form
			this.toggle( $form, 'unlock' );
			
			
			// bail early if validationw as not valid
			if( !this.valid ) return;
			
			
			
			
			// update ignore (allow form submit to not run validation)
			this.ignore = 1;
				
				
			// remove previous error message
			var $message = $form.children('.fieldmaster-error-message');
			
			if( $message.exists() ) {
				
				$message.addClass('-success');
				$message.children('p').html( fieldmaster._e('validation_successful') );
				
				
				// remove message
				setTimeout(function(){
					
					fieldmaster.remove_el( $message );
					
				}, 2000);
				
			}
			
		
			// remove hidden postboxes (this will stop them from being posted to save)
			$form.find('.fieldmaster-postbox.fieldmaster-hidden').remove();
			
			
			// action for 3rd party customization
			fieldmaster.do_action('submit', $form);
			
			
			// submit form again
			if( this.$trigger ) {
				
				this.$trigger.click();
			
			} else {
				
				$form.submit();
			
			}
			
			
			// lock form
			this.toggle( $form, 'lock' );
			
		},
		
		
		/*
		*  fetch_success
		*
		*  description
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		fetch_success: function( $form, json ){
			
			// filter for 3rd party customization
			json = fieldmaster.apply_filters('validation_complete', json, $form);
						
			
			// validate json
			if( !json || json.valid || !json.errors ) {
				
				// set valid (allows fetch_complete to run)
				this.valid = true;
				
				
				// action for 3rd party
				fieldmaster.do_action('validation_success');
			
				
				// end function
				return;
				
			}
			
			
			// action for 3rd party
			fieldmaster.do_action('validation_failure');
			
			
			// set valid (prevents fetch_complete from runing)
			this.valid = false;
			
			
			// reset trigger
			this.$trigger = null;
			
			
			// vars
			var $scrollTo = null,
				count = 0,
				message = fieldmaster._e('validation_failed');
			
			
			// show field error messages
			if( json.errors && json.errors.length > 0 ) {
				
				for( var i in json.errors ) {
					
					// get error
					var error = json.errors[ i ];
					
					
					// is error for a specific field?
					if( !error.input ) {
						
						// update message
						message += '. ' + error.message;
						
						
						// ignore following functionality
						continue;
						
					}
					
					
					// get input
					var $input = $form.find('[name="' + error.input + '"]').first();
					
					
					// if $_POST value was an array, this $input may not exist
					if( !$input.exists() ) {
						
						$input = $form.find('[name^="' + error.input + '"]').first();
						
					}
					
					
					// bail early if input doesn't exist
					if( !$input.exists() ) continue;
					
					
					// increase
					count++;
					
					
					// now get field
					var $field = fieldmaster.get_field_wrap( $input );
					
					
					// add error
					this.add_error( $field, error.message );
					
					
					// set $scrollTo
					if( $scrollTo === null ) {
						
						$scrollTo = $field;
						
					}
					
				}
				
				
				// message
				if( count == 1 ) {
					
					message += '. ' + fieldmaster._e('validation_failed_1');
					
				} else if( count > 1 ) {
					
					message += '. ' + fieldmaster._e('validation_failed_2').replace('%d', count);
					
				}
			
			}
			
				
			// get $message
			var $message = $form.children('.fieldmaster-error-message');
			
			if( !$message.exists() ) {
				
				$message = $('<div class="fieldmaster-error-message"><p></p><a href="#" class="fieldmaster-icon -cancel small"></a></div>');
				
				$form.prepend( $message );
				
			}
			
			
			// update message
			$message.children('p').html( message );
			
			
			// if no $scrollTo, set to message
			if( $scrollTo === null ) {
				
				$scrollTo = $message;
				
			}
			
			
			// timeout avoids flicker jump
			setTimeout(function(){
				
				$("html, body").animate({ scrollTop: $scrollTo.offset().top - ( $(window).height() / 2 ) }, 500);
				
			}, 1);
			
		},
		
		
		/*
		*  add_error
		*
		*  This function will add error markup to a field
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$field (jQuery)
		*  @param	message (string)
		*  @return	n/a
		*/
		
		add_error: function( $field, message ){
			
			// reference
			var self = this;
			
			
			// add class
			$field.addClass(this.error_class);
			
			
			// add message
			if( message !== undefined ) {
				
				$field.children('.fieldmaster-input').children('.' + this.message_class).remove();
				$field.children('.fieldmaster-input').prepend('<div class="' + this.message_class + '"><p>' + message + '</p></div>');
			
			}
			
			
			// add event
			var event = function(){
				
				// remove error
				self.remove_error( $field );
			
				
				// remove self
				$field.off('focus change', 'input, textarea, select', event);
				
			}
			
			$field.on('focus change', 'input, textarea, select', event);
			
			
			// hook for 3rd party customization
			fieldmaster.do_action('add_field_error', $field);
			
		},
		
		
		/*
		*  remove_error
		*
		*  This function will remove error markup from a field
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$field (jQuery)
		*  @return	n/a
		*/
		
		remove_error: function( $field ){
			
			// var
			var $message = $field.children('.fieldmaster-input').children('.' + this.message_class);
			
			
			// remove class
			$field.removeClass(this.error_class);
			
			
			// remove message
			setTimeout(function(){
				
				fieldmaster.remove_el( $message );
				
			}, 250);
			
			
			// hook for 3rd party customization
			fieldmaster.do_action('remove_field_error', $field);
			
		},
		
		
		/*
		*  add_warning
		*
		*  This functino will add and auto remove an error message to a field
		*
		*  @type	function
		*  @date	4/05/2015
		*  @since	5.2.3
		*
		*  @param	$field (jQuery)
		*  @param	message (string)
		*  @return	n/a
		*/
		
		add_warning: function( $field, message ){
			
			this.add_error( $field, message );
			
			setTimeout(function(){
				
				fieldmaster.validation.remove_error( $field )
				
			}, 1000);
			
		},
		
		
		/*
		*  show_spinner
		*
		*  This function will show a spinner element. Logic changed in WP 4.2
		*
		*  @type	function
		*  @date	3/05/2015
		*  @since	5.2.3
		*
		*  @param	$spinner (jQuery)
		*  @return	n/a
		*/
		
		show_spinner: function( $spinner ){
			
			// bail early if no spinner
			if( !$spinner.exists() ) {
				
				return;
				
			}
			
			
			// vars
			var wp_version = fieldmaster.get('wp_version');
			
			
			// show
			if( parseFloat(wp_version) >= 4.2 ) {
				
				$spinner.addClass('is-active');
			
			} else {
				
				$spinner.css('display', 'inline-block');
			
			}
			
		},
		
		
		/*
		*  hide_spinner
		*
		*  This function will hide a spinner element. Logic changed in WP 4.2
		*
		*  @type	function
		*  @date	3/05/2015
		*  @since	5.2.3
		*
		*  @param	$spinner (jQuery)
		*  @return	n/a
		*/
		
		hide_spinner: function( $spinner ){
			
			// bail early if no spinner
			if( !$spinner.exists() ) {
				
				return;
				
			}
			
			
			// vars
			var wp_version = fieldmaster.get('wp_version');
			
			
			// hide
			if( parseFloat(wp_version) >= 4.2 ) {
				
				$spinner.removeClass('is-active');
			
			} else {
				
				$spinner.css('display', 'none');
			
			}
			
		},
		
		
		/*
		*  disable_submit
		*
		*  This function will disable the $trigger is possible
		*
		*  @type	function
		*  @date	3/05/2015
		*  @since	5.2.3
		*
		*  @param	$spinner (jQuery)
		*  @return	n/a
		*/
		
		disable_submit: function( $submit ){
			
			// bail early if no submit
			if( !$submit.exists() ) {
				
				return;
				
			}
			
			
			// add class
			$submit.addClass('disabled button-disabled button-primary-disabled');
			
		},
		
		
		/*
		*  enable_submit
		*
		*  This function will enable the $trigger is possible
		*
		*  @type	function
		*  @date	3/05/2015
		*  @since	5.2.3
		*
		*  @param	$spinner (jQuery)
		*  @return	n/a
		*/
		
		enable_submit: function( $submit ){
			
			// bail early if no submit
			if( !$submit.exists() ) {
				
				return;
				
			}
			
			
			// remove class
			$submit.removeClass('disabled button-disabled button-primary-disabled');
			
		}
		
	});

})(jQuery);

(function($){
	
	fieldmaster.fields.wysiwyg = fieldmaster.field.extend({
		
		type: 'wysiwyg',
		$el: null,
		$textarea: null,
		toolbars: {},
		
		events: {
			'mousedown .fieldmaster-editor-wrap.delay': 'mousedown'
		},
		
		actions: {
			'load':			'initialize',
			'append':		'initialize',
			'remove':		'disable',
			'sortstart':	'disable',
			'sortstop':		'enable'
		},
		
		focus: function(){
			
			// get elements
			this.$el = this.$field.find('.wp-editor-wrap').last();
			this.$textarea = this.$el.find('textarea');
			
			// get options
			this.o = fieldmaster.get_data( this.$el );
			this.o.id = this.$textarea.attr('id');
			
		},
		
		mousedown: function(e) {
			
			// prevent default
			e.preventDefault();
			
			
			// remove delay class
			this.$el.removeClass('delay');
			this.$el.find('.fieldmaster-editor-toolbar').remove();
			
			
			// initialize
			this.initialize();
			
		},
		
		initialize: function(){
			
			// bail early if delay
			if( this.$el.hasClass('delay') ) return;
			
			
			// bail early if no tinyMCEPreInit (needed by both tinymce and quicktags)
			if( typeof tinyMCEPreInit === 'undefined' ) return;
			
			
			// generate new id
			var old_id = this.o.id,
				new_id = fieldmaster.get_uniqid('fieldmaster-editor-'),
				html = this.$el.outerHTML();
			
			
			// replace
			html = fieldmaster.str_replace( old_id, new_id, html );
			
			
			// swap
			this.$el.replaceWith( html );			
			
						
			// update id
			this.o.id = new_id
			
			
			// initialize
			this.initialize_tinymce();
			this.initialize_quicktags();
			
		},
		
		initialize_tinymce: function(){
			
			// bail early if no tinymce
			if( typeof tinymce === 'undefined' ) return;
			
			
			// bail early if no tinyMCEPreInit.mceInit
			if( typeof tinyMCEPreInit.mceInit === 'undefined' ) return;
			
			
			// vars
			var mceInit = this.get_mceInit();
			
			
			// append
			tinyMCEPreInit.mceInit[ mceInit.id ] = mceInit;
			
			
			// bail early if not visual active
			if( !this.$el.hasClass('tmce-active') ) return;
			
			
			// initialize
			try {
				
				// init
				tinymce.init( mceInit );
				
				
				// vars
				var ed = tinyMCE.get( mceInit.id );
				
				
				// action for 3rd party customization
				fieldmaster.do_action('wysiwyg_tinymce_init', ed, ed.id, mceInit, this.$field);
				
			} catch(e){}
			
		},
		
		initialize_quicktags: function(){
			
			// bail early if no quicktags
			if( typeof quicktags === 'undefined' ) return;
			
			
			// bail early if no tinyMCEPreInit.qtInit
			if( typeof tinyMCEPreInit.qtInit === 'undefined' ) return;
			
			
			// vars
			var qtInit = this.get_qtInit();
			
			
			// append
			tinyMCEPreInit.qtInit[ qtInit.id ] = qtInit;
			
			
			// initialize
			try {
				
				// init
				var qtag = quicktags( qtInit );
				
				
				// buttons
				this._buttonsInit( qtag );
				
				
				// action for 3rd party customization
				fieldmaster.do_action('wysiwyg_quicktags_init', qtag, qtag.id, qtInit, this.$field);
				
			} catch(e){}
			
		},
		
		get_mceInit : function(){
			
			// reference
			var $field = this.$field;
				
				
			// vars
			var toolbar = this.get_toolbar( this.o.toolbar ),
				mceInit = $.extend({}, tinyMCEPreInit.mceInit.fieldmaster_content);
			
			
			// selector
			mceInit.selector = '#' + this.o.id;
			
			
			// id
			mceInit.id = this.o.id; // tinymce v4
			mceInit.elements = this.o.id; // tinymce v3
			
			
			// toolbar
			if( toolbar ) {
				
				var k = (tinymce.majorVersion < 4) ? 'theme_advanced_buttons' : 'toolbar';
				
				for( var i = 1; i < 5; i++ ) {
					
					mceInit[ k + i ] = fieldmaster.isset(toolbar, i) ? toolbar[i] : '';
					
				}
				
			}
			
			
			// events
			if( tinymce.majorVersion < 4 ) {
				
				mceInit.setup = function( ed ){
					
					ed.onInit.add(function(ed, event) {
						
						// focus
						$(ed.getBody()).on('focus', function(){
					
							fieldmaster.validation.remove_error( $field );
							
						});
						
						$(ed.getBody()).on('blur', function(){
							
							// update the hidden textarea
							// - This fixes a bug when adding a taxonomy term as the form is not posted and the hidden textarea is never populated!
			
							// save to textarea	
							ed.save();
							
							
							// trigger change on textarea
							$field.find('textarea').trigger('change');
							
						});
					
					});
					
				};
			
			} else {
			
				mceInit.setup = function( ed ){
					
					ed.on('focus', function(e) {
				
						fieldmaster.validation.remove_error( $field );
						
					});
					
					ed.on('change', function(e) {
						
						// save to textarea	
						ed.save();
						
						
						$field.find('textarea').trigger('change');
						
					});
					
/*
					ed.on('blur', function(e) {
						
						// update the hidden textarea
						// - This fixes a but when adding a taxonomy term as the form is not posted and the hidden textarea is never populated!
		
						// save to textarea	
						ed.save();
						
						
						// trigger change on textarea
						$field.find('textarea').trigger('change');
						
					});
*/
					
					/*
ed.on('ResizeEditor', function(e) {
					    // console.log(e);
					});
*/
					
				};
			
			}
			
			
			// disable wp_autoresize_on (no solution yet for fixed toolbar)
			mceInit.wp_autoresize_on = false;
			
			
			// hook for 3rd party customization
			mceInit = fieldmaster.apply_filters('wysiwyg_tinymce_settings', mceInit, mceInit.id, this.$field);
			
			
			// return
			return mceInit;
			
		},
		
		get_qtInit : function(){
				
			// vars
			var qtInit = $.extend({}, tinyMCEPreInit.qtInit.fieldmaster_content);
			
			
			// id
			qtInit.id = this.o.id;
			
			
			// hook for 3rd party customization
			qtInit = fieldmaster.apply_filters('wysiwyg_quicktags_settings', qtInit, qtInit.id, this.$field);
			
			
			// return
			return qtInit;
			
		},
		
		/*
		*  disable
		*
		*  This function will disable the tinymce for a given field
		*  Note: txtarea_el is different from $textarea.val() and is the value that you see, not the value that you save.
		*        this allows text like <--more--> to wok instead of showing as an image when the tinymce is removed
		*
		*  @type	function
		*  @date	1/08/2014
		*  @since	5.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		disable: function(){
			
			try {
				
				// vars
				var ed = tinyMCE.get( this.o.id )
					
				
				// save
				ed.save();
				
				
				// destroy editor
				ed.destroy();
								
			} catch(e) {}
			
		},
		
		enable: function(){
			
			try {
				
				// bail early if html mode
				if( this.$el.hasClass('tmce-active') ) {
					
					switchEditors.go( this.o.id, 'tmce');
					
				}
								
			} catch(e) {}
			
		},
		
		get_toolbar : function( name ){
			
			// bail early if toolbar doesn't exist
			if( typeof this.toolbars[ name ] !== 'undefined' ) {
				
				return this.toolbars[ name ];
				
			}
			
			
			// return
			return false;
			
		},
		
		
		/*
		*  _buttonsInit
		*
		*  This function will add the quicktags HTML to a WYSIWYG field. Normaly, this is added via quicktags on document ready,
		*  however, there is no support for 'append'. Source: wp-includes/js/quicktags.js:245
		*
		*  @type	function
		*  @date	1/08/2014
		*  @since	5.0.0
		*
		*  @param	ed (object) quicktag object
		*  @return	n/a
		*/
		
		_buttonsInit: function( ed ) {
			var defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,';
	
			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';

			// set buttons
			if ( settings.buttons ) {
				use = ','+settings.buttons+',';
			}

			for ( i in edButtons ) {
				if ( !edButtons[i] ) {
					continue;
				}

				id = edButtons[i].id;
				if ( use && defaults.indexOf( ',' + id + ',' ) !== -1 && use.indexOf( ',' + id + ',' ) === -1 ) {
					continue;
				}

				if ( !edButtons[i].instance || edButtons[i].instance === inst ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html ) {
						html += edButtons[i].html(name + '_');
					}
				}
			}

			if ( use && use.indexOf(',fullscreen,') !== -1 ) {
				theButtons.fullscreen = new qt.FullscreenButton();
				html += theButtons.fullscreen.html(name + '_');
			}


			if ( 'rtl' === document.getElementsByTagName('html')[0].dir ) {
				theButtons.textdirection = new qt.TextDirectionButton();
				html += theButtons.textdirection.html(name + '_');
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;
			
		}
		
	});
	
	
	/*
	*  wysiwyg_manager
	*
	*  This model will handle validation of fields within a tab group
	*
	*  @type	function
	*  @date	25/11/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	var fieldmaster_content = fieldmaster.model.extend({
		
		$div: null,
		
		actions: {
			'ready': 'ready'
		},
		
		ready: function(){

			// vars
			this.$div = $('#fieldmaster-hidden-wp-editor');
			
			
			// bail early if doesn't exist
			if( !this.$div.exists() ) return;
			
			
			// move to footer
			this.$div.appendTo('body');
			
			
			// bail early if no tinymce
			if( !fieldmaster.isset(window,'tinymce','on') ) return;
			
			
			// restore default activeEditor
			tinymce.on('AddEditor', function( data ){
				
				// vars
				var editor = data.editor;
				
				
				// bail early if not 'fieldmaster'
				if( editor.id.substr(0, 3) !== 'fieldmaster' ) return;
				
				
				// override if 'content' exists
				editor = tinymce.editors.content || editor;
				
				
				// update vars
				tinymce.activeEditor = editor;
				wpActiveEditor = editor.id;
				
			});
			
		}
		
	});

})(jQuery);

// @codekit-prepend "../js/event-manager.js";
// @codekit-prepend "../js/fieldmaster.js";
// @codekit-prepend "../js/fieldmaster-ajax.js";
// @codekit-prepend "../js/fieldmaster-checkbox.js";
// @codekit-prepend "../js/fieldmaster-color-picker.js";
// @codekit-prepend "../js/fieldmaster-conditional-logic.js";
// @codekit-prepend "../js/fieldmaster-date-picker.js";
// @codekit-prepend "../js/fieldmaster-date-time-picker.js";
// @codekit-prepend "../js/fieldmaster-file.js";
// @codekit-prepend "../js/fieldmaster-google-map.js";
// @codekit-prepend "../js/fieldmaster-image.js";
// @codekit-prepend "../js/fieldmaster-media.js";
// @codekit-prepend "../js/fieldmaster-oembed.js";
// @codekit-prepend "../js/fieldmaster-radio.js";
// @codekit-prepend "../js/fieldmaster-relationship.js";
// @codekit-prepend "../js/fieldmaster-select2.js";
// @codekit-prepend "../js/fieldmaster-select.js";
// @codekit-prepend "../js/fieldmaster-tab.js";
// @codekit-prepend "../js/fieldmaster-time-picker.js";
// @codekit-prepend "../js/fieldmaster-true-false.js";
// @codekit-prepend "../js/fieldmaster-taxonomy.js";
// @codekit-prepend "../js/fieldmaster-url.js";
// @codekit-prepend "../js/fieldmaster-validation.js";
// @codekit-prepend "../js/fieldmaster-wysiwyg.js";

