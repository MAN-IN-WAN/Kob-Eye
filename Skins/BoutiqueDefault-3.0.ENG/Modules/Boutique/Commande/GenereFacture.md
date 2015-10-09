// génération d'une facture
// à partir de la commande
[STORPROC [!Query!]|CDE][/STORPROC]


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
				<h1>Génération de la facture pour la Commande [!CDE::RefCommande!]</h1>
				[COUNT [!Query!]/Facture|NF]
				[IF [!NF!]>0] 
					[STORPROC [!Query!]/Facture|Fac][/STORPROC]
					Facture déjà générée : [!Fac::NumFac!]
				[ELSE]
					[!CDE::GenereFacture()!]
					[STORPROC [!Query!]/Facture|Fac]
						Facture générée : [!Fac::NumFac!]
					[/STORPROC]
				[/IF]
			[/BLOC]
		</div>
	</form>
</div>
