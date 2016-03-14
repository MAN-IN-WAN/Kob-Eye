<?php
class Systeme extends Module {
    /**
     * getToken
     * get a new token from your current connection object
     * return string
     */
    static function getToken() {
        //si connecté
        return Sys::$Session->Session;
    }
    /**
     * isLogged
     * check if session is started
     * if session exists, start session
     */
    static function isLogged(){
        if (isset($_POST['logkey']))
            $session = $_POST['logkey'];
        else return false;
        if (isset($_POST['user_id']))
            $user_id = $_POST['user_id'];
        else return false;
        if (!empty($session)) {
            $c = Sys::getOneData('Systeme', 'Connexion/Session=' . $session.'&Utilisateur='.$user_id);
            if (is_object($c)) {
                Connection::startConnection($c);
                return true;
            }
        }
        return false;
    }
    /**
     * Surcharge de la fonction Check
     * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
     * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
     */
    function Check () {
        parent::Check();
        $g = Sys::getCount('Systeme','Group');
        if (!$g){
            //creation du groupe public
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[DEFAULT] PUBLIC";
            $g->Skin = "LoginBootstrap";
            $g->Save();

            //creation de l'utilisateur login par défaut
            $u = genericClass::createInstance('Systeme','User');
            $u->Login = 'login';
            $u->Pass = 'secret';
            $u->Mail = 'login@login.com';
            $u->Skin = 'LoginBootstrap';
            $u->Actif = true;
            $u->addParent($g);
            $u->Save();

            //creation du groupe admin
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[DEFAULT] ADMIN";
            $g->Skin = "LoginBootstrap";
            $g->Save();

            //creation de l'utilisateur admin par défaut
            $u = genericClass::createInstance('Systeme','User');
            $u->Login = 'admin';
            $u->Pass = '21wyisey';
            $u->Mail = 'admin@admin.com';
            $u->Skin = 'AdminV2';
            $u->Actif = true;
            $u->Admin = true;
            $u->addParent($g);
            $u->Save();
        }
    }

    static function retrievePassword($email){
        //recherche du compte
        $u = Sys::getOneData('Systeme','User/Mail='.$email);
        if ($u){
            $np = substr($u->CodeVerif,0,8);

            $Mail = new Mail();
            $Mail->Subject("Mot de passe oublié ".Sys::$domain);
            $Mail -> From("noreply@".Sys::$domain);
            $Mail -> ReplyTo("noreply@".Sys::$domain);
            $Mail -> To($u->Mail);
            $bloc = new Bloc();
            $mailContent = "
			Bonjour ".$u->Nom." ".$u->Prenom.",<br />Vous avez effectué une demande de changement de mot de passe.<br/>
			Conservez le bien ou changez le à la prochaine connexion.<br />Nouveau mot de passe: <h1>".$np."</h1>";
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            $Mail -> Send();
            $u->Set('Pass',$np);
            $u->Save();
            return true;
        }
        return false;
    }
}