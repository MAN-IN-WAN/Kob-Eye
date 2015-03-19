<div class=" TitreReferentiel" style="overflow:hidden;">
	<h1 >Référentiel des examens biologiques</h1>
</div>

<p>
	Mis à jour en temps réél, le référentiel des examens de biologie médicale vous permet d'accéder
	directement aux informations relatives à toutes les analyses proposées par le laboratoire :
	pré-analytique, technique de dosage, intérêt clinique, cotation.
</p>

[STORPROC [!Query!]|A|0|1|tmsEdit|DESC]
	<p class="LastUpdateDB">Dernière mise à jour : [UTIL FULLDATEFR][!A::tmsEdit!][/UTIL]</p>
[/STORPROC]

<form action="/[!Lien!]" method="get" id="AnalyseSearch">
	<div class="LigneForm">
		<input style="display:block; position:relative; float:left; margin:4px 4px 0 0" type="text" id="A_MotCle" name="MotCle" value="[!MotCle!]" />
		<button type="submit" class="RechercherBtn">Rechercher</button>
	</div>
	<div class="LigneForm">
		<strong>ou</strong>
	</div>
	<div class="LigneForm">
		<select name="Specialite" id="A_Specialites" style="display:block; position:relative; float:left; margin:4px 4px 0 0">
			<option value="">- Veuillez sélectionner -</option>
			[STORPROC Unibio/Specialite|S]
				<option [IF [!Specialite!]=[!S::Id!]] selected="selected" [/IF] value="[!S::Id!]">[!S::Nom!]</option>
			[/STORPROC]
		</select>
		<button type="submit" class="RechercherBtn">Rechercher</button>
	</div>
</form>

<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText($('A_MotCle'), "Tapez un mot clé", $('AnalyseSearch'));
	});
</script>

[!Requete:=!]
[!Requete+=[!Query!]/!]
[IF [!Specialite!]!=][!Requete+=Specialite=[!Specialite!]!][/IF]
[IF [!MotCle!]!=&&[!Specialite!]!=][!Requete+=&&!][/IF]
[IF [!MotCle!]][!Requete+=MotClef.AnalyseId(Canon~[!Utils::Canonic([!MotCle!])!])!][/IF]
//[!Requete!]
// Données Pagination
[!Limit:=10!]
[COUNT [!Requete!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!Start:=[!Page:-1!]!][!Start*=[!Limit!]!]
[!NbPages:=[!Total:/[!Limit!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]


<div class="Resultats">
	[OBJ Unibio|Analyse|A]
	<div class="ResultatsTitre">
		Résultats de votre recherche :
		<span class="blue">
			[!MotCle!][IF [!MotCle!]!=&&[!Specialite!]!=],[/IF]
			[IF [!Specialite!]!=]
				[STORPROC Unibio/Specialite/[!Specialite!]|Spe]
					[!Spe::Nom!]
				[/STORPROC]
			[/IF]
		</span>
	</div>
	[STORPROC [!Requete!]|A|[!Start!]|[!Limit!]|Examen|ASC]
		<ul>
			[LIMIT 0|[!Limit!]]
				<li class="[IF [!Utils::isPair([!Key!])!]]Pair[/IF]">
					<span class="Puce"></span>
					<a href="/[!Lien!]/[!A::Url!]">[!A::Examen!]</a>
				</li>
			[/LIMIT]
		</ul>
		[NORESULT]
			Aucun résultat
		[/NORESULT]
	[/STORPROC]
</div>

// Pagination
[IF [!NbPages!]>1]
	<div class="Pagination">
		<span class="PaginationPages PaginationPagesEP">
			<a class="FirstPage" href="/[!Lien!]?MotCle=[!MotCle!]&amp;Specialite=[!Specialite!]"></a>
			<a class="PreviousPage" href="/[!Lien!]?MotCle=[!MotCle!]&amp;Specialite=[!Specialite!][IF [!Page:-1!]>1]&amp;Page=[!Page:-1!][/IF]"></a>
			[STORPROC [!NbPages!]|P]
				<a href="/[!Lien!]?MotCle=[!MotCle!]&amp;Specialite=[!Specialite!][IF [!Pos!]>1]&Page=[!Pos!][/IF]" class="Page [IF [!Pos!]=[!Page!]]currentPage[/IF]">[!Pos!]</a>[IF [!Pos!]!=[!NbResult!]][/IF]
			[/STORPROC]
			<a class="NextPage" href="/[!Lien!]?MotCle=[!MotCle!]&amp;Specialite=[!Specialite!]&amp;Page=[IF [!Page:+1!]>[!NbPages!]][!NbPages!][ELSE][!Page:+1!][/IF]"></a>
			<a class="LastPage" href="/[!Lien!]?MotCle=[!MotCle!]&amp;Specialite=[!Specialite!]&amp;Page=[!NbPages!]"></a>
		</span>
	</div>
[/IF]
