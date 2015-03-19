[IF 1]
	[COUNT Vitrine/Produit|C]
	<h1> COUNT [!C!] </h1>
	[!Nb:=[!Math::Floor([!C:/100!])!]!]
	[STORPROC [!Nb:+1!]|K]
		[STORPROC Vitrine/Produit|P|[!K:*100!]|100]
			<li> [!K!] [!Pos!] [!P::Nom!] - [!P::Save()!]</li>
		[/STORPROC]
	[/STORPROC]
[/IF]
