/**
 * Created by www on 2016-10-21.
 */
/*
 * js transfrom
 * @param obj {obj}    原生dom对象
 * @param properties  {json} ||string     { translate3d:'220px,10px,0',left:'1em',opacity:0.2, rotateY:'30deg'} || animationName 多个可以以逗号分割 如 'fadeIn,sliderDown';
 * @param duration {number}      css3持续时间 秒 默认400毫秒
 * @param ease {str}           默认linear 支持  cubic-bezier(0.42,0,1,1)写法;
 * @param callback {function}    回调函数
 * @param delay {number}    延迟时间

 */
/* http://www.cnblogs.com/surfaces/
 * @param properties 为  {}  或者 string     ；如果 properties= string 为animation-name
 * transform(elem, properties)
 * transform(elem, properties, ease)
 * transform(elem, properties, ease, delay)
 * transform(elem, properties, ease, callback, delay)
 * transform(elem, properties, callback)
 * transform(elem, properties, callback, delay)
 * transform(elem, properties, duration )
 * transform(elem, properties, duration, ease)
 * transform(elem, properties, duration, delay)
 * transform(elem, properties, duration, callback)
 * transform(elem, properties, duration, callback,delay)
 * transform(elem, properties, duration, ease, delay)
 * transform(elem, properties, duration, ease, callback)
 * transform(elem, properties, duration, ease, callback,delay)

 transform(elem,{translateX:'150px',left:'1em',opacity:0.2, rotateY:'40deg'},600,'linear',function(){  console.log('结束回调') },200) ;

 transform(elem, keyframesName,600,'linear',function(){  console.log('结束回调') },200) ;
 */
;(function(window,document,undefined){var prefix=function(){var div=document.createElement("div");var cssText="-webkit-transition:all .1s;-moz-transition:all .1s; -Khtml-transition:all .1s; -o-transition:all .1s; -ms-transition:all .1s; transition:all .1s;";div.style.cssText=cssText;var style=div.style;var dom="";if(style.webkitTransition){dom="webkit"}else{if(style.MozTransition){dom="moz"}else{if(style.khtmlTransition){dom="Khtml"}else{if(style.oTransition){dom="o"}else{if(style.msTransition){dom="ms"}}}}}div=null;if(dom){return{dom:dom,lowercase:dom,css:"-"+dom+"-",js:dom[0].toUpperCase()+dom.substr(1)}}else{return false}}();var transitionEnd=function(){var el=document.createElement("div");var transEndEventNames={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",msTransition:"MSTransitionEnd",transition:"transitionend"};for(var name in transEndEventNames){if(el.style[name]!==undefined){return transEndEventNames[name]}}el=null;return false}();var animationEnd=(function(){var eleStyle=document.createElement("div").style;var verdors=["a","webkitA","MozA","OA","msA"];var endEvents=["animationend","webkitAnimationEnd","animationend","oAnimationEnd","MSAnimationEnd"];var animation;for(var i=0,len=verdors.length;i<len;i++){animation=verdors[i]+"nimation";if(animation in eleStyle){return endEvents[i]}}return"animationend"}());var supportedTransforms=/^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i;var dasherize=function(str){return str.replace(/::/g,"/").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/_/g,"-").toLowerCase()};function transform(obj,properties,duration,ease,callback,delay){if(!obj){return}if(typeof duration=="undefined"){duration=400;ease="linear";callback=undefined;delay=undefined}if(typeof duration=="string"){if(typeof ease=="number"){delay=ease;callback=undefined}if(typeof ease=="function"){delay=callback;callback=ease}ease=duration;duration=400}else{if(typeof duration=="function"){if(typeof ease=="number"){delay=ease}callback=duration;duration=400;ease="linear"}else{if(typeof duration=="number"){if(typeof ease=="undefined"){ease="linear"}else{if(typeof ease=="string"){ease=ease}else{if(typeof ease=="function"){if(typeof callback=="number"){delay=callback}callback=ease;ease="linear"}else{if(typeof ease=="number"){delay=ease;ease="linear"}}}}if(typeof callback=="number"){delay=callback;callback=undefined}}}}delay=(typeof delay=="number")?delay:0;var endEvent=transitionEnd;var nowTransition=prefix.js+"Transition";var nowTransform=prefix.js+"Transform";var prefixcss=prefix.css;if(!prefix.js){nowTransition="transition";nowTransform="transform";prefixcss=""}var transitionProperty,transitionDuration,transitionTiming,transitionDelay;var animationName,animationDuration,animationTiming,animationDelay;var key,cssValues={},cssProperties,transforms="";var transform;var cssReset={};var css="";var cssProperties=[];transform=prefixcss+"transform";cssReset[transitionProperty=prefixcss+"transition-property"]=cssReset[transitionDuration=prefixcss+"transition-duration"]=cssReset[transitionTiming=prefixcss+"transition-timing-function"]=cssReset[transitionDelay=prefixcss+"transition-delay"]=cssReset[animationName=prefixcss+"animation-name"]=cssReset[animationDuration=prefixcss+"animation-duration"]=cssReset[animationTiming=prefixcss+"animation-timing-function"]=cssReset[animationDelay=prefixcss+"animation-delay"]="";if(typeof properties=="string"){cssValues[animationName]=properties;cssValues[animationDuration]=duration+"ms";cssValues[animationTiming]=(ease||"linear");cssValues[animationDelay]=(delay)+"ms";endEvent=animationEnd}else{endEvent=transitionEnd;for(key in properties){if(supportedTransforms.test(key)){transforms+=key+"("+properties[key]+") "}else{cssValues[key]=properties[key],cssProperties.push(dasherize(key))}}if(transforms){cssValues[transform]=transforms,cssProperties.push(transform)}if(duration>0&&typeof properties==="object"){cssValues[transitionProperty]=cssProperties.join(", ");cssValues[transitionDuration]=duration+"ms";cssValues[transitionTiming]=(ease||"linear");cssValues[transitionDelay]=(delay)+"ms"}}for(var attr in cssValues){css+=dasherize(attr)+":"+cssValues[attr]+";"}obj.style.cssText=obj.style.cssText+";"+css;obj.clientLeft;if(!callback){return}var fired=false;var handler=function(event){if(typeof event!=="undefined"){if(event.target!==event.currentTarget){fired=true;return}}callback&&callback.apply(obj,arguments);fired=true;obj.removeEventListener(endEvent,arguments.callee,false)};if(obj.addEventListener){obj.addEventListener(endEvent,handler,false)}if(!endEvent||duration<=0){setTimeout(function(){handler()});return}setTimeout(function(){if(fired){return}handler()},parseInt((duration+delay)+25))}window.transform=transform})(window,document);