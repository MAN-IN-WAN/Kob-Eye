
<!-- page header -->
<h1 id="page-header">Les programmes</h1>	


<div class="fluid-container">
	[STORPROC ProgrammeMinceur/Programme|P|0|10|Ordre|ASC]
		<div class="well clearfix well-small">
			<img src="[IF [!P::Image!]][!P::Image!].mini.150x150.jpg[ELSE]/Skins/Minceur/Img/recette.png[/IF]" class="media pull-left" style="margin:10px;"/>
			<h4>[!P::Nom!]</h4>
			<p>[!P::Description!]</p>
			<div class="well pull-right">
			[STORPROC ProgrammeMinceur/Programme/[!P::Id!]/Fichiers|F|0|10|Nom|ASC]
				<div class="row">
				<a href="/[!F::Fichier!]" class="btn btn-primary" target="_blank">Télécharger - [!F::Nom!]</a>
				</div>
			[/STORPROC]
			</div>
		</div>
	[/STORPROC]	
</div>		
