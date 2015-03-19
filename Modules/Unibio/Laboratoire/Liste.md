[IF [!Domaine!]~unibio.fr]
	[!FiltrePublic:=1!]
[/IF]
// pour filtrer en fonction de l'entité à afficher Unibio ou Biomed
[!FiltreEntite:=/Entite=Unibio!]
[IF [!Domaine!]~biomed][!FiltreEntite:=/Entite=Biomed!][/IF]
[IF [!Domaine!]~intranet][!FiltreEntite:=!][/IF]
<div class="UnibioLabo">
	<div class=" TitreLabo" style="overflow:hidden;">
		<h1>Rechercher votre laboratoire</h1>
	</div>
	<p></p>
	<form action="/[!Lien!]" method="get">
		<div class="LigneForm">
			<select name="Zone">
				<option value="">- Veuillez sélectionner -</option>
				[!Req:=!]
				[IF [!FiltrePublic!]][!Req+=Unibio/Region/Public=1!][ELSE][!Req+=Unibio/Region!][/IF]
				[STORPROC [!Req!]|R]
					[COUNT Unibio/Region/[!R::Url!]/Laboratoire[!FiltreEntite!]|NbLabo]
					[IF [!NbLabo!]]<option [IF [!Zone!]=[!R::Url!]] selected="selected" [/IF] value="[!R::Url!]">[!R::Nom!]</option>[/IF]
				[/STORPROC]
			</select>
			<button type="submit" class="RechercherBtn" style="float:none;margin:0;">Rechercher</button>
		</div>
	</form>
	
	[!Requete:=!]
	[IF [!Zone!]]
		[!Requete+=Unibio/Region/[!Zone!]/Laboratoire[!FiltreEntite!]!]
	[ELSE]
		[IF [!FiltrePublic!]]
			[!Requete+=Unibio/Region/Public=1/Laboratoire[!FiltreEntite!]!]
		[ELSE]
			[!Requete+=Unibio/Region/*/Laboratoire[!FiltreEntite!]!]
		[/IF]
	[/IF]
</div>
 
[!Limit:=8!]
[COUNT [!Requete!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]

[!Start:=[!Page:-1!]!][!Start*=8!]
[!NbPages:=[!Total:/[!Limit!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]

<div class="UnibioLesLabo">
	<h2>
		[IF [!Zone!]=]
			Les laboratoires
		[ELSE]
			[STORPROC Unibio/Region/[!Zone!]|Z]
				Les laboratoires de [!Z::Nom!]
			[/STORPROC]
		[/IF]
	</h2>
</div>

[IF [!NbPages!]>1]
	<div class="Pagination">
		<span class="PaginationPagesWhite">
			<a class="FirstPage" href="/[!Systeme::CurrentMenu::Url!][IF [!Zone!]]?Zone=[!Zone!][/IF]"></a>
			<a class="PreviousPage" href="/[!Systeme::CurrentMenu::Url!][IF [!Page:-1!]>1]?Page=[!Page:-1!][IF [!Zone!]]&Zone=[!Zone!][/IF][ELSE][IF [!Zone!]]?Zone=[!Zone!][/IF][/IF]"></a>
			[STORPROC [!NbPages!]|P]
				<a href="/[!Systeme::CurrentMenu::Url!][IF [!Pos!]>1]?Page=[!Pos!][IF [!Zone!]]&Zone=[!Zone!][/IF][ELSE][IF [!Zone!]]?Zone=[!Zone!][/IF][/IF]" [IF [!Pos!]=[!Page!]]class="CurrentPage"[/IF]>[!Pos!]</a>[IF [!Pos!]!=[!NbResult!]][/IF]
			[/STORPROC]
			<a class="NextPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[IF [!Page:+1!]>[!NbPages!]][!NbPages!][ELSE][!Page:+1!][/IF][IF [!Zone!]]&Zone=[!Zone!][/IF]"></a>
			<a class="LastPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[!NbPages!][IF [!Zone!]]&Zone=[!Zone!][/IF]"></a>
		</span>
	</div>
[/IF]

[STORPROC [!Requete!]|Lab|[!Start!]|[!Limit!]|Nom|ASC]
	

	<div class="LaboItem [IF [!Utils::isPair([!Key!])!]] LaboItemPair [/IF]">
		<div class="LaboItemLeft">
			<div class="LaboNom">
				<a href="/[!Lien!]/[!Lab::Url!]">[!Lab::Nom!]</a>
			</div>
			<div class="LaboPersonnes">
				[STORPROC [!Query!]/[!Lab::Id!]/Professionel|Pro]
					<div class="LaboPers"><div class="LaboPersCivilite">[!Pro::Nom!] [!Pro::Prenom!] </div> <div class="LaboPersProfession">&nbsp;- [!Pro::Profession!]</div></div>
				[/STORPROC]
			</div>
		</div>
		<div class="LaboItemRight">
			<div class="LaboItemRightTop">
				<div class="LaboAdresse"><strong>ADRESSE : </strong>[!Lab::Adresse!]</div>
				<div class="LaboNums"><strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]</div>
			</div>
			<div class="LaboItemRightBottom">
				<div class="LaboHoraires">[!Lab::Horaires!]</div>
				<div class="LaboMoreLinks">
					<a class="LaboPlusInfos" href="/[!Lien!]/[!Lab::Url!]">Plus d'infos</a>
					<div class="LaboMoreLinksPipe">|</div>
					<a class="LaboPlanAcces" href="http://maps.google.fr/maps?f=q&hl=fr&q=[IF [!Lab::GPS!]!=][!Lab::GPS!][ELSE][URL][!Lab::Adresse!][/URL][/IF]" target="_blank">Plan d'accès</a>
				</div>
			</div>
		</div>
	</div>
[/STORPROC]
[IF [!NbPages!]>1]
	<div class="Pagination">
		<span class="PaginationPagesBlue">
			<a class="FirstPage" href="/[!Systeme::CurrentMenu::Url!][IF [!Zone!]]?Zone=[!Zone!][/IF]"></a>
			<a class="PreviousPage" href="/[!Systeme::CurrentMenu::Url!][IF [!Page:-1!]>1]?Page=[!Page:-1!][IF [!Zone!]]&Zone=[!Zone!][/IF][ELSE][IF [!Zone!]]?Zone=[!Zone!][/IF][/IF]"></a>
			[STORPROC [!NbPages!]|P]
				<a href="/[!Systeme::CurrentMenu::Url!][IF [!Pos!]>1]?Page=[!Pos!][IF [!Zone!]]&Zone=[!Zone!][/IF][ELSE][IF [!Zone!]]?Zone=[!Zone!][/IF][/IF]" [IF [!Pos!]=[!Page!]]class="CurrentPage"[/IF]>[!Pos!]</a>[IF [!Pos!]!=[!NbResult!]][/IF]
			[/STORPROC]
			<a class="NextPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[IF [!Page:+1!]>[!NbPages!]][!NbPages!][ELSE][!Page:+1!][/IF][IF [!Zone!]]&Zone=[!Zone!][/IF]"></a>
			<a class="LastPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[!NbPages!][IF [!Zone!]]&Zone=[!Zone!][/IF]"></a>
		</span>
	</div>
[/IF]
<div class="HautdePage"><a href="#top" >Haut de page</a></div>
