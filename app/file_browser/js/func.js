function getAjaxUri()
{
    uri = 'index.php?ajax=1&editor='+editor+'&filter='+gFilter+'&path='+gPath;
    return uri;
}



/**
 * PHP.JS (http://phpjs.org)
 *
 * This function is convenient when encoding a string to be used in a query part of a URL,
 * as a convenient way to pass variables to the next page.
 *
 * http://phpjs.org/functions/urlencode:573
 */
function urlencode (str) {
    str = (str+'').toString();

    // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
    // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}

/**
 * PHP.JS (http://phpjs.org)
 *
 * http://phpjs.org/functions/urldecode:572
 */
function urldecode (str) {
    return decodeURIComponent(str.replace(/\+/g, '%20'));
}

function isNumber(val) {
    return /^-?((\d+\.?\d?)|(\.\d+))$/.test(val);
}

/**
 * Dav Glass extension for the Yahoo UI Library
 *
 * Produces output according to format.
 */
function printf() {
    var num = arguments.length;
    var oStr = arguments[0];
    for (var i = 1; i < num; i++) {
        var pattern = "\\{" + (i-1) + "\\}";
        var re = new RegExp(pattern, "g");
        oStr = oStr.replace(re, arguments[i]);
    }
    return oStr;
}


String.prototype.format = function() {
    var formatted = this;
    for(arg in arguments) {
        formatted = formatted.replace("{" + arg + "}", arguments[arg]);
    }
    return formatted;
};




var all_lines = new Array();
var details_folders_last_ordered = "";
function tableDetailsSort(type, direction){

    folders = all_lines['folders'];
    files = all_lines['files'];

    if(typeof folders === 'undefined')folders = new Array();
    if(typeof files === 'undefined')files = new Array();

    if(details_folders_last_ordered == '')
        details_folders_last_ordered = folders;


    // order by name
    if(type == 'name'){

        folders.sort(function(a, b){
            var nameA=a.name.toLowerCase(), nameB=b.name.toLowerCase();
            if (nameA < nameB)return -1; //sort string ascending
            if (nameA > nameB)return 1;
            return 0; // default return value (no sorting)
        });

        files.sort(function(a, b){
            var nameA=a.name.toLowerCase(), nameB=b.name.toLowerCase();
            if (nameA < nameB)return -1; //sort string ascending
            if (nameA > nameB)return 1;
            return 0; // default return value (no sorting)
        });

        if(direction == 'down'){
            folders.reverse();
            files.reverse();
        }
        details_folders_last_ordered = folders;
    }

    // order by date
    if(type == 'date'){
        folders.sort(function(a, b){
            return a.date - b.date;
        });

        files.sort(function(a, b){
            return a.date - b.date;
        });

        if(direction == 'down'){
            folders.reverse();
            files.reverse();
        }
        details_folders_last_ordered = folders;
    }

    // order by type
    if(type == 'type'){
        files.sort(function(a, b){
            var nameA=a.type.toLowerCase(), nameB=b.type.toLowerCase();
            if (nameA < nameB)return -1; //sort string ascending
            if (nameA > nameB)return 1;
            return 0; // default return value (no sorting)
        });

        if(direction == 'down'){
            files.reverse();
        }
    }

    // order by size
    if(type == 'size'){
        files.sort(function(a, b){
            return a.size - b.size;
        });

        if(direction == 'down'){
            files.reverse();
        }
    }

    // order by dimension
    if(type == 'dimensions'){
        files.sort(function(a, b){
            var nameA=a.dimensions, nameB=b.dimensions;
            if (nameA < nameB)return -1; //sort string ascending
            if (nameA > nameB)return 1;
            return 0; // default return value (no sorting)
        });

        if(direction == 'down'){
            files.reverse();
        }
    }

    str = "";

    // folders
    for(i=0; i < details_folders_last_ordered.length; i++)
        str += details_folders_last_ordered[i]['tr'];

    // files
    for(i=0; i < files.length; i++)
        str += files[i]['tr'];

    $('#details tbody').html(str);
    $.MediaBrowser.filter();
}


function showUpload(){
    nWindowOpen('upload_window');
}



function nWindowOpen(objId)
{
    w_width = $('#'+objId).width();
    w_height = $('#'+objId).height();
    b_width = $('body').width();
    b_height = $('body').height();

    if(b_width < w_width)b_width = w_width;
    if(b_height < w_height)b_height = w_height;

    left = (b_width - w_width) / 2;
    top = (b_height - w_height) / 2;


    $('#'+objId).css('top', top+'px');
    $('#'+objId).css('left', left+'px');
    $('#'+objId).show();

}


function nWindowClose(objId)
{
    $('#'+objId).hide();
}
