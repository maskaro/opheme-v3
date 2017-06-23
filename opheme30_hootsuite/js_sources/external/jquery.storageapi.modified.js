/*
 * jQuery Storage API Plugin
 *
 * Copyright (c) 2013 Julien Maurel
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 * https://github.com/julien-maurel/jQuery-Storage-API
 *
 * Version: 1.7.3
 *
 * Modified by Razvan-Ioan Dinita
 *
 */
(
	function ( factory )
	{
		if ( typeof define === 'function' && define.amd ) {
			// AMD
			define ( [ 'jquery' ], factory );
		}
		else if ( typeof exports === 'object' ) {
			// CommonJS
			factory ( require ( 'jquery' ) );
		}
		else {
			// Browser globals
			factory ( jQuery );
		}
	} (
		function ( $ )
		{

			// Get items from a storage
			function _get ( storage )
			{
				var l  = arguments.length,
				    s  = $.customStorage,
				    a  = arguments,
				    a1 = a[ 1 ],
				    vi, ret, tmp;

				if ( l < 2 ) {
					throw new Error ( 'Minimum 2 arguments must be given' );
				}
				else if ( $.isArray ( a1 ) ) {
					// If second argument is an array, return an object with value of storage for each item in this array
					ret = {};
					for ( var i in a1 ) {
						vi = a1[ i ];
						try {
							ret[ vi ] = JSON.parse ( s.getItem ( vi ) );
						}
						catch ( e ) {
							ret[ vi ] = s.getItem ( vi );
						}
					}
					return ret;
				}
				else if ( l == 2 ) {
					// If only 2 arguments, return value directly
					try {
						return JSON.parse ( s.getItem ( a1 ) );
					}
					catch ( e ) {
						return s.getItem ( a1 );
					}
				}
				else {
					// If more than 2 arguments, parse storage to retrieve final value to return it
					// Get first level
					try {
						ret = JSON.parse ( s.getItem ( a1 ) );
					}
					catch ( e ) {
						throw new ReferenceError ( a1 + ' is not defined in this storage' );
					}
					// Parse next levels
					for ( var i = 2; i < l - 1; i++ ) {
						ret = ret[ a[ i ] ];
						if ( ret === undefined ) {
							throw new ReferenceError ( [].slice.call ( a, 1, i + 1 ).join ( '.' ) + ' is not defined in this storage' );
						}
					}
					// If last argument is an array, return an object with value for each item in this array
					// Else return value normally
					if ( $.isArray ( a[ i ] ) ) {
						tmp = ret;
						ret = {};
						for ( var j in a[ i ] ) {
							ret[ a[ i ][ j ] ] = tmp[ a[ i ][ j ] ];
						}
						return ret;
					}
					else {
						return ret[ a[ i ] ];
					}
				}
			}

			// Set items of a storage
			function _set ( storage )
			{
				var l            = arguments.length,
				    s            = $.customStorage,
				    a            = arguments,
				    a1           = a[ 1 ],
				    a2           = a[ 2 ],
				    vi, to_store = {},
				    tmp;
				if ( l < 2 || !$.isPlainObject ( a1 ) && l < 3 ) {
					throw new Error ( 'Minimum 3 arguments must be given or second parameter must be an object' );
				}
				else if ( $.isPlainObject ( a1 ) ) {
					// If first argument is an object, set values of storage for each property of this object
					for ( var i in a1 ) {
						vi = a1[ i ];
						if ( !$.isPlainObject ( vi ) ) {
							s.setItem ( i, vi );
						}
						else {
							s.setItem ( i, JSON.stringify ( vi ) );
						}
					}
					return a1;
				}
				else if ( l == 3 ) {
					// If only 3 arguments, set value of storage directly
					if ( typeof a2 === 'object' ) {
						s.setItem ( a1, JSON.stringify ( a2 ) );
					}
					else {
						s.setItem ( a1, a2 );
					}
					return a2;
				}
				else {
					// If more than 3 arguments, parse storage to retrieve final node and set value
					// Get first level
					try {
						tmp = s.getItem ( a1 );
						if ( tmp != null ) {
							to_store = JSON.parse ( tmp );
						}
					}
					catch ( e ) {}
					tmp = to_store;
					// Parse next levels and set value
					for ( var i = 2; i < l - 2; i++ ) {
						vi = a[ i ];
						if ( !tmp[ vi ] || !$.isPlainObject ( tmp[ vi ] ) ) {
							tmp[ vi ] = {};
						}
						tmp = tmp[ vi ];
					}
					tmp[ a[ i ] ] = a[ i + 1 ];
					s.setItem ( a1, JSON.stringify ( to_store ) );
					return to_store;
				}
			}

			// Remove items from a storage
			function _remove ( storage )
			{
				var l  = arguments.length,
				    s  = $.customStorage,
				    a  = arguments,
				    a1 = a[ 1 ],
				    to_store, tmp;
				if ( l < 2 ) {
					throw new Error ( 'Minimum 2 arguments must be given' );
				}
				else if ( $.isArray ( a1 ) ) {
					// If first argument is an array, remove values from storage for each item of this array
					for ( var i in a1 ) {
						s.removeItem ( a1[ i ] );
					}
					return true;
				}
				else if ( l == 2 ) {
					// If only 2 arguments, remove value from storage directly
					s.removeItem ( a1 );
					return true;
				}
				else {
					// If more than 2 arguments, parse storage to retrieve final node and remove value
					// Get first level
					try {
						to_store = tmp = JSON.parse ( s.getItem ( a1 ) );
					}
					catch ( e ) {
						throw new ReferenceError ( a1 + ' is not defined in this storage' );
					}
					// Parse next levels and remove value
					for ( var i = 2; i < l - 1; i++ ) {
						tmp = tmp[ a[ i ] ];
						if ( tmp === undefined ) {
							throw new ReferenceError ( [].slice.call ( a, 1, i ).join ( '.' ) + ' is not defined in this storage' );
						}
					}
					// If last argument is an array,remove value for each item in this array
					// Else remove value normally
					if ( $.isArray ( a[ i ] ) ) {
						for ( var j in a[ i ] ) {
							delete tmp[ a[ i ][ j ] ];
						}
					}
					else {
						delete tmp[ a[ i ] ];
					}
					s.setItem ( a1, JSON.stringify ( to_store ) );
					return true;
				}
			}

			// Remove all items from a storage
			function _removeAll ( storage )
			{
				var keys = _keys ( storage );
				for ( var i in keys ) {
					_remove ( storage, keys[ i ] );
				}
			}

			// Check if items of a storage are empty
			function _isEmpty ( storage )
			{
				var l  = arguments.length,
				    a  = arguments,
				    s  = $.customStorage,
				    a1 = a[ 1 ];
				if ( l == 1 ) {
					// If only one argument, test if storage is empty
					return (
					_keys ( storage ).length == 0
					);
				}
				else if ( $.isArray ( a1 ) ) {
					// If first argument is an array, test each item of this array and return true only if all items are empty
					for ( var i = 0; i < a1.length; i++ ) {
						if ( !_isEmpty ( storage, a1[ i ] ) ) {
							return false;
						}
					}
					return true;
				}
				else {
					// If more than 1 argument, try to get value and test it
					try {
						var v = _get.apply ( this, arguments );
						// Convert result to an object (if last argument is an array, _get return already an object) and test each item
						if ( !$.isArray ( a[ l - 1 ] ) ) {
							v = {
								'totest': v
							};
						}
						for ( var i in v ) {
							if ( !(
								(
								$.isPlainObject ( v[ i ] ) && $.isEmptyObject ( v[ i ] )
								) ||
								(
								$.isArray ( v[ i ] ) && !v[ i ].length
								) ||
								(
									!v[ i ]
								)
								) ) {
								return false;
							}
						}
						return true;
					}
					catch ( e ) {
						return true;
					}
				}
			}

			// Check if items of a storage exist
			function _isSet ( storage )
			{
				var l  = arguments.length,
				    a  = arguments,
				    s  = $.customStorage,
				    a1 = a[ 1 ];
				if ( l < 2 ) {
					throw new Error ( 'Minimum 2 arguments must be given' );
				}
				if ( $.isArray ( a1 ) ) {
					// If first argument is an array, test each item of this array and return true only if all items exist
					for ( var i = 0; i < a1.length; i++ ) {
						if ( !_isSet ( storage, a1[ i ] ) ) {
							return false;
						}
					}
					return true;
				}
				else {
					// For other case, try to get value and test it
					try {
						var v = _get.apply ( this, arguments );
						// Convert result to an object (if last argument is an array, _get return already an object) and test each item
						if ( !$.isArray ( a[ l - 1 ] ) ) {
							v = {
								'totest': v
							};
						}
						for ( var i in v ) {
							if ( !(
								v[ i ] !== undefined && v[ i ] !== null
								) ) {
								return false;
							}
						}
						return true;
					}
					catch ( e ) {
						return false;
					}
				}
			}

			// Get keys of a storage or of an item of the storage
			function _keys ( storage )
			{
				var l    = arguments.length,
				    s    = $.customStorage,
				    a    = arguments,
				    a1   = a[ 1 ],
				    keys = [],
				    o    = {};
				// If more than 1 argument, get value from storage to retrieve keys
				// Else, use storage to retrieve keys
				if ( l > 1 ) {
					o = _get.apply ( this, a );
				}
				else {
					o = s;
				}
				for ( var i in o ) {
					keys.push ( i );
				}
				return keys;
			}

			var storage = {
				_type      : '',
				_ns        : null,
				_callMethod: function ( f, a )
				{
					var p  = [ this._type ],
					    a  = Array.prototype.slice.call ( a ),
					    a0 = a[ 0 ];
					if ( this._ns ) {
						p.push ( this._ns );
					}
					if ( typeof a0 === 'string' && a0.indexOf ( '.' ) !== -1 ) {
						a.shift ();
						[].unshift.apply ( a, a0.split ( '.' ) );
					}
					[].push.apply ( p, a );
					return f.apply ( this, p );
				},
				// Get items. If no parameters and storage have a namespace, return all namespace
				get        : function ()
				{
					return this._callMethod ( _get, arguments );
				},
				// Set items
				set        : function ()
				{
					var l  = arguments.length,
					    a  = arguments,
					    a0 = a[ 0 ];
					if ( l < 1 || !$.isPlainObject ( a0 ) && l < 2 ) {
						throw new Error ( 'Minimum 2 arguments must be given or first parameter must be an object' );
					}
					// If first argument is an object and storage is a namespace storage, set values individually
					if ( $.isPlainObject ( a0 ) && this._ns ) {
						for ( var i in a0 ) {
							_set ( this._type, this._ns, i, a0[ i ] );
						}
						return a0;
					}
					else {
						var r = this._callMethod ( _set, a );
						if ( this._ns ) {
							return r[ a0.split ( '.' )[ 0 ] ];
						}
						else {
							return r;
						}
					}
				},
				// Delete items
				remove     : function ()
				{
					if ( arguments.length < 1 ) {
						throw new Error ( 'Minimum 1 argument must be given' );
					}
					return this._callMethod ( _remove, arguments );
				},
				// Delete all items
				removeAll  : function ()
				{
					if ( this._ns ) {
						_set ( this._type, this._ns, {} );
						return true;
					}
					else {
						return _removeAll ( this._type );
					}
				},
				// Items empty
				isEmpty    : function ()
				{
					return this._callMethod ( _isEmpty, arguments );
				},
				// Items exists
				isSet      : function ()
				{
					if ( arguments.length < 1 ) {
						throw new Error ( 'Minimum 1 argument must be given' );
					}
					return this._callMethod ( _isSet, arguments );
				},
				// Get keys of items
				keys       : function ()
				{
					return this._callMethod ( _keys, arguments );
				}
			};

			if ( !window.name ) {
				window.name = Math.floor ( Math.random () * 100000000 );
			}

			$.customStorage = $.extend (
				{}, storage, {
					_type     : 'custom',
					container : {},
					setItem   : function ( n, v )
					{
						this.container[ n ] = v;
						return this;
					},
					getItem   : function ( n )
					{
						return this.container[ n ];
					},
					removeItem: function ( n )
					{
						delete this.container[ n ];
						return this;
					},
				}
			);

			// Remove all items in all storages
			$.removeAllStorages = function ()
			{
				if ( $.customStorage ) {
					$.customStorage.removeAll ();
				}
			}
		}
	)
);