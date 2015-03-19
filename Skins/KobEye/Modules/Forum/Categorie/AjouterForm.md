[HEADER]
	<script type="text/javascript" src="/Skins/[!Systeme::Skin!]/Js/tinyMce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			theme : "advanced",
			mode : "textareas",
			plugins : "bbcode,fullscreen,table,inlinepopups",
			theme_advanced_buttons1 : "bold,italic,underline,undo,redo,link,table,unlink,image,forecolor,removeformat,cleanup,code,bullist,numlist,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
			content_css : "/Skins/[!Systeme::Skin!]/Css/bbcode.css",
			entity_encoding : "raw",
			add_unload_trigger : false,
			remove_linebreaks : false,
			force_br_newlines : false,
			convert_newlines_to_brs : false,
			convert_urls : false
		});
	</script>
[/HEADER]
[IF [!Systeme::User::Public!]]
	<div class="Infos">Vous n'&ecirc;tes pas autoris&eacute; &agrave; ajouter un sujet</div>
[ELSE]
	<form action="/[!referer!]" method="post" class="ForumF">
		<p>Nouveau th&egrave;me [IF [!parent!]!=""][STORPROC Forum/Categorie/[!parent!]|CatP]dans "[!CatP::getFirstSearchOrder!]"[/STORPROC][/IF]</p>
		<div class="LigneForm">
			[BLOC Bouton|width:85px;float:right;margin-top:-6px;margin-bottom:-6px;]
				<input type="hidden" name="parent" value="[!parent!]" />
				<input type="hidden" name="referer" value="[!referer!]" />
				<input type="submit" name="confCategorie" value="Ajouter" />
			[/BLOC]
			<label for="nomCategorie">Nom : </label>
			<input type="text" class="Text" name="nomCategorie"/>
		</div>
	</form>
[/IF]
