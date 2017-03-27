//Modèle pour affichage de l'accueil GroupeAbtel
<div class="row noMargin">
	<div id="projectHome" class="homeLateral col-md-3">
		<div id="tiersLogo">
		</div>
		<div id="projectResume">
		</div>
		<div id="projectShare">
			<div id="progress_AI" class="progressContainer">
				<p class="projectEntity"><span class="entityPercent"></span> <span class="entityName"></span></p>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
					</div>
				</div>
			</div>	
			<div id="progress_AW" class="progressContainer">
				<p class="projectEntity"><span class="entityPercent"></span><span class="entityName"></span></p>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
					</div>
				</div>
			</div>
			<div id="progress_AN" class="progressContainer">
				<p class="projectEntity"><span class="entityPercent"></span><span class="entityName"></span></p>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
					</div>
				</div>
			</div>
			<div id="progress_AF" class="progressContainer">
				<p class="projectEntity"><span class="entityPercent"></span><span class="entityName"></span></p>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
					</div>
				</div>
			</div>
		</div>
		<p id="slideNextProj"><a id="slideNextProj" href="#slideBack" role="button" data-slide="next" title="Projet suivant">></a></p>
	</div>

	<div id="newsHome" class="homeLateral col-md-3 pull-right">
		[STORPROC News/Nouvelle/Publier=1|Actu|0|3]
			[STORPROC News/Categorie/Nouvelle/[!Actu::Id!]|Cat][/STORPROC]
			[!Entite:=[!Cat::getOneChild(Entite)!]!]
			
			[IF [!Entite!]]
			[ELSE]
				[STORPROC Abtel/Entite/CodeGestion=00|Entite][/STORPROC]			
			[/IF]
			<a href="[!Actu::getUrl()!]">
				<div class="newsPreview">		
					<h3><span class="newsEntite newsEntite_[!Entite::CodeGestion!]" style="color:[!Entite::CodeCouleur!];">[!Entite::CodeGestion!]</span> [!Actu::Titre!]</h3>
					<div class="newsContent"><p>[SUBSTR 150][!Actu::Contenu!][/SUBSTR]</p></div>
				</div>
			</a>
		[/STORPROC]
	</div>
</div>

<script type="text/javascript">
	//Permet de voir le background de la page d'accueil
	$('#main').css('background-color','transparent');
	
	var okGo = 0;
	var stockProj;
	$(document).on('ready', function(){
		$.ajax({
			url : '/Abtel/Projet/getHomeProjects.json',
			dataType :'json',
			success : function(result){
				$('#siteWrap').append(result.back);
				stockProj = result.side;
				okGo = 1;
			},
			error: function(e){
				console.log(e);
			}
		}).done(function(){
			if (!okGo) {
				//Eventuiellement afficher une erreur
                                return false;
                        }
			updateProj();
			$('#slideBack').on('slid.bs.carousel', function () {
				updateProj();
			});
			$('#slideBack').carousel('cycle');
			//fitHeight();
		});

	});
	
	$(window).on('resize',fitHeight);
	
	function updateProj(){
		var projId = $('.item.active').data('project');
		var projInfo = stockProj[projId];
		
		if (projInfo.logo) {
                        $('#tiersLogo').html('<img src="'+projInfo.logo+'" alt="'+projInfo.tiersNom+'" title="'+projInfo.tiersNom+'"/>');
                } else {
			$('#tiersLogo').html('');
		}
		
		if (projInfo.desc) {
			$('#projectResume').html(projInfo.desc);
		} else {
			$('#projectResume').html('');
		}
		
		for (var n in projInfo.progress){
			var content = projInfo.progress[n];
			$('#progress_'+n+' .entityPercent').text(content.value+'%');
			$('#progress_'+n+' .entityName').text(content.name);
			$('#progress_'+n+' .progress-bar').attr('aria-valuenow',content.value);
			$('#progress_'+n+' .progress-bar').css('background-color',content.color);
			$('#progress_'+n+' .progress-bar').css('width',content.value+'%');
		}
	}
</script>