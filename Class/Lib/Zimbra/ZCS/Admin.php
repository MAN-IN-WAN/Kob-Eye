<?php

/**
 * Zimbra SOAP API calls.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS;

class Admin
{

    private $authToken;
    private $authTokenChild;
    private $zimbraConnect;
    private $zimbraConnectChild;
    private $systemUsers = array(
        "postmaster",
        "ham",
        "spam",
        "virus-quarantine",
        "galsync"
    );

    public function __construct($server, $port = 7071, $authToken = null)
    {
        $this->zimbraConnect = new \Zimbra\ZCS\SoapClient($server, $port);
        $this->zimbraConnectChild = new \Zimbra\ZCS\SoapClient($server, $port);
        
        if ($authToken) {
            $this->authToken = $authToken;
            $this->zimbraConnect->addContextChild('authToken', $this->authToken);
        }
    }

    /**********************/
    /******** AUTH ********/
    /**********************/
    public function auth($email, $password)
    {
        $SOAPMessage = $this->zimbraConnect;
        $xml = $SOAPMessage->request('AuthRequest', array('name' => $email, 'password' => $password));

        $this->authToken = $xml->children()->AuthResponse->authToken;
        $this->zimbraConnect->addContextChild('authToken', $this->authToken);

        return (string) $this->authToken;
    }
    
    public function delegateAuth($email)
    {
        $SOAPMessage = $this->zimbraConnect;
        $xml = $SOAPMessage->request('DelegateAuthRequest', array(),array('account' => array('_'=>$email,'by'=>'name')));

        $this->authTokenChild = $xml->children()->DelegateAuthResponse->authToken;
        $this->zimbraConnectChild->addContextChild('authToken', $this->authTokenChild);

        return (string) $this->authTokenChild;
    }

    
    
    /*************************************************/
    /******** ADMIN (Gestion compte et autre) ********/
    /*************************************************/
    
    public function searchDirectory($domain, $limit = 10, $offset = 0, $type = 'accounts', $sort = null, $query = null) {
        if ($type == 'accounts') {
            $ldapQuery = "(&amp;";

            if ($query !== null) {
                $ldapQuery .= "(name=$query*)";
            }

            $ldapQuery .= '(!(zimbraIsSystemResource=TRUE))';
            $ldapQuery .= '(!(zimbraIsAdminAccount=TRUE))';

            $ldapQuery .= ')';
        } else {
            $ldapQuery = '';
        }

        $attributes = array(
            'limit'  => $limit,
            'offset' => $offset,
            'domain' => $domain,
            'types'  => $type,
        );

        if (!is_null($sort)) {
            $attributes['sortBy'] = $sort[0];
            $attributes['sortAscending'] = $sort[1];
        }

        return $this->zimbraConnect->request('SearchDirectoryRequest', $attributes, array('query' => $ldapQuery));
    }
    
    //Recherche un objet zimbra
    /*Types
     * conversation|message|contact|appointment|task|wiki|document  (conversation=>default)
     **/
    public function search($mail, $query = null, $type = 'conversation'){
        $this->delegateAuth($mail);
        $params = array();
        if($query) $params = array('query'=> $query);
        $response = $this->zimbraConnectChild->requestMail('SearchRequest', array('types'=> $type), $params);
        
        return $response->children()->SearchResponse;
    }

    public function count($domain, $query = null){
        $response = $this->searchDirectory($domain, 1, 0, 'accounts', null, $query);

        return $response->children()->SearchDirectoryResponse['searchTotal'];
    }
    
    //Retourne le nombre des objets existants sur le serveur
    public function countObjects($type){
        $response = $this->zimbraConnect->request('CountObjectsRequest',array('type'=>$type));
        
        return  (int) $response->children()->CountObjectsResponse['num'];
    }
    
    //Réalise un autocomplete depuis la Global Adress List
    public function autoCompleteGal($domain, $name, $limit = 10){
        $attributes = array(
            'domain' => $domain,
            'limit'  => $limit,
        );

        $response = $this->zimbraConnect->request('AutoCompleteGalRequest', $attributes, array('name' => $name));
        foreach ($response->children()->AutoCompleteGalResponse->children() as $cn) {
            foreach ($cn->children()->a as $a) {
                $result[(string) $a['n']] = (string) $a;
            }

            // Skip groups
            if (!isset($result['type'])) {
                $results[] = $result;
            }
        }

        return $results;
    }

    //Ne sert à rien à part à conserver une session admin vivante
    public function noOp(){
        $this->zimbraConnect->request('NoOpRequest');
    }
    
    
    /**** COMPTES ****/
    
    //Crée un compte
    public function createAccount($values){
        $params = array();

        $params['name'] = $values['name'];
        unset($values['name']);

        $params['password'] = $values['password'];
        unset($values['password']);

        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('CreateAccountRequest', array(), $params);
        $accounts = $response->children()->CreateAccountResponse->children();

        return new \Zimbra\ZCS\Entity\Account($accounts[0]);
    }
    
    //Retourne la liste de tous les comptes NOTE:Il existe une methode soap autre que SearchDirectoryRequest pour récupérer la liste des accounts : GetAllAccountsRequest
    public function getAccounts(array $attributes, $sort = null, $query = null)  {
        $accounts = $this->searchDirectory($attributes['domain'], $attributes['limit'], $attributes['offset'], 'accounts', $sort, $query);

        $results = array();

        foreach ($accounts->children()->SearchDirectoryResponse->children() as $account) {
            $res = new \Zimbra\ZCS\Entity\Account($account);
            if(in_array($res->get('uid'),$this->systemUsers)) continue; //Ignore les postmasters...
            $results[] = $res;
        }

        return $results;
    }
    
    
    //Retourne la liste de tous les comptes NOTE:Il existe une methode soap autre que SearchDirectoryRequest pour récupérer la liste des accounts : GetAllAccountsRequest
    public function getAllAccounts($domain=null)  {
        $params=array();
        if($domain){
                $params = array(
                        'domain' => array(
                            '_'  => $domain,
                            'by' => 'name',
                        )
                );
        }
        $accounts = $this->zimbraConnect->request('GetAllAccountsRequest',array(),$params);
        
        $results = array();

        foreach ($accounts->children()->GetAllAccountsResponse->children() as $account) {
            $res = new \Zimbra\ZCS\Entity\Account($account);
            
            $uidClean = $res->get('uid');
            $uidClean = explode('.',$uidClean);
            $uidClean = $uidClean[0];
            
            if(in_array($uidClean ,$this->systemUsers)) continue; //Ignore les postmasters...
            $results[] = $res;
        }

        return $results;
    }
    

    //Renvoie un les infos d'un compte
    public function getAccount($domain, $by, $account){
        $params = array(
            'account' => array(
                '_'  => $account,
                'by' => $by,
            )
        );

        $response = $this->zimbraConnect->request('GetAccountRequest', array(), $params);

        $accounts = $response->children()->GetAccountResponse->children();
        $account = new \Zimbra\ZCS\Entity\Account($accounts[0]);

        $aliases = $this->getAliases($domain);
        if (array_key_exists($account->name, $aliases)) {
            $account->setAliases($aliases[$account->name]);
        }

        return $account;
    }

    //Modifie un compte (Nécéssite l'id)
    public function modifyAccount($values){
        $params = array();
        $params['id'] = $values['id'];
        unset($values['id']);
        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('ModifyAccountRequest', array(), $params);
        $accounts = $response->children()->ModifyAccountResponse->children();

        return new \Zimbra\ZCS\Entity\Account($accounts[0]);
    }

    
    //Supprime un compte (depuis son id)
    public function deleteAccount($id){
        $this->zimbraConnect->request('DeleteAccountRequest', array(), array('id' => $id));

        return true;
    }

    //Affecte le mot de passe d'un compte (depuis son id)
    public function setPassword($id, $password){
        $params = array(
            'id' => $id,
            'newPassword' => $password
        );
        $this->zimbraConnect->request('SetPasswordRequest', array(), $params);
    }

    //Affecte le mot de passe d'un compte (depuis son id)
    public function renameAccount($id, $newName){
        $params = array(
            'id' => $id,
            'newName' => $newName
        );
        $this->zimbraConnect->request('RenameAccountRequest', array(), $params);
    }
    
    //Recupère la ou les préférences du compte
    public function getPrefs($mail,$pref = array()){
        //TODO cas ou l'on veut une Pref en particulier
        $this->delegateAuth($mail);
        
        $response = $this->zimbraConnectChild->requestAccount('GetPrefsRequest',array(),array());
        
        $res = '';
        foreach($response->children()->GetPrefsResponse->children() as $pref){
                $res .= (string)$pref->attributes() . " : " . (string)$pref . PHP_EOL;
        }
        
        return $res;        
    }
    
    //Modifie la ou les préférences du compte
    public function modifyPrefs($mail,$pref){
        $this->delegateAuth($mail);
        $params = array(
                'pref'=> array(
                                'name'=> $pref['name'],
                                '_' => $pref['value']
                        )
                );

        $response = $this->zimbraConnectChild->requestAccount('ModifyPrefsRequest',array(),$params);
        
        //echo '<pre>';
        //simplexml_dump($response->children()->ModifyPrefsResponse->attributes());
        //echo '</pre>';
    }
    
    /**** DOMAINES ****/

    //Retourne la liste des domaines présents sur le serveur (Version objet xml non untilisable par l'user)
    private function getAllDomains()    {
        return $this->zimbraConnect->request('GetAllDomainsRequest');
    }

    //Retourne la liste des domaines présents sur le serveur (Version lisible par le user)
    public function getDomains(){
        foreach ($this->getAllDomains()->children()->GetAllDomainsResponse->children() as $domain) {
            $results[] = new \Zimbra\ZCS\Entity\Domain ($domain);
        }

        return $results;
    }
    
    //Crée un domaine
    public function createDomain($values){
        $params = array();

        $params['name'] = $values['name'];
        unset($values['name']);

        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('CreateDomainRequest', array(), $params);
        $domain = $response->children()->CreateDomainResponse->children();

        return new \Zimbra\ZCS\Entity\Domain($domain[0]);
    }

    //Récupère une domaine
    public function getDomain($domain, $by = 'name', $attrs = array()){
        $attributes = array();

        if (!empty($attrs)) {
            $attributes['attrs'] = implode(',', $attrs);
        }

        $params = array(
            'domain' => array(
                '_'  => $domain,
                'by' => $by,
            )
        );

        $response = $this->zimbraConnect->request('GetDomainRequest', $attributes, $params);
        $domains = $response->children()->GetDomainResponse->children();

        return new \Zimbra\ZCS\Entity\Domain($domains[0]);
    }

    //Modifie un domaine (Nécéssite un Id)
    public function modifyDomain($values){
        $params = array();
        $params['id'] = $values['id'];
        unset($values['id']);
        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('ModifyDomainRequest', array(), $params);
        $domains = $response->children()->ModifyDomainResponse->children();

        return new \Zimbra\ZCS\Entity\Domain($domains[0]);
    }

    
    
    
    /**** SERVEURS ****/

    //Retourne la liste des serveurs (Version objet xml non untilisable par l'user)
    private function getAllServers($service = 'mailbox'){
        return $this->zimbraConnect->request('GetAllServersRequest', array(
            'service' => $service
        ));
    }

    //Retourne la liste des serveurs (Version lisible par le user)
    public function getServers(){
        foreach ($this->getAllServers()->children()->GetAllServersResponse->children() as $server) {
            $results[] = new \Zimbra\ZCS\Entity\Server($server);
        }

        return $results;
    }

    //Retourne le serveur demandé
    public function getServer($server, $by = 'name')    {
        $params = array(
            'server' => array(
                '_'  => $server,
                'by' => $by,
            )
        );

        $response = $this->zimbraConnect->request('GetServerRequest', array(), $params);
        $servers = $response->children()->GetServerResponse->children();
        return new \Zimbra\ZCS\Entity\Server($servers[0]);
    }
    
    
    /**** QUOTAS ****/
    
    //Retourne l'utilisation du quota des boites (Par defaut toutes les boites du serveur)(non utilisable par l'user)
    private function getQuotaUsage(array $attributes, $targetServer = null){
        if (isset($targetServer)) {
            $this->zimbraConnect->addContextChild('targetServer', $targetServer);
        }

        return $this->zimbraConnect->request('GetQuotaUsageRequest', $attributes);
    }

    //Retourne l'utilisation du quota des boites (Par defaut toutes les boites du serveur)
    public function getQuotas(array $attributes, $sort = null, $targetServer = null){
        $results = array();

        if (!is_null($sort)) {
            $attributes['sortBy'] = $sort[0];
            $attributes['sortAscending'] = $sort[1];
        }

        $response = $this->getQuotaUsage($attributes, $targetServer);
        $quotas = $response->children()->GetQuotaUsageResponse->children();
        
        foreach ($quotas as $quota) {
            $account = explode('@', $quota['name']);
            $b = explode('.', $account[0]);

            if (!in_array($b[0], $this->systemUsers)) {
                $results[(string) $quota['id']] = array(
                    'name' => (string) $quota['name'],
                    'used' => (string) $quota['used'],
                    'limit' => (string) $quota['limit'],
                );
            }
        }

        return $results;
    }

    //Retourne l'utilisation du quota des boites d'un domaine particulier (Par defaut toutes les boites du serveur)
    public function getTotalQuota($domain){
        $result = array(
            'diskUsage'       => 0,
            'diskProvisioned' => 0,
            'mailTotal'       => 0,
        );

        foreach ($this->getAllServers()->children()->GetAllServersResponse->children() as $server) {

            $response = $this->getQuotaUsage(array('domain' => $domain), (string) $server['id']);
            $result['mailTotal'] += (string) $response->children()->GetQuotaUsageResponse['searchTotal'];

            foreach ($response->children()->GetQuotaUsageResponse->children() as $quota) {
                $b = explode('@', $quota['name']);
                $c = explode('.', $b[0]);
                $account = $c[0];

                // Remove the system users (antispam, etc.) from the count
                if (!in_array($account, $this->systemUsers)) {
                    $result['diskUsage'] += $quota['used'];
                    $result['diskProvisioned'] += $quota['limit'];
                } elseif ($b[0] = 'postmaster') {
                    $result['diskLimit'] = (int) $quota['limit'];
                    $result['mailTotal'] -= 1;
                } else {
                    $result['mailTotal'] -= 1;
                }
            }
        }

        $domain = $this->getDomain($domain, 'name', array('zimbraDomainMaxAccounts'));
        $result['mailLimit'] = (int) $domain->zimbraDomainMaxAccounts;

        return $result;
    }
    
    
    
    /**** ALIAS ****/
    
    //Retourne la liste des alias de compte d'un domaine
    public function getAliases($domain,$direct = true){
        $results = array();

        $response = $this->searchDirectory($domain, 0, 0, 'aliases');
        $aliases = $response->children()->SearchDirectoryResponse->children();

        if($direct){
            foreach ($aliases as $alias) {
                $results[] = strstr($alias['name'], '@', true);
            }
        } else{
            foreach ($aliases as $alias) {
                $results[(string) $alias['targetName']][] = strstr($alias['name'], '@', true);
            }
        }


        return $results;
    }
    
    //Ajoute un alias sur un compte (Nécéssite un Id)
    public function addAccountAlias($id, $alias){
        $params = array(
            'id'    => $id,
            'alias' => $alias
        );

        $this->zimbraConnect->request('AddAccountAliasRequest', array(), $params);

        return true;
    }

    //Supprime un alias d'un compte ( Nécéssite un Id)
    public function removeAccountAlias($id, $alias){
        $params = array(
            'id'    => $id,
            'alias' => $alias
        );

        $this->zimbraConnect->request('RemoveAccountAliasRequest', array(), $params);

        return true;
    }
    
    
    
    
    /**** LISTES DE DIFFUSION ****/

    //Retourne les listes de diffusion d'un domaine
    public function getDistributionLists($domain){
        $results = array();

        $response = $this->searchDirectory($domain, 0, 0, 'distributionlists');

        foreach ($response->children()->SearchDirectoryResponse->children() as $listData) {
            $results[] = new \Zimbra\ZCS\Entity\DistributionList($listData);
        }

        return $results;
    }

    //Retourne une liste de diffusion
    public function getDistributionList($list, $by = 'id')  {
        $params = array(
            'dl' => array(
                '_'  => $list,
                'by' => $by,
            )
        );

        $response = $this->zimbraConnect->request('GetDistributionListRequest', array(), $params);
        $lists = $response->children()->GetDistributionListResponse->children();

        return new \Zimbra\ZCS\Entity\DistributionList($lists[0]);
    }

    //Modifie une liste de diffusion (Nécéssite un Id)
    public function modifyDistributionList($values){
        $params = array();
        $params['id'] = $values['id'];
        unset($values['id']);
        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('ModifyDistributionListRequest', array(), $params);
        $lists = $response->children()->ModifyDistributionListResponse->children();

        return new \Zimbra\ZCS\Entity\DistributionList($lists[0]);
    }

    //Ajoute un membre à une liste de diffusion
    public function addDistributionListMember($id, $member){
        $params = array(
            'id'    => $id,
            'dlm' => $member
        );

        $this->zimbraConnect->request('AddDistributionListMemberRequest', array(), $params);

        return true;
    }

    //Supprime un membre d'une liste de diffusion
    public function removeDistributionListMember($id, $member){
        $params = array(
            'id'    => $id,
            'dlm' => $member
        );

        $this->zimbraConnect->request('RemoveDistributionListMemberRequest', array(), $params);

        return true;
    }

    //Crée une liste de diffusion
    public function createDistributionList($values){
        $params = array();

        $params['name'] = $values['name'];
        unset($values['name']);

        $params['attributes'] = $values;

        $response = $this->zimbraConnect->request('CreateDistributionListRequest', array(), $params);
        $lists = $response->children()->CreateDistributionListResponse->children();

        return new \Zimbra\ZCS\Entity\DistributionList($lists[0]);
    }

    //Supprime une liste de diffusion
    public function deleteDistributionList($id){
        $this->zimbraConnect->request('DeleteDistributionListRequest', array(), array('id' => $id));

        return true;
    }

    
    
    
    /**** COS ****/
    //Retourne tout les cos COS
    public function getAllCos(){
        $response = $this->zimbraConnect->request('GetAllCosRequest');
        
        foreach ($response->children()->GetAllCosResponse->children() as $cos) {
            $results[] = new \Zimbra\ZCS\Entity\Cos($cos);
        }

        return $results;
    }
    
    //Retourne un COS
    public function getCos($cos, $by = 'id'){
        $params = array(
            'cos' => array(
                '_'  => $cos,
                'by' => $by,
            )
        );

        $response = $this->zimbraConnect->request('GetCosRequest', array(), $params);
        $coses = $response->children()->GetCosResponse->children();

        return new \Zimbra\ZCS\Entity\Cos($coses[0]);
    }

    
    /**************************************************/
    /***** MAILBOX (Gestion Boite dans le détail) *****/
    /**************************************************/
    
    /**** DOSSIERS ****/
    
    //Crée un dossier (Note : Les calendriers sont des dossiers)
    public function createFolder($mail,$params=array()){
        $default=array(
            'name'=>'DefaultName',
            'view'=>'message',
            '_'=>array('acl'=>array())
        );
        $params = array_replace($default,$params);

        $this->delegateAuth($mail);
        $response = $this->zimbraConnectChild->requestMail('CreateFolderRequest', array(), array('folder'=>$params));
        
        $fold = $response->children()->CreateFolderResponse->children();
        
        return new \Zimbra\ZCS\Entity\Folder($fold[0]);
    }
    //Effectue des actions sur les dossiers
    public function actionFolder($params=array()){
        $default=array(
            'recursive' => true,
            'url' => '/',                           //exemple d'url du dossier
            'op' => 'grant',                             //type d'opération ex: read|delete|rename|move|trash|empty|color|[!]grant|revokeorphangrants |url|import|sync|fb|[!]check|update|[!]syncon|retentionpolicy
            '_'=>array(
                'grant'=>array(
                    'perm' => 'rwixa',                   //les permissions ex: rwixa
                    'gt' => 'account',                     //le type de permission ex: usr ou account
                    'zid' => '',                    //id du user
                    ''
                )
            )
        );
        $params = array_replace($default,$params);

        //$this->delegateAuth($mail);
        $response = $this->zimbraConnectChild->requestMail('FolderActionRequest', array(), array('action'=>$params));
        $fold = $response->children()->CreateFolderResponse->children();
        return new \Zimbra\ZCS\Entity\Folder($fold[0]);
    }

    //Retourne un dossier
    public function getFolder($mail,$view = 'appointment',$path =null){
        /*views:
         * appointment => Calendrier
         * document => briefcase
         * massage => chat
         * 
         */
        
        $params = array();
        if($path) $params['folder'] = array('path'=>$path);
        
        $this->delegateAuth($mail);
        $response = $this->zimbraConnectChild->requestMail('GetFolderRequest', array('view'=> $view), $params);
        
        $fold = $response->children()->GetFolderResponse->children();

        return new \Zimbra\ZCS\Entity\Folder($fold[0]);
    }
    
    //Sauvegarde un document grace à son id (depuis une piece jointe ou un upload vie servlet)
    public function saveDocument($mail,$docId,$oldData=array()){
        $this->delegateAuth($mail);
        $baseDoc = array(
                            '_'=>array(
                                'upload'=> array(
                                    'id'=>$docId
                                )
                            )
                        );
        $newDoc = array_merge($baseDoc,$oldData);
        //Pour eviter mail.MODIFY_CONFLICT
        if($this->zimbraConnect->changeToken){
                $this->zimbraConnectChild->changeToken = $this->zimbraConnect->changeToken;
                $this->zimbraConnectChild->addContextChildAttr('change',array('token'=>$this->zimbraConnectChild->changeToken, 'type'=>'mod'));
        }
        $params =  array(
                        'doc'=> $newDoc
                        );
        
        $response = $this->zimbraConnectChild->requestMail('SaveDocumentRequest', array(), $params);
        
        $doc = $response->children()->SaveDocumentResponse->children();
        //simplexml_dump($doc[0]);
        //return new \Zimbra\ZCS\Entity\Folder($fold[0]);
    }
    
    
    
    //Liste les siagntures du compte $mail
    public function getSignatures($mail){
        $this->delegateAuth($mail);
        $response = $this->zimbraConnectChild->requestAccount('GetSignaturesRequest');
        
        $results = array();
        foreach ($response->children()->GetSignaturesResponse->children() as $sig) {
            $results[] = new \Zimbra\ZCS\Entity\Signature($sig);
        }

        return $results;
    }
    
    
    //Ajoute une signature au compte $mail
    public function addSignature($mail,$params=array()){
        $this->delegateAuth($mail);
        $default=array(
                'signature'=>array(
                        'name'=>'Abtel_auto',
                        '_'=>array('content'=>array(
                                            'type'=>'text/html',
                                            '_'=>'Cordialement'
                                            ))
                )
        );
        $params = array_replace_recursive($default,$params);
        
        $response = $this->zimbraConnectChild->requestAccount('CreateSignatureRequest',array(),$params);
        
        $sig = $response->children()->CreateSignatureResponse->children(); 

        return new \Zimbra\ZCS\Entity\Signature($sig[0]);
    }
    
    //Supprime une signature du compte $mail
    public function delSignature($mail,$params=array()){
        $this->delegateAuth($mail);
        $default=array(
                'signature'=>array(
                        'name'=>'Abtel_auto',
                )
        );
        $params = array_replace_recursive($default,$params);
        
        $response = $this->zimbraConnectChild->requestAccount('DeleteSignatureRequest',array(),$params);

        $sig = $response->children()->DeleteSignatureResponse->children(); 
        
        return false;
    }
}
