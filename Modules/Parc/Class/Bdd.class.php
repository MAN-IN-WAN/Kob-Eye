<?php
class Bdd extends genericClass {
    public function Save () {
        parent::Save();
        if ($this->checkDatabase())
            return true;
        else return false;
    }
    private function checkDatabase(){
        $serv = $this->getOneParent('Server');
        $host = $this->getOneParent('Host');
        if (!is_object($serv)) return false;
        try {
            $dbGuac = new PDO('mysql:host=' . $serv->InternalIP . ';dbname=mysql', $serv->SshUser, $serv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //vérification de l'existence de l'utilisateur
            $query = "SELECT COUNT(*) FROM user WHERE User='" . $host->Nom . "' AND Host='%';";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if (!$result[0]["COUNT(*)"]) {
                $query = "CREATE USER '" . $host->Nom . "'@'%' IDENTIFIED BY '" . $host->Password . "';";
                $dbGuac->query($query);
            }

            //creation de la base de donnée et obtention des droits
            $query = "CREATE DATABASE IF NOT EXISTS " . $this->Nom.";";
            $dbGuac->query($query);
            $query = "GRANT ALL PRIVILEGES ON " . $this->Nom . " . * TO '" . $host->Nom . "'@'%';";
            $dbGuac->query($query);

            //flush privileges
            $query = "FLUSH PRIVILEGES;";
            $dbGuac->query($query);
        }catch (Exception $e){
            $this->addError(Array("Message"=>"Erreur de base de donnée: ".$e->getMessage()));
            return false;
        }
        return true;
    }
    public function Delete() {
        if ($this->removeFromDatabase())
            parent::Delete();
        else {
            //$this->AddError(Array("Message"=> ""));
            return false;
        }
        return true;
    }
    private function removeFromDatabase(){
        $serv = $this->getOneParent('Server');
        if (!is_object($serv)) return false;
        $dbGuac = new PDO('mysql:host=' . $serv->IP . ';dbname=mysql', $serv->SshUser, $serv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //flush privileges
        $query = "DROP DATABASE " . $this->Nom.";";
        $dbGuac->query($query);
        return true;
    }
}