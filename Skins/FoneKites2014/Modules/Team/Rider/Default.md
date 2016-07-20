[!lelienS:=!][!lelienP:=!]
[!NomRidP:=!][!NomRidS:=!][!PrenomRidP:=!][!PrenomRidS:=!]

[STORPROC [!Query!]|Rid|0|1][/STORPROC]
[STORPROC Team/Equipe/Rider/[!Rid::Id!]|Eq|0|1][/STORPROC]

[!lelienS:=!][!lelienP:=!]
[!NomRidP:=!][!NomRidS:=!][!PrenomRidP:=!][!PrenomRidS:=!]

//SUIVANT
[STORPROC Team/Equipe/[!Eq::Url!]/Rider/Ordre>[!Rid::Ordre!]&Display=1|RidS|0|1|Ordre|ASC]
	[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!Eq::Url!]/Rider/[!RidS::Url!]!]
	[!NomRidS:=[!RidS::Nom!]!]
	[!PrenomRidS:=[!RidS::Prenom!]!]
	[NORESULT]
		[STORPROC Team/Equipe/[!Eq::Url!]/Rider/Display=1|RidS|0|1|Ordre|ASC]
			[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!Eq::Url!]/Rider/[!RidS::Url!]!]
			[!NomRidS:=[!RidS::Nom!]!]
			[!PrenomRidS:=[!RidS::Prenom!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]

//PRECEDENT
[STORPROC Team/Equipe/[!Eq::Url!]/Rider/Ordre<[!Rid::Ordre!]&Display=1|RidP|0|1|Ordre|DESC]
	[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!Eq::Url!]/Rider/[!RidP::Url!]!]
	[!NomRidP:=[!RidP::Nom!]!]
	[!PrenomRidP:=[!RidP::Prenom!]!]
	[NORESULT]
		[STORPROC Team/Equipe/[!Eq::Url!]/Rider/Display=1|RidP|0|1|Ordre|DESC]
			[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!Eq::Url!]/Rider/[!RidP::Url!]!]
			[!NomRidP:=[!RidP::Nom!]!]
			[!PrenomRidP:=[!RidP::Prenom!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]

<div class="titre-product gris-clair">
	<div class="title-product container  nopadding-right nopadding-left" >
		<div class="row">
			<div class="col-lg-10 col-xs-6">
				<h1 class="title_prod">[!Rid::Prenom!] <span class="title">[!Rid::Nom!]</span></h1>
			</div>
			<div class="col-lg-2 col-xs-6">
				<div class="nav-product">
					<div class="nav-product-btn">
						<a class="left" href="/[!lelienP!]"  onmouseover="$('#Nom-P').css('display','block');" onmouseout="$('#Nom-P').css('display','none');" >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone" />
						</a>
					</div>
					<div class="nav-product-btn">
						<a class="right" href="/[!lelienS!]" onmouseover="$('#Nom-S').css('display','block');" onmouseout="$('#Nom-S').css('display','none');" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/></a>
					</div>
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="col-lg-10 col-xs-8">
				<div class="caract" style="min-height:40px;">[!Eq::Titre!]</div>
			</div>
			<div class="col-lg-2 col-xs-4" >
				<div class="Nom-Navigation" id="Nom-P"  style="display:none;">[!PrenomRidP!]<br />[!NomRidP!]</div>
				<div class="Nom-Navigation" id="Nom-S"  style="display:none;" >[!PrenomRidS!]<br />[!NomRidS!]</div>
			</div>

		</div>
	
	</div>



	</div>
</div>

<div class="featured">
    	<div class="container nopadding-right nopadding-left">
		<div class="row">
			<div class="col-lg-12 col">
				<img class="img-responsive" src="/[!Rid::Fond!]" alt="[!Rid::Nom!]"/>
			</div>
		</div>
        	<h3>Profile</h3>
        	<div id="fone">
			[STORPROC [!Rid::getChildren(Caracteristique)!]|Car]
				<div class="row w2">
					<div class="title-identity col-md-3 col-xs-3">[!Car::Titre!]</div> 
					<div class="palmares_1 col-md-9 col-xs-9">[!Car::Valeur!]</div>
				</div>
			[/STORPROC]
			[IF [!Rid::Web!]!=]
				<div class="row w2">
					<div class="title-identity col-md-3 col-xs-3">Site Web</div> 
					<div class="palmares_1 col-md-9 col-xs-9"><a href="[IF [!Rid::Web!]~http[ELSE]http://[/IF][!Rid::Web!]" target="_blank" rel="link" >[!Rid::Web!]</a></div>
				</div>
			[/IF]
			[IF [!Rid::Description2!]!=]
			<div class="row w2">
					<div class="title-identity col-md-3 col-xs-3">PALMARES</div> 
					<div class="palmares_1 col-md-9 col-xs-9">[!Rid::Description2!]</div>
			</div>
			[/IF]
	
			[IF [!Rid::Description3!]!=]
			<div class="row w2">
					<div class="title-identity col-md-3 col-xs-3">SPONSORS</div>
					<div class="partners_1 col-md-9 col-xs-9">[!Rid::Description3!]</div>
			</div>
			[/IF]
		</div>
		<div class="pull-right" style="line-height:42px;">
			[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
		</div>
	       

	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#fone').isotope({
			layoutMode : 'masonry'
		});
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
		    	$('#fone').isotope({
				layoutMode : 'masonry'
			});
		});	
	});
</script>


[STORPROC Team/Rider/[!Rid::Id!]/PunchText|Punch|0|1|tmsCreate|DESC]
<div class="gris-fonce">
	<div class="container">
		<h1>[!Punch::Texte!]</h1>
	</div>
</div>
[/STORPROC]

[!More:=2!]
[!Req:=Team/Rider/[!Rid::Id!]/Post/Display=1!]
[COUNT [!Req!]|NbPost]
	<div class="featured" style="padding-bottom:0;">
		<div class="container nopadding-right nopadding-left">
[IF [!NbPost!]>=1]
			<h1>__LAST_NEWS__</h1>
[/IF]
		</div>
	</div>
	
[IF [!NbPost!]>=1]
	<div class="last-news" style="padding-top:0;">
		<div class="container nopadding-right nopadding-left">
			<div id="fone2">
				[MODULE Blog/Post/LoadPost?Req=[!Req!]&Limit=[!More!]&Offset=0]
			</div> 
		</div>
	</div>  
	
	<div class="container nopadding-right nopadding-left">
		<div class="load-more">
			<a href="#nogo" class="btn-more-Media btn-primary" id="LoadMore" data-url="/Blog/Post/LoadPost.htm" data-max="[!NbPost!]" data-more="[!More!]" data-current="[!More!]" [IF [!NbPost!]<=[!More!]]style="display:none;"[/IF]>LOAD MORE NEWS</a>
		</div> 
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			var currentfilter = 'all';
			var datamax = [!NbPost!];
			/**
			 * ISOTOPE + MASONRY
			 */
			$('#fone2').isotope({
				filter: '.'+currentfilter
			});
			function refreshIsotope(newElements) {
				$('#fone2').isotope({
					filter: '.'+currentfilter
				});
				$('#fone2').imagesLoaded( function(){
					$('#fone2').isotope({
						
						// options
						filter: '.'+currentfilter
					});
				});	
			}
			
			$(window).on("debouncedresize", function( event ) {
				refreshIsotope();
			});
			
			// trigger Isotope after images have loaded
			$('#fone2').imagesLoaded( function(){
				refreshIsotope();
			});	
			/**
			 * LOAD MORE
			 */
			function loadMore(reset) {
				//affichage indicateur de chargement
				var offset=0;
				var limit = [!More!];
				var url = "/Blog/Post/LoadPost.htm";
				var req = "Post/Display=1";
				var module = "Team/Rider/[!Rid::Id!]/";
				offset = $('#fone2 .fone-item').size();
				req = module+req;
				if (reset){
					offset=0;
					$('#fone2').empty();
				}
				//lancement du chargement ajax
				$.ajax({
					url: url+'?Offset='+offset+'&Limit='+limit+'&Req='+req,
					success: function (data) {
						$('#fone2').append($(data)).isotope('reloadItems');
						refreshIsotope(data);
						//on compte le nombre d'elements
						var nb = $('#fone2 .fone-item').size();
						var nbmax =  $('#fone2 .fone-item').attr('max-item');
						console.log(nb +' < '+nbmax);
						//on compare avec le nombre total avec le nombre affiché
						if (nb<nbmax){
							$('#LoadMore').css('display','inline');
						}else $('#LoadMore').css('display','none');
	
					},
					dataType: 'html'
				});
				return false;
			}
			$('#LoadMore').on('click',function() {
				loadMore(false);
			});
		});
	</script>
[/IF]