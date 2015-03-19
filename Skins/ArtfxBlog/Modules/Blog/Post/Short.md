[STORPROC [!Query!]|Post]
	[STORPROC Systeme/User/[!Post::userCreate!]|Auteur][/STORPROC]
	<div class="BlocPost">
		[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat][/STORPROC]
		<div class="Titre[!Cat::Type!]">
			<h1>
				<a href="/Blog/Post/[!Post::Link!]" title="Acc&egrave;s au d&eacute;tail de [!Post::Titre!]">[!Post::Titre!]</a>
			</h1>
			<p class="Date">
				[IF [!Auteur::Site!]!=]
					Post&eacute; par <span class="Bold">
					<a href="http://[!Auteur::Site!]" title="[!Auteur::Site!]" rel="nofollow" onclick="window.open(this.href); return false;">[!Auteur::Prenom!] [!Auteur::Nom!]</a></span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]
				[ELSE]
					Post&eacute; par <span class="Bold">
					[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]
				[/IF]
			</p>
		</div>
		<div class="Post">
			[STORPROC Blog/Post/[!Post::Id!]/Fichier|Fich|0|1|tmsCreate|ASC]
				[IF [!Fich::Type!]=Image&&[!Fich::Fichier!]!=]
					<div class="FichPost">
						<a href="/Blog/Post/[!Post::Link!]"  title="Acc&egrave;s au d&eacute;tail de [!Post::Titre!]"><img src="/[!Fich::Fichier!].mini.425x130.jpg" alt="[!Post::Titre!]"  />
						</a>
					</div>
				[/IF]
			[/STORPROC]
			<div class="Border">[SUBSTR 320|[...]][!Utils::noHtml([!Post::Contenu!])!][/SUBSTR]</div>
			<ul class="BasPost">
				<li style="border-right:1px solid #5C5C5C;"><a href="/Blog/Categorie/[!Cat::Link!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]">Cat&eacute;gorie : [!Cat::Titre!]</a></li>
				<li style="border-right:1px solid #5C5C5C;">
					[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com]
					<a href="/Blog/Post/[!Post::Link!]#Commentaire" [IF [!Com!]>1] title="Lire les commentaires de [!Post::Titre!]" [/IF][IF [!Com!]=1] title="Lire le commentaire de [!Post::Titre!]" [/IF][IF [!Com!]=0] title="Proposer un commentaire &agrave; [!Post::Titre!]" [/IF]>
						
						
						[IF [!Com!]>1][!Com!] commentaires[/IF][IF [!Com!]=1][!Com!] commentaire[/IF]
						[IF [!Com!]=0]Proposer un commentaire[/IF]
					</a>
				</li>
				<li>
					[IF[!Lien!]=Categories]
						<a href="/Blog/Categorie/[!Cat::Link!]" title="Voir tous les articles de la cat&eacute;gorie [!Cat::Titre!]">Voir tous les articles</a>
					[ELSE]
						<a href="/Blog/Post/[!Post::Link!]" title="Acc&egrave;s au d&eacute;tail de l'article">D&eacute;tail du post</a>
					[/IF]
				</li>
			</ul>
		</div>
	</div>
[/STORPROC]