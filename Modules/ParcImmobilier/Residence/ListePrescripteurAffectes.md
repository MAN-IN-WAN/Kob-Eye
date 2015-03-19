[IF [!Systeme::User::Public!]]
	[REDIRECT]/[/REDIRECT]
[ELSE]
	[STORPROC [!Query!]|RS|0|1][/STORPROC]

	[TITLE]Admin Kob-Eye | Impression documents de la commande[/TITLE]
	[MODULE Systeme/Interfaces/FilAriane]
	<div id="Container">
		<form action="" method="post" name="rech[!Test::TypeChild!]" class="FormRech">
			<div id="Arbo">
				[BLOC Panneau]
				[/BLOC]
			</div>
			<div id="Data">
				[BLOC Panneau]
	
		<a href="/ParcImmobilier/Residence/[!RS::Id!]/PdfListePrescripteurAffectes"  rel="link"  >Voir la liste des prescripteur lié à [!Rs::Nom!]</a>
					
				[/BLOC]
			</div>
		</form>
	</div>
[/IF]