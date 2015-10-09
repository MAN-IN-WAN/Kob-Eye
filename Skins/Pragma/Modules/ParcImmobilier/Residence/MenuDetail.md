<ul class="lvl1">
	<li class="lvl1 [IF [!Voir!]=] current[/IF] ">
		<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!][IF [!Affichage!]=Client]?Affichage=Client[/IF]">Détails de la résidence</a>
		<ul class="lvl2"><li style="display:none"></li></ul>
	</li>
	//<li class="lvl1 [IF [!Voir!]=Plaquette] current [/IF]">
	//	<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]?Voir=Plaquette[IF [!Affichage!]=Client]&Affichage=Client[/IF]">Consulter la plaquette</a>
	//	<ul class="lvl2"><li style="display:none"></li></ul>
	//</li>
	<li class="lvl1">
		<a class="lvl1" href="[!Domaine!]/[!R::Doc!]" rel="link" target="_blank">Télécharger la plaquette</a>
		<ul class="lvl2"><li style="display:none"></li></ul>
	</li>
	<li class="lvl1 [IF [!Voir!]=Localiser] current [/IF]">
		<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]?Voir=Localiser[IF [!Affichage!]=Client]&Affichage=Client[/IF]">Localiser sur la carte</a>
		<ul class="lvl2"><li style="display:none"></li></ul>
	</li>
</ul>

<br />

<div class="ActionsResidence">
	<a class="Contact" href="/Contact?C_Sujet=[URL]Résidence - [!R::Titre!][/URL]">Contactez-nous</a>
	<a class="EnvoyerAmi" href="/Envoyer-Ami?C_Adresse=[URL][!Domaine!]/[!Lien!][/URL]">Envoyer à un ami</a>
	<a class="Simulateurs" href="/Simulateurs">Simulateurs</a>
</div>