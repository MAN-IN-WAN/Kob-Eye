[STORPROC [!Query!]|Post]
[IF [!Post::Title!]!=][TITLE][!Post::Title!][/TITLE][/IF]
[IF [!Post::Description!]!=][DESCRIPTION][!Post::Description!][/DESCRIPTION][/IF]
[IF [!Post::Keywords!]!=][KEYWORDS][!Post::Keywords!][/KEYWORDS][/IF]
	[STORPROC Systeme/User/[!Post::userCreate!]|Auteur][/STORPROC]
	<div class="BlocPost">
		//On affiche les posts si ils sont a l etat actif et non brouillon
		[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat][/STORPROC]
		<div class="Titre[!Cat::Type!]">
			<h1>[!Post::Titre!]</h1>
			[IF [!Auteur::Site!]!=]
				<p class="Date">Post&eacute; par <span class="Bold"><a href="http://[!Auteur::Site!]" title="[!Auteur::Site!]" rel="nofollow" onclick="window.open(this.href); return false;">[!Auteur::Prenom!] [!Auteur::Nom!]</a></span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]</p>
			[ELSE]
				<p class="Date">Post&eacute; par <span class="Bold">[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]</p>
			[/IF]
		</div>
		<div class="Post">			
			<div style="overflow:auto;width:100%;">
				//On verifie si il y a des fichiers
				[COUNT Blog/Post/[!Post::Id!]/Fichier|Fichu]
				[IF [!Fichu!]]
					[STORPROC Blog/Post/[!Post::Id!]/Fichier|Fich|0|1|tmsCreate|ASC]
							[IF [!Fich::Type!]=Image]
								<div class="FichPost">
									<a href="/[!Fich::Fichier!].limit.1024x1024.jpg" class="mb" rel="type:jpg" title="[IF [!Fich::Titre!]!=][!Fich::Titre!][ELSE][!Post::Titre!][/IF]"><img src="/[!Fich::Fichier!].mini.425x130.jpg" alt="[!Fich::Titre!]"  />
									</a>
								</div>
							[/IF]
					[/STORPROC]
				[/IF]
				[IF [!Fichu!]>1]
					[STORPROC Blog/Post/[!Post::Id!]/Fichier|Fich|1|10|tmsCreate|ASC]
						//je verifie le type de mon fichier et qu il n est pas vide
						[IF [!Fich::Fichier!]!=&&[!Fich::Type!]=Image]
							<div class="FichPost" style="float:left;margin-right:5px;margin-top:0;display:block;">
								<a href="/[!Fich::Fichier!].limit.1024x1024.jpg" class="mb" rel="type:jpg" title="[IF [!Fich::Titre!]!=][!Fich::Titre!][ELSE][!Post::Titre!][/IF]"><img src="/[!Fich::Fichier!].mini.50x50.jpg" alt="[!Fich::Titre!]"  style="border:5px solid #0C0C0C;"/>
								</a>
							</div>
						[/IF]
						[IF [!Fich::Fichier!]!=&&[!Fich::Type!]=Video]
							<div class="FichPost" style="float:left;margin-right:5px;margin-top:0;display:block;">
								<a href="/[!Fich::Fichier!]" class="mb"  title="[IF [!Fich::Titre!]!=][!Fich::Titre!][ELSE][!Post::Titre!][/IF]"><img src="/Skins/ArtfxBlog/Img/ImgVideo.jpg" alt="[!Fich::Titre!]"  style="border:5px solid #0C0C0C;width:50px;height:50px;"/>
								</a>
							</div>
						[/IF]
						[IF [!Fich::Fichier!]!=&&[!Fich::Type!]=Son]
							<div class="FichPost" style="float:left;margin-right:5px;margin-top:0;display:block;">
								<a href="/[!Fich::Fichier!]" class="mb" title="[IF [!Fich::Titre!]!=][!Fich::Titre!][ELSE][!Post::Titre!][/IF]"><img src="/[!Fich::Fichier!].mini.50x50.jpg" alt="[!Fich::Titre!]"  style="border:5px solid #0C0C0C;"/>
								</a>
							</div>
						[/IF]
					[/STORPROC]
				[/IF]
			</div>
			<!--<div style="overflow:auto;width:100%;">
				[STORPROC Blog/Post/[!Post::Id!]/Fichier/Type!=Image|Fich|0|10|tmsCreate|ASC]
					//je verifie le type de mon fichier et qu il n est pas vide
					[IF [!Fich::Fichier!]!=&&[!Fich::Type!]=Video]
						<div style="width:350px;height:375px;margin-left:15px;">
							<object height="350" width="375" title="Artfx video" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
							<param value="/Skins/ArtfxBlog/Js/Files/flvplayer.swf" name="movie"/>
							<param value="high" name="quality"/>
							<param value="TL" name="salign"/>
							<param value="noScale" name="scale"/>
							<param value="path=http://www.artfx.fr/[!Fich::Fichier!]" name="FlashVars"/>
							<embed height="350" width="375" flashvars="path=http://www.artfx.fr/[!Fich::Fichier!]" scale="noScale" salign="TL" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" quality="high" src="/Skins/ArtfxBlog/Js/Files/flvplayer.swf"/>
							</object>
						</div>
					[/IF]
					[IF [!Fich::Fichier!]!=&&[!Fich::Type!]=Son]
						<div style="width:320px;height:75px;margin-left:auto;margin-right:auto;">
							<object height="70" width="320" title="Artfx video" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
							<param value="/Skins/ArtfxBlog/Js/Files/mp3player.swf" name="movie"/>
							<param value="high" name="quality"/>
							<param value="TL" name="salign"/>
							<param value="noScale" name="scale"/>
							<param value="path=http://www.artfx.fr/[!Fich::Fichier!]" name="FlashVars"/>
							<embed height="70" width="320" flashvars="path=http://www.artfx.fr/[!Fich::Fichier!]" scale="noScale" salign="TL" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" quality="high" src="/Skins/ArtfxBlog/Js/Files/mp3player.swf"/>
							</object>
						</div>
					[/IF]
				[/STORPROC]
			</div>-->
			<div class="Border">[!Post::Contenu!]</div>
			<ul class="BasPost">
				<li style="border-right:1px solid #5C5C5C;">Cat&eacute;gorie : [!Cat::Titre!]</li>
				<li>
					[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com]
					<a href="/Blog/Post/[!Post::Id!]" [IF [!Com!]>1] title="Lire les commentaires de [!Post::Titre!]" [/IF][IF [!Com!]=1] title="Lire le commentaire de [!Post::Titre!]" [/IF][IF [!Com!]=0] title="Proposer un commentaire &agrave; [!Post::Titre!]" [/IF]>[IF [!Com!]>1][!Com!] commentaires[/IF][IF [!Com!]=1][!Com!] commentaire[/IF][IF [!Com!]=0]Proposer un commentaire[/IF]</a>
				</li>
			</ul>
		</div>
		[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|NbCom]
		<div class="Comments">
			[IF [!NbCom!]>0]
				<h2><a name="Commentaire" title="Vos commentaires sur [!Post::Titre!]" class="Ancre">Vos commentaires</a></h2>
				[STORPROC Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com|0|100|tmsCreate|ASC]
					[IF [!Com::Site!]!=]
						[BLOC Rounded|background-color:#0C0C0C;||padding:5px;]
							<p class="Date">Post&eacute; par <span class="Bold" style="color:#FFBB00"><a href="http://[!Com::Site!]" title="Site de [!Com::Pseudo!]" onclick="window.open(this.href); return false;">[!Com::Pseudo!]</a></span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]</p>
<!-- 							<h3>[!Com::Titre!]</h3> -->
							<p style="padding-top:5px;">[!Com::Comment!]</p>
						[/BLOC]
					[ELSE]
						[BLOC Rounded|background-color:#0C0C0C;||padding:5px;]
							<p class="Date">Post&eacute; par <span class="Bold" style="color:#FFBB00">[!Com::Pseudo!]</span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]</p>
<!-- 							<h3>[!Com::Titre!]</h3> -->
							<p style="padding-top:5px;">[!Com::Comment!]</p>
						[/BLOC]
					[/IF]
					
				[/STORPROC]					
			[/IF]
			[MODULE Blog/Commentaire/Ajouter]
		</div>
	</div>
[/STORPROC]