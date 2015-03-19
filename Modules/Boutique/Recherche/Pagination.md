// -- Gestion de la pagination
<div class="LignePagination">
	// Retour à la première page
	<a href="[!lelien!]" class="pagedebut" />Debut</a>
	// Page précédente
	<a href="[!lelien!][IF [!PageCourante:-1!]<1][ELSE]&PagePos=[!PageCourante:-1!][/IF]" class="pageprec" />Precedent</a>
	// Première page
	[IF [!PageCourante!]>2]
		<a href="[!lelien!]&PagePos=1">1</a>
	[/IF]
	// Aller à une page précise
	[IF [!PageCourante!]>3]...[/IF]
	// Page n-1
	[IF [!PageCourante!]>1]
		<a href="[!lelien!]&PagePos=[!PageCourante:-1!]" [IF [!PageCourante!]=[!PageCourante:-1!]] class="current"[/IF] >[!PageCourante:-1!]</a>
	[/IF]
	// Page courante
	<span class="current ">[!PageCourante!]</span>
	// Page n+1
	[IF [!PageCourante!]<[!TotalP:-1!]]
		<a href="[!lelien!]&PagePos=[!PageCourante:+1!]" [IF [!PageCourante!]=[!PageCourante:+1!]] class="current"[/IF] >[!PageCourante:+1!]</a>
	[/IF]
	[IF [!PageCourante!]<[!TotalP:-2!]]...[/IF]
	// Page n-1
	// Dernière page
	[IF [!PageCourante!]!=[!TotalP!]]
		<a href="[!lelien!]&PagePos=[!TotalP!]"  [IF [!PageCourante!]=[!TotalP!]] class="current"[/IF]>[!TotalP!]</a>
	[/IF]
	// Page suivante
	<a href="[!lelien!]&PagePos=[IF [!PageCourante:+1!]>[!TotalP!]][!TotalP!][ELSE][!PageCourante:+1!][/IF]" class="pagesuiv" >Suivant</a>
	// Dernière Page
	<a href="[!lelien!]&PagePos=[!TotalP!]" class="pagefin">Fin</a>
</div>