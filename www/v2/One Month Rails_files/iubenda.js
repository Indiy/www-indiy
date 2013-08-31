(function(t,e,i){function n(t,i){var n,s=e.createElement(t||"div");for(n in i)s[n]=i[n];return s}function s(t){for(var e=1,i=arguments.length;i>e;e++)t.appendChild(arguments[e]);return t}function o(t,e,i,n){var s=["opacity",e,~~(100*t),i,n].join("-"),o=.01+100*(i/n),a=Math.max(1-(1-t)/e*(100-o),t),r=u.substring(0,u.indexOf("Animation")).toLowerCase(),l=r&&"-"+r+"-"||"";return d[s]||(p.insertRule("@"+l+"keyframes "+s+"{"+"0%{opacity:"+a+"}"+o+"%{opacity:"+t+"}"+(o+.01)+"%{opacity:1}"+(o+e)%100+"%{opacity:"+t+"}"+"100%{opacity:"+a+"}"+"}",0),d[s]=1),s}function a(t,e){var n,s,o=t.style;if(o[e]!==i)return e;for(e=e.charAt(0).toUpperCase()+e.slice(1),s=0;s<c.length;s++)if(n=c[s]+e,o[n]!==i)return n}function r(t,e){for(var i in e)t.style[a(t,i)||i]=e[i];return t}function l(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var s in n)t[s]===i&&(t[s]=n[s])}return t}function h(t){for(var e={x:t.offsetLeft,y:t.offsetTop};t=t.offsetParent;)e.x+=t.offsetLeft,e.y+=t.offsetTop;return e}/**
 * Copyright (c) 2011 Felix Gnass [fgnass at neteye dot de]
 * Licensed under the MIT license
 */
var u,c=["webkit","Moz","ms","O"],d={},p=function(){var t=n("style");return s(e.getElementsByTagName("head")[0],t),t.sheet||t.styleSheet}(),f={lines:12,length:7,width:5,radius:10,rotate:0,color:"#000",speed:1,trail:100,opacity:.25,fps:20,zIndex:2e9,className:"spinner",top:"auto",left:"auto"},m=function g(t){return this.spin?(this.opts=l(t||{},g.defaults,f),void 0):new g(t)};m.defaults={},l(m.prototype,{spin:function(t){this.stop();var e,i,s=this,o=s.opts,a=s.el=r(n(0,{className:o.className}),{position:"relative",zIndex:o.zIndex}),l=o.radius+o.length+o.width;if(t&&(t.insertBefore(a,t.firstChild||null),i=h(t),e=h(a),r(a,{left:(o.left=="auto"?i.x-e.x+(t.offsetWidth>>1):o.left+l)+"px",top:(o.top=="auto"?i.y-e.y+(t.offsetHeight>>1):o.top+l)+"px"})),a.setAttribute("aria-role","progressbar"),s.lines(a,s.opts),!u){var c=0,d=o.fps,p=d/o.speed,f=(1-o.opacity)/(p*o.trail/100),m=p/o.lines;!function g(){c++;for(var t=o.lines;t;t--){var e=Math.max(1-(c+t*m)%p*f,o.opacity);s.opacity(a,o.lines-t,e,o)}s.timeout=s.el&&setTimeout(g,~~(1e3/d))}()}return s},stop:function(){var t=this.el;return t&&(clearTimeout(this.timeout),t.parentNode&&t.parentNode.removeChild(t),this.el=i),this},lines:function(t,e){function i(t,i){return r(n(),{position:"absolute",width:e.length+e.width+"px",height:e.width+"px",background:t,boxShadow:i,transformOrigin:"left",transform:"rotate("+~~(360/e.lines*l+e.rotate)+"deg) translate("+e.radius+"px"+",0)",borderRadius:(e.width>>1)+"px"})}for(var a,l=0;l<e.lines;l++)a=r(n(),{position:"absolute",top:1+~(e.width/2)+"px",transform:e.hwaccel?"translate3d(0,0,0)":"",opacity:e.opacity,animation:u&&o(e.opacity,e.trail,l,e.lines)+" "+1/e.speed+"s linear infinite"}),e.shadow&&s(a,r(i("#000","0 0 4px #000"),{top:"2px"})),s(t,s(a,i(e.color,"0 0 1px rgba(0,0,0,.1)")));return t},opacity:function(t,e,i){e<t.childNodes.length&&(t.childNodes[e].style.opacity=i)}}),!function(){function t(t,e){return n("<"+t+' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">',e)}var e=r(n("group"),{behavior:"url(#default#VML)"});!a(e,"transform")&&e.adj?(p.addRule(".spin-vml","behavior:url(#default#VML)"),m.prototype.lines=function(e,i){function n(){return r(t("group",{coordsize:h+" "+h,coordorigin:-l+" "+-l}),{width:h,height:h})}function o(e,o,a){s(c,s(r(n(),{rotation:360/i.lines*e+"deg",left:~~o}),s(r(t("roundrect",{arcsize:1}),{width:l,height:i.width,left:i.radius,top:-i.width>>1,filter:a}),t("fill",{color:i.color,opacity:i.opacity}),t("stroke",{opacity:0}))))}var a,l=i.length+i.width,h=2*l,u=-(i.width+i.length)*2+"px",c=r(n(),{position:"absolute",top:u,left:u});if(i.shadow)for(a=1;a<=i.lines;a++)o(a,-2,"progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");for(a=1;a<=i.lines;a++)o(a);return s(e,c)},m.prototype.opacity=function(t,e,i,n){var s=t.firstChild;n=n.shadow&&n.lines||0,s&&e+n<s.childNodes.length&&(s=s.childNodes[e+n],s=s&&s.firstChild,s=s&&s.firstChild,s&&(s.opacity=i))}):u=a(e,"animation")}(),"function"==typeof define&&define.amd?define(function(){return m}):t.Spinner=m})(window,document),function(){typeof String.prototype.trim!="function"&&(String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g,"")})}();var _iub=_iub||[];_iub.badges=_iub.badges||[],function(t,e){function i(t,i){var r=t,l=!1,h=!1,u=!1,c=!1,p=!1,v="iubenda-white",x=t.getAttribute("href"),k=x.split("/"),C=k[k.length-1],D=t.className.split(" ");g(D,"no-brand")&&(l=!0),g(D,"skip-track")&&(h=!0),g(D,"iub-body-embed")&&(u=!0),g(D,"iub-legal-only")&&(c=!0),g(D,"iub-anchor")&&(p=!0);var T=x.indexOf("http://")!=-1?b.replace("https://","http://"):b,S=x.indexOf("http://")!=-1?y.replace("https://","http://"):y,N=x.indexOf("http://")!=-1?_.replace("https://","http://"):_,M=x.indexOf("http://")!=-1?w.replace("https://","http://"):w;if(v=f(D,"iubenda-no-icon")!=-1?"iubenda-nostyle":m(["iubenda-green","iubenda-green-m","iubenda-green-s","iubenda-green-xs","iubenda-lowgray","iubenda-lowgray-m","iubenda-lowgray-s","iubenda-lowgray-xs","iubenda-midgray","iubenda-midgray-m","iubenda-midgray-s","iubenda-midgray-xs","iubenda-darkgray","iubenda-darkgray-m","iubenda-darkgray-s","iubenda-darkgray-xs","iubenda-white","iubenda-black","iubenda-nostyle"],D),-1==v&&(v="iubenda-white"),"iubenda-nostyle"!=v&&(t.style.outline="0px",t.style.border="0px",t.style.textDecoration="none",t.style.display="inline-block",t.style.background="none"),u)a(t,i,M,c);else if(f(["iubenda-white","iubenda-black"],v)!=-1)o(t,v,null,null,C,l,T,S,N,h,c,p),r=null;else{if(c&&(t.href=t.href+"/full-legal"),"iubenda-nostyle"!=v){var I=116,A=25,P=".gif";(v.indexOf("-m")!=-1&&v.indexOf("-mid")==-1||v.indexOf("midgray-m")!=-1)&&(I=81,A=21),(v.indexOf("-s")!=-1||v.indexOf("-xs")!=-1)&&(I=82,A=17,P=".png"),t.style.width=I+"px",t.style.height=A+"px",v+=P,n(t,T+v,I,A)}d(S,t,function(){_iub.ifr.iubendaStartBadge({linkA:t,embedP:e.getElementsByTagName("body")[0],iFrUrl:t.href,cdnBaseUrl:T})}),h||s(t,C)}return"undefined"!=typeof editLinkA&&null!=editLinkA&&(editLinkA=null),r}function n(t,e,i,n){u(t.id,e,100,i,n)}function s(){}function o(t,i,n,o,a,r,l,u,c,d,p,f){t.style.display="none";var m=t.innerHTML.trim()||"Privacy Policy",g=t.getAttribute("title")||"Privacy Policy",v={"Informativa Privacy":136,"Datenschutzerklärung":154,"Política de privacidad":146,"Politique de confidentialité":178},b=n||v[m]||105,y=o||22,_=e.createElement("IFRAME"),w=f?"iubenda-ibadge iubenda-iframe-anchor":"iubenda-ibadge";_.setAttribute("class",w),_.setAttribute("scrolling","no"),_.setAttribute("frameBorder","0"),_.setAttribute("allowtransparency","true");var x="width:"+b+"px; height:"+y+"px;";f&&(x+=" z-index:9998; position:fixed; bottom:0px; right:0px;"),h(_,x),t.parentNode.insertBefore(_,t.nextSibling),t.parentNode.removeChild(t);var k=_.contentWindow.document;k.open(),k.write();var C=t.href.replace("www.iubenda","hermes.iubenda");p&&(C+="/full-legal");var D=p?t.href+"/full-legal":t.href,T='<html><head><title>iubenda badge</title><meta name="viewport" content="width=device-width"><link type="text/css" rel="stylesheet" href="'+c+'" media="screen" />'+'<script type="text/javascript" src="'+u+'"></script></head>'+"<body onload=\"_iub.ifr.iubendaStartBadge({iFrUrl:'"+C+'\'});"><a href="'+D+'" class="'+i+" "+(r?"no-brand":"")+" "+(f?"iub-anchor":"")+'" id="i_badge-link" title="'+g+'" target="_parent" >'+m+"</a></body></html>";k.write(T),k.close(),d||s(_,a)}function a(t,e,i,n){r(i);var s=n?"/embed-legal":"/embed",o=isNaN(parseInt(e))?t.href+s+"?callback=_iub.loadPPContent":t.href+s+"?i="+e+"&callback=_iub.loadPPContent",a={lines:8,length:2,width:2,radius:2,color:"#696969",speed:1.2,trail:60,shadow:!1},l=new Spinner(a).spin();l.el.className="_iub-pp-loading-alert",h(l.el,"position:relative; display:inline-block; padding: 6px;"),t.parentNode.insertBefore(l.el,t),t.style.display="none",d(o,t)}function r(t){var i=e.createElement("link");i.type="text/css",i.rel="stylesheet",i.href=t,e.getElementsByTagName("head")[0].appendChild(i)}function l(t){var i=isNaN(parseInt(t.i))?e.getElementById("iubenda-embed"):_iub.badges[parseInt(t.i)];if(i){var n=e.createElement("div");n.setAttribute("id","iub-pp-container"),n.innerHTML=t.content,i.parentNode.insertBefore(n,i.nextSibling);var s=i.previousSibling;s.className=="_iub-pp-loading-alert"&&s.parentNode.removeChild(s),i.parentNode.removeChild(i)}}function h(t,e){var i=c();-1!=i&&8>i?t.style.cssText=e:t.setAttribute("style",e)}function u(t,i,n,s,o){if(!(0>=n)){var a=e.getElementById(t),r=e.createElement("img");r.src=i,r.style.width=s+"px",r.style.height=o+"px",r.style.border="0px",a&&r.width?(r.alt=a.firstChild.nodeValue,r.title=a.firstChild.nodeValue,a.replaceChild(r,a.firstChild)):setTimeout("_iub.imageFastReplace('"+t+"','"+i+"',"+--n+","+s+","+o+");",150)}}function c(){var t=-1;if(navigator.appName=="Microsoft Internet Explorer"){var e=navigator.userAgent,i=new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");i.exec(e)!=null&&(t=parseFloat(RegExp.$1))}return t}function d(t,i,n){var s=e.createElement("script");s.setAttribute("type","text/javascript"),s.setAttribute("src",t),i.parentNode.insertBefore(s,i.nextSibling),"function"==typeof n&&p(s,n)}function p(t,e){var i=c();-1!=i&&9>i?t.onreadystatechange=function(){(this.readyState=="loaded"||this.readyState=="complete")&&e()}:t.onload=function(){e()}}function f(t,e){var i=Object(t),n=i.length>>>0;if(0===n)return-1;var s=0;if(arguments.length>0&&(s=Number(arguments[1]),s!==s?s=0:0!==s&&s!==1/0&&s!==-(1/0)&&(s=(s>0||-1)*Math.floor(Math.abs(s)))),s>=n)return-1;for(var o=s>=0?s:Math.max(n-Math.abs(s),0);n>o;o++)if(o in i&&i[o]===e)return o;return-1}function m(t,e){var i=Object(t),n=i.length>>>0;if(0===n)return-1;for(var s=0;s<e.length;s++)if(f(i,e[s])!=-1)return e[s];return-1}function g(t,e){return f(t,e)!=-1?(t.splice(f(t,e),1),!0):!1}function v(t,i){i||(i=e.getElementsByTagName("body")[0]);for(var n=[],s=new RegExp("\\b"+t+"\\b"),o=i.getElementsByTagName("*"),a=0,r=o.length;r>a;a++)s.test(o[a].className)&&n.push(o[a]);return n}var b="https://cdn.iubenda.com/",y="https://cdn.iubenda.com/iubenda_i_badge.js",_="https://cdn.iubenda.com/iubenda_i_badge.css",w="https://www.iubenda.com/assets/privacy_policy.css";(function(){var t=v("iubenda-embed",e);if(t.length==0){var n=e.getElementById("iubenda-embed");n&&i(n)}else for(var s=0;s<t.length;s++)if(f(_iub.badges,t[s])==-1){var o=i(t[s],s);null!=o&&_iub.badges.push(o)}})(),t._iub.setStyle=function(t,e){h(t,e)},t._iub.onLoadCall=function(t,e){p(t,e)},t._iub.imageFastReplace=function(t,e,i,n,s){u(t,e,i,n,s)},t._iub.getElementsByClassName=function(t,e){return v(t,e)},t._iub.loadPPContent=function(t){l(t)}}(window,document);