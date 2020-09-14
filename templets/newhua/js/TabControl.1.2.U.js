
function Collection()
{
    this.items=[];
}

Collection.prototype={
    add:function(col)
    {
        this.items.push(col);
    },
    clear:function()
    {
        this.items=[];
    },
    getCount:function()
    {
    	return this.items.length;
    },
    each:function(func)
    {
    	for(var i=0;i<this.getCount();i++){
    		func(this.items[i]);
    	}
    },
    indexOf:function(item)
    {
    	var r=-1;
		for(i=0;i<this.getCount();i++ ){
            if(item==this.items[i]){ r=i; break;}
        }
        return r;
    },
    find:function(func)
    {
    	var r=null;
    	for(var i=0;i<this.getCount();i++){
    		if(func(this.items[i])==true){ r=this.items[i];break;}
    	}
    	return r;
    },
    findAll:function(func)
    {
    	var r=new Collection();
    	this.each(
    		function(item){ 
    			if(func(item)==true){ r.add(item); }
    		}
    	);
    	return r;
    }
}

function TabPage(triggerId,sheetId)
{
    this.trigger=$(triggerId);
    this.sheet=$(sheetId);
}

/**
* Title     : TabControl
* Author    : bigtreexu
* Version   : 1.1.1.U
* Desc      : 便于Seo，使用html的方式在页面中书写触发节点和内容节点，而不是使用js构造内容的方式,使用BX包
* modify    :  cici
* update    : 增加了tab切换时的回调功能。
* PubDate   : 2009-11-03 9:29
*/
function TabControl()
{
    this.styleName=null;
    this.tabPages=new Collection();
    this.currentTabPage=null;
    this.triggerType='click';
    this.defaultPage=0;
    this.enableSlide=false;
    this.slideInterval=3000;
	this.preButton=null;
	this.nextButton=null;
	this.onComplete = null;
	this.options = null;
	this.currentClassName ='current';
    
    this.onChanging=new Collection();
    /*添加默认事件处理句柄*/
    this.onChanging.add( this.defaultChangingHandler );
    
    this.onInit=new Collection();
    /*添加默认初始化句柄*/
    this.onInit.add(this.defaultInitHandler);
    this.onInit.add(this.autoSlideInitHandler);
    
    this.onAdding=new Collection();
    /*标签页添加事件处理*/
    this.onAdding.add( this.defaultAddingHandler );
    
    /*private*/
    this._autoSlideEv=null;

}

/**
* 两个主要方法：
* add(tagPage)
* addRange(triggers,sheets);
* select(i);
* init();
*/
TabControl.prototype={
    add:function(tabPage)
    {        
        this.tabPages.add(tabPage);        
        var handler=function(func){ func(tabPage); };
        this.onAdding.each( handler );
    },
    addRange:function(triggers,sheets)
    {
        if(triggers.length==0||triggers.length!=sheets.length){ return; }
        for(var i=0;i<triggers.length;i++){
            var tabPage= new TabPage(triggers[i],sheets[i]);
            this.add(tabPage);
        }
    },
	pre:function()//cds add 
	{
		var i= this.indexOf(this.currentTabPage.trigger);  
		this.select(i-1);
	},
	next:function()//cds add 
	{
		var i= this.indexOf(this.currentTabPage.trigger);  
		this.select(i+1);
	},	
    defaultAddingHandler:function(tabPage)
    {
    	
    },
    init:function()
    {
        var _=this;
        var handler=function(func){	func(_);}
        
        if(this.tabPages.getCount()==0){return;}      
        if(this.currentTabPage==null){
            this.currentTabPage=this.tabPages.items[this.defaultPage];
        }        
        this.onInit.each(handler);
		
		if($(this.preButton)) $(this.preButton).onclick=this.GetFunction(this,"pre");
		if($(this.nextButton)) $(this.nextButton).onclick=this.GetFunction(this,"next");
    },
    defaultInitHandler:function(obj)
    {
		var handler=function(item){ V.addListener(item.trigger,obj.triggerType,obj.selectHanlder,obj);O.hide(item.sheet); };		
		obj.tabPages.each(handler);		
        obj.select(obj.defaultPage);
    },
    autoSlideInitHandler:function(o){ 	 
    	if(!o.enableSlide){return;}
    	var delayStartEv=null;
    	var delayStartHandler=function(){
    		delayStartEv=setTimeout(function(){o.autoSlideHandler(o);},300);
    	};
    	var clearHandler=function(){
    		clearTimeout(delayStartEv);
    		clearInterval(o._autoSlideEv);
    	};
    	var handler=function(item){
    		V.addListener(item.trigger,o.triggerType,clearHandler,o); 
    		V.addListener(item.sheet,'mouseover',clearHandler,o); 
    		V.addListener([item.trigger,item.sheet],'mouseout',delayStartHandler,o);
    	};
    	o.tabPages.each(handler);
    	o.autoSlideHandler(o);
    },
    autoSlideHandler:function(o){
    	var count=o.tabPages.getCount();
    	clearInterval(o._autoSlideEv);    	
    	o._autoSlideEv=setInterval(function(){
    		var i=o.indexOf(o.currentTabPage.trigger);
    		if(i==-1){return;}
    		i++;
    		if(i>=count){i=0;}
    		o.select(i);
    	},o.slideInterval);
    },
    selectHanlder:function(e,o)
    {
        var i= this.indexOf(o);        
        this.select(i);
    },
    select:function(i)
    {
        if(i<0||i>=this.tabPages.getCount()){return;}
        var _=this;
        var page=this.tabPages.items[i];
        
        var handler=function(func){ func(_.currentTabPage,page);};
    	this.onChanging.each(handler);
    	
        this.currentTabPage=page;
		
		//设置上一张下一张按钮状态
		if($(this.preButton))
		{
			$(this.preButton).className="enable";
			if(i==0) 
				$(this.preButton).className="unenable";
		}
		if($(this.nextButton))
		{
			$(this.nextButton).className="enable";
			if(i==this.tabPages.getCount()-1) 
				$(this.nextButton).className="unenable";
		}
		
		if(typeof(this.onComplete)=="function")
		{
			this.onComplete(this.options,i,this.currentTabPage);
		}
		
    },    
   	defaultChangingHandler:function(oldTabPage,newTabPage)
   	{
   		if(oldTabPage.sheet){
        	O.hide(oldTabPage.sheet);
        }
        if(newTabPage.sheet){
        	O.show(newTabPage.sheet);
        }
        O.removeClass(oldTabPage.trigger,'current');        
        O.addClass(newTabPage.trigger,'current');

   	},
    indexOf:function(trigger)
    {
        var r=-1;
        var handler=function(item){return item.trigger==trigger;};
        var item=this.tabPages.find( handler );
        if(item!=null){
        	r=this.tabPages.indexOf(item);
        }
        return r;
    },
	GetFunction:function(variable,method,param)
	{
		return function()
		{
			variable[method](param);
		}
	}
}
