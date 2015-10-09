////////// Requete //////////
[!Req:=ParcImmobilier!]
[IF [!DepId!]!=0&&[!VilleId!]!=]
	[!Req+=/Departement/[!DepId!]!]
[ELSE]
	[!Req+=/Departement/*!]
[/IF]
[IF [!VilleId!]!=0&&[!VilleId!]!=]
	[!Req+=/Ville/[!VilleId!]!]
[ELSE]
	[!Req+=/Ville/*!]
[/IF]
[!Req+=/Residence/Client=1!]

////////// Calcul Pagination //////////
[!NbParPage:=6!]
[COUNT [!Req!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!IdxPage:=[!Page:-1!]!]
[!Start:=[!IdxPage:*[!NbParPage!]!]!]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]
[!Prev:=[!Page:-1!]!]
[IF [!Prev!]<1][!Prev:=1!][/IF]
[!Next:=[!Page:+1!]!]
[IF [!Next!]>[!NbPages!]][!Next:=[!NbPages!]!][/IF]



////////// Liste //////////
[STORPROC [!Req!]|R|[!Start!]|[!NbParPage!]|tmsEdit|DESC|[!Selection!]]
	[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
	<div class="BlocResidence [IF [!Utils::isPair([!Pos!])!]] BRPair [ELSE] BRImpair [/IF] " id="ListeResidenceBordered">
		<a class="VoirResidence bleu" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]?Affichage=Client"><img class="Icone" src="/[!R::Icone!].mini.105x75.jpg" width="105" height="75" alt="[!R::Titre!]" />
		<h2>[!R::Titre!]</h2></a>
		<h3><span style="text-transform:uppercase">[!D::Nom!]</span> - [!V::Nom!]</h3>
		<div class="Livraison">Livraison [!R::DateLivraison!]</div>
		<div class="SousTitreCommercial">[!R::SousTitreCcial!]</div>
		<div class="Pictos">
			[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
				<img src="/[!PR::Picto!]"  alt="[!PR::Titre!]" title="[!PR::Titre!]" />
			[/STORPROC]
		</div>
		<div class="ActionsResidence">
			<a class="VoirResidence bleu" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]?Affichage=Client">Voir la résidence</a>
			<a class="Simulateurs bleu" href="/Simulateurs">Simulateurs</a>
			<a class="Contact bleu" href="/Contact?C_Sujet=[URL]Résidence - [!R::Titre!][/URL]">Contact</a>
			<a class="EnvoyerAmi bleu" href="/Envoyer-Ami?C_Adresse=[URL][!Domaine!]/ParcImmobilier/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!][/URL]">Envoyer à un ami</a>
		</div>
	</div>
	[NORESULT]
		<div class="p10">Aucun résultat pour votre recherche...</div>
	[/NORESULT]
[/STORPROC]
////////// Affichage Pagination //////////
[IF [!NbPages!]>1]
	<div class="Pagination">
		<div class="PaginationBody">
			<a class="PagiFirst" href="/[!Lien!]">&nbsp;</a>
			<a class="PagiPrev" href="/[!Lien!][IF [!Prev!]>1]?Page=[!Prev!][/IF]">&nbsp;</a>
			[STORPROC [!NbPages!]|P]
				[IF [!Pos!]=[!Page!]]<strong>[/IF]
				<a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
				[IF [!Pos!]=[!Page!]]</strong>[/IF]
			[/STORPROC]
			<a class="PagiNext" href="/[!Lien!]?Page=[!Next!]">&nbsp;</a>
			<a class="PagiLast" href="/[!Lien!]?Page=[!NbPages!]">&nbsp;</a>
		</div>
	</div>
[/IF]

