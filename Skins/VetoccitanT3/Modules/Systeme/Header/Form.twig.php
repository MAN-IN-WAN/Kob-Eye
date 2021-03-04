<?php
require_once("Class/Lib/Mail.class.php");

$values = json_decode(file_get_contents('php://input'),true);
$token = $values["token"];
$ok = false;
if ($token){
    require_once 'Class/Lib/ReCaptcha/autoload.php';
    $secret ='6LcivUYaAAAAAKZPH7Nu3myCvP5QHH-uaJLvI7d0';
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    $resp = $recaptcha->verify($token, "vetoccitan.fr");
    var_dump($resp);
    $ok = $resp->isSuccess();
}

if(!$ok){
    echo json_encode(["success"=>false,
                        "cause"=>"captcha"]);
    exit(0);
}


// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=" . Sys::$domain);
$LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);
//$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent = Sys::getOneData("Vetoccitan","Adherent/9");
$vars['Tel'] = $lAdherent->Tel;
$vars['EmailContact'] = $lAdherent->EmailContact;
$vars['Nom'] = $lAdherent->Nom;
$vars['Adresse'] = $lAdherent->Adresse . " " . $lAdherent->CodePostal . " " . $lAdherent->Ville . " " . $lAdherent->France;
$vars['Retour'] = Sys::$CurrentMenu->Url;
$mailSend = $lAdherent->EmailContact;

if (!empty($values)&& !empty($values['formValues']["Mail"])){

    $vars['nom'] = $values['formValues']['Nom'];
    $vars['prenom'] = $values['formValues']['Prenom'];
    $vars['email'] = $values['formValues']['Mail'];
    $vars['telephone'] = $values['formValues']['Tel'];
    $vars['message'] = $values['formValues']['Message'];
    $vars['departement'] = $values['formValues']['Departement']?$values['formValues']['Departement']:"Non communiqué";
    $vars['ville'] = $values['formValues']['Ville']?$values['formValues']['Ville']:"Non communiqué";;

    $Mail = new Mail();
    $Mail->Subject("Formulaire de contact");
    $Mail->From($vars['email']);
//    $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
//    $Mail->To($mailSend);
    $Mail -> To('psabate@abtel.fr');
//    $Mail -> Cc( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
//    $Mail -> Bcc('enguer@enguer.com');
    $bloc = new Bloc();
    $mailContent= "
        <div>
            <ul>
                <li>
                    <b>Nom : </b> ".$vars['nom']."
                </li>
                <li>
                    <b>Prénom : </b> ".$vars['prenom']."
                </li>
                <li>
                    <b>Mail : </b> ".$vars['email']."
                </li>
                <li>
                    <b>Téléphone : </b> ".$vars['telephone']."
                </li>
                <li>
                    <b>Département : </b> ".$vars['departement']."
                </li>
                <li>
                    <b>Ville : </b> ".$vars['ville']."
                </li>
            </ul>
            <div>
                <b>Message : </b>
                <div>
                    ".$vars['message']."
                </div>
            </div>

        </div>
    ";

    $bloc->setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
    $Pr = new Process();
    $bloc->init($Pr);
    $bloc->generate($Pr);
    $Mail->Body($bloc->Affich());
//    if (!$this->Cloture)
    $Mail->Send();

    echo json_encode(["success"=>true]);
    exit(1);
}
echo json_encode(
    [
        "success"=>false,
        "cause"=>"beurk"
    ]
);
exit(0);


