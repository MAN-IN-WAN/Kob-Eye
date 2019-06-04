// Affichage de spectacles en liste
[IF [!FILTRE!]!=][!LEFILTRE:=&[!FILTRE!]!][/IF]
[STORPROC Reservation/Spectacle|Spec|0|1][/STORPROC]
[!CHAMPSALIRE:=[!Spec::RenvoiTableau(1,[!CHAMPS!])!]!]
[!CHAMPSCARACT:=[!Spec::RenvoiTableau(1,[!NBCARACT!])!]!]

<div class="[!NOMDIV!]">
	<div class="TitreDiv"><h2>[!TITREBLOC!]</h2></div>
	[STORPROC Reservation/Spectacle/DateFin>[!TMS::Now!][!LEFILTRE!]|Sp|0|[!NBINFOS!]|DateDebut|ASC]
		<div class="LeBloc" style="width:[!WIDTHBLOC!]px;height:[!HEIGHTBLOC!]px;">
			<div class="Image" style="width:[!WIDTHIMG!]px;height:[!HEIGHTIMG!]px;" >
				<img src="/[!Sp::Logo!].limit.[!WIDTHIMG!]x[!HEIGHTIMG!].jpg" alt="[!Sp::Nom!]" title="[!Sp::Nom!]" />
			</div>
			<div class="Description">

				[STORPROC [!NBLIGNESDESC!]|NbL]
					[STORPROC [!CHAMPSALIRE!]|Ch|0|[!Pos!]] [/STORPROC]
					[STORPROC [!CHAMPSCARACT!]|Su|0|[!Pos!]] [/STORPROC]
					[IF [!Su!]!=0]
						[!LeChamp:=[SUBSTR [!Su!]][!Utils::noHtml([!Sp::[!Ch!]!])!][/SUBSTR]!]
					[ELSE]
						[!LeChamp:=[!Sp::[!Ch!]!]!]
					[/IF]
					[IF [!Ch!]~Date]
						[!DD:=[!Utils::getDate(d-m-Y,[!Sp::DateDebut!])!]!]
						[!DF:=[!Utils::getDate(d-m-Y,[!Sp::DateFin!])!]!]
						[IF [!DD!]=[!DF!]]
						//[IF [!Sp::DateDebut!]==[!Sp::DateFin!]]
							[!LeChamp:=Le [DATE d.m.Y][!Sp::DateDebut!][/DATE] à [DATE H:i][!Sp::DateDebut!][/DATE]!]
						[ELSE]
							[!LeChamp:=Du [DATE d.m.Y][!Sp::DateDebut!][/DATE] au [DATE d.m.Y][!Sp::DateFin!][/DATE]!]
						[/IF]
					[/IF]
					[IF [!Ch!]~Duree]
						[IF [!Sp::Duree!]!=0][!LeChamp:=Durée : [!Sp::getDuree!]!][ELSE][!LeChamp:=Durée : NC!][/IF]
					[/IF]
					<div class="Ligne"><div class="Ligne[!Pos!]">[!LeChamp!]</div></div>
				[/STORPROC]
			</div>
		</div>
		[IF [!TEXTELIEN!]!=]<div class="Lien"><a href="/[!Systeme::getMenu(Reservation/Spectacle)!]/[!Sp::Url!]" />[!TEXTELIEN!]</a></div>[/IF]
	[/STORPROC]
	<div class="FinDiv"></div>

</div>