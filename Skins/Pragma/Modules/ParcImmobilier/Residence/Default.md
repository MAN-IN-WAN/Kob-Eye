<br />
[STORPROC [!Query!]|R][/STORPROC]
[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
[MODULE ParcImmobilier/Residence/Anim]
[!LEH1:=[!R::Titre!] - [!V::Nom!] - [!R::Chapo!]!]
<h1 style="margin-top:20px">[SUBSTR 135][!LEH1!][/SUBSTR]</h1>

<div style="overflow:hidden; margin-top:20px">
	<div id="MenuResidence" class="arial">
		[MODULE ParcImmobilier/Residence/MenuDetail]
	</div>
	<div id="ContenuResidence" class="[!Voir!] arial">
		[IF [!Voir!]=Plaquette]
			[MODULE ParcImmobilier/Residence/Plaquette]
		[ELSE]
			[IF [!Voir!]=Localiser]
				[MODULE ParcImmobilier/Residence/Localiser]
			[ELSE]
				[MODULE ParcImmobilier/Residence/Detail]
			[/IF]
		[/IF]
	</div>
</div>
<div id="FooterResidence">
	[IF [!R::EspaceVente!]!=&&[!R::ContactEspaceVente!]>0]
		<div id="EspaceVente" class="bleu">
			<span style="text-transform:uppercase">ESPACE DE VENTE</span> - [!R::EspaceVente!]<br />
			[STORPROC Systeme/User/[!R::ContactEspaceVente!]|U][/STORPROC]
			<a href="/Contact?C_User=[!U::Id!]&C_Residence=[!R::Id!]" class="bleu" style="text-decoration:underline">Votre contact : [!U::Prenom!] <span style="text-transform:uppercase">[!U::Nom!]</span></a>
		</div>
	[/IF]
	[IF [!Affichage!]=Client]
		<a class="Print" href="/[!Lien!].print?Affichage=Client">Imprimer cette page</a>
		<a class="RetourRecherche" href="/Espace-Client/_residence-client">Retour à la liste</a>
	[ELSE]
		<a class="Print" href="/[!Lien!].print[IF [!Voir!]!=]?Voir=[!Voir!][/IF]">Imprimer cette page</a>
		<a class="RetourRecherche" href="/[!Systeme::getMenu(ParcImmobilier)!]">Retour à la liste</a>
	[/IF]
</div>