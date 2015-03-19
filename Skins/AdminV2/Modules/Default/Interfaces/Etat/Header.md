
[!Q:=[!Module::Actuel::Nom!]/!]
[STORPROC [!Obj::Historique!]|E]
	[!Q+=[!E::ObjectType!]/[!E::Id!]!]
[/STORPROC]
[IF [!Q!]=[!Module::Actuel::Nom!]/]
	[!Flag:=False!]
	[STORPROC [!Obj::typesParent!]|Par]
	[IF [!Flag!]=False]
		[STORPROC [!Module::Actuel::Nom!]/[!Par::Titre!]/[!Obj::ObjectType!]/[!Obj::Id!]|P|0|1]
		[!Q+=[!Par::Titre!]/[!P::Id!]!]
		[!T:=[!Par::Titre!]!]
		[!I:=[!Par::Icon!]!]
		[!Flag:=True!]
		[/STORPROC]
	[/IF]
	[/STORPROC]
[/IF]
[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:0px;padding-bottom:0px;width:49%;float:left;margin:1px;height:108px;]
	[STORPROC [!Module::[!Obj::Module!]::Db::ObjectClass!]|ObjClass]
		[IF [!ObjClass::titre!]=[!Obj::ObjectType!]]
			[!ObjImg:=[!ObjClass::Icon!]!]
		[/IF]
	[/STORPROC]
	<div style="background:url([!ObjImg!]) no-repeat transparent center left;height:100px;position:relative;padding-left:130px;">
		<h1 style="text-align:left;height:auto;">
			[!Obj::getFirstSearchOrder!]
		</h1>
		<div class="Etat1" style="float:left;width:49%;">
			[!UserName:=!]
			[STORPROC Systeme/User/[!Obj::userCreate!]|U|0|1|Id|DESC|Login,Id][/STORPROC]
			Cr&eacute;&eacute; par [!U::Login!]([!U::Id!]) le [!Utils::getDate(d/m/Y H:i:s,[!Obj::tmsCreate!])!]  
		</div>
		<div class="Etat2" style="float:left;width:49%;">
			[!UserName2:=!]
			[STORPROC Systeme/User/[!Obj::userEdit!]|U|0|1|Id|DESC|Login,Id][/STORPROC]
			Modifi&eacute; par [!U::Login!] le [!Utils::getDate(d/m/Y H:i:s,[!Obj::tmsEdit!])!]
		</div>
		<div class="Etat3" style="float:left;width:49%;">
			Droits d'acc&egrave;s : [!Obj::umod!][!Obj::gmod!][!Obj::omod!] 
		</div>
		<div class="Etat3" style="float:left;width:49%;">
			[STORPROC Systeme/User/[!Obj::uid!]|U|0|1|Id|DESC|Login,Id][/STORPROC]
			[STORPROC Systeme/Group/[!Obj::gid!]|G|0|1|Id|DESC|Nom,Id][/STORPROC]
			Propriétaires : [!U::Login!]([!U::Id!]) / [!G::Nom!]([!G::Id!])
		</div>
	</div>
[/BLOC]	
