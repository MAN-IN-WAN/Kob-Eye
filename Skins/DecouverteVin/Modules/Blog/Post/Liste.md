//PARAMETRES
[IF [!Page!]=][!Page:=1!][/IF]
[COUNT [!Chemin!]|Nb]
[!NbParPage:=5!]
[!NbNumParPage:=3!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]

[!Limit1:=!]
[!Limit2:=!]

[IF [!Lien!]=]
	[!Limit1:=0!]
	[!Limit2:=5!]
[/IF]
[STORPROC [!Chemin!]|Pst|[![!Page:-1!]:*[!NbParPage!]!]|[!NbParPage!]|Date|DESC]
[NORESULT]
	<div class="alert alert-danger" style="margin:20px;">Pas de résultat...</div>
[/NORESULT]
	[STORPROC Systeme/User/[!Pst::userCreate!]|Auteur][/STORPROC]
	<div class="box">
		[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat][/STORPROC]
		<div class="TitrePost" >
			<div class="LigneTitrePost">
				[IF [!Cat::Icone!]!=]<div class="ImageCat"><img src="/[!Cat::Icone!]" alt="[!Cat::Titre!]" ></div>[/IF]
				<h2>
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de [!Pst::Titre!]">[!Pst::Titre!]</a>
				</h2>
			</div>
			<div class="LigneTitrePostDate">
				"Post&eacute; par <span class="Auteur">
				[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Pst::Date!][/DATE], &agrave; [UTIL HOUR][!Pst::Date!][/UTIL]
				<span class="badge badge-warning">Cat&eacute;gorie : [!Cat::Titre!]</span>
				[COUNT Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|Com]
				<span class="badge badge-info">[IF [!Com!]][!Com!][ELSE]0[/IF] commentaires</span>
			</div>
		</div>
		<div class="Post">
			<div class="ContenuPost"><blockquote>[!Pst::Resume!]</blockquote></div>
			// dans la liste des post on affiche la dernière image liée
			[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|0|1|tmsCreate|DESC]
				<div class="FichPost">
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de l'article"><img src="/[!Don::Fichier!].mini.840x200.jpg" alt="[!Don::Titre!]"  title="[!Don::Titre!]" /></a>
				</div>
			[/STORPROC]
			<div class="pull-right" style="line-height:25px;">
				// Facebook
				<iframe src="http://www.facebook.com/plugins/like.php?href=[!Domaine!]/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px" allowTransparency="true"></iframe>
				// Google
				<script type="text/javascript">document.write('<g:plusone size="small"></g:plusone>')</script>
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
				// Twitter
				<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="InfoWebMaster">Tweet</a>
				<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				// Envoyer à un ami
				<a  href="[!Domaine!]/Envoyer-a-un-ami?C_Lien=/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" class="btn btn-small btn-danger" style="margin-bottom:10px;">Envoyer à un ami</a>
			</div>
			<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de l'article" class="btn btn-danger">Lire la suite</a>
		</div>
	</div>
[/STORPROC]
<div class="fluid-container">
	
	<div class="pagination"> <!-- Start Paging --> 
		<ul>
			//<li><button class="active">Page 1 sur [!NbPage!] </button></li> 
			[IF [!Page!]>1]
				<li><a href="/[!Lien!]" class=""><span>&laquo;</span></a></li>
				<li><a href="[IF [!Page!]=2]/[!Lien!][ELSE]?Page=[!Page:-1!][/IF]" class="">&lsaquo;</a>
				[IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
					<li><a href="/[!Lien!]" class=""><span>1</span></a></li> 
					<li><a href="#" class=""><span>...</span></a></li> 
				[/IF]
			[/IF]
			[!start:=1!]
			[IF [!Page!]>[!start:+[!NbNumParPage:/2!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
			[STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
			<li class=" [IF [!P!]=[!Page!]]active[/IF]"><a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]" class="">[!P!]</a></li> 
			[/STORPROC]
			[IF [!Page!]<[!NbPage!]]
				[IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
					<li><a href="#" class=""><span>...</span></a></li> 
					<li><a href="?Page=[!NbPage!]" class="">[!NbPage!]</a></li> 
				[/IF]
				<li><a href="?Page=[!Page:+1!]" class=""><span>&rsaquo;</span></a></li> 
				<li><a href="?Page=[!NbPage!]" class="">&raquo;</a></li> 
			[/IF] 
		</ul>
	</div>	<!-- End Paging -->
</div>
