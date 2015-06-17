// Acheteur connecté
[!Panier:=[!CurrentClient::getPanier()!]!]
[!TabReducCodePromo:=[!Panier::getReductionCodePromo([!CodePromo!],[!CurrentClient::Id!])!]!]

[IF [!TabReducCodePromo::Ok!]]
    [!Panier::setCodePromo([!CodePromo!])!]
[/IF]

{
	"ReducMontant":[!TabReducCodePromo::Montant!],
	"ReducDesc":"[!TabReducCodePromo::Desc!]",
	"ReducOk":"[!TabReducCodePromo::Ok!]",
	"PortOffert":"[!TabReducCodePromo::PortOffert!]",
	"Message":"[!TabReducCodePromo::Message!]"
}


