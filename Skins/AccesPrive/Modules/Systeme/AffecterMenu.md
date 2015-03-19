[STRORPROC Systeme/User/Id>51|U]

	[OBJ Systeme/Menu|M]
	[!M::Set(Titre, Shops)!]
	[!M::Set(Url, Shops)!]
	[!M::AddParent(Systeme/User/[!U::Id!])!]
	[!M::Save()!]

	
	[OBJ Systeme/Menu|M2]
	[!M2::Set(Titre, Shop List)!]
	[!M2::Set(Url, ShopList)!]
	[!M2::Set(Alias, Distributeur/Shop)!]
	[!M2::Set(Filtre, userCreate=)!]
	[!M2::AddParent(Systeme/Menu/[!M::Id!])!]
	[!M2::Save()!]

[/STORPROC]