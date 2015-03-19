[MODULE Systeme/Structure/CouleurUnivers]
<!--- colonne de gauche + le contenu -->
<div class="colonneGauche">
	[IF [!Systeme::User::Public!]!=1]
		[MODULE Boutique/Client/Menu]
	[/IF]
	[MODULE Redaction/Navigation]
//	[MODULE Publicite/PubColonne]
	[MODULE Boutique/Interface/DernierAvis]
</div> <!-- fin colonne gauche-->