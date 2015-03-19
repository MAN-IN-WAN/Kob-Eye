<div class="titreNews">&nbsp;</div>
<div class="contenuColonneCoeurNews">
	[STORPROC News/Nouvelle/Publier=1|Ne|0|4|tmsEdit|DESC]
		[IF [!Ne::Image!]!=]
			<div class="imgNews">
				<img src="/[!Ne::Image!].limit.50x50.jpg"/>
			</div>
		[/IF]
		<div class="dateNews">[DATE d.m.Y][!Ne::tmsCreate!][/DATE]</div>
		<div class="soustitreNews">[!Ne::Titre!]</div>
		<div class="texteNews">[SUBSTR 150| [...]][!Ne::Contenu!][/SUBSTR]</div>
		<div class="NewsLien"><a class="lienNews" href="/">Lire la suite</a></div>
	[/STORPROC]
	<div class="NewsLien"><a class="lienToutesNews" href="/">Voir toutes les news</a></div>
</div>
<div class="BasCoeurNews">&nbsp;</div>
