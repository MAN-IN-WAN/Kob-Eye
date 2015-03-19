(function(){tinymce.create('tinymce.plugins.Preview',{init:function(ed,url){var t=this;t.editor=ed;ed.addCommand('mcePreview',t._preview,t);ed.addButton('preview',{title:'preview.preview_desc',cmd:'mcePreview'});},getInfo:function(){return{longname:'Preview',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/preview',version:tinymce.majorVersion+"."+tinymce.minorVersion};},_preview:function(){var ed=this.editor,win,html,c,pos,pos2,css,i,page=ed.getParam("plugin_preview_pageurl",null),w=ed.getParam("plugin_preview_width","550"),h=ed.getParam("plugin_preview_height","600");if(page){ed.windowManager.open({file:ed.getParam("plugin_preview_pageurl",null),width:w,height:h},{resizable:"yes",scrollbars:"yes",inline:1});}else{win=window.open("","mcePreview","menubar=no,toolbar=no,scrollbars=yes,resizable=yes,left=20,top=20,width="+w+",height="+h);html="";c=ed.getContent();pos=c.indexOf('<body');css=ed.getParam("content_css",'').split(',');tinymce.map(css,function(u){return ed.documentBaseURI.toAbsolute(u);});if(pos!=-1){pos=c.indexOf('>',pos);pos2=c.lastIndexOf('</body>');c=c.substring(pos+1,pos2);}html+=ed.getParam('doctype');html+='<html xmlns="http://www.w3.org/1999/xhtml">';html+='<head>';html+='<title>'+ed.getLang('preview.preview_desc')+'</title>';html+='<base href="'+ed.documentBaseURI.getURI()+'" />';html+='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';for(i=0;i<css.length;i++)html+='<link href="'+css[i]+'" rel="stylesheet" type="text/css" />';html+='</head>';html+='<body dir="'+ed.getParam("directionality")+'" onload="window.opener.tinymce.EditorManager.get(\''+ed.id+'\').plugins[\'preview\']._onLoad(window,document);">';html+=c;html+='</body>';html+='</html>';win.document.write(html);win.document.close();}},_onLoad:function(w,d){var t=this,nl,i,el=[],sv,ne;t._doc=d;w.writeFlash=t._writeFlash;w.writeShockWave=t._writeShockWave;w.writeQuickTime=t._writeQuickTime;w.writeRealMedia=t._writeRealMedia;w.writeWindowsMedia=t._writeWindowsMedia;w.writeEmbed=t._writeEmbed;nl=d.getElementsByTagName("script");for(i=0;i<nl.length;i++){sv=tinymce.isIE?nl[i].innerHTML:nl[i].firstChild.nodeValue;if(new RegExp('write(Flash|ShockWave|WindowsMedia|QuickTime|RealMedia)\\(.*','g').test(sv))el[el.length]=nl[i];}for(i=0;i<el.length;i++){ne=d.createElement("div");ne.innerHTML=d._embeds[i];el[i].parentNode.insertBefore(ne.firstChild,el[i]);}},_writeFlash:function(p){p.src=this.editor.documentBaseURI.toAbsolute(p.src);TinyMCE_PreviewPlugin._writeEmbed('D27CDB6E-AE6D-11cf-96B8-444553540000','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0','application/x-shockwave-flash',p);},_writeShockWave:function(p){this.editor.documentBaseURI.toAbsolute(p.src);TinyMCE_PreviewPlugin._writeEmbed('166B1BCA-3F9C-11CF-8075-444553540000','http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0','application/x-director',p);},_writeQuickTime:function(p){this.editor.documentBaseURI.toAbsolute(p.src);TinyMCE_PreviewPlugin._writeEmbed('02BF25D5-8C17-4B23-BC80-D3488ABDDC6B','http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0','video/quicktime',p);},_writeRealMedia:function(p){this.editor.documentBaseURI.toAbsolute(p.src);TinyMCE_PreviewPlugin._writeEmbed('CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0','audio/x-pn-realaudio-plugin',p);},_writeWindowsMedia:function(p){this.editor.documentBaseURI.toAbsolute(p.src);p.url=p.src;TinyMCE_PreviewPlugin._writeEmbed('6BF52A52-394A-11D3-B153-00C04F79FAA6','http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701','application/x-mplayer2',p);},_writeEmbed:function(cls,cb,mt,p){var h='',n,d=t._doc,ne,c;h+='<object classid="clsid:'+cls+'" codebase="'+cb+'"';h+=typeof(p.id)!="undefined"?'id="'+p.id+'"':'';h+=typeof(p.name)!="undefined"?'name="'+p.name+'"':'';h+=typeof(p.width)!="undefined"?'width="'+p.width+'"':'';h+=typeof(p.height)!="undefined"?'height="'+p.height+'"':'';h+=typeof(p.align)!="undefined"?'align="'+p.align+'"':'';h+='>';for(n in p)h+='<param name="'+n+'" value="'+p[n]+'">';h+='<embed type="'+mt+'"';for(n in p)h+=n+'="'+p[n]+'" ';h+='></embed></object>';d._embeds[d._embeds.length]=h;}});tinymce.PluginManager.add('preview',tinymce.plugins.Preview);})();

(function() {
	tinymce.create('tinymce.plugins.BBCodePlugin', {
		init : function(ed, url) {
			var t = this, dialect = ed.getParam('bbcode_dialect', 'punbb').toLowerCase();

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t['_' + dialect + '_bbcode2html'](o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.set)
					o.content = t['_' + dialect + '_bbcode2html'](o.content);

				if (o.get)
					o.content = t['_' + dialect + '_html2bbcode'](o.content);
			});
		},

		getInfo : function() {
			return {
				longname : 'BBCode Plugin',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/bbcode',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		// Private methods
		setInLink : function (s){
			s = s.replace(/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/gi,"[url=$2|||$1$3]$4[/url]");
			s = s.replace(/\[url=(.*?)\|\|\|(.*?)title=\"(.*?)\"(.*?)\]/gi,"[url=$1|$3||$2$4]");
			s = s.replace(/\[url=(.*?)\|(.*?)\|\|(.*?)rel=\"(.*?)\"(.*?)\]/gi,"[url=$1|$2|$4|$3$5]");
			s = s.replace(/\[url=(.*?)\|(.*?)\|(.*?)\|(.*?)class=\"(.*?)\"(.*?)\]/gi,"[url=$1|$2|$3|$5]");
			s = s.replace(/\[url=(.*?)\|(.*?)\|(.*?)\|(.*?)\]/gi,"[url=$1|$2|$3|$4]");
			return s;
		},
		// HTML -> BBCode in PunBB dialect
		_punbb_html2bbcode : function(s) {
			s = tinymce.trim(s);

			function rep(re, str) {
				s = s.replace(re, str);
			};
			//A
			if (s.match(/<a.*?>(.*?)<\/a>/gi)) s = this.setInLink(s);
			//FONT
			rep(/<font.*?color=\"(.*?)\".*?class=\"codeStyle\".*?>(.*?)<\/font>/gi,"[code][color=$1]$2[/color][/code]");
			rep(/<font.*?color=\"(.*?)\".*?class=\"quoteStyle\".*?>(.*?)<\/font>/gi,"[quote][color=$1]$2[/color][/quote]");
			rep(/<font.*?class=\"codeStyle\".*?color=\"(.*?)\".*?>(.*?)<\/font>/gi,"[code][color=$1]$2[/color][/code]");
			rep(/<font.*?class=\"quoteStyle\".*?color=\"(.*?)\".*?>(.*?)<\/font>/gi,"[quote][color=$1]$2[/color][/quote]");
   			rep(/<font>(.*?)<\/font>/gi,"$1");
			rep(/<font.*?color=\"(.*?)\".*?>(.*?)<\/font>/gi,"[color=$1]$2[/color]");
			//SPAN                                                               	
			rep(/<span style=\"color: ?(.*?);\">(.*?)<\/span>/gi,"[color=$1]$2[/color]");
			rep(/<span style=\"font-size:(.*?);\">(.*?)<\/span>/gi,"[size=$1]$2[/size]");
			rep(/<span style=\"text-decoration: ?underline;\">(.*?)<\/span>/gi,"[u]$1[/u]");
			rep(/<span style=\"font-weight: ?bold;\">(.*?)<\/span>/gi,"[b]$1[/b]");
   			rep(/<span class=\"bb_bold">(.*?)<\/span>/gi,"[b]$1[/b]");
			//IMG
            rep(/<img.*?src=\"(.*?)\".*?\/>/gi,"[img]$1[/img]");
			//STRONG
			rep(/<strong class=\"codeStyle\">(.*?)<\/strong>/gi,"[code][b]$1[/b][/code]");
			rep(/<strong class=\"quoteStyle\">(.*?)<\/strong>/gi,"[quote][b]$1[/b][/quote]");
   			rep(/<\/(strong|b)>/gi,"[/b]");
			rep(/<(strong|b)>/gi,"[b]");
			//EM
			rep(/<em class=\"codeStyle\">(.*?)<\/em>/gi,"[code][i]$1[/i][/code]");
			rep(/<em class=\"quoteStyle\">(.*?)<\/em>/gi,"[quote][i]$1[/i][/quote]");
   			rep(/<\/(em|i)>/gi,"[/i]");
			rep(/<(em|i)>/gi,"[i]");
			//U
            rep(/<u class=\"codeStyle\">(.*?)<\/u>/gi,"[code][u]$1[/u][/code]");
			rep(/<u class=\"quoteStyle\">(.*?)<\/u>/gi,"[quote][u]$1[/u][/quote]");
			//TABLE                                                             
			rep(/<table(.*?)>/gi,"[table$1]");
			rep(/<\/table>/gi,"[/table]");
			rep(/<thead(.*?)>/gi,"[thead$1]");
			rep(/<\/thead>/gi,"[/thead]");
			rep(/<tbody(.*?)>/gi,"[tbody$1]");
			rep(/<\/tbody>/gi,"[/tbody]");
			rep(/<tr>/gi,"[tr]");
			rep(/<\/tr>/gi,"[/tr]");
			rep(/<td>/gi,"[td]");
			rep(/<\/td>/gi,"[/td]");
			//LIST
			rep(/<\/ul>/gi,"[/list]");
			rep(/<ul>/gi,"[list]");
			rep(/<ul(.*?)>/gi,"[list]");
			rep(/<li(.*?)>/gi,"[item]");
			rep(/<\/ol>/gi,"[/numlist]");
			rep(/<ol>/gi,"[numlist]");
			rep(/<\/li>/gi,"[/item]");
			rep(/<li>/gi,"[item]");
			//U
			rep(/<\/u>/gi,"[/u]");
			rep(/<u>/gi,"[u]");
			//BLOCKQUOTE
			rep(/<blockquote[^>]*>/gi,"[quote]");
			rep(/<\/blockquote>/gi,"[/quote]");
			//BR
			rep(/<br \/>/gi,"\r\n");
			rep(/<br\/>/gi,"\r\n");
			rep(/<br>/gi,"");
			//P
			rep(/<p>/gi,"");
			rep(/<\/p>/gi,"\n");
			//SPECIALS CHARS
			rep(/&nbsp;/gi," ");
			rep(/&quot;/gi,"\"");
			rep(/&lt;/gi,"<");
			rep(/&gt;/gi,">");
			rep(/&amp;/gi,"&");
			return s; 
		},

		// BBCode -> HTML from PunBB dialect
		_punbb_bbcode2html : function(s) {
			s = tinymce.trim(s);

			function rep(re, str) {
				s = s.replace(re, str);
			};
			// example: [b] to <strong>
			rep(/\r/gi,"");
			rep(/\n/gi,"<br />");
			rep(/\[b\]/gi,"<strong>");
			rep(/\[\/b\]/gi,"</strong>");
			rep(/\[list(.*?)\]/gi,"<ul>");
			rep(/\[\/list\]/gi,"</ul>");
			rep(/\[numlist\]/gi,"<ol>");
			rep(/\[\/numlist\]/gi,"</ol>");
			rep(/\[item\]/gi,"<li>");
			rep(/\[\/item\]/gi,"</li>");
			rep(/\[i\]/gi,"<em>");
			rep(/\[\/i\]/gi,"</em>");
			rep(/\[u\]/gi,"<u>");
			rep(/\[\/u\]/gi,"</u>");
			rep(/\[url=([^\|^\]]+)[\|]{0,1}?([^\|^\]]*?)[\|]{0,1}?([^\]^\|]*?)[\|]{0,1}?([^\]^\|]*?)\](.*?)\[\/url\]/gi,"<a href=\"$1\" title=\"$2\" rel=\"$3\" class=\"$4\">$5</a>");
			rep(/\[img\](.*?)\[\/img\]/gi,"<img src=\"$1\" />");
			rep(/\[color=(.*?)\](.*?)\[\/color\]/gi,"<font color=\"$1\">$2</font>");
			rep(/\[code\](.*?)\[\/code\]/gi,"<span class=\"codeStyle\">$1</span>&nbsp;");
			rep(/\[quote.*?\](.*?)\[\/quote\]/gi,"<span class=\"quoteStyle\">$1</span>&nbsp;");
 			rep(/\[table(.*?)\]/gi,"<table$1>");
 			rep(/\[\/table\]/gi,"</table>");
 			rep(/\[thead(.*?)\]/gi,"<thead$1>");
 			rep(/\[\/thead\]/gi,"</thead>");
 			rep(/\[tbody(.*?)\]/gi,"<tbody$1>");
 			rep(/\[\/tbody\]/gi,"</tbody>");
 			rep(/\[tr\]/gi,"<tr>");
 			rep(/\[\/tr\]/gi,"</tr>");
 			rep(/\[td\]/gi,"<td>");
 			rep(/\[\/td\]/gi,"</td>");
			return s; 
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bbcode', tinymce.plugins.BBCodePlugin);
})();

(function(){var DOM=tinymce.DOM;tinymce.create('tinymce.plugins.FullScreenPlugin',{init:function(ed,url){var t=this,s={},vp;t.editor=ed;ed.addCommand('mceFullScreen',function(){var win,de=DOM.doc.documentElement;if(ed.getParam('fullscreen_is_enabled')){if(ed.getParam('fullscreen_new_window'))closeFullscreen();else{DOM.win.setTimeout(function(){tinymce.dom.Event.remove(DOM.win,'resize',t.resizeFunc);tinyMCE.get(ed.getParam('fullscreen_editor_id')).setContent(ed.getContent({format:'raw'}),{format:'raw'});tinyMCE.remove(ed);DOM.remove('mce_fullscreen_container');de.style.overflow=ed.getParam('fullscreen_html_overflow');DOM.setStyle(DOM.doc.body,'overflow',ed.getParam('fullscreen_overflow'));DOM.win.scrollTo(ed.getParam('fullscreen_scrollx'),ed.getParam('fullscreen_scrolly'));tinyMCE.settings=tinyMCE.oldSettings;},10);}return;}if(ed.getParam('fullscreen_new_window')){win=DOM.win.open(url+"/fullscreen.htm","mceFullScreenPopup","fullscreen=yes,menubar=no,toolbar=no,scrollbars=no,resizable=yes,left=0,top=0,width="+screen.availWidth+",height="+screen.availHeight);try{win.resizeTo(screen.availWidth,screen.availHeight);}catch(e){}}else{tinyMCE.oldSettings=tinyMCE.settings;s.fullscreen_overflow=DOM.getStyle(DOM.doc.body,'overflow',1)||'auto';s.fullscreen_html_overflow=DOM.getStyle(de,'overflow',1);vp=DOM.getViewPort();s.fullscreen_scrollx=vp.x;s.fullscreen_scrolly=vp.y;if(tinymce.isOpera&&s.fullscreen_overflow=='visible')s.fullscreen_overflow='auto';if(tinymce.isIE&&s.fullscreen_overflow=='scroll')s.fullscreen_overflow='auto';if(s.fullscreen_overflow=='0px')s.fullscreen_overflow='';DOM.setStyle(DOM.doc.body,'overflow','hidden');de.style.overflow='hidden';vp=DOM.getViewPort();DOM.win.scrollTo(0,0);if(tinymce.isIE)vp.h-=1;n=DOM.add(DOM.doc.body,'div',{id:'mce_fullscreen_container',style:'position:absolute;top:0;left:0;width:'+vp.w+'px;height:'+vp.h+'px;z-index:200000;'});DOM.add(n,'div',{id:'mce_fullscreen'});tinymce.each(ed.settings,function(v,n){s[n]=v;});s.id='mce_fullscreen';s.width=n.clientWidth;s.height=n.clientHeight-15;s.fullscreen_is_enabled=true;s.fullscreen_editor_id=ed.id;s.theme_advanced_resizing=false;s.save_onsavecallback=function(){ed.setContent(tinyMCE.get(s.id).getContent({format:'raw'}),{format:'raw'});ed.execCommand('mceSave');};tinymce.each(ed.getParam('fullscreen_settings'),function(v,k){s[k]=v;});if(s.theme_advanced_toolbar_location==='external')s.theme_advanced_toolbar_location='top';t.fullscreenEditor=new tinymce.Editor('mce_fullscreen',s);t.fullscreenEditor.onInit.add(function(){t.fullscreenEditor.setContent(ed.getContent());t.fullscreenEditor.focus();});t.fullscreenEditor.render();tinyMCE.add(t.fullscreenEditor);t.fullscreenElement=new tinymce.dom.Element('mce_fullscreen_container');t.fullscreenElement.update();t.resizeFunc=tinymce.dom.Event.add(DOM.win,'resize',function(){var vp=tinymce.DOM.getViewPort();t.fullscreenEditor.theme.resizeTo(vp.w,vp.h);});}});ed.addButton('fullscreen',{title:'fullscreen.desc',cmd:'mceFullScreen'});ed.onNodeChange.add(function(ed,cm){cm.setActive('fullscreen',ed.getParam('fullscreen_is_enabled'));});},getInfo:function(){return{longname:'Fullscreen',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/fullscreen',version:tinymce.majorVersion+"."+tinymce.minorVersion};}});tinymce.PluginManager.add('fullscreen',tinymce.plugins.FullScreenPlugin);})();

(function(){var each=tinymce.each;tinymce.create('tinymce.plugins.TablePlugin',{init:function(ed,url){var t=this;t.editor=ed;t.url=url;each([['table','table.desc','mceInsertTable',true],['delete_table','table.del','mceTableDelete'],['delete_col','table.delete_col_desc','mceTableDeleteCol'],['delete_row','table.delete_row_desc','mceTableDeleteRow'],['col_after','table.col_after_desc','mceTableInsertColAfter'],['col_before','table.col_before_desc','mceTableInsertColBefore'],['row_after','table.row_after_desc','mceTableInsertRowAfter'],['row_before','table.row_before_desc','mceTableInsertRowBefore'],['row_props','table.row_desc','mceTableRowProps',true],['cell_props','table.cell_desc','mceTableCellProps',true],['split_cells','table.split_cells_desc','mceTableSplitCells',true],['merge_cells','table.merge_cells_desc','mceTableMergeCells',true]],function(c){ed.addButton(c[0],{title:c[1],cmd:c[2],ui:c[3]});});ed.onInit.add(function(){if(ed&&ed.plugins.contextmenu){ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){var sm,se=ed.selection,el=se.getNode()||ed.getBody();if(ed.dom.getParent(e,'td')||ed.dom.getParent(e,'th')){m.removeAll();if(el.nodeName=='A'&&!ed.dom.getAttrib(el,'name')){m.add({title:'advanced.link_desc',icon:'link',cmd:ed.plugins.advlink?'mceAdvLink':'mceLink',ui:true});m.add({title:'advanced.unlink_desc',icon:'unlink',cmd:'UnLink'});m.addSeparator();}if(el.nodeName=='IMG'&&el.className.indexOf('mceItem')==-1){m.add({title:'advanced.image_desc',icon:'image',cmd:ed.plugins.advimage?'mceAdvImage':'mceImage',ui:true});m.addSeparator();}m.add({title:'table.desc',icon:'table',cmd:'mceInsertTable',ui:true,value:{action:'insert'}});m.add({title:'table.props_desc',icon:'table_props',cmd:'mceInsertTable',ui:true});m.add({title:'table.del',icon:'delete_table',cmd:'mceTableDelete',ui:true});m.addSeparator();sm=m.addMenu({title:'table.cell'});sm.add({title:'table.cell_desc',icon:'cell_props',cmd:'mceTableCellProps',ui:true});sm.add({title:'table.split_cells_desc',icon:'split_cells',cmd:'mceTableSplitCells',ui:true});sm.add({title:'table.merge_cells_desc',icon:'merge_cells',cmd:'mceTableMergeCells',ui:true});sm=m.addMenu({title:'table.row'});sm.add({title:'table.row_desc',icon:'row_props',cmd:'mceTableRowProps',ui:true});sm.add({title:'table.row_before_desc',icon:'row_before',cmd:'mceTableInsertRowBefore'});sm.add({title:'table.row_after_desc',icon:'row_after',cmd:'mceTableInsertRowAfter'});sm.add({title:'table.delete_row_desc',icon:'delete_row',cmd:'mceTableDeleteRow'});sm.addSeparator();sm.add({title:'table.cut_row_desc',icon:'cut',cmd:'mceTableCutRow'});sm.add({title:'table.copy_row_desc',icon:'copy',cmd:'mceTableCopyRow'});sm.add({title:'table.paste_row_before_desc',icon:'paste',cmd:'mceTablePasteRowBefore'});sm.add({title:'table.paste_row_after_desc',icon:'paste',cmd:'mceTablePasteRowAfter'});sm=m.addMenu({title:'table.col'});sm.add({title:'table.col_before_desc',icon:'col_before',cmd:'mceTableInsertColBefore'});sm.add({title:'table.col_after_desc',icon:'col_after',cmd:'mceTableInsertColAfter'});sm.add({title:'table.delete_col_desc',icon:'delete_col',cmd:'mceTableDeleteCol'});}else m.add({title:'table.desc',icon:'table',cmd:'mceInsertTable',ui:true});});}});ed.onKeyDown.add(function(ed,e){if(e.keyCode==9&&ed.dom.getParent(ed.selection.getNode(),'TABLE')){if(!tinymce.isGecko&&!tinymce.isOpera){tinyMCE.execInstanceCommand(ed.editorId,"mceTableMoveToNextRow",true);return tinymce.dom.Event.cancel(e);}ed.undoManager.add();}});if(!tinymce.isIE){if(ed.getParam('table_selection',true)){ed.onClick.add(function(ed,e){e=e.target;if(e.nodeName==='TABLE')ed.selection.select(e);});}}ed.onNodeChange.add(function(ed,cm,n){var p=ed.dom.getParent(n,'td,th,caption');cm.setActive('table',n.nodeName==='TABLE'||!!p);if(p&&p.nodeName==='CAPTION')p=null;cm.setDisabled('delete_table',!p);cm.setDisabled('delete_col',!p);cm.setDisabled('delete_table',!p);cm.setDisabled('delete_row',!p);cm.setDisabled('col_after',!p);cm.setDisabled('col_before',!p);cm.setDisabled('row_after',!p);cm.setDisabled('row_before',!p);cm.setDisabled('row_props',!p);cm.setDisabled('cell_props',!p);cm.setDisabled('split_cells',!p||(parseInt(ed.dom.getAttrib(p,'colspan','1'))<2&&parseInt(ed.dom.getAttrib(p,'rowspan','1'))<2));cm.setDisabled('merge_cells',!p);});if(!tinymce.isIE){ed.onBeforeSetContent.add(function(ed,o){if(o.initial)o.content=o.content.replace(/<(td|th)([^>]+|)>\s*<\/(td|th)>/g,tinymce.isOpera?'<$1$2>&nbsp;</$1>':'<$1$2><br mce_bogus="1" /></$1>');});}},execCommand:function(cmd,ui,val){var ed=this.editor,b;switch(cmd){case"mceTableMoveToNextRow":case"mceInsertTable":case"mceTableRowProps":case"mceTableCellProps":case"mceTableSplitCells":case"mceTableMergeCells":case"mceTableInsertRowBefore":case"mceTableInsertRowAfter":case"mceTableDeleteRow":case"mceTableInsertColBefore":case"mceTableInsertColAfter":case"mceTableDeleteCol":case"mceTableCutRow":case"mceTableCopyRow":case"mceTablePasteRowBefore":case"mceTablePasteRowAfter":case"mceTableDelete":ed.execCommand('mceBeginUndoLevel');this._doExecCommand(cmd,ui,val);ed.execCommand('mceEndUndoLevel');return true;}return false;},getInfo:function(){return{longname:'Tables',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/table',version:tinymce.majorVersion+"."+tinymce.minorVersion};},_doExecCommand:function(command,user_interface,value){var inst=this.editor,ed=inst,url=this.url;var focusElm=inst.selection.getNode();var trElm=inst.dom.getParent(focusElm,"tr");var tdElm=inst.dom.getParent(focusElm,"td,th");var tableElm=inst.dom.getParent(focusElm,"table");var doc=inst.contentWindow.document;var tableBorder=tableElm?tableElm.getAttribute("border"):"";if(trElm&&tdElm==null)tdElm=trElm.cells[0];function inArray(ar,v){for(var i=0;i<ar.length;i++){if(ar[i].length>0&&inArray(ar[i],v))return true;if(ar[i]==v)return true;}return false;}function select(dx,dy){var td;grid=getTableGrid(tableElm);dx=dx||0;dy=dy||0;dx=Math.max(cpos.cellindex+dx,0);dy=Math.max(cpos.rowindex+dy,0);inst.execCommand('mceRepaint');td=getCell(grid,dy,dx);if(td){inst.selection.select(td.firstChild||td);inst.selection.collapse(1);}};function makeTD(){var newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';}function getColRowSpan(td){var colspan=inst.dom.getAttrib(td,"colspan");var rowspan=inst.dom.getAttrib(td,"rowspan");colspan=colspan==""?1:parseInt(colspan);rowspan=rowspan==""?1:parseInt(rowspan);return{colspan:colspan,rowspan:rowspan};}function getCellPos(grid,td){var x,y;for(y=0;y<grid.length;y++){for(x=0;x<grid[y].length;x++){if(grid[y][x]==td)return{cellindex:x,rowindex:y};}}return null;}function getCell(grid,row,col){if(grid[row]&&grid[row][col])return grid[row][col];return null;}function getNextCell(table,cell){var cells=[],x=0,i,j,cell,nextCell;for(i=0;i<table.rows.length;i++)for(j=0;j<table.rows[i].cells.length;j++,x++)cells[x]=table.rows[i].cells[j];for(i=0;i<cells.length;i++)if(cells[i]==cell)if(nextCell=cells[i+1])return nextCell;}function getTableGrid(table){var grid=[],rows=table.rows,x,y,td,sd,xstart,x2,y2;for(y=0;y<rows.length;y++){for(x=0;x<rows[y].cells.length;x++){td=rows[y].cells[x];sd=getColRowSpan(td);for(xstart=x;grid[y]&&grid[y][xstart];xstart++);for(y2=y;y2<y+sd['rowspan'];y2++){if(!grid[y2])grid[y2]=[];for(x2=xstart;x2<xstart+sd['colspan'];x2++)grid[y2][x2]=td;}}}return grid;}function trimRow(table,tr,td,new_tr){var grid=getTableGrid(table),cpos=getCellPos(grid,td);var cells,lastElm;if(new_tr.cells.length!=tr.childNodes.length){cells=tr.childNodes;lastElm=null;for(var x=0;td=getCell(grid,cpos.rowindex,x);x++){var remove=true;var sd=getColRowSpan(td);if(inArray(cells,td)){new_tr.childNodes[x]._delete=true;}else if((lastElm==null||td!=lastElm)&&sd.colspan>1){for(var i=x;i<x+td.colSpan;i++)new_tr.childNodes[i]._delete=true;}if((lastElm==null||td!=lastElm)&&sd.rowspan>1)td.rowSpan=sd.rowspan+1;lastElm=td;}deleteMarked(tableElm);}}function prevElm(node,name){while((node=node.previousSibling)!=null){if(node.nodeName==name)return node;}return null;}function nextElm(node,names){var namesAr=names.split(',');while((node=node.nextSibling)!=null){for(var i=0;i<namesAr.length;i++){if(node.nodeName.toLowerCase()==namesAr[i].toLowerCase())return node;}}return null;}function deleteMarked(tbl){if(tbl.rows==0)return;var tr=tbl.rows[0];do{var next=nextElm(tr,"TR");if(tr._delete){tr.parentNode.removeChild(tr);continue;}var td=tr.cells[0];if(td.cells>1){do{var nexttd=nextElm(td,"TD,TH");if(td._delete)td.parentNode.removeChild(td);}while((td=nexttd)!=null);}}while((tr=next)!=null);}function addRows(td_elm,tr_elm,rowspan){td_elm.rowSpan=1;var trNext=nextElm(tr_elm,"TR");for(var i=1;i<rowspan&&trNext;i++){var newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';if(tinymce.isIE)trNext.insertBefore(newTD,trNext.cells(td_elm.cellIndex));else trNext.insertBefore(newTD,trNext.cells[td_elm.cellIndex]);trNext=nextElm(trNext,"TR");}}function copyRow(doc,table,tr){var grid=getTableGrid(table);var newTR=tr.cloneNode(false);var cpos=getCellPos(grid,tr.cells[0]);var lastCell=null;var tableBorder=inst.dom.getAttrib(table,"border");var tdElm=null;for(var x=0;tdElm=getCell(grid,cpos.rowindex,x);x++){var newTD=null;if(lastCell!=tdElm){for(var i=0;i<tr.cells.length;i++){if(tdElm==tr.cells[i]){newTD=tdElm.cloneNode(true);break;}}}if(newTD==null){newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';}newTD.colSpan=1;newTD.rowSpan=1;newTR.appendChild(newTD);lastCell=tdElm;}return newTR;}switch(command){case"mceTableMoveToNextRow":var nextCell=getNextCell(tableElm,tdElm);if(!nextCell){inst.execCommand("mceTableInsertRowAfter",tdElm);nextCell=getNextCell(tableElm,tdElm);}inst.selection.select(nextCell);inst.selection.collapse(true);return true;case"mceTableRowProps":if(trElm==null)return true;if(user_interface){inst.windowManager.open({url:url+'/row.htm',width:400+parseInt(inst.getLang('table.rowprops_delta_width',0)),height:295+parseInt(inst.getLang('table.rowprops_delta_height',0)),inline:1},{plugin_url:url});}return true;case"mceTableCellProps":if(tdElm==null)return true;if(user_interface){inst.windowManager.open({url:url+'/cell.htm',width:400+parseInt(inst.getLang('table.cellprops_delta_width',0)),height:295+parseInt(inst.getLang('table.cellprops_delta_height',0)),inline:1},{plugin_url:url});}return true;case"mceInsertTable":if(user_interface){inst.windowManager.open({url:url+'/table.htm',width:400+parseInt(inst.getLang('table.table_delta_width',0)),height:320+parseInt(inst.getLang('table.table_delta_height',0)),inline:1},{plugin_url:url,action:value?value.action:0});}return true;case"mceTableDelete":var table=inst.dom.getParent(inst.selection.getNode(),"table");if(table){table.parentNode.removeChild(table);inst.execCommand('mceRepaint');}return true;case"mceTableSplitCells":case"mceTableMergeCells":case"mceTableInsertRowBefore":case"mceTableInsertRowAfter":case"mceTableDeleteRow":case"mceTableInsertColBefore":case"mceTableInsertColAfter":case"mceTableDeleteCol":case"mceTableCutRow":case"mceTableCopyRow":case"mceTablePasteRowBefore":case"mceTablePasteRowAfter":if(!tableElm)return true;if(trElm&&tableElm!=trElm.parentNode)tableElm=trElm.parentNode;if(tableElm&&trElm){switch(command){case"mceTableCutRow":if(!trElm||!tdElm)return true;inst.tableRowClipboard=copyRow(doc,tableElm,trElm);inst.execCommand("mceTableDeleteRow");break;case"mceTableCopyRow":if(!trElm||!tdElm)return true;inst.tableRowClipboard=copyRow(doc,tableElm,trElm);break;case"mceTablePasteRowBefore":if(!trElm||!tdElm)return true;var newTR=inst.tableRowClipboard.cloneNode(true);var prevTR=prevElm(trElm,"TR");if(prevTR!=null)trimRow(tableElm,prevTR,prevTR.cells[0],newTR);trElm.parentNode.insertBefore(newTR,trElm);break;case"mceTablePasteRowAfter":if(!trElm||!tdElm)return true;var nextTR=nextElm(trElm,"TR");var newTR=inst.tableRowClipboard.cloneNode(true);trimRow(tableElm,trElm,tdElm,newTR);if(nextTR==null)trElm.parentNode.appendChild(newTR);else nextTR.parentNode.insertBefore(newTR,nextTR);break;case"mceTableInsertRowBefore":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);var newTR=doc.createElement("tr");var lastTDElm=null;cpos.rowindex--;if(cpos.rowindex<0)cpos.rowindex=0;for(var x=0;tdElm=getCell(grid,cpos.rowindex,x);x++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd['rowspan']==1){var newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';newTD.colSpan=tdElm.colSpan;newTR.appendChild(newTD);}else tdElm.rowSpan=sd['rowspan']+1;lastTDElm=tdElm;}}trElm.parentNode.insertBefore(newTR,trElm);select(0,1);break;case"mceTableInsertRowAfter":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);var newTR=doc.createElement("tr");var lastTDElm=null;for(var x=0;tdElm=getCell(grid,cpos.rowindex,x);x++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd['rowspan']==1){var newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';newTD.colSpan=tdElm.colSpan;newTR.appendChild(newTD);}else tdElm.rowSpan=sd['rowspan']+1;lastTDElm=tdElm;}}if(newTR.hasChildNodes()){var nextTR=nextElm(trElm,"TR");if(nextTR)nextTR.parentNode.insertBefore(newTR,nextTR);else tableElm.appendChild(newTR);}select(0,1);break;case"mceTableDeleteRow":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);if(grid.length==1){inst.dom.remove(inst.dom.getParent(tableElm,"table"));return true;}var cells=trElm.cells;var nextTR=nextElm(trElm,"TR");for(var x=0;x<cells.length;x++){if(cells[x].rowSpan>1){var newTD=cells[x].cloneNode(true);var sd=getColRowSpan(cells[x]);newTD.rowSpan=sd.rowspan-1;var nextTD=nextTR.cells[x];if(nextTD==null)nextTR.appendChild(newTD);else nextTR.insertBefore(newTD,nextTD);}}var lastTDElm=null;for(var x=0;tdElm=getCell(grid,cpos.rowindex,x);x++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd.rowspan>1){tdElm.rowSpan=sd.rowspan-1;}else{trElm=tdElm.parentNode;if(trElm.parentNode)trElm._delete=true;}lastTDElm=tdElm;}}deleteMarked(tableElm);select(0,-1);break;case"mceTableInsertColBefore":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);var lastTDElm=null;for(var y=0;tdElm=getCell(grid,y,cpos.cellindex);y++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd['colspan']==1){var newTD=doc.createElement(tdElm.nodeName);if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';newTD.rowSpan=tdElm.rowSpan;tdElm.parentNode.insertBefore(newTD,tdElm);}else tdElm.colSpan++;lastTDElm=tdElm;}}select();break;case"mceTableInsertColAfter":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);var lastTDElm=null;for(var y=0;tdElm=getCell(grid,y,cpos.cellindex);y++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd['colspan']==1){var newTD=doc.createElement(tdElm.nodeName);if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';newTD.rowSpan=tdElm.rowSpan;var nextTD=nextElm(tdElm,"TD,TH");if(nextTD==null)tdElm.parentNode.appendChild(newTD);else nextTD.parentNode.insertBefore(newTD,nextTD);}else tdElm.colSpan++;lastTDElm=tdElm;}}select(1);break;case"mceTableDeleteCol":if(!trElm||!tdElm)return true;var grid=getTableGrid(tableElm);var cpos=getCellPos(grid,tdElm);var lastTDElm=null;if(grid.length>1&&grid[0].length<=1){inst.dom.remove(inst.dom.getParent(tableElm,"table"));return true;}for(var y=0;tdElm=getCell(grid,y,cpos.cellindex);y++){if(tdElm!=lastTDElm){var sd=getColRowSpan(tdElm);if(sd['colspan']>1)tdElm.colSpan=sd['colspan']-1;else{if(tdElm.parentNode)tdElm.parentNode.removeChild(tdElm);}lastTDElm=tdElm;}}select(-1);break;case"mceTableSplitCells":if(!trElm||!tdElm)return true;var spandata=getColRowSpan(tdElm);var colspan=spandata["colspan"];var rowspan=spandata["rowspan"];if(colspan>1||rowspan>1){tdElm.colSpan=1;for(var i=1;i<colspan;i++){var newTD=doc.createElement("td");if(!tinymce.isIE)newTD.innerHTML='<br mce_bogus="1"/>';trElm.insertBefore(newTD,nextElm(tdElm,"TD,TH"));if(rowspan>1)addRows(newTD,trElm,rowspan);}addRows(tdElm,trElm,rowspan);}tableElm=inst.dom.getParent(inst.selection.getNode(),"table");break;case"mceTableMergeCells":var rows=[];var sel=inst.selection.getSel();var grid=getTableGrid(tableElm);if(tinymce.isIE||sel.rangeCount==1){if(user_interface){var sp=getColRowSpan(tdElm);inst.windowManager.open({url:url+'/merge_cells.htm',width:240+parseInt(inst.getLang('table.merge_cells_delta_width',0)),height:110+parseInt(inst.getLang('table.merge_cells_delta_height',0)),inline:1},{action:"update",numcols:sp.colspan,numrows:sp.rowspan,plugin_url:url});return true;}else{var numRows=parseInt(value['numrows']);var numCols=parseInt(value['numcols']);var cpos=getCellPos(grid,tdElm);if((""+numRows)=="NaN")numRows=1;if((""+numCols)=="NaN")numCols=1;var tRows=tableElm.rows;for(var y=cpos.rowindex;y<grid.length;y++){var rowCells=[];for(var x=cpos.cellindex;x<grid[y].length;x++){var td=getCell(grid,y,x);if(td&&!inArray(rows,td)&&!inArray(rowCells,td)){var cp=getCellPos(grid,td);if(cp.cellindex<cpos.cellindex+numCols&&cp.rowindex<cpos.rowindex+numRows)rowCells[rowCells.length]=td;}}if(rowCells.length>0)rows[rows.length]=rowCells;var td=getCell(grid,cpos.rowindex,cpos.cellindex);each(ed.dom.select('br',td),function(e,i){if(i>0&&ed.dom.getAttrib('mce_bogus'))ed.dom.remove(e);});}}}else{var cells=[];var sel=inst.selection.getSel();var lastTR=null;var curRow=null;var x1=-1,y1=-1,x2,y2;if(sel.rangeCount<2)return true;for(var i=0;i<sel.rangeCount;i++){var rng=sel.getRangeAt(i);var tdElm=rng.startContainer.childNodes[rng.startOffset];if(!tdElm)break;if(tdElm.nodeName=="TD"||tdElm.nodeName=="TH")cells[cells.length]=tdElm;}var tRows=tableElm.rows;for(var y=0;y<tRows.length;y++){var rowCells=[];for(var x=0;x<tRows[y].cells.length;x++){var td=tRows[y].cells[x];for(var i=0;i<cells.length;i++){if(td==cells[i]){rowCells[rowCells.length]=td;}}}if(rowCells.length>0)rows[rows.length]=rowCells;}var curRow=[];var lastTR=null;for(var y=0;y<grid.length;y++){for(var x=0;x<grid[y].length;x++){grid[y][x]._selected=false;for(var i=0;i<cells.length;i++){if(grid[y][x]==cells[i]){if(x1==-1){x1=x;y1=y;}x2=x;y2=y;grid[y][x]._selected=true;}}}}for(var y=y1;y<=y2;y++){for(var x=x1;x<=x2;x++){if(!grid[y][x]._selected){alert("Invalid selection for merge.");return true;}}}}var rowSpan=1,colSpan=1;var lastRowSpan=-1;for(var y=0;y<rows.length;y++){var rowColSpan=0;for(var x=0;x<rows[y].length;x++){var sd=getColRowSpan(rows[y][x]);rowColSpan+=sd['colspan'];if(lastRowSpan!=-1&&sd['rowspan']!=lastRowSpan){alert("Invalid selection for merge.");return true;}lastRowSpan=sd['rowspan'];}if(rowColSpan>colSpan)colSpan=rowColSpan;lastRowSpan=-1;}var lastColSpan=-1;for(var x=0;x<rows[0].length;x++){var colRowSpan=0;for(var y=0;y<rows.length;y++){var sd=getColRowSpan(rows[y][x]);colRowSpan+=sd['rowspan'];if(lastColSpan!=-1&&sd['colspan']!=lastColSpan){alert("Invalid selection for merge.");return true;}lastColSpan=sd['colspan'];}if(colRowSpan>rowSpan)rowSpan=colRowSpan;lastColSpan=-1;}tdElm=rows[0][0];tdElm.rowSpan=rowSpan;tdElm.colSpan=colSpan;for(var y=0;y<rows.length;y++){for(var x=0;x<rows[y].length;x++){var html=rows[y][x].innerHTML;var chk=html.replace(/[ \t\r\n]/g,"");if(chk!="<br/>"&&chk!="<br>"&&chk!='<br mce_bogus="1"/>'&&(x+y>0))tdElm.innerHTML+=html;if(rows[y][x]!=tdElm&&!rows[y][x]._deleted){var cpos=getCellPos(grid,rows[y][x]);var tr=rows[y][x].parentNode;tr.removeChild(rows[y][x]);rows[y][x]._deleted=true;if(!tr.hasChildNodes()){tr.parentNode.removeChild(tr);var lastCell=null;for(var x=0;cellElm=getCell(grid,cpos.rowindex,x);x++){if(cellElm!=lastCell&&cellElm.rowSpan>1)cellElm.rowSpan--;lastCell=cellElm;}if(tdElm.rowSpan>1)tdElm.rowSpan--;}}}}each(ed.dom.select('br',tdElm),function(e,i){if(i>0&&ed.dom.getAttrib(e,'mce_bogus'))ed.dom.remove(e);});break;}tableElm=inst.dom.getParent(inst.selection.getNode(),"table");inst.addVisual(tableElm);inst.nodeChanged();}return true;}return false;}});tinymce.PluginManager.add('table',tinymce.plugins.TablePlugin);})();

(function(){var DOM=tinymce.DOM,Element=tinymce.dom.Element,Event=tinymce.dom.Event,each=tinymce.each,is=tinymce.is;tinymce.create('tinymce.plugins.InlinePopups',{init:function(ed,url){ed.onBeforeRenderUI.add(function(){ed.windowManager=new tinymce.InlineWindowManager(ed);DOM.loadCSS(url+'/skins/'+(ed.settings.inlinepopups_skin||'clearlooks2')+"/window.css");});},getInfo:function(){return{longname:'InlinePopups',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/inlinepopups',version:tinymce.majorVersion+"."+tinymce.minorVersion};}});tinymce.create('tinymce.InlineWindowManager:tinymce.WindowManager',{InlineWindowManager:function(ed){var t=this;t.parent(ed);t.zIndex=300000;t.count=0;},open:function(f,p){var t=this,id,opt='',ed=t.editor,dw=0,dh=0,vp,po,mdf,clf,we,w,u;f=f||{};p=p||{};if(!f.inline)return t.parent(f,p);if(!f.type)t.bookmark=ed.selection.getBookmark('simple');id=DOM.uniqueId();vp=DOM.getViewPort();f.width=parseInt(f.width||320);f.height=parseInt(f.height||240)+(tinymce.isIE?8:0);f.min_width=parseInt(f.min_width||150);f.min_height=parseInt(f.min_height||100);f.max_width=parseInt(f.max_width||2000);f.max_height=parseInt(f.max_height||2000);f.left=f.left||Math.round(Math.max(vp.x,vp.x+(vp.w/ 2.0) - (f.width /2.0)));f.top=f.top||Math.round(Math.max(vp.y,vp.y+(vp.h/ 2.0) - (f.height /2.0)));f.movable=f.resizable=true;p.mce_width=f.width;p.mce_height=f.height;p.mce_inline=true;p.mce_window_id=id;p.mce_auto_focus=f.auto_focus;t.features=f;t.params=p;t.onOpen.dispatch(t,f,p);if(f.type){opt+=' mceModal';if(f.type)opt+=' mce'+f.type.substring(0,1).toUpperCase()+f.type.substring(1);f.resizable=false;}if(f.statusbar)opt+=' mceStatusbar';if(f.resizable)opt+=' mceResizable';if(f.minimizable)opt+=' mceMinimizable';if(f.maximizable)opt+=' mceMaximizable';if(f.movable)opt+=' mceMovable';t._addAll(DOM.doc.body,['div',{id:id,'class':ed.settings.inlinepopups_skin||'clearlooks2',style:'width:100px;height:100px'},['div',{id:id+'_wrapper','class':'mceWrapper'+opt},['div',{id:id+'_top','class':'mceTop'},['div',{'class':'mceLeft'}],['div',{'class':'mceCenter'}],['div',{'class':'mceRight'}],['span',{id:id+'_title'},f.title||'']],['div',{id:id+'_middle','class':'mceMiddle'},['div',{id:id+'_left','class':'mceLeft'}],['span',{id:id+'_content'}],['div',{id:id+'_right','class':'mceRight'}]],['div',{id:id+'_bottom','class':'mceBottom'},['div',{'class':'mceLeft'}],['div',{'class':'mceCenter'}],['div',{'class':'mceRight'}],['span',{id:id+'_status'},'Content']],['a',{'class':'mceMove',tabindex:'-1',href:'javascript:;'}],['a',{'class':'mceMin',tabindex:'-1',href:'javascript:;',onmousedown:'return false;'}],['a',{'class':'mceMax',tabindex:'-1',href:'javascript:;',onmousedown:'return false;'}],['a',{'class':'mceMed',tabindex:'-1',href:'javascript:;',onmousedown:'return false;'}],['a',{'class':'mceClose',tabindex:'-1',href:'javascript:;',onmousedown:'return false;'}],['a',{id:id+'_resize_n','class':'mceResize mceResizeN',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_s','class':'mceResize mceResizeS',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_w','class':'mceResize mceResizeW',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_e','class':'mceResize mceResizeE',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_nw','class':'mceResize mceResizeNW',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_ne','class':'mceResize mceResizeNE',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_sw','class':'mceResize mceResizeSW',tabindex:'-1',href:'javascript:;'}],['a',{id:id+'_resize_se','class':'mceResize mceResizeSE',tabindex:'-1',href:'javascript:;'}]]]);DOM.setStyles(id,{top:-10000,left:-10000});if(tinymce.isGecko)DOM.setStyle(id,'overflow','auto');if(!f.type){dw+=DOM.get(id+'_left').clientWidth;dw+=DOM.get(id+'_right').clientWidth;dh+=DOM.get(id+'_top').clientHeight;dh+=DOM.get(id+'_bottom').clientHeight;}DOM.setStyles(id,{top:f.top,left:f.left,width:f.width+dw,height:f.height+dh});u=f.url||f.file;if(u){if(tinymce.relaxedDomain)u+=(u.indexOf('?')==-1?'?':'&')+'mce_rdomain='+tinymce.relaxedDomain;u=tinymce._addVer(u);}if(!f.type){DOM.add(id+'_content','iframe',{id:id+'_ifr',src:'javascript:""',frameBorder:0,style:'border:0;width:10px;height:10px'});DOM.setStyles(id+'_ifr',{width:f.width,height:f.height});DOM.setAttrib(id+'_ifr','src',u);}else{DOM.add(id+'_wrapper','a',{id:id+'_ok','class':'mceButton mceOk',href:'javascript:;',onmousedown:'return false;'},'Ok');if(f.type=='confirm')DOM.add(id+'_wrapper','a',{'class':'mceButton mceCancel',href:'javascript:;',onmousedown:'return false;'},'Cancel');DOM.add(id+'_middle','div',{'class':'mceIcon'});DOM.setHTML(id+'_content',f.content.replace('\n','<br />'));}mdf=Event.add(id,'mousedown',function(e){var n=e.target,w,vp;w=t.windows[id];t.focus(id);if(n.nodeName=='A'||n.nodeName=='a'){if(n.className=='mceMax'){w.oldPos=w.element.getXY();w.oldSize=w.element.getSize();vp=DOM.getViewPort();vp.w-=2;vp.h-=2;w.element.moveTo(vp.x,vp.y);w.element.resizeTo(vp.w,vp.h);DOM.setStyles(id+'_ifr',{width:vp.w-w.deltaWidth,height:vp.h-w.deltaHeight});DOM.addClass(id+'_wrapper','mceMaximized');}else if(n.className=='mceMed'){w.element.moveTo(w.oldPos.x,w.oldPos.y);w.element.resizeTo(w.oldSize.w,w.oldSize.h);w.iframeElement.resizeTo(w.oldSize.w-w.deltaWidth,w.oldSize.h-w.deltaHeight);DOM.removeClass(id+'_wrapper','mceMaximized');}else if(n.className=='mceMove')return t._startDrag(id,e,n.className);else if(DOM.hasClass(n,'mceResize'))return t._startDrag(id,e,n.className.substring(13));}});clf=Event.add(id,'click',function(e){var n=e.target;t.focus(id);if(n.nodeName=='A'||n.nodeName=='a'){switch(n.className){case'mceClose':t.close(null,id);return Event.cancel(e);case'mceButton mceOk':case'mceButton mceCancel':f.button_func(n.className=='mceButton mceOk');return Event.cancel(e);}}});t.windows=t.windows||{};w=t.windows[id]={id:id,mousedown_func:mdf,click_func:clf,element:new Element(id,{blocker:1,container:ed.getContainer()}),iframeElement:new Element(id+'_ifr'),features:f,deltaWidth:dw,deltaHeight:dh};w.iframeElement.on('focus',function(){t.focus(id);});if(t.count==0&&t.editor.getParam('dialog_type')=='modal'){DOM.add(DOM.doc.body,'div',{id:'mceModalBlocker','class':(t.editor.settings.inlinepopups_skin||'clearlooks2')+'_modalBlocker',style:{left:vp.x,top:vp.y,zIndex:t.zIndex-1}});DOM.show('mceModalBlocker');}else DOM.setStyle('mceModalBlocker','z-index',t.zIndex-1);t.focus(id);t._fixIELayout(id,1);if(DOM.get(id+'_ok'))DOM.get(id+'_ok').focus();t.count++;return w;},focus:function(id){var t=this,w=t.windows[id];w.zIndex=this.zIndex++;w.element.setStyle('zIndex',w.zIndex);w.element.update();id=id+'_wrapper';DOM.removeClass(t.lastId,'mceFocus');DOM.addClass(id,'mceFocus');t.lastId=id;},_addAll:function(te,ne){var i,n,t=this,dom=tinymce.DOM;if(is(ne,'string'))te.appendChild(dom.doc.createTextNode(ne));else if(ne.length){te=te.appendChild(dom.create(ne[0],ne[1]));for(i=2;i<ne.length;i++)t._addAll(te,ne[i]);}},_startDrag:function(id,se,ac){var t=this,mu,mm,d=DOM.doc,eb,w=t.windows[id],we=w.element,sp=we.getXY(),p,sz,ph,cp,vp,sx,sy,sex,sey,dx,dy,dw,dh;cp={x:0,y:0};vp=DOM.getViewPort();vp.w-=2;vp.h-=2;sex=se.screenX;sey=se.screenY;dx=dy=dw=dh=0;mu=Event.add(d,'mouseup',function(e){Event.remove(d,'mouseup',mu);Event.remove(d,'mousemove',mm);if(eb)eb.remove();we.moveBy(dx,dy);we.resizeBy(dw,dh);sz=we.getSize();DOM.setStyles(id+'_ifr',{width:sz.w-w.deltaWidth,height:sz.h-w.deltaHeight});t._fixIELayout(id,1);return Event.cancel(e);});if(ac!='Move')startMove();function startMove(){if(eb)return;t._fixIELayout(id,0);DOM.add(d.body,'div',{id:'mceEventBlocker','class':'mceEventBlocker '+(t.editor.settings.inlinepopups_skin||'clearlooks2'),style:{left:vp.x,top:vp.y,zIndex:t.zIndex+1}});eb=new Element('mceEventBlocker');eb.update();p=we.getXY();sz=we.getSize();sx=cp.x+p.x-vp.x;sy=cp.y+p.y-vp.y;DOM.add(eb.get(),'div',{id:'mcePlaceHolder','class':'mcePlaceHolder',style:{left:sx,top:sy,width:sz.w,height:sz.h}});ph=new Element('mcePlaceHolder');};mm=Event.add(d,'mousemove',function(e){var x,y,v;startMove();x=e.screenX-sex;y=e.screenY-sey;switch(ac){case'ResizeW':dx=x;dw=0-x;break;case'ResizeE':dw=x;break;case'ResizeN':case'ResizeNW':case'ResizeNE':if(ac=="ResizeNW"){dx=x;dw=0-x;}else if(ac=="ResizeNE")dw=x;dy=y;dh=0-y;break;case'ResizeS':case'ResizeSW':case'ResizeSE':if(ac=="ResizeSW"){dx=x;dw=0-x;}else if(ac=="ResizeSE")dw=x;dh=y;break;case'mceMove':dx=x;dy=y;break;}if(dw<(v=w.features.min_width-sz.w)){if(dx!==0)dx+=dw-v;dw=v;}if(dh<(v=w.features.min_height-sz.h)){if(dy!==0)dy+=dh-v;dh=v;}dw=Math.min(dw,w.features.max_width-sz.w);dh=Math.min(dh,w.features.max_height-sz.h);dx=Math.max(dx,vp.x-(sx+vp.x));dy=Math.max(dy,vp.y-(sy+vp.y));dx=Math.min(dx,(vp.w+vp.x)-(sx+sz.w+vp.x));dy=Math.min(dy,(vp.h+vp.y)-(sy+sz.h+vp.y));if(dx+dy!==0){if(sx+dx<0)dx=0;if(sy+dy<0)dy=0;ph.moveTo(sx+dx,sy+dy);}if(dw+dh!==0)ph.resizeTo(sz.w+dw,sz.h+dh);return Event.cancel(e);});return Event.cancel(se);},resizeBy:function(dw,dh,id){var w=this.windows[id];if(w){w.element.resizeBy(dw,dh);w.iframeElement.resizeBy(dw,dh);}},close:function(win,id){var t=this,w,d=DOM.doc,ix=0,fw,id;id=t._findId(id||win);t.count--;if(t.count==0)DOM.remove('mceModalBlocker');if(!id&&win){t.parent(win);return;}if(w=t.windows[id]){t.onClose.dispatch(t);Event.remove(d,'mousedown',w.mousedownFunc);Event.remove(d,'click',w.clickFunc);Event.clear(id);Event.clear(id+'_ifr');DOM.setAttrib(id+'_ifr','src','javascript:""');w.element.remove();delete t.windows[id];each(t.windows,function(w){if(w.zIndex>ix){fw=w;ix=w.zIndex;}});if(fw)t.focus(fw.id);}},setTitle:function(w,ti){var e;w=this._findId(w);if(e=DOM.get(w+'_title'))e.innerHTML=DOM.encode(ti);},alert:function(txt,cb,s){var t=this,w;w=t.open({title:t,type:'alert',button_func:function(s){if(cb)cb.call(s||t,s);t.close(null,w.id);},content:DOM.encode(t.editor.getLang(txt,txt)),inline:1,width:400,height:130});},confirm:function(txt,cb,s){var t=this,w;w=t.open({title:t,type:'confirm',button_func:function(s){if(cb)cb.call(s||t,s);t.close(null,w.id);},content:DOM.encode(t.editor.getLang(txt,txt)),inline:1,width:400,height:130});},_findId:function(w){var t=this;if(typeof(w)=='string')return w;each(t.windows,function(wo){var ifr=DOM.get(wo.id+'_ifr');if(ifr&&w==ifr.contentWindow){w=wo.id;return false;}});return w;},_fixIELayout:function(id,s){var w,img;if(!tinymce.isIE6)return;each(['n','s','w','e','nw','ne','sw','se'],function(v){var e=DOM.get(id+'_resize_'+v);DOM.setStyles(e,{width:s?e.clientWidth:'',height:s?e.clientHeight:'',cursor:DOM.getStyle(e,'cursor',1)});DOM.setStyle(id+"_bottom",'bottom','-1px');e=0;});if(w=this.windows[id]){w.element.hide();w.element.show();each(DOM.select('div,a',id),function(e,i){if(e.currentStyle.backgroundImage!='none'){img=new Image();img.src=e.currentStyle.backgroundImage.replace(/url\(\"(.+)\"\)/,'$1');}});DOM.get(id).style.filter='';}}});tinymce.PluginManager.add('inlinepopups',tinymce.plugins.InlinePopups);})();

(function(){var Event=tinymce.dom.Event;tinymce.create('tinymce.plugins.PastePlugin',{init:function(ed,url){var t=this;t.editor=ed;ed.addCommand('mcePasteText',function(ui,v){if(ui){if((ed.getParam('paste_use_dialog',true))||(!tinymce.isIE)){ed.windowManager.open({file:url+'/pastetext.htm',width:450,height:400,inline:1},{plugin_url:url});}else t._insertText(clipboardData.getData("Text"),true);}else t._insertText(v.html,v.linebreaks);});ed.addCommand('mcePasteWord',function(ui,v){if(ui){if((ed.getParam('paste_use_dialog',true))||(!tinymce.isIE)){ed.windowManager.open({file:url+'/pasteword.htm',width:450,height:400,inline:1},{plugin_url:url});}else t._insertText(t._clipboardHTML());}else t._insertWordContent(v);});ed.addCommand('mceSelectAll',function(){ed.execCommand('selectall');});ed.addButton('pastetext',{title:'paste.paste_text_desc',cmd:'mcePasteText',ui:true});ed.addButton('pasteword',{title:'paste.paste_word_desc',cmd:'mcePasteWord',ui:true});ed.addButton('selectall',{title:'paste.selectall_desc',cmd:'mceSelectAll'});if(ed.getParam("paste_auto_cleanup_on_paste",false)){ed.onPaste.add(function(ed,e){return t._handlePasteEvent(e)});}if(!tinymce.isIE&&ed.getParam("paste_auto_cleanup_on_paste",false)){ed.onKeyDown.add(function(ed,e){if(e.ctrlKey&&e.keyCode==86){window.setTimeout(function(){ed.execCommand("mcePasteText",true);},1);Event.cancel(e);}});}},getInfo:function(){return{longname:'Paste text/word',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/paste',version:tinymce.majorVersion+"."+tinymce.minorVersion};},_handlePasteEvent:function(e){var html=this._clipboardHTML(),ed=this.editor,sel=ed.selection,r;if(ed&&(r=sel.getRng())&&r.text.length>0)ed.execCommand('delete');if(html&&html.length>0)ed.execCommand('mcePasteWord',false,html);return Event.cancel(e);},_insertText:function(content,bLinebreaks){if(content&&content.length>0){if(bLinebreaks){if(this.editor.getParam("paste_create_paragraphs",true)){var rl=this.editor.getParam("paste_replace_list",'\u2122,<sup>TM</sup>,\u2026,...,\u201c|\u201d,",\u2019,\',\u2013|\u2014|\u2015|\u2212,-').split(',');for(var i=0;i<rl.length;i+=2)content=content.replace(new RegExp(rl[i],'gi'),rl[i+1]);content=content.replace(/\r\n\r\n/g,'</p><p>');content=content.replace(/\r\r/g,'</p><p>');content=content.replace(/\n\n/g,'</p><p>');if((pos=content.indexOf('</p><p>'))!=-1){this.editor.execCommand("Delete");var node=this.editor.selection.getNode();var breakElms=[];do{if(node.nodeType==1){if(node.nodeName=="TD"||node.nodeName=="BODY")break;breakElms[breakElms.length]=node;}}while(node=node.parentNode);var before="",after="</p>";before+=content.substring(0,pos);for(var i=0;i<breakElms.length;i++){before+="</"+breakElms[i].nodeName+">";after+="<"+breakElms[(breakElms.length-1)-i].nodeName+">";}before+="<p>";content=before+content.substring(pos+7)+after;}}if(this.editor.getParam("paste_create_linebreaks",true)){content=content.replace(/\r\n/g,'<br />');content=content.replace(/\r/g,'<br />');content=content.replace(/\n/g,'<br />');}}this.editor.execCommand("mceInsertRawHTML",false,content);}},_insertWordContent:function(content){var t=this,ed=t.editor;if(content&&content.length>0){var bull=String.fromCharCode(8226);var middot=String.fromCharCode(183);if(ed.getParam('paste_insert_word_content_callback'))content=ed.execCallback('paste_insert_word_content_callback','before',content);var rl=ed.getParam("paste_replace_list",'\u2122,<sup>TM</sup>,\u2026,...,\u201c|\u201d,",\u2019,\',\u2013|\u2014|\u2015|\u2212,-').split(',');for(var i=0;i<rl.length;i+=2)content=content.replace(new RegExp(rl[i],'gi'),rl[i+1]);if(this.editor.getParam("paste_convert_headers_to_strong",false)){content=content.replace(new RegExp('<p class=MsoHeading.*?>(.*?)<\/p>','gi'),'<p><b>$1</b></p>');}content=content.replace(new RegExp('tab-stops: list [0-9]+.0pt">','gi'),'">'+"--list--");content=content.replace(new RegExp(bull+"(.*?)<BR>","gi"),"<p>"+middot+"$1</p>");content=content.replace(new RegExp('<SPAN style="mso-list: Ignore">','gi'),"<span>"+bull);content=content.replace(/<o:p><\/o:p>/gi,"");content=content.replace(new RegExp('<br style="page-break-before: always;.*>','gi'),'-- page break --');content=content.replace(new RegExp('<(!--)([^>]*)(--)>','g'),"");if(this.editor.getParam("paste_remove_spans",true))content=content.replace(/<\/?span[^>]*>/gi,"");if(this.editor.getParam("paste_remove_styles",true))content=content.replace(new RegExp('<(\\w[^>]*) style="([^"]*)"([^>]*)','gi'),"<$1$3");content=content.replace(/<\/?font[^>]*>/gi,"");switch(this.editor.getParam("paste_strip_class_attributes","all")){case"all":content=content.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi,"<$1$3");break;case"mso":content=content.replace(new RegExp('<(\\w[^>]*) class="?mso([^ |>]*)([^>]*)','gi'),"<$1$3");break;}content=content.replace(new RegExp('href="?'+this._reEscape(""+document.location)+'','gi'),'href="'+this.editor.documentBaseURI.getURI());content=content.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi,"<$1$3");content=content.replace(/<\\?\?xml[^>]*>/gi,"");content=content.replace(/<\/?\w+:[^>]*>/gi,"");content=content.replace(/-- page break --\s*<p>&nbsp;<\/p>/gi,"");content=content.replace(/-- page break --/gi,"");if(!this.editor.getParam('force_p_newlines')){content=content.replace('','','gi');content=content.replace('</p>','<br /><br />','gi');}if(!tinymce.isIE&&!this.editor.getParam('force_p_newlines')){content=content.replace(/<\/?p[^>]*>/gi,"");}content=content.replace(/<\/?div[^>]*>/gi,"");if(this.editor.getParam("paste_convert_middot_lists",true)){var div=ed.dom.create("div",null,content);var className=this.editor.getParam("paste_unindented_list_class","unIndentedList");while(this._convertMiddots(div,"--list--"));while(this._convertMiddots(div,middot,className));while(this._convertMiddots(div,bull));content=div.innerHTML;}if(this.editor.getParam("paste_convert_headers_to_strong",false)){content=content.replace(/<h[1-6]>&nbsp;<\/h[1-6]>/gi,'<p>&nbsp;&nbsp;</p>');content=content.replace(/<h[1-6]>/gi,'<p><b>');content=content.replace(/<\/h[1-6]>/gi,'</b></p>');content=content.replace(/<b>&nbsp;<\/b>/gi,'<b>&nbsp;&nbsp;</b>');content=content.replace(/^(&nbsp;)*/gi,'');}content=content.replace(/--list--/gi,"");if(ed.getParam('paste_insert_word_content_callback'))content=ed.execCallback('paste_insert_word_content_callback','after',content);this.editor.execCommand("mceInsertContent",false,content);if(this.editor.getParam('paste_force_cleanup_wordpaste',true)){var ed=this.editor;window.setTimeout(function(){ed.execCommand("mceCleanup");},1);}}},_reEscape:function(s){var l="?.\\*[](){}+^$:";var o="";for(var i=0;i<s.length;i++){var c=s.charAt(i);if(l.indexOf(c)!=-1)o+='\\'+c;else o+=c;}return o;},_convertMiddots:function(div,search,class_name){var ed=this.editor,mdot=String.fromCharCode(183),bull=String.fromCharCode(8226);var nodes,prevul,i,p,ul,li,np,cp,li;nodes=div.getElementsByTagName("p");for(i=0;i<nodes.length;i++){p=nodes[i];if(p.innerHTML.indexOf(search)==0){ul=ed.dom.create("ul");if(class_name)ul.className=class_name;li=ed.dom.create("li");li.innerHTML=p.innerHTML.replace(new RegExp(''+mdot+'|'+bull+'|--list--|&nbsp;',"gi"),'');ul.appendChild(li);np=p.nextSibling;while(np){if(np.nodeType==3&&new RegExp('^\\s$','m').test(np.nodeValue)){np=np.nextSibling;continue;}if(search==mdot){if(np.nodeType==1&&new RegExp('^o(\\s+|&nbsp;)').test(np.innerHTML)){if(!prevul){prevul=ul;ul=ed.dom.create("ul");prevul.appendChild(ul);}np.innerHTML=np.innerHTML.replace(/^o/,'');}else{if(prevul){ul=prevul;prevul=null;}if(np.nodeType!=1||np.innerHTML.indexOf(search)!=0)break;}}else{if(np.nodeType!=1||np.innerHTML.indexOf(search)!=0)break;}cp=np.nextSibling;li=ed.dom.create("li");li.innerHTML=np.innerHTML.replace(new RegExp(''+mdot+'|'+bull+'|--list--|&nbsp;',"gi"),'');np.parentNode.removeChild(np);ul.appendChild(li);np=cp;}p.parentNode.replaceChild(ul,p);return true;}}return false;},_clipboardHTML:function(){var div=document.getElementById('_TinyMCE_clipboardHTML');if(!div){var div=document.createElement('DIV');div.id='_TinyMCE_clipboardHTML';with(div.style){visibility='hidden';overflow='hidden';position='absolute';width=1;height=1;}document.body.appendChild(div);}div.innerHTML='';var rng=document.body.createTextRange();rng.moveToElementText(div);rng.execCommand('Paste');var html=div.innerHTML;div.innerHTML='';return html;}});tinymce.PluginManager.add('paste',tinymce.plugins.PastePlugin);})();

(function(){var Event=tinymce.dom.Event,each=tinymce.each,DOM=tinymce.DOM;tinymce.create('tinymce.plugins.ContextMenu',{init:function(ed){var t=this;t.editor=ed;t.onContextMenu=new tinymce.util.Dispatcher(this);ed.onContextMenu.add(function(ed,e){if(!e.ctrlKey){t._getMenu(ed).showMenu(e.clientX,e.clientY);Event.add(document,'click',hide);Event.cancel(e);}});function hide(){if(t._menu){t._menu.removeAll();t._menu.destroy();Event.remove(document,'click',hide);}};ed.onMouseDown.add(hide);ed.onKeyDown.add(hide);},getInfo:function(){return{longname:'Contextmenu',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/contextmenu',version:tinymce.majorVersion+"."+tinymce.minorVersion};},_getMenu:function(ed){var t=this,m=t._menu,se=ed.selection,col=se.isCollapsed(),el=se.getNode()||ed.getBody(),am,p1,p2;if(m){m.removeAll();m.destroy();}p1=DOM.getPos(ed.getContentAreaContainer());p2=DOM.getPos(ed.getContainer());m=ed.controlManager.createDropMenu('contextmenu',{offset_x:p1.x,offset_y:p1.y,constrain:1});t._menu=m;m.add({title:'advanced.cut_desc',icon:'cut',cmd:'Cut'}).setDisabled(col);m.add({title:'advanced.copy_desc',icon:'copy',cmd:'Copy'}).setDisabled(col);m.add({title:'advanced.paste_desc',icon:'paste',cmd:'Paste'});if((el.nodeName=='A'&&!ed.dom.getAttrib(el,'name'))||!col){m.addSeparator();m.add({title:'advanced.link_desc',icon:'link',cmd:ed.plugins.advlink?'mceAdvLink':'mceLink',ui:true});m.add({title:'advanced.unlink_desc',icon:'unlink',cmd:'UnLink'});}m.addSeparator();m.add({title:'advanced.image_desc',icon:'image',cmd:ed.plugins.advimage?'mceAdvImage':'mceImage',ui:true});m.addSeparator();am=m.addMenu({title:'contextmenu.align'});am.add({title:'contextmenu.left',icon:'justifyleft',cmd:'JustifyLeft'});am.add({title:'contextmenu.center',icon:'justifycenter',cmd:'JustifyCenter'});am.add({title:'contextmenu.right',icon:'justifyright',cmd:'JustifyRight'});am.add({title:'contextmenu.full',icon:'justifyfull',cmd:'JustifyFull'});t.onContextMenu.dispatch(t,m,el,col);return m;}});tinymce.PluginManager.add('contextmenu',tinymce.plugins.ContextMenu);})();

(function(){tinymce.create('tinymce.plugins.IESpell',{init:function(ed,url){var t=this,sp;if(!tinymce.isIE)return;t.editor=ed;ed.addCommand('mceIESpell',function(){try{sp=new ActiveXObject("ieSpell.ieSpellExtension");sp.CheckDocumentNode(ed.getDoc().documentElement);}catch(e){if(e.number==-2146827859){ed.windowManager.confirm(ed.getLang("iespell.download"),function(s){if(s)window.open('http://www.iespell.com/download.php','ieSpellDownload','');});}else ed.windowManager.alert("Error Loading ieSpell: Exception "+e.number);}});ed.addButton('iespell',{title:'iespell.iespell_desc',cmd:'mceIESpell'});},getInfo:function(){return{longname:'IESpell (IE Only)',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/iespell',version:tinymce.majorVersion+"."+tinymce.minorVersion};}});tinymce.PluginManager.add('iespell',tinymce.plugins.IESpell);})();

(function(){tinymce.create('tinymce.plugins.XHTMLXtrasPlugin',{init:function(ed,url){ed.addCommand('mceCite',function(){ed.windowManager.open({file:url+'/cite.htm',width:350+parseInt(ed.getLang('xhtmlxtras.cite_delta_width',0)),height:250+parseInt(ed.getLang('xhtmlxtras.cite_delta_height',0)),inline:1},{plugin_url:url});});ed.addCommand('mceAcronym',function(){ed.windowManager.open({file:url+'/acronym.htm',width:350+parseInt(ed.getLang('xhtmlxtras.acronym_delta_width',0)),height:250+parseInt(ed.getLang('xhtmlxtras.acronym_delta_width',0)),inline:1},{plugin_url:url});});ed.addCommand('mceAbbr',function(){ed.windowManager.open({file:url+'/abbr.htm',width:350+parseInt(ed.getLang('xhtmlxtras.abbr_delta_width',0)),height:250+parseInt(ed.getLang('xhtmlxtras.abbr_delta_width',0)),inline:1},{plugin_url:url});});ed.addCommand('mceDel',function(){ed.windowManager.open({file:url+'/del.htm',width:340+parseInt(ed.getLang('xhtmlxtras.del_delta_width',0)),height:310+parseInt(ed.getLang('xhtmlxtras.del_delta_width',0)),inline:1},{plugin_url:url});});ed.addCommand('mceIns',function(){ed.windowManager.open({file:url+'/ins.htm',width:340+parseInt(ed.getLang('xhtmlxtras.ins_delta_width',0)),height:310+parseInt(ed.getLang('xhtmlxtras.ins_delta_width',0)),inline:1},{plugin_url:url});});ed.addCommand('mceAttributes',function(){ed.windowManager.open({file:url+'/attributes.htm',width:380,height:370,inline:1},{plugin_url:url});});ed.addButton('cite',{title:'xhtmlxtras.cite_desc',cmd:'mceCite'});ed.addButton('acronym',{title:'xhtmlxtras.acronym_desc',cmd:'mceAcronym'});ed.addButton('abbr',{title:'xhtmlxtras.abbr_desc',cmd:'mceAbbr'});ed.addButton('del',{title:'xhtmlxtras.del_desc',cmd:'mceDel'});ed.addButton('ins',{title:'xhtmlxtras.ins_desc',cmd:'mceIns'});ed.addButton('attribs',{title:'xhtmlxtras.attribs_desc',cmd:'mceAttributes'});if(tinymce.isIE){function fix(ed,o){if(o.set){o.content=o.content.replace(/<abbr([^>]+)>/gi,'<html:abbr $1>');o.content=o.content.replace(/<\/abbr>/gi,'</html:abbr>');}};ed.onBeforeSetContent.add(fix);ed.onPostProcess.add(fix);}ed.onNodeChange.add(function(ed,cm,n,co){n=ed.dom.getParent(n,'CITE,ACRONYM,ABBR,DEL,INS');cm.setDisabled('cite',co);cm.setDisabled('acronym',co);cm.setDisabled('abbr',co);cm.setDisabled('del',co);cm.setDisabled('ins',co);cm.setDisabled('attribs',n&&n.nodeName=='BODY');if(n){cm.setDisabled(n.nodeName.toLowerCase(),0);cm.setActive(n.nodeName.toLowerCase(),1);}else{cm.setActive('cite',0);cm.setActive('acronym',0);cm.setActive('abbr',0);cm.setActive('del',0);cm.setActive('ins',0);}});},getInfo:function(){return{longname:'XHTML Xtras Plugin',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/xhtmlxtras',version:tinymce.majorVersion+"."+tinymce.minorVersion};}});tinymce.PluginManager.add('xhtmlxtras',tinymce.plugins.XHTMLXtrasPlugin);})();


tinyMCE.addI18n({en:{
common:{
edit_confirm:"Do you want to use the WYSIWYG mode for this textarea?",
apply:"Apply",
insert:"Insert",
update:"Update",
cancel:"Cancel",
close:"Close",
browse:"Browse",
class_name:"Class",
not_set:"-- Not set --",
clipboard_msg:"Copy/Cut/Paste is not available in Mozilla and Firefox.\nDo you want more information about this issue?",
clipboard_no_support:"Currently not supported by your browser, use keyboard shortcuts instead.",
popup_blocked:"Sorry, but we have noticed that your popup-blocker has disabled a window that provides application functionality. You will need to disable popup blocking on this site in order to fully utilize this tool.",
invalid_data:"Error: Invalid values entered, these are marked in red.",
more_colors:"More colors"
},
contextmenu:{
align:"Alignment",
left:"Left",
center:"Center",
right:"Right",
full:"Full"
},
insertdatetime:{
date_fmt:"%Y-%m-%d",
time_fmt:"%H:%M:%S",
insertdate_desc:"Insert date",
inserttime_desc:"Insert time",
months_long:"January,February,March,April,May,June,July,August,September,October,November,December",
months_short:"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec",
day_long:"Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday",
day_short:"Sun,Mon,Tue,Wed,Thu,Fri,Sat,Sun"
},
print:{
print_desc:"Print"
},
preview:{
preview_desc:"Preview"
},
directionality:{
ltr_desc:"Direction left to right",
rtl_desc:"Direction right to left"
},
layer:{
insertlayer_desc:"Insert new layer",
forward_desc:"Move forward",
backward_desc:"Move backward",
absolute_desc:"Toggle absolute positioning",
content:"New layer..."
},
save:{
save_desc:"Save",
cancel_desc:"Cancel all changes"
},
nonbreaking:{
nonbreaking_desc:"Insert non-breaking space character"
},
iespell:{
iespell_desc:"Run spell checking",
download:"ieSpell not detected. Do you want to install it now?"
},
advhr:{
advhr_desc:"Horizontal rule"
},
emotions:{
emotions_desc:"Emotions"
},
searchreplace:{
search_desc:"Find",
replace_desc:"Find/Replace"
},
advimage:{
image_desc:"Insert/edit image"
},
advlink:{
link_desc:"Insert/edit link"
},
xhtmlxtras:{
cite_desc:"Citation",
abbr_desc:"Abbreviation",
acronym_desc:"Acronym",
del_desc:"Deletion",
ins_desc:"Insertion",
attribs_desc:"Insert/Edit Attributes"
},
style:{
desc:"Edit CSS Style"
},
paste:{
paste_text_desc:"Paste as Plain Text",
paste_word_desc:"Paste from Word",
selectall_desc:"Select All"
},
paste_dlg:{
text_title:"Use CTRL+V on your keyboard to paste the text into the window.",
text_linebreaks:"Keep linebreaks",
word_title:"Use CTRL+V on your keyboard to paste the text into the window."
},
table:{
desc:"Inserts a new table",
row_before_desc:"Insert row before",
row_after_desc:"Insert row after",
delete_row_desc:"Delete row",
col_before_desc:"Insert column before",
col_after_desc:"Insert column after",
delete_col_desc:"Remove column",
split_cells_desc:"Split merged table cells",
merge_cells_desc:"Merge table cells",
row_desc:"Table row properties",
cell_desc:"Table cell properties",
props_desc:"Table properties",
paste_row_before_desc:"Paste table row before",
paste_row_after_desc:"Paste table row after",
cut_row_desc:"Cut table row",
copy_row_desc:"Copy table row",
del:"Delete table",
row:"Row",
col:"Column",
cell:"Cell"
},
autosave:{
unload_msg:"The changes you made will be lost if you navigate away from this page."
},
fullscreen:{
desc:"Toggle fullscreen mode"
},
media:{
desc:"Insert / edit embedded media",
edit:"Edit embedded media"
},
fullpage:{
desc:"Document properties"
},
template:{
desc:"Insert predefined template content"
},
visualchars:{
desc:"Visual control characters on/off."
},
spellchecker:{
desc:"Toggle spellchecker",
menu:"Spellchecker settings",
ignore_word:"Ignore word",
ignore_words:"Ignore all",
langs:"Languages",
wait:"Please wait...",
sug:"Suggestions",
no_sug:"No suggestions",
no_mpell:"No misspellings found."
},
pagebreak:{
desc:"Insert page break."
}}});