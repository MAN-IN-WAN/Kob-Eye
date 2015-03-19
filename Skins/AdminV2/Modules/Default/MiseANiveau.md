[MODULE Systeme/Configuration/Top]

[LIB svnclient|svn]

[BLOC Panneau]
[IF [!Update!]!=]

	[!svn::update([!Dossiers!],[!Forcer!])!]
	[!Files:=[!svn::getFilesUpdated()!]!]
	
	<h1 style="text-align:center">Mise à niveau effectuée !<br /><img src="/Skins/[!Systeme::Skin!]/Img/ouf.jpg" /></h1>
	[STORPROC [!Files!]|F]
		<h2>Les fichiers suivants ont été mis à jour...</h2>
		[LIMIT 0|15000]
			[!F!]<br />
		[/LIMIT]
		[NORESULT]
			<h2>Aucun fichier n'a été mis à jour.</h2>
		[/NORESULT]
	[/STORPROC]

	

[ELSE]

	<form id="FormMiseANiveau" action="" style="text-align:center" method="post">
		<h1 style="text-align:center">Attention, la mise à niveau peu entrainer des perturbations dans<br />Kob-eye, êtes-vous sûr de vouloir continuer ?<br /></h1>
		<h2>( Cette opération peut prendre du temps )<br />&nbsp;</h2>
		<h3>Répertoires</h3>
		<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Class" /> Class</div>
		<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Conf" /> Conf</div>
		<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Templates" /> Templates</div>
		<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Tools" /> Tools</div>
		[STORPROC [!svn::getAll(Skins)!]|S]
			<h3>Skins</h3>
			<div style="padding-left:5px">
				[LIMIT 0|100]
					[IF [!S!]!=]
						<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Skins/[!S!]" /> [!S!]</div>
					[/IF]
				[/LIMIT]
			</div>
		[/STORPROC]
		[STORPROC [!svn::getAll(Modules)!]|S]
			<h3>Modules</h3>
			<div style="padding-left:5px">
				[LIMIT 0|100]
					[IF [!S!]!=]
						<div style="text-align:left; width:400px; margin:auto"><input type="checkbox" name="Dossiers[]" value="Modules/[!S!]" /> [!S!]</div>
					[/IF]
				[/LIMIT]
			</div>
		[/STORPROC]
		<h3>Options</h3>
		<div style="text-align:left; width:400px; margin:auto">
			Forcer l'écrasement des fichiers
			<input type="radio" checked="checked" name="Forcer" value="0" /> Non
			<input type="radio" name="Forcer" value="1" /> Oui
			<input type="hidden" name="Update" value="1" /> 
		</div>
		<br />
		<input type="submit" name="Confirm" style="width:400px" value="Oui, je veux mettre à niveau les éléments sélectionnés !" />
	</form>

[/IF]
[/BLOC]


[MODULE Systeme/Configuration/Bottom]