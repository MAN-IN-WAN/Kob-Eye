<?php

class Host extends genericClass
{
    var $_isVerified = false;
    var $_KEServer = false;
    var $_KEClient = false;

    /**
     * Force la vérification avant enregistrement
     * @param    boolean    Enregistrer aussi sur LDAP
     * @return    void
     */
    public function Save($synchro = true)
    {
        parent::Save();
        // Forcer la vérification
        if (!$this->_isVerified) $this->Verify($synchro);
        // Enregistrement si pas d'erreur + Récupération GID CLIENT
        if ($this->_isVerified) {
            //$this->getGidFromClient($synchro);
            parent::Save();
        }
        // Maj Quotas niveau serveur
        /*if ($this->Id) {
            $T1 = Sys::$Modules["Parc"]->callData("Parc/Server/Host/{$this->Id}", "", 0, 1);
            if (!empty($T1)) {
                $Server = genericClass::createInstance('Parc', $T1[0]);
                $Server->EspaceProvisionne = 0;
                $Tab = Sys::$Modules["Parc"]->callData("Parc/Server/{$Server->Id}/Host", "", 0, 1000);
                if (!empty($Tab)) foreach ($Tab as $H) $Server->EspaceProvisionne += $H["Quota"];
                $Server->Save();
            }
        }*/
        return true;
    }
    /**
     * getLdapID
     * récupère le ldapId d'une entrée pour un serveur spécifique
     */
    public function getLdapID($KEServer) {
        if (!empty($this->LdapID)) {
            $en = json_decode($this->LdapID, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapID);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapID
     * défniit le ldapId d'une entrée pour un serveur spécifique
     */
    public function setLdapID($KEServer,$ldapId) {
        if (!empty($this->LdapID)) {
            $en = json_decode($this->LdapID, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapID);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapId;
        $this->LdapID = json_encode($en);
    }
    /**
     * getLdapDN
     * récupère le ldapDN d'une entrée pour un serveur spécifique
     */
    public function getLdapDN($KEServer) {
        if (!empty($this->LdapDN)) {
            $en = json_decode($this->LdapDN, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapDN);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapDN
     * définit le ldapDN d'une entrée pour un serveur spécifique
     */
    public function setLdapDN($KEServer,$ldapDn) {
        if (!empty($this->LdapDN)) {
            $en = json_decode($this->LdapDN, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapDN);
        } else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapDn;
        $this->LdapDN = json_encode($en);
    }
    /**
     * getLdapTms
     * récupère le ldapTms d'une entrée pour un serveur spécifique
     */
    public function getLdapTms($KEServer) {
        if (!empty($this->LdapTms)) {
            $en = json_decode($this->LdapTms, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapTms);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapTms
     * définit le ldapTms d'une entrée pour un serveur spécifique
     */
    public function setLdapTms($KEServer,$ldapTms) {
        if (!empty($this->LdapTms)) {
            $en = json_decode($this->LdapTms, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapTms);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapTms;
        $this->LdapTms = json_encode($en);
    }
    /**
     * getLdapUid
     * récupère le ldapUid d'une entrée pour un serveur spécifique
     */
    public function getLdapUid($KEServer)
    {

        if (!empty($this->LdapUid)){
            $en = json_decode($this->LdapUid, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapUid);
        }else $en=array();
        if (!isset($en[$KEServer->Id])){
            $en[$KEServer->Id] = Server::getNextUid();
            $this->setLdapUid($KEServer,$en[$KEServer->Id]);
        }
        return $en[$KEServer->Id];
    }
    /**
     * setLdapUid
     * définit le ldapUid d'une entrée pour un serveur spécifique
     */
    public function setLdapUid($KEServer,$ldapUid) {
        if (!empty($this->LdapUid)){
            $en = json_decode($this->LdapUid, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapUid);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapUid;
        $this->LdapUid = json_encode($en);
    }

    /**
     * Verification des erreurs possibles
     * @param    boolean    Verifie aussi sur LDAP
     * @return    Verification OK ou NON
     */
    public function Verify($synchro = true)
    {
        //test du nom
        if (!preg_match('#[a-z0-9]+#',$this->Nom)){
            $this->addError(array("Prop"=>"Nom","Message"=>"Le nom ne peut contenir de caractères accentués, de majuscule, d'espaces ou de ponctuations."));
            return false;
        }
        if (strlen($this->Nom)>25||strlen($this->Nom)<2){
            $this->addError(array("Prop"=>"Nom","Message"=>"Le nom doit comporter de 2 à 25 caractères"));
            return false;
        }
        if (parent::Verify()) {
            //Verification du client
            if (!$this->getKEClient()) return true;
            //Verification des server
            if (!$this->getKEServer()){
                return true;
            }

            $this->_isVerified = true;

            if ($synchro) {

                // On boucle sur tous les serveurs
                $KEServers = $this->getKEServer();
                foreach ($KEServers as $KEServer) {
                    $dn = 'cn=' . $this->Nom . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE;
                    // Verification à jour
                    $res = Server::checkTms($this,$KEServer);
                    if ($res['exists']) {
                        if (!$res['OK']) {
                            $this->AddError($res);
                            $this->_isVerified = false;
                        } else {
                            // Déplacement
                            if ($this->getLdapDN($KEServer) != 'cn=' . $this->Nom . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE) $res = Server::ldapRename($this->getLdapDN($KEServer), 'cn=' . $this->Nom, 'ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE);
                            else $res = array('OK' => true);
                            if ($res['OK']) {
                                // Modification
                                $entry = $this->buildEntry($KEServer,false);
                                $res = Server::ldapModify($this->getLdapID($KEServer), $entry);
                                if ($res['OK']) {
                                    // Tout s'est passé correctement
                                    $this->setLdapDN($KEServer,$dn);
                                    $this->setLdapTms($KEServer,$res['LdapTms']);
                                } else {
                                    // Erreur
                                    $this->AddError($res);
                                    $this->_isVerified = false;
                                    // Rollback du déplacement
                                    $tab = explode(',', $this->getLdapDN($KEServer));
                                    $leaf = array_shift($tab);
                                    $rest = implode(',', $tab);
                                    Server::ldapRename($dn, $leaf, $rest);
                                }
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        }

                    } else {
                        ////////// Nouvel élément
                        if ($KEServer) {
                            $entry = $this->buildEntry($KEServer);
                            $res = Server::ldapAdd($dn, $entry);
                            if ($res['OK']) {
                                $res2 = Server::ldapAdd('ou=users,' . $dn, array('objectclass' => array('organizationalUnit', 'top'), 'ou' => 'users'));
                                $this->setLdapDN($KEServer,$dn);
                                $this->setLdapUid($KEServer,$entry['uidnumber']);
                                $this->LdapGid = $entry['gidnumber'];
                                $this->setLdapID($KEServer,$res['LdapID']);
                                $this->setLdapTms($KEServer,$res2['LdapTms']);
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        } else {
                            $this->AddError(array('Message' => "Un hébergement doit obligatoirement être créé dans un serveur donné.", 'Prop' => ''));
                            $this->_isVerified = false;
                        }
                    }
                }
            }

        } else {

            $this->_isVerified = false;

        }

        return $this->_isVerified;

    }

    /**
     * Configuration d'une nouvelle entrée type
     * Utilisé lors du test dans Verify
     * puis lors du vrai ajout dans Save
     * @param    boolean        Si FALSE c'est simplement une mise à jour
     * @return    Array
     */
    private function buildEntry($KEServer,$new = true)
    {
        $entry = array();
        $entry['cn'] = $this->Nom;
        $entry['givenname'] = $this->Nom;
        $entry['homedirectory'] = '/home/' . $this->Nom;
        $entry['sn'] = $this->Nom;
        $entry['uid'] = $this->Nom;
        $entry['description'] = json_encode(array("Quota" => $this->Quota));
        $entry['preferredLanguage'] = $this->PHPVersion;
        if ($new) {
            $entry['uidnumber'] = $this->getLdapUid($KEServer);
            $entry['gidnumber'] = "100";//$this->_KEClient->LdapGid;
            $entry['loginshell'] = '/bin/bash';
            $entry['objectclass'][0] = 'inetOrgPerson';
            $entry['objectclass'][1] = 'posixAccount';
            $entry['objectclass'][2] = 'top';
            $entry['userpassword'] = "{MD5}".base64_encode(pack("H*",md5($this->Password)));
        }
        return $entry;
    }

    /**
     * Récupère le Gid du Client Parent s'il existe
     * @param    boolean        Synchroniser aussi sur LDAP
     * @return    void
     */
    private function getGidFromClient($synchro = true)
    {
        $tab = $this->getParents('Client');
        if (!empty($tab)) {
            $this->LdapGid = $tab[0]->LdapGid;
            if ($synchro) {
                $entry = array('gidnumber' => $this->LdapGid);
                $KEServer = $this->getKEServer();
                Server::ldapModify($this->LdapID, $entry);
            }
        }
    }


    /**
     * Suppression de la BDD
     * Relai de cette suppression à LDAP
     * On utilise aussi la fonction de la superclasse
     * @return    void
     */
    public function Delete(){
        //suppression des apaches
        $aps = $this->getChildren('Apache');
        foreach ($aps as $ap) $ap->Delete();
        //suppression des ftp
        $ftps = $this->getChildren('Ftpuser');
        foreach ($ftps as $ftp) $ftp->Delete();
        //suppression des apaches
        $bdds = $this->getChildren('Bdd');
        foreach ($bdds as $bdd) $bdd->Delete();
        //suppression ldap
        $KEServers = $this->getKEServer();
        foreach ($KEServers as $KEServer) {
            try {
                if (!empty($this->Nom))
                    $KEServer->remoteExec('rm /home/' . $this->Nom . ' -Rf');
            } catch (Exception $e) {
                $this->addError(Array("Message" => "Impossible d'effectuer la commande de suppression sur le serveur"));
                return false;
            }
            //suppression définitive
            if ($this->getLdapID($KEServer)) Server::ldapDelete($this->getLdapID($KEServer), true);
        }

        parent::Delete();
    }


    /**
     * Récupère une référence vers l'objet KE "Server"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le serveur
     * pour le cas d'une utilisation ultérieure
     * @return    L'objet Kob-Eye
     */
    public function getKEServer()
    {
        if (!is_array($this->_KEServer)) {
            $tab = $this->getParents('Server');
            if (empty($tab)) return false;
            else $this->_KEServer = $tab;
        }
        return $this->_KEServer;
    }

    /**
     * Récupère une référence vers l'objet KE "Client"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le client
     * pour le cas d'une utilisation ultérieure
     * @return    L'objet Kob-Eye
     */
    public function getKEClient()
    {
        if (!is_object($this->_KEClient)) {
            $tab = $this->getParents('Client');
            if (empty($tab)) return false;
            else $this->_KEClient = $tab[0];
        }
        return $this->_KEClient;
    }

    /**
     * Retrouve les parents lors d'une synchronisation
     * @return    void
     */
    public function findParents()
    {
        $Parts = explode(',', $this->LdapDN);
        foreach ($Parts as $i => $P) $Parts[$i] = explode('=', $P);
        // Parent Client
        $Tab = Sys::$Modules["Parc"]->callData("Parc/Client/NomLDAP=" . $Parts[0][1], "", 0, 1);
        if (!empty($Tab)) {
            $obj = genericClass::createInstance('Parc', $Tab[0]);
            $this->AddParent($obj);
        }
        // Parent Server
        $Tab = Sys::$Modules["Parc"]->callData("Parc/Server/LDAPNom=" . $Parts[1][1], "", 0, 1);
        if (!empty($Tab)) {
            $obj = genericClass::createInstance('Parc', $Tab[0]);
            $this->AddParent($obj);
        }
    }

    public function Terminal()
    {
        /**
         * The command comes from ajax-post.
         */
        if(isset($_POST['stdin'])){
            Terminal::postCommand($_POST['stdin']);
            exit;
        }

        /**
         * The authentication.
         * You can change this method.
         * I've used Auth Basic as example.
         */
        /*if (!isset($_SERVER['PHP_AUTH_USER']) ||
            !Terminal::autenticate($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
            header('WWW-Authenticate: Basic realm="xwiterm (use your linux login)"');
            header('HTTP/1.0 401 Unauthorized');
            echo "Authentication failure :)";
            exit;
        }*/

//        return Terminal::run($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        return Terminal::run('enguer', '21wyisey');
    }
}
/**
 * Terminal
 * Class to interact with the user and the shell process.
 * @author Thiago Bocchile <tykoth@gmail.com>
 * @package xwiterm
 * @subpackage xwiterm-linux
 */

class Terminal {


    private $login;
    private $password;

    static $username;
    static $commandsFile = "/tmp/comandos.txt";
    static $totalCommands;
    static $process;
    static $status;
    static $meta;

    static $instance;

    /**
     * Static function to simply autenticate users.
     * Can be used for other purposes.
     * @param string $login the linux username
     * @param string $password password
     * @access public
     * @return bool - true if login and password is right
     */
    public static function autenticate($login, $password) {


        self::$username = $login;
        $process = new TerminalProcess("su " . escapeshellarg($login));
        usleep(500000);
        $process->put($password);
        $process->put(chr(13));
        usleep(500000);
        return (bool) !$process->close();
    }

    /**
     * Get the terminal title.
     * @return string
     */
    public static function getTitle(){
        if(!empty(self::$username)){
            $process = new TerminalProcess("uname -n");
            $title = self::$username."@".trim($process->get());
            $process->close();
            return $title;
        } else {
            return "not logged shell - how cold it be possible, uh?";
        }
    }
    /**
     * Static function to "run" the terminal.
     * It must be used at the end of all html output.
     * @param string $login
     * @param string $password
     * @return bool - true if runs
     */
    public static function run($login, $password){
        self::$instance = new self();
        return self::$instance->open($login, $password);
    }

    /**
     * Simple function to "post" a command in the commands file.
     * @todo Another way to catch the commands, file is ugly.
     * @param string $command
     */
    public static function postCommand($command){
        file_put_contents(self::$commandsFile, $command."\n", FILE_APPEND);
    }

    /**
     * Open the terminal process.
     * @param string $login
     * @param string $password
     * @return bool - true if runs
     */
    private function open($login, $password) {
        $this->login = $login;
        $this->password = $password;

        if(!is_writable(self::$commandsFile)){
            $this->output("\r\nNeed permission to write in ".self::$commandsFile."\r\n");
            return false;
        }

        // Clean commands
        file_put_contents(self::$commandsFile, "");
        $this->startProcess();

        do {
            $out = self::$process->get();

            // Detect "blocking" (wait for stdin)
            if(sizeof($out) == 1 && ord($out) == 0) {
                $this->listenCommand();
            } else {
                // Provisorio, meldels. (usuario www-data não tem controle de servico, dude!)
                if(preg_match('/-su: no job control in this shell/', $out)) continue;
                $this->output($out);
            }
            usleep(50000);
            self::$status = self::$process->getStatus();
            self::$meta = self::$process->metaData();
        } while(self::$meta['eof'] === false);
        return true;
    }

    /**
     * Starts the terminal process
     * Uses the class Process.
     * @return true if runs
     */
    private function startProcess() {
        $cmd = "sudo -S su - {$this->login}";
        self::$process = new TerminalProcess($cmd);
        //self::$process = new TerminalProcess("whoami");
//        self::$process = new TerminalProcess("vi");
        if(!self::$process->isResource()) {
            throw new Exception("RESOURCE NOT AVAIBLE");
            return false;
        }
        usleep(500000);
        self::$process->getStatus();
        self::$process->put($this->password);
        self::$process->put(chr(13));
        self::$process->get();
        usleep(500000);
        self::$status = self::$process->getStatus();
        self::$meta = self::$process->metaData();
    }

    /**
     * Simulates the terminal colors :)
     * Format the input and returns as html with styles
     * Function to be used with preg_replace.
     * @param string $code
     * @param string $value
     * @return string - the html tag with style
     */
    private function consoleTag($code, $value){
        $attrs = explode(";", $code);

        if(sizeof($attrs) == 2 && intval($attrs[0]) > 10){
            $attrs[2] = $attrs[1];
            $attrs[1] = $attrs[0];

        }

        if(sizeof($attrs) == 2 && intval($attrs[0]) == 0 && intval($attrs[1]) == 0){
            $attrs[0] = 0;
            $attrs[1] = 37;
        }
        $text = array(
            '0' => '',
            '1' => 'font-weight:bold',
            '3' => 'text-decoration:underline',
            '5' => 'blink'
        );
        $colors = array(
            '0' => 'black',
            '1' => 'red',
            '2' => '#89E234', // green
            '3' => 'yellow',
            '4' => '#729FCF', // blue
            '5' => 'magenta',
            '6' => 'cyan',
            '7' => 'white'
        );

        $text_decoration = (isset($attrs[0]) && array_key_exists(intval($attrs[0]), $text)) ? $text[intval($attrs[0])] : $text[0];
        $color = (isset($attrs[1]) && array_key_exists(intval($attrs[1])-30, $colors)) ? $colors[intval($attrs[1])-30] : $colors[0];
        $style = sprintf("%s;color:%s;", $text_decoration, $color);
        $style.= (isset($attrs[2]) && array_key_exists((intval($attrs[2])-40), $colors)) ? "background-color:".$colors[(intval($attrs[2])-40)] : '';
        return "<tt style=\\\"$style\\\">$value</tt>";
    }

    /**
     * "Hard" output.
     * It's not a good practice to echo from class methods, so it's a provisory
     * method.
     * @param string $output
     * @param bool $return - true to return the formatted output
     * @param bool $html - true to format html
     * @return mixed - if $return is true, returns the output
     */
    private function output($output, $return = false, $html = true) {

        if(preg_match('/\x08/',$output)) return false;

        $output = htmlentities($output);
        $output = addslashes($output);

        $output = explode("\n", $output);
        $output = implode("</span><span>", $output);
        $output = sprintf("<span>%s</span>", $output);
        $output = preg_replace( "/\r\n|\r|\n/", '\n', $output);

        // Removes the first occurrence (on ls)
        $output = preg_replace('/\x1B\[0m(\x1B)/', "\x1B", $output);
        // Add colors to default coloring sytem
        $output = preg_replace('/\x1B\[([^m]+)m([^\x1B]+)\x1B\[0m/e', '$this->consoleTag(\'\\1\',\'\\2\')', $output);
        $output = preg_replace('/\x1B\[([^m]+)m([^\x1B]+)\x1B\[m/e', '$this->consoleTag(\'\\1\',\'\\2\')', $output);
        // Add colors to grep color system
        $output = preg_replace('/\x1B\[([^m]+)m\x1B\[K([^\x1B]+)\x1B\[m\x1B\[K/e', '$this->consoleTag("\\1","\\2")', $output);

        // Removes some dumb chars
        $output = preg_replace('/\x1B\[m/', '', $output);
        $output = preg_replace('/\x07/', '', $output);


        if($return === false){
            echo "<script>recebe(\"{$output}\");</script>\n"; flush();
        } else {
            return $output;
        }
    }

    /**
     * Formats the output to be used as command suggest (pressing TAB)
     * @param string $output
     * @return string
     */
    private function commandSuggest($output){
        $output = preg_replace( "/\n|\r|\r\n/", '', $output);
        $output = preg_replace('/'.chr(7).'/', '', $output);
        return trim($output);
    }

    /**
     * Listener for incoming commands
     */
    private function listenCommand() {

        $commands = file(self::$commandsFile);

        if(sizeof($commands) > self::$totalCommands) {
            self::$totalCommands = sizeof($commands);
            $command = $commands[self::$totalCommands-1];
            $this->parse($command);
        }
    }

    /**
     * Parse the command
     * @param string $command - incomming command from terminal
     * @return void
     */
    private function parse($command) {


        switch(trim($command)) {
            case chr(3):
                // SIGTERM
                return $this->sendSigterm();
                break;
            case chr(4):
                self::$process->put("exit");
                self::$process->put(chr(13));
                break;

            case chr(26):
                //STOP - experimental
                return $this->sendSigstop();
                break;
            case 'fg':
                return $this->sendFg();
                break;

            default:
                // Checks for "TAB"
                if(ord(substr($command,-2,1)) == 9){
                    self::$process->put(trim($command).chr(9));
                    usleep(500000);

                    $out = self::$process->get();
                    // Check if is a "RE-TAB"
                    if(trim($command) == $this->commandSuggest($out)){
                        self::$process->put(trim($command).chr(9).chr(9));
                        self::$process->put(chr(21));
                        $this->output(self::$process->get());
                    } else {
                        echo "<script>recebe(null, '".$this->commandSuggest($out)."')</script>\n"; flush();
                    }
                    self::$process->put(chr(21));
                } else {
                    self::$process->put(chr(21));
                    self::$process->put(trim($command));
                    self::$process->put(chr(13));
                }

                usleep(500000);
                break;
        }
    }


    /**
     * Emulates the SIGTERM sending via CTRL-C
     * @return void
     */
    private function sendSigterm() {
        // SLAYER!!! GRRRRRRRRRR
        // http://www.youtube.com/watch?v=VSoh3c7QVyw
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -9 $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        usleep(500000);
    }

    /**
     * Simulates the SIGSTOP sending via CTRL-Z
     * @return void
     */
    private function sendSigstop() {
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -TSTP $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        self::$process->put(chr(13));
        usleep(500000);
    }

    /**
     * Simulates the SIGCONT sending via 'fg'
     */
    private function sendFg() {
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -CONT $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        self::$process->put(chr(13));
        usleep(500000);
    }
}/**
 * Class to control the process.
 * @author Thiago Bocchile <tykoth@gmail.com>
 * @package xwiterm
 * @subpackage xwiterm-linux
 */
class TerminalProcess {
    public $pipes;
    public $process;

    public function __construct($command) {
        return $this->open($command);
    }

    public function __destruct() {
        return $this->close();
    }

    public function open($command) {
        /*        $spec = array(
                    array("pty"), // MAGIC - THE GATHERING!! MWAHAHAHAHA
                    array("pty"),
                    array("pty")
                );*/
        $spec = array(
            array("pipe","r"), // MAGIC - THE GATHERING!! MWAHAHAHAHA
            array("pipe","w"),
            array("pipe","w")
        );
        $this->process = proc_open($command, $spec, $this->pipes);
        $this->setBlocking(0);
    }

    public function isResource() {
        return is_resource($this->process);
    }
    public function setBlocking($blocking = 1) {
        return stream_set_blocking($this->pipes[1], $blocking);
    }
    public function getStatus() {
        return proc_get_status($this->process);
    }
    public function get() {
//		$out = fread($this->pipes[1], 128);
//		$out = fgets($this->pipes[1]);
        $out = stream_get_contents($this->pipes[1]);
        return $out;
    }

    public function put($data) {
//		fwrite($this->pipes[1], $data."\n");
        fwrite($this->pipes[1], $data);
//		fwrite($this->pipes[1], chr(13));
        fflush($this->pipes[1]);
//		return fwrite($this->pipes[1], $data);
    }

    public function close() {
        if(is_resource($this->process)) {
            fclose($this->pipes[0]);
            fclose($this->pipes[1]);
            fclose($this->pipes[2]);
            return proc_close($this->process);
        }
    }
    public function metaData() {
        return stream_get_meta_data($this->pipes[1]);
    }
}