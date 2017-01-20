[STORPROC [!Chemin!]|R|0|1]
	[IF [!R::TitleMeta!]!=]
		[!LeTitre:=[!R::TitleMeta!]!]
	[ELSE]
		[IF [!R::Titre!]][!LeTitre:=[!R::Titre!]!][ELSE][!LeTitre:=[!R::Nom!]!][/IF]
	[/IF]

	[IF [!R::DescriptionMeta!]!=]
		[!LaDesc:=[!R::DescriptionMeta!]!]
	[ELSE]
		[IF [!Systeme::CurrentMenu::Alias!]~Rider]
			[!LaDesc:=[!Utils::noHtml([!R::Description2!])!]!]
		[ELSE]
			[IF [!R::Contenu!]][!LaDesc:=[!Utils::noHtml([!R::Contenu!])!]!][ELSE][!LaDesc:=[!Utils::noHtml([!R::Description!])!]!][/IF]
		[/IF]
	[/IF]
	[IF [!R::ImgMeta!]!=]
		[!Limg:=[!R::ImgMeta!]!]
	[ELSE]
		[!Limg:=[!Domaine!]/Skins/[!Systeme::Skin!]/img/logo_f-one.jpg!]
	[/IF]
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@FOneKites">
	<meta name="twitter:title" content="[!LeTitre!]">
	<meta name="twitter:description" content="[!LaDesc!]">
	<meta name="twitter:creator" content="@FOneKites">
	<meta name="twitter:image" content="[!Domaine!]/[!Limg!].mini.200x200.jpg"> 

	<meta property="og:title" content="[!LeTitre!]" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="[!Domaine!]/[!Lien!]" />
	<meta property="og:image" content="[!Domaine!]/[!Limg!]" />
	<meta property="og:description" content="[!LaDesc!]" />
	<meta property="og:site_name" content="[!Domaine!]" />
[/STORPROC]
