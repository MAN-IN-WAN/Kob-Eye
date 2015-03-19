[MODULE Systeme/Structure/CouleurUnivers]
<div class="colonneGauche">
	[MODULE Boutique/Interface/Categorie]
	[IF [!Systeme::User::Public!]!=1]
		[MODULE Boutique/Client/Menu]
	[/IF]
	[MODULE Redaction/Navigation]
</div> <!-- fin colonne gauche-->
