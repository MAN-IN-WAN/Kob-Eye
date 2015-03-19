//DETECTION DE L EXISTENCE DU PROFIL
[STORPROC [!Systeme::User::getChildren(Profil)!]|P|0|1]

	//CALCUL PROGRESSION
	[!DELTA:=[!P::PoidsDepart:-[!P::PoidsSouhaite!]!]!]
	[!ETAT:=[!P::PoidsDepart:-[!P::PoidsActuel!]!]!]
	[!PG:=[!ETAT:/[!DELTA!]!]!]
	//PROGRESSION DU PROFIL
	<div class="jarviswidget" id="widget-id-1">
		<header>
			<h2>Votre progression</h2>                           
		</header>
		<div>
			<div class="inner-spacer"> 
				<div class="control-group">
					<label class="control-label">
						Vous êtes à [!Math::Round([!PG:*100!])!]% de votre objectif.
					</label>
					<div class="controls">
						<div class="progress active-bar-success active progress-striped">
							<div class="bar" style="width: [!Math::Round([!PG:*100!])!]%; "></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	//RECAPITULATIF DU PROFIL
	<!-- new widget -->
	<div class="jarviswidget" id="widget-id-0">
		<header>
			<h2>Courbe de poids</h2>                           
		</header>
		
		<!-- widget div-->
		<div>
			<div class="inner-spacer"> 

				//FELICITATION
				[IF [!Math::Round([!PG:*100!])!]=100]
					<div class="widget alert alert-success adjusted">
						<button class="btn btn-mini pull-right btn-success">Object atteint</button>
						<i class="cus-accept"></i>
						<strong>Félicitation vous avez atteint votre objectif !
					</div>
				[/IF]

				

				<!-- chart -->
				<div id="site-stats" class="chart has-legend"></div>
				<script type="text/javascript">
					$.poids = [[STORPROC ProgrammeMinceur/Profil/[!P::Id!]/Pesee|Pe|0|1000|Date|ASC][IF [!Pos!]>1],[ELSE][!First:=[!Pe!]!][/IF][IF [!Pos!]=[!NbResult!]][!Last:=[!Pe!]!][/IF][[!Pe::Date:*1000!], [!Pe::Poids!]][/STORPROC]];
					$.poidssouhaite = [[STORPROC ProgrammeMinceur/Profil/[!P::Id!]/Pesee|Pe|0|1|Date|ASC][[!First::Date:*1000!], [!P::PoidsSouhaite!]],[[!Last::Date:*1000!], [!P::PoidsSouhaite!]][/STORPROC]];
				</script>
				<!-- end content -->	
			</div>
			
		</div>
	</div>
	<!-- end widget div -->
	
	[NORESULT]
		[MODULE ProgrammeMinceur/Profil/MessageCreation]
	[/NORESULT]
[/STORPROC]