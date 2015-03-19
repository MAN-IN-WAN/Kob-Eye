<div class="ProprieteDisplay  Type[IF [!Prop::isNull!]==True]normal[ELSE][!Prop::displayType!][/IF]">
	<div class="ProprieteTitre[!Class!]">[IF [!Prop::description!]!=][!Prop::description!][ELSE][!Prop::Nom!][/IF] </div>

[SWITCH [!Prop::Type!]|=]
	[CASE text]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">[UTIL BBCODE][!Prop::Valeur!][/UTIL]</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[/IF]
	[/CASE]
	[CASE metad]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">[UTIL BBCODE][!Prop::Valeur!][/UTIL]</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[/IF]
	[/CASE]
	[CASE metat]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">[UTIL BBCODE][!Prop::Valeur!][/UTIL]</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[/IF]
	[/CASE]
	[CASE html]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">[UTIL BBCODE][!Prop::Valeur!][/UTIL]</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[/IF]
	[/CASE]
	[CASE bbcode]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">[UTIL BBCODE][!Prop::Valeur!][/UTIL]</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[/IF]
	[/CASE]
	[CASE date]
		[IF [!Prop::isNull!]==True]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[ELSE]
			<div class="ProprieteValeur">[DATE d/m/Y H:i][!Prop::Valeur!][/DATE]</div>
		[/IF]
	[/CASE]
	[CASE float]
		[IF [!Prop::isNull!]==True]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]">[!Prop::Valeur!]</div>
		[/IF]
	[/CASE]
	[CASE boolean]
		[IF [!Prop::Valeur!]]
			<div class="ProprieteValeur[!Class!]">Oui</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]">Non</div>
		[/IF]
	[/CASE]
	[CASE ref]
		[IF [!Prop::isNull!]]
			<div class="ProprieteValeur[!Class!]" style="font-style:italic;">Non renseign&eacute;</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]"><a href="/Systeme/Group/[!Prop::Valeur!]" onClick="window.open('/Systeme/Group/[!Prop::Valeur!]');return false;">Accéder au groupe [!Prop::Valeur!]</a></div>
		[/IF]
	[/CASE]
	[CASE ObjectClass]
		[IF [!Prop::Valeur!]=EMPTY]
			<div class="ProprieteValeur[!Class!]"  style="font-style:italic;">Non renseign&eacute;</div>
		[ELSE]
			<div class="ProprieteValeur[!Class!]">
			[STORPROC [!Prop::Valeur!]|T|0|1]
				[!T::ObjectType!] - [!T::getFirstSearchOrder!]
			[/STORPROC]
			</div>
		[/IF]
	[/CASE]
	[CASE file]
	    [IF [!Prop::Valeur!]=||[!Prop::Valeur!]=EMPTY]
			<div class="ProprieteValeur[!Class!]"  style="font-style:italic;">Aucun fichier attach&eacute;</div>
	    [ELSE]
		[IF jpg~[!Prop::Valeur!]]
		    <div class="ProprieteValeur[!Class!]"><a title="[!Prop::description!]" class="mb" href="/[!Prop::Valeur!]"><img src="/[!Prop::Valeur!].mini.210x55.jpg"/></a></div>
		[ELSE]
		  [IF flv~[!Prop::Valeur!]]
		    <div class="ProprieteValeur[!Class!]"><a title="[!Prop::description!]" class="mb" href="/[!Prop::Valeur!]">Voir la vid&eacute;o</a></div>
		  [ELSE]
		<div class="ProprieteValeur[!Class!]"><a href="/[!Prop::Valeur!]">[!Prop::Valeur!]</a></div>
		  [/IF]
	        [/IF]
		[/IF]
	[/CASE]
	[CASE image]
	    [IF [!Prop::Valeur!]=||[!Prop::Valeur!]=EMPTY]
			<div class="ProprieteValeur[!Class!]"  style="font-style:italic;">Aucune image attach&eacute;e</div>
	    [ELSE]
        <div class="ProprieteValeur"><a title="[!Prop::description!]" class="mb" href="/[!Prop::Valeur!].limit.800x600.jpg"><img src="/[!Prop::Valeur!].mini.210x55.jpg" /></a></div>
	    [/IF]
	[/CASE]
	[CASE h2ProdList]
	    <div class="ProprieteValeur[!Class!]">
	    [STORPROC Boutique/ProduitReel/VoyageReel=[!Obj::Id!]|Vr]
		[STORPROC Boutique/Produit/ProduitReel/[!Vr::Id!]|P|0|1]
		    [!P::getFirstSearchOrder!] ([!Vr::Tarif!] &euro;)
		[/STORPROC]
		[IF [!Pos!]!=[!NbResult!]], [/IF]
	    [/STORPROC]
	    </div>
	[/CASE]
	[DEFAULT]
		[IF [!Prop::values!]]
			[STORPROC [![!Prop::values!]:/,!]|Val]
				[!T:=[![!Val!]:/::!]!]
				[COUNT [!T!]|S]
				[IF [!S!]>1&&[!Prop::Valeur!]=[!T::0!]]
				<div class="ProprieteValeur[!Class!]">[!T::1!]</div>
				[/IF]
				[NORESULT]
				[/NORESULT]
			[/STORPROC]
		[ELSE]
			[IF [!Prop::query!]]
				[!Q:=[![!Prop::query!]:/::!]!]
				[COUNT [!Q!]|PQ]
				[IF [!PQ!]>2]
					[IF [!Prop::Valeur!]]
						[STORPROC [!Q::0!]/[!Prop::Valeur!]|Val|0|1]
							<div class="ProprieteValeur[!Class!]">[!Val::[!Q::2!]!]</div>
							[NORESULT]
							<div class="ProprieteValeur[!Class!]">Non renseign&eacute;</div>
							[/NORESULT]
						[/STORPROC]
					[ELSE]
						<div class="ProprieteValeur[!Class!]">Non renseign&eacute;</div>
					[/IF]
				[ELSE]
					<div class="ProprieteValeur[!Class!]">[!Prop::Valeur!]</div>
				[/IF]
			[ELSE]
				[IF [!Prop::Valeur!]=]
					<div class="ProprieteValeur[!Class!]">Non renseign&eacute;</div>
				[ELSE]
					<div class="ProprieteValeur[!Class!]">[!Prop::Valeur!]</div>
				[/IF]
			[/IF]
		[/IF]
	[/DEFAULT]
[/SWITCH]
</div>	
