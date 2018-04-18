<?php
class EmailDelegation extends genericClass {
    public function Save() {
        if ($this->Id>0) {
            $old = Sys::getOneData('Parc', 'EmailDelegation/' . $this->Id);
            if ($this->TargetMail != $old->TargetMail){
                $this->addError(array('Message'=>'Vous ne pouvez pas modifier le compte email cible.... veuillez créer une nouvelle délégation.'));
                return false;
            }
            $this->TargetMail = $old->TargetMail;
        }else $old = genericClass::createInstance('Parc','EmailDelegation');

        parent::Save();
        if (!$old->Enabled&&$this->Enabled
            || $this->Enabled&&!$this->Status){
            if ($this->enableShaeMailbox()) {
                $this->Status = true;
            }else{
                $this->Status = false;
            }
        }
        return true;
    }
    public function Verify() {
        //teste le mail cible
        $target = Sys::getOneData('Parc','CompteMail/Adresse='.$this->TargetMail);
        if(!is_object($target) || $target->ObjectType != 'CompteMail'){
            $this->AddError(array('Message'=>'Le compte email cible n\'existe pas.'));
            return false;
        }
        return parent::Verify();
    }
    public function enableShaeMailbox() {
        //teste le compte mail
        $mail = $this->getOneParent('CompteMail');
        if(!is_object($mail) || $mail->ObjectType != 'CompteMail'){
            $this->AddError(array('Message'=>'Une délégation de compte mail doit être lié a un compte mail.'));
            return false;
        }

        //teste le serveur
        $srv = $mail->getOneParent('Server');
        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }

        //récupère le compte email cible
        $target = Sys::getOneData('Parc','CompteMail/Adresse='.$this->TargetMail);

        //récupération du domaine
        $dom = explode('@',$mail->Adresse);
        $dom = $dom[1];

        //connexion admin
        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        //modification des droits de la racine sur le compte email
        try {
            $test = $zimbra->actionFolder(array(
                'recursive' => true,
                'url' => '/',                           //exemple d'url du dossier
                'op' => '',                             //type d'opération ex: read|delete|rename|move|trash|empty|color|[!]grant|revokeorphangrants |url|import|sync|fb|[!]check|update|[!]syncon|retentionpolicy
                '_' => array(
                    'grant' => array(
                        'perm' => '',                   //les permissions ex: rwixa
                        'gt' => '',                     //le type de permission ex: usr ou account
                        'zid' => '',                    //id du user
                        ''
                    )
                )
            ));
        }catch (Exception $e){
            echo "ERROR => ".$e->getMessage();
            die();
        }
        print_r($test);
        //modification des droits du dossier Brouillon sur le compte email
        //modification des droits du dossier Inbox sur le compte email
        //creation du point de montage
        //autorisation d'envoi en tant que
    }
}