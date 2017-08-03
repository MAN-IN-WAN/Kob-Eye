<?php
class Action extends genericClass {
        public $dbAbtel = null;
        private $error = '';
        
/*        public function __construct($o,$i){
                try{
                        $this->dbAbtel = new PDO('mysql:host=10.0.3.8;dbname=gestion','gestion','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                        $this->dbAbtel->query("SET AUTOCOMMIT=1");
                } catch (Exception $e){
                        $this->error .= print_r($e,true);
                }
                parent::__construct($o,$i);
        }
        
        //Crée les fichiers ics qui vont servir aux calendriers zimbra des employés abtel à  partir de la gestion
        public function getIcs(){

                file_put_contents( '/var/www/calendar/cal_status.log','ICS update started at '.date('d/m/y H:i:s').PHP_EOL,FILE_APPEND);
                
                require_once '/var/www/html/Class/Lib/rtf2html.php';
                
                $query = 'SELECT UTCODE as User, UT_MAIL as Mail FROM utilisateurs
                WHERE UTSTATUT = 1 ';
                
                try{
                        $q = $this->dbAbtel->query($query);
                }catch (Exception $e){
                        $this->error .= print_r($e,true);
                }
                
                if ($q)
                        $result = $q->fetchALL ( PDO::FETCH_ASSOC );
                else {
                        $result = Array();
                        $this->error .= print_r($this->dbAbtel->errorinfo(),true);
                }
                
                
                
                foreach ($result as $r){
                        $util = $r['User'];
                        
                        $query = 'SELECT ACDATECREATION as DateCrea, ACHEUREDEB as HeureDebut, ACHEUREFIN as HeureFin, ACTITRE as Titre, ACCLIENT as Client, ACNOTE as Description  FROM actions
                        WHERE ACUTIL = "'.$util.'" AND ACETAT = 1 ORDER BY ACDATECREATION DESC LIMIT 0,100';
                        try{
                                $q = $this->dbAbtel->query($query);
                        }catch (Exception $e){
                                $this->error .= print_r($e,true);
                        }
                        if ($q)
                                $result = $q->fetchALL ( PDO::FETCH_ASSOC );
                        else {
                                $result = Array();
                                $this->error .= print_r($this->dbAbtel->errorinfo(),true);
                        }
                        
                        
                        
                        $out=
'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ABTEL Mediterranee//Gestion v0.1//FR';
                        foreach ($result as $r){
                                $desc ='';
                                
                                if($r['Description'] !='' && $r['Description']!=null && strpos($r['Description'],'{\rtf1\ansi') !== false ){
                                        $reader = new RtfReader();
                                        $reader->Parse($r['Description']);
                                        $formatter = new RtfHtml();
                                        $desc=$formatter->Format($reader->root);
                                        $desc=strip_tags($desc);
                                }
                                
                                
                                //formatage des dates
                                $db = str_replace('-','',$r['DateCrea']).'T'.str_replace('-','',$r["HeureDebut"]).'';
                                $de = str_replace('-','',$r['DateCrea']).'T'.str_replace('-','',$r["HeureFin"]).'';
                                $out.='
BEGIN:VEVENT
DTSTART:'.$db.'
DTEND:'.$de.'
CATEGORIES:'.$r['Client'].'
SUMMARY:'.$r['Client'].' : '.$r['Titre'].'
DESCRIPTION: '.$desc.'
END:VEVENT';
                        }
                        $out.='
END:VCALENDAR';
                        
                        file_put_contents( '/var/www/calendar/ics/'.$util.'.ics',$out);
                        
                }
                
                if ($this->error != '')  file_put_contents( '/var/www/calendar/cal_error.log',date('d/m/y H:i:s').PHP_EOL.$this->error.PHP_EOL,FILE_APPEND);
                file_put_contents( '/var/www/calendar/cal_status.log','ICS update ended at '.date('y/m/d H:i:s').PHP_EOL,FILE_APPEND);
                file_put_contents( '/var/www/calendar/cal_status.log','++++++++++++++++++++'.PHP_EOL,FILE_APPEND);
                
                //return $out;
        }
        
        //Crée les calendriers dans les comptes zimbra des employés Abtel a partir d'ics
        public function creaCal(){

                //Recup de la liste des comptes utilisateur actifs et non royalcanin dans la gestion
                $query = "SELECT UTCODE as User, UT_MAIL as Mail FROM utilisateurs
                WHERE UTSTATUT = 1 AND `UTDOMAINE` != 'ROYAL-CANIN'";
                
                try{
                        $q = $this->dbAbtel->query($query);
                }catch (Exception $e){
                        $this->error .= print_r($e,true);
                }
                if ($q)
                        $result = $q->fetchALL ( PDO::FETCH_ASSOC );
                else {
                        $result = Array();
                        $this->error .= print_r($this->dbAbtel->errorinfo(),true);
                }
                
                
                require_once '/var/www/html/Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_dump.php';
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_tree.php';
                
                $classLoader = new SplClassLoader('Zimbra', realpath('/var/www/html/Class/Lib/')); // Point this to the src folder of the zcs-php repo
                $classLoader->register();

                // Define some constants we're going to use
                define('ZIMBRA_LOGIN', 'admin');
                define('ZIMBRA_PASS',  's2atES#apEr_');
                define('ZIMBRA_SERVER', '10.0.88.11'); //mbx1.abtel.link
                define('ZIMBRA_PORT', '7071');
                
                // Create a new Admin class and authenticate
                $zimbra = new \Zimbra\ZCS\Admin(ZIMBRA_SERVER, ZIMBRA_PORT);
                $zimbra->auth(ZIMBRA_LOGIN, ZIMBRA_PASS);
                
                //Recup de la liste des comptes existant sur le serveur mail
                try{
                        $accList = $zimbra->getAccounts(array(
                                                              'domain'=>'abtel.fr',
                                                              'limit'=> 200,
                                                              'offset'=> 0
                                                              ));
                        $list=array();
                        foreach($accList as $acc){
                                       $list[$acc->get('name')] = $acc->get('id');
                        }
                } catch (Exception $e){
                        print_r ($e);
                }

                foreach($result as $user){
                        //Si aucun mail ou mail n'existant pas sur le serveur on zappe
                        if ($user['Mail'] == '' && $user['Mail'] == null && !array_key_exists($user['Mail'],$list)) continue;
                        
                        print_r($user);
                        continue;
                        try{
                                exit;
                                $account = $zimbra->modifyAccount(array('id'=>$list[$user['Mail']],'zimbraDataSourceCalendarPollingInterval'=>'10m'));
                                $folder = $zimbra->createFolder($user['Mail'],array(
                                        'name'=>'/Gestion_'.$user['User'], //Nom du dossier
                                        'view'=>'appointment',  //view ou apparait le dossier
                                        'url'=>'http://www.abtel.fr/Modules/AbtelTools/Calendar/'.$user['User'].'.ics', //Url pour synchro remote
                                        'rgb'=> '#ff99ff', //Couleur du dossier / calendar
                                        'f'=>'#i', //Flag pour qu'il soit séléctionné et apparaisse sur le calendrier utilisateur par defaut
                                        'fie' => 1 //Evite les ereur si il existe déja
                                ));
                        } catch (Exception $e){
                                print_r ($e);
                        }
                }             
        }
        
        
        //Get les actions de la gestion pour les retourner a l'appli smartphone d'abtel (en devenir)
        public function getActions($offset,$limit,$util = 'GC'){
                require_once '/var/www/html/Class/Lib/rtf2html.php';
                
                $query = 'SELECT COUNT(*)  FROM actions
                        WHERE ACUTIL = "'.$util.'"';
                try{
                        $q = $this->dbAbtel->query($query);
                }catch (Exception $e){
                        $this->error .= print_r($e,true);
                }
                if ($q)
                        $result = $q->fetchALL ( PDO::FETCH_ASSOC );
                else {
                        $result = Array();
                        $this->error .= print_r($this->dbAbtel->errorinfo(),true);
                }
                $total = $result;
                
                $query = 'SELECT id, ACDATECREATION as DateCrea, ACHEUREDEB as HeureDebut, ACHEUREFIN as HeureFin, ACTITRE as Titre, ACCLIENT as Client, ACNOTE as Description  FROM actions
                        WHERE ACUTIL = "'.$util.'" ORDER BY ACDATECREATION DESC LIMIT '.$offset.','.$limit.'';
                try{
                        $q = $this->dbAbtel->query($query);
                }catch (Exception $e){
                        $this->error .= print_r($e,true);
                }
                if ($q)
                        $result = $q->fetchALL ( PDO::FETCH_ASSOC );
                else {
                        $result = Array();
                        $this->error .= print_r($this->dbAbtel->errorinfo(),true);
                }
                
                
                array_walk($result,function(&$r){
                        if($r['Description'] !='' && $r['Description']!=null && strpos($r['Description'],'{\rtf1\ansi') !== false ){
                                $reader = new RtfReader();
                                $reader->Parse($r['Description']);
                                $formatter = new RtfHtml();
                                $desc=$formatter->Format($reader->root);
                                $r['Description'] = strip_tags($desc);
                        }
                });
                                
                
                $json = array('data'=>$result,'total'=>$total['COUNT(*)']);
                return json_encode($json);
        }*/
        
        //Recupère les comptes protégés par le MIB Abtel
        public function getMibProtected(){
                $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';
                
                //Première étape : Recupérer un sessid Valide :
                /*
                 *      ETAPE 1
                 *
                 */
                //open connection
                $ch = curl_init();
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, 'https://mib.abtel.fr/config/administration.mib');                
                
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                //curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                curl_setopt($ch, CURLOPT_SSLVERSION,3);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                
                //execute post
                $result = curl_exec($ch);
                
                $info  = curl_getinfo( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                curl_close( $ch );
                
                $header_content = substr($result, 0, $info['header_size']);
                $body_content = trim(str_replace($header_content, '', $result));
                $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m"; 
                preg_match_all($pattern, $header_content, $matches); 
                $cookiesOut = implode("; ", $matches['cookie']);
                
                $peteMatches = array();
                $pete = preg_match('/id="javax.faces.ViewState" value="(.*)"/',$result,$peteMatches);
                $peteUrl = urlencode($peteMatches[1]);
                
                //echo '<pre>';
                //echo 'Info: '.PHP_EOL;
                //print_r($info);
                //echo PHP_EOL.'Err: '.PHP_EOL;
                //print_r($errmsg);
                //klog::l('$peteMatches',$peteMatches);
                //echo PHP_EOL.'Cookies: '.PHP_EOL;
                //print_r($cookiesOut);
                //echo PHP_EOL.'Body: '.PHP_EOL;
                //print_r($body_content);
                //echo '</pre>';
                
                
                //Seconde étape : Se connecter
                /*
                 *      ETAPE 2
                 *
                 */
                //Chaine à envoyer en POST pour simuler une connexion admin abtel
                $password=urlencode('NES3_fRaPr?3');
                $fields_string = 'loginform%3AloginAdmin=admin&loginform%3Apassword='.$password.'&loginform%3AavailableLang=fr&loginform=loginform&autoScroll=&loginform%3A_link_hidden_=&loginform%3A_idcl=loginform%3Avalidate&jsf_sequence=1&javax.faces.ViewState='.$peteUrl;
                $len =strlen($fields_string);
                //open connection
                $ch = curl_init();
                
                $heads = array(
                        'POST /config/login.mib HTTP/1.1',
                        'Host: mib.abtel.fr',
                        'Connection: keep-alive',
                        'Content-Length: '.$len,
                        'Cache-Control: max-age=0',
                        'Origin: https://mib.abtel.fr',
                        'Upgrade-Insecure-Requests: 1',
                        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Referer: https://mib.abtel.fr/config/login.mib',
                        'Accept-Encoding: gzip, deflate, br',
                        'Accept-Language: fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Cookie: '.$cookiesOut,
                        'Expect:'
                );
                
                curl_setopt($ch,CURLOPT_HTTPHEADER,$heads);
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, 'https://mib.abtel.fr/config/login.mib');
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                
                
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                //curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                curl_setopt($ch, CURLOPT_SSLVERSION,3);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                
                //execute post
                $result = curl_exec($ch);
                
                $info  = curl_getinfo( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                curl_close( $ch );
                
                $header_content = substr($result, 0, $info['header_size']);
                $body_content = trim(str_replace($header_content, '', $result));
                
                $peteMatches = array();
                $pete = preg_match('/id="javax.faces.ViewState" value="(.*)"/',$result,$peteMatches);
                $peteUrl = urlencode($peteMatches[1]);

                //echo '<pre>';
                //echo 'Info: '.PHP_EOL;
                //print_r($info);
                //echo PHP_EOL.'Err: '.PHP_EOL;
                //print_r($errmsg);
                //klog::l('$peteMatches',$peteMatches);
                //echo PHP_EOL.'Cookies: '.PHP_EOL;
                //print_r($cookiesOut);
                //echo PHP_EOL.'Body: '.PHP_EOL;
                //print_r($body_content);
                //echo '</pre>';

                //Troisieme étape : Aller sur la liste des users
                /*
                 *      ETAPE 3
                 *
                 */
                //Chaine à envoyer en POST pour simuler une connexion admin abtel
                $fields_string = 'navigationMenu=navigationMenu&autoScroll=0%2C0&javax.faces.ViewState='.$peteUrl.'&UI_COMPONENT_NAME=usersData&navigationMenu%3A_idcl=navigationMenu%3Anav1%3Auser';

                //open connection
                $ch = curl_init();
               
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, 'https://mib.abtel.fr/config/administration.mib');
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                
                 
                $heads = array(
                        'POST /config/users.mib HTTP/1.1',
                        'Host: mib.abtel.fr',
                        'Connection: keep-alive',
                        'Content-Length: '.strlen($fields_string),
                        'Cache-Control: max-age=0',
                        'Origin: https://mib.abtel.fr',
                        'Upgrade-Insecure-Requests: 1',
                        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Referer: https://mib.abtel.fr/config/login.mib',
                        'Accept-Encoding: gzip, deflate, br',
                        'Accept-Language: fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Cookie: '.$cookiesOut
                );
                
                curl_setopt($ch,CURLOPT_HTTPHEADER,$heads);
                
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                //curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                curl_setopt($ch, CURLOPT_SSLVERSION,3);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                
                //execute post
                $result = curl_exec($ch);
                
                $info  = curl_getinfo( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                curl_close( $ch );
                
                $header_content = substr($result, 0, $info['header_size']);
                $body_content = trim(str_replace($header_content, '', $result));
                
                $peteMatches = array();
                $pete = preg_match('/id="javax.faces.ViewState" value="(.*)"/',$result,$peteMatches);
                $peteUrl = urlencode($peteMatches[1]);
                
                //echo '<pre>';
                //echo 'Info: '.PHP_EOL;
                //print_r($info);
                //echo PHP_EOL.'Err: '.PHP_EOL;
                //print_r($errmsg);
                //klog::l('$peteMatches',$peteMatches);
                //echo PHP_EOL.'Cookies: '.PHP_EOL;
                //print_r($cookiesOut);
                //echo PHP_EOL.'Body: '.PHP_EOL;
                //print_r($body_content);
                //echo '</pre>';

                //Quatrième étape : effectuer la recherche
                /*
                 *      ETAPE 4
                 *
                 */
                //Chaine à envoyer en POST pour simuler une connexion admin abtel
                $fields_string = 'login=&groupForSearch=&domainForSearch=&userListId%3Afirstname=&userListId%3AuserOrigin=user_origin_all&userListId%3Alastname=&userListId%3AuserStatus=user_status_protected&userListId%3AuserSearchCriteriaGroupId=&userListId%3AuserSearchCriteriaDomainId=&userListId%3AisAdvancedSearchPanelOpen=false&autoScroll=&gotoField=&userListId=userListId&autoScroll=&userListId%3A_link_hidden_=&userListId%3A_idcl=userListId%3AsearchAction&jsf_sequence=1&javax.faces.ViewState='.$peteUrl;

                //open connection
                $ch = curl_init();
               
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, 'https://mib.abtel.fr/config/users.mib');
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                
                 
                $heads = array(
                        'POST /config/users.mib HTTP/1.1',
                        'Host: mib.abtel.fr',
                        'Connection: keep-alive',
                        'Content-Length: '.strlen($fields_string),
                        'Cache-Control: max-age=0',
                        'Origin: https://mib.abtel.fr',
                        'Upgrade-Insecure-Requests: 1',
                        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Referer: https://mib.abtel.fr/config/administration.mib',
                        'Accept-Encoding: gzip, deflate, br',
                        'Accept-Language: fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Cookie: '.$cookiesOut
                );
                
                curl_setopt($ch,CURLOPT_HTTPHEADER,$heads);
                
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                //curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                curl_setopt($ch, CURLOPT_SSLVERSION,3);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                
                //execute post
                $result = curl_exec($ch);
                
                $info  = curl_getinfo( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                curl_close( $ch );
                
                $header_content = substr($result, 0, $info['header_size']);
                $body_content = trim(str_replace($header_content, '', $result));
                
                $peteMatches = array();
                $pete = preg_match('/id="javax.faces.ViewState" value="(.*)"/',$result,$peteMatches);
                $peteUrl = urlencode($peteMatches[1]);
                 //echo '<pre>';
                //print_r($info);
                //print_r($body_content);
                //klog::l('$peteMatches',$peteMatches);
                //print_r($cookiesOut);
                //echo '</pre>';
                
                //Cinquieme étape : recupérer les données
                /*
                 *      ETAPE 5
                 *
                 */
                //Chaine à envoyer en POST pour simuler une connexion admin abtel
                $fields_string = 'login=&groupForSearch=&domainForSearch=&userListId%3Afirstname=&userListId%3AuserOrigin=user_origin_all&userListId%3Alastname=&userListId%3AuserStatus=user_status_protected&userListId%3AuserSearchCriteriaGroupId=&userListId%3AuserSearchCriteriaDomainId=&userListId%3AisAdvancedSearchPanelOpen=false&autoScroll=&gotoField=&userListId=userListId&autoScroll=&userListId%3A_link_hidden_=&jsf_sequence=1&javax.faces.ViewState='.$peteUrl.'&autoScroll=0%2C363&ENTITY_NAME=User&SPOOL_TYPE=0&action=export&userListId%3A_idcl=userListId%3AexportButtonLink';

                //open connection
                $ch = curl_init();
               
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, 'https://mib.abtel.fr/config/users.mib');
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                
                 
                $heads = array(
                        'POST /config/users.mib HTTP/1.1',
                        'Host: mib.abtel.fr',
                        'Connection: keep-alive',
                        'Content-Length: '.strlen($fields_string),
                        'Cache-Control: max-age=0',
                        'Origin: https://mib.abtel.fr',
                        'Upgrade-Insecure-Requests: 1',
                        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Referer: https://mib.abtel.fr/config/users.mib',
                        'Accept-Encoding: gzip, deflate, br',
                        'Accept-Language: fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Cookie: '.$cookiesOut
                );
                
                curl_setopt($ch,CURLOPT_HTTPHEADER,$heads);
                
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                //curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                curl_setopt($ch, CURLOPT_SSLVERSION,3);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                
                //execute post
                $result = curl_exec($ch);
                
                $info  = curl_getinfo( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                curl_close( $ch );
                
                $header_content = substr($result, 0, $info['header_size']);
                $body_content = trim(str_replace($header_content, '', $result));
                
               
                //echo '<pre>';
                //print_r($info);
                //print_r($peteUrl);
                //print_r($body_content);
                //klog::l('$errmsg',$errmsg);
                //klog::l('$body_content',$body_content);
                ////print_r($cookiesOut);
                //echo '</pre>';
                $lines = $body_content;
                $mails = explode("\n",$lines);
                //print_r($mails);
                $mailArray = array_map(function($l){return str_getcsv($l,';',"\r");},$mails);
                
                $ordered = array();
                foreach($mailArray as $mail){
                        //Supprime la dernière entrée tojours vide
                        array_pop($mail);
                        $table = array(
                                'mib_prenom'=>$mail[0],
                                'mib_nom'=>$mail[1],
                                'mib_alias&listes'=>array_slice($mail,4),
                                'mib_domaine'=>substr($mail[2], strpos($mail[2], "@") + 1)
                        );
                        $ordered[$mail[2]] = $table;
                }
                //print_r($ordered);
                return  $ordered;
        }
        
        
        public function getMailBSP($domainSearch = null) {
                require_once '/var/www/html/Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_dump.php';
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_tree.php';
                
                $classLoader = new SplClassLoader('Zimbra', realpath('/var/www/html/Class/Lib/')); // Point this to the src folder of the zcs-php repo
                $classLoader->register();
                
                
                $servers = array(array('10.0.88.11','hUTHach?p26B'));

                // Define some constants we're going to use
                define('ZIMBRA_PORT', '7071');
                
                //$batchSize = 10;  
                
                foreach($servers as $server){
                        //TODO Verif que les comptes admin sont bien actifs avant !!!
                        
                        
                        // Create a new Admin class and authenticate
                        $zimbra = new \Zimbra\ZCS\Admin($server[0], ZIMBRA_PORT);
                        $zimbra->auth('zmapi@abtel.link', $server[1]);
                        
                        try{
                                
                                $cosesTemp = $zimbra->getAllCos();
                                $coses = array();
                                foreach ($cosesTemp as $cosTemp){
                                        $coses[$cosTemp->get('id')]=$cosTemp;
                                }
                                
                                $accountCount = $zimbra->countObjects('account');
                                //$batchCount = ceil($accountCount/$batchSize);
                                $quotas = array();
                                //for($i=0; $i<$batchCount; $i++){
                                        //$quotasTemp = $zimbra->getQuotas(array('limit'=>$batchSize,'offset'=>$batchSize*$i,'allServers'=>true));
                                
                                
                                $domaines = $zimbra->getDomains();
                                $dNames = array();
                                foreach($domaines as $domain){
                                        $domainName = $domain->get('name');
                                        $dNames[] = $domainName;
                                        
                                        $quotasTemp = $zimbra->getQuotas(array('domain'=>$domainName,'allServers'=>true));
                                        $quotas = array_merge($quotas,$quotasTemp);
                                }
                                
                                //echo '<pre>';
                                //print_r($coses);
                                //echo '</pre>';
                                //exit;
                                
                                $allAccounts = array();
                                $accList = $zimbra->getAllAccounts($domainSearch);
                                
                                foreach($accList as $account){
                                        //echo '<pre>';
                                        //print_r($account);
                                        //echo '</pre>';
                                        //exit;
                                        $cos = null;
                                        
                                        $accId = $account->get('id');
                                        $accName = $account->get('name');
                                        $userNom = $account->get('sn');
                                        $userPrenom = $account->get('givenName');
                                        //print_r($accId.' : '.$accName.'<br>');
                                        //print_r($quotas[$accId]['limit'].' / '.$quotas[$accId]['used'] .'<br>');
                                        $userQuota = $quotas[$accId]['limit'];
                                        $userUsed = $quotas[$accId]['used'];
                                        $accStatus = $account->get('zimbraMailStatus');
                                        $cosId = $account->get('zimbraCOSId');
                                        if(isset($cosId) && $cosId != '')
                                            $cos = $coses[$cosId];

                                        //echo '<pre>';
                                        //print_r($account);
                                        //echo '</pre>';
                                        //exit;
                                        $cosName = 'Aucune COS !!!';
                                        $cosNotes = '';
                                        if(is_object($cos)){
                                             $cosName =   $cos->get('name');
                                             $cosNotes =   $cos->get('zimbraNotes');
                                        }

                                        $dname = substr($accName,strpos($accName,'@')+1);
                                        
                                        $allAccounts[$accName] = array(
                                                'adresse'=>$accName,
                                                'zimbra_id'=>$accId,
                                                'zimbra_nom'=>$userNom,
                                                'zimbra_prenom'=>$userPrenom,
                                                'zimbra_quota'=>floor($userQuota/1048576),
                                                'zimbra_utilise'=>floor($userUsed/1048576),
                                                'zimbra_status'=>$accStatus,
                                                'zimbra_domaine'=>$dname,
                                                'zimbra_cosnotes'=>$cosNotes,
                                                'zimbra_cos'=> $cosName,
                                                'zimbra_cosId'=> $cosId
                                        );
                                }
                                
                                $allAccounts[] = $dNames;
                                
                                return $allAccounts;
                        } catch (Exception $e){
                                echo '<pre>';
                                print_r ($e);
                                echo '</pre>';
                                return false;
                        }
                        
                }
                
        }
        
        
      
        //Recupere les infos sur les mails (depuis zimbra et mib) et les formate en un tout coherent
        public function getMailsDiag($search = null, $byDomain = null){
                $protecteds = $this->getMibProtected();
                //klog::l('$protecteds',$protecteds);
                //echo '<pre>';
                //print_r($protecteds);
                //echo '</pre>';
                $bsp = $this->getMailBSP($byDomain);
                $domaines = array_pop($bsp);
                
                foreach($protecteds as $mibAdr=>$mibVals){
                        
                        if(array_key_exists($mibAdr,$bsp)){
                                $bsp[$mibAdr]=array_merge($bsp[$mibAdr],$mibVals);
                        }
                }
                //
                //echo '<pre>';
                //print_r($bsp);
                //echo '</pre>';
                
                //Gestion de la recherche
                if($search){
                        $searchTerms = explode(';',$search);
                        $sRes = array();
                        foreach($searchTerms as $term){
                                $term = trim($term);
                                $temp = array_filter($bsp, function($mail) use($term){
                                        if(strpos($mail['adresse'],$term) !== false){
                                                return true;
                                        }
                                        if(strpos($mail['zimbra_cos'],$term) !== false){
                                                return true;
                                        }
                                        return false;
                                });
                                $sRes = array_merge($sRes,$temp);
                        }
                        $bsp = $sRes;
                }
                
                
                //Traitement pour les stats
                $coses = array();
                $protectCount = 0;
                
                foreach($bsp as $mail){
                     if(isset($coses[$mail['zimbra_cos']])){
                                $coses[$mail['zimbra_cos']]['count']++;
                     } else {
                                $coses[$mail['zimbra_cos']]['count']=1;
                                $coses[$mail['zimbra_cos']]['note']=$mail['zimbra_cosnotes'];
                     }
                     
                     if(isset($mail['mib_domaine'])){
                                $protectCount++;
                     }
                }
                //Tri selon le nom des cos pour lisibilité
                ksort($coses);
                
                $ret = array(
                        'mails'=>$bsp,
                        'domains'=>$domaines,
                        'coses'=>$coses,
                        'protected'=>$protectCount
                );
                return $ret;
        }
        
        
        public function testSignature($mail){
                
                require_once '/var/www/html/Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_dump.php';
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_tree.php';
                
                $classLoader = new SplClassLoader('Zimbra', realpath('/var/www/html/Class/Lib/')); // Point this to the src folder of the zcs-php repo
                $classLoader->register();
                
                
                $servers = array(array('10.0.88.11','TRbi9b3UD34'));

                // Define some constants we're going to use
                define('ZIMBRA_PORT', '7071');
                
                //$batchSize = 10;  
                
                foreach($servers as $server){
                        //TODO Verif que les comptes admin sont bien actifs avant !!!
                        
                        
                        // Create a new Admin class and authenticate
                        $zimbra = new \Zimbra\ZCS\Admin($server[0], ZIMBRA_PORT);
                        $token = $zimbra->auth('admin', $server[1]);
                        $mailToken = $zimbra->delegateAuth($mail);
                        
                        try{
                                //$sigName ='Abtel_auto_Test';
                                //$alrEx = false;
                                //
                                //$sigs = $zimbra->getSignatures($mail);
                                //foreach($sigs as $sig){
                                //        if($sig->get('name') == $sigName) $alrEx = true;
                                //}
                                ////Peut être mergé dnas le if ce dessus mais séparée pour lecture facilitée / permettre modif
                                //if( $alrEx ){
                                //        $zimbra->delSignature($mail,array(
                                //                                        'signature'=>array(
                                //                                                'name'=>$sigName,
                                //                                        )
                                //                                ));
                                //}
                                //
                                //$sigRes = $zimbra->addSignature($mail,array(
                                //                                                'signature'=>array(
                                //                                                        'name'=>$sigName,
                                //                                                        '_'=>array('content'=>array(
                                //                                                                            '_'=>'<b>Cordialement</b>'
                                //                                                                            ))
                                //                                                )
                                //                                        ));
                                //print_r($sigRes);
                                
                                
                                //$imgName = 'Logo_Abtel_Web';
                                //$sigImgType = 'jpg';
                                //$tempImg = sys_get_temp_dir().'/'.$imgName.'.'.$sigImgType;
                                //$img = '/var/www/html/Logo_Abtel_Web.png';
                                //
                                //if(is_file($img)){
                                //        $img;
                                //        
                                //        $ch = curl_init();
                                //        $data = array('filename' => $imgName, 'file' => '@'.$img, 'requestId'=>$mail);
                                //        curl_setopt($ch, CURLOPT_URL, "https://$server[0]:".ZIMBRA_PORT."/service/upload?admin=1&fmt=raw"); //,extended
                                //
                                //        curl_setopt($ch, CURLOPT_POST, 1);
                                //        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: ZM_ADMIN_AUTH_TOKEN=".$mailToken));
                                //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                                //        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                                //        //CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                                //        //So next line is required as of php >= 5.6.0
                                //        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                                //        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                                //        $res = curl_exec($ch);
                                //        $err = curl_error($ch);
                                //        //$info = curl_getinfo($ch);
                                //        
                                //        curl_close($ch);
                                //        
                                //        //echo '<pre>';
                                //        //print_r($info);
                                //        //print_r(PHP_EOL);
                                //        //print_r($res);
                                //        //print_r(PHP_EOL);
                                //        //print_r($err);
                                //        //echo '</pre>';
                                //        
                                //        $docId = explode(',',$res);
                                //        $docId = $docId[2];
                                //        $docId = str_replace("'",'',$docId);
                                //
                                //        $sRes = $zimbra->search($mail,$imgName,'document');
                                //
                                //        
                                //        if( $sRes[0]->count() ){
                                //                $old['ver'] = (int) $sRes->children()->doc->attributes()->ver;
                                //                $old['id'] =  (int) $sRes->children()->doc->attributes()->id;
                                //                $old['desc'] = 'Timestamp : '.time();
                                //                
                                //                $zimbra->saveDocument($mail,trim($docId),$old);
                                //        } else{
                                //                $zimbra->saveDocument($mail,trim($docId));
                                //        }
                                //}
                                
                               $zimbra->modifyPrefs($mail,array('name'=>'zimbraPrefSkin','value'=>'carbon'));
                                
                        } catch (Exception $e){
                                echo '<pre>';
                                print_r ($e);
                                echo '</pre>';
                                return false;
                        }
                        
                }
        }
}