// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 
[!Panier:=[!CLCONN::getPanier()!]!]
[!TabReducCodePromo:=[!Panier::getReductionCodePromo([!CodePromo!],[!CLCONN::Id!])!]!]



{
	"ReducMontant":"[!TabReducCodePromo::Montant!]",
	"ReducDesc":"[!TabReducCodePromo::Desc!]",
	"ReducOk":"[!TabReducCodePromo::Ok!]",
	"PortOffert":"[!TabReducCodePromo::PortOffert!]",
	"Message":"[!TabReducCodePromo::Message!]"
}


