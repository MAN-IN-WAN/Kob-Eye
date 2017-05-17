[!R:=[!RES!]!]
[!CHECK:=1!]

//VERIFICATION
[IF [!R::Verify()!]][ELSE]
    [!CHECK:=0!]
    <div class="alert alert-danger">
        <ul>
            [STORPROC [!R::Error!]|E]
            <li>[!E::Message!]</li>
            [/STORPROC]
        </ul>
    </div>
[/IF]

[IF [!Valider!]=Valider la réservation]
    <div class="alert alert-success">
        La réservation a été validée avec succès.
    </div>
    //Validation de la réservation
    [!R::setValide()!]
    [!R::Save()!]
[/IF]

[IF [!Valider!]=Payer en carte bleue]
    [!RES::Save()!]
    [COOKIE Set|RES|RES]
    <div class="alert alert-success">
    La réservation a étée validée avec succès.
    </div>
    [REDIRECT][!Sys::getMenu(Reservations/Reservation)!]/[!RES::Id!]/Payer[/REDIRECT]
[/IF]

<div class="row">
    <div class="col-md-12">
        <form action="" method="POST">
            <h1>Détail de votre réservation</h1>
            [!Service:=[!R::getService()!]!]
            [!Court:=[!R::getCourt()!]!]
            <h3><b>Description: </b>[!Service::Titre!] pour [!Court::Titre!]</h3>
            <h3><b>Date: </b>le [DATE d/m/Y][!R::DateDebut!][/DATE]</h3>
            <h3><b>Nombre de participant(s) total(s):</b>[!R::NbParticipant!]</h3>
            [STORPROC [!R::getPartenaires()!]|Pa]
            <h3><b>Partenaire(s) adhérent(s):</b>
            <ul>
                [LIMIT 0|100]
                <li>[!Pa::Nom!] <span class="label label-primary" >[!Pa::Email!]</span></li>
                [/LIMIT]
            </ul></h3>
            [/STORPROC]
            <h3><b>Total :</b></h3>
            [STORPROC [!R::getLigneFacture()!]|Lf]
                <div class="alert alert-info">[!Lf::Libelle!] (x[!Lf::Quantite!]) <span class="label label-primary pull-right" >[!Utils::getPrice([!Lf::MontantTTC!])!] €</span></div>
            [/STORPROC]
            </ul></h3>
            <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!R::getTotal()!])!] €</span></h3>

            [IF [!CHECK!]&&[!R::Valide!]=]
                [IF [!R::getTotal()!]>0]
                    <input type="submit" class="btn btn-success btn-large btn-block" name="Valider" value="Payer en carte bleue" />
                [ELSE]
                    <input type="submit" class="btn btn-success btn-large btn-block" name="Valider" value="Valider la réservation">
                [/IF]
                [ELSE]
                    <div class="alert alert-success">
                        Cette réservation est validée.
                    </div>
                [/IF]
            [/IF]
            <br />
            <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        </form>
    </div>
</div>
