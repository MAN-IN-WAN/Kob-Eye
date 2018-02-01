[INFO [!Chemin!]|I]
// Recherche de l'utilisateur pour avoir l'entite en cours
[STORPROC Systeme/User/[!Systeme::User::Id!]/Site|S|0|1]
	[STORPROC Systeme/Site/[!S::Id!]/Entite|Et|0|1]	[/STORPROC]
[/STORPROC]

<div class="container">
	<div class="row">
		<nav id="navmenuhaut">
			[STORPROC Abtel/Entite/[!Et::Id!]/Categorie/Publier=1|Cat]
			<a href="#" class="amenuhaut" data-ref=".[!Cat::Url!]">[!Cat::Nom!]</a>
			[/STORPROC]
			<a href="#" class="amenuhaut encours" data-ref="*">Tout voir</a>
		</nav>
		<div id="referenceContainer">
			[STORPROC Portfolio/Reference/Publier=1|Ref|0|100|DateSortie|DESC]

			<div class="col-md-3 col-sm-4 reference [STORPROC Portfolio/Categorie/Reference/[!Ref::Id!]|CP] [!CP::Url!][/STORPROC]">
				<div class="inner">
					<figure class="imgreferences">
						<a href="/[!Systeme::CurrentMenu::Url!]/Reference/[!Ref::Url!]" title="Voir le d&eacute;tail de [!Ref::Titre!]">
							[IF [!Ref::Icone!]]
							<img src="/[!Ref::Icone!]" alt="[!Ref::Titre!]" />
							[ELSE]
							<img src="/Skins/Expressiv/Img/RefDefault.jpg" alt="[!Ref::Titre!]"/>
							[/IF]
						</a>
					</figure>
					<div class="InfoRef">
						<span class="DateSortie" style="float:right;">
							[DATE m.Y][!Ref::DateSortie!][/DATE]
						</span>
						<h2 style="padding:0;font-style:normal;color:#939292;text-transform:none;">[!Ref::Titre!]</h2>
					</div>
					<p style="width:230px;">[!Ref::Chapo!]</p>
				</div>
			</div>
			[/STORPROC]
		</div>
	</div>
</div>

<script type="text/javascript">
    // init Isotope
    var iso = $('#referenceContainer').isotope({
        // options
    });
    // filter items on button click
    $('#navmenuhaut').on( 'click', 'a', function(e) {
        e.stopPropagation();
        e.preventDefault();
        var filterValue = $(this).attr('data-ref');
        iso.isotope({ filter: filterValue });

        $(this).addClass('encours');
        $(this).siblings('a').removeClass('encours');
    });
</script>