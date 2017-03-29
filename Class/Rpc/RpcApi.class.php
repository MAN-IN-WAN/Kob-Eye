<?php
//require_once('/usr/share/pear/XML/RPC2/Client.php');
require_once(__DIR__ .'/../Lib/phpxmlrpc-master/src/Autoloader.php');

class RpcApi extends Root{
        
        protected static $apiurl = '';
        public static $encoder = null;
        protected static $client = null;
        protected static $debug = 0;
        protected static $initialized = 0;
        //tableau à encoder en json pour retour gestion
        public static $jReturn = array(
                                        'status'=> 'Erreur',
                                        'message'=> 'Erreur inconnue',
                                        'objetRpc'=> null
                                 );
        
	public function __construct() {}
        
        
        
        
        //Init des variables statiques
        protected static function init(){
                if(self::$initialized) return true;
                
                PhpXmlRpc\Autoloader::register();
                self::$encoder = new PhpXmlRpc\Encoder();
                self::$client = new PhpXmlRpc\Client(static::$apiurl);
                self::$client->setDebug(static::$debug);
                
                self::$initialized =1;
        }
        
        /*Crée une requète phpxmlrpc
         *
         *@params       -method (String)
         *              -arguments (String/Array)
         *              -parameters (String/Array)
         *
         */
        protected static function mkRequest($method,$args){
                $method = func_get_arg(0);
                $args = is_array($args)? $args : array($args);
                $args = array_map("self::encode",$args);
                $request = new PhpXmlRpc\Request($method,$args);
                
                if(static::$debug == 1)
                        print_r(htmlentities($request->serialize()));

                return $request ;
        }
        
        /*Gère le résultat de la requète phpxmlrpc
         *
         *@params       -result (xmlrpc query result)
         *
         */
        protected static function result($res,$print = false){
                
                if (!$res->faultCode()) {
                        $v = $res->value();
                        $final = self::decode($v);
                        if($print){
                                print_r('<pre>');
                                print_r($final);
                                print_r('</pre>');
                        }
                        
                        return $final;
                } else {
                        if($print){
                                print "<br/>An error occurred: ";
                                print "Code: " . htmlspecialchars($res->faultCode()). "<br/> Reason: '" . htmlspecialchars($res->faultString()) . "'</pre><br/>";
                        }
                }
                
                return false;
        }
        
        
        //Utilisation de l'encoder xmlrpc
        protected static function encode($var){
                return self::$encoder->encode($var);
        }
        protected static function decode($var){
                return self::$encoder->decode($var);
        }
        
}

?>
