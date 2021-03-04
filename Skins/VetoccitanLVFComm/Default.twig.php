<?php
require_once ("Class/Lib/Mail.class.php");
$GLOBALS["Systeme"]->Header->addJs('Tools/Js/Jquery/1.11.3/jquery.min.js');
$GLOBALS["Systeme"]->Header->addJs('Tools/Css/Bootstrap/3.3.1/js/bootstrap.js');
$skin = Sys::$Skin;
$GLOBALS["Systeme"]->Header->addJs('Skins/'.$skin.'/Js/Veto1.js');


$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.3.1/css/bootstrap-theme.css');
$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.3.1/css/bootstrap.css');
$GLOBALS["Systeme"]->Header->addCss('Tools/Fonts/fontawesome-free-5.0.10/web-fonts-with-css/css/fontawesome-all.css');

if (!empty($_COOKIE['myCookie'])){
    $vars['accueil'] = true;
}else{
    $vars['accueil'] = false;
}
//var_dump($_COOKIE);
//echo date('d-m-Y His');

//if ($_POST['email']) var_dump($_POST['email']);

// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

//$lAdherent = $LeClient->getOneChild("Adherent");
//$lAdherent->Module="Vetoccitan";
//$lAdherent->Media= $lAdherent->getOneChild("Media");
//$vars['Adherent']=$lAdherent;
//$vars['curSkin'] = $skin;
//$vars['men'] = Sys::$CurrentMenu->Titre;
//$vars['menClass'] = Utils::strToCode(Sys::$CurrentMenu->Titre);


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
//    $Mail -> To('fromilhague.romain@gmail.com');
//    $Mail -> To('j.visse@lvfcommunication.com');
    $Mail -> Bcc ( 'marie@abtel.fr');
    $Mail -> To('marie@abtel.fr');
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


