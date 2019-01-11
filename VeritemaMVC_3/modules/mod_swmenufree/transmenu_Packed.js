/* =================================================================================================

 * TransMenu

 * March, 2003

 *

 * Customizable multi-level animated DHTML menus with transparency.

 *

 * Copyright 2003-2004, Aaron Boodman (www.youngpup.net)

 * =================================================================================================

 * "Can I use this?"

 *

 * Use of this library is governed by the Creative Commons Attribution 2.0 License. You can check it

 * out at: http://creativecommons.org/licenses/by/2.0/

 *

 * Basically: You may copy, distribute, and eat this code as you wish. But you must give me credit

 * for writing it. You may not misrepresent yourself as the author of this code.

 * =================================================================================================

 * "It's kinda hard to read, though"

 *

 * The uncompressed, commented version of this script can be found at:

 * http://youngpup.net/projects/transMenus

 * =================================================================================================

 * updates:

 * 04.19.04 fixed cascade problem with menus nested greater than two levels.

 * 12.23.03 added hideCurrent for menu actuators with no menus. renamed to TransMenu.

 * 04.18.03	fixed render bug in IE 5.0 Mac by removing that browser from compatibility table ;)

 *			also made gecko check a little more strict by specifying build no.

 * ==============================================================================================

 * Modified by Sean White http://www.swmenupro.com

 */







TransMenu.spacerGif="img/x.gif";TransMenu.dingbatOn="img/submenu-on.gif";TransMenu.dingbatOff="img/submenu-off.gif";TransMenu.dingbatSize=14;TransMenu.menuPadding=5;TransMenu.itemPadding=3;TransMenu.shadowSize=2;TransMenu.shadowOffset=3;TransMenu.shadowColor="#888";TransMenu.shadowPng="img/grey-40.png";TransMenu.backgroundColor="white";TransMenu.backgroundPng="img/white-90.png";TransMenu.hideDelay=1000;TransMenu.slideTime=400;TransMenu.autoposition=1;TransMenu.reference={topLeft:1,topRight:2,bottomLeft:3,bottomRight:4};TransMenu.direction={down:1,right:2,up:3,left:4,dleft:5};TransMenu.registry=[];TransMenu._maxZ=100;TransMenu.isSupported=function(){var a=navigator.userAgent.toLowerCase();var b=navigator.platform.toLowerCase();var c=navigator.appName;var r=true;if(a.indexOf("gecko")>-1&&navigator.productSub>=20020605)r=true;else if(c=="Microsoft Internet Explorer"){if(document.getElementById){if(b.indexOf("mac")==0){r=/msie (\d(.\d*)?)/.test(a)&&Number(RegExp.$1)>=5.1}else r=true}}return r};TransMenu.initialize=function(){for(var i=0,menu=null;menu=this.registry[i];i++){menu.initialize()}};TransMenu.renderAll=function(){var a=[];for(var i=0,menu=null;menu=this.registry[i];i++){a[i]=menu.toString()}document.write(a.join(""))};function TransMenu(n,o,p,q,r,s){this.addItem=addItem;this.addMenu=addMenu;this.toString=toString;this.initialize=initialize;this.isOpen=false;this.show=show;this.hide=hide;this.items=[];this.onactivate=new Function();this.ondeactivate=new Function();this.onmouseover=new Function();this.onqueue=new Function();this.ondequeue=new Function();this.index=TransMenu.registry.length;TransMenu.registry[this.index]=this;var t="TransMenu"+this.index;var u=null;var v=null;var w=null;var z=false;var A=[];var B=-1;var C=null;var D=false;var E=this;var a=null;var F=(o==TransMenu.direction.down||o==TransMenu.direction.up||o==TransMenu.direction.dleft)?"top":"left";var G=null;function addItem(a,b,c,d){var e=new TransMenuItem(a,b,this,c,d,(t+"-"+this.items.length),o);e._index=this.items.length;this.items[e._index]=e}function addMenu(a,b,c){if(!a.parentMenu==this)throw new Error("Cannot add a menu here");var d=TransMenu.direction.right;var e=TransMenu.reference.topRight;if(o==TransMenu.direction.left||o==TransMenu.direction.dleft){d=TransMenu.direction.left;e=TransMenu.reference.topLeft}if(w==null)w=new TransMenuSet(d,b,c,e);var m=w.addMenu(a);A[a._index]=m;m.onmouseover=child_mouseover;m.ondeactivate=child_deactivate;m.onqueue=child_queue;m.ondequeue=child_dequeue;return m}function initialize(){initCache();initEvents();initSize();D=true}function show(){if(D){E.isOpen=true;z=true;setContainerPos();C["clip"].style.visibility="visible";C["clip"].style.zIndex=TransMenu._maxZ++;slideStart();if(TransMenu.selecthack){WCH.Apply(t)}E.onactivate()}}function hide(){if(D){E.isOpen=false;z=true;for(var i=0,item=null;item=C.item[i];i++)dehighlight(item);if(w)w.hide();slideStart();if(TransMenu.selecthack){WCH.Discard(t)}E.ondeactivate()}}function setContainerPos(){var a=n.constructor==TransMenuItem;var b=a?n.parentMenu.elmCache["item"][n._index]:n;var c=b;var x=0;var y=0;var d=navigator.userAgent.toLowerCase();var e=-1;if(navigator.appName=='Microsoft Internet Explorer'){var d=navigator.userAgent;var f=new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");if(f.exec(d)!=null)e=parseFloat(RegExp.$1)}if(TransMenu.autoposition){if((d.indexOf("opera")>=0)){var g=0;var h=(window.innerWidth?window.innerWidth+document.body.scrollLeft:document.body.clientWidth+document.body.scrollLeft)-parseInt(C["clip"].style.width);var i=0;var j=(window.innerHeight?window.innerHeight+document.body.scrollTop:document.body.clientHeight+document.body.scrollTop)-parseInt(C["clip"].style.height)}else if(e==9){var g=0;var h=(document.documentElement.clientWidth+document.documentElement.scrollLeft)-parseInt(C["clip"].style.width);var i=0;var j=(document.documentElement.clientHeight+document.documentElement.scrollTop)-parseInt(C["clip"].style.height)}else{var g=0;var h=(window.innerWidth?window.innerWidth+window.scrollX:document.documentElement.clientWidth+document.documentElement.scrollLeft)-parseInt(C["clip"].style.width);var i=0;var j=(window.innerHeight?window.innerHeight+window.scrollY:document.documentElement.clientHeight+document.documentElement.scrollTop)-parseInt(C["clip"].style.height)}}var k=0;while(a?c.parentNode.className.indexOf("transMenu")==-1:c.offsetParent){if((c.id.indexOf("menu")>-1)||a){x+=c.offsetLeft;y+=c.offsetTop}k++;if(c.scrollLeft)x-=c.scrollLeft;if(c.scrollTop)y-=c.scrollTop;c=c.offsetParent}if(n.constructor==TransMenuItem){x+=parseInt(c.parentNode.style.left);y+=parseInt(c.parentNode.style.top)}switch(r){case TransMenu.reference.topLeft:break;case TransMenu.reference.topRight:x+=b.offsetWidth;break;case TransMenu.reference.bottomLeft:y+=b.offsetHeight;break;case TransMenu.reference.bottomRight:x+=b.offsetWidth;y+=b.offsetHeight;break}x+=c.offsetLeft;y+=c.offsetTop;if((b.tagName=="TR"&&b.childNodes[0])&&d.indexOf("safari")>=0){if(o==4){}else{xx_offset=0;if(TransMenu.sub_indicator){xx_offset=b.childNodes[1].offsetWidth}start=d.indexOf("safari");ver=parseInt(d.substring((start+7),(start+10)));if(ver<500){x+=b.childNodes[0].offsetLeft+b.childNodes[0].offsetWidth+xx_offset;y+=b.childNodes[0].offsetTop}}}if((b.tagName=="TR"&&d.indexOf("opera")>=0)){}x+=p;y+=q;if(TransMenu.autoposition){x=Math.max(Math.min(x,h),g);y=Math.max(Math.min(y,j),i)}var l=C["items"].offsetWidth;var m=C["items"].offsetHeight;u=m+TransMenu.shadowSize;v=l+TransMenu.shadowSize;if(o==TransMenu.direction.up){y-=u}if(o==TransMenu.direction.left||o==TransMenu.direction.dleft){x-=v}C["clip"].style.left=x+"px";C["clip"].style.top=y+"px"}function slideStart(){var b=parseInt(C["content"].style[F]);var c=E.isOpen?0:-G;if(a!=null)a.stop();a=new Accelimation(b,c,TransMenu.slideTime,B);a.onframe=slideFrame;a.onend=slideEnd;a.start()}function slideFrame(x){C["content"].style[F]=x+"px"}function slideEnd(){if(!E.isOpen)C["clip"].style.visibility="hidden";z=false}function initSize(){var a=C["items"].offsetWidth;var b=C["items"].offsetHeight;var c=navigator.userAgent.toLowerCase();C["clip"].style.width=a+TransMenu.shadowSize+2+"px";C["clip"].style.height=b+TransMenu.shadowSize+2+"px";C["content"].style.width=a+TransMenu.shadowSize+"px";C["content"].style.height=b+TransMenu.shadowSize+"px";u=b+TransMenu.shadowSize;v=a+TransMenu.shadowSize;G=(o==TransMenu.direction.down||o==TransMenu.direction.up)?u:v;if(o==TransMenu.direction.left||o==TransMenu.direction.up){G=-G}C["content"].style[F]=-G-TransMenu.shadowSize+"px";C["clip"].style.visibility="hidden";if(c.indexOf("mac")==-1||c.indexOf("gecko")>-1){C["background"].style.width=a+"px";C["background"].style.height=b+"px";C["background"].style.backgroundColor=TransMenu.backgroundColor;C["shadowRight"].style.left=a+"px";C["shadowRight"].style.height=b-(TransMenu.shadowOffset-TransMenu.shadowSize)+"px";C["shadowRight"].style.backgroundColor=TransMenu.shadowColor;C["shadowBottom"].style.top=b+"px";C["shadowBottom"].style.width=a-TransMenu.shadowOffset+"px";C["shadowBottom"].style.backgroundColor=TransMenu.shadowColor}else{C["background"].firstChild.src=TransMenu.backgroundPng;C["background"].firstChild.width=a;C["background"].firstChild.height=b;C["shadowRight"].firstChild.src=TransMenu.shadowPng;C["shadowRight"].style.left=a+"px";C["shadowRight"].firstChild.width=TransMenu.shadowSize;C["shadowRight"].firstChild.height=b-(TransMenu.shadowOffset-TransMenu.shadowSize);C["shadowBottom"].firstChild.src=TransMenu.shadowPng;C["shadowBottom"].style.top=b+"px";C["shadowBottom"].firstChild.height=TransMenu.shadowSize;C["shadowBottom"].firstChild.width=a-TransMenu.shadowOffset}}function initCache(){var a=document.getElementById(t);var b=a.all?a.all:a.getElementsByTagName("*");C={};C["clip"]=a;C["item"]=[];for(var i=0,elm=null;elm=b[i];i++){switch(elm.className){case"items":case"content":case"background":case"shadowRight":case"shadowBottom":C[elm.className]=elm;break;case"item":elm._index=C["item"].length;C["item"][elm._index]=elm;break}}E.elmCache=C}function initEvents(){for(var i=0,item=null;item=C.item[i];i++){item.onmouseover=item_mouseover;item.onmouseout=item_mouseout;item.onclick=item_click}if(typeof n.tagName!="undefined"){n.onmouseover=actuator_mouseover;n.onmouseout=actuator_mouseout}C["content"].onmouseover=content_mouseover;C["content"].onmouseout=content_mouseout}function highlight(a){a.className="item hover";if(A[a._index])if(TransMenu.sub_indicator&&a.lastChild.firstChild.src){a.lastChild.firstChild.src=TransMenu.dingbatOn}}function dehighlight(a){a.className="item";if(A[a._index])if(TransMenu.sub_indicator&&a.lastChild.firstChild.src){a.lastChild.firstChild.src=TransMenu.dingbatOff}}function item_mouseover(){if(!z){highlight(this);if(A[this._index])w.showMenu(A[this._index]);else if(w)w.hide()}}function item_mouseout(){if(!z){if(A[this._index]){}else{dehighlight(this)}}}function item_click(){if(!z){if(E.items[this._index].url){if(E.items[this._index].target=="1"){window.open(E.items[this._index].url,"_blank")}else if(E.items[this._index].target=="2"){window.open(E.items[this._index].url,'','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550')}else if(E.items[this._index].target=="3"){location.href=void(0)}else{location.href=E.items[this._index].url}}}}function actuator_mouseover(){s.showMenu(E)}function actuator_mouseout(){s.hideMenu(E)}function content_mouseover(){if(!z){s.showMenu(E);E.onmouseover()}}function content_mouseout(){if(!z){s.hideMenu(E)}}function child_mouseover(){if(!z){s.showMenu(E)}}function child_deactivate(){for(var i=0;i<A.length;i++){if(A[i]==this){dehighlight(C["item"][i]);break}}}function child_queue(){s.hideMenu(E)}function child_dequeue(){s.showMenu(E)}function toString(){var a=[];var b="transMenu"+(n.constructor!=TransMenuItem?" top":"");for(var i=0,item=null;item=this.items[i];i++){a[i]=item.toString(A[i])}return'<div id="'+t+'" class="'+b+'">'+'<div class="content"><table class="items" cellpadding="0" cellspacing="0" border="0">'+a.join('')+'</table>'+'<div class="shadowBottom"></div>'+'<div class="shadowRight"></div>'+'<div class="background"></div>'+'</div></div>'}}TransMenuSet.registry=[];function TransMenuSet(b,c,d,e){this.addMenu=addMenu;this.showMenu=showMenu;this.hideMenu=hideMenu;this.hide=hide;this.hideCurrent=hideCurrent;var f=[];var g=this;var h=null;this.index=TransMenuSet.registry.length;TransMenuSet.registry[this.index]=this;function addMenu(a){var m=new TransMenu(a,b,c,d,e,this);f[f.length]=m;return m}function showMenu(a){if(a!=h){if(h!=null)hide(h);h=a;a.show()}else{cancelHide(a)}}function hideMenu(a){if(h==a&&a.isOpen){if(!a.hideTimer)scheduleHide(a)}}function scheduleHide(a){a.onqueue();a.hideTimer=window.setTimeout("TransMenuSet.registry["+g.index+"].hide(TransMenu.registry["+a.index+"])",TransMenu.hideDelay)}function cancelHide(a){if(a.hideTimer){a.ondequeue();window.clearTimeout(a.hideTimer);a.hideTimer=null}}function hide(a){if(!a&&h)a=h;if(a&&h==a&&a.isOpen){hideCurrent()}}function hideCurrent(){if(null!=h){cancelHide(h);h.hideTimer=null;h.hide();h=null}}}function TransMenuItem(g,h,i,j,k,l,m){this.toString=toString;this.text=g;this.url=h;this.target=j;this.parentMenu=i;function toString(a){var b=a?TransMenu.dingbatOff:TransMenu.spacerGif;var c=TransMenu.itemPadding+TransMenu.menuPadding;var d="padding:"+TransMenu.itemPadding+"px; padding-left:"+c+"px;";var e="padding:"+TransMenu.itemPadding+"px; padding-right:"+c+"px;";var f='<tr class="item">';if(TransMenu.sub_indicator&&((m==TransMenu.direction.left)||(m==TransMenu.direction.dleft))){f+='<td style="border:none;'+e+'" border="0">'+'<img src="'+b+'" ></td>'}f+='<td nowrap style="border:none;'+d+'" id="'+l+'" border="0">'+g+'</td>';if(TransMenu.sub_indicator&&((m==TransMenu.direction.down)||(m==TransMenu.direction.right)||(m==TransMenu.direction.up))){f+='<td style="border:none;'+e+'">'+'<img src="'+b+'" border="0"></td>'}f+='</tr>';return f}}function Accelimation(a,b,c,d){if(typeof d=="undefined")d=0;if(typeof unit=="undefined")unit="px";this.x0=a;this.x1=b;this.dt=c;this.zip=-d;this.unit=unit;this.timer=null;this.onend=new Function();this.onframe=new Function()}Accelimation.prototype.start=function(){this.t0=new Date().getTime();this.t1=this.t0+this.dt;var a=this.x1-this.x0;this.c1=this.x0+((1+this.zip)*a/3);this.c2=this.x0+((2+this.zip)*a/3);Accelimation._add(this)};Accelimation.prototype.stop=function(){Accelimation._remove(this)};Accelimation.prototype._paint=function(a){if(a<this.t1){var b=a-this.t0;this.onframe(Accelimation._getBezier(b/this.dt,this.x0,this.x1,this.c1,this.c2))}else this._end()};Accelimation.prototype._end=function(){Accelimation._remove(this);this.onframe(this.x1);this.onend()};Accelimation._add=function(o){var a=this.instances.length;this.instances[a]=o;if(this.instances.length==1){this.timerID=window.setInterval("Accelimation._paintAll()",this.targetRes)}};Accelimation._remove=function(o){for(var i=0;i<this.instances.length;i++){if(o==this.instances[i]){this.instances=this.instances.slice(0,i).concat(this.instances.slice(i+1));break}}if(this.instances.length==0){window.clearInterval(this.timerID);this.timerID=null}};Accelimation._paintAll=function(){var a=new Date().getTime();for(var i=0;i<this.instances.length;i++){this.instances[i]._paint(a)}};Accelimation._B1=function(t){return t*t*t};Accelimation._B2=function(t){return 3*t*t*(1-t)};Accelimation._B3=function(t){return 3*t*(1-t)*(1-t)};Accelimation._B4=function(t){return(1-t)*(1-t)*(1-t)};Accelimation._getBezier=function(a,b,c,d,e){return c*this._B1(a)+e*this._B2(a)+d*this._B3(a)+b*this._B4(a)};Accelimation.instances=[];Accelimation.targetRes=10;Accelimation.timerID=null;if(window.attachEvent){var cearElementProps=['data','onmouseover','onmouseout','onmousedown','onmouseup','ondblclick','onclick','onselectstart','oncontextmenu'];window.attachEvent("onunload",function(){var a;for(var d=document.all.length;d--;){a=document.all[d];for(var c=cearElementProps.length;c--;){a[cearElementProps[c]]=null}}})}var WCH_Constructor=function(){if(!(document.all&&document.getElementById&&!window.opera&&navigator.userAgent.toLowerCase().indexOf("mac")==-1)){this.Apply=function(){};this.Discard=function(){};return}var j=false;var k=false;var l=null;var m=true;var n=this;this.Apply=function(a,b,c){if(m)_Setup();if(j&&(oIframe=_Hider(a,b,c))){oIframe.style.visibility="visible"}else if(l!=null){l.style.visibility="hidden"}};this.Discard=function(a,b){if(j&&(oIframe=_Hider(a,b,false))){oIframe.style.visibility="hidden"}else if(l!=null){l.style.visibility="visible"}};function _Hider(a,b,c){var d=_GetObj(a);var e=((oTmp=_GetObj(b))?oTmp:document.getElementsByTagName("body")[0]);if(!d||!e)return;var f=document.getElementById("WCHhider"+d.id);if(!f){var g=(k)?"filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);":"";var h=d.style.zIndex;if(h=="")h=d.currentStyle.zIndex;h=parseInt(h);if(isNaN(h))return null;if(h<2)return null;h--;var i="WCHhider"+d.id;e.insertAdjacentHTML("afterBegin",'<iframe class="WCHiframe" src="javascript:false;" id="'+i+'" scroll="no" frameborder="0" style="position:absolute;visibility:hidden;'+g+'border:0;top:0;left;0;width:0;height:0;background-color:#ccc;z-index:'+h+';"></iframe>');f=document.getElementById(i);_SetPos(f,d)}else if(c){_SetPos(f,d)}return f};function _SetPos(a,b){a.style.width=b.offsetWidth+"px";a.style.height=b.offsetHeight+"px";a.style.left=b.offsetLeft+"px";a.style.top=b.offsetTop+"px"};function _GetObj(a){var b=null;switch(typeof(a)){case"object":b=a;break;case"string":b=document.getElementById(a);break}return b};function _Setup(){j=(typeof(document.body.contentEditable)!="undefined");k=(typeof(document.compatMode)!="undefined");if(!j){if(document.styleSheets.length==0)document.createStyleSheet();var a=document.styleSheets[0];a.addRule(".WCHhider","visibility:visible");l=a.rules(a.rules.length-1)}m=false}};var WCH=new WCH_Constructor();