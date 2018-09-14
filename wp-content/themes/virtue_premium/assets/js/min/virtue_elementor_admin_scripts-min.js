!function(t){var e="Close",i="BeforeClose",n="AfterClose",s="BeforeAppend",a="MarkupParse",o="Open",r="Change",c="mfp",l="."+c,p="mfp-ready",d="mfp-removing",u="mfp-prevent-close",h,f=function(){},g=!!window.jQuery,m,v=t(window),y,k,C,b,w,P=function(t,e){h.ev.on(c+t+l,e)},I=function(e,i,n,s){var a=document.createElement("div");return a.className="mfp-"+e,n&&(a.innerHTML=n),s?i&&i.appendChild(a):(a=t(a),i&&a.appendTo(i)),a},x=function(e,i){h.ev.triggerHandler(c+e,i),h.st.callbacks&&(e=e.charAt(0).toLowerCase()+e.slice(1),h.st.callbacks[e]&&h.st.callbacks[e].apply(h,t.isArray(i)?i:[i]))},_=function(e){return e===w&&h.currTemplate.closeBtn||(h.currTemplate.closeBtn=t(h.st.closeMarkup.replace("%title%",h.st.tClose)),w=e),h.currTemplate.closeBtn},S=function(){t.magnificPopup.instance||(h=new f,h.init(),t.magnificPopup.instance=h)},T=function(){var t=document.createElement("p").style,e=["ms","O","Moz","Webkit"];if(void 0!==t.transition)return!0;for(;e.length;)if(e.pop()+"Transition"in t)return!0;return!1};f.prototype={constructor:f,init:function(){var e=navigator.appVersion;h.isIE7=-1!==e.indexOf("MSIE 7."),h.isIE8=-1!==e.indexOf("MSIE 8."),h.isLowIE=h.isIE7||h.isIE8,h.isAndroid=/android/gi.test(e),h.isIOS=/iphone|ipad|ipod/gi.test(e),h.supportsTransition=T(),h.probablyMobile=h.isAndroid||h.isIOS||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent),k=t(document),h.popupsCache={}},open:function(e){y||(y=t(document.body));var i;if(!1===e.isObj){h.items=e.items.toArray(),h.index=0;var n=e.items,s;for(i=0;i<n.length;i++)if(s=n[i],s.parsed&&(s=s.el[0]),s===e.el[0]){h.index=i;break}}else h.items=t.isArray(e.items)?e.items:[e.items],h.index=e.index||0;if(h.isOpen)return void h.updateItemHTML();h.types=[],b="",e.mainEl&&e.mainEl.length?h.ev=e.mainEl.eq(0):h.ev=k,e.key?(h.popupsCache[e.key]||(h.popupsCache[e.key]={}),h.currTemplate=h.popupsCache[e.key]):h.currTemplate={},h.st=t.extend(!0,{},t.magnificPopup.defaults,e),h.fixedContentPos="auto"===h.st.fixedContentPos?!h.probablyMobile:h.st.fixedContentPos,h.st.modal&&(h.st.closeOnContentClick=!1,h.st.closeOnBgClick=!1,h.st.showCloseBtn=!1,h.st.enableEscapeKey=!1),h.bgOverlay||(h.bgOverlay=I("bg").on("click"+l,function(){h.close()}),h.wrap=I("wrap").attr("tabindex",-1).on("click"+l,function(t){h._checkIfClose(t.target)&&h.close()}),h.container=I("container",h.wrap)),h.contentContainer=I("content"),h.st.preloader&&(h.preloader=I("preloader",h.container,h.st.tLoading));var r=t.magnificPopup.modules;for(i=0;i<r.length;i++){var c=r[i];c=c.charAt(0).toUpperCase()+c.slice(1),h["init"+c].call(h)}x("BeforeOpen"),h.st.showCloseBtn&&(h.st.closeBtnInside?(P(a,function(t,e,i,n){i.close_replaceWith=_(n.type)}),b+=" mfp-close-btn-in"):h.wrap.append(_())),h.st.alignTop&&(b+=" mfp-align-top"),h.fixedContentPos?h.wrap.css({overflow:h.st.overflowY,overflowX:"hidden",overflowY:h.st.overflowY}):h.wrap.css({top:v.scrollTop(),position:"absolute"}),(!1===h.st.fixedBgPos||"auto"===h.st.fixedBgPos&&!h.fixedContentPos)&&h.bgOverlay.css({height:k.height(),position:"absolute"}),h.st.enableEscapeKey&&k.on("keyup"+l,function(t){27===t.keyCode&&h.close()}),v.on("resize"+l,function(){h.updateSize()}),h.st.closeOnContentClick||(b+=" mfp-auto-cursor"),b&&h.wrap.addClass(b);var d=h.wH=v.height(),u={};if(h.fixedContentPos&&h._hasScrollBar(d)){var f=h._getScrollbarSize();f&&(u.marginRight=f)}h.fixedContentPos&&(h.isIE7?t("body, html").css("overflow","hidden"):u.overflow="hidden");var g=h.st.mainClass;return h.isIE7&&(g+=" mfp-ie7"),g&&h._addClassToMFP(g),h.updateItemHTML(),x("BuildControls"),t("html").css(u),h.bgOverlay.add(h.wrap).prependTo(h.st.prependTo||y),h._lastFocusedEl=document.activeElement,setTimeout(function(){h.content?(h._addClassToMFP(p),h._setFocus()):h.bgOverlay.addClass(p),k.on("focusin"+l,h._onFocusIn)},16),h.isOpen=!0,h.updateSize(d),x(o),e},close:function(){h.isOpen&&(x(i),h.isOpen=!1,h.st.removalDelay&&!h.isLowIE&&h.supportsTransition?(h._addClassToMFP(d),setTimeout(function(){h._close()},h.st.removalDelay)):h._close())},_close:function(){x(e);var i=d+" "+p+" ";if(h.bgOverlay.detach(),h.wrap.detach(),h.container.empty(),h.st.mainClass&&(i+=h.st.mainClass+" "),h._removeClassFromMFP(i),h.fixedContentPos){var s={marginRight:""};h.isIE7?t("body, html").css("overflow",""):s.overflow="",t("html").css(s)}k.off("keyup.mfp focusin"+l),h.ev.off(l),h.wrap.attr("class","mfp-wrap").removeAttr("style"),h.bgOverlay.attr("class","mfp-bg"),h.container.attr("class","mfp-container"),h.st.showCloseBtn&&(!h.st.closeBtnInside||!0===h.currTemplate[h.currItem.type])&&h.currTemplate.closeBtn&&h.currTemplate.closeBtn.detach(),h._lastFocusedEl&&t(h._lastFocusedEl).focus(),h.currItem=null,h.content=null,h.currTemplate=null,h.prevHeight=0,x(n)},updateSize:function(t){if(h.isIOS){var e=document.documentElement.clientWidth/window.innerWidth,i=window.innerHeight*e;h.wrap.css("height",i),h.wH=i}else h.wH=t||v.height();h.fixedContentPos||h.wrap.css("height",h.wH),x("Resize")},updateItemHTML:function(){var e=h.items[h.index];h.contentContainer.detach(),h.content&&h.content.detach(),e.parsed||(e=h.parseEl(h.index));var i=e.type;if(x("BeforeChange",[h.currItem?h.currItem.type:"",i]),h.currItem=e,!h.currTemplate[i]){var n=!!h.st[i]&&h.st[i].markup;x("FirstMarkupParse",n),h.currTemplate[i]=!n||t(n)}C&&C!==e.type&&h.container.removeClass("mfp-"+C+"-holder");var s=h["get"+i.charAt(0).toUpperCase()+i.slice(1)](e,h.currTemplate[i]);h.appendContent(s,i),e.preloaded=!0,x(r,e),C=e.type,h.container.prepend(h.contentContainer),x("AfterChange")},appendContent:function(t,e){h.content=t,t?h.st.showCloseBtn&&h.st.closeBtnInside&&!0===h.currTemplate[e]?h.content.find(".mfp-close").length||h.content.append(_()):h.content=t:h.content="",x(s),h.container.addClass("mfp-"+e+"-holder"),h.contentContainer.append(h.content)},parseEl:function(e){var i=h.items[e],n;if(i.tagName?i={el:t(i)}:(n=i.type,i={data:i,src:i.src}),i.el){for(var s=h.types,a=0;a<s.length;a++)if(i.el.hasClass("mfp-"+s[a])){n=s[a];break}i.src=i.el.attr("data-mfp-src"),i.src||(i.src=i.el.attr("href"))}return i.type=n||h.st.type||"inline",i.index=e,i.parsed=!0,h.items[e]=i,x("ElementParse",i),h.items[e]},addGroup:function(t,e){var i=function(i){i.mfpEl=this,h._openClick(i,t,e)};e||(e={});var n="click.magnificPopup";e.mainEl=t,e.items?(e.isObj=!0,t.off(n).on(n,i)):(e.isObj=!1,e.delegate?t.off(n).on(n,e.delegate,i):(e.items=t,t.off(n).on(n,i)))},_openClick:function(e,i,n){if((void 0!==n.midClick?n.midClick:t.magnificPopup.defaults.midClick)||2!==e.which&&!e.ctrlKey&&!e.metaKey){var s=void 0!==n.disableOn?n.disableOn:t.magnificPopup.defaults.disableOn;if(s)if(t.isFunction(s)){if(!s.call(h))return!0}else if(v.width()<s)return!0;e.type&&(e.preventDefault(),h.isOpen&&e.stopPropagation()),n.el=t(e.mfpEl),n.delegate&&(n.items=i.find(n.delegate)),h.open(n)}},updateStatus:function(t,e){if(h.preloader){m!==t&&h.container.removeClass("mfp-s-"+m),!e&&"loading"===t&&(e=h.st.tLoading);var i={status:t,text:e};x("UpdateStatus",i),t=i.status,e=i.text,h.preloader.html(e),h.preloader.find("a").on("click",function(t){t.stopImmediatePropagation()}),h.container.addClass("mfp-s-"+t),m=t}},_checkIfClose:function(e){if(!t(e).hasClass(u)){var i=h.st.closeOnContentClick,n=h.st.closeOnBgClick;if(i&&n)return!0;if(!h.content||t(e).hasClass("mfp-close")||h.preloader&&e===h.preloader[0])return!0;if(e===h.content[0]||t.contains(h.content[0],e)){if(i)return!0}else if(n&&t.contains(document,e))return!0;return!1}},_addClassToMFP:function(t){h.bgOverlay.addClass(t),h.wrap.addClass(t)},_removeClassFromMFP:function(t){this.bgOverlay.removeClass(t),h.wrap.removeClass(t)},_hasScrollBar:function(t){return(h.isIE7?k.height():document.body.scrollHeight)>(t||v.height())},_setFocus:function(){(h.st.focus?h.content.find(h.st.focus).eq(0):h.wrap).focus()},_onFocusIn:function(e){if(e.target!==h.wrap[0]&&!t.contains(h.wrap[0],e.target))return h._setFocus(),!1},_parseMarkup:function(e,i,n){var s;n.data&&(i=t.extend(n.data,i)),x(a,[e,i,n]),t.each(i,function(t,i){if(void 0===i||!1===i)return!0;if(s=t.split("_"),s.length>1){var n=e.find(l+"-"+s[0]);if(n.length>0){var a=s[1];"replaceWith"===a?n[0]!==i[0]&&n.replaceWith(i):"img"===a?n.is("img")?n.attr("src",i):n.replaceWith('<img src="'+i+'" class="'+n.attr("class")+'" />'):n.attr(s[1],i)}}else e.find(l+"-"+t).html(i)})},_getScrollbarSize:function(){if(void 0===h.scrollbarSize){var t=document.createElement("div");t.id="mfp-sbm",t.style.cssText="width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;",document.body.appendChild(t),h.scrollbarSize=t.offsetWidth-t.clientWidth,document.body.removeChild(t)}return h.scrollbarSize}},t.magnificPopup={instance:null,proto:f.prototype,modules:[],open:function(e,i){return S(),e=e?t.extend(!0,{},e):{},e.isObj=!0,e.index=i||0,this.instance.open(e)},close:function(){return t.magnificPopup.instance&&t.magnificPopup.instance.close()},registerModule:function(e,i){i.options&&(t.magnificPopup.defaults[e]=i.options),t.extend(this.proto,i.proto),this.modules.push(e)},defaults:{disableOn:0,key:null,midClick:!1,mainClass:"",preloader:!0,focus:"",closeOnContentClick:!1,closeOnBgClick:!0,closeBtnInside:!0,showCloseBtn:!0,enableEscapeKey:!0,modal:!1,alignTop:!1,removalDelay:0,prependTo:null,fixedContentPos:"auto",fixedBgPos:"auto",overflowY:"auto",closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',tClose:"Close (Esc)",tLoading:"Loading..."}},t.fn.magnificPopup=function(e){S();var i=t(this);if("string"==typeof e)if("open"===e){var n,s=g?i.data("magnificPopup"):i[0].magnificPopup,a=parseInt(arguments[1],10)||0;s.items?n=s.items[a]:(n=i,s.delegate&&(n=n.find(s.delegate)),n=n.eq(a)),h._openClick({mfpEl:n},i,s)}else h.isOpen&&h[e].apply(h,Array.prototype.slice.call(arguments,1));else e=t.extend(!0,{},e),g?i.data("magnificPopup",e):i[0].magnificPopup=e,h.addGroup(i,e);return i};var z="inline",O,E,A,B=function(){A&&(E.after(A.addClass(O)).detach(),A=null)};t.magnificPopup.registerModule(z,{options:{hiddenClass:"hide",markup:"",tNotFound:"Content not found"},proto:{initInline:function(){h.types.push(z),P(e+"."+z,function(){B()})},getInline:function(e,i){if(B(),e.src){var n=h.st.inline,s=t(e.src);if(s.length){var a=s[0].parentNode;a&&a.tagName&&(E||(O=n.hiddenClass,E=I(O),O="mfp-"+O),A=s.after(E).detach().removeClass(O)),h.updateStatus("ready")}else h.updateStatus("error",n.tNotFound),s=t("<div>");return e.inlineElement=s,s}return h.updateStatus("ready"),h._parseMarkup(i,{},e),i}}});var M,j=function(){return void 0===M&&(M=void 0!==document.createElement("p").style.MozTransform),M};t.magnificPopup.registerModule("zoom",{options:{enabled:!1,easing:"ease-in-out",duration:300,opener:function(t){return t.is("img")?t:t.find("img")}},proto:{initZoom:function(){var t=h.st.zoom,n=".zoom",s;if(t.enabled&&h.supportsTransition){var a=t.duration,o=function(e){var i=e.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),n="all "+t.duration/1e3+"s "+t.easing,s={position:"fixed",zIndex:9999,left:0,top:0,"-webkit-backface-visibility":"hidden"},a="transition";return s["-webkit-"+a]=s["-moz-"+a]=s["-o-"+a]=s[a]=n,i.css(s),i},r=function(){h.content.css("visibility","visible")},c,l;P("BuildControls"+n,function(){if(h._allowZoom()){if(clearTimeout(c),h.content.css("visibility","hidden"),s=h._getItemToZoom(),!s)return void r();l=o(s),l.css(h._getOffset()),h.wrap.append(l),c=setTimeout(function(){l.css(h._getOffset(!0)),c=setTimeout(function(){r(),setTimeout(function(){l.remove(),s=l=null,x("ZoomAnimationEnded")},16)},a)},16)}}),P(i+n,function(){if(h._allowZoom()){if(clearTimeout(c),h.st.removalDelay=a,!s){if(!(s=h._getItemToZoom()))return;l=o(s)}l.css(h._getOffset(!0)),h.wrap.append(l),h.content.css("visibility","hidden"),setTimeout(function(){l.css(h._getOffset())},16)}}),P(e+n,function(){h._allowZoom()&&(r(),l&&l.remove(),s=null)})}},_allowZoom:function(){return"image"===h.currItem.type},_getItemToZoom:function(){return!!h.currItem.hasSize&&h.currItem.img},_getOffset:function(e){var i;i=e?h.currItem.img:h.st.zoom.opener(h.currItem.el||h.currItem);var n=i.offset(),s=parseInt(i.css("padding-top"),10),a=parseInt(i.css("padding-bottom"),10);n.top-=t(window).scrollTop()-s;var o={width:i.width(),height:(g?i.innerHeight():i[0].offsetHeight)-a-s};return j()?o["-moz-transform"]=o.transform="translate("+n.left+"px,"+n.top+"px)":(o.left=n.left,o.top=n.top),o}}}),S()}(window.jQuery||window.Zepto),function(t){"use strict";function e(e,n){this.element=t(e),this.settings=t.extend({},i,n),this.settings.emptyIcon&&this.settings.iconsPerPage--,this.iconPicker=t("<div/>",{class:"icons-selector",style:"position: relative",html:'<div class="selector"><span class="selected-icon"><i class="fip-icon-block"></i></span><span class="selector-button"><i class="fip-icon-down-dir"></i></span></div><div class="selector-popup" style="display: none;">'+(this.settings.hasSearch?'<div class="selector-search"><input type="text" name="" value="" placeholder="Search icon" class="icons-search-input"/><i class="fip-icon-search"></i></div>':"")+'<div class="selector-category"><select name="" class="icon-category-select" style="display: none"></select></div><div class="fip-icons-container"></div><div class="selector-footer" style="display:none;"><span class="selector-pages">1/2</span><span class="selector-arrows"><span class="selector-arrow-left" style="display:none;"><i class="fip-icon-left-dir"></i></span><span class="selector-arrow-right"><i class="fip-icon-right-dir"></i></span></span></div></div>'}),this.iconContainer=this.iconPicker.find(".fip-icons-container"),this.searchIcon=this.iconPicker.find(".selector-search i"),this.iconsSearched=[],this.isSearch=!1,this.totalPage=1,this.currentPage=1,this.currentIcon=!1,this.iconsCount=0,this.open=!1,this.searchValues=[],this.availableCategoriesSearch=[],this.triggerEvent=null,this.backupSource=[],this.backupSearch=[],this.isCategorized=!1,this.selectCategory=this.iconPicker.find(".icon-category-select"),this.selectedCategory=!1,this.availableCategories=[],this.unCategorizedKey=null,this.init()}var i={theme:"fip-grey",source:!1,emptyIcon:!0,emptyIconValue:"",iconsPerPage:20,hasSearch:!0,searchSource:!1,useAttribute:!1,attributeName:"data-icon",convertToHex:!0,allCategoryText:"From all categories",unCategorizedText:"Uncategorized"};e.prototype={init:function(){this.iconPicker.addClass(this.settings.theme),this.iconPicker.css({left:-9999}).appendTo("body");var e=this.iconPicker.outerHeight(),i=this.iconPicker.outerWidth();if(this.iconPicker.css({left:""}),this.element.before(this.iconPicker),this.element.css({visibility:"hidden",top:0,position:"relative",zIndex:"-1",left:"-"+i+"px",display:"inline-block",height:e+"px",width:i+"px",padding:"0",margin:"0 -"+i+"px 0 0",border:"0 none",verticalAlign:"top"}),!this.element.is("select")){var n=function(){for(var t=3,e=document.createElement("div"),i=e.all||[];e.innerHTML="<!--[if gt IE "+ ++t+"]><br><![endif]-->",i[0];);return t>4?t:!t}(),s=document.createElement("div");this.triggerEvent=9!==n&&"oninput"in s?["input","keyup"]:["keyup"]}!this.settings.source&&this.element.is("select")?(this.settings.source=[],this.settings.searchSource=[],this.element.find("optgroup").length?(this.isCategorized=!0,this.element.find("optgroup").each(t.proxy(function(e,i){var n=this.availableCategories.length,s=t("<option />");s.attr("value",n),s.html(t(i).attr("label")),this.selectCategory.append(s),this.availableCategories[n]=[],this.availableCategoriesSearch[n]=[],t(i).find("option").each(t.proxy(function(e,i){var s=t(i).val(),a=t(i).html();s&&s!==this.settings.emptyIconValue&&(this.settings.source.push(s),this.availableCategories[n].push(s),this.searchValues.push(a),this.availableCategoriesSearch[n].push(a))},this))},this)),this.element.find("> option").length&&this.element.find("> option").each(t.proxy(function(e,i){var n=t(i).val(),s=t(i).html();return!n||""===n||n==this.settings.emptyIconValue||(null===this.unCategorizedKey&&(this.unCategorizedKey=this.availableCategories.length,this.availableCategories[this.unCategorizedKey]=[],this.availableCategoriesSearch[this.unCategorizedKey]=[],t("<option />").attr("value",this.unCategorizedKey).html(this.settings.unCategorizedText).appendTo(this.selectCategory)),this.settings.source.push(n),this.availableCategories[this.unCategorizedKey].push(n),this.searchValues.push(s),void this.availableCategoriesSearch[this.unCategorizedKey].push(s))},this))):this.element.find("option").each(t.proxy(function(e,i){var n=t(i).val(),s=t(i).html();n&&(this.settings.source.push(n),this.searchValues.push(s))},this)),this.backupSource=this.settings.source.slice(0),this.backupSearch=this.searchValues.slice(0),this.loadCategories()):this.initSourceIndex(),this.loadIcons(),this.selectCategory.on("change keyup",t.proxy(function(e){if(!1===this.isCategorized)return!1;var i=t(e.currentTarget),n=i.val();if("all"===i.val())this.settings.source=this.backupSource,this.searchValues=this.backupSearch;else{var s=parseInt(n,10);this.availableCategories[s]&&(this.settings.source=this.availableCategories[s],this.searchValues=this.availableCategoriesSearch[s])}this.resetSearch(),this.loadIcons()},this)),this.iconPicker.find(".selector-button").click(t.proxy(function(){this.toggleIconSelector()},this)),this.iconPicker.find(".selector-arrow-right").click(t.proxy(function(e){this.currentPage<this.totalPage&&(this.iconPicker.find(".selector-arrow-left").show(),this.currentPage=this.currentPage+1,this.renderIconContainer()),this.currentPage===this.totalPage&&t(e.currentTarget).hide()},this)),this.iconPicker.find(".selector-arrow-left").click(t.proxy(function(e){this.currentPage>1&&(this.iconPicker.find(".selector-arrow-right").show(),this.currentPage=this.currentPage-1,this.renderIconContainer()),1===this.currentPage&&t(e.currentTarget).hide()},this)),this.iconPicker.find(".icons-search-input").keyup(t.proxy(function(e){var i=t(e.currentTarget).val();return""===i?void this.resetSearch():(this.searchIcon.removeClass("fip-icon-search"),this.searchIcon.addClass("fip-icon-cancel"),this.isSearch=!0,this.currentPage=1,this.iconsSearched=[],t.grep(this.searchValues,t.proxy(function(t,e){return t.toLowerCase().search(i.toLowerCase())>=0?(this.iconsSearched[this.iconsSearched.length]=this.settings.source[e],!0):void 0},this)),void this.renderIconContainer())},this)),this.iconPicker.find(".selector-search").on("click",".fip-icon-cancel",t.proxy(function(){this.iconPicker.find(".icons-search-input").focus(),this.resetSearch()},this)),this.iconContainer.on("click",".fip-box",t.proxy(function(e){this.setSelectedIcon(t(e.currentTarget).find("i").attr("data-fip-value")),this.toggleIconSelector()},this)),this.iconPicker.click(function(t){return t.stopPropagation(),!1}),t("html").click(t.proxy(function(){this.open&&this.toggleIconSelector()},this))},initSourceIndex:function(){if("object"==typeof this.settings.source){if(t.isArray(this.settings.source))this.isCategorized=!1,this.selectCategory.html("").hide(),this.settings.source=t.map(this.settings.source,function(t){return"function"==typeof t.toString?t.toString():t}),this.searchValues=t.isArray(this.settings.searchSource)?t.map(this.settings.searchSource,function(t){return"function"==typeof t.toString?t.toString():t}):this.settings.source.slice(0);else{var e=t.extend(!0,{},this.settings.source);this.settings.source=[],this.searchValues=[],this.availableCategoriesSearch=[],this.selectedCategory=!1,this.availableCategories=[],this.unCategorizedKey=null,this.isCategorized=!0,this.selectCategory.html("");for(var i in e){var n=this.availableCategories.length,s=t("<option />");s.attr("value",n),s.html(i),this.selectCategory.append(s),this.availableCategories[n]=[],this.availableCategoriesSearch[n]=[];for(var a in e[i]){var o=e[i][a],r=this.settings.searchSource&&this.settings.searchSource[i]&&this.settings.searchSource[i][a]?this.settings.searchSource[i][a]:o;"function"==typeof o.toString&&(o=o.toString()),o&&o!==this.settings.emptyIconValue&&(this.settings.source.push(o),this.availableCategories[n].push(o),this.searchValues.push(r),this.availableCategoriesSearch[n].push(r))}}}this.backupSource=this.settings.source.slice(0),this.backupSearch=this.searchValues.slice(0),this.loadCategories()}},loadCategories:function(){!1!==this.isCategorized&&(t('<option value="all">'+this.settings.allCategoryText+"</option>").prependTo(this.selectCategory),this.selectCategory.show().val("all").trigger("change"))},loadIcons:function(){this.iconContainer.html('<i class="fip-icon-spin3 animate-spin loading"></i>'),this.settings.source instanceof Array&&this.renderIconContainer()},renderIconContainer:function(){var e,i=[];if(i=this.isSearch?this.iconsSearched:this.settings.source,this.iconsCount=i.length,this.totalPage=Math.ceil(this.iconsCount/this.settings.iconsPerPage),this.totalPage>1?this.iconPicker.find(".selector-footer").show():this.iconPicker.find(".selector-footer").hide(),this.iconPicker.find(".selector-pages").html(this.currentPage+"/"+this.totalPage+" <em>("+this.iconsCount+")</em>"),e=(this.currentPage-1)*this.settings.iconsPerPage,this.settings.emptyIcon)this.iconContainer.html('<span class="fip-box"><i class="fip-icon-block" data-fip-value="fip-icon-block"></i></span>');else{if(i.length<1)return void this.iconContainer.html('<span class="icons-picker-error"><i class="fip-icon-block" data-fip-value="fip-icon-block"></i></span>');this.iconContainer.html("")}i=i.slice(e,e+this.settings.iconsPerPage);for(var n,s=0;n=i[s++];){var a=n;t.grep(this.settings.source,t.proxy(function(t,e){return t===n&&(a=this.searchValues[e],!0)},this)),t("<span/>",{html:'<i data-fip-value="'+n+'" '+(this.settings.useAttribute?this.settings.attributeName+'="'+(this.settings.convertToHex?"&#x"+parseInt(n,10).toString(16)+";":n)+'"':'class="'+n+'"')+"></i>",class:"fip-box",title:a}).appendTo(this.iconContainer)}this.settings.emptyIcon||this.element.val()&&-1!==t.inArray(this.element.val(),this.settings.source)?-1===t.inArray(this.element.val(),this.settings.source)?this.setSelectedIcon():this.setSelectedIcon(this.element.val()):this.setSelectedIcon(i[0])},setHighlightedIcon:function(){this.iconContainer.find(".current-icon").removeClass("current-icon"),this.currentIcon&&this.iconContainer.find('[data-fip-value="'+this.currentIcon+'"]').parent("span").addClass("current-icon")},setSelectedIcon:function(t){if("fip-icon-block"===t&&(t=""),this.settings.useAttribute?t?this.iconPicker.find(".selected-icon").html("<i "+this.settings.attributeName+'="'+(this.settings.convertToHex?"&#x"+parseInt(t,10).toString(16)+";":t)+'"></i>'):this.iconPicker.find(".selected-icon").html('<i class="fip-icon-block"></i>'):this.iconPicker.find(".selected-icon").html('<i class="'+(t||"fip-icon-block")+'"></i>'),this.element.val(""===t?this.settings.emptyIconValue:t).trigger("change"),null!==this.triggerEvent)for(var e in this.triggerEvent)this.element.trigger(this.triggerEvent[e]);this.currentIcon=t,this.setHighlightedIcon()},toggleIconSelector:function(){this.open=this.open?0:1,this.iconPicker.find(".selector-popup").slideToggle(300),this.iconPicker.find(".selector-button i").toggleClass("fip-icon-down-dir"),this.iconPicker.find(".selector-button i").toggleClass("fip-icon-up-dir"),this.open&&this.iconPicker.find(".icons-search-input").focus().select()},resetSearch:function(){this.iconPicker.find(".icons-search-input").val(""),this.searchIcon.removeClass("fip-icon-cancel"),this.searchIcon.addClass("fip-icon-search"),this.iconPicker.find(".selector-arrow-left").hide(),this.currentPage=1,this.isSearch=!1,this.renderIconContainer(),this.totalPage>1&&this.iconPicker.find(".selector-arrow-right").show()}},t.fn.fontIconPicker=function(i){return this.each(function(){t.data(this,"fontIconPicker")||t.data(this,"fontIconPicker",new e(this,i))}),this.setIcons=t.proxy(function(e,i){void 0===e&&(e=!1),void 0===i&&(i=!1),this.each(function(){t.data(this,"fontIconPicker").settings.source=e,t.data(this,"fontIconPicker").settings.searchSource=i,t.data(this,"fontIconPicker").initSourceIndex(),t.data(this,"fontIconPicker").resetSearch(),t.data(this,"fontIconPicker").loadIcons()})},this),this.destroyPicker=t.proxy(function(){this.each(function(){t.data(this,"fontIconPicker")&&(t.data(this,"fontIconPicker").iconPicker.remove(),t.data(this,"fontIconPicker").element.css({visibility:"",top:"",position:"",zIndex:"",left:"",display:"",height:"",width:"",padding:"",margin:"",border:"",verticalAlign:""}),t.removeData(this,"fontIconPicker"))})},this),this.refreshPicker=t.proxy(function(n){n||(n=i),this.destroyPicker(),this.each(function(){t.data(this,"fontIconPicker")||t.data(this,"fontIconPicker",new e(this,n))})},this),this}}(jQuery),jQuery(document).ready(function($){function t(){$(".kadenceshortcode-content").find("input:text, input:file, textarea").val(""),$(".kadenceshortcode-content").find("select").removeAttr("selected"," "),$(".kadenceshortcode-content").find("input:radio, input:checkbox").removeAttr("checked").removeAttr("selected"),$(".kadenceshortcode-content").find(".wp-color-result").attr("style","")}function e(t){jQuery(t).find("select.kad_icomoon").fontIconPicker({emptyIcon:!0,iconsPerPage:25})}function n(t){jQuery(t).find(".kad-widget-colorpicker").wpColorPicker({change:_.throttle(function(){jQuery(this).trigger("change")},3e3)})}function s(t,i){n(i),e(i)}function a(t,i){n(".so-content.panel-dialog"),e(".so-content.panel-dialog")}$("body").on("click",".virtue-generator-button",function(){$.magnificPopup.open({mainClass:"mfp-zoom-in",items:{src:"#kadence-shortcode-innercontainer"},type:"inline"})}),$("input.kad-widget-colorpicker").wpColorPicker(),$("input.kad-popup-colorpicker").wpColorPicker(),$("#kad-shortcode-insert").click(function(){var t=$("#kadence-shortcodes").val(),e="";if("columns"==t){var n="";$("#options-"+t+' input[type="radio"]').each(function(){"checked"==$(this).attr("checked")&&(n=$(this).attr("value"))}),e="[columns] ","span6"==n?(e+="[span6] ",e+="<p>add content here</p>",e+="[/span6]",e+="[span6] ",e+="<p>add content here</p>",e+="[/span6]"):"span4left"==n?(e+="[span4] ",e+="<p>add content here</p>",e+="[/span4]",e+="[span8] ",e+="<p>add content here</p>",e+="[/span8]"):"span4right"==n?(e+="[span8] ",e+="<p>add content here</p>",e+="[/span8]",e+="[span4] ",e+="<p>add content here</p>",e+="[/span4]"):"span4"==n?(e+="[span4] ",e+="<p>add content here</p>",e+="[/span4]",e+="[span4] ",e+="<p>add content here</p>",e+="[/span4]",e+="[span4] ",e+="<p>add content here</p>",e+="[/span4]"):"span3"==n&&(e+="[span3] ",e+="<p>add content here</p>",e+="[/span3]",e+="[span3] ",e+="<p>add content here</p>",e+="[/span3]",e+="[span3] ",e+="<p>add content here</p>",e+="[/span3]",e+="[span3] ",e+="<p>add content here</p>",e+="[/span3]"),e+="[/columns]"}else if("table"==t){var s=""!=$("#options-"+t+" .kad-sc-columns").attr("value")?parseInt($("#options-"+t+" .kad-sc-columns").val()):2,a=""!=$("#options-"+t+" .kad-sc-rows").attr("value")?parseInt($("#options-"+t+" .kad-sc-rows").val()):2,o="checked"==$("#options-"+t+" #head").attr("checked");if(e="<table>",o){e+="<thead>",e+="<tr>";var r=1;for(c=0;c<s;c++)e+="<th>Column "+r+"</th>",r++;e+="</tr>",e+="</thead>"}e+="<tbody>";var l=1;for(i=0;i<a;i++){e+="<tr>";var r=1;for(c=0;c<s;c++)e+="<td>Row "+l+", Column "+r+"</td>",r++;l++}e+="</tr>",e+="</tbody>",e+="</table>"}else if("tabs"==t)e="[tabs]",e+='[tab title="title1" start=open] <p>Put content here</p> [/tab]',e+='[tab title="title2"] <p>Put content here</p>[/tab]',e+='[tab title="title3"]<p>Copy and paste to create more</p>[/tab]',e+="[/tabs]";else if("accordion"==t)e="[accordion]",e+='[pane title="title1" start=open] <p>Put content here</p> [/pane]',e+='[pane title="title2"] <p>Put content here</p>[/pane]',e+='[pane title="title3"]<p>Copy and paste to create more</p>[/pane]',e+="[/accordion]";else if("pullquote"==t||"blockquote"==t){var p="",d="";$("#options-"+t+" select").each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("id")+'="'+$(this).attr("value")+'"')}),d=$("#options-"+t+" textarea.kad-sc-content").val(),e="["+t,e+=p,e+="]",e+="<p>"+d+"</p>",e+="[/"+t+"]"}else if("kad_modal"==t){var p="",d="";$("#options-"+t+" select").each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("id")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"]').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].wp-color-picker').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),d=$("#options-"+t+" textarea.kad-sc-content").val(),e="["+t,e+=p,e+="]",e+="<p>"+d+"</p>",e+="[/"+t+"]"}else if("iconbox"==t){var p="",d="",u="";$("#options-"+t+" select").each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("id")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].kad-sc-link').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].wp-color-picker').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),d=$("#options-"+t+" textarea.kad-sc-description").val(),u=$("#options-"+t+' [type="text"].kad-sc-title').attr("value"),e="["+t,e+=p,e+="]",u&&(e+="<h4>"+u+"</h4>"),d&&(e+="<p>"+d+"</p>"),e+="[/"+t+"]"}else if("kt_box"==t){var p="",d="";$("#options-"+t+" select").each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("id")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].attr').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].wp-color-picker').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+" input[type=checkbox]").each(function(){"checked"==$(this).attr("checked")&&(p+=" "+$(this).attr("data-attrname")+'="middle"')}),d=$("#options-"+t+" textarea.kad-sc-content").val(),e="["+t,e+=p,e+="]",e+="<p>"+d+"</p>",e+="[/"+t+"]"}else{var p="",h="",f=" ";$("#options-"+t+' input[type="text"].kad-sc-textinput').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="text"].kad-popup-colorpicker').each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+" select").each(function(){""!=$(this).attr("value")&&(p+=" "+$(this).attr("id")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+' input[type="radio"]').each(function(){"checked"==$(this).attr("checked")&&(p+=" "+$(this).attr("data-attrname")+'="'+$(this).attr("value")+'"')}),$("#options-"+t+" input[type=checkbox]").each(function(){"checked"==$(this).attr("checked")&&(p+=" "+$(this).attr("data-attrname")+'="true"')}),f+=p,e="["+t,e+=f,e+="]"}window.wp.media.editor.insert(e),$.magnificPopup.close()}),$("#kadence-shortcodes").change(function(){$(".shortcode-options").hide(),$("#options-"+$(this).val()).show()}),$("#options-columns input:radio").addClass("input_hidden"),$("#options-columns label").click(function(){$(this).addClass("selected").siblings().removeClass("selected")}),jQuery("#widgets-right .widget:has(select.kad_icomoon)").each(function(){e(jQuery(this))}),jQuery("#widgets-right .widget:has(.kad-widget-colorpicker)").each(function(){n(jQuery(this))}),jQuery(document).on("widget-added widget-updated",s),jQuery(document).on("panelsopen",a)}),jQuery(document).ready(function($){$("select#kadence-shortcodes").select2().on("select2-open",function(){$("body").addClass("kt-select-mask")}).on("select2:open",function(){$("body").addClass("kt-select-mask"),$(".wp-admin .mfp-wrap.kt-mfp-shortcode").removeAttr("tabindex")}).on("select2-close",function(){
$("body").removeClass("kt-select-mask")}).on("select2:close",function(){$("body").removeClass("kt-select-mask")}),$("select.kad-sc-select").select2().on("select2-open",function(){$("body").addClass("kt-select-mask")}).on("select2:open",function(){$("body").addClass("kt-select-mask")}).on("select2-close",function(){$("body").removeClass("kt-select-mask")}).on("select2:close",function(){$("body").removeClass("kt-select-mask")}),$("select.kad-icon-select").fontIconPicker({emptyIcon:!0,iconsPerPage:25})}),function($){"use strict";$.imgupload=$.imgupload||{},$(document).ready(function(){$.imgupload()}),$.imgupload=function(){$("body").on({click:function(t){var e=$(this).closest(".kad_img_upload_widget");if("undefined"!=typeof wp&&wp.media){t.preventDefault();var i,n=$(this);if(i)return void i.open();i=wp.media({multiple:!1,library:{type:"image"}}),i.on("select",function(){var t=i.state().get("selection").first();i.close(),e.find(".kad_custom_media_url").val(t.attributes.url),e.find(".kad_custom_media_id").val(t.attributes.id);var n=t.attributes.url;n=void 0!==t.attributes.sizes&&void 0!==t.attributes.sizes.thumbnail?t.attributes.sizes.thumbnail.url:t.attributes.icon,e.find(".kad_custom_media_image").attr("src",n),e.find(".kad_custom_media_url").trigger("change")}),i.open()}}},".kad_custom_media_upload")}}(jQuery),function($){"use strict";$.imgupload2=$.imgupload2||{},$(document).ready(function(){$.imgupload2()}),$.imgupload2=function(){$("body").on({click:function(t){var e=$(this).closest(".panels-admin-dialog");if("undefined"!=typeof wp&&wp.media){t.preventDefault();var i,n=$(this);if(i)return void i.open();i=wp.media({multiple:!1,library:{type:"image"}}),i.on("select",function(){var t=i.state().get("selection").first();i.close(),e.find('input[data-style-field="background_image"]').val(t.attributes.url)}),i.open()}}},".kad_custom_background_upload")}}(jQuery),function($){"use strict";$.virtuegallery=$.virtuegallery||{},$(document).ready(function(){$.virtuegallery()}),$.virtuegallery=function(){$("body").on({click:function(t){var e=$(this).closest(".kad_widget_image_gallery");if("clear-gallery"===t.currentTarget.id){var i=e.find(".gallery_values").val("");return e.find(".gallery_images").html(""),void e.find(".gallery_values").trigger("change")}if("undefined"!=typeof wp&&wp.media&&wp.media.gallery){t.preventDefault();var n=$(this),s=e.find(".gallery_values").val();wp.media.view.Settings.Gallery=wp.media.view.Settings.Gallery.extend({template:function(t){}});var a;if(s)a='[gallery ids="'+s+'"]',r=wp.media.gallery.edit(a);else var o={frame:"post",state:"gallery",multiple:!0},r=wp.media.editor.open("gallery_values",o);return r.state("gallery-edit").on("update",function(t){r.detach(),e.find(".gallery_images").html("");var i,n="",s,a,o=t.models.map(function(t){return i=t.toJSON(),s=void 0!==i.sizes.thumbnail?i.sizes.thumbnail.url:i.url,a=i.id,n='<a class="of-uploaded-image edit-kt-meta-gal" data-attachment-id="'+a+'" href="#"><img class="gallery-widget-image" src="'+s+'" /></a>',e.find(".gallery_images").append(n),t.id});e.find(".gallery_values").val(o.join(",")),e.find(".gallery_values").trigger("change")}),!1}}},".gallery-attachments")}}(jQuery),function($){"use strict";$.virtue_attachment_gallery=$.virtue_attachment_gallery||{},$(document).ready(function(){$.virtue_attachment_gallery()}),$.virtue_attachment_gallery=function(){$("body").on({click:function(t){var e=$(this).closest(".kad_widget_image_gallery"),i=$(this).data("attachment-id");if("undefined"!=typeof wp&&wp.media&&wp.media.gallery){t.preventDefault(),wp.media.view.Settings.Gallery=wp.media.view.Settings.Gallery.extend({template:function(t){}});var n=$(this),s=e.find(".gallery_values").val(),a='[gallery ids="'+s+'"]';return wp.media.gallery.edit(a).state("gallery-edit").on("update",function(t){e.find(".gallery_images").html("");var i,n="",s,a,o=t.models.map(function(t){return i=t.toJSON(),s=void 0!==i.sizes.thumbnail?i.sizes.thumbnail.url:i.url,a=i.id,n='<a class="of-uploaded-image edit-kt-meta-gal" data-attachment-id="'+a+'" href="#"><img class="gallery-widget-image" src="'+s+'" /></a>',e.find(".gallery_images").append(n),t.id});e.find(".gallery_values").val(o.join(",")),e.find(".gallery_values").trigger("change")}),!1}}},".edit-kt-meta-gal")}}(jQuery);