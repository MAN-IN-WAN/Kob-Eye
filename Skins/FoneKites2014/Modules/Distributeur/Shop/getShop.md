[OBJ Distributeur|Shop|SH]
[STORPROC [!SH::getNearestShop([!Lat!],[!Long!])!]|S]
	[STORPROC Distributeur/Categorie/Shop/[!S::Id!]|C][/STORPROC]
	<div class="item [IF [!Pos!]=1]active[/IF]">
		[IF [!S::Website!]]<a href="[!S::Website!]">[/IF]<strong>[!S::Name!]</strong>[IF [!S::Website!]]</a>[/IF]<br />[!S::Adress!]<br />[!S::PostalCode!] [!S::City!]<br />[!S::Phone!]<br />[IF [!S::Website!]]<a href="http://[UTIL NOHTTP][!S::Website!][/UTIL]" target="_blank">[UTIL NOHTTP][!S::Website!][/UTIL]</a><br />[/IF]<a href="/[!Systeme::getMenu(Distributeur/Categorie)!]/[!C::Url!]/Shop/[!S::Url!]">__SEE_MAP__</a>
	</div>
[/STORPROC]
