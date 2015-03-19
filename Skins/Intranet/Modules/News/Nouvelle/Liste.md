// Données Pagination
[!Req:=News/Nouvelle!]
[!Limit:=4!]
[COUNT [!Req!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!Start:=[!Page:-1!]!][!Start*=4!]
[!NbPages:=[!Total:/[!Limit!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]
<div class="ListeNews">
	<div class=" TitreActus" style="overflow:hidden;padding-bottom: 20px;">
		<h1>Nos actualités</h1>
	</div>
	[!I:=0!]
	[STORPROC [!Req!]|N|[!Start!]|[!Limit!]|Id|DESC]
		<div class="News [IF [!I!]=1]Pair[/IF]">
			<div class="ContenuNews">
				[IF [!N::Image!]!=]
					<div class="ImageNews">
						<img src="/[!N::Image!].limit.150x90.jpg" alt="[!N::Titre!]" />
					</div>
				[/IF]
				<div style="display:block;" id="articlereduit[!N::Id!]">
					<div class="Titre">Le [DATE d/m/Y][!N::tmsCreate!][/DATE]</div>
					<h2>[!N::Titre!]</h2>
					<p class="Chapo">[!N::Chapo!]</p>

					<p>[SUBSTR 300][!N::Contenu!][/SUBSTR]</p>
					<a class="lirearticle" href="/[!Systeme::getMenu(News/Nouvelle)!]/[!N::Url!]"  >Lire cette actu</a>
				</div>
				<div style="display:none;" id="articlecomplet[!N::Id!]">
					<p>[!N::Contenu!]</p>
				</div>
			</div>
		</div>
		[IF [!I!]=0][!I:=1!][ELSE][!I:=0!][/IF]
	[/STORPROC]
</div>
// Pagination
[IF [!NbPages!]>1]
	<div class="Pagination">
		<span class="PaginationPages">
			<a class="FirstPage" href="/[!Systeme::CurrentMenu::Url!]"></a>
			<a class="PreviousPage" href="/[!Systeme::CurrentMenu::Url!][IF [!Page:-1!]>1]?Page=[!Page:-1!][/IF]"></a>
			[STORPROC [!NbPages!]|P]
				<a href="/[!Systeme::CurrentMenu::Url!][IF [!Pos!]>1]?Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]]class="CurrentPage"[/IF]>[!Pos!]</a>[IF [!Pos!]!=[!NbResult!]][/IF]
			[/STORPROC]


			<a class="NextPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[IF [!Page:+1!]>[!NbPages!]][!NbPages!][ELSE][!Page:+1!][/IF]"></a>
			<a class="LastPage" href="/[!Systeme::CurrentMenu::Url!]?Page=[!NbPages!]"></a>
		</span>
	</div>
[/IF]

<div class="HautdePage"><a href="/[!Lien!]" >Haut de page</a></div>
<script type="text/javascript">

	function MasqueBlock($Id) {
		
		$('articlecomplet'+$Id).setStyle('display','block');
		$('articlecomplet'+$Id).setStyle('margin-bottom','20px');
		$('articlereduit'+$Id).setStyle('display','none');
	}

</script>