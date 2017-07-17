//LISTE DES RESERVATIONS
[!DateDebut:=[!Utils::getTodayMorning()!]!]
[!DateFin:=[!Utils::getTodayEvening()!]!]
            <table class="page_header" cellspacing="0" cellspadding="0" border="1">
                <tr><th colspan="7"> <a href="/[!Sys::getMenu(Reservations/Reservation)!]/ResaJournee.print" data-title="Imprimer les réservations" class="btn btn-info pull-right btn-lg"><span class="glyphicon glyphicon-print" aria-hidden="true" ></span> Imprimer les réservations</a></th></tr>
                <tr><th colspan="7"><h1>Liste des réservations de [DATE d/m/Y H:i][!DateDebut!][/DATE] à [DATE d/m/Y H:i][!DateFin!][/DATE]</h1></th></tr>
                <tr><th>Début</th><th>Fin</th><th>Origine</th><th>Terrain</th><th>Client</th><th>Payé</th><th>Reste du</th></tr>
                [STORPROC Reservations/Reservation/DateDebut>[!DateDebut!]&&DateDebut<[!DateFin!]|R|||DateDebut|ASC]
 //               [STORPROC Reservations/Reservation/DateDebut>1500656000|R|||DateDebut|ASC]

                    <tr>
                        // Initialisation des variables
                        [!TypeTerrain:=!][!LeTerrain:=!][!LeClient:=!][!MtPaye:=0!][!MtAPayer:=0!]
                        // une réservation
                        [STORPROC Reservations/Court/Reservation/[!R::Id!]|C|||Nom|ASC]
                            [!LeTerrain:=[!C::Titre!]!]
                        [/STORPROC]
                        [STORPROC Reservations/TypeCourt/Court/[!C::Id!]|TC|||Nom|ASC]
                            [!TypeTerrain:=[!TC::Titre!]!]
                        [/STORPROC]
                        [STORPROC Reservations/Client/Reservation/[!R::Id!]|CL|||Nom|ASC]
                            [!LeClient:=[!CL::Nom!] [!CL::Tel!]!]
                        [/STORPROC]
                        [STORPROC Reservations/Reservation/[!R::Id!]/Facture|F|||Id|DESC]
                            [!MtPaye:=[!F::MontantTTC!]!]
                        [/STORPROC]
                        [STORPROC Reservations/Reservation/[!R::Id!]/LigneFacture|LF|||Id|DESC]
                            [!MtAPayer:=[!LF::MontantTTC!]!]
                        [/STORPROC]
                        <td>[DATE H:00][!R::DateDebut!][/DATE]</td>
                        <td>[DATE H:00][!R::DateFin!][/DATE]</td>
                        <td>[IF [!CL::Id!]=10]Ddf[ELSE]En ligne[/IF] (-[!R::Id!])</td>
                        <td>[!TypeTerrain!] - [!LeTerrain!]</td>
                        <td>[!LeClient!]<br/>
                            [STORPROC Reservations/Reservation/[!R::Id!]/StatusReservation|SP|||Id|ASC]
                               [STORPROC Reservations/StatusReservation/[!SP::Id!]/Partenaire|P|0|1]
                                    [!lacouleur:=#000!]
                                    [IF [!SP::Present!]=NC]
                                        // n'a pas répondu à la demande de participation
                                        [IF [!SP::Paye!]]
                                            [!lacouleur:=green!]
                                            <span style="color:[!lacouleur!];">Présence non confirmée mais Payé Oui :</span> [!P::Nom!] - [!P::Prenom!]<br>
                                        [/IF]
                                        [IF [!SP::Paye!]=0]
                                            [!lacouleur:=#ccc!]
                                            <span style="color:[!lacouleur!];">Présence non confirmée et Non Payé  :</span>[!P::Nom!] - [!P::Prenom!]<br>
                                        [/IF]
                                    [/IF]
                                    [IF [!SP::Present!]=Oui]
                                        [IF [!SP::Paye!]]
                                            [!lacouleur:=green!]
                                            <span style="color:[!lacouleur!];">Présence confirmée Payé Oui :</span> [!P::Nom!] - [!P::Prenom!]<br>                                                          [/IF]
                                        [IF [!SP::Paye!]=0]
                                            [!lacouleur:=red!]
                                            <span style="color:[!lacouleur!];">Présence confirmée mais Non Payé  :</span>[!P::Nom!] - [!P::Prenom!]<br>
                                        [/IF]
                                    [/IF]
                                    [IF [!SP::Present!]=Non]
                                        [IF [!SP::Paye!]]
                                            [!lacouleur:=orange!]
                                            <span style="color:[!lacouleur!];">ABSENT Payé Oui :</span> [!P::Nom!] - [!P::Prenom!]<br>                                                                      [/IF]
                                        [IF [!SP::Paye!]=0]
                                            [!lacouleur:=red!]
                                            <span style="color:[!lacouleur!];">ABSENT et Non Payé  :</span>[!P::Nom!] - [!P::Prenom!]<br>
                                        [/IF]
                                    [/IF]
                                [/STORPROC]
                            [/STORPROC]
                        </td>
                        <td style="text-align: right;">[IF [!CL::Id!]=10]0[ELSE][!MtPaye!][/IF]</td>
                        <td style="text-align: right;">
                            [IF [!CL::Id!]=10]
                                [!Tot:=[!MtAPayer!]!]
                                [!Tot-=[!MtPaye!]!]
                                [!Tot!]
                            [ELSE]
                                [!MtAPayer!]
                            [/IF]
                        </td>
                    </tr>
                [/STORPROC]
            </table>
