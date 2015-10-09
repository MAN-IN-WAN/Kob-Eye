[STORPROC [!Query!]/Donnee/TypePlan=[!TypePlan!]|Plan]
	<div class="BlocPlan">
		<h4>[!H4!]</h4>
		[IF [!TypePlan!]=Villa&&[!R::Villa!]!=]]
			<img src="/[!R::Villa!]" alt="" />
		[/IF]
		[IF [!TypePlan!]=Studio&&[!R::Studio!]!=]]
			<img src="/[!R::Studio!]!]" alt="" />
		[/IF]

		[IF [!R::Plan[!TypePlan!]!]!=]
			<img src="/[!R::Plan[!TypePlan!]!]" alt="" />

		[/IF]
		<a class="VoirPlans bleu" style="display:none" href="#">Voir tous les plans</a>
		<ul class="ListePlans">
			[LIMIT 0|100]
				<li><a href="/[!Plan::URL!]" target="_blank" >[!Plan::Titre!]</a></li>
			[/LIMIT]
		</ul>
	</div>
[/STORPROC]