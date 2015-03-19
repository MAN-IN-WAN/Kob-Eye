//Detection de l existence d une priorite sur un element
[INFO [!Query!]|Test]
[!Default:=Prop!]
[STORPROC [!Test::typesEnfant!]|Enf]
	[IF [!Enf::Behaviour!]="List"][!Default:=[!Enf::Titre!]!][/IF]
[/STORPROC]
[IF [!NavObj!]=][!NavObj:=[!Default!]!][/IF]
[COUNT [!Query!]|Lv]
[SWITCH [!Test::TypeSearch!]|=]
	[CASE Child]
		[!NavObj:=[!Test::TypeChild!]!]
		//Barre d'outil
		<div class="BarreAction">
			<div id="Gauche"></div>
				<div class="menu">
					<ul>
						<li><a href="/[!Test::LastDirect!]" [IF [!NavObj!]=Prop]class="selected"[/IF]>Propri&eacute;t&eacute;s</a></li>
						[INFO [!Test::LastDirect!]|Ty]
						[STORPROC [!Ty::typesEnfant!]|Type]
							[IF [!Type::Titre!]!=[!Ty::TypeChild!]&&[!Type::Behaviour!]!=Integrated]
								<li><a href="/[!Test::LastDirect!]/[!Type::Titre!]" [IF [!NavObj!]=[!Type::Titre!]]class="selected"[/IF]>[!Type::Titre!]</a></li>
							[/IF]
						[/STORPROC]
						<li><a href="/[!Test::LastDirect!]/[!Test::TypeChild!]/Ajouter" [IF [!Test::Behaviour!]=Integrated]class="makePopup" rel="/Systeme/Interfaces/Formulaire/Popup.htm?Action=Ajouter&QueryObj=[!Query!]/[!Test::Titre!]::[!Query!]/[!Test::Titre!]/Ajouter::true"[/IF]>Ajouter [!Test::TypeChild!] </a></li>
					</ul>
				</div>
			<div id="Droite"></div>
		</div>
	[/CASE]
	[CASE Direct]
		//Barre d'outil
		<div class="BarreAction">
			<div id="Gauche"></div>
				<div class="menu">
					<ul>
						[STORPROC [!Query!]|Objet|0|1][/STORPROC]
						<li><a href="/[!Query!][IF [!Lv!]>1]/[!Id!][/IF]/Modifier">Modifier</a></li>
						<li><a href="/[!Query!][IF [!Lv!]>1]/[!Id!][/IF]/Supprimer" rel="confirm" message="Attention! Vous allez supprimer l'objet.Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Test::LastChild!].htm">Supprimer</a></li>
						[STORPROC [!Test::typesEnfant!]|Type]
							<li><a href="#nogo" id="Edition">Ajouter<!--[if IE 7]><!--></a><!--<![endif]-->
								<!--[if lte IE 6]><table><tr><td><![endif]-->
									<ul>
										[LIMIT 0|100]
				[IF [!Type::hidden!]!=1&&[!Type::container!]!=1]						   [IF [!Type::Behaviour!]=Integrated]
	<li><a href="/[!Query!]/[!Type::Titre!]/Ajouter" class="makePopup" rel="/Systeme/Interfaces/Formulaire/Popup.htm?Action=Ajouter&QueryObj=[!QueryVous allez supprimer l'objet !]/[!Type::Titre!]::[!Query!]/[!Type::Titre!]/Ajouter::true">Ajouter [!Type::Titre!]</a></li>
	[ELSE]
	<li><a href="/[!Query!]/[!Type::Titre!]/Ajouter">Ajouter [!Type::Titre!]</a></li>
	[/IF]
	[/IF]									[/LIMIT]
									</ul>
								<!--[if lte IE 6]><table><tr><td><![endif]-->
							</li>
							[/STORPROC]
							[STORPROC [!Test::typesParent!]|TypeP]
							<li><a href="#nogo" id="Edition">D&eacute;placer<!--[if IE 7]><!--></a><!--<![endif]-->
								<!--[if lte IE 6]><table><tr><td><![endif]-->
									<ul>
										[LIMIT 0|100]
											<li><a href="/[!Query!]/[!TypeP::Titre!]/Deplacer">D&eacute;placer par rapport &agrave; un  [!TypeP::Titre!]</a></li>
										[/LIMIT]
									</ul>
								<!--[if lte IE 6]><table><tr><td><![endif]-->
							</li>
							[/STORPROC]
							<!--[if lte IE 6]><table><tr><td><![endif]-->
						[STORPROC [!Test::Functions!]|Func]
							<li><a href="/[!Query!]/[!Key!]"><img src="[!Func::Icon!]" style="margin-bottom:-5px;">&nbsp;[!Key!]<!--[if IE 7]><!--></a><!--<![endif]-->
							</li>
						[/STORPROC]
					</ul>
				</div>
			<div id="Droite"></div>
		</div>
	[/CASE]
[/SWITCH]
