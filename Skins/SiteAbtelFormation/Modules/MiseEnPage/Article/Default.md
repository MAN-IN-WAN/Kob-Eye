[INFO [!Query!]|I]

[IF [!Systeme::CurrentMenu::Url!]=actualite]
	[MODULE MiseEnPage/Article/Actualite?Chemin=[!Query!]]
[ELSE]
	[MODULE MiseEnPage/Article/Basic?Chemin=[!Query!]]
[/IF]