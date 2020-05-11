<?php

class ListeDiffusion extends genericClass {

//var $_isVerified = false;
    var $_KEServer = false;
    var $_KEClient = false;

    /**
     * Force la vérification avant enregistrement
     * @param	boolean	Enregistrer aussi sur LDAP
     * @return	bool
     */
    public function Save( $synchro = true ) {

        $client = $this->getKEClient();
        if (!$client){
            $this->addError(array('Message'=>'Compte client introuvable.'));
            return false;
        }
        $this->addParent($client);

        if(!$synchro){
            parent::Save();
            return true;
        }

        if((!isset($this->IdDiffusion) || $this->IdDiffusion=='') && $this->getKEServer()){
            if(!$this->createDiffusion()){
                //$this->Delete();
                $this->addError(array('Message'=>'Impossible de créer la liste de diffusion...'));
                return false;
            }
        } elseif ($this->getKEServer()){
            if(!$this->updateDiffusion()) {
                return false;
            }

        } else{
            $this->addError(array('Message'=>'Impossible de trouver le serveur.'));
            //$this->Delete();
            return false;
        }


        return parent::Save();
    }


    public function getKEServer() {
        if(empty($this->Id)){
            $pars = array();
            foreach ($this->Parents as $p){
                if($p['Titre'] == 'Server'){
                    $pa = Sys::getOneData('Parc','Server/'.$p['Id'],0,1,null,null,null,null,true);
                    $pars[] = $pa;
                }

            }
            $this->_KEServer = $pars;
        }
        if(!is_object($this->_KEServer)) {
            $this->_KEServer = Sys::getOneData('Parc','Server/ListeDiffusion/'.$this->Id,0,1,'','','','',true);
        }
        if (!is_object($this->_KEServer)){
            //retroune le serveur de mail par defaut
            $this->_KEServer = Sys::getOneData('Parc','Server/defaultMailServer=1',0,1,'','','','',true);
        }
        if (!is_object($this->_KEServer)){
            return false;
        }
        return $this->_KEServer;
    }

    /**
     * Récupère une référence vers l'objet KE "Client"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le client
     * pour le cas d'une utilisation ultérieure
     * @return	L'objet Kob-Eye
     */
    public function getKEClient() {
        if(!is_object($this->_KEClient)) {
            $this->_KEClient = $this->getOneParent('Client');
        }
        if(!is_object($this->_KEClient)&&Sys::$User->hasRole('PARC_CLIENT')) {
            //donc i ls'agit d'un client connecté, on récupère l'objet client
            $this->_KEClient = Sys::$User->getOneChild('Client');
        }
        if(!is_object($this->_KEClient)) {
            return false;
        }
        return $this->_KEClient;
    }


    /**
     * Met à jour une liste de diffusion sur le serveur
     * @return	bool
     */
    public function updateDiffusion(){
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Une liste de diffusion doit être liée a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);


        $dom = explode('@',$this->Nom);
        $dom = $dom[1];



        try{
            $domaine = $zimbra->getDomain($dom);
        } catch (Exception $e){
            //if($e->getMessage() == 'no such domain'){
            $this->AddError(array('Message' => 'Erreur, le domaine renseigné n\'existe pas', 'Object' => $e));
            return false;
            //TODO : Creation du domaine ?
            //}
        }

        //Check que la liste existe déjà
        try{
            $temp = $zimbra->getDistributionList($this->IdDiffusion, 'id');
            $actuName = $temp->get('name');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, cette liste de diffusion n\'existe plus', 'Object' => $e));
            return false;
        }

        //Check que l'adresse ne soit pas déjà prise par un autre compte/Alias/Liste de diffusion
        try{
            $temp = $zimbra->getDistributionList($this->Nom, 'name' );
            if($temp->id != $this->IdDiffusion){
                $this->AddError(array('Message' => 'Erreur, cette liste de diffusion existe déjà', 'Object' => $temp));
                return false;
            }
        } catch (Exception $e) {
            //print_r($e);
        }
        try{
            $temp = $zimbra->getAccount( $dom,$this->Nom,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une compte déjà existant', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        try{
            if($actuName != $this->Nom)
                $resName = $zimbra->renameDistributionList($this->IdDiffusion, $this->Nom);

        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'enregistrement','Object'=>$e));
            print_r($e);
            return false;
        }

        return true;

    }

    /**
     * Crée une  liste de diffusion sur le serveur mail a pres l'avoir enregistré en base
     * @return	bool
     */
    public function createDiffusion(){

        $srv = $this->getKEServer();
        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }


        // Create a new Admin class and authenticate
        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);


        $dom = explode('@',$this->Nom);
        $dom = $dom[1];


        try{
            $domaine = $zimbra->getDomain($dom);
        } catch (Exception $e){
            //print_r($e);
            //if($e->getMessage() == 'no such domain'){
            $this->AddError(array('Message' => 'Erreur, le domaine renseigné n\'existe pas', 'Object' => $e));
            return false;
            //TODO : Creation du domaine ?
            //}
        }

        //Check que l'adresse ne soit pas déjà prise par un autre compte/Alias/Liste de diffusion
        try{
            $temp = $zimbra->getAccount($dom, 'name', $this->Nom);
            $this->AddError(array('Message' => 'Erreur, cette adresse est déjà utilisée par un compte mail', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //On souhaite l'excpetion no_such_account pour confirmer que l'adresse n'exsite pas.
        }
        try{
            $temp = $zimbra->getDistributionList( $this->Nom,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une liste de diffusion déjà existante', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        try{
            $values = array();



            $values['name'] = $this->Nom;

            $res = $zimbra->createDistributionList($values);

            $this->IdDiffusion = $res->get('id');

        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'enregistrement : '.$e->getErrorCode(),'Object'=>''));
            return false;
        }


        return true;
    }


    /**
     * Ajoute un membre à une liste de diffusion sur le serveur mail
     * @return	bool
     */
    public function addListMember($cptMail){
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Une liste de diffusion doit être liée a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);


        //Check que la liste existe déjà
        try{
            $temp = $zimbra->getDistributionList($this->IdDiffusion, 'id');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, cette liste de diffusion n\'existe plus', 'Object' => $e));
            return false;
        }

        $members = $temp->getMembers();

        foreach($members as $memb){
            if($cptMail->Adresse == $memb) {
                $this->AddError(array('Message' => 'Erreur, ce compte mail est déjà membre de cette liste de diffusion', 'Object' => ''));
                return false;
            }
        }

        $members[] = $cptMail->Adresse;
        $temp = $zimbra->modifyDistributionList(array(
           'id' => $this->IdDiffusion,
            'zimbraMailForwardingAddress'=> $members
        ));


        return true;
    }

    /**
     * Enleve un membre d'une liste de diffusion sur le serveur mail
     * @return	bool
     */
    public function delListMember($cptMail){

        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Une liste de diffusion doit être liée a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        //Check que la liste existe déjà
        try{
            $temp = $zimbra->getDistributionList($this->IdDiffusion, 'id');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, cette liste de diffusion n\'existe plus', 'Object' => $e));
            return false;
        }

        $members = $temp->getMembers();

        $newMembs = array();
        foreach($members as $memb){
            if($cptMail->Adresse != $memb) {
                $newMembs[] = $memb;
            }
        }

        if(!count($newMembs))
            $newMembs = '';
        try {
            $temp = $zimbra->modifyDistributionList(array(
                'id' => $this->IdDiffusion,
                'zimbraMailForwardingAddress'=> $newMembs
            ));
        }catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, lors de la suppression ', 'Object' => $e));
            return false;
        }

        return true;
    }

    /**
     * Enleve un membre d'une liste de diffusion sur le serveur mail
     * @return	bool
     */
    public function updateMemberList($list){
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Une liste de diffusion doit être liée a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        //Check que la liste existe déjà
        try{
            $temp = $zimbra->getDistributionList($this->IdDiffusion, 'id');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, cette liste de diffusion n\'existe plus', 'Object' => $e));
            return false;
        }

        try {
            $temp = $zimbra->modifyDistributionList(array(
                'id' => $this->IdDiffusion,
                'zimbraMailForwardingAddress' => $list
            ));
        }catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, lors de l\'actualisation de la liste. ', 'Object' => $e));
            return false;
        }

        return true;
    }



    /**
     * Actualise la liste des membres d'une liste de diffusion concernant un compte mail
     * @return	bool
     */
    public function checkListMember($cptMail){
        $proxyMembers = $this->getChildren('CompteMail');
        $membs = array();
        foreach($proxyMembers as $cpt){
            $membs[] = $cpt->Adresse;
        }

        if(in_array($cptMail->Adresse,$membs)){
           $this->addListMember($cptMail);
        } else {
            $this->delListMember($cptMail);
        }
    }



    /**
     * addChild
     * add a child link
     * @param String Object Type name
     * @param Int Id of the parent object
     */
    public function addChild($Type, $Id) {
        $child = parent::addChild($Type, $Id);
        if($Type == 'CompteMail'){
            $this->addListMember($child);
        }

        return $child;
    }

    /**
     * delChild
     * add a child link
     * @param String Object Type name
     * @param Int Id of the parent object
     */
    public function delChild($Type, $Id) {
        $child = parent::delChild($Type, $Id);

        if($Type == 'CompteMail'){
            $this->delListMember($child);
        }

        return $child;
    }

    /**
     * resetChilds
     * delete all childs link from an object Type
     * @param String Object Type name
     */
    public function resetChilds($Class) {
        if($Class == 'CompteMail') {
            $olds = $this->getChildren($Class);
            foreach($olds as $old){
                $this->delListMember($old);
            }
        }

        parent::resetChilds($Class);

        return true;
    }

    /**
     * Delete
     * Suppression d'une ressource
     */
    public function Delete($sync = true){
        if($sync) {
            $srv = $this->getKEServer();

            if (!is_object($srv) || $srv->ObjectType != 'Server') {
                $this->AddError(array('Message' => 'Une liste de diffusion doit être liée a un serveur.'));
                return false;
            }

            $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
            $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

            $zimbra->deleteDistributionList($this->IdDiffusion);
        }

        parent::Delete();
    }

}