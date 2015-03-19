[IF [!Module!]=]
    	[!Module:=[!Module::Actuel::Nom!]!]
[/IF]
[IF [!FirstObject!]=]
    	[!FirstObject:=[!QueryFirstObject!]!]
[/IF]
<div class="LogoEntete">
<div class="ArabesqueEntete" id="ArabesqueEntete">
<ul class="tabs" style="position:absolute;bottom:0;right:20px;z-index:0;" id="TabulationsDD">
	[IF [!Lien!]=[!Module!]]
		<li class="Selected ModuleAccueil" >
			<a href="/[!Module!]" class="selected"><b class="p1"></b><b class="p2">
			Accueil</b><b class="p3"></b></a>
		</li>
	[ELSE]
		<li class="ModuleAccueil">
			<a href="/[!Module!]"><b class="p1"></b><b class="p2">
			Accueil</b><b class="p3"></b></a>
		</li>
	[/IF]
	[STORPROC [!Module::[!Module!]::Db::AccessPoint!]|ObjClass]
		[IF [!ObjClass::titre!]=[!FirstObject!]]
			<li class="Selected Module[!ObjClass::titre!]">
				<a href="/[!Module!]/[!ObjClass::titre!]" class="selected"><b class="p1"></b><b class="p2">
				[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</b><b class="p3"></b></a>
			</li>
		[ELSE]
			<li class="Module[!ObjClass::titre!]">
				<a href="/[!Module!]/[!ObjClass::titre!]"><b class="p1"></b><b class="p2">
				[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</b><b class="p3"></b></a>
			</li>
		[/IF]
	[/STORPROC]
</ul>
</div>
</div>
