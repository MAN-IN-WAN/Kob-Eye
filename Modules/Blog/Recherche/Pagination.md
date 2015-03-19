// -- Gestion de la pagination
<div class="LignePagination">
	// Retour à la première page
	<a href="[!lelien!]" class="pagedebut" />Debut</a>
	// Page précédente
	<a href="[!lelien!][IF [!Enfant:-1!]<1][ELSE]&PagePos=[!Enfant:-1!][/IF]" class="pageprec" />Precedent</a>
	// Aller à une page précise
	// ...
	[IF [!Enfant!]>10]<span class="current ">...</span>[/IF]
	// Page n-1
	[IF [!Enfant!]>2]
		<a href="[!lelien!]&PagePos=[!Enfant:-1!]" [IF [!PageCourante!]=[!Enfant:-1!]] class="current"[/IF] >[!Enfant:-1!]</a>
	[/IF]
	// Page courante
	[IF [!Enfant!]>1]
		<span class="current ">[!Enfant!]</span>
	[/IF]
	// Page n+1
	[IF [!Enfant!]<[!TotalP:-1!]]
		<a href="[!lelien!]&PagePos=[!Enfant:+1!]" [IF [!PageCourante!]=[!Enfant:+1!]] class="current"[/IF] >[!Enfant:+1!]</a>
	[/IF]
	// ...
	[IF [!Enfant!]<[!TotalP:-2!]]...[/IF]
	// Page n-1
	// Dernière page
	[IF [!Enfant!]!=[!TotalP!]]
		<a href="[!lelien!]&PagePos=[!TotalP!]"  [IF [!PageCourante!]=[!TotalP!]] class="current"[/IF]>[!TotalP!]</a>
	[/IF]
	// Page suivante
	<a href="[!lelien!]&PagePos=[IF [!PageCourante:+1!]>[!TotalP!]][!TotalP!][ELSE][!PageCourante:+1!][/IF]" class="pagesuiv" >Suivant</a>
	// Dernière Page
	<a href="[!lelien!]&PagePos=[!TotalP!]" class="pagefin">Fin</a>
</div>