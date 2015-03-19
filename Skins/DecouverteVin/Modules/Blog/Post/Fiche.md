
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
				"Post&eacute; par <span class="Auteur">
				[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Pst::Date!][/DATE], &agrave; [UTIL HOUR][!Pst::Date!][/UTIL]
				<span class="badge badge-warning">Cat&eacute;gorie : [!Cat::Titre!]</span>
				[COUNT Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|Com]
				<span class="badge badge-info">[IF [!Com!]][!Com!][ELSE]0[/IF] commentaires</span>
			</div>
			[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|0|1|tmsCreate|DESC]
				<div class="FichPost">
					[LIMIT 0|1]
						<a href="#lightBox[!Pos!]" title="[!Don::Titre!]" style="float:none;"  data-toggle="lightbox" >
							<img src="/[!Don::Fichier!].mini.870x200.jpg" alt="[!Don::Titre!]"  title="[!Don::Titre!]" />
						</a>	
						<div  id="lightBox[!Pos!]" class="lightbox fade hide"  tabindex="-1" role="dialog" aria-hidden="true">
								<div class='lightbox-content'>
									<img src="/[!Don::Fichier!].limit.800x600.jpg" alt="[!Don::Titre!]" />
									<div class="lightbox-caption"><p>[!Don::Titre!]</p></div>
								</div>
						</div>
					[/LIMIT]
				</div>
			[/STORPROC]
		</div>
		<div class="Post">
			// dans la liste des post on affiche la dernière image liée
			<div class="ContenuPost">[!Pst::Contenu!]</div>
			<div >
				//On verifie si il y a des fichiers
				[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|||tmsCreate|DESC]
					<div class="FichPost">
							<div id="Diapo" style="overflow:hidden;">
								[LIMIT 1|100]
									<a title="[!Don::Titre!]" style="margin:10px 10px 0 0 ;" data-toggle="lightbox" href="#lightBox[!Pos!]">
										<img src="/[!Don::Fichier!].mini.60x60.jpg" width="60" height="60" alt="Titre" />
									</a>
									<div  id="lightBox[!Pos!]" class="lightbox fade hide"  tabindex="-1" role="dialog" aria-hidden="true">
											<div class='lightbox-content'>
												<img src="/[!Don::Fichier!].limit.800x600.jpg" alt="[!Don::Titre!]" />
												<div class="lightbox-caption"><p>[!Don::Titre!]</p></div>
											</div>
									</div>
								[/LIMIT]
							</div>
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
		</div>
			<div  style="line-height:25px;">
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
		[COUNT Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|NbCom]
		<div class="well row-fluid">
			<div class="span6">
			[IF [!NbCom!]>0]
				<div class="box">
					<h2><a name="Commentaire" title="Vos commentaires sur [!Pst::Titre!]" class="Ancre">Vos commentaires</a></h2>
					[STORPROC Blog/Post/[!Pst::Id!]/Commentaire/Publier=1|Com|0|100|tmsCreate|DESC]
						<div class=" well [IF [!Utils::isPair([!Pos!])!]]darker[/IF]">
							Post&eacute; par <span class="Auteur">[IF [!Com::Site!]]<a href="[!Com::Site!]" target="_blank" rel="nofollow">[/IF][!Com::Pseudo!][IF [!Com::Site!]]</a>[/IF]</span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]
							<div class="ContenuPost">[!Com::Comment!]</div>
						</div>
		
					[/STORPROC]		
				</div>			
			[/IF]
			</div>
			<div class="span6">
			[MODULE Blog/Commentaire/Ajouter]
			</div>
		</div>
	</div>
[/STORPROC]
	
	