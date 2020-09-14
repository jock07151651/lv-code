/**
*BX 全局包 1.0.11
*编码：utf-8版本
*作者：bigtreexu
*网站：http://www.bigtreexu.com
*/
/*___BX GLOABLE___*/
var BX={
	version:'1.0.10.A',
	encoding:'utf-8',
	author:'bigtreexu'
};
BX.namespace=function(ns){
	if(!ns||!ns.length)
	{
		return null;
	}
	var _pr=ns.split('.');
	var _nx=BX;
	for(var i=0;i!=_pr.length;i++)
	{
		_nx[_pr[i]]=_nx[_pr[i]]||{};
		_nx=_nx[_pr[i]];
	}
}

/*___Predigest Application___*/
function $(el)
{
	if(!el)
	{
		return null;
	}
	else if(typeof el=='string')
	{
		return document.getElementById(el);
	}
	else if(typeof el=='object')
	{
		return el;
	}
}
/**
*将id，对象，id数组，对象数组加工成对应的对象数组
*@param 	{String||Object||Array} els id，对象，id数组，对象数组
*@return 	{Array} 对象数组
*/
function $A(els){
	var _els=[];
	if(els instanceof Array)
	{
		for(var i=0;i!=els.length;i++)
		{
			_els[_els.length]=$(els[i]);
		}
	}
	else if(typeof els=='object'&&typeof els['length']!='undefined'&&els['length']>0)
	{
		for(var i=0;i!=els.length;i++)
		{
			_els[_els.length]=$(els[i]);
		}
	}else
	{
		_els[0]=$(els);
	}
	return _els;
}

function StringBuilder(str)
{
	this._buffer=[];
	if(str){
		this.append(str);
	}
}

StringBuilder.prototype={	
	append:function(str){
		this._buffer.push(str);
	},
	appendFormat:function(format,parms){
		var count=arguments.length;
		if(count<2){
			return;
		}
		var _f=format;
		for(var i=1;i!=count;i++){
			_f=_f.replace('{'+(i-1)+'}',arguments[i]);
		}
		this.append(_f);
	},
	toString:function(){
		return this._buffer.join('')
	}
}

function Dictionary(){
	this.buffer=[];
}
Dictionary.prototype={
	get:function(key){
		if(this.contain(key)){			
			return this.buffer[key];
		}else{
			return null;
		}
	},
	add:function(key,value){
		if(!this.contain(key)){
			this.buffer[key]=value;
		}
	},
	remove:function(key){
		if(this.contain(key)){
			this.buffer[key]=undefined;
		}
	},
	getKeys:function(){
		var r=[];
		for(var k in this.buffer){
			r.push(k);
		}
		return r;
	},
	getValues:function(){
		var r=[];
		for(var k in this.buffer){
			r.push(this.get(k));
		}
		return r;
	},
	contain:function(key){
		return (typeof this.buffer[key]!='undefined');
	}
}


/*___Init___*/
BX.namespace('Dom');
BX.namespace('Event');
BX.namespace('Effect');
BX.namespace('Cookie');
/**
*BX DOM包 1.0.0
*utf-8版本
*http://www.bigtreexu.com
*/
BX.Dom={
	_batch:function(el,func)
	{
		var _el=$A(el);
		for(var i=0;i!=_el.length;i++)
		{
			if(_el[i])
			{
				func(_el[i]);
			}
		}
	},
	getMouseXY:function(e)
	{
		var _x=_y=0;
		_x=document.documentElement.scrollLeft;
		_y=document.documentElement.scrollTop;
		if(e.clientX||e.clientY)
		{
			_x+=e.clientX;
			_y+=e.clientY;
		}
		else if(e.pageX||e.pageY)
		{
			_x+=e.pageX;
			_y+=e.pageY;
		}
		return [_x,_y];
	},
	getXY:function(el)
	{
		var _x=_y=0;
		while(el)
		{
			_x+=el.offsetLeft;
			_y+=el.offsetTop;
			el=el.parentElement;
		}
		return [_x,_y];
	},
	getWH:function(el)
	{
		return [el.offsetWidth,el.offsetHeight];
	},
	setOpacity:function(els,val)
	{
		var _run=function(el)
		{
			el.style.MozOpacity=''+val/100;
			el.style.filter='Alpha(Opacity='+val+')';
		}
		this._batch(els,_run);
	},
	hide:function(els)
	{
		var _run=function(el)
		{
			el.style.display='none';
		}
		this._batch(els,_run);
	},
	show:function(els)
	{
		var _run=function(el)
		{
			el.style.display='block';
		}
		this._batch(els,_run);
	},
	getClass:function(el)
	{
		if($(el))
		{
			return $(el).className;
		}
		else
		{
			return;
		}
	},
	setClass:function(els,val)
	{
		var _run=function(el)
		{
			el.className=val;
		}
		this._batch(els,_run);
		
	},
	addClass:function(els,val)
	{
		if(!val)
		{
			return;
		}
		var _run=function(el)
		{
			var _cln=el.className.split(' ');
			for(var i=0;i!=_cln.length;i++)
			{
				if(_cln[i]==val)
				{
					return;
				}
			}
			if(el.className.length>0)
			{
				el.className=el.className+' '+val;
			}
			else
			{
				el.className=val;
			}
		}
		this._batch(els,_run);
	},
	hasClass:function(el,val)
	{
		var _bl=false;
		if($(el))
		{
			if(!el.className){return;}
			var _cln=el.className.split(' ');
			for(var i=0;i!=_cln.length;i++)
			{
				if(_cln[i]==val)
				{
					_bl=true;
					break;
				}
			}
		}
		return _bl;
	},
	removeClass:function(els,val)
	{
		if(!val)
		{
			return;
		}
		var _run=function(el)
		{
			var _cln=el.className.split(' ');
			var _s='';
			for(var i=0;i!=_cln.length;i++)
			{
				if(_cln[i]!=val)
				{
					_s+=_cln[i]+' ';
				}
			}
			if(_s==' ')
			{
				_s='';
			}
			if(_s.length!=0)
			{
				_s=_s.substr(0,_s.length-1);
			}
			el.className=_s;
		}
		this._batch(els,_run);
	},
	replaceClass:function(els,vala,valb)
	{
		if(!vala||!valb)
		{
			return;
		}
		var _run=function(el)
		{
			var _cln=el.className.split(' ');
			for(var i=0;i!=_cln.length;i++)
			{
				if(_cln[i]==vala)
				{
					_cln[i]=valb;
				}
			}
			el.className=_cln.join(' ');
		}
		this._batch(els,_run);
	},
	setStyle:function(els,styleName,styleValue)
	{
		var _run=function(el)
		{
			el.style[styleName]=styleValue;
		}
		this._batch(els,_run);
	},
	getStyle:function(el,styleName)
	{
		return el.style[styleName];
	},
	getElementsByClassName:function(parentEl,className,tagName){
		if(!parentEl||!className){
			return null;
		}
		var els=cds=[];
		cds=$(parentEl).childNodes;
		className=className.toUpperCase();
		for(var i=0;i<cds.length;i++){
			var _type=cds[i].nodeType;
			if(_type!=3&&_type!=8&&cds[i].className.toUpperCase()==className){
				if(!tagName||cds[i].nodeName.toUpperCase()==tagName.toUpperCase()){
					els[els.length]=cds[i];
				}
			}
		}
		return els;
	}
}

BX.Event={
	_cache:[],
	_batch:function(els,func)
	{
		try{
			els=$A(els);
			for(var i=0;i<els.length;i++){
				func(els[i]);
			}
		}
		catch(e)
		{
			//alert(e.description)
		}
	},
	/**
	*给指定元素增加监听，触发时执行一定的操作
	*示例：
	*var callBack=function(e,obj){alert(obj.id);};
	*BX.Event.addListener('sample','click',callBack);
	*这么在sample元素点击的时候将弹出它的id也就是sample
	*
	*@param  {String||Array||Object} el 代操作对象的id，对象本身，id数组，对象数组；
	*@param  {String} eventName 事件名称，比如click,load,mouseover,mouseout等
	*@param  {Function} func(_ev,_scope) 事件触发的方法,其中e为出发的事件对象，_scope为响应该对象的元素对象如div,window等
	*/
	addListener:function(els,eventName,func,range){
		var _run=function(el){
			var _scope=el;
			var _fn=function(e){
				var _ev=e||window.event;
				//传递相应事件的元素对象
				if(range){
					func.apply(range,[_ev,_scope])
				}
				else
				{
					func(_ev,_scope);
				}
			};
			if (!BX.Event._cache[el])
			{
				BX.Event._cache[el]=[];
			}
			/*防止重复绑定同样的事件*/
			if (BX.Event._cache[el][func]) 
			{
				//return false;
			}
			BX.Event._cache[el][func]=_fn;
			if(el.attachEvent){
				el.attachEvent('on'+eventName,_fn);
			}else if(el.addEventListener){
				el.addEventListener(eventName,_fn,false);
			}
			else
			{
				el['on'+eventName] = _fn;
			}
		};
		this._batch(els,_run);
	},
	removeListener:function(els,eventName,func)
	{
		var _run=function(el)
		{
			if(el.detachEvent)
			{
				el.detachEvent('on'+eventName,BX.Event._cache[el][func]);
			}
			else if(el.removeEventListener)
			{
				el.removeEventListener(eventName,BX.Event._cache[el][func],false);
			}
			else
			{
				el['on'+eventName] = null;
			}
			BX.Event._cache[el][func]=null;
		}
		this._batch(els,_run);
	}
}

/**
*	滑动模块(类自由落体运动)
*	版本：version 1.0.0
*	日期：11:50 2006-8-31
*	作者：bigtreexu
*	支持：IE 6.0+ ,FF 1.5+
/*--------------------------------------------------------------------------*/
BX.Effect=function(){}
BX.Effect.prototype={
	/**
	*@param {object} obj 需要操作的对象
	*@param {integer} startVal 初始的高度
	*@param {integer} endVal 结束的高度
	*@param {integer} speed 运动的速度，越大越慢
	*@param {function} func 完成滑动时要执行的操作，可以传递obj对象参数进行操作
	*@return null
	*/
	roll:function(obj,startVal,endVal,speed,func){
		if(!obj){return null;}
		if(startVal<0||endVal<0){return null;}
		var _in6=null,_height=_t=0,_handle=null;
		if(!speed){speed=50;}
		if(startVal<1){startVal=1;}//解决IE中高度不能为0的bug
		if(startVal-endVal<0){
			_handle=function(){
				if(_height-endVal>=0){
					clearInterval(_in6);
					if(func){func(obj);}
				}else{
					_height=parseInt(_height);_t++;_height=0.5*5*_t*_t;_height=parseInt(_height>=endVal?endVal:_height);
					obj.style.height=_height+'px';
				}
			};
		}else{
			_height=startVal;
			_handle=function(){
				if(_height-endVal<=0){
					clearInterval(_in6);
					if(func){func(obj);}
				}else{
					_height=parseInt(_height);_t++;_height-=0.5*5*_t*_t;_height=parseInt(_height<=endVal?endVal:_height);
					obj.style.height=_height+'px';
				}
			};
		}
		clearInterval(_in6);
		_in6=setInterval(_handle,speed);
	}
}
/**
 * @version 1.0.0
 * @author bigtreexu
 * @classDescription 用来操作客户端cookie的类
 */
BX.Cookie={
	/**
	 * 生成要求格式的过期时间
	 * @param {integer} days 有效的天数
	 * @param {integer} hours 有效的小时数
	 * @param {integer} minutes 有效的分钟数
	 * @return {datetime} 返回标准GMT时间
	 */
	getExpDate:function(days, hours, minutes) {
	    var expDate = new Date( );
	    if (typeof days == "number" && typeof hours == "number" && 
	        typeof hours == "number") {
	        expDate.setDate(expDate.getDate( ) + parseInt(days));
	        expDate.setHours(expDate.getHours( ) + parseInt(hours));
	        expDate.setMinutes(expDate.getMinutes( ) + parseInt(minutes));
	        return expDate.toGMTString( );
	    }
	},  
	getCookieVal:function(offset) {
	    var endstr = document.cookie.indexOf (";", offset);
	    if (endstr == -1) {
	        endstr = document.cookie.length;
	    }
	    return unescape(document.cookie.substring(offset, endstr));
	},
	/**
	 * 获取对应name的cookie值
	 * @param {String} name 要获取cookie的名称
	 * @return {string} 返回name要获取cookie的值
	 */
	getCookie:function(name) {
	    var arg = name + "=";
	    var alen = arg.length;
	    var clen = document.cookie.length;
	    var i = 0;
	    while (i < clen) {
	        var j = i + alen;
	        if (document.cookie.substring(i, j) == arg) {
	            return this.getCookieVal(j);
	        }
	        i = document.cookie.indexOf(" ", i) + 1;
	        if (i == 0) break; 
	    }
	    return "";
	},
	/**
	 * 以数组形式获取所有cookies
	 * @return {Array} 返回一个二维数组，Array[]['name'],Array[]['value']
	 */
	getCookies:function(){
		 _Cookie = new Array();
		 if(document.cookie.indexOf(";")!=-1){
	          var _sp,_name,_tp,_tars,_tarslength; 
	          var _item=document.cookie.split("; "); 
	          var _itemlength=_item.length; 
	          for(i=0;i<_itemlength;i++){
	          	_sp = _item[i].split("=");
	          	_name=_sp[0];
	          	_value =_sp[1];
	          	_coo = new Array();
	          	_coo['name']=_name;
	          	_coo['value']=_value;
	          	_Cookie.push(_coo);
	          }
	     } 
	     return _Cookie;
	},
	/**
	 * 设置cookies
	 * @param {String} name
	 * @param {String} value
	 * @param {DateTime} expires
	 * @param {String} path
	 * @param {String} domain
	 * @param {String} secure
	 */
	setCookie:function(name, value, expires, path, domain, secure) {
	    document.cookie = name + "=" + escape (value) +
	        ((expires) ? "; expires=" + expires : "") +
	        ((path) ? "; path=" + path : "") +
	        ((domain) ? "; domain=" + domain : "") +
	        ((secure) ? "; secure" : "");
	    
	},
	/**
	 * 通过设置过期时间删除参数确定的cookie
	 * @param {String} name
	 * @param {String} path
	 * @param {String} domain
	 */
	deleteCookie:function(name,path,domain) {
	    if (this.getCookie(name)) {
	        document.cookie = name + "=" +
	            ((path) ? "; path=" + path : "") +
	            ((domain) ? "; domain=" + domain : "") +
	            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	    }
	},
	/**
	 * 清除客户端所与cookies
	 * @method
	 */
	clearCookie:function(){
		cookies = this.getCookies();
		for(i=0;i<cookies.length;i++){
			this.deleteCookie(cookies[i]['name']);
		}
	},
	/**
	 * 获取客户端cookies字符串
	 * @return {String}
	 */
	getCookieString:function(){
		return document.cookie;	
	}
}

BX.Utility={
	CACHE_QUERY:null,
	request:function(name)
	{
		var _r=null;
		if(this.CACHE_QUERY==null) 
		{
			this.CACHE_QUERY=this.parseQueryString();
		}
		if(this.CACHE_QUERY&&this.CACHE_QUERY[name])
		{
			_r=decodeURI(this.CACHE_QUERY[name]);
		}
		return _r;
	},
	parseQueryString:function()
	{
		var _q=location.search;
		if(!_q)
		{
			return null;
		}
		_q=_q.substring(1);
		var _qs=_q.split('&');
		var _r=[];
		for(i=0;i<_qs.length;i++)
		{
			var __tmp=_qs[i].split('=');
			if(__tmp.length==2)
			{
				_r[__tmp[0]]=__tmp[1];
			}
		}
		return _r;
	}
}

BX.isEditMode=function(){var r=0;var h=location.pathname.toLowerCase();if(h.indexOf('.aspx')!=-1){r=1;}return r;}();
	
var O=BX.Dom;
var V=BX.Event;
var F=BX.Effect;
var C=BX.Cookie;
