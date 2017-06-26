[INFO [!Chemin!]|Inf]
[STORPROC [!Inf::Historique!]|H|0|1]
	[!Niv0:=[!H::Value!]!]
[/STORPROC]
//[!Chemin!]
<div style="overflow:hidden;">
	[MODULE Portfolio/Structure/Gauche]
	<div id="Milieu" style="margin-left:260px;">
		<div id="Data" style="border-top:1px solid #827152;">
			[STORPROC 5|L]
				[STORPROC [!Query!]|Cust|[!L:*3!]|3|Id|DESC]
					<div style="overflow:hidden;[IF [!L!]>0]border-top:1px solid #827152;[/IF]">
					[LIMIT 0|3]
						<div style="float:left;width:229px;margin:0px 5px 15px 0;">
							<h1 class="Reference">[!Cust::Nom!]</h1>
							[IF [!Cust::Logo!]=]
								<a href="" title="[!Cust::Nom!]"><img src="/Skins/Expressiv/Img/RefDefault.jpg" width="229" height="133" alt="[!Cust::Nom!]"/></a>
							[ELSE]
								<a href="" title="[!Cust::Nom!]"><img src="/[!Cust::Logo!]" width="229" height="133" alt="[!Cust::Nom!]"/></a>
							[/IF]
							<p>[!Cust::Description!]</p>
						</div>
					[/LIMIT]
					</div>
				[/STORPROC]
			[/STORPROC]
		</div>
	</div>
</div>