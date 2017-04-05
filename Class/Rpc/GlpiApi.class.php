<?php
//require_once('/usr/share/pear/XML/RPC2/Client.php');
require_once(__DIR__ .'/../Lib/phpxmlrpc-master/src/Autoloader.php');

class GlpiApi extends RpcApi{
        
        
        private static $mainUser = 'glpi';//'parc2';
        private static $mainPass = 'd0fYj89';//'zH34Y6u5';
        private static $secondaryUser = 'parc2';
        private static $secondaryPass = 'zH34Y6u5';
        protected static $apiurl = 'http://glpi.abtel.fr/glpi/plugins/webservices/xmlrpc.php';
        private static $sessId = '';
        
	public function __construct() {}

        public function test(){
                self::init();
                
                $req = self::mkRequest('glpi.getObject',array(array(
                                                                        //'help'=>true,
                                                                        'itemtype'=>'Computer',
                                                                        'id'=>121,
                                                                        'with_software' => true,
                                                                        'show_label'=>true,
                                                                        'show_name'=>true,
                                                                        'with_infocom' => true,
                                                                        'with_networkport' => true,
                                                                        'with_phone' => true,
                                                                        'with_printer' => true,
                                                                        'with_monitor' => true,
                                                                        'with_peripheral' => true,
                                                                        'with_document' => true,
                                                                        //'with_ticket' => true,
                                                                        //'with_tickettask' => true,
                                                                        //'with_ticketfollowup' => true,
                                                                        //'with_ticketvalidation' => true,
                                                                        'with_reservation' => true,
                                                                        'with_software' => true,
                                                                        'with_softwareversion' => true,
                                                                        'with_softwarelicense' => true,
                                                                        'with_contract' => 'true'
                                                                    )));
                $res = self::$client->send($req);
                
                $final = self::result($res,true);
                
                $req = self::mkRequest('fusioninventory.computerextendedinfo',array(array('computers_id'=>121,'xmlrpc'=>true)));
                $res = self::$client->send($req);
                
                $computers = self::result($res,true);
        }
        
        
        public function getComputerList(){
                self::init();
                
                $req = self::mkRequest('glpi.listObjects',array(array('itemtype'=>'computer', 'start'=>0, 'limit'=>9999)));
                $res = self::$client->send($req);
                
                $computers = self::result($res);
                
                exit (json_encode($computers));
        }
        
        public function getComputerInfo(){
                self::init();
                
                $req = self::mkRequest('glpi.getObject',array(array(
                                                                        //'help'=>true,
                                                                        'itemtype'=>'Computer',
                                                                        'id'=>121,
                                                                        'with_software' => true,
                                                                        'show_label'=>true,
                                                                        'show_name'=>true,
                                                                        'with_infocom' => true,
                                                                        'with_networkport' => true,
                                                                        'with_phone' => true,
                                                                        'with_printer' => true,
                                                                        'with_monitor' => true,
                                                                        'with_peripheral' => true,
                                                                        'with_document' => true,
                                                                        //'with_ticket' => true,
                                                                        //'with_tickettask' => true,
                                                                        //'with_ticketfollowup' => true,
                                                                        //'with_ticketvalidation' => true,
                                                                        'with_reservation' => true,
                                                                        'with_software' => true,
                                                                        'with_softwareversion' => true,
                                                                        'with_softwarelicense' => true,
                                                                        'with_contract' => 'true'
                                                                    )));
                $res = self::$client->send($req);
                
                $computer = self::result($res,true);
                
                $req = self::mkRequest('fusioninventory.computerextendedinfo',array(array('computers_id'=>121,'xmlrpc'=>true)));
                $res = self::$client->send($req);
                
                $computerFusion = self::result($res,true);
                
                //TODO filtrer & recoller
                
        }
        
        
        //Connection pour recup un sessId
        public function connect(){ 
                $req = self::mkRequest('glpi.doLogin',array(array('login_name'=>self::$mainUser,'login_password'=>self::$mainPass,'username'=>self::$secondaryUser,'password'=>self::$secondaryPass)));
                $res = self::$client->send($req);
                
                $final = self::result($res);
                self::$sessId = $final['session'];
        }
        
        
        //Init des variables statiques
        protected static function init(){
                if(self::$initialized) return true;
                
                PhpXmlRpc\Autoloader::register();
                self::$encoder = new PhpXmlRpc\Encoder();
                self::$client = new PhpXmlRpc\Client(self::$apiurl);
                self::$client->setDebug(self::$debug);
                self::connect();
                self::$client = new PhpXmlRpc\Client(self::$apiurl.'?session='.self::$sessId);
                
                self::$initialized = 1;
        }
        
}

?>
