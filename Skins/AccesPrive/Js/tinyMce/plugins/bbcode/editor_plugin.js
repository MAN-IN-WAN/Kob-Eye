/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

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