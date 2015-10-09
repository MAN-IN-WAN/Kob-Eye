
[STORPROC [!Query!]|Pst|0|1]
	[TITLE][!Pst::TitleMeta!][/TITLE]
	[DESCRIPTION][!Pst::DescriptionMeta!][/DESCRIPTION]
	[STORPROC Systeme/User/[!Pst::userCreate!]|Auteur][/STORPROC]
	<div class="BlocPost">
		[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat][/STORPROC]
		<div class="TitrePost">
			<div class="LigneTitrePost">
				[IF [!Cat::Icone!]!=]<div class="ImageCat"><img src="/[!Cat::Icone!]" alt="[!Cat::Titre!]" ></div>[/IF]
				<h2>
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="Acc&egrave;s au d&eacute;tail de [!Pst::Titre!]">[!Pst::Titre!]</a>
				</h2>
			</div>
			<div class="LigneTitrePostDate">
				Post&eacute; par <span class="Auteur">
				[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Pst::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Pst::tmsCreate!][/UTIL]
			</div>
		</div>
		<div class="Post">
			// dans la liste des post on affiche la dernière image liée
			<div class="ContenuPost">[!Pst::Contenu!]</div>
			<div >
				//On verifie si il y a des fichiers
				[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|||tmsCreate|DESC]
					<div class="FichPost">
						[LIMIT 0|1]
							<a href="/[!Don::Fichier!]" class="mb" rel="[images[!Pst::Id!]]" title="[!Don::Titre!]" style="float:none;">
								<img src="/[!Don::Fichier!].mini.575x200.jpg" alt="[!Don::Titre!]"  title="[!Don::Titre!]" />
							</a>	
						[/LIMIT]
						[IF [!NbResult!]>1]
							<div id="Diapo" style="overflow:hidden;">
								[LIMIT 1|100]
									<a href="/[!Don::Fichier!]" class="mb" rel="[images[!Pst::Id!]]" title="[!Don::Titre!]" style="margin:10px 10px 0 0 ;">
									<img src="/[!Don::Fichier!].mini.60x60.jpg" width="60" height="60" alt="Titre" />
									</a>
								[/LIMIT]
							</div>
						[/IF]
					</div>
				[/STORPROC]
			
				[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Video|Donn|||tmsCreate|ASC]
					<div class="FichPost">
						<a href="/[!Donn::Fichier!]" class="mb"  title="[IF [!Donn::Titre!]!=][!Donn::Titre!][ELSE][!Pst::Titre!][/IF]">[!Donn::Titre!]</a>
					</div>
				[/STORPROC]
				[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Son|Son|0|1|tmsCreate|ASC]
					<div class="FichPost">
						<a href="/[!Donn::Fichier!]" class="mb"  title="[IF [!Donn::Titre!]!=][!Donn::Titre!][ELSE][!Pst::Titre!][/IF]">[!Donn::Titre!]</a>
					</div>
				[/STORPROC]
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
					// Envoyer à un ami
					<div style="position:absolute; left:247px; top: 0">
						<a class="SendFriend" href="[!Domaine!]/Envoyer-a-un-ami?C_Lien=/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]">Envoyer à un ami</a>
						</div>
				</div>
			</div>
	
			<ul >
				<li style="border-right:none;;"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]">Cat&eacute;gorie : [!Cat::Titre!]</a></li>
	
			</ul>
		</div>
		[COUNT Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|NbCom]
		<div class="Comments">
			[IF [!NbCom!]>0]
				<h2><a name="Commentaire" title="Vos commentaires sur [!Pst::Titre!]" class="Ancre">Vos commentaires</a></h2>
				[STORPROC Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|Com|0|100|tmsCreate|DESC]
					<div class="LigneTitrePostDate well [IF [!Utils::isPair([!Pos!])!]]darker[/IF]">
						Post&eacute; par <span class="Auteur">[IF [!Com::Site!]]<a href="[!Com::Site!]" target="_blank" rel="nofollow">[/IF][!Com::Pseudo!][IF [!Com::Site!]]</a>[/IF]</span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]
						<div class="ContenuPost">[!Com::Comment!]</div>
					</div>
	
				[/STORPROC]					
			[/IF]
			//[MODULE Blog/Commentaire/Ajouter]
		</div>
	</div>
[/STORPROC]
	
	