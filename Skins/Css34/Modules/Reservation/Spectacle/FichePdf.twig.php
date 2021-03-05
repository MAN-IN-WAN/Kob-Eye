<?php
include $_SERVER['DOCUMENT_ROOT']."/Class/Lib/HTML2PDF.class.php";

$texte = '<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt"><div class="FicheSpectacle">';
$Spectacle = Sys::getOneData("Reservation",$vars['Query']);
$Organisations = Sys::getData("Reservation",'Reservation/Organisation/Spectacle/'.$Spectacle->Id);
$PdfSpectacle = new HTML2PDF();
$PdfSpectacle->setTestTdInOnePage(false);
$skin = Sys::$Skin;
$domain = Sys::$domain;
$dir = getcwd();
$logo = $dir."/Skins/".$skin."/Img/Img/logo.jpg";

$handi = $dir."/Skins/".$skin."/Img/Img/handi.jpg";
$handiRed = $dir."/Skins/".$skin."/Img/Img/handiRed.jpg";
$logoSpectacle = $dir.'/'.$Spectacle->Logo;
$dateDebut = date("d-m-Y",$Spectacle->DateDebut);
$dateFin = date("d-m-Y",$Spectacle->DateFin);

$AccesHand = 0;
$SalleAdresse = "";
$SalleTelInfo = "";
$SiteWebSalle = "";

if ( $dateDebut == $dateFin){
    $texte .= 'Le '.$dateDebut.'<br>'.$Spectacle->Nom;
}else{
    $texte .= $Spectacle->Nom.' du '.$dateDebut.' au '.$dateFin;
}
$texte .= '<table class="Detail" cellspacing="2" cellpadding="0" >
            <tr >
                <td style="width:160mm;overflow:hidden;" colspan="2">
                    <table class="colonneDetailD" cellspacing="2" cellpadding="0"  style="text-align:justify;width:185mm;">
                        <tr>
                            <td class="Entete" style="text-align:center;width:125mm;" >
                                <h3>
                                    <br><br><br>'.$Spectacle->Nom.'
                                </h3>
                            </td>
                            <td style="text-align:right;width:60mm;" >
                                <img src="'.$logo.'" alt="'.$Spectacle->Nom.'" title="'.$Spectacle->Nom.'" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="width:160mm;padding-top:5px;margin:5px">
							<td class="ColonneGauche" style="width:50mm;overflow:hidden;">
								<table class="colonneDetailG" cellspacing="2" cellpadding="0">
									<tr>
                                        <td colspan="2" class="ImgTable">';
                                            if ($Spectacle->Logo)$texte.='<img src="'.$logoSpectacle.'"  alt="'.$Spectacle->Nom.'" title="'.$Spectacle->Nom.'" style="height:150px;width:200px;" />';
                                            $texte .='</td>
                                    </tr>
								</table>
							</td>
							<td class="ColonneDroite" style="width:90mm;overflow:hidden;">
								<table class="colonneDetailD" cellspacing="2" cellpadding="0"  style="text-align:justify;">
									<tr class="Genre">
									    <td class="Nom">';
									    if($Spectacle->Genre !='')
									        $texte .= 'Type de Spectacle :<span style="font-weight:bold;">'.$Spectacle->Genre.'</span>';
                                        else{
                                            $texte .= 'Type de Sortie :<span style="font-weight:bold;">'.$Spectacle->TypeSortie.'</span>';
                                        }
                                        $texte .= '</td>
                                    </tr>
									<tr>
									    <td class="Nom">Public :<span style="font-weight:bold;">'.$Spectacle->TypePublic.'</span></td>
									</tr>
									<tr>
									    <td class="Nom"> Duree :<span style="font-weight:bold;">';
                                        if($Spectacle->Duree>0){
                                            $texte .= ' '.$Spectacle->getduree().' ';
                                        }else{
                                            $texte .= 'NC';
                                        }
                                        $texte .= '</span></td>
                                    </tr>
									<tr>
									    <td class="Nom"> </td>
									</tr>';
                                    foreach ($Organisations as $value){
                                        $texte .= '<tr><td class="Cell_GrisFonce" style="text-decoration:underline;">Partenaires</td></tr>
                                        <tr><td class="Nom">'.$value->Nom.'</td></tr>
                                        <tr><td class="Adresse">'.$value->Adresse.'</td></tr>
                                        <tr><td class="Ville">'.$value->Codpos.' '.$value->Ville.'</td></tr>
                                        <tr><td class="Tel">Tél: '.$value->Tel.'</td></tr>';
                                        if (preg_match("#^http#",$value->SiteWeb) === 1){
                                            $texte .= '<tr><td class="SiteWeb"><a href="'.$value->SiteWeb.'" onclick="window.open(this.href);return false;" title="'.$value->Nom.'">'.$value->SiteWeb.'</a></td></tr>';
                                        }else{
                                            $texte .= '<tr><td class="SiteWeb"><a href="http://'.$value->SiteWeb.'" onclick="window.open(this.href);return false;" title="'.$value->Nom.'">'.$value->SiteWeb.'</a></td></tr>';
                                        }
                                    }
                                    $texte .= '</table></td></tr>
                                    <tr><td  colspan="2" style="height:10px;"></td></tr>';
                                        if($Spectacle->Disponibilite === 0){
                                            $texte .= '<tr style="width:160mm;padding-top:5px;margin:20px 5px;">
                                                        <td class="InformationDispo" colspan="2" style="width:160mm;">Plus d\'invitation disponible pour nos partenaires sociaux</td>
                                                       </tr>';
                                        }
                                        if ($Spectacle->Lieu != ''){
                                            $texte .= '<tr><td colspan="2">Lieu : '.$Spectacle->Lieu.'</td></tr>';
                                        }
                                    $texte .= '<tr><td  class="InformationsPresentation" colspan="2" style="width:190mm;text-align:justify;">'.$Spectacle->Presentation.'</td></tr>
                                                <tr><td  class="Texte" colspan="2" style="width:190mm;text-align:justify;">'.$Spectacle->Resume.'</td></tr></table>';

                                         $evenements = Sys::getData("Reservation",'Reservation/Spectacle/'.$Spectacle->Id.'/Evenement/DateDebut>='.time());
                                         if (!empty($evenements)){
                                             $texte .= '<table class="Evenements" cellpadding="0" cellspacing="2" style="border-top:1px dotted #ccccc;border-left:1px dotted #ccccc;border-right:1px dotted #ccccc;">
                                                <tr style="width:160mm;padding-top:5px;margin:20px 5px;">
                                                    <th style="width:40mm;border-right:1px dotted #ccccc;border-bottom:1px dotted #ccccc;text-align:center;">Dates</th>
                                                    <th style="width:60mm;border-right:1px dotted #ccccc;border-bottom:1px dotted #ccccc;text-align:center;">Info Lieu</th>
                                                    <th style="width:30mm;border-right:1px dotted #ccccc;border-bottom:1px dotted #ccccc;text-align:center;">Accès<br>handicapé</th>
                                                    <th style="width:10mm;border-bottom:1px dotted #ccccc;text-align:center;">Dispo</th>
                                                </tr>';

                                             foreach ( $evenements as $items) {
                                                 $salles = Sys::getData("Reservation",'Reservation/Salle/Evenement/'.$items->Id);
                                                 foreach($salles as $values){
                                                     $AccesHand = $values->Handi;
                                                     if(!empty($values->Adresse))$SalleAdresse=$values->Adresse.'<br>';
                                                     if(!empty($values->CodPos))$SalleAdresse.=$values->CodPos;
                                                     if(!empty($values->Ville))$SalleAdresse.=$values->Ville;
                                                     if(!empty($values->Transport))$SalleAdresse.='<br>'.$values->Transport;
                                                     if(!empty($values->TelInfo))$SalleAdresse.='<br>'.$values->TelInfo;
                                                     if(!empty($values->SiteWeb))$SalleAdresse.='<br>'.$values->SiteWeb;
                                                 }
                                                 $texte .= '<tr style="width:160mm;padding-top:5px;margin:5px;border-bottom:1px dotted #ccccc;">
									                            <td class="Texte" style="padding:5px;border-right:1px dotted #ccccc;border-bottom:1px dotted #ccccc;width:35mm;">';
                                                                $LeDebut = date("D/M/Y",$items->DateDebut);
                                                                $LaFin = date("D/M/Y",$items->DateFin);
                                                                if ($LeDebut === $LaFin){
                                                                    $texte .= 'Le '.date("d/m/Y",$items->DateDebut).'<br>de '.date("H:i",$items->DateDebut).' à '.date("H:i",$items->DateFin).'';
                                                                }else{
                                                                    $texte .= 'Du '.date("d/m/Y",$items->DateDebut).'<br> Au '.date("d/m/Y",$items->DateFin).' à '.date("H:i",$items->DateFin).'';
                                                                }
                                                 $texte .= '</td><td class="Texte" style="padding:5px;border-right:1px dotted #ccccc;border-bottom:1px dotted #ccccc;width:105mm;">'.$SalleAdresse.'</td>';
										                    if ($AccesHand == 1){
                                                                $texte .= '<td class="AutreTexte TexteImg" style="text-align:center;width:15mm;padding:5px;border-bottom:1px dotted #ccccc;border-right:1px dotted #ccccc;" >
											                    <img src="'.$handi.'" width="20" height="20" alt="Accès Handicapés" title="Accès Handicapés" />';
										                    }else{
										                        $texte .= '<td class="TexteImg"  style="text-align:center;width:15mm;padding:5px;border-bottom:1px dotted #ccccc;border-right:1px dotted #ccccc;" >
											                    <img src="'.$handiRed.'" width="20" height="19" alt="Attention : pas d\'accès handicapés" title="Attention : pas d\'accès handicapés" /><br>Non';
										                    }
                                                 $texte .= '</td>';
                                                            if ($items->NbPlace == 0) {
                                                                $texte .= '<td class="Texte" style="width:10mm;padding:5px;border-bottom:1px dotted #ccccc;text-align:center;">' . $items->NbPlace . '</td></tr>';
                                                            }else{
                                                                $texte .= '<td class="AutreTexte" style="width:10mm;padding:5px;border-bottom:1px dotted #ccccc;text-align:center;">' . $items->NbPlace . '</td></tr>';
                                                            }
                                             }
                                             $texte .= '</table>';
                                            }

$texte .= '</div></page>';
//                                         die($texte);
$PdfSpectacle->writeHTML($texte);
ob_get_clean();
$PdfSpectacle->Output('Spectacle_'.$Spectacle->Id.'_'.date("Ymd",time()).'.pdf');
ob_end_clean();
