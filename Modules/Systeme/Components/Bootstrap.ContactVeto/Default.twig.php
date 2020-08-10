<?php
require_once ("Class/Lib/Mail.class.php");


// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);
$lAdherent = $LeClient->getOneChild("Adherent");
$vars['Adresse'] = $lAdherent->Adresse." ".$lAdherent->CodePostal." ".$lAdherent->Ville." ".$lAdherent->France;
$vars['Retour']=Sys::$CurrentMenu->Url;
$mailSend=$lAdherent->EmailContact;

if (isset($_POST['flag'])){
    $vars['post'] = true;
    $vars['nom'] = $_POST['nom'];
    $vars['prenom'] = $_POST['prenom'];
    $vars['email'] = $_POST['email'];
    $vars['telephone'] = $_POST['telephone'];
    $vars['message'] = $_POST['message'];

    $Mail = new Mail();
    $Mail->Subject("Formulaire de contact");
    $Mail -> From($vars['email']);
//    $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
    $Mail -> To($mailSend);
    //$Mail -> To('enguer@enguer.com');
//    $Mail -> Cc( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
//    $Mail -> Bcc('enguer@enguer.com');
    $bloc = new Bloc();
    $mailContent = $vars['message'];

    $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
    $Pr = new Process();
    $bloc -> init($Pr);
    $bloc -> generate($Pr);
    $Mail -> Body($bloc -> Affich());
//    if (!$this->Cloture)
    $Mail -> Send();

    unset($_POST);

}

