<!-- page header -->

<a class="btn btn-danger btn-large pull-right" id="mapesee">Ma pesée aujourd'hui</a>
<h1 id="page-header">Mon profil</h1>	
[STORPROC [!Systeme::User::getChildren(Profil)!]|P|0|1]
<div class="fluid-container">
	<div class="well">
		<div class="row-fluid">
			<div class="span3">
				Nom:
			</div>
			<div class="span3">
				<strong>[!P::Nom!] [!P::Prenom!]</strong>
			</div>
			<div class="span3">
				Taille (cm):<br/>
				Poids actuel (kg):
			</div>
			<div class="span3">
				<strong>[!P::Taille!]</strong> cm <br />
				<strong>[!P::PoidsActuel!]</strong> kg
			</div>
		</div>
	</div>
	<div class="row-fluid">
		[MODULE ProgrammeMinceur/Profil/Recapitulatif]
	</div>
</div>
	[NORESULT]
		//Si pas de profil alors redirection sur la formulaire
		[REDIRECT]MonProfil/Editer[/REDIRECT]
	[/NORESULT]
[/STORPROC]