[STORPROC [!Query!]|Post]
//	[IF [!Post::Title!]!=][TITLE][!Post::Title!][/TITLE][/IF]
//	[IF [!Post::Description!]!=][DESCRIPTION][!Post::Description!][/DESCRIPTION][/IF]
//	[IF [!Post::Keywords!]!=][KEYWORDS][!Post::Keywords!][/KEYWORDS][/IF]
	[STORPROC Systeme/User/[!Post::userCreate!]|Auteur][/STORPROC]
	<h1>[!Post::Titre!]</h1>
	<div class="BlocPost">
		//On affiche les posts si ils sont a l etat actif et non brouillon
		[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat][/STORPROC]
		[IF [!Auteur::Site!]!=]
			<p>Post&eacute; par <span class="Bold"><a href="http://[!Auteur::Site!]" title="[!Auteur::Site!]"  onclick="window.open(this.href); return false;">[!Auteur::Prenom!] [!Auteur::Nom!]</a></span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]</p>
		[ELSE]
			<p>Post&eacute; par <span class="Bold">[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]</p>
		[/IF]
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
			<div>[!Post::Contenu!]</div>
			<ul class="BasPost">
				<li>Cat&eacute;gorie : [!Cat::Titre!]</li>
				<li style="border:none;">
					[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com]
					<a href="/Blog/Post/[!Post::Id!]" [IF [!Com!]>1] title="Lire les commentaires de [!Post::Titre!]" [/IF][IF [!Com!]=1] title="Lire le commentaire de [!Post::Titre!]" [/IF][IF [!Com!]=0] title="Proposer un commentaire &agrave; [!Post::Titre!]" [/IF]>[IF [!Com!]>1][!Com!] commentaires[/IF][IF [!Com!]=1][!Com!] commentaire[/IF][IF [!Com!]=0]Proposer un commentaire[/IF]</a>
				</li>
			</ul>
		</div>
		[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|NbCom]
		<div>
			[IF [!NbCom!]>0]
				<h2>Vos commentaires</h2>
				[STORPROC Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com|0|100|tmsCreate|ASC]
					<div class="Comments">
						[IF [!Com::Site!]!=]
							<p>Post&eacute; par <span class="Bold"><a href="http://[!Com::Site!]" title="Site de [!Com::Pseudo!]" onclick="window.open(this.href); return false;">[!Com::Pseudo!]</a></span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]</p>
						[ELSE]
							<p>Post&eacute; par <span class="Bold">[!Com::Pseudo!]</span> le [DATE d.m.Y][!Com::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Com::tmsCreate!][/UTIL]</p>
						[/IF]
						<h3>[!Com::Titre!]</h3>
						<p>[!Com::Comment!]</p>
					</div>
				[/STORPROC]
			[/IF]
			[MODULE Blog/Commentaire/Ajouter]
		</div>
	</div>
[/STORPROC]