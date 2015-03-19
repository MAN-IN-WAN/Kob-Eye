[STORPROC [!Query!]|FA][/STORPROC]
[TITLE]Admin Kob-Eye | Impression de facture [/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			<h1>Impression Facture [!FA::NumFac!]</h1>
			<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
				<a href="/Boutique/Facture/[!FA::Id!]/FacturePdf" rel="link"   >Imprimer la facture</a>
			</div>
		[/BLOC]
	</div>
</div>

