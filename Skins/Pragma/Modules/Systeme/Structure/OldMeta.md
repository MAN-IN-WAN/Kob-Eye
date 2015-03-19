// META MENU EN COURS
[!Men:=[!Systeme::CurrentMenu!]!]

[IF [!Men::Title!]!=][TITLE][!Men::Title!][/TITLE][/IF]
[IF [!Men::Description!]!=][DESCRIPTION][!Men::Description!][/DESCRIPTION][/IF]
[IF [!Men::Keywords!]!=][KEYWORDS][!Men::Keywords!][/KEYWORDS][/IF]

// METAS QUERY EN COURS
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
	[STORPROC [!Query!]|MetaQ]
		[IF [!MetaQ::TitleMeta!]!=][TITLE][!MetaQ::TitleMeta!][/TITLE][/IF]
		[IF [!MetaQ::DescriptionMeta!]!=][DESCRIPTION][!MetaQ::DescriptionMeta!][/DESCRIPTION][/IF]
		[IF [!MetaQ::KeywordsMeta!]!=][KEYWORDS][!MetaQ::KeywordsMeta!][/KEYWORDS][/IF]
		
	[/STORPROC]
[/IF]
[IF [!Chemin!]!=]
	[INFO [!Chemin!]|I]
	[IF [!I::TypeSearch!]=Direct]
		[STORPROC [!Chemin!]|MetaQ]
			[IF [!MetaQ::TitleMeta!]!=][TITLE][!MetaQ::TitleMeta!][/TITLE][/IF]
			[IF [!MetaQ::DescriptionMeta!]!=][DESCRIPTION][!MetaQ::DescriptionMeta!][/DESCRIPTION][/IF]
			[IF [!MetaQ::KeywordsMeta!]!=][KEYWORDS][!MetaQ::KeywordsMeta!][/KEYWORDS][/IF]
		[/STORPROC]
	[/IF]
[/IF]