[IF [!Systeme::User::Admin!]]
	<head>
		<script src="/Skins/AdminV2/Js/mootools.js"></script>
		<script src="/Skins/AdminV2/Js/mootools-more.js"></script>
		<script src="/Skins/AdminV2/Js/cal.js"></script>
		<script src="/Skins/AdminV2/Js/datepicker.js"></script>
		<script src="/Skins/Public2012/Js/autocomplete.js"></script>
		<link href="/Skins/AdminV2/Js/datepicker_vista/datepicker_vista.css" rel="stylesheet" type="text/css" ></link>
		<link href="/Skins/Public2012/Css/autocomplete.css" rel="stylesheet" type="text/css" ></link>
	</head>
	<div id="Container" style="overflow:auto;margin-bottom:40px;">
	    [STORPROC [!Query!]|Spec][/STORPROC]
	    <h2 style="margin-left:30px;">Génération d'évenements pour le spectacle : <span style="color:#006fb4;font-size:18px;">[!Spec::Nom!]</span></h2>
	    <h3 style="margin-left:30px;color:#ff0000;"> FONCTION ACTIVE QUI GENERE DES EVENEMENTS DES CLIC SUR BOUTON OK </h3>
	    [!ErreurTrouvee:=!]
	    [!ErreurDates:=!][!ErreurSalle:=!][!ErreurNbPlaces:=!][!ErreurDeb:=!][!ErreurFin:=!][!ErreurNbEve:=!]
	    [!ErreurcloResa:=!][!ErreurHcloResa:=!][!ErreurMcloResa:=!]
    	<div style="margin:0;font-size:15px;margin:20px;">
            <form action="" name="CreaEve" >
                <input type="hidden" value="1" name="CreaEve" >
                <input type="hidden" value="[!Ab!]" name="Ab" >
                <br />
                <table border="1" style="overflow:show;">
                    <tr>
                        <td style="text-align:center;width:15px;">Nombre<br />d'évènements</td>
                        <td style="text-align:center;">Date<br />Premier évènement<br/> et heure de début</td>
                        <td style="text-align:center;">Date fin<br />premier évènement<br/> et heure de fin</td>
                        <td style="text-align:center;">Nombre de places<br />mis à disposition<br/>par évenement</td>
                        <td style="text-align:center;">Clôture des réservations<br /> à j - x de la date de fin<br /> de l'évènement </td>
                        <td style="text-align:center;">Horaire <br /> fin réservation<br /> hh:mm</td>
                        <td style="text-align:center;">
                            Salle
                            <div style="font-style:italic;font-size:10px;display:block;">Résultats limités à 10, au delà veuillez affiner votre recherche<br /> vous pouvez saisir plusieurs mot<br/>ex: theatre montpellier ou musee sete...</div>
                        </td>
                        <td style="text-align:center;">
                            Evenement Mensuel à créer
                        </td>
                        <td style="text-align:center;">
                            Jours d'ouverture de la salle
                            <div style="font-style:italic;font-size:10px;display:block;">N'ont pris en compte en cas de création d'évènement qui dure sur plusieurs jours</div>
                            //<div style="font-style:italic;font-size:10px;display:block;">ex: création d'un évenement ou l'on met à disposition 10places par semaine<br/> et qui dure 10 semaines<br/>ex: on créé un évenement qui va du lundi xx/xx/xx au vendredi xx/xx/xx et on demande 10 répétitions<br /> création</div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;width:15px;vertical-align:middle;"><input id="NbEve" type="text" value="[!NbEve!]" name="NbEve" size="3" style="text-align:right;"></td>
                        <td style="text-align:center;width:15px;vertical-align:middle;">
                            <input id="start" class="ncalendar" type="text" value="[!start!]" name="start" size="11" >
                        </td>
                        <td style="text-align:center;width:15px;vertical-align:middle;">
                            <input id="stop" class="ncalendar" type="text" value="[!stop!]" name="stop" size="11" >
                                <script type="text/javascript">
                                new DatePicker('.ncalendar', { pickerClass: 'datepicker_vista', timePicker:true, format:'Y-m-d H:i:s', allowEmpty: true });
                                </script>
                        </td>
                        <td style="text-align:center;width:15px;vertical-align:middle;"><input id="NbPlaces" type="text" value="[!NbPlaces!]" name="NbPlaces" size="3" style="text-align:center;"></td>
                        <td  style="text-align:center;width:15px;vertical-align:middle;"><input id="[IF [!cloResa!]!=][!cloResa!][ELSE]1[/IF]" type="text" value="[!cloResa!]" name="cloResa" size="3" style="text-align:center;"></td>
                        <td style="text-align:center;vertical-align:middle;">
                            <input id="cloHResa" type="text" value="[IF [!cloHResa!]!=][!cloHResa!][ELSE]08[/IF]" name="cloHResa"  size="2" style="text-align:center;">
                            <span> : </span>
                            <span><input id="cloMResa" type="text" value="[IF [!cloMResa!]!=][!cloMResa!][ELSE]00[/IF]" name="cloMResa"  size="2" style="text-align:center;"></span>
                        </td>
                        <td style="text-align:center;width:35px;vertical-align:middle;">
                            <input id="Salle" type="text" value="[!Salle!]" name="Salle" autocomplete="off" >
                             <div class="ACRelative_Salle"></div>
                            <script type="text/javascript">autoCompleteField('Salle', 'Reservation/Salle','', 'Id','Nom', null,null,null,20);</script>
                        </td>
                        <td style="text-align:center;width:35px;vertical-align:middle;">
                            Mois <input id="Mois" type="checkbox" value="1" name="Mois" >
                        </td>
                        <td style="text-align:center;width:300px;vertical-align:middle;">
                        <table>
                            <tr><td>Lundi</td><td><input id="Lundi" type="checkbox" value="1" name="Lundi"  checked></td><td>Mardi</td><td><input id="Mardi" type="checkbox" value="1" name="Mardi" checked></td><td>Mercredi</td><td><input id="Mercredi" type="checkbox" value="1" name="Mercredi"checked></td></tr>
                            <tr><td>Jeudi</td><td><input id="Jeudi" type="checkbox" value="1" name="Jeudi" checked></td><td>Vendredi</td><td><input id="Vendredi" type="checkbox" value="1" name="Vendredi" checked></td><td>Samedi</td><td><input id="Samedi" type="checkbox" value="1" name="Samedi" checked></td></tr>
                            <tr><td>Dimanche</td><td><input id="Dimanche" type="checkbox" value="1" name="Dimanche" checked></td>
                            </tr>
                        </tr></table>
                        </td>
                        <td align="center"><button type="submit">OK</button></td>
                    </tr>
                </table>	
            </form>
            <div>
                Cette fonction Vous permet de créer :<br />
                <ul>
                    <li>des événements qui dure une journée 
                        <ul>
                            <li>=> il vous faut cocher les jours de semaine pour lesquels vous voulez créer des évenements.</li>
                            <li>=> on créera x évenements sur un jour en ne créant que pour les jours de semaine cochés.</li>
                        </ul>
                    </li>
                    <li>des événements mensuels (du premier au dernier jours du mois).
                        <ul>
                            <li>=> il suffit de mettre un jour de début et un jour de fin et cocher Mois </li>
                            <li>=> le premier évenement sera créé de la date de début jusqu'à la fin du mois en cours</li>
                            <li>=> les autres évenements seront créés avec du premier au dernier jours des mois </li>
                            <li>=> on ne tient pas compte des jours de semaines cochés</li>
                        </ul>
                    </li>
                    <li>des événements sur plusieurs jours récurrents sur la même semaine
                        <ul>
                            <li>=> créer un évenement qui va du mardi au jeudi par exemple pendant 10 semaines</li>
                            <li>=> on va créer 10 événements du mardi au jeudi</li>
                            <li>=> ne peut fonctionner que pour des évenements avec des jours consécutifs</li>
                            <li>=> on ne tient pas compte des jours de semaines cochés</li>
                        </ul>
                    </li>
                </ul>
            </div>		
        </div>
    	[IF [!CreaEve!]&&[!NbeEve!]!=0]
            // vérification de la cohérence de la demande
            [IF [!NbEve!]!=&&[!NbEve!]!=0][ELSE][!ErreurNbEve:=Le nombre d'événement à créer est obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!start!]=][!ErreurDeb:=Date du premier événement obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!stop!]=][!ErreurFin:=Date fin événement obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!start!]>[!stop!]][!ErreurDates:=Date de début supérieure à date de fin!][!ErreurTrouvee:=1!][/IF]
            [IF [!Salle!]!=][ELSE][!ErreurSalle:=La salle est obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!NbPlaces!]!=&&[!NbPlaces!]!=0][ELSE][!ErreurNbPlaces:=Le nombre de places est obligatoire!][!ErreurTrouvee:=1!][/IF]
    //		[IF [!cloResa!]!=&&[!cloResa!]!=0][ELSE][!ErreurcloResa:=Le nombre de jour ante cloture évenement est obligatoire!][!ErreurTrouvee:=1!][/IF]
    // Appel Frederique le 2/11/16 : on autorise la cloture le jour même
            [IF [!cloResa!]!=][ELSE][!ErreurcloResa:=Le nombre de jour ante cloture évenement est obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!cloHResa!]!=][ELSE][!ErreurHcloResa:=Heure de fin réservation est obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!cloMResa!]!=][ELSE][!ErreurMcloResa:=Minute de fin réservation est obligatoire!][!ErreurTrouvee:=1!][/IF]
            [IF [!ErreurTrouvee!]!=]
                <div style="border:1px solid #ff0000;padding:10px;font-weight:bold;color:#094674;">
                    <u><b>Incohérence dans votre demande</b></u><br/><br/>
                    [IF [!ErreurNbEve!]][!ErreurNbEve!]<br />[/IF]
                    [IF [!ErreurDeb!]][!ErreurDeb!]<br />[/IF]
                    [IF [!ErreurFin!]][!ErreurFin!]<br />[/IF]
                    [IF [!ErreurDates!]][!ErreurDates!]<br />[/IF]
                    [IF [!ErreurNbPlaces!]][!ErreurNbPlaces!]<br />[/IF]
                    [IF [!ErreurSalle!]][!ErreurSalle!]<br />[/IF]
                    [IF [!ErreurcloResa!]][!ErreurcloResa!]<br />[/IF]
                    [IF [!ErreurHcloResa!]][!ErreurHcloResa!]<br />[/IF]
                    [IF [!ErreurMcloResa!]][!ErreurMcloResa!]<br />[/IF]
                </div>
            [ELSE]
                [!Deb:=[!start!]!]
                [!Fin:=[!stop!]!]
                [!HoraireDeb:=[!Utils::getDate(H:i,[!Deb!])!]!]
                [!HoraireFin:=[!Utils::getDate(H:i,[!Fin!])!]!]
                [!JourDeb:=[!Utils::getDate(D,[!Deb!])!]!]
                [!NumJourDeb:=[!Utils::getDate(d,[!Deb!])!]!]
                [!Eve:=!]
                [!Ecart:=[!stop!]!]
                [!Ecart-=[!start!]!]
                [IF [!Ecart!]>86400]
                    [IF [!Mois!]=1][!Eve:=Mois!][/IF]
                    [!Ecart/=86400!]
                    [!Ecart2:=[!Utils::entiere([!Ecart!])!]!]
                    [!Ecart:=[!Ecart2!]!]
                    Ecart supérieur à 1 jour !!!!!<br>
                [ELSE]
                    [!Ecart:=1!]
                    [IF [!Lundi!]]
                        [!JoursOk+=Mon-!]
                    [/IF]
                    [IF [!Mardi!]]
                        [!JoursOk+=Tue-!]
                    [/IF]
                    [IF [!Mercredi!]]
                        [!JoursOk+=Wed-!]
                    [/IF]
                    [IF [!Jeudi!]]
                        [!JoursOk+=Thu-!]
                    [/IF]
                    [IF [!Vendredi!]]
                        [!JoursOk+=Fri-!]
                    [/IF]
                    [IF [!Samedi!]]
                        [!JoursOk+=Sat-!]
                    [/IF]
                    [IF [!Dimanche!]]
                        [!JoursOk+=Sun!]
                    [/IF]
                [/IF]
                // Generation des évènements
                <div style="font-family:Arial;font-size:12px;overflow:hidden;margin:;20px;">
                    <table border="1">
                        <tr>
                            <td>v</td>
                            <td>Date Evenement</td>
                            <td>Date Fin Evenement</td>
                            <td>Date Fin Réservation</td>
                            <td>Salle</td>
                            <td>Places à dispo</td>
                        </tr>
                        [STORPROC [!NbEve!]]
                            <tr>
                                <td>[!Pos!]</td>
                                <td>[!Utils::getDate(d/m/Y - H:i,[!Deb!])!]</td>
                                <td>[!Utils::getDate(d/m/Y - H:i,[!Fin!])!]</td>
                                <td>
                                    [!Cloture:=[!Fin!]!]
                                    [!Cloture2:=[!cloResa!]!]
                                    [!Cloture2*=86400!]
                                    [!Cloture-=[!Cloture2!]!]
                                    [!DateA:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Cloture!])!] [!cloHResa!]:[!cloMResa!])!]!]
                                    [!Utils::getDate(d/m/Y - H:i,[!DateA!])!] 
                                </td>
                                <td>
                                    [STORPROC Reservation/Salle/[!Salle!]|Sa|0|1][!Salle!] - [!Sa::Nom!][/STORPROC]
                                </td>
                                <td>[!NbPlaces!]</td>
                            </tr>
                     //       [IF [!SERVER::REMOTE_ADDR!]=185.87.66.101][!Spec::Nom!] / DateCloture  : [!DateA!] /  DateFin : [!Fin!][/if]
                            [OBJ Reservation|Evenement|EvCrea]
                            [METHOD EvCrea|Set]
                                [PARAM]DateCloture[/PARAM]
                                [PARAM][!DateA!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|Set]
                                [PARAM]DateDebut[/PARAM]
                                [PARAM][!Deb!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|Set]
                                [PARAM]Nom[/PARAM]
                                [PARAM][!Spec::Nom!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|Set]
                                [PARAM]DateFin[/PARAM]
                                [PARAM][!Fin!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|Set]
                                [PARAM]NbPlace[/PARAM]
                                [PARAM][!NbPlaces!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|Set]
                                [PARAM]Valide[/PARAM]
                                [PARAM]1[/PARAM]
                            [/METHOD]
                            // !!! accord donné par mail de jérémy chassang le 2/10/2016 à 15:46:47
                            [METHOD EvCrea|AddParent]
                                [PARAM]Reservation/Spectacle/[!Spec::Id!][/PARAM]
                            [/METHOD]
                            [METHOD EvCrea|AddParent]
                                [PARAM]Reservation/Salle/[!Salle!][/PARAM]
                            [/METHOD]
                            //[IF [!SERVER::REMOTE_ADDR!]!=185.87.66.101]   
                             [METHOD EvCrea|Save][/METHOD]
                            // [/IF]
                            // génération évenement suivant
                            // je pars de la fin du précédent + 1 jour
                            [!Deb:=[!Fin!]!]
                            [!Deb+=86400!]
                            [!Ok:=0!]
                            // je vérifie que le jour de début est un jour autorisé soit par les jours ouvrés soit par la similitude de la demande
                            // si on crée un évènement du mardi au dimanche par exemple
                            // à voir
                            [IF [!Eve!]=Mois]
                                [!Deb:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Fin!])!] [!HoraireDeb!])!]!]
                                [!Deb+=86400!]
                                [!Fin:=[!Utils::getTms([!Utils::getDate(31/m/Y,[!Deb!])!] [!HoraireFin!])!]!]
                                [!MDeb:=[!Utils::getDate(m,[!Deb!])!]!]
                                //[!Deb!]/[!Fin!]=>[!MDeb!]=
                                [STORPROC 4]
                                    [!MFin:=[!Utils::getDate(m,[!Fin!])!]!]
                                    //[!MFin!]<br/>
                                    [IF [!MDeb!]!=[!MFin!]]
                                        [!Fin-=86400!]
                                    [/IF]
                                [/STORPROC]
                            [ELSE]
                                [STORPROC 7]
                                    [!JourEncours:=[!Utils::getDate(D,[!Deb!])!]!]
                                    [IF [!Ecart!]!=1]
                                        [IF [!JourEncours!]!=[!JourDeb!]]
                                            [!Deb+=86400!]
                                        [/IF]
                                    [ELSE]
                                       // JourOk :  [!JoursOk!] Jour en cours : [!JourEncours!] -</br>
                                        [IF [!JoursOk!]~[!JourEncours!]]
                                            [!Ok:=1!]
                                        [ELSE]
                                            [IF [!Ok!]=0][!Deb+=86400!][/IF]
                                        [/IF]
                                    [/IF]
                                [/STORPROC]
                                [!Deb:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Deb!])!] [!HoraireDeb!])!]!]
                                [!LaFin:=[!Deb!]!]
                                [IF [!Ecart!]!=1]
                                    [STORPROC [!Ecart!]]
                                        [!LaFin+=86400!]
                                        [!Fin:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!LaFin!])!] [!HoraireFin!])!]!]
                                    [/STORPROC]
                                [ELSE]
                                    [!Fin:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!LaFin!])!] [!HoraireFin!])!]!]
                                [/IF]
                            [/IF]
                        [/STORPROC]				
                    </table>				
                </div>
            [/IF]
	 [/IF]
[ELSE]
	vous n'avez pas accès à cette fonctionnalité
[/IF]	
	
	
	
	
