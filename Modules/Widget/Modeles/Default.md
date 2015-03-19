//Affichage d'un teaser
<script type="text/javascript">
	window.addEvent('domready', function() {
		var myCookie = Cookie.read("TeaserOlrap");
		if (!myCookie) show();
		
		
	});

	function show() {
		$('TeaserDebut').setStyle ("display","block");
		$('TeaserDebut').focus();
	}
	
	function hide($url) {
		$('TeaserDebut').setStyle ("display","none");
		var myCookie = Cookie.write("TeaserOlrap", true, "duration:0");
		window.location.href =$url; 
	}
	
 	function hideInfos($url) {
		$('TeaserDebut').setStyle ("display","none");
		var myCookie = Cookie.write("TeaserOlrap", true, "duration:0");
		window.location.href =$url;  
	}


</script>

[STORPROC Widget/Teaser/Publier=1&DateExpiration>=[!TMS::Now!]|Wg|0|1]

	<div id="TeaserDebut" style="display:none;" onBlur="javascript:hide('[!Wg::LienHaut!]');">
		<div class="TeaserBack" ></div>
		<div class="[!Wg::NomDiv!]" >
			<div class="TeaserDroite" style="[IF [!Wg::ImageFond!]!=]background: url('[!Domaine!]/[!Wg::ImageFond!]') no-repeat;[/IF]">
				<div class="LiensFermeTeaser"><a href="javascript:hide('[!Wg::LienHaut!]');"  title="[!Wg::TexteLienHaut!]" >[!Wg::TexteLienHaut!]</a></div>
				<div class="AccrocheTeaser" style="font-size:[!Wg::CssAccroche!]" >[!Wg::Accroche!]</div>
				<div class="TexteTeaser" style="font-size:[!Wg::CssTexte!]">[!Wg::Texte!]</div>
				[IF [!Wg::Chrono!]]	[!LeRebours:=[!Wg::DateExpirationChrono!]!][!LeRebours-=[!TMS::Now!]!]<div class="ChronoTeaser">J -[DATE d][!LeRebours!][/DATE] </div>[/IF]
				<div class="LiensInfos"><a href="javascript:hideInfos('[!Wg::Lien!]');" title="[!Wg::TexteLien!]" >[!Wg::TexteLien!]</a></div>
			</div>
			<div class="TeaserGauche" style="[IF [!Wg::ImageFond!]!=]background: url('[!Domaine!]/[!Wg::Image!]') no-repeat;[/IF]"></div>
			
		</div>
	</div>

[/STORPROC]


