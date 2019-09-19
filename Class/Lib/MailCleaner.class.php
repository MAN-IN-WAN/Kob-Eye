<?php

class MailCleaner
{
    private $mc = null;

    /**
     * MailCleaner constructor.
     * @param null $infra
     * @throws SoapFault
     * @throws Exception
     */
    function __construct($infra = null)
    {
        if (!$infra)
            $infra = Sys::getOneData('Parc', 'Infra/Type=Mail&Default=1');

        if (!$infra)
            throw new Exception('Aucune infra fournie et aucune infra de mail par défaut.');


        $serv = $infra->getOneChild('Server/MailCleaner=1');

        if (!$serv)
            throw new Exception('Aucun serveur MailCleaner de disponible.');

        $this->mc = new SoapClient('http://' . $serv->DNSNom . '/api/soap/?wsdl',array('connection_timeout' => 10));

        return true;
    }


    /*********************************
     *  DOMAINS
     *********************************/
    /**
     * @param $domain
     * @return bool
     */
    public function addDomain($domain)
    {
        $res = $this->mc->domainAdd($domain);

        return $res['status_code'] == 200;
    }

    /**
     * @param $domain
     * @return bool
     */
    public function delDomain($domain)
    {
        $res = $this->mc->domainRemove($domain);

        return $res['status_code'] == 200;
    }

    /**
     * @param $domain
     * @param $params
     * @return bool
     */
    public function editDomain($domain, $params)
    {
        $res = $this->mc->domainEdit($domain, $params);

        return $res['status_code'] == 200;
    }

    /**
     * @param $domain
     * @return bool
     */
    public function showDomain($domain)
    {
        $res = $this->mc->domainShow($domain);

        if ($res['status_code'] == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function listDomains()
    {
        $res = $this->mc->domainList();

        if ($res['status_code'] == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public function domainExists($domain)
    {
        $res = $this->mc->domainExists($domain);

        return $res['status_code'] == 200;
    }

    /******************************
     *  USERS
     *******************************/
    /**
     * @param $user
     * @return bool
     */
    public function addUser($user)
    {
        $res = $this->mc->userAdd($user);

        return $res['status_code'] == 200;
    }

    /**
     * @param $user
     * @return bool
     */
    public function delUser($user)
    {
        $res = $this->mc->userRemove($user);

        return $res['status_code'] == 200;
    }

    /**
     * @param $user
     * @param $params
     * @return bool
     */
    public function editUser($user, $params)
    {
        $res = $this->mc->userEdit($user, $params);

        return $res['status_code'] == 200;
    }

    /**
     * @param $user
     * @return bool
     */
    public function showUser($user)
    {
        $res = $this->mc->userShow($user);

        if ($res['status_code'] == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function listUsers()
    {
        $res = $this->mc->userList();

        if ($res['status_code'] == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public function userExists($domain)
    {
        $res = $this->mc->userExists($domain);

        return $res['status_code'] == 200;
    }

    /******************************
     *  Addresses
     *******************************/
    /**
     * @param $address
     * @return bool
     */
    public function addAddress($address)
    {
        $res = $this->mc->addressAdd($address);

        return $res['status_code'] == 200;
    }

    /**
     * @param $address
     * @return bool
     */
    public function delAddress($address)
    {
        $res = $this->mc->addressRemove($address);

        return $res['status_code'] == 200;
    }

    /**
     * @param $address
     * @param $params
     * @return bool
     */
    public function editAddress($address, $params)
    {
        $res = $this->mc->addressEdit($address, $params);

        return $res['status_code'] == 200;
    }

    /**
     * @return bool
     */
    public function listAddresses()
    {
        $res = $this->mc->addressList();

        if ($res['status_code'] == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * @param $address
     * @return bool
     */
    public function addressExists($address)
    {
        $res = $this->mc->addressExists($address);

        return $res['status_code'] == 200;
    }

}