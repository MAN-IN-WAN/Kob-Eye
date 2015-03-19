

<div class="item-rating"></div>
<!--
	<div class="row-fluid">
		<div class="span3" style="text-align:center;">
			<img src="/Skins/Minceur/Img/Courbe.png" />
			<h4>Enregistrez vos données</h4>
		</div>
		<div class="span3" style="text-align:center;">
			<img src="/Skins/Minceur/Img/Calendrier.png" />
			<h4>Définissez votre programme</h4>
		</div>
		<div class="span3" style="text-align:center;">
			<img src="/Skins/Minceur/Img/Balance.png" />
			<h4>Suivez vos progrès minceur</h4>
		</div>
		<div class="span3" style="text-align:center;">
			<img src="/Skins/Minceur/Img/CheckList.png" />
			<h4>Téléchargez vos listes de courses</h4>
		</div>
	</div>
-->
	[STORPROC [!Query!]|C|0|1]
		<div class="row-fluid">
			[STORPROC Redaction/Categorie/[!C::Id!]/Article|A|0|10|Ordre|ASC]
				[LIMIT 0|1]
				<div class="span6 well">
					[IF [!A::AfficheTitre!]]<h2>[!A::Titre!]</h2>[/IF]
					[STORPROC Redaction/Article/[!A::Id!]/Image|I]
						<img src="/[!I::URL!].mini.400x200.jpg">
					[/STORPROC]
					<p>[!A::Contenu!]</p>
				</div>
				[/LIMIT]
				[LIMIT 1|1]
				<div class="span6 well">
					[IF [!A::AfficheTitre!]]<h2>[!A::Titre!]</h2>[/IF]
					[STORPROC Redaction/Article/[!A::Id!]/Image|I]
						<img src="/[!I::URL!].mini.400x200.jpg">
					[/STORPROC]
					<p>[!A::Contenu!]</p>
				</div>
				[/LIMIT]
			[/STORPROC]
		</div>
		<div class="row-fluid" style="margin-top:10px;">
			<div class=" span12"> 
				<h1>[!C::Titre!]</h1>
					<p>
					[IF [!C::Icone!]]
						<img src="/[!C::Icone!]" class="pull-left"/>
					[/IF]
					[!C::Description!]
					</p>
			</div>
		</div>
	[/STORPROC]
<div class="item-separator"></div>
