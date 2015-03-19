//ENREGISTREMENT DU PROFIL
[STORPROC [!Systeme::User::getChildren(Profil)!]|Pro|0|1]
	[NORESULT]
		[OBJ ProgrammeMinceur|Profil|Pro]
	[/NORESULT]
[/STORPROC]
[IF [!ENVOYER!]=OK]
	//IDENTITE
	[METHOD Pro|Set][PARAM]Nom[/PARAM][PARAM][!Nom!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]Prenom[/PARAM][PARAM][!Prenom!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]DateNaissance[/PARAM][PARAM][!DateNaissance!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]Sexe[/PARAM][PARAM][!Sexe!][/PARAM][/METHOD]
	//MENSURATIONS
	[METHOD Pro|Set][PARAM]PoidsDepart[/PARAM][PARAM][!PoidsDepart!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]Taille[/PARAM][PARAM][!Taille!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]Imc[/PARAM][PARAM][!Imc!][/PARAM][/METHOD]
	//OBJECTIF
	[METHOD Pro|Set][PARAM]PoidsIdeal[/PARAM][PARAM][!PoidsIdeal!][/PARAM][/METHOD]
	[METHOD Pro|Set][PARAM]PoidsSouhaite[/PARAM][PARAM][!PoidsSouhaite!][/PARAM][/METHOD]
	//PROGRAMME
	[METHOD Pro|Set][PARAM]DateDepart[/PARAM][PARAM][!DateDepart!][/PARAM][/METHOD]
	[METHOD Pro|AddParent][PARAM][!Systeme::User!][/PARAM][/METHOD]
	[IF [!Pro::Verify!]]
		[METHOD Pro|Save][/METHOD]
		[REDIRECT]MonProfil/Detail[/REDIRECT]
	[ELSE]
		//ERREURS
		<div class="alert alert-danger">Il y a des erreurs dans votre saisie... Veuillez vérifier vos informations.</div>
	[/IF]
[/IF]

//EDITION DU PROFIL
<div class="row-fluid">
	<article class="span12 sortable-grid ui-sortable">
		<!-- new widget -->
		<div class="jarviswidget" id="widget-id-0">
			<header>
			<h2>Edition du profil</h2> 
							
			</header>
			<!-- wrap div -->
			<div>
			
			<div class="jarviswidget-editbox">
				<div>
				<label>Title:</label>
				<input type="text" />
				</div>
				<div>
				<label>Styles:</label>
				<span data-widget-setstyle="purple" class="purple-btn"></span>
				<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
				<span data-widget-setstyle="green" class="green-btn"></span>
				<span data-widget-setstyle="yellow" class="yellow-btn"></span>
				<span data-widget-setstyle="orange" class="orange-btn"></span>
				<span data-widget-setstyle="pink" class="pink-btn"></span>
				<span data-widget-setstyle="red" class="red-btn"></span>
				<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
				<span data-widget-setstyle="black" class="black-btn"></span>
				</div>
			</div>
		
			<div class="inner-spacer"> 
			<!-- content goes here -->
					<form id="wizard" class="themed" method="POST">
																
						<div id="wizard_name">
							
							<!-- wizard steps -->
							<ul class="bwizard-steps">
								<li>
									<span class="label badge-inverse">1</span>
									<a href="#inverse-tab1" data-toggle="tab">Identité</a>
								</li>
								<li>
									<span class="label badge-inverse">2</span>
									<a href="#inverse-tab2" data-toggle="tab">Mensurations</a>
								</li>
								<li>
									<span class="label badge-inverse">3</span>
									<a href="#inverse-tab3" data-toggle="tab">Objectif</a>
								</li>
								<li>
									<span class="label badge-inverse">4</span>
									<a href="#inverse-tab4" data-toggle="tab">Programme</a>
								</li>
							</ul>
							<!-- end wizard steps -->
							
							<div class="tab-content">
								<!-- step 1 Identité-->
								<fieldset class="tab-pane" id="inverse-tab1">
									<div class="control-group">
										<label class="control-label" for="name">Prénom</label>
										<div class="controls">
											<input type="text" name="Nom" class="span12" id="name" value="[!Systeme::User::Prenom!]" required>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="lname">Nom</label>
										<div class="controls">
											<input type="text" name="Prenom" class="span12" id="lname" value="[!Systeme::User::Nom!]" required>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="sexe">Sexe</label>
										<div class="controls">
											<select class="span12" id="sexe" name="Sexe" required>
												<option >Veuillez sélectionner</option>
												<option value="F">Femme</option>
												<option value="H">Homme</option>
											</select>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="input01">Date de naissance</label>
										<div class="controls">
											<input type="date" class="span12" name="DateNaissance" value="[IF [!Pro::DateNaissance!]!!][DATE d/m/Y][!Pro::DateNaissance!][/DATE][/IF]" id="input01" placeholder="jj/mm/aaaa">
										</div>
									</div>
								</fieldset>
								<!-- step 2 Mensuration-->
								<fieldset class="tab-pane" id="inverse-tab2">
									<div class="control-group">
										<label class="control-label" for="s1">Poids actuel</label>
										<div class="controls">
											<input type="text" class="span12" name="PoidsDepart" value="[!Pro::PoidsDepart!]" id="s1" required/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="s2">Taille (cm)</label>
										<div class="controls">
											<input type="text" class="span12" name="Taille" value="[!Pro::Taille!]" id="s2" required/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="s3">Indice de masse corporelle (champ automatique)</label>
										<div class="controls">
											<input type="text" class="span12" name="Imc" value="[!Pro::Imc!]" id="s3">
										</div>
									</div>
								</fieldset>
								<!-- step 3 Objectif-->
								<fieldset class="tab-pane" id="inverse-tab3">
									<div class="control-group">
										<label class="control-label" for="s4">Poids idéal (champs automatique)</label>
										<div class="controls">
											<input type="text" class="span12" value="[!Pro::PoidsIdeal!]" name="PoidsIdeal" id="s4">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="s5">Poids souhaité</label>
										<div class="controls">
											<input type="text" name="PoidsSouhaite" value="[!Pro::PoidsSouhaite!]" class="span12" id="s5">
										</div>
									</div>
								</fieldset>
								<!-- step 4 Programme-->
								<fieldset class="tab-pane" id="inverse-tab4">
									<div class="control-group">
										<label class="control-label" for="DateDebut">Date souhaitée de début de programme</label>
										<div class="controls">
											<input type="date" class="span12" name="DateDepart" value="[IF [!Pro::DateDepart!]!!][DATE d/m/Y][!Pro::DateDepart!][/DATE][/IF]" id="DateDebut" placeholder="jj/mm/aaaa">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="s8">Enregistrer mon profil</label>
										<div class="controls">
											<input type="hidden" name="ENVOYER" value="OK"/>
											<button class="btn btn-primary">Enregistrer et commencer mon programme</button>
										</div>
									</div>
								</fieldset>
								<!-- wizard -->
								<div class="form-actions wizard">
								<div class="span6 hidden-phone">
									<strong class="" style="margin-right: 5px; line-height: 25px; float:left;">Début</strong>
									<strong class="" style="margin-left: 5px; line-height: 25px;">Fin</strong>
										<div id="bar" class="progress progress-info slim" style="margin-bottom:0;">
											<div class="bar"></div>
										</div>
								</div>
		
									<div class="span6">
										<ul style="list-style: none;">
												<li class="previous" id="previous">
													<a class="btn medium btn-danger">
														Précédent
													</a>
												</li>
												<li class="next" id="next">
													<a class="btn medium btn-primary next">
														Suivant
													</a>
												</li>
										</ul>
									</div>
		
								</div>
							</div>
								
						</div>
		
					</form>
				</div>
				<!-- end content-->
			</div>
			<!-- end wrap div -->
		</div>
		<!-- end widget -->
	</article>
</div>
