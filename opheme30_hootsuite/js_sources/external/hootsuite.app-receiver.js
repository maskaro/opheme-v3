function getWindow ( a, e ) {return parent.frames[ a + "_" + e ]}
function parseUrl ()
{
	var a = document.location.href, e = a.indexOf ( "?" ) > -1 ? a.split ( "?" )[ 1 ] : "", b, a = [];
	if ( e.length && (
			a = e.split ( /&(?!amp;)/ ), a[ 0 ].match ( /^action=(\w)+$/i )
		) ) {
		var e = a[ 0 ].replace ( /^action=/i, "" ).toLowerCase (), c = a[ 1 ].replace ( /^p1=/i, "" ), f = a[ 2 ].replace ( /^p2=/i, "" ), d = a[ 3 ] && a[ 3 ].replace ( /^key=/i, "" ) || null, g = a[ 4 ] && a[ 4 ].replace ( /^pid=/i, "" ) || null;
		c && c.length && (
			c = decodeURIComponent ( c.replace ( /\+/g, " " ) )
		);
		f && f.length && (
			f = decodeURIComponent ( f.replace ( /\+/g, " " ) )
		);
		d     = getWindow ( d, g );
		switch ( e ) {
			case "trigger_callingback":
				b =
					d.hsp.fnCallingBack;
				a = [ c ];
				break;
			case "trigger_phonehome":
				b = d.hsp.fnPhoneHome;
				a = [ c ];
				break;
			case "trigger_refresh":
				b = d.hsp.fnOnRefresh;
				break;
			case "trigger_gettwitteraccounts":
				b = d.hsp.fnOnGetTwitterAccounts;
				a = [ c ];
				break;
			case "trigger_dropuser":
				b = d.hsp.fnOnDropUser;
				a = [ c, f ];
				break;
			case "trigger_closepopup":
				b = d.hsp.fnOnClosePopUp;
				break;
			case "trigger_sendtoapp":
				b = d.hsp.fnOnSendToApp;
				a = JSON.parse ( c );
				a = [ a ];
				break;
			case "trigger_sendcomposedmsgtoapp":
				b = d.hsp.fnOnSendComposedMsgToApp;
				a = JSON.parse ( c );
				a = [ a ];
				break;
			case "trigger_sendprofiletoapp":
				b = d.hsp.fnOnSendProfileToApp;
				a = JSON.parse ( c );
				a = [ a ];
				break;
			case "trigger_sendassignmentupdates":
				b = d.hsp.fnOnSendAssignmentUpdates, a = JSON.parse ( c ), a = [ a ]
		}
		typeof b != "undefined" && b.apply ( null, a );
		window.location.hash = storedHash = ""
	}
}
function go ()
{
	try {parseUrl ()}
	catch ( a ) {}
}
var storedHash = window.location.hash;
"onhashchange"in window ? window.onhashchange = function () {go ()} : window.setInterval ( function () {
	if ( window.location.hash != storedHash ) {
		storedHash = window.location.hash, go ()
	}
}, 100 );
go ();