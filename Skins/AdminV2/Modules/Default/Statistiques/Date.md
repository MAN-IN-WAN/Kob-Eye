[INFO [!Lien!]|I]
[IF [![!Var!]!]=][![!Var!]:=[!Date::getYear()!]-[!Date::getMonth()!]-[!Date::getDay()!]!][/IF]
[BLOC Rounded][![!Var!]!][/BLOC]
//[IF [!I::NbHisto!]=2]
	[STATS [!Module::Actuel::Nom!]|V]
	[!DateT:=[![![!Var!]!]:/-!]!]
	[!DateU:=[![![!Var2!]!]:/-!]!]
	[STORPROC [!V!]|V1|0|1][/STORPROC]
	[STATS [!Module::Actuel::Nom!]/[!V1::Name!]/Folder|V2]
	<ul>
	[STORPROC [!V2!]|F]
		[!Affich:=0!]
		[IF [!Var!]=DateFin][!Mois:=12!][ELSE][!Mois:=1!][/IF]
		[IF [!Var!]=DateDebut&&[!DateU::0!]>=[!F::Name!]]
			<li><a [IF [!DateT::0!]=[!F::Name!]]style="font-weight:bold;color:#000000;"[/IF] href="/[!Lien!]?[!Var!]=[!F::Name!]-[!Mois!]&[!Var2!]=[![!Var2!]!]">[!F::Name!]</a>
			[!Affich:=1!]
		[/IF]
		[IF [!Var!]=DateFin&&[!DateU::0!]<=[!F::Name!]]
			<li><a [IF [!DateT::0!]=[!F::Name!]]style="font-weight:bold;color:#000000;"[/IF] href="/[!Lien!]?[!Var!]=[!F::Name!]-[!Mois!]&[!Var2!]=[![!Var2!]!]">[!F::Name!]</a>
			[!Affich:=1!]
		[/IF]
		[IF [!DateT::0!]=[!F::Name!]&&[!Affich!]]
			<ul>
			[STATS [!Module::Actuel::Nom!]/[!V1::Name!]/Folder/[!F::Name!]|V3]
			[STORPROC [!V3!]|F2]
				[IF [!Var!]=DateFin][!Jour:=31!][ELSE][!Jour:=1!][/IF]
				[IF [!Var!]=DateDebut&&[!DateU::0!][!DateU::1!]>=[!F::Name!][!F2::Name!]]
					<li>
					<a [IF [!DateT::1!]=[!F2::Name!]]style="font-weight:bold;color:#000000;"[/IF] href="/[!Lien!]?[!Var!]=[!F::Name!]-[!F2::Name!]-[!Jour!]&[!Var2!]=[![!Var2!]!]">
					[!Date::getDate(M,[!Utils::getTms(1,[!F2::Name!],1970)!])!]
					</a>
					</li>
				[/IF]
				[IF [!Var!]=DateFin&&[!DateU::0!][!DateU::1!]<=[!F::Name!][!F2::Name!]]
					<li>
					<a [IF [!DateT::1!]=[!F2::Name!]]style="font-weight:bold;color:#000000;"[/IF] href="/[!Lien!]?[!Var!]=[!F::Name!]-[!F2::Name!]-[!Jour!]&[!Var2!]=[![!Var2!]!]">
					[!Date::getDate(M,[!Utils::getTms(1,[!F2::Name!],1970)!])!]
					</a>
					</li>
				[/IF]
			[/STORPROC]
			</ul>
		[/IF]
		</li>
	[/STORPROC]
	</ul>
//[/IF]
