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
	<div class="well">
		[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat][/STORPROC]
				[IF [!Cat::Icone!]!=]<div class="ImageCat"><img src="/[!Cat::Icone!]" alt="[!Cat::Titre!]" ></div>[/IF]
            <h2>
                <a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de [!Pst::Titre!]">[!Pst::Titre!]</a>
            </h2>
			<p>
				Post&eacute; par <span class="Auteur">
				[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Pst::Date!][/DATE], &agrave; [UTIL HOUR][!Pst::Date!][/UTIL]
			</p>
            <div class="row-fluid">
                // dans la liste des post on affiche la dernière image liée
                [STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|0|1|tmsCreate|DESC]
                <div class="span4">
                    <a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de l'article"><img src="/[!Don::Fichier!].mini.200x200.jpg" alt="[!Don::Titre!]"  title="[!Don::Titre!]" /></a>
                </div>
                [/STORPROC]
                <div class="span8">
                    <blockquote>[!Pst::Resume!]</blockquote>
                    <div>
                        <ul >
                            <li><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]">Cat&eacute;gorie : [!Cat::Titre!]</a></li>
                            <li>
                                [COUNT Blog/Post/[!Pst::Id!]/Commentaire/Actif=1|Com]
                                <a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]#Commentaire" [IF [!Com!]>1] title="Lire les commentaires de [!Pst::Titre!]" [/IF][IF [!Com!]=1] title="Lire le commentaire de [!Pst::Titre!]" [/IF][IF [!Com!]=0] title="Proposer un commentaire &agrave; [!Pst::Titre!]" [/IF]>
                                    [IF [!Com!]>1][!Com!] commentaires[/IF][IF [!Com!]=1][!Com!] commentaire[/IF]
                                    [IF [!Com!]=0]Proposer un commentaire[/IF]
                                </a>
                            </li>
                            <li style="border-right:none;">
                                <a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de l'article">D&eacute;tail du post</a>
                            </li>

                        </ul>
                    </div>
                    <!--<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de l'article" class="btn btn-primary btn-block">Lire la suite</a>-->
               </div>
            </div>
        <div style="overflow:hidden;padding-top:2px">
            <div style="position:relative;width:278px;height:20px;float:right;">
                // Facebook
                <div style="position:absolute; left:0; top: 0">
                    <iframe src="http://www.facebook.com/plugins/like.php?href=[!Domaine!]/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px" allowTransparency="true"></iframe>
                </div>
                // Google
                <div style="position:absolute; left:90px; top: 3px">
                    <script type="text/javascript">document.write('<g:plusone size="small"></g:plusone>')</script>
                    <script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
                </div>
                // Twitter
                <div style="position:absolute; left:145px; top: 0">
                    <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="InfoWebMaster">Tweet</a>
                    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                </div>
            </div>
        </div>
    </div>
[/STORPROC]
<!--<div class="content_sortPagiBar">
	<div class="row pagePagiBar">
		<div class="col-md-9">
			<div class="btn-toolbar" role="toolbar">
				<div class="btn-group" role="group">
					<button class="btn btn-default" disabled="disabled">Page 1 sur [!NbPage!] </button>
					[IF [!Page!]>1]
					<a href="/[!Lien!]" class="btn btn-default"><span>&laquo;</span></a>
					<a href="[IF [!Page!]=2]/[!Lien!][ELSE]?Page=[!Page:-1!][/IF]" class="btn btn-default">&lsaquo;</a>
					[IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
					<a href="/[!Lien!]" class="btn btn-default"><span>1</span></a>
					<a href="#" class="btn btn-default" disabled="disabled"><span>...</span></a>
					[/IF]
					[/IF]
					[!start:=1!]
					[IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
					[STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
					<a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]" class="btn btn-default [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
					[/STORPROC]
					[IF [!Page!]<[!NbPage!]]
					[IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
					<a href="#" class="btn btn-default"><span>...</span></a>
					<a href="?Page=[!NbPage!]" class="btn btn-default">[!NbPage!]</a>
					[/IF]
					<a href="?Page=[!Page:+1!]" class="btn btn-default"><span>&rsaquo;</span></a>
					<a href="?Page=[!NbPage!]" class="btn btn-default">&raquo;</a>
					[/IF]
				</div>
			</div>
			<div class="col-md-3">
				<div class="inner">
				</div>
			</div>
		</div>
	</div>
	-->