[STORPROC Boutique/Magasin/Actif=1|Mag|0|1][/STORPROC]
[IF [!Systeme::User::Public!]=0]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 
[/IF]
// Utilisateur (Connecté ou non ?)
[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[NORESULT]
			[OBJ Boutique|Client|Cli]
		[/NORESULT]
	[/STORPROC]
[/IF]



<div class="EntetePied">
	<div class="Bandeau">
		<a style="position:absolute;top:0;left:0;right:0;bottom:0" href="/"></a>
		<div class="MenuHautR">
			<ul>
				<li style="background:none;">
					<a href="/ContactBlog" class="MiniContact">Contact</a>
				</li>
				<li>
					<a href="http://www.facebook.com/SableEtJasmin" class="Facebook">Facebook</a>
				</li>
				<li>
					<a href="http://www.twitter.fr/share?url=[!Domaine!]/[!Lien!]" class="Twitter">Twitter</a>
				</li>
				<li>
					<a href="/Envoyer-a-un-ami?C_Lien=[!Lien!]" class="SendFriend">SendFriend</a>
				</li>
				<li><a href="[!CONF::GENERAL::INFO::SITE_NAME!]" target="_blank" class="Boutique">La boutique</a>

				</li>
			</ul>
		</div>
		<div class="EnteteTel">[!Mag::Tel!]<div class="EnteteTelCout">Coût d'un appel local</div></div>
		<div class="MenuCentral">
			[MODULE Systeme/Menu/MenuBlog]

		</div>
	</div>	
</div>

