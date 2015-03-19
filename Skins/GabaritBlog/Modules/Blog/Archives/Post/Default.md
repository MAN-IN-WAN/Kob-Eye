[!Requete:=[!Query!]!]
[IF [!Mois!]!=&&[!Annee!]!=]
	//Calcul du timestamp de la date demand&eacute;e
	[!Depart:=[!Utils::getTms(1,[!Mois!],[!Annee!])!]!]
	[!Fin:=[!Utils::getTms(1,[!Mois:+1!],[!Annee!])!]!]
	[!Requete+=/tmsCreate>[!Depart!]&tmsCreate<[!Fin!]!]
	[!Filter:=1!]
	[!Titre:=Affichage des posts du [!Mois!]/[!Annee!] au [!Mois:+1!]/[!Annee!]!]
[/IF]
[IF [!Filter!]=][!Requete+=/!][ELSE][!Requete+=&!][/IF]
[!Requete+=Actif=1&Brouillon=0!]
//Parametres de la pagination
[!TypeEnf:=Post!]//Definition des elements a afficher
[!Page[!TypeEnf!]:=1!]//On definit la page 1 par defaut
[!MaxLine:=6!]//Nombre d elements qu on veut afficher par page
[COUNT [!Requete!]|Test2]//On compte le nombre total d element a afficher
[!TotalPage:=[!Test2:/[!MaxLine!]!]!]//On calcule le nombre total de page
[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]//On arrondit au chiffre superieur le nombre total de page
	[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
[/IF]
<h1>[!Titre!]</h1>
[STORPROC [!Requete!]|Post|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
	[STORPROC Systeme/User/[!Post::userCreate!]|Auteur][/STORPROC]
	[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat][/STORPROC]
	<div class="BlocPost">
		<h1><a href="/Blog/Post/[!Post::Link!]" title="Acc&egrave;s au d&eacute;tail de [!Post::Titre!]">[!Post::Titre!]</a></h1>
		<p>Post&eacute; par <span class="Bold">[!Auteur::Prenom!] [!Auteur::Nom!]</span> le [DATE d.m.Y][!Post::tmsCreate!][/DATE], &agrave; [UTIL HOUR][!Post::tmsCreate!][/UTIL]</p>
		<div class="Post">
			[STORPROC Blog/Post/[!Post::Id!]/Fichier|Fich|0|1|tmsCreate|ASC]
				[IF [!Fich::Type!]=Image&&[!Fich::Fichier!]!=]
					<div class="FichPost">
						<a href="/Blog/Post/[!Post::Link!]"  title="Acc&egrave;s au d&eacute;tail de [!Post::Titre!]"><img src="/[!Fich::Fichier!].mini.375x120.jpg" alt="[!Post::Titre!]"  />
						</a>
					</div>
				[/IF]
			[/STORPROC]
			<div>[SUBSTR 700|[...]][!Utils::noHtml([!Post::Contenu!])!][/SUBSTR]</div>
			<ul class="BasPost">
				<li>
					<a href="/Blog/Categorie/[!Cat::Link!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]">Cat&eacute;gorie : [!Cat::Titre!]</a>
				</li>
				<li>
					[COUNT Blog/Post/[!Post::Id!]/Commentaire/Actif=1|Com]
					<a href="/Blog/Post/[!Post::Link!]#Commentaire" [IF [!Com!]>1] title="Lire les commentaires de [!Post::Titre!]" [/IF][IF [!Com!]=1] title="Lire le commentaire de [!Post::Titre!]" [/IF][IF [!Com!]=0] title="Proposer un commentaire &agrave; [!Post::Titre!]" [/IF]>
						[IF [!Com!]>1][!Com!] commentaires[/IF][IF [!Com!]=1][!Com!] commentaire[/IF]
						[IF [!Com!]=0]Proposer un commentaire[/IF]
					</a>
				</li>
				<li style="border:none;">
					[IF[!Lien!]=Categories]
						<a href="/Blog/Categorie/[!Cat::Link!]" title="Voir tous les articles de la cat&eacute;gorie [!Cat::Titre!]">Voir tous les articles</a>
					[ELSE]
						<a href="/Blog/Post/[!Post::Link!]" title="Acc&egrave;s au d&eacute;tail de l'article">D&eacute;tail du post</a>
					[/IF]
				</li>
			</ul>
		</div>
	</div>
	[NORESULT]
		<div class="BlocPost">
			Aucun billet pour cette page<br /><br />
			<a href="[!SERVER::HTTP_REFERER!]" title="Retour &agrave; la liste des archives Artfx" style="background:none;color:#FFBB00;">Retour &agrave; la liste des archives</a>
		</div>
	[/NORESULT]
[/STORPROC]
//PAGINATION
[IF [!TotalPage!]>1]
	<form id="Pagination" action="/[!Lien!]" method="get">
		[IF [!TotalPage!]>1&&[!Page[!TypeEnf!]:+1!]<[![!TotalPage!]:+1!]]
			<div class="FlechesD">
				<input class="PageSuiv" type="submit" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]" /> 
				<!--<input class="Page2" type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" /> -->
			</div>		
		[/IF]
		[IF [!Page[!TypeEnf!]!]>1]
			<div class="FlechesG">
				<input class="Page1" type="submit" value="1" name="Page[!TypeEnf!]" />
				<input class="PagePrec" type="submit" value="[!Page[!TypeEnf!]:-1!]" name="Page[!TypeEnf!]" />
			</div>
		[/IF]
		//Liste des Numeros de pages
		<div class="NumPages">
			[STORPROC [!TotalPage!]|Pag]
				[IF [!Pos!]!=[!Page[!TypeEnf!]!]]
					<input type="submit" value="[!Pos!]" name="Page[!TypeEnf!]" /> 
				[ELSE]
					<span>[!Page[!TypeEnf!]!]</span>
				[/IF]
			[/STORPROC]
		</div>
		<input type="hidden" name="Mois" value="[!Mois!]" />
		<input type="hidden" name="Annee" value="[!Annee!]" />
		<div class="Clear"></div>
	</form>
[/IF]
