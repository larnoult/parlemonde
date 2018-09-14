/*
 * imagesLoaded PACKAGED v3.1.5
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("eventEmitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(this,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function c(e){this.img=e}function f(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];if("IMG"===n.nodeName&&this.addImage(n),n.nodeType&&(1===n.nodeType||9===n.nodeType))for(var i=n.querySelectorAll("img"),r=0,o=i.length;o>r;r++){var s=i[r];this.addImage(s)}}},s.prototype.addImage=function(e){var t=new c(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),c.prototype=new t,c.prototype.check=function(){var e=v[this.img.src]||new f(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},c.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return f.prototype=new t,f.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},f.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},f.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},f.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},f.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},f.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

/* Hammer.JS - v2.0.4 - 2014-09-28
 * http://hammerjs.github.io/
 *
 * Copyright (c) 2014 Jorik Tangelder;
 * Licensed under the MIT license */
!function(a,b,c,d){"use strict";function e(a,b,c){return setTimeout(k(a,c),b)}function f(a,b,c){return Array.isArray(a)?(g(a,c[b],c),!0):!1}function g(a,b,c){var e;if(a)if(a.forEach)a.forEach(b,c);else if(a.length!==d)for(e=0;e<a.length;)b.call(c,a[e],e,a),e++;else for(e in a)a.hasOwnProperty(e)&&b.call(c,a[e],e,a)}function h(a,b,c){for(var e=Object.keys(b),f=0;f<e.length;)(!c||c&&a[e[f]]===d)&&(a[e[f]]=b[e[f]]),f++;return a}function i(a,b){return h(a,b,!0)}function j(a,b,c){var d,e=b.prototype;d=a.prototype=Object.create(e),d.constructor=a,d._super=e,c&&h(d,c)}function k(a,b){return function(){return a.apply(b,arguments)}}function l(a,b){return typeof a==kb?a.apply(b?b[0]||d:d,b):a}function m(a,b){return a===d?b:a}function n(a,b,c){g(r(b),function(b){a.addEventListener(b,c,!1)})}function o(a,b,c){g(r(b),function(b){a.removeEventListener(b,c,!1)})}function p(a,b){for(;a;){if(a==b)return!0;a=a.parentNode}return!1}function q(a,b){return a.indexOf(b)>-1}function r(a){return a.trim().split(/\s+/g)}function s(a,b,c){if(a.indexOf&&!c)return a.indexOf(b);for(var d=0;d<a.length;){if(c&&a[d][c]==b||!c&&a[d]===b)return d;d++}return-1}function t(a){return Array.prototype.slice.call(a,0)}function u(a,b,c){for(var d=[],e=[],f=0;f<a.length;){var g=b?a[f][b]:a[f];s(e,g)<0&&d.push(a[f]),e[f]=g,f++}return c&&(d=b?d.sort(function(a,c){return a[b]>c[b]}):d.sort()),d}function v(a,b){for(var c,e,f=b[0].toUpperCase()+b.slice(1),g=0;g<ib.length;){if(c=ib[g],e=c?c+f:b,e in a)return e;g++}return d}function w(){return ob++}function x(a){var b=a.ownerDocument;return b.defaultView||b.parentWindow}function y(a,b){var c=this;this.manager=a,this.callback=b,this.element=a.element,this.target=a.options.inputTarget,this.domHandler=function(b){l(a.options.enable,[a])&&c.handler(b)},this.init()}function z(a){var b,c=a.options.inputClass;return new(b=c?c:rb?N:sb?Q:qb?S:M)(a,A)}function A(a,b,c){var d=c.pointers.length,e=c.changedPointers.length,f=b&yb&&d-e===0,g=b&(Ab|Bb)&&d-e===0;c.isFirst=!!f,c.isFinal=!!g,f&&(a.session={}),c.eventType=b,B(a,c),a.emit("hammer.input",c),a.recognize(c),a.session.prevInput=c}function B(a,b){var c=a.session,d=b.pointers,e=d.length;c.firstInput||(c.firstInput=E(b)),e>1&&!c.firstMultiple?c.firstMultiple=E(b):1===e&&(c.firstMultiple=!1);var f=c.firstInput,g=c.firstMultiple,h=g?g.center:f.center,i=b.center=F(d);b.timeStamp=nb(),b.deltaTime=b.timeStamp-f.timeStamp,b.angle=J(h,i),b.distance=I(h,i),C(c,b),b.offsetDirection=H(b.deltaX,b.deltaY),b.scale=g?L(g.pointers,d):1,b.rotation=g?K(g.pointers,d):0,D(c,b);var j=a.element;p(b.srcEvent.target,j)&&(j=b.srcEvent.target),b.target=j}function C(a,b){var c=b.center,d=a.offsetDelta||{},e=a.prevDelta||{},f=a.prevInput||{};(b.eventType===yb||f.eventType===Ab)&&(e=a.prevDelta={x:f.deltaX||0,y:f.deltaY||0},d=a.offsetDelta={x:c.x,y:c.y}),b.deltaX=e.x+(c.x-d.x),b.deltaY=e.y+(c.y-d.y)}function D(a,b){var c,e,f,g,h=a.lastInterval||b,i=b.timeStamp-h.timeStamp;if(b.eventType!=Bb&&(i>xb||h.velocity===d)){var j=h.deltaX-b.deltaX,k=h.deltaY-b.deltaY,l=G(i,j,k);e=l.x,f=l.y,c=mb(l.x)>mb(l.y)?l.x:l.y,g=H(j,k),a.lastInterval=b}else c=h.velocity,e=h.velocityX,f=h.velocityY,g=h.direction;b.velocity=c,b.velocityX=e,b.velocityY=f,b.direction=g}function E(a){for(var b=[],c=0;c<a.pointers.length;)b[c]={clientX:lb(a.pointers[c].clientX),clientY:lb(a.pointers[c].clientY)},c++;return{timeStamp:nb(),pointers:b,center:F(b),deltaX:a.deltaX,deltaY:a.deltaY}}function F(a){var b=a.length;if(1===b)return{x:lb(a[0].clientX),y:lb(a[0].clientY)};for(var c=0,d=0,e=0;b>e;)c+=a[e].clientX,d+=a[e].clientY,e++;return{x:lb(c/b),y:lb(d/b)}}function G(a,b,c){return{x:b/a||0,y:c/a||0}}function H(a,b){return a===b?Cb:mb(a)>=mb(b)?a>0?Db:Eb:b>0?Fb:Gb}function I(a,b,c){c||(c=Kb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return Math.sqrt(d*d+e*e)}function J(a,b,c){c||(c=Kb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return 180*Math.atan2(e,d)/Math.PI}function K(a,b){return J(b[1],b[0],Lb)-J(a[1],a[0],Lb)}function L(a,b){return I(b[0],b[1],Lb)/I(a[0],a[1],Lb)}function M(){this.evEl=Nb,this.evWin=Ob,this.allow=!0,this.pressed=!1,y.apply(this,arguments)}function N(){this.evEl=Rb,this.evWin=Sb,y.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function O(){this.evTarget=Ub,this.evWin=Vb,this.started=!1,y.apply(this,arguments)}function P(a,b){var c=t(a.touches),d=t(a.changedTouches);return b&(Ab|Bb)&&(c=u(c.concat(d),"identifier",!0)),[c,d]}function Q(){this.evTarget=Xb,this.targetIds={},y.apply(this,arguments)}function R(a,b){var c=t(a.touches),d=this.targetIds;if(b&(yb|zb)&&1===c.length)return d[c[0].identifier]=!0,[c,c];var e,f,g=t(a.changedTouches),h=[],i=this.target;if(f=c.filter(function(a){return p(a.target,i)}),b===yb)for(e=0;e<f.length;)d[f[e].identifier]=!0,e++;for(e=0;e<g.length;)d[g[e].identifier]&&h.push(g[e]),b&(Ab|Bb)&&delete d[g[e].identifier],e++;return h.length?[u(f.concat(h),"identifier",!0),h]:void 0}function S(){y.apply(this,arguments);var a=k(this.handler,this);this.touch=new Q(this.manager,a),this.mouse=new M(this.manager,a)}function T(a,b){this.manager=a,this.set(b)}function U(a){if(q(a,bc))return bc;var b=q(a,cc),c=q(a,dc);return b&&c?cc+" "+dc:b||c?b?cc:dc:q(a,ac)?ac:_b}function V(a){this.id=w(),this.manager=null,this.options=i(a||{},this.defaults),this.options.enable=m(this.options.enable,!0),this.state=ec,this.simultaneous={},this.requireFail=[]}function W(a){return a&jc?"cancel":a&hc?"end":a&gc?"move":a&fc?"start":""}function X(a){return a==Gb?"down":a==Fb?"up":a==Db?"left":a==Eb?"right":""}function Y(a,b){var c=b.manager;return c?c.get(a):a}function Z(){V.apply(this,arguments)}function $(){Z.apply(this,arguments),this.pX=null,this.pY=null}function _(){Z.apply(this,arguments)}function ab(){V.apply(this,arguments),this._timer=null,this._input=null}function bb(){Z.apply(this,arguments)}function cb(){Z.apply(this,arguments)}function db(){V.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function eb(a,b){return b=b||{},b.recognizers=m(b.recognizers,eb.defaults.preset),new fb(a,b)}function fb(a,b){b=b||{},this.options=i(b,eb.defaults),this.options.inputTarget=this.options.inputTarget||a,this.handlers={},this.session={},this.recognizers=[],this.element=a,this.input=z(this),this.touchAction=new T(this,this.options.touchAction),gb(this,!0),g(b.recognizers,function(a){var b=this.add(new a[0](a[1]));a[2]&&b.recognizeWith(a[2]),a[3]&&b.requireFailure(a[3])},this)}function gb(a,b){var c=a.element;g(a.options.cssProps,function(a,d){c.style[v(c.style,d)]=b?a:""})}function hb(a,c){var d=b.createEvent("Event");d.initEvent(a,!0,!0),d.gesture=c,c.target.dispatchEvent(d)}var ib=["","webkit","moz","MS","ms","o"],jb=b.createElement("div"),kb="function",lb=Math.round,mb=Math.abs,nb=Date.now,ob=1,pb=/mobile|tablet|ip(ad|hone|od)|android/i,qb="ontouchstart"in a,rb=v(a,"PointerEvent")!==d,sb=qb&&pb.test(navigator.userAgent),tb="touch",ub="pen",vb="mouse",wb="kinect",xb=25,yb=1,zb=2,Ab=4,Bb=8,Cb=1,Db=2,Eb=4,Fb=8,Gb=16,Hb=Db|Eb,Ib=Fb|Gb,Jb=Hb|Ib,Kb=["x","y"],Lb=["clientX","clientY"];y.prototype={handler:function(){},init:function(){this.evEl&&n(this.element,this.evEl,this.domHandler),this.evTarget&&n(this.target,this.evTarget,this.domHandler),this.evWin&&n(x(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&o(this.element,this.evEl,this.domHandler),this.evTarget&&o(this.target,this.evTarget,this.domHandler),this.evWin&&o(x(this.element),this.evWin,this.domHandler)}};var Mb={mousedown:yb,mousemove:zb,mouseup:Ab},Nb="mousedown",Ob="mousemove mouseup";j(M,y,{handler:function(a){var b=Mb[a.type];b&yb&&0===a.button&&(this.pressed=!0),b&zb&&1!==a.which&&(b=Ab),this.pressed&&this.allow&&(b&Ab&&(this.pressed=!1),this.callback(this.manager,b,{pointers:[a],changedPointers:[a],pointerType:vb,srcEvent:a}))}});var Pb={pointerdown:yb,pointermove:zb,pointerup:Ab,pointercancel:Bb,pointerout:Bb},Qb={2:tb,3:ub,4:vb,5:wb},Rb="pointerdown",Sb="pointermove pointerup pointercancel";a.MSPointerEvent&&(Rb="MSPointerDown",Sb="MSPointerMove MSPointerUp MSPointerCancel"),j(N,y,{handler:function(a){var b=this.store,c=!1,d=a.type.toLowerCase().replace("ms",""),e=Pb[d],f=Qb[a.pointerType]||a.pointerType,g=f==tb,h=s(b,a.pointerId,"pointerId");e&yb&&(0===a.button||g)?0>h&&(b.push(a),h=b.length-1):e&(Ab|Bb)&&(c=!0),0>h||(b[h]=a,this.callback(this.manager,e,{pointers:b,changedPointers:[a],pointerType:f,srcEvent:a}),c&&b.splice(h,1))}});var Tb={touchstart:yb,touchmove:zb,touchend:Ab,touchcancel:Bb},Ub="touchstart",Vb="touchstart touchmove touchend touchcancel";j(O,y,{handler:function(a){var b=Tb[a.type];if(b===yb&&(this.started=!0),this.started){var c=P.call(this,a,b);b&(Ab|Bb)&&c[0].length-c[1].length===0&&(this.started=!1),this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:tb,srcEvent:a})}}});var Wb={touchstart:yb,touchmove:zb,touchend:Ab,touchcancel:Bb},Xb="touchstart touchmove touchend touchcancel";j(Q,y,{handler:function(a){var b=Wb[a.type],c=R.call(this,a,b);c&&this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:tb,srcEvent:a})}}),j(S,y,{handler:function(a,b,c){var d=c.pointerType==tb,e=c.pointerType==vb;if(d)this.mouse.allow=!1;else if(e&&!this.mouse.allow)return;b&(Ab|Bb)&&(this.mouse.allow=!0),this.callback(a,b,c)},destroy:function(){this.touch.destroy(),this.mouse.destroy()}});var Yb=v(jb.style,"touchAction"),Zb=Yb!==d,$b="compute",_b="auto",ac="manipulation",bc="none",cc="pan-x",dc="pan-y";T.prototype={set:function(a){a==$b&&(a=this.compute()),Zb&&(this.manager.element.style[Yb]=a),this.actions=a.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var a=[];return g(this.manager.recognizers,function(b){l(b.options.enable,[b])&&(a=a.concat(b.getTouchAction()))}),U(a.join(" "))},preventDefaults:function(a){if(!Zb){var b=a.srcEvent,c=a.offsetDirection;if(this.manager.session.prevented)return void b.preventDefault();var d=this.actions,e=q(d,bc),f=q(d,dc),g=q(d,cc);return e||f&&c&Hb||g&&c&Ib?this.preventSrc(b):void 0}},preventSrc:function(a){this.manager.session.prevented=!0,a.preventDefault()}};var ec=1,fc=2,gc=4,hc=8,ic=hc,jc=16,kc=32;V.prototype={defaults:{},set:function(a){return h(this.options,a),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(a){if(f(a,"recognizeWith",this))return this;var b=this.simultaneous;return a=Y(a,this),b[a.id]||(b[a.id]=a,a.recognizeWith(this)),this},dropRecognizeWith:function(a){return f(a,"dropRecognizeWith",this)?this:(a=Y(a,this),delete this.simultaneous[a.id],this)},requireFailure:function(a){if(f(a,"requireFailure",this))return this;var b=this.requireFail;return a=Y(a,this),-1===s(b,a)&&(b.push(a),a.requireFailure(this)),this},dropRequireFailure:function(a){if(f(a,"dropRequireFailure",this))return this;a=Y(a,this);var b=s(this.requireFail,a);return b>-1&&this.requireFail.splice(b,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(a){return!!this.simultaneous[a.id]},emit:function(a){function b(b){c.manager.emit(c.options.event+(b?W(d):""),a)}var c=this,d=this.state;hc>d&&b(!0),b(),d>=hc&&b(!0)},tryEmit:function(a){return this.canEmit()?this.emit(a):void(this.state=kc)},canEmit:function(){for(var a=0;a<this.requireFail.length;){if(!(this.requireFail[a].state&(kc|ec)))return!1;a++}return!0},recognize:function(a){var b=h({},a);return l(this.options.enable,[this,b])?(this.state&(ic|jc|kc)&&(this.state=ec),this.state=this.process(b),void(this.state&(fc|gc|hc|jc)&&this.tryEmit(b))):(this.reset(),void(this.state=kc))},process:function(){},getTouchAction:function(){},reset:function(){}},j(Z,V,{defaults:{pointers:1},attrTest:function(a){var b=this.options.pointers;return 0===b||a.pointers.length===b},process:function(a){var b=this.state,c=a.eventType,d=b&(fc|gc),e=this.attrTest(a);return d&&(c&Bb||!e)?b|jc:d||e?c&Ab?b|hc:b&fc?b|gc:fc:kc}}),j($,Z,{defaults:{event:"pan",threshold:10,pointers:1,direction:Jb},getTouchAction:function(){var a=this.options.direction,b=[];return a&Hb&&b.push(dc),a&Ib&&b.push(cc),b},directionTest:function(a){var b=this.options,c=!0,d=a.distance,e=a.direction,f=a.deltaX,g=a.deltaY;return e&b.direction||(b.direction&Hb?(e=0===f?Cb:0>f?Db:Eb,c=f!=this.pX,d=Math.abs(a.deltaX)):(e=0===g?Cb:0>g?Fb:Gb,c=g!=this.pY,d=Math.abs(a.deltaY))),a.direction=e,c&&d>b.threshold&&e&b.direction},attrTest:function(a){return Z.prototype.attrTest.call(this,a)&&(this.state&fc||!(this.state&fc)&&this.directionTest(a))},emit:function(a){this.pX=a.deltaX,this.pY=a.deltaY;var b=X(a.direction);b&&this.manager.emit(this.options.event+b,a),this._super.emit.call(this,a)}}),j(_,Z,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[bc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.scale-1)>this.options.threshold||this.state&fc)},emit:function(a){if(this._super.emit.call(this,a),1!==a.scale){var b=a.scale<1?"in":"out";this.manager.emit(this.options.event+b,a)}}}),j(ab,V,{defaults:{event:"press",pointers:1,time:500,threshold:5},getTouchAction:function(){return[_b]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime>b.time;if(this._input=a,!d||!c||a.eventType&(Ab|Bb)&&!f)this.reset();else if(a.eventType&yb)this.reset(),this._timer=e(function(){this.state=ic,this.tryEmit()},b.time,this);else if(a.eventType&Ab)return ic;return kc},reset:function(){clearTimeout(this._timer)},emit:function(a){this.state===ic&&(a&&a.eventType&Ab?this.manager.emit(this.options.event+"up",a):(this._input.timeStamp=nb(),this.manager.emit(this.options.event,this._input)))}}),j(bb,Z,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[bc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.rotation)>this.options.threshold||this.state&fc)}}),j(cb,Z,{defaults:{event:"swipe",threshold:10,velocity:.65,direction:Hb|Ib,pointers:1},getTouchAction:function(){return $.prototype.getTouchAction.call(this)},attrTest:function(a){var b,c=this.options.direction;return c&(Hb|Ib)?b=a.velocity:c&Hb?b=a.velocityX:c&Ib&&(b=a.velocityY),this._super.attrTest.call(this,a)&&c&a.direction&&a.distance>this.options.threshold&&mb(b)>this.options.velocity&&a.eventType&Ab},emit:function(a){var b=X(a.direction);b&&this.manager.emit(this.options.event+b,a),this.manager.emit(this.options.event,a)}}),j(db,V,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:2,posThreshold:10},getTouchAction:function(){return[ac]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime<b.time;if(this.reset(),a.eventType&yb&&0===this.count)return this.failTimeout();if(d&&f&&c){if(a.eventType!=Ab)return this.failTimeout();var g=this.pTime?a.timeStamp-this.pTime<b.interval:!0,h=!this.pCenter||I(this.pCenter,a.center)<b.posThreshold;this.pTime=a.timeStamp,this.pCenter=a.center,h&&g?this.count+=1:this.count=1,this._input=a;var i=this.count%b.taps;if(0===i)return this.hasRequireFailures()?(this._timer=e(function(){this.state=ic,this.tryEmit()},b.interval,this),fc):ic}return kc},failTimeout:function(){return this._timer=e(function(){this.state=kc},this.options.interval,this),kc},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==ic&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),eb.VERSION="2.0.4",eb.defaults={domEvents:!1,touchAction:$b,enable:!0,inputTarget:null,inputClass:null,preset:[[bb,{enable:!1}],[_,{enable:!1},["rotate"]],[cb,{direction:Hb}],[$,{direction:Hb},["swipe"]],[db],[db,{event:"doubletap",taps:2},["tap"]],[ab]],cssProps:{userSelect:"none",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}};var lc=1,mc=2;fb.prototype={set:function(a){return h(this.options,a),a.touchAction&&this.touchAction.update(),a.inputTarget&&(this.input.destroy(),this.input.target=a.inputTarget,this.input.init()),this},stop:function(a){this.session.stopped=a?mc:lc},recognize:function(a){var b=this.session;if(!b.stopped){this.touchAction.preventDefaults(a);var c,d=this.recognizers,e=b.curRecognizer;(!e||e&&e.state&ic)&&(e=b.curRecognizer=null);for(var f=0;f<d.length;)c=d[f],b.stopped===mc||e&&c!=e&&!c.canRecognizeWith(e)?c.reset():c.recognize(a),!e&&c.state&(fc|gc|hc)&&(e=b.curRecognizer=c),f++}},get:function(a){if(a instanceof V)return a;for(var b=this.recognizers,c=0;c<b.length;c++)if(b[c].options.event==a)return b[c];return null},add:function(a){if(f(a,"add",this))return this;var b=this.get(a.options.event);return b&&this.remove(b),this.recognizers.push(a),a.manager=this,this.touchAction.update(),a},remove:function(a){if(f(a,"remove",this))return this;var b=this.recognizers;return a=this.get(a),b.splice(s(b,a),1),this.touchAction.update(),this},on:function(a,b){var c=this.handlers;return g(r(a),function(a){c[a]=c[a]||[],c[a].push(b)}),this},off:function(a,b){var c=this.handlers;return g(r(a),function(a){b?c[a].splice(s(c[a],b),1):delete c[a]}),this},emit:function(a,b){this.options.domEvents&&hb(a,b);var c=this.handlers[a]&&this.handlers[a].slice();if(c&&c.length){b.type=a,b.preventDefault=function(){b.srcEvent.preventDefault()};for(var d=0;d<c.length;)c[d](b),d++}},destroy:function(){this.element&&gb(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},h(eb,{INPUT_START:yb,INPUT_MOVE:zb,INPUT_END:Ab,INPUT_CANCEL:Bb,STATE_POSSIBLE:ec,STATE_BEGAN:fc,STATE_CHANGED:gc,STATE_ENDED:hc,STATE_RECOGNIZED:ic,STATE_CANCELLED:jc,STATE_FAILED:kc,DIRECTION_NONE:Cb,DIRECTION_LEFT:Db,DIRECTION_RIGHT:Eb,DIRECTION_UP:Fb,DIRECTION_DOWN:Gb,DIRECTION_HORIZONTAL:Hb,DIRECTION_VERTICAL:Ib,DIRECTION_ALL:Jb,Manager:fb,Input:y,TouchAction:T,TouchInput:Q,MouseInput:M,PointerEventInput:N,TouchMouseInput:S,SingleTouchInput:O,Recognizer:V,AttrRecognizer:Z,Tap:db,Pan:$,Swipe:cb,Pinch:_,Rotate:bb,Press:ab,on:n,off:o,each:g,merge:i,extend:h,inherit:j,bindFn:k,prefixed:v}),typeof define==kb&&define.amd?define(function(){return eb}):"undefined"!=typeof module&&module.exports?module.exports=eb:a[c]=eb}(window,document,"Hammer");

/*
 * Sequence.js
 * The responsive CSS animation framework for creating unique sliders,
 * presentations, banners, and other step-based applications.
 * @ link https://github.com/IanLunn/Sequence
 * @ author IanLunn
 * @ version 2.1.0
 * @ license http://sequencejs.com/licenses/
 * 
 */
function defineSequence(imagesLoaded,Hammer){"use strict";var instances=[],instance=0,Sequence=function(element,options){function isArray(object){return"[object Array]"===Object.prototype.toString.call(object)?!0:!1}function extend(a,b){for(var i in b)a[i]=b[i];return a}function getStyle(element,property){var value;return element.currentStyle?value=element.currentStyle[property]:document.defaultView&&document.defaultView.getComputedStyle&&(value=document.defaultView.getComputedStyle(element,"")[property]),value}function addEvent(element,eventName,handler){if(element.addEventListener)return element.addEventListener(eventName,handler,!1),handler;if(element.attachEvent){var handlerr=function(){handler.call(element)};return element.attachEvent("on"+eventName,handlerr),handlerr}}function removeEvent(element,eventName,handler){element.addEventListener?element.removeEventListener(eventName,handler,!1):element.detachEvent&&element.detachEvent("on"+eventName,handler)}function convertTimeToMs(time){var convertedTime,fraction;return fraction=time.indexOf("ms")>-1?1:1e3,convertedTime="0s"==time?0:parseFloat(time.replace("s",""))*fraction}function hasClass(element,name){return void 0!==element?new RegExp("(\\s|^)"+name+"(\\s|$)").test(element.className):void 0}function addClass(elements,name){var element,elementsLength,i;for(isArray(elements)===!1&&(elementsLength=1,elements=[elements]),elementsLength=elements.length,i=0;elementsLength>i;i++)element=elements[i],hasClass(element,name)===!1&&(element.className+=(element.className?" ":"")+name)}function removeClass(elements,name){var element,elementsLength,i;for(isArray(elements)===!1?(elementsLength=1,elements=[elements]):elementsLength=elements.length,i=0;elementsLength>i;i++)element=elements[i],hasClass(element,name)===!0&&(element.className=element.className.replace(new RegExp("(\\s|^)"+name+"(\\s|$)")," ").replace(/^\s+|\s+$/g,""))}function insideElement(element,cursor){var rect=element.getBoundingClientRect(),inside=!1;return cursor.clientX>=rect.left&&cursor.clientX<=rect.right&&cursor.clientY>=rect.top&&cursor.clientY<=rect.bottom&&(inside=!0),inside}function hasParent(parent,target,previousTarget){if("BODY"===target.nodeName)return!1;if(parent!==target)return previousTarget=target,hasParent(parent,target.parentNode,previousTarget);if(void 0!==previousTarget)for(var topLevel=previousTarget,allTopLevel=parent.getElementsByTagName(topLevel.nodeName),i=allTopLevel.length;i--;)if(topLevel===allTopLevel[i])return i+1}function getHammerDirection(swipeEvents){var swipeDirections=0,hammerDirection=Hammer.DIRECTION_NONE;return(void 0!==swipeEvents.left||void 0!==swipeEvents.right)&&(swipeDirections+=1),(void 0!==swipeEvents.up||void 0!==swipeEvents.down)&&(swipeDirections+=2),1===swipeDirections?hammerDirection=Hammer.DIRECTION_HORIZONTAL:2===swipeDirections?hammerDirection=Hammer.DIRECTION_VERTICAL:3===swipeDirections&&(hammerDirection=Hammer.DIRECTION_ALL),hammerDirection}function addFeatureSupportClasses($el,Modernizr){var prefix="seq-",support="no-touch";Modernizr.touch===!0&&(support="touch"),addClass($el,prefix+support)}var instanceId=element.getAttribute("data-seq-enabled");if(null!==instanceId)return instances[instanceId];element.setAttribute("data-seq-enabled",instance),instance++;var defaults={startingStepId:1,startingStepAnimatesIn:!1,cycle:!0,phaseThreshold:!0,reverseWhenNavigatingBackwards:!1,reverseTimingFunctionWhenNavigatingBackwards:!1,moveActiveStepToTop:!0,animateCanvas:!0,animateCanvasDuration:250,autoPlay:!1,autoPlayInterval:5e3,autoPlayDelay:null,autoPlayDirection:1,autoPlayButton:!0,autoPlayPauseOnHover:!0,navigationSkip:!0,navigationSkipThreshold:250,fadeStepWhenSkipped:!0,fadeStepTime:500,ignorePhaseThresholdWhenSkipped:!1,preventReverseSkipping:!1,nextButton:!0,prevButton:!0,pagination:!0,preloader:!1,preloadTheseSteps:[1],preloadTheseImages:[],hideStepsUntilPreloaded:!1,pausePreloader:!1,keyNavigation:!1,numericKeysGoToSteps:!1,keyEvents:{left:function(sequence){sequence.prev()},right:function(sequence){sequence.next()}},swipeNavigation:!0,swipeEvents:{left:function(sequence){sequence.next()},right:function(sequence){sequence.prev()},up:void 0,down:void 0},swipeHammerOptions:{},hashTags:!1,hashDataAttribute:!1,hashChangesOnFirstStep:!1,fallback:{speed:500}},domThreshold=50,resizeThreshold=100,prefixTranslations={animation:{WebkitAnimation:"-webkit-",animation:""}},Modernizr=function(a,b,c){function z(a){i.cssText=a}function B(a,b){return typeof a===b}function C(a,b){return!!~(""+a).indexOf(b)}function D(a,b){for(var d in a){var e=a[d];if(!C(e,"-")&&i[e]!==c)return"pfx"==b?e:!0}return!1}function E(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:B(f,"function")?f.bind(d||b):f}return!1}function F(a,b,c){var d=a.charAt(0).toUpperCase()+a.slice(1),e=(a+" "+n.join(d+" ")+d).split(" ");return B(b,"string")||B(b,"undefined")?D(e,b):(e=(a+" "+o.join(d+" ")+d).split(" "),E(e,b,c))}var j,v,y,d="2.8.3",e={},f=b.documentElement,g="modernizr",h=b.createElement(g),i=h.style,l=({}.toString," -webkit- -moz- -o- -ms- ".split(" ")),m="Webkit Moz O ms",n=m.split(" "),o=m.toLowerCase().split(" "),p={svg:"http://www.w3.org/2000/svg"},q={},t=[],u=t.slice,w=function(a,c,d,e){var h,i,j,k,l=b.createElement("div"),m=b.body,n=m||b.createElement("body");if(parseInt(d,10))for(;d--;)j=b.createElement("div"),j.id=e?e[d]:g+(d+1),l.appendChild(j);return h=["&#173;",'<style id="s',g,'">',a,"</style>"].join(""),l.id=g,(m?l:n).innerHTML+=h,n.appendChild(l),m||(n.style.background="",n.style.overflow="hidden",k=f.style.overflow,f.style.overflow="hidden",f.appendChild(n)),i=c(l,a),m?l.parentNode.removeChild(l):(n.parentNode.removeChild(n),f.style.overflow=k),!!i},x={}.hasOwnProperty;y=B(x,"undefined")||B(x.call,"undefined")?function(a,b){return b in a&&B(a.constructor.prototype[b],"undefined")}:function(a,b){return x.call(a,b)},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if("function"!=typeof c)throw new TypeError;var d=u.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(u.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(u.call(arguments)))};return e}),q.touch=function(){var c;return"ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch?c=!0:w(["@media (",l.join("touch-enabled),("),g,")","{#modernizr{top:9px;position:absolute}}"].join(""),function(a){c=9===a.offsetTop}),c},q.cssanimations=function(){return F("animationName")},q.csstransforms=function(){return!!F("transform")},q.csstransitions=function(){return F("transition")},q.svg=function(){return!!b.createElementNS&&!!b.createElementNS(p.svg,"svg").createSVGRect};for(var G in q)y(q,G)&&(v=G.toLowerCase(),e[v]=q[G](),t.push((e[v]?"":"no-")+v));return e.addTest=function(a,b){if("object"==typeof a)for(var d in a)y(a,d)&&e.addTest(d,a[d]);else{if(a=a.toLowerCase(),e[a]!==c)return e;b="function"==typeof b?b():b,"undefined"!=typeof enableClasses&&enableClasses&&(f.className+=" "+(b?"":"no-")+a),e[a]=b}return e},z(""),h=j=null,e._version=d,e._prefixes=l,e._domPrefixes=o,e._cssomPrefixes=n,e.testProp=function(a){return D([a])},e.testAllProps=F,e.testStyles=w,e.prefixed=function(a,b,c){return b?F(a,b,c):F(a,"pfx")},e}(window,window.document);Array.prototype.indexOf||(Array.prototype.indexOf=function(searchElement,fromIndex){if(void 0===this||null===this)throw new TypeError('"this" is null or not defined');var length=this.length>>>0;for(fromIndex=+fromIndex||0,Math.abs(fromIndex)===1/0&&(fromIndex=0),0>fromIndex&&(fromIndex+=length,0>fromIndex&&(fromIndex=0));length>fromIndex;fromIndex++)if(this[fromIndex]===searchElement)return fromIndex;return-1});var hidden,visibilityChange;"undefined"!=typeof document.hidden?(hidden="hidden",visibilityChange="visibilitychange"):"undefined"!=typeof document.mozHidden?(hidden="mozHidden",visibilityChange="mozvisibilitychange"):"undefined"!=typeof document.msHidden?(hidden="msHidden",visibilityChange="msvisibilitychange"):"undefined"!=typeof document.webkitHidden&&(hidden="webkitHidden",visibilityChange="webkitvisibilitychange");var self={modernizr:Modernizr};return self.ui={defaultElements:{nextButton:"seq-next",prevButton:"seq-prev",autoPlayButton:"seq-autoplay",pagination:"seq-pagination",preloader:"seq-preloader"},getElements:function(type,option){var element,elements,elementsLength,rel,i,relatedElements=[];for(elements=option===!0?document.querySelectorAll("."+this.defaultElements[type]):document.querySelectorAll(option),elementsLength=elements.length,i=0;elementsLength>i;i++)element=elements[i],rel=element.getAttribute("rel"),(null===rel||rel===self.$container.getAttribute("id"))&&relatedElements.push(element);return relatedElements},show:function(element,duration){self.propertySupport.transitions===!0?(element.style[Modernizr.prefixed("transitionDuration")]=duration+"ms",element.style[Modernizr.prefixed("transitionProperty")]="opacity, "+Modernizr.prefixed("transform"),element.style.opacity=1):self.animationFallback.animate(element,"opacity","",0,1,duration)},hide:function(element,duration,callback){self.propertySupport.transitions===!0?(element.style[Modernizr.prefixed("transitionDuration")]=duration+"ms",element.style[Modernizr.prefixed("transitionProperty")]="opacity, "+Modernizr.prefixed("transform"),element.style.opacity=0):self.animationFallback.animate(element,"opacity","",1,0,duration),void 0!==callback&&(self.hideTimer=setTimeout(function(){callback()},duration))}},self.autoPlay={init:function(){self.isAutoPlayPaused=!1,self.isAutoPlaying=!1},getDelay:function(delay,startDelay,autoPlayInterval){switch(delay){case!0:delay=null===startDelay?autoPlayInterval:startDelay;break;case!1:case void 0:delay=0}return delay},start:function(delay,continuing){if(self.isAutoPlaying===!0||self.isReady===!1)return!1;var options=self.options;return delay=this.getDelay(delay,options.autoPlayDelay,options.autoPlayInterval),void 0===continuing&&self.started(self),addClass(self.$container,"seq-autoplaying"),addClass(self.$autoPlay,"seq-autoplaying"),options.autoPlay=!0,self.isAutoPlaying=!0,self.isAnimating===!1&&(self.autoPlayTimer=setTimeout(function(){1===options.autoPlayDirection?self.next():self.prev()},delay)),!0},stop:function(){return self.options.autoPlay!==!0||self.isAutoPlaying!==!0?!1:(self.options.autoPlay=!1,self.isAutoPlaying=!1,clearTimeout(self.autoPlayTimer),removeClass(self.$container,"seq-autoplaying"),removeClass(self.$autoPlay,"seq-autoplaying"),self.stopped(self),!0)},unpause:function(){return self.isAutoPlayPaused!==!0?!1:(self.isAutoPlayPaused=!1,this.start(!0),!0)},pause:function(){return self.options.autoPlay!==!0?!1:(self.isAutoPlayPaused=!0,this.stop(),!0)}},self.canvas={init:function(id){void 0!==self.$screen&&(self.$screen.style.height="100%",self.$screen.style.width="100%"),self.canvas.getTransformProperties()},getSteps:function(canvas){var stepId,step,i,steps=[],stepElements=canvas.children,stepsLength=stepElements.length;for(self.stepProperties={},i=0;stepsLength>i;i++)step=stepElements[i],stepId=i+1,steps.push(step),self.stepProperties[stepId]={},self.stepProperties[stepId].isActive=!1;return steps},getTransformProperties:function(){var i,step,stepId,canvasTransform;for(i=0;i<self.noOfSteps;i++)step=self.$steps[i],stepId=i+1,canvasTransform={seqX:0,seqY:0,seqZ:0},canvasTransform.seqX+=-1*step.offsetLeft,canvasTransform.seqY+=-1*step.offsetTop,self.stepProperties[stepId].canvasTransform=canvasTransform},move:function(id,animate){if(self.options.animateCanvas===!0){var transforms,duration=0;return animate===!0&&self.firstRun===!1&&(duration=self.options.animateCanvasDuration),self.isFallbackMode===!1&&(transforms=self.stepProperties[id].canvasTransform,self.$canvas.style[Modernizr.prefixed("transitionDuration")]=duration+"ms",self.$canvas.style[Modernizr.prefixed("transform")]="translateX("+transforms.seqX+"px) translateY("+transforms.seqY+"px) translateZ("+transforms.seqZ+"px) "),!0}return!1},removeNoJsClass:function(){if(self.isFallbackMode!==!0)for(var i=0;i<self.$steps.length;i++){var element=self.$steps[i];if(hasClass(element,"seq-in")===!0){var step=i+1;self.animation.resetInheritedSpeed(step),removeClass(element,"seq-in")}}}},self.animation={getPhaseProperties:function(stepId){var el,i,duration,delay,stepElement=self.$steps[stepId-1],stepAnimatedChildren=stepElement.querySelectorAll("*[data-seq]"),stepChildren=stepElement.querySelectorAll("*"),stepChildrenLength=stepChildren.length,watchedDurations=[],watchedDelays=[],watchedLengths=[],durations=[],delays=[],lengths=[];for(i=0;stepChildrenLength>i;i++)el=stepChildren[i],duration=convertTimeToMs(getStyle(el,Modernizr.prefixed("transitionDuration"))),delay=convertTimeToMs(getStyle(el,Modernizr.prefixed("transitionDelay"))),durations.push(duration),delays.push(delay),lengths.push(duration+delay),null!==el.getAttribute("data-seq")&&(watchedDurations.push(duration),watchedDelays.push(delay),watchedLengths.push(duration+delay));var maxDuration=Math.max.apply(Math,durations),maxDelay=Math.max.apply(Math,delays),maxTotal=Math.max.apply(Math,lengths),watchedMaxDuration=Math.max.apply(Math,watchedDurations),watchedMaxDelay=Math.max.apply(Math,watchedDelays),watchedMaxTotal=Math.max.apply(Math,watchedLengths);return{stepId:stepId,stepElement:stepElement,children:stepChildren,animatedChildren:stepAnimatedChildren,watchedTimings:{maxDuration:watchedMaxDuration,maxDelay:watchedMaxDelay,maxTotal:watchedMaxTotal},timings:{maxDuration:maxDuration,maxDelay:maxDelay,maxTotal:maxTotal}}},getPhaseThreshold:function(ignorePhaseThreshold,phaseThresholdOption,isAnimating,currentPhaseDuration){var phaseThresholdTime=0;return isAnimating===!0&&self.options.ignorePhaseThresholdWhenSkipped===!0&&(ignorePhaseThreshold=!0),void 0===ignorePhaseThreshold&&(phaseThresholdOption===!0?phaseThresholdTime=currentPhaseDuration:phaseThresholdOption!==!1&&(phaseThresholdTime=phaseThresholdOption)),phaseThresholdTime},getReversePhaseDelay:function(currentPhaseTotal,nextPhaseTotal,phaseThresholdOption,ignorePhaseThresholdWhenSkippedOption,isAnimating){var phaseDifference=0,current=0,next=0;return phaseThresholdOption===!0||ignorePhaseThresholdWhenSkippedOption!==!1&&isAnimating!==!1||(phaseDifference=currentPhaseTotal-nextPhaseTotal,phaseDifference>0?next=phaseDifference:0>phaseDifference&&(current=Math.abs(phaseDifference))),{next:next,current:current}},moveActiveStepToTop:function(currentElement,nextElement){if(self.options.moveActiveStepToTop===!0){var prevStepElement=self.$steps[self.prevStepId-1],lastStepId=self.noOfSteps-1;prevStepElement.style.zIndex=1,currentElement.style.zIndex=lastStepId,nextElement.style.zIndex=self.noOfSteps}return null},manageNavigationSkip:function(id,nextStepElement){if(self.isFallbackMode!==!0){var i,stepProperties,stepElement,stepId,phaseSkipped;if(self.ui.show(nextStepElement,0),self.options.navigationSkip===!0){if(self.navigationSkipThresholdActive=!0,0!==self.phasesAnimating&&(clearTimeout(self.phaseThresholdTimer),clearTimeout(self.nextPhaseStartedTimer),self.options.fadeStepWhenSkipped===!0))for(i=1;i<=self.noOfSteps;i++)stepProperties=self.stepProperties[i],stepProperties.isActive===!0&&i!==id&&(stepElement=self.$steps[i-1],stepId=i,phaseSkipped={},phaseSkipped.id=stepId,phaseSkipped.element=stepElement,self.phasesSkipped.push(phaseSkipped),self.animation.stepSkipped(stepElement));self.navigationSkipThresholdTimer=setTimeout(function(){self.navigationSkipThresholdActive=!1},self.options.navigationSkipThreshold)}}},stepSkipped:function(stepElement){self.ui.hide(stepElement,self.options.fadeStepTime,function(){})},changeStep:function(id){var stepToAdd="seq-step"+id;if(void 0!==self.currentStepId){var stepToRemove="seq-step"+self.currentStepId;addClass(self.$container,stepToAdd),removeClass(self.$container,stepToRemove)}else addClass(self.$container,stepToAdd)},reverseProperties:function(phaseProperties,phaseDelay,phaseThresholdTime,ignorePhaseThreshold,options){var el,i,duration,delay,total,maxTotal,maxWatchedTotal,animation=this,phaseElements=phaseProperties.children,noOfPhaseElements=phaseElements.length,stepDurations=phaseProperties.timings,timingFunction="",timingFunctionReversed="",totals=[],watchedTotals=[];for(i=0;noOfPhaseElements>i;i++)el=phaseElements[i],duration=convertTimeToMs(getStyle(el,Modernizr.prefixed("transitionDuration"))),delay=convertTimeToMs(getStyle(el,Modernizr.prefixed("transitionDelay"))),total=duration+delay,delay=stepDurations.maxTotal-total,ignorePhaseThreshold!==!0&&(delay+=phaseDelay),total=duration+delay,totals.push(total),null!==el.getAttribute("data-seq")&&watchedTotals.push(total),options.reverseTimingFunctionWhenNavigatingBackwards===!0&&(timingFunction=getStyle(el,Modernizr.prefixed("transitionTimingFunction")),timingFunctionReversed=animation.reverseTimingFunction(timingFunction)),el.style[Modernizr.prefixed("transition")]=duration+"ms "+delay+"ms "+timingFunctionReversed;return maxTotal=Math.max.apply(Math,totals),maxWatchedTotal=Math.max.apply(Math,watchedTotals),setTimeout(function(){animation.domDelay(function(){for(i=0;noOfPhaseElements>i;i++)el=phaseElements[i],el.style[Modernizr.prefixed("transition")]=""})},maxTotal+phaseThresholdTime),maxWatchedTotal},forward:function(id,currentStepElement,nextStepElement,ignorePhaseThreshold,hashTagNav){var currentPhaseProperties,currentPhaseTotal,phaseThresholdTime,animation=this;self.firstRun===!1&&animation.currentPhaseStarted(self.currentStepId),removeClass(nextStepElement,"seq-out"),animation.domDelay(function(){currentPhaseProperties=animation.startAnimateOut(self.currentStepId,currentStepElement,1),currentPhaseTotal=currentPhaseProperties.watchedTimings.maxTotal,phaseThresholdTime=animation.getPhaseThreshold(ignorePhaseThreshold,self.options.phaseThreshold,self.isAnimating,currentPhaseTotal),self.isAnimating=!0,animation.startAnimateIn(id,currentPhaseTotal,nextStepElement,phaseThresholdTime,hashTagNav)})},reverse:function(id,currentStepElement,nextStepElement,ignorePhaseThreshold,hashTagNav){var reversePhaseDelay,currentPhaseProperties,nextPhaseProperties,currentPhaseTotal,nextPhaseTotal,animation=this,phaseThresholdTime=0;addClass(nextStepElement,"seq-out"),animation.domDelay(function(){currentPhaseProperties=animation.getPhaseProperties(self.currentStepId,"current"),nextPhaseProperties=animation.getPhaseProperties(id,"next"),phaseThresholdTime=animation.getPhaseThreshold(ignorePhaseThreshold,self.options.phaseThreshold,self.isAnimating,currentPhaseProperties.timings.maxTotal),reversePhaseDelay=animation.getReversePhaseDelay(currentPhaseProperties.timings.maxTotal,nextPhaseProperties.timings.maxTotal,self.options.phaseThreshold,self.options.ignorePhaseThresholdWhenSkipped,self.isAnimating),currentPhaseTotal=animation.reverseProperties(currentPhaseProperties,reversePhaseDelay.current,0,ignorePhaseThreshold,self.options),nextPhaseTotal=animation.reverseProperties(nextPhaseProperties,reversePhaseDelay.next,phaseThresholdTime,ignorePhaseThreshold,self.options),animation.startAnimateOut(self.currentStepId,currentStepElement,-1,currentPhaseTotal),self.isAnimating=!0,self.firstRun===!1&&animation.currentPhaseStarted(self.currentStepId),animation.startAnimateIn(id,currentPhaseTotal,nextStepElement,phaseThresholdTime,hashTagNav,nextPhaseTotal)})},startAnimateIn:function(id,currentPhaseTotal,nextStepElement,phaseThresholdTime,hashTagNav,nextPhaseTotal){var nextPhaseProperties,stepDuration,animation=this;self.prevStepId=self.currentStepId,self.currentStepId=id,self.firstRun===!0&&self.pagination.update(),self.firstRun===!1||self.options.startingStepAnimatesIn===!0?(self.stepProperties[id].isActive=!0,self.nextPhaseStartedTimer=setTimeout(function(){animation.nextPhaseStarted(id,hashTagNav)},phaseThresholdTime),self.phaseThresholdTimer=setTimeout(function(){addClass(nextStepElement,"seq-in"),removeClass(nextStepElement,"seq-out"),void 0===nextPhaseTotal&&(nextPhaseProperties=self.animation.getPhaseProperties(id,"next"),nextPhaseTotal=nextPhaseProperties.watchedTimings.maxTotal),animation.phaseEnded(id,"next",nextPhaseTotal,animation.nextPhaseEnded),stepDuration=animation.getStepDuration(currentPhaseTotal,nextPhaseTotal,self.options.phaseThreshold),self.stepEndedTimer=setTimeout(function(){self.animation.stepEnded(id)},stepDuration)},phaseThresholdTime)):(animation.resetInheritedSpeed(id),self.phasesAnimating=0,self.isAnimating=!1,self.options.autoPlay===!0&&self.autoPlay.start(!0),addClass(nextStepElement,"seq-in"),removeClass(nextStepElement,"seq-out")),self.firstRun=!1},startAnimateOut:function(id,currentStepElement,direction,currentPhaseTotal){var currentPhaseProperties,animation=this;return 1===direction?(removeClass(currentStepElement,"seq-in"),addClass(currentStepElement,"seq-out"),currentPhaseProperties=animation.getPhaseProperties(id,"current"),currentPhaseTotal=currentPhaseProperties.watchedTimings.maxTotal):removeClass(currentStepElement,"seq-in"),self.firstRun===!1&&(self.stepProperties[id].isActive=!0,animation.phaseEnded(id,"current",currentPhaseTotal,animation.currentPhaseEnded)),currentPhaseProperties},getStepDuration:function(currentPhaseTotal,nextPhaseTotal,phaseThresholdOption){var stepDuration;switch(phaseThresholdOption){case!0:stepDuration=nextPhaseTotal;break;case!1:stepDuration=currentPhaseTotal>=nextPhaseTotal?currentPhaseTotal:nextPhaseTotal;break;default:stepDuration=currentPhaseTotal>=nextPhaseTotal+phaseThresholdOption?currentPhaseTotal-phaseThresholdOption:nextPhaseTotal}return stepDuration},currentPhaseStarted:function(id){self.phasesAnimating++,self.currentPhaseStarted(id,self)},currentPhaseEnded:function(id){self.currentPhaseEnded(id,self)},nextPhaseStarted:function(id,hashTagNav){self.phasesAnimating++,void 0===hashTagNav&&self.hashTags.update(),self.pagination.update(),self.nextPhaseStarted(id,self)},nextPhaseEnded:function(id){self.nextPhaseEnded(id,self)},phaseEnded:function(id,phase,phaseDuration,callback){var phaseEnded;phaseEnded=function(id){self.stepProperties[id].isActive=!1,self.phasesAnimating--,callback(id)},"current"===phase?self.currentPhaseEndedTimer=setTimeout(function(){phaseEnded(id)},phaseDuration):self.nextPhaseEndedTimer=setTimeout(function(){phaseEnded(id)},phaseDuration)},stepEnded:function(id){self.isAnimating=!1,self.isAutoPlaying=!1,self.options.autoPlay===!0&&self.autoPlay.start(!0,!0),self.animationEnded(id,self)},reversePhase:function(phase){var reversePhase={"seq-out":"seq-in","seq-in":"seq-out"};return reversePhase[phase]},domDelay:function(callback){setTimeout(function(){callback()},domThreshold)},reverseTimingFunction:function(timingFunction){if(""===timingFunction||void 0===timingFunction)return timingFunction;var cubicBezier,cubicBezierLength,reversedCubicBezier,i,timingFunctionToCubicBezier={linear:"cubic-bezier(0.0,0.0,1.0,1.0)",ease:"cubic-bezier(0.25, 0.1, 0.25, 1.0)","ease-in":"cubic-bezier(0.42, 0.0, 1.0, 1.0)","ease-in-out":"cubic-bezier(0.42, 0.0, 0.58, 1.0)","ease-out":"cubic-bezier(0.0, 0.0, 0.58, 1.0)"};for(timingFunction.indexOf("cubic-bezier")<0&&(timingFunction=timingFunction.split(",")[0],timingFunction=timingFunctionToCubicBezier[timingFunction]),cubicBezier=timingFunction.replace("cubic-bezier(","").replace(")","").split(","),cubicBezierLength=cubicBezier.length,i=0;cubicBezierLength>i;i++)cubicBezier[i]=parseFloat(cubicBezier[i]);return reversedCubicBezier=[1-cubicBezier[2],1-cubicBezier[3],1-cubicBezier[0],1-cubicBezier[1]],timingFunction="cubic-bezier("+reversedCubicBezier+")"},resetInheritedSpeed:function(step){if(self.isFallbackMode!==!0){var el,i,stepElements=self.$steps[step-1].querySelectorAll("*"),numberOfStepElements=stepElements.length;for(i=0;numberOfStepElements>i;i++)el=stepElements[i],el.style[Modernizr.prefixed("transition")]="0ms 0ms";self.animation.domDelay(function(){for(i=0;numberOfStepElements>i;i++)el=stepElements[i],el.style[Modernizr.prefixed("transition")]=""})}},getDirection:function(id,definedDirection,currentStepId,noOfSteps,isFallbackMode,reverseWhenNavigatingBackwardsOption,cycleOption){var direction=1;return direction=void 0!==definedDirection?definedDirection:reverseWhenNavigatingBackwardsOption===!0||isFallbackMode===!0?cycleOption===!0&&1===id&&currentStepId===noOfSteps?1:currentStepId>id?-1:1:1},requiresFallbackMode:function(propertySupport){var transitions=propertySupport.transitions,isFallbackMode=!1;return transitions===!1&&(isFallbackMode=!0),isFallbackMode},getPropertySupport:function(properties){var transitions=!1,animations=!1;return Modernizr.csstransitions===!0&&(transitions=!0),Modernizr.cssanimations===!0&&(animations=!0),{transitions:transitions,animations:animations}}},self.animationFallback={animate:function(element,style,unit,from,to,time,callback){if(element!==!1){var start=(new Date).getTime(),timer=setInterval(function(){var step=Math.min(1,((new Date).getTime()-start)/time);element.style[style]=from+step*(to-from)+unit,1===step&&(void 0!==callback&&callback(),clearInterval(timer))},25);element.style[style]=from+unit}},setupCanvas:function(id){var i,step,stepId;if(self.isFallbackMode===!0)for(addClass(self.$container,"seq-fallback"),void 0!==self.$screen&&(self.$screen.style.overflow="hidden",self.$screen.style.width="100%",self.$screen.style.height="100%"),self.$canvas.style.width="100%",self.$canvas.style.height="100%",this.canvasWidth=self.$canvas.offsetWidth,i=0;i<self.noOfSteps;i++)step=self.$steps[i],stepId=i+1,addClass(step,"seq-in"),step.style.width="100%",step.style.height="100%",step.style.position="absolute",step.style.whiteSpace="normal",step.style.left="100%"},moveCanvas:function(nextStepElement,currentStepElement,direction,animate){if(animate===!0){var currentFrom=0,currentTo=-100,nextFrom=100,nextTo=0;-1===direction&&(currentTo=100,nextFrom=-100),this.animate(currentStepElement,"left","%",currentFrom,currentTo,self.options.fallback.speed),this.animate(nextStepElement,"left","%",nextFrom,nextTo,self.options.fallback.speed)}else currentStepElement.style.left="-100%",nextStepElement.style.left="0"},goTo:function(id,currentStep,currentStepElement,nextStep,nextStepElement,direction,hashTagNav){self.prevStepId=self.currentStepId,self.currentStepId=id,void 0===hashTagNav&&self.hashTags.update(),self.pagination.update(),self.firstRun===!1?(this.moveCanvas(nextStepElement,currentStepElement,direction,!0),self.isAnimating=!0,self.animationStarted(self.currentStepId,self),self.stepEndedTimer=setTimeout(function(){self.animation.stepEnded(self.currentStepId)},self.options.fallback.speed)):(this.moveCanvas(nextStepElement,currentStepElement,direction,!1),self.firstRun=!1,self.options.autoPlay===!0&&self.autoPlay.start(!0))}},self.pagination={getLinks:function(element,rel){var childElement,i,childElements=element.childNodes,childElementsLength=childElements.length,paginationLinks=[];for(i=0;childElementsLength>i;i++)childElement=childElements[i],1===childElement.nodeType&&paginationLinks.push(childElement);return paginationLinks},update:function(){if(void 0!==self.$pagination.elements){var i,j,currentPaginationLink,currentPaginationLinksLength,id=self.currentStepId-1,paginationLength=self.$pagination.elements.length;if(void 0!==self.$pagination.currentLinks)for(currentPaginationLinksLength=self.$pagination.currentLinks.length,i=0;currentPaginationLinksLength>i;i++)currentPaginationLink=self.$pagination.currentLinks[i],removeClass(currentPaginationLink,"seq-current");for(self.$pagination.currentLinks=[],j=0;paginationLength>j;j++)currentPaginationLink=self.$pagination.links[j][id],self.$pagination.currentLinks.push(currentPaginationLink),addClass(currentPaginationLink,"seq-current")}return self.$pagination.currentLinks}},self.hashTags={init:function(id){if(self.options.hashTags===!0){var correspondingStepId,newHashTag;self.hasPushstate=!(!window.history||!history.pushState),newHashTag=location.hash.replace("#!",""),self.stepHashTags=this.getStepHashTags(),""!==newHashTag&&(self.currentHashTag=newHashTag,correspondingStepId=this.hasCorrespondingStep(self.currentHashTag),correspondingStepId>-1&&(id=correspondingStepId+1))}return id},hasCorrespondingStep:function(hashTag){var correspondingStep=-1,correspondingStepId=self.stepHashTags.indexOf(hashTag);return correspondingStepId>-1&&(correspondingStep=correspondingStepId),correspondingStep},getStepHashTags:function(){var i,elementHashTag,stepHashTags=[];for(i=0;i<self.noOfSteps;i++)elementHashTag=self.options.hashDataAttribute===!1?self.$steps[i].id:self.$steps[i].getAttribute("data-seq-hashtag"),stepHashTags.push(elementHashTag);return stepHashTags},update:function(){if(self.options.hashTags===!0&&self.firstRun===!1||self.options.hashTags===!0&&self.firstRun===!0&&self.options.hashChangesOnFirstStep===!0){var hashTagId=self.currentStepId-1;self.currentHashTag=self.stepHashTags[hashTagId],""!==self.currentHashtag&&(self.hasPushstate===!0?history.pushState(null,null,"#!"+self.currentHashTag):location.hash="#!"+self.currentHashTag)}},setupEvent:function(){if("onhashchange"in window){if(window.addEventListener)return window.addHashChange=function(func,before){window.addEventListener("hashchange",func,before)},void(window.removeHashChange=function(func){window.removeEventListener("hashchange",func)});if(window.attachEvent)return window.addHashChange=function(func){window.attachEvent("onhashchange",func)},void(window.removeHashChange=function(func){window.detachEvent("onhashchange",func)})}var hashChangeFuncs=[],oldHref=location.href;window.addHashChange=function(func,before){"function"==typeof func&&hashChangeFuncs[before?"unshift":"push"](func)},window.removeHashChange=function(func){for(var i=hashChangeFuncs.length-1;i>=0;i--)hashChangeFuncs[i]===func&&hashChangeFuncs.splice(i,1)},setInterval(function(){var newHref=location.href;if(oldHref!==newHref){var _oldHref=oldHref;oldHref=newHref;for(var i=0;i<hashChangeFuncs.length;i++)hashChangeFuncs[i].call(window,{type:"hashchange",newURL:newHref,oldURL:_oldHref})}},100)}},self.preload={defaultHtml:'<svg width="39" height="16" viewBox="0 0 39 16" xmlns="http://www.w3.org/2000/svg" class="seq-preload-indicator"><title>Sequence.js Preloading Indicator</title><desc>Three orange dots increasing in size from left to right</desc><g fill="#F96D38"><path class="seq-preload-circle seq-preload-circle-1" d="M3.999 12.012c2.209 0 3.999-1.791 3.999-3.999s-1.79-3.999-3.999-3.999-3.999 1.791-3.999 3.999 1.79 3.999 3.999 3.999z"/><path class="seq-preload-circle seq-preload-circle-2" d="M15.996 13.468c3.018 0 5.465-2.447 5.465-5.466 0-3.018-2.447-5.465-5.465-5.465-3.019 0-5.466 2.447-5.466 5.465 0 3.019 2.447 5.466 5.466 5.466z"/><path class="seq-preload-circle seq-preload-circle-3" d="M31.322 15.334c4.049 0 7.332-3.282 7.332-7.332 0-4.049-3.282-7.332-7.332-7.332s-7.332 3.283-7.332 7.332c0 4.05 3.283 7.332 7.332 7.332z"/></g></svg>',fallbackHtml:'<div class="seq-preload-indicator seq-preload-indicator-fallback"><div class="seq-preload-circle seq-preload-circle-1"></div><div class="seq-preload-circle seq-preload-circle-2"></div><div class="seq-preload-circle seq-preload-circle-3"></div></div>',defaultStyles:"@"+prefixTranslations.animation[Modernizr.prefixed("animation")]+"keyframes seq-preloader {50% {opacity: 1;}100% {opacity: 0;}}.seq-preloader {background: white;visibility: visible;opacity: 1;position: absolute;z-index: 9999;height: 100%;width: 100%;top: 0;left: 0;right: 0;bottom: 0;} .seq-preloader.seq-preloaded {opacity: 0;visibility: hidden;"+Modernizr.prefixed("transition")+": visibility 0s .5s, opacity .5s;}.seq-preload-indicator {overflow: visible;position: relative;top: 50%;left: 50%;-webkit-transform: translate(-50%, -50%);transform: translate(-50%, -50%);}.seq-preload-circle {display: inline-block;height: 12px;width: 12px;fill: #F96D38;opacity: 0;"+prefixTranslations.animation[Modernizr.prefixed("animation")]+"animation: seq-preloader 1.25s infinite;}.seq-preload-circle-2 {"+prefixTranslations.animation[Modernizr.prefixed("animation")]+"animation-delay: .15s;}.seq-preload-circle-3 {"+prefixTranslations.animation[Modernizr.prefixed("animation")]+"animation-delay: .3s;}.seq-preload-indicator-fallback{width: 42px;margin-left: -21px;overflow: visible;}.seq-preload-indicator-fallback .seq-preload-circle {width: 8px; height:8px;background-color: #F96D38;border-radius: 100%; opacity: 1;display: inline-block; vertical-align: middle;}.seq-preload-indicator-fallback .seq-preload-circle-2{margin-left: 3px; margin-right: 3px; width: 12px; height: 12px;}.seq-preload-indicator-fallback .seq-preload-circle-3{width: 16px; height: 16px;}",
init:function(callback){if(self.options.preloader!==!1){var stepImagesToPreload,individualImagesToPreload,imagesToPreload,imgLoad,progress,result,preload=this;return addClass(self.$container,"seq-preloading"),self.$preloader=self.ui.getElements("preloader",self.options.preloader),preload.append(),preload.addStyles(),preload.toggleStepsVisibility("hide"),stepImagesToPreload=preload.getImagesToPreload(self.options.preloadTheseSteps),individualImagesToPreload=preload.getImagesToPreload(self.options.preloadTheseImages,!0),imagesToPreload=stepImagesToPreload.concat(individualImagesToPreload),imgLoad=imagesLoaded(imagesToPreload),imgLoad.on("always",function(instance){preload.complete(callback)}),progress=1,imgLoad.on("progress",function(instance,image){result=image.isLoaded?"loaded":"broken",self.preloadProgress(result,image.img.src,progress++,imagesToPreload.length,self)}),!0}return!1},complete:function(callback){self.preloaded(self),self.options.pausePreloader!==!0&&(this.toggleStepsVisibility("show"),removeClass(self.$container,"seq-preloading"),addClass(self.$container,"seq-preloaded"),addClass(self.$preloader[0],"seq-preloaded"),void 0!==this.preloadIndicatorTimer&&clearInterval(this.preloadIndicatorTimer),(Modernizr.prefixed("animation")===!1||Modernizr.svg===!1)&&(self.$preloader[0].style.display="none"),void 0!==callback&&callback())},addStyles:function(){if(self.options.preloader===!0){var head=document.head||document.getElementsByTagName("head")[0];if(this.styleElement=document.createElement("style"),this.styleElement.type="text/css",this.styleElement.styleSheet?this.styleElement.styleSheet.cssText=this.defaultStyles:this.styleElement.appendChild(document.createTextNode(this.defaultStyles)),head.appendChild(this.styleElement),Modernizr.prefixed("animation")===!1||Modernizr.svg===!1){var preloadIndicator=self.$preloader[0].firstChild,indicatorFlash=function(){preloadIndicator.style.visibility="hidden",preloadFlashTime=1e3,setTimeout(function(){preloadIndicator.style.visibility="visible"},500)};indicatorFlash(),this.preloadIndicatorTimer=setInterval(function(){indicatorFlash()},1e3)}return!0}return!1},removeStyles:function(){this.styleElement.parentNode.removeChild(this.styleElement)},getImagesToPreload:function(elements,srcOnly){var imagesToPreload=[];if(isArray(elements)===!0){var i,j,step,imagesInStep,imagesInStepLength,image,img,src,elementLength=elements.length;if(srcOnly!==!0)for(i=0;elementLength>i;i++)for(step=self.$steps[i],imagesInStep=step.getElementsByTagName("img"),imagesInStepLength=imagesInStep.length,j=0;imagesInStepLength>j;j++)image=imagesInStep[j],imagesToPreload.push(image);else for(img=[],i=0;elementLength>i;i++)src=elements[i],img[i]=new Image,img[i].src=src,imagesToPreload.push(img[i])}return imagesToPreload},append:function(){return self.options.preloader===!0?(self.$preloader=document.createElement("div"),self.$preloader.className="seq-preloader",self.$preloader=[self.$preloader],Modernizr.prefixed("animation")!==!1&&Modernizr.svg===!0?self.$preloader[0].innerHTML=self.preload.defaultHtml:self.$preloader[0].innerHTML=self.preload.fallbackHtml,self.$container.insertBefore(self.$preloader[0],null),!0):!1},toggleStepsVisibility:function(type){if(self.options.hideStepsUntilPreloaded===!0){var i,step;for(i=0;i<self.noOfSteps;i++)step=self.$steps[i],"hide"===type?self.ui.hide(step,0):self.ui.show(step,0);return!0}return!1}},self.manageEvents={list:{load:[],click:[],touchstart:[],mousemove:[],mouseleave:[],hammer:[],keyup:[],hashchange:[],resize:[]},init:function(){return self.manageEvents.list[visibilityChange]=[],self.options.hashTags===!0&&this.add.hashChange(),self.options.swipeNavigation===!0?this.add.swipeNavigation():self.hammerTime=!1,self.options.keyNavigation===!0&&this.add.keyNavigation(),this.add.resizeThrottle(),this.add.pageVisibility(),self.options.nextButton!==!1&&(self.$next=self.ui.getElements("nextButton",self.options.nextButton),this.add.button(self.$next,"nav",self.next)),self.options.prevButton!==!1&&(self.$prev=self.ui.getElements("prevButton",self.options.prevButton),this.add.button(self.$prev,"nav",self.prev)),self.options.autoPlayButton!==!1&&(self.$autoPlay=self.ui.getElements("autoPlayButton",self.options.autoPlayButton),this.add.button(self.$autoPlay,"nav",self.toggleAutoPlay)),this.add.stopOnHover(),self.options.pagination!==!1&&(self.$pagination={},self.$pagination.relatedElementId=instance,self.$pagination.links=[],self.$pagination.elements=self.ui.getElements("pagination",self.options.pagination),this.add.button(self.$pagination.elements,"pagination")),null},removeAll:function(eventList){var eventType,theEvents;for(eventType in eventList)eventList.hasOwnProperty(eventType)===!0&&(theEvents=eventList[eventType],this.remove(eventType));return null},remove:function(type){var eventElements=self.manageEvents.list[type],eventElementsLength=eventElements.length;switch(type){case"hashchange":self.options.hashTags===!0&&removeHashChange(eventElements[0].handler);break;case"hammer":if(self.manageEvents.list.hammer.length>0&&void 0!==document.querySelectorAll){var handler=self.manageEvents.list.hammer[0].handler;self.hammerTime.off("swipe",[handler])}break;default:for(var i=0;eventElementsLength>i;i++){var eventProperties=eventElements[i];removeEvent(eventProperties.element,type,eventProperties.handler)}}},add:{hashChange:function(){self.hashTags.setupEvent();var handler=function(e){var newHashTag,id;newHashTag=e.newURL||location.href,newHashTag=newHashTag.split("#!")[1],self.currentHashTag!==newHashTag&&(id=self.stepHashTags.indexOf(newHashTag)+1,self.currentHashTag=newHashTag,self.goTo(id,void 0,void 0,!0))};return addHashChange(handler),self.manageEvents.list.hashchange.push({element:window,handler:handler}),self.manageEvents.list.hashchange},button:function(elements,type,callback){var handler,element,buttonEvent,parent,rel,id,i,elementsLength=elements.length;switch(type){case"nav":buttonEvent=function(element){handler=addEvent(element,"click",function(event){event||(event=window.event),event.preventDefault?event.preventDefault():event.returnValue=!1,callback()})};break;case"pagination":buttonEvent=function(element,rel){handler=addEvent(element,"click",function(event,element){event||(event=window.event),event.preventDefault?event.preventDefault():event.returnValue=!1;var targetElement=event.target||event.srcElement;parent=this,id=hasParent(parent,targetElement),self.goTo(id)}),self.$pagination.links.push(self.pagination.getLinks(element,rel))}}for(i=0;elementsLength>i;i++)element=elements[i],rel=element.getAttribute("rel"),rel===self.$container.id&&null===element.getAttribute("data-seq-button")?(element.setAttribute("data-seq-button",!0),buttonEvent(element,rel)):null===rel&&null===element.getAttribute("data-seq-button")&&buttonEvent(element,rel),self.manageEvents.list.click.push({element:element,handler:handler});return self.manageEvents.list.click},stopOnHover:function(){var touchHandler,handler;return self.isMouseOver=!1,touchHandler=addEvent(self.$container,"touchstart",function(e){self.isTouched=!0}),self.manageEvents.list.touchstart.push({element:self.$container,handler:touchHandler}),handler=addEvent(self.$container,"mousemove",function(e){return e=e||window.event,self.isTouched===!0?void(self.isTouched=!1):void(insideElement(self.$container,e)===!0?(self.options.autoPlayPauseOnHover===!0&&self.isMouseOver===!1&&self.autoPlay.pause(),self.isMouseOver=!0):(self.options.autoPlayPauseOnHover===!0&&self.isMouseOver===!0&&self.autoPlay.unpause(),self.isMouseOver=!1))}),self.manageEvents.list.mousemove.push({element:self.$container,handler:handler}),handler=addEvent(self.$container,"mouseleave",function(e){self.options.autoPlayPauseOnHover===!0&&self.autoPlay.unpause(),self.isMouseOver=!1}),self.manageEvents.list.mouseleave.push({element:self.$container,handler:handler}),null},swipeNavigation:function(){if(void 0===window.addEventListener)return void(self.hammerTime=!1);var hammerDirection,handler;"function"==typeof Hammer&&(handler=function(e){switch(e.direction){case 2:self.options.swipeEvents.left(self);break;case 4:self.options.swipeEvents.right(self);break;case 8:self.options.swipeEvents.up(self);break;case 16:self.options.swipeEvents.down(self)}},self.hammerTime=new Hammer(self.$container).on("swipe",handler),self.hammerTime.get("swipe").set(self.options.swipeHammerOptions),hammerDirection=getHammerDirection(self.options.swipeEvents),self.hammerTime.get("swipe").set({direction:hammerDirection}),self.manageEvents.list.hammer.push({element:self.$container,handler:handler}))},keyNavigation:function(){var handler=addEvent(document,"keyup",function(event){event||(event=window.event);var keyCodeChar=parseInt(String.fromCharCode(event.keyCode));switch(keyCodeChar>0&&keyCodeChar<=self.noOfSteps&&self.options.numericKeysGoToSteps&&self.goTo(keyCodeChar),event.keyCode){case 37:self.options.keyEvents.left(self);break;case 39:self.options.keyEvents.right(self)}});self.manageEvents.list.keyup.push({element:document,handler:handler})},resizeThrottle:function(){function throttledEvents(){setTimeout(function(){self.canvas.getTransformProperties(),self.propertySupport.transitions===!0&&self.canvas.move(self.currentStepId,!1)},500),self.throttledResize(self)}var throttleTimer,handler;handler=addEvent(window,"resize",function(e){clearTimeout(throttleTimer),throttleTimer=setTimeout(throttledEvents,resizeThreshold)}),self.manageEvents.list.resize.push({element:window,handler:handler})},pageVisibility:function(){var handler=addEvent(document,visibilityChange,function(){document[hidden]?self.autoPlay.pause():self.autoPlay.unpause()},!1);self.manageEvents.list[visibilityChange].push({element:document,handler:handler})}}},self.init=function(element){var id,prevStep,prevStepId,goToFirstStep;self.options=extend(defaults,options),self.isAnimating=!1,self.isReady=!1,self.$container=element,self.$screen=self.$container.querySelectorAll(".seq-screen")[0],self.$canvas=self.$container.querySelectorAll(".seq-canvas")[0],self.$steps=self.canvas.getSteps(self.$canvas),self.noOfSteps=self.$steps.length,self.phasesAnimating=0,self.phasesSkipped=[],addFeatureSupportClasses(self.$container,Modernizr),id=self.options.startingStepId,addClass(self.$container,"seq-active"),self.propertySupport=self.animation.getPropertySupport(),self.isFallbackMode=self.animation.requiresFallbackMode(self.propertySupport),self.canvas.init(id),self.canvas.removeNoJsClass(self),self.manageEvents.init(),self.autoPlay.init(),self.firstRun=!0,id=self.hashTags.init(id),1===self.options.autoPlayDirection?(prevStepId=id-1,self.prevStepId=1>prevStepId?self.noOfSteps:prevStepId):(prevStepId=id+1,self.prevStepId=prevStepId>self.noOfSteps?1:prevStepId),self.currentStepId=self.prevStepId,prevStep=self.prevStepId,self.animationFallback.setupCanvas(id),goToFirstStep=function(){self.animation.domDelay(function(){self.animation.domDelay(function(){self.animation.resetInheritedSpeed(prevStep)}),self.isReady=!0,self.ready(self),self.goTo(id,self.options.autoPlayDirection,!0)})},self.options.preloader!==!1&&void 0!==document.querySelectorAll&&"function"==typeof imagesLoaded?self.preload.init(function(){goToFirstStep()}):goToFirstStep()},self.destroy=function(){var i,step,lastStep;for(self.autoPlay.stop(),clearTimeout(self.autoPlayTimer),clearTimeout(self.phaseThresholdTimer),clearTimeout(self.stepEndedTimer),clearTimeout(self.currentPhaseEndedTimer),clearTimeout(self.nextPhaseStartedTimer),clearTimeout(self.nextPhaseEndedTimer),clearTimeout(self.fadeStepTimer),clearTimeout(self.hideTimer),clearTimeout(self.navigationSkipThresholdTimer),self.manageEvents.removeAll(self.manageEvents.list),removeClass(self.$pagination.currentLinks,"seq-current"),removeClass(self.$container,"seq-step"+self.currentStepId),removeClass(self.$container,"seq-active"),self.$container.removeAttribute("style"),void 0!==self.$screen&&self.$screen.removeAttribute("style"),self.$canvas.removeAttribute("style"),i=0;i<self.noOfSteps;i++)step=self.$steps[i],step.removeAttribute("style"),self.animation.resetInheritedSpeed(i+1),removeClass(step,"seq-in"),removeClass(step,"seq-out");return lastStep=self.$steps[self.options.startingStepId-1],self.animation.resetInheritedSpeed(self.options.startingStepId),addClass(lastStep,"seq-in"),element.removeAttribute("data-seq-enabled"),self.destroyed(self),self=null},self.next=function(){var nextStepId=self.currentStepId+1;return nextStepId>self.noOfSteps&&self.options.cycle===!1?!1:(nextStepId>self.noOfSteps&&(nextStepId=1),self.goTo(nextStepId),nextStepId)},self.prev=function(){var direction,prevStepId=self.currentStepId-1;return 1>prevStepId&&self.options.cycle===!1?!1:(1>prevStepId&&(prevStepId=self.noOfSteps),self.options.reverseWhenNavigatingBackwards===!0&&(direction=-1),self.goTo(prevStepId,direction),prevStepId)},self.toggleAutoPlay=function(delay){return self.isAutoPlaying===!1?self.start(delay):self.stop(),self.isAutoPlaying},self.stop=function(){self.autoPlay.stop()},self.start=function(delay){self.autoPlay.start(delay)},self.goTo=function(id,direction,ignorePhaseThreshold,hashTagNav){if(direction=self.animation.getDirection(id,direction,self.currentStepId,self.noOfSteps,self.isFallbackMode,self.options.reverseWhenNavigatingBackwards,self.options.cycle),void 0===id||1>id||id>self.noOfSteps||id===self.currentStepId||self.options.navigationSkip===!1&&self.isAnimating===!0||self.options.navigationSkip===!0&&self.navigationSkipThresholdActive===!0&&void 0===hashTagNav||self.isFallbackMode===!0&&self.isAnimating===!0&&void 0===hashTagNav||self.options.preventReverseSkipping===!0&&self.direction!==direction&&self.isAnimating===!0)return!1;var currentStepElement,nextStepElement;return clearTimeout(self.autoPlayTimer),self.direction=direction,1===direction?removeClass(self.$container,"seq-reversed"):addClass(self.$container,"seq-reversed"),currentStepElement=self.$steps[self.currentStepId-1],nextStepElement=self.$steps[id-1],self.animation.moveActiveStepToTop(currentStepElement,nextStepElement),self.animation.changeStep(id),self.isFallbackMode===!1?(self.animation.resetInheritedSpeed(id),(self.firstRun===!1||self.firstRun===!0&&self.options.startingStepAnimatesIn===!0)&&self.animationStarted(id,self),self.canvas.move(id,!0),self.animation.manageNavigationSkip(id,nextStepElement),1===direction?self.animation.forward(id,currentStepElement,nextStepElement,ignorePhaseThreshold,hashTagNav):self.animation.reverse(id,currentStepElement,nextStepElement,ignorePhaseThreshold,hashTagNav)):self.animationFallback.goTo(id,self.currentStepId,currentStepElement,id,nextStepElement,direction,hashTagNav),!0},self.started=function(self){},self.stopped=function(self){},self.animationStarted=function(id,self){},self.animationEnded=function(id,self){},self.currentPhaseStarted=function(id,self){},self.currentPhaseEnded=function(id,self){},self.nextPhaseStarted=function(id,self){},self.nextPhaseEnded=function(id,self){},self.throttledResize=function(self){},self.preloaded=function(self){},self.preloadProgress=function(result,src,progress,length,self){},self.ready=function(self){},self.destroyed=function(self){},self.utils={addClass:addClass,removeClass:removeClass,addEvent:addEvent,removeEvent:removeEvent},self.init(element),instances.push(self),self};return Sequence}if("function"==typeof define&&define.amd)define(["imagesLoaded","Hammer"],defineSequence);else{Hammer="function"!=typeof Hammer?null:Hammer,imagesLoaded="function"!=typeof imagesLoaded?null:imagesLoaded;var sequence=defineSequence(imagesLoaded,Hammer)}

/*
 * Edited in Fix for Safari issue
 * Vidbg v1.1.1(BETA) (https://github.com/blakewilson/vidbg)
 * Vidbg By Blake Wilson
 * Edited in Fix for Safari issue
 * @license Licensed Under MIT (https://github.com/blakewilson/vidbg/blob/master/LICENSE)
 */

!function(e,t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports?require("jquery"):e.jQuery)}(this,function($){"use strict";function e(e){var t={},i,o,r,n,s,a,d;for(s=e.replace(/\s*:\s*/g,":").replace(/\s*,\s*/g,",").split(","),d=0,a=s.length;d<a&&(o=s[d],-1===o.search(/^(http|https|ftp):\/\//)&&-1!==o.search(":"));d++)i=o.indexOf(":"),r=o.substring(0,i),n=o.substring(i+1),n||(n=void 0),"string"==typeof n&&(n="true"===n||"false"!==n&&n),"string"==typeof n&&(n=isNaN(n)?n:+n),t[r]=n;return null==r&&null==n?e:t}function t(e){e=""+e;var t=e.split(/\s+/),i="50%",o="50%",r,n,s;for(s=0,r=t.length;s<r;s++)n=t[s],"left"===n?i="0%":"right"===n?i="100%":"top"===n?o="0%":"bottom"===n?o="100%":"center"===n?0===s?i="50%":o="50%":0===s?i=n:o=n;return{x:i,y:o}}function i(e){var t=/^#?([a-f\d])([a-f\d])([a-f\d])$/i;e=e.replace(t,function(e,t,i,o){return t+t+i+i+o+o});var i=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e);return i?{r:parseInt(i[1],16),g:parseInt(i[2],16),b:parseInt(i[3],16)}:null}function o(t,i,o){this.$element=$(t),"string"==typeof i&&(i=e(i)),o?"string"==typeof o&&(o=e(o)):o={},this.settings=$.extend({},n,o),this.path=i;try{this.init()}catch(e){if(e.message!==s)throw e}}var r="vidbg",n={volume:1,playbackRate:1,muted:!0,loop:!0,autoplay:!0,position:"50% 50%",overlay:!1,overlayColor:"#000",overlayAlpha:.3,resizing:!0},s="Not implemented";o.prototype.init=function(){var e=this,o=e.path,r=o,n="",a=e.$element,d=e.settings,p=t(d.position),c,u,l;u=e.$wrapper=$('<div class="vidbg-container">').css({position:"absolute","z-index":-1,top:0,left:0,bottom:0,right:0,overflow:"hidden","-webkit-background-size":"cover","-moz-background-size":"cover","-o-background-size":"cover","background-size":"cover","background-repeat":"no-repeat","background-position":p.x+" "+p.y}),"object"==typeof o&&(o.poster?r=o.poster:o.mp4?r=o.mp4:o.webm&&(r=o.webm)),u.css("background-image","url("+r+")"),"static"===a.css("position")&&a.css("position","relative"),a.css("z-index","1"),a.is("body")&&u.css({position:"fixed"}),a.prepend(u),"object"==typeof o?(o.mp4&&(n+='<source src="'+o.mp4+'" type="video/mp4">'),o.webm&&(n+='<source src="'+o.webm+'" type="video/webm">'),c=e.$video=$("<video>"+n+"</video>")):c=e.$video=$('<video><source src="'+o+'" type="video/mp4"><source src="'+o+'" type="video/webm"></video>');try{c.prop({autoplay:d.autoplay,loop:d.loop,volume:d.volume,muted:d.muted,defaultMuted:d.muted,playbackRate:d.playbackRate,defaultPlaybackRate:d.playbackRate})}catch(e){throw new Error(s)}c.css({margin:"auto",position:"absolute","z-index":-1,top:p.y,left:p.x,"-webkit-transform":"translate(-"+p.x+", -"+p.y+")","-ms-transform":"translate(-"+p.x+", -"+p.y+")","-moz-transform":"translate(-"+p.x+", -"+p.y+")",transform:"translate(-"+p.x+", -"+p.y+")","max-width":"none",visibility:"hidden",opacity:0}).one("canplaythrough.vidbg",function(){e.resize(),-1==navigator.userAgent.indexOf("Safari")||-1!=navigator.userAgent.indexOf("Chrome")||isMobile_kt_slider.any()||c.css({visibility:"visible",opacity:1})}).one("playing.vidbg",function(){c.css({visibility:"visible",opacity:1}),u.css("background-image","none")}),a.on("resize.vidbg",function(){d.resizing&&e.resize()}),u.append(c),l=e.$overlay=$('<div class="vidbg-overlay">').css({position:"absolute",top:0,left:0,right:0,bottom:0,background:"rgba("+i(d.overlayColor).r+", "+i(d.overlayColor).g+", "+i(d.overlayColor).b+", "+d.overlayAlpha+")"}),d.overlay&&u.append(l)},o.prototype.getVideoObject=function(){return this.$video[0]},o.prototype.resize=function(){if(this.$video){var e=this.$wrapper,t=this.$video,i=t[0],o=i.videoHeight,r=i.videoWidth,n=e.height(),s=e.width();s/r>n/o?t.css({width:s+2,height:"auto"}):t.css({width:"auto",height:n+2})}},o.prototype.destroy=function(){delete $.vidbg.lookup[this.index],this.$video&&this.$video.off("vidbg"),this.$element.off("vidbg").removeData("vidbg"),this.$wrapper.remove()},$.vidbg={lookup:[]},$.fn.vidbg=function(e,t){var i;return this.each(function(){i=$.data(this,"vidbg"),i&&i.destroy(),i=new o(this,e,t),i.index=$.vidbg.lookup.push(i)-1,$.data(this,"vidbg",i)}),this},$(document).ready(function(){var e=$(window);e.on("resize.vidbg",function(){for(var e=$.vidbg.lookup.length,t=0,i;t<e;t++)(i=$.vidbg.lookup[t])&&i.settings.resizing&&i.resize()}),e.on("unload.vidbg",function(){return!1}),$(document).find("[data-vidbg-bg]").each(function(e,t){var i=$(t),o=i.data("vidbg-options"),r=i.data("vidbg-bg");i.vidbg(r,o)})})});
/*
 * YoutubeBackground - A wrapper for the Youtube API - Great for fullscreen background videos or just regular videos.
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 * Version:  1.0.5
 *
 */
"function"!=typeof Object.create&&(Object.create=function(e){function t(){}return t.prototype=e,new t}),function($,e,t){var o=function o(n){var i=t.createElement("script"),l=t.getElementsByTagName("head")[0];"file://"==e.location.origin?i.src="http://www.youtube.com/iframe_api":i.src="//www.youtube.com/iframe_api",l.appendChild(i),l=null,i=null,a(n)},a=function t(o){"undefined"==typeof YT&&void 0===e.loadingPlayer?(e.loadingPlayer=!0,e.dfd=$.Deferred(),e.onYouTubeIframeAPIReady=function(){e.onYouTubeIframeAPIReady=null,e.dfd.resolve("done"),o()}):"object"==typeof YT?o():e.dfd.done(function(e){o()})};YTPlayer={player:null,defaults:{ratio:16/9,videoId:"LSmgKRx5pBo",mute:!0,repeat:!0,width:$(e).width(),playButtonClass:"YTPlayer-play",pauseButtonClass:"YTPlayer-pause",muteButtonClass:"YTPlayer-mute",volumeUpClass:"YTPlayer-volume-up",volumeDownClass:"YTPlayer-volume-down",start:0,pauseOnScroll:!1,fitToBackground:!0,playerVars:{iv_load_policy:3,modestbranding:1,autoplay:1,controls:0,showinfo:0,wmode:"opaque",branding:0,autohide:0},events:null},init:function t(a,n){var i=this;return i.userOptions=n,i.$body=$("body"),i.$node=$(a),i.$window=$(e),i.defaults.events={onReady:function(e){i.onPlayerReady(e),i.options.pauseOnScroll&&i.pauseOnScroll(),"function"==typeof i.options.callback&&i.options.callback.call(this)},onStateChange:function(e){1===e.data?(i.$node.find("img").fadeOut(400),i.$node.addClass("loaded")):0===e.data&&i.options.repeat&&i.player.seekTo(i.options.start)}},i.options=$.extend(!0,{},i.defaults,i.userOptions),i.options.height=Math.ceil(i.options.width/i.options.ratio),i.ID=(new Date).getTime(),i.holderID="YTPlayer-ID-"+i.ID,i.options.fitToBackground?i.createBackgroundVideo():i.createContainerVideo(),i.$window.on("resize.YTplayer"+i.ID,function(){i.resize(i)}),o(i.onYouTubeIframeAPIReady.bind(i)),i.resize(i),i},pauseOnScroll:function e(){var t=this;t.$window.on("scroll.YTplayer"+t.ID,function(){1===t.player.getPlayerState()&&t.player.pauseVideo()}),t.$window.scrollStopped(function(){2===t.player.getPlayerState()&&t.player.playVideo()})},createContainerVideo:function e(){var t=this,o=$('<div id="ytplayer-container'+t.ID+'" class="ytplayer-container" >                                    <div id="'+t.holderID+'" class="ytplayer-player-inline"></div>                                     </div>                                     <div id="ytplayer-shield" class="ytplayer-shield"></div>');t.$node.append(o),t.$YTPlayerString=o,o=null},createBackgroundVideo:function e(){var t=this,o=$('<div id="ytplayer-container'+t.ID+'" class="ytplayer-container background">                                    <div id="'+t.holderID+'" class="ytplayer-player"></div>                                    </div>                                    <div id="ytplayer-shield" class="ytplayer-shield"></div>');t.$node.append(o),t.$YTPlayerString=o,o=null},resize:function t(o){var a=$(e);o.options.fitToBackground||(a=o.$node);var n=a.width(),i,l=a.height(),r,d=$("#"+o.holderID);n/o.options.ratio<l?(i=Math.ceil(l*o.options.ratio),d.width(i).height(l).css({left:(n-i)/2,top:0})):(r=Math.ceil(n/o.options.ratio),d.width(n).height(r).css({left:0,top:(l-r)/2})),d=null,a=null},onYouTubeIframeAPIReady:function t(){var o=this;o.player=new e.YT.Player(o.holderID,o.options)},onPlayerReady:function e(t){this.options.mute&&t.target.mute(),t.target.playVideo()},getPlayer:function e(){return this.player},destroy:function t(){var o=this;o.$node.removeData("yt-init").removeData("ytPlayer").removeClass("loaded"),o.$YTPlayerString.remove(),$(e).off("resize.YTplayer"+o.ID),$(e).off("scroll.YTplayer"+o.ID),o.$body=null,o.$node=null,o.$YTPlayerString=null,o.player.destroy(),o.player=null}},$.fn.scrollStopped=function(e){var t=$(this),o=this;t.scroll(function(){t.data("scrollTimeout")&&clearTimeout(t.data("scrollTimeout")),t.data("scrollTimeout",setTimeout(e,250,o))})},$.fn.YTPlayer=function(e){return this.each(function(){var t=this;$(t).data("yt-init",!0);var o=Object.create(YTPlayer);o.init(t,e),$.data(t,"ytPlayer",o)})}}(jQuery,window,document);
/*
 * jQuery.appear
 * https://github.com/bas2k/jquery.appear/
 * http://code.google.com/p/jquery-appear/
 * http://bas2k.ru/
 *
 * Copyright (c) 2009 Michael Hixson
 * Copyright (c) 2012-2014 Alexander Brovikov
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 */
(function($) {
    $.fn.appear_ksp = function(fn, options) {

        var settings = $.extend({

            //arbitrary data to pass to fn
            data: undefined,

            //call fn only on the first appear?
            one: true,

            // X & Y accuracy
            accX: 0,
            accY: 0

        }, options);

        return this.each(function() {

            var t = $(this);

            //whether the element is currently visible
            t.appeared = false;

            if (!fn) {

                //trigger the custom event
                t.trigger('appear', settings.data);
                return;
            }

            var w = $(window);

            //fires the appear event when appropriate
            var check = function() {

                //is the element hidden?
                if (!t.is(':visible')) {

                    //it became hidden
                    t.appeared = false;
                    return;
                }

                //is the element inside the visible window?
                var a = w.scrollLeft();
                var b = w.scrollTop();
                var o = t.offset();
                var x = o.left;
                var y = o.top;

                var ax = settings.accX;
                var ay = settings.accY;
                var th = t.height();
                var wh = w.height();
                var tw = t.width();
                var ww = w.width();

                if (y + th + ay >= b &&
                    y <= b + wh + ay &&
                    x + tw + ax >= a &&
                    x <= a + ww + ax) {

                    //trigger the custom event
                    if (!t.appeared) t.trigger('appear', settings.data);

                } else {

                    //it scrolled out of view
                    t.appeared = false;
                }
            };

            //create a modified fn with some additional logic
            var modifiedFn = function() {

                //mark the element as visible
                t.appeared = true;

                //is this supposed to happen only once?
                if (settings.one) {

                    //remove the check
                    w.unbind('scroll', check);
                    var i = $.inArray(check, $.fn.appear_ksp.checks);
                    if (i >= 0) $.fn.appear_ksp.checks.splice(i, 1);
                }

                //trigger the original fn
                fn.apply(this, arguments);
            };

            //bind the modified fn to the element
            if (settings.one) t.one('appear', settings.data, modifiedFn);
            else t.bind('appear', settings.data, modifiedFn);

            //check whenever the window scrolls
            w.scroll(check);

            //check whenever the dom changes
            $.fn.appear_ksp.checks.push(check);

            //check now
            (check)();
        });
    };

    //keep a queue of appearance checks
    $.extend($.fn.appear_ksp, {

        checks: [],
        timeout: null,

        //process the queue
        checkAll: function() {
            var length = $.fn.appear_ksp.checks.length;
            if (length > 0) while (length--) ($.fn.appear_ksp.checks[length])();
        },

        //check the queue asynchronously
        run: function() {
            if ($.fn.appear_ksp.timeout) clearTimeout($.fn.appear_ksp.timeout);
            $.fn.appear_ksp.timeout = setTimeout($.fn.appear_ksp.checkAll, 20);
        }
    });

    //run checks when these methods are called
    $.each(['append', 'prepend', 'after', 'before', 'attr',
        'removeAttr', 'addClass', 'removeClass', 'toggleClass',
        'remove', 'css', 'show', 'hide'], function(i, n) {
        var old = $.fn[n];
        if (old) {
            $.fn[n] = function() {
                var r = old.apply(this, arguments);
                $.fn.appear_ksp.run();
                return r;
            }
        }
    });

})(jQuery);

var isMobile_kt_slider = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile_kt_slider.Android() || isMobile_kt_slider.BlackBerry() || isMobile_kt_slider.iOS() || isMobile_kt_slider.Opera() || isMobile_kt_slider.Windows());
    }
};
jQuery(document).ready(function ($) {

    function ksp_slide_ratio_Height(container) {
		var container = '.'+container;
			var imageHeight = $(container).find('.kad-slide-1 img.kt-ratio-img').height();
			var kt_height = $(container).attr('data-ktslider-height');
		if(imageHeight == '') {imageHeight = kt_height;}
    		if(imageHeight > kt_height) {
    			var setheight = kt_height;
    			var top = '50%';
    			var mtop = - +imageHeight/2;
    		} else {
    			var setheight = imageHeight;
    			var top = 0;
    			var mtop = 0;
    		}
            $(container).find('.kad-slider').height(setheight);
            $(container).find('.kad-slider .kad-slider-canvas').height(setheight);
            $(container).find('.kad-slider .kad-slide img.kt-ratio-img').css('top', top);
            $(container).find('.kad-slider .kad-slide img.kt-ratio-img').css('margin-top', mtop);
    }
    function ksp_slide_ratio_resize_Height(container) {
    	var container = '.'+container;
    	var count = $(container).attr('data-ktslider-count');
    	var kt_height = $(container).attr('data-ktslider-height');

    	if (count == '1') {
    		var imageHeight =  $(container).find('.kad-slider .kad-slide img.kt-ratio-img').height();
    	} else {
            var imageHeight = $(container).find('.kad-slider .seq-in .kad-slide img.kt-ratio-img').height();
        }
        if(imageHeight == '') {imageHeight = kt_height;}
    		if(imageHeight > kt_height) {
    			var setheight = kt_height;
    			var top = '50%';
    			var mtop = - +imageHeight/2;
    		} else {
    			var setheight = imageHeight;
    			var top = 0;
    			var mtop = 0;
    		}
            $(container).find('.kad-slider').height(setheight);
            $(container).find('.kad-slider .kad-slider-canvas').height(setheight);
            if (count == '1') {
    			jQuery('.kad-slider .kad-slide img.kt-ratio-img').css('top', top);
            	jQuery('.kad-slider .kad-slide img.kt-ratio-img').css('margin-top', mtop);
	    	} else {
	            jQuery('.kad-slider .seq-in .kad-slide img.kt-ratio-img').css('top', top);
            	jQuery('.kad-slider .seq-in .kad-slide img.kt-ratio-img').css('margin-top', mtop);
	        }
         
    }
    function ksp_slide_layer_first_animate(kt_id_name) {
    	var slides = jQuery('#'+kt_id_name+'.kad-slider');
    	slides.find('.ksp-layer').each(function(){
    		$(this).addClass($(this).data('in'));
    	});
    	var slide = jQuery('.kad-slider .kad-slide-1');
        slide.find('.ksp-layer').each(function(){
        		var animate_delay = $(this).data('delay');
        		var animate_ease = $(this).data('ease');
        		if(animate_ease > 1100) {
        			animate_ease = animate_ease-500;
        		} else if(animate_ease > 600) {
        			animate_ease = animate_ease-400;
        		}
        		var time_delay = animate_delay + animate_ease;

	        			var time_delay = animate_delay + animate_ease;
	        			$(this).css({ opacity: 0 });
	        			$(this).delay(time_delay).queue(function(next){
	        				next();
	        				$(this).css({ opacity: 1 });
						});
        });
    }
    function ksp_slide_video_first(kt_id_name) {
    	var slide = jQuery('#'+kt_id_name+'.kad-slider .kad-slide-1');
    	var video = slide.attr('data-video-slide');
    	if(video == 'youtube') {
    		var videoid = slide.attr('data-video-id');
    		var videomute = parseInt(slide.attr('data-video-sound'));
    		var videoloop = parseInt(slide.attr('data-video-loop'));
    		var videoratio = slide.attr('data-video-ratio');
    		var videostart = parseInt(slide.attr('data-video-start'));
    		var videoplaypause = parseInt(slide.attr('data-video-playpause'));
    		if(1 == videomute) {
    			videomute = false;
    			slide.find('.ksp-background-video-mute').show();
    		} else {
    			videomute = true;
    		}
    		if(videoratio != '') {
    			videoratio = videoratio;
    		} else {
    			videoratio = '1.777777778';
    		}
    		if(videostart != '') {
    			videostart = videostart;
    		} else {
    			videostart = '0';
    		}
    		if(1 == videoloop) {
    			videoloop = true;
    		} else {
    			videoloop = false;
    		}
    		if(1 == videoplaypause) {
    			slide.find('.ksp-background-video-pause').show();
    		}
	    	slide.YTPlayer({
	    		ratio: videoratio,
	    		width: slide.width(),
	    		mute: videomute,
      			repeat: videoloop,
			    videoId: videoid,
			    host: 'http://www.youtube.com',
			    fitToBackground: false,
			    playerVars: {
			        rel: 0,
			        start: videostart,
			      },
			});
    	} else if(video == 'html5') {
    		var videomp4 = slide.attr('data-video-mp4');
    		var videowebm = slide.attr('data-video-webm');
    		var videoposter = slide.attr('data-video-poster');
    		var videomute = parseInt(slide.attr('data-video-sound'));
    		var videoloop = parseInt(slide.attr('data-video-loop'));
    		var videoplaypause = parseInt(slide.attr('data-video-playpause'));
    		if(1 == videomute) {
    			videomute = false;
    			slide.find('.ksp-background-video-mute').show();
    		} else {
    			videomute = true;
    		}
    		if(1 == videoloop) {
    			videoloop = true;
    		} else {
    			videoloop = false;
    		}
    		if(1 == videoplaypause) {
    			slide.find('.ksp-background-video-pause').show();
    		}
    		slide.vidbg({
			  	'mp4': videomp4,
			  	'webm': videowebm,
			  	'poster': videoposter,
			}, {
				muted: videomute,
				loop: videoloop,
				overlay: false,
			});
    	}
    }
    function ksp_slide_video_pause(container) {
    	var count = jQuery('.'+container).attr('data-ktslider-count');
    	if (count == '1') {
    		var slide = jQuery('.'+container + ' .kad-slide');
    	} else {
            var slide = jQuery('.'+container + ' .kad-slider .seq-in .kad-slide');
        }
        var video = slide.attr('data-video-slide');
        if(video == 'youtube') {
        	var player = slide.data('ytPlayer').player;
			player.pauseVideo();
    	} else if(video == 'html5') {
    		var player = slide.find('.vidbg-container video');
    		if(player.get(0)) {
	    		if(!player.get(0).paused) {
					player.get(0).pause();
				}
			}
    	}
    }
    function ksp_slide_video(container) {
    	var count = jQuery('.'+container).attr('data-ktslider-count');
    	if (count == '1') {
    		var slide = jQuery('.'+container + ' .kad-slide');
    	} else {
            var slide = jQuery('.'+container + ' .kad-slider .seq-in .kad-slide');
        }
        var video = slide.attr('data-video-slide');
        if(video == 'youtube') {
        	if( typeof slide.data('ytPlayer') !== 'undefined' ) {
        	var player = slide.data('ytPlayer').player;
        		player.playVideo();
        	} else {
        		var videoid = slide.attr('data-video-id');
	    		var videomute = parseInt(slide.attr('data-video-sound'));
	    		var videoloop = parseInt(slide.attr('data-video-loop'));
	    		var videoratio = parseInt(slide.attr('data-video-ratio'));
	    		var videostart = parseInt(slide.attr('data-video-start'));
	    		var videoplaypause = parseInt(slide.attr('data-video-playpause'));
	    		if(1 == videomute) {
	    			videomute = false;
	    			slide.find('.ksp-background-video-mute').show();
	    		} else {
	    			videomute = true;
	    		}
	    		if(videoratio != '') {
	    			videoratio = videoratio;
	    		} else {
	    			videoratio = '16 / 9';
	    		}
	    		if(videostart != '') {
	    			videostart = videostart;
	    		} else {
	    			videostart = '0';
	    		}
	    		if(1 == videoloop) {
	    			videoloop = true;
	    		} else {
	    			videoloop = false;
	    		}
	    		if(1 == videoplaypause) {
	    			slide.find('.ksp-background-video-pause').show();
	    		}
		    	slide.YTPlayer({
		    		ratio: videoratio,
		    		width: slide.width(),
		    		mute: videomute,
	      			repeat: videoloop,
				    videoId: videoid,
				    fitToBackground: false,
				    playerVars: {
				        rel: 0,
				        start: videostart,
				      },
				});
		    }
    	} else if(video == 'html5') {
    		var player = slide.find('.vidbg-container video');
    		if(player.get(0)) {
        		player.get(0).play();
        	} else {
        		var videomp4 = slide.attr('data-video-mp4');
	    		var videowebm = slide.attr('data-video-webm');
	    		var videoposter = slide.attr('data-video-poster');
	    		var videomute = parseInt(slide.attr('data-video-sound'));
	    		var videoloop = parseInt(slide.attr('data-video-loop'));
	    		var videoplaypause = parseInt(slide.attr('data-video-playpause'));
	    		if(1 == videomute) {
	    			videomute = false;
	    			slide.find('.ksp-background-video-mute').show();
	    		} else {
	    			videomute = true;
	    		}
	    		if(1 == videoloop) {
	    			videoloop = true;
	    		} else {
	    			videoloop = false;
	    		}
	    		if(1 == videoplaypause) {
	    			slide.find('.ksp-background-video-pause').show();
	    		}
	    		slide.vidbg({
				  	'mp4': videomp4,
				  	'webm': videowebm,
				  	'poster': videoposter,
				}, {
					muted: videomute,
					loop: videoloop,
					overlay: false,
				});
		    }
    	}
    }
    function ksp_slide_layer_animate(container) {
    	var count = jQuery('.'+container).attr('data-ktslider-count');
    	jQuery('.'+container).find('.ksp-layer').each(function(){
    		$(this).css({ opacity: 0 });
    	});
    	if (count == '1') {
    		var slide = jQuery('.'+container + ' .kad-slide');
    	} else {
            var slide = jQuery('.'+container + ' .kad-slider .seq-in .kad-slide');
        }
        slide.find('.ksp-layer').each(function(){
        		var animate_delay = $(this).data('delay');
        		var animate_ease = $(this).data('ease');
        		if(animate_ease > 1100) {
        			animate_ease = animate_ease-500;
        		} else if(animate_ease > 600) {
        			animate_ease = animate_ease-300;
        		}
        		var time_delay = animate_delay + animate_ease;
    			$(this).css({ opacity: 0 });
    			$(this).delay(time_delay).queue(function(next){
    				next();
    				$(this).css({ opacity: 1 });
				});
        });
    }
    function ksp_slide_scale_delay(container) {
		setTimeout(function(){
			ksp_slide_scale(container);
		}, 25);
    }
	function ksp_slide_scale(container) {
		//console.log(container);
		var container_string = container;
		var container = '.'+container;

	 	function ksp_scaled(value) {
			return value * ksp_getscale();
		}

		function ksp_scaled_height(value, height) {
			var slider_height = $(container).height();
			var kt_height = $(container).attr('data-ktslider-height');
			var	getscale_height = slider_height / kt_height;

			if(slider_height  >= kt_height) {
				var new_top = value * getscale_height;
				var new_height = height * getscale_height;
				var differance = new_height - height;
				var final_top = new_top + (differance / 2.5);
			} else {
				var final_top = value * getscale_height;
			}
			return final_top;
		}

		function ksp_getscale() {
			var slider_width = $(container).width();
			var kt_width = $(container).attr('data-ktslider-width');
			if(slider_width >= kt_width) {
				return 1;
			} else {
				return slider_width / kt_width;
			}
		}
		function ksp_getscale_height() {
			var slider_height = $(container).height();
			var kt_height = $(container).attr('data-ktslider-height');
				return slider_height / kt_height;
		}
		var htype = $(container).attr('data-ktslider-height-type');
		if(htype == 'full'){
			var windowHeight = jQuery(window).height();
			var offset = $(container).data('ktslider-height-offset');
			if ($(offset).length) {
				offset_height = $(offset).height();
			} else {
				offset_height = 0;
			}
            $(container).find('.kad-slider').height(windowHeight - offset_height);
           	$(container).find('.kad-slider .kad-slider-canvas').height(windowHeight - offset_height);
		} else if(htype == 'ratio'){
			ksp_slide_ratio_resize_Height(container_string);
		} else {
			$(container).find('.kad-slider').height(ksp_scaled($(container).attr('data-ktslider-height')));
    	    $(container).find('.kad-slider .kad-slider-canvas').height(ksp_scaled($(container).attr('data-ktslider-height')));
    	}
    	$(container).find('.kad-slide').each(function(){
        	$(this).find('.ksp-layer-wrap').each(function(){
        		$(this).css({
        			'top': ksp_scaled_height($(this).data('top'), $(this).height()),
        			'left': ksp_scaled($(this).data('left')),
        		})
        		var layer = $(this).find('.ksp-layer');
        		// Element contains text
				if(layer.hasClass('ksp-text-layer')) {
					layer.css({					
						'line-height'	 : ksp_scaled_height(layer.data('line-height')) + 'px',
						'letter-spacing' : ksp_scaled(layer.data('letter-spacing')) + 'px',
						'font-size'		 : ksp_scaled(layer.data('font-size')),
					});
				} else if(layer.hasClass('ksp-btn-layer')) {
					layer.css({					
						'line-height'	 : ksp_scaled(layer.data('line-height')) + 'px',
						'letter-spacing' : ksp_scaled(layer.data('letter-spacing')) + 'px',
						'padding-left' : ksp_scaled(layer.data('padding')) + 'px',
						'padding-right' : ksp_scaled(layer.data('padding')) + 'px',
						'border-width' : ksp_scaled(layer.data('border-width')) + 'px',
						'font-size'		 : ksp_scaled(layer.data('font-size')),
					});
				} else if(layer.hasClass('ksp-image-layer')) {
					layer.css({					
						'width'	 : ksp_scaled(layer.data('width')),
						'height' : ksp_scaled(layer.data('height')),
					});
				}
        	});
        });
    }

    $('.kad-slider-parallax .kad-slider .kad-slide').each(function(){
	 	$(this).css({ backgroundPosition: '50% '+ '0px' });
	 	$(this).appear_ksp(function() {
	        var $bgobj = $(this);
	        $(window).scroll(function() {
	            var yPos =  -($(window).scrollTop() / 10); 
	            var coords = '50% '+ yPos + 'px';
	            $bgobj.css({ backgroundPosition: coords });
	        });
        });
    });  

    $('.ksp-background-video-buttons-youtube a').on('click', function(){
    	var slide  = $(this).closest('.kad-slide');
    	var player = slide.data('ytPlayer').player;
    	if($(this).hasClass('ksp-background-video-play')) {
        	player.playVideo();
        	$(this).siblings('.ksp-background-video-pause').show();
        	$(this).hide();
    	} else if($(this).hasClass('ksp-background-video-pause')) {
        	player.pauseVideo();
        	$(this).siblings('.ksp-background-video-play').show();
        	$(this).hide();
    	} else if($(this).hasClass('ksp-background-video-mute')) {
        	player.mute();
        	$(this).siblings('.ksp-background-video-unmute').show();
        	$(this).hide();
    	}else if($(this).hasClass('ksp-background-video-unmute')) {
        	player.unMute();
        	$(this).siblings('.ksp-background-video-mute').show();
        	$(this).hide();
    	}
    });  
    $('.ksp-background-video-buttons-html5 a').on('click', function(){
    	var slide  = $(this).closest('.kad-slide');
    	var player = slide.find('.vidbg-container video');
    	if($(this).hasClass('ksp-background-video-play')) {
        	player.get(0).play();
        	$(this).siblings('.ksp-background-video-pause').show();
        	$(this).hide();
    	} else if($(this).hasClass('ksp-background-video-pause')) {
        	player.get(0).pause();
        	$(this).siblings('.ksp-background-video-play').show();
        	$(this).hide();
    	} else if($(this).hasClass('ksp-background-video-mute')) {
        	player.prop('muted', true);
        	$(this).siblings('.ksp-background-video-unmute').show();
        	$(this).hide();
    	}else if($(this).hasClass('ksp-background-video-unmute')) {
        	player.prop('muted', false);;
        	$(this).siblings('.ksp-background-video-mute').show();
        	$(this).hide();
    	}
    });   
   //init Slider
    $('.ksp-slider-wrapper').each(function(index){
    	var this_slider = $(this);
     	var kt_autoplay = this_slider.attr('data-ktslider-auto-play'),
        kt_pausetime = this_slider.attr('data-ktslider-pause-time'),
        kt_id = this_slider.attr('data-ktslider-id'),
        kt_height = this_slider.attr('data-ktslider-height'),
        kt_hover_pause = this_slider.attr('data-ktslider-pause-hover'),
        kt_height_type = this_slider.attr('data-ktslider-height-type'),
        kt_width = this_slider.attr('data-ktslider-width'),
        count = this_slider.attr('data-ktslider-count');
        var kt_id_name = 'kad-slider-'+kt_id;
        this_slider.find('.ksp-btn-layer').each(function() {
            $(this).mouseover(function() {
                $(this).css('color', $(this).data('hcolor'));
                $(this).css('border-color', $(this).data('hborder-color'));
                $(this).css('background', $(this).data('hbackground-color'));
            });
            $(this).mouseout(function() {
                $(this).css('color', $(this).data('color'));
                $(this).css('border-color', $(this).data('border-color'));
                $(this).css('background', $(this).data('background-color'));
            });
        });
        if(kt_height_type == 'ratio') {
        	this_slider.find('.kad-slide-1 img.kt-ratio-img').imagesLoaded( function() {
    			ksp_slide_ratio_Height(kt_id_name);
    			ksp_slide_scale(kt_id_name);
    		});
    	} else {
    		ksp_slide_scale(kt_id_name);
    	}

        if(kt_autoplay == "true") {
        	kt_autoplay = true;
        } else {
        	kt_autoplay = false;
        }
        if(kt_hover_pause == "true") {
        	kt_hover_pause = true;
        } else {
        	kt_hover_pause = false;
        }
        var options = {
            animateStartingFrameIn: false,
            autoPlay: kt_autoplay,
            preloader: true,
            autoPlayPauseOnHover: kt_hover_pause,
            autoPlayInterval: kt_pausetime,
            fadeFrameWhenSkipped: true,
            fadeFrameTime: 700,
            pagination: '.ksp-pag-'+kt_id,
            nextButton:'.ksp-next-'+kt_id,
            prevButton:'.ksp-prev-'+kt_id,
        };
        var kspElement = document.getElementById(kt_id_name);
        var ksp = sequence(kspElement, options);
        	if(count == 1) {
	        	ksp.preloaded = function() {
	        		ksp_slide_video_first(kt_id_name);
		        	ksp_slide_layer_first_animate(kt_id_name);
		        }
		    } else {
		    	ksp.ready = function() {
		    		ksp_slide_video_first(kt_id_name);
		        	ksp_slide_layer_first_animate(kt_id_name);
		        }
		    }
		    ksp.currentPhaseStarted = function(id, this_slider) {
		        	ksp_slide_video_pause(this_slider.$container['id']);
		    }
	        if(kt_height_type == 'ratio') {
		        ksp.nextPhaseEnded = function(id, this_slider) {
		        	ksp_slide_scale(this_slider.$container['id']);
		        	ksp_slide_video(this_slider.$container['id']);
		        	ksp_slide_layer_animate(this_slider.$container['id']);
		        }
		    } else {
		    	ksp.nextPhaseEnded = function(id, this_slider) {
		    		ksp_slide_video(this_slider.$container['id']);
		        	ksp_slide_layer_animate(this_slider.$container['id']);
		        }
		    }
	        ksp.throttledResize = function(this_slider) {
	        	ksp_slide_scale(this_slider.$container['id']);
	        }
	        $( window ).on( 'panelsStretchRows', ksp_slide_scale_delay(kt_id_name) );

    });

   /* LEGACY */ 
    function kad_slideHeight() {
            var windowHeight = jQuery(window).height();
            jQuery('.kad-slider-wrapper .kad-slider').height(windowHeight);
            jQuery('.kad-slider-wrapper .kad-slider .kad-slider-canvas').height(windowHeight);
    }
    function kad_slide_ratio_Height(container) {
	    	var container = '.'+container;
			var imageHeight = $(container).find('.kad-slide img.kt-ratio-img').height();
			var kt_height = $(container).attr('data-ktslider-height');
    		if(imageHeight > kt_height) {
    			var setheight = kt_height;
    			var top = '50%';
    			var mtop = - +imageHeight/2;
    		} else {
    			var setheight = imageHeight;
    			var top = 0;
    			var mtop = 0;
    		}
    		$(container).find('.kad-slider').height(setheight);
            $(container).find('.kad-slider .kad-slider-canvas').height(setheight);
            $(container).find('.kad-slider .kad-slide img').css('top', top);
            $(container).find('.kad-slider .kad-slide img').css('margin-top', mtop);
    }
    function kad_slide_ratio_resize_Height(container) {
    	var container = '.'+container;
    	var count = $(container).attr('data-ktslider-count');
    	var kt_height = $(container).attr('data-ktslider-height');

    	if (count == '1') {
    		var imageHeight =  $(container).find('.kad-slider .kad-slide img.kt-ratio-img').height();
    	} else {
            var imageHeight = $(container).find('.kad-slider .seq-in .kad-slide img.kt-ratio-img').height();
        }

    		if(imageHeight > kt_height) {
    			var setheight = kt_height;
    			var top = '50%';
    			var mtop = - +imageHeight/2;
    		} else {
    			var setheight = imageHeight;
    			var top = 0;
    			var mtop = 0;
    		}
            $(container).find('.kad-slider').height(setheight);
            $(container).find('.kad-slider .kad-slider-canvas').height(setheight);
            if (count == '1') {
    			$(container).find('.kad-slider .kad-slide img').css('top', top);
            	$(container).find('.kad-slider .kad-slide img').css('margin-top', mtop);
	    	} else {
            	$(container).find('.kad-slider .seq-in .kad-slide img').css('top', top);
            	$(container).find('.kad-slider .seq-in .kad-slide img').css('margin-top', mtop);
	        }
         
    }
      // Lagacy Scroll fade
    function kt_slide_content_fade() {
	var coords = $(window).scrollTop()*-0.004;
		$('.sliderclass .kad-slider-parallax .kad-slider .caption-case-inner').css({ opacity:coords+1 });
		$('.sliderclass .kad_fullslider_arrow').css({ opacity:coords+1 });
		$('.sliderclass .kad-slider ul.kad-slider-pagination').css({ opacity:coords+1 });
	}
	$('.sliderclass .kad-slider-parallax .kad-slider .caption-case-inner').css({ opacity: 1});
	$('.sliderclass .kad_fullslider_arrow').css({ opacity: 1});
	$('.sliderclass .kad-slider ul.kad-slider-pagination').css({ opacity: 1});
	$(window).scroll(kt_slide_content_fade);

	 // Lagacy INIT
    $('.kad-slider-wrapper').each(function(){
    	var this_slider = $(this);
     	var kt_autoplay = this_slider.attr('data-ktslider-auto-play'),
        kt_pausetime = this_slider.attr('data-ktslider-pause-time'),
        kt_hover_pause = this_slider.attr('data-ktslider-pause-hover'),
        kt_id = this_slider.attr('data-ktslider-id'),
        kt_height = this_slider.attr('data-ktslider-height'),
        kt_height_type = this_slider.attr('data-ktslider-height-type'),
        kt_width = this_slider.attr('data-ktslider-width');
        var kt_id_name = 'kad-slider-'+kt_id;
        if(kt_height_type == 'full') {
	        kad_slideHeight();
	        if( !isMobile_kt_slider.any() ) {
		        var kadresizeTimer;
		        $(window).resize(function() {
		            clearTimeout(kadresizeTimer);
		            kadresizeTimer = setTimeout(kad_slideHeight, 100);
		        });
		    } else {
		    	$(window).on("orientationchange",function(){
		    		kad_slideHeight();
		    	});
		    }
    	} else if(kt_height_type == 'ratio') {
    		kad_slide_ratio_Height(kt_id_name);
	        if( !isMobile_kt_slider.any() ) {
		        var kadresizeTimer;
		        $(window).resize(function() {
		            clearTimeout(kadresizeTimer);
		            kadresizeTimer = setTimeout(kad_slide_ratio_resize_Height, 100);
		        });
		    } else {
		    	$(window).on("orientationchange",function(){
		    		kad_slide_ratio_resize_Height();
		    	});
		    }
    	}
        if(kt_autoplay == "true") {
        	kt_autoplay = true;
        } else {
        	kt_autoplay = false;
        }
        if(kt_hover_pause == "true") {
        	kt_hover_pause = true;
        } else {
        	kt_hover_pause = false;
        }
        var options = {
            animateStartingFrameIn: true,
            autoPlay: kt_autoplay,
            preloader: true,
            autoPlayPauseOnHover: kt_hover_pause,
            autoPlayInterval: kt_pausetime,
            fadeFrameWhenSkipped: true,
            fadeFrameTime: 700,
            pagination: '.kad-pag-'+kt_id,
            nextButton:'.kad-next-'+kt_id,
            prevButton:'.kad-prev-'+kt_id,
        };
        var sequenceElement = document.getElementById(kt_id_name);
        var kadenceslider = sequence(sequenceElement, options);
        if(kt_height_type == 'ratio') {
	        kadenceslider.nextPhaseEnded = function(id, this_slider) {
	        	kad_slide_ratio_resize_Height(this_slider.$container['id']);
	        }
	         kadenceslider.throttledResize = function(this_slider) {
	        	kad_slide_ratio_resize_Height(this_slider.$container['id']);
	        }
    	}
    	});
});