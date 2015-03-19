[!Requete:=ParcImmobilier/Residence!]
[IF [!CHAMP!]!=&&[!VALEUR!]!=&&]
	[!Requete+=/[!CHAMP!]=!]
	[!Requete+=[!VALEUR!]!]
[ELSE]
	[!Requete:=ParcImmobilier/Residence/Client=1!]
	
[/IF]
<div class="[!NOMDIV!]" >
	<h2>[!TITRE!]</h2>
	<ul class="Navigation" >
		[STORPROC [!Requete!]|R|||tmscreate|DESC]
			
			[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V]
				[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D]
					[!Url:=ParcourirOffre/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]!]
					[!UrlV:=ParcourirOffre/Departement/[!D::Lien!]/Ville/[!V::Lien!]!]
				[/STORPROC]
			[/STORPROC]
			<li>
				<div class="lienResidence"><a href="/[!Url!]?Affichage=Client&TITRE=[!TITRE!]">[!R::Titre!]</a></div>
			</li>
		[/STORPROC]
	</ul>

</div>




	
