// Pink/Expert/ExpertGrid
[HEADER JS]Skins/[!Systeme::Skin!]/Js/pink.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/jPlayer/jquery.jplayer.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/miniplayer/jquery.mb.miniPlayer.js[/HEADER]
[HEADER CSS]Skins/[!Systeme::Skin!]/Js/miniplayer/miniplayer.css[/HEADER]
[!NbParPage:=9!]
[!Total:=0!]
[COUNT Pink/Expert/Actif=1|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!IdxPage:=[!Page:-1!]!]
[!Start:=[!IdxPage:*[!NbParPage!]!]!]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]
[!Prev:=[!Page:-1!]!]
[IF [!Prev!]<1][!Prev:=1!][/IF]
[!Next:=[!Page:+1!]!]
[IF [!Next!]>[!NbPages!]][!Next:=[!NbPages!]!][/IF]
<div class="row">
	<div class="ExpertGrid">
		[IF [!NbPages!]>1]
			<div class="Pagination">
				<div class="PaginationBody">
					<a class="PagiFirst" href="/[!Lien!][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF]">&nbsp;</a>
					<a class="PagiPrev" href="/[!Lien!][IF [!Prev!]>1]?Page=[!Prev!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]">&nbsp;</a>
					[STORPROC [!NbPages!]|P]
						[IF [!Pos!]=[!Page!]]<strong>[/IF]
						<a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
						[IF [!Pos!]=[!Page!]]</strong>[/IF]
					[/STORPROC]
					<a class="PagiNext" href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
					<a class="PagiLast" href="/[!Lien!]?Page=[!NbPages!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
				</div>
			</div>
		[/IF]
		[!Cpt:=0!]
		<div class="ExpertList row">
			[STORPROC Pink/Expert/Actif=1|Exp|[!Start!]|[!NbParPage!]|Available|DESC]
				[STORPROC Pink/Expert/[!Exp::Id!]|CatUrl|0|1][/STORPROC]
				[!heart:=[!Exp::Score!]!][!heart*=16!]
				[!Cpt+=1!]
				[IF [!Cpt!]>3]
					[!Cpt:=1!]
					</div>
					<div class="ExpertList row" >
				[/IF]
				<div class="col-md-4 ExpertCell" id="expert-[!Exp::Id!]">
					<a href="/[!Prod::getUrl()!]" title="[!Utils::noHtml([!Exp::Description!])!]">
						<img src="/[!Exp::Avatar!].mini.100x115.jpg" />
					</a>
					[IF [!Exp::Available!]]
						[IF [!Exp::OnLine!]]
							[!picto:=PhoneOn!][!state:=En ligne!][!button:=Message moi!]
						[ELSE]
							[!picto:=PhoneIdle!][!state:=!][!button:=Apelle moi!]
						[/IF]
					[ELSE]
						[!picto:=PhoneOff!][!state:=Indisponible!][!button:=Message moi!]
					[/IF]
					<i id="phone-[!Exp::Id!]" class="[!picto!]" title="[!state!]"></i>
					<div id="state-[!Exp::Id!]" class="Phone">[!state!]</div>
					[IF [!Exp::VoiceMessage!]]<i class="Speaker" title="Accueil vocal"></i>[/IF]
					<div class="Info">
						<h2>[!Exp::Initiales!]</h2>
						<h3>[SUBSTR 35|...][!Exp::Presentation!][/SUBSTR]</h3>
					</div>
					<button id="button-[!Exp::Id!]" class="btn btn-kirigami CallMe" data-options='{"profile_id":"[!Exp::Id!]"}'>[!button!]</button>
					<div class="EmptyHearts"></div>
					<div class="FullHearts" style="width:[!heart!]px"></div>
					<div class="Cost">[!Exp::Cost!] u/min</div>
					[IF [!Exp::VoiceMessage!]]
					<div class="Player">
						<a id="player_[!Exp::Id!]" class="Audio {skin:'black',showVolumeLevel:false,showRew:false,showTime:false}" href="[!Exp::VoiceMessage!]"></a>
						<i class="PlayerStop" title="Fermer"></i>
					</div>
					[/IF]
				</div>
			[/STORPROC]
		</div>
		[IF [!NbPages!]>1]
			<div class="Pagination">
				<div class="PaginationBody">
					<a class="PagiFirst" href="/[!Lien!][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF]">&nbsp;</a>
					<a class="PagiPrev" href="/[!Lien!][IF [!Prev!]>1]?Page=[!Prev!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]">&nbsp;</a>
					[STORPROC [!NbPages!]|P]
						[IF [!Pos!]=[!Page!]]<strong>[/IF]
						<a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
						[IF [!Pos!]=[!Page!]]</strong>[/IF]
					[/STORPROC]
					<a class="PagiNext" href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
					<a class="PagiLast" href="/[!Lien!]?Page=[!NbPages!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
				</div>
			</div>
		[/IF]
	</div>
</div>

