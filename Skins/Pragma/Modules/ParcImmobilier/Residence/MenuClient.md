////////// Requete //////////
[!Req:=ParcImmobilier!]
[!Req+=/Residence/Client=1!]
////////// Liste //////////
<ul class="lvl1">
	[STORPROC [!Req!]|R|||tmsEdit|DESC]
		[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
		[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
		<li class="lvl1 [IF [!Voir!]!=] current[/IF] ">
			<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]?Affichage=Client">[!R::Titre!]</a>
			<ul class="lvl2"><li style="display:none"></li></ul>
		</li>
	[/STORPROC]
</ul>
