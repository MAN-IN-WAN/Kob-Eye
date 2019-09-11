<?php

class Mib
{
    const BASEURL = "https://mib.abtel.link/app/api/v1.0/";
    const APIUSER = "admin";
    const APIPASS = "mailinblack";

    //Connexion à l'api MailInBlack

    /**
     * @return array|null
     */
    private static function connect()
    {
        $con_handle = curl_init(self::BASEURL . 'login');
        curl_setopt($con_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");
        $data = json_encode(array('username' => self::APIUSER, 'password' => self::APIPASS));
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($con_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($con_handle, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = json_decode(curl_exec($con_handle), true);
        if (!$ret) $ret = json_decode(curl_exec($con_handle), true); // Retry en cas de timeout
        if ($ret && $ret['token']) {
            $api_token = $ret['token'];
            return array('handle' => $con_handle, 'token' => $api_token);
        } else {
            return null;
        }
    }

    /*********************************
     *   CLIENT
     *********************************/
    /**
     * @param $client : nom Client
     * @return mixed
     */
    public static function addClient($client)
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];
        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'clients');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");
        $client = array(
            'name' => $client,
            'step' => 1000,
            'templateId' => 1,
            'defaultTemplate' => 1
        );
        $data = json_encode($client);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function getClient($search, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $paramsDefault = array(
            'size' => 20,
            'page' => 1,
            'projection' => 'clientListProjection'
        );

        $params = array_replace($paramsDefault, $params);
        $params = array_merge($params, $search);

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'findByGlobalSearch');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function editClient($client, $params)
    {
        $codes = array(
            'logo' => 16,
            'color' => 17
        );
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        $rets = array();
        foreach ($params as $k => $param) {
            curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'clientHasFields/' . $codes[$k] . '-' . $cId);
            curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "PATCH");
            $param = array(
                'value' => $param,
                'defaut' => false
            );
            $data = json_encode($param);
            curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                    'X-Auth-Token: ' . $mc['token'],
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $ret = json_decode(curl_exec($con_handle), true);
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
    public static function addLicense($client, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'licenses');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $license = array(
            'clientId' => $cId,
            'endContract' => '2099-01-01T00:00:00.000+0000',
            'flag' => 4,
            'nbLicense' => -1
        );
        $license = array_replace($license, $params);

        $data = json_encode($license);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

        return $ret;
    }

    /**
     * @param $client
     * @param array $params cf addLicense
     * @return mixed
     */
    public static function editLicense($client, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'licenses');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "PUT");

        $license = array(
            'clientId' => $cId,
            'endContract' => '2099-01-01T00:00:00.000+0000',
            'flag' => 4,
            'nbLicense' => -1
        );
        $license = array_replace($license, $params);

        $data = json_encode($license);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

        return $ret;
    }

    /**
     * @param $client
     * @return mixed
     */
    public static function getLicense($client)
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'licenses');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "GET");

        $license = array(
            'clientId' => $cId
        );

        $data = json_encode($license);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function addDomain($client, $domain, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'domains');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");

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
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function getDomain($search, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

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

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'findByGlobalSearch');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function addDomainServer($client, $domain, $server, $priority = 1)
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        $dom = self::getDomain(array('domain' => $domain));
        $dId = $dom[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'domainServers');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");

        $domainServer = array(
            'clientId' => $cId,
            'domain' => $dId,
            'priority' => $priority,
            'server' => $server
        );

        $data = json_encode($domainServer);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function addUser($client, $mail, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

        $cli = self::getClient(array('name' => $client));
        $cId = $cli[0]['id'];

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'domains');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "POST");

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
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

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
    public static function getUser($search, $params = array())
    {
        $mc = self::connect();
        $con_handle = $mc['handle'];

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

        curl_setopt($con_handle, CURLOPT_URL, self::BASEURL . 'findByGlobalSearch');
        curl_setopt($con_handle, CURLOPT_CUSTOMREQUEST, "GET");
        $data = json_encode($params);
        curl_setopt($con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($con_handle, CURLOPT_HTTPHEADER, array(
                'X-Auth-Token: ' . $mc['token'],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($con_handle), true);

        if ($params['projection'] == 'userId') {
            return $ret['_embedded']['domains'];
        } else {
            //TODO
        }

        return true;
    }

    //TODO : Edit users a tâtons car pas défini dans la doc
}