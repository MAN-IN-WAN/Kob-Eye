<?php

class Mib
{
    private $con_handle = null;
    private $token = null;
    private $url = null;

    //Connexion à l'api MailInBlack

    /**
     * Mib constructor.
     * @param null $infra
     * @throws Exception
     */
    function __construct($infra = null)
    {
        if (!$infra)
            $infra = Sys::getOneData('Parc', 'Infra/Type=Mail&Default=1');

        if (!$infra)
            throw new Exception('Aucune infra fournie et aucune infra de mail par défaut.');


        $serv = $infra->getOneChild('Server/MailInBlack=1');

        if (!$serv)
            throw new Exception('Aucun serveur MailCleaner de disponible.');

        $this->url = "https://' . $serv->DNSNom . '/app/api/v1.0/";
        $user = $serv->MIBUser;
        $admin = $serv->MIBPass;
        
        $this->con_handle = curl_init($this->url . 'login');
        curl_setopt($this->con_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");
        $data = json_encode(array('username' => $user, 'password' => $admin));
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($this->con_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->con_handle, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = json_decode(curl_exec($this->con_handle), true);
        if (!$ret) $ret = json_decode(curl_exec($this->con_handle), true); // Retry en cas de timeout
        if ($ret && $ret['token']) {
            $this->token = $ret['token'];
        }
    }

    /*********************************
     *   CLIENT
     *********************************/
    /**
     * @param $client : nom Client
     * @return mixed
     */
    public function addClient($client)
    {
        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'clients');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");
        $client = array(
            'name' => $client,
            'step' => 1000,
            'templateId' => 1,
            'defaultTemplate' => 1
        );
        $data = json_encode($client);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /**
     * @param $search array(name => value / domain => value)
     * @param array $params (
     *                      size -> items par page (20) /+
     *                      page (1) /+
     *                      projection -> granularité du resultat (clientListProjection)
     *                              |-> clientWithAll -> liste des clients + les domaines + les utilisateurs + les parametres de ceux-ci
     *                              |-> clientWithDomain -> liste des clients + les domaines
     *                              |-> clientListProjection -> liste de clients uniquement
     *                      )
     * @return mixed
     */
    public function getClient($search, $params = array())
    {
        $paramsDefault = array(
            'size' => 20,
            'page' => 1,
            'projection' => 'clientListProjection'
        );

        $params = array_replace($paramsDefault, $params);
        $params = array_merge($params, $search);

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'findByGlobalSearch');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        if ($params['projection'] == 'clientListProjection') {
            return $ret['_embedded']['clients'];
        } else {
            //TODO
        }

        return true;
    }

    /**
     * @param $client
     * @param $params (logo / color)
     * @return mixed
     *
     */
    public function editClient($client, $params)
    {
        $codes = array(
            'logo' => 16,
            'color' => 17
        );

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        $rets = array();
        foreach ($params as $k => $param) {
            curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'clientHasFields/' . $codes[$k] . '-' . $cId);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "PATCH");
            $param = array(
                'value' => $param,
                'defaut' => false
            );
            $data = json_encode($param);
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'X-Auth-Token: ' . $this->token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $ret = json_decode(curl_exec($this->con_handle), true);
            $rets[] = $ret;
        }

        return $rets;
    }

    /*********************************
     *   LICENSES
     *********************************/
    /**
     * @param $client : nom Client
     * @param array $params (
     *              endContract -> Date de fin du contrat  "Y-m-d\TH:i:s.uO" Ex : 2099-01-01T00:00:00.000+0000 /+
     *              flag -> 4 for "Use global license parameters" and 1 if it is not the case /+
     *              nbLicense -> Must be set if flag is equals to 4, put -1 if flag is 4
     *          )
     * @return mixed
     */
    public function addLicense($client, $params = array())
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'licenses');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $license = array(
            'clientId' => $cId,
            'endContract' => '2099-01-01T00:00:00.000+0000',
            'flag' => 4,
            'nbLicense' => -1
        );
        $license = array_replace($license, $params);

        $data = json_encode($license);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /**
     * @param $client
     * @param array $params cf addLicense
     * @return mixed
     */
    public function editLicense($client, $params = array())
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'licenses');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "PUT");

        $license = array(
            'clientId' => $cId,
            'endContract' => '2099-01-01T00:00:00.000+0000',
            'flag' => 4,
            'nbLicense' => -1
        );
        $license = array_replace($license, $params);

        $data = json_encode($license);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /**
     * @param $client
     * @return mixed
     */
    public function getLicense($client)
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'licenses');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");

        $license = array(
            'clientId' => $cId
        );

        $data = json_encode($license);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }



    /*********************************
     *   DOMAINS
     *********************************/
    /**
     * @param $client
     * @param $domain
     * @param array $params (
     *              port (25) -> port pour les serveurs destinations /+
     *              templateId (1) -> template1  tjs présent.
     *          )
     * @return mixed
     */
    public function addDomain($client, $domain, $params = array())
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'domains');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $domain = array(
            'clientId' => $cId,
            'domain' => $domain,
            'defaultTemplate' => true,
            'flag' => 6,
            'port' => 25,
            'templateId' => 1
        );
        $domain = array_replace($domain, $params);

        $data = json_encode($domain);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /**
     * @param $search (client => value / domain => value / server => value)
     * @param array $params (
     *                      size -> items par page (20) /+
     *                      page (1) /+
     *                      projection -> granularité du resultat (clientListProjection)
     *                              |-> domainWithServer -> liste des domaines + les serveurs
     *                              |-> domainWithClient -> liste des domaines + les serveurs + les clients
     *                      )
     * @return bool
     */
    public function getDomain($search, $params = array())
    {
        if (!empty($search['client'])) {
            $cli = self::getClient(array('name' => $search['client']));
            $cId = $cli[0]['id'];
            $search = array('clientId' => $cId);
        }

        $paramsDefault = array(
            'size' => 20,
            'page' => 1,
            'projection' => 'domainWithServer'
        );

        $params = array_replace($paramsDefault, $params);
        $params = array_merge($params, $search);

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'findByGlobalSearch');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        if ($params['projection'] == 'domainWithServer') {
            return $ret['_embedded']['domains'];
        } else {
            //TODO
        }

        return true;
    }

    /**
     * @param $client
     * @param $domain
     * @param $server
     * @param int $priority
     * @return mixed
     */
    public function addDomainServer($client, $domain, $server, $priority = 1)
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        $dom = self::getDomain(array('domain' => $domain));
        $dId = $dom[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'domainServers');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $domainServer = array(
            'clientId' => $cId,
            'domain' => $dId,
            'priority' => $priority,
            'server' => $server
        );

        $data = json_encode($domainServer);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /******************************
     *    USERS
     *******************************/
    /**
     * @param $client
     * @param $mail
     * @param array $params (
     *                      firstname /+
     *                      lastname /+
     *                      licence /+
     *                          |-> 0 : Transparent
     *                          |-> 1 : Inactive
     *                          |-> 2 : Unprotected
     *                          |-> 3 : Protected
     *                       profileId /+
     *                          |-> 1 : Admin
     *                          |-> 2 : Manager
     *                          |-> 3 : User
     *                          |-> 4 : Retailer
     *                          |-> 5 : EasyManage
     *                       emails
     *                      )
     * @return mixed
     */
    public function addUser($client, $mail, $params = array())
    {
        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'domains');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $emails = array($mail);
        if (!empty($params['emails'])) {
            $emails = array_merge($emails, $params['emails']);
            unset($params['emails']);
        }
        $name = explode('@', $mail);
        $name = $name[0];

        $user = array(
            'clientId' => $cId,
            'firstname' => '',
            'lastname' => $name,
            'license' => 2,
            'profileId' => 3,
            'email' => $emails
        );
        $user = array_replace($user, $params);

        $data = json_encode($user);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        return $ret;
    }

    /**
     * @param $search ( client => value / domain => value / mail => value / name => value / licence => value (0/1/2/3) )
     * @param array $params (
     *                      size -> items par page (20) /+
     *                      page (1) /+
     *                      projection -> granularité du resultat (clientListProjection)
     *                              |-> userWithAll -> liste des utilisateur et tout ce qui leur est lié
     *                              |-> userId -> liste Id utilisateur seulement
     *                              |-> userWithMail -> liste des utilisateur et mails associés
     *                      )
     * @return bool
     */
    public function getUser($search, $params = array())
    {
        if (!empty($search['client'])) {
            $cli = self::getClient(array('name' => $search['client']));
            $cId = $cli[0]['id'];
            $search = array('clientId' => $cId);
        } elseif (!empty($search['domain'])) {
            $dom = self::getDomain(array('domain' => $search['domain']));
            $dId = $dom[0]['id'];
            $search = array('domainId' => $dId);
        } elseif (!empty($search['mail'])) {
            $search = array('inputMail' => $search['mail']);
        } elseif (!empty($search['name'])) {
            $search = array('inputName' => $search['name']);
        }

        $paramsDefault = array(
            'size' => 20,
            'page' => 1,
            'projection' => 'domainWithServer'
        );

        $params = array_replace($paramsDefault, $params);
        $params = array_merge($params, $search);

        curl_setopt($this->con_handle, CURLOPT_URL, $this->url . 'findByGlobalSearch');
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $this->token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($this->con_handle), true);

        if ($params['projection'] == 'userId') {
            return $ret['_embedded']['domains'];
        } else {
            //TODO
        }

        return true;
    }

    //TODO : Edit users a tâtons car pas défini dans la doc
}