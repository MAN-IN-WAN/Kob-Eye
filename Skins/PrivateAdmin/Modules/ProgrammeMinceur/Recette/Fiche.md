
[STORPROC [!Query!]|R|0|1]
<!-- page header -->
<h1 id="page-header">[!R::Nom!]</h1>	


<div class="fluid-container">
		<div class="well clearfix well-small">
			<div class="row-fluid">
				<div class="span6">
					<h2>La recette</h2>
					<img src="[IF [!R::Image!]][!R::Image!].mini.150x150.jpg[ELSE]/Skins/Minceur/Img/recette.png[/IF]" class="media pull-left" style="margin:10px;"/>
					<p>[!R::Description!]</p>
				</div>
				<div class="span6">
					<h2>Les ingrédients</h2>
					<ul>
					[STORPROC [!Query!]/Ingredient|Ig]
						<li>[!Ig::Nom!]</li>
					[/STORPROC]
					</ul>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span4"></div>
				<div class="span3">
					<a href="javascript:alert('ajouter à la selection');" class="btn btn-info pull-right btn-block">Ajouter à ma sélection</a>
				</div>
				<div class="span3">
					<a href="javascript:window.print();" class="btn btn-primary pull-right btn-block">Imprimer la recette</a>
				</div>
				<div class="span2">
					<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-danger pull-right btn-block">Retour à la liste</a>
				</div>
			</div>
		</div>
</div>		
[/STORPROC]	
