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
}