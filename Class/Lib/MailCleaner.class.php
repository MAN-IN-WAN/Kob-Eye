<?php

class MailCleaner {

    const MC_URL = 'https://mailcleaner1.abtel.link/api/soap/?wsdl';


    function __construct($infra) {
        print "In BaseClass constructor\n";
    }

    //Connexion à l'api Mailcleaner
    private static function connect(){
        $mc = new SoapClient(self::MC_URL);
        return $mc;
    }


    /*********************************
     *   DOMAINS
    *********************************/
    public static function addDomain($domain){
        $mc = self::connect();
        $res = $mc->domainAdd($domain);

        return $res['status_code'] == 200;
    }

    public static function delDomain($domain){
        $mc = self::connect();
        $res = $mc->domainRemove($domain);

        return $res['status_code'] == 200;
    }

    public static function editDomain($domain,$params){
        $mc = self::connect();

        $res = $mc->domainEdit($domain,$params);

        return $res['status_code'] == 200;
    }

    public static function showDomain($domain){
        $mc = self::connect();
        $res = $mc->domainShow($domain);

        if($res['status_code'] == 200){
            return $res;
        }else{
            return false;
        }
    }

    public static function listDomains(){
        $mc = self::connect();
        $res = $mc->domainList();

        if($res['status_code'] == 200){
            return $res;
        }else{
            return false;
        }
    }

    public static function domainExists($domain){
        $mc = self::connect();

        $res = $mc->domainExists($domain);

        return $res['status_code'] == 200;
    }

    /******************************
    *    USERS
    *******************************/
    public static function addUser($user){
        $mc = self::connect();
        $res = $mc->userAdd($user);

        return $res['status_code'] == 200;
    }

    public static function delUser($user){
        $mc = self::connect();
        $res = $mc->userRemove($user);

        return $res['status_code'] == 200;
    }

    public static function editUser($user,$params){
        $mc = self::connect();

        $res = $mc->userEdit($user,$params);

        return $res['status_code'] == 200;
    }

    public static function showUser($user){
        $mc = self::connect();
        $res = $mc->userShow($user);

        if($res['status_code'] == 200){
            return $res;
        }else{
            return false;
        }
    }

    public static function listUsers(){
        $mc = self::connect();
        $res = $mc->userList();

        if($res['status_code'] == 200){
            return $res;
        }else{
            return false;
        }
    }

    public static function userExists($domain){
        $mc = self::connect();
        $res = $mc->userExists($domain);

        return $res['status_code'] == 200;
    }

    /******************************
     *    Addresses
     *******************************/
    public static function addAddress($address){
        $mc = self::connect();
        $res = $mc->addressAdd($address);

        return $res['status_code'] == 200;
    }

    public static function delAddress($address){
        $mc = self::connect();
        $res = $mc->addressRemove($address);

        return $res['status_code'] == 200;
    }

    public static function editAddress($address,$params){
        $mc = self::connect();

        $res = $mc->addressEdit($address,$params);

        return $res['status_code'] == 200;
    }

    public static function listAddresses(){
        $mc = self::connect();
        $res = $mc->addressList();

        if($res['status_code'] == 200){
            return $res;
        }else{
            return false;
        }
    }

    public static function addressExists($address){
        $mc = self::connect();
        $res = $mc->addressExists($address);

        return $res['status_code'] == 200;
    }

}