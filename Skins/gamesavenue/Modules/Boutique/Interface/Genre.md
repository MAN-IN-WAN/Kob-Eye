[STORPROC [!O::Module!]/[!O::ObjectType!]/[!O::Id!]/Genre|G|0|100|Nom|ASC]
	[LIMIT 0|100]
		[!NbProd:=[!G::getNbProduitConsole([!MenuDemande!])!]!]
		[IF [!NbProd!]>0]

			<div class="p10 blocambiance_puce">
				[!Selected:=0!]
				[STORPROC [!I::Historique!]|H|1|10]
					[IF [!G::Url!]=[!H::Value!]][!Selected:=1!][/IF]
				[/STORPROC]
				<a  style="text-decoration:none;padding-left:10px;[IF [!Selected!]]font-weight:bold;[/IF]" href="[!U!]/[!G::Url!]">[!G::Nom!]&nbsp;([!NbProd!])</a>
				[IF [!Selected!]]
					[MODULE Boutique/Interface/Genre?O=[!G!]&U=[!U!]/[!G::Url!]]
				[/IF]
			</div>
		[/IF]
	[/LIMIT]
[/STORPROC]
