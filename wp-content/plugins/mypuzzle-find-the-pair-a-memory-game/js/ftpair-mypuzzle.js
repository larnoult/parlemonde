/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var m_appPath = "/app/find-the-pair/";
var m_galleryPath;
var m_galleryGetPHP;

var m_cards;
var m_cardsize;
var m_openCards;
var m_closedCards;
var m_images;
var m_tries;

var m_ftpair_width;
var m_ftpair_height;
var m_ftpair_pairs;
var m_galleryDir;
var m_galleryPath;
var m_galleryGetPHP;

function ftpair_mp_memory(picturePairs)
{
    ftpair_mp_getVars();
    if (picturePairs == undefined) 
        m_cards = 8;
    else
        m_cards = picturePairs;
    m_openCards = new Array();
    m_closedCards = 0;
    m_tries = 0;
    //log_message("Find all the pairs!");
    ftpair_mp_init_grid();
}   


function ftpair_mp_getVars(){
     m_ftpair_width = jQuery('#var_ftpair_width').text();
     m_ftpair_height = jQuery('#var_ftpair_height').text();
     m_ftpair_pairs = jQuery('#var_ftpair_pairs').text();
     m_galleryPath = jQuery('#var_galleryPath').text();
     m_galleryDir = jQuery('#var_galleryDir').text();
     m_galleryGetPHP = jQuery('#var_galleryGetPHP').text();
}

function ftpair_mp_init_grid()
{
    var item;
    jQuery('#mem-grid').empty();
    m_images = new Array();
    var sUrl = m_galleryGetPHP+'?dir='+m_galleryDir+'&cards='+m_cards;
    
    
    jQuery.getJSON(sUrl,'callback=?', function(data){
        jQuery('#image_container').empty();
        jQuery.each(data, function(key, val) {
            
            m_images.push(val);
            
        });
        m_images.shuffle();
        
        var plot = m_ftpair_width * m_ftpair_height;
        var cards = m_cards * 2;
        var plotCard = plot / cards;
        var squareEdge = Math.sqrt(plotCard)-5;
        var calRow = m_ftpair_width / squareEdge;
        var row = Math.round(calRow);
        
        var m_cardsize = parseInt(m_ftpair_width / row - 5);
        
        //console.log("Divisor for "+allCards.toString()+" is "+divisor(allCards).toString());
        for (var x = 0; x < m_images.length; x++){
            item = jQuery('#imgWrapTemplate').clone();
            item.attr({'style': ''});
            item.find('img').attr('src','/'+m_galleryPath+'/'+m_images[x]);
            item.click(function(){ 
                ftpair_mp_clickCard(jQuery(this));
            });
            jQuery('#mem-grid').append(item);
        }        
        jQuery('.memCard').addClass('selected');
        jQuery('.memCard').css('width', m_cardsize+'px');
        jQuery('.memCard').css('height', m_cardsize+'px');
        jQuery('.memImage').css('width', (m_cardsize-4).toString()+'px');
        jQuery('.memImage').css('height', (m_cardsize-4).toString()+'px');
        ftpair_mp_restCards(2000);
    });
}

function ftpair_mp_clickCard(card){
    
    if (m_openCards.length == 2) return;
    if (card.hasClass("selected")) return;
    if (card.hasClass("empty")) return;
    card.addClass("selected");
    m_openCards.push(card);
    ftpair_mp_checkForPair();
}

function ftpair_mp_checkForPair(){
    if (m_openCards.length != 2) return;
    m_tries++;
    if (m_openCards[0].html() == m_openCards[1].html()){
        m_closedCards++;
        ftpair_mp_closeCards();
    } else {
        
        ftpair_mp_restCards(800);
    }
    var logMessage = "You found " + m_closedCards.toString() + " out of " + m_cards.toString() + " pairs with " + m_tries.toString();
    if (m_tries > 1)
        logMessage += " tries."
    else
        logMessage += " try."
    ftpair_mp_log_message(logMessage);
}

function ftpair_mp_restCards(timeValue){
    setTimeout( function() {
            jQuery('.memCard').removeClass('selected');
            m_openCards = [];
    }, timeValue);
}

function ftpair_mp_closeCards(){
    setTimeout( function() {
        while (m_openCards.length > 0) {          
            m_openCards.shift().removeClass('selected').addClass('empty');
        }
        ftpair_mp_endGame();
    }, 400);
}

function ftpair_mp_endGame(){
    //console.log('memCard count='+jQuery('.memCard').length);
    //console.log('empty count='+jQuery('.empty').length);
    if (jQuery('.memCard').length-1 === jQuery('.empty').length) {
        alert("Félicitations, vous avez réussi !");
		jQuery('#enigmeHolder img').css('opacity', '1');
    //    ftpair_mp_memory(m_cards);
    }
}


function ftpair_mp_log_message(msg) {
    jQuery("#mem_stats").text(msg);
}
/////////////////////////////
// compatibility ////////////
/////////////////////////////
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

Array.prototype.shuffle = function() {
    var s = [];
    while (this.length) s.push(this.splice(Math.random() * this.length, 1));
    while (s.length) this.push(s.pop());
    return this;
}

shuffle2 = function(o){ //v1.0
    for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};

function roundNumber(rnum, rlength) { // Arguments: number to round, number of decimal places
    var newnumber = Math.round(rnum * Math.pow(10, rlength)) / Math.pow(10, rlength);
    return newnumber;
}