<?php
class EmailAlias extends genericClass {
    public function Save($sync = true) {
        if($sync) {
            if ($this->Id > 0) {
                $old = Sys::getOneData('Parc', 'EmailAlias/' . $this->Id);
                if ($this->TargetMail != $old->TargetMail) {
                    $this->updateAlias($old->TargetMail);
                }
            } else {
                $this->createAlias();
            }
        }
        //sauvegarde des informations
        return parent::Save();
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
            $temp = $zimbra->getAccount($dom, 'name', $this->TargetMail);
            if($temp->id != $this->IdMail){
                $this->AddError(array('Message' => 'Erreur, cette adresse mail est liée à un autre compte', 'Object' => $temp));
                return false;
            }
        } catch (Exception $e) {
            //print_r($e);
        }
        try{
            $temp = $zimbra->getDistributionList( $this->TargetMail,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        $zimbra->addAccountAlias($mail->IdMail,$this->TargetMail);


    }

    /**
     * updateAlias
     * Mise à jour d'un alias
     */
    private function updateAlias($oldTarget) {
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
            $temp = $zimbra->getAccount($dom, 'name', $this->TargetMail);
            if($temp->id != $this->IdMail){
                $this->AddError(array('Message' => 'Erreur, cette adresse mail est liée à un autre compte', 'Object' => $temp));
                return false;
            }
        } catch (Exception $e) {
            //print_r($e);
        }
        try{
            $temp = $zimbra->getDistributionList( $this->TargetMail,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }

        $zimbra->removeAccountAlias($mail->IdMail,$oldTarget);
        $zimbra->addAccountAlias($mail->IdMail,$this->TargetMail);
    }

    /**
    * Delete
    * Suppression d'un alias
    */
    public function Delete($sync = true) {
        if($sync) {
            $mail = $this->getOneParent('CompteMail');
            $srv = $mail->getKEServer();

            if (!is_object($srv) || $srv->ObjectType != 'Server') {
                $this->AddError(array('Message' => 'Un compte mail doit être lié a un serveur.'));
                return false;
            }

            $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
            $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

            $zimbra->removeAccountAlias($mail->IdMail, $this->TargetMail);
        }

        return parent::Delete();
    }


    /*public function Verify() {
        //extraction du domaine
        preg_match('#([^@]+?)@(.*)#',$this->TargetMail,$out);
        //teste l'existence du domaine
        return parent::Verify();
    }*/
}