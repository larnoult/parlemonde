var m_word;
var m_words;
var m_aWords;
var m_aFindWords;
var m_iFound;
var m_category;
var m_subcategory;
var m_wordlist;
var m_gameStarted;
var m_Solved;
var m_dim;
var m_style;
var m_referer;
var m_found;
var m_getscript;
var m_size;
var m_availWidth;
var m_availHeight;
var m_screenRatio;
var m_showlink;
var m_domain;

function init_hook() {

    jQuery(function () {       
        var isMouseDown = false;     
        var first, last;
        var firstId, lastId;
        jQuery("#ws-table td")         
            .mousedown(function () {   
                isMouseDown = true;        
                m_word = "";
                first = this;last = this;
                m_first = this;m_last = this;
                firstId = this.id;lastId = this.id;

                jQuery(this).toggleClass("highlighted");  
                jQuery(this).toggleClass("startend"); 

                isHighlighted = jQuery(this).hasClass("highlighted");
                return false; // prevent text selection         
            })         
            .mouseover(function () {           
                if (isMouseDown) 
                {             
                    jQuery(this).toggleClass("highlighted");
                    if (last != first) jQuery(last).removeClass("startend");
                    last = this;m_last = this;
                    jQuery(last).addClass("startend");
                    lastId = this.id;
                    unselectAll(first);
                    selectBetween(firstId, lastId);
                } else {
                    jQuery("#ws-table td").removeClass("focused");
                    jQuery(this).toggleClass("focused");
                }         
            })         
            .bind("selectstart", function () {           
                return false; // prevent text selection in IE         
            })         
            .mouseup(function () {           
                isMouseDown = false;  

                if (!wordFound()) {
                    unselectAll(null);
                } else {
                    
                }
                
                jQuery(first).removeClass("startend");
                jQuery(last).removeClass("startend");
            });     
    });
}
function unselectAll(first) {
   jQuery("#ws-table td").removeClass("highlighted");
   jQuery(first).toggleClass("highlighted");  
}

function selectBetween(firstTd, lastTd) {
    var cFirst = firstTd.indexOf("c");
    var cLast = lastTd.indexOf("c");
    var firstRow = parseInt(firstTd.substring(1,cFirst));
    var firstCol = parseInt(firstTd.substring(cFirst+1,cFirst+3));
    var lastRow = parseInt(lastTd.substring(1,cLast));
    var lastCol = parseInt(lastTd.substring(cLast+1,cLast+3));
    m_word = "";
    var sCaption = "";

    //same row forward
    if (firstRow == lastRow && firstCol <= lastCol) {
        for (var i = firstCol; i <= lastCol; i++) {
            sId = "r" + firstRow.toString() + "c" + i.toString();
            
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd)
                jQuery("#"+sId).toggleClass("highlighted");
        }
    }
    
    //same row backward
    if (firstRow == lastRow && firstCol >= lastCol) {
        for (var i = firstCol; i >= lastCol; i--) {
            sId = "r" + firstRow.toString() + "c" + i.toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd)
                jQuery("#"+sId).toggleClass("highlighted");
        }
    }
    //same col downwards
    if (firstCol == lastCol && firstRow <= lastRow) {
        for (var i = firstRow; i <= lastRow; i++) {
            sId = "r" + i.toString() + "c" + firstCol.toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd)
                jQuery("#"+sId).toggleClass("highlighted");
        }
    }
    //same col upwards
    if (firstCol == lastCol && firstRow >= lastRow) {
        for (var i = firstRow; i >= lastRow; i--) {
            sId = "r" + i.toString() + "c" + firstCol.toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd)
                jQuery("#"+sId).toggleClass("highlighted");
        }
    }
    //diagonal up-left to down-right
     if (lastRow - firstRow == lastCol - firstCol) {
        for (var i = firstRow; i <= lastRow; i++) {
            sId = "r" + i.toString() + "c" + (firstCol+i-firstRow).toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd) {
                jQuery("#"+sId).toggleClass("highlighted");
            }
        }
    }
    //diagonal down-right to up-left
     if (firstRow - lastRow == firstCol - lastCol) {
        for (var i = firstRow; i >= lastRow; i--) {
            sId = "r" + i.toString() + "c" + (lastCol+i-lastRow).toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd) {
                jQuery("#"+sId).toggleClass("highlighted");
            }
        }
    }
    //diagonal down-left to up-right
     if (firstRow - lastRow == lastCol - firstCol) {
        
        for (var i = firstRow; i >= lastRow; i--) {
            sId = "r" + i.toString() + "c" + (lastCol-i+lastRow).toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd) {
                jQuery("#"+sId).toggleClass("highlighted");
            }
        }
    }
    //diagonal up-right to down-left
     if (lastRow - firstRow == firstCol - lastCol) {
        
        for (var i = firstRow; i <= lastRow; i++) {
            sId = "r" + i.toString() + "c" + (firstCol-i+firstRow).toString();
            m_word += document.getElementById(sId).innerHTML;
            if (sId != firstTd) {
                jQuery("#"+sId).toggleClass("highlighted");
            }            
        }
    }
}

function init_grid(words, dim)
{
    m_words = words;
    m_dim = dim;
    m_getscript = jQuery('#var_mp_ws_getscript').text();
    m_size = jQuery('#var_mp_ws_width').text();
    m_showlink = jQuery('#var_mp_ws_showlink').text();
    m_domain =  jQuery('#var_mp_ws_domain').text();
    m_availWidth = window.screen.availWidth;
    m_availHeight = window.screen.availHeight;
    m_screenRatio = m_availWidth / m_availHeight;
    changeLevel();
}

function changeLevel(){
    //jQuery("#ws-table td").removeClass(m_style);
    m_style = "wsSize"+m_dim;
    var htmlGrid = "<table border='0' cellpadding='0' cellspacing='0' id='ws-table'>";
    for (var r = 1; r <= m_dim; r++) {
        htmlGrid += "<tr>";
        for (var c = 1; c <= m_dim; c++) {
             htmlGrid += "<td class='wsCell' id='r"+r.toString()+"c"+c.toString()+"'></td>";
        }
        htmlGrid += "</tr>";
    }
    htmlGrid += "";
    htmlGrid += "</table>";
    //if (m_showlink == '1') {
    var anchor = 'Puzzle';
    var asW = new Array('a', 'b', 'c', 'd', 'e', 'f', '1');
    if (asW.indexOf(m_domain) != -1)
        anchor = 'Puzzles';
    //htmlGrid += "<div style='font-size:10px;text-align:left'><a href='http://mypuzzle.org'>"+anchor+"</a> by MyPuzzle.org</div>";
    //}
    jQuery("#ws-grid").html(htmlGrid);
    jQuery("#ws-table td").addClass('ws_size');
    
    jQuery("#ws-table td").hide().fadeIn(1000)
    init_hook();
    
    m_gameStarted = false
    load_grid();
}

function load_grid()
{
    var sUrl = m_getscript+'?dim='+m_dim+'&words='+m_words;
    jQuery.get(sUrl, function(data) {
        
        var fields = data.split('&');
        var aRows = new Array();
        m_aWords = new Array();
        m_Solved = false;
        var field;
        
        for (var f = 0; f <= fields.length-1; f++) {
            field = fields[f].split('=');
            if (field[0].substring(0,3) == 'row') aRows.push(field[1]);
            if (field[0].substring(0,4) == 'word') m_aWords.push(field[1]);
        }
        var iRows = m_dim;
        var iCols = m_dim;
        for (var r = 1; r <= iRows; r++) {
            for (var c = 1; c <= iCols; c++) {
                var sId = "r" + r.toString() +"c"+c.toString();
                jQuery("#"+sId).html(aRows[r-1].substring(c-1, c));
            }
        }
        jQuery("#ws-table td").hide().fadeIn(1000)
        load_words();
        m_aFindWords = m_aWords.slice(0);
    });
    jQuery("#ws-table td").removeClass("highlighted");
    jQuery("#ws-table td").removeClass("found");
}

function load_words(){
    var htmlWords = "<ul>\n";
    
    for (var i = 0; i < m_aWords.length; i++) {
        htmlWords += "<li id='wordid" +i.toString()+ "'>"+m_aWords[i]+"</li>\n";
    }
    htmlWords += "</ul>\n";
    jQuery('#wordlist').html(htmlWords);
    m_found = 0;
}

function wordFound() {
    if (m_aFindWords==null) return(false);
    var found = false;
    if (contains(m_aFindWords, m_word)) {
        found = true;
        selectFound();
        m_gameStarted = true;
        var sId = "#wordid" + m_iFound.toString();
        jQuery(sId).hide("slow");
        m_aFindWords.splice(m_aFindWords.indexOf(m_word), 1);
        m_found++;
    }
    if (m_aFindWords.length == 0) {
        m_Solved = true;
        m_gameStarted = false;
        var content = jQuery('.ws_popcontent'),
        message = "Congratulations, you solved the puzzle! :-)";
        jQuery('#ws_popup').bPopup({
            onOpen: function() {
                content.html(message);
            }
        });
    }
    return(found);
}
function selectFound(){
    jQuery("#ws-table td").each(function(){
        if (jQuery(this).hasClass("highlighted")) {
            jQuery(this).removeClass("highlighted");
            jQuery(this).addClass("found");
        }
    })
}
function contains(a, obj) {     
    for (var i = 0; i < a.length; i++) {         
        if (a[i] === obj) {     
            m_iFound = m_aWords.indexOf(m_word);
            return true;         
        }     
    }
    return false; 
} 

if(!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(elt
    /*, from*/
    ) {
        var len = this.length >>> 0;
        var from = Number(arguments[1]) || 0;
        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
        if (from < 0)
            from += len;
        for (; from < len; from++) {
            if (from in this && this[from] === elt)
                return from;
        }
        return - 1;
    };
}
String.prototype.capitalize = function() {     
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase(); 
}
