<?php
class EmailAlias extends genericClass {
    public function Save() {
        if ($this->Id>0) {
            $old = Sys::getOneData('Parc', 'EmailAlias/' . $this->Id);
            if ($this->TargetMail != $old->TargetMail){
                $this->addError(array('Message'=>'Vous ne pouvez pas modifier le compte email cible.... veuillez le supprimer et recréer un alias.'));
                return false;
            }
            $this->TargetMail = $old->TargetMail;
        }

        //sauvegarde des informations
        parent::Save();

        if ($this->Status){
            //updateAlias
            $this->updateAlias();
        }else{
            //createALias
            $this->createAlias();
        }
        return true;
    }

    /**
     * createAlias
     * Créatio d'un alias
     */
    private function createAlias() {
        $mail = $this->getOneParent('CompteMail');
        $srv = $mail->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        $dom = explode('@',$this->TargetMail);
        $dom = $dom[1];

        try{
            $domaine = $zimbra->getDomain($dom);
        } catch (Exception $e){
            //if($e->getMessage() == 'no such domain'){
            $this->AddError(array('Message' => 'Erreur, le domaine de l\'adresse alias n\'existe pas', 'Object' => $e));
            return false;
        }

        //Check que l'adresse ne soit pas déjà prise par un autre compte/Alias/Liste de diffusion
        try{
            $temp = $zimbra->getAccount($dom, 'name', $this->Adresse);
            if($temp->id != $this->IdMail){
                $this->AddError(array('Message' => 'Erreur, cette adresse mail est liée à un autre compte', 'Object' => $temp));
                return false;
            }
        } catch (Exception $e) {
            //print_r($e);
        }
        try{
            $temp = $zimbra->getDistributionList( $this->Adresse,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }

    }

    /**
     * updateAlias
     * Mise à jour d'un alias
     */
    private function updateAlias() {

    }

    public function Verify() {
        //extraction du domaine
        preg_match('#([^@]+?)@(.*)#',$this->TargetMail,$out);
        //teste l'existence du domaine
        return parent::Verify();
    }
}