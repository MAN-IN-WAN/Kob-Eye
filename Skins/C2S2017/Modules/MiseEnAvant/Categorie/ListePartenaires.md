[!Cpt:=0!]
<div CLASS="PartenairesBottom">
	[STORPROC MiseEnAvant/Categorie/Partenaires|CatInf]
		[STORPROC MiseEnAvant/Categorie/[!CatInf::Id!]/InfoEnAvant/Publier=1|Inf]
			[IF [!Cpt!]=0]<div class="row" style="margin-bottom:10px;">[/IF]
			<div class="col-md-4 col-xs-4" >
				<img src="[!Domaine!]/[!Inf::Image!]" alt="[!Inf::Titre!]" class="img-responsive" style="height:30px;">
			</div>
			[!Cpt+=1!]
			[IF [!Cpt!]=3]</div>[!Cpt:=0!][/IF]
		[/STORPROC]
	[/STORPROC]

</div>
