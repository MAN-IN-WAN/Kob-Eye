//affichage des posts en liste
[!LIENURL:=/[!Lien!]?RechercheMotCle=[!RechercheMotCle!]&TitreListe=[!TitreListe!]!]
[!REQUETE:=Blog/Post/Actif=1!]
//[!REQUETE!]
// Mots cles
[IF [!RechercheMotCle!]!=Rechercher...&&[!RechercheMotCle!]!=]
	[!Re:=[!Utils::setSearch([!RechercheMotCle!])!]!]
	[!REQUETE+=&MotClef.PostId(Canon~[!Re!])!]
[/IF]
<div class="BlocPost">
	<h1>[!TitreListe!]</h1>
</div>	
[MODULE Blog/Post/Liste?Chemin=[!REQUETE!]]
