<!--System/Menu/Default-->
<ul class="menuhaut">
	<li><a href="/Navigation/_Comment-ca-marche/_Comment-Acheter" >Comment Acheter</a></li>
	<li><a href="/Navigation/_Comment-ca-marche/_Comment-Vendre" >Comment Vendre</a></li>
	[IF [!Systeme::User::Public!]!=1]
		<li ><a href="/Mon_Compte" class="elementmenu">Mon compte</a></li>
		<li style="border:none;padding-right:0px;"><a href="/Mon_Compte/Deconnexion" class="elementmenu">Se Deconnecter</a></li>
	[ELSE]
		<li style="border:none;padding-right:0px;"><a href="/Espace_Abonnes" class="elementmenu">Connexion</a></li>
	[/IF]

</ul>