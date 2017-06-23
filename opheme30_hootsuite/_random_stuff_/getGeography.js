var Base64 = {

// private property
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
    encode: function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

// public method for decoding
    decode: function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

// private method for UTF-8 encoding
    _utf8_encode: function (string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

// private method for UTF-8 decoding
    _utf8_decode: function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}


function updateDialog() {


    var e = document.getElementById("company");
    var company = e.options[e.selectedIndex].value;
    var query = document.location.search;
    var url = "/vidpiq/ajax/updateStreamDialog.php" + query;

    _gaq.push(['_trackEvent', 'Vidpiq', 'UpdateDialog']);
    _gaq.push(['_trackPageview', '/vidpiq/updateDialog.php']);


    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                $body = xmlhttp.responseText;
                document.getElementById("update").innerHTML = $body;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + company);


}


function echeck(str) {

    try {
        str = this.trim(str)
        var at = "@"
        var dot = "."
        var lat = str.indexOf(at)
        var lstr = str.length
        var ldot = str.indexOf(dot)
        if (str.indexOf(at) == -1) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }

        if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr) {
            hsp.showStatusMessage("Please enter a valid username.", "error");

            return false
        }

        if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }

        if (str.indexOf(at, (lat + 1)) != -1) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }

        if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }

        if (str.indexOf(dot, (lat + 2)) == -1) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }

        if (str.indexOf(" ") != -1) {
            hsp.showStatusMessage("Please enter a valid username.", "error");
            return false
        }
    } catch (err) {
    }
    return true
}

function setFocus(id) {
    //console.log(id);
    //document.getElementById(id).focus();

}

function validateLoginDetails() {


    _gaq.push(['_trackEvent', 'Vidpiq', 'Login']);
    _gaq.push(['_trackPageview', '/vidpiq/login.php']);
    var emailID = document.login.username.value;
    var password = document.login.pw.value;

    if (echeck(document.login.username.value) == false) {
        username.value = "";
        username.focus();
        return false;
    }

    if (document.login.username.value == '') {
        hsp.showStatusMessage("Please enter a valid Email.", "error");

        document.login.pw.focus();
        return false;
    }

    if (document.login.pw.value == '') {
        hsp.showStatusMessage("Please enter a valid password.", "error");
        document.login.pw.focus();
        return false;
    }
    var query = document.location.search;
    var url = "ajax/checkUser.php" + query;

    //_gaq.push(['_setAccount', 'UA-23977134-2']);

    //_gaq.push(['_trackPageview','/vidpiq/Authenticate.php']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = trim(xmlhttp.responseText);
                if (result == 1) {
                    location.reload(true)
                } else {

                    hsp.showStatusMessage("Invalid login", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('emailID=' + emailID + "&password=" + password);


    return true;
}


function initHS(username) {

    var server = getServerName();

    var apikey = getApikey();

    if (username != "") {
        username = "(" + username + ")";
    }
    var receiver = getProto() + '//' + getServerName() + '/my_receiver.html';

    hsp.init({apiKey: apikey, receiverPath: receiver, useTheme: false, subtitle: username});

    hsp.bind('refresh', function () {
        refreshStream();
        return false;
    });

    /*
     if (username != "") {
     var username = "(" + username + ")"; 
     hsp.updatePlacementSubtitle(username);
     }
     */

}


function getApikey() {
    var apikey;
    if (getServerName() == "apps-dev.synaptive.net") {
        apikey = "5mo6x36eb2wwcwkccc4sgckog3iab8ndd76";
    } else {
        apikey = "09abskw53ssw80ckwsk0kgwo43ibh0e5ejb";
    }
    return apikey;
}

function deleteItem(id) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/deleteItem.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/deleteItem.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'DeleteItem']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {
                    var makeFavObj = document.getElementById("item_" + id);
                    makeFavObj.style.display = "none";
                    hsp.showStatusMessage("Deleted item", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't delete item", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);
}

function archiveItem(id, state) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/archiveItem.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/archiveItem.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'ArchiveItem']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {

                    if (state == "unread") {
                        var makeFavObj = document.getElementById("item_" + id);
                        makeFavObj.style.display = "none";
                    }
                    var archiveObj = document.getElementById(id + "_arch");
                    archiveObj.onclick = new Function("unarchiveItem('" + id + "');return false;");
                    archiveObj.innerHTML = "Unarchive";

                    hsp.showStatusMessage("Archived item", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't archive item", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);
}

function unarchiveItem(id, state) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/unarchiveItem.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/unarchiveItem.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'UnarchiveItem']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {


                    if (state == "archive") {
                        var makeFavObj = document.getElementById("item_" + id);
                        makeFavObj.style.display = "none";
                    }

                    var archiveObj = document.getElementById(id + "_arch");
                    archiveObj.onclick = new Function("archiveItem('" + id + "');return false;");
                    archiveObj.innerHTML = "Archive";
                    hsp.showStatusMessage("Unarchived item", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't unarchive item", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);
}

function showTagInput(id) {

    var div_id = "tag_input_div_" + id;
    document.getElementById(div_id).style.display = "inline";


}

function hideTagInput(id) {

    var div_id = "tag_input_div_" + id;
    document.getElementById(div_id).style.display = "none";
}

function addTag(id) {


    var tag_input = 'tag_input_' + id;

    var query = document.location.search;

    var url = "/vidpiq/ajax/addTag.php" + query;
    var tags = document.getElementById(tag_input).value;
    //console.log(tags);

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/addTag.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'AddTag']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {

                    var tag = values.tag;
                    var tag_enc = Base64.encode(tag);
                    //var tag_html = "<span class=tags>" + tag + "</span>";

                    var tag_html = '';

                    var query = document.location.search;

                    var url2 = "index.php" + query + "&tag=" + escape(tag_enc);
                    ;


                    var tag_html = '<div id="tag_' + tag_enc + '_' + id + '" class="btn-group dropup">';

                    tag_html += '<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">' + tag + '</button>';

                    tag_html += '<ul class="dropdown-menu" style="width:80px;min-width:80px;border-radius:4px;">';

                    tag_html += '<li><a onclick="deleteTag(' + "'" + id + "'" + ',' + "'" + tag_enc + "'" + ');return false " href="#" style="font-size:10px;line-height:14px;">Delete tag</a></li>';

                    tag_html += '<li><a href="' + url2 + '" style="font-size:10px;line-height:14px;">Search</a></li>'
                    tag_html += '</ul>';
                    tag_html += '</div>';


                    //var tag_html = "<span class=tags onclick='deleteTag(" + '"' + id + '","' + tag_enc + '"' + ");return false;'   title='Delete tag' id='tag_" + tag_enc + "_" + id + "' >" + tag + "</span>";


//<span title='Delete tag' id=\"tag_".$tag_enc."_".$id."\" class=tags onclick=\"deleteTag('$id','$tag_enc');return false \">$tag</span>";					

                    var div_id = "tags_" + id;
                    var innerHTML = document.getElementById(div_id).innerHTML;
                    innerHTML = innerHTML + tag_html;
                    document.getElementById(div_id).innerHTML = innerHTML;


                    hideTagInput(id);

                    hsp.showStatusMessage("Added tag", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't add tag", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id + "&tags=" + escape(tags));
}


function deleteTag(id, tag) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/deleteTag.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/deleteTag.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'DeleteTag']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {


                    var tag_id = "tag_" + tag + "_" + id;
                    //console.log(tag_id);
                    var makeFavObj = document.getElementById(tag_id);
                    makeFavObj.style.display = "none";


                    hsp.showStatusMessage("Deleted tag", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't delete tag", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id + '&tags=' + escape(tag));
}


function makeFavourite(id) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/makeFavorite.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/makeFavourite.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'Favourite']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {
                    var makeFavObj = document.getElementById("make_favorite_" + id);

                    makeFavObj.className = "icon-app-dir x-heart-on";

                    makeFavObj.onclick = new Function("makeUnFavourite('" + id + "');return false;");
                    makeFavObj.title = "Unfavorite";
                    hsp.showStatusMessage("Added as favorite", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't make favorite", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);
}

function makeUnFavourite(id) {
    var query = document.location.search;

    url = "/vidpiq/ajax/makeUnfavorite.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);

    _gaq.push(['_trackPageview', '/vidpiq/makeUnfavourite.php']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'Unfavourite']);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {
                    var makeFavObj = document.getElementById("make_favorite_" + id);

                    var frameUrl = window.location.href;

                    makeFavObj.className = "icon-app-dir x-heart";


                    makeFavObj.onclick = new Function("makeFavourite('" + id + "');return false;");
                    makeFavObj.title = "Favorite";

                    hsp.showStatusMessage("Removed as favorite", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't remove from favorites", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);
}

function Follow(userid, id) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/Follow.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/Follow.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'Follow']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {
                    var makeFavObj = document.getElementById("make_follow_" + id);

                    makeFavObj.className = "icon-app-dir x-unfollow";

                    makeFavObj.onclick = new Function("unFollow('" + userid + "','" + id + "');return false;");
                    makeFavObj.title = "Unfollow this user";
                    hsp.showStatusMessage("Added to follow list", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't follow this user", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + userid);
}

function Follow2(userid) {
    var query = document.location.search;

    var url = "/vidpiq/ajax/Follow.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);


    _gaq.push(['_trackPageview', '/vidpiq/Follow.php']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'Follow']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {

                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;


                    var makeFavObj = document.getElementById('follow_button');


                    makeFavObj.onclick = new Function("unFollow2('" + userid + "');return false;");
                    makeFavObj.innerHTML = "Following";
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Now following this user", "success");
                } else {
                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;

                    window.parent.frames[apikey_pid].hsp.showStatusMessage("ERROR: Couldn't follow this user", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + userid);
}


function unFollow(userid, id) {
    var query = document.location.search;

    url = "/vidpiq/ajax/unFollow.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);

    _gaq.push(['_trackPageview', '/vidpiq/unFollow.php']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'Unfollow']);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                if (values.success) {
                    var makeFavObj = document.getElementById("make_follow_" + id);

                    makeFavObj.className = "icon-app-dir x-user";


                    makeFavObj.onclick = new Function("Follow('" + userid + "','" + id + "');return false;");
                    makeFavObj.title = "Follow this user";

                    hsp.showStatusMessage("Unfollowed user", "success");
                } else {
                    hsp.showStatusMessage("ERROR: Couldn't unfollow user", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + userid);
}

function unFollow2(userid, id) {
    var query = document.location.search;

    url = "/vidpiq/ajax/unFollow.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);

    _gaq.push(['_trackPageview', '/vidpiq/unFollow.php']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'Unfollow']);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                var apikey = getApikey();
                var pid = getParameterByName('pid');
                var apikey_pid = apikey + "_" + pid;
                if (values.success) {

                    var makeFavObj = document.getElementById('follow_button');


                    makeFavObj.onclick = new Function("Follow2('" + userid + "');return false;");
                    makeFavObj.innerHTML = "Follow";


                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Unfollowed user", "success");
                } else {
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("ERROR: Couldn't unfollow user", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + userid);
}

function trackEvent(category, action, label) {

    _gaq.push(['_setAccount', 'UA-47413801-1']);
    _gaq.push(['_trackEvent', category, action, label]);

}

function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function showUser(id, name) {

    var query = document.location.search;
    var server = getServerName();
    var title = "User Profile: " + name;
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowUserInfo']);
    //console.log(id);
    var url = getProto() + "//" + server + "/vidpiq/ajax/showUser.php" + query + "&id=" + id + "&name=" + name;
    hsp.showCustomPopup(url, title, '750', '430');


}


function showUserPopup(id, name) {


    var apikey = getApikey();
    var pid = getParameterByName('pid');


    var query = document.location.search;
    var server = getServerName();
    var title = "User Profile: " + name;


    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowUserInfo']);
    //console.log(id);
    var url = getProto() + "//" + server + "/vidpiq/ajax/showUser.php" + query + "&id=" + id + "&name=" + name;

    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].hsp.showCustomPopup(url, title, '750', '430');


}

function replaceTagStream(tag) {
    var query = document.location.search;

    var buttonObj = document.getElementById('view_tag');

    buttonObj.onclick = new Function("viewTagInStream('" + tag + "');return false;");


    var url = '/vidpiq/ajax/showTagStream.php' + query + "&tag=" + tag;

    $.get(url, function (data) {
        $('#ytStream').html(data);
    });

}


function picPopup(url) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var title = "Image";
    window.parent.frames[apikey_pid].hsp.showCustomPopup(url, title, '500', '500');

}

function showTag(tag) {
    var server = getServerName();
    var query = document.location.search;
    //console.log(tag);
    //console.log(escape(tag));

    var url = getProto() + "//" + server + "/vidpiq/ajax/showTag.php" + query + "&tag=" + escape(tag);
    var url = getProto() + "//" + server + "/vidpiq/ajax/showTag.php" + query + "&tag=" + tag;


    var title = "Hashtag: " + tag;

    hsp.showCustomPopup(url, title, '750', '430');

}


function showTagPopup(tag) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');

    var server = getServerName();
    var query = document.location.search;
    //console.log(tag);
    //console.log(escape(tag));

    var url = getProto() + "//" + server + "/vidpiq/ajax/showTag.php" + query + "&tag=" + escape(tag);
    var url = getProto() + "//" + server + "/vidpiq/ajax/showTag.php" + query + "&tag=" + tag;


    var title = "Hashtag: " + tag;

    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].hsp.showCustomPopup(url, title, '750', '430');


}

function deleteSearch() {

    var query = document.location.search;
    var server = getServerName();

    var select = document.getElementById("delete_feed");
    var select2 = document.getElementById("stream_feed");

    var delete_feed = select.options[select.selectedIndex].value;

    _gaq.push(['_trackEvent', 'Vidpiq', 'DeleteSubscription']);


    var url = getProto() + "//" + server + "/vidpiq/ajax/deleteSubscription.php" + query + "&id=" + delete_feed;
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                //console.log(values);
                if (values.success) {
                    hsp.showStatusMessage(values.success, "success");
                } else {
                    hsp.showStatusMessage(values.error, "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);


    for (i = 0; i < select.length; i++) {
        if (select.options[i].value == delete_feed) {
            select.remove(i);
        }
    }

    for (i = 0; i < select2.length; i++) {
        if (select2.options[i].value == delete_feed) {
            select2.remove(i);
        }
    }

}

function showComments(id, stream_feed, popup) {

    var query = document.location.search;
    var server = getServerName();
    var name = "";
    var title = "Show Comments" + name;
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowComments']);


    var url = getProto() + "//" + server + "/vidpiq/ajax/showComments.php" + query + "&id=" + id + "&stream_feed=" + stream_feed + "&popup=" + popup;
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var html = xmlhttp.responseText;
                //console.log(html);
                if (html) {
                    var cmt_id = id + "_cmt";
                    document.getElementById(cmt_id).style.display = "block";
                    document.getElementById(cmt_id).innerHTML = html;

                } else {
                    hsp.showStatusMessage("Error getting comments", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);

}


function showLikes(id, stream_feed, popup) {

    var query = document.location.search;
    var server = getServerName();
    var name = "";
    var title = "Show Likes" + name;
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowLikes']);


    var url = getProto() + "//" + server + "/vidpiq/ajax/showLikes.php" + query + "&id=" + id + "&stream_feed=" + stream_feed + "&popup=" + popup;
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var html = xmlhttp.responseText;
                //console.log(html);
                if (html) {
                    var cmt_id = id + "_cmt";
                    document.getElementById(cmt_id).style.display = "block";
                    document.getElementById(cmt_id).innerHTML = html;

                } else {
                    hsp.showStatusMessage("Error getting likes", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id);


}


function viewMedia(id) {

    var query = document.location.search;
    var server = getServerName();
    var name = "";
    var title = "View Media";
    _gaq.push(['_trackEvent', 'Vidpiq', 'ViewMedia']);
    var url = getProto() + "//" + server + "/vidpiq/ajax/viewMedia.php" + query + "&id=" + id;
    hsp.showCustomPopup(url, title, '500', '500');

}

function refreshStream() {


    if (document.body.scrollTop == 0 && document.getElementById("messages")) {
        //console.log('refresh');
        _gaq.push(['_trackEvent', 'Vidpiq', 'Refresh']);
        _gaq.push(['_trackPageview', '/vidpiq/refresh.php']);


        var query = document.location.search;


        url = '/vidpiq/ajax/getFeed.php' + query + '&refresh=1&page=1';

        _gaq.push(['_setAccount', 'UA-47413801-1']);

        _gaq.push(['_trackPageview', '/vidpiq/getFeed.php']);
        _gaq.push(['_trackEvent', 'Vidpiq', 'Refresh']);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', url, true);
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200) {

                    var obj = document.getElementById('messages');
                    //console.log(xmlhttp.responseText);
                    if (xmlhttp.responseText != "") {
                        obj.innerHTML = xmlhttp.responseText;
                    }
                    //obj.innerHTML = "test";

                } else {
                }
            }
        }
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlhttp.send();

        return false;
    } else {

    }

}


function refreshStream_original() {
    //console.log('refresh');
    var body = document.getElementsByTagName('body')[0];
    var divs = body.getElementsByTagName("div");
    var div;

    for (i = 0; i < divs.length; i++) {
        div = divs[i];
        if (div.className == 'newMessage') {
            div.style.display = "none";
        }
    }
    document.getElementById('new_msgs').style.display = "none";

    _gaq.push(['_trackEvent', 'Vidpiq', 'Refresh']);
    _gaq.push(['_trackPageview', '/vidpiq/refresh.php']);


    var query = document.location.search;


    url = '/vidpiq/ajax/getFeed.php' + query + '&refresh=1&page=1';

    _gaq.push(['_setAccount', 'UA-47413801-1']);

    _gaq.push(['_trackPageview', '/vidpiq/getFeed.php']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'Refresh']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                var obj = document.getElementById('messages');

                var newcontent = document.createElement('div');

                newcontent.innerHTML = xmlhttp.responseText;
                //console.log(xmlhttp.responseText);
                var count = 0;
                while (newcontent.lastChild) {
                    //console.log(newcontent.lastChild.id);					
                    if (/_msg/.test(newcontent.lastChild.id)) {
                        count++;
                    }

                    obj.insertBefore(newcontent.lastChild, obj.firstChild);

                }
                //console.log(count);


                if (document.getElementById('new_msgs') && (count > 0)) {

                    document.getElementById('new_msgs').style.display = "inline-block";
                    document.getElementById('new-items').innerHTML = count;

                }

            } else {
            }
        }
    }
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send();

    return false;

}

function showLocation(lat, lng, title) {

    var server = getServerName();
    var query = document.location.search;

    var url = getProto() + "//" + server + "/vidpiq/ajax/showLocation.php" + query + "&lat=" + lat + "&lng=" + lng + "&title=" + escape(title);
    hsp.showCustomPopup(url, title, '900', '500');

}

function initialize(lat, lng, id, title) {

    title = unescape(title);


    if (title == "Location") {
        title = "";
    }

    var mapObj = document.getElementById(id);

    mapObj.style.position = "fixed";
    mapObj.style.display = "block";
    mapObj.width = "600px";
    mapObj.height = "500px";

    var latlng = new google.maps.LatLng(lat, lng);
    var myOptions = {zoom: 16, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP};
    var map = new google.maps.Map(document.getElementById(id), myOptions);

    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title
    });

}

function toggleLicense() {
    for (index = 0; index < document.fms_search.licencetype.length; index++) {
        if (document.fms_search.licencetype[index].checked) {
            var radioValue = document.fms_search.licencetype[index].value;
            break;
        }
    }
    if (radioValue == "cc") {
        document.getElementById('cc_search').style.display = "block";
    } else {
        document.getElementById('cc_search').style.display = "none";
        document.getElementById('commercial').checked = false;
        document.getElementById('derivs').checked = false;
    }
}

function checkNotLoading() {
    if (document.getElementById('loading').style.display != "block") {
        //alert('not loading');
        return true;
    }
    //alert('loading');
    return false;
}


function dump(arr, level) {
    var dumped_text = "";
    if (!level) level = 0;

    //The padding given at the beginning of the line.
    var level_padding = "";
    for (var j = 0; j < level + 1; j++) level_padding += "    ";

    if (typeof(arr) == 'object') { //Array/Hashes/Objects 
        for (var item in arr) {
            var value = arr[item];

            if (typeof(value) == 'object') { //If it is an array,
                dumped_text += level_padding + "'" + item + "' ...\n";
                dumped_text += dump(value, level + 1);
            } else {
                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }
    } else { //Stings/Chars/Numbers etc.
        dumped_text = "===>" + arr + "<===(" + typeof(arr) + ")";
    }
    return dumped_text;
}


function deleteStream(stream_id) {

    _gaq.push(['_trackEvent', 'Vidpiq', 'DeleteStream']);
    _gaq.push(['_trackPageview', '/vidpiq/deleteStream.php']);
    var query = document.location.search;
    var server = getServerName();
    var url = getProto() + "//" + server + "/vidpiq/ajax/deleteStream.php" + query;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = trim(xmlhttp.responseText);
                if (result == 1) {
                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;
                    closePopUp(true)
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Stream deleted", "success");

                } else {
                    alert("Couldn't delete stream");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('stream_id=' + stream_id);


}


function createStream() {
    var query = document.location.search;

    _gaq.push(['_setAccount', 'UA-47413801-1']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'CreateStream']);
    _gaq.push(['_trackPageview', '/vidpiq/createStream.php']);

    var query = document.location.search;
    var server = getServerName();	//name = "Channel: " + name;
    var url = getProto() + "//" + server + "/vidpiq/ajax/createStream.php" + query;
    var name = "Create New Stream";
    hsp.showCustomPopup(url, name, '600', '420');

}
function toggleSearchType() {

    var e = document.getElementById("s_sub_type");
    var sub_type = e.options[e.selectedIndex].value;

    if (sub_type == "tags") {

        document.getElementById('s_tag_search').style.display = "inline";
        document.getElementById('s_loc_search').style.display = "none";
        document.getElementById('s_geo_search').style.display = "none";
        document.getElementById('s_user_search').style.display = "none";

        document.getElementById('s_user').value = "";
        document.getElementById('s_user_id').value = "";
        document.getElementById('s_geo').value = "";
        document.getElementById('s_loc').value = "";

    } else if (sub_type == "loc") {

        document.getElementById('s_tag_search').style.display = "none";
        document.getElementById('s_loc_search').style.display = "inline";
        document.getElementById('s_geo_search').style.display = "none";
        document.getElementById('s_user_search').style.display = "none";

        document.getElementById('s_user').value = "";
        document.getElementById('s_user_id').value = "";
        document.getElementById('s_geo').value = "";
        document.getElementById('s_tag').value = "";

    } else if (sub_type == "geo") {

        document.getElementById('s_tag_search').style.display = "none";
        document.getElementById('s_loc_search').style.display = "none";
        document.getElementById('s_geo_search').style.display = "inline";
        document.getElementById('s_user_search').style.display = "none";


        document.getElementById('user').value = "";
        document.getElementById('user_id').value = "";
        document.getElementById('s_loc').value = "";
        document.getElementById('s_tag').value = "";

    } else if (sub_type == "user") {

        document.getElementById('s_tag_search').style.display = "none";
        document.getElementById('s_loc_search').style.display = "none";
        document.getElementById('s_geo_search').style.display = "none";
        document.getElementById('s_user_search').style.display = "inline";

        document.getElementById('s_geo').value = "";
        document.getElementById('s_loc').value = "";
        document.getElementById('s_tag').value = "";

    }

}


function toggleSearchType2() {

    var e = document.getElementById("sub_type");
    var sub_type = e.options[e.selectedIndex].value;

    if (sub_type == "tags") {

        document.getElementById('tag_search').style.display = "inline";
        document.getElementById('loc_search').style.display = "none";
        document.getElementById('geo_search').style.display = "none";
        document.getElementById('user_search').style.display = "none";

        document.getElementById('user').value = "";
        document.getElementById('user_id').value = "";
        document.getElementById('geo').value = "";
        document.getElementById('loc').value = "";


    } else if (sub_type == "loc") {

        document.getElementById('tag_search').style.display = "none";
        document.getElementById('loc_search').style.display = "inline";
        document.getElementById('geo_search').style.display = "none";
        document.getElementById('user_search').style.display = "none";

        document.getElementById('user').value = "";
        document.getElementById('user_id').value = "";

        document.getElementById('geo').value = "";
        document.getElementById('tag').value = "";


    } else if (sub_type == "geo") {

        document.getElementById('tag_search').style.display = "none";
        document.getElementById('loc_search').style.display = "none";
        document.getElementById('geo_search').style.display = "inline";
        document.getElementById('user_search').style.display = "none";

        document.getElementById('user').value = "";
        document.getElementById('user_id').value = "";
        document.getElementById('loc').value = "";
        document.getElementById('tag').value = "";


    } else if (sub_type == "user") {

        document.getElementById('tag_search').style.display = "none";
        document.getElementById('loc_search').style.display = "none";
        document.getElementById('geo_search').style.display = "none";
        document.getElementById('user_search').style.display = "inline";

        document.getElementById('geo').value = "";
        document.getElementById('loc').value = "";
        document.getElementById('tag').value = "";


    }

}


function getLocation() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getLocation.php" + query;
    var title = "Select Search Location";
    hsp.showCustomPopup(url, title, '900', '500');

}

function getLocation2() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getLocation2.php" + query;
    var title = "Select Search Location";
    hsp.showCustomPopup(url, title, '900', '500');

}


function getGeography() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getGeography.php" + query;
    var title = "Select Search Area";
    hsp.showCustomPopup(url, title, '900', '500');

}

function getGeography2() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getGeography2.php" + query;
    var title = "Select Search Area";
    hsp.showCustomPopup(url, title, '900', '500');

}


function returnValue(id) {
    return document.getElementById(id).value;


}

function isInt(value) {
    return !isNaN(value) &&
        parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
}

function setRadius() {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;


    var radius = document.getElementById('radius').value


    if (radius == "Enter radius in meters (max 5000)") {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a valid radius", "error");
        return "ERROR";
    } else if (!isInt(radius)) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a valid number for radius", "error");
        return "ERROR";
    } else if (parseInt(radius) > 5000) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Maximum radius allowed is 5000 meters", "error");
        return "ERROR";

    } else {
        window.parent.frames[apikey_pid].setValue('radius', radius);
        return "OK";
    }

}

function setRadius2() {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;


    var radius = document.getElementById('radius').value


    if (radius == "Enter radius in meters (max 5000)") {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a valid radius", "error");
        return "ERROR";
    } else if (!isInt(radius)) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a valid number for radius", "error");
        return "ERROR";
    } else if (parseInt(radius) > 5000) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Maximum radius allowed is 5000 meters", "error");
        return "ERROR";

    } else {
        //console.log('setting radius value');
        window.parent.frames[apikey_pid].setValue('s_radius', radius);
        return "OK";
    }

}


function clearLoc() {
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('loc', '');
    window.parent.frames[apikey_pid].setValue('radius', '');

    closePopUp(false);
}

function clearLoc2() {
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('s_geo', '');
    window.parent.frames[apikey_pid].setValue('s_radius', '');

    closePopUp(false);
}

function hideMap() {
    closePopUp(false);
}

function setValue(id, loc_value) {
    document.getElementById(id).value = loc_value;


}

function getMap() {
    // Initialize default values

    var loc_id = "geo";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var tmplatlng = window.parent.frames[apikey_pid].returnValue(loc_id);


    var zoom = 13;

    if (tmplatlng != "") {
        var res = tmplatlng.split(', ');
        var lat = res[0];
        var lng = res[1];
        var latlng = new google.maps.LatLng(lat, lng);
        makeMap(zoom);
    } else {

        // If ClientLocation was filled in by the loader, use that info instead
        if (google.loader.ClientLocation) {
            var latlng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
            var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
            window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);

            makeMap(zoom);

        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
                window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);

                makeMap(zoom);
            });
        } else {
            var latlng = new google.maps.LatLng(49.24131, -123.10081);
            var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
            window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);
            makeMap(zoom);

        }


    }


}

function getMap3() {
    // Initialize default values

    var loc_id = "s_geo";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var tmplatlng = window.parent.frames[apikey_pid].returnValue(loc_id);


    var zoom = 13;

    if (tmplatlng != "") {
        var res = tmplatlng.split(', ');
        var lat = res[0];
        var lng = res[1];
        var latlng = new google.maps.LatLng(lat, lng);
        makeMap3(zoom);
    } else {

        // If ClientLocation was filled in by the loader, use that info instead
        if (google.loader.ClientLocation) {
            var latlng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
            var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
            window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);

            makeMap3(zoom);

        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
                window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);

                makeMap3(zoom);
            });
        } else {
            var latlng = new google.maps.LatLng(49.24131, -123.10081);
            var tmp_loc = latlng.lat().toFixed(5) + ", " + latlng.lng().toFixed(5);
            window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);
            makeMap3(zoom);

        }


    }


}


function makeMap3(zoom) {

    var loc_id = "s_geo";
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var tmplatlng = window.parent.frames[apikey_pid].returnValue(loc_id);
    var loc_radius = window.parent.frames[apikey_pid].returnValue('s_radius');

    if (loc_radius != "") {
        document.getElementById('radius').value = loc_radius;

    }

    var res = tmplatlng.split(', ');
    var lat = res[0];
    var lng = res[1];

    var latlng = new google.maps.LatLng(lat, lng);
    //var latlng = new google.maps.LatLng(49.24131, -123.10081);


    var myOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    }
    //console.log(myOptions);
    //document.getElementById(\"map\").style.display = "inline";

    var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);


    //var autocomplete = new google.maps.places.Autocomplete(input);	

    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true
    });
    var input = document.getElementById("searchBox");
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var searchBox = new google.maps.places.SearchBox(input);

    loc_radius = parseInt(loc_radius);

    var circle = new google.maps.Circle({
        map: map,
        radius: loc_radius,
        fillColor: '#FF0000',
        strokeWeight: 1,
        strokeOpacity: 0.8,
        strokeColor: '#FF0000',
        fillOpacity: 0.35,

    });

    circle.bindTo('center', marker, 'position');

    google.maps.event.addDomListener(
        document.getElementById('radius'), 'change', function () {
            new_radius = document.getElementById('radius').value;
            if (setRadius2(new_radius) == "OK") {
                new_radius = parseInt(new_radius);
                circle.setRadius(new_radius);
            }
        });


    google.maps.event.addListener(marker, "dragend", function (event) {
        var point = marker.getPosition();
        map.panTo(point);
        //ocument.getElementById(loc_id).value = point.lat().toFixed(5) + ", " + point.lng().toFixed(5);

        var tmp_loc = point.lat().toFixed(5) + ", " + point.lng().toFixed(5);
        window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);


    });


    document.getElementById("map-canvas").style.visibility = 'visible';
}

function makeMap(zoom) {

    var loc_id = "geo";
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var tmplatlng = window.parent.frames[apikey_pid].returnValue(loc_id);
    var loc_radius = window.parent.frames[apikey_pid].returnValue('radius');

    if (loc_radius != "") {
        document.getElementById('radius').value = loc_radius;
    }

    var res = tmplatlng.split(', ');
    var lat = res[0];
    var lng = res[1];

    var latlng = new google.maps.LatLng(lat, lng);
    //var latlng = new google.maps.LatLng(49.24131, -123.10081);


    var myOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    }
    //console.log(myOptions);
    //document.getElementById(\"map\").style.display = "inline";

    var map = new google.maps.Map(document.getElementById("map"), myOptions);

    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true
    });

    // Create the search box and link it to the UI element.
    var input = document.getElementById('searchBox');
    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);	 

    //var searchBox = new google.maps.places.SearchBox(input);  

    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('searchBox'));

    loc_radius = parseInt(loc_radius);

    var circle = new google.maps.Circle({
        map: map,
        radius: loc_radius,
        fillColor: '#FF0000',
        strokeWeight: 1,
        strokeOpacity: 0.8,
        strokeColor: '#FF0000',
        fillOpacity: 0.35,

    });

    circle.bindTo('center', marker, 'position');

    google.maps.event.addDomListener(
        document.getElementById('radius'), 'change', function () {
            new_radius = document.getElementById('radius').value;
            if (setRadius(new_radius) == "OK") {
                new_radius = parseInt(new_radius);
                circle.setRadius(new_radius);
            }
        });


    google.maps.event.addListener(marker, "dragend", function (event) {
        var point = marker.getPosition();
        map.panTo(point);
        //ocument.getElementById(loc_id).value = point.lat().toFixed(5) + ", " + point.lng().toFixed(5);

        var tmp_loc = point.lat().toFixed(5) + ", " + point.lng().toFixed(5);
        window.parent.frames[apikey_pid].setValue(loc_id, tmp_loc);


    });


    document.getElementById("map_div").style.visibility = 'visible';
}


function getMap2() {
    // Initialize default values

    var lat_id = "loc_lat";
    var lng_id = "loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);

    var zoom = 13;

    if ((lat != "") && (lng != "")) {

        var latlng = new google.maps.LatLng(lat, lng);
        makeMap2(zoom);
    } else {

        // If ClientLocation was filled in by the loader, use that info instead
        if (google.loader.ClientLocation) {
            var latlng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);


            var lat = latlng.lat().toFixed(5);
            //setValue('lat',lat);
            var lng = latlng.lng().toFixed(5);
            //setValue('lng',lng);			


            window.parent.frames[apikey_pid].setValue(lat_id, lat);
            window.parent.frames[apikey_pid].setValue(lng_id, lng);


            makeMap2(zoom);

        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                var lat = latlng.lat().toFixed(5);
                //setValue('lat',lat);
                var lng = latlng.lng().toFixed(5);
                //setValue('lng',lng);			
                window.parent.frames[apikey_pid].setValue(lat_id, lat);
                window.parent.frames[apikey_pid].setValue(lng_id, lng);

                makeMap2(zoom);
            });
        } else {
            var latlng = new google.maps.LatLng(49.24131, -123.10081);
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            var lat = latlng.lat().toFixed(5);
            //setValue('lat',lat);
            var lng = latlng.lng().toFixed(5);
            //setValue('lng',lng);	
            window.parent.frames[apikey_pid].setValue(lat_id, lat);
            window.parent.frames[apikey_pid].setValue(lng_id, lng);

            makeMap2(zoom);

        }


    }


}


function makeMap2(zoom) {

    var lat_id = "loc_lat";
    var lng_id = "loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);


    var latlng = new google.maps.LatLng(lat, lng);


    var myOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    }
    //console.log(myOptions);
    //document.getElementById(\"map\").style.display = "inline";

    var map = new google.maps.Map(document.getElementById("map"), myOptions);

    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true
    });

    google.maps.event.addListener(marker, "dragend", function (event) {
        var point = marker.getPosition();
        map.panTo(point);

        var lat = point.lat().toFixed(5);
        var lng = point.lng().toFixed(5);

        window.parent.frames[apikey_pid].setValue(lat_id, lat);
        window.parent.frames[apikey_pid].setValue(lng_id, lng);

    });

    document.getElementById("map_div").style.visibility = 'visible';
}


function getMap4() {
    // Initialize default values

    var lat_id = "s_loc_lat";
    var lng_id = "s_loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);

    //console.log(lat);
    //console.log(lng);

    var zoom = 13;

    if ((lat != "") && (lng != "")) {

        var latlng = new google.maps.LatLng(lat, lng);
        makeMap4(zoom);
    } else {

        // If ClientLocation was filled in by the loader, use that info instead
        if (google.loader.ClientLocation) {
            var latlng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);


            var lat = latlng.lat().toFixed(5);
            //setValue('lat',lat);
            var lng = latlng.lng().toFixed(5);
            //setValue('lng',lng);			


            window.parent.frames[apikey_pid].setValue(lat_id, lat);
            window.parent.frames[apikey_pid].setValue(lng_id, lng);


            makeMap4(zoom);

        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                var lat = latlng.lat().toFixed(5);
                //setValue('lat',lat);
                var lng = latlng.lng().toFixed(5);
                //setValue('lng',lng);			
                window.parent.frames[apikey_pid].setValue(lat_id, lat);
                window.parent.frames[apikey_pid].setValue(lng_id, lng);

                makeMap4(zoom);
            });
        } else {
            var latlng = new google.maps.LatLng(49.24131, -123.10081);
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            var lat = latlng.lat().toFixed(5);
            //setValue('lat',lat);
            var lng = latlng.lng().toFixed(5);
            //setValue('lng',lng);	
            window.parent.frames[apikey_pid].setValue(lat_id, lat);
            window.parent.frames[apikey_pid].setValue(lng_id, lng);

            makeMap4(zoom);

        }


    }


}


function makeMap4(zoom) {

    var lat_id = "s_loc_lat";
    var lng_id = "s_loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);


    var latlng = new google.maps.LatLng(lat, lng);


    var myOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    }
    //console.log(myOptions);
    //document.getElementById(\"map\").style.display = "inline";

    var map = new google.maps.Map(document.getElementById("map"), myOptions);

    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true
    });

    google.maps.event.addListener(marker, "dragend", function (event) {
        var point = marker.getPosition();
        map.panTo(point);

        var lat = point.lat().toFixed(5);
        var lng = point.lng().toFixed(5);

        window.parent.frames[apikey_pid].setValue(lat_id, lat);
        window.parent.frames[apikey_pid].setValue(lng_id, lng);

    });

    document.getElementById("map_div").style.visibility = 'visible';
}

function selectLocation(loc, name) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('search_name', name);
    window.parent.frames[apikey_pid].setValue('loc', loc);

    closePopUp(false)

}

function selectLocation2(loc, name) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('s_search_name', name);
    window.parent.frames[apikey_pid].setValue('s_loc', loc);

    closePopUp(false)

}


function selectTag(tag) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('tag', tag);
    var search_name = '#' + tag;

    window.parent.frames[apikey_pid].setValue('search_name', search_name);

    closePopUp(false)

}

function selectTag2(tag) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('s_tag', tag);
    var search_name = '#' + tag;

    window.parent.frames[apikey_pid].setValue('s_search_name', search_name);

    closePopUp(false)

}

function selectUser(userid, username) {
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('user', username);
    window.parent.frames[apikey_pid].setValue('user_id', userid);
    var search_name = '@' + username;
    window.parent.frames[apikey_pid].setValue('search_name', search_name);
    closePopUp(false)

}

function selectUser2(userid, username) {

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].setValue('s_user', username);
    window.parent.frames[apikey_pid].setValue('s_user_id', userid);

    var search_name = '@' + username;
    window.parent.frames[apikey_pid].setValue('s_search_name', search_name);
    closePopUp(false)

}

function findLocations() {

    var query = document.location.search;
    var server = getServerName();
    var title = "Find Locations";
    _gaq.push(['_trackEvent', 'Vidpiq', 'FindLocations']);

    var lat_id = "s_loc_lat";
    var lng_id = "s_loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);

    var url = getProto() + "//" + server + "/vidpiq/ajax/findLocations.php" + query + '&lat=' + lat + '&lng=' + lng;
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("location_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}

function findLocations2() {

    var query = document.location.search;
    var server = getServerName();
    var title = "Find Locations";
    _gaq.push(['_trackEvent', 'Vidpiq', 'FindLocations']);

    var lat_id = "s_loc_lat";
    var lng_id = "s_loc_lng";

    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var lat = window.parent.frames[apikey_pid].returnValue(lat_id);
    var lng = window.parent.frames[apikey_pid].returnValue(lng_id);

    var url = getProto() + "//" + server + "/vidpiq/ajax/findLocations2.php" + query + '&lat=' + lat + '&lng=' + lng;
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("location_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}

function findTags() {

    var query = document.location.search;
    var server = getServerName();
    var name = "";

    var q = document.getElementById("tag").value;
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowComments']);

    var url = getProto() + "//" + server + "/vidpiq/ajax/findTags.php" + query + '&q=' + q;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("tag_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}

function findTags2() {

    var query = document.location.search;
    var server = getServerName();
    var name = "";

    var q = document.getElementById("tag").value;
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowComments']);

    var url = getProto() + "//" + server + "/vidpiq/ajax/findTags2.php" + query + '&q=' + q;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("tag_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}

function findUsers() {

    var query = document.location.search;
    var server = getServerName();
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowComments']);

    var q = document.getElementById("user").value;


    var url = getProto() + "//" + server + "/vidpiq/ajax/findUsers.php" + query + '&q=' + q + '&count=100';
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("user_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}


function findUsers2() {

    var query = document.location.search;
    var server = getServerName();
    _gaq.push(['_trackEvent', 'Vidpiq', 'ShowComments']);

    var q = document.getElementById("user").value;


    var url = getProto() + "//" + server + "/vidpiq/ajax/findUsers2.php" + query + '&q=' + q + '&count=100';
    //hsp.showCustomPopup(url, title, '400', '500');

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                document.getElementById("user_list").innerHTML = xmlhttp.responseText;

            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send();

}

function newSub() {

    var query = document.location.search;

    _gaq.push(['_setAccount', 'UA-47413801-1']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'newSub']);
    _gaq.push(['_trackPageview', '/vidpiq/newSub.php']);

    var query = document.location.search;
    var server = getServerName();	//name = "Channel: " + name;


    var loc = document.getElementById("loc").value;
    var geo = document.getElementById("geo").value;
    var tag = document.getElementById("tag").value;
    var user = document.getElementById("user").value;
    var user_id = document.getElementById("user_id").value;
    var radius = document.getElementById("radius").value;

    var url = getProto() + "//" + server + "/vidpiq/ajax/newSub.php" + query;

    if ((loc == "") && (geo == "") && (tag == "") && (user == "")) {
        hsp.showStatusMessage("Please enter search criteria", "error");
        return;

    }

    var search_name = document.getElementById("search_name").value;

    if (search_name == "") {
        hsp.showStatusMessage("Please enter a Search Name", "error");
        return;
    }

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                //console.log(values);
                if (values.success) {
                    hsp.showStatusMessage(values.success, "success");
                    location.href = "/vidpiq/" + query;
                } else {
                    hsp.showStatusMessage(values.error, "error");
                }
            } else {
                console.log('there was an error...');
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('username=' + escape(user) + '&tag=' + escape(tag) + '&loc=' + escape(loc) + '&geo=' + escape(geo) + '&radius=' + escape(radius) + '&user_id=' + escape(user_id) + '&search_name=' + escape(search_name));
}


function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)')
        .exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}


function getTag() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getTag.php" + query;
    var title = "Instagram Tag Search";
    hsp.showCustomPopup(url, title, '300', '300');

}

function getTag2() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getTag2.php" + query;
    var title = "Instagram Tag Search";
    hsp.showCustomPopup(url, title, '300', '300');

}

function getUser() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getUser.php" + query;
    var title = "Instagram User Search";
    hsp.showCustomPopup(url, title, '300', '300');

}

function getUser2() {
    var server = getServerName();	//name = "Channel: " + name;
    var query = document.location.search;

    var url = "https://" + server + "/vidpiq/ajax/getUser2.php" + query;
    var title = "Instagram User Search";
    hsp.showCustomPopup(url, title, '300', '300');

}


function showNav(id) {

    var navid = id + "_nav";
    var navidObj = document.getElementById(navid);
    //console.log(id);
    navidObj.style.display = "inline";
}

function hideNav(id) {

    var navid = id + "_nav";
    var navidObj = document.getElementById(navid);
    navidObj.style.display = "none";
}


function handleEnterKey(myform, event) {
    var keycode;

    event = event || window.event;

    if (event) {
        keycode = event.keyCode;
    } else {
        return true;
    }

    if (keycode == 13) {
        validateLoginDetails();
        //document.forms[myform].submit();
        return false;
    } else {
        return true;
    }
}


function getUrlVars() {
    var map = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        map[key] = value;
    });
    return map;
}

function receiveMessage(text) {
    alert("Message received: " + text);
}

function openLink(url) {
    instance = window.open("about:blank");
    instance.document.write("<meta http-equiv=\"refresh\" content=\"0;url=" + url + "\">");
    instance.document.close();
    return false;
}

function getWorkflow(id) {


    var query = document.location.search;
    var server = getServerName();	//name = "Channel: " + name;
    var url = getProto() + "//" + server + "/vidpiq/ajax/getWorkflow.php" + query + "&id=" + id;

    var name = "Update Workflow";
    //hsp.showCustomPopup(url, name, '300', '470');
    hsp.showCustomPopup(url, name, '450', '395');

}

function updateWorkflow(id) {


    var e = document.getElementById("workflow");
    var workflow = e.options[e.selectedIndex].value;

    var workflow_note = document.getElementById("workflow_note").value;

    var query = document.location.search;
    var server = getServerName();
    var url = getProto() + "//" + server + "/vidpiq/ajax/updateWorkflow.php" + query;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = trim(xmlhttp.responseText);
                var apikey = getApikey();
                var pid = getParameterByName('pid');
                var apikey_pid = apikey + "_" + pid;
                if (result == 1) {

                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Updated Review's Workflow", "success");
                    closePopUp(true)

                } else {
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("There was an error updating the Review's Workflow", "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('review_id=' + id + "&workflow=" + workflow + "&note=" + escape(workflow_note));

    hsp.composeMessage(text, {shortenLinks: true});

}

function addReviewSite() {

    var e = document.getElementById("add_loc");
    var location = e.options[e.selectedIndex].value;
    var display = document.getElementById(location).style.display;
    if (display == 'inline') {

        var apikey = getApikey();
        var pid = getParameterByName('pid');
        var apikey_pid = apikey + "_" + pid;

        window.parent.frames[apikey_pid].hsp.showStatusMessage("You have already added this site", "error");

    } else {

        document.getElementById(location).style.display = "inline";
        var apikey = getApikey();
        var pid = getParameterByName('pid');
        var apikey_pid = apikey + "_" + pid;
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Added new site", "success");

    }

}

function editLocation() {
    var e = document.getElementById("edit_loc");
    var location = e.options[e.selectedIndex].value;


    var query = document.location.search;
    var server = getServerName();
    var url = getProto() + "//" + server + "/vidpiq/ajax/editLocation.php" + query + "&id=" + location;
    var name = "Edit Location";
    //hsp.showCustomPopup(url, name, '300', '470');
    hsp.showCustomPopup(url, name, '650', '500');

}


function createLocation() {

    var query = document.location.search;
    var server = getServerName();
    var url = getProto() + "//" + server + "/vidpiq/ajax/editLocation.php" + query + "&id=new";
    var name = "Create New Location";
    //hsp.showCustomPopup(url, name, '300', '470');
    hsp.showCustomPopup(url, name, '650', '500');

}

function saveNewLocation(form, button) {

    var items = "";
    if (document.getElementById("loc_name").value == "") {
        items = "Location Name";
    }

    if (document.getElementById("loc_address").value == "") {

        if (items == "") {
            items = "Address";
        } else {
            items = items + ", Address";
        }
    }

    if (document.getElementById("loc_city").value == "") {

        if (items == "") {
            items = "City";
        } else {
            items = items + ", City";
        }
    }

    if (document.getElementById("loc_prov").value == "") {

        if (items == "") {
            items = "State/Province";
        } else {
            items = items + ", State/Province";
        }
    }

    if (document.getElementById("loc_postal_code").value == "") {

        if (items == "") {
            items = "Zip/Postal Code";
        } else {
            items = items + ", Zip/Postal Code";
        }
    }

    if (document.getElementById("loc_country").value == "") {

        if (items == "") {
            items = "Country";
        } else {
            items = items + ", Country";
        }
    }

    if (document.getElementById("loc_phone").value == "") {

        if (items == "") {
            items = "Phone";
        } else {
            items = items + ", Phone";
        }
    }


    if (items != "") {
        var msg = "Please enter: " + items;
        var apikey = getApikey();
        var pid = getParameterByName('pid');
        var apikey_pid = apikey + "_" + pid;

        window.parent.frames[apikey_pid].hsp.showStatusMessage(msg, "error");
    } else {
        // post the form

        var request;
        // bind to the submit event of our form
        $("#location_form").submit(function (event) {
            // abort any pending request
            if (request) {
                request.abort();
            }
            // setup some local variables
            var $form = $(this);
            // let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $form.serialize();

            // let's disable the inputs for the duration of the ajax request
            // Note: we disable elements AFTER the form data has been serialized.
            // Disabled form elements will not be serialized.
            $inputs.prop("disabled", true);

            // fire off the request to /form.php
            request = $.ajax({
                url: "/ajax/createNewLocation.php",
                type: "post",
                data: serializedData
            });

            request.done(function (response, textStatus, jqXHR) {
                //console.log(response);
            });

            request.fail(function (jqXHR, textStatus, errorThrown) {
                console.error(
                    "The following error occured: " +
                    textStatus, errorThrown
                );
            });

            request.always(function () {
                $inputs.prop("disabled", false);
            });

            event.preventDefault();
        });
    }


}

function createNewLocation() {

}


function clearForm() {

    document.getElementById("stream_title").value = "";
    document.getElementById("since").value = "";
    document.getElementById("tag").value = "";
    document.getElementById("search").value = "";
    document.getElementById("domain").value = "";

}

function sendMessage(text) {


    _gaq.push(['_setAccount', 'UA-47413801-1']);

    _gaq.push(['_trackEvent', 'Vidpiq', 'ShareMessage']);
    _gaq.push(['_trackPageview', '/vidpiq/shareMessage.php']);


    hsp.composeMessage(text, {shortenLinks: true});


}


function showAll($id) {
    var showAllId = $id + "_show";
    var hideAllId = $id + "_hide";
    var titleId = $id + "_title";

    var showidObj = document.getElementById(showAllId);
    var hideidObj = document.getElementById(hideAllId);
    var titleIdObj = document.getElementById(titleId);
    var idObj = document.getElementById($id);

    idObj.style.display = 'inline';
    showidObj.style.display = "none";
    hideidObj.style.display = "inline";
    titleIdObj.style.fontWeight = "bold";
}

function hideAll($id) {

    var showAllId = $id + "_show";
    var hideAllId = $id + "_hide";
    var titleId = $id + "_title";
    var idObj = document.getElementById($id);
    var showidObj = document.getElementById(showAllId);
    var hideidObj = document.getElementById(hideAllId);
    var titleIdObj = document.getElementById(titleId);

    idObj.style.display = 'none';
    showidObj.style.display = "inline";
    hideidObj.style.display = "none";
    titleIdObj.style.fontWeight = "normal";


}

function viewMore3($type, $nextFeed, $query) {

    $id = "show_more";

    if (document.getElementById($id)) {
        document.getElementById('loading').style.display = 'block';
        var idObj = document.getElementById($id);
        idObj.style.display = 'none';
        idObj.id = idObj.id + "_foo";

        $url = '/vidpiq/ajax/getFeed3.php' + $query + '&nextFeed=' + $nextFeed + '&type=' + $type;

        _gaq.push(['_setAccount', 'UA-47413801-1']);
        _gaq.push(['_trackPageview', '/vidpiq/getFeed.php']);


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', $url, true);

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200) {
                    var obj = document.getElementById('messages');
                    //var origHtml = obj.innerHTML;
                    //obj.innerHTML = obj.innerHTML + xmlhttp.responseText;
                    var newcontent = document.createElement('div');
                    newcontent.innerHTML = xmlhttp.responseText;
                    while (newcontent.firstChild) {
                        obj.appendChild(newcontent.firstChild);
                    }
                    //$('.hs_icon').hs_tipTip();
                    //obj.innerHTML = origHtml + obj.innerHTML;
                    document.getElementById('loading').style.display = 'none';
                } else {
                    hsp.showStatusMessage("Network connection error", "error");

                }
            }
        }
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlhttp.send();
    }
}


function initHSPlugin() {
    var server = getServerName();

    var apikey = getApikey();

    var receiver = getProto() + '//' + getServerName() + '/my_receiver.html';

    hsp.init({apiKey: apikey, receiverPath: receiver});


    hsp.bind('sendtoapp', function (message) {
        saveMessageTo(message);
    });

}

function saveMessageTo(message) {


    _gaq.push(['_setAccount', 'UA-23977134-2']);
    _gaq.push(['_trackPageview', '/vidpiq/saveMessageTo']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'saveMessageTo']);


    var msgQuery = "&msg=" + encodeURIComponent(JSON.stringify(message));
    var query = document.location.search;

    var query = document.location.search;
    var server = getServerName();	//name = "Channel: " + name;
    var url = "https://" + server + "/vidpiq/ajax/saveMessageTo.php" + query + msgQuery;
    var name = "Add to Vidpiq";

    hsp.showCustomPopup(url, name, '300', '225');


}

function manageTags(id) {

    url = "/vidpiq/ajax/manageTagsPopup.php";

    var query = document.location.search;
    var server = getServerName();	//name = "Channel: " + name;
    var url = "https://" + server + "/vidpiq/ajax/manageTagsPopup.php" + query + "&id=" + id;
    var name = "Manage Tags";

    hsp.showCustomPopup(url, name, '300', '225');

}

function addTags(id) {

    var tags = document.getElementById("tags").value;
    var query = document.location.search;

    url = "/vidpiq/ajax/addTags.php" + query;

    //console.log('add tags');
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var values = JSON.parse(xmlhttp.responseText);
                //console.log(values);
                if (values.success) {
                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;

                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Added tags", "success");

                    closePopUp(true);
                } else {
                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;

                    //window.parent.frames[apikey_pid].hsp.showStatusMessage("ERROR: Couldn't add tags", "error");	

                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Added tags", "success");

                    closePopUp(true);


                }
            } else {
                console.log('some sort of error...');
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id + '&tags=' + escape(tags));

}


function viewMore(stream_feed, page, max, refresh) {


    var query = document.location.search;

    id = "show_more";

    if (document.getElementById(id)) {
        document.getElementById('loading').style.display = 'block';
        var idObj = document.getElementById(id);
        idObj.style.display = 'none';
        idObj.id = idObj.id + "_foo";

        var url = '/vidpiq/ajax/getFeed.php' + query + '&page=' + page + '&max=' + max + '&stream_feed=' + stream_feed + '&refresh=' + refresh;
        //console.log(url);
        _gaq.push(['_setAccount', 'UA-47413801-1']);
        _gaq.push(['_trackPageview', '/vidpiq/ViewMore.php']);
        _gaq.push(['_trackEvent', 'Vidpiq', 'ViewMore']);

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', url, true);

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200) {
                    var obj = document.getElementById('messages');
                    //var origHtml = obj.innerHTML;
                    //obj.innerHTML = obj.innerHTML + xmlhttp.responseText;
                    var newcontent = document.createElement('div');
                    newcontent.innerHTML = xmlhttp.responseText;
                    while (newcontent.firstChild) {
                        obj.appendChild(newcontent.firstChild);
                    }
                    //$('.hs_icon').hs_tipTip();
                    //obj.innerHTML = origHtml + obj.innerHTML;
                    document.getElementById('loading').style.display = 'none';
                } else {
                    hsp.showStatusMessage("Network connection error", "error");
                }
            }
        }
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlhttp.send();
    }
}

function closePopUp2(reload) {
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;

    hsp.closeCustomPopup(apikey, pid);

    if (reload == true) {
        setTimeout(function () {
            location.reload();
        }, 4000);
    }

}

function closePopUp(reload) {
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    window.parent.frames[apikey_pid].closePopUp2(reload);

}

function selectAll(id) {

    var obj2 = document.getElementById(id + '_span');

    obj2.style.display = 'none';

    var obj = document.getElementById(id);

    if (!obj.options.length) {
        return;
    }
    for (var i = 0; i < obj.options.length; i++) {
        obj.options[i].selected = true;
    }


}

function deSelectAll(id) {

    var obj2 = document.getElementById(id + '_span');
    obj2.style.display = 'inline-block';

    var obj = document.getElementById(id);


    if (!obj.options.length) {
        return;
    }
    for (var i = 0; i < obj.options.length; i++) {
        obj.options[i].selected = false;
    }


}

function saveStream(reload) {

    var query = document.location.search;
    var url = "saveStream.php" + query;

    _gaq.push(['_trackEvent', 'Vidpiq', 'SaveStream']);
    _gaq.push(['_trackPageview', '/vidpiq/saveStream.php']);
    var e = document.getElementById("company");
    var company = e.options[e.selectedIndex].value;


    var select1 = document.getElementById("branch");
    var selected1 = [];
    var select1_selected = false;

    for (var i = 0; i < select1.length; i++) {
        if (select1.options[i].selected) {
            selected1.push(select1.options[i].value);
            select1_selected = true;
        }
    }


    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;


    if (select1_selected == false) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a location", "error");

        return;
    }

    var branch = selected1;


    var select2 = document.getElementById("rating_site");
    var selected2 = [];
    var select2_selected = false;

    for (var i = 0; i < select2.length; i++) {
        if (select2.options[i].selected) {
            selected2.push(select2.options[i].value);
            select2_selected = true;
        }
    }
    if (select2_selected == false) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a Review Site", "error");

        return;
    }


    var rating_site = selected2;


    var select3 = document.getElementById("workflows");
    var selected3 = [];
    var select3_selected = false;

    for (var i = 0; i < select3.length; i++) {
        if (select3.options[i].selected) {
            selected3.push(select3.options[i].value);
            select3_selected = true;
        }
    }
    if (select3_selected == false) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a Workflow", "error");

        return;
    }


    var workflow = selected3;

    var e = document.getElementById("star_min");
    var star_min = e.options[e.selectedIndex].value;

    var e = document.getElementById("star_max");
    var star_max = e.options[e.selectedIndex].value;

    //var e = document.getElementById("isprivate");	

    if (document.getElementById('isprivate').checked) {
        var isprivate = document.getElementById('isprivate').value;
    } else {
        var isprivate = 1;
    }

    var shared_radio = getSelectedRadio(shared);

    if (shared_radio == 'all') {
        shared_radio = '';
    }

    var stream_name = document.getElementById("stream_name").value;
    var keywords = document.getElementById("keywords").value;

    if (stream_name == '') {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a Stream Name", "error");
        return;
    }

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = trim(xmlhttp.responseText);
                if (result == 1) {

                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;


                    closePopUp(reload)

                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Stream saved", "success");

                } else {

                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    var locations_radio = getSelectedRadio(display);
    var review_sites_radio = getSelectedRadio(display2);
    var workflow_radio = getSelectedRadio(display3);

    if (locations_radio == "all") {
        branch = "";
    }

    if (review_sites_radio == "all") {
        rating_site = "";
    }

    if (workflow_radio == "all") {
        workflow = "";
    }


    xmlhttp.send('company=' + company + "&branch=" + branch + "&rating_site=" + rating_site + "&star_min=" + star_min + "&star_max=" + star_max + "&isprivate=" + isprivate + "&shared=" + shared_radio + "&stream_name=" + escape(stream_name) + "&workflow=" + workflow + "&keywords=" + escape(keywords));

}


function getSelectedRadio(radio_group) {

    for (var i = 0; i < radio_group.length; i++) {
        var button = radio_group[i];
        if (button.checked) {
            //alert(button.value);
            return button.value;
        }
    }
    return "none";


}

function updateStream(reload) {

    var query = document.location.search;
    var url = "updateStream.php" + query;
    _gaq.push(['_trackEvent', 'Vidpiq', 'UpdateStream']);
    _gaq.push(['_trackPageview', '/vidpiq/updateStream.php']);

    //display == locations
    //display2 == review sites


    var stream_id = document.getElementById("stream_id").value;
    var e = document.getElementById("company");
    var company = e.options[e.selectedIndex].value;


    var select1 = document.getElementById("branch");
    var selected1 = [];
    var select1_selected = false;
    for (var i = 0; i < select1.length; i++) {
        if (select1.options[i].selected) {
            selected1.push(select1.options[i].value);
            select1_selected = true;
        }
    }


    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;

    if (select1_selected == false && getSelectedRadio(display) == 'filtered') {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a location", "error");
        return;
    }


    var branch = selected1;


    var select2 = document.getElementById("rating_site");
    var selected2 = [];
    var select2_selected = false;
    for (var i = 0; i < select2.length; i++) {
        if (select2.options[i].selected) {
            selected2.push(select2.options[i].value);
            select2_selected = true;
        }
    }

    if (select2_selected == false && getSelectedRadio(display2) == 'filtered') {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a Review Site", "error");
        return;
    }

    var rating_site = selected2;

    var select3 = document.getElementById("workflows");
    var selected3 = [];
    var select3_selected = false;

    for (var i = 0; i < select3.length; i++) {
        if (select3.options[i].selected) {
            selected3.push(select3.options[i].value);
            select3_selected = true;
        }
    }
    if (select3_selected == false) {
        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please select a Workflow", "error");
        return;
    }


    var workflow = selected3;

    var e = document.getElementById("star_min");
    var star_min = e.options[e.selectedIndex].value;

    var e = document.getElementById("star_max");
    var star_max = e.options[e.selectedIndex].value;

    //var e = document.getElementById("isprivate");	


    if (document.getElementById('isprivate').checked) {
        var isprivate = document.getElementById('isprivate').value;
    } else {
        var isprivate = 1;
    }

    var shared_radio = getSelectedRadio(shared);

    if (shared_radio == 'all') {
        shared_radio = '';
    }

    var stream_name = document.getElementById("stream_name").value;
    var keywords = document.getElementById("keywords").value;

    if (stream_name == '') {

        window.parent.frames[apikey_pid].hsp.showStatusMessage("Please enter a Stream Name", "error");
        return;
    }


    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = trim(xmlhttp.responseText);
                if (result == 1) {

                    var apikey = getApikey();
                    var pid = getParameterByName('pid');
                    var apikey_pid = apikey + "_" + pid;

                    closePopUp(reload)
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Stream updated", "success");

                } else {

                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


    var locations_radio = getSelectedRadio(display);
    var review_sites_radio = getSelectedRadio(display2);
    var workflow_radio = getSelectedRadio(display3);

    if (locations_radio == "all") {
        branch = "";
    }

    if (review_sites_radio == "all") {
        rating_site = "";
    }

    if (workflow_radio == "all") {
        workflow = "";
    }

    xmlhttp.send('stream_id=' + stream_id + '&company=' + company + "&branch=" + branch + "&rating_site=" + rating_site + "&star_min=" + star_min + "&star_max=" + star_max + "&isprivate=" + isprivate + "&shared=" + shared_radio + "&stream_name=" + escape(stream_name) + "&workflow=" + workflow + "&keywords=" + escape(keywords));

}

function submitSearch() {
    _gaq.push(['_trackEvent', 'Vidpiq', 'Search']);
    _gaq.push(['_trackPageview', '/vidpiq/search.php']);

    var type = document.getElementById("s_sub_type").value;
    //console.log(type);

    if (type == "tags") {
        var tag = document.getElementById("s_tag").value;

        if (tag == "") {
            hsp.showStatusMessage("Please enter a tag", "error");
            return;
        } else {
            submitForm('vidpiq_search', 'submit_search');
        }

    } else if (type == "geo") {
        var geo = document.getElementById("s_geo").value;
        var radius = document.getElementById("radius").value;
        if (geo == "") {
            hsp.showStatusMessage("Please select a location", "error");
            return;
        } else {
            submitForm('vidpiq_search', 'submit_search');
        }
    } else if (type == "loc") {
        var loc = document.getElementById("s_loc").value;
        if (loc == "") {
            hsp.showStatusMessage("Please select a location", "error");
            return;
        } else {
            submitForm('vidpiq_search', 'submit_search');
        }
    } else if (type == "user") {
        var user = document.getElementById("s_user").value;
        var user_id = document.getElementById("user_id").value;
        if (user == "") {
            hsp.showStatusMessage("Please enter a user", "error");
            return;
        } else {
            submitForm('vidpiq_search', 'submit_search');
        }
    }


}

function submitStream() {
    _gaq.push(['_trackEvent', 'Vidpiq', 'SelectStream']);
    _gaq.push(['_trackPageview', '/vidpiq/selectStream.php']);


    submitForm('vidpiq_stream', 'submit_stream');

}

function editStream(reload) {

    var query = document.location.search;
    var url = "editStream.php" + query;
    _gaq.push(['_trackEvent', 'Vidpiq', 'EditStream']);
    _gaq.push(['_trackPageview', '/vidpiq/editStream.php']);

    if (document.getElementById("selected_stream_edit").length > 0) {
        var e = document.getElementById('selected_stream_edit');
        var stream = e.options[e.selectedIndex].value;
        _gaq.push(['_setAccount', 'UA-47413801-1']);
        _gaq.push(['_trackPageview', '/vidpiq/editStream.php']);

        var query = document.location.search;
        var server = getServerName();	//name = "Channel: " + name;
        var url = getProto() + "//" + server + "/vidpiq/ajax/editStream.php" + query + "&id=" + stream;
        var name = "Edit Stream";
        //hsp.showCustomPopup(url, name, '300', '470');
        hsp.showCustomPopup(url, name, '600', '420');


    } else {
        hsp.showStatusMessage("You have no editable streams", "error");
    }
}


function viewUserInStream(username, userid) {


    var query = document.location.search;
    url = '/vidpiq/ajax/changeStream.php' + query + '&s_user=' + username + '&s_user_id=' + userid;
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                window.parent.frames[apikey_pid].location.href = "/vidpiq/" + query;
                closePopUp();
            } else {
                hsp.showStatusMessage("Network connection error", "error");
            }
        }
    }
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send();
}

function viewLocationInStream(loc, loc_name) {


    var query = document.location.search;
    url = '/vidpiq/ajax/changeStream.php' + query + '&s_geo=' + loc + '&s_radius=50&geo_name=' + escape(loc_name);
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                window.parent.frames[apikey_pid].location.href = "/vidpiq/" + query;
                closePopUp();
            } else {
                hsp.showStatusMessage("Network connection error", "error");
            }
        }
    }
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send();
}

function viewTagInStream(tag) {


    var query = document.location.search;
    url = '/vidpiq/ajax/changeStream.php' + query + '&s_tag=' + tag;
    var apikey = getApikey();
    var pid = getParameterByName('pid');
    var apikey_pid = apikey + "_" + pid;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('GET', url, true);

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                window.parent.frames[apikey_pid].location.href = "/vidpiq/" + query;
                closePopUp();
            } else {
                hsp.showStatusMessage("Network connection error", "error");
            }
        }
    }
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send();
}


function showMore_original(id) {
    var fadeId = "fade_" + id;
    //console.log(fadeId);
    var cmtId = "cmt_" + id;
    //console.log(fadeId);	
    var obj = document.getElementById(fadeId);
    obj.style.display = 'none';

    var obj = document.getElementById(cmtId);
    obj.style.maxHeight = '5000px';

}

function showMore(id) {
    var button = id + "_more_less";
    //document.getElementById(id).style.overflow='visible';
    //document.getElementById(id).style.height='auto';
    document.getElementById(id).style.display = 'block';
    document.getElementById(button).style.display = "none";
    //document.getElementById(button).innerHTML = "Hide";
    //document.getElementById(button).onclick = new Function("showLess('" + id + "');return false;");	


    _gaq.push(['_trackEvent', "Vidpiq", 'ShowMore']);

}

function addComment(id, stream_feed, popup) {


    var comment_text_id = "comment_text_" + id;
    var text = document.getElementById(comment_text_id).value;
    //console.log(text);
    var query = document.location.search;

    url = "/vidpiq/ajax/addComment.php" + query;

    _gaq.push(['_setAccount', 'UA-23977134-2']);

    _gaq.push(['_trackPageview', '/vidpiq/addComment.php']);
    _gaq.push(['_trackEvent', 'Vidpiq', 'AddComment']);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', url, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log(xmlhttp.responseText);
                var values = JSON.parse(xmlhttp.responseText);
                var apikey = getApikey();
                var pid = getParameterByName('pid');
                var apikey_pid = apikey + "_" + pid;

                if (values.success) {
                    window.parent.frames[apikey_pid].hsp.showStatusMessage("Added comment", "success");
                    showComments(id, stream_feed, popup);
                } else {
                    window.parent.frames[apikey_pid].hsp.showStatusMessage(values.error, "error");
                }
            } else {
            }
        }
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xmlhttp.send('id=' + id + '&text=' + escape(text));


}


function showLess(id) {
    var button = id + "_more_less";
    ;

    //document.getElementById(id).style.overflow='hidden';
    //document.getElementById(id).style.height='0px';
    document.getElementById(id).style.display = 'none';

    document.getElementById(button).innerHTML = "Show more";
    document.getElementById(button).onclick = new Function("showMore('" + id + "');return false;");


    _gaq.push(['_trackEvent', "Vidpiq", 'ShowLess']);

}


function submitForm(form, button) {
    oFormObject = document.forms[form];
    oFormObject.submit();
}


function throb(id, tid) {
    var buttonObj = document.getElementById(id);
    var tbuttonObj = document.getElementById(tid);

    buttonObj.style.display = 'none';
    tbuttonObj.style.display = 'inline';

}

function unThrob(id, tid) {
    var buttonObj = document.getElementById(id);
    var tbuttonObj = document.getElementById(tid);

    buttonObj.style.display = 'inline';
    tbuttonObj.style.display = 'none';

}


function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g, "");
}

function deleteSelected() {
    var select = document.getElementById('subscriptions[]');

    var i = select.length;
    var j = select.length - 1;
    while (i > 0) {
        if (select.options[j].selected) {
            select.options.remove(j);
        }
        --i;
        --j;

    }
}


function scroll() {


    if (document.getElementById('show_more')) {

        //var height = clientHeight();
        var height = document.body.offsetHeight;
        var scroll = scrollTop();
        /*
         alert(height); 
         alert(scroll); 
         alert(height - scroll); 
         */

        if ((height - scroll) <= 3000) {
            //alert('loading');

            var divId = document.getElementById('show_more');
            divId.onclick();
        }
    }
}


function scroll_new() {

    if (document.getElementById('show_more')) {

        var note = document.getElementById('show_more');
        var screenPosition = note.getBoundingClientRect();

        var bottom = screenPosition.bottom;
//		console.log(bottom);
        //var height = clientHeight();
        //var height = document.body.offsetHeight;
        //var scroll = scrollTop();
        /*
         alert(height); 
         alert(scroll); 
         alert(height - scroll); 
         */

        if (bottom <= 3000) {
            //alert('loading');

            var divId = document.getElementById('show_more');
            divId.onclick();
        }
    }
}

function clientHeight() {
    return filterResults(
        window.innerHeight ? window.innerHeight : 0,
        document.documentElement ? document.documentElement.clientHeight : 0,
        document.body ? document.body.clientHeight : 0
    );
}

function scrollTop() {
    return filterResults(
        window.pageYOffset ? window.pageYOffset : 0,
        document.documentElement ? document.documentElement.scrollTop : 0,
        document.body ? document.body.scrollTop : 0
    );
}
function filterResults(n_win, n_docel, n_body) {
    var n_result = n_win ? n_win : 0;
    if (n_docel && (!n_result || (n_result > n_docel)))
        n_result = n_docel;
    return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}


function getProto() {
    return window.location.protocol;
}

function showDIV(id) {

    var div = document.getElementById(id);

    div.style.display = "block";

    return;


}

function hideDIV(id) {

    var div = document.getElementById(id);

    div.style.display = "none";

}


function hideDIV2(id) {

    var div = document.getElementById(id);
    document.getElementById('is_friend').checked = false;
    document.getElementById('is_family').checked = false;

    div.style.display = "none";

}


function deleteCookie(c_name) {

    var cookie = getCookie(c_name);

    if (cookie) {
        setCookie(c_name);
    }

}

function getCookie(c_name) {

    var c_value = null;

    if (document.cookie) {

        var arr = document.cookie.split((escape(c_name) + '='));

        if (2 <= arr.length) {
            var arr2 = arr[1].split(';');
            c_value = unescape(arr2[0]);
        }
    }

    return c_value;

}

function setCookieValue(c_name, c_value) {

    var cookie = escape(c_name) + '=' + escape(c_value);
    //cookie += '; EXPIRES=Monday, 19-Aug-1996 05:00:00 GMT';
    cookie += '; PATH=/';
    cookie += '; DOMAIN=' + getServerName();
    document.cookie = cookie;

}

function setCookie(c_name) {

    var c_value = "false";
    var cookie = escape(c_name) + '=' + escape(c_value);
    cookie += '; EXPIRES=Monday, 19-Aug-1996 05:00:00 GMT';
    cookie += '; PATH=/';
    cookie += '; DOMAIN=' + getServerName();
    document.cookie = cookie;

}

function setCookieTrue(c_name) {

    var c_value = "true";
    var cookie = escape(c_name) + '=' + escape(c_value);
    cookie += '; EXPIRES=Monday, 19-Aug-1996 05:00:00 GMT';
    cookie += '; PATH=/';
    cookie += '; DOMAIN=' + getServerName();
    alert(cookie);
    document.cookie = cookie;

}


function authConnect(authUrl, authWindow, specs, pid, uid) {
    setCookieValue('vidpiq_pid', pid)
    setCookieValue('vidpiq_uid', uid)


    window.open(authUrl);

}

function hideCancel() {
    //document.getElementById('cancel_button').style.display = "none";
    unThrob('authorize_bid', 'authorize_tbid');
}

function checkStatus(c_name) {
    setInterval("GetCookie('" + c_name + "');", 3000);
}

function getCookieVal(offset) {
    var endstr = document.cookie.indexOf(";", offset);
    if (endstr == -1) {
        endstr = document.cookie.length;
    }
    return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg) {
            if (trim(getCookieVal(j)) == "true") {
                var url = window.location.href;
                DeleteCookie(name, '/', getServerName());
                window.location = url;
            }
        }
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0) break;
    }
    return null;
}


function DeleteCookie(name, path, domain) {

    document.cookie = name + "=" +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        "; expires=Thu, 01-Jan-70 00:00:01 GMT";

}

function SetCookie(name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function getServerName() {

    return window.location.hostname;

}


(function ($) {
    $.fn.hs_tipTip = function (options) {
        var defaults = {
            defaultPosition: "top",
            position: 'fixed',
            zindex: 50,
            content: false // HTML or String to fill TipTIp with
        };
        var fixed = {
            activation: "hover",
            keepAlive: false,
            maxWidth: "250px",
            edgeOffset: 3,
            delay: 0,
            fadeIn: 0,
            fadeOut: 0,
            attribute: "title",
            enter: function () {
            },
            exit: function () {
            }
        };
        var opts = $.extend(defaults, options, fixed);

        // Setup tip tip elements and render them to the DOM

        if ($("#hs_tiptip_holder").length <= 0) {
            var hs_tiptip_holder = $('<div id="hs_tiptip_holder" style="max-width:' + opts.maxWidth + ';"></div>');
            var hs_tiptip_content = $('<div id="hs_tiptip_content"></div>');
            var hs_tiptip_arrow = $('<div id="hs_tiptip_arrow"></div>');
            $("body").append(hs_tiptip_holder.html(hs_tiptip_content).prepend(hs_tiptip_arrow.html('<div id="hs_tiptip_arrow_inner"></div>')));
        } else {
            var hs_tiptip_holder = $("#hs_tiptip_holder");
            var hs_tiptip_content = $("#hs_tiptip_content");
            var hs_tiptip_arrow = $("#hs_tiptip_arrow");
        }

        return this.each(function () {
            var org_elem = $(this);
            var org_title;
            if (opts.content) {
                org_title = opts.content;
            } else {
                org_title = org_elem.attr(opts.attribute);
            }
            if (org_title != "" && org_title != undefined) {
                if (!opts.content) {
                    org_elem.removeAttr(opts.attribute); //remove original Attribute
                }
                var timeout = false;

                if (opts.activation == "hover") {
                    org_elem.hover(function () {
                        active_hs_tiptip();
                    }, function () {
                        if (!opts.keepAlive) {
                            deactive_hs_tiptip();
                        }
                    });
                    if (opts.keepAlive) {
                        hs_tiptip_holder.hover(function () {
                        }, function () {
                            deactive_hs_tiptip();
                        });
                    }
                } else if (opts.activation == "focus") {
                    org_elem.focus(function () {
                        active_hs_tiptip();
                    }).blur(function () {
                        deactive_hs_tiptip();
                    });
                } else if (opts.activation == "click") {
                    org_elem.click(function () {
                        active_hs_tiptip();
                        return false;
                    }).hover(function () {
                    }, function () {
                        if (!opts.keepAlive) {
                            deactive_hs_tiptip();
                        }
                    });
                    if (opts.keepAlive) {
                        hs_tiptip_holder.hover(function () {
                        }, function () {
                            deactive_hs_tiptip();
                        });
                    }
                }

                function active_hs_tiptip() {
                    opts.enter.call(this);
                    hs_tiptip_content.html(org_title);
                    hs_tiptip_holder.hide().removeAttr("class").css("margin", "0");
                    hs_tiptip_arrow.removeAttr("style");

                    var top = parseInt(org_elem.offset()['top']);
                    var left = parseInt(org_elem.offset()['left']);
                    var org_width = parseInt(org_elem.outerWidth());
                    var org_height = parseInt(org_elem.outerHeight());
                    var tip_w = hs_tiptip_holder.outerWidth();
                    var tip_h = hs_tiptip_holder.outerHeight();
                    var w_compare = Math.round((org_width - tip_w) / 2);
                    var h_compare = Math.round((org_height - tip_h) / 2);
                    var marg_left = Math.round(left + w_compare);
                    var marg_top = Math.round(top + org_height + opts.edgeOffset);
                    var t_class = "";
                    var arrow_top = "";
                    var arrow_left = Math.round(tip_w - 12) / 2;

                    if (opts.defaultPosition == "bottom") {
                        t_class = "_bottom";
                    } else if (opts.defaultPosition == "top") {
                        t_class = "_top";
                    } else if (opts.defaultPosition == "left") {
                        t_class = "_left";
                    } else if (opts.defaultPosition == "right") {
                        t_class = "_right";
                    }

                    var right_compare = (w_compare + left) < parseInt($(window).scrollLeft());
                    var left_compare = (left + tip_w / 2 + org_width / 2) > parseInt($(window).width());

                    if ((right_compare && w_compare < 0) || (t_class == "_right" && !left_compare) || (t_class == "_left" && left < (tip_w + opts.edgeOffset + 5))) {
                        t_class = "_right";
                        arrow_top = Math.round(tip_h - 13) / 2;
                        arrow_left = -12;
                        marg_left = Math.round(left + org_width + opts.edgeOffset);
                        marg_top = Math.round(top + h_compare);
                    } else if ((left_compare && w_compare < 0) || (t_class == "_left" && !right_compare)) {
                        t_class = "_left";
                        arrow_top = Math.round(tip_h - 13) / 2;
                        arrow_left = Math.round(tip_w);
                        marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
                        marg_top = Math.round(top + h_compare);
                    }

                    var top_compare = (top + org_height + opts.edgeOffset + tip_h + 8) > parseInt($(window).height() + $(window).scrollTop());
                    var topbar_h = 0;
                    if (opts.where != 'topbar') {
                        topbar_h = parseInt($('.hs_topBar .hs_content').height());
                        if (isNaN(topbar_h)) topbar_h = 0;
                    }
                    var bottom_compare = ((top + org_height) - (opts.edgeOffset + tip_h + 8 + topbar_h)) < 0;
                    // fixes:
                    if (!top_compare && !right_compare && bottom_compare && left_compare) {
                        bottom_compare = false;
                    }
                    if (opts.defaultPosition == "left" && !top_compare && !right_compare && bottom_compare && !left_compare) {
                        bottom_compare = false;
                    }

                    if (top_compare || (t_class == "_bottom" && top_compare) || (t_class == "_top" && !bottom_compare)) {
                        if (t_class == "_top" || t_class == "_bottom") {
                            t_class = "_top";
                        } else {
                            t_class = t_class + "_top";
                        }
                        arrow_top = tip_h;
                        marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset));
                    } else if (bottom_compare | (t_class == "_top" && bottom_compare) || (t_class == "_bottom" && !top_compare)) {
                        if (t_class == "_top" || t_class == "_bottom") {
                            t_class = "_bottom";
                        } else {
                            t_class = t_class + "_bottom";
                        }
                        arrow_top = -12;
                        marg_top = Math.round(top + org_height + opts.edgeOffset);
                    }

                    if (t_class == "_right_top" || t_class == "_left_top") {
                        marg_top = marg_top + 5;
                    } else if (t_class == "_right_bottom" || t_class == "_left_bottom") {
                        marg_top = marg_top - 5;
                    }
                    if (t_class == "_left_top" || t_class == "_left_bottom") {
                        marg_left = marg_left + 5;
                    }
                    hs_tiptip_arrow.css({"margin-left": arrow_left + "px", "margin-top": arrow_top + "px"});
                    hs_tiptip_holder.css({
                        "margin-left": marg_left + "px",
                        "margin-top": marg_top + "px"
                    }).attr("class", "tip" + t_class);

                    if (opts.zindex != undefined) {
                        hs_tiptip_holder.css("z-index", opts.zindex);
                    } else {
                        hs_tiptip_holder.css("z-index", '');
                    }
                    if (opts.position == 'fixed') {
                        hs_tiptip_holder.css("position", 'fixed');
                        hs_tiptip_holder.css({
                            "margin-left": (marg_left - parseInt($(window).scrollLeft())) + "px",
                            "margin-top": (marg_top - top) + "px"
                        });
                    } else {
                        hs_tiptip_holder.css("position", 'absolute');
                    }

                    if (timeout) {
                        clearTimeout(timeout);
                    }
                    timeout = setTimeout(function () {
                        hs_tiptip_holder.stop(true, true).fadeIn(opts.fadeIn);
                    }, opts.delay);
                }

                function deactive_hs_tiptip() {
                    opts.exit.call(this);
                    if (timeout) {
                        clearTimeout(timeout);
                    }
                    hs_tiptip_holder.fadeOut(opts.fadeOut);
                }
            }
        });
    }
})(jQuery);