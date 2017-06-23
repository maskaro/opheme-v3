(
	function ()
	{

		String.prototype.toCamelCase = function ( firstLetterUpper )
		{
			var capitalizeFirst = firstLetterUpper || false,
			    str             = (
				                      capitalizeFirst ? this[ 0 ].toUpperCase () : this[ 0 ].toLowerCase ()
			                      ) + this.substr ( 1 );
			return str
				.replace ( /['"]/g, "" )
				.replace ( /\W+/g, " " )
				.replace (
				/ (.)/g, function ( $1 )
				{
					return $1.toUpperCase ();
				}
			)
				.replace ( / /g, "" );
		};

		// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
		if ( !Object.keys ) {
			Object.keys = (
				function ()
				{

					var hasOwnProperty  = Object.prototype.hasOwnProperty,
					    hasDontEnumBug  = !(
					    {
						    toString: null
					    }
					    ).propertyIsEnumerable ( "toString" ),
					    dontEnums       = [
						    "toString",
						    "toLocaleString",
						    "valueOf",
						    "hasOwnProperty",
						    "isPrototypeOf",
						    "propertyIsEnumerable",
						    "constructor"
					    ],
					    dontEnumsLength = dontEnums.length;

					return function ( obj )
					{
						if ( typeof obj !== "object" && (
							typeof obj !== "function" || obj === null
							) ) {
							throw new TypeError ( "Object.keys called on non-object" );
						}

						var result = [],
						    prop, i;

						for ( prop in obj ) {
							if ( hasOwnProperty.call ( obj, prop ) ) {
								result.push ( prop );
							}
						}

						if ( hasDontEnumBug ) {
							for ( i = 0; i < dontEnumsLength; i++ ) {
								if ( hasOwnProperty.call ( obj, dontEnums[ i ] ) ) {
									result.push ( dontEnums[ i ] );
								}
							}
						}
						return result;
					};
				} ()
			);
		}

		// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array#Array_generic_methods
		// Assumes Array extras already present (one may use polyfills for these as well)
		var i,
		    // We could also build the array of methods with the following, but the
		    //   getOwnPropertyNames() method is non-shimable:
		    // Object.getOwnPropertyNames(Array).filter(function(methodName) {
		    //   return typeof Array[methodName] === 'function'
		    // });
		    methods            = [
			    "join", "reverse", "sort", "push", "pop", "shift", "unshift",
			    "splice", "concat", "slice", "indexOf", "lastIndexOf",
			    "forEach", "map", "reduce", "reduceRight", "filter",
			    "some", "every"
		    ],
		    methodCount        = methods.length,
		    assignArrayGeneric = function ( methodName )
		    {
			    if ( !Array[ methodName ] ) {
				    var method = Array.prototype[ methodName ];
				    if ( typeof method === "function" ) {
					    Array[ methodName ] = function ()
					    {
						    return method.call.apply ( method, arguments );
					    };
				    }
			    }
		    };

		for ( i = 0; i < methodCount; i++ ) {
			assignArrayGeneric ( methods[ i ] );
		}
	} ()
);