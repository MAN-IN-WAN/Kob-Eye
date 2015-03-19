<div id="Menu">
	<ul>
		[STORPROC Systeme/Group/3/User/120/Menu|Men]
			[IF [!Men::Affiche!]]
				<li>
					<a href="/[!Men::Url!]" title="[!Men::Titre!]" onfocus="this.blur()" class="[IF [!Lien!]~[!Men::Url!]]Actif[/IF]">[!Men::Titre!]</a>
				</li>
			[/IF]
		[/STORPROC]
		<li style="border:none;">
			<a href="/Blog/Post/Rss.xml" title="S'abonner au flux RSS">RSS</a>
		</li></ul>
	<div class="Clear"></div>
</div>