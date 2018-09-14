var m_dirGallery;
var m_urlGallery;
var m_pathGallery;
var m_urlResize;
var m_pathResize;
var m_urlResizePath;
var m_dirUpload;
var m_urlUpload;
var m_pathUpload;
var m_dirPlugin;
var m_urlUpload;
var m_myImage;
var m_myImageUrl;
var m_hint;
var m_pieces;
var m_width;
var m_height;
var m_maxImgWidth;
var m_maxImgHeight;
var m_bgColor;
var m_flash;
var m_closeButton;
var m_chgImage;
var m_doResize;
var m_debug;
var m_siteurl;

function pictureChange() {
	var e = document.getElementById("selPicture");
	var sPic = e.value;
	rewriteFlashObject(sPic);
}

function rewriteFlashObject(sPic) {
	var e = document.getElementById("flashObject");
	var s = "";
        s += "<object id='myFlash' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'";
	s += " codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'";
	s += " width='"+m_width+"' height='"+m_height+"' align='middle'>";  
	s += "<param name='allowScriptAccess' value='sameDomain' />";
	s += "<param name='allowFullScreen' value='false' />";
	s += "<param name='movie' value='"+m_flash+"' />";
	s += "<param name='flashvars' value='myPic=" + sPic + "&myHint=" + m_hint + "&myPieces=" + m_pieces+"' />";
	s += "<param name='quality' value='high' />";
	s += "<param name='menu' value='false' />";
	s += "<param name='bgcolor' value='"+m_bgColor+"' />";
        s += "<param name='wmode' value='transparent' />";
	s += "<embed src='"+m_flash+"' flashvars='myPic=" + sPic + "&myHint=" + m_hint + "&myPieces=" + m_pieces+"' quality='high' bgcolor='"+m_bgColor+"'  swLiveConnect='true' ";
	s += "    width='"+m_width+"' height='"+m_height+"' name='sliding' menu='false' align='middle' allowScriptAccess='sameDomain' ";
	s += "    allowFullScreen='false' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />";
	s += "</object>";	     
        s += "<div style='width:"+m_width+"px;text-align: right;font-size:12px;'><a href='http://mypuzzle.org/'>Puzzle Games</a></div>";
        e.innerHTML = s;
}

function showGallery() {
        
    m_chgImage = false;
    jQuery('#gallery').bPopup({
        onOpen: function() {
            getFlashVars();
            getData2();
        },
        onClose: function() { 
            if (m_chgImage==true) {
                rewriteFlashObject(m_myImage); 
            }
        }
    });
}

function getFlashVars() {
    m_myImage = jQuery('#flashvar_startPicture').text();
    m_hint = jQuery('#flashvar_hint').text();
    m_pieces = jQuery('#flashvar_pieces').text();
    m_width = jQuery('#flashvar_width').text();
    m_height = jQuery('#flashvar_height').text();
    m_bgColor = jQuery('#flashvar_bgcolor').text();
    m_dirUpload = jQuery('#var_uploadDir').text();
    m_pathUpload = jQuery('#var_uploadPath').text();
    m_urlUpload = jQuery('#var_uploadUrl').text();
    m_dirPlugin = jQuery('#var_plugin').text();
    m_flash = jQuery('#var_flash').text();
    m_dirGallery = jQuery('#var_galleryDir').text();
    m_urlGallery = jQuery('#var_galleryUrl').text();
    m_pathGallery = jQuery('#var_galleryPath').text();
    
    
    m_urlResize = jQuery('#var_resizeUrl').text();
    m_urlResizePath = jQuery('#var_resizePathUrl').text();
    m_pathResize = jQuery('#var_resizePath').text();
    
    m_closeButton = jQuery('#var_closebutton').text();
    m_doResize = jQuery('#var_doresize').text();
    m_debug = jQuery('#var_debug').text();
    m_siteurl = jQuery('#var_siteurl').text();
}

function getData(){
    // getting json data
    var item;
    var sUrl = m_dirPlugin+'/getGallery.php';
    console.log(sUrl);
    jQuery.getJSON(sUrl,function(data){
        
        if (data == null) return;
        jQuery('#image_container').empty();
        jQuery.each(data, function(key, val) {
            item = jQuery('#imgWrapTemplate').clone();
            item.attr({'style': ''});
            item.find('.imageTitle').text(key);
            item.find('img').attr('src',m_dirGallery+'/'+val);
            
            item.find('img').click(function(){     //remove border on any images that might be selected     
                var selImage = jQuery(this).attr("src");
                getResizedImage(selImage);
            });
            jQuery('#image_container').append(item);
        });
        
        return('');
    });//end getJson
}// end getData

function getData2(){
    // getting json data
    var item;
    var sUrl = m_urlGallery+'?dir='+m_dirGallery;
    //console.log(sUrl);
    jQuery.getJSON(sUrl,'callback=?', function(data){
        jQuery('#image_container').empty();
        jQuery.each(data, function(key, val) {
            item = jQuery('#imgWrapTemplate').clone();
            item.attr({'style': ''});
            item.find('.imageTitle').text(key);
            var d = new Date();
            item.find('img').attr('src',m_siteurl + '/' + m_pathGallery+'/'+val);
            
            item.find('img').click(function(){      
                m_myImage = jQuery(this).attr("src");
                //console.log(m_myImage);
                m_chgImage = true;
                if (m_doResize==1) {
                    var imgTitle = jQuery(this).parent().find('.imageTitle').text();
                    //console.log(m_dirGallery+'/'+imgTitle);
                    getResizedImage(m_dirGallery+'/'+imgTitle);
                    
                }
                else
                    jQuery('#gallery').bPopup().close()
                
            });
            jQuery('#image_container').append(item);
        });       
        return('');
    });//end getJson
}// end getData

function getResizedImage(selImage) {
    
    var sUrl = m_urlResize+'?imageUrl='+selImage+'&resizePath='+m_pathResize;
    //console.log(sUrl);
    jQuery.getJSON(sUrl,function(data){
        
        if (data == null) return;

        jQuery.each(data, function(key, val) {
            //console.log(key+"-"+val);
            if (key == 'file') m_myImage = m_urlResizePath + '/' + val;
            //console.log('m_myImage: '+m_myImage);
        });
        jQuery('#gallery').bPopup().close();
    });
}
