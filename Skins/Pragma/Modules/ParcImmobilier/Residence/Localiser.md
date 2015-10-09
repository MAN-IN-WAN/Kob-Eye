<div id="Localiser">
	[!NbCartes:=0!]
	[IF [!R::Plan!]]
		[!NbCartes+=1!]
		<a href="/[!R::Plan!].limit.800x600.jpg" class="mb" rel="link" title="[!R::Titre!]" alt="[!R::Titre!]" style="text-align:center;float:none;margin:0;"><img class="Plan" src="/[!R::Plan!].limit.578x403.jpg" alt="Plan" /></a>
	[/IF]
	[IF [!R::PlanSitu!]]
		[!NbCartes+=1!]
		<a href="/[!R::PlanSitu!].limit.800x600.jpg" class="mb" rel="link" title="[!R::Titre!]" alt="[!R::Titre!]" style="text-align:center;float:none;margin:0;"><img class="Plan" src="/[!R::PlanSitu!].limit.578x403.jpg" alt="Plan de situation" /></a>
	[/IF]
	[IF [!R::PlanMasse!]]
		[!NbCartes+=1!]
		<a href="/[!R::PlanMasse!].limit.800x600.jpg" class="mb" rel="link" title="[!R::Titre!]" alt="[!R::Titre!]" style="text-align:center;float:none;margin:0;"><img class="Plan" src="/[!R::PlanMasse!].limit.578x403.jpg" alt="Plan de masse" /></a>
	[/IF]
	[IF [!NbCartes!]>0]
		<div id="ChangeCarte" style="display:none">
			[STORPROC [!NbCartes!]|Idx]
				<a class="changeCarte [IF [!Pos!]=1] currentCarte [/IF]" href="#">[!Pos!]</a>
			[/STORPROC]
		</div>
	[ELSE]
		Pas de plan pour cette résidence
	[/IF]
</div>