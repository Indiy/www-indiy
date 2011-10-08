// JavaScript Document:

function ra_ActiveSearch(cid)
{
var i;
this.instance_name=null;
	
this.instance_id=cid;

this.id_ctl_searchBox="div_searchBox_"+cid;
this.id_ctl_searchText="searchText_"+cid;
this.id_ctl_searchList="div_searchList_"+cid;
this.id_ctl_searchListItem="div_searchListItem_"+cid+"_";
this.class_searchBox="searchBox";
this.class_searchText="searchText";
this.class_searchListItem="searchListItem";
this.class_searchListItemMatch="searchListItemMatch";
this.class_searchListItemSelected="searchListItemSelected";
this.class_searchList="searchList";
this.class_searchListItemCategory="searchListItemCategory";
this.class_search_thumbnail="search_thumbnail";
this.class_search_text="search_text";
this.listItemSelected=-1;
this.searchText="";
this.searchTextEmpty="SEARCH     ";
this.listItemsChanged=true;
this.searchBoxServer="searchbox.php";
this.listItems=[];
this.listResultItems=[]; // intermediate search items
this.listWords=[];
this.listSubWords=[];
this.lastFind="";
this.lastEnter=null;

this.maxResults=7;
this.artistLogoFolder="http://myartistdna.fm/artists/images/";
this.songImagesFolder="http://myartistdna.fm/artists/images/";
this.mediaImagesFolder="http://myartistdna.fm/artists/images/";
this.url_suffix=".myartistdna.fm";

this.artistFormat="<img src=\'{artist_logo}\' class=\'"+this.class_search_thumbnail+"\' /><div class=\'"+this.class_search_text+"\'><div class='heading'>Artist:</div> {artist_name}<br />{artist_site}</div>";
this.songFormat="<img src=\'{song_image}\' class=\'"+this.class_search_thumbnail+"\' /><div class=\'"+this.class_search_text+"\'><div class='heading'>Artist:</div> {artist_name}<br /><div class='heading'>Song:</div> {song_name}</div>";
this.mediaFormat="<img src=\'{media_image}\' class=\'"+this.class_search_thumbnail+"\' /><div class=\'"+this.class_search_text+"\'><div class='heading'>Artist:</div> {artist_name}<br /><div class='heading'>Album:</div> {media_name}</div>";

this.listItems_ST="";
this.listWords_ST="";
this.listSubWords_ST="";

this.getSearchItems=function(S,pfx)
	{
	var me=this;
	if(pfx!=null)
		pfx="&pfx="+pfx;
	$.ajax({
  				url: this.searchBoxServer,
				cache: false,
  				data: "ff="+S+pfx,
				error:function(x)
					{
					alert("problem occured with request!!"+x);
					},
  				success: function(data)
	{
	// this.listItems=[];
	this.listItemSelected=-1;
	
	var rec_sep=String.fromCharCode(13);
	var fld_sep=String.fromCharCode(1);
	var grp_marker="*";
	
	var aItems=data.split(rec_sep);
	var i;
	var aFields=null;
	var group=null;
	for(i=0;i<aItems.length;i++)
		{
		aFields=aItems[i].split(fld_sep);		
		if(aFields.length==1)
			{
			if(aFields[0].indexOf(grp_marker)>-1)
				group=aFields[0].substring(1);				
			// alert("NEW GROUP: "+group);
			continue;
			}
		// alert("["+group+"] aFields: "+aFields);
		if(group=="artists")			
			me.addArtist(aFields[0],aFields[1],aFields[2],aFields[3],aFields[4]);						
		else
		if(group=="songs")
			{
			if(me.getListItem(parseInt(aFields[1]))!=null)
				me.addSong(aFields[0],aFields[1],aFields[2],aFields[3]);			
			}
		else
		if(group=="media")
			{
			if(me.getListItem(parseInt(aFields[1]))!=null)
				me.addMedia(aFields[0],aFields[1],aFields[2],aFields[3]);			
			}
		}
				
	// this.renderItems();
	},  				
				});
	}

// this.searchItemsCatcher=

this.enterListItem=function(idx)
	{
	if(this.listResultItems.length==0)
		return;
		
	if(idx)	
		{
		this.listItemSelected=idx;		
		this.lastEnter=null; // always open url when idx specified
		}
		
	if(this.listItemSelected>(this.listResultItems.length-1) || this.listItemSelected<0)
		this.listItemSelected=0;
		
	if(this.listResultItems[this.listItemSelected].id!=this.lastEnter)
		{
		
		var sid=this.listResultItems[this.listItemSelected].id;
		var website=null;
		var hid=sid.split("_");			
		if(hid.length>1)
			{
			var LIA=this.getListItem(hid[0]);
			// alert("id: "+sid);
			if(LIA)
				website=LIA.data.url;
			}
		else
			website=this.listResultItems[this.listItemSelected].data.url;
			
		// alert("Selected item id: "+this.listResultItems[this.listItemSelected].id+" site: "+website);
		
		if(website)
			{			
			if(website.indexOf(".")<0)
				website=("http://"+website+this.url_suffix);
			window.open(website,"_blank");
			}
			
		this.lastEnter=sid;
		}
	}


this.selectListItemNext=function()
	{
	if(this.listItems.length==0)
		return;
	
	this.listItemSelected=(this.listItemSelected<this.listResultItems.length) ? (this.listItemSelected+1):0;
	this.selectListItem(this.listItemSelected);
	}
	
this.selectListItemPrevious=function()
	{
	if(this.listItems.length==0)
		return;
	
	this.listItemSelected=(this.listItemSelected>0) ? (this.listItemSelected-1):(this.listResultItems.length-1);
	this.selectListItem(this.listItemSelected);
	}


this.selectListItem=function(i)
	{
	
	var obj_edit=$('.'+this.class_searchListItemMatch);
	var s_remove=this.class_searchListItemMatch;
	var s_replace=this.class_searchListItem;
	obj_edit.removeClass(s_remove);
	obj_edit.addClass(s_replace);
	
	if(i>-1)
		{
		var obj=$('#'+this.id_ctl_searchListItem+i);
		var obj_searchText=$('#'+this.id_ctl_searchText);
		obj.removeClass(this.class_searchListItem);
		obj.addClass(this.class_searchListItemMatch);
		// obj_searchText.val(this.listItems[i].text);
		}
	}

this.getInstanceName=function(id)
	{
	for(var i in window)
	{
	try
	{
	// alert("window["+i+"]");
	if(window[i].instance_id==id)
		{
		this.instance_name=i;
		return i;		
		}
	}
	catch(e)
		{
		}
	}
	return null;
	}

// alert("this="+typeof(this)+" "+this.toString()+" "+this.__proto__.arguments[0].name);
ra_ActiveSearch.prototype.removePunctuation=function(S)
	{
	var i;
	var N="",c;
	for(i=0;i<S.length;i++)
		{
		c=S.charAt(i);
		if((c>="a" && c<="z") || (c>="A" && c<="Z") || (c>="0" && c<="9") || c==" ")
			N+=c;
		}
	return N;
	}


ra_ActiveSearch.prototype.isValidID=function()
		{
		return ($('#'+this.instance_id)!=null);
		}
		
ra_ActiveSearch.prototype.showItemList=function(bShow)
	{
	var obj=$('#'+this.id_ctl_searchList);
	if(bShow==true)
		obj.show();
	else
		obj.hide();
	}

ra_ActiveSearch.prototype.reset=function()
		{
		var obj=$('#'+this.instance_id);
		
		if(obj==null)
			return;		
		this.listItems=[]; // reset
		var S="<div id=\""+this.id_ctl_searchBox+"\" class=\""+this.class_searchBox+"\">";
		S+="<input type=\"text\" name=\""+this.id_ctl_searchText+"\" id=\""+this.id_ctl_searchText+"\" class=\""+this.class_searchText+"\" value=\""+this.searchTextEmpty+"\"></div>";
		S+="<div id=\""+this.id_ctl_searchList+"\" class=\""+this.class_searchList+"\">";
      //<div id="searchList1" class="searchListItem">TEST 1<br />This is number 1<br /></div>
      //<div id="searchList2" class="searchListItem">TEST 2</div>	  
    	S+="</div>";
		obj.html(S);
		
		this.instance_name=this.getInstanceName(this.instance_id);
		
		this.attachEvents();
		}
		
ra_ActiveSearch.prototype.findItems=function(SEARCH)
	{
	if(SEARCH.length<1)
		this.showItemList(false);
		
	SEARCH=this.removePunctuation(SEARCH.toLowerCase());	
		
	if(this.lastFind==SEARCH)
		return;
		
	this.lastFind=SEARCH;
		
	this.listResultItems=[];	
	
	
	var words=SEARCH.split(" ");
	
	
	
	/*
	var dout=$("#debug");
		
	dout.html(this.listWords_ST);
	*/
	
	var aRecognized=[];	
	var aCollisionList=[];	
	var i,found=0;
	var test="";
	for(i=0;i<words.length;i++)
		{		
		if(this.getListWord(words[i])!=null)			
			aRecognized[words[i]]=this.getListWord(words[i]).items;
		else
			{			
			// find all the words it could be
			
			var word=words[i];
			var ww=word.charAt(0);			
			var ii;	
			var aSubWords=this.getListSubWords(this.listSubWords[ww]);
			
			if(aSubWords)
				{
				for(ii=0;ii<aSubWords.length;ii++)
 					{				
					if(word==aSubWords[ii].substring(0,word.length))			
						aRecognized[aSubWords[ii]]=this.getListWord(aSubWords[ii]).items;			
					}
				
				}			
			}
		}
		
		
	// this.listWords[words[w]][this.listItems[i]]=this.listItems[i];
	var j; // collision list of recognized words
	
	j=0;
	var test="";
		
	// new list that 
	var text;
	var pass=words.length;	
	var count=0;
	var aCollisions=[];	
	
	for(j in aRecognized)
		{			
		for(i in aRecognized[j])
			{
			if(aCollisions[i]==null)
				aCollisions[i]=0;
			aCollisions[i]++;
			if(aCollisions[i]==pass)								
				this.listResultItems[count++]=this.getListItem(i);				
			}
		}	
		
	
	this.listResultItems.sort(function(a,b)
								 {
								 var s1,s2;
								 s1=a.category+" "+a.text;
								 s2=b.category+" "+b.text;
								 if(s1>s2)
								 	return 1;
								 else
								 if(s1<s2)
								    return -1;
								 return 0;								 
								 }
								 );
	
	// create regular expressions
	// 1. create two lists
	//    a. create a list of recognized index words
	//         .. also create regular expressions for words
	//    b. create a list of "other" words that contain the unrecognized words as substrings
	//         .. also create collective regular expression for substrings 
	// 2. create a collision collection of possible result matches using list a and b
	// 3. create a result list collision collection and regular expressions
	
	if(this.listResultItems.length>0)
		{
		this.listItemsChanged=true;
		this.renderItems();
		}
	
	}
	
ra_ActiveSearch.prototype.addArtist=function(sid,name,url,logo,website)
	{
	var scategory="// Artist Name //";
	var stext=name;	
	var item_data={type:'A',site:website,artist_logo:logo,artist_name:name,url:url};
	this.addItem(sid,scategory,stext,item_data);
	}
	
ra_ActiveSearch.prototype.addSong=function(sid,artistid,name,image)
	{
	var scategory="// Song Title //";
	var stext=name;	
	var hid=artistid+"_S"+sid;
	var item_data={type:'S',song_image:image,song_name:name};
	this.addItem(hid,scategory,stext,item_data);
	}
	
ra_ActiveSearch.prototype.addMedia=function(sid,artistid,name,image)
	{
	var scategory="// Album Title //";
	var stext=name;	
	var hid=artistid+"_M"+sid;
	var item_data={type:'M',media_image:image,media_name:name};
	this.addItem(hid,scategory,stext,item_data);
	}

ra_ActiveSearch.prototype.getListItem=function(sid)
	{
	var sfind="["+sid+":";
	var ipos=this.listItems_ST.indexOf(sfind);
	if(ipos<0)
		return null;
	ipos+=(sfind.length);
	var ipos2=this.listItems_ST.indexOf(" ",ipos);
	if(ipos2<0)
		return null;
	
	var slot=this.listItems_ST.substring(ipos,ipos2);
	var i=parseInt(slot);
	// alert("slot: "+slot+" ipos: "+ipos+" ipos2: "+ipos2);
	return this.listItems[i];
	}
	
	
ra_ActiveSearch.prototype.getListWord=function(word)
	{
	var sfind="["+word+":";
	var ipos=this.listWords_ST.indexOf(sfind);
	if(ipos<0)
		return null;
	ipos+=(sfind.length);
	var ipos2=this.listWords_ST.indexOf(" ",ipos);
	if(ipos2<0)
		return null;
	
	var slot=this.listWords_ST.substring(ipos,ipos2);
	var i=parseInt(slot);
	// alert("slot: "+slot+" ipos: "+ipos+" ipos2: "+ipos2);
	return this.listWords[i];	
	}
	
ra_ActiveSearch.prototype.addListWord=function(word,sid)
	{	
	var obj=this.getListWord(word);
	if(obj)
		{
		obj.items[sid]=1;
		return;
		}
	var li_len=(this.listWords!=null) ? this.listWords.length:0; 		
	this.listWords[li_len]={items:[]};
	this.listWords[li_len].items[sid]=1;
	
	var currentLen=this.listWords_ST.length;
	var awi=("["+word+":"+li_len+" ");
	this.listWords_ST+=awi;
	/*
	if(this.listWords_ST.length!=(currentLen+awi.length))
		alert("word not added: "+word);
	if(this.listWords_ST.indexOf(awi)<0)
		alert("word not added: "+word);
	*/
	}
	
ra_ActiveSearch.prototype.addListSubWord=function(obj,word)
	{	
	if(obj.words.indexOf("|"+word+"|")>-1)
		return;
	obj.words+=(word+"|");
	}
	
ra_ActiveSearch.prototype.getListSubWords=function(obj)
	{
	if(obj==null || obj.words==null)
		return null;
	var aWords=obj.words.split("|");
	if(aWords.length<1)
		return null;
	return aWords;
	}

		
ra_ActiveSearch.prototype.addItem=function(sid,scategory,stext,item_data)
	{
		/*
		purpose: adds an item the results list
		sid - id of item
		shtml - markup displayed in results list
		stext - text of item
		*/
	// alert("addItem: "+sid+" category: "+scategory+" stext: "+stext);
	var obj=$('#'+this.id_ctl_searchList);
	if(obj==null)
		return;
	
		
	stext=this.removePunctuation(stext);
	
	var li_len=(this.listItems!=null) ? this.listItems.length:0; 
		
	this.listItems[li_len]={id:sid,category:scategory,text:null,data:item_data};  // text not needed after indexing
	this.listItems_ST+=("["+sid+":"+li_len+" ");
	
	
	this.listItemsChanged=true;
	
	// sort
	
	var text=stext.toLowerCase();
	var words=text.split(" ");
	var w,l,ww,ss;
	for(w=0;w<words.length;w++)
	  {
	  ww=words[w];	  	  
	  ss=ww.charAt(0);
	  if((ss>="a" && ss<="z") || (ss>="0" && ss<="9"))
	  	{	
			
			this.addListWord(ww,sid);	  		
	  
	  		if(this.listSubWords[ss]==null)	  	
	  			this.listSubWords[ss]={words:"|"};	  
	  		this.addListSubWord(this.listSubWords[ss],ww);
		
		}
	  }	
	}
	
ra_ActiveSearch.prototype.renderItems=function()
	{
		
	this.showItemList(this.listResultItems.length>0);
	
		
	var i;
	var S="";
	var cat=null;
	var class_auto;	
	var html_out;
	var item_type;
	var sid;
	var LI;
	var obj=$('#'+this.id_ctl_searchList);
	var resultLimit=(this.listResultItems.length<this.maxResults) ? this.listResultItems.length:this.maxResults;
	for(i=0;i<resultLimit;i++)
		{
		
		if(cat!=this.listResultItems[i].category)
			{
			
			S+="<div  class=\""+this.class_searchListItemCategory+"\">";
			S+=this.listResultItems[i].category;
			S+="</div>";
			
			cat=this.listResultItems[i].category;
			}
		class_auto=(i==0) ? this.class_searchListItemMatch:this.class_searchListItem;		
		S+="<div id=\""+this.id_ctl_searchListItem+i+"\" class=\""+class_auto+"\">";
		LI=this.listResultItems[i];
		
		
		item_type=LI.data.type;
		sid=LI.id;
		
			
		if(item_type=="A")
			{			
			html_out=this.artistFormat;			
			
			if(LI.data.artist_logo)
				html_out=html_out.replace("{artist_logo}",this.artistLogoFolder+LI.data.artist_logo);
			else
				html_out=html_out.replace("{artist_logo}","no logo");
			html_out=html_out.replace("{artist_name}",LI.data.artist_name);
			if(LI.data.artist_site)
				html_out=html_out.replace("{artist_site}",LI.data.artist_site);
			else
				html_out=html_out.replace("{artist_site}","");
			}
		else
		if(item_type=="S")
			{
			html_out=this.songFormat;
			var hid=sid.split("_");			
			var LIA=this.getListItem(hid[0])
			if(LI.data.song_image)
				html_out=html_out.replace("{song_image}",this.songImagesFolder+LI.data.song_image);
			else
				html_out=html_out.replace("{song_image}","no image");
			html_out=html_out.replace("{song_name}",LI.data.song_name);			
			if(LIA)
				html_out=html_out.replace("{artist_name}",LIA.data.artist_name);			
			else
				html_out=html_out.replace("{artist_name}","Unknown");			
			}
		else
		if(item_type=="M")
			{
			html_out=this.mediaFormat;
			// this.mediaFormat="<img src=\'{media_image} />Artist: {artist_name}<br />Song: {media_name}<br />";
			var hid=sid.split("_");						
			var LIA=this.getListItem(hid[0])
			if(LI.data.media_image)
				html_out=html_out.replace("{media_image}",this.mediaImagesFolder+LI.data.media_image);
			else
				html_out=html_out.replace("{media_image}","no image");
			html_out=html_out.replace("{media_name}",LI.data.media_name);			
			if(LIA)
				html_out=html_out.replace("{artist_name}",LIA.data.artist_name);			
			else
				html_out=html_out.replace("{artist_name}","Unknown");			
			}
		else
			{
			html_out="Problem!<br /><br />";
			}
		S+=html_out;
		S+="</div>";
		
		}	
	obj.html(S);
	
	

	
	this.showItemList(true);
	
	this.listItemsChanged=false;
	
	this.listItemSelected=0;
	
	// check
		
	this.attachEvents();
	}
		
ra_ActiveSearch.prototype.attachEvents=function()
	{
	var obj_searchText=$('#'+this.id_ctl_searchText);
	var obj_searchList=$('#'+this.id_ctl_searchList);	
	var searchTextEmpty=this.searchTextEmpty;
	var self=this.instance_name+".";
	// global
	$(document).keydown(function(event)
						 {
						// alert("which: "+event.which);
						 if(event.which==13)
						 	{							
							var fn=new Function(self+"enterListItem()");
							fn();
							obj_searchText.val(searchTextEmpty);
							obj_searchList.hide();
							// event.preventDefault();
							}
						 else
						 if(event.which==40)
						 	{
							var fn=new Function(self+"selectListItemNext()");
							fn();
							event.preventDefault();
							}
						 else
						 if(event.which==38)
						 	{
							var fn=new Function(self+"selectListItemPrevious()");
							fn();			
							event.preventDefault();
							}
						 }
						 );
		
	// edit box events
	//  text change
	//  keystrokes
	var obj=$('#'+this.id_ctl_searchText);
	
		
	obj.focus(function() 
						{
						var v=$(this).val();
						if(v==searchTextEmpty)
							{
							$(this).val('');
							obj_searchList.hide();
							}
						}
						);
	
	obj.blur(function() 
						{
						var v=$(this).val();
						if(v=="")
							{
							$(this).val(searchTextEmpty);
							obj_searchList.hide();
							}
						}
						);
	
	
	obj.change(function() 
						{
						
						}
						);
	var renderItems=self+"renderItems()";
	var findItems=self+"findItems";
	
	obj.keydown(function(event)
						 {	
						 	var fn=null;
							var value=$(this).val();
						
							if(event.which==8)
								{
								if(value.length>0)
									value=value.substring(0,value.length-1);							
								
								fn=new Function(findItems+"(\""+value+"\")");
								fn();
								
								
								}						
						 }
						 );
	
	obj.keypress(function(event) 
						{
						
						var fn=null;
						var value=$(this).val();
						
						var key=(event.which!=32) ? String.fromCharCode(event.which):"";
						
						if((key>="a" && key<="z") || (key>="A" && key<="Z") || (key>="0" && key<="9"))
							{						
							value+=key;
							fn=new Function(findItems+"(\""+value+"\")");
							fn();						
							
							}
						
													
						// alert("elapsed: "+elapsed);
						}
						);
	
	// item events
	//   mouse over
	//   mouse click
	obj=$('.'+this.class_searchListItem+', .'+this.class_searchListItemMatch);
	var listItems=this.listItems;
	
	obj.hover(function()
					   {
						var fn=new Function(self+"selectListItem(-1)");
						fn();						
					   }
					   );
	obj.click(function()
					   {
						  var i=0;
						  var S=$(this).attr('id');
						
						  i=S.lastIndexOf("_");						  
						  if(i>0)
						  	{
							i=parseInt(S.substring(i+1),10);
							// this.listItemSelected=i;
							
							// alert("i: "+i+this.listResultItems[i].id);
							
							// alert("clicked item "+i+" "+listItems[i].text);
							var fn=new Function(self+"enterListItem("+i+")");
							fn();
							obj_searchText.val(searchTextEmpty);
							obj_searchList.hide();
							}						  
					   }
					   );
	
	
	
	// magnifying glass
	obj=$('#'+this.id_ctl_searchBox);
	var obj_searchText=$('#'+this.id_ctl_searchText);
	var searchTextEmpty=this.searchTextEmpty;
	obj.click(function(event)
					   {						  
						if(obj_searchText.is(":focus")==false && (obj_searchText.val()!="" && obj_searchText.val()!=searchTextEmpty))
						  	{							
							 var fn=new Function(self+"enterListItem()");
							fn();
							obj_searchText.val(searchTextEmpty);
							obj_searchList.hide();
							event.preventDefault();
							}
					   }
					   );
	
	
	
	}
	
	
	
}

