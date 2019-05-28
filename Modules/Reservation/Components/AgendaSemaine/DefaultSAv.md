[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]

	//--- agenda de la semain
	[!DateStockee:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!TMS::Now!])!] 00:01)!]!]
	[!Nb:=[!Utils::getDate(w,[!DateStockee!]!]!]
	[!TABLEJOUR::[!Nb!]:=[!DateStockee!]!]
	[STORPROC 7]
		[!Nb:=[!Utils::getDate(w,[!DateStockee!]!]!]
		[!TABLEJOUR::[!Nb!]:=[!DateStockee!]!]
		[!DateStockee+=86400!]
	[/STORPROC]

	[!HgConteneurTotal:=53!]
	[!LgConteneurVisible:=106!]
	[!HgConteneurVisible:=53!]
	
	<div class="[!NOMDIV!]">
		<div class="TitreAgendaAccueil "><h2>L'Agenda</h2></div>
		<table class="AgendaAccueil" cellspacing="0" cellpadding="0"> 
			[!Ji:=0!]
			[STORPROC 7]
				[!R:=[!Pos!]!]
				[IF [!Pos!]=7][!R:=0!][ELSE][/IF]
				[!DateJourlu:=[!TABLEJOUR::[!R!]!]!]
				[!DateFinJourlu:=[!TABLEFINJOUR::[!R!]!]!]
				<tr class="UnJour">
					<td class="JourSemaine">
						[SWITCH [!Pos!]|=]
							[CASE 1]Lundi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 2]Mardi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 3]Mercredi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 4]Jeudi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 5]Vendredi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 6]Samedi<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
							[CASE 7]Dimanche<br />[DATE d.m.Y][!DateJourlu!][/DATE][/CASE]
						[/SWITCH]
					</td>
					<td style="width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
						<div class="ContenuVisible"  style="overflow:hidden;position:relative;width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
							[!NBJ:=0!]
							// compter le nombre de spectacle pour le jour lu
							[COUNT Reservation/Evenement/DateDebut>=[!DateJourlu!]&DateFin>=[!DateJourlu!]|NBJ]
							[!LgConteneurTotal:=[!LgConteneurVisible!]!]
							[!LgConteneurTotal*=[!NBJ!]!]
							<div class="ContenuTotal" id="ladivadeplacer[!Ji!]" style="height:[!HgConteneurTotal!]px;width:[!LgConteneurTotal!]px;" >
								[!SpeLu:=!]
								[STORPROC Reservation/Evenement/DateDebut>=[!DateJourlu!]&DateFin>=[!DateJourlu!]|Ev]
									[!OkAffiche:=0!]
									[!DebutEv:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Ev::DateDebut!])!] 00:01)!]!]
									[!FinEv:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Ev::DateFin!])!] 00:01)!]!]
									[IF [!DebutEv!]=[!FinEv!]]
										[IF [!DebutEv!]=[!DateJourlu!]]
											[!OkAffiche:=1!]
										[/IF]
									[ELSE]
										[IF [!DebutEv!]>=[!DateJourlu!]&&[!FinEv!]<=[!DateJourlu!]]
											[!OkAffiche:=1!]
										[/IF]
									[/IF]
									
									[IF [!OkAffiche!]=1]
										[STORPROC Reservation/Spectacle/Evenement/[!Ev::Id!]|Sp|0|1]
											<a href="/[!Systeme::getMenu(Reservation/Spectacle)!]/[!Sp::Url!]" >
												<img src="[!Domaine!]/[!Sp::Logo!].mini.53x53.jpg" width="53" height="53" alt="[!Sp::Nom!]" title="[!Sp::Nom!]"/>
											</a>
										[/STORPROC]

									 
									[/IF]
								[/STORPROC]
							</div>
						</div>
					</td>
					<td class="FlecheSuite"><a href="javascript:;" class="suivant"   onclick="deplacediv('ladivadeplacer[!Ji!]',[!NBJ!]);" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/AgendaAccueilFleche.jpg" /></a></td>					
				</tr>
				[!Ji+=1!]
			[/STORPROC]
		</table>
		<div class="ImprimeAgendaAccueil " ><a href="/Reservation/Agenda/ImprimeSemaine.pdf" alt="impression calendrier" title="impression calendrier" target="_blank"> Imprimer la semaine <span class="IconGraphique">d</span></a></div>
	
	</div>


[ELSE]
	//--- calcul des dates, ligne, nbjour nécessaire à l'affichage du mois demandé
	[!J:=[!Date::getDay()!]!]
	[!M:=[!Date::getMonth()!]!]
	[!A:=[!Date::getYear()!]!]
	
	//je remplis la table pour l'afficher
	[STORPROC Reservation/Spectacle/Id>1|Spe|0|1][/STORPROC] 
	[STORPROC Reservation/Spectacle/Id>1|Spe2|0|1][/STORPROC] 
	[!Spe::initTableSpectacle!]
	//[!Spe2::initTableSpectacle_Semaine()!]
	
	[!HgConteneurTotal:=53!]
	[!LgConteneurVisible:=106!]
	[!HgConteneurVisible:=53!]
	
	<div class="[!NOMDIV!]">
		<div class="TitreAgendaAccueil "><h2>L'Agenda</h2></div>
		<table class="AgendaAccueil" cellspacing="0" cellpadding="0"> 
			[!Ji:=0!]
			[STORPROC [!Date::getDaysOfWeek()!]|Jour]
				[!LeJour:=[!Spe::GetSpectacleJour([!Ji!])!]!]
				<tr class="UnJour">
					<td class="JourSemaine">[!Jour!]</td>
					<td style="width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
						<div class="ContenuVisible"  style="overflow:hidden;position:relative;width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
							[!NBJ:=0!]
							[COUNT [!LeJour!]|NBJ]
							[!LgConteneurTotal:=[!LgConteneurVisible!]!]
							[!LgConteneurTotal*=[!NBJ!]!]
							<div class="ContenuTotal" id="ladivadeplacer[!Ji!]" style="height:[!HgConteneurTotal!]px;width:[!LgConteneurTotal!]px;" >
								[STORPROC [!LeJour!]|LJ]
									<a href="/[!Systeme::getMenu(Reservation/Spectacle)!]/[!LJ::Url!]" >
										<img src="[!Domaine!]/[!LJ::Img!].mini.53x53.jpg" width="53" height="53" alt="[!LJ::Nom!]" title="[!LJ::Nom!]"/>
									</a>
								[/STORPROC]
							</div>
						</div>
					</td>
					<td class="FlecheSuite"><a href="javascript:;" class="suivant"   onclick="deplacediv('ladivadeplacer[!Ji!]',[!NBJ!]);" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/AgendaAccueilFleche.jpg" /></a></td>
				</tr>
				[!Ji+=1!]
	
			[/STORPROC]
		</table>
		<div class="ImprimeAgendaAccueil " ><a href="/Reservation/Agenda/ImprimeSemaine.pdf" alt="impression calendrier" title="impression calendrier" target="_blank"> Imprimer la semaine <span class="IconGraphique">d</span></a></div>
	
	</div>

[/IF]

// Surcouche JS
<script type="text/javascript">
	var Largeurinfo =58;
	var nomdeladiv="";
	
	var tab_div= new Array();
	for (i=0;i<7;i++) {
		nomdeladiv='ladivadeplacer'+i;
		tab_div[nomdeladiv]=0;
		
	}

	function deplacediv(lenomdiv, nbinfos) {
		// fonction pour déplacer quand il y a plusieurs blocks affichés
		totalinfos=-Largeurinfo*nbinfos;
		lemarginSpe = tab_div[lenomdiv] - Largeurinfo ;
		if (totalinfos == lemarginSpe) lemarginSpe = 0 ;
		$(lenomdiv).tween('margin-left', lemarginSpe+'px'); 
		tab_div[lenomdiv]=lemarginSpe;
	
	}

</script>
	


