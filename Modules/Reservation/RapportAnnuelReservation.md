[IF [!Systeme::User::Public!]!=1||[!Ab!]=23686]

    [!AnneeP:=[!Annee!]!]
    [!AnneeP-=1!]
    [!DateDebut:=[!Utils::getTms(1/1/[!Annee!]) 00:00!]!]
    [!DateFin:=[!Utils::getTms(31/12/[!Annee!] 23:59)!]!]
    [!DateDebutP:=[!Utils::getTms(1/1/[!AnneeP!]) 00:00!]!]
    [!DateFinP:=[!Utils::getTms(31/12/[!AnneeP!] 23:59)!]!]

    [!TotalResa:=0!]
    [!TotalPers:=0!]
    [LIB HTML2PDF|html2pdf]
    [METHOD html2pdf|writeHTML]
        [PARAM]
        <page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
            <table class="page_header" cellspacing="0" cellspadding="0" border="1">
                <tr style="width:190mm;font-size:10px;background-color:#eee;">
                    <td colspan="4" style="font-size:14px;font-weight:bold;padding:20px;text-align:center;">Réservations [DATE d/m/Y][!DateDebut!][/DATE] au [DATE d/m/Y][!DateFin!][/DATE]</td>
                </tr>
                <tr style="width:190mm;font-size:10px;">
                    <th style="text-align:center;font-weight:bold;">Structure</th>
                    <th style="text-align:center;font-weight:bold;">Ville</th>
                    <th style="text-align:center;font-weight:bold;">Nb Résa [!Annee!]</th>
                    <th style="text-align:center;font-weight:bold;">Nb Pers [!Annee!]</th>
                </tr>
                [!TotResa:=0!]
                [!TGTotResa:=0!]
                [!TotPers:=0!]
                [!TGTotPers:=0!]
                // demande pour structures bien precises
                [STORPROC Reservation/Client|Cl|||Nom|ASC]
                    [!NbResa:=0!]
                    [!NbResaArt:=0!]
                    [!NbPersT:=0!]
                    [!NbPersTArt:=0!]
                    [STORPROC Reservation/Client/[!Cl::Id!]/Reservations/tmsCreate>=[!DateDebut!]&&tmsCreate<=[!DateFin!]|Res]
                        [!NbResa+=1!]
                        [!NbPers:=0!]
                        [COUNT Reservation/Reservations/[!Res::Id!]/Personne|NbPers]
                        [!NbPersT+=[!NbPers!]!]
                    [/STORPROC]

                    <tr>
                        <td style="width:90mm;font-size:12px;">[!Cl::Nom!] [IF [!Cl::Tel!]!=]<br/>[!Cl::Tel!][/IF][IF [!Cl::Mail!]!=]<br/>[!Cl::Mail!][/IF]</td>
                        <td style="width:20mm;font-size:12px;">[!Cl::Ville!]</td>
                        <td style="width:10mm;font-size:12px;text-align:right;padding-right:5px;">[!NbResa!] </td>
                        <td style="width:10mm;font-size:12px;text-align:right;padding-right:5px;">[!NbPersT!] </td>
                    </tr>
                    [!TotResa+=[!NbResa!]!]
                    [!TotPers+=[!NbPersT!]!]
                [/STORPROC]

                <tr style="width:190mm;font-size:10px;background-color:#ccc;">
                    <td colspan="2" style="text-align:right;font-size:20px;font-weight:bold;color:#ff0000;padding:20px;">Total GÉNÉRAL</td>
                    <td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;"> [!TotResa!]</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">[!TotPers!]</td>
                </tr>

            </table>
        </page>
        [/PARAM]
        [PARAM][/PARAM]
    [/METHOD]

    [!html2pdf::Output(RapportReservations.pdf)!]

[/IF]
