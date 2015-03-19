[HEADER]
	<style type="text/css">
		#Milieu{
			margin-right:0;
			padding-bottom:20px;
		}
	</style>
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
[MODULE Systeme/Ariane]
<div id="Milieu">
[STORPROC [!Query!]|this|0|1]
	<table class="TableSuj">
		<thead>
			<tr>
				<td>Auteur</td>
				<td>Messages</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="TdAuteur">
					[MODULE Systeme/User/[!this::userCreate!]/InfoUser]
				</td>
				<td>
					<div class="PostForum">
						<p>
							<img src="/Skins/Intranet/Img/Forum/toutPetitSujet.gif" alt="sujet" />
							<span class="Bold">Sujet : </span>[!this::Titre!]
						</p>
						<p>
							<img src="/Skins/Intranet/Img/Forum/toutPetitHeure.gif" alt="sujet" />
							<span class="Bold">Post&eacute; le : </span>[UTIL FULLDATEFR][!this::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!this::tmsCreate!][/UTIL]
						</p>
						<p>
							[IF [!this::Contenu!]]
								<p><span class="Bold">Message : </span>
								[!this::Contenu!]</p>
							[/IF]
							[STORPROC Forum/Post/[!this::Id!]/Fichier|Fich]
								[IF [!Fich::URL!]~jpg]
									<img src="/[!Fich::URL!]" alt="" />
								[/IF]
							[/STORPROC]
						</p>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	[IF [!Systeme::User::Public!]]
		<div class="Infos">Vous n'&ecirc;tes pas autoris&eacute; !</div>
	[ELSE]
		[IF [!confPost!]=Modifier]
			[METHOD this|Set]
				[PARAM]Titre[/PARAM]
				[PARAM][!titrePost!][/PARAM]
			[/METHOD]
			[METHOD this|Set]
				[PARAM]Contenu[/PARAM]
				[PARAM][!Form_contPost!][/PARAM]
			[/METHOD]
			[METHOD this|Save][/METHOD]
			[IF [!Form_URL_Upload::error!]=0]
			// L input type=file génère un tableau avec différents champs dont error qui est =0 s il y a 1 fichier
				[OBJ Forum|Fichier|Fich]
				[METHOD Fich|Set]
					[PARAM]Titre[/PARAM]
					[PARAM][!titrePost!][/PARAM]
				[/METHOD]
				[METHOD Fich|Set]
					[PARAM]URL[/PARAM]
					[PARAM][!Form_URL_Upload!][/PARAM]
				[/METHOD]
				[METHOD Fich|AddParent]
					[PARAM]Forum/Post/[!this::Id!][/PARAM]
				[/METHOD]
				[METHOD Fich|Save][/METHOD]
			[/IF]
			[STORPROC [!Supprimer!]|S]
			// On fait un storproc sur le tableau de la variable générée par la checkbox cochée
				[STORPROC Forum/Fichier/[!S!]|Fic]
					[!Fic::Delete!]
				[/STORPROC]
			[/STORPROC]
			[REDIRECT][!referer!][/REDIRECT]
		[/IF]
		<form name="" action="#" method="post" class="ForumF" enctype="multipart/form-data">
			<p>Modifier le message</p>
			[IF [!Num!]=1]
				<div class="LigneForm">
					<label for='titreSujet'>Titre : </label>
					<input type='text' id='titrePost' name='titrePost' value="[!this::Titre!]"/>
				</div>
			[/IF]
			<div class="LigneForm">
				<label for='descSujet'>Message : </label>
				<textarea ROWS="15" class="Champ" name="Form_contPost">[!this::Contenu!]</textarea>
			</div>
			[STORPROC Forum/Post/[!this::Id!]/Fichier|Fich]
				<div class="LigneForm">
					<label>&nbsp;</label>
					<div class="FicPost">
						[IF [!Fich::URL!]~jpg||[!Fich::URL!]~png]
							<img src="/[!Fich::URL!].limit.500x300.jpg" alt="Image attach&eacute;e au message" title="Image attach&eacute;e au message" />
							
						[ELSE]
							<a href="/[!Fich::URL!]" title="Document attach&eacute; au message">[!Fich::URL!]</a>
						[/IF]
						<input type="checkbox" name="Supprimer[]" value="[!Fich::Id!]" />Supprimer le fichier actuel
						// Supprimer[] crée un tableau où seront stockés les Id des fichiers à supprimer
					</div>
				</div>
			[/STORPROC]
			<div class="LigneForm">
				<label for="URL">Fichier : </label>
				<input type="file" name="Form_URL_Upload" />
			</div>
			[BLOC Bouton|width:140px;margin-left:775px;||width:105px;]
				<input type="hidden" name="referer" value='[!referer!]'/>
				<input type='submit' class='button' name='confPost' value="Modifier"/>
			[/BLOC]
		</form>
	[/IF]
[/STORPROC]
</div>
<div class="Clear"></div>