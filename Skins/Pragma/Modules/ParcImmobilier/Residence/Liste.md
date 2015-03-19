////////// Requete //////////
[!Req:=ParcImmobilier!]
[IF [!DepId!]!=0&&[!DepId!]!=]
	[!Req+=/Departement/[!DepId!]!]
[ELSE]
	[!Req+=/Departement/*!]
[/IF]

[IF [!VilleId!]!=0&&[!VilleId!]!=]
	[!Req+=/Ville/[!VilleId!]!]
[ELSE]
	[!Req+=/Ville/*!]
[/IF]
[!Req+=/Residence/Logement=1&&Reference=0!]

////////// Filtres Recherche //////////
[IF [!RR_Type!]!=||[!RR_Prix!]!=]
	[!Req+=/TypeLogement/!]
	[IF [!RR_Type!]!=]
		[!Req+=Titre~[!RR_Type!]!]
	[/IF]
	[IF [!RR_Type!]!=&&[!RR_Prix!]!=]
		[!Req+=&&!]
	[/IF]
	[IF [!RR_Prix!]!=]
		[IF [!RR_Prix!]=T1]
			[!Req+=PrixMin<=120000!]
		[/IF]
		[IF [!RR_Prix!]=T2]
			[!Req+=PrixMin<=160000!]
			[!Req+=(PrixMax>=120000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T3]
			[!Req+=PrixMin<=190000!]
			[!Req+=(PrixMax>=160000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T4]
			[!Req+=PrixMin<=260000!]
			[!Req+=(PrixMax>=190000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T5]
			[!Req+=PrixMin<=350000!]
			[!Req+=(PrixMax>=260000||PrixMax=-)!]
		[/IF]
		[IF [!RR_Prix!]=T6]
			[!Req+=PrixMin>=350000!]
		[/IF]
	[/IF]
	[!Selection:=distinct(j2.Id),j2.*!]
[/IF]

[!NbParPage:=6!]
[!Total:=0!]
[STORPROC [!Req!]|Cpt|0|1000|Ordre|DESC|[!Selection!]][!Total+=1!][/STORPROC]
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
[STORPROC [!Req!]|R|[!Start!]|[!NbParPage!]|Ordre|DESC|[!Selection!]]
	[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
	<div class="BlocResidence [IF [!Utils::isPair([!Pos!])!]] BRPair [ELSE] BRImpair [/IF]">
		<a class="bleu" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]"><img class="Icone" src="/[!R::Icone!].mini.105x75.jpg" width="105" height="75" alt="[!R::Titre!]" /></a>
		<h2><a class="bleu" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]">[!R::Titre!]</a></h2>
		<h3><span style="text-transform:uppercase">[!D::Nom!]</span> - [!V::Nom!]</h3>
		<div class="Livraison">Livraison [!R::DateLivraison!]</div>
		<div class="SousTitreCommercial">[!R::SousTitreCcial!]</div>
		<div class="Pictos">
			[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
				<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
			[/STORPROC]
		</div>
		<div class="ActionsResidence">
			<a class="VoirResidence bleu" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]">Voir la résidence</a>
			<a class="Simulateurs bleu" href="/Simulateurs">Simulateurs</a>
			<a class="Contact bleu" href="/[!Systeme::getMenu(Systeme/Contact)!]?C_Sujet=[URL]Résidence - [!R::Titre!][/URL]">Contact</a>
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
			<a class="PagiFirst" href="/[!Lien!][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF]">&nbsp;</a>
			<a class="PagiPrev" href="/[!Lien!][IF [!Prev!]>1]?Page=[!Prev!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]">&nbsp;</a>
			[STORPROC [!NbPages!]|P]
				[IF [!Pos!]=[!Page!]]<strong>[/IF]
				<a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
				[IF [!Pos!]=[!Page!]]</strong>[/IF]
			[/STORPROC]
			<a class="PagiNext" href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
			<a class="PagiLast" href="/[!Lien!]?Page=[!NbPages!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
		</div>
	</div>
[/IF]