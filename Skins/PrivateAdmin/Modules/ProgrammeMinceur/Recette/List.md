//PARAMETRES
[IF [!Page!]=][!Page:=1!][/IF]
[COUNT ProgrammeMinceur/Recette|Nb]
[!NbParPage:=5!]
[!NbNumParPage:=3!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]

<!-- page header -->
<h1 id="page-header">Les recettes</h1>	


<div class="btn-toolbar blogpagging"> <!-- Start Paging --> 
	<div class="btn-group">
		<button class="btn active">Page 1 sur [!NbPage!] </button> 
		[IF [!Page!]>1]
			<a href="/[!Lien!]" class="btn"><span>&laquo;</span></a>
			<a href="[IF [!Page!]=2]/[!Lien!][ELSE]?Page=[!Page:-1!][/IF]" class="btn">&lsaquo;</a>
			[IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
				<a href="/[!Lien!]" class="btn"><span>1</span></a>
				<a href="#" class="btn"><span>...</span></a>
			[/IF]
		[/IF]
		[!start:=1!]
		[IF [!Page!]>[!start:+[!NbNumParPage:/2!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
		[STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
		<a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]" class="btn [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
		[/STORPROC]
		[IF [!Page!]<[!NbPage!]]
			[IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
				<a href="#" class="btn"><span>...</span></a>
				<a href="?Page=[!NbPage!]" class="btn">[!NbPage!]</a>
			[/IF]
			<a href="?Page=[!Page:+1!]" class="btn"><span>&rsaquo;</span></a>
			<a href="?Page=[!NbPage!]" class="btn">&raquo;</a>
		[/IF] 
	</div>
</div>	<!-- End Paging -->

<div class="fluid-container">
	[STORPROC ProgrammeMinceur/Recette|R|[![!Page:-1!]:*[!NbParPage!]!]|[!NbParPage!]|tmsCreate|DESC]
		<div class="well clearfix well-small">
			<img src="[IF [!R::Image!]][!R::Image!].mini.150x150.jpg[ELSE]/Skins/Minceur/Img/recette.png[/IF]" class="media pull-left" style="margin:10px;"/>
			<h4>[!R::Nom!]</h4>
			<p>[SUBSTR 200][!R::Description!][/SUBSTR]</p>
			<a href="/[!Systeme::CurrentMenu::Url!]/[!R::Url!]" class="btn btn-primary pull-right">Voir la recette</a>
		</div>
	[/STORPROC]	
</div>		

<div class="btn-toolbar blogpagging"> <!-- Start Paging --> 
	<div class="btn-group">
		<button class="btn active">Page 1 sur [!NbPage!] </button> 
		[IF [!Page!]>1]
			<a href="/[!Lien!]" class="btn"><span>&laquo;</span></a>
			<a href="[IF [!Page!]=2]/[!Lien!][ELSE]?Page=[!Page:-1!][/IF]" class="btn">&lsaquo;</a>
			[IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
				<a href="/[!Lien!]" class="btn"><span>1</span></a>
				<a href="#" class="btn"><span>...</span></a>
			[/IF]
		[/IF]
		[!start:=1!]
		[IF [!Page!]>[!start:+[!NbNumParPage:/2!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
		[STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
		<a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]" class="btn [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
		[/STORPROC]
		[IF [!Page!]<[!NbPage!]]
			[IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
				<a href="#" class="btn"><span>...</span></a>
				<a href="?Page=[!NbPage!]" class="btn">[!NbPage!]</a>
			[/IF]
			<a href="?Page=[!Page:+1!]" class="btn"><span>&rsaquo;</span></a>
			<a href="?Page=[!NbPage!]" class="btn">&raquo;</a>
		[/IF] 
	</div>
</div>	<!-- End Paging -->
