[HEADER]<script src="/Skins/AdminV2/Js/jquery.treeview.pack.js" type="text/javascript"></script>[/HEADER]
[HEADER]<script src="/Skins/AdminV2/Js/jquery.cookies-packed.js" type="text/javascript"></script>[/HEADER]
[HEADER]
	<script type="text/javascript">
	$(document).ready(function(){
		$("#browser[!TypeEnf!]").Treeview({
			speed: "fast",
			collapsed:true
		});
	});
	</script>
	<style type="text/css">
		.treeview, .treeview ul { 
			padding: 0;
			margin: 0;
			list-style: none;
		}	

		.treeview li { 
			margin: 0;
			padding: 3px 0pt 3px 16px;
		}
		
		ul.dir li { padding: 2px 0 0 16px; }
		ul.dir li div:hover{ background-color:#F0F0F0;}
		
	  	#browser[!TypeEnf!].treeview li { background: url(/Skins/AdminV2/Img/black/tv-item.gif) 0 0 no-repeat; }
	  	#browser[!TypeEnf!].treeview .collapsable { background-image: url(/Skins/AdminV2/Img/black/tv-collapsable.gif); }
	  	#browser[!TypeEnf!].treeview .expandable { background-image: url(/Skins/AdminV2/Img/black/tv-expandable.gif); }
	  	#browser[!TypeEnf!].treeview .last { background-image: url(/Skins/AdminV2/Img/black/tv-item-last.gif); }
	  	#browser[!TypeEnf!].treeview .lastCollapsable { background-image: url(/Skins/AdminV2/Img/black/tv-collapsable-last.gif); }
	  	#browser[!TypeEnf!].treeview .lastExpandable { background-image: url(/Skins/AdminV2/Img/black/tv-expandable-last.gif); }
	  	#treecontrol { margin: 1em 0; }

	</style>
[/HEADER]
<ul id="browser[!TypeEnf!]" class="dir">
[STORPROC [!Chemin!]|Objet]
	[IF [!Pos!]=1]<ul>[/IF]
	<li [IF [!Objet::isCurrent!]]class="open"[/IF]>
		<div style="width:100%;height:16px;">
			<img src="[!Objet::getIcon!]"/> 
			<input type="submit" name="Requete" value="[!Chemin!]/[!Objet::Id!]" />
			[SUBSTR 100][!Objet::getFirstSearchOrder!][/SUBSTR]
		</div>
		[RECURSIV]
	</li>
	[IF [!Pos!]=[!NbResult!]]</ul>[/IF]
[/STORPROC]
</ul>	
	
	