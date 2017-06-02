<?php
//require_once('/usr/share/pear/XML/RPC2/Client.php');
require_once(__DIR__ .'/../Lib/phpxmlrpc-master/src/Autoloader.php');

class GandiApi extends RpcApi{
        
        private static $apikey = 'nUYedzVkVV3G6GvnvswWyHSJ';
        protected static $apiurl = 'https://rpc.ote.gandi.net/xmlrpc/';
       
        
	public function __construct() {}
        
        static function test(){
                $domain ='abtel.com';  
                //$params = array(
                //        'given'=> 'Guillaume',
                //        'family'=> 'Candella',
                //        'email'=> 'gcandella@abtel.fr',
                //        'streetaddr'=> 'Parc Delta',
                //        'zip'=> '30390',
                //        'city'=> 'Bouillargues',
                //        'country'=> 'FR',
                //        'phone'=>'+33.123456789',
                //        'type'=> 0,
                //        'password'=> 'sercret'
                //);
                //self::contactCreate($params);
                
                
                

                //
                //$params = array(
                //        'extra_parameters' => Array
                //                (
                //                    'birth_date' => '1985-02-09',
                //                    'birth_department' => '30',
                //                    'birth_city' => 'Nimes',
                //                    'birth_country' => 'FR'
                //                )
                //);
                //self::contactUpdate('GC27-GANDI', $params);
                
                //self::operationRelaunch(251546);
                
                
                //********************************************************
                
                //self::domainList(true);
                
                //self::operationList(true);
                
                //self::creerDomaine('abtel.net');
                
                //self::renouvelerDomaine('abtel.com');
                
                //self::contactInfo('JN19-GANDI',true);
                
                self::recupererPrix('create','abtel.us', 1);
        }
        // ===================== FONCTIONS POUR LA GESTION
        //Création d'un domaine avec retour pour la gestion
        static function  creerDomaine($domain,$contact = 'JN19-GANDI', $nameservers = array('ns1.abtel.fr', 'ns2.abtel.fr')){
                $da = self::domainAvailable($domain);
                
                if($da[$domain] != 'available'){
                        self::$jReturn['message'] = 'Le domaine "'.$domain.'" semble indisponnible. Retour Gandi : "'.$da[$domain].'".';
                        self::$jReturn['objetRpc'] = $da;
                        exit (json_encode(self::$jReturn));
                }
                
                $ct = self::contactTest(array($contact,array('domain'=>$domain,'owner'=>True,'tech'=>True,'bill'=>True,'admin'=>True)));
                 
                if($ct != 1){
                        self::$jReturn['message'] = 'Le contact "'.$contact.'" ne semble pas pouvoir être affecté au domaine "'.$domain.'". Voir objetRpc';
                        self::$jReturn['objetRpc'] = $ct;
                        exit (json_encode(self::$jReturn));
                }                
                                
                $params = array(
                                'admin'=> $contact,
                                'bill'=> $contact,
                                'duration'=> 1,
                                'owner'=> $contact,
                                'tech'=> $contact,
                                'nameservers' => array('ns1.abtel.fr', 'ns2.abtel.fr')
                );
                
                $dc = self::domainCreate(array($domain,$params));
                if(is_array($dc) && $dc['errortype'] == ''){
                        self::$jReturn['status'] = 'Succes';
                        self::$jReturn['message'] = 'La procédure d\'enregistrement du domaine "'.$domain.'" a bien été initiée.';
                        self::$jReturn['objetRpc'] = $dc;
                } else {
                        self::$jReturn['message'] = 'La procédure d\'enregistrement du domaine "'.$domain.'" a échoué. Voir objetRpc';
                        self::$jReturn['objetRpc'] = $dc;
                }
                
                exit (json_encode(self::$jReturn));
        }
        
        //Renouvellemement d'un domaine avec retour pour la gestion
        static function renouvelerDomaine($domain,$duration = 1){
                $di = self::domainInfo($domain);
                
                $date_fin = $di['date_registry_end'];
                $annee_fin = substr($date_fin,0,4);
                
                $params = array(
                                'current_year'=> (int)$annee_fin,
                                'duration'=> $duration
                                );
                $dr = self::domainRenew($domain,$params);
                
                 if(is_array($dr) && $dr['errortype'] == ''){
                        self::$jReturn['status'] = 'Succes';
                        self::$jReturn['message'] = 'La procédure de renouvellement du domaine "'.$domain.'" a bien été initiée.';
                        self::$jReturn['objetRpc'] = $dr;
                } else {
                        self::$jReturn['message'] = 'La procédure de renouvellement du domaine "'.$domain.'" a échoué. Voir objetRpc';
                        self::$jReturn['objetRpc'] = $dr;
                }
                
                exit (json_encode(self::$jReturn));
        }
        
        //Recuperation des prix avec retour pour la gestion
        /*
         * action -> renew / create (autres à ajouter si besoin)
         * duration -> Durée en année (0 pour avoir l'ensemble des plages tarifaires)
         * description -> extension du nom de domaine. (.fr/.com ...) A priori si l'on fourni le nom de domain entier cela fonctionne quand même
         **/
        static function recupererPrix($action, $description, $duration = 0){
                $ab = self::accountBalance();
                $grid = $ab['grid'];
                $dispo = $ab['prepaid']['amount'];

                $params = array(
                'product' => array(
                                   'type'=> 'domain',
                                   'description'=>$description
                                   ),
                'action' => array(
                                  'name'=>$action,
                                  'duration'=>$duration
                                 )
                );

                
                $cl = self::catalogList($params,$grid);
                
                if(count($cl) && $cl !== false){
                        self::$jReturn['status'] = 'Succes';
                        self::$jReturn['message'] = 'Le prix pour l\'action souhaitée a pu être récupéré. Voir objetRpc';
                        self::$jReturn['objetRpc'] = $cl;
                } else {
                        self::$jReturn['message'] = 'Le prix pour l\'action souhaitée n\'a pas pu être récupéré. Voir objetRpc (Si c\'est un tableau vide cela signifie que la durée est trop longue)';
                        self::$jReturn['objetRpc'] = $cl;
                }
                
                exit (json_encode(self::$jReturn));    
        }
        
        // =====================  API DOMAIN
        //Teste si un domain est déja reservé ou non
        static function domainAvailable($domain,$print = false){
                if(!is_array($domain)) $domain = array($domain);
                self::init();
                
                $fault = 0;
                $resOk = 0;
                
                $req = self::mkRequest('domain.available',array(self::$apikey,$domain));
                $res = self::$client->send($req);
                if (!$res->faultCode()) {
                        $v = $res->value();
                        $v = self::decode($v);
                        $status = $v[$domain[0]];
                        if($status != 'pending') $resOk = 1;
                } else {
                        $fault = 1;
                }

                while(!$fault && !$resOk){
                        usleep(700000); //700ms
                        $res = self::$client->send($req);  
                        if (!$res->faultCode()) {
                                $v = $res->value();
                                $v = self::decode($v);
                                $status = $v[$domain[0]];
                                if($status != 'pending') $resOk = 1;
                        } else {
                                $fault = 1;
                        }
                }
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renvoi la liste des domaines lié à ce compte
        static function domainList($print = false){
                self::init();

                $req = self::mkRequest('domain.list',array(self::$apikey));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renvoi les infos du domaine handle
        static function domainInfo($domain,$print = false){
                self::init();
                
                if(is_array($domain))$domain = $domain[0];

                $req = self::mkRequest('domain.info',array(self::$apikey,$domain));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Crée un domaine avec les params passés en argument
        static function domainCreate($params,$print = false){
                self::init();
                
                if(is_array($params) && sizeof($params)>1){
                        $domain = $params[0];
                        $params = $params[1];
                } else{
                       return false; 
                }

                $req = self::mkRequest('domain.create',array(self::$apikey,$domain,$params));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Donne les infos d'un domaine y compris son prix
        static function domainPrice($domain,$print = false){
                self::init();
                
                if(is_array($domain))$domain = $domain[0];

                $req = self::mkRequest('domain.price',array(self::$apikey,$domain));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renouvelle un domaine
        static function domainRenew($domain,$params,$print = false){
                self::init();
                
                if(is_array($domain))$domain = $domain[0];

                $req = self::mkRequest('domain.renew',array(self::$apikey,$domain,$params));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        
        
        
        // =====================  API CONTACT
        //Renvoi la balance du compte prépayé sur gandi
        static function accountBalance($print = false){
                self::init();

                $req = self::mkRequest('contact.balance',array(self::$apikey));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renvoi la liste des contacts lié à ce compte
        static function contactList($print = false){
                self::init();

                $req = self::mkRequest('contact.list',array(self::$apikey));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renvoi les infos du contact handle
        static function contactInfo($handle,$print = false){
                self::init();
                
                if(is_array($handle))$handle = $handle[0];

                $req = self::mkRequest('contact.info',array(self::$apikey,$handle));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Crée un contact avec les params passés en argument
        static function contactCreate($params,$print = false){
                self::init();

                $req = self::mkRequest('contact.create',array(self::$apikey,$params));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Crée un contact avec les params passés en argument
        static function contactUpdate($params,$print = false){
                self::init();

                if(is_array($params) && sizeof($params)>1){
                        $handle = $params[0];
                        $params = $params[1];
                } else{
                       return false; 
                }
                $req = self::mkRequest('contact.update',array(self::$apikey,$handle,$params));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Supprime le contact avec le handle passé en argument
        static function contactDelete($handle,$print = false){
                self::init();
                
                if(is_array($handle))$handle = $handle[0];

                $req = self::mkRequest('contact.delete',array(self::$apikey,$handle));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Test la vlidité d'un contact avant de l'associer
        static function contactTest($params,$print = false){
                self::init();

                if(is_array($params) && sizeof($params)>1){
                        $handle = $params[0];
                        $domain = $params[1];
                } else{
                       return false; 
                }
                
                $req = self::mkRequest('contact.can_associate_domain',array(self::$apikey,$handle,$domain));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        // =====================  API CATALOG
        
        //Renvoie les prix pour l'action demandée
        static function catalogList($params,$grid,$print = false){
                self::init();
                
                $req = self::mkRequest('catalog.list',array(self::$apikey,$params,'EUR',$grid));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        // =====================  API OPERATION
        //Renvoi les infos de la liste des opérations
        static function operationList($print = false){
                self::init();
                
                $req = self::mkRequest('operation.list',array(self::$apikey));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Renvoi les infos de l'opération passée en argument
        static function operationInfo($codeOp,$print = false){
                self::init();

                if(is_array($codeOp))$codeOp = $codeOp[0];
                $codeOp = (int)$codeOp;

                $req = self::mkRequest('operation.info',array(self::$apikey,$codeOp));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Annule l'opération fournie en argument
        static function operationCancel($codeOp,$print = false){
                self::init();
                
                if(is_array($codeOp))$codeOp = $codeOp[0];
                $codeOp = (int)$codeOp;

                $req = self::mkRequest('operation.cancel',array(self::$apikey,$codeOp));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
        //Relance l'opération passée en argument
        static function operationRelaunch($codeOp,$print = false){
                self::init();
                
                if(is_array($codeOp))$codeOp = $codeOp[0];
                $codeOp = (int)$codeOp;

                $req = self::mkRequest('operation.relaunch',array(self::$apikey,$codeOp));
                $res = self::$client->send($req);
                
                $final = self::result($res,$print);
                return $final;
        }
        
}

?>
