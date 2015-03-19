// recherche des metas
[STORPROC Systeme/Page/MD5=[UTIL MD5][!Domaine!]/[!Lien!][/UTIL]|P|0|1]
    [IF [!P::Title!]!=][TITLE][!P::Title!][/TITLE][/IF]
    [IF [!P::Description!]!=][DESCRIPTION][!P::Description!][/DESCRIPTION][/IF]
    [IF [!P::Keywords!]!=][KEYWORDS][!P::Keywords!][/KEYWORDS][/IF]
    
    [NORESULT]
  		// META MENU EN COURS
		[!Men:=[!Systeme::CurrentMenu!]!]
		[IF [!Men::Title!]!=]
			[!LeTitre:=[!Men::Title!]!]
		[/IF]
		[IF [!Men::Description!]!=]
			[!LaDesc:=[!Men::Description!]!]
		[/IF]
		[IF [!Men::Keywords!]!=]
			[!LesKeys:=[!Men::Keywords!]!]
		[/IF]
		[!Lurl:=[!Men::Url!]!]
		// METAS QUERY EN COURS
		[STORPROC [!Query!]|MetaQ]
			[IF [!Query!]~ParcImmobilier]
				[IF [!MetaQ::MetaTitle!]!=][!LeTitre:=[!MetaQ::MetaTitle!]!][/IF]
				[IF [!MetaQ::MetaDescription!]!=][!LaDesc:=[!MetaQ::MetaDescription!]!][/IF]
				[IF [!MetaQ::MetaKeywords!]!=][!LesKeys:=[!MetaQ::MetaKeywords!]!][/IF]
			[ELSE]
				[IF [!MetaQ::TitleMeta!]!=][!LeTitre:=[!MetaQ::TitleMeta!]!][/IF]
				[IF [!MetaQ::DescriptionMeta!]!=][!LaDesc:=[!MetaQ::DescriptionMeta!]!][/IF]
				[IF [!MetaQ::KeywordsMeta!]!=][!LesKeys:=[!MetaQ::KeywordsMeta!]!][/IF]
			[/IF]
			[!Lurl:=[!MetaQ::Url!]!]
		[/STORPROC]
		[IF [!Chemin!]!=]
			[STORPROC [!Chemin!]|MetaQ]
				[IF [!MetaQ::TitleMeta!]!=]
					[!LeTitre:=[!MetaQ::TitleMeta!]!]
				[/IF]
				[IF [!MetaQ::DescriptionMeta!]!=]
					[!LaDesc:=[!MetaQ::DescriptionMeta!]!]
					
				[/IF]
				[IF [!MetaQ::KeywordsMeta!]!=]
					[!LesKeys:=[!MetaQ::KeywordsMeta!]!]
				[/IF]
				[!Lurl:=[!MetaQ::Url!]!]
			[/STORPROC]
		[/IF]

		[TITLE][!LeTitre!][/TITLE]
		[DESCRIPTION][!LaDesc!][/DESCRIPTION]
		[KEYWORDS][!LesKeys!][/KEYWORDS]

		[STORPROC Systeme/Site|P]
			[IF [!P::Domaine!]=[!P::RenvoieSite([!Domaine!])!]]
				[!LeSite:=[!P::Id!]!]
			[/IF]
		[/STORPROC]
		[IF [!LeTitre!]!=]
			[OBJ Systeme|Page|Rec]
				[METHOD Rec|Set]
					[PARAM]Url[/PARAM]
					[PARAM][!Domaine!]/[!Lurl!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]MD5[/PARAM]
					[PARAM][UTIL MD5][!Domaine!]/[!Lien!][/UTIL][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]LastMod[/PARAM]
					[PARAM][!TMS::Now!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]Title[/PARAM]
					[PARAM][!LeTitre!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]Description[/PARAM]
					[PARAM][!LaDesc!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]Keywords[/PARAM]
					[PARAM][!LesKeys!][/PARAM]
				[/METHOD]
				[METHOD Rec|AddParent]
					[PARAM]Systeme/Site/[!LeSite!][/PARAM]
				[/METHOD]
			[METHOD Rec|Save][/METHOD]
		[/IF]
    [/NORESULT]
    
[/STORPROC]

// recherche de google analytics et piwik

[STORPROC Systeme/Site|P]
	[IF [!P::Domaine!]=[!P::RenvoieSite([!Domaine!])!]]
		[IF [!P::AnalyticsCode!]!=]
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', '[!P::AnalyticsCode!]']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			
			</script>
		[/IF]	
		[IF [!P::PiwikCle!]!=]
			<!-- Piwik -->
			<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.abtel.fr/" : "http://piwik.abtel.fr/");
			document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
			</script><script type="text/javascript">
			try {
			var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", [!P::PiwikCle!]);
			piwikTracker.trackPageView();
			piwikTracker.enableLinkTracking();
			} catch( err ) {}
			</script><noscript><p><img src="http://piwik.abtel.fr/piwik.php?idsite=[!P::PiwikCle!]" style="border:0" alt="" /></p></noscript>
			<!-- End Piwik Tracking Code -->
		
		[/IF]		
	[/IF]

[/STORPROC]