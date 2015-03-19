[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	<h1>Plan du site</h1>
	[STORPROC Systeme/Group/1/Menu/Affiche=1|Test|0|20|Ordre|ASC]
		<div class="Decale">
			<span>&nbsp;&nbsp;</span>
			[COUNT Systeme/Group/1/Menu/[!Test::Id!]/Menu|NbTest2]
			[IF [!NbTest2!]>0]
				<p>[!Test::Titre!]</p>
			[ELSE]
				<a href="/[!Test::Url!]" title="[!Test::Titre!]">[!Test::Titre!]</a>
			[/IF]
			[STORPROC Systeme/Menu/[!Test::Id!]/Menu/Affiche=1|Test2|0|10|Ordre|ASC]
				<div class="Decale">
					<span>&nbsp;&nbsp;</span>
					[IF [!Test2::Lien!]=""]
						<a href="#" title="[!Test2::Titre!]" [!SelectMenu2!] onFocus="this.blur()" style="cursor:default;">&nbsp;&nbsp;[!Test2::Titre!]</a>
					[ELSE]
						<a href="/[!Test::Url!]/[!Test2::Url!]" title="[!Test2::Titre!]" style="cursor:pointer;">[!Test2::Titre!]</a>
					[/IF]
					[STORPROC Systeme/Menu/[!Test2::Id!]/Menu/Affiche=1|Test3|0|10|Ordre|ASC]
						<div class="Decale">
							<span>&nbsp;&nbsp;</span>
							<a href="/[!Test::Url!]/[!Test2::Url!]/[!Test3::Url!]" title="[!Test::Titre!]">[!Test3::Titre!]</a>
						</div>
					[/STORPROC]
				</div>
			[/STORPROC]
		</div>
	[/STORPROC]
</div>
<div class="Clear"></div>